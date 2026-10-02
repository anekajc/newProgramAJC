<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utilitas\HitungUlangStockController;

Route::middleware('auth')->group(function () {
    Route::get('/hitungulangstock', [HitungUlangStockController::class, 'index']);
    Route::post('/hitungulangstockproses', [HitungUlangStockController::class, 'proses']);
});
