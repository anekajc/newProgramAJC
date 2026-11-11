<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Produksi\ProduksiBomController;
use App\Http\Controllers\Produksi\ProduksiSPKController;
use App\Http\Controllers\Produksi\ProduksiPemakaianBahanController;
use App\Http\Controllers\Produksi\ProduksiPemakaianTambahanController;

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
Route::get('/produksispklistbarangjadi', [ProduksiSPKController::class, 'listBarangJadiBrowse'])->middleware('auth');
Route::get('/produksispklistbombybarang', [ProduksiSPKController::class, 'listBomByBarang'])->middleware('auth');
Route::get('/produksispklistbj', [ProduksiSPKController::class, 'listBJ'])->middleware('auth');
Route::get('/produksispkheader', [ProduksiSPKController::class, 'header'])->middleware('auth');
Route::post('/produksispkhapus', [ProduksiSPKController::class, 'hapusSPK'])->middleware('auth');
Route::post('/produksispkspotorisasi', [ProduksiSPKController::class, 'spOtorisasi'])->middleware('auth');
Route::post('/produksispkspbatalotorisasi', [ProduksiSPKController::class, 'spBatalOtorisasi'])->middleware('auth');
Route::get('/produksispklistproses', [ProduksiSPKController::class, 'listProsesSPK'])->middleware('auth');
Route::post('/produksispkspspkjd', [ProduksiSPKController::class, 'spSPKJD'])->middleware('auth');
Route::post('/produksispkspspk', [ProduksiSPKController::class, 'spSPK'])->middleware('auth');

// PEMAKAIAN BAHAN ======================================

Route::get('/produksipemakaianbahan', [ProduksiPemakaianBahanController::class, 'index'])->middleware('auth');
Route::get('/produksipemakaianbahanloadoutstanding', [ProduksiPemakaianBahanController::class, 'loadOutstanding'])->middleware('auth');
Route::get('/produksipemakaianbahanloadbp', [ProduksiPemakaianBahanController::class, 'loadBP'])->middleware('auth');
Route::get('/produksipemakaianbahannextnobukti', [ProduksiPemakaianBahanController::class, 'nextNoBukti'])->middleware('auth');
Route::get('/produksipemakaianbahanlistgudang', [ProduksiPemakaianBahanController::class, 'listGudang'])->middleware('auth');
Route::get('/produksipemakaianbahanambilspk', [ProduksiPemakaianBahanController::class, 'ambilSPK'])->middleware('auth');
Route::post('/produksipemakaianbahansimpanbp', [ProduksiPemakaianBahanController::class, 'simpanBP'])->middleware('auth');
Route::get('/produksipemakaianbahanloaddetailbp', [ProduksiPemakaianBahanController::class, 'loadDetailBP'])->middleware('auth');
Route::post('/produksipemakaianbahanupdatebp', [ProduksiPemakaianBahanController::class, 'updateBP'])->middleware('auth');
Route::post('/produksipemakaianbahanhapusbp', [ProduksiPemakaianBahanController::class, 'hapusBP'])->middleware('auth');
Route::post('/produksipemakaianbahanspotorisasi', [ProduksiPemakaianBahanController::class, 'spOtorisasi'])->middleware('auth');
Route::post('/produksipemakaianbahanspbatalotorisasi', [ProduksiPemakaianBahanController::class, 'spBatalOtorisasi'])->middleware('auth');

// PEMAKAIAN TAMBAHAN
Route::get('/produksipemakaiantambahan', [ProduksiPemakaianTambahanController::class, 'index'])->middleware('auth');
Route::get('/produksipemakaiantambahanloadbp', [ProduksiPemakaianTambahanController::class, 'loadBP'])->middleware('auth');
Route::get('/produksipemakaiantambahanlistgudang', [ProduksiPemakaianTambahanController::class, 'listGudang'])->middleware('auth');
Route::get('/produksipemakaiantambahanlistbarang', [ProduksiPemakaianTambahanController::class, 'listBarang'])->middleware('auth');
Route::get('/produksipemakaiantambahanloaddetailbp', [ProduksiPemakaianTambahanController::class, 'loadDetailBP'])->middleware('auth');
Route::post('/produksipemakaiantambahantambahitem', [ProduksiPemakaianTambahanController::class, 'tambahItem'])->middleware('auth');
Route::post('/produksipemakaiantambahanhapusitem', [ProduksiPemakaianTambahanController::class, 'hapusItem'])->middleware('auth');
Route::post('/produksipemakaiantambahanupdatebp', [ProduksiPemakaianTambahanController::class, 'updateBP'])->middleware('auth');
Route::post('/produksipemakaiantambahanspotorisasi', [ProduksiPemakaianTambahanController::class, 'spOtorisasi'])->middleware('auth');
Route::post('/produksipemakaiantambahanspbatalotorisasi', [ProduksiPemakaianTambahanController::class, 'spBatalOtorisasi'])->middleware('auth');

//========================================================================================================================================
