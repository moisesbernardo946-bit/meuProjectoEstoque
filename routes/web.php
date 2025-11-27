<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// ======== REQUERENTE ========
foreach (glob(__DIR__ . '/requerente/*.php') as $file) {
    require $file;
}

// ======== ALMOXARIFE ========
foreach (glob(__DIR__ . '/almoxarife/*.php') as $file) {
    require $file;
}

// ======== DIRETOR ========
foreach (glob(__DIR__ . '/diretor/*.php') as $file) {
    require $file;
}

// ======== COMPRADOR ========
foreach (glob(__DIR__ . '/comprador/*.php') as $file) {
    require $file;
}

// ======== FINANCEIRO ========
foreach (glob(__DIR__ . '/financeiro/*.php') as $file) {
    require $file;
}

// ======== MOTORISTA ========
foreach (glob(__DIR__ . '/motorista/*.php') as $file) {
    require $file;
}
