@extends('newmaster')
@section('buttons')

@endsection

@section('css')

{{-- report-table.css/report-table.js + public/js/headerEngine.js power #tabel/#tabel2's
     draggable-column/gear-menu headers and "Tampilkan" (page-length) control, copied 1:1
     from Sales Order's page1 (see so.blade.php's #tabel/#tabel7). Linked here page-locally
     so only this page gets it. --}}
<link rel="stylesheet" href="{{ asset('css/report-table.css') }}?v={{ filemtime(public_path('css/report-table.css')) }}">

<style>
  /* Holds the pagination element JS relocates here (see moveDataTablePagination()) so it
     lives outside .table-wrap's horizontal scroll -- .dataTables_paginate still floats
     right per DataTables' own CSS, but now within this container's width (the visible
     viewport, not the wide table), so it lands at a reachable spot instead of the
     table's far edge. overflow:hidden clears the float so this container doesn't
     collapse to zero height. Matches so.blade.php's .tb-pagination-outside 1:1. */
  .tb-pagination-outside {
    overflow: hidden;
    margin-top: 8px;
    padding: 0 14px 10px;
  }
  .tb-pagination-outside .dataTables_paginate {
    float: right;
  }
  .tb-pagination-outside .paginate_button {
    box-sizing: border-box;
    display: inline-block;
    padding: 0.4em 0.9em;
    margin-left: 4px;
    border-radius: 6px;
    border: 1px solid var(--sp-border, #e7e9ee);
    color: var(--sp-text, #1f2430);
    text-decoration: none !important;
    cursor: pointer;
  }
  .tb-pagination-outside .paginate_button.current {
    background: var(--sp-primary, #6f42f3);
    border-color: var(--sp-primary, #6f42f3);
    color: #fff;
  }
  .tb-pagination-outside .paginate_button.disabled {
    cursor: default;
    color: var(--sp-text-soft, #6b7280);
    opacity: .6;
  }
  .tb-pagination-outside .paginate_button:hover:not(.disabled):not(.current) {
    background: var(--sp-bg, #f4f5f7);
  }

  /* report-table.css's .filter-wrap/.toolbar classes (the "Tampilkan" control) are
     designed to sit directly above a .tb-report table -- give them the same small
     bottom margin so.blade.php's toolbar has. */
  .tb-report .toolbar {
    margin-bottom: 10px;
  }

  /* "Reset kolom" pill + the row that holds it alongside the hidden-columns bar. Not
     part of report-table.css itself (so.blade.php declares these page-locally too) --
     kept OUTSIDE #rtBarTabelX as a flex sibling, not a child, because report-table.js's
     renderBar() fully overwrites those divs' innerHTML on every drag/hide/decimal
     change; a button placed inside them would vanish on the first re-render. */
  .rt-bar-row {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    margin-bottom: 10px;
  }
  .rt-bar-row .rt-bar { margin-bottom: 0; }
  .rt-reset-btn {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 8px 12px;
    border: 1px solid var(--rt-border);
    border-radius: var(--rt-radius);
    background: var(--rt-card);
    font-size: 13px;
    font-weight: 600;
    font-family: inherit;
    line-height: 1.2;
    cursor: pointer;
    white-space: nowrap;
    color: var(--rt-ink-soft);
  }
  .rt-reset-btn:hover {
    color: #D64550;
    border-color: #D64550;
    background: #FEF2F2;
  }
</style>

<style>
#tabel_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }

#tabel_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }
</style>
{{-- end tampilan search bar 1 --}}

{{-- tampilan search bar 2 --}}
<style>
#tabel2_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }

#tabel2_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel2_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }

#tabel2_filter input:focus {
    border-color: #007bff;
    outline: none;
  }
</style>
{{-- end tampilan search bar 2 --}}

{{-- tampilan search bar modal add pelanggan --}}
<style>

  #tabel_add_list_nosj_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_nosj_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>

<style>

  #tabel_add_list_custsupp_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_custsupp_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>
{{-- end tampilan search bar modal add pelanggan --}}
<style>
  #tabel_add_list_pelanggan_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_pelanggan_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>
{{-- tampilan search sales --}}
<style>
  #tabel_add_list_sales_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_sales_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>
{{-- end tampilan search sales --}}

{{-- tampilan search modal barang all --}}
<style>
  #input_search_barang_all {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
    display: flex;
    align-items: flex-end;
    margin-left: 95px;
  }
  .search-label {
  font-weight: bold;
  font-size: 0.75rem;
  margin-right: 155px;
  margin-top : -45px;
  display: inline-block;
  vertical-align: middle;
  }
</style>
{{-- end tampilan search modal barang all --}}

{{-- Header restyle to match Sales Order's page1 (see SalesOrder.blade.php):
     tableMaster2.css is what defines .btn-action-primary, used below on the
     + RSPB button in place of the old inline-styled pill button. --}}
<link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}">

{{-- radioChoiceMaster pill-tab look, copied 1:1 from Sales Order's page1 inline
     <style> block. Sales Order applies this to <button data-bs-toggle="tab">
     elements; this page's tabs still run on the Bootstrap 4 tab plugin
     (data-toggle="tab" + <a href="#...">), so the only additions versus Sales
     Order's copy are `display: inline-flex; align-items: center;` and
     `text-decoration: none;` on .radioChoiceMaster-btn -- an <a> needs both
     (box behavior and underline removal) that a <button> gets for free.
     Everything else, including colors and the active state, is unchanged
     from Sales Order's version. --}}
<style>
  .radioChoiceMaster {
    display: inline-flex;
    list-style: none;
    margin: 0;
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 999px;
    padding: 4px;
    gap: 4px;
  }

  .radioChoiceMaster-item {
    display: flex;
  }

  .radioChoiceMaster-btn {
    display: inline-flex;
    align-items: center;
    border: none;
    border-radius: 999px;
    padding: 8px 18px;
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    background-color: transparent;
    transition: all 0.2s ease;
    white-space: nowrap;
    outline: none;
    box-shadow: none;
    cursor: pointer;
    text-decoration: none;
  }

  .radioChoiceMaster-btn:hover {
    color: #212529;
    background-color: rgba(0,0,0,0.04);
    text-decoration: none;
  }

  .radioChoiceMaster-btn:focus,
  .radioChoiceMaster-btn:focus-visible {
    outline: none;
    box-shadow: none;
  }

  .radioChoiceMaster-btn.active {
    color: #fff;
    background-color: #007bff;
    box-shadow: 0 2px 6px rgba(0,123,255,0.35);
  }
</style>
@endsection


@section('content')
<div id="page1" class="container-fluid">


<div class="container-fluid">

  <!-- <div id="qrcode"></div> -->
  <div class="row align-items-center">
    <div class="col-6 text-left">
      <h2>Retur SJ</h2>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-action-primary" onclick="buttonAdd()">
        + RSPB
      </button>
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">


</div>
<div id="contentContainer" class="container-fluid" >
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  {{-- Deliberately NOT wrapped in .card -- newmaster.css's global .card rule
       (display:flex; align-items:center;) is meant for the dashboard module-home
       tile grid, and squeezes any .card-body down to its content's intrinsic width
       instead of stretching it, which is what made this table render far narrower
       than Sales Order's. Sales Order's own working pattern (giroTab) confirms this:
       its pill tab bar and table content both sit directly in the page flow with no
       .card wrapper at all -- matched 1:1 here. --}}
  <div style="margin-bottom:12px;">
<ul class="radioChoiceMaster" id="nav-tab" role="tablist">
  <li class="radioChoiceMaster-item" role="presentation">
    <a class="radioChoiceMaster-btn active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">
      Retur SJ Belum Otorisasi
    </a>
  </li>
  <li class="radioChoiceMaster-item" role="presentation">
    <a class="radioChoiceMaster-btn" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">
      Retur SJ Sudah Otorisasi
    </a>
  </li>
</ul>
</div>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="toolbar" style="margin-bottom:10px;">
      <div class="filter-wrap">
        <label for="tabel_length_visual">Tampilkan</label>
        <select id="tabel_length_visual" class="filter-inp" style="cursor:pointer;">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="-1">Semua</option>
        </select>
      </div>
    </div>
    {{-- .tb-report scopes report-table.css's gear-menu/drag-column styling (and the
         "Tampilkan" toolbar above) to just this table. Sits directly under .tab-pane
         with no .row/.col-12 wrapper -- matching so.blade.php's page1 exactly, since
         that wrapper's grid padding/negative-margin is what was shrinking this table
         down from full width. --}}
    <div class="tb-report main">
    <div class="rt-bar-row">
      <button class="rt-reset-btn" type="button" title="Reset kolom" onclick="buttonHeaderTable('tabel')">
        <i class="bi bi-arrow-clockwise"></i> Reset kolom
      </button>
      <div id="rtBarTabel"></div>
    </div>
    <div class="table-outer">
      <div class="table-wrap">
        <table id="tabel" class="tb">
          {{-- Header content is fully JS-owned: replaceTheadWithHeader() (called from
               renderTabelRows(), which runs on page load via reinitTabel()) replaces
               this <thead>'s contents based on gcart_header before the user ever sees
               it -- the tag itself is just a placeholder for that selector. --}}
          <thead style="white-space:nowrap;"></thead>
          <tbody id="tabel_data" class="text-left"></tbody>
        </table>
      </div>
      <div id="tabelPaginationOutside" class="tb-pagination-outside"></div>
      <div class="rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
        untuk menyembunyikan kolom atau mengatur jumlah desimal.
      </div>
    </div>
    </div>
  </div>

  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    <div class="toolbar" style="margin-bottom:10px;">
      <div class="filter-wrap">
        <label for="tabel2_length_visual">Tampilkan</label>
        <select id="tabel2_length_visual" class="filter-inp" style="cursor:pointer;">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="-1">Semua</option>
        </select>
      </div>
    </div>
    <div class="tb-report main">
    <div class="rt-bar-row">
      <button class="rt-reset-btn" type="button" title="Reset kolom" onclick="buttonHeaderTable('tabel2')">
        <i class="bi bi-arrow-clockwise"></i> Reset kolom
      </button>
      <div id="rtBarTabel2"></div>
    </div>
    <div class="table-outer">
      <div class="table-wrap">
        <table id="tabel2" class="tb">
          <thead style="white-space:nowrap;"></thead>
          <tbody id="tabel2_data" class="text-left"></tbody>
        </table>
      </div>
      <div id="tabel2PaginationOutside" class="tb-pagination-outside"></div>
      <div class="rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
        untuk menyembunyikan kolom atau mengatur jumlah desimal.
      </div>
    </div>
    </div>
    </div>
  </div>




</div>
</div>
</div>


</div>

</div>

<div id="page2" class="container-fluid" style="display: none">

  <div class="container-fluid">

    <!-- <div id="qrcode"></div> -->
    <div class="row" style="margin-top: -50px">
      <div class="col-6 text-left">
        <h1>Form Retur SJ</h1>
      </div>
      <div class="col-6 text-right">
        <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="buttonCloseForm()">
          Close
        </button>
      </div>
    </div>
  <!-- <button onclick="loadAll()">tes</button> -->
  </div>
  <div id="modalBodyAddMain" class="">
  <div class="modal-body">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
      <div class="row">

        <div class="col-3">
          <div class="row">


          <div class="col-4">
            <div class="form-group">
              <label>No SJ</label>
            </div>
          </div>
          <!-- <div class="col-3 text-right">
            <div class="form-group">
          </div> -->
          <div class="col-8">
            <div class="form-group input-group">
              <input type="text" class="form-control" id="input_add_nosj" placeholder="" disabled>
              <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoSJ" onclick="buttonAddListNoSJ()"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_nobukti" placeholder="No SO" disabled>
                </div>
              </div>
            </div>
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >
                </div>
              </div>
            </div>
          </div>


        </div>






      </div>

      <hr/>

      <div class="container-fluid">


      <div class="row">
        <div class="col-3">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <div class="col-8">
                <div class="form-group input-group">
                  <input type="text" class="form-control" id="input_add_kodecustomer" placeholder="" disabled>
                  <button class="btn btn-primary btn-sm text-right" id="buttonAddListCustSupp" onclick="buttonAddListCustSupp()"><i class="bi bi-plus"></i></button>
                </div>
            </div>
            <!-- <div class="col-2">
              <div class="form-group">


              </div>

            </div> -->
          </div>
            <div class="row" style="margin-top: -10px">

            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_customer"  disabled></textarea>
              </div>
            </div>
          </div>

        </div>
        <div class="col-3">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <!-- <input type="hidden" class="form-control" id="input_add_kodegdg" placeholder="" > -->
                <input type="text" class="form-control" id="input_add_noso" placeholder="" disabled>
              </div>
            </div>
          </div>
            <div class="row" style="margin-top: -10px">


            <div class="col-4">
              <div class="form-group">

                <label>Catatan</label>
              </div>

            </div>
            <div class="col-8">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none; " rows=4  class="form-control" id="input_add_catatan"  ></textarea>
              </div>
            </div>

          </div>
        </div>

        <div class="col-3">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Tgl SJ</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <!-- <input type="hidden" class="form-control" id="input_add_kodegdg" placeholder="" > -->
              <input type="date" class="form-control" id="input_add_tanggalsj" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">



            <!-- <div class="row"> -->
              <div class="col-4">
                <div class="form-group">
                  <label>Gudang</label>
                </div>
              </div>
              <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_gudang" placeholder="" disabled>
                  </div>
              </div>

            <!-- </div> -->

          </div>
        </div>

        <div class="col-3">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Tgl SC</label>
              </div>
            </div>

            <div class="col-8">
              <div class="form-group">
                <!-- <input type="hidden" class="form-control" id="input_add_kodegdg" placeholder="" > -->

              <input type="date" class="form-control" id="input_add_tanggalsc" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">


            <div class="col-4">
              <div class="form-group">
                <label>No Pol Kend</label>
              </div>
            </div>
            <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_nopol" placeholder="" >
                </div>
            </div>

          </div>
        </div>


      </div>

      <div class="row">
        <div class="col-3">

        </div>

        <div class="col-8">
          <div class="row">
            <div class="col-6">

            </div>

            <div class="col-6">
              <div class="row">


              </div>
            </div>

            <div class="col-12">
              <div class="row">


              </div>

            </div>



          </div>

        </div>

      </div>

      </div>



    </div>
    <div class="container-fluid">
      <hr/>

    </div>

  <div class="container-fluid mt-4" style="overflow-x: auto;">

        <table id="addTable" class="table table-bordered table-hover table-striped table-responsive-lg"  >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
              <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
              <th style="padding: 4px 12px;" scope="col">Qty1</th>
              <th style="padding: 4px 12px;" scope="col">Sat1</th>
              <th style="padding: 4px 12px;" scope="col">Qty2</th>
              <th style="padding: 4px 12px;" scope="col">Sat2</th>
              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="text-right" >
            <tr >

                <td colspan=7 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>



  <div class="col-12">

  <div class="row">
    <div class="col-12  text-right">

      <button type="button" class="btn btn-primary btn-lg" style="
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddAdd()" ><b>+ Tambah Item</b></button>
      </div>
    </div>

  </div>
</div>
<div id="formAddAdd" class="container-fluid showhide mb-2">
  <!-- <div class="line"></div> -->
  <hr/>
  <div class="row">
    <div class="col-12">
      <h4>Add Item</h4>
    </div>
  </div>
  <div class="row">

    <div class="col-3">



  <div class="row">
    <div class="col-4">
      <div class="form-group">
      <label>Kode Barang</label>
    </div>
    </div>
    <!-- <div class="col-3 text-right">

      </div> -->
    <div class="col-8">
      <div class="form-group input-group">

        <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
        <button type="button" id="buttonAddListBarang" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button>
      </div>
    </div>

  </div>

</div>

<div class="col-6">
  <div class="row">
    <div class="col-2">
      <div class="form-group">
      <label>Nama Barang</label>
    </div>
    </div>
    <div class="col-6">
      <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
    </div>
  </div>
</div>

<div class="col-3">

</div>

</div>
<div class="row" style="margin-top: -10px">

<div class="col-3 ">
  <div class="row">
    <div class="col-4">
      <div class="form-group">
      <label>Retur Supp</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">

        <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
      </div> -->
    <div class="col-8">
      <select  id="AddAddReturSupp" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
        <option value=0 selected>Tidak</option>
        <option value=1 >Ya</option>
      </select>
      <!-- <input id="AddAddNamaGdg" type="text" class="form-control" disabled>
      <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
    </div>

  </div>
</div>
<div class="col-6 ">
  <div class="row">
    <div class="col-2">
      <div class="form-group">
      <label>Qty</label>
    </div>
    </div>
    <div class="col-4">
      <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right">
    </div>
    <div class="col-2">
      <input id="AddAddInputSatuan" type="text"  class="form-control  " disabled>
      <input id="AddAddInputIsi" type="hidden"  class="form-control  " disabled>
      <input id="AddAddInputSatuan1" type="hidden"  class="form-control  " disabled>
    </div>


  </div>
</div>
</div>



  <div class="row mt-2">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-secondary btn-lg" style="
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonBatalAdd()" class="btn btn-secondary">Batal</button>

      <button type="button" id="buttonSubmitAddAdd" class="btn btn-primary btn-lg" style="
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="submitAddAdd()" class="btn btn-secondary">Submit Add</button>
</div>

  </div>
  <!-- <div class="line"></div> -->
  <!-- <hr/> -->
</div>

<div id="formAddEdit" class="container-fluid showhide mb-2">
  <!-- <div class="line"></div> -->
  <hr/>
  <div class="row">
    <div class="col-12">
      <h4>Edit Item</h4>
    </div>
  </div>
  <div class="row">

    <div class="col-3">



  <div class="row">
    <div class="col-4">
      <div class="form-group">
      <label>Kode Barang</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">

    </div> -->
    <div class="col-8">
      <input id="AddEditKodeBrg" type="text" class="form-control" disabled>
    </div>

  </div>

</div>

<div class="col-6">
  <div class="row">
    <div class="col-2">
      <div class="form-group">
      <label>Nama Barang</label>
    </div>
    </div>
    <div class="col-6">
      <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
    </div>
  </div>
</div>

<div class="col-3">

</div>

</div>

<div class="row" style="margin-top: -10px">

<div class="col-3 ">
  <div class="row">
    <div class="col-4">
      <div class="form-group">
      <label>Retur Supp</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">

        <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
      </div> -->
    <div class="col-8">
      <select  id="AddEditReturSupp" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
        <option value=0 selected>Tidak</option>
        <option value=1 >Ya</option>
      </select>
      <!-- <input id="AddEditNamaGdg" type="text" class="form-control" disabled>
      <input id="AddEditKodeGdg" type="hidden" class="form-control" disabled> -->
    </div>

  </div>
</div>
<div class="col-6">
  <div class="row">
    <div class="col-2">
      <div class="form-group">
      <label>Qty</label>
    </div>
    </div>
    <div class="col-4">
      <input id="AddEditInputQty" type="number" value='0.00' class="form-control text-right">
    </div>
    <div class="col-2">
      <input id="AddEditInputSatuan" type="text"  class="form-control  " disabled>
      <input id="AddEditInputIsi" type="hidden"  class="form-control  " disabled>
      <input id="AddEditInputSatuan1" type="hidden"  class="form-control  " disabled>
    </div>


  </div>
</div>
</div>



  <div class="row mt-2">
    <div class="col-md-12 text-right mt-4">
      <!-- <button type="button" class="btn btn-secondary" onclick="buttonBatalAdd()" >Batal</button>

      <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->

      <button type="button" class="btn btn-secondary btn-lg" style="
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonBatalAdd()" class="btn btn-secondary">Batal</button>

      <button type="button" id="buttonSubmitAddEdit" class="btn btn-primary btn-lg" style="
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="submitAddEdit()" class="btn btn-secondary">Submit Edit</button>


    </div>

  </div>
  <!-- <div class="line"></div> -->
  <!-- <hr/> -->
</div>

  </div>

  </div>



  <div id="page3" class="container-fluid" style="display: none">

    <div class="container-fluid">

      <!-- <div id="qrcode"></div> -->
      <div class="row">
        <div class="col-6 text-left">
          <h1 class="" id="modalTitleDetail">Detail</h1>

            <h1 class="" id="modalTitleOto">Otorisasi</h1>
        </div>
        <div class="col-6 text-right">
          <button type="button" class="btn btn-primary btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="buttonCloseForm()">
            Close
          </button>
        </div>
      </div>
    <!-- <button onclick="loadAll()">tes</button> -->
    </div>
    <div id="" class="">
    <div class="modal-body">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />
        <div class="row">

          <div class="col-3">
            <div class="row">


            <div class="col-4">
              <div class="form-group">
                <label>No SJ</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">
              <div class="form-group">
            </div> -->
            <div class="col-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_detail_nosj" placeholder="No SJ" disabled>
                <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoSJ" onclick="buttonAddListNoSJ()"><i class="bi bi-plus"></i></button> -->
              </div>
            </div>
          </div>
            </div>

            <div class="col-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No SO" disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="date" class="form-control" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}"  disabled>
                  </div>
                </div>
              </div>
            </div>


          </div>






        </div>

        <hr/>

        <div class="container-fluid">


        <div class="row">
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Customer</label>
                </div>
              </div>
              <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_kodecustomer" placeholder="" disabled>
                  </div>
              </div>




              </div>
              <div class="row" style="margin-top: -10px">
              <!-- <div class="col-2">
                <div class="form-group">
                div

                </div>

              </div> -->
              <div class="col-12">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_customer"  disabled></textarea>
                </div>
              </div>
            </div>

          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>No SO</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <!-- <input type="hidden" class="form-control" id="input_detail_kodegdg" placeholder="" > -->
                  <input type="text" class="form-control" id="input_detail_noso" placeholder="" disabled>
                </div>
              </div>


              </div>
              <div class="row" style="margin-top: -10px">

              <div class="col-4">
                <div class="form-group">

                  <label>Catatan</label>
                </div>

              </div>
              <div class="col-8">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none; " rows=4  class="form-control" id="input_detail_catatan"  disabled></textarea>
                </div>
              </div>

            </div>
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tgl SJ</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <!-- <input type="hidden" class="form-control" id="input_detail_kodegdg" placeholder="" > -->
                <input type="date" class="form-control" id="input_detail_tanggalsj" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>

              </div>
              <div class="row" style="margin-top: -10px">

              <!-- <div class="row"> -->
                <div class="col-4">
                  <div class="form-group">
                    <label>Gudang</label>
                  </div>
                </div>
                <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_detail_gudang" placeholder="" disabled>
                    </div>
                </div>

              <!-- </div> -->

            </div>
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tgl SC</label>
                </div>
              </div>

              <div class="col-8">
                <div class="form-group">
                  <!-- <input type="hidden" class="form-control" id="input_detail_kodegdg" placeholder="" > -->

                <input type="date" class="form-control" id="input_detail_tanggalsc" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>


              </div>
              <div class="row" style="margin-top: -10px">

              <div class="col-4">
                <div class="form-group">
                  <label>No Pol Kend</label>
                </div>
              </div>
              <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_nopol" placeholder="" disabled>
                  </div>
              </div>

            </div>
          </div>


        </div>

        <div class="row">
          <div class="col-3">

          </div>

          <div class="col-8">
            <div class="row">
              <div class="col-6">

              </div>

              <div class="col-6">
                <div class="row">


                </div>
              </div>

              <div class="col-12">
                <div class="row">


                </div>

              </div>



            </div>

          </div>

        </div>

        </div>



      </div>





    <div class="container-fluid" style="overflow-x: auto;">

          <table id="detailTable" class="table table-bordered table-hover table-striped table-responsive-lg"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Qty1</th>
                <th style="padding: 4px 12px;" scope="col">Sat1</th>
                <th style="padding: 4px 12px;" scope="col">Qty2</th>
                <th style="padding: 4px 12px;" scope="col">Sat2</th>
                <!-- <th scope="col">Actions</th> -->

              </tr>
            </thead>


            <tbody id="detailTableData" class="text-right" >
              <tr >

                  <td colspan=6 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    <div id="" class="container-fluid ">
      <div class="row">
        <div class="col-12 text-right">
          <button type="button" id="buttonSubmitOto" class="btn btn-primary btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="submitOto()" class="btn btn-secondary">Otorisasi</button>

        </div>

      </div>
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    </div>

    </div>

    </div>


</div>


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modalBodyAddListBarang" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12" style="margin-top: -40px">
              <h3>Barang</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row" style="margin-top: -20px">
            <div class="col-12" style="overflow:auto;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_barang" class="table table-bordered table-hover table-striped table-responsive-lg"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>

                  <th style="padding: 4px 12px;" scope="col">Qty</th>

                  <th style="padding: 4px 12px;" scope="col">Sat</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_barang" class="text-left" >

                <tr >

                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                    </td>
              </tr>
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>

    </div>
    <div id="modalBodyAddListNoSJ" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid" >
        <div class="row">
          <div class="col-12">
            <h3>SJ</h3>
          </div>
        </div>
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row" style="margin-top: -60px">
          <div class="col-12" style="overflow:auto;">
          <!-- <div class="container-fluid"> -->


          <table id="tabel_add_list_nosj" class="table table-bordered table-hover table-striped table-responsive-lg"  >
            <thead class="text-center bg-primary text-white">
              <tr>

                <th style="padding: 4px 12px;" scope="col">Actions</th>
                <th style="padding: 4px 12px;" scope="col">No SPB</th>
                <th style="padding: 4px 12px;" scope="col">Tgl</th>

                <th style="padding: 4px 12px;" scope="col">Tipe</th>
                <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
                <th style="padding: 4px 12px;" scope="col">Cust</th>

              </tr>
            </thead>


            <tbody id="tabel_data_add_list_nosj" class="text-left" >

              <tr >
                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                </td>
                <td>-</td>
                <td>-</td>

                <td>-</td>
                <td>-</td>
                <td>-</td>



            </tr>
            </tbody>


          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>

  </div>


  <div id="modalBodyAddListCust" class="showhidemodalbodyadd">
    <div class="modal-body" >

    <div class="container-fluid" >
      <div class="row">
        <div class="col-12">
          <h3>Custsupp</h3>
        </div>
      </div>
      <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
      <div class="row" style="margin-top: -60px">
        <div class="col-12" style="overflow:auto;">
        <!-- <div class="container-fluid"> -->


        <table id="tabel_add_list_custsupp" class="table table-bordered table-hover table-striped table-responsive-lg"  >
          <thead class="text-center bg-primary text-white">
            <tr>

              <th style="padding: 4px 12px;" scope="col">Actions</th>
              <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
              <th style="padding: 4px 12px;" scope="col">Cust</th>

            </tr>
          </thead>


          <tbody id="tabel_data_add_list_custsupp" class="text-left" >

            <tr >
              <td class="text-center">
                <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
              </td>
              <td>-</td>
              <td>-</td>



          </tr>
          </tbody>


        </table>
      <!-- </div> -->
        <!-- <button onclick="buttonSubKategori()">tes</button> -->
      </div>
        </div>
        </div>
  </div>

</div>


      <div id="modalBodyFooterList" class="modal-footer showhidemodalfooteradd">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <!-- <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()">Batal</button> -->
      </div>

      <div id="modalBodyFooterMain" class="modal-footer showhidemodalfooteradd">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->









@endsection

@section('js')
<script src="{{ asset('js/report-table.js') }}"></script>
<script src="{{ asset('js/headerEngine.js') }}?v={{ filemtime(public_path('js/headerEngine.js')) }}"></script>
<script type="text/javascript">

let listSJ = []
let listBarang = []
let listCust = []

let dataTableAddHeader = {}
let dataTableAdd = []
let dataForm = {}

let dataTableDetailHeader = {}
let dataTableDetail = []
let dataFormDetail = {}

let dataBarangAdd = {}
let dataBarangEdit = {}

// -- #tabel/#tabel2 interactive column engine -- same shared drag/hide/decimal-column
// engine so.blade.php uses for #tabel/#tabel7 (copied 1:1). Persistence goes through
// ReturSuratJalanController::loadHeader/simpanHeader (SML-backed), keyed by a href
// unique to each of this page's tables.
HeaderEngine.configure({
  loadUrl: "{!! url('retursuratjalanloadheader') !!}",
  simpanUrl: "{!! url('retursuratjalansimpanheader') !!}"
});

var lastTabelRows = [];
var lastTabel2Rows = [];

HeaderEngine.registerTable('tabel', {
  href: 'retursuratjalan_tabel',
  tableSel: '#tabel',
  barSel: '#rtBarTabel',
  setDefault: function () { setDefaultHeaderTabel(); },
  onChange: function () { reinitTabel(); }
});
HeaderEngine.registerTable('tabel2', {
  href: 'retursuratjalan_tabel2',
  tableSel: '#tabel2',
  barSel: '#rtBarTabel2',
  setDefault: function () { setDefaultHeaderTabel2(); },
  onChange: function () { reinitTabel2(); }
});

function setDefaultHeaderTabel() {
  gcart_header = [
    ['NOBUKTI',      'No. Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL',      'Tanggal',   1, 'date',    0, 0],
    ['NOSPB',        'No SPB',    1, 'varchar', 0, 0],
    ['TglSPB',       'Tgl SPB',   1, 'date',    0, 0],
    ['NamaCustSupp', 'Customer',  1, 'varchar', 0, 0]
  ];
}

function setDefaultHeaderTabel2() {
  gcart_header = [
    ['NOBUKTI',      'No. Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL',      'Tanggal',   1, 'date',    0, 0],
    ['NOSPB',        'No SPB',    1, 'varchar', 0, 0],
    ['TglSPB',       'Tgl SPB',   1, 'date',    0, 0],
    ['NamaCustSupp', 'Customer',  1, 'varchar', 0, 0],
    ['OtoUser1',     'User Oto1', 1, 'varchar', 0, 0],
    ['TglOto1',      'Tgl Oto1',  1, 'date',    0, 0]
  ];
}

// Fixed, non-draggable Actions cell for #tabel (Retur SJ Belum Otorisasi) -- same
// buttons/onclick args the old static markup and loadAll() row-builder both used,
// just read via pickCI() now.
function tabelActionsCell(row) {
  var nobukti = HeaderEngine.pickCI(row, 'NOBUKTI');
  var isOto1 = HeaderEngine.pickCI(row, 'IsOtorisasi1');
  var html = '<td class="text-center">';
  html += '<button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button> ';
  html += '<button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi(\'' + nobukti + '\' , \'' + isOto1 + '\')"><i class="bi bi-pen"></i></button> ';
  html += (Number(isOto1) === 1)
    ? '<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOto(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>'
    : '<button class="btn btn-primary btn-sm" type="button" onclick="buttonOto(\'' + nobukti + '\' , \'add\')"><i class="bi bi-key"></i></button>';
  html += '</td>';
  return html;
}

// Fixed Actions cell for #tabel2 (Retur SJ Sudah Otorisasi) -- no Koreksi button here,
// matching the old markup/loadAll() exactly.
function tabel2ActionsCell(row) {
  var nobukti = HeaderEngine.pickCI(row, 'NOBUKTI');
  var isOto1 = HeaderEngine.pickCI(row, 'IsOtorisasi1');
  var html = '<td class="text-center">';
  html += '<button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button> ';
  html += (Number(isOto1) === 1)
    ? '<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOto(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>'
    : '<button class="btn btn-primary btn-sm" type="button" onclick="buttonOto(\'' + nobukti + '\' , \'add\')"><i class="bi bi-key"></i></button>';
  html += '</td>';
  return html;
}

// col: [field, label, visible, type, hasTotal, decimals]. Date formatting matches this
// page's own formatDate(date, '/') convention (yyyy/mm/dd), not so.blade.php's dd/mm/yyyy
// -- preserves the exact display this page already had, only the mechanism changed.
function retursuratjalanFormatTanggal(raw) {
  if (!raw) { return ''; }
  var d = new Date(raw);
  if (isNaN(d)) { return ''; }
  var dd = ('0' + d.getDate()).slice(-2);
  var mm = ('0' + (d.getMonth() + 1)).slice(-2);
  return d.getFullYear() + '/' + mm + '/' + dd;
}

function tabelValueCell(row, col) {
  var raw = HeaderEngine.pickCI(row, col[0]);
  var type = col[3];

  if (type === 'date') {
    return '<td>' + retursuratjalanFormatTanggal(raw) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

// ReportTable.init()'s bindHead(thead) attaches drag/gear listeners with no matching
// removeEventListener -- calling init() again (which reinitTabel()/reinitTabel2() do, on
// purpose, to re-bind after DataTables rebuilds) is only safe against a genuinely NEW
// <thead> node each time, so the whole element is replaced here rather than just its
// innerHTML.
function replaceTheadWithHeader(tableSel, cols) {
  var oldThead = document.querySelector(tableSel + ' thead');
  if (!oldThead || !window.ReportTable) { return; }
  // ReportTable.headHtml() only knows about gcart_header entries, so it never accounts
  // for the fixed Actions column every body row starts with. Splice a plain,
  // non-draggable Actions <th> in as the first cell so thead/tbody column counts
  // actually match.
  var headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  var newThead = document.createElement('thead');
  newThead.setAttribute('style', 'white-space:nowrap;');
  newThead.innerHTML = headRowHtml;
  oldThead.parentNode.replaceChild(newThead, oldThead);
}

// DataTables names its auto-generated wrapper <tableId>_wrapper and puts
// .dataTables_paginate inside it, alongside the table -- meaning it's part of
// .table-wrap's horizontally-scrolling content. Physically relocating the pagination
// element into a container placed outside .table-wrap keeps it out of that scroll and
// always visible below the table. Empty the target first so each reinit replaces last
// time's pagination instead of stacking a new one on top of it.
function moveDataTablePagination(tableId, targetSel) {
  $(targetSel).empty();
  $('#' + tableId + '_wrapper .dataTables_paginate').appendTo(targetSel);
}

function renderTabelRows(rows) {
  if (HeaderEngine.activeKey() !== 'tabel') { HeaderEngine.activateEngineData('tabel'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; }); // same refs -- never .map()
  var html = '';
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  replaceTheadWithHeader('#tabel', cols);
}

function renderTabel2Rows(rows) {
  if (HeaderEngine.activeKey() !== 'tabel2') { HeaderEngine.activateEngineData('tabel2'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; });
  var html = '';
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabel2ActionsCell(row);
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  replaceTheadWithHeader('#tabel2', cols);
}

function reinitTabel() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: 'ftip', lengthChange: false, paging: true, order: [[1, 'asc']], ordering: false });
    moveDataTablePagination('tabel', '#tabelPaginationOutside');
    HeaderEngine.bindEngineDom('tabel');
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function reinitTabel2() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({ dom: 'ftip', lengthChange: false, paging: true, order: [[1, 'asc']], ordering: false });
    moveDataTablePagination('tabel2', '#tabel2PaginationOutside');
    HeaderEngine.bindEngineDom('tabel2');
  } catch (e) {
    console.error('reinitTabel2 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function buttonHeaderTable(key) {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    HeaderEngine.activateEngineData(key);
    HeaderEngine.doSetHeader(1, true);
    (key === 'tabel' ? reinitTabel : reinitTabel2)();
    alertify.success('Kolom telah direset ke tampilan default');
  }, function () {});
}

$(document).ready(function(){

      // #home/#tabel ("Retur SJ Belum Otorisasi") is the tab shown by default, so
      // initialize it last -- reinitTabel()/reinitTabel2() each end by binding
      // ReportTable to their own table, and whichever runs last wins, so this order
      // leaves the actually-visible tab interactive.
      HeaderEngine.activateEngineData('tabel2');
      HeaderEngine.doSetHeader(1);
      lastTabel2Rows = @json($tempOutstanding2);
      reinitTabel2();

      HeaderEngine.activateEngineData('tabel');
      HeaderEngine.doSetHeader(1);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();

      // Re-bind the interactive engine whenever the user switches tabs -- ReportTable's
      // listeners are bound to one table's DOM at a time.
      $('#nav-home-tab').on('shown.bs.tab', function () {
        HeaderEngine.activateEngineData('tabel');
        HeaderEngine.bindEngineDom('tabel');
      });
      $('#nav-profile-tab').on('shown.bs.tab', function () {
        HeaderEngine.activateEngineData('tabel2');
        HeaderEngine.bindEngineDom('tabel2');
      });

      ['tabel', 'tabel2'].forEach(function (key) {
        $('#' + key + '_length_visual').on('change', function () {
          var len = Number(this.value);
          $('#' + key).DataTable().page.len(len).draw();
        });
      });

        $("#tabel_add_list_nosj").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
                 {"targets" :[0] , 'orderable' : false}
              ]
        });

        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
                 {"targets" :[0] , 'orderable' : false}
              ]
        });

});


