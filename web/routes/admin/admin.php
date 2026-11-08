<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Dedicated routes for the Secure ThreatShield Admin Web Dashboard.
| Contains Account Recovery, Lost Device Kill Switch, Notifications, and Settings.
|
*/

Route::prefix('admin')->group(function () {
    // ── Main Dashboard (Account Recovery & Device Kill Switch) ──
    Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');

    // ── Security Notifications Center ──
    Route::get('/notifications', [AdminDashboardController::class, 'notifications'])->name('admin.notifications');

    // ── Admin Security & Recovery Settings ──
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');

    // ── Action Endpoints ──
    Route::post('/recovery/generate-code', [AdminDashboardController::class, 'generateBypassCode'])->name('admin.recovery.generate');
    Route::post('/devices/lockdown', [AdminDashboardController::class, 'lockdownDevice'])->name('admin.devices.lockdown');
    Route::post('/settings/save', [AdminDashboardController::class, 'saveSettings'])->name('admin.settings.save');
});
