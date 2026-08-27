@extends('report.masterreport2')

<!-- Warna centang -->
  <style>
    .checkmark-red {
      color: red !important;
      font-weight: bold;
      margin-left: 6px;
    }

    #inputOtorisasi{
      border: 0;
      background: none;
      padding: 0;
      box-shadow: none;
      color: #495057;
      font-weight: 600;
    }

    #inputOtorisasi:hover,
    #inputOtorisasi:focus{
      color: #0d6efd;
      box-shadow: none;
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

  </style>
<!-- Warna centang -->


@section('header2')
    <div class="tb-report main">
      <div class="content">

        <!-- TOOLBAR -->
        <div class="toolbar">
          <div>
            <div class="page-title">Debet Note</div>
            <!-- <div class="page-sub">Dicetak oleh: {{ $akses['user'] }} &nbsp;&middot;&nbsp; <span id="printTime"></span></div> -->
          </div>

          <!-- Periode (date range) -->
          <div class="filter-wrap">
            <label>Periode</label>
            <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
            <span class="filter-sep">s/d</span>
            <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
          </div>

          <!-- otorisasi -->
          <!-- <div class="filter-wrap">
          <button
                class="btn btn-outline-primary dropdown-toggle"
                type="button"
                id="inputOtorisasi"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                Otorisasi
            </button>
            <ul class="dropdown-menu" id="dropdownOtorisasi" aria-labelledby="inputOtorisasi">
              <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setOtorisasi('2')">Semua
              <span class="checkmark-red" style="display:none;">&#10003</span>
              </a></li>
              <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setOtorisasi('1')">Belum Otorisasi
              <span class="checkmark-red" style="display:none;">&#10003</span>
              </a></li>
              <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setOtorisasi('0')">Sudah Otorisasi
              <span class="checkmark-red" style="display:none;">&#10003</span>
              </a></li>
            </ul>
          </div> -->

          <!-- order by -->
          <!-- <div class="filter-wrap">
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
                <li><a class="dropdown-item" data-value="S" onclick="setOrderBy('S')">Supplier
                <span class="checkmark-red" style="display:none;">&#10003</span>
                </a></li>
            </ul>
          </div> -->

          <!-- Actions: second (row-level) search + load + export -->
          <div class="action-group">
            <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
            <!-- <button class="btn-load" onclick="doShowFormFilterData()" title="Filter Data"><i class="bi bi-filter-left"></i> Filter Data</button> -->
            <button
              class="btn-load"
              data-bs-toggle="modal"
              data-bs-target="#modalFilter">
              <i class="fas fa-filter"></i> Filter
            </button>
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
                  <th style="min-width:130px">No. Invoice</th>
                  <th style="min-width:90px">Tanggal</th>
                  <th style="min-width:90px">Kode Customer</th>
                  <th style="min-width:130px">Nama Supplier</th>
                  <th class="num" style="min-width:10px">Nilai VLS</th>
                  <th class="num" style="min-width:10px">VLS</th>
                  <th class="num" style="min-width:10px">Kurs</th>
                  <th class="num" style="min-width:10px">DPP</th>
                  <th class="num" style="min-width:10px">Otorisasi</th>
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

      </div><!-- /content -->

      <!-- TOAST -->
      <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
    </div><!-- /tb-report -->

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
                        <option value="1">Belum Otorisasi</option>
                        <option value="0">Sudah Otorisasi</option>
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
  });

  $('#modalFilter').on('show.bs.modal', function () {
    $("#modalOtorisasi").val(globalOtorisasi);
  });

  function applyModalFilter() {

    setOtorisasi($("#modalOtorisasi").val());

    $('#modalFilter').modal('hide');
  }

  // $(document).ready(function() {
  //   console.log("Script sudah jalan. Coba klik tombol untuk tes fungsi.");
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

  //   setOtorisasi(globalOtorisasi);
  //   setOrderBy(globalOrderBy);
  //   showPeriode();
  //   setDefaultHeader();

  //   setTimeout(() => {
  //     makeTable('REPORT');
  //   }, 100);
  // });

  // periode
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
    // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  }

  // otorisasi
  function setOtorisasi(val) {
    globalOtorisasi = val;

    // // sembunyikan semua centang
    // $('#dropdownOtorisasi .checkmark-red').hide();

    // // tampilkan centang yang dipilih
    // $(`#dropdownOtorisasi .dropdown-item[data-value='${val}'] .checkmark-red`).show();

    // // ubah tulisan tombol
    // const text = {
    //     "2": "Semua",
    //     "1": "Belum Otorisasi",
    //     "0": "Sudah Otorisasi",
    // };

    // $("#inputOtorisasi").html(
    //     `Otorisasi : ${text[val]}`
    // );
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

  // // periode
  // function showPeriode() {
  //   globalDate1 = $('#inputDate1').val();
  //   globalDate2 = $('#inputDate2').val();
  //   // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  // }

  // // otorisasi
  // function setOtorisasi(val) {
  //   globalOtorisasi = val;
  //   let text = (val == '0') ? 'Semua' : (val == '1') ? 'Otorisasi' : 'Non Otorisasi';
  //   // alertify.success(`Otorisasi: ${text}`);

  //   // hapus semua centang
  //   $('#dropdownOtorisasi .dropdown-item').each(function() {
  //     let itemText = $(this).text().replace(' ?', '').trim(); 
  //     $(this).text(itemText);
  //   });

  //   // tambah centang di item yg di pilih
  //   $(`#dropdownOtorisasi .dropdown-item[data-value='${val}']`).each(function() {
  //     $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
  //   });
  // }

  // // order by
  // function setOrderBy(val) {
  //   globalOrderBy = val;
  //   let text = (val == 'N') ? 'Nomor Bukti' : 'Supplier';
  //   // alertify.success(`Order By: ${text}`);

  //   // hapus semua centang
  //   $('#dropdownOrder .dropdown-item').each(function() {
  //     let itemText = $(this).text().replace(' ?', '').trim();
  //     $(this).text(itemText);
  //   });

  //   // tambah centang di item yg di pilih
  //   $(`#dropdownOrder .dropdown-item[data-value='${val}']`).each(function() {
  //     $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
  //   });
  // }

  var modereport_detail = 0, modereport_rekap = 1;
  g_modeReport = modereport_detail;

  function setDefaultHeader() {
    if (g_modeReport == modereport_detail) {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['NoInv', 'No. Invoice', 1, 'varchar', 0, 0],
        ['tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['kodecustsupp', 'Kode Customer', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NDPP', 'Nilai VLS', 1, 'float', 1, 2],
        ['KodeVLS', 'VLS', 1, 'varchar', 0, 0],
        ['Kurs', 'Kurs', 1, 'varchar', 0, 0],
        ['NDPPRP', 'DPP', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;
    } else {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['NoInv', 'No. Invoice', 1, 'varchar', 0, 0],
        ['tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['kodecustsupp', 'Kode Customer', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NDPP', 'Nilai VLS', 1, 'float', 1, 2],
        ['KodeVLS', 'VLS', 1, 'varchar', 0, 0],
        ['Kurs', 'Kurs', 1, 'varchar', 0, 0],
        ['NDPPRP', 'DPP', 1, 'float', 1, 2],
        ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;
    }
  }

  const reportUrl = "{{ url('laporanpengadaandebetnote_doReport') }}"
  function makeTable(_mode) {
    let groupby = '';
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();
    let input_order = globalOrderBy;

    if (input_order == "N") {
      g_modeReport = modereport_detail;
      groupby = 'NoBukti';
    } else if (input_order == "S") {
      g_modeReport = modereport_customer;
      groupby = 'NoBukti';
    }

    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let data = {
      date1    : _date1,
      date2    : _date2,
      inputOto : globalOtorisasi,
      inputOrd : input_order,
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

    // doMakeTable(_mode, groupby, data, "REPORT PENGADAAN DEBET NOTE", _date1, _date2);
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
    const qntVisible = cols.some(c => c[0] === 'NDPP');
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

    let html = '', prev = null, sub = { NDPP: 0, NDPPRP: 0 }, grand = { NDPP: 0, NDPPRP: 0 };

    rows.forEach(function (r, i) {
      const now = r[groupby];

      // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
      if (showSub && i !== 0 && prev !== now) { html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row'); sub = {  NDPP: 0, NDPPRP: 0 };
      }

      sub.NDPP += currencyNormalizer(r.NDPP);
      sub.NDPPRP += currencyNormalizer(r.NDPPRP);

      grand.NDPP += currencyNormalizer(r.NDPP);
      grand.NDPPRP += currencyNormalizer(r.NDPPRP);

      // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5])
      html += '<tr class="data-row">' + cols.map(function (c) {
        const key = c[0], type = c[3];
        // Status Otorisasi
        if (key === 'NeedOtorisasi') {
          return `<td> ${r.NeedOtorisasi == 1 ? '<span class="sp-badge is-inactive">Belum</span>' : '<span class="sp-badge is-active">Sudah</span>'} </td>`;
        }
        
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
        !['NDPP', 'NDPPRP'].includes(c[0])
    );

    const tds = cols.map(function(c, idx) {
        if (c[0] === 'NDPP')
            return '<td class="num">' + format_number(total.NDPP, 2) + '</td>';
        if (c[0] === 'NDPPRP')
            return '<td class="num">' + format_number(total.NDPPRP, 2) + '</td>';
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
    if (g_modeReport == modereport_detail) {
      data = ['NoBukti', 'tanggal'];
    } else {
      data = ['NoBukti', 'tanggal'];
    }

    return data;
  }

</script>

@endsection