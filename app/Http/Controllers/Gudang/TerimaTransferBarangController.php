<?php

namespace App\Http\Controllers\Gudang;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;



class TerimaTransferBarangController extends Controller
{
  public function index(Request $req) {
    $kodemenu = '06042';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

    $tempOutstanding = DB::connection("SML")->select("
    SELECT 	
        B.NOBUKTI,
        X.TANGGAL,
        B.URUT,
        B.KODEBRG,
        C.NAMABRG,
        '' AS Jns_Kertas,
        '' AS Ukr_Kertas,
        B.QNT,
        B.QNT2,
        B.SAT_1,
        B.SAT_2,
        B.ISI,
        B.GdgAsal,
        B.GdgTujuan,
        D.Nama + ' (' + B.GdgAsal + ')' AS NamagdgAsal,
        E.Nama + ' (' + B.GdgTujuan + ')' AS NamagdgTujuan,
        0.00 AS GSM,
        CASE 
            WHEN B.nosat = 1 THEN B.QNT - ISNULL(F.Qnt, 0) 
            WHEN B.nosat = 2 THEN B.QNT2 - ISNULL(F.Qnt2, 0) 
        END AS Qntx,
        CASE 
            WHEN B.nosat = 1 THEN C.sat1 
            WHEN B.nosat = 2 THEN C.sat2 
        END AS Satx,
        X.NOTE
    FROM dbTransferDet B    
    LEFT JOIN dbBarang C ON C.KodeBrg = B.KodeBrg
    LEFT JOIN dbGudang D ON D.Kodegdg = B.GdgAsal
    LEFT JOIN dbGudang E ON E.Kodegdg = B.GdgTujuan
    LEFT JOIN DBTRANSFER X ON B.NOBUKTI = X.NOBUKTI
    LEFT JOIN (
        SELECT NoTransfer, UrutTransfer, SUM(QNT) Qnt, SUM(QNT2) Qnt2 
        FROM DBTRANSFERDET 
        GROUP BY NoTransfer, UrutTransfer
    ) F ON B.NOBUKTI = F.NoTransfer AND B.URUT = F.UrutTransfer
    WHERE	
        B.QNT - ISNULL(F.Qnt, 0) > 0 
        AND ISNULL(B.isbatal, 0) = 0
        AND ISNULL(X.pTERIMA, 0) = 0
    ORDER BY B.Urut
");


    $tempPenerimaan = DB::connection("SML")->select("
    SELECT 	
        X.TANGGAL, 
        B.NOBUKTI,
        B.URUT,
        B.KODEBRG,
        C.NAMABRG,
        '' AS Jns_Kertas,
        '' AS Ukr_Kertas,
        B.QNT,
        B.QNT2,
        B.SAT_1,
        B.SAT_2,
        B.ISI,
        B.GdgAsal,
        B.GdgTujuan,
        D.Nama + ' (' + B.GdgAsal + ')' AS NamagdgAsal,
        E.Nama + ' (' + B.GdgTujuan + ')' AS NamagdgTujuan,
        0.00 AS GSM,
        CASE 
            WHEN B.nosat = 1 THEN B.QNT - ISNULL(F.Qnt, 0) 
            WHEN B.nosat = 2 THEN B.QNT2 - ISNULL(F.Qnt2, 0) 
        END AS Qntx,
        CASE 
            WHEN B.nosat = 1 THEN C.sat1 
            WHEN B.nosat = 2 THEN C.sat2 
        END AS Satx,
        X.NOTE
    FROM dbTransferDet B    
    LEFT JOIN dbBarang C ON C.KodeBrg = B.KodeBrg
    LEFT JOIN dbGudang D ON D.Kodegdg = B.GdgAsal
    LEFT JOIN dbGudang E ON E.Kodegdg = B.GdgTujuan
    LEFT JOIN DBTRANSFER X ON B.NOBUKTI = X.NOBUKTI
    LEFT JOIN (
        SELECT NoTransfer, UrutTransfer, SUM(QNT) AS Qnt, SUM(QNT2) AS Qnt2 
        FROM DBTRANSFERDET 
        GROUP BY NoTransfer, UrutTransfer
    ) F ON B.NOBUKTI = F.NoTransfer AND B.URUT = F.UrutTransfer
    WHERE	
        B.QNT - ISNULL(F.Qnt, 0) > 0 
        AND ISNULL(B.isbatal, 0) = 0
        AND ISNULL(X.pTERIMA, 0) = 1
        AND MONTH(X.TANGGAL) = :bulan 
        AND YEAR(X.TANGGAL) = :tahun
    ORDER BY B.Urut
", [
    "tahun" => $periode->tahun, 
    "bulan" => $periode->bulan
]);

    // $tempPenerimaan2 = DB::connection("SML")->select("
    // SELECT 
    //     A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
    //     A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note AS Keterangan, 
    //     A.IDUSER, A.NoPenyerahan,
    //     A.KODECUSTSUPP, F.NamaCustSupp,
    //     A.KODESLS, G.Nama AS NAMASLS,
    //     AA.RefPR,
    //     COUNT(B.URUT) AS JumlahItem,
    //     SUM(CASE WHEN B.NOSAT = 1 THEN ISNULL(B.QNT, 0) ELSE ISNULL(B.QNT2, 0) END) AS TotalQntSample,
    //     SUM(ISNULL(BB.QNTOUTSTANDING, 0)) AS TotalQntOutstanding
    // FROM DBserahSAMPLE A
    // LEFT JOIN DBserahSAMPLEDET B ON B.NoBukti = A.NoBukti
    // LEFT JOIN DbCustSupp F ON F.KodeCustSupp = A.KODECUSTSUPP
    // LEFT JOIN dbKaryawan G ON A.KODESLS = G.KeyNIK
    // LEFT JOIN DBPRSAMPLE AA ON B.NOPRSAMPLE = AA.NOBUKTI
    // LEFT JOIN (
    //     SELECT 
    //         A.NOBUKTI, A.URUT,
    //         CASE 
    //             WHEN A.NOSAT = 1 THEN ISNULL(A.QNT, 0) - ISNULL(B.Qnt1, 0) 
    //             ELSE ISNULL(A.QNT2, 0) - ISNULL(B.Qnt2, 0) 
    //         END AS QNTOUTSTANDING
    //     FROM DBPRSAMPLEDET A 
    //     LEFT JOIN (
    //         SELECT NOPRSAMPLE, URUTPRSAMPLE, SUM(QNT) AS Qnt1, SUM(QNT2) AS Qnt2
    //         FROM DBSERAHSAMPLEDET
    //         GROUP BY NOPRSAMPLE, URUTPRSAMPLE
    //     ) B ON A.NoBukti = B.NOPRSAMPLE AND A.Urut = B.URUTPRSAMPLE
    // ) BB ON BB.NOBUKTI = B.NOPRSAMPLE AND BB.URUT = B.URUTPRSAMPLE
    // WHERE MONTH(A.TANGGAL) = :bulan 
    //     AND YEAR(A.TANGGAL) = :tahun
    //     and A.IsOtorisasi1 = 1
    //     AND ISNULL(AA.pKonsi, 0) = 1
    // GROUP BY 
    //     A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
    //     A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note, 
    //     A.IDUSER, A.NoPenyerahan,
    //     A.KODECUSTSUPP, F.NamaCustSupp,
    //     A.KODESLS, G.Nama,
    //     AA.RefPR
    // ORDER BY A.NOBUKTI", ["tahun" => $periode->tahun, "bulan" => $periode->bulan]);

    return view('gudang.terimatransferbarang', [
        "menul0" => $menul0,
        "periode" => $periode,
        "tempOutstanding" => $tempOutstanding,
        "tempPenerimaan" => $tempPenerimaan,
        // "tempPenerimaan2" => $tempPenerimaan2,
        "akses" => $akses
    ]);
}

