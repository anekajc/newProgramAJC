@extends('report.masterreportGudang')
        @include('report.modalbrowsemaster')

{{-- @section('reportname')
      <h3>Report Stock Fisik Gudang</h3>
@endsection --}}


@section('header2')

<div class="w-100 bg-light shadow-sm py-3 px-4 border-bottom d-flex align-items-center justify-content-between"
     style="margin-top:-20px; margin-bottom:150px;">

  <!-- LEFT CONTROLS -->
  <div class="d-flex align-items-center" style="gap:10px;">

    <!-- MODE TOGGLE (HIDDEN) -->
    <div class="btn-group" role="group" hidden>
      <button type="button"
              class="btn btn-primary"
              id="buttonMode0"
              onclick="doReportMode(0)">
        No Bukti
      </button>
      <button type="button"
              class="btn btn-outline-primary"
              id="buttonMode1"
              onclick="doReportMode(1)">
        Kode Barang
      </button>
    </div>

    <!-- PERIODE -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle"
              data-bs-toggle="dropdown"
              title="Periode">
        <i class="fas fa-calendar-alt"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:300px;">
        <label for="inputDate1" class="mb-1">Periode</label>
        <input type="date"
               class="form-control"
               id="inputDate1"
               value="{!! date('Y-m-d') !!}">
      </div>
    </div>

    <!-- GUDANG -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle"
              data-bs-toggle="dropdown"
              title="Gudang">
        <i class="fas fa-warehouse"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:300px;">
        <label for="inputGudang" class="mb-1">Gudang</label>
        <div class="input-group">
          <input type="text"
                 id="inputGudang"
                 class="form-control"
                 placeholder="-"
                 value="-"
                 onfocus="doSetInputBrowseMaster('inputGudang', '{!! $gudang !!}')"
                 onblur="doBlurInputBrowseMaster()">
          <button class="btn btn-primary"
                  onclick="doBrowseMaster('inputGudang', '{!! $gudang !!}')">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- GRUP -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle"
              data-bs-toggle="dropdown"
              title="Grup">
        <i class="fas fa-layer-group"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:300px;">
        <label for="inputGrup" class="mb-1">Grup</label>
        <div class="input-group">
          <input type="text"
                 id="inputGrup"
                 class="form-control"
                 placeholder="-"
                 value="-"
                 onfocus="doSetInputBrowseMaster('inputGrup', '{!! $grup !!}')"
                 onblur="doBlurInputBrowseMaster()">
          <button class="btn btn-primary"
                  onclick="doBrowseMaster('inputGrup', '{!! $grup !!}')">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </div>
    </div>

  </div>

  <!-- RIGHT ACTION -->
  <div class="d-flex ms-auto">
    <button type="button"
            class="btn btn-outline-primary"
            onclick="makeTable('REPORT')"
            title="Submit">
      <i class="fas fa-check"></i>
    </button>
  </div>

</div>

@endsection

@section('additionalModal')
        <!-- start modal browse master -->
        <!-- End modal browse master -->
@endsection


