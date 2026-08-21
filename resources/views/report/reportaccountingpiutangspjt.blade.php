@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php).
     Piutang Sisa Per Jatuh Tempo: styled .tb-report, dikelompokkan per pelanggan (NamaCust)
     dengan subtotal + grand total (Jumlah/Bayar/Sisa Rp). Filter: Periode (rentang tanggal),
     Perkiraan (dropdown), Pelanggan Awal/Akhir (modal). Klik No Nota membuka voucher (Invoice
     penjualan) di panel bawah (report-table.js). Mode tunggal IDR — tanpa Valas / Order By. --}}
<style>
  #inputPerkiraanBtn {
    border: 0; background: none; padding: 0; box-shadow: none;
    color: #495057; font-weight: 600;
  }
  #inputPerkiraanBtn:hover, #inputPerkiraanBtn:focus { color: #0d6efd; box-shadow: none; }

  /* tinggi awal area tabel supaya dropdown tidak terpotong container pendek */
  .tb-report .table-wrap { min-height: 10vh; }
</style>

@section('header2')
<div class="tb-report main">
  <div class="content">

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div>
        <div class="page-title">Piutang Sisa Per Jatuh Tempo</div>
        <div class="page-sub">Dicetak oleh: {{ $akses['user'] }} &nbsp;&middot;&nbsp; <span id="printTime"></span></div>
      </div>

      <!-- Periode (rentang tanggal) -->
      <div class="filter-wrap">
        <label>Periode</label>
        <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
        <span class="filter-sep">s/d</span>
        <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
      </div>

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

    <!-- TABLE (header + rows rendered dynamically from gcart_header; grouped per pelanggan) -->
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
  let globalDate2 = "{!! date('Y-m-d') !!}";

  let g_reportTitle = "";
  let g_inputPerkiraan = "";

  let lastRows = [];   // hasil fetch terakhir (dipakai render / search)

  // Report ini mode tunggal (tanpa Valas). Mode NUMERIK: DBSIMPANHEADER.reportmode itu
  // kolom integer, jadi mode string membuat header (termasuk toggle Subtotal/Grand Total)
  // TIDAK tersimpan. Int di sini scoped per href, jadi bebas dipakai ulang di halaman lain.
  var modereport_detail = 14;
  g_modeReport = modereport_detail;
  var jenisreport = 0, DetOrRekap = 0;

  const reportUrl = "{{ url('reportaccountingpiutangspjt_doReport') }}";

  // Bottom voucher panel endpoints (report-table.js is loaded via masterreport2).
  // Clicking a No Nota calls openVoucher(no, jenisFromNo(no)); for piutang the No Nota
  // is a sales Invoice (INVC) → CetakInvoicePenjualan.
  window.ReportTableConfig = {
    kasUrl    : "{{ url('reportaccountingpiutangspjt_doKasharian') }}",
    invoiceUrl: "{{ url('reportaccountingpiutangspjt_doInvoice') }}",
    lpbUrl    : "{{ url('reportaccountingpiutangspjt_doLpb') }}",
    bpUrl     : "{{ url('reportaccountingpiutangspjt_doBp') }}"
  };

  $(document).ready(function () {
    document.getElementById('printTime').textContent = new Date().toLocaleString('id-ID');
    setModeReport();                   // muat gcart_header + customize
    loadPerkiraanDropdown();           // isi dropdown Perkiraan (default akun PT pertama)

    setTimeout(() => { makeTable('REPORT'); }, 100);
  });

  /* ── kolom (gcart_header). Tabel styled DI-RENDER dari sini (Customize Table).
        Kolom uang (Jumlah/Bayar/Sisa Rp) ditandai total (item[4]=1) → ikut Subtotal &
        Grand Total. Kolom pelanggan (NamaCust) jadi header grup, tidak ditampilkan lagi. ── */
  function setDefaultHeader() {
    gcart_header = [
      ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
      ['JatuhTempo', 'Jatuh Tempo', 1, 'date', 0, 0],
      ['Nofaktur', 'No Nota', 1, 'varchar', 0, 0],
      ['Jumlah', 'Jumlah Rp', 1, 'float', 1, 2],
      ['Terbayar', 'Bayar Rp', 1, 'float', 1, 2],
      ['sisa', 'Sisa Rp', 1, 'float', 1, 2],
    ];
    gsum_issubtotal = 1; gsum_isgrandtotal = 1;
  }

  function setModeReport() {
    g_modeReport = modereport_detail;
    doSetHeader(g_modeReport);   // muat susunan kolom (default / kustomisasi tersimpan)
    doShowCustomize();
  }

  /* ── toolbar controls ── */
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
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
    const header = ['Pelanggan'].concat(cols.map(c => c[1]));
    const body = (lastRows || []).map(r => [str(pickCI(r, 'NamaCust'))].concat(cols.map(function (c) {
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
    a.download = 'PiutangSPJT_' + (globalDate1 || '') + '_' + (globalDate2 || '') + '.' + ext;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    showToast('📄', 'Data diekspor sebagai ' + fmt);
  }

  /* ── LOAD DATA (sp_ReportSisaHutangTempo; doReport mengembalikan array biasa) ── */
  function makeTable(_mode) {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
    g_reportTitle = 'REPORT ACCOUNTING PIUTANG LAPORAN SISA PIUTANG PER JATUH TEMPO';

    let _perk   = $('#inputPerkiraan').val() || '-';
    let _suppAw = $('#inputSuppAwal').val()  || '-';
    let _suppAk = $('#inputSuppAkhir').val() || '-';

    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    const data = {
      date1: globalDate1, date2: globalDate2,
      inputSuppAwal: _suppAw, inputSuppAkhir: _suppAk,
      inputPerkiraan: _perk
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

  // No Nota cell: clickable only for a real voucher number (has '/', and is not an
  // opening "Saldo Awal" row). Opens the bottom voucher panel (report-table.js),
  // dispatching by the Jenis parsed from the number itself.
  function isVoucherNo(v) {
    const s = str(v);
    if (!s || s.indexOf('/') === -1) return false;
    return s.toUpperCase().indexOf('SALDO AWAL') === -1;
  }
  // Kolom No Nota kini hanya PETUNJUK visual (garis bawah biru); klik dilakukan pada
  // SELURUH baris (lihat voucherRowOpen) karena hanya ada satu kolom yang bisa diklik.
  function voucherCell(v) {
    const s = str(v);
    if (!isVoucherNo(s)) return '<td>' + nullToEmpty(v) + '</td>';
    return '<td class="kas-clickable" style="color:#0d6efd;text-decoration:underline">' + nullToEmpty(v) + '</td>';
  }

  // Tag <tr> pembuka baris data: bila memuat nomor voucher betulan, SELURUH baris
  // bisa diklik untuk membuka voucher (report-table.js).
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

  /* ── RENDER: dikelompokkan per pelanggan (kolom `NamaCust`). Subtotal & Grand Total
     menjumlahkan semua kolom ber-total (item[4]=1). ── */
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

    // kelompokkan per pelanggan (NamaCust), pertahankan urutan kemunculan
    const order = [], buckets = {};
    (lastRows || []).forEach(r => {
      if (search && rowSearchText(r, cols).indexOf(search) === -1) return;
      const gkey = str(pickCI(r, 'NamaCust'));
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
        html += voucherRowOpen(pickCI(r, 'nofaktur'), 'data-row') + cols.map(function (c) {
          const key = c[0], type = c[3];
          const v = pickCI(r, key);
          if (type === 'date') return '<td>' + format_date(v) + '</td>';
          if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(v), c[5]) + '</td>';
          if (String(key).toLowerCase() === 'nofaktur') return voucherCell(v);
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
    let s = str(pickCI(r, 'NamaCust'));   // ikutkan nama pelanggan
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

  /* ── DROPDOWN PERKIRAAN (akun PT; default akun pertama) ── */
  function loadPerkiraanDropdown() {
    let list = [];
    $.ajax({
      url: "{!! url('reportaccountingpiutangspjt_loadperkiraan') !!}",
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

    // pelanggan difilter per perkiraan → reset pilihan pelanggan saat perkiraan berubah
    $('#inputSuppAwal').val('-');
    $('#inputSuppAkhir').val('-');

    $('#dropdownPerkiraan .checkmark-red').hide();
    $(`#dropdownPerkiraan .perkiraan-item[data-value='${kode}'] .checkmark-red`).show();
  }

  $(document).on('click', '#dropdownPerkiraan .perkiraan-item', function () {
    setPerkiraan($(this).data('value'), $(this).data('ket'));
  });

  /* ── MODAL PELANGGAN AWAL ── */
  function buttonSelectSuppAwal() { loadSelectSuppAwal(); $("#formSelectSuppAwal").modal('toggle'); }
  function buttonPilihSuppAwal(kode) { $("#inputSuppAwal").val(kode); $("#formSelectSuppAwal").modal('hide'); }

  function loadSelectSuppAwal() {
    let perkiraan = $("#inputPerkiraan").val();
    let dataRefresh = [];
    if ($.fn.DataTable.isDataTable('#tabelSelectSuppAwal')) { $('#tabelSelectSuppAwal').DataTable().destroy(); }

    $.ajax({
      url: "{!! url('reportaccountingpiutangspjt_loadsuppawal') !!}",
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
      url: "{!! url('reportaccountingpiutangspjt_loadsuppawal') !!}",
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
</script>
@endsection
