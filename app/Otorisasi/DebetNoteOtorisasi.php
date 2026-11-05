<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Permintaan Debet Note (href 'pembelianpermintaandebetnote', DBDEBETNOTE). Satu level (IsOtorisasi1).
 * Otorisasi memposting hutang dan jurnal (sp_ProsesPostingHutPiut + sp_ProsesPostingJurnalOto, TipeProses 1) di periode
 * kerja user yang login; batal otorisasi menghapus posting itu (TipeProses 0). UPDATE-nya dipindahkan apa adanya dari
 * PembelianPermintaanDebetNoteController (updateOtorisasi / updateBatalOtorisasi, yang kini memanggil handler ini) -
 * termasuk bahwa otorisasi dari halaman tidak menulis dbLogFile (hanya batal yang menulis 'btloto').
 */
class DebetNoteOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NOBUKTI, A.TANGGAL, A.KodeSupp, C.NAMACUSTSUPP
            from DBDEBETNOTE A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KodeSupp
            where A.NOBUKTI = cast(:nobukti as varchar(50))
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        $lines = DB::connection('SML')->select('
            select URUT, NoInv, Keterangan, Nilai, KodeVLS, Kurs, NilaiRp
            from DBDEBETNOTEDET
            where NOBUKTI = cast(:nobukti as varchar(50))
            order by URUT
        ', ['nobukti' => $nobukti]);

        $total = 0;
        $rows = [];
        foreach ($lines as $l) {
            $total += (float) $l->NilaiRp;
            $asing = $l->KodeVLS && strtoupper(trim($l->KodeVLS)) !== 'IDR';
            $info = $asing
                ? $l->KodeVLS.' '.number_format((float) $l->Nilai, 2, ',', '.').' x kurs '.number_format((float) $l->Kurs, 2, ',', '.')
                : '';
            $ket = trim((string) $l->Keterangan);
            if ($ket !== '' && $ket !== '-') {
                $info = trim($info.($info !== '' ? ' - ' : '').$ket);
            }
            $rows[] = [
                'Uraian' => 'Invoice '.$l->NoInv,
                'Jumlah' => 'Rp '.number_format((float) $l->NilaiRp, 2, ',', '.'),
                'Info' => $info,
            ];
        }

        return [
            'header' => [
                'No. Bukti' => $h->NOBUKTI,
                'Tanggal' => $h->TANGGAL ? date('d-m-Y', strtotime($h->TANGGAL)) : '',
                'Supplier' => trim($h->KodeSupp.' '.$h->NAMACUSTSUPP),
            ],
            'lines' => $rows,
            'total' => $total,
        ];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from DBDEBETNOTE where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // Sama persis dengan PembelianPermintaanDebetNoteController::updateOtorisasi (yang kini memanggil ini): tidak
        // menyentuh MaxOL; syarat belum diotorisasi di WHERE supaya klik ganda / dua approver tidak memposting dobel.
        $res = DB::connection('SML')->update('UPDATE dbdebetnote SET IsOtorisasi1 = 1, OtoUser1 = :username, TglOto1 = :tanggal WHERE NoBukti = cast(:nobukti as varchar(50)) AND isnull(IsOtorisasi1, 0) = 0', [
            'username' => $username,
            'tanggal' => date('Y-m-d H:i:s'),
            'nobukti' => $nobukti,
        ]);

        // vwPostHutPiut hanya mengambil DN yang IsOtorisasi1 = 1, jadi posting dijalankan sesudah flag-nya terisi.
        if ($res > 0) {
            $this->posting($nobukti, 1);
        }

        return $res;
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        // Sudah dipakai di pelunasan (DPH): batal otorisasi menghapus posting hutang & jurnal padahal pelunasan itu
        // masih menunjuk debet note ini.
        foreach (['NoInvoice', 'NOFAKTUR'] as $kolom) {
            $dph = DB::connection('SML')->selectOne("select top 1 NoBukti from DBDPHDET where $kolom = cast(? as varchar(50))", [$nobukti]);
            if ($dph) {
                return 'Debet note '.$nobukti.' sudah dipakai di pelunasan '.$dph->NoBukti.', otorisasi tidak bisa dibatalkan.';
            }
        }

        return null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        // Sama dengan PembelianPermintaanDebetNoteController::updateBatalOtorisasi, ditambah penjaga IsOtorisasi1 = 1
        // supaya posting tidak dibalik dua kali.
        $res = DB::connection('SML')->update("UPDATE dbdebetnote SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = cast(:nobukti as varchar(50)) AND isnull(IsOtorisasi1, 0) = 1", ['nobukti' => $nobukti]);

        if ($res > 0) {
            $this->posting($nobukti, 0);
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'DN', $nobukti, $alasan, 0, 'dbdebetnote');
        }

        return $res;
    }

    /** TipeProses 1 = posting DBHUTPIUT (NOINVOICE 'DN') + DBJurnalOto, 0 = hapus posting; di periode kerja user yang login. */
    private function posting(string $nobukti, int $tipeProses): void
    {
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $values = ['', 'DBDEBETNOTE', $periode->bulan, $periode->tahun, $nobukti, $tipeProses];
        DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
        DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);
    }
}
