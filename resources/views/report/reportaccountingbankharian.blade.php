@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php).
     .checkmark-red is also defined there. Only the toolbar dropdown-toggle buttons and the
     saldo summary table need page-local tweaks. --}}
<style>
  #inputReportMode, #inputTolakan, #inputPerkiraanBtn {
    border: 0; background: none; padding: 0; box-shadow: none;
    color: #495057; font-weight: 600;
  }
  #inputReportMode:hover, #inputReportMode:focus,
  #inputTolakan:hover,   #inputTolakan:focus,
  #inputPerkiraanBtn:hover, #inputPerkiraanBtn:focus { color: #0d6efd; box-shadow: none; }

  /* Saldo/signature summary is a second <tbody> inside #mainTable, so it inherits
     the .tb table width & styling. Give its cells the same padding + light row
     separator as data rows (override the base .tb td border:0 rule). */
  #bankSummary td { padding: 9px 14px; border-bottom: 1px solid #F1F5F9 !important; }
</style>

@section('header2')
<div class="tb-report main">
  <div class="content">

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div>
        <div class="page-title">Bank Harian</div>
      </div>

      <!-- Periode (date range) -->
      <div class="filter-wrap">
        <label>Periode</label>
        <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
        <span class="filter-sep">s/d</span>
        <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
      </div>

      <!-- Perkiraan (dropdown; diisi dari reportaccountingbankharian_loadperkiraan) -->
      <div class="filter-wrap">
        <label>Perkiraan</label>
        <input type="hidden" id="inputPerkiraan" value="-">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputPerkiraanBtn"
                data-bs-toggle="dropdown" aria-expanded="false"><span id="perkiraanLabel">-</span></button>
        <ul class="dropdown-menu" id="dropdownPerkiraan" aria-labelledby="inputPerkiraanBtn"
            style="max-height:320px; overflow:auto;"></ul>
      </div>

      <!-- Report Mode (Rp / Valas) -->
      <div class="filter-wrap">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputReportMode"
                data-bs-toggle="dropdown" aria-expanded="false">Mode: <span id="reportModeLabel">Rp</span></button>
        <ul class="dropdown-menu" id="dropdownReportMode" aria-labelledby="inputReportMode">
          <li><a class="dropdown-item" style="cursor:pointer" data-value="1" onclick="setReportMode('1')">Rp
            <span class="checkmark-red" style="display:none">&#10003;</span></a></li>
          <li><a class="dropdown-item" style="cursor:pointer" data-value="2" onclick="setReportMode('2')">Valas
            <span class="checkmark-red" style="display:none">&#10003;</span></a></li>
        </ul>
      </div>

      <!-- Actions: row-level search + customize + tampilkan + export -->
      <div class="action-group">
        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
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

    <!-- TABLE (header + rows rendered dynamically from gcart_header) -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead>
            <tr><th>Tanggal</th></tr>
          </thead>
          <tbody id="tableBody">
            <tr class="empty-row"><td>Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
          </tbody>
          <!-- SALDO / SIGNATURE SUMMARY (bank footer): a second tbody so it shares
               the .tb table's width & styling (built from Sp_LapSaldoAwal +
               Sp_ReportBankharian + rows). -->
          <tbody id="bankSummary"></tbody>
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
@endsection


@section('jsreport')
{{-- Shared formatters (fmtRp/fmtN) + voucher helpers live in public/js/report-table.js --}}
<script src="{!! URL::asset('public/js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>

