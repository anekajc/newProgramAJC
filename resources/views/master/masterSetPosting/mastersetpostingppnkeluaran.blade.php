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
      <h1>Master Posting PPN Keluaran</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Posting PPN Keluaran</button>
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
            </tr>
          </thead>
          <tbody id="tabel_data" class="text-right">
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div>



@include('master.masterSetPosting.mastersetpostingmastertable');
@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();

  document.getElementById('judulPosting').innerHTML = 'Master Posting PPN Keluaran'

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingppnkeluaranloadall') !!}",
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
    <td>${item.keterangan}</td>
    </tr>`
  });
  
  if(dataRefresh.length == 1){
  document.getElementById('divAdd').hidden = true
  } else {
    document.getElementById('divAdd').hidden = false
  }

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
  document.getElementById('judulTipeModal').innerHTML =  'Add'
  document.getElementById('input_kode').value = ''
  
  isiButton = `
         
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit Add</button>
        `

  document.getElementById('buttonTipeModal').innerHTML = isiButton;

  $("#form").modal('toggle')

}

function buttonEdit (kode) {

  document.getElementById('judulTipeModal').innerHTML =  'Edit'

  isiButton = `
         
        <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit Edit</button>
        `

  document.getElementById('buttonTipeModal').innerHTML = isiButton;

  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastersetpostingppnkeluaranspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_kode").value = res[0].Perkiraan
      perkiraanTemp = res[0].Perkiraan

    }})
    $("#form").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Perkiraan', 'Apakah yakin ingin menghapus Perkiraan Posting PPN Keluaran ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersetpostingppnkeluaranspdelete') !!}",
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
              alertify.success("Perkiraan Posting Kas telah dihapus");

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
  let kode = $("#input_kode").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastersetpostingppnkeluaranspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
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
        $("#form").modal('toggle')
      }
    }})
}

function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_kode").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastersetpostingppnkeluaranspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Posting Kas telah ditambah");
        loadAll()
        $("#form").modal('hide')
      }

    }})

  // console.log(kodearea, namaarea)
}

function backToSetPosting() {
  window.location.href = "{{ ('mastersetposting') }}";
}

function buttonSelectPerkiraan () {
  loadSelectPerkiraan()
  $("#formSelectPerkiraan").modal('toggle')
}

function loadSelectPerkiraan() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelAktivaSelectPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingppnkeluaranloadperkiraan') !!}",
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
        <button class="btn-action-md btn-action-primary" type="button" onclick="buttonPilihPerkiraan('${item.Perkiraan}')"><i class="bi bi-plus-square"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAktivaSelectPerkiraan").innerHTML = rowTable;
  $("#tabelAktivaSelectPerkiraan").DataTable({
    "lengthChange": true,
    "paging": true,
  });
}

function buttonPilihPerkiraan(selectedPerkiraan) {
  $("#input_kode").val(selectedPerkiraan);
  $("#formSelectPerkiraan").modal("hide");

}

function buttonEditSelectPerkiraan () {
  loadEditSelectPerkiraan()
  $("#formEditSelectPerkiraan").modal('toggle')
}

function loadEditSelectPerkiraan() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelEditAktivaSelectPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingppnkeluaranloadperkiraan') !!}",
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
        <button class="btn-action-md btn-action-primary" type="button" onclick="buttonEditPilihPerkiraan('${item.Perkiraan}')"><i class="bi bi-plus-square"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataEditAktivaSelectPerkiraan").innerHTML = rowTable;
  $("#tabelEditAktivaSelectPerkiraan").DataTable({
    "lengthChange": true,
    "paging": true,
  });
}

function buttonEditPilihPerkiraan(selectedPerkiraan) {
  $("#input_edit_kode").val(selectedPerkiraan);
  $("#formEditSelectPerkiraan").modal("hide");

}

window.onload = function() {
loadAll();
}

</script>




@endsection
