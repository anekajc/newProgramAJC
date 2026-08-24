@extends('report.masterreport2')
@include('report.modalAccountingJurnal')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php).
     Laporan Biaya: styled .tb-report, satu baris per Perkiraan (akun beban) — tabel RATA (tanpa grup)
     + Grand Total. Filter: Bulan/Tahun (dropdown), Divisi (dropdown, default divisi pertama),
     Perkiraan Awal/Akhir (rentang akun, modal). Tidak ada kolom No Bukti/No Nota → tanpa panel voucher.
     Sumber: Sp_LapBiaya :divisi,:bulan,:tahun,:perk1,:perk2. --}}
<style>
  .checkmark-red { color: red !important; font-weight: bold; margin-left: 6px; }

  #inputDivisiBtn {
    border: 0; background: none; padding: 0; box-shadow: none;
    color: #495057; font-weight: 600;
  }
  #inputDivisiBtn:hover, #inputDivisiBtn:focus { color: #0d6efd; box-shadow: none; }

  /* tinggi awal area tabel supaya dropdown tidak terpotong container pendek */
  .tb-report .table-wrap { min-height: 10vh; }
</style>

@section('header2')
<div class="tb-report main">
  <div class="content">

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div>
        <div class="page-title">Laporan Biaya</div>
        <div class="page-sub">Dicetak oleh: {{ $akses['user'] }} &nbsp;&middot;&nbsp; <span id="printTime"></span></div>
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

      <!-- Perkiraan range (modal — buttonSelect di modalAccountingJurnal) -->
      <div class="filter-wrap">
        <label>Perkiraan</label>
        <input type="text" class="filter-inp" id="inputPerkiraan1" value="-" style="width:80px">
        <button type="button" class="btn-pick" onclick="buttonSelect('selectPerkiraan1')">+</button>
        <span class="filter-sep">s/d</span>
        <input type="text" class="filter-inp" id="inputPerkiraan2" value="-" style="width:80px">
        <button type="button" class="btn-pick" onclick="buttonSelect('selectPerkiraan2')">+</button>
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

    <!-- TABLE (kolom di-render dari gcart_header; tabel rata + grand total) -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead>
            <tr><th>Perkiraan</th></tr>
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

@endsection


