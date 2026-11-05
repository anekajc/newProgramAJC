<?php

namespace App\Otorisasi;

/**
 * Handler PO Non Stock (href 'pononstock'): DBPO dengan pJasa = 1. Aturan level/plafon, penerima permintaan
 * (berdasarkan nilai PO) dan batal otorisasinya sama dengan PO Stock - lihat PurchaseOrderOtorisasi.
 */
class PurchaseOrderNonStockOtorisasi extends PurchaseOrderOtorisasi
{
    protected int $pJasa = 1;
}
