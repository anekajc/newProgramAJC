@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Pemakaian Tambahan')

{{-- Pemakaian Tambahan -- sama dengan Pemakaian Bahan (daftar BP yang sama, DBPenyerahanBhn) tapi tanpa
     tab OutStanding SPK. Tambah = pilih BP yang sudah ada, lalu tambah Kode Barang lain (browsing
     DBBARANG IsAktif = 1) langsung ke BP itu (sp_PenyerahanBhn 'I', lihat
     ProduksiPemakaianTambahanController::tambahItem()). Kerangka tampilan disamakan dengan
     produksipemakaianbahan.blade.php / so.blade.php. --}}
@section('css')
<link rel="stylesheet"
    href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
<link rel="stylesheet"
    href="{!! URL::asset('css/master-list.css') !!}?v={{ @filemtime(base_path('public/css/master-list.css')) ?: '1' }}">
<link rel="stylesheet"
    href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">
<link rel="stylesheet"
    href="{!! URL::asset('css/produksi-oval.css') !!}?v={{ @filemtime(base_path('public/css/produksi-oval.css')) ?: '1' }}">
<style>

  /* Grid barang form BP (#page2) -- gaya ringkas sama dengan .pb-sub-table di halaman BOM/SPK. */
  .bp-grid {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }
  .bp-grid th,
  .bp-grid td {
    padding: 6px 10px;
    border-bottom: 1px solid #f3f4f6;
  }
  .bp-grid th {
    text-align: left;
    background: #f9fafb;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: .04em;
    border-bottom: 1px solid #e5e7eb;
  }
  .bp-grid .text-right { text-align: right; }

  /* Kolom QNT di grid form BP bisa diedit langsung (input di dalam sel). Selektor diawali
     #formBsGrid supaya menang atas aturan tinggi 38px untuk .form-control di partial tampilan. */
  #formBsGrid .bp-grid .bp-qnt-input {
    display: inline-block;
    width: 120px;
    height: 30px;
    padding: 2px 8px;
    text-align: right;
    font-size: 13px;
  }
  #formBsGrid .bp-grid .bp-qnt-input.is-invalid-qnt {
    border-color: #dc3545;
    background: #fff5f5;
  }
</style>

@include('marketing.partials.tampilan-purchasing-css')
@endsection

@section('content')

{{-- Modal Filter -- disalin dari master/partials/toolbarMaster.blade.php (dipindah ke <body> oleh MasterList). --}}
<div class="modal fade rt-filter" id="modalFilterMaster">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter BP
          <span class="rt-active-badge" id="masterFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterMaster').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <label for="filterOtorisasi">Status Otorisasi</label>
          <select id="filterOtorisasi" class="form-control">
            <option value="">Semua</option>
            <option value="0">Belum Otorisasi</option>
            <option value="1">Sudah Otorisasi</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="MasterList.filter.reset()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" onclick="$('#modalFilterMaster').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="MasterList.filter.terapkan()">Terapkan</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="page1">

  <div id="contentContainer" class="">
    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
    <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
    <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
    <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

    <div class="card">
      <div class="card-body" style="padding:0;">
        <div class="row">
          <div class="col-md-12">
            <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
              <div class="po-toolbar">
                <input type="search" id="bpSearch" class="po-search-inp" placeholder="Cari data">
                <div class="po-len-wrap">
                  <label for="bpLen">Tampilkan</label>
                  <select id="bpLen" class="po-len-inp">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="-1">Semua</option>
                  </select>
                </div>
                {{-- Tombol Filter hijau (modal #modalFilterMaster, MasterList.filter) -- status otorisasi.
                     Diterapkan di sisi klien oleh renderTabelBP() lewat window.loadAll(). --}}
                <button class="po-btn-filter" type="button" id="btnFilterMaster" onclick="$('#modalFilterMaster').modal('show')">
                  <i class="bi bi-funnel"></i> Filter
                </button>
                <span class="po-filter-aktif" id="masterFilterAktif"></span>
              </div>
              <div id="rtBarBP"></div>
              <table id="tabel7" class="data-table">
                <thead id="tabel_bp_header" style="white-space:nowrap;"></thead>
                <tbody id="tabel_data_bp" class="text-left"></tbody>
              </table>
              <div class="po-rt-hint">
                <i class="bi bi-info-circle"></i>
                Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
                untuk menyembunyikan kolom atau mengatur jumlah desimal.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- ============================================================
     PAGE 2 -- Form BP (Koreksi / Detail). Header dikunci (Tanggal, Gudang, No. SPK dari BP-nya). Di mode
     Koreksi, QNT tiap baris bisa diedit dan panel "Tambah Barang" muncul: Kode Barang dipilih dari
     DBBARANG yang IsAktif = 1, Quantity diisi, Satuan ikut barang. Baris yang ditambah lewat panel ini
     bisa dihapus (tombol di kolom Actions); baris dari SPK tidak.
     ============================================================ --}}
