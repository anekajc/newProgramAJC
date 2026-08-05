@extends('newmaster')
@section('buttons')

@endsection
@section('content')

<link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Sales</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Sales</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Sales</button>
  </div> --}}

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  @include('master.partials.headerTableMaster')

  <div class="table-outer">
    <div class="table-wrap">
      <table class="tb" id="tabel">
        <thead>
          <tr>
            <th scope="col">Actions</th>
            <th scope="col">Nama Sales</th>
            <th scope="col">Telepon HP</th>
            <th scope="col">Kode Gudang</th>
            <th scope="col">Nama Gudang</th>
            <th scope="col">Kode Cost</th>
            <th scope="col">Nama Cost</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-right">
      </tbody>
      </table>
    </div>
</div>

</div>
  
<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Sales</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_Sales" placeholder="Sales" disabled>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode Gudang</label>
                </div>
              </div>
              <div class="col-8">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_edit_kodeGudang" placeholder="Kode Gudang">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary btn-select" onclick="buttonGudang()">+</button>
                    </div>
                </div>
            </div>
            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode Cost</label>
                </div>
              </div>
              <div class="col-8">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_edit_kodeCost" placeholder="Kode Cost">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary btn-select" onclick="buttonCosting()">+</button>
                    </div>
                </div>
              </div>
            </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit</button>
  </div>
</div>
</div>
</div>

<!-- start modal sales -->
<div class="modal fade"  id="formSales" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Sales Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">

            <input type="hidden" id="input_harga_kodegroup" value="" />
            <div class="col-2">
              <div class="form-group">
                <label>Kode Sales</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_harga_kodesales" placeholder="Kode Sales" disabled>
              </div>
            </div>
          
            <div class="col-2 ml-auto text-right">
              <button type="button" class="btn btn-primary" onclick="buttonAddSales()" class="btn btn-secondary">Add Sales Cust</button>
            </div>

          </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addHarga" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Add Sales/Cust</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Kode Cust Supp</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_custSupp_add_kodeCustSupp" placeholder="Kode Cust Supp">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary btn-select" onclick="buttonCustSupp()">+</button>
                    </div>
                </div>
            </div>
            </div>
            

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label>Minggu Ke</label>
                </div>
              </div>
              <div class="col-4">
                <select id="input_custSupp_add_mingguKe" class="form-control" aria-label="Default select example">
                  <option value ='0'>-</option>
                  <option value ='1'>1</option>
                  <option value ='2'>2</option>
                  <option value ='3'>3</option>
                  <option value ='4'>4</option>
                </select>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowSales()" >Batal</button>
                <button type="button" onclick="submitAddSales()" class="btn btn-primary" >Add</button>
              </div>

            </div>
          </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editHarga" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Edit Sales/Cust</h4>
              </div>
            </div>

            <div class="row" hidden>
              <div class="col-2">
                <div class="form-group">
                 <label>Kode Cust Supp LAMA</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_custSupp_edit_kodeCustSupp_old" placeholder="Kode Cust Supp">
                </div>
            </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Kode Cust Supp</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_custSupp_edit_kodeCustSupp" placeholder="Kode Cust Supp">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary btn-select" onclick="buttonCustSupp()">+</button>
                    </div>
                </div>
            </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                  <label>Minggu Ke</label>
                </div>
              </div>
              <div class="col-4">
                <select id="input_custSupp_edit_mingguKe" class="form-control" aria-label="Default select example">
                  <option value ='0'>-</option>
                  <option value ='1'>1</option>
                  <option value ='2'>2</option>
                  <option value ='3'>3</option>
                  <option value ='4'>4</option>
                </select>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowSales()" >Batal</button>
                <button type="button" onclick="submitEditSales()" class="btn btn-primary" >Edit</button>
              </div>

            </div>
          </div>

    <!-- END EDIT SUBGROUP -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_harga" class="table table-bordered table-striped"  >
              <thead id='theadCustom' class="text-center">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Kode Customer</th>
                  <th scope="col">Nama Customer</th>
                  <th scope="col">Minggu Ke</th>

                </tr>
              </thead>

              <tbody id="tabel_data_harga" class="text-left" >

                <tr>
                  <td colspan=3></td>

                    <td class="text-center">
                      <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
                      <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                    </td>
              </tr>
              </tbody>


            </table>
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->


    </div>
  </div>

