<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Pembelian / Penerimaan ACC (href 'newpobeliacc', DBBELI). Satu level (MaxOL = 1, IsOtorisasi1). Satu halaman
 * melayani semua jenis (Tunai/Kredit x Stock/Non Stock), jadi tidak ada filter pJasa/TipeBayar di sini.
 *
 * Otorisasi memposting hutang dan jurnal (sp_ProsesPostingHutPiut + sp_ProsesPostingJurnalOto) memakai periode kerja
 * user yang sedang login - approver, sama persis dengan menekan Otorisasi di halamannya. Batal otorisasi membalik
 * posting itu dan ditolak kalau penerimaan sudah dipakai dokumen lanjutan.
 */
class PembelianAccOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NOBUKTI, A.TANGGAL, A.KODESUPP, C.NAMACUSTSUPP, A.NoPOHd, A.FAKTURSUPP, A.KETERANGAN,
                   A.TIPEBAYAR, A.KODEVLS, A.KURS, isnull(A.pJasa, 0) pJasa, isnull(A.IsBatal, 0) IsBatal
            from DBBELI A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KODESUPP
            where A.NOBUKTI = cast(:nobukti as varchar(50))
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        // Total per baris = NNetRp (rupiah), sama dengan kolom Total di grid (TotNet x Kurs).
        $lines = DB::connection('SML')->select("
            select B.URUT, B.KODEBRG, case when isnull(B.NamaBrg, '') = '' then E.NamaBrg else B.NamaBrg end NamaBrg,
                   B.QNT, B.SATUAN, B.HARGA, B.NNetRp
            from DBBELIDET B
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

        $jenis = ((int) $h->TIPEBAYAR === 1 ? 'Kredit' : 'Tunai').' - '.((int) $h->pJasa === 1 ? 'Non Stock' : 'Stock');
        $info = [
            'No. Bukti' => $h->NOBUKTI,
            'Tanggal' => $h->TANGGAL ? date('d-m-Y', strtotime($h->TANGGAL)) : '',
            'Supplier' => trim($h->KODESUPP.' '.$h->NAMACUSTSUPP),
            'Jenis' => $jenis,
        ];
        $nopo = trim((string) $h->NoPOHd);
        if ($nopo !== '' && $nopo !== '-') {
            $info['No. PO'] = $nopo;
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
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from DBBELI where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // Sama persis dengan NewPOBeliAccController::spOtorisasi1 (yang kini memanggil ini). Syarat belum diotorisasi di
        // WHERE: klik ganda / dua approver bersamaan tidak memposting dua kali.
        $update = DB::connection('SML')->update('update dbbeli set IsOtorisasi1 = 1, OtoUser1 = :username, TglOto1 = getDate(), MaxOL = 1 where nobukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 0', [
            'username' => $username,
            'nobukti' => $nobukti,
        ]);

        if ($update > 0) {
            $this->posting($nobukti, 1);
        }

        return $update;
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        // Sudah di-invoice / diretur / dibayar: batal otorisasi menghapus posting hutang & jurnal padahal dokumen
        // lanjutan itu masih menunjuk pembelian ini (pengecekan yang sama dengan halaman).
        $pakai = app('App\Http\Controllers\Purchasing\NewPOController')->dipakaiBeli($nobukti);

        return $pakai ? 'Pembelian '.$nobukti.' sudah dipakai di '.$pakai.', otorisasi tidak bisa dibatalkan.' : null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        // Sama dengan NewPOBeliAccController::spUnOtorisasi1 (yang kini memanggil ini), ditambah penjaga IsOtorisasi1 = 1
        // supaya posting tidak dibalik dua kali. Halaman tidak menyimpan keterangan batal; di HP alasannya ada di
        // riwayat permintaan (DBREQOTORISASI.ALASAN).
        $update = DB::connection('SML')->update("update dbbeli set IsOtorisasi1 = 0, OtoUser1 = '', TglOto1 = NULL, MaxOL = -1 where nobukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 1", ['nobukti' => $nobukti]);

        if ($update > 0) {
            $this->posting($nobukti, 0);
        }

        return $update;
    }

    /** Posting hutang/piutang dan jurnal otorisasi ($oto 1) atau pembalikannya ($oto 0), di periode kerja user yang login. */
    private function posting(string $nobukti, int $oto): void
    {
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $values = ['', 'dbbeli', $periode->bulan, $periode->tahun, $nobukti, $oto];
        DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
        DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);
    }
}
