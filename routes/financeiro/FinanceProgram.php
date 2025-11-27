<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Financeiro\FinanceProgramController;

Route::middleware(['auth', 'verified', 'role:financeiro'])->group(function () {
    Route::get('programacoes', [FinanceProgramController::class, 'index'])
        ->name('financeiro.programs.index');

    Route::get('programacoes/{program}', [FinanceProgramController::class, 'show'])
        ->name('financeiro.programs.show');
});
