<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Diretor\UserController;

Route::middleware(['auth', 'verified', 'role:diretor'])->group(function () {
        Route::resource('users', UserController::class, [
            'as' => 'diretor', // gera nomes tipo diretor.users.index
        ])->except(['show']); // não precisamos do show agora
    });
