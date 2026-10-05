<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utilitas\HitungUlangStockController;
use App\Http\Controllers\Utilitas\PostingController;

Route::middleware('auth')->group(function () {
    Route::get('/hitungulangstock', [HitungUlangStockController::class, 'index']);
    Route::post('/hitungulangstockproses', [HitungUlangStockController::class, 'proses']);
    Route::get('/posting', [PostingController::class, 'index']);
    Route::post('/postingproses', [PostingController::class, 'proses']);
});
