<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Invoice Retur Beli (href 'invoicereturbeli', DBRBELI). Satu level (MaxOL = 1, IsOtorisasi1). Otorisasi
 * memposting hutang dan jurnal (sp_ProsesPostingHutPiut + sp_ProsesPostingJurnalOto) di periode kerja user yang login
 * (approver), sama dengan menekan Otorisasi di halamannya; batal otorisasi membalik posting itu.
 *
 * Aturan halaman yang tadinya hanya di JS/controller ikut dijaga di sini supaya berlaku juga lewat HP: periode yang
 * dikunci (dblockperiode, dicek JS lewat /ceklockperiode) dan dokumen tanpa barang tidak boleh diotorisasi. Penolakan
 * dilempar sebagai \DomainException (lihat OtorisasiHandler::approve).
 */
class InvoiceReturBeliOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NOBUKTI, A.TANGGAL, A.KODESUPP, C.NAMACUSTSUPP, A.NOBELI, A.FAKTURSUPP, A.KETERANGAN,
                   A.TIPEBAYAR, A.KODEVLS, A.KURS, isnull(A.IsBatal, 0) IsBatal
            from DBRBELI A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KODESUPP
            where A.NOBUKTI = cast(:nobukti as varchar(50))
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        // Total per baris = NNetRp (rupiah), sama dengan kolom TotNetRp di grid.
        $lines = DB::connection('SML')->select("
            select B.URUT, B.KODEBRG, case when isnull(B.NamaBrg, '') = '' then E.NamaBrg else B.NamaBrg end NamaBrg,
                   B.QNT, B.SATUAN, B.HARGA, B.NNetRp, B.NOPBL
            from DBRBELIDET B
            left outer join DBBARANG E on E.KODEBRG = B.KODEBRG
            where B.NOBUKTI = cast(:nobukti as varchar(50))
            order by B.URUT
        ", ['nobukti' => $nobukti]);

        $total = 0;
        $rows = [];
        foreach ($lines as $l) {
            $total += (float) $l->NNetRp;
            $rows[] = [
                'Uraian' => trim($l->KODEBRG.' '.$l->NamaBrg),
                'Jumlah' => 'Rp '.number_format((float) $l->NNetRp, 2, ',', '.'),
                'Info' => number_format((float) $l->QNT, 2, ',', '.').' '.$l->SATUAN.' x '.number_format((float) $l->HARGA, 2, ',', '.'),
            ];
        }

        $info = [
            'No. Bukti' => $h->NOBUKTI,
            'Tanggal' => $h->TANGGAL ? date('d-m-Y', strtotime($h->TANGGAL)) : '',
            'Supplier' => trim($h->KODESUPP.' '.$h->NAMACUSTSUPP),
            'Pembayaran' => (int) $h->TIPEBAYAR === 1 ? 'Kredit' : 'Tunai',
        ];
        $nobeli = trim((string) $h->NOBELI);
        if ($nobeli !== '' && $nobeli !== '-') {
            $info['No. Pembelian'] = $nobeli;
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
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from DBRBELI where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        $this->periodeHarusTerbuka();

        // Dokumen yang hanya punya header (belum ada barang) tidak boleh diotorisasi - kalau lolos, posting
        // hutang/jurnal jalan dengan nilai 0 (sama dengan InvoiceReturBeliController::spOtorisasi).
        if (! DB::connection('SML')->selectOne('select top 1 1 x from DBRBELIDET where NOBUKTI = cast(? as varchar(50))', [$nobukti])) {
            throw new \DomainException('Retur '.$nobukti.' belum punya barang, tidak bisa diotorisasi.');
        }

        // Hanya yang belum diotorisasi yang diproses - mencegah posting dobel saat tombol diklik lebih dari sekali.
        $res = DB::connection('SML')->update('update dbrbeli set isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal where nobukti = cast(:nobukti as varchar(50)) and isnull(isOtorisasi1, 0) = 0', [
            'username' => $username,
            'tanggal' => date('Y-m-d H:i:s'),
            'nobukti' => $nobukti,
        ]);

        if ($res > 0) {
            $this->posting($nobukti, 1);
            app('App\Http\Controllers\GlobalController')->LoggingData('oto', 'RPB', $nobukti, '', 0, 'DBRBELI');
        }

        return $res;
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        // Sudah dipakai di pelunasan (DPH): batal otorisasi menghapus posting hutang & jurnal padahal pelunasan itu
        // masih menunjuk retur ini.
        foreach (['NoInvoice', 'NOFAKTUR'] as $kolom) {
            $dph = DB::connection('SML')->selectOne("select top 1 NoBukti from DBDPHDET where $kolom = cast(? as varchar(50))", [$nobukti]);
            if ($dph) {
                return 'Retur '.$nobukti.' sudah dipakai di pelunasan '.$dph->NoBukti.', otorisasi tidak bisa dibatalkan.';
            }
        }

        return null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        $this->periodeHarusTerbuka();

        // Hanya yang sedang terotorisasi yang boleh dibatalkan (sama dengan InvoiceReturBeliController::spBatalOtorisasi).
        $res = DB::connection('SML')->update("update dbrbeli set isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL where nobukti = cast(:nobukti as varchar(50)) and isOtorisasi1 = 1", ['nobukti' => $nobukti]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'RPB', $nobukti, $alasan, 0, 'DBRBELI');
            $this->posting($nobukti, 0);
        }

        return $res;
    }

    /** Periode kerja user yang login sudah dikunci (dblockperiode)? Dicek JS halaman lewat /ceklockperiode. */
    private function periodeHarusTerbuka(): void
    {
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $kunci = DB::connection('SML')->selectOne('select top 1 1 x from dblockperiode where bulan = :bulan and tahun = :tahun', ['bulan' => $periode->bulan, 'tahun' => $periode->tahun]);
        if ($kunci) {
            throw new \DomainException('Periode sudah dikunci');
        }
    }

    /** Posting hutang/piutang dan jurnal otorisasi ($oto 1) atau pembalikannya ($oto 0), di periode kerja user yang login. */
    private function posting(string $nobukti, int $oto): void
    {
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $values = ['', 'dbrbeli', $periode->bulan, $periode->tahun, $nobukti, $oto];
        DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
        DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);
    }
}
