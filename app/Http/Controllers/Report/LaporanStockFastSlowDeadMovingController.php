<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanStockFastSlowDeadMovingController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanstockfastslowdeadmoving");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstockfastslowdeadmoving' , [
        "akses" => $akses,
        "gudang" => url('functionbrowse_doBrowseGudang')
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $Tgl     = $req->get('date1');
    $Tipe    = $req->get('inputTipe');
    $Kodegdg = $req->get('inputGudang');

    $values  = [$Tgl, $Tipe, $Kodegdg];
    
    $res = DB::connection('MGL')->select('exec SP_REPORTMOVING ?,?,?',
      $values);

    return $res;
  }


}
