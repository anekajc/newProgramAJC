<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Produksi\ProduksiBomController;
use App\Http\Controllers\Produksi\ProduksiSPKController;

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

//SPK ===============================================================

Route::get('/produksispk', [ProduksiSPKController::class, 'index'])->middleware('auth');
Route::get('/produksispkloadall', [ProduksiSPKController::class, 'loadAll'])->middleware('auth');
Route::get('/produksispklistdetail', [ProduksiSPKController::class, 'listDetail'])->middleware('auth');
Route::get('/produksispknextnobukti', [ProduksiSPKController::class, 'nextNoBukti'])->middleware('auth');
Route::get('/produksispklistmesin', [ProduksiSPKController::class, 'listMesin'])->middleware('auth');
Route::get('/produksispklistbombrowse', [ProduksiSPKController::class, 'listBomBrowse'])->middleware('auth');
Route::get('/produksispklistbarangjadibybom', [ProduksiSPKController::class, 'listBarangJadiByBom'])->middleware('auth');
Route::get('/produksispklistbj', [ProduksiSPKController::class, 'listBJ'])->middleware('auth');
Route::get('/produksispkheader', [ProduksiSPKController::class, 'header'])->middleware('auth');
Route::post('/produksispkhapus', [ProduksiSPKController::class, 'hapusSPK'])->middleware('auth');
Route::post('/produksispkspspkjd', [ProduksiSPKController::class, 'spSPKJD'])->middleware('auth');
Route::post('/produksispkspspk', [ProduksiSPKController::class, 'spSPK'])->middleware('auth');


//========================================================================================================================================
