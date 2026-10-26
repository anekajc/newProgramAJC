<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\DB;

// Master Entitas Bea Cukai (Pengusaha TPB / Pemilik / Penerima Barang) - dipakai dokumen BC
// (mis. BC 2.6.1). Data entitas yang dikirim ke CEISA diambil dari tabel ini, bukan dari isian
// dokumen. Nama tabel di config/beacukai.php.
class MasterBcEntitasController extends Controller
{

  private function aksesMenu () {
    return app('App\Http\Controllers\GlobalController')->getAkses('', 'masterbcentitas');
  }

  // Cek hak akses tombol (ISTAMBAH / ISKOREKSI / ISHAPUS) - pesan string bila ditolak.
  private function tolakAkses ($hak) {
    $akses = $this->aksesMenu();
    if (!$akses || !$akses->HASACCESS || !$akses->$hak) {
      return 'Anda tidak memiliki hak akses';
    }
    return null;
  }

  private function tabel () {
    return config('beacukai.tables.entitas');
  }

  public function index(Request $req) {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    $akses = app('App\Http\Controllers\GlobalController')->getAkses('' , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);
    $negara = DB::connection('SML')->select('SELECT id, KODENEGARA, NAMANEGARA FROM ' . config('beacukai.tables.negara') . ' ORDER BY KODENEGARA');

    return view('master.masterbcentitas' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "negara" => $negara,
      "akses" => $akses
    ]);

  }

  public function loadAll () {
    return DB::connection('SML')->select('
      SELECT e.*, n.KODENEGARA, n.NAMANEGARA
      FROM ' . $this->tabel() . ' e
      LEFT JOIN ' . config('beacukai.tables.negara') . ' n ON n.id = e.IDNEGARA
      ORDER BY e.NAMAENTITAS');
  }

  // Isian form → parameter SQL. Tanggal kosong = null; kode bawaan CEISA bila kosong.
  private function isian (Request $req) {
    $teks = fn ($v) => ($v === null || trim((string) $v) === '') ? null : trim((string) $v);

    return [
      'kode' => $teks($req->kode),
      'nama' => trim((string) $req->nama),
      'alamat' => trim((string) $req->alamat),
      'jenisapi' => $teks($req->jenisapi) ?? '02',
      'jenisidentitas' => $teks($req->jenisidentitas) ?? '6',
      'idnegara' => (int) $req->idnegara,
      'status' => $teks($req->status) ?? '0',
      'nib' => $teks($req->nib),
      'nomoridentitas' => $teks($req->nomoridentitas),
      'nomorijin' => $teks($req->nomorijin),
      'tanggalijin' => $teks($req->tanggalijin),
      'niper' => $teks($req->niper),
    ];
  }

  private function cekIsian (array $p, $id = null) {
    if ($p['nama'] === '') {
      return 'Nama entitas harus diisi';
    }
    if ($p['alamat'] === '') {
      return 'Alamat entitas harus diisi';
    }
    if (!$p['idnegara']) {
      return 'Negara harus dipilih';
    }
    if ($p['kode'] !== null) {
      $check = DB::connection('SML')->select('SELECT id FROM ' . $this->tabel() . ' WHERE KODENTITAS = :kode AND id <> :id', ['kode' => $p['kode'], 'id' => (int) $id]);
      if ($check) {
        return 'Kode entitas sudah ada di database';
      }
    }
    return null;
  }

  public function spAdd (Request $req) {
    if ($pesan = $this->tolakAkses('ISTAMBAH')) {
      return $pesan;
    }

    $p = $this->isian($req);
    if ($pesan = $this->cekIsian($p)) {
      return $pesan;
    }

    DB::connection('SML')->insert('insert into ' . $this->tabel() . ' (KODENTITAS, NAMAENTITAS, ALAMATENTITAS, KODEJENISAPI, KODEJENISIDENTITAS, IDNEGARA, KODESTATUS, NIBENTITAS, NOMORIDENTITAS, NOMORIJINIDENTITAS, TANGGALIJINIDENTITAS, NIPERENTITAS)
      values (:kode, :nama, :alamat, :jenisapi, :jenisidentitas, :idnegara, :status, :nib, :nomoridentitas, :nomorijin, :tanggalijin, :niper)', $p);
    return 1;
  }

  public function spEdit (Request $req) {
    if ($pesan = $this->tolakAkses('ISKOREKSI')) {
      return $pesan;
    }

    $p = $this->isian($req);
    if ($pesan = $this->cekIsian($p, $req->id)) {
      return $pesan;
    }

    $edit = DB::connection('SML')->update('update ' . $this->tabel() . ' set KODENTITAS = :kode, NAMAENTITAS = :nama, ALAMATENTITAS = :alamat, KODEJENISAPI = :jenisapi,
        KODEJENISIDENTITAS = :jenisidentitas, IDNEGARA = :idnegara, KODESTATUS = :status, NIBENTITAS = :nib, NOMORIDENTITAS = :nomoridentitas,
        NOMORIJINIDENTITAS = :nomorijin, TANGGALIJINIDENTITAS = :tanggalijin, NIPERENTITAS = :niper
      where id = :id', $p + ['id' => (int) $req->id]);

    return $edit ? 1 : 'Entitas tidak ditemukan';
  }

  public function spDelete (Request $req) {
    if ($pesan = $this->tolakAkses('ISHAPUS')) {
      return $pesan;
    }

    // Entitas yang sudah dipilih di dokumen BC 2.6.1 tidak boleh dihapus (data kirim CEISA
    // diambil dari master ini). Dicek hanya bila tabel dokumennya sudah dibuat.
    $bc261 = config('beacukai.tables.bc261');
    $adaTabel = $bc261 ? DB::connection('SML')->selectOne('SELECT OBJECT_ID(:tabel, :tipe) as id', ['tabel' => $bc261, 'tipe' => 'U']) : null;
    if ($adaTabel && $adaTabel->id) {
      $check = DB::connection('SML')->select("SELECT top 1 1 as ada FROM {$bc261} WHERE SELECTENTITAS1 = :id1 OR SELECTENTITAS2 = :id2 OR SELECTENTITAS3 = :id3",
        ['id1' => (int) $req->id, 'id2' => (int) $req->id, 'id3' => (int) $req->id]);
      if ($check) {
        return 'Entitas digunakan di Dokumen BC 2.6.1';
      }
    }

    DB::connection('SML')->delete('delete from ' . $this->tabel() . ' where id = :id' , ['id' => (int) $req->id]);
    return 1;
  }

  public function spDetail (Request $req) {
    return DB::connection('SML')->select('SELECT * FROM ' . $this->tabel() . ' where id = :id' , ['id' => (int) $req->id]);
  }

}
