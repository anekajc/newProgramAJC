<?php

namespace App\Http\Controllers\Produksi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Pemakaian Bahan (Penyerahan Bahan / BP) -- halaman browse dua tab, mengikuti tampilan desktop
// lama (Trade Exchange > Penyerahan Bahan(BP)): tab "OutStanding SPK" (bahan baku SPK yang
// belum habis dipakai) dan tab "BP" (daftar bukti penyerahan bahan). Baru tahap browse --
// tab BP masih placeholder (loadBP() mengembalikan kosong) menunggu query/SP dari senior,
// Tambah/Koreksi/Otorisasi belum ada.
class ProduksiPemakaianBahanController extends Controller
{
  public function index(Request $req) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    return view('produksi.produksipemakaianbahan', [
      "periode" => $periode,
      "akses" => $akses,
    ]);
  }

  // Tab "OutStanding SPK" -- satu baris per baris Bahan Baku SPK (DBSPKDET) yang masih punya sisa:
  // vwOutSPK_Pakai = Qnt SPK vs Qnt yang sudah diserahkan lewat BP (DBPenyerahanBhnDET), jadi
  // QntSisa > 0 = masih outstanding. Satuan diambil dari DBBARANG sesuai NOSAT baris itu
  // (NOSAT 0 -- baris manual/hasil ledak-BOM yang NoSat-nya belum diisi -- dianggap satuan 1).
  // Seluruh hasil dikirim sekaligus (tabel di blade client-side, pola sama dengan mastergiro).
  public function loadOutstanding() {
    // Query dari senior. Parameter aplikasi lama :0 (pPotong) dan :1 (pProses) diisi 0 dan 0 --
    // SPK reguler (bukan potong, bukan proses), sama dengan nilai yang disimpan sp_SPKJD dari
    // halaman SPK. Hanya baris yang belum pernah dipakai (QntPakai = 0) dan tahun > 2022.
    // Satu penyimpangan dari query asli: Satuan memakai Sat1 juga untuk NoSat = 0 (asli: NoSat=1
    // saja) -- baris Bahan Baku yang dibuat halaman SPK menyimpan NoSat 0, yang di query asli
    // jatuh ke Sat2 (kosong/"0").
    return DB::connection('SML')->select(
      "SELECT A.NOBUKTI, B.TANGGAL, A.URUT, A.KODEBRG, Br.NAMABRG, A.NOSAT,
              CASE WHEN A.NoSat IN (0, 1) THEN Br.Sat1 ELSE Br.Sat2 END AS Satuan,
              A.QntSPK, A.QntPakai, A.QntSisa, B.pBJ
       FROM vwOutSPK_Pakai A
       LEFT OUTER JOIN DBSPK B ON B.NOBUKTI = A.NoBukti
       LEFT OUTER JOIN DBBARANG Br ON Br.KODEBRG = A.KodeBrg
       WHERE A.QntSisa > 0
         AND B.pPotong = 0
         AND B.pProses = 0 AND ISNULL(A.QntPakai, 0) = 0 AND YEAR(B.TANGGAL) > 2022
       ORDER BY B.TANGGAL, A.NoBukti, A.Urut"
    );
  }

  // ===================== Form Tambah BP (tombol "+" OutStanding / Tambah di tab BP) =====================

  // Nomor Bukti BP -- pola BP/<NoUrut 5 digit>/<MMYY>/MM/<WIP|BJ>, sama dengan daftar di tab BP
  // (BP/00001/1026/MM/WIP, BP/00001/1026/MM/BJ). WIP dan BJ punya urutan sendiri-sendiri per
  // bulan+tahun; NoUrut = MAX(DBPenyerahanBhn.NoUrut)+1 untuk Nobukti yang polanya cocok. Pola
  // 'BP%/...' sengaja juga mencakup nomor berawalan BPS/ (mis. BPS/00006/1026/MM/WIP dari form
  // desktop) supaya urutannya tidak bentrok/mengulang.
  private function hitungNoBuktiBP($tipe, $bulan, $tahun) {
    $tipe = $tipe === 'BJ' ? 'BJ' : 'WIP';
    $mmyy = str_pad((int) $bulan, 2, '0', STR_PAD_LEFT) . str_pad(((int) $tahun) % 100, 2, '0', STR_PAD_LEFT);

    $row = DB::connection('SML')->selectOne(
      "SELECT MAX(CASE WHEN ISNUMERIC(NoUrut) = 1 THEN CAST(NoUrut AS INT) END) AS MaxUrut
       FROM DBPenyerahanBhn WHERE NoBukti LIKE :p",
      ['p' => "BP%/%/{$mmyy}/MM/{$tipe}"]
    );
    $urut = str_pad(((int) ($row->MaxUrut ?? 0)) + 1, 5, '0', STR_PAD_LEFT);

    return ['nobukti' => "BP/{$urut}/{$mmyy}/MM/{$tipe}", 'nourut' => $urut];
  }

  // Dipanggil blade tiap form dibuka (WIP/BJ ikut akhiran No. SPK). Belum disimpan ke mana pun.
  public function nextNoBukti(Request $req) {
    return response()->json($this->hitungNoBuktiBP($req->tipe, $req->bulan, $req->tahun));
  }

  // "Ambil SPK" -- baris yang akan diambil BP dari satu SPK. Isi & rumus sama dengan SELECT di
  // SP_INSPEMAKAI: SEMUA baris DBSPKDET milik SPK itu (SP tidak memeriksa sisa/outstanding), Qnt yang
  // ditampilkan = A.QNT (SP: Qnt1 NoSat=1 ? QNT : QNT*ISI, Qnt2 = QNT/ISI-satuan; yang tampil di grid
  // desktop = NoSat=1 ? Qnt1 : Qnt2, ujungnya sama dengan QNT SPK). Satuan kosong di SPK (mis. dari
  // BOM) ditampilkan dengan Sat1 barang. Hanya pratinjau -- tidak menyimpan apa pun.
  public function ambilSPK(Request $req) {
    return DB::connection('SML')->select(
      "SELECT A.URUT, A.NOBUKTI AS NoSPK, A.KODEBRG, Br.NAMABRG, A.NOSAT,
              ISNULL(NULLIF(A.SATUAN, ''), Br.SAT1) AS Sat, A.QNT AS Qnt
       FROM DBSPKDET A
       LEFT OUTER JOIN DBBARANG Br ON Br.KODEBRG = A.KODEBRG
       WHERE A.NOBUKTI = :nospk
       ORDER BY A.URUT",
      ['nospk' => $req->nospk]
    );
  }

  // Simpan BP -- meniru tombol "Ambil SPK" desktop: Exec SP_INSPEMAKAI (Nobukti, Nourut, NOSPK,
  // Tanggal, Kodegdg, PBHN, Shift, PBPRS, php02), di sini dipanggil saat Simpan (bukan saat form
  // dibuka) karena SP-nya langsung menulis header + semua baris ke DBPenyerahanBhn/DET -- memanggilnya
  // otomatis tiap "+" diklik akan meninggalkan BP yatim kalau form ditutup, dan Gudang belum dipilih
  // (FK ke DBGUDANG). PBHN, Shift, PBPRS, php02 = 0 untuk sementara (pBJ dihitung SP sendiri dari
  // 'BJ' di Nobukti). Nomor Bukti dihitung ULANG di sini supaya tidak bentrok kalau BP lain
  // tersimpan lebih dulu.
  public function simpanBP(Request $req) {
    $db = DB::connection('SML');
    $nospk = trim((string) $req->nospk);
    $kodegdg = trim((string) $req->kodegdg);

    if ($nospk === '') {
      return response()->json(['message' => 'Pilih SPK dari tab OutStanding SPK (tombol +)'], 422);
    }
    if ($kodegdg === '') {
      return response()->json(['message' => 'Pilih Gudang terlebih dahulu'], 422);
    }
    if (!$db->selectOne('SELECT 1 AS x FROM DBSPK WHERE NOBUKTI = ?', [$nospk])) {
      return response()->json(['message' => 'SPK tidak ditemukan'], 404);
    }
    if ((int) $db->selectOne('SELECT COUNT(*) AS n FROM DBSPKDET WHERE NOBUKTI = ?', [$nospk])->n === 0) {
      return response()->json(['message' => 'SPK tidak punya barang'], 422);
    }

    // Qnt yang diubah user di grid (qnt[<Urut baris SPK>] = nilai). SP_INSPEMAKAI selalu menyalin Qnt
    // SPK apa adanya, jadi nilai yang diubah diterapkan ke baris BP SETELAH SP jalan, dalam transaksi
    // yang sama. Tiap Urut harus baris SPK ini dan nilainya angka > 0.
    $qnt = is_array($req->qnt) ? $req->qnt : [];
    $urutSPK = array_map(fn ($r) => (int) $r->URUT, $db->select('SELECT URUT FROM DBSPKDET WHERE NOBUKTI = ?', [$nospk]));
    foreach ($qnt as $urut => $q) {
      if (!in_array((int) $urut, $urutSPK, true)) {
        return response()->json(['message' => 'Baris SPK tidak dikenal: ' . $urut], 422);
      }
      if (!is_numeric($q) || (float) $q <= 0) {
        return response()->json(['message' => 'Qnt harus berupa angka lebih dari 0'], 422);
      }
    }

    // Shift: '-' (atau kosong) = 0, selain itu 1-4 (kolom DBPenyerahanBhn.Shif bertipe tinyint).
    $shiftIn = trim((string) $req->shift);
    $shift = ($shiftIn === '' || $shiftIn === '-') ? 0 : (int) $shiftIn;
    if ($shift < 0 || $shift > 4) {
      return response()->json(['message' => 'Shift tidak valid'], 422);
    }

    $tipe = substr($nospk, -3) === '/BJ' ? 'BJ' : 'WIP';
    $no =$this->hitungNoBuktiBP($tipe, $req->bulan, $req->tahun);

    try {
      $db->transaction(function () use ($db, $no, $nospk, $req, $kodegdg, $qnt, $shift) {
        $db->statement('exec SP_INSPEMAKAI ?,?,?,?,?,?,?,?,?', [
          $no['nobukti'],
          $no['nourut'],
          $nospk,
          $req->tanggal ?: date('Y-m-d'),
          $kodegdg,
          0,
          $shift,
          0,
          0,
        ]);

        // Rumus Qnt/Qnt2 sama dengan SP_INSPEMAKAI untuk qty yang ditampilkan di grid: NoSat=1 ->
        // Qnt = q; satuan lain -> Qnt = q * Isi; Qnt2 = q.
        foreach ($qnt as $urut => $q) {
          $q = round((float) $q, 2);
          $db->update(
            'UPDATE DBPenyerahanBhnDET
             SET Qnt = CASE WHEN NoSat = 1 THEN ? ELSE ? * ISNULL(Isi, 1) END, Qnt2 = ?
             WHERE Nobukti = ? AND Urut = ?',
            [$q, $q, $q, $no['nobukti'], (int) $urut]
          );
        }
      });
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Gagal menyimpan BP: ' . $e->getMessage()], 422);
    }

    if (!$db->selectOne('SELECT 1 AS x FROM DBPenyerahanBhn WHERE Nobukti = ?', [$no['nobukti']])) {
      return response()->json(['message' => 'BP tidak tersimpan (SP tidak menghasilkan data)'], 500);
    }

    return response()->json(['success' => true, 'nobukti' => $no['nobukti']]);
  }

  // Dropdown "Gudang" di form Tambah BP -- gudang aktif (DBGUDANG).
  public function listGudang() {
    return DB::connection('SML')->select(
      'SELECT KODEGDG, NAMA FROM DBGUDANG WHERE IsAktif = 1 ORDER BY KODEGDG'
    );
  }

  // Header + baris satu BP -- dipakai buttonDetail() dan buttonEdit() (form dibuka read-only/Koreksi).
  // Query pemuatan dari senior (header di-join ke baris, tidak ada baris = BP tidak ditemukan);
  // kolom placeholder Stok/QntBppB/QntBP/sisa (selalu 0) dan Isi2/NFix tidak dibawa. Qnt = yang
  // tampil di grid (NoSat=1 -> Qnt, selain itu Qnt2). Satuan kosong ditampilkan dengan Sat1 barang.
  public function loadDetailBP(Request $req) {
    return DB::connection('SML')->select(
      "SELECT A.NoBukti, A.NoUrut, A.Tanggal, A.KodeGdg, A.NoBPPB, A.Shif, A.IsOtorisasi1,
              B.Urut, B.KodeBrg,
              CASE WHEN B.NoSat = 1 THEN B.Qnt ELSE B.Qnt2 END AS Qnt,
              B.Qnt AS Qnt1, B.Qnt2, B.Isi, B.NoSat,
              ISNULL(NULLIF(B.Sat, ''), D.SAT1) AS Sat,
              D.NamaBrg, B.UrutSPK, B.NoSatSPK
       FROM DBPenyerahanBhn A
       LEFT OUTER JOIN DBPenyerahanBhnDET B ON B.NoBukti = A.NoBukti
       LEFT OUTER JOIN DBBARANG D ON D.KodeBrg = B.KodeBrg
       WHERE A.NoBukti = ?
       ORDER BY B.Urut",
      [$req->nobukti]
    );
  }

  // Koreksi BP -- hanya Qnt per baris yang bisa diubah (qnt[<Urut>] = nilai): header BP (Tanggal,
  // Gudang, No. SPK, ...) tidak punya SP/query ubah, jadi dikunci di form -- sama seperti Koreksi SPK.
  // Rumus Qnt/Qnt2 sama dengan simpanBP(). Ditolak kalau BP sudah diotorisasi.
  public function updateBP(Request $req) {
    $db = DB::connection('SML');
    $nobukti = $req->nobukti;

    $bp = $db->selectOne(
      'SELECT IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5 FROM DBPenyerahanBhn WHERE Nobukti = ?',
      [$nobukti]
    );
    if (!$bp) {
      return response()->json(['message' => 'BP tidak ditemukan'], 404);
    }
    if ($bp->IsOtorisasi1 || $bp->IsOtorisasi2 || $bp->IsOtorisasi3 || $bp->IsOtorisasi4 || $bp->IsOtorisasi5) {
      return response()->json(['message' => 'BP sudah diotorisasi, tidak bisa dikoreksi'], 422);
    }

    $qnt = is_array($req->qnt) ? $req->qnt : [];
    if (!$qnt) {
      return response()->json(['message' => 'Tidak ada Qnt yang dikirim'], 422);
    }
    $urutBP = array_map(fn ($r) => (int) $r->Urut, $db->select('SELECT Urut FROM DBPenyerahanBhnDET WHERE Nobukti = ?', [$nobukti]));
    foreach ($qnt as $urut => $q) {
      if (!in_array((int) $urut, $urutBP, true)) {
        return response()->json(['message' => 'Baris BP tidak dikenal: ' . $urut], 422);
      }
      if (!is_numeric($q) || (float) $q <= 0) {
        return response()->json(['message' => 'Qnt harus berupa angka lebih dari 0'], 422);
      }
    }

    try {
      $db->transaction(function () use ($db, $nobukti, $qnt) {
        foreach ($qnt as $urut => $q) {
          $q = round((float) $q, 2);
          $db->update(
            'UPDATE DBPenyerahanBhnDET
             SET Qnt = CASE WHEN NoSat = 1 THEN ? ELSE ? * ISNULL(Isi, 1) END, Qnt2 = ?
             WHERE Nobukti = ? AND Urut = ?',
            [$q, $q, $q, $nobukti, (int) $urut]
          );
        }
      });
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Gagal menyimpan koreksi BP: ' . $e->getMessage()], 422);
    }

    return response()->json(['success' => true, 'nobukti' => $nobukti]);
  }

  // Otorisasi BP -- pola sama dengan Otorisasi SPK/menu satu-level lain (mis. MemorialKoreksiController):
  // UPDATE langsung header (DBPenyerahanBhn), level 1, MaxOL = 1. Dikembalikan jumlah baris terupdate
  // (1 = berhasil); BP yang sudah diotorisasi atau tidak ada tidak disentuh (0). Hak akses
  // (IsOtorisasi1 menu) dicek di sisi tombol, sama seperti menu lain.
  public function spOtorisasi(Request $req) {
    return DB::connection('SML')->update(
      "UPDATE DBPenyerahanBhn SET IsOtorisasi1 = 1, MaxOL = 1, OtoUser1 = :username, TglOto1 = :tanggal
       WHERE Nobukti = :nobukti AND ISNULL(IsOtorisasi1, 0) = 0",
      ['username' => \Auth::user()->username, 'tanggal' => date('Y-m-d 00:00:00'), 'nobukti' => $req->nobukti]
    );
  }

  // Batal Otorisasi BP -- keterangan (pket) wajib dari sisi tombol, dicatat lewat LoggingData('btloto')
  // seperti menu lain. Hanya BP yang memang sudah diotorisasi.
  public function spBatalOtorisasi(Request $req) {
    $res = DB::connection('SML')->update(
      "UPDATE DBPenyerahanBhn SET IsOtorisasi1 = 0, MaxOL = -1, OtoUser1 = '', TglOto1 = NULL
       WHERE Nobukti = :nobukti AND ISNULL(IsOtorisasi1, 0) = 1",
      ['nobukti' => $req->nobukti]
    );
    if ($res > 0) {
      app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'BP', $req->nobukti, $req->pket, 0, 'DBPenyerahanBhn');
    }
    return $res;
  }

  // Tab "BP" -- daftar Penyerahan Bahan per periode (bulan+tahun dikirim blade dari periode
  // aktif). Query dari senior; parameter aplikasi lama :0 (PBHN) dan :1 (PAlat) diisi 0 dan 0 untuk
  // sementara (sama dengan yang disimpan simpanBP()). Baris lama yang PBHN-nya masih NULL (mis.
  // nomor 'SML/...') tidak tampil di sini.
  public function loadBP(Request $req) {
    return DB::connection('SML')->select(
      "SELECT A.NoBukti, A.Tanggal, A.KodeGdg,
              A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
              A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
              A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
              CAST(CASE WHEN CASE WHEN A.IsOtorisasi1 = 1 THEN 1 ELSE 0 END +
                             CASE WHEN A.IsOtorisasi2 = 1 THEN 1 ELSE 0 END +
                             CASE WHEN A.IsOtorisasi3 = 1 THEN 1 ELSE 0 END +
                             CASE WHEN A.IsOtorisasi4 = 1 THEN 1 ELSE 0 END +
                             CASE WHEN A.IsOtorisasi5 = 1 THEN 1 ELSE 0 END = A.MaxOL THEN 0
                        ELSE 1
                   END AS BIT) NeedOtorisasi
       FROM DBPenyerahanBhn A
       WHERE YEAR(A.Tanggal) = ? AND MONTH(A.Tanggal) = ? AND A.PBHN = ? AND ISNULL(A.PAlat, 0) = ?
       ORDER BY A.pBJ, A.NoBukti",
      [(int) $req->tahun, (int) $req->bulan, 0, 0]
    );
  }
}
