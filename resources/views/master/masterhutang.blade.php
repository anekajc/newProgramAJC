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
    <span class="sp-crumb-active">Hutang</span>
  </div> --}}

  <div class="sp-page-head">
    <div>
      <h1>Master Hutang</h1>
    </div>
    {{-- <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Hutang</button> --}}
  </div>

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
      min-width: 220px; /* keeps "Hutang Usaha (21201)" from getting clipped */
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
      <select id="perkiraanCustomer" class="form-select" onchange="loadAll()">
        @foreach ($listDataCustomer as $customer)
          <option value="{{ $customer->keterangan }} ({{ $customer->Perkiraan }})">
            {{ $customer->keterangan }} ({{ $customer->Perkiraan }})
          </option>
        @endforeach
      </select>
    </div>

    <div class="sp-length-wrap">
      <label for="tabel_length_visual">Tampilkan</label>
      <select id="tabel_length_visual" class="form-select form-select-sm">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
        <option value="-1">Semua</option>
      </select>
    </div>
  </div>

  <div class="table-outer">
    <div class="table-wrap">
      <table class="tb" id="tabel">
        <thead>
          <tr>
            <th scope="col">Actions</th>
            <th scope="col">Kode Supplier</th>
            <th scope="col">Nama Supplier</th>
            <th scope="col">No Faktur</th>
            <th scope="col">Tanggal</th>
            <th scope="col">Jatuh Tempo</th>
            <th scope="col">Valas</th>
            <th scope="col">Kurs</th>
            <th scope="col">Debet(Rp)</th>
            <th scope="col">Debet Valas</th>
            <th scope="col">Kredit(Rp)</th>
            <th scope="col">Kredit Valas</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-right">
      </tbody>
      </table>
    </div>
</div>

</div>

<!-- Modal Add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 700px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Hutang Awal</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="noUrut" id="input_add_noUrut">

        <div class="container-fluid">
          <!-- Supplier -->
          <div class="form-group row">
            <label class="col-sm-3 form-label">Supplier</label>
            <div class="col-sm-3">
              <input type="text" class="form-control" id="kodeSupplier" placeholder="Supplier" disabled>
            </div>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="namaSupplier" placeholder="Nama Supplier" disabled>
            </div>
          </div>

          <!-- Perkiraan -->
          <div class="form-group row">
            <label class="col-sm-3 form-label">Perkiraan</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="namaPerkiraan" placeholder="Perkiraan" disabled>
            </div>
          </div>

          <!-- No Faktur -->
          <div class="form-group row">
            <label class="col-sm-3 form-label">No. Faktur</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="input_add_noFaktur" placeholder="No. Faktur">
            </div>
          </div>

          <!-- Tanggal Faktur -->
          <div class="form-group row">
            <label class="col-sm-3 form-label">Tanggal Faktur</label>
            <div class="col-sm-4">
              <input type="date" class="form-control" id="input_add_tanggalFaktur">
            </div>
            <label class="col-sm-2 form-label">Jatuh Tempo</label>
            <div class="col-sm-3">
              <input type="date" class="form-control" id="input_add_jatuhTempo">
            </div>
          </div>

          <div class="form-group row">

            <div class="col-sm-3">
                <label class="form-label">Valas</label>
            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_add_valas" value='IDR' disabled>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary btn-select" onclick="buttonValasAdd()">+</button>
                    </div>
                </div>
            </div>

            <label class="col-sm-2 form-label">Kurs</label>
            <div class="col-sm-3">
              <input type="number" class="form-control text-right" id="input_add_kurs" value=1.00 disabled>
            </div>
          </div>

          <!-- Debet -->
          <div class="form-group row" hidden>
            <label class="col-sm-3 form-label">Debet</label>
            <div class="col-sm-3">
              <input type="number" class="form-control text-right" id="input_add_debet" value="0" disabled>
            </div>
            <label class="col-sm-3 form-label">Debet (Rp)</label>
            <div class="col-sm-3">
              <input type="number" class="form-control text-right" id="input_add_debetRp" value="0" disabled>
            </div>
          </div>

          <!-- Kredit -->
          <div class="form-group row">
            <label class="col-sm-3 form-label">Kredit</label>
            <div class="col-sm-4">
              <input type="number" class="form-control text-right" id="input_add_kredit">
            </div>
            <label class="col-sm-2 form-label">Kredit (Rp)</label>
            <div class="col-sm-3">
              <input type="text" class="form-control text-right" id="input_add_kreditRp" disabled>
            </div>
          </div>

          <!-- No PO -->
          <div class="form-group row">
            <label class="col-sm-3 form-label">No. PO</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="input_add_noPO" placeholder="No. PO">
            </div>
          </div>

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End Modal Add -->

