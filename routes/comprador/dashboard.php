<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Comprador\CompradorController;

Route::middleware(['auth', 'verified', 'role:comprador'])->group(function () {
    Route::get('/comprador/dashboard', [CompradorController::class, 'index'])
        ->name('comprador.dashboard');
});
