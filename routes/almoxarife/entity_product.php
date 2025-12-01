<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\EntityProductControllerU;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {

    Route::resource('almoxarife/entity_product', EntityProductControllerU::class)
        ->names('almoxarife.entity_products');

});
