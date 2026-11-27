@extends('newmasterTest')
@section('page-title', 'Amortisasi')

@section('css')
{{-- Gaya toolbar (po-toolbar, po-filter-wrap, po-search-inp) - sama seperti Bon Sementara. --}}
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">

<style>
#content { padding-top: 12px; }

/* Rule .card global layout newmasterTest (menu beranda) dinetralkan - sama seperti bonsementara. */
#page1 .card {
  display: block !important;
  align-items: stretch !important;
  padding: 0 !important;
  text-align: left !important;
  cursor: default !important;
}
#page1 .card:hover {
  transform: none !important;
  box-shadow: none !important;
  border-color: var(--border, #e5e7eb) !important;
}

.custom-tabs {
  display: inline-flex;
  align-items: center;
  gap: 2px;
  background-color: #f1f3f5;
  border-radius: 20px;
  padding: 3px;
}
.custom-tabs .nav-link {
  display: inline-block !important;
  padding: 5px 16px !important;
  font-size: 0.75rem !important;
  border: none;
  border-radius: 17px;
  color: #495057;
  background: transparent;
  font-weight: 600;
  transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}
.custom-tabs .nav-link:hover { background: transparent; color: #007bff; }
.custom-tabs .nav-link.active {
  background: #007bff;
  color: #fff;
  box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
}
.tab-card {
  display: block !important;
  padding: 0 !important;
  border: none !important;
  margin-bottom: 6px !important;
}
.tab-card .card-body { padding: 5px 10px !important; }

.po-table-wrap table.dataTable thead th,
.po-table-wrap table.dataTable tbody td { white-space: nowrap; }

#tabel, #tabel2 { min-width: 100%; }

/* ---------- Kolom Aksi - tombol kecil warna pastel ---------- */
#tabel td:first-child, #tabel2 td:first-child { vertical-align: middle; }
.po-aksi-wrap { display: flex; gap: 4px; justify-content: center; align-items: center; }
#tabel td:first-child .btn,
#tabel2 td:first-child .btn {
  width: 30px;
  height: 30px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 7px;
  font-size: 13px;
  border: 1px solid transparent;
  box-shadow: none;
  transition: all .12s ease;
}
#tabel td:first-child .btn:hover,
#tabel2 td:first-child .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
#tabel td:first-child .btn-success, #tabel2 td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel td:first-child .btn-danger,  #tabel2 td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

