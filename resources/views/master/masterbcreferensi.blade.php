@extends('newmasterTest')
@section('buttons')
@endsection
@section('page-title', $judul)

{{-- Satu view untuk master referensi Bea Cukai (MasterBcReferensiController::definisi); kolom tabel &
     isian form dibentuk dari $defs[*]['kolom']. Pola halaman = master/mastervalas.
     $defs berisi satu tabel (7 master) atau dua tabel (Kontainer: Ukuran + Tipe, masing-masing
     dengan tombol Tambah, kotak cari dan modal sendiri). --}}
@php
  $multi = count($defs) > 1;
  $seksi = array_map(fn ($d) => ['jenis' => $d['jenis'], 'judul' => $d['judul'], 'kolom' => $d['kolom'], 'bisaEdit' => $d['bisaEdit']], $defs);
@endphp
@section('content')

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <input type="hidden" id="akses_istambah" value="{!! (int) ($akses->ISTAMBAH ?? 0) !!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! (int) ($akses->ISKOREKSI ?? 0) !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! (int) ($akses->ISHAPUS ?? 0) !!}" />

  @if ($multi)
    <link rel="stylesheet" href="{!! URL::asset('css/master-list.css') !!}?v={{ @filemtime(base_path('public/css/master-list.css')) ?: '1' }}">
    <style>
      .bc-seksi + .bc-seksi { margin-top: 16px; }
      .bc-seksi .po-table-wrap { max-height: 340px; overflow: auto; }
      .bc-seksi-judul { font-weight: 600; margin-right: 12px; white-space: nowrap; }
    </style>
  @endif

  @foreach ($defs as $def)
    <div class="card bc-seksi">
      <div class="card-body" style="padding:0;">

        @if ($multi)
          <div class="po-toolbar">
            <span class="bc-seksi-judul">{{ $def['judul'] }}</span>
            <input type="search" class="po-search-inp bc-cari" data-jenis="{{ $def['jenis'] }}" placeholder="Cari data" autocomplete="off">
            @if ((int) ($akses->ISTAMBAH ?? 0) === 1)
              <div class="po-toolbar-act">
                <button class="btn btn-dpp-utama" type="button" onclick="buttonAdd('{{ $def['jenis'] }}')">Tambah</button>
              </div>
            @endif
          </div>
        @else
          @include('master.partials.toolbarMaster', ['tanpaTambah' => (int) ($akses->ISTAMBAH ?? 0) !== 1])
        @endif

        <table id="{{ $multi ? 'tabel_' . $def['jenis'] : 'tabel' }}" class="data-table po-aksi-hover">
          <thead>
            <tr>
              <th style="padding: 4px 12px;" scope="col">Actions</th>
              @foreach ($def['kolom'] as $k)
                <th style="padding: 4px 12px;" scope="col">{{ $k[1] }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody id="tbody_{{ $def['jenis'] }}" class="text-left"></tbody>
        </table>

      </div>
    </div>
  @endforeach

</div>


@foreach ($defs as $def)
<!-- start modal add / edit {{ $def['judul'] }} -->
<div class="modal fade" id="form_{{ $def['jenis'] }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="labelForm_{{ $def['jenis'] }}">Tambah {{ $def['judul'] }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="formBsGrid">
      <div class="modal-body">
        <input type="hidden" id="input_id_{{ $def['jenis'] }}" value="" />

        <div class="bs-form bs-form-1">
          @foreach ($def['kolom'] as $k)
            <label for="input_{{ $def['jenis'] }}_{{ $k[0] }}" class="bs-wrap">{{ $k[1] }}</label>
            @if ($k[2] === 'angka')
              <input type="number" step="any" class="form-control text-right" id="input_{{ $def['jenis'] }}_{{ $k[0] }}" data-k="{{ $k[0] }}" placeholder="0">
            @else
              <input type="text" class="form-control {{ $k[4] ? 'bc-kode' : '' }}" id="input_{{ $def['jenis'] }}_{{ $k[0] }}" data-k="{{ $k[0] }}" maxlength="{{ $k[3] }}" placeholder="{{ $k[1] }}">
            @endif
          @endforeach
        </div>
        <div class="text-muted mt-2 bc-info-kode" id="infoKode_{{ $def['jenis'] }}" style="font-size: .75rem; display:none">Kode tidak bisa diubah - dokumen BC menyimpan kode ini.</div>
      </div>
      </div>{{-- /#formBsGrid --}}
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitForm('{{ $def['jenis'] }}')">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add / edit -->
@endforeach

@endsection

@section('js')
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script type="text/javascript">

const MULTI = @json($multi);
// Tiap tabel: { jenis, judul, kolom: [[kolom DB, label, tipe teks|angka, panjang maks, kode?]], bisaEdit }
const SEKSI = @json($seksi);
const DATA = {}

function seksi (jenis) {
  return SEKSI.find(s => s.jenis === jenis) || SEKSI[0]
}

function idTabel (jenis) {
  return MULTI ? '#tabel_' + jenis : '#tabel'
}

function esc (v) {
  return $('<div>').text(v === null || v === undefined ? '' : String(v)).html()
}

function angka (v) {
  const n = Number(String(v === null || v === undefined || v === '' ? 0 : v).replace(/^(-?)\./, '$10.'))
  return isNaN(n) ? '' : n.toLocaleString('en-US', { maximumFractionDigits: 4 })
}

function loadAll (jenis) {

  if (!jenis) {
    SEKSI.forEach(s => loadAll(s.jenis))
    return
  }

  const s = seksi(jenis)
  const tabel = idTabel(jenis)
  let _token = $("#_token").val();
  if ($.fn.DataTable.isDataTable(tabel)) {
    $(tabel).DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterbcreferensiloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      jenis,
    },
    success: function(res) {
      DATA[jenis] = res
  }})

  const bisaEdit = s.bisaEdit && Number($("#akses_iskoreksi").val()) === 1
  const bisaHapus = Number($("#akses_ishapus").val()) === 1

  let rowTable = ""
  ;(DATA[jenis] || []).forEach((item, i) => {
    rowTable += `<tr>
      <td class="text-center">
        <div class="po-aksi-wrap">
          ${bisaEdit ? `<button title="Edit" class="btn btn-success" type="button" onclick="buttonEdit('${jenis}', ${Number(item.id)})"><i class="bi bi-pen"></i></button>` : ''}
          ${bisaHapus ? `<button title="Delete" class="btn btn-danger" type="button" onclick="buttonDelete('${jenis}', ${Number(item.id)})"><i class="bi bi-trash"></i></button>` : ''}
        </div>
      </td>
      ${s.kolom.map(k => k[2] === 'angka' ? `<td class="text-right">${angka(item[k[0]])}</td>` : `<td>${esc(item[k[0]])}</td>`).join('')}
    </tr>`

  });

  document.getElementById("tbody_" + jenis).innerHTML = rowTable
  $(tabel).DataTable(MasterList.opsi())
  if (!MULTI) {
    MasterList.selesai(tabel)
  }

}

// Edit: kolom kode dikunci (dokumen BC menyimpan kodenya sebagai teks).
function isiForm (jenis, d, edit) {
  $("#input_id_" + jenis).val(d.id ?? '')
  seksi(jenis).kolom.forEach(k => {
    let v = d[k[0]]
    if (k[2] === 'angka') {
      v = (v === null || v === undefined || v === '') ? '' : Number(String(v).replace(/^(-?)\./, '$10.'))
    }
    $("#input_" + jenis + "_" + k[0]).val(v ?? '')
  })
  $("#form_" + jenis + " .bc-kode").prop('disabled', edit)
  $("#infoKode_" + jenis).toggle(edit)
}

function buttonAdd (jenis) {
  jenis = seksi(jenis).jenis
  isiForm(jenis, {}, false)
  $("#labelForm_" + jenis).text('Tambah ' + seksi(jenis).judul)
  $("#form_" + jenis).modal('show')
}

function buttonEdit (jenis, id) {
  let _token = $("#_token").val();
  let ketemu = false
  $.ajax({
    url: "{!! url('masterbcreferensispdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      jenis,
      id
    },
    success: function(res) {
      if (!res || !res.length) {
        alertify.warning("Data " + seksi(jenis).judul + " tidak ditemukan, silakan refresh halaman")
        return
      }
      ketemu = true
      isiForm(jenis, res[0], true)
    }})
    if (ketemu) {
      $("#labelForm_" + jenis).text('Edit ' + seksi(jenis).judul)
      $("#form_" + jenis).modal('show')
    }
}

