<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                $query->whereHas('course', function($q) {
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
                'video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm,mkv|max:51200', // Max 50GB
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
                $previewImagePath = $previewImage->storeAs('video-previews', $previewImageName, 'public');
                $videoData['preview_image'] = $previewImagePath;
            }

            // Handle different video types
            if ($request->video_type == 2 && $request->hasFile('video_file')) {
                // Cloudflare Stream upload
                $cloudflareData = $this->uploadToCloudflare($request->file('video_file'), $request->title);
                
                if ($cloudflareData['success']) {
                    $videoData['video_url'] = $cloudflareData['video_url'];
                    $videoData['cloudflare_video_id'] = $cloudflareData['video_id'];
                    $videoData['thumbnail_url'] = $cloudflareData['thumbnail_url'];
                    $videoData['file_size'] = $cloudflareData['file_size'];
                    
                    // Update duration if not provided
                    if (!$request->duration && isset($cloudflareData['duration'])) {
                        $videoData['duration'] = $cloudflareData['duration'];
                    }
                } else {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Error uploading to Cloudflare: ' . $cloudflareData['message']);
                }
            } elseif ($request->video_type == 1 && $request->video_url) {
                // YouTube video
                $videoData['video_url'] = $request->video_url;
            } elseif ($request->video_type == 0 && $request->video_url) {
                // S3 or other URL
                $videoData['video_url'] = $request->video_url;
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
                    'duration' => isset($result['duration']) ? (int)$result['duration'] : null,
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
                'video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv,webm,mkv|max:51200', // Max 50GB
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
                // Delete old preview image if exists
                if ($video->preview_image && Storage::disk('public')->exists($video->preview_image)) {
                    Storage::disk('public')->delete($video->preview_image);
                }
                
                $previewImage = $request->file('preview_image');
                $previewImageName = time() . '_' . $previewImage->getClientOriginalName();
                $previewImagePath = $previewImage->storeAs('video-previews', $previewImageName, 'public');
                $videoData['preview_image'] = $previewImagePath;
            }

            // Handle different video types
            if ($request->video_type == 2) {
                // Cloudflare Stream - only upload if new file provided
                if ($request->hasFile('video_file')) {
                    $cloudflareData = $this->uploadToCloudflare($request->file('video_file'), $request->title);
                    
                    if ($cloudflareData['success']) {
                        $videoData['video_url'] = $cloudflareData['video_url'];
                        $videoData['cloudflare_video_id'] = $cloudflareData['video_id'];
                        $videoData['thumbnail_url'] = $cloudflareData['thumbnail_url'];
                        $videoData['file_size'] = $cloudflareData['file_size'];
                        
                        // Update duration if not provided
                        if (!$request->duration && isset($cloudflareData['duration'])) {
                            $videoData['duration'] = $cloudflareData['duration'];
                        }
                    } else {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error uploading to Cloudflare: ' . $cloudflareData['message']);
                    }
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
            if ($video->preview_image && Storage::disk('public')->exists($video->preview_image)) {
                Storage::disk('public')->delete($video->preview_image);
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
