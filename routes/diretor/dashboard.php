<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Diretor\DiretorController;

Route::middleware(['auth',  'verified', 'role:diretor'])->group(function () {
    Route::get('/diretor/dashboard', [DiretorController::class, 'index'])
        ->name('diretor.dashboard');
});
