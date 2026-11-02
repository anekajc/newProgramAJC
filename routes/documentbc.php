<?php

use App\Http\Controllers\DocumentBc\CeisaAuthController;
use App\Http\Controllers\DocumentBc\DocumentBc23Controller;
use App\Http\Controllers\DocumentBc\DocumentBc25Controller;
use App\Http\Controllers\DocumentBc\DocumentBc261Controller;
use App\Http\Controllers\DocumentBc\DocumentBc262Controller;
use App\Http\Controllers\DocumentBc\DocumentBc40Controller;
use App\Http\Controllers\DocumentBc\DocumentBc41Controller;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Documentbc 23
    Route::get('/documentbc23', [DocumentBc23Controller::class, 'index']);
    Route::get('/documentbc23loadoutstanding', [DocumentBc23Controller::class, 'loadOutstanding']);
    Route::get('/documentbc23loadall', [DocumentBc23Controller::class, 'loadAll']);
    Route::get('/documentbc23spsumber', [DocumentBc23Controller::class, 'spSumber']);
    Route::get('/documentbc23lisths', [DocumentBc23Controller::class, 'listHs']);
    Route::get('/documentbc23spdetail', [DocumentBc23Controller::class, 'spDetail']);
    Route::post('/documentbc23spadd', [DocumentBc23Controller::class, 'spAdd']);
    Route::post('/documentbc23spedit', [DocumentBc23Controller::class, 'spEdit']);
    Route::post('/documentbc23spdelete', [DocumentBc23Controller::class, 'spDelete']);
    Route::post('/documentbc23spkirim', [DocumentBc23Controller::class, 'spKirim']);
    Route::get('/documentbc23sppreview', [DocumentBc23Controller::class, 'spPreview']);

    // Documentbc 25
    Route::get('/documentbc25', [DocumentBc25Controller::class, 'index']);
    Route::get('/documentbc25loadall', [DocumentBc25Controller::class, 'loadAll']);
    Route::get('/documentbc25lisths', [DocumentBc25Controller::class, 'listHs']);
    Route::get('/documentbc25spdetail', [DocumentBc25Controller::class, 'spDetail']);
    Route::post('/documentbc25spadd', [DocumentBc25Controller::class, 'spAdd']);
    Route::post('/documentbc25spedit', [DocumentBc25Controller::class, 'spEdit']);
    Route::post('/documentbc25spdelete', [DocumentBc25Controller::class, 'spDelete']);
    Route::post('/documentbc25spkirim', [DocumentBc25Controller::class, 'spKirim']);
    Route::get('/documentbc25sppreview', [DocumentBc25Controller::class, 'spPreview']);

    // Documentbc 40
    Route::get('/documentbc40', [DocumentBc40Controller::class, 'index']);
    Route::get('/documentbc40loadoutstanding', [DocumentBc40Controller::class, 'loadOutstanding']);
    Route::get('/documentbc40loadall', [DocumentBc40Controller::class, 'loadAll']);
    Route::get('/documentbc40spsumber', [DocumentBc40Controller::class, 'spSumber']);
    Route::get('/documentbc40lisths', [DocumentBc40Controller::class, 'listHs']);
    Route::get('/documentbc40spdetail', [DocumentBc40Controller::class, 'spDetail']);
    Route::post('/documentbc40spadd', [DocumentBc40Controller::class, 'spAdd']);
    Route::post('/documentbc40spedit', [DocumentBc40Controller::class, 'spEdit']);
    Route::post('/documentbc40spdelete', [DocumentBc40Controller::class, 'spDelete']);
    Route::post('/documentbc40spkirim', [DocumentBc40Controller::class, 'spKirim']);
    Route::get('/documentbc40sppreview', [DocumentBc40Controller::class, 'spPreview']);

    // Documentbc 41
    Route::get('/documentbc41', [DocumentBc41Controller::class, 'index']);
    Route::get('/documentbc41loadall', [DocumentBc41Controller::class, 'loadAll']);
    Route::get('/documentbc41lisths', [DocumentBc41Controller::class, 'listHs']);
    Route::get('/documentbc41spdetail', [DocumentBc41Controller::class, 'spDetail']);
    Route::post('/documentbc41spadd', [DocumentBc41Controller::class, 'spAdd']);
    Route::post('/documentbc41spedit', [DocumentBc41Controller::class, 'spEdit']);
    Route::post('/documentbc41spdelete', [DocumentBc41Controller::class, 'spDelete']);
    Route::post('/documentbc41spkirim', [DocumentBc41Controller::class, 'spKirim']);
    Route::get('/documentbc41sppreview', [DocumentBc41Controller::class, 'spPreview']);

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
    Route::get('/documentbc262', [DocumentBc262Controller::class, 'index']);
    Route::get('/documentbc262loadoutstanding', [DocumentBc262Controller::class, 'loadOutstanding']);
    Route::get('/documentbc262loadall', [DocumentBc262Controller::class, 'loadAll']);
    Route::get('/documentbc262lisths', [DocumentBc262Controller::class, 'listHs']);
    Route::get('/documentbc262spdetail', [DocumentBc262Controller::class, 'spDetail']);
    Route::post('/documentbc262spadd', [DocumentBc262Controller::class, 'spAdd']);
    Route::post('/documentbc262spedit', [DocumentBc262Controller::class, 'spEdit']);
    Route::post('/documentbc262spdelete', [DocumentBc262Controller::class, 'spDelete']);
    Route::post('/documentbc262spkirim', [DocumentBc262Controller::class, 'spKirim']);
    Route::get('/documentbc262sppreview', [DocumentBc262Controller::class, 'spPreview']);

});
