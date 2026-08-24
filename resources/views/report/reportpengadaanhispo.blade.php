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
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputDataPilih" data-bs-toggle="dropdown" aria-expanded="false" title="Order By">
          <i class="fa-solid fa-filter" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputDataPilih" style="min-width: 600px; padding: 10px;">
          <li onclick="event.stopPropagation();">
            <!-- Your filter form here -->
            <div class="row text-center">
              <div class="col-2">
                <label for="inputlokasi">Customer</label>
              </div>
              
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputCust" placeholder="Customer" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelectCustomer()">+</button>
                  </div>
              </div>

              <div class="col-2">
                <label for="inputlokasi">Lokasi</label>
              </div>
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputLokasi" placeholder="Group" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelectLokasi()">+</button>
                  </div>
              </div>

            </div>


          </li>
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

<!-- start modal aktiva select customer -->
  <div class="modal fade"  id="formSelectCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Select Customer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table id="tabelSelectCustomer" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode</th>
                <th scope="col">Nama</th>
                <th scope="col">Kota</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>

            <tbody id="tabel_dataSelectCustomer" class="text-left" >
              {{-- <tr>

                <td></td>
                <td></td>
                <td></td>

                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button type="button" onclick="buttonPilihCustomer()"><i class="bi bi-pen">Select</i></button>
                  </td>
            </tr> --}}
            </tbody>


          </table>


      </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
          </div>
    </div>
  </div>
  </div>
<!-- End modal aktiva select customer-->

<!-- start modal aktiva select lokasi -->
  <div class="modal fade"  id="formSelectLokasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Select Lokasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table id="tabelSelectLokasi" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode Kebun</th>
                <th scope="col">Nama Kebun</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>

            <tbody id="tabel_dataSelectLokasi" class="text-left" >
              {{-- <tr>

                <td></td>
                <td></td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button type="button" onclick="buttonPilihLokasi()"><i class="bi bi-pen">Select</i></button>
                  </td>
            </tr> --}}
            </tbody>


          </table>


      </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
          </div>
    </div>
  </div>
  </div>
<!-- End modal aktiva select Lokasi-->
@endsection


