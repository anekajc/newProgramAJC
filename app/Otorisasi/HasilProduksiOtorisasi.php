<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Hasil Produksi (href 'produksihasilproduksi', DBHASILPRD). Satu level (IsOtorisasi1).
 *
 * Otorisasi hanya mengisi flag (IsOtorisasi1, OtoUser1, TglOto1, MaxOL = 1) - sama dengan data desktop di
 * DBMas (sudah otorisasi -> MaxOL 1, belum -> -1). Tidak ada posting jurnal di sini: jurnal 'HPD' di desktop
 * (vwPostJurnalOto) hanya terbentuk dari HPP (isnull(hpp,0) <> 0) yang diisi proses costing, bukan saat input.
 * Batal otorisasi mengembalikan flag dan mencatat alasan ke dbLogFile ('btloto'), pola DebetNoteOtorisasi.
 */
class HasilProduksiOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $h = DB::connection('SML')->selectOne('
            select top 1 A.NOBUKTI, A.TANGGAL, A.NoSPK, A.Shift, A.KodeMSN, M.Ket as NamaMesin
            from DBHASILPRD A
            left outer join DBMesin M on M.KodeMsn = A.KodeMSN
            where A.NOBUKTI = cast(:nobukti as varchar(50))
        ', ['nobukti' => $nobukti]);

        if (! $h) {
            return null;
        }

        $lines = DB::connection('SML')->select('
            select B.URUT, B.KODEBRG, C.NAMABRG, B.QNT, B.QntReject, B.SATUAN, B.KodeGdg, isnull(B.IsAv, 0) IsAv
            from DBHASILPRDDET B
            left outer join DBBARANG C on C.KODEBRG = B.KODEBRG
            where B.NOBUKTI = cast(:nobukti as varchar(50))
            order by B.URUT
        ', ['nobukti' => $nobukti]);

        $total = 0;
        $rows = [];
        foreach ($lines as $l) {
            $total += (float) $l->QNT;
            $rows[] = [
                'Uraian' => trim($l->KODEBRG.' '.$l->NAMABRG),
                'Jumlah' => number_format((float) $l->QNT, 2, '.', ',').' '.$l->SATUAN,
                'Info' => 'Reject '.number_format((float) $l->QntReject, 2, '.', ',').' - Gudang '.$l->KodeGdg
                    .((int) $l->IsAv === 1 ? ' - Avalan' : ''),
            ];
        }

        return [
            'header' => [
                'No. Bukti' => $h->NOBUKTI,
                'Tanggal' => $h->TANGGAL ? date('d-m-Y', strtotime($h->TANGGAL)) : '',
                'No. SPK' => (string) $h->NoSPK,
                'Mesin' => trim($h->KodeMSN.' '.$h->NamaMesin),
                'Shift' => (int) $h->Shift ? (string) (int) $h->Shift : '-',
            ],
            'lines' => $rows,
            'total' => $total,
        ];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->selectOne('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from DBHASILPRD where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);

        return $res && (int) $res->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        $this->tolakKalauPeriodeTerkunci($nobukti);

        $item = DB::connection('SML')->selectOne('select count(*) n from DBHASILPRDDET where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);
        if (! $item || (int) $item->n === 0) {
            throw new \DomainException('Hasil produksi '.$nobukti.' belum punya item, tidak bisa diotorisasi.');
        }

        // Syarat belum diotorisasi di WHERE supaya klik ganda / dua approver tidak dobel.
        return DB::connection('SML')->update("
            UPDATE DBHASILPRD SET IsOtorisasi1 = 1, OtoUser1 = :username, TglOto1 = getdate(), MaxOL = 1
            WHERE NOBUKTI = cast(:nobukti as varchar(50)) AND isnull(IsOtorisasi1, 0) = 0
        ", ['username' => $username, 'nobukti' => $nobukti]);
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        $h = DB::connection('SML')->selectOne('select convert(varchar(10), TANGGAL, 23) Tgl from DBHASILPRD where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);
        if ($h && $this->periodeTerkunci($h->Tgl)) {
            return 'Periode hasil produksi '.$nobukti.' sudah dikunci, otorisasi tidak bisa dibatalkan.';
        }

        return null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        $res = DB::connection('SML')->update("
            UPDATE DBHASILPRD SET IsOtorisasi1 = 0, OtoUser1 = '', TglOto1 = NULL, MaxOL = -1
            WHERE NOBUKTI = cast(:nobukti as varchar(50)) AND isnull(IsOtorisasi1, 0) = 1
        ", ['nobukti' => $nobukti]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'HPD', $nobukti, $alasan, 0, 'dbhasilprd');
        }

        return $res;
    }

    private function periodeTerkunci(?string $tanggal): bool
    {
        if (! $tanggal) {
            return false;
        }

        return (bool) DB::connection('SML')->selectOne(
            'select top 1 1 x from dblockperiode where bulan = :bulan and tahun = :tahun',
            ['bulan' => (int) substr($tanggal, 5, 2), 'tahun' => (int) substr($tanggal, 0, 4)]
        );
    }

    private function tolakKalauPeriodeTerkunci(string $nobukti): void
    {
        $h = DB::connection('SML')->selectOne('select convert(varchar(10), TANGGAL, 23) Tgl from DBHASILPRD where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);
        if ($h && $this->periodeTerkunci($h->Tgl)) {
            throw new \DomainException('Periode hasil produksi '.$nobukti.' sudah dikunci, tidak bisa diotorisasi.');
        }
    }
}
