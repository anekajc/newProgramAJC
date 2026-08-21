<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanHisPoController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanhispo");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportpengadaanhispo' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $TGLAWAL = $req->get('date1');
    $TGLAKHIR = $req->get('date2');
    $KodeCust    = $req->get('inputCust');
    $Lokasi    = $req->get('inputLokasi');
    $IDuser = '';
    $TipeTrans = '';

    $values  = [$TGLAWAL, $TGLAKHIR, $KodeCust, $Lokasi, $IDuser, $TipeTrans];
    
    $res = DB::connection('SML')->select('exec SP_REPORTHISPO ?,?,?,?,?,?',
      $values);

    return $res;
  }

  public function doFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti, Tanggal' : 'KODEBRG, NAMABRG';
    $listData = DB::connection('MGL')->select('select ' . $kolom . ' from VwREPORTHISPO where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti' : 'KODEBRG';
    $res = [];

    for ($i=0; $i < count($req->listdata); $i++) {
      $row = DB::connection('MGL')->select('select * from VwREPORTHISPO where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);
      
      for ($j=0; $j < count($row); $j++) {
        $res = array_add($res, $i+$j, $row[$j]);
      }
    }
    
    return $res;
  }

  public function loadLokasi () {
    $listData = DB::connection('SML')->select('Select KodeKebun,nama namaKebun from DbKebunCustSupp Group By KodeKebun,nama');
    return $listData;
  }

  public function loadCustomer () {
    $listData = DB::connection('SML')->select('select distinct A.KodeCustSupp, A.NamaCustSupp, A.AlamatKota, A.NamaKota, A.PPN, A.Hari, A.HariHutPiut 
                from vwBrowsCustSupp A 
                where IsSupplier=1 and (A.KodeCustSupp like \'%%\' or A.NamaCustSupp like \'%%\') 
                order by A.KodeCustSupp');
    return $listData;
  }

}


