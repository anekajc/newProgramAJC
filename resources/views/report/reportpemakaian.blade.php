@extends('report.masterreport4')

@section('reportname')
      @if ($mode_menu == 'QTY')
      @else
      @endif
@endsection

<!-- <style>
  .checkmark-red {
    color: red !important;
    font-weight: bold;
    margin-left: 6px;
  }

  .tb-report,
  .tb-report *,
  .tb-report *::before,
  .tb-report *::after {
    box-sizing: border-box;
  }

  .tb-report * {
    margin: 0;
    padding: 0;
  }

  :root {
    --bg: #F8FAFF;
    --white: #fff;
    --border: #E2E8F4;
    --text: #0F172A;
    --muted: #5A6A85;
    --accent: #4F46E5;
    --radius: 10px;
    --c-asset: #1D4ED8;
    --c-asset-bg: #EFF6FF;
  }

  .tb-report {
    font-family: 'Segoe UI', system-ui, sans-serif;
    width: 100% !important;
  }

  .tb-report .main {
    display: flex;
    flex-direction: column;
    min-width: 0;
    background: var(--bg);
    width: 100% !important;
  }

  .tb-report .content {
    display: flex;
    flex-direction: column;
    padding: 20px 24px 0;
    width: 100% !important;
  }

  .tb-report .toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    flex-wrap: wrap;
    position: relative;
    z-index: 30;
    width: 100% !important;
  }

  .tb-report .page-title {
    font-size: 19px;
    font-weight: 800;
    color: var(--text);
  }

  .tb-report .page-sub {
    font-size: 12.5px;
    color: var(--muted);
    margin-top: 1px;
  }

  .tb-report .filter-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: 9px;
    padding: 6px 12px;
  }

  .tb-report .filter-wrap label {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
  }

  .tb-report .filter-inp {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 600;
    color: var(--text);
    outline: none;
    padding: 2px 0;
  }

  .tb-report .filter-inp[type=date] { color: var(--accent); font-weight: 700; }
  .tb-report .filter-sep { color: var(--muted); font-size: 12px; }

  .tb-report .action-group { margin-left: auto; display: flex; align-items: center; gap: 10px; }
  
  .tb-report .btn-load {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    border: none;
    color: #fff;
    background: linear-gradient(135deg, #16A34A, #15803D);
    box-shadow: 0 4px 14px rgba(22, 163, 74, .28);
    transition: all .15s;
  }
  .tb-report .btn-load:hover { box-shadow: 0 6px 20px rgba(22, 163, 74, .4); transform: translateY(-1px); }

  .tb-report .search-inp {
    padding: 8px 12px 8px 34px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: 13px;
    background: var(--white);
    color: var(--text);
    outline: none;
    width: 200px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%235A6A85' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: 10px center;
    transition: border-color .14s;
  }

  .tb-report .export-wrap { position: relative; }
  .tb-report .export-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    border: none;
    background: linear-gradient(135deg, #4F46E5, #7C3AED);
    color: #fff;
    box-shadow: 0 4px 14px rgba(79, 70, 229, .28);
    transition: all .15s;
  }
  .tb-report .export-drop {
    display: none; position: absolute; right: 0; top: calc(100% + 8px); background: var(--white);
    border: 1.5px solid var(--border); border-radius: 10px; overflow: hidden; min-width: 170px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .12); z-index: 100;
  }
  .tb-report .export-drop.open { display: block; }
  .tb-report .export-opt {
    display: flex; align-items: center; gap: 10px; padding: 10px 14px; font-size: 13px;
    color: var(--text); cursor: pointer; border-bottom: 1px solid var(--border);
  }
  .tb-report .export-opt:last-child { border-bottom: none; }
  .tb-report .export-opt:hover { background: #EEF2FF; }

  .tb-report .table-outer { display: flex; flex-direction: column; width: 100% !important; margin-top: 15px; }
  .tb-report .table-wrap {
    max-height: 65vh; overflow: auto; background: var(--white);
    border: 1px solid var(--border); border-radius: var(--radius) var(--radius) 0 0; width: 100% !important;
  }
  .tb-report .tb { width: 100% !important; border-collapse: collapse; font-size: 13px; }
  .tb-report .tb thead th {
    position: sticky; top: 0; z-index: 20; background: #FAFBFF; padding: 10px 14px;
    font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase;
    letter-spacing: .06em; border-bottom: 2px solid var(--border) !important; white-space: nowrap; text-align: left;
  }
  .tb-report .tb thead th.num { text-align: right; }
  .tb-report .empty-row td { padding: 28px 14px; text-align: center; color: var(--muted); font-size: 13px; }
  .tb-report .table-footer {
    background: var(--white); border: 1px solid var(--border); border-top: none;
    border-radius: 0 0 var(--radius) var(--radius); padding: 10px 16px; font-size: 12.5px; color: var(--muted);
  }

  .tb-report .tb, .tb-report .tb th, .tb-report .tb td { border: 0 !important; }
  .tb-report .tb thead th     { border-bottom: 2px solid var(--border) !important; }
  .tb-report .data-row td     { border-bottom: 1px solid #F1F5F9 !important; }
</style> -->

@section('header2')
  <div class="tb-report main">
    <div class="content">

      <!-- toolbar-->
      <div class="toolbar">
        <div>
          <div class="page-title">Pemakaian</div>
        </div>
        
        <!-- date range-->
        <div class="filter-wrap">
          <label>Periode</label>
          <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}"> 
          <span class="filter-sep">s/d</span>
          <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
        </div>
        
        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()">

        <div class="action-group">
          <button
            class="btn-load"
            data-bs-toggle="modal"
            data-bs-target="#modalFilter">
            <i class="fas fa-filter"></i> Filter
          </button>
          <button class="btn-load" onclick="makeTable('REPORT')" title="Tampilkan laporan"><i class="fas fa-check"></i> Tampilkan</button>
          <div class="export-wrap" id="exportWrap">
            <button class="export-btn" onclick="toggleExport()"><i class="bi bi-arrow-down"></i> Export <i class="bi bi-caret-down-fill"></i></button>
            <div class="export-drop" id="exportDrop">
              <div class="export-opt" onclick="doExport('Excel')"> Ekspor ke <span class="ext">XLSX</span></div>
              <div class="export-opt" onclick="doExport('Print')"> Cetak Laporan</div>
            </div>
          </div>
        </div>
      </div>

      <!-- tabel-->
      <div class="table-outer">
        <div class="table-wrap">
          <table class="tb" id="mainTable">
            <thead>
            </thead>
            <tbody id="tableBody">
              <tr class="empty-row"><td colspan="8">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
            </tbody>
          </table>
        </div>
        <div class="table-footer">
          <span id="footerLabel">Belum ada data dimuat</span>
        </div>
      </div> 
    </div> 

    <!-- toast-->
    <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
  </div> 

  <!-- modal filter -->
  <div class="modal fade" id="modalFilter">
    <div class="modal-dialog modal-md">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-filter"></i>
            Filter Laporan
          </h5>

          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal">
          </button>
        </div>

        <div class="modal-body">

          <div class="mb-3">
            <label>Otorisasi</label>
            <select class="form-select" id="modalOtorisasi">
              <option value="2">Semua</option>
              <option value="1">Belum</option>
              <option value="0">Sudah</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">

          <button
            class="btn btn-primary"
            onclick="applyModalFilter()">
            Terapkan
          </button>

        </div>

      </div>
    </div>
  </div>
  <!-- modal filter -->

@endsection

@section('jsreport')
<script src="{!! URL::asset('public/js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>

<script type="text/javascript">
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";
  let globalOtorisasi = "2"; 
  let globalOrderBy = "N";   
  let globalReportMode = "0"; 
  var jenisreport = 0; 
  let lastRows = [];
  let currentGroupby = 'NoBukti'; 

  var modereport_detail = 3; 
  g_modeReport = modereport_detail;

  const reportUrl = "{{ url('laporanpemakaian_doReport') }}"; 

  $(document).ready(function() {
    $("#showTableReport table").hide();
    $(".card-body table").hide();
    $(".btn-success, .btn-danger, #btnSubmitReport, .tombol-toggle").attr('style', 'display: none !important;').hide();

    setDefaultHeader();
    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });

  function setDefaultHeader() {
    if ("{!! $mode_menu !!}" == "QTY") {
      if (jenisreport === 1) {
        gcart_header = [
          ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
          ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
          ['KodePerkiraan', 'No. PRK', 1, 'varchar', 0, 0],
          ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
          ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
          ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
          ['Qnt', 'Qnt', 1, 'float', 1, 2],
          ['HPP', 'HPP', 1, 'float', 1, 2],
          ['NilaiHPP', 'HPP X Qnt', 1, 'float', 1, 2]
        ];
      } else {
        gcart_header = [
          ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
          ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
          ['KodePerkiraan', 'No. PRK', 1, 'varchar', 0, 0],
          ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
          ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
          ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
          ['Qnt', 'Qnt', 1, 'float', 1, 2],
          ['HPP', 'HPP', 1, 'float', 1, 2],
          ['NilaiHPP', 'HPP X Qnt', 1, 'float', 1, 2],
          ['StatusOto', 'Otorisasi', 1, 'status', 0, 0]
        ];
      }
    } else {
      gcart_header = [
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
        ['KodePerkiraan', 'No. PRK', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['Qnt', 'Qnt', 1, 'float', 1, 2],
        ['HPP', 'HPP', 1, 'float', 1, 2],
        ['NilaiHPP', 'HPP X Qnt', 1, 'float', 1, 2],
        ['StatusOto', 'Otorisasi', 1, 'status', 0, 0]
      ];
    }
    gsum_issubtotal = 0; 
    gsum_isgrandtotal = 1;

  }

  // Field aslinya (dari laporanpemakaian_doReport): "NeedOtorisasi".
  let _otoFieldWarned = false;
  function getOtoStatusLabel(r) {
    const need = pickCI(r, 'NeedOtorisasi');
    if (need !== undefined && need !== null && need !== '') {
      const s = String(need).trim().toUpperCase();
      const needTruthy = (need === 1 || need === '1' || need === true || s === 'Y' || s === 'YES' || s === 'TRUE');
      return needTruthy
        ? { text: 'Belum', color: '#B91C1C', bg: '#FEE2E2' }
        : { text: 'Sudah', color: '#15803D', bg: '#DCFCE7' } 
    }

    const candidates = ['Otorisasi', 'StatusOtorisasi', 'IsOtorisasi', 'FlagOtorisasi', 'Oto', 'StatusOto', 'Approve', 'Approved', 'IsApproved', 'Disetujui', 'StatusApproval', 'Approval', 'Auth', 'IsAuth', 'Authorized', 'Acc', 'ACC'];
    let raw;
    for (const c of candidates) {
      const v = pickCI(r, c);
      if (v !== undefined && v !== null && v !== '') { raw = v; break; }
    }
    if (raw === undefined) {
      if (!_otoFieldWarned) {
        _otoFieldWarned = true;
        console.warn('[Status Otorisasi] Nama field-nya gak ketemu dari daftar kandidat. Cek nama kolom asli di sini:', Object.keys(r));
        console.warn('[Status Otorisasi] Contoh isi 1 baris data:', r);
      }
      return { text: '(field?)', color: '#B45309', bg: '#FEF3C7' };
    }

    const s = String(raw).trim().toUpperCase();
    const truthy = (raw === 1 || raw === '1' || raw === true || s === 'Y' || s === 'YES' || s === 'OTORISASI' || s === 'SUDAH' || s === 'TRUE');
    return truthy
      ? { text: 'Belum', color: '#B91C1C', bg: '#FEE2E2' }
      : { text: 'Sudah', color: '#15803D', bg: '#DCFCE7' };
  }

  function renderOtoStatusCell(r) {
    const st = getOtoStatusLabel(r);
    return '<td><span style="display:inline-flex; align-items:center; gap:6px; padding:2px 10px; border-radius:999px; font-size:11.5px; font-weight:700; color:' + st.color + '; background:' + st.bg + ';"><span style="width:6px; height:6px; border-radius:50%; background:' + st.color + '; flex-shrink:0;"></span>' + st.text + '</span></td>';
  }

  $('#modalFilter').on('show.bs.modal', function () {
    $("#modalOtorisasi").val(globalOtorisasi);
  });

  function applyModalFilter() {
    setOtorisasi($("#modalOtorisasi").val());

    $('#modalFilter').modal('hide');
  }

  function setOtorisasi(val) { globalOtorisasi = val; }


  function makeTable(_mode) {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();

    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }
    setDefaultHeader();
    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    let filterData = {
      date1: globalDate1,
      date2: globalDate2,
      inputOto: globalOtorisasi,
      inputOrd: globalOrderBy
    };

    $.ajax({
      url: reportUrl, 
      type: 'get', 
      data: filterData,
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

      if (showSub && i !== 0 && prev !== now) {
        html += totalRow('Subtotal', sub, cols, totalKeys, 'subtotal-row');
        totalKeys.forEach(k => sub[k] = 0);
      }

      totalKeys.forEach(function (k) {
        const v = currencyNormalizer(r[k]);
        sub[k] += v; grand[k] += v;
      });

      html += '<tr class="data-row">' + cols.map(function (c) {
        const key = c[0], type = c[3];
        if (type === 'date') return '<td>' + format_date(r[key]) + '</td>';
        if (type === 'status') return renderOtoStatusCell(r);
        if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(r[key]), c[5]) + '</td>';
        return '<td>' + nullToEmpty(r[key]) + '</td>';
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
      if (c[3] === 'status') return getOtoStatusLabel(r).text;
      const v = r[c[0]];
      if (c[3] === 'date') return format_date(v);
      return (v == null ? '' : String(v));
    }).join(' ').toLowerCase();
  }

  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  function doExport(fmt) { if (fmt === 'Print') { window.print(); } }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });
  function getKolomFilter() { return ['NoBukti', 'Tanggal']; }
</script>
@endsection