<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\ProfileController;

Route::middleware(['auth', 'verified', 'role:comprador'])->group(function () {
    // Perfil
    Route::get('/comprador_perfil', [ProfileController::class, 'profile'])
        ->name('comprador.comprador_profile');

    Route::put('/comprador_perfil', [ProfileController::class, 'updateProfile'])
        ->name('comprador.comprador_profile.update');

    Route::put('/comprador_perfil/password', [ProfileController::class, 'updatePassword'])
        ->name('comprador.comprador_profile.password');
});
