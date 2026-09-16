@extends('gudang.newmasterx')
@section('buttons')

@endsection
@section('page-title')Pembebanan Pemakaian @endsection
  @section('css')
{{-- report-table.css / report-table.js dimuat dari gudang/newmasterx.blade.php (layout
     bersama), bukan per-halaman — lihat catatan di layout tersebut. --}}

{{-- Search box #tabel_add_list_perkiraan_filter / _costing_filter / _subcosting_filter
     dihapus - modal Perkiraan/Costing/Sub Costing sekarang pakai .rt-picker-v2, yang
     sudah menata kotak search DataTables-nya sendiri (lihat report-table.css). Style
     id-scoped lama di sini punya specificity lebih tinggi dari
     .rt-picker-v2 .dataTables_filter input, jadi kalau dibiarkan bakal menimpanya. --}}

<style>
    /* Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar — lihat catatan di
       gudang/permintaanpemakaian.blade.php soal kenapa ditulis lokal di sini, bukan di
       report-table.css. Warna/border memakai variabel --white/--border/--muted milik
       .tb-report di report-table.css supaya tetap seragam dengan kotak search & tombol
       Filter di sebelahnya. */
    .len-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 5px 12px;
    }

    .len-wrap label {
        margin: 0;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .05em;
        white-space: nowrap;
    }

    .len-inp {
        border: none;
        background: transparent;
        font-size: 13px;
        font-weight: 700;
        color: #1D2130;
        outline: none;
        cursor: pointer;
        padding: 2px 20px 2px 0;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
        background-repeat: no-repeat;
        background-position: right center;
    }

    /* Tombol Prev/Next dinonaktifkan (halaman pertama/terakhir) — .pg/.pg.active sudah
       ada di report-table.css, .disabled belum. */
    .tb-report .pg.disabled {
        opacity: .4;
        cursor: not-allowed;
        pointer-events: none;
    }
</style>
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
        <div class="filter-wrap">
          <label>Periode</label>
          <input type="date" class="filter-inp" id="inputDate1" value="{!! $date1 !!}"
              onchange="reloadData()">
          <span class="filter-sep">s/d</span>
          <input type="date" class="filter-inp" id="inputDate2" value="{!! $date2 !!}"
              onchange="reloadData()">
        </div>
        <div>
          <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..."
              oninput="renderTabel()" style="width:200px">
        </div>

        {{-- Jumlah baris per halaman. -1 = tampilkan semua data (tanpa pager) — lihat
             renderTabel()/onLenChange2() di bawah. --}}
        <div class="len-wrap">
          <label for="tabelLen2">Tampilkan</label>
          <select id="tabelLen2" class="len-inp" onchange="onLenChange2()">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>

        <button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
      </div>

      <!-- Bar kolom tersembunyi (diisi oleh report-table.js / ReportTable) -->
      <div id="rtBar"></div>

      <div class="table-outer">
        <div class="table-wrap">
          <table id="mainTable" class="tb aksi-hover">
            <thead>
              <tr>
                <th class="rt-fixed-th">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel2_data" class="text-left"></tbody>
          </table>
        </div>
        <div class="table-footer">
          <span id="footerLabel2">Belum ada data</span>
          <div class="pager-btns" id="pagerBtns2"></div>
        </div>
      </div>

      <div class="rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk
        sembunyikan kolom.
      </div>

</div>
</div>
</div>
</div>
</div>

{{-- modal filter — DILETAKKAN DI LUAR .tb-report supaya reset `.tb-report *{margin:0;padding:0}`
     di report-table.css tidak merusak padding/margin modal Bootstrap. --}}
