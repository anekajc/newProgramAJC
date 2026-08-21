@extends('newmaster')
@section('buttons')

@endsection

@section('css')

{{-- report-table.css/report-table.js + public/js/headerEngine.js power #tabel/#tabel2/
     #tabel3/#tabel5/#tabel6's draggable-column/gear-menu headers and "Tampilkan"
     (page-length) control, same pattern as so.blade.php's #tabel/#tabel7. Linked here
     page-locally (not in newmaster.blade.php) so only this page gets it. #tabel4 ("Out SO
     Booking") is left on the old static header -- its nav-tab link is already commented
     out elsewhere in this file, so it's unreachable in the UI. --}}
<link rel="stylesheet" href="{{ asset('css/report-table.css') }}?v={{ filemtime(public_path('css/report-table.css')) }}">

<style>
  /* Holds the pagination element JS relocates here (see moveDataTablePagination()) so it
     lives outside .table-responsive's horizontal scroll. Matches so.blade.php's
     .tb-pagination-outside treatment. */
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

  /* "Reset kolom" pill + the row that holds it alongside the hidden-columns bar.
     Not part of report-table.css itself (so.blade.php declares these page-locally
     too) -- kept OUTSIDE #rtBarTabelX as a flex sibling, not a child, because
     report-table.js's renderBar() fully overwrites that div's innerHTML on every
     drag/hide/decimal change; a button placed inside it would vanish on re-render. */
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

{{-- tampilan search bar 1 --}}
  @section('css')
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

{{-- tampilan search bar 3 --}}
  <style>
  #tabel3_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel3_filter label input {
      width: 150px;
      padding: 5px 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  #tabel3_filter label {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
    }

  #tabel3_filter input:focus {
      border-color: #007bff;
      outline: none;
    }
  </style>
{{-- end tampilan search bar 3 --}}

<style>
#tabel6_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }

#tabel6_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel6_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }

#tabel6_filter input:focus {
    border-color: #007bff;
    outline: none;
  }
</style>

<style>
#tabel4_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }

#tabel4_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel4_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }

#tabel4_filter input:focus {
    border-color: #007bff;
    outline: none;
  }
</style>


<style>
#tabel5_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }

#tabel5_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel5_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }

#tabel5_filter input:focus {
    border-color: #007bff;
    outline: none;
  }
</style>

{{-- end tampilan search bar 2 --}}

{{-- tampilan search bar modal add pelanggan --}}
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
{{-- end tampilan search bar modal add pelanggan --}}

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
@endsection


@section('content')
<div id="page1" class="container-fluid">
<div class="container-fluid">

  <!-- <div id="qrcode"></div> -->
  <div class="row">
    <div class="col-6 text-left">
      <h2 style="margin-top:-85px;">Outstanding SO</h2>
    </div>
    <div class="col-6 text-right" >

    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
<!-- <button onclick="buttonAdd()">tes</button> -->
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
  <div class="card" style="margin-top: -30px">
<div class="card-header" >
<div class="row">
  <nav style="width: 100%;">
    <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
 
  <a class="nav-item nav-link" id="nav-profile3-tab" data-toggle="tab" href="#profile3" role="tab" aria-controls="nav-profile3" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        Out SO Prioritas
      </a>

  <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        SO Siap Kirim
      </a>


      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true"
         style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left; ">
        SO Belum Siap Kirim
      </a>
    

      <a class="nav-item nav-link" id="nav-profile1-tab" data-toggle="tab" href="#profile1" role="tab" aria-controls="nav-profile1" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        Surat Jalan Belum Otorisasi
      </a>

      <a class="nav-item nav-link" id="nav-profile4-tab" data-toggle="tab" href="#profile4" role="tab" aria-controls="nav-profile4" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        Surat Jalan Sudah Otorisasi
      </a>

      
   <!--   <a class="nav-item nav-link" id="nav-profile2-tab" data-toggle="tab" href="#profile2" role="tab" aria-controls="nav-profile2" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        Out SO Booking
      </a> -->
    

    </div>
  </nav>
</div>
</div>
<div class="card-body" style="padding:0;" >
<div class="tab-content" id="myTabContent">

  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="row">
      <div class="col-12">
        {{-- .tb-report scopes report-table.css's gear-menu/drag-column styling (and the
             "Tampilkan" toolbar below) to just this table. --}}
        <div class="tb-report main">
        <div class="toolbar">
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
    </div>
  </div>
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
      <div class="col-12">
        <div class="tb-report main">
        <div class="toolbar">
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
  <div class="tab-pane fade" id="profile1" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
      <div class="col-12">
        <div class="tb-report main">
        <div class="toolbar">
          <div class="filter-wrap">
            <label for="tabel3_length_visual">Tampilkan</label>
            <select id="tabel3_length_visual" class="filter-inp" style="cursor:pointer;">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="-1">Semua</option>
            </select>
          </div>
        </div>
        <div class="rt-bar-row">
          <button class="rt-reset-btn" type="button" title="Reset kolom" onclick="buttonHeaderTable('tabel3')">
            <i class="bi bi-arrow-clockwise"></i> Reset kolom
          </button>
          <div id="rtBarTabel3"></div>
        </div>
        <div class="table-outer">
          <div class="table-wrap">
            <table id="tabel3" class="tb">
              <thead style="white-space:nowrap;"></thead>
              <tbody id="tabel3_data" class="text-left"></tbody>
            </table>
          </div>
          <div id="tabel3PaginationOutside" class="tb-pagination-outside"></div>
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
  <div class="tab-pane fade" id="profile4" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
      <div class="col-12">
        <div class="tb-report main">
        <div class="toolbar">
          <div class="filter-wrap">
            <label for="tabel6_length_visual">Tampilkan</label>
            <select id="tabel6_length_visual" class="filter-inp" style="cursor:pointer;">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="-1">Semua</option>
            </select>
          </div>
        </div>
        <div class="rt-bar-row">
          <button class="rt-reset-btn" type="button" title="Reset kolom" onclick="buttonHeaderTable('tabel6')">
            <i class="bi bi-arrow-clockwise"></i> Reset kolom
          </button>
          <div id="rtBarTabel6"></div>
        </div>
        <div class="table-outer">
          <div class="table-wrap">
            <table id="tabel6" class="tb">
              <thead style="white-space:nowrap;"></thead>
              <tbody id="tabel6_data" class="text-left"></tbody>
            </table>
          </div>
          <div id="tabel6PaginationOutside" class="tb-pagination-outside"></div>
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
  <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
      <div class="col-12">
        <div class="table-responsive">

              <table id="tabel4" class="table table-bordered table-hover table-striped table-responsive-lg">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Nobukti</th>
                    <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Cust</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                    <th style="padding: 4px 12px;" scope="col">Ref PR</th>
                    <th style="padding: 4px 12px;" scope="col">NoPesanan</th>
                    <th style="padding: 4px 12px;" scope="col">Qnt</th>
                    <th style="padding: 4px 12px;" scope="col">Qty</th>
                    <th style="padding: 4px 12px;" scope="col">Satuan</th>
                    <th style="padding: 4px 12px;" scope="col">SaldoQnt</th>
                    <th style="padding: 4px 12px;" scope="col">Catatan</th>
                    <th style="padding: 4px 12px;" scope="col">DueDate</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Kebun</th>
                    <th style="padding: 4px 12px;" scope="col">PartNumber</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Merk</th>
                    <th style="padding: 4px 12px;" scope="col">UserID</th>

                  </tr>
                </thead>


                <tbody id="tabel4_data" class="text-left" >
                    @for ($i = 0; $i < count($tempOutstanding4); $i++)
                      <tr>
                        <td>{{ $tempOutstanding4[$i]->NOBUKTI }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding4[$i]->Tanggal)) !!}</td>
                        <td>{{ $tempOutstanding4[$i]->NamaCustSupp }}</td>
                        <td>{{ $tempOutstanding4[$i]->NamaBrg }}</td>
                        <td>{{ $tempOutstanding4[$i]->RefPR }}</td>
                        <td>{{ $tempOutstanding4[$i]->Nopesanan }}</td>
                        <td class="text-right">{{ number_format($tempOutstanding4[$i]->Qnt, 2 ,'.' , '') }}</td>
                        <td class="text-right">{{ number_format($tempOutstanding4[$i]->QntOut, 2 ,'.' , '') }}</td>
                        <td>{{ $tempOutstanding4[$i]->SATUAN }}</td>
                        <td class="text-right">{{ number_format($tempOutstanding4[$i]->SaldoQnt, 2 ,'.' , '') }}</td>
                        <td>{{ $tempOutstanding4[$i]->catatan }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding4[$i]->DUEDATE)) !!}</td>
                        <td>{{ $tempOutstanding4[$i]->namakebun }}</td>
                        <td>{{ $tempOutstanding4[$i]->PartNumber }}</td>
                        <td>{{ $tempOutstanding4[$i]->NamaMerk }}</td>
                        <td>{{ $tempOutstanding4[$i]->UserID }}</td>

                      </tr>
                    @endfor
                </tbody>
              </table>
        </div>
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="profile3" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
      <div class="col-12">
        <div class="tb-report main">
        <div class="toolbar">
          <div class="filter-wrap">
            <label for="tabel5_length_visual">Tampilkan</label>
            <select id="tabel5_length_visual" class="filter-inp" style="cursor:pointer;">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="-1">Semua</option>
            </select>
          </div>
        </div>
        <div class="rt-bar-row">
          <button class="rt-reset-btn" type="button" title="Reset kolom" onclick="buttonHeaderTable('tabel5')">
            <i class="bi bi-arrow-clockwise"></i> Reset kolom
          </button>
          <div id="rtBarTabel5"></div>
        </div>
        <div class="table-outer">
          <div class="table-wrap">
            <table id="tabel5" class="tb">
              <thead style="white-space:nowrap;"></thead>
              <tbody id="tabel5_data" class="text-left"></tbody>
            </table>
          </div>
          <div id="tabel5PaginationOutside" class="tb-pagination-outside"></div>
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
</div>


