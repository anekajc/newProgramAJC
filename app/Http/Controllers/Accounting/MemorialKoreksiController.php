<?php


namespace App\Http\Controllers\Accounting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;





class MemorialKoreksiController extends Controller


{

  // Nama href untuk DBHEADERTABLE - dipatok, harus sama persis dengan MK_HREF di blade.
  const HREF = 'memorialkoreksi';

  // Kolom default tabel daftar Memorial/Koreksi. Nama key HARUS sama persis dengan alias
  // kolom di queryDaftar() karena dipakai juga sebagai nama field data di JS.
  // tipe: 0 = varchar, 1 = float, 2 = date.
  // IsOtorisasi1/OtoUser1/TglOto1 sengaja TIDAK dimasukkan - itu kolom teknis untuk filter
  // otorisasi dan kolom Oto/User Oto/Tgl Oto yang selalu ditambahkan lewat JS.
  private function kolomDefault () {
    return [
      'No Bukti'   => 0,
      'Tanggal'    => 2,
      'Trans'      => 0,
      'Perkiraan'  => 0,
      'Keterangan' => 0,
      'Jumlah Rp'  => 1,
    ];
  }

  // Rentang tanggal default = satu bulan penuh periode kerja user, sama seperti
  // PengajuanDPPController@periodeRange.
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  // Rentang tanggal yang diminta browser, dijatuhkan ke periode kerja bila tidak valid.
  private function rentangTanggal (Request $req, $periode) {
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }
    return [$tglawal, $tglakhir];
  }

  /**
   * Susunan/tampil/desimal kolom milik user untuk tabel daftar Memorial/Koreksi.
   * Sama persis polanya dengan PengajuanDPPController@headerTable supaya blade bisa
   * memakai ReportTable (geser & sembunyikan kolom) lewat route 'saveheadertable' generik.
   */
  private function headerTable ($reset = false) {
    $username = \Auth::user()->username;

    if ($reset) {
      DB::connection("SML")->update(
        "delete from DBHEADERTABLE where username = :username and href = :href",
        ["username" => $username, "href" => self::HREF]
      );
    }

    $headertable = DB::connection("SML")->select(
      "select * from dbheadertable where href = :href and username = :username",
      ["username" => $username, "href" => self::HREF]
    );

    $headertableheader = [];
    $headertablevalue = [];
    $isnumberheadertable = [];
    $headerisshown = [];
    $isparsed = 0;

    if (count($headertable) > 0) {
      $isnumberheadertable = json_decode($headertable[0]->isnumber);
      $headertablevalue    = json_decode($headertable[0]->value);
      $headertableheader   = json_decode($headertable[0]->header);
      $headerisshown       = json_decode($headertable[0]->isshown);
    } else {
      $isparsed = 1;
      foreach ($this->kolomDefault() as $key => $tipe) {
        array_push($headertablevalue, $key);
        array_push($headertableheader, $key);
        array_push($headerisshown, 1);
        $isnumberheadertable[] = $tipe;
      }
    }

    $aliasOrdered = [];
    foreach ($headertablevalue as $header) {
      array_push($aliasOrdered, ["value" => $header, "alias" => $header]);
    }

    // Desimal dititipkan sebagai JSON array di kolom `tipe` DBHEADERTABLE.
    $tersimpan = [];
    if (count($headertable) > 0) {
      $decoded = json_decode($headertable[0]->tipe);
      if (is_array($decoded)) { $tersimpan = $decoded; }
    }

    $desimal = [];
    foreach ($isnumberheadertable as $i => $tipe) {
      if (isset($tersimpan[$i]) && is_numeric($tersimpan[$i])) {
        $nilai = (int) $tersimpan[$i];
        if ($nilai < 0) { $nilai = 0; }
        if ($nilai > 4) { $nilai = 4; }
        array_push($desimal, $nilai);
      } else {
        array_push($desimal, ((int) $tipe === 1) ? 2 : 0);
      }
    }

    return [
      "aliasordered"      => $aliasOrdered,
      "headertableheader" => $headertableheader,
      "headertablevalue"  => $headertablevalue,
      "isnumeric"         => $isnumberheadertable,
      "isshown"           => $headerisshown,
      "isparsed"          => $isparsed,
      "desimal"           => $desimal,
    ];
  }

  // Dipanggil tombol "Reset kolom" di bar kolom tersembunyi (ReportTable).
  public function resetHeader () {
    return $this->headerTable(true);
  }

  // Daftar Memorial/Koreksi dalam rentang tanggal, satu baris per NoBukti, terbaru di atas.
  // Kolom Perkiraan diambil dari baris detail pertama (dbTransaksi.Urut) tiap NoBukti -
  // memorial bisa punya banyak perkiraan, jadi nilai ini bersifat indikatif.
  private function queryDaftar ($tglawal, $tglakhir, $username) {
    return DB::connection("SML")->select("
select  A.NoUrut, A.NoBukti,
        A.NoBukti                            as [No Bukti],
        convert(varchar(10), A.Tanggal, 23)  as [Tanggal],
        A.TipeTransHd                        as [Trans],
        isnull((select top 1 B2.Perkiraan from dbTransaksi B2
                where B2.NoBukti = A.NoBukti order by B2.Urut), '') as [Perkiraan],
        isnull(A.Note, '')                   as [Keterangan],
        sum((B.Debet) * B.Kurs)              as [Jumlah Rp],
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1
from dbTrans A
left outer join dbTransaksi B on B.NoBukti=A.NoBukti
where A.Tanggal between :tglawal and :tglakhir and
 A.TipeTransHd in ('BMM','BJK') and isnull(A.Jenis,0)=0
       And
       A.nobukti Not in (
        select A.NoBukti
        from dbtrans A
        left outer join dbTransaksi b on a.NoBukti=b.NoBukti
        where A.TipeTransHd in  ('BMM','BJK')
        and
        (B.Perkiraan not in (select Perkiraan from DBAKSESPERKIRAAN where UserID=:username1)
        or
        B.lawan not in (select Perkiraan from DBAKSESPERKIRAAN where UserID=:username2))
        group by A.NoBukti
       )
group by A.NoUrut, A.NoBukti, A.Tanggal, A.Note, A.TipeTransHd,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1
order by A.Tanggal desc, A.NoBukti desc
" , [
      "tglawal" => $tglawal,
      "tglakhir" => $tglakhir,
      "username1" => $username,
      "username2" => $username,
    ]);
  }

  public function index(Request $req) {
    $kodemenu = '02015';

    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu,$req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);
    $username = \Auth::user()->username;

    list($mkTglAwal, $mkTglAkhir) = $this->periodeRange($periode);

    $devisi = DB::connection("SML")->select("select devisi, namadevisi from dbdevisi");

    return view('accounting.memorialkoreksi' , array_merge([
      "menul0" => $menul0,
      "periode" => $periode,
      "akses" => $akses,
      "devisi" => $devisi,
      "mkTglAwal" => $mkTglAwal,
      "mkTglAkhir" => $mkTglAkhir,
    ], $this->headerTable()));

  }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakMemo ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function loadAll (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->rentangTanggal($req, $periode);

    $tempOutstanding = $this->queryDaftar($tglawal, $tglakhir, $username);

    return array_merge(["tempOutstanding" => $tempOutstanding], $this->headerTable());
  }



  public function getDetail (Request $req ) {



        $tempOutstanding = DB::connection("SML")->select("select a.*,b.keterangan as namaPerkiraan,d.Keterangan NamaLawan, '' MyID,
case when a.valas<>'IDR' then a.debet+ a.kredit
     else 0
end as Jumlah,
a.DebetRp+a.KreditRp as JumlahRp,c.NamaDevisi,
Case when a.TPHC='C' then '[C]ash'
     When a.TPHC='T' then '[T]ransfer'
     when a.TPHC='H' then '[H]utang Giro'
     when a.TPHC='P' then '[P]iutang Giro'
     else ''
end MyTPHC,e.NamaBag,f.nourut
from dbtransaksi a
     left outer join dbperkiraan b on a.perkiraan=b.perkiraan
     left outer join dbdevisi c on c.Devisi=a.Devisi
     left outer join dbPerkiraan d on d.Perkiraan=a.Lawan
     left outer join dbBagian e on e.kodebag=a.kodebag
     left outer join dbTrans f on f.nobukti=a.nobukti
where a.nobukti= :nobukti
Order by a.Nobukti,a.Urut
        " , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }


    public function listPerkiraan (Request $req) {

          $username = \Auth::user()->username;
          $listData = [];

          if ($req->transaksi == 'BMM') {
            $listData = DB::connection('SML')->select("
            select a.Perkiraan, a.Keterangan,a.Simbol,C.Kode, C.IsLokalOrExim from dbPerkiraan a
                        left Outer join dbAksesPerkiraan b on b.Perkiraan=a.Perkiraan
                         Left Outer Join (select perkiraan,kode,IsLokalOrExim from dbPOSTHUTPIUT group by perkiraan,kode,IsLokalOrExim)  C on A.Perkiraan=C.Perkiraan
                        where a.Tipe=1 and b.UserID = :username
                        and a.perkiraan not in (select Perkiraan from DBPOSTHUTPIUT where Kode='PT')

                        order by a.Perkiraan" , [ "username" => $username ]);

          } else {
            $listData = DB::connection('SML')->select("
            select a.Perkiraan, a.Keterangan, a.Simbol,C.Kode, C.IsLokalOrExim from dbPerkiraan a
                  left Outer join dbAksesPerkiraan b on b.Perkiraan=a.Perkiraan
                   Left Outer Join (select perkiraan,kode,IsLokalOrExim from dbPOSTHUTPIUT group by perkiraan,kode,IsLokalOrExim)  C on A.Perkiraan=C.Perkiraan
                  where a.Tipe=1 and b.UserID = :username
                  and a.perkiraan not in (select Perkiraan from DBPOSTHUTPIUT where Kode='HT' and Perkiraan not in ('116100','21203') )

                  order by a.Perkiraan" , [ "username" => $username ]);


          }



      return $listData;
    }

    public function listValas (Request $req) {

      $listData = DB::connection('SML')->select("select * from DBVALAS");
      return $listData;
    }

  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbtrans set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbtrans set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL  where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'MK', $req->nobukti, $req->pket, 0, 'DBTRANS');
    return $res;
  }

  public function spAdd (Request $req) {

    $username = \Auth::user()->username;

    $jmlrecord = $req->jmlrecord;


    // return [
    //   $req->choice,
    //   $req->nobukti,
    //   $req->nourut,
    //   $req->tanggal ,
    //   $req->note  ?? '',
    //   0,
    //   $req->kodedevisi ,
    //   $req->perkiraan ,
    //   $req->lawan ,
    //   $req->keterangan  ?? '', // 10
    //   $req->keterangan2  ?? '',
    //   $req->debet,
    //   $req->kredit,
    //   $req->valas,
    //   $req->kurs,
    //   $req->debetRp,
    //   $req->kreditRp,
    //
    //   $req->transaksi,
    //   $req->tphc,
    //   $req->custsuppP ?? '',
    //   $req->custsuppL ?? '', //20
    //   $req->urut,
    //   $req->noaktivaP ?? '',
    //   $req->noaktivaL ?? '',
    //   $req->statusaktivaP ?? '',
    //   $req->statusaktivaL ?? '',
    //   $req->nobon ?? '',
    //   $req->kodebag ?? '',
    //   $req->kodeP ?? '',
    //   $req->kodeL ?? '', // 30
    //   $req->statusgiro ?? '', // 30
    //   $req->simbol ?? '', // 30
    //   $username,
    //   $req->notitipan ?? '',
    //   $req->uruttitipan,
    //   $req->keterangandetail ?? ''
    //
    // ];

    if ($jmlrecord == 0 ) {
      $check = DB::connection('SML')->select('select * from dbtrans where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }

        DB::connection('SML')->statement('exec sp_TransaksiMemorial ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
          $req->choice,
          $req->nobukti,
          $req->nourut,
          $req->tanggal ,
          $req->note  ?? '',
          0,
          $req->kodedevisi ,
          $req->perkiraan ,
          $req->lawan ,
          $req->keterangan  ?? '', // 10
          $req->keterangan2  ?? '',
          $req->debet,
          $req->kredit,
          $req->valas,
          $req->kurs,
          $req->debetRp,
          $req->kreditRp,

          $req->transaksi,
          $req->tphc,
          $req->custsuppP ?? '',
          $req->custsuppL ?? '', //20
          $req->urut,
          $req->noaktivaP ?? '',
          $req->noaktivaL ?? '',
          $req->statusaktivaP ?? '',
          $req->statusaktivaL ?? '',
          $req->nobon ?? '',
          $req->kodebag ?? '',
          $req->kodeP ?? '',
          $req->kodeL ?? '', // 30
          $req->statusgiro ?? '', // 30
          $req->simbol ?? '', // 30
          $username,
          $req->notitipan ?? '',
          $req->uruttitipan,
          $req->keterangandetail ?? ''

        ]);

        // $jmlrecord = 1;



      // }

      return 1;

  }

  public function spkoreksi (Request $req) {

    $username = \Auth::user()->username;
    $jmlrecord = $req->jmlrecord;

//     select * from dbdph where NoBukti like '%0525%'
//


    DB::connection('SML')->statement('exec sp_dph ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->nobukti,
      $req->nourut,
      $req->tanggal ,
      $req->valas,
      '',
      $req->tipe ,
      $req->urut,
      $req->kodecustsupp, //
      $req->nofaktur, //
      $req->dibayar, //
      NULL,
      -1 ,
      $req->perkiraan, //
      $req->kl, //
      $req->lb, //
      0 ,
      0 , //
      $req->noinvoice ? $req->noinvoice : '', //
      $req->tglinvoice, //
      $req->pcopy, //
      1,
      $username

    ]);


  }




}
