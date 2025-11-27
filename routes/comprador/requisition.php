<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Comprador\CompradorRequisitionController;

Route::middleware(['auth', 'verified', 'role:comprador'])->group(function () {
    // Requisições disponíveis para programação
    Route::get('requisicoes', [CompradorRequisitionController::class, 'index'])
        ->name('comprador.requisitions.index');
});
