<?php

namespace App\Otorisasi;

use Illuminate\Support\Facades\DB;

/**
 * Handler PO Stock (href 'purchaseorder'). Otorisasi bertingkat: IsOtorisasi1 + IsOtorisasi2 dengan plafon jabatan -
 * aturannya dipindahkan apa adanya dari POController::updateOtorisasi (yang kini memanggil approveWithRules()).
 * Penerima permintaan push ditentukan nilai PO (approverLevel): sampai 200 jt ke approver level 1, di atasnya hanya
 * level 2. Yang menyetujui di HP tunduk pada aturan plafon yang sama dengan di halaman.
 *
 * Batal otorisasi PO (BatalOtorisasiHandler): sama dengan tombol batal di halaman - di atas plafon approver hanya
 * otorisasinya yang dihapus, dalam plafon PO-nya ikut dibatalkan (isbatal = 1). Penerima permintaan batal mengikuti
 * aturan nilai yang sama dengan permintaan otorisasi (approverLevel) dengan hak IsBatal.
 *
 * dbPO dipakai bersama PO Non Stock (pJasa = 1, href 'pononstock'): PurchaseOrderNonStockOtorisasi hanya mengganti
 * $pJasa, dan summary/isApproved/hasApproval dibatasi pJasa itu.
 */
class PurchaseOrderOtorisasi implements BatalOtorisasiHandler, BatalPreviewHandler, LeveledOtorisasiHandler, OtorisasiHandler
{
    /** Batas nilai PO (Rp) di atas mana dept head (KodeJab 03) hanya mengisi level 1, level 2 oleh direktur. */
    private const BATAS_DEPT_HEAD = 200000000;

    /** DBPO.pJasa dokumen yang ditangani handler ini: 0 = PO Stock, 1 = PO Non Stock. */
    protected int $pJasa = 0;

    /**
     * PO sampai 200 jt: permintaan ke approver level 1 (dalam plafon mereka menyetujui level 1 dan 2 sekaligus).
     * PO di atas 200 jt: hanya ke approver level 2. Nilai PO = SUM(NNetRp), sama dengan yang dipakai aturan plafon.
     */
    public function approverLevel(string $nobukti): int
    {
        return $this->nilaiPO($nobukti) > self::BATAS_DEPT_HEAD ? 2 : 1;
    }

    private function nilaiPO(string $nobukti): float
    {
        $res = DB::connection('SML')->selectOne('select sum(NNetRp) nnet from DBPODET where NoBukti = cast(? as varchar(50))', [$nobukti]);

        return (float) ($res->nnet ?? 0);
    }

