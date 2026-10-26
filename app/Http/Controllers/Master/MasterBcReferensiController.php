<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\DB;

// Master referensi Bea Cukai (Negara, Pelabuhan, Kontainer, Pos Tarif HS, Kategori Barang,
// Jenis Dokumen, Jenis Kemasan, Jenis Satuan). Satu controller + satu view untuk kedelapannya;
// tiap master tetap punya menu/href sendiri (masterbc<jenis>) supaya hak aksesnya bisa diatur
// terpisah di Set Pemakai. Nama tabel di config/beacukai.php.
//
// Aturan bersama:
// - Kolom kode tidak bisa diubah saat Edit (dokumen BC menyimpan kodenya sebagai teks).
// - Kode tidak boleh dobel (dicek di sini, juga untuk tabel yang tidak punya unique key).
// - Hapus ditolak kalau kodenya sudah dipakai entitas / dokumen BC 2.6.1.
class MasterBcReferensiController extends Controller
{

  // Definisi tiap master. kolom: [kolom DB, label, tipe teks|angka, panjang maks, kode?].
  // pakai: [tabel config, [kolom di tabel pemakai => kolom master], label] untuk cek Hapus.
  private function definisi ($jenis) {
    $t = config('beacukai.tables');
    $semua = [
      'negara' => [
        'judul' => 'Negara',
        'tabel' => $t['negara'],
        'kolom' => [
          ['KODENEGARA', 'Kode Negara', 'teks', 5, true],
          ['NAMANEGARA', 'Nama Negara', 'teks', 50, false],
        ],
        'pakai' => [
          ['entitas', ['IDNEGARA' => 'id'], 'Master Entitas'],
          ['bc261_barang', ['KODENEGARAASAL' => 'KODENEGARA'], 'Barang Dokumen BC 2.6.1'],
        ],
      ],
      'pelabuhan' => [
        'judul' => 'Pelabuhan',
        'tabel' => $t['pelabuhan']['table'],
        'kolom' => [
          ['KODEPELABUHAN', 'Kode Pelabuhan', 'teks', 10, true],
          ['NAMAPELABUHAN', 'Nama Pelabuhan', 'teks', 50, false],
        ],
        'pakai' => [],
      ],
      'kontainer' => [
        'judul' => 'Kontainer',
        'tabel' => $t['kontainer'],
        'kolom' => [
          ['KODEUKURANKONTAINER', 'Ukuran Kontainer', 'teks', 20, true],
          ['KODETIPEKONTAINER', 'Tipe Kontainer', 'teks', 20, true],
        ],
        'pakai' => [
          ['bc261_kontainer', ['KODEUKURANKONTAINER' => 'KODEUKURANKONTAINER', 'KODETIPEKONTAINER' => 'KODETIPEKONTAINER'], 'Peti Kemas Dokumen BC 2.6.1'],
        ],
      ],
      'postarif' => [
        'judul' => 'Pos Tarif HS',
        'tabel' => $t['hs']['table'],
        'kolom' => [
          ['POSTARIF', 'Pos Tarif', 'teks', 100, true],
          ['PERSENBM', 'BM (%)', 'angka', 0, false],
          ['PERSENPPN', 'PPN (%)', 'angka', 0, false],
          ['PERSENPPh', 'PPh (%)', 'angka', 0, false],
        ],
        'pakai' => [
          ['bc261_barang', ['POSTARIF' => 'POSTARIF'], 'Barang Dokumen BC 2.6.1'],
          ['bc261_bahanbaku', ['POSTARIF' => 'POSTARIF'], 'Bahan Baku Dokumen BC 2.6.1'],
        ],
      ],
      'kategoribarang' => [
        'judul' => 'Kategori Barang',
        'tabel' => $t['kategori_barang']['table'],
        'kolom' => [
          ['KODEKATEGORIBARANG', 'Kode Kategori', 'teks', 20, true],
          ['KETKATEGORIBARANG', 'Keterangan', 'teks', 200, false],
        ],
        'pakai' => [
          ['bc261_barang', ['KODEKATEGORIBARANG' => 'KODEKATEGORIBARANG'], 'Barang Dokumen BC 2.6.1'],
        ],
      ],
      'jenisdokumen' => [
        'judul' => 'Jenis Dokumen',
        'tabel' => $t['jenis_dokumen']['table'],
        'kolom' => [
          ['KODEJENISDOKUMEN', 'Kode Dokumen', 'teks', 20, true],
          ['NAMAJENISDOKUMEN', 'Nama Dokumen', 'teks', 200, false],
        ],
        'pakai' => [
          ['bc261_dokumen', ['KODEDOKUMEN' => 'KODEJENISDOKUMEN'], 'Dokumen Pelengkap BC 2.6.1'],
        ],
      ],
      'jeniskemasan' => [
        'judul' => 'Jenis Kemasan',
        'tabel' => $t['kemasan']['table'],
        'kolom' => [
          ['KODEJENISKEMASAN', 'Kode Kemasan', 'teks', 20, true],
          ['NAMAJENISKEMASAN', 'Nama Kemasan', 'teks', 200, false],
        ],
        'pakai' => [
          ['bc261_barang', ['KODEJENISKEMASAN' => 'KODEJENISKEMASAN'], 'Barang Dokumen BC 2.6.1'],
          ['bc261_kemasan', ['KODEJENISKEMASAN' => 'KODEJENISKEMASAN'], 'Kemasan Dokumen BC 2.6.1'],
        ],
      ],
      'jenissatuan' => [
        'judul' => 'Jenis Satuan',
        'tabel' => $t['satuan']['table'],
        'kolom' => [
          ['KODEJENISSATUAN', 'Kode Satuan', 'teks', 20, true],
          ['NAMAJENISSATUAN', 'Nama Satuan', 'teks', 200, false],
        ],
        'pakai' => [
          ['bc261_barang', ['KODESATUANBARANG' => 'KODEJENISSATUAN'], 'Barang Dokumen BC 2.6.1'],
          ['bc261_bahanbaku', ['KODESATUANBARANG' => 'KODEJENISSATUAN'], 'Bahan Baku Dokumen BC 2.6.1'],
        ],
      ],
    ];

    $def = $semua[$jenis] ?? null;
    if (!$def) {
      abort(404);
    }
    $def['jenis'] = $jenis;
    $def['href'] = 'masterbc' . $jenis;
    // Master yang semua kolomnya kode (Kontainer) tidak punya isian yang bisa diedit.
    $def['bisaEdit'] = collect($def['kolom'])->contains(fn ($k) => !$k[4]);
    return $def;
  }

