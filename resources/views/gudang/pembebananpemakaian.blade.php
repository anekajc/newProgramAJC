@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title')Pembebanan Pemakaian @endsection
  @section('css')
  <link rel="stylesheet"
      href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
  <link rel="stylesheet"
      href="{!! URL::asset('css/tableMaster2.css') !!}?v={{ @filemtime(base_path('public/css/tableMaster2.css')) ?: '1' }}">
{{-- Search box #tabel_filter / #tabel2_filter / #tabel_oto_filter dihapus - DataTables
     bawaan kedua tab dimatikan (dom:'rt') dan diganti satu #searchBox di toolbar
     (lihat activeTable()/onToolbarSearch() di bagian JS halaman ini). #tabel_oto sendiri
     sudah dikomentari total di bawah, jadi CSS-nya memang mati. --}}

{{-- Search box #tabel_add_list_perkiraan_filter / _costing_filter / _subcosting_filter
     dihapus - modal Perkiraan/Costing/Sub Costing sekarang pakai .rt-picker-v2, yang
     sudah menata kotak search DataTables-nya sendiri (lihat report-table.css). Style
     id-scoped lama di sini punya specificity lebih tinggi dari
     .rt-picker-v2 .dataTables_filter input, jadi kalau dibiarkan bakal menimpanya. --}}

@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">

<div id="printContainer" style="display:none">


</div>
<div id="contentContainer" class="" >
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="tb-report">
    <div class="content">

      <div class="toolbar">
        <div>
          <input class="search-inp" type="text" id="searchBox" placeholder="Cari data..."
              oninput="onToolbarSearch()" style="width:200px">
        </div>
      </div>

      {{-- class "nav" WAJIB ada di sini - lihat catatan di gudang/pemakaianbarang.blade.php:
           Bootstrap Tab plugin mencari kontainer aktif lewat closest(".nav, .list-group"),
           tanpa itu klik tab kedua tidak melepas active dari pill pertama dan tab macet. --}}
      <div class="tab-toggle nav" id="nav-tab" role="tablist" style="margin-bottom: 7px">
        <a class="tab-toggle-btn active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab"
            aria-controls="home" aria-selected="true">PP Belum Otorisasi</a>
        <a class="tab-toggle-btn" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
            aria-controls="profile" aria-selected="false">PP Sudah Otorisasi</a>
      </div>

      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="table-outer">
            <div class="table-wrap">
              <table id="tabel" class="tb aksi-hover">
                <thead>
                  <tr>
                    <th class="rt-fixed-th">Actions</th>
                    <th>No.Bukti</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Perkiraan</th>
                  </tr>
                </thead>
                <tbody id="tabel_data" class="text-left">
                  @for ($i = 0; $i < count($outstandingArray); $i++)
                  <tr>
                    <td class="text-center">
                      <div class="action-buttons">
                        <button type="button" class="btn-action-sm btn-action-warning" data-toggle="tooltip" title="Detail" onclick="buttonDetailKoreksi('{{ $outstandingArray[$i][0]->NOBUKTI }}' )"><i class="bi bi-info"></i></button>
                        <button type="button" class="btn-action-sm btn-action-primary" data-toggle="tooltip" title="Otorisasi" onclick="buttonOtorisasi('{{ $outstandingArray[$i][0]->NOBUKTI }}', '{{ $outstandingArray[$i][0]->IsOtorisasi1 }}')"><i class="bi bi-key"></i></button>
                        <button type="button" class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" onclick="buttonKoreksi('{{ $outstandingArray[$i][0]->NOBUKTI }}' , 'edit')"><i class="bi bi-pencil-fill"></i></button>
                      </div>
                    </td>
                    <td>{{ $outstandingArray[$i][0]->NOBUKTI}}</td>
                    <td>{!! date("Y/m/d", strtotime($outstandingArray[$i][0]->TANGGAL)) !!}</td>
                    <td>{{ $outstandingArray[$i][0]->Keterangan}}</td>
                    <td>{{ $outstandingArray[$i][0]->Perkiraan}}</td>
                  </tr>
                  @endfor
                </tbody>
              </table>
            </div>
            <div class="table-footer"><span id="footerLabel1">Belum ada data</span></div>
          </div>
        </div>
        {{-- Tab belum oto --}}
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="table-outer">
            <div class="table-wrap">
              <table id="tabel2" class="tb aksi-hover">
                <thead>
                  <tr>
                    <th class="rt-fixed-th">Actions</th>
                    <th>No.Bukti</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Perkiraan</th>
                    <th>User Oto</th>
                    <th>Tanggal Oto</th>
                    {{-- <th scope="col">Oto</th> --}}
                  </tr>
                </thead>
                <tbody id="tabel2_data" class="text-left">
                  @for ($i = 0; $i < count($penerimaanArray); $i++)
                  <tr>
                    <td class="text-center">
                      <div class="action-buttons">
                        <button type="button" class="btn-action-sm btn-action-warning" data-toggle="tooltip" title="Detail" onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI }}' )"><i class="bi bi-info"></i></button>
                        <button type="button" class="btn-action-sm btn-action-danger" data-toggle="tooltip" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('{{ $penerimaanArray[$i][0]->NOBUKTI}}','{{ $penerimaanArray[$i][0]->IsOtorisasi1 }}')"><i class="bi bi-key-fill"></i></button>
                        <button type="button" class="btn-action-sm btn-action-info" data-toggle="tooltip" title="Print" onclick="submitPrint('{{$penerimaanArray[$i][0]->NOBUKTI}}')"><i class="bi bi-printer"></i></button>
                      </div>
                    </td>
                    <td>{{ $penerimaanArray[$i][0]->NOBUKTI}}</td>
                    <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->TANGGAL)) !!}</td>
                    <td>{{ $penerimaanArray[$i][0]->Keterangan}}</td>
                    <td>{{ $penerimaanArray[$i][0]->Perkiraan}}</td>
                    <td>{{ $penerimaanArray[$i][0]->OtoUser1}}</td>
                    <td>
                      @if ($penerimaanArray[$i][0]->TglOto1)
                        {{ \Carbon\Carbon::parse($penerimaanArray[$i][0]->TglOto1)->format('Y/m/d') }}
                      @endif
                    </td>

                    {{-- @if ($tempPenerimaan[$i]->IsOtorisasi1)
                        <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                        @else
                        <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                        @endif --}}
                  </tr>
                  @endfor
                </tbody>
              </table>
            </div>
            <div class="table-footer"><span id="footerLabel2">Belum ada data</span></div>
          </div>
        </div>
        {{-- Tab sudah oto --}}
  {{-- <div class="tab-pane fade" id="home2" role="tabpanel" aria-labelledby="home2-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
              <div class="container-fluid">
                <table id="tabel_oto" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="padding: 4px 12px;"  scope="col">Actions</th>
                      <th style="padding: 4px 12px;"  scope="col">No. Urut</th>
                      <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                      <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;"  scope="col">Sales</th>
                      <th style="padding: 4px 12px;"  scope="col">User</th>
                      <th style="padding: 4px 12px;"  scope="col">No.Ref</th>
                      <th style="padding: 4px 12px;"  scope="col">User Oto</th>
                      <th style="padding: 4px 12px;"  scope="col">Tgl Oto</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_oto_data" class="text-left">
                     @for ($i = 0; $i < count($tempPenerimaan2); $i++)
                    <tr>
                      <td class='text-center'>
                        <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('{{ $tempPenerimaan2[$i]->NOBUKTI }}' )"><i class="bi bi-info"></i></button>
                        @if ($tempPenerimaan2[$i]->IsOtorisasi1 == 1)
                        <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempPenerimaan2[$i]->NOBUKTI }}' , 'edit')"><i class="bi bi-key"></i></button>
                        @else
                        <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('{{ $tempPenerimaan2[$i]->NOBUKTI }}' , 'add')"><i class="bi bi-key"></i></button>
                        @endif
                      </td>
                      <td>{{ $tempPenerimaan2[$i]->NOURUT}}</td>
                      <td>{{ $tempPenerimaan2[$i]->NOBUKTI}}</td>
                      <td>{!! date("Y/m/d", strtotime($tempPenerimaan2[$i]->TANGGAL)) !!}</td>
                      <td>{{ $tempPenerimaan2[$i]->NAMASLS}}</td>
                      <td>{{ $tempPenerimaan2[$i]->IDUSER}}</td>
                      <td>{{ $tempPenerimaan2[$i]->RefPR}}</td>
                      <td>{{ $tempPenerimaan2[$i]->OtoUser1 }}</td>
                      <td>{!! $tempPenerimaan2[$i]->TglOto1 ? date("Y/m/d", strtotime($tempPenerimaan2[$i]->TglOto1)) : '' !!}</td>
                    </tr>
                @endfor
                </tbody>
                </table>
              </div>
            </div>
          </div>
        </div> --}}
      {{-- end tab sudah oto --}}

