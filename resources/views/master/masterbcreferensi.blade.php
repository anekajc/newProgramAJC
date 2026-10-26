@extends('newmasterTest')
@section('buttons')
@endsection
@section('page-title', $def['judul'])

{{-- Satu view untuk 8 master referensi Bea Cukai (MasterBcReferensiController::definisi);
     kolom tabel & isian form dibentuk dari $def['kolom']. Pola halaman = master/mastervalas. --}}
@section('content')

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! (int) ($akses->ISKOREKSI ?? 0) !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! (int) ($akses->ISHAPUS ?? 0) !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

      @include('master.partials.toolbarMaster', ['tanpaTambah' => (int) ($akses->ISTAMBAH ?? 0) !== 1])

      <table id="tabel" class="data-table po-aksi-hover">
        <thead>
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            @foreach ($def['kolom'] as $k)
              <th style="padding: 4px 12px;" scope="col">{{ $k[1] }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left"></tbody>
      </table>

    </div>
  </div>

</div>


<!-- start modal add / edit -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="labelForm" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="labelForm">Tambah {{ $def['judul'] }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="formBsGrid">
      <div class="modal-body">
        <input type="hidden" id="input_id" value="" />

        <div class="bs-form bs-form-1">
          @foreach ($def['kolom'] as $k)
            <label for="input_{{ $k[0] }}" class="bs-wrap">{{ $k[1] }}</label>
            @if ($k[2] === 'angka')
              <input type="number" step="any" class="form-control text-right" id="input_{{ $k[0] }}" data-k="{{ $k[0] }}" placeholder="0">
            @else
              <input type="text" class="form-control {{ $k[4] ? 'bc-kode' : '' }}" id="input_{{ $k[0] }}" data-k="{{ $k[0] }}" maxlength="{{ $k[3] }}" placeholder="{{ $k[1] }}">
            @endif
          @endforeach
        </div>
        <div class="text-muted mt-2" id="infoKode" style="font-size: .75rem; display:none">Kode tidak bisa diubah - dokumen BC menyimpan kode ini.</div>
      </div>
      </div>{{-- /#formBsGrid --}}
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitForm()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add / edit-->

@endsection

@section('js')
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script type="text/javascript">

const JENIS = @json($def['jenis']);
const JUDUL = @json($def['judul']);
// [kolom DB, label, tipe teks|angka, panjang maks, kode?]
const KOLOM = @json($def['kolom']);
const BISA_EDIT = @json($def['bisaEdit']);

let dataRefresh = []

function esc (v) {
  return $('<div>').text(v === null || v === undefined ? '' : String(v)).html()
}

function angka (v) {
  const n = Number(String(v === null || v === undefined || v === '' ? 0 : v).replace(/^(-?)\./, '$10.'))
  return isNaN(n) ? '' : n.toLocaleString('en-US', { maximumFractionDigits: 4 })
}

function loadAll () {

  let _token = $("#_token").val();
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterbcreferensiloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      jenis: JENIS,
    },
    success: function(res) {
      dataRefresh = res
  }})

  const bisaEdit = BISA_EDIT && Number($("#akses_iskoreksi").val()) === 1
  const bisaHapus = Number($("#akses_ishapus").val()) === 1

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    rowTable += `<tr>
      <td class="text-center">
        <div class="po-aksi-wrap">
          ${bisaEdit ? `<button title="Edit" class="btn btn-success" type="button" onclick="buttonEdit(${Number(item.id)})"><i class="bi bi-pen"></i></button>` : ''}
          ${bisaHapus ? `<button title="Delete" class="btn btn-danger" type="button" onclick="buttonDelete(${Number(item.id)})"><i class="bi bi-trash"></i></button>` : ''}
        </div>
      </td>
      ${KOLOM.map(k => k[2] === 'angka' ? `<td class="text-right">${angka(item[k[0]])}</td>` : `<td>${esc(item[k[0]])}</td>`).join('')}
    </tr>`

  });

  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable(MasterList.opsi())
  MasterList.selesai('#tabel')

}

// Edit: kolom kode dikunci (dokumen BC menyimpan kodenya sebagai teks).
function isiForm (d, edit) {
  $("#input_id").val(d.id ?? '')
  KOLOM.forEach(k => {
    let v = d[k[0]]
    if (k[2] === 'angka') {
      v = (v === null || v === undefined || v === '') ? '' : Number(String(v).replace(/^(-?)\./, '$10.'))
    }
    $("#input_" + k[0]).val(v ?? '')
  })
  $("#form .bc-kode").prop('disabled', edit)
  $("#infoKode").toggle(edit)
}

function buttonAdd () {
  isiForm({}, false)
  $("#labelForm").text('Tambah ' + JUDUL)
  $("#form").modal('show')
}

function buttonEdit (id) {
  let _token = $("#_token").val();
  let ketemu = false
  $.ajax({
    url: "{!! url('masterbcreferensispdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      jenis: JENIS,
      id
    },
    success: function(res) {
      if (!res || !res.length) {
        alertify.warning("Data " + JUDUL + " tidak ditemukan, silakan refresh halaman")
        return
      }
      ketemu = true
      isiForm(res[0], true)
    }})
    if (ketemu) {
      $("#labelForm").text('Edit ' + JUDUL)
      $("#form").modal('show')
    }
}

function buttonDelete (id) {
  let _token = $("#_token").val();
  const item = dataRefresh.find(d => Number(d.id) === Number(id)) || {}
  const kode = KOLOM.filter(k => k[4]).map(k => item[k[0]]).join(' / ')

  alertify.confirm('Hapus ' + JUDUL, 'Apakah yakin ingin menghapus ' + esc(JUDUL) + ' ' + esc(kode) + ' ?',
      function() {
        $.ajax({
          url: "{!! url('masterbcreferensispdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            jenis: JENIS,
            id
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              loadAll()
              alertify.success(JUDUL + " telah dihapus");
            }
          }})
      }
    ,function(){
    });

}

function submitForm () {

  const id = $("#input_id").val()
  const data = {
    _token : $("#_token").val(),
    jenis: JENIS,
    id,
  }

  for (const k of KOLOM) {
    const v = ($("#input_" + k[0]).val() || '').trim()
    if (k[2] !== 'angka' && !v && !(id && k[4])) {
      alertify.warning(k[1] + " harus diisi");
      return
    }
    data[k[0]] = v
  }

  $.ajax({
    url: id ? "{!! url('masterbcreferensispedit') !!}" : "{!! url('masterbcreferensispadd') !!}",
    type: "post",
    async: false,
    data,
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        alertify.success(id ? "Data " + JUDUL + " telah diedit" : "Data " + JUDUL + " telah ditambah");
        loadAll()
        $("#form").modal('hide')
      }

    }})

}

window.onload = function(){
  loadAll();
};

</script>

@endsection
