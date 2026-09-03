<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GameController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/quick-login/{id}', [AuthController::class, 'quickLogin'])->name('quick-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Session Auth)
Route::middleware(['auth'])->group(function () {

    // Dashboard Admin (Guru)
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/update-setting/{id}', [AdminController::class, 'updateSetting'])->name('admin.update-setting');
    Route::post('/admin/reset-balance/{id}', [AdminController::class, 'resetBalance'])->name('admin.reset-balance');
    Route::get('/admin/live-logs', [AdminController::class, 'liveLogs'])->name('admin.live-logs');

    // Slot Player (Siswa)
    Route::get('/slot', [GameController::class, 'index'])->name('player.slot');
    Route::post('/spin', [GameController::class, 'spin'])->name('player.spin');
});