</div>
</div>
</div>
</div>
</div>





<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row">
    <div class="col-8 text-left">
      <h2>Form Penyerahan Sample</h2>
    </div>
    <div class="col-4 text-right action-group" id="contentContainer">
      <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nobukti" placeholder="" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  >
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>
    <hr/>
        <div class="tb-report container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
          <div class="table-outer">
            <div class="table-wrap">
              <table id="addTable" class="tb">
                <thead>
                  <tr>
                    <th>Serahkan</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Stock</th>
                  </tr>
                </thead>
                <tbody id="addTableData">
                  <tr>
                    <td colspan="8" class="text-center">Belum ada data</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
    </div>
    <div class="row mt-2" style="margin-top: 0">
      <div class="col-md-12 text-right mt-4">
        <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit</button>
        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>
    </div>
  </div>
</div>


<div id="page3" style="display: none" class="mainpage container-fluid" >
  <div class="row">
    <div class="col-8 text-left">
      <h2>Koreksi Pembebanan Pemakaian</h2>
    </div>
    <div class="col-4 text-right" id="contentContainer">
      <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    {{-- <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" /> --}}
    <div class="row">
        <input type="hidden" class="form-control" id="input_koreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label" style="margin-top:-5px;">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-left" id="input_koreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
        </div>

        <div class="tb-report container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
          <div class="table-outer">
            <div class="table-wrap">
              <table id="koreksiTable" class="tb">
                <thead>
                <tr>
                  <th colspan="6">Deskripsi Barang</th>
                  <th colspan="1"></th>
                </tr>
                <tr>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Perkiraan</th>
                  <th>Nama Perkiraan</th>
                  <th class="rt-fixed-th">Actions</th>
                </tr>
                </thead>
                <tbody id="koreksiTableData">
                  <tr>
                    <td colspan="11" class="text-center">Belum ada data</td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
    </div>

    <div id="formKoreksiEdit" class="container-fluid showhideitem">
    <div class="row">
      <div class="col-4">
        <h4 id="h4KoreksiEditItem" style="margin-left:-15px;">Edit Item</h4>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="row align-items-center mt-2">
          <label class="col-4 col-form-label font-weight-bold">Perkiraan</label>
          <div class="col">
            <div class="input-group">
              <input id="KoreksiEditPerkiraan" type="text" class="form-control text-left" placeholder="Perkiraan" onkeypress="onKeyPressPicker(event,'perkiraan')">
              <button type="button" onclick="openPicker('perkiraan')" class="btn btn-primary btn-sm rounded-right shadow-sm"><i class="bi bi-plus"></i></button>
              <input type="hidden" id="KoreksiEditNamaPerkiraan">
            </div>
          </div>
        </div>

        <div class="row align-items-center mt-2">
          <label class="col-4 col-form-label font-weight-bold">Costing</label>
          <div class="col">
            <div class="input-group">
              <input id="KoreksiEditCosting" type="text" class="form-control text-left" placeholder="Costing" onkeypress="onKeyPressPicker(event,'costing')">
              <button type="button" onclick="openPicker('costing')" class="btn btn-primary btn-sm rounded-right shadow-sm"><i class="bi bi-plus"></i></button>
              <input type="hidden" id="input_costing">
            </div>
          </div>
        </div>

        <div class="row align-items-center mt-2">
          <label class="col-4 col-form-label font-weight-bold">Sub Costing</label>
          <div class="col">
            <div class="input-group">
              <input id="KoreksiEditSubCosting" type="text" class="form-control text-left" placeholder="Sub Costing" onkeypress="onKeyPressPicker(event,'subcosting')">
              <button type="button" onclick="openPicker('subcosting')" class="btn btn-primary btn-sm rounded-right shadow-sm"><i class="bi bi-plus"></i></button>
              <input type="hidden" id="input_sub_costing">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-12 text-right" id="contentContainer">
        <button type="button" class="btn btn-danger" onclick="buttonKoreksiItemBatal()">Batal</button>

        <button id="buttonSubmitKoreksiEdit" type="button" onclick="submitKoreksiEdit()" class="btn btn-primary" >Submit Edit</button>
      </div>
    </div>
  </div>
    <hr/>

  </div>
</div>
</div>
{{-- Start Modal List perkiraan --}}
  <div class="modal fade rt-picker-v2" id="modalAddListPerkiraan" role="dialog" aria-labelledby="labelPerkiraan" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="labelPerkiraan">Pilih Perkiraan</h5>
          <button type="button" class="close" onclick="buttonAddListBatal()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table id="tabel_add_list_perkiraan" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th scope="col">Perkiraan</th>
                <th scope="col">Keterangan</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List perkiraan --}}

