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
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputOrder" data-bs-toggle="dropdown" aria-expanded="false" title="Order By">
          <i class="fas fa-exchange-alt" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputOrder">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="N" onclick="setOrderBy('N')">Nomor Bukti</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="B" onclick="setOrderBy('B')">Barang</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="S" onclick="setOrderBy('S')">Supplier</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="G" onclick="setOrderBy('G')">Gudang</a></li>
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

  // order by
  function setOrderBy(val) {
    globalOrderBy = val;
    let text = (val == 'N') ? 'Nomor Bukti' : (val == 'B') ? 'Barang' : (val == 'S') ? 'Supplier' : 'Gudang';
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

  var modereport_detailnobukti = 0, modereport_detailbarang = 1, modereport_detailcustomer = 2, modereport_detailgudang = 3;
  g_modeReport = modereport_detailnobukti;
  var jenisreport = 0; // ini untuk detail dan rekap

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailnobukti) {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NoPO', 'No PO', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NAMAMERK', 'Merk', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['NOPOCUST', 'PO Customer', 1, 'varchar', 0, 0],
        ['namacust', 'Nama Customer', 1, 'varchar', 0, 0],
        ['Qntx', 'QNT', 1, 'float', 1, 2],
        ['satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['KODELOKASI', 'Lokasi', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 0;

    } else if (g_modeReport == modereport_detailbarang){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NoPO', 'No PO', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['PartNumber', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['NOPOCUST', 'PO Customer', 1, 'varchar', 0, 0],
        ['Qntx', 'QNT', 1, 'float', 1, 2],
        ['satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['KODELOKASI', 'Lokasi', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;

    } else if(g_modeReport == modereport_detailcustomer){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NoPO', 'No PO', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['qnt', 'QNT', 1, 'float', 1, 2],
        ['satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['Qnt2', 'QNT', 1, 'float', 1, 2],
        ['satuan2', 'Satuan', 1, 'varchar', 0, 0],
        ['KODELOKASI', 'Lokasi', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;

    } else {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NoPO', 'No PO', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['PartNumber', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['qnt', 'QNT', 1, 'float', 1, 2],
        ['satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['Qnt2', 'QNT', 1, 'float', 1, 2],
        ['satuan2', 'Satuan', 1, 'varchar', 0, 0],
        ['KODELOKASI', 'Lokasi', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 0;
    }
  }

  function makeTable(_mode) {
    let groupby = '';
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();
    let input_order = globalOrderBy;

    if (input_order == "N") {
      g_modeReport = modereport_detailnobukti;
      groupby = 'NoBukti';
    } else if (input_order == "B") {
      g_modeReport = modereport_detailbarang;
      groupby = 'PartNumber';
    } else if (input_order == "S") {
      g_modeReport = modereport_detailcustomer;
      groupby = 'NAMACUSTSUPP';
    } else {
      groupby = 'NoBukti';
    }

    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }
    

    let data = {
      date1    : _date1,
      date2    : _date2,
      inputOrd : input_order,
    };

    doMakeTable(_mode, groupby, data, "REPORT PENGADAAN PR", _date1, _date2);
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if ($("#inputOrder").val() == "N"){
      data = ['NoBukti', 'TANGGAL'];
    } else if ($("#inputOrder").val() == "B"){
      data = ['PartNumber', 'NamaBrg'];
    } else if ($("#inputOrder").val() == "S"){
      data = ['NAMACUSTSUPP', 'KodeBrg'];
    } else {
      data = ['NoBukti', 'TANGGAL'];
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
    } else if ($("#inputOrder").val() == "S") {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailcustomer;
      } else {
        g_modeReport = modereport_rekapcustomer;
      }
    } else {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailgudang;
      } else {
        g_modeReport = modereport_rekapgudang;
      }
    }

    doSetHeader(g_modeReport);
    doShowCustomize();
  }


</script>

@endsection
