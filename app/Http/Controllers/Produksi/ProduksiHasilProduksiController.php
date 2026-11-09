<?php

namespace App\Http\Controllers\Produksi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\PenjagaTransaksi;
use App\Services\OtorisasiService;

// Hasil Produksi (DBMENUWEB 08007, href produksihasilproduksi) -- pola halaman
// purchasing/pembelianpermintaandebetnote: browse 1 tab + filter periode/otorisasi, form
// Tambah/Koreksi/Detail (item lewat Sp_HasilPrd), Otorisasi & Batal Otorisasi lewat
// App\Otorisasi\HasilProduksiOtorisasi (config/otorisasi.php, mendukung Minta Otorisasi).
//
// Semua hak diatur di Berkas > Set Pemakai (DBFLMENUWEB, menu 08007): HASACCESS (buka menu),
// ISTAMBAH, ISKOREKSI, ISHAPUS, IsOtorisasi1 (otorisasi), IsBatal (batal otorisasi).
//
// Perubahan DB yang dibutuhkan: docs/update-db-DBSMLNEW/produksi/24, 25, 26.
class ProduksiHasilProduksiController extends Controller
{
  use PenjagaTransaksi;

  // Kunci DBMENUWEB.href dan DBHEADERTABLE.href.
  private const HREF = 'produksihasilproduksi';

  // Rentang tanggal default = satu bulan penuh periode kerja user (sama seperti Debet Note).
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  public function index(Request $req) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', $req->path());
    if (!$akses || !$akses->HASACCESS) {
      return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($hpTglAwal, $hpTglAkhir) = $this->periodeRange($periode);

    return view('produksi.produksihasilproduksi', [
      "periode"    => $periode,
      "akses"      => $akses,
      "hpTglAwal"  => $hpTglAwal,
      "hpTglAkhir" => $hpTglAkhir,
    ]);
  }

  // Satu tabel gabungan (belum maupun sudah diotorisasi) -- penyaringan otorisasi dikerjakan
  // di browser lewat modal Filter, sama seperti Debet Note. Filter tahun/bulan pada query
  // desktop diganti rentang tanggal dari toolbar. Kolom dialiaskan supaya sama persis dengan
  // default di HeaderTableController@getHeaderTable cabang 'produksihasilproduksi'.
  public function loadAll(Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    try {
      $reqHeader = new Request(['href' => self::HREF]);
      $header = app('App\Http\Controllers\HeaderTableController')->getHeaderTable($reqHeader);

      $data = DB::connection('SML')->select("
        SELECT
            A.NOBUKTI,
            A.NOBUKTI                            as [No Bukti],
            Convert(varchar(10), A.TANGGAL, 23)  as [Tanggal],
            A.Shift                              as [Shift],
            A.NoSPK, A.KodePrs, B.Keterangan     as NamaProses,
            A.KodeMSN, A.KETERANGAN, A.MaxOL,
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
            Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                           Case when A.IsOtorisasi2=1 then 1 else 0 end+
                           Case when A.IsOtorisasi3=1 then 1 else 0 end+
                           Case when A.IsOtorisasi4=1 then 1 else 0 end+
                           Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                      else 1
                 end As Bit) as NeedOtorisasi
        FROM dbHasilPrd A
        LEFT OUTER JOIN DBPRoses B ON B.KodePrs = A.KodePrs
        WHERE A.TANGGAL >= :tglawal AND A.TANGGAL < DATEADD(day, 1, CAST(:tglakhir AS datetime))
          AND Isnull(A.Php02, 0) = 0
        ORDER BY A.NOBUKTI
      ", [
        "tglawal"  => $tglawal,
        "tglakhir" => $tglakhir,
      ]);

