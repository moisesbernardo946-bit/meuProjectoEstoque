<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Diretor\ClientController;

Route::middleware(['auth', 'verified', 'role:diretor'])->group(function () {
    Route::resource('client', ClientController::class)
        ->names('diretor.clientes');
});