<!-- start modal edit -->
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 700px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Hutang Awal</h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="noUrut" id="input_edit_noUrut">

        <div class="container-fluid">
          <!-- Supplier -->
          
          <div class="form-group row">
            <label class="col-sm-3 form-label">Supplier</label>
            <div class="col-sm-3">
              <input type="text" class="form-control" id="kodeSupplierEdit" placeholder="Supplier" disabled>
            </div>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="namaSupplierEdit" placeholder="Nama Supplier" disabled>
            </div>
          </div>

          <!-- Perkiraan -->
          <div class="form-group row mt-2">
            <label class="col-sm-3 form-label">Perkiraan</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="namaPerkiraanEdit" placeholder="Perkiraan" disabled>
            </div>
          </div>

          <!-- No Faktur -->
          <div class="form-group row mt-2">
            <label class="col-sm-3 form-label">No. Faktur</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="input_edit_noFaktur" placeholder="No. Faktur" disabled>
            </div>
          </div>

          <!-- Tanggal Faktur -->
          <div class="form-group row mt-2">
            <label class="col-sm-3 form-label">Tanggal Faktur</label>
            <div class="col-sm-4">
              <input type="date" class="form-control" id="input_edit_tanggalFaktur">
            </div>
            <label class="col-sm-2 form-label">Jatuh Tempo</label>
            <div class="col-sm-3">
              <input type="date" class="form-control" id="input_edit_jatuhTempo">
            </div>
          </div>

          <div class="form-group row mt-2">

            <div class="col-sm-3">
                <label class="form-label">Valas</label>
            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="input_edit_valas" placeholder="Valas" disabled>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary btn-select" onclick="buttonValasAdd()">+</button>
                    </div>
                </div>
            </div>

            <label class="col-sm-2 form-label">Kurs</label>
            <div class="col-sm-3">
              <input type="text" class="form-control text-right" id="input_edit_kurs" placeholder="Kurs" disabled>
            </div>
          </div>

          <!-- Debet -->
          <div class="form-group row" hidden>
            <label class="col-sm-3 form-label">Debet</label>
            <div class="col-sm-3">
              <input type="text" class="form-control" id="input_edit_debet" value="0" disabled>
            </div>
            <label class="col-sm-3 form-label">Debet (Rp)</label>
            <div class="col-sm-3">
              <input type="text" class="form-control" id="input_edit_debetRp" value="0" disabled>
            </div>
          </div>

          <!-- Kredit -->
          <div class="form-group row mt-2">
            <label class="col-sm-3 form-label">Kredit</label>
            <div class="col-sm-4">
              <input type="number" class="form-control text-right" id="input_edit_kredit">
            </div>
            <label class="col-sm-2 form-label">Kredit (Rp)</label>
            <div class="col-sm-3">
              <input type="text" class="form-control text-right" id="input_edit_kreditRp" disabled>
            </div>
          </div>

          <!-- No PO -->
          <div class="form-group row mt-2">
            <label class="col-sm-3 form-label">No. PO</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="input_edit_noPO" placeholder="No. PO">
            </div>
          </div>

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->

<!-- start modal valas select -->
<div class="modal fade"  id="formSelectValas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Valas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelSelectValas" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>1
              <th scope="col">Valas</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Kurs</th>

            </tr>
          </thead>

          <tbody id="tabel_dataSelectValas" class="text-left" >
            <tr>

              <td></td>
              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihValas()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        </div>
  </div>
</div>
</div>
<!-- End modal select valas-->


@endsection

