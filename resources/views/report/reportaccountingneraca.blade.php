@extends('report.masterreportNeraca')

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

      <div class="dropdown" hidden>
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

      <div class="dropdown" hidden>
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputDataPilih" data-bs-toggle="dropdown" aria-expanded="false" title="Perkiraan">
          <i class="fa-solid fa-filter" style="cursor: pointer;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownOrder" aria-labelledby="inputDataPilih" style="min-width: 300px; padding: 10px;">
          <li onclick="event.stopPropagation();">
            <!-- Your filter form here -->
            <div class="row text-center">
              <div class="col-4">
                <label for="inputPerkiraan">Perkiraan</label>
              </div>
              <div class="col-8 input-group">
                <input type="text" class="form-control" id="inputPerkiraan" placeholder="Group" value='-'>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" style='height:31px;' onclick="buttonSelectPerkiraan()">+</button>
                  </div>
              </div>
            </div>
          </li>
        </ul>
      </div>

      <div class="dropdown" hidden>
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputReportMode" data-bs-toggle="dropdown" aria-expanded="false" title="Report Mode" style="cursor: pointer;">
          <i class="fas fa-book"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownReportMode" aria-labelledby="inputReportMode">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setReportMode('1')">Rp</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setReportMode('2')">Valas</a></li>
        </ul>
      </div>

      <div class="dropdown" hidden>
        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputTolakan" data-bs-toggle="dropdown" aria-expanded="false" title="Tolakan" style="cursor: pointer;">
          <i class="fas fa-key"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownTolakan" aria-labelledby="inputTolakan">
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="2" onclick="setTolakan('2')">Semua</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="1" onclick="setTolakan('1')">Non Tolakan</a></li>
          <li><a class="dropdown-item" style="cursor: pointer;" data-value="0" onclick="setTolakan('0')">Tolakan</a></li>
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
          <h5 class="modal-title" id="exampleModalLabel">Select Perkiraan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table id="tabelSelectPerkiraan" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Perkiraan</th>
                <th scope="col">Keterangan</th>
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
@endsection

