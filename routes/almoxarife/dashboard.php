<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\AlmoxarifeController;

Route::middleware(['auth',  'verified', 'role:almoxarife'])->group(function () {
    Route::get('/almoxarife/dashboard', [AlmoxarifeController::class, 'index'])
        ->name('almoxarife.dashboard');
});
