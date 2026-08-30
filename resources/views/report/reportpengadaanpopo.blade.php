@extends('report.masterreport2')

<style>
    .tb-report .table-wrap { min-height: 15vh; }
</style>

@section('header2')
  <div class="tb-report main">
      <div class="content">

        <!-- TOOLBAR -->
        <div class="toolbar">
          {{-- <div>
            <div class="page-title">PO</div>
          </div> --}}

          <!-- Periode (date range) -->
          <div class="filter-wrap">
            <label>Periode</label>
            <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
            <span class="filter-sep">s/d</span>
            <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
          </div>

          <!-- Actions: search + filter modal + tampilkan + export -->
          <div class="action-group">
            <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
            {{-- Dibuka lewat plugin jQuery (Bootstrap 4), BUKAN data-bs-toggle (Bootstrap 5).
                 Halaman ini memuat dua Bootstrap; jQuery dimuat SESUDAH bundle BS5, jadi
                 $.fn.modal dipegang BS4. applyModalFilter() menutup modal ini dengan
                 $('#modalFilter').modal('hide'), jadi pembukanya harus API yang sama. --}}
            <button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
              <i class="fas fa-filter"></i> Filter
            </button>
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
                <tr>
                  <th>No. Bukti</th>
                </tr>
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

        <div class="rt-hint">
          <i class="bi bi-info-circle"></i>
          Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk sembunyikan
          kolom atau atur desimal &amp; total.
        </div>

      </div><!-- /content -->

      <!-- TOAST -->
      <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
    </div><!-- /tb-report -->

    {{-- Modal DILETAKKAN DI LUAR .tb-report supaya reset `.tb-report *{margin:0;padding:0}`
     di report-table.css tidak merusak padding/margin modal Bootstrap. --}}

    <!-- modal filter -->
  <div class="modal fade rt-filter" id="modalFilter">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-filter"></i>
                    Filter Laporan
                    <span class="rt-active-badge" id="filterBadge">0 aktif</span>
                </h5>
                {{-- data-dismiss (BS4) = yang benar-benar menutup, karena modal ini dibuka lewat
                     $.fn.modal milik BS4. data-bs-dismiss dibiarkan untuk jaga-jaga. --}}
                <button type="button" class="btn-close" aria-label="Close" data-dismiss="modal" data-bs-dismiss="modal"
                    onclick="$('#modalFilter').modal('hide')"></button>
            </div>

            <div class="modal-body">

                <div class="rt-section">
                    <div class="rt-group-label">Pengaturan Laporan</div>
                    <div class="rt-grid-2">
                        <div>
                            <label class="rt-field-label" for="modalReport">Report</label>
                            <select class="rt-native" id="modalReport">
                                <option value="0">Detail</option>
                                <option value="1">Rekap</option>
                            </select>
                        </div>
                        <div>
                            <label class="rt-field-label" for="modalValas">VALAS</label>
                            <select class="rt-native" id="modalValas">
                                <option value="0">IDR</option>
                                <option value="1">VLS</option>
                            </select>
                        </div>
                    </div>
                    <div class="rt-grid-2">
                        <div>
                            <label class="rt-field-label" for="modalOtorisasi">Otorisasi</label>
                            <select class="rt-native" id="modalOtorisasi">
                                <option value="2">Semua</option>
                                <option value="1">Belum Otorisasi</option>
                                <option value="0">Sudah Otorisasi</option>
                                <option value="3">Diterima</option>
                                <option value="4">Menunggu</option>
                                <option value="5">Sebagian</option>
                                <option value="6">Batal</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="rt-reset-link" onclick="resetAllFilters()">Reset semua</button>
                <div class="rt-footer-buttons">
                    <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" data-bs-dismiss="modal"
                        onclick="$('#modalFilter').modal('hide')">Batal</button>
                    <button type="button" class="rt-btn rt-btn-primary" onclick="applyModalFilter()">Terapkan</button>
                </div>
            </div>

          </div>
      </div>
  </div>
<!-- modal filter -->

@endsection