  // Cek hak akses tombol (ISTAMBAH / ISKOREKSI / ISHAPUS) - pesan string bila ditolak.
  private function tolakAkses ($def, $hak) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses('', $def['href']);
    if (!$akses || !$akses->HASACCESS || !$akses->$hak) {
      return 'Anda tidak memiliki hak akses';
    }
    return null;
  }

  public function index(Request $req, $jenis) {

    $def = $this->definisi($jenis);
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    $akses = app('App\Http\Controllers\GlobalController')->getAkses('' , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterbcreferensi' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "def" => $def,
      "akses" => $akses
    ]);

  }

  public function loadAll (Request $req) {
    $def = $this->definisi($req->jenis);
    $kolom = implode(', ', array_column($def['kolom'], 0));
    $kode = implode(', ', array_column(array_filter($def['kolom'], fn ($k) => $k[4]), 0));
    return DB::connection('SML')->select("SELECT id, {$kolom} FROM {$def['tabel']} ORDER BY {$kode}");
  }

  public function spDetail (Request $req) {
    $def = $this->definisi($req->jenis);
    $kolom = implode(', ', array_column($def['kolom'], 0));
    return DB::connection('SML')->select("SELECT id, {$kolom} FROM {$def['tabel']} WHERE id = :id", ['id' => (int) $req->id]);
  }

  // Isian form → [kolom => nilai], atau string pesan bila tidak valid.
  private function isian ($def, Request $req, $termasukKode) {
    $nilai = [];
    foreach ($def['kolom'] as [$kolom, $label, $tipe, $maks, $isKode]) {
      if ($isKode && !$termasukKode) {
        continue;
      }
      $v = trim((string) $req->input($kolom, ''));
      if ($tipe === 'angka') {
        $v = str_replace(',', '', $v);
        if ($v === '') {
          $v = 0;
        } elseif (!is_numeric($v)) {
          return $label . ' harus berupa angka';
        }
        $nilai[$kolom] = (float) $v;
        continue;
      }
      if ($v === '') {
        return $label . ' harus diisi';
      }
      if (mb_strlen($v) > $maks) {
        return $label . ' maksimal ' . $maks . ' karakter';
      }
      $nilai[$kolom] = $v;
    }
    return $nilai;
  }

  public function spAdd (Request $req) {
    $def = $this->definisi($req->jenis);
    if ($pesan = $this->tolakAkses($def, 'ISTAMBAH')) {
      return $pesan;
    }

    $nilai = $this->isian($def, $req, true);
    if (is_string($nilai)) {
      return $nilai;
    }

    // Kode (atau pasangan kode, untuk Kontainer) tidak boleh dobel.
    $kode = array_filter($def['kolom'], fn ($k) => $k[4]);
    $where = implode(' AND ', array_map(fn ($k) => "{$k[0]} = ?", $kode));
    $check = DB::connection('SML')->select("SELECT top 1 id FROM {$def['tabel']} WHERE {$where}", array_map(fn ($k) => $nilai[$k[0]], array_values($kode)));
    if ($check) {
      return $def['judul'] . ' dengan kode ' . implode(' / ', array_map(fn ($k) => $nilai[$k[0]], $kode)) . ' sudah ada di database';
    }

    $kolom = implode(', ', array_keys($nilai));
    $tanda = implode(', ', array_fill(0, count($nilai), '?'));
    DB::connection('SML')->insert("INSERT INTO {$def['tabel']} ({$kolom}) VALUES ({$tanda})", array_values($nilai));
    return 1;
  }

  public function spEdit (Request $req) {
    $def = $this->definisi($req->jenis);
    if ($pesan = $this->tolakAkses($def, 'ISKOREKSI')) {
      return $pesan;
    }
    if (!$def['bisaEdit']) {
      return $def['judul'] . ' tidak punya isian yang bisa diubah';
    }

    // Kolom kode sengaja tidak ikut diubah.
    $nilai = $this->isian($def, $req, false);
    if (is_string($nilai)) {
      return $nilai;
    }

    $set = implode(', ', array_map(fn ($k) => "{$k} = ?", array_keys($nilai)));
    $edit = DB::connection('SML')->update("UPDATE {$def['tabel']} SET {$set} WHERE id = ?", array_merge(array_values($nilai), [(int) $req->id]));
    return $edit ? 1 : $def['judul'] . ' tidak ditemukan';
  }

  public function spDelete (Request $req) {
    $def = $this->definisi($req->jenis);
    if ($pesan = $this->tolakAkses($def, 'ISHAPUS')) {
      return $pesan;
    }

    $baris = DB::connection('SML')->selectOne("SELECT * FROM {$def['tabel']} WHERE id = :id", ['id' => (int) $req->id]);
    if (!$baris) {
      return $def['judul'] . ' tidak ditemukan';
    }
    $baris = (array) $baris;

    // Tabel dokumen BC yang belum dibuat dilewati.
    foreach ($def['pakai'] as [$kunci, $cocok, $label]) {
      $tabel = config("beacukai.tables.{$kunci}");
      if (!$tabel || !$this->tabelAda($tabel)) {
        continue;
      }
      $where = implode(' AND ', array_map(fn ($k) => "{$k} = ?", array_keys($cocok)));
      $check = DB::connection('SML')->select("SELECT top 1 1 as ada FROM {$tabel} WHERE {$where}", array_map(fn ($m) => $baris[$m], array_values($cocok)));
      if ($check) {
        return $def['judul'] . ' digunakan di ' . $label;
      }
    }

    DB::connection('SML')->delete("DELETE FROM {$def['tabel']} WHERE id = :id", ['id' => (int) $req->id]);
    return 1;
  }

  protected function tabelAda ($tabel) {
    $ada = DB::connection('SML')->selectOne('SELECT OBJECT_ID(?, ?) as id', [$tabel, 'U']);
    return $ada && $ada->id;
  }

}
