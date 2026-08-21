@extends('report.masterreportGudang')

@include('report.modalbrowsemaster')
{{-- @section('reportname')
      <h3>Report Mutasi Stock Per Merk (Qty +Rp)</h3>
@endsection --}}


@section('header2')
<div class="w-100 bg-light shadow-sm py-3 px-4 border-bottom d-flex align-items-center justify-content-between"
     style="margin-top:-20px; margin-bottom:150px;">

  <!-- LEFT SIDE -->
  <div class="d-flex align-items-center" style="gap:10px;">

    <!-- PERIODE -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" title="Periode">
        <i class="fas fa-calendar-alt"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:250px;">
        <label for="inputDate1" class="mb-1">Periode</label>
        <input type="month"
               class="form-control"
               id="inputDate1"
               value="{!! date('Y-m') !!}">
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

    <!-- GUDANG -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" title="Gudang">
        <i class="fas fa-warehouse"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:300px;">
        <label for="inputGudang" class="mb-1">Gudang</label>
        <div class="input-group">
          <input type="text"
                 id="inputGudang"
                 class="form-control"
                 placeholder="-"
                 onfocus="doSetInputBrowseMaster('inputGudang', '{!! $gudang !!}')"
                 onblur="doBlurInputBrowseMaster()"
                 value="-">
          <button class="btn btn-primary"
                  onclick="doBrowseMaster('inputGudang', '{!! $gudang !!}')">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- MERK -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" title="Merk">
        <i class="fas fa-tags"></i>
      </button>
      <div class="dropdown-menu p-3" style="min-width:300px;">
        <label for="inputMerk" class="mb-1">Merk</label>
        <div class="input-group">
          <input type="text"
                 id="inputMerk"
                 class="form-control"
                 placeholder="-"
                 onfocus="doSetInputBrowseMaster('inputMerk', '{!! $merk !!}')"
                 onblur="doBlurInputBrowseMaster()"
                 value="-">
          <button class="btn btn-primary"
                  onclick="doBrowseMaster('inputMerk', '{!! $merk !!}')">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </div>
    </div>

  </div>
  
      <div class="d-flex ms-auto" style="gap: 8px;">
      <button type="button" class="btn btn-outline-primary" onclick="doShowFormFilterData()" title="Filter Data">
        <i class="fas fa-magnifying-glass"></i>
      </button>
      <button type="button" class="btn btn-outline-primary" onclick="makeTable('REPORT')" title="Submit">
        <i class="fas fa-check"></i>
      </button>
    </div>

</div>
@endsection

