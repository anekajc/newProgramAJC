@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php).
     Laporan Mutasi Kas & Bank: styled .tb-report table with a two-level header
     (Sampai Dengan Bulan Ini / Bulan Ini). Subtotal per jenis akun (digit pertama ACC)
     + Grand Total dikendalikan lewat toggle Customize Table (gsum_issubtotal/isgrandtotal). --}}
<style>
  #inputPerkiraanBtn {
    border: 0; background: none; padding: 0; box-shadow: none;
    color: #495057; font-weight: 600;
  }
  #inputPerkiraanBtn:hover, #inputPerkiraanBtn:focus { color: #0d6efd; box-shadow: none; }

  /* tinggi awal area tabel supaya dropdown Divisi tidak terpotong container pendek */
  .tb-report .table-wrap { min-height: 10vh; }
</style>

@section('header2')
<div class="tb-report main">
  <div class="content">

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div>
        <div class="page-title">Laporan Arus Kas &amp; Bank</div>
      </div>

      <!-- Periode (Bulan + Tahun, seperti Trial Balance) -->
      <div class="period-select-wrap">
        <label>Periode</label>
        <select class="period-select" id="periodBulan" onchange="setPeriodPart()"></select>
        <select class="period-select" id="periodTahun" onchange="setPeriodPart()"></select>
      </div>

      <!-- Divisi (dropdown; diisi dari reportaccountinglaporanarus_loadperkiraan) -->
      <div class="filter-wrap">
        <label>Divisi</label>
        <input type="hidden" id="inputPerkiraan" value="-">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputPerkiraanBtn"
                data-bs-toggle="dropdown" aria-expanded="false"><span id="perkiraanLabel">-</span></button>
        <ul class="dropdown-menu" id="dropdownPerkiraan" aria-labelledby="inputPerkiraanBtn"
            style="max-height:320px; overflow:auto;"></ul>
      </div>

      <!-- Actions: search + customize + tampilkan + export -->
      <div class="action-group">
        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari akun..." oninput="applyFilters()" style="width:180px">
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

    <!-- TABLE (two-level header: Sampai Dengan Bulan Ini / Bulan Ini) -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead>
            <tr>
              <th rowspan="2" style="width:90px">ACC</th>
              <th rowspan="2">Keterangan</th>
              <th colspan="3" class="th-group" style="min-width:300px">Sampai Dengan Bulan Ini</th>
              <th colspan="3" class="th-group" style="min-width:300px">Bulan Ini</th>
            </tr>
            <tr>
              <th class="num" style="min-width:110px">Nilai Jurnal</th>
              <th class="num" style="min-width:100px">R/K</th>
              <th class="num" style="min-width:110px">Nilai Akhir</th>
              <th class="num" style="min-width:110px">Nilai Jurnal</th>
              <th class="num" style="min-width:100px">R/K</th>
              <th class="num" style="min-width:110px">Nilai Akhir</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            <tr class="empty-row"><td colspan="8">Pilih Periode &amp; Divisi lalu klik <b>Tampilkan</b>.</td></tr>
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
  let defaultBulan = new Date().getMonth() + 1;  // 1-12
  let defaultTahun = new Date().getFullYear();
  let currentPeriod = '';

  let g_reportTitle = "REPORT ACCOUNTING LAPORAN MUTASI KAS & BANK";
  let g_date2 = "", g_inputPerkiraan = "";

  let lastRows = [];   // hasil fetch terakhir (dipakai render / search)

  // Satu mode. Mode di-bump ke 3 supaya header TERSIMPAN versi lama (tanpa total,
  // grand total mati) tidak dipakai lagi; mulai bersih dari setDefaultHeader().
  var modereport_detail = 3, modereport_rekap = 4;
  g_modeReport = modereport_detail;
  var jenisreport = 0;

  const reportUrl = "{{ url('reportaccountinglaporanarus_doReport') }}";

  $(document).ready(function () {
    setDefaultHeader();
    doSetHeader(g_modeReport);      // muat gsum flags (toggle Subtotal/Grand Total tersimpan)
    populatePeriodSelectors();
    loadDivisiDropdown();           // isi dropdown Divisi + pilih divisi pertama

    setTimeout(() => { makeTable('REPORT'); }, 100);
  });

  /* ── gcart_header: dipakai modal Customize Table + persistensi gsum. Kolom numerik
        ditandai total (item[4]=1). Subtotal per jenis akun & Grand Total dikendalikan
        toggle #buttonSubtotal/#buttonGrandtotal (gsum_issubtotal/gsum_isgrandtotal). ── */
  function setDefaultHeader() {
    gcart_header = [
      ['Perkiraan',   'ACC', 1, 'varchar', 0, 0],
      ['Keterangan',  'Keterangan', 1, 'varchar', 0, 0],
      ['Awal',  'S/D Bln - Nilai Jurnal', 1, 'float', 1, 2],
      ['RK',    'S/D Bln - R/K',          1, 'float', 1, 2],
      ['Debet', 'S/D Bln - Nilai Akhir',  1, 'float', 1, 2],
      ['awlsd', 'Bln Ini - Nilai Jurnal', 1, 'float', 1, 2],
      ['rksd',  'Bln Ini - R/K',          1, 'float', 1, 2],
      ['sldsd', 'Bln Ini - Nilai Akhir',  1, 'float', 1, 2],
    ];
    gsum_issubtotal = 1; gsum_isgrandtotal = 1;
  }

  /* ── PERIOD PICKER (Bulan + Tahun, sama seperti Trial Balance) ── */
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
  }

  // Hanya set state; laporan dimuat saat klik Tampilkan (konsisten dgn Divisi).
  function setPeriodPart() {
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
    const rows = [['ACC', 'Keterangan',
      'S/D Bln Nilai Jurnal', 'S/D Bln R/K', 'S/D Bln Nilai Akhir',
      'Bln Ini Nilai Jurnal', 'Bln Ini R/K', 'Bln Ini Nilai Akhir']];
    (lastRows || []).forEach(r0 => {
      const r = lc(r0);
      rows.push([str(r.perkiraan), str(r.keterangan),
        num(r.awal), num(r.rk), num(r.debet), num(r.awlsd), num(r.rksd), num(r.sldsd)]);
    });
    const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const ext = (fmt === 'Excel') ? 'xls' : 'csv';
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'MutasiKasBank_' + currentPeriod.replace('/', '-') + '.' + ext;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    showToast('📄', 'Data diekspor sebagai ' + fmt);
  }

  /* ── LOAD DATA (sp_LapPenerimaanKeuangan; doReport array biasa) ── */
  function makeTable(_mode) {
    const bulan  = defaultBulan, tahun = defaultTahun;
    const divisi = $('#inputPerkiraan').val() || '-';
    const date2  = tahun + '-' + String(bulan).padStart(2, '0');   // YYYY-MM utk controller

    g_date2 = date2; g_inputPerkiraan = divisi;
    currentPeriod = bulan + '/' + tahun;

    // muat gsum flags (default / hasil toggle Customize Table tersimpan)
    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    $.ajax({
      url: reportUrl, type: 'get', data: { date2: date2, inputPerkiraan: divisi },
      success: function (res) {
        lastRows = Array.isArray(res) ? res : ((res && res.res1) ? res.res1 : []);
        $('#searchBox2').val('');
        render();
      },
      error: function () { lastRows = []; render(); document.getElementById('footerLabel').textContent = 'Gagal memuat data laporan.'; }
    });
  }

  /* ── helpers ── */
  function num(v) { if (v === null || v === undefined || v === '') return 0; const n = parseFloat(v); return isNaN(n) ? 0 : n; }
  function str(v) { return (v == null ? '' : String(v)).trim(); }
  function lc(r) { const o = {}; Object.keys(r).forEach(k => { o[k.toLowerCase()] = r[k]; }); return o; }
  function fmt(v) { return format_number(v, 2); }

  const NUMS = ['awal', 'rk', 'debet', 'awlsd', 'rksd', 'sldsd'];

  /* ── RENDER ──
     Baris dikelompokkan berdasarkan NILAI kolom MK (klasifikasi dari SP), dalam
     urutan kemunculannya. Subtotal per grup & Grand Total mengikuti toggle
     Customize Table (gsum_issubtotal/gsum_isgrandtotal), dimuat doSetHeader() saat
     Tampilkan. ── */
  function render() {
    const tbody = document.getElementById('tableBody');
    const showSub   = (gsum_issubtotal === 1);
    const showGrand = (gsum_isgrandtotal === 1);
    const search = ($('#searchBox2').val() || '').trim().toLowerCase();

    // kelompokkan per nilai kolom MK, pertahankan urutan kemunculan.
    // Label grup diambil dari kolom KET (bukan MK-nya, bukan "Aset" dst).
    const order = [];
    const buckets = {};
    const labels = {};
    (lastRows || []).forEach(r0 => {
      const r = lc(r0);
      const code = str(r.perkiraan), name = str(r.keterangan);
      if (search && code.toLowerCase().indexOf(search) === -1 && name.toLowerCase().indexOf(search) === -1) return;
      const gkey = str(r.mk);            // klasifikasi dari kolom MK
      if (!(gkey in buckets)) { buckets[gkey] = []; order.push(gkey); labels[gkey] = str(r.ket); }
      const row = { code: code, name: name };
      NUMS.forEach(k => row[k] = num(r[k]));
      buckets[gkey].push(row);
    });

    let html = '';
    const grand = {}; NUMS.forEach(k => grand[k] = 0);
    let visible = 0;

    order.forEach(gkey => {
      const accs = buckets[gkey];
      const label = (labels[gkey] !== '' ? labels[gkey] : (gkey !== '' ? gkey : 'LAINNYA'));

      // group header (label dari kolom KET)
      html += '<tr class="group-row"><td colspan="8">' + label +
        ' <span style="font-size:11px;font-weight:600;opacity:.7;margin-left:8px">(' + accs.length + ' akun)</span></td></tr>';

      const sub = {}; NUMS.forEach(k => sub[k] = 0);
      accs.forEach(a => {
        NUMS.forEach(k => { sub[k] += a[k]; grand[k] += a[k]; });
        html += '<tr class="data-row">' +
          '<td class="code">' + a.code + '</td>' +
          '<td class="name">' + a.name + '</td>' +
          NUMS.map(k => '<td class="num">' + fmt(a[k]) + '</td>').join('') +
          '</tr>';
        visible++;
      });

      if (showSub) {
        html += '<tr class="subtotal-row"><td colspan="2">Subtotal ' + label + '</td>' +
          NUMS.map(k => '<td class="num">' + fmt(sub[k]) + '</td>').join('') + '</tr>';
      }
    });

    if (!visible) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="8">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    if (showGrand) {
      html += '<tr class="grand-total"><td colspan="2" style="font-weight:800">TOTAL KESELURUHAN</td>' +
        NUMS.map(k => '<td class="num">' + fmt(grand[k]) + '</td>').join('') + '</tr>';
    }

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + visible + ' akun';
  }

  function applyFilters() { render(); }

  /* ── DROPDOWN DIVISI (Bootstrap; diisi dari loadperkiraan) ── */
  function loadDivisiDropdown() {
    let list = [];
    $.ajax({
      url: "{!! url('reportaccountinglaporanarus_loadperkiraan') !!}",
      type: "get", async: false,
      success: function (res) { list = Array.isArray(res) ? res : []; }
    });

    let html = '';
    list.forEach((item) => {
      const nama = (item.NamaDevisi != null ? String(item.NamaDevisi) : '').replace(/"/g, '&quot;');
      html += '<li><a class="dropdown-item divisi-item" style="cursor:pointer" ' +
        'data-value="' + item.Devisi + '" data-nama="' + nama + '">' +
        item.Devisi + ' - ' + (item.NamaDevisi != null ? item.NamaDevisi : '') +
        ' <span class="checkmark-red" style="display:none">&#10003;</span></a></li>';
    });
    $("#dropdownPerkiraan").html(html);

    if (list.length) { setDivisi(list[0].Devisi, list[0].NamaDevisi != null ? list[0].NamaDevisi : ''); }
  }

  function setDivisi(kode, nama) {
    $("#inputPerkiraan").val(kode);
    $("#perkiraanLabel").text(kode);
    $("#inputPerkiraanBtn").attr('title', kode + (nama ? ' - ' + nama : ''));
    $('#dropdownPerkiraan .checkmark-red').hide();
    $(`#dropdownPerkiraan .divisi-item[data-value='${kode}'] .checkmark-red`).show();
  }

  $(document).on('click', '#dropdownPerkiraan .divisi-item', function () {
    setDivisi($(this).data('value'), $(this).data('nama'));
  });

  /* ── TOAST ── */
  function showToast(icon, msg) {
    const t = document.getElementById('toast');
    document.getElementById('ti').textContent = icon;
    document.getElementById('tm').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  /* ── Filter Data engine (opsional) ── */
  function getKolomFilter() { return ['Perkiraan', 'Keterangan']; }
</script>
@endsection
