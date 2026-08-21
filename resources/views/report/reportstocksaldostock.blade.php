@extends('report.masterreportGudang')
@include('report.modalbrowsemaster')

{{-- @section('reportname')
      <h3>Report Saldo Stock</h3>
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

    <!-- NO SATUAN -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle"
              data-bs-toggle="dropdown"
              title="No Satuan">
        <i class="fas fa-sort-numeric-up"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:200px;">
        <label for="inputIsi" class="mb-1">No Satuan</label>
        <input type="number"
               id="inputIsi"
               class="form-control"
               value="1">
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

    <!-- KATEGORI -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle"
              data-bs-toggle="dropdown"
              title="Kategori">
        <i class="fas fa-tags"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:300px;">
        <label for="inputKategori" class="mb-1">Kategori</label>
        <div class="input-group">
          <input type="text"
                 id="inputKategori"
                 class="form-control"
                 placeholder="-"
                 value="-"
                 onfocus="doSetInputBrowseMaster('inputKategori', '{!! $kategori !!}')"
                 onblur="doBlurInputBrowseMaster()">
          <button class="btn btn-primary"
                  onclick="doBrowseMaster('inputKategori', '{!! $kategori !!}')">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- SUB KATEGORI -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle"
              data-bs-toggle="dropdown"
              title="Sub Kategori">
        <i class="fas fa-sitemap"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:300px;">
        <label for="inputSubKategori" class="mb-1">Sub Kategori</label>
        <div class="input-group">
          <input type="text"
                 id="inputSubKategori"
                 class="form-control"
                 placeholder="-"
                 value="-"
                 onfocus="doSetInputBrowseMaster('inputSubKategori', '{!! $subkategori !!}')"
                 onblur="doBlurInputBrowseMaster()">
          <button class="btn btn-primary"
                  onclick="doBrowseMaster('inputSubKategori', '{!! $subkategori !!}')">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- MERK -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle"
              data-bs-toggle="dropdown"
              title="Merk">
        <i class="fas fa-copyright"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:300px;">
        <label for="inputMerk" class="mb-1">Merk</label>
        <div class="input-group">
          <input type="text"
                 id="inputMerk"
                 class="form-control"
                 placeholder="-"
                 value="-"
                 onfocus="doSetInputBrowseMaster('inputMerk', '{!! $merk !!}')"
                 onblur="doBlurInputBrowseMaster()">
          <button class="btn btn-primary"
                  onclick="doBrowseMaster('inputMerk', '{!! $merk !!}')">
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
<script src="{!! URL::asset('public/js/ajc-browsemaster.js') !!}"></script>
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
      ['KodeBrg', 'Kode', 1, 'varchar', 0, 0],
      ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
      ['PartNumber', 'Part Number', 1, 'varchar', 0, 0],
      ['KodeMerk', 'Kode Merk', 1, 'varchar', 0, 0],
      ['NAMAMERK', 'Merk', 1, 'varchar', 0, 0],
      ['NamaGdg', 'Gudang', 1, 'varchar', 0, 0],
      ['SALDOQNT', 'Saldo', 1, 'float', 1, 2],
      ['Sat1', 'Sat', 1, 'varchar', 0, 0]
    ];

    gsum_issubtotal = 0; gsum_isgrandtotal = 1;
  }
  
  function getRowFooter(_col) {
    let _sum = gcart_res.reduce((sum, item) => sum + currencyNormalizer(item[_col]), 0);
    return '  <td class="cellcompact-right" style="border: 1px solid black; white-space:nowrap;">' + format_number(_sum, 2) + '</td>';
  }

  function makeTable(_mode) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = "KodeBrg";
    let _date1  = $("#inputDate1").val();

    let data = {
      date1            : _date1,
      inputGudang      : $("#inputGudang").val(),
      inputIsi         : $("#inputIsi").val(),
      inputGrup        : $("#inputGrup").val(),
      inputKategori    : $("#inputKategori").val(),
      inputSubKategori : $("#inputSubKategori").val(),
      inputMerk        : $("#inputMerk").val(),
    };

    doMakeTable(_mode, groupby, data, "LAPORAN STOK AKHIR", _date1);

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
    data = ['KodeBrg', 'NamaBrg'];

    return data;
  }

</script>

@endsection