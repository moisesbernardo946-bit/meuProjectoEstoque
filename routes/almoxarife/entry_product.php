<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\EntryProductControllerU;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {

    Route::resource('almoxarife/entry_product', EntryProductControllerU::class)
        ->names('almoxarife.entry_products');

    Route::get('almoxarife/entry_products-export-excel', [EntryProductControllerU::class, 'exportExcel'])
        ->name('almoxarife.entry_products.export.excel');

    Route::get('almoxarife/entry_products-export-pdf', [EntryProductControllerU::class, 'exportPdf'])
        ->name('almoxarife.entry_products.export.pdf');
});

