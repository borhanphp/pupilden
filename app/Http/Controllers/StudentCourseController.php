<?php

namespace App\Http\Controllers;

use App\Models\CourseStudent;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentCourseController extends Controller
{
    /**
     * Display a listing of course enrollment requests
     */
    public function index(Request $request)
    {
        $organizationId = Auth::user()->organization_id;

        $query = CourseStudent::with(['course', 'student'])
            ->whereHas('course', function($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        } else {
            // Default to pending requests
            $query->where('status', 'pending');
        }

        // Filter by course
        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        // Search by student name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);
        $courses = Course::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('student-courses.index', compact('requests', 'courses'));
    }

    /**
     * Approve a course enrollment request
     */
    public function approve(CourseStudent $courseStudent)
    {
        // Verify the course belongs to user's organization
        if ($courseStudent->course->organization_id !== Auth::user()->organization_id) {
            return redirect()->route('student-courses.index')
                ->with('error', 'Unauthorized access.');
        }

        $courseStudent->update([
            'status' => 'approved',
            'approved_at' => now(),
            'enrolled_at' => now(),
            'updated_by' => Auth::user()->id,
        ]);

        return redirect()->route('student-courses.index')
            ->with('success', 'Course enrollment request approved successfully!');
    }

    /**
     * Disapprove a course enrollment request
     */
    public function disapprove(Request $request, CourseStudent $courseStudent)
    {
        // Verify the course belongs to user's organization
        if ($courseStudent->course->organization_id !== Auth::user()->organization_id) {
            return redirect()->route('student-courses.index')
                ->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $courseStudent->update([
            'status' => 'disapproved',
            'rejection_reason' => $request->rejection_reason,
            'disapproved_at' => now(),
            'updated_by' => Auth::user()->id,
        ]);

        return redirect()->route('student-courses.index')
            ->with('success', 'Course enrollment request disapproved.');
    }

    /**
     * Show details of a course enrollment request
     */
    public function show(CourseStudent $courseStudent)
    {
        // Verify the course belongs to user's organization
        if ($courseStudent->course->organization_id !== Auth::user()->organization_id) {
            return redirect()->route('student-courses.index')
                ->with('error', 'Unauthorized access.');
        }

        $courseStudent->load(['course', 'student']);

        return view('student-courses.show', compact('courseStudent'));
    }
}