<div id="page2" class="container-fluid" style="display: none">

  <div class="row" style="margin-top: -65px">
    <div class="col-8 text-left">
      <h2>Form Surat Jalan</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
    </div>
  </div>
    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
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
                <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Out" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No SO</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_noso" placeholder="No SO" disabled>
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
                  <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >
                </div>
              </div>
            </div>
          </div>




        </div>

        <hr/ style="margin-top: -5px">
        <div class="row">
          <div class="col-md-3">
            <div class="row">

          <div class="col-md-4">
            <div class="form-group">
              <label>Gudang</label>
            </div>
          </div>
          <!-- <div class="col-8 text-right">
            <div class="form-group input-group">

          </div>
        </div> -->
          <div class="col-md-8">
            <div class="form-group input-group">
              <input type="hidden" class="form-control" id="input_add_kodegdg" placeholder="" >
              <input type="text" class="form-control" id="input_add_gdg" placeholder="" disabled>
              <button class="btn btn-primary btn-sm text-right" id="buttonAddListGudang" onclick="buttonAddListGudang()"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>
        <div class="row" style="margin-top: -10px">
          <div class="col-md-4">
            <div class="form-group">
              <label>Ekspedisi</label>
            </div>
          </div>
          <!-- <div class="col-3 text-right">
            <div class="form-group">

          </div>
        </div> -->
          <div class="col-md-8">
            <div class="form-group input-group">
              <input type="hidden" class="form-control" id="input_add_kodeekspedisi" placeholder="" >
              <input type="text" class="form-control" id="input_add_ekspedisi" placeholder=""  disabled>
              <button class="btn btn-primary btn-sm text-right" id="buttonAddListEkspedisi" onclick="buttonAddListEkspedisi()"><i class="bi bi-plus"></i></button>
            </div>
          </div>
        </div>

        <div class="row" style="margin-top: -10px">
          <div class="col-md-4">
            <div class="form-group">
              <label>No Pol Kend.</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_nopol" placeholder="" required >
            </div>
          </div>
        </div>



          <div class="row" style="margin-top: -10px">

          <div class="col-md-4">
            <div class="form-group">
              <label>Sopir</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_sopir" placeholder="" required >
            </div>
          </div>

        </div>

      </div>


      <div class="col-md-3">
        <div class="row">

      <div class="col-md-4">
        <div class="form-group">
          <label>Customer</label>
        </div>
      </div>
      <div class="col-md-8">
        <div class="form-group">
          <input type="text" class="form-control" id="input_add_customer" placeholder="" required disabled>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top: -10px">
      <div class="col-md-4">
        <div class="form-group">
          <label>Lok. Penerima</label>
        </div>
      </div>
      <div class="col-md-8">
        <div class="form-group">
          <input type="text" class="form-control" id="input_add_lokasipenerima" placeholder="" required disabled>
        </div>
      </div>
    </div>



      <div class="row" style="margin-top: -10px">


      <div class="col-md-4">
        <div class="form-group">
          <label>Ref UKM</label>
        </div>
      </div>
      <div class="col-md-8">
        <div class="form-group">
          <input type="text" class="form-control" id="input_add_refukm" placeholder="" required >
        </div>
      </div>

    </div>

  </div>

  <div class="col-md-6">
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label>Alamat Kirim</label>
        </div>
      </div>
      <div class="col-md-10">
        <div class="form-group">
          <textarea type="text" style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_alamatkirim"  disabled></textarea>
          <!-- <input type="text" class="form-control" id="input_add_alamatkirim" placeholder="" required disabled> -->
        </div>
      </div>

    </div>
      <div class="row" style="margin-top: -10px">


      <div class="col-md-2">
        <div class="form-group">
          <label>Catatan SO</label>
        </div>
      </div>
      <div class="col-md-10">
        <div class="form-group">
          <input type="text" class="form-control" id="input_add_catatanso" placeholder="" required >
        </div>
      </div>
    </div>

  </div>

      </div>

      <div class="row">


      </div>
      </div>



    </div>
      <div class="container-fluid" style="overflow-x: auto;">
        <hr/>

            <table id="addTable" class="table table-bordered table-hover table-striped table-responsive-lg" >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Terima</th>
                  <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Satuan</th>
                  <th style="padding: 4px 12px;" scope="col">Qty OS</th>
                  <th style="padding: 4px 12px;" scope="col">Qty Sisa</th>
                  <th style="padding: 4px 12px;" scope="col">Qty Book</th>
                  <th style="padding: 4px 12px;" scope="col">Qty Kirim</th>

                </tr>
              </thead>


              <tbody id="addTableData" class="text-right" >
                <tr >

                    <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>

              </tr>

              </tbody>


            </table>
      </div>
    </div>
    <!-- <div class="container fluid"> -->
      <div class="row">

        <div class="col-12">
          <!-- <hr/> -->

          <div class="container-fluid">
            <hr/>
            <div class="row">
              <div id="" class="text-right col-12">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
                <button type="button" id="submitAddAdd" class="btn btn-primary btn-lg" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="submitAdd()" class="btn btn-secondary">Submit Add</button>

              </div>

            </div>

          </div>
        </div>

      </div>

    <!-- </div> -->




</div>

<!-- page 2 end -->

<div id="page3" class="container-fluid" style="display: none">
  <div class="row" style="margin-top: -65px">
    <div class="col-6 text-left">
      <h1 style="">Form Koreksi Surat Jalan</h1>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
    </div>
  </div>



  <div id="" class="">
  <div class="modal-body">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" />
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
              <input type="text" class="form-control" id="input_koreksi_nobukti" placeholder="No Out" disabled>
            </div>
          </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_noso" placeholder="No SO" disabled>
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
                <input type="date" class="form-control text-center" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
          </div>
        </div>




      </div>

      <hr/ style="margin-top:-10px">
      <div class="row">
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Ekspedisi</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">
              <div class="form-group">
            </div> -->
            <div class="col-md-8">
              <div class="form-group">
                <input type="hidden" class="form-control" id="input_koreksi_kodeekspedisi" placeholder="" >
                <input type="text" class="form-control" id="input_koreksi_ekspedisi" placeholder=""  disabled>
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">


            <div class="col-md-4">
              <div class="form-group">
                <label>No Pol Kend.</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_nopol" placeholder="" required disabled>
              </div>
            </div>


          </div>
            <div class="row" style="margin-top: -10px">


            <div class="col-md-4">
              <div class="form-group">
                <label>Sopir</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_sopir" placeholder="" required disabled>
              </div>
            </div>







          </div>

          <!-- <div class="row">

        <div class="col-md-4" style="display: none">
          <div class="form-group">
            <label>Gudang</label>
          </div>
        </div>

      </div> -->
        <!-- <div class="col-md-8" style="display: none">
          <div class="form-group"> -->
            <input type="hidden" class="form-control" id="input_koreksi_kodegdg" placeholder="" >
            <input type="hidden" class="form-control" id="input_koreksi_gdg" placeholder="" disabled>
          <!-- </div>
        </div> -->


      </div>

      <div class="col-md-3">
        <div class="row">


          <div class="col-md-4">
            <div class="form-group">
              <label>Customer</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" class="form-control" id="input_koreksi_customer" placeholder="" required disabled>
            </div>
          </div>

        </div>
          <div class="row" style="margin-top: -10px">



          <div class="col-md-4">
            <div class="form-group">
              <label>Lok. Penerima</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" class="form-control" id="input_koreksi_lokasipenerima" placeholder="" required disabled>
            </div>
          </div>


        </div>

          <div class="row" style="margin-top: -10px">



          <div class="col-md-4">
            <div class="form-group">
              <label>Ref UKM</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" class="form-control" id="input_koreksi_refukm" placeholder="" required disabled >
            </div>
          </div>


        </div>



      </div>

      <div class="col-md-6">
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label>Alamat Kirim</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_koreksi_alamatkirim"  disabled></textarea>
              <!-- <input type="text" class="form-control" id="input_koreksi_alamatkirim" placeholder="" required disabled> -->
            </div>
          </div>

        </div>
          <div class="row" style="margin-top: -10px">




          <div class="col-md-2">
            <div class="form-group">
              <label>Catatan SO</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <input type="text" class="form-control" id="input_koreksi_catatanso" placeholder="" required disabled>
            </div>
          </div>





        </div>

      </div>







        <!-- <div class="col-2">
          <div class="form-group">
            <label>Nama Cust</label>
          </div>
        </div>
        <div class="col-4">
          <div class="form-group">
            <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Gdg Tujuan" disabled>
          </div>
        </div> -->


      </div>

    </div>


    <div class="col-8">
      <div class="row">







    <!-- <div class="col-2">
      <div class="form-group">
        <label>Nama Cust</label>
      </div>
    </div>
    <div class="col-4">
      <div class="form-group">
        <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Gdg Tujuan" disabled>
      </div>
    </div> -->


  </div>

</div>

    </div>

    <div class="row">


    </div>
    </div>







  <div class="container-fluid mt-4" style="overflow-x: auto; ">
    <div class="mt-4">

      <hr/ style="margin-top: -20px">
    </div>

        <table id="koreksiTable" class="table table-bordered table-hover table-striped table-responsive-lg" >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
              <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
              <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
              <th style="padding: 4px 12px;" scope="col">Qty</th>
              <th style="padding: 4px 12px;" scope="col">Sat</th>
              <th style="padding: 4px 12px;" scope="col">Qty2</th>
              <th style="padding: 4px 12px;" scope="col">Sat2</th>
              <th style="padding: 4px 12px;" scope="col">Gudang</th>
              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="koreksiTableData" class="text-right" >
            <tr >

                <!-- <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td> -->
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>

          </tr>

          </tbody>


        </table>
  </div>


  <div class="container-fluid">
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
          onclick="buttonKoreksiAdd()" class="btn btn-secondary"><b>+ Tambah Item</b></button>
        <!-- <button type="button" onclick="buttonKoreksiAdd()" class="btn btn-primary">+ Tambah Item</button> -->
      </div>

    </div>

    <!-- koreksi add -->
    <div id="formAddAdd" class="container-fluid showhide">
      <!-- <div class="line"></div> -->
      <hr/>
      <div class="row">
        <div class="col-12">
          <h4>Add Item</h4>
        </div>
      </div>
      <div class="row">

        <div class="col-md-3">



          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Kode Barang</label>
              </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="form-group input-group">
                <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
                <button type="button" onclick="buttonKoreksiListBarang()" class="btn btn-primary" >+</button>

              </div>

            </div>

          </div>

        </div>

        <div class="col-md-6">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Nama Barang</label>
              </div>
            </div>
            <div class="col-md-6">
              <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
            </div>
          </div>
        </div>
        <div class="col-md-3">

        </div>

      </div>
        <div class="row" style="margin-top: -10px">

        <div class="col-md-3 ">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Gudang</label>
              </div>
            </div>
            <!-- <div class="col-4 text-right">

            </div> -->
            <div class="col-md-8">
              <div class="form-group input-group">

                <input id="AddAddNamaGdg" type="text" class="form-control" disabled>
                <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled>
                <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
              </div>
            </div>

          </div>
        </div>
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-2">
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

          <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
        </div>

      </div>
      <!-- <div class="line"></div> -->
      <hr/>
    </div>

    <div id="formAddEdit" class="container-fluid showhide">
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

            <button type="button" onclick="buttonAddListBarang('edit')" class="btn btn-primary" >+</button>
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
              <label>Gudang</label>
            </div>
          </div>
          <!-- <div class="col-8 text-right">

          </div> -->
          <div class="col-8">
            <div class="input-group form-group">

              <input id="AddEditNamaGdg" type="text" class="form-control" disabled>
              <input id="AddEditKodeGdg" type="hidden" class="form-control" disabled>
              <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
            </div>
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
            <input id="AddEditInputSatuan" type="text"  class="form-control " disabled>
            <input id="AddEditInputSatuan1" type="hidden"  class="form-control " disabled>
          </div>


        </div>
      </div>
    </div>



    <div class="row mt-2">
      <div class="col-md-12 text-right mt-4">

        <button type="button" class="btn btn-danger btn-lg" style="
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonBatalAdd()" class="btn btn-secondary">Batal</button>


        <button type="button" id="buttomSubmitAddEdit" class="btn btn-primary btn-lg" style="
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="submitAddEdit()" class="btn btn-secondary">Edit</button>
        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>

    </div>
    <!-- <div class="line"></div> -->
    <hr/>
  </div>

</div>


  </div>


  <div id="page4" class="container-fluid" style="display: none">
    <div class="row" style="margin-top: -65px">
      <div class="col-6 text-left">
        <h1 style="">Form Detail Surat Jalan</h1>
      </div>
      <div class="col-6 text-right">
        <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
      </div>
    </div>



    <div id="" class="">
    <div class="modal-body">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />
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
                <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Out" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No SO</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_noso" placeholder="No SO" disabled>
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
                  <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>
            </div>
          </div>




        </div>

        <hr/ style="margin-top:-10px">
        <div class="row">
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Ekspedisi</label>
                </div>
              </div>
              <!-- <div class="col-3 text-right">
                <div class="form-group">
              </div> -->
              <div class="col-md-8">
                <div class="form-group">
                  <input type="hidden" class="form-control" id="input_detail_kodeekspedisi" placeholder="" >
                  <input type="text" class="form-control" id="input_detail_ekspedisi" placeholder=""  disabled>
                </div>
              </div>

            </div>
              <div class="row" style="margin-top: -10px">


              <div class="col-md-4">
                <div class="form-group">
                  <label>No Pol Kend.</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_nopol" placeholder="" required disabled>
                </div>
              </div>


            </div>
              <div class="row" style="margin-top: -10px">


              <div class="col-md-4">
                <div class="form-group">
                  <label>Sopir</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_sopir" placeholder="" required disabled>
                </div>
              </div>







            </div>

            <!-- <div class="row">

          <div class="col-md-4" style="display: none">
            <div class="form-group">
              <label>Gudang</label>
            </div>
          </div>

        </div> -->
          <!-- <div class="col-md-8" style="display: none">
            <div class="form-group"> -->
              <input type="hidden" class="form-control" id="input_detail_kodegdg" placeholder="" >
              <input type="hidden" class="form-control" id="input_detail_gdg" placeholder="" disabled>
            <!-- </div>
          </div> -->


        </div>

        <div class="col-md-3">
          <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_customer" placeholder="" required disabled>
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">



            <div class="col-md-4">
              <div class="form-group">
                <label>Lok. Penerima</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_lokasipenerima" placeholder="" required disabled>
              </div>
            </div>


          </div>

            <div class="row" style="margin-top: -10px">



            <div class="col-md-4">
              <div class="form-group">
                <label>Ref UKM</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_refukm" placeholder="" required disabled >
              </div>
            </div>


          </div>



        </div>

        <div class="col-md-6">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Alamat Kirim</label>
              </div>
            </div>
            <div class="col-md-10">
              <div class="form-group">
                <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatkirim"  disabled></textarea>
                <!-- <input type="text" class="form-control" id="input_detail_alamatkirim" placeholder="" required disabled> -->
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">




            <div class="col-md-2">
              <div class="form-group">
                <label>Catatan SO</label>
              </div>
            </div>
            <div class="col-md-10">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_catatanso" placeholder="" required disabled>
              </div>
            </div>





          </div>

        </div>







          <!-- <div class="col-2">
            <div class="form-group">
              <label>Nama Cust</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Gdg Tujuan" disabled>
            </div>
          </div> -->


        </div>

      </div>


      <div class="col-8">
        <div class="row">







      <!-- <div class="col-2">
        <div class="form-group">
          <label>Nama Cust</label>
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Gdg Tujuan" disabled>
        </div>
      </div> -->


    </div>

  </div>

      </div>

      <div class="row">


      </div>
      </div>







    <div class="container-fluid mt-4" style="overflow-x: auto; ">
      <div class="mt-4">

        <hr/ style="margin-top: -20px">
      </div>

          <table id="detailTable" class="table table-bordered table-hover table-striped table-responsive-lg" >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Qty2</th>
                <th style="padding: 4px 12px;" scope="col">Sat2</th>
                <th style="padding: 4px 12px;" scope="col">Gudang</th>

              </tr>
            </thead>


            <tbody id="detailTableData" class="text-right" >
              <tr >

                  <!-- <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td> -->
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>

            </tr>

            </tbody>


          </table>
    </div>





    </div>






  </div>




