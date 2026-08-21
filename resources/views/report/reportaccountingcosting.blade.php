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

<!-- warna header -->
  <style>
    table, th, td {
      border: 1px solid black !important;
      border-collapse: collapse !important;
    }

    /* Override semua TH header dari kedua function */
    tr.text-center.bg-dark.text-light th {
      background-color: #646668 !important; /* warna abu muda */
      color: white !important;
      text-align: center !important;
      font-weight: bold !important;
    }

    /* Untuk memastikan nested header juga ikut ke-style */
    .tabel_header_kolom th,
    .tabel_header_kolom tr.text-center.bg-dark.text-light th {
      background-color: #646668 !important;
      color: white !important;
      font-weight: bold !important;
    }
  </style>
<!-- warna header -->

@section('header2')
  <div class="w-100 bg-light shadow-sm py-3 px-4 border-bottom d-flex align-items-center justify-content-between" style="margin-top:-20px; margin-bottom:150px;">
    <!-- Kiri: ikon -->
    <div class="d-flex" style="gap: 10px;">
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnPeriode" data-bs-toggle="dropdown" aria-expanded="false" title="Periode">
          <i class="fas fa-calendar-alt"></i>
        </button>
        <div class="dropdown-menu p-3" style="min-width: 350px;">
          <input type="month" class="form-control mt-1" id="inputDate2" value="{!! date('Y-m') !!}">
        </div>
      </div>
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputDataPilih" data-bs-toggle="dropdown" aria-expanded="false" title="Perkiraan">
          <i class="fa-solid fa-filter" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputDataPilih" style="min-width: 600px; padding: 10px;">
          <li onclick="event.stopPropagation();">
            <!-- Your filter form here -->
            <div class="row text-center">
              <div class="col-2">
                <label for="inputPerkiraan">Costing</label>
              </div>
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputPerkiraan" placeholder="Group" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelectPerkiraan()">+</button>
                  </div>
              </div>

              <div class="col-2">
                <label for="inputsubcosting">Sub Costing</label>
              </div>
              <div class="col-4 input-group">
                <input type="text" class="form-control" id="inputSubCosting" placeholder="Group" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelectSubCosting()">+</button>
                  </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputReportMode" data-bs-toggle="dropdown" aria-expanded="false" title="Report Mode" style="cursor: pointer;">
          <i class="fas fa-book"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownReportMode" aria-labelledby="inputReportMode">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setReportMode('1')">Bulan</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setReportMode('2')">Tahun</a></li>
        </ul>
      </div>
    </div>

    <!-- Kanan: tombol aksi menempel ke ujung kanan layar -->
    <div class="d-flex ms-auto" style="gap: 8px;">
      {{-- <button type="button" class="btn btn-outline-primary" onclick="doShowFormFilterData()" title="Filter Data">
        <i class="fas fa-magnifying-glass"></i>
      </button> --}}
      <button type="button" class="btn btn-outline-primary" onclick="doShowFormCustomizeTable()" title="Customize Table">
        <i class="fas fa-cog"></i>
      </button>
      <button type="button" class="btn btn-outline-primary" onclick="makeTable('REPORT')" title="Submit">
        <i class="fas fa-check"></i>
      </button>
    </div>
  </div>

<!-- start modal aktiva select perkiraan -->
  <div class="modal fade"  id="formSelectPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Select Costing</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table id="tabelSelectPerkiraan" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode Costing</th>
                <th scope="col">Nama Costing</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>

            <tbody id="tabel_dataSelectPerkiraan" class="text-left" >
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
<!-- End modal aktiva select perkiraan-->

<!-- start modal aktiva select subcosting -->
  <div class="modal fade"  id="formSelectSubCosting" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Select Sub Costing</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table id="tabelSelectSubCosting" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode Sub Costing</th>
                <th scope="col">Nama Sub Costing</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>

            <tbody id="tabel_dataSelectSubCosting" class="text-left" >
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
<!-- End modal aktiva -->
@endsection

