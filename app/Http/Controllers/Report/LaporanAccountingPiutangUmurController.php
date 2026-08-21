<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;

class LaporanAccountingPiutangUmurController extends Controller {
  use AksesTrait;
  use GlobalTrait;
  use ReportVoucherTrait;   // doKasharian / doInvoice / doLpb / doBp (bottom voucher panel)

  public function index() {
    $akses = $this->cekAkses("reportaccountingpiutangumur");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingpiutangumur' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req)
  {
      $tanggal    = $req->get('date1');
      $tipe       = (int) $req->get('inputOrd');
      $devisi     = '01';
      $perkiraan  = $req->get('inputPerkiraan');
      $KodeVls    = $req->get('valas_value');

      $awal  = $req->get('inputSuppAwal');
      $akhir = $req->get('inputSuppAkhir');

      $selectedCust = $req->get('selectedCust', []);

      $db = DB::connection('SML');

      if (is_array($selectedCust) && count($selectedCust) > 0) {

          $db->table('DBCUSTOMIZE')
              ->where('Tipe', 20405)
              ->delete();
          foreach ($selectedCust as $agent) {
              $db->table('DBCUSTOMIZE')->insert([
                  'Tipe' => 20405,
                  'Id'   => $agent
              ]);
          }
          $result = $db->select(
              'exec sp_ReportUmurPiutang ?,?,?,?,?,?,?,?',
              [
                  $tanggal,
                  $tipe,
                  $awal,    
                  $akhir,    
                  $devisi,
                  $perkiraan,
                  $KodeVls,
                  'F'
              ]
          );
      }
      else {
          $result = $db->select(
              'exec sp_ReportUmurPiutang ?,?,?,?,?,?,?,?',
              [
                  $tanggal,
                  $tipe,
                  $awal,
                  $akhir,
                  $devisi,
                  $perkiraan,
                  $KodeVls,
                  'T'
              ]
          );
      }
      return response()->json($result);
  }

  public function doFilter(Request $req)
  {
      $listData = DB::connection('MGL')->select("
          SELECT 
              X.KodeCustSupp,
              X.NamaCustSupp,
              X.AGent,
              Y.NAMAGROUPCUSTSUPP AS NamaGroup,
              RTRIM(LTRIM(
                  ISNULL(X.Alamat1,'') +
                  CASE 
                      WHEN ISNULL(X.Alamat2,'') = '' 
                      THEN '' 
                      ELSE ' ' + X.Alamat2 
                  END
              )) AS Alamat,
              X.Kota
          FROM DBCUSTSUPP X
          LEFT OUTER JOIN DBGROUPCUSTSUPP Y 
              ON Y.KODEGROUPCUSTSUPP = X.AGent
          WHERE X.jenis = 1
          ORDER BY X.KodeCustSupp
      ");

      return response()->json($listData);
  }

  // public function doReportFilter(Request $req) {
  //   $kolom = ($req->get('inputOrd') == "N") ? 'nobukti' : 'KODEBRG';
  //   $res = [];

  //   for ($i=0; $i < count($req->listdata); $i++) {
  //     $row = DB::connection('MGL')->select('select * from VwREPORTHISPO where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);
      
  //     for ($j=0; $j < count($row); $j++) {
  //       $res = array_add($res, $i+$j, $row[$j]);
  //     }
  //   }
    
  //   return $res;
  // }

    public function loadPerkiraan()
  {
      $kode = 'PT';
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

public function loadValas()
  {
      $listData = DB::connection('SML')->select("
          select Kodevls,NamaVls,Kurs from dbValas order by kodevls
      ");

      return $listData;
  }

  public function loadSuppAwal(Request $request)
  {
      $perkiraan = $request->input('perkiraan');

      $listData = DB::connection('SML')->select("
          select a.KodeCustsupp, 
                a.NamaCustSupp as NamaCust, 
                a.Alamat, 
                a.Telpon 
          from vwBrowsCust A
          where a.isaktif = 1
            and a.PERKIRAAN = ?
          order by a.KodeCustsupp
      ", [$perkiraan]);

      return $listData;
  }


}


