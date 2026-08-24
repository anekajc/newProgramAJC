<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;

class LaporanAccountingPiutangOutstandingJTController extends Controller {
  use AksesTrait;
  use GlobalTrait;
  // doKasharian / doInvoice / doLpb / doBp for the bottom voucher panel.
  use ReportVoucherTrait;

  public function index() {
    $akses = $this->cekAkses("reportaccountingpiutangoutstandingJT");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingpiutangoutstandingJT' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {

    $tanggal = $req->get('date1');
    $awal = $req->get('inputSuppAwal');
    $akhir= $req->get('inputSuppAkhir');
    $devisi = 0;
    $tipe = $req->get('inputOrd');
    $Perkiraan = $req->get('inputPerkiraan');
    $KodeVls = $req->get('valas_value');
    $IsiList = '';
    $Tanggal1 = '12-30-1899';
    $IsGroup = $req->get('inputGroup');
    $Lokasi = $req->get('inputLokasi');
    $PIC = $req->get('inputCust');

    // $values  = [$tanggal, $awal, $akhir, $devisi, $tipe, $Perkiraan, $KodeVls, $IsiList, $Tanggal1, $IsGroup, $Lokasi, $PIC];
    
    $res = DB::connection('SML')->select("exec sp_ReportSisaPiutangN '''{$tanggal}''','''{$awal}''','''{$akhir}''','{$KodeVls}',{$devisi},'''{$Perkiraan}''',{$tipe},'{$IsiList}','''{$Tanggal1}''',{$IsGroup},'''{$Lokasi}''','''{$PIC}''' ");

    return $res;
  }

  
// exec sp_ReportSisaPiutangN '''01-31-2026''','''CA001''','''DE024''','IDR',0,'''113100''',0,'','''12-30-1899''',0,'''-''','''-'''


  // public function doFilter(Request $req) {
  //   $kolom = ($req->get('inputOrd') == "N") ? 'nobukti, Tanggal' : 'KODEBRG, NAMABRG';
  //   $listData = DB::connection('MGL')->select('select ' . $kolom . ' from VwREPORTHISPO where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
  //   return $listData;
  // }

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

  public function loadLokasi () {
    $listData = DB::connection('SML')->select('Select KodeKebun,nama namaKebun from DbKebunCustSupp Group By KodeKebun,nama');
    return $listData;
  }

  public function loadCustomer () {
    $listData = DB::connection('SML')->select("
        SELECT 
            KodePIC,
            Nama AS NamaPIC
        FROM DBPICCUSTSUPP
        ORDER BY Nama ASC");

    return $listData;
  }

}


