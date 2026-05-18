<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CoursePurchase;
use App\Models\Video;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();
        $orgId   = $request->header('X-Organization-Id') ?: $request->query('org_id');

        // Scope to one org if selected, otherwise all teacher's orgs
        $orgIds = $orgId
            ? [$orgId]
            : $teacher->activeOrganizations()->pluck('organizations.id')->toArray();

        $courseQuery = Course::where('teacher_id', $teacher->id)
                             ->whereIn('organization_id', $orgIds);

        $courseIds    = (clone $courseQuery)->pluck('id');
        $totalCourses = (clone $courseQuery)->count();
        $published    = (clone $courseQuery)->where('is_published', true)->where('is_active', true)->count();
        $drafts       = (clone $courseQuery)->where('is_published', false)->count();

        $totalStudents = CoursePurchase::whereIn('course_id', $courseIds)
                                       ->where('payment_status', 'completed')
                                       ->distinct('student_id')
                                       ->count('student_id');

        $totalVideos = Video::whereIn('course_id', $courseIds)->count();

        $recentEnrollments = CoursePurchase::with(['student:id,name,email', 'course:id,name'])
                                           ->whereIn('course_id', $courseIds)
                                           ->where('payment_status', 'completed')
                                           ->latest()
                                           ->limit(8)
                                           ->get()
                                           ->map(fn($p) => [
                                               'student_name'  => $p->student?->name,
                                               'student_email' => $p->student?->email,
                                               'course_name'   => $p->course?->name,
                                               'enrolled_at'   => $p->created_at?->diffForHumans(),
                                           ]);

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_courses'   => $totalCourses,
                    'published'       => $published,
                    'drafts'          => $drafts,
                    'total_students'  => $totalStudents,
                    'total_videos'    => $totalVideos,
                ],
                'recent_enrollments' => $recentEnrollments,
            ],
        ]);
    }
}
