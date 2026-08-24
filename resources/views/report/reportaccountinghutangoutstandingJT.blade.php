@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php).
     Hutang Outstanding JT (sisa hutang per jatuh tempo): styled .tb-report, dikelompokkan per
     supplier (namasupp) dengan subtotal per supplier + grand total (toggle Customize Table).
     Mode IDR / $ (valas). Supplier Awal/Akhir & Valas tetap pakai modal; Perkiraan dropdown. --}}
<style>
  #inputReportMode, #inputOrder, #inputPerkiraanBtn, #inputReportBtn {
    border: 0; background: none; padding: 0; box-shadow: none;
    color: #495057; font-weight: 600;
  }
  #inputReportMode:hover, #inputReportMode:focus,
  #inputOrder:hover, #inputOrder:focus,
  #inputPerkiraanBtn:hover, #inputPerkiraanBtn:focus,
  #inputReportBtn:hover, #inputReportBtn:focus { color: #0d6efd; box-shadow: none; }

  /* tinggi awal area tabel supaya dropdown tidak terpotong container pendek */
  .tb-report .table-wrap { min-height: 10vh; }
</style>

@section('header2')
<div class="tb-report main">
  <div class="content">

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div>
        <div class="page-title" id="reportTitleLabel">Outstanding Jatuh Tempo</div>
      </div>

      <!-- Laporan (DROPDOWN; pilih salah satu dari 3 laporan hutang) -->
      <div class="filter-wrap">
        <label>Laporan</label>
        <input type="hidden" id="inputReport" value="jt">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputReportBtn"
                data-bs-toggle="dropdown" aria-expanded="false"><span id="reportLabel">Outstanding Jatuh Tempo</span></button>
        <ul class="dropdown-menu" id="dropdownReport" aria-labelledby="inputReportBtn"
            style="max-height:320px; overflow:auto;"></ul>
      </div>

      <!-- Per Tanggal (JT/Nota) / Periode rentang (LHPJT — #inputDate2 tampil saat dateRange) -->
      <div class="filter-wrap">
        <label id="dateLabel">Per Tanggal</label>
        <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
        <span class="filter-sep" id="dateSep" style="display:none">s/d</span>
        <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}" style="display:none">
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

      <!-- Perkiraan (dropdown; akun HT) -->
      <div class="filter-wrap">
        <label>Perkiraan</label>
        <input type="hidden" id="inputPerkiraan" value="-">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputPerkiraanBtn"
                data-bs-toggle="dropdown" aria-expanded="false"><span id="perkiraanLabel">-</span></button>
        <ul class="dropdown-menu" id="dropdownPerkiraan" aria-labelledby="inputPerkiraanBtn"
            style="max-height:320px; overflow:auto;"></ul>
      </div>

      <!-- Supplier Awal (modal — data banyak) -->
      <div class="filter-wrap">
        <label>Supp Awal</label>
        <input type="text" class="filter-inp" id="inputSuppAwal" style="width:90px" value="-" readonly>
        <button type="button" class="btn-pick" onclick="buttonSelectSuppAwal()" title="Pilih Supplier Awal">+</button>
      </div>

      <!-- Supplier Akhir (modal — data banyak) -->
      <div class="filter-wrap">
        <label>Supp Akhir</label>
        <input type="text" class="filter-inp" id="inputSuppAkhir" style="width:90px" value="-" readonly>
        <button type="button" class="btn-pick" onclick="buttonSelectSuppAkhir()" title="Pilih Supplier Akhir">+</button>
      </div>

      <!-- Order By -->
      <div class="filter-wrap">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputOrder"
                data-bs-toggle="dropdown" aria-expanded="false">Urut: <span id="orderLabel">Tanggal</span></button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputOrder">
          <li><a class="dropdown-item" style="cursor:pointer" data-value="0" onclick="setOrderBy('0')">Tanggal
            <span class="checkmark-red" style="display:none">&#10003;</span></a></li>
          <li><a class="dropdown-item" style="cursor:pointer" data-value="1" onclick="setOrderBy('1')">No.Nota
            <span class="checkmark-red" style="display:none">&#10003;</span></a></li>
        </ul>
      </div>
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

    <!-- TABLE (header + rows rendered dynamically from gcart_header; grouped per supplier) -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead>
            <tr><th>Tanggal</th></tr>
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

  <!-- TOAST -->
  <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>

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

