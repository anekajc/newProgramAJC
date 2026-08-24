@extends('report.masterreportGudang')
@include('report.modalBrowseMaster')

<!-- Chart.js v4 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

@section('header2')
<style>
  /* Chart section (khusus report ini, bukan bagian dari report-table.css) */
  .chart-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 14px;
  }
  @media (max-width: 900px) { .chart-grid { grid-template-columns: 1fr; } }
  .chart-box {
    background: #fff; border: 1.5px solid #E2E8F4; border-radius: 12px;
    padding: 16px 20px; box-shadow: 0 1px 4px rgba(0,0,0,.06);
  }
  .chart-box h3 {
    font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 12px;
  }
  .chart-holder { position: relative; height: 260px; }
  .chart-holder canvas { max-height: 260px; }

  /* Periode Bulan/Tahun */
  .period-select {
    border: none; background: transparent; font-size: 13px; font-weight: 700;
    color: #4F46E5; outline: none; cursor: pointer;
  }

  #tabel thead th {
    font-size: 13px !important;
    font-weight: 700 !important;
    padding: 12px 14px !important;
    white-space: nowrap;
  }
  #tabel tbody td {
    font-size: 13.5px !important;
    padding: 11px 14px !important;
    line-height: 1.5 !important;
  }
  #tabel tbody tr td {
    border-bottom: 1px solid #EEF1F8 !important;
  }

  /* Samakan baris Subtotal & Grand Total dengan .tb-report .subtotal-row / .grand-total
     di report-table.css. */
  #tabel tr[id^="strow"] td {
    border: none !important;
    border-top: 1.5px solid #E2E8F4 !important;
    border-bottom: 2px solid #E2E8F4 !important;
    background: #F8F9FF !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    padding: 11px 14px !important;
  }
  #tabel tr[id^="strow"] td.st {
    text-align: left !important;
  }
  #tabel #gtrow td {
    border: none !important;
    border-top: 2px solid #4F46E5 !important;
    background: linear-gradient(135deg, #EEF2FF, #F5F3FF) !important;
    font-weight: 800 !important;
    font-size: 14.5px !important;
    padding: 13px 14px !important;
  }
  #tabel #gtrow td.gt {
    text-align: left !important;
  }
  #tabel #gtrow td:not(.gt) {
    color: #4F46E5 !important;
  }

  /* Popup "Pilih Data" (Gudang/Grup/Kategori/SubKategori/Merk) dari dalam
     modal Filter. Dibuat manual */
  .modal-picker-backdrop {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0, 0, 0, .5);
    z-index: 1071;
  }
  .modal-picker-backdrop.show { display: block; }
  .modal-picker {
    display: none;
    position: fixed; inset: 0;
    z-index: 1072;
    overflow-x: hidden; overflow-y: auto;
    outline: 0;
  }
  .modal-picker.show { display: block; }
  .modal-picker .modal-dialog {
    margin: 1.75rem auto;
  }
</style>

