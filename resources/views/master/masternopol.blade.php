@extends('newmaster')
@section('buttons')

@endsection
@section('content')


<?php
function getStatus($data) {
    return $data == 1 ? "Aktif" : "Non-Aktif";
}
?>

<link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">No. Pol.</span>
  </div> --}}

  <div class="sp-page-head">
    <div>
      <h1>Master No. Pol.</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add No. Pol.</button>
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
                    <th scope="col">No. Pol.</th>
                    <th scope="col">Aktif / Non-Aktif</th>
                    <th scope="col">Nama Cost</th>
                  </tr>
                </thead>
                <tbody id="tabel_data" class="text-right">
              </tbody>
              </table>
            </div>
        </div>

</div>

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                  <label class="text-left">No. Pol.</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kode" placeholder="No. Pol.">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Status Aktif</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select class="form-control" id="input_add_nama">
                    <option value=1>Aktif</option>
                    <option value=0>Non-Aktif</option>
                    <select>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode Cost</label>
                </div>
              </div>
              <div class="col-8 mt-2">
              
              <div class="input-group">
                  <input type="text" class="form-control" id="input_add_perkiraan" placeholder="Kode Cost" readonly>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" onclick="buttonSelectPerkiraan()">+</button>
                  </div>
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
                  <label class="text-left">No. Pol.</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kode" placeholder="No. Pol." disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Status Aktif</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select class="form-control" id="input_edit_nama">
                    <option value=1>Aktif</option>
                    <option value=0>Non-Aktif</option>
                    <select>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode Cost</label>
                </div>
              </div>

              <div class="col-8 mt-2">
              <div class="input-group">
                  <input type="text" class="form-control" id="input_edit_perkiraan" placeholder="Kode Cost" readonly>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" onclick="buttonSelectPerkiraan()">+</button>
                  </div>
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


<!-- start modal perkiraan add -->
<div class="modal fade"  id="formPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1000px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Perkiraan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

          <div class="col-12" style="overflow:auto;">
            <div class="">

                  <table id="tabelPerkiraan" class="table table-bordered table-striped"  >
                    <thead id='theadCustom' class="text-center">
                      <tr>
                        <th scope="col">Actions</th>
                        <th scope="col">Kode Cost</th>
                        <th scope="col">Nama Cost</th>

                      </tr>
                    </thead>


                    <tbody id="tabelData_perkiraan" class="text-left" >
                      @for ($i = 0; $i < count($listData); $i++)
                      <tr >

                        <td></td>
                        <td></td>


                          <td class="text-center">
                          </td>
                    </tr>
                    @endfor
                    </tbody>


                  </table>
            </div>
          </div>


    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
  </div>
</div>
</div>
</div>
<!-- End modal perkiraan add -->

@endsection

@section('js')

<script src="{{ asset('js/masterTable.js') }}"></script>

<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masternopolloadall') !!}",
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
        <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODEKEND}')"><i class="bi bi-pen"></i></button>
        <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KODEKEND}')"><i class="bi bi-trash"></i></button>
    </div>
        </td>
    <td>${item.KODEKEND}</td>
    <td>${item.IsAktif == 1 ? '<span class="sp-badge is-admin">Aktif</span>' : '<span class="sp-badge is-supervisor">Tidak Aktif</span>'}</td>
    <td>${item.KodeCost}</td>
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

  document.getElementById('input_add_perkiraan').value = ''
  $("#form").modal('toggle')

}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masternopolspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].KODEKEND
      document.getElementById("input_edit_nama").value = res[0].IsAktif
      document.getElementById("input_edit_perkiraan").value = res[0].KodeCost

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Area', 'Apakah yakin ingin menghapus Data ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masternopolspdelete') !!}",
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
              alertify.success("Data Biaya telah dihapus");

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
  let kodecost = $("#input_edit_perkiraan").val();

  console.log(kode,nama,kodecost)
  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Keterangan harus diisi");
    return
  }

  if (!kodecost) {
    alertify.warning("Perkiraan harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masternopolspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      kodecost
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Biaya telah diedit");
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
  let kodecost = $("#input_add_perkiraan").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Keterangan harus diisi");
    return
  }

  if (!kodecost) {
    alertify.warning("Kode Cost harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masternopolspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      kodecost
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Kendaraan telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

function buttonSelectPerkiraan(kode) {
  console.log(kode);
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masternopolselectperkiraan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kode: kode // Make sure 'kode' is passed correctly
    },
    success: function (dataRefresh) {
      let rowTable = "";

      dataRefresh.forEach((item, i) => {
        rowTable += `<tr>
          <td class="text-center">
            <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilihPerkiraan('${item.kodecost}')"><i class='bi bi-plus'></i></button>
          </td>
          <td>${item.kodecost}</td>
          <td>${item.namacost}</td>
        </tr>`;
      });

      $("#tabelData_perkiraan").html(rowTable); // Update the table content

      $("#tabel_perkiraan").DataTable({
        "lengthChange": false,
        "paging": false,
      });

      // Close the modal if needed
      $("#formPerkiraan").modal("toggle");
    }
  });
}

function buttonPilihPerkiraan(selectedPerkiraan) {
  // Get the input element by ID
  document.getElementById("input_add_perkiraan").value = selectedPerkiraan
  document.getElementById("input_edit_perkiraan").value = selectedPerkiraan

  $("#formPerkiraan").modal("hide");
}

window.onload = function(){
  loadAll();
};

</script>


@endsection