      return [
        "listData1"         => $data,
        "aliasordered"      => $header['aliasordered'],
        "headertableheader" => $header['headertableheader'],
        "isnumeric"         => $header['isnumeric'],
        "headertablevalue"  => $header['headertablevalue'],
        "isparsed"          => $header['isparsed'],
        "isshown"           => $header['isshown'],
        "desimal"           => $header['desimal'],
      ];
    } catch (\Throwable $e) {
      Log::error('ProduksiHasilProduksi loadAll: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Gagal memuat data Hasil Produksi: ' . $e->getMessage(),
      ], 500);
    }
  }

  // ===================== Form Tambah / Koreksi / Detail =====================

  // No. Bukti -- pola desktop HPDS/<NoUrut 5 digit>/<MMYY>/MM/<WIP|BJ>. WIP dan BJ punya urutan
  // sendiri-sendiri per bulan (pola sama dengan ProduksiSPKController::nextNoBukti()).
  // Sp_HasilPrd mengisi pBJ dari No Bukti (LIKE '%BJ%').
  public function nextNoBukti(Request $req) {
    $tipe = $req->tipe === 'BJ' ? 'BJ' : 'WIP';
    $tanggal = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->tanggal) ? $req->tanggal : date('Y-m-d');
    $mmyy = substr($tanggal, 5, 2) . substr($tanggal, 2, 2);
    $pattern = "HPDS/%/{$mmyy}/MM/{$tipe}";

    $row = DB::connection('SML')->selectOne(
      "SELECT MAX(CAST(NOURUT AS INT)) as MaxUrut FROM DBHASILPRD WHERE NOBUKTI LIKE :p AND ISNUMERIC(NOURUT) = 1",
      ['p' => $pattern]
    );
    $urut = str_pad(((int) ($row->MaxUrut ?? 0)) + 1, 5, '0', STR_PAD_LEFT);

    return response()->json([
      'nobukti' => "HPDS/{$urut}/{$mmyy}/MM/{$tipe}",
      'nourut'  => $urut,
    ]);
  }

  // Header + item satu No Bukti -- query detail/koreksi dari desktop. Satu baris per item;
  // header diambil dari baris pertama (kalau header belum punya item, B.* bernilai NULL).
  public function detail(Request $req) {
    return DB::connection('SML')->select("
      select A.NOBUKTI, A.NOURUT, A.TANGGAL,
             A.KodePrs, P.Keterangan NamaPrs, A.KETERANGAN,
             A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
             A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
             A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
             A.NoJurnal, A.NoUrutJurnal, A.TglJurnal, A.CetakKe, A.MaxOL, B.Qnt, B.Isi, A.Shift,
             B.URUT, B.NoSPK, B.KODEBRG, B0.NAMABRG,
             Case when B.Nosat=1 then B0.sat1 when B.nosat=2 then B0.sat2 When B.Nosat=3 then B0.sat3 End Satx,
             A.KodeMSn, A.NoSPK as NoSPKHeader, D.ket NmMesin, B.QntReject, B.UrutSpk,
             B.NOSAT, B.KodeGdg, G.NAMA as NamaGdg, B.IsAv
      from DBHASILPRD A
      left outer join DBHASILPRDDET B on B.NOBUKTI=A.NOBUKTI
      Left OUter join DBBARANG B0 on B0.KODEBRG=B.KODEBRG
      left outer join DBPRoses P on P.KodePrs=A.KodePrs
      left outer join DBSPK C on C.NOBUKTI = B.NoSPK
      left outer join DBmesin D on A.KodeMsn=D.KodeMSN
      left outer join DBGUDANG G on G.KODEGDG = B.KodeGdg
      where A.NOBUKTI = :nobukti
      order by A.NOBUKTI, B.URUT
    ", ['nobukti' => (string) $req->nobukti]);
  }

  // Browse No. SPK (header) -- query desktop: baris DBSPKJD yang masih punya sisa
  // (Qnt*Isi dikurangi hasil produksi + reject yang sudah diinput per NoSPK+UrutSPK).
  // Mesin diambil dari header SPK (DBSPK.KodeMSN -> DBMesin) supaya Kode/Nama Mesin di form
  // terisi otomatis. Parameter nobukti opsional: dipakai saat No SPK diketik manual.
  public function listSPK(Request $req) {
    $filter = '';
    $bind = [];
    if ($req->filled('nobukti')) {
      $filter = ' and a.NOBUKTI = :nobukti';
      $bind['nobukti'] = (string) $req->nobukti;
    }

    return DB::connection('SML')->select("
      Select a.nobukti, a.urut, a.kodebrg, D.namaBrg, (a.QNT * a.ISI) - isnull(b.HPD1,0) sisa,
             C.KodeMSN, M.Ket as NamaMesin
      from DBSPKJD a
      left outer join (select a.NoSPK, b.UrutSPK, SUM((QNT + B.QntReject) * ISI) HPD1
                       from DBHASILPRD a
                       left outer join DBHASILPRDDET b on a.NOBUKTI=b.NOBUKTI
                       group by a.NoSPK, b.UrutSPK
                      ) b on a.NOBUKTI=b.NoSPK and a.URUT=b.UrutSPK
      left outer join dbSPK C on A.nobukti=C.nobukti
      Left outer Join Dbbarang D on A.kodebrg=D.Kodebrg
      left outer join DBMesin M on M.KodeMsn = C.KodeMSN
      where (a.QNT * a.ISI) - isnull(b.HPD1,0) > 0 {$filter}
      order by A.nobukti, a.urut
    ", $bind);
  }

  // Browse Kode Barang, avalan DICENTANG -- query desktop: semua barang aktif (Sisa selalu 0,
  // NoSat 1). 18 ribuan baris, jadi server-side DataTables (ROW_NUMBER, pola
  // ProduksiSPKController::loadAll() -- SQL Server 2008 tidak dukung OFFSET/FETCH).
  // Pencarian pola pembelianpermintaanagen: hanya mencari kalau ada kata kunci (kode / nama),
  // dibatasi 200 baris. No Bukti diisi No SPK di header (permintaan user); Sisa tetap 0
  // sesuai query desktop.
  public function listBarangAvalan(Request $req) {
    $search = trim((string) $req->input('search'));
    if ($search === '') {
      return [];
    }

    return DB::connection('SML')->select("
      Select TOP 200 :nospk as NObukti, 0 as Urut, A.KODEBRG, A.NAMABRG, 0 as Sisa, 1 as NOsat,
             A.Isi1 as ISi, A.Isi2 as ISi2, A.Isi3 as ISi3
      from dbBarang A
      where A.isaktif = 1 and (A.KODEBRG like :f1 or A.NAMABRG like :f2)
      order by case when A.KODEBRG = :persis then 0 else 1 end, A.KODEBRG
    ", [
      'nospk'  => (string) $req->input('nospk'),
      'f1'     => "%{$search}%",
      'f2'     => "%{$search}%",
      'persis' => $search,
    ]);
  }

  // Browse Kode Barang, avalan TIDAK dicentang -- query desktop: baris DBSPKJD milik No SPK di
  // header yang masih punya sisa. Sama dengan listSPK() tetapi wajib per No SPK.
  public function listBarangSPK(Request $req) {
    if (!$req->filled('nospk')) {
      return [];
    }
    return $this->listSPK(new Request(['nobukti' => (string) $req->nospk]));
  }

  // Sisa SPK (satuan dasar = Qnt*Isi) untuk satu baris DBSPKJD, tidak menghitung item
  // Hasil Produksi yang sedang dikoreksi (nobukti + urut) supaya nilainya bisa diganti.
  private function sisaSPK($nospk, $urutspk, $nobukti, $urut) {
    $row = DB::connection('SML')->selectOne("
      SELECT a.KODEBRG,
             (a.QNT * a.ISI) - ISNULL((
               SELECT SUM((d.QNT + ISNULL(d.QntReject, 0)) * d.ISI)
               FROM DBHASILPRDDET d
               WHERE d.NoSPK = a.NOBUKTI AND d.UrutSPK = a.URUT
                 AND NOT (d.NOBUKTI = :nobukti AND d.URUT = :urut)
             ), 0) as Sisa
      FROM DBSPKJD a
      WHERE a.NOBUKTI = :nospk AND a.URUT = :urutspk
    ", ['nobukti' => $nobukti, 'urut' => (int) $urut, 'nospk' => $nospk, 'urutspk' => (int) $urutspk]);
    return $row;
  }

  // Dropdown Gudang di panel item -- DBGUDANG DBSMLNEW. Kolom ditulis eksplisit, bukan
  // select *: kolom MyID (timestamp/biner) membuat response JSON gagal di-encode.
  public function listGudang() {
    return DB::connection('SML')->select('SELECT KODEGDG, NAMA FROM DBGUDANG ORDER BY KODEGDG');
  }

  private function angka($nilai) {
    $bersih = str_replace(',', '', trim((string) $nilai));
    return is_numeric($bersih) ? (float) $bersih : 0;
  }

  private function periodeTerkunci($tanggal) {
    $lock = DB::connection('SML')->select(
      'select 1 as x from dblockperiode where bulan = :bulan and tahun = :tahun',
      ['bulan' => (int) substr($tanggal, 5, 2), 'tahun' => (int) substr($tanggal, 0, 4)]
    );
    return count($lock) > 0;
  }

  private function sudahOtorisasi($nobukti) {
    $row = DB::connection('SML')->selectOne(
      'select IsOtorisasi1 from DBHASILPRD where NOBUKTI = :nobukti',
      ['nobukti' => $nobukti]
    );
    return $row && (int) $row->IsOtorisasi1 === 1;
  }

  // Simpan item (Choice I = tambah, U = koreksi) lewat Sp_HasilPrd. Header dbHasilPrd dibuat
  // oleh SP saat item pertama disimpan (if not exists). Satuan/Isi dihitung SP dari NoSat.
  // Sp_HasilPrd (versi DBSMLNEW, skrip 25) punya Begin/Commit tran sendiri -- JANGAN dibungkus
  // DB::transaction (jumlah transaksi jadi tidak seimbang, error 266).
  public function spItem(Request $req) {
    $req->validate([
      'choice'   => 'required|in:I,U',
      'nobukti'  => 'required|string|max:30',
      'nourut'   => 'nullable|string|max:10',
      'tanggal'  => 'required|date_format:Y-m-d',
      'kodebrg'  => 'required|string|max:25',
      'kodegdg'  => 'required|string|max:15',
      'nosat'    => 'required|integer|in:1,2,3',
      'shift'    => 'nullable|integer|in:0,1,2,3',
      'nospk'    => 'nullable|string|max:30',
      'kodemsn'  => 'nullable|string|max:20',
      'urut'     => 'nullable|integer',
      'urutspk'  => 'nullable|integer',
      'isav'     => 'nullable|in:0,1',
    ]);

    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', self::HREF);
    $kolomAkses = $req->choice === 'I' ? 'ISTAMBAH' : 'ISKOREKSI';
    if (!$akses || !$akses->HASACCESS || !(int) $akses->{$kolomAkses}) {
      return response()->json(['success' => false, 'message' => 'Tidak punya akses'], 403);
    }
    if ($this->periodeTerkunci($req->tanggal)) {
      return response()->json(['success' => false, 'message' => 'Periode sudah dikunci'], 422);
    }
    if ($this->sudahOtorisasi($req->nobukti)) {
      return response()->json(['success' => false, 'message' => 'Data sudah diotorisasi, tidak bisa diubah'], 422);
    }

    $qnt = $this->angka($req->qnt);
    $reject = $this->angka($req->reject);
    if ($qnt <= 0 && $reject <= 0) {
      return response()->json(['success' => false, 'message' => 'Qnt atau Reject harus diisi'], 422);
    }

    $c = DB::connection('SML');

    // Koreksi item: hanya Qty & Reject yang boleh berubah. Barang, gudang, no satuan, avalan dan
    // baris SPK diambil dari item yang tersimpan - nilai kiriman browser untuk kolom itu diabaikan.
    if ($req->choice === 'U') {
      $lama = $c->selectOne(
        'select KODEBRG, KodeGdg, NOSAT, isnull(IsAv, 0) IsAv, isnull(UrutSPK, 0) UrutSPK, NoSPK
         from DBHASILPRDDET where NOBUKTI = :nobukti and URUT = :urut',
        ['nobukti' => $req->nobukti, 'urut' => (int) $req->urut]
      );
      if (!$lama) {
        return response()->json(['success' => false, 'message' => 'Item tidak ditemukan, mungkin sudah dihapus'], 404);
      }
      $req->merge([
        'kodebrg' => $lama->KODEBRG,
        'kodegdg' => $lama->KodeGdg,
        'nosat'   => (int) $lama->NOSAT ?: 1,
        'isav'    => (int) $lama->IsAv,
        'urutspk' => (int) $lama->UrutSPK,
        'nospk'   => (string) $lama->NoSPK,
      ]);
    }

    if (!$c->selectOne('select KODEBRG from DBBARANG where KODEBRG = :k', ['k' => $req->kodebrg])) {
      return response()->json(['success' => false, 'message' => 'Kode barang ' . $req->kodebrg . ' tidak ditemukan'], 422);
    }
    if (!$c->selectOne('select KODEGDG from DBGUDANG where KODEGDG = :k', ['k' => $req->kodegdg])) {
      return response()->json(['success' => false, 'message' => 'Kode gudang ' . $req->kodegdg . ' tidak ditemukan'], 422);
    }

    // Tanpa avalan: barang harus dari baris SPK di header, No Satuan selalu 1, dan
    // (Qty + Reject) x Isi tidak boleh melebihi sisa SPK. Avalan tidak dibatasi (permintaan user).
    $isav = (int) $req->isav;
    $nosat = (int) $req->nosat;
    $urutspk = (int) $req->urutspk;
    if ($isav === 0) {
      $nosat = 1;
      if (!$req->filled('nospk') || $urutspk <= 0) {
        return response()->json(['success' => false, 'message' => 'Pilih No SPK di header dan pilih barang dari SPK itu'], 422);
      }
      $spk = $this->sisaSPK((string) $req->nospk, $urutspk, $req->nobukti, $req->choice === 'U' ? (int) $req->urut : 0);
      if (!$spk || $spk->KODEBRG !== $req->kodebrg) {
        return response()->json(['success' => false, 'message' => 'Barang tidak sesuai dengan SPK ' . $req->nospk], 422);
      }
      $isi = (float) ($c->selectOne('select ISI1 from DBBARANG where KODEBRG = :k', ['k' => $req->kodebrg])->ISI1 ?? 1) ?: 1;
      $sisa = (float) $spk->Sisa;
      if (($qnt + $reject) * $isi > $sisa + 0.0001) {
        return response()->json([
          'success' => false,
          'message' => 'Qty + Reject (' . number_format($qnt + $reject, 2) . ') melebihi sisa SPK (' . number_format($sisa / $isi, 2) . ')',
        ], 422);
      }
    }

    try {
      $c->statement(
        'SET NOCOUNT ON; EXEC Sp_HasilPrd @Choice = ?, @NoBukti = ?, @NoUrut = ?, @Tanggal = ?, @KodeGdg = ?,
           @Keterangan = ?, @KodeBrg = ?, @Qnt = ?, @NoSat = ?, @NoSPK = ?, @urutSPK = ?, @Urut = ?, @Shift = ?,
           @KodeMSn = ?, @Reject = ?, @IsAV = ?',
        [
          $req->choice,
          $req->nobukti,
          (string) $req->nourut,
          $req->tanggal,
          $req->kodegdg,
          '',
          $req->kodebrg,
          $qnt,
          $nosat,
          (string) $req->nospk,
          // Urut baris DBSPKJD barang yang dipilih (browse Kode Barang tanpa avalan) - dipakai
          // query sisa SPK (join NoSPK + UrutSPK). Avalan = 0.
          $isav === 0 ? $urutspk : 0,
          (int) $req->urut,
          (int) $req->shift,
          (string) $req->kodemsn,
          $reject,
          $isav,
        ]
      );

      // Sp_HasilPrd Choice='U' tidak menyentuh IsAv dan UrutSPK -- disimpan langsung di sini
      // (pola sama dengan ProduksiSPKController::spSPK() untuk KodePrs).
      if ($req->choice === 'U') {
        $c->update(
          'UPDATE DBHASILPRDDET SET IsAv = :isav, UrutSPK = :urutspk WHERE NOBUKTI = :nobukti AND URUT = :urut',
          ['isav' => $isav, 'urutspk' => $isav === 0 ? $urutspk : 0, 'nobukti' => $req->nobukti, 'urut' => (int) $req->urut]
        );
      }

      return response()->json(['success' => true, 'message' => 'Item tersimpan']);
    } catch (\Throwable $e) {
      Log::error('ProduksiHasilProduksi spItem: ' . $e->getMessage());
      return response()->json(['success' => false, 'message' => 'Gagal menyimpan item: ' . $e->getMessage()], 500);
    }
  }

  // ===================== Otorisasi / Batal Otorisasi =====================
  // Pola PembelianPermintaanDebetNoteController. Hak dicek di server lewat OtorisasiService
  // (DBFLMENUWEB menu 08007: HASACCESS + IsOtorisasi1 / IsBatal). User tanpa hak memakai
  // "Minta Otorisasi" (route otorisasirequest) dari JS.

  public function updateOtorisasi(Request $req, OtorisasiService $oto) {
    $username = \Auth::user()->username;
    if (!$oto->canApprove($username, self::HREF)) {
      return $this->tolak('Anda tidak berhak otorisasi hasil produksi');
    }
    $hp = $this->headerDokumen('DBHASILPRD', $req->nobukti);
    if (!$hp) {
      return $this->tolak('Hasil produksi ' . $req->nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
    }
    if ((int) $hp->isotorisasi1 === 1) {
      return $this->tolak('Hasil produksi ' . $req->nobukti . ' sudah diotorisasi oleh ' . $hp->otouser1 . '.');
    }

    try {
      $res = DB::connection('SML')->transaction(fn () => $oto->handler(self::HREF)->approve($req->nobukti, $username));
    } catch (\DomainException $e) {
      return $this->tolak($e->getMessage());
    }
    if ($res > 0) {
      // Tutup permintaan push yang masih pending + kabari peminta.
      $oto->closePending(self::HREF, $req->nobukti, $username);
    }
    return response()->json(['success' => $res > 0]);
  }

  // Alasan wajib - dicatat ke dbLogFile ('btloto') oleh handler.
  public function updateBatalOtorisasi(Request $req, OtorisasiService $oto) {
    $username = \Auth::user()->username;
    if (!$oto->canCancel($username, self::HREF)) {
      return response()->json(['message' => 'No access'], 403);
    }
    if (trim((string) $req->pket) === '') {
      return response()->json(['message' => 'Keterangan harus diisi'], 422);
    }
    $hp = $this->headerDokumen('DBHASILPRD', $req->nobukti);
    if (!$hp) {
      return $this->tolak('Hasil produksi ' . $req->nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
    }
    if ((int) $hp->isotorisasi1 !== 1) {
      return $this->tolak('Hasil produksi ' . $req->nobukti . ' belum diotorisasi.');
    }
    $handler = $oto->handler(self::HREF);
    if ($blocker = $handler->cancelBlocker($req->nobukti)) {
      return $this->tolak($blocker);
    }

    $res = DB::connection('SML')->transaction(fn () => $handler->cancelApproval($req->nobukti, $username, trim((string) $req->pket)));
    if ($res > 0) {
      $oto->closePending(self::HREF, $req->nobukti, $username, OtorisasiService::BATAL);
    }
    return $res;
  }

  // Hapus satu item lewat Sp_HasilPrd Choice D. SP ikut menghapus header kalau item terakhir.
  public function spHapusItem(Request $req) {
    $req->validate([
      'nobukti' => 'required|string|max:30',
      'urut'    => 'required|integer',
    ]);

    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', self::HREF);
    if (!$akses || !$akses->HASACCESS || !(int) $akses->ISHAPUS) {
      return response()->json(['success' => false, 'message' => 'Tidak punya akses hapus'], 403);
    }

    $c = DB::connection('SML');
    $header = $c->selectOne('select convert(varchar(10), TANGGAL, 23) as Tanggal from DBHASILPRD where NOBUKTI = :n', ['n' => $req->nobukti]);
    if (!$header) {
      return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
    }
    if ($this->periodeTerkunci($header->Tanggal)) {
      return response()->json(['success' => false, 'message' => 'Periode sudah dikunci'], 422);
    }
    if ($this->sudahOtorisasi($req->nobukti)) {
      return response()->json(['success' => false, 'message' => 'Data sudah diotorisasi, tidak bisa dihapus'], 422);
    }

    try {
      $c->statement(
        'SET NOCOUNT ON; EXEC Sp_HasilPrd @Choice = ?, @NoBukti = ?, @Urut = ?, @KodeMSn = ?, @Reject = ?',
        ['D', $req->nobukti, (int) $req->urut, '', 0]
      );
      $sisa = $c->selectOne('select count(*) as j from DBHASILPRD where NOBUKTI = :n', ['n' => $req->nobukti])->j;

      return response()->json(['success' => true, 'message' => 'Item dihapus', 'headerAda' => (int) $sisa > 0]);
    } catch (\Throwable $e) {
      Log::error('ProduksiHasilProduksi spHapusItem: ' . $e->getMessage());
      return response()->json(['success' => false, 'message' => 'Gagal menghapus item: ' . $e->getMessage()], 500);
    }
  }
}
