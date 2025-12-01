<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\EntryProductController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    
    Route::resource('almoxarife/entry_product', EntryProductController::class)
        ->names('almoxarife.entry_products');

    Route::get('almoxarife/entry_products-export-excel', [EntryProductController::class, 'exportExcel'])
        ->name('almoxarife.entry_products.export.excel');

    Route::get('almoxarife/entry_products-export-pdf', [EntryProductController::class, 'exportPdf'])
        ->name('almoxarife.entry_products.export.pdf');
});

