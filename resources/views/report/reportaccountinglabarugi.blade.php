@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php).
     Laporan Laba Rugi (income statement): styled .tb-report dengan header DUA TINGKAT — Keterangan
     + 3 band (Bulan Lalu / Bulan Ini / s/d Bulan Ini), masing-masing Rp. + %. Baris P&L di-render
     apa adanya (urutan dari SP) + Grand Total pada kolom Rp saja. Filter: Bulan/Tahun (dropdown) +
     Divisi (dropdown, default divisi pertama). Tidak ada kolom No Bukti → tanpa panel voucher.
     Alur: triggerSp (ambil totalA/B/C dari dbLRHPP) → sp_ReportLabaRugi (baris + % memakai total itu).
     Data hanya dimuat setelah klik "Tampilkan".
     Di atas tabel: KPI strip (Pendapatan/HPP/Laba Kotor/Laba Bersih dari baris Persen A-D) +
     2 bar chart (Pendapatan vs Beban per periode; Komposisi Beban per akun Kelompok=4) dibangun
     sisi-klien dari lastRows (kolom Persen/Kelompok dari SP, sebelumnya tidak dipakai). --}}

<!-- Chart.js v4 (di-bundle lokal: public/plugins/chart.js/chart.umd.min.js) -->
<script src="{!! URL::asset('public/plugins/chart.js/chart.umd.min.js') !!}?v={{ @filemtime(base_path('public/plugins/chart.js/chart.umd.min.js')) ?: '1' }}"></script>

<style>
  .checkmark-red { color: red !important; font-weight: bold; margin-left: 6px; }

  #inputDivisiBtn {
    border: 0; background: none; padding: 0; box-shadow: none;
    color: #495057; font-weight: 600;
  }
  #inputDivisiBtn:hover, #inputDivisiBtn:focus { color: #0d6efd; box-shadow: none; }

  /* tinggi awal area tabel supaya dropdown tidak terpotong container pendek */
  .tb-report .table-wrap { min-height: 10vh; }

  /* ── KPI strip ── */
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
        <div class="page-title">Laporan Laba Rugi</div>
      </div>

      <!-- Period selector (populated dynamically by populatePeriodSelectors) -->
      <div class="period-select-wrap">
        <label>Periode</label>
        <select class="period-select" id="periodBulan" onchange="changePeriodParts()"></select>
        <select class="period-select" id="periodTahun" onchange="changePeriodParts()"></select>
      </div>

      <!-- Divisi (DROPDOWN; sumber loadDivisi, default divisi pertama) -->
      <div class="filter-wrap">
        <label>Divisi</label>
        <input type="hidden" id="inputDivisi" value="-">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputDivisiBtn"
                data-bs-toggle="dropdown" aria-expanded="false"><span id="divisiLabel">-</span></button>
        <ul class="dropdown-menu" id="dropdownDivisi" aria-labelledby="inputDivisiBtn"
            style="max-height:320px; overflow:auto;"></ul>
      </div>

      <!-- Actions: search + tampilkan + export -->
      <div class="action-group">
        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:160px">
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

    <!-- KPI STRIP (Pendapatan/HPP/Laba Kotor/Laba Bersih; dibangun dari lastRows) -->
    <div class="kpi-strip" id="kpiStrip"></div>

    <!-- CHARTS (dibangun sisi-klien dari lastRows) -->
    <div class="chart-grid">
      <div class="chart-box">
        <h3>Pendapatan vs Beban</h3>
        <div class="chart-holder"><canvas id="lrChart1"></canvas></div>
      </div>
      <div class="chart-box">
        <h3>Komposisi Beban</h3>
        <div class="chart-holder"><canvas id="lrChart2"></canvas></div>
      </div>
    </div>

    <!-- TABLE — header dua tingkat (band per periode); baris di-render dari makeTable() -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead>
            <tr>
              <th rowspan="2" style="min-width:260px">Keterangan</th>
              <th colspan="2" class="th-group">Bulan Lalu</th>
              <th colspan="2" class="th-group">Bulan Ini</th>
              <th colspan="2" class="th-group">s/d Bulan Ini</th>
            </tr>
            <tr>
              <th class="num" style="min-width:130px">Rp.</th>
              <th class="num" style="min-width:70px">%</th>
              <th class="num" style="min-width:130px">Rp.</th>
              <th class="num" style="min-width:70px">%</th>
              <th class="num" style="min-width:130px">Rp.</th>
              <th class="num" style="min-width:70px">%</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            <tr class="empty-row"><td colspan="7">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
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
@endsection


