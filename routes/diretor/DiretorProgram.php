<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Diretor\DiretorProgramController;

Route::middleware(['auth', 'verified', 'role:diretor'])->group(function () {
    Route::get('diretor/programacoes', [DiretorProgramController::class, 'index'])
        ->name('diretor.diretorprograms.index');

    Route::get('diretor/programacoes/{program}', [DiretorProgramController::class, 'show'])
        ->name('diretor.diretorprograms.show');

    Route::post('diretor/programacoes/{program}/aprovar', [DiretorProgramController::class, 'approve'])
        ->name('diretor.diretorprograms.approve');
});
