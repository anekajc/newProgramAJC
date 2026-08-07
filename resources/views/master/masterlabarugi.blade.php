@extends('newmaster')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">


 <link rel="stylesheet" href="{{ asset('css/tableMaster2.css') }}?v={{ filemtime(public_path('css/tableMaster2.css')) }}">


  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Laba Rugi</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Laba Rugi</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Laba Rugi</button>
  </div> --}}

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

    <style>
    .sp-length-wrap {
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
    }    
    
    .sp-filter-wrap {
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
    }

    .sp-length-wrap label {
      margin: 0; /* stops default label margin from pushing the select down/over */
    }

    .sp-length-wrap select {
      width: auto; /* stops form-select from stretching full-width and forcing a wrap */
    }

    .sp-toolbar {
      display: flex;
      flex-wrap: wrap; /* lets controls drop to a new line on narrow screens instead of overflowing */
      align-items: center;
      row-gap: 10px;
      column-gap: 12px; /* controls the tight spacing between search and the dropdown next to it */
    }

    .sp-filter-wrap select {
      width: auto;
      min-width: 150px; /* keeps "Hutang Usaha (21201)" from getting clipped */
    }

    .sp-length-wrap {
      margin-left: auto; /* pushes Tampilkan to the far right, away from the search+filter group */
    }
  </style>

  <div class="sp-toolbar">
    <div class="sp-search-wrap">
      <i class="bi bi-search sp-search-icon"></i>
      <input type="text" id="tabel_filter_visual" placeholder="Cari user...">
    </div>

    <div class="sp-filter-wrap">
      <label for="tabel_length_visual">Tampilkan</label>
      <select id="tabel_length_visual" class="form-select form-select-sm" style='width:40px;'>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
        <option value="-1">Semua</option>
      </select>
    </div>

    <div class="sp-filter-wrap">
      <label for="jenisDevisi">Devisi :</label>
      <select name="devisi" id="jenisDevisi" class="form-control" onChange='loadAll()'>
        @foreach ($listDataDevisi as $Devisi)
            <option value="{{ $Devisi->Devisi }}">{{ $Devisi->Devisi }} - {{ $Devisi->NamaDevisi }}</option>
        @endforeach
      </select>
    </div>
    <div class="sp-filter-wrap">
      <label for="jenisLaporan">Laporan :</label>
      <select name="perkiraanCustomer" id="jenisLaporan" class="form-control" onChange='loadAll()'>
        <option value='0'> Laba Rugi</option>
        <option value='1'> HPP </option>
      </select>
    </div>

    <div class="sp-length-wrap">
      <button id='AddVisibility' class="btn btn-action-primary" onclick="buttonAdd()">+ Add</button>
    </div>
  </div>

          <div class="table-outer">
            <div class="table-wrap">
              <table class="tb" id="tabel">
                <thead>
                  <tr>
                    <th scope="col">Actions</th>
                    <th scope="col">Nomor</th>
                    <th scope="col">Perkiraan</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">Grup</th>
                    <th scope="col">Tipe</th>
                    <th scope="col">Tanda</th>
                    <th scope="col">Persen</th>
                    <th scope="col">Jumlah</th>
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
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 900px">
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

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nomor</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_nomor" placeholder="Nomor">
                </div>
              </div>

              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Perkiraan</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                  <input type="text" class="form-control" id="input_add_perkiraan" placeholder="Perkiraan">
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraan()">+</button>
                  </div>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_keterangan" placeholder="Keterangan">
                </div>
              </div>

            </div>

            <div class="row mt-2">

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tipe</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <select class='form-control' id='input_add_tipe'>
                    <option value='' selected disabled></option>
                    <option value=1>1 - Mutasi Debet</option>
                    <option value=2>2 - Mutasi Kredit</option>
                    <option value=3>3 - Koreksi Debet</option>
                    <option value=4>4 - Koreksi Kredit</option>
                    <option value='A'>A - Saldo Awal</option>
                    <option value='K'>K - Saldo Akhir</option>
                    <option value='Z'>Z - Persediaan Akhir</option>
                    <option value='M'>M -Mutasi</option>
                    <option value='H'>H - HPP</option>
                  </select>
                </div>
              </div>

              
              <div class="col-1 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanda</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <select class='form-control' id='input_add_tanda'>
                    <option value='' selected disabled></option>
                    <option value='+'>+</option>
                    <option value='-'>-</option>
                  </select>
                </div>
              </div>
              
              <div class="col-1 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Jumlah</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <select class='form-control' id='input_add_jumlah'>
                    <option value='' selected disabled></option>
                    <option value='T'>T - Total</option>
                    <option value='G'>G - Group</option>
                    <option value='S'>S - Sub Group</option>
                  </select>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Persentasi</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_persentasi" placeholder="Persentasi">
                </div>
              </div>

              <div class="col-1 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tampil</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <select class='form-control' id='input_add_tampil'>
                    <option value='Y'>Y - Ya</option>
                    <option value='T'>T - Tidak</option>
                  </select>
                </div>
              </div>

              <div class="col-1 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Group</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_group" placeholder="Group">
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
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 900px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nomor</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_nomor" placeholder="Nomor" disabled>
                </div>
              </div>

              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Perkiraan</label>
                </div>
              </div>
              <div class="col-4">
                <div class="input-group">
                  <input type="text" class="form-control" id="input_edit_perkiraan" placeholder="Perkiraan">
                  <div class="input-group-append">
                      <button type="button" class="btn btn-primary btn-select" onclick="buttonPerkiraan()">+</button>
                  </div>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_keterangan" placeholder="Keterangan">
                </div>
              </div>

            </div>

            <div class="row">

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tipe</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <select class='form-control' id='input_edit_tipe'>
                    <option value='' selected disabled></option>
                    <option value=1>1 - Mutasi Debet</option>
                    <option value=2>2 - Mutasi Kredit</option>
                    <option value=3>3 - Koreksi Debet</option>
                    <option value=4>4 - Koreksi Kredit</option>
                    <option value='A'>A - Saldo Awal</option>
                    <option value='K'>K - Saldo Akhir</option>
                    <option value='Z'>Z - Persediaan Akhir</option>
                    <option value='M'>M -Mutasi</option>
                    <option value='H'>H - HPP</option>
                  </select>
                </div>
              </div>

              
              <div class="col-1 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanda</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <select class='form-control' id='input_edit_tanda'>
                    <option value='' selected disabled></option>
                    <option value='+'>+</option>
                    <option value='-'>-</option>
                  </select>
                </div>
              </div>
              
              <div class="col-1 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Jumlah</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <select class='form-control' id='input_edit_jumlah'>
                    <option value='' selected disabled></option>
                    <option value='T'>T - Total</option>
                    <option value='G'>G - Group</option>
                    <option value='S'>S - Sub Group</option>
                  </select>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Persentasi</label>
                </div>
              </div>
              <div class="col-2">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_persentasi" placeholder="Persentasi">
                </div>
              </div>

              <div class="col-1 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tampil</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <select class='form-control' id='input_edit_tampil'>
                    <option value='Y'>Y - Ya</option>
                    <option value='T'>T - Tidak</option>
                  </select>
                </div>
              </div>

              <div class="col-1 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Group</label>
                </div>
              </div>
              <div class="col-3">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_group" placeholder="Group">
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
  console.log('asd')
  let _token = $("#_token").val();

  document.getElementById('breadcrumb').innerHTML = "Master Laba Rugi"
  
  let filterDevisi = $("#jenisDevisi").val();
  let filterLaporan = $("#jenisLaporan").val();

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masterlabarugiloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      filterDevisi,
      filterLaporan
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""

    rowTable += `<tr>
                      
    <td class="text-center">
      <button class="btn btn-success btn-sm hover-tooltip" data-tooltip='Edit' type="button" onclick="buttonEdit('${item.Nomor}')"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm hover-tooltip" data-tooltip='Delete' type="button" onclick="buttonDelete('${item.Nomor}')"><i class="bi bi-trash"></i></button>
    </td>
    <td>${item.Nomor}</td>
    <td>${item.Perkiraan}</td>
    <td>${item.Keterangan}</td>
    <td>${item.Grup}</td>
    <td>${item.Tipe}</td>
    <td>${item.Tanda}</td>
    <td>${item.Persen}</td>
    <td>${item.Jumlah}</td>
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

  document.getElementById('input_add_nomor').value = ''
  document.getElementById('input_add_perkiraan').value = ''
  document.getElementById('input_add_keterangan').value = ''
  document.getElementById('input_add_tipe').value = ''
  document.getElementById('input_add_tanda').value = ''
  document.getElementById('input_add_jumlah').value = ''
  document.getElementById('input_add_persentasi').value = ''
  document.getElementById('input_add_tampil').value = 'Y'
  document.getElementById('input_add_group').value = ''

}

function buttonEdit (nomor) {
  console.log(nomor)
  let _token = $("#_token").val();
  let filterDevisi = $("#jenisDevisi").val();
  let filterLaporan = $("#jenisLaporan").val();

  $.ajax({
    url: "{!! url('masterLabaRugiLoadEdit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nomor,
      filterDevisi,
      filterLaporan
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_edit_nomor").value = res[0].Nomor
      document.getElementById("input_edit_perkiraan").value = res[0].Perkiraan
      document.getElementById("input_edit_keterangan").value = res[0].Keterangan
      document.getElementById("input_edit_tipe").value = res[0].Tipe
      document.getElementById("input_edit_tanda").value = res[0].Tanda
      document.getElementById("input_edit_jumlah").value = res[0].Jumlah
      document.getElementById("input_edit_persentasi").value = res[0].Persen
      document.getElementById("input_edit_tampil").value = res[0].Tampil
      document.getElementById("input_edit_group").value = res[0].Grup

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (nomor) {
  console.log(nomor)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Devisi', 'Apakah yakin ingin menghapus data ' + nomor + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterLabaRugiSubmitDelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            nomor
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Data Laba Rugi telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function submitEdit () {

  let _token = $("#_token").val();
  
  let nomor = $("#input_edit_nomor").val();
  let perkiraan = $("#input_edit_perkiraan").val();
  let keterangan = $("#input_edit_keterangan").val();

  let tipe = $("#input_edit_tipe").val();
  let tanda = $("#input_edit_tanda").val();
  let jumlah = $("#input_edit_jumlah").val();

  let persentasi = $("#input_edit_persentasi").val();
  let tampil = $("#input_edit_tampil").val();
  let group = $("#input_edit_group").val();

  let devisi = $("#jenisDevisi").val();

  if (!nomor) {
    alertify.warning("Nomor harus diisi");
    return
  }

  if (!perkiraan) {
    perkiraan = '-';
  }

  if (!keterangan) {
    alertify.warning("Keterangan harus diisi");
    return
  }

  if (!tipe) {
    tipe = '-';
  }

  if (!tanda) {
    alertify.warning("Tanda harus diisi");
    return
  }

  if (!jumlah) {
    jumlah = '-';
  }

  if (!persentasi) {
     persentasi = '-'
  }

  if (!group) {
    group = '-';
  }

  if (group.length > 3) {
    alertify.warning("Group Hanya boleh 3 huruf");
    return;
  }

  if (!tampil) {
    alertify.warning("Tampil harus diisi");
    return
  }
  
  $.ajax({
    url: "{!! url('masterLabaRugiSubmitEdit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nomor,
      perkiraan,
      keterangan,
      tipe,
      tanda,
      jumlah,
      persentasi,
      tampil,
      group,
      devisi
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Laba Rugi telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();

  let nomor = $("#input_add_nomor").val();
  let perkiraan = $("#input_add_perkiraan").val();
  let keterangan = $("#input_add_keterangan").val();

  let tipe = $("#input_add_tipe").val();
  let tanda = $("#input_add_tanda").val();
  let jumlah = $("#input_add_jumlah").val();

  let persentasi = $("#input_add_persentasi").val();
  let tampil = $("#input_add_tampil").val();
  let group = $("#input_add_group").val();

  let devisi = $("#jenisDevisi").val();

  if (!nomor) {
    alertify.warning("Nomor harus diisi");
    return
  }

  if (!perkiraan) {
    perkiraan = '-';
  }

  if (!keterangan) {
    alertify.warning("Keterangan harus diisi");
    return
  }

  if (!tipe) {
    tipe = '-';
  }

  if (!tanda) {
    alertify.warning("Tanda harus diisi");
    return
  }

  if (!jumlah) {
    jumlah = '-';
  }

  if (!persentasi) {
     persentasi = '-'
  }

  if (!group) {
    group = '-';
  }

  if (group.length > 3) {
    alertify.warning("Group Hanya boleh 3 huruf");
    return;
  }

  if (!tampil) {
    alertify.warning("Tampil harus diisi");
    return
  }
  
  $.ajax({
    url: "{!! url('masterLabaRugiSubmitAdd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nomor,
      perkiraan,
      keterangan,
      tipe,
      tanda,
      jumlah,
      persentasi,
      tampil,
      group,
      devisi
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Laba Rugi telah ditambah");
        loadAll()
      }
    }})
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
    url: "{!! url('masterLabaRugiLoadPerkiraan') !!}",
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
    let tipePerkiraan = '';
        if (item.Tipe == 0) {
          tipePerkiraan = `<td>General</td>`;
        } else {
          tipePerkiraan = `<td>Detail</td>`;
        }

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonSelectPerkiraan('${item.Perkiraan}', '${item.Keterangan}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
      ${tipePerkiraan}
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
    <th scope="col">Tipe</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Merk'

  $("#tabelModalOpen").DataTable({
    "lengthChange": true,
    "paging": true,
  });
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectPerkiraan (perkiraan, keterangan){

  document.getElementById('input_add_perkiraan').value = perkiraan;
  document.getElementById('input_add_keterangan').value = keterangan;
  document.getElementById('input_edit_perkiraan').value = perkiraan;
  document.getElementById('input_edit_keterangan').value = keterangan;
  // document.getElementById('input_target_edit_merk').value = merk;

  $("#formModalOpen").modal("hide");
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