@section('jsreport')
<script type="text/javascript">

  let DetOrRekap = 0;
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";
  let globalOtorisasi = "2"; // default: Semua
  let globalReportMode = "0"; // default: Detail
  let globalValas = "0";
  let lastRows = [];         // hasil fetch terakhir (dipakai renderRows / export / search)
  let currentGroupby = 'NoBukti'; // groupby aktif untuk render ulang saat search

  var modereport_detailidr = 0;
  var modereport_detailvls = 1;
  var modereport_rekapidr  = 2;
  var modereport_rekapvls  = 3;

  g_modeReport = modereport_detailidr;

  $(document).ready(function () {
      setOtorisasi(globalOtorisasi);
      setValas(globalValas);
      setReportMode(globalReportMode);

      setDefaultHeader();
      doSetHeader(g_modeReport);
      doShowCustomize();

      // Header tabel interaktif: drag-reorder + gear (sembunyikan/desimal/total) + bar
      // "Reset kolom"/kolom tersembunyi. Tidak ada "Tampilan" switcher di #rtBar -- Report
      // (Detail/Rekap) & VALAS tetap di modal Filter seperti sebelumnya karena keduanya
      // dua dimensi independen (4 kombinasi gcart_header), bukan satu switcher tunggal.
      ReportTable.init({
        table: '#mainTable',
        bar: '#rtBar',
        onChange: function () {
          if (lastRows.length) { applyFilters(); } else { renderRows([], currentGroupby); }
        }
      });
  });

  $('#modalFilter').on('show.bs.modal', function () {
    $("#modalReport").val(globalReportMode);
    $("#modalValas").val(globalValas);
    $("#modalOtorisasi").val(globalOtorisasi);
    updateFilterBadge();
  });

  $('#modalFilter').on('change', 'select.rt-native', updateFilterBadge);

  function updateFilterBadge() {
    let count = 0;
    // Report & VALAS: pilihan wajib tanpa nilai netral -> sengaja tidak dihitung
    if ($('#modalOtorisasi').val() !== '2') { count++; }
    $('#filterBadge').text(count + ' aktif');
  }

  function resetAllFilters() {
    $('#modalReport').val('0');
    $('#modalValas').val('0');
    $('#modalOtorisasi').val('2');
    updateFilterBadge();
  }

  function applyModalFilter() {

    setReportMode($("#modalReport").val());
    setValas($("#modalValas").val());
    setOtorisasi($("#modalOtorisasi").val());

    $('#modalFilter').modal('hide');
  }

  // periode
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
  }

  // otorisasi
  function setOtorisasi(val) {
    globalOtorisasi = val;
  }

  /* -- EXPORT -- */
  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });
  function doExport(fmt) {
    document.getElementById('exportDrop').classList.remove('open');
    if (fmt === 'Print') { window.print(); }
  }

  // valas
  function setValas(val) {
    globalValas = val;
  }

  function setReportMode(val) {
    globalReportMode = val;
    DetOrRekap = Number(val);
  }

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailidr) {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Sat', 1, 'varchar', 0, 0],
        ['Qnt', 'Qnt', 1, 'float', 1, 2],
        ['HARGA', 'Harga', 1, 'float', 1, 2],
        ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
        ['DISCP', 'Disc', 1, 'float', 1, 2],
        ['NDPP', 'DPP', 1, 'float', 1, 2],
        ['NPPN', 'PPN', 1, 'float', 1, 2],
        ['TotalIDR', 'Total', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['DiTerima', 'Di Terima', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if (g_modeReport == modereport_detailvls){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Sat', 1, 'varchar', 0, 0],
        ['Qnt', 'Qnt', 1, 'float', 1, 2],
        ['HARGA', 'Harga', 1, 'float', 1, 2],
        ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
        ['disctotusd', 'Disc', 1, 'float', 1, 2],
        ['Ndppusd', 'DPP', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN', 1, 'float', 1, 2],
        ['totalusd', 'Total', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['DiTerima', 'Di Terima', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if(g_modeReport == modereport_rekapidr){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['HARGA', 'Harga', 1, 'float', 1, 2],
        ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
        ['DISCP', 'Disc', 1, 'float', 1, 2],
        ['NDPP', 'DPP', 1, 'float', 1, 2],
        ['NPPN', 'PPN', 1, 'float', 1, 2],
        ['TotalIDR', 'Total', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['DiTerima', 'Di Terima', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else if(g_modeReport == modereport_rekapvls){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['HARGA', 'Harga', 1, 'float', 1, 2],
        ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
        ['disctotusd', 'Disc', 1, 'float', 1, 2],
        ['Ndppusd', 'DPP', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN', 1, 'float', 1, 2],
        ['totalusd', 'Total', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['DiTerima', 'Di Terima', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['HARGA', 'Harga', 1, 'float', 1, 2],
        ['KODEVLS', 'VLS', 1, 'varchar', 0, 0],
        ['disctotusd', 'Disc', 1, 'float', 1, 2],
        ['Ndppusd', 'DPP', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN', 1, 'float', 1, 2],
        ['totalusd', 'Total', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['DiTerima', 'Di Terima', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    }
  }

  const reportUrl = "{{ url('laporanpurchaseorderpo_doReport') }}"
  function makeTable(_mode) {
    let groupby = 'NoBukti';
    let _date1 = $("#inputDate1").val();
    let _date2 = $("#inputDate2").val();

    let input_valas = globalValas;

    // BELUM DIPERBAIKI (di luar cakupan migrasi ini -- lihat catatan yang dilaporkan ke
    // user): kondisi kedua di bawah cek "0" lagi (harusnya "1"), jadi memilih VALAS "VLS"
    // TIDAK PERNAH mengganti g_modeReport/kolom ke varian *vls -- sudah begini sejak sebelum
    // migrasi.
    if (input_valas == "0") {
      if (DetOrRekap === 0) {
        g_modeReport = modereport_detailidr;
        groupby = 'NoBukti';
      } else {
        g_modeReport = modereport_rekapidr;
        groupby = 'NoBukti';
      }
    } else if (input_valas == "0") {
      if (DetOrRekap === 0) {
        g_modeReport = modereport_detailvls;
        groupby = 'NoBukti';
      } else {
        g_modeReport = modereport_rekapvls;
        groupby = 'NoBukti';
      }
    }

    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let inputOtoSP = globalOtorisasi;

    if (['3', '4', '5', '6'].includes(globalOtorisasi)) {
      inputOtoSP = '2';   // Semua
    }

    let data = {
      date1: _date1,
      date2: _date2,
      inputOto: inputOtoSP,
      inputOrd: 'N',
      inputDetOrRekap: DetOrRekap,
      inputValas: globalValas
    };

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    // Ambil data SEKALI, lalu render langsung ke tabel styled baru (#tableBody).
    $.ajax({
      url    : reportUrl,
      type   : 'get',
      data   : data,
      success: function (res) {
      let rows = res || [];

        if (globalOtorisasi === '3') {
          rows = rows.filter(r => getStatusDiterima(r) === 'Diterima');
        }
        else if (globalOtorisasi === '4') {
          rows = rows.filter(r => getStatusDiterima(r) === 'Menunggu');
        }
        else if (globalOtorisasi === '5') {
          rows = rows.filter(r => getStatusDiterima(r) === 'Sebagian');
        }
        else if (globalOtorisasi === '6') {
          rows = rows.filter(r => getStatusDiterima(r) === 'Batal');
        }

        lastRows = rows;
        currentGroupby = groupby;
        $('#searchBox2').val('');
        renderRows(lastRows, groupby);
      },
      error  : function (xhr) {
        console.error('laporanpurchaseorderpo_doReport gagal:', xhr.status, xhr.responseText);
        showToast('⚠️', 'Gagal memuat data (' + xhr.status + ')');
        lastRows = [];
        currentGroupby = groupby;
        renderRows(lastRows, groupby);
      }
    });
  }

  function getStatusDiterima (r) {
    const qnt      = currencyNormalizer(r.Qnt);
    const qntLPB   = currencyNormalizer(r.qntLPB);
    const qntBatal = currencyNormalizer(r.QntBatal);

    // Batal
    if (qnt === 0 && qntLPB === 0 && qntBatal > 0) {
      return "Batal";
    }

    // Diterima (habis)
    if (
      (qnt === qntLPB && qnt > 0) ||
      (qnt === 0 && qntLPB > 0)
    ) {
      return "Diterima";
    }

    // Menunggu
    if (qnt > 0 && qntLPB === 0) {
      return "Menunggu";
    }

    // Sebagian
    if (qnt > qntLPB && qntLPB > 0) {
      return "Sebagian";
    }
    return "";
  }

  // === RENDER KE TABEL STYLED (.tb-report #mainTable) ===
  // Kolom dibangun DINAMIS dari gcart_header (hanya kolom yang terlihat /
  // item[2]===1, sesuai urutan simpanan). Jadi hasil "Customize Table"
  // (show/hide + urutan kolom) langsung tampil. <thead> dibangun oleh
  // ReportTable.headHtml() (drag-reorder + gear). Subtotal/Grand Total =
  // jumlah tiap kolom yang ditandai total (item[4]===1), dikelompokkan per
  // `groupby`. (Data sudah terurut dari proc sesuai inputOrd, jadi cukup
  // deteksi pergantian nilai grup.)
  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1); // kolom terlihat, terurut
    const keys  = cols.filter(c => c[4] === 1).map(c => c[0]); // kolom yang di-subtotal
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');
    // Baris total cuma tampil kalau kolom DPP (NDPP/Ndppusd) sedang terlihat --
    // dipertahankan apa adanya (bukan "ada kolom total apa saja"), sesuai versi sebelumnya.
    const totalVisible = cols.some(c => ['NDPP', 'Ndppusd'].includes(c[0]));
    // Baris Subtotal & Grand Total mengikuti toggle di modal Customize Table
    // (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal).
    // gsum_* dimuat oleh doSetHeader() saat klik Tampilkan, jadi pilihan user
    // (sudah tersimpan) langsung berlaku.
    const showSub   = totalVisible && (gsum_issubtotal === 1);
    const showGrand = totalVisible && (gsum_isgrandtotal === 1);

    // HEADER dinamis — dibangun report-table.js (ReportTable) supaya kolom bisa diseret
    // untuk diurutkan & punya menu roda gigi (sembunyikan / desimal / total).
    thead.innerHTML = ReportTable.headHtml(cols);

    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null;
    let sub = {}, grand = {};
    keys.forEach(k => { sub[k] = 0; grand[k] = 0; });

    rows.forEach(function (r, i) {
      const now = r[groupby];

      // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
      if (showSub && i !== 0 && prev !== now) {
        html += totalRowTotal('Subtotal', sub, cols, keys, 'subtotal-row');
        keys.forEach(k => { sub[k] = 0; });
      }

      keys.forEach(function (k) {
        const v = currencyNormalizer(r[k]);
        sub[k] += v; grand[k] += v;
      });

      // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5])
      html += '<tr class="data-row">' + cols.map(function (c) {
      const key = c[0], type = c[3];

      // Status Otorisasi
      if (key === 'NeedOtorisasi') {
        return `<td> ${r.NeedOtorisasi == 1 ? '<span class="sp-badge is-inactive">Belum</span>' : '<span class="sp-badge is-active">Sudah</span>'} </td>`;
      }

      // Status diterima
      if (key === 'DiTerima') {

      const status = getStatusDiterima(r);

      switch (status) {
        case 'Diterima':
          return '<td><span class="sp-badge is-active">Diterima</span></td>';

        case 'Menunggu':
          return '<td><span class="sp-badge is-user">Menunggu</span></td>';

        case 'Sebagian':
          return '<td><span class="sp-badge is-supervisor">Sebagian</span></td>';

        case 'Batal':
          return '<td><span class="sp-badge is-inactive">Batal</span></td>';

        default:
          return '<td></td>';
        }
      }

        if (type === 'date') return '<td>' + format_date(r[key]) + '</td>';
        if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(r[key]), c[5]) + '</td>';
        if (key === 'NamaBrg') return '<td style="white-space: nowrap;">' + nullToEmpty(r[key]) + '</td>';
        if (key === 'NAMACUSTSUPP') return '<td style="white-space: nowrap;">' + nullToEmpty(r[key]) + '</td>';
        return '<td>' + nullToEmpty(r[key]) + '</td>';
      }).join('') + '</tr>';

      prev = now;
    });

    // subtotal grup terakhir + grand total   mengikuti toggle di modal
    if (showSub)   html += totalRowTotal('Subtotal', sub, cols, keys, 'subtotal-row');
    if (showGrand) html += totalRowTotal('GRAND TOTAL', grand, cols, keys, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
  }

  // Baris total: nilai di kolom yang di-subtotal (item[4]===1), label di kolom pertama
  // non-total yang masih terlihat, sel lain dikosongkan.
  function totalRowTotal(label, total, cols, keys, cls) {
    const labelIdx = cols.findIndex(c => keys.indexOf(c[0]) === -1);

    const tds = cols.map(function (c, idx) {
      if (keys.indexOf(c[0]) !== -1) {
        return '<td class="num">' + format_number(total[c[0]], c[5]) + '</td>';
      }
      if (idx === labelIdx) return '<td>' + label + '</td>';
      return '<td></td>';
    });

    return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
  }

  // === PENCARIAN SISI-KLIEN ===
  // Menyaring data yang SUDAH dimuat (lastRows) berdasarkan teks pencarian,
  // dicocokkan ke semua kolom yang sedang terlihat, lalu render ulang tabel
  // styled (renderRows menghitung ulang subtotal/grand total untuk hasil saring).
  function applyFilters() {
    if (!lastRows.length) return;        // belum ada data dimuat

    const term = ($('#searchBox2').val() || '').trim().toLowerCase();
    if (!term) { renderRows(lastRows, currentGroupby); return; }   // kosong -> tampilkan semua

    const cols = gcart_header.filter(c => c[2] === 1); // kolom yang terlihat
    const filtered = lastRows.filter(function (r) {
      return rowSearchText(r, cols).indexOf(term) !== -1;
    });

    renderRows(filtered, currentGroupby);
  }

  // Gabungan teks satu baris dari kolom terlihat (tanggal pakai format tampil
  // dd/mm/yyyy) supaya pencarian cocok dengan apa yang user lihat di tabel.
  function rowSearchText(r, cols) {
    return cols.map(function (c) {
      const v = r[c[0]];
      if (c[3] === 'date') return format_date(v);
      return (v == null ? '' : String(v));
    }).join(' ').toLowerCase();
  }

  /* -- TOAST -- */
  function showToast(icon, msg) {
    const t = document.getElementById('toast');
    document.getElementById('ti').textContent = icon;
    document.getElementById('tm').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

</script>

@endsection
