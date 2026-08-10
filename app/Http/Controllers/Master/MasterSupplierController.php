<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class MasterSupplierController extends Controller
{

  public function index(Request $req) {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBKOTA');

    $listData = DB::connection('SML')->select('SELECT USAHA , KODECUSTSUPP , NAMACUSTSUPP ,  ALAMAT1 , Kota, KODEPOS , NEGARA, TELPON , FAX , EMAIL, NPPH23 , NPPH22 , HARIHUTPIUT , IsAktif, Att , AttPhone , AttDepart, bank, NoAcc , ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, PPN, Jenis,namakota from vwbrowssupp where Jenis = 0');
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastersupplier' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function spListKota () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBKOTA');
    return $listData;

  }

public function loadAll(Request $request)
{
    $query = DB::connection('SML')->table('vwbrowssupp')
        ->where('Jenis', 0);

    // Total before filtering (cheap: single view, no joins)
    $total = (clone $query)->count();

    // Filtering
    $search = $request->input('search.value');
    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('KODECUSTSUPP', 'like', "%{$search}%")
              ->orWhere('USAHA', 'like', "%{$search}%")
              ->orWhere('NAMACUSTSUPP', 'like', "%{$search}%")
              ->orWhere('ALAMAT1', 'like', "%{$search}%")
              ->orWhere('Kota', 'like', "%{$search}%")
              ->orWhere('KODEPOS', 'like', "%{$search}%")
              ->orWhere('NEGARA', 'like', "%{$search}%")
              ->orWhere('TELPON', 'like', "%{$search}%")
              ->orWhere('FAX', 'like', "%{$search}%")
              ->orWhere('EMAIL', 'like', "%{$search}%");
        });
    }

    // Ordering — ROW_NUMBER() below needs an ORDER BY, and the column name is
    // client-supplied, so it must be checked against an allow-list before it
    // touches raw SQL.
    $allowedColumns = ['KODECUSTSUPP', 'USAHA', 'NAMACUSTSUPP', 'ALAMAT1', 'namakota', 'NEGARA', 'TELPON', 'EMAIL'];
    $orderColumn = 'KODECUSTSUPP';
    $orderDir = 'asc';
    if ($request->has('order')) {
        $columns = $request->input('columns');
        $order = $request->input('order')[0];
        $requestedColumn = $columns[$order['column']]['data'] ?? null;
        if (in_array($requestedColumn, $allowedColumns, true)) {
            $orderColumn = $requestedColumn;
        }
        $orderDir = ($order['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
    }

    $start = (int) $request->input('start', 0);
    $length = (int) $request->input('length', 10);

    // DBSMLNEW's compatibility level doesn't support SQL Server's OFFSET/FETCH
    // syntax (Laravel's default ->skip()->take() for sqlsrv), so page via
    // ROW_NUMBER() in a subquery instead. COUNT(*) OVER() rides along in the
    // same pass so the filtered total doesn't need its own separate query.
    $dataQuery = (clone $query)->select(
        'USAHA',
        'KODECUSTSUPP',
        'NAMACUSTSUPP',
        'ALAMAT1',
        'Kota',
        'KODEPOS',
        'NEGARA',
        'TELPON',
        'FAX',
        'EMAIL',
        'NPPH23',
        'NPPH22',
        'HARIHUTPIUT',
        'IsAktif',
        'Att',
        'AttPhone',
        'AttDepart',
        'bank',
        'NoAcc',
        'ATN',
        'NPWP',
        'NAMAPKP',
        'ALAMATPKP1',
        'KOTAPKP',
        'PPN',
        'Jenis',
        'namakota',
        DB::raw("ROW_NUMBER() OVER (ORDER BY {$orderColumn} {$orderDir}) as RowNum"),
        DB::raw('COUNT(*) OVER () as TotalCount')
    );

    $paged = DB::connection('SML')->query()->fromSub($dataQuery, 'sub');

    // DataTables sends length = -1 for the "Semua" (All) option, meaning no limit
    if ($length >= 0) {
        $paged->whereBetween('RowNum', [$start + 1, $start + $length]);
    }

    $data = $paged->get();

    $filtered = $data->isNotEmpty() ? (int) $data->first()->TotalCount : (clone $query)->count();

    return response()->json([
        "draw" => intval($request->input('draw')),
        "recordsTotal" => $total,
        "recordsFiltered" => $filtered,
        "data" => $data
    ]);
}

public function spAdd(Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBCUSTSUPP where KODECUSTSUPP = :kode', ['kode' => $req->kode]);
    
    $checkNIK = DB::connection('SML')->select('SELECT * FROM DBCUSTSUPP where NPWP = :npwp', ['npwp' => $req->npwp]);

    if ($check) {
        return 'Kode supplier sudah ada di database';
    }

     if ($checkNIK) {
        return 'NPWP sudah ada di database';
    }

    $listData = DB::connection('SML')->update('insert into DBCUSTSUPP (HARI, USAHA, KODECUSTSUPP, NAMACUSTSUPP, ALAMAT1, Kota, KODEPOS, NEGARA, TELPON, FAX, EMAIL, NPPH23, NPPH22, HARIHUTPIUT, IsAktif, Att, AttPhone, AttDepart, bank, NoAcc, ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, IsPpn, Jenis) VALUES (:haribiasa, :usaha, :kode, :nama, :alamat, :kota, :kodepos, :negara, :telp, :fax, :email, :npph23, :npph21, :haripiutang, :isaktif, :att, :attphone, :attdepart, :bank, :accno, :atn, :npwp, :namapkp, :alamatpkp, :kotapkp, :isppn, :jenis)', [
        'haribiasa' => $req->haribiasa,  
        'usaha' => $req->bentukusaha,
        'kode' => $req->kode,
        'nama' => $req->nama,
        'alamat' => $req->alamat,
        'kota' => $req->kota,
        'kodepos' => $req->kodepos,
        'negara' => $req->negara,
        'telp' => $req->telp,
        'fax' => $req->fax,
        'email' => $req->email,
        'npph23' => $req->pph23,
        'npph21' => $req->pph21,
        'haripiutang' => $req->haripiutang,
        'isaktif' => $req->isaktif,
        'att' => $req->att,
        'attphone' => $req->attphone,
        'attdepart' => $req->attdepart,
        'bank' => $req->bank,
        'accno' => $req->accno,
        'atn' => $req->atn,
        'npwp' => $req->npwp,
        'namapkp' => $req->namapkp,
        'alamatpkp' => $req->alamatpkp,
        'kotapkp' => $req->kotapkp,
        'isppn' => $req->isppn,
        'jenis' => $req->jenis
    ]);

    return 1;
}

  public function spDelete (Request $req) {

    $delete = DB::connection('SML')->update('delete from DBCUSTSUPP where KODECUSTSUPP = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBCUSTSUPP set USAHA =:usaha , NAMACUSTSUPP = :nama , HARI = :haribiasa, ALAMAT1 = :alamat, Kota = :kota, KODEPOS = :kodepos , NEGARA= :negara, TELPON = :telp , FAX  = :fax, EMAIL = :email, NPPH23 = :npph23, NPPH22 = :npph21, HARIHUTPIUT = :haripiutang , IsAktif = :isaktif, Att = :att , AttPhone = :attphone, AttDepart = :attdepart , bank = :bank , NoAcc = :accno, ATN = :atn, NPWP = :npwp , NAMAPKP = :namapkp , ALAMATPKP1 = :alamatpkp , KOTAPKP = :kotapkp , IsPpn = :isppn  where KODECUSTSUPP = :kode' , ['usaha'=> $req->bentukusaha , 'nama' => $req->nama , 'alamat' => $req->alamat , 'haribiasa' => $req->haribiasa , 'kota'=> $req->kota , 'kodepos' => $req->kodepos ,  'negara' => $req->negara, 'telp' => $req->telp ,'fax' => $req->fax , 'email' => $req->email , 'npph23' => $req->pph23 , 'npph21' => $req->pph21 , 'haripiutang' => $req->haripiutang , 'isaktif'=> $req->isaktif , 'att'=>$req->att, 'attphone'=> $req->attphone , 'attdepart'=>$req->attdepart, 'bank'=>$req->bank,'accno'=> $req->accno , 'atn' => $req->atn , 'npwp' => $req->npwp , 'namapkp' => $req->namapkp ,'alamatpkp' => $req->alamatpkp , 'kotapkp' => $req->kotapkp , 'isppn'=> $req->isppn , 'kode' => $req->kode]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT USAHA , KODECUSTSUPP , NAMACUSTSUPP ,  ALAMAT1 , Kota, KODEPOS , NEGARA, TELPON , FAX , EMAIL, NPPH23 , NPPH22 , HARIHUTPIUT , IsAktif, Att , AttPhone , AttDepart, bank, NoAcc , ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, IsPpn, Jenis from DBCUSTSUPP where KODECUSTSUPP = :kode', ['kode' => $req->kode]);
    // $listData = DB::connection('SML')->select('SELECT USAHA , KODECUSTSUPP , NAMACUSTSUPP ,  ALAMAT1 , Kota, KODEPOS , NEGARA, TELPON , FAX , EMAIL, NPPH23 , NPPH22 , HARIHUTPIUT , IsAktif, Att , AttPhone , AttDepart, bank, NoAcc , ATN, NPWP, NAMAPKP, ALAMATPKP1, KOTAPKP, PPN, Jenis from DBCUSTSUPP where Jenis = 0');
    return $detail;
  }

  public function loadAlamat(Request $req) {
  $listData = DB::connection('SML')->select(" SELECT A.KodeCustSupp, A.Nomor, A.Nama, A.Alamat, A.Telp, A.Fax,
                                              cast(A.Alamat as text) AlamatTxt ,A.UP
                                              from dbAlamatCust A
                                              where A.kodecustSupp= :kodeCustSupp and nomor<>0  and isnull(pSurat,0)=0 
                                              Order by A.Nomor",['kodeCustSupp'=>$req->kodeDetail]);
                                              return $listData;
                                        }

    public function submitAlamat (Request $req)
    {
        $ambilData = DB::connection('SML')->select("SELECT MAX(Nomor) as maxNomor
                                                    FROM DBALAMATCUST 
                                                    WHERE KODECUSTSUPP = :kode", 
                                                    ['kode' => $req->kode]);
        
        $nomorBaru = ($ambilData[0]->maxNomor ?? 0) + 1;
        
        $listData = DB::connection('SML')->insert("INSERT INTO dbAlamatCust (KODECUSTSUPP, Nomor, Alamat, Telp, Fax, Nama, pSurat, up, Area) 
                                                  VALUES (:kodeCustSupp, :nomor, :alamat, :telp, :fax, :nama, 0, :up, 'blackpink')", 
                                                  [
                                                      'kodeCustSupp' => $req->kodeCustSupp, 
                                                      'nomor' => $nomorBaru,
                                                      'nama' => $req->nama, 
                                                      'alamat' => $req->alamat, 
                                                      'fax' => $req->fax, 
                                                      'telp' => $req->telp, 
                                                      'up' => $req->up
                                                  ]);
        return 1;
    }

  public function spDeleteAlamat (Request $req) {

    $delete = DB::connection('SML')->update('DELETE dbAlamatCust where KodeCustsupp= :kode and Nomor= :nomor' , ['kode' => $req->kodeCustSupp,'nomor' => $req->nomor]);
    return $delete;
  }

  public function spEditAlamat (Request $req) 
  {
    $edit = DB::connection('SML')->update('UPDATE dbAlamatCust set Alamat = :alamat, Telp = :telp, Fax = :fax, Nama = :nama, up =:up where KodeCustSupp = :kodeCustSupp and Nomor = :nomor' , 
    ['alamat'=> $req->alamat, 
    'nama' => $req->nama, 
    'nomor' => $req->nomor, 
    'kodeCustSupp' => $req->kodeCustSupp, 
    'telp'=> $req->telp,
    'fax' => $req->fax,
    'up' => $req->up]);

    return $edit;
  }

}
