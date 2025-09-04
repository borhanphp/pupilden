<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\Auth\UserController;



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
});


require __DIR__.'/auth.php';