/* ---------- Badge status jadwal ---------- */
.am-badge {
  display: inline-block;
  padding: 2px 10px;
  border-radius: 10px;
  font-size: 11.5px;
  font-weight: 600;
}
.am-badge-0 { background: #fff4e0; color: #b45309; }
.am-badge-1 { background: #e7f7ed; color: #16a34a; }
.am-badge-2 { background: #f1f3f5; color: #6b7280; }

.btn-dpp-utama {
  height: 36px;
  border-radius: 8px;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 0 16px;
  background-color: #e8edff;
  border: 1px solid #cfdcff;
  color: #2563eb;
  box-shadow: none;
}
.btn-dpp-utama:hover,
.btn-dpp-utama:focus { background-color: #dce6ff; border-color: #b9c9ff; color: #1d4ed8; }

.modal-footer .btn-primary {
  background-color: #e8edff;
  border-color: #cfdcff;
  color: #2563eb;
  border-radius: 8px !important;
  text-transform: none !important;
  box-shadow: none;
}
.modal-footer .btn-primary:hover { background-color: #dce6ff; border-color: #b9c9ff; color: #1d4ed8; }
.modal-footer .btn-secondary {
  background-color: #f1f3f5;
  border-color: #dee2e6;
  color: #495057;
  border-radius: 8px !important;
  text-transform: none !important;
  box-shadow: none;
}
.modal-footer .btn-danger {
  background-color: #fdeaea;
  border-color: #f7cfcf;
  color: #dc2626;
  border-radius: 8px !important;
  text-transform: none !important;
  box-shadow: none;
}

/* ---------- Form Tambah / Koreksi ---------- */
#form .modal-content { border: none; border-radius: var(--rt-radius, 12px); }
#form .modal-header { padding: 16px 24px; border-bottom: 1px solid var(--rt-border-soft, #eef0f4); }
#form .modal-body   { padding: 20px 24px; }
#form .modal-footer { padding: 14px 24px 18px; border-top: 1px solid var(--rt-border-soft, #eef0f4); }

#form .am-form {
  display: grid;
  grid-template-columns: 96px minmax(0, 1fr) 56px minmax(0, 1fr);
  gap: 14px 12px;
  align-items: center;
}
/* Input yang membentang dari kolom input pertama sampai tepi kanan. */
#form .am-form .am-full { grid-column: 2 / -1; }
#form .am-form label {
  margin-bottom: 0;
  font-size: 11.5px;
  letter-spacing: .04em;
  color: var(--rt-ink-soft, #6B7180);
  white-space: nowrap;
}
#form .form-control {
  height: 38px;
  border-radius: 8px;
  border: 1px solid var(--rt-border, #E7E8F0);
  font-size: 13.5px;
  box-shadow: none;
}
#form .form-control:disabled {
  background-color: #f1f3f5;
  border-color: #e3e6ea;
  color: #8A8F9C;
}

#amPreviewWrap { margin-top: 18px; }
#amPreviewWrap .am-preview-judul {
  font-size: 11.5px;
  font-weight: 700;
  letter-spacing: .04em;
  color: var(--rt-ink-soft, #6B7180);
  text-transform: uppercase;
  margin-bottom: 6px;
}
#amPreviewScroll {
  max-height: 220px;
  overflow: auto;
  border: 1px solid #eef0f4;
  border-radius: 8px;
}
#amPreview { width: 100%; font-size: 12.5px; margin: 0; }
#amPreview th, #amPreview td { padding: 4px 10px; border-bottom: 1px solid #f1f3f5; }
#amPreview thead th { position: sticky; top: 0; background: #f8f9fb; }

@media (max-width: 575.98px) {
  #form .am-form { grid-template-columns: 1fr; gap: 4px 0; }
  #form .am-form label { margin-top: 8px; }
  #form .am-form .am-full { grid-column: 1 / -1; }
}
</style>
@endsection


@section('content')

<div id="page1" class="container-fluid mainpage">
<div class="container-fluid">

  <input type="hidden" id="akses_istambah" value="{{ (int) $akses->ISTAMBAH }}" />
  <input type="hidden" id="akses_iskoreksi" value="{{ (int) $akses->ISKOREKSI }}" />
  <input type="hidden" id="akses_ishapus" value="{{ (int) $akses->ISHAPUS }}" />
  <input type="hidden" id="akses_isotorisasi1" value="{{ (int) $akses->IsOtorisasi1 }}" />
  <input type="hidden" id="akses_isbatal" value="{{ (int) $akses->IsBatal }}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-daftar-tab" data-toggle="tab" href="#tabDaftar" role="tab" aria-controls="tabDaftar" aria-selected="true">
          Daftar Amortisasi
        </a>
        <a class="nav-item nav-link" id="nav-jadwal-tab" data-toggle="tab" href="#tabJadwal" role="tab" aria-controls="tabJadwal" aria-selected="false">
          Jadwal Amortisasi
        </a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="tab-content">

        {{-- ===== Tab 1: header DBAMORTISASI, difilter DBAMORTISASI.Tanggal ===== --}}
        <div class="tab-pane fade show active" id="tabDaftar" role="tabpanel" aria-labelledby="nav-daftar-tab">
          <div class="po-toolbar">
            <div class="po-filter-wrap">
              <label>Periode</label>
              <input type="date" class="po-filter-inp" id="amTglAwal" value="{{ $tglAwal }}">
              <span class="po-filter-sep">s/d</span>
              <input type="date" class="po-filter-inp" id="amTglAkhir" value="{{ $tglAkhir }}">
            </div>
            <input type="search" id="amSearch" class="po-search-inp" placeholder="Cari data">
            <div class="po-toolbar-act">
              @if ((int) $akses->ISTAMBAH === 1)
                <button type="button" class="btn btn-dpp-utama" onclick="buttonAdd()">Tambah</button>
              @endif
            </div>
          </div>

          <table id="tabel" class="data-table">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;">Actions</th>
                <th style="padding: 4px 12px;">Kode</th>
                <th style="padding: 4px 12px;">Keterangan</th>
                <th style="padding: 4px 12px;">Tanggal</th>
                <th style="padding: 4px 12px;">Nilai</th>
                <th style="padding: 4px 12px;">Tipe</th>
                <th style="padding: 4px 12px;">Metode</th>
                <th style="padding: 4px 12px;">Persen</th>
                <th style="padding: 4px 12px;">Lama</th>
                <th style="padding: 4px 12px;">Progres</th>
              </tr>
            </thead>
            <tbody id="tabel_data" class="text-left"></tbody>
          </table>
        </div>

        {{-- ===== Tab 2: jadwal DBAMORTISASIDET (semua status), difilter DBAMORTISASIDET.Tanggal ===== --}}
        <div class="tab-pane fade" id="tabJadwal" role="tabpanel" aria-labelledby="nav-jadwal-tab">
          <div class="po-toolbar">
            <div class="po-filter-wrap">
              <label>Periode</label>
              <input type="date" class="po-filter-inp" id="jdTglAwal" value="{{ $tglAwal }}">
              <span class="po-filter-sep">s/d</span>
              <input type="date" class="po-filter-inp" id="jdTglAkhir" value="{{ $tglAkhir }}">
            </div>
            <input type="search" id="jdSearch" class="po-search-inp" placeholder="Cari data">
          </div>

          <table id="tabel2" class="data-table">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;">Actions</th>
                <th style="padding: 4px 12px;">Kode</th>
                <th style="padding: 4px 12px;">Keterangan</th>
                <th style="padding: 4px 12px;">Tanggal</th>
                <th style="padding: 4px 12px;">Nilai</th>
                <th style="padding: 4px 12px;">Sisa</th>
                <th style="padding: 4px 12px;">Status</th>
              </tr>
            </thead>
            <tbody id="tabel2_data" class="text-left"></tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

</div>
</div>


<!-- ===== Modal Tambah / Koreksi ===== -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formJudul">Tambah Amortisasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="am-form">
          <label for="input_kode" class="am-baris-kode">Kode</label>
          <input type="text" class="form-control am-full am-baris-kode" id="input_kode" disabled>

          <label for="input_keterangan">Keterangan</label>
          <input type="text" class="form-control am-full" id="input_keterangan" maxlength="50">

          <label for="input_metode">Metode</label>
          <select class="form-control" id="input_metode">
            <option value="0">Garis Lurus</option>
            <option value="1">Saldo Menurun</option>
          </select>

          <label for="input_persen">Persen</label>
          <input type="text" inputmode="decimal" class="form-control text-right" id="input_persen" placeholder="% / tahun" autocomplete="off" disabled>

          <label for="input_nilai">Nilai</label>
          <input type="text" inputmode="decimal" class="form-control text-right" id="input_nilai">

          <label for="input_tipe">Tipe</label>
          <select class="form-control" id="input_tipe">
            <option value="0">Bulan</option>
            <option value="1">Tahun</option>
          </select>

          <label for="input_tanggal">Tanggal</label>
          <input type="date" class="form-control" id="input_tanggal">

          <label for="input_lama">Lama</label>
          <input type="number" min="1" max="600" step="1" class="form-control text-right" id="input_lama">
        </div>

        {{-- Preview jadwal yang akan dibuat - aturannya sama dengan buatJadwal() di controller. --}}
        <div id="amPreviewWrap">
          <div class="am-preview-judul">Jadwal yang akan dibuat <span id="amPreviewInfo"></span></div>
          <div id="amPreviewScroll">
            <table id="amPreview">
              <thead><tr><th>#</th><th>Tanggal</th><th class="text-right">Nilai</th><th class="text-right">Sisa</th></tr></thead>
              <tbody id="amPreviewData"></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="buttonSimpan" onclick="submitForm()">Simpan</button>
      </div>
    </div>
  </div>
</div>


<!-- ===== Modal Batal jadwal ===== -->
<div class="modal fade" id="modalBatal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Batal Jadwal Amortisasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="batalTeks" style="margin-bottom: 8px;"></p>
        <p class="text-muted" style="font-size: 12.5px; margin-bottom: 0;">
          Jadwal yang sudah tereksekusi tidak ikut dibatalkan.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-danger" onclick="submitBatal('satu')">Batal baris ini saja</button>
        <button type="button" class="btn btn-danger" onclick="submitBatal('sisa')">Batal baris ini s/d terakhir</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
<script type="text/javascript">

const AM_TIPE   = { 0: 'Bulan', 1: 'Tahun' }
const AM_METODE = { 0: 'Garis Lurus', 1: 'Saldo Menurun' }
const AM_STATUS = { 0: 'Belum', 1: 'Tereksekusi', 2: 'Batal' }

const hak = {
  tambah  : $('#akses_istambah').val() === '1',
  koreksi : $('#akses_iskoreksi').val() === '1',
  hapus   : $('#akses_ishapus').val() === '1',
  eksekusi: $('#akses_isotorisasi1').val() === '1',
  batal   : $('#akses_isbatal').val() === '1',
}

let modeForm = 'add'    // 'add' | 'edit'
let batalTarget = null  // { kode, tanggal }

$(document).ready(function () {
  amPasangSeparatorInput('input_nilai')

  amPasangPersenInput('input_persen')

  $('#input_metode').on('change', amAturPersen)
  $('#input_keterangan, #input_metode, #input_persen, #input_nilai, #input_tipe, #input_tanggal, #input_lama').on('input change', renderPreview)

  amIkatPeriode('amTglAwal', 'amTglAkhir', loadAll)
  amIkatPeriode('jdTglAwal', 'jdTglAkhir', loadDetail)
  amIkatSearch('amSearch', '#tabel')
  amIkatSearch('jdSearch', '#tabel2')

  // Tabel di tab tersembunyi terukur 0 saat init - dihitung ulang saat tabnya dibuka.
  $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
    ['#tabel', '#tabel2'].forEach((id) => {
      if ($.fn.DataTable.isDataTable(id)) { $(id).DataTable().columns.adjust() }
    })
  })

  muatSemua()
})