<script type="text/javascript">
  let globalDate1     = "{!! date('Y-m-d') !!}";
  let globalDate2     = "{!! date('Y-m-d') !!}";
  let globalReportMode = "1";  // default: Rp (1 = Rp/detail, 2 = Valas/rekap)

  let g_reportTitle = "";
  let g_date1 = "", g_date2 = "", g_inputPerkiraan = "";

  let lastRows = [];               // hasil fetch terakhir (dipakai renderRows / search)
  let currentGroupby = 'nobukti';  // groupby aktif untuk render ulang saat search

  // mode report menentukan susunan kolom (gcart_header) & pengelompokan subtotal
  var modereport_detail = 1, modereport_rekap = 2;
  g_modeReport = modereport_detail;
  var jenisreport = 1;   // 1 = Rp/detail, 2 = Valas/rekap
  var DetOrRekap  = 1;   // dikirim ke proc sebagai @Valas

  const reportUrl     = "{{ url('reportaccountingbankharian_doReport') }}";
  const saldoAwalUrl  = "{{ url('reportaccountingbankharian_saldoawal') }}";
  const saldoAkhirUrl = "{{ url('reportaccountingbankharian_saldoakhir') }}";

  $(document).ready(function () {
    setReportMode(globalReportMode);   // memuat gcart_header untuk mode aktif
    loadPerkiraanDropdown();           // isi dropdown Perkiraan + pilih akun pertama

    // setTimeout(() => { makeTable('REPORT'); }, 100);
  });

  /* ── kolom (gcart_header) per mode. Tabel styled DI-RENDER dari sini, jadi hasil
        "Customize Table" (show/hide + urutan kolom) langsung ikut tampil. ── */
  function setDefaultHeader() {
    if (g_modeReport == modereport_detail) {
      gcart_header = [
        ['tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['nobukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['keterangan', 'Uraian', 1, 'varchar', 0, 0],
        ['Perkiraan', 'Perkiraan', 1, 'varchar', 0, 0],
        ['NamaPerkiraan', 'Uraian Perkiraan', 1, 'varchar', 0, 0],
        ['Debet', 'Penerimaan', 1, 'float', 1, 2],
        ['kredit', 'Pengeluaran', 1, 'float', 1, 2],
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    } else {
      gcart_header = [
        ['tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['nobukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['keterangan', 'Uraian', 1, 'varchar', 0, 0],
        ['Perkiraan', 'Perkiraan Bank', 1, 'varchar', 0, 0],
        ['Perkiraan', 'Perkiraan', 1, 'varchar', 0, 0],
        ['NamaPerkiraan', 'Uraian Perkiraan', 1, 'varchar', 0, 0],
        ['DebetD', 'Penerimaan', 1, 'float', 1, 2],
        ['kreditD', 'Pengeluaran', 1, 'float', 1, 2],
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    }
  }

  /* ── toolbar controls ── */
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
  }

  function setReportMode(val) {
    globalReportMode = val;
    jenisreport = Number(val);   // 1 = Rp, 2 = Valas
    DetOrRekap  = Number(val);

    $('#reportModeLabel').text(val === '1' ? 'Rp' : 'Valas');
    $('#dropdownReportMode .checkmark-red').hide();
    $(`#dropdownReportMode .dropdown-item[data-value='${val}'] .checkmark-red`).show();

    setModeReport();
  }

  function setModeReport() {
    g_modeReport = (jenisreport === 1) ? modereport_detail : modereport_rekap;
    doSetHeader(g_modeReport);   // muat susunan kolom (default / hasil kustomisasi user)
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
    const cols = gcart_header.filter(c => c[2] === 1);
    const header = cols.map(c => c[1]);
    const body = (lastRows || []).map(r => cols.map(function (c) {
      if (c[3] === 'date') return format_date(r[c[0]]);
      if (c[3] === 'float' || c[3] === 'int') return currencyNormalizer(r[c[0]]);
      return (r[c[0]] == null ? '' : r[c[0]]);
    }));
    const rows = [header].concat(body);
    const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const ext = (fmt === 'Excel') ? 'xls' : 'csv';
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'BankHarian_' + (g_date1 || '') + '_' + (g_date2 || '') + '.' + ext;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    showToast('📄', 'Data diekspor sebagai ' + fmt);
  }

  /* ── LOAD DATA: saldo awal -> saldo akhir -> data baris ── */
  function makeTable(_mode) {
    const groupby = (DetOrRekap === 1) ? 'nobukti' : 'Perkiraan';
    const _date1  = $('#inputDate1').val();
    const _date2  = $('#inputDate2').val();
    let   _perk   = $('#inputPerkiraan').val();
    if (!_perk) { _perk = '-'; }

    g_reportTitle = 'REPORT ACCOUNTING BANK HARIAN';
    g_date1 = _date1; g_date2 = _date2; g_inputPerkiraan = _perk;

    // Muat susunan kolom mode ini (default atau hasil "Customize Table" tersimpan)
    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }

    document.getElementById('footerLabel').textContent = 'Memuat data...';

    const data      = { date1: _date1, date2: _date2, inputPerkiraan: _perk, detOrRekap: DetOrRekap };
    const dataSaldo = { date1: _date1, date2: _date2, inputPerkiraan: _perk };

    // 1) saldo awal (Sp_LapSaldoAwal) -> 2) saldo akhir (Sp_ReportBankharian) -> 3) data (Sp_LapKasHarian)
    $.ajax({
      url: saldoAwalUrl, type: 'get', data: dataSaldo,
      success: function (res) { window.resSaldoAwal = (res && res.res2 && res.res2[0]) ? res.res2[0] : {}; },
      error: function () { window.resSaldoAwal = {}; },
      complete: function () {
        $.ajax({
          url: saldoAkhirUrl, type: 'get', data: dataSaldo,
          success: function (res) { window.resSaldoAkhir = (res && res.res3 && res.res3[0]) ? res.res3[0] : {}; },
          error: function () { window.resSaldoAkhir = {}; },
          complete: function () {
            $.ajax({
              url: reportUrl, type: 'get', data: data,
              success: function (res) {
                lastRows = (res && res.res1) ? res.res1 : [];
                currentGroupby = groupby;
                $('#searchBox2').val('');
                renderRows(lastRows, groupby);
                renderBankSummary();
              },
              error: function () {
                lastRows = []; currentGroupby = groupby;
                renderRows([], groupby);
                renderBankSummary();
              }
            });
          }
        });
      }
    });
  }

  /* ── RENDER KE TABEL STYLED (.tb-report #mainTable) ──
     Kolom dibangun DINAMIS dari gcart_header (hanya kolom terlihat / item[2]===1,
     sesuai urutan simpanan) — hasil "Customize Table" langsung tampil. Subtotal
     (per pergantian nilai `groupby`) & Grand Total menjumlahkan SEMUA kolom numerik
     yang ber-total (item[4]===1). Baris total mengikuti toggle di modal Customize
     Table (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal),
     yang dimuat oleh doSetHeader() saat klik Tampilkan. ── */
  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1);
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');

    // kolom numerik yang ikut ditotal
    const totalCols = cols.filter(c => (c[3] === 'float' || c[3] === 'int') && c[4] === 1);
    const totalKeys = totalCols.map(c => c[0]);
    const hasTotal  = totalCols.length > 0;
    const showSub   = hasTotal && (gsum_issubtotal === 1);
    const showGrand = hasTotal && (gsum_isgrandtotal === 1);

    // HEADER dinamis
    thead.innerHTML = '<tr>' + cols.map(function (c) {
      const isNum = (c[3] === 'float' || c[3] === 'int');
      return '<th' + (isNum ? ' class="num"' : '') + '>' + c[1] + '</th>';
    }).join('') + '</tr>';

    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null;
    let sub = {}, grand = {};
    totalKeys.forEach(k => { sub[k] = 0; grand[k] = 0; });

    rows.forEach(function (r, i) {
      const now = pickCI(r, groupby);

      // subtotal saat nilai grup berganti (bila toggle Subtotal aktif)
      if (showSub && i !== 0 && prev !== now) {
        html += totalRow('Subtotal', sub, cols, totalKeys, 'subtotal-row');
        totalKeys.forEach(k => sub[k] = 0);
      }

      totalKeys.forEach(function (k) {
        const v = currencyNormalizer(r[k]);
        sub[k] += v; grand[k] += v;
      });

      // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5])
      html += '<tr class="data-row">' + cols.map(function (c) {
        const key = c[0], type = c[3];
        if (type === 'date') return '<td>' + format_date(r[key]) + '</td>';
        if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(r[key]), c[5]) + '</td>';
        return '<td>' + nullToEmpty(r[key]) + '</td>';
      }).join('') + '</tr>';

      prev = now;
    });

    // subtotal grup terakhir + grand total — mengikuti toggle di modal
    if (showSub)   html += totalRow('Subtotal', sub, cols, totalKeys, 'subtotal-row');
    if (showGrand) html += totalRow('GRAND TOTAL', grand, cols, totalKeys, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
  }

  // Baris total: nilai di tiap kolom numerik yang ditotal; label di kolom pertama non-total.
  function totalRow(label, sums, cols, totalKeys, cls) {
    const labelIdx = cols.findIndex(c => totalKeys.indexOf(c[0]) === -1);
    const tds = cols.map(function (c, idx) {
      if (totalKeys.indexOf(c[0]) !== -1) return '<td class="num">' + format_number(sums[c[0]], c[5]) + '</td>';
      if (idx === labelIdx) return '<td>' + label + '</td>';
      return '<td></td>';
    });
    return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
  }

  // Ambil properti baris tanpa peduli besar/kecil huruf (proc mencampur casing).
  function pickCI(r, key) {
    if (r[key] !== undefined) return r[key];
    const lk = String(key).toLowerCase();
    for (const k in r) { if (k.toLowerCase() === lk) return r[k]; }
    return undefined;
  }

  /* ── PENCARIAN SISI-KLIEN: saring lastRows lalu render ulang tabel styled.
        Ringkasan saldo TIDAK ikut berubah (ia total periode, bukan hasil saring). ── */
  function applyFilters() {
    if (!lastRows.length) return;
    const term = ($('#searchBox2').val() || '').trim().toLowerCase();
    if (!term) { renderRows(lastRows, currentGroupby); return; }

    const cols = gcart_header.filter(c => c[2] === 1);
    const filtered = lastRows.filter(r => rowSearchText(r, cols).indexOf(term) !== -1);
    renderRows(filtered, currentGroupby);
  }

  function rowSearchText(r, cols) {
    return cols.map(function (c) {
      const v = r[c[0]];
      if (c[3] === 'date') return format_date(v);
      return (v == null ? '' : String(v));
    }).join(' ').toLowerCase();
  }

  /* ── RINGKASAN SALDO / TANDA TANGAN (footer bank) ──
        Dirender sebagai baris tambahan di dalam #mainTable. Perhitungan sama
        seperti versi lama (Sp_LapSaldoAwal + Sp_ReportBankharian + baris res1). ── */
  function renderBankSummary() {
    const box = document.getElementById('bankSummary');   // <tbody> inside #mainTable
    if (!box) return;
    box.innerHTML = setRowFooter(window.resSaldoAwal || {}, lastRows || [], window.resSaldoAkhir || []);
  }

  function setRowFooter(res2 = {}, res1 = [], res3 = []) {
    let isRupiah = String(globalReportMode) === "1";
    if (isRupiah) {
        return setRowFooterRp(res2, res1);       // MODE RP
    } else {
        return setRowFooterValas(res3, res2, res1); // MODE VALAS
    }
  }

  function setRowFooterRp(res2 = {}, res1 = []) {
    if (!res2 || Object.keys(res2).length === 0) { res2 = window.resSaldoAwal || {}; }
    let r2 = Array.isArray(res2) ? (res2[0] || {}) : res2;

    // menghitung debet & kredit
    let Debet = 0, kredit = 0;
    res1.forEach(r => {
        Debet  += Number(r.Debet  ?? 0);
        kredit += Number(r.kredit ?? 0);
    });

    // menghitung saldo akhir
    let totalDebet = 0, totalKredit = 0;
    res1.forEach(r => {
        totalDebet  += Number(r.Debet  ?? 0) + Number(r.Debet2  ?? 0);
        totalKredit += Number(r.kredit ?? 0) + Number(r.kredit2 ?? 0);
    });

    let saldoAwalD  = Number(r2.SaldoAwal ?? 0);
    let saldoAwalK  = -Number(r2.SaldoAwal ?? 0);
    let saldoAkhirD = - ( Number(r2.SaldoAwal ?? 0) + totalDebet - totalKredit );
    let saldoAkhirK = Number(r2.SaldoAwal ?? 0) + totalDebet - totalKredit;

    let f = (v) => Number(v).toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    let spacing = `<tr><td colspan="10" style="border:0 !important; height:10px;"></td></tr>`;

    let leftBlock = `
      <tr>
        <td></td>
        <td></td>
        <td style="text-align:right;"></td>
        <td colspan="2" style="font-weight:bold; text-align: center;">Jumlah</td>
        <td style="text-align:right;">${f(Debet)}</td>
        <td style="text-align:right;">${f(kredit)}</td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td style="text-align:right;"></td>
        <td colspan="2" style="font-weight:bold; text-align: center;">Saldo Awal</td>
        <td style="text-align:right;">${f(saldoAwalD)}</td>
        <td style="text-align:right;"></td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td style="text-align:right;"></td>
        <td colspan="2" style="font-weight:bold; text-align: center;">Saldo Akhir</td>
        <td style="text-align:right;"></td>
        <td style="text-align:right;">${f(saldoAkhirK)}</td>
      </tr>`;

    let signature = `
      <tr>
        <td colspan="3" style="height:90px; text-align:center; font-weight:bold;">Pimpinan</td>
        <td colspan="2" style="text-align:center; font-weight:bold;">Kontrol</td>
        <td colspan="2" style="text-align:center; font-weight:bold;">Kasir</td>
      </tr>
    `;

    return spacing + leftBlock + signature;
  }

  function setRowFooterValas(res2 = {}, res1 = [], res3 = []) {
    if (!res2 || Object.keys(res2).length === 0) { res2 = window.resSaldoAwal || {}; }
    if (!Array.isArray(res1)) { res1 = Object.values(res1 || {}); }

    let r2 = Array.isArray(res2) ? (res2[0] || {}) : res2;
    let r3 = Array.isArray(res3) ? (res3[0] || {}) : res3;

    let SaldoUs     = Number(r3.SaldoUs    || 0);
    let SaldoAwalD  = Number(r2.SaldoAwalD || 0);

    let DebetD = 0, kreditD = 0;
    res1 = res1.filter(r => r && typeof r === "object");
    res1.forEach(row => {
        DebetD  += Number(row.DebetD  || 0);
        kreditD += Number(row.kreditD || 0);
    });

    // hitungan saldo akhir
    let saldoAkhirD = (DebetD + SaldoAwalD) - kreditD;

    let f = (v) => Number(v).toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    let spacing = `<tr><td colspan="15" style="border:0 !important; height:10px;"></td></tr>`;

    let block = `
      <tr>
        <td></td>
        <td></td>
        <td style="text-align:right;"></td>
        <td></td>
        <td colspan="2" style="font-weight:bold;">Jumlah</td>
        <td style="text-align:right;">${f(DebetD)}</td>
        <td style="text-align:right;">${f(kreditD)}</td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td style="text-align:right;"></td>
        <td></td>
        <td colspan="2" style="font-weight:bold;">Saldo Awal</td>
        <td style="text-align:right;">${f(SaldoAwalD)}</td>
        <td style="text-align:right;"></td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td style="text-align:right;"></td>
        <td></td>
        <td colspan="2" style="font-weight:bold;">Saldo Akhir</td>
        <td style="text-align:right;"></td>
        <td style="text-align:right;">${f(SaldoUs)}</td>
      </tr>
    `;

    let signature = `
      <tr>
        <td colspan="3" style="height:90px; text-align:center; font-weight:bold;">Pimpinan</td>
        <td colspan="2" style="text-align:center; font-weight:bold;">Kontrol</td>
        <td colspan="2" style="text-align:center; font-weight:bold;">Kasir</td>
      </tr>
    `;

    return spacing + block + signature;
  }

  /* ── TOAST ── */
  function showToast(icon, msg) {
    const t = document.getElementById('toast');
    document.getElementById('ti').textContent = icon;
    document.getElementById('tm').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  /* ── Filter Data engine (opsional): kolom yang dipakai modal "Filter Data" ── */
  function getKolomFilter() {
    return ['nobukti', 'tanggal'];
  }

  /* ── DROPDOWN PERKIRAAN ──
        Diisi sekali dari reportaccountingbankharian_loadperkiraan saat page load.
        Memilih item hanya menyetel nilai (#inputPerkiraan) + label; laporan baru
        dimuat saat klik Tampilkan (konsisten dgn filter Periode/Mode). ── */
  function loadPerkiraanDropdown() {
    let list = [];
    $.ajax({
      url: "{!! url('reportaccountingbankharian_loadperkiraan') !!}",
      type: "get",
      async: false,
      success: function (res) { list = res || []; }
    });

    let html = "";
    list.forEach((item) => {
      const ket = (item.Keterangan != null ? String(item.Keterangan) : '').replace(/"/g, '&quot;');
      html += '<li><a class="dropdown-item perkiraan-item" style="cursor:pointer" '
            + 'data-value="' + item.Perkiraan + '" data-ket="' + ket + '">'
            + item.Perkiraan + ' - ' + (item.Keterangan != null ? item.Keterangan : '')
            + ' <span class="checkmark-red" style="display:none">&#10003;</span></a></li>';
    });
    $("#dropdownPerkiraan").html(html);

    // pilih akun pertama sebagai default (tidak ada opsi "Semua")
    if (list.length) {
      setPerkiraan(list[0].Perkiraan, list[0].Keterangan != null ? list[0].Keterangan : '');
    }
  }

  function setPerkiraan(kode, ket) {
    $("#inputPerkiraan").val(kode);
    $("#perkiraanLabel").text(kode);
    $("#inputPerkiraanBtn").attr('title', kode + (ket ? ' - ' + ket : ''));

    $('#dropdownPerkiraan .checkmark-red').hide();
    $(`#dropdownPerkiraan .perkiraan-item[data-value='${kode}'] .checkmark-red`).show();
  }

  // klik item dropdown (event delegation — menghindari masalah escaping di onclick)
  $(document).on('click', '#dropdownPerkiraan .perkiraan-item', function () {
    setPerkiraan($(this).data('value'), $(this).data('ket'));
  });
</script>
@endsection
