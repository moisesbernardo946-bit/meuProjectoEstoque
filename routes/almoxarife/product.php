<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\ProductController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('almoxarife/product', ProductController::class)
        ->names('almoxarife.products');
});