@section('jsreport')
<script type="text/javascript">
  let defaultBulan = new Date().getMonth() + 1;  // 1–12
  let defaultTahun = new Date().getFullYear();

  let g_reportTitle = "";
  let lastRows = [];   // hasil fetch terakhir (dipakai render / search)

  // Base total untuk kolom % (diisi triggerSp sebelum doReport).
  let tempTotalA = 0, tempTotalB = 0, tempTotalC = 0;

  // Report mode dipakai engine masterreport2 (doSetHeader) — cukup satu int.
  g_modeReport = 24;

  const reportUrl  = "{{ url('laporanaccountinglabarugi_doReport') }}";
  const triggerUrl = "{{ url('laporanaccountinglabarugi_triggerSp') }}";

  // Susunan kolom (urutan mengikuti header dua tingkat di markup). Hanya kolom Rp
  // (TotalA/TotalB/TotalC) yang ditotal; kolom % (P1/P2/P3) tidak ditotal.
  const COLS = [
    { key: 'keterangan', label: 'Keterangan',  type: 'str', dec: 0, total: false },
    { key: 'TotalA',     label: 'Rp.',         type: 'num', dec: 2, total: true  },
    { key: 'P1',         label: '%',           type: 'num', dec: 2, total: false },
    { key: 'TotalB',     label: 'Rp.',         type: 'num', dec: 2, total: true  },
    { key: 'P2',         label: '%',           type: 'num', dec: 2, total: false },
    { key: 'TotalC',     label: 'Rp.',         type: 'num', dec: 2, total: true  },
    { key: 'P3',         label: '%',           type: 'num', dec: 2, total: false },
  ];

  $(document).ready(function () {
    populatePeriodSelectors();
    loadDivisiDropdown();   // isi dropdown Divisi (default: divisi pertama)

    // KPI + chart dipaint kosong (Rp 0 / chart kosong) sebelum data pertama dimuat.
    buildKpis([]);
    buildCharts([]);

    // Sengaja TIDAK memuat data saat halaman dibuka — laporan hanya dimuat setelah
    // pengguna klik tombol "Tampilkan".
  });

  // Header sederhana untuk menjaga engine masterreport2 tetap terinisialisasi.
  // Tabel styled di-render sendiri oleh render() dari COLS.
  function setDefaultHeader() {
    gcart_header = COLS.map(c => [c.key, c.label, 1, (c.type === 'num' ? 'float' : c.type), (c.total ? 1 : 0), c.dec]);
    gsum_issubtotal = 1; gsum_isgrandtotal = 1;
  }

  /* ── PERIODE (Bulan / Tahun) ── */
  const NAMA_BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  function populatePeriodSelectors() {
    const selB = document.getElementById('periodBulan');
    const selT = document.getElementById('periodTahun');
    selB.innerHTML = NAMA_BULAN.map((nama, i) =>
      `<option value="${i + 1}" ${(i + 1) == defaultBulan ? 'selected' : ''}>${nama}</option>`).join('');
    const thisYear = new Date().getFullYear();
    let years = '';
    for (let y = thisYear; y >= thisYear - 6; y--) {
      years += `<option value="${y}" ${y == defaultTahun ? 'selected' : ''}>${y}</option>`;
    }
    selT.innerHTML = years;
  }
  // Hanya perbarui nilai periode; TIDAK memuat data (tunggu klik "Tampilkan").
  function changePeriodParts() {
    defaultBulan = parseInt(document.getElementById('periodBulan').value, 10);
    defaultTahun = parseInt(document.getElementById('periodTahun').value, 10);
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
    const header = ['Keterangan', 'Bulan Lalu Rp.', 'Bulan Lalu %',
                    'Bulan Ini Rp.', 'Bulan Ini %', 's/d Bulan Ini Rp.', 's/d Bulan Ini %'];
    const body = (lastRows || []).map(r => COLS.map(function (c) {
      const v = pickCI(r, c.key);
      if (c.type === 'num') return currencyNormalizer(v);
      return (v == null ? '' : v);
    }));
    const rows = [header].concat(body);
    const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const ext = (fmt === 'Excel') ? 'xls' : 'csv';
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'LabaRugi_' + defaultBulan + '-' + defaultTahun + '.' + ext;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    showToast('📄', 'Data diekspor sebagai ' + fmt);
  }

  /* ── LOAD DATA: triggerSp (base total) → sp_ReportLabaRugi ── */
  function triggerSp(bulan, tahun, divisi) {
    tempTotalA = 0; tempTotalB = 0; tempTotalC = 0;
    $.ajax({
      url: triggerUrl, type: 'get', async: false,
      data: { bulan: bulan, tahun: tahun, divisi: divisi },
      success: function (res) {
        if (res && res[0]) {
          tempTotalA = res[0].totalA || 0;
          tempTotalB = res[0].totalB || 0;
          tempTotalC = res[0].totalC || 0;
        }
      }
    });
  }

  function makeTable(_mode) {
    g_reportTitle = 'REPORT LABA RUGI';
    let _divisi = $('#inputDivisi').val() || '-';

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    // 1) ambil base total untuk kolom %  2) ambil baris laba rugi
    triggerSp(defaultBulan, defaultTahun, _divisi);

    const data = {
      inputBulan: defaultBulan, inputTahun: defaultTahun, divisi: _divisi,
      totalA: tempTotalA, totalB: tempTotalB, totalC: tempTotalC
    };

    $.ajax({
      url: reportUrl, type: 'get', data: data,
      success: function (res) {
        lastRows = Array.isArray(res) ? res : ((res && res.res1) ? res.res1 : []);
        $('#searchBox2').val('');
        render();
        buildKpis(lastRows);
        buildCharts(lastRows);
      },
      error: function () {
        lastRows = [];
        render();
        buildKpis(lastRows);
        buildCharts(lastRows);
      }
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

  /* ── KPI + CHARTS (dibangun sisi-klien dari lastRows; kolom Persen/Kelompok dari SP) ── */
  const CHART_PALETTE = ['#4F46E5','#7C3AED','#DB2777','#2563eb','#16a34a','#ca8a04','#ea580c','#0891b2','#e11d48','#65a30d'];
  // [label, Persen, warna latar ikon, class <i> untuk ikon Bootstrap]
  // Slot ikon sengaja dikosongkan — tambahkan class bi-* pada elemen <i> di markup
  // hasil render (mis. class="kpi-icon-pendapatan bi bi-cash-stack").
  const KPI_DEFS = [
    ['Pendapatan',  'A', '#ede9fe', 'kpi-icon-pendapatan bi bi-currency-dollar'],
    ['HPP',         'B', '#fee2e2', 'kpi-icon-hpp bi bi-briefcase-fill'],
    ['Laba Kotor',  'C', '#dcfce7', 'kpi-icon-labakotor bi bi-graph-up'],
    ['Laba Bersih', 'D', '#dbeafe', 'kpi-icon-lababersih bi bi-bar-chart-fill'],
  ];
  let _charts = {};

  // Nilai negatif ditampilkan sebagai positif di KPI & chart (tabel tetap apa adanya).
  function absNum(v) { const n = currencyNormalizer(v); return n < 0 ? n * -1 : n; }

  function fmtShort(v) {
    v = Math.round(num(v)); const a = Math.abs(v);
    if (a >= 1e9) return (v / 1e9).toFixed(1).replace(/\.0$/, '') + ' M';
    if (a >= 1e6) return (v / 1e6).toFixed(1).replace(/\.0$/, '') + ' jt';
    if (a >= 1e3) return (v / 1e3).toFixed(0) + ' rb';
    return String(v);
  }
  function _destroyChart(id) { if (_charts[id]) { _charts[id].destroy(); delete _charts[id]; } }

  /* ── KPI: TotalB dari baris Persen A/B/C/D ── */
  function buildKpis(rows) {
    const byPersen = {};
    (rows || []).forEach(r => {
      const p = str(pickCI(r, 'Persen')).toUpperCase();
      if (p) byPersen[p] = r;
    });

    let html = '';
    KPI_DEFS.forEach(function (d) {
      const label = d[0], persen = d[1], tint = d[2], iconCls = d[3];
      const row = byPersen[persen];
      const val = row ? absNum(pickCI(row, 'TotalB')) : 0;
      html += '<div class="kpi-card">' +
        '<div class="kpi-ic" style="background:' + tint + '"><i class="' + iconCls + '"></i></div>' +
        '<div><div class="kpi-label">' + label + '</div>' +
        '<div class="kpi-val">Rp ' + fmtShort(val) + '</div></div>' +
        '</div>';
    });
    document.getElementById('kpiStrip').innerHTML = html;
  }

  /* ── Chart 1 (kiri): Pendapatan (Kelompok 3) vs Beban (Kelompok 4), dijumlah per periode.
     Chart 2 (kanan): Komposisi Beban — satu bar per baris Kelompok 4 (TotalB, tanpa dijumlah). ── */
  function buildCharts(rows) {
    if (typeof Chart === 'undefined') return;   // gagal muat Chart.js → lewati (tabel tetap jalan)
    try {
      Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
      Chart.defaults.font.size = 12;
      Chart.defaults.color = '#64748b';

      // Dijumlah dulu apa adanya, baru tandanya dibuang — supaya total bersih
      // (mis. beban yang saling menghapus) tidak melar akibat abs per baris.
      const sum = (kelompok, key) => Math.abs((rows || [])
        .filter(r => str(pickCI(r, 'Kelompok')) === kelompok)
        .reduce((acc, r) => acc + currencyNormalizer(pickCI(r, key)), 0));

      const pendapatan = [sum('3', 'TotalA'), sum('3', 'TotalB'), sum('3', 'TotalC')];
      const beban      = [sum('4', 'TotalA'), sum('4', 'TotalB'), sum('4', 'TotalC')];

      _destroyChart('lr1');
      _charts.lr1 = new Chart(document.getElementById('lrChart1'), {
        type: 'bar',
        data: {
          labels: ['Bulan Lalu', 'Bulan Ini', 's/d Bulan Ini'],
          datasets: [
            { label: 'Pendapatan', data: pendapatan, backgroundColor: '#4F46E5', borderRadius: 6 },
            { label: 'Beban',      data: beban,      backgroundColor: '#DB2777', borderRadius: 6 },
          ]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: (c) => ' ' + c.dataset.label + ': ' + fmtShort(c.parsed.y) } }
          },
          scales: { y: { ticks: { callback: (v) => fmtShort(v) } } }
        }
      });

      // Baris Keterangan == "HPP" (persis, case-sensitive) dikeluarkan dari chart —
      // "HPP Barang Terjual" dkk tetap ikut karena bukan match persis.
      const bebanRows = (rows || []).filter(r =>
        str(pickCI(r, 'Kelompok')) === '4' &&
        currencyNormalizer(pickCI(r, 'TotalB')) !== 0 &&
        str(pickCI(r, 'Keterangan')) !== 'HPP');

      _destroyChart('lr2');
      _charts.lr2 = new Chart(document.getElementById('lrChart2'), {
        type: 'bar',
        data: {
          labels: bebanRows.map(r => str(pickCI(r, 'Keterangan'))),
          datasets: [{
            label: 'Beban',
            data: bebanRows.map(r => absNum(pickCI(r, 'TotalB'))),
            backgroundColor: bebanRows.map((r, i) => CHART_PALETTE[i % CHART_PALETTE.length]),
            borderRadius: 6
          }]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: { callbacks: { title: (items) => items[0].label, label: (c) => ' ' + fmtShort(c.parsed.y) } }
          },
          scales: {
            x: { ticks: { display: false } },
            y: { ticks: { callback: (v) => fmtShort(v) } }
          }
        }
      });
    } catch (e) { console.error('buildCharts', e); }
  }

  /* ── RENDER: baris laba rugi apa adanya (urutan dari SP) + Grand Total pada kolom Rp. ── */
  function render() {
    const tbody = document.getElementById('tableBody');
    const search = ($('#searchBox2').val() || '').trim().toLowerCase();

    const rows = (lastRows || []).filter(r => !search || rowSearchText(r).indexOf(search) !== -1);

    if (!rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + COLS.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    const totals = COLS.map(() => 0);   // total per kolom (per index)
    let html = '';

    rows.forEach(r => {
      COLS.forEach((c, idx) => { if (c.total) totals[idx] += currencyNormalizer(pickCI(r, c.key)); });
      html += '<tr class="data-row">' + COLS.map(function (c) {
        const v = pickCI(r, c.key);
        if (c.type === 'num') {
          const n = currencyNormalizer(v);
          return '<td class="num">' + (n === 0 ? '' : format_number(n, c.dec)) + '</td>';
        }
        return '<td>' + nullToEmpty(v) + '</td>';
      }).join('') + '</tr>';
    });

    if (gsum_isgrandtotal === 1) html += grandRow(totals);

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
  }

  // Baris Grand Total: label di kolom Keterangan, total di kolom Rp (TotalA/B/C),
  // kolom % dikosongkan.
  function grandRow(totals) {
    const firstTotal = COLS.findIndex(c => c.total);
    let html = '<tr class="grand-total"><td colspan="' + firstTotal + '">GRAND TOTAL</td>';
    for (let i = firstTotal; i < COLS.length; i++) {
      const c = COLS[i];
      html += c.total
        ? '<td class="num">' + format_number(totals[i], c.dec) + '</td>'
        : '<td></td>';
    }
    html += '</tr>';
    return html;
  }

  /* ── PENCARIAN SISI-KLIEN ── */
  function applyFilters() { render(); }

  function rowSearchText(r) {
    return COLS.map(function (c) {
      const v = pickCI(r, c.key);
      return (v == null ? '' : String(v));
    }).join(' ').toLowerCase();
  }

  /* ── TOAST ── */
  function showToast(icon, msg) {
    const t = document.getElementById('toast');
    document.getElementById('ti').textContent = icon;
    document.getElementById('tm').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  function getKolomFilter() { return ['keterangan']; }

  /* ── DROPDOWN DIVISI (sumber loadDivisi; default: divisi pertama) ── */
  function loadDivisiDropdown() {
    let list = [];
    $.ajax({
      url: "{!! url('laporanaccountinglabarugi_loaddivisi') !!}",
      type: "get", async: false,
      success: function (res) { list = res || []; }
    });

    let html = '';
    list.forEach((item) => {
      const nama = (item.NamaDevisi != null ? String(item.NamaDevisi) : '').replace(/"/g, '&quot;');
      html += '<li><a class="dropdown-item divisi-item" style="cursor:pointer" ' +
        'data-value="' + item.Devisi + '" data-nama="' + nama + '">' +
        item.Devisi + ' - ' + (item.NamaDevisi != null ? item.NamaDevisi : '') +
        ' <span class="checkmark-red" style="display:none">&#10003;</span></a></li>';
    });
    $("#dropdownDivisi").html(html);

    // default: divisi pertama (tanpa memuat ulang — laporan dimuat saat klik "Tampilkan")
    if (list.length) { applyDivisi(list[0].Devisi, list[0].NamaDevisi != null ? list[0].NamaDevisi : ''); }
  }

  function applyDivisi(kode, nama) {
    $("#inputDivisi").val(kode);
    $("#divisiLabel").text(nama || kode);
    $("#inputDivisiBtn").attr('title', nama || kode);
    $('#dropdownDivisi .checkmark-red').hide();
    $(`#dropdownDivisi .divisi-item[data-value='${kode}'] .checkmark-red`).show();
  }

  // Memilih divisi hanya menyetel filter; TIDAK memuat data (tunggu klik "Tampilkan").
  $(document).on('click', '#dropdownDivisi .divisi-item', function () {
    applyDivisi($(this).data('value'), $(this).data('nama'));
  });
</script>
@endsection
