<?php

namespace App\Otorisasi;

/**
 * Tambahan OPSIONAL untuk handler yang otorisasinya bertingkat (DBPO: IsOtorisasi1/2 + plafon jabatan).
 * Handler biasa (Kas, Bank, PR) cukup OtorisasiHandler: satu level, IsOtorisasi1.
 *
 * - Permintaan dikirim HANYA ke user yang punya hak level approverLevel($nobukti) - level ditentukan handler per
 *   dokumen (mis. PO sampai 200 jt ke level 1, di atasnya ke level 2). Level itu disimpan di
 *   DBREQOTORISASI.LEVELOTO dan menentukan siapa yang menerima push dan melihat permintaan di antrean.
 * - Aturan level/plafon (siapa boleh mengisi level berapa) tetap milik handler: approveWithRules().
 */
interface LeveledOtorisasiHandler
{
    /** Level otorisasi (1..5) yang menerima dan memproses permintaan untuk dokumen ini. */
    public function approverLevel(string $nobukti): int;

    /**
     * Terapkan aturan level/plafon untuk $username, sama dengan otorisasi dari halaman transaksinya.
     *
     * result: 'done' (otorisasi lengkap), 'partial' (ada level terisi tapi belum lengkap - permintaan tetap
     * pending), 'refused' (tidak ada yang diubah). 'code' (opsional, 2/3) adalah kode lama yang dibaca JS halaman
     * untuk 'Melebihi plafon' / 'Diperlukan otorisasi 1'.
     *
     * @return array{result: string, message: string, code?: int}
     */
    public function approveWithRules(string $nobukti, string $username): array;

    /**
     * Sudah ada level yang terisi (level mana pun)? Dipakai untuk permintaan batal: dokumen yang baru sebagian
     * diotorisasi (isApproved() masih false) tetap boleh dibatalkan otorisasinya.
     */
    public function hasApproval(string $nobukti): bool;
}
