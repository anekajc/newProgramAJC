<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Perintah Retur Beli (href 'perintahreturbeli', DBPRRBELI). Satu level (MaxOL = 1, IsOtorisasi1) dan hanya
 * menandai dokumen - tidak ada posting. UPDATE-nya dipindahkan apa adanya dari PerintahReturBeliController
 * (updateOtorisasi / updateBatalOtorisasi, yang kini memanggil handler ini), lengkap dengan jejak audit dbLogFile.
 */
class PerintahReturBeliOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NOBUKTI, A.TANGGAL, A.FAKTURSUPP, A.KETERANGAN, A.KODEVLS, A.KURS, A.TIPEBAYAR, isnull(A.IsBatal, 0) IsBatal
            from DBPRRBELI A
            where A.NOBUKTI = cast(:nobukti as varchar(50))
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        // Supplier ada di baris (DBPRRBELIDET.kodesupp), bukan di header.
        $lines = DB::connection('SML')->select("
            select B.URUT, B.KODEBRG, case when isnull(B.NamaBrg, '') = '' then E.NamaBrg else B.NamaBrg end NamaBrg,
                   B.QNT, B.SATUAN, B.HARGA, B.NNetRp, B.NOPBL, B.NORJUAL, B.kodesupp, C.NAMACUSTSUPP
            from DBPRRBELIDET B
            left outer join DBBARANG E on E.KODEBRG = B.KODEBRG
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = B.kodesupp
            where B.NOBUKTI = cast(:nobukti as varchar(50))
            order by B.URUT
        ", ['nobukti' => $nobukti]);

        $total = 0;
        $rows = [];
        $suppliers = [];
        $nobeli = [];
        foreach ($lines as $l) {
            $total += (float) $l->NNetRp;
            if (trim((string) $l->kodesupp) !== '') {
                $suppliers[$l->kodesupp] = trim($l->kodesupp.' '.$l->NAMACUSTSUPP);
            }
            if (trim((string) $l->NOPBL) !== '' && trim((string) $l->NOPBL) !== '-') {
                $nobeli[$l->NOPBL] = $l->NOPBL;
            }
            $rows[] = [
                'Uraian' => trim($l->KODEBRG.' '.$l->NamaBrg),
                'Jumlah' => 'Rp '.number_format((float) $l->NNetRp, 2, ',', '.'),
                'Info' => number_format((float) $l->QNT, 2, ',', '.').' '.$l->SATUAN.' x '.number_format((float) $l->HARGA, 2, ',', '.'),
            ];
        }

        $info = [
            'No. Bukti' => $h->NOBUKTI,
            'Tanggal' => $h->TANGGAL ? date('d-m-Y', strtotime($h->TANGGAL)) : '',
        ];
        if ($suppliers) {
            $info['Supplier'] = implode(', ', $suppliers);
        }
        if ($nobeli) {
            $info['No. Pembelian'] = implode(', ', $nobeli);
        }
        if (trim((string) $h->FAKTURSUPP) !== '') {
            $info['Faktur Supplier'] = $h->FAKTURSUPP;
        }
        if ($h->KODEVLS && strtoupper(trim($h->KODEVLS)) !== 'IDR') {
            $info['Mata uang'] = $h->KODEVLS.' (kurs '.number_format((float) $h->KURS, 2, ',', '.').')';
        }
        if (trim((string) $h->KETERANGAN) !== '' && trim((string) $h->KETERANGAN) !== '-') {
            $info['Keterangan'] = $h->KETERANGAN;
        }
        if ((int) $h->IsBatal === 1) {
            $info['Status'] = 'Dibatalkan';
        }

        return ['header' => $info, 'lines' => $rows, 'total' => $total];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from DBPRRBELI where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // Sama persis dengan UPDATE di PerintahReturBeliController::updateOtorisasi. Syarat belum diotorisasi di WHERE:
        // dua approver bersamaan tidak saling menimpa user/tanggal otorisasi.
        $res = DB::connection('SML')->update('update DBPRRBELI set isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal where nobukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 0', [
            'username' => $username,
            'tanggal' => date('Y-m-d H:i:s'),
            'nobukti' => $nobukti,
        ]);

        // Jejak audit yang sama dengan otorisasi dari desktop, supaya approve lewat HP juga tercatat di dbLogFile.
        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('oto', 'PRB', $nobukti, '', 0, 'DBPRRBELI');
        }

        return $res;
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        // Sudah dipakai Retur Pembelian Gudang: sesudah batal otorisasi itemnya bisa diedit/dihapus, padahal
        // DBRBELIDET.NOPRB/UrutPRB masih menunjuk item itu.
        $retur = DB::connection('SML')->selectOne('select top 1 NoBukti from DBRBELIDET where NOPRB = cast(? as varchar(50))', [$nobukti]);

        return $retur ? 'Perintah retur '.$nobukti.' sudah dipakai di '.$retur->NoBukti.', otorisasi tidak bisa dibatalkan.' : null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        // Sama dengan UPDATE di PerintahReturBeliController::updateBatalOtorisasi, ditambah penjaga IsOtorisasi1 = 1.
        $res = DB::connection('SML')->update("update DBPRRBELI set isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL where nobukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 1", ['nobukti' => $nobukti]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'PRB', $nobukti, $alasan, 0, 'DBPRRBELI');
        }

        return $res;
    }
}
