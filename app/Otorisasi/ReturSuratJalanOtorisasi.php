<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Retur Surat Jalan (href 'retursuratjalan', dbRSPB). Satu level (dbmenu.OL = 1, IsOtorisasi1); tidak ada posting - dbRSPB tidak
 * punya trigger, jadi otorisasi hanya menandai dokumen. UPDATE + log dipindahkan dari ReturSuratJalanController (spOtorisasi /
 * spBatalOtorisasi, yang kini memanggil handler ini), ditambah penjaga status di WHERE supaya klik ganda tidak menimpa user/tanggal.
 *
 * Aturan halaman ikut dijaga di sini supaya berlaku juga lewat HP: otorisasi dan batal otorisasi hanya untuk retur yang tanggalnya di
 * bulan periode kerja user yang login (approver) - di luar itu dilempar sebagai \DomainException (lihat OtorisasiHandler::approve), jadi
 * permintaan di HP tetap menunggu dan pesannya tampil ke approver. Approver harus berada di periode yang sama dengan tanggal retur.
 *
 * cancelBlocker() sengaja kosong: halaman tidak memeriksa pemakaian retur (mis. nota retur / uang muka yang menunjuknya lewat NoRetur).
 */
class ReturSuratJalanOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select("
            select top 1 A.NoBukti, A.Tanggal, A.NoSPB, S.Tanggal TglSPB, A.KodeCustSupp, C.NamaCustSupp, A.IsFlag, A.NoPolKend,
                   A.Container, A.NoContainer, A.NoSeal, A.Catatan, A.KodeGdg
            from dbRSPB A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KodeCustSupp
            left outer join dbSPB S on S.NoBukti = A.NoSPB
            where A.NoBukti = cast(:nobukti as varchar(50))
        ", ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        $lines = DB::connection('SML')->select("
            select B.Urut, B.NoSPB, B.KodeBrg, case when isnull(B.Namabrg, '') = '' then E.NamaBrg else B.Namabrg end Namabrg,
                   B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.NOSAT, B.GrossW
            from dbRSPBDet B
            left outer join DBBARANG E on E.KODEBRG = B.KodeBrg
            where B.NoBukti = cast(:nobukti as varchar(50))
            order by B.Urut
        ", ['nobukti' => $nobukti]);

        $rows = [];
        foreach ($lines as $l) {
            // Satuan yang dipakai baris = NOSAT (1 = satuan utama, selain itu satuan kedua), seperti kolom Qnt di halaman.
            $pakaiSatuan2 = (int) $l->NOSAT !== 1;
            $qty = number_format((float) ($pakaiSatuan2 ? $l->QNT2 : $l->QNT), 2, ',', '.').' '.trim((string) ($pakaiSatuan2 ? $l->SAT_2 : $l->SAT_1));
            $kode = trim((string) $l->KodeBrg);
            $row = ['Uraian' => trim(($kode !== '' && $kode !== '-' ? $kode.' ' : '').$l->Namabrg), 'Jumlah' => trim($qty)];
            if ((float) $l->GrossW > 0) {
                $row['Info'] = 'Berat '.number_format((float) $l->GrossW, 2, ',', '.');
            }
            $rows[] = $row;
        }

        $info = [
            'No. Bukti' => $h->NoBukti,
            'Tanggal' => $h->Tanggal ? date('d-m-Y', strtotime($h->Tanggal)) : '',
            'Customer' => trim($h->KodeCustSupp.' '.$h->NamaCustSupp),
            'Tipe' => (int) $h->IsFlag === 1 ? 'Retur Surat Jalan Bahan Baku dan Lain-lain' : 'Retur Surat Jalan Barang Jadi',
            'No. Surat Jalan' => trim((string) $h->NoSPB).($h->TglSPB ? ' ('.date('d-m-Y', strtotime($h->TglSPB)).')' : ''),
        ];
        foreach (['Gudang' => $h->KodeGdg, 'No. Polisi' => $h->NoPolKend, 'Container' => $h->Container, 'No. Container' => $h->NoContainer, 'No. Seal' => $h->NoSeal, 'Catatan' => $h->Catatan] as $label => $nilai) {
            if (trim((string) $nilai) !== '' && trim((string) $nilai) !== '-') {
                $info[$label] = $nilai;
            }
        }

        // Retur surat jalan tidak punya nilai uang: tanpa total.
        return ['header' => $info, 'lines' => $rows, 'total' => null];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from dbRSPB where NoBukti = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        $this->periodeHarusAktif($nobukti, 'diotorisasi');

        // Hanya yang belum diotorisasi yang diproses - klik ganda / dua user bersamaan tidak menimpa user & tanggal otorisasi.
        $res = DB::connection('SML')->update('update dbrSPB set IsOtorisasi1 = 1, OtoUser1 = :username, TglOto1 = getDate(), MaxOL = 1 where NoBukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 0', [
            'username' => $username,
            'nobukti' => $nobukti,
        ]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('oto', 'RSJ', $nobukti, '', 0, 'dbrSPB');
        }

        return $res;
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        return null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        $this->periodeHarusAktif($nobukti, 'batal otorisasi');

        $res = DB::connection('SML')->update("update dbrSPB set IsOtorisasi1 = 0, OtoUser1 = '', TglOto1 = NULL, maxol = -1 where NoBukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 1", ['nobukti' => $nobukti]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'RSJ', $nobukti, $alasan, 0, 'dbrSPB');
        }

        return $res;
    }

    /** Tanggal retur harus di bulan periode kerja user yang login (ReturSuratJalanController::dalamPeriodeAktif). */
    private function periodeHarusAktif(string $nobukti, string $aksi): void
    {
        $retur = DB::connection('SML')->selectOne('select Tanggal from dbRSPB where NoBukti = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);
        if (! $retur || ! $retur->Tanggal) {
            return; // tidak ditemukan: UPDATE berikutnya tidak mengubah apa-apa dan pemanggil menjawab "tidak ditemukan"
        }
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $tgl = \Carbon\Carbon::parse($retur->Tanggal);
        if ((int) $tgl->year !== (int) $periode->tahun || (int) $tgl->month !== (int) $periode->bulan) {
            throw new \DomainException('Retur SJ '.$nobukti.' di luar periode aktif Anda ('.sprintf('%02d/%d', $periode->bulan, $periode->tahun).'), tidak bisa '.$aksi.'.');
        }
    }
}
