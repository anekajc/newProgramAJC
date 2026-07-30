@extends('newmaster')
@section('buttons')

@endsection
@section('content')

<link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Berkas</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Menu</span>
  </div> --}}

  <div class="sp-page-head">
    <div>
      <h1>Menu</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Menu</button>
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
                    <th scope="col">Kode</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">L0</th>
                    <th scope="col">Kode Akses</th>
                    <th scope="col">Level Otorisasi</th>
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
        <h5 class="modal-title" id="formTitle">Add</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Kode Menu</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_kodeMenu">
                </div>
              </div>

            </div>

            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_keterangan">
                </div>
              </div>

            </div>

            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">L0</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_L0">
                </div>
              </div>

            </div>


            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Access</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_access">
                </div>
              </div>

            </div>
            
            <div class="row mb-1">
              <div class="col-4 text-left">
                <div class="form-group text-left mb-1">
                  <label class="text-left">Otorisasi Level</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_add_otorisasiLevel">
                </div>
              </div>

            </div>

    </div>
  </div>
  <div id='FooterOptionAdd' class="modal-footer">
    <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit Add</button>
  </div>
  <div id='FooterOptionEdit' class="modal-footer">
    <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit Edit</button>
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
    url: "{!! url('berkasmenuloadall') !!}",
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
            <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODEMENU}')"><i class="bi bi-pen"></i></button>
            <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KODEMENU}')"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    <td>${item.KODEMENU}</td>
    <td>${item.Keterangan}</td>
    <td>${item.L0}</td>
    <td>${item.ACCESS}</td>
    <td>${item.OL}</td>
    </tr>`

  });

   let currentLength = $("#tabel_length_visual").val() ? Number($("#tabel_length_visual").val()) : 10;
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

$("#tabel_filter_visual").on("keyup", function () {
  $("#tabel").DataTable().search(this.value).draw();
});

function buttonAdd () {
  document.getElementById("input_add_kodeMenu").value = "";
  document.getElementById("input_add_keterangan").value = "";
  document.getElementById("input_add_L0").value = 0;
  document.getElementById("input_add_access").value = 0;
  document.getElementById("input_add_otorisasiLevel").value = 0;
  
  document.getElementById("input_add_kodeMenu").readOnly = false;

  document.getElementById('formTitle').innerHTML = 'Add'

      document.getElementById('FooterOptionAdd').hidden = false;
      document.getElementById('FooterOptionEdit').hidden = true;

  $("#form").modal('toggle')

}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('berkasmenuspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      KODEMENU : kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_kodeMenu").value = res[0].KODEMENU
      document.getElementById("input_add_keterangan").value = res[0].Keterangan
      document.getElementById("input_add_L0").value = res[0].L0;
      document.getElementById("input_add_access").value = res[0].ACCESS;
      document.getElementById("input_add_otorisasiLevel").value = res[0].OL;
      
      document.getElementById("input_add_kodeMenu").readOnly = true;

      document.getElementById('formTitle').innerHTML = 'Edit'

      document.getElementById('FooterOptionAdd').hidden = true;
      document.getElementById('FooterOptionEdit').hidden = false;

    }})
    $("#form").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Valas', 'Apakah yakin ingin menghapus Kode Menu ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('berkasmenuspdelete') !!}",
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
              alertify.success("Menu telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });

}

function submitEdit () {

  let _token = $("#_token").val();
  let KODEMENU = $("#input_add_kodeMenu").val();
  let Keterangan = $("#input_add_keterangan").val();
  let L0 = $("#input_add_L0").val();
  let ACCESS = $("#input_add_access").val();
  let OL = $("#input_add_otorisasiLevel").val();

  if (!KODEMENU) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!Keterangan) {
    alertify.warning("Keterangan harus diisi");
    return
  }

  if (!L0) {
    alertify.warning("L0 harus diisi");
    return
  }

  if (!ACCESS) {
    alertify.warning("Access harus diisi");
    return
  }
  
  if (!OL) {
    alertify.warning("OL harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('berkasmenuspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      KODEMENU,
      Keterangan,
      L0,
      ACCESS,
      OL
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Kode Menu telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();
  let KODEMENU = $("#input_add_kodeMenu").val();
  let Keterangan = $("#input_add_keterangan").val();
  let L0 = $("#input_add_L0").val();
  let ACCESS = $("#input_add_access").val();
  let OL = $("#input_add_otorisasiLevel").val();

  if (!KODEMENU) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!Keterangan) {
    alertify.warning("Keterangan harus diisi");
    return
  }

  if (!L0) {
    alertify.warning("L0 harus diisi");
    return
  }

  if (!ACCESS) {
    alertify.warning("Access harus diisi");
    return
  }
  
  if (!OL) {
    alertify.warning("OL harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('berkasmenuspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      KODEMENU,
      Keterangan,
      L0,
      ACCESS,
      OL
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Kode Menu telah ditambah");
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
