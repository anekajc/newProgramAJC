@extends('newmaster')
@section('buttons')

@endsection


@section('content')
@include('master/partials/sidebarPosting')

 <link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}?v={{ filemtime(public_path('css/tableMaster2.css')) }}">


<style>
  .sp-page-wrap {
    margin-right: 280px; /* clears the fixed posting-sidebar so content doesn't run under it */
  }

  .sp-page-wrap .sp-page-head,
  .sp-page-wrap #contentContainer {
    max-width: 900px;   /* stops the table from stretching edge-to-edge */
    margin-left: auto;
    margin-right: auto;
  }

  @media (max-width: 768px) {
    .sp-page-wrap {
      margin-right: 0; /* posting-sidebar slides off-screen on mobile, no need to reserve space */
    }
  }
</style>

<div class="sp-page-wrap">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Satuan</span>
  </div> --}}

  <div class="sp-page-head">
    <div>
      <h1>Master Posting Aktiva</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Posting Aktiva</button>
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
            <th scope="col">Perkiraan</th>
            <th scope="col">Keterangan</th>
            <th scope="col">Persen</th>
            <th scope="col">Metode</th>
            <th scope="col">Perkiraan Akumulasi</th>
            <th scope="col">Perkiraan Biaya 1</th>
            <th scope="col">Persen Biaya 1</th>
            <th scope="col">Perkiraan Biaya 2</th>
            <th scope="col">Persen Biaya 2</th>
            </tr>
          </thead>
          <tbody id="tabel_data" class="text-right">
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div>

<!-- start modal add posting aktiva-->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1000px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Posting Aktiva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

          <div class="row g-3 align-items-end">
            <div class="col-md-2">
              <label class="form-label">Perkiraan</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_perkiraan">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraan()">+</button>
                </div>
              </div>
            </div>

            <div class="col-md-2">
              <label class="form-label hover-tooltip" data-tooltip='Biaya Penyusutan 1'>Biaya Penyusutan 1</label>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_biayaPenyusutan1">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraanBP1()">+</button>
                </div>
              </div>
            </div>
            <div class="col-md-1">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_persenBiaya1" placeholder='(%)'>
              </div>
            </div>
          </div>

          <div class="row g-3 align-items-end mt-2">
            <div class="col-md-2">
              <label class="form-label">Akumulasi Penyusutan</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_akm">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary btn-select" onclick="buttonAkumulasi()">+</button>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <label class="form-label hover-tooltip" data-tooltip='Biaya Penyusutan 2'>Biaya Penyusutan 2</label>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_biayaPenyusutan2">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraanBP2()">+</button>
                </div>
              </div>
            </div>
            <div class="col-md-1">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_persenBiaya2" placeholder='(%)'>
              </div>
            </div>
          </div>

          <div class="row g-3 align-items-end mt-2">
            <div class="col-md-2">
              <label class="form-label">Persen Susut</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_persenSusut" placeholder='(%)'>
              </div>
            </div>
            <div class="col-md-2">
              <label class="form-label">Metode Penyusutan</label>
            </div>
            <div class="col-md-2">
              <select name="MedPenyu" class='form-control' id="input_add_metodePenyusutan">
                <option value="L">[L]urus</option>
                <option value="M">[M]enurun</option>
                <option value="P">[P]ajak</option>
              </select>
            </div>

            <div class="col-md-2 d-flex align-items-center gap-2">
              <input type="checkbox" id="input_add_uangMuka" value="">
              <label for="input_add_uangMuka" class="form-label mb-0">Uang Muka</label>
            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer">
         
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit Add</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add posting aktiva-->

