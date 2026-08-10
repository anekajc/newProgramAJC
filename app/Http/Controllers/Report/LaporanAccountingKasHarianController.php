<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanAccountingKasHarianController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("reportaccountingkasharian");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingkasharian' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {

    $TglAw = $req->get('date1');
    $TglAk = $req->get('date2');
    $Perkiraan    = $req->get('inputPerkiraan');
    $IDuser = '';
    $TipeTrans = '';
    $Valas = $req->get('detOrRekap');
    $Divisi = '01';

    $values  = [$Perkiraan, $TglAw, $TglAk, $Divisi, $IDuser, $TipeTrans, $Valas];
    
    $res = DB::connection('SML')->select('exec Sp_LapKasHarian ?,?,?,?,?,?,?',
      $values);

    // $values2  = [$Perkiraan, $TglAw, $TglAk, $Divisi];
    
    // $res2 = DB::connection('SML')->select(
    //     "EXEC Sp_LapSaldoAwal ?,?,?,?",
    //     $values2
    // );

    return response()->json([
        "res1" => $res
    ]);

    // return $res;
  }

  public function doReportSaldoAwal(Request $req)
  {
      $TglAw = $req->get('date1');
      $TglAk = $req->get('date2');
      $Perkiraan = $req->get('inputPerkiraan');
      $Divisi = '01';

      $values2  = [$Perkiraan, $TglAw, $TglAk, $Divisi];

      $res2 = DB::connection('SML')->select(
          "exec Sp_LapSaldoAwal ?,?,?,?",
          $values2
      );

      return response()->json([
          "res2" => $res2
      ]);
      // return $res2;
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

  public function loadPerkiraan()
  {
      $kode = 'KAS';
      $userid = auth()->user()->username;

      // $kode = $request->input('kode');
      // $userid = $request->input('userid');

      $listData = DB::connection('SML')->select("
          SELECT a.Perkiraan, b.Keterangan 
          FROM dbposthutpiut a
          LEFT OUTER JOIN dbperkiraan b ON b.Perkiraan = a.Perkiraan
          WHERE a.Kode = ? 
            AND a.Perkiraan IN (
                SELECT Perkiraan 
                FROM DBAKSESPERKIRAANR 
                WHERE UserID = ?
            )
          ORDER BY a.Perkiraan
      ", [$kode, $userid]);

      return $listData;
  }

}


