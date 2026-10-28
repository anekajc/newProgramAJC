@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Bills of Material')

{{-- Browser BOM 4 panel, mengikuti tampilan lama (System Inventory Control > BOM):
     kiri = daftar Barang Jadi/WIP (po-toolbar + data-table server-side, gaya SO/Master),
     kanan = 3 panel bertingkat (Daftar BOM -> Daftar Proses + Detail BOM) yang terisi lewat
     AJAX begitu baris kiri/BOM diklik. Baru tahap browse (Read) -- buttonAdd()/buttonEdit()/
     submitPrint() masih placeholder, menunggu endpoint Tambah/Koreksi/Cetak. --}}
@section('css')
<link rel="stylesheet"
    href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
<link rel="stylesheet"
    href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>

  /* Halaman ini harus muat 1 layar (tidak discroll) -- #content adalah wrapper layout
     newmasterTest yang sebenarnya discroll (overflow-y:auto bawaan), tapi dia sendiri
     sudah dibatasi tinggi lewat flex:1 di dalam .main (flex-column, overflow:hidden).
     Jadi rantai flex sebenarnya sudah ada, cuma #content-blade/.po-list-page di
     bawahnya masih div polos (height:auto) -- itu yang bikin isinya meluber dan
     memaksa #content ikut discroll. Override di sini cuma berlaku selagi stylesheet
     halaman ini dimuat (page-scoped), bukan mengedit file css global. */
  #content {
    overflow: hidden !important;
    display: flex;
    flex-direction: column;
  }

  #content-blade {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
  }

  .po-list-page {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
  }

  .pb-panel-title {
    font-weight: 700;
    font-size: 14px;
    color: var(--rt-ink, #1f2430);
    padding: 12px 16px;
    border-bottom: 1px solid var(--rt-border-soft, #EDEEF3);
  }

  .pb-panel-title .pb-sub {
    font-weight: 600;
    color: #2563eb;
  }

  .pb-sub-table {
    width: 100%;
    margin: 0;
  }

  .pb-sub-table th,
  .pb-sub-table td {
    padding: 8px 16px;
    font-size: 13px;
    border-bottom: 1px solid var(--rt-border-soft, #EDEEF3);
  }

  .pb-sub-table th {
    text-align: left;
    color: #7B8194;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: .04em;
  }

  .pb-sub-table tbody tr:hover {
    background: #f8f9fc;
  }

  .pb-sub-table tbody tr.pb-row-selected {
    background: #fff3b0;
  }

  #tabel tbody tr {
    cursor: pointer;
  }

  #tabel tbody tr.pb-row-selected {
    background: #fff3b0 !important;
  }

  .pb-right-col {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  /* Dua kolom (tabel kiri + 3 panel kanan) diregang mengisi 1 layar penuh lewat rantai
     flex murni (#content -> #content-blade -> .po-list-page -> baris ini), bukan lagi JS
     pixel-measurement -- lihat override #content/#content-blade/.po-list-page di atas. */
  .pb-main-row {
    flex: 1;
    min-height: 0;
    align-items: stretch;
  }

  .pb-left-col,
  .pb-right-col {
    display: flex;
    flex-direction: column;
    min-height: 0;
  }

  .pb-left-col .card {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
  }

  /* po-table-header.css punya :where(.po-list-page) .card { display:block !important },
     jadi .card di atas TIDAK benar-benar jadi flex container (cuma ukurannya sendiri yang
     ikut flex:1 dari .pb-left-col) -- height:100% di sini supaya .card-body tetap dapat
     tinggi pasti dari .card walau .card-nya sendiri bukan flex, lalu .card-body baru yang
     jadi flex container sungguhan untuk #tabel_wrapper di bawahnya. */
  .pb-left-col .card-body {
    height: 100%;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    min-height: 0;
  }

  /* overflow:hidden menahan isi #tabel_wrapper (po-table-wrap + baris info/pagination) supaya
     tidak meluber keluar kartu kalau totalnya sedikit lebih tinggi dari ruang yang ada --
     po-table-wrap-nya sendiri yang discroll (overflow:auto bawaan .po-table-wrap), bukan
     keseluruhan wrapper. */
  #tabel_wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
    overflow: hidden;
  }

  /* max-height:none !important supaya menang atas inline max-height yang ditulis
     MasterList.aturTinggi() (rumusnya untuk layout 1 kolom, tidak cocok di sini) --
     tinggi po-table-wrap yang sebenarnya diatur rantai flex (flex:1 1 auto + min-height:0)
     di atas, bukan nilai inline itu. */
  #tabel_wrapper .po-table-wrap {
    flex: 1 1 auto;
    min-height: 0;
    max-height: none !important;
  }

  /* Baris info ("Showing x to y...") DataTables bawaan (dom cuma 'i' sekarang - token 'p'
     dilepas dari dom string di loadAll(), paginationnya diganti #pbPager custom di bawah).
     flex-shrink:0 supaya baris ini tidak pernah terdesak sampai 0 tinggi oleh po-table-wrap
     di atasnya. */
  .pb-left-col #tabel_wrapper > .row {
    margin: 0;
    flex: 0 0 auto;
    text-align: center;
    padding: 4px 16px;
  }

  /* Pager custom (#pbPager, diisi pbRenderPager()) - tombol kecil & rapat, beda dari
     pagination Bootstrap bawaan DataTables yang lebar/renggang dan gampang meluber untuk
     18 ribuan baris (1840 halaman). Scroll horizontal kalau tetap tidak muat. */
  .pb-pager {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    flex-wrap: nowrap;
    overflow-x: auto;
    padding: 8px 16px 12px;
  }

  .pb-pg-btn {
    flex: 0 0 auto;
    min-width: 34px;
    height: 34px;
    padding: 0 8px;
    border: 1px solid var(--rt-border, #E7E8F0);
    background: #fff;
    border-radius: 6px;
    font-size: 13.5px;
    line-height: 1;
    color: #1f2430;
    cursor: pointer;
  }

  .pb-pg-btn:hover:not(:disabled) {
    background: #f3f4f8;
  }

  .pb-pg-btn:disabled {
    opacity: .4;
    cursor: default;
  }

  .pb-pg-btn.pb-pg-active {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
  }

  .pb-pg-ellipsis {
    flex: 0 0 auto;
    padding: 0 2px;
    color: #9297A8;
    font-size: 13px;
  }

  .pb-right-col .card {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
  }

  /* po-table-header.css punya :where(.po-list-page) .card { display:block !important },
     jadi .card di atas TIDAK benar-benar jadi flex container sendiri (lihat catatan sama
     di .pb-left-col .card-body) -- .pb-card-inner di sini yang jadi flex container
     sungguhan, dapat tinggi pasti dari .card lewat height:100%. */
  .pb-right-col .pb-card-inner {
    height: 100%;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    min-height: 0;
  }

  .pb-right-col .pb-card-inner > div[style*="overflow-x"] {
    flex: 1;
    overflow-y: auto;
    min-height: 0;
  }

</style>
@endsection

@section('content')

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="row pb-main-row">

    <div class="col-md-5 pb-left-col">
      <div class="card">
        <div class="card-body" style="padding:0;">

          @include('master.partials.toolbarMaster')

          <table id="tabel" class="data-table po-aksi-hover" style='height:95% !important;'>
            <thead>
              <tr>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              </tr>
            </thead>
            <tbody id="tabel_data" class="text-left"></tbody>
          </table>

          <div id="pbPager" class="pb-pager"></div>

        </div>
      </div>
    </div>

    <div class="col-md-7 pb-right-col">

      <div class="card">
        <div class="pb-card-inner">
          <div class="pb-panel-title">Daftar BOM untuk Kode Barang: <span class="pb-sub" id="pbJudulBarang">-</span></div>
          <div style="overflow-x:auto;">
            <table class="pb-sub-table">
              <thead>
                <tr>
                  <th>Kode BOM</th>
                  <th>Default</th>
                </tr>
              </thead>
              <tbody id="tabel_data_bom">
                <tr><td colspan="2" class="text-center text-muted">Pilih barang di sebelah kiri</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="pb-card-inner">
          <div class="pb-panel-title">Daftar Proses</div>
          <div style="overflow-x:auto;">
            <table class="pb-sub-table">
              <thead>
                <tr>
                  <th>Urut</th>
                  <th>Kode Proses</th>
                </tr>
              </thead>
              <tbody id="tabel_data_proses">
                <tr><td colspan="2" class="text-center text-muted">Pilih BOM di atas</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="pb-card-inner">
          <div class="pb-panel-title">Detail BOM: <span class="pb-sub" id="pbJudulBom">-</span></div>
          <div style="overflow-x:auto;">
            <table class="pb-sub-table">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Sat</th>
                  <th>Numerator</th>
                  <th>Denominator</th>
                  <th>Loss Ratio</th>
                </tr>
              </thead>
              <tbody id="tabel_data_detail">
                <tr><td colspan="6" class="text-center text-muted">Pilih BOM di atas</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

  </div>

</div>

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>

<script type="text/javascript">

let pbKodeBarangAktif = null
let pbKodeBomAktif = null
let pbKodePrsAktif = null

// Tabel kiri (#tabel): server-side karena DBBARANG grup BJ/WIP bisa belasan ribu baris.
// Hanya 3 kolom (Actions, Kode Barang, Nama Barang) -- di bawah ambang MasterList.kolom()
// (>5 kolom), jadi header-nya statis, tidak pakai drag/sembunyikan kolom.
function loadAll () {
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  $('#tabel').DataTable(MasterList.opsi({
    processing: true,
    serverSide: true,
    // Token 'p' (pagination Bootstrap bawaan) dilepas dari dom MasterList - diganti #pbPager
    // custom (pbRenderPager()) yang tombolnya kecil & rapat, lebih cocok untuk 1800+ halaman.
    dom: "<'po-table-wrap't><'row'<'col-sm-12'i>>",
    ajax: {
      url: "{!! url('produksibomloadallbarang') !!}",
      type: "GET",
    },
    columns: [
      {
        data: null, orderable: false, searchable: false, className: 'text-center',
        render: function (data, type, row) {
          return `
            <div class="action-buttons-wrap">
              <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${row.KodeBrg}')"><i class="bi bi-pen"></i></button>
              <button title="Cetak" class="btn-action-sm btn-action-primary" type="button" onclick="submitPrint('${row.KodeBrg}')"><i class="bi bi-printer"></i></button>
            </div>
          `;
        }
      },
      { data: 'KodeBrg' },
      { data: 'NamaBrg' },
    ],
    // MasterList.aturTinggi() tetap menulis max-height INLINE yang rumusnya tidak cocok
    // untuk layout dua kolom ini, tapi sudah dikalahkan oleh `max-height: none !important`
    // di CSS -- tinggi po-table-wrap yang sebenarnya ikut tinggi .card-body (lihat
    // pbAturTinggi()).
    drawCallback: function () { pbRenderPager(); pbAturTinggi() }
  }));

  MasterList.selesai('#tabel')
  pbAturTinggi()
}

// #content/#content-blade/.po-list-page/.pb-main-row sudah dipaksa flex:1+overflow:hidden
// lewat CSS di atas supaya halaman ini muat 1 layar (tidak discroll) -- tapi
// po-table-header.css punya :where(.po-list-page) .card { display:block !important },
// yang memutus rantai flex TEPAT DI .card: boks luarnya sendiri masih benar tingginya
// (ikut flex:1 dari .pb-left-col/.pb-right-col, properti flex-sizing tidak kena blokir
// !important itu), tapi ANAKNYA (.card-body / .pb-card-inner) tidak lagi flex item karena
// parent-nya (.card) bukan flex container -- height:100% CSS sudah dicoba tapi tidak bisa
// diandalkan di semua kondisi render. Jadi di sini tinggi .card-body/.pb-card-inner
// diambil LANGSUNG dari hasil ukur .card-nya sendiri dan dipaksa lewat inline style (inline
// selalu menang atas aturan stylesheet biasa, termasuk yang kena !important display tadi,
// karena yang kita set bukan display tapi height).
function pbAturTinggi () {
  let row = document.querySelector('#contentContainer .pb-main-row')
  if (!row) { return }

  // Dipaksa ulang langsung dari hasil ukur, bukan cuma mengandalkan rantai flex
  // #content->...->.pb-main-row benar-benar tersambung sampai atas -- kalau ternyata ADA
  // titik putus lain yang belum ketahuan, baris ini tetap jadi jaring pengaman supaya row
  // tidak pernah mengecil sependek isinya saja.
  row.style.height = ''
  let kontenEl = document.querySelector('.content')
  let bawahKonten = kontenEl ? kontenEl.getBoundingClientRect().bottom : window.innerHeight
  let atas = row.getBoundingClientRect().top
  let tinggiRow = Math.max(300, Math.floor(bawahKonten - atas))
  row.style.height = tinggiRow + 'px'

  document.querySelectorAll('.pb-left-col .card-body').forEach(function (el) {
    el.style.height = ''
  })
  document.querySelectorAll('.pb-right-col .pb-card-inner').forEach(function (el) {
    el.style.height = ''
  })

  document.querySelectorAll('.pb-left-col .card').forEach(function (card) {
    let body = card.querySelector('.card-body')
    let tinggi = Math.floor(card.getBoundingClientRect().height)
    if (body && tinggi > 0) { body.style.height = tinggi + 'px' }
  })

  document.querySelectorAll('.pb-right-col .card').forEach(function (card) {
    let inner = card.querySelector('.pb-card-inner')
    let tinggi = Math.floor(card.getBoundingClientRect().height)
    if (inner && tinggi > 0) { inner.style.height = tinggi + 'px' }
  })
}

$(window).on('resize', pbAturTinggi)

// Pager custom #pbPager, gantinya pagination Bootstrap bawaan DataTables - lihat alasannya
// di komentar dom:'...' pada loadAll(). Dipanggil tiap draw (drawCallback), bukan cuma
// sekali, supaya tombol aktif/disabled-nya tetap benar setiap pindah halaman.
function pbRenderPager () {
  let pager = document.getElementById('pbPager')
  if (!pager || !$.fn.DataTable.isDataTable('#tabel')) { return }

  let dt = $('#tabel').DataTable()
  let info = dt.page.info()
  let total = info.pages
  let current = info.page

  if (total <= 1) {
    pager.innerHTML = ''
    return
  }

  let html = ''
  html += `<button type="button" class="pb-pg-btn" ${current === 0 ? 'disabled' : ''} onclick="pbGoPage(${current - 1})" title="Sebelumnya">&laquo;</button>`

  pbJendelaHalaman(current, total).forEach(function (p) {
    if (p === '...') {
      html += `<span class="pb-pg-ellipsis">&hellip;</span>`
    } else {
      html += `<button type="button" class="pb-pg-btn${p === current ? ' pb-pg-active' : ''}" onclick="pbGoPage(${p})">${p + 1}</button>`
    }
  })

  html += `<button type="button" class="pb-pg-btn" ${current >= total - 1 ? 'disabled' : ''} onclick="pbGoPage(${current + 1})" title="Berikutnya">&raquo;</button>`

  pager.innerHTML = html
}

// Halaman pertama, terakhir, dan 2 di kiri-kanan halaman aktif - sisanya dirangkum "...".
function pbJendelaHalaman (current, total) {
  let set = new Set([0, total - 1, current - 2, current - 1, current, current + 1, current + 2])
  let urut = [...set].filter(function (p) { return p >= 0 && p < total }).sort(function (a, b) { return a - b })

  let hasil = []
  let sebelumnya = null
  urut.forEach(function (p) {
    if (sebelumnya !== null && p - sebelumnya > 1) { hasil.push('...') }
    hasil.push(p)
    sebelumnya = p
  })
  return hasil
}

function pbGoPage (n) {
  $('#tabel').DataTable().page(n).draw('page')
}

// Delegasi lewat document (bukan langsung ke <tbody>) supaya tetap jalan setelah
// loadAll() destroy()+init() ulang DataTables-nya. Klik tombol Actions tidak ikut
// memilih baris (buttonEdit/submitPrint menghentikan sendiri lewat target .action-buttons-wrap).
$(document).on('click', '#tabel tbody tr', function (e) {
  if ($(e.target).closest('.action-buttons-wrap').length) { return; }

  let dt = $('#tabel').DataTable();
  let data = dt.row(this).data();
  if (!data) { return; }

  $('#tabel tbody tr').removeClass('pb-row-selected');
  $(this).addClass('pb-row-selected');

  pbPilihBarang(data.KodeBrg, data.NamaBrg);
});

function pbPilihBarang (kodebrg, namabrg) {
  pbKodeBarangAktif = kodebrg
  pbKodeBomAktif = null

  document.getElementById('pbJudulBarang').textContent = kodebrg + ' - ' + namabrg
  document.getElementById('pbJudulBom').textContent = '-'
  document.getElementById('tabel_data_proses').innerHTML = '<tr><td colspan="2" class="text-center text-muted">Pilih BOM di atas</td></tr>'
  document.getElementById('tabel_data_detail').innerHTML = '<tr><td colspan="6" class="text-center text-muted">Pilih BOM di atas</td></tr>'

  $.ajax({
    url: "{!! url('produksibomlistbom') !!}",
    type: "get",
    data: { kodebrg },
    success: function (res) {
      let rowTable = ''
      res.forEach((item) => {
        rowTable += `
          <tr class="pb-pilih-bom" data-kodebom="${item.KodeBOM}" onclick="pbPilihBom('${item.KodeBOM}', this)">
            <td>${item.KodeBOM}</td>
            <td class="text-center">${Number(item.IsDefault) ? '<i class="bi bi-check2 text-success"></i>' : ''}</td>
          </tr>
        `
      });
      if (!res.length) {
        rowTable = '<tr><td colspan="2" class="text-center text-muted">Belum ada BOM untuk barang ini</td></tr>'
      }
      document.getElementById('tabel_data_bom').innerHTML = rowTable
    },
    error: function () {
      alertify.error('Gagal memuat daftar BOM')
    }
  });
}

function pbPilihBom (kodebom, tr) {
  pbKodeBomAktif = kodebom
  pbKodePrsAktif = null

  $('#tabel_data_bom tr').removeClass('pb-row-selected')
  if (tr) { $(tr).addClass('pb-row-selected') }

  document.getElementById('pbJudulBom').textContent = kodebom
  document.getElementById('tabel_data_detail').innerHTML = '<tr><td colspan="6" class="text-center text-muted">Pilih proses di atas</td></tr>'

  $.ajax({
    url: "{!! url('produksibomlistproses') !!}",
    type: "get",
    data: { kodebom },
    success: function (res) {
      let rowTable = ''
      res.forEach((item) => {
        rowTable += `
          <tr class="pb-pilih-proses" onclick="pbPilihProses('${kodebom}', '${item.KodePrs}', this)">
            <td>${item.Urut}</td>
            <td>${item.KodePrs}</td>
          </tr>
        `
      });
      if (!res.length) {
        rowTable = '<tr><td colspan="2" class="text-center text-muted">Belum ada proses</td></tr>'
      }
      document.getElementById('tabel_data_proses').innerHTML = rowTable
    },
    error: function () {
      alertify.error('Gagal memuat daftar proses')
    }
  });
}

// Detail BOM (DBBOMDET) difilter KodeBOM + KodePrs (satu baris Daftar Proses), bukan
// seluruh BOM sekaligus -- lihat query dari senior.
function pbPilihProses (kodebom, kodeprs, tr) {
  pbKodePrsAktif = kodeprs

  $('#tabel_data_proses tr').removeClass('pb-row-selected')
  if (tr) { $(tr).addClass('pb-row-selected') }

  $.ajax({
    url: "{!! url('produksibomlistdetail') !!}",
    type: "get",
    data: { kodebom, kodeprs },
    success: function (res) {
      let rowTable = ''
      res.forEach((item) => {
        rowTable += `
          <tr>
            <td>${item.KodeBrg ?? ''}</td>
            <td>${item.NAMABRG ?? ''}</td>
            <td>${item.SAT1 ?? ''}</td>
            <td>${item.Numerator ?? ''}</td>
            <td>${item.Denominator ?? ''}</td>
            <td>${item.LossRatio ?? ''}</td>
          </tr>
        `
      });
      if (!res.length) {
        rowTable = '<tr><td colspan="6" class="text-center text-muted">Belum ada detail</td></tr>'
      }
      document.getElementById('tabel_data_detail').innerHTML = rowTable
    },
    error: function () {
      alertify.error('Gagal memuat detail BOM')
    }
  });
}

// Placeholder -- Tambah/Koreksi/Cetak belum ada endpoint-nya, menyusul.
function buttonAdd () {
  alertify.message('Tambah BOM belum tersedia')
}

function buttonEdit (kodebrg) {
  alertify.message('Koreksi BOM belum tersedia')
}

function submitPrint (kodebrg) {
  alertify.message('Cetak BOM belum tersedia')
}

window.onload = function () {
  loadAll();
  // Diulang sekali lagi setelah layout benar-benar settle (font/baris toolbar dsb) --
  // panggilan di dalam loadAll()/drawCallback kadang berjalan sebelum browser selesai
  // menghitung tinggi .card hasil flex:1, jadi hasil pertamanya bisa 0/salah.
  setTimeout(pbAturTinggi, 50)
};

</script>
@endsection