</div>
</div>
</div>
<!-- End modal sales-->

<!-- start modal target -->
<div class="modal fade"  id="formTarget" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Sales Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">

            <input type="hidden" id="input_harga_kodegroup" value="" />
            <div class="col-2">
              <div class="form-group">
                <label>Kode Sales</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_target_kodesales" placeholder="Kode Sales" disabled>
              </div>
            </div>
          
            <div class="col-2 ml-auto text-right">
              <button type="button" class="btn btn-primary" onclick="buttonAddTarget()" class="btn btn-secondary">Add Target Sales</button>
            </div>

          </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addTarget" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Add Target</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Tahun</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control text-right" id="input_target_add_tahun" placeholder="Tahun">
                </div>
            </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Merk</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_target_add_merk" placeholder="Merk">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary btn-select" onclick="buttonMerk()">+</button>
                    </div>
                </div>
            </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Target</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="number" class="form-control text-right" id="input_target_add_target" placeholder="Target">
                </div>
            </div>
            </div>
            

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" onclick="submitAddTarget()" class="btn btn-primary" >Add</button>
              </div>

            </div>
      </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editTarget" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Edit Sales/Cust</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-4">
                <h4>Edit Target</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Tahun</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control text-right" id="input_target_edit_tahun" placeholder="Tahun" disabled>
                </div>
            </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Merk</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_target_edit_merk" placeholder="Merk" disabled>
                    {{-- <div class="input-group-append">
                        <button type="button" class="btn btn-primary btn-select" onclick="buttonMerk()">+</button>
                    </div> --}}
                </div>
            </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="form-group">
                 <label>Target</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                    <input type="number" class="form-control text-right" id="input_target_edit_target" placeholder="Target">
                </div>
            </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowSales()" >Batal</button>
                <button type="button" onclick="submitEditTarget()" class="btn btn-primary" >Edit</button>
              </div>

            </div>
          </div>

    <!-- END EDIT SUBGROUP -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_targetSales" class="table table-bordered table-striped"  >
              <thead id='theadCustom' class="text-center">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Kode Customer</th>
                  <th scope="col">Nama Customer</th>
                  <th scope="col">Minggu Ke</th>

                </tr>
              </thead>

              <tbody id="tabel_data_targetSales" class="text-left" >

                <tr>
                  <td colspan=3></td>

                    <td class="text-center">
                      <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
                      <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                    </td>
              </tr>
              </tbody>


            </table>
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->


    </div>
  </div>

</div>
</div>
</div>
<!-- End modal target-->

@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []

let listSelectHeadGroup = []
let listSelectSubGroup = []
let listSelectSubKategori = []

function loadAll () {
  let _token = $("#_token").val();

  document.getElementById('breadcrumb').innerHTML = "Master Sales"

  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('mastersalesloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""

    rowTable += `<tr>
    <td class="text-center">
      <div class="action-buttons-wrap">
      <button class="btn-action-sm btn-action-success hover-tooltip" type="button" onclick="buttonEdit('${item.KeyNIK}')" data-tooltip="Koreksi Gudang/Stok">
        <i class="bi bi-pen"></i>
      </button>
      <button class="btn-action-sm btn-action-warning hover-tooltip" type="button" onclick="buttonSales('${item.KeyNIK}')" data-tooltip="Sales Cust">
        <i class="bi bi-receipt"></i>
      </button>
      <button class="btn-action-sm btn-action-primary hover-tooltip" type="button" onclick="buttonTarget('${item.KeyNIK}')" data-tooltip="Target">
        <i class="bi bi-bullseye"></i>
      </button>
      </div>
    </td>
    <td>${item.Nama || ''}</td>
    <td>${item.TeleponHP || ''}</td>
    <td>${item.KodeGdg || ''}</td>
    <td>${item.NamaGdg || ''}</td>
    <td>${item.KodeCost || ''}</td>
    <td>${item.NamaCost || ''}</td>
    </tr>`
  });

  
   let currentLength = $("#tabel_length_visual").val() ? Number($("#tabel_length_visual").val()) : 10;
      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        "lengthChange": false,
        "paging": true,
        "searching": true,
        "dom": 'tip',
        "pageLength": currentLength
      });

}

