<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Student\StudentAuthController;
use App\Http\Controllers\Api\Student\CourseController;
use App\Http\Controllers\Api\Student\CoursePurchaseController;
use App\Http\Controllers\Api\Student\ExamAttemptController;
use App\Http\Controllers\Api\Student\AIExamGradingController;
use App\Http\Controllers\Api\Student\PurchasedCourceController;
use App\Http\Controllers\Api\PublicCourseController;
use App\Http\Controllers\Api\SiteInformationController;

// Admin API (default users)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/profile', fn (Request $r) => $r->user()); 
});

// Student API
Route::prefix('student')->group(function () {
    Route::post('/register', [StudentAuthController::class, 'register']);
    Route::post('/login', [StudentAuthController::class, 'login']);

    Route::middleware('auth:student')->group(function () {
        Route::post('/profile_update', [StudentAuthController::class, 'profile_update']);
        Route::get('/me', [StudentAuthController::class, 'me']);
        Route::post('/logout', [StudentAuthController::class, 'logout']);
        
        // Course routes
        Route::get('/courses', [CourseController::class, 'index']);
        Route::get('/courses/featured', [CourseController::class, 'featured']);
        Route::get('/courses/single/{id}', [CourseController::class, 'show']);
        Route::get('/courses/categories', [CourseController::class, 'categories']);
        Route::get('/courses/category/{categoryId}', [CourseController::class, 'byCategory']);
        Route::get('/courses/search', [CourseController::class, 'search']);
        Route::get('/courses/statistics', [CourseController::class, 'statistics']);
        
        // Course purchase routes
        Route::post('/courses/purchase', [CoursePurchaseController::class, 'purchase']);
        Route::post('/courses/validate-coupon', [CoursePurchaseController::class, 'validateCoupon']);
        Route::get('/courses/available-coupons', [CoursePurchaseController::class, 'availableCoupons']);
        Route::get('/purchases', [CoursePurchaseController::class, 'purchaseHistory']);
        
        // Exam attempt routes
        Route::post('/exam-attempts/start', [ExamAttemptController::class, 'start']);
        Route::post('/exam-attempts/{attemptId}/submit-answer', [ExamAttemptController::class, 'submitAnswer']);
        Route::post('/exam-attempts/{attemptId}/submit-exam', [ExamAttemptController::class, 'submitExam']);
        Route::post('/exam-attempts/{attemptId}/update-marks', [ExamAttemptController::class, 'updateAnswerMarks']);
        Route::post('/exam-attempts/{attemptId}/ai-grade', [AIExamGradingController::class, 'aiGradeAnswer']);
        Route::post('/exam-attempts/{attemptId}/ai-grade-multiple', [AIExamGradingController::class, 'aiGradeMultipleAnswers']);
        Route::get('/exam-attempts/history', [ExamAttemptController::class, 'history']);
        Route::get('/exam-attempts/{attemptId}', [ExamAttemptController::class, 'show']);
        
        // Purchased courses routes
        Route::get('/purchased-courses', [PurchasedCourceController::class, 'index']);
        Route::get('/purchased-courses/{courseId}', [PurchasedCourceController::class, 'show']);
        Route::get('/purchased-courses/{courseId}/modules', [PurchasedCourceController::class, 'modules']);
        Route::get('/purchased-courses/{courseId}/module-videos', [PurchasedCourceController::class, 'moduleVideos']);
        Route::get('/purchased-courses/{courseId}/modules/{moduleId}/videos', [PurchasedCourceController::class, 'moduleVideosByModule']);
    });
});



    // Public Course routes
    Route::get('/courses', [PublicCourseController::class, 'index']);
    Route::get('/courses/featured', [PublicCourseController::class, 'featured']);
    Route::get('/courses/single/{id}', [PublicCourseController::class, 'show']);
    Route::get('/courses/categories', [PublicCourseController::class, 'categories']);
    Route::get('/courses/category/{categoryId}', [PublicCourseController::class, 'byCategory']);
    Route::get('/courses/search', [PublicCourseController::class, 'search']);
    Route::get('/courses/statistics', [PublicCourseController::class, 'statistics']);

    // Site information routes
    Route::get('/site-information', [SiteInformationController::class, 'siteInformation']);