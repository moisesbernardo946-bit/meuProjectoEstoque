<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\ZoneController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('zone', ZoneController::class)
        ->names('almoxarife.zones');
});
