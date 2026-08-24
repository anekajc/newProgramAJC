<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanStockMutasiStockPerMerkController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanstockmutasistockpermerk");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstockmutasistockpermerk' , [
        "akses" => $akses,
        "gudang" => url('functionbrowse_doBrowseGudang'),
        "merk" => url('functionbrowse_doBrowseMerk')
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $date = $req->get('date1');
    $dateParts = explode('-', $date);

    $Bulan = (int)$dateParts[1];
    $Tahun = $dateParts[0];
    $isi = "";
    $Kodegdg = $req->get('inputGudang');
    $KodeMerk = $req->get('inputMerk');
    $PrEKAP = $req->get('inputTampil');

    $values  = [$Bulan, $Tahun, $isi, $Kodegdg, $KodeMerk, $PrEKAP];
    
    $res = DB::connection('MGL')->select('exec Sp_reportmERK ?,?,?,?,?,?',
      $values);

    return $res;
  }


}
