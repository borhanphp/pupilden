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
});


require __DIR__.'/auth.php';