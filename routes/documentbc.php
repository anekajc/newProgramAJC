<?php

use App\Http\Controllers\DocumentBc\CeisaAuthController;
use App\Http\Controllers\DocumentBc\DocumentBc261Controller;
use Illuminate\Support\Facades\Route;

// Controller BC 2.3 / 2.5 / 4.0 / 4.1 / 2.6.2 masih kosong (belum ada index), route-nya
// diaktifkan lagi saat modulnya dibuat.
// use App\Http\Controllers\DocumentBc\DocumentBc23Controller;
// use App\Http\Controllers\DocumentBc\DocumentBc25Controller;
// use App\Http\Controllers\DocumentBc\DocumentBc262Controller;
// use App\Http\Controllers\DocumentBc\DocumentBc40Controller;
// use App\Http\Controllers\DocumentBc\DocumentBc41Controller;

Route::middleware('auth')->group(function () {
    // Documentbc 23
    // Route::get('/documentbc23', [DocumentBc23Controller::class, 'index']);

    // Documentbc 25
    // Route::get('/documentbc25', [DocumentBc25Controller::class, 'index']);

    // Documentbc 40
    // Route::get('/documentbc40', [DocumentBc40Controller::class, 'index']);

    // Documentbc 41
    // Route::get('/documentbc41', [DocumentBc41Controller::class, 'index']);

    // Login CEISA (token per user, dipakai semua dokumen BC)
    Route::get('/bcceisastatus', [CeisaAuthController::class, 'status']);
    Route::post('/bcceisalogin', [CeisaAuthController::class, 'login']);

    // Documentbc 261
    Route::get('/documentbc261', [DocumentBc261Controller::class, 'index']);
    Route::get('/documentbc261loadall', [DocumentBc261Controller::class, 'loadAll']);
    Route::get('/documentbc261lisths', [DocumentBc261Controller::class, 'listHs']);
    Route::get('/documentbc261spdetail', [DocumentBc261Controller::class, 'spDetail']);
    Route::post('/documentbc261spadd', [DocumentBc261Controller::class, 'spAdd']);
    Route::post('/documentbc261spedit', [DocumentBc261Controller::class, 'spEdit']);
    Route::post('/documentbc261spdelete', [DocumentBc261Controller::class, 'spDelete']);
    Route::post('/documentbc261spkirim', [DocumentBc261Controller::class, 'spKirim']);
    Route::get('/documentbc261sppreview', [DocumentBc261Controller::class, 'spPreview']);

    // Documentbc262
    // Route::get('/documentbc262', [DocumentBc262Controller::class, 'index']);

});