  public function loadAll(Request $request)
{
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $tempOutstanding = DB::connection("SML")->select("
    SELECT 	
        B.NOBUKTI,
        X.TANGGAL,
        B.URUT,
        B.KODEBRG,
        C.NAMABRG,
        '' AS Jns_Kertas,
        '' AS Ukr_Kertas,
        B.QNT,
        B.QNT2,
        B.SAT_1,
        B.SAT_2,
        B.ISI,
        B.GdgAsal,
        B.GdgTujuan,
        D.Nama + ' (' + B.GdgAsal + ')' AS NamagdgAsal,
        E.Nama + ' (' + B.GdgTujuan + ')' AS NamagdgTujuan,
        0.00 AS GSM,
        CASE 
            WHEN B.nosat = 1 THEN B.QNT - ISNULL(F.Qnt, 0) 
            WHEN B.nosat = 2 THEN B.QNT2 - ISNULL(F.Qnt2, 0) 
        END AS Qntx,
        CASE 
            WHEN B.nosat = 1 THEN C.sat1 
            WHEN B.nosat = 2 THEN C.sat2 
        END AS Satx,
        X.NOTE
    FROM dbTransferDet B    
    LEFT JOIN dbBarang C ON C.KodeBrg = B.KodeBrg
    LEFT JOIN dbGudang D ON D.Kodegdg = B.GdgAsal
    LEFT JOIN dbGudang E ON E.Kodegdg = B.GdgTujuan
    LEFT JOIN DBTRANSFER X ON B.NOBUKTI = X.NOBUKTI
    LEFT JOIN (
        SELECT NoTransfer, UrutTransfer, SUM(QNT) Qnt, SUM(QNT2) Qnt2 
        FROM DBTRANSFERDET 
        GROUP BY NoTransfer, UrutTransfer
    ) F ON B.NOBUKTI = F.NoTransfer AND B.URUT = F.UrutTransfer
    WHERE	
        B.QNT - ISNULL(F.Qnt, 0) > 0 
        AND ISNULL(B.isbatal, 0) = 0
        AND ISNULL(X.pTERIMA, 0) = 0
    ORDER BY B.Urut
");

    $tempPenerimaan = DB::connection("SML")->select("
    SELECT 	
        X.TANGGAL,
        B.NOBUKTI,
        B.URUT,
        B.KODEBRG,
        C.NAMABRG,
        '' AS Jns_Kertas,
        '' AS Ukr_Kertas,
        B.QNT,
        B.QNT2,
        B.SAT_1,
        B.SAT_2,
        B.ISI,
        B.GdgAsal,
        B.GdgTujuan,
        D.Nama + ' (' + B.GdgAsal + ')' AS NamagdgAsal,
        E.Nama + ' (' + B.GdgTujuan + ')' AS NamagdgTujuan,
        0.00 AS GSM,
        CASE 
            WHEN B.nosat = 1 THEN B.QNT - ISNULL(F.Qnt, 0) 
            WHEN B.nosat = 2 THEN B.QNT2 - ISNULL(F.Qnt2, 0) 
        END AS Qntx,
        CASE 
            WHEN B.nosat = 1 THEN C.sat1 
            WHEN B.nosat = 2 THEN C.sat2 
        END AS Satx,
        X.NOTE
    FROM dbTransferDet B    
    LEFT JOIN dbBarang C ON C.KodeBrg = B.KodeBrg
    LEFT JOIN dbGudang D ON D.Kodegdg = B.GdgAsal
    LEFT JOIN dbGudang E ON E.Kodegdg = B.GdgTujuan
    LEFT JOIN DBTRANSFER X ON B.NOBUKTI = X.NOBUKTI
    LEFT JOIN (
        SELECT NoTransfer, UrutTransfer, SUM(QNT) AS Qnt, SUM(QNT2) AS Qnt2 
        FROM DBTRANSFERDET 
        GROUP BY NoTransfer, UrutTransfer
    ) F ON B.NOBUKTI = F.NoTransfer AND B.URUT = F.UrutTransfer
    WHERE	
        B.QNT - ISNULL(F.Qnt, 0) > 0 
        AND ISNULL(B.isbatal, 0) = 0
        AND ISNULL(X.pTERIMA, 0) = 1
        AND MONTH(X.TANGGAL) = :bulan 
        AND YEAR(X.TANGGAL) = :tahun
    ORDER BY B.Urut
", [
    "tahun" => $periode->tahun, 
    "bulan" => $periode->bulan
]);

    // $tempPenerimaan2 = DB::connection("SML")->select("
    // SELECT 
    //     A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
    //     A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note AS Keterangan, 
    //     A.IDUSER, A.NoPenyerahan,
    //     A.KODECUSTSUPP, F.NamaCustSupp,
    //     A.KODESLS, G.Nama AS NAMASLS,
    //     AA.RefPR,
    //     COUNT(B.URUT) AS JumlahItem,
    //     SUM(CASE WHEN B.NOSAT = 1 THEN ISNULL(B.QNT, 0) ELSE ISNULL(B.QNT2, 0) END) AS TotalQntSample,
    //     SUM(ISNULL(BB.QNTOUTSTANDING, 0)) AS TotalQntOutstanding
    // FROM DBserahSAMPLE A
    // LEFT JOIN DBserahSAMPLEDET B ON B.NoBukti = A.NoBukti
    // LEFT JOIN DbCustSupp F ON F.KodeCustSupp = A.KODECUSTSUPP
    // LEFT JOIN dbKaryawan G ON A.KODESLS = G.KeyNIK
    // LEFT JOIN DBPRSAMPLE AA ON B.NOPRSAMPLE = AA.NOBUKTI
    // LEFT JOIN (
    //     SELECT 
    //         A.NOBUKTI, A.URUT,
    //         CASE 
    //             WHEN A.NOSAT = 1 THEN ISNULL(A.QNT, 0) - ISNULL(B.Qnt1, 0) 
    //             ELSE ISNULL(A.QNT2, 0) - ISNULL(B.Qnt2, 0) 
    //         END AS QNTOUTSTANDING
    //     FROM DBPRSAMPLEDET A 
    //     LEFT JOIN (
    //         SELECT NOPRSAMPLE, URUTPRSAMPLE, SUM(QNT) AS Qnt1, SUM(QNT2) AS Qnt2
    //         FROM DBSERAHSAMPLEDET
    //         GROUP BY NOPRSAMPLE, URUTPRSAMPLE
    //     ) B ON A.NoBukti = B.NOPRSAMPLE AND A.Urut = B.URUTPRSAMPLE
    // ) BB ON BB.NOBUKTI = B.NOPRSAMPLE AND BB.URUT = B.URUTPRSAMPLE
    // WHERE MONTH(A.TANGGAL) = :bulan 
    //     AND YEAR(A.TANGGAL) = :tahun
    //     and A.IsOtorisasi1 = 1
    //     AND ISNULL(AA.pKonsi, 0) = 1
    // GROUP BY 
    //     A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
    //     A.NOBUKTI, A.NOURUT, A.TANGGAL, A.note, 
    //     A.IDUSER, A.NoPenyerahan,
    //     A.KODECUSTSUPP, F.NamaCustSupp,
    //     A.KODESLS, G.Nama,
    //     AA.RefPR
    // ORDER BY A.NOBUKTI", ["tahun" => $periode->tahun, "bulan" => $periode->bulan]);

    return response()->json([
        "tempOutstanding" => $tempOutstanding,
        "tempPenerimaan" => $tempPenerimaan
        // "tempPenerimaan2" => $tempPenerimaan2
    ]);
}