<div id="page2" class="container-fluid" style="display: none">
<div id="formBsGrid">
  <div class="row">
    <div class="col-6 text-left">
      <h5 id="bpFormJudul" style="margin:0; font-weight:700;">Koreksi BP</h5>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="bpTutupForm()">
        Close
      </button>
    </div>
  </div>

  <div class="modal-body" id="bpFormBody" style="padding-left:0; padding-right:0;">
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label>WIP/BJ</label>
          <select class="form-control" id="input_bp_tipe" disabled>
            <option value="WIP">WIP</option>
            <option value="BJ">BJ</option>
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Nomor Urut</label>
          <input type="text" class="form-control" id="input_bp_nourut" disabled>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>Nomor Bukti</label>
          <input type="text" class="form-control" id="input_bp_nobukti" disabled>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Tanggal</label>
          <input type="date" class="form-control" id="input_bp_tanggal" disabled>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Gudang</label>
          <select class="form-control" id="input_bp_gudang" disabled></select>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label>No. SPK</label>
          <input type="text" class="form-control" id="input_bp_nospk" disabled>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Shift</label>
          <select class="form-control" id="input_bp_shift" disabled>
            <option value="">-</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-12" style="overflow:auto;">
        <table class="bp-grid">
          <thead>
            <tr><th>Kode Barang</th><th>Nama Barang</th><th class="text-right">QNT</th><th>Sat</th><th class="bp-aksi">Actions</th></tr>
          </thead>
          <tbody id="tabel_data_bpform"><tr><td colspan="5" class="text-center text-muted">Belum ada barang</td></tr></tbody>
          <tfoot>
            <tr><td colspan="2"></td><td class="text-right"><strong id="bpFormTotal">0.00</strong></td><td colspan="2"></td></tr>
          </tfoot>
        </table>
      </div>
    </div>

    {{-- Panel Tambah Barang (hanya mode Koreksi) -- barang di luar SPK. Satuan ikut Sat1 barang. --}}
    <div id="bpTambahPanel" class="mt-3" style="display:none; border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
      <div class="row">
        <div class="col-md-4">
          <label>Kode Barang</label>
          <div class="input-group">
            <input type="text" class="form-control" id="input_tbh_kodebrg" readonly placeholder="Pilih barang">
            <div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="bpPilihBarang()" title="Cari Kode Barang"><i class="bi bi-search"></i></button></div>
          </div>
        </div>
        <div class="col-md-4">
          <label>Nama Barang</label>
          <input type="text" class="form-control" id="input_tbh_namabrg" disabled>
        </div>
        <div class="col-md-2">
          <label>Quantity</label>
          <input type="text" inputmode="decimal" class="form-control" id="input_tbh_qnt" value="0.00">
        </div>
        <div class="col-md-2">
          <label>Satuan</label>
          <input type="text" class="form-control" id="input_tbh_sat" disabled>
        </div>
      </div>
      <div class="text-right mt-2">
        <button type="button" class="btn btn-sm btn-danger" onclick="bpTambahBatal()">Batal</button>
        <button type="button" class="btn btn-sm btn-chip-biru" onclick="bpTambahSimpan()">Simpan Barang</button>
      </div>
    </div>

    <div class="text-right mt-3" id="bpFormAksi">
      <button type="button" class="btn btn-sm btn-danger" onclick="bpTutupForm()">Batal</button>
      <button type="button" class="btn btn-sm btn-chip-biru" onclick="bpSimpanKoreksi()">Simpan</button>
    </div>
  </div>
