<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Diretor\ProfileController;

Route::middleware(['auth', 'verified', 'role:diretor'])->group(function () {
    // Perfil
    Route::get('/perfil', [ProfileController::class, 'profile'])
        ->name('diretor.profile');

    Route::put('/perfil', [ProfileController::class, 'updateProfile'])
        ->name('diretor.profile.update');

    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])
        ->name('diretor.profile.password');
});
