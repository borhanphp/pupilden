<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Student\StudentAuthController;

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
        
    });
});