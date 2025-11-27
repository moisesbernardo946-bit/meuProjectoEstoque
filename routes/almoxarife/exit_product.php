<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\ExitProductController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('exit_product', ExitProductController::class)
        ->names('almoxarife.exit_products');

    Route::get('exit_products-export-excel', [ExitProductController::class, 'exportExcel'])
        ->name('almoxarife.exit_products.export.excel');

    Route::get('exit_products-export-pdf', [ExitProductController::class, 'exportPdf'])
        ->name('almoxarife.exit_products.export.pdf');
});
