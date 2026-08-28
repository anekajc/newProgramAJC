@extends('report.masterreport2')

<style>
  /* tinggi awal area tabel supaya dropdown tidak terpotong container pendek */
  .tb-report .table-wrap { min-height: 10vh; }
</style>

@section('header2')
<div class="tb-report main">
      <div class="content">

        <!-- TOOLBAR -->
        <div class="toolbar">
          {{-- <div>
            <div class="page-title">Pengadaan PR</div>
            <!-- <div class="page-sub">Dicetak oleh: {{ $akses['user'] }} &nbsp;&middot;&nbsp; <span id="printTime"></span></div> -->
          </div> --}}

          <!-- Periode (date range) -->
          <div class="filter-wrap">
            <label>Periode</label>
            <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
            <span class="filter-sep">s/d</span>
            <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
          </div>

          {{-- Search --}}
          <div>
            <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
          </div>

          {{-- Otorisasi pindah ke modal "Filter Laporan" (lihat docs/new-filter-modal-ui-guide.md).
               Order By (No Bukti/Barang/Customer) jadi "Tampilan" switcher di bar tabel (diisi
               ReportTable.init({ views: ... }), lihat docs/new-slider-table-guide.md §Step 5) --
               keduanya sebelumnya dropdown toolbar yang di-comment total, tidak pernah terpasang. --}}

          <!-- Actions: search + filter + tampilkan + export -->
          <div class="action-group">
            {{-- Dibuka lewat plugin jQuery (Bootstrap 4), BUKAN data-bs-toggle (Bootstrap 5) —
                 lihat aturan dua-Bootstrap di new-design-all-guide.md §5.1. --}}
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

        <!-- Bar kolom tersembunyi + Tampilan (Order By) (diisi oleh report-table.js / ReportTable) -->
        <div id="rtBar"></div>

        <!-- TABLE — header satu tingkat (tanpa band), dibangun oleh ReportTable.headHtml() di
             renderRows() (drag-reorder + gear aktif seperti biasa). -->
        <div class="table-outer">
          <div class="table-wrap">
            <table class="tb" id="mainTable">
              <thead>
                <tr>
                  <th style="min-width:130px">No. Bukti</th>
                  <th style="min-width:90px">Tanggal</th>
                  <th style="min-width:130px">Customer</th>
                  <th style="min-width:80px">Kode Barang</th>
                  <th style="min-width:130px">Nama Barang</th>
                  <th class="num" style="min-width:10px">Sat</th>
                  <th class="num" style="min-width:10px">Qnt</th>
                  <th class="num" style="min-width:10px">Qnt PO</th>
                  <th>Keterangan</th>
                  <th>Otorisasi</th>
                  <th>PO</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                <tr class="empty-row"><td colspan="10">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
              </tbody>
            </table>
          </div>
          <div class="table-footer">
            <span id="footerLabel">Belum ada data dimuat</span>
          </div>
        </div>

        <div class="rt-hint">
          <i class="bi bi-info-circle"></i>
          Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> untuk sembunyikan kolom atau atur total.
        </div>

      </div><!-- /content -->

      <!-- TOAST -->
      <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
    </div><!-- /tb-report -->