{{-- Start Modal List costing --}}
  <div class="modal fade rt-picker-v2" id="modalAddListCosting" role="dialog" aria-labelledby="labelCosting" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="labelCosting">Pilih Costing</h5>
          <button type="button" class="close" onclick="buttonAddListBatal()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table id="tabel_add_list_costing" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th scope="col">Kode Cost</th>
                <th scope="col">Nama Cost</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List costing --}}

{{-- Start Modal List subcosting --}}
  <div class="modal fade rt-picker-v2" id="modalAddListSubCosting" role="dialog" aria-labelledby="labelSubCosting" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="labelSubCosting">Pilih Sub Costing</h5>
          <button type="button" class="close" onclick="buttonAddListBatal()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table id="tabel_add_list_subcosting" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th scope="col">Kode Cost</th>
                <th scope="col">Kode Sub Cost</th>
                <th scope="col">Nama Sub Cost</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List subcosting --}}

<div id="page4" style="display: none" class="mainpage container-fluid" >
  <div class="row">
    <div class="col-8 text-left">
      <h2>Detail Pembebanan Pemakaian</h2>
    </div>
    <div class="col-4 text-right action-group" id="contentContainer">
      <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
        <input type="hidden" class="form-control" id="input_detailkoreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-left" id="input_detailkoreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
        </div>
        </div>
    <hr/>
        <div class="tb-report container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
          <div class="table-outer">
            <div class="table-wrap">
              <table id="detailKoreksiTable" class="tb">
                <thead>
                <tr>
                  <th colspan="6">Deskripsi Barang</th>
                </tr>
                <tr>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Perkiraan</th>
                  <th>Nama Perkiraan</th>
                </tr>
                </thead>
                <tbody id="detailKoreksiTableData">
                  <tr>
                    <td colspan="6" class="text-center">Belum ada data</td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
    </div>
  </div>
</div>





@endsection

@section('js')
<script type="text/javascript">



let dataTableAdd = []
let dataTableKoreksi = []
let barangKoreksiEdit = {}

// dom:'rt' membuang search box + info line bawaan DataTables — diganti satu #searchBox
// di toolbar (lihat activeTable()/onToolbarSearch()) supaya kedua tab pakai satu kotak
// pencarian. emptyTable dipakai footer draw handler di bawah untuk teks "Tidak ada data".
const dtOptionsOutstanding = {
  dom: 'rt',
  order: [
    [1, 'asc']
  ],
  lengthChange: false,
  paging: false,
  language: {
    emptyTable: 'Tidak ada data'
  },
  columnDefs: [{
      type: 'date',
      targets: [2]
    },
    {
      className: 'text-center',
      targets: [0],
      orderable: false
    }
  ]
};

const dtOptionsPenerimaan = {
  dom: 'rt',
  order: [
    [1, 'asc']
  ],
  lengthChange: false,
  paging: false,
  language: {
    emptyTable: 'Tidak ada data'
  },
  columnDefs: [{
      type: 'date',
      targets: [2]
    },
    {
      className: 'text-center',
      targets: [0],
      orderable: false
    }
  ]
};

// Tabel DataTable yang sedang terlihat — dipakai toolbar search supaya satu kotak
// mengontrol tab manapun yang sedang aktif.
function activeTable() {
  return $('.tab-pane.active table').eq(0).DataTable();
}

function onToolbarSearch() {
  activeTable().search($('#searchBox').val() || '').draw();
}

function updateFooter(tableId, footerId) {
  const api = $('#' + tableId).DataTable();
  const count = api.rows({
    search: 'applied'
  }).count();
  $('#' + footerId).text(count ? ('Menampilkan ' + count + ' baris') : 'Tidak ada data');
}

$(document).ready(function(){
  $("#tabel").DataTable(dtOptionsOutstanding);
  $("#tabel2").DataTable(dtOptionsPenerimaan);

  $("#tabel").on('draw.dt', function() {
    updateFooter('tabel', 'footerLabel1');
  });
  $("#tabel2").on('draw.dt', function() {
    updateFooter('tabel2', 'footerLabel2');
  });
  updateFooter('tabel', 'footerLabel1');
  updateFooter('tabel2', 'footerLabel2');

  // columns.adjust() wajib dipanggil setelah tab baru terlihat — DataTables mengukur
  // lebar kolom 0px kalau tabel masih di dalam tab-pane yang hidden saat init.
  $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    const targetId = $(e.target).attr('href');
    $(targetId + ' table').DataTable().columns.adjust();
    activeTable().search($('#searchBox').val() || '').draw();
  });

  $('[data-toggle="tooltip"]').tooltip({
    container: 'body',
    boundary: 'window'
  });

  // Satu-satunya titik di mana modal pemilih Perkiraan/Costing/Sub Costing dijamin
  // sudah display:block, jadi di sinilah lebar kolom DataTables boleh dihitung —
  // pola sama dengan #formAddListItem di permintaanpemakaian.blade.php.
  $('#modalAddListPerkiraan').on('shown.bs.modal', function() { flushPickerPending('perkiraan'); });
  $('#modalAddListCosting').on('shown.bs.modal', function() { flushPickerPending('costing'); });
  $('#modalAddListSubCosting').on('shown.bs.modal', function() { flushPickerPending('subcosting'); });

  //   formAddListItem
});

