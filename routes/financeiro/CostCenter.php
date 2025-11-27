<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Financeiro\CostCenterController;

Route::middleware(['auth', 'verified', 'role:financeiro'])->prefix('financeiro')->group(function () {

    Route::get('financeiro/centros-de-custo', [CostCenterController::class, 'index'])
        ->name('financeiro.cost_centers.index');

    Route::get('financeiro/centros-de-custo/create', [CostCenterController::class, 'create'])
        ->name('financeiro.cost_centers.create');

    Route::post('financeiro/centros-de-custo', [CostCenterController::class, 'store'])
        ->name('financeiro.cost_centers.store');

    Route::get('financeiro/centros-de-custo/{costCenter}', [CostCenterController::class, 'show'])
        ->name('financeiro.cost_centers.show');
});
