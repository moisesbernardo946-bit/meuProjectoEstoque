<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Comprador\PurchaseProgramController;

Route::middleware(['auth', 'verified', 'role:comprador'])->group(function () {
    // Programações
    Route::get('comprador/programacoes', [PurchaseProgramController::class, 'index'])->name('comprador.programs.index');

    // Programações de compra
    Route::get('comprador/programacoes/create', [PurchaseProgramController::class, 'create'])->name('comprador.programs.create');
    Route::post('comprador/programacoes', [PurchaseProgramController::class, 'store'])->name('comprador.programs.store');

    // show / edit / update da programação
    Route::get('comprador/programacoes/{program}', [PurchaseProgramController::class, 'show'])->name('comprador.programs.show');
    Route::get('comprador/programacoes/{program}/editar', [PurchaseProgramController::class, 'edit'])->name('comprador.programs.edit');
    Route::put('comprador/programacoes/{program}', [PurchaseProgramController::class, 'update'])->name('comprador.programs.update');

    //rotas para pdf e excel
    Route::get('comprador/programacoes/{program}/export/pdf', [PurchaseProgramController::class, 'exportPdf'])->name('comprador.programs.export.pdf');

    Route::get('comprador/programacoes/{program}/export/excel', [PurchaseProgramController::class, 'exportExcel'])->name('comprador.programs.export.excel');

    //rota para eliminar um anexo
    Route::delete('comprador/programacoes/{program}/anexos/{attachment}', [PurchaseProgramController::class, 'destroyAttachment'])->name('comprador.programs.attachments.destroy');

});