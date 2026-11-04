<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler PR Keagenan (href 'pembelianpermintaanagen'). DBPPL dipakai bersama PR Stock Non Keagenan
 * (pAgen = 0) dan PR Jasa (pJasa = 1), jadi SEMUA query dibatasi pAgen = 1 dan pJasa = 0 - sama dengan
 * daftar PembelianPermintaanAgenController::loadAll (vwppl: IsJasa = 0 and pAgen = 1).
 */
class PembelianPermintaanAgenOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NoBukti, A.Tanggal, A.KDDep, D.NMDEP
            from DBPPL A
            left outer join DBDEPART D on D.KDDEP = A.KDDep
            where A.NoBukti = cast(:nobukti as varchar(50))
              and isnull(A.pAgen, 0) = 1 and isnull(A.pJasa, 0) = 0
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        // VwPPL memberi Satuan sesuai NoSat; left join ke DBPPLDet, jadi PR tanpa item menghasilkan baris Urut null.
        $lines = DB::connection('SML')->select('
            select Urut, KodeBrg, NamaBrg, Qnt, Satuan, Keterangan, NoSOCust
            from VwPPL
            where NoBukti = cast(:nobukti as varchar(50)) and Urut is not null
            order by Urut
        ', ['nobukti' => $nobukti]);

        $rows = [];
        foreach ($lines as $l) {
            $soCust = trim((string) $l->NoSOCust);
            $rows[] = [
                'Uraian' => trim($l->KodeBrg.' '.$l->NamaBrg),
                'Keterangan' => $l->Keterangan,
                'Jumlah' => number_format((float) $l->Qnt, 2, ',', '.').' '.$l->Satuan,
                'Info' => ($soCust !== '' && $soCust !== '-') ? 'SO Customer: '.$soCust : '',
            ];
        }

        return [
            'header' => [
                'No. Bukti' => $h->NoBukti,
                'Tanggal' => $h->Tanggal ? date('d-m-Y', strtotime($h->Tanggal)) : '',
                'Departemen' => ($h->NMDEP && $h->NMDEP !== $h->KDDep) ? $h->KDDep.' - '.$h->NMDEP : trim((string) $h->KDDep),
            ],
            'lines' => $rows,
            'total' => null, // PR tidak punya nilai uang - approve.blade.php menyembunyikan baris Total
        ];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from DBPPL where NoBukti = cast(:nobukti as varchar(50)) and isnull(pAgen, 0) = 1 and isnull(pJasa, 0) = 0', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // Sama persis dengan UPDATE lama di PembelianPermintaanAgenController::updateOtorisasi (yang kini
        // memanggil ini), ditambah filter pAgen/pJasa supaya tidak bisa menyentuh PR Non Agen / PR Jasa.
        $res = DB::connection('SML')->update('UPDATE DBPPL SET isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal WHERE NoBukti = cast(:nobukti as varchar(50)) AND isnull(IsOtorisasi1, 0) = 0 AND isnull(pAgen, 0) = 1 AND isnull(pJasa, 0) = 0', [
            'username' => $username,
            'tanggal' => date('Y-m-d H:i:s'),
            'nobukti' => $nobukti,
        ]);

        // Jejak audit yang sama dengan otorisasi dari desktop, supaya approve lewat HP juga tercatat di dbLogFile.
        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('oto', 'PR', $nobukti, '', 0, 'DBPPL');
        }

        return $res;
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        // PR yang sudah dibuat PO tidak boleh dibatalkan: sesudah batal otorisasi itemnya bisa
        // diedit/dihapus, padahal DBPODET.NoPPL/UrutPPL masih menunjuk item itu.
        $po = DB::connection('SML')->selectOne('select top 1 NoBukti from DBPODET where NoPPL = cast(? as varchar(50))', [$nobukti]);

        return $po ? 'PR '.$nobukti.' sudah dibuat PO '.$po->NoBukti.', otorisasi tidak bisa dibatalkan.' : null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        // Sama dengan UPDATE lama di PembelianPermintaanAgenController::updateBatalOtorisasi (yang kini
        // memanggil ini), ditambah filter pAgen/pJasa dan penjaga IsOtorisasi1 = 1.
        $res = DB::connection('SML')->update("UPDATE DBPPL SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = cast(:nobukti as varchar(50)) AND isnull(IsOtorisasi1, 0) = 1 AND isnull(pAgen, 0) = 1 AND isnull(pJasa, 0) = 0", [
            'nobukti' => $nobukti,
        ]);

        // Jejak audit yang sama dengan batal otorisasi dari desktop (keterangan = alasan).
        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'PR', $nobukti, $alasan, 0, 'DBPPL');
        }

        return $res;
    }
}