@section('jsreport')
<script src="{!! URL::asset('public/js/ajc-browsemaster.js') !!}"></script>
<script type="text/javascript">
  var modereport_detail = 0, modereport_rekap = 1;
  g_modeReport = modereport_detail;

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

  var colspanRow = 4 + 2 + 16 + 16 + 2   +1;

  $(document).ready(function(){
    $("#gButtonCustomizeTable").hide();
  });

  function setDefaultHeader() {
    if (g_modeReport == modereport_detail) {
      gcart_header = [
          ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
          ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
          ['partNumber', 'Part Number', 1, 'varchar', 0, 0],
          ['pMERK', 'Kode Merk', 0, 'varchar', 0, 0],
          ['pNAMAMERK', 'Nama Merk', 0, 'varchar', 0, 0],
          ['NAMAMERK', 'Merk', 1, 'varchar', 0, 0],
          ['QntAwal', 'So. Awal', 1, 'float', 1, 2],
          ['HRGAWAL', 'So. Awal', 1, 'float', 1, 0],
          ['QNTPBL', 'Pembelian', 1, 'float', 1, 2],
          ['HRGPBL', 'Pembelian', 1, 'float', 1, 0],
          ['QNTRPJ', 'Retur Jual', 1, 'float', 1, 2],
          ['HRGRPJ', 'Retur Jual', 1, 'float', 1, 0],
          ['QNTADI', 'Kor. Msk', 1, 'float', 1, 2],
          ['HRGADI', 'Kor. Msk', 1, 'float', 1, 0],
          ['QNTTRI', 'Trans. Msk', 1, 'float', 1, 2],
          ['HRGTRI', 'Trans. Msk', 1, 'float', 1, 0],
          ['QNTRPK', 'R. Pemakaian', 1, 'float', 1, 2],
          ['HRGRPK', 'R. Pemakaian', 1, 'float', 1, 0],
          ['QNTUKI', 'Ubah Kemasan In', 1, 'float', 1, 2],
          ['HRGUKI', 'Ubah Kemasan In', 1, 'float', 1, 0],
          ['qntrspb', 'Terima dr R.Sjln', 1, 'float', 1, 2],
          ['hrgrspb', 'Terima dr R.Sjln', 1, 'float', 1, 0],
          ['QntHPrd', 'Gd TC dr SJ', 1, 'float', 1, 2],
          ['HRGHPrd', 'Gd TC dr SJ', 1, 'float', 1, 0],
          ['QNTPNJ', 'S.Jalan', 1, 'float', 1, 2],
          ['HRGPNJ', 'S.Jalan', 1, 'float', 1, 0],
          ['qntrgtc', 'Retur Sjln dr GTC', 1, 'float', 1, 2],
          ['hrgrgtc', 'Retur Sjln dr GTC', 1, 'float', 1, 0],
          ['QNTPRJ', 'HPP', 1, 'float', 1, 2],
          ['HRGPRJ', 'HPP', 1, 'float', 1, 0],
          ['QNTRBP', 'Retur Beli', 1, 'float', 1, 2],
          ['HRGRBP', 'Retur  Beli', 1, 'float', 1, 0],
          ['QNTADO', 'Kor. Klr', 1, 'float', 1, 2],
          ['HRGADO', 'Kor. Klr', 1, 'float', 1, 0],
          ['QNTTRO', 'Trans. Klr', 1, 'float', 1, 2],
          ['HRGTRO', 'Trans. Klr', 1, 'float', 1, 0],
          ['QNTUKO', 'Ubah Kemasan Out', 1, 'float', 1, 2],
          ['HRGUKO', 'Ubah Kemasan Out', 1, 'float', 1, 0],
          ['QNTPMK', 'Pemakaian', 1, 'float', 1, 2],
          ['HRGPMK', 'Pemakaian', 1, 'float', 1, 0],
          ['SALDOQNT', 'So. Akhir', 1, 'float', 1, 2],
          ['SALDORP', 'So. Akhir', 1, 'float', 1, 0]
      ];
      
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    } else {
      gcart_header = [
          ['pMERK', 'Kode', 1, 'varchar', 0, 0],
          ['pNAMAMERK', 'Merk', 1, 'varchar', 0, 0],
          ['KodeGDG', 'Gdg', 1, 'varchar', 0, 0],
          ['QntAwal', 'So. Awal', 1, 'float', 1, 2],
          ['HRGAWAL', 'So. Awal', 1, 'float', 1, 0],
          ['QNTPBL', 'Pembelian', 1, 'float', 1, 2],
          ['HRGPBL', 'Pembelian', 1, 'float', 1, 0],
          ['QNTRPJ', 'Retur Jual', 1, 'float', 1, 2],
          ['HRGRPJ', 'Retur Jual', 1, 'float', 1, 0],
          ['QNTADI', 'Kor. Msk', 1, 'float', 1, 2],
          ['HRGADI', 'Kor. Msk', 1, 'float', 1, 0],
          ['QNTTRI', 'Trans. Msk', 1, 'float', 1, 2],
          ['HRGTRI', 'Trans. Msk', 1, 'float', 1, 0],
          ['QNTRPK', 'R. Pemakaian', 1, 'float', 1, 2],
          ['HRGRPK', 'R. Pemakaian', 1, 'float', 1, 0],
          ['QNTUKI', 'Ubah Kemasan In', 1, 'float', 1, 2],
          ['HRGUKI', 'Ubah Kemasan In', 1, 'float', 1, 0],
          ['qntrspb', 'Terima dr R.Sjln', 1, 'float', 1, 2],
          ['hrgrspb', 'Terima dr R.Sjln', 1, 'float', 1, 0],
          ['QntHPrd', 'Gd TC dr SJ', 1, 'float', 1, 2],
          ['HRGHPrd', 'Gd TC dr SJ', 1, 'float', 1, 0],
          ['QNTPNJ', 'S.Jalan', 1, 'float', 1, 2],
          ['HRGPNJ', 'S.Jalan', 1, 'float', 1, 0],
          ['qntrgtc', 'Retur Sjln dr GTC', 1, 'float', 1, 2],
          ['hrgrgtc', 'Retur Sjln dr GTC', 1, 'float', 1, 0],
          ['QNTPRJ', 'HPP', 1, 'float', 1, 2],
          ['HRGPRJ', 'HPP', 1, 'float', 1, 0],
          ['QNTRBP', 'Retur Beli', 1, 'float', 1, 2],
          ['HRGRBP', 'Retur  Beli', 1, 'float', 1, 0],
          ['QNTADO', 'Kor. Klr', 1, 'float', 1, 2],
          ['HRGADO', 'Kor. Klr', 1, 'float', 1, 0],
          ['QNTTRO', 'Trans. Klr', 1, 'float', 1, 2],
          ['HRGTRO', 'Trans. Klr', 1, 'float', 1, 0],
          ['QNTUKO', 'Ubah Kemasan Out', 1, 'float', 1, 2],
          ['HRGUKO', 'Ubah Kemasan Out', 1, 'float', 1, 0],
          ['QNTPMK', 'Pemakaian', 1, 'float', 1, 2],
          ['HRGPMK', 'Pemakaian', 1, 'float', 1, 0],
          ['SALDOQNT', 'So. Akhir', 1, 'float', 1, 2],
          ['SALDORP', 'So. Akhir', 1, 'float', 1, 0]
      ];

      gsum_issubtotal = 0; gsum_isgrandtotal = 1;
    }
  }

  function setRowHeader(_rowHeader) {
      let _thopen = "", _thclose = "</th>";

      // FIRST ROW
      _rowHeader += '<tr style="height: 45px; padding: 20px;" class="text-center bg-dark text-light">';
      _thopen = '<th rowspan="3" scope="col" style="border: 1px solid black; white-space:nowrap; vertical-align: middle;">';

      if (g_modeReport == modereport_detail) {
        _rowHeader += _thopen + 'Kode' + _thclose;
        _rowHeader += _thopen + 'Nama Barang' + _thclose;
        _rowHeader += _thopen + 'Part Number' + _thclose;
        _rowHeader += _thopen + 'Merk' + _thclose;
      } else {
        _rowHeader += _thopen + 'Kode' + _thclose;
        _rowHeader += _thopen + 'Merk' + _thclose;
        _rowHeader += _thopen + 'GDG' + _thclose;
      }

      _rowHeader += '<th colspan="2" rowspan="2" style="border: 1px solid black; white-space:nowrap; vertical-align: middle;">So. Awal</th>';
      _rowHeader += '<th colspan="16" style="border: 1px solid black; white-space:nowrap;">Masuk</th>';
      _rowHeader += '<th colspan="16" style="border: 1px solid black; white-space:nowrap;">Keluar</th>';
      _rowHeader += '<th colspan="2" rowspan="2" style="border: 1px solid black; white-space:nowrap; vertical-align: middle;">So. Akhir</th>';

      _rowHeader += '</tr>';

      // SECOND ROW
      _rowHeader += '<tr class="text-center bg-dark text-light">';

      const masukKeluarLabels = [
          'Pembelian',    'Retur Jual',      'Kor. Msk',         'Trans. Msk', 
          'R. Pemakaian', 'Ubah Kemasan In', 'Terima dr R.Sjln', 'Gd TC dr SJ',

          'S. Jalan', 'Retur Sjln dr GTC', 'HPP',              'Retur Beli', 
          'Kor. Klr', 'Trans. Klr',        'Ubah Kemasan Out', 'Pemakaian'
      ];
      for (let i = 0; i < masukKeluarLabels.length; i++) {
          _rowHeader += `<th colspan="2" scope="col" style="border: 1px solid black; white-space:nowrap;">${masukKeluarLabels[i]}</th>`;
      }

      _rowHeader += '</tr>';

      // THIRD ROW
      _rowHeader += '<tr class="text-center bg-dark text-light">';

      let _qtyrp = '<th scope="col" style="border: 1px solid black; white-space:nowrap;">Qty</th>';
      _qtyrp += '<th scope="col" style="border: 1px solid black; white-space:nowrap;">Rp.</th>';
      _rowHeader += _qtyrp.repeat(1 + 8 + 8 + 1);

      _rowHeader += '</tr>';

      return _rowHeader;
  }

  function setRowSubheader(_item) {
    if (g_modeReport == modereport_rekap) return;

    _str = _item["pMERK"] + " : " + _item["pNAMAMERK"];
    return '<tr style="text-align: center"><td colspan="' + (colspanRow) + '" class="cellcompact-left" style="border: 1px solid black; white-space:nowrap; background-color:#515962; color:white">' + _str + '</td></tr>';
  }
  
  function getRowFooter1(_col) {
    let _sum = gcart_res.reduce((sum, item) => sum + currencyNormalizer(item[_col]), 0);
    let _decimal = (gcart_header.find(row => row[0] === _col) || [])[5];

    return '  <td class="cellcompact-right" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">' + format_number(_sum, _decimal) + '</td>';
  }
  
  function getRowFooter2(_col) {
    let _sum = gcart_res.filter(item => currencyNormalizer(item[_col]) !== 0).length;
    let _str = '  <td colspan="2" class="cellcompact-right" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">' + _sum + '</td>'

    return { _sum, _str };
  }

  function setRowFooter() {
    if (g_modeReport == modereport_rekap) { return; }
    let rowFooter1 = "", rowFooter2 = "";

    rowFooter1 += "<tr style='text-align: center'>";
    rowFooter1 += '  <td colspan="4" class="cellcompact-center" style="border: 1px solid black; white-space:nowrap; font-weight: bold;">Total Item</td>';

    rowFooter2 += "<tr style='text-align: center'>";
    rowFooter2 += '  <td colspan="4" class="cellcompact-right" style="border: 1px solid black; white-space:nowrap; font-weight: bold;"></td>';

    let tempFooter2;
    let tot_masuk = 0, tot_keluar = 0, tos = 0;

    // So. Awal
    rowFooter1 += getRowFooter1("QntAwal");
    rowFooter1 += getRowFooter1("HRGAWAL");
    tempFooter2 = getRowFooter2("QntAwal");
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // Pembelian
    rowFooter1 += getRowFooter1("QNTPBL");
    rowFooter1 += getRowFooter1("HRGPBL");
    tempFooter2 = getRowFooter2("QNTPBL");
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // Retur Jual
    rowFooter1 += getRowFooter1("QNTRPJ");
    rowFooter1 += getRowFooter1("HRGRPJ");
    tempFooter2 = getRowFooter2("QNTRPJ");
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // Kor. Msk
    rowFooter1 += getRowFooter1("QNTADI");
    rowFooter1 += getRowFooter1("HRGADI");
    tempFooter2 = getRowFooter2("QNTADI");
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // Trans. Msk
    rowFooter1 += getRowFooter1("QNTTRI");
    rowFooter1 += getRowFooter1("HRGTRI");
    tempFooter2 = getRowFooter2("QNTTRI");
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // R. Pemakaian
    rowFooter1 += getRowFooter1("QNTRPK");
    rowFooter1 += getRowFooter1("HRGRPK");
    tempFooter2 = getRowFooter2("QNTRPK");
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // Ubah Kemasan In
    rowFooter1 += getRowFooter1("QNTUKI");
    rowFooter1 += getRowFooter1("HRGUKI");
    tempFooter2 = getRowFooter2("QNTUKI");
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // Terima dr R.Sjln
    rowFooter1 += getRowFooter1("qntrspb");
    rowFooter1 += getRowFooter1("hrgrspb");
    tempFooter2 = getRowFooter2("qntrspb");
    rowFooter2 += tempFooter2._str;
    // tot_masuk  += tempFooter2._sum;

    // Gd TC dr SJ
    rowFooter1 += getRowFooter1("QntHPrd");
    rowFooter1 += getRowFooter1("HRGHPrd");
    tempFooter2 = getRowFooter2("QntHPrd");
    rowFooter2 += tempFooter2._str;
    tot_masuk  += tempFooter2._sum;

    // S.Jalan
    rowFooter1 += getRowFooter1("QNTPNJ");
    rowFooter1 += getRowFooter1("HRGPNJ");
    tempFooter2 = getRowFooter2("QNTPNJ");
    rowFooter2 += tempFooter2._str;
    tot_keluar  += tempFooter2._sum;

    // Retur Sjln dr GTC
    rowFooter1 += getRowFooter1("qntrgtc");
    rowFooter1 += getRowFooter1("hrgrgtc");
    tempFooter2 = getRowFooter2("qntrgtc");
    rowFooter2 += tempFooter2._str;
    tot_keluar  += tempFooter2._sum;

    // HPP
    rowFooter1 += getRowFooter1("QNTPRJ");
    rowFooter1 += getRowFooter1("HRGPRJ");
    tempFooter2 = getRowFooter2("QNTPRJ");
    rowFooter2 += tempFooter2._str;
    // tot_keluar  += tempFooter2._sum;

    // Retur Beli
    rowFooter1 += getRowFooter1("QNTRBP");
    rowFooter1 += getRowFooter1("HRGRBP");
    tempFooter2 = getRowFooter2("QNTRBP");
    rowFooter2 += tempFooter2._str;
    // tot_keluar  += tempFooter2._sum;

    // Kor. Klr
    rowFooter1 += getRowFooter1("QNTADO");
    rowFooter1 += getRowFooter1("HRGADO");
    tempFooter2 = getRowFooter2("QNTADO");
    rowFooter2 += tempFooter2._str;
    tot_keluar  += tempFooter2._sum;

    // Trans. Klr
    rowFooter1 += getRowFooter1("QNTTRO");
    rowFooter1 += getRowFooter1("HRGTRO");
    tempFooter2 = getRowFooter2("QNTTRO");
    rowFooter2 += tempFooter2._str;
    tot_keluar  += tempFooter2._sum;

    // Ubah Kemasan Out
    rowFooter1 += getRowFooter1("QNTUKO");
    rowFooter1 += getRowFooter1("HRGUKO");
    tempFooter2 = getRowFooter2("QNTUKO");
    rowFooter2 += tempFooter2._str;
    tot_keluar  += tempFooter2._sum;

    // Pemakaian
    rowFooter1 += getRowFooter1("QNTPMK");
    rowFooter1 += getRowFooter1("HRGPMK");
    tempFooter2 = getRowFooter2("QNTPMK");
    rowFooter2 += tempFooter2._str;
    tot_keluar  += tempFooter2._sum;

    // So. Akhir
    rowFooter1 += getRowFooter1("SALDOQNT");
    rowFooter1 += getRowFooter1("SALDORP");
    tempFooter2 = getRowFooter2("SALDOQNT");
    rowFooter2 += tempFooter2._str;
    // tot_keluar  += tempFooter2._sum;

    rowFooter1 += "</tr>";
    rowFooter2 += "</tr>";

    let rowTOS = "";
    let _style = 'style="border: none !important; outline: none !important;"';

    rowTOS += '<tr><td colspan="' + (colspanRow) + '" ' + _style + '></td></tr>';
    rowTOS += '<tr><td colspan="' + (colspanRow) + '" ' + _style + '></td></tr>';

    tot_masuk = (tot_masuk !== 0) ? tot_masuk : 1;
    tos = format_number((tot_keluar / tot_masuk) * 100, 2);
    let _td = '<td ' + _style + '></td>';
    rowTOS += '<tr>' + _td + '<td colspan="' + colspanRow + '" class="cellcompact-left" ' + _style + '>Item masuk : ' + tot_masuk + '</td></tr>'
    rowTOS += '<tr>' + _td + '<td colspan="' + colspanRow + '" class="cellcompact-left" ' + _style + '>Item keluar : ' + tot_keluar + '</td></tr>'
    rowTOS += '<tr>' + _td + '<td colspan="' + colspanRow + '" class="cellcompact-left" ' + _style + '>Turn over stock : ' + tos + ' %</td></tr>'
    
    rowTOS += '<tr><td colspan="' + (colspanRow) + '" ' + _style + '></td></tr>';
    rowTOS += '<tr><td colspan="' + (colspanRow) + '" ' + _style + '></td></tr>';

    return rowFooter1 + rowFooter2 + rowTOS;
  }

  function makeTable(_mode) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = "pMERK";
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();

    let data = {
      date1         : _date1,
      date2         : _date2,
      inputGudang   : $("#inputGudang").val(),
      inputMerk     : $("#inputMerk").val(),
      inputTampil   : g_modeReport,
    };

    doMakeTable(_mode, groupby, data, "LAPORAN STOK BULANAN QTY+RUPIAH", _date1);

    doRenameSubTotal("");
    doRenameGrandTotal("");
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
    data = ['pMERK', 'pNAMAMERK'];

    return data;
  }

</script>

@endsection