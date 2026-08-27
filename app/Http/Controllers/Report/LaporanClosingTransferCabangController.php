<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanClosingTransferCabangController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanclosingtransfercabang");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportclosingtransfercabang' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $tgl1  = $req->get('date1');
    $bulan = $this->getBulan($tgl1);
    $tahun = $this->getTahun($tgl1);

    $values  = [$bulan, $tahun];
    
    $res = DB::connection('MGL')->select('exec SP_ClosingTransfer ?,?',
      $values);

    return $res;
  }

  public function doFilter(Request $req) {
    // $kolom = ($req->get('inputOrd') == "N") ? 'nobukti, Tanggal' : 'KODEBRG, NAMABRG';
    // $listData = DB::connection('MGL')->select('select ' . $kolom . ' from VWREPORToutSERAHSAMPLE where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
    
    $listData = $this->doReport($req);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    // $kolom = ($req->get('inputOrd') == "N") ? 'nobukti' : 'KODEBRG';
    // $res = [];

    // for ($i=0; $i < count($req->listdata); $i++) {
    //   $row = DB::connection('MGL')->select('select * from VWREPORToutSERAHSAMPLE where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);
      
    //   for ($j=0; $j < count($row); $j++) {
    //     $res = array_add($res, $i+$j, $row[$j]);
    //   }
    // }

    $row = $this->doReport($req);
    $res = [];
    $count = 0;
    
    for ($i=0; $i < count($req->listdata); $i++) {
      for ($j=0; $j < count($row); $j++) {
        if ($req->listdata[$i] == $row[$j]->NOBUKTI) {
          $res = array_add($res, $count, $row[$j]);
          $count++;
        }
      }
    }
    
    return $res;
  }


}