@section('jsreport')
<script type="text/javascript">

  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";
  let globalTolakan = "2"; // default: Semua
  let globalReportMode = "1"; // default: Rp
  let g_reportTitle = "";
  let g_date1 = "";
  let g_date2 = "";
  let g_cellcount = 0;
  let g_inputPerkiraan = "";
  let g_inputKeterangan = "";

  let defaultBulan = new Date().getMonth() + 1;  // +1 because getMonth() returns 0-11
  let defaultTahun = new Date().getFullYear();

  $(document).ready(function () {
    $("#btnCustomizeTable").on("click", function () {
      if (typeof doShowFormCustomizeTable === "function") doShowFormCustomizeTable();
      else alert(" Fungsi doShowFormCustomizeTable belum tersedia.");
    });
    
    $("#btnSubmitReport").on("click", function () {
      makeTable("REPORT");
    });

    setReportMode(globalReportMode);
    setTolakan(globalTolakan);
    showPeriode();
    
    document.getElementById('inputBulan').value = defaultBulan;
    document.getElementById('inputTahun').value = defaultTahun;

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });

  function buttonPilihPerkiraan(selectedPerkiraan) {
    $("#inputPerkiraan").val(selectedPerkiraan);
    $("#formSelectPerkiraan").modal("hide");
  }

  // periode
  function showPeriode() {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();
    // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  }

  function changeBulan(){
  defaultBulan = document.getElementById('inputBulan').value
  }

  function changeTahun(){
    defaultTahun = document.getElementById('inputTahun').value
  }

  // tolakan
  function setTolakan(val) {
    globalTolakan = val;
    let text = (val == '0') ? 'Semua' : (val == '1') ? 'Tolakan' : 'Non Tolakan';
    // alertify.success(`Tolakan: ${text}`);

    // hapus semua centang
    $('#dropdownTolakan .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ?', '').trim(); 
      $(this).text(itemText);
    });

    // tambah centang di item yg di pilih
    $(`#dropdownTolakan .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
    });
  }

  // mode report
  function setReportMode(val) {
    globalReportMode = val;
    jenisreport = Number(val);   // 1 = Rp, 2 = Valas
    DetOrRekap = Number(val);    

    $('#dropdownReportMode .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ?', '').trim();
      $(this).text(itemText);
    });

    $(`#dropdownReportMode .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
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
        ['keterangan', 'Ket', 1, 'varchar', 0, 0],
        ['jumlah1', 'No Bukti', 1, 'float', 1, 2],
        ['jumlah2', 'Uraian', 1, 'float', 1, 2],
        ['keteranganPasiva', 'Perkiraan', 1, 'varchar', 0, 0],
        ['jumlah1Pasiva', 'Uraian Perkiraan', 1, 'float', 1, 2],
        ['jumlah2Pasiva', 'Penerimaan', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    } else {
      gcart_header = [
        ['tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['nobukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['keterangan', 'Uraian', 1, 'varchar', 0, 0],
        ['Perkiraan', 'Perkiraan', 1, 'varchar', 0, 0],
        ['NamaPerkiraan', 'Uraian Perkiraan', 1, 'varchar', 0, 0],
        ['DebetRp', 'Penerimaan', 1, 'float', 1, 2],
        ['DebetD', 'Penerimaan', 1, 'float', 1, 2],
        ['kreditRp', 'Pengeluaran', 1, 'float', 1, 2],
        ['kreditD', 'Pengeluaran', 1, 'float', 1, 2],
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    }
  }

  function doMakeTableSaldoAwal (_data, callback) {
    $.ajax({
        url: "{{ url('/laporanaccountingneraca_saldoawal') }}",
        type: "get",
        data: _data,
        success: function(res) {
            window.resSaldoAwal = res.res2?.[0] || {};
            console.log("Saldo Awal Ready", window.resSaldoAwal);

            if (typeof callback === "function") callback();
        }
    });
  }


  function getRowFooter1(_col) {
    let _sum = gcart_res.reduce((sum, item) => sum + currencyNormalizer(item[_col]), 0);
    let _decimal = (gcart_header.find(row => row[0] === _col) || [])[5];

    return '  <td class="cellcompact-right" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">' + format_number(_sum, _decimal) + '</td>';
  }
  
  function getRowFooter2(_col, _colspanRow2) {
    let _sum = gcart_res.filter(item => currencyNormalizer(item[_col]) !== 0).length;
    let _str = '  <td colspan="' + _colspanRow2 + '" class="cellcompact-right" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">' + _sum + '</td>'

    return { _sum, _str };
  }

  function setRowFooter(res2 = {}, res1 = [], res3 = []) {
    let isRupiah = String(globalReportMode) === "1";

    if (isRupiah) {
        return setRowFooterRp(res2, res1, res3);   // MODE RP - pass res3 (SP2 data)
    } else {
        return setRowFooterValas(res2, res1); // MODE VALAS 
    }
  }

  function setRowFooterRp(res2 = {}, res1 = [], res3 = []) {

    if (!res2 || Object.keys(res2).length === 0) {
        res2 = window.resSaldoAwal || {};
    }

    let r2 = Array.isArray(res2) ? (res2[0] || {}) : res2;

    // ========== AKTIVA (SP1) - LEFT SIDE ==========
    let totalAktivaBulanIni = 0;
    let totalAktivaBulanLalu = 0;

    res1.forEach(r => {
        totalAktivaBulanIni += Number(r.jumlah1 ?? 0);
        totalAktivaBulanLalu += Number(r.jumlah2 ?? 0);
    });

    // ========== PASIVA (SP2) - RIGHT SIDE ==========
    // NOTE: SP2 uses different column names!
    let totalPasivaBulanIni = 0;
    let totalPasivaBulanLalu = 0;

    res3.forEach(r => {
        totalPasivaBulanIni += Number(r.jumlah1Pasiva ?? 0);
        totalPasivaBulanLalu += Number(r.jumlah2Pasiva ?? 0);
    });

    // Format angka
    let f = (v) => Number(v).toLocaleString("id-ID", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    let spacing = `
        <tr>
            <td colspan="6" style="height:8px;"></td>
        </tr>
    `;

    let footerRow = `
        <tr style="font-weight: bold;">
            <td style="border:1px solid black; padding:4px;">JUMLAH ASSET</td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(totalAktivaBulanIni)}</td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(totalAktivaBulanLalu)}</td>
            
            <td style="border:1px solid black; padding:4px;">JUMLAH KEWAJIBAN DAN EKUITAS</td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(totalPasivaBulanIni)}</td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(totalPasivaBulanLalu)}</td>
        </tr>
    `;

    return spacing + footerRow;
}

  function setRowFooterValas(res2 = {}, res1 = []) {

    if (!res2 || Object.keys(res2).length === 0) {
        res2 = window.resSaldoAwal || {};
    }

    let r2 = Array.isArray(res2) ? (res2[0] || {}) : res2;

    let SaldoAwal   = Number(r2.SaldoAwal   || 0);
    let SaldoAwalD  = Number(r2.SaldoAwalD  || 0);

    let DebetRp  = 0;
    let DebetD  = 0;
    let kreditRp = 0;
    let kreditD = 0;

    res1.forEach(row => {
        DebetRp  += Number(row.DebetRp  || 0);
        DebetD   += Number(row.DebetD   || 0);
        kreditRp += Number(row.kreditRp || 0);
        kreditD  += Number(row.kreditD  || 0);
    });

    // hitungan saldo akhir
    let saldoAkhir  = (DebetRp  + SaldoAwal) - kreditRp;
    let saldoAkhirD = (DebetD + SaldoAwalD ) - kreditD;

    let f = (v) => Number(v).toLocaleString("id-ID", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    let spacing = `<tr><td colspan="15" style="height:8px;"></td></tr>`;

    let block = `
        <tr>
            <td style="border:1px solid black; padding:4px;"></td>
            <td style="border:1px solid black; padding:4px;"></td>
            <td style="border:1px solid black; text-align:right; padding:4px;"></td>

            <td colspan="2" style="border:1px solid black; padding:4px;">Sub. Jumlah</td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(DebetRp)}</td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(DebetD)}</td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(kreditRp)}</td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(kreditD)}</td>
        </tr>

        <tr>
            <td style="border:1px solid black; padding:4px;"></td>
            <td style="border:1px solid black; padding:4px;"></td>
            <td style="border:1px solid black; text-align:right; padding:4px;"></td>

            <td colspan="2" style="border:1px solid black; padding:4px;">Saldo Awal</td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(SaldoAwal)}</td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(SaldoAwalD)}</td>
            <td style="border:1px solid black; text-align:right; padding:4px;"></td>
            <td style="border:1px solid black;"></td>
        </tr>

        <tr>
            <td style="border:1px solid black; padding:4px;"></td>
            <td style="border:1px solid black; padding:4px;"></td>
            <td style="border:1px solid black; text-align:right; padding:4px;"></td>

            <td colspan="2" style="border:1px solid black; padding:4px;">Saldo Akhir</td>
            <td style="border:1px solid black; text-align:right; padding:4px;"></td>
            <td style="border:1px solid black; text-align:right; padding:4px;"></td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(saldoAkhir)}</td>
            <td style="border:1px solid black; text-align:right; padding:4px;">${f(saldoAkhirD)}</td>
        </tr>
    `;

    let signature = `
        <tr>
            <td colspan="3" style="border:1px solid black; height:90px; text-align:center;">Pimpinan</td>
            <td colspan="2" style="border:1px solid black; text-align:center;">Kontrol</td>
            <td colspan="2" style="border:1px solid black; text-align:center;">Kasir</td>
        </tr>
    `;

    return spacing + block + signature;
  }

  function setRowHeader() {
    let rowHeader = "";
    rowHeader += '<tr>';
    rowHeader += '<th colspan="9" style="text-align: left; font-weight: bold">' +
      '{!! $akses["program"] !!}<br/>' + g_reportTitle +
    '</th>';
    rowHeader += '</tr>';
    
    if (g_date1 && g_date1 !== 'undefined' && g_date1 !== '') {
      rowHeader += '<tr>';
      rowHeader += '  <th colspan="9" style="text-align: left; font-weight: bold;">PERIODE: ' +
        format_date(g_date1, true) +
        ((g_date2 == null || g_date2 === '' || g_date2 === 'undefined')
            ? ''
            : ' S.D ' + format_date(g_date2, true)) +
      '</th>';
      rowHeader += '</tr>';}

    rowHeader += '<tr>';
    rowHeader +=
      '  <th colspan="9" style="text-align: left; font-weight: bold;">Dicetak Oleh : {!! $akses["user"] !!} // Tanggal : '
      + getDateIndo() +
      ' // Jam : ' + getTimeNow() +
      '</th>';
    rowHeader += '</tr>';
    rowHeader += '<tr><th colspan="9"></th></tr>';

    if (g_modeReport == modereport_detail) {
      rowHeader = setRowHeaderQtyOrRp(rowHeader);
    } else {
      rowHeader = setRowHeaderQtyRp(rowHeader);
    }

    return rowHeader;
  }

  function setRowHeaderQtyOrRp(_rowHeader) {

    _rowHeader += '<tr style="height: 45px; padding: 20px; " class="text-center bg-dark text-light">';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Uraian</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Bulan Ini</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Bulan Lalu</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Uraian</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Bulan Ini</th>';
    _rowHeader += '  <th scope="col" style="border: 1px solid black; white-space:nowrap;">Bulan Lalu</th>';
    _rowHeader += '</tr>';

    return _rowHeader;
  }

  function setRowHeaderQtyRp(_rowHeader) {
    let _thopen = "", _thclose = "</th>";

    // FIRST ROW
    _rowHeader += '<tr style="height: 45px; padding: 20px;" class="text-center bg-dark text-light">';
    _thopen = '<th rowspan="2" scope="col" style="border: 1px solid black; white-space:nowrap; vertical-align: middle;">';
    _rowHeader += _thopen + 'Tanggal' + _thclose;
    _rowHeader += _thopen + 'No Bukti' + _thclose;
    _rowHeader += _thopen + 'Uraian' + _thclose;
    _rowHeader += _thopen + 'Perkiraan' + _thclose;
    _rowHeader += _thopen + 'Uraian Perkiraan' + _thclose;

    _rowHeader += '<th colspan="2" rowspan="1" style="border: 1px solid black; white-space:nowrap; vertical-align: middle;">Penerimaan</th>';
    _rowHeader += '<th colspan="2" rowspan="1" style="border: 1px solid black; white-space:nowrap; vertical-align: middle;">Pengeluaran</th>';
    _rowHeader += '</tr>';

    // SECOND ROW
    _rowHeader += '<tr class="text-center bg-dark text-light">';

    let _qtyrp = '<th scope="col" style="border: 1px solid black; white-space:nowrap; width:80px;">Rp.</th>';
    _qtyrp += '<th scope="col" style="border: 1px solid black; white-space:nowrap; width:80px;">$</th>';
    _rowHeader += _qtyrp.repeat(1 + 1);

    _rowHeader += '</tr>';

    return _rowHeader;
  }
  
  function makeTable (_mode) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = (DetOrRekap === 1) ? "nobukti" : "perkiraan";    
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();
    let _inputPerkiraan = $("#inputPerkiraan").val();
    let inputBulan    = defaultBulan;
    let inputTahun    = defaultTahun;
    let divisi    = $("#inputDivisi").val();
    
    if (!_inputPerkiraan){
      _inputPerkiraan = '-'
    }

    g_reportTitle = "REPORT ACCOUNTING NERACA";
    g_date1 = _date1;
    g_date2 = _date2;
    g_inputPerkiraan = _inputPerkiraan;

    let data = {
      date1    : _date1,
      date2    : _date2,
      inputBulan  : inputBulan,
      inputTahun  : inputTahun,
      divisi   : divisi
    };
    console.log(data);

    let dataSP2 = {
      date1    : _date1,
      date2    : _date2,
      inputBulan  : inputBulan,
      inputTahun  : inputTahun,
      divisi   : divisi
    };

    doMakeTableSaldoAwal(dataSP2, function() {
      doMakeTable(_mode, groupby, data, _date1, _date2, _inputPerkiraan);
    });
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
      url: "{!! url('reportaccountingkasharian_loadperkiraan') !!}",
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
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        <td class="text-center">
          <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilihPerkiraan('${item.Perkiraan}')">+</button>
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

</script>

@endsection