@section('js')
<script src="{{ asset('js/masterTable.js') }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();
  let filter = $("#perkiraanCustomer").val();
  console.log(filter);

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masterhutangloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      filter
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
      <div class="action-buttons-wrap">
      <button data-toggle="tooltip" data-placement="top" title="Add Hutang" class="btn-action-sm btn-action-primary" type="button" onclick="buttonAdd'${item.KodeCustSupp}', '${item.Perkiraan}', '${item.NAMACUST}')"><i class="bi bi-file-earmark-plus"></i></button>
      ${item.NoFaktur != null ? `
        <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.NoFaktur}', '${item.KodeCustSupp}', '${item.Perkiraan}')"><i class="bi bi-pen"></i></button>
        <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.NoFaktur}')"><i class="bi bi-trash"></i></button>
      ` : ''}
      </div>
    </td>
    <td>${item.KodeCustSupp}</td>
    <td>${item.NAMACUST}</td>
    <td>${item.NoFaktur}</td>
    <td>${(new Date(item.Tanggal)).toLocaleDateString('en-CA')}</td>
    <td>${(new Date(item.JatuhTempo)).toLocaleDateString('en-CA')}</td>
    <td>${item.Valas}</td>
    <td>${item.Kurs}</td>
    <td>${item.Debet == null ? '' : item.Debet}</td>
    <td>${item.DebetD == null ? '' : item.DebetD}</td>
    <td>${item.Kredit}</td>
    <td>${item.KreditD}</td>
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

function buttonAdd (kodeSupplier, Perkiraan, namaSupplier) {
  document.getElementById("kodeSupplier").value = kodeSupplier
  document.getElementById("namaSupplier").value = namaSupplier
  document.getElementById("namaPerkiraan").value = Perkiraan
  document.getElementById("input_add_noFaktur").value = ''
  document.getElementById("input_add_tanggalFaktur").value = ''
  document.getElementById("input_add_jatuhTempo").value = ''
  document.getElementById("input_add_kredit").value = ''
  document.getElementById("input_add_kreditRp").value = ''
  document.getElementById("input_add_noPO").value = ''
  $("#form").modal('toggle')

}

