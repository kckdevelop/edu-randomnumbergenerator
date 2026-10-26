<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::middleware(['web', 'auth'])->group(function () {
    // Endpoint API Utama untuk simulasi spin slot
    Route::post('/spin', [GameController::class, 'spin'])->name('api.spin');
});
