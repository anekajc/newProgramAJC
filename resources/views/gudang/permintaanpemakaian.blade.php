@extends('gudang.newmasterx')
@section('buttons')
@endsection

{{-- Bootstrap 4 only (CSS v4.5.0 / JS v4.6.2 via gudang/newmaster.blade.php).
     BS5 class names (fw-bold, form-select, rounded-end, ms-*/me-*, text-end) are
     undefined here and silently do nothing — use the BS4 names. --}}

{{-- tampilan search bar 1 (tab PPI Belum Otorisasi, DataTables bawaan) --}}
{{-- report-table.css / report-table.js dimuat dari gudang/newmaster.blade.php (layout bersama),
       bukan per-halaman — lihat catatan di layout tersebut. --}}
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

        /* Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar. Ditulis lokal
                   di sini (bukan di report-table.css) meniru pola .po-len-wrap milik
                   purchasing/purchaseOrder.blade.php — supaya halaman lain yang memakai
                   report-table.css tidak ikut berubah. Warna/border memakai variabel
                   --white/--border/--muted milik .tb-report di report-table.css supaya
                   tetap seragam dengan kotak search & tombol Filter di sebelahnya. */
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

        /* Tombol Prev/Next dinonaktifkan (halaman pertama/terakhir) — .pg/.pg.active
                   sudah ada di report-table.css, .disabled belum. */
        .tb-report .pg.disabled {
            opacity: .4;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
    {{-- end tampilan search bar 1 --}}

    {{-- Tab strip, rt-fixed-th, pill buttons, action-buttons, and the .tb-report
         DataTables chrome rules moved to public/css/tableMaster2.css (loaded globally
         by gudang/newmaster.blade.php) so other gudang pages can reuse them. The pill
         button rule there is scoped #contentContainer .btn.btn-pill-primary/-secondary
         so it beats tableMaster2.css's own #contentContainer .btn rule — see the
         comment in that file. --}}
@endsection
@section('content')
    <div id="imagecontainer" class="d-none" style="">
        <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
    </div>

    <div id="loadingContainer" style="display: none" class="container-fluid mt-6 justify-content-middle align-middle">
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <div id="printContainer" style="display:none">

    </div>

    <div id="tempPrintContainer" style="display:none">

    </div>

    <div id="contentContainer" class="container-fluid">
        <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
        <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
        <input type="hidden" id="akses_isotorisasi1" value="{{ $akses->IsOtorisasi1 }}">
        <input type="hidden" id="akses_isbatal" value="{{ $akses->IsBatal }}">
        <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
        <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS !!}" />
        <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
        <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />

        <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

        <div id="pageHome" class="tb-report main">
            <div class="content">

                <div class="toolbar">
                    {{-- <div class="page-title">Permintaan Pemakaian Internal</div> --}}

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

                    {{-- Jumlah baris per halaman. -1 = tampilkan semua data (tanpa pager) —
                         lihat renderTabel()/onLenChange2() di bawah. --}}
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

                    <div class="po-toolbar-act action-group">
                        <button type="button" class="btn btn-primary" onclick="buttonAdd()">
                            {{-- <i class="bi bi-plus-lg"></i>Add</button> --}}
                            Tambah</button>
                    </div>
                </div>

                <!-- Bar kolom tersembunyi (diisi oleh report-table.js / ReportTable) -->
                <div id="rtBar"></div>

                <div class="table-outer">
                    <div class="table-wrap">
                        <table class="tb aksi-hover" id="mainTable">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tabel2_data"></tbody>
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
                            <div>
                                <label class="rt-field-label" for="modalStatus">Status</label>
                                <select class="rt-native" id="modalStatus">
                                    <option value="2">Semua</option>
                                    <option value="1">Terkirim</option>
                                    <option value="0">Belum Terkirim</option>
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


    <!-- <div> -->


    <!-- start pageForm (Add/Edit, full page) -->
    <div id="pageForm" class="container-fluid" style="display:none">

        {{-- .tb-report cuma dipakai buat header supaya dapat gaya .toolbar/.page-title —
             DIBATASI ke sini saja, TIDAK membungkus seluruh halaman, karena
             `.tb-report *{margin:0;padding:0}` di report-table.css akan menghapus
             gutter grid Bootstrap dan padding form-control di bawahnya. --}}
        <div class="tb-report" id="contentContainer">
            <div class="content">
                <div class="toolbar">
                    <div class="page-title" id="pageForm_title"></div>
                    <div class="action-group">
                        <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                            onclick="buttonCloseForm()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
                <!-- No Bukti -->
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-bold">No Bukti</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="input_add_nobukti"
                                style="box-shadow: 0 0 5px rgba(81, 203, 238, 1); border: 1px solid rgba(81, 203, 238, 1);"
                                disabled>
                        </div>
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-bold">Tanggal</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="input_add_tanggal"
                                value="{!! date('Y-m-d') !!}">
                        </div>
                    </div>
                </div>

                <!-- Gudang -->
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-bold">Gudang</label>
                        <div class="col-sm-8">
                            <select id="input_add_gudang" class="form-control">
                                <option value="0" selected disabled>-- Pilih Gudang --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br />
        <div class="container-fluid">
            <div class="row ">
                <div class="col-md-12 text-right" id="contentContainer">
                    <button type="button" class="btn btn-action-primary btn-primary btn-pill-primary"
                        onclick="buttonAddAdd()">
                        Tambah Item
                    </button>
                </div>

                <!-- koreksi add -->
                <div class="tb-report container-fluid mt-4">
                    <div class="table-outer">
                        <div class="table-wrap" style="max-height:40vh;">
                            <table id="addTable" class="tb">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="addTableData">
                                    <tr>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="formAddAdd" class="container-fluid showhide">
                    <!-- <div class="line"></div> -->
                    <br />
                    <div class="row">
                        <div class="col-12">
                            <h4>Tambah Item</h4>
                        </div>
                    </div>

                    <div class="container-fluid">
                        <!-- Baris 1 -->
                        <div class="row">
                            <!-- Kode Barang -->
                            <div class="col-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label font-weight-bold">Kode Barang</label>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <input id="AddAddKodeBrg" type="text" class="form-control text-left"
                                                placeholder="Kode Barang" onkeypress="onKeyPressBarang(event)">
                                            <button type="button" onclick="buttonAddListBarang()"
                                                class="btn btn-primary">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Qty -->
                            <div class="col-2">
                                <div class="row align-items-center">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Qty</label>
                                    <div class="col-sm-8">

                                        <input type="text" id="AddAddInputQty"
                                            class="form-control text-right input-partial-number">
                                    </div>
                                </div>
                            </div>

                            <!-- Satuan -->
                            <div class="col-3">
                                <div class="row align-items-center">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Satuan</label>
                                    <div class="col-sm-8">
                                        <select id="AddAddSatuan" class="form-control text-center">
                                            <option value="0" selected disabled>-- Pilih Satuan --
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 2 -->
                        <div class="row mt-2">
                            <!-- Nama Barang -->
                            <div class="col-8">
                                <div class="row align-items-center">
                                    <label class="col-3 col-form-label font-weight-bold">Nama Barang</label>
                                    <div class="col">
                                        <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 text-right" id="contentContainer">
                            <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                                onclick="buttonBatalAdd()">Batal</button>

                            <button type="button" class="btn btn-action-primary btn-primary btn-pill-primary" onclick="submitAddAdd()">Simpan</button>
                        </div>

                    </div>
                    <!-- <div class="line"></div> -->
                    <hr />
                </div>
                <!-- end -->

                <!-- koreksi edit -->
                <div id="formAddEdit" class="container-fluid showhide">
                    <!-- <div class="line"></div> -->
                    <br />
                    <div class="row">
                        <div class="col-12">
                            <h4>Edit Item</h4>
                        </div>
                    </div>

                    <div class="container-fluid">
                        <!-- Baris 1 -->
                        <div class="row">
                            <!-- Kode Barang -->
                            <div class="col-6">
                                <div class="row align-items-center">
                                    <label class="col-4 col-form-label font-weight-bold">Kode Barang</label>
                                    <div class="col">
                                        <div class="input-group">
                                            <input id="AddEditKodeBrg" type="text" class="form-control text-left"
                                                placeholder="Kode Barang" disabled>
                                            <!-- Tombol Plus (sementara di-comment sesuai kode asli) -->
                                            <!--
                                                                                      <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary btn-sm rounded-right shadow-sm">
                                                                                        <i class="bi bi-plus"></i>
                                                                                      </button>
                                                                                      -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Qty -->
                            <div class="col-2">
                                <div class="row align-items-center">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Qty</label>
                                    <div class="col-sm-8">

                                        <input type="text" id="AddEditInputQty" data-a-sign="" data-a-dec="."
                                            data-a-sep="," class="form-control text-right input-partial-number">
                                    </div>
                                </div>
                            </div>

                            <!-- Satuan -->
                            <div class="col-3">
                                <div class="row align-items-center">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Satuan</label>
                                    <div class="col-sm-8">
                                        <select id="AddEditSatuan" class="form-control text-center">
                                            <option value="0" selected disabled>-- Pilih Satuan --
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <!-- Baris 2 -->
                        <div class="row mt-2">
                            <!-- Nama Barang -->
                            <div class="col-8">
                                <div class="row align-items-center">
                                    <label class="col-3 col-form-label font-weight-bold">Nama Barang</label>
                                    <div class="col">
                                        <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-outline-danger"
                                onclick="buttonBatalAdd()">Batal</button>

                            <button type="button" onclick="submitAddEdit()" class="btn btn-pill-primary">Simpan</button>
                        </div>

                    </div>
                    <!-- <div class="line"></div> -->
                    <hr />
                </div>
                <!-- end -->
            </div>
        </div>
    </div>
    <!-- End pageForm -->


    <!-- start modal list item add -->
    <div class="modal fade rt-picker-v2" id="formAddListItem" tabindex="-1" role="dialog"
        aria-labelledby="formAddListItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formAddListItemLabel">Pilih Barang</h5>
                    <button type="button" class="close" onclick="closeFormList()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <table id="tabel_add_list_item" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Kode Barang</th>
                                <th scope="col">Nama Barang</th>
                            </tr>
                        </thead>

                        <tbody>

                        </tbody>

                    </table>

                </div>
            </div>
        </div>
    </div>
    <!-- End modal list item add-->


    <!-- start pageDetail (Detail, full page) -->
    <div id="pageDetail" class="container-fluid" style="display:none">

        {{-- .tb-report cuma dipakai buat header — lihat catatan di pageForm di atas
             soal kenapa TIDAK membungkus seluruh halaman. --}}
        <div class="tb-report" id="contentContainer">
            <div class="content">
                <div class="toolbar">
                    <div class="page-title"></div>
                    <div class="action-group">
                        <button type="button" class="btn btn-action-danger btn-danger btn-pill-primary"
                            onclick="buttonCloseForm()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <input type="hidden" name="noUrut" id="input_detail_koreksi_noUrut" value="" />

                <!-- No Bukti -->
                <div class="col-md-6">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label">No Bukti</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="input_detail_koreksi_nobukti" placeholder=""
                                disabled>
                        </div>
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="col-md-6">
                    <div class="row align-items-center">
                        <label class="col-sm-4 col-form-label">Tanggal</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="input_detail_koreksi_tanggal"
                                value="{!! date('Y-m-d') !!}" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row ">
                <div class="tb-report container-fluid mt-4">
                    <div class="table-outer">
                        <div class="table-wrap" style="max-height:40vh;">
                            <table id="detailKoreksiTable" class="tb">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody id="detailKoreksiTableData">
                                    <tr class="empty-row">
                                        <td colspan="4">Data tidak ditemukan</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End pageDetail -->