</div>
</div>

<!-- Picker BP -- BP yang belum diotorisasi di periode ini; dipilih dengan klik baris. -->
<div class="modal fade" id="formPilihBP" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih BP</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">
              <table id="tabel_pilihbp" class="data-table">
                <thead><tr><th style="padding: 4px 12px;" scope="col">No. Bukti</th><th style="padding: 4px 12px;" scope="col">Tanggal</th><th style="padding: 4px 12px;" scope="col">Gudang</th></tr></thead>
                <tbody id="tabel_data_pilihbp" class="text-left"><tr><td>-</td><td>-</td><td>-</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-danger" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

<!-- Picker Kode Barang -- DBBARANG IsAktif = 1, DataTable serverSide (produksipemakaiantambahanlistbarang). -->
<div class="modal fade" id="formPilihBarangTbh" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Kode Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-2">
          <div class="row">
            <div class="col-12">
              <input type="text" class="form-control" id="input_cari_barangtbh" placeholder="Ketik Kode/Nama Barang lalu Enter&hellip;">
            </div>
          </div>
        </div>
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">
              <table id="tabel_barangtbh" class="data-table">
                <thead><tr><th style="padding: 4px 12px;" scope="col">Kode</th><th style="padding: 4px 12px;" scope="col">Nama</th><th style="padding: 4px 12px;" scope="col">Sat</th></tr></thead>
                <tbody id="tabel_data_barangtbh" class="text-left"><tr><td>-</td><td>-</td><td>-</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-danger" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
@include('marketing.partials.tampilan-purchasing-js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
{{-- master-list.js membaca alamat simpan/muat susunan kolom dari #masterListCfg (biasanya ikut
     toolbarMaster, yang tidak dipakai di halaman ini). --}}
<div id="masterListCfg" class="d-none"
     data-load-header="{{ url('globalfunctions_doLoadHeader') }}"
     data-simpan-header="{{ url('globalfunctions_doSimpanHeader') }}"></div>

<script type="text/javascript">

// Kolom tabel BP -- bisa digeser & disembunyikan (MasterList.kolom(), disimpan per user di
// DBSIMPANHEADER dengan href 'produksipemakaiantambahan', mode 2). [field, label, tampil, tipe, total, desimal]
const BP_KOLOM = [
  ['NoBukti',      'No. Bukti',         1, 'varchar', 0, 0],
  ['Tanggal',      'Tanggal',           1, 'date',    0, 0],
  ['IsOtorisasi1', 'Otorisasi',         1, 'varchar', 0, 0],
  ['OtoUser1',     'Otorisasi User',    1, 'varchar', 0, 0],
  ['TglOto1',      'Otorisasi Date',    1, 'date',    0, 0],
]

let dataBP = []

// Sel khusus: kolom Otorisasi sebagai centang hijau / silang merah (sama dengan SO).
const BP_KHUSUS = {
  IsOtorisasi1: function (item) {
    return '<td class="text-center">' + (Number(item.IsOtorisasi1)
      ? '<i class="bi bi-check2 text-success" title="Sudah Otorisasi" style="-webkit-text-stroke-width: 2px;"></i>'
      : '<i class="bi bi-x-lg text-danger" title="Belum Otorisasi" style="-webkit-text-stroke-width: 1px;"></i>') + '</td>'
  },
}

function pbOpsi (idLen) {
  return MasterList.opsi({ pageLength: Number($('#' + idLen).val()) || 10 })
}

