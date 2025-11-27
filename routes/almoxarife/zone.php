<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\zoneController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('zone', zoneController::class)
        ->names('almoxarife.zones');
});
