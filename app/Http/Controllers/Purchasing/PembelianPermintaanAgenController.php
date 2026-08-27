<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\VwPPL;


// use App\Http\Controllers\NewMenuController;

class PembelianPermintaanAgenController extends Controller
{

    public function index(Request $req)
    {
        $kodemenu = '030102';

        $akses = app('App\Http\Controllers\GlobalController')
            ->getAkses($kodemenu, $req->path());

        if (!$akses || !$akses->HASACCESS) {
            return redirect('/home');
        }

        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $menul0  = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

        // =========================
        // Belum Otorisasi
        // =========================
        $outstanding = VwPPL::where('Bulan', $periode->bulan)
            ->where('Tahun', $periode->tahun)
            ->where('IsJasa', 0)
            ->where('pAgen', 1)
            ->where(function ($query) {
                $query->whereNull('IsOtorisasi1')
                      ->orWhere('IsOtorisasi1', 0);
            })
            ->get()
            ->groupBy('NoBukti');

        $tempOutstanding = [];
        foreach ($outstanding as $groupedData) {
            $tempOutstanding[] = $groupedData;
        }

        // =========================
        // Data Otorisasi
        // =========================
        $otorisasi = DB::connection("SML")->select(
            "SELECT NoBukti, Tanggal, IsOtorisasi1, TglOto1, OtoUser1
             FROM vwPPL
             WHERE MONTH(Tanggal)=:bulan
               AND YEAR(Tanggal)=:tahun
               AND IsJasa=0
               AND pAgen=1",
            [
                "bulan" => $periode->bulan,
                "tahun" => $periode->tahun
            ]
        );

        $otorisasi = collect($otorisasi)->groupBy('NoBukti');

        $tempOtorisasi = [];
        foreach ($otorisasi as $groupedData) {
            $tempOtorisasi[] = $groupedData;
        }

        // =========================
        // Header Table
        // =========================
        $reqHeader = new Request([
            'href' => 'pembelianpermintaanagen'
        ]);

        $header = app('App\Http\Controllers\HeaderTableController')
            ->getHeaderTable($reqHeader);

        return view('purchasing.pembelianpermintaanagen', [

            "aliasordered"      => $header['aliasordered'],
            "headertableheader" => $header['headertableheader'],
            "isnumeric"         => $header['isnumeric'],
            "headertablevalue"  => $header['headertablevalue'],
            "isshown"           => $header['isshown'],

            "menul0"            => $menul0,
            "periode"           => $periode,
            "akses"             => $akses,
            "listData1"         => $tempOutstanding,
            "listData2"         => $tempOtorisasi,
        ]);
    }


    public function loadAll(Request $req)
    {
        $queryOtorisasi = '';

        if ($req->isoto != 2) {
            $queryOtorisasi = ' AND IsOtorisasi1 = ' . $req->isoto;
        }

        // =========================
        // Header Table
        // =========================
        $reqHeader = new Request([
            'href' => 'pembelianpermintaanagen'
        ]);

        $header = app('App\Http\Controllers\HeaderTableController')
            ->getHeaderTable($reqHeader);

        // =========================
        // Data Pembelian Agen
        // =========================
        $outstanding = DB::connection("SML")->select(
            "SELECT NoBukti, CONVERT(varchar(10), Tanggal, 23) AS Tanggal, IsOtorisasi1, TglOto1, OtoUser1,
                    Qnt AS Qnt, QntBatal AS QntBatal, QntPO AS QntPO
             FROM vwPPL
             WHERE Tanggal BETWEEN :tglawal AND :tglakhir
               AND IsJasa = 0
               AND pAgen = 1
               $queryOtorisasi
             ORDER BY Tanggal DESC, NoBukti DESC",
            [
                "tglawal"  => $req->tglawal,
                "tglakhir" => $req->tglakhir
            ]
        );

        $collection = collect($outstanding)->groupBy('NoBukti');

        $tempOutstanding = [];
        foreach ($collection as $groupedData) {
            $tempOutstanding[] = $groupedData;
        }

        // Mengikuti pola Non Agen
        $tempOtorisasi = [];

        return [
            "listData1" => $tempOutstanding,
            "listData2" => $tempOtorisasi,

            "aliasordered"      => $header['aliasordered'],
            "headertableheader" => $header['headertableheader'],
            "isnumeric"         => $header['isnumeric'],
            "headertablevalue"  => $header['headertablevalue'],
            "isparsed"          => $header['isparsed'],
            "isshown"           => $header['isshown'],
            "desimal"           => $header['desimal'],
        ];
    }

