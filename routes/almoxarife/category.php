<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\categoryController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('category', categoryController::class)
        ->names('almoxarife.categories');
});
