@extends('newmaster')
@section('buttons')
 
@endsection
{{-- tampilan search bar 1 --}}
  @section('css')

  <style>
  .rodokNdukurTitik{
    margin-top:-12px;
  }
  </style>

  <style>
    .dataTables_wrapper {
    overflow-x: auto;
  }

  .dataTables_scrollBody {
      border: none !important;
  }

  /* Remove conflicting borders */
  .table-responsive {
      border: none !important;
  }

  #tabel td, #tabel th {
      border-left: 1px solid #dee2e6;
      border-top: 1px solid #dee2e6;
  }

  #tabel td:first-child, #tabel th:first-child {
      border-left: none;
  }

  #tabel thead tr:first-child th {
      border-top: none;
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

{{-- tableMaster2.css carries the .btn-action-{success,warning,primary,danger}
     chip classes used below for #tabel2/#tabel3's Actions buttons. The
     shared newmaster layout doesn't load it (so.blade.php links it the same
     page-local way). --}}
<link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}?v={{ filemtime(public_path('css/tableMaster2.css')) }}">

<style>
  /* "Fresh look" pass matching so.blade.php/purchaseOrder.blade.php: flat
     gray-uppercase header, light-purple row hover, consistent padding.
     #tabel2/#tabel3 had no custom header/row styling at all before this --
     just Bootstrap's default bg-primary/text-white header. */
  #tabel2 thead th,
  #tabel3 thead th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-bottom: 1px solid #e7e9ee;
    padding: 10px 14px;
  }
  #tabel2 tbody td,
  #tabel3 tbody td {
    padding: 10px 14px;
    font-size: 15px;
  }
  #tabel2 tbody tr:hover td,
  #tabel3 tbody tr:hover td {
    background-color: #f5f3ff;
  }

  /* Re-skins the Penawaran SO / Penawaran SO Otorisasi tab toggle to match
     so.blade.php's .radioChoiceMaster pill look (white pill bar, rounded
     buttons, solid-blue active state with a soft shadow), without touching
     the underlying <div class="nav nav-tabs">/<a data-toggle="tab"> markup
     -- that structure is what Bootstrap's tab JS binds to, so the visual
     restyle is done entirely through these selectors instead of changing
     tags. Replaces the old per-element inline styles (outlined pill,
     always-on border, no real "active" state distinction). */
  #nav-tab.nav-tabs {
    display: inline-flex;
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 999px;
    padding: 4px;
    gap: 4px;
  }
  #nav-tab.nav-tabs .nav-link {
    border: none;
    border-radius: 999px;
    padding: 8px 18px;
    margin: 0;
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    background-color: transparent;
    transition: all 0.2s ease;
    white-space: nowrap;
  }
  #nav-tab.nav-tabs .nav-link:hover {
    color: #212529;
    background-color: rgba(0,0,0,0.04);
  }
  #nav-tab.nav-tabs .nav-link.active {
    color: #fff;
    background-color: #007bff;
    box-shadow: 0 2px 6px rgba(0,123,255,0.35);
  }

  /* Wraps the pill tab bar in its own card, matching purchaseOrder.blade.php's
     .card.tab-card -- a dedicated card holding only the tab nav, separate
     from the card below it that holds the tables. */
  .tab-card {
    display: block !important;
    align-items: flex-start !important;
    padding: 0 !important;
    border: none !important;
    margin-bottom: 6px !important;
  }
  .tab-card .card-body {
    padding: 5px 10px !important;
  }
</style>

