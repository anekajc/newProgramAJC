<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler SO (href 'so', DBSO). Satu level (dbmenu.OL = 1, IsOtorisasi1); tidak ada posting. UPDATE dan aturan-aturannya
 * dipindahkan dari SOController (updateOtorisasi / updateBatalOtorisasi, yang kini memanggil handler ini):
 *  - otorisasi ditolak kalau customer blacklist (DBCUSTSUPP.pblacklist);
 *  - otorisasi dan batal ditolak kalau nilai SO (SUM DBSODET.nnet) melebihi plafon jabatan approver (DBJABATAN.PlafonOtoSO)
 *    dan approver itu level 1 (hak lama dbflmenu) - approver level lebih tinggi tidak dibatasi plafon.
 * Penolakan dilempar sebagai \DomainException supaya di HP permintaan tetap menunggu dan pesannya tampil ke approver.
 *
 * cancelBlocker() sengaja kosong: batal otorisasi di halaman memang tidak memeriksa pemakaian. SO dipakai Surat Jalan (dbSPBDet.NoSo),
 * Invoice (dbInvoicePLDet.NoSO), Uang Muka Jual (DBUMJUAL.NOSO), PO (DBPO.NOSO), Perintah Retur Jual (dbPRRJual.NOSO), dsb. -
 * kalau nanti ingin diblokir, di sinilah tempatnya.
 */
class SOOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NOBUKTI, A.TANGGAL, A.KODECUST, C.NAMACUSTSUPP, M1.Nama NAMASALES, M3.Nama NAMAPIC, A.NoPesanan,
                   A.TIPEBAYAR, A.HARI, A.TglKirim, A.CATATAN, A.KETERANGAN, A.KODEVLS, A.KURS, isnull(A.ISBATAL, 0) ISBATAL
            from DBSO A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KODECUST
            left outer join dbKaryawan M1 on A.KODESLS = M1.KeyNIK
            left outer join DBPICCUSTSUPP M3 on A.KodePF = M3.KODEPIC and A.KODECUST = M3.KODECUSTSUPP
            where A.NOBUKTI = cast(:nobukti as varchar(50))
        ', ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        // Total per baris = NNETRp (rupiah), sama dengan kolom total di grid.
        $lines = DB::connection('SML')->select("
            select B.URUT, B.KODEBRG, case when isnull(B.NAMABRG, '') = '' then E.NamaBrg else B.NAMABRG end NAMABRG,
                   B.QNT, B.SATUAN, B.HARGA, B.NNETRP, isnull(B.ISBATAL, 0) ISBATAL
            from DBSODET B
            left outer join DBBARANG E on E.KODEBRG = B.KODEBRG
            where B.NOBUKTI = cast(:nobukti as varchar(50))
            order by B.URUT
        ", ['nobukti' => $nobukti]);

        $total = 0;
        $rows = [];
        foreach ($lines as $l) {
            $total += (float) $l->NNETRP;
            $kode = trim((string) $l->KODEBRG);
            $info = trim(number_format((float) $l->QNT, 2, ',', '.').' '.trim((string) $l->SATUAN)).' x '.number_format((float) $l->HARGA, 2, ',', '.');
            $rows[] = [
                'Uraian' => trim(($kode !== '' && $kode !== '-' ? $kode.' ' : '').$l->NAMABRG),
                'Jumlah' => 'Rp '.number_format((float) $l->NNETRP, 2, ',', '.'),
                'Info' => $info.((int) $l->ISBATAL === 1 ? ' (baris dibatalkan)' : ''),
            ];
        }

        $info = [
            'No. Bukti' => $h->NOBUKTI,
            'Tanggal' => $h->TANGGAL ? date('d-m-Y', strtotime($h->TANGGAL)) : '',
            'Customer' => trim($h->KODECUST.' '.$h->NAMACUSTSUPP),
        ];
        foreach (['Sales' => $h->NAMASALES, 'PIC' => $h->NAMAPIC, 'No. Pesanan' => $h->NoPesanan, 'Keterangan' => $h->KETERANGAN, 'Catatan' => $h->CATATAN] as $label => $nilai) {
            if (trim((string) $nilai) !== '' && trim((string) $nilai) !== '-') {
                $info[$label] = $nilai;
            }
        }
        // Kode tipe bayar sama dengan filter "Tipe Bayar" di halaman: 0 CBD, 1 Kredit, 2 Termin.
        $tipeBayar = [0 => 'CBD', 1 => 'Kredit', 2 => 'Termin'][(int) $h->TIPEBAYAR] ?? '';
        if ($tipeBayar !== '') {
            $info['Pembayaran'] = $tipeBayar.((int) $h->TIPEBAYAR === 1 && (int) $h->HARI > 0 ? ' '.(int) $h->HARI.' hari' : '');
        }
        if ($h->TglKirim) {
            $info['Tgl Kirim'] = date('d-m-Y', strtotime($h->TglKirim));
        }
        if ($h->KODEVLS && strtoupper(trim($h->KODEVLS)) !== 'IDR') {
            $info['Mata uang'] = $h->KODEVLS.' (kurs '.number_format((float) $h->KURS, 2, ',', '.').')';
        }
        if ((int) $h->ISBATAL === 1) {
            $info['Status'] = 'Dibatalkan';
        }

        return ['header' => $info, 'lines' => $rows, 'total' => $total, 'warnings' => $this->peringatanHarga($nobukti)];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from DBSO where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        $this->periksaBlacklist($nobukti);
        $this->periksaPlafon($nobukti, $username, 'otorisasi');

        // MaxOL diambil dari dbmenu.OL menu SO, sama dengan SOController::updateOtorisasi.
        $menu = DB::connection('SML')->selectOne("select top 1 OL from dbmenu where href = 'so'");

        $res = DB::connection('SML')->update('update dbso set isOtorisasi1 = 1, maxol = :maxol, OtoUser1 = :username, TglOto1 = :tanggal where nobukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 0', [
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
        // Hanya kalau SO memang sedang berotorisasi - klik ganda tidak menulis ulang apa-apa.
        if (! $this->isApproved($nobukti)) {
            return 0;
        }
        $this->periksaPlafon($nobukti, $username, 'batal otorisasi');

        $res = DB::connection('SML')->update("update dbso set isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL where nobukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 1", ['nobukti' => $nobukti]);

        if ($res > 0) {
            $this->log('btloto', $nobukti, $alasan);
        }

        return $res;
    }

    /**
     * Cek harga jual SO terhadap harga beli terakhir per barang (SQL yang dulu ada di SOController::cekHargaOto, dipindah ke sini
     * supaya halaman dan halaman approve HP memakai satu sumber). Tiap baris: kodebrg, hrgminso, hrgmaxso, xhrgpo, Ket ('lanjut' = aman).
     *
     * @return array<int, object>
     */
    public static function cekHarga(string $nobukti): array
    {
        return DB::connection('SML')->select("declare @noSO varchaR(30)--,@KODEBRG VARCHAR(30)
select @noSO= :noso

SELECT XTABLE.kodebrg ,

XTABLE.XBeli + (XTABLE.XBeli * 0.1 )  hrgminso,
XTABLE.XBeli + (XTABLE.XBeli * 1.00 ) hrgmaxso,
XTABLE.xhrgpo
, case when XTABLE.xhrgpo < XTABLE.XBeli + (XTABLE.XBeli * 0.1 ) then 'Margin Kurang dari Harga Minimal'
  when XTABLE.xhrgpo >= XTABLE.XBeli + (XTABLE.XBeli * 1.00 ) then 'Margin Lebih dari 100%'
else
'lanjut' End Ket
 FROM (


 select AA.kodebrg,AA.Harga - AA.DiscRp1 Hrg,B.tanggal,AA.nosat,
 AA.harga * AA.Kurs harga,
 (AA.harga * AA.Kurs ) - (AA.DiscTot * AA.kurs) -
(case when AA.PPN=2 then  (AA.harga * AA.Kurs ) * 0.1 else 0 end )XHrgPO,

 (select top 1

 case when aa.NOSAT=1 then
      case when A.NOSAT=1 then A.HRGNETTO - (case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end)
            when A.NOSAT=2 then (A.HRGNETTO /C.ISI2)- ((case when a.PPN=2 then (A.HRGNETTO/C.ISI2) * 0.10 else 0 end))
            when A.NOSAT=3 THEN (A.HRGNETTO /C.ISI3) - ((case when a.PPN=2 then (A.HRGNETTO/C.ISI3) * 0.10 else 0 end)) END * A.KURS

     when aa.NOSAt=2 then
       CASE WHEN A.NOSAT=2 THEN A.HRGNETTO - (case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end)
            WHEN A.NOSAT=1 THEN (A.HRGNETTO * C.ISI2)- ((case when a.PPN=2 then (A.HRGNETTO * C.ISI2) * 0.10 else 0 end))
            WHEN A.NOSAT=3 THEN (((A.HRGNETTO / C.ISI3)*C.ISI2)- ((case when a.PPN=2 then ((A.HRGNETTO / C.ISI3)*C.ISI2) * 0.10 else 0 end))) END * A.KURS

     when aa.NOSAt=3 then
       CASE WHEN A.NOSAT=3 THEN A.HRGNETTO - ((case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end))
            WHEN A.NOSAT=1 THEN (A.HRGNETTO * C.ISI3)- ((case when a.PPN=2 then (A.HRGNETTO * C.ISI3) * 0.10 else 0 end))
            WHEN A.NOSAT=2 THEN (((A.HRGNETTO/ C.ISI2)*c.ISI3) - ((case when a.PPN=2 then ((A.HRGNETTO / C.ISI2)*c.ISI3) * 0.10 else 0 end))) eND * A.KURS
End
from DBBELIDET A
Left OUter join DBBELI b on A.nobukti=B.NOBUKTI
Left Outer join DBBARANG C on A.KODEBRG=C.KODEBRG
Where B.TANGGAL <=B.TANGGAL and A.KODEBRG=AA.KODEBRG  and A.kodegdg<>'G06'
order by B.TANGGAL Desc
   ) XBeli


 from DbSODET AA
left outer join dbSo B on AA.NOBUKTI=B.nobukti
where AA.nobukti = @noSO and AA.iscetakkitir=0
  ) XTABLE

      ", ['noso' => $nobukti]);
    }

    /** @return array<int, string> kalimat peringatan per barang yang margin-nya di luar batas (kosong = aman) */
    protected function peringatanHarga(string $nobukti): array
    {
        $warnings = [];
        foreach (self::cekHarga($nobukti) as $r) {
            if ($r->Ket !== 'lanjut') {
                $warnings[] = 'Barang '.$r->kodebrg.' - '.$r->Ket;
            }
        }

        return $warnings;
    }

    private function periksaBlacklist(string $nobukti): void
    {
        $row = DB::connection('SML')->selectOne('
            select isnull(C.pblacklist, 0) pblacklist
            from DBSO A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KODECUST
            where A.NOBUKTI = cast(:nobukti as varchar(50))
        ', ['nobukti' => $nobukti]);

        if ($row && (int) $row->pblacklist === 1) {
            throw new \DomainException('Customer masuk dalam daftar blacklist');
        }
    }

    /**
     * Aturan plafon lama: nilai SO (SUM nnet) di atas PlafonOtoSO jabatan approver menolak approver level 1; approver dengan
     * level lebih tinggi (case-nya sama dengan SOController lama) tidak dibatasi. Hak yang dibaca adalah dbflmenu (tabel lama)
     * pada dbmenu href 'so', persis seperti query asli.
     */
    private function periksaPlafon(string $nobukti, string $username, string $aksi): void
    {
        $approver = DB::connection('SML')->selectOne("
            select top 1 isnull(d.PlafonOtoSO, 0) PlafonOtoSO,
                   case when b.IsOtorisasi1 = 1 then 1 when b.IsOtorisasi2 = 1 then 2 when b.IsOtorisasi3 = 1 then 3
                        when b.IsOtorisasi4 = 1 then 4 when b.IsOtorisasi5 = 1 then 5 end leveloto
            from dbmenu a
            join dbflmenu b on a.kodemenu = b.l1
            join DBFLPASS c on b.USERID = c.USERID
            join DBJABATAN d on c.KodeJab = d.KODEJAB
            where a.href = 'so' and b.USERID = :username
        ", ['username' => $username]);

        if (! $approver) {
            throw new \DomainException('Jabatan/plafon Anda untuk menu SO tidak ditemukan, '.$aksi.' tidak bisa diproses.');
        }

        $so = DB::connection('SML')->selectOne('select isnull(sum(nnet), 0) nnet from DBSODET where NOBUKTI = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);

        if ((int) $so->nnet > (int) $approver->PlafonOtoSO && (int) $approver->leveloto === 1) {
            throw new \DomainException('Nilai SO melebihi plafon '.$aksi.' Anda');
        }
    }

    /** Jejak audit dbLogFile - kalau gagal dicatat, otorisasinya tetap jalan (sama dengan PenawaranSOOtorisasi). */
    private function log(string $aktivitas, string $nobukti, string $keterangan): void
    {
        try {
            app('App\Http\Controllers\GlobalController')->LoggingData($aktivitas, 'SO', $nobukti, $keterangan, 0, 'DBSO');
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
