{{-- Toolbar tabel daftar menu Master (layout newmasterTest) - kotak cari, dropdown "Tampilkan",
     dan tombol Tambah, mengikuti toolbar purchasing/newpo.blade.php. Id #tabel_filter_visual /
     #tabel_length_visual / #AddVisibility sengaja sama dengan headerTableMaster lama, jadi kode
     halaman yang membaca/menyembunyikannya tetap jalan. Diikat oleh public/js/master-list.js.

     Parameter opsional:
       $tanpaTambah = true   -> tombol Tambah tidak ditampilkan
       $slotFilter           -> HTML tambahan (filter) yang diletakkan sebelum kotak cari
       $slotAksi             -> HTML tombol aksi pengganti tombol Tambah bawaan (mis. mastergiro) --}}
<link rel="stylesheet" href="{!! URL::asset('css/master-list.css') !!}?v={{ @filemtime(base_path('public/css/master-list.css')) ?: '1' }}">

<div id="masterListCfg" class="d-none"
     data-load-header="{{ url('globalfunctions_doLoadHeader') }}"
     data-simpan-header="{{ url('globalfunctions_doSimpanHeader') }}"></div>

<div class="po-toolbar">
  {!! $slotFilter ?? '' !!}

  <input type="search" id="tabel_filter_visual" class="po-search-inp" placeholder="Cari data" autocomplete="off">

  <div class="po-len-wrap">
    <label for="tabel_length_visual">Tampilkan</label>
    <select id="tabel_length_visual" class="po-len-inp">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
      <option value="-1">Semua</option>
    </select>
  </div>

  @if (!empty($slotAksi))
    <div class="po-toolbar-act">
      {!! $slotAksi !!}
    </div>
  @elseif (empty($tanpaTambah))
    <div class="po-toolbar-act">
      <button id="AddVisibility" class="btn btn-dpp-utama" type="button" onclick="buttonAdd()">Tambah</button>
    </div>
  @endif
</div>

{{-- Bar kolom tersembunyi + tombol "Reset kolom" - hanya terisi di tabel yang memakai
     MasterList.kolom() (kolom data lebih dari 5). --}}
<div id="rtBar"></div>
