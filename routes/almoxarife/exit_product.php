<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\ExitProductControllerU;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    
    Route::resource('almoxarife/exit_product', ExitProductControllerU::class)
        ->names('almoxarife.exit_products');

    Route::get('almoxarife/exit_products-export-excel', [ExitProductControllerU::class, 'exportExcel'])
        ->name('almoxarife.exit_products.export.excel');

    Route::get('almoxarife/exit_products-export-pdf', [ExitProductControllerU::class, 'exportPdf'])
        ->name('almoxarife.exit_products.export.pdf');
});
