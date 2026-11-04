<?php

namespace App\Otorisasi;

/**
 * Tambahan OPSIONAL untuk handler yang mendukung "Minta Batal Otorisasi" (DBREQOTORISASI.TIPE = 'B').
 * Handler tanpa interface ini (Kas, Bank) tetap hanya mendukung permintaan otorisasi biasa.
 * Hak yang dicek untuk permintaan batal adalah DBFLMENUWEB.IsBatal (bukan IsOtorisasi1).
 */
interface BatalOtorisasiHandler
{
    /** Alasan dokumen ini TIDAK boleh dibatalkan otorisasinya (mis. sudah dipakai PO), atau null kalau boleh. */
    public function cancelBlocker(string $nobukti): ?string;

    /**
     * Batalkan otorisasi dokumen - UPDATE yang sama dengan batal otorisasi di halaman transaksinya.
     * Mengembalikan jumlah baris yang ter-update.
     */
    public function cancelApproval(string $nobukti, string $username, string $alasan): int;
}
