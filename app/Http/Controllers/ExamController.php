<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Display a listing of exams for a specific course
     */
    public function index(Request $request, $courseId = null)
    {
        try {
            $query = Exam::with(['course', 'creator', 'updater', 'questions', 'attempts']);

            if ($courseId) {
                // Get exams for a specific course
                $course = Course::where('organization_id', auth()->user()->organization_id)
                    ->findOrFail($courseId);
                
                $query->where('course_id', $courseId);
            } else {
                // Get all exams for the organization
                $query->whereHas('course', function($q) {
                    $q->where('organization_id', auth()->user()->organization_id);
                });
            }

            $exams = $query->orderBy('created_at', 'desc')->get();

            if ($request->ajax()) {
                return response()->json(['exams' => $exams]);
            }

            return view('exams.index', compact('exams', 'courseId'));

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Error retrieving exams: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error retrieving exams: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new exam
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

            return view('exams.form', compact('courses', 'courseId', 'course'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created exam
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'title' => 'required|string|max:255',
                'type' => 'required|in:pre_course,final_exam',
                'pass_mark' => 'required|integer|min:0|max:100',
                'is_published' => 'boolean'
            ]);

            // Verify course belongs to organization
            $course = Course::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($request->course_id);

            $exam = Exam::create([
                'course_id' => $request->course_id,
                'title' => $request->title,
                'type' => $request->type,
                'pass_mark' => $request->pass_mark,
                'is_published' => $request->has('is_published'),
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ]);

            return redirect()->route('exams.index', $request->course_id)
                ->with('success', 'Exam created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating exam: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified exam
     */
    public function show(Exam $exam)
    {
        try {
            // Verify exam belongs to organization
            $exam->load(['course', 'creator', 'updater', 'questions', 'attempts']);
            
            if ($exam->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            return view('exams.show', compact('exam'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving exam: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified exam
     */
    public function edit(Exam $exam)
    {
        try {
            // Verify exam belongs to organization
            if ($exam->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $courses = Course::where('organization_id', auth()->user()->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->orderBy('name')
                ->get();

            return view('exams.form', compact('exam', 'courses'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified exam
     */
    public function update(Request $request, Exam $exam)
    {
        try {
            // Verify exam belongs to organization
            if ($exam->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'title' => 'required|string|max:255',
                'type' => 'required|in:pre_course,final_exam',
                'pass_mark' => 'required|integer|min:0|max:100',
                'is_published' => 'boolean'
            ]);

            // Verify course belongs to organization
            $course = Course::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($request->course_id);

            $exam->update([
                'course_id' => $request->course_id,
                'title' => $request->title,
                'type' => $request->type,
                'pass_mark' => $request->pass_mark,
                'is_published' => $request->has('is_published'),
                'updated_by' => auth()->user()->id,
            ]);

            return redirect()->route('exams.index', $request->course_id)
                ->with('success', 'Exam updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating exam: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified exam
     */
    public function destroy(Exam $exam)
    {
        try {
            // Verify exam belongs to organization
            if ($exam->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $courseId = $exam->course_id;
            $exam->delete();

            return redirect()->route('exams.index', $courseId)
                ->with('success', 'Exam deleted successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting exam: ' . $e->getMessage());
        }
    }

    /**
     * Toggle exam published status
     */
    public function togglePublished(Exam $exam)
    {
        try {
            // Verify exam belongs to organization
            if ($exam->course->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $exam->update([
                'is_published' => !$exam->is_published,
                'updated_by' => auth()->user()->id
            ]);

            $status = $exam->is_published ? 'published' : 'unpublished';
            return redirect()->back()
                ->with('success', "Exam {$status} successfully");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating exam status: ' . $e->getMessage());
        }
    }
}