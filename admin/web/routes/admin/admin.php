<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Dedicated routes for the Secure ThreatShield Admin Web Dashboard.
| Account Recovery, Lost Device Kill Switch, Notifications, and Settings.
| The dashboard itself is admin/web/dashboard.blade.php (see routes/web.php).
|
*/

Route::prefix('admin')->group(function () {
    Route::get('/api/overview', [AdminDashboardController::class, 'overview'])->name('admin.api.overview');
    Route::post('/api/devices/{device}/action', [AdminDashboardController::class, 'deviceAction'])->name('admin.api.devices.action');
    Route::get('/users', [AdminDashboardController::class, 'section'])->defaults('section', 'users')->name('admin.users');
    Route::get('/sessions', [AdminDashboardController::class, 'section'])->defaults('section', 'sessions')->name('admin.sessions');
    Route::get('/recovery', [AdminDashboardController::class, 'section'])->defaults('section', 'recovery')->name('admin.recovery');
    Route::get('/audit', [AdminDashboardController::class, 'section'])->defaults('section', 'audit')->name('admin.audit');
    Route::get('/status', [AdminDashboardController::class, 'status'])->name('admin.status');
    // ── Security Notifications Center ──
    Route::get('/notifications', [AdminDashboardController::class, 'notifications'])->name('admin.notifications');

    // ── Admin Security & Recovery Settings ──
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');

    // ── Action Endpoints ──
    Route::post('/notifications/read', [AdminDashboardController::class, 'markNotificationsRead'])->name('admin.notifications.read');
    Route::post('/devices/lockdown', [AdminDashboardController::class, 'lockdownDevice'])->name('admin.devices.lockdown');
    Route::post('/settings/save', [AdminDashboardController::class, 'saveSettings'])->name('admin.settings.save');
});
