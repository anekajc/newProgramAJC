<?php

use App\Otorisasi\BankOtorisasi;
use App\Otorisasi\DebetNoteOtorisasi;
use App\Otorisasi\InvoiceReturBeliOtorisasi;
use App\Otorisasi\KasOtorisasi;
use App\Otorisasi\PembelianAccOtorisasi;
use App\Otorisasi\PembelianPermintaanAgenOtorisasi;
use App\Otorisasi\PembelianPermintaanNonAgenOtorisasi;
use App\Otorisasi\PembelianPermintaanNonStockOtorisasi;
use App\Otorisasi\PenawaranSOOtorisasi;
use App\Otorisasi\PerformanceOtorisasi;
use App\Otorisasi\SOOtorisasi;
use App\Otorisasi\InvoicePenjualanOtorisasi;
use App\Otorisasi\PerintahReturJualOtorisasi;
use App\Otorisasi\ReturSuratJalanOtorisasi;
use App\Otorisasi\SuratJalanOtorisasi;
use App\Otorisasi\UangMukaJualOtorisasi;
use App\Otorisasi\PerintahReturBeliOtorisasi;
use App\Otorisasi\PurchaseOrderNonStockOtorisasi;
use App\Otorisasi\PurchaseOrderOtorisasi;

/*
|--------------------------------------------------------------------------
| Modul yang mendukung "Minta Otorisasi" (push ke approver)
|--------------------------------------------------------------------------
|
| Kunci = DBMENUWEB.href halaman transaksinya (sama dengan $req->path() yang dipakai
| GlobalController::getAkses()). 'handler' mengimplementasikan
| App\Otorisasi\OtorisasiHandler. Tambah modul baru cukup dengan satu baris di sini
| plus satu kelas handler. KODEMENU tidak ditulis di sini - di-resolve dari
| DBMENUWEB lewat href (Kas = '0202'; $kodemenu '02011' di KasController::index()
| sudah usang dan memang tidak dipakai getAkses()).
|
*/

return [

    'kas' => [
        'label' => 'Kas',
        'handler' => KasOtorisasi::class,
    ],

    'bank' => [
        'label' => 'Bank',
        'handler' => BankOtorisasi::class,
    ],

    'pembelianpermintaannonagen' => [
        'label' => 'PR Stock (Non Keagenan)',
        'handler' => PembelianPermintaanNonAgenOtorisasi::class,
    ],

    'pembelianpermintaanagen' => [
        'label' => 'PR (Keagenan)',
        'handler' => PembelianPermintaanAgenOtorisasi::class,
    ],

    'pembelianpermintaannonstock' => [
        'label' => 'PR Non Stock',
        'handler' => PembelianPermintaanNonStockOtorisasi::class,
    ],

    'purchaseorder' => [
        'label' => 'PO Stock',
        'handler' => PurchaseOrderOtorisasi::class,
    ],

    'pononstock' => [
        'label' => 'PO Non Stock',
        'handler' => PurchaseOrderNonStockOtorisasi::class,
    ],

    'newpobeliacc' => [
        'label' => 'Pembelian ACC',
        'handler' => PembelianAccOtorisasi::class,
    ],

    'perintahreturbeli' => [
        'label' => 'Perintah Retur Beli',
        'handler' => PerintahReturBeliOtorisasi::class,
    ],

    'invoicereturbeli' => [
        'label' => 'Invoice Retur Beli',
        'handler' => InvoiceReturBeliOtorisasi::class,
    ],

    'pembelianpermintaandebetnote' => [
        'label' => 'Debet Note',
        'handler' => DebetNoteOtorisasi::class,
    ],

    'penawaranso' => [
        'label' => 'Penawaran SO',
        'handler' => PenawaranSOOtorisasi::class,
    ],

    'so' => [
        'label' => 'SO',
        'handler' => SOOtorisasi::class,
    ],

    'performance' => [
        'label' => 'Performa',
        'handler' => PerformanceOtorisasi::class,
    ],

    'uangmukajual' => [
        'label' => 'Uang Muka Jual',
        'handler' => UangMukaJualOtorisasi::class,
    ],

    'suratjalan' => [
        'label' => 'Surat Jalan',
        'handler' => SuratJalanOtorisasi::class,
    ],

    'retursuratjalan' => [
        'label' => 'Retur Surat Jalan',
        'handler' => ReturSuratJalanOtorisasi::class,
    ],

    'invoicepenjualan' => [
        'label' => 'Invoice Penjualan',
        'handler' => InvoicePenjualanOtorisasi::class,
    ],

    'perintahreturjual' => [
        'label' => 'Perintah Retur Jual',
        'handler' => PerintahReturJualOtorisasi::class,
    ],

];
