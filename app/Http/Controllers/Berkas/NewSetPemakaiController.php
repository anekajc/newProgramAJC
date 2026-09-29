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

class NewSetPemakaiController extends Controller
{

  public function index(Request $req) {

    // Daftar user tidak lagi dirender di server: tabel & kartu ringkasan diisi loadAll()
    // lewat AJAX, jadi tidak ada dua versi baris (Blade vs JS) yang susunan kolomnya berbeda.
    // Menu samping layout newmasterTest diambil lewat AJAX /getmenu, jadi getMenuL0() (3 query
    // join DBFLMENUWEB) juga tidak perlu - makin sedikit query, makin kecil peluang halaman
    // jatuh ke 500 saat koneksi ke SQL Server sedang lambat.
    // $users = DB::connection("SML")->select('select * from DBFLPASS');
    // $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(1);

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    return view('berkas.newsetpemakai' , [
      "menul0" => [],
      "periode" => $periode,
    ]);

  }

  public function loadAll () {
    $users = DB::connection("SML")->select('select USERID, username, FullName, TINGKAT, STATUS, kodeBag, KodeJab, KodeKasir, [limit], keynik from DBFLPASS order by USERID');
    return $users;

  }

  public function detailUser (Request $req ) {

    $list = DB::connection("SML")->select('select USERID, username, FullName, TINGKAT, STATUS, kodeBag, KodeJab, KodeKasir, [limit], keynik from DBFLPASS where username = :username ' ,["username" => $req->username]);
    return $list;

  }


  public function listAkses (Request $req) {
    $values = [
        $req->userid,
      ];

    // return [$values];  
    DB::connection('SML')->update('exec sp_updateMenuWeb1 ?',$values);


    $listAkses = DB::connection("SML")->select('select a.* , b.* from DBMENUWEB a join DBFLMENUWEB b ON a.KODEMENU = b.L1 where b.USERID = :username order by a.KODEMENU' , ['username' => $req->userid]);

    return $listAkses;
  }

  public function listAksesReport (Request $req) {
    //   select * from DBMENUREPORT
    //
    // select * from DBFLMENUREPORT
    $values = [
        $req->userid,
      ];
    DB::connection('SML')->update('exec sp_updateMenuReportWeb1 ?',$values);


    $listAksesReport = DB::connection("SML")->select('select a.* , b.* from DBMENUREPORTWEB a join DBFLMENUREPORT b on a.KODEMENU = b.L1 where b.UserID = :username order by a.KODEMENU', ['username' => $req->userid]);
    return $listAksesReport;


  }

  public function listCoa (Request $req) {
    $tes = DB::connection("SML")->update('update DBPERKIRAAN set iskirim = 0');
    $listCoa = DB::connection("SML")->select('select Perkiraan, Keterangan from DBPERKIRAAN where Perkiraan not in ( select Perkiraan from DBAKSESPERKIRAAN where UserID = :username ) order by Perkiraan', ['username' => $req->username]);
    $listAksesCoa = DB::connection("SML")->select('select a.Keterangan , b.Perkiraan from DBAKSESPERKIRAAN b join DBPERKIRAAN a on a.Perkiraan = b.Perkiraan where b.UserID = :username order by b.Perkiraan', ['username' => $req->username]);

    return [
      'listCoa' => $listCoa, 'listAksesCoa' => $listAksesCoa, 'tes'=>$tes
    ];
  }

  public function updateIsKirimCOA (Request $req) {


      $tes = DB::connection("SML")->update('update DBPERKIRAAN set iskirim = :nilai where Perkiraan = :perkiraan' , ['perkiraan' => $req->perkiraan, 'nilai' => $req->nilai]);
      return $tes;
  }

  public function updateAddAksesCOA (Request $req) {
    // insert into dbaksesperkiraan
    // Select '+QuotedStr(Myuser)+',Perkiraan from dbperkiraan
    // where Tipe=1 and perkiraan not in(select perkiraan from dbaksesperkiraan where userid='+QuotedStr(Myuser)+')
    // insert into DBAKSESPERKIRAAN select 'SA' , Perkiraan from DBPERKIRAAN where iskirim=1 and Perkiraan not in (select perkiraan from dbaksesperkiraan where userid='SA')
    $tes = DB::connection("SML")->insert('insert into DBAKSESPERKIRAAN select :username , Perkiraan from DBPERKIRAAN where iskirim=1 and Perkiraan not in (select perkiraan from dbaksesperkiraan where userid=:username1)', ['username' => $req->username , 'username1' => $req->username]);
    return json_encode($tes);

  }

  public function updateAddAllAksesCOA (Request $req) {
    $tes = DB::connection("SML")->insert('insert into DBAKSESPERKIRAAN select :username , Perkiraan from DBPERKIRAAN where Perkiraan not in (select perkiraan from dbaksesperkiraan where userid=:username1)' , ['username' => $req->username , 'username1' => $req->username]);
    return json_encode($tes);
  }