{{-- report-table.css/report-table.js + public/js/headerEngine.js power #tabel2/#tabel3's
     drag-to-reorder/hide/decimal column headers -- same engine so.blade.php uses (see that
     file's header comment), extracted there specifically so other pages could reuse it
     instead of each re-implementing purchaseOrder.blade.php's ~900-line one-off version
     (which turned out to depend on two ajax routes -- podataoutstandingpr/poloadpurchaseorder
     -- that don't actually exist in this app, so it never worked). --}}
<link rel="stylesheet" href="{{ asset('css/report-table.css') }}?v={{ filemtime(public_path('css/report-table.css')) }}">

<style>
  /* "Reset kolom" pill + its row, matching so.blade.php's copy of this same block.
     Kept OUTSIDE #rtBarTabel2/#rtBarTabel3 (as a flex sibling, not a child) because
     report-table.js's renderBar() fully overwrites those divs' innerHTML on every
     drag/hide/decimal change -- a button placed inside them would vanish on the
     first re-render. Not part of report-table.css itself, so declared here. */
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
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml1.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="imagecontainerTtd" class="d-none">
  <img src="" style="height: 95px; width: 200px" alt="">
</div>

<div id="page1" class="container-fluid">
    <!-- <div id="qrcode"></div> -->
    {{-- Tab bar wrapped in its own card, matching purchaseOrder.blade.php's
         .card.tab-card -- a dedicated card holding only the tab nav,
         separate from the card below holding the tables. --}}
  <div id="contentContainer" class="">
    <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
    <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
    <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
    <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
    <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
    <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
    <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
    <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />
    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
    <input type="hidden" id="level" value="{!! $level !!}" />

    {{-- No outer Bootstrap .card here (unlike the old markup this replaces) --
         .tb-report/.tb-report .main below already render as their own card-like
         panel (report-table.css), the same way so.blade.php's tables sit directly
         in its tab-content with no surrounding .card. Nesting them inside a
         Bootstrap .card as well double-boxed the toolbar/table/hint text, each
         fighting the other's padding and rounded corners. --}}
    <div class="tab-content" id="myTabContent">

          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            {{-- Toolbar matching purchaseOrder.blade.php's .po-toolbar on its "Purchase
                 Order" tab (period filter + search + Filter button + Add), rebuilt with
                 report-table.css's own .tb-report/.toolbar/.filter-wrap/.search-inp/
                 .btn-load/.action-group classes -- the same ones so.blade.php's toolbar
                 already uses -- instead of duplicating another copy of that CSS. --}}
            <div class="tb-report main">
              <div class="toolbar" style="margin-bottom:10px;">
                <input type="search" id="tabel2_search" class="search-inp" placeholder="Cari data...">

                <div class="filter-wrap">
                  <label>Periode</label>
                  <input type="date" class="filter-inp" id="tabel2_tglawal" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
                  <span class="filter-sep">s/d</span>
                  <input type="date" class="filter-inp" id="tabel2_tglakhir" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
                </div>

                <button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
                  <i class="bi bi-funnel"></i> Filter
                </button>

                <div class="action-group">
                  <button type="button" class="btn btn-action-primary" onclick="buttonAdd()">+ Add Penawaran</button>
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
                    {{-- Header content is fully JS-owned -- penawaransoReplaceThead()
                         (called from renderTabel2Rows(), run on page load via loadAll())
                         replaces this <thead>'s contents based on gcart_header. --}}
                    <thead style="white-space:nowrap;"></thead>
                    <tbody id="tabel2_data" class="text-left"></tbody>
                  </table>
                </div>
                <div class="rt-hint">
                  <i class="bi bi-info-circle"></i>
                  Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
                  untuk menyembunyikan kolom atau mengatur jumlah desimal.
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-12" style="overflow:auto;">
                <div class="container-fluid">
                      <table id="tabelRetur" class="table table-bordered table-striped"  >
                        <thead class="text-center">
                          <tr>
                            <th scope="col">Profile 2</th>
                            <th scope="col">No. SSP</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">No. Out</th>
                            <th scope="col">Gudang</th>
                          </tr>
                        </thead>

                        <tbody id="tabelRetur_data" class="text-left" >

                        </tbody>
                      </table>
                </div>
              </div>
            </div>
          </div>

          <!-- <div class="tab-pane fade" id="profile3" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-12" style="overflow:auto;">
                <div class="container-fluid">

                      <table id="tabelRetur" class="table table-bordered table-striped"  >
                        <thead class="text-center">
                          <tr>
                            <th scope="col">Profile 3</th>
                            <th scope="col">No. SSP</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">No. Out</th>
                            <th scope="col">Gudang</th>
                          </tr>
                        </thead>

                        <tbody id="tabelRetur_data" class="text-left" >

                        </tbody>
                      </table>
                </div>
              </div>
            </div>
          </div> -->

    </div>
  </div>

</div>

{{-- Filter modal for the "Penawaran SO" tab's Filter button. Reuses report-table.css's
     #modalFilter.rt-filter styling verbatim (that CSS is scoped to the literal id
     "modalFilter", the same id so.blade.php's own filter modal uses -- safe to repeat
     here since each page has its own DOM). --}}
<div class="modal fade rt-filter" id="modalFilter">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Penawaran SO
          <span class="rt-active-badge" id="penawaransoFilterBadge" style="display:none">1 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilter').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="penawaransoModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="penawaransoModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="penawaransoResetFilter()">Reset</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilter').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="penawaransoTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>

<div id="page2" class="container-fluid" style="display: none" >
  <div class="row">
    <div class="col-6 text-left">
      <h2 style="">Form Penawaran</h2>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg" style="
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

  <div id="modalBodyAddMain" class="">
    <div class="modal-body" style="">
      <div class="row">
        <input type="hidden" class="form-control" id="input_add_nourut">
        <div class="col-md-3">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-left" placeholder="Kode Customer" id="input_add_kodesupplier" onkeyup="checkKodeSupplier()">
                <button class="btn btn-primary btn-sm rounded-end shadow-sm" id="buttonAddListPelanggan" onclick="performSearchSupplier()">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>

            <div class="col-md-8" style="margin-top:-12px;">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>

            <div class="col-md-8" style="margin-top:-10px;">
              <div class="form-group">
                <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>



          </div>
        </div>

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control text-left" placeholder="Nama Customer" id="input_add_namasupplier" disabled> 
              </div>
            </div>


            <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group">
                <label>perihal</label>
              </div>
            </div>

            
            <div class="col-md-12">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows=2 placeholder="perihal" class="form-control text-left align-items-left" id="input_add_alamatsupplier"  ></textarea>
              </div>
            </div>


          </div>
        </div>


        <div class="col-md-3">
          <div class="row">

            <div class="col-md-6">
              <div class="row">
                <div class="col-9">
                  <div class="form-group">
                    <label>Valas</label>
                  </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="form-group">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <div class="input-group form-group">
                    <input type="text" class="form-control" id="input_add_valas"  disabled>
                    <button onclick="buttonAddListValas()" id="buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-12px;">
              <div class="row">

                <div class="col-6">
                  <div class="form-group">
                    <label>Kurs</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control text-right" id="input_add_kurs"  disabled>
                  </div>
                </div>




                <div class="col-6" style="margin-top:-10px;">
              <div class="form-group">
                <label>freight</label>
              </div>
            </div>


            

                <div class="col-md-6" style="margin-top:-10px;">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="input_add_freight"  placeholder="0.00">
              </div>
            </div>

            
       



              </div>
            </div>

          </div>
        </div>

      <div class="col-md-3">
        <div class="row">

          <div class="col-md-12">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>PPN</label>
                </div>
              </div>
              <div class="col-md-6">
                <select onchange="onChangeTipePPN()" id="input_add_tipeppn" class="form-control text-left form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 selected>None</option>
                  <option value=1 >Exclude</option>
                  <option value=2 >Include</option>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-12 rodokNdukurTitik">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>Pembayaran</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <select id="input_add_pembayaran" onchange="onChangeInputAddPembayaran()" class="form-control form-select-lg mb-3 text-left" aria-label=".form-select-lg example">
                    <option value=0 selected >Tunai</option>
                    <option value=1 >Kredit</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          {{-- budi sementara --}}
          <div class="col-md-12" style="margin-top:-12px;">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>Hari</label>
                </div>
              </div>

              <div class="col-md-6">
                <input type="number" class="form-control text-right" id="input_add_hari" onblur="onChangeHari()" value=0 min=0 >
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

          <hr/>
          <div class="row" style='margin-top:5px'>
            <div class="col-md-12 mt-2 text-left">
              <button type="button" class="btn btn-primary btn-lg" style="
                height: 30px;
                margin-top: -35px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="buttonShowHideHeader()" class="btn btn-secondary"><b>Show/Hide Header</b></button>
            </div>
          </div>
            <div class="showhidemodalbodyaddmain mt-4" id="modalBodyAddMainHeader" style="display: none;">
              <div class="row" style='margin-top:-30px'>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Franco</label>
                      </div>
                    </div>

                    <!-- <div class="form-group row">
                      <input class="form-control col-8" id="input_add_kodealamatkirim" readonly value='GMPL' >
                      <button onclick="buttonAddListGudang()" id="buttonAddListGudang"  style="height:32px;" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                    </div> -->

                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_alamatkirim"  ></textarea>
                      </div>
                    </div>

                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Delivery</label>
                      </div>
                    </div>

                    


                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_ekspedisi"  ></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-12">
                      <label>Validitas</label>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group" style="margin-top: 14px">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_keterangan" onblur="onChangeCatatan()"></textarea>
                      </div>
                    </div>
                  </div>
                </div>


                <div class="col-md-3">
                  <div class="row">

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>TTD</label>
                          </div>
                        </div>
                        <div class="col-8" style="margin-top:-5px">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_noso" value='-' readonly>
                            <button onclick="buttonAddListNoSO()" id="buttonAddListNoSo" style="height:32px;" class="btn btn-primary btn-sm text-right">
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Nama Ttd</label>
                          </div>
                        </div>
                        <div class="col-8">
                          <div class="form-group">
                            <input type="text" class="form-control" id="input_add_nopocust" value ='-' readonly>
                          </div>
                        </div>
                      </div>
                    </div>

                    


                  </div>
                </div>



               <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Up</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_up"  ></textarea>
                      </div>
                    </div>
                  </div>
                </div>



              <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Up2</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_up2"  ></textarea>
                      </div>
                    </div>
                  </div>
              </div>  


              <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-12">
                      <label>note</label>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group" style="margin-top: 14px">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_note" onblur="onChangeCatatan()"></textarea>
                      </div>
                    </div>
                  </div>
              </div>


              <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-12">
                      <label>Ket Revisi</label>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group" style="margin-top: 14px">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_ketrevisi" onblur="onChangeCatatan()"></textarea>
                      </div>
                    </div>
                  </div>
              </div>





              
              <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Tgl revisi</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="input-group form-group">
                         <input type="date" class="form-control text-left" id="input_add_tglrevisi" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()">
                      </div>
                    </div>
                  </div>
                </div>



            <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Tgl pr. cust</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                         <input type="date" class="form-control text-left" id="input_add_tglprcust" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()">
                      </div>
                    </div>
                  </div>
              </div>  



            <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Lokasi Penerima</label>
                      </div>
                    </div>

                    <div class="col-8" style="margin-top:-5px">
                          <div class="input-group form-group">

                            <input type="text" class="form-control" id="input_add_lokasipenerima" value='-' readonly>
                             <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima" style="height:32px;" value = '-' class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                 

                          </div>
                        </div>
                  </div>
              </div>  


             <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>nama lokasi</label>
                      </div>
                    </div>

                    <div class="col-8" style="margin-top:-5px">
                          <div class="input-group form-group">

                            <input type="text" class="form-control" id="input_add_namalokasipenerima" value='-' readonly>
                            


                          </div>
                        </div>
                  </div>
              </div>   








              
              








            <div class="col-md-3" hidden>
              <div class="row">

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Back Office</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-12">
                          <div class="input-group form-group">
                            <input type="hidden" class="form-control" id="input_add_kodebackoffice" >
                            <input type="text" class="form-control" id="input_add_namabackoffice"  disabled>
                            <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            {{-- budi sementara 2 --}}
            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>PIC</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input type="hidden" class="form-control" id="input_add_kodepic"  >
                        <input type="text" class="form-control" id="input_add_namapic"  disabled>
                        <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Sales</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group form-group">
                    <input type="hidden" class="form-control" id="input_add_kodesales" >
                    <input type="text" class="form-control" id="input_add_namasales"  disabled>
                    <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-6" style="margin-top:-40px">
                  <div class="form-group">
                    <label>Draft PO</label>
                  </div>
                </div>

                <div class="col-md-6" style="margin-top:-40px">
                  <select onchange="onChangeDraftPO()" id="input_add_draftpo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0 selected>Tidak</option>
                    <option value=1 >Ya</option>
                  </select>
                </div>
              </div>
            </div>

          </div>

        </div>
            <hr/>
      </div>

    </div>

      <div class="showhidemodalbodyaddmain container-fluid" id="modalBodyAddMainItems">
        <div class="container-fluid" style="overflow:auto;">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead id='tabel_data_header' class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qnt</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Harga</th>
                  <th style="padding: 4px 12px;" scope="col">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">No. PR</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add" class="text-left" >
                <tr>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="">
                        <i class="bi bi-info"></i>
                      </button>
                      <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="">
                        <i class="bi bi-key-fill"></i>
                      </button>
                      <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="">
                        <i class="bi bi-pencil-fill"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mt-2 text-right">
            <button type="button" id='buttonTambahItem' class="btn btn-primary btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="buttonAddAddItem()" class="btn btn-secondary"><b>+ Tambah Item</b></button>
          </div>
        </div>

        <!-- ADD add -->
        <div id="addAddItem" class="container-fluid showhide">
          <hr/>

            <div class="row">
              <div class="col-4">
                <h4 id="h4AddAddItem" style="margin-left:-35px;">Add Item</h4>
                <h4 id="h4AddEditItem" style="margin-left:-35px;">Edit Item</h4>
              </div>
            </div>

          <div class="row" style='margin-top:-30px'>
            <div class="col-md-12">
                <div class="row">
              <!-- No Penyerahan -->

              <div class="col-md-3">
                <div class="row">
                  <!-- No Penyerahan -->
                  <div class="col-md-12">
                    <div class="row" hidden>
                      <div class="col-6">
                        <div class="form-group">
                          <label>Jasa</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group form-group">
                          <select id="input_add_add_jasa" class="form-control form-select-lg mb-3" disabled>
                            <option value=0 selected>Tidak</option>
                            <option value=1>Iya</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <!-- No Penyerahan -->
                  <div class="col-md-12">
                    <div class="row" hidden>
                      <div class="col-4">
                        <div class="form-group">
                          <label>FOC</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group form-group"> {{-- nama lama : nopenyerahan --}}
                          <select id="input_add_add_foc" onChange="LockFreeOfCharge()" class="form-control form-select-lg mb-3">
                            <option value=0>Tidak</option>
                            <option value=1>Iya</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3" style="margin-left:-50px;">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>QTY</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_add_add_qty" onblur="cekQntStock()" value="0.00" >
                    </div>
                  </div>
                </div>
              </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Satuan</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <select id="input_add_add_nosat" class="form-control form-select-lg mb-3">
                        <option value=0 selected>Tidak</option>
                      </select>

                        <input type="text" class="form-control" value="-" id="input_add_add_satminus" >

                    </div>
                  </div>
                </div>

              </div>

                <div class="row">
                  <!-- Barang dan Nama Produk -->
                  <div class="col-md-6">
                    <!-- <div class="row">
                      
                      <div class="col-3" style="margin-top:-25px;">
                        <div class="form-group" hidden>
                          <label>No. PNW PO</label>
                        </div>
                      </div>

                      <div class="col-md-8" style="margin-top:-25px;">
                        <div class="input-group form-group" hidden>
                          <input type="hidden" class="form-control" value="-" id="input_add_add_nopnwpo" readonly>
                          <button onclick="buttonAddAddListPWO()" id="buttonAddAddListBarang" class="btn btn-primary btn-sm text-right" tabindex="1">
                            <i class="bi bi-plus"></i>
                          </button>
                        </div>
                      </div>

                    </div> -->

                    <!-- Kode Barang and Nama Barang in same row, halved -->
                    <div class="row">
                      <!-- Kode Barang - Left Half -->
                      <div class="col-md-6" style="margin-top:-12px;">
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Kode Barang</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="input-group form-group">
                              <input type="text" class="form-control" id="input_add_add_kodebarang">
                              <button onclick="performSearch()" id="buttonAddAddListBarang" class="btn btn-primary btn-sm text-right" >
                                  <i class="bi bi-plus"></i>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Nama Barang - Right Half -->
                      <div class="col-md-5" style="margin-top:-12px;">
                        <div class="form-group">
                          <input type="text" class="form-control" id="input_add_add_namabarangasli" readonly>
                        </div>
                      </div>

                    </div>

                    <!-- Second Nama Barang (kept separate as requested) -->
                    <div class="row">
                      <div class="col-3" style="margin-top:-12px;">
                        <div class="form-group">
                          <label>Nama Barang</label>
                        </div>
                      </div>
                      <div class="col-md-8" style="margin-top:-12px;">
                        <div class="form-group">
                          <input type="text" class="form-control" id="input_add_add_namabarang">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-3" style="margin-top:-12px;">
                        <div class="form-group">
                          <label>Note</label>
                        </div>
                      </div>
                      <div class="col-md-8" style="margin-top:-12px;">
                        <div class="form-group">
                          <input type="text" class="form-control" id="input_add_add_keteranganbarang">
                        </div>
                      </div>
                    </div>
                  </div>



                  

                <div class="col-md-6" style="margin-left:-50px;">
                  <div class="row">
                    <!-- Harga - Left Half -->
                    <div class="col-md-6" style="margin-top:-12px;">
                      <div class="row">

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Harga</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="number" class="form-control text-right" onchange="onChangeInputAddAddHarga()" id="input_add_add_harga" value="0.00" >
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Harga - Left Half -->
                    <div class="col-md-6" style="margin-top:-12px;">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label   id="label_add_add_isi" >isi</label>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="number" class="form-control text-right" id="input_add_add_isi" value="0.00" >
                           <!-- <input type="number" class="form-control text-right" id="input_add_add_discrp" onchange="reverseCalculateDiscPercent()" tabindex="7"> -->
                          </div>
                        </div>
                      </div>

                    </div>
                    <!-- Disc RP - Right Half -->





                  </div>






                  <div class="row">
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                        <!-- <label>Merk SO</label> -->
                      </div>
                    </div>
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                        <!-- <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen1" value=0 onChange='calculateDiscRp()' tabindex="8"> -->
                        
                      </div>
                    </div>

                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                        <input type="hidden" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen2" value=0 onChange='calculateDiscRp()' >
                      </div>
                    </div>
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                        <input type="hidden" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen3" value=0 onChange='calculateDiscRp()'>
                      </div>
                    </div>

                  </div>


                  <div class="row">
                    <div class="col-md-3" style="margin-top:-17px;">
                      <div class="form-group">
                         <label>Disc(%)</label> 

                      </div>
                    </div>


                    <div class="col-md-3" style="margin-top:-17px;">
                      <div class="form-group">
                       <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen1" value=0 onChange='calculateDiscRp()' >

                      </div>
                    </div>


                     <div class="col-md-6" style="margin-top:-12px;">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Disc Rp</label>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="hidden" class="form-control text-right" id="input_add_add_hargaAwal" value="0.00">
                           <input type="number" class="form-control text-right" id="input_add_add_discrp" onchange="reverseCalculateDiscPercent()" >
                          </div>
                        </div>
                      </div>

                    </div>



                  </div>


                  <div class="row">
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                         <label>tipe so</label> 

                      </div>
                    </div>
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                       <input type="text" class="form-control" id="input_add_add_tipeso">

                      </div>
                    </div>


                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                         <label>merk so</label> 

                      </div>
                    </div>
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                       <input type="text" class="form-control" id="input_add_add_merkso">

                      </div>
                    </div>





                  </div>


                  <!-- <div class="row">
                    
                  </div> -->

                  






                </div>

                <div class="col-md-1" style="margin-top:-10px;" hidden>
                  <div class="form-group">
                    <input type="text" min="1" max="100" class="form-control" id="input_add_add_noPPL" > {{-- nama lama : satuanproduk --}}
                  </div>
                </div>
                <div class="col-md-1" style="margin-top:-10px;" hidden>
                  <div class="form-group">
                    <input type="text" min="1" max="100" class="form-control" id="input_add_add_urutPPL" > {{-- nama lama : satuanproduk --}}
                  </div>
                </div>

              </div>
            </div>

            <div class="col-md-12">
              <div id="divhargaterakhir">
                <div class="row">

                  <div class="col-12">
                    <div class="form-group">
                      <label>Harga Terakhir</label>
                    </div>
                  </div>

                  <div class="col-md-12 mb-4" style="overflow:auto;">
                    <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                      <table id="tabel_add_harga_terakhir" class="table table-bordered table-hover table-striped table-responsive-lg">
                        <thead class="text-center bg-primary text-white">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Supplier</th>
                            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                            <th style="padding: 4px 12px;" scope="col">Qnt</th>
                            <th style="padding: 4px 12px;" scope="col">Satuan</th>
                            <th style="padding: 4px 12px;" scope="col">Valas</th>
                            <th style="padding: 4px 12px;" scope="col">Kurs</th>
                            <th style="padding: 4px 12px;" scope="col">Harga</th>
                            <th style="padding: 4px 12px;" scope="col">Disc Rp</th>
                            <th style="padding: 4px 12px;" scope="col">Hrg. Nett</th>
                          </tr>
                        </thead>
                        <tbody id="tabel_data_add_harga_terakhir" class="text-left" >
                          <tr>
                            <td>-</td>
                            <td>-</td>
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

                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div id="divStockProyeksi">
                <div class="row">

                  <div class="col-12">
                    <div class="form-group">
                      <label>Stock Proyeksi</label>
                    </div>
                  </div>

                  <div class="col-md-12 mb-4" style="overflow:auto;">
                    <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                      <table id="tabel_add_stock_proyeksi" class="table table-bordered table-hover table-striped table-responsive-lg">
                        <thead class="text-center bg-primary text-white">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Stock</th>
                            <th style="padding: 4px 12px;" scope="col">Out PO</th>
                            <th style="padding: 4px 12px;" scope="col">Out SO</th>
                            <th style="padding: 4px 12px;" scope="col">S Marketing</th>
                          </tr>
                        </thead>
                        <tbody id="tabel_data_add_stock_proyeksi" class="text-left" >
                          <tr>
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

                </div>
              </div>
            </div>

          </div>

          <div class="row mt-2">
            <div class="col-md-12 text-right" style="margin-top:-40px;">

              <button type="button" class="btn btn-success btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="showTableHargaTerakhir()" class="btn btn-secondary">Histori Harga</button>

              <button type="button" class="btn btn-info btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="showTableStockProyeksi()" class="btn btn-secondary">Stock Proyeksi</button>

              <button type="button" class="btn btn-danger btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="closeShowHideAdd()" class="btn btn-secondary">Batal</button>

              <button type="button" id="submitAddAdd" class="btn btn-primary btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddAdd('I')" class="btn btn-secondary">Simpan Data</button>

              <button type="button" id="submitAddEdit" class="btn btn-primary btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddAdd('U')" class="btn btn-secondary">Submit Edit</button>
            </div>

          </div>

        </div>

        <!-- END ADD ADD -->

        <!-- ADD EDIT -->

        <div  id="addEditItem" class="container-fluid showhide">
            <div class="row">
              <div class="col-4">
                <h4>Edit Item</h4>
              </div>
            </div>
            <div class="row">

              <div class="col-md-12">

              <div class="row">

            <div class="col-md-4">

            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Ref PR</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus" ></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_refpr" value=""  disabled>
              </div>
            </div>

            </div>

            </div>


            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>No Penyerahan</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_nopenyerahan"  disabled>
              </div>
            </div>

            </div>

            </div>
            </div>
            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Barang</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick="buttonAddEditListBarang()" id="buttonAddEditListBarang"  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="hidden" class="form-control" id="input_add_edit_kodebarang" >
                <input type="text" class="form-control" id="input_add_edit_namabarang"  disabled>
              </div>
            </div>

            </div>

            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>Nama Produk</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_namaproduk" >
              </div>
            </div>

            </div>

            </div>

<div class="col-md-12">
  <div class="row">

    <div class="col-12">
      <div class="form-group">
        <label>Harga Terakhir</label>
      </div>
    </div>

    <div class="col-md-12 mb-4">
      <div class="form-group">
        <table id="tabel_edit_harga_terakhir" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Tanggal</th>
              <th scope="col">Qnt</th>
              <th scope="col">Satuan</th>
              <th scope="col">Valas</th>
              <th scope="col">Kurs</th>
              <th scope="col">Harga</th>
              <th scope="col">Disc Rp</th>
              <th scope="col">Total Diskon</th>
            </tr>
          </thead>
          <tbody id="tabel_data_edit_harga_terakhir" class="text-left" >
            <tr>
              <td>-</td>
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

  </div>
</div>


            <div class="col-md-12">
              <div class="row">


              <div class="col-md-2">
                <div class="row">

              <div class="col-md-12">
                <div class="form-group">
                  <label>Qty</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="input_add_edit_qty" value ="0.00" >
              </div>
            </div>

            </div>
          </div>

            <div class="col-md-2">
              <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>Satuan</label>
              </div>
            </div>


            <div class="col-md-12">
              <select id="input_add_edit_nosat" onchange="onChangeInputAddAddNosat()" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" >
                <option value=0 selected>Pilih Satuan</option>
              </select>
            </div>

          </div>
        </div>

        <div class="col-md-2 row">
          <div class="col-12">
            <div class="form-group">
              <label>Satuan Produk</label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_edit_satuanproduk" >
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Harga</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
              <input type="number" class="form-control text-right" onchange="onChangeInputAddAddHarga()" id="input_add_edit_harga" value ="0.00" >
              </div>
            </div>
          </div>
        </div>


    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_edit_disc" onChange="onChangeInputAddAddDisc()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc Rp</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_edit_discrp" onChange="onChangeInputAddAddDiscRp()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

        </div>
        </div>
        <div class="col-md-12">
        <div class="row">


        </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Tambah ke PO</label>
              </div>
            </div>
            <div class="col-md-12">
              <select onchange="" id="input_add_edit_tambahkepo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Pilih</option>
                <option value=1 >Tidak</option>
                <option value=2 >Ya</option>
              </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Booking</label>
              </div>
            </div>
            <div class="col-md-12">
            <select onchange="" id="input_add_edit_booking" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
              <option value=0 selected>Tidak</option>
              <option value=1 >Ya</option>
            </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
              <label>Urgent</label>
              </div>
            </div>
            <div class="col-md-12">
                <select onchange="" id="input_add_edit_urgent" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Tidak</option>
                <option value=1 >Ya</option>
              </select>
            </div>
          </div>
        </div>
      </div>



          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
            </div>
          </div>


          <hr/>

          </div>


        <hr/>
    </div>

  <div class="container-fluid" style="margin-top: -10px;">
  <div class="row">

    <!-- Disc % -->
    <div class="col">
      <div class="form-group">
        <label>Disc %</label>
        <input type="number" class="form-control text-right" id="input_add_disc" onblur="onChangeInputAddDisc()" value="0.00">
      </div>
    </div>

    <!-- DiscRp -->
    <div class="col">
      <div class="form-group">
        <label>DiscRp</label>
        <input type="number" class="form-control text-right" id="input_add_discrp" onblur="onChangeInputAddDiscRp()" value ="0.00" >
      </div>
    </div>

    <!-- DPP -->
    <div class="col">
      <div class="form-group">
        <label>DPP</label>
        <input type="text" class="form-control text-right" id="input_add_dpp" value="0.00" disabled>
      </div>
    </div>

    <!-- PPN -->
    <div class="col">
      <div class="form-group">
        <label>PPN</label>
        <input type="text" class="form-control text-right" id="input_add_ppn" value="0.00" disabled>
      </div>
    </div>

    <!-- Grand Total -->
    <div class="col">
      <div class="form-group">
        <label>Grand Total</label>
        <input type="text" class="form-control text-right" id="input_add_grandtotal" value="0.00" disabled>
      </div>
    </div>

  </div>
</div>

</div>

<!-- page3 -->

<div id="page3" class="container-fluid" style="display: none" >
      <div class="row">
        <div class="col-6 text-left">
          <h2>Detail SO</h2>
        </div>
        <div class="col-6 text-right">
          <button type="button" class="btn btn-danger btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonCloseForm()">Close</button>
        </div>
      </div>

<div id="" class="">
  <div class="modal-body" >
<!-- <div class="container-fluid"> -->
  <div class="row">

    <input type="hidden" class="form-control" id="input_detail_nourut" >
    <div class="col-md-3">

      <div class="row">

        <div class="col-md-4" style="margin-top:-40px;">
          <div class="form-group">
            <label>No Bukti</label>
          </div>
        </div>
        <div class="col-md-8" style="margin-top:-40px;">
          <div class="form-group">
            <input type="text" class="form-control text-center" id="input_detail_nobukti" placeholder="" disabled>
          </div>
        </div>

      <div class="col-md-4" style="margin-top:-12px;">
        <div class="form-group">
          <label>Tanggal</label>
        </div>
      </div>
      <div class="col-md-8" style="margin-top:-12px;">
        <div class="form-group">
          <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
        </div>
      </div>


      <div class="col-md-4" style="margin-top:-10px;">
        <div class="form-group">
          <label>Pelanggan</label>
        </div>
      </div>


    <div class="col-md-8" style="margin-top:-10px;">
      <div class="input-group form-group">
        <input type="text" class="form-control text-center" id="input_detail_kodepelanggan" disabled>
      </div>
    </div>

      </div>

    </div>

    <div class="col-md-3">

      <div class="row">
        <!-- <div class="col-md-6">
          <div class="row">


        <div class="col-9">
          <div class="form-group">
            <label>Pelanggan</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <button class="btn btn-primary btn-sm text-right" id="buttonAddListPelanggan" onclick="buttonAddListPelanggan()"><i class="bi bi-plus"></i></button>
        </div>

      </div>
      </div>
    </div>
    <div class="col-md-6">
    </div> -->


      <!-- <div class="col-md-6">
        <div class="row"> -->



        <div class="col-md-12" style="margin-top:-40px;">
          <div class="form-group">
            <input type="text" class="form-control text-center" id="input_detail_namapelanggan"  disabled>
          </div>
        </div>
        <!-- </div>
      </div> -->
      <!-- <div class="col-md-6">
        <div class="row"> -->


        <div class="col-md-12" style="margin-top:-10px;">
          <div class="form-group">
            <textarea  style="width: 100%; resize: none" rows=3  class="form-control text-center" id="input_detail_alamatpelanggan" disabled></textarea>
          </div>
        </div>
        <!-- </div>
      </div> -->

      </div>
    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-6">

        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <div class="form-group">
              <label>Valas</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="buttonAddListValas()" id="buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>


      </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-40px;">
          <div class="input-group form-group">
            <input type="text" class="form-control text-center" id="input_detail_valas"  disabled>
            <!-- <button onclick="buttonAddListValas()" id="buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12" style="margin-top:-20px;">
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label>Kurs</label>
          </div>
        </div>
          <div class="col-md-6">
            <div class="form-group">
              <input type="text" class="form-control text-center" id="input_detail_kurs"  disabled>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12" style="margin-top:-12px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>TOP</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_hari" disabled value=0 min=0 >
          </div>
        </div>
        </div>
    </div>

      </div>

    </div>



    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12" style="margin-top:-40px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>Pembayaran</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
          <select  id="input_detail_pembayaran" disabled class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example">
            <option value=0 selected >Tunai</option>
            <option value=1 >Kredit</option>
          </select>
        </div>
        </div>
        </div>
        </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>TGL KIRIM</label>
              </div>
            </div>
            <div class="col-md-6">
                <input type="date" class="form-control text-left" id="input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
          </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>
            <div class="col-md-6">
              <select id="input_detail_tipeppn" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                <option value=0 selected>None</option>
                <option value=1 >Exclude</option>
                <option value=2 >Include</option>
              </select>
            </div>
          </div>
        </div>


      </div>

    </div>
  </div>

  <!-- </div> -->
  <!-- <hr/> -->
  <!-- <div class="row ">
    <div class="col-md-12 text-left">
      <div class="row">
        <div class="col-md-12">

        </div>
      </div>
    <button type="button" class="btn btn-primary" onclick="buttonAddMainHeader()" class="btn btn-secondary"  >Header</button>
    <button type="button" class="btn btn-primary" onclick="buttonAddMainItems()" class="btn btn-secondary"  >Items</button>
</div>
</div> -->
<hr/>
<div class="row ">
<div class="col-md-12 mt-2 text-left">
  <button type="button" class="btn btn-primary btn-lg" style="
  height: 30px;
  margin-top: -40px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  transition: background-color 0.3s, box-shadow 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
  onclick="buttonShowHideHeaderDetail()" class="btn btn-secondary"><b>Show Hide Header</b></button>
</div>
</div>
  <div class="mt-4" id="modalBodyDetailMainHeader">

  <div class="row">
    <div class="col-md-3">
      <div class="row">

        <div class="col-md-6" style="margin-top:-20px;">
          <div class="form-group">
            <label>Alamat Kirim</label>
          </div>
        </div>

        <div class="col-md-12" style="margin-top:-15px;">
          <div class="form-group">
            <input type="hidden" class="form-control" id="input_detail_kodealamatkirim" >
            <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatkirim"  disabled></textarea>
          </div>
        </div>

      </div>

    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-8" style="margin-top:-20px;">
          <div class="form-group">
            <label>Ekspedisi</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <!-- <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
        </div>

      </div>

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->
      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group">
          <input type="hidden" class="form-control" id="input_detail_kodelokasipenerima" >
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatlokasipenerima"  value ='-'disabled></textarea>
        </div>
      </div>

      </div>

      <!-- <div class="row">
        <div class="col-9">
          <div class="form-group">
            <label>PIC</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
        </div>

      </div>
      </div> -->
      <div class="row">

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->

      </div>

    </div>

    <div class="col-md-3">


      <div class="row">

        <div class="col-md-10" style="margin-top:-20px;">
          <label>Keterangan</label>
        </div>

      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group" style="margin-top: 14px">
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_catatan" disabled></textarea>
        </div>
      </div>

      <!-- <div class="col-md-12">

      </div> -->

      </div>

      <div class="row">

      <!-- <div class="row"> -->

      </div>

      <div class="row ">

  </div>

    </div>

    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6" style="margin-top:-20px;">
              <div class="form-group">
                <label>DP</label>
              </div>
            </div>

          <div class="col-md-6" style="margin-top:-20px;">
            <div class="form-group">
              <input type="number" class="form-control text-center" id="input_detail_dp" value='0.00' disabled>
            </div>
          </div>
          </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">

            <div class="form-group">
              <label>No PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input  type="text" class="form-control text-center" id="input_detail_nopo"  disabled>
          </div>
        </div>
        </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">
            <div class="form-group">
              <label>Tgl PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input type="date" class="form-control text-center" id="input_detail_tanggalpo" value="{!! date('Y-m-d') !!}" disabled>
          </div>
        </div>
        </div>
        </div>

      </div>

      <div class="row">

      <!-- <div class="col-md-12">

      </div> -->

      </div>

    </div>
    <!-- <div class="col-md-12 mt-2 text-right" style="margin-bottom: 20px">
    <button type="button" class="btn btn-primary" id="buttonSubmitSaveHeader" onclick="submitSaveHeader()" class="btn btn-secondary"  >Save Header</button>
</div> -->

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-6" style="margin-top:-10px;">
        <div class="form-group">
          <label>PIC</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="input_detail_kodepic"  >
            <input type="text" class="form-control" id="input_detail_namapic"  disabled>
            <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>

  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-10" style="margin-top:-10px;">
        <div class="form-group">
          <label>Back Office</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>

    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12">
          <div class="row">

          <!-- <div class="col-4">

          <div class="form-group">

          </div>

          </div> -->
          <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="input_detail_kodebackoffice" >
            <input type="text" class="form-control" id="input_detail_namabackoffice"  disabled>
            <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>

          </div>
          </div>
        <!-- </div> -->
        </div>
      </div>

    </div>

    <!-- <div class="row"> -->

    <!-- </div> -->

  </div>

</div>

<div class="col-md-3">
  <div class="row">

  <div class="col-md-12">
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-8" style="margin-top:-10px;">
            <div class="form-group">
              <label>Sales</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>

        </div>
      </div>
      <div class="col-md-6" style="margin-top:-10px;">
        <div class="input-group form-group">
          <input type="hidden" class="form-control" id="input_detail_kodesales" >
          <input type="text" class="form-control" id="input_detail_namasales"  disabled>
          <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

        </div>
      </div>

    </div>

  </div>
  <!-- <div class="col-md-12">
    <div class="form-group">
      <input type="hidden" class="form-control" id="input_detail_kodesales" >
      <input type="text" class="form-control" id="input_detail_namasales"  disabled>
    </div>
  </div> -->
  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6" style="margin-top:-25px;">
      <div class="form-group">
        <label>Draft PO</label>
      </div>
    </div>

  <div class="col-md-6" style="margin-top:-25px;">
    <select  id="input_detail_draftpo" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
      <option value=0 selected>Tidak</option>
      <option value=1 >Ya</option>
    </select>
  </div>
  </div>

</div>

  </div>
  <hr/>

</div>

</div>
<div class=" container-fluid" id="" style="margin-top:-40px;">

  <!-- sinia -->

<!-- END ADD EDIT -->

<div class="container-fluid mt-4" style="overflow:auto;">
  <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
  <div class="row" style="overflow:auto;">
    <table id="tabel_detail" class="table table-bordered table-hover table-striped table-responsive-lg">
      <thead class="text-center bg-primary text-white">
        <tr>
          <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
          <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
          <th style="padding: 4px 12px;" scope="col">Qty</th>
          <th style="padding: 4px 12px;" scope="col">Sat</th>
          <th style="padding: 4px 12px;" scope="col">Harga</th>
          <th style="padding: 4px 12px;" scope="col">Diskon</th>
          <th style="padding: 4px 12px;" scope="col">NDPP</th>
          <!-- <th scope="col">Actions</th> -->

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
    <!-- <button onclick="buttonSubKategori()">tes</button> -->
</div>

<div class="row ">
<div class="col-md-12 mt-2 text-right">
<!-- <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary"  ><b>+ Tambah Item</b></button> -->
</div>
</div>

<hr/>
</div>

  <div class="container-fluid" style="margin-top: -10px;">
    <div class="row" >

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-top:-50px; margin-left:60px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_disc" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DiscRp</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-25px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_discrp" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DPP</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-50px;">
          <div class="form-group">
            <input type="text" class="form-control text-right" id="input_detail_dpp" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-40px;">PPN</label>
          </div>
        </div>

        <div class="col-md-9" style="margin-left:-80px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="input_detail_ppn" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label style="margin-left:-75px;">Grand Total</label>
          </div>
        </div>

        <div class="col-md-10" style="margin-left:45px; margin-top:-50px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="input_detail_grandtotal" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    </div>

  </div>
</div>
</div>

<!-- Add this modal HTML once in your Blade template (outside the function) -->
<div class="modal fade" id="modalOtorisasi" tabindex="-1" role="dialog" aria-labelledby="modalOtorisasiLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header text-white">
        <h5 class="modal-title" id="modalOtorisasiLabel">Detail Otorisasi PO</h5>
        <button type="button" class="close text-black" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="overflow-x: auto;">
        <table class="table table-bordered table-lg">
          <thead>
            <tr class="bg-primary text-white">
              <th>Kode Barang</th>
              <th>Nama Barang</th>
              <th>Qnt</th>
              <th>Harga</th>
              <th>Diskon</th>
              <th>Sub Total</th>
              <th>Stock</th>
              <th>Nilai Stock RP</th>
            </tr>
          </thead>
          <tbody id="otorisasi-table-body">
            <tr><td colspan="8" class="text-center">Loading...</td></tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-confirm-otorisasi">
          <i class="fa fa-check"></i> Otorisasi
        </button>
      </div>
    </div>
  </div>
</div>

<!-- page3 end input_add -->

<!-- start modal print-->
<div class="modal fade" id="modalPrint" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        
        <div class="modal-header">
          <h5 class="modal-title">Pilih Design Cetak</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('default')">
            Cetak PO IDR
          </button>

          <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('design3')">
            Cetak PO Non IDR
          </button>
        </div>

      </div>
    </div>
  </div>
<!-- end modal print-->

@include('purchasing/modals/modalPOAdd')

@endsection

@section('js')
<script src="{{ asset('js/report-table.js') }}"></script>
<script src="{{ asset('js/headerEngine.js') }}?v={{ filemtime(public_path('js/headerEngine.js')) }}"></script>
<script type="text/javascript">
$purut=0
let dataTableAdd = []
let dataTableEdit = []
let dataCekHarga = []
let dataAddAddListItem = []

let dataRefreshOutstanding = []
let dataRefreshOutstanding2 = []

let dataRefreshPenerimaan = []

// -- #tabel2/#tabel3 interactive column engine (public/js/headerEngine.js) --
// Same shared drag/hide/decimal-column engine so.blade.php uses (extracted
// there from its original #tabel/#tabel7 implementation specifically so it
// could be reused here instead of duplicating it). Persistence goes through
// PenawaranSOController::loadHeader/simpanHeader -- the same generic
// (username, href, reportmode) contract SOController's own loadHeader/
// simpanHeader use, just with a href unique to each of this page's tables.
HeaderEngine.configure({
  loadUrl: "{!! url('penawaransoloadheader') !!}",
  simpanUrl: "{!! url('penawaransosimpanheader') !!}"
});

var lastTabel2Rows = [];

HeaderEngine.registerTable('tabel2', {
  href: 'penawaranso_tabel2',
  tableSel: '#tabel2',
  barSel: '#rtBarTabel2',
  setDefault: function () { setDefaultHeaderTabel2(); },
  onChange: function () { reinitTabel2(); }
});

// #tabel2's columns match dbpenawaranso's loadAll() query (Marketing/PenawaranSOController::
// loadAll()); "No. SO" is kept for parity with the table this replaces even though that
// query doesn't actually select a NOSO field, so it always renders blank today.
function setDefaultHeaderTabel2() {
  gcart_header = [
    ['NoBukti',      'No Bukti',       1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',        1, 'date',    0, 0],
    ['NamaCustSupp', 'Customer',       1, 'varchar', 0, 0],
    ['NOSO',         'No. SO',         1, 'varchar', 0, 0],
    ['TotDPPRp',     'DPP Rp',         1, 'float',   0, 2],
    ['TotPPNRp',     'PPN Rp',         1, 'float',   0, 2],
    ['TotNetRp',     'Grand Total Rp', 1, 'float',   0, 2]
  ];
}

// Digabung dari tabel2ActionsCell (Belum Otorisasi, tombol Otorisasi) + tabel3ActionsCell
// (Sudah Otorisasi, tombol Batal Otorisasi) sejak keduanya digabung jadi satu tabel dengan
// filter Semua/Belum/Sudah Otorisasi -- port 1:1 dari pola tabelActionsCell gabungan
// milik PerintahReturJualController/queryOutstanding().
function tabel2ActionsCell(row) {
  var nobukti = HeaderEngine.pickCI(row, 'NoBukti');
  var needOto = Number(HeaderEngine.pickCI(row, 'NeedOtorisasi'));
  var html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn-action-sm btn-action-warning" type="button" title="Details" onclick="buttonDetail(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>';
  if (needOto) {
    html += '<button class="btn-action-sm btn-action-primary" type="button" title="Otorisasi" onclick="buttonOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
  } else {
    html += '<button class="btn-action-sm btn-action-danger" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
  }
  html += '<button class="btn-action-sm btn-action-primary" type="button" title="Print" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
  html += '<button class="btn-action-sm btn-action-success" type="button" title="Edit" onclick="buttonEdit(\'' + nobukti + '\')"><i class="bi bi-pencil-fill"></i></button>';
  html += '</div></td>';
  return html;
}

function penawaransoFormatTanggal(raw) {
  if (!raw) { return ''; }
  var d = new Date(raw);
  var dd = ('0' + d.getDate()).slice(-2);
  var mm = ('0' + (d.getMonth() + 1)).slice(-2);
  return dd + '/' + mm + '/' + d.getFullYear();
}

// col: [field, label, visible, type, hasTotal, decimals] -- same shape as gcart_header rows.
function penawaransoValueCell(row, col) {
  var raw = HeaderEngine.pickCI(row, col[0]);
  var type = col[3];
  if (type === 'date') {
    return '<td>' + penawaransoFormatTanggal(raw) + '</td>';
  }
  if (type === 'float') {
    var dp = Number(col[5]) || 0;
    var n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + formatAngka(n.toFixed(dp)) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

function penawaransoOtoBoolCell(flag) {
  return Number(flag)
    ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
    : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
}

// Kolom Otorisasi (level 1..$level) + Batal ditempel FIXED di belakang kolom hasil cart --
// jumlahnya berubah tergantung level login, jadi tidak cocok disimpan/diseret sebagai
// bagian dari layout kolom milik user (sama seperti kolom Authorized/Batal di
// purchaseOrder.blade.php's renderTabelPO(), yang juga dirakit manual di luar poCart).
function penawaransoOtoHeaderTabel2(level) {
  var html = '<th style="padding: 4px 12px;" scope="col">Oto1</th>'
    + '<th style="padding: 4px 12px;" scope="col">User Oto1</th>'
    + '<th style="padding: 4px 12px;" scope="col">Tgl Oto1</th>';
  for (var lvl = 2; lvl <= level; lvl++) {
    html += '<th style="padding: 4px 12px;" scope="col">Oto' + lvl + '</th>'
      + '<th style="padding: 4px 12px;" scope="col">User Oto' + lvl + '</th>'
      + '<th style="padding: 4px 12px;" scope="col">Tgl Oto' + lvl + '</th>';
  }
  html += '<th style="padding: 4px 12px;" scope="col">Batal</th>'
    + '<th style="padding: 4px 12px;" scope="col">User Batal</th>'
    + '<th style="padding: 4px 12px;" scope="col">Tanggal Batal</th>';
  return html;
}

function penawaransoOtoRowTabel2(row, level) {
  var html = penawaransoOtoBoolCell(row.IsOtorisasi1)
    + '<td>' + (row.OtoUser1 || '') + '</td>'
    + '<td>' + (row.TglOto1 ? penawaransoFormatTanggal(row.TglOto1) : '') + '</td>';
  for (var lvl = 2; lvl <= level; lvl++) {
    html += penawaransoOtoBoolCell(row['IsOtorisasi' + lvl])
      + '<td>' + (row['OtoUser' + lvl] || '') + '</td>'
      + '<td>' + (row['TglOto' + lvl] ? penawaransoFormatTanggal(row['TglOto' + lvl]) : '') + '</td>';
  }
  html += penawaransoOtoBoolCell(row.IsBatal)
    + '<td>' + (row.UserBatal || '') + '</td>'
    + '<td>' + (row.TglBatal ? penawaransoFormatTanggal(row.TglBatal) : '') + '</td>';
  return html;
}

// ReportTable.init()'s bindHead(thead) attaches drag/gear listeners with no matching
// removeEventListener -- calling init() again (which reinitTabel2() does, on purpose,
// to re-bind after DataTables rebuilds) is only safe against a genuinely
// NEW <thead> node each time, so the whole element is replaced here rather than just
// its innerHTML (same reasoning as so.blade.php's replaceTheadWithHeader()).
function penawaransoReplaceThead(tableSel, cols, trailingHtml) {
  var oldThead = document.querySelector(tableSel + ' thead');
  if (!oldThead || !window.ReportTable) { return; }
  var headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>')
    .replace('</tr>', trailingHtml + '</tr>');
  var newThead = document.createElement('thead');
  newThead.setAttribute('style', 'white-space:nowrap;');
  newThead.innerHTML = headRowHtml;
  oldThead.parentNode.replaceChild(newThead, oldThead);
}

// Client-side period + Otorisasi filtering, same idea as purchaseOrder.blade.php's
// poFilterStatus/poFilterOtorisasi (filtering the already-fetched array, not re-querying
// the server -- penawaransoloadall has no date-range/otorisasi params to filter by).
var penawaransoFilterOtorisasi = 'SEMUA';

function penawaransoFilterRowsTabel2(rows) {
  var awalEl = document.getElementById('tabel2_tglawal');
  var akhirEl = document.getElementById('tabel2_tglakhir');
  var awal = awalEl ? awalEl.value : '';
  var akhir = akhirEl ? akhirEl.value : '';
  return (rows || []).filter(function (row) {
    var tgl = row.Tanggal ? String(row.Tanggal).slice(0, 10) : '';
    if (awal && tgl && tgl < awal) { return false; }
    if (akhir && tgl && tgl > akhir) { return false; }
    if (penawaransoFilterOtorisasi === 'Sudah' && !Number(row.IsOtorisasi1)) { return false; }
    if (penawaransoFilterOtorisasi === 'Belum' && Number(row.IsOtorisasi1)) { return false; }
    return true;
  });
}

function renderTabel2Rows(rows) {
  if (HeaderEngine.activeKey() !== 'tabel2') { HeaderEngine.activateEngineData('tabel2'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; }); // same refs -- never .map()
  var level = Number($('#level').val()) || 1;
  var dataTampil = penawaransoFilterRowsTabel2(rows);
  var html = '';
  dataTampil.forEach(function (row) {
    html += '<tr>' + tabel2ActionsCell(row);
    cols.forEach(function (col) { html += penawaransoValueCell(row, col); });
    html += penawaransoOtoRowTabel2(row, level) + '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  penawaransoReplaceThead('#tabel2', cols, penawaransoOtoHeaderTabel2(level));
}

function reinitTabel2() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({ dom: 't', lengthChange: false, paging: false, ordering: false });
    HeaderEngine.bindEngineDom('tabel2');
  } catch (e) {
    console.error('reinitTabel2 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function penawaransoUpdateFilterBadge() {
  var badge = document.getElementById('penawaransoFilterBadge');
  if (!badge) { return; }
  badge.style.display = (penawaransoFilterOtorisasi !== 'SEMUA') ? '' : 'none';
}

// Terapkan/Reset dulu cuma reinitTabel2() (client-side, dari data yang sudah
// kepotong server-side ke Belum Otorisasi doang) -- makanya milih "Sudah
// Otorisasi" selalu kosong. Sekarang beneran manggil loadAll() supaya
// filterso ikut dikirim ke server, port 1:1 dari pola buttonFilterPRJ()
// milik perintahreturjual.blade.php.
function penawaransoTerapkanFilter() {
  penawaransoFilterOtorisasi = $('#penawaransoModalOtorisasi').val() || 'SEMUA';
  penawaransoUpdateFilterBadge();
  $('#modalFilter').modal('hide');
  loadAll();
}

function penawaransoResetFilter() {
  penawaransoFilterOtorisasi = 'SEMUA';
  $('#penawaransoModalOtorisasi').val('SEMUA');
  penawaransoUpdateFilterBadge();
  $('#modalFilter').modal('hide');
  loadAll();
}

function buttonHeaderTable(key) {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    HeaderEngine.activateEngineData(key);
    HeaderEngine.doSetHeader(1, true);
    reinitTabel2();
    alertify.success('Kolom telah direset ke tampilan default');
  }, function () {});
}

// One-time seed: load the table's saved column layout (or fall back to its
// setDefault()) before the very first render.
HeaderEngine.activateEngineData('tabel2');
HeaderEngine.doSetHeader(1);

$('#modalFilter').on('show.bs.modal', function () {
  $('#penawaransoModalOtorisasi').val(penawaransoFilterOtorisasi);
});
$('#tabel2_search').on('input', function () {
  $('#tabel2').DataTable().search($(this).val()).draw();
});
$('#tabel2_tglawal, #tabel2_tglakhir').on('change', function () {
  loadAll();
});

let listAlamatKirim = []

let selectedNoBukti = ''

let tempAddAdd = {}
let tempAddEdit = {}
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}

let dataPrintPenerimaan = []

let noBuktiUntukAdd = 0


let tipeform = ''
let tipeformitem = ''
$("#input_add_add_satminus").hide();
$("#input_add_add_isi").hide();
$("#label_add_add_isi").hide();
buttonShowHideHeader ();
console.log('==========cccc=========');



  jQuery(function($) {
    $('.input-partial-number').autoNumeric('init',
      {
        minimumValue : '0',
        // negativeSignCharacter: 'z'
      }
    );
  });


  function formatAngkaVal (angka) {
    return Number(angka.split(',').join(''))
  }

function openPrintModal(nobukti) {
  selectedNoBukti = nobukti
  $('#modalPrint').modal('show')
}

function choosePrint(type) {
  $('#modalPrint').modal('hide')

  if (type === 'default') {
    submitPrint(selectedNoBukti)
  } 
  else if (type === 'design3') {
    submitPrint2(selectedNoBukti)
  } 
}

// function buttonOtorisasi (nobukti) {

//   let akses = $("#akses_isotorisasi1").val();
//   if (!Number(akses)) {
//     alertify.warning('No access');
//     return;
//   }

//   // Update modal title and reset table
//   $('#modalOtorisasiLabel').text('Detail Otorisasi PO: ' + nobukti);
//   $('#otorisasi-table-body').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');

//   // Show modal
//   $('#modalOtorisasi').modal('show');

//   // Fetch detail data
//   let _token = $("#_token").val();

//   $.ajax({
//     url: "{!! url('penawaransogetdetail') !!}",
//     type: "POST",
//     data: { _token, nobukti },
//     success: function(res) {
//       let dataTableAdd = res.list;
//       dataCekHarga = res.list
//       console.log('dataTableAdd ' , dataTableAdd)
//       let rows = "";
//       let totalTotal = 0;
//       let saldoTotal = 0;

//       dataTableAdd.forEach((item) => {
//         rows += `<tr>
//           <td>${item.KodeBrg}</td>
//           <td>${item.NamaBrg}</td>
//           <td class="text-right">${item.Qnt ? parseFloat(item.Qnt).toFixed(2) : '0.00'}</td>
//           <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
//           <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
//           <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
//           <td class="text-right">${item.SaldoQnt ? formatAngka(parseFloat(item.SaldoQnt).toFixed(2)) : '0.00'}</td>
//           <td class="text-right">${item.SaldoRP ? formatAngka(parseFloat(item.SaldoRP).toFixed(2)) : '0.00'}</td>
//         </tr>`;

//         totalTotal += item.Total;
//         saldoTotal += item.SaldoRP;
//       });

//       rows += `<tr class="border-0">
//         <td colspan="3"></td>
//         <td class="text-right">Total:</td>
//         <td class="text-right">${formatAngka(parseFloat(totalTotal).toFixed(2))}</td>
//         <td class="text-right"></td>
//         <td class="text-right">${formatAngka(parseFloat(saldoTotal).toFixed(2))}</td>
//       </tr>`;

//       $('#otorisasi-table-body').html(rows);
//     },
//     error: function(err) {
//       $('#otorisasi-table-body').html('<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>');
//     }
//   });

//   // Handle confirm/otorisasi button ï¿½ unbind first to avoid duplicate handlers
//   $('#btn-confirm-otorisasi').off('click').on('click', function() {

//       let _token = $("#_token").val();
//       // let nobukti = $("#input")
//     console.log("SubmitOtorisasi ")
//     console.log(dataCekHarga)
//     let mssg = ''
//     $.ajax({
//       url: "{!! url('purchaseordercekhargaoto') !!}",
//       type: "POST",
//       data: { _token, tempData: dataCekHarga },
//       success: function(res) {
//         console.log('rescekharga' ,res)

//         for (var i = 0; i < res.length; i++) {
//           console.log('1',i, mssg)

//           console.log('a',i)
//           let xtempx = 1;
//           if (mssg) {
//             mssg += ' , '
//           }
//           // if ()
//           // res.forEach((item, i) => {

//           if (res[i][0].Ket != 'lanjut') {
//             mssg += `
//               Barang ${res[i][0].kodebrg} - ${res[i][0].Ket}
//             `
//           }

//           // });
//           console.log(i, mssg)




//           // alertify.confirm('Konfirmasi Otorisasi', 'Apakah yakin ingin menghapus item ' + String(i) + ' ?',
//           //     function() {
//           //       console.log('yes')
//           //       let xtempx = 1;
//           //     }
//           //   ,function(){
//           //     console.log('no')
//           //       let xtempx = 0;
//           //   };
//           //   if (xtempx == 0) {
//           //     break;
//           //   })

//         }


//         console.log('mssg sini' , mssg)
//         if (mssg) {
//           console.log('mssg yes')
//           alertify.confirm('Konfirmasi Otorisasi', mssg + '. Lanjut otorisasi ?',
//               function() {
//                 console.log('yes')
//                 // return
//                 $.ajax({
//                   url: "{!! url('poupdateotorisasi') !!}",
//                   type: "POST",
//                   data: { _token, nobukti },
//                   success: function(res) {
//                     console.log('Tesresmaxol' , res)
//                     $('#modalOtorisasi').modal('hide');
//                     alertify.success('Berhasil update otorisasi');
//                     loadAll();
//                   },
//                   error: function(err) {
//                     console.log(err);
//                     alertify.warning('Terjadi kesalahan silahkan refresh browser');
//                   }
//                 });
//               }
//             ,function(){
//               console.log('no')
//             });
//             // if (xtempx == 0) {
//             //   break;
//             // })




//         } else {
//           console.log('else')
//           // return
//           console.log({ _token, nobukti })
//           $.ajax({
//             url: "{!! url('poupdateotorisasi') !!}",
//             type: "POST",
//             data: { _token, nobukti },
//             success: function(res) {
//               console.log('resoto',res)
//               $('#modalOtorisasi').modal('hide');
//               alertify.success('Berhasil update otorisasi');
//               loadAll();
//             },
//             error: function(err) {
//               console.log(err);
//               alertify.warning('Terjadi kesalahan silahkan refresh browser');
//             }
//           });
//         }

//       },
//       error: function(err) {
//         console.log(err);
//         alertify.warning('Terjadi kesalahan silahkan refresh browser');
//       }
//     });


//     // return



//   });
// }

function buttonOtorisasi (nobukti) {
  console.log(nobukti)

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

    let _token = $("#_token").val();

  alertify.confirm('Otorisasi Otorisasi', ' Otorisasi penawaran SO ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('penawaransoupdateotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

          },
          success: function(res) {
            alertify.success('Berhasil update otorisasi')
            loadAll()

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


function buttonBatalOtorisasi (nobukti) {
  console.log(nobukti)

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.prompt('Batal Otorisasi',"Masukkan keterangan batal otorisasi nomor  " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          $.abort();
        }
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('penawaransoupdatebatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
          pket :value

          },
          success: function(res) {
            alertify.success('Berhasil batal otorisasi')
            loadAll()

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }

    ,function(){
      console.log('no')
      alertify.error("Action cancelled");
    });

}

function onChangeCatatan () {

  if (tipeform == 'edit') {
    let value  = $("#input_add_keterangan").val()
    onChangeHeader('Keterangan' , value)

  }

}
function onChangeNoPO () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_noso").val()
    onChangeHeader('NoPesanan' , value)
  }
}

function onChangeTglprcust () {
  if (tipeform == 'edit') {
    let value  = $("#onChangeTglprcust").val()
    onChangeHeader('TglKirim' , value)
  }
}

function onChangeTipePPN () {
  console.log('onChangeTipePPN')
  if (tipeform == 'edit') {
    let value = $("#input_add_tipeppn").val()
    console.log(value)
    onChangeHeader('TipePPn' , value)
    onChangeHeader('PPN' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeDP () {
  console.log('onChangeDP')
  if (tipeform == 'edit') {
    let value = $("#input_add_nopocust").val()
    console.log(value)
    onChangeHeader('DP' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeDraftPO () {
  console.log('onChangeDraftPO')
  if (tipeform == 'edit') {
    let value = $("#input_add_draftpo").val()
    console.log(value)
    onChangeHeader('PPO' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeHari () {
  console.log('onChangeHari')
  if (tipeform == 'edit')
  {
    let value = $("#input_add_hari").val()
    console.log(value)
    onChangeHeader('HARI' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }
}

function onChangeInputAddDisc () {
    // document.getElementById("input_add_discrp").value = '0.00'
    console.log('onChangeDisc')
    if (tipeform == 'edit') {
      let value = $("#input_add_disc").val()
      console.log(value)
      onChangeHeader('DISC' , value)
      refreshUpdateHeader()
      let nobukti = $("#input_add_nobukti").val()
      refreshDataTableAdd(nobukti)
    }
}

function onChangeInputAddDiscRp () {
    // document.getElementById("input_add_disc").value = '0.00'
    console.log('onChangeDiscRp')
      if (tipeform == 'edit') {
        let value = $("#input_add_discrp").val()
        console.log(dataHeaderAdd)
        let x = Number(value) / Number(dataHeaderAdd.TOtSubtotalRP) * 100
        console.log(x)
        console.log(value)
        onChangeHeader('DISC' , x)
        refreshUpdateHeader()
        let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)
      }
}

function refreshUpdateHeader ()
{
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('pospupdatepo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      // alertify.success('update header berhasil')
      // return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeHeaderSP (field, value , field1 = null , value2 = null)
{
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('soonchangeheadersp') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success('update header berhasil')
      return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeHeader (field, value) {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('poonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success(`update ${field} berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}






function submitAddAdd (pchoice) {

    console.log('submitAddAdd')

    let checkDate = new Date($("#input_add_tanggal").val())

    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value

    if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
        alertify.warning("Tanggal tidak sesuai periode");
        return
    }

    let TglJatuhTempo = new Date($("#input_add_tanggal").val())

    let hari = $("#input_add_hari").val()

    TglJatuhTempo.setDate(TglJatuhTempo.getDate() + Number(hari))
    console.log(TglJatuhTempo)

    let jmlrecord = 0
    if (dataTableAdd.length) {
      jmlrecord = 1
    }

//    kodegdg, kodeexp, 
// userid, tglinput, nopesanan, tgkirim, masaberlaku , tglbatal , urutmaster , pembayaran , franco , delivery , validitas , namapic  , namapic2 , 
// freight , ketrevisi   , kodekebunh 

// qnt2,isi,idmaster,isVerf,nmsat,tipeso,namamerkso,TglVerf,UserVerf,jmlrecord
    

    let _token  = $("#_token").val()
    let choice = pchoice
    
    let nobukti = $("#input_add_nobukti").val()

    let tanggal = $("#input_add_tanggal").val()
    let tglrevisi = $("#input_add_tglrevisi").val()
    let tglprcust = $("#input_add_tglprcust").val()
    let kodesupp = $("#input_add_kodesupplier").val()
    let tipedisc =0

    let catatan = $("#input_add_note").val()
    let keterangan = $("#input_add_alamatsupplier").val()


    let kodevls = $("#input_add_valas").val()
    let kurs = $("#input_add_kurs").val()
    let ppn = $("#input_add_tipeppn").val()
    let tipebayar = $("#input_add_pembayaran").val()
    let urut = 0


  if (choice != 'I'){
     urut = $purut }


console.log(choice,nobukti,urut,$purut)



    let kodebrg =  $("#input_add_add_kodebarang").val()
    let qnt =  $("#input_add_add_qty").val()
    let nosat =  $("#input_add_add_nosat").val()

    let harga = $("#input_add_add_harga").val()
    let discp1 = $("#input_add_add_discpersen1").val()
    let disctot = $("#input_add_add_discrp").val()
    let noppl = $("#input_add_add_noPPL").val()
    let urutppl = $("#input_add_add_urutPPL").val()
    

    let discpdet2 = $("#input_add_add_discpersen2").val()
    let discpdet3 = $("#input_add_add_discpersen3").val()

    let namabrg =  $("#input_add_add_namabarang").val()


    let ttd = $("#input_add_noso").val()

    // let NOPOCUST = $("#input_add_nopocust").val()

    let ketdet = $("#input_add_add_keteranganbarang").val()

    let pembayaran = $("#input_add_pembayaran").val()
    let franco = $("#input_add_alamatkirim").val()
    let delivery = $("#input_add_ekspedisi").val()  
    let validitas = $("#input_add_keterangan").val()

    let namapic =$("#input_add_up").val()

    let namapic2 =$("#input_add_up2").val()

    let freight =$("#input_add_freight").val()
    let ketrevisi =$("#input_add_ketrevisi").val()
    let namacustomer =$("#input_add_namasupplier").val()
    let idmaster=0
    let norpr=''
    let urutrpr =0

    // let nmsat=satuan
    let tipeso=$("#input_add_add_tipeso").val()
    let namamerkso=$("#input_add_add_merkso").val()
    let kodekebunh=$("#input_add_lokasipenerima").val()
    let satminus=$("#input_add_add_satminus").val()
    





    // let date1 = ""
    // if (TglJatuhTempo) {
    //     let date = new Date(TglJatuhTempo);
    //     let day = ("0" + date.getDate()).slice(-2);
    //     let month = ("0" + (date.getMonth() + 1)).slice(-2);
    //     date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    //   }

    // TglJatuhTempo  = date1



    console.log(tempAddAdd)



    let satuan = ''
    let qnt1 = 0
    let isi = 0

    if (kodebrg== '-')
    {nosat=1 
     isi= $("#input_add_add_isi").val()

    }
    else {
          cekSatuanBarang(kodebrg)    
          if (nosat == 1) {
            qnt1 = qnt * tempSatuanBarang[0].ISI1
            satuan = tempSatuanBarang[0].SAT1
            isi = tempSatuanBarang[0].ISI1
          }
          if (nosat == 2) {
            qnt1 = qnt * tempSatuanBarang[0].ISI2
            satuan = tempSatuanBarang[0].SAT2
            isi = tempSatuanBarang[0].ISI2
          }
          if (nosat == 3) {
            qnt1 = qnt * tempSatuanBarang[0].ISI3
            satuan = tempSatuanBarang[0].SAT3
            Isi = tempSatuanBarang[0].ISI3
          }
    }


    if (!catatan) {
      catatan = '-'
    }

    console.log({
      _token,
        nobukti,
tanggal ,kodesupp, kodevls, kurs, ppn, tipebayar, hari,keterangan ,catatan, tipedisc,
  pembayaran , franco , delivery , validitas , namapic , ttd , namapic2 , 
freight , ketrevisi , tglrevisi , tglprcust , kodekebunh , urut

,kodebrg,namabrg,qnt,nosat,isi,harga,discp1,disctot,idmaster,norpr,urutrpr,ketdet,satuan,tipeso,namamerkso,jmlrecord,satminus,namacustomer
    })

    console.log('==========' , Number(nosat))

    if (Number(hari) < 0 || Number(qnt) < 0 || Number(harga) < 0 || Number(disctot) < 0)  {
      alertify.warning("Angka negatif")
      return
    }


  let xppn=0
  let xharga=0
  if  ( $("#input_add_tipeppn").val()==2) {
      xppn= harga * 0.1
  } 

 xharga= harga -  $("#input_add_discrp").val() - xppn



console.log('==========start ============================================') 
$.ajax({
  url: "{!! url('penawaransospadd') !!}",
  type: "post",
  async: false,
  data: {
    _token,
    choice,
    nobukti,
tanggal ,kodesupp, kodevls, kurs, ppn, tipebayar, hari,keterangan ,catatan, tipedisc, 
 pembayaran , franco , delivery , validitas , namapic , ttd , namapic2 , 
freight , ketrevisi , tglrevisi , tglprcust , kodekebunh , urut

,kodebrg,namabrg,qnt,nosat,isi,harga,discp1,disctot,idmaster,norpr,urutrpr,ketdet,satuan,tipeso,namamerkso,jmlrecord,satminus,namacustomer
    


  },
  success: function(res) {
    console.log(res)
    if (res == 1) {

      loadAll()
      tipeform = 'edit'
      document.getElementById("buttonAddListPelanggan").disabled = true
      $('#divhargaterakhir').hide();
      $('#divStockProyeksi').hide();
      cleanFormAddAdd()

      refreshDataTableAdd(nobukti)
      if (choice == 'U'){
      $('.showhide').hide();
      alertify.success('Berhasil koreksi item')
      }
      else {
      alertify.success('Berhasil menambah item')
      }
    }
    if(res == 2) {
      setNewNoBukti(1)
      alertify.warning('Nobukti telah direfresh silahkan submit ulang')
    }

  },
  error: function (err) {
    console.log(err)
    alertify.warning('Terjadi kesalahan silahkan refresh browser')
  }

})



              
      
}




function submitAddEdit () {

  console.log('submitAddEdits')

  let checkDate = new Date($("#input_add_tanggal").val())
  let TglJatuhTempo = new Date($("#input_add_tanggal").val())

  let hari = $("#input_add_hari").val()

  TglJatuhTempo.setDate(TglJatuhTempo.getDate() + Number(hari))
  console.log(TglJatuhTempo)

  let Jmlrecord = 0
  if (dataTableAdd.length) {
    Jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let Choice = "U"
  let NoBukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val()
  let Tanggal = $("#input_add_tanggal").val()
  let KodeSupp = $("#input_add_kodesupplier").val()

  let Keterangan = $("#input_add_keterangan").val()
  //faktursupp kosong
  let KodeVls = $("#input_add_valas").val()
  let Kurs = $("#input_add_kurs").val()
  let PPn = $("#input_add_tipeppn").val()
  let TipeBayar = $("#input_add_pembayaran").val()
  let Hari = $("#input_add_hari").val()
  //TipeDisc kosong
  //Disc = 0
  //DiscRp
  let Urut = tempAddEdit.Urut
  let KodeBrg =  $("#input_add_add_kodebarang").val()
  let Qnt =  $("#input_add_add_qty").val()
  let NoSat =  $("#input_add_add_nosat").val()
  //satuan
  //isi teko dbbarang
  let Harga = $("#input_add_add_harga").val()
  let DiscP = $("#input_add_add_discpersen1").val()
  let DiscTot = $("#input_add_add_discrp").val()
  let NoPPL = $("#input_add_add_noPPL").val()
  //isclose kosong
  //isCloseD kosong
  //catatan kosong
  //IsExp = false
  //Tolerate kosong
  let UrutPPL = $("#input_add_add_urutPPL").val()
  // let Kodegdg = $("#input_add_kodealamatkirim").val()
  let Discpdet2 = $("#input_add_add_discpersen2").val()
  let Discpdet3 = $("#input_add_add_discpersen3").val()
  //discpdet4 kosong
  //discpdet5 kosong
  //flagtipe 1
  let NamaBrg =  $("#input_add_add_namabarang").val()
  //isjasa = 0
  //pFirst = 0
  let pFOC = $("#input_add_add_foc").val()
  let Noso = $("#input_add_noso").val()
  //jmlrecord no bukti duplikat
  let NOPOCUST = $("#input_add_nopocust").val()
  //iduser = $user->name
  //pJasa = 0
  //npph23 0
  //perkiraan
  //satX
  //cost
  //subcost
  // let TglKirim = $("#input_add_tanggalkirim").val()
  //pph21
  // let NOPNw = $("#input_add_add_nopnwpo").val()
  let UrutPNW = 0
  let HrgAwal = $("#input_add_add_hargaAwal").val()
  let KeteranganBarang = $("#input_add_add_keteranganbarang").val()

  // console.log(kodesupplier,'*')
  // if (!kodesupplier || !kodebackoffice || !nobukti || !valas || !kodealamatkirim || !kodelokasipenerima) {
  //   alertify.warning("Data tidak lengkap")
  //   return
  //}

  if (!NoPPL){
    NoPPL = ''
  };

  let date1 = ""
  if (TglJatuhTempo) {
      let date = new Date(TglJatuhTempo);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    }

  TglJatuhTempo  = date1

  // let tipediskon = 0
  // if (disc) {
  //   tipediskon = 1
  // }
  // if (discrp) {
  //   tipediskon = 1
  // }

  console.log(tempAddEdit)

  let Satuan = ''
  let qnt1 = 0
  let Isi = 0
  if (NoSat == 1) {
    qnt1 = Qnt * tempAddEdit.Isi
    Satuan = tempAddEdit.Satuan
    Isi = tempAddEdit.Isi
  }

  if (NOPNw == '-') {
    UrutPNW = 0
  }

  if (!Keterangan) {
    Keterangan = '-'
  }

  console.log({
    _token,
    Choice,
    NoBukti,
    NoUrut,
    Tanggal,
    TglJatuhTempo,
    KodeSupp,
    // Handling,
    KodeExp,
    Keterangan,
    // FakturSupp,
    KodeVls,
    Kurs,
    PPn,
    TipeBayar,
    Hari,
    // TipeDisc,
    // Disc,
    // DiscRp,
    Urut,
    KodeBrg,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Harga,
    DiscP,
    DiscTot,
    NoPPL,
    // IsClose,
    // IsCloseD,
    // Catatan,
    // IsExp,
    // Tolerate,
    UrutPPL,
    Kodegdg,
    Discpdet2,
    Discpdet3,
    // Discpdet4,
    // Discpdet5,
    // FlagTipe,
    NamaBrg,
    // IsJasa,
    // pFirst,
    pFOC,
    Noso,
    Jmlrecord,
    NOPOCUST,
    // IdUser,
    // pJasa,
    // NPPH23,
    // PERKIRAAN,
    // SatX,
    // COST,
    // SUBCOST,
    TglKirim,
    // PPH21,
    NOPNw,
    UrutPNW,
    HrgAwal,
    KeteranganBarang
  })

  console.log('==========' , Number(NoSat))
  if (!KodeBrg || !Kodegdg) {
    alertify.warning("Data belum lengkap")
    return
  }
  if (Number(Hari) < 0 || Number(Qnt) <= 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
    alertify.warning("Angka negatif")
    return
  }





  let xppn=0
  let xharga=0
  if  ( $("#input_add_tipeppn").val()==2) {
      xppn= Harga * 0.1
  } 
 xharga= Harga -  $("#input_add_discrp").val() - xppn
  // console.log(kodebarang,tanggal,xharga,nosat,choice)
   console.log(KodeBrg,Noso,xharga,NoSat)


   $.ajax({
    url: "{!! url('checkhargaddd') !!}",
    type: "get",
    async: false,
    data: { Noso,KodeBrg,xharga,NoSat
    },
    success: function(res) {
    console.log ('=============================>',res)
    flagharga = res
    console.log ('=============================>',flagharga)
    if (flagharga !='lanjut'){
         alertify.confirm('' + flagharga + ' ?',
          function() { 

          


                                                          $.ajax({
                                                            url: "{!! url('pospadd') !!}",
                                                            type: "post",
                                                            async: false,
                                                            data: {
                                                              _token,
                                                              Choice,
                                                              NoBukti,
                                                              NoUrut,
                                                              Tanggal,
                                                              TglJatuhTempo,
                                                              KodeSupp,
                                                              // Handling,
                                                              KodeExp,
                                                              Keterangan,
                                                              // FakturSupp,
                                                              KodeVls,
                                                              Kurs,
                                                              PPn,
                                                              TipeBayar,
                                                              Hari,
                                                              // TipeDisc,
                                                              // Disc,
                                                              // DiscRp,
                                                              Urut,
                                                              KodeBrg,
                                                              Qnt,
                                                              NoSat,
                                                              Satuan,
                                                              Isi,
                                                              Harga,
                                                              DiscP,
                                                              DiscTot,
                                                              NoPPL,
                                                              // IsClose,
                                                              // IsCloseD,
                                                              // Catatan,
                                                              // IsExp,
                                                              // Tolerate,
                                                              UrutPPL,
                                                              Kodegdg,
                                                              Discpdet2,
                                                              Discpdet3,
                                                              // Discpdet4,
                                                              // Discpdet5,
                                                              // FlagTipe,
                                                              NamaBrg,
                                                              // IsJasa,
                                                              // pFirst,
                                                              pFOC,
                                                              Noso,
                                                              Jmlrecord,
                                                              NOPOCUST,
                                                              // IdUser,
                                                              // pJasa,
                                                              // NPPH23,
                                                              // PERKIRAAN,
                                                              // SatX,
                                                              // COST,
                                                              // SUBCOST,
                                                              TglKirim,
                                                              // PPH21,
                                                              NOPNw,
                                                              UrutPNW,
                                                              HrgAwal,
                                                              KeteranganBarang

                                                            },
                                                            success: function(res) {
                                                              console.log('resspsoaddedit', res)
                                                              loadAll()

                                                              // lockFormAdd()
                                                              $('.showhide').hide();
                                                              refreshDataTableAdd(NoBukti)

                                                              alertify.success('Berhasil edit item')

                                                            },
                                                            error: function (err) {
                                                              console.log(err)
                                                              alertify.warning('Terjadi kesalahan silahkan refresh browser')
                                                            }

                                                          })

                              }

                              
                  ,function(){
                console.log(' cancel harga minimal')
        
                  return
                });


              }else{

                 $.ajax({
                                                            url: "{!! url('pospadd') !!}",
                                                            type: "post",
                                                            async: false,
                                                            data: {
                                                              _token,
                                                              Choice,
                                                              NoBukti,
                                                              NoUrut,
                                                              Tanggal,
                                                              TglJatuhTempo,
                                                              KodeSupp,
                                                              // Handling,
                                                              KodeExp,
                                                              Keterangan,
                                                              // FakturSupp,
                                                              KodeVls,
                                                              Kurs,
                                                              PPn,
                                                              TipeBayar,
                                                              Hari,
                                                              // TipeDisc,
                                                              // Disc,
                                                              // DiscRp,
                                                              Urut,
                                                              KodeBrg,
                                                              Qnt,
                                                              NoSat,
                                                              Satuan,
                                                              Isi,
                                                              Harga,
                                                              DiscP,
                                                              DiscTot,
                                                              NoPPL,
                                                              // IsClose,
                                                              // IsCloseD,
                                                              // Catatan,
                                                              // IsExp,
                                                              // Tolerate,
                                                              UrutPPL,
                                                              Kodegdg,
                                                              Discpdet2,
                                                              Discpdet3,
                                                              // Discpdet4,
                                                              // Discpdet5,
                                                              // FlagTipe,
                                                              NamaBrg,
                                                              // IsJasa,
                                                              // pFirst,
                                                              pFOC,
                                                              Noso,
                                                              Jmlrecord,
                                                              NOPOCUST,
                                                              // IdUser,
                                                              // pJasa,
                                                              // NPPH23,
                                                              // PERKIRAAN,
                                                              // SatX,
                                                              // COST,
                                                              // SUBCOST,
                                                              TglKirim,
                                                              // PPH21,
                                                              NOPNw,
                                                              UrutPNW,
                                                              HrgAwal,
                                                              KeteranganBarang

                                                            },
                                                            success: function(res) {
                                                              console.log('resspsoaddedit', res)
                                                              loadAll()

                                                              // lockFormAdd()
                                                              $('.showhide').hide();
                                                              refreshDataTableAdd(NoBukti)

                                                              alertify.success('Berhasil edit item')

                                                            },
                                                            error: function (err) {
                                                              console.log(err)
                                                              alertify.warning('Terjadi kesalahan silahkan refresh browser')
                                                            }

                        })
              }

    }
})
}










function onChangeInputAddPembayaran () {
  console.log("onChangeInputAddPembayaran")
  let check = Number($("#input_add_pembayaran").val())
  console.log(typeof check)
  console.log(check)

  if (dataTableAdd.length) {

    onChangeHeader('TIPEBAYAR' , check)
  }
  let nobukti = $("#input_add_nobukti").val()
  console.log('len',dataTableAdd.length)
  if (check) {
    let _token = $("#_token").val();
    let kodesupplier = $("#input_add_kodesupplier").val();

    $.ajax({
      url: "{!! url('socekkredithari') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        kodesupplier
      },
      success: function(res) {
        console.log(res)
        if(res.length && res[0].harihutpiut) {
          document.getElementById("input_add_hari").value = res[0].harihutpiut

          if (dataTableAdd.length) {
            console.log('masokk')
            onChangeHeader('HARI' , res[0].harihutpiut)
            refreshUpdateHeader()
            // let nobukti = $("#input_add_nobukti").val()
            refreshDataTableAdd(nobukti)

          }
        }

      }})

  } else {
    document.getElementById("input_add_hari").value = 0
    // console.log('onChangeHari')
    if (tipeform == 'edit') {
      console.log('len', dataTableAdd.length)
      console.log(value)
      // onChangeHeader('TIPEBAYAR' , check)
      if (dataTableAdd.length) {
        console.log('masokk 2')
        onChangeHeader('HARI' , 0)
        refreshUpdateHeader()
        // let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)

      }
    }
  }
}




function onChangeInputAddAddDisc () {
  console.log("onChangeInputAddAddDisc")
  let harga = $("#input_add_add_harga").val();

  if (!Number(harga)) {

    document.getElementById("input_add_add_discrp").value = '0.00'
    return
  }

  let disc = $("#input_add_add_disc").val();
  let discRp = Number(harga) * Number(disc) / 100
  document.getElementById("input_add_add_discrp").value = parseFloat(discRp).toFixed(2)

}

function onChangeInputAddAddHarga () {
  document.getElementById("input_add_add_discrp").value = '0.00'
  document.getElementById("input_add_add_discpersen1").value = '0.00'
  document.getElementById("input_add_add_discpersen2").value = '0.00'
  document.getElementById("input_add_add_discpersen3").value = '0.00'


  document.getElementById("input_add_add_hargaAwal").value = document.getElementById("input_add_add_harga").value
}

function onChangeInputAddEditHarga () {
  document.getElementById("input_add_edit_discrp").value = '0.00'
  document.getElementById("input_add_edit_disc").value = '0.00'
}

function onChangeInputAddAddDiscRp () {
  console.log("onChangeInputAddAddDiscRp")
  let harga = $("#input_add_add_harga").val();

  if (!Number(harga)) {

    document.getElementById("input_add_add_disc").value = '0.00'
    return
  }

  let discRp = $("#input_add_add_discrp").val();
  let disc = Number(discRp) / Number(harga) * 100
  document.getElementById("input_add_add_disc").value = parseFloat(disc).toFixed(2)
}

function buttonAddAddItem () {
  tipeformitem = 'add'
  $('.showhide').hide();

  $('#divhargaterakhir').hide();

  cleanFormAddAdd()
  document.getElementById("buttonAddAddListBarang").disabled = false
  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
  document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function showTableHargaTerakhir () {
  if ( $("#divStockProyeksi").is(':visible'))
  {
    $('#divStockProyeksi').hide();
  }

  if (!$("#divhargaterakhir").is(':visible'))
  {
    $('#divhargaterakhir').show();
  } else
  {
    $('#divhargaterakhir').hide();
  }
}

function showTableStockProyeksi () {
  if ($("#divhargaterakhir").is(':visible'))
  {
    $('#divhargaterakhir').hide();
  }

  if (!$("#divStockProyeksi").is(':visible'))
  {
    $('#divStockProyeksi').show();
  } else
  {
    $('#divStockProyeksi').hide();
  }
}

function buttonAddEditItem (i) {
  tipeformitem = 'edit'
  let _token = $("#_token").val();
  $('.showhide').hide();
  // cleanFormAddAdd()
  console.log(dataTableAdd[i])
  tempAddEdit = dataTableAdd[i]

  console.log(typeof tempAddEdit.Harga);
  console.log(tempAddEdit.Harga + " ini harga")

  let selectOption = ''
  if (tempAddEdit.Satuan) {
    selectOption += `<option value=1 selected>${tempAddEdit.Satuan}</option>`
  }

  if (tempAddEdit.NoPNW == ''){
    tempAddEdit.NoPNW = '-'
  }

  if (tempAddEdit.KodeBrg=='-'){
  // {document.getElementById("input_add_add_isi").show()
  //  document.getElementById("label_add_add_isi").show()
  //  document.getElementById("input_add_add_nosatminus").show()
  $("#input_add_add_satminus").show();
  $("#input_add_add_isi").show();
  $("#label_add_add_isi").show();
  $("#input_add_add_nosat").hide();

  }
  else{
 $("#input_add_add_satminus").hide();
  $("#input_add_add_isi").hide();
  $("#label_add_add_isi").hide();
  $("#input_add_add_nosat").show();

  }

   $purut  = tempAddEdit.Urut
  document.getElementById("input_add_add_jasa").value = tempAddEdit.Isjasa
  document.getElementById("input_add_add_foc").value = tempAddEdit.PFOC
  document.getElementById("input_add_add_isi").value = tempAddEdit.Isi
  document.getElementById("input_add_add_kodebarang").value = tempAddEdit.KodeBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_namabarang").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_discpersen1").value = Number(tempAddEdit.DiscP1) ?  tempAddEdit.DiscP1 : '0.00'
  document.getElementById("input_add_add_discpersen2").value = Number(tempAddEdit.Discp2) ?  tempAddEdit.Discp2 : '0.00'
  document.getElementById("input_add_add_discpersen3").value = Number(tempAddEdit.Discp3) ?  tempAddEdit.Discp3 : '0.00'
  document.getElementById("input_add_add_qty").value = parseFloat(tempAddEdit.Qnt).toFixed(2)
  document.getElementById("input_add_add_nosat").innerHTML = selectOption
  document.getElementById("input_add_add_harga").value = Number(tempAddEdit.Harga) ? parseFloat(tempAddEdit.Harga).toFixed(2) : '0.00'
  document.getElementById("input_add_add_discrp").value = Number(tempAddEdit.DISCTOT) ? parseFloat(tempAddEdit.DISCTOT).toFixed(2) : '0.00'
  document.getElementById("input_add_add_noPPL").value = tempAddEdit.NoPPL
  document.getElementById("input_add_add_urutPPL").value = tempAddEdit.UrutPPL
  document.getElementById("input_add_add_keteranganbarang").value = tempAddEdit.KeteranganBarang
  document.getElementById("input_add_add_hargaAwal").value = tempAddEdit.Hrgawal
  document.getElementById("input_add_add_tipeso").value = tempAddEdit.tipeso
  document.getElementById("input_add_add_merkso").value = tempAddEdit.namamerkso
  document.getElementById("input_add_add_satminus").value = tempAddEdit.nmsat

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${item.QNT}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  $('#divhargaterakhir').hide();
  $('#divStockProyeksi').hide();
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function closeShowHideAdd () {
  $('.showhide').hide();

}


function setNewNoBukti (tipePpn) {
  let _token = $("#_token").val();

  if (tipePpn == 1){
  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      kode:'PNW',
      _token
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
  } else if (tipePpn != 1){
  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      kode:'PNW',
      _token
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})}

}


function buttonAddListPIC () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodesupplier").val();

  if (!kodecustsupp) {
    alertify.warning("Isi pelanggan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('solistpic') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.kodepic}</td>
        <td>${item.nama}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPIC('${item.kodepic}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });

      document.getElementById("tabel_data_add_list_pic").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPIC').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddAddListPWO () {

  let _token = $("#_token").val();
  let noSo = $("#input_add_noso").val();

  if (!noSo) {
    alertify.warning("Isi Nomor SO terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('polistpwo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      noSo
    },
    success: function(res) {
      let rowTable = `
        <tr>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPWO('-' , '-')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td>${item.no_bukti}</td>
          <td>${item.tanggal}</td>
          <td>${item.supplier}</td>
          <td>${item.kode}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.qty}</td>
          <td>${item.satuan}</td>
          <td>${item.harga}</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPWO('${item.no_bukti}' , '${item.tanggal}')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_pwo").innerHTML = rowTable

      document.getElementById("namaHeaderTable").textContent = 'Nomor Penawaran PO'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPWO').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function buttonAddAddListBarang () {


  console.log('buttonAddAddListBarang')

  let _token = $("#_token").val();
  let foc = $("#input_add_add_foc").val();
  let noSo = $("#input_add_noso").val();
  let search = $("#input_add_add_kodebarang").val();

  if (search == '-') {
    $("#input_add_add_namabarangasli").val('');
    $("#input_add_add_namabarang").val('').focus();
    $("#input_add_add_nosat").hide();
    $("#input_add_add_satminus").show();
    $("#input_add_add_isi").show();
    $("#label_add_add_isi").show();


} else {

   $("#input_add_add_nosat").show();
    $("#input_add_add_satminus").hide();
    $("#input_add_add_isi").hide();
    $("#label_add_add_isi").hide();
                console.log(noSo)
                console.log('=======')
                console.log(noBuktiUntukAdd)
                $('#tabel_add_list_barang_foc').DataTable().destroy();

                  $.ajax({
                    url: "{!! url('penawaransolistbarangfoc') !!}",
                    type: "get",
                    async: false,
                    data: {  search
                    },
                    success: function(res) {
                      console.log(res)
                      let rowTable = ``
                      dataAddAddListItem = res
                      dataAddAddListItem.forEach((item, i) => {
                        rowTable += `
                        <tr>
                          <td style="white-space:nowrap;" class="text-center">
                            <button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangFOCPlus(${i})" type="button" ><i class="bi bi-plus"></i></button>
                          </td>
                          <td style="white-space:nowrap;">${item.Kodebrg}</td>
                          <td style="white-space:nowrap;">${item.NamaBrg}</td>
                          <td style="white-space:nowrap;">${item.partNumber}</td>
                          <td style="white-space:nowrap;">${item.NamaMerk}</td>
                        </tr>`
                      });

                      document.getElementById("tabel_data_add_list_barang_foc").innerHTML = rowTable

                      $("#tabel_add_list_barang_foc").DataTable({
                        "lengthChange": false,
                          "paging": true ,
                      });
                      document.getElementById("namaHeaderTable").textContent = 'Barang (FOC)'
                      $('.showhidemodalbodyadd').hide();
                      $('#modalBodyAddAddListBarangFOC').show();

                      $("#form").modal('toggle')

                    },
                    error: function (err) {
                      console.log(err)
                      alertify.warning('Terjadi kesalahan silahkan refresh browser')
                    }

                  })


 }

}









function buttonAddListNoSO () {

  let _token = $("#_token").val();
  console.log('list ttd===========')

  $('#tabel_add_list_Ttd').DataTable().destroy();
  $.ajax({
    url: "{!! url('penawaransolistttd') !!}",
    type: "get",
    async: false,
    data: {
      _token
    },
    success: function(res) {
         console.log(res)
      let rowTable = `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickNoSO('-' , '-')" type="button" ><i class="bi bi-plus"></i></button></td>
          <td>-</td>
          <td>-</td>
   
          </tr>`

      listNoSo = res

      listNoSo.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickNoSO('${item.boffice}' , '${item.Nama}')" type="button" ><i class="bi bi-plus"></i></button></td>

          <td>${item.boffice}</td>
          <td>${item.Nama}</td>
          </tr>`
      });

      document.getElementById("tabel_data_add_list_Ttd").innerHTML = rowTable
      $("#tabel_add_list_Ttd").DataTable({
        "lengthChange": false,
        "paging": true,
      });
      document.getElementById("namaHeaderTable").textContent = 'Ttd'
      $('.showhidemodalbodyadd').hide();
      console.log('hhhhhhhhhhhhhhhhhhhhhhhhh')
      $('#modalBodyAddListTtd').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}






// function buttonAddListGudang () {

//   let _token = $("#_token").val();

//   $('#tabel_add_list_alamatkirim').DataTable().destroy();
//   $.ajax({
//     url: "{!! url('polistgudang') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token
//     },
//     success: function(res) {
//       let rowTable = ``

//       listAlamatKirim = res

//       listAlamatKirim.forEach((item, i) => {
//         rowTable += `
//         <tr>
//         <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickAlamatKirim(${i} )" type="button" ><i class="bi bi-plus"></i></button></td>
//         <td>${item.KODEGDG}</td>
//         <td>${item.NAMA}</td>
//         <td>${item.Alamat}</td>

//         </tr>`
//       });

//       document.getElementById("tabel_data_add_list_alamatkirim").innerHTML = rowTable
//       $("#tabel_add_list_alamatkirim").DataTable({
//         "lengthChange": false,
//         "paging": true,
//       });

//       document.getElementById("namaHeaderTable").textContent = 'Dikirim Ke'

//       $('.showhidemodalbodyadd').hide();
//       $('#modalBodyAddListAlamatKirim').show();

//       $("#form").modal('toggle')

//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//     }

//   })

// }


function buttonAddListLokasiPenerima () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodesupplier").val();
  $('#tabel_add_list_lokasipenerima').DataTable().destroy();

  $.ajax({
    url: "{!! url('penawaransolistlokasipenerima') !!}",
    type: "post",
    async: false,
    data: {
      _token,kodecustsupp
    },
    success: function(res) {
      let rowTable = `<tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickLokasiPenerima('-' , '-' )" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>-</td>
        <td>-</td>

        </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickLokasiPenerima('${item.KodeCustsupp}' , '${item.NamaCust}' )" type="button"><i class="bi bi-plus"></i></button></td>
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_Kebun").innerHTML = rowTable
      $("#tabel_add_list_Kebun").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      document.getElementById("namaHeaderTable").textContent = 'Lokasi Penerima'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListKebun').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListValas () {

  $('#tabel_add_list_valas').DataTable().destroy();
  $.ajax({
    url: "{!! url('polistvalas') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickValas('${item.kodevls}' , '${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}' )" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.kodevls}</td>
        <td>${item.namavls}</td>
        <td>${formatAngka(item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00')}</td>

        </tr>`
      });

      document.getElementById("tabel_data_add_list_valas").innerHTML = rowTable
      $("#tabel_add_list_valas").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      document.getElementById("namaHeaderTable").textContent = 'Valas'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListValas').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListPelanggan ()
{
  $('#tabel_add_list_pelanggan').DataTable().destroy();

  $.ajax({
    url: "{!! url('penawaransolistpelanggan') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:10px;" onclick="buttonAddPickPelanggan('${item.KodeCustSupp}' , '${item.NamaCustSupp}' , '${item.Alamat}','${item.HARIHUTPIUT}', '${item.PPN}')" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.KodeCustSupp}</td>
        <td>${item.NamaCustSupp}</td>
        <td>${item.Alamat}</td>

        </tr>`
      });


      document.getElementById("tabel_data_add_list_pelanggan").innerHTML = rowTable
      $("#tabel_add_list_pelanggan").DataTable({
        "lengthChange": false,
          "paging": true ,
      });

      document.getElementById("namaHeaderTable").textContent = 'Supplier'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPelanggan').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListBackOffice () {
  $.ajax({
    url: "{!! url('solistbackoffice') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.keynik}</td>
        <td>${item.fullname}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBackOffice('${item.keynik}' , '${item.fullname}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });


      document.getElementById("tabel_data_add_list_backoffice").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListBackOffice').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListSales () {
  $('#tabel_add_list_sales').DataTable().destroy();
  $.ajax({
    url: "{!! url('solistsales') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        console.log(item.keynik)
        console.log(item.nama)
        rowTable += `
        <tr>
        <td>${item.keynik}</td>
        <td>${item.nama}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSales('${item.keynik}' , '${String(item.nama)}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });


      document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable
      $("#tabel_add_list_sales").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListSales').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function onChangeInputAddAddNosat () {
  console.log('onChangeInputAddAddNosat')
  let _token  = $("#_token").val()
  let nosat = $("#input_add_add_nosat").val()
  console.log(nosat)
  console.log(Number(nosat))
  let kodebarang = $("#input_add_add_kodebarang").val()

  if (!kodebarang) {
    return
  }

  $.ajax({
    url: "{!! url('socekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang ,
      nosat
    },
    success: function(res) {
      console.log(res)

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.TANGGAL}</td>
        <td>-</td>
        <td>${item.SATUAN}</td>
        <td>-</td>
        <td>-</td>
        <td>${item.Xharga}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      // let rowTable = ``
      // res.forEach((item, i) => {
      //   rowTable += `
      //   <tr>
      //   <td>${item.keynik}</td>
      //   <td>${item.nama}</td>
      //   <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSales('${item.keynik}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>
      //
      //   </tr>`
      // });
      //
      //
      //
      //
      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      // }
      // document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable

      if (tipeformitem == 'add') {
        console.log(tempAddAdd[`Hrg${nosat}_1`])
        if (res.length && res[0].Xharga) {
          console.log('if1')
          document.getElementById("input_add_add_harga").value = res[0].Xharga
        } else {
          console.log('else1')
          if (tempAddAdd[`Hrg${nosat}_1`]) {
            console.log('if2')
            document.getElementById("input_add_add_harga").value = tempAddAdd[`Hrg${nosat}_1`]
          } else {
            console.log('else2')
            document.getElementById("input_add_add_harga").value = '0.00'
          }
        }
      } else {

      }

      // if (res.length && res[0].Xharga) {
      //   document.getElementById("input_add_add_harga").value = res[0].Xharga
      // } else {
      //   if ( nosat == 1) {
      //     if (tempAddAdd.Hrg1_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg1_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 2) {
      //     if (tempAddAdd.Hrg2_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg2_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 3) {
      //     if (tempAddAdd.Hrg3_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg3_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      // }


    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}





// filterso: 0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi -- mapped from
// penawaransoFilterOtorisasi (SEMUA/Belum/Sudah) set by penawaransoTerapkanFilter().
function penawaransoFilterSoValue() {
  if (penawaransoFilterOtorisasi === 'Belum') { return 1; }
  if (penawaransoFilterOtorisasi === 'Sudah') { return 2; }
  return 0;
}

function loadAll () {
  console.log('loadall')
  var tglawal = $('#tabel2_tglawal').val()
  var tglakhir = $('#tabel2_tglakhir').val()
  var filterso = penawaransoFilterSoValue()

  $.ajax({
    url: "{!! url('penawaransoloadall') !!}",
    type: "get",
    async: false,
    data: {
      tglawal: tglawal, tglakhir: tglakhir, filterso: filterso
    },
    success: function(res) {
      dataRefreshOutstanding2 = res.tempOutstanding3
    }})

  lastTabel2Rows = dataRefreshOutstanding2 || []
  reinitTabel2()

  console.log('loadall selesai');
}














function buttonAddAddPickBarangAll (kodebrg) {

  let _token  = $("#_token").val()

  $.ajax({
    url: "{!! url('sodetailbarangall') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg : kodebrg,
      nosat : 1
    },
    success: function(res) {
      console.log(res)

      if (!res.barang.length) {

        alertify.warning("Terjadi kesalahan pada server")
        return
      }

      tempAddAdd = res.barang[0]
      document.getElementById("input_add_add_kodebarang").value = tempAddAdd.Kodebrg
      document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
      document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
      document.getElementById("input_add_add_disc").value = '0.00'
      document.getElementById("input_add_add_discrp").value = '0.00'
      let selectOption = ''

      if (tempAddAdd.Sat1) {
        selectOption += `<option value=1 selected>${tempAddAdd.Sat1}</option>`
      }
      if (tempAddAdd.Sat2) {
        selectOption += `<option value=2>${tempAddAdd.Sat2}</option>`
      }
      if (tempAddAdd.Sat3) {
        selectOption += `<option value=3>${tempAddAdd.Sat3}</option>`
      }

      document.getElementById("input_add_add_nosat").innerHTML = selectOption

      console.log(res.harga)
      let rowTable = ``
      res.harga.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
        <td>${date1}</td>
        <td>-</td>
        <td>${item.SATUAN}</td>
        <td>-</td>
        <td>-</td>
        <td class="text-right">${Number(item.Xharga) ? parseFloat(item.Xharga).toFixed(2) : '0.00'}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.harga.length && Number(res.harga[0].Xharga)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res.harga[0].Xharga).toFixed(2)
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = parseFloat(tempAddAdd.Hrg1_1).toFixed(2)
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refres.hargah browser')
    }

  })

}







function buttonAddAddPickBarangFOCPlus (index , pEdit = 0) {
  let _token  = $("#_token").val()

  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.Kodebrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.Kodebrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_noPPL").value = ''
  document.getElementById("input_add_add_urutPPL").value = 0
  // document.getElementById("input_add_add_disc").value = '0.00'
  // document.getElementById("input_add_add_discrp").value = '0.00'

  let selectOption = ''
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${tempSatuanBarang[0].SAT1}(${tempSatuanBarang[0].ISI1})</option>`
  }
  if (tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}(${tempSatuanBarang[0].ISI2})</option>`
  }
  if (tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${tempSatuanBarang[0].SAT3}(${tempSatuanBarang[0].ISI3})</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.Kodebrg,
      nosat : 1
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${date1}</td>
          <td>-</td>
          <td>${item.SATUAN}</td>
          <td>-</td>
          <td>-</td>
          <td class="text-right">${Number(item.Xharga) ? parseFloat(item.Xharga).toFixed(2) : '0.00'}</td>
          <td>-</td>
          <td>-</td>

        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].Xharga)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res[0].Xharga).toFixed(2)
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = parseFloat(tempAddAdd.Hrg1_1).toFixed(2)
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}


let cekQntPR = 0
let cekQntPO = 0
let cekQntSisa = 0

function cekQntStock () {

  // tempStockAdd = dataAddAddListItem[0]

  // let currentQntPO = 0

  // cekQntPR = tempStockAdd.Qnt

  // cekQntPO = tempStockAdd.QntPO
  // cekQntSisa = tempStockAdd.SisaPPL

  // currentQntPO = parseInt(document.getElementById("input_add_add_qty").value) || 0

  // console.log(currentQntPO + ' current qnt PO')

  // if (currentQntPO > cekQntSisa) {
  //   alertify.warning('Qnt PO Tidak boleh melebihi Qnt Sisa')
  //   document.getElementById("input_add_add_qty").value = '0.00'
  // }

}

function buttonAddAddPickBarangNonFOC (index , pEdit = 0) {
 console.log('buttonAddAddPickBarangNonFOC xxx')
  let _token  = $("#_token").val()

  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = tempAddAdd.SisaPPL
  console.log(tempAddAdd.SisaPPL,'===================================')
  document.getElementById("input_add_add_noPPL").value = tempAddAdd.NoBukti
  document.getElementById("input_add_add_urutPPL").value = tempAddAdd.Urut
  // document.getElementById("input_add_add_discrp").value = '0.00'
  let selectOption = ''
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${tempSatuanBarang[0].SAT1}(${tempSatuanBarang[0].ISI1})</option>`
  }
  if (tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}(${tempSatuanBarang[0].ISI2})</option>`
  }
  if (tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${tempSatuanBarang[0].SAT3}(${tempSatuanBarang[0].ISI3})</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${item.QNT}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res[0].HARGA).toFixed(2)
        document.getElementById("input_add_add_hargaAwal").value = parseFloat(res[0].HARGA).toFixed(2)
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = parseFloat(tempAddAdd.Hrg1_1).toFixed(2)
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }


      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

function buttonAddAddPickBarangNonFOCPlus (index , pEdit = 0) {
  let _token  = $("#_token").val()
  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = tempAddAdd.Qnt
  document.getElementById("input_add_add_noPPL").value = tempAddAdd.NoBukti
  document.getElementById("input_add_add_urutPPL").value = tempAddAdd.Urut
  // document.getElementById("input_add_add_discrp").value = '0.00'

  let selectOption = ''
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${tempSatuanBarang[0].SAT1}(${tempSatuanBarang[0].ISI1})</option>`
  }
  if (tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}(${tempSatuanBarang[0].ISI2})</option>`
  }
  if (tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${tempSatuanBarang[0].SAT3}(${tempSatuanBarang[0].ISI3})</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${item.QNT}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res[0].HARGA).toFixed(2)
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = parseFloat(tempAddAdd.Hrg1_1).toFixed(2)
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

let tempSatuanBarang = []

function cekSatuanBarang (KodeBrg){
  let _token = $("#_token").val()

  $.ajax({
    url: "{!! url('ceksatuanbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      KodeBrg : KodeBrg
    },
    success: function(res) {
      tempSatuanBarang = res
    },
    error: function (err) {
      console.log(err)
    }
  })
}

function buttonAddPickPelanggan (kode, nama , alamat, hari, ppn) {
  console.log('buttonAddPickPelanggan')

  setNewNoBukti(ppn)

  document.getElementById("input_add_kodesupplier").value = kode
  document.getElementById("input_add_kodesupplier").value = kode
  let namaSupplier = document.getElementById("input_add_namasupplier");
    namaSupplier.value = nama;
    namaSupplier.disabled = true;
  // document.getElementById("input_add_namasupplier").value = nama
  // document.getElementById("input_add_alamatsupplier").value = alamat
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_hari").value = hari
  // document.getElementById("input_add_kodealamatkirim").value = ''
  document.getElementById("input_add_alamatkirim").value = ''
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  // document.getElementById("input_add_kodeekspedisi").value = '-'
  document.getElementById("input_add_ekspedisi").value = '-'

    selectTipeBayar = `
    <option value=1 selected>30 Hari Setelah Barang Dikirim</option>
    <option value=2 >60 Hari Setelah Barang Dikirim</option>
    <option value=3 >45 Hari Setelah Barang Dikirim</option>
    <option value=4 >Cash Before Delivery</option>
    <option value=5 >30 Hari Setelah Pekerjaan Selesai</option>
    <option value=6 >BG 30 hari setelah barang diterima</option>
    `
  

  if (ppn == 1){
    selectTipePPN = `
    <option value=1>Exclude</option>
    <option value=2>Include</option>`
  } else if (ppn == 0) {
    selectTipePPN = `
    <option value=0>None</option>`
  }

  document.getElementById("input_add_pembayaran").innerHTML = selectTipeBayar
  document.getElementById("input_add_tipeppn").innerHTML = selectTipePPN

  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickAlamatKirim (index) {

  let itemX = listAlamatKirim[index]
  console.log(itemX)
  // console.log(kode,nama,alamat)
  if (tipeform == 'edit') {
    onChangeHeader('NoAlamatKirim' , itemX.KODEGDG)
    onChangeHeader('AlamatKirim' , itemX.Alamat)
  }

  // document.getElementById("input_add_kodealamatkirim").value = itemX.KODEGDG
  document.getElementById("input_add_alamatkirim").value = itemX.Alamat
  buttonAddListBatal()

}

function buttonAddPickNoSO (kode, nama) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("input_add_noso").value = kode
  document.getElementById("input_add_nopocust").value = nama

  buttonAddListBatal()
}



function buttonAddPickLokasiPenerima (kode, nama ) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUN' , kode)

  }
  
  document.getElementById("input_add_lokasipenerima").value = kode
  document.getElementById("input_add_namalokasipenerima").value = nama

  buttonAddListBatal()
  // document.getElementById("input_add_kodeekspedisi").scrollIntoView();
}

function buttonAddPickPIC (kode, nama ) {
  console.log('buttonAddPickPIC')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KodePF' , kode)
  }
  document.getElementById("input_add_kodepic").value = kode
  document.getElementById("input_add_namapic").value = nama
  buttonAddListBatal()
}

function buttonAddPickPWO (kode, nama) {
  console.log('buttonAddPickPWO')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KodePF' , kode)
  }
  document.getElementById("input_add_kodepic").value = kode
  document.getElementById("input_add_namapic").value = nama
  buttonAddListBatal()
}

function buttonAddPickValas (kode, kurs) {
  console.log('buttonAddPickValas')
  console.log(kode,kurs)
  if (tipeform == 'edit') {
    onChangeHeader('KODEVLS' , kode)
    onChangeHeader('KURS' , kurs)
  }
  document.getElementById("input_add_valas").value = kode
  document.getElementById("input_add_kurs").value = kurs
  buttonAddListBatal()
}

function buttonAddPickSales (kode, nama ) {
  console.log('buttonAddPickSales')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODESLS' , kode)

  }
  document.getElementById("input_add_kodesales").value = kode
  document.getElementById("input_add_namasales").value = nama
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickBackOffice (kode, nama ) {
  console.log('buttonAddPickBackOffice')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('Boffice' , kode)

  }
  document.getElementById("input_add_kodebackoffice").value = kode
  document.getElementById("input_add_namabackoffice").value = nama
  buttonAddListBatal()

  document.getElementById("input_add_kodebackoffice").scrollIntoView();

}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}

function cleanFormAddAdd () {

  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_namabarang").value = ''
  document.getElementById("input_add_add_namabarangasli").value = ''
  document.getElementById("input_add_add_keteranganbarang").value = ''
  document.getElementById("input_add_add_tipeso").value = ''
  document.getElementById("input_add_add_merkso").value = ''
  document.getElementById("input_add_add_satminus").value = ''
  document.getElementById("input_add_add_qty").value = '0.00'
  document.getElementById("input_add_add_nosat").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
  // document.getElementById("input_add_add_satuanproduk").value = ''
  document.getElementById("input_add_add_harga").value = '0.00'
  // document.getElementById("input_add_add_disc").value = '0.00'
  document.getElementById("input_add_add_discrp").value = '0.00'
  document.getElementById("input_add_add_discpersen1").value = '0.00'
  document.getElementById("input_add_add_discpersen2").value = '0.00'
  document.getElementById("input_add_add_discpersen3").value = '0.00'
  document.getElementById("input_add_add_hargaAwal").value = '0.00'
  // document.getElementById("input_add_add_tambahkepo").value = 0

}

function lockFormAdd () {
   document.getElementById("input_add_kodesupplier").disabled = true
    document.getElementById("input_add_alamatsupplier").disabled = true
    document.getElementById("input_add_freight").disabled = true
     document.getElementById("input_add_tipeppn").disabled = true
  document.getElementById("input_add_alamatkirim").disabled = true
   document.getElementById("input_add_ekspedisi").disabled = true
  document.getElementById("input_add_pembayaran").disabled = true
  document.getElementById("input_add_nopocust").disabled = true
  document.getElementById("input_add_noso").disabled = true
  document.getElementById("input_add_keterangan").disabled = true
  document.getElementById("input_add_tglrevisi").disabled = true
  document.getElementById("input_add_tglprcust").disabled = true
  document.getElementById("input_add_hari").disabled = true
  document.getElementById("input_add_draftpo").disabled = true


  document.getElementById("input_add_up").disabled = true
  document.getElementById("input_add_up2").disabled = true
  document.getElementById("input_add_note").disabled = true
  // document.getElementById("input_add_catatan").disabled = true
  document.getElementById("input_add_ketrevisi").disabled = true
  document.getElementById("input_add_namalokasipenerima").disabled = true
  document.getElementById("input_add_lokasipenerima").disabled = true
  // document.getElementById("buttonaddlistpenerima").disabled = true


  document.getElementById("input_add_add_discpersen1").disabled = true
  document.getElementById("input_add_add_discpersen2").disabled = true
  document.getElementById("input_add_add_discpersen3").disabled = true
  document.getElementById("input_add_add_foc").disabled = true

  document.getElementById("buttonAddListPelanggan").hidden = true
  // document.getElementById("buttonAddListGudang").hidden = true
  document.getElementById("buttonAddListSales").hidden = true
  document.getElementById("buttonAddListValas").hidden = true
  document.getElementById("buttonAddListPIC").hidden = true
  document.getElementById("buttonAddListLokasiPenerima").disabled = true
  document.getElementById("buttonAddListBackOffice").hidden = true
  document.getElementById("buttonAddListBackOffice").hidden = true
  document.getElementById("buttonAddListNoSo").hidden = true
  document.getElementById("buttonTambahItem").hidden = true

  document.getElementById("input_add_disc").disabled = true
  document.getElementById("input_add_discrp").disabled = true
  document.getElementById("input_add_add_tipeso").disabled = true
  document.getElementById("input_add_add_merkso").disabled = true
  document.getElementById("input_add_add_satminus").disabled = true





}

function buttonShowHideHeader ()
{
  var modal = document.getElementById("modalBodyAddMainHeader");
  console.log($('#modalBodyAddMainHeader').css('display'))
  if($('#modalBodyAddMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function buttonShowHideHeaderDetail () {
  var modal = document.getElementById("modalBodyDetailMainHeader");
  console.log($('#modalBodyDetailMainHeader').css('display'))
  if($('#modalBodyDetailMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function unlockFormAdd () {
  // document.getElementById("input_add_tipeppn").disabled = false
  // document.getElementById("input_add_pembayaran").disabled = false
  // document.getElementById("input_add_nopocust").disabled = false
  // document.getElementById("input_add_noso").disabled = false
  // document.getElementById("input_add_keterangan").disabled = false
  // document.getElementById("input_add_tglprcust").disabled = false
  //  document.getElementById("input_add_tglrevisi").disabled = false
  // document.getElementById("input_add_hari").disabled = false
  // document.getElementById("input_add_draftpo").disabled = false


  
  // document.getElementById("input_add_up").disabled = false
  // document.getElementById("input_add_up2").disabled = false
  // document.getElementById("input_add_note").disabled = false

  // document.getElementById("input_add_ketrevisi").disabled = false
  // document.getElementById("input_add_namalokasipenerima").disabled = false
  // document.getElementById("input_add_lokasipenerima").disabled = false

  // document.getElementById("input_add_add_discpersen1").disabled = false
  // document.getElementById("input_add_add_discpersen2").disabled = false
  // document.getElementById("input_add_add_discpersen3").disabled = false
  // document.getElementById("input_add_add_foc").disabled = false

  // document.getElementById("buttonAddListPelanggan").hidden = false

  // document.getElementById("buttonAddListSales").hidden = false
  // document.getElementById("buttonAddListValas").hidden = false
  // document.getElementById("buttonAddListPIC").hidden = false

  // document.getElementById("buttonAddListBackOffice").hidden = false
  // document.getElementById("buttonAddListBackOffice").hidden = false
  // document.getElementById("buttonAddListNoSo").hidden = false
  // document.getElementById("buttonTambahItem").hidden = false

  // document.getElementById("input_add_disc").disabled = false
  // document.getElementById("input_add_discrp").disabled = false


// ==========================
 document.getElementById("input_add_kodesupplier").disabled = false
    document.getElementById("input_add_alamatsupplier").disabled = false
    document.getElementById("input_add_freight").disabled = false
     document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_alamatkirim").disabled = false
   document.getElementById("input_add_ekspedisi").disabled = false
  document.getElementById("input_add_pembayaran").disabled = false
  document.getElementById("input_add_nopocust").disabled = false
  document.getElementById("input_add_noso").disabled = false
  document.getElementById("input_add_keterangan").disabled = false
  document.getElementById("input_add_tglrevisi").disabled = false
  document.getElementById("input_add_tglprcust").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_draftpo").disabled = false


  document.getElementById("input_add_up").disabled = false
  document.getElementById("input_add_up2").disabled = false
  document.getElementById("input_add_note").disabled = false
  // document.getElementById("input_add_catatan").disabled = false
  document.getElementById("input_add_ketrevisi").disabled = false
  document.getElementById("input_add_namalokasipenerima").disabled = false
  document.getElementById("input_add_lokasipenerima").disabled = false
  // document.getElementById("buttonaddlistpenerima").disabled = false


  document.getElementById("input_add_add_discpersen1").disabled = false
  document.getElementById("input_add_add_discpersen2").disabled = false
  document.getElementById("input_add_add_discpersen3").disabled = false
  document.getElementById("input_add_add_foc").disabled = false

  document.getElementById("buttonAddListPelanggan").hidden = false
  // document.getElementById("buttonAddListGudang").hidden = false
  document.getElementById("buttonAddListSales").hidden = false
  document.getElementById("buttonAddListValas").hidden = false
  document.getElementById("buttonAddListPIC").hidden = false
  document.getElementById("buttonAddListLokasiPenerima").disabled = false
  document.getElementById("buttonAddListBackOffice").hidden = false
  document.getElementById("buttonAddListBackOffice").hidden = false
  document.getElementById("buttonAddListNoSo").hidden = false
  document.getElementById("buttonTambahItem").hidden = false

  document.getElementById("input_add_disc").disabled = false
  document.getElementById("input_add_discrp").disabled = false




}

function cleanFormAdd () {

  document.getElementById("input_add_nobukti").value = ''
  // document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  // document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  document.getElementById("input_add_kodesupplier").value = ''
  document.getElementById("input_add_namasupplier").value = ''
  document.getElementById("input_add_alamatsupplier").value = ''
  // document.getElementById("input_add_kodealamatkirim").value = 'GMPL'
  document.getElementById("input_add_alamatkirim").value = ''
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  // document.getElementById("input_add_kodeekspedisi").value = '-'
  document.getElementById("input_add_ekspedisi").value = '-'
  document.getElementById("input_add_keterangan").value = ''
  document.getElementById("input_add_valas").value = ''
  document.getElementById("input_add_kurs").value = ''
  document.getElementById("input_add_nopocust").value = '-'
  document.getElementById("input_add_noso").value = '-'
  document.getElementById("input_add_kodebackoffice").value = ''
  document.getElementById("input_add_namabackoffice").value = ''
  document.getElementById("input_add_tipeppn").value = 0
   document.getElementById("input_add_freight").value = 0
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_kodesales").value = ''
  document.getElementById("input_add_namasales").value = ''
  document.getElementById("input_add_hari").value = 0
  document.getElementById("input_add_draftpo").value = 0



  document.getElementById("input_add_up").value = ''
  document.getElementById("input_add_up2").value = ''
  document.getElementById("input_add_note").value = ''
  // document.getElementById("input_add_catatan").value = ''
  document.getElementById("input_add_ketrevisi").value = ''
  document.getElementById("input_add_namalokasipenerima").value = ''
  document.getElementById("input_add_lokasipenerima").value = ''
  


  document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_pembayaran").disabled = false
  document.getElementById("input_add_nopocust").disabled = false
  document.getElementById("input_add_noso").disabled = false
  document.getElementById("input_add_keterangan").disabled = false
  // document.getElementById("input_add_tanggalkirim").disabled = false
  // document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_draftpo").disabled = false

  document.getElementById("buttonAddListPelanggan").disabled = false
  // document.getElementById("buttonAddListGudang").disabled = false
  document.getElementById("buttonAddListSales").disabled = false
  document.getElementById("buttonAddListValas").disabled = false
  document.getElementById("buttonAddListPIC").disabled = false
  // document.getElementById("buttonAddListLokasiPenerima").disabled = false
  document.getElementById("buttonAddListBackOffice").disabled = false

  document.getElementById("input_add_disc").disabled = false
  document.getElementById("input_add_discrp").disabled = false

  document.getElementById("input_add_disc").value = '0.00'
  document.getElementById("input_add_discrp").value = '0.00'
  document.getElementById("input_add_ppn").value = '0.00'
  document.getElementById("input_add_dpp").value = '0.00'
  document.getElementById("input_add_grandtotal").value = '0.00'
}

function buttonEdit (NOBUKTI) {
let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}


  tipeform = 'edit'
  console.log('buttonEdit' , NOBUKTI)

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  unlockFormAdd()

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let _token  = $("#_token").val()
  let oto = 1

  $.ajax({
    url: "{!! url('penawaransocekotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      oto = res.isOtorisasi
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  if (oto == 1) {
    alertify.warning("Sudah diotorisasi")
    return
  }

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableEdit(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}








function buttonAdd (noBukti) {
  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}
  tipeform = 'add'

  if (noBukti != null){
  noBuktiUntukAdd = noBukti
  }
  if ( noBukti == null){
    noBuktiUntukAdd = 0
  }

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').hide();
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  dataTableAdd = []
  cleanFormAdd()
  unlockFormAdd()
    const now = new Date()
    const tanggalCetak = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/\//g, '/')

    console.log(tanggalCetak, now)

  refreshDataTableAdd()
  document.getElementById("input_add_valas").value = 'IDR'
  document.getElementById("input_add_kurs").value = '1.00'

  document.getElementById("input_add_tanggal").value = formatDate(now)

  $('#page1').hide();
  $('#page2').show();

}

function buttonCloseForm () {
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

}

function buttonCloseFormDetail () {
  $('#page3').hide();
  $('#page1').show();

}

function submitAdd ()
{

  let alamatpelanggan = $("#input_add_alamatsupplier").val();
  console.log(alamatpelanggan)
  let catatan = $("#input_add_keterangan").val();
  console.log(catatan)

}

function buttonAddMainHeader() {
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainHeader').show();
  // $('#buttonAddListPelanggan').hide();
}

function buttonAddMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainItems').show();
}

function buttonDetailMainHeader() {
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainHeader').show();
  // $('#buttonDetailListPelanggan').hide();
}

function buttonDetailMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainItems').show();
}

function cekPoDet () {

  let _token  = $("#_token").val()
  $.ajax({
    url: "{!! url('cekPoDet') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res){
      console.log(res)
    },
    error: function (err) {
      console.log(err)
    }
  })
}

function buttonDetail (NOBUKTI) {
  tipeform = 'detail'
  console.log('button Detail' , NOBUKTI)

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  lockFormAdd()

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableDetail(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

function refreshDataTableAdd (NOBUKTI) {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {

    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="9">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('penawaransogetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        if (!res.list.length) {
          alertify.warning("Data habis")
          //  $("#form").modal('toggle')
          $('#page3').hide();
          $('#page2').hide();
          $('#page1').show();
        } else {
          dataHeaderAdd = res.list[0]
          dataTableAdd = res.list

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {

            rowTable +=
            `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${item.Qnt ? parseFloat(item.Qnt).toFixed(2) : '0.00'}</td>
              <td class="text-center">${item.Satuan}</td>
              <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>
              <td class="text-center">
                ${tipeform == 'edit' ?
                `<button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>`
                : `-`
                }
              </td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="9">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

         


          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti
          console.log('======================= nnn1',dataHeaderAdd.NamaCustSupp)
          document.getElementById("input_add_namasupplier").value = dataHeaderAdd.NamaCustSupp
          document.getElementById("input_add_freight").value = formatAngka(parseFloat(dataHeaderAdd.freight).toFixed(2))
          document.getElementById("input_add_kodesupplier").value = dataHeaderAdd.KodeSupp
          document.getElementById("input_add_alamatsupplier").value = dataHeaderAdd.Keterangan
          document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          document.getElementById("input_add_nopocust").value = dataHeaderAdd.Nopesanan
          document.getElementById("input_add_note").value = dataHeaderAdd.catatan
          document.getElementById("input_add_up").value = dataHeaderAdd.namapic
          document.getElementById("input_add_up2").value = dataHeaderAdd.namapic2
          document.getElementById("input_add_alamatkirim").value = dataHeaderAdd.franco
          document.getElementById("input_add_ketrevisi").value = dataHeaderAdd.ketrevisi
          document.getElementById("input_add_ekspedisi").value = dataHeaderAdd.delivery
          document.getElementById("input_add_noso").value = dataHeaderAdd.NOSO
          document.getElementById("input_add_hari").value = dataHeaderAdd.Hari
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.validitas
          document.getElementById("input_add_pembayaran").value = dataHeaderAdd.TipeBayar
          document.getElementById("input_add_lokasipenerima").value = dataHeaderAdd.kodekebunh
          document.getElementById("input_add_namalokasipenerima").value = dataHeaderAdd.namakebun


          document.getElementById("input_add_tipeppn").value = dataHeaderAdd.PPN
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.Tanggal)
          document.getElementById("input_add_tglprcust").value = formatDate(dataHeaderAdd.tglprcust)
          document.getElementById("input_add_tglrevisi").value = formatDate(dataHeaderAdd.tglrevisi)
          document.getElementById("input_add_disc").value = parseFloat(dataHeaderAdd.DiscP1).toFixed(2)
          document.getElementById("input_add_discrp").value = parseFloat(dataHeaderAdd.TotDiskon).toFixed(2)
          document.getElementById("input_add_dpp").value = formatAngka(parseFloat(dataHeaderAdd.TotDPP).toFixed(2))
          document.getElementById("input_add_ppn").value = formatAngka(parseFloat(dataHeaderAdd.TotPPN).toFixed(2))
          document.getElementById("input_add_grandtotal").value = formatAngka(parseFloat(dataHeaderAdd.TotNet).toFixed(2))





        }

      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

    let rowHeader = ""
            rowHeader =
            `<tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qnt</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Harga</th>
                  <th style="padding: 4px 12px;" scope="col">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">No. PR</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
            </tr>`

          document.getElementById("tabel_data_header").innerHTML = rowHeader
  }
}

function refreshDataTableEdit (NOBUKTI) {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {

    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="9">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('penawaransogetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        if (!res.list.length) {
          alertify.warning("Data habis")
          //  $("#form").modal('toggle')
          $('#page3').hide();
          $('#page2').hide();
          $('#page1').show();
        } else {
          dataHeaderAdd = res.list[0]
          dataTableAdd = res.list

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {

            rowTable +=
            `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${item.Qnt ? parseFloat(item.Qnt).toFixed(2) : '0.00'}</td>
              <td class="text-center">${item.Satuan}</td>
              <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>
              <td class="text-center">
                ${tipeform == 'edit' ?
                `<button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>`
                : `-`
                }
              </td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="9">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable
          $purut=dataHeaderAdd.Urut
          console.log('urut koreksi hapus', $purut)
         document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti

           console.log('======================= nnn2',dataHeaderAdd.NamaCustSupp)
          document.getElementById("input_add_namasupplier").value = dataHeaderAdd.NamaCustSupp


          document.getElementById("input_add_kodesupplier").value = dataHeaderAdd.KodeSupp
          document.getElementById("input_add_freight").value = parseFloat(dataHeaderAdd.freight).toFixed(2)
          document.getElementById("input_add_alamatsupplier").value = dataHeaderAdd.Keterangan
          document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          document.getElementById("input_add_nopocust").value = dataHeaderAdd.Nopesanan
          document.getElementById("input_add_note").value = dataHeaderAdd.catatan
          document.getElementById("input_add_up").value = dataHeaderAdd.namapic
          document.getElementById("input_add_up2").value = dataHeaderAdd.namapic2
          document.getElementById("input_add_alamatkirim").value = dataHeaderAdd.franco
          document.getElementById("input_add_ketrevisi").value = dataHeaderAdd.ketrevisi
          document.getElementById("input_add_ekspedisi").value = dataHeaderAdd.delivery
          document.getElementById("input_add_noso").value = dataHeaderAdd.NOSO
          document.getElementById("input_add_hari").value = dataHeaderAdd.Hari
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.validitas
          document.getElementById("input_add_pembayaran").value = dataHeaderAdd.TipeBayar
          document.getElementById("input_add_lokasipenerima").value = dataHeaderAdd.kodekebunh
          document.getElementById("input_add_namalokasipenerima").value = dataHeaderAdd.namakebun


          document.getElementById("input_add_tipeppn").value = dataHeaderAdd.PPN
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.Tanggal)
          document.getElementById("input_add_tglprcust").value = formatDate(dataHeaderAdd.tglprcust)
          document.getElementById("input_add_tglrevisi").value = formatDate(dataHeaderAdd.tglrevisi)
          document.getElementById("input_add_disc").value = parseFloat(dataHeaderAdd.DiscP1).toFixed(2)
          document.getElementById("input_add_discrp").value = parseFloat(dataHeaderAdd.TotDiskon).toFixed(2)
          document.getElementById("input_add_dpp").value = formatAngka(parseFloat(dataHeaderAdd.TotDPP).toFixed(2))
          document.getElementById("input_add_ppn").value = formatAngka(parseFloat(dataHeaderAdd.TotPPN).toFixed(2))
          document.getElementById("input_add_grandtotal").value = formatAngka(parseFloat(dataHeaderAdd.TotNet).toFixed(2))





          

          noBuktiUntukAdd = dataHeaderAdd.NoPPL

        }

      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

    let rowHeader = ""
            rowHeader =
            `<tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qnt</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Harga</th>
                  <th style="padding: 4px 12px;" scope="col">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">No. PR</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
            </tr>`

          document.getElementById("tabel_data_header").innerHTML = rowHeader
  }
}

function refreshDataTableDetail (NOBUKTI) {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {

    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="9">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('penawaransogetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        if (!res.list.length) {
          alertify.warning("Data habis")
          //  $("#form").modal('toggle')
          $('#page3').hide();
          $('#page2').hide();
          $('#page1').show();
        } else {
          dataHeaderAdd = res.list[0]
          dataTableAdd = res.list

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {

            rowTable +=
            `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td>${item.KeteranganBarang || ''}</td>
              <td class="text-right">${item.Qnt ? parseFloat(item.Qnt).toFixed(2) : '0.00'}</td>
              <td class="text-center">${item.Satuan}</td>
              <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="9">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti
           console.log('======================= nnn',dataHeaderAdd.NamaCustSupp)
          document.getElementById("input_add_namasupplier").value = dataHeaderAdd.NamaCustSupp
          document.getElementById("input_add_freight").value = formatAngka(parseFloat(dataHeaderAdd.freight).toFixed(2))
          document.getElementById("input_add_kodesupplier").value = dataHeaderAdd.KodeSupp
          document.getElementById("input_add_alamatsupplier").value = dataHeaderAdd.Keterangan
          document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          document.getElementById("input_add_nopocust").value = dataHeaderAdd.Nopesanan
          document.getElementById("input_add_note").value = dataHeaderAdd.catatan
          document.getElementById("input_add_up").value = dataHeaderAdd.namapic
          document.getElementById("input_add_up2").value = dataHeaderAdd.namapic2
          document.getElementById("input_add_alamatkirim").value = dataHeaderAdd.franco
          document.getElementById("input_add_ketrevisi").value = dataHeaderAdd.ketrevisi
          document.getElementById("input_add_ekspedisi").value = dataHeaderAdd.delivery
          document.getElementById("input_add_noso").value = dataHeaderAdd.NOSO
          document.getElementById("input_add_hari").value = dataHeaderAdd.Hari
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.validitas
          document.getElementById("input_add_pembayaran").value = dataHeaderAdd.TipeBayar
          document.getElementById("input_add_lokasipenerima").value = dataHeaderAdd.kodekebunh
          document.getElementById("input_add_namalokasipenerima").value = dataHeaderAdd.namakebun


          document.getElementById("input_add_tipeppn").value = dataHeaderAdd.PPN
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.Tanggal)
          document.getElementById("input_add_tglprcust").value = formatDate(dataHeaderAdd.tglprcust)
          document.getElementById("input_add_tglrevisi").value = formatDate(dataHeaderAdd.tglrevisi)
          document.getElementById("input_add_disc").value = parseFloat(dataHeaderAdd.DiscP1).toFixed(2)
          document.getElementById("input_add_discrp").value = parseFloat(dataHeaderAdd.TotDiskon).toFixed(2)
          document.getElementById("input_add_dpp").value = formatAngka(parseFloat(dataHeaderAdd.TotDPP).toFixed(2))
          document.getElementById("input_add_ppn").value = formatAngka(parseFloat(dataHeaderAdd.TotPPN).toFixed(2))
          document.getElementById("input_add_grandtotal").value = formatAngka(parseFloat(dataHeaderAdd.TotNet).toFixed(2))

        }

      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

          let rowHeader = ""

            rowHeader =
            `<tr>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                  <th style="padding: 4px 12px;" scope="col">Qnt</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Harga</th>
                  <th style="padding: 4px 12px;" scope="col">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">No. PR</th>
                </tr>
            </tr>`

          document.getElementById("tabel_data_header").innerHTML = rowHeader
  }
}

function submitPrint (nobukti) {

    let _token = $('#_token').val()

    $.ajax({
      url: "{!! url('penawaransoprint') !!}",
      type: "get",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {

        dataPrint = res

        console.log(dataPrint)

      }
    })

    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=100)
    {
      let tempArray = dataPrint.slice(i,i+100)
      arrayDataPrint.push(tempArray)
    }

    document.getElementById('imagecontainerTtd').querySelector('img').src = `img/ttd/${dataPrint[0].ttd}.png`;
    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let imageContentTtd = document.getElementById(`imagecontainerTtd`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].tanggal.split(' ')[0].split('-').reverse().join('/');
    let tanggalKirimOnly = ''

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

    .no-border { border: none; }
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .fw-bold { font-weight: bold; }
    .m-0 { margin: 0; }
    .pb-1 { padding-bottom: 0.25rem; }
    .pb-2 { padding-bottom: 0.5rem; }
    .ps-3 { padding-left: 1rem; }
    .pe-1 { padding-right: 0.25rem; }
    .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mt-3 { margin-top: 1rem; }

    .body-main-prints {
      width: 21cm;
      height: 29.7cm;
      position: relative;
    }

    .footer-sign {
      padding-top: 5px;
      position: absolute;
      width: 100%;
      bottom: 30px;
    }

    .footer-print-date {
      position: absolute;
      width: 95%;
      bottom: 130px;
    }

    .detail-spb-table { margin: 0; }
  </style>`;
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 100%">
                    <div style="width: 100%">
                      <div class="pb-1" style="width: 15%; margin-top: 15px">
                        `+ imageContent +`
                      </div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; margin-top: 10px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Ref.No</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%; font-size: 13px;">`+dataPrint[0].nobukti+`</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Tanggal</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%; font-size: 13px;">`+tanggalOnly+`</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Kepada Yth</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%; font-size: 13px;">${dataPrint[0].NAMA ?? ''}</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Lokasi</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%; font-size: 13px;">${dataPrint[0].namalokasi ?? ''}</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Up</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%; font-size: 13px;">${dataPrint[0].namapic ?? ''}</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Dari</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%; font-size: 13px;">${dataPrint[0].nmprs ?? ''}</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%"></div>
                      <div class="pb-1" style="width: 3%"></div>
                      <div class="pb-1" style="width: 100%; font-size: 13px;">${dataPrint[0].alamatprs ?? ''}</div>
                    </div>
                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%"></div>
                      <div class="pb-1" style="width: 3%"></div>
                      <div class="pb-1" style="width: 100%; font-size: 13px;">${dataPrint[0].kotaprs ?? ''}</div>
                    </div>
                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%"></div>
                      <div class="pb-1" style="width: 3%"></div>
                      <div class="pb-1" style="width: 100%; font-size: 13px;">Telp: (0541) 4104142 Email: sml@indo.net.id</div>
                    </div>

                  </div>

                </div>

              <div style="width:100%; text-align:center; margin:30px 0;">
                <span style="
                    font-size:15px;
                    text-decoration: underline;
                    text-decoration-thickness: 2px;">
                    PERIHAL : PENAWARAN HARGA NF
                </span>
              </div>

              <div style="display: flex; width: 80%; font-size: 13px;">
                 <div class="pb-1" style="width: 30%;">Dengan hormat,</div>
              </div>

              <div style="display: flex; width: 100%; font-size: 13px;">
                 <div class="pb-1" style="width: 100%;">Bersama ini perkenankanlah kami menawarkan harga sebagai berikut:</div>
              </div>
   <table
    class="detail-spb-table"
    style="width: 98%; height: 100px; max-height: 100px; font-family: sans-serif; display: table; border: 1px solid #3c3c3c; font-size: 10px;">
                <thead style='background-color:#609af7'>
                  <tr>
                    <td class="text-center" style="color:White; width: 2%" >No.</td>
                    <td class="text-center" style="color:White; width: 45%">Nama Barang</td>
                    <td class="text-center" style="color:White; width: 5%">Tipe</td>
                    <td class="text-center" style="color:White; width: 5%">Merk</td>
                    <td class="text-center" style="color:White; width: 8%">Qty</td>
                    <td class="text-center" style="color:White; width: 5%">Sat</td>
                    <td class="text-center" style="color:White; width: 10%">Harga</td>
                    <td class="text-center" style="color:White; width: 10%">Disc</td>
                    <td class="text-center" style="color:White; width: 15%">Total</td>
                    <td class="text-center" style="color:White; width: 15%">Keterangan</td>
                  </tr>
                </thead> `;

    let z = 0
    let jumlahTotal = 0
    let diskonTotal = 0
    let subTotal = 0
    let ppnTotal = 0
    let totalTotal = 0

    let tempPrintStr = ``
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>
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
        tempPrintStr += `
          <tr>
            <td style="width: 2%; text-align: center; font-size: 10px;">${z+1}</td>

            <td style="width: 45%; text-align: left; font-size: 10px;">${itemSub.NAMABRG}</td>

            <td style="width: 5%; text-align: center; font-size: 10px;">${itemSub.tipeso}</td>

            <td style="width: 5%; text-align: left; font-size: 10px;">${itemSub.namamerkso ?? ''}</td>

            <td style="width: 8%; text-align: right; font-size: 10px;">${itemSub.QNT ? parseFloat(itemSub.QNT).toFixed(2) : ''}</td>

            <td style="width: 5%; text-align: center; font-size: 10px;">${itemSub.SATUAN}</td>

            <td style="width: 10%; text-align: right; font-size: 10px;">${formatAngka(parseFloat(itemSub.harga).toFixed(2))}</td>

            <td style="width: 10%; text-align: right; font-size: 10px;">${formatAngka(parseFloat(itemSub.ndiskon).toFixed(2))}</td>

            <td style="width: 15%; text-align: right; font-size: 10px;">${formatAngka(parseFloat(itemSub.nnet).toFixed(2))}</td>

            <td style="width: 15%; text-align: left; font-size: 10px;">${itemSub.ketdet ?? ''}</td>
          </tr>`;
        z++;
      });

tempPrintStr += `
        <tr>
          <td colspan="7" style="border:1px solid; padding:5px; font-size: 10px;">
          </td>
          <td style="border:1px solid; text-align:right; font-size: 10px;">
            Grand total
          </td>
          <td style="border:1px solid; text-align:right; font-size: 10px;">
            ${formatAngka(parseFloat(dataPrint[0].totalnnet || 0).toFixed(2))}
          </td>
          <td colspan="1" style="border:1px solid; padding:5px;">
          </td>
        </tr>
        <tr>
          <td colspan="7" style="border:1px solid; padding:5px;">
          </td>
          <td style="border:1px solid; text-align:right; font-size: 10px;">
            Freight
          </td>
          <td style="border:1px solid; text-align:right; font-size: 10px;">
            ${formatAngka(parseFloat(dataPrint[0].freight || 0).toFixed(2))}
          </td>
          <td colspan="1" style="border:1px solid; padding:5px;">
          </td>
        </tr>`;

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="width: 100%;">


  <div style="width: 95%; font-family: sans-serif; font-size: 10px;">

     <div style="display: flex; width: 100%; padding:10px 0;">
      <div>
        <span style="
          font-weight: bold;
          font-style: italic;
          font-size: 13px;
          border-bottom: 1px solid #000;
          line-height: 1.5;">
          â€œMohon perhatikan spesifikasi barang yang kami tawarkan di atas, barang tidak dapat DIRETUR setelah PO kami proses. Mohon menjadi perhatian bersama. Terima kasih.â€
        </span>
      </div>
    </div>


    <div style="display: flex; width: 100%; margin-top: 10px; padding: 2px 0; font-size: 13px;">
      <div>Adapun penawaran yang kami ajukan dengan kondisi sebagai berikut:</div>
    </div>

    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Harga</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].myppn+`</div>
    </div>


    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Pembayaran</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].ketpemb+`</div>
    </div>

    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Franco</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].franco+`</div>
    </div>

    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Delivery</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].delivery+`</div>
    </div>

    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Validitas</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].validitas+`<span style="font-weight:bold; margin-left:1px;">
          (HARGA DAN STOK TIDAK TERIKAT)
        </span></div>
    </div>

    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Note</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].catatan+`</div>
    </div>

    <div style="display: flex; width: 100%; padding: 15px 0; font-size: 13px;">
      <div class="pb-1" style="width: 100%;">Demikian penawaran harga dari kami dengan harapan mendapat kabar baik dari Bapak/Ibu.
        Atas perhatian dan kerjasama nya kami ucapkan terimakasih.
    </div>
    </div>


    <div style="display: flex; width: 80%; margin-top: 20px; font-size: 13px;">
      <div class="pb-1" style="width: 30%;">Hormat kami ,</div>
    </div>

    <div style="display: flex; width: 100%;">
      <div class="pb-1" style="width: 100%; margin-top: 15px">
        `+ imageContentTtd +`
      </div>
    </div>

    <div style="display: flex; width: 80%; margin-top: 10px; font-size: 13px;">
      <div class="pb-1" style="width: 100%;">`+dataPrint[0].namattd+`</div>
    </div>

    <div style="display: flex; width: 80%; font-size: 13px;">
      <div class="pb-1" style="width: 100%;">`+dataPrint[0].jabatan+`</div>
    </div>


    


  </div>`

     tempPrintStr += `
      <div style="width:50%; margin-top:15px; margin-left:-25px; float:left; text-align:center; font-size:15px;">
       
      </div>

        <div style="width:50%; float:left; font-size:15px; margin-top:-10px; margin-left:-15px;">
        <table style="width:100%; border-collapse: collapse;">

       
    </div>

    <div style="clear: both;"></div>
  </div>

</div>`


        tempPrintStr += `</div>`
      });


      tempPrintStr +=  `</body></html>`

    let w = window.open(' ');

    w.document.open();
    w.document.write(tempPrintStr);
    w.document.close();

    setTimeout(() => {
      w.focus();
      w.print();
      }, 300);
    }


function submitPrint2 (nobukti) {

let _token = $('#_token').val()

$.ajax({
  url: "{!! url('purchaseorderprint') !!}",
  type: "get",
  async: false,
  data: {
    _token : _token,
    NOBUKTI: nobukti
  },
  success: function(res) {

    dataPrint = res

    console.log(dataPrint)

  }
})

let arrayDataPrint = []
for (let i = 0; i < dataPrint.length; i+=7)
{
  let tempArray = dataPrint.slice(i,i+7)
  arrayDataPrint.push(tempArray)
}

let printContent = ''
let imageContent = document.getElementById(`imagecontainer`).innerHTML;
let css = ''
let hdr = ''
let str= ''
let ftr= ''
let tanggalOnly = dataPrint[0].tanggal.split(' ')[0];
let tanggalKirimOnly = dataPrint[0].tglkirim.split(' ')[0];


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
    height: 18px;
    padding: 0px 4px;
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

  .border-bottom {
    border: bottom;
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
    height: 14cm;
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
hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

              <div class="pe-1" style="width: 60%">
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 15%; margin-top: 15px">
                    `+ imageContent +`
                  </div>
                  <div class="pb-1 ps-3" style="width: 85%; margin-top: 10px;">
                    <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                    <div class="pb-1" style="width: 100%; font-size: 11px;">Jl. Ampera Pergudangan Mangkupalas Bisnis Centre Blok D No.18 RT.022, Simpang Pasir Palaran, Samarinda - Kalimantan Timur</div>
                    <div class="pb-1" style="width: 100%; font-size: 10px;">TELP : 0541 - 4104142, | FAX : 0541 - 4104195</div>
                    <div class="pb-1" style="width: 100%; font-size: 11px;">E-Mail : sml@indo.net.id</div>
                  </div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%;">Kepada Yth : </div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%;">`+dataPrint[0].NAMA+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%;">`+dataPrint[0].ALAMAT1+`</div>
                </div>
              </div>

              <div style="width: 40%; margin-left: 30px; margin-top: 15px;">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">PURCHASE ORDER</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">PO Number</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">`+dataPrint[0].nobukti+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">Date</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">Due Date</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">`+tanggalKirimOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">TOP</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">`+dataPrint[0].HARI+` Hari</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">Currency</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">`+dataPrint[0].KODEVLS+`</div>
                </div>
              </div>

            </div>
<table
class="detail-spb-table"
style="width: 95%; height: 100px; max-height: 100px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
            <thead>
              <tr>
                <td class="text-center" style="width: 2%" >No.</td>
                <td class="text-center" style="width: 35%">DESCRIPTION</td>
                <td class="text-center" style="width: 15%">PART NUMBER</td>
                <td class="text-center" style="width: 10%">BRAND</td>
                <td class="text-center" style="width: 8%">QTY</td>
                <td class="text-center" style="width: 5%">UOM</td>
                <td class="text-center" style="width: 10%">UNIT PRICE</td>
                <td class="text-center" style="width: 15%">AMOUNT</td>
              </tr>
            </thead> `;

let z = 0
let jumlahTotal = 0
let diskonTotal = 0
let subTotal = 0
let ppnTotal = 0
let totalTotal = 0

let tempPrintStr = ``
tempPrintStr += `<html>
<head>
  <title></title>
</head>
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
tempPrintStr += `
<tr>
  <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 2%; text-align: center;">${z+1}</td>
  <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 35%;">${itemSub.NAMABRG}</td>
  <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: center;">${itemSub.PartNumber}</td>
  <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 10%;">${itemSub.NAMAMERK ?? ''}</td>
  <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 8%; text-align: right;">${itemSub.QNT ? parseFloat(itemSub.QNT).toFixed(2) : ''}</td>
  <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: center;">${itemSub.SATUAN}</td>
  <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 10%; text-align: right;">${formatAngka(parseFloat(itemSub.harga).toFixed(2))}</td>
  <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: right;">${formatAngka(parseFloat(itemSub.SUBTOTALRp).toFixed(2))}</td>
</tr>`;
z++;
});

// Fill remaining empty rows   table is 225px, each row ~24px, header ~24px = ~8 total slots
const maxRows = 7;
const fillerCount = Math.max(0, maxRows - item.length);
for (let f = 0; f < fillerCount; f++) {
tempPrintStr += `
<tr style="height: 18px;">
  <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
  <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
  <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
  <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
  <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
  <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
  <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
  <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
</tr>`;
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

     tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 13px;">

<div style="width: 38%; font-family: sans-serif; font-size: 10px;">
<div style="display: flex; width: 100%">
  <h3 class="m-0 pb-2">Ship To:</h3>
</div>
<div style="display: flex; width: 100%">
  <div class="pb-1" style="width: 100%">CV. SINAR MAHAKAM LESTARI</div>
</div>
<div style="display: flex; width: 100%">
  <div class="pb-1" style="width: 100%">`+dataPrint[0].AlamatGudang+`</div>
</div>
<div style="display: flex; width: 100%">
  <h3 class="m-0 pb-2">Appointed Forwarder:</h3>
</div>
<div style="display: flex; width: 100%">
  <div class="pb-1" style="width: 100%">${dataPrint[0].Expedisi ?? ''}</div>
</div>
<div style="display: flex; width: 100%">
  <div class="pb-1" style="width: 100%">${dataPrint[0].almkirim ?? ''}</div>
</div>
<div style="display: flex; width: 100%">
  <h3 class="m-0 pb-2">Please send all original document to address below:</h3>
</div>
<div style="display: flex; flex-direction: column; width: 100%">
  <div class="pb-1">CV. SINAR MAHAKAM LESTARI</div>
  <div class="pb-1">Pergudangan Mangkupalas Centre, Jl. Ampera RT.22 Kel. Simpang Pasir Mangkupalas, Samarinda Seberang.</div>
  <div class="pb-1">Telp: +62 541-4104142 | UP. IBU ALVI</div>
</div>
<div style="display: flex; width: 100%">
  <div>User :</div>
  <div>`+dataPrint[0].Iduser+`</div>
</div>
</div>`

if(i == arrayDataPrint.length - 1){
      
tempPrintStr += `
<div style="width: 62%; font-family: sans-serif; font-size: 10px;">

<div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 2px;">
  <div style="width: 5%; text-align:left;"> JUMLAH </div>
  <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].tsub).toFixed(2))}</div>
</div>
<div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 4px; position: relative;">
  <div style="width: 5%; text-align:left;"> DISKON </div>
  <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].Tdisc).toFixed(2))}</div>

  <div style="
  position: absolute;
  right: 0;
  bottom: 0;
  width: 35%;
  border-bottom: 1px solid #000;"></div>
</div>
<div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 2px;">
  <div style="width: 5%; text-align:left;"> DPP </div>
  <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TSUBTOTALRp).toFixed(2))}</div>
</div>
<div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 6px; position: relative;">
  <div style="width: 5%; text-align:left;"> PPN </div>
  <div style="
    position: absolute;
    right: 0;
    bottom: 3px;
    width: 35%;
    border-bottom: 1px solid #000;">
  </div>

  <!-- garis bawah 2 -->
  <div style="
    position: absolute;
    right: 0;
    bottom: 0;
    width: 35%;
    border-bottom: 1px solid #000;">
  </div>
  <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TnppnRp).toFixed(2))}</div>
</div>
<div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 8px; font-weight: bold;">
  <div style="width: 5%; text-align:left;"> TOTAL </div>
  <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TnnetRp).toFixed(2))}</div>
