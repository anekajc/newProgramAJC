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
}
