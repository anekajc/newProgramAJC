<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Produksi\ProduksiBomController;

// BOM ==========================================================
Route::get('/produksibom', [ProduksiBomController::class, 'index'])->middleware('auth');
Route::get('/produksibomloadallbarang', [ProduksiBomController::class, 'loadAllBarang'])->middleware('auth');
Route::get('/produksibomlistbom', [ProduksiBomController::class, 'listBom'])->middleware('auth');
Route::get('/produksibomlistproses', [ProduksiBomController::class, 'listProses'])->middleware('auth');
Route::get('/produksibomlistdetail', [ProduksiBomController::class, 'listDetail'])->middleware('auth');

//========================================================================================================================================
