<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Diretor\CompanyController;

Route::middleware(['auth', 'verified', 'role:diretor'])->group(function () {
    // como o diretor só mexe na própria empresa, vamos limitar as ações
    Route::get('companies', [CompanyController::class, 'index'])->name('diretor.companies.index');
    Route::get('companies/{company}/edit', [CompanyController::class, 'edit'])->name('diretor.companies.edit');
    Route::put('companies/{company}', [CompanyController::class, 'update'])->name('diretor.companies.update');
});
