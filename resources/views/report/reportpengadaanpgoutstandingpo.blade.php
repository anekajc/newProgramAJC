@extends('report.masterreport2')

<!-- Warna centang -->
  <style>
    .checkmark-red {
      color: red !important;
      font-weight: bold;
      margin-left: 6px;
    }

    #inputOrder{
      border: 0;
      background: none;
      padding: 0;
      box-shadow: none;
      color: #495057;
      font-weight: 600;
    }

    #inputOrder:hover,
    #inputOrder:focus{
      color: #0d6efd;
      box-shadow: none;
    }

    #inputReportMode{
      border: 0;
      background: none;
      padding: 0;
      box-shadow: none;
      color: #495057;
      font-weight: 600;
    }

    #inputReportMode:hover,
    #inputReportMode:focus{
      color: #0d6efd;
      box-shadow: none;
    }

  </style>
<!-- Warna centang -->

@section('header2')
  <div class="tb-report main">
      <div class="content">

        <!-- TOOLBAR -->
        <div class="toolbar">
          <div>
            <div class="page-title">Pengadaan Outstanding PO</div>
            <div class="page-sub">Dicetak oleh: {{ $akses['user'] }} &nbsp;&middot;&nbsp; <span id="printTime"></span></div>
          </div>

          <!-- Periode (date range) -->
          <div class="filter-wrap">
            <label>Periode</label>
            <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
            <span class="filter-sep">s/d</span>
            <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
          </div>

          <!-- mode report -->
          <div class="filter-wrap">
          <button
                class="btn btn-outline-primary dropdown-toggle"
                type="button"
                id="inputReportMode"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                Mode Report
            </button>
            <ul class="dropdown-menu" id="dropdownReportMode" aria-labelledby="inputReportMode">
              <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setReportMode('0')">Detail
              <span class="checkmark-red" style="display:none;">&#10003</span>
              </a></li>
              <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setReportMode('1')">Rekap
              <span class="checkmark-red" style="display:none;">&#10003</span>
              </a></li>
            </ul>
          </div>

          <!-- order by -->
          <div class="filter-wrap">
            <button
                class="btn btn-outline-primary dropdown-toggle"
                type="button"
                id="inputOrder"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                Order By
            </button>
            <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputOrder">
                <li><a class="dropdown-item" data-value="N" onclick="setOrderBy('N')">No Bukti
                <span class="checkmark-red" style="display:none;">&#10003</span>
                </a></li>
                <li><a class="dropdown-item" data-value="B" onclick="setOrderBy('B')">Barang
                <span class="checkmark-red" style="display:none;">&#10003</span>
                </a></li>
                <li><a class="dropdown-item" data-value="S" onclick="setOrderBy('S')">Supplier
                <span class="checkmark-red" style="display:none;">&#10003</span>
                </a></li>
            </ul>
          </div>

          <!-- Actions: second (row-level) search + load + export -->
          <div class="action-group">
            <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
            <button class="btn-load" onclick="doShowFormFilterData()" title="Filter Data"><i class="bi bi-filter-left"></i> Filter Data</button>
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

        <!-- TABLE -->
        <div class="table-outer">
          <div class="table-wrap">
            <table class="tb" id="mainTable">
              <thead>
                <tr>
                  <th style="min-width:130px">No. Bukti</th>
                  <th style="min-width:90px">Tanggal</th>
                  <th style="min-width:130px">Nama Supplier</th>
                  <th style="min-width:130px">Kode Barang</th>
                  <th style="min-width:130px">Nama Barang</th>
                  <th style="min-width:130px">PO Cust</th>
                  <th style="min-width:130px">Nama Customer</th>
                  <th class="num" style="min-width:10px">Sat</th>
                  <th class="num" style="min-width:10px">Qnt PO</th>
                  <th class="num" style="min-width:10px">Qnt Terima</th>
                  <th class="num" style="min-width:10px">Qnt Sisa</th>
                  <th class="num" style="min-width:10px">Lead Time</th>
                  <th class="num" style="min-width:10px">Harga</th>
                  <th class="num" style="min-width:10px">DPP</th>
                  <th class="num" style="min-width:10px">Total</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                <tr class="empty-row"><td colspan="12">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
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

  let DetOrRekap = 0;
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";
  let globalOrderBy = "N";   // default: Nomor Bukti
  let globalReportMode = "0"; // default: Detail
  let lastRows = [];         // hasil fetch terakhir (dipakai renderRows / export / search)
  let currentGroupby = 'NoBukti'; // groupby aktif untuk render ulang saat search

  $(document).ready(function () {
      setOrderBy(globalOrderBy);
      setReportMode(globalReportMode);
  });

  // periode
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
    // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  }

  /* -- EXPORT -- */
  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });

  // order by
  function setOrderBy(val) {
    globalOrderBy = val;

    // sembunyikan semua centang
    $('#dropdownOrder .checkmark-red').hide();

    // tampilkan centang yang dipilih
    $(`#dropdownOrder .dropdown-item[data-value='${val}'] .checkmark-red`).show();
  }

  // reportmode
  function setReportMode(val) {
    globalReportMode = val;
    DetOrRekap = Number(val);

    $('#dropdownReportMode .checkmark-red').hide();
    $(`#dropdownReportMode .dropdown-item[data-value='${val}'] .checkmark-red`).show();
  }

  // $(document).ready(function() {
  //   $("#btnFilterData").on("click", function() {
  //     if (typeof doShowFormFilterData === "function") doShowFormFilterData();
  //     else alert(" Fungsi doShowFormFilterData belum tersedia.");
  //   });

  //   $("#btnCustomizeTable").on("click", function() {
  //     if (typeof doShowFormCustomizeTable === "function") doShowFormCustomizeTable();
  //     else alert(" Fungsi doShowFormCustomizeTable belum tersedia.");
  //   });

  //   $("#btnSubmitReport").on("click", function() {
  //     makeTable('REPORT');
  //   });

  //   setReportMode(globalReportMode);
  //   setOrderBy(globalOrderBy);
  //   showPeriode();

  //   setDefaultHeader();

  //   setTimeout(() => {
  //     makeTable('REPORT');
  //   }, 100);
  // });
  


  var modereport_detailnobukti = 0, modereport_detailbarang = 1, modereport_detailcustomer = 2 ;
  var modereport_rekapnobukti = 3, modereport_rekapbarang = 4, modereport_rekapcustomer = 5 ;
  g_modeReport = modereport_detailnobukti;
  var jenisreport = 0; // ini untuk detail dan rekap

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailnobukti) {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['NOPOCUST', 'PO Cust', 1, 'varchar', 0, 0],
        ['NAMACUSTOMER', 'Nama Customer', 1, 'varchar', 0, 0],
        ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['QntPO', 'Qnt PO', 1, 'float', 1, 0],
        ['QntBeli', 'Qnt Terima', 1, 'float', 1, 0],
        ['QNTOS', 'Qnt Sisa', 1, 'float', 1, 0],
        ['LeadTime', 'Lead Time', 1, 'varchar', 0, 0],
        ['HARGA', 'Harga', 1, 'float', 1, 2],
        ['NDPP', 'DPP', 1, 'float', 1, 2],
        ['NNET', 'Total', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else if (g_modeReport == modereport_detailbarang){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['QntPO', 'Qnt PO', 1, 'float', 1, 0],
        ['QntBeli', 'Qnt Terima', 1, 'float', 1, 0],
        ['QNTOS', 'Qnt Sisa', 1, 'float', 1, 0],
        ['LeadTime', 'Lead Time', 1, 'varchar', 0, 0],
        ['NDPP', 'DPP', 1, 'float', 1, 2],
        ['NNET', 'Total', 1, 'float', 1, 2]  
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;

    } else if(g_modeReport == modereport_detailcustomer){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['QntPO', 'Qnt PO', 1, 'float', 1, 0],
        ['QntBeli', 'Qnt Terima', 1, 'float', 1, 0],
        ['QNTOS', 'Qnt Sisa', 1, 'float', 1, 0],
        ['LeadTime', 'Lead Time', 1, 'varchar', 0, 0],
        ['NDPP', 'DPP', 1, 'float', 1, 2],
        ['NNET', 'Total', 1, 'float', 1, 2] 
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;

    } else if(g_modeReport == modereport_rekapnobukti){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KOdeCustSupp', 'Kode Cust', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['QntPO', 'Qnt PO', 1, 'float', 1, 2],
        ['OS', 'Disc OS', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else if(g_modeReport == modereport_rekapbarang){
      gcart_header = [
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['QntPO', 'Qnt PO', 1, 'float', 1, 2],
        ['OS', 'Disc OS', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else {
      gcart_header = [
        ['KOdeCustSupp', 'Kode Cust', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['QntPO', 'Qnt PO', 1, 'float', 1, 2],
        ['OS', 'Disc OS', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;
    }
  }

  const reportUrl = "{{ url('laporanpenerimaangudangospo_doReport') }}"
  function makeTable(_mode) {
    console.log(" makeTable jalankan mode:", _mode);

    let groupby = '';
    let _date1 = $("#inputDate1").val();
    let _date2 = $("#inputDate2").val();
    let input_order = globalOrderBy;

    // mode report 
    if (input_order == "N") {
      if (DetOrRekap === 0) {
        g_modeReport = modereport_detailnobukti;
        groupby = 'NoBukti';
      } else {
        g_modeReport = modereport_rekapnobukti;
        groupby = 'NoBukti';
      }
    } else if (input_order == "B") {
      if (DetOrRekap === 0) {
        g_modeReport = modereport_detailbarang;
        groupby = 'NamaBrg';
      } else {
        g_modeReport = modereport_rekapbarang;
        groupby = 'NamaBrg';
      }
    } else {
      if (DetOrRekap === 0) {
        g_modeReport = modereport_detailcustomer;
        groupby = 'NAMACUSTSUPP';
      } else {
        g_modeReport = modereport_rekapcustomer;
        groupby = 'NAMACUSTSUPP';
      }
    }

    console.log("Mode report aktif:", g_modeReport, "| Group By:", groupby);
    console.log(input_order, DetOrRekap)

    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let data = {
      date1: _date1,
      date2: _date2,
      inputOrd: input_order,
      inputDetOrRekap: DetOrRekap,
    };

    // Ambil data SEKALI, lalu render langsung ke tabel styled baru (#tableBody).
    $.ajax({
      url    : reportUrl,
      type   : 'get',
      data   : data,
      success: function (res) {
        lastRows = res || [];
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

    // console.log("Data terkirim ke server:", data);

    // doMakeTable(_mode, groupby, data, "REPORT PENGADAAN OUTSTANDING PO", _date1, _date2, DetOrRekap);
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
    const qntVisible = cols.some(c => c[0] === 'QntPO');
    // Baris Subtotal & Grand Total mengikuti toggle di modal Customize Table
    // (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal).
    // gsum_* dimuat oleh doSetHeader() saat klik Tampilkan, jadi pilihan user
    // (sudah tersimpan) langsung berlaku. Total hanya tampil bila kolom Qnt ada.
    const showSub   = qntVisible && (gsum_issubtotal === 1);
    const showGrand = qntVisible && (gsum_isgrandtotal === 1);

    // HEADER dinamis dari gcart_header
    thead.innerHTML = '<tr>' + cols.map(function (c) {
      const isNum = (c[3] === 'float' || c[3] === 'int');
      return '<th' + (isNum ? ' class="num"' : '') + '>' + c[1] + '</th>';
    }).join('') + '</tr>';

    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null, sub = { QntPO: 0, HARGA: 0, QNTOS: 0, NDPP: 0, QntBeli: 0, NNET: 0 }, grand = { QntPO: 0, HARGA: 0, QNTOS: 0, NDPP: 0, QntBeli: 0, NNET: 0 };

    rows.forEach(function (r, i) {
      const now = r[groupby];

      // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
      if (showSub && i !== 0 && prev !== now) { html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row'); sub = { QntPO: 0, HARGA: 0, QNTOS: 0, NDPP: 0, QntBeli: 0, NNET: 0 };
      }

      sub.QntPO   += currencyNormalizer(r.QntPO);
      sub.HARGA += currencyNormalizer(r.HARGA);
      sub.QNTOS += currencyNormalizer(r.QNTOS);
      sub.NDPP += currencyNormalizer(r.NDPP);
      sub.NNET += currencyNormalizer(r.NNET);
      sub.QntBeli += currencyNormalizer(r.QntBeli);

      grand.QntPO   += currencyNormalizer(r.QntPO);
      grand.HARGA += currencyNormalizer(r.HARGA);
      grand.QNTOS += currencyNormalizer(r.QNTOS);
      grand.NDPP += currencyNormalizer(r.NDPP);
      grand.NNET += currencyNormalizer(r.NNET);
      grand.QntBeli += currencyNormalizer(r.QntBeli);

      // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5])
      html += '<tr class="data-row">' + cols.map(function (c) {
        const key = c[0], type = c[3];
        if (type === 'date') return '<td>' + format_date(r[key]) + '</td>';
        if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(r[key]), c[5]) + '</td>';
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
        !['HARGA', 'QntBeli', 'QntPO', 'NDPP', 'NNET', 'QNTOS'].includes(c[0])
    );

    const tds = cols.map(function(c, idx) {
        if (c[0] === 'HARGA')
            return '<td class="num">' + format_number(total.HARGA, 2) + '</td>';
        if (c[0] === 'QNTOS')
            return '<td class="num">' + format_number(total.QNTOS, 2) + '</td>';
        if (c[0] === 'NDPP')
            return '<td class="num">' + format_number(total.NDPP, 2) + '</td>';
        if (c[0] === 'NNET')
            return '<td class="num">' + format_number(total.NNET, 2) + '</td>';
        if (c[0] === 'QntBeli')
            return '<td class="num">' + format_number(total.QntBeli, 2) + '</td>';
        if (c[0] === 'QntPO')
            return '<td class="num">' + format_number(total.QntPO, 2) + '</td>';
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

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if ($("#inputOrder").val() == "N"){
      data = ['NoBukti', 'TANGGAL'];
    } else if ($("#inputOrder").val() == "B"){
      data = ['KodeBrg', 'NamaBrg'];
    } else {
      data = ['KodeBrg', 'NAMACUSTSUPP'];
    }

    return data;
  }

  function reportMode(_mode) {
    if (jenisreport != _mode) {
      let prev_mode = jenisreport;
      jenisreport = _mode;

      $("#tombolMode" + prev_mode).removeClass("btn-primary");
      $("#tombolMode" + prev_mode).addClass("btn-outline-primary");

      $("#tombolMode" + jenisreport).removeClass("btn-outline-primary");
      $("#tombolMode" + jenisreport).addClass("btn-primary");

      setModeReport();

    }
  }

  function setModeReport() {
    if ($("#inputOrder").val() == "N") {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailnobukti;
      } else {
        g_modeReport = modereport_rekapnobukti;
      }
    } else if ($("#inputOrder").val() == "B") {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailbarang;
      } else {
        g_modeReport = modereport_rekapbarang;
      }
    } else {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailcustomer;
      } else {
        g_modeReport = modereport_rekapcustomer;
      }
    }

    doSetHeader(g_modeReport);
    doShowCustomize();
  }


</script>

@endsection