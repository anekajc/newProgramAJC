@extends('report.masterreport2')

<!-- Warna centang -->
  <style>
    .checkmark-red {
      color: red !important;
      font-weight: bold;
      margin-left: 6px;
    }
  </style>
<!-- Warna centang -->

@section('header2')
  <div class="w-100 bg-light shadow-sm py-3 px-4 border-bottom d-flex align-items-center justify-content-between" style="margin-top:-20px; margin-bottom:150px;">
    <!-- Kiri: ikon -->
    <div class="d-flex" style="gap: 10px;">
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnPeriode" data-bs-toggle="dropdown" aria-expanded="false" title="Periode">
          <i class="fas fa-calendar-alt"></i>
        </button>
        <div class="dropdown-menu p-3" style="min-width: 350px;">
          <input type="date" class="form-control mb-2" id="inputDate1" value="{!! date('Y-m-d') !!}">
          <label for="inputDate2" class="mb-0">s/d</label>
          <input type="date" class="form-control mt-1" id="inputDate2" value="{!! date('Y-m-d') !!}">
        </div>
      </div> 
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputReportMode" data-bs-toggle="dropdown" aria-expanded="false" title="Report Mode" style="cursor: pointer;">
          <i class="fas fa-book"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownReportMode" aria-labelledby="inputReportMode">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setReportMode('1')">Rekap</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setReportMode('0')">Detail</a></li>
        </ul>
      </div>
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputOtorisasi" data-bs-toggle="dropdown" aria-expanded="false" title="Otorisasi" style="cursor: pointer;">
          <i class="fas fa-key"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOtorisasi" aria-labelledby="inputOtorisasi">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setOtorisasi('2')">Semua</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setOtorisasi('1')">Non Otorisasi</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setOtorisasi('0')">Otorisasi</a></li>
        </ul>
      </div>
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputOrder" data-bs-toggle="dropdown" aria-expanded="false" title="Order By">
          <i class="fas fa-exchange-alt" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputOrder">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="N" onclick="setOrderBy('N')">Nomor Bukti</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="S" onclick="setOrderBy('S')">Supplier</a></li>
        </ul>
      </div>
    </div>

    <!-- Kanan: tombol aksi menempel ke ujung kanan layar -->
    <div class="d-flex ms-auto" style="gap: 8px;">
      <button type="button" class="btn btn-outline-primary" onclick="doShowFormFilterData()" title="Filter Data">
        <i class="fas fa-magnifying-glass"></i>
      </button>
      <button type="button" class="btn btn-outline-primary" onclick="doShowFormCustomizeTable()" title="Customize Table">
        <i class="fas fa-cog"></i>
      </button>
      <button type="button" class="btn btn-outline-primary" onclick="makeTable('REPORT')" title="Submit">
        <i class="fas fa-check"></i>
      </button>
    </div>
  </div>

@endsection

