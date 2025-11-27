<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\EntryProductController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('entry_product', EntryProductController::class)
        ->names('almoxarife.entry_products');

    Route::get('entry_products-export-excel', [EntryProductController::class, 'exportExcel'])
        ->name('almoxarife.entry_products.export.excel');

    Route::get('entry_products-export-pdf', [EntryProductController::class, 'exportPdf'])
        ->name('almoxarife.entry_products.export.pdf');
});