</div>



<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialo g-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modalBodyAddListGudang" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12" style="margin-top:-40px;">
              <h3>Gudang</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_gudang" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>

                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_gudang" class="text-left" >

                <tr >

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
    <div id="modalBodyAddListEkspedisi" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid mt-4" >
        <div class="row">
          <div class="col-12" style="margin-top:-40px;">
            <h3>Ekspedisi</h3>
          </div>
        </div>
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto;">
          <!-- <div class="container-fluid"> -->


          <table id="tabel_add_list_ekspedisi" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">
              <tr>

                <th style="padding: 4px 12px;" scope="col">Actions</th>
                <th style="padding: 4px 12px;" scope="col">Kode</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>

              </tr>
            </thead>


            <tbody id="tabel_data_add_list_ekspedisi" class="text-left" >

              <tr >

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

    </div>
  </div>
</div>
<!-- End modal add-->


<!-- start modal detail -->
<div class="modal fade"  id="formDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>




        <div id="modalBodyDetailMain" class="showhidemodalbodyadd">
          <div class="modal-body" >

        <div class="container-fluid">
          <div class="row">

            <input type="hidden" class="form-control" id="input_detail_nourut" >
            <div class="col-md-8">



              <div class="row">
                <div class="col-md-6">
                  <div class="row">


                <div class="col-9">
                  <div class="form-group">
                    <label>Pelanggan</label>
                  </div>
                </div>
                <div class="col-3 text-right">
                  <div class="form-group">
                <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListPelanggan" onclick="buttonAddListPelanggan()"><i class="bi bi-plus"></i></button> -->
                </div>

              </div>
              </div>
            </div>
            <div class="col-md-6">
            </div>


              <div class="col-md-6">
                <div class="row">


                <div class="col-md-12">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_kodepelanggan"  disabled>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_namapelanggan"  disabled>
                  </div>
                </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">


                <div class="col-md-12">
                  <div class="form-group">
                    <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_detail_alamatpelanggan"  disabled></textarea>
                  </div>
                </div>
                </div>
              </div>

              </div>
            </div>
            <div class="col-md-4">

              <div class="row">


              <div class="col-md-12">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_nobukti" placeholder="" disabled>
                </div>
              </div>


              <div class="col-md-12">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>
            </div>


            </div>















          </div>
          <!-- <hr/> -->
          <div class="row ">
            <div class="col-md-12 text-left">
            <button type="button" class="btn btn-primary" onclick="buttonDetailMainHeader()" class="btn btn-secondary"  >Header</button>
            <button type="button" class="btn btn-primary" onclick="buttonDetailMainItems()" class="btn btn-secondary"  >Items</button>
        </div>
      </div>
      <hr/>
      <div class="showhidemodalbodydetailmain" id="modalBodyDetailMainHeader">

      <div class="row">
        <div class="col-md-4">
          <div class="row">

          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>


                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="hidden" class="form-control" id="input_detail_kodesales" >
                  <input type="text" class="form-control" id="input_detail_namasales"  disabled>
                </div>
              </div>

            </div>

          </div>

          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Alamat Kirim</label>
              </div>
            </div>

          <div class="col-md-12">
            <div class="form-group">
              <input type="hidden" class="form-control" id="input_detail_kodealamatkirim" >
              <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatkirim"  disabled></textarea>
            </div>
          </div>
          </div>

          <div class="row">
            <div class="col-md-6">

            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>



          </div>
        </div>
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_valas"  disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>Kurs</label>
                </div>
              </div>



            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_kurs"  disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-12">


          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>Draft PO</label>
              </div>
            </div>



          <div class="col-md-6">
            <select disabled id="input_detail_draftpo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
              <option value=0 selected>Tidak</option>
              <option value=1 >Ya</option>
            </select>
          </div>
          </div>
          </div>



          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-6">
              <div class="row">


              <div class="col-12">
                <div class="form-group">
                  <label>PIC</label>
                </div>
              </div>

            </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <input type="hidden" class="form-control" id="input_detail_kodepic"  >
                    <input type="text" class="form-control" id="input_detail_namapic"  disabled>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <div class="row">




          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Lokasi Penerima</label>
              </div>
            </div>

          <div class="col-md-12">
            <div class="form-group">
              <input type="hidden" class="form-control" id="input_detail_kodelokasipenerima" >
              <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatlokasipenerima"  disabled></textarea>
            </div>
          </div>

          <div class="col-md-12">
          <div class="row">
            <div class="col-6">

              <div class="form-group">
                <label>No PO</label>
              </div>
            </div>





          <div class="col-md-6">
            <div class="form-group">
              <input type="text" class="form-control" id="input_detail_nopo"  disabled>
            </div>
          </div>
          </div>
          </div>



          <div class="col-md-12">


          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tgl PO</label>
              </div>
            </div>


          <div class="col-md-6">
            <div class="form-group">
              <input type="date" class="form-control text-center" id="input_detail_tanggalpo" value="{!! date('Y-m-d') !!}" disabled>
            </div>
          </div>
          </div>
          </div>
          <div class="col-md-12">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tgl Kirim</label>
              </div>
            </div>


          <div class="col-md-6">
            <div class="form-group">
              <input type="date" class="form-control text-center" id="input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
            </div>
          </div>
          </div>
        </div>
          </div>









        </div>

        <div class="col-md-4">
          <div class="row">
            <div class="col-md-6">
              <div class="row">


              <div class="col-12">
                <div class="form-group">
                  <label>Back Office</label>
                </div>
              </div>

            </div>
            </div>

            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <div class="row">



                  <div class="col-12">


                  <div class="form-group">
                    <input type="hidden" class="form-control" id="input_detail_kodebackoffice" >
                    <input type="text" class="form-control" id="input_detail_namabackoffice"  disabled>
                  </div>

                  </div>
                  </div>

                </div>
              </div>

            </div>





              <div class="col-md-12">
                <label>Keterangan</label>
              </div>



            <div class="col-md-12">

              <div class="form-group" style="margin-top: 14px">
                <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_catatan" disabled></textarea>


              </div>
            </div>



          </div>


          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>DP</label>
              </div>
            </div>



          <div class="col-md-6">
            <div class="form-group">
              <input type="number" class="form-control text-right" id="input_detail_dp" value='0.00'  disabled>
            </div>
          </div>
          </div>

          <div class="row">



          <div class="col-md-12">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>Pembayaran</label>
              </div>
            </div>



          <div class="col-md-6">
            <select  id="input_detail_pembayaran"  class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
              <option value=0 selected >Tunai</option>
              <option value=1  >Kredit</option>
            </select>
          </div>
          </div>
          </div>

          <div class="col-md-12">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>TOP</label>
              </div>
            </div>



          <div class="col-md-6">
            <div class="form-group">
              <input type="number" class="form-control text-right" id="input_detail_hari"  value=0 min=0 disabled>
            </div>
          </div>
          </div>
          </div>

          <div class="col-md-12">

          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>



          <div class="col-md-6">
            <select  id="input_detail_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
              <option value=0 selected>None</option>
              <option value=1 >Exclude</option>
              <option value=2 >Include</option>
            </select>
          </div>
          </div>
          </div>









          </div>
















          <div class="row ">

      </div>

        </div>
        <!-- <div class="col-md-12 mt-2 text-right" style="margin-bottom: 20px">
        <button type="button" class="btn btn-primary" id="buttonSubmitSaveHeader" onclick="submitSaveHeader()" class="btn btn-secondary"  >Save Header</button>
    </div> -->


      </div>
      <hr/>
    </div>

    </div>
    <div id="modalBodyFooterList" class="modal-footer showhidemodalfooteradd">
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()">Batal</button>
    </div>
    <div class="showhidemodalbodydetailmain container-fluid" id="modalBodyDetailMainItems">
    <div class="row ">
      <div class="col-md-12 mt-2 text-right">

  </div>
</div>




        <div class="container-fluid mt-4" style="overflow:auto;">
          <div class="row">
            <table id="tabel_detail" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>

                  <th scope="col">Sat</th>
                  <th scope="col">Harga</th>
                  <th scope="col">Diskon</th>
                  <th scope="col">NDPP</th>


                </tr>
              </thead>


              <tbody id="tabel_data_detail" class="text-left" >

                <tr >

                  <td></td>
                  <td></td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
                      <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-list"></i></button>
                    </td>
              </tr>
              </tbody>


            </table>
          </div>



    </div>

    </div>
    </div>
      <hr/ style="margin-top: -20px">
    <div class="container-fluid" style="margin-top: -10px;">


          <div class="row">


        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Disc %</label>
              </div>
            </div>


          <div class="col-12" style="margin-top: -20px">
            <div class="form-group">
              <input type="number" class="form-control text-right" id="input_detail_disc" onchange="onChangeInputAddDisc()" value ="0.00" disabled>
            </div>
          </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>DiscRp</label>
              </div>
            </div>


          <div class="col-12" style="margin-top: -20px">
            <div class="form-group">
              <input type="number" class="form-control text-right" id="input_detail_discrp" onchange="onChangeInputAddDiscRp()" value ="0.00" disabled>
            </div>
          </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>DPP</label>
              </div>
            </div>


          <div class="col-12" style="margin-top: -20px">
            <div class="form-group">
              <input type="text" class="form-control text-right" id="input_detail_dpp" value ="0.00" disabled>
            </div>
          </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>


          <div class="col-12" style="margin-top: -20px">
            <div class="form-group">
              <input type="text" class="form-control text-right" id="input_detail_ppn" value ="0.00" disabled>
            </div>
          </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Grand Total</label>
              </div>
            </div>


          <div class="col-12" style="margin-top: -20px">
            <div class="form-group">
              <input type="text" class="form-control text-right" id="input_detail_grandtotal" value ="0.00" disabled>
            </div>
          </div>
          </div>
        </div>

      </div>

      <div class="row justify-content-end">




  </div>



    </div>


  </div>


</div>
</div>
</div>
<!-- End modal detail-->

