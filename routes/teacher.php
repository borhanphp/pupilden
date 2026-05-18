<?php

use App\Http\Controllers\Api\Teacher\TeacherAuthController;
use App\Http\Controllers\Api\Teacher\TeacherCourseController;
use App\Http\Controllers\Api\Teacher\TeacherDashboardController;
use App\Http\Controllers\Api\Teacher\TeacherVideoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Teacher API Routes
|--------------------------------------------------------------------------
| Prefix: /api/teacher
*/

// Public: login
Route::post('/login', [TeacherAuthController::class, 'login']);

// Authenticated teacher routes
Route::middleware('auth:teacher')->group(function () {

    // Auth
    Route::post('/logout',         [TeacherAuthController::class, 'logout']);
    Route::get('/me',              [TeacherAuthController::class, 'me']);
    Route::post('/profile',        [TeacherAuthController::class, 'updateProfile']); // POST with _method=PUT for FormData

    // Dashboard stats
    Route::get('/dashboard',       [TeacherDashboardController::class, 'index']);

    // Courses
    Route::get('/courses',         [TeacherCourseController::class, 'index']);
    Route::post('/courses',        [TeacherCourseController::class, 'store']);
    Route::get('/courses/{id}',    [TeacherCourseController::class, 'show']);
    Route::post('/courses/{id}',   [TeacherCourseController::class, 'update']); // POST with _method=PUT for FormData
    Route::delete('/courses/{id}', [TeacherCourseController::class, 'destroy']);

    // Categories (for course form)
    Route::get('/categories',      [TeacherCourseController::class, 'categories']);

    // Videos
    Route::get('/courses/{courseId}/videos', [TeacherVideoController::class, 'index']);
    Route::post('/videos',                   [TeacherVideoController::class, 'store']);
    Route::delete('/videos/{id}',            [TeacherVideoController::class, 'destroy']);
    Route::post('/videos/upload-url',        [TeacherVideoController::class, 'uploadUrl']);
});