<div class="modal fade rt-filter" id="modalFilter">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-filter"></i>
          Filter Laporan
          <span class="rt-active-badge" id="filterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Pengaturan Laporan</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="modalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="modalOtorisasi">
                <option value="2">Semua</option>
                <option value="1">Sudah Otorisasi</option>
                <option value="0">Belum Otorisasi</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="resetAllFilters()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary"
              onclick="applyModalFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- modal filter -->





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
      <h2></h2>
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
              <button type="button" onclick="openPicker('perkiraan')" class="btn btn-chip-biru"><i class="bi bi-search"></i></button>
              <input type="hidden" id="KoreksiEditNamaPerkiraan">
            </div>
          </div>
        </div>

        <div class="row align-items-center mt-2">
          <label class="col-4 col-form-label font-weight-bold">Costing</label>
          <div class="col">
            <div class="input-group">
              <input id="KoreksiEditCosting" type="text" class="form-control text-left" placeholder="Costing" onkeypress="onKeyPressPicker(event,'costing')">
              <button type="button" onclick="openPicker('costing')" class="btn btn-chip-biru"><i class="bi bi-search"></i></button>
              <input type="hidden" id="input_costing">
            </div>
          </div>
        </div>

        <div class="row align-items-center mt-2">
          <label class="col-4 col-form-label font-weight-bold">Sub Costing</label>
          <div class="col">
            <div class="input-group">
              <input id="KoreksiEditSubCosting" type="text" class="form-control text-left" placeholder="Sub Costing" onkeypress="onKeyPressPicker(event,'subcosting')">
              <button type="button" onclick="openPicker('subcosting')" class="btn btn-chip-biru"><i class="bi bi-search"></i></button>
              <input type="hidden" id="input_sub_costing">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-12 text-right" id="contentContainer">
        <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary" onclick="buttonKoreksiItemBatal()">Batal</button>

        <button id="buttonSubmitKoreksiEdit" type="button" onclick="submitKoreksiEdit()" class="btn btn-action-primary btn-primary btn-pill-primary" >Submit Edit</button>
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
      <h2></h2>
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

/* ============================================================================
 * Tabel interaktif gabungan (belum + sudah otorisasi) — port dari
 * gudang/permintaanpemakaian.blade.php (mesin gcart_header, lihat
 * docs/new-slider-table-guide.md). doShowCustomize()/doButtonSubtotal()/
 * doButtonGrandtotal() sengaja tidak diikutkan — itu untuk modal "Atur Kolom"
 * yang tidak ada di halaman ini; report-table.js memanggil onChange sendiri.
 * ========================================================================= */
let lastRows = @json($penerimaanArray); // paint pertama tanpa AJAX; reloadData() menyegarkan setelahnya
let globalOtorisasi = "2"; // filter modal: 2=Semua, 1=Sudah Otorisasi, 0=Belum Otorisasi

// Dropdown "Tampilkan" (#tabelLen2) — jumlah baris per halaman. -1 = semua data,
// paging murni client-side (renderTabel() sudah memegang seluruh lastRows).
let tabelLen2 = 10;
let tabelPage2 = 1;

var g_href = 'pembebananpemakaian';
var g_modeReport = '1';
var gcart_header = [];
var gsum_issubtotal = 0;
var gsum_isgrandtotal = 0;
var gct_desimal_max = 4;

function setDefaultHeader() {
  // [ field, label, visible, type, total, decimals ]
  gcart_header = [
    ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
    ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0],
    ['Perkiraan', 'Perkiraan', 1, 'varchar', 0, 0],
    // 'varchar', bukan 'float' — nilainya dirender jadi badge Sudah/Belum, jangan
    // diperlakukan sebagai kolom angka (rata kanan).
    ['IsOtorisasi1', 'Otorisasi', 1, 'varchar', 0, 0],
    ['OtoUser1', 'User Oto', 1, 'varchar', 0, 0],
    ['TglOto1', 'Tanggal Oto', 1, 'date', 0, 0],
    ['Kodegdg', 'Kode Gudang', 0, 'varchar', 0, 0],
    ['Namagdg', 'Nama Gudang', 0, 'varchar', 0, 0]
  ];
}

function doSetHeader(_modereport, _isReset = false) {
  let _strHeader = (!_isReset) ? doLoadHeader(g_href, _modereport) : "";

  if (_strHeader != "") {
    gcart_header = doGetHeader(_strHeader);
  } else if ($.isFunction(window.setDefaultHeader)) {
    setDefaultHeader();
    doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
  }
}

function doLoadHeader(_href, _mode) {
  let _header = "";

  $.ajax({
    url: "{!! url('globalfunctions_doLoadHeader') !!}",
    type: "get",
    async: false,
    data: {
      href: _href,
      mode: _mode
    },
    success: function(res) {
      _header = (res.length > 0) ? res[0].header : "";
      if (res.length > 0) {
        gsum_issubtotal = Number(res[0].issubtotal);
        gsum_isgrandtotal = Number(res[0].isgrandtotal);
      }
    }
  })

  return _header;
}

function doGetHeader(_strHeader) {
  let _cart = [];

  _strHeader.split("||").forEach((item, i) => {
    let temp = [];
    temp.push(item.split(";;")[0]);
    temp.push(item.split(";;")[1]);
    temp.push(Number(item.split(";;")[2]));
    temp.push(item.split(";;")[3]);
    temp.push(Number(item.split(";;")[4]));
    temp.push(Number(item.split(";;")[5]));
    _cart.push(temp);
  });

  return _cart;
}

