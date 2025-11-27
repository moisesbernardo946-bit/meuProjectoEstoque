<?php

use App\Http\Controllers\Motorista\MotoristaProgramController;

Route::middleware(['auth', 'verified', 'role:motorista'])->prefix('motorista')->group(function () {
    Route::get('motorista/programacoes', [MotoristaProgramController::class, 'index'])
        ->name('motorista.motoristaPrograms.index');

    Route::get('motorista/programacoes/{program}', [MotoristaProgramController::class, 'show'])
        ->name('motorista.motoristaPrograms.show');

    // Concluir programação (marcar itens)
    Route::post('motorista/programacoes/{program}/concluir', [MotoristaProgramController::class, 'conclude'])
        ->name('motorista.motoristaPrograms.conclude');

    // anexos motorista
    Route::post('motorista/programacoes/{program}/attachments', [MotoristaProgramController::class, 'uploadAttachment'])
        ->name('motorista.motoristaPrograms.attachments.upload');

    Route::get('motorista/attachments/{attachment}/download', [MotoristaProgramController::class, 'downloadAttachment'])
        ->name('motorista.motoristaPrograms.attachments.download');

    Route::delete('motorista/attachments/{attachment}', [MotoristaProgramController::class, 'deleteAttachment'])
        ->name('motorista.motoristaPrograms.attachments.delete');

    // export
    Route::get('motorista/programacoes/{program}/export/pdf', [MotoristaProgramController::class, 'exportPdf'])
        ->name('motorista.motoristaPrograms.export.pdf');

    Route::get('motorista/programacoes/{program}/export/excel', [MotoristaProgramController::class, 'exportExcel'])
        ->name('motorista.motoristaPrograms.export.excel');
});