@section('jsreport')
<script type="text/javascript">
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";
  let globalOtorisasi = "2"; // default: Semua
  let globalOrderBy = "N";   // default: Nomor Bukti
  let globalReportMode = "0"; // default: Detail

  $(document).ready(function() {
    $("#btnFilterData").on("click", function() {
      if (typeof doShowFormFilterData === "function") doShowFormFilterData();
      else alert(" Fungsi doShowFormFilterData belum tersedia.");
    });

    $("#btnCustomizeTable").on("click", function() {
      if (typeof doShowFormCustomizeTable === "function") doShowFormCustomizeTable();
      else alert(" Fungsi doShowFormCustomizeTable belum tersedia.");
    });

    $("#btnSubmitReport").on("click", function() {
      makeTable('REPORT');
    });

    setReportMode(globalReportMode);
    setOtorisasi(globalOtorisasi);
    setOrderBy(globalOrderBy);
    showPeriode();

    setDefaultHeader();

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });
  

  // periode
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
    // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  }

  // otorisasi
  function setOtorisasi(val) {
    globalOtorisasi = val;
    let text = (val == '0') ? 'Semua' : (val == '1') ? 'Otorisasi' : 'Non Otorisasi';
    // alertify.success(`Otorisasi: ${text}`);

    // hapus semua centang
    $('#dropdownOtorisasi .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ✔', '').trim(); 
      $(this).text(itemText);
    });

    // tambah centang di item yg di pilih
    $(`#dropdownOtorisasi .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">✔</span>`);
    });
  }

  // mode report
  function setReportMode(val) {
    globalReportMode = val;
    jenisreport = Number(val);   // 0 = Detail, 1 = Rekap
    DetOrRekap = Number(val);    // samakan dengan variabel yang ada di setModeReport

    // hapus centang dulu
    $('#dropdownReportMode .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ✔', '').trim();
      $(this).text(itemText);
    });

    // tambah centang di item terpilih
    $(`#dropdownReportMode .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">✔</span>`);
    });

    // update g_modeReport sesuai pilihan order & detail/rekap
    // setModeReport() sudah mengatur g_modeReport berdasarkan $("#inputOrder").val() dan jenisreport/DetOrRekap
    setModeReport();
  }

  // order by
  function setOrderBy(val) {
    globalOrderBy = val;
    let text = (val == 'N') ? 'Nomor Bukti' : 'Supplier';
    // alertify.success(`Order By: ${text}`);

    // hapus semua centang
    $('#dropdownOrder .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ✔', '').trim();
      $(this).text(itemText);
    });

    // tambah centang di item yg dipilih
    $(`#dropdownOrder .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">✔</span>`);
    });
  }

  var modereport_detailnobukti = 0, modereport_detailcustomer = 1 ;
  var modereport_rekapnobukti = 2, modereport_rekapcustomer = 3;
  g_modeReport = modereport_detailnobukti;
  var jenisreport = 0; // ini untuk detail dan rekap

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailnobukti) {
      gcart_header = [
        ['NoBukti', 'Nobukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Cust Supp', 1, 'varchar', 0, 0],
        ['namabrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['qnt', 'Qnt', 1, 'float', 1, 2],
        ['SATUAN', 'Sat', 1, 'varchar', 0, 0],
        ['harga', 'Harga', 1, 'float', 1, 2],
        ['DISCTOT', 'Disc', 1, 'float', 1, 2],
        ['NoFakturPajak', 'No. FPJ', 1, 'varchar', 0, 0],
        ['TglFakturPajak', 'Tgl. FPJ', 1, 'date', 0, 0],
        ['NDPP', 'Nilai DPP', 1, 'float', 1, 2],
        ['NPPN', 'Nilai PPN', 1, 'float', 1, 2],
        ['NNET', 'Nilai Net', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if (g_modeReport == modereport_detailcustomer){
      gcart_header = [
        ['NoBukti', 'Nobukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Cust Supp', 1, 'varchar', 0, 0],
        ['namabrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['qnt', 'Qnt', 1, 'float', 1, 2],
        ['SATUAN', 'Sat', 1, 'varchar', 0, 0],
        ['harga', 'Harga', 1, 'float', 1, 2],
        ['DISCTOT', 'Disc', 1, 'float', 1, 2],
        ['NoFakturPajak', 'No. FPJ', 1, 'varchar', 0, 0],
        ['TglFakturPajak', 'Tgl. FPJ', 1, 'date', 0, 0],
        ['NDPP', 'Nilai DPP', 1, 'float', 1, 2],
        ['NPPN', 'Nilai PPN', 1, 'float', 1, 2],
        ['NNET', 'Nilai Net', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if (g_modeReport == modereport_rekapnobukti){
      gcart_header = [
        ['NoBukti', 'Nobukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Supplier', 1, 'varchar', 0, 0],
        ['NoFakturPajak', 'Faktur Pajak', 1, 'varchar', 0, 0],
        ['TglFakturPajak', 'Tgl. FPJ', 1, 'date', 0, 0],
        ['NOPO', 'No Nota', 1, 'date', 0, 0],
        ['NDPP', 'DPP', 1, 'float', 1, 2],
        ['NPPN', 'PPN', 1, 'float', 1, 2],
        ['NNET', 'Total', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if (g_modeReport == modereport_rekapcustomer){
      gcart_header = [
        ['NoBukti', 'Nobukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Supplier', 1, 'varchar', 0, 0],
        ['NoFakturPajak', 'Faktur Pajak', 1, 'varchar', 0, 0],
        ['TglFakturPajak', 'Tgl. FPJ', 1, 'date', 0, 0],
        ['NOPO', 'No Nota', 1, '', 0, 0],
        ['NDPP', 'DPP', 1, 'float', 1, 2],
        ['NPPN', 'PPN', 1, 'float', 1, 2],
        ['NNET', 'Total', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    }
  }

  function makeTable(_mode) {
    console.log(" makeTable jalankan mode:", _mode);

    let groupby = '';
    let _date1 = $("#inputDate1").val();
    let _date2 = $("#inputDate2").val();

    let input_oto = globalOtorisasi;
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
    } else {
      if (DetOrRekap === 0) {
        g_modeReport = modereport_detailcustomer;
        groupby = 'NoBukti';
      } else {
        g_modeReport = modereport_rekapcustomer;
        groupby = 'NoBukti';
      }
    }

    console.log("Mode report aktif:", g_modeReport, "| Group By:", groupby);

    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let data = {
      date1: _date1,
      date2: _date2,
      inputOto: globalOtorisasi,
      inputOrd: input_order,
      inputDetOrRekap: DetOrRekap,
    };

    console.log("Data terkirim ke server:", data);

    doMakeTable(_mode, groupby, data, "REPORT INVOICE PEMBELIAN", _date1, _date2, DetOrRekap);
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if ($("#inputOrder").val() == "N"){
      data = ['NoBukti', 'TANGGAL'];
    } else if ($("#inputOrder").val() == "S"){
      data = ['NoBukti', 'TANGGAL'];
    } else {
      data = ['NoBukti', 'NAMACUSTSUPP'];
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
    } else if ($("#inputOrder").val() == "S") {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailcustomer;
      } else {
        g_modeReport = modereport_rekapcustomer;
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