function buttonDelete (jenis, id) {
  let _token = $("#_token").val();
  const s = seksi(jenis)
  const item = (DATA[jenis] || []).find(d => Number(d.id) === Number(id)) || {}
  const kode = s.kolom.filter(k => k[4]).map(k => item[k[0]]).join(' / ')

  alertify.confirm('Hapus ' + s.judul, 'Apakah yakin ingin menghapus ' + esc(s.judul) + ' ' + esc(kode) + ' ?',
      function() {
        $.ajax({
          url: "{!! url('masterbcreferensispdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            jenis,
            id
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              loadAll(jenis)
              alertify.success(s.judul + " telah dihapus");
            }
          }})
      }
    ,function(){
    });

}

function submitForm (jenis) {

  const s = seksi(jenis)
  const id = $("#input_id_" + jenis).val()
  const data = {
    _token : $("#_token").val(),
    jenis,
    id,
  }

  for (const k of s.kolom) {
    const v = ($("#input_" + jenis + "_" + k[0]).val() || '').trim()
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
        alertify.success(id ? "Data " + s.judul + " telah diedit" : "Data " + s.judul + " telah ditambah");
        loadAll(jenis)
        $("#form_" + jenis).modal('hide')
      }

    }})

}

// Halaman dua tabel: kotak cari tiap tabel memfilter tabelnya sendiri.
$(document).on('input', '.bc-cari', function () {
  const tabel = idTabel($(this).data('jenis'))
  if ($.fn.DataTable.isDataTable(tabel)) {
    $(tabel).DataTable().search(this.value).draw()
  }
})

window.onload = function(){
  loadAll();
};

</script>

@endsection
