<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Course;
use App\Jobs\ProcessVideoUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * VideoController
 * 
 * Handles video management including Cloudflare Stream integration
 * 
 * Required Environment Variables:
 * - CLOUDFLARE_API_TOKEN: Your Cloudflare API token with Stream permissions
 * - CLOUDFLARE_ACCOUNT_ID: Your Cloudflare account ID
 * 
 * Video Types:
 * - 0: S3/External URL
 * - 1: YouTube
 * - 2: Cloudflare Stream
 */

class VideoController extends Controller
{
    /**
     * Display a listing of videos for a specific course
     */
    public function index(Request $request, $courseId = null)
    {


        try {
            $query = Video::with(['course', 'creator', 'updater']);

            if ($courseId) {
                // Get videos for a specific course
                $course = Course::where('organization_id', auth()->user()->organization_id)
                    ->findOrFail($courseId);

                $query->where('course_id', $courseId);
            } else {
                // Get all videos for the organization
                $query->whereHas('course', function ($q) {
                    $q->where('organization_id', auth()->user()->organization_id);
                });
            }

            $videos = $query->orderBy('order')->get();

            if ($request->ajax()) {
                return response()->json(['videos' => $videos]);
            }

            return view('videos.index', compact('videos', 'courseId'));

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Error retrieving videos: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error retrieving videos: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new video
     */
    public function create(Request $request, $courseId = null)
    {
        try {
            $courses = Course::where('organization_id', auth()->user()->organization_id)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->orderBy('name')
                ->get();

            $course = null;
            if ($courseId) {
                $course = Course::where('organization_id', auth()->user()->organization_id)
                    ->findOrFail($courseId);
            }

            return view('videos.form', compact('courses', 'courseId', 'course'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created video
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'title' => 'required|string|max:255',
                'video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm,mkv|max:2097152', // Max 2GB (2048MB = 2097152KB)
                'video_url' => 'nullable|url|max:500',
                'video_type' => 'required|in:0,1,2', // 0=S3, 1=YouTube, 2=Cloudflare
                'course_module_id' => 'nullable|exists:course_modules,id',
                'preview_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
                'duration' => 'nullable|integer|min:0',
                'is_preview' => 'boolean',
                'is_published' => 'boolean',
                'order' => 'nullable|integer|min:0'
            ]);

            // Verify course belongs to organization
            $course = Course::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($request->course_id);

            $videoData = [
                'course_id' => $request->course_id,
                'course_module_id' => $request->course_module_id,
                'title' => $request->title,
                'video_type' => $request->video_type,
                'duration' => $request->duration,
                'is_preview' => $request->has('is_preview'),
                'is_published' => $request->has('is_published'),
                'order' => $request->order ?? 0,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            // Handle preview image upload
            if ($request->hasFile('preview_image')) {
                $previewImage = $request->file('preview_image');
                $previewImageName = time() . '_' . $previewImage->getClientOriginalName();
                $previewImage->storeAs('video-previews', $previewImageName, 'r2');
                $videoData['preview_image'] = 'video-previews/' . $previewImageName;
            }

            // Handle different video types
            if ($request->video_type == 2) {
                // Cloudflare Stream upload
                if ($request->has('cloudflare_upload_id')) {
                    // Direct upload from browser - video already uploaded to Cloudflare
                    $uploadId = $request->cloudflare_upload_id;

                    // Get video details from Cloudflare
                    $videoInfo = $this->getCloudflareVideoInfo($uploadId);

                    if ($videoInfo['success']) {
                        $videoData['video_url'] = $videoInfo['video_url'];
                        $videoData['cloudflare_video_id'] = $videoInfo['video_id'];
                        $videoData['thumbnail_url'] = $videoInfo['thumbnail_url'];
                        $videoData['file_size'] = $videoInfo['file_size'] ?? null;
                        $videoData['duration'] = $videoInfo['duration'] ?? $request->duration;
                        $videoData['upload_status'] = 'completed';
                        $videoData['upload_progress'] = 100;
                    } else {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error retrieving video from Cloudflare: ' . $videoInfo['message']);
                    }
                } elseif ($request->hasFile('video_file')) {
                    // Fallback: Server-side upload for smaller files (< 500MB)
                    $file = $request->file('video_file');
                    $fileSize = $file->getSize();

                    // For files larger than 500MB, recommend direct upload
                    if ($fileSize > 500 * 1024 * 1024) {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'For files larger than 500MB, please use direct upload. The form will automatically switch to direct upload mode.');
                    }

                    // Store file temporarily
                    $tempPath = $file->storeAs('temp_videos', time() . '_' . $file->getClientOriginalName());

                    // Set initial upload status
                    $videoData['upload_status'] = 'pending';
                    $videoData['upload_progress'] = 0;
                    $videoData['file_size'] = $fileSize;

                    // Create video record first
                    $video = Video::create($videoData);

                    // Dispatch job to queue for background processing
                    ProcessVideoUpload::dispatch(
                        $video,
                        $tempPath,
                        $file->getClientOriginalName(),
                        $fileSize
                    );

                    return redirect()->route('videos.index', $request->course_id)
                        ->with('success', 'Video is being uploaded in the background. You will be notified when it\'s ready.');
                } else {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Please provide a video file for Cloudflare upload.');
                }

            } elseif ($request->video_type == 1 && $request->video_url) {
                // YouTube video
                $videoData['video_url'] = $request->video_url;
                $videoData['upload_status'] = 'completed';
                $videoData['upload_progress'] = 100;
            } elseif ($request->video_type == 0 && $request->video_url) {
                // S3 or other URL
                $videoData['video_url'] = $request->video_url;
                $videoData['upload_status'] = 'completed';
                $videoData['upload_progress'] = 100;
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Please provide either a video file (for Cloudflare) or video URL (for S3/YouTube)');
            }

            $video = Video::create($videoData);

            return redirect()->route('videos.index', $request->course_id)
                ->with('success', 'Video created successfully');

        } catch (\Exception $e) {
            Log::error('Video creation error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating video: ' . $e->getMessage());
        }
    }

    /**
     * Upload video to Cloudflare Stream
     */
    private function uploadToCloudflare($file, $title = null)
    {
        try {
            // Check if Cloudflare credentials are configured
            if (!env('CLOUDFLARE_API_TOKEN') || !env('CLOUDFLARE_ACCOUNT_ID')) {
                return [
                    'success' => false,
                    'message' => 'Cloudflare credentials not configured'
                ];
            }

            // Get the file's temporary path directly
            $tempPath = $file->getRealPath();

            // Verify the file exists
            if (!file_exists($tempPath)) {
                return [
                    'success' => false,
                    'message' => 'Temporary file not found'
                ];
            }

            // Prepare Cloudflare Stream API request
            $response = Http::withToken(env('CLOUDFLARE_API_TOKEN'))
                ->attach('file', fopen($tempPath, 'r'), $file->getClientOriginalName())
                ->post("https://api.cloudflare.com/client/v4/accounts/" . env('CLOUDFLARE_ACCOUNT_ID') . "/stream", [
                    'name' => $title ?: $file->getClientOriginalName(),
                    'requireSignedURLs' => false, // Set to true if you want signed URLs
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['result'])) {
                $result = $data['result'];

                return [
                    'success' => true,
                    'video_id' => $result['uid'],
                    'video_url' => $result['playback']['hls'],
                    'thumbnail_url' => $result['thumbnail'],
                    'file_size' => $file->getSize(),
                    'duration' => isset($result['duration']) ? (int) $result['duration'] : null,
                    'message' => 'Video uploaded successfully to Cloudflare'
                ];
            } else {
                $errorMessage = isset($data['errors']) ? implode(', ', array_column($data['errors'], 'message')) : 'Unknown error';
                return [
                    'success' => false,
                    'message' => 'Cloudflare upload failed: ' . $errorMessage
                ];
            }

        } catch (\Exception $e) {
            Log::error('Cloudflare upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Upload error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Display the specified video
     */
    public function show(Video $video)
    {
        try {
            // Verify video belongs to organization
            $video->load(['course', 'creator', 'updater']);

            if ($video->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            return view('videos.show', compact('video'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving video: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified video
     */
    public function edit(Video $video)
    {
        try {
            // Verify video belongs to organization
            if ($video->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $courses = Course::where('organization_id', auth()->user()->organization_id)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->orderBy('name')
                ->get();

            return view('videos.form', compact('video', 'courses'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified video
     */
    public function update(Request $request, Video $video)
    {
        try {
            // Verify video belongs to organization
            if ($video->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'title' => 'required|string|max:255',
                'video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm,mkv|max:2097152', // Max 2GB (2048MB = 2097152KB)
                'video_url' => 'nullable|url|max:500',
                'video_type' => 'required|in:0,1,2', // 0=S3, 1=YouTube, 2=Cloudflare
                'course_module_id' => 'nullable|exists:course_modules,id',
                'preview_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
                'duration' => 'nullable|integer|min:0',
                'is_preview' => 'boolean',
                'is_published' => 'boolean',
                'order' => 'nullable|integer|min:0'
            ]);

            // Verify course belongs to organization
            $course = Course::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($request->course_id);

            $videoData = [
                'course_id' => $request->course_id,
                'course_module_id' => $request->course_module_id,
                'title' => $request->title,
                'video_type' => $request->video_type,
                'duration' => $request->duration,
                'is_preview' => $request->has('is_preview'),
                'is_published' => $request->has('is_published'),
                'order' => $request->order ?? 0,
                'updated_by' => auth()->user()->id,
            ];

            // Handle preview image upload
            if ($request->hasFile('preview_image')) {
                if ($video->preview_image) {
                    Storage::disk('r2')->delete($video->preview_image);
                }

                $previewImage = $request->file('preview_image');
                $previewImageName = time() . '_' . $previewImage->getClientOriginalName();
                $previewImage->storeAs('video-previews', $previewImageName, 'r2');
                $videoData['preview_image'] = 'video-previews/' . $previewImageName;
            }

            // Handle different video types
            if ($request->video_type == 2) {
                // Cloudflare Stream — direct browser upload (large files via JS)
                if ($request->has('cloudflare_upload_id')) {
                    $uploadId = $request->cloudflare_upload_id;
                    $videoInfo = $this->getCloudflareVideoInfo($uploadId);

                    if ($videoInfo['success']) {
                        $videoData['video_url']           = $videoInfo['video_url'];
                        $videoData['cloudflare_video_id'] = $videoInfo['video_id'];
                        $videoData['thumbnail_url']       = $videoInfo['thumbnail_url'];
                        $videoData['file_size']           = $videoInfo['file_size'] ?? null;
                        $videoData['duration']            = $videoInfo['duration'] ?? $request->duration;
                        $videoData['upload_status']       = 'completed';
                        $videoData['upload_progress']     = 100;
                    } else {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error retrieving video from Cloudflare: ' . $videoInfo['message']);
                    }
                } elseif ($request->hasFile('video_file')) {
                    // Server-side upload for smaller files (< 500MB)
                    $file = $request->file('video_file');

                    // Store file temporarily
                    $tempPath = $file->storeAs('temp_videos', time() . '_' . $file->getClientOriginalName());

                    // Set upload status
                    $videoData['upload_status'] = 'pending';
                    $videoData['upload_progress'] = 0;
                    $videoData['file_size'] = $file->getSize();

                    // Update video record first
                    $video->update($videoData);

                    // Dispatch job to queue for background processing
                    ProcessVideoUpload::dispatch(
                        $video,
                        $tempPath,
                        $file->getClientOriginalName(),
                        $file->getSize()
                    );

                    return redirect()->route('videos.index', $request->course_id)
                        ->with('success', 'Video is being uploaded in the background. You will be notified when it\'s ready.');
                }
                // If no new file uploaded, keep existing Cloudflare video data
            } elseif ($request->video_type == 1) {
                // YouTube video - update URL if provided
                if ($request->video_url) {
                    $videoData['video_url'] = $request->video_url;
                }
                // Clear Cloudflare-specific fields when switching from Cloudflare to YouTube
                if ($video->video_type == 2) {
                    $videoData['cloudflare_video_id'] = null;
                    $videoData['thumbnail_url'] = null;
                    $videoData['file_size'] = null;
                }
            } elseif ($request->video_type == 0) {
                // S3 or other URL - update URL if provided
                if ($request->video_url) {
                    $videoData['video_url'] = $request->video_url;
                }
                // Clear Cloudflare-specific fields when switching from Cloudflare to S3
                if ($video->video_type == 2) {
                    $videoData['cloudflare_video_id'] = null;
                    $videoData['thumbnail_url'] = null;
                    $videoData['file_size'] = null;
                }
            }

            $video->update($videoData);

            return redirect()->route('videos.index', $request->course_id)
                ->with('success', 'Video updated successfully');

        } catch (\Exception $e) {
            Log::error('Video update error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating video: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified video
     */
    public function destroy(Video $video)
    {
        try {
            // Verify video belongs to organization
            if ($video->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            // Delete from Cloudflare if it's a Cloudflare video
            if ($video->video_type == 2 && $video->cloudflare_video_id) {
                $this->deleteFromCloudflare($video->cloudflare_video_id);
            }

            // Delete preview image if exists
            if ($video->preview_image) {
                Storage::disk('r2')->delete($video->preview_image);
            }

            $courseId = $video->course_id;
            $video->delete();

            return redirect()->route('videos.index', $courseId)
                ->with('success', 'Video deleted successfully');

        } catch (\Exception $e) {
            Log::error('Video deletion error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting video: ' . $e->getMessage());
        }
    }

    /**
     * Get direct upload URL from Cloudflare Stream
     * This allows uploading large files directly from browser to Cloudflare
     */
    public function getDirectUploadUrl(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'file_size' => 'nullable|integer|min:0'
            ]);

            if (!env('CLOUDFLARE_API_TOKEN') || !env('CLOUDFLARE_ACCOUNT_ID')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cloudflare credentials not configured'
                ], 500);
            }

            // Build request payload - Cloudflare Stream Direct Upload API format
            $payload = [
                'maxDurationSeconds' => 7200, // 2 hours max for large videos
                'requireSignedURLs' => false,
            ];

            // Add video metadata (name/title) - Cloudflare will use this for the video name
            if ($request->has('title') && !empty($request->title)) {
                $payload['meta'] = [
                    'name' => $request->title
                ];
            }

            // For large files, increase timeout
            $fileSize = $request->input('file_size', 0);
            $largeFileThreshold = 500 * 1024 * 1024; // 500MB

            if ($fileSize > $largeFileThreshold) {
                $payload['maxDurationSeconds'] = 14400; // 4 hours for very large files
            }

            // Do NOT set allowedOrigins — leave empty so videos play on any domain.
            // Access control is handled by signed URL tokens, not domain restrictions.

            // Create direct creator upload URL
            $response = Http::withToken(env('CLOUDFLARE_API_TOKEN'))
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post("https://api.cloudflare.com/client/v4/accounts/" . env('CLOUDFLARE_ACCOUNT_ID') . "/stream/direct_upload", $payload);

            $data = $response->json();

            // Log full response for debugging
            Log::info('Cloudflare Direct Upload Response', [
                'status' => $response->status(),
                'response' => $data,
                'payload' => $payload
            ]);

            if ($response->successful() && isset($data['result'])) {
                $result = $data['result'];

                return response()->json([
                    'success' => true,
                    'upload_url' => $result['uploadURL'] ?? $result['uploadUrl'] ?? null,
                    'video_id' => $result['uid'] ?? null,
                    'message' => 'Direct upload URL created successfully'
                ]);
            } else {
                // Better error message extraction
                $errorMessage = 'Unknown error';
                if (isset($data['errors']) && is_array($data['errors'])) {
                    $messages = [];
                    foreach ($data['errors'] as $error) {
                        if (isset($error['message'])) {
                            $messages[] = $error['message'];
                        } elseif (is_string($error)) {
                            $messages[] = $error;
                        }
                    }
                    $errorMessage = !empty($messages) ? implode(', ', $messages) : 'Bad Request';
                } elseif (isset($data['messages']) && is_array($data['messages'])) {
                    $messages = [];
                    foreach ($data['messages'] as $message) {
                        if (isset($message['message'])) {
                            $messages[] = $message['message'];
                        }
                    }
                    $errorMessage = !empty($messages) ? implode(', ', $messages) : 'Bad Request';
                } elseif (isset($data['message'])) {
                    $errorMessage = $data['message'];
                }

                Log::error('Cloudflare Direct Upload Failed', [
                    'status' => $response->status(),
                    'error' => $errorMessage,
                    'full_response' => $data
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create upload URL: ' . $errorMessage,
                    'details' => $data['errors'] ?? $data['messages'] ?? null
                ], $response->status() ?: 500);
            }

        } catch (\Exception $e) {
            Log::error('Direct upload URL creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating upload URL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get video information from Cloudflare after direct upload
     */
    private function getCloudflareVideoInfo($videoId)
    {
        if (!env('CLOUDFLARE_API_TOKEN') || !env('CLOUDFLARE_ACCOUNT_ID')) {
            return ['success' => false, 'message' => 'Cloudflare credentials not configured'];
        }

        // Cloudflare processes videos asynchronously after upload.
        // Poll up to 5 times (with 2-second pauses) waiting for readyToStream.
        $maxAttempts = 5;
        $lastError   = 'Timed out waiting for Cloudflare to process video';

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::withToken(env('CLOUDFLARE_API_TOKEN'))
                    ->get("https://api.cloudflare.com/client/v4/accounts/" . env('CLOUDFLARE_ACCOUNT_ID') . "/stream/" . $videoId);

                $data = $response->json();

                if ($response->successful() && isset($data['result'])) {
                    $result = $data['result'];
                    $hls    = $result['playback']['hls'] ?? null;

                    // Video is registered but still processing — wait and retry
                    if (!$hls || !($result['readyToStream'] ?? false)) {
                        Log::info("Cloudflare video {$videoId} still processing (attempt {$attempt}/{$maxAttempts})");
                        if ($attempt < $maxAttempts) {
                            sleep(2);
                            continue;
                        }
                        // Last attempt: build HLS URL manually — video will be playable once Cloudflare finishes
                        $customerCode = config('services.cloudflare.stream_customer_code');
                        $hls = $hls ?? "https://customer-{$customerCode}.cloudflarestream.com/{$videoId}/manifest/video.m3u8";
                    }

                    return [
                        'success'       => true,
                        'video_id'      => $result['uid'],
                        'video_url'     => $hls,
                        'thumbnail_url' => $result['thumbnail'] ?? null,
                        'file_size'     => isset($result['size']) ? (int) $result['size'] : null,
                        'duration'      => isset($result['duration']) ? (int) $result['duration'] : null,
                    ];
                }

                $lastError = isset($data['errors'])
                    ? implode(', ', array_column($data['errors'], 'message'))
                    : ($response->body() ?: 'Unknown error');

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::error("getCloudflareVideoInfo attempt {$attempt} error: " . $e->getMessage());
            }

            if ($attempt < $maxAttempts) sleep(2);
        }

        Log::error("getCloudflareVideoInfo failed after {$maxAttempts} attempts for {$videoId}: {$lastError}");
        return ['success' => false, 'message' => 'Failed to get video info: ' . $lastError];
    }

    /**
     * Handle Cloudflare webhook callback when video upload completes
     */
    public function cloudflareWebhook(Request $request)
    {
        try {
            // Verify webhook signature (optional but recommended)
            // You can add signature verification here for security

            $data = $request->all();

            Log::info('Cloudflare webhook received: ' . json_encode($data));

            // Find video by Cloudflare video ID
            if (isset($data['uid'])) {
                $video = Video::where('cloudflare_video_id', $data['uid'])->first();

                if ($video) {
                    // Update video with final details
                    $videoInfo = $this->getCloudflareVideoInfo($data['uid']);

                    if ($videoInfo['success']) {
                        $video->update([
                            'video_url' => $videoInfo['video_url'],
                            'thumbnail_url' => $videoInfo['thumbnail_url'],
                            'file_size' => $videoInfo['file_size'],
                            'duration' => $videoInfo['duration'] ?? $video->duration,
                            'upload_status' => 'completed',
                            'upload_progress' => 100
                        ]);

                        Log::info("Video {$video->id} updated from webhook");
                    }
                }
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Cloudflare webhook error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete video from Cloudflare Stream
     */
    private function deleteFromCloudflare($videoId)
    {
        try {
            if (!env('CLOUDFLARE_API_TOKEN') || !env('CLOUDFLARE_ACCOUNT_ID')) {
                Log::warning('Cloudflare credentials not configured for video deletion');
                return false;
            }

            $response = Http::withToken(env('CLOUDFLARE_API_TOKEN'))
                ->delete("https://api.cloudflare.com/client/v4/accounts/" . env('CLOUDFLARE_ACCOUNT_ID') . "/stream/" . $videoId);

            if ($response->successful()) {
                Log::info("Successfully deleted Cloudflare video: {$videoId}");
                return true;
            } else {
                Log::error("Failed to delete Cloudflare video: {$videoId}. Response: " . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Cloudflare video deletion error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update video order
     */
    public function updateOrder(Request $request)
    {
        try {
            $request->validate([
                'videos' => 'required|array',
                'videos.*.id' => 'required|exists:videos,id',
                'videos.*.order' => 'required|integer|min:0'
            ]);

            foreach ($request->videos as $videoData) {
                $video = Video::find($videoData['id']);

                // Verify video belongs to organization
                if ($video->course->organization_id !== auth()->user()->organization_id) {
                    continue;
                }

                $video->update([
                    'order' => $videoData['order'],
                    'updated_by' => auth()->user()->id
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Video order updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating video order: ' . $e->getMessage()], 500);
        }
    }
}
