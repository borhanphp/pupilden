<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeacherVideoController extends Controller
{
    public function index(Request $request, $courseId)
    {
        $teacher = $request->user();
        $course  = $this->findOwnedCourse($teacher, $courseId);
        if (!$course) return response()->json(['success' => false, 'message' => 'Course not found'], 404);

        $videos = Video::with('courseModule:id,name')
            ->where('course_id', $course->id)
            ->orderBy('order')
            ->get()
            ->map(fn($v) => $this->formatVideo($v));

        $modules = CourseModule::where('course_id', $course->id)
            ->orderBy('order')
            ->select('id', 'name', 'order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'course'  => ['id' => $course->id, 'name' => $course->name],
                'modules' => $modules,
                'videos'  => $videos,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $teacher = $request->user();

        $validator = Validator::make($request->all(), [
            'course_id'        => 'required|integer',
            'course_module_id' => 'nullable|exists:course_modules,id',
            'title'            => 'required|string|max:255',
            'is_preview'       => 'boolean',
            'order'            => 'nullable|integer',
            'cloudflare_upload_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $course = $this->findOwnedCourse($teacher, $request->course_id);
        if (!$course) return response()->json(['success' => false, 'message' => 'Course not found'], 404);

        $data = [
            'course_id'        => $course->id,
            'course_module_id' => $request->course_module_id,
            'title'            => $request->title,
            'is_preview'       => $request->boolean('is_preview', false),
            'is_published'     => true,
            'video_type'       => 2, // Cloudflare
            'order'            => $request->input('order', 0),
            'upload_status'    => 'pending',
            'created_by'       => null,
        ];

        if ($request->filled('cloudflare_upload_id')) {
            $videoInfo = $this->getCloudflareVideoInfo($request->cloudflare_upload_id);
            if ($videoInfo) {
                $data['cloudflare_video_id'] = $videoInfo['video_id'] ?? $request->cloudflare_upload_id;
                $data['upload_status']       = $videoInfo['status'] ?? 'processing';
                $data['video_url']           = $videoInfo['hls_url'] ?? null;
                $data['duration']            = $videoInfo['duration'] ?? 0;
            }
        }

        $video = Video::create($data);

        return response()->json(['success' => true, 'message' => 'Video saved', 'data' => $this->formatVideo($video)], 201);
    }

    public function destroy(Request $request, $id)
    {
        $teacher = $request->user();
        $video   = Video::with('course')->find($id);

        if (!$video) return response()->json(['success' => false, 'message' => 'Video not found'], 404);

        $course = $this->findOwnedCourse($teacher, $video->course_id);
        if (!$course) return response()->json(['success' => false, 'message' => 'Forbidden'], 403);

        if ($video->cloudflare_video_id) {
            $this->deleteFromCloudflare($video->cloudflare_video_id);
        }

        $video->delete();
        return response()->json(['success' => true, 'message' => 'Video deleted']);
    }

    public function uploadUrl(Request $request)
    {
        $teacher = $request->user();

        $validator = Validator::make($request->all(), [
            'course_id' => 'required|integer',
            'name'      => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $course = $this->findOwnedCourse($teacher, $request->course_id);
        if (!$course) return response()->json(['success' => false, 'message' => 'Course not found'], 404);

        try {
            $accountId = env('CLOUDFLARE_ACCOUNT_ID');
            $apiToken  = env('CLOUDFLARE_API_TOKEN');

            $payload = [
                'maxDurationSeconds' => 21600,
                'meta'               => ['name' => $request->name],
            ];

            $response = Http::withToken($apiToken)
                ->post("https://api.cloudflare.com/client/v4/accounts/{$accountId}/stream/direct_upload", $payload);

            if (!$response->successful()) {
                throw new \Exception('Cloudflare API error: ' . $response->body());
            }

            $result = $response->json();
            $resultData = $result['result'] ?? $result;

            return response()->json([
                'success'    => true,
                'upload_url' => $resultData['uploadURL'] ?? $resultData['uploadUrl'] ?? null,
                'video_uid'  => $resultData['uid'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to get upload URL: ' . $e->getMessage()], 500);
        }
    }

    // --- Helpers ---

    private function findOwnedCourse($teacher, $courseId)
    {
        $orgIds = $teacher->activeOrganizations()->pluck('organizations.id')->toArray();
        return Course::where('id', $courseId)
                     ->where('teacher_id', $teacher->id)
                     ->whereIn('organization_id', $orgIds)
                     ->first();
    }

    private function getCloudflareVideoInfo(string $uploadId): ?array
    {
        try {
            $accountId = env('CLOUDFLARE_ACCOUNT_ID');
            $apiToken  = env('CLOUDFLARE_API_TOKEN');
            $customerCode = config('services.cloudflare.stream_customer_code');

            $response = Http::withToken($apiToken)
                ->get("https://api.cloudflare.com/client/v4/accounts/{$accountId}/stream/{$uploadId}");

            if (!$response->successful()) return null;

            $data   = $response->json()['result'] ?? [];
            $videoId = $data['uid'] ?? $uploadId;
            $hls    = $data['playback']['hls'] ?? "https://customer-{$customerCode}.cloudflarestream.com/{$videoId}/manifest/video.m3u8";

            return [
                'video_id' => $videoId,
                'hls_url'  => $hls,
                'duration' => (int) ($data['duration'] ?? 0),
                'status'   => $data['status']['state'] ?? 'processing',
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function deleteFromCloudflare(string $videoId): void
    {
        try {
            Http::withToken(env('CLOUDFLARE_API_TOKEN'))
                ->delete("https://api.cloudflare.com/client/v4/accounts/" . env('CLOUDFLARE_ACCOUNT_ID') . "/stream/{$videoId}");
        } catch (\Exception $e) {
            // log and continue
        }
    }

    private function formatVideo(Video $video): array
    {
        return [
            'id'                  => $video->id,
            'title'               => $video->title,
            'course_module_id'    => $video->course_module_id,
            'module_name'         => $video->relationLoaded('courseModule') ? $video->courseModule?->name : null,
            'is_preview'          => $video->is_preview,
            'is_published'        => $video->is_published,
            'upload_status'       => $video->upload_status,
            'cloudflare_video_id' => $video->cloudflare_video_id,
            'video_url'           => $video->video_url,
            'duration'            => $video->duration,
            'order'               => $video->order,
            'created_at'          => $video->created_at?->toDateString(),
        ];
    }
}
