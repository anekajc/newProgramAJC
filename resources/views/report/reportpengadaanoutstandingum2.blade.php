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
          <input type="date" class="form-control mt-1" id="inputDate2" value="{!! date('Y-m-d') !!}">
        </div>
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
  let globalDate2 = "{!! date('Y-m-d') !!}";

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

    showPeriode();

    setDefaultHeader();

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });
  
  // periode
  function showPeriode() {
    globalDate2 = $('#inputDate2').val();
    // alertify.success(`Periode: ${globalDate1} s/d ${globalDate2}`);
  }

  var modereport_detailnobukti = 0, modereport_detailbarang = 1;
  g_modeReport = modereport_detailnobukti;
  var jenisreport = 0; // ini untuk detail dan rekap

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailnobukti) {
      gcart_header = [
        ['tanggal', 'Tgl BBK', 1, 'date', 0, 0],
        ['NoBukti', 'No BBK', 1, 'varchar', 0, 0],
        ['NoFaktur', 'No UM', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Supplier', 1, 'varchar', 0, 0],
        ['NOSO', 'No. PO', 1, 'varchar', 0, 0],
        ['dpp', 'Rp. UM', 1, 'float', 1, 2],
        ['bayar', 'Bayar', 1, 'float', 1, 2],
        ['sisa', 'Sisa', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else if (g_modeReport == modereport_detailbarang){
      gcart_header = [
        ['tanggal', 'Tgl BBK', 1, 'date', 0, 0],
        ['NoBukti', 'No BBK', 1, 'varchar', 0, 0],
        ['NoFaktur', 'No UM', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Supplier', 1, 'varchar', 0, 0],
        ['NOSO', 'No. PO', 1, 'varchar', 0, 0],
        ['dpp', 'Rp. UM', 1, 'float', 1, 2],
        ['bayar', 'Bayar', 1, 'float', 1, 2],
        ['sisa', 'Sisa', 1, 'float', 1, 2]  
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    }
  }

  function makeTable(_mode) {
    console.log("makeTable jalankan mode:", _mode);

    let groupby = 'NOBUKTI';
    let _date2 = $("#inputDate2").val();

    g_modeReport = modereport_detailnobukti;

    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    let data = {
      date2: _date2,
    };

    console.log("Data terkirim ke server:", data);

    doMakeTable(_mode, groupby, data, "REPORT OUTSTANDING UANG MUKA BELI2", _date2);
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    return ['NoBukti', 'tanggal'];
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
    if (jenisreport === 0) {
      g_modeReport = modereport_detailnobukti;
    } else {
      g_modeReport = modereport_rekapnobukti;
    }

    doSetHeader(g_modeReport);
    doShowCustomize();
  }


</script>

@endsection













{{-- @extends('newmaster')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-6 text-left">
      <h3>Report Pengadaan - OutStanding Uang Muka Beli (2)</h1>
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

                <br>

                <div class="row rounded" style="background-color: #E8E8E8; padding: 10px; display: flex; justify-content: center;">
                  <div class="col-2 text-start" style="padding: 0 7px;">
                    <label for="inputDate1" style="">Periode</label>
                  </div>
                  <div class="col-2" style="padding: 0 7px; flex-basis: 0;">
                    <label for="inputDate1" style="">Akhir</label>
                  </div>
                  <div class="col-3" style="padding: 0 7px; flex-basis: 0;">
                    <input type="date" class="form-control" id="inputDate2" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>

                <br>

                <div class="row pr-3" style="display: flex; justify-content: right;">
                  <button type="button" class="btn btn-primary" style="font-size: 16px; margin: 0 5px;" onclick="showFormFilterData()">Filter Data</button>
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
                <table id="tabel" class="table table-bordered">

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
    let _cellcount = 0;
    tempcart.forEach((item, i) => {
      _cellcount += item[2];
    });

    let rowTable = "";
    let _qnt = 0.00, _hpp = 0, _dpp = 0, _bayar = 0, _sisa = 0;
    let showQnt = true, showHPP = false;
    let posArray = [], posCount = 0;

    let orderByText = "";
    let inputOrd = $("#inputOrder").val();

    if (inputOrd === "N") {
        orderByText = "PER NOMOR BUKTI";
    } else if (inputOrd === "B") {
        orderByText = "PER NOMOR BARANG";
    } else if (inputOrd === "S") {
        orderByText = "PER NOMOR CUSTOMER";
    } else {
        orderByText = "";
    }

    // TABLE HEADER
    rowTable += '<tr>';
    rowTable += '  <th colspan="' + _cellcount + '" style="text-align: left; font-weight: bold;">PT. MITRA GLOBALINDO LESTARI<br/> REPORT PENGADAAN - PR<br/>'+ orderByText+ '</th>';
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
          if (item[0] == "dpp")      { showQnt = true; posArray.push(posCount); }
          if (item[0] == "NilaiHPP") { showHPP = true; posArray.push(posCount); }
        }
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
        _nownb = item.NoBukti;

        rowTable += "<tr style='text-align: center'>";
        tempcart.forEach((itemcart, j) => {
          if (itemcart[2]){
            if (itemcart[0] == "NoBukti") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black;">' + _nownb + '</td>';
            } else if (itemcart[0] == "Tanggal") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black;">' + format_date(item.Tanggal) + '</td>';
            } else if (itemcart[0] == "Qnt") {
              _qnt += toFloat(item.Qnt);
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; text-align: right;">' + item.Qnt + '</td>';
            } else if (itemcart[0] == "tanggal") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black;">' + format_date(item.tanggal) + '</td>';
            } else if (itemcart[0] == "NilaiHPP") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; text-align: right;">' + numberNoDecimals(item.NilaiHPP) + '</td>';
            } else if (itemcart[0] == "NamaBrg") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; text-align: center; white-space: nowrap;">' + item.NamaBrg + '</td>';
            } else if (itemcart[0] == "dpp") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; text-align: center; white-space: nowrap;">' + numbersWithDividers(item.dpp) + '</td>';
            } else if (itemcart[0] == "sisa") {
              _sisa += toFloat(item.sisa);
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; text-align: center; white-space: nowrap;">' + numbersWithDividers(item.sisa) + '</td>'; 
            } else if (itemcart[0] == "bayar") {
              _bayar += toFloat(item.bayar);
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; text-align: center; white-space: nowrap;">' + numbersWithDividers(item.bayar) + '</td>'; 
            } else if (itemcart[0] == "NAMACUSTSUPP") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black; white-space: nowrap;">' + nullToEmpty(item.NAMACUSTSUPP) + '</td>';
            } else if (itemcart[0] == "Nomor") {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black;">' + (i+1) + '</td>';
            } else {
              rowTable += '  <td class="cellcompact-left" style="border: 1px solid black;">' + item[itemcart[0]] + '</td>';
            }
          }
        });
        rowTable += "</tr>";

        _prevnb = item.NoBukti;
      })

      // TABLE FOOTER
      if (showQnt || showHPP) {
        let _counter = 0;
        rowTable += '<tr id="gtrow" style="text-align: center">';
        tempcart.forEach((item, i) => {
          if (item[2]) {
            _counter++;
            if (item[0] == "dpp") {
              rowTable += '  <td id="gt' + _counter + '" style="border:1px solid black; font-weight: bold; text-align: right;">' + numbersWithDividers(_dpp) + '</td>';
            } else if (item[0] == "bayar") {
              rowTable += '  <td id="gt' + _counter + '" style="border:1px solid black; font-weight: bold; text-align: right;">' + numbersWithDividers(_bayar) + '</td>';
            } else if (item[0] == "sisa") {
              rowTable += '  <td id="gt' + _counter + '" style="border:1px solid black; font-weight: bold; text-align: right;">' + numbersWithDividers(_sisa) + '</td>';
            } else {
              rowTable += '  <td id="gt' + _counter + '" style="border-bottom-style: hidden; border-right-style: 0px solid black; border-left-style: 0px solid black; font-weight: bold; text-align: right;"></td>';
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

    // TABLE FOOTER - POSISI TULISAN TOTAL
    if (res.length > 0) {
    let posStrTotal = (posArray.length > 0) ? Math.min(...posArray) - 2 : 0;

    if (posStrTotal < 0) {
      // nothing to do
    } else if (posStrTotal == 0) {
      $("#gt1")
        .html("Total :")
        .css('border-right', '1px solid black',); // Add border here
    } else if (posStrTotal == (_cellcount - 1)) {
      $("#gt" + posStrTotal)
        .html("Total :")
        .css('border-right', '1px solid black'); // Add border here
    } else {
      let _row = document.getElementById("gtrow");
      _row.deleteCell(posStrTotal); // Remove the unwanted cell

      $("#gt" + posStrTotal)
        .attr('colspan', 2) // Span 2 columns
        .html("Total :")
        .css('border-right', '1px solid black'); // Add border here
    }
  }

    godown();
  }

  function makeTable() {
    let date1    = $("#inputDate1").val();
    let date2    = $("#inputDate2").val();
    let inputOto = $("#inputOtorisasi").val();
    let inputOrd = $("#inputOrder").val();

    document.getElementById("showTableReport").style.display = "block"
    $.ajax({
      url     : "{!! url('laporanpengadaanoutstandingum2_doReport') !!}",
      type    : "get",
      async   : false,
      data    : {
        date1,
        date2,
        inputOto,
        inputOrd
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
        ['tanggal', 'Tgl BBK', 1],
        ['NoBukti', 'No BBK', 1],
        ['NoFaktur', 'No UM', 1],
        ['NAMACUSTSUPP', 'Supplier', 1],
        ['NOSO', 'No. PO', 1],
        ['dpp', 'Rp. UM', 1],
        ['bayar', 'Bayar', 1],
        ['sisa', 'Sisa', 1]
      ];

      doSimpanHeader('{!! $akses['href'] !!}', modereport_detail, cart_headerDetail);
    }
  }

  function setHeaderRekap(_isReset = false) {
    let _strHeader = (!_isReset) ? doLoadHeader('{!! $akses['href'] !!}', modereport_rekap) : "";

    if (_strHeader != "") {
      cart_headerRekap = doGetHeader(_strHeader);
    } else {
      cart_headerRekap = [
        ['Nomor', 'Nomor (No)', 1],
        ['tanggal', 'Tgl BBK', 1],
        ['NoBukti', 'No BBK', 1],
        ['NoFaktur', 'No UM', 1],
        ['NAMACUSTSUPP', 'Supplier', 1],
        ['NOSO', 'No. PO', 1],
        ['dpp', 'Rp. UM', 1],
        ['bayar', 'Bayar', 1],
        ['sisa', 'Sisa', 1]
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
      url     : "{!! url('laporanpengadaanoutstandingum2_doFilter') !!}",
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
            let _data1 = (inputOrd == "N") ? item.NoBukti : item.KodeBrg;
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
      url     : "{!! url('laporanpengadaanoutstandingum2_doReportFilter') !!}",
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
