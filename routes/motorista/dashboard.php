<?php

use App\Http\Controllers\Motorista\MotoristaController;

Route::middleware(['auth', 'verified', 'role:motorista'])->prefix('motorista')->group(function () {
    Route::get('dashboard', [MotoristaController::class, 'index'])
        ->name('motorista.dashboard');
});
