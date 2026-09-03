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
    Route::get('/admin/realtime-data', [AdminController::class, 'realtimeData'])->name('admin.realtime-data');
    Route::post('/admin/update-setting/{id}', [AdminController::class, 'updateSetting'])->name('admin.update-setting');
    Route::post('/admin/reset-balance/{id}', [AdminController::class, 'resetBalance'])->name('admin.reset-balance');
    Route::match(['DELETE', 'POST'], '/admin/delete-player/{id}', [AdminController::class, 'deletePlayer'])->name('admin.delete-player');
    Route::post('/admin/delete-players-bulk', [AdminController::class, 'bulkDeletePlayers'])->name('admin.delete-players-bulk');
    Route::post('/admin/approve-deposit/{id}', [AdminController::class, 'approveDeposit'])->name('admin.approve-deposit');
    Route::post('/admin/reject-deposit/{id}', [AdminController::class, 'rejectDeposit'])->name('admin.reject-deposit');
    Route::get('/admin/live-logs', [AdminController::class, 'liveLogs'])->name('admin.live-logs');

    // Slot Player (Siswa)
    Route::get('/slot', [GameController::class, 'index'])->name('player.slot');
    Route::post('/spin', [GameController::class, 'spin'])->name('player.spin');
    Route::post('/deposit', [GameController::class, 'requestDeposit'])->name('player.deposit');
    Route::get('/user-deposits', [GameController::class, 'userDeposits'])->name('player.user-deposits');
});