@endsection

@section('js')
    <script type="text/javascript">
        let listBarang = []
        let barangCacheAll = null
        let listLokasi = []

        let listItemForm = []
        let itemAddObj = {}
        let itemEditObj = {}


        // console.log(Number('0019'))

        jQuery(function($) {
            $('.input-partial-number').autoNumeric('init', {
                minimumValue: '0',
                // negativeSignCharacter: 'z'
            });
        });

        /* ============================================================================
         * Tabel interaktif gabungan (belum + sudah otorisasi) — port halaman-lokal dari
         * mesin gcart_header di report/masterreport2.blade.php, karena halaman ini
         * @@extends('gudang.newmaster') dan bukan report.masterreport2 (lihat
         * docs/new-slider-table-guide.md). doShowCustomize()/doButtonSubtotal()/
         * doButtonGrandtotal() sengaja tidak diikutkan — itu untuk modal "Atur Kolom"
         * yang tidak ada di halaman ini; report-table.js memanggil onChange sendiri.
         * ========================================================================= */
        let lastRows = @json($penerimaanArray); // paint pertama tanpa AJAX; reloadData() menyegarkan setelahnya
        let globalOtorisasi = "2"; // filter modal: 2=Semua, 1=Sudah Otorisasi, 0=Belum Otorisasi
        let globalStatus = "2"; // filter modal: 2=Semua, 1=Terkirim, 0=Belum Terkirim

        // Dropdown "Tampilkan" (#tabelLen2) — jumlah baris per halaman. -1 = semua data,
        // sama seperti pola poLen1 di purchasing/purchaseOrder.blade.php, tapi paging di
        // sini murni client-side (renderTabel() sudah memegang seluruh lastRows), bukan
        // lewat DataTables. tabelPage2 disimpan terpisah dari elemen select supaya tidak
        // ikut ter-reset saat renderTabel() dipanggil ulang oleh onChange report-table.js.
        let tabelLen2 = 10;
        let tabelPage2 = 1;

        var g_href = 'permintaanpemakaian';
        // '2' (bukan '0'/'1') supaya user lama dengan layout tersimpan otomatis dapat key
        // baru dan setDefaultHeader() jalan lagi — kalau tidak, kolom Status baru tidak
        // akan pernah muncul buat mereka (doLoadHeader mengembalikan string lama), dan
        // yang sempat menyimpan versi 'float' tetap kebawa rata kanan.
        var g_modeReport = '2';
        var gcart_header = [];
        var gsum_issubtotal = 0;
        var gsum_isgrandtotal = 0;
        var gct_desimal_max = 4;

        function setDefaultHeader() {
            // [ field, label, visible, type, total, decimals ]
            gcart_header = [
                ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
                ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                ['OtoUser1', 'User Oto', 1, 'varchar', 0, 0],
                ['TglOto1', 'Tanggal Oto', 1, 'date', 0, 0],
                ['IsOtorisasi1', 'Otorisasi', 1, 'varchar', 0, 0],
                // 'varchar', bukan 'float' — nilainya dirender jadi badge Terkirim/Belum
                // Terkirim, jadi jangan diperlakukan sebagai kolom angka (rata kanan).
                ['QntOS', 'Status', 1, 'varchar', 0, 0],
                ['KodeGdg', 'Kode Gudang', 0, 'varchar', 0, 0],
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

        // Ambil field dari row tanpa peduli besar/kecil huruf — VWMASTERPRPENYERAHANBHN
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
        // server selalu mengembalikan seluruh rentang tanggal.
        function filterByOtorisasi(rows, filterVal) {
            if (filterVal === '1') {
                return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 1);
            }
            if (filterVal === '0') {
                return rows.filter(r => Number(pickCI(r, 'IsOtorisasi1')) === 0);
            }
            return rows;
        }

        // QntOS = sisa qty yang belum diserahkan (dijumlah per NOBUKTI di controller).
        // 0 berarti seluruh item sudah dikirim; angka berapa pun di atas 0 berarti masih
        // ada sisa. Nilai kosong/null diperlakukan 0 supaya tidak jadi "Belum Terkirim" palsu.
        function isTerkirim(r) {
            return Number(pickCI(r, 'QntOS') || 0) <= 0;
        }

        // #modalStatus: 2=Semua, 1=Terkirim, 0=Belum Terkirim — client-side, sama seperti
        // filter otorisasi.
        function filterByStatus(rows, filterVal) {
            if (filterVal === '1') {
                return rows.filter(r => isTerkirim(r));
            }
            if (filterVal === '0') {
                return rows.filter(r => !isTerkirim(r));
            }
            return rows;
        }

        // Warna, ikon dan urutan tombol sengaja disamakan dengan kolom Actions di
        // purchasing/purchaseOrder.blade.php supaya konsisten antar halaman:
        // Detail=amber bi-info, Otorisasi=biru bi-key, Edit=hijau bi-pencil-fill,
        // Batal Otorisasi=merah bi-key-fill, Print=cyan bi-printer.
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
                nobukti + '\', \'' + r.NoUrut + '\')"><i class="bi bi-pencil-fill"></i></button>' +
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
            thead.innerHTML = ReportTable.headHtml(cols).replace('<tr>', '<tr><th class="rt-fixed-th">Action</th>');

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
            rows = filterByStatus(rows, globalStatus);

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
                    if (c[0] === 'QntOS') {
                        return isTerkirim(r) ?
                            '<td><span class="sp-badge is-active">Terkirim</span></td>' :
                            '<td><span class="sp-badge is-inactive">Belum Terkirim</span></td>';
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
            // container:'body' — tanpa ini tooltip disisipkan sebagai sibling tombol di
            // dalam <td>, jadi mepet/numpuk di atas tombol (baris tabel sempit) dan
            // menghalangi klik. Dengan container:'body', Popper bebas menaruhnya di atas baris.
            // boundary:'window' — default Popper di tooltip Bootstrap 4 adalah 'scrollParent'
            // (di sini .table-wrap yang overflow-y:auto dan pendek), jadi Popper mengira tidak
            // ada ruang buat flip ke atas dan malah numpuk di tombol. 'window' bikin Popper
            // ukur ruang dari seluruh viewport, bukan dari kotak scroll tabel yang sempit.
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

        /* -- FILTER MODAL (Otorisasi: Semua/Sudah Otorisasi/Belum, Status: Semua/Terkirim/Belum) -- */
        function updateFilterBadge() {
            let count = ($('#modalOtorisasi').val() !== '2') ? 1 : 0;
            count += ($('#modalStatus').val() !== '2') ? 1 : 0;
            $('#filterBadge').text(count + ' aktif');
        }

        function resetAllFilters() {
            $('#modalOtorisasi').val('2');
            $('#modalStatus').val('2');
            updateFilterBadge();
        }

        $(document).on('show.bs.modal', '#modalFilter', function() {
            $('#modalOtorisasi').val(globalOtorisasi);
            $('#modalStatus').val(globalStatus);
            updateFilterBadge();
        });

        $(document).on('change', '#modalFilter select.rt-native', updateFilterBadge);

        function applyModalFilter() {
            globalOtorisasi = $('#modalOtorisasi').val();
            globalStatus = $('#modalStatus').val();
            renderTabel();
            $('#modalFilter').modal('hide');
        }

        /* -- FULL PAGE SWITCH (pageHome / pageForm / pageDetail) — pola sama dengan
           gudang/ubahkemasanbarang.blade.php, bukan modal Bootstrap. -- */
        function showPage(id) {
            $('#pageHome, #pageForm, #pageDetail').hide();
            $('#' + id).show();
        }

        function buttonCloseForm() {
            $('.showhide').hide();
            showPage('pageHome');
        }



        $(document).ready(function() {

            initBarangTable([])

            // Satu-satunya titik di mana modal pemilih barang dijamin sudah
            // display:block, jadi di sinilah lebar kolom boleh dihitung.
            $('#formAddListItem').on('shown.bs.modal', function() {
                if (barangTablePending !== null) {
                    initBarangTable(barangTablePending, barangSearchPending)
                } else if (barangTableDT) {
                    resetBarangTableWidths()
                    barangTableDT.columns.adjust()
                    barangTableDT.search(barangSearchPending || '').draw()
                    barangSearchPending = null
                }
            });

            // Tabel gabungan — mesin interaktif (drag/gear/bar), lihat
            // docs/new-slider-table-guide.md. doSetHeader() memuat layout tersimpan
            // milik user ini untuk halaman+mode ini, atau menyimpan setDefaultHeader()
            // kalau ini kunjungan pertama.
            doSetHeader(g_modeReport);
            ReportTable.init({
                table: '#mainTable',
                bar: '#rtBar',
                onChange: renderTabel
            });
            renderTabel();

        });

        function buttonOtorisasi(nobukti, isOtorisasi) {
            let akses = $("#akses_isotorisasi1").val();
            if (!Number(akses)) {
                alertify.warning('No access');
                return;
            }
            console.log(nobukti, isOtorisasi)

            if (Number(isOtorisasi) > 0) {
                alertify.warning('Sudah diotorisasi');
                return;
            }

            let _token = $("#_token").val();

            $.ajax({
                url: "{!! url('permintaanpemakaianotorisasi') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    nobukti,
                    otorisasi: 1
                },
                success: function(res) {
                    if (res > 0) {
                        alertify.success('Berhasil otorisasi');
                        reloadData();
                    } else {
                        alertify.warning('Gagal otorisasi');
                    }
                },
                error: function(err) {
                    console.log(err);
                    alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
                }
            });
        }



        function buttonBatalOtorisasi(nobukti, isOtorisasi) {
            let akses = $("#akses_isbatal").val();
            // if (!Number(akses)) {
            //   alertify.warning('No access');
            //   return;
            // }

            if (Number(isOtorisasi) === 0) {
                alertify.warning('Belum diotorisasi');
                return;
            }

            // alertify.prompt(JUDUL, PESAN, NILAI_AWAL, onOK, onCancel)
            // Argumen pertama = judul di header dialog. Kalau argumen ini tidak diisi
            // (bentuk 4 argumen), AlertifyJS memakai default 'AlertifyJS'
            // (lihat public/js/alertify.js baris 50, alertify.defaults.glossary.title).
            // Kelas 'ajs-app-buttons' + 'is-danger' ditempel ke root dialog supaya CSS
            // tombol OK/Cancel di public/css/report-table.css (blok "gaya bersama")
            // hanya kena dialog ini, bukan semua dialog alertify di aplikasi.
            // 'is-danger' bikin tombol OK merah karena batal otorisasi aksi merusak.
            var dlgBatalOtorisasi = alertify.prompt("Batal Otorisasi", "Masukkan keterangan batal otorisasi nomor   " +
                nobukti, "",
                function(evt, value) {
                    // alertify.success("You entered: " + value);
                    let xpket = value;

                    if (xpket == '') {
                        alertify.warning('Keterangan harus diisi.');
                        $.abort();
                    }
                    let _token = $("#_token").val();

                    $.ajax({
                        url: "{!! url('permintaanpemakaianbatalotorisasi') !!}",
                        type: "post",
                        async: false,
                        data: {
                            _token,
                            nobukti,
                            otorisasi: 0,
                            pket: value
                        },
                        success: function(res) {
                            if (res > 0) {
                                alertify.success('Berhasil batal otorisasi');
                                reloadData();
                            } else {
                                alertify.warning('Gagal batal otorisasi');
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
                        }
                    });
                },
                function() {
                    console.log('Pembatalan Otorisasi dibatalkan');
                    alertify.error("Action cancelled");
                }
            );
            dlgBatalOtorisasi.elements.root.classList.add('ajs-app-buttons', 'is-danger');
        }

        // Menggantikan loadAll() lama — satu list gabungan, difilter di server berdasarkan
        // rentang tanggal yang sedang dipilih. Dipanggil saat tanggal berubah dan setelah
        // otorisasi/batal otorisasi/simpan/hapus supaya tabel menyegarkan diri sendiri.
        function reloadData() {
            $.ajax({
                url: "{!! url('permintaanpemakaianloadall') !!}",
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

        function submitPrint(nobukti) {
            // for (var i = 0; i < 30; i++) {
            //   dataPrint.push(dataPrint[0])
            // }
            let _token = $('#_token').val()
            $.ajax({
                url: "{!! url('permintaanpemakaiandetailCetak') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    NOBUKTI: nobukti
                },
                success: function(res) {
                    console.log(res)

                    dataPrint = res
                    console.log(res[0])
                    console.log(res[0][0])

                    // console.log(res[0][0].IsOtorisasi1)

                }
            })

            let arrayDataPrint = []
            for (let i = 0; i < dataPrint.length; i += 7) {
                let tempArray = dataPrint.slice(i, i + 7)
                arrayDataPrint.push(tempArray)
            }

            let printContent = ''
            let imageContent = document.getElementById(`imagecontainer`).innerHTML;
            let css = ''
            let hdr = ''
            let str = ''
            let ftr = ''
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

      .p-2 {
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
                    ` + imageContent + `
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
                  <h2 class="m-0 pb-2">BUKTI PERMINTAAN PEMAKAIAN INTERNAL</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">` + dataPrint[0].NoBukti + `</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">` + tanggalOnly + `</div>
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
                ` + printContent + `
              </div>
            </div>
      <table

                class="detail-spb-table"
                style="width: 100%; height: 225px; max-height: 225px;font-family: sans-serif;  display: table;
                font-size: 10px">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 50%">URAIAN BARANG</td>
                    <td class="text-center" style="width: 5%">SATUAN</td>
                    <td class="text-center" style="width: 5%">QTY</td>
                  </tr>
                </thead> `;

            let z = 0
            let tempPrintStr = ``
            tempPrintStr += `<html>
    <head>
      <title></title>
    </head>

    <body onload="window.print()">
      ` + css

            arrayDataPrint.forEach((item, i) => {
                console.log('arrayDataPrint', i)
                if (i == 0) {

                    tempPrintStr +=
                        `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
                    // } else if ( i < 1) {
                    //   tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:15px; page-break-before: always">`
                } else {
                    tempPrintStr +=
                        `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px;padding-top:7px; ">`
                }
                tempPrintStr += hdr
                tempPrintStr += `<tbody border="1">`;
                item.forEach((itemSub, j) => {
                    tempPrintStr += ``



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
         </tr>`;

                    z++;

                });
                tempPrintStr += `
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
         </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Diminta Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Disetujui Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
              <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
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


            tempPrintStr += `</body></html>`



            w = window.open(' ')
            w.document.write(tempPrintStr)
            w.print()
            w.close()

        }

        function closeListItemAdd() {
            $('#formAddListItem').modal('hide');
        }

        function closeFormList() {
            $('#formAddListItem').modal('hide');
        }


        function submitAddEdit() {
            console.log('submitAddEdit')
            console.log(itemEditObj)
            let _token = $("#_token").val();
            let qntTerima = formatAngkaVal($("#AddEditInputQty").val())
            let satuan = $("#AddEditSatuan").val()
            console.log({
                _token: _token,
                choice: 'U',
                urut: itemEditObj.URUT,
                nobukti: itemEditObj.NOBUKTI,
                nourut: itemEditObj.NoUrut,
                qntTerima,
                kodebrg: itemEditObj.KODEBRG,
                namabrg: itemEditObj.NAMABRG,
                qnt1: 0,
                qnt2: 0,
                qntx: qntTerima,
                nosat: satuan,
                tanggal: itemEditObj.TANGGAL,
                jmlRecord: 1,
                kodegdg: itemEditObj.KodeGdg
            })
            // return

            if (Number(qntTerima) <= 0) {
                alertify.warning("Qnt <= 0")
                return
            }
            if (!satuan) {
                alertify.warning("satuan harus diisi")
                return
            }

            $.ajax({
                url: "{!! url('permintaanpemakaianspadd') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    choice: 'U',
                    urut: itemEditObj.URUT,
                    nobukti: itemEditObj.NOBUKTI,
                    nourut: itemEditObj.NoUrut,
                    qntTerima,
                    kodebrg: itemEditObj.KODEBRG,
                    namabrg: itemEditObj.NAMABRG,
                    qnt1: 0,
                    qnt2: 0,
                    qntx: qntTerima,
                    nosat: satuan,
                    tanggal: itemEditObj.TANGGAL,
                    jmlRecord: 1,
                    kodegdg: itemEditObj.KodeGdg
                },
                success: function(res) {
                    console.log(res)
                    console.log('masuk success')
                    if (res == 1) {
                        console.log(res, '!')

                        refreshTableAdd()
                        reloadData()
                        // Form Edit sengaja TIDAK ditutup (cuma tombol Batal yang menutup).
                        // itemEditObj disinkronkan ulang dari listItemForm hasil refresh supaya
                        // submit berikutnya tetap menyasar baris yang sama dan isian di layar
                        // sama dengan yang tersimpan.
                        syncFormEditFromList()
                        alertify.success('Permintaan gudang telah diedit');
                    }
                },
                error: function(err) {
                    console.log('masuk error')
                    console.log(err)
                }
            })


        }

        function submitAddAdd() {
            console.log('submitAddAdd')

            let _token = $("#_token").val();
            let nobukti = $('#input_add_nobukti').val()
            let nourut = $('#input_add_noUrut').val()
            let qntTerima = formatAngkaVal($("#AddAddInputQty").val())
            let kodebrg = $("#AddAddKodeBrg").val()
            let namabrg = $("#AddAddNamaBrg").val()
            let kodegdg = $("#input_add_gudang").val()
            // let lokasiasal = $("#AddAddLokasiAsal").val()
            // let lokasitujuan = $("#AddAddLokasiTujuan").val()
            let satuan = $("#AddAddSatuan").val()
            let tanggal = $("#input_add_tanggal").val()
            // let keterangan = $("#input_add_keterangan").val()

            let checkDate = new Date(tanggal)

            let periode_bulan = document.getElementById("periode_bulan").value
            let periode_tahun = document.getElementById("periode_tahun").value

            let jmlRecord = 0
            if (listItemForm.length) {
                jmlRecord = 1
            }
            console.log(jmlRecord)
            // return

            if (!kodegdg) {
                alertify.warning("Gudang harus diisi")
                return
            }
            if (!kodebrg) {
                alertify.warning("kodebarang harus diisi")
                return
            }

            if (kodebrg != itemAddObj.KODEBRG) {
                alertify.warning("Barang tidak sesuai dengan pilihan")
                return

            }


            // if (!lokasiasal) {
            //   alertify.warning("lokasiasal harus diisi")
            //   return
            // }
            // if (!lokasitujuan) {
            //   alertify.warning("lokasitujuan harus diisi")
            //   return
            // }


            if (Number(qntTerima) <= 0) {
                alertify.warning("Qnt <= 0")
                return
            }
            if (!satuan) {
                alertify.warning("satuan harus diisi")
                return
            }

            if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {

                alertify.warning("Tanggal tidak sesuai periode");
                return
            }
            // kalo nosat 1 qnt , qnt2 langsung dr input
            //kalo nosat 2/3 , qnt dikali isi qnt 2 dr input
            let qnt2 = qntTerima
            let qnt1 = 0
            if (satuan == 1) {
                qnt1 = qntTerima
            } else if (satuan == 2) {
                qnt1 = qntTerima * itemAddObj.ISI2
            } else if (satuan == 3) {
                qnt1 = qntTerima * itemAddObj.ISI3
            }
            let qntx = 0

            console.log(itemAddObj)
            console.log('nobukti', nobukti)
            console.log('nourut', nourut)
            console.log('qntTerima', qntTerima)
            console.log('kodebrg', kodebrg)
            console.log('namabrg', namabrg)
            // console.log('lokasiasal' , lokasiasal)
            // console.log('lokasitujuan' , lokasitujuan)
            console.log('satuan', satuan)
            console.log('tanggal', tanggal)
            // console.log('keterangan' , keterangan)
            console.log('jmlRecord', jmlRecord)
            console.log('kodegdg', kodegdg)
            console.log(qnt1, qnt2)


            // return

            $.ajax({
                url: "{!! url('permintaanpemakaianspadd') !!}",
                type: "post",
                async: false,
                data: {
                    _token: _token,
                    choice: 'I',
                    urut: 0,
                    nobukti,
                    nourut,
                    qnt1,
                    qnt2: qntTerima,
                    kodebrg,
                    kodegdg,
                    namabrg,
                    nosat: satuan,
                    tanggal,
                    jmlRecord,
                    qntx
                },
                success: function(res) {
                    console.log(res)
                    console.log('masuk success')
                    if (res == 1) {
                        console.log(res, '!')

                        refreshTableAdd()
                        reloadData()
                        // Form Add sengaja TIDAK ditutup (cuma tombol Batal yang menutup) —
                        // dikosongkan saja supaya siap untuk item berikutnya. itemAddObj ikut
                        // direset karena validasi membandingkan kodebrg dengan pilihan terakhir.
                        resetTableAddAdd()
                        itemAddObj = {}
                        $('#formAddAdd').show();
                        setTimeout(() => {
                            document.getElementById("AddAddKodeBrg").focus();
                            document.getElementById("AddAddKodeBrg").select();
                        }, 100);
                        alertify.success('Permintaan gudang telah ditambah');
                    }
                    if (res == 2) {
                        setNewNoBukti()
                        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
                    }
                },
                error: function(err) {
                    console.log('masuk error')
                    console.log(err)
                }
            })





        }

        // Baris pemilih barang (whole-row clickable, tanpa kolom Actions) — dipakai oleh
        // buttonAddListBarang() dan onKeyPressBarang(), lihat docs/new-cust-supp-modal-guide.md.
        // DataTables owns the data (deferRender + paging) instead of a hand-built innerHTML,
        // so opening the picker on a large barang list doesn't freeze the page.
        var barangTableDT = null;
        var barangTablePending = null;
        var barangSearchPending = null;
        var barangLookupBusy = false;

        // destroy() TIDAK membersihkan style="width:...px" yang ditulis DataTables
        // ke tiap <th> (lihat _fnDestroy, DataTables 1.10.18 — cuma table.style.width
        // yang dikembalikan). Init berikutnya membaca style itu sebagai sWidthOrig,
        // masuk jalur "user defined width", lalu menempelkan lebar px tetap ke
        // <table> — menimpa .rt-picker-v2 table { width:100% } sehingga kolom
        // menyusut tiap kali modal dibuka ulang. Bersihkan dulu sebelum re-init.
        function resetBarangTableWidths() {
            var $t = $('#tabel_add_list_item');
            $t.css('width', '');
            $t.children('colgroup').remove();
            $t.find('thead th').css('width', '');
        }

        function initBarangTable(list, searchTerm) {
            // DataTables menghitung lebar kolom dari container saat init, dan
            // Bootstrap 4 baru memasang display:block setelah transisi backdrop
            // selesai. Kalau init dipanggil tepat setelah .modal('toggle') (kasus
            // cache hit di buttonAddListBarang), modal masih hidden → lebar diukur
            // di container 0px. Antre saja, biar dieksekusi di shown.bs.modal.
            if (!$('#formAddListItem').is(':visible')) {
                barangTablePending = list;
                barangSearchPending = searchTerm || '';
                return;
            }
            barangTablePending = null;

            if ($.fn.DataTable.isDataTable('#tabel_add_list_item')) {
                $('#tabel_add_list_item').DataTable().clear().destroy();
            }
            resetBarangTableWidths();
            barangTableDT = $('#tabel_add_list_item').DataTable({
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
                columns: [{
                        data: 'KODEBRG'
                    },
                    {
                        data: 'NAMABRG'
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    row.className = 'pick-row';
                    row.setAttribute('onclick', 'buttonAddAddInsertItem(' + dataIndex + ')');
                }
            });

            // Dorong kode/nama yang sudah diketik user ke kotak search DataTables, supaya
            // picker terbuka langsung menyaring — lihat resolveBarang()/openBarangPicker().
            barangTableDT.search(searchTerm || '').draw();
            barangSearchPending = null;
        }

        function fetchBarangList(search, callback) {
            $.ajax({
                url: "{!! url('permintaanpemakaianlistbarang') !!}",
                type: "get",
                async: true,
                data: {
                    search
                },
                success: function(res) {
                    listBarang = res
                    if (callback) callback(res)
                },
                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan saat memuat daftar barang')
                }
            })
        }



        // Buka picker dengan seluruh katalog (barangCacheAll, dicache) dan dorong `term`
        // ke kotak search DataTables-nya. listBarang HARUS ditunjuk ke array yang sama
        // yang dioper ke initBarangTable() — createdRow menempelkan index array itu ke
        // onclick, jadi buttonAddAddInsertItem(index) salah baris kalau keduanya beda.
        function openBarangPicker(term) {
            $("#formAddListItem").modal('show')

            if (barangCacheAll) {
                listBarang = barangCacheAll
                initBarangTable(listBarang, term)
                return
            }

            initBarangTable([], term)

            fetchBarangList('', function(res) {
                barangCacheAll = res
                listBarang = res
                initBarangTable(listBarang, term)
            })
        }

        // Titik masuk tunggal buat Enter dan tombol plus di Kode Barang (Add Item).
        // Kode yang PERSIS cocok langsung mengisi form tanpa membuka modal; selain itu
        // (kode sebagian, nama, atau kosong) modal dibuka dengan `term` sudah terisi di
        // kotak search-nya. Form Edit sengaja tidak memakai ini — Kode Barang di sana disabled.
        function resolveBarang(term) {
            term = (term || '').trim()

            if (!term) {
                openBarangPicker('')
                return
            }

            if (barangLookupBusy) {
                return
            }

            let findExact = function(list) {
                let needle = term.toLowerCase()
                return list.find(b => String(b.KODEBRG || '').trim().toLowerCase() === needle)
            }

            if (barangCacheAll) {
                let hit = findExact(barangCacheAll)
                if (hit) {
                    applyBarangToForm(hit)
                } else {
                    openBarangPicker(term)
                }
                return
            }

            barangLookupBusy = true
            $.ajax({
                url: "{!! url('permintaanpemakaianlistbarang') !!}",
                type: "get",
                async: true,
                data: {
                    search: term
                },
                success: function(res) {
                    barangLookupBusy = false
                    let hit = findExact(res)
                    if (hit) {
                        applyBarangToForm(hit)
                    } else {
                        openBarangPicker(term)
                    }
                },
                error: function(err) {
                    barangLookupBusy = false
                    console.log(err)
                    alertify.warning('Terjadi kesalahan saat memuat daftar barang')
                }
            })
        }

        function onKeyPressBarang(e) {
            if (e.which === 13) {
                resolveBarang($('#AddAddKodeBrg').val())
            }
        }

        function buttonAddListBarang() {
            resolveBarang($('#AddAddKodeBrg').val())
        }

        function buttonBatalAdd() {
            $('.showhide').hide();
        }



        // Dipakai baik oleh klik-baris di picker (buttonAddAddInsertItem) maupun oleh
        // resolveBarang() saat kode yang diketik persis cocok — satu tempat untuk isi
        // Kode/Nama/Satuan form Add Item lalu pindah fokus ke Qty.
        function applyBarangToForm(item) {
            document.getElementById("AddAddKodeBrg").value = item.KODEBRG
            document.getElementById("AddAddNamaBrg").value = item.NAMABRG
            itemAddObj = item

            let rowSelect = ""
            if (item.SAT1) {
                rowSelect += `<option value=1 selected>${item.SAT1}</option>`
            }
            if (item.SAT2) {
                rowSelect += `<option value=2>${item.SAT2}</option>`
            }
            if (item.SAT3) {
                rowSelect += `<option value=3>${item.SAT3}</option>`
            }
            document.getElementById("AddAddSatuan").innerHTML = rowSelect

            setTimeout(() => {
                document.getElementById("AddAddInputQty").focus();
                document.getElementById("AddAddInputQty").select();
            }, 300);
        }

        function buttonAddAddInsertItem(index) {
            closeListItemAdd()
            applyBarangToForm(listBarang[index])
        }


        function setNewNoBukti() {
            let _token = $("#_token").val()
            $.ajax({
                url: "{!! url('spnobukti') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    kode: "PRP"
                },
                success: function(res) {
                    console.log(res)
                    document.getElementById("input_add_nobukti").value = res[0].Nobukti
                    document.getElementById("input_add_noUrut").value = res[0].Nourut
                },

                error: function(err) {
                    console.log(err)
                    alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }
            })
        }

        function buttonKoreksi(nobukti, nourut) {

            // $('#formAddListLokasi').css('display', '')
            $('#formAddListItem').css('display', '')

            $('.showhide').hide();
            console.log(nobukti, nourut)

            $('#input_add_nobukti').css('border', '')
            $('#input_add_nobukti').css('box-shadow', '')
            document.getElementById("input_add_nobukti").value = nobukti
            document.getElementById("input_add_noUrut").value = nourut


            refreshTableAdd()
            let rowSelect =
                `<option value='${listItemForm[0].KodeGdg}' selected>${listItemForm[0].KodeGdg} - ${listItemForm[0].Namagdg}</option>`

            // refreshTableDetailKoreksi
            console.log('tes44')

            document.getElementById("input_add_gudang").innerHTML = rowSelect

            $('#pageForm_title').text('');
            showPage('pageForm')

        }


        function buttonAdd() {
            console.log('buttonAdd')

            let akses = $("#akses_istambah").val();

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }

            $('.showhide').hide();
            console.log('buttonAdd')
            $('#input_add_nobukti').css('border', '')
            $('#input_add_nobukti').css('box-shadow', '')
            // document.getElementById("input_add_keterangan").value = ''

            $.ajax({
                url: "{!! url('permintaanpemakaianlistgudang') !!}",
                type: "get",
                async: false,
                data: {
                    // _token,
                    // nobukti
                },
                success: function(res) {
                    console.log(res)
                    let rowSelect = '<option value=0 selected disabled>-- Pilih Gudang --</option>'

                    res.forEach((item, i) => {

                        rowSelect +=
                            `<option value='${item.KODEGDG}'>${item.KODEGDG} - ${item.NAMA}</option>`
                    });




                    document.getElementById("input_add_gudang").innerHTML = rowSelect

                }
            })




            setNewNoBukti()
            refreshTableAdd()
            console.log('1')

            $('#pageForm_title').text('');
            showPage('pageForm')
        }

        function buttonDetailKoreksi(nobukti) {
            console.log('buttonDetailKoreksi')
            console.log(nobukti)
            refreshTableDetailKoreksi(nobukti)

            showPage('pageDetail')

        }

        function refreshTableDetailKoreksi(nobukti) {
            let _token = $("#_token").val();

            $.ajax({
                url: "{!! url('permintaanpemakaiandetailpenerimaan') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    nobukti
                },
                success: function(res) {
                    console.log(res)

                    let tableRow = ''
                    document.getElementById("input_detail_koreksi_nobukti").value = res[0].NOBUKTI
                    // document.getElementById("input_detail_koreksi_keterangan").value = res[0].KETERANGAN
                    let date = new Date(res[0].TANGGAL);
                    let day = ("0" + date.getDate()).slice(-2);
                    let month = ("0" + (date.getMonth() + 1)).slice(-2);
                    date1 = date.getFullYear() + "-" + (month) + "-" + (day);
                    console.log(date1)
                    // console.log($("#input_detail_koreksi_tanggal").val())
                    $('#input_detail_koreksi_tanggal').val(date1)
                    res.forEach((item, i) => {
                        tableRow += `
        <tr class="data-row">
        <td>${item.KODEBRG}</td>
        <td>${item.NAMABRG}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Qntx).toFixed(2))}</td>
        <td class="text-center">${item.SATUAN}</td>
        </tr>
        `
                    });
                    console.log(res.length)
                    if (!res.length) {
                        tableRow = `<tr class="empty-row">
        <td colspan="4">Data tidak ditemukan</td>
        </tr>`
                    }


                    document.getElementById("detailKoreksiTableData").innerHTML = tableRow
                }
            })
        }

        function buttonEdit(index) {
            console.log('buttonEdit')

            let akses = $("#akses_iskoreksi").val();

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }

            $('.showhide').hide();
            console.log(index)
            itemEditObj = listItemForm[index]
            console.log(itemEditObj)

            document.getElementById("AddEditKodeBrg").value = itemEditObj.KODEBRG
            document.getElementById("AddEditNamaBrg").value = itemEditObj.NAMABRG
            // document.getElementById("AddEditLokasiAsal").value = itemEditObj.KodeLokasi
            // document.getElementById("AddEditLokasiTujuan").value = itemEditObj.KodeLokTujuan
            document.getElementById("AddEditInputQty").value = formatAngka(parseFloat(itemEditObj.Qntx).toFixed(2))
            let rowSelect = '<option value=0 selected disabled>-- Pilih Satuan --</option>'

            if (itemEditObj.SAT1) {
                rowSelect += `<option value=1>Sat 1 - ${itemEditObj.SAT1}</option>`
            }
            if (itemEditObj.SAT2) {
                rowSelect += `<option value=2>Sat 2 - ${itemEditObj.SAT2}</option>`
            }
            if (itemEditObj.SAT3) {
                rowSelect += `<option value=3>Sat 3 - ${itemEditObj.SAT3}</option>`
            }
            document.getElementById("AddEditSatuan").innerHTML = rowSelect
            document.getElementById("AddEditSatuan").value = itemEditObj.NOSAT


            $('#formAddEdit').show();

        }

        // Dipanggil setelah submitAddEdit() sukses: form Edit tetap terbuka, jadi objek
        // acuannya harus ditarik ulang dari listItemForm yang baru saja di-refresh
        // (dicocokkan NOBUKTI + URUT, bukan index — urutan array bisa berubah).
        // Kalau barisnya sudah tidak ada, form ditutup supaya tidak menyimpan ke baris hantu.
        function syncFormEditFromList() {
            let row = listItemForm.find(function(item) {
                return String(item.NOBUKTI) === String(itemEditObj.NOBUKTI) &&
                    Number(item.URUT) === Number(itemEditObj.URUT);
            });

            if (!row) {
                $('#formAddEdit').hide();
                return;
            }

            itemEditObj = row;
            document.getElementById("AddEditKodeBrg").value = row.KODEBRG
            document.getElementById("AddEditNamaBrg").value = row.NAMABRG
            document.getElementById("AddEditInputQty").value = formatAngka(parseFloat(row.Qntx).toFixed(2))
            document.getElementById("AddEditSatuan").value = row.NOSAT
            $('#formAddEdit').show();
        }

        function refreshTableAdd(action = 'I') {
            console.log('refreshTableAdd', action)
            // $('#formAddListLokasi').css('display', '')
            // $('#formAddListItem').css('display', '')
            listItemForm = []
            console.log(action)
            let nobukti = $('#input_add_nobukti').val();
            let _token = $("#_token").val();
            console.log('nobukti', nobukti)
            console.log('token', _token)

            $.ajax({
                url: "{!! url('permintaanpemakaiandetailpenerimaan') !!}",
                type: "post",
                async: false,
                data: {
                    _token,
                    nobukti
                },
                success: function(res) {
                    console.log(res)
                    listItemForm = res
                }
            })
            console.log(listItemForm)

            // addTableData
            let tableRow = ''
            listItemForm.forEach((item, i) => {
                tableRow += `
      <tr class="data-row">
      <td>${item.KODEBRG}</td>
      <td>${item.NAMABRG}</td>
      <td class="text-right">${formatAngka(parseFloat(item.Qntx).toFixed(2))}</td>
      <td class="text-center">${item.SATUAN}</td>
      <td class="text-center">
        <button class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit(${i})" ><i class="bi bi-pen"></i></button>
        <button class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.NOBUKTI}', ${item.URUT})" ><i class="bi bi-trash"></i></button>

      </td>
      </tr>
      `
            });
            console.log(listItemForm.length)
            // if (!listItemForm.length && action == 'D') {
            //   alertify.warning('Data item habis')
            // }
            if (!listItemForm.length) {
                tableRow = `<tr class="empty-row">
      <td colspan="5">Belum ada data</td>
      </tr>`
                console.log('masok !')
                $('#input_add_tanggal').prop('disabled', false)
                // $('#input_add_keterangan').prop('disabled', false)
                $('#input_add_gudang').prop('disabled', false)
            }
            if (listItemForm.length && listItemForm[0].TANGGAL) {
                let date = new Date(listItemForm[0].TANGGAL);
                let day = ("0" + date.getDate()).slice(-2);
                let month = ("0" + (date.getMonth() + 1)).slice(-2);
                date1 = date.getFullYear() + "-" + (month) + "-" + (day);
                console.log(date1)
                $('#input_add_tanggal').val(date1)
                // document.getElementById("input_add_keterangan").value = listItemForm[0].KETERANGAN
                $('#input_add_tanggal').prop('disabled', true)
                // $('#input_add_keterangan').prop('disabled', true)
                $('#input_add_gudang').prop('disabled', true)

            }

            document.getElementById("addTableData").innerHTML = tableRow
            return listItemForm.length
        }

        function buttonDelete(nobukti, urut) {
            console.log('buttonDelete')
            console.log(nobukti, urut)

            let akses = $("#akses_ishapus").val();
            console.log(akses)

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }
            let _token = $("#_token").val();

            alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + ' ?',
                function() {
                    $.ajax({
                        url: "{!! url('permintaanpemakaianspadd') !!}",
                        type: "post",
                        async: false,
                        data: {
                            _token: _token,
                            choice: 'D',
                            urut: urut,
                            nobukti: nobukti,
                            nourut: '',
                            qnt1: 0,
                            qnt2: 0,
                            qntx: 0,
                            kodebrg: '',
                            namabrg: '',
                            kodegdg: '',
                            nosat: 0,
                            tanggal: '',
                            jmlRecord: 0
                        },
                        success: function(res) {
                            console.log(res)
                            console.log('masuk success')
                            if (res == 1) {
                                let sisaItem = refreshTableAdd('D')
                                console.log(res, '!')

                                refreshTableAdd('D')
                                reloadData()
                                // Form Edit sekarang bisa tetap terbuka saat item dihapus —
                                // sinkronkan supaya ia menutup sendiri kalau baris acuannya hilang.
                                if ($('#formAddEdit').is(':visible')) {
                                    syncFormEditFromList()
                                }
                                alertify.success('Permintaan gudang telah didelete');
                                if (sisaItem === 0) {
                                    buttonCloseForm()
                                }
                            }
                        },
                        error: function(err) {
                            console.log('masuk error')
                            console.log(err)
                        }
                    })


                },
                function() {
                    console.log('no')
                });
        }

        function buttonAddAdd() {
            let akses = $("#akses_istambah").val();

            if (!Number(akses)) {
                alertify.warning('No access')
                return
            }
            $('.showhide').hide();

            resetTableAddAdd()
            // document.getElementById("AddAddInputQty").value = '-1.00'
            $('#formAddAdd').show();

        }


        function generateInputNumber(id, style, classes, onchange) {
            return `<input type="text" id="${id}" onchange="${onchange}" style="${style}" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number ${classes}">`
        }

        function formatAngkaX(angka) {
            if (!angka) {
                return '0.00'
            } else {
                return formatAngka(parseFloat(angka).toFixed(2))
            }

        }

        function formatAngkaParse(angka) {

            return parseFloat(angka).toFixed(2)
        }

        function formatAngkaVal(angka) {
            return Number(angka.split(',').join(''))
        }


        function formatAngka(angkaString) {
            // console.log('formatAngka' , angkaString);
            let tempAngka = angkaString.split('.')

            if (tempAngka[0][0] == '-') {
                let temp2 = ''

                let tempAngka1 = tempAngka[0].split('-')
                for (let i = 0; i < tempAngka1[1].length; i++) {
                    if (i != 0 && i % 3 == 0) {
                        temp2 = ',' + temp2
                    }
                    temp2 = tempAngka1[1][tempAngka1[1].length - i - 1] + temp2
                    // console.log(i, temp2)
                }
                temp2 += '.' + tempAngka[1]
                temp2 = '-' + temp2

                return temp2
            }
            let temp1 = ''
            for (let i = 0; i < tempAngka[0].length; i++) {
                if (i != 0 && i % 3 == 0) {
                    temp1 = ',' + temp1
                }
                temp1 = tempAngka[0][tempAngka[0].length - i - 1] + temp1
                // console.log(i, temp1)
            }
            temp1 += '.' + tempAngka[1]
            return temp1
        }

        function resetTableAddAdd() {
            document.getElementById("AddAddKodeBrg").value = ''
            document.getElementById("AddAddNamaBrg").value = ''
            // document.getElementById("AddAddLokasiAsal").value = ''
            // document.getElementById("AddAddLokasiTujuan").value = ''
            document.getElementById("AddAddInputQty").value = '0.00'
            let rowSelect = '<option value=0 selected disabled>-- Pilih Satuan --</option>'
            document.getElementById("AddAddSatuan").innerHTML = rowSelect
        }
    </script>
@endsection