  public function deleteAllAksesCOA (Request $req) {
    // delete from DBAKSESPERKIRAAN where UserID= 'SA'

    $tes = DB::connection("SML")->update('delete from DBAKSESPERKIRAAN where UserID= :username' , ['username' => $req->username ]);

    return $tes;
  }


  public function deleteUser (Request $req) {

    if (!$req->username) {
      return 'User belum dipilih';
    }

    // User yang sedang login tidak boleh menghapus dirinya sendiri - sesi yang sedang
    // berjalan langsung kehilangan data user-nya.
    if (strcasecmp(trim($req->username), trim(\Auth::user()->username)) === 0) {
      return 'User yang sedang dipakai login tidak bisa dihapus';
    }
    // Sama dengan pengaman Sp_FLpassWEB choice 'D' (UserID<>'SA').
    if (strcasecmp(trim($req->username), 'SA') === 0) {
      return 'User SA tidak bisa dihapus';
    }

      $tes = DB::connection("SML")->update('delete from dbflpass where username= :username' , ['username' => $req->username]);

    if (!$tes) {
      return 'User ' . $req->username . ' tidak ditemukan';
    }

    return 1;
  }


  public function deleteAksesCOA (Request $req) {
    $perkiraan = $req->perkiraan;
    $username = $req->username;

    // Tanpa pilihan, $perkiraan null dan foreach di bawah membuat 500.
    if (!is_array($perkiraan) || !$perkiraan) {
      return 'Pilih perkiraan yang akan dihapus aksesnya';
    }
    // $tes = DB::connection("SML")->update('delete from DBAKSESPERKIRAAN where UserID= :username and Perkiraan = :perkiraan' , ['username' => $req->username, 'perkiraan' => $req->perkiraan ]);
    // delete from DBAKSESPERKIRAAN where UserID= 'SA' and Perkiraan = '00'

    foreach ($perkiraan as $p) {
      // code...
      $tes = DB::connection("SML")->update('delete from DBAKSESPERKIRAAN where UserID= :username and Perkiraan = :perkiraan' , ['username' => $username, 'perkiraan' => $p['Perkiraan'] ]);
    }
    return 1;
  }

  public function spUpdateAksesReport (Request $req) {
    $username = $req->input('username');

    $data = $req->input('tempData');

    // DB::connection('SML')->statement('delete	DBFLMENUREPORT where UserID = :USERID',['USERID' => $username ]);

    // foreach ($data as $d) {
      $values = [
         $username,
         $data['KODEMENU'],
         $data['HASACCESS'],
         $data['ISDESIGN'],
         $data['ISEXPORT']
        ];
      DB::connection('SML')->update('exec SP_Flmenureportweb1 ?,?,?,?,?',$values);
    // }

    return 1;
  }



