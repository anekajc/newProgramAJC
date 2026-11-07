<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Kredit Note (href 'kreditnote', DBKREDITNOTE / DBKREDITNOTEDET - dokumen KN). Satu level (dbmenu.OL = 1, IsOtorisasi1).
 * Otorisasi memposting piutang dan jurnal (sp_ProsesPostingHutPiut + sp_ProsesPostingJurnalOto, TipeProses 1) di periode kerja user
 * yang login (approver), sama dengan menekan Otorisasi di halamannya; batal otorisasi membalik posting itu (TipeProses 0). UPDATE + log
 * dipindahkan dari KreditNoteController (spOtorisasi / spBatalOtorisasi, yang kini memanggil handler ini). Dipanggil di dalam
 * transaksi (service HP dan KreditNoteController), jadi kalau posting gagal flag-nya ikut batal.
 *
 * Seperti di halamannya: otorisasi tidak menulis dbLogFile (hanya batal yang menulis 'btloto'), dan tidak ada pemeriksaan pemakaian
 * (cancelBlocker kosong) - kredit note bisa saja sudah dipakai di pelunasan piutang; di sinilah tempat blokirnya kalau nanti diinginkan.
 * Trigger Trig_UPD_DBKREDITNOTE ikut jalan di UPDATE ini persis seperti di desktop.
 */
class KreditNoteOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NOBUKTI, A.TANGGAL, A.KodeSupp, C.NAMACUSTSUPP
            from DBKREDITNOTE A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KodeSupp
            where A.NOBUKTI = cast(:nobukti as varchar(100))
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        $lines = DB::connection('SML')->select('
            select URUT, NoInv, Keterangan, Nilai, KodeVLS, Kurs, NilaiRp
            from DBKREDITNOTEDET
            where NOBUKTI = cast(:nobukti as varchar(100))
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
                'Customer' => trim($h->KodeSupp.' '.$h->NAMACUSTSUPP),
            ],
            'lines' => $rows,
            'total' => $total,
        ];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from DBKREDITNOTE where NOBUKTI = cast(:nobukti as varchar(100))', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // Syarat "belum otorisasi" ikut di WHERE: klik ganda / dua user bersamaan tidak boleh memposting piutang & jurnal dua kali.
        // TglOto1 memakai jam SQL Server (getdate), sama seperti tanggal KN.
        $res = DB::connection('SML')->update('update dbkreditnote set isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = getdate() where nobukti = cast(:nobukti as varchar(100)) and isnull(isOtorisasi1, 0) <> 1', [
            'username' => $username,
            'nobukti' => $nobukti,
        ]);

        // vwPostHutPiut hanya mengambil KN yang IsOtorisasi1 = 1, jadi posting dijalankan sesudah flag-nya terisi.
        if ($res > 0) {
            $this->posting($nobukti, 1);
        }

        return $res;
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        return null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        $res = DB::connection('SML')->update("update dbkreditnote set isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL where nobukti = cast(:nobukti as varchar(100)) and isOtorisasi1 = 1", [
            'nobukti' => $nobukti,
        ]);

        if ($res > 0) {
            $this->posting($nobukti, 0);
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'KN', $nobukti, $alasan, 0, 'DBKREDITNOTE');
        }

        return $res;
    }

    /** Posting piutang dan jurnal otorisasi ($tipeProses 1) atau pembalikannya (0), di periode kerja user yang login. */
    private function posting(string $nobukti, int $tipeProses): void
    {
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $values = ['', 'DBKREDITNOTE', $periode->bulan, $periode->tahun, $nobukti, $tipeProses];
        DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
        DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);
    }
}