@section('jsreport')
<script type="text/javascript">
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";

  let selectedCustomerCode = "";
  let selectedCustomerName = "";
  let selectedCustomerNamaKota = "";
  let originalIndex = null;
  let lastScrollPosition = 0;

  let selectedLokasiCode = "";
  let selectedLokasiName = "";

  $(document).ready(function () {
    $("#btnFilterData").on("click", function () {
      if (typeof doShowFormFilterData === "function") doShowFormFilterData();
      else alert(" Fungsi doShowFormFilterData belum tersedia.");
    });

    $("#btnCustomizeTable").on("click", function () {
      if (typeof doShowFormCustomizeTable === "function") doShowFormCustomizeTable();
      else alert(" Fungsi doShowFormCustomizeTable belum tersedia.");
    });

    $("#btnSubmitReport").on("click", function () {
      makeTable("REPORT");
    });

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


  var modereport_detail = 0, modereport_rekap = 1;
  g_modeReport = modereport_detail;

  function setDefaultHeader() {
    if (g_modeReport == modereport_detail) {
      gcart_header = [
        ['NOPO', 'No Bukti', 1, 'varchar', 0, 0],
        ['TGLPO', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['QNTPO', 'Qnt PO', 1, 'float', 1, 2],
        ['NOBELI', 'No. Po', 1, 'varchar', 0, 0],
        ['TGLBELI', 'Tgl. LPB', 1, 'date', 0, 0],
        ['QNTBELI', 'Qnt. Inv', 1, 'float', 1, 2],
        ['tglkirim', 'DUEDATE', 1, 'date', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    } else {
      gcart_header = [
        ['NOPO', 'No Bukti', 1, 'varchar', 0, 0],
        ['TGLPO', 'Tanggal', 1, 'date', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['QNTPO', 'Qnt PO', 1, 'float', 1, 2],
        ['NOBELI', 'No. Po', 1, 'varchar', 0, 0],
        ['TGLBELI', 'Tgl. LPB', 1, 'float', 1, 2],
        ['QNTBELI', 'Qnt. Inv', 1, 'float', 1, 2],
        ['tglkirim', 'DUEDATE', 1, 'date', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    }
  }

  function makeTable(_mode) {
    console.log(" makeTable dijalankan mode:", _mode);

    let groupby = "";
    let _date1 = $("#inputDate1").val();
    let _date2 = $("#inputDate2").val();

    let _inputCust = $("#inputCust").val();
    if (!_inputCust){
      _inputCust = '-'
    }

    let _inputLokasi = $("#inputLokasi").val();
    
    if (!_inputLokasi){
      _inputLokasi = '-'
    }

    console.log(_inputCust,_inputLokasi)

    let data = {
      date1: _date1,
      date2: _date2,
      inputCust: _inputCust,
      inputLokasi: _inputLokasi
    };

    console.log(" Data dikirim ke backend:", data);

    setDefaultHeader();
    if (typeof doSetHeader === "function") {
      doSetHeader(g_modeReport);
    }

    doMakeTable(
      _mode,
      groupby,
      data,
      "REPORT PENGADAAN HIS PO",
      _date1,
      _date2,
      _inputCust,
      _inputLokasi
    );
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if (g_modeReport == modereport_detail) {
      data = ['NOBELI', 'TGLPO'];
    } else {
      data = ['NOBELI', 'TGLPO'];
    }
    
    return data;
  }

  function reportMode(_mode) {
    if (jenisreport != _mode) {
      let prev_mode = jenisreport;
      jenisreport != _mode;

      $('#tombolmode' + prev_mode). removeClass ('btn-primary');
      $('#tombolmode' + prev_mode). addClass ('btn-outline-primary');

      $('#tombolmode' + prev_mode). removeClass ('btn-outline-primary');
      $('#tombolmode' + prev_mode). addClass ('btn-primary');

      setModeReport();
    }
  }

// js modal kode cust his po
  function buttonSelectCustomer () {
      loadSelectCustomer()
      $("#formSelectCustomer").modal('toggle')
    }

    function buttonPilihCustomer(selectedCustomer) {
      $("#inputCust").val(selectedCustomer);
      $("#formSelectCustomer").modal("hide");

    }

    function loadSelectCustomer() {
      console.log('asd');
      let _token = $("#_token").val();

      $('#tabelSelectCustomer').DataTable().destroy();

      $.ajax({
        url: "{!! url('laporanhispo_loadcustomer') !!}",
        type: "get",
        async: false,
        data: {
          _token: _token,
        },
        success: function (res) {
          console.log(res);
          dataRefresh = res;
        },
      });

      let rowTable = "";
      dataRefresh.forEach((item, i) => {
        let temp = "";

        rowTable += `<tr>
          <td>${item.KodeCustSupp}</td>
          <td>${item.NamaCustSupp}</td>
          <td>${item.NamaKota}</td>
          <td class="text-center">
            <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilihCustomer('${item.KodeCustSupp}')">+</button>
          </td>
        </tr>`;
      });

      document.getElementById("tabel_dataSelectCustomer").innerHTML = rowTable;
      $("#tabelSelectCustomer").DataTable({
        "lengthChange": false,
        "paging": true,
      });
    }
// end modal cust

// js modal lokasi his po
  function buttonSelectLokasi () {
    loadSelectLokasi()
    $("#formSelectLokasi").modal('toggle')
  }

  function buttonPilihLokasi(selectedLokasi) {
    $("#inputLokasi").val(selectedLokasi);
    $("#formSelectLokasi").modal("hide");

  }

  function loadSelectLokasi() {
    console.log('asd');
    let _token = $("#_token").val();

    $('#tabelSelectLokasi').DataTable().destroy();

    $.ajax({
      url: "{!! url('laporanhispo_loadlokasi') !!}",
      type: "get",
      async: false,
      data: {
        _token: _token,
      },
      success: function (res) {
        console.log(res);
        dataRefresh = res;
      },
    });

    let rowTable = "";
    dataRefresh.forEach((item, i) => {
      let temp = "";

      rowTable += `<tr>
        <td>${item.KodeKebun}</td>
        <td>${item.namaKebun}</td>
        <td class="text-center">
          <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilihLokasi('${item.namaKebun}')">+</button>
        </td>
      </tr>`;
    });

    document.getElementById("tabel_dataSelectLokasi").innerHTML = rowTable;
    $("#tabelSelectLokasi").DataTable({
      "lengthChange": false,
      "paging": true,
    });
  }
// end js

</script>

@endsection













{{-- @extends('newmaster')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-6 text-left">
      <h3>Report Pengadaan HIS PO</h1>
    </div>
  </div>
</div>

<div id="printContainer" style="display:none">

</div>
<div id="contentContainer" class="container-fluid">

        <div class="row">

        </div>
        <div class="row justify-content-center">
          <div class="card w-75">
            <div class="card-body">
              <div class="container-fluid">
                <div class="row rounded" style="background-color: #E8E8E8; padding: 10px; display: flex; justify-content: center;">
                  <div class="col-2" style="padding: 0 7px; flex-basis: 0;">
                    <label for="inputDate1" style="">Periode</label>
                  </div>
                  <div class="col-3" style="padding: 0 7px; flex-basis: 0;">
                    <input type="date" class="form-control" id="inputDate1" value="{!! date('Y-m-d') !!}">
                  </div>
                  <div class="col-2" style="padding: 0 7px; flex-basis: 0;">
                    <label for="inputDate2">s/d</label>
                  </div>
                  <div class="col-3" style="padding: 0 7px; flex-basis: 0;">
                    <input type="date" class="form-control" id="inputDate2" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>

                <div class="row text-center mt-4">
                  <div class="col-2">
                    <label for="inputCust">Kode Customer</label>
                  </div>
                  <div class="col-2">
                    <input type="text" id="inputCust" class="form-control" aria-label="Default input example" placeholder="Kode Cust" value="-" disabled>
                  </div>
                  <button type="button" class="btn btn-primary" style="font-size: 16px; margin: 0 5px;" onclick="buttonSelectCustomer()">Kode Cust</button>                  
                  <div class="col-2">
                    <label for="inputlokasi">Lokasi</label>
                  </div>
                  <div class="col-2">
                    <input type="text" id="inputLokasi" class="form-control" aria-label="Default input example" placeholder="Lokasi" value="-" disabled>
                  </div> 
                  <button type="button" class="btn btn-primary" style="font-size: 16px; margin: 0 5px;" onclick="buttonSelectLokasi()">Lokasi</button>
                </div>

                <br>
                <br>

<!-- start modal aktiva select customer -->
  <div class="modal fade"  id="formSelectCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Customer</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table id="tabelSelectCustomer" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode</th>
                <th scope="col">Nama</th>
                <th scope="col">Kota</th>
                <th scope="col">Actions</th>

              </tr>
            </thead>

            <tbody id="tabel_dataSelectCustomer" class="text-left" >
              <tr>

                <td></td>
                <td></td>
                <td></td>

                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button type="button" onclick="buttonPilihCustomer()"><i class="bi bi-pen">Select</i></button>
                  </td>
            </tr>
            </tbody>


          </table>


      </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
          </div>
    </div>
  </div>
  </div>
<!-- End modal aktiva select customer-->

<!-- start modal aktiva select lokasi -->
  <div class="modal fade"  id="formSelectLokasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Lokasi</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table id="tabelSelectLokasi" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode Kebun</th>
                <th scope="col">Nama Kebun</th>
                <th scope="col">Actions</th>

              </tr>
            </thead>

            <tbody id="tabel_dataSelectLokasi" class="text-left" >
              <tr>

                <td></td>
                <td></td>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button type="button" onclick="buttonPilihLokasi()"><i class="bi bi-pen">Select</i></button>
                  </td>
            </tr>
            </tbody>


          </table>


      </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
          </div>
    </div>
  </div>
  </div>
<!-- End modal aktiva select Lokasi-->

                <div class="row pr-3" style="display: flex; justify-content: right;">
                  <button type="button" class="btn btn-primary" style="font-size: 16px; margin: 0 5px;" onclick="showFormCustomizeTable()">Customize Table</button>
                  <button type="button" class="btn btn-primary" style="font-size: 16px; margin: 0 5px;" onclick="makeTable()">Submit</button>
                </div>

              </div>
            </div>
          </div>
        </div>

        <div class="container-fluid mt-6">
          <div id="showTableReport" style="display:none; background-color: white; padding: 10px" class="row mt-4 rounded">
            <div class="col-12 text-right">
              <button type="button" class="btn btn-success" onclick="exportTableToExcel('tabel')">Export to Excel</button>
              <button type="button" class="btn btn-secondary" onclick="closeTable()">Close Table</button>
            </div>
            <div class="col-12 mt-4" style="overflow:auto;">
              <div class="">
                <table id="tabel" class="table table-bordered table-striped">

                  <thead id="tabel_header" class="text-left" >
                  </thead>

                  <tbody id="tabel_data" class="text-center"  style="border: 1px solid black; text-align: center;">
                  </tbody>

                </table>
              </div>
            </div>
          </div>
        </div>
</div>

@endsection

@section('js')
<script type="text/javascript">

// js modal kode cust his po
  function buttonSelectCustomer () {
    loadSelectCustomer()
    $("#formSelectCustomer").modal('toggle')
  }

  function buttonPilihCustomer(selectedCustomer) {
    $("#inputCust").val(selectedCustomer);
    $("#formSelectCustomer").modal("hide");

  }

  function loadSelectCustomer() {
    console.log('asd');
    let _token = $("#_token").val();

    $('#tabelSelectCustomer').DataTable().destroy();

    $.ajax({
      url: "{!! url('laporanhispo_loadcustomer') !!}",
      type: "get",
      async: false,
      data: {
        _token: _token,
      },
      success: function (res) {
        console.log(res);
        dataRefresh = res;
      },
    });

    let rowTable = "";
    dataRefresh.forEach((item, i) => {
      let temp = "";

      rowTable += `<tr>
        <td>${item.KodeCustSupp}</td>
        <td>${item.NamaCustSupp}</td>
        <td>${item.NamaKota}</td>
        <td class="text-center">
          <button class="btn btn-success btn-sm" type="button" onclick="buttonPilihCustomer('${item.KodeCustSupp}')">Select</button>
        </td>
      </tr>`;
    });

    document.getElementById("tabel_dataSelectCustomer").innerHTML = rowTable;
    $("#tabelSelectCustomer").DataTable({
      "lengthChange": false,
      "paging": false,
    });
  }
// end modal cust

// js modal lokasi his po
    function buttonSelectLokasi () {
    loadSelectLokasi()
    $("#formSelectLokasi").modal('toggle')
  }

  function buttonPilihLokasi(selectedLokasi) {
    $("#inputLokasi").val(selectedLokasi);
    $("#formSelectLokasi").modal("hide");

  }

  function loadSelectLokasi() {
    console.log('asd');
    let _token = $("#_token").val();

    $('#tabelSelectLokasi').DataTable().destroy();

    $.ajax({
      url: "{!! url('laporanhispo_loadlokasi') !!}",
      type: "get",
      async: false,
      data: {
        _token: _token,
      },
      success: function (res) {
        console.log(res);
        dataRefresh = res;
      },
    });

    let rowTable = "";
    dataRefresh.forEach((item, i) => {
      let temp = "";

      rowTable += `<tr>
        <td>${item.KodeKebun}</td>
        <td>${item.namaKebun}</td>
        <td class="text-center">
          <button class="btn btn-success btn-sm" type="button" onclick="buttonPilihLokasi('${item.namaKebun}')">Select</button>
        </td>
      </tr>`;
    });

    document.getElementById("tabel_dataSelectLokasi").innerHTML = rowTable;
    $("#tabelSelectLokasi").DataTable({
      "lengthChange": false,
      "paging": false,
    });
  }
// end js

    var modereport_detail = 0, modereport_rekap = 1;
    var report_mode = modereport_detail;

    var cart_headerDetail = [], cart_headerRekap = [];

    var cart_filter = [];


    $(document).ready(function(){
      setHeaderDetail();
      setHeaderRekap();
    });

    function reportMode(_mode) {
      if (report_mode != _mode) {
        let prev_mode = report_mode;
        report_mode = _mode;

        $("#buttonMode" + prev_mode).removeClass("btn-primary");
        $("#buttonMode" + prev_mode).addClass("btn-outline-primary");

        $("#buttonMode" + report_mode).removeClass("btn-outline-primary");
        $("#buttonMode" + report_mode).addClass("btn-primary");
      }
    }

  function closeTable () {
    document.getElementById("showTableReport").style.display = "none"
  }

  function showReport(res) {
    let date1    = $("#inputDate1").val();
    let date2    = $("#inputDate2").val();

    let tempcart = (report_mode == modereport_detail) ? cart_headerDetail : cart_headerRekap;
    let _cellcount = 1;
    tempcart.forEach((item, i) => {
      _cellcount += item[2];
    });
    
    let rowTable = "";
    let _qnt = 0.00, _hpp = 0;
    let showQnt = false, showHPP = false;
    let posArray = [], posCount = 0;

    // TABLE HEADER
    rowTable += '<tr>';
    rowTable += '  <th colspan="' + _cellcount + '" style="text-align: left; font-weight: bold;">PT. MITRA GLOBALINDO LESTARI<br/> REPORT PENGADAAN HIS (PO)</th>';
    rowTable += '</tr>';
    rowTable += '<tr>';
    rowTable += '  <th colspan="' + _cellcount + '"  style="text-align: left; font-weight: bold;">PERIODE: ' + format_date(date1, true) + ' S.D ' + format_date(date2, true) + '</th>';
    rowTable += '</tr>';
    rowTable += '<tr>';
    rowTable += '  <th colspan="' + _cellcount + '"  style="text-align: left; font-weight: bold;">Dicetak Oleh :  ' + '  {!! \Auth::user()->username !!}  //  Tanggal : '+ (dateIndo) +' // Jam : ' + padZero(dateHours) + ':' + padZero(dateMinutes) + ':' + padZero(dateSeconds) + '</th>';
    rowTable += '</tr>';
    rowTable += '<tr>';
    rowTable += '  <th colspan="' + _cellcount + '"></th>';
    rowTable += '</tr>';

    rowTable += '<tr style="height: 45px; padding: 20px; " class="text-center bg-dark text-light">';
    tempcart.forEach((item, i) => {
      if (item[2]) {
        posCount += 1;
        if (item[0] == "Nomor") {
          rowTable += '  <th scope="col" style="border: 1px solid black;">No</th>';
        } else {
          rowTable += '  <th scope="col" style="border: 1px solid black;">' + item[1] + '</th>';
        }
        
        if (item[0] == "Qnt")      { showQnt = true; posArray.push(posCount); }
        if (item[0] == "NilaiHPP") { showHPP = true; posArray.push(posCount); }
      }
    });
    rowTable += '</tr>';
    $("#tabel_header").html(rowTable);



    // TABLE DATA
    let _prevnb = "", _nownb = "";
    rowTable = '';
    if (res.length > 0) {
      res.forEach((item, i) => {
        // _nownb = (_prevnb == item.NoBukti) ? "" : item.NoBukti;
        _nownb = item.NOBELI;
        rowTable += "<tr style='text-align: center'>";
        tempcart.forEach((itemcart, j) => {
          if (itemcart[2]) {
            if (itemcart[0] == "NOBELI") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black;">' + _nownb + '</td>';
            } else if (itemcart[0] == "Nomor") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black;">' + (i+1) + '</td>';
            } else if (itemcart[0] == "TGLPO") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black;">' + format_date(item.TGLPO) + '</td>';
            } else if (itemcart[0] == "QNTPO") {
              _qnt += toFloat(item.QNTPO);
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; text-align: right;">' + item.QNTPO + '</td>';
            } else if (itemcart[0] == "NAMABRG") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; white-space:nowrap;">' + (item.NAMABRG) + '</td>';
            } else if (itemcart[0] == "NAMACUSTSUPP") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; white-space:nowrap;">' + (item.NAMACUSTSUPP) + '</td>';
            } else if (itemcart[0] == "NilaiHPP") {
              _hpp = toFloat(numberNoDecimals(item.NilaiHPP));
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; text-align: right;">' + numberNoDecimals(item.NilaiHPP) + '</td>';
            } else if (itemcart[0] == "QNTBELI") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; white-space: nowrap;">' + nullToEmpty(item.QNTBELI) + '</td>';
            } else if (itemcart[0] == "tglkirim") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black;">' + format_date(item.tglkirim) + '</td>';
            } else if (itemcart[0] == "TGLBELI") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black;">' + format_date(item.TGLBELI) + '</td>';
            } else {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black;">' + item[itemcart[0]] + '</td>';
            }
          }
        });
        rowTable += "</tr>";

        _prevnb = item.NOBELI;
      })

      // TABLE FOOTER
      if (showQnt || showHPP) {
        let posStrTotal = (posArray.length > 0) ? Math.min(...posArray)-1 : 0;

        rowTable += "<tr style='text-align: center'>";
        tempcart.forEach((item, i) => {
          if (i+1 == posStrTotal) {
            rowTable += '  <td style="border-bottom-style: hidden; border-right-style: hidden; border-left-style: hidden; font-weight: bold; text-align: right;">Total :</td>';
          } else if (item[2]) {
            if (item[0] == "QNTPO") {
              rowTable += '  <td style="border-bottom-style: hidden; border-right-style: hidden; font-weight: bold; text-align: right;">' + _qnt.toFixed(2) + '</td>';
            } else if (item[0] == "NilaiHPP") {
              rowTable += '  <td style="border-bottom-style: hidden; border-right-style: hidden; font-weight: bold; text-align: right;">' + _hpp + '</td>';
            } else {
              rowTable += '  <td style="border-bottom-style: hidden; border-right-style: hidden; border-left-style: hidden; font-weight: bold; text-align: right;"></td>';
            }
          }
        });
        rowTable += "</tr>";
      }

    } else {
        rowTable += "<tr style='text-align: center'>";
        rowTable += '  <td colspan="' + _cellcount + '" style="border: 1px solid black;">Tidak ada data ditemukan</td>';
        for (let i = 0; i < (_cellcount-1); i++) {
          rowTable += '  <td style="display: none;"></td>';
        }
        rowTable += "</tr>";
    }

    $("#tabel_data").html(rowTable);
    godown();
  }

  function makeTable() {
    let date1    = $("#inputDate1").val();
    let date2    = $("#inputDate2").val();
    let inputLokasi = $("#inputLokasi").val();
    let inputCust = $("#inputCust").val();

    document.getElementById("showTableReport").style.display = "block"
    $.ajax({
      url     : "{!! url('laporanhispo_doReport') !!}",
      type    : "get",
      async   : false,
      data    : {
        date1,
        date2,
        inputLokasi,
        inputCust
      },
      success: function(res) {
        showReport(res);
        alertify.success("Report ditampilkan");
      }
    })
  }

  //======== CUSTOMIZE TABLE FORM ========================

  function setHeaderDetail(_isReset = false) {
    let _strHeader = (!_isReset) ? doLoadHeader('{!! $akses['href'] !!}', modereport_detail) : "";

    if (_strHeader != "") {
      cart_headerDetail = doGetHeader(_strHeader);
    } else {
      cart_headerDetail = [
        ['Nomor', 'Nomor (No)', 1],
        ['NOBELI', 'No Bukti', 1],
        ['TGLPO', 'Tanggal', 1],
        ['NAMACUSTSUPP', 'Nama Supplier', 1],
        ['NAMABRG', 'Nama Barang', 1],
        ['QNTPO', 'Qnt PO', 1],
        ['NOPO', 'No. Po', 1],
        ['TGLBELI', 'Tgl. LPB', 1],
        ['QNTBELI', 'Qnt. Inv', 1],
        ['tglkirim', 'DUEDATE', 1]
      ];

      doSimpanHeader('{!! $akses['href'] !!}', modereport_detail, cart_headerDetail);
    }
  }

  function setHeaderRekap(_isReset = false) {
    let _strHeader = (!_isReset) ? doLoadHeader('{!! $akses['href'] !!}', modereport_rekap) : "";

    if (_strHeader != "") {
      cart_headerRekap = doGetHeader(_strHeader);
    } else {
      cart_headerDetail = [
        ['Nomor', 'Nomor (No)', 1],
        ['NOBELI', 'No Bukti', 1],
        ['TGLPO', 'Tanggal', 1],
        ['NAMACUSTSUPP', 'Nama Supplier', 1],
        ['NAMABRG', 'Nama Barang', 1],
        ['QNTPO', 'Qnt PO', 1],
        ['NOPO', 'No. Po', 1],
        ['TGLBELI', 'Tgl. LPB', 1],
        ['QNTBELI', 'Qnt. Inv', 1],
        ['tglkirim', 'DUEDATE', 1]
      ];

      doSimpanHeader('{!! $akses['href'] !!}', modereport_rekap, cart_headerRekap);
    }
  }

  function resetHeader() {
    switch (report_mode) {
      case modereport_detail :
            setHeaderDetail(true);
            break;
                     
      case modereport_rekap :
            setHeaderRekap(true);
            break;
     
      default :
                return;
    }

    showCustomize();
  }

  function showCustomize() {
    let str = "";
    let tempcart = (report_mode == modereport_detail) ? cart_headerDetail : cart_headerRekap;

    tempcart.forEach((item, i) => {
      let _checked = (item[2]) ? 'btn-success' : 'btn-outline-danger';
      let _icon_eye = (item[2]) ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
      str += '<div class="row justify-content-center text-center">';
      str += '  <div class="col-2 ' + _checked + ' text-center header-toggle" id="buttonHeader' + i + '" onclick="buttonVisibility(' + i + ')">' + _icon_eye + '</div>';
      str += '  <div class="col-5 btn-outline-dark text-center header-toggle disabled" draggable="true">' + item[1] + '</div>';
      str += '  <div class="col-2 btn-primary text-center header-toggle" id="buttonUp' + i + '" onclick="buttonUpDown(' + i + ', 0)"><i class="bi bi-arrow-up"></i></div>';
      str += '  <div class="col-2 btn-primary text-center header-toggle" id="buttonDown' + i + '" onclick="buttonUpDown(' + i + ', 1)"><i class="bi bi-arrow-down"></i></div>';
      str += '</div>';
    });

    $("#tabelcustomize_data").html(str);

  }

  function buttonVisibility(_id) {
    if (report_mode == modereport_detail) {
      if (cart_headerDetail[_id][2] == 1) {
        $("#buttonHeader" + _id).removeClass("btn-success");
        $("#buttonHeader" + _id).addClass("btn-outline-danger");
        $("#buttonHeader" + _id).html('<i class="bi bi-eye-slash"></i>');
        cart_headerDetail[_id][2] = 0;
      } else {
        $("#buttonHeader" + _id).removeClass("btn-outline-danger");
        $("#buttonHeader" + _id).addClass("btn-success");
        $("#buttonHeader" + _id).html('<i class="bi bi-eye"></i>');
        cart_headerDetail[_id][2] = 1;
      }
      doSimpanHeader('{!! $akses['href'] !!}', modereport_detail, cart_headerDetail);
    } else {
      if (cart_headerRekap[_id][2] == 1) {
        $("#buttonHeader" + _id).removeClass("btn-success");
        $("#buttonHeader" + _id).addClass("btn-outline-danger");
        $("#buttonHeader" + _id).html('<i class="bi bi-eye-slash"></i>');
        cart_headerRekap[_id][2] = 0;
      } else {
        $("#buttonHeader" + _id).removeClass("btn-outline-danger");
        $("#buttonHeader" + _id).addClass("btn-success");
        $("#buttonHeader" + _id).html('<i class="bi bi-eye"></i>');
        cart_headerRekap[_id][2] = 1;
      }
      doSimpanHeader('{!! $akses['href'] !!}', modereport_rekap, cart_headerRekap);
    }
  }

  function buttonUpDown(_id, _mode) {
    // mode = 0 UP, 1 DOWN
    let temp = [];
    let _idx = (_mode == 0) ? _id-1 : _id+1;

    if (report_mode == modereport_detail) {
      let _isNotEdge = (_mode == 0) ? (_id > 0) : (_id < cart_headerDetail.length-1);

      if (_isNotEdge) {
        temp.push(cart_headerDetail[_idx][0]);
        temp.push(cart_headerDetail[_idx][1]);
        temp.push(cart_headerDetail[_idx][2]);

        cart_headerDetail[_idx][0] = cart_headerDetail[_id][0];
        cart_headerDetail[_idx][1] = cart_headerDetail[_id][1];
        cart_headerDetail[_idx][2] = cart_headerDetail[_id][2];

        cart_headerDetail[_id] = temp;
        doSimpanHeader('{!! $akses['href'] !!}', modereport_detail, cart_headerDetail);
        showCustomize();
      }
    } else {
      let _isNotEdge = (_mode == 0) ? (_id > 0) : (_id < cart_headerDetail.length-1);

      if (_isNotEdge) {
        temp.push(cart_headerRekap[_idx][0]);
        temp.push(cart_headerRekap[_idx][1]);
        temp.push(cart_headerRekap[_idx][2]);

        cart_headerRekap[_idx][0] = cart_headerRekap[_id][0];
        cart_headerRekap[_idx][1] = cart_headerRekap[_id][1];
        cart_headerRekap[_idx][2] = cart_headerRekap[_id][2];

        cart_headerRekap[_id] = temp;
        doSimpanHeader('{!! $akses['href'] !!}', modereport_rekap, cart_headerRekap);
        showCustomize();
      }
    }
  }

  //======== FILTER DATA FORM ========================

  function showFilter() {
    let date1    = $("#inputDate1").val();
    let date2    = $("#inputDate2").val();
    let inputOrd = $("#inputOrder").val();

    var str = "";
    $.ajax({
      url     : "{!! url('laporanhispo_doFilter') !!}",
      type    : "get",
      async   : false,
      data    : {
        date1,
        date2,
        inputOrd
      },
      success: function(res) {
        cart_filter = [];

        let _head1 = (inputOrd == "N") ? "Nomor Bukti" : "Kode Barang";
        let _head2 = (inputOrd == "N") ? "Tanggal" : "Nama Barang";
        $("#tabelfilter_header").html('<tr><th>' + _head1 + '</th><th>' + _head2 + '</th></tr>');

        if (res.length > 0) {
          res.forEach((item, i) => {
            let _data1 = (inputOrd == "N") ? item.NOBELI : item.KodeBrg;
            let _data2 = (inputOrd == "N") ? format_date(item.Tanggal) : item.NamaBrg;

            str += '<tr id="' + i + '-trrowfilter" draggable="true" onclick="selectrowfilter(' + i + ')">';
            str += '  <td>' + _data1 + '</td>';
            str += '  <td>' + _data2 + '</td>';
            str += '</tr>';

            let temp = [];
            temp.push(_data1);
            temp.push(false);
            cart_filter.push(temp);
          });
        } else {
            str += '<tr>';
            str += '  <td colspan="2" class="text-center">Tidak ada transaksi ditemukan.</td>';
            str += '  <td style="display: none;"></td>';
            str += '</tr>';
        }
      }
    })

    $("#tabelfilter_data").html(str);
  }

  function selectrowfilter(_row) {
    let _row_start, _row_end;

    if (!event.shiftKey) {
      _row_start = _row;
      _row_end = _row;
    } else {
      if (_row > g_lastrowfilter) {
        _row_start = g_lastrowfilter + 1;
        _row_end = _row;
      } else if (_row < g_lastrowfilter) {
        _row_start = _row;
        _row_end = g_lastrowfilter - 1;
      } else {
        _row_start = _row;
        _row_end = _row;
      }
    }

    while (_row_start <= _row_end) {
      if (cart_filter[_row_start][1]) {
        // unselect
        $("#"+_row_start+"-trrowfilter").css('background-color', '');
        $("#"+_row_start+"-trrowfilter").css('color', '');
      } else {
        // select
        $("#"+_row_start+"-trrowfilter").css('background-color', '#0069d9');
        $("#"+_row_start+"-trrowfilter").css('color', 'white');
      }

      cart_filter[_row_start][1] = !cart_filter[_row_start][1];
      _row_start++;
    }

    g_lastrowfilter = _row;
  }

  function showReportFilter() {
    if (cart_filter.length <= 0) { closeFormFilterData(); return; }

    let inputOrd = $("#inputOrder").val();
    let _listdata = [];
    cart_filter.forEach((item, i) => {
      if (item[1]) {
        _listdata.push(item[0]);
      }
    });

    document.getElementById("showTableReport").style.display = "block"
    $.ajax({
      url     : "{!! url('laporanhispo_doReportFilter') !!}",
      type    : "get",
      async   : false,
      data    : {
        listdata : _listdata,
        inputOrd
      },
      success: function(res) {
        showReport(res);
        alertify.success("Report ditampilkan");
      }
    })

    closeFormFilterData();
  }

</script>




@endsection --}}