// ===================== Perkiraan / Costing / Sub Costing picker =====================
// Satu mesin generik dipakai untuk ketiga field, mengikuti pola resolveBarang()/
// initBarangTable() di permintaanpemakaian.blade.php: ketik kode + Enter (atau klik +)
// -> kode yang PERSIS cocok langsung mengisi field tanpa modal; selain itu modal
// .rt-picker-v2 dibuka dengan pencarian sudah terisi, dan klik baris langsung memilih.
//
// Beda dengan referensinya: Costing/Sub Costing bergantung pada field induk
// (Perkiraan/Costing), jadi cache-nya di-key per nilai induk (pickerCacheKey), dan
// openPicker() TETAP menunggu hasil fetch sebelum modal ditampilkan (bukan optimistic
// show-dulu-isi-belakangan) supaya guard "induk belum dipilih" / "induk tidak punya
// anak" — perilaku asli buttonKoreksiListCosting/SubCosting — tetap berlaku persis
// sebelum modal sempat terbuka.
const PICKERS = {
  perkiraan: {
    modal: '#modalAddListPerkiraan',
    table: '#tabel_add_list_perkiraan',
    codeInput: '#KoreksiEditPerkiraan',
    nameInput: '#KoreksiEditNamaPerkiraan',
    url: "{{ url('pembebananpemakaianlistperkiraan') }}",
    codeField: 'Perkiraan',
    nameField: 'Keterangan',
    columns: [{ data: 'Perkiraan' }, { data: 'Keterangan' }],
    params: () => ({}),
    parent: null,
    clears: ['costing', 'subcosting'],
  },
  costing: {
    modal: '#modalAddListCosting',
    table: '#tabel_add_list_costing',
    codeInput: '#KoreksiEditCosting',
    nameInput: '#input_costing',
    url: "{{ url('pembebananpemakaianlistcosting') }}",
    codeField: 'KodeCost',
    nameField: 'NamaCost',
    columns: [{ data: 'KodeCost' }, { data: 'NamaCost' }],
    params: () => ({ perkiraan: $('#KoreksiEditPerkiraan').val() }),
    parent: {
      input: '#KoreksiEditPerkiraan',
      msg: 'Silakan pilih perkiraan terlebih dahulu.',
      emptyMsg: 'Perkiraan ini tidak memiliki costing.',
    },
    clears: ['subcosting'],
  },
  subcosting: {
    modal: '#modalAddListSubCosting',
    table: '#tabel_add_list_subcosting',
    codeInput: '#KoreksiEditSubCosting',
    nameInput: '#input_sub_costing',
    url: "{{ url('pembebananpemakaianlistsubcosting') }}",
    codeField: 'KodeSubCost',
    nameField: 'NamaSubCost',
    columns: [{ data: 'KodeCost' }, { data: 'KodeSubCost' }, { data: 'NamaSubCost' }],
    params: () => ({ kodeCost: $('#KoreksiEditCosting').val() }),
    parent: {
      input: '#KoreksiEditCosting',
      msg: 'Silakan pilih costing terlebih dahulu.',
      emptyMsg: 'Costing ini tidak memiliki sub costing.',
    },
    clears: [],
  },
};

// cache: daftar per nilai induk saat ini (key '' untuk Perkiraan, yang tidak punya induk).
// dt: instance DataTables aktif. busy: guard Enter-mashing. pendingList/pendingTerm:
// dipakai initPickerTable() kalau modal belum display:block saat init dipanggil.
const pickerState = {
  perkiraan: { cache: {}, dt: null, busy: false, pendingList: null, pendingTerm: '' },
  costing: { cache: {}, dt: null, busy: false, pendingList: null, pendingTerm: '' },
  subcosting: { cache: {}, dt: null, busy: false, pendingList: null, pendingTerm: '' },
};

function pickerCacheKey(key) {
  let cfg = PICKERS[key];
  return cfg.parent ? ($(cfg.parent.input).val() || '') : '';
}

function pickerCachedList(key) {
  return pickerState[key].cache[pickerCacheKey(key)];
}

function fetchPickerList(key, callback) {
  let cfg = PICKERS[key];
  $.ajax({
    url: cfg.url,
    type: 'get',
    data: cfg.params(),
    success: function(res) {
      let list = res || [];
      pickerState[key].cache[pickerCacheKey(key)] = list;
      if (callback) callback(list);
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan saat mengambil data.');
    }
  });
}

// destroy() TIDAK membersihkan style="width:...px" yang ditulis DataTables ke tiap
// <th> — init berikutnya membacanya sebagai lebar tetap dan kolom menyusut tiap kali
// modal dibuka ulang. Bersihkan dulu sebelum re-init (sama seperti
// resetBarangTableWidths() di permintaanpemakaian.blade.php).
function resetPickerTableWidths(key) {
  let $t = $(PICKERS[key].table);
  $t.css('width', '');
  $t.children('colgroup').remove();
  $t.find('thead th').css('width', '');
}

function initPickerTable(key, list, term) {
  let cfg = PICKERS[key];
  let st = pickerState[key];

  // DataTables menghitung lebar kolom dari container saat init, dan modal BS4 baru
  // display:block setelah transisi fade selesai — kalau init dipanggil sebelum itu,
  // lebar diukur di container 0px. Antre saja, dieksekusi di shown.bs.modal.
  if (!$(cfg.modal).is(':visible')) {
    st.pendingList = list;
    st.pendingTerm = term || '';
    return;
  }
  st.pendingList = null;

  if ($.fn.DataTable.isDataTable(cfg.table)) {
    $(cfg.table).DataTable().clear().destroy();
  }
  resetPickerTableWidths(key);

  st.dt = $(cfg.table).DataTable({
    data: list,
    deferRender: true,
    paging: true,
    pageLength: 25,
    lengthChange: false,
    searching: true,
    order: [],
    language: {
      emptyTable: 'Tidak ada data'
    },
    columns: cfg.columns,
    createdRow: function(row, data) {
      row.className = 'pick-row';
      row.onclick = function() {
        applyPick(key, data);
      };
    }
  });

  st.dt.search(term || '').draw();
  st.pendingTerm = '';
}

function flushPickerPending(key) {
  let st = pickerState[key];
  if (st.pendingList !== null) {
    initPickerTable(key, st.pendingList, st.pendingTerm);
  }
}

// Buka modal untuk `key`. Menjaga guard field induk yang sama seperti
// buttonKoreksiListCosting()/buttonKoreksiListSubCosting() versi lama: induk kosong
// -> peringatan, tidak fetch; induk terisi tapi tidak punya anak -> field dikosongkan
// + pesan info, modal TIDAK dibuka.
function openPicker(key, term) {
  let cfg = PICKERS[key];
  term = term || '';

  if (cfg.parent && !$(cfg.parent.input).val()) {
    alertify.warning(cfg.parent.msg);
    return;
  }

  let showList = function(list) {
    if (cfg.parent && !list.length) {
      $(cfg.codeInput).val('');
      $(cfg.nameInput).val('');
      alertify.message(cfg.parent.emptyMsg);
      return;
    }
    initPickerTable(key, list, term);
    $(cfg.modal).modal('show');
  };

  let cached = pickerCachedList(key);
  if (cached) {
    showList(cached);
    return;
  }

  fetchPickerList(key, showList);
}

