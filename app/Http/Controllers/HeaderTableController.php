<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\DBFLMENU;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;

class HeaderTableController extends Controller


{
  public function saveHeaderTable (Request $req) {
    $username = \Auth::user()->username;
    // DBHEADERTABLE has a real `urut` column (multiple tables/tabs sharing one
    // href, e.g. purchaseOrder.blade.php's PR/PO/Outstanding-SO = urut 1/2/3),
    // but this method never filtered by it -- every table saved under the same
    // href clobbered whichever one saved last. Pages that never send `urut`
    // (e.g. pembelianpermintaannonagen) already have rows with urut = NULL in
    // the DB, so missing urut defaults to 0 and the comparison is NULL-safe to
    // keep matching those existing rows.
    $urut = $req->urut ?? 0;

    $res = DB::connection('SML')->select(
        "select * from  DBHEADERTABLE where username = :username and href = :href and ISNULL(urut, 0) = :urut",
        [
          "username" => $username,
          "href" => $req->href,
          "urut" => $urut
        ]
    );
    if ($res) {

    $res = DB::connection('SML')->update(
        "UPDATE DBHEADERTABLE SET header = :header , tipe = :tipe , isnumber = :isnumber , value = :value , isshown = :isshown  where username = :username and href = :href and ISNULL(urut, 0) = :urut",
        [
          "header" => $req->header ,
           "tipe" => $req->tipe ,
            "isnumber" => $req->isnumber ,
             "value" => $req->value ,
              "isshown" => $req->isshown ,
            "username" => \Auth::user()->username,

            "href" => $req->href,
            "urut" => $urut

        ]
    );
  } else {
    $res = DB::connection('SML')->statement(
        "INSERT INTO DBHEADERTABLE (username, href, header, tipe, isshown , value, isnumber, urut)
VALUES (:username , :href , :header , :tipe , :isshown , :value , :isnumber, :urut);",
        [
          "header" => $req->header ,
           "tipe" => $req->tipe ,
            "isnumber" => $req->isnumber ,
             "value" => $req->value ,
              "isshown" => $req->isshown ,
            "username" => \Auth::user()->username,

            "href" => $req->href,
            "urut" => $urut
        ]
    );

  }
  return 1;

  }

  public function getHeaderTable (Request $req) {
    $isnumberheadertable = [];
    $headertablevalue = [];
    $headertableheader = [];
    $headerisshown = [];
    $headertabletipe = [];
    $isparsed = 0;
    $username = \Auth::user()->username;
    // See saveHeaderTable() for why urut defaults to 0 and the comparison is
    // NULL-safe -- same reasoning applies here so a saved row is actually
    // found again by the table/tab that saved it, instead of any row for
    // that href (or none, for hrefs whose other urut already grabbed the
    // only unfiltered match).
    $urut = $req->urut ?? 0;

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $headertable = DB::connection("SML")->select("select *  from dbheadertable where  href= :href and username = :username and ISNULL(urut, 0) = :urut"  , ["username" => $username , "href" => $req->href , "urut" => $urut ]);

    if (count($headertable) > 0) {
      // header/tipe/isnumber/value/isshown are stored as JSON strings
      // (saveHeaderTable() writes them via JS's JSON.stringify()) and
      // returned as-is here -- pembelianpermintaannonagen's own JS already
      // does JSON.parse() on these fields itself when isparsed == 0, so
      // decoding them here would double-decode and break that page. Callers
      // that want real arrays (e.g. so.blade.php's soBuatCart()) need to
      // JSON.parse() these on their own side too.
      $isnumberheadertable = $headertable[0]->isnumber;
      $headertablevalue = $headertable[0]->value;
      $headertableheader = $headertable[0]->header;
      $headerisshown = $headertable[0]->isshown;
      $headertabletipe = $headertable[0]->tipe;
      $isparsed = 0;
    } else if ($req->href == 'pembelianpermintaannonagen') {
      $otorisasi = DB::connection("SML")->select("select NoBukti , Tanggal  , IsOtorisasi1, TglOto1, OtoUser1  from vwppl where  bulan = :bulan and tahun = :tahun and IsJasa = 0 and pAgen = 0 "  , ["bulan" => $periode->bulan , "tahun" => $periode->tahun ]);

      $otorisasi = collect($otorisasi)->groupBy('NOBUKTI');
      $tempOtorisasi = [];
      foreach ($otorisasi as $groupedData) {
          $tempOtorisasi[] = $groupedData;
      }

      if(!$tempOtorisasi) {

      } else {
        $isparsed = 1;
        foreach ($tempOtorisasi[0][0] as $key => $value) {

          if (str_contains($key, "Oto")) {


          } else {

              array_push($headertablevalue, $key);
            array_push($headertableheader, $key);
          array_push($headerisshown, 1);

            if (strtotime($value)) {

                  array_push( $isnumberheadertable , 2);
            } else if (is_numeric($value)) {
                array_push( $isnumberheadertable , 1);
            } else {

                  array_push($isnumberheadertable,0);
            }
          }



        }
      }

    }

    return [
      "isparsed" => $isparsed ,
      "headertableheader" => $headertableheader ,
      "isnumeric" => $isnumberheadertable,
      "headertablevalue" => $headertablevalue,
      "isshown" => $headerisshown,
      "tipe" => $headertabletipe];

  }


}