<!-- start modal add posting aktiva-->
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1000px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Posting Aktiva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

          <div class="row g-3 align-items-end">
            <div class="col-md-2">
              <label class="form-label">Perkiraan</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_perkiraan">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraan()">+</button>
                </div>
              </div>
            </div>

            <div class="col-md-2">
              <label class="form-label hover-tooltip" data-tooltip='Biaya Penyusutan 1'>Biaya Penyusutan 1</label>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_biayaPenyusutan1">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraanBP1()">+</button>
                </div>
              </div>
            </div>
            <div class="col-md-1">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_persenBiaya1" placeholder='(%)'>
              </div>
            </div>
          </div>

          <div class="row g-3 align-items-end mt-2">
            <div class="col-md-2">
              <label class="form-label">Akumulasi Penyusutan</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_akm">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary btn-select" onclick="buttonAkumulasi()">+</button>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <label class="form-label hover-tooltip" data-tooltip='Biaya Penyusutan 2'>Biaya Penyusutan 2</label>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_biayaPenyusutan2">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraanBP2()">+</button>
                </div>
              </div>
            </div>
            <div class="col-md-1">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_persenBiaya2" placeholder='(%)'>
              </div>
            </div>
          </div>

          <div class="row g-3 align-items-end mt-2">
            <div class="col-md-2">
              <label class="form-label">Persen Susut</label>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_persenSusut" placeholder='(%)'>
              </div>
            </div>
            <div class="col-md-2">
              <label class="form-label">Metode Penyusutan</label>
            </div>
            <div class="col-md-2">
              <select name="MedPenyu" class='form-control' id="input_edit_metodePenyusutan">
                <option value="L">[L]urus</option>
                <option value="M">[M]enurun</option>
                <option value="P">[P]ajak</option>
              </select>
            </div>

            <div class="col-md-2 d-flex align-items-center gap-2">
              <input type="checkbox" id="input_edit_uangMuka" value="">
              <label for="input_edit_uangMuka" class="form-label mb-0">Uang Muka</label>
            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer">
         
        <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit Add</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add posting aktiva-->

@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();

  document.getElementById('judulPosting').innerHTML = 'Master Posting Aktiva'

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingaktivaloadall') !!}",
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
          <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.Perkiraan}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.Perkiraan}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${item.Perkiraan}</td>
    <td>${item.Keterangan}</td>
    <td>${item.Persen}</td>
    <td>${item.Tipe}</td>
    <td>${item.Akumulasi}</td>
    <td>${item.Biaya1}</td>
    <td>${item.PersenBiaya1}</td>
    <td>${item.Biaya2}</td>
    <td>${item.PersenBiaya2}</td>
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
  $("#form").modal('toggle')
}

