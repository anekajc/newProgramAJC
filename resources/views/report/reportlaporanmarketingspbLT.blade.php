@extends('masterreport')

@section('reportname')
      <h3>Report Marketing - Lead Time SPB Invoice</h3>
@endsection


@section('input')

<div class="row" style="display: flex; justify-content: center;">
  <div class="col-6 btn-primary text-center tombol-toggle" id="tombolMode0" onclick="reportMode(0)">Detail</div>
  <div class="col-6 btn-outline-primary text-center tombol-toggle" id="tombolMode1" onclick="reportMode(1)">Rekap</div>
</div>

<br>

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
      <label for="inputOtorisasi">Otorisasi</label>
  </div>
  <div class="col-4">
      <select id="inputOtorisasi" class="form-control" aria-label="Default select example">
      <option value=0>Otorisasi</option>
      <option value=1>Non Otorisasi</option>
      <option value=2>Semua</option>
      </select>
  </div>
      <div class="col-2">
        <label for="inputOrder">Order By</label>
      </div>
      <div class="col-4">
        <select id="inputOrder" onclick="setModeReport()" class="form-control" aria-label="Default select example">
          <option value="LT">Lead Time SPB Invoice</option>
        </select>
      </div>
  </div>
  <div class="row text-center mt-4">
  <div class="col-2">
    <label for="inputTerima">Tgl. Terima</label>
  </div>
  <div class="col-4">
    <select id="inputTerima" class="form-control" aria-label="Default select example">
      <option value=0>Tgl. Terima</option>
      <option value=1>Non Tgl. Terima</option>
      <option value=2>Semua</option>
  </select>
  </div>
</div>
</div>
@endsection

@section('jsreport')
<script type="text/javascript">
  var modereport_detailnobukti = 0, modereport_detailbarang = 1, modereport_detailcustomer = 2 ;
  var modereport_rekapnobukti = 3, modereport_rekapbarang = 4, modereport_rekapcustomer = 5 ;
  g_modeReport = modereport_detailnobukti;
  var jenisreport = 0; // ini untuk detail dan rekap

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailnobukti) {
      gcart_header = [
        ['nobukti', 'Tanggal', 1, 'varchar', 0, 0],
        ['Tanggal', 'PO. Cust', 1, 'date', 0, 0],
        ['NoPOCustomer', 'Lokasi Penerima', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Customer', 1, 'varchar', 0, 0],
        ['TGLKIRIM', 'Nama Barang', 1, 'date', 0, 0],
        ['TGLTERIMA', 'Sat', 1, 'date', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if (g_modeReport == modereport_detailbarang){
      gcart_header = [
        ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode Customer', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['QNT', 'Qnt', 1, 'float', 1, 0],
        ['NetW', 'Net W', 1, 'float', 1, 2],
        ['GrossW', 'Gross W', 1, 'float', 1, 2],
        ['HARGA', 'Harga', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if(g_modeReport == modereport_detailcustomer){
      gcart_header = [
        ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode Customer', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
        ['QNT', 'Qnt', 1, 'float', 1, 0],
        ['NetW', 'Net W', 1, 'float', 1, 2],
        ['GrossW', 'Gross W', 1, 'float', 1, 2],
        ['HARGA', 'Harga', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if(g_modeReport == modereport_rekapnobukti){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NDPP', 'DPP IDR', 1, 'float', 1, 2],
        ['NPPN', 'PPN IDR', 1, 'float', 1, 2],
        ['TotalIDR', 'Total IDR', 1, 'float', 1, 2],
        ['KODEVLS', 'Vls', 1, 'varchar', 0, 0],
        ['kurs', 'Kurs', 1, 'varchar', 0, 0],
        ['Ndppusd', 'DPP $', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN $', 1, 'float', 1, 2],
        ['totalusd', 'Total $', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else if(g_modeReport == modereport_rekapbarang){
      gcart_header = [
        ['KodeBrg', 'No Bukti', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Qnt', 'QNT', 1, 'float', 1, 2],
        ['NDPP', 'DPP IDR', 1, 'float', 1, 2],
        ['NPPN', 'PPN IDR', 1, 'float', 1, 2],
        ['TotalIDR', 'Total IDR', 1, 'float', 1, 2],
        ['KODEVLS', 'Vls', 1, 'varchar', 0, 0],
        ['kurs', 'Kurs', 1, 'varchar', 0, 0],
        ['Ndppusd', 'DPP $', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN $', 1, 'float', 1, 2],
        ['totalusd', 'Total $', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 0; gsum_isgrandtotal = 1;

    } else {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['KodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['NDPP', 'DPP IDR', 1, 'float', 1, 2],
        ['NPPN', 'PPN IDR', 1, 'float', 1, 2],
        ['TotalIDR', 'Total IDR', 1, 'float', 1, 2],
        ['KODEVLS', 'Vls', 1, 'varchar', 0, 0],
        ['kurs', 'Kurs', 1, 'varchar', 0, 0],
        ['Ndppusd', 'DPP $', 1, 'float', 1, 2],
        ['NPPNusd', 'PPN $', 1, 'float', 1, 2],
        ['totalusd', 'Total $', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;
    }
  }

  function makeTable(_mode) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    // mode report menentukan kolom yang dipakai
    let groupby = '';
    let _date1    = $("#inputDate1").val();
    let _date2    = $("#inputDate2").val();
    let inputOto = $("#inputOto").val();
    let input_order = $("#inputOrder").val();
      if (input_order == "LT") {
        groupby = 'NoBukti';
      } else if (input_order == "B") {
        groupby = 'KODEBRG';
      } else {
        groupby = 'KodeCustSupp';
      }

    let data = {
      date1    : _date1,
      date2    : _date2,
      inputOto : $("#inputOto").val(),
      inputOrd : input_order,
    };

    doMakeTable(_mode, groupby, data, "REPORT MARKETING - Lead Time SPB Invoice", _date1, _date2);
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if ($("#inputOrder").val() == "LT"){
      data = ['NoBukti', 'Tanggal'];
    } else if ($("#inputOrder").val() == "B"){
      data = ['KODEBRG', 'NAMABRG'];
    } else {
      data = ['KodeCustSupp', 'NAMACUSTSUPP'];
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
    if ($("#inputOrder").val() == "LT") {
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
    } else {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailcustomer;
      } else {
        g_modeReport = modereport_rekapcustomer;
      }
    }

    doSetHeader(g_modeReport);
    doShowCustomize();
  }


</script>

@endsection