function buttonEdit (kode, perkiraanCust) {
  console.log(kode)
  console.log(perkiraanCust)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masterhutangspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("kodeSupplierEdit").value = res[0].KodeCustSupp
      document.getElementById("namaSupplierEdit").value = res[0].NAMACUST
      document.getElementById("namaPerkiraanEdit").value = res[0].Perkiraan
      document.getElementById("input_edit_noFaktur").value = res[0].NoFaktur
      document.getElementById("input_edit_tanggalFaktur").value = new Date(res[0].Tanggal).toLocaleDateString('en-CA');
      document.getElementById("input_edit_jatuhTempo").value = new Date(res[0].JatuhTempo).toLocaleDateString('en-CA');
      document.getElementById("input_edit_valas").value = res[0].Valas
      document.getElementById("input_edit_kurs").value = formatAngka(parseFloat(res[0].Kurs).toFixed(2))
      document.getElementById("input_edit_debet").value = res[0].Debet
      document.getElementById("input_edit_debetRp").value = res[0].DebetD
      document.getElementById("input_edit_kredit").value = res[0].KreditD
      document.getElementById("input_edit_kreditRp").value = formatAngka(parseFloat(res[0].Kredit).toFixed(2))
      document.getElementById("input_edit_noPO").value = res[0].POcust


    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (noFaktur) {
  console.log(noFaktur)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Nomor Faktur', 'Apakah yakin ingin menghapus No. Faktur ' + noFaktur + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterhutangspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            noFaktur
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("No. Faktur telah dihapus");

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
  let noFaktur = $("#input_edit_noFaktur").val();
  let tanggalFaktur = $("#input_edit_tanggalFaktur").val();
  let jatuhTempo = $("#input_edit_jatuhTempo").val();
  let valas = $("#input_edit_valas").val();
  let kurs = $("#input_edit_kurs").val();
  let kredit = $("#input_edit_kredit").val();
  let kreditRp = $("#input_edit_kreditRp").val();
  let noPo = $("#input_edit_noPO").val();

  $.ajax({
    url: "{!! url('masterhutangspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      noFaktur,
      tanggalFaktur,
      jatuhTempo,
      valas,
      kurs,
      kredit,
      kreditRp,
      noPo
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Hutang telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}
//
function submitAdd () {

  let _token = $("#_token").val();
  let noFaktur = $("#input_add_noFaktur").val();
  let kodeSupplier = $("#kodeSupplier").val();
  let perkiraanSupplier = $("#namaPerkiraan").val();
  let tanggalFaktur = $("#input_add_tanggalFaktur").val();
  let jatuhTempo = $("#input_add_jatuhTempo").val();
  let valas = $("#input_add_valas").val();
  let kurs = $("#input_add_kurs").val();
  let debet = $("#input_add_debet").val();
  let debetRp = $("#input_add_debetRp").val();
  let kredit = $("#input_add_kredit").val();
  let kreditRp = $("#input_add_kreditRp").val();
  let noPo = $("#input_add_noPO").val();

  if (!noFaktur) {
    alertify.warning("No. Faktur harus diisi");
    return
  }

  if (!tanggalFaktur) {
    alertify.warning("Tanggal Faktur harus diisi");
    return
  }

  if (!jatuhTempo) {
    alertify.warning("Jatuh Tempo harus diisi");
    return
  }

  if (!valas) {
    alertify.warning("Valas harus diisi");
    return
  }

  if (!kurs) {
    alertify.warning("Kurs harus diisi");
    return
  }

  if (!kredit) {
    alertify.warning("Kredit harus diisi");
    return
  }

  if (!noPo) {
    alertify.warning("No. PO harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterhutangspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      noFaktur,
      kodeSupplier,
      perkiraanSupplier,
      tanggalFaktur,
      jatuhTempo,
      valas,
      kurs,
      debet,
      debetRp,
      kredit,
      kreditRp,
      noPo
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Hutang telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

function buttonValasAdd () {
  loadValas()
  $("#formSelectValas").modal('toggle')
}

function buttonPilihValas(selectedPerkiraan, selectedKurs) {
  $("#input_add_valas").val(selectedPerkiraan);
  $("#input_add_kurs").val(selectedKurs);
  $("#input_edit_valas").val(selectedPerkiraan);
  $("#input_edit_kurs").val(selectedKurs);
  $("#formSelectValas").modal("hide");

}

function loadValas() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelSelectValas').DataTable().destroy();

  $.ajax({
    url: "{!! url('masterhutangloadvalas') !!}",
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
        <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonPilihValas('${item.KODEVLS}', '${item.KURS}')"><i class='bi bi-plus'></i></button>
      </td>
      <td>${item.KODEVLS}</td>
      <td>${item.NAMAVLS}</td>
      <td class='text-right'>${item.KURS}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelectValas").innerHTML = rowTable
  $("#tabelSelectValas").DataTable({
    "lengthChange": true,
    "paging": true,
    "searching": true,
    "dom": 'tip'
  });
  
}

function loadCustomer() {
    // Fetch data from the server
    $.ajax({
        url: "{!! url('masterhutangloadcustomer') !!}",
        type: "get",
        success: function(res) {
            console.log(res);
            populateValasDropdown(res);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

function populateValasDropdown(data) {
    let valasOptions = '';
    data.forEach((item) => {
        valasOptions += `<option value="${item.Perkiraan}">${item.keterangan} (${item.Perkiraan})</option>`;
    });

    // Set the HTML content of the select dropdown
    $('#perkiraanCustomer').html(valasOptions);
}

    const inputKredit = document.getElementById('input_add_kredit');
    const inputKreditRp = document.getElementById('input_add_kreditRp');
    const inputKurs = document.getElementById('input_add_kurs');

    // Add event listener to the Kredit input field
    inputKredit.addEventListener('input', function() {
        // Get the values from the input fields
        const kredit = parseFloat(inputKredit.value);
        const kurs = parseFloat(inputKurs.value);

        // Calculate the Kredit(Rp)
        const kreditRp = kredit * kurs;

        // Update the value of the Kredit(Rp) input field
        inputKreditRp.value = kreditRp; // Assuming you want to display two decimal places
    });

    const inputKreditEdit = document.getElementById('input_edit_kredit');
    const inputKreditRpEdit = document.getElementById('input_edit_kreditRp');
    const inputKursEdit = document.getElementById('input_edit_kurs');

    // Add event listener to the Kredit input field
    inputKreditEdit.addEventListener('input', function() {
        // Get the values from the input fields
        const kreditEdit = parseFloat(inputKreditEdit.value);
        const kursEdit = parseFloat(inputKursEdit.value);

        // Calculate the Kredit(Rp)
        const kreditRpEdit = kreditEdit * kursEdit;

        // Update the value of the Kredit(Rp) input field
        inputKreditRpEdit.value = kreditRpEdit; // Assuming you want to display two decimal places
    });

function formatAngka (angkaString) {
  console.log('formatAngka' , angkaString);
  let tempAngka = angkaString.split('.')
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
    // console.log(i, temp1)
  }
  temp1 += '.' + tempAngka[1]
  return temp1
};

window.onload = function() {
    loadAll();
};

</script>




@endsection