function doSimpanHeader(_href, _mode, _cart, _issubtotal, _isgrandtotal) {
  let _strHeader = "";

  _cart.forEach((item, i) => {
    if (i != 0) {
      _strHeader += '||';
    }
    _strHeader += item[0] + ';;' + item[1] + ';;' + item[2] + ';;' + item[3] + ';;' + item[4] + ';;' +
      item[5];
  });

  $.ajax({
    url: "{!! url('globalfunctions_doSimpanHeader') !!}",
    type: "get",
    async: false,
    data: {
      href: _href,
      mode: _mode,
      header: _strHeader,
      issubtotal: _issubtotal,
      isgrandtotal: _isgrandtotal
    },
    success: function(res) {
      // nothing to do
    }
  })
}

function doMoveHeader(_from, _to) {
  if (_from < 0 || _to < 0 || _from === _to) {
    return;
  }
  if (_from >= gcart_header.length || _to >= gcart_header.length) {
    return;
  }

  let _moved = gcart_header.splice(_from, 1)[0];
  gcart_header.splice(_to, 0, _moved);

  doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
}

function doButtonVisibility(_id) {
  gcart_header[_id][2] = (Number(gcart_header[_id][2]) === 1) ? 0 : 1;

  doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
}

function doSetDesimal(_index, _step) {
  let _next = Number(gcart_header[_index][5]) + _step;
  if (_next < 0 || _next > gct_desimal_max) {
    return;
  }

  gcart_header[_index][5] = _next;
  doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
}

function doButtonTotal(_index) {
  gcart_header[_index][4] = (Number(gcart_header[_index][4]) === 1) ? 0 : 1;

  doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
}

// Ambil field dari row tanpa peduli besar/kecil huruf — hasil grouping controller
// mencampur UPPERCASE (NOBUKTI/TANGGAL) dengan PascalCase (OtoUser1/TglOto1).
function pickCI(r, key) {
  if (r[key] !== undefined) {
    return r[key];
  }
  let lk = String(key).toLowerCase();
  for (let k in r) {
    if (k.toLowerCase() === lk) {
      return r[k];
    }
  }
  return null;
}

function nullToEmpty(v) {
  return (v === null || v === undefined) ? '' : v;
}

function fmtYMD(v) {
  if (!v) {
    return '';
  }
  let date = new Date(v);
  if (isNaN(date)) {
    return '';
  }
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  return date.getFullYear() + "/" + month + "/" + day;
}

// #modalOtorisasi: 2=Semua, 1=Sudah, 0=Belum — client-side saja,
// server selalu mengembalikan seluruh rentang tanggal yang dipilih.
function filterByOtorisasi(rows, filterVal) {
  if (filterVal === '1') {
    return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 1);
  }
  if (filterVal === '0') {
    return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 0);
  }
  return rows;
}

// Warna, ikon dan urutan tombol sengaja disamakan dengan kolom Actions di
// purchasing/purchaseOrder.blade.php dan permintaanpemakaian.blade.php supaya
// konsisten antar halaman: Detail=amber bi-info, Otorisasi=biru bi-key,
// Edit=hijau bi-pencil-fill, Batal Otorisasi=merah bi-key-fill, Print=cyan bi-printer.
function aksiButtonsHtml(r) {
  const nobukti = r.NOBUKTI;
  const detailBtn =
    '<button type="button" class="btn-action-sm btn-action-warning" data-toggle="tooltip" title="Detail" onclick="buttonDetailKoreksi(\'' +
    nobukti + '\')"><i class="bi bi-info"></i></button>';

  if (Number(pickCI(r, 'IsOtorisasi1')) === 1) {
    // Sudah otorisasi — Batal Otorisasi + Print
    return '<div class="action-buttons">' + detailBtn +
      '<button type="button" class="btn-action-sm btn-action-danger" data-toggle="tooltip" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' +
      nobukti + '\', \'' + r.IsOtorisasi1 + '\')"><i class="bi bi-key-fill"></i></button>' +
      '<button type="button" class="btn-action-sm btn-action-info" data-toggle="tooltip" title="Print" onclick="submitPrint(\'' +
      nobukti + '\')"><i class="bi bi-printer"></i></button>' +
      '</div>';
  }

  // Belum otorisasi — Otorisasi + Edit
  return '<div class="action-buttons">' + detailBtn +
    '<button type="button" class="btn-action-sm btn-action-primary" data-toggle="tooltip" title="Otorisasi" onclick="buttonOtorisasi(\'' +
    nobukti + '\', \'' + r.IsOtorisasi1 + '\')"><i class="bi bi-key"></i></button>' +
    '<button type="button" class="btn-action-sm btn-action-success" data-toggle="tooltip" title="Edit" onclick="buttonKoreksi(\'' +
    nobukti + '\')"><i class="bi bi-pencil-fill"></i></button>' +
    '</div>';
}

