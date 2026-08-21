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
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputOrder" data-bs-toggle="dropdown" aria-expanded="false" title="Order By">
          <i class="fas fa-exchange-alt" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputOrder">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="N" onclick="setOrderBy('N')">Nomor Bukti</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="B" onclick="setOrderBy('B')">Barang</a></li>
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
    let text = (val == 'N') ? 'Nomor Bukti' : (val == 'B') ? 'Barang' : 'Supplier';
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

    
    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let data = {
      date1: _date1,
      date2: _date2,
      inputOrd: input_order,
      inputDetOrRekap: DetOrRekap,
    };

    console.log("Data terkirim ke server:", data);

    doMakeTable(_mode, groupby, data, "REPORT PENGADAAN OUTSTANDING PO", _date1, _date2, DetOrRekap);
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