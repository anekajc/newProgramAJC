<div id="page1" class="container-fluid mainpage">
<div class="container-fluid">

<div id="printContainer" style="display:none">




</div>
<div id="contentContainer" class="container-fluid">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="tb-report">
    <div class="content">

      <div class="toolbar">
        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="renderTabel()" style="width:200px">

        {{-- Jumlah baris per halaman. -1 = tampilkan semua data (tanpa pager) — lihat
             renderTabel()/onLenChange2() di bagian JS halaman ini. --}}
        <div class="len-wrap">
          <label for="tabelLen2">Tampilkan</label>
          <select id="tabelLen2" class="len-inp" onchange="onLenChange2()">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>

        <button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>

        {{-- margin-left:auto pada .action-group (report-table.css) mendorongnya ke ujung kanan
             toolbar, terpisah dari Filter di sebelah kiri. --}}
        <div class="action-group">
          <button type="button" class="btn btn-chip-biru" onclick="buttonAdd()">+ Bank</button>
        </div>
      </div>

      <div id="rtBar"></div>

      <div class="table-outer">
        <div class="table-wrap">
          <table id="mainTable" class="tb aksi-hover">
            <thead>
              <tr>
                <th class="rt-fixed-th">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel_data" class="text-left"></tbody>
          </table>
        </div>
        <div class="table-footer">
          <span id="footerLabel1">Belum ada data</span>
          <div class="pager-btns" id="pagerBtns1"></div>
        </div>
      </div>

      <div class="rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk sembunyikan kolom.
      </div>

    </div>
  </div>

</div>
</div>
</div>

{{-- modal filter — DILETAKKAN DI LUAR .tb-report, lihat catatan .tb-report * {margin:0;padding:0}
     di new-design-gudang-style-guide.md §3 / new-filter-modal-ui-guide.md §1. --}}
<div class="modal fade rt-filter" id="modalFilter">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-filter"></i>
          Filter Laporan
          <span class="rt-active-badge" id="filterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Pengaturan Laporan</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="modalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="modalOtorisasi">
                <option value="2">Semua</option>
                <option value="1">Sudah Otorisasi</option>
                <option value="0">Belum Otorisasi</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="resetAllFilters()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="applyModalFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
{{-- /modal filter --}}