  public function spUpdateAkses (Request $req) {

    $username = $req->input('username');

    $data = $req->input('tempData');

    // DB::connection('SML')->statement('delete	DBFLMENUWEB where USERID = :USERID',['USERID' => $username ]);

    // foreach ($data as $d) {
      $values = [
         $username,
         $data['KODEMENU'],
         $data['HASACCESS'],
         $data['ISTAMBAH'],
         $data['ISKOREKSI'],
         $data['ISHAPUS'],
         $data['ISCETAK'],
         $data['ISEXPORT'],
         $data['ISOTO1'],
         $data['ISOTO2'],
         $data['ISOTO3'],
         $data['ISOTO4'],
         $data['ISOTO5'],
         $data['ISBATAL'],
         ''
        ];
      DB::connection('SML')->update('exec SP_FlmenuWeb1 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
    // }

    return 1;


  }

  public function spUpdateAksesHeader (Request $req) {

    // $username = $req->input('username');

    // $data = $req->input('tempData');


    // DB::connection('SML')->statement('delete	DBFLMENUWEB where USERID = :USERID',['USERID' => $username ]);


    // foreach ($data as $d) {
      $values = [
         $req->input('headermenu'),
         $req->input('nilai'),
         $req->input('username'),
         $req->input('field'),
        ];
      DB::connection('SML')->update('exec sp_updatesetmenuweb1 ?,?,?,?',$values);
    // }

    return 1;


  }

  public function loadKaryawan (){
    $list = DB::connection('SML')->select('SELECT NIK, Nama FROM DBKARYAWAN');

    return $list;
  }

  public function loadDepartemen (){
    $list = DB::connection('SML')->select('SELECT KDDEP,NMDEP FROM DBDEPART');

    return $list;
  }

  public function loadJabatan (){
    $list = DB::connection('SML')->select('SELECT KODEJAB, NamaJab FROM DBJABATAN');

    return $list;
  }

  public function submitAdduser (Request $req) {
    $choice = $req->input('choice');
    $user = trim((string) $req->input('user'));

    if ($choice !== 'I' && $choice !== 'U') {
      return 'Mode simpan tidak dikenal';
    }
    if ($user === '') {
      return 'User harus diisi';
    }
    if (trim((string) $req->input('namaLengkap')) === '') {
      return 'Nama Lengkap harus diisi';
    }
    // Batas panjang & tipe mengikuti parameter Sp_FLpassWEB - nilai yang melebihi membuat SP
    // gagal (500) atau terpotong diam-diam.
    if (strlen($user) > 15) {
      return 'User maksimal 15 karakter';
    }
    if (strlen(trim((string) $req->input('namaLengkap'))) > 50) {
      return 'Nama Lengkap maksimal 50 karakter';
    }
    if (trim((string) $req->input('departemen')) === '' || strlen((string) $req->input('departemen')) > 15) {
      return 'Departemen harus diisi (maksimal 15 karakter)';
    }
    if (trim((string) $req->input('jabatan')) === '' || strlen((string) $req->input('jabatan')) > 15) {
      return 'Jabatan harus diisi (maksimal 15 karakter)';
    }
    if (strlen((string) $req->input('kodeKasir')) > 3) {
      return 'Kode Kasir maksimal 3 karakter';
    }
    if (!in_array((string) $req->input('level'), ['0', '1', '2'], true)) {
      return 'Level tidak dikenal';
    }
    if (!in_array((string) $req->input('status'), ['0', '1'], true)) {
      return 'Status tidak dikenal';
    }
    if ($req->input('limit') !== null && $req->input('limit') !== '' && (!is_numeric($req->input('limit')) || $req->input('limit') < 0)) {
      return 'Limit harus angka 0 atau lebih';
    }
    // @Keynik bertipe integer. Saat edit (U) SP tidak mengubah keynik, jadi hanya dicek saat tambah.
    if ($choice == 'I' && !ctype_digit((string) $req->input('nik'))) {
      return 'NIK harus berupa angka';
    }

    $ada = DB::connection('SML')->select(
      'select top 1 USERID from DBFLPASS where USERID = :userid or username = :username',
      ['userid' => $user, 'username' => $user]
    );

    if ($choice == 'I') {
      // Dulu tidak dicek: user yang sudah ada bisa "ditambah" lagi dan diam-diam tertimpa SP.
      if ($ada) {
        return 'User ' . $user . ' sudah ada';
      }
      if ((string) $req->input('password') === '') {
        return 'Password harus diisi';
      }
    } else if (!$ada) {
      return 'User ' . $user . ' tidak ditemukan';
    }

    $hashedPassword = 'xx';
    if ( $req->input('choice') == 'I') {

      $hashedPassword = Hash::make($req->input('password'));

    }

   //   [
   //     $req->input('choice'),
   //     $req->input('user'),
   //     'dariweb' ,
   //     $req->input('level'),
   //     $req->input('status'),
   //     $req->input('namaLengkap'),
   //     $req->input('departemen'),
   //     $req->input('jabatan'),
   //      $req->input('kodeKasir'),
   //      '',
   //     $req->input('nik'),
   //     $req->input('limit'),
   //     $hashedPassword
   // ];

  $check = DB::connection('SML')->update("EXEC Sp_FLpassWEB ?,?,?,?,?,?,?,?,?,?,?,?,?",
   [
      $req->input('choice'),
      $user,
      'dariweb' ,
      $req->input('level'),
      $req->input('status'),
      $req->input('namaLengkap'),
      $req->input('departemen'),
      $req->input('jabatan'),
      // Kode Kasir boleh kosong; null dari form kosong dikirim sebagai '' seperti Limit 0.
      (string) $req->input('kodeKasir'),
      '',
      // U: keynik tidak dipakai SP (baris update-nya di-comment di SP), dikirim apa adanya/0.
      ctype_digit((string) $req->input('nik')) ? (int) $req->input('nik') : 0,
      is_numeric($req->input('limit')) ? $req->input('limit') : 0,
      $hashedPassword
  ]);

  // Sp_FLpassWEB tidak membuat baris DBPERIODE, sedangkan layout membaca $periode->bulan di
  // setiap halaman - tanpa baris ini user baru langsung kena 500 begitu login (kasus LEON &
  // MSYAIDI). Formatnya sama dengan baris lain: userid & user_id = user, periode = bulan berjalan.
  if ($choice == 'I') {
    DB::connection('SML')->insert(
      'insert into DBPERIODE (userid, bulan, tahun, user_id)
       select :userid, cast(MONTH(GETDATE()) as varchar(2)), cast(YEAR(GETDATE()) as varchar(4)), :username
       where not exists (select 1 from DBPERIODE where user_id = :cek)',
      ['userid' => $user, 'username' => $user, 'cek' => $user]
    );
  }

  return 1;

}

}