</div>`};
  
 tempPrintStr += `
 <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
    <tr>
      <td class="no-border text-center" style="width: 40%; font-size:13px;">Approved By,</td>
      <td class="no-border text-center" style="width: 33%; font-size:13px;">Confirmed By,</td>
    </tr>
    <tr style="height: 2.5rem;">
      <td class="no-border" colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td class="no-border px-2">
        <p class="m-0"></p>
      </td>
      <td class="no-border px-2">
        <p class="m-0" style="border-bottom: 1px solid black; font-size:10px;">Name</p>
      </td>
    </tr>
    <tr>
      <td class="no-border px-2 text-center">
        <p class="m-0" style='font-size:10px;'>`+dataPrint[0].otouser+`</p>
        <p class="m-0" style='font-size:10px;'>
        ELECTRONICALLY APPROVED
     </p>
      </td>
      <td class="no-border px-2">
        <p class="m-0" style="border-bottom: 1px solid black; font-size:10px;">Date</p>
      </td>
    </tr>
  </table>
</div>

<div style="clear: both;"></div>
</div>

</div>`


    tempPrintStr += `</div>`
  });


  tempPrintStr +=  `</body></html>`

let w = window.open(' ');

w.document.open();
w.document.write(tempPrintStr);
w.document.close();

setTimeout(() => {
  w.focus();
  w.print();
  }, 300);
}




function buttonAddDeleteItem (i) {
  console.log('data hapus',i)

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log(dataTableAdd[i])
  let dataDelete = dataTableAdd[i]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataDelete.NamaBrg + ' ?',
      function() {

        let _token = $("#_token").val();
        let choice = "D"

        let nobukti = dataDelete.NoBukti
        let urut = dataDelete.Urut
        console.log('delete',nobukti,urut)
        $.ajax({
          url: "{!! url('penawaransospadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nobukti,
            NoUrut:0,
            Tanggal: '',
            TglJatuhTempo: '',
            KodeSupp: '',
            // Handling,
            KodeExp: '',
            Keterangan: '',
            // FakturSupp,
            KodeVls: '',
            Kurs: 0,
            PPn: 0,
            TipeBayar: 0,
            Hari: 0,
            // TipeDisc,
            // Disc,
            DiscRp: 0,
            urut,
            KodeBrg: 0,
            Qnt: 0,
            NoSat: 0,
            Satuan: '',
            Isi: 0,
            Harga: 0,
            DiscP: 0,
            // DiscTot,
            NoPPL: '',
            // IsClose,
            // IsCloseD,
            // Catatan,
            // IsExp,
            // Tolerate,
            UrutPPL: 0,
            Kodegdg: '',
            Discpdet2: 0,
            Discpdet3: 0,
            // Discpdet4,
            // Discpdet5,
            // FlagTipe,
            NamaBrg: '',
            // IsJasa,
            // pFirst,
            pFOC: 0,
            Noso: '',
            Jmlrecord: 0,
            NOPOCUST: '',
            // IdUser,
            // pJasa,
            // NPPH23,
            // PERKIRAAN,
            // SatX,
            // COST,
            // SUBCOST,
            TglKirim: '',
            // PPH21,
            NOPNw: '',
            UrutPNW: 0

          },
          success: function(res) {
            console.log('resspsoadd', res)
            loadAll()

            // lockFormAdd()
            $('.showhide').hide();

            refreshDataTableAdd(nobukti)

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

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}

function searchBarangAll (e) {
  if (e.which == 13) {
    console.log('enter')

    let search = $("#input_search_barang_all").val();

    $('#tabel_add_list_barangall').DataTable().destroy();

  }

}

function formatAngka (angkaString) {
  // console.log('formatAngka' , angkaString);
  let tempAngka = angkaString.split('.')
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
    // console.log(i, temp1)
  }
  temp1 += '.' + tempAngka[1]
  return temp1
};

function reverseCalculateDiscPercent() {
  let harga = parseFloat($('#input_add_add_harga').val()) || 0;
  let discRp = parseFloat(document.getElementById('input_add_add_discrp').value) || 0;

  // Clear all discount percentage fields first
  document.getElementById('input_add_add_discpersen1').value = 0;
  document.getElementById('input_add_add_discpersen2').value = 0;
  document.getElementById('input_add_add_discpersen3').value = 0;

  // If harga is 0, we can't calculate percentage
  if (harga === 0) {
    return;
  }

  // Calculate the discount percentage
  let discPercent = (discRp / harga) * 100;

  // Validate that discount doesn't exceed 100%
  if (discPercent > 100) {
    alert("Diskon tidak boleh melebihi harga");
    document.getElementById('input_add_add_discrp').value = "";
    return;
  }

  // Set the first discount percentage field
  document.getElementById('input_add_add_discpersen1').value = discPercent.toFixed(2);
}

function calculateDiscRp() {
  let disc1 = document.getElementById('input_add_add_discpersen1').value
  let disc2 = document.getElementById('input_add_add_discpersen2').value
  let disc3 = document.getElementById('input_add_add_discpersen3').value

  let discRp = $('#input_add_add_harga').val()

  disc1 = parseFloat(disc1) || 0
  disc2 = parseFloat(disc2) || 0
  disc3 = parseFloat(disc3) || 0
  discRp = parseFloat(discRp) || 0

  if (disc1 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen1').value = ""
    return
  }
  if (disc2 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen2').value = ""
    return
  }
  if (disc3 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen3').value = ""
    return
  }

  let currentAmount = discRp
  let totalDiscount = 0

  if (disc1 > 0) {
    let afterDiskon1 = currentAmount * (disc1/100)
    currentAmount = currentAmount - afterDiskon1
    totalDiscount += afterDiskon1
  }

  if (disc2 > 0) {
    let afterDiskon2 = currentAmount * (disc2/100)
    currentAmount = currentAmount - afterDiskon2
    totalDiscount += afterDiskon2
  }

  if (disc3 > 0) {
    let afterDiskon3 = currentAmount * (disc3/100)
    currentAmount = currentAmount - afterDiskon3
    totalDiscount += afterDiskon3
  }

  document.getElementById('input_add_add_discrp').value = totalDiscount
}

function LockFreeOfCharge(){
  let focState = document.getElementById('input_add_add_foc').value

  if (focState == 1){
    document.getElementById('input_add_add_harga').disabled = true;
    document.getElementById('input_add_add_discrp').disabled = true;
    document.getElementById('input_add_add_discpersen1').disabled = true;
    document.getElementById('input_add_add_discpersen2').disabled = true;
    document.getElementById('input_add_add_discpersen3').disabled = true;

    document.getElementById('input_add_add_harga').value = 0,00 ;
    document.getElementById('input_add_add_discrp').value = 0,00 ;
    document.getElementById('input_add_add_discpersen1').value = 0 ;
    document.getElementById('input_add_add_discpersen2').value = 0 ;
    document.getElementById('input_add_add_discpersen3').value = 0 ;
  } else {
    document.getElementById('input_add_add_harga').disabled = false;
    document.getElementById('input_add_add_discrp').disabled = false;
    document.getElementById('input_add_add_discpersen1').disabled = false;
    document.getElementById('input_add_add_discpersen2').disabled = false;
    document.getElementById('input_add_add_discpersen3').disabled = false;
  }

}

</script>
  <script>
    function performSearchSupplier () {
      const searchValue = document.getElementById('input_add_kodesupplier').value.trim();

      buttonAddListPelanggan();

      // Apply search to all DataTables
      $('#tabel_add_list_pelanggan').DataTable().search(searchValue).draw();
    }

    // Keyboard event
    document.getElementById('input_add_kodesupplier').addEventListener('keypress', function(event) {

      if (event.key === 'Enter') {
         let kode = document.getElementById("input_add_kodesupplier").value.trim();
          if (kode != "-") {
          event.preventDefault();
          performSearchSupplier();
        }
      }
    });

    function performSearch () {
      const searchValue = document.getElementById('input_add_add_kodebarang').value.trim();

      buttonAddAddListBarang();

      // Apply search to all DataTables
      $('#tabel_add_list_barang_nonfoc').DataTable().search(searchValue).draw();
      $('#tabel_add_list_barang_nonfocplus').DataTable().search(searchValue).draw();
      $('#tabel_add_list_barang_foc').DataTable().search(searchValue).draw();
    }

    // Keyboard event
    document.getElementById('input_add_add_kodebarang').addEventListener('keypress', function(event) {
      if (event.key === 'Enter') {
          event.preventDefault();
          performSearch();
      }
    });

    window.onload = function(){
      loadAll();
    };

    function checkKodeSupplier() {
      console.log('cek supp');
      let kode = document.getElementById("input_add_kodesupplier").value.trim();
      let nama = document.getElementById("input_add_namasupplier");

      if (kode === "-") {
          nama.disabled = false;
          nama.value = "";
          setNewNoBukti(1);
          nama.focus();
      } else {
          nama.disabled = true;
      }
  }
  </script>

@endsection