<div class="tb-report main" style="font-family: 'Segoe UI', sans-serif;">
  <div class="content" style="padding: 20px 24px 0;">


    <div hidden>
      <button type="button" id="buttonMode0" onclick="doReportMode(0)"></button>
      <button type="button" id="buttonMode1" onclick="doReportMode(1)"></button>
    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">
      {{-- <div>
        <div class="page-title">
          @if ($mode_menu == 'QTY')
           Stok Quantity
          @elseif ($mode_menu == 'RP')
           Stok Rupiah
          @elseif ($mode_menu == 'QTYRP')
            Stok Quantity + Rupiah
          @else
            Stok per Periode
          @endif
        </div>
      </div> --}}

      <!-- Periode -->
      <div class="filter-wrap">
        <label>Periode</label>
        @if ($mode_menu == 'PERIODE')
          <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
          <span class="filter-sep">s/d</span>
          <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
        @else
          <!-- Bulan/Tahun -->
          <select class="period-select" id="periodBulan" onchange="changePeriodParts()"></select>
          <select class="period-select" id="periodTahun" onchange="changePeriodParts()"></select>
          <input type="hidden" id="inputDate1" value="{!! date('Y-m') !!}">
        @endif
      </div>

      {{-- Search --}}
      <div>
          <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
      </div>

      <!-- search + filter + filter data + customize + tampilkan + export -->
      <div class="action-group">
        <button
          class="btn-load"
          data-bs-toggle="modal"
          data-bs-target="#modalFilter">
          <i class="fas fa-filter"></i> Filter
        </button>
        <button class="btn-load" onclick="doShowFormFilterData()" title="Filter Data"><i class="fas fa-magnifying-glass"></i> Filter Data</button>
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

    <div id="kpiGrid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 14px;">
      <div style="background: #fff; border: 1.5px solid #E2E8F4; border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
        <div style="width: 10px; height: 10px; border-radius: 50%; background: #1D4ED8;"></div>
        <div>
          <div style="font-size: 11px; font-weight: 700; color: #5A6A85; text-transform: uppercase;">TOTAL ITEM</div>
          <div style="font-size: 14px; font-weight: 800; margin-top: 2px;" id="kpiTotalItem">0</div>
        </div>
      </div>
      <!-- Nilai Stok Total: cuma relevan untuk mode RP / QTYRP (ada nilai Rupiah-nya) -->
      <div id="kpiNilaiStokCard" style="background: #fff; border: 1.5px solid #E2E8F4; border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
        <div style="width: 10px; height: 10px; border-radius: 50%; background: #15803D;"></div>
        <div>
          <div style="font-size: 11px; font-weight: 700; color: #5A6A85; text-transform: uppercase;">NILAI STOK TOTAL</div>
          <div style="font-size: 14px; font-weight: 800; margin-top: 2px;" id="kpiNilaiStok">0</div>
        </div>
      </div>
      <div style="background: #fff; border: 1.5px solid #E2E8F4; border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
        <div style="width: 10px; height: 10px; border-radius: 50%; background: #B45309;"></div>
        <div>
          <div style="font-size: 11px; font-weight: 700; color: #5A6A85; text-transform: uppercase;">STOK MENIPIS</div>
          <div style="font-size: 14px; font-weight: 800; margin-top: 2px;" id="kpiStokMenipis">0</div>
        </div>
      </div>
      <div style="background: #fff; border: 1.5px solid #E2E8F4; border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
        <div style="width: 10px; height: 10px; border-radius: 50%; background: #7C3AED;"></div>
        <div>
          <div style="font-size: 11px; font-weight: 700; color: #5A6A85; text-transform: uppercase;">TURN OVER STOCK</div>
          <div style="font-size: 14px; font-weight: 800; margin-top: 2px;" id="kpiTOS">0 %</div>
        </div>
      </div>
    </div>

    <!-- CHARTS  -->
    <div class="chart-grid" id="chartGrid">
      <div class="chart-box">
        <h3>Penjualan Terbanyak</h3>
        <div class="chart-holder"><canvas id="topJualChart"></canvas></div>
      </div>
      <div class="chart-box">
        <h3>Perbandingan Beli vs Jual</h3>
        <div class="chart-holder"><canvas id="beliJualChart"></canvas></div>
      </div>
    </div>

    <div class="table-outer">
      <div class="table-wrap" id="showTableReport">
        <table class="tb" id="tabel">
          <thead id="tabel_header">
            <tr>
              <th>KODE BARANG</th>
              <th>NAMA BARANG</th>
              <th>SATUAN</th>
              <th>GDG</th>
              <th>AWAL</th>
              <th>BELI</th>
              <th>R.JUAL</th>
              <th>ADJ (+)</th>
              <th>TR (+)</th>
              <th>RPM (+)</th>
              <th>PRD</th>
              <th>JUAL</th>
              <th>R.BELI</th>
              <th>ADJ (-)</th>
              <th>TR (=)</th>
              <th>PMK (-)</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="tabel_data">
            <tr class="empty-row">
              <td colspan="100%">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer Tabel (Belum ada data dimuat)  -->
      <div class="table-footer">
        <span id="footerLabel">Belum ada data dimuat</span>
      </div>

    </div>

  </div>
</div>

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
          <label>Gudang</label>
          <div class="input-group input-group-sm">
            <input type="text" id="inputGudang" class="form-control" placeholder="-" value="-" readonly>
            <button type="button" class="btn btn-primary" onclick="openPickMaster('inputGudang', '{!! $gudang !!}', 'Pilih Gudang')"><i class="bi bi-search"></i></button>
          </div>
        </div>

        <div class="mb-3">
          <label>No Satuan</label>
          <input type="number" id="inputIsi" class="form-control" value="1">
        </div>

        <div class="mb-3 d-flex align-items-center" style="gap:8px;">
          <input type="checkbox" id="inputStockMinus" style="width:18px;height:18px;cursor:pointer;">
          <label for="inputStockMinus" style="cursor:pointer;margin:0;">Stock Minus?</label>
        </div>

        <div class="mb-3">
          <label>Grup</label>
          <div class="input-group input-group-sm">
            <input type="text" id="inputGrup" class="form-control" placeholder="-" value="-" readonly>
            <button type="button" class="btn btn-primary" onclick="openPickMaster('inputGrup', '{!! $grup !!}', 'Pilih Grup')"><i class="bi bi-search"></i></button>
          </div>
        </div>

        <div class="mb-3">
          <label>Kategori</label>
          <div class="input-group input-group-sm">
            <input type="text" id="inputKategori" class="form-control" placeholder="-" value="-" readonly>
            <button type="button" class="btn btn-primary" onclick="openPickMaster('inputKategori', '{!! $kategori !!}', 'Pilih Kategori')"><i class="bi bi-search"></i></button>
          </div>
        </div>

        <div class="mb-3">
          <label>Sub Kategori</label>
          <div class="input-group input-group-sm">
            <input type="text" id="inputSubKategori" class="form-control" placeholder="-" value="-" readonly>
            <button type="button" class="btn btn-primary" onclick="openPickMaster('inputSubKategori', '{!! $subkategori !!}', 'Pilih Sub Kategori')"><i class="bi bi-search"></i></button>
          </div>
        </div>

        <div class="mb-3">
          <label>Merk</label>
          <div class="input-group input-group-sm">
            <input type="text" id="inputMerk" class="form-control" placeholder="-" value="-" readonly>
            <button type="button" class="btn btn-primary" onclick="openPickMaster('inputMerk', '{!! $merk !!}', 'Pilih Merk')"><i class="bi bi-search"></i></button>
          </div>
        </div>

        <div class="mb-3">
          <label>Status Agen</label>
          <select id="inputAgenSelect" class="form-select" onchange="setAgen(this.value)">
            <option value="0">Agen</option>
            <option value="1">Non-Agen</option>
            <option value="2" selected>Semua</option>
          </select>
        </div>

      </div>

      <div class="modal-footer">
        <button
          class="btn btn-primary"
          data-bs-dismiss="modal">
          Terapkan
        </button>
      </div>

    </div>
  </div>
