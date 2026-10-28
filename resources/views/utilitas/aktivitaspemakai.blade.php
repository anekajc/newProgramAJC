@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Aktivitas Pemakai')

{{-- Laporan log DBLOGFILE -- murni Read, tidak ada Tambah/Edit/Hapus. Toolbar memakai
     master.partials.toolbarMaster 1:1 dengan pola gudang/pembebanansample.blade.php: Periode
     (s/d) langsung di toolbar lewat $slotFilter, dropdown Aktivitas/Transaksi/Pemakai di dalam
     modal Filter (tombol hijau) lewat $filterIsi -- MasterList.filter.terapkan()/reset() di
     master-list.js otomatis memanggil window.loadAll(). Tabel hasil tidak punya kolom Actions
     sama sekali, sama seperti tab Penawaran di marketing/so.blade.php (#tabel7). Data dimuat
     langsung saat halaman dibuka (bukan lewat tombol "Lihat Aktivitas" lagi). --}}
@section('css')
<link rel="stylesheet"
    href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
<link rel="stylesheet"
    href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
@endsection

@section('content')

@php
  $akTanggalAwal = \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->startOfMonth()->format('Y-m-d');
  $akTanggalAkhir = \Carbon\Carbon::createFromDate((int) $periode->tahun, (int) $periode->bulan, 1)->endOfMonth()->format('Y-m-d');
@endphp

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

      @include('master.partials.toolbarMaster', [
        'tanpaTambah' => true,
        'slotFilter' => '
          <div class="po-filter-wrap">
            <label>Periode</label>
            <input type="date" onchange="loadAll()" class="po-filter-inp" id="ak_tanggal_awal" value="' . $akTanggalAwal . '">
            <span class="po-filter-sep">s/d</span>
            <input type="date" onchange="loadAll()" class="po-filter-inp" id="ak_tanggal_akhir" value="' . $akTanggalAkhir . '">
          </div>
        ',
        'filterIsi' => '
          <div>
            <label class="rt-field-label" for="ak_aktivitas">Aktivitas</label>
            <select class="rt-native" id="ak_aktivitas"><option value="" selected>Semua</option></select>
          </div>
          <div class="mt-2">
            <label class="rt-field-label" for="ak_sumber">Transaksi</label>
            <select class="rt-native" id="ak_sumber"><option value="" selected>Semua</option></select>
          </div>
          <div class="mt-2">
            <label class="rt-field-label" for="ak_pemakai">Pemakai</label>
            <select class="rt-native" id="ak_pemakai"><option value="" selected>Semua</option></select>
          </div>
        ',
        'filterJudul' => 'Filter Aktivitas Pemakai',
      ])

      <table id="tabel" class="data-table">
        <thead></thead>
        <tbody></tbody>
      </table>

    </div>
  </div>

</div>

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>

<script type="text/javascript">

// Kode Aktivitas (kolom DBLOGFILE.Aktivitas) -> label.
const AK_LABEL_AKTIVITAS = {
  'I': 'Tambah',
  'U': 'Koreksi',
  'D': 'Hapus',
  'oto': 'Otorisasi',
  'btloto': 'Batal Otorisasi',
  'btloto2': 'Batal Otorisasi 2',
  'oto2': 'Otorisasi 2',
};

// Kode Transaksi/Sumber (kolom DBLOGFILE.Sumber, mis. 'BBS', 'SSK') -> label. Diisi manual
// di sini (bukan dari database) -- kode yang belum didaftarkan di bawah tetap tampil apa
// adanya (raw code), jadi aman menambah baris baru kapan saja tanpa menunggu perubahan lain.
const AK_LABEL_SUMBER = {
  // 'BBS': 'Pembebanan Sample',
  // 'SSK': 'Serah Sample Konsinyasi',
};

function akIsiSelect (id, list, field, label) {
  label = label || {};
  let html = '<option value="" selected>Semua</option>';
  (list || []).forEach(function (item) {
    let v = item[field];
    html += '<option value="' + v + '">' + (label[v] || v) + '</option>';
  });
  document.getElementById(id).innerHTML = html;
}

function akMuatSelect () {
  $.ajax({
    url: "{!! url('aktivitaspemakailistselect') !!}",
    type: "get",
    data: {},
    success: function (res) {
      akIsiSelect('ak_aktivitas', res.listAktivitas, 'Aktivitas', AK_LABEL_AKTIVITAS);
      akIsiSelect('ak_sumber', res.listSumber, 'Sumber', AK_LABEL_SUMBER);
      akIsiSelect('ak_pemakai', res.listPemakai, 'Pemakai');
    }
  });
}

function akFormatTanggal (v) {
  if (!v) { return ''; }
  let d = new Date(String(v).replace(' ', 'T'));
  if (isNaN(d.getTime())) { return v; }
  let pad = function (n) { return String(n).padStart(2, '0'); };
  return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear()
    + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
}

function akRenderTabel (rows) {
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  $('#tabel').DataTable(MasterList.opsi({
    data: rows || [],
    columns: [
      { data: 'Tanggal', title: 'Tanggal', render: function (d) { return akFormatTanggal(d); } },
      { data: 'Pemakai', title: 'Pemakai', defaultContent: '' },
      { data: 'Aktivitas', title: 'Aktivitas', defaultContent: '' },
      { data: 'Sumber', title: 'Transaksi', defaultContent: '', render: function (d) { return AK_LABEL_SUMBER[d] || d; } },
      { data: 'NoBukti', title: 'No. Bukti', defaultContent: '' },
      { data: 'Keterangan', title: 'Keterangan', defaultContent: '' },
    ],
    columnDefs: [],
    order: []
  }));

  MasterList.selesai('#tabel');
}

// Dipanggil saat halaman dibuka, saat tanggal diganti, dan dari modal Filter
// (MasterList.filter.terapkan()/reset() di master-list.js memanggil window.loadAll()).
function loadAll () {
  $.ajax({
    url: "{!! url('aktivitaspemakailoadall') !!}",
    type: "get",
    data: {
      tanggal_awal: $('#ak_tanggal_awal').val(),
      tanggal_akhir: $('#ak_tanggal_akhir').val(),
      aktivitas: $('#ak_aktivitas').val(),
      sumber: $('#ak_sumber').val(),
      pemakai: $('#ak_pemakai').val(),
    },
    success: function (res) {
      akRenderTabel(res);
    },
    error: function () {
      alertify.error('Gagal memuat data aktivitas');
    }
  });
}

$(function () {
  akMuatSelect();
  loadAll();
});

</script>
@endsection
