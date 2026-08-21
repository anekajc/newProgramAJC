@extends('report.masterreport2')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Warna centang -->
<style>
    .checkmark-red {
        color: red !important;
        font-weight: bold;
        margin-left: 6px;
    }

    #inputPPN {
        border: 0;
        background: none;
        padding: 0;
        box-shadow: none;
        color: #495057;
        font-weight: 600;
    }

    #inputPPN:hover,
    #inputPPN:focus {
        color: #0d6efd;
        box-shadow: none;
    }

    #inputPjasa {
        border: 0;
        background: none;
        padding: 0;
        box-shadow: none;
        color: #495057;
        font-weight: 600;
    }

    #inputPjasa:hover,
    #inputPjasa:focus {
        color: #0d6efd;
        box-shadow: none;
    }

    #inputTipebayar {
        border: 0;
        background: none;
        padding: 0;
        box-shadow: none;
        color: #495057;
        font-weight: 600;
    }

    #inputTipebayar:hover,
    #inputTipebayar:focus {
        color: #0d6efd;
        box-shadow: none;
    }

    #inputOtorisasi {
        border: 0;
        background: none;
        padding: 0;
        box-shadow: none;
        color: #495057;
        font-weight: 600;
    }

    #inputOtorisasi:hover,
    #inputOtorisasi:focus {
        color: #0d6efd;
        box-shadow: none;
    }

    #inputOrder {
        border: 0;
        background: none;
        padding: 0;
        box-shadow: none;
        color: #495057;
        font-weight: 600;
    }

    #inputOrder:hover,
    #inputOrder:focus {
        color: #0d6efd;
        box-shadow: none;
    }

    #inputReportMode {
        border: 0;
        background: none;
        padding: 0;
        box-shadow: none;
        color: #495057;
        font-weight: 600;
    }

    #inputReportMode:hover,
    #inputReportMode:focus {
        color: #0d6efd;
        box-shadow: none;
    }

    .tb-report .kpi-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width: 900px) {
        .tb-report .kpi-strip {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 560px) {
        .tb-report .kpi-strip {
            grid-template-columns: 1fr;
        }
    }

    .tb-report .kpi-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px 28px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        display: flex;
        align-items: flex-start;
        gap: 18px;
    }

    .tb-report .kpi-ic {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 28px;
    }

    .tb-report .kpi-label {
        font-size: 15px;
        color: #64748b;
        margin-bottom: 4px;
    }

    .tb-report .kpi-val {
        font-size: 29px;
        font-weight: 700;
        color: #1e293b;
    }

    .tb-report .chart-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width:900px) {
        .tb-report .chart-grid {
            grid-template-columns: 1fr;
        }
    }

    .tb-report .chart-box {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
    }

    .tb-report .chart-box h3 {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 12px;
    }

    .tb-report .chart-holder {
        position: relative;
        height: 260px;
    }

    .tb-report .chart-holder canvas {
        max-height: 260px;
    }
</style>
<!-- Warna centang -->

