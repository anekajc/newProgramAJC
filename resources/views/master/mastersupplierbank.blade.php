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
    <span class="sp-crumb-active">Supplier Bank Account</span>
  </div> --}}

  <div class="sp-page-head">
    <div>
      <h1>Master Supplier Bank Account</h1>
    </div>
    {{-- <button class="btn btn-primary" onclick="buttonAdd()">+ Add Supplier Bank Account</button> --}}
  </div>

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  @include('master.partials.headerTableMaster')

          <div class="table-outer">
            <div class="table-wrap">
              <table class="tb" id="tabel">
                <thead>
                  <tr>
                    <th scope="col">Actions</th>
                    <th scope="col">Kode Supplier</th>
                    <th scope="col">Nama Supplier</th>
                    <th scope="col">Bank</th>
                    <th scope="col">No. Acc</th>
                    <th scope="col">ATN</th>
                    <th scope="col">Bank Temp</th>
                    <th scope="col">No ACC Temp</th>
                    <th scope="col">AN Temp</th>
                  </tr>
                </thead>
                <tbody id="tabel_data" class="text-right">
              </tbody>
              </table>
            </div>
        </div>

</div>

<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kode Valas</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_kode" placeholder="Kode Valas">
                </div>
              </div>

            </div>

            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Nama Valas</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_nama" placeholder="Nama Valas">
                </div>
              </div>

            </div>

            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kurs</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group mb-1">
                  <input type="text" class="form-control text-right" id="input_add_kurs" 
                    value="0.00" 
                    style="font-variant-numeric: tabular-nums;" 
                    oninput="formatNumber(this)">
                </div>
              </div>

            </div>

            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Simbol</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_simbol" placeholder="Simbol">
                </div>
              </div>

            </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

<!-- start modal add -->
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
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="" />

            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kode Supplier</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_kode"disabled>
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Bank Temp</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_bankTemp">
                </div>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">No. ACC Temp</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_noAccTemp">
                </div>
              </div>

            </div>

            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">ATN Temp</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_atnTemp">
                </div>
              </div>

            </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  let _token = $("#_token").val();
  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersupplierbankloadall') !!}",
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

    rowTable += `<tr class='theadCustom'>
    <td style="white-space:nowrap;" class='text-center'>
      <div class="action-buttons-wrap">
          <button data-toggle="tooltip" data-placement="top" title="Update Bank" class="btn-action-sm btn-action-primary" type="button" onclick="buttonUpdateBank('${item.KODECUSTSUPP}')"><i class="bi bi-send"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODECUSTSUPP}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KODECUSTSUPP}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${item.KODECUSTSUPP || ''}</td>
    <td>${item.NAMACUSTSUPP || ''}</td>
    <td>${item.bank || ''}</td>
    <td>${item.NoAcc || ''}</td>
    <td>${item.ATN || ''}</td>
    <td>${item.bankTemp || ''}</td>
    <td>${item.NoAccTemp || ''}</td>
    <td>${item.ATNTemp || ''}</td>
    </tr>`

  });

  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable({
    "lengthChange": true,
    "paging": true,
    "searching": true,
    "dom": 'tip'
  });

}

function buttonAdd () {
  document.getElementById("input_add_kode").value = ""
  document.getElementById("input_add_nama").value = ""
  document.getElementById("input_add_kurs").value = ""
  document.getElementById("input_add_simbol").value = ""

  $("#form").modal('toggle')

}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastersupplierbankspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].KodeCustSupp
      document.getElementById("input_edit_bankTemp").value = res[0].BankTemp
      document.getElementById("input_edit_noAccTemp").value = res[0].NoaccTemp
      document.getElementById("input_edit_atnTemp").value = res[0].ATNTemp

    
    }})
    $("#formEdit").modal('toggle')
}


function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Cust SUpp', 'Apakah yakin ingin menghapus Cust Supp ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersupplierbankspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Data Cust Supp telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function buttonUpdateBank (kode) {
  console.log(kode)
  let _token = $("#_token").val();

  alertify.confirm('Update Bank Account', 'Update Bank Account ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersupplierbankspupdate') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Data telah di-update");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}
//
function submitEdit () {

  let _token = $("#_token").val();
  let kode = $("#input_edit_kode").val();
  let bankTemp = $("#input_edit_bankTemp").val();
  let noAccTemp = $("#input_edit_noAccTemp").val();
  let atnTemp = $("#input_edit_atnTemp").val();

  $.ajax({
    url: "{!! url('mastersupplierbankspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      bankTemp,
      noAccTemp,
      atnTemp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Supplier Bank Acc. telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}
//
function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val();
  let nama = $("#input_add_nama").val();
  let kurs = parseFloat(($("#input_add_kurs").val() || 0).toString().replace(/,/g, '')) || 0;
  let simbol = $("#input_add_simbol").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (!kurs) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (!simbol) {
    alertify.warning("Nama harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastervalasspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      kurs,
      simbol
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Valas telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

}

window.onload = function(){
  loadAll();
};

</script>



@endsection