/* ==========================================================================
   Helper
   ========================================================================== */
function amEsc (teks) {
  return String(teks === null || teks === undefined ? '' : teks)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#39;')
}

function amFormatAngka (nilai, des = 2) {
  let angka = Number(nilai)
  if (isNaN(angka)) { angka = 0 }
  let teks = angka.toFixed(des)
  let minus = teks.charAt(0) === '-'
  if (minus) { teks = teks.substring(1) }
  let bagian = teks.split('.')
  let bulat = bagian[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  return (minus ? '-' : '') + bulat + (bagian[1] ? '.' + bagian[1] : '')
}

// 'YYYY-MM-DD' -> 'DD-MM-YYYY' untuk tampilan.
function amFormatTanggal (ymd) {
  if (!ymd) { return '' }
  let p = String(ymd).substring(0, 10).split('-')
  return p.length === 3 ? `${p[2]}-${p[1]}-${p[0]}` : ymd
}

function amHariIni () {
  let d = new Date()
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
}

function amAngkaMentah (val) {
  return Number(String(val || '0').split(',').join('')) || 0
}

// Separator ribuan saat mengetik - sama seperti bsPasangSeparatorInput di bonsementara.
function amPasangSeparatorInput (id) {
  let el = document.getElementById(id)
  if (!el) { return }
  el.addEventListener('input', function () {
    let raw = el.value.replace(/,/g, '')
    if (raw === '') { return }
    let parts = raw.split('.')
    let bulat = parts[0].replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',')
    let desimal = parts.length > 1 ? '.' + parts[1].replace(/\D/g, '').slice(0, 2) : ''
    el.value = bulat + desimal
  })
}

