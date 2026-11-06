<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler Perintah Retur Jual (href 'perintahreturjual', dbPRRJual dengan pMIN = 0). Satu level (dbmenu.OL = 1, IsOtorisasi1); tidak ada
 * posting - dbPRRJual tidak punya trigger, jadi otorisasi hanya menandai dokumen. UPDATE + log dipindahkan dari
 * PerintahReturJualController (spOtorisasi / spBatalOtorisasi, yang kini memanggil handler ini).
 *
 * Tabel dbPRRJual dipakai dua halaman: Perintah Retur Jual (pMIN = 0) dan Perintah Retur Jual (-) (pMIN = 1, href 'perintahreturjualminus').
 * Seluruh query dan UPDATE dibatasi $pMin supaya nomor halaman yang satu tidak bisa diotorisasi lewat menu yang lain; halaman (-) cukup
 * subclass dengan `protected int $pMin = 1` (seperti PurchaseOrderNonStockOtorisasi terhadap PurchaseOrderOtorisasi).
 *
 * Batal otorisasi ditolak kalau PRJ sudah dipakai Retur Penjualan Gudang (dbSPBRJualDet.NoPRRJUAL) - aturan lama halamannya.
 */
class PerintahReturJualOtorisasi implements BatalOtorisasiHandler, OtorisasiHandler
{
    /** 0 = Perintah Retur Jual, 1 = Perintah Retur Jual (-). */
    protected int $pMin = 0;

    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NoBukti, A.Tanggal, A.KodeCustSupp, C.NamaCustSupp, A.NoRPJ, A.NOSO, A.KodeGDG, A.Catatan
            from dbPRRJual A
            left outer join DBCUSTSUPP C on C.KODECUSTSUPP = A.KodeCustSupp
            where A.NoBukti = cast(:nobukti as varchar(100)) and isnull(A.pMIN, 0) = :pmin
        ', ['nobukti' => $nobukti, 'pmin' => $this->pMin]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        $lines = DB::connection('SML')->select("
            select B.Urut, B.Noinv, B.KodeBrg, case when isnull(B.Namabrg, '') = '' then E.NamaBrg else B.Namabrg end Namabrg,
                   B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.NOSAT, B.KodeGdg, B.KETDET
            from dbPRRJualDet B
            left outer join DBBARANG E on E.KODEBRG = B.KodeBrg
            where B.NoBukti = cast(:nobukti as varchar(100))
            order by B.Urut
        ", ['nobukti' => $nobukti]);

        $rows = [];
        $invoice = [];
        $gudang = [];
        foreach ($lines as $l) {
            // Satuan yang dipakai baris = NOSAT (1 = satuan utama, selain itu satuan kedua), seperti kolom Qnt di halaman.
            $pakaiSatuan2 = (int) $l->NOSAT !== 1;
            $qty = number_format((float) ($pakaiSatuan2 ? $l->QNT2 : $l->QNT), 2, ',', '.').' '.trim((string) ($pakaiSatuan2 ? $l->SAT_2 : $l->SAT_1));
            $info = [];
            if (trim((string) $l->Noinv) !== '' && trim((string) $l->Noinv) !== '-') {
                $info[] = 'Invoice '.trim($l->Noinv);
                $invoice[trim($l->Noinv)] = true;
            }
            if (trim((string) $l->KodeGdg) !== '') {
                $gudang[trim($l->KodeGdg)] = true;
            }
            if (trim((string) $l->KETDET) !== '') {
                $info[] = trim($l->KETDET);
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
        if (trim((string) $h->Catatan) !== '' && trim((string) $h->Catatan) !== '-') {
            $info['Catatan'] = $h->Catatan;
        }

        // Perintah retur tidak punya nilai uang: tanpa total.
        return ['header' => $info, 'lines' => $rows, 'total' => null];
    }

    public function isApproved(string $nobukti): bool
    {
        $res = DB::connection('SML')->select('select top 1 isnull(IsOtorisasi1, 0) IsOtorisasi1 from dbPRRJual where NoBukti = cast(:nobukti as varchar(100)) and isnull(pMIN, 0) = :pmin', ['nobukti' => $nobukti, 'pmin' => $this->pMin]);

        return $res && (int) $res[0]->IsOtorisasi1 === 1;
    }

    public function approve(string $nobukti, string $username): int
    {
        // isnull(isOtorisasi1,0) <> 1: penjaga kalau dua user menekan Otorisasi bersamaan (sama dengan controller lama).
        $res = DB::connection('SML')->update('update dbprrjual set isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal where nobukti = cast(:nobukti as varchar(100)) and isnull(isOtorisasi1, 0) <> 1 and isnull(pMIN, 0) = :pmin', [
            'username' => $username,
            'tanggal' => date('Y-m-d H:i:s'),
            'nobukti' => $nobukti,
            'pmin' => $this->pMin,
        ]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('oto', 'PRJ', $nobukti, '', 0, 'DBPRRJUAL');
        }

        return $res;
    }

    /** PRJ yang sudah dipakai Retur Penjualan Gudang tidak boleh dibatal otorisasi (dipakai juga PerintahReturJualController::getDetail). */
    public static function dipakaiGudang(string $nobukti): bool
    {
        return (bool) DB::connection('SML')->selectOne('select top 1 1 ada from dbSPBRJualDet where NoPRRJUAL = cast(? as varchar(100))', [$nobukti]);
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        return self::dipakaiGudang($nobukti) ? 'PRJ '.$nobukti.' sudah diproses Retur Penjualan Gudang, tidak bisa batal otorisasi' : null;
    }

    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        $res = DB::connection('SML')->update("update dbprrjual set isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL where nobukti = cast(:nobukti as varchar(100)) and isOtorisasi1 = 1 and isnull(pMIN, 0) = :pmin", [
            'nobukti' => $nobukti,
            'pmin' => $this->pMin,
        ]);

        if ($res > 0) {
            app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'PRJ', $nobukti, $alasan, 0, 'DBPRRJUAL');
        }

        return $res;
    }
}
