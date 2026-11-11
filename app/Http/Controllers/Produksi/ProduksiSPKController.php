<?php

namespace App\Http\Controllers\Produksi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Browser SPK (Surat Perintah Kerja) -- 1 tabel server-side (gaya SO/notareturpenjualan:
// po-list-page + toolbarMaster + data-table), BEDA dari produksibom yang 4 panel. Baru tahap
// browse (Read) + Detail (buttonDetail(), modal item dbSPKDet) -- Tambah/Koreksi/Cetak/
// Otorisasi/Batal Otorisasi masih placeholder ('belum tersedia') di blade, menunggu
// spesifikasi dari senior (terutama buttonAdd()).
class ProduksiSPKController extends Controller
{
  public function index(Request $req) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    return view('produksi.produksispk', [
      "periode" => $periode,
      "akses" => $akses,
    ]);
  }

  // Panel "Daftar SPK" -- server-side DataTables (ROW_NUMBER()/COUNT() OVER(), pola sama
  // dengan ProduksiBomController::loadAllBarang() -- compat level DBSMLNEW tidak dukung
  // OFFSET/FETCH). DBSPK = header SPK (satu baris per NOBUKTI, sekaligus menyimpan item
  // OUTPUT yang diproduksi -- KodeBrg/Qnt/KodeBOM-nya sendiri, dicek lewat
  // INFORMATION_SCHEMA.COLUMNS, bukan cuma header murni). DBSPK TIDAK punya kolom KodeMesin
  // sama sekali -- mesin ditentukan PER BARIS dbSPKDet lewat KodePrs -> DBMesin.KodePrs
  // (dicek juga lewat INFORMATION_SCHEMA), jadi untuk listing satu-baris-per-SPK ini diambil
  // SATU baris dbSPKDet representatif per NOBUKTI (Urut terkecil) buat resolve KodeMsn/Ket.
  // Detail PER BARIS (termasuk mesin per baris, bisa beda-beda) ada di listDetail() di bawah.
  public function loadAll(Request $req) {
    $search = $req->input('search.value');
    $start = (int) $req->input('start', 0);
    $length = (int) $req->input('length', 10);

    // Filter status otorisasi (tombol Filter hijau di toolbar): '' = semua, '0' = belum
    // diotorisasi, '1' = sudah diotorisasi (DBSPK.IsOtorisasi1; NULL dianggap belum). Digabung dengan
    // kotak cari -- pencarian dibungkus kurung supaya OR-nya tidak lolos dari filter.
    $kondisi = [];
    $filterBindings = [];
    if (!empty($search)) {
      $kondisi[] = '(a.NOBUKTI LIKE :f1 OR msn.KodeMsn LIKE :f2 OR msn.Ket LIKE :f3)';
      $filterBindings = ['f1' => "%{$search}%", 'f2' => "%{$search}%", 'f3' => "%{$search}%"];
    }
    if ($req->input('otorisasi') === '0') {
      $kondisi[] = 'ISNULL(a.IsOtorisasi1, 0) = 0';
    } elseif ($req->input('otorisasi') === '1') {
      $kondisi[] = 'ISNULL(a.IsOtorisasi1, 0) = 1';
    }
    $where = $kondisi ? 'WHERE ' . implode(' AND ', $kondisi) : '';

    // Mesin sekarang disimpan di header (DBSPK.KodeMSN, diisi sp_SPKJD), jadi di-join langsung
    // dari header -- bukan lagi lewat KodePrs baris dbSPKDet (baris Bahan Baku yang dibuat
    // otomatis oleh sp_SPKJD dari DBBOMDET tidak membawa KodePrs).
    $baseSql = "
      FROM DBSPK a
      LEFT OUTER JOIN DBMesin msn ON msn.KodeMsn = a.KodeMSN
      {$where}
    ";

    $total = DB::connection('SML')->selectOne("SELECT COUNT(DISTINCT a.NOBUKTI) as Total {$baseSql}", $filterBindings)->Total;

    if ($length >= 0) {
      $data = DB::connection('SML')->select("
        SELECT NOBUKTI, TANGGAL, KodeMsn, Ket, IsOtorisasi1, OtoUser1, TglOto1 FROM (
          SELECT a.NOBUKTI, a.TANGGAL, msn.KodeMsn, msn.Ket, a.IsOtorisasi1, a.OtoUser1, a.TglOto1,
                 ROW_NUMBER() OVER (ORDER BY a.NOBUKTI ASC) as RowNum
          {$baseSql}
        ) sub
        WHERE RowNum BETWEEN :start AND :end
        ORDER BY RowNum
      ", array_merge($filterBindings, ['start' => $start + 1, 'end' => $start + $length]));
    } else {
      $data = DB::connection('SML')->select("
        SELECT a.NOBUKTI, a.TANGGAL, msn.KodeMsn, msn.Ket, a.IsOtorisasi1, a.OtoUser1, a.TglOto1 {$baseSql} ORDER BY a.NOBUKTI ASC
      ", $filterBindings);
    }

    return response()->json([
      "draw" => intval($req->input('draw')),
      "recordsTotal" => $total,
      "recordsFiltered" => $total,
      "data" => $data,
    ]);
  }

  // Modal "Detail" (buttonDetail() di produksispk.blade.php) -- item-item satu SPK
  // (dbSPKDet), query dari senior (join DBSPK buat Tanggal, dbBarang buat NamaBrg). Mesin DI
  // SINI diambil per baris lewat KodePrs milik baris itu sendiri (bisa beda-beda per baris,
  // BEDA dari loadAll() yang cuma ambil satu representatif per SPK buat listing).
  public function listDetail(Request $req) {
    return DB::connection('SML')->select(
      "SELECT B.NOBUKTI, a.TANGGAL, B.URUT, B.URUTSPK, B.KODEBRG, H.NamaBrg, B.QNT, COALESCE(NULLIF(B.SATUAN, ''), H.SAT1) as SATUAN, B.ISI,
              B.KodePrs, pr.Keterangan as NamaPrs,
              msn.KodeMsn, msn.Ket, 1 as Tipe, 'Bahan Baku' as KetTipe
       FROM dbSPKDet B
       LEFT OUTER JOIN DBSPK a ON a.NOBUKTI = B.NOBUKTI
       LEFT OUTER JOIN DBMesin msn ON msn.KodeMsn = a.KodeMSN
       LEFT OUTER JOIN DBBARANG H ON H.KODEBRG = B.KODEBRG
       LEFT OUTER JOIN DBPRoses pr ON pr.KodePrs = B.KodePrs
       WHERE B.NOBUKTI = :nobukti
       ORDER BY B.URUTSPK, B.KodePrs, B.URUT",
      ['nobukti' => $req->nobukti]
    );
  }

  // ===================== Tambah SPK (buttonAdd(), modalAddSPK.blade.php) =====================

  // No. Bukti -- pola SPKS/<NoUrut 5 digit>/<MMYY>/MM/<WIP|BJ>. WIP dan BJ punya urutan
  // SENDIRI-SENDIRI (lihat screenshot: SPKS/00001..00005/.../WIP berdampingan dengan
  // SPKS/00002/.../BJ) -- NoUrut di-scan dari DBSPK.NoBukti yang polanya cocok (bulan+tahun+
  // tipe yang sama), MAX(NoUrut)+1, pola sama dengan ProduksiBomController::nextKodeBom().
  // DBSPK.NoUrut sendiri (bukan NoBukti) yang jadi sumber MAX -- app yang menghitung &
  // mengirim NoUrut ke sp_SPK/Sp_SPKBJ, SP-nya sendiri tidak menghitung ulang.
  public function nextNoBukti(Request $req) {
    $tipe = $req->tipe === 'BJ' ? 'BJ' : 'WIP';
    $bulan = str_pad((int) $req->bulan, 2, '0', STR_PAD_LEFT);
    $tahun2 = str_pad(((int) $req->tahun) % 100, 2, '0', STR_PAD_LEFT);
    $mmyy = $bulan . $tahun2;
    $pattern = "SPKS/%/{$mmyy}/MM/{$tipe}";

    $row = DB::connection('SML')->selectOne(
      "SELECT MAX(CAST(NoUrut AS INT)) as MaxUrut FROM DBSPK WHERE NoBukti LIKE :p",
      ['p' => $pattern]
    );
    $urut = str_pad(((int) ($row->MaxUrut ?? 0)) + 1, 5, '0', STR_PAD_LEFT);

    return response()->json([
      'nobukti' => "SPKS/{$urut}/{$mmyy}/MM/{$tipe}",
      'nourut' => $urut,
    ]);
  }

  // Dropdown "Kode Mesin" (header Tambah SPK) -- query dari senior. Cuma 3 baris di DB ini,
  // jadi <select> biasa (lihat spkIsiMesin() di blade), bukan picker-kas modal kaya Kode BOM.
  // BELUM dikirim ke server sama sekali -- satu-satunya tempat KodeMsn beneran disimpan ada
  // di sp_SPKPrs -> DBJADWALPRD, yang juga butuh Tanggal Proses/Jam Awal/Jam Akhir/
  // Keterangan/KodePrs (field yang tidak ada di form ini) -- menunggu klarifikasi alur
  // penjadwalan sebelum dipersist.
  public function listMesin() {
    return DB::connection('SML')->select('SELECT KodeMsn, Ket FROM DBMesin ORDER BY KodeMsn');
  }

  // Alur grid Barang Jadi di Tambah SPK: Kode Barang dipilih DULU (picker di bawah), baru Kode
  // BOM milik barang itu (listBomByBarang()). Pencocokan BOM ke barang lewat kolom
  // DBBOM.KodeBrg ATAU awalan KodeBOM (<KodeBrg>-NN) -- ada baris DBBOM lama yang KodeBrg-nya
  // NULL (mis. 10102011780001-02), sama alasannya dengan ProduksiBomController::nextKodeBom().
  // Cuma BOM yang sudah punya proses (DBBOMProses) yang dihitung, sama dengan syarat picker
  // Kode BOM sebelumnya.
  private const SQL_BOM_BARANG = "(b.KodeBrg = v.KODEBRG OR LEFT(b.KodeBOM, LEN(v.KODEBRG) + 1) = v.KODEBRG + '-')
       AND EXISTS (SELECT 1 FROM DBBOMProses p WHERE p.KodeBOM = b.KodeBOM)";

  // Picker "Kode Barang" (Barang Jadi) -- server-side DataTables (ROW_NUMBER()/COUNT() OVER(),
  // compat level DBSMLNEW tidak dukung OFFSET/FETCH). Daftar barang Jadi/WIP aktif (filter
  // IsBarang sama dengan panel kiri modul BOM) yang punya minimal satu BOM berproses -- barang
  // tanpa BOM tidak ditampilkan karena tidak akan punya pilihan Kode BOM.
  public function listBarangJadiBrowse(Request $req) {
    $search = $req->input('search.value');
    $start = (int) $req->input('start', 0);
    $length = (int) $req->input('length', 10);

    $where = "(v.IsBarang IN (2, 4) OR (v.IsBarang = 3 AND v.IsJasa = 0))
              AND v.ISAKTIF = 1
              AND EXISTS (SELECT 1 FROM DBBOM b WHERE " . self::SQL_BOM_BARANG . ")";
    $bindings = [];
    if (!empty($search)) {
      $where .= ' AND (v.KODEBRG LIKE ? OR v.NAMABRG LIKE ?)';
      $bindings = ["%{$search}%", "%{$search}%"];
    }

    $range = '';
    $rangeBindings = [];
    if ($length >= 0) {
      $range = 'WHERE RowNum BETWEEN ? AND ?';
      $rangeBindings = [$start + 1, $start + $length];
    }

    $data = DB::connection('SML')->select(
      "SELECT KodeBrg, NamaBrg, Sat1, TotalCount FROM (
         SELECT v.KODEBRG as KodeBrg, v.NAMABRG as NamaBrg, v.SAT1 as Sat1,
                ROW_NUMBER() OVER (ORDER BY v.KODEBRG) as RowNum,
                COUNT(*) OVER () as TotalCount
         FROM vwBarang v
         WHERE {$where}
       ) sub
       {$range}
       ORDER BY RowNum",
      array_merge($bindings, $rangeBindings)
    );

    $total = $data ? (int) $data[0]->TotalCount : 0;

    return response()->json([
      "draw" => intval($req->input('draw')),
      "recordsTotal" => $total,
      "recordsFiltered" => $total,
      "data" => $data,
    ]);
  }

  // Picker "Kode BOM" -- BOM berproses milik SATU Kode Barang yang sudah dipilih di atas.
  public function listBomByBarang(Request $req) {
    return DB::connection('SML')->select(
      "SELECT b.KodeBOM, b.IsDefault
       FROM DBBOM b
       JOIN (SELECT ? AS KODEBRG) v ON 1 = 1
       WHERE " . self::SQL_BOM_BARANG . "
       ORDER BY b.KodeBOM",
      [$req->kodebrg]
    );
  }

  // Panel "Barang Jadi" (DBSPKJD) -- daftar output per SPK. KodeBOM per baris (DBSPKJD.kodeBOM).
  public function listBJ(Request $req) {
    return DB::connection('SML')->select(
      "SELECT B.NoBukti, B.Urut, B.KodeBrg, H.NamaBrg, B.Qnt, B.Satuan, B.Isi, B.KodeBOM
       FROM DBSPKJD B
       LEFT OUTER JOIN DBBARANG H ON H.KODEBRG = B.KodeBrg
       WHERE B.NoBukti = :nobukti
       ORDER BY B.Urut",
      ['nobukti' => $req->nobukti]
    );
  }

  // Header satu SPK -- dipakai buttonEdit() (Koreksi SPK) buat mengisi ulang Tanggal/No. Urut/
  // Kode Mesin sebelum modal Tambah SPK dibuka dalam mode edit.
  public function header(Request $req) {
    $row = DB::connection('SML')->selectOne(
      "SELECT a.NOBUKTI, a.NoUrut, a.TANGGAL, a.KodeMSN, msn.Ket
       FROM DBSPK a
       LEFT OUTER JOIN DBMesin msn ON msn.KodeMsn = a.KodeMSN
       WHERE a.NOBUKTI = :nobukti",
      ['nobukti' => $req->nobukti]
    );

    if (!$row) {
      return response()->json(['message' => 'SPK tidak ditemukan'], 404);
    }
    return response()->json($row);
  }

  // Otorisasi SPK -- pola sama dengan menu lain yang tabelnya cuma punya satu level (mis.
  // MemorialKoreksiController::spOtorisasi()): UPDATE langsung header (DBSPK), level 1,
  // MaxOL = 1. Dikembalikan jumlah baris terupdate (1 = berhasil); SPK yang sudah diotorisasi
  // atau tidak ada tidak disentuh (0). Hak akses (IsOtorisasi1 menu) dicek di sisi tombol, sama
  // seperti menu lain.
  public function spOtorisasi(Request $req) {
    $tanggal = date('Y-m-d 00:00:00');
    return DB::connection('SML')->update(
      "UPDATE DBSPK SET IsOtorisasi1 = 1, MaxOL = 1, OtoUser1 = :username, TglOto1 = :tanggal
       WHERE NOBUKTI = :nobukti AND ISNULL(IsOtorisasi1, 0) = 0",
      ['username' => \Auth::user()->username, 'tanggal' => $tanggal, 'nobukti' => $req->nobukti]
    );
  }

  // Batal Otorisasi SPK -- keterangan (pket) wajib dari sisi tombol, dicatat lewat
  // LoggingData('btloto') seperti menu lain. Ditolak kalau SPK sudah ditutup (IsCLose) -- SPK yang
  // sudah ditutup tidak boleh dikoreksi lagi.
  public function spBatalOtorisasi(Request $req) {
    $res = DB::connection('SML')->update(
      "UPDATE DBSPK SET IsOtorisasi1 = 0, MaxOL = -1, OtoUser1 = '', TglOto1 = NULL
       WHERE NOBUKTI = :nobukti AND ISNULL(IsOtorisasi1, 0) = 1 AND ISNULL(IsCLose, 0) = 0",
      ['nobukti' => $req->nobukti]
    );
    if ($res > 0) {
      app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'SPK', $req->nobukti, $req->pket, 0, 'DBSPK');
    }
    return $res;
  }

  // Hapus SELURUH SPK (tombol Hapus di baris Daftar SPK) -- beda dari spSPKJD Choice='D' yang
  // cuma menghapus satu baris Barang Jadi. Ditolak kalau SPK sudah ditutup (IsCLose),
  // sudah diotorisasi (IsOtorisasi1-5), atau sudah dipakai jadwal produksi (DBJADWALPRD.NOSPK --
  // pengecekan yang sama dengan sp_SPK lama). Tiap baris DBSPKJD dihapus lewat sp_SPKJD
  // Choice='D' (SP menghapus baris itu + Bahan Baku hasil ledak-BOM-nya, URUTSPK sama, dan
  // header DBSPK begitu DBSPKJD terakhir hilang). Sisa Bahan Baku MANUAL (URUTSPK = 0, tidak
  // terkait baris Barang Jadi mana pun) dan header yatim dibersihkan di sini, dalam satu
  // transaksi supaya tidak setengah terhapus.
  public function hapusSPK(Request $req) {
    $db = DB::connection('SML');
    $nobukti = $req->nobukti;

    $spk = $db->selectOne(
      "SELECT IsCLose, IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5
       FROM DBSPK WHERE NOBUKTI = :nobukti",
      ['nobukti' => $nobukti]
    );
    if (!$spk) {
      return response()->json(['message' => 'SPK tidak ditemukan'], 404);
    }
    if ($spk->IsCLose) {
      return response()->json(['message' => 'SPK sudah ditutup, tidak bisa dihapus'], 422);
    }
    if ($spk->IsOtorisasi1 || $spk->IsOtorisasi2 || $spk->IsOtorisasi3 || $spk->IsOtorisasi4 || $spk->IsOtorisasi5) {
      return response()->json(['message' => 'SPK sudah diotorisasi, tidak bisa dihapus'], 422);
    }
    $jadwal = $db->selectOne('SELECT COUNT(*) as n FROM DBJADWALPRD WHERE NOSPK = :nobukti', ['nobukti' => $nobukti]);
    if ($jadwal->n > 0) {
      return response()->json(['message' => 'SPK sudah dipakai di jadwal produksi, tidak bisa dihapus'], 422);
    }

    $db->transaction(function () use ($db, $nobukti) {
      $rows = $db->select('SELECT URUT FROM DBSPKJD WHERE NOBUKTI = :nobukti', ['nobukti' => $nobukti]);
      foreach ($rows as $r) {
        $db->statement('exec sp_SPKJD ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
          'D', $nobukti, '', '1970-01-01', '', 0, 1, '', 0, '', $r->URUT, 0, 0, 0, '', 0, '', 0, 0,
        ]);
      }
      $db->delete('DELETE FROM DBSPKDET WHERE NOBUKTI = :nobukti', ['nobukti' => $nobukti]);
      $db->delete('DELETE FROM DBSPK WHERE NOBUKTI = :nobukti', ['nobukti' => $nobukti]);
    });

    return response()->json(['success' => true]);
  }

  // Simpan/Edit/Hapus Barang Jadi -- sp_SPKJD Choice='I'/'U'/'D' (menggantikan Sp_SPKBJ/
  // dbSPKBJDet). Choice='I': kalau DBSPK (header) belum ada, SP ikut insert header-nya
  // (NoBukti/NoUrut/Tanggal/KodeMSN/flag), insert baris DBSPKJD (Urut dihitung ulang di SP,
  // SatJ/IsiJ juga diambil SP dari DBBARANG sesuai NosatJ), lalu OTOMATIS meledakkan DBBOMDET
  // milik @kodebom ke DBSPKDET (Bahan Baku): Qnt = Numerator / (Denominator, 0 dianggap 1) * QntJ,
  // URUTSPK = Urut baris DBSPKJD ini. Choice='U' TIDAK menghitung ulang Bahan Baku. Choice='D'
  // menghapus baris DBSPKJD + Bahan Baku-nya (URUTSPK sama), dan header kalau sudah tidak ada
  // DBSPKJD tersisa. Flag pPotong/pProses/pHp02/pBJ/NoBeli/UrutBeli belum punya input di form,
  // dikirim default (0/kosong).
  public function spSPKJD(Request $req) {
    DB::connection('SML')->statement('exec sp_SPKJD ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->nobukti,
      $req->nourut ?: '',
      $req->tanggal ?: '1970-01-01',
      $req->kodebrg ?: '',
      $req->qnt ?: 0,
      1,
      '',
      0,
      $req->kodemsn ?: '',
      $req->urut ?: 0,
      0,
      0,
      0,
      '',
      0,
      $req->kodebom ?: '',
      0,
      0,
    ]);

    return response()->json(['success' => true]);
  }

  // Dropdown "Kode Proses" di form Bahan Baku -- hanya proses yang terikat ke Kode BOM milik
  // Barang Jadi di SPK ini (DBBOMProses lewat DBSPKJD.kodeBOM). Kalau SPK punya beberapa Barang
  // Jadi/BOM, hasilnya gabungan proses dari semuanya (tanpa duplikat). SPK yang belum punya
  // Barang Jadi -> kosong.
  public function listProsesSPK(Request $req) {
    return DB::connection('SML')->select(
      "SELECT DISTINCT bp.KodePrs, pr.Keterangan
       FROM DBSPKJD j
       JOIN DBBOMProses bp ON bp.KodeBOM = j.kodeBOM
       LEFT OUTER JOIN DBPRoses pr ON pr.KodePrs = bp.KodePrs
       WHERE j.NOBUKTI = :nobukti
       ORDER BY bp.KodePrs",
      ['nobukti' => $req->nobukti]
    );
  }

  // Simpan/Edit/Hapus Bahan Baku -- sp_SPK Choice='I'/'U'/'D' (body lengkap dicek lewat
  // OBJECT_DEFINITION, sama pola dengan sp_BOM/sp_BOMProses di ProduksiBomController). Header
  // sudah pasti ada duluan (dibuat spSPKJD() saat Barang Jadi pertama ditambah) -- SP cuma
  // insert header kalau BELUM ada, jadi di sini dikirim placeholder kosong untuk parameter
  // header. Dipakai untuk Bahan Baku MANUAL; Bahan Baku hasil ledak-BOM otomatis dibuat
  // sp_SPKJD. 33 parameter (versi baru sp_SPK) -- parameter ke-30 (@UrutSPK) WAJIB benar
  // untuk Choice='D' karena SP menghapus berdasarkan NoBukti+URUTSPK+URUT; baris manual
  // URUTSPK-nya 0, baris otomatis = Urut baris DBSPKJD asalnya.
  public function spSPK(Request $req) {
    DB::connection('SML')->statement(
      'exec sp_SPK ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',
      [
        $req->choice,
        $req->nobukti,
        '1970-01-01',
        '',
        0,
        0,
        0,
        '',
        0,
        $req->kodebrg ?: '',
        $req->urut ?: 0,
        $req->isi ?: 0,
        $req->qnt ?: 0,
        0,
        $req->satuan ?: '',
        '',
        '',
        '1970-01-01',
        '',
        '',
        0,
        '',
        0,
        $req->kodeprs ?: '',
        0,
        '',
        '',
        '',
        0,
        $req->urutspk ?: 0,
        0,
        '',
        0,
      ]
    );

    // sp_SPK Choice='U' tidak menyentuh KodePrs (hanya KodeBrg/Satuan/Isi/Qnt/NoSat), jadi
    // proses yang diganti saat Koreksi Bahan Baku di-UPDATE langsung di sini.
    if ($req->choice === 'U' && $req->kodeprs) {
      DB::connection('SML')->update(
        'UPDATE DBSPKDET SET KodePrs = :kodeprs WHERE NOBUKTI = :nobukti AND URUT = :urut',
        ['kodeprs' => $req->kodeprs, 'nobukti' => $req->nobukti, 'urut' => $req->urut]
      );
    }

    return response()->json(['success' => true]);
  }
}
