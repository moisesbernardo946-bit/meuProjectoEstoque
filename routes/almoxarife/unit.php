<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\unitController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('unit', unitController::class)
        ->names('almoxarife.units');
});