  public function getDetail (Request $req ) {
    DB::connection('SML')->statement('exec sp_TempOutTerimatransferWeb ?,?,?,?,?,?,?', [\Auth::User()->username, $req->nobukti, (int)date('Y'), (int)date('m'),'','','']);

    // $tempOutstanding = DB::connection("SML")->select("select * from TempOutTerimatransferWeb where IDUser = :username", ["username" => \Auth::User()->username]);


    $tempOutstanding = DB::connection("SML")->select("
        select  B.NOBUKTI, 
        X.TANGGAL,
        B.URUT,  
        B.KODEBRG, 
        C.NAMABRG, 
        '' Jns_Kertas, 
        '' Ukr_Kertas,
        B.QNT, 
        B.QNT2, 
        B.SAT_1, 
        B.SAT_2, 
        B.ISI, 
        B.GdgAsal, 
        B.GdgTujuan, 
        D.Nama+' ('+B.GdgAsal+')' NamagdgAsal,
        E.Nama+' ('+B.GdgTujuan+')' NamagdgTujuan, 
        0.00 GSM,
        case 
            when B.nosat=1 then B.QNT  - ISNULL(F.Qnt,0) 
            when B.nosat=2 then B.QNT2 - ISNULL(F.Qnt2,0) 
        end as Qntx,
        case 
            when B.nosat=1 then C.sat1 
            when B.nosat=2 then C.sat2 
        end as Satx
from    dbTransferDet B    
left join dbBarang C on C.KodeBrg = B.KodeBrg
left join dbGudang D on D.Kodegdg = B.GdgAsal
left join dbGudang E on E.Kodegdg = B.GdgTujuan
left join DBTRANSFER X on B.NOBUKTI = X.NOBUKTI
left join (
    select NoTransfer, UrutTransfer,
           SUM(case when NoSat=1 then QNT  else 0 end) as Qnt,
           SUM(case when NoSat=2 then QNT2 else 0 end) as Qnt2
    from DBTRANSFERDET
    where NoTransfer is not null
    group by NoTransfer, UrutTransfer
) F ON B.NOBUKTI = F.NoTransfer 
    AND B.URUT = F.UrutTransfer
where 
    (
        (B.nosat=1 and B.QNT  - ISNULL(F.Qnt,0)  > 0) OR
        (B.nosat=2 and B.QNT2 - ISNULL(F.Qnt2,0) > 0)
    )
and isnull(B.isbatal,0) = 0
and ISNULL(X.pTERIMA,0) = 0
and B.NOBUKTI = :nobukti
order by B.Urut

    ", [
        "nobukti" => $req->nobukti
    ]);

