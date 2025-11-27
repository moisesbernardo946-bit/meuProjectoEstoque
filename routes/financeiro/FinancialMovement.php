<?php

use App\Http\Controllers\Financeiro\FinancialMovementController;

Route::middleware(['auth', 'verified', 'role:financeiro'])->group(function () {

    // Lançamentos financeiros
    Route::get('financeiro/movimentos', [FinancialMovementController::class, 'index'])
        ->name('financeiro.financial_movements.index');

    Route::get('financeiro/movimentos/create', [FinancialMovementController::class, 'create'])
        ->name('financeiro.financial_movements.create');

    Route::post('financeiro/movimentos', [FinancialMovementController::class, 'store'])
        ->name('financeiro.financial_movements.store');

    Route::get('financeiro/movimentos/{financial_movement}/edit', [FinancialMovementController::class, 'edit'])
        ->name('financeiro.financial_movements.edit');

    Route::put('financeiro/movimentos/{financial_movement}', [FinancialMovementController::class, 'update'])
        ->name('financeiro.financial_movements.update');

    Route::delete('financeiro/movimentos/{financial_movement}', [FinancialMovementController::class, 'destroy'])
        ->name('financeiro.financial_movements.destroy');
});
