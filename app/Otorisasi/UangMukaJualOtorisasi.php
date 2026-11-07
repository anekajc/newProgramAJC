<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Uang Muka Jual (href 'uangmukajual', DBUMJUAL dengan pBeli kosong/0 - tabel yang sama dipakai Uang Muka Beli, pBeli = 1).
 * Satu level (dbmenu.OL = 1, IsOtorisasi1). Otorisasi menjalankan posting (sp_ProsesPostingHutPiut + sp_ProsesPostingJurnalOto) di
 * periode kerja user yang login (approver), sama dengan menekan Otorisasi di halamannya; yang tercatat untuk UMJ adalah baris jurnal
 * di DBJurnalOto (Jenis 'UMJ', NoBuktiTrans = nomor UMJ), batal otorisasi menghapusnya lagi. Dipanggil di dalam transaksi (service HP
 * dan UangMukaJualController), jadi kalau posting gagal flag-nya ikut batal.
 *
 * Mirror halaman: otorisasi tidak menyentuh MaxOL dan tidak mengecek periode terkunci; batal tidak mengubah MaxOL; log 'oto' / 'btloto'
 * sumber 'UMJ' ditulis di dalam transaksi (kalau gagal ikut batal, seperti di controller lama).
 */
class UangMukaJualOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    /** Hanya uang muka jual: pBeli kosong atau 0. */
    private const JUAL = 'isnull(pBeli, 0) = 0';

    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NOBUKTI, A.TANGGAL, A.NOSO, B.KodeCust, C.NAMACUSTSUPP, A.PERSEN, A.VALAS, A.KURS, A.DPP, A.PPN, A.SUBTOTAL,
                   isnull(I.TotDPP, 0) TotDPPSO, isnull(A.IsBatal, 0) IsBatal
            from DBUMJUAL A
            left outer join DBSO B on B.NOBUKTI = A.NOSO
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = B.KodeCust
            left outer join vwRpDetSO I on I.NoBukti = A.NOSO
            where A.NOBUKTI = cast(:nobukti as varchar(50)) and isnull(A.pBeli, 0) = 0
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        // Uang muka = satu dokumen tanpa baris barang: DPP dan PPN ditampilkan sebagai dua baris, dalam mata uang dokumen.
        $valas = strtoupper(trim((string) $h->VALAS));
        $asing = $valas !== '' && $valas !== 'IDR';
        $fmt = fn ($n) => ($asing ? $valas : 'Rp').' '.number_format((float) $n, 2, ',', '.');
        $rows = [
            ['Uraian' => 'DPP uang muka ('.number_format((float) $h->PERSEN, 2, ',', '.').'% dari DPP SO)', 'Jumlah' => $fmt($h->DPP), 'Info' => 'DPP SO '.$fmt($h->TotDPPSO)],
            ['Uraian' => 'PPN', 'Jumlah' => $fmt($h->PPN)],
        ];
        $kurs = $asing ? (float) $h->KURS : 1.0;

        $info = [
            'No. Bukti' => $h->NOBUKTI,
            'Tanggal' => $h->TANGGAL ? date('d-m-Y', strtotime($h->TANGGAL)) : '',
            'No. SO' => $h->NOSO,
            'Customer' => trim($h->KodeCust.' '.$h->NAMACUSTSUPP),
        ];
        if ($asing) {
            $info['Mata uang'] = $valas.' (kurs '.number_format((float) $h->KURS, 2, ',', '.').')';
        }
        if ((int) $h->IsBatal === 1) {
            $info['Status'] = 'Dibatalkan';
        }

        // Total dalam rupiah (kolom total di halaman approve selalu "Rp").
        return ['header' => $info, 'lines' => $rows, 'total' => (float) $h->SUBTOTAL * $kurs];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from DBUMJUAL where NOBUKTI = cast(:nobukti as varchar(50)) and '.self::JUAL, ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // Hanya yang belum diotorisasi yang diproses - otorisasi ulang akan memposting piutang & jurnal lagi.
        $res = DB::connection('SML')->update('update DBUMJUAL set TGLOTO1 = GetDate(), ISOTORISASI1 = 1, OTOUSER1 = :username where nobukti = cast(:nobukti as varchar(50)) and isnull(ISOTORISASI1, 0) = 0 and '.self::JUAL, [
            'username' => $username,
            'nobukti' => $nobukti,
        ]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('oto', 'UMJ', $nobukti, '', 0, 'DBUMJUAL');
            $this->posting($nobukti, 1);
        }

        return $res;
    }

    /**
     * Faktur penjualan (belum batal) yang sudah memotong uang muka dari UMJ ini, atau null. Di SML pemotongan tercatat di DBINVOICEPL
     * (NoSPP = SO dari UMJ, NUangMuka <> 0, seperti view REPORTOUTUMJual) dan DBJUAL.NobuktiUM. Dipakai juga UangMukaJualController
     * (koreksi / hapus).
     */
    public static function invoicePemakai(string $nobukti): ?string
    {
        $pakai = DB::connection('SML')->select('
            select top 1 X.NOBUKTI from (
              select A.NOBUKTI
              from DBINVOICEPL A
              join DBUMJUAL B on A.NoSPP = B.NOSO and isnull(B.pBeli, 0) = 0
              where B.NOBUKTI = ? and isnull(A.NUangMuka, 0) <> 0 and isnull(A.IsBatal, 0) = 0
              union all
              select J.NOBUKTI from DBJUAL J
              where J.NobuktiUM = ? and isnull(J.ISBATAL, 0) = 0
            ) X', [$nobukti, $nobukti]);

        return $pakai ? $pakai[0]->NOBUKTI : null;
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        $invoice = self::invoicePemakai($nobukti);

        return $invoice ? 'UMJ '.$nobukti.' sudah dipakai di faktur '.$invoice.', batalkan/koreksi faktur dulu' : null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        // Hanya yang sedang terotorisasi yang boleh dibatalkan (klik ganda / dua user bersamaan).
        $res = DB::connection('SML')->update("update DBUMJUAL set TGLOTO1 = NULL, ISOTORISASI1 = 0, OTOUSER1 = '' where nobukti = cast(:nobukti as varchar(50)) and isnull(ISOTORISASI1, 0) = 1 and ".self::JUAL, ['nobukti' => $nobukti]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'UMJ', $nobukti, $alasan, 0, 'DBUMJUAL');
            $this->posting($nobukti, 0);
        }

        return $res;
    }

    /** Posting piutang dan jurnal otorisasi ($oto 1) atau pembalikannya ($oto 0), di periode kerja user yang login. */
    private function posting(string $nobukti, int $oto): void
    {
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $values = ['', 'DBUMJUAL', $periode->bulan, $periode->tahun, $nobukti, $oto];
        DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
        DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);
    }
}