<!-- modal select supplier awal -->
<div class="modal fade rt-picker-v2" id="formSelectSuppAwal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Supplier Awal</h5>
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

<!-- modal select supplier akhir -->
<div class="modal fade rt-picker-v2" id="formSelectSuppAkhir" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Supplier Akhir</h5>
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
  let globalDate2 = "{!! date('Y-m-d') !!}";
  let globalOrderBy = "0";        // default: Tanggal
  let globalReportMode = "IDR";   // default: IDR

  let g_reportTitle = "";
  let g_inputPerkiraan = "";

  let lastRows = [];   // hasil fetch terakhir (dipakai render / search)

  // Mode string: 'IDR' (detail) / '$' (valas)
  // Mode NUMERIK (bukan 'IDR'/'$'): DBSIMPANHEADER.reportmode itu kolom integer, jadi
  // mode string membuat header (termasuk toggle Subtotal/Grand Total) TIDAK tersimpan —
  // saat Tampilkan ia jatuh ke setDefaultHeader() sehingga subtotal/grand total nyala lagi.
  var modereport_detail = 5, modereport_rekap = 6;
  g_modeReport = modereport_detail;
  var jenisreport = 0, DetOrRekap = 0;

  // ── PETA 3 LAPORAN HUTANG (JT = basis) ──────────────────────────────────────
  // Satu halaman; dropdown "Laporan" berpindah antar laporan. reportUrl + 4 URL voucher
  // diturunkan dari `slug` + BASE. Backend TIDAK berubah: tiap laporan tetap memakai
  // stored proc & endpoint _doReport masing-masing. loadPerkiraan/loadSuppAwal/loadValas
  // identik di 3 controller → tetap dipanggil dari endpoint JT (tidak ikut berpindah).
  const BASE = "{{ url('/') }}";
  const REPORTS = {
    jt   : { slug:'reportaccountinghutangoutstandingJT',   title:'Outstanding Jatuh Tempo',
             reportTitle:'REPORT ACCOUNTING HUTANG OUTSTANDING JT',
             file:'HutangOutstandingJT',   modeDetail:5,  modeRekap:6,  voucher:false, dateRange:false },
    nota : { slug:'reportaccountinghutangoutstandingnota', title:'Outstanding Nota',
             reportTitle:'REPORT ACCOUNTING HUTANG OUTSTANDING NOTA',
             file:'HutangOutstandingNota', modeDetail:9,  modeRekap:10, voucher:true,  dateRange:false },
    lhpjt: { slug:'reportaccountinghutanglhpjt',           title:'Hutang Per Jatuh Tempo',
             reportTitle:'REPORT ACCOUNTING HUTANG LAPORAN HUTANG PER JATUH TEMPO',
             file:'HutangJatuhTempo',      modeDetail:11, modeRekap:12, voucher:true,  dateRange:true }
  };

  // reportUrl berpindah saat laporan diganti → `let`. Di-set applyReport().
  let reportUrl    = "";
  let g_reportKey  = "jt";
  let g_hasVoucher = false;   // JT: baris tak bisa diklik; Nota/LHPJT: bisa (voucher panel)
  let g_dateRange  = false;   // LHPJT: rentang tanggal (pakai date2)
  let g_exportBase = "HutangOutstandingJT";

  // report-table.js membaca window.ReportTableConfig lazily (cfg()) saat baris diklik,
  // jadi aman menukar/mengosongkannya tiap ganti laporan (applyReport).
  window.ReportTableConfig = {};

  $(document).ready(function () {
    buildReportDropdown();             // isi dropdown Laporan dari peta REPORTS
    setOrderBy(globalOrderBy);
    applyReport(g_reportKey);          // set url/voucher/date + ID mode + setReportMode (muat gcart_header)
    loadPerkiraanDropdown();           // isi dropdown Perkiraan (default akun HT pertama)

    // setTimeout(() => { makeTable('REPORT'); }, 100);
  });

  /* ── kolom (gcart_header) per mode. Tabel styled DI-RENDER dari sini (Customize Table).
        Kolom nominal ditandai total (item[4]=1); Subtotal per supplier & Grand Total ikut
        toggle Customize Table. Kolom `namasupp` dipakai untuk header grup (bukan kolom). ── */
  function setDefaultHeader() {
    if (g_modeReport == modereport_detail) {
      gcart_header = [
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['JatuhTempo', 'Jatuh Tempo', 1, 'date', 0, 0],
        ['Nofaktur', 'No Nota', 1, 'varchar', 0, 0],
        ['Jumlah', 'Jumlah Rp', 1, 'float', 1, 2],
        ['Terbayar', 'Bayar Rp', 1, 'float', 1, 2],
        ['sisa', 'Sisa Rp', 1, 'float', 1, 2],
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    } else {
      gcart_header = [
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['JatuhTempo', 'Jatuh Tempo', 1, 'date', 0, 0],
        ['Nofaktur', 'No Nota', 1, 'varchar', 0, 0],
        ['Jumlah', 'Jumlah Rp', 1, 'float', 1, 2],
        ['Terbayar', 'Bayar Rp', 1, 'float', 1, 2],
        ['sisa', 'Sisa Rp', 1, 'float', 1, 2],
        ['JumlahD', 'Jumlah $', 1, 'float', 1, 2],
        ['TerbayarD', 'Bayar $', 1, 'float', 1, 2],
        ['sisaD', 'Sisa $', 1, 'float', 1, 2],
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    }
  }

  /* ── toolbar controls ── */
  function showPeriode() { globalDate1 = $('#inputDate1').val(); }

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

  function setOrderBy(val) {
    globalOrderBy = val;
    $('#inputOrd').val(val);
    $('#orderLabel').text(val === '0' ? 'Tanggal' : 'No.Nota');
    $('#dropdownOrder .checkmark-red').hide();
    $(`#dropdownOrder .dropdown-item[data-value='${val}'] .checkmark-red`).show();
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
    const cols = gcart_header.filter(c => c[2] === 1);
    const header = ['Supplier'].concat(cols.map(c => c[1]));
    const body = (lastRows || []).map(r => [str(pickCI(r, 'namasupp'))].concat(cols.map(function (c) {
      const v = pickCI(r, c[0]);
      if (c[3] === 'date') return format_date(v);
      if (c[3] === 'float' || c[3] === 'int') return currencyNormalizer(v);
      return (v == null ? '' : v);
    })));
    const rows = [header].concat(body);
    const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const ext = (fmt === 'Excel') ? 'xls' : 'csv';
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = g_exportBase + '_' + (globalDate1 || '') + (g_dateRange ? '_' + (globalDate2 || '') : '') + '.' + ext;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    showToast('📄', 'Data diekspor sebagai ' + fmt);
  }

  /* ── LOAD DATA (sp_ReportSisaHutang; doReport mengembalikan array biasa) ── */
  function makeTable(_mode) {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
    // g_reportTitle di-set oleh applyReport() sesuai laporan aktif.

    let _perk   = $('#inputPerkiraan').val() || '-';
    let _suppAw = $('#inputSuppAwal').val()  || '-';
    let _suppAk = $('#inputSuppAkhir').val() || '-';
    let _ord    = $('#inputOrd').val();
    let _valas  = $('#valas_value').val();

    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    const data = {
      date1: globalDate1, date2: globalDate2,
      inputSuppAwal: _suppAw, inputSuppAkhir: _suppAk,
      inputOrd: _ord, inputPerkiraan: _perk, valas_value: _valas
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

  /* ── VOUCHER (hanya laporan dengan g_hasVoucher = Nota/LHPJT) ──
     No Nota clickable hanya untuk nomor voucher betulan (mengandung '/', bukan baris
     "Saldo Awal"). Membuka panel voucher bawah (report-table.js); Jenis diambil dari
     nomor via jenisFromNo. JT (g_hasVoucher=false) → baris statis, helper ini tak dipakai. ── */
  function isVoucherNo(v) {
    const s = str(v);
    if (!s || s.indexOf('/') === -1) return false;
    return s.toUpperCase().indexOf('SALDO AWAL') === -1;
  }
  function voucherCell(v) {
    const s = str(v);
    if (!isVoucherNo(s)) return '<td>' + nullToEmpty(v) + '</td>';
    return '<td class="kas-clickable" style="color:#0d6efd;text-decoration:underline">' + nullToEmpty(v) + '</td>';
  }
  function voucherRowOpen(v, cls) {
    const s = str(v);
    if (!isVoucherNo(s)) return '<tr class="' + cls + '">';
    const jn  = (typeof jenisFromNo === 'function') ? jenisFromNo(s) : '';
    const ttl = (typeof jenisTitle === 'function') ? jenisTitle(jn) : 'Voucher';
    const esc = s.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    const jsc = String(jn).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    return '<tr class="' + cls + '" title="Klik untuk lihat ' + ttl + ' ' + s + '" ' +
           'onclick="openVoucher(\'' + esc + '\',\'' + jsc + '\')">';
  }

  /* ── RENDER: dikelompokkan per supplier (kolom `namasupp`) ── */
  function render() {
    const cols  = gcart_header.filter(c => c[2] === 1);
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');

    const totalCols = cols.filter(c => (c[3] === 'float' || c[3] === 'int') && c[4] === 1);
    const totalKeys = totalCols.map(c => c[0]);
    const hasTotal  = totalCols.length > 0;
    const showSub   = hasTotal && (gsum_issubtotal === 1);
    const showGrand = hasTotal && (gsum_isgrandtotal === 1);
    const search = ($('#searchBox2').val() || '').trim().toLowerCase();

    // HEADER dinamis
    thead.innerHTML = '<tr>' + cols.map(function (c) {
      const isNum = (c[3] === 'float' || c[3] === 'int');
      return '<th' + (isNum ? ' class="num"' : '') + '>' + c[1] + '</th>';
    }).join('') + '</tr>';

    // kelompokkan per supplier (namasupp), pertahankan urutan kemunculan
    const order = [], buckets = {};
    (lastRows || []).forEach(r => {
      if (search && rowSearchText(r, cols).indexOf(search) === -1) return;
      const gkey = str(pickCI(r, 'namasupp'));
      if (!(gkey in buckets)) { buckets[gkey] = []; order.push(gkey); }
      buckets[gkey].push(r);
    });

    if (!order.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '';
    const grand = {}; totalKeys.forEach(k => grand[k] = 0);
    let visible = 0;

    order.forEach(gkey => {
      const rows = buckets[gkey];
      const label = (gkey !== '' ? gkey : '(Tanpa Nama)');

      html += '<tr class="group-row"><td colspan="' + cols.length + '">' + label +
        ' <span style="font-size:11px;font-weight:600;opacity:.7;margin-left:8px">(' + rows.length + ' transaksi)</span></td></tr>';

      const sub = {}; totalKeys.forEach(k => sub[k] = 0);
      rows.forEach(r => {
        totalKeys.forEach(k => { const v = currencyNormalizer(pickCI(r, k)); sub[k] += v; grand[k] += v; });
        const openTag = g_hasVoucher ? voucherRowOpen(pickCI(r, 'nofaktur'), 'data-row') : '<tr class="data-row">';
        html += openTag + cols.map(function (c) {
          const key = c[0], type = c[3];
          const v = pickCI(r, key);
          if (type === 'date') return '<td>' + format_date(v) + '</td>';
          if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(v), c[5]) + '</td>';
          if (g_hasVoucher && String(key).toLowerCase() === 'nofaktur') return voucherCell(v);
          return '<td>' + nullToEmpty(v) + '</td>';
        }).join('') + '</tr>';
        visible++;
      });

      if (showSub) html += totalRow('Subtotal', sub, cols, totalKeys, 'subtotal-row');
    });

    if (showGrand) html += totalRow('GRAND TOTAL', grand, cols, totalKeys, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + visible + ' baris';
  }

  function totalRow(label, sums, cols, totalKeys, cls) {
    const labelIdx = cols.findIndex(c => totalKeys.indexOf(c[0]) === -1);
    const tds = cols.map(function (c, idx) {
      if (totalKeys.indexOf(c[0]) !== -1) return '<td class="num">' + format_number(sums[c[0]], c[5]) + '</td>';
      if (idx === labelIdx) return '<td>' + label + '</td>';
      return '<td></td>';
    });
    return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
  }

  /* ── PENCARIAN SISI-KLIEN ── */
  function applyFilters() { render(); }

  function rowSearchText(r, cols) {
    let s = str(pickCI(r, 'namasupp'));   // ikutkan nama supplier
    cols.forEach(function (c) {
      const v = pickCI(r, c[0]);
      s += ' ' + (c[3] === 'date' ? format_date(v) : (v == null ? '' : String(v)));
    });
    return s.toLowerCase();
  }

  /* ── TOAST ── */
  function showToast(icon, msg) {
    const t = document.getElementById('toast');
    document.getElementById('ti').textContent = icon;
    document.getElementById('tm').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  function getKolomFilter() { return ['Tanggal', 'Nofaktur']; }

  /* ── DROPDOWN PERKIRAAN (akun HT; default akun pertama) ── */
  function loadPerkiraanDropdown() {
    let list = [];
    $.ajax({
      url: "{!! url('reportaccountinghutangoutstandingJT_loadperkiraan') !!}",
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

    // supplier difilter per perkiraan → auto-pilih rentang: Awal = supplier pertama, Akhir = terakhir
    autoSelectSuppRange();

    $('#dropdownPerkiraan .checkmark-red').hide();
    $(`#dropdownPerkiraan .perkiraan-item[data-value='${kode}'] .checkmark-red`).show();
  }

  $(document).on('click', '#dropdownPerkiraan .perkiraan-item', function () {
    setPerkiraan($(this).data('value'), $(this).data('ket'));
  });

  /* ── DROPDOWN LAPORAN (peta REPORTS; JT = basis) ── */
  function buildReportDropdown() {
    let html = '';
    Object.keys(REPORTS).forEach((key) => {
      html += '<li><a class="dropdown-item report-item" style="cursor:pointer" data-key="' + key + '">' +
        REPORTS[key].title + ' <span class="checkmark-red" style="display:none">&#10003;</span></a></li>';
    });
    $('#dropdownReport').html(html);
  }

  // Set url report + config voucher + judul + basis export + UI tanggal + ID mode sesuai
  // laporan terpilih, lalu muat ulang header (setReportMode). TIDAK memuat data sendiri.
  function applyReport(key) {
    const r = REPORTS[key];
    if (!r) return;
    g_reportKey       = key;
    reportUrl         = BASE + '/' + r.slug + '_doReport';
    modereport_detail = r.modeDetail;
    modereport_rekap  = r.modeRekap;
    g_hasVoucher      = !!r.voucher;
    g_dateRange       = !!r.dateRange;
    g_exportBase      = r.file;
    g_reportTitle     = r.reportTitle;

    // voucher panel: hanya Nota/LHPJT (JT tidak bisa diklik → config dikosongkan)
    if (g_hasVoucher) {
      window.ReportTableConfig = {
        kasUrl    : BASE + '/' + r.slug + '_doKasharian',
        invoiceUrl: BASE + '/' + r.slug + '_doInvoice',
        lpbUrl    : BASE + '/' + r.slug + '_doLpb',
        bpUrl     : BASE + '/' + r.slug + '_doBp'
      };
    } else {
      window.ReportTableConfig = {};
    }

    // UI tanggal: rentang (Periode) untuk LHPJT, tunggal (Per Tanggal) untuk lainnya
    $('#dateLabel').text(g_dateRange ? 'Periode' : 'Per Tanggal');
    $('#dateSep').toggle(g_dateRange);
    $('#inputDate2').toggle(g_dateRange);

    // label + centang dropdown
    $('#inputReport').val(key);
    $('#reportTitleLabel').text(r.title);
    $('#reportLabel').text(r.title);
    $('#inputReportBtn').attr('title', r.title);
    $('#dropdownReport .checkmark-red').hide();
    $(`#dropdownReport .report-item[data-key='${key}'] .checkmark-red`).show();

    // muat susunan kolom untuk ID mode laporan ini (Customize Table per laporan)
    setReportMode(globalReportMode);
  }

  // Ganti laporan hanya menyetel konfigurasi/kolom — TIDAK memuat data. Data dimuat
  // saat user klik "Tampilkan" (makeTable). Auto-load awal tetap di $(document).ready.
  function setReport(key) {
    applyReport(key);
  }

  $(document).on('click', '#dropdownReport .report-item', function () {
    setReport($(this).data('key'));
  });

  /* ── AUTO-PILIH RENTANG SUPPLIER ──
     Isi Supp Awal = supplier pertama, Supp Akhir = supplier terakhir dari list akun
     (perkiraan) terpilih. List diurut per KodeCustsupp di endpoint, jadi pertama = kode
     terendah, terakhir = kode tertinggi. Dipanggil saat load & setiap ganti Perkiraan. ── */
  function autoSelectSuppRange() {
    let perkiraan = $("#inputPerkiraan").val();
    let list = [];
    $.ajax({
      url: "{!! url('reportaccountinghutangoutstandingJT_loadsuppawal') !!}",
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

  /* ── MODAL SUPPLIER AWAL ── */
  function buttonSelectSuppAwal() { loadSelectSuppAwal(); $("#formSelectSuppAwal").modal('toggle'); }
  function buttonPilihSuppAwal(kode) { $("#inputSuppAwal").val(kode); $("#formSelectSuppAwal").modal('hide'); }

  function loadSelectSuppAwal() {
    let perkiraan = $("#inputPerkiraan").val();
    let dataRefresh = [];
    if ($.fn.DataTable.isDataTable('#tabelSelectSuppAwal')) { $('#tabelSelectSuppAwal').DataTable().destroy(); }

    $.ajax({
      url: "{!! url('reportaccountinghutangoutstandingJT_loadsuppawal') !!}",
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

  /* ── MODAL SUPPLIER AKHIR ── */
  function buttonSelectSuppAkhir() { loadSelectSuppAkhir(); $("#formSelectSuppAkhir").modal('toggle'); }
  function buttonPilihSuppAkhir(kode) { $("#inputSuppAkhir").val(kode); $("#formSelectSuppAkhir").modal('hide'); }

  function loadSelectSuppAkhir() {
    let perkiraan = $("#inputPerkiraan").val();
    let dataRefresh = [];
    if ($.fn.DataTable.isDataTable('#tabelSelectSuppAkhir')) { $('#tabelSelectSuppAkhir').DataTable().destroy(); }

    $.ajax({
      url: "{!! url('reportaccountinghutangoutstandingJT_loadsuppawal') !!}",
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
      url: "{!! url('reportaccountinghutangoutstandingJT_loadvalas') !!}",
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