  public function updateOtorisasi(Request $req) {
    $tanggal = now();
    $res = DB::connection('SML')->update(
        "UPDATE DBPPL SET isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal WHERE NoBukti = :nobukti",
        [
            "username" => \Auth::user()->username,
            "tanggal" => $tanggal,
            "nobukti" => $req->nobukti
        ]
    );

    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PR',$req->nobukti,'',0,'DBPPL');
    return $res;
}
  public function updateBatalOtorisasi(Request $req) {
    $res = DB::connection('SML')->update(
        "UPDATE DBPPL SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = :nobukti",
        [
            "nobukti" => $req->nobukti
        ]


    );

     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PR',$req->nobukti,$req->pket,0,'DBPPL');
    return $res;
}
  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from DBPPL where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function spDetail (Request $req) {
    $detailOutstanding = VwPPL::all()->where('NoBukti', $req->nobukti )->sortBy('Urut');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;
  }


  public function listDepartemen (Request $req) {
    $listDepartemen = DB::connection("SML")->select('select * from DBDEPART');
    return $listDepartemen;
  }

  public function getNoBukti (Request $req) {
    // $values = [
    //   'a'
    // ];
    // return 'tes';
    // $po = DB::connection("SML")->select('exec sp_outstanding_po ?',$values);
    // $periode = NewPeriode::where('user_id' , \Auth::id())->first();
    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $inisial = DB::connection("SML")->select('select PRA from DBNOMOR');
    // $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->PRA,
        $periode->bulan,
        $periode->tahun,
        $username,
        // $periode
        // $periode
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function listBarang (Request $req) {
    // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
    //     select b.NAMAMERK ,  a.* from dbbarang a
    // join DBMERK b on a.KodeMerk = b.KODEMERK
    //  where a.KODEGRP = 'BJ' and a.pAgen = 1

    if (!$req->filled('search')) {
        return response()->json([]);
    }

    $search = "%" . $req->input('search') . "%";

    $listData = DB::connection('SML')->select("
        SELECT
            b.NAMAMERK,
            a.KODEBRG,
            a.NAMABRG,
            a.KODEMERK,
            a.PartNumber,
            a.SAT1,
            a.SAT2,
            a.SAT3,
            a.ISI1,
            a.ISI2,
            a.ISI3
        FROM dbbarang a
        JOIN DBMERK b ON a.KodeMerk = b.KODEMERK
        WHERE a.KODEGRP = 'BJ'
	AND a.IsAktif = 1
        AND a.pAgen = ?
        AND (a.KODEBRG LIKE ? OR a.NAMABRG LIKE ?)
        ORDER BY a.KODEBRG ASC
    ", [
        $req->isagen,
        $search,
        $search
    ]);

    return response()->json($listData);
  }

  public function spAdd (Request $req) {
    $choice = $req->choice;
    $jmlrecord = $req->jmlrecord;
    $nobukti = $req->nobukti;


     $purut = DB::connection('SML')->select('select * from DBPPLdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBPPLdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
            // return 'uuu';
        $xurut= $purut[0]->xurut;
        }else {
            // return 'mmm';
            $xurut = $req->urut;
        }

    }else{
        // return 'ttt';
        $xurut=1;
    }

    if ($choice == "I" && $jmlrecord == 0) {
        $check = DB::connection('SML')->select('select * from DBPPL where Nobukti = :nobukti', ['nobukti' => $nobukti]);
        if ($check) {
            return 2;
        }
    }
    $values = [
        $choice,
        $nobukti,
        strval($req->nourut),
        date('Y-m-d H:i:s', strtotime($req->tanggal)),
        (int) $req->urut,
        $req->kodebarang,
         $req->qnt,
        (int) $req->nosat,
        $req->satuan,
         $req->isi,
        $req->keterangan ?? '',
        (int) $req->isclose,
        (int) $req->isclosed,
        $req->kodedepartemen,
        $req->keterangannama ?? '',
        $req->isjasa,
        $req->noso ?? '',
        (int) $req->urutso,
        (int) $req->pagen,
        $req->nopocust ?? '',
        (int) $req->jmlrecord,
        (int) $req->pjasa
    ];
    DB::connection('SML')->statement('exec sp_PPL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PR',$nobukti,'',$xurut,'DBPPLDET');

    return 1;
  }


  public function spDelete (Request $req) {

    // $listData = $req->listData;
    // foreach ($listData as $d) {
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PR',$req->nobukti,'',$req->urut,'DBPPLDET');
      // code...
      $values = [
        $req->choice,
        $req->nobukti,
        $req->nourut,
        $req->tanggal,
        $req->urut,
        $req->kodebarang,
        $req->qnt,
        $req->nosat,
        $req->satuan,
        $req->isi,
        $req->keterangan,
        $req->isclose,
        $req->isclosed,
        $req->kddep,
        $req->keterangannama,
        $req->isjasa,
        $req->noso,
        $req->urutso,
        $req->pagen,
        $req->nopocust,
        $req->jmlrecord,
        $req->pjasa

      ];
      DB::connection('SML')->statement('exec sp_PPL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    // }
    return 1;
    // foreach ($penerimaan as $p) {
    //   // code...
    //   array_push($tempPenerimaan, $p);
    // }
    //
    // DB::connection('SML')->statement('exec sp_RSPB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    // return 1;
  }

}
