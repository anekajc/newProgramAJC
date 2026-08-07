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
    <span class="sp-crumb-active">Neraca</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Neraca</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Neraca</button>
  </div> --}}

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  @include('master.partials.headerTableMaster')

    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="tabel">
          <thead>
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Kelompok</th>
              <th scope="col">Tipe</th>
              <th scope="col">Neraca</th>
            </tr>
          </thead>
          <tbody id="tabel_data" class="text-right">
            @for ($i = 0; $i < count($listData); $i++)
            <tr>
                <td>{{ $listData[$i]->Perkiraan }}</td>
                <td>{{ $listData[$i]->Keterangan }}</td>
                <td>{{ $listData[$i]->mKelompok }}</td>
                    <td>
                      @if($listData[$i]->mTipe == 'General')
                      <span class="sp-badge is-user">General</span>
                      @elseif($listData[$i]->mTipe == 'Detail')
                      <span class="sp-badge is-supervisor">Detail</span>
                      @endif
                    </td>
                <td class='text-left'>
                  <input class='form-control' 
                          onblur="onChangeNeraca('{{ $listData[$i]->Perkiraan }}')" 
                          oninput="formatNeracaInput(this)"
                          type='text'
                          maxlength="10"
                          value='{{ $listData[$i]->Neraca }}'>
                </td>
            </tr>
            @endfor
        </tbody>
        </table>
      </div>
  </div>

</div>

<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Perkiraan</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_perkiraan" placeholder="Perkiraan" disabled>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_keterangan" placeholder="Keterangan"disabled>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tipe</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_tipe" placeholder="Tipe" disabled>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Neraca</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_neraca" >
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

$(document).ready(function () {
  
  document.getElementById('breadcrumb').innerHTML = "Master Neraca"
  document.getElementById('AddVisibility').hidden = true;

  $("#tabel").DataTable({
    "lengthChange": true,
    "paging": true,
    "searching": true,
    "ordering": false,
    "dom": 'tip'
  });
});

  $("#tabel_filter_visual").on("keyup", function () {
    $("#tabel").DataTable().search(this.value).draw();
  });
  
let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();
  

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masterneracaloadall') !!}",
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
    <td>${item.Perkiraan}</td>
    <td>${item.Keterangan}</td>
    <td>${item.Kelompok}</td>
    <td>${item.Tipe}</td>
    <td class='text-left'><input class='form-control' onblur="changeQnt(${item.Neraca})" type='text'></td>
    </tr>`
  });


  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masterneracaspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_perkiraan").value = res[0].Perkiraan
      document.getElementById("input_edit_keterangan").value = res[0].Keterangan
      document.getElementById("input_edit_tipe").value = res[0].mTipe
      document.getElementById("input_edit_neraca").value = res[0].Neraca

    }})
    $("#formEdit").modal('toggle')
}


function onChangeNeraca (Perkiraan) {
  
// Or more explicitly:
if (!tempNeraca) {
    tempNeraca = '-';
}

  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masterneracaonChangeNeraca') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      Perkiraan,
      tempNeraca
    },
    success: function(res) {
        tempNeraca = ''
        alertify.success("Neraca Perkiraan : " + Perkiraan + " Berhasil Di-Update");

    }})
}

function submitEdit () {

  let _token = $("#_token").val();
  let neraca = $("#input_edit_neraca").val();
  let perkiraan = $("#input_edit_perkiraan").val();

  $.ajax({
    url: "{!! url('masterneracaspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      neraca,
      perkiraan
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Devisi telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }}
  )

  $("#formEdit").modal('hide')
}

let tempNeraca = ''

function formatNeracaInput(input) {
    let cursorPos = input.selectionStart;
    
    let value = input.value.replace(/[-]/g, '').replace(/[^a-zA-Z0-9]/g, '');
    
    let formatted = '';
    
    if (value.length > 0) {
        formatted += value.substring(0, 2); 
    }
    if (value.length > 2) {
        formatted += '-' + value.substring(2, 4);
    }
    if (value.length > 4) {
        formatted += '-' + value.substring(4, 8);
    }
    input.value = formatted;
    
    let newCursorPos = cursorPos;
    if (cursorPos > 2 && formatted.length > 2) newCursorPos++;
    if (cursorPos > 4 && formatted.length > 5) newCursorPos++;
    
    input.setSelectionRange(newCursorPos, newCursorPos);

    tempNeraca = input.value;
}

</script>




@endsection