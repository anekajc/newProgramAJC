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
    <span class="sp-crumb-active">Online/Offline</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Online/Offline</h1>
    </div>
    <button class="btn btn-primary" onclick="buttonAdd()">+ Online/Offline</button>
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
                    <th scope="col">User ID</th>
                    <th scope="col">Nama Lengkap</th>
                    <th scope="col">Status</th>
                  </tr>
                </thead>
                <tbody id="tabel_data" class="text-right">
              </tbody>
              </table>
            </div>
        </div>

</div>

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
                  <label class="text-left">USER ID</label>
                </div>
              </div>
              <div class="col-8 mb-1">
                <div class="form-group mb-1">
                  <input type="text" class="form-control" id="input_edit_kode" readonly>
                </div>
              </div>

            </div>
              <div class="row mb-1">
                  <div class="col-4 text-left">
                      <div class="form-group text-left mb-1">
                          <label class="text-left">Status</label>
                      </div>
                  </div>
                  <div class="col-8 mb-1">
                      <div class="form-group mb-1">
                          <select class="form-control" id="input_edit_status">
                              <option value="0">Offline</option>
                              <option value="1">Online</option>
                          </select>
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
  
  document.getElementById('breadcrumb').innerHTML = "Online / Offline"
  document.getElementById('AddVisibility').hidden = true;

  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('berkasstatusloadall') !!}",
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
            <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.USERID}')"><i class="bi bi-pen"></i></button>
        </div>
      </td>
    <td>${item.USERID}</td>
    <td>${item.FullName}</td>
    <td>
      ${
          item.STATUS == 0
              ? '<span class="sp-badge is-supervisor">Offline</span>'
              : '<span class="sp-badge is-user">Online</span>'
      }
    </td>
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

$("#tabel_filter_visual").on("keyup", function () {
  $("#tabel").DataTable().search(this.value).draw();
});

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('berkasstatusspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].USERID
      document.getElementById("input_edit_status").value = res[0].STATUS
      
      formatNumber(document.getElementById("input_edit_kurs"))
      

    }})
    $("#formEdit").modal('toggle')
}

function submitEdit () {

  let _token = $("#_token").val();
  let kode = $("#input_edit_kode").val();
  let status = $("#input_edit_status").val();

  $.ajax({
    url: "{!! url('berkasstatusspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      status
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Status telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

window.onload = function(){
  loadAll();
};

</script>



@endsection
