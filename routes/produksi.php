<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Produksi\ProduksiBomController;

// BOM ==========================================================
Route::get('/produksibom', [ProduksiBomController::class, 'index'])->middleware('auth');
Route::get('/produksibomloadallbarang', [ProduksiBomController::class, 'loadAllBarang'])->middleware('auth');
Route::get('/produksibomnextkodebom', [ProduksiBomController::class, 'nextKodeBom'])->middleware('auth');
Route::get('/produksibomlistbom', [ProduksiBomController::class, 'listBom'])->middleware('auth');
Route::get('/produksibomlistproses', [ProduksiBomController::class, 'listProses'])->middleware('auth');
Route::get('/produksibomlistkodeproses', [ProduksiBomController::class, 'listKodeProses'])->middleware('auth');
Route::post('/produksibomspbomproses', [ProduksiBomController::class, 'spBOMProses'])->middleware('auth');
Route::get('/produksibomlistkodebarang', [ProduksiBomController::class, 'listKodeBarang'])->middleware('auth');
Route::post('/produksibomspbom', [ProduksiBomController::class, 'spBOM'])->middleware('auth');
Route::get('/produksibomlistdetail', [ProduksiBomController::class, 'listDetail'])->middleware('auth');
Route::get('/produksibomlistdetailbybom', [ProduksiBomController::class, 'listDetailByBom'])->middleware('auth');

//========================================================================================================================================
