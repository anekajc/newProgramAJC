@extends('report.masterreportGudang')
@include('report.modalBrowseMaster')

@section('header2')
<style>
  /* popup "pilih data" dari dalam modal filter. ini dibuat manual pakai z-index di atas modal bootstrap */
  .modal-picker-backdrop {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0, 0, 0, .5);
    z-index: 1071;
  }
  .modal-picker-backdrop.show { display: block; }
  .modal-picker {
    display: none;
    position: fixed; inset: 0;
    z-index: 1072;
    overflow-x: hidden; overflow-y: auto;
    outline: 0;
  }
  .modal-picker.show { display: block; }
  .modal-picker .modal-dialog {
    margin: 1.75rem auto;
  }

  #tabelPickMaster,
  #tabelPickMaster th,
  #tabelPickMaster td {
    border: 0 !important;
  }
  #tabelPickMaster thead th {
    border-bottom: 2px solid var(--border) !important;
  }
  #tabelPickMaster tbody tr.pick-row td {
    border-bottom: 1px solid #F1F5F9 !important;
  }
</style>

  <div class="tb-report main">
    <div class="content">

      <!-- toolbar -->
      <div class="toolbar">
        <div>
          <div class="page-title">
            @if ($mode_menu == 'QTY')
              Kartu Stok (Qty)
            @else
              Kartu Stok (Qty + Rp)
            @endif
          </div>
        </div>

        <!-- periode -->
        <div class="filter-wrap">
          <label>Periode</label>
          <input type="month" class="filter-inp" id="inputDate1" value="{!! date('Y-m') !!}">
          <span class="filter-sep">s/d</span>
          <input type="month" class="filter-inp" id="inputDate2" value="{!! date('Y-m') !!}">
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
              <div class="export-opt" onclick="doExport('Excel')"><i class="bi bi-journals text-success"></i> Ekspor ke <span class="ext">XLSX</span></div>
              <div class="export-opt" onclick="doExport('Print')"><i class="bi bi-printer-fill text-warning"></i> Cetak Laporan</div>
            </div>
          </div>
        </div>
      </div>

      <!-- tabel -->
      <div class="table-outer">
        <div class="table-wrap">
          <table class="tb" id="mainTable">
            <thead>
            </thead>
            <tbody id="tableBody">
              <tr class="empty-row"><td id="emptyColspan" colspan="8">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
            </tbody>
          </table>
        </div>
        <div class="table-footer">
          <span id="footerLabel">Belum ada data dimuat</span>
        </div>
      </div>
    </div>

    <!-- toast -->
    <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
  </div>

  <!-- modal filter: Gudang, Barang, No Satuan -->
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
            <label for="inputGudang" class="mb-1">Gudang</label>
            <select id="inputGudang" class="form-control form-select">
              <option value="-" selected>-- Semua Gudang --</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="inputBarang" class="mb-1">Barang</label>
            <div class="input-group">
              <input type="text"
                     id="inputBarang"
                     class="form-control"
                     placeholder="Kode Barang"
                     value="-"
                     onkeypress="onKeyPressBarang(event)">
              <button type="button" class="btn btn-primary"
                      onclick="buttonAddListBarang()">
                <i class="bi bi-plus"></i>
              </button>
            </div>
          </div>

          <div class="mb-3">
            <label for="inputIsi" class="mb-1">No Satuan</label>
            <input type="number"
                   id="inputIsi"
                   class="form-control"
                   value="1">
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

  <!-- modal pilih barang -->
  <div class="modal-picker-backdrop" id="modalPickMasterBackdrop" onclick="closeBarangPicker()"></div>
  <div class="modal-picker rt-picker-v2" id="modalPickMaster" tabindex="-1" role="dialog" aria-labelledby="modalPickMasterLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPickMasterLabel">Pilih Barang</h5>
          <button type="button" class="btn-close" aria-label="Close" onclick="closeBarangPicker()"></button>
        </div>
        <div class="modal-body">
          <table id="tabelPickMaster">
            <thead>
              <tr>
                <th scope="col">Kode Barang</th>
                <th scope="col">Nama Barang</th>
              </tr>
            </thead>
            <tbody id="tabelPickMaster_data"></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeBarangPicker()">Batal</button>
        </div>
      </div>
    </div>
  </div>
  <!-- modal pilih Barang -->

@endsection


