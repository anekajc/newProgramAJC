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

  public function index(Request $req) {
    $kodemenu = '02015';

    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu,$req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }




    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();





    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);
    $username = \Auth::user()->username;

    $tempOutstanding = DB::connection("SML")->select("
declare @Tahun int, @Bulan int ,@IDUser Varchar(20)

select @Tahun= :tahun , @Bulan= :bulan , @IdUser= :username

select  A.NoUrut, A.NoBukti, A.Tanggal, A.Note, '' Devisi, '' Perkiraan, A.TipeTransHd,
        sum(case when B.Valas='IDR' then 0.00 else B.Debet+B.Kredit end) TotalD,
        sum((B.Debet)*B.Kurs) TotalRp,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5
from dbTrans A
left outer join dbTransaksi B on B.NoBukti=A.NoBukti
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan and
 A.TipeTransHd in ('BMM','BJK') and isnull(A.Jenis,0)=0
       And
       A.nobukti Not in (
        select A.NoBukti
        from dbtrans A
        left outer join dbTransaksi b on a.NoBukti=b.NoBukti
        where A.TipeTransHd in  ('BMM','BJK')
        and
        (B.Perkiraan not in (select Perkiraan from DBAKSESPERKIRAAN where UserID=@Iduser)
        or
        B.lawan not in (select Perkiraan from DBAKSESPERKIRAAN where UserID=@Iduser))
        group by A.NoBukti
       )
group by A.NoUrut, A.NoBukti, A.Tanggal, A.Note, A.TipeTransHd,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5
Order by A.Nobukti
" , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan , "username" => $username]);
        // $tempOutstanding = [];
        // foreach ($outstanding as $p) {
        //   // code...
        //   array_push($tempOutstanding, $p);
        // }
        //

        $collection1 = collect($tempOutstanding)->groupBy('NoBukti');
        $tempOutstanding1 = [];
        foreach ($collection1 as $p) {
          // code...
          array_push($tempOutstanding1, $p);
        }


            $devisi = DB::connection("SML")->select("select devisi, namadevisi from dbdevisi");


    return view('accounting.memorialkoreksi' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding1,
      "akses" => $akses,
      "devisi" => $devisi
    ]);

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

  public function loadAll () {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tempOutstanding = DB::connection("SML")->select("
        declare @Tahun int, @Bulan int ,@IDUser Varchar(20)

        select @Tahun= :tahun , @Bulan= :bulan , @IdUser= :username

        select  A.NoUrut, A.NoBukti, A.Tanggal, A.Note, '' Devisi, '' Perkiraan, A.TipeTransHd,
                sum(case when B.Valas='IDR' then 0.00 else B.Debet+B.Kredit end) TotalD,
                sum((B.Debet)*B.Kurs) TotalRp,
        	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
        	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
        	A.IsOtorisasi5, A.OtoUser5, A.TglOto5
        from dbTrans A
        left outer join dbTransaksi B on B.NoBukti=A.NoBukti
        where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan and
         A.TipeTransHd in ('BMM','BJK') and isnull(A.Jenis,0)=0
               And
               A.nobukti Not in (
                select A.NoBukti
                from dbtrans A
                left outer join dbTransaksi b on a.NoBukti=b.NoBukti
                where A.TipeTransHd in  ('BMM','BJK')
                and
                (B.Perkiraan not in (select Perkiraan from DBAKSESPERKIRAAN where UserID=@Iduser)
                or
                B.lawan not in (select Perkiraan from DBAKSESPERKIRAAN where UserID=@Iduser))
                group by A.NoBukti
               )
        group by A.NoUrut, A.NoBukti, A.Tanggal, A.Note, A.TipeTransHd,
        	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
        	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
        	A.IsOtorisasi5, A.OtoUser5, A.TglOto5
        Order by A.Nobukti
        " , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan , "username" => $username]);


        $collection1 = collect($tempOutstanding)->groupBy('NoBukti');
        $tempOutstanding1 = [];
        foreach ($collection1 as $p) {
          // code...
          array_push($tempOutstanding1, $p);
        }
    return ["tempOutstanding" => $tempOutstanding1];
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
