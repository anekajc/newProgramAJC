<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanStockMutasiStockController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  private function index($akses, $mode_menu) {
    return [
      "akses" => $akses,
      "mode_menu" => $mode_menu,
      "gudang" => url('functionbrowse_doBrowseGudang'),
      "grup" => url('functionbrowse_doBrowseHdGroup'),
      "kategori" => url('functionbrowse_doBrowseSubGroup'),
      "subkategori" => url('functionbrowse_doBrowseSubGroupJnsTambah'),
      "merk" => url('functionbrowse_doBrowseMerk')
    ];
  }

  public function indexqty() {
    $akses = $this->cekAkses("laporanstockmutasistockqty");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstockmutasistock' , $this->index($akses, "QTY"));
    } else {
      return redirect('/home');
    }
  }

  public function indexrp() {
    $akses = $this->cekAkses("laporanstockmutasistockrp");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstockmutasistock' , $this->index($akses, "RP"));
    } else {
      return redirect('/home');
    }
  }

  public function indexqtyrp() {
    $akses = $this->cekAkses("laporanstockmutasistockqtyrp");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstockmutasistock' , $this->index($akses, "QTYRP"));
    } else {
      return redirect('/home');
    }
  }

  public function indexperiode() {
    $akses = $this->cekAkses("laporanstockmutasistockperiode");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstockmutasistock' , $this->index($akses, "PERIODE"));
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $date = $req->get('date1');
    $dateParts = explode('-', $date);

    $Bulan = (int)$dateParts[1];
    $Tahun = $dateParts[0];
    $isi = $req->get('inputIsi');
    $Kodegdg = $req->get('inputGudang');
    $KodeGrp = "-";
    $SReport = "T";
    $IDuser = "";
    $Tipe = "";
    $IsMinus = $req->get('inputStockMinus');
    $jenis = $req->get('inputJenis');
    $KodeHdGrp = $req->get('inputGrup');
    $Kategori = $req->get('inputKategori');
    $SubKategori = $req->get('inputSubKategori');
    $KodeMerk = $req->get('inputMerk');

    $values  = [$Bulan, $Tahun, $isi, $Kodegdg, $KodeGrp, $SReport, $IDuser, $Tipe, $IsMinus, $jenis, $KodeHdGrp, $Kategori, $SubKategori, $KodeMerk];

    $strSP = 'Sp_reportStockQtyRp ';

    if ($req->get('g_modeReport') === 3) {
      $date2 = $req->get('date2');
      $date2Parts = explode('-', $date2);

      $Bulan2 = (int)$dateParts[1];
      $Tahun2 = $dateParts[0];

      $values[] = $Bulan2;
      $values[] = $Tahun2;

      $strSP .= 'Sp_reportMutasiperiode ?,?,';
    }

    $res = DB::connection('MGL')->select('exec ' . $strSP . '?,?,?,?,?,?,?,?,?,?,?,?,?,?',
      $values);

    return $res;
  }


}