// Persen: angka dengan titik desimal, maksimal 2 desimal (tanpa separator ribuan).
function amPasangPersenInput (id) {
  let el = document.getElementById(id)
  if (!el) { return }
  el.addEventListener('input', function () {
    let parts = el.value.replace(/[^0-9.]/g, '').split('.')
    el.value = parts.length > 1 ? parts[0] + '.' + parts.slice(1).join('').slice(0, 2) : parts[0]
  })
}

// Persen hanya aktif untuk Saldo Menurun; untuk Garis Lurus dikosongkan dan dinonaktifkan.
function amAturPersen () {
  let menurun = $('#input_metode').val() === '1'
  $('#input_persen').prop('disabled', !menurun)
  if (!menurun) { $('#input_persen').val('') }
}

// Persen valid: 0 < persen <= 100.
function amPersenValid (persen) {
  return persen > 0 && persen <= 100
}

function amIkatPeriode (idAwal, idAkhir, muat) {
  let awal = document.getElementById(idAwal)
  let akhir = document.getElementById(idAkhir)
  let onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    muat()
  }
  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

function amIkatSearch (idInput, idTabel) {
  let input = document.getElementById(idInput)
  input.addEventListener('input', function () {
    if ($.fn.DataTable.isDataTable(idTabel)) { $(idTabel).DataTable().search(input.value).draw() }
  })
}

