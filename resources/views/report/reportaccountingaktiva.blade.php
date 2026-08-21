@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php).
     Laporan Aktiva (aktiva tetap): styled .tb-report dengan header DUA TINGKAT — kolom awal
     (No. Aktiva, Keterangan, Tgl Perolehan, Qnt, Susut), lalu dua band: "Nilai Perolehan Aktiva
     Tetap" (4 kolom) & "Akumulasi Aktiva Tetap" (4 kolom), lalu "Nilai Buku". Header dua tingkat
     TETAP statis (tidak dinamis dari gcart_header) — sama seperti Laporan Arus/Laba Rugi/Trial
     Balance/Neraca Lajur; Customize Table (toggle Subtotal/Grand Total) dipakai, tapi
     show/hide/reorder kolom dari modalnya tidak berlaku di sini. Baris dikelompokkan per
     GrpPerkiraan (label "GrpPerkiraan - NamaPerkiraan") dengan Subtotal per grup + Grand Total,
     mengikuti toggle Customize Table (gsum_issubtotal/gsum_isgrandtotal). Filter: Bulan/Tahun
     (dropdown) + Divisi (dropdown, default divisi pertama). Tidak ada kolom No Bukti/No Nota →
     tanpa panel voucher. Sumber: sp_LapAktiva :bulan,:tahun,:divisi. Data hanya dimuat setelah
     klik "Tampilkan". --}}
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
        <div class="page-title">Laporan Aktiva</div>
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

    <!-- TABLE — header dua tingkat (band Perolehan / Akumulasi); baris di-render dari makeTable() -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead>
            <tr>
              <th rowspan="2" style="min-width:90px">No. Aktiva</th>
              <th rowspan="2" style="min-width:160px">Keterangan</th>
              <th rowspan="2" style="min-width:100px">Tanggal</th>
              <th rowspan="2" class="num" style="min-width:60px">Qty</th>
              <th rowspan="2" class="num" style="min-width:60px">Susut</th>
              <th colspan="4" class="th-group">Nilai Perolehan Aktiva Tetap</th>
              <th colspan="4" class="th-group">Akumulasi Aktiva Tetap</th>
              <th rowspan="2" class="num" style="min-width:120px">Nilai Buku</th>
            </tr>
            <tr>
              <th class="num" style="min-width:120px">Bulan Lalu</th>
              <th class="num" style="min-width:120px">Penambahan</th>
              <th class="num" style="min-width:120px">Pengurangan</th>
              <th class="num" style="min-width:120px">Bulan Ini</th>
              <th class="num" style="min-width:120px">Bulan Lalu</th>
              <th class="num" style="min-width:120px">Penambahan</th>
              <th class="num" style="min-width:120px">Pengurangan</th>
              <th class="num" style="min-width:120px">Bulan Ini</th>
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
@endsection