</div>
<!-- modal filter -->

<!-- modal pilih data master (Gudang/Grup/Kategori/SubKategori/Merk) -->
<div class="modal-picker-backdrop" id="modalPickMasterBackdrop" onclick="closePickMaster()"></div>
<div class="modal-picker" id="modalPickMaster" tabindex="-1" role="dialog" aria-labelledby="modalPickMasterLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPickMasterLabel">Pilih Data</h5>
        <button type="button" class="btn-close" aria-label="Close" onclick="closePickMaster()"></button>
      </div>
      <div class="modal-body">
        <table id="tabelPickMaster" class="table table-bordered table-striped">
          <thead id="tabelPickMaster_header" class="text-center"></thead>
          <tbody id="tabelPickMaster_data" class="text-left"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closePickMaster()">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- modal pilih data master -->

@endsection

@section('jsreport')
<script src="{!! URL::asset('public/js/ajc-browsemaster.js') !!}"></script>
<script type="text/javascript">

  var modereport_qty = 0, modereport_rp = 1, modereport_qtyrp = 2, modereport_periode = 3;
  g_modeReport = modereport_qty;

  let globalAgen = "2";

  var reportTitle = "";

  if (typeof loadingHtml !== 'function') {
    window.loadingHtml = function (msg) {
      return '<span style="display:inline-flex;align-items:center;gap:6px;color:#5A6A85;">' +
             '<i class="fas fa-spinner fa-spin"></i> ' + (msg || 'Memuat...') + '</span>';
    };
  }

  /* modal "Pilih Data" (Gudang/Grup/Kategori/SubKategori/Merk)
     Menggantikan popup shared #formBrowseMaster (search + Submit) dengan
     Actions berisi tombol "+" per baris. */
  let pickerTargetInput = "";

  function openPickMaster(targetInputId, url, title) {
    pickerTargetInput = targetInputId;
    $("#modalPickMasterLabel").text(title || "Pilih Data");

    try {
      if ($.fn.DataTable.isDataTable('#tabelPickMaster')) {
        $('#tabelPickMaster').DataTable().destroy();
      }
    } catch (e) {
      console.error('openPickMaster: gagal destroy DataTable sebelumnya', e);
    }

    $("#tabelPickMaster_header").html("");
    $("#tabelPickMaster_data").html('<tr><td>' + loadingHtml('Memuat data...') + '</td></tr>');

    $('#modalPickMasterBackdrop').addClass('show');
    $('#modalPickMaster').addClass('show').attr('aria-hidden', 'false');

    $.ajax({
      url: url,
      type: 'get',
      success: function (res) { renderPickMaster(res); },
      error: function () {
        $("#tabelPickMaster_data").html('<tr><td class="text-center">Gagal memuat data.</td></tr>');
      }
    });
  }

  function closePickMaster() {
    $('#modalPickMaster').removeClass('show').attr('aria-hidden', 'true');
    $('#modalPickMasterBackdrop').removeClass('show');
  }

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('#modalPickMaster').hasClass('show')) {
      closePickMaster();
    }
  });

  function renderPickMaster(res) {
    const kolom = (res && res.kolom) || [];
    const rows = (res && res.table) || [];

    let headHtml = '<tr>';
    kolom.forEach(function (k) { headHtml += '<th class="text-center">' + k[1] + '</th>'; });
    headHtml += '<th class="text-center">Actions</th></tr>';
    $("#tabelPickMaster_header").html(headHtml);

    let bodyHtml = '';
    if (rows.length) {
      rows.forEach(function (item) {
        bodyHtml += '<tr>';
        kolom.forEach(function (k) {
          let val;
          if (k[2] === 'date') { val = format_date(item[k[0]]); }
          else if (k[2] === 'float') { val = format_number(currencyNormalizer(item[k[0]]), k[3]); }
          else { val = nullToEmpty(item[k[0]]); }
          bodyHtml += '<td>' + val + '</td>';
        });
        const kode = kolom.length ? item[kolom[0][0]] : '';
        bodyHtml += '<td class="text-center">' +
          '<button type="button" class="btn btn-primary btn-sm" onclick="pickMasterSelect(\'' + String(kode).replace(/'/g, "\\'") + '\')"><i class="bi bi-plus-lg"></i></button>' +
          '</td></tr>';
      });
    } else {
      bodyHtml = '<tr><td colspan="' + (kolom.length + 1) + '" class="text-center">Tidak ada data ditemukan</td></tr>';
    }
    $("#tabelPickMaster_data").html(bodyHtml);

    try {
      $('#tabelPickMaster').DataTable({
        lengthChange: false,
        paging: rows.length > 10
      });
    } catch (e) {
      console.error('renderPickMaster: gagal inisialisasi DataTable', e);
    }
  }

  function pickMasterSelect(kode) {
    if (pickerTargetInput) { $('#' + pickerTargetInput).val(kode); }
    closePickMaster();
  }

  let defaultBulan = new Date().getMonth() + 1;
  let defaultTahun = new Date().getFullYear();
  const NAMA_BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

  function populatePeriodSelectors() {
    const selB = document.getElementById('periodBulan');
    const selT = document.getElementById('periodTahun');
    if (!selB || !selT) return;

    selB.innerHTML = NAMA_BULAN.map((nama, i) =>
      `<option value="${i + 1}" ${(i + 1) == defaultBulan ? 'selected' : ''}>${nama}</option>`).join('');
    const thisYear = new Date().getFullYear();
    let years = '';
    for (let y = thisYear; y >= thisYear - 6; y--) {
      years += `<option value="${y}" ${y == defaultTahun ? 'selected' : ''}>${y}</option>`;
    }
    selT.innerHTML = years;
    changePeriodParts();
  }

  // Bulan/Tahun -> gabung ke #inputDate1 format "YYYY-MM"
  function changePeriodParts() {
    const selB = document.getElementById('periodBulan');
    const selT = document.getElementById('periodTahun');
    if (!selB || !selT) return;
    defaultBulan = parseInt(selB.value, 10);
    defaultTahun = parseInt(selT.value, 10);
    const mm = String(defaultBulan).padStart(2, '0');
    $('#inputDate1').val(defaultTahun + '-' + mm);
  }

  $(document).ready(function() {

    if ("{!! $mode_menu !!}" == "QTY") {
      g_modeReport = modereport_qty;
      reportTitle = "LAPORAN STOK QUANTITY";
    } else if ("{!! $mode_menu !!}" == "RP") {
      g_modeReport = modereport_rp;
      reportTitle = "LAPORAN STOK BULANAN DALAM RUPIAH";
    } else if ("{!! $mode_menu !!}" == "QTYRP") {
      g_modeReport = modereport_qtyrp;
      reportTitle = "LAPORAN STOK BULANAN QTY+RUPIAH";
    } else {
      g_modeReport = modereport_periode;
      reportTitle = "LAPORAN STOK BULANAN QTY+RUPIAH";
    }

    // Nilai Stok Total cuma relevan kalau ada nilai Rupiah-nya (RP / QTYRP / PERIODE).
    if (g_modeReport == modereport_qty) {
      $('#kpiNilaiStokCard').hide();
      $('#kpiGrid').css('grid-template-columns', 'repeat(3, 1fr)');
    } else {
      $('#kpiNilaiStokCard').show();
      $('#kpiGrid').css('grid-template-columns', 'repeat(4, 1fr)');
    }

    $("#gButtonCustomizeTable").hide();

    $("#btnCustomizeTable").on("click", function() {
      if (typeof doShowFormCustomizeTable === "function") doShowFormCustomizeTable();
      else alert(" Fungsi doShowFormCustomizeTable belum tersedia.");
    });

    $("#btnSubmitReport").on("click", function() {
      makeTable('REPORT');
    });

    populatePeriodSelectors();
    setAgen(globalAgen);
    setDefaultHeader();

    // setTimeout(() => {
    //   makeTable('REPORT');
    // }, 100);
  });

  function setAgen (val) {
    globalAgen = val;
    $('#inputAgenSelect').val(val);
  }

  function setDefaultHeader() {
    if (g_modeReport == modereport_qty) {
      gcart_header = [
        ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0], ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Sat', 1, 'varchar', 0, 0], ['KODEGDG', 'Gdg', 1, 'varchar', 0, 0],
        ['QntAwal', 'Awal', 1, 'float', 1, 2], ['QNTPBL', 'Beli', 1, 'float', 1, 2],
        ['QNTRPJ', 'R. Jual', 1, 'float', 1, 2], ['QNTADI', 'Adj (+)', 1, 'float', 1, 2],
        ['QNTTRI', 'Tr (+)', 1, 'float', 1, 2], ['QNTRPK', 'RPM (+)', 1, 'float', 1, 2],
        ['QntHPrd', 'PRD (+)', 1, 'float', 1, 2], ['QNTPNJ', 'Jual', 1, 'float', 1, 2],
        ['QNTRBP', 'R.Beli', 1, 'float', 1, 2], ['QNTADO', 'Adj (-)', 1, 'float', 1, 2],
        ['QNTTRO', 'Tr (-)', 1, 'float', 1, 2], ['QNTPMK', 'PMK (-)', 1, 'float', 1, 2],
        ['SALDOQNT', 'Akhir', 1, 'float', 1, 2]
      ];
    } else if (g_modeReport == modereport_rp) {
      gcart_header = [
        ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0], ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Sat', 1, 'varchar', 0, 0], ['KODEGDG', 'Gdg', 1, 'varchar', 0, 0],
        ['HRGAWAL', 'Awal', 1, 'float', 1, 0], ['HRGPBL', 'Beli', 1, 'float', 1, 0],
        ['HRGRPJ', 'R. Jual', 1, 'float', 1, 0], ['HRGADI', 'Adj (+)', 1, 'float', 1, 0],
        ['HRGTRI', 'Tr (+)', 1, 'float', 1, 0], ['HRGRPK', 'RPM (+)', 1, 'float', 1, 0],
        ['HRGHPrd', 'PRD (+)', 1, 'float', 1, 0], ['HRGPNJ', 'Jual', 1, 'float', 1, 0],
        ['HRGRBP', 'R.Beli', 1, 'float', 1, 0], ['HRGADO', 'Adj (-)', 1, 'float', 1, 0],
        ['HRGTRO', 'Tr (-)', 1, 'float', 1, 0], ['HRGPMK', 'PMK (-)', 1, 'float', 1, 0],
        ['SALDORP', 'Akhir', 1, 'float', 1, 0]
      ];
    } else if (g_modeReport == modereport_qtyrp || g_modeReport == modereport_periode) {
      gcart_header = [
          ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0], ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
          ['partNumber', 'Part Number', 1, 'varchar', 0, 0], ['NAMAMERK', 'Merk', 1, 'varchar', 0, 0],
          ['KODEGDG', 'GD', 1, 'varchar', 0, 0], ['Satuan', 'Sat', 1, 'varchar', 0, 0],
          ['QntAwal', 'So. Awal', 1, 'float', 1, 2], ['HRGAWAL', 'So. Awal', 1, 'float', 1, 0],
          ['QNTPBL', 'Pembelian', 1, 'float', 1, 2], ['HRGPBL', 'Pembelian', 1, 'float', 1, 0],
          ['QNTRPJ', 'Retur Jual', 1, 'float', 1, 2], ['HRGRPJ', 'Retur Jual', 1, 'float', 1, 0],
          ['QNTADI', 'Kor. Msk', 1, 'float', 1, 2], ['HRGADI', 'Kor. Msk', 1, 'float', 1, 0],
          ['QNTTRI', 'Trans. Msk', 1, 'float', 1, 2], ['HRGTRI', 'Trans. Msk', 1, 'float', 1, 0],
          ['QNTRPK', 'R. Pemakaian', 1, 'float', 1, 2], ['HRGRPK', 'R. Pemakaian', 1, 'float', 1, 0],
          ['QNTUKI', 'Ubah Kemasan In', 1, 'float', 1, 2], ['HRGUKI', 'Ubah Kemasan In', 1, 'float', 1, 0],
          ['qntrspb', 'Terima dr R.Sjln', 1, 'float', 1, 2], ['hrgrspb', 'Terima dr R.Sjln', 1, 'float', 1, 0],
          ['QntHPrd', 'Gd TC dr SJ', 1, 'float', 1, 2], ['HRGHPrd', 'Gd TC dr SJ', 1, 'float', 1, 0],
          ['QNTPNJ', 'S.Jalan', 1, 'float', 1, 2], ['HRGPNJ', 'S.Jalan', 1, 'float', 1, 0],
          ['qntrgtc', 'Retur Sjln dr GTC', 1, 'float', 1, 2], ['hrgrgtc', 'Retur Sjln dr GTC', 1, 'float', 1, 0],
          ['QNTPRJ', 'HPP', 1, 'float', 1, 2], ['HRGPRJ', 'HPP', 1, 'float', 1, 0],
          ['QNTRBP', 'Retur Beli', 1, 'float', 1, 2], ['HRGRBP', 'Retur  Beli', 1, 'float', 1, 0],
          ['QNTADO', 'Kor. Klr', 1, 'float', 1, 2], ['HRGADO', 'Kor. Klr', 1, 'float', 1, 0],
          ['QNTTRO', 'Trans. Klr', 1, 'float', 1, 2], ['HRGTRO', 'Trans. Klr', 1, 'float', 1, 0],
          ['QNTUKO', 'Ubah Kemasan Out', 1, 'float', 1, 2], ['HRGUKO', 'Ubah Kemasan Out', 1, 'float', 1, 0],
          ['QNTPMK', 'Pemakaian', 1, 'float', 1, 2], ['HRGPMK', 'Pemakaian', 1, 'float', 1, 0],
          ['SALDOQNT', 'So. Akhir', 1, 'float', 1, 2], ['SALDORP', 'So. Akhir', 1, 'float', 1, 0]
      ];
    }
    gsum_issubtotal = 1; gsum_isgrandtotal = 1;
  }

  function setRowHeader(_rowHeader) {
    if (g_modeReport == modereport_qty || g_modeReport == modereport_rp) {
      return setRowHeaderQtyOrRp(_rowHeader);
    } else {
      return setRowHeaderQtyRp(_rowHeader);
    }
  }

  function setRowHeaderQtyOrRp(_rowHeader) {
    _rowHeader += '<tr>';
    _rowHeader += '  <th scope="col" class="text-start">Kode Barang</th>';
    _rowHeader += '  <th scope="col" class="text-start">Nama Barang</th>';
    _rowHeader += '  <th scope="col" class="text-center">Sat</th>';
    _rowHeader += '  <th scope="col" class="text-center">Gdg</th>';
    _rowHeader += '  <th scope="col" class="text-end">Awal</th>';
    _rowHeader += '  <th scope="col" class="text-end">Beli</th>';
    _rowHeader += '  <th scope="col" class="text-end">R. Jual</th>';
    _rowHeader += '  <th scope="col" class="text-end">Adj (+)</th>';
    _rowHeader += '  <th scope="col" class="text-end">Tr (+)</th>';
    _rowHeader += '  <th scope="col" class="text-end">RPM (+)</th>';
    _rowHeader += '  <th scope="col" class="text-end">PRD (+)</th>';
    _rowHeader += '  <th scope="col" class="text-end">Jual</th>';
    _rowHeader += '  <th scope="col" class="text-end">R.Beli</th>';
    _rowHeader += '  <th scope="col" class="text-end">Adj (-)</th>';
    _rowHeader += '  <th scope="col" class="text-end">Tr (-)</th>';
    _rowHeader += '  <th scope="col" class="text-end">PMK (-)</th>';
    _rowHeader += '  <th scope="col" class="text-end">Akhir</th>';
    _rowHeader += '</tr>';
    return _rowHeader;
  }

  function setRowHeaderQtyRp(_rowHeader) {
    let _thopen = "", _thclose = "</th>";
    _rowHeader += '<tr>';
    _thopen = '<th rowspan="3" scope="col" class="text-start">';
    _rowHeader += _thopen + 'Kode Barang' + _thclose;
    _rowHeader += _thopen + 'Nama Barang' + _thclose;
    _rowHeader += _thopen + 'Part Number' + _thclose;
    _rowHeader += _thopen + 'Merk' + _thclose;
    _rowHeader += _thopen + 'Gdg' + _thclose;
    _rowHeader += _thopen + 'Sat' + _thclose;
    _rowHeader += '<th colspan="2" rowspan="2" class="text-center">So. Awal</th>';
    _rowHeader += '<th colspan="16" class="text-center">Masuk</th>';
    _rowHeader += '<th colspan="16" class="text-center">Keluar</th>';
    _rowHeader += '<th colspan="2" rowspan="2" class="text-center">So. Akhir</th>';
    _rowHeader += '</tr>';

    _rowHeader += '<tr>';
    const masukKeluarLabels = [
        'Pembelian', 'Retur Jual', 'Kor. Msk', 'Trans. Msk',
        'R. Pemakaian', 'Ubah Kemasan In', 'Terima dr R.Sjln', 'Gd TC dr SJ',
        'S. Jalan', 'Retur Sjln dr GTC', 'HPP', 'Retur Beli',
        'Kor. Klr', 'Trans. Klr', 'Ubah Kemasan Out', 'Pemakaian'
    ];
    for (let i = 0; i < masukKeluarLabels.length; i++) {
        _rowHeader += `<th colspan="2" scope="col" class="text-center">${masukKeluarLabels[i]}</th>`;
    }
    _rowHeader += '</tr>';

    _rowHeader += '<tr>';
    let _qtyrp = '<th scope="col" class="text-end">Qty</th><th scope="col" class="text-end">Rp.</th>';
    _rowHeader += _qtyrp.repeat(1 + 8 + 8 + 1);
    _rowHeader += '</tr>';
    return _rowHeader;
  }

  function getRowFooter1(_col) {
    let _sum = gcart_res.reduce((sum, item) => sum + currencyNormalizer(item[_col]), 0);
    let _decimal = (gcart_header.find(row => row[0] === _col) || [])[5];
    return '  <td class="text-end fw-bold">' + format_number(_sum, _decimal) + '</td>';
  }

  function getRowFooter2(_col, _colspanRow2) {
    let _sum = gcart_res.filter(item => currencyNormalizer(item[_col]) !== 0).length;
    let _str = '  <td colspan="' + _colspanRow2 + '" class="text-end fw-bold">' + _sum + '</td>'
    return { _sum, _str };
  }

  function setRowFooter() {
    let tot_masuk = 0, tot_keluar = 0, tos = 0;

    const kolomMasuk = ["QntAwal", "QNTPBL", "QNTRPJ", "QNTADI", "QNTTRI", "QNTRPK", "QntHPrd"];
    const kolomKeluar = ["QNTPNJ", "QNTADO", "QNTTRO", "QNTPMK"];
    if (g_modeReport == modereport_qtyrp || g_modeReport == modereport_periode) {
      kolomMasuk.push("QNTUKI", "qntrspb", "qntrgtc", "QNTUKO");
      kolomKeluar.push("QNTPRJ");
    }

    kolomMasuk.forEach((col) => { tot_masuk += getRowFooter2(col, 1)._sum; });
    kolomKeluar.forEach((col) => { tot_keluar += getRowFooter2(col, 1)._sum; });

    tot_masuk = (tot_masuk !== 0) ? tot_masuk : 1;
    tos = format_number((tot_keluar / tot_masuk) * 100, 2);
    tot_masuk = (gcart_res.length != 0) ? tot_masuk : 0;

    try { updateKpiWidgetsFromRes(gcart_res, tot_masuk, tot_keluar, tos); } catch(e){}

    return "";
  }

  function makeTable (_mode) {
    let groupby = "KODEBRG";
    let _date1  = $("#inputDate1").val();
    let _date2  = (g_modeReport == modereport_periode) ? $("#inputDate2").val() : null;

    let temp_href = g_href;
    g_href = 'laporanstockmutasistock';

    let data = {
      date1            : _date1,
      inputGudang      : $("#inputGudang").val(),
      inputIsi         : $("#inputIsi").val(),
      inputStockMinus  : $('#inputStockMinus').prop('checked') ? 1 : 0,
      inputGrup        : $("#inputGrup").val(),
      inputKategori    : $("#inputKategori").val(),
      inputSubKategori : $("#inputSubKategori").val(),
      inputMerk        : $("#inputMerk").val(),
      inputJenis       : globalAgen,
      date2            : _date2,
      modeMenu         : g_modeReport,
    };

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    doMakeTable(_mode, groupby, data, reportTitle, _date1);

    $('#tabel_header tr').slice(0, 4).remove();

    setTimeout(() => {

      let footerMsg = gcart_res && gcart_res.length > 0 ? "Menampilkan " + gcart_res.length + " baris" : "Belum ada data dimuat";
      document.getElementById('footerLabel').textContent = footerMsg;

      buildCharts(gcart_res || []);
      relabelSubtotalRows();
    }, 500);

    g_href = temp_href;
  }

  function relabelSubtotalRows() {
    $('#tabel tr[id^="strow"] td.st').each(function () {
      const $cell = $(this);
      $cell.html($cell.html().replace(/Total\s*:/i, 'Subtotal :'));
    });
  }

  function getKolomFilter() { return ['KODEBRG', 'NAMABRG']; }

  function updateKpiWidgetsFromRes(resData, totMasuk, totKeluar, tosPercentage) {
    if(!resData || !resData.length) {
      $('#kpiTotalItem').text('0'); $('#kpiNilaiStok').text('0');
      $('#kpiStokMenipis').text('0'); $('#kpiTOS').text('0 %'); return;
    }
    let totalStokRp = 0; let stokMenipis = 0;
    resData.forEach(r => {
      if(r.SALDORP) totalStokRp += currencyNormalizer(r.SALDORP);
      else if(r.SALDOQNT && r.HRGAWAL) totalStokRp += (currencyNormalizer(r.SALDOQNT) * currencyNormalizer(r.HRGAWAL));
      if(r.SALDOQNT && currencyNormalizer(r.SALDOQNT) > 0 && currencyNormalizer(r.SALDOQNT) <= 10) stokMenipis++;
    });
    $('#kpiTotalItem').text(format_number(resData.length, 0));
    $('#kpiNilaiStok').text(format_number(totalStokRp, 0));
    $('#kpiStokMenipis').text(format_number(stokMenipis, 0));
    $('#kpiTOS').text(tosPercentage + ' %');
  }

  /* -- CHARTS (Chart.js v4)
     Kiri : Penjualan Terbanyak (Qty) Top 10 barang berdasar kolom JUAL
     Kanan : Beli vs Jual (Qty) rekap KESELURUHAN data 1 bulan itu -- */
  const CHART_PALETTE = ['#4F46E5','#7C3AED','#DB2777','#2563eb','#16a34a','#ca8a04','#ea580c','#0891b2','#e11d48','#65a30d'];
  let _charts = {};

  function fmtShort(v) {
    v = Math.round(num(v)); const a = Math.abs(v);
    if (a >= 1e9) return (v / 1e9).toFixed(1).replace(/\.0$/, '') + ' M';
    if (a >= 1e6) return (v / 1e6).toFixed(1).replace(/\.0$/, '') + ' jt';
    if (a >= 1e3) return (v / 1e3).toFixed(0) + ' rb';
    return String(v);
  }
  function num(v) { if (v === null || v === undefined || v === '') return 0; const n = parseFloat(v); return isNaN(n) ? 0 : n; }
  function _destroyChart(id) { if (_charts[id]) { _charts[id].destroy(); delete _charts[id]; } }

  function pickCIChart(r, key) {
    if (r[key] !== undefined) return r[key];
    const lk = String(key).toLowerCase();
    for (const k in r) { if (k.toLowerCase() === lk) return r[k]; }
    return undefined;
  }

  function _chartMsg(canvasId, msg) {
    const cv = document.getElementById(canvasId);
    if (!cv) return;
    const holder = cv.parentElement;
    if (holder) holder.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;font-size:12.5px;text-align:center;padding:0 20px">' + msg + '</div>';
  }

  function buildCharts(rows) {
    const holders = document.querySelectorAll('#chartGrid .chart-holder');
    if (holders[0] && !document.getElementById('topJualChart')) holders[0].innerHTML = '<canvas id="topJualChart"></canvas>';
    if (holders[1] && !document.getElementById('beliJualChart')) holders[1].innerHTML = '<canvas id="beliJualChart"></canvas>';

    if (typeof Chart === 'undefined') {
      _chartMsg('topJualChart', 'Chart.js gagal dimuat. Cek path <code>public/plugins/chart.js/chart.umd.min.js</code>.');
      _chartMsg('beliJualChart', 'Chart.js gagal dimuat. Cek path <code>public/plugins/chart.js/chart.umd.min.js</code>.');
      return;
    }
    if (!rows || !rows.length) {
      _chartMsg('topJualChart', 'Belum ada data untuk grafik.');
      _chartMsg('beliJualChart', 'Belum ada data untuk grafik.');
      return;
    }
    try {
      Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
      Chart.defaults.font.size = 12;
      Chart.defaults.color = '#64748b';

      const order = [], jualByItem = {}, namaByItem = {};
      rows.forEach(r => {
        const kode = String(pickCIChart(r, 'KODEBRG') || '').trim();
        if (!kode) return;
        if (!(kode in jualByItem)) { jualByItem[kode] = 0; namaByItem[kode] = pickCIChart(r, 'NAMABRG') || kode; order.push(kode); }
        jualByItem[kode] += num(pickCIChart(r, 'QNTPNJ'));
      });

      // Top 10 barang dgn Qty Jual paling banyak
      const top = order.map(k => [k, jualByItem[k]]).sort((a, b) => b[1] - a[1]).slice(0, 10);
      const topLabels = top.map(t => namaByItem[t[0]]);

      const cvTop = document.getElementById('topJualChart');
      if (!top.length || !cvTop) {
        _chartMsg('topJualChart', 'Belum ada data penjualan (Qty) pada periode ini.');
      } else {
        _destroyChart('topJual');
        _charts.topJual = new Chart(cvTop, {
          type: 'bar',
          data: {
            labels: topLabels,
            datasets: [{
              label: 'Jual (Qty)',
              data: top.map(t => t[1]),
              backgroundColor: top.map((t, i) => CHART_PALETTE[i % CHART_PALETTE.length]),
              borderRadius: 6
            }]
          },
          options: {
            responsive: true, maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
              legend: { display: false },
              tooltip: { callbacks: { label: (c) => ' ' + fmtShort(c.parsed.x) } }
            },
            scales: { x: { ticks: { callback: (v) => fmtShort(v) } } }
          }
        });
      }

      // Beli vs Jual (Qty) KESELURUHAN 1 bulan
      const gdgOrder = [], beliByGdg = {}, jualByGdg = {};
      rows.forEach(r => {
        const gdg = String(pickCIChart(r, 'KODEGDG') || '-').trim() || '-';
        if (!(gdg in beliByGdg)) { beliByGdg[gdg] = 0; jualByGdg[gdg] = 0; gdgOrder.push(gdg); }
        beliByGdg[gdg] += num(pickCIChart(r, 'QNTPBL'));
        jualByGdg[gdg] += num(pickCIChart(r, 'QNTPNJ'));
      });

      let gdgLabels, beliData, jualData;
      if (gdgOrder.length > 1) {
        gdgLabels = gdgOrder;
        beliData = gdgOrder.map(g => beliByGdg[g]);
        jualData = gdgOrder.map(g => jualByGdg[g]);
      } else {
        const totBeli = gdgOrder.reduce((s, g) => s + beliByGdg[g], 0);
        const totJual = gdgOrder.reduce((s, g) => s + jualByGdg[g], 0);
        gdgLabels = ['Total Bulan Ini'];
        beliData = [totBeli];
        jualData = [totJual];
      }

      const cvBJ = document.getElementById('beliJualChart');
      if (!cvBJ) {
        _chartMsg('beliJualChart', 'Belum ada data Beli/Jual pada periode ini.');
      } else {
        _destroyChart('beliJual');
        _charts.beliJual = new Chart(cvBJ, {
          type: 'line',
          data: {
            labels: gdgLabels,
            datasets: [
              {
                label: 'Beli (Qty)',
                data: beliData,
                borderColor: '#16a34a', backgroundColor: '#16a34a',
                tension: .3, fill: false, pointRadius: 5
              },
              {
                label: 'Jual (Qty)',
                data: jualData,
                borderColor: '#DB2777', backgroundColor: '#DB2777',
                tension: .3, fill: false, pointRadius: 5
              }
            ]
          },
          options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
              legend: { position: 'top' },
              tooltip: { callbacks: { label: (c) => ' ' + c.dataset.label + ': ' + fmtShort(c.parsed.y) } }
            },
            scales: {
              x: { ticks: { maxRotation: 40, minRotation: 0, autoSkip: false, font: { size: 10 } } },
              y: { ticks: { callback: (v) => fmtShort(v) } }
            }
          }
        });
      }
    } catch (e) {
      console.error('buildCharts', e);
      _chartMsg('topJualChart', 'Gagal menampilkan grafik (lihat console).');
      _chartMsg('beliJualChart', 'Gagal menampilkan grafik (lihat console).');
    }
  }

  function applyFilters() {
    if (typeof gcart_res === 'undefined' || !gcart_res.length) return;
    const term = ($('#searchBox2').val() || '').trim().toLowerCase();
    if (!term) { doRenderTable(gcart_res, "KODEBRG"); relabelSubtotalRows(); return; }
    const cols = gcart_header.filter(c => c[2] === 1);
    const filtered = gcart_res.filter(r => cols.map(c => String(r[c[0]] || '')).join(' ').toLowerCase().indexOf(term) !== -1);
    doRenderTable(filtered, "KODEBRG");
    relabelSubtotalRows();
  }

  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });
  function doExport(fmt) {
    document.getElementById('exportDrop').classList.remove('open');
    if (fmt === 'Print') { window.print(); return; }
    if (typeof gcart_res !== 'undefined') {
      const cols = gcart_header.filter(c => c[2] === 1);
      const header = cols.map(c => c[1]);
      const body = gcart_res.map(r => cols.map(c => c[3] === 'float' || c[3] === 'int' ? currencyNormalizer(r[c[0]]) : (r[c[0]] || '')));
      const csv = [header].concat(body).map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
      const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
      const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
      a.download = 'LaporanMutasiStok.' + (fmt === 'Excel' ? 'xls' : 'csv');
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
    }
  }
</script>
@endsection