function amOpsiDataTable () {
  return {
    lengthChange: false,
    pageLength: 25,
    order: [],
    columnDefs: [{ targets: [0], orderable: false }],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  }
}

// Pesan error dari server (HTTP 4xx berisi { message }). Kalau datanya sudah berubah
// di tempat lain (pesan "dimuat ulang"), kedua tabel dimuat ulang.
function amError (err) {
  console.log(err)
  let pesan = err && err.responseJSON && err.responseJSON.message
  if (err && err.status === 419) { pesan = 'Sesi habis, silahkan refresh browser' }
  alertify.warning(pesan || 'Terjadi kesalahan silahkan refresh browser')
  if (pesan && pesan.indexOf('dimuat ulang') !== -1) { muatSemua() }
}

/* ==========================================================================
   Jadwal (preview) - HARUS sama dengan AmortisasiController::buatJadwal().
   - Tanggal ke-i = tanggal awal + i bulan/tahun dari tanggal awal; kalau harinya
     tidak ada di bulan tujuan, dipakai hari terakhir bulan itu (31 Jan -> 28 Feb).
   - Nilai dalam sen. Garis Lurus (metode 0): tiap baris floor(total / lama), sisa
     masuk baris terakhir.
   - Saldo Menurun (metode 1): tiap baris kecuali terakhir = sisa x persen / 1200
     (Bulan) atau / 100 (Tahun), dibulatkan half-up dan dibatasi paling besar sisa;
     baris terakhir = seluruh sisa. Hitungan bilangan bulat (persen dalam basis point,
     BigInt) supaya identik dengan nilaiSaldoMenurun() di controller.
   ========================================================================== */
function amTambahTanggal (ymd, tipe, i) {
  let [y, m, d] = ymd.split('-').map(Number)
  if (Number(tipe) === 1) {
    y += i
  } else {
    let m0 = (m - 1) + i
    y += Math.floor(m0 / 12)
    m = (m0 % 12) + 1
  }
  let akhirBulan = new Date(y, m, 0).getDate()
  d = Math.min(d, akhirBulan)
  return `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`
}

function amBuatJadwal (nilai, tipe, tanggal, lama, metode, persen) {
  let totalSen = Math.round(nilai * 100)
  let perBaris = Math.floor(totalSen / lama)
  let sisaSen = totalSen
  let hasil = []
  for (let i = 0; i < lama; i++) {
    let sen
    if (Number(metode) === 1) {
      if (i === lama - 1) {
        sen = sisaSen
      } else {
        // BigInt supaya tepat (angka JS hanya eksak sampai 2^53); pembulatan half-up.
        const p = BigInt(Math.round(persen * 100))
        const den = BigInt(Number(tipe) === 1 ? 10000 : 120000)
        const num = BigInt(sisaSen) * p
        let q = num / den
        if ((num % den) * 2n >= den) { q += 1n }
        sen = Math.min(Number(q), sisaSen)
      }
    } else {
      sen = (i === lama - 1) ? totalSen - perBaris * (lama - 1) : perBaris
    }
    sisaSen -= sen
    hasil.push({ tanggal: amTambahTanggal(tanggal, tipe, i), nilai: sen / 100, sisa: sisaSen / 100 })
  }
  return hasil
}

