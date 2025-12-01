<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\EntityProductController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('entity_product', EntityProductController::class)
        ->names('almoxarife.entity_products');
});
