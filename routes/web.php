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
use App\Http\Controllers\OrganizationThemeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\SectionLayoutController;
use App\Http\Controllers\SectionContentController;
use App\Http\Controllers\SeoSettingController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OrganizationSettingController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\GoogleOAuthController;
use App\Http\Controllers\SliderController;

// Google OAuth for Gmail (obtain refresh token – callback must match GOOGLE_REDIRECT_URI in .env)
Route::get('google/oauth', [GoogleOAuthController::class, 'redirect'])->name('google.oauth');
Route::get('google/callback', [GoogleOAuthController::class, 'callback'])->name('google.callback');
// Support /oauth/callback if that's what's configured in Google Cloud Console
Route::get('oauth/callback', [GoogleOAuthController::class, 'callback'])->name('oauth.callback');

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
    Route::post('organizations/settings/update', [OrganizationController::class, 'updateSettings'])->name('organizations.settings.update');
    Route::resource('organization-themes', OrganizationThemeController::class);
    Route::resource('themes', ThemeController::class);
    Route::resource('pages', PageController::class);
    Route::resource('sections', SectionController::class)->except(['index', 'create', 'show', 'store']);
    Route::get('pages/{page}/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::post('pages/{page}/sections', [SectionController::class, 'store'])->name('sections.store');
    Route::resource('contents', ContentController::class)->except(['index', 'create', 'show']);
    Route::get('sections/{section}/contents', [ContentController::class, 'index'])->name('contents.index');
            Route::resource('course-categories', CourseCategoryController::class);
        Route::resource('course-sub-categories', CourseSubCategoryController::class);
        Route::resource('courses', CourseController::class);
        Route::resource('videos', VideoController::class);
        Route::post('videos/update-order', [VideoController::class, 'updateOrder'])->name('videos.update-order');
        Route::post('videos/direct-upload-url', [VideoController::class, 'getDirectUploadUrl'])->name('videos.direct-upload-url');
        Route::post('videos/cloudflare-webhook', [VideoController::class, 'cloudflareWebhook'])->name('videos.cloudflare-webhook');
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

        Route::resource('section-layouts', SectionLayoutController::class);
        Route::resource('section-contents', SectionContentController::class);
        Route::resource('seo-settings', SeoSettingController::class);
        Route::resource('media', MediaController::class);
        Route::resource('organization-settings', OrganizationSettingController::class);
        Route::resource('student-courses', StudentCourseController::class)->only(['index', 'show']);
        Route::put('student-courses/{courseStudent}/approve', [StudentCourseController::class, 'approve'])->name('student-courses.approve');
        Route::put('student-courses/{courseStudent}/disapprove', [StudentCourseController::class, 'disapprove'])->name('student-courses.disapprove');
        Route::resource('payment-gateways', PaymentGatewayController::class);
        Route::put('payment-gateways/{paymentGateway}/toggle-active', [PaymentGatewayController::class, 'toggleActive'])->name('payment-gateways.toggle-active');
        Route::put('payment-gateways/{paymentGateway}/set-default', [PaymentGatewayController::class, 'setDefault'])->name('payment-gateways.set-default');

        Route::resource('sliders', SliderController::class)->except(['show']);
});


require __DIR__.'/auth.php';