function renderPreview () {
  let nilai = amAngkaMentah($('#input_nilai').val())
  let tipe = $('#input_tipe').val()
  let tanggal = $('#input_tanggal').val()
  let lama = parseInt($('#input_lama').val(), 10)
  let metode = $('#input_metode').val()
  let persen = Math.round(amAngkaMentah($('#input_persen').val()) * 100) / 100

  if (!tanggal || !(nilai > 0) || !(lama >= 1) || lama > 600) {
    $('#amPreviewData').html('<tr><td colspan="4" class="text-muted text-center">Isi Nilai, Tanggal dan Lama</td></tr>')
    $('#amPreviewInfo').text('')
    return
  }
  if (metode === '1' && !amPersenValid(persen)) {
    $('#amPreviewData').html('<tr><td colspan="4" class="text-muted text-center">Isi Persen (lebih dari 0, maksimal 100)</td></tr>')
    $('#amPreviewInfo').text('')
    return
  }

  let html = ''
  amBuatJadwal(nilai, tipe, tanggal, lama, metode, persen).forEach((b, i) => {
    html += `<tr><td>${i + 1}</td><td>${amFormatTanggal(b.tanggal)}</td><td class="text-right">${amFormatAngka(b.nilai)}</td><td class="text-right">${amFormatAngka(b.sisa)}</td></tr>`
  })
  $('#amPreviewData').html(html)
  let namaMetode = AM_METODE[metode] + (metode === '1' ? ` ${amFormatAngka(persen)}%` : '')
  $('#amPreviewInfo').text(`(${lama} baris, per ${AM_TIPE[tipe].toLowerCase()}, ${namaMetode})`)
}

/* ==========================================================================
   Muat data
   ========================================================================== */
function muatSemua () {
  loadAll()
  loadDetail()
}

function loadAll () {
  $.ajax({
    url: "{!! url('amortisasiloadall') !!}",
    type: 'get',
    data: { tglawal: $('#amTglAwal').val(), tglakhir: $('#amTglAkhir').val() },
    success: renderDaftar,
    error: amError
  })
}

function loadDetail () {
  $.ajax({
    url: "{!! url('amortisasiloaddetail') !!}",
    type: 'get',
    data: { tglawal: $('#jdTglAwal').val(), tglakhir: $('#jdTglAkhir').val() },
    success: renderJadwal,
    error: amError
  })
}

// Tab Daftar Amortisasi. Koreksi & Hapus hanya muncul selama semua jadwalnya masih Belum.
function renderDaftar (rows) {
  if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy() }

  let html = ''
  ;(rows || []).forEach((r) => {
    let kode = amEsc(r.Kode)
    let masihBelum = (Number(r.JmlEksekusi) + Number(r.JmlBatal)) === 0
    let aksi = ''
    if (masihBelum && hak.koreksi) {
      aksi += `<button class="btn btn-success btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi('${kode}')"><i class="bi bi-pen"></i></button>`
    }
    if (masihBelum && hak.hapus) {
      aksi += `<button class="btn btn-danger btn-sm" type="button" title="Hapus" onclick="buttonHapus('${kode}')"><i class="bi bi-trash"></i></button>`
    }

    let progres = `${Number(r.JmlEksekusi)} / ${Number(r.JmlJadwal)} tereksekusi`
    if (Number(r.JmlBatal) > 0) { progres += `, ${Number(r.JmlBatal)} batal` }

    html += `<tr>
      <td class="text-center"><div class="po-aksi-wrap">${aksi}</div></td>
      <td>${kode}</td>
      <td>${amEsc(r.Keterangan)}</td>
      <td data-order="${amEsc(r.Tanggal)}">${amFormatTanggal(r.Tanggal)}</td>
      <td class="text-right" data-order="${Number(r.Nilai)}">${amFormatAngka(r.Nilai)}</td>
      <td>${AM_TIPE[Number(r.Tipe)] || ''}</td>
      <td>${Number(r.Metode) === 1 ? AM_METODE[1] : AM_METODE[0]}</td>
      <td class="text-right">${Number(r.Metode) === 1 ? amFormatAngka(r.Persen) + '%' : ''}</td>
      <td class="text-right">${Number(r.Lama)}</td>
      <td>${progres}</td>
    </tr>`
  })
  document.getElementById('tabel_data').innerHTML = html

  $('#tabel').DataTable(amOpsiDataTable())
  let cari = $('#amSearch').val()
  if (cari) { $('#tabel').DataTable().search(cari).draw() }
}

