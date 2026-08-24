@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php).
     Piutang Umur (Analisa Umur Piutang): styled .tb-report, per-nota rows dikelompokkan per pelanggan
     (Nama) → 1 baris per pelanggan (subtotal Saldo Akhir & bucket umur Belum JT/1-30/31-60/61-90/>90).
     Di atas tabel: kartu KPI (Total, Belum JT, JT <30, Overdue >30) + chart Aging Piutang (doughnut) +
     Piutang per Pelanggan Top 10 (bar). Mode IDR / $ (valas). Klik baris pelanggan → rincian nota di
     panel kanan; kolom No Nota → Faktur Penjualan (INVC) di panel bawah (report-table.js). --}}

<!-- Chart.js v4 (di-bundle lokal: public/plugins/chart.js/chart.umd.min.js) -->
<script src="{!! URL::asset('plugins/chart.js/chart.umd.min.js') !!}?v={{ @filemtime(base_path('public/plugins/chart.js/chart.umd.min.js')) ?: '1' }}"></script>

<style>
  #inputReportMode, #inputPerkiraanBtn {
    border: 0; background: none; padding: 0; box-shadow: none;
    color: #495057; font-weight: 600;
  }
  #inputReportMode:hover, #inputReportMode:focus,
  #inputPerkiraanBtn:hover, #inputPerkiraanBtn:focus { color: #0d6efd; box-shadow: none; }

  /* tinggi awal area tabel supaya dropdown tidak terpotong container pendek */
  .tb-report .table-wrap { min-height: 10vh; }

  /* ── KPI cards ── */
  .tb-report .kpi-strip {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px;
  }
  @media (max-width: 900px) { .tb-report .kpi-strip { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 560px) { .tb-report .kpi-strip { grid-template-columns: 1fr; } }
  .tb-report .kpi-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 18px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06); display: flex; align-items: flex-start; gap: 12px;
  }
  .tb-report .kpi-ic {
    width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center;
    justify-content: center; flex-shrink: 0; font-size: 18px;
  }
  .tb-report .kpi-label { font-size: 12px; color: #64748b; margin-bottom: 4px; }
  .tb-report .kpi-val { font-size: 19px; font-weight: 700; color: #1e293b; }

  /* ── Chart section ── */
  .tb-report .chart-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;
  }
  @media (max-width: 900px) { .tb-report .chart-grid { grid-template-columns: 1fr; } }
  .tb-report .chart-box {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 16px 20px; box-shadow: 0 1px 4px rgba(0,0,0,.06);
  }
  .tb-report .chart-box h3 {
    font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 12px;
  }
  .tb-report .chart-holder { position: relative; height: 260px; }
  .tb-report .chart-holder canvas { max-height: 260px; }
</style>

@section('header2')
<div class="tb-report main">
  <div class="content">

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div>
        <div class="page-title">Analisa Umur Piutang</div>
      </div>

      <!-- Periode (per tanggal) -->
      <div class="filter-wrap">
        <label>Per Tanggal</label>
        <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
      </div>

      <!-- Mode Valas (IDR / $) -->
      <div class="filter-wrap">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputReportMode"
                data-bs-toggle="dropdown" aria-expanded="false">Valas: <span id="reportModeLabel">IDR</span></button>
        <ul class="dropdown-menu" id="dropdownReportMode" aria-labelledby="inputReportMode">
          <li><a class="dropdown-item" style="cursor:pointer" data-value="IDR" onclick="setReportMode('IDR')">IDR
            <span class="checkmark-red" style="display:none">&#10003;</span></a></li>
          <li><a class="dropdown-item" style="cursor:pointer" data-value="$" onclick="setReportMode('$')">$
            <span class="checkmark-red" style="display:none">&#10003;</span></a></li>
        </ul>
      </div>

      <!-- Valas picker (hanya mode $) -->
      <div class="filter-wrap" id="valas_container" style="display:none;">
        <label>Kurs Valas</label>
        <input type="text" id="valas_display" class="filter-inp" style="width:80px" readonly placeholder="Pilih">
        <button type="button" class="btn-pick" onclick="buttonSelectValas()" title="Pilih Valas">+</button>
      </div>
      <input type="hidden" id="valas_value" value="IDR">

      <!-- Perkiraan (dropdown; akun PT) -->
      <div class="filter-wrap">
        <label>Perkiraan</label>
        <input type="hidden" id="inputPerkiraan" value="-">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputPerkiraanBtn"
                data-bs-toggle="dropdown" aria-expanded="false"><span id="perkiraanLabel">-</span></button>
        <ul class="dropdown-menu" id="dropdownPerkiraan" aria-labelledby="inputPerkiraanBtn"
            style="max-height:320px; overflow:auto;"></ul>
      </div>

      <!-- Pelanggan Awal (modal — data banyak) -->
      <div class="filter-wrap">
        <label>Plgn Awal</label>
        <input type="text" class="filter-inp" id="inputSuppAwal" style="width:90px" value="-" readonly>
        <button type="button" class="btn-pick" onclick="buttonSelectSuppAwal()" title="Pilih Pelanggan Awal">+</button>
      </div>

      <!-- Pelanggan Akhir (modal — data banyak) -->
      <div class="filter-wrap">
        <label>Plgn Akhir</label>
        <input type="text" class="filter-inp" id="inputSuppAkhir" style="width:90px" value="-" readonly>
        <button type="button" class="btn-pick" onclick="buttonSelectSuppAkhir()" title="Pilih Pelanggan Akhir">+</button>
      </div>

      <!-- Order By dihapus — selalu default 0 (urut tanggal) -->
      <input type="hidden" id="inputOrd" value="0">

      <!-- Actions: search + customize + tampilkan + export -->
      <div class="action-group">
        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:160px">
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

    <!-- KPI CARDS -->
    <div class="kpi-strip" id="kpiStrip"></div>

    <!-- CHARTS (dibangun sisi-klien dari data yang dimuat) -->
    <div class="chart-grid" id="chartGrid">
      <div class="chart-box">
        <h3>Aging Piutang</h3>
        <div class="chart-holder"><canvas id="agingChart"></canvas></div>
      </div>
      <div class="chart-box">
        <h3>Piutang per Pelanggan (Top 10)</h3>
        <div class="chart-holder"><canvas id="topCustomerChart"></canvas></div>
      </div>
    </div>

    <!-- Petunjuk drill -->
    <div style="font-size:12px;color:var(--muted);margin:-6px 0 10px 2px">
      <i class="bi bi-lightbulb-fill text-warning"></i>
      Klik baris pelanggan untuk melihat rincian nota, lalu klik nota untuk membuka Faktur Penjualan.
    </div>

    <!-- TABLE (1 baris per pelanggan + subtotal; rincian nota di panel kanan saat baris diklik) -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead>
            <tr><th>Pelanggan</th></tr>
          </thead>
          <tbody id="tableBody">
            <tr class="empty-row"><td>Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
          </tbody>
        </table>
      </div>
      <div class="table-footer">
        <span id="footerLabel">Belum ada data dimuat</span>
      </div>
    </div>

  </div><!-- /content -->

  <!-- DRILL OVERLAY + PANEL (rincian nota per pelanggan, geser dari kanan) -->
  <div class="drill-overlay" id="drillOverlay" onclick="closeDrill()"></div>
  <div class="drill-panel" id="drillPanel">
    <div class="dp-header">
      <div>
        <div class="dp-title" id="dpTitle">-</div>
        <div class="dp-sub" id="dpSub">-</div>
      </div>
      <div class="dp-close" onclick="closeDrill()"><i class="bi bi-x"></i></div>
    </div>
    <div class="dp-meta" id="dpMeta"></div>
    <div class="dp-body" id="dpBody"></div>
  </div>

  <!-- TOAST -->
  <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>

  <!-- KAS/VOUCHER PANEL (muncul dari bawah; diisi report-table.js saat openVoucher) -->
  <div class="kas-panel" id="kasPanel">
    <div class="kas-head">
      <div class="kas-title" id="kasTitle">Voucher</div>
      <div class="dp-close" onclick="closeKasharian()"><i class="bi bi-x"></i></div>
    </div>
    <div class="kas-body" id="kasBody"></div>
  </div>

</div><!-- /tb-report -->

{{-- Modal-modal DILETAKKAN DI LUAR .tb-report supaya reset `.tb-report *{margin:0;padding:0}`
     di report-table.css tidak merusak padding/margin modal Bootstrap. --}}

<!-- modal select valas -->
<div class="modal fade rt-picker-v2" id="formSelectValas" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1000px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Valas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelSelectValas">
          <thead>
            <tr><th>Valas</th><th>Keterangan</th><th>Kurs</th></tr>
          </thead>
          <tbody id="tabel_dataSelectValas"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

<!-- modal select pelanggan awal -->
<div class="modal fade rt-picker-v2" id="formSelectSuppAwal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Pelanggan Awal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelSelectSuppAwal">
          <thead>
            <tr><th>Kode</th><th>Nama</th><th>Alamat</th><th>Telpon</th></tr>
          </thead>
          <tbody id="tabel_dataSelectSuppAwal"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

<!-- modal select pelanggan akhir -->
<div class="modal fade rt-picker-v2" id="formSelectSuppAkhir" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Pelanggan Akhir</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelSelectSuppAkhir">
          <thead>
            <tr><th>Kode</th><th>Nama</th><th>Alamat</th><th>Telpon</th></tr>
          </thead>
          <tbody id="tabel_dataSelectSuppAkhir"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
@endsection


@section('jsreport')
<script type="text/javascript">
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalOrderBy = "0";        // default: Tanggal
  let globalReportMode = "IDR";   // default: IDR

  let g_reportTitle = "";
  let g_inputPerkiraan = "";

  let lastRows = [];   // hasil fetch terakhir (dipakai render / search / chart / kpi)

  // Mode NUMERIK: DBSIMPANHEADER.reportmode itu kolom integer, jadi mode string membuat
  // header (termasuk toggle Subtotal/Grand Total) TIDAK tersimpan. Int scoped per href.
  var modereport_detail = 19, modereport_rekap = 20;
  g_modeReport = modereport_detail;
  var jenisreport = 0, DetOrRekap = 0;

  const reportUrl = "{{ url('reportaccountingpiutangumur_doReport') }}";

  // Bottom voucher panel endpoints (report-table.js is loaded via masterreport2).
  // No Nota (NoFaktur) → jenisFromNo → INVC → Faktur Penjualan (doInvoice).
  window.ReportTableConfig = {
    kasUrl    : "{{ url('reportaccountingpiutangumur_doKasharian') }}",
    invoiceUrl: "{{ url('reportaccountingpiutangumur_doInvoice') }}",
    lpbUrl    : "{{ url('reportaccountingpiutangumur_doLpb') }}",
    bpUrl     : "{{ url('reportaccountingpiutangumur_doBp') }}"
  };

  $(document).ready(function () {
    setReportMode(globalReportMode);   // set mode + muat gcart_header
    loadPerkiraanDropdown();           // isi dropdown Perkiraan (default akun PT pertama)

    // setTimeout(() => { makeTable('REPORT'); }, 100);
  });

  /* ── kolom (gcart_header) tabel induk (1 baris/pelanggan). Kolom uang (Saldo Akhir + bucket
        umur) visible=1 & total=1 → subtotal per pelanggan + Grand Total, bisa di-toggle Customize.
        Kolom per-nota (Nota/PO/Tgl/Over Due) hidden default (visible=0) → kosong (—) di baris
        pelanggan bila di-on-kan; nilai aslinya ada di panel rincian (drill). Bucket umur diwarnai. ── */
  function setDefaultHeader() {
    gcart_header = [
      ['NoFaktur', 'Nota', 0, 'varchar', 0, 0],
      ['pono', 'No PO', 0, 'varchar', 0, 0],
      ['tglNota', 'Tgl Invoice', 0, 'date', 0, 0],
      ['tanggal', 'Tgl Jatuh Tempo', 0, 'date', 0, 0],
      ['Umur', 'Over Due', 0, 'varchar', 0, 0],
      ['Saldo', 'Saldo Akhir', 1, 'float', 1, 2],
      ['Saldo0', 'Belum JT', 1, 'float', 1, 2],
      ['Saldo30', '1 - 30', 1, 'float', 1, 2],
      ['Saldo60', '31 - 60', 1, 'float', 1, 2],
      ['Saldo90', '61 - 90', 1, 'float', 1, 2],
      ['Saldo121', '>90', 1, 'float', 1, 2],
    ];
    gsum_issubtotal = 1; gsum_isgrandtotal = 1;
  }

  /* ── toolbar controls ── */
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
  }

  function setReportMode(val) {
    globalReportMode = val;
    $('#reportModeLabel').text(val === 'IDR' ? 'IDR' : '$');

    if (val === 'IDR') {
      jenisreport = 0; DetOrRekap = 0;
      $('#valas_value').val('IDR');
      $('#valas_container').hide();
      $('#valas_display').val('');
    } else {
      jenisreport = 1; DetOrRekap = 1;
      $('#valas_value').val('');
      $('#valas_container').show();
      $('#valas_display').val('');
    }

    $('#dropdownReportMode .checkmark-red').hide();
    $(`#dropdownReportMode .dropdown-item[data-value='${val}'] .checkmark-red`).show();

    setModeReport();
  }

  function setModeReport() {
    g_modeReport = (globalReportMode === 'IDR') ? modereport_detail : modereport_rekap;
    doSetHeader(g_modeReport);   // muat susunan kolom mode ini (default / kustomisasi tersimpan)
    doShowCustomize();
  }


  /* ── EXPORT ── */
  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });
  function doExport(fmt) {
    document.getElementById('exportDrop').classList.remove('open');
    if (fmt === 'Print') { window.print(); return; }
    exportDelimited(fmt);
  }
  function exportDelimited(fmt) {
    // Ekspor rincian per-nota (bukan hanya subtotal): Kode + Pelanggan + kolom DRILL_COLS.
    const header = ['Kode', 'Pelanggan'].concat(DRILL_COLS.map(c => c[1]));
    const body = (lastRows || []).map(r => [str(pickCI(r, 'Kode')), str(pickCI(r, 'Nama'))].concat(DRILL_COLS.map(function (c) {
      const v = pickCI(r, c[0]);
      if (c[2] === 'date') return format_date(v);
      if (c[2] === 'num') return currencyNormalizer(v);
      return (v == null ? '' : v);
    })));
    const rows = [header].concat(body);
    const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const ext = (fmt === 'Excel') ? 'xls' : 'csv';
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'PiutangUmur_' + (globalDate1 || '') + '.' + ext;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    showToast('📄', 'Data diekspor sebagai ' + fmt);
  }

  /* ── LOAD DATA (sp_ReportUmurPiutang; doReport mengembalikan JSON array) ── */
  function makeTable(_mode) {
    globalDate1 = $('#inputDate1').val();
    g_reportTitle = 'REPORT ACCOUNTING PIUTANG UMUR';

    let _perk   = $('#inputPerkiraan').val() || '-';
    let _suppAw = $('#inputSuppAwal').val()  || '-';
    let _suppAk = $('#inputSuppAkhir').val() || '-';
    let _ord    = $('#inputOrd').val();
    let _valas  = $('#valas_value').val();

    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    const data = {
      date1: globalDate1,
      inputSuppAwal: _suppAw, inputSuppAkhir: _suppAk,
      inputOrd: _ord, inputPerkiraan: _perk, valas_value: _valas,
      selectedCust: window.selectedCust ?? []      // filter multi-pelanggan (dipertahankan)
    };

    $.ajax({
      url: reportUrl, type: 'get', data: data,
      success: function (res) {
        lastRows = Array.isArray(res) ? res : ((res && res.res1) ? res.res1 : []);
        $('#searchBox2').val('');
        render();
      },
      error: function () { lastRows = []; render(); }
    });
  }

  /* ── helpers ── */
  function num(v) { if (v === null || v === undefined || v === '') return 0; const n = parseFloat(v); return isNaN(n) ? 0 : n; }
  function str(v) { return (v == null ? '' : String(v)).trim(); }
  function pickCI(r, key) {
    if (r[key] !== undefined) return r[key];
    const lk = String(key).toLowerCase();
    for (const k in r) { if (k.toLowerCase() === lk) return r[k]; }
    return undefined;
  }

  // No Nota clickable hanya untuk nomor voucher betulan (ada '/', bukan baris pembuka
  // "Saldo Awal"). Panel voucher dibuka via report-table.js (dispatch by Jenis).
  function isVoucherNo(v) {
    const s = str(v);
    if (!s || s.indexOf('/') === -1) return false;
    return s.toUpperCase().indexOf('SALDO AWAL') === -1;
  }
  function escapeJs(s) { return String(s == null ? '' : s).replace(/\\/g, '\\\\').replace(/'/g, "\\'"); }

  // Kolom rincian nota (drill) — selalu tetap; dibaca langsung dari baris mentah SP.
  const DRILL_COLS = [
    ['NoFaktur', 'Nota',            'voucher'],
    ['pono',     'No PO',           'text'],
    ['tglNota',  'Tgl Invoice',     'date'],
    ['tanggal',  'Tgl Jatuh Tempo', 'date'],
    ['Umur',     'Over Due',        'text'],
    ['Saldo',    'Saldo Akhir',     'num'],
    ['Saldo0',   'Belum JT',        'num'],
    ['Saldo30',  '1 - 30',          'num'],
    ['Saldo60',  '31 - 60',         'num'],
    ['Saldo90',  '61 - 90',         'num'],
    ['Saldo121', '>90',             'num'],
  ];

  // Warna bucket umur — SATU sumber dipakai header+kolom tabel induk, tabel drill, dan doughnut.
  // Belum JT (Saldo0) = tak jatuh tempo → hijau (sehat); makin lama makin merah.
  const AGING_COLORS = {
    Saldo0:   '#16a34a',
    Saldo30:  '#ca8a04',
    Saldo60:  '#ea580c',
    Saldo90:  '#dc2626',
    Saldo121: '#991b1b',
  };
  function agingStyle(key) { return AGING_COLORS[key] ? ' style="color:' + AGING_COLORS[key] + '"' : ''; }

  // Grup per pelanggan hasil render terakhir (dipakai openDrill by index).
  let custGroups = [];

  /* ── KPI ── */
  function renderKpi(rows) {
    let total = 0, belum = 0, jt30 = 0, overdue = 0;
    (rows || []).forEach(r => {
      total   += currencyNormalizer(pickCI(r, 'Saldo'));
      belum   += currencyNormalizer(pickCI(r, 'Saldo0'));
      jt30    += currencyNormalizer(pickCI(r, 'Saldo30'));
      overdue += currencyNormalizer(pickCI(r, 'Saldo60')) + currencyNormalizer(pickCI(r, 'Saldo90')) + currencyNormalizer(pickCI(r, 'Saldo121'));
    });
    const cards = [
      ['Total Piutang',      total,   '#4f46e5', '#ede9fe', 'bi-wallet2'],
      ['Belum Jatuh Tempo',  belum,   '#16a34a', '#dcfce7', 'bi-check-circle'],
      ['Jatuh Tempo <30 hr', jt30,    '#ca8a04', '#fef9c3', 'bi-clock-history'],
      ['Overdue >30 hr',     overdue, '#dc2626', '#fee2e2', 'bi-exclamation-triangle'],
    ];
    document.getElementById('kpiStrip').innerHTML = cards.map(c =>
      '<div class="kpi-card"><div class="kpi-ic" style="background:' + c[3] + ';color:' + c[2] + '"><i class="bi ' + c[4] + '"></i></div>' +
      '<div><div class="kpi-label">' + c[0] + '</div><div class="kpi-val">Rp ' + format_number(c[1], 0) + '</div></div></div>'
    ).join('');
  }

  /* ── RENDER: 1 BARIS per pelanggan (subtotal kolom uang) + Grand Total.
     Rincian nota tidak tampil inline — dibuka di panel kanan saat baris diklik. ── */
  function render() {
    const cols  = gcart_header.filter(c => c[2] === 1);   // kolom terlihat (Customize): uang + opsional per-nota
    const keys  = cols.filter(c => c[4] === 1).map(c => c[0]);   // hanya kolom uang yang di-subtotal
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');
    const search = ($('#searchBox2').val() || '').trim().toLowerCase();

    // HEADER: Pelanggan + kolom terlihat (uang rata-kanan; per-nota rata-kiri). Bucket umur diwarnai.
    thead.innerHTML = '<tr><th>Pelanggan</th>' +
      cols.map(c => '<th' + (c[4] === 1 ? ' class="num"' : '') + agingStyle(c[0]) + '>' + c[1] + '</th>').join('') + '</tr>';

    // kelompokkan per pelanggan (Nama), pertahankan urutan kemunculan + jumlahkan kolom uang
    const order = [], buckets = {};
    (lastRows || []).forEach(r => {
      const gkey = str(pickCI(r, 'Nama'));
      if (!(gkey in buckets)) { buckets[gkey] = []; order.push(gkey); }
      buckets[gkey].push(r);
    });

    custGroups = order.map(gkey => {
      const rows = buckets[gkey];
      const sums = {}; keys.forEach(k => sums[k] = 0);
      rows.forEach(r => keys.forEach(k => { sums[k] += currencyNormalizer(pickCI(r, k)); }));
      return { code: str(pickCI(rows[0], 'Kode')), name: (gkey !== '' ? gkey : '(Tanpa Nama)'), rows: rows, sums: sums };
    }).filter(g => !search || g.code.toLowerCase().indexOf(search) !== -1 || g.name.toLowerCase().indexOf(search) !== -1);

    if (!custGroups.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + (cols.length + 1) + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      renderKpi([]); buildCharts([]);
      return;
    }

    let html = '';
    const grand = {}; keys.forEach(k => grand[k] = 0);

    custGroups.forEach((g, idx) => {
      keys.forEach(k => grand[k] += g.sums[k]);
      const label = (g.code ? g.code + ' - ' : '') + g.name;
      html += '<tr class="data-row" title="Klik untuk melihat rincian nota ' + g.name + '" onclick="openDrill(' + idx + ')">' +
        '<td class="name">' + label +
        ' <span style="font-size:11px;font-weight:600;opacity:.6">(' + g.rows.length + ' nota)</span>' +
        '<span class="drill-hint"><i class="bi bi-arrow-right-short"></i> detail</span></td>' +
        cols.map(c => c[4] === 1
          ? '<td class="num"' + agingStyle(c[0]) + '>' + format_number(g.sums[c[0]], c[5]) + '</td>'
          : '<td style="color:var(--muted);text-align:center">—</td>').join('') +
        '</tr>';
    });

    html += '<tr class="grand-total"><td style="font-weight:800">GRAND TOTAL</td>' +
      cols.map(c => c[4] === 1
        ? '<td class="num"' + agingStyle(c[0]) + '>' + format_number(grand[c[0]], c[5]) + '</td>'
        : '<td></td>').join('') + '</tr>';

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + custGroups.length + ' pelanggan';

    // KPI + charts dari seluruh nota pelanggan yang tampil
    const shownRows = [];
    custGroups.forEach(g => g.rows.forEach(r => shownRows.push(r)));
    renderKpi(shownRows);
    buildCharts(shownRows);
  }

  /* ── DRILL: rincian nota per pelanggan (panel kanan). Data dari baris yang sudah dimuat
     (tanpa panggilan server tambahan). Klik baris nota → panel voucher (Faktur Penjualan). ── */
  function openDrill(idx) {
    const g = custGroups[idx];
    if (!g) return;

    document.getElementById('dpTitle').textContent = g.name;
    document.getElementById('dpSub').textContent = 'Kode: ' + (g.code || '-') + ' - ' + g.rows.length + ' nota';

    const metaDefs = [
      ['Saldo', 'Saldo Akhir'], ['Saldo0', 'Belum JT'], ['Saldo30', '1 - 30'],
      ['Saldo60', '31 - 60'], ['Saldo90', '61 - 90'], ['Saldo121', '>90'],
    ];
    document.getElementById('dpMeta').innerHTML = metaDefs.map(function (m, i) {
      const v = g.sums[m[0]] || 0;
      const big = (i === 0) ? ' style="font-size:16px"' : '';
      return '<div class="dp-meta-item"><span class="dp-meta-label">' + m[1] + '</span>' +
             '<span class="dp-meta-val ' + (v < 0 ? 'neg' : '') + '"' + big + '>' + format_number(v, 0) + '</span></div>';
    }).join('');

    renderDrillBody(g);
    document.getElementById('drillOverlay').classList.add('open');
    document.getElementById('drillPanel').classList.add('open');
  }

  function renderDrillBody(g) {
    const totals = {}; DRILL_COLS.forEach(c => { if (c[2] === 'num') totals[c[0]] = 0; });

    let body = '';
    g.rows.forEach(r => {
      const nof = str(pickCI(r, 'NoFaktur'));
      const clickable = isVoucherNo(nof);
      const jn = clickable && (typeof jenisFromNo === 'function') ? jenisFromNo(nof) : '';
      const ttl = clickable && (typeof jenisTitle === 'function') ? jenisTitle(jn) : 'Voucher';
      const tr = clickable
        ? '<tr class="kas-clickable" title="Klik untuk lihat ' + ttl + ' ' + nof + '" ' +
          'onclick="openVoucher(\'' + escapeJs(nof) + '\',\'' + escapeJs(jn) + '\')">'
        : '<tr>';

      body += tr + DRILL_COLS.map(function (c) {
        const v = pickCI(r, c[0]);
        if (c[2] === 'num') { const n = currencyNormalizer(v); totals[c[0]] += n; return '<td class="num"' + agingStyle(c[0]) + '>' + (n ? format_number(n, 0) : '-') + '</td>'; }
        if (c[2] === 'date') return '<td style="white-space:nowrap">' + format_date(v) + '</td>';
        if (c[2] === 'voucher') return '<td><span class="ref-badge">' + nullToEmpty(v) + '</span></td>';
        return '<td>' + nullToEmpty(v) + '</td>';
      }).join('') + '</tr>';
    });

    if (!g.rows.length) body = '<tr><td colspan="' + DRILL_COLS.length + '" style="text-align:center;color:var(--muted);padding:14px">Tidak ada nota</td></tr>';

    const thead = '<tr>' + DRILL_COLS.map(c => '<th' + (c[2] === 'num' ? ' class="num"' : '') + agingStyle(c[0]) + '>' + c[1] + '</th>').join('') + '</tr>';
    const tfoot = '<tr class="ledger-total"><td colspan="5" style="font-weight:800">Total ' + g.name + '</td>' +
      DRILL_COLS.filter(c => c[2] === 'num').map(c => '<td class="num"' + agingStyle(c[0]) + '>' + format_number(totals[c[0]], 0) + '</td>').join('') + '</tr>';

    document.getElementById('dpBody').innerHTML =
      '<div class="dp-section-title">Rincian Nota - ' + g.rows.length + ' nota</div>' +
      '<div style="overflow-x:auto"><table class="ledger-table">' +
      '<thead>' + thead + '</thead><tbody>' + body + '</tbody><tfoot>' + tfoot + '</tfoot></table></div>';
  }

  function closeDrill() {
    document.getElementById('drillOverlay').classList.remove('open');
    document.getElementById('drillPanel').classList.remove('open');
  }

  /* ── CHARTS (Chart.js v4) ────────────────────────────────────────────────
     Kiri  : Aging Piutang (doughnut) — 5 bucket Belum JT / 1-30 / 31-60 / 61-90 / >90.
     Kanan : Piutang per Pelanggan Top 10 (bar horizontal) — Saldo Akhir per pelanggan, desc. ── */
  const CHART_PALETTE = ['#4F46E5','#7C3AED','#DB2777','#2563eb','#16a34a','#ca8a04','#ea580c','#0891b2','#e11d48','#65a30d'];
  const AGING_DEFS = [
    ['Belum JT', 'Saldo0',   AGING_COLORS.Saldo0],
    ['1 - 30',   'Saldo30',  AGING_COLORS.Saldo30],
    ['31 - 60',  'Saldo60',  AGING_COLORS.Saldo60],
    ['61 - 90',  'Saldo90',  AGING_COLORS.Saldo90],
    ['>90',      'Saldo121', AGING_COLORS.Saldo121],
  ];
  let _charts = {};

  function fmtShort(v) {
    v = Math.round(num(v)); const a = Math.abs(v);
    if (a >= 1e9) return (v / 1e9).toFixed(1).replace(/\.0$/, '') + ' M';
    if (a >= 1e6) return (v / 1e6).toFixed(1).replace(/\.0$/, '') + ' jt';
    if (a >= 1e3) return (v / 1e3).toFixed(0) + ' rb';
    return String(v);
  }
  function _destroyChart(id) { if (_charts[id]) { _charts[id].destroy(); delete _charts[id]; } }

  function buildCharts(rows) {
    if (typeof Chart === 'undefined') return;   // gagal muat Chart.js → lewati (tabel tetap jalan)
    try {
      Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
      Chart.defaults.font.size = 12;
      Chart.defaults.color = '#64748b';

      // agregasi
      const aging = AGING_DEFS.map(() => 0);
      const custOrder = [], custSum = {};
      (rows || []).forEach(r => {
        AGING_DEFS.forEach((d, i) => { aging[i] += currencyNormalizer(pickCI(r, d[1])); });
        const nm = str(pickCI(r, 'Nama')) || '(Tanpa Nama)';
        if (!(nm in custSum)) { custSum[nm] = 0; custOrder.push(nm); }
        custSum[nm] += currencyNormalizer(pickCI(r, 'Saldo'));
      });
      const top = custOrder.map(n => [n, custSum[n]]).sort((a, b) => b[1] - a[1]).slice(0, 10);

      // ── Aging doughnut ──
      _destroyChart('aging');
      _charts.aging = new Chart(document.getElementById('agingChart'), {
        type: 'doughnut',
        data: {
          labels: AGING_DEFS.map(d => d[0]),
          datasets: [{
            data: aging,
            backgroundColor: AGING_DEFS.map(d => d[2]),
            borderWidth: 2, borderColor: '#fff'
          }]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: {
            legend: { position: 'right' },
            tooltip: { callbacks: { label: (c) => ' ' + c.label + ': ' + fmtShort(c.parsed) } }
          }
        }
      });

      // ── Top 10 pelanggan bar (horizontal) ──
      _destroyChart('topCustomer');
      _charts.topCustomer = new Chart(document.getElementById('topCustomerChart'), {
        type: 'bar',
        data: {
          labels: top.map(t => t[0]),
          datasets: [{
            label: 'Saldo Akhir',
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
    } catch (e) { console.error('buildCharts', e); }
  }

  /* ── PENCARIAN SISI-KLIEN (cocokkan kode / nama pelanggan) ── */
  function applyFilters() { render(); }

  /* ── TOAST ── */
  function showToast(icon, msg) {
    const t = document.getElementById('toast');
    document.getElementById('ti').textContent = icon;
    document.getElementById('tm').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  function getKolomFilter() { return ['Tanggal', 'NoFaktur']; }

  /* ── DROPDOWN PERKIRAAN (akun PT; default akun pertama) ── */
  function loadPerkiraanDropdown() {
    let list = [];
    $.ajax({
      url: "{!! url('reportaccountingpiutangumur_loadperkiraan') !!}",
      type: "get", async: false,
      success: function (res) { list = res || []; }
    });

    let html = '';
    list.forEach((item) => {
      const ket = (item.Keterangan != null ? String(item.Keterangan) : '').replace(/"/g, '&quot;');
      html += '<li><a class="dropdown-item perkiraan-item" style="cursor:pointer" ' +
        'data-value="' + item.Perkiraan + '" data-ket="' + ket + '">' +
        item.Perkiraan + ' - ' + (item.Keterangan != null ? item.Keterangan : '') +
        ' <span class="checkmark-red" style="display:none">&#10003;</span></a></li>';
    });
    $("#dropdownPerkiraan").html(html);

    if (list.length) { setPerkiraan(list[0].Perkiraan, list[0].Keterangan != null ? list[0].Keterangan : ''); }
  }

  function setPerkiraan(kode, ket) {
    $("#inputPerkiraan").val(kode);
    $("#perkiraanLabel").text(kode);
    $("#inputPerkiraanBtn").attr('title', kode + (ket ? ' - ' + ket : ''));
    g_inputPerkiraan = kode + (ket ? ' - ' + ket : '');

    // pelanggan difilter per perkiraan → auto-pilih rentang: Awal = pelanggan pertama, Akhir = terakhir
    autoSelectSuppRange();

    $('#dropdownPerkiraan .checkmark-red').hide();
    $(`#dropdownPerkiraan .perkiraan-item[data-value='${kode}'] .checkmark-red`).show();
  }

  $(document).on('click', '#dropdownPerkiraan .perkiraan-item', function () {
    setPerkiraan($(this).data('value'), $(this).data('ket'));
  });

  /* ── AUTO-PILIH RENTANG PELANGGAN ──
     Isi Awal = pelanggan pertama, Akhir = pelanggan terakhir dari list akun (perkiraan)
     terpilih. List diurut per KodeCustsupp di endpoint, jadi pertama = kode terendah,
     terakhir = kode tertinggi. Dipanggil saat load & setiap ganti Perkiraan. ── */
  function autoSelectSuppRange() {
    let perkiraan = $("#inputPerkiraan").val();
    let list = [];
    $.ajax({
      url: "{!! url('reportaccountingpiutangumur_loadsuppawal') !!}",
      type: "get", async: false, data: { perkiraan: perkiraan },
      success: function (res) { list = res || []; }
    });

    if (list.length) {
      $('#inputSuppAwal').val(list[0].KodeCustsupp);
      $('#inputSuppAkhir').val(list[list.length - 1].KodeCustsupp);
    } else {
      $('#inputSuppAwal').val('-');
      $('#inputSuppAkhir').val('-');
    }
  }

  /* ── MODAL PELANGGAN AWAL ── */
  function buttonSelectSuppAwal() { loadSelectSuppAwal(); $("#formSelectSuppAwal").modal('toggle'); }
  function buttonPilihSuppAwal(kode) { $("#inputSuppAwal").val(kode); $("#formSelectSuppAwal").modal('hide'); }

  function loadSelectSuppAwal() {
    let perkiraan = $("#inputPerkiraan").val();
    let dataRefresh = [];
    if ($.fn.DataTable.isDataTable('#tabelSelectSuppAwal')) { $('#tabelSelectSuppAwal').DataTable().destroy(); }

    $.ajax({
      url: "{!! url('reportaccountingpiutangumur_loadsuppawal') !!}",
      type: "get", async: false, data: { perkiraan: perkiraan },
      success: function (res) { dataRefresh = res || []; }
    });

    let rowTable = "";
    dataRefresh.forEach((item) => {
      rowTable += `<tr class="pick-row" onclick="buttonPilihSuppAwal('${item.KodeCustsupp}')">
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        <td>${item.Alamat ?? ''}</td>
        <td>${item.Telpon ?? ''}</td>
      </tr>`;
    });
    document.getElementById("tabel_dataSelectSuppAwal").innerHTML = rowTable;
    $("#tabelSelectSuppAwal").DataTable({ "lengthChange": false, "paging": true });
  }

  /* ── MODAL PELANGGAN AKHIR ── */
  function buttonSelectSuppAkhir() { loadSelectSuppAkhir(); $("#formSelectSuppAkhir").modal('toggle'); }
  function buttonPilihSuppAkhir(kode) { $("#inputSuppAkhir").val(kode); $("#formSelectSuppAkhir").modal('hide'); }

  function loadSelectSuppAkhir() {
    let perkiraan = $("#inputPerkiraan").val();
    let dataRefresh = [];
    if ($.fn.DataTable.isDataTable('#tabelSelectSuppAkhir')) { $('#tabelSelectSuppAkhir').DataTable().destroy(); }

    $.ajax({
      url: "{!! url('reportaccountingpiutangumur_loadsuppawal') !!}",
      type: "get", async: false, data: { perkiraan: perkiraan },
      success: function (res) { dataRefresh = res || []; }
    });

    let rowTable = "";
    dataRefresh.forEach((item) => {
      rowTable += `<tr class="pick-row" onclick="buttonPilihSuppAkhir('${item.KodeCustsupp}')">
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        <td>${item.Alamat ?? ''}</td>
        <td>${item.Telpon ?? ''}</td>
      </tr>`;
    });
    document.getElementById("tabel_dataSelectSuppAkhir").innerHTML = rowTable;
    $("#tabelSelectSuppAkhir").DataTable({ "lengthChange": false, "paging": true });
  }

  /* ── MODAL VALAS ── */
  function buttonSelectValas() { loadSelectValas(); $("#formSelectValas").modal('toggle'); }
  function buttonPilihValas(kode) { $('#valas_display').val(kode); $('#valas_value').val(kode); $('#formSelectValas').modal('hide'); }

  function loadSelectValas() {
    let dataRefresh = [];
    if ($.fn.DataTable.isDataTable('#tabelSelectValas')) { $('#tabelSelectValas').DataTable().destroy(); }

    $.ajax({
      url: "{!! url('reportaccountingpiutangumur_loadvalas') !!}",
      type: "get", async: false,
      success: function (res) { dataRefresh = res || []; }
    });

    let rowTable = "";
    dataRefresh.forEach((item) => {
      rowTable += `<tr class="pick-row" onclick="buttonPilihValas('${item.Kodevls}')">
        <td>${item.Kodevls}</td>
        <td>${item.NamaVls}</td>
        <td>${item.Kurs}</td>
      </tr>`;
    });
    document.getElementById("tabel_dataSelectValas").innerHTML = rowTable;
    $("#tabelSelectValas").DataTable({ "lengthChange": false, "paging": true });
  }
</script>
@endsection
