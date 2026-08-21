<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanHistoriPenyerahanSampleController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanhistoripenyerahansample");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('reporthistoripenyerahansample' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $tgl1   = $req->get('date1');
    $tgl2   = $req->get('date2');
    $IDuser = "";
    $SLS    = "";

    $values  = [$tgl1, $tgl2, $IDuser, $SLS];
    
    $res = DB::connection('MGL')->select('exec ReportHisSerahSampleN ?,?,?,?',
      $values);

    return $res;
  }

  public function doFilter(Request $req) {
    // $kolom = ($req->get('inputOrd') == "N") ? 'nobukti, Tanggal' : 'KODEBRG, NAMABRG';
    // $listData = DB::connection('MGL')->select('select ' . $kolom . ' from xxx where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);

    $listData = $this->doReport($req);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    // $kolom = ($req->get('inputOrd') == "N") ? 'nobukti' : 'KODEBRG';
    // $res = [];

    // for ($i=0; $i < count($req->listdata); $i++) {
    //   $row = DB::connection('MGL')->select('select * from xxx where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);
      
    //   for ($j=0; $j < count($row); $j++) {
    //     $res = array_add($res, $i+$j, $row[$j]);
    //   }
    // }

    $row = $this->doReport($req);
    $res = [];
    $count = 0;

    for ($i=0; $i < count($req->listdata); $i++) {
      for ($j=0; $j < count($row); $j++) {
        if ($req->listdata[$i] == $row[$j]->nobukti) {
          $res = array_add($res, $count, $row[$j]);
          $count++;
        }
      }
    }
    
    return $res;
  }


}
