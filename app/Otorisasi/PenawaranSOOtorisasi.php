<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Penawaran SO (href 'penawaranso', DBPENAWARANSO). Satu level (dbmenu.OL = 1, IsOtorisasi1); hanya menandai
 * dokumen, tidak ada posting. UPDATE-nya dipindahkan dari PenawaranSOController (updateOtorisasi / updateBatalOtorisasi,
 * yang kini memanggil handler ini), ditambah penjaga status di WHERE supaya klik ganda tidak menimpa user/tanggal oto.
 *
 * cancelBlocker() sengaja kosong: batal otorisasi di halaman memang tidak memeriksa pemakaian. Perlu diketahui bahwa
 * penawaran dipakai SO (DBSODET.NOtawar / urutTawar) dan PO (DBPODET.nopnw) - kalau nanti ingin diblokir, di sinilah tempatnya.
 */
class PenawaranSOOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NOBUKTI, A.TANGGAL, A.KODECUST, C.NAMACUSTSUPP, A.KETERANGAN, A.VALIDITAS, A.MasaBerlaku,
                   A.KODEVLS, A.KURS, A.NAMAPIC, isnull(A.ISBATAL, 0) ISBATAL
            from DBPENAWARANSO A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KODECUST
            where A.NOBUKTI = cast(:nobukti as varchar(50))
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        // Total per baris = NNETRP (rupiah), sama dengan kolom total di grid.
        $lines = DB::connection('SML')->select("
            select B.URUT, B.KODEBRG, case when isnull(B.NAMABRG, '') = '' then E.NamaBrg else B.NAMABRG end NAMABRG,
                   B.QNT, B.nmsat, B.HARGA, B.NNETRP
            from DBPENAWARANSODET B
            left outer join DBBARANG E on E.KODEBRG = B.KODEBRG
            where B.NOBUKTI = cast(:nobukti as varchar(50))
            order by B.URUT
        ", ['nobukti' => $nobukti]);

        $total = 0;
        $rows = [];
        foreach ($lines as $l) {
            $total += (float) $l->NNETRP;
            $kode = trim((string) $l->KODEBRG);
            $rows[] = [
                'Uraian' => trim(($kode !== '' && $kode !== '-' ? $kode.' ' : '').$l->NAMABRG),
                'Jumlah' => 'Rp '.number_format((float) $l->NNETRP, 2, ',', '.'),
                'Info' => trim(number_format((float) $l->QNT, 2, ',', '.').' '.$l->nmsat).' x '.number_format((float) $l->HARGA, 2, ',', '.'),
            ];
        }

        $info = [
            'No. Bukti' => $h->NOBUKTI,
            'Tanggal' => $h->TANGGAL ? date('d-m-Y', strtotime($h->TANGGAL)) : '',
            'Customer' => trim($h->KODECUST.' '.$h->NAMACUSTSUPP),
        ];
        // Di halaman, "keterangan" = perihal dan "validitas" = catatan (lihat onChangePerihal / onChangeCatatan).
        foreach (['Perihal' => $h->KETERANGAN, 'Catatan' => $h->VALIDITAS, 'PIC' => $h->NAMAPIC] as $label => $nilai) {
            if (trim((string) $nilai) !== '' && trim((string) $nilai) !== '-') {
                $info[$label] = $nilai;
            }
        }
        if ($h->MasaBerlaku) {
            $info['Berlaku sampai'] = date('d-m-Y', strtotime($h->MasaBerlaku));
        }
        if ($h->KODEVLS && strtoupper(trim($h->KODEVLS)) !== 'IDR') {
            $info['Mata uang'] = $h->KODEVLS.' (kurs '.number_format((float) $h->KURS, 2, ',', '.').')';
        }
        if ((int) $h->ISBATAL === 1) {
            $info['Status'] = 'Dibatalkan';
        }

        return ['header' => $info, 'lines' => $rows, 'total' => $total];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from DBPENAWARANSO where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // MaxOL diambil dari dbmenu.OL menu Penawaran SO, sama dengan PenawaranSOController::updateOtorisasi.
        $menu = DB::connection('SML')->selectOne("select top 1 OL from dbmenu where href = 'penawaranso'");

        $res = DB::connection('SML')->update('update dbpenawaranso set isOtorisasi1 = 1, maxol = :maxol, OtoUser1 = :username, TglOto1 = :tanggal where nobukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 0', [
            'maxol' => $menu->OL ?? 1,
            'username' => $username,
            'tanggal' => date('Y-m-d H:i:s'),
            'nobukti' => $nobukti,
        ]);

        if ($res > 0) {
            $this->log('oto', $nobukti, '');
        }

        return $res;
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        return null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        $res = DB::connection('SML')->update("update dbpenawaranso set isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL where nobukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 1", ['nobukti' => $nobukti]);

        if ($res > 0) {
            $this->log('btloto', $nobukti, $alasan);
        }

        return $res;
    }

    /** Jejak audit dbLogFile - kalau gagal dicatat, otorisasinya tetap jalan (sama dengan PenawaranSOController::catatLog). */
    private function log(string $aktivitas, string $nobukti, string $keterangan): void
    {
        try {
            app('App\Http\Controllers\GlobalController')->LoggingData($aktivitas, 'PNW', $nobukti, $keterangan, 0, 'DBPENAWARANSO');
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
