<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Nota Retur Penjualan (href 'notareturpenjualan', DBINVOICERPJ / DBINVOICERPJDet - dokumen IVRJ). Satu level (IsOtorisasi1;
 * dbmenu tidak punya baris untuk 04055, kodenya hanya memakai IsOtorisasi1). Otorisasi memposting piutang dan jurnal
 * (sp_ProsesPostingHutPiut + sp_ProsesPostingJurnalOto, jenis 'RPJ') di periode kerja user yang login (approver), sama dengan menekan
 * Otorisasi di halamannya; batal otorisasi membalik posting itu. Dipanggil di dalam transaksi (service HP dan
 * NotaReturPenjualanController), jadi kalau posting gagal flag-nya ikut batal. UPDATE + log dipindahkan dari controller.
 *
 * PERHATIAN - "Batal Otorisasi" di halaman ini MENANDAI nota batal: IsBatal = 1, UserBatal, TglBatal, sekaligus mencabut otorisasi dan membalik
 * posting; otorisasi ulang menghapus tanda batal itu lagi. Itu yang dilakukan tombolnya sekarang, jadi itu juga yang dilakukan HP.
 *
 * Nota tanpa item ditolak (DomainException, sama dengan penolakan "belum punya item" di halaman). cancelBlocker() sengaja kosong: halaman tidak
 * memeriksa pemakaian nota (misalnya pelunasan piutang atas nota ini) - di sinilah tempat blokirnya kalau nanti diinginkan.
 * DBINVOICERPJ hanya dipakai halaman ini, jadi tidak perlu filter tambahan.
 */
class NotaReturPenjualanOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NoBukti, A.Tanggal, A.KODECUSTSUPP, C.NamaCustSupp, A.NoInvoice, A.NORPJ, A.KODEVLS, A.KURS, A.TIPEBAYAR, A.HARI,
                   A.NoPajak, A.catatan, isnull(A.IsBatal, 0) IsBatal, A.UserBatal
            from DBINVOICERPJ A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KODECUSTSUPP
            where A.NoBukti = cast(:nobukti as varchar(100))
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        // Nilai per baris = NNetRp (rupiah, termasuk PPN), sama dengan total di grid halaman.
        $lines = DB::connection('SML')->select("
            select B.Urut, B.Kodebrg, case when isnull(B.NamaBrg, '') = '' then E.NamaBrg else B.NamaBrg end NamaBrg,
                   B.Qnt, B.Qnt2, B.SAT_1, B.SAT_2, B.Nosat, B.Harga, B.NDPPRp, B.NPPNRp, B.NNETRp, B.NOSPR
            from DBINVOICERPJDet B
            left outer join DBBARANG E on E.KODEBRG = B.Kodebrg
            where B.NoBukti = cast(:nobukti as varchar(100))
            order by B.Urut
        ", ['nobukti' => $nobukti]);

        $rows = [];
        $spr = [];
        $dpp = 0.0;
        $ppn = 0.0;
        $net = 0.0;
        foreach ($lines as $l) {
            // Satuan yang dipakai baris = NOSAT (1 = satuan utama, selain itu satuan kedua), seperti kolom Qnt di halaman.
            $pakaiSatuan2 = (int) $l->Nosat !== 1;
            $qty = trim(number_format((float) ($pakaiSatuan2 ? $l->Qnt2 : $l->Qnt), 2, ',', '.').' '.trim((string) ($pakaiSatuan2 ? $l->SAT_2 : $l->SAT_1)));
            $kode = trim((string) $l->Kodebrg);
            $rows[] = [
                'Uraian' => trim(($kode !== '' && $kode !== '-' ? $kode.' ' : '').$l->NamaBrg),
                'Jumlah' => 'Rp '.number_format((float) $l->NNETRp, 2, ',', '.'),
                'Info' => $qty.' x '.number_format((float) $l->Harga, 2, ',', '.'),
            ];
            if (trim((string) $l->NOSPR) !== '' && trim((string) $l->NOSPR) !== '-') {
                $spr[trim($l->NOSPR)] = true;
            }
            $dpp += (float) $l->NDPPRp;
            $ppn += (float) $l->NPPNRp;
            $net += (float) $l->NNETRp;
        }

        $info = [
            'No. Bukti' => $h->NoBukti,
            'Tanggal' => $h->Tanggal ? date('d-m-Y', strtotime($h->Tanggal)) : '',
            'Customer' => trim($h->KODECUSTSUPP.' '.$h->NamaCustSupp),
        ];
        if (trim((string) $h->NoInvoice) !== '' && trim((string) $h->NoInvoice) !== '-') {
            $info['No. Invoice'] = $h->NoInvoice;
        }
        // Retur gudang (SPR) yang ditarik: dari baris, atau (kalau kosong) dari header (NORPJ).
        $noSpr = $spr ? implode(', ', array_keys($spr)) : trim((string) $h->NORPJ);
        if ($noSpr !== '' && $noSpr !== '-') {
            $info['No. Retur Gudang'] = $noSpr;
        }
        // Kode tipe bayar sama dengan filter "Tipe Bayar" di halaman SO: 0 CBD, 1 Kredit, 2 Termin.
        $tipeBayar = [0 => 'CBD', 1 => 'Kredit', 2 => 'Termin'][(int) $h->TIPEBAYAR] ?? '';
        if ($tipeBayar !== '') {
            $info['Pembayaran'] = $tipeBayar.((int) $h->TIPEBAYAR === 1 && (int) $h->HARI > 0 ? ' '.(int) $h->HARI.' hari' : '');
        }
        if (trim((string) $h->NoPajak) !== '') {
            $info['No. Pajak'] = $h->NoPajak;
        }
        if ($h->KODEVLS && strtoupper(trim($h->KODEVLS)) !== 'IDR') {
            $info['Mata uang'] = $h->KODEVLS.' (kurs '.number_format((float) $h->KURS, 2, ',', '.').')';
        }
        $info['DPP'] = 'Rp '.number_format($dpp, 2, ',', '.');
        $info['PPN'] = 'Rp '.number_format($ppn, 2, ',', '.');
        if (trim((string) $h->catatan) !== '' && trim((string) $h->catatan) !== '-') {
            $info['Catatan'] = $h->catatan;
        }
        if ((int) $h->IsBatal === 1) {
            $info['Status'] = 'Dibatalkan'.(trim((string) $h->UserBatal) !== '' ? ' oleh '.trim($h->UserBatal) : '');
        }

        return ['header' => $info, 'lines' => $rows, 'total' => $net];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from DBINVOICERPJ where NoBukti = cast(:nobukti as varchar(100))', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        $item = DB::connection('SML')->selectOne('select count(*) n from DBINVOICERPJDet where NoBukti = cast(? as varchar(100))', [$nobukti]);
        if (! (int) $item->n) {
            throw new \DomainException('Nota '.$nobukti.' belum punya item');
        }

        // Syarat "belum otorisasi" ikut di WHERE: klik ganda / dua user bersamaan tidak boleh memposting piutang & jurnal dua kali.
        $res = DB::connection('SML')->update('update DBINVOICERPJ set IsOtorisasi1 = 1, OtoUser1 = :username, TglOto1 = getDate(), IsBatal = NULL, UserBatal = NULL, TglBatal = NULL, MaxOL = 1 where NoBukti = cast(:nobukti as varchar(100)) and isnull(IsOtorisasi1, 0) <> 1', [
            'username' => $username,
            'nobukti' => $nobukti,
        ]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('oto', 'IVRJ', $nobukti, '', 0, 'DBINVOICERPJ');
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
        $res = DB::connection('SML')->update("update DBINVOICERPJ set IsBatal = 1, UserBatal = :username, TglBatal = GETDATE(), IsOtorisasi1 = 0, OtoUser1 = '', TglOto1 = NULL, maxol = -1 where NoBukti = cast(:nobukti as varchar(100)) and IsOtorisasi1 = 1", [
            'username' => $username,
            'nobukti' => $nobukti,
        ]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'IVRJ', $nobukti, $alasan, 0, 'DBINVOICERPJ');
            $this->posting($nobukti, 0);
        }

        return $res;
    }

    /** Posting piutang dan jurnal otorisasi ($oto 1) atau pembalikannya ($oto 0), di periode kerja user yang login. */
    private function posting(string $nobukti, int $oto): void
    {
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $values = ['', 'DBINVOICERPJ', $periode->bulan, $periode->tahun, $nobukti, $oto];
        DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
        DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);
    }
}
