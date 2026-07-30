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
    <span class="sp-crumb-active">Costing</span>
  </div> --}}

  <div class="sp-page-head">
    <div>
      <h1>Master Costing</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Costing</button>
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
              <th scope="col">Kode Costing</th>
              <th scope="col">Nama Costing</th>
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
                  <label class="text-left">Kode Costing</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kode" placeholder="Kode Costing">
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama Cost</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_nama" placeholder="Nama Costing">
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
                  <label class="text-left">Kode Cost</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kode" placeholder="Kode Cost" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama Cost</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_nama" placeholder="Nama Cost">
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

<!-- start modal sub costing -->
<div class="modal fade"  id="formPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 900px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Akun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">

            <div class="row">
                <div class="col-1 text-left">
                  <div class="form-group text-left rowPerkiraan">
                    <label class="text-left">Perkiraan</label>
                  </div>
                  <div class="form-group text-left rowPerkiraanEdit">
                    <label class="text-left">Perkiraan</label>
                  </div>
                </div>
                <div class="col-3 ml-4">
                    <div class="input-group rowPerkiraan">
                        <input type="text" class="form-control" id="input_perkiraan">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraan()">+</button>
                        </div>
                    </div>
                    <div class="input-group rowPerkiraanEdit">
                        <input type="text" class="form-control" id="input_perkiraanEdit">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraan()">+</button>
                        </div>
                    </div>
                </div>
                <div class="col-1 text-left">
                  <div class="form-group text-left rowPerkiraan">
                      <button type="button" class="btn btn-primary btn-select" onclick="spAddPerkiraan()">Submit</button>
                  </div><div class="form-group text-left rowPerkiraanEdit">
                      <button type="button" class="btn btn-primary btn-select" onclick="spEditPerkiraan()">Submit</button>
                  </div>
                </div>
                <div class="col-1 text-left ms-2">
                  <div class="form-group text-left rowPerkiraan">
                      <button type="button" class="btn btn-danger btn-select" onclick="buttonTutupAddPerkiraan()">Batal</button>
                  </div>
                  <div class="form-group text-left rowPerkiraanEdit">
                      <button type="button" class="btn btn-danger btn-select" onclick="buttonTutupAddPerkiraan()">Batal</button>
                  </div>
                </div>
                

              <div class="col-5 d-flex justify-content-end">
                <div class="form-group">
                  <button type="button" class="btn btn-primary btn-lg" style="
                      height: 30px; 
                      padding: 4px 12px; 
                      border-radius: 20px; 
                      font-size: 0.75rem; 
                      font-weight: 600; 
                      text-transform: uppercase; 
                      transition: background-color 0.3s, box-shadow 0.3s;
                      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                      onclick="buttonAddPerkiraan()">
                    Add
                  </button>
                </div>
              </div>
            </div>

                <div class="col-12" style="overflow:auto;">
                  <div class="">

                        <table id="tabelPerkiraan" class="table table-bordered table-striped">
                          <thead id='theadCustom' class="text-center">
                            <tr>
                              <th scope="col">Actions</th>
                              <th scope="col">Perkiraan</th>
                              <th scope="col">Nama Perkiraan</th>

                            </tr>
                          </thead>


                          <tbody id="tabel_dataPerkiraan" class="text-left" >
                          </tbody>


                        </table>
                  </div>
                </div>


    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
  </div>
</div>
</div>
</div>
<!-- End modal sub costing-->

@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []
let kodeCostTemp = ''


function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();


  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastercostingloadall') !!}",
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
          <button data-toggle="tooltip" data-placement="top" title="Sub Cost" class="btn-action-sm btn-action-primary" type="button" onclick="buttonSubCost('${item.KodeCost}')"><i class="bi bi-list"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KodeCost}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KodeCost}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${item.KodeCost}</td>
    <td>${item.NamaCost}</td>
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

function buttonAdd () {

  $("#form").modal('toggle')

  document.getElementById('input_add_kode').value = ''
  document.getElementById('input_add_nama').value = ''

}

function buttonAddPerkiraan () {

  let elements = document.getElementsByClassName('rowPerkiraan');
    for (let i = 0; i < elements.length; i++) {
      elements[i].hidden = false;
    }

}

let urutTemp = 0

function buttonEditPerkiraan (kodeCost, urut) {

  urutTemp = urut

  let elements = document.getElementsByClassName('rowPerkiraan');
    for (let i = 0; i < elements.length; i++) {
      elements[i].hidden = true;
    }
  
  let elementsEdit = document.getElementsByClassName('rowPerkiraanEdit');
    for (let i = 0; i < elementsEdit.length; i++) {
      elementsEdit[i].hidden = false;
    }

  $.ajax({
    url: "{!! url('mastercostinglistdetailakunedit') !!}",
    type: "get",
    async: false,
    data:{
      kodeCost,
      urut
    },
    success: function(res){
      console.log(res)
      document.getElementById('input_perkiraanEdit').value = res[0].Perkiraan
    }

  })

}