function buttonEdit (keynik) {
  console.log(keynik)

  keynikTemp = keynik;

  $.ajax({
    url: "{!! url('masterSalesSpDetail') !!}",
    type: "get",
    async: false,
    data: {
      keynik
    },
    success: function(res) {
      console.log(res)

      document.getElementById("input_edit_Sales").value = res[0].Nama
      document.getElementById("input_edit_kodeGudang").value = res[0].KodeGdg
      document.getElementById("input_edit_kodeCost").value = res[0].KodeCost

    }})

    $("#formEdit").modal('toggle')
}

function buttonAddSales () {
  document.getElementById("input_custSupp_add_kodeCustSupp").value = ''

  $('.showhide').hide();
  $('#addHarga').show()
}

function buttonAddTarget () {
  document.getElementById("input_target_add_tahun").value = ''
  document.getElementById("input_target_add_target").value = ''

  $('.showhide').hide();
  $('#addTarget').show()
}

function buttonEditSales (kodeCustSupp, keynik) {

  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterSalesLoadDataSalesEdit') !!}",
    type: "get",
    async: false,
    data: {
      kodeCustSupp,
      keynik,
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_custSupp_edit_kodeCustSupp_old").value = res[0].kodeCustSupp
      document.getElementById("input_custSupp_edit_kodeCustSupp").value = res[0].kodeCustSupp
      document.getElementById("input_custSupp_edit_mingguKe").value = res[0].Mingguke

    }})

  $('#editHarga').show()
}

function closeShowSales () {
  $('.showhide').hide();
}

function submitEditSales () {

  let _token = $("#_token").val();
  console.log('submitEditSales')
  
  let kodeCustSuppOld = $("#input_custSupp_edit_kodeCustSupp_old").val();
  let kodeCustSupp = $("#input_custSupp_edit_kodeCustSupp").val();
  let Mingguke = $("#input_custSupp_edit_mingguKe").val();
  
  console.log(keynikTemp,kodeCustSupp,Mingguke,kodeCustSuppOld)

$.ajax({
  url: "{!! url('masterSalesEditSalesCust') !!}",
  type: "post",
  async: false,
  data: {
    _token,
    keynik: keynikTemp,
    kodeCustSupp,
    kodeCustSuppOld,
    Mingguke
  },
  success: function(res) {
    console.log(res)
    $('.showhide').hide();
    refreshTableSales(keynikTemp)
    alertify.success("Data Sales Cust telah dikoreksi");
  }})

}

function submitAddSales () {

  let _token = $("#_token").val();
  console.log('submitAddSales')
  let kodeCustSupp = $("#input_custSupp_add_kodeCustSupp").val();
  let Mingguke = $("#input_custSupp_add_mingguKe").val();

  $.ajax({
    url: "{!! url('masterSalesAddSalesCust') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodeCustSupp,
      Mingguke,
      keynik: keynikTemp,
      NIK : nikTemp
    },
    success: function(res) {
      console.log(res)
      $('.showhide').hide();
      refreshTableSales(keynikTemp)

    }})

}

function submitAddTarget () {

  let _token = $("#_token").val();
  console.log('submitAddTarget')
  let Tahun = $("#input_target_add_tahun").val();
  let Merk = $("#input_target_add_merk").val();
  let TgtSales = $("#input_target_add_target").val();

  $.ajax({
    url: "{!! url('masterSalesAddTarget') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      Tahun,
      Merk,
      TgtSales,
      keynik: keynikTemp
    },
    success: function(res) {
      console.log(res)
      $('.showhide').hide();
      refreshTableTarget(keynikTemp)

    }})

}

function buttonDeleteTarget (keynik, Tahun) {
  console.log('button Delete Target')
  console.log(keynik, Tahun)

  let _token = $("#_token").val();

  alertify.confirm('Hapus Target', 'Apakah yakin ingin menghapus Target Sales?',
      function() {

        $.ajax({
          url: "{!! url('masterSalesDeleteTarget') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            keynik,
            Tahun
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Target Sales telah dihapus");
              refreshTableTarget(keynik)
          }
          }})
      }
    ,function(){
      console.log('no')
    });
}