function renderTabelBP () {
  if ($.fn.DataTable.isDataTable('#tabel7')) {
    $('#tabel7').DataTable().destroy()
  }

  let cols = MasterList.kolomTampil('#tabel7')
  document.getElementById('tabel_bp_header').innerHTML = MasterList.headHtml(cols, '#tabel7')

  let rowTable = ''
  let filterOto = $('#filterOtorisasi').val()
  dataBP.forEach((item) => {
    if (filterOto !== '' && filterOto != null && Number(item.IsOtorisasi1 || 0) !== Number(filterOto)) { return }
    // Pola sama dengan Pemakaian Bahan: BP yang sudah diotorisasi hanya bisa dilihat (Detail) atau
    // dibatalkan otorisasinya (kunci merah); yang belum boleh Edit (Koreksi + tambah barang) dan Otorisasi.
    let sudahOto = Number(item.IsOtorisasi1) === 1
    let aksi = `<button title="Detail" class="btn-action-sm btn-action-warning" type="button" onclick="buttonDetail('${item.NoBukti}')"><i class="bi bi-info"></i></button>`
    if (sudahOto) {
      aksi += `<button title="Batal Otorisasi" class="btn-action-sm btn-action-danger" type="button" onclick="buttonBatalOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>`
    } else {
      aksi += `
        <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.NoBukti}')"><i class="bi bi-pen"></i></button>
        <button title="Otorisasi" class="btn-action-sm btn-action-info" type="button" onclick="buttonOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>`
    }
    aksi = `<div class="action-buttons-wrap">${aksi}</div>`
    rowTable += MasterList.baris(item, cols, aksi, BP_KHUSUS)
  })

  document.getElementById('tabel_data_bp').innerHTML = rowTable
  let dt = $('#tabel7').DataTable(pbOpsi('bpLen'))
  if ($('#bpSearch').val()) { dt.search($('#bpSearch').val()).draw() }
}

// Toolbar mengendalikan tabelnya sendiri (cari ditunda 300 ms, sama seperti toolbar Master).
let pbTundaCari = null
$(document).on('input', '#bpSearch', function () {
  let kata = this.value
  clearTimeout(pbTundaCari)
  pbTundaCari = setTimeout(function () {
    if ($.fn.DataTable.isDataTable('#tabel7')) { $('#tabel7').DataTable().search(kata).draw() }
  }, 300)
})
$(document).on('change', '#bpLen', function () {
  if ($.fn.DataTable.isDataTable('#tabel7')) { $('#tabel7').DataTable().page.len(Number(this.value)).draw() }
})

// ===================== Tambah (pilih BP) =====================

// Tombol Tambah -- picker BP yang belum diotorisasi; klik baris membuka BP itu di mode Koreksi
// (di situ panel Tambah Barang aktif).
function buttonAddBP () {
  let belum = dataBP.filter((b) => Number(b.IsOtorisasi1) !== 1)
  let rowTable = ''
  belum.forEach((b) => {
    rowTable += `
      <tr class="pick-row" onclick="bpPilihBPSet('${b.NoBukti}')">
        <td>${b.NoBukti}</td>
        <td>${b.Tanggal ? String(b.Tanggal).substring(0, 10) : ''}</td>
        <td>${b.KodeGdg ?? ''}</td>
      </tr>`
  })
  if (!belum.length) {
    rowTable = '<tr><td colspan="3" class="text-center text-muted">Tidak ada BP yang belum diotorisasi</td></tr>'
  }
  if ($.fn.DataTable.isDataTable('#tabel_pilihbp')) { $('#tabel_pilihbp').DataTable().destroy() }
  document.getElementById('tabel_data_pilihbp').innerHTML = rowTable
  if (typeof pickerKasInit === 'function') { pickerKasInit('tabel_pilihbp') }
  $('#formPilihBP').modal('show')
}

function bpPilihBPSet (nobukti) {
  $('#formPilihBP').modal('hide')
  bpMuatBP(nobukti, 'edit')
}

// ===================== Form BP (#page2) =====================

// Mode form: 'edit' (Koreksi -- QNT bisa diubah, panel Tambah Barang aktif) atau 'lihat' (Detail --
// semua dikunci, tombol Batal/Simpan dan panel disembunyikan, tinggal Close).
let bpMode = 'edit'

function bpSetMode (mode) {
  bpMode = mode
  document.getElementById('bpFormJudul').textContent = mode === 'edit' ? 'Koreksi BP' : 'Detail BP'
  $('#bpFormAksi').toggle(mode !== 'lihat')
  $('#bpTambahPanel').hide()
  if (mode === 'edit') { bpTambahBatal(); $('#bpTambahPanel').show() }
}