// Tab Jadwal Amortisasi. Tombol centang / silang hanya pada baris Belum.
function renderJadwal (rows) {
  if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy() }

  let html = ''
  ;(rows || []).forEach((r) => {
    let kode = amEsc(r.Kode)
    let tgl = amEsc(r.Tanggal)
    let status = Number(r.Status)
    let aksi = ''
    if (status === 0 && hak.eksekusi) {
      aksi += `<button class="btn btn-success btn-sm" type="button" title="Eksekusi" onclick="buttonEksekusi('${kode}','${tgl}','${amFormatAngka(r.Nilai)}')"><i class="bi bi-check-lg"></i></button>`
    }
    if (status === 0 && hak.batal) {
      aksi += `<button class="btn btn-danger btn-sm" type="button" title="Batal" onclick="buttonBatal('${kode}','${tgl}')"><i class="bi bi-x-lg"></i></button>`
    }

    html += `<tr>
      <td class="text-center"><div class="po-aksi-wrap">${aksi}</div></td>
      <td>${kode}</td>
      <td>${amEsc(r.Keterangan)}</td>
      <td data-order="${tgl}">${amFormatTanggal(r.Tanggal)}</td>
      <td class="text-right" data-order="${Number(r.Nilai)}">${amFormatAngka(r.Nilai)}</td>
      <td class="text-right" data-order="${Number(r.Sisa)}">${amFormatAngka(r.Sisa)}</td>
      <td><span class="am-badge am-badge-${status}">${AM_STATUS[status] || status}</span></td>
    </tr>`
  })
  document.getElementById('tabel2_data').innerHTML = html

  $('#tabel2').DataTable(amOpsiDataTable())
  let cari = $('#jdSearch').val()
  if (cari) { $('#tabel2').DataTable().search(cari).draw() }
}

/* ==========================================================================
   Tambah / Koreksi / Hapus header
   ========================================================================== */
function bukaForm (mode, data) {
  modeForm = mode
  $('#formJudul').text(mode === 'edit' ? 'Koreksi Amortisasi' : 'Tambah Amortisasi')
  $('.am-baris-kode').toggle(mode === 'edit')

  $('#input_kode').val(data ? data.Kode : '')
  $('#input_keterangan').val(data ? data.Keterangan : '')
  $('#input_nilai').val(data ? amFormatAngka(data.Nilai) : '')
  $('#input_tipe').val(data ? String(Number(data.Tipe)) : '0')
  $('#input_tanggal').val(data ? data.Tanggal : amHariIni())
  $('#input_lama').val(data ? Number(data.Lama) : '')
  $('#input_metode').val(data ? String(Number(data.Metode) === 1 ? 1 : 0) : '0')
  $('#input_persen').val(data && Number(data.Metode) === 1 && data.Persen !== null ? String(Number(Number(data.Persen).toFixed(2))) : '')
  amAturPersen()

  renderPreview()
  $('#form').modal('show')
}

function buttonAdd () {
  bukaForm('add', null)
}

function buttonKoreksi (kode) {
  $.ajax({
    url: "{!! url('amortisasispdetail') !!}",
    type: 'post',
    data: { _token: $('#_token').val(), kode },
    success: function (res) {
      let sudahDiproses = (res.detail || []).some((d) => Number(d.Status) !== 0)
      if (sudahDiproses) {
        alertify.warning('Sudah ada jadwal yang tereksekusi / batal, tidak bisa dikoreksi')
        muatSemua()
        return
      }
      bukaForm('edit', res.header)
    },
    error: amError
  })
}