@section('jsreport')
<script src="{!! URL::asset('public/js/ajc-browsemaster.js') !!}"></script>
<script src="{!! URL::asset('public/js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>

<script type="text/javascript">
  var modereport_qty = 0, modereport_qtyrp = 1;
  g_modeReport = ("{!! $mode_menu !!}" == "QTY") ? modereport_qty : modereport_qtyrp;

  let globalDate1 = "{!! date('Y-m') !!}";
  let globalDate2 = "{!! date('Y-m') !!}";
  let lastRows = [];

  if (typeof loadingHtml !== 'function') {
    window.loadingHtml = function (msg) {
      return '<span style="display:inline-flex;align-items:center;gap:6px;color:#5A6A85;">' +
             '<i class="fas fa-spinner fa-spin"></i> ' + (msg || 'Memuat...') + '</span>';
    };
  }
  let currentGroupby = 'NoBukti';

  const reportUrl = "{{ url('laporanstockkartustock_doReport') }}";
  const urlLoadBarang = "{{ url('functionbrowse_doLoadBarang') }}";
  const urlListGudang = "{!! $gudang !!}";

  let gInfoGudang = "-", gInfoBarang = "-", gInfoLokasi = "-";

  /* gudang (pakai dropdown) */
  function loadGudangDropdown() {
    $.ajax({
      url: urlListGudang,
      type: 'get',
      success: function (res) {
        const rows = Array.isArray(res) ? res : ((res && res.table) || []);
        let rowSelect = '<option value="-" selected>-- Semua Gudang --</option>';

        rows.forEach(function (item) {
          const kode = item.KODEGDG || item.KodeGdg || item.kode || '';
          const nama = item.NAMA || item.NAMAGDG || item.Namagdg || item.KETERANGAN || item.nama || '';
          if (!kode) return;
          rowSelect += '<option value="' + kode + '">' + kode + (nama ? (' - ' + nama) : '') + '</option>';
        });

        $('#inputGudang').html(rowSelect);
      },
      error: function () {
        console.error('loadGudangDropdown: gagal memuat daftar gudang');
      }
    });
  }

  let barangTableDT = null;
  let barangCacheAll = null;
  let barangLookupBusy = false;

  function normalizeBarangList(res) {
    if (Array.isArray(res)) {
      return res.map(function (item) {
        return {
          KODEBRG: item.KODEBRG || item.KodeBrg || item.kode || '',
          NAMABRG: item.NAMABRG || item.NamaBrg || item.nama || ''
        };
      });
    }

    const kolom = (res && res.kolom) || [];
    const rows = (res && res.table) || [];
    const codeKey = kolom.length ? kolom[0][0] : 'KODEBRG';
    const nameKey = kolom.length > 1 ? kolom[1][0] : 'NAMABRG';

    return rows.map(function (item) {
      return {
        KODEBRG: item[codeKey] || '',
        NAMABRG: item[nameKey] || ''
      };
    });
  }

  function fetchBarangList(callback) {
    $.ajax({
      url: '{!! $barang !!}',
      type: 'get',
      async: true,
      success: function (res) {
        const list = normalizeBarangList(res);
        if (callback) callback(list);
      },
      error: function (err) {
        console.error('fetchBarangList: gagal memuat daftar barang', err);
      }
    });
  }

  function initBarangTable(list, searchTerm) {
    if ($.fn.DataTable.isDataTable('#tabelPickMaster')) {
      $('#tabelPickMaster').DataTable().clear().destroy();
    }

    barangTableDT = $('#tabelPickMaster').DataTable({
      data: list,
      deferRender: true,
      paging: list.length > 10,
      lengthChange: false,
      searching: true,
      order: [],
      language: { emptyTable: 'Tidak ada data' },
      columns: [
        { data: 'KODEBRG' },
        { data: 'NAMABRG' }
      ],
      createdRow: function (row, data, dataIndex) {
        row.className = 'pick-row';
        row.style.cursor = 'pointer';
        row.setAttribute('onclick', 'buttonPickBarangInsert(' + dataIndex + ')');
      }
    });

    barangTableDT.search(searchTerm || '').draw();
  }

  function openBarangPicker(term) {
    $('#modalPickMasterBackdrop').addClass('show');
    $('#modalPickMaster').addClass('show').attr('aria-hidden', 'false');

    if (barangCacheAll) {
      requestAnimationFrame(function () { initBarangTable(barangCacheAll, term); });
      return;
    }

    $('#tabelPickMaster_data').html('<tr><td colspan="2">' + loadingHtml('Memuat data...') + '</td></tr>');

    fetchBarangList(function (list) {
      barangCacheAll = list;
      requestAnimationFrame(function () { initBarangTable(list, term); });
    });
  }

  function closeBarangPicker() {
    $('#modalPickMaster').removeClass('show').attr('aria-hidden', 'true');
    $('#modalPickMasterBackdrop').removeClass('show');
  }

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('#modalPickMaster').hasClass('show')) {
      closeBarangPicker();
    }
  });

  function resolveBarang(term) {
    term = (term || '').trim();

    if (!term || term === '-') {
      openBarangPicker('');
      return;
    }

    if (barangLookupBusy) { return; }

    const findExact = function (list) {
      const needle = term.toLowerCase();
      return list.find(function (b) { return String(b.KODEBRG || '').trim().toLowerCase() === needle; });
    };

    if (barangCacheAll) {
      const hit = findExact(barangCacheAll);
      if (hit) { applyBarangSelection(hit); } else { openBarangPicker(term); }
      return;
    }

    barangLookupBusy = true;
    fetchBarangList(function (list) {
      barangLookupBusy = false;
      barangCacheAll = list;
      const hit = findExact(list);
      if (hit) { applyBarangSelection(hit); } else { openBarangPicker(term); }
    });
  }

  function onKeyPressBarang(e) {
    if (e.which === 13) {
      e.preventDefault();
      resolveBarang($('#inputBarang').val());
    }
  }

  function buttonAddListBarang() {
    resolveBarang($('#inputBarang').val());
  }

  function applyBarangSelection(item) {
    $('#inputBarang').val(item.KODEBRG);
  }

  function buttonPickBarangInsert(index) {
    const item = barangTableDT ? barangTableDT.row(index).data() : null;
    closeBarangPicker();
    if (item) { applyBarangSelection(item); }
  }

  $(document).ready(function() {
    $("#showTableReport table").hide();
    $(".card-body table").hide();
    $(".btn-success, .btn-danger, #btnSubmitReport, .tombol-toggle").attr('style', 'display: none !important;').hide();

    setDefaultHeader();
    setEmptyColspan();
    loadGudangDropdown();

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });

  function setEmptyColspan() {
    let n = (g_modeReport == modereport_qty) ? 8 : 12;
    let el = document.getElementById('emptyColspan');
    if (el) el.setAttribute('colspan', n);
  }

  function setDefaultHeader() {
    if (g_modeReport == modereport_qty) {
      gcart_header = [
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
        ['Tipe', 'Tipe', 1, 'varchar', 0, 0],
        ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0],
        ['SATUAN', 'Sat', 1, 'varchar', 0, 0],
        ['QntDB', 'Masuk', 1, 'float', 1, 2],
        ['QntCR', 'Keluar', 1, 'float', 1, 2],
        ['QntSaldo', 'Saldo', 1, 'sumfloat', 0, 2]
      ];
    } else {
      gcart_header = [
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
        ['Tipe', 'Tipe', 1, 'varchar', 0, 0],
        ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0],
        ['SATUAN', 'Sat', 1, 'varchar', 0, 0],
        ['HPP', 'HPP', 1, 'float', 0, 2],
        ['QntDB', 'Masuk (Qty)', 1, 'float', 1, 2],
        ['HrgDebet', 'Masuk (Rp)', 1, 'float', 1, 2],
        ['QntCR', 'Keluar (Qty)', 1, 'float', 1, 2],
        ['HrgKredit', 'Keluar (Rp)', 1, 'float', 1, 2],
        ['QntSaldo', 'Saldo (Qty)', 1, 'sumfloat', 0, 2],
        ['HrgSaldo', 'Saldo (Rp)', 1, 'sumfloat', 0, 2]
      ];
    }

    gsum_issubtotal = 0;
    gsum_isgrandtotal = 1;
  }

  function applyModalFilter() {
    $('#modalFilter').modal('hide');
  }

  function loadInfoHeader() {
    let _kodeBarang = $("#inputBarang").val();
    let _barangRes  = null;

    gInfoGudang = "-";
    gInfoBarang = "-";
    gInfoLokasi = "-";

    const _selGudangText = $("#inputGudang option:selected").text();
    if ($("#inputGudang").val() !== '-') { gInfoGudang = _selGudangText; }

    $.ajax({
      url: urlLoadBarang,
      type: "get",
      async: false,
      data: { kode: _kodeBarang },
      success: function (res) {
        if (res && res.length > 0) {
          _barangRes  = res;
          gInfoBarang = res[0].KODEBRG + ' - ' + res[0].NAMABRG;
        }
      }
    });

    if (_barangRes && _barangRes.length > 0) {
      $.ajax({
        url: urlLoadBarang,
        type: "get",
        async: false,
        data: { kode: _barangRes[0].KODEBRG },
        success: function (res) {
          if (res && res.length > 0) { gInfoLokasi = nullToEmpty(res[0].KETERANGAN) || "-"; }
        }
      });
    }
  }

  function buildInfoRows(colspan) {
    return '<tr class="info-row"><th colspan="' + colspan + '" style="text-align:left;">Gudang : ' + gInfoGudang + '</th></tr>' +
           '<tr class="info-row"><th colspan="' + colspan + '" style="text-align:left;">Barang : ' + gInfoBarang + ' &nbsp;&nbsp;&nbsp; Lokasi : ' + gInfoLokasi + '</th></tr>';
  }

  function buildTheadQty(cols) {
    let html = buildInfoRows(cols.length);
    html += '<tr>' + cols.map(function (c) {
      const isNum = (c[3] === 'float' || c[3] === 'int' || c[3] === 'sumfloat');
      return '<th' + (isNum ? ' class="num"' : '') + '>' + c[1] + '</th>';
    }).join('') + '</tr>';
    return html;
  }

  function buildTheadQtyRp(cols) {
    let html = buildInfoRows(cols.length);
    let _thopen = '<th rowspan="2" class="text-center">', _thclose = '</th>';

    html += '<tr>';
    html += _thopen + 'Tanggal' + _thclose;
    html += _thopen + 'No. Bukti' + _thclose;
    html += _thopen + 'Tipe' + _thclose;
    html += _thopen + 'Keterangan' + _thclose;
    html += _thopen + 'Sat' + _thclose;
    html += _thopen + 'HPP' + _thclose;
    html += '<th colspan="2" class="text-center">Masuk</th>';
    html += '<th colspan="2" class="text-center">Keluar</th>';
    html += '<th colspan="2" class="text-center">Saldo</th>';
    html += '</tr>';

    html += '<tr>';
    html += '<th class="num">Quantity</th><th class="num">Rupiah</th>';
    html += '<th class="num">Quantity</th><th class="num">Rupiah</th>';
    html += '<th class="num">Quantity</th><th class="num">Rupiah</th>';
    html += '</tr>';

    return html;
  }

  function makeTable(_mode) {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();

    setDefaultHeader();
    setEmptyColspan();
    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    let filterData = {
      date1       : globalDate1,
      date2       : globalDate2,
      inputGudang : $("#inputGudang").val(),
      inputBarang : $("#inputBarang").val(),
      inputIsi    : $("#inputIsi").val()
    };

    loadInfoHeader();

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

    const totalCols = cols.filter(c => (c[3] === 'float' || c[3] === 'int' || c[3] === 'sumfloat') && (c[4] === 1 || c[3] === 'sumfloat'));
    const totalKeys = totalCols.map(c => c[0]);
    const hasTotal  = totalCols.length > 0;
    const showSub   = hasTotal && (gsum_issubtotal === 1);
    const showGrand = hasTotal && (gsum_isgrandtotal === 1);

    thead.innerHTML = (g_modeReport == modereport_qty) ? buildTheadQty(cols) : buildTheadQtyRp(cols);

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
        if (type === 'float' || type === 'int' || type === 'sumfloat') return '<td class="num">' + format_number(currencyNormalizer(r[key]), c[5]) + '</td>';
        return '<td>' + nullToEmpty(r[key]) + '</td>';
      }).join('') + '</tr>';

      prev = now;
    });

    if (showSub)   html += totalRow('Subtotal', sub, cols, totalKeys, 'subtotal-row');
    if (showGrand) html += totalRow('Total', grand, cols, totalKeys, 'grand-total');

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

  function getKolomFilter() {
    return ['NoBukti', 'Tanggal'];
  }
</script>

@endsection