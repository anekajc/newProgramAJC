@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Pemakaian Bahan')

{{-- Pemakaian Bahan (Penyerahan Bahan / BP) -- dua tab mengikuti tampilan desktop lama: "OutStanding
     SPK" (bahan baku SPK yang belum habis dipakai, data asli dari vwOutSPK_Pakai) dan "BP"
     (daftar bukti penyerahan bahan -- masih PLACEHOLDER, menunggu query/SP dari senior).
     Kerangka tampilan disamakan dengan marketing/so.blade.php: #page1 > #contentContainer, kartu
     tab bergaya pil (.tab-card/.custom-tabs), lalu kartu berisi .tab-content dengan .po-toolbar
     SENDIRI di tiap tab (cari + Tampilkan), #rtBar per tabel, tabel .data-table, dan petunjuk
     .po-rt-hint. Gaya/perilaku visual datang dari dua partial yang sama dengan SO
     (tampilan-purchasing-css / tampilan-purchasing-js). Header kolom bisa digeser/disembunyikan
     lewat MasterList.kolom() (satu instance per tabel). Baru tahap browse. --}}
@section('css')
<link rel="stylesheet"
    href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
<link rel="stylesheet"
    href="{!! URL::asset('css/master-list.css') !!}?v={{ @filemtime(base_path('public/css/master-list.css')) ?: '1' }}">
<link rel="stylesheet"
    href="{!! URL::asset('css/produksi-oval.css') !!}?v={{ @filemtime(base_path('public/css/produksi-oval.css')) ?: '1' }}">