<!-- modal filter -->
<div class="modal fade rt-filter" id="modalFilter">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-filter"></i> Filter Laporan
          <span class="rt-active-badge" id="filterBadge">0 aktif</span>
        </h5>
        {{-- data-dismiss (BS4) = jaga-jaga; BS5 (data-bs-dismiss) yang benar-benar menutup di
             halaman Class A ini -- lihat aturan dua-Bootstrap di new-design-all-guide.md §5.1. --}}
        <button type="button" class="btn-close" aria-label="Close" data-dismiss="modal" data-bs-dismiss="modal"
                onclick="$('#modalFilter').modal('hide')"></button>
      </div>

      <div class="modal-body">

        <div class="rt-section">
          <div class="rt-group-label">Pengaturan Laporan</div>
          <div class="rt-grid-1">
            <div>
              {{-- Gabungan status Otorisasi + PO dalam satu pilihan (bukan dua field terpisah) --
                   ini bentuk aslinya, tidak diubah. Selalu punya opsi "Semua" -> DIHITUNG di
                   badge saat ≠ '2' (lihat updateFilterBadge()). --}}
              <label class="rt-field-label" for="modalOtorisasi">Filter</label>
              <select class="rt-native" id="modalOtorisasi">
                <option value="2">Semua</option>
                <option value="1">Belum Otorisasi</option>
                <option value="0">Sudah Otorisasi</option>
                <option value="3">Belum PO</option>
                <option value="4">Sudah PO</option>
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
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";
  let globalOtorisasi = "2"; // default: Semua
  let globalOrderBy = "N";   // default: Nomor Bukti
  let lastRows = [];         // hasil fetch terakhir (dipakai renderRows / export / search)
  let currentGroupby = 'NoBukti'; // groupby aktif untuk render ulang saat search

  $(document).ready(function () {
      setOtorisasi(globalOtorisasi);
      setOrderBy(globalOrderBy);

      // Header tabel interaktif standar (drag-reorder + gear per kolom + bar "kolom
      // tersembunyi"/"Reset kolom"), plus "Tampilan" switcher untuk Order By -- halaman ini
      // SUDAH punya mode yang menukar susunan kolom (g_modeReport/setDefaultHeader(), lihat
      // makeTable()), cuma dropdown pemicunya dulu di-comment total dan tidak pernah terpasang.
      // "Order By" di sini menukar SUSUNAN KOLOM (gcart_header) & harus di-query ulang ke server
      // (inputOrd menentukan urutan sortir dari SP, dipakai deteksi pergantian grup di
      // renderRows()) -- makanya set() di bawah memanggil makeTable('REPORT'), bukan render().
      ReportTable.init({
        table: '#mainTable',
        bar: '#rtBar',
        onChange: function () { applyFilters(); },
        views: {
          label: 'Order By',
          options: [
            { value: 'N', label: 'No Bukti', desc: 'Dikelompokkan per No Bukti' },
            { value: 'B', label: 'Barang',   desc: 'Dikelompokkan per Kode Barang' },
            { value: 'S', label: 'Customer', desc: 'Dikelompokkan per Customer' }
          ],
          get: function () { return globalOrderBy; },
          set: function (v) {
            setOrderBy(String(v));
            if (lastRows.length) { makeTable('REPORT'); }
          }
        }
      });
  });

  $('#modalFilter').on('show.bs.modal', function () {
    $("#modalOtorisasi").val(globalOtorisasi);
    updateFilterBadge();
  });

  $('#modalFilter').on('change', 'select.rt-native', updateFilterBadge);

  function applyModalFilter() {
    setOtorisasi($("#modalOtorisasi").val());
    $('#modalFilter').modal('hide');
  }

  /* ── FILTER MODAL ──
        Otorisasi punya opsi "Semua" -> DIHITUNG di badge saat nilainya ≠ '2' (aturan sama
        seperti Otorisasi di reportmarketingso, lihat docs/new-filter-modal-ui-guide.md §5).
        Order By TIDAK dihitung -- itu jadi "Tampilan" switcher di bar tabel, bukan field
        filter modal, dan lagipula forced-choice (tidak ada opsi netral). ── */
  function updateFilterBadge() {
    let count = 0;
    if ($('#modalOtorisasi').val() !== '2') { count++; }
    $('#filterBadge').text(count + ' aktif');
  }

  function resetAllFilters() {
    $('#modalOtorisasi').val('2');
    updateFilterBadge();
  }

  // periode
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
    // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  }

  // otorisasi (nilai sebenarnya -- tidak ada UI toolbar terpisah untuk ini lagi, hanya modal Filter Laporan)
  function setOtorisasi(val) {
    globalOtorisasi = val;
  }

  /* -- EXPORT -- */
  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });

  // order by (nilai sebenarnya -- UI-nya "Tampilan" switcher di #rtBar, lihat ReportTable.init() di atas)
  function setOrderBy(val) {
    globalOrderBy = val;
  }


  var modereport_nobukti = 0, modereport_barang = 1, modereport_customer = 2;
  g_modeReport = modereport_nobukti;

  function setDefaultHeader() {
    if (g_modeReport == modereport_nobukti) {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Customer', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Sat', 1, 'varchar', 0, 0],
        ['Qnt', 'Qnt', 1, 'float', 1, 2],
        ['QNTPO', 'Qnt PO', 1, 'float', 1, 2],
        ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['StatusPO', 'PO', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;

    } else if (g_modeReport == modereport_barang){
      gcart_header = [
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['Satuan', 'Sat', 1, 'varchar', 0, 0],
        ['Qnt', 'Qnt', 1, 'float', 1, 2],
        ['QNTPO', 'Qnt PO', 1, 'float', 1, 2],
        ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['StatusPO', 'PO', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 0;

    } else {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Sat', 1, 'varchar', 0, 0],
        ['Qnt', 'Qnt', 1, 'float', 1, 2],
        ['QNTPO', 'Qnt PO', 1, 'float', 1, 2],
        ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0],
        ['StatusPO', 'PO', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;
    }
  }

  const reportUrl = "{{ url('laporanpengadaanpr_doReport') }}"
  function makeTable(_mode) {
    let groupby = '';
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();
    let input_order = globalOrderBy;

    if (input_order == "N") {
      g_modeReport = modereport_nobukti;
      groupby = 'NoBukti';
    } else if (input_order == "B") {
      g_modeReport = modereport_barang;
      groupby = 'KodeBrg';
    } else {
      g_modeReport = modereport_customer;
      groupby = 'NAMACUSTSUPP';
    }

    // Muat susunan kolom (gcart_header) untuk mode ini   termasuk hasil
    // kustomisasi user dari modal "Customize Table" (doShowFormCustomizeTable):
    // show/hide kolom + urutannya. Tabel styled di-render DARI gcart_header,
    // jadi pilihan kolom user langsung ikut tampil saat klik Tampilkan.
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let inputOto = globalOtorisasi;
    // Untuk filter PO
    if (inputOto == "3" || inputOto == "4") {
        inputOto = "2";
    }

    let data = {
      date1    : _date1,
      date2    : _date2,
      inputOto : inputOto,
      inputOrd : input_order,
    };

    // Ambil data SEKALI, lalu render langsung ke tabel styled baru (#tableBody).
    $.ajax({
      url    : reportUrl,
      type   : 'get',
      data   : data,
      success: function (res) {
        lastRows = res || [];
        
        if (globalOtorisasi == "3") {
          // Belum PO
          lastRows = lastRows.filter(r => currencyNormalizer(r.QNTPO || 0) == 0);
        }
        else if (globalOtorisasi == "4") {
          // Sudah PO
          lastRows = lastRows.filter(r => currencyNormalizer(r.QNTPO || 0) > 0);
        }

        currentGroupby = groupby;        // simpan utk render ulang saat search
        $('#searchBox2').val('');        // reset kotak cari tiap muat data baru
        renderRows(lastRows, groupby);   // <-- render ke .tb-report #tableBody
      },
      error  : function () {
        lastRows = [];
        currentGroupby = groupby;
        renderRows([], groupby);
      }
    });

    // --- ENGINE LAMA (tabel #tabel pada masterreport2) DIMATIKAN ---
    // Baris di bawah inilah yang dulu menampilkan data ke tabel LAMA
    // (#showTableReport/#tabel) sekaligus melakukan pemanggilan data KEDUA.
    // Dikomentari supaya data hanya tampil di tabel styled baru di atas.
    // Aktifkan lagi kalau mau memunculkan tabel lama.
    // doMakeTable(_mode, groupby, data, "REPORT PENGADAAN PR", _date1, _date2);
  }

  // === RENDER KE TABEL STYLED (.tb-report #mainTable) ===
  // Kolom dibangun DINAMIS dari gcart_header (hanya kolom yang terlihat /
  // item[2]===1, sesuai urutan simpanan). Jadi hasil "Customize Table"
  // (show/hide + urutan kolom) langsung tampil. <thead> ditulis ulang tiap
  // render. Subtotal/Grand Total = jumlah kolom Qnt, dikelompokkan per `groupby`.
  // (Data sudah terurut dari proc sesuai inputOrd, jadi cukup deteksi pergantian
  // nilai grup. Jika kolom Qnt disembunyikan, baris total tidak ditampilkan.)
  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1); // kolom terlihat, terurut
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');
    const qntVisible = cols.some(c => c[0] === 'Qnt');
    // Baris Subtotal & Grand Total mengikuti toggle di modal Customize Table
    // (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal).
    // gsum_* dimuat oleh doSetHeader() saat klik Tampilkan, jadi pilihan user
    // (sudah tersimpan) langsung berlaku. Total hanya tampil bila kolom Qnt ada.
    const showSub   = qntVisible && (gsum_issubtotal === 1);
    const showGrand = qntVisible && (gsum_isgrandtotal === 1);

    // HEADER dinamis dari gcart_header — ReportTable.headHtml() (drag-reorder + gear per
    // kolom), bukan lagi <th> polos manual.
    thead.innerHTML = ReportTable.headHtml(cols);

    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null, sub = { Qnt: 0, QNTPO: 0 }, grand = { Qnt: 0, QNTPO: 0 };

    rows.forEach(function (r, i) {
      const now = r[groupby];

      // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
      if (showSub && i !== 0 && prev !== now) { html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row'); sub = { Qnt: 0, QNTPO: 0 };
      }

      sub.Qnt   += currencyNormalizer(r.Qnt);
      sub.QNTPO += currencyNormalizer(r.QNTPO);

      grand.Qnt   += currencyNormalizer(r.Qnt);
      grand.QNTPO += currencyNormalizer(r.QNTPO);

      // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5])
      html += '<tr class="data-row">' + cols.map(function (c) {
        const key = c[0], type = c[3];
        // Status Otorisasi
        if (key === 'NeedOtorisasi') {
          return `<td> ${r.NeedOtorisasi == 1 ? '<span class="sp-badge is-inactive">Belum</span>' : '<span class="sp-badge is-active">Sudah</span>'} </td>`;
        }

        // Status PO
        if (key === 'StatusPO') {
          const qntpo = currencyNormalizer(r.QNTPO || 0);
          return `<td> ${qntpo > 0 ? '<span class="sp-badge is-active">Sudah</span>' : '<span class="sp-badge is-inactive">Belum</span>'} </td>`;
        }

        if (type === 'date') return '<td>' + format_date(r[key]) + '</td>';
        if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(r[key]), c[5]) + '</td>';
        if (key === 'NamaBrg') return '<td style="white-space: nowrap;">' + nullToEmpty(r[key]) + '</td>';
        return '<td>' + nullToEmpty(r[key]) + '</td>';
      }).join('') + '</tr>';

      prev = now;
    });

    // subtotal grup terakhir + grand total   mengikuti toggle di modal
    if (showSub)   html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
    if (showGrand) html += totalRowTotal('GRAND TOTAL', grand, cols, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
  }

  // Baris total (Qnt saja): nilai di kolom Qnt, label di kolom pertama (bukan Qnt),
  // sel lain dikosongkan   mengikuti urutan kolom terlihat saat ini.
  function totalRowTotal(label, total, cols, cls) {
    const labelIdx = cols.findIndex(c =>
        !['Qnt', 'QNTPO'].includes(c[0])
    );

    const tds = cols.map(function(c, idx) {
        if (c[0] === 'Qnt')
            return '<td class="num">' + format_number(total.Qnt, 2) + '</td>';
        if (c[0] === 'QNTPO')
            return '<td class="num">' + format_number(total.QNTPO, 2) + '</td>';
        if (idx === labelIdx)
            return '<td>' + label + '</td>';
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

  // getKolomFilter() milik ENGINE LAMA (modal "Filter Data" / doShowFormFilterData()), yang
  // TIDAK dipakai halaman ini (Filter Laporan sendiri sudah punya modal #modalFilter). Stub ini
  // cuma jaga-jaga supaya base script masterreport2 tidak error kalau memanggilnya.
  function getKolomFilter() { return []; }
</script>

@endsection