// resetPage tidak dikirim (undefined) di semua pemanggilan lama (search/filter/
// reloadData/onChange report-table.js) sehingga tetap kembali ke halaman 1 — sikap
// aman kalau data/filter berubah. Hanya gotoPage2() yang mengirim resetPage=false,
// karena di situ tabelPage2 sudah sengaja diarahkan ke halaman tujuan.
function renderTabel(resetPage) {
  if (resetPage !== false) {
    tabelPage2 = 1;
  }

  const cols = gcart_header.filter(c => c[2] === 1);
  const thead = document.querySelector('#mainTable thead');
  thead.innerHTML = ReportTable.headHtml(cols).replace('<tr>', '<tr><th class="rt-fixed-th">Actions</th>');

  const search = ($('#searchBox2').val() || '').trim().toLowerCase();
  let rows = lastRows;
  if (search) {
    rows = rows.filter(function(r) {
      return cols.some(function(c) {
        const v = pickCI(r, c[0]);
        return v != null && String(v).toLowerCase().indexOf(search) !== -1;
      });
    });
  }
  rows = filterByOtorisasi(rows, globalOtorisasi);

  const tbody = document.getElementById('tabel2_data');

  // Buang instance tooltip lama sebelum tombolnya dihapus lewat innerHTML —
  // tooltip Bootstrap 4 nempel elemen terpisah di <body>, jadi kalau tombol
  // pemicunya diganti tanpa dispose dulu, tooltip lama nyangkut selamanya
  // dan bisa menutupi tombol baru (klik jadi tidak berfungsi).
  $(tbody).find('[data-toggle="tooltip"]').tooltip('dispose');

  if (!rows.length) {
    tbody.innerHTML = '<tr class="empty-row"><td colspan="' + (cols.length + 1) + '">Tidak ada data</td></tr>';
    document.getElementById('footerLabel2').textContent = 'Tidak ada data';
    renderPager2(0, 0);
    return;
  }

  const totalRows = rows.length;
  const totalPages = tabelLen2 === -1 ? 1 : Math.max(1, Math.ceil(totalRows / tabelLen2));
  if (tabelPage2 > totalPages) {
    tabelPage2 = totalPages;
  }
  const pageRows = tabelLen2 === -1 ? rows : rows.slice((tabelPage2 - 1) * tabelLen2, tabelPage2 * tabelLen2);

  let html = '';
  pageRows.forEach(function(r) {
    html += '<tr class="data-row">';
    html += '<td class="text-center">' + aksiButtonsHtml(r) + '</td>';
    html += cols.map(function(c) {
      const v = pickCI(r, c[0]);
      if (c[0] === 'IsOtorisasi1') {
        return (Number(v) === 1) ?
          '<td><span class="sp-badge is-active">Sudah</span></td>' :
          '<td><span class="sp-badge is-inactive">Belum</span></td>';
      }
      if (c[3] === 'date') {
        return '<td>' + fmtYMD(v) + '</td>';
      }
      return '<td>' + nullToEmpty(v) + '</td>';
    }).join('');
    html += '</tr>';
  });

  tbody.innerHTML = html;
  document.getElementById('footerLabel2').textContent = tabelLen2 === -1 ?
    'Menampilkan ' + totalRows + ' baris' :
    'Menampilkan ' + pageRows.length + ' dari ' + totalRows + ' baris';
  renderPager2(tabelPage2, totalPages);
  // container:'body', boundary:'window' — lihat penjelasan panjang di
  // permintaanpemakaian.blade.php renderTabel() soal kenapa keduanya wajib di dalam
  // kotak scroll pendek (.table-wrap).
  $('[data-toggle="tooltip"]').tooltip({
    container: 'body',
    boundary: 'window'
  });
}

