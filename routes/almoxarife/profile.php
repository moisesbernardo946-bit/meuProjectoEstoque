<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\ProfileController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    // Perfil
    Route::get('/almoxarife_perfil', [ProfileController::class, 'profile'])
        ->name('almoxarife.almoxarife_profile');

    Route::put('/almoxarife_perfil', [ProfileController::class, 'updateProfile'])
        ->name('almoxarife.almoxarife_profile.update');

    Route::put('/almoxarife_perfil/password', [ProfileController::class, 'updatePassword'])
        ->name('almoxarife.almoxarife_profile.password');
});