@section('header2')
    <div class="tb-report main">
        <div class="content">
            <!-- TOOLBAR -->
            <div class="toolbar">
                <div>
                    <div class="page-title">Register Pembelian</div>
                    <!-- <div class="page-sub">Dicetak oleh: {{ $akses['user'] }} &nbsp;&middot;&nbsp; <span id="printTime"></span></div> -->
                </div>

                <!-- Periode (date range) -->
                <div class="filter-wrap">
                    <label>Periode</label>
                    <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
                    <span class="filter-sep">s/d</span>
                    <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
                </div>

                <!-- mode report -->
                <!-- <div class="filter-wrap">
                  <button
                        class="btn btn-outline-primary dropdown-toggle"
                        type="button"
                        id="inputReportMode"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Report
                    </button>
                    <ul class="dropdown-menu" id="dropdownReportMode" aria-labelledby="inputReportMode">
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setReportMode('0')">Detail
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setReportMode('1')">Rekap
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                    </ul>
                  </div> -->

                <!-- ppn -->
                <!-- <div class="filter-wrap">
                  <button
                        class="btn btn-outline-primary dropdown-toggle"
                        type="button"
                        id="inputPPN"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        PPN
                    </button>
                    <ul class="dropdown-menu" id="dropdownPPN" aria-labelledby="inputPPN">
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setPPN('2')">Semua
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setPPN('1')">Non PPN
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setPPN('0')">PPN
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                    </ul>
                  </div> -->

                <!-- pjasa -->
                <!-- <div class="filter-wrap">
                  <button
                        class="btn btn-outline-primary dropdown-toggle"
                        type="button"
                        id="inputPjasa"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Jasa
                    </button>
                    <ul class="dropdown-menu" id="dropdownPjasa" aria-labelledby="inputPjasa">
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setPjasa('2')">Semua
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setPjasa('1')">PBJ
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setPjasa('0')">LPB
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                    </ul>
                  </div> -->

                <!-- tipebayar -->
                <!-- <div class="filter-wrap">
                  <button
                        class="btn btn-outline-primary dropdown-toggle"
                        type="button"
                        id="inputTipebayar"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Tipe
                    </button>
                    <ul class="dropdown-menu" id="dropdownTipebayar" aria-labelledby="inputTipebayar">
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setTipebayar('2')">Semua
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setTipebayar('1')">Kredit
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setTipebayar('0')">Tunai
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                    </ul>
                  </div> -->

                <!-- otorisasi -->
                <!-- <div class="filter-wrap">
                  <button
                        class="btn btn-outline-primary dropdown-toggle"
                        type="button"
                        id="inputOtorisasi"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Otorisasi
                    </button>
                    <ul class="dropdown-menu" id="dropdownOtorisasi" aria-labelledby="inputOtorisasi">
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setOtorisasi('2')">Semua
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setOtorisasi('1')">Belum Otorisasi
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                      <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setOtorisasi('0')">Sudah Otorisasi
                      <span class="checkmark-red" style="display:none;">&#10003</span>
                      </a></li>
                    </ul>
                  </div> -->

                <!-- order by -->
                <!-- <div class="filter-wrap">
                    <button
                        class="btn btn-outline-primary dropdown-toggle"
                        type="button"
                        id="inputOrder"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Order By
                    </button>
                    <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputOrder">
                        <li><a class="dropdown-item" data-value="N" onclick="setOrderBy('N')">No Bukti
                        <span class="checkmark-red" style="display:none;">&#10003</span>
                        </a></li>
                        <li><a class="dropdown-item" data-value="B" onclick="setOrderBy('B')">Barang
                        <span class="checkmark-red" style="display:none;">&#10003</span>
                        </a></li>
                        <li><a class="dropdown-item" data-value="S" onclick="setOrderBy('S')">Supplier
                        <span class="checkmark-red" style="display:none;">&#10003</span>
                        </a></li>
                    </ul>
                  </div> -->

                <!-- Actions: second (row-level) search + load + export -->
                <div class="action-group">
                    <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..."
                        oninput="applyFilters()" style="width:180px">
                    <!-- <button class="btn-load" onclick="doShowFormFilterData()" title="Filter Data"><i class="bi bi-filter-left"></i> Filter Data</button> -->
                    {{-- Dibuka lewat plugin jQuery (Bootstrap 4), BUKAN data-bs-toggle (Bootstrap 5).
                         Halaman ini memuat dua Bootstrap; jQuery dimuat SESUDAH bundle BS5, jadi
                         $.fn.modal dipegang BS4. applyModalFilter() menutup modal ini dengan
                         $('#modalFilter').modal('hide'), jadi pembukanya harus API yang sama. --}}
                    <button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button class="btn-load" onclick="doShowFormCustomizeTable()" title="Customize Table"><i
                            class="fas fa-cog"></i> Customize Table</button>
                    <button class="btn-load" onclick="makeTable('REPORT')" title="Tampilkan laporan"><i
                            class="fas fa-check"></i> Tampilkan</button>
                    <div class="export-wrap" id="exportWrap">
                        <button class="export-btn" onclick="toggleExport()"><i class="bi bi-arrow-down"></i> Export <i
                                class="bi bi-caret-down-fill"></i></button>
                        <div class="export-drop" id="exportDrop">
                            <div class="export-opt" onclick="doExport('Excel')"><i
                                    class="bi bi-journals text-success"></i> Ekspor ke <span class="ext">XLSX</span>
                            </div>
                            <div class="export-opt" onclick="doExport('CSV')"><i class="bi bi-clipboard"></i> Ekspor ke
                                <span class="ext">CSV</span>
                            </div>
                            <div class="export-opt" onclick="doExport('Print')"><i
                                    class="bi bi-printer-fill text-warning"></i> Cetak Laporan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI CARDS -->
            <div class="kpi-strip" id="kpiStrip"></div>

            <!-- CHARTS (dibangun sisi-klien dari data yang dimuat) -->
            <div class="chart-grid" id="chartGrid">
                <div class="chart-box">
                    <h3>Pembelian Per Supplier (Top 6)</h3>
                    <div class="chart-holder"><canvas id="topCustomerChart"></canvas></div>
                </div>
                <div class="chart-box">
                    <h3>Pembelian Vs Penjualan</h3>
                    <div class="chart-holder"><canvas id="agingChart"></canvas></div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-outer">
                <div class="table-wrap">
                    <table class="tb" id="mainTable">
                        <thead>
                            <tr>
                                <th style="min-width:130px">No. Bukti</th>
                                <th style="min-width:90px">Tanggal</th>
                                <th style="min-width:130px">No PO</th>
                                <th style="min-width:130px">Nama Supplier</th>
                                <th style="min-width:130px">Nama Barang</th>
                                <th class="num" style="min-width:10px">Satuan</th>
                                <th class="num" style="min-width:10px">Qnt</th>
                                <th class="num" style="min-width:10px">Harga</th>
                                <th class="num" style="min-width:10px">Disc</th>
                                <th class="num" style="min-width:10px">DPP IDR</th>
                                <th class="num" style="min-width:10px">PNN IDR</th>
                                <th class="num" style="min-width:10px">Total IDR</th>
                                <th class="num" style="min-width:10px">Otorisasi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr class="empty-row">
                                <td colspan="13">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <span id="footerLabel">Belum ada data dimuat</span>
                </div>
            </div>
        </div><!-- /content -->

        <!-- TOAST -->
        <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
    </div><!-- /tb-report -->

    <!-- modal filter -->
    <div class="modal fade" id="modalFilter">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-filter"></i>
                        Filter Laporan
                    </h5>

                    <button type="button" class="btn-close" aria-label="Close"
                        data-dismiss="modal" data-bs-dismiss="modal"
                        onclick="$('#modalFilter').modal('hide')">
                    </button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Report</label>
                        <select class="form-select" id="modalReport">
                            <option value="0">Detail</option>
                            <option value="1">Rekap</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>PPN</label>
                        <select class="form-select" id="modalPPN">
                            <option value="2">Semua</option>
                            <option value="1">Non PPN</option>
                            <option value="0">PPN</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Jasa</label>
                        <select class="form-select" id="modalPjasa">
                            <option value="2">Semua</option>
                            <option value="1">PBJ</option>
                            <option value="0">LPB</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Tipe</label>
                        <select class="form-select" id="modalTipe">
                            <option value="2">Semua</option>
                            <option value="1">Kredit</option>
                            <option value="0">Tunai</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Otorisasi</label>
                        <select class="form-select" id="modalOtorisasi">
                            <option value="2">Semua</option>
                            <option value="1">Belum Otorisasi</option>
                            <option value="0">Sudah Otorisasi</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-primary" onclick="applyModalFilter()">
                        Terapkan
                    </button>

                </div>

            </div>
        </div>
    </div>
    <!-- modal filter -->
