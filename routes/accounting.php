<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\BankController;
use App\Http\Controllers\Accounting\BonSementaraController;
use App\Http\Controllers\Accounting\CetakPengajuanDphController;
use App\Http\Controllers\Accounting\GiroDibukaController;
use App\Http\Controllers\Accounting\GiroDiterimaController;
use App\Http\Controllers\Accounting\KasController;
use App\Http\Controllers\Accounting\MemorialKoreksiController;
use App\Http\Controllers\Accounting\PelunasanPiutangDPPController;
use App\Http\Controllers\Accounting\PenerimaanDPPController;
use App\Http\Controllers\Accounting\PengajuanDPHController;
use App\Http\Controllers\Accounting\PengajuanDPHTunaiController;
use App\Http\Controllers\Accounting\PengajuanDPPController;

Route::middleware('auth')->group(function () {
// Route::get('/kas', [KasController::class, 'index']);
// Route::get('/kaslistkasheader', [KasController::class, 'listKasHeader']);
// Route::post('/kaslistlawan', [KasController::class, 'listLawan']);
// Route::post('/kaslistbon', [KasController::class, 'listBon']);
// Route::post('/kaslistsubcosting', [KasController::class, 'listSubCosting']);
// Route::post('/kaslistcosting', [KasController::class, 'listCosting']);
// Route::post('/kaschangekembaliuang', [KasController::class, 'changeKembaliUang']);

// Route::post('/kaslistakumulasi', [KasController::class, 'listAkumulasi']);

// Route::get('/kaslistakumulasiinput', [KasController::class, 'listAkumulasiInput']);
// Route::get('/kaslistbiayainput', [KasController::class, 'listBiayaInput']);

// Route::post('/kasgetnourutaktiva' , [KasController::class, 'getNoUrutAktiva']);
// Route::post('/kasspaddnewaktiva' , [KasController::class, 'spAddNewAktiva']);

// Route::post('/kaslistaktiva', [KasController::class, 'listAktiva']);
// Route::post('/kaslistdetailaktiva', [KasController::class, 'listDetailAktiva']);
// Route::post('/kasupdatedbaktivadet', [KasController::class, 'updateDBAktivaDet']);
// Route::post('/kasspaddaktiva', [KasController::class, 'spAddAktiva']);

// Route::get('/kaslistdepartemen', [KasController::class, 'listDepartemen']);
// Route::get('/kaslistdevisi', [KasController::class, 'listDevisi']);
// Route::get('/kaslistvalas', [KasController::class, 'listValas']);
// Route::post('/kasspadd', [KasController::class, 'spAdd']);
// Route::post('/kasspdetail', [KasController::class, 'getDetail']);
// Route::get('/kasloadall', [KasController::class, 'loadAll']);
// Route::get('/kaslistdph', [KasController::class, 'listDPH']);
// Route::get('/kaslistdphuht', [KasController::class, 'listDPHUHT']);
// Route::get('/kaslistdpp', [KasController::class, 'listDPP']);
// Route::get('/kaslistcustsupp', [KasController::class, 'listCustsupp']);
// Route::get('/kaslistcustsuppx', [KasController::class, 'listCustSuppX']);
// Route::post('/kaslisttunai', [KasController::class, 'listTunai']);
// Route::get('/kaslisttunaix', [KasController::class, 'listTunaiX']);
// Route::post('/kaslistcustsupptunai', [KasController::class, 'listCustSuppTunai']);


// Route::post('/kassptemphutpiut', [KasController::class, 'spTempHutPiut']);

// Route::get('/kaslistcustsuppumb', [KasController::class, 'listCustsuppUMB']);
// Route::post('/kasprosesumb', [KasController::class, 'prosesUMB']);
// Route::post('/kaslistumb', [KasController::class, 'listUMB']);
// Route::post('/kasspadddppdph', [KasController::class, 'spAddDPPDPH']);
// Route::post('/kasspaddtemprumjual', [KasController::class, 'spAddTempRUMJUAL']);
// Route::post('/kasspdeletetemprumjual', [KasController::class, 'spDeleteTempRUMJUAL']);
// Route::post('/kasspupdatetemprumjual', [KasController::class, 'spUpdateTempRUMJUAL']);
// Route::post('/kasspotorisasi', [KasController::class, 'spOtorisasi']);
// Route::post('/kasspbatalotorisasi', [KasController::class, 'spBatalOtorisasi']);
// Route::post('/kasdetailCetak', [KasController::class, 'getDetailCetak']);



// // Bank
// Route::get('/bank', [BankController::class, 'index']);
// Route::get('/banklistkasheader', [BankController::class, 'listKasHeader']);
// Route::post('/banklistlawan', [BankController::class, 'listLawan']);
// Route::post('/banklistsubcosting', [BankController::class, 'listSubCosting']);
// Route::post('/banklistcosting', [BankController::class, 'listCosting']);

// Route::post('/banklistakumulasi', [BankController::class, 'listAkumulasi']);

// Route::get('/banklistakumulasiinput', [KasController::class, 'listAkumulasiInput']);
// Route::get('/banklistbiayainput', [KasController::class, 'listBiayaInput']);

// Route::post('/bankgetnourutaktiva' , [KasController::class, 'getNoUrutAktiva']);
// Route::post('/bankspaddnewaktiva' , [KasController::class, 'spAddNewAktiva']);


// Route::post('/banklistaktiva', [BankController::class, 'listAktiva']);
// Route::post('/banklistdetailaktiva', [BankController::class, 'listDetailAktiva']);
// Route::post('/bankupdatedbaktivadet', [BankController::class, 'updateDBAktivaDet']);
// Route::post('/bankspaddaktiva', [BankController::class, 'spAddAktiva']);



// Route::post('/bankspnobukti', [BankController::class, 'getNoBukti']);
// Route::get('/banklistdepartemen', [BankController::class, 'listDepartemen']);
// Route::get('/banklistdevisi', [BankController::class, 'listDevisi']);
// Route::get('/banklistvalas', [BankController::class, 'listValas']);
// Route::post('/bankspadd', [BankController::class, 'spAdd']);
// Route::post('/bankspdetail', [BankController::class, 'getDetail']);
// Route::get('/bankloadall', [BankController::class, 'loadAll']);
// Route::get('/banklistdph', [BankController::class, 'listDPH']);
// Route::get('/banklistdphuht', [BankController::class, 'listDPHUHT']);
// Route::get('/banklistdpp', [BankController::class, 'listDPP']);
// Route::get('/banklistcustsupp', [BankController::class, 'listCustsupp']);


// Route::get('/banklistcustsuppx', [BankController::class, 'listCustSuppX']);
// Route::post('/banklisttunai', [BankController::class, 'listTunai']);
// Route::get('/banklisttunaix', [BankController::class, 'listTunaiX']);
// Route::post('/banklistcustsupptunai', [BankController::class, 'listCustSuppTunai']);


// Route::post('/banksptemphutpiut', [BankController::class, 'spTempHutPiut']);



// Route::get('/banklistcustsuppumb', [BankController::class, 'listCustsuppUMB']);





// Route::post('/bankprosesumb', [BankController::class, 'prosesUMB']);
// Route::post('/banklistumb', [BankController::class, 'listUMB']);
// Route::post('/bankspadddppdph', [BankController::class, 'spAddDPPDPH']);
// Route::post('/bankspaddtemprumjual', [BankController::class, 'spAddTempRUMJUAL']);
// Route::post('/bankspdeletetemprumjual', [BankController::class, 'spDeleteTempRUMJUAL']);
// Route::post('/bankspupdatetemprumjual', [BankController::class, 'spUpdateTempRUMJUAL']);
// Route::post('/bankspotorisasi', [BankController::class, 'spOtorisasi']);
// Route::post('/bankspbatalotorisasi', [BankController::class, 'spBatalOtorisasi']);
// Route::post('/bankdetailCetak', [BankController::class, 'getDetailCetak']);

// // CETAK PENGAJUAN DPH
// Route::post('/cetakpengajuandphspnobukti', [BankController::class, 'getNoBukti']);
// Route::get('/cetakpengajuandph' , [CetakPengajuanDphController::class, 'index']);
// Route::post('/cetakpengajuandphdetailkoreksi' , [CetakPengajuanDphController::class, 'detailKoreksi']);
// Route::post('/cetakpengajuandphdetailoutstanding' , [CetakPengajuanDphController::class, 'getDetailOutstanding']);
// Route::post('/cetakpengajuandphlistproses' , [CetakPengajuanDphController::class, 'listProses']);

// Route::post('/cetakpengajuandphspadd' , [CetakPengajuanDphController::class, 'spAdd']);
// Route::post('/cetakpengajuandphspkoreksi' , [CetakPengajuanDphController::class, 'spKoreksi']);
// Route::post('/cetakpengajuandphspproses' , [CetakPengajuanDphController::class, 'spProses']);
// Route::get('/cetakpengajuandphloadall' , [CetakPengajuanDphController::class, 'loadAll'] );
// Route::post('/cetakpengajuandphdetailCetak', [CetakPengajuanDphController::class, 'getDetailCetak']);


// // Giro Diterima
// Route::get('/giroditerima', [GiroDiterimaController::class, 'index']);
// Route::get('/giroditerimalistperkiraanheader', [GiroDiterimaController::class, 'listPerkiraanHeader']);
// Route::post('/giroditerimalistlawan', [GiroDiterimaController::class, 'listLawan']);
// Route::post('/giroditerimalistlawanbgc', [GiroDiterimaController::class, 'listLawanBGC']);
// Route::post('/giroditerimalistgiro', [GiroDiterimaController::class, 'listGiro']);
// Route::post('/giroditerimaspnobukti', [GiroDiterimaController::class, 'getNoBukti']);
// Route::post('/giroditerimacekgiroexist', [GiroDiterimaController::class, 'cekGiroExist']);
// Route::post('/giroditerimalistpencairangiro', [GiroDiterimaController::class, 'listPencairanGiro']);
// Route::post('/giroditerimalistpencairangirokoreksi', [GiroDiterimaController::class, 'listPencairanGiroKoreksi']);
// Route::post('/giroditerimalistpencairangirobgt', [GiroDiterimaController::class, 'listPencairanGiroKoreksiBGT']);

// Route::post('/giroditerimaspaddgirobgt', [GiroDiterimaController::class, 'spGiroBGT']);
// // Route::post('/giroditerimaspdeletegirobgt', [GiroDiterimaController::class, 'spDeleteGiroBGT']);




// Route::get('/giroditerimalistdepartemen', [GiroDiterimaController::class, 'listDepartemen']);
// Route::get('/giroditerimalistdevisi', [GiroDiterimaController::class, 'listDevisi']);
// Route::get('/giroditerimalistvalas', [GiroDiterimaController::class, 'listValas']);
// Route::post('/giroditerimaspadd', [GiroDiterimaController::class, 'spAdd']);
// Route::post('/giroditerimaspaddbgc', [GiroDiterimaController::class, 'spAddBGC']);
// Route::post('/giroditerimaspaddgirokoreksi', [GiroDiterimaController::class, 'spAddGiroKoreksi']);
// Route::post('/giroditerimaspdeletegirokoreksi', [GiroDiterimaController::class, 'spDeleteGiroKoreksi']);
// Route::post('/giroditerimaspdelete', [GiroDiterimaController::class, 'spDelete']);





// Route::post('/giroditerimaspdetail', [GiroDiterimaController::class, 'getDetail']);
// Route::get('/giroditerimaloadall', [GiroDiterimaController::class, 'loadAll']);
// Route::get('/giroditerimalistdph', [GiroDiterimaController::class, 'listDPH']);
// Route::get('/giroditerimalistdphuht', [GiroDiterimaController::class, 'listDPHUHT']);
// Route::get('/giroditerimalistdpp', [GiroDiterimaController::class, 'listDPP']);
// Route::get('/giroditerimalistcustsupp', [GiroDiterimaController::class, 'listCustsupp']);
// Route::get('/giroditerimalistcustsuppumb', [GiroDiterimaController::class, 'listCustsuppUMB']);
// Route::post('/giroditerimaprosesumb', [GiroDiterimaController::class, 'prosesUMB']);
// Route::post('/giroditerimalistumb', [GiroDiterimaController::class, 'listUMB']);
// Route::post('/giroditerimaspadddppdph', [GiroDiterimaController::class, 'spAddDPPDPH']);
// Route::post('/giroditerimaspaddtemprumjual', [GiroDiterimaController::class, 'spAddTempRUMJUAL']);
// Route::post('/giroditerimaspdeletetemprumjual', [GiroDiterimaController::class, 'spDeleteTempRUMJUAL']);
// Route::post('/giroditerimaspupdatetemprumjual', [GiroDiterimaController::class, 'spUpdateTempRUMJUAL']);
// Route::post('/giroditerimaspotorisasi', [GiroDiterimaController::class, 'spOtorisasi']);
// Route::post('/giroditerimaspbatalotorisasi', [GiroDiterimaController::class, 'spBatalOtorisasi']);
// Route::post('/giroditerimadetailCetak', [GiroDiterimaController::class, 'getDetailCetak']);




// // Giro Dibuka
// Route::get('/girodibuka', [GiroDibukaController::class, 'index']);
// Route::get('/girodibukalistperkiraanheader', [GiroDibukaController::class, 'listPerkiraanHeader']);
// Route::post('/girodibukalistlawan', [GiroDibukaController::class, 'listLawan']);
// Route::post('/girodibukalistlawanbgc', [GiroDibukaController::class, 'listLawanBGC']);
// Route::post('/girodibukalistgiro', [GiroDibukaController::class, 'listGiro']);
// Route::post('/girodibukaspnobukti', [GiroDibukaController::class, 'getNoBukti']);
// Route::post('/girodibukacekgiroexist', [GiroDibukaController::class, 'cekGiroExist']);
// Route::post('/girodibukalistpencairangiro', [GiroDibukaController::class, 'listPencairanGiro']);
// Route::post('/girodibukalistpencairangirokoreksi', [GiroDibukaController::class, 'listPencairanGiroKoreksi']);
// Route::post('/girodibukalistpencairangirobgt', [GiroDibukaController::class, 'listPencairanGiroKoreksiBGT']);

// Route::post('/girodibukaspaddgirobgt', [GiroDibukaController::class, 'spGiroBGT']);
// // Route::post('/girodibukaspdeletegirobgt', [GiroDibukaController::class, 'spDeleteGiroBGT']);




// Route::get('/girodibukalistdepartemen', [GiroDibukaController::class, 'listDepartemen']);
// Route::get('/girodibukalistdevisi', [GiroDibukaController::class, 'listDevisi']);
// Route::get('/girodibukalistvalas', [GiroDibukaController::class, 'listValas']);
// Route::post('/girodibukaspadd', [GiroDibukaController::class, 'spAdd']);
// Route::post('/girodibukaspaddbgc', [GiroDibukaController::class, 'spAddBGC']);
// Route::post('/girodibukaspaddgirokoreksi', [GiroDibukaController::class, 'spAddGiroKoreksi']);
// Route::post('/girodibukaspdeletegirokoreksi', [GiroDibukaController::class, 'spDeleteGiroKoreksi']);
// Route::post('/girodibukaspdelete', [GiroDibukaController::class, 'spDelete']);





// Route::post('/girodibukaspdetail', [GiroDibukaController::class, 'getDetail']);
// Route::get('/girodibukaloadall', [GiroDibukaController::class, 'loadAll']);
// Route::get('/girodibukalistdph', [GiroDibukaController::class, 'listDPH']);
// Route::get('/girodibukalistdphbbg', [GiroDibukaController::class, 'listDPHBBG']);

// Route::get('/girodibukalistdphuht', [GiroDibukaController::class, 'listDPHUHT']);
// Route::get('/girodibukalistdpp', [GiroDibukaController::class, 'listDPP']);
// Route::get('/girodibukalistcustsupp', [GiroDibukaController::class, 'listCustsupp']);
// Route::get('/girodibukalistcustsuppumb', [GiroDibukaController::class, 'listCustsuppUMB']);
// Route::post('/girodibukaprosesumb', [GiroDibukaController::class, 'prosesUMB']);
// Route::post('/girodibukalistumb', [GiroDibukaController::class, 'listUMB']);
// Route::post('/girodibukaspadddppdph', [GiroDibukaController::class, 'spAddDPPDPH']);
// Route::post('/girodibukaspaddtemprumjual', [GiroDibukaController::class, 'spAddTempRUMJUAL']);
// Route::post('/girodibukaspdeletetemprumjual', [GiroDibukaController::class, 'spDeleteTempRUMJUAL']);
// Route::post('/girodibukaspupdatetemprumjual', [GiroDibukaController::class, 'spUpdateTempRUMJUAL']);
// Route::post('/girodibukaspotorisasi', [GiroDibukaController::class, 'spOtorisasi']);
// Route::post('/girodibukaspbatalotorisasi', [GiroDibukaController::class, 'spBatalOtorisasi']);
// Route::post('/girodibukadetailCetak', [GiroDibukaController::class, 'getDetailCetak']);

 
// // PENGAJUAN DPH
// Route::get('/pengajuandph', [PengajuanDPHController::class, 'index']);
// Route::post('/pengajuandphspdetail', [PengajuanDPHController::class, 'getDetail']);
// Route::post('/pengajuandphspdetailkledit', [PengajuanDPHController::class, 'getDetailKLEdit']);

// Route::post('/pengajuandphsplistpengajuan', [PengajuanDPHController::class, 'getListPengajuan']);
// Route::post('/pengajuandphspaddkledit' , [PengajuanDPHController::class, 'spAddKLEdit']);
// Route::post('/pengajuandphspdeletekledit' , [PengajuanDPHController::class, 'spDeleteKLEdit']);
// Route::post('/pengajuandphspupdatedphdet' , [PengajuanDPHController::class, 'spUpdateDPHDet']);

// Route::post('/pengajuandphspadd' , [PengajuanDPHController::class, 'spAdd']);
// Route::post('/pengajuandphspkoreksi' , [PengajuanDPHController::class, 'spKoreksi']);
// Route::get('/pengajuandphloadall' , [PengajuanDPHController::class, 'loadAll']);
// Route::post('/pengajuandphspotorisasi' , [PengajuanDPHController::class, 'spOtorisasi']);
// Route::post('/pengajuandphspbatalotorisasi' , [PengajuanDPHController::class, 'spBatalOtorisasi']);
// Route::post('/pengajuandphdetailCetak', [PengajuanDPHController::class, 'getDetailCetak']);


// PENGAJUAN DPH
Route::get('/pengajuandphtunai', [PengajuanDPHTunaiController::class, 'index']);
Route::post('/pengajuandphtunaispdetail', [PengajuanDPHTunaiController::class, 'getDetail']);
// Route::post('/pengajuandphtunaispdetailkl', [PengajuanDPHTunaiController::class, 'getDetailKL']);
Route::post('/pengajuandphtunaispdetailkledit', [PengajuanDPHTunaiController::class, 'getDetailKLEdit']);
Route::post('/pengajuandphtunaisplistpengajuan', [PengajuanDPHTunaiController::class, 'getListPengajuan']);
Route::post('/pengajuandphtunaispaddkledit' , [PengajuanDPHTunaiController::class, 'spAddKLEdit']);
Route::post('/pengajuandphtunaispdeletekledit' , [PengajuanDPHTunaiController::class, 'spDeleteKLEdit']);
Route::post('/pengajuandphtunaispupdatedphdet' , [PengajuanDPHTunaiController::class, 'spUpdateDPHDet']);

Route::post('/pengajuandphtunaispadd' , [PengajuanDPHTunaiController::class, 'spAdd']);
Route::post('/pengajuandphtunaispkoreksi' , [PengajuanDPHTunaiController::class, 'spKoreksi']);
Route::get('/pengajuandphtunailoadall' , [PengajuanDPHTunaiController::class, 'loadAll']);
Route::post('/pengajuandphtunaispotorisasi' , [PengajuanDPHTunaiController::class, 'spOtorisasi']);
Route::post('/pengajuandphtunaispbatalotorisasi' , [PengajuanDPHTunaiController::class, 'spBatalOtorisasi']);
Route::post('/pengajuandphtunaidetailCetak', [PengajuanDPHTunaiController::class, 'getDetailCetak']);


// // PENGAJUAN DPP
// Route::get('/pengajuandpp', [PengajuanDPPController::class, 'index']);
// Route::post('/pengajuandppspdetail', [PengajuanDPPController::class, 'getDetail']);
// Route::post('/pengajuandppsplistpengajuan', [PengajuanDPPController::class, 'getListPengajuan']);
// Route::post('/pengajuandppspadd' , [PengajuanDPPController::class, 'spAdd']);
// Route::post('/pengajuandppspkoreksi' , [PengajuanDPPController::class, 'spKoreksi']);
// Route::get('/pengajuandpploadall' , [PengajuanDPPController::class, 'loadAll']);
// Route::post('/pengajuandppspotorisasi' , [PengajuanDPPController::class, 'spOtorisasi']);
// Route::post('/pengajuandppspbatalotorisasi' , [PengajuanDPPController::class, 'spBatalOtorisasi']);
// Route::post('/pengajuandppdetailCetak', [PengajuanDPPController::class, 'getDetailCetak']);



// // PENERIMAAN DPP
// Route::get('/penerimaandpp' , [PenerimaanDPPController::class, 'index']);
// Route::get('/penerimaandpplistperkiraanadd' , [PenerimaanDPPController::class, 'listPerkiraanAdd']);
// Route::get('/penerimaandpplistperkiraanlbkl' , [PenerimaanDPPController::class, 'listPerkiraanLBKL']);
// Route::post('/penerimaandppdetailkoreksi' , [PenerimaanDPPController::class, 'detailKoreksi']);
// Route::post('/penerimaandppdetailoutstanding' , [PenerimaanDPPController::class, 'detailOutstanding']);
// Route::post('/penerimaandpplistproses' , [PenerimaanDPPController::class, 'listProses']);
// Route::post('/penerimaandppcheckgiro' , [PenerimaanDPPController::class, 'checkGiro']);
// Route::post('/penerimaandppspgiro' , [PenerimaanDPPController::class, 'spGiro']);
// Route::post('/penerimaandppspadd' , [PenerimaanDPPController::class, 'spAdd']);
// Route::post('/penerimaandppspkoreksi' , [PenerimaanDPPController::class, 'spKoreksi']);
// Route::post('/penerimaandppspproses' , [PenerimaanDPPController::class, 'spProses']);
// Route::get('/penerimaandpploadall' , [PenerimaanDPPController::class, 'loadAll'] );
// Route::post('/penerimaandppdetailCetak', [PenerimaanDPPController::class, 'getDetailCetak']);


// // PELUNASAN PIUTANG DPP
// Route::get('/pelunasanpiutangdpp', [PelunasanPiutangDPPController::class, 'index']);
// Route::post('/pelunasanpiutangdppspdetail', [PelunasanPiutangDPPController::class, 'getDetail']);
// Route::post('/pelunasanpiutangdppspdetailoutstanding', [PelunasanPiutangDPPController::class, 'getDetailOutstanding']);
// Route::post('/pelunasanpiutangdppgetlistterimadpp', [PelunasanPiutangDPPController::class, 'getListTerimaDPP']);
// Route::post('/pelunasanpiutangdppspdetailpenerimaan' , [PelunasanPiutangDPPController::class, 'getdetailPenerimaan'] );
// Route::post('/pelunasanpiutangdppspadd' , [PelunasanPiutangDPPController::class, 'spAdd'] );
// Route::get('/pelunasanpiutangdpploadall' , [PelunasanPiutangDPPController::class, 'loadAll'] );
// Route::post('/pelunasanpiutangdppspkoreksi' , [PelunasanPiutangDPPController::class, 'spKoreksi'] );
// Route::post('/pelunasanpiutangdppspotorisasi' , [PelunasanPiutangDPPController::class, 'spOtorisasi'] );
// Route::post('/pelunasanpiutangdppspbatalotorisasi' , [PelunasanPiutangDPPController::class, 'spBatalOtorisasi'] );
// Route::post('/pelunasanpiutangdppdetailCetak', [PelunasanPiutangDPPController::class, 'getDetailCetak']);


// // MEMORIAL KOREKSI
// Route::get('/memorialkoreksi', [MemorialKoreksiController::class, 'index']);
// Route::post('/memorialkoreksispdetail', [MemorialKoreksiController::class, 'getDetail']);
// Route::post('/memorialkoreksilistperkiraan', [MemorialKoreksiController::class, 'listPerkiraan']);
// Route::get('/memorialkoreksilistvalas', [MemorialKoreksiController::class, 'listValas']);
// Route::post('/memorialkoreksispadd', [MemorialKoreksiController::class, 'spAdd']);
// Route::get('/memorialkoreksiloadall', [MemorialKoreksiController::class, 'loadAll']);
// Route::post('/memorialkoreksispotorisasi', [MemorialKoreksiController::class, 'spOtorisasi']);
// Route::post('/memorialkoreksispbatalotorisasi', [MemorialKoreksiController::class, 'spBatalOtorisasi']);
// Route::post('/memorialkoreksidetailCetak', [MemorialKoreksiController::class, 'getDetailCetak']);


// // BON SEMENTARA
// Route::get('/bonsementara' , [BonSementaraController::class, 'index']);
// Route::post('/bonsementaraloadall' , [BonSementaraController::class, 'loadAll']);
// Route::post('/bonsementaraspnobukti' , [BonSementaraController::class, 'getNoBukti']);
// Route::post('/bonsementaraspdetail' , [BonSementaraController::class, 'getDetailOutstanding']);
// Route::post('/bonsementaraspadd' , [BonSementaraController::class, 'spAdd']);


});