@section('jsreport')
<script type="text/javascript">
  let globalDate2 = "{!! date('Y-m') !!}";
  let globalReportMode = "1"; // default: bulan
  let g_reportTitle = "";
  let g_cellcount = 0;
  let g_inputPerkiraan = "";
  let g_inputSubCosting = "";

  $(document).ready(function () {
    $("#btnCustomizeTable").on("click", function () {
      if (typeof doShowFormCustomizeTable === "function") doShowFormCustomizeTable();
      else alert(" Fungsi doShowFormCustomizeTable belum tersedia.");
    });
    
    $("#btnSubmitReport").on("click", function () {
      makeTable("REPORT");
    });

    showPeriode();
    setReportMode(globalReportMode);

    $("#inputPerkiraan").val("");
    $("#inputSubCosting").val("");

    setTimeout(() => {
        makeTable("REPORT");
    }, 100);
  });

  // periode
  function showPeriode() {
    globalDate2 = $('#inputDate2').val();
    g_date2 = globalDate2;
    console.log("PERIODE DIPILIH:", globalDate2);
  }

  // mode report
  function setReportMode(val) {
    globalReportMode = val;
    jenisreport = Number(val);   // 1 = Rp, 2 = Valas
    DetOrRekap = Number(val);    

    $('#dropdownReportMode .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ✔', '').trim();
      $(this).text(itemText);
    });

    $(`#dropdownReportMode .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">✔</span>`);
    });

    // update g_modeReport sesuai pilihan order & detail/rekap
    // setModeReport() sudah mengatur g_modeReport berdasarkan $("#inputOrder").val() dan jenisreport/DetOrRekap
    setModeReport();
  }

  var modereport_detail = 1, modereport_rekap = 2;
  g_modeReport = modereport_detail;

  function setDefaultHeader() {
    if (g_modeReport == modereport_detail) {
      gcart_header = [
        ['tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['KodePerk', 'Perkiraan', 1, 'varchar', 0, 0],
        ['Nama', 'Nama Perkiraan', 1, 'varchar', 0, 0],
        ['KETERANGAN', 'Keterangan', 1, 'varchar', 0, 0],
        ['Saldo', 'Jumlah', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    } else {
      gcart_header = [
        ['KodePerk', 'Perkiraan', 1, 'date', 0, 0],
        ['KETERANGAN', 'Nama Perkiraan', 1, 'varchar', 0, 0],
        ['saldo1', 'Januari', 1, 'float', 1, 2],
        ['saldo2', 'Februari', 1, 'float', 1, 2],
        ['saldo3', 'Maret', 1, 'float', 1, 2],
        ['saldo4', 'April', 1, 'float', 1, 2],
        ['saldo5', 'Mei', 1, 'float', 1, 2],
        ['saldo6', 'Juni', 1, 'float', 1, 2],
        ['saldo7', 'Juli', 1, 'float', 1, 2],
        ['saldo8', 'Agustus', 1, 'float', 1, 2],
        ['saldo9', 'September', 1, 'float', 1, 2],
        ['saldo10', 'Oktober', 1, 'float', 1, 2],
        ['saldo11', 'November', 1, 'float', 1, 2],
        ['saldo12', 'Desember', 1, 'float', 1, 2],
        ['total', 'Total', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    }
  }

  function doMakeTableSaldoAwal(_data, callback) {
    $.ajax({
        url: "{{ url('/reportaccountingcosting_saldoawal') }}",
        type: "get",
        data: _data,
        success: function(res) {
            window.resSaldoAwal = res.res2?.[0] || {};
            console.log("Saldo Awal Ready", window.resSaldoAwal);

            if (typeof callback === "function") callback();
        }
    });
  }

  function loadSaldoAwal(_data) {

  let url = "{{ url('/reportaccountingcosting_saldoawal') }}";

    $.ajax({
        url: url,
        type: "get",
        data: _data,
        success: function (res) {
            console.log("=== SALDO AWAL SP2 ===", res);

            window.resSaldoAwal = res.res2?.[0] || {};
        }
    });
  }
  
  function setRowHeader() {
      let rowHeader = "";

      rowHeader += '<tr>';
      rowHeader += '<th colspan="15" style="text-align: left; font-weight: bold;">' +
                  '{!! $akses["program"] !!}<br/>' + g_reportTitle +
                  '</th>';
      rowHeader += '</tr>';

      if (g_date2 && g_date2 !== 'undefined' && g_date2 !== '') {
        let tahun = g_date2.substring(0, 4);
        let bulan = g_date2.substring(5, 7);
        let namaBulan = [
            "", "JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI",
            "JULI", "AGUSTUS", "SEPTEMBER", "OKTOBER", "NOVEMBER", "DESEMBER"
        ][parseInt(bulan)];

        rowHeader += '<tr>';
        rowHeader += '<th colspan="15" style="text-align: left; font-weight: bold;">PERIODE: ' +
                    namaBulan + " " + tahun +
                    '</th>';
        rowHeader += '</tr>';
      }

      rowHeader += '<tr>';
      rowHeader += '<th colspan="15" style="text-align: left; font-weight: bold;">Costing: ' +
                  g_inputPerkiraan +
                  '</th>';
      rowHeader += '</tr>';

      rowHeader += '<tr>';
      rowHeader += '<th colspan="15" style="text-align: left; font-weight: bold;">Sub Costing: ' +
                  g_inputSubCosting +
                  '</th>';
      rowHeader += '</tr>';

      rowHeader += '<tr>';
      rowHeader += '<th colspan="15" style="text-align: left; font-weight: bold;">Dicetak Oleh : {!! $akses["user"] !!} // Tanggal : ' +
                  getDateIndo() +
                  ' // Jam : ' + getTimeNow() +
                  '</th>';
      rowHeader += '</tr>';

      rowHeader += '<tr><th colspan="15"></th></tr>';

      if (g_modeReport == modereport_detail) {
          rowHeader = setRowHeaderQtyOrRp(rowHeader);
      } else {
          rowHeader = setRowHeaderQtyRp(rowHeader);
      }

      return rowHeader;
  }

  function setRowHeaderQtyOrRp(_rowHeader) {
    _rowHeader += '<tr style="height: 45px; padding: 20px; " class="text-center bg-dark text-light">';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Tanggal</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Perkiraan</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Nama Perkiraan</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Keterangan</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Jumlah</th>';
    _rowHeader += '</tr>';

    return _rowHeader;
  }

  function setRowHeaderQtyRp(_rowHeader) {
    _rowHeader += '<tr style="height: 45px; padding: 20px; " class="text-center bg-dark text-light">';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Perkiraan</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Nama Perkiraan</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Januari</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Februari</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Maret</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">April</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Mei</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Juni</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Juli</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Agustus</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">September</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Oktober</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">November</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Desember</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Total</th>';
    _rowHeader += '</tr>';

    return _rowHeader;
  }

  function makeTable (_mode) {
    let groupby = (DetOrRekap === 1) ? "tanggal" : "KodePerk";

    let _date2 = $('#inputDate2').val();
    let _inputPerkiraan = $("#inputPerkiraan").val() || '-';
    let _inputSubCosting = $("#inputSubCosting").val() || '-';

    g_reportTitle = "REPORT ACCOUNTING COSTING";
    g_inputPerkiraan = _inputPerkiraan;
    g_inputSubCosting = _inputSubCosting;
    g_date2 = $('#inputDate2').val();

    let data = {
      date2: _date2,
      inputPerkiraan: _inputPerkiraan,
      inputSubCosting: _inputSubCosting,
      detOrRekap: DetOrRekap
    };

    console.log("REPORT:", data);

    // SP1
    if (DetOrRekap === 1) {

      doMakeTableSaldoAwal(data, function () {
        doMakeTable(
          _mode,
          groupby,
          data,
          _date2
        );
      });

    }
    // SP2
    else {

      $.ajax({
        url: "{{ url('/reportaccountingcosting_saldoawal') }}",
        type: "get",
        data: data,
        success: function (res) {

          console.log("DATA TAHUN (SP2):", res);

          window.res1 = res.res2 || [];
          window.resSaldoAwal = {};

          doMakeTable(
            _mode,
            groupby,
            data,
            _date2
          );
        }
      });

    }
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if (g_modeReport == modereport_detail) {
      data = ['nobukti', 'tanggal'];
    } else {
      data = ['nobukti', 'tanggal'];
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

  function setModeReport() {
      if (jenisreport === 1) {
        g_modeReport = modereport_detail;
      } else {
        g_modeReport = modereport_rekap;
      }

    doSetHeader(g_modeReport);
    doShowCustomize();  
  }

// js modal perkiraan
  function buttonSelectPerkiraan () {
    loadSelectPerkiraan()
    $("#formSelectPerkiraan").modal('toggle')
  }

  function buttonPilihPerkiraan(selectedPerkiraan) {
    $("#inputPerkiraan").val(selectedPerkiraan);
    $("#formSelectPerkiraan").modal("hide");

  }

  function loadSelectPerkiraan() {
    console.log('asd');
    let _token = $("#_token").val();

    $('#tabelSelectPerkiraan').DataTable().destroy();

    $.ajax({
      url: "{!! url('reportaccountingcosting_loadperkiraan') !!}",
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
        <td>${item.KodeCost}</td>
        <td>${item.NamaCost}</td>
        <td class="text-center">
          <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilihPerkiraan('${item.KodeCost}')">+</button>
        </td>
      </tr>`;
    });

    document.getElementById("tabel_dataSelectPerkiraan").innerHTML = rowTable;
    $("#tabelSelectPerkiraan").DataTable({
      "lengthChange": false,
      "paging": true,
    });
  }
// end js

// js modal subcosting
  function buttonSelectSubCosting () {
    loadSelectSubCosting()
    $("#formSelectSubCosting").modal('toggle')
  }

  function buttonPilihSubCosting(selectedSubCosting) {
    $("#inputSubCosting").val(selectedSubCosting);
    $("#formSelectSubCosting").modal("hide");

  }

  function loadSelectSubCosting() {
    let _token = $("#_token").val();
    let noKira = $("#inputPerkiraan").val(); 

    if (!noKira || noKira.trim() === "") {
      alert("Pilih Cost terlebih dahulu");
      return;
    }

    $('#tabelSelectSubCosting').DataTable().destroy();

    $.ajax({
      url: "{!! url('reportaccountingcosting_loadsubcosting') !!}",
      type: "get",
      async: false,
      data: {
        _token: _token,
        NoKira: noKira  
      },
      success: function (res) {
        dataRefresh = res;
      },
    });

    let rowTable = "";
    dataRefresh.forEach((item) => {
      rowTable += `<tr>
        <td>${item.KodeSubCost}</td>
        <td>${item.NamaSubCost}</td>
        <td class="text-center">
          <button class="btn btn-primary btn-sm"
            type="button"
            onclick="buttonPilihSubCosting('${item.KodeSubCost}')">+</button>
        </td>
      </tr>`;
    });

    $("#tabel_dataSelectSubCosting").html(rowTable);
    $("#tabelSelectSubCosting").DataTable({
      lengthChange: false,
      paging: true,
    });
  }
// end

</script>

@endsection