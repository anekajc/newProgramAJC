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
    <span class="sp-crumb-active">Valas</span>
  </div> --}}

  <div class="sp-page-head">
    <div>
      <h1>Master Valas</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Valas</button>
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
                    <th scope="col">Kode Valas</th>
                    <th scope="col">Nama Valas</th>
                    <th scope="col">Kurs</th>
                    <th scope="col">Valas</th>
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
                  <input type="text" class="form-control" id="input_add_simbol" placeholder="Simbol" maxlength="4">
                </div>
              </div>

            </div>

    </div>
  </div>
  <div class="modal-footer">
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
                  <label class="text-left">Kode Valas</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_kode" placeholder="Kode Valas" disabled>
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
                  <input type="text" class="form-control" id="input_edit_nama" placeholder="Nama Valas">
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
                  <input type="text" class="form-control text-right" id="input_edit_kurs" 
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
                  <input type="text" class="form-control" id="input_edit_simbol" placeholder="Simbol" maxlength="4">
                </div>
              </div>
            </div>

    </div>
  </div>
  <div class="modal-footer">
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
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('mastervalasloadall') !!}",
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
            <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODEVLS}')"><i class="bi bi-pen"></i></button>
            <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KODEVLS}')"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    <td>${item.KODEVLS}</td>
    <td>${item.NAMAVLS}</td>
    <td class='text-right'>${new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(item.KURS))}</td>
    <td>${item.Simbol}</td>
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
    url: "{!! url('mastervalasspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].KODEVLS
      document.getElementById("input_edit_nama").value = res[0].NAMAVLS
      document.getElementById("input_edit_kurs").value = parseFloat(res[0].KURS).toFixed(2);
      document.getElementById("input_edit_simbol").value = res[0].Simbol

      
      formatNumber(document.getElementById("input_edit_kurs"))
      

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Valas', 'Apakah yakin ingin menghapus Kode Valas ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastervalasspdelete') !!}",
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
              alertify.success("Kode Valas telah dihapus");

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
  let nama = $("#input_edit_nama").val();
  let kurs = parseFloat(($("#input_edit_kurs").val() || 0).toString().replace(/,/g, '')) || 0;
  let simbol = $("#input_edit_simbol").val();

  console.log(kode,nama)
  if (!kode) {
    alertify.warning("Kode  harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama  harus diisi");
    return
  }

  if (!kurs) {
    alertify.warning("Kurs  harus diisi");
    return
  }

  if (!simbol) {
    alertify.warning("Simbol  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastervalasspedit') !!}",
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
        alertify.success("Data Departemen telah diedit");
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
