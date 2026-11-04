<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Bank (href 'bank'). dbTrans dipakai bersama Kas, jadi SEMUA query dibatasi nomor
 * BBK/BBM - sama dengan filter BankController::fetchList().
 */
class BankOtorisasi implements OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NoBukti, A.Tanggal, A.Note, A.TipeTransHd, A.PerkiraanHd, P.Keterangan NamaPerkiraanHd,
                   A.IsOtorisasi1, A.OtoUser1, A.TglOto1
            from dbTrans A
            left outer join dbPerkiraan P on P.Perkiraan = A.PerkiraanHd
            where A.NoBukti = :nobukti
              and (A.NoBukti like \'%BBK%\' or A.NoBukti like \'%BBM%\')
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        $lines = DB::connection('SML')->select('
            select a.Urut, a.Perkiraan, b.Keterangan NamaPerkiraan, a.Lawan, d.Keterangan NamaLawan,
                   a.Keterangan, a.Valas, a.Debet + a.Kredit Jumlah, a.DebetRp + a.KreditRp JumlahRp
            from dbTransaksi a
            left outer join dbPerkiraan b on b.Perkiraan = a.Perkiraan
            left outer join dbPerkiraan d on d.Perkiraan = a.Lawan
            where a.NoBukti = :nobukti
            order by a.Urut
        ', ['nobukti' => $nobukti]);

        $total = 0;
        $rows = [];
        foreach ($lines as $l) {
            $total += (float) $l->JumlahRp;
            $rows[] = [
                'Perkiraan' => trim($l->Perkiraan.' '.$l->NamaPerkiraan),
                'Lawan' => trim($l->Lawan.' '.$l->NamaLawan),
                'Keterangan' => $l->Keterangan,
                'Jumlah' => ($l->Valas && $l->Valas !== 'IDR' ? $l->Valas.' '.number_format((float) $l->Jumlah, 2, ',', '.').' / ' : '')
                    .'Rp '.number_format((float) $l->JumlahRp, 2, ',', '.'),
            ];
        }

        return [
            'header' => [
                'No. Bukti' => $h->NoBukti,
                'Tanggal' => $h->Tanggal ? date('d-m-Y', strtotime($h->Tanggal)) : '',
                'Tipe' => $h->TipeTransHd,
                'Bank' => trim($h->PerkiraanHd.' '.$h->NamaPerkiraanHd),
                'Note' => $h->Note,
            ],
            'lines' => $rows,
            'total' => $total,
        ];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from dbTrans where NoBukti = :nobukti and (NoBukti like \'%BBK%\' or NoBukti like \'%BBM%\')', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // Sama persis dengan UPDATE lama di BankController::spOtorisasi (yang kini memanggil ini),
        // ditambah filter nomor BBK/BBM supaya tidak bisa menyentuh dokumen Kas (dbTrans dipakai bersama).
        return DB::connection('SML')->update('update dbtrans set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti and (nobukti like \'%BBK%\' or nobukti like \'%BBM%\')', [
            'username' => $username,
            'tanggal' => date('Y-m-d H:i:s'),
            'nobukti' => $nobukti,
        ]);
    }
}
