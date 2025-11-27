<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Diretor\DiretorRequisitionController;

Route::middleware(['auth', 'verified', 'role:diretor'])->group(function () {
Route::middleware(['auth', 'verified', 'role:diretor'])
    ->prefix('diretor')
    ->name('diretor.')
    ->group(function () {

        Route::get('/requisitions', [DiretorRequisitionController::class, 'index'])
            ->name('requisitions.index');

    });

    Route::get('requisitions/{id}', [DiretorRequisitionController::class, 'show'])
        ->name('diretor.requisitions.show');

    Route::get('requisitions/{id}/approval', [DiretorRequisitionController::class, 'approvalForm'])
        ->name('diretor.requisitions.approval.form');

    Route::post('requisitions/{id}/approval', [DiretorRequisitionController::class, 'approvalStore'])
        ->name('diretor.requisitions.approval.store');
});