    return $tempOutstanding;
  }

  public function getDetailPenerimaan (Request $req) {
    $tempPenerimaan = DB::connection("SML")->select("
        SELECT 	
        X.TANGGAL,
        B.NOBUKTI,
        B.URUT,
        B.KODEBRG,
        C.NAMABRG,
        '' AS Jns_Kertas,
        '' AS Ukr_Kertas,
        B.QNT,
        B.QNT2,
        B.SAT_1,
        B.SAT_2,
        B.NOSAT,
        B.ISI,
        B.GdgAsal,
        B.GdgTujuan,
        D.Nama + ' (' + B.GdgAsal + ')' AS NamagdgAsal,
        E.Nama + ' (' + B.GdgTujuan + ')' AS NamagdgTujuan,
        0.00 AS GSM,
        CASE 
            WHEN B.nosat = 1 THEN B.QNT - ISNULL(F.Qnt, 0) 
            WHEN B.nosat = 2 THEN B.QNT2 - ISNULL(F.Qnt2, 0) 
        END AS Qntx,
        CASE 
            WHEN B.nosat = 1 THEN C.sat1 
            WHEN B.nosat = 2 THEN C.sat2 
        END AS Satx,
        X.NOTE
    FROM dbTransferDet B    
    LEFT JOIN dbBarang C ON C.KodeBrg = B.KodeBrg
    LEFT JOIN dbGudang D ON D.Kodegdg = B.GdgAsal
    LEFT JOIN dbGudang E ON E.Kodegdg = B.GdgTujuan
    LEFT JOIN DBTRANSFER X ON B.NOBUKTI = X.NOBUKTI
    LEFT JOIN (
        SELECT NoTransfer, UrutTransfer, SUM(QNT) AS Qnt, SUM(QNT2) AS Qnt2 
        FROM DBTRANSFERDET 
        GROUP BY NoTransfer, UrutTransfer
    ) F ON B.NOBUKTI = F.NoTransfer AND B.URUT = F.UrutTransfer
    WHERE	
        B.QNT - ISNULL(F.Qnt, 0) > 0 
        AND ISNULL(B.isbatal, 0) = 0
        AND ISNULL(X.pTERIMA, 0) = 1
        AND B.NOBUKTI = :nobukti
    ORDER BY B.Urut
    ", [
        "nobukti" => $req->nobukti
    ]);

    return $tempPenerimaan;
}