function buttonAddListBatal () {


  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  $('.showhidemodalfooteradd').hide();
  $('#modalBodyFooterMain').show();
}

function buttonCloseForm () {
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

}


function buttonBatalOto (nobukti) {
  console.log('buttonBatalOtorisasi' , nobukti)

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi RSPB ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();



        $.ajax({
          url: "{!! url('retursuratjalanspbataloto') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

          },
          success: function(res) {
            console.log('!', res)
            loadAll()

            // lockFormAdd()

            alertify.success('Berhasil Batal Otorisasi RSPB')

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });
}

function submitOto () {

  console.log(dataTableDetailHeader.NOBUKTI)


  let _token = $("#_token").val();



  $.ajax({
    url: "{!! url('retursuratjalanspoto') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti : dataTableDetailHeader.NOBUKTI

    },
    success: function(res) {
      console.log('!', res)
      loadAll()

      // lockFormAdd()
      // $("#formDetail").modal('toggle')
      $('#page3').hide();
      $('#page1').show();

      alertify.success('Berhasil Otorisasi RSPB')


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function loadAll () {
  console.log('loadall')
  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('retursuratjalanloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)

      lastTabelRows = res.tempOutstanding
      reinitTabel()

      lastTabel2Rows = res.tempOutstanding2
      reinitTabel2()
    }})


}

function buttonAddPickBarang (index) {
  dataBarangAdd = listBarang[index]
  console.log(dataBarangAdd)

  document.getElementById("AddAddKodeBrg").value = dataBarangAdd.kodebrg
  document.getElementById("AddAddNamaBrg").value = dataBarangAdd.Namabrg
  document.getElementById("AddAddInputSatuan").value = dataBarangAdd.Satuan
  document.getElementById("AddAddInputQty").value = parseFloat(dataBarangAdd.Qty).toFixed(2)


  buttonAddListBatal()
  $("#form").modal('toggle')
}

function buttonAddPickCust (index) {
  dataCust = listCust[index]
  console.log(dataCust)

  // document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_customer").value = dataCust.NAMACUSTSUPP + '\n' + dataCust.Alamat
  document.getElementById("input_add_kodecustomer").value = dataCust.KodeCustSupp
  document.getElementById("input_add_nosj").value = ''

  // document.getElementById("input_add_nopol").value = ''
  //
  // document.getElementById("input_add_tanggal").value = formatDate(new Date())
    // document.getElementById("input_add_catatan").value = ''
  $('#buttonAddListNoSJ').show();
  // $('#buttonAddListCustSupp').hide();
  buttonAddListBatal()

  $('.showhide').hide();
  $("#form").modal('toggle')
}

function buttonAddPickNoSJ (index) {
  dataForm = listSJ[index]
  console.log(dataForm)

  // document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_nosj").value = dataForm.NoSPB
  document.getElementById("input_add_noso").value = dataForm.NoSC
  // document.getElementById("input_add_customer").value = dataForm.NAMACUSTSUPP + '\n' + dataForm.Alamat
  // document.getElementById("input_add_kodecustomer").value = dataForm.KodeCustSupp
  document.getElementById("input_add_gudang").value = dataForm.Nama
  // document.getElementById("input_add_nopol").value = ''
  //
  // document.getElementById("input_add_tanggal").value = formatDate(new Date())
  document.getElementById("input_add_tanggalsj").value = formatDate(dataForm.TglSPB)
  document.getElementById("input_add_tanggalsc").value = formatDate(dataForm.TglSC)
  // document.getElementById("input_add_catatan").value = ''
  setNewNoBukti(dataForm.PPNCUST)
  // $('#buttonAddListNoSJ').hide();
  buttonAddListBatal()

  $('.showhide').hide();
  $("#form").modal('toggle')
}

function buttonOto (nobukti) {


  console.log('buttonOto' , nobukti)

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  let _token = $("#_token").val();
  $('#modalTitleDetail').hide();
  $('#modalTitleOto').show();

  $('#buttonSubmitOto').show();
  $.ajax({
    url: "{!! url('retursuratjalanspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti,


    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res.detail.length == 0) {
          // addTableData

          alertify.warning('Data habis/ tidak ditemukan')
          // $("#form").modal('toggle')
          return
      }
      dataTableDetail = res.detail
      dataTableDetailHeader = res.header[0]
      dataFormDetail = res.dataForm[0]
      console.log(formatDate(dataTableDetailHeader.TANGGAL))
      document.getElementById("input_detail_tanggal").value = formatDate(dataTableDetailHeader.TANGGAL)
      document.getElementById("input_detail_nopol").value = dataTableDetailHeader.NoPolKend
      document.getElementById("input_detail_catatan").value = dataTableDetailHeader.Catatan

      document.getElementById("input_detail_nobukti").value = nobukti
      document.getElementById("input_detail_nourut").value = dataTableDetailHeader.NOURUT

      document.getElementById("input_detail_nosj").value = dataFormDetail.NoSPB
      document.getElementById("input_detail_noso").value = dataFormDetail.NoSC
      document.getElementById("input_detail_customer").value = dataFormDetail.NAMACUSTSUPP + '\n' + dataFormDetail.Alamat
      document.getElementById("input_detail_kodecustomer").value = dataFormDetail.KodeCustSupp
      document.getElementById("input_detail_gudang").value = dataFormDetail.Nama
      // document.getElementById("input_detail_nopol").value = ''
      //
      // document.getElementById("input_detail_tanggal").value = formatDate(new Date())
      document.getElementById("input_detail_tanggalsj").value = formatDate(dataFormDetail.TglSPB)
      document.getElementById("input_detail_tanggalsc").value = formatDate(dataFormDetail.TglSC)


      let rowTable = ''

      dataTableDetail.forEach((item, i) => {
        rowTable += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td class='text-right'>${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>
            <td class='text-right'>${parseFloat(item.QNT2).toFixed(2)}</td>
            <td>${item.SAT_2}</td>

          </tr>
        `
      });


      document.getElementById("detailTableData").innerHTML = rowTable
       // $("#formDetail").modal('toggle')
       $('#page1').hide();
       $('#page3').show();

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })




  $("#formDetail").modal('toggle')
}

function buttonDetail (nobukti) {
  console.log('buttonDetail' , nobukti)
  let _token = $("#_token").val();
  $('#modalTitleDetail').show();

  $('#buttonSubmitOto').hide();
  $('#modalTitleOto').hide();
  $.ajax({
    url: "{!! url('retursuratjalanspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti,


    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res.detail.length == 0) {
          // addTableData

          alertify.warning('Data habis/ tidak ditemukan')
          // $("#form").modal('toggle')
          return
      }
      dataTableDetail = res.detail
      dataTableDetailHeader = res.header[0]
      dataFormDetail = res.dataForm[0]
      console.log(formatDate(dataTableDetailHeader.TANGGAL))
      document.getElementById("input_detail_tanggal").value = formatDate(dataTableDetailHeader.TANGGAL)
      document.getElementById("input_detail_nopol").value = dataTableDetailHeader.NoPolKend
      document.getElementById("input_detail_catatan").value = dataTableDetailHeader.Catatan

      document.getElementById("input_detail_nobukti").value = nobukti
      document.getElementById("input_detail_nourut").value = dataTableDetailHeader.NOURUT

      document.getElementById("input_detail_nosj").value = dataFormDetail.NoSPB
      document.getElementById("input_detail_noso").value = dataFormDetail.NoSC
      document.getElementById("input_detail_customer").value = dataFormDetail.NAMACUSTSUPP + '\n' + dataFormDetail.Alamat
      document.getElementById("input_detail_kodecustomer").value = dataFormDetail.KodeCustSupp
      document.getElementById("input_detail_gudang").value = dataFormDetail.Nama
      // document.getElementById("input_detail_nopol").value = ''
      //
      // document.getElementById("input_detail_tanggal").value = formatDate(new Date())
      document.getElementById("input_detail_tanggalsj").value = formatDate(dataFormDetail.TglSPB)
      document.getElementById("input_detail_tanggalsc").value = formatDate(dataFormDetail.TglSC)


      let rowTable = ''

      dataTableDetail.forEach((item, i) => {
        rowTable += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td class='text-right'>${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>
            <td class='text-right'>${parseFloat(item.QNT2).toFixed(2)}</td>
            <td>${item.SAT_2}</td>

          </tr>
        `
      });


      document.getElementById("detailTableData").innerHTML = rowTable
       // $("#formDetail").modal('toggle')
       $('#page1').hide();
       $('#page3').show();


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })




  // $("#formDetail").modal('toggle')
}

function refreshDataTableAdd (nobukti) {
  console.log('refreshDataTableAdd')
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('retursuratjalanspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti,


    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res.detail.length == 0) {
          // addTableData

          alertify.warning('Data habis/ tidak ditemukan')
          // $("#form").modal('toggle')

          $('#page2').hide();
          $('#page1').show();

          return
      }
      dataTableAdd = res.detail
      dataTableAddHeader = res.header[0]
      dataForm = res.dataForm[0]
      console.log(formatDate(dataTableAddHeader.TANGGAL))
      document.getElementById("input_add_tanggal").value = formatDate(dataTableAddHeader.TANGGAL)
      document.getElementById("input_add_nopol").value = dataTableAddHeader.NoPolKend
      document.getElementById("input_add_catatan").value = dataTableAddHeader.Catatan

      document.getElementById("input_add_nobukti").value = nobukti
      document.getElementById("input_add_nourut").value = dataTableAddHeader.NOURUT

      document.getElementById("input_add_nosj").value = dataForm.NoSPB
      document.getElementById("input_add_noso").value = dataForm.NoSC
      document.getElementById("input_add_customer").value = dataForm.NAMACUSTSUPP + '\n' + dataForm.Alamat
      document.getElementById("input_add_kodecustomer").value = dataForm.KodeCustSupp
      document.getElementById("input_add_gudang").value = dataForm.Nama
      // document.getElementById("input_add_nopol").value = ''
      //
      // document.getElementById("input_add_tanggal").value = formatDate(new Date())
      document.getElementById("input_add_tanggalsj").value = formatDate(dataForm.TglSPB)
      document.getElementById("input_add_tanggalsc").value = formatDate(dataForm.TglSC)

      document.getElementById("input_add_catatan").disabled = true;
      document.getElementById("input_add_nopol").disabled = true;
      document.getElementById("input_add_tanggal").disabled = true;

      let rowTable = ''

      dataTableAdd.forEach((item, i) => {
        rowTable += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td class='text-right'>${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>
            <td class='text-right'>${parseFloat(item.QNT2).toFixed(2)}</td>
            <td>${item.SAT_2}</td>
            <td class='text-center'>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEdit(${i})"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete('${item.NOBUKTI}' ,  ${item.URUT})"><i class="bi bi-trash"></i></button>


            </td>
          </tr>
        `
      });


      document.getElementById("addTableData").innerHTML = rowTable


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}


function submitAddEdit () {
  let tempKodeBarang = $("#AddEditKodeBrg").val();

  let tempNamaBarang = $("#AddEditNamaBrg").val();
  let choice = 'U'
  let _token = $("#_token").val();

  console.log(dataForm)
  console.log(dataTableAddHeader)
  console.log(dataBarangEdit)

  let tempQnt = $("#AddEditInputQty").val();
  console.log(tempQnt)

  if (Number(tempQnt) <= 0) {
    alertify.warning("Qty <= 0")
    return
  }




  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let tanggal = $("#input_add_tanggal").val();
  let nopol = $("#input_add_nopol").val();
  let catatan = $("#input_add_catatan").val();
  let retursupp = $("#AddEditReturSupp").val();

  let flagtipe = 0
  if (Number(dataBarangEdit.FlagTipe)) {
    flagtipe = 1
  }

  let tempQnt1 = tempQnt * dataBarangEdit.ISI

  console.log({
    _token : _token,
    choice,
    nobukti,
    nourut,
    tanggal,
    nosj : dataBarangEdit.NoSC,
    kodecustsupp: dataForm.KodeCustSupp,
    nopol,
    container: '',
    nocontainer : '',
    noseal : '',
    catatan,
    urut : dataBarangEdit.URUT,
    urutsj : dataBarangEdit.UrutSC,
    kodebrg : tempKodeBarang,
    qnt: tempQnt1,
    qnt2: tempQnt,
    sat1: dataBarangEdit.SAT_1,
    sat2:  dataBarangEdit.SAT_2,
    nosat : dataBarangEdit.NOSAT,
    isi :  dataBarangEdit.ISI,
    netw : 0,
    grossw: 0,
    isempty: dataTableAdd.length,
    namabrg: tempNamaBarang,
    flagmenu: 0,
    flagtipe,
    retursupp

  })
  // return

  $.ajax({
    url: "{!! url('retursuratjalanspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      nosj : dataBarangEdit.NoSC,
      kodecustsupp: dataForm.KodeCustSupp,
      nopol,
      container: '',
      nocontainer : '',
      noseal : '',
      catatan,
      urut : dataBarangEdit.URUT,
      urutsj : dataBarangEdit.UrutSC,
      kodebrg : tempKodeBarang,
      qnt: tempQnt1,
      qnt2: tempQnt,
      sat1: dataBarangEdit.SAT_1,
      sat2:  dataBarangEdit.SAT_2,
      nosat : dataBarangEdit.NOSAT,
      isi :  dataBarangEdit.ISI,
      netw : 0,
      grossw: 0,
      isempty: dataTableAdd.length,
      namabrg: tempNamaBarang,
      flagmenu: 0,
      flagtipe,
      retursupp

    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res == 1) {
        // loadAll()
        // refreshDataTableKoreksi(nobukti)
        refreshDataTableAdd(nobukti)
        $(".showhide").hide()
        // $("#form").modal('toggle')
        alertify.success('Item telah diedit');
        // console.log('before ===========')
        // loadAll()
        // console.log("after ===========")
      }




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })



}


function submitAddAdd () {
  let tempKodeBarang = $("#AddAddKodeBrg").val();

  let tempNamaBarang = $("#AddAddNamaBrg").val();
  let choice = 'I'
  let _token = $("#_token").val();

  if (!tempKodeBarang) {
    alertify.warning("Pilih barang terlebih dahulu")

    return
  }



  console.log(dataForm)
  console.log(dataBarangAdd)

  let tempQnt = $("#AddAddInputQty").val();
  console.log(tempQnt)

  if (Number(tempQnt) <= 0) {
    alertify.warning("Qty <= 0")
    return
  }




  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let tanggal = $("#input_add_tanggal").val();
  let nopol = $("#input_add_nopol").val();
  let catatan = $("#input_add_catatan").val();
  let retursupp = $("#AddAddReturSupp").val();

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  let checkDate = new Date(tanggal)

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let flagtipe = 0
  if (Number(dataForm.FlagTipe)) {
    flagtipe = 1
  }

  console.log({
    choice,
    nobukti,
    nourut,
    tanggal,
    nosj : dataBarangAdd.nobukti,
    kodecustsupp: dataForm.KodeCustSupp,
    nopol,
    container: '',
    nocontainer : '',
    noseal : '',
    catatan,
    urut : 0,
    urutsj : dataBarangAdd.urut,
    kodebrg : tempKodeBarang,
    qnt: dataBarangAdd.qnt,
    qnt2: dataBarangAdd.qnt2,
    sat1: dataBarangAdd.Sat_1,
    sat2:  dataBarangAdd.sat_2,
    nosat : dataBarangAdd.nosat,
    isi :  dataBarangAdd.isi,
    netw : 0,
    grossw: 0,
    isempty: dataTableAdd.length,
    namabrg: tempNamaBarang,
    flagmenu: 0,
    flagtipe,
    retursupp
  })

  let tempQnt1 = tempQnt * dataBarangAdd.isi

  $.ajax({
    url: "{!! url('retursuratjalanspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      nosj : dataBarangAdd.nobukti,
      kodecustsupp: dataForm.KodeCustSupp,
      nopol,
      container: '',
      nocontainer : '',
      noseal : '',
      catatan,
      urut : 0,
      urutsj : dataBarangAdd.urut,
      kodebrg : tempKodeBarang,
      qnt: tempQnt1,
      qnt2: tempQnt,
      sat1: dataBarangAdd.Sat_1,
      sat2:  dataBarangAdd.sat_2,
      nosat : dataBarangAdd.nosat,
      isi :  dataBarangAdd.isi,
      netw : 0,
      grossw: 0,
      isempty: dataTableAdd.length,
      namabrg: tempNamaBarang,
      flagmenu: 0,
      flagtipe,
      retursupp

    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res == 1) {
        loadAll()
        // refreshDataTableKoreksi(nobukti)
        document.getElementById("input_add_catatan").disabled = true;
        document.getElementById("input_add_nopol").disabled = true;

        document.getElementById("buttonAddListNoSJ").disabled = true;
        document.getElementById("buttonAddListCustSupp").disabled = true;
        refreshDataTableAdd(nobukti)
        $(".showhide").hide()
        // $("#form").modal('toggle')
        alertify.success('Item telah ditambah');
        // console.log('before ===========')
        // loadAll()
        // console.log("after ===========")
      }

      if (res == 2) {
        setNewNoBukti(dataForm.PPNCUST)
        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}


function buttonAddDelete (nobukti , urut) {

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log('buttonAddDelete')
  console.log(nobukti, urut)


  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"

        $.ajax({
          url: "{!! url('retursuratjalanspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            nobukti,
            nourut : '',
            tanggal: '',
            nosj : '',
            kodecustsupp: '',
            nopol: '' ,
            container: '',
            nocontainer : '',
            noseal : '',
            catatan : '',
            urut : urut,
            urutsj : 0,
            kodebrg : '',
            qnt: 0,
            qnt2: 0,
            sat1: '',
            sat2:  '',
            nosat : 1,
            isi :  1,
            netw : 0,
            grossw: 0,
            isempty: 1,
            namabrg: '',
            flagmenu: 0,
            flagtipe: 0,
            retursupp : 0

          },
          success: function(res) {
            console.log('res delete', res)
            loadAll()
            refreshDataTableAdd(nobukti)
            // lockFormAdd()
            $('.showhide').hide();
            // refreshDataTableAdd(nobukti)

            alertify.success('Berhasil menghapus item')

          },
          error: function (err) {
            console.log('err delete')
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });
}

function buttonAddListBarang () {

  let tempNoSJ = $("#input_add_nosj").val();
  if (!tempNoSJ) {
    alertify.warning("Pilih SJ terlebih dahulu")

    return
  }


  // $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListBarang').show();
  // $('.showhidemodalfooteradd').hide();
  // $('#modalBodyFooterList').show();

  listBarang = []
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('retursuratjalanlistbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti : tempNoSJ,
    },
    success: function(res) {
      console.log(res)
      listBarang = res

      if (!res.length) {
        alertify.warning('Tidak ada barang untuk ditambah')
        return
      }
      let rowTable = ``
      listBarang.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBarang(${i})" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.kodebrg}</td>
        <td>${item.Namabrg}</td>
        <td class="text-right">${parseFloat(item.Qty).toFixed(2)}</td>
        <td>${item.Satuan}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_barang").innerHTML = rowTable
      loadAll()
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListBarang').show();
      // showhidemodalfooteradd
      $('.showhidemodalfooteradd').hide();
      $('#modalBodyFooterList').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}



function buttonAddListCustSupp () {

  listCust = []
  $.ajax({
    url: "{!! url('retursuratjalanlistcustsuppbaru') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      console.log(res)
      $('#tabel_add_list_custsupp').DataTable().destroy();

      listCust = res
      let rowTable = ``
      listCust.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickCust(${i})" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.KodeCustSupp}</td>
        <td>${item.NAMACUSTSUPP}</td>
        </tr>`
      });




      if(!res.length) {
        rowTable= ``
      }
      document.getElementById("tabel_data_add_list_custsupp").innerHTML = rowTable

      $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
                 {"targets" :[0] , 'orderable' : false}
              ]
        });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListCust').show();
      // showhidemodalfooteradd
      $('.showhidemodalfooteradd').hide();
      $('#modalBodyFooterList').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonAddListNoSJ () {
  let xkodecust = $("#input_add_kodecustomer").val()

  if (!xkodecust) {

    alertify.warning("Pilih Cust Terlebih dahulu")
    return
  }
  let _token = $("#_token").val()

  listSJ = []
  $.ajax({
    url: "{!! url('retursuratjalanlistsj') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp : xkodecust

    },
    success: function(res) {
      console.log(res)
      $('#tabel_add_list_nosj').DataTable().destroy();

      listSJ = res
      let rowTable = ``
      listSJ.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoSJ(${i})" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.NoSPB}</td>
        <td>${formatDate(item.TglSPB , '/')}</td>
        <td class="text-right">${item.PPNCUST}</td>
        <td>${item.KodeCustSupp}</td>
        <td>${item.NAMACUSTSUPP}</td>
        </tr>`
      });




      if(!res.length) {
        rowTable= ``
      }
      document.getElementById("tabel_data_add_list_nosj").innerHTML = rowTable

      $("#tabel_add_list_nosj").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
                 {"targets" :[0] , 'orderable' : false}
              ]
        });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListNoSJ').show();
      // showhidemodalfooteradd
      $('.showhidemodalfooteradd').hide();
      $('#modalBodyFooterList').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
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




function buttonKoreksi (nobukti , oto) {
  console.log('buttonKoreksi')
  console.log(nobukti)
  console.log(oto)

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  if (Number(oto)) {
    alertify.warning('RSPB sudah diotorisasi')
    return
  }
  dataTableAdd = []
  dataForm = {}

  resetForm()

  // $('#buttonAddListNoSJ').hide();
  $('.showhide').hide();
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  document.getElementById("input_add_catatan").disabled = true;
  document.getElementById("input_add_nopol").disabled = true;
  document.getElementById("input_add_tanggal").disabled = true;
  refreshDataTableAdd(nobukti)
  document.getElementById("buttonAddListNoSJ").disabled = true;
  document.getElementById("buttonAddListCustSupp").disabled = true;
  $('.showhidemodalfooteradd').hide();
  $('#modalBodyFooterMain').show();
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();

}

function buttonAdd () {

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  dataTableAdd = []
  resetForm()
  // setNewNoBukti()
  $('#buttonAddListNoSJ').show();
  $('.showhide').hide();
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  document.getElementById("input_add_nobukti").value = '';

  document.getElementById("input_add_catatan").disabled = false;
  document.getElementById("input_add_nopol").disabled = false;
  document.getElementById("input_add_tanggal").disabled = false;
  document.getElementById("buttonAddListNoSJ").disabled = false;
  document.getElementById("buttonAddListCustSupp").disabled = false;
  document.getElementById("addTableData").innerHTML = `<tr >

      <td colspan=7 class="text-center">Belum ada data</td>

</tr>`;

  $('.showhidemodalfooteradd').hide();
  $('#modalBodyFooterMain').show();
  // $("#form").modal('toggle')
  $('#page2').show();
  $('#page1').hide();

}

function resetFormAddAdd () {
  document.getElementById("AddAddKodeBrg").value = ''
  document.getElementById("AddAddNamaBrg").value = ''
  document.getElementById("AddAddInputSatuan").value = ''
  document.getElementById("AddAddInputQty").value = '0.00'
  document.getElementById("AddAddReturSupp").value = 0
}

function resetForm () {
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_nosj").value = ''
  document.getElementById("input_add_noso").value = ''
  document.getElementById("input_add_customer").value = ''
  document.getElementById("input_add_kodecustomer").value = ''
  document.getElementById("input_add_gudang").value = ''
  document.getElementById("input_add_nopol").value = ''

  document.getElementById("input_add_tanggal").value = formatDate(new Date())
  document.getElementById("input_add_tanggalsj").value = ''
  document.getElementById("input_add_tanggalsc").value = ''
  document.getElementById("input_add_catatan").value = ''

}

function buttonAddEdit (i) {
  console.log('buttonAddEdit')
  console.log(dataTableAdd[i])
  dataBarangEdit = dataTableAdd[i]
  // return
  document.getElementById("AddEditReturSupp").value = Number(dataBarangEdit.FlagKembali)

  document.getElementById("AddEditKodeBrg").value = dataBarangEdit.KODEBRG
  document.getElementById("AddEditNamaBrg").value = dataBarangEdit.NAMABRG
  if (dataBarangEdit.NOSAT == 1) {

    document.getElementById("AddEditInputSatuan").value = dataBarangEdit.SAT_1
    document.getElementById("AddEditInputQty").value = parseFloat(dataBarangEdit.QNT).toFixed(2)
  } else {
    document.getElementById("AddEditInputSatuan").value = dataBarangEdit.SAT_2
    document.getElementById("AddEditInputQty").value = parseFloat(dataBarangEdit.QNT2).toFixed(2)
  }

  $('.showhide').hide();
  $('#formAddEdit').show();

  document.getElementById("AddEditKodeBrg").scrollIntoView();
}

function buttonAddAdd () {
  console.log('buttonAddAdd')
    // tipeformitem = 'Add'
    // document.getElementById("AddAddKodeBrg").value = ''
    // document.getElementById("AddAddNamaBrg").value = ''
    // document.getElementById("AddAddInputSatuan").value = ''
    // document.getElementById("AddAddInputQty").value = '0.00'
    // document.getElementById("AddAddReturSupp").value = 0

    if (!$("#input_add_nosj").val()) {
      alertify.warning("Pilih SJ terlebih dahulu")

      return
    }

    resetFormAddAdd()
    $('.showhide').hide();
    $('#formAddAdd').show();
    document.getElementById("AddAddKodeBrg").scrollIntoView();

}

function buttonBatalAdd () {

    $('.showhide').hide();
}



function setNewNoBukti (ppn) {
  $.ajax({
    url: "{!! url('retursuratjalanspnobukti') !!}",
    type: "get",
    async: false,
    data: {
      ppn
    },
    success: function(res) {
      console.log(res)

      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}


</script>

<script>
  // const tabHome = document.getElementById('nav-home-tab');
  // const tabProfile = document.getElementById('nav-profile-tab');

  function setActiveTab(idNav) {
    $(".nav-item").css("background-color", "#f8f9fa");
    $(".nav-item").css("color", "#007bff");
    console.log(idNav)
    document.getElementById(idNav).style.backgroundColor = '#007bff';
    document.getElementById(idNav).style.color = '#fff';

  }

  // Default warna tab
  setActiveTab("nav-home-tab");

  // buat ganti tab
  document.getElementById('nav-home-tab').addEventListener('click', function () {
    setActiveTab("nav-home-tab");
  });

  document.getElementById('nav-profile-tab').addEventListener('click', function () {
    setActiveTab("nav-profile-tab");
  });

</script>




@endsection
