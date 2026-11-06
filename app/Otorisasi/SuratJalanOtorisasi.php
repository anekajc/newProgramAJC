<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Surat Jalan (href 'suratjalan', dbSPB). Satu level (dbmenu.OL = 1, IsOtorisasi1); tidak ada posting - dbSPB tidak punya
 * trigger header, jadi otorisasi hanya menandai dokumen (stok sudah diurus trigger dbSPBDet saat simpan). UPDATE dipindahkan dari
 * SuratJalanController (spOtorisasi / spBatalOtorisasi, yang kini memanggil handler ini), ditambah penjaga status di WHERE supaya
 * klik ganda tidak menimpa user/tanggal.
 *
 * PERHATIAN - "Batal Otorisasi" di halaman ini MEMBATALKAN (void) SPB: IsBatal = 1, UserBatal, TglBatal, sekaligus mencabut otorisasi;
 * otorisasi ulang menghapus tanda batal itu lagi. Itu yang dilakukan tombolnya sekarang, jadi itu juga yang dilakukan HP.
 *
 * cancelBlocker() sengaja kosong: halaman tidak memeriksa pemakaian. SPB dipakai Invoice Penjualan (dbInvoicePLDet.NoSPB), Retur Surat
 * Jalan (DBRSPB / DBRSPBDet.NoSPB), Retur Invoice (DBRInvoicePL.NOSPB) - di sinilah tempat blokirnya kalau nanti diinginkan.
 * Semua query dibatasi NoBukti bukan '%POS%', sama dengan daftar di halaman (dbSPB juga memuat dokumen POS/kasir).
 */
class SuratJalanOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    private const BUKAN_POS = "NoBukti not like '%POS%'";

    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select("
            select top 1 A.NoBukti, A.Tanggal, A.KodeCustSupp, C.NamaCustSupp, A.IsFlag, A.NoPolKend, A.Sopir, A.KodeExp, A.Catatan,
                   A.Container, A.NoContainer, A.NoSeal, A.NoResi, isnull(A.IsBatal, 0) IsBatal, A.UserBatal
            from dbSPB A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KodeCustSupp
            where A.NoBukti = cast(:nobukti as varchar(50)) and A.".self::BUKAN_POS."
        ", ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        $lines = DB::connection('SML')->select("
            select B.Urut, B.KodeBrg, case when isnull(B.Namabrg, '') = '' then E.NamaBrg else B.Namabrg end Namabrg,
                   B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.NOSAT, B.KodeGdg, B.NoSo, B.NetW, B.GrossW
            from dbSPBDet B
            left outer join DBBARANG E on E.KODEBRG = B.KodeBrg
            where B.NoBukti = cast(:nobukti as varchar(50))
            order by B.Urut
        ", ['nobukti' => $nobukti]);

        $rows = [];
        $so = [];
        $gudang = [];
        foreach ($lines as $l) {
            // Satuan yang dipakai baris = NOSAT (1 = satuan utama, selain itu satuan kedua), seperti kolom Qnt di halaman.
            $pakaiSatuan2 = (int) $l->NOSAT !== 1;
            $qty = number_format((float) ($pakaiSatuan2 ? $l->QNT2 : $l->QNT), 2, ',', '.').' '.trim((string) ($pakaiSatuan2 ? $l->SAT_2 : $l->SAT_1));
            $info = [];
            if ((float) $l->GrossW > 0) {
                $info[] = 'Berat '.number_format((float) $l->GrossW, 2, ',', '.');
            }
            if (trim((string) $l->KodeGdg) !== '') {
                $info[] = 'Gudang '.trim($l->KodeGdg);
                $gudang[trim($l->KodeGdg)] = true;
            }
            $nosoLine = trim((string) $l->NoSo);
            if ($nosoLine !== '' && $nosoLine !== '-') {
                $so[$nosoLine] = true;
            }
            $kode = trim((string) $l->KodeBrg);
            $row = ['Uraian' => trim(($kode !== '' && $kode !== '-' ? $kode.' ' : '').$l->Namabrg), 'Jumlah' => trim($qty)];
            if ($info) {
                $row['Info'] = implode(' · ', $info);
            }
            $rows[] = $row;
        }

        $info = [
            'No. Bukti' => $h->NoBukti,
            'Tanggal' => $h->Tanggal ? date('d-m-Y', strtotime($h->Tanggal)) : '',
            'Customer' => trim($h->KodeCustSupp.' '.$h->NamaCustSupp),
            'Tipe' => (int) $h->IsFlag === 1 ? 'SPB Bahan Baku dan Lain-lain' : 'SPB Barang Jadi',
        ];
        if ($so) {
            $info['No. SO'] = implode(', ', array_keys($so));
        }
        if ($gudang) {
            $info['Gudang'] = implode(', ', array_keys($gudang));
        }
        foreach (['Ekspedisi' => $h->KodeExp, 'No. Polisi' => $h->NoPolKend, 'Sopir' => $h->Sopir, 'No. Resi' => $h->NoResi, 'Container' => $h->Container, 'No. Container' => $h->NoContainer, 'No. Seal' => $h->NoSeal, 'Catatan' => $h->Catatan] as $label => $nilai) {
            if (trim((string) $nilai) !== '' && trim((string) $nilai) !== '-') {
                $info[$label] = $nilai;
            }
        }
        if ((int) $h->IsBatal === 1) {
            $info['Status'] = 'Dibatalkan'.(trim((string) $h->UserBatal) !== '' ? ' oleh '.trim($h->UserBatal) : '');
        }

        // Surat jalan tidak punya nilai uang: tanpa total.
        return ['header' => $info, 'lines' => $rows, 'total' => null];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from dbSPB where NoBukti = cast(:nobukti as varchar(50)) and '.self::BUKAN_POS, ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        return DB::connection('SML')->update('update dbSPB set IsOtorisasi1 = 1, OtoUser1 = :username, TglOto1 = getDate(), IsBatal = NULL, UserBatal = NULL, TglBatal = NULL, MaxOL = 1 where NoBukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 0 and '.self::BUKAN_POS, [
            'username' => $username,
            'nobukti' => $nobukti,
        ]);
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        return null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        // Alasan tidak punya kolom di dbSPB dan halaman tidak pernah meminta atau mencatatnya (tanpa dbLogFile); di HP alasan tersimpan di permintaannya.
        return DB::connection('SML')->update("update dbSPB set IsBatal = 1, UserBatal = :username, TglBatal = GETDATE(), IsOtorisasi1 = 0, OtoUser1 = '', TglOto1 = NULL, maxol = -1 where NoBukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 1 and ".self::BUKAN_POS, [
            'username' => $username,
            'nobukti' => $nobukti,
        ]);
    }
}
