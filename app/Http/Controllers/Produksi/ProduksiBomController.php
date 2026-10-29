<?php

namespace App\Http\Controllers\Produksi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Browser BOM (Bill of Materials) untuk item Barang Jadi/WIP -- 4 panel mengikuti tampilan
// lama (System Inventory Control > BOM): daftar barang (kiri), lalu di kanan daftar BOM per
// item, daftar proses per BOM, dan detail komponen BOM. Baru tahap browse (Read) --
// Tambah/Koreksi/Cetak belum diisi, lihat buttonAdd()/buttonEdit()/submitPrint() di blade.
class ProduksiBomController extends Controller
{
  public function index(Request $req) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    return view('produksi.produksibom', [
      "periode" => $periode,
      "akses" => $akses,
    ]);
  }

  // Panel kiri ("Daftar Barang Jadi dan WIP") -- server-side DataTables, kolom Kode Barang/
  // Nama Barang. Query persis dari senior: vwBarang, IsBarang 2/4 (Jadi/WIP) atau 3 tanpa
  // Jasa (IsJasa=0) -- bukan DBBARANG.KODEGRP langsung.
  public function loadAllBarang(Request $req) {
    $query = DB::connection('SML')->table('vwBarang')
      ->where(function ($q) {
        $q->where('IsBarang', 2)
          ->orWhere('IsBarang', 4)
          ->orWhere(function ($q2) {
            $q2->where('IsBarang', 3)->where('IsJasa', 0);
          });
      });

    $search = $req->input('search.value');
    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('KodeBrg', 'like', "%{$search}%")
          ->orWhere('NamaBrg', 'like', "%{$search}%");
      });
    }

    $start = (int) $req->input('start', 0);
    $length = (int) $req->input('length', 10);

    // DBSMLNEW's compatibility level doesn't support OFFSET/FETCH, page via ROW_NUMBER()
    // instead (sama pola dengan MasterBarangController::loadAll()).
    $dataQuery = (clone $query)->select(
      'KodeBrg', 'NamaBrg', 'Sat1',
      DB::raw('ROW_NUMBER() OVER (ORDER BY KodeBrg) as RowNum'),
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

  // Kode BOM berikutnya untuk satu Kode Barang (dipanggil buttonAdd() di modalProduksiBOM) --
  // pola <KodeBrg>-NN, NN dari NoUrut (varchar 2 digit) terbesar yang sudah dipakai + 1.
  // Belum ada baris untuk barang itu -> NoUrut mulai dari 01.
  public function nextKodeBom(Request $req) {
    $kodebrg = $req->kodebrg;

    $row = DB::connection('SML')->selectOne(
      'SELECT MAX(CAST(NoUrut AS INT)) as MaxUrut FROM DBBOM WHERE KodeBrg = :kodebrg',
      ['kodebrg' => $kodebrg]
    );
    $urut = str_pad(((int) ($row->MaxUrut ?? 0)) + 1, 2, '0', STR_PAD_LEFT);

    return response()->json([
      'kodebom' => $kodebrg . '-' . $urut,
      'urut' => $urut,
    ]);
  }

  // submitAddProses()/buttonHapusProses() di modalProduksiBOM.blade.php -- sp_BOMProses
  // (body lengkap dicek lewat OBJECT_DEFINITION, bukan ditebak):
  // Choice='I': kalau KodeBOM belum ada di DBBOM, SP insert header-nya juga (pakai
  // @NoUrut/@IsDefault/@TglAwal/@TglAkhir yang dikirim di sini), lalu selalu insert satu
  // baris DBBOMProses (Urut dihitung ULANG di dalam SP dari MAX(Urut)+1 per KodeBOM --
  // @Urut yang dikirim di sini diabaikan untuk Choice='I', dikirim tetap karena parameternya
  // wajib diisi).
  // Choice='D': SP hapus DBBOMProses WHERE KodeBOM+Urut, lalu kalau KodeBOM itu jadi tidak
  // punya proses sama sekali lagi, SP ikut hapus header DBBOM-nya juga (auto-hapus BOM
  // kosong) -- SP SENDIRI TIDAK CEK DBBOMDET sama sekali, jadi proses yang masih punya
  // Detail BOM akan ikut ter-delete begitu saja kalau dibiarkan. Makanya dicegah di sini:
  // kalau DBBOMDET masih ada baris untuk KodeBOM+KodePrs ini, tolak sebelum sampai exec SP.
  public function spBOMProses(Request $req) {
    if ($req->choice === 'D') {
      $row = DB::connection('SML')->selectOne(
        'SELECT COUNT(*) as Total FROM DBBOMDET WHERE KodeBOM = :kodebom AND KodePrs = :kodeprs',
        ['kodebom' => $req->kodebom, 'kodeprs' => $req->kodeprs]
      );
      if (($row->Total ?? 0) > 0) {
        return response()->json([
          'success' => false,
          'message' => 'Proses ini masih punya Detail BOM, hapus semua detailnya dulu sebelum menghapus proses.',
        ], 422);
      }
    }

    DB::connection('SML')->statement('exec sp_BOMProses ?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->kodebom,
      $req->kodebrghd,
      $req->nourut,
      $req->isdefault,
      $req->tglawal,
      $req->tglakhir,
      $req->urut,
      $req->kodeprs,
    ]);

    return response()->json(['success' => true]);
  }

  // Panel "Daftar BOM untuk Kode Barang" -- semua BOM milik satu item.
  public function listBom(Request $req) {
    return DB::connection('SML')->select(
      'SELECT A.* FROM DBBOM A WHERE A.KodeBrg = :kodebrg ORDER BY A.KodeBOM',
      ['kodebrg' => $req->kodebrg]
    );
  }

  // Panel "Daftar Proses" -- proses milik satu BOM.
  public function listProses(Request $req) {
    return DB::connection('SML')->select(
      'SELECT A.* FROM DBBOMProses A WHERE A.KodeBOM = :kodebom ORDER BY A.KodeBOM',
      ['kodebom' => $req->kodebom]
    );
  }

  // Picker "Kode Proses" (modalProduksiBOM.blade.php, tombol cari di samping input Kode
  // Proses) -- daftar master proses (DBPRoses). kodebom (opsional, dikirim buttonKodeProses()
  // selama Tambah Proses sudah tahu KodeBOM-nya): KodePrs yang SUDAH dipakai KodeBOM itu di
  // DBBOMProses dikeluarkan dari daftar, supaya tidak bisa pilih proses yang sama dua kali
  // untuk satu BOM (sp_BOMProses sendiri tidak menolak duplikat -- insert begitu saja).
  public function listKodeProses(Request $req) {
    if ($req->kodebom) {
      return DB::connection('SML')->select(
        'SELECT KodePrs, Keterangan FROM DBPRoses
         WHERE KodePrs NOT IN (SELECT KodePrs FROM DBBOMProses WHERE KodeBOM = :kodebom)
         ORDER BY KodePrs',
        ['kodebom' => $req->kodebom]
      );
    }

    return DB::connection('SML')->select('SELECT KodePrs, Keterangan FROM DBPRoses ORDER BY KodePrs');
  }

  // Picker "Kode Barang" (modalProduksiBOM.blade.php, Tambah Detail BOM) -- DBBARANG punya
  // 18rb+ baris, jadi dipaging betulan server-side (pola sama dengan loadAllBarang() di atas
  // dan MasterBarangController::loadAll() -- ROW_NUMBER()/COUNT() OVER() karena compat level
  // DBSMLNEW tidak dukung OFFSET/FETCH), bukan TOP 100 + DataTables client-side seperti versi
  // lama. Tabel #tabel_kodebarangdet diinisialisasi sebagai DataTable serverSide:true di
  // modalProduksiBOM.blade.php, kirim start/length/search.value standar DataTables. Belum ada
  // pencarian -> dibatasi KODEGRP WIP/BJ (barang jadi/WIP, komponen BOM biasanya dari sini);
  // sudah mengetik -> cari KODEBRG/NAMABRG di SELURUH DBBARANG (sengaja tidak dibatasi
  // KODEGRP, sama seperti perilaku lama).
  public function listKodeBarang(Request $req) {
    $query = DB::connection('SML')->table('DBBARANG');

    $search = $req->input('search.value');
    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('KODEBRG', 'like', "%{$search}%")
          ->orWhere('NAMABRG', 'like', "%{$search}%");
      });
    } else {
      $query->whereIn('KODEGRP', ['WIP', 'BB', 'BP']);
    }

    $start = (int) $req->input('start', 0);
    $length = (int) $req->input('length', 10);

    $dataQuery = (clone $query)->select(
      'KODEBRG', 'NAMABRG', 'KODEGRP',
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

  // submitAddDetailBom()/buttonHapusDetailBom() di modalProduksiBOM.blade.php -- satu baris
  // DBBOMDET per KodeBOM+KodePrs. Choice='I': @Urut/@Qnt yang dikirim cuma pengisi parameter
  // wajib, sp_BOM sendiri menghitung ulang keduanya (@Urut = MAX(Urut)+1 per
  // KodeBOM+KodePrs, @Qnt = @Numerator/@Denominator). Choice='U'/'D': @Urut WAJIB benar,
  // dipakai SP buat kunci baris (KodeBOM+Urut+KodePrs) -- lihat body SP dari senior. Field
  // yang belum ada input-nya di form Tambah/Edit Detail BOM diisi default sesuai instruksi
  // senior: @PlaceCD kosong (''), @NoSat 0 (kolomnya int, tidak boleh NULL), @Satuan kosong
  // (''), @Isi = 1 -- diabaikan SP untuk Choice='U'/'D' (update cuma set KodeBrg/Qnt/
  // Numerator/Denominator/LossRatio/PlaceCD, delete cuma pakai KodeBOM+Urut+KodePrs).
  public function spBOM(Request $req) {
    DB::connection('SML')->statement('exec sp_bom ?,?,?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->kodebom,
      $req->kodeprs,
      $req->urut ?: 0,
      $req->kodebrg ?: '',
      0,
      $req->numerator ?: 0,
      $req->denominator ?: 0,
      $req->lossratio ?: 0,
      '',
      0,
      '',
      1,
    ]);

    return response()->json(['success' => true]);
  }

  // Panel "Detail BOM" -- komponen/bahan milik satu BOM, UNTUK satu proses yang dipilih
  // di panel Daftar Proses (bukan seluruh BOM sekaligus).
  public function listDetail(Request $req) {
    return DB::connection('SML')->select(
      'SELECT A.*, B.NAMABRG, B.SAT1
       FROM DBBOMDET A
       LEFT OUTER JOIN DBBARANG B ON B.KODEBRG = A.KodeBrg
       WHERE A.KodeBOM = :kodebom AND A.KodePrs = :kodeprs
       ORDER BY A.Urut',
      ['kodebom' => $req->kodebom, 'kodeprs' => $req->kodeprs]
    );
  }

  // Grid "Detail BOM" di modal Edit BOM (modalProduksiBOM.blade.php, buttonEditBom()) --
  // beda dari listDetail() di atas: seluruh detail satu KodeBOM lintas KodePrs sekaligus,
  // bukan difilter per proses yang dipilih.
  public function listDetailByBom(Request $req) {
    return DB::connection('SML')->select(
      'SELECT A.*, B.NAMABRG, B.SAT1
       FROM DBBOMDET A
       LEFT OUTER JOIN DBBARANG B ON B.KODEBRG = A.KodeBrg
       WHERE A.KodeBOM = :kodebom
       ORDER BY A.KodePrs, A.Urut',
      ['kodebom' => $req->kodebom]
    );
  }
}