<!-- start modal koreksi -->
<div class="modal fade" id="formKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi SPB</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modalKoreksiListBarang" class="showhidemodalkoreksi">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Barang</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_koreksi_list_barang" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>

                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>

                  <th style="padding: 4px 12px;" scope="col">Qnt Out</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>


                </tr>
              </thead>


              <tbody id="tabel_data_koreksi_list_barang" class="text-left" >
                @for ($i = 0; $i < count($listBarang); $i++)
                <tr >

                  <td>{{ $listBarang[$i]->Kodebrg }}</td>
                  <td>{{ $listBarang[$i]->NamaBrg }}</td>

                  <td>{{ $listBarang[$i]->SATUAN }}</td>
                  <td></td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" onclick="buttonKoreksiPickBarang('{{ $listBarang[$i]->Kodebrg }}' , '{{ $listBarang[$i]->NamaBrg }}' , '{{ $listBarang[$i]->Sat1 }}' , '{{ $listBarang[$i]->ISI1 }}')"><i class="bi bi-plus"></i></button>
                    </td>
              </tr>
              @endfor
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>

    </div>
      <div id="modalKoreksiListGudang" class="showhidemodalkoreksi">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Gudang</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_koreksi_list_gudang" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_koreksi_list_gudang" class="text-left" >
                @for ($i = 0; $i < count($listGudang); $i++)

                <tr >

                  <td>{{ $listGudang[$i]->KODEGDG }}</td>
                  <td>{{ $listGudang[$i]->NAMA }}</td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" onclick="buttonKoreksiPickGudang('{{ $listGudang[$i]->KODEGDG }}' , '{{ $listGudang[$i]->NAMA }}')"><i class="bi bi-plus"></i></button>
                    </td>
              </tr>
              @endfor
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>

    </div>

      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <!-- <button type="button" class="btn btn-primary" onclick="submitKoreksi()">Koreksi</button> -->
      </div>
    </div>
  </div>
</div>
<!-- End modal koreksi-->

<!-- start modal add -->
<div class="modal fade"  id="formKirimTerima" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tgl Kirim / Terima</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          
          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <h4 id='titleInvoice'>No Invoice</h4>
              </div>
            </div>

          </div>

          
          <div class="row">
            <div class="col-8">
              <div class="form-group">
                <h4 id='titleSupplier'>Supplier</h4>
              </div>
            </div>

          </div>

          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <label class="text-left">Tanggal Kirim</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggalKirim">
              </div>
            </div>

          </div>

          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <label class="text-left">Tanggal Terima Barang</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggalTerimaBarang">
              </div>
            </div>

          </div>

            
          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <label class="text-left">Tanggal Terima</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggalTerima">
              </div>
            </div>

          </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitKirimTerima()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->


<!-- start modal add -->
<div class="modal fade"  id="formKirimTerimaAcc" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tgl Terima ACC</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          
          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <h4 id='titleInvoice2'>No Invoice</h4>
              </div>
            </div>

          </div>

          
          <div class="row">
            <div class="col-8">
              <div class="form-group">
                <h4 id='titleSupplier2'>Supplier</h4>
              </div>
            </div>

          </div>

          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <label class="text-left">Tanggal Kirim</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggalKirimAcc">
              </div>
            </div>

          </div>


    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitKirimTerimaAcc()">Submit</button>
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

let dataTableAdd = []
let dataTableAddHeader = []
let dataTableEdit = []
let dataKoreksiEdit = {}
let dataKoreksiAdd = {}

let dataAddAddListItem = []

let dataRefreshOutstanding = []
let dataRefreshOutstanding2 = []
let dataRefreshOutstanding3 = []

let dataRefreshPenerimaan = []

let dataTableKoreksiHeader = []
let dataTableKoreksi = []
let listAddBarangKoreksi = []
let tempAddAdd = {}
let tempAddEdit = {}
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}

let tipeform = ''
let tipeformitem = ''

// -- #tabel/#tabel2/#tabel3/#tabel5/#tabel6 interactive column engine --
// Same shared drag/hide/decimal-column engine so.blade.php uses for #tabel/#tabel7.
// Persistence goes through SuratJalanController::loadHeader/simpanHeader (SML-backed),
// keyed by a href unique to each of this page's tables. #tabel4 ("Out SO Booking") is
// left out -- its nav-tab link is already commented out elsewhere in this file.
HeaderEngine.configure({
  loadUrl: "{!! url('suratjalanloadheader') !!}",
  simpanUrl: "{!! url('suratjalansimpanheader') !!}"
});

var lastTabelRows = [];
var lastTabel2Rows = [];
var lastTabel3Rows = [];
var lastTabel5Rows = [];
var lastTabel6Rows = [];

HeaderEngine.registerTable('tabel', {
  href: 'suratjalan_tabel',
  tableSel: '#tabel',
  barSel: '#rtBarTabel',
  setDefault: function () { setDefaultHeaderTabel(); },
  onChange: function () { reinitTabel(); }
});
HeaderEngine.registerTable('tabel2', {
  href: 'suratjalan_tabel2',
  tableSel: '#tabel2',
  barSel: '#rtBarTabel2',
  setDefault: function () { setDefaultHeaderTabel2(); },
  onChange: function () { reinitTabel2(); }
});
HeaderEngine.registerTable('tabel3', {
  href: 'suratjalan_tabel3',
  tableSel: '#tabel3',
  barSel: '#rtBarTabel3',
  setDefault: function () { setDefaultHeaderTabel3(); },
  onChange: function () { reinitTabel3(); }
});
HeaderEngine.registerTable('tabel5', {
  href: 'suratjalan_tabel5',
  tableSel: '#tabel5',
  barSel: '#rtBarTabel5',
  setDefault: function () { setDefaultHeaderTabel5(); },
  onChange: function () { reinitTabel5(); }
});
HeaderEngine.registerTable('tabel6', {
  href: 'suratjalan_tabel6',
  tableSel: '#tabel6',
  barSel: '#rtBarTabel6',
  setDefault: function () { setDefaultHeaderTabel6(); },
  onChange: function () { reinitTabel6(); }
});

function setDefaultHeaderTabel() {
  gcart_header = [
    ['NOBUKTI',    'No. Bukti',       1, 'varchar', 0, 0],
    ['Tanggal',    'Tanggal',         1, 'date',    0, 0],
    ['NamaBrg',    'Nama Barang',     1, 'varchar', 0, 0],
    ['KODEBRG',    'Kode Barang',     1, 'varchar', 0, 0],
    ['Nopesanan',  'PO Cust',         1, 'varchar', 0, 0],
    ['QntOut',     'Qty Sisa',        1, 'float',   0, 2],
    ['Qnt',        'Qty SO',          1, 'float',   0, 2],
    ['SATUAN',     'SAT',             1, 'varchar', 0, 0],
    ['catatan',    'Catatan',         1, 'varchar', 0, 0],
    ['DUEDATE',    'Due Date',        1, 'date',    0, 0],
    ['namakebun',  'Lokasi Penerima', 1, 'varchar', 0, 0],
    ['PartNumber', 'Part Number',     1, 'varchar', 0, 0],
    ['NamaMerk',   'Nama Merk',       1, 'varchar', 0, 0],
    ['UserID',     'UserID',          1, 'varchar', 0, 0]
  ];
}

// #tabel2 ("SO Siap Kirim") shares dbSuratJalan's same outstanding-SO query shape as
// #tabel ("SO Belum Siap Kirim"), just pre-filtered to rows ready to ship.
function setDefaultHeaderTabel2() {
  gcart_header = [
    ['NOBUKTI',    'No. Bukti',       1, 'varchar', 0, 0],
    ['Tanggal',    'Tanggal',         1, 'date',    0, 0],
    ['NamaBrg',    'Nama Barang',     1, 'varchar', 0, 0],
    ['KODEBRG',    'Kode Barang',     1, 'varchar', 0, 0],
    ['Nopesanan',  'PO Cust',         1, 'varchar', 0, 0],
    ['QntOut',     'Qty Sisa',        1, 'float',   0, 2],
    ['Qnt',        'Qty SO',          1, 'float',   0, 2],
    ['SATUAN',     'SAT',             1, 'varchar', 0, 0],
    ['catatan',    'Catatan',         1, 'varchar', 0, 0],
    ['DUEDATE',    'Due Date',        1, 'date',    0, 0],
    ['namakebun',  'Lokasi Penerima', 1, 'varchar', 0, 0],
    ['PartNumber', 'Part Number',     1, 'varchar', 0, 0],
    ['NamaMerk',   'Nama Merk',       1, 'varchar', 0, 0],
    ['UserID',     'UserID',          1, 'varchar', 0, 0]
  ];
}

// 'LokasiPenerima' has no matching field in dbSPB's result set (the original static
// markup rendered an always-empty <td></td> for it too) -- kept as a column so hiding/
// reordering still works, it just always renders blank via tabelValueCell's fallback.
function setDefaultHeaderTabel3() {
  gcart_header = [
    ['NOBUKTI',        'Nobukti',         1, 'varchar', 0, 0],
    ['TANGGAL',        'Tanggal',         1, 'date',    0, 0],
    ['NamaCustSupp',   'Customer',        1, 'varchar', 0, 0],
    ['NoPesanan',      'NoPesanan',       1, 'varchar', 0, 0],
    ['IDUser',         'User',            1, 'varchar', 0, 0],
    ['TGLKIRIM',       'Tgl Kirim',       1, 'date',    0, 0],
    ['TGLTERIMA',      'Tgl Terima',      1, 'date',    0, 0],
    ['LokasiPenerima', 'Lokasi Penerima', 1, 'varchar', 0, 0],
    ['RefUKM',         'No UKM',          1, 'varchar', 0, 0],
    ['Isbatal',        'Batal',           1, 'bool',    0, 0],
    ['Userbatal',      'User Batal',      1, 'varchar', 0, 0],
    ['Tglbatal',       'Tgl Batal',       1, 'date',    0, 0]
  ];
}

