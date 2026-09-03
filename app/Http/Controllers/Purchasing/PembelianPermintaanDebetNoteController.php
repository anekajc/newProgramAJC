<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class PembelianPermintaanDebetNoteController extends Controller


// contoh desktop ce006
// sp_ProsesPostingHutPiut
// sp_ProsesPostingJurnalOto
{

  public function index(Request $req) {
    $kodemenu = '0306';
    $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu, $req->path)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);
    $tempOutstanding = DB::connection("SML")->select("
    select a.* , b.NAMACUSTSUPP from dbdebetnote a join DBCUSTSUPP b on a.KodeSupp = b.KODECUSTSUPP
    where Month(a.TANGGAL) = :bulan and YEAR(a.Tanggal) = :tahun
    ",["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

    return view('purchasing.pembelianpermintaandebetnote' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses
    ]);

  }

  public function loadAll()
{
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $data = collect(DB::connection("SML")->select("
        SELECT 
            a.NoBukti,
            a.Tanggal,
            a.IsOtorisasi1,
            a.TglOto1,
            a.OtoUser1,
            a.KodeSupp,
            b.NAMACUSTSUPP as NamaCustSupp
        FROM 
            dbdebetnote a 
        JOIN 
            DBCUSTSUPP b ON a.KodeSupp = b.KODECUSTSUPP
        WHERE 
            MONTH(a.TANGGAL) = :bulan 
            AND YEAR(a.Tanggal) = :tahun
        ORDER BY 
            a.TANGGAL DESC
    ", [
        "bulan" => $periode->bulan,
        "tahun" => $periode->tahun
    ]));

    $belumOto = $data->filter(function ($item) {
        return is_null($item->IsOtorisasi1) || $item->IsOtorisasi1 == 0;
    })->values();

    $sudahOto = $data->filter(function ($item) {
        return $item->IsOtorisasi1 == 1;
    })->values();

    return response()->json([
        'listData1' => $belumOto,
        'listData2' => $sudahOto
    ]);
}

  public function getDetail (Request $req ) {
        $tempOutstanding = DB::connection("SML")->select("Select  c.KodeSupp,d.NamaCustSupp,d.Alamat1,Right(a.noBukti,4)NoUrut,a.Urut,a.NoBukti,c.tanggal,Keterangan,A.NoInv, b.Saldo, b.SaldoD,
        a.Nilai,
        Case when a.kodevls='IDR' then 0.00 else a.Nilai end NilaiValas,
        a.kodeVls, a.Kurs, a.NilaiRp , c.IsOtorisasi1
  From  dbDebetNoteDet a
  Left Outer Join dbDebetNote c On c.NoBukti=a.NoBukti
  Left Outer Join (select a.NoFaktur,Sum(Saldo) Saldo, Sum(SaldoD) SaldoD
                  from dbo.vwHutPiut a
                  where a.Tipe='HT'
                  Group by a.NoFaktur) b On a.NoInv=B.Nofaktur
  Left Outer Join dbCustSupp d On d.KodeCustSupp=c.KodeSupp
  where a.NOBUKTI = :nobukti
  order by a.urut" , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $inisial = DB::connection("SML")->select('select DN from DBNOMOR');

    $values = [
        $inisial[0]->DN,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

    return $noBukti;
  }


  public function listCustomer (Request $req) {

    $listData = DB::connection('SML')->select("select * from vwBrowsCustSupp where IsCustomer=0");
    return $listData;
  }

  public function listInvoice (Request $req) {

    $listData = DB::connection('SML')->select("declare @awal Varchar(15)
    Select @Awal= :kodecustsupp
    select Cast(1 as Bit) Pilih,Convert(Numeric(18,2),0) Nilai, a.Valas KodeVls, a.Kurs, a.NoFaktur,Min(a.Tanggal) Tanggal, Min(a.JatuhTempo) JatuhTempo,
           SUM(a.Saldo) Saldo, SUM(A.SaldoD) SaldoD
    from dbo.vwHutPiut a
         left outer join (select NoInv from dbdebetNoteDet) b on b.NoInv=a.NoFaktur
    where a.KodeCustSupp=@Awal and B.NoInv is null and a.Tipe='HT'
    Group by a.NoFaktur,a.Valas, a.Kurs
    Having SUM(a.Saldo)<>0 or SUM(A.SaldoD)<>0
    Order by a.Nofaktur" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;

  }

public function updateOtorisasi(Request $req) {
    $tanggal = now();
    $res = DB::connection('SML')->update(
        "UPDATE dbdebetnote 
         SET IsOtorisasi1 = 1, OtoUser1 = :username, TglOto1 = :tanggal 
         WHERE NoBukti = :nobukti",
        [
            "username" => \Auth::user()->username,
            "tanggal" => $tanggal,
            "nobukti" => $req->nobukti
        ]
    );
    return response()->json(['success' => $res > 0]);
}


public function updateBatalOtorisasi(Request $req) {
    $res = DB::connection('SML')->update(
        "UPDATE dbdebetnote SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = :nobukti",
        [
            "nobukti" => $req->nobukti
        ]
    );
    return $res;
}

  public function spAdd (Request $req) {

      $tempData = $req->input('tempData');
      $username = \Auth::user()->username;


      if ($req->tipeform == 'add' ) {
        $check = DB::connection('SML')->select('select * from DBDEBETNOTE where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
          if ($check) {
            return 2;
        }
      }

      foreach ($tempData as $d)  {
        // code...
        DB::connection('SML')->statement('exec Sp_DebetNote ?,?,?,?,?,?,?,?,?,?,?,?', [
          "I",
          $req->nobukti,
          $req->tanggal,
          $d['Keterangan'],
          0,
          $d['NoFaktur'],
          $req->kodecustsupp,
          $d['inputNilai'],
          $d['KodeVls'],
          $d['inputKurs'],
          $d['inputNilaiRp'],
          $req->nourut


        ]);
      }

      return 1;

  }

  public function spKoreksi (Request $req) {
  try {
    DB::connection('SML')->statement('exec Sp_DebetNote ?,?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->nobukti,
      $req->tanggal,
      $req->keterangan,
      $req->urut,
      $req->noinvoice,
      $req->kodecustsupp,
      $req->nilai,
      $req->kodevls,
      $req->kurs,
      $req->nilairp,
      $req->nourut,
    ]);

    return 1;

  } catch (\Exception $e) {
    \Log::error('SP ERROR: '.$e->getMessage());
    return response()->json(['error' => $e->getMessage()], 500);
  }
}

}
