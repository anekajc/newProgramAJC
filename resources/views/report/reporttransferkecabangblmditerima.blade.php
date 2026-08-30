@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css.
     Layout dirapikan menggunakan .tb-report --}}

@section('header2')
<div class="tb-report main">
  <div class="content">

    <!-- TOOLBAR -->
    <div class="toolbar">
      {{-- <div>
        <div class="page-title">Laporan Transfer Ke Cabang Belum Diterima</div>
      </div> --}}

      <!-- Single Date (Hanya 1 tanggal, tanpa range s/d) -->
      <div class="filter-wrap">
        <label>Tanggal</label>
        <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
      </div>

      {{-- Search --}}
      <div>
        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
      </div>

      {{-- Otorisasi & Order By TIDAK ada di sini: input hidden-nya di halaman lama tidak pernah
           dibaca JS manapun (globalOtorisasi/globalOrderBy sudah hardcode "2"/"N" di JS-nya),
           dan controller (doReport) cuma pernah baca date1 -- proc SP_TransferBlmTerima cuma
           menerima 1 parameter (tgl1). Tidak ada yang genuinely bisa difilter selain Tanggal,
           jadi tombol "Filter Data" & "Customize Table" (modal lama) tidak ada lagi di halaman
           ini -- digantikan #rtBar untuk atur kolom. --}}

      <!-- search + tampilkan + export -->
      <div class="action-group">
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

    <!-- Bar kolom tersembunyi (diisi oleh report-table.js / ReportTable) -->
    <div id="rtBar"></div>

    <!-- TABLE -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead>
          </thead>
          <tbody id="tableBody">
            <tr class="empty-row"><td>Pilih tanggal lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
          </tbody>
        </table>
      </div>
      <div class="table-footer">
        <span id="footerLabel">Belum ada data dimuat</span>
      </div>
    </div>

    <div class="rt-hint">
      <i class="bi bi-info-circle"></i>
      Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk sembunyikan kolom atau atur desimal &amp; total.
    </div>

  </div><!-- /content -->

  <!-- TOAST -->
  <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>

</div><!-- /tb-report -->
@endsection

@section('jsreport')
<script type="text/javascript">
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";
  // Otorisasi & Order By tidak pernah punya kontrol UI sungguhan (input hidden-nya di halaman
  // lama tidak dibaca JS manapun), dan doReport() di controller cuma baca date1 -- dipertahankan
  // sebagai nilai tetap yang dikirim (harmless, tidak dibaca server) sama seperti sebelum migrasi.
  let globalOtorisasi = "2";
  let globalOrderBy = "N";

  let lastRows = []; // hasil fetch terakhir (dipakai render / export / search)
  let currentGroupby = 'NOBUKTI';

  var modereport_nobukti = 0, modereport_barang = 1;
  g_modeReport = modereport_nobukti;

  const reportUrl = "{{ url('laporantransferkecabangblmditerima_doReport') }}";

  $(document).ready(function () {
    // Tabel bergaya lama dari engine masterreport2 (#showTableReport) tidak pernah dipakai
    // halaman ini (kita punya #mainTable/.tb-report sendiri) -- dikosongkan & disembunyikan,
    // sama seperti reportaccountingkasharian.blade.php.
    $("#showTableReport").empty().hide();

    setDefaultHeader();
    doSetHeader(g_modeReport);
    doShowCustomize();

    // Header tabel interaktif: drag-reorder + gear (sembunyikan/desimal/total) + bar
    // "Reset kolom"/kolom tersembunyi. Tidak ada "Tampilan" switcher -- Order By tidak
    // pernah punya kontrol UI sungguhan di halaman ini (selalu "N"), sama seperti sebelum migrasi.
    ReportTable.init({
      table: '#mainTable',
      bar: '#rtBar',
      onChange: function () {
        if (lastRows.length) { applyFilters(); } else { renderRows([], currentGroupby); }
      }
    });

    // setTimeout(() => { makeTable('REPORT'); }, 100);
  });

  function setDefaultHeader() {
    if (g_modeReport == modereport_nobukti) {
      gcart_header = [
        ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['GDGASAL', 'Asal', 1, 'varchar', 0, 0],
        ['GDGTUJUAN', 'Tujuan', 1, 'varchar', 0, 0],
        ['SATUAN', 'Satuan', 1, 'varchar', 0, 0],
        ['QNT', 'Qty', 1, 'float', 1, 2],
        ['QNTTERIMA', 'Qty Terima', 1, 'float', 1, 2],
        ['SISA', 'Qty Sisa', 1, 'float', 1, 2],
        ['hpp', 'HPP', 1, 'float', 1, 2],
        ['TOTHARGA', 'Total', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1;
      gsum_isgrandtotal = 1;
    } else {
      gcart_header = [
        ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['GDGASAL', 'Asal', 1, 'varchar', 0, 0],
        ['GDGTUJUAN', 'Tujuan', 1, 'varchar', 0, 0],
        ['SATUAN', 'Satuan', 1, 'varchar', 0, 0],
        ['QNT', 'Qty', 1, 'float', 1, 2],
        ['QNTTERIMA', 'Qty Terima', 1, 'float', 1, 2],
        ['SISA', 'Qty Sisa', 1, 'float', 1, 2],
        ['hpp', 'HPP', 1, 'float', 1, 2],
        ['TOTHARGA', 'Total', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1;
      gsum_isgrandtotal = 1;
    }
  }

  // EXPORT ENGINE
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
      if (c[3] === 'date') return format_date(v);
      if (c[3] === 'float' || c[3] === 'int') return currencyNormalizer(v);
      return (v == null ? '' : v);
    }));
    const rows = [header].concat(body);
    const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const ext = (fmt === 'Excel') ? 'xls' : 'csv';
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'TransferCabang_' + (globalDate1 || '') + '.' + ext;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    showToast('📄', 'Data diekspor sebagai ' + fmt);
  }

  // MAKE TABLE ENGINE
  function makeTable(_mode) {
    globalDate1 = $("#inputDate1").val();
    globalDate2 = globalDate1;

    setDefaultHeader();
    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }

    const filterData = {
      date1: globalDate1,
      date2: globalDate2,
      inputOto: globalOtorisasi,
      inputOrd: globalOrderBy
    };

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    $.ajax({
      url: reportUrl, type: 'get', data: filterData,
      success: function (res) {
        lastRows = Array.isArray(res) ? res : ((res && res.res1) ? res.res1 : []);
        $('#searchBox2').val('');
        renderRows(lastRows, currentGroupby);
      },
      error: function () {
        lastRows = [];
        renderRows([], currentGroupby);
      }
    });
  }

  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1);
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');

    const totalCols = cols.filter(c => (c[3] === 'float' || c[3] === 'int') && c[4] === 1);
    const totalKeys = totalCols.map(c => c[0]);
    const hasTotal  = totalCols.length > 0;
    const showSub   = hasTotal && (gsum_issubtotal === 1);
    const showGrand = hasTotal && (gsum_isgrandtotal === 1);

    // Header dinamis -- dibangun report-table.js (ReportTable) supaya kolom bisa diseret
    // untuk diurutkan & punya menu roda gigi (sembunyikan / desimal / total).
    thead.innerHTML = ReportTable.headHtml(cols);

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

      if (showSub && i !== 0 && prev !== now) {
        html += totalRow('Subtotal', sub, cols, totalKeys, 'subtotal-row');
        totalKeys.forEach(k => sub[k] = 0);
      }

      totalKeys.forEach(function (k) {
        const v = currencyNormalizer(pickCI(r, k));
        sub[k] += v; grand[k] += v;
      });

      html += '<tr class="data-row">' + cols.map(function (c) {
        const key = c[0], type = c[3];
        if (type === 'date') return '<td>' + format_date(pickCI(r, key)) + '</td>';
        if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(pickCI(r, key)), c[5]) + '</td>';
        return '<td>' + nullToEmpty(pickCI(r, key)) + '</td>';
      }).join('') + '</tr>';

      prev = now;
    });

    if (showSub)   html += totalRow('Subtotal', sub, cols, totalKeys, 'subtotal-row');
    if (showGrand) html += totalRow('GRAND TOTAL', grand, cols, totalKeys, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
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

  function pickCI(r, key) {
    if (r[key] !== undefined) return r[key];
    const lk = String(key).toLowerCase();
    for (const k in r) { if (k.toLowerCase() === lk) return r[k]; }
    return undefined;
  }

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
      const v = pickCI(r, c[0]);
      if (c[3] === 'date') return format_date(v);
      return (v == null ? '' : String(v));
    }).join(' ').toLowerCase();
  }

  function showToast(icon, msg) {
    const t = document.getElementById('toast');
    document.getElementById('ti').textContent = icon;
    document.getElementById('tm').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }
</script>
@endsection