function submitEditTarget () {

  let _token = $("#_token").val();
  console.log('submitEditSales')
  
  let TgtSales = $("#input_target_edit_target").val();
  let Merk = $("#input_target_edit_merk").val();
  let Tahun = $("#input_target_edit_tahun").val();
  
  $.ajax({
    url: "{!! url('masterSalesEditTarget') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      keynik: keynikTemp,
      TgtSales,
      Merk,
      Tahun
    },
    success: function(res) {
      console.log(res)
      $('.showhide').hide();
      refreshTableTarget(keynikTemp)
      alertify.success("Data Target telah dikoreksi");
    }})

  }

function buttonEditTarget (keynik, Tahun) {

  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterSalesLoadDataTargetEdit') !!}",
    type: "get",
    async: false,
    data: {
      Tahun,
      keynik,
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_target_edit_tahun").value = res[0].Tahun
      document.getElementById("input_target_edit_merk").value = res[0].Merk
      document.getElementById("input_target_edit_target").value = res[0].TgtSales

    }})

  $('#editTarget').show()
}

function buttonDelete (kodebarang) {
  console.log('buttonDeleteSales')
  console.log(kodebarang)

  let _token = $("#_token").val();

  alertify.confirm('Hapus Harga', 'Apakah yakin ingin menghapus Barang ' + kodebarang + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterbarangspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodebarang,
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Barang telah dihapus");
          }
          }})
      }
    ,function(){
      console.log('no')
    });
}

function buttonDelete (kodebarang) {
  console.log('buttonDeleteSales')
  console.log(kodebarang)

  let _token = $("#_token").val();

  alertify.confirm('Hapus Harga', 'Apakah yakin ingin menghapus Barang ' + kodebarang + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterbarangspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodebarang,
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Barang telah dihapus");
          }
          }})
      }
    ,function(){
      console.log('no')
    });
}

function buttonDeleteSales (kodeCustSupp, keynik) {
  
  let _token = $("#_token").val();

  alertify.confirm('Hapus Harga', 'Apakah yakin ingin menghapus Sales ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterSalesDeleteSalesCust') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            keynik,
            kodeCustSupp
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              refreshTableSales(keynik)
              alertify.success("Data Sales Cust telah dihapus");

          }
          }})
      }
    ,function(){
      console.log('no')
    });

}

let nikTemp = 0

function buttonSales (keynik) {
  
  keynikTemp = keynik;

  $.ajax({
      url: "{!! url('masterSalesLoadKaryawan') !!}",
      type: "get",
      async: false,
      data: {
        keynik
      },
      success: function(res) {

        nikTemp = res[0].NIK

      }})

  $('.showhide').hide();

    $.ajax({
      url: "{!! url('masterSalesLoadDataSales') !!}",
      type: "get",
      async: false,
      data: {
        keynik
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditSales('${item.kodeCustSupp}', '${item.Keynik}')" type="button" data-tooltip="Edit Harga"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteSales('${item.kodeCustSupp}', '${item.Keynik}')" type="button" data-tooltip="Delete Harga" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.kodeCustSupp}</td>
          <td>${item.namaCustSupp}</td>
          <td>${item.Mingguke}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_harga").innerHTML = rowTable
        document.getElementById("input_harga_kodesales").value = keynik

      }})


  $("#formSales").modal('toggle')
}

function buttonTarget (keynik) {
  
  keynikTemp = keynik;

  $('.showhide').hide();

    $.ajax({
      url: "{!! url('masterSalesLoadDataTarget') !!}",
      type: "get",
      async: false,
      data: {
        keynik
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditTarget('${item.KeyNik}', '${item.Tahun}')" type="button" data-tooltip="Edit Target Sales"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteTarget('${item.KeyNik}', '${item.Tahun}')" type="button" data-tooltip="Delete Target Sales" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.Tahun}</td>
          <td>${item.Merk}</td>
          <td>${item.TgtSales}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_targetSales").innerHTML = rowTable
        document.getElementById("input_target_kodesales").value = keynik

      }})


  $("#formTarget").modal('toggle')
}

function refreshTableSales(keynik){

  $.ajax({
      url: "{!! url('masterSalesLoadDataSales') !!}",
      type: "get",
      async: false,
      data: {
        keynik
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditSales('${item.kodeCustSupp}', '${item.Keynik}')" type="button" data-tooltip="Edit Harga"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteSales('${item.kodeCustSupp}', '${item.Keynik}')" type="button" data-tooltip="Delete Harga" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.kodeCustSupp}</td>
          <td>${item.namaCustSupp}</td>
          <td>${item.Mingguke}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_harga").innerHTML = rowTable
        document.getElementById("input_harga_kodesales").value = keynik

      }})

}

