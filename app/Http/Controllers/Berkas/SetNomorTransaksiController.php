<?php

namespace App\Http\Controllers\Berkas;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// use App\Http\Controllers\NewMenuController;

class SetNomorTransaksiController extends Controller
{

  public function index(Request $req) {

    // Daftar user & menu samping tidak dipakai halaman ini (layout newmasterTest mengambil menu
    // lewat AJAX /getmenu) - query-nya dimatikan supaya halaman lebih ringan.
    // $users = DB::connection("SML")->select('select * from DBFLPASS');
    // $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(1);

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    return view('berkas.setnomortransaksi' , [
      "menul0" => [],
      "periode" => $periode,
    ]);

  }

  public function loadAll () {
    $users = DB::connection("SML")->select('select * from DBFLPASS');
    return $users;

  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select("SELECT
    KODEUSAHA,
    NAMA,
    ALAMAT1,
    ALAMAT2,
    KOTA,
    Telpon,
    Fax,
    NAMAPKP,
    ALAMATPKP1,
    ALAMATPKP2,
    KOTAPKP,
    NPWP,
    TGLPENGUKUHAN,
    NAMAPKP1,
    ALAMATPKP21,
    ALAMATPKP22,
    KOTAPKP1,
    NPWP1,
    TGLPENGUKUHAN1,
    Direksi,
    Jabatan,
    email,
    L_Update
        FROM DBPERUSAHAAN");
    return $detail;
  }

    public function submitEdit(Request $req) {
    // Kolom teks DBPERUSAHAAN hampir semuanya NOT NULL, sedangkan Laravel mengubah isian kosong
    // jadi null - dulu mengosongkan Fax/Alamat 2/dst membuat simpan gagal (500). Semua teks
    // dikirim sebagai string (kosong = ''); tanggal kosong tetap null (kolomnya boleh null).
    $t = function ($nama) use ($req) { return trim((string) $req->input($nama)); };
    $tgl = function ($nama) use ($req) {
      $v = trim((string) $req->input($nama));
      return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : null;
    };

    if ($t('nama') === '') {
      return 'Nama perusahaan harus diisi';
    }
    // Batas panjang kolom DBPERUSAHAAN.
    $batas = ['nama' => 40, 'alamat1' => 100, 'alamat2' => 100, 'kota' => 40, 'telpon' => 30, 'fax' => 30, 'email' => 100,
      'namapkp' => 40, 'alamatpkp1' => 100, 'alamatpkp2' => 100, 'kotapkp' => 40, 'npwp' => 40,
      'namapkp1' => 40, 'alamatpkp21' => 100, 'alamatpkp22' => 100, 'kotapkp1' => 40, 'npwp1' => 40,
      'direksi' => 50, 'jabatan' => 50];
    foreach ($batas as $nama => $maks) {
      if (strlen($t($nama)) > $maks) {
        return 'Isian ' . $nama . ' maksimal ' . $maks . ' karakter';
      }
    }

    DB::connection('SML')->update(
        "UPDATE DBPERUSAHAAN SET
            NAMA = :nama,
            ALAMAT1 = :alamat1,
            ALAMAT2 = :alamat2,
            KOTA = :kota,
            Telpon = :telpon,
            Fax = :fax,
            email = :email,
            NAMAPKP = :namapkp,
            ALAMATPKP1 = :alamatpkp1,
            ALAMATPKP2 = :alamatpkp2,
            KOTAPKP = :kotapkp,
            NPWP = :npwp,
            TGLPENGUKUHAN = :tglpengukuhan,
            NAMAPKP1 = :namapkp1,
            ALAMATPKP21 = :alamatpkp21,
            ALAMATPKP22 = :alamatpkp22,
            KOTAPKP1 = :kotapkp1,
            NPWP1 = :npwp1,
            TGLPENGUKUHAN1 = :tglpengukuhan1,
            Direksi = :direksi,
            Jabatan = :jabatan,
            L_Update = GETDATE()
        ",
        [
            "nama"           => $t('nama'),
            "alamat1"        => $t('alamat1'),
            "alamat2"        => $t('alamat2'),
            "kota"           => $t('kota'),
            "telpon"         => $t('telpon'),
            "fax"            => $t('fax'),
            "email"          => $t('email'),
            "namapkp"        => $t('namapkp'),
            "alamatpkp1"     => $t('alamatpkp1'),
            "alamatpkp2"     => $t('alamatpkp2'),
            "kotapkp"        => $t('kotapkp'),
            "npwp"           => $t('npwp'),
            "tglpengukuhan"  => $tgl('tglpengukuhan'),
            "namapkp1"       => $t('namapkp1'),
            "alamatpkp21"    => $t('alamatpkp21'),
            "alamatpkp22"    => $t('alamatpkp22'),
            "kotapkp1"       => $t('kotapkp1'),
            "npwp1"          => $t('npwp1'),
            "tglpengukuhan1" => $tgl('tglpengukuhan1'),
            "direksi"        => $t('direksi'),
            "jabatan"        => $t('jabatan'),
        ]
    );

    return 1;
    }

}