function buttonDetail (nobukti) {
  bpMuatBP(nobukti, 'lihat')
}

function buttonEdit (nobukti) {
  bpMuatBP(nobukti, 'edit')
}

// Muat satu BP ke form (ProduksiPemakaianTambahanController::loadDetailBP()). pertahankan (opsional) --
// {Urut: teks QNT} yang sedang diedit user, dipulihkan setelah grid digambar ulang (dipakai setelah
// Simpan Barang / Hapus supaya QNT yang belum disimpan tidak hilang).
function bpMuatBP (nobukti, mode, pertahankan) {
  $.ajax({
    url: "{!! url('produksipemakaiantambahanloaddetailbp') !!}",
    type: "get",
    dataType: "json",
    data: { nobukti },
    success: function (res) {
      if (!Array.isArray(res) || !res.length) {
        alertify.warning('BP tidak ditemukan')
        return
      }
      let h = res[0]
      bpSetMode(mode)
      document.getElementById('input_bp_tipe').value = /\/BJ$/.test(h.NoBukti) ? 'BJ' : 'WIP'
      document.getElementById('input_bp_nourut').value = h.NoUrut ?? ''
      document.getElementById('input_bp_nobukti').value = h.NoBukti
      document.getElementById('input_bp_tanggal').value = h.Tanggal ? String(h.Tanggal).substring(0, 10) : ''
      document.getElementById('input_bp_nospk').value = h.NoBPPB ?? ''
      bpIsiGudang(h.KodeGdg)

      // Baris tanpa Urut = BP tanpa detail (LEFT JOIN) -- grid dikosongkan.
      let rows = res.filter((r) => r.Urut !== null && r.Urut !== undefined)
      if (!rows.length) {
        bpKosongkanGrid()
      } else {
        let editable = mode === 'edit'
        let jumlah = rows.length
        let rowTable = ''
        rows.forEach((item) => {
          // Hanya baris tambahan (UrutSPK = 0) yang boleh dihapus, dan bukan baris terakhir BP.
          let bolehHapus = editable && Number(item.UrutSPK) === 0 && jumlah > 1
          let aksi = bolehHapus
            ? `<button title="Hapus" class="btn-action-sm btn-action-danger" type="button" onclick="bpHapusItem(${item.Urut}, '${String(item.KodeBrg).replace(/'/g, '')}')"><i class="bi bi-trash"></i></button>`
            : ''
          rowTable += `
            <tr>
              <td>${item.KodeBrg ?? ''}</td>
              <td>${item.NamaBrg ?? ''}</td>
              <td class="text-right"><input type="text" inputmode="decimal" class="form-control bp-qnt-input" data-urut="${item.Urut}" value="${formatAngkaBP(Number(item.Qnt ?? 0))}"${editable ? '' : ' disabled'}></td>
              <td>${item.Sat ?? ''}</td>
              <td class="bp-aksi">${aksi}</td>
            </tr>`
        })
        document.getElementById('tabel_data_bpform').innerHTML = rowTable
        if (pertahankan) {
          $('#tabel_data_bpform .bp-qnt-input').each(function () {
            let u = $(this).data('urut')
            if (pertahankan[u] !== undefined) { this.value = pertahankan[u] }
          })
        }
        $('.bp-aksi').toggle(mode === 'edit')
        bpHitungTotal()
      }

      $('#page1').hide()
      $('#page2').show()
    },
    error: function () {
      alertify.error('Gagal memuat BP')
    }
  })
}

// QNT yang sedang diisi di grid -> {Urut: teks}.
function bpAmbilNilaiGrid () {
  let hasil = {}
  $('#tabel_data_bpform .bp-qnt-input').each(function () { hasil[$(this).data('urut')] = this.value })
  return hasil
}

// ===================== Panel Tambah Barang =====================

let tabelBarangTbhDT = null

function bpPilihBarang () {
  document.getElementById('input_cari_barangtbh').value = ''

  if (!tabelBarangTbhDT) {
    document.getElementById('tabel_data_barangtbh').innerHTML = ''
    tabelBarangTbhDT = $('#tabel_barangtbh').DataTable({
      serverSide: true,
      processing: true,
      searching: true,
      ordering: false,
      dom: 'ltip',
      ajax: {
        url: "{!! url('produksipemakaiantambahanlistbarang') !!}",
        type: 'GET',
      },
      columns: [
        { data: 'KODEBRG' },
        { data: 'NAMABRG' },
        { data: 'SAT1' },
      ],
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      pageLength: 10,
      language: {
        lengthMenu: 'Tampilkan _MENU_',
        emptyTable: 'Tidak ada data',
        zeroRecords: 'Tidak ada data yang cocok dengan pencarian',
        processing: 'Memuat...'
      },
      createdRow: function (row, data) {
        $(row).addClass('pick-row').on('click', function () {
          document.getElementById('input_tbh_kodebrg').value = data.KODEBRG
          document.getElementById('input_tbh_namabrg').value = data.NAMABRG || ''
          document.getElementById('input_tbh_sat').value = data.SAT1 || ''
          $('#formPilihBarangTbh').modal('hide')
          document.getElementById('input_tbh_qnt').focus()
        })
      }
    })
  }

  tabelBarangTbhDT.search('').draw()
  $('#formPilihBarangTbh').modal('show')
}

$(document).on('keyup', '#input_cari_barangtbh', function (e) {
  if (e.key === 'Enter' && tabelBarangTbhDT) {
    tabelBarangTbhDT.search($(this).val()).draw()
  }
})

function bpTambahBatal () {
  document.getElementById('input_tbh_kodebrg').value = ''
  document.getElementById('input_tbh_namabrg').value = ''
  document.getElementById('input_tbh_sat').value = ''
  document.getElementById('input_tbh_qnt').value = '0.00'
}

// Simpan Barang -- tambah satu baris ke BP ini (tambahItem()). QNT lain yang sedang diedit di grid
// dipertahankan (belum disimpan sampai tombol Simpan di bawah).
function bpTambahSimpan () {
  let kodebrg = $('#input_tbh_kodebrg').val()
  if (!kodebrg) {
    alertify.warning('Pilih Kode Barang terlebih dahulu')
    return
  }
  let n = angkaBP($('#input_tbh_qnt').val())
  if (isNaN(n) || n <= 0) {
    alertify.warning('Qnt harus berupa angka lebih dari 0')
    return
  }

  let nobukti = $('#input_bp_nobukti').val()
  let simpan = bpAmbilNilaiGrid()
  $.ajax({
    url: "{!! url('produksipemakaiantambahantambahitem') !!}",
    type: "post",
    dataType: "json",
    data: { _token: $('#_token').val(), nobukti: nobukti, kodebrg: kodebrg, qnt: n },
    success: function () {
      alertify.success('Barang berhasil ditambahkan')
      bpTambahBatal()
      bpMuatBP(nobukti, 'edit', simpan)
    },
    error: function (xhr) {
      alertify.error(xhr.responseJSON?.message || 'Gagal menambah barang')
    }
  })
}

function bpHapusItem (urut, kodebrg) {
  let nobukti = $('#input_bp_nobukti').val()
  alertify.confirm('Hapus Barang', 'Apakah yakin ingin menghapus "' + kodebrg + '" dari BP ini?',
    function () {
      let simpan = bpAmbilNilaiGrid()
      delete simpan[urut]
      $.ajax({
        url: "{!! url('produksipemakaiantambahanhapusitem') !!}",
        type: "post",
        dataType: "json",
        data: { _token: $('#_token').val(), nobukti: nobukti, urut: urut },
        success: function () {
          alertify.success('Barang berhasil dihapus')
          bpMuatBP(nobukti, 'edit', simpan)
        },
        error: function (xhr) {
          alertify.error(xhr.responseJSON?.message || 'Gagal menghapus barang')
        }
      })
    },
    function () {}
  )
}

// ===================== Otorisasi =====================

