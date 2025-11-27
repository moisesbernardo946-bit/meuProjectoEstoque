<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Financeiro\FinanceiroController;

Route::middleware(['auth', 'verified', 'role:financeiro'])->group(function () {
    Route::get('/financeiro/dashboard', [FinanceiroController::class, 'index'])
        ->name('financeiro.dashboard');
});



