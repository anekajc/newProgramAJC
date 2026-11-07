<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Retur Penjualan Gudang (href 'returpenjualangudang', dbSPBRJual - dokumen SPR). Satu level (IsOtorisasi1);
 * tidak ada posting - dbSPBRJual tidak punya trigger, jadi otorisasi hanya menandai dokumen (MaxOL 1 / -1) supaya
 * Nota Retur Penjualan bisa menariknya. UPDATE + log dipindahkan dari ReturPenjualanGudangController
 * (spOtorisasi / spBatalOtorisasi, yang kini memanggil handler ini).
 *
 * Batal otorisasi ditolak kalau SPR sudah ditarik ke Nota Retur Penjualan (DBINVOICERPJDet.NOSPR) - aturan lama halamannya.
 */
class ReturPenjualanGudangOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select("
            select top 1 A.NoBukti, A.Tanggal, A.KodeCustSupp, C.NamaCustSupp, A.NoRPJ, A.NOSO, A.KodeGDG, A.NoPolKend, A.Sopir, A.Catatan
            from dbSPBRJual A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KodeCustSupp
            where A.NoBukti = cast(:nobukti as varchar(100)) and A.NoBukti like '%SPR%'
        ", ['nobukti' => $nobukti]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        $lines = DB::connection('SML')->select("
            select B.Urut, B.Noinv, B.KodeBrg, case when isnull(B.Namabrg, '') = '' then E.NamaBrg else B.Namabrg end Namabrg,
                   B.QNT1, B.QNT2, B.SAT_1, B.SAT_2, B.NOSAT, B.QNT1REJECT, B.QNT2REJECT, B.KodeGdg, B.NoPRRJUAL
            from dbSPBRJualDet B
            left outer join DBBARANG E on E.KODEBRG = B.KodeBrg
            where B.NoBukti = cast(:nobukti as varchar(100))
            order by B.Urut
        ", ['nobukti' => $nobukti]);

        $rows = [];
        $invoice = [];
        $prj = [];
        $gudang = [];
        foreach ($lines as $l) {
            // Satuan yang dipakai baris = NOSAT (1 = satuan utama, selain itu satuan kedua), seperti kolom Qnt di halaman.
            $pakaiSatuan2 = (int) $l->NOSAT !== 1;
            $satuan = trim((string) ($pakaiSatuan2 ? $l->SAT_2 : $l->SAT_1));
            $qty = number_format((float) ($pakaiSatuan2 ? $l->QNT2 : $l->QNT1), 2, ',', '.').' '.$satuan;
            $reject = (float) ($pakaiSatuan2 ? $l->QNT2REJECT : $l->QNT1REJECT);

            $info = [];
            if (trim((string) $l->Noinv) !== '' && trim((string) $l->Noinv) !== '-') {
                $info[] = 'Invoice '.trim($l->Noinv);
                $invoice[trim($l->Noinv)] = true;
            }
            if (trim((string) $l->NoPRRJUAL) !== '' && trim((string) $l->NoPRRJUAL) !== '-') {
                $prj[trim($l->NoPRRJUAL)] = true;
            }
            if (trim((string) $l->KodeGdg) !== '') {
                $gudang[trim($l->KodeGdg)] = true;
            }
            if ($reject > 0) {
                $info[] = 'Reject '.number_format($reject, 2, ',', '.').' '.$satuan;
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
        ];
        if ($prj) {
            $info['No. PRJ'] = implode(', ', array_keys($prj));
        }
        // Nomor invoice yang diretur: dari baris, atau (kalau kosong) dari header (NoRPJ).
        $noInvoice = $invoice ? implode(', ', array_keys($invoice)) : trim((string) $h->NoRPJ);
        if ($noInvoice !== '' && $noInvoice !== '-') {
            $info['No. Invoice'] = $noInvoice;
        }
        if (trim((string) $h->NOSO) !== '' && trim((string) $h->NOSO) !== '-') {
            $info['No. SO'] = $h->NOSO;
        }
        if ($gudang) {
            $info['Gudang'] = implode(', ', array_keys($gudang));
        } elseif (trim((string) $h->KodeGDG) !== '') {
            $info['Gudang'] = $h->KodeGDG;
        }
        if (trim((string) $h->NoPolKend) !== '' && trim((string) $h->NoPolKend) !== '-') {
            $info['No. Kendaraan'] = $h->NoPolKend;
        }
        if (trim((string) $h->Sopir) !== '' && trim((string) $h->Sopir) !== '-') {
            $info['Sopir'] = $h->Sopir;
        }
        if (trim((string) $h->Catatan) !== '' && trim((string) $h->Catatan) !== '-') {
            $info['Catatan'] = $h->Catatan;
        }

        // Retur gudang tidak punya nilai uang: tanpa total.
        return ['header' => $info, 'lines' => $rows, 'total' => null];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select("select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from dbSPBRJual where NoBukti = cast(:nobukti as varchar(100)) and NoBukti like '%SPR%'", ['nobukti' => $nobukti]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // isnull(isOtorisasi1,0) <> 1: penjaga kalau dua user menekan Otorisasi bersamaan (sama dengan controller lama).
        $res = DB::connection('SML')->update("update dbSPBRJual set isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal where nobukti = cast(:nobukti as varchar(100)) and nobukti like '%SPR%' and isnull(isOtorisasi1, 0) <> 1", [
            'username' => $username,
            'tanggal' => date('Y-m-d H:i:s'),
            'nobukti' => $nobukti,
        ]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('oto', 'RPJ', $nobukti, '', 0, 'dbSPBRJUAL');
        }

        return $res;
    }

    /** SPR yang sudah ditarik ke Nota Retur Penjualan tidak boleh dibatal otorisasi (dipakai juga ReturPenjualanGudangController). */
    public static function dipakaiNotaRetur(string $nobukti): bool
    {
        return (bool) DB::connection('SML')->selectOne('select top 1 1 ada from DBINVOICERPJDet where NOSPR = cast(? as varchar(100))', [$nobukti]);
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        return self::dipakaiNotaRetur($nobukti) ? 'SPR '.$nobukti.' sudah dipakai Nota Retur Penjualan, tidak bisa batal otorisasi' : null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        $res = DB::connection('SML')->update("update dbSPBRJual set isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL where nobukti = cast(:nobukti as varchar(100)) and nobukti like '%SPR%' and isOtorisasi1 = 1", [
            'nobukti' => $nobukti,
        ]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'RPJ', $nobukti, $alasan, 0, 'dbSPBRJUAL');
        }

        return $res;
    }
}
