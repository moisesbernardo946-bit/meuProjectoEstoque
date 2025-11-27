<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\entityProductController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('entity_product', entityProductController::class)
        ->names('almoxarife.entity_products');
});