function refreshTableTarget(keynik){

    $.ajax({
      url: "{!! url('masterSalesLoadDataTarget') !!}",
      type: "get",
      async: false,
      data: {
        keynik
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn btn-success btn-sm hover-tooltip" onclick="buttonEditTarget('${item.KeyNik}', '${item.Tahun}')" type="button" data-tooltip="Edit Target Sales"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm hover-tooltip" onclick="buttonDeleteTarget('${item.KeyNik}', '${item.Tahun}')" type="button" data-tooltip="Delete Target Sales" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.Tahun}</td>
          <td>${item.Merk}</td>
          <td>${item.TgtSales}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_targetSales").innerHTML = rowTable
        document.getElementById("input_target_kodesales").value = keynik

      }})
}

let keynikTemp = 0

function submitEdit () {
  
  let _token = $("#_token").val();

  let kodeGdg = $("#input_edit_kodeGudang").val();
  let kodeCost = $("#input_edit_kodeCost").val();

  $.ajax({
    url: "{!! url('masterSalesSpEdit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      keynikTemp,
      kodeGdg,
      kodeCost
    },
    success: function(res) {

    console.log(res)
    if (res != 1) {
      alertify.warning(res);
    }  else {
      alertify.success('Gudang/Cost telah diedit');
      loadAll()
      $("#formEdit").modal('toggle')
    }
    }})

}

function buttonGudang () {
  console.log('asd');
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSalesListGudang') !!}",
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
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectGudang('${item.KODEGDG}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.KODEGDG}</td>
      <td>${item.NAMA}</td>
      <td>${item.Alamat}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Kode Gudang</th>
    <th scope="col">Nama Gudang</th>
    <th scope="col">Alamat Gudang</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Gudang'

  $("#tabelModalOpen").DataTable({
    "lengthChange": false,
    "paging": false,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonCosting () {
  console.log('asd');
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSalesListCosting') !!}",
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
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectCosting('${item.KodeCost}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.KodeCost}</td>
      <td>${item.NamaCost}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Kode Costing</th>
    <th scope="col">Nama Costing</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Costing'

  $("#tabelModalOpen").DataTable({
    "lengthChange": false,
    "paging": false,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonCustSupp () {
  console.log('asd');
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSalesListCustSupp') !!}",
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
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectCustSupp('${item.KodeCust}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.KodeCust}</td>
      <td>${item.NamaCust}</td>
      <td>${item.Alamat}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Kode</th>
    <th scope="col">Nama Pelanggan</th>
    <th scope="col">Alamat</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Cust Supp'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonMerk () {
  console.log('asd');
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSalesListMerk') !!}",
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
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectMerk('${item.KodeMerk}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.KodeMerk}</td>
      <td>${item.NamaMerk}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Kode Merk</th>
    <th scope="col">Nama Merk</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Merk'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectGudang (kodegdg){

  document.getElementById('input_edit_kodeGudang').value = kodegdg;

  $("#formModalOpen").modal("hide");
}

function buttonSelectCosting (kodecost){

  document.getElementById('input_edit_kodeCost').value = kodecost;

  $("#formModalOpen").modal("hide");
}

function buttonSelectCustSupp (kodesales){

  document.getElementById('input_custSupp_add_kodeCustSupp').value = kodesales;
  document.getElementById('input_custSupp_edit_kodeCustSupp').value = kodesales;

  $("#formModalOpen").modal("hide");
}

function buttonSelectMerk (merk){

  document.getElementById('input_target_add_merk').value = merk;
  // document.getElementById('input_target_edit_merk').value = merk;

  $("#formModalOpen").modal("hide");
}

window.onload = function() {
    loadAll();
};
</script>

<!-- start modal select add akumulasi penyusutan -->
<div class="modal fade" id="formModalOpen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="namaModalOpen"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelModalOpen" class="table table-bordered table-striped">
          <thead id='theadOpen' class="text-center bg-primary text-white">
            <tr></tr>
          </thead>
          <tbody id="tabel_dataModalOpen" class="text-left">
            <tr></tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal select add akumulasi penyusutan-->

@endsection
