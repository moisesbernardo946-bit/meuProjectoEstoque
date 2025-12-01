<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\CategoryController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('almoxarife/category', CategoryController::class)
        ->names('almoxarife.categories');
});
