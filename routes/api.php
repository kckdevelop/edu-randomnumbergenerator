<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::middleware(['web', 'auth'])->group(function () {
    // Endpoint API Utama untuk simulasi spin slot & fitur spin gratis
    Route::post('/spin', [GameController::class, 'spin'])->name('api.spin');
    Route::post('/claim-free-spins', [GameController::class, 'claimFreeSpins'])->name('api.claim-free-spins');
    Route::post('/buy-free-spins', [GameController::class, 'buyFreeSpins'])->name('api.buy-free-spins');
});
