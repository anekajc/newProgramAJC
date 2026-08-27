@extends('report.masterreportGudang')
@include('report.modalbrowsemaster')

{{-- @section('reportname')
      <h3>Report Stock Kartu Dan Opname</h3>
@endsection --}}


@section('input')

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
      <div class="dropdown-menu p-3" style="min-width:350px;">
        <label for="inputDate1" class="mb-1">Periode</label>
        <input type="date"
               class="form-control mb-2"
               id="inputDate1"
               value="{!! date('Y-m-d') !!}">

        <label for="inputDate2" class="mb-1">s/d</label>
        <input type="date"
               class="form-control"
               id="inputDate2"
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

  function setDefaultHeader() {
    gcart_header = [
      ['KODEBRG', 'Kode', 1, 'varchar', 0, 0],
      ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
      ['PartNumber', 'Part Number', 1, 'varchar', 0, 0],
      ['NAMAMERK', 'Merk', 1, 'varchar', 0, 0],
      ['SaldoQnt', 'Saldo', 1, 'float', 1, 2],
      ['TOTALSALDO', 'Total Saldo', 1, 'float', 0, 2],
      ['QNTOPNAME', 'Opname', 1, 'float', 0, 2],
      ['SALDOOPNAME', 'Saldo Opname', 1, 'float', 0, 2],
      ['QNTSELISIH', 'Selisih', 1, 'float', 0, 2],
      ['SALDOSELISIH', 'Saldo Selisih', 1, 'float', 0, 2],
      ['SAT1', 'Sat', 1, 'varchar', 0, 0]
    ];

    gsum_issubtotal = 0; gsum_isgrandtotal = 1;
  }

  function makeTable(_mode) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = "KODEBRG";
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();

    let data = {
      date1            : _date1,
      date2            : _date2,
      inputGudang      : $("#inputGudang").val(),
    };

    doMakeTable(_mode, groupby, data, "LAPORAN KARTU OPNAME", _date1, _date2);

    doRenameGrandTotal("Total Item : " + gcart_res.length, galign_left);
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
    data = ['KODEBRG', 'NAMABRG'];

    return data;
  }

</script>

@endsection