// Otorisasi langsung dari baris tabel (tanpa dialog konfirmasi) -- sama dengan Pemakaian Bahan.
function buttonOtorisasi (nobukti) {
  if (!Number($('#akses_isotorisasi1').val())) {
    alertify.warning('No access')
    return
  }

  $.ajax({
    url: "{!! url('produksipemakaiantambahanspotorisasi') !!}",
    type: "post",
    data: { _token: $('#_token').val(), nobukti },
    success: function (res) {
      if (res > 0) {
        alertify.success('Berhasil otorisasi')
        loadBP()
      } else {
        alertify.warning('Gagal otorisasi (BP sudah diotorisasi atau tidak ditemukan)')
      }
    },
    error: function () {
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

// Batal otorisasi -- keterangan wajib diisi, dikirim sebagai 'pket' dan dicatat LoggingData.
function buttonBatalOtorisasi (nobukti) {
  if (!Number($('#akses_isotorisasi1').val())) {
    alertify.warning('No access')
    return
  }

  alertify.prompt('Masukkan keterangan batal otorisasi nomor   ' + nobukti, '',
    function (evt, value) {
      let pket = (value || '').trim()
      if (pket == '') {
        alertify.warning('Keterangan harus diisi.')
        return false
      }

      $.ajax({
        url: "{!! url('produksipemakaiantambahanspbatalotorisasi') !!}",
        type: "post",
        data: { _token: $('#_token').val(), nobukti, pket },
        success: function (res) {
          if (res > 0) {
            alertify.success('Berhasil batal otorisasi')
            loadBP()
          } else {
            alertify.warning('Gagal batal otorisasi (BP belum diotorisasi)')
          }
        },
        error: function () {
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
      })
    },
    function () {
      alertify.error('Action cancelled')
    })
}

// Simpan Koreksi BP -- hanya Qnt per baris (updateBP()).
function bpSimpanKoreksi () {
  let inputs = $('#tabel_data_bpform .bp-qnt-input')
  let qnt = {}
  let salah = null
  inputs.each(function () {
    let n = angkaBP(this.value)
    if (isNaN(n) || n <= 0) {
      this.classList.add('is-invalid-qnt')
      if (!salah) { salah = this }
    } else {
      qnt[$(this).data('urut')] = n
    }
  })
  if (!inputs.length) {
    alertify.warning('BP ini tidak punya barang')
    return
  }
  if (salah) {
    alertify.warning('Qnt harus berupa angka lebih dari 0')
    salah.focus()
    return
  }

  $.ajax({
    url: "{!! url('produksipemakaiantambahanupdatebp') !!}",
    type: "post",
    dataType: "json",
    data: { _token: $('#_token').val(), nobukti: $('#input_bp_nobukti').val(), qnt: qnt },
    success: function (res) {
      alertify.success('Koreksi BP ' + res.nobukti + ' berhasil disimpan')
      bpTutupForm()
      loadBP()
    },
    error: function (xhr) {
      alertify.error(xhr.responseJSON?.message || 'Gagal menyimpan koreksi BP')
    }
  })
}

function bpTutupForm () {
  $('#page2').hide()
  $('#page1').show()
  loadBP()
  if (window.mktAturTinggiTabel) { window.mktAturTinggiTabel() }
}

function bpKosongkanGrid () {
  document.getElementById('tabel_data_bpform').innerHTML = '<tr><td colspan="5" class="text-center text-muted">Belum ada barang</td></tr>'
  document.getElementById('bpFormTotal').textContent = '0.00'
}

// Dropdown Gudang (DBGUDANG aktif) -- hanya untuk menampilkan gudang BP (terkunci).
function bpIsiGudang (terpilih) {
  $.ajax({
    url: "{!! url('produksipemakaiantambahanlistgudang') !!}",
    type: "get",
    dataType: "json",
    success: function (res) {
      let opts = '<option value="">-</option>'
      let ada = false
      ;(Array.isArray(res) ? res : []).forEach((item) => {
        if (item.KODEGDG === terpilih) { ada = true }
        opts += `<option value="${item.KODEGDG}">${item.KODEGDG} - ${item.NAMA ?? ''}</option>`
      })
      if (terpilih && !ada) { opts += `<option value="${terpilih}">${terpilih}</option>` }
      let el = document.getElementById('input_bp_gudang')
      el.innerHTML = opts
      el.value = terpilih || ''
    },
    error: function () {
      alertify.error('Gagal memuat daftar Gudang (coba muat ulang halaman)')
    }
  })
}

function formatAngkaBP (n) {
  return Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

// Nilai angka dari teks "1,234.50" -> 1234.5 (NaN kalau kosong/bukan angka).
function angkaBP (teks) {
  let t = String(teks ?? '').replace(/,/g, '').trim()
  return t === '' ? NaN : Number(t)
}

function bpHitungTotal () {
  let total = 0
  $('#tabel_data_bpform .bp-qnt-input').each(function () {
    let n = angkaBP(this.value)
    if (!isNaN(n)) { total += n }
  })
  document.getElementById('bpFormTotal').textContent = formatAngkaBP(total)
}

// Edit QNT langsung di grid: hanya angka dan titik (maks. 2 desimal); saat keluar kolom dirapikan ke
// format 2 desimal. Kolom kosong/bukan angka/<= 0 diberi tanda merah dan ditolak saat Simpan.
$(document).on('input', '#tabel_data_bpform .bp-qnt-input', function () {
  let v = this.value.replace(/[^0-9.]/g, '')
  let titik = v.indexOf('.')
  if (titik !== -1) {
    v = v.slice(0, titik + 1) + v.slice(titik + 1).replace(/\./g, '').slice(0, 2)
  }
  this.value = v
  this.classList.remove('is-invalid-qnt')
  bpHitungTotal()
})

$(document).on('blur', '#tabel_data_bpform .bp-qnt-input', function () {
  let n = angkaBP(this.value)
  if (isNaN(n)) { this.classList.add('is-invalid-qnt'); return }
  this.value = formatAngkaBP(n)
  this.classList.toggle('is-invalid-qnt', n <= 0)
  bpHitungTotal()
})

// Enter di kolom QNT pindah ke QNT baris berikutnya (seperti grid desktop).
$(document).on('keydown', '#tabel_data_bpform .bp-qnt-input', function (e) {
  if (e.key !== 'Enter') { return }
  e.preventDefault()
  let semua = $('#tabel_data_bpform .bp-qnt-input')
  let i = semua.index(this)
  if (i < semua.length - 1) { semua.eq(i + 1).focus().select() } else { this.blur() }
})

// Quantity panel Tambah Barang: hanya angka dan titik (maks. 2 desimal), dirapikan saat keluar kolom.
$(document).on('input', '#input_tbh_qnt', function () {
  let v = this.value.replace(/[^0-9.]/g, '')
  let titik = v.indexOf('.')
  if (titik !== -1) {
    v = v.slice(0, titik + 1) + v.slice(titik + 1).replace(/\./g, '').slice(0, 2)
  }
  this.value = v
})
$(document).on('blur', '#input_tbh_qnt', function () {
  let n = angkaBP(this.value)
  this.value = formatAngkaBP(isNaN(n) ? 0 : n)
})

// Dipanggil MasterList.filter.terapkan()/reset() -- filter otorisasi diterapkan di sisi klien.
function loadAll () {
  renderTabelBP()
}

function loadBP () {
  $.ajax({
    url: "{!! url('produksipemakaiantambahanloadbp') !!}",
    type: "get",
    data: { tahun: $('#periode_tahun').val(), bulan: $('#periode_bulan').val() },
    success: function (res) {
      dataBP = res
      renderTabelBP()
    },
    error: function () {
      alertify.error('Gagal memuat BP')
    }
  })
}

// Modal pemilih disamakan dengan modal pemilih menu Kas/SO/PO (class picker-kas + tombol Batal).
document.addEventListener('DOMContentLoaded', function () {
  jQuery('#formPilihBP, #formPilihBarangTbh').addClass('picker-kas')
  jQuery('#formPilihBP .modal-footer .btn, #formPilihBarangTbh .modal-footer .btn').filter(function () {
    return jQuery.trim(jQuery(this).text()) === 'Batal'
  }).addClass('picker-kas-batal')
})

window.onload = function () {
  MasterList.kolom({ href: 'produksipemakaiantambahan', mode: 2, table: '#tabel7', bar: '#rtBarBP', kolom: BP_KOLOM, onChange: renderTabelBP })
  loadBP()
  MasterList.pakaiKolom('#tabel7')
}

</script>
@endsection
