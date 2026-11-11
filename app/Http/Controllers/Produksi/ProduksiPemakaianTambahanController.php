<?php

namespace App\Http\Controllers\Produksi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Pemakaian Tambahan -- sama dengan Pemakaian Bahan (Penyerahan Bahan / BP, tabel DBPenyerahanBhn +
// DBPenyerahanBhnDET, daftar BP yang sama), bedanya tidak ada tab OutStanding SPK dan cara input:
// user memilih BP yang SUDAH ADA lalu menambah Kode Barang lain (browsing DBBARANG yang IsAktif = 1)
// langsung ke BP itu lewat sp_PenyerahanBhn Choice 'I'. Baris tambahan memakai NoSPK = NoBPPB header BP
// itu, UrutSPK = 0 dan NoSatSPK = 0 (UrutSPK 0 = bukan berasal dari SPK; baris dari SPK UrutSPK-nya >= 1).
class ProduksiPemakaianTambahanController extends Controller
{
  public function index(Request $req) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    return view('produksi.produksipemakaiantambahan', [
      "periode" => $periode,
      "akses" => $akses,
    ]);
  }

  // Dropdown "Gudang" di form BP -- gudang aktif (DBGUDANG).
  public function listGudang() {
    return DB::connection('SML')->select(
      'SELECT KODEGDG, NAMA FROM DBGUDANG WHERE IsAktif = 1 ORDER BY KODEGDG'
    );
  }

  // Picker Kode Barang -- DBBARANG yang IsAktif = 1, DataTables serverSide (ROW_NUMBER, tanpa
  // OFFSET/FETCH), pola sama dengan ProduksiBomController::listKodeBarang().
  public function listBarang(Request $req) {
    $query = DB::connection('SML')->table('DBBARANG')->where('ISAKTIF', 1);

    $search = $req->input('search.value');
    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('KODEBRG', 'like', "%{$search}%")
          ->orWhere('NAMABRG', 'like', "%{$search}%");
      });
    }

    $start = (int) $req->input('start', 0);
    $length = (int) $req->input('length', 10);

    $dataQuery = (clone $query)->select(
      'KODEBRG', 'NAMABRG', 'SAT1', 'ISI1',
      DB::raw('ROW_NUMBER() OVER (ORDER BY KODEBRG) as RowNum'),
      DB::raw('COUNT(*) OVER () as TotalCount')
    );

    $paged = DB::connection('SML')->query()->fromSub($dataQuery, 'sub');
    if ($length >= 0) {
      $paged->whereBetween('RowNum', [$start + 1, $start + $length]);
    }

    $data = $paged->get();
    $total = $data->isNotEmpty() ? (int) $data->first()->TotalCount : (clone $query)->count();

    return response()->json([
      "draw" => intval($req->input('draw')),
      "recordsTotal" => $total,
      "recordsFiltered" => $total,
      "data" => $data,
    ]);
  }

  // Header + baris satu BP -- dipakai buttonDetail() dan buttonEdit() (form dibuka read-only/Koreksi).
  // Qnt = yang tampil di grid (NoSat=1 -> Qnt, selain itu Qnt2). Satuan kosong ditampilkan dengan Sat1 barang.
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

  // True kalau salah satu level otorisasi BP sudah terisi.
  private function sudahOtorisasi($bp) {
    return $bp->IsOtorisasi1 || $bp->IsOtorisasi2 || $bp->IsOtorisasi3 || $bp->IsOtorisasi4 || $bp->IsOtorisasi5;
  }

  // Tambah satu Kode Barang ke BP yang sudah ada -- sp_PenyerahanBhn Choice 'I' (Urut dihitung SP:
  // MAX(Urut)+1). Satuan 1 barang dipakai (NoSat 1, Sat = Sat1, Isi = Isi1), Qnt = Qnt2 = qty. Ditolak
  // kalau BP tidak ada/sudah diotorisasi, barang tidak aktif, atau barang itu sudah ada di BP.
  public function tambahItem(Request $req) {
    $db = DB::connection('SML');
    $nobukti = trim((string) $req->nobukti);
    $kodebrg = trim((string) $req->kodebrg);
    $q = $req->qnt;

    $bp = $db->selectOne(
      'SELECT NoUrut, Tanggal, KodeGdg, NoBPPB, IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5
       FROM DBPenyerahanBhn WHERE Nobukti = ?',
      [$nobukti]
    );
    if (!$bp) {
      return response()->json(['message' => 'BP tidak ditemukan'], 404);
    }
    if ($this->sudahOtorisasi($bp)) {
      return response()->json(['message' => 'BP sudah diotorisasi, tidak bisa ditambah barang'], 422);
    }
    if ($kodebrg === '') {
      return response()->json(['message' => 'Pilih Kode Barang terlebih dahulu'], 422);
    }
    if (!is_numeric($q) || (float) $q <= 0) {
      return response()->json(['message' => 'Qnt harus berupa angka lebih dari 0'], 422);
    }
    $q = round((float) $q, 2);

    $brg = $db->selectOne('SELECT KODEBRG, SAT1, ISI1 FROM DBBARANG WHERE KODEBRG = ? AND ISAKTIF = 1', [$kodebrg]);
    if (!$brg) {
      return response()->json(['message' => 'Kode Barang tidak ditemukan atau tidak aktif'], 422);
    }
    if ($db->selectOne('SELECT 1 AS x FROM DBPenyerahanBhnDET WHERE Nobukti = ? AND KodeBrg = ?', [$nobukti, $kodebrg])) {
      return response()->json(['message' => 'Kode Barang sudah ada di BP ini'], 422);
    }

    $sebelum = (int) $db->selectOne('SELECT COUNT(*) AS n FROM DBPenyerahanBhnDET WHERE Nobukti = ?', [$nobukti])->n;

    try {
      $db->statement('exec sp_PenyerahanBhn ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
        'I',
        $nobukti,
        $bp->NoUrut,
        $bp->Tanggal,
        $bp->KodeGdg,
        0,                          // @Urut -- dihitung SP
        $kodebrg,
        $q,                         // @Qnt
        1,                          // @NoSat
        $brg->SAT1,                 // @Sat
        $brg->ISI1 ?: 1,            // @Isi
        (string) $bp->NoBPPB,       // @NoBppB -> DBPenyerahanBhnDET.NoSPK
        $q,                         // @Qnt2
        0,                          // @UrutSPK -- bukan dari SPK
        0,                          // @NoSatSPK
        0,                          // @IsSampel
        '',                         // @KodePrs
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Gagal menambah barang: ' . $e->getMessage()], 422);
    }

    $sesudah = (int) $db->selectOne('SELECT COUNT(*) AS n FROM DBPenyerahanBhnDET WHERE Nobukti = ?', [$nobukti])->n;
    if ($sesudah <= $sebelum) {
      return response()->json(['message' => 'Barang tidak tersimpan (SP tidak menambah baris)'], 500);
    }

    return response()->json(['success' => true, 'nobukti' => $nobukti]);
  }

  // Hapus satu baris yang ditambahkan lewat halaman ini (UrutSPK = 0) -- sp_PenyerahanBhn Choice 'D'.
  // Baris yang berasal dari SPK (UrutSPK >= 1) dan baris terakhir BP tidak boleh dihapus (SP akan ikut
  // menghapus header BP kalau barisnya habis).
  public function hapusItem(Request $req) {
    $db = DB::connection('SML');
    $nobukti = trim((string) $req->nobukti);
    $urut = (int) $req->urut;

    $bp = $db->selectOne(
      'SELECT NoBPPB, IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5
       FROM DBPenyerahanBhn WHERE Nobukti = ?',
      [$nobukti]
    );
    if (!$bp) {
      return response()->json(['message' => 'BP tidak ditemukan'], 404);
    }
    if ($this->sudahOtorisasi($bp)) {
      return response()->json(['message' => 'BP sudah diotorisasi, tidak bisa diubah'], 422);
    }

    $det = $db->selectOne('SELECT UrutSPK, NoSPK FROM DBPenyerahanBhnDET WHERE Nobukti = ? AND Urut = ?', [$nobukti, $urut]);
    if (!$det) {
      return response()->json(['message' => 'Baris tidak ditemukan'], 404);
    }
    if ((int) $det->UrutSPK !== 0) {
      return response()->json(['message' => 'Baris dari SPK tidak bisa dihapus di sini'], 422);
    }
    $jumlah = (int) $db->selectOne('SELECT COUNT(*) AS n FROM DBPenyerahanBhnDET WHERE Nobukti = ?', [$nobukti])->n;
    if ($jumlah <= 1) {
      return response()->json(['message' => 'Baris terakhir BP tidak bisa dihapus'], 422);
    }

    try {
      $db->statement('exec sp_PenyerahanBhn ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
        'D', $nobukti, '', '1970-01-01', '', $urut, '', 0, 0, '', 0, $det->NoSPK, 0, 0, 0, 0, '',
      ]);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Gagal menghapus barang: ' . $e->getMessage()], 422);
    }

    return response()->json(['success' => true, 'nobukti' => $nobukti]);
  }

  // Koreksi BP -- hanya Qnt per baris yang bisa diubah (qnt[<Urut>] = nilai). Ditolak kalau BP sudah
  // diotorisasi.
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
    if ($this->sudahOtorisasi($bp)) {
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

  // Otorisasi BP -- sama dengan Pemakaian Bahan: UPDATE header level 1, MaxOL = 1. Dikembalikan jumlah
  // baris terupdate (1 = berhasil).
  public function spOtorisasi(Request $req) {
    return DB::connection('SML')->update(
      "UPDATE DBPenyerahanBhn SET IsOtorisasi1 = 1, MaxOL = 1, OtoUser1 = :username, TglOto1 = :tanggal
       WHERE Nobukti = :nobukti AND ISNULL(IsOtorisasi1, 0) = 0",
      ['username' => \Auth::user()->username, 'tanggal' => date('Y-m-d 00:00:00'), 'nobukti' => $req->nobukti]
    );
  }

  // Batal Otorisasi BP -- keterangan (pket) wajib, dicatat lewat LoggingData('btloto').
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

  // Daftar BP per periode (bulan+tahun dari periode aktif) -- query dan filter (PBHN = 0, PAlat = 0)
  // sama dengan Pemakaian Bahan, jadi kedua menu menampilkan BP yang sama.
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