@section('jsreport')
<script src="{!! URL::asset('js/ajc-browsemaster.js') !!}"></script>
<script type="text/javascript">
  g_modeReport = 0;

    $(document).ready(function() {
    console.log("Script sudah jalan. Coba klik tombol untuk tes fungsi.");
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

    // setReportMode(globalReportMode);
    // setOtorisasi(globalOtorisasi);
    // setOrderBy(globalOrderBy);
    // setAgen(globalAgen);
    // showPeriode();
    // setDefaultHeader();

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });

  $(document).ready(function(){
    $("#gButtonCustomizeTable").hide();
  });

  function setDefaultHeader() {
    gcart_header = [
      ['KodeBrg', 'Kode Brg', 1, 'varchar', 0, 0],
      ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
      ['KodeSupp', 'KodeSupp', 0, 'varchar', 0, 0],
      ['Isi3', 'Isi3', 1, 'float', 0, 0],
      ['SALDO3QNT', 'Saldo Sat3', 1, 'floatSat3', 0, 0],
      ['Isi2', 'Isi2', 1, 'float', 0, 0],
      ['SALDO2QNT', 'Saldo Sat2', 1, 'floatSat2', 0, 0],
      ['SALDO1QNT', 'Saldo Sat1', 1, 'floatSat1', 0, 0],
      ['Fisik', 'Fisik', 1, 'empty', 0, 0],
      ['Selisih (+)', 'Selisih (+)', 1, 'empty', 0, 0],
      ['Selisih (-)', 'Selisih (-)', 1, 'empty', 0, 0]
    ];

    gsum_issubtotal = 0; gsum_isgrandtotal = 0;
  }
  
  function setRowHeader(_rowHeader) {
    let _url = "{!! url('functionbrowse_doLoadGudang') !!}";
    let _kode = $("#inputGudang").val();

    $.ajax({
      url     : _url,
      type    : "get",
      async   : false,
      data    : {
        kode : _kode
      },
      success: function(res) {
        if (res && res.length > 0) {
            _rowHeader += '<tr>'
            _rowHeader += '  <th colspan="10" style="text-align: left;">' + res[0].KODEGDG + ' - ' + res[0].NAMA + '</th>';
            _rowHeader += '</tr>'; 
        }
      }
    })

    _rowHeader += doSetRowHeaderDefault(gcart_header);

    return _rowHeader;
  }

  function setRowFooter() {
    let rowFooter = "";

    rowFooter += "<tr style='text-align: center'>";
    rowFooter += '  <td colspan="10" class="cellcompact-center" style="border: 1px solid black; white-space:nowrap;">Total Item : ' + gcart_res.length + '</td>';
    rowFooter += '</tr>';

    let rowTTD = "";
    let _style = 'style="border: none !important; outline: none !important;"';
    rowTTD += '<tr><td colspan="10" ' + _style + '></td></tr>';
    rowTTD += '<tr><td colspan="10" ' + _style + '></td></tr>';
    rowTTD += '<tr>';
    rowTTD += '  <td class="cellcompact-center" colspan="2" ' + _style + '><b>Dibuat oleh,</b></td>';
    rowTTD += '  <td class="cellcompact-center" colspan="4" ' + _style + '></td>';
    rowTTD += '  <td class="cellcompact-center" colspan="3" ' + _style + '><b>Mengetahui</b></td>';
    rowTTD += '  <td class="cellcompact-center" colspan="1" ' + _style + '></td>';
    rowTTD += '</tr>';
    rowTTD += '<tr><td colspan="10" ' + _style + '></td></tr>';
    rowTTD += '<tr><td colspan="10" ' + _style + '></td></tr>';
    rowTTD += '<tr><td colspan="10" ' + _style + '></td></tr>';
    rowTTD += '<tr><td colspan="10" ' + _style + '></td></tr>';
    rowTTD += '<tr>';
    rowTTD += '  <td class="cellcompact-center" colspan="2" ' + _style + '><u><b>Ka. Gudang</b></u></td>';
    rowTTD += '  <td class="cellcompact-center" colspan="4" ' + _style + '></td>';
    rowTTD += '  <td class="cellcompact-center" colspan="3" ' + _style + '><u><b>Supervisor</b></u></td>';
    rowTTD += '  <td class="cellcompact-center" colspan="1" ' + _style + '></td>';
    rowTTD += '</tr>';

    return rowFooter + rowTTD;
  }

  function makeTable(_mode) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = "KodeSupp";
    let _date1  = $("#inputDate1").val();

    let data = {
      date1            : _date1,
      inputGudang      : $("#inputGudang").val(),
      inputGrup        : $("#inputGrup").val(),
    };

    doMakeTable(_mode, groupby, data, "LAPORAN STOK FISIK GUDANG", _date1);
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    // if (g_modeReport == modereport_nobukti) {
    //   data = ['nobukti', 'Tanggal'];
    // } else {
    //   data = ['KODEBRG', 'NAMABRG'];
    // }
    data = ['KodeBrg', 'NamaBrg'];

    return data;
  }

</script>

@endsection