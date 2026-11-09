<?php

namespace App\Otorisasi;

/**
 * Handler Perintah Retur Jual (-) (href 'perintahreturjualminus', dbPRRJual dengan pMIN = 1). Seluruh logika ada di
 * PerintahReturJualOtorisasi; kelas ini hanya membalik filter pMIN supaya kedua halaman tidak saling mengotorisasi
 * nomor dokumen lainnya. Ringkasan HP memakai pembacaan yang sama (PRJ (-) tidak punya invoice, baris 'Invoice' dan
 * 'No. SO' otomatis tidak tampil karena berisi '-').
 */
class PerintahReturJualMinusOtorisasi extends PerintahReturJualOtorisasi
{
    protected int $pMin = 1;
}
