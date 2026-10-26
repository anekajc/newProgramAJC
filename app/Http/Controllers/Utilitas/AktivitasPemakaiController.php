<?php

namespace App\Http\Controllers\Utilitas;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Log aktivitas pemakai (DBLOGFILE) -- halaman ini murni laporan/read-only, tidak ada
// Tambah/Edit/Hapus. Sebelumnya file ini isinya hasil copy-paste TutupBukuController
// (SP tutup buku) yang tidak nyambung sama sekali dengan menu Aktivitas Pemakai.
class AktivitasPemakaiController extends Controller
{
  public function index(Request $req) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    return view('utilitas.aktivitaspemakai', [
      "periode" => $periode,
      "akses" => $akses,
    ]);
  }

  // Isi dropdown Aktivitas/Transaksi/Pemakai -- nilai mentah (apa adanya) dari data yang
  // sudah ada di DBLOGFILE, tidak ada pemetaan ke label karena arti tiap kode tidak pasti.
  public function listSelect(Request $req) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', 'aktivitaspemakai');
    if (!$akses || !$akses->HASACCESS) {
        return [];
    }

    $listAktivitas = DB::connection('SML')->select(
      "SELECT DISTINCT Aktivitas FROM DBLOGFILE WHERE Aktivitas IS NOT NULL AND Aktivitas <> '' ORDER BY Aktivitas"
    );
    $listSumber = DB::connection('SML')->select(
      "SELECT DISTINCT Sumber FROM DBLOGFILE WHERE Sumber IS NOT NULL AND Sumber <> '' ORDER BY Sumber"
    );
    $listPemakai = DB::connection('SML')->select(
      "SELECT DISTINCT Pemakai FROM DBLOGFILE WHERE Pemakai IS NOT NULL AND Pemakai <> '' ORDER BY Pemakai"
    );

    return [
      'listAktivitas' => $listAktivitas,
      'listSumber' => $listSumber,
      'listPemakai' => $listPemakai,
    ];
  }

  // Dipanggil langsung saat halaman dibuka dan tiap kali filter berubah (Periode di
  // toolbar, atau Aktivitas/Transaksi/Pemakai di modal Filter) -- filter Tanggal
  // (selalu range, s/d), Aktivitas, Transaksi (Sumber), Pemakai.
  public function loadAll(Request $req) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', 'aktivitaspemakai');
    if (!$akses || !$akses->HASACCESS) {
        return [];
    }

    $where = [];
    $params = [];

    if ($req->tanggal_awal && $req->tanggal_akhir) {
      $where[] = 'Tanggal >= :tanggal_awal AND Tanggal < DATEADD(day, 1, :tanggal_akhir)';
      $params['tanggal_awal'] = $req->tanggal_awal;
      $params['tanggal_akhir'] = $req->tanggal_akhir;
    }

    if ($req->filled('aktivitas')) {
      $where[] = 'Aktivitas = :aktivitas';
      $params['aktivitas'] = $req->aktivitas;
    }

    if ($req->filled('sumber')) {
      $where[] = 'Sumber = :sumber';
      $params['sumber'] = $req->sumber;
    }

    if ($req->filled('pemakai')) {
      $where[] = 'Pemakai = :pemakai';
      $params['pemakai'] = $req->pemakai;
    }

    $sql = 'SELECT Tanggal, Pemakai, Sumber, NoBukti, Keterangan, Aktivitas FROM DBLOGFILE';
    if ($where) {
      $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY Tanggal DESC';

    return DB::connection('SML')->select($sql, $params);
  }
}
