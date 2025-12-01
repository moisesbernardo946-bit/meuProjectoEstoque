<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\ExitProductController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('almoxarife/exit_product', ExitProductController::class)
        ->names('almoxarife.exit_products');

    Route::get('almoxarife/exit_products-export-excel', [ExitProductController::class, 'exportExcel'])
        ->name('almoxarife.exit_products.export.excel');

    Route::get('almoxarife/exit_products-export-pdf', [ExitProductController::class, 'exportPdf'])
        ->name('almoxarife.exit_products.export.pdf');
});
