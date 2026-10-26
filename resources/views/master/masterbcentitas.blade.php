@extends('newmasterTest')
@section('buttons')
@endsection
@section('page-title', 'Entitas')

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
            <th style="padding: 4px 12px;" scope="col">Kode</th>
            <th style="padding: 4px 12px;" scope="col">Nama Entitas</th>
            <th style="padding: 4px 12px;" scope="col">NPWP / Nomor Identitas</th>
            <th style="padding: 4px 12px;" scope="col">NIB</th>
            <th style="padding: 4px 12px;" scope="col">Negara</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left"></tbody>
      </table>

    </div>
  </div>

</div>


<!-- start modal add / edit -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="labelForm" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 760px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="labelForm">Tambah Entitas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="formBsGrid">
      <div class="modal-body">
        <input type="hidden" id="input_id" value="" />

        <div class="bs-form">
          <label for="input_kode">Kode</label>
          <input type="text" class="form-control" id="input_kode" maxlength="50" placeholder="Kode entitas">

          <label for="input_idnegara">Negara</label>
          <select class="form-control" id="input_idnegara">
            <option value="">- Pilih Negara -</option>
            @foreach ($negara as $n)
              <option value="{{ $n->id }}">{{ $n->KODENEGARA }} - {{ $n->NAMANEGARA }}</option>
            @endforeach
          </select>

          <label for="input_nama">Nama</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_nama" maxlength="100" placeholder="Nama entitas"></div>

          <label for="input_alamat">Alamat</label>
          <div class="bs-full"><textarea class="form-control" id="input_alamat" rows="2" maxlength="1000" placeholder="Alamat entitas"></textarea></div>

          <label for="input_jenisidentitas" class="bs-wrap">Jenis Identitas</label>
          <input type="text" class="form-control" id="input_jenisidentitas" maxlength="50" placeholder="6">

          <label for="input_nomoridentitas" class="bs-wrap">Nomor Identitas</label>
          <input type="text" class="form-control" id="input_nomoridentitas" maxlength="50" placeholder="NPWP">

          <label for="input_nib">NIB</label>
          <input type="text" class="form-control" id="input_nib" maxlength="50" placeholder="NIB">

          <label for="input_niper">NIPER</label>
          <input type="text" class="form-control" id="input_niper" maxlength="50" placeholder="NIPER">

          <label for="input_jenisapi">Jenis API</label>
          <input type="text" class="form-control" id="input_jenisapi" maxlength="50" placeholder="02">

          <label for="input_status">Kode Status</label>
          <input type="text" class="form-control" id="input_status" maxlength="50" placeholder="0">

          <label for="input_nomorijin" class="bs-wrap">Nomor Izin TPB</label>
          <input type="text" class="form-control" id="input_nomorijin" maxlength="50" placeholder="Nomor izin">

          <label for="input_tanggalijin" class="bs-wrap">Tanggal Izin</label>
          <input type="date" class="form-control" id="input_tanggalijin">
        </div>
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

let dataRefresh = []

function esc (v) {
  return $('<div>').text(v === null || v === undefined ? '' : String(v)).html()
}

function loadAll () {

  let _token = $("#_token").val();
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterbcentitasloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      dataRefresh = res
  }})

  const bisaEdit = Number($("#akses_iskoreksi").val()) === 1
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
    <td>${esc(item.KODENTITAS)}</td>
    <td>${esc(item.NAMAENTITAS)}</td>
    <td>${esc(item.NOMORIDENTITAS)}</td>
    <td>${esc(item.NIBENTITAS)}</td>
    <td>${esc(item.KODENEGARA ? item.KODENEGARA + ' - ' + (item.NAMANEGARA || '') : '')}</td>
    </tr>`

  });

  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable(MasterList.opsi())
  MasterList.selesai('#tabel')

}

function isiForm (d) {
  $("#input_id").val(d.id ?? '')
  $("#input_kode").val(d.KODENTITAS ?? '')
  $("#input_nama").val(d.NAMAENTITAS ?? '')
  $("#input_alamat").val(d.ALAMATENTITAS ?? '')
  $("#input_idnegara").val(d.IDNEGARA ? String(d.IDNEGARA) : '')
  $("#input_jenisidentitas").val(d.KODEJENISIDENTITAS ?? '6')
  $("#input_nomoridentitas").val(d.NOMORIDENTITAS ?? '')
  $("#input_nib").val(d.NIBENTITAS ?? '')
  $("#input_niper").val(d.NIPERENTITAS ?? '')
  $("#input_jenisapi").val(d.KODEJENISAPI ?? '02')
  $("#input_status").val(d.KODESTATUS ?? '0')
  $("#input_nomorijin").val(d.NOMORIJINIDENTITAS ?? '')
  $("#input_tanggalijin").val(d.TANGGALIJINIDENTITAS ? String(d.TANGGALIJINIDENTITAS).substring(0, 10) : '')
}

function buttonAdd () {
  isiForm({})
  $("#labelForm").text('Tambah Entitas')
  $("#form").modal('show')
}

function buttonEdit (id) {
  let _token = $("#_token").val();
  let ketemu = false
  $.ajax({
    url: "{!! url('masterbcentitasspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      id
    },
    success: function(res) {
      if (!res || !res.length) {
        alertify.warning("Data entitas tidak ditemukan, silakan refresh halaman")
        return
      }
      ketemu = true
      isiForm(res[0])
    }})
    if (ketemu) {
      $("#labelForm").text('Edit Entitas')
      $("#form").modal('show')
    }
}

function buttonDelete (id) {
  let _token = $("#_token").val();
  const item = dataRefresh.find(d => Number(d.id) === Number(id)) || {}

  alertify.confirm('Hapus Entitas', 'Apakah yakin ingin menghapus entitas ' + esc(item.NAMAENTITAS || id) + ' ?',
      function() {
        $.ajax({
          url: "{!! url('masterbcentitasspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            id
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              loadAll()
              alertify.success("Entitas telah dihapus");
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
    id,
    kode: $("#input_kode").val(),
    nama: ($("#input_nama").val() || '').trim(),
    alamat: ($("#input_alamat").val() || '').trim(),
    idnegara: $("#input_idnegara").val(),
    jenisidentitas: $("#input_jenisidentitas").val(),
    nomoridentitas: $("#input_nomoridentitas").val(),
    nib: $("#input_nib").val(),
    niper: $("#input_niper").val(),
    jenisapi: $("#input_jenisapi").val(),
    status: $("#input_status").val(),
    nomorijin: $("#input_nomorijin").val(),
    tanggalijin: $("#input_tanggalijin").val(),
  }

  if (!data.nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (!data.alamat) {
    alertify.warning("Alamat harus diisi");
    return
  }

  if (!data.idnegara) {
    alertify.warning("Negara harus dipilih");
    return
  }

  $.ajax({
    url: id ? "{!! url('masterbcentitasspedit') !!}" : "{!! url('masterbcentitasspadd') !!}",
    type: "post",
    async: false,
    data,
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        alertify.success(id ? "Data entitas telah diedit" : "Data entitas telah ditambah");
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