// public function GudangAsal(Request $req)
//   {
//     $data = DB::connection("SML")->select("
//         SELECT 
//         a.KodeGdg, 
//         a.Nama AS NamaGdg, 
//         a.IsRusak 
//         FROM dbGudang a 
//         LEFT OUTER JOIN dbPemakaiGdg b ON b.kodegdg = a.kodegdg 
//         GROUP BY a.KodeGdg, a.Nama, a.IsRusak 
//         ORDER BY a.KodeGdg");

//     return response()->json($data);
//   }

//   public function GudangTujuan(Request $req)
//   {
//     $data = DB::connection("SML")->select("
//         SELECT 
//         a.KodeGdg, 
//         a.Nama AS NamaGdg, 
//         a.IsRusak 
//         FROM dbGudang a 
//         LEFT OUTER JOIN dbPemakaiGdg b ON b.kodegdg = a.kodegdg 
//         GROUP BY a.KodeGdg, a.Nama, a.IsRusak 
//         ORDER BY a.KodeGdg");

//     return response()->json($data);
//   }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $inisial = DB::connection("SML")->select('select TRT from DBNOMOR');


    $values = [
        $inisial[0]->TRT,
        $periode->bulan,
        $periode->tahun,
        $username,
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

    return $noBukti;
  }

