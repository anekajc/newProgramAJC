<?php

namespace App\Http\Controllers\Gudang;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
// use App\Model\VWOutBRGPemakaian;


// use App\Model\VWPenyerahanOut;


class PembebananPemakaianController extends Controller
{
  public function index (Request $req) {

    $kodemenu = '06013';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    // $outstanding = VWOutBRGPemakaian::all()->sortBy('URUT')->groupBy('NOBUKTI');
    //
    // $tempOutstanding = [];
    // foreach ($outstanding as $o) {
    //   // code...
    //   array_push($tempOutstanding, $o);
    // }


        $tempOutstanding = DB::connection("SML")->select("
        Select MONTH(A.Tanggal) Bulan, YEAR(A.Tanggal) Tahun, A.TANGGAL,A.NOBUKTI,  a.NOURUT	,
        	B.URUT, B.KODEBRG, H.NAMABRG, B.QNT, B.QNT2,B.NOSAT, C.SAT1, C.SAT2, B.ISI,C.ISI2, A.Kodegdg, G.Nama Namagdg,
                case when b.NOSAT=1 then c.SAT1 when b.NOSAT=2 then C.SAT2 end Satuan,
                case when b.NOSAT=1 then B.QNT when b.NOSAT=2 then b.QNT2 end Qntx , b.NOPRPB NooutBRg
                ,OS.QntOS,
        A.Keterangan,
        A.IsOtorisasi1,
        A.OtoUser1,
        A.TglOto1, dbo.DataPerkiraan(A.Nobukti,'BHN') Perkiraan
        From dbPenyerahanBhn A
        Left Outer join  dbPenyerahanBhnDet B on B.NoBukti=a.NoBukti
        left outer join dbBarang C on C.KodeBrg=B.KodeBrg
        Left Outer join dbBarang H on H.KodeBrg=b.KodeBrg
        left outer join DBGUDANG G on A.Kodegdg = G.KODEGDG
        left outer join (select b.Nobukti,b.urut,case when a.NoSat=1 then a.Qnt else a.qnt2 end -
        				 SUM(isnull(case when A.NoSat=1 then  b.Qnt else b.Qnt2 end,0 )) QntOS
        				 from DBPRPenyerahanBhnDET a
        				 left outer join DBPenyerahanBhnDET b on a.Nobukti=b.NOPRPB and a.urut=b.URUTPRPB
        				 group by b.Nobukti,b.urut,B.NoSat,A.NoSat,A.Qnt,A.Qnt2
        				 ) OS ON B.Nobukti=OS.Nobukti AND B.URUT=OS.urut

                 where Year(a.tanggal ) = :tahun and Month(a.Tanggal)  = :bulan and A.IsOtorisasi1 = 0
        order by A.NoBukti, B.Urut" , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan ]);



        $collection1 = collect($tempOutstanding)->groupBy('NOBUKTI');
        $tempOutstanding1 = [];
        foreach ($collection1 as $p) {
          // code...
          array_push($tempOutstanding1, $p);
        }



    //
    // $penerimaan = VWTRansferOut::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = DB::connection("SML")->select("

        Select MONTH(A.Tanggal) Bulan, YEAR(A.Tanggal) Tahun, A.TANGGAL,A.NOBUKTI,  a.NOURUT	,
        	B.URUT, B.KODEBRG, H.NAMABRG, B.QNT, B.QNT2,B.NOSAT, C.SAT1, C.SAT2, B.ISI,C.ISI2, A.Kodegdg, G.Nama Namagdg,

                case when b.NOSAT=1 then c.SAT1 when b.NOSAT=2 then C.SAT2 end Satuan,
                case when b.NOSAT=1 then B.QNT when b.NOSAT=2 then b.QNT2 end Qntx , b.NOPRPB NooutBRg
                ,OS.QntOS,
        A.Keterangan,
        A.IsOtorisasi1,
        A.OtoUser1,
        A.TglOto1, dbo.DataPerkiraan(A.Nobukti,'BHN') Perkiraan
        From dbPenyerahanBhn A
        Left Outer join  dbPenyerahanBhnDet B on B.NoBukti=a.NoBukti
        left outer join dbBarang C on C.KodeBrg=B.KodeBrg
        Left Outer join dbBarang H on H.KodeBrg=b.KodeBrg
        left outer join DBGUDANG G on A.Kodegdg = G.KODEGDG
        left outer join (select b.Nobukti,b.urut,case when a.NoSat=1 then a.Qnt else a.qnt2 end -
        				 SUM(isnull(case when A.NoSat=1 then  b.Qnt else b.Qnt2 end,0 )) QntOS
        				 from DBPRPenyerahanBhnDET a
        				 left outer join DBPenyerahanBhnDET b on a.Nobukti=b.NOPRPB and a.urut=b.URUTPRPB
        				 group by b.Nobukti,b.urut,B.NoSat,A.NoSat,A.Qnt,A.Qnt2
        				 ) OS ON B.Nobukti=OS.Nobukti AND B.URUT=OS.urut

                 where Year(a.tanggal ) = :tahun and Month(a.Tanggal)  = :bulan and A.IsOtorisasi1 = 1
        order by A.NoBukti, B.Urut

" , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan ]);



        $collection1 = collect($tempPenerimaan)->groupBy('NOBUKTI');
        $tempPenerimaan1 = [];
        foreach ($collection1 as $p) {
          // code...
          array_push($tempPenerimaan1, $p);
        }


    return view('gudang.pembebananpemakaian' , [
      "periode" => $periode,
      "menul0" => $menul0,
      "outstandingArray" => $tempOutstanding1,
      "penerimaanArray" => $tempPenerimaan1,
      "akses" => $akses
    ]);
  }

  public function loadAll(Request $request)
{
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    // === OUTSTANDING ===
    $tempOutstanding = DB::connection("SML")->select("
        Select MONTH(A.Tanggal) Bulan, YEAR(A.Tanggal) Tahun, A.TANGGAL, A.NOBUKTI, a.NOURUT,
            B.URUT, B.KODEBRG, H.NAMABRG, B.QNT, B.QNT2, B.NOSAT, C.SAT1, C.SAT2, B.ISI, C.ISI2,
            A.Kodegdg, G.Nama Namagdg,
            case when b.NOSAT=1 then c.SAT1 when b.NOSAT=2 then C.SAT2 end Satuan,
            case when b.NOSAT=1 then B.QNT when b.NOSAT=2 then b.QNT2 end Qntx,
            b.NOPRPB NooutBRg, OS.QntOS,
            A.Keterangan,
            A.IsOtorisasi1,
        A.OtoUser1,
        A.TglOto1, dbo.DataPerkiraan(A.Nobukti,'BHN') Perkiraan
        From dbPenyerahanBhn A
        Left Outer join dbPenyerahanBhnDet B on B.NoBukti = A.NoBukti
        left outer join dbBarang C on C.KodeBrg = B.KodeBrg
        Left Outer join dbBarang H on H.KodeBrg = B.KodeBrg
        left outer join DBGUDANG G on A.Kodegdg = G.KODEGDG
        left outer join (
            select b.Nobukti, b.urut,
                case when a.NoSat = 1 then a.Qnt else a.Qnt2 end -
                SUM(isnull(case when A.NoSat=1 then b.Qnt else b.Qnt2 end,0)) QntOS
            from DBPRPenyerahanBhnDET a
            left outer join DBPenyerahanBhnDET b
                on a.Nobukti = b.NOPRPB and a.urut = b.URUTPRPB
            group by b.Nobukti, b.urut, B.NoSat, A.NoSat, A.Qnt, A.Qnt2
        ) OS ON B.Nobukti = OS.Nobukti AND B.URUT = OS.urut
        where Year(a.tanggal) = :tahun and Month(a.Tanggal) = :bulan and A.IsOtorisasi1 = 0
        order by A.NoBukti, B.Urut
    ", ["tahun" => $periode->tahun, "bulan" => $periode->bulan]);

    $collectionOutstanding = collect($tempOutstanding)->groupBy('NOBUKTI');
    $tempOutstanding1 = [];
    foreach ($collectionOutstanding as $p) {
        array_push($tempOutstanding1, $p);
    }

    // === PENERIMAAN ===
    $tempPenerimaan = DB::connection("SML")->select("
        Select MONTH(A.Tanggal) Bulan, YEAR(A.Tanggal) Tahun, A.TANGGAL, A.NOBUKTI, a.NOURUT,
            B.URUT, B.KODEBRG, H.NAMABRG, B.QNT, B.QNT2, B.NOSAT, C.SAT1, C.SAT2, B.ISI, C.ISI2,
            A.Kodegdg, G.Nama Namagdg,
            case when b.NOSAT=1 then c.SAT1 when b.NOSAT=2 then C.SAT2 end Satuan,
            case when b.NOSAT=1 then B.QNT when b.NOSAT=2 then b.Qnt2 end Qntx,
            b.NOPRPB NooutBRg, OS.QntOS,
            A.Keterangan,
            A.IsOtorisasi1,
        A.OtoUser1,
        A.TglOto1, dbo.DataPerkiraan(A.Nobukti,'BHN') Perkiraan
        From dbPenyerahanBhn A
        Left Outer join dbPenyerahanBhnDet B on B.NoBukti = A.NoBukti
        left outer join dbBarang C on C.KodeBrg = B.KodeBrg
        Left Outer join dbBarang H on H.KodeBrg = B.KodeBrg
        left outer join DBGUDANG G on A.Kodegdg = G.KODEGDG
        left outer join (
            select b.Nobukti, b.urut,
                case when a.NoSat = 1 then a.Qnt else a.Qnt2 end -
                SUM(isnull(case when A.NoSat=1 then b.Qnt else b.Qnt2 end,0)) QntOS
            from DBPRPenyerahanBhnDET a
            left outer join DBPenyerahanBhnDET b
                on a.Nobukti = b.NOPRPB and a.urut = b.URUTPRPB
            group by b.Nobukti, b.urut, B.NoSat, A.NoSat, A.Qnt, A.Qnt2
        ) OS ON B.Nobukti = OS.Nobukti AND B.URUT = OS.urut
        where Year(a.tanggal) = :tahun and Month(a.Tanggal) = :bulan and A.IsOtorisasi1 = 1
        order by A.NoBukti, B.Urut
    ", ["tahun" => $periode->tahun, "bulan" => $periode->bulan]);

    $collectionPenerimaan = collect($tempPenerimaan)->groupBy('NOBUKTI');
    $tempPenerimaan1 = [];
    foreach ($collectionPenerimaan as $p) {
        array_push($tempPenerimaan1, $p);
    }

    return response()->json([
        "tempOutstanding" => $tempOutstanding1,
        "tempPenerimaan" => $tempPenerimaan1
    ]);
}

public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakPemakaianBahanACC ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function getDetail (Request $req ) {
    DB::connection('SML')->statement('exec sp_RefreshTempSerahSample ?,?,?', [\Auth::User()->username, $req->nobukti, date('Y-m-d')]);

    $tempOutstanding = DB::connection("SML")->select("select * from TempSerahSample where IDUser = :username", ["username" => \Auth::User()->username]);


    // $tempOutstanding = DB::connection("SML")->select("declare @Tahun int, @Bulan int, @Flagmenu tinyint,@UserID Varchar(30)
    //
    // select @Tahun= '' , @Bulan= '', @UserID= :user
    //
    // select distinct A.NoBukti, A.NoUrut, A.Tanggal, A.KodeCustSupp, A.NAMACUSTSUPP, A.NamaKota,
    //   A.NoRPJ, A.NoSO, A.IDUser,
    //   A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.NeedOtorisasi,
    //     A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,A.Noinv,A.KodeGdg , A.KodeBrg, A.NamaBrg ,A.QNT, A.QNT1, A.QNT2, A.SAT_1, A.SAT_2, A.NOSAT, A.ISI,A.URUT
    // from vwTransPRRJual A
    // Left Outer Join (  SELECT A.NOBUKTI
    //          FROM dbPRRJualDet A
    //          LEFT OUTER JOIN (select noprrjual,urutprrjual,sum(QNT1)Qnt1,SUM(QNT2) Qnt2
    //                                                 from dbSPBRJualDet  group by noprrjual,urutprrjual
    //             ) B on A.NoBukti=B.NoprRJual AND A.Urut=B.URUTPRRJUAL
    //          WHERE ISNULL(A.QNT1,0)-ISNULL(B.Qnt1,0) >0
    //          GROUP BY A.NoBukti
    //         )B ON A.NoBukti=B.NoBukti
    // where A.Kodegdg In(select KodeGdg from DBPemakaiGdg where UserId=@UserId)
    // and NeedOtorisasi=0
    // AND B.NoBukti = :nobukti
    // order by A.NoBukti",[ "user" => \Auth::User()->username , "nobukti" => $req->nobukti]);

    return $tempOutstanding;
  }

  public function getDetailPenerimaan(Request $req) {
    $tempOutstanding = DB::connection("SML")->select("
        Select  
            A.NoBukti,
            B.Urut, 
            B.KodeBrg, 
            H.NamaBrg, 
            B.Qnt, 
            B.NoSat, 
            B.Isi Isi, 
            B.Sat Satuan,
            B.KodePerkiraan,
            I.Keterangan namaPerkiraan,
            A.Nobukti + CAST(B.Urut AS varchar(20)) as Keybukti,
            B.KODECOST,
            J.NamaCost,
            B.KODESUBCOST,
            K.NamaSubCost
        From dbPenyerahanBhn A
        Left Outer join dbPenyerahanBhnDet B on B.NoBukti = A.NoBukti
        Left Outer join dbBarang H on H.KodeBrg = B.KodeBrg
        Left Outer join dbPerkiraan I on I.Perkiraan = B.KodePerkiraan
        Left Outer join dbCost J on B.KODECOST = J.KodeCost
        Left Outer join vwSubCost K on B.KODESUBCOST = K.KodeSubCost
        where A.NoBukti = :nobukti
        order by A.NoBukti, B.Urut
    ", [
        "nobukti" => $req->nobukti
    ]);

    return $tempOutstanding;
}


public function Perkiraan(Request $req)
{
    $data = DB::connection("SML")->select("
        SELECT 
            A.Perkiraan, 
            B.Keterangan
        FROM DBPOSTHUTPIUT A
        LEFT OUTER JOIN DBPERKIRAAN B 
            ON B.Perkiraan = A.Perkiraan
        WHERE A.Kode = 'PD'
    ");

    return response()->json($data);
}


  public function Costing (Request $req)
{
    $perkiraan = $req->input('perkiraan'); 

    $data = DB::connection("SML")->select("
        SELECT 
            a.KodeCost, 
            a.NamaCost
        FROM dbCost a
        INNER JOIN dbPerkCost b ON a.KodeCost = b.KodeCost
        WHERE b.Perkiraan = ?
        GROUP BY a.KodeCost, a.NamaCost
        ORDER BY a.KodeCost
    ", [$perkiraan]);

    return response()->json($data ?? []);
}

public function SubCosting(Request $req)
{
    $kodeCost = $req->input('kodeCost');

    $data = DB::connection("SML")->select("
        SELECT 
            a.KodeSubCost,
            a.NamaSubCost,
            a.KodeCost
        FROM vwSubCost a
        WHERE a.KodeCost = :kodeCost
        ORDER BY a.KodeSubCost
    ", ['kodeCost' => $kodeCost]);

    return response()->json($data ?? []);
}



  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $inisial = DB::connection("SML")->select('select SSP from DBNOMOR');


    $values = [
        $inisial[0]->SSP,
        $periode->bulan,
        $periode->tahun,
        $username,
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

    return $noBukti;
  }

  public function spOtorisasi (Request $req)
{
    $tanggal = date('Y-m-d H:i:s');

    // cek perkiraan
    $cek = DB::connection('SML')->selectOne("
        SELECT COUNT(*) as jmlKosong
        FROM dbPenyerahanBhnDet
        WHERE NoBukti = :nobukti
          AND (KodePerkiraan IS NULL OR LTRIM(RTRIM(KodePerkiraan)) = '')
    ", ["nobukti" => $req->nobukti]);

    if ($cek && $cek->jmlKosong > 0) {
        // perkiraan kosong = gagal
        return response()->json([
            'status' => 0,
            'msg' => "Gagal Otorisasi. Perkiraan Masih Kosong."
        ]);
    }

    // perkiraan sudah diisi
    $res = DB::connection('SML')->update("
        UPDATE dbPenyerahanBhn
        SET isOtorisasi1 = 1,
            maxol = 1,
            OtoUser1 = :username,
            TglOto1 = :tanggal
        WHERE NoBukti = :nobukti
    ", [
        "username" => \Auth::user()->username,
        "tanggal" => $tanggal,
        "nobukti" => $req->nobukti
    ]);


     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PBG',$req->nobukti,'',0,'dbPenyerahanBhn');

    return response()->json([
        'status' => $res,
        'msg' => $res > 0 ? "Berhasil otorisasi" : "Gagal otorisasi"
    ]);
}

  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbPenyerahanBhn set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PBG',$req->nobukti,$req->pket,0,'dbPenyerahanBhn');
    
    
    return $res;
  }


  public function spKoreksi(Request $req)
{
    try {
        DB::connection("SML")->update("
            UPDATE DBPenyerahanBhnDet 
            SET KodePerkiraan = ?, 
                KODECOST = ?, 
                KODESUBCOST = ? 
            WHERE NoBukti = ? 
              AND Urut = ?
        ", [
            $req->kodeperkiraan,   
            $req->kodecost,       
            $req->kodesubcost,     
            $req->nobukti,        
            (int)$req->urut       
        ]);
         $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData('U','PBG',$req->nobukti,'',$req->urut,'dbPenyerahanBhndet');
        return response()->json([
            "success" => true,
            "message" => "Data berhasil dikoreksi"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            "success" => false,
            "message" => "Terjadi kesalahan: " . $e->getMessage()
        ], 500);
    }

    

}



  public function onChangeHeader (Request $req) {
    $query = 'update dbserahsample set ' . $req->field . ' = :value where nobukti = :nobukti';
    
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }

}