// Dropdown "Tampilkan" — ganti jumlah baris/halaman lalu balik ke halaman 1
// (nomor halaman lama tidak lagi berarti setelah panjang halaman berubah).
function onLenChange2() {
  const v = Number(document.getElementById('tabelLen2').value);
  tabelLen2 = (v === -1 || v > 0) ? v : 10;
  renderTabel();
}

// Dipanggil tombol Prev/Next/nomor halaman di #pagerBtns2. resetPage=false supaya
// renderTabel() tidak langsung membalikkan tabelPage2 ke 1.
function gotoPage2(p) {
  tabelPage2 = p;
  renderTabel(false);
}

// Gambar ulang tombol pager di footer tabel. totalPages<=1 (atau tabelLen2=-1,
// "Semua") menyembunyikan pager sepenuhnya — tidak ada gunanya menavigasi satu halaman.
function renderPager2(page, totalPages) {
  const el = document.getElementById('pagerBtns2');
  if (!el) {
    return;
  }
  if (!totalPages || totalPages <= 1) {
    el.innerHTML = '';
    return;
  }

  function pgBtn(label, targetPage, active, disabled) {
    const cls = 'pg' + (active ? ' active' : '') + (disabled ? ' disabled' : '');
    const click = disabled ? '' : ' onclick="gotoPage2(' + targetPage + ')"';
    return '<div class="' + cls + '"' + click + '>' + label + '</div>';
  }

  // Jendela nomor halaman: maksimal 5 tombol angka di sekitar halaman aktif,
  // supaya pager tidak melebar tak terbatas kalau datanya banyak.
  let start = Math.max(1, page - 2);
  let end = Math.min(totalPages, start + 4);
  start = Math.max(1, end - 4);

  let html = pgBtn('&laquo;', page - 1, false, page <= 1);
  for (let p = start; p <= end; p++) {
    html += pgBtn(String(p), p, p === page, false);
  }
  html += pgBtn('&raquo;', page + 1, false, page >= totalPages);

  el.innerHTML = html;
}

/* -- FILTER MODAL (Otorisasi: Semua/Sudah Otorisasi/Belum) -- */
function updateFilterBadge() {
  let count = ($('#modalOtorisasi').val() !== '2') ? 1 : 0;
  $('#filterBadge').text(count + ' aktif');
}

function resetAllFilters() {
  $('#modalOtorisasi').val('2');
  updateFilterBadge();
}

$(document).on('show.bs.modal', '#modalFilter', function() {
  $('#modalOtorisasi').val(globalOtorisasi);
  updateFilterBadge();
});

$(document).on('change', '#modalFilter select.rt-native', updateFilterBadge);

function applyModalFilter() {
  globalOtorisasi = $('#modalOtorisasi').val();
  renderTabel();
  $('#modalFilter').modal('hide');
}

// Menggantikan loadAll() lama — satu list gabungan, difilter di server berdasarkan
// rentang tanggal yang sedang dipilih. Dipanggil saat tanggal berubah dan setelah
// otorisasi/batal otorisasi/koreksi supaya tabel menyegarkan diri sendiri.
function reloadData() {
  $.ajax({
    url: "{!! url('pembebananpemakaianloadall') !!}",
    type: "get",
    async: false,
    data: {
      date1: $('#inputDate1').val(),
      date2: $('#inputDate2').val()
    },
    success: function(res) {
      lastRows = res.penerimaan;
      renderTabel();
    }
  });
}

$(document).ready(function(){
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

  // Tabel gabungan — mesin interaktif (drag/gear/bar), lihat
  // docs/new-slider-table-guide.md. doSetHeader() memuat layout tersimpan milik
  // user ini untuk halaman+mode ini, atau menyimpan setDefaultHeader() kalau ini
  // kunjungan pertama.
  doSetHeader(g_modeReport);
  ReportTable.init({
    table: '#mainTable',
    bar: '#rtBar',
    onChange: renderTabel
  });
  renderTabel();
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
        reloadData();
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

  // alertify.prompt(JUDUL, PESAN, NILAI_AWAL, onOK, onCancel) — argumen pertama jadi
  // judul header dialog; classList di bawah menyamakan gaya tombol OK/Cancel dengan
  // dialog Batal Otorisasi di permintaanpemakaian.blade.php ('is-danger' karena aksi ini
  // merusak/membalik otorisasi).
  var dlgBatalOtorisasi = alertify.prompt("Batal Otorisasi", "Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
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
          reloadData();
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
  dlgBatalOtorisasi.elements.root.classList.add('ajs-app-buttons', 'is-danger');
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
        reloadData();
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
  reloadData();
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

@endsection