//   public function spOtorisasi (Request $req) {
//     $tanggal = date('Y-m-d H:i:s');
//     $res = DB::connection('SML')->update("update DBSERAHSAMPLE set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
//     return $res;
//   }
//   public function spBatalOtorisasi (Request $req) {
//     $tanggal = date('Y-m-d H:i:s');
//     $res = DB::connection('SML')->update("update DBSERAHSAMPLE set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
//     return $res;
//   }

public function spAdd (Request $req)
{
    $tempData = $req->input('tempData', []);
    $username = \Auth::user()->username;

    try {
        foreach ($tempData as $d) {
            if (!empty($d['checked']) && $d['checked'] == true) {
                DB::connection('SML')->statement('exec SP_InsertTERIMATRANSFERweb ?,?,?,?,?,?,?,?', [
                    $req->nobukti,  
                    $req->nourut,
                    $d['NOBUKTI'],       
                    $username,
                    -1,
                    $req->tanggal,
                    $d['inputQnt'],
                    $d['URUT'],     
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal simpan: ' . $e->getMessage()
        ], 500);
    }
}


//   public function spAdd(Request $req)
// {
//     $tempData = $req->input('tempData');
//     $username = \Auth::user()->username;

//     foreach ($tempData as $d) {
//         // dd($d);
//         DB::connection('SML')->statement('exec SP_InsertTERIMATRANSFERweb ?,?,?,?,?,?', [
//             $req->nobukti,                
//             $req->nourut,                            
//             $d['NOBUKTI'],                           
//             $username,                             
//             1,                                       
//             date('Y-m-d', strtotime($req->tanggal))
//         ]);
//     }

//     return 1;
// }


  public function spKoreksi (Request $req)
{
    $choice  = $req->choice; 
    $nobukti = $req->nobukti;

    $values = [
        $choice,                       
        $nobukti,                      
        $req->nourut,              
        $req->tanggal,          
        $req->note ?? '',               
        (int)$req->urut,                 
        $req->kodebrg,                   
        $req->kodegdgasal,               
        $req->kodegdgtujuan,             
        $req->sat1,                     
        $req->sat2,                      
        $req->qnt,                      
        $req->qnt2,                     
        $req->nosat,                    
        $req->isi,                     
        \Auth::user()->username,       
        $req->nopenyerahan ?? ''         
    ];

    DB::connection('SML')->statement(
        'exec SP_TRANSFER ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', 
        $values
    );

    return 1;
}

  public function onChangeHeader (Request $req) {
    $query = 'update dbserahsample set ' . $req->field . ' = :value where nobukti = :nobukti';
    
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }

}
