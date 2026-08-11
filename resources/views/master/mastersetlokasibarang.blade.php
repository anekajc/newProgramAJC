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
    <span class="sp-crumb-active">Set Lokasi Barang</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Set Lokasi Barang</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Set Lokasi Barang</button>
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
            <th scope="col">Kode Barang</th>
            <th scope="col">Nama Barang</th>
            <th scope="col">Merk</th>
            <th scope="col">Kode Lokasi</th>
            <th scope="col">Nama Lokasi</th>
            <th scope="col">Sat 1</th>
            <th scope="col">Sat 2</th>
            <th scope="col">Isi 2</th>
            <th scope="col">Sat 3</th>
            <th scope="col">Isi 3</th>
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
                    <label class="text-left">Kode Lokasi</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_kode" placeholder="Kode Lokasi">
                  </div>
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-4 text-left">
                  <div class="form-group text-left">
                    <label class="text-left">Keterangan</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_nama" placeholder="Keterangan">
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
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

            <div class="row mt-2">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Lokasi Barang</label>
                </div>
              </div>
              <div class="col-8">
                  <div class="input-group">
                      <input type="text" class="form-control" id="input_edit_lokasiBarang" placeholder="Lokasi Barang">
                      <div class="input-group-append">
                          <button type="button" class="btn btn-primary btn-select" onclick="buttonLokasiBarang()">+</button>
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

@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();

  document.getElementById('breadcrumb').innerHTML = "Master Set Lokasi Barang"
  document.getElementById('AddVisibility').hidden = true;

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetlokasibarangloadall') !!}",
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
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODEBRG}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="History Lokasi Barang" class="btn-action-sm btn-action-primary" type="button" onclick="buttonHistory('${item.KODEBRG}')"><i class="bi bi-clock-history"></i></button>
      </div>
    </td>
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td>${item.namaMerk}</td>
    <td>${item.Mlokasi}</td>
    <td>${item.KetLokasi}</td>
    <td class='text-right'>${item.SAT1}</td>
    <td>${item.SAT2}</td>
    <td class='text-right'>${item.ISI2}</td>
    <td>${item.SAT3}</td>
    <td class='text-right'>${item.ISI3}</td>
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

let kodeBarangTemp = ''

function buttonEdit (kode) {

  kodeBarangTemp = kode
  console.log(kodeBarangTemp)

  $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Valas', 'Apakah yakin ingin menghapus Kode Valas ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterlokasibarangspdelete') !!}",
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
              alertify.success("Kode Lokasi Barang telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function submitEdit () {

  let _token = $("#_token").val();
  let lokasiBarang = $("#input_edit_lokasiBarang").val();

  if (!lokasiBarang) {
    alertify.warning("Lokasi Barang harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterSetLokasiBarangSubmitEdit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      lokasiBarang,
      kodeBarang : kodeBarangTemp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Lokasi berhasil disimpan.");
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
    url: "{!! url('masterlokasibarangspadd') !!}",
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
        alertify.success("Data Valas telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

window.onload = function(){
  loadAll();
}

function buttonHistory (kodeBarang) {
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSetLokasiBarangLoadHistory') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kodeBarang
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
      <td>${item.lokasi}</td>
      <td>${item.tgl}</td>
      <td>${item.iduser}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Lokasi</th>
    <th scope="col">Tanggal</th>
    <th scope="col">User</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'History Barang'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonLokasiBarang () {
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSetLokasiBarangLoadLokasiBarang') !!}",
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
        <button class="btn-action-md btn-action-primary" type="button" onclick="buttonSelectLokasi('${item.KODELOKASI}')"><i class="bi bi-plus-square"></i></button>
      </td>
      <td>${item.KODELOKASI}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Action</th>
    <th scope="col">LOkasi</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Lokasi Barang'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectLokasi(kodeLokasi){

  document.getElementById('input_edit_lokasiBarang').value = kodeLokasi
  
  $("#formModalOpen").modal('toggle')

}

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
         
      </div>
    </div>
  </div>
</div>
<!-- End modal select add akumulasi penyusutan-->

@endsection