    public function summary(string $nobukti): ?array
    {
        $header = DB::connection('SML')->select('
            select top 1 A.NoBukti, A.Tanggal, A.KodeSupp, C.NamaCustSupp, A.KodeVls, A.Kurs, A.NOSO, A.Keterangan,
                   isnull(A.IsOtorisasi1, 0) IsOtorisasi1, A.OtoUser1, isnull(A.IsOtorisasi2, 0) IsOtorisasi2, A.OtoUser2
            from DBPO A
            left outer join dbCustSupp C on C.KodeCustSupp = A.KodeSupp
            where A.NoBukti = cast(:nobukti as varchar(50)) and isnull(A.pJasa, 0) = :pjasa
        ', ['nobukti' => $nobukti, 'pjasa' => $this->pJasa]);

        if (! $header) {
            return null;
        }
        $h = $header[0];

        // Total per baris sama dengan POController::getDetail: round(SubTotal * Kurs, 2) + BYAngkut.
        $lines = DB::connection('SML')->select("
            select B.Urut, B.KodeBrg, case when isnull(B.NamaBrg, '') = '' then E.NamaBrg else B.NamaBrg end NamaBrg,
                   B.Qnt, B.Satuan, B.Harga, B.DISCTOT, round(B.SubTotal * A.Kurs, 2) + B.BYAngkut Total
            from DBPO A
            join DBPODET B on B.NoBukti = A.NoBukti
            left outer join dbBarang E on E.KodeBrg = B.KodeBrg
            where A.NoBukti = cast(:nobukti as varchar(50))
            order by B.Urut
        ", ['nobukti' => $nobukti]);

        $total = 0;
        $rows = [];
        foreach ($lines as $l) {
            $total += (float) $l->Total;
            $disc = (float) $l->DISCTOT;
            $rows[] = [
                'Uraian' => trim($l->KodeBrg.' '.$l->NamaBrg),
                'Jumlah' => 'Rp '.number_format((float) $l->Total, 2, ',', '.'),
                'Info' => number_format((float) $l->Qnt, 2, ',', '.').' '.$l->Satuan.' x Rp '.number_format((float) $l->Harga, 2, ',', '.')
                    .($disc > 0 ? ' (diskon Rp '.number_format($disc, 2, ',', '.').')' : ''),
            ];
        }

        $noso = trim((string) $h->NOSO);
        $header = [
            'No. Bukti' => $h->NoBukti,
            'Tanggal' => $h->Tanggal ? date('d-m-Y', strtotime($h->Tanggal)) : '',
            'Supplier' => trim($h->KodeSupp.' '.$h->NamaCustSupp),
        ];
        if ($noso !== '' && $noso !== '-') {
            $header['No. SO'] = $noso;
        }
        if ($h->KodeVls && (float) $h->Kurs !== 1.0) {
            $header['Mata uang'] = $h->KodeVls.' (kurs '.number_format((float) $h->Kurs, 2, ',', '.').')';
        }
        if (trim((string) $h->Keterangan) !== '') {
            $header['Keterangan'] = $h->Keterangan;
        }
        $header['Otorisasi Level 1'] = (int) $h->IsOtorisasi1 === 1 ? 'Sudah ('.$h->OtoUser1.')' : 'Belum';
        $header['Otorisasi Level 2'] = (int) $h->IsOtorisasi2 === 1 ? 'Sudah ('.$h->OtoUser2.')' : 'Belum';

        return [
            'header' => $header,
            'lines' => $rows,
            'total' => $total,
            'warnings' => $this->peringatanHarga($nobukti, trim((string) $h->NOSO)),
        ];
    }

    /** Lengkap = level 1 dan level 2 sama-sama terisi (sama dengan POController::updateOtorisasi). */
    public function isApproved(string $nobukti): bool
    {
        $res = $this->statusOtorisasi($nobukti);

        return $res && (int) $res->o1 === 1 && (int) $res->o2 === 1;
    }

    /** Level mana pun sudah terisi - syarat batal otorisasi di halaman (PO yang baru level 1 pun boleh dibatalkan). */
    public function hasApproval(string $nobukti): bool
    {
        $res = $this->statusOtorisasi($nobukti);

        return $res && ((int) $res->o1 === 1 || (int) $res->o2 === 1);
    }

    private function statusOtorisasi(string $nobukti): ?object
    {
        return DB::connection('SML')->selectOne('select top 1 isnull(IsOtorisasi1, 0) o1, isnull(IsOtorisasi2, 0) o2 from DBPO where NoBukti = cast(:nobukti as varchar(50)) and isnull(pJasa, 0) = :pjasa', ['nobukti' => $nobukti, 'pjasa' => $this->pJasa]);
    }

    public function approve(string $nobukti, string $username): int
    {
        return $this->approveWithRules($nobukti, $username)['result'] === 'refused' ? 0 : 1;
    }

    public function approveWithRules(string $nobukti, string $username): array
    {
        $refuse = fn (string $message, ?int $code = null) => ['result' => 'refused', 'message' => $message] + ($code ? ['code' => $code] : []);
        $db = DB::connection('SML');

        // Tanpa filter pJasa: POController::updateOtorisasi memilih handler dari pJasa dokumennya sendiri.
        $po = $db->selectOne('select top 1 * from DBPO where NoBukti = cast(? as varchar(50))', [$nobukti]);
        if (! $po) {
            return $refuse('PO '.$nobukti.' tidak ditemukan, mungkin sudah dihapus.');
        }
        $o1 = (int) ($po->IsOtorisasi1 ?? $po->isotorisasi1 ?? 0);
        $o2 = (int) ($po->IsOtorisasi2 ?? $po->isotorisasi2 ?? 0);
        $oto1User = $po->OtoUser1 ?? $po->otouser1 ?? '';
        if ($o1 === 1 && $o2 === 1) {
            return $refuse('PO '.$nobukti.' sudah diotorisasi.');
        }
        // PO yang sudah dibatalkan (batal otorisasi dalam plafon) tidak boleh diotorisasi lagi.
        if ((int) ($po->IsBatal ?? $po->isbatal ?? 0) === 1) {
            return $refuse('PO '.$nobukti.' sudah dibatalkan.');
        }

        // maxol, jabatan dan plafon dibaca dari tabel menu lama (dbmenu/dbflmenu) - sama dengan POController.
        $menu = $db->select("select * from dbmenu where href = 'purchaseorder'");
        $u = $this->infoPlafon($nobukti, $username);
        if (! $u || ! $menu) {
            return $refuse('Jabatan/plafon otorisasi PO untuk user '.$username.' belum diatur.');
        }
        $maxOL = $menu[0]->OL;
        $nnet = (float) $u->nnet;
        $tanggal = date('Y-m-d H:i:s');
        $log = fn (string $aktivitas) => app('App\Http\Controllers\GlobalController')->LoggingData($aktivitas, 'PO', $nobukti, '', 0, 'DBPO');

        $setLevel1 = fn () => $db->update('update dbpo set isOtorisasi1 = 1, maxol = :maxol, OtoUser1 = :username, TglOto1 = :tanggal where nobukti = cast(:nobukti as varchar(50))', ['maxol' => $maxOL, 'username' => $username, 'tanggal' => $tanggal, 'nobukti' => $nobukti]);
        // Level 2 dijaga isnull(IsOtorisasi2,0) = 0: dua approver yang menekan bersamaan, yang kedua dapat 0 baris.
        $setLevel2 = fn () => $db->update('update dbpo set isOtorisasi2 = 1, maxol = :maxol, OtoUser2 = :username, TglOto2 = :tanggal where nobukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi2, 0) = 0', ['maxol' => $maxOL, 'username' => $username, 'tanggal' => $tanggal, 'nobukti' => $nobukti]);

        // Level 1 sudah oleh user lain: jalur dept head (> 200 jt) tidak menimpa otorisasi pertama.
        if ($o1 === 1 && $nnet > self::BATAS_DEPT_HEAD && $u->KodeJab == '03') {
            return $refuse('PO '.$nobukti.' sudah diotorisasi level 1 oleh '.$oto1User.', menunggu otorisasi direktur.');
        }

        // Di atas 200 jt: dept head mengisi level 1 saja, level 2 menunggu direktur.
        if ($nnet > self::BATAS_DEPT_HEAD && $u->KodeJab == '03') {
            if ($db->update('update dbpo set isOtorisasi1 = 1, maxol = :maxol, OtoUser1 = :username, TglOto1 = :tanggal where nobukti = cast(:nobukti as varchar(50)) and isnull(IsOtorisasi1, 0) = 0', ['maxol' => $maxOL, 'username' => $username, 'tanggal' => $tanggal, 'nobukti' => $nobukti]) === 0) {
                return $refuse('PO '.$nobukti.' sudah diotorisasi level 1.');
            }
            $log('oto');

            return ['result' => 'partial', 'message' => 'Level 1 disetujui, PO menunggu otorisasi direktur (level 2)'];
        }

        // Direktur: mengisi level 2, hanya setelah level 1.
        if ($u->KodeJab == 'DIR') {
            if ($o1 === 0) {
                return $refuse('Diperlukan otorisasi 1 terlebih dahulu', 3);
            }
            if ($setLevel2() === 0) {
                return $refuse('PO '.$nobukti.' sudah diotorisasi.');
            }
            $log('oto2');

            return ['result' => 'done', 'message' => 'Berhasil otorisasi'];
        }

        if ($nnet > (float) $u->PlafonOtoPO) {
            return $refuse('Melebihi plafon', 2);
        }

        // Dalam plafon: level 1 dan 2 sekaligus oleh user ini.
        if ($setLevel2() === 0) {
            return $refuse('PO '.$nobukti.' sudah diotorisasi.');
        }
        $setLevel1();
        $log('oto');
        $log('oto2');

        return ['result' => 'done', 'message' => 'Berhasil otorisasi'];
    }

    /**
     * Jabatan, plafon otorisasi PO, nilai PO (Rp) dan level otorisasi pertama yang dimiliki user - dari tabel menu lama
     * (dbmenu/dbflmenu/DBJABATAN), sama dengan POController. Null kalau user belum punya baris/jabatan di sana.
     * Dipakai approveWithRules() dan batal otorisasi.
     */
    private function infoPlafon(string $nobukti, string $username): ?object
    {
        return DB::connection('SML')->selectOne("
            select b.USERID, c.KodeJab, isnull(d.PlafonOtoPO, 0) PlafonOtoPO, e.nnet,
                   case when b.IsOtorisasi1 = 1 then 1
                        when b.IsOtorisasi2 = 1 then 2
                        when b.IsOtorisasi3 = 1 then 3
                        when b.IsOtorisasi4 = 1 then 4
                        when b.IsOtorisasi5 = 1 then 5 end leveloto
            from dbmenu a
            join dbflmenu b on a.kodemenu = b.l1
            join DBFLPASS c on b.USERID = c.USERID
            join DBJABATAN d on c.KodeJab = d.KODEJAB
            left outer join (select nobukti, SUM(nnetrp) nnet from DBPODET where NOBUKTI = :nobukti group by nobukti) e on 1 = 1
            where a.href = 'purchaseorder' and b.USERID = :username
        ", ['nobukti' => $nobukti, 'username' => $username]);
    }

    public function cancelBlocker(string $nobukti): ?string
    {
        // PO yang sudah diterima gudang / sudah ada uang muka tidak boleh dibatalkan: sesudah batal otorisasi itemnya bisa
        // diedit/dihapus, padahal DBBELIDET/DBUMJUAL masih menunjuk PO ini.
        $db = DB::connection('SML');
        $terima = $db->selectOne('select top 1 NoBukti from DBBELIDET where NoPO = cast(? as varchar(50))', [$nobukti]);
        if ($terima) {
            return 'PO '.$nobukti.' sudah diterima di '.$terima->NoBukti.', otorisasi tidak bisa dibatalkan.';
        }
        $um = $db->selectOne('select top 1 NOBUKTI from DBUMJUAL where NOSO = cast(? as varchar(50)) and isnull(pBeli, 0) = 1 and isnull(IsBatal, 0) = 0', [$nobukti]);
        if ($um) {
            return 'PO '.$nobukti.' sudah dipakai Uang Muka Beli '.$um->NOBUKTI.', otorisasi tidak bisa dibatalkan.';
        }

        return null;
    }

    /**
     * Sama dengan POController::updateBatalOtorisasi (yang kini memanggil ini): nilai PO di atas plafon $username - hanya
     * otorisasi yang dihapus (level 1 saja kalau level pertama user level 1, selain itu keduanya); dalam plafon -
     * otorisasi dihapus dan PO ikut dibatalkan (isbatal = 1). Nilai PO memakai NNetRp, sama dengan aturan otorisasinya.
     */
    public function cancelApproval(string $nobukti, string $username, string $alasan): int
    {
        $u = $this->infoPlafon($nobukti, $username);
        if (! $u) {
            return 0;
        }

        $db = DB::connection('SML');
        $log = fn (string $aktivitas) => app('App\Http\Controllers\GlobalController')->LoggingData($aktivitas, 'PO', $nobukti, $alasan, 0, 'DBPO');
        $clear1 = "update dbpo set isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL";
        $clear2 = "update dbpo set isOtorisasi2 = 0, maxol = -1, OtoUser2 = '', TglOto2 = NULL";

        if ((float) $u->nnet > (float) $u->PlafonOtoPO) {
            if ((int) $u->leveloto === 1) {
                $res = $db->update($clear1.' where nobukti = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);
                $log('btloto');
            } else {
                $res = $db->update($clear1.' where nobukti = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);
                $res = $db->update($clear2.' where nobukti = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);
                $log('btloto');
                $log('btloto2');
            }

            return $res;
        }

        $batal = ', isbatal = 1, Userbatal = :username, tglbatal = getDate() where nobukti = cast(:nobukti as varchar(50))';
        $res = $db->update($clear1.$batal, ['username' => $username, 'nobukti' => $nobukti]);
        $res = $db->update($clear2.$batal, ['username' => $username, 'nobukti' => $nobukti]);
        $log('btloto');
        $log('btloto2');

        return $res;
    }

    public function cancelPreview(string $nobukti, string $username): ?string
    {
        $u = $this->infoPlafon($nobukti, $username);
        if (! $u) {
            return null;
        }

        $nilai = 'Rp '.number_format((float) $u->nnet, 2, ',', '.');
        $plafon = 'Rp '.number_format((float) $u->PlafonOtoPO, 2, ',', '.');

        return (float) $u->nnet > (float) $u->PlafonOtoPO
            ? 'Nilai PO '.$nilai.' di atas plafon Anda ('.$plafon.'): hanya otorisasinya yang dihapus, PO tetap bisa diedit dan diotorisasi ulang.'
            : 'Nilai PO '.$nilai.' dalam plafon Anda ('.$plafon.'): otorisasi dihapus DAN PO ikut dibatalkan (ditandai batal).';
    }

    /**
     * Cek harga PO terhadap harga SO untuk satu barang - SQL yang dulu ada di POController::cekHargaOto,
     * dipindah ke sini supaya halaman dan halaman approve HP memakai satu sumber.
     * $noso '' atau '-' = PO tidak berasal dari SO (hasilnya kosong).
     *
     * @return array<int, object>
     */
    public static function cekHargaBarang(string $noso, string $kodebrg, string $nopo): array
    {
        return DB::connection('SML')->select("declare @noSO varchaR(30),@KODEBRG VARCHAR(30)
    select @noSO= :noso ,@KODEBRG= :kodebrg

    SELECT XTABLE.kodebrg ,

    XTABLE.hrgmin hrgminso,XTABLE.HRGMIN + (XTABLE.HRGMIN * 1.00 ) hrgmaxso,
    XTABLE.xhrgpo
    , case when XTABLE.xhrgpo > XTABLE.HRGMIN then 'harga lebih besar dari pada harga min SO'

    	when XTABLE.xhrgpo >= (XTABLE.HRGMIN * 1.00 ) then 'harga lebih besar dari pada harga max SO (100%)'
    else
    'lanjut' End Ket
     FROM (

    select
    AA.kodebrg,AA.PPN * AA.KUrs  PPN ,AA.HrgNetto Hrg,B.tanggal,AA.nosat,
    (AA.harga * AA.Kurs )harga,
    (AA.harga * AA.Kurs ) - (AA.DiscTot * AA.kurs) -
    (case when AA.PPN=2 then  (AA.harga * AA.Kurs ) * 0.1 else 0 end )XHrgPO,
    AA.DiscTot * AA.kurs DiscTot,B.NOSO ,
    (
    select top 1
    case when AA.NOSAT=1 then
          case when A.NOSAT=1 then A.HRGNETTO - (case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end)
                when A.NOSAT=2 then (A.HRGNETTO /C.ISI2)- ((case when a.PPN=2 then (A.HRGNETTO/C.ISI2) * 0.10 else 0 end))
                when A.NOSAT=3 THEN (A.HRGNETTO /C.ISI3) - ((case when a.PPN=2 then (A.HRGNETTO/C.ISI3) * 0.10 else 0 end)) END * A.KURS
          when AA.NOSAT=2 then
           CASE WHEN A.NOSAT=2 THEN A.HRGNETTO - (case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end)
                WHEN A.NOSAT=1 THEN (A.HRGNETTO * C.ISI2)- ((case when a.PPN=2 then (A.HRGNETTO * C.ISI2) * 0.10 else 0 end))
                WHEN A.NOSAT=3 THEN (((A.HRGNETTO / C.ISI3)*C.ISI2)- ((case when a.PPN=2 then ((A.HRGNETTO / C.ISI3)*C.ISI2) * 0.10 else 0 end))) END * A.KURS

          when AA.NOSAT=3 then
           CASE WHEN A.NOSAT=3 THEN A.HRGNETTO - ((case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end))
                WHEN A.NOSAT=1 THEN (A.HRGNETTO * C.ISI3)- ((case when a.PPN=2 then (A.HRGNETTO * C.ISI3) * 0.10 else 0 end))
                WHEN A.NOSAT=2 THEN (((A.HRGNETTO/ C.ISI2)*c.ISI3) - ((case when a.PPN=2 then ((A.HRGNETTO / C.ISI2)*c.ISI3) * 0.10 else 0 end))) eND * A.KURS

    END
    from DBSODET A
    Left OUter join DBSO b on A.nobukti=B.NOBUKTI
    Left Outer join DBBARANG C on A.KODEBRG=C.KODEBRG
    Where A.NOBUKTI=@noSO and A.KODEBRG=@Kodebrg  --and A.kodegdg<>'G06'
    order by B.TANGGAL Desc
    ) HRGMIN

    from DbPODET AA
    left outer join dbPo B on AA.nobukti=B.nobukti
    where AA.nobukti = :nopo and isnull(B.noso,'') not in ('','-')
    ) XTABLE where XTABLE.KODEBRG=@KODEBRG
    ", ['noso' => $noso, 'kodebrg' => $kodebrg, 'nopo' => $nopo]);
    }

    /** Peringatan harga per barang ("Barang X - harga lebih besar ...") untuk halaman approve HP. */
    private function peringatanHarga(string $nobukti, string $noso): array
    {
        if ($noso === '' || $noso === '-') {
            return [];
        }

        $items = DB::connection('SML')->select('select distinct KodeBrg from DBPODET where NoBukti = cast(:nobukti as varchar(50))', ['nobukti' => $nobukti]);
        $warnings = [];
        foreach ($items as $it) {
            $r = self::cekHargaBarang($noso, $it->KodeBrg, $nobukti);
            if ($r && $r[0]->Ket !== 'lanjut') {
                $warnings[] = 'Barang '.$r[0]->kodebrg.' - '.$r[0]->Ket;
            }
        }

        return $warnings;
    }
}
