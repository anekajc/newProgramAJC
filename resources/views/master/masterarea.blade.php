@extends('newmaster')
@section('buttons')

@endsection
@section('content')

 <link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}?v={{ filemtime(public_path('css/tableMaster2.css')) }}">


  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Satuan</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Satuan</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Satuan</button>
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
            <th scope="col">Kode Area</th>
            <th scope="col">Nama Area</th>
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

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode Area</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kodearea" placeholder="Kode Area">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama Area</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_namaarea" placeholder="Nama Area">
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

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode Area</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kodearea" placeholder="Kode Area">
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama Area</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_namaarea" placeholder="Nama Area">
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
                  <label class="text-left">Kode Area</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kodearea" placeholder="Kode Area" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama Area</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_namaarea" placeholder="Nama Area">
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
<!-- End modal edit-->

@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();

  document.getElementById('breadcrumb').innerHTML = "Master Area"

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masterarealoadall') !!}",
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
    <td style="white-space:nowrap;" class='text-center'>
      <div class="action-buttons-wrap">
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODEAREA}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KODEAREA}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${item.KODEAREA}</td>
    <td>${item.NAMAAREA}</td>
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

function buttonAdd () {

  document.getElementById('input_add_kodearea').value = ''
  document.getElementById('input_add_namaarea').value = ''

  $("#form").modal('toggle')

}

function buttonEdit (kodearea) {
  console.log(kodearea)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masterareaspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodearea
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kodearea").value = res[0].KODEAREA
      document.getElementById("input_edit_namaarea").value = res[0].NAMAAREA

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (kodearea) {
  console.log(kodearea)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Area', 'Apakah yakin ingin menghapus Area ' + kodearea + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterareaspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodearea
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Area telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function submitEdit () {

  let _token = $("#_token").val();
  let kodearea = $("#input_edit_kodearea").val();
  let namaarea = $("#input_edit_namaarea").val();
  console.log(kodearea,namaarea)
  if (!kodearea) {
    alertify.warning("Kode area harus diisi");
    return
  }

  if (!namaarea) {
    alertify.warning("Nama area harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterareaspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodearea,
      namaarea
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Area telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();
  let kodearea = $("#input_add_kodearea").val();
  let namaarea = $("#input_add_namaarea").val();

  if (!kodearea) {
    alertify.warning("Kode area harus diisi");
    return
  }

  if (!namaarea) {
    alertify.warning("Nama area harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterareaspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodearea,
      namaarea
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Area telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

window.onload = function () {
  loadAll();
}

</script>




@endsection