function spAddPerkiraan () {

  let perkiraanTemp = document.getElementById('input_perkiraan').value

  console.log(perkiraanTemp, kodeCostTemp)

  $.ajax({
    url: "{!! url('mastercostingspperkiraan') !!}",
    type: "get",
    async: false,
    data:{
      Choice : 'I',
      KodeCost: kodeCostTemp,
      Urut : 0,
      Perkiraan :perkiraanTemp
    },
    success: function(res){
      console.log(res)
      document.getElementById('input_perkiraan').value = res[0].Perkiraan
      refreshTabelPerkiraan(kodeCostTemp)

    }

  })

}

function spEditPerkiraan () {

  let perkiraanTemp = document.getElementById('input_perkiraanEdit').value

  console.log(perkiraanTemp, kodeCostTemp, urutTemp)

  $.ajax({
    url: "{!! url('mastercostingspperkiraan') !!}",
    type: "get",
    async: false,
    data:{
      Choice : 'U',
      KodeCost: kodeCostTemp,
      Urut : urutTemp,
      Perkiraan :perkiraanTemp
    },
    success: function(res){
      console.log(res)
      document.getElementById('input_perkiraan').value = res[0].Perkiraan
      refreshTabelPerkiraan(kodeCostTemp)

    }

  })

}

function buttonDeletePerkiraan (KodeCost, Urut) {

  $.ajax({
    url: "{!! url('mastercostingspperkiraan') !!}",
    type: "get",
    async: false,
    data:{
      Choice : 'D',
      KodeCost,
      Urut,
      Perkiraan : ''
    },
    success: function(res){
      console.log(res)
      alertify.success('Perkiraan berhasil dihapus')
      refreshTabelPerkiraan(KodeCost);

    }

  })

}

function buttonTutupAddPerkiraan () {

  let elements = document.getElementsByClassName('rowPerkiraan');
    for (let i = 0; i < elements.length; i++) {
      elements[i].hidden = true;
    }
  let elementsEdit = document.getElementsByClassName('rowPerkiraanEdit');
    for (let i = 0; i < elementsEdit.length; i++) {
      elementsEdit[i].hidden = true;
    }

  document.getElementById('input_perkiraan').value = ''

}

function buttonSubCost (kodeCost) {

  kodeCostTemp = kodeCost

  let elements = document.getElementsByClassName('rowPerkiraan');
    for (let i = 0; i < elements.length; i++) {
      elements[i].hidden = true;
    }

  let elementsEdit = document.getElementsByClassName('rowPerkiraanEdit');
    for (let i = 0; i < elementsEdit.length; i++) {
      elementsEdit[i].hidden = true;
    }

  refreshTabelPerkiraan(kodeCost)

  
  $("#formPerkiraan").modal('toggle')

}

function refreshTabelPerkiraan(kodeCost){
  
  $('#tabelPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastercostinglistdetailakun') !!}",
    type: "get",
    async: false,
    data: {
      kodeCost
    },
    success: function(res){
      console.log(res)
      
      let rowTable = ""
      res.forEach((item, i) => {

        rowTable += `<tr>
          <td style="white-space:nowrap;" class='text-center'>
            <div class="action-buttons-wrap">
                <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEditPerkiraan('${item.KodeCost}', '${item.Urut}')"><i class="bi bi-pen"></i></button>
                <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDeletePerkiraan('${item.KodeCost}', '${item.Urut}')"><i class="bi bi-trash"></i></button>
            </div>
          </td>
        <td>${item.Perkiraan}</td>
        <td>${item.NamaPerkiraan}</td>
        </tr>`
      });

      document.getElementById('input_perkiraan').value = ''

        let elements = document.getElementsByClassName('rowPerkiraan');
        for (let i = 0; i < elements.length; i++) {
          elements[i].hidden = true;
        }
        
        let elementsEdit = document.getElementsByClassName('rowPerkiraanEdit');
          for (let i = 0; i < elementsEdit.length; i++) {
            elementsEdit[i].hidden = true;
          }

      document.getElementById("tabel_dataPerkiraan").innerHTML = rowTable
      $("#tabelPerkiraan").DataTable({
        "lengthChange": false,
          "paging": true,
        });


    }

  });
}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastercostingspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].KodeCost
      document.getElementById("input_edit_nama").value = res[0].NamaCost

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Cost', 'Apakah yakin ingin menghapus Kode Cost ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastercostingspdelete') !!}",
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
              alertify.success("Data Cost telah dihapus");

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
  console.log(kode,nama)
  if (!kode) {
    alertify.warning("Kode  harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastercostingspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Cost telah diedit");
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

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastercostingspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Costing telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

window.onload = function(){
  loadAll();
};

function buttonPerkiraan () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('mastercostinglistperkiraan') !!}",
    type: "get",
    async: false,
    data: {
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
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonSelectPerkiraan('${item.Perkiraan}')"><i class="bi bi-plus"></i></button>
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
  document.getElementById("namaModalOpen").innerHTML = 'Perkiraan'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectPerkiraan(perkiraan){
  document.getElementById('input_perkiraan').value = perkiraan;
  document.getElementById('input_perkiraanEdit').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

</script>




@endsection