// Titik masuk tunggal buat Enter dan tombol plus — pola sama dengan resolveBarang() di
// permintaanpemakaian.blade.php. Kode yang PERSIS cocok (case-insensitive, trimmed)
// langsung mengisi field tanpa modal; selain itu (sebagian, nama, atau kosong) modal
// dibuka dengan `term` sudah terisi di kotak search-nya.
function resolvePicker(key, term) {
  let cfg = PICKERS[key];
  term = (term || '').trim();

  if (cfg.parent && !$(cfg.parent.input).val()) {
    alertify.warning(cfg.parent.msg);
    return;
  }

  if (!term) {
    openPicker(key, '');
    return;
  }

  if (pickerState[key].busy) {
    return;
  }

  let findExact = function(list) {
    let needle = term.toLowerCase();
    return (list || []).find(item => String(item[cfg.codeField] || '').trim().toLowerCase() === needle);
  };

  let cached = pickerCachedList(key);
  if (cached) {
    let hit = findExact(cached);
    if (hit) {
      applyPick(key, hit);
    } else {
      openPicker(key, term);
    }
    return;
  }

  pickerState[key].busy = true;
  fetchPickerList(key, function(list) {
    pickerState[key].busy = false;
    let hit = findExact(list);
    if (hit) {
      applyPick(key, hit);
    } else {
      openPicker(key, term);
    }
  });
}

function onKeyPressPicker(e, key) {
  if (e.which === 13) {
    e.preventDefault();
    resolvePicker(key, $(PICKERS[key].codeInput).val());
  }
}

// Satu-satunya tempat yang mengisi kode+nama field, baik dari klik baris di picker
// maupun dari kecocokan persis di resolvePicker() — lalu membuang pilihan turunan
// yang jadi usang (mis. ganti Perkiraan membuang Costing & Sub Costing).
function applyPick(key, item) {
  let cfg = PICKERS[key];
  $(cfg.codeInput).val(item[cfg.codeField]);
  $(cfg.nameInput).val(item[cfg.nameField]);
  $(cfg.modal).modal('hide');

  cfg.clears.forEach(function(childKey) {
    let childCfg = PICKERS[childKey];
    $(childCfg.codeInput).val('');
    $(childCfg.nameInput).val('');
  });
}

function buttonAddListBatal() {
  $('#modalAddListPerkiraan').modal('hide');
  $('#modalAddListCosting').modal('hide');
  $('#modalAddListSubCosting').modal('hide');
}


function buttonKoreksiEditItem (i) {
  let barang = dataTableKoreksi[i];
  barangKoreksiEdit = barang;

  document.getElementById("KoreksiEditPerkiraan").value = barang.KodePerkiraan;
  document.getElementById("KoreksiEditNamaPerkiraan").value = barang.namaPerkiraan;

  document.getElementById("KoreksiEditCosting").value = barang.KODECOST;
  document.getElementById("input_costing").value = barang.NamaCost;

  document.getElementById("KoreksiEditSubCosting").value = barang.KODESUBCOST;
  document.getElementById("input_sub_costing").value = barang.NamaSubCost;

  $('#formKoreksiEdit').show();
}

