@extends('report.masterreport2')

<!-- Warna centang -->
  <style>
    .checkmark-red {
      color: red !important;
      font-weight: bold;
      margin-left: 6px;
    }

    #inputOtorisasi{
      border: 0;
      background: none;
      padding: 0;
      box-shadow: none;
      color: #495057;
      font-weight: 600;
    }

    #inputOtorisasi:hover,
    #inputOtorisasi:focus{
      color: #0d6efd;
      box-shadow: none;
    }

    #inputValas{
      border: 0;
      background: none;
      padding: 0;
      box-shadow: none;
      color: #495057;
      font-weight: 600;
    }

    #inputValas:hover,
    #inputValas:focus{
      color: #0d6efd;
      box-shadow: none;
    }

    #inputReportMode{
      border: 0;
      background: none;
      padding: 0;
      box-shadow: none;
      color: #495057;
      font-weight: 600;
    }

    #inputReportMode:hover,
    #inputReportMode:focus{
      color: #0d6efd;
      box-shadow: none;
    }

    .tb-report .table-wrap { min-height: 15vh; }

  </style>
<!-- Warna centang -->


@section('header2')
  <div class="tb-report main">
      <div class="content">

        <!-- TOOLBAR -->
        <div class="toolbar">
          <div>
            <div class="page-title">PO</div>
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

          <!-- otorisasi -->
          <!-- <div class="filter-wrap">
          <button
                class="btn btn-outline-primary dropdown-toggle"
                type="button"
                id="inputOtorisasi"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                Filter
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
              <li><a class="dropdown-item" style="cursor: pointer;" data-value="3" onclick="setOtorisasi('3')">Diterima
              <span class="checkmark-red" style="display:none;">&#10003</span>
              </a></li>
              <li><a class="dropdown-item" style="cursor: pointer;" data-value="4" onclick="setOtorisasi('4')">Menunggu
              <span class="checkmark-red" style="display:none;">&#10003</span>
              </a></li>
              <li><a class="dropdown-item" style="cursor: pointer;" data-value="5" onclick="setOtorisasi('5')">Sebagian
              <span class="checkmark-red" style="display:none;">&#10003</span>
              </a></li>
              <li><a class="dropdown-item" style="cursor: pointer;" data-value="6" onclick="setOtorisasi('6')">Batal
              <span class="checkmark-red" style="display:none;">&#10003</span>
              </a></li>
            </ul>
          </div> -->

          <!-- VALAS -->
          <!-- <div class="filter-wrap">
            <button
                class="btn btn-outline-primary dropdown-toggle"
                type="button"
                id="inputValas"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                VALAS
            </button>
            <ul class="dropdown-menu" id="dropdownValas" aria-labelledby="inputValas">
                <li><a class="dropdown-item" data-value="0" onclick="setValas('0')">IDR
                <span class="checkmark-red" style="display:none;">&#10003</span>
                </a></li>
                <li><a class="dropdown-item" data-value="1" onclick="setValas('1')">VLS
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
            <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
            <!-- <button class="btn-load" onclick="doShowFormFilterData()" title="Filter Data"><i class="bi bi-filter-left"></i> Filter Data</button> -->
            <button
              class="btn-load"
              data-bs-toggle="modal"
              data-bs-target="#modalFilter">
              <i class="fas fa-filter"></i> Filter
            </button>
            <button class="btn-load" onclick="doShowFormCustomizeTable()" title="Customize Table"><i class="fas fa-cog"></i> Customize Table</button>
            <button class="btn-load" onclick="makeTable('REPORT')" title="Tampilkan laporan"><i class="fas fa-check"></i> Tampilkan</button>
            <div class="export-wrap" id="exportWrap">
              <button class="export-btn" onclick="toggleExport()"><i class="bi bi-arrow-down"></i> Export <i class="bi bi-caret-down-fill"></i></button>
              <div class="export-drop" id="exportDrop">
                <div class="export-opt" onclick="doExport('Excel')"><i class="bi bi-journals text-success"></i> Ekspor ke <span class="ext">XLSX</span></div>
                <div class="export-opt" onclick="doExport('CSV')"><i class="bi bi-clipboard"></i> Ekspor ke <span class="ext">CSV</span></div>
                <div class="export-opt" onclick="doExport('Print')"><i class="bi bi-printer-fill text-warning"></i> Cetak Laporan</div>
              </div>
            </div>
          </div>
        </div>

        <!-- TABLE -->
        <div class="table-outer">
          <div class="table-wrap">
            <table class="tb" id="mainTable">
              <thead>
                <tr>
                  <th style="min-width:130px">No.Bukti</th>
                  <th style="min-width:90px">Tanggal</th>
                  <th style="min-width:150px">Nama Supplier</th>
                  <th style="min-width:290px">Nama Barang</th>
                  <th class="num" style="min-width:10px">Sat</th>
                  <th class="num" style="min-width:10px">Qnt</th>
                  <th class="num" style="min-width:10px">Harga</th>
                  <th class="num" style="min-width:10px">VLS</th>
                  <th class="num" style="min-width:10px">Disc</th>
                  <th class="num" style="min-width:10px">DPP</th>
                  <th class="num" style="min-width:10px">PPN</th>
                  <th class="num" style="min-width:10px">Total</th>
                  <th class="num" style="min-width:10px">Otorisasi</th>
                  <th class="num" style="min-width:10px">Di Terima</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                <tr class="empty-row"><td colspan="14">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
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

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
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
                    <label>VALAS</label>
                    <select class="form-select" id="modalValas">
                        <option value="0">IDR</option>
                        <option value="1">VLS</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Otorisasi</label>
                    <select class="form-select" id="modalOtorisasi">
                        <option value="2">Semua</option>
                        <option value="1">Belum Otorisasi</option>
                        <option value="0">Sudah Otorisasi</option>
                        <option value="3">Diterima</option>
                        <option value="4">Menunggu</option>
                        <option value="5">Sebagian</option>
                        <option value="6">Batal</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-primary"
                    onclick="applyModalFilter()">
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
  let globalOrderBy = "N";   // default: Nomor Bukti
  let globalReportMode = "0"; // default: Detail
  let globalValas = "0";
  let lastRows = [];         // hasil fetch terakhir (dipakai renderRows / export / search)
  let currentGroupby = 'NoBukti'; // groupby aktif untuk render ulang saat search

  $(document).ready(function () {
      setOtorisasi(globalOtorisasi);
      // setOrderBy(globalOrderBy);
      setValas(globalValas);
      setReportMode(globalReportMode);
  });

  $('#modalFilter').on('show.bs.modal', function () {
    $("#modalReport").val(globalReportMode);
    $("#modalValas").val(globalValas);
    $("#modalOtorisasi").val(globalOtorisasi);
  });

  function applyModalFilter() {

    setReportMode($("#modalReport").val());
    setValas($("#modalValas").val());
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
    //     "3": "Diterima",
    //     "4": "Menunggu",
    //     "5": "Sebagian",
    //     "6": "Batal"
    // };

    // $("#inputOtorisasi").html(
    //     `Filter : ${text[val]}`
    // );
  }

  /* -- EXPORT -- */
  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });

  // order by
  function setOrderBy(val) {
    globalOrderBy = val;

    // sembunyikan semua centang
    $('#dropdownOrder .checkmark-red').hide();

    // tampilkan centang yang dipilih
    $(`#dropdownOrder .dropdown-item[data-value='${val}'] .checkmark-red`).show();
  }

  // valas
  function setValas(val) {
    globalValas = val;

    // // sembunyikan semua centang
    // $('#dropdownValas .checkmark-red').hide();

    // // tampilkan centang yang dipilih
    // $(`#dropdownValas .dropdown-item[data-value='${val}'] .checkmark-red`).show();

    // // Ubah tulisan tombol
    // const text = {
    //     "0": "IDR",
    //     "1": "VLS"
    // };

    // $("#inputValas").html(
    //     `Valas : ${text[val]}`
    // );
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

  // var modereport_detailnobukti = 0, modereport_detailbarang = 1, modereport_detailcustomer = 2 ;
  // var modereport_rekapnobukti = 3, modereport_rekapbarang = 4, modereport_rekapcustomer = 5 ;
  // g_modeReport = modereport_detailnobukti;

  var modereport_detailidr = 0;
  var modereport_detailvls = 1;
  var modereport_rekapidr  = 2;
  var modereport_rekapvls  = 3;

  g_modeReport = modereport_detailidr;
  var jenisreport = 0; // ini untuk detail dan rekap

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailidr) {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Sat', 1, 'varchar', 0, 0],
        ['Qnt', 'Qnt', 1, 'float', 1, 2],
        ['HARGA', 'Harga', 1, 'float', 1, 2],
        ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
        ['DISCP', 'Disc', 1, 'float', 1, 2],
        ['NDPP', 'DPP', 1, 'float', 1, 2],
        ['NPPN', 'PPN', 1, 'float', 1, 2],
        ['TotalIDR', 'Total', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['DiTerima', 'Di Terima', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if (g_modeReport == modereport_detailvls){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Sat', 1, 'varchar', 0, 0],
        ['Qnt', 'Qnt', 1, 'float', 1, 2],
        ['HARGA', 'Harga', 1, 'float', 1, 2],
        ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
        ['disctotusd', 'Disc', 1, 'float', 1, 2],
        ['Ndppusd', 'DPP', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN', 1, 'float', 1, 2],
        ['totalusd', 'Total', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['DiTerima', 'Di Terima', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if(g_modeReport == modereport_rekapidr){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['HARGA', 'Harga', 1, 'float', 1, 2],
        ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
        ['DISCP', 'Disc', 1, 'float', 1, 2],
        ['NDPP', 'DPP', 1, 'float', 1, 2],
        ['NPPN', 'PPN', 1, 'float', 1, 2],
        ['TotalIDR', 'Total', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['DiTerima', 'Di Terima', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else if(g_modeReport == modereport_rekapvls){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['HARGA', 'Harga', 1, 'float', 1, 2],
        ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
        ['disctotusd', 'Disc', 1, 'float', 1, 2],
        ['Ndppusd', 'DPP', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN', 1, 'float', 1, 2],
        ['totalusd', 'Total', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['DiTerima', 'Di Terima', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['HARGA', 'Harga', 1, 'float', 1, 2],
        ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
        ['disctotusd', 'Disc', 1, 'float', 1, 2],
        ['Ndppusd', 'DPP', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN', 1, 'float', 1, 2],
        ['totalusd', 'Total', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['DiTerima', 'Di Terima', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    }
  }

  const reportUrl = "{{ url('laporanpurchaseorderpo_doReport') }}"
  function makeTable(_mode) {
    console.log(" makeTable jalankan mode:", _mode);

    let groupby = '';
    let _date1 = $("#inputDate1").val();
    let _date2 = $("#inputDate2").val();

    let input_oto = globalOtorisasi;
    // let input_order = globalOrderBy;
    let input_valas = globalValas;

    // mode report 
    if (input_valas == "0") {
      if (DetOrRekap === 0) {
        g_modeReport = modereport_detailidr;
        groupby = 'NoBukti';
      } else {
        g_modeReport = modereport_rekapidr;
        groupby = 'NoBukti';
      }
    } else if (input_valas == "0") {
      if (DetOrRekap === 0) {
        g_modeReport = modereport_detailvls;
        groupby = 'NoBukti';
      } else {
        g_modeReport = modereport_rekapvls;
        groupby = 'NoBukti';
      }
    }

    console.log("Mode report aktif:", g_modeReport, "| Group By:", groupby);

    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let inputOtoSP = globalOtorisasi;

    if (['3', '4', '5', '6'].includes(globalOtorisasi)) {
      inputOtoSP = '2';   // Semua
    }

    let data = {
      date1: _date1,
      date2: _date2,
      inputOto: inputOtoSP,
      inputOrd: 'N',
      inputDetOrRekap: DetOrRekap,
      inputValas: globalValas
    };

    // Ambil data SEKALI, lalu render langsung ke tabel styled baru (#tableBody).
    $.ajax({
      url    : reportUrl,
      type   : 'get',
      data   : data,
      success: function (res) {
      let rows = res || [];

      rows.forEach(r => {
      console.log(
          r.NoBukti,
          "Qnt =", r.Qnt,
          "qntLPB =", r.qntLPB,
          "QntBatal =", r.QntBatal,
          "Status =", getStatusDiterima(r)
        );
      });

        if (globalOtorisasi === '3') {
          rows = rows.filter(r => getStatusDiterima(r) === 'Diterima');
        }
        else if (globalOtorisasi === '4') {
          rows = rows.filter(r => getStatusDiterima(r) === 'Menunggu');
        }
        else if (globalOtorisasi === '5') {
          rows = rows.filter(r => getStatusDiterima(r) === 'Sebagian');
        }
        else if (globalOtorisasi === '6') {
          rows = rows.filter(r => getStatusDiterima(r) === 'Batal');
        }

        lastRows = rows;
        currentGroupby = groupby;       
        $('#searchBox2').val('');       
        renderRows(lastRows, groupby);   
      },
      error  : function () {
        lastRows = [];
        currentGroupby = groupby;
        renderRows(lastRows, groupby);
      }
    });

    // console.log("Data terkirim ke server:", data);

    // doMakeTable(_mode, groupby, data, "REPORT PENGADAAN PURCHASE ORDER (PO)", _date1, _date2, DetOrRekap);
  }

  function getStatusDiterima (r) {
    const qnt      = currencyNormalizer(r.Qnt);
    const qntLPB   = currencyNormalizer(r.qntLPB);
    const qntBatal = currencyNormalizer(r.QntBatal);

    // Batal
    if (qnt === 0 && qntLPB === 0 && qntBatal > 0) {
      return "Batal";
    }

    // Diterima (habis)
    if (
      (qnt === qntLPB && qnt > 0) ||
      (qnt === 0 && qntLPB > 0)
    ) {
      return "Diterima";
    }

    // Menunggu
    if (qnt > 0 && qntLPB === 0) {
      return "Menunggu";
    }

    // Sebagian
    if (qnt > qntLPB && qntLPB > 0) {
      return "Sebagian";
    }
    return "";
  }

  // === RENDER KE TABEL STYLED (.tb-report #mainTable) ===
  // Kolom dibangun DINAMIS dari gcart_header (hanya kolom yang terlihat /
  // item[2]===1, sesuai urutan simpanan). Jadi hasil "Customize Table"
  // (show/hide + urutan kolom) langsung tampil. <thead> ditulis ulang tiap
  // render. Subtotal/Grand Total = jumlah kolom Qnt, dikelompokkan per `groupby`.
  // (Data sudah terurut dari proc sesuai inputOrd, jadi cukup deteksi pergantian
  // nilai grup. Jika kolom Qnt disembunyikan, baris total tidak ditampilkan.)
  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1); // kolom terlihat, terurut
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');
    const totalVisible = cols.some(c => ['NDPP', 'Ndppusd'].includes(c[0]));
    // Baris Subtotal & Grand Total mengikuti toggle di modal Customize Table
    // (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal).
    // gsum_* dimuat oleh doSetHeader() saat klik Tampilkan, jadi pilihan user
    // (sudah tersimpan) langsung berlaku. Total hanya tampil bila kolom Qnt ada.
    const showSub   = totalVisible && (gsum_issubtotal === 1);
    const showGrand = totalVisible && (gsum_isgrandtotal === 1);

    // HEADER dinamis dari gcart_header
    thead.innerHTML = '<tr>' + cols.map(function (c) {
      const isNum = (c[3] === 'float' || c[3] === 'int');
      return '<th' + (isNum ? ' class="num"' : '') + '>' + c[1] + '</th>';
    }).join('') + '</tr>';

    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null, sub = { Qnt: 0, HARGA: 0, DISCP: 0, NDPP: 0, NPPN: 0, TotalIDR: 0, Ndppusd: 0, NPPNusd: 0, totalusd: 0, disctotusd: 0 }, grand = { Qnt: 0, HARGA: 0, DISCP: 0, NDPP: 0, NPPN: 0, TotalIDR: 0, Ndppusd: 0, NPPNusd: 0, totalusd: 0, disctotusd: 0 };

    rows.forEach(function (r, i) {
      const now = r[groupby];

      // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
      if (showSub && i !== 0 && prev !== now) { html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row'); sub = { Qnt: 0, HARGA: 0, DISCP: 0, NDPP: 0, NPPN: 0, TotalIDR: 0, Ndppusd: 0, NPPNusd: 0, totalusd: 0, disctotusd: 0 };
      }

      sub.Qnt   += currencyNormalizer(r.Qnt);
      sub.HARGA += currencyNormalizer(r.HARGA);
      sub.DISCP += currencyNormalizer(r.DISCP);
      sub.NDPP += currencyNormalizer(r.NDPP);
      sub.NPPN += currencyNormalizer(r.NPPN);
      sub.TotalIDR += currencyNormalizer(r.TotalIDR);
      sub.disctotusd += currencyNormalizer(r.disctotusd);
      sub.Ndppusd += currencyNormalizer(r.Ndppusd);
      sub.NPPNusd += currencyNormalizer(r.NPPNusd);
      sub.totalusd += currencyNormalizer(r.totalusd);

      grand.Qnt   += currencyNormalizer(r.Qnt);
      grand.HARGA += currencyNormalizer(r.HARGA);
      grand.DISCP += currencyNormalizer(r.DISCP);
      grand.NDPP += currencyNormalizer(r.NDPP);
      grand.NPPN += currencyNormalizer(r.NPPN);
      grand.TotalIDR += currencyNormalizer(r.TotalIDR);
      grand.disctotusd += currencyNormalizer(r.disctotusd);
      grand.Ndppusd += currencyNormalizer(r.Ndppusd);
      grand.NPPNusd += currencyNormalizer(r.NPPNusd);
      grand.totalusd += currencyNormalizer(r.totalusd);

      // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5])
      html += '<tr class="data-row">' + cols.map(function (c) {
      const key = c[0], type = c[3];

      // Status Otorisasi
      if (key === 'NeedOtorisasi') {
        return `<td> ${r.NeedOtorisasi == 1 ? '<span class="sp-badge is-inactive">Belum</span>' : '<span class="sp-badge is-active">Sudah</span>'} </td>`;
      }

      // Status diterima
      if (key === 'DiTerima') {

      const status = getStatusDiterima(r);

      switch (status) {
        case 'Diterima':
          return '<td><span class="sp-badge is-active">Diterima</span></td>';

        case 'Menunggu':
          return '<td><span class="sp-badge is-user">Menunggu</span></td>';

        case 'Sebagian':
          return '<td><span class="sp-badge is-supervisor">Sebagian</span></td>';

        case 'Batal':
          return '<td><span class="sp-badge is-inactive">Batal</span></td>';

        default:
          return '<td></td>';
        }
      }

        if (type === 'date') return '<td>' + format_date(r[key]) + '</td>';
        if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(r[key]), c[5]) + '</td>';
        if (key === 'NamaBrg') return '<td style="white-space: nowrap;">' + nullToEmpty(r[key]) + '</td>';
        if (key === 'NAMACUSTSUPP') return '<td style="white-space: nowrap;">' + nullToEmpty(r[key]) + '</td>';
        return '<td>' + nullToEmpty(r[key]) + '</td>';
      }).join('') + '</tr>';

      prev = now;
    });

    // subtotal grup terakhir + grand total   mengikuti toggle di modal
    if (showSub)   html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
    if (showGrand) html += totalRowTotal('GRAND TOTAL', grand, cols, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
  }

  // Baris total (Qnt saja): nilai di kolom Qnt, label di kolom pertama (bukan Qnt),
  // sel lain dikosongkan   mengikuti urutan kolom terlihat saat ini.
  function totalRowTotal(label, total, cols, cls) {
    const labelIdx = cols.findIndex(c =>
        !['HARGA', 'DISCP', 'Qnt', 'NDPP', 'NPPN', 'TotalIDR', 'disctotusd', 'Ndppusd', 'NPPNusd', 'totalusd'].includes(c[0])
    );

    const tds = cols.map(function(c, idx) {
        if (c[0] === 'HARGA')
            return '<td class="num">' + format_number(total.HARGA, 2) + '</td>';
        if (c[0] === 'DISCP')
            return '<td class="num">' + format_number(total.DISCP, 2) + '</td>';
        if (c[0] === 'NDPP')
            return '<td class="num">' + format_number(total.NDPP, 2) + '</td>';
        if (c[0] === 'NPPN')
            return '<td class="num">' + format_number(total.NPPN, 2) + '</td>';
        if (c[0] === 'TotalIDR')
            return '<td class="num">' + format_number(total.TotalIDR, 2) + '</td>';
        if (c[0] === 'disctotusd')
            return '<td class="num">' + format_number(total.disctotusd, 2) + '</td>';
        if (c[0] === 'Ndppusd')
            return '<td class="num">' + format_number(total.Ndppusd, 2) + '</td>';
        if (c[0] === 'NPPNusd')
            return '<td class="num">' + format_number(total.NPPNusd, 2) + '</td>';
        if (c[0] === 'totalusd')
            return '<td class="num">' + format_number(total.totalusd, 2) + '</td>';
        if (c[0] === 'Qnt')
            return '<td class="num">' + format_number(total.Qnt, 2) + '</td>';
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
    if (!lastRows.length) return;        // belum ada data dimuat

    const term = ($('#searchBox2').val() || '').trim().toLowerCase();
    if (!term) { renderRows(lastRows, currentGroupby); return; }   // kosong -> tampilkan semua

    const cols = gcart_header.filter(c => c[2] === 1); // kolom yang terlihat
    const filtered = lastRows.filter(function (r) {
      return rowSearchText(r, cols).indexOf(term) !== -1;
    });

    renderRows(filtered, currentGroupby);
  }

  // Gabungan teks satu baris dari kolom terlihat (tanggal pakai format tampil
  // dd/mm/yyyy) supaya pencarian cocok dengan apa yang user lihat di tabel.
  function rowSearchText(r, cols) {
    return cols.map(function (c) {
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
    if ($("#inputOrder").val() == "N"){
      data = ['NoBukti', 'Tanggal'];
    } else if ($("#inputOrder").val() == "B"){
      data = ['KodeBrg', 'NamaBrg'];
    } else {
      data = ['KodeCustSupp', 'NAMACUSTSUPP'];
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