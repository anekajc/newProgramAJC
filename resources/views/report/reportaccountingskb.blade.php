@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php).
     Laporan SKB (buku tambahan SKB): styled .tb-report, daftar RATA baris ledger (No Bukti, Tanggal,
     Keterangan, Perkiraan, Lawan, Nilai). Filter: Periode (rentang tanggal). Klik di mana saja pada
     baris membuka voucher (CetakKasharian) di panel bawah — Jenis diambil dari No Bukti via
     jenisFromNo. Sumber: sp_ReportBukuTambahanSKB ?,?. Data hanya dimuat setelah klik "Tampilkan".
     Mode "Nomor Barang" lama dihapus (kolomnya tidak cocok dengan SP ini; Order By disembunyikan). --}}
<style>
  /* tinggi awal area tabel supaya dropdown tidak terpotong container pendek */
  .tb-report .table-wrap { min-height: 10vh; }
</style>

@section('header2')
<div class="tb-report main">
  <div class="content">

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div>
        <div class="page-title">Laporan SKB</div>
      </div>

      <!-- Periode (rentang tanggal) -->
      <div class="filter-wrap">
        <label>Periode</label>
        <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
        <span class="filter-sep">s/d</span>
        <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
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

    <!-- TABLE (kolom di-render dari COLS; klik baris membuka voucher) -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead>
            <tr><th>Nomor Bukti</th></tr>
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
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";

  let g_reportTitle = "";
  let lastRows = [];   // hasil fetch terakhir (dipakai render / search)

  // Report mode dipakai engine masterreport2 (doSetHeader) — cukup satu int.
  g_modeReport = 23;

  const reportUrl = "{{ url('laporanaccountingskb_doReport') }}";

  // Bottom voucher panel endpoints (report-table.js dimuat oleh masterreport2).
  // No Bukti buku tambahan → openVoucher(no, jenisFromNo(no)); Jenis diambil dari
  // nomor (segmen ke-2), lalu di-dispatch (Bukti Kas/Bank/Jurnal → CetakKasharian, dst).
  window.ReportTableConfig = {
    kasUrl    : "{{ url('laporanaccountingskb_doKasharian') }}",
    invoiceUrl: "{{ url('laporanaccountingskb_doInvoice') }}",
    lpbUrl    : "{{ url('laporanaccountingskb_doLpb') }}",
    bpUrl     : "{{ url('laporanaccountingskb_doBp') }}"
  };

  // Susunan kolom (mode "Nomor Bukti"). Kolom No Bukti dapat diklik (voucher).
  const COLS = [
    { key: 'NoBukti',    label: 'Nomor Bukti', type: 'str',  dec: 0, voucher: true },
    { key: 'Tanggal',    label: 'Tanggal',     type: 'date', dec: 0 },
    { key: 'Keterangan', label: 'Keterangan',  type: 'str',  dec: 0 },
    { key: 'Perkiraan',  label: 'Perkiraan',   type: 'str',  dec: 0 },
    { key: 'Lawan',      label: 'Lawan',       type: 'str',  dec: 0 },
    { key: 'Nilai',      label: 'Nilai',       type: 'num',  dec: 0 },
  ];

  $(document).ready(function () {
    // Sengaja TIDAK memuat data saat halaman dibuka — laporan hanya dimuat setelah
    // pengguna klik tombol "Tampilkan".
  });

  // Header sederhana untuk menjaga engine masterreport2 tetap terinisialisasi
  // (doSetHeader memanggil ini bila belum ada header tersimpan). Tabel styled
  // di-render sendiri oleh render() dari COLS, jadi gcart_header tidak dipakai untuk layout.
  function setDefaultHeader() {
    gcart_header = COLS.map(c => [c.key, c.label, 1, (c.type === 'num' ? 'float' : c.type), 0, c.dec]);
    gsum_issubtotal = 1; gsum_isgrandtotal = 1;
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
    const header = COLS.map(c => c.label);
    const body = (lastRows || []).map(r => COLS.map(function (c) {
      const v = pickCI(r, c.key);
      if (c.type === 'date') return format_date(v);
      if (c.type === 'num') return currencyNormalizer(v);
      return (v == null ? '' : v);
    }));
    const rows = [header].concat(body);
    const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const ext = (fmt === 'Excel') ? 'xls' : 'csv';
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'SKB_' + (globalDate1 || '') + '_' + (globalDate2 || '') + '.' + ext;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    showToast('📄', 'Data diekspor sebagai ' + fmt);
  }

  /* ── LOAD DATA (sp_ReportBukuTambahanSKB; doReport mengembalikan array biasa) ── */
  function makeTable(_mode) {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
    g_reportTitle = 'REPORT SKB';

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    const data = { date1: globalDate1, date2: globalDate2 };

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

  // No Bukti clickable hanya untuk nomor voucher betulan (mengandung '/', bukan baris
  // "Saldo Awal"). Klik dilakukan pada SELURUH baris (lihat voucherRowOpen); Jenis
  // diambil dari nomor via jenisFromNo → openVoucher men-dispatch ke proc yang tepat.
  function isVoucherNo(v) {
    const s = str(v);
    if (!s || s.indexOf('/') === -1) return false;
    return s.toUpperCase().indexOf('SALDO AWAL') === -1;
  }

  // Kolom No Bukti hanya PETUNJUK visual (garis bawah biru); klik dilakukan pada
  // SELURUH baris karena hanya ada satu kolom yang bisa diklik.
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

  /* ── RENDER: daftar RATA baris ledger (satu baris per transaksi). Kolom & urutan
     dari COLS; No Bukti dapat diklik (buka voucher lewat klik baris). ── */
  function render() {
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');
    const search = ($('#searchBox2').val() || '').trim().toLowerCase();

    // HEADER
    thead.innerHTML = '<tr>' + COLS.map(function (c) {
      return '<th' + (c.type === 'num' ? ' class="num"' : '') + '>' + c.label + '</th>';
    }).join('') + '</tr>';

    const rows = (lastRows || []).filter(r => !search || rowSearchText(r).indexOf(search) !== -1);

    if (!rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + COLS.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '';
    rows.forEach(r => {
      html += voucherRowOpen(pickCI(r, 'NoBukti'), 'data-row') + COLS.map(function (c) {
        const v = pickCI(r, c.key);
        if (c.voucher) return voucherCell(v);
        if (c.type === 'date') return '<td>' + format_date(v) + '</td>';
        if (c.type === 'num') return '<td class="num">' + format_number(currencyNormalizer(v), c.dec) + '</td>';
        return '<td>' + nullToEmpty(v) + '</td>';
      }).join('') + '</tr>';
    });

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
  }

  /* ── PENCARIAN SISI-KLIEN ── */
  function applyFilters() { render(); }

  function rowSearchText(r) {
    return COLS.map(function (c) {
      const v = pickCI(r, c.key);
      return (c.type === 'date') ? format_date(v) : (v == null ? '' : String(v));
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

  function getKolomFilter() { return ['NoBukti', 'Tanggal']; }
</script>
@endsection