@section('jsreport')
<script type="text/javascript">
  let defaultBulan = new Date().getMonth() + 1;  // 1–12
  let defaultTahun = new Date().getFullYear();

  let g_reportTitle = "";
  let lastRows = [];   // hasil fetch terakhir (dipakai render / search)

  // Report mode dipakai engine masterreport2 (doSetHeader) — cukup satu int.
  g_modeReport = 21;

  const reportUrl = "{{ url('laporanaccountingaktiva_doReport') }}";

  // Susunan kolom tabel (urutan mengikuti header dua tingkat di markup).
  // total=true → ikut Grand Total. Catatan: "Susut" (Persen) TIDAK ditotal (persentase).
  const COLS = [
    { key: 'perkiraan',  label: 'No. Aktiva',                     type: 'str',  dec: 0, total: false },
    { key: 'Keterangan', label: 'Keterangan',                     type: 'str',  dec: 0, total: false },
    { key: 'Tanggal',    label: 'Tgl. Perolehan',                 type: 'date', dec: 0, total: false },
    { key: 'Quantity',   label: 'Qnt',                            type: 'num',  dec: 0, total: true  },
    { key: 'Persen',     label: 'Susut',                          type: 'num',  dec: 0, total: false },
    { key: 'awal',       label: 'Perolehan Bulan Lalu',           type: 'num',  dec: 2, total: true  },
    { key: 'MD',         label: 'Perolehan Penambahan Bulan Ini', type: 'num',  dec: 2, total: true  },
    { key: 'MK',         label: 'Perolehan Pengurangan Bulan Ini',type: 'num',  dec: 2, total: true  },
    { key: 'akhir',      label: 'Perolehan Bulan Ini',            type: 'num',  dec: 2, total: true  },
    { key: 'awalSusut',  label: 'Akumulasi Bulan Lalu',           type: 'num',  dec: 2, total: true  },
    { key: 'SK',         label: 'Akumulasi Penambahan Bulan Ini', type: 'num',  dec: 2, total: true  },
    { key: 'SD',         label: 'Akumulasi Pengurangan Bulan Ini',type: 'num',  dec: 2, total: true  },
    { key: 'AkhirSusut', label: 'Akumulasi Bulan Ini',            type: 'num',  dec: 2, total: true  },
    { key: 'NilaiAk',    label: 'Nilai Buku',                     type: 'num',  dec: 0, total: true  },
  ];

  $(document).ready(function () {
    doSetHeader(g_modeReport);   // muat gsum flags tersimpan (toggle Subtotal/Grand Total)
    populatePeriodSelectors();
    loadDivisiDropdown();   // isi dropdown Divisi (default: divisi pertama)

    // Sengaja TIDAK memuat data saat halaman dibuka — laporan hanya dimuat setelah
    // pengguna klik tombol "Tampilkan" (atau memilih filter lalu Tampilkan).
  });

  // Header sederhana untuk menjaga engine masterreport2 tetap terinisialisasi
  // (doSetHeader memanggil ini bila belum ada header tersimpan). Tabel styled
  // di-render sendiri oleh render() dari COLS, jadi gcart_header tidak dipakai untuk layout.
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
    const header = ['Grup Aktiva'].concat(COLS.map(c => c.label));
    const body = (lastRows || []).map(r => [groupLabel(r)].concat(COLS.map(function (c) {
      const v = pickCI(r, c.key);
      if (c.type === 'date') return format_date(v);
      if (c.type === 'num') return currencyNormalizer(v);
      return (v == null ? '' : v);
    })));
    const rows = [header].concat(body);
    const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const ext = (fmt === 'Excel') ? 'xls' : 'csv';
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'Aktiva_' + defaultBulan + '-' + defaultTahun + '.' + ext;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    showToast('📄', 'Data diekspor sebagai ' + fmt);
  }

  /* ── LOAD DATA (sp_LapAktiva; doReport mengembalikan array biasa) ── */
  function makeTable(_mode) {
    g_reportTitle = 'REPORT AKTIVA';

    let _divisi = $('#inputDivisi').val() || '-';

    // muat gsum flags (default / hasil toggle Customize Table tersimpan)
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    const data = { inputBulan: defaultBulan, inputTahun: defaultTahun, divisi: _divisi };

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

  // Label grup: "GrpPerkiraan - NamaPerkiraan" (fallback ke GrpPerkiraan saja bila NamaPerkiraan kosong).
  function groupLabel(r) {
    const kode = str(pickCI(r, 'GrpPerkiraan'));
    const nama = str(pickCI(r, 'NamaPerkiraan'));
    if (kode === '' && nama === '') return '(Tanpa Grup)';
    return nama !== '' ? (kode + ' - ' + nama) : kode;
  }

  /* ── RENDER: dikelompokkan per GrpPerkiraan (label "GrpPerkiraan - NamaPerkiraan"), Subtotal
     per grup + Grand Total — mengikuti toggle Customize Table (gsum_issubtotal/gsum_isgrandtotal).
     Kolom & urutan dari COLS (cocok dengan header dua tingkat statis di markup). ── */
  function render() {
    const tbody = document.getElementById('tableBody');
    const search = ($('#searchBox2').val() || '').trim().toLowerCase();

    const totalKeys = COLS.filter(c => c.total).map(c => c.key);
    const showSub = (gsum_issubtotal === 1);
    const showGrand = (gsum_isgrandtotal === 1);

    // kelompokkan per GrpPerkiraan, pertahankan urutan kemunculan
    const order = [], buckets = {}, labels = {};
    (lastRows || []).forEach(r => {
      if (search && rowSearchText(r).indexOf(search) === -1) return;
      const gkey = str(pickCI(r, 'GrpPerkiraan'));
      if (!(gkey in buckets)) { buckets[gkey] = []; order.push(gkey); labels[gkey] = groupLabel(r); }
      buckets[gkey].push(r);
    });

    if (!order.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + COLS.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '';
    const grand = {}; totalKeys.forEach(k => grand[k] = 0);
    let visible = 0;

    order.forEach(gkey => {
      const rows = buckets[gkey];
      const label = labels[gkey];

      html += '<tr class="group-row"><td colspan="' + COLS.length + '">' + label +
        ' <span style="font-size:11px;font-weight:600;opacity:.7;margin-left:8px">(' + rows.length + ' aktiva)</span></td></tr>';

      const sub = {}; totalKeys.forEach(k => sub[k] = 0);
      rows.forEach(r => {
        totalKeys.forEach(k => { const v = currencyNormalizer(pickCI(r, k)); sub[k] += v; grand[k] += v; });
        html += '<tr class="data-row">' + COLS.map(function (c) {
          const v = pickCI(r, c.key);
          if (c.type === 'date') return '<td>' + format_date(v) + '</td>';
          if (c.type === 'num') return '<td class="num">' + format_number(currencyNormalizer(v), c.dec) + '</td>';
          return '<td>' + nullToEmpty(v) + '</td>';
        }).join('') + '</tr>';
        visible++;
      });

      if (showSub) html += totalRow('Subtotal ' + label, sub, totalKeys, 'subtotal-row');
    });

    if (showGrand) html += totalRow('GRAND TOTAL', grand, totalKeys, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + visible + ' baris';
  }

  // Baris Subtotal/Grand Total: label membentang kolom teks awal (sampai kolom bertotal
  // pertama), lalu nilai total di kolom ber-total (Susut/Persen dikosongkan).
  function totalRow(label, sums, totalKeys, cls) {
    const firstTotal = COLS.findIndex(c => c.total);
    let html = '<tr class="' + cls + '"><td colspan="' + firstTotal + '">' + label + '</td>';
    for (let i = firstTotal; i < COLS.length; i++) {
      const c = COLS[i];
      html += (totalKeys.indexOf(c.key) !== -1)
        ? '<td class="num">' + format_number(sums[c.key], c.dec) + '</td>'
        : '<td></td>';
    }
    html += '</tr>';
    return html;
  }

  /* ── PENCARIAN SISI-KLIEN ── */
  function applyFilters() { render(); }

  function rowSearchText(r) {
    let s = str(pickCI(r, 'GrpPerkiraan')) + ' ' + str(pickCI(r, 'NamaPerkiraan')); // ikutkan label grup
    s += ' ' + COLS.map(function (c) {
      const v = pickCI(r, c.key);
      return (c.type === 'date') ? format_date(v) : (v == null ? '' : String(v));
    }).join(' ');
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

  function getKolomFilter() { return ['perkiraan', 'Keterangan']; }

  /* ── DROPDOWN DIVISI (sumber loadDivisi; default: divisi pertama) ── */
  function loadDivisiDropdown() {
    let list = [];
    $.ajax({
      url: "{!! url('laporanaccountingaktiva_loaddivisi') !!}",
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
