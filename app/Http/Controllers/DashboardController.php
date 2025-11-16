<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\CoursePurchase;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $organizationId = Auth::user()->organization_id;

        // Basic Statistics
        $totalStudents = Student::where('organization_id', $organizationId)->count();
        $activeStudents = Student::where('organization_id', $organizationId)->where('is_active', true)->count();
        $totalCourses = Course::where('organization_id', $organizationId)->count();
        $publishedCourses = Course::where('organization_id', $organizationId)
            ->where('is_published', true)
            ->where('is_active', true)
            ->count();
        
        // Enrollment Requests
        $pendingRequests = CourseStudent::whereHas('course', function($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })->where('status', 'pending')->count();
        
        $approvedEnrollments = CourseStudent::whereHas('course', function($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })->where('status', 'approved')->count();

        // Revenue Statistics
        $totalRevenue = CoursePurchase::whereHas('course', function($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })->where('payment_status', 'completed')->sum('final_price');
        
        $monthlyRevenue = CoursePurchase::whereHas('course', function($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })->where('payment_status', 'completed')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('final_price');

        $totalPurchases = CoursePurchase::whereHas('course', function($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })->where('payment_status', 'completed')->count();

        // Recent Purchases
        $recentPurchases = CoursePurchase::with(['student', 'course'])
            ->whereHas('course', function($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->where('payment_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Revenue Chart Data (Last 6 months)
        $revenueChartData = CoursePurchase::whereHas('course', function($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })
        ->where('payment_status', 'completed')
        ->select(
            DB::raw("DATE_TRUNC('month', created_at) as month"),
            DB::raw('SUM(final_price) as revenue')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy(DB::raw("DATE_TRUNC('month', created_at)"))
        ->orderBy('month')
        ->get();

        // Course Enrollment Chart Data
        $enrollmentChartData = CourseStudent::whereHas('course', function($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })
        ->select(
            DB::raw("DATE_TRUNC('month', created_at) as month"),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy(DB::raw("DATE_TRUNC('month', created_at)"))
        ->orderBy('month')
        ->get();

        // Top Courses by Enrollment
        $topCourses = Course::where('organization_id', $organizationId)
            ->select('courses.*')
            ->selectSub(function($query) {
                $query->selectRaw('COUNT(*)')
                    ->from('course_students')
                    ->whereColumn('course_students.course_id', 'courses.id')
                    ->where('course_students.status', 'approved');
            }, 'approved_students_count')
            ->orderBy('approved_students_count', 'desc')
            ->limit(5)
            ->get();

        // Recent Enrollment Requests
        $recentRequests = CourseStudent::with(['student', 'course'])
            ->whereHas('course', function($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalStudents',
            'activeStudents',
            'totalCourses',
            'publishedCourses',
            'pendingRequests',
            'approvedEnrollments',
            'totalRevenue',
            'monthlyRevenue',
            'totalPurchases',
            'recentPurchases',
            'revenueChartData',
            'enrollmentChartData',
            'topCourses',
            'recentRequests'
        ));
    }

    public function superadminDashboard()
    {
        return view('dashboard.superadmin');
    }
}