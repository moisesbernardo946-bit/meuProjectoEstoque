<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Almoxarife\RequisitionController;

Route::middleware(['auth', 'verified', 'role:almoxarife'])->group(function () {
    Route::resource('requisition', RequisitionController::class)
        ->names('almoxarife.requisitions');

    Route::get('client-products',[RequisitionController::class, 'getClientProducts'])
        ->name('almoxarife.requisitions.client-products');


    // Individual – para os botões na página SHOW
    Route::get('requisitions/{id}/export/pdf', [RequisitionController::class, 'exportPdf'])->name('almoxarife.requisitions.export.pdf');

    Route::get('requisitions/{id}/export/excel', [RequisitionController::class, 'exportExcel'])->name('almoxarife.requisitions.export.excel');

    //reber produtos pela requisição
    Route::get('almoxarife/requisitions/{id}/receive', [RequisitionController::class, 'receiveForm'])->name('almoxarife.requisitions.receive');
    Route::post('almoxarife/requisitions/{id}/receive', [RequisitionController::class, 'receiveStore'])->name('almoxarife.requisitions.receive.store');
});
