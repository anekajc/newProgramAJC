<?php

namespace App\Otorisasi;

/**
 * Tambahan OPSIONAL untuk BatalOtorisasiHandler yang akibat batalnya bergantung pada siapa yang menyetujui
 * (PO: dalam plafon approver, PO ikut dibatalkan; di atas plafon, hanya otorisasinya). Teksnya ditampilkan di halaman
 * approve HP sebelum approver menekan Setujui.
 */
interface BatalPreviewHandler
{
    /** Penjelasan singkat apa yang terjadi kalau $username menyetujui batal otorisasi dokumen ini, atau null. */
    public function cancelPreview(string $nobukti, string $username): ?string;
}