@section('jsreport')
<script type="text/javascript">
  let defaultBulan = new Date().getMonth() + 1;  // 1–12
  let defaultTahun = new Date().getFullYear();

  let g_reportTitle = "";
  let lastRows = [];   // hasil fetch terakhir (dipakai render / search)

  // Report mode dipakai engine masterreport2 (doSetHeader) — cukup satu int.
  g_modeReport = 20;

  const reportUrl = "{{ url('laporanaccountingbiaya_doReport') }}";

  $(document).ready(function () {
    document.getElementById('printTime').textContent = new Date().toLocaleString('id-ID');
    populatePeriodSelectors();
    loadDivisiDropdown();   // isi dropdown Divisi (default: divisi pertama)

    // Sengaja TIDAK memuat data saat halaman dibuka — laporan hanya dimuat setelah
    // pengguna klik tombol "Tampilkan" (atau memilih filter lalu Tampilkan).
  });

  /* ── kolom (gcart_header). Satu baris per Perkiraan (akun beban). Kolom uang bertanda
        total (item[4]=1) ikut Grand Total. Catatan: mengikuti setelan lama — "Bulan Ini"
        (BulanKini) TIDAK ditotal, sedangkan Bulan Lalu / Naik-Turun / Sampai Bulan Ini ditotal. ── */
  function setDefaultHeader() {
    gcart_header = [
      ['perkiraan', 'Perkiraan', 1, 'varchar', 0, 0],
      ['keterangan', 'Keterangan', 1, 'varchar', 0, 0],
      ['BulanLalu', 'Bulan Lalu', 1, 'float', 1, 2],
      ['Persen', 'Naik/Turun', 1, 'float', 1, 2],
      ['BulanKini', 'Bulan Ini', 1, 'float', 0, 0],
      ['sdBulanini', 'Sampai Bulan Ini', 1, 'float', 1, 0],
    ];
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
    const cols = gcart_header.filter(c => c[2] === 1);
    const header = cols.map(c => c[1]);
    const body = (lastRows || []).map(r => cols.map(function (c) {
      const v = pickCI(r, c[0]);
      if (c[3] === 'float' || c[3] === 'int') return currencyNormalizer(v);
      return (v == null ? '' : v);
    }));
    const rows = [header].concat(body);
    const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const ext = (fmt === 'Excel') ? 'xls' : 'csv';
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'Biaya_' + defaultBulan + '-' + defaultTahun + '.' + ext;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    showToast('📄', 'Data diekspor sebagai ' + fmt);
  }

  /* ── LOAD DATA (Sp_LapBiaya; doReport mengembalikan array biasa) ── */
  function makeTable(_mode) {
    g_reportTitle = 'REPORT BIAYA';

    let _perk1 = $('#inputPerkiraan1').val() || '-';
    let _perk2 = $('#inputPerkiraan2').val() || '-';
    let _divisi = $('#inputDivisi').val() || '-';

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    const data = {
      inputBulan: defaultBulan, inputTahun: defaultTahun,
      divisi: _divisi, inputPerkiraan1: _perk1, inputPerkiraan2: _perk2
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

  /* ── RENDER: tabel RATA (satu baris per Perkiraan, tanpa grup) + Grand Total.
     Kolom dinamis dari gcart_header (item[2]===1). Grand Total menjumlahkan kolom
     ber-total (item[4]=1) dan mengikuti gsum_isgrandtotal. ── */
  function render() {
    const cols  = gcart_header.filter(c => c[2] === 1);
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');

    const totalCols = cols.filter(c => (c[3] === 'float' || c[3] === 'int') && c[4] === 1);
    const totalKeys = totalCols.map(c => c[0]);
    const hasTotal  = totalCols.length > 0;
    const showGrand = hasTotal && (gsum_isgrandtotal === 1);
    const search = ($('#searchBox2').val() || '').trim().toLowerCase();

    // HEADER dinamis
    thead.innerHTML = '<tr>' + cols.map(function (c) {
      const isNum = (c[3] === 'float' || c[3] === 'int');
      return '<th' + (isNum ? ' class="num"' : '') + '>' + c[1] + '</th>';
    }).join('') + '</tr>';

    const rows = (lastRows || []).filter(r => !search || rowSearchText(r, cols).indexOf(search) !== -1);

    if (!rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '';
    const grand = {}; totalKeys.forEach(k => grand[k] = 0);

    rows.forEach(r => {
      totalKeys.forEach(k => { grand[k] += currencyNormalizer(pickCI(r, k)); });
      html += '<tr class="data-row">' + cols.map(function (c) {
        const type = c[3];
        const v = pickCI(r, c[0]);
        if (type === 'date') return '<td>' + format_date(v) + '</td>';
        if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(v), c[5]) + '</td>';
        return '<td>' + nullToEmpty(v) + '</td>';
      }).join('') + '</tr>';
    });

    if (showGrand) html += totalRow('GRAND TOTAL', grand, cols, totalKeys, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
  }

  // Baris total: nilai di tiap kolom pada `sumKeys`; label di kolom pertama non-sum.
  function totalRow(label, sums, cols, sumKeys, cls) {
    const labelIdx = cols.findIndex(c => sumKeys.indexOf(c[0]) === -1);
    const tds = cols.map(function (c, idx) {
      if (sumKeys.indexOf(c[0]) !== -1) return '<td class="num">' + format_number(sums[c[0]], c[5]) + '</td>';
      if (idx === labelIdx) return '<td>' + label + '</td>';
      return '<td></td>';
    });
    return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
  }

  /* ── PENCARIAN SISI-KLIEN ── */
  function applyFilters() { render(); }

  function rowSearchText(r, cols) {
    let s = '';
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

  function getKolomFilter() { return ['perkiraan', 'keterangan']; }

  /* Rentang Perkiraan memakai modal bersama (buttonSelect di modalAccountingJurnal.blade.php)
     yang langsung menulis ke #inputPerkiraan1 / #inputPerkiraan2. */

  /* ── DROPDOWN DIVISI (sumber loadDivisi; default: divisi pertama) ── */
  function loadDivisiDropdown() {
    let list = [];
    $.ajax({
      url: "{!! url('laporanaccountingbiaya_loaddivisi') !!}",
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

    // default: divisi pertama
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