function setDefaultHeaderTabel5() {
  gcart_header = [
    ['NOBUKTI',      'Nobukti',    1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',    1, 'date',    0, 0],
    ['NamaCustSupp', 'Nama Cust',  1, 'varchar', 0, 0],
    ['NamaBrg',      'Nama Brg',   1, 'varchar', 0, 0],
    ['RefPR',        'Ref PR',     1, 'varchar', 0, 0],
    ['Nopesanan',    'NoPesanan',  1, 'varchar', 0, 0],
    ['Qnt',          'Qnt',        1, 'float',   0, 2],
    ['QntOut',       'Qty',        1, 'float',   0, 2],
    ['SATUAN',       'Satuan',     1, 'varchar', 0, 0],
    ['SaldoQnt',     'SaldoQnt',   1, 'float',   0, 2],
    ['catatan',      'Catatan',    1, 'varchar', 0, 0],
    ['DUEDATE',      'DueDate',    1, 'date',    0, 0],
    ['namakebun',    'Nama Kebun', 1, 'varchar', 0, 0],
    ['PartNumber',   'PartNumber', 1, 'varchar', 0, 0],
    ['NamaMerk',     'Nama Merk',  1, 'varchar', 0, 0],
    ['UserID',       'UserID',     1, 'varchar', 0, 0]
  ];
}

function setDefaultHeaderTabel6() {
  gcart_header = [
    ['NOBUKTI',        'Nobukti',         1, 'varchar', 0, 0],
    ['TANGGAL',        'Tanggal',         1, 'date',    0, 0],
    ['NamaCustSupp',   'Customer',        1, 'varchar', 0, 0],
    ['NoPesanan',      'NoPesanan',       1, 'varchar', 0, 0],
    ['IDUser',         'User',            1, 'varchar', 0, 0],
    ['TGLKIRIM',       'Tgl Kirim',       1, 'date',    0, 0],
    ['TGLTERIMA',      'Tgl Terima',      1, 'date',    0, 0],
    ['OtoUser1',       'User Oto1',       1, 'varchar', 0, 0],
    ['TglOto1',        'Tgl Oto1',        1, 'date',    0, 0],
    ['LokasiPenerima', 'Lokasi Penerima', 1, 'varchar', 0, 0],
    ['RefUKM',         'No UKM',          1, 'varchar', 0, 0],
    ['Isbatal',        'Batal',           1, 'bool',    0, 0],
    ['Userbatal',      'User Batal',      1, 'varchar', 0, 0],
    ['Tglbatal',       'Tgl Batal',       1, 'date',    0, 0]
  ];
}

// Fixed, non-draggable Actions cell for #tabel6 -- same buttons/onclick args the old
// static markup and loadAll() row-builder both used, just read via pickCI() now.
function tabel6ActionsCell(row) {
  var nobukti = HeaderEngine.pickCI(row, 'NOBUKTI');
  var namaCustSupp = HeaderEngine.pickCI(row, 'NamaCustSupp');
  var tglKirim = HeaderEngine.pickCI(row, 'TGLKIRIM');
  var tglTerimaBrg = HeaderEngine.pickCI(row, 'TglTerimaBRG');
  var tglTerima = HeaderEngine.pickCI(row, 'TGLTERIMA');
  var tglSpbInvc = HeaderEngine.pickCI(row, 'TglSPBINVC');
  var html = '<td class="text-center">';
  html += '<button class="btn btn-primary btn-sm" type="button" onclick="buttonKirimTerima(\'' + nobukti + '\',\'' + namaCustSupp + '\',\'' + tglKirim + '\',\'' + tglTerimaBrg + '\',\'' + tglTerima + '\')"><i class="bi bi-calendar4-week"></i></button> ';
  html += '<button class="btn btn-success btn-sm" type="button" onclick="buttonTerimaAcc(\'' + nobukti + '\',\'' + namaCustSupp + '\',\'' + tglSpbInvc + '\')"><i class="bi bi-calendar4-range"></i></button>';
  html += '</td>';
  return html;
}

// col: [field, label, visible, type, hasTotal, decimals]. Numeric formatting stays
// plain .toFixed() (no id-ID thousands separator) to match this page's existing qty
// display -- unlike so.blade.php's Rupiah columns, these are quantities, not money.
function suratjalanFormatTanggal(raw) {
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
    return '<td>' + suratjalanFormatTanggal(raw) + '</td>';
  }
  if (type === 'float') {
    var dp = Number(col[5]) || 0;
    var n = (raw !== undefined && raw !== null && raw !== '') ? parseFloat(raw) : 0;
    if (isNaN(n)) { n = 0; }
    return '<td class="text-right">' + n.toFixed(dp) + '</td>';
  }
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

// ReportTable.init()'s bindHead(thead) attaches drag/gear listeners with no matching
// removeEventListener -- calling init() again (which reinitTabelX() do, on purpose, to
// re-bind after DataTables rebuilds) is only safe against a genuinely NEW <thead> node
// each time, so the whole element is replaced here rather than just its innerHTML.
function suratjalanReplaceThead(tableSel, cols, leadingThHtml) {
  var oldThead = document.querySelector(tableSel + ' thead');
  if (!oldThead || !window.ReportTable) { return; }
  var headRowHtml = ReportTable.headHtml(cols);
  if (leadingThHtml) {
    headRowHtml = headRowHtml.replace('<tr>', '<tr>' + leadingThHtml);
  }
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
    html += '<tr style="color: red">';
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  suratjalanReplaceThead('#tabel', cols);
}

function renderTabel2Rows(rows) {
  if (HeaderEngine.activeKey() !== 'tabel2') { HeaderEngine.activateEngineData('tabel2'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; });
  var html = '';
  (rows || []).forEach(function (row) {
    html += '<tr>';
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  suratjalanReplaceThead('#tabel2', cols);
}

function renderTabel3Rows(rows) {
  if (HeaderEngine.activeKey() !== 'tabel3') { HeaderEngine.activateEngineData('tabel3'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; });
  var html = '';
  (rows || []).forEach(function (row) {
    html += '<tr>';
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel3_data').innerHTML = html;
  suratjalanReplaceThead('#tabel3', cols);
}

function renderTabel5Rows(rows) {
  if (HeaderEngine.activeKey() !== 'tabel5') { HeaderEngine.activateEngineData('tabel5'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; });
  var html = '';
  (rows || []).forEach(function (row) {
    html += '<tr>';
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel5_data').innerHTML = html;
  suratjalanReplaceThead('#tabel5', cols);
}

function renderTabel6Rows(rows) {
  if (HeaderEngine.activeKey() !== 'tabel6') { HeaderEngine.activateEngineData('tabel6'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; });
  var html = '';
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabel6ActionsCell(row);
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel6_data').innerHTML = html;
  suratjalanReplaceThead('#tabel6', cols, '<th style="padding: 4px 12px;">Actions</th>');
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

function reinitTabel3() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel3')) { $('#tabel3').DataTable().destroy(); }
    renderTabel3Rows(lastTabel3Rows);
    $('#tabel3').DataTable({ dom: 'ftip', lengthChange: false, paging: true, order: [[1, 'asc']], ordering: false });
    moveDataTablePagination('tabel3', '#tabel3PaginationOutside');
    HeaderEngine.bindEngineDom('tabel3');
  } catch (e) {
    console.error('reinitTabel3 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function reinitTabel5() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel5')) { $('#tabel5').DataTable().destroy(); }
    renderTabel5Rows(lastTabel5Rows);
    $('#tabel5').DataTable({ dom: 'ftip', lengthChange: false, paging: true, ordering: false });
    moveDataTablePagination('tabel5', '#tabel5PaginationOutside');
    HeaderEngine.bindEngineDom('tabel5');
  } catch (e) {
    console.error('reinitTabel5 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function reinitTabel6() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel6')) { $('#tabel6').DataTable().destroy(); }
    renderTabel6Rows(lastTabel6Rows);
    $('#tabel6').DataTable({ dom: 'ftip', lengthChange: false, paging: true, order: [[1, 'asc']], ordering: false });
    moveDataTablePagination('tabel6', '#tabel6PaginationOutside');
    HeaderEngine.bindEngineDom('tabel6');
  } catch (e) {
    console.error('reinitTabel6 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function buttonHeaderTable(key) {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    HeaderEngine.activateEngineData(key);
    HeaderEngine.doSetHeader(1, true);
    ({ tabel: reinitTabel, tabel2: reinitTabel2, tabel3: reinitTabel3, tabel5: reinitTabel5, tabel6: reinitTabel6 })[key]();
    alertify.success('Kolom telah direset ke tampilan default');
  }, function () {});
}

$(document).ready(function(){

      // #home/#tabel ("SO Belum Siap Kirim") is the tab shown by default, so
      // initialize it last -- reinitTabelX() each end by binding ReportTable to their
      // own table, and whichever runs last wins, so this order leaves the actually-
      // visible tab interactive.
      HeaderEngine.activateEngineData('tabel3');
      HeaderEngine.doSetHeader(1);
      lastTabel3Rows = @json($tempOutstanding3);
      reinitTabel3();

      HeaderEngine.activateEngineData('tabel6');
      HeaderEngine.doSetHeader(1);
      lastTabel6Rows = @json($tempOutstanding6);
      reinitTabel6();

      HeaderEngine.activateEngineData('tabel2');
      HeaderEngine.doSetHeader(1);
      lastTabel2Rows = @json($tempOutstanding2);
      reinitTabel2();

      HeaderEngine.activateEngineData('tabel5');
      HeaderEngine.doSetHeader(1);
      lastTabel5Rows = @json($tempOutstanding5);
      reinitTabel5();

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
      $('#nav-profile1-tab').on('shown.bs.tab', function () {
        HeaderEngine.activateEngineData('tabel3');
        HeaderEngine.bindEngineDom('tabel3');
      });
      $('#nav-profile3-tab').on('shown.bs.tab', function () {
        HeaderEngine.activateEngineData('tabel5');
        HeaderEngine.bindEngineDom('tabel5');
      });
      $('#nav-profile4-tab').on('shown.bs.tab', function () {
        HeaderEngine.activateEngineData('tabel6');
        HeaderEngine.bindEngineDom('tabel6');
      });

      ['tabel', 'tabel2', 'tabel3', 'tabel5', 'tabel6'].forEach(function (key) {
        $('#' + key + '_length_visual').on('change', function () {
          var len = Number(this.value);
          $('#' + key).DataTable().page.len(len).draw();
        });
      });

            $("#tabel4").DataTable({
              "lengthChange": false,
                "paging": false ,
                // "columns" : [{"width" : "20px"}]

              });

        $("#tabel_add_list_barang").DataTable({
          "lengthChange": false,
            "paging": false ,
        });

    $("#tabel_add_list_pelanggan").DataTable({
      "lengthChange": false,
        "paging": false ,
    });

  $("#tabel_add_list_sales").DataTable({
    "lengthChange": false,
      "paging": false ,
    });
    // $("#tabel_koreksi_list_gudang").DataTable({
    //   "lengthChange": false,
    //     "paging": false ,
    // });
    // $("#tabel_koreksi_list_barang").DataTable({
    //   "lengthChange": false,
    //     "paging": false ,
    // });

    //   formAddListItem
});


function closeShowHideAdd () {
  $('.showhide').hide();

}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  $('.showhidemodalfooteradd').hide();
  $('#modalBodyFooterMain').show();
}


function buttonKoreksiListBarang () {


  let _token = $("#_token").val();
  let noso = $("#input_koreksi_noso").val();
  console.log(noso)
  listAddBarangKoreksi = []

  $.ajax({
    url: "{!! url('suratjalanlistbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: noso

    },
    success: function(res) {
      console.log(res)
      if (!res.length) {
        alertify.warning("Tidak ada barang untuk ditambah")
        return
      }
      listAddBarangKoreksi = res
      let rowListBarangKoreksi =  ``

      listAddBarangKoreksi.forEach((item, i) => {
        rowListBarangKoreksi += `
          <tr>
          <td class="text-center">
            <button class="btn btn-primary btn-sm" type="button" onclick="buttonKoreksiPickBarang(${i})"><i class="bi bi-plus"></i></button>
          </td>
            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>

            <td class="text-right">${parseFloat(item.QntOut).toFixed(2) }</td>
            <td>${item.SATUAN}</td>

          </tr>
        `
      });
      document.getElementById(`tabel_data_koreksi_list_barang`).innerHTML = rowListBarangKoreksi





      $('.showhidemodalkoreksi').hide();
      $('#modalKoreksiListBarang').show();
      $("#formKoreksi").modal('toggle')
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}

function buttonKoreksiPickBarang (index ) {
  // tipeformitem

dataKoreksiAdd = listAddBarangKoreksi[index]

  document.getElementById(`Add${tipeformitem}KodeBrg`).value = dataKoreksiAdd.KodeBrg
  document.getElementById(`Add${tipeformitem}NamaBrg`).value = dataKoreksiAdd.NamaBrg

  let tempIsi = ''
  let tempSat = ''

  if (dataKoreksiAdd.NOSAT == 3) {
    tempIsi = dataKoreksiAdd.ISI3
    tempSat = dataKoreksiAdd.Sat3
  } else if (dataKoreksiAdd.NOSAT == 2) {
    tempIsi = dataKoreksiAdd.ISI2
    tempSat = dataKoreksiAdd.Sat2
  } else {
    tempIsi = dataKoreksiAdd.ISI1
    tempSat = dataKoreksiAdd.Sat1
  }

  document.getElementById(`Add${tipeformitem}InputIsi`).value = tempIsi
  document.getElementById(`AddAddInputSatuan1`).value = dataKoreksiAdd.Sat1
  document.getElementById(`Add${tipeformitem}InputSatuan`).value = tempSat
  document.getElementById(`Add${tipeformitem}InputQty`).value = parseFloat(dataKoreksiAdd.QntOut).toFixed(2)
  $('.showhidemodalkoreksi').hide();
  $('#modalKoreksiMain').show();
  // document.getElementById(`Add${tipeformitem}KodeBrg`).scrollIntoView();
  $("#formKoreksi").modal('toggle')

}

function buttonKoreksiPickGudang (kode , nama ) {
  // tipeformitem
  document.getElementById(`Add${tipeformitem}KodeGdg`).value = kode
  document.getElementById(`Add${tipeformitem}NamaGdg`).value = nama
  $('.showhidemodalkoreksi').hide();
  $('#modalKoreksiMain').show();
  document.getElementById(`Add${tipeformitem}NamaGdg`).scrollIntoView();
  $("#formKoreksi").modal('toggle')
}

function buttonKoreksiListGudang () {
  $('.showhidemodalkoreksi').hide();
  $('#modalKoreksiListGudang').show();
  $("#formKoreksi").modal('toggle')
}

function buttonAddListEkspedisi () {
  // $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListEkspedisi').show();
  // // showhidemodalfooteradd
  // $('.showhidemodalfooteradd').hide();
  // $('#modalBodyFooterList').show();


  $.ajax({
    url: "{!! url('suratjalanlistekspedisi') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickEkspedisi('${item.KODECUSTSUPP}' , '${item.NAMACUSTSUPP}'  )" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_ekspedisi").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListEkspedisi').show();
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
// input_add_kodegdg

function submitAddAdd () {
  console.log('submitAddAdd')
  let choice ="I"
  let _token = $("#_token").val();
  console.log(dataTableKoreksiHeader)
  console.log(dataTableKoreksi)
  console.log(dataKoreksiAdd)



  let NamaBrg = $("#AddAddNamaBrg").val();
  let KodeBrg = $("#AddAddKodeBrg").val();
  let KodeGdg = $("#AddAddKodeGdg").val();
  let NamaGdg = $("#AddAddNamaGdg").val();
  let Satuan = $("#AddAddInputSatuan").val();
  let Qty = $("#AddAddInputQty").val();
  let Isi = $("#AddAddInputIsi").val();
  let nobukti = $("#input_koreksi_nobukti").val();
  let sat1 =  $("#AddAddInputSatuan1").val();

  if (Number(Qty) > Number(dataKoreksiAdd.QntOut)) {
    alertify.warning('Qty melebihi QntOut')
    return
  }
  console.log( NamaBrg , KodeBrg )
  console.log( NamaGdg , KodeGdg )
  console.log( Qty , Satuan )


  if (!KodeGdg || !KodeBrg || !Satuan || Number(Qty) < 0) {
    alertify.warning("Input tidak lengkap")
    return
  }

  console.log({
    choice,
    nobukti: nobukti,
    nourut: dataTableKoreksiHeader[0].nourut,
    // urut: 0,
    tanggal: dataTableKoreksiHeader[0].TANGGAL,
    nospp: '',
    kodecustsupp: dataTableKoreksiHeader[0].KODECUSTSUPP,
    container: null,
    nocontainer: null,
    noseal: null,
    catatan: dataTableKoreksiHeader[0].Catatan,
    urut: 0,
    urutspp: 0,
    kodebarang: KodeBrg,
    qnt: Qty * Isi,
    qnt2: Qty,
    sat1: sat1,
    sat2: Satuan,
    isi: Isi,
    netw: null,
    grossw: null,
    namabarang: '',
    sopir: dataTableKoreksiHeader[0].SOPIR,
    kodegdg: KodeGdg,
    kodeexp :  dataTableKoreksiHeader[0].KODEEXP,
    noresi: '',
    jumlahtagihan: 0,
    flagtipe: 1,
    nobatch: '-' ,
    noso: dataKoreksiAdd.NOSO ,
    urutso: dataKoreksiAdd.URUTSO,
    satx: ''

  })
  // return

    $.ajax({
      url: "{!! url('suratjalanspkoreksi') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        nobukti: nobukti,
        nourut: dataTableKoreksiHeader[0].nourut,
        // urut: 0,
        tanggal: dataTableKoreksiHeader[0].TANGGAL,
        nospp: '',
        kodecustsupp: dataTableKoreksiHeader[0].KODECUSTSUPP,
        container: null,
        nocontainer: null,
        noseal: null,
        catatan: dataTableKoreksiHeader[0].Catatan,
        urut: 0,
        urutspp: 0,
        kodebarang: KodeBrg,
        qnt: Qty * Isi,
        qnt2: Qty,
        sat1: sat1,
        sat2: Satuan,
        nosat: dataKoreksiAdd.NOSAT,
        isi: Isi,
        netw: null,
        grossw: null,
        namabarang: '',
        sopir: dataTableKoreksiHeader[0].SOPIR,
        kodegdg: KodeGdg,
        kodeexp :  dataTableKoreksiHeader[0].KODEEXP,
        noresi: '',
        jumlahtagihan: 0,
        flagtipe: 1,
        nobatch: '-' ,
        noso: dataKoreksiAdd.NOSO ,
        urutso: dataKoreksiAdd.URUTSO,
        satx: ''

      },
      success: function(res) {
        console.log(res ,'!')
        // loadAll()
        if(res == 1) {
          loadAll()
          refreshDataTableKoreksi(nobukti)
          $(".showhide").hide()
          // $("#form").modal('toggle')
          alertify.success('Item telah ditambah');
          // console.log('before ===========')
          // loadAll()
          // console.log("after ===========")
        }

        if (res == 2) {
          // setNewNoBukti()
          // alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }

        if (res == 3 ) {
          // alertify.warning('Stok gudang tidak mencukupi');
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })



}

function submitAddEdit () {
  console.log('submitAddEdit')
  let _token = $("#_token").val();
  console.log(dataTableKoreksiHeader)
  console.log(dataTableKoreksi)

  let NamaBrg = $("#AddEditNamaBrg").val();
  let KodeBrg = $("#AddEditKodeBrg").val();
  let KodeGdg = $("#AddEditKodeGdg").val();
  let NamaGdg = $("#AddEditNamaGdg").val();
  let Satuan = $("#AddEditInputSatuan").val();

  let sat1 = $("#AddEditInputSatuan1").val();
  let choice = "U"
  let Qty = $("#AddEditInputQty").val();
  console.log(NamaBrg , KodeBrg)
  console.log(NamaGdg , KodeGdg)
  console.log(Qty , Satuan)

  console.log({
    choice,
    nobukti: dataKoreksiEdit.NOBUKTI,
    nourut: dataTableKoreksiHeader[0].nourut,
    urut: dataKoreksiEdit.URUT,
    tanggal: dataTableKoreksiHeader[0].TANGGAL,
    nospp: dataKoreksiEdit.NoSC,
    kodecustsupp: dataTableKoreksiHeader[0].KODECUSTSUPP,
    container: null,
    nocontainer: null,
    noseal: null,
    catatan: dataTableKoreksiHeader[0].Catatan,
    kodebarang: dataKoreksiEdit.KODEBRG,
    qnt: Qty * dataKoreksiEdit.ISI,
    qnt2: Qty,
    sat1: sat1,
    sat2: Satuan,
    isi: dataKoreksiEdit.ISI,
    nosat: dataKoreksiEdit.nosat,
    netw: null,
    grossw: null,
    namabarang: dataKoreksiEdit.namabrgx,
    sopir: dataTableKoreksiHeader[0].SOPIR,
    kodegdg: KodeGdg,
    kodeexp :  dataTableKoreksiHeader[0].KODEEXP,
    noresi: '',
    jumlahtagihan: 0,
    flagtipe: 1,
    nobatch: '-' ,
    noso: dataKoreksiEdit.NOSO ,
    urutso: dataKoreksiEdit.URUTSO,
    satx: dataKoreksiEdit.SATX

  })
  // return
  $.ajax({
    url: "{!! url('suratjalanspkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nobukti: dataKoreksiEdit.NOBUKTI,
      nourut: dataTableKoreksiHeader[0].nourut,
      urut: dataKoreksiEdit.URUT,
      tanggal: dataTableKoreksiHeader[0].TANGGAL,
      nospp: dataKoreksiEdit.NoSC,
      kodecustsupp: dataTableKoreksiHeader[0].KODECUSTSUPP,
      container: null,
      nocontainer: null,
      noseal: null,
      catatan: dataTableKoreksiHeader[0].Catatan,
      urut: dataKoreksiEdit.URUT,
      kodebarang: dataKoreksiEdit.KODEBRG,
      qnt: Qty * dataKoreksiEdit.ISI,
      qnt2: Qty,
      sat1: sat1,
      sat2: Satuan,
      isi: dataKoreksiEdit.ISI,

      nosat: dataKoreksiEdit.nosat,
      netw: null,
      grossw: null,
      namabarang: dataKoreksiEdit.namabrgx,
      sopir: dataTableKoreksiHeader[0].SOPIR,
      kodegdg: KodeGdg,
      kodeexp :  dataTableKoreksiHeader[0].KODEEXP,
      noresi: '',
      jumlahtagihan: 0,
      flagtipe: 1,
      nobatch: '-' ,
      noso: dataKoreksiEdit.NOSO ,
      urutso: dataKoreksiEdit.URUTSO,
      satx: dataKoreksiEdit.SATX

    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res == 1) {

        loadAll()
        refreshDataTableKoreksi(dataKoreksiEdit.NOBUKTI)
        $(".showhide").hide()
        alertify.success('Item telah diedit');
        // alertify.success('SPB telah ditambah');
        // console.log('before ===========')
        // loadAll()
        // console.log("after ===========")
      }

      if (res == 2) {
        // setNewNoBukti()
        // alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      }

      if (res == 3 ) {
        // alertify.warning('Stok gudang tidak mencukupi');
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function buttonAddPickGudang (kode, nama) {
  console.log('buttonAddPickGudang')
  console.log(kode, nama)
  // if (tipeform == 'edit') {
  //   onChangeHeader('KODEVLS' , kode)
  //   onChangeHeader('KURS' , kurs)
  // }
  document.getElementById("input_add_kodegdg").value = kode
  document.getElementById("input_add_gdg").value = nama
  // buttonAddListBatal()
  $("#form").modal('toggle')
}

function buttonAddPickEkspedisi (kode, nama) {
  console.log('buttonAddPickEkspedisi')
  console.log(kode, nama)
  // if (tipeform == 'edit') {
  //   onChangeHeader('KODEVLS' , kode)
  //   onChangeHeader('KURS' , kurs)
  // }
  document.getElementById("input_add_kodeekspedisi").value = kode
  document.getElementById("input_add_ekspedisi").value = nama
  buttonAddListBatal()
  $("#form").modal('toggle')
}


function buttonAddListGudang () {


  // return

  $.ajax({
    url: "{!! url('suratjalanlistgudang') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickGudang('${item.kodegdg}' , '${item.nama}'  )" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.kodegdg}</td>
        <td>${item.nama}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListGudang').show();
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



function loadAll () {
  console.log('loadall tes')
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('suratjalanloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      dataRefreshOutstanding = res.tempOutstanding
      dataRefreshOutstanding2 = res.tempOutstanding2

      dataRefreshOutstanding3 = res.tempOutstanding3
      dataRefreshOutstanding4 = res.tempOutstanding4
      dataRefreshOutstanding5 = res.tempOutstanding5
      dataRefreshOutstanding6 = res.tempOutstanding6


    }})

  lastTabelRows = dataRefreshOutstanding
  reinitTabel()

  lastTabel2Rows = dataRefreshOutstanding2
  reinitTabel2()

  lastTabel3Rows = dataRefreshOutstanding3
  reinitTabel3()

  lastTabel6Rows = dataRefreshOutstanding6
  reinitTabel6()

          $('#tabel4').DataTable().destroy();

          let rowTable4 = ""
          dataRefreshOutstanding4.forEach((item, i) => {




            rowTable4 += `
            <tr>
              <td>${ item.NOBUKTI }</td>
              <td>${ formatDate(item.Tanggal , '/')}</td>
              <td>${ item.NamaCustSupp }</td>
              <td>${ item.NamaBrg }</td>
              <td>${ item.RefPR }</td>
              <td>${ item.Nopesanan }</td>
              <td class="text-right">${ parseFloat(item.Qnt).toFixed(2) }</td>
              <td class="text-right">${ parseFloat(item.QntOut).toFixed(2) }</td>
              <td>${ item.SATUAN }</td>
              <td class="text-right">${ parseFloat(item.SaldoQnt).toFixed(2) }</td>
              <td>${ item.catatan }</td>
              <td>${ formatDate(item.DUEDATE , '/')}</td>
              <td>${ item.namakebun }</td>
              <td>${ item.PartNumber }</td>
              <td>${ item.NamaMerk }</td>
              <td>${ item.UserID }</td>

            </tr>
            `



          });

          document.getElementById("tabel4_data").innerHTML = rowTable4
          $("#tabel4").DataTable({
            "lengthChange": false,
              "paging": false ,

            });



            lastTabel5Rows = dataRefreshOutstanding5
            reinitTabel5()

}

function buttonAddListBatal () {
  // $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();
  //
  // $('.showhidemodalfooteradd').hide();
  // $('#modalBodyFooterMain').show();


  $("#form").modal('toggle')

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

function cleanFormAdd () {
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_noso").value = ''
  document.getElementById("input_add_tanggal").value = formatDate(new Date())
  document.getElementById("input_add_kodegdg").value = ''
  document.getElementById("input_add_gdg").value = ''
  document.getElementById("input_add_nopol").value = ''
  document.getElementById("input_add_sopir").value = ''
  document.getElementById("input_add_refukm").value = ''
  document.getElementById("input_add_catatanso").value = ''
  document.getElementById("input_add_kodeekspedisi").value = ''
  document.getElementById("input_add_ekspedisi").value = ''


}

function refreshDataTableKoreksi (NOBUKTI) {
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('suratjalangetdetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      // if (res.header[0].IsOtorisasi1 == 1) {
      //
      //   alertify.warning('Nobukti sudah di otorisasi')
      //   return
      // }

      if (!res.detail.length) {
        // $("#formKoreksi").modal('toggle')
        buttonCloseForm()
        alertify.warning("Data habis")
        return
      }

      dataTableKoreksiHeader = res.header
      dataTableKoreksi = res.detail

      document.getElementById("input_koreksi_catatanso").value = res.header[0].Catatan
      document.getElementById("input_koreksi_nobukti").value = res.header[0].NOBUKTI

      document.getElementById("input_koreksi_nourut").value = res.header[0].nourut
      document.getElementById("input_koreksi_customer").value = res.header[0].NamaCustSupp
      document.getElementById("input_koreksi_noso").value = res.header[0].NOSOT
      document.getElementById("input_koreksi_ekspedisi").value = res.header[0].KODEEXP
      document.getElementById("input_koreksi_nopol").value = res.header[0].NoPolKend
      document.getElementById("input_koreksi_alamatkirim").value =  res.header[0].AlamatKirimX
      document.getElementById("input_koreksi_gdg").value = '-'
      document.getElementById("input_koreksi_lokasipenerima").value =  res.header[0].NamaKebunX
      document.getElementById("input_koreksi_sopir").value = res.header[0].SOPIR
      document.getElementById("input_koreksi_refukm").value = res.header[0].RefUKM


      let rowTableKoreksi = ``
      res.detail.forEach((item, i) => {
        rowTableKoreksi += `
        <tr class="text-left">
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.namabrgx ? item.namabrgx : ''}</td>
          <td class="text-right">${parseFloat(item.QNT).toFixed(2)}</td>
          <td>${item.SAT_1}</td>
          <td class="text-right">${parseFloat(item.QNT2).toFixed(2)}</td>
          <td>${item.SAT_2}</td>

          <td>${item.NAMAGDG}</td>
          <td class='text-center'>
            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksiEdit('${item.URUT}' , ${i})"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete('${item.URUT}' , ${i})"><i class="bi bi-trash"></i></button>


          </td>
        </tr>
        `

      });


      document.getElementById("koreksiTableData").innerHTML = rowTableKoreksi




      // $("#formKoreksi").modal('toggle')




    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonKoreksiAdd () {
  console.log('buttonKoreksiAdd')
    tipeformitem = 'Add'
    document.getElementById("AddAddKodeBrg").value = ''
    document.getElementById("AddAddNamaBrg").value = ''
    document.getElementById("AddAddKodeGdg").value = ''
    document.getElementById("AddAddNamaGdg").value = ''
    document.getElementById("AddAddInputSatuan").value = ''
    document.getElementById("AddAddInputQty").value = '0.00'
    $('.showhide').hide();
    $('#formAddAdd').show();

}

function buttonBatalAdd () {

    $('.showhide').hide();
}

function buttonKoreksiDelete (urut, index) {
  console.log('buttonKoreksiDelete')
  let tempData = dataTableKoreksi[index]
  console.log(tempData)
  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + tempData.NAMABRG + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"





        $.ajax({
          url: "{!! url('suratjalanspkoreksi') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            nobukti: tempData.NOBUKTI,
            nourut: '',
            urut: tempData.URUT,
            tanggal: '',
            nospp: tempData.NoSC,
            kodecustsupp: '',
            container: null,
            nocontainer: null,
            noseal: null,
            catatan: '',
            urut: tempData.URUT,
            kodebarang: tempData.KODEBRG,
            qnt: 0,
            qnt2: 0,
            sat1: '',
            sat2: '',
            isi: tempData.ISI,

            nosat: tempData.nosat,
            netw: null,
            grossw: null,
            namabarang: tempData.namabrgx,
            sopir: dataTableKoreksiHeader[0].SOPIR,
            kodegdg: '',
            kodeexp :  dataTableKoreksiHeader[0].KODEEXP,
            noresi: '',
            jumlahtagihan: 0,
            flagtipe: 1,
            nobatch: '-' ,
            noso: tempData.NOSO ,
            urutso: tempData.URUTSO,
            satx: tempData.SATX

          },
          success: function(res) {
            console.log('res', res)
            refreshDataTableKoreksi(tempData.NOBUKTI)
            loadAll()

            // lockFormAdd()
            $('.showhide').hide();
            // refreshDataTableAdd(nobukti)

            alertify.success('Berhasil menghapus item')

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

function buttonKoreksiEdit (urut , index) {
  console.log('buttonKoreksiEdit')
    tipeformitem = 'Edit'
    console.log(dataTableKoreksi[index])
    dataKoreksiEdit = dataTableKoreksi[index]
    document.getElementById("AddEditKodeBrg").value = dataKoreksiEdit.KODEBRG
    document.getElementById("AddEditNamaBrg").value = dataKoreksiEdit.NAMABRG

    document.getElementById("AddEditKodeGdg").value = dataKoreksiEdit.KODEGDG
    document.getElementById("AddEditNamaGdg").value = dataKoreksiEdit.NAMAGDG
    document.getElementById("AddEditInputSatuan1").value = dataKoreksiEdit.SAT_1

    document.getElementById("AddEditInputQty").value = dataKoreksiEdit.QNT2 ? parseFloat(dataKoreksiEdit.QNT2).toFixed(2) : '0.00'
    document.getElementById("AddEditInputSatuan").value = dataKoreksiEdit.Satuan
    // document.getElementById("AddEditInputIsi").value = dataKoreksiEdit.ISI
    $('.showhide').hide();
    $('#formAddEdit').show();
}


function buttonDetailSPB (NOBUKTI) {

  tipeform = 'edit'

  // modalKoreksiMain
  // $('#modalKoreksiMain').show();
  console.log('buttonDetailSPB' , NOBUKTI)
  let dataTableDetail = []
  let dataTableDetailHeader = []
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('suratjalangetdetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      dataTableDetailHeader = res.header
      dataTableDetail = res.detail
      document.getElementById("input_koreksi_catatanso").value = res.header[0].Catatan
      document.getElementById("input_koreksi_nobukti").value = res.header[0].NOBUKTI
      document.getElementById("input_koreksi_customer").value = res.header[0].NamaCustSupp
      document.getElementById("input_koreksi_noso").value = res.header[0].NOSOT
      document.getElementById("input_koreksi_ekspedisi").value = res.header[0].KODEEXP
      document.getElementById("input_koreksi_nopol").value = res.header[0].NoPolKend
      document.getElementById("input_koreksi_alamatkirim").value =  res.header[0].AlamatKirimX
      document.getElementById("input_koreksi_gdg").value = '-'
      document.getElementById("input_koreksi_lokasipenerima").value =  res.header[0].NamaKebunX

      document.getElementById("input_koreksi_sopir").value = res.header[0].SOPIR
      document.getElementById("input_koreksi_refukm").value = res.header[0].RefUKM


      let rowTableKoreksi = ``
      res.detail.forEach((item, i) => {
        rowTableKoreksi += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td>${item.namabrgx ? item.namabrgx : ''}</td>
            <td class="text-right">${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>
            <td class="text-right">${parseFloat(item.QNT2).toFixed(2)}</td>
            <td>${item.SAT_2}</td>

            <td>${item.NAMAGDG}</td>
          </tr>
        `

      });


      document.getElementById("detailTableData").innerHTML = rowTableKoreksi

      // $("#formKoreksi").modal('toggle')

      if (res.detail.length) {

        $('#page1').hide();
        $('#page4').show();

      }



    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}

function buttonKoreksiSPB (NOBUKTI) {

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  tipeform = 'edit'

  // $('.showhidemodalkoreksi').hide();
  $('.showhide').hide();
  // modalKoreksiMain
  // $('#modalKoreksiMain').show();
  console.log('buttonKoreksiSPB' , NOBUKTI)
  dataTableKoreksi = []
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('suratjalangetdetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      if (res.header[0].IsOtorisasi1 == 1) {

        alertify.warning('Nobukti sudah di otorisasi')
        return
      }
      dataTableKoreksiHeader = res.header
      dataTableKoreksi = res.detail
      document.getElementById("input_koreksi_catatanso").value = res.header[0].Catatan
      document.getElementById("input_koreksi_nobukti").value = res.header[0].NOBUKTI
      document.getElementById("input_koreksi_customer").value = res.header[0].NamaCustSupp
      document.getElementById("input_koreksi_noso").value = res.header[0].NOSOT
      document.getElementById("input_koreksi_ekspedisi").value = res.header[0].KODEEXP
      document.getElementById("input_koreksi_nopol").value = res.header[0].NoPolKend
      document.getElementById("input_koreksi_alamatkirim").value =  res.header[0].AlamatKirimX
      document.getElementById("input_koreksi_gdg").value = '-'
      document.getElementById("input_koreksi_lokasipenerima").value =  res.header[0].NamaKebunX

      document.getElementById("input_koreksi_sopir").value = res.header[0].SOPIR
      document.getElementById("input_koreksi_refukm").value = res.header[0].RefUKM


      let rowTableKoreksi = ``
      res.detail.forEach((item, i) => {
        rowTableKoreksi += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td>${item.namabrgx ? item.namabrgx : ''}</td>
            <td class="text-right">${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>
            <td class="text-right">${parseFloat(item.QNT2).toFixed(2)}</td>
            <td>${item.SAT_2}</td>

            <td>${item.NAMAGDG}</td>
            <td class='text-center'>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksiEdit('${item.URUT}' , ${i})"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete('${item.URUT}' , ${i})"><i class="bi bi-trash"></i></button>


            </td>
          </tr>
        `

      });


      document.getElementById("koreksiTableData").innerHTML = rowTableKoreksi

      // $("#formKoreksi").modal('toggle')

      if (res.detail.length) {

        $('#page1').hide();
        $('#page3').show();

      }



    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}


// function buttonAdd (NOBUKTI) {
//   cleanFormAdd()
//   let akses = $("#akses_istambah").val();
//
//   if (!Number(akses)) {
//     alertify.warning('No access')
//     return
//   }
//   refreshDataTableAdd(NOBUKTI)
//   // $('.showhidemodalbodyadd').hide();
//   // $('#modalBodyAddMain').show();
//   // $('.showhidemodalfooteradd').hide();
//   // $('#modalBodyFooterMain').show();
//   document.getElementById("input_add_kodegdg").value = ''
//   document.getElementById("input_add_kodeekspedisi").value = ''
//   document.getElementById("input_add_noso").value = NOBUKTI
//   setNewNoBukti()
//
//   // $("#form").modal('toggle')
//   $('#page1').hide();
//   $('#page2').show();
//   return
//   // lockFormAdd()
// }


function buttonAdd (NOBUKTI) {
  tipeform = 'add'
  cleanFormAdd()
  dataTableAdd = []
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  refreshDataTableAdd(NOBUKTI)
  // $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();
  // $('.showhidemodalfooteradd').hide();
  // $('#modalBodyFooterMain').show();
  document.getElementById("input_add_kodegdg").value = ''
  document.getElementById("input_add_kodeekspedisi").value = ''
  document.getElementById("input_add_noso").value = NOBUKTI


  if (dataTableAdd.length) {


      setNewNoBukti(Number(dataTableAddHeader[0].TipePPN))

    $('#page1').hide();
    $('#page2').show();
  }
  // $("#form").modal('toggle')
  return
  // lockFormAdd()
}

function buttonCloseForm () {
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

}






function setNewNoBukti (xppn) {
  $.ajax({
    url: "{!! url('suratjalanspnobukti') !!}",
    type: "get",
    async: false,
    data: {
      ppn: Number(xppn)
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


function buttonOtorisasiSPB (NOBUKTI) {
  console.log('buttonOtorisasiSPB' , NOBUKTI)

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Otorisasi', 'Otorisasi SPB ' + NOBUKTI + ' ?',
      function() {
        let _token = $("#_token").val();



        $.ajax({
          url: "{!! url('suratjalanspotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            NOBUKTI

          },
          success: function(res) {
            console.log('!', res)
            loadAll()

            // lockFormAdd()

            alertify.success('Berhasil Otorisasi SPB')

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


function buttonBatalOtorisasiSPB (NOBUKTI) {
  console.log('buttonBatalOtorisasiSPB' , NOBUKTI)

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi SPB ' + NOBUKTI + ' ?',
      function() {
        let _token = $("#_token").val();



        $.ajax({
          url: "{!! url('suratjalanspbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            NOBUKTI

          },
          success: function(res) {
            console.log('!', res)
            loadAll()

            // lockFormAdd()

            alertify.success('Berhasil Batal Otorisasi SPB')

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




function submitAdd () {

  console.log('Submit Add')
    let _token = $("#_token").val();
    let tempData = []

    // console.log('TES ==========')
    // return
    let qtyCheck = false

    let kodegdg = document.getElementById("input_add_kodegdg").value

    let kodeekspedisi = document.getElementById("input_add_kodeekspedisi").value
    let nobukti = document.getElementById("input_add_nobukti").value
    let nourut = document.getElementById("input_add_nourut").value
    let noso = document.getElementById("input_add_noso").value
    let tanggal = document.getElementById("input_add_tanggal").value
    let nopol = document.getElementById("input_add_nopol").value
    let sopir = document.getElementById("input_add_sopir").value
    let refukm = document.getElementById("input_add_refukm").value
    let alamatkirim = document.getElementById("input_add_alamatkirim").value
    let kodekebun = dataTableAdd[0].kodekebun
    let lokasipenerima = document.getElementById("input_add_lokasipenerima").value
    let customer = document.getElementById("input_add_customer").value
    let catatanso = document.getElementById("input_add_catatanso").value

    if (!kodegdg) {
      // console.log(qtyCheck, 'true')
      // qtyCheck = false
      alertify.warning("Gdg harus diisi");
      return
    }

    dataTableAdd.forEach((item, i) => {
      if (document.getElementById(`add_checkbox${i}`).checked) {
        dataTableAdd[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
        if (Number(dataTableAdd[i].inputQntTerima) > Number(dataTableAdd[i].QntSisa)) {
          qtyCheck = true
        }
        tempData.push(dataTableAdd[i])
      }
    });
    console.log('qwer')
    // console.log(tempData)
    // console.log(qtyCheck)
    if (qtyCheck) {
      // console.log(qtyCheck, 'true')
      // qtyCheck = false
      alertify.warning("Qty SPB tidak bisa lebih besar dari Qty Sisa");
      return
    }
    // return

    if (!tempData.length) {
      alertify.warning("Tidak ada item dipilih");
      return
    }
    let flag = false
    tempData.forEach((item, i) => {
      console.log(item , '==================')
      console.log(Number(item.inputQntTerima) ,Number(item.QntSisa) )
      if (Number(item.inputQntTerima) > Number(item.QntSisa)) {
        console.log('os')

        // return
        flag =true
      }
      if (Number(item.inputQntTerima) <= 0) {
        console.log('negatif')

        // return
        flag =true
      }
    });
    if (flag) {
      alertify.warning("Qty tidak bisa negatif / melebihi OS");
      return
    }
    let inputDate = $("#input_add_tanggal").val();
    // let nosj = $(`#input_add_nosj`).val();
    // let norspb = $(`#input_add_norspb`).val();
    // let nourut =  $(`#input_add_noUrut`).val();
    // let nopolkendaraan =  $(`#input_add_nopolkendaraan`).val();
    // let expedisi = document.getElementById("input_add_expedisi").value;
    // let lokasiterima =  $(`#input_add_lokasiterima`).val();


    let checkDate = new Date(inputDate)

    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value
    console.log('asd')

    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

        alertify.warning("Tanggal tidak sesuai periode");
        return
    }

    console.log(tempData)

    console.log(
      {
          kodegdg,
          kodeekspedisi,
          nobukti,
          nourut,
          noso,
          tanggal,
          nopol,
          sopir,
          refukm,
          alamatkirim,
          lokasipenerima,
          customer,
          catatanso,
          tempData
      }
    )





    // return

    $.ajax({
      url: "{!! url('suratjalanspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,

            kodegdg,
            kodeekspedisi,
            nobukti,
            nourut,
            noso,
            tanggal,
            nopol,
            sopir,
            refukm,
            alamatkirim,
            lokasipenerima,
            customer,
            catatanso,
            tempData,
            noresi: '',
            jumlah: 0,
            jmlrecord: 0,
            kodekebun

      },
      success: function(res) {
        console.log(res ,'!')
        // loadAll()
        if(res == 1) {
          tipeform = 'edit'
          loadAll()
          // $("#form").modal('toggle')
          alertify.success('SPB telah ditambah');
          buttonCloseForm()
          // console.log('before ===========')
          // loadAll()
          // console.log("after ===========")
        }

        if (res == 2) {
          setNewNoBukti(Number(dataTableAddHeader[0].TipePPN))
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }

        if (res == 3 ) {
          // alertify.warning('Stok gudang tidak mencukupi');
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}



function buttonDetail (NOBUKTI) {
  console.log('buttonDetail' , NOBUKTI)
  $('.showhide').hide();
  $('.showhidemodalbodydetailmain').hide();

  let _token  = $("#_token").val()


  $.ajax({
    url: "{!! url('sogetdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      // console.log('aaa')
      console.log('res' , res)

      // res.header.forEach((item, i) => {
      //   console.log('a' , i)
      // });
      //
      // res.list.forEach((item, i) => {
      //   console.log('b' , i)
      // });

      if (!res.list) {
        alertify.warning("Data habis")
        // $("#form").modal('toggle')
        return
      } else {
        let dataHeaderDetail = res.header[0]
        let dataTableDetail = res.list

        let rowTable = ""
        dataTableDetail.forEach((item, i) => {
          rowTable += `<tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-right">${item.Qnt ? parseFloat(item.Qnt).toFixed(2) : '0.00'}</td>
          <td>${item.Satuan}</td>
          <td class="text-right">${item.Harga ? parseFloat(item.Harga).toFixed(2) : '0.00'}</td>
          <td class="text-right">${item.DiscRp1 ? parseFloat(item.DiscRp1).toFixed(2) : '0.00'}</td>
          <td class="text-right">${item.NDPP ? parseFloat(item.NDPP).toFixed(2) : '0.00'}</td>

          </tr>`
        });

        if(!dataTableDetail.length) {
          rowTable = `<tr>
          <td class="text-center" colspan="5">Belum ada barang</td>
          </tr>`
        }
      }

      $('.showhidemodalbodydetail').hide();
      // $('#modalBodyAddListPelanggan').show();
      $('#modalBodyDetailMain').show();
      // setNewNoBukti()

      // refreshDataTableAdd()
      $("#formDetail").modal('toggle')



    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })




}




function refreshDataTableAdd (NOBUKTI = "") {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {


    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="7">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()


    $.ajax({
      url: "{!! url('suratjalangetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        // console.log('aaa')
        console.log('res' , res)

        // res.header.forEach((item, i) => {
        //   console.log('a' , i)
        // });
        if (!res.detail.length) {
          alertify.warning("Data tidak ditemukan")


        } else {
          dataTableAddHeader = res.header
          dataTableAdd = res.detail

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {
            rowTable += `<tr>
            <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
            <td class='text-left' >${item.KODEBRG}</td>
            <td class='text-left'>${item.NAMABRG}</td>
            <td class='text-left'>${item.SATUAN}</td>
            <td class='text-right'>${parseFloat(item.QNT).toFixed(2)}</td>
            <td class='text-right'>${parseFloat(item.QntSisa).toFixed(2)}</td>
            <td class='text-right'>${ Number(item.QntBooking) ? parseFloat(item.QntBooking).toFixed(2) : '0.00'}</td>
            <td class="text-center"><input onchange="" id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=${parseFloat(item.QntSisa).toFixed(2)}></td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="5">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("addTableData").innerHTML = rowTable
          document.getElementById("input_add_customer").value = res.header[0].NamaCustSupp
          document.getElementById("input_add_alamatkirim").value = res.header[0].ALAMAT

          document.getElementById("input_add_lokasipenerima").value = res.header[0].AlamatLokasi


        }



      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })




  }


}


function submitKirimTerima () {

  console.log('Submit KirimTerima')

    let _token = $("#_token").val();
    let tanggalKirim = document.getElementById("input_add_tanggalKirim").value
    let tanggalTerimaBarang = document.getElementById("input_add_tanggalTerimaBarang").value
    let tanggalTerima = document.getElementById("input_add_tanggalTerima").value
  
    console.log(
      {
          tanggalKirim, tanggalTerimaBarang, tanggalTerima
      }
    )

    // return

    $.ajax({
      url: "{!! url('suratJalanAddKirimTerima') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
            kodeInvoiceTemp,
            tanggalKirim,
            tanggalTerimaBarang,
            tanggalTerima

      },
      success: function(res) {
          loadAll()
          

  $("#formKirimTerima").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}


function submitKirimTerimaAcc () {

  console.log('Submit KirimTerima')

    let _token = $("#_token").val();
    let tanggalKirimAcc = document.getElementById("input_add_tanggalKirimAcc").value
  
    console.log(
      {
          tanggalKirimAcc
      }
    )

    // return

    $.ajax({
      url: "{!! url('suratJalanAddKirimTerimaAcc') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
            kodeInvoiceTemp,
            tanggalKirimAcc

      },
      success: function(res) {
          loadAll()
          

  $("#formKirimTerimaAcc").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}


</script>


<script>
  // const tabHome = document.getElementById('nav-home-tab');
  // const tabProfile = document.getElementById('nav-profile-tab');

  //function setActiveTab(idNav) {
    //$(".nav-item").css("background-color", "#f8f9fa");
    //$(".nav-item").css("color", "#007bff");
    //console.log(idNav)
    //document.getElementById(idNav).style.backgroundColor = '#007bff';
    //document.getElementById(idNav).style.color = '#fff';

  //}

  // Default warna tab
  //setActiveTab("nav-home-tab");

  // buat ganti tab
  //document.getElementById('nav-home-tab').addEventListener('click', function () {
    //setActiveTab("nav-home-tab");
  //});

  //document.getElementById('nav-profile-tab').addEventListener('click', function () {
    //setActiveTab("nav-profile-tab");
  //});

  //document.getElementById('nav-profile1-tab').addEventListener('click', function () {
    //setActiveTab("nav-profile1-tab");
  //});

  //document.getElementById('nav-profile2-tab').addEventListener('click', function () {
    //setActiveTab("nav-profile2-tab");
  //});

  //document.getElementById('nav-profile3-tab').addEventListener('click', function () {
    //setActiveTab("nav-profile3-tab");
  //});
  //document.getElementById('nav-profile4-tab').addEventListener('click', function () {
    //setActiveTab("nav-profile4-tab");
  //});

function setActiveTab(idNav) {
  // reset semua tab
  $(".nav-link").css({
    "background-color": "#f8f9fa",
    "color": "#007bff",
    "border": "2px solid #007bff"
  });

  // aktifkan tab yang diklik
  $("#" + idNav).css({
    "background-color": "#007bff",
    "color": "#fff"
  });
}

// default tab aktif
setActiveTab("nav-home-tab");

$(".nav-link").on("click", function () {
  let id = $(this).attr("id");
  setActiveTab(id);
});

let kodeInvoiceTemp = ''
let kodeCustTemp = ''

function buttonKirimTerima(kodeInvoice, kodeCust, tglKirim, tglTerimaBarang, tglTerima){

  kodeCustTemp = kodeCust;
  kodeInvoiceTemp = kodeInvoice;

  document.getElementById('titleInvoice').innerHTML = kodeInvoice;
  document.getElementById('titleSupplier').innerHTML = kodeCust;

  document.getElementById('input_add_tanggalKirim').value = formatTanggal(tglKirim)
  document.getElementById('input_add_tanggalTerimaBarang').value = formatTanggal(tglTerimaBarang)
  document.getElementById('input_add_tanggalTerima').value = formatTanggal(tglTerima)
  

  $("#formKirimTerima").modal('toggle')

}

function buttonTerimaAcc (kodeInvoice, kodeCust, tglAcc){

  kodeCustTemp = kodeCust;
  kodeInvoiceTemp = kodeInvoice;

  document.getElementById('titleInvoice2').innerHTML = kodeInvoice;
  document.getElementById('titleSupplier2').innerHTML = kodeCust;

  document.getElementById('input_add_tanggalKirimAcc').value = formatTanggal(tglAcc)
  

  $("#formKirimTerimaAcc").modal('toggle')

}

function formatTanggal(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toISOString().split('T')[0]; // "2024-01-15"
}
</script>





@endsection
