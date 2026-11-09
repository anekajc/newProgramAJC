<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Performa (href 'performance', tabel DBSO kolom OtoPerf). Otorisasi kedua di atas SO yang sudah diotorisasi penuh
 * (SO CBD atau ber-uang muka): tanda OtoPerf=1 itulah yang membuka pembuatan Uang Muka Jual. Satu level (dbmenu.OL = 1, IsOtorisasi1);
 * tidak ada posting dan - sama dengan halamannya - tidak ada jejak dbLogFile.
 *
 * Mewarisi ringkasan SO (SOOtorisasi::summary) tapi tanpa cek margin harga dan tanpa aturan blacklist/plafon: halaman Performa
 * tidak pernah memakainya. Yang berbeda hanya tanda yang dibaca/ditulis (OtoPerf, bukan IsOtorisasi1).
 */
class PerformanceOtorisasi extends SOOtorisasi
{
    public function summary(string $nobukti): ?array
    {
        $s = parent::summary($nobukti);
        if ($s === null) {
            return null;
        }

        $extra = DB::connection('SML')->selectOne('select isnull(DP, 0) DP, OtoUser1 from DBSO where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);
        if ($extra) {
            if ((float) $extra->DP > 0) {
                $s['header']['Uang Muka (DP)'] = 'Rp '.number_format((float) $extra->DP, 2, ',', '.');
            }
            if (trim((string) $extra->OtoUser1) !== '') {
                $s['header']['Otorisasi SO oleh'] = $extra->OtoUser1;
            }
        }

        return $s;
    }

    /** Performa tidak punya cek margin harga. */
    protected function peringatanHarga(string $nobukti): array
    {
        return [];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(OtoPerf, 0) OtoPerf from DBSO where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->OtoPerf === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // Daftar Performa hanya memuat SO yang sudah diotorisasi penuh (NeedOtorisasi = 0, sama dengan getDetail); endpoint lama tidak
        // mengeceknya, sekarang dicek supaya permintaan atas SO yang belum/tidak lagi diotorisasi tidak lolos.
        $so = DB::connection('SML')->selectOne('
            select Cast(Case when Case when A.IsOtorisasi1 = 1 then 1 else 0 end + Case when A.IsOtorisasi2 = 1 then 1 else 0 end +
                              Case when A.IsOtorisasi3 = 1 then 1 else 0 end + Case when A.IsOtorisasi4 = 1 then 1 else 0 end +
                              Case when A.IsOtorisasi5 = 1 then 1 else 0 end = A.MaxOL then 0 else 1 end As Bit) NeedOtorisasi
            from DBSO A where A.NOBUKTI = cast(:nobukti as varchar(50))
        ', ['nobukti' => $nobukti]);
        if ($so && (int) $so->NeedOtorisasi === 1) {
            throw new \DomainException('SO belum diotorisasi penuh, Oto Performa belum bisa diproses.');
        }

        return DB::connection('SML')->update('update DBSO set OtoPerf = 1, TglOtoPerf = GetDate(), UserOtoPerf = :username where nobukti = cast(:nobukti as varchar(50)) and isnull(OtoPerf, 0) = 0', [
            'username' => $username,
            'nobukti' => $nobukti,
        ]);
    }

    /** Sama dengan PerformanceController::spBatalOtoPerf: ditolak kalau Uang Muka Jual sudah dibuat untuk SO ini. */
    public function cancelBlocker(string $nobukti): ?string
    {
        $um = DB::connection('SML')->selectOne('select top 1 NoBukti from dbumjual where noso = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);

        return $um ? 'Tidak bisa batal otorisasi, uang muka jual '.$um->NoBukti.' sudah dibuat untuk SO ini' : null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        return DB::connection('SML')->update("update DBSO set OtoPerf = 0, TglOtoPerf = NULL, UserOtoPerf = '' where nobukti = cast(:nobukti as varchar(50)) and isnull(OtoPerf, 0) = 1", ['nobukti' => $nobukti]);
    }
}
