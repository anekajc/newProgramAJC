@extends('report.masterreport4')

<!-- Warna centang -->
  <style>
    .checkmark-red {
      color: red !important;
      font-weight: bold;
      margin-left: 6px;
    }
  </style>
<!-- Warna centang -->

@include('report.modalAccountingJurnal')

@section('header2')
  <div class="w-100 bg-light shadow-sm py-3 px-4 border-bottom d-flex align-items-center justify-content-between" style="margin-top:-20px; margin-bottom:150px;">
    <!-- Kiri: ikon -->
    <div class="d-flex" style="gap: 10px;">
      <div class="dropdown" hidden>
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnPeriode" data-bs-toggle="dropdown" aria-expanded="false" title="Periode">
          <i class="fas fa-calendar-alt"></i>
        </button>
        <div class="dropdown-menu p-3" style="min-width: 350px;">
          <input type="date" class="form-control mb-2" id="inputDate1" value="{!! date('Y-m-d') !!}">
          <label for="inputDate2" class="mb-0">s/d</label>
          <input type="date" class="form-control mt-1" id="inputDate2" value="{!! date('Y-m-d') !!}">
          {{-- <button class="btn btn-primary btn-sm mt-2 w-100" onclick="showPeriode()">Terapkan</button> --}}
        </div>
      </div> 

      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="btnPeriode" data-bs-toggle="dropdown" aria-expanded="false" title="Periode">
          <i class="fas fa-calendar-alt"></i>
        </button>
        <div class="dropdown-menu p-3" style="min-width: 350px;">
          <input type="text" class="form-control mb-2" id="inputBulan" onblur ="changeBulan()">
          <label for="inputDate2" class="mb-0">Bulan/Tahun</label>
          <input type="text" class="form-control mt-1" id="inputTahun" onblur ="changeTahun()">
          {{-- <button class="btn btn-primary btn-sm mt-2 w-100" onclick="showPeriode()">Terapkan</button> --}}
        </div>
      </div> 

      <div class="dropdown" hidden>
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputReportMode" data-bs-toggle="dropdown" aria-expanded="false" title="Report Mode" style="cursor: pointer;">
          <i class="fas fa-book"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownReportMode" aria-labelledby="inputReportMode">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setReportMode('1')">Rekap</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setReportMode('0')">Detail</a></li>
        </ul>
      </div>

      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputDataPilih" data-bs-toggle="dropdown" aria-expanded="false" title="Order By">
          <i class="fa-solid fa-filter" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputDataPilih" style="min-width: 300px; padding: 10px;">
          <li onclick="event.stopPropagation();">
            <!-- Your filter form here -->

            <div class="row text-center">
              <div class="col-4">
                <label for="inputlokasi">Divisi</label>
              </div>
              
              <div class="col-8 input-group">
                <input type="text" class="form-control" id="inputDivisi" placeholder="Divisi" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelect('selectDivisi')">+</button>
                  </div>
              </div>

            </div>


          </li>
        </ul>
      </div>
      
    </div>

    <!-- Kanan: tombol aksi menempel ke ujung kanan layar -->
    <div class="d-flex ms-auto" style="gap: 8px;">
      {{-- <button type="button" class="btn btn-outline-primary" onclick="doShowFormFilterData()" title="Filter Data">
        <i class="fas fa-magnifying-glass"></i>
      </button>
      <button type="button" class="btn btn-outline-primary" onclick="doShowFormCustomizeTable()" title="Customize Table">
        <i class="fas fa-cog"></i>
      </button> --}}
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
  let globalReportMode = "0"; // default: Detail
  let globalOrderBy = "N"; // default: Detail
  let defaultBulan = new Date().getMonth() + 1;  // +1 because getMonth() returns 0-11
  let defaultTahun = new Date().getFullYear();

  var jenisreport = 0; // ini untuk detail dan rekap
  
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

    setReportMode(globalReportMode);
    showPeriode();
    setDefaultHeader();

    document.getElementById('inputBulan').value = defaultBulan;
    document.getElementById('inputTahun').value = defaultTahun;

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });

  function changeBulan(){
    defaultBulan = document.getElementById('inputBulan').value
  }

  function changeTahun(){
    defaultTahun = document.getElementById('inputTahun').value
  }

  // periode
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
    // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  }

  function setReportMode(val) {
    globalReportMode = val;
    jenisreport = Number(val);   // 0 = Detail, 1 = Rekap
    DetOrRekap = Number(val);    // samakan dengan variabel yang ada di setModeReport

    console.log(val)
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
  
  var modereport_detailnobukti = 0, modereport_rekapnobukti = 1;
  g_modeReport = modereport_detailnobukti;

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailnobukti) {
      gcart_header = [
        ['keterangan', 'Keterangan', 1, 'varchar', 0, 0, [1, 1, 2], false], // has data
        ['', 'Bulan Lalu', 1, 'group', 0, 0, [1, 2, 1], true], // header only
        ['totalA', 'Rp.', 1, 'float', 1, 2, [2, 1, 1], false], // has data
        ['P1', '%', 1, 'float', 1, 2, [2, 1, 1], false], // has data
        ['', 'Bulan Ini', 1, 'group', 0, 0, [1, 2, 1], true], // header only
        ['totalB', 'Rp.', 1, 'float', 1, 2, [2, 1, 1], false], // has data
        ['P2', '%', 1, 'float', 1, 2, [2, 1, 1], false], // has data
        ['', 's/d Bulan Ini', 1, 'group', 0, 0, [1, 2, 1], true], // header only
        ['totalC', 'Rp.', 1, 'float', 1, 2, [2, 1, 1], false], // has data
        ['P3', '%', 1, 'float', 1, 2, [2, 1, 1], false] // has data
      ];

      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if (g_modeReport == modereport_rekapnobukti){
      gcart_header = [
        ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NamaSls', 'Sales', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Customer', 1, 'varchar', 0, 0],
        ['NOPOCUstomer', 'No. PO. Customer', 1, 'varchar', 0, 0],
        ['NoSo', 'No. SO', 1, 'varchar', 0, 0],
        ['TanggalSO', 'Tanggal SO', 1, 'date', 0, 0],
        ['NDPPRPZX', 'Total', 1, 'float', 1, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    }

  }

  function makeTable (_mode) {

    let groupby = '';
    let _date1    = $("#inputDate1").val();
    let _date2    = $("#inputDate2").val();
    let inputBulan    = defaultBulan;
    let inputTahun    = defaultTahun;
    let divisi    = $("#inputDivisi").val();
    let input_order = globalOrderBy;

    console.log(defaultBulan, defaultTahun)

      if (input_order == "N") {
        groupby = 'NoBukti';
      }

    setDefaultHeader();
    // if (typeof doSetHeader === 'function') {
    //   doSetHeader(g_modeReport);
    // }

    triggerSP(inputBulan, inputTahun, divisi);

    let data = {
      date1    : _date1,
      date2    : _date2,
      inputBulan  : inputBulan,
      inputTahun  : inputTahun,
      divisi   : divisi,
      totalA : tempTotalA,
      totalB : tempTotalB,
      totalC : tempTotalC
    };

    doMakeTable(_mode, groupby, data, "REPORT HPP", _date1);
  }

  let tempTotalA = 0;
  let tempTotalB = 0;
  let tempTotalC = 0;

  function triggerSP(bulan, tahun, divisi){
    $.ajax({
    url: "{!! url('laporanaccountinglabarugi_triggerSp') !!}",
    type: "get",
    async: false,
    data: {
      bulan,
      tahun,
      divisi
    },
    success: function(res) {
      tempTotalA = (res[0].totalA || 0)
      tempTotalB = (res[0].totalB || 0)
      tempTotalC = (res[0].totalC || 0)

    }})
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if ($("#inputOrder").val() == "N")
    {
      data = ['NoBukti', 'TanggalSO'];
    }

    return data;
  }

  function setModeReport () {
    if (globalOrderBy == "N") {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailnobukti;
      } else {
        g_modeReport = modereport_rekapnobukti;
      }
    }

    doSetHeader(g_modeReport);
    doShowCustomize();
  }

</script>

@endsection