@endsection


@section('jsreport')
    <script type="text/javascript">
        let DetOrRekap = 0;
        let globalDate1 = "{!! date('Y-m-d') !!}";
        let globalDate2 = "{!! date('Y-m-d') !!}";
        let globalOtorisasi = "2"; // default: Semua
        let globalOrderBy = "N"; // default: Nomor Bukti
        let globalPPN = "2"; // default: Semua
        let globalPjasa = "2"; // default: Semua
        let globalTipebayar = "2"; // default: Semua
        let globalReportMode = "0"; // default: Detail
        let lastRows = []; // hasil fetch terakhir (dipakai renderRows / export / search)
        let currentGroupby = 'NoBukti'; // groupby aktif untuk render ulang saat search

        $(document).ready(function() {
            setOtorisasi(globalOtorisasi);
            setPPN(globalPPN);
            setPjasa(globalPjasa);
            setTipebayar(globalTipebayar);
            setOrderBy(globalOrderBy);
            setReportMode(globalReportMode);
        });

        $('#modalFilter').on('show.bs.modal', function() {
            $("#modalReport").val(globalReportMode);
            $("#modalPPN").val(globalPPN);
            $("#modalPjasa").val(globalPjasa);
            $("#modalTipe").val(globalTipebayar);
            $("#modalOtorisasi").val(globalOtorisasi);
        });

        function applyModalFilter() {

            setReportMode($("#modalReport").val());

            setPPN($("#modalPPN").val());

            setPjasa($("#modalPjasa").val());

            setTipebayar($("#modalTipe").val());

            setOtorisasi($("#modalOtorisasi").val());

            $('#modalFilter').modal('hide');
        }
        // $(document).ready(function() {
        //   $("#btnFilterData").on("click", function() {
        //     if (typeof doShowFormFilterData === "function") doShowFormFilterData();
        //     else alert(" Fungsi doShowFormFilterData belum tersedia.");
        //   });

        //   $("#btnCustomizeTable").on("click", function() {
        //     if (typeof doShowFormCustomizeTable === "function") doShowFormCustomizeTable();
        //     else alert(" Fungsi doShowFormCustomizeTable belum tersedia.");
        //   });

        //   $("#btnSubmitReport").on("click", function() {
        //     makeTable('REPORT');
        //   });

        //   setReportMode(globalReportMode);
        //   setOtorisasi(globalOtorisasi);
        //   setOrderBy(globalOrderBy);
        //   setPPN(globalPPN);
        //   setPjasa(globalPjasa);
        //   setTipebayar(globalTipebayar);
        //   showPeriode();

        //   setDefaultHeader();

        //   setTimeout(() => {
        //     makeTable('REPORT');
        //   }, 100);
        // });

        // periode
        function showPeriode() {
            globalDate1 = $('#inputDate1').val();
            globalDate2 = $('#inputDate2').val();
            // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
        }

        // otorisasi
        function setOtorisasi(val) {
            globalOtorisasi = val;

            // // sembunyikan semua centang
            // $('#dropdownOtorisasi .checkmark-red').hide();

            // // tampilkan centang yang dipilih
            // $(`#dropdownOtorisasi .dropdown-item[data-value='${val}'] .checkmark-red`).show();

            // // ubah tulisan tombol
            // const text = {
            //     "2": "Semua",
            //     "1": "Belum Otorisasi",
            //     "0": "Sudah Otorisasi",
            // };

            // $("#inputOtorisasi").html(
            //     `Otorisasi : ${text[val]}`
            // );
        }

        //ppn
        function setPPN(val) {
            globalPPN = val;

            // // sembunyikan semua centang
            // $('#dropdownPPN .checkmark-red').hide();

            // // tampilkan centang yang dipilih
            // $(`#dropdownPPN .dropdown-item[data-value='${val}'] .checkmark-red`).show();

            // // ubah tulisan tombol
            // const text = {
            //     "2": "Semua",
            //     "1": "Non PPN",
            //     "0": "PPN"
            // };

            // $("#inputPPN").html(
            //     `PPN : ${text[val]}`
            // );
        }

        // pjasa
        function setPjasa(val) {
            globalPjasa = val;

            // // sembunyikan semua centang
            // $('#dropdownPjasa .checkmark-red').hide();

            // // tampilkan centang yang dipilih
            // $(`#dropdownPjasa .dropdown-item[data-value='${val}'] .checkmark-red`).show();

            // // ubah tulisan tombol
            // const text = {
            //     "2": "Semua",
            //     "1": "PBJ",
            //     "0": "LPB"
            // };

            // $("#inputPjasa").html(
            //     `Jasa : ${text[val]}`
            // );
        }

        //tipe bayar
        function setTipebayar(val) {
            globalTipebayar = val;

            // // sembunyikan semua centang
            // $('#dropdownTipebayar .checkmark-red').hide();

            // // tampilkan centang yang dipilih
            // $(`#dropdownTipebayar .dropdown-item[data-value='${val}'] .checkmark-red`).show();

            // // ubah tulisan tombol
            // const text = {
            //     "2": "Semua",
            //     "1": "Kredit",
            //     "0": "Tunai"
            // };

            // $("#inputTipebayar").html(
            //     `Tipe : ${text[val]}`
            // );
        }

        /* -- EXPORT -- */
        function toggleExport() {
            document.getElementById('exportDrop').classList.toggle('open');
        }
        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('exportWrap');
            if (wrap && !wrap.contains(e.target)) {
                document.getElementById('exportDrop').classList.remove('open');
            }
        });

        // order by
        function setOrderBy(val) {
            globalOrderBy = val;

            // // sembunyikan semua centang
            // $('#dropdownOrder .checkmark-red').hide();

            // // tampilkan centang yang dipilih
            // $(`#dropdownOrder .dropdown-item[data-value='${val}'] .checkmark-red`).show();
        }

        function setReportMode(val) {
            globalReportMode = val;
            DetOrRekap = Number(val);

            // $('#dropdownReportMode .checkmark-red').hide();
            // $(`#dropdownReportMode .dropdown-item[data-value='${val}'] .checkmark-red`).show();

            // // Ubah tulisan tombol
            // const text = {
            //     "0": "Detail",
            //     "1": "Rekap"
            // };

            // $("#inputReportMode").html(
            //     `Report : ${text[val]}`
            // );
        }


        // // periode
        // function showPeriode() {
        //   globalDate1 = $('#inputDate1').val();
        //   globalDate2 = $('#inputDate2').val();
        //   // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
        // }

        // // otorisasi
        // function setOtorisasi(val) {
        //   globalOtorisasi = val;
        //   let text = (val == '0') ? 'Semua' : (val == '1') ? 'Otorisasi' : 'Non Otorisasi';
        //   // alertify.success(`Otorisasi: ${text}`);

        //   // hapus semua centang
        //   $('#dropdownOtorisasi .dropdown-item').each(function() {
        //     let itemText = $(this).text().replace(' ?', '').trim();
        //     $(this).text(itemText);
        //   });

        //   // tambah centang di item yg di pilih
        //   $(`#dropdownOtorisasi .dropdown-item[data-value='${val}']`).each(function() {
        //     $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
        //   });
        // }

        // // mode report
        // function setReportMode(val) {
        //   globalReportMode = val;
        //   jenisreport = Number(val);   // 0 = Detail, 1 = Rekap
        //   DetOrRekap = Number(val);    // samakan dengan variabel yang ada di setModeReport

        //   // hapus centang dulu
        //   $('#dropdownReportMode .dropdown-item').each(function() {
        //     let itemText = $(this).text().replace(' ?', '').trim();
        //     $(this).text(itemText);
        //   });

        //   // tambah centang di item terpilih
        //   $(`#dropdownReportMode .dropdown-item[data-value='${val}']`).each(function() {
        //     $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
        //   });

        //   // update g_modeReport sesuai pilihan order & detail/rekap
        //   // setModeReport() sudah mengatur g_modeReport berdasarkan $("#inputOrder").val() dan jenisreport/DetOrRekap
        //   setModeReport();
        // }

        // // set ppn
        // function setPPN(val) {
        //   globalPPN = val;
        //   let text = (val == '0') ? 'PPN' : (val == '1') ? 'Non PPN' : 'Semua';
        //   // alertify.success(`PPN: ${text}`);

        //   // hapus semua centang
        //   $('#dropdownPPN .dropdown-item').each(function() {
        //     let itemText = $(this).text().replace(' ?', '').trim();
        //     $(this).text(itemText);
        //   });

        //   // tambah centang di item yg dipilih
        //   $(`#dropdownPPN .dropdown-item[data-value='${val}']`).each(function() {
        //     $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
        //   });
        // }

        // // set pjasa
        // function setPjasa(val) {
        //   globalPjasa = val;
        //   let text = (val == '0') ? 'LPB' : (val == '1') ? 'PBJ' : 'Semua';
        //   // alertify.success(`P. Jasa: ${text}`);

        //   // hapus semua centang
        //   $('#dropdownPjasa .dropdown-item').each(function() {
        //     let itemText = $(this).text().replace(' ?', '').trim();
        //     $(this).text(itemText);
        //   });

        //   // tambah centang di item yg dipilih
        //   $(`#dropdownPjasa .dropdown-item[data-value='${val}']`).each(function() {
        //     $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
        //   });
        // }

        // // tipe bayar
        // function setTipebayar(val) {
        //   globalTipebayar = val;
        //   let text = (val === '2') ? 'Semua' : (val === '1') ? 'Kredit' : 'Tunai';
        //   // alertify.success(`Tipe Bayar: ${text}`);

        //   $('#dropdownTipebayar .dropdown-item').each(function() {
        //     let itemText = $(this).text().replace(' ?', '').trim();
        //     $(this).text(itemText);
        //   });

        //   $(`#dropdownTipebayar .dropdown-item[data-value='${val}']`).each(function() {
        //     $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
        //   });
        // }

        // // order by
        // function setOrderBy(val) {
        //   globalOrderBy = val;
        //   let text = (val == 'N') ? 'Nomor Bukti' : (val == 'B') ? 'Barang' : 'Supplier';
        //   // alertify.success(`Order By: ${text}`);

        //   // hapus semua centang
        //   $('#dropdownOrder .dropdown-item').each(function() {
        //     let itemText = $(this).text().replace(' ?', '').trim();
        //     $(this).text(itemText);
        //   });

        //   // tambah centang di item yg dipilih
        //   $(`#dropdownOrder .dropdown-item[data-value='${val}']`).each(function() {
        //     $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
        //   });
        // }

        // var DetOrRekap = 0;
        var modereport_detailnobukti = 0,
            modereport_detailbarang = 1,
            modereport_detailcustomer = 2;
        var modereport_rekapnobukti = 3,
            modereport_rekapbarang = 4,
            modereport_rekapcustomer = 5;
        g_modeReport = modereport_detailnobukti;
        var jenisreport = 0; // ini untuk detail dan rekap

        function setDefaultHeader() {
            if (g_modeReport == modereport_detailnobukti) {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['NoPO', 'No PO', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['satbeli', 'Satuan', 1, 'varchar', 0, 0],
                    ['qntbeli', 'Qnt', 1, 'float', 0, 2],
                    ['Harga', 'HARGA', 1, 'float', 0, 2],
                    ['DiscP', 'Discount', 1, 'float', 0, 2],
                    ['NDPPRp', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRp', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else if (g_modeReport == modereport_detailbarang) {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['satbeli', 'Satuan', 1, 'varchar', 0, 0],
                    ['qntbeli', 'Qnt', 1, 'float', 0, 2],
                    ['Harga', 'HARGA', 1, 'float', 0, 2],
                    ['KODEVLS', '$', 1, 'varchar', 0, 0],
                    ['DiscP', 'Discount', 1, 'float', 0, 2],
                    ['NDPP', 'DPP $', 1, 'float', 0, 0],
                    ['KURS', 'Kurs', 1, 'varchar', 0, 0],
                    ['NDPPRp', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRp', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else if (g_modeReport == modereport_detailcustomer) {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['satbeli', 'Satuan', 1, 'varchar', 0, 0],
                    ['qntbeli', 'Qnt', 1, 'float', 0, 2],
                    ['Harga', 'HARGA', 1, 'float', 0, 2],
                    ['KODEVLS', '$', 1, 'varchar', 0, 0],
                    ['DiscP', 'Discount', 1, 'float', 0, 2],
                    ['NDPP', 'DPP $', 1, 'float', 0, 0],
                    ['KURS', 'Kurs', 1, 'varchar', 0, 0],
                    ['NDPPRp', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRp', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else if (g_modeReport == modereport_rekapnobukti) {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['NoPO', 'No PO', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['Harga', 'HARGA', 1, 'float', 0, 2],
                    ['DiscP', 'Discount', 1, 'float', 0, 2],
                    ['NDPPRp', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRp', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]

                    // ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    // ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    // ['NAMACUSTSUPP', 'Supplier', 1, 'varchar', 0, 0],
                    // ['NDPPRP', 'DPP', 1, 'float', 1, 2],
                    // ['nuangmuka', 'Uang Muka', 1, 'float', 1, 2],
                    // ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    // ['NNETRP', 'Total', 1, 'float', 1, 2]

                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else if (g_modeReport == modereport_rekapbarang) {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
                    ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['NDPP', 'DPP VLS', 1, 'float', 1, 2],
                    ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
                    ['KURS', 'Kurs', 1, 'varchar', 0, 0],
                    ['NDPPRP', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRP', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['NAMACUSTSUPP', 'Supplier', 1, 'varchar', 0, 0],
                    ['NDPP', 'DPP VLS', 1, 'float', 0, 2],
                    ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
                    ['KURS', 'Kurs', 1, 'varchar', 0, 0],
                    ['NDPPRP', 'DPP', 1, 'float', 1, 2],
                    ['NPPNRp', 'PPN', 1, 'float', 1, 2],
                    ['NNETRP', 'Total', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;
            }
        }

        const reportUrl = "{{ url('laporanregisterpembelian_doReport') }}"
        const grafikUrl = "{{ url('laporanregisterpembelian_doGrafik') }}";

        function makeTable(_mode) {
            console.log(" makeTable jalankan mode:", _mode);

            let groupby = '';
            let _date1 = $("#inputDate1").val();
            let _date2 = $("#inputDate2").val();

            let input_oto = globalOtorisasi;
            let input_order = globalOrderBy;
            let input_ppn = globalPPN;
            let input_pjasa = globalPjasa;
            let input_tipebayar = globalTipebayar;

            // mode report
            if (input_order == "N") {
                if (DetOrRekap === 0) {
                    g_modeReport = modereport_detailnobukti;
                    groupby = 'NoBukti';
                } else {
                    g_modeReport = modereport_rekapnobukti;
                    groupby = 'NAMABRG';
                }
            } else if (input_order == "B") {
                if (DetOrRekap === 0) {
                    g_modeReport = modereport_detailbarang;
                    groupby = 'NamaBrg';
                } else {
                    g_modeReport = modereport_rekapbarang;
                    groupby = 'NAMABRG';
                }
            } else {
                if (DetOrRekap === 0) {
                    g_modeReport = modereport_detailcustomer;
                    groupby = 'NamaBrg';
                } else {
                    g_modeReport = modereport_rekapcustomer;
                    groupby = 'NAMACUSTSUPP';
                }
            }

            console.log("Mode report aktif:", g_modeReport, "| Group By:", groupby);

            if (typeof doSetHeader === 'function') {
                doSetHeader(g_modeReport);
            }

            let data = {
                date1: _date1,
                date2: _date2,
                inputOto: globalOtorisasi,
                inputOrd: input_order,
                inputPPN: globalPPN,
                inputPjasa: globalPjasa,
                inputTipebayar: globalTipebayar,
                inputDetOrRekap: DetOrRekap,
            };

            // Ambil data SEKALI, lalu render langsung ke tabel styled baru (#tableBody).
            $.ajax({
                url: reportUrl,
                type: 'get',
                data: data,
                success: function(res) {
                    lastRows = res || [];
                    currentGroupby = groupby; // simpan utk render ulang saat search
                    $('#searchBox2').val(''); // reset kotak cari tiap muat data baru
                    renderRows(lastRows, groupby); // <-- render ke .tb-report #tableBody
                    renderKpi(lastRows);
                    buildCharts(lastRows);
                    loadLineChart(_date1);
                },
                error: function() {
                    lastRows = [];
                    currentGroupby = groupby;
                    renderRows([], groupby);
                }
            });

            // console.log("Data terkirim ke server:", data);

            // doMakeTable(_mode, groupby, data, "REGISTER PEMBELIAN", _date1, _date2, DetOrRekap);
        }

        function renderKpi(rows) {
            let totalDPP = 0;
            let totalPPN = 0;

            const supplierSet = new Set();
            const poSet = new Set();

            (rows || []).forEach(r => {
                totalDPP += currencyNormalizer(pickCI(r, 'NDPPRp'));
                totalPPN += currencyNormalizer(pickCI(r, 'NPPNRp'));

                const supplier = (pickCI(r, 'NAMACUSTSUPP') || '').trim();
                if (supplier) supplierSet.add(supplier);

                const po = (pickCI(r, 'NoPO') || '').trim();
                if (po) poSet.add(po);
            });

            const cards = [
                ['Total DPP', totalDPP, '#4f46e5', '#ede9fe', 'bi bi-receipt'],
                ['Total PPN', totalPPN, '#16a34a', '#dcfce7', 'bi bi-percent'],
                ['Jumlah Supplier', supplierSet.size, '#ca8a04', '#fef9c3', 'bi bi-people-fill'],
                ['Jumlah PO', poSet.size, '#dc2626', '#fee2e2', 'bi bi-file-earmark-text']
            ];

            document.getElementById('kpiStrip').innerHTML =
                cards.map(c => `
          <div class="kpi-card">
              <div class="kpi-ic" style="background:${c[3]};color:${c[2]}">
                  <i class="${c[4]}"></i>
              </div>
              <div>
                  <div class="kpi-label">${c[0]}</div>
                  <div class="kpi-val">
                      ${
                        c[0].startsWith('Total')
                            ? 'Rp ' + format_number(c[1], 0)
                            : format_number(c[1], 0)
                      }
                  </div>
              </div>
          </div>
      `).join('');
        }

        function pickCI(r, key) {
            if (r[key] !== undefined) return r[key];

            const lk = key.toLowerCase();

            for (const k in r) {
                if (k.toLowerCase() === lk)
                    return r[k];
            }
            return undefined;
        }

        /* -- CHARTS (Chart.js v4). -- */
        const CHART_PALETTE = ['#4F46E5', '#7C3AED', '#DB2777', '#2563eb', '#16a34a', '#ca8a04', '#ea580c', '#0891b2',
            '#e11d48', '#65a30d'
        ];
        let _charts = {};

        function fmtShort(v) {
            v = Math.round(num(v));
            const a = Math.abs(v);
            if (a >= 1e9) return (v / 1e9).toFixed(1).replace(/\.0$/, '') + ' M';
            if (a >= 1e6) return (v / 1e6).toFixed(1).replace(/\.0$/, '') + ' jt';
            if (a >= 1e3) return (v / 1e3).toFixed(0) + ' rb';
            return String(v);
        }

        function _destroyChart(id) {
            if (_charts[id]) {
                _charts[id].destroy();
                delete _charts[id];
            }
        }

        function loadLineChart(date1) {
            $.ajax({
                url: grafikUrl,
                type: "GET",
                data: {
                    date1: date1
                },
                success: function(res) {
                    console.log("Grafik Result :", res);
                    console.log("Jumlah data :", res.length);

                    const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov',
                        'Des'
                    ];
                    const jual = new Array(12).fill(0);
                    const beli = new Array(12).fill(0);
                    (res || []).forEach(r => {
                        console.log(r);
                        const idx = parseInt(r.Bulan) - 1;
                        if (r.Tipe === 'JUAL')
                            jual[idx] = currencyNormalizer(r.Total);

                        if (r.Tipe === 'BELI')
                            beli[idx] = currencyNormalizer(r.Total);
                    });

                    console.log("Data Penjualan :", jual);
                    console.log("Data Pembelian :", beli);

                    _destroyChart('aging');
                    _charts.aging = new Chart(document.getElementById('agingChart'), {
                        type: 'line',
                        data: {
                            labels: bulan,
                            datasets: [{
                                    label: 'Penjualan',
                                    data: jual,
                                    borderColor: '#4F46E5',
                                    backgroundColor: '#4F46E5',
                                    tension: .4,
                                    fill: false
                                },
                                {
                                    label: 'Pembelian',
                                    data: beli,
                                    borderColor: '#DB2777',
                                    backgroundColor: '#DB2777',
                                    tension: .4,
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            },
                            scales: {
                                y: {
                                    ticks: {
                                        callback: v => fmtShort(v)
                                    }
                                }
                            }
                        }
                    });
                }
            });
        }

        function buildCharts(rows) {
            if (typeof Chart === 'undefined') return;

            Chart.defaults.font.family = "'Segoe UI',system-ui,sans-serif";
            Chart.defaults.font.size = 12;
            Chart.defaults.color = "#64748b";

            //-----------------------------------------
            // TOP SUPPLIER
            //-----------------------------------------

            const supplierMap = {};

            (rows || []).forEach(r => {
                const supplier = pickCI(r, 'NAMACUSTSUPP') || 'Tidak diketahui';
                if (!supplierMap[supplier]) {
                    supplierMap[supplier] = 0;
                }
                supplierMap[supplier] += currencyNormalizer(
                    pickCI(r, 'NNETRp')
                );
            });

            const topSupplier =
                Object.entries(supplierMap)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 6);
            _destroyChart('topCustomer');

            _charts.topCustomer =
                new Chart(
                    document.getElementById('topCustomerChart'), {
                        type: 'bar',
                        data: {
                            labels: topSupplier.map(x => x[0]),
                            datasets: [{
                                label: 'Grand Total',

                                data: topSupplier.map(x => x[1]),

                                backgroundColor: topSupplier.map(
                                    (x, i) =>
                                    CHART_PALETTE[
                                        i % CHART_PALETTE.length
                                    ]
                                ),
                                borderRadius: 6
                            }]
                        },

                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) =>
                                            ' Rp ' + format_number(ctx.parsed.x, 0)
                                    }
                                }
                            },

                            scales: {
                                x: {
                                    ticks: {
                                        callback: (v) =>
                                            fmtShort(v)
                                    }
                                }
                            }
                        }
                    });

            //-----------------------------------------
            // CHART KANAN
            //-----------------------------------------

            // const monthly = {};
            // (rows || []).forEach(r => {
            //   const tgl = pickCI(r, 'TANGGAL') || pickCI(r, 'Tanggal');
            //   if (!tgl) return;
            //   const bulan = new Date(tgl).toLocaleString('id-ID', {
            //     month: 'short'
            //   });

            //   if (!monthly[bulan]) {
            //     monthly[bulan] = {
            //       dpp: 0,
            //       ppn: 0
            //     };
            //   }

            //   monthly[bulan].dpp += currencyNormalizer(
            //     pickCI(r, 'NDPPRp')
            //   );

            //   monthly[bulan].ppn += currencyNormalizer(
            //     pickCI(r, 'NPPNRp')
            //   );
            // });

            // const labels = Object.keys(monthly);
            // const dataDPP = labels.map(b => monthly[b].dpp);
            // const dataPPN = labels.map(b => monthly[b].ppn);

            // _destroyChart('aging');

            // _charts.aging = new Chart(
            //   document.getElementById('agingChart'),
            //   {type: 'line',
            //     data: {
            //       labels: labels,
            //         datasets: [
            //           {
            //             label: 'DPP',
            //             data: dataDPP,
            //             borderColor: '#4F46E5',
            //             backgroundColor: '#4F46E5',
            //             tension: 0.4,
            //             fill: false
            //           },
            //           {
            //             label: 'PPN',
            //             data: dataPPN,
            //             borderColor: '#16a34a',
            //             backgroundColor: '#16a34a',
            //             tension: 0.4,
            //             fill: false
            //           }
            //         ]
            //       },

            //       options: {
            //         responsive: true,
            //         maintainAspectRatio: false,
            //         plugins: {
            //           legend: {
            //             position: 'bottom'
            //           },
            //             tooltip: {
            //               callbacks: {
            //                 label: function(ctx){
            //                   return ctx.dataset.label + ' : Rp ' + format_number(ctx.parsed.y,0);
            //                 }
            //               }
            //             }
            //           },

            //           scales: {
            //             y: {
            //               ticks: {
            //                 callback: function(value){
            //                   return 'Rp ' + fmtShort(value);
            //                 }
            //               }
            //             }
            //           }
            //         }
            //       });
        }

        // === RENDER KE TABEL STYLED (.tb-report #mainTable) ===
        // Kolom dibangun DINAMIS dari gcart_header (hanya kolom yang terlihat /
        // item[2]===1, sesuai urutan simpanan). Jadi hasil "Customize Table"
        // (show/hide + urutan kolom) langsung tampil. <thead> ditulis ulang tiap
        // render. Subtotal/Grand Total = jumlah kolom Qnt, dikelompokkan per `groupby`.
        // (Data sudah terurut dari proc sesuai inputOrd, jadi cukup deteksi pergantian
        // nilai grup. Jika kolom Qnt disembunyikan, baris total tidak ditampilkan.)
        function renderRows(rows, groupby) {
            const cols = gcart_header.filter(c => c[2] === 1); // kolom terlihat, terurut
            const thead = document.querySelector('#mainTable thead');
            const tbody = document.getElementById('tableBody');
            const qntVisible = cols.some(c => c[0] === 'NDPPRp');
            // Baris Subtotal & Grand Total mengikuti toggle di modal Customize Table
            // (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal).
            // gsum_* dimuat oleh doSetHeader() saat klik Tampilkan, jadi pilihan user
            // (sudah tersimpan) langsung berlaku. Total hanya tampil bila kolom Qnt ada.
            const showSub = qntVisible && (gsum_issubtotal === 1);
            const showGrand = qntVisible && (gsum_isgrandtotal === 1);

            // HEADER dinamis dari gcart_header
            thead.innerHTML = '<tr>' + cols.map(function(c) {
                const isNum = (c[3] === 'float' || c[3] === 'int');
                return '<th' + (isNum ? ' class="num"' : '') + '>' + c[1] + '</th>';
            }).join('') + '</tr>';

            if (!rows || !rows.length) {
                tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length +
                    '">Tidak ada data ditemukan.</td></tr>';
                document.getElementById('footerLabel').textContent = 'Tidak ada data';
                return;
            }

            let html = '',
                prev = null,
                sub = {
                    NDPPRp: 0,
                    NPPNRp: 0,
                    NNETRp: 0
                },
                grand = {
                    NDPPRp: 0,
                    NPPNRp: 0,
                    NNETRp: 0
                };

            rows.forEach(function(r, i) {
                const now = r[groupby];

                // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
                if (showSub && i !== 0 && prev !== now) {
                    html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
                    sub = {
                        NDPPRp: 0,
                        NPPNRp: 0,
                        NNETRp: 0
                    };
                }

                sub.NDPPRp += currencyNormalizer(r.NDPPRp);
                sub.NPPNRp += currencyNormalizer(r.NPPNRp);
                sub.NNETRp += currencyNormalizer(r.NNETRp);

                grand.NDPPRp += currencyNormalizer(r.NDPPRp);
                grand.NPPNRp += currencyNormalizer(r.NPPNRp);
                grand.NNETRp += currencyNormalizer(r.NNETRp);

                // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5])
                html += '<tr class="data-row">' + cols.map(function(c) {
                    const key = c[0],
                        type = c[3];
                    // Status Otorisasi
                    if (key === 'NeedOtorisasi') {
                        return `<td> ${r.NeedOtorisasi == 1 ? '<span class="sp-badge is-inactive">Belum</span>' : '<span class="sp-badge is-active">Sudah</span>'} </td>`;
                    }

                    if (type === 'date') return '<td>' + format_date(r[key]) + '</td>';
                    if (type === 'float' || type === 'int') return '<td class="num">' + format_number(
                        currencyNormalizer(r[key]), c[5]) + '</td>';
                    return '<td>' + nullToEmpty(r[key]) + '</td>';
                }).join('') + '</tr>';

                prev = now;
            });

            // subtotal grup terakhir + grand total   mengikuti toggle di modal
            if (showSub) html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
            if (showGrand) html += totalRowTotal('GRAND TOTAL', grand, cols, 'grand-total');

            tbody.innerHTML = html;
            document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
        }

        // Baris total (Qnt saja): nilai di kolom Qnt, label di kolom pertama (bukan Qnt),
        // sel lain dikosongkan   mengikuti urutan kolom terlihat saat ini.
        function totalRowTotal(label, total, cols, cls) {
            const labelIdx = cols.findIndex(c =>
                !['NDPPRp', 'NPPNRp', 'NNETRp'].includes(c[0])
            );

            const tds = cols.map(function(c, idx) {
                if (c[0] === 'NDPPRp')
                    return '<td class="num">' + format_number(total.NDPPRp, 2) + '</td>';
                if (c[0] === 'NPPNRp')
                    return '<td class="num">' + format_number(total.NPPNRp, 2) + '</td>';
                if (c[0] === 'NNETRp')
                    return '<td class="num">' + format_number(total.NNETRp, 2) + '</td>';
                if (idx === labelIdx)
                    return '<td>' + label + '</td>';
                return '<td></td>';
            });

            return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
        }

        // === PENCARIAN SISI-KLIEN ===
        // Menyaring data yang SUDAH dimuat (lastRows) berdasarkan teks pencarian,
        // dicocokkan ke semua kolom yang sedang terlihat, lalu render ulang tabel
        // styled (renderRows menghitung ulang subtotal/grand total untuk hasil saring).
        function applyFilters() {
            if (!lastRows.length) return; // belum ada data dimuat

            const term = ($('#searchBox2').val() || '').trim().toLowerCase();
            if (!term) {
                renderRows(lastRows, currentGroupby);
                return;
            } // kosong -> tampilkan semua

            const cols = gcart_header.filter(c => c[2] === 1); // kolom yang terlihat
            const filtered = lastRows.filter(function(r) {
                return rowSearchText(r, cols).indexOf(term) !== -1;
            });

            renderRows(filtered, currentGroupby);
        }

        // Gabungan teks satu baris dari kolom terlihat (tanggal pakai format tampil
        // dd/mm/yyyy) supaya pencarian cocok dengan apa yang user lihat di tabel.
        function rowSearchText(r, cols) {
            return cols.map(function(c) {
                const v = r[c[0]];
                if (c[3] === 'date') return format_date(v);
                return (v == null ? '' : String(v));
            }).join(' ').toLowerCase();
        }

        function getKolomFilter() {
            // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
            // mode report menentukan kolom yang dipakai
            // berapa pun bisa asal dalam bentuk array

            let data = [];
            const order = $("#inputOrder").val();

            if (order == "N") {
                data = ['NoBukti', 'Tanggal'];
            } else if (order == "B") {
                if (DetOrRekap === 0) {
                    // Detail
                    data = ['NoBukti', 'NamaBrg'];
                } else {
                    // Rekap
                    data = ['NAMABRG', 'NAMABRG'];
                }
            } else if (order == "S") {
                if (DetOrRekap === 0) {
                    //Detail
                    data = ['NAMACUSTSUPP', 'NamaBrg'];
                } else {
                    //Rekap
                    data = ['NoBukti', 'NAMACUSTSUPP'];
                }
            }

            return data;
        }

        function reportMode(_mode) {
            if (jenisreport != _mode) {
                let prev_mode = jenisreport;
                jenisreport = _mode;

                $("#tombolMode" + prev_mode).removeClass("btn-primary");
                $("#tombolMode" + prev_mode).addClass("btn-outline-primary");

                $("#tombolMode" + jenisreport).removeClass("btn-outline-primary");
                $("#tombolMode" + jenisreport).addClass("btn-primary");

                setModeReport();
            }
            DetOrRekap = _mode;
        }

        function setModeReport() {
            if ($("#inputOrder").val() == "N") {
                if (jenisreport === 0) {
                    g_modeReport = modereport_detailnobukti;
                } else {
                    g_modeReport = modereport_rekapnobukti;
                }
            } else if ($("#inputOrder").val() == "B") {
                if (jenisreport === 0) {
                    g_modeReport = modereport_detailbarang;
                } else {
                    g_modeReport = modereport_rekapbarang;
                }
            } else {
                if (jenisreport === 0) {
                    g_modeReport = modereport_detailcustomer;
                } else {
                    g_modeReport = modereport_rekapcustomer;
                }
            }

            doSetHeader(g_modeReport);
            doShowCustomize();
        }
    </script>
@endsection