function submitForm () {
  let data = {
    _token: $('#_token').val(),
    kode: $('#input_kode').val(),
    keterangan: $('#input_keterangan').val().trim(),
    nilai: amAngkaMentah($('#input_nilai').val()),
    tipe: $('#input_tipe').val(),
    tanggal: $('#input_tanggal').val(),
    lama: $('#input_lama').val(),
    metode: $('#input_metode').val(),
    persen: $('#input_metode').val() === '1' ? amAngkaMentah($('#input_persen').val()) : '',
  }
  let lama = parseInt(data.lama, 10)

  if (!data.keterangan) { alertify.warning('Keterangan harus diisi'); return }
  if (!(data.nilai > 0)) { alertify.warning('Nilai harus lebih dari 0'); return }
  if (!data.tanggal) { alertify.warning('Tanggal harus diisi'); return }
  if (!(lama >= 1) || lama > 600 || String(lama) !== String(data.lama).trim()) {
    alertify.warning('Lama harus bilangan bulat 1 - 600'); return
  }
  if (data.metode === '1' && data.nilai > 999999999999.99) {
    alertify.warning('Nilai maksimal 999,999,999,999.99 untuk Saldo Menurun'); return
  }
  if (data.metode === '1' && !amPersenValid(data.persen)) {
    alertify.warning('Persen harus lebih dari 0 dan maksimal 100'); return
  }
  if (data.metode === '0' && Math.round(data.nilai * 100) < lama) {
    alertify.warning('Nilai terlalu kecil untuk dibagi ' + lama + ' baris'); return
  }

  $('#buttonSimpan').prop('disabled', true)
  $.ajax({
    url: modeForm === 'edit' ? "{!! url('amortisasispedit') !!}" : "{!! url('amortisasispadd') !!}",
    type: 'post',
    data,
    success: function (res) {
      if (res && res.ok == 1) {
        alertify.success(modeForm === 'edit'
          ? `Amortisasi ${res.kode} telah dikoreksi`
          : `Amortisasi ${res.kode} telah ditambah`)
        $('#form').modal('hide')
        muatSemua()
      }
    },
    error: amError,
    complete: function () { $('#buttonSimpan').prop('disabled', false) }
  })
}

function buttonHapus (kode) {
  alertify.confirm('Hapus Amortisasi', `Hapus amortisasi ${kode} beserta seluruh jadwalnya?`,
    function () {
      $.ajax({
        url: "{!! url('amortisasispdelete') !!}",
        type: 'post',
        data: { _token: $('#_token').val(), kode },
        success: function (res) {
          if (res && res.ok == 1) {
            alertify.success(`Amortisasi ${kode} telah dihapus`)
            muatSemua()
          }
        },
        error: amError
      })
    },
    function () {}
  ).set('labels', { ok: 'Hapus', cancel: 'Batal' })
}

/* ==========================================================================
   Eksekusi / Batal jadwal
   ========================================================================== */
function buttonEksekusi (kode, tanggal, nilaiTeks) {
  var dlgEksekusi = alertify.confirm('Eksekusi Jadwal',
    `Eksekusi jadwal ${kode} tanggal ${amFormatTanggal(tanggal)} senilai ${nilaiTeks}?`,
    function () {
      $.ajax({
        url: "{!! url('amortisasispeksekusi') !!}",
        type: 'post',
        data: { _token: $('#_token').val(), kode, tanggal },
        success: function (res) {
          if (res && res.ok == 1) {
            alertify.success('Jadwal telah tereksekusi')
            muatSemua()
          }
        },
        error: amError
      })
    },
    function () {}
  ).set('labels', { ok: 'Eksekusi', cancel: 'Batal' })
  dlgEksekusi.elements.root.classList.add('ajs-app-buttons')
}

function buttonBatal (kode, tanggal) {
  batalTarget = { kode, tanggal }
  $('#batalTeks').text(`Batalkan jadwal ${kode} tanggal ${amFormatTanggal(tanggal)}?`)
  $('#modalBatal').modal('show')
}

function submitBatal (mode) {
  if (!batalTarget) { return }
  $.ajax({
    url: "{!! url('amortisasispbatal') !!}",
    type: 'post',
    data: { _token: $('#_token').val(), kode: batalTarget.kode, tanggal: batalTarget.tanggal, mode },
    success: function (res) {
      if (res && res.ok == 1) {
        alertify.success(`${res.jumlah} jadwal telah dibatalkan`)
        $('#modalBatal').modal('hide')
        batalTarget = null
        muatSemua()
      }
    },
    error: function (err) {
      $('#modalBatal').modal('hide')
      amError(err)
    }
  })
}

</script>
@endsection
