<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Invoice Penjualan (href 'invoicepenjualan', dbInvoicePL). Satu level (dbmenu.OL = 1, IsOtorisasi1). Otorisasi memposting
 * piutang dan jurnal (sp_ProsesPostingHutPiut + sp_ProsesPostingJurnalOto) di periode kerja user yang login (approver), sama dengan
 * menekan Otorisasi di halamannya; batal otorisasi membalik posting itu. Dipanggil di dalam transaksi (service HP dan
 * InvoicePenjualanController), jadi kalau posting gagal flag-nya ikut batal.
 *
 * PERHATIAN - "Batal Otorisasi" di halaman ini MEMBATALKAN (void) invoice: IsBatal = 1, UserBatal, TglBatal, sekaligus mencabut otorisasi
 * dan membalik posting; otorisasi ulang menghapus tanda batal itu lagi. Itu yang dilakukan tombolnya sekarang, jadi itu juga yang dilakukan HP.
 *
 * Tabel dbInvoicePL juga memuat Invoice Jasa (pJasa = 1) dan invoice kasir (FlagKasir = 1); daftar di halaman mengecualikan keduanya, jadi
 * semua query dan UPDATE di sini juga.
 *
 * cancelBlocker() sengaja kosong: halaman tidak memeriksa pemakaian invoice. Invoice dipakai pelunasan (DBDPHDET.NoInvoice / NOFAKTUR), Nota Retur
 * Penjualan (DBINVOICERPJ.NoInvoice), Retur Invoice (DBRInvoicePL.NoInvoice), Kredit/Debet Note (DBKreditNoteDET / DBDebetNoteDET.NoInv) -
 * di sinilah tempat blokirnya kalau nanti diinginkan.
 */
class InvoicePenjualanOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    /** Hanya Invoice Penjualan: bukan kasir, bukan jasa (sama dengan daftar di InvoicePenjualanController::sqlInvoicePenjualan). */
    private function invoicePenjualan(string $alias = ''): string
    {
        $a = $alias === '' ? '' : $alias.'.';

        return "isnull({$a}FlagKasir, 0) = 0 and isnull({$a}pJasa, 0) = 0";
    }

    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NoBukti, A.Tanggal, A.KodeCustSupp, C.NamaCustSupp, A.Valas, A.Kurs, A.TipeBayar, A.HARI, A.NoPajak, A.Footnote,
                   isnull(A.NUangMuka, 0) NUangMuka, isnull(A.IsBatal, 0) IsBatal, A.UserBatal,
                   isnull(V.TotDPPRp, 0) TotDPPRp, isnull(V.TotPPNRp, 0) TotPPNRp, isnull(V.TotNetRp, 0) TotNetRp
            from dbInvoicePL A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KodeCustSupp
            left outer join vwRpDetInvoicePL V on V.NoBukti = A.NoBukti
            where A.NoBukti = cast(:nobukti as varchar(50)) and '.$this->invoicePenjualan('A').'
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        // Total per baris = NNetRp (rupiah, termasuk PPN), sama dengan kolom total di grid.
        $lines = DB::connection('SML')->select("
            select B.Urut, B.KodeBrg, case when isnull(B.Namabrg, '') = '' then E.NamaBrg else B.Namabrg end Namabrg,
                   B.QNT, B.SAT_1, B.HARGA, B.NNetRp, B.NoSPB, B.NoSO
            from dbInvoicePLDet B
            left outer join DBBARANG E on E.KODEBRG = B.KodeBrg
            where B.NoBukti = cast(:nobukti as varchar(50))
            order by B.Urut
        ", ['nobukti' => $nobukti]);

        $rows = [];
        $spb = [];
        $so = [];
        foreach ($lines as $l) {
            $kode = trim((string) $l->KodeBrg);
            $rows[] = [
                'Uraian' => trim(($kode !== '' && $kode !== '-' ? $kode.' ' : '').$l->Namabrg),
                'Jumlah' => 'Rp '.number_format((float) $l->NNetRp, 2, ',', '.'),
                'Info' => trim(number_format((float) $l->QNT, 2, ',', '.').' '.trim((string) $l->SAT_1)).' x '.number_format((float) $l->HARGA, 2, ',', '.'),
            ];
            if (trim((string) $l->NoSPB) !== '' && trim((string) $l->NoSPB) !== '-') {
                $spb[trim($l->NoSPB)] = true;
            }
            if (trim((string) $l->NoSO) !== '' && trim((string) $l->NoSO) !== '-') {
                $so[trim($l->NoSO)] = true;
            }
        }

        $info = [
            'No. Bukti' => $h->NoBukti,
            'Tanggal' => $h->Tanggal ? date('d-m-Y', strtotime($h->Tanggal)) : '',
            'Customer' => trim($h->KodeCustSupp.' '.$h->NamaCustSupp),
        ];
        if ($so) {
            $info['No. SO'] = implode(', ', array_keys($so));
        }
        if ($spb) {
            $info['No. Surat Jalan'] = implode(', ', array_keys($spb));
        }
        // Kode tipe bayar sama dengan filter "Tipe Bayar" di halaman SO: 0 CBD, 1 Kredit, 2 Termin.
        $tipeBayar = [0 => 'CBD', 1 => 'Kredit', 2 => 'Termin'][(int) $h->TipeBayar] ?? '';
        if ($tipeBayar !== '') {
            $info['Pembayaran'] = $tipeBayar.((int) $h->TipeBayar === 1 && (int) $h->HARI > 0 ? ' '.(int) $h->HARI.' hari' : '');
        }
        if (trim((string) $h->NoPajak) !== '') {
            $info['No. Pajak'] = $h->NoPajak;
        }
        if ($h->Valas && strtoupper(trim($h->Valas)) !== 'IDR') {
            $info['Mata uang'] = $h->Valas.' (kurs '.number_format((float) $h->Kurs, 2, ',', '.').')';
        }
        $info['DPP'] = 'Rp '.number_format((float) $h->TotDPPRp, 2, ',', '.');
        $info['PPN'] = 'Rp '.number_format((float) $h->TotPPNRp, 2, ',', '.');
        if ((float) $h->NUangMuka != 0.0) {
            $info['Uang muka'] = number_format((float) $h->NUangMuka, 2, ',', '.');
        }
        if (trim((string) $h->Footnote) !== '' && trim((string) $h->Footnote) !== '-') {
            $info['Catatan'] = $h->Footnote;
        }
        if ((int) $h->IsBatal === 1) {
            $info['Status'] = 'Dibatalkan'.(trim((string) $h->UserBatal) !== '' ? ' oleh '.trim($h->UserBatal) : '');
        }

        return ['header' => $info, 'lines' => $rows, 'total' => (float) $h->TotNetRp];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from dbInvoicePL where NoBukti = cast(:nobukti as varchar(50)) and '.$this->invoicePenjualan(), ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // Syarat "belum otorisasi" ikut di WHERE: klik ganda / dua user bersamaan tidak boleh memposting hutang-piutang & jurnal dua kali.
        $res = DB::connection('SML')->update('update dbinvoicepl set IsOtorisasi1 = 1, OtoUser1 = :username, TglOto1 = getDate(), IsBatal = NULL, UserBatal = NULL, TglBatal = NULL, MaxOL = 1 where NoBukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 0 and '.$this->invoicePenjualan(), [
            'username' => $username,
            'nobukti' => $nobukti,
        ]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('oto', 'INVC', $nobukti, '', 0, 'DBINVOICEPL');
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
        $res = DB::connection('SML')->update("update dbinvoicepl set IsBatal = 1, UserBatal = :username, TglBatal = GETDATE(), IsOtorisasi1 = 0, OtoUser1 = '', TglOto1 = NULL, maxol = -1 where NoBukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 1 and ".$this->invoicePenjualan(), [
            'username' => $username,
            'nobukti' => $nobukti,
        ]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'INVC', $nobukti, $alasan, 0, 'DBINVOICEPL');
            $this->posting($nobukti, 0);
        }

        return $res;
    }

    /** Posting piutang dan jurnal otorisasi ($oto 1) atau pembalikannya ($oto 0), di periode kerja user yang login. */
    private function posting(string $nobukti, int $oto): void
    {
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $values = ['', 'DBINVOICEPL', $periode->bulan, $periode->tahun, $nobukti, $oto];
        DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
        DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);
    }
}