<style>

  /* Grid barang form Tambah BP (#page2) -- gaya ringkas sama dengan .pb-sub-table di halaman BOM/SPK. */
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

  /* Kolom QNT di grid form Tambah BP bisa diedit langsung (input di dalam sel). Selektor diawali
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

<div id="page1">

  <div id="contentContainer" class="">
    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
    <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
    <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
    <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

    <div class="card mb-3 tab-card">
      <div class="card-body">
        <div class="nav nav-tabs border-0 custom-tabs" id="pbTab" role="tablist">

          <a class="nav-item nav-link active"
            id="tab-os-btn"
            data-toggle="tab"
            href="#tab-os"
            role="tab"
            aria-controls="tab-os"
            aria-selected="true">
              OutStanding SPK
          </a>

          <a class="nav-item nav-link"
            id="tab-bp-btn"
            data-toggle="tab"
            href="#tab-bp"
            role="tab"
            aria-controls="tab-bp"
            aria-selected="false">
              BP
          </a>

        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body" style="padding:0;">
        <div class="tab-content" id="pbTabContent">

          {{-- OutStanding SPK: toolbar cuma cari + Tampilkan (tidak ada Tambah/Filter). --}}
          <div class="tab-pane fade show active" id="tab-os" role="tabpanel" aria-labelledby="tab-os-btn">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <input type="search" id="osSearch" class="po-search-inp" placeholder="Cari data">
                    <div class="po-len-wrap">
                      <label for="osLen">Tampilkan</label>
                      <select id="osLen" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                  </div>
                  <div id="rtBarOS"></div>
                  <table id="tabel" class="data-table">
                    <thead id="tabel_os_header" style="white-space:nowrap;"></thead>
                    <tbody id="tabel_data_os" class="text-left"></tbody>
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

          {{-- BP (placeholder): cari + Tampilkan + Tambah. --}}
          <div class="tab-pane fade" id="tab-bp" role="tabpanel" aria-labelledby="tab-bp-btn">
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
                    <div class="po-toolbar-act">
                      <button id="AddVisibility" class="btn btn-dpp-utama" type="button" onclick="buttonAddBP()">Tambah</button>
                    </div>
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

  </div>
</div>

{{-- ============================================================
     PAGE 2 -- Form Tambah BP (Penyerahan Bahan), mengikuti form desktop lama: WIP/BJ, Nomor Urut,
     Nomor Bukti, Tanggal, Gudang, No. SPK, Shift, tombol "Ambil SPK", lalu grid Kode Barang/
     Nama Barang/QNT/Sat. Pola #page1/#page2 sama dengan SO (daftar disembunyikan, form mengambil
     alih, tombol Close). Baru kerangka: Ambil SPK (SP_INSPEMAKAI belum ada di database) dan Simpan
     masih placeholder; Shift tidak punya kolom/tabel di database ini jadi dinonaktifkan.
     ============================================================ --}}
<div id="page2" class="container-fluid" style="display: none">
<div id="formBsGrid">
  <div class="row">
    <div class="col-6 text-left">
      <h5 id="bpFormJudul" style="margin:0; font-weight:700;">Tambah BP</h5>
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
          <select class="form-control" id="input_bp_tipe" onchange="bpGenerateNoBukti()">
            <option value="WIP">WIP</option>
            <option value="BJ">BJ</option>
          </select>
        </div>
      </div>
      <input type="hidden" id="input_bp_nourut">
      <div class="col-md-3">
        <div class="form-group">
          <label>Nomor Bukti</label>
          <input type="text" class="form-control" id="input_bp_nobukti" disabled>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Tanggal</label>
          <input type="date" class="form-control" id="input_bp_tanggal">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Gudang</label>
          <select class="form-control" id="input_bp_gudang"></select>
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
          <select class="form-control" id="input_bp_shift">
            <option value="0">-</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
          </select>
        </div>
      </div>
      {{-- Tombol Ambil SPK DISEMBUNYIKAN sementara (d-none) -- bukan dihapus: Ambil SPK sudah berjalan
           otomatis saat form dibuka dari "+". Untuk menampilkannya lagi ganti d-none jadi d-flex. --}}
      <div class="col-md-2 d-none align-items-end">
        <div class="form-group">
          <button type="button" class="btn btn-chip-biru" onclick="bpAmbilSPK()"><i class="bi bi-check2"></i> Ambil SPK</button>
        </div>
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-12" style="overflow:auto;">
        <table class="bp-grid">
          <thead>
            <tr><th>Kode Barang</th><th>Nama Barang</th><th class="text-right">QNT</th><th>Sat</th></tr>
          </thead>
          <tbody id="tabel_data_bpform"><tr><td colspan="4" class="text-center text-muted">Belum ada barang</td></tr></tbody>
          <tfoot>
            <tr><td colspan="2"></td><td class="text-right"><strong id="bpFormTotal">0.00</strong></td><td></td></tr>
          </tfoot>
        </table>
      </div>
    </div>

    <div class="text-right mt-3" id="bpFormAksi">
      <button type="button" class="btn btn-sm btn-danger" onclick="bpTutupForm()">Batal</button>
      <button type="button" class="btn btn-sm btn-chip-biru" onclick="bpSimpan()">Simpan</button>
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

// Kolom kedua tabel -- bisa digeser & disembunyikan (MasterList.kolom(), disimpan per user di
// DBSIMPANHEADER dengan href 'produksipemakaianbahan', mode 1 = OutStanding SPK, 2 = BP).
// [field, label, tampil, tipe, total, desimal]
const OS_KOLOM = [
  ['NOBUKTI',  'No. Bukti',   1, 'varchar', 0, 0],
  ['TANGGAL',  'Tanggal',     1, 'date',    0, 0],
  ['KODEBRG',  'Kode Barang', 1, 'varchar', 0, 0],
  ['NAMABRG',  'Nama Barang', 1, 'varchar', 0, 0],
  ['Satuan',   'Sat',         1, 'varchar', 0, 0],
  ['QntSPK',   'Qty SPK',     1, 'float',   0, 2],
  ['QntPakai', 'Qty Pakai',   1, 'float',   0, 2],
  ['QntSisa',  'Qty Sisa',    1, 'float',   0, 2],
]

const BP_KOLOM = [
  ['NoBukti',      'No. Bukti',         1, 'varchar', 0, 0],
  ['Tanggal',      'Tanggal',           1, 'date',    0, 0],
  ['IsOtorisasi1', 'Otorisasi',         1, 'varchar', 0, 0],
  ['OtoUser1',     'Otorisasi User',    1, 'varchar', 0, 0],
  ['TglOto1',      'Otorisasi Date',    1, 'date',    0, 0],
]

let dataOS = []
let dataBP = []

// Sel khusus tab BP: kolom Otorisasi sebagai centang hijau / silang merah (sama dengan SO).
const BP_KHUSUS = {
  IsOtorisasi1: function (item) {
    return '<td class="text-center">' + (Number(item.IsOtorisasi1)
      ? '<i class="bi bi-check2 text-success" title="Sudah Otorisasi" style="-webkit-text-stroke-width: 2px;"></i>'
      : '<i class="bi bi-x-lg text-danger" title="Belum Otorisasi" style="-webkit-text-stroke-width: 1px;"></i>') + '</td>'
  },
}

// Opsi DataTables per tab: panjang halaman & kata cari diambil dari toolbar tab itu sendiri
// (bukan toolbar bersama MasterList), supaya tetap terjaga waktu tabel digambar ulang.
function pbOpsi (idLen) {
  return MasterList.opsi({ pageLength: Number($('#' + idLen).val()) || 10 })
}

function renderTabelOS () {
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy()
  }

  let cols = MasterList.kolomTampil('#tabel')
  document.getElementById('tabel_os_header').innerHTML = MasterList.headHtml(cols, '#tabel')

  let rowTable = ''
  dataOS.forEach((item) => {
    // Tombol "+" biru di kolom Actions -- buat BP dari baris SPK ini (belum tersedia, lihat buttonAddDariOS()).
    let aksi = `
      <div class="action-buttons-wrap">
        <button title="Tambah" class="btn-action-sm btn-action-primary" type="button" onclick="buttonAddDariOS('${item.NOBUKTI}', ${item.URUT})"><i class="bi bi-plus"></i></button>
      </div>`
    rowTable += MasterList.baris(item, cols, aksi, null)
  })

  document.getElementById('tabel_data_os').innerHTML = rowTable
  let dt = $('#tabel').DataTable(pbOpsi('osLen'))
  if ($('#osSearch').val()) { dt.search($('#osSearch').val()).draw() }
}

function renderTabelBP () {
  if ($.fn.DataTable.isDataTable('#tabel7')) {
    $('#tabel7').DataTable().destroy()
  }

  let cols = MasterList.kolomTampil('#tabel7')
  document.getElementById('tabel_bp_header').innerHTML = MasterList.headHtml(cols, '#tabel7')

  let rowTable = ''
  dataBP.forEach((item) => {
    // Pola sama dengan halaman SPK: BP yang sudah diotorisasi hanya bisa dilihat (Detail) atau dibatalkan
    // otorisasinya (kunci merah); yang belum boleh Edit (Koreksi) dan Otorisasi (kunci cyan).
    let sudahOto = Number(item.IsOtorisasi1) === 1
    let aksi = `<button title="Detail" class="btn-action-sm btn-action-warning" type="button" onclick="buttonDetail('${item.NoBukti}')"><i class="bi bi-info"></i></button>`
    if (sudahOto) {
      aksi += `<button title="Batal Otorisasi" class="btn-action-sm btn-action-danger" type="button" onclick="buttonBatalOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>`
    } else {
      aksi += `
        <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.NoBukti}')"><i class="bi bi-pen"></i></button>
        <button title="Otorisasi" class="btn-action-sm btn-action-info" type="button" onclick="buttonOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>
        <button title="Hapus" class="btn-action-sm btn-action-danger" type="button" onclick="buttonHapusBP('${item.NoBukti}')"><i class="bi bi-trash"></i></button>`
    }
    aksi = `<div class="action-buttons-wrap">${aksi}</div>`
    rowTable += MasterList.baris(item, cols, aksi, BP_KHUSUS)
  })

  document.getElementById('tabel_data_bp').innerHTML = rowTable
  let dt = $('#tabel7').DataTable(pbOpsi('bpLen'))
  if ($('#bpSearch').val()) { dt.search($('#bpSearch').val()).draw() }
}

// Toolbar tiap tab mengendalikan tabelnya sendiri (cari ditunda 300 ms, sama seperti toolbar Master).
let pbTundaCari = null
function pbPasangToolbar (idSearch, idLen, sel) {
  $(document).on('input', '#' + idSearch, function () {
    let kata = this.value
    clearTimeout(pbTundaCari)
    pbTundaCari = setTimeout(function () {
      if ($.fn.DataTable.isDataTable(sel)) { $(sel).DataTable().search(kata).draw() }
    }, 300)
  })
  $(document).on('change', '#' + idLen, function () {
    if ($.fn.DataTable.isDataTable(sel)) { $(sel).DataTable().page.len(Number(this.value)).draw() }
  })
}
pbPasangToolbar('osSearch', 'osLen', '#tabel')
pbPasangToolbar('bpSearch', 'bpLen', '#tabel7')

// Tab berganti: bar kolom tersembunyi (ReportTable) ikut milik tabel di tab yang tampil.
$(document).on('shown.bs.tab', '#pbTab [data-toggle="tab"]', function () {
  let sel = $(this).attr('href') === '#tab-bp' ? '#tabel7' : '#tabel'
  MasterList.pakaiKolom(sel)
  if (window.mktAturTinggiTabel) { window.mktAturTinggiTabel() }
})

// ===================== Form Tambah BP (#page2) =====================

// Tombol Tambah di tab BP -- form kosong (No. SPK belum terisi).
function buttonAddBP () {
  bpBukaForm('')
}

// Tombol "+" biru di tab OutStanding SPK -- form terisi No. SPK baris yang diklik; WIP/BJ ikut
// akhiran No. SPK-nya (SPKS/.../WIP atau .../BJ).
function buttonAddDariOS (nobukti, urut) {
  bpBukaForm(nobukti)
}

// Buka form -- kalau No. SPK terisi (dari tombol "+"), WIP/BJ dikunci mengikuti SPK-nya, Nomor Bukti
// dihitung, Gudang dimuat, dan "Ambil SPK" dijalankan OTOMATIS: baris SPK langsung masuk ke grid
// (grid tidak bisa diisi manual, hanya lewat Ambil SPK).
function bpBukaForm (nospk) {
  bpSetMode('tambah')
  let tipeEl = document.getElementById('input_bp_tipe')
  tipeEl.value = /\/BJ$/.test(nospk) ? 'BJ' : 'WIP'
  tipeEl.disabled = nospk !== ''
  document.getElementById('input_bp_nobukti').value = ''
  document.getElementById('input_bp_nourut').value = ''
  document.getElementById('input_bp_tanggal').valueAsDate = new Date()
  document.getElementById('input_bp_nospk').value = nospk
  document.getElementById('input_bp_shift').value = '0'
  bpKosongkanGrid()

  bpGenerateNoBukti()
  bpIsiGudang()
  if (nospk !== '') { bpAmbilSPK() }

  $('#page1').hide()
  $('#page2').show()
}

// Mode form #page2: 'tambah' (Tambah BP), 'edit' (Koreksi BP -- header dikunci, hanya QNT yang bisa
// diubah) atau 'lihat' (Detail BP -- semua dikunci, tombol Batal/Simpan disembunyikan, tinggal Close).
// Header BP (Tanggal, Gudang, No. SPK, ...) tidak punya SP/query ubah, jadi dikunci di edit dan lihat.
let bpMode = 'tambah'

function bpSetMode (mode) {
  bpMode = mode
  let kunci = mode !== 'tambah'
  document.getElementById('bpFormJudul').textContent = mode === 'edit' ? 'Koreksi BP' : (mode === 'lihat' ? 'Detail BP' : 'Tambah BP')
  document.getElementById('input_bp_tanggal').disabled = kunci
  document.getElementById('input_bp_gudang').disabled = kunci
  document.getElementById('input_bp_shift').disabled = kunci
  if (kunci) { document.getElementById('input_bp_tipe').disabled = true }
  $('#bpFormAksi').toggle(mode !== 'lihat')
}

// Tombol Detail/Edit di baris tab BP -- form yang sama dengan Tambah, diisi dari BP yang sudah ada.
function buttonDetail (nobukti) {
  bpMuatBP(nobukti, 'lihat')
}

function buttonEdit (nobukti) {
  bpMuatBP(nobukti, 'edit')
}

// Muat satu BP ke form (ProduksiPemakaianBahanController::loadDetailBP(), query pemuatan dari senior).
function bpMuatBP (nobukti, mode) {
  $.ajax({
    url: "{!! url('produksipemakaianbahanloaddetailbp') !!}",
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
      document.getElementById('input_bp_shift').value = String(h.Shif ?? 0)
      bpIsiGudang(h.KodeGdg)

      // Baris tanpa Urut = BP tanpa detail (LEFT JOIN) -- grid dikosongkan.
      let rows = res.filter((r) => r.Urut !== null && r.Urut !== undefined)
      if (!rows.length) {
        bpKosongkanGrid()
      } else {
        let editable = mode === 'edit'
        let rowTable = ''
        rows.forEach((item) => {
          rowTable += `
            <tr>
              <td>${item.KodeBrg ?? ''}</td>
              <td>${item.NamaBrg ?? ''}</td>
              <td class="text-right"><input type="text" inputmode="decimal" class="form-control bp-qnt-input" data-urut="${item.Urut}" value="${formatAngkaBP(Number(item.Qnt ?? 0))}"${editable ? '' : ' disabled'}></td>
              <td>${item.Sat ?? ''}</td>
            </tr>`
        })
        document.getElementById('tabel_data_bpform').innerHTML = rowTable
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

// Hapus BP dari baris tab BP (hanya yang belum diotorisasi) -- ProduksiPemakaianBahanController::hapusBP().
// Setelah berhasil kedua daftar dimuat ulang (Qnt Pakai di OutStanding SPK ikut berubah).
function buttonHapusBP (nobukti) {
  alertify.confirm('Hapus BP', 'Apakah yakin ingin menghapus BP "' + nobukti + '" beserta seluruh barangnya?',
    function () {
      $.ajax({
        url: "{!! url('produksipemakaianbahanhapusbp') !!}",
        type: "post",
        dataType: "json",
        data: { _token: $('#_token').val(), nobukti },
        success: function () {
          alertify.success('BP berhasil dihapus')
          loadBP()
          if (typeof loadOutstanding === 'function') { loadOutstanding() }
        },
        error: function (xhr) {
          alertify.error(xhr.responseJSON?.message || 'Gagal menghapus BP')
        }
      })
    },
    function () {}
  )
}

// Otorisasi langsung dari baris tabel (tanpa membuka form, tanpa dialog konfirmasi) -- pola sama dengan
// buttonOtorisasi() di halaman SPK. ProduksiPemakaianBahanController::spOtorisasi().
function buttonOtorisasi (nobukti) {
  if (!Number($('#akses_isotorisasi1').val())) {
    alertify.warning('No access')
    return
  }

  $.ajax({
    url: "{!! url('produksipemakaianbahanspotorisasi') !!}",
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

// Batal otorisasi -- keterangan wajib diisi, dikirim sebagai 'pket' dan dicatat LoggingData di
// ProduksiPemakaianBahanController::spBatalOtorisasi().
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
        url: "{!! url('produksipemakaianbahanspbatalotorisasi') !!}",
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

// Simpan Koreksi BP -- hanya Qnt per baris (ProduksiPemakaianBahanController::updateBP()).
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
    url: "{!! url('produksipemakaianbahanupdatebp') !!}",
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
  if (window.mktAturTinggiTabel) { window.mktAturTinggiTabel() }
}

function bpKosongkanGrid () {
  document.getElementById('tabel_data_bpform').innerHTML = '<tr><td colspan="4" class="text-center text-muted">Belum ada barang</td></tr>'
  document.getElementById('bpFormTotal').textContent = '0.00'
}

// Nomor Bukti BP/<urut>/<MMYY>/MM/<WIP|BJ> dihitung server (ProduksiPemakaianBahanController::
// nextNoBukti()). dataType 'json' dipaksa: kalau server membalas selain JSON valid (mis. halaman login
// karena sesi habis) tampil pesan galat, bukan "undefined" di kolom.
function bpGenerateNoBukti () {
  $.ajax({
    url: "{!! url('produksipemakaianbahannextnobukti') !!}",
    type: "get",
    dataType: "json",
    data: {
      tipe: $('#input_bp_tipe').val(),
      bulan: $('#periode_bulan').val(),
      tahun: $('#periode_tahun').val(),
    },
    success: function (res) {
      if (!res || !res.nobukti) {
        alertify.error('Nomor Bukti tidak valid dari server')
        return
      }
      document.getElementById('input_bp_nobukti').value = res.nobukti
      document.getElementById('input_bp_nourut').value = res.nourut
    },
    error: function () {
      alertify.error('Gagal menghitung Nomor Bukti (coba muat ulang halaman)')
    }
  })
}

// Dropdown Gudang (DBGUDANG aktif), dimuat ulang tiap form dibuka. terpilih (opsional) -- KodeGdg yang
// langsung dipilih (Detail/Edit BP); kalau gudang itu sudah tidak aktif tetap ditambahkan supaya tampil.
function bpIsiGudang (terpilih) {
  $.ajax({
    url: "{!! url('produksipemakaianbahanlistgudang') !!}",
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

// Ambil SPK -- dijalankan otomatis saat form dibuka dari "+", tombolnya tetap ada untuk memuat ulang.
// Mengisi grid dengan baris Bahan dari SPK itu (ProduksiPemakaianBahanController::ambilSPK(), isi dan
// rumus sama dengan SP_INSPEMAKAI). Hanya pratinjau -- BP baru tersimpan saat Simpan.
function bpAmbilSPK () {
  let nospk = $('#input_bp_nospk').val()
  if (!nospk) {
    alertify.warning('Pilih SPK dari tab OutStanding SPK (tombol +)')
    return
  }

  $.ajax({
    url: "{!! url('produksipemakaianbahanambilspk') !!}",
    type: "get",
    dataType: "json",
    data: { nospk },
    success: function (res) {
      if (!Array.isArray(res) || !res.length) {
        bpKosongkanGrid()
        alertify.warning('SPK ini tidak punya barang')
        return
      }
      let rowTable = ''
      res.forEach((item) => {
        // QNT bisa diedit langsung; data-urut = Urut baris SPK (sama dengan Urut baris BP di
        // SP_INSPEMAKAI), dipakai bpSimpan() untuk mengirim nilai yang diubah.
        rowTable += `
          <tr>
            <td>${item.KODEBRG ?? ''}</td>
            <td>${item.NAMABRG ?? ''}</td>
            <td class="text-right"><input type="text" inputmode="decimal" class="form-control bp-qnt-input" data-urut="${item.URUT}" value="${formatAngkaBP(Number(item.Qnt ?? 0))}"></td>
            <td>${item.Sat ?? ''}</td>
          </tr>`
      })
      document.getElementById('tabel_data_bpform').innerHTML = rowTable
      bpHitungTotal()
    },
    error: function () {
      alertify.error('Gagal mengambil data SPK')
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

// Edit QNT langsung di grid: saat mengetik hanya angka dan titik (maks. 2 desimal) yang diterima dan
// total ikut dihitung; saat keluar kolom nilainya dirapikan ke format 2 desimal (ribuan koma). Kolom
// kosong/bukan angka/<= 0 diberi tanda merah dan ditolak saat Simpan.
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

// Simpan BP -- ProduksiPemakaianBahanController::simpanBP() (Exec SP_INSPEMAKAI: header + semua baris
// SPK). Setelah berhasil form ditutup dan kedua daftar dimuat ulang (Qnt Pakai di OutStanding berubah).
function bpSimpan () {
  if (bpMode === 'edit') {
    bpSimpanKoreksi()
    return
  }
  if (!$('#input_bp_nospk').val()) {
    alertify.warning('Pilih SPK dari tab OutStanding SPK (tombol +)')
    return
  }
  if (!$('#input_bp_gudang').val()) {
    alertify.warning('Pilih Gudang terlebih dahulu')
    return
  }
  let inputs = $('#tabel_data_bpform .bp-qnt-input')
  if (inputs.length === 0) {
    alertify.warning('Belum ada barang dari SPK')
    return
  }

  // Semua QNT harus angka > 0; yang diubah user ikut dikirim (per Urut baris SPK).
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
  if (salah) {
    alertify.warning('Qnt harus berupa angka lebih dari 0')
    salah.focus()
    return
  }

  $.ajax({
    url: "{!! url('produksipemakaianbahansimpanbp') !!}",
    type: "post",
    dataType: "json",
    data: {
      _token: $('#_token').val(),
      nobukti: $('#input_bp_nobukti').val(),
      shift: $('#input_bp_shift').val(),
      nospk: $('#input_bp_nospk').val(),
      tanggal: $('#input_bp_tanggal').val(),
      kodegdg: $('#input_bp_gudang').val(),
      bulan: $('#periode_bulan').val(),
      tahun: $('#periode_tahun').val(),
      qnt: qnt,
    },
    success: function (res) {
      alertify.success('BP ' + res.nobukti + ' berhasil disimpan')
      bpTutupForm()
      loadBP()
      loadOutstanding()
    },
    error: function (xhr) {
      alertify.error(xhr.responseJSON?.message || 'Gagal menyimpan BP')
    }
  })
}

function loadOutstanding () {
  $.ajax({
    url: "{!! url('produksipemakaianbahanloadoutstanding') !!}",
    type: "get",
    success: function (res) {
      dataOS = res
      renderTabelOS()
    },
    error: function () {
      alertify.error('Gagal memuat OutStanding SPK')
    }
  })
}

function loadBP () {
  $.ajax({
    url: "{!! url('produksipemakaianbahanloadbp') !!}",
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

window.onload = function () {
  MasterList.kolom({ href: 'produksipemakaianbahan', mode: 1, table: '#tabel', bar: '#rtBarOS', kolom: OS_KOLOM, onChange: renderTabelOS })
  MasterList.kolom({ href: 'produksipemakaianbahan', mode: 2, table: '#tabel7', bar: '#rtBarBP', kolom: BP_KOLOM, onChange: renderTabelBP })
  loadOutstanding()
  loadBP()
  // Tab pertama (OutStanding SPK) aktif: ReportTable dikembalikan ke tabelnya setelah kedua kolom() terdaftar.
  MasterList.pakaiKolom('#tabel')
}

</script>
@endsection