function refreshDataTableKoreksi (nobukti) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pembebananpemakaiangetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        buttonCloseForm();
        alertify.warning("Data tidak ditemukan");
        return;
      }

      dataTableKoreksi = res;

      let rowTable = "";

      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-right">${parseFloat(item.Qnt).toLocaleString()}</td>
          <td class="text-center">${item.Satuan}</td>
          <td>${item.KodePerkiraan ?? ''}</td>
          <td>${item.namaPerkiraan ?? ''}</td>
          <td class="text-center">
            <button type="button" class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pencil-fill"></i></button>
          </td>
        </tr>`;
      });

      document.getElementById("koreksiTableData").innerHTML = rowTable;

      let header = res[0];

      $("#input_koreksi_nobukti").val(header.NoBukti || header.NOBUKTI);

      buttonKoreksiItemBatal();
    },
    error: function (err) {
      console.error(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}



function buttonOtorisasi(nobukti, isOtorisasi) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  if (Number(isOtorisasi) > 0) {
    alertify.warning('Sudah diotorisasi');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pembebananpemakaianspotorisasi') !!}",
    type: "post",
    dataType: "json",
    data: {
      _token,
      nobukti,
      otorisasi: 1
    },
    success: function (res) {
      if (res.status > 0) {
        alertify.success(res.msg);
        loadAll();
      } else {
        alertify.warning(res.msg);
      }
    },
    error: function (err) {
      console.log(err);
      alertify.error('Terjadi kesalahan. Silakan refresh browser.');
    }
  });
}

function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  alertify.prompt("Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          $.abort();
        }
      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('pembebananpemakaianspbatalotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          pket :value
        },
        success: function (res) {
          alertify.success('Berhasil batal otorisasi');
          loadAll();
        },
        error: function (err) {
          console.error(err);
          alertify.warning('Terjadi kesalahan, silakan refresh browser');
        }
      });
    },
    function () {
      console.log('Batal otorisasi dibatalkan');
      alertify.error("Action cancelled");
    }
  );
}


function submitKoreksiEdit () {
  let _token = $("#_token").val();

  if (!barangKoreksiEdit) {
    alertify.warning('Tidak ada data yang dipilih untuk koreksi.');
    return;
  }

  let nobukti = barangKoreksiEdit.NoBukti || barangKoreksiEdit.NOBUKTI;
  let urut    = barangKoreksiEdit.Urut || barangKoreksiEdit.URUT;

  let kodePerkiraan = ($('#KoreksiEditPerkiraan').val() || '').trim();
  let kodeCost      = ($('#KoreksiEditCosting').val() || '').trim();
  let kodeSubCost   = ($('#KoreksiEditSubCosting').val() || '').trim();

  if (!nobukti || !urut) {
    alertify.warning('Data item tidak lengkap (NoBukti/Urut).');
    return;
  }
  if (!kodePerkiraan) {
    alertify.warning('Perkiraan wajib diisi.');
    return;
  }

  // Field-nya sekarang bisa diketik langsung (lihat resolvePicker()), jadi ketikan
  // yang tidak pernah di-resolve ke kode yang benar (mis. diketik lalu modal-nya
  // dibatalkan) bisa lolos sampai sini. Divalidasi HANYA kalau cache picker untuk
  // field itu sudah pernah terisi di sesi ini (artinya field ini memang sempat
  // disentuh lewat picker) — field yang tidak pernah disentuh (nilai bawaan dari
  // buttonKoreksiEditItem() saat form dibuka) dipercaya begitu saja, sama seperti
  // dulu saat field-nya masih disabled.
  let invalidField = ['perkiraan', 'costing', 'subcosting'].find(function(key) {
    let code = { perkiraan: kodePerkiraan, costing: kodeCost, subcosting: kodeSubCost }[key];
    if (!code) return false;
    let list = pickerCachedList(key);
    if (!list) return false;
    let needle = code.toLowerCase();
    return !list.some(item => String(item[PICKERS[key].codeField] || '').trim().toLowerCase() === needle);
  });
  if (invalidField) {
    let label = { perkiraan: 'Perkiraan', costing: 'Costing', subcosting: 'Sub Costing' }[invalidField];
    alertify.warning('Kode ' + label + ' tidak dikenali, silakan pilih dari daftar.');
    return;
  }

  $.ajax({
    url: "{!! url('pembebananpemakaianspkoreksi') !!}",
    type: "post",
    data: {
      _token,
      nobukti,
      urut,
      kodeperkiraan: kodePerkiraan,
      kodecost: kodeCost,
      kodesubcost: kodeSubCost
    },
    success: function (res) {
      if (res && res.success) {
        $("#modalKoreksiEdit").modal("hide");
        refreshDataTableKoreksi(nobukti);
        loadAll();
        alertify.success(res.message || 'Koreksi akun berhasil disimpan.');
      } else {
        alertify.warning(res.message || 'Koreksi gagal disimpan.');
      }
    },
    error: function (err) {
      console.log(err);
      const msg = err?.responseJSON?.message || 'Terjadi kesalahan, silakan refresh browser';
      alertify.warning(msg);
    }
  });
}



function buttonKoreksiItemBatal () {

  $('.showhideitem').hide();
}


function buttonKoreksi (nobukti) {
  console.log('buttonKoreksi', nobukti);

  let akses = $("#akses_iskoreksi").val();
  $('.showhideitem').hide();

  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pembebananpemakaiangetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      const data = res[0];

      if (data.IsOtorisasi1 == 1) {
        alertify.warning("Data sudah diotorisasi");
        return;
      }

      dataTableKoreksi = res;

      // Isi Tabel Item
      let rowTable = "";
      res.forEach((item, i) => {
        rowTable += `<tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-right">${parseFloat(item.Qnt).toLocaleString()}</td>
          <td class="text-center">${item.Satuan}</td>
          <td>${item.KodePerkiraan ?? ''}</td>
          <td>${item.namaPerkiraan ?? ''}</td>
          <td class="text-center">
            <button type="button" class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pencil-fill"></i></button>
          </td>
        </tr>`;
      });

      $("#koreksiTableData").html(rowTable);

      // Isi Form Header
      $("#input_koreksi_nobukti").val(data.NoBukti);

      const tanggal = data.TANGGAL;
      $("#input_koreksi_tanggal").val(tanggal);

      $('.mainpage').hide();
      $('#page3').show();
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

function buttonDetailKoreksi (nobukti) {
  console.log('buttonDetailKoreksi', nobukti);

  $('.showhideitem').hide();

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pembebananpemakaiangetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      // Isi Tabel Detail Item
      let rowTable = "";
      res.forEach((item, i) => {
        rowTable += `<tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-right">${parseFloat(item.Qnt).toLocaleString()}</td>
          <td class="text-center">${item.Satuan}</td>
          <td>${item.KodePerkiraan ?? ''}</td>
          <td>${item.namaPerkiraan ?? ''}</td>
        </tr>`;
      });

      $("#detailKoreksiTableData").html(rowTable);

      // Isi Header Detail
      const data = res[0];

      $("#input_detailkoreksi_nobukti").val(data.NoBukti);

      $('.mainpage').hide();
      $('#page4').show();
    },
    error: function (err) {
      console.error('Error response:', err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}


function loadAll () {
  $.ajax({
    url: "{!! url('pembebananpemakaianloadall') !!}",
    type: "get",
    async: false,
    success: function (res) {
      // Buang tooltip lama sebelum tombolnya diganti lewat innerHTML - tooltip
      // Bootstrap nempel elemen terpisah di <body>, jadi kalau tidak dibuang dulu bisa
      // nyangkut menutupi tombol baru. Lihat catatan sama di
      // gudang/pemakaianbarang.blade.php loadAll().
      $('#tabel_data, #tabel2_data').find('[data-toggle="tooltip"]').tooltip('dispose');

      // ===================== TAB 1 (Outstanding) =====================
      if ($.fn.DataTable.isDataTable('#tabel')) {
        $('#tabel').DataTable().clear().destroy();
      }
      let rowTable = '';

      res.tempOutstanding.forEach((group) => {
      let item = group[0];
        rowTable += `
          <tr>
            <td class="text-center">
              <div class="action-buttons">
                <button type="button" class="btn-action-sm btn-action-warning" data-toggle="tooltip" title="Detail" onclick="buttonDetailKoreksi('${item.NOBUKTI}')">
                  <i class="bi bi-info"></i>
                </button>
                <button type="button" class="btn-action-sm btn-action-primary" data-toggle="tooltip" title="Otorisasi" onclick="buttonOtorisasi('${item.NOBUKTI}')">
                  <i class="bi bi-key"></i>
                </button>
                <button type="button" class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" onclick="buttonKoreksi('${item.NOBUKTI}', 'edit')">
                  <i class="bi bi-pencil-fill"></i>
                </button>
              </div>
            </td>
            <td>${item.NOBUKTI}</td>
            <td>${item.TANGGAL ? formatDate(item.TANGGAL, '/') : ''}</td>
            <td>${item.Keterangan || ''}</td>
            <td>${item.Perkiraan || ''}</td>
          </tr>`;
      });

      document.getElementById("tabel_data").innerHTML = rowTable;
      $("#tabel").DataTable(dtOptionsOutstanding);
      updateFooter('tabel', 'footerLabel1');

      // ===================== TAB 2 =====================
      if ($.fn.DataTable.isDataTable('#tabel2')) {
        $('#tabel2').DataTable().clear().destroy();
      }
      let rowTable2 = '';

      res.tempPenerimaan.forEach((group) => {
      let item = group[0];
        rowTable2 += `
          <tr>
            <td class="text-center">
              <div class="action-buttons">
                <button type="button" class="btn-action-sm btn-action-warning" data-toggle="tooltip" title="Detail" onclick="buttonDetailKoreksi('${item.NOBUKTI}')">
                  <i class="bi bi-info"></i>
                </button>
                ${
                  item.IsOtorisasi1 == 1
                    ? `<button type="button" class="btn-action-sm btn-action-danger" data-toggle="tooltip" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NOBUKTI}', 'edit')">
                        <i class="bi bi-key-fill"></i>
                      </button>`
                    : `<button type="button" class="btn-action-sm btn-action-primary" data-toggle="tooltip" title="Otorisasi" onclick="buttonOtorisasi('${item.NOBUKTI}', 'add')">
                        <i class="bi bi-key"></i>
                      </button>`
                }
                <button type="button" class="btn-action-sm btn-action-info" data-toggle="tooltip" title="Print" onclick="submitPrint('${item.NOBUKTI}')">
                  <i class="bi bi-printer"></i>
                </button>
              </div>
            </td>
            <td>${item.NOBUKTI}</td>
            <td>${item.TANGGAL ? formatDate(item.TANGGAL, '/') : ''}</td>
            <td>${item.Keterangan || ''}</td>
            <td>${item.Perkiraan || ''}</td>
            <td>${item.OtoUser1 || ''}</td>
            <td>${item.TglOto1 ? formatDate(item.TglOto1, '/') : ''}</td>
          </tr>`;
      });

      document.getElementById("tabel2_data").innerHTML = rowTable2;
      $("#tabel2").DataTable(dtOptionsPenerimaan);
      updateFooter('tabel2', 'footerLabel2');

      // container:'body', boundary:'window' — lihat penjelasan panjang di
      // permintaanpemakaian.blade.php renderTabel() soal kenapa keduanya wajib di
      // dalam kotak scroll pendek (.table-wrap).
      $('[data-toggle="tooltip"]').tooltip({
        container: 'body',
        boundary: 'window'
      });
    }
  });
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('pembebananpemakaiandetailCetak') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log(res,'zzzzz')

        dataPrint = res
        console.log(res[0])
        console.log(res[0][0])

        // console.log(res[0][0].IsOtorisasi1)

      }
    })

    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=7) {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0];

    css = `<style type="text/css">
      body {
        font-family: sans-serif;
        font-size: 11px !important;
      }

      table {
        margin: 20px auto;
        border-collapse: collapse;
      }

      table th,
      table td {
        border: 1px solid #3c3c3c;
        height: 24px;
        padding: 1px 5px 0px;
        overflow: hidden;
      }

      a {
        background: blue;
        color: #fff;
        padding: 8px 10px;
        text-decoration: none;
        border-radius: 2px;
      }

      .ttd-place {
        height: 80px;
        text-align: center;
      }

      #ttd {
        width: 1000px;
        border: none;
      }

      .ttd-header {
        padding-top: 40px;
      }

      .body-main-print {
        padding: 1rem;
        padding-top: 1rem;

      }

      .header-ba {
        margin-bottom: 2rem;
        text-decoration: underline;
        margin-top: 2rem;
      }

      .detail-spb-table {
        margin: 0;
      }

      .no-border {
        border: none;
      }

      .detail-ba-div {
      }

      .vertical-align-baseline {
        vertical-align: baseline;
      }

      .mt-2rem {
        margin-top: 2rem;
      }

      .mb-3 {
        margin-bottom: 0.5rem;
      }

      .fw-bold {
        font-weight: bold;
      }

      .mb-1 {
        margin-bottom: 0.25rem;
      }

      .mb-2 {
        margin-bottom: 0.5rem;
      }

      .mb-3 {
        margin-bottom: 1rem;
      }

      .mb-4 {
        margin-bottom: 1.5rem;
      }

      .mb-5 {
        margin-bottom: 3rem;
      }

      .mt-1 {
        margin-top: 0.25rem;
      }

      .mt-2 {
        margin-top: 0.5rem;
      }

      .mt-3 {
        margin-top: 1rem;
      }

      .mt-4 {
        margin-top: 1.5rem;
      }

      .mt-5 {
        margin-top: 3rem;
      }

      .ms-1 {
        margin-left: 0.25rem;
      }

      .ms-2 {
        margin-left: 0.5rem;
      }

      .ms-3 {
        margin-left: 1rem;
      }

      .ms-4 {
        margin-left: 1.5rem;
      }

      .ms-5 {
        margin-left: 3rem;
      }

      .me-1 {
        margin-right: 0.25rem;
      }

      .me-2 {
        margin-right: 0.5rem;
      }

      .me-3 {
        margin-right: 1rem;
      }

      .me-4 {
        margin-right: 1.5rem;
      }

      .me-5 {
        margin-right: 3rem;
      }

      .my-1 {
        margin-top: 0.25rem;
        margin-bottom: 0.25rem;
      }

      .my-2 {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
      }

      .my-3 {
        margin-top: 1rem;
        margin-bottom: 1rem;
      }

      .my-4 {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .my-5 {
        margin-top: 3rem;
        margin-bottom: 3rem;
      }

      .pb-1 {
        padding-bottom: 0.25rem;
      }

      .pb-2 {
        padding-bottom: 0.5rem;
      }

      .pb-3 {
        padding-bottom: 1rem;
      }

      .pb-4 {
        padding-bottom: 1.5rem;
      }

      .pb-5 {
        padding-bottom: 3rem;
      }

      .pt-1 {
        padding-top: 0.25rem;
      }

      .pt-2 {
        padding-top: 0.5rem;
      }

      .pt-3 {
        padding-top: 1rem;
      }

      .pt-4 {
        padding-top: 1.5rem;
      }

      .pt-5 {
        padding-top: 3rem;
      }

      .ps-0 {
        padding-left: 0;
      }

      .ps-1 {
        padding-left: 0.25rem;
      }

      .ps-2 {
        padding-left: 0.5rem;
      }

      .ps-3 {
        padding-left: 1rem;
      }

      .ps-4 {
        padding-left: 1.5rem;
      }

      .ps-5 {
        padding-left: 3rem;
      }

      .pe-1 {
        padding-right: 0.25rem;
      }

      .pe-2 {
        padding-right: 0.5rem;
      }

      .pe-3 {
        padding-right: 1rem;
      }

      .pe-4 {
        padding-right: 1.5rem;
      }

      .pe-5 {
        padding-right: 3rem;
      }

      .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
      }

      .py-1-5 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-3 {
        padding-top: 1rem;
        padding-bottom: 1rem;
      }

      .py-4 {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
      }

      .py-5 {
        padding-top: 3rem;
        padding-bottom: 3rem;
      }

      .px-1 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
      }

      .px-1-5 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-2 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-3 {
        padding-left: 1rem;
        padding-right: 1rem;
      }

      .px-4 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
      }

      .px-5 {
        padding-left: 3rem;
        padding-right: 3rem;
      }

      .text-left {
        text-align: left;
      }

      .text-center {
        text-align: center;
      }

      .text-right {
        text-align: right;
      }

      .text-decoration-underline {
        text-decoration: underline;
      }

      ul {
        margin: 0;
        padding-left: 10px;
      }

      .note {
        width: 75%;
      }

      .w-15 {
        width: 16%;
      }

      .w-25 {
        width: 30%;
      }

      .w-10 {
        width: 4%;
      }

      .w-1 {
        width: 1%;
      }

      .m-0 {
        margin: 0;
      }

      .body-main-prints {
        width: 21cm;
        height: 13.5cm;
        position: relative;
      }

      .footer-sign {
        padding-top: 5px;
        position: absolute;
        width: 100%;
        bottom: 12px;
      }

      .footer-print-date {
        position: absolute;
        width: 100%;
        bottom: 5px;
      }

       .solid{
        border-left: 0px red solid;
        height: 225px;
        width: 0px;
        display: inline-block;
        padding-left: 0px;
        }

      </style>`;
    hdr = `<div class="" style="display: flex; width: 100%">
              <div class="pe-1" style="width: 50%">
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 15%; margin-top: 15px">
                    `+ imageContent +`
                  </div>
                  <div class="pb-1 ps-3" style="width: 85%; ">
                    <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                  </div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Departemen : </div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Untuk Keperluan : </div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">BUKTI PEMAKAIAN INTERNAL ACC</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].NoBukti+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>
              <div
                style="
                  width: 12%;
                  height: 80px;
                  overflow: hidden;
                "
                >
                `+printContent+`
              </div>
            </div>
      <table

                class="detail-spb-table"
                style="width: 100%; height: 225px; max-height: 225px;font-family: sans-serif;  display: table;
                font-size: 10px">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%">No.</td>
                    <td class="text-center" style="width: 50%">URAIAN BARANG</td>
                    <td class="text-center" style="width: 5%">SATUAN</td>
                    <td class="text-center" style="width: 5%">QTY</td>
                    <td class="text-center" style="width: 10%">HARGA SAT</td>
                    <td class="text-center" style="width: 10%">TOTAL</td>
                    <td class="text-center" style="width: 10%">COST</td>
                    <td class="text-center" style="width: 10%">COA</td>
                    <td class="text-center" style="width: 50%">COA</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotal = 0;
    arrayDataPrint.forEach(group => {
      group.forEach(item => {
        if (item.Total) {
          grandTotal += parseFloat(item.Total) || 0;
        }
      });
    });
    // end
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>

    <body onload="window.print()">
      ` + css

      arrayDataPrint.forEach((item, i) => {
        console.log('arrayDataPrint' , i)
        if (i == 0) {

          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
        // } else if ( i < 1) {
        //   tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:15px; page-break-before: always">`
        } else {
          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px;padding-top:7px; ">`
        }
        tempPrintStr += hdr
        tempPrintStr += `<tbody border="1">`;
        item.forEach((itemSub, j) => {
          tempPrintStr += ``
	console.log('oooo',itemSub);


         tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 2%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 50%;  ">${itemSub.NamaBrg}</td>
         <td class="text-align: text-center"
               style="width: 5%;">${itemSub.Sat}</td>
         <td class="text-align: text-right"
               style="width: 5%;  ">${itemSub.Qnt ? parseFloat(itemSub.Qnt).toFixed(2) : ''}</td>
         <td style="width: 10%; text-align: right;">
            ${itemSub.HPP
              ? Number(itemSub.HPP).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}
         </td>
         <td style="width: 10%; text-align: right;">
            ${itemSub.Total
              ? Number(itemSub.Total).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}
         </td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.KODESUBCOST ? parseFloat(itemSub.KODESUBCOST).toFixed(2) : ''}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.KodePerkiraan}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.NamaPerkiraan}</td>
         </tr>`;

           z++;

        });
        tempPrintStr +=`
          <tr style>

          </tr>`;

         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>

          <hr style="margin-top: -6px" />

         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>

         <span style="float: left; display: block; clear: left;">
          <h5>
          Total : ${grandTotal.toLocaleString('id-ID', {minimumFractionDigits: 2,maximumFractionDigits: 2
                })}
          </h5>
         </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%"></td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Dibuat Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
              <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
         </div>


         <div class="footer-print-date">
           <table class="m-0" style="width: 100% ; font-family: sans-serif;
           font-size: 10px ">
             <tr>
               <td class="no-border"></td>
               <td class="no-border text-right">Page ${i+1} of ${arrayDataPrint.length}</td>
             </tr>
           </table>
         </div>`


        tempPrintStr += `</div>`
      });


      tempPrintStr +=  `</body></html>`



    w=window.open(' ')
    w.document.write(tempPrintStr)

    w.print()
    w.close()

  }

function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();
  loadAll();
}

function formatDate(date , pemisah = '-') {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join(pemisah);
}


</script>

{{-- script buat hover belum otorisasi dan sudah otorisasi --}}
  <script>
    const tabHome = document.getElementById('nav-home-tab');
    const tabProfile = document.getElementById('nav-profile-tab');

    function setActiveTab(homeActive) {
      if (homeActive) {
        tabHome.style.backgroundColor = '#007bff';
        tabHome.style.color = '#fff';
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';
      } else {
        tabProfile.style.backgroundColor = '#007bff';
        tabProfile.style.color = '#fff';
        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';
      }
    }

    // Default warna tab
    setActiveTab(true);

    // buat ganti tab
    tabHome.addEventListener('click', function () {
      setActiveTab(true);
    });

    tabProfile.addEventListener('click', function () {
      setActiveTab(false);
    });
  </script>
{{-- script buat hover belum otorisasi dan sudah otorisasi --}}



@endsection
