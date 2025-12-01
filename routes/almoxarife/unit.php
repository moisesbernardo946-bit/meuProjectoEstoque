<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\UnitController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('unit', UnitController::class)
        ->names('almoxarife.units');
});