function buttonEdit (kode) {

  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastersetpostingaktivaspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_perkiraan").value = res[0].Perkiraan
      document.getElementById("input_edit_akm").value = res[0].Akumulasi
      document.getElementById("input_edit_biayaPenyusutan1").value = res[0].Biaya1
      document.getElementById("input_edit_persenBiaya1").value = res[0].PersenBiaya1
      document.getElementById("input_edit_biayaPenyusutan2").value = res[0].Biaya2
      document.getElementById("input_edit_persenBiaya2").value = res[0].PersenBiaya2
      document.getElementById("input_edit_persenSusut").value = res[0].Persen
      document.getElementById("input_edit_metodePenyusutan").value = res[0].Tipe

      if (Number(res[0].IsUM)) {
        document.getElementById("input_edit_uangMuka").checked = true
      } else {
        document.getElementById("input_edit_uangMuka").checked = false
      }

      perkiraanTemp = res[0].Perkiraan

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Perkiraan', 'Apakah yakin ingin menghapus Perkiraan Posting Aktiva ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersetpostingaktivaspdelete') !!}",
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
              alertify.success("Perkiraan Posting Aktiva telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

let perkiraanTemp = ''

function submitEdit () {

  let _token = $("#_token").val();

  let perkiraan = $("#input_edit_perkiraan").val();
  let akm = $("#input_edit_akm").val();
  let bp1 = $("#input_edit_biayaPenyusutan1").val();
  let persenbp1 = $("#input_edit_persenBiaya1").val();
  let bp2 = $("#input_edit_biayaPenyusutan2").val();
  let persenbp2 = $("#input_edit_persenBiaya2").val();
  let persenSusut = $("#input_edit_persenSusut").val();
  let metodePenyusutan = $("#input_edit_metodePenyusutan").val();

  let uangMuka = 0
  if (document.getElementById("input_edit_uangMuka").checked) {
    uangMuka = 1
  }

  $.ajax({
    url: "{!! url('mastersetpostingaktivaspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      perkiraan, akm, bp1, persenbp1, bp2, persenbp2, persenSusut, metodePenyusutan, uangMuka,
      kodeLama: perkiraanTemp
    },
    success: function(res) {
      if (res != 1) {
        alertify.warning(res);
      }  
      else 
      {
        console.log(res ,'!')
        alertify.success("Data Posting Kas telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }
    }})
}

function submitAdd () {

  let _token = $("#_token").val();
  let perkiraan = $("#input_add_perkiraan").val();
  let akm = $("#input_add_akm").val();
  let bp1 = $("#input_add_biayaPenyusutan1").val();
  let persenbp1 = $("#input_add_persenBiaya1").val();
  let bp2 = $("#input_add_biayaPenyusutan2").val();
  let persenbp2 = $("#input_add_persenBiaya2").val();
  let persenSusut = $("#input_add_persenSusut").val();
  let metodePenyusutan = $("#input_add_metodePenyusutan").val();

  let uangMuka = 0
  if (document.getElementById("input_add_uangMuka").checked) {
    uangMuka = 1
  }

  if (!persenSusut){
    alertify.warning('Persen Susut tidak boleh kosong.')
    return;
  }

  if (!bp2){
    bp2 = '-'
  }

  if (!persenbp2){
    persenbp2 = 0
  }
  
  $.ajax({
    url: "{!! url('mastersetpostingaktivaspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      perkiraan, akm, bp1, persenbp1, bp2, persenbp2, persenSusut, metodePenyusutan, uangMuka
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Posting Aktiva telah ditambah");
        loadAll()
        $("#form").modal('hide')
      }

    }})

}

window.onload = function() {
loadAll();
}

function buttonPerkiraan () {

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
    }

  $.ajax({
    url: "{!! url('mastersetpostingaktivaloadperkiraan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
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
        <button class="btn-action-md btn-action-primary" type="button" onclick="buttonSelectPerkiraan('${item.Perkiraan}')"><i class="bi bi-plus-square"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
  </tr>
  `
  
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Select Perkiraan'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectPerkiraan(perkiraan){
  document.getElementById('input_add_perkiraan').value = perkiraan;
  document.getElementById('input_edit_perkiraan').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonAkumulasi () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('mastersetpostingaktivaloadakumulasi') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
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
        <button class="btn-action-md btn-action-primary" type="button" onclick="buttonSelectAkumulasi('${item.Perkiraan}')"><i class="bi bi-plus-square"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Akumulasi'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectAkumulasi(perkiraan){
  document.getElementById('input_add_akm').value = perkiraan;
  document.getElementById('input_edit_akm').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonPerkiraanBP1 () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('mastersetpostingaktivaloadperkiraanpenyusutan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
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
        <button class="btn-action-md btn-action-primary" type="button" onclick="buttonSelectBP1('${item.Perkiraan}')"><i class="bi bi-plus-square"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Perkiraan Biaya Penyusutan 1'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectBP1(perkiraan){
  document.getElementById('input_add_biayaPenyusutan1').value = perkiraan;
  document.getElementById('input_edit_biayaPenyusutan1').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonPerkiraanBP2 () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('mastersetpostingaktivaloadperkiraanpenyusutan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
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
        <button class="btn-action-md btn-action-primary" type="button" onclick="buttonSelectBP2('${item.Perkiraan}')"><i class="bi bi-plus-square"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Perkiraan Biaya Penyusutan 2'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectBP2(perkiraan){
  document.getElementById('input_add_biayaPenyusutan2').value = perkiraan;
  document.getElementById('input_edit_biayaPenyusutan2').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

</script>




@endsection
