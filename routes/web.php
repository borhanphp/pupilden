<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\CourseSubCategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CourseModuleController;
use App\Http\Controllers\CourseModuleFileController;
use App\Http\Controllers\StudentController;



// Authorization routes
Route::middleware(['auth'])->group(function () {
    Route::resource('roles', \App\Http\Controllers\Authorization\RoleController::class);
    Route::resource('permissions', \App\Http\Controllers\Authorization\PermissionController::class);

    Route::get('dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    Route::get('dashboard/admin', [DashboardController::class, 'adminDashboard'])->name('dashboard.admin');
    Route::get('dashboard/superadmin', [DashboardController::class, 'superadminDashboard'])->name('dashboard.superadmin');
    Route::resource('users', UserController::class);
    Route::resource('domains', DomainController::class);
    Route::resource('organizations', OrganizationController::class);
            Route::resource('course-categories', CourseCategoryController::class);
        Route::resource('course-sub-categories', CourseSubCategoryController::class);
        Route::resource('courses', CourseController::class);
        Route::resource('videos', VideoController::class);
        Route::post('videos/update-order', [VideoController::class, 'updateOrder'])->name('videos.update-order');
        Route::resource('exams', ExamController::class);
        Route::post('exams/{exam}/toggle-published', [ExamController::class, 'togglePublished'])->name('exams.toggle-published');
        Route::resource('questions', QuestionController::class);
        Route::post('questions/{question}/duplicate', [QuestionController::class, 'duplicate'])->name('questions.duplicate');
        Route::resource('coupons', CouponController::class);
        Route::post('coupons/{coupon}/toggle-active', [CouponController::class, 'toggleActive'])->name('coupons.toggle-active');
        Route::post('coupons/{coupon}/duplicate', [CouponController::class, 'duplicate'])->name('coupons.duplicate');
        Route::resource('course-modules', CourseModuleController::class);
        Route::post('course-modules/update-order', [CourseModuleController::class, 'updateOrder'])->name('course-modules.update-order');
        Route::post('course-modules/{courseModule}/toggle-status', [CourseModuleController::class, 'toggleStatus'])->name('course-modules.toggle-status');
        Route::resource('course-module-files', CourseModuleFileController::class);
        Route::get('course-module-files/{courseModuleFile}/download', [CourseModuleFileController::class, 'download'])->name('course-module-files.download');
        Route::resource('students', StudentController::class);
        Route::get('students/{student}/payments', [StudentController::class, 'payments'])->name('students.payments');
        Route::post('students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle-status');
        Route::post('students/{student}/reset-password', [StudentController::class, 'resetPassword'])->name('students.reset-password');
        Route::get('students/{student}/statistics', [StudentController::class, 'statistics'])->name('students.statistics');
        Route::post('course-purchases/{purchase}/update-status', [StudentController::class, 'updatePaymentStatus'])->name('course-purchases.update-status');
});


require __DIR__.'/auth.php';