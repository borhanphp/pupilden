<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                ->where('is_published', true)
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
                'video_url' => 'required|url|max:500',
                'video_type' => 'required|in:0,1',
                'duration' => 'nullable|integer|min:0',
                'is_preview' => 'boolean',
                'order' => 'nullable|integer|min:0'
            ]);

            // Verify course belongs to organization
            $course = Course::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($request->course_id);

            $video = Video::create([
                'course_id' => $request->course_id,
                'title' => $request->title,
                'video_url' => $request->video_url,
                'video_type' => $request->video_type,
                'duration' => $request->duration,
                'is_preview' => $request->has('is_preview'),
                'order' => $request->order ?? 0,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ]);

            return redirect()->route('videos.index', $request->course_id)
                ->with('success', 'Video created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating video: ' . $e->getMessage());
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
                ->where('is_published', true)
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
                'video_url' => 'required|url|max:500',
                'video_type' => 'required|in:0,1',
                'duration' => 'nullable|integer|min:0',
                'is_preview' => 'boolean',
                'order' => 'nullable|integer|min:0'
            ]);

            // Verify course belongs to organization
            $course = Course::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($request->course_id);

            $video->update([
                'course_id' => $request->course_id,
                'title' => $request->title,
                'video_url' => $request->video_url,
                'video_type' => $request->video_type,
                'duration' => $request->duration,
                'is_preview' => $request->has('is_preview'),
                'order' => $request->order ?? 0,
                'updated_by' => auth()->user()->id,
            ]);

            return redirect()->route('videos.index', $request->course_id)
                ->with('success', 'Video updated successfully');

        } catch (\Exception $e) {
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

            $courseId = $video->course_id;
            $video->delete();

            return redirect()->route('videos.index', $courseId)
                ->with('success', 'Video deleted successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting video: ' . $e->getMessage());
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
