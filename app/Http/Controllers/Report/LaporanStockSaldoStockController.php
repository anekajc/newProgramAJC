<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanStockSaldoStockController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanstocksaldostock");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstocksaldostock' , [
        "akses" => $akses,
        "gudang" => url('functionbrowse_doBrowseGudang'),
        "grup" => url('functionbrowse_doBrowseHdGroup'),
        "kategori" => url('functionbrowse_doBrowseSubGroup'),
        "subkategori" => url('functionbrowse_doBrowseSubGroupJnsTambah'),
        "merk" => url('functionbrowse_doBrowseMerk')
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $Nosat = $req->get('inputIsi');
    $tanggal = $req->get('date1');
    $KOdegdg = $req->get('inputGudang');
    $KodeHDGroup = $req->get('inputGrup');
    $Kategori = $req->get('inputKategori');
    $SubKategori = $req->get('inputSubKategori');
    $KodeMerk = $req->get('inputMerk');

    $values  = [$Nosat, $tanggal, $KOdegdg, $KodeHDGroup, $Kategori, $SubKategori, $KodeMerk];
    
    $res = DB::connection('MGL')->select('exec Sp_ReportStockAkhir ?,?,?,?,?,?,?',
      $values);

    return $res;
  }


}
