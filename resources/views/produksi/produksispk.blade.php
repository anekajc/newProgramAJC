@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Surat Perintah Kerja')

{{-- Browser SPK (Surat Perintah Kerja) -- 1 tabel server-side, gaya SO/notareturpenjualan
     (po-list-page + toolbarMaster + data-table), BEDA dari produksibom yang 4 panel -- SPK
     tidak perlu pilih "induk" dulu kaya Barang->BOM, jadi satu tabel datar saja cukup.
     Baru tahap browse (Read) + Detail (buttonDetail(), modal item dbSPKDet) --
     Tambah/Koreksi/Cetak/Otorisasi/Batal Otorisasi masih placeholder ('belum tersedia'),
     menunggu spesifikasi dari senior (terutama isi buttonAdd()).
     DBSPK TIDAK punya kolom KodeMesin -- mesin ditentukan PER BARIS dbSPKDet lewat
     KodePrs -> DBMesin.KodePrs, lihat ProduksiSPKController::loadAll()/listDetail(). --}}
@section('css')
<link rel="stylesheet"
    href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
<link rel="stylesheet"
    href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<link rel="stylesheet"
    href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">
@endsection

@section('content')

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

      @include('master.partials.toolbarMaster')

      {{-- Header interaktif (geser & sembunyikan kolom, disimpan per user) -- pola sama dengan
           halaman Master (masterbarang): <thead> diisi MasterList.headHtml() dari loadAll(),
           kolomnya didefinisikan di SPK_KOLOM. --}}
      <table id="tabel" class="data-table">
        <thead id="tabel_header" class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left"></tbody>
      </table>

    </div>
  </div>

</div>

<!-- Modal Detail SPK -- item dbSPKDet satu NoBukti, dipicu buttonDetail() di baris Daftar
     SPK. Header (No Bukti/Tanggal/Kode Mesin/Mesin) diisi dari baris pertama hasil
     produksispklistdetail (mesin DI SINI per baris, bisa beda dari mesin rollup di listing
     utama kalau tiap item SPK pakai proses/mesin berbeda-beda). -->
<div class="modal fade" id="formDetailSPK" tabindex="-1" role="dialog" aria-labelledby="formDetailSPKLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formDetailSPKLabel">Detail SPK</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-3"><strong>No. Bukti</strong><div id="spkDetailNoBukti">-</div></div>
            <div class="col-3"><strong>Tanggal</strong><div id="spkDetailTanggal">-</div></div>
            <div class="col-3"><strong>Kode Mesin</strong><div id="spkDetailKodeMesin">-</div></div>
            <div class="col-3"><strong>Mesin</strong><div id="spkDetailMesin">-</div></div>
          </div>
        </div>
        <hr>
        <div class="container-fluid mt-2">
          <div class="row">
            <table class="pb-sub-table" style="width:100%;">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Kode Proses</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Isi</th>
                  <th>Tipe</th>
                </tr>
              </thead>
              <tbody id="tabel_data_spkdetail">
                <tr><td colspan="7" class="text-center text-muted">Memuat...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-danger" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah SPK -- header (DBSPK) dibuat implisit lewat Simpan Barang Jadi pertama
     (sp_SPKJD Choice='I'), BUKAN tombol "simpan header" terpisah -- lihat
     ProduksiSPKController::spSPKJD()/spSPK(). Kode Mesin dikirim ke sp_SPKJD (DBSPK.KodeMSN)
     saat Barang Jadi disimpan -- header cuma di-insert sekali, jadi mesin yang diganti SETELAH
     Barang Jadi pertama disimpan tidak ikut tersimpan. Kolom "No. Beli"/"No SPK Asal" belum
     punya input di form ini (NoBeli/UrutBeli dikirim kosong/0). -->

<div class="modal fade" id="formAddSPK" tabindex="-1" role="dialog" aria-labelledby="formAddSPKLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formAddSPKLabel">Tambah SPK</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="container-fluid">
          {{-- Satu baris: WIP/BJ dan No. Urut lebar tetap (isinya pendek), 4 kolom lain membagi sisa
               lebar (No. Bukti dan Kode Mesin sedikit lebih lebar karena isinya lebih panjang). --}}
          <div class="row">
            <div class="col-md-auto" style="width:105px;">
              <label>WIP/BJ</label>
              <select class="form-control" id="input_spk_tipe" onchange="spkGenerateNoBukti()">
                <option value="WIP">WIP</option>
                <option value="BJ">BJ</option>
              </select>
            </div>
            <div class="col-md" style="flex:1.4 1 0;">
              <label>No. Bukti</label>
              <input type="text" class="form-control" id="input_spk_nobukti" disabled>
            </div>
            <div class="col-md-auto" style="width:100px;">
              <label>No. Urut</label>
              <input type="text" class="form-control" id="input_spk_nourut" disabled>
            </div>
            <div class="col-md">
              <label>Tanggal</label>
              <input type="date" class="form-control" id="input_spk_tanggal">
            </div>
            <div class="col-md" style="flex:1.4 1 0;">
              <label>Kode Mesin</label>
              {{-- <select> biasa (cuma 3 baris di DBMesin), diisi spkIsiMesin() dari
                   produksispklistmesin. Dikirim sebagai @KodeMsn ke sp_SPKJD (spkBJSimpan()). --}}
              <select class="form-control" id="input_spk_kodemesin" onchange="spkPilihMesin()"></select>
            </div>
            <div class="col-md">
              <label>Mesin</label>
              <input type="text" class="form-control" id="input_spk_mesin" disabled>
            </div>
          </div>
        </div>

        <hr>

        <div class="container-fluid">
          <div class="row align-items-center">
            <div class="col-6"><strong>Barang Jadi</strong></div>
            <div class="col-6 text-right">
              <button type="button" class="btn btn-sm btn-chip-biru" onclick="spkBJTambah()">Tambah</button>
            </div>
          </div>
        </div>
        <div class="container-fluid mt-2">
          <table class="pb-sub-table" style="width:100%;">
            <thead>
              <tr><th>Kode Barang Jadi</th><th>Nama Barang Jadi</th><th>Qnt</th><th>Sat</th><th>BOM</th><th>Actions</th></tr>
            </thead>
            <tbody id="tabel_data_spkbj"><tr><td colspan="6" class="text-center text-muted">Belum ada Barang Jadi</td></tr></tbody>
          </table>
        </div>

        {{-- Kode Barang (Barang Jadi) dipilih DULU lewat picker (spkPilihBarangJadi()), baru Kode
             BOM milik barang itu (spkPilihBOM() -- difilter per barang). Satuan ikut dari barang.
             KodeBOM dikirim ke sp_SPKJD (DBSPKJD.kodeBOM) dan dipakai SP untuk mengisi Bahan Baku
             otomatis dari DBBOMDET -- lihat ProduksiSPKController::spSPKJD(). Saat Edit baris
             (spkBJEdit()) kedua tombol cari dimatikan: sp_SPKJD Choice='U' tidak mengubah
             KodeBOM dan tidak menghitung ulang Bahan Baku. --}}
        <div id="spkBJForm" class="container-fluid mt-3" style="display:none; border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
          <input type="hidden" id="input_spkbj_urut" value="" />
          <div class="row">
            <div class="col-md-3">
              <label>Kode Barang</label>
              <div class="input-group">
                <input type="text" class="form-control" id="input_spkbj_kodebrg" readonly placeholder="Pilih Barang Jadi">
                <div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" id="btn_spkbj_barang" onclick="spkPilihBarangJadi()" title="Cari Kode Barang"><i class="bi bi-search"></i></button></div>
              </div>
            </div>
            <div class="col-md-3">
              <label>Kode BOM</label>
              <div class="input-group">
                <input type="text" class="form-control" id="input_spkbj_kodebom" readonly placeholder="Pilih Kode Barang dulu">
                <div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" id="btn_spkbj_bom" onclick="spkPilihBOM()" title="Cari Kode BOM"><i class="bi bi-search"></i></button></div>
              </div>
            </div>
            <div class="col-md-2">
              <label>Quantity</label>
              <input type="text" inputmode="decimal" class="form-control" id="input_spkbj_qnt" value="0.00" oninput="formatAngkaKetik(this)" onblur="formatAngkaInput(this)">
            </div>
            <div class="col-md-2">
              <label>Satuan</label>
              <input type="text" class="form-control" id="input_spkbj_satuan" disabled>
            </div>
          </div>
          <div class="text-right mt-2">
            <button type="button" class="btn btn-sm btn-danger" onclick="spkBJTutup()">Batal</button>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="spkBJSimpan()">Simpan</button>
          </div>
        </div>

        <hr>

        <div class="container-fluid">
          <div class="row align-items-center">
            <div class="col-6"><strong>Bahan Baku</strong></div>
            <div class="col-6 text-right">
              <button type="button" class="btn btn-sm btn-chip-biru" onclick="spkDetTambah()">Tambah</button>
            </div>
          </div>
        </div>
        <div class="container-fluid mt-2">
          <table class="pb-sub-table" style="width:100%;">
            <thead>
              <tr><th>Kode Barang</th><th>Nama Barang</th><th>Kode Proses</th><th>Qnt</th><th>Sat</th><th>Actions</th></tr>
            </thead>
            <tbody id="tabel_data_spkdet"><tr><td colspan="6" class="text-center text-muted">Belum ada Bahan Baku</td></tr></tbody>
          </table>
        </div>

        <div id="spkDetForm" class="container-fluid mt-3" style="display:none; border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
          <input type="hidden" id="input_spkdet_urut" value="" />
          <div class="row">
            <div class="col-md-4">
              <label>Kode Barang</label>
              <div class="input-group">
                <input type="text" class="form-control" id="input_spkdet_kodebrg" readonly>
                <div class="input-group-append"><button type="button" class="btn btn-chip-biru btn-select" onclick="spkPilihBahanBaku()" title="Cari Kode Barang"><i class="bi bi-search"></i></button></div>
              </div>
            </div>
            <div class="col-md-3">
              <label>Kode Proses</label>
              {{-- Hanya proses yang terikat ke Kode BOM Barang Jadi di SPK ini -- diisi
                   spkIsiProses() dari produksispklistproses. --}}
              <select class="form-control" id="input_spkdet_kodeprs"></select>
            </div>
            <div class="col-md-2">
              <label>Qnt</label>
              <input type="text" inputmode="decimal" class="form-control" id="input_spkdet_qnt" value="0.00" oninput="formatAngkaKetik(this)" onblur="formatAngkaInput(this)">
            </div>
            <div class="col-md-2">
              <label>Satuan</label>
              <input type="text" class="form-control" id="input_spkdet_satuan" disabled>
            </div>
          </div>
          <div class="text-right mt-2">
            <button type="button" class="btn btn-sm btn-danger" onclick="spkDetTutup()">Batal</button>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="spkDetSimpan()">Simpan</button>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-danger" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Picker Kode Barang (Barang Jadi) -- DataTable serverSide:true lewat produksispklistbarangjadi
     (barang Jadi/WIP aktif yang punya BOM berproses), pola sama dengan picker Bahan Baku. -->
<div class="modal fade" id="formPilihBarangJadi" tabindex="-1" role="dialog" aria-labelledby="formPilihBarangJadiLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formPilihBarangJadiLabel">Kode Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-2">
          <div class="row">
            <div class="col-12">
              <input type="text" class="form-control" id="input_cari_barangjadi" placeholder="Ketik Kode/Nama Barang lalu Enter&hellip;">
            </div>
          </div>
        </div>
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">
              <table id="tabel_barangjadi" class="data-table">
                <thead><tr><th style="padding: 4px 12px;" scope="col">Kode</th><th style="padding: 4px 12px;" scope="col">Nama</th><th style="padding: 4px 12px;" scope="col">Sat</th></tr></thead>
                <tbody id="tabel_data_barangjadi" class="text-left"><tr><td>-</td><td>-</td><td>-</td></tr></tbody>
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

<!-- Picker Kode BOM -- BOM berproses milik Kode Barang yang sudah dipilih (produksispklistbombybarang). -->
<div class="modal fade" id="formPilihBOM" tabindex="-1" role="dialog" aria-labelledby="formPilihBOMLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formPilihBOMLabel">Kode BOM</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-2">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabel_pilihbom" class="data-table">
                <thead><tr><th style="padding: 4px 12px;" scope="col">Kode BOM</th><th style="padding: 4px 12px;" scope="col">Default</th></tr></thead>
                <tbody id="tabel_data_pilihbom" class="text-left"><tr><td>-</td><td>-</td></tr></tbody>
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

<!-- Picker Kode Barang buat Bahan Baku -- DataTable serverSide:true pakai endpoint yang sama
     persis dengan picker Kode Barang BOM (produksibomlistkodebarang), 18rb+ baris. -->
<div class="modal fade" id="formPilihBahanBaku" tabindex="-1" role="dialog" aria-labelledby="formPilihBahanBakuLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formPilihBahanBakuLabel">Kode Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-2">
          <div class="row">
            <div class="col-12">
              <input type="text" class="form-control" id="input_cari_bahanbaku" placeholder="Ketik Kode/Nama Barang lalu Enter&hellip; (kosongkan untuk Bahan Baku/Pembantu)">
            </div>
          </div>
        </div>
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-30px;">
              <table id="tabel_bahanbaku" class="data-table">
                <thead><tr><th style="padding: 4px 12px;" scope="col">Kode</th><th style="padding: 4px 12px;" scope="col">Nama</th></tr></thead>
                <tbody id="tabel_data_bahanbaku" class="text-left"><tr><td>-</td><td>-</td></tr></tbody>
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

{{-- .pb-sub-table dipakai di sini (bukan data-table) -- CSS-nya cuma didefinisikan di
     produksibom.blade.php (modul lain), jadi diulang ringkas di sini biar modal Detail
     tetap bergaya PO (thead abu-abu, garis tipis) walau dibuka tanpa produksibom. --}}
<style>
  .pb-sub-table {
    width: 100%;
    margin: 0;
    border-collapse: collapse;
    font-size: 13px;
  }
  .pb-sub-table th,
  .pb-sub-table td {
    padding: 6px 10px;
    border-bottom: 1px solid #f3f4f6;
  }
  .pb-sub-table th {
    text-align: left;
    background: #f9fafb;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: .04em;
    border-bottom: 1px solid #e5e7eb;
  }
  .pb-sub-table tbody tr:last-child td { border-bottom: none; }
</style>

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>

<script type="text/javascript">

// Kolom tabel daftar SPK -- bisa digeser & disembunyikan (MasterList.kolom(), disimpan per user
// di DBSIMPANHEADER dengan href 'produksispk'). [field, label, tampil, tipe, total, desimal]
const SPK_KOLOM = [
  ['NOBUKTI',  'No. Bukti',  1, 'varchar', 0, 0],
  ['TANGGAL',  'Tanggal',    1, 'date',    0, 0],
  ['KodeMsn',  'Kode Mesin', 1, 'varchar', 0, 0],
  ['Ket',      'Mesin',      1, 'varchar', 0, 0],
]

// Kolom DataTables disusun ulang dari kolom yang tampil tiap kali kolom digeser/disembunyikan
// (ReportTable memanggil loadAll lewat onChange). Server (produksispkloadall) tidak memproses
// urutan kolom, jadi ordering dimatikan -- sama seperti masterbarang.
function loadAll () {
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }
  document.getElementById('tabel_data').innerHTML = ''

  let cols = MasterList.kolomTampil()
  document.getElementById('tabel_header').innerHTML = MasterList.headHtml(cols)

  $('#tabel').DataTable(MasterList.opsi({
    processing: true,
    serverSide: true,
    ordering: false,
    autoWidth: false,
    responsive: false,
    ajax: {
      url: "{!! url('produksispkloadall') !!}",
      type: "GET",
    },
    columns: MasterList.kolomServer(cols, function (data, type, row) {
      return `
        <div class="action-buttons-wrap">
          <button title="Detail" class="btn-action-sm btn-action-warning" type="button" onclick="buttonDetail('${row.NOBUKTI}')"><i class="bi bi-info"></i></button>
          <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${row.NOBUKTI}')"><i class="bi bi-pen"></i></button>
          <button title="Hapus" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${row.NOBUKTI}')"><i class="bi bi-trash"></i></button>
        </div>
      `;
    }, {
      KodeMsn: { defaultContent: '-' },
      Ket: { defaultContent: '-' },
    }),
  }));

  MasterList.selesai('#tabel')
}

// Modal Detail -- item dbSPKDet satu NoBukti (ProduksiSPKController::listDetail()).
function buttonDetail (nobukti) {
  document.getElementById('spkDetailNoBukti').textContent = nobukti
  document.getElementById('spkDetailTanggal').textContent = '-'
  document.getElementById('spkDetailKodeMesin').textContent = '-'
  document.getElementById('spkDetailMesin').textContent = '-'
  document.getElementById('tabel_data_spkdetail').innerHTML = '<tr><td colspan="7" class="text-center text-muted">Memuat...</td></tr>'
  $('#formDetailSPK').modal('show')

  $.ajax({
    url: "{!! url('produksispklistdetail') !!}",
    type: "get",
    data: { nobukti },
    success: function (res) {
      if (res.length) {
        let d = res[0].TANGGAL ? new Date(res[0].TANGGAL) : null
        document.getElementById('spkDetailTanggal').textContent = d
          ? String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2, '0') + '/' + d.getFullYear()
          : '-'
        document.getElementById('spkDetailKodeMesin').textContent = res[0].KodeMsn || '-'
        document.getElementById('spkDetailMesin').textContent = res[0].Ket || '-'
      }

      let rowTable = ''
      res.forEach((item) => {
        rowTable += `
          <tr>
            <td>${item.KODEBRG ?? ''}</td>
            <td>${item.NamaBrg ?? ''}</td>
            <td>${item.KodePrs ? item.KodePrs + (item.NamaPrs ? ' - ' + item.NamaPrs : '') : '-'}</td>
            <td>${item.QNT ?? ''}</td>
            <td>${item.SATUAN ?? ''}</td>
            <td>${item.ISI ?? ''}</td>
            <td>${item.KetTipe ?? ''}</td>
          </tr>
        `
      });
      if (!res.length) {
        rowTable = '<tr><td colspan="7" class="text-center text-muted">Belum ada detail</td></tr>'
      }
      document.getElementById('tabel_data_spkdetail').innerHTML = rowTable
    },
    error: function () {
      alertify.error('Gagal memuat Detail SPK')
    }
  });
}

// Separator ribuan buat Quantity/Qnt -- disalin dari produksibom.blade.php (yang disalin dari
// public/js/kas.js), bukan punya file util bersama jadi diulang per halaman.
function formatAngka (angkaString) {
  let tempAngka = angkaString.split('.')
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) { temp1 = ',' + temp1 }
    temp1 = tempAngka[0][tempAngka[0].length - i - 1] + temp1
  }
  temp1 += '.' + tempAngka[1]
  return temp1
}
function unformatAngka (angka) {
  if (!angka) return 0
  return parseFloat(String(angka).replace(/,/g, '')) || 0
}
function formatAngkaInput (el) {
  el.value = formatAngka(unformatAngka(el.value).toFixed(2))
}
function formatAngkaKetik (el) {
  let posDariKanan = el.value.length - el.selectionStart
  let minus = el.value.trim().startsWith('-') ? '-' : ''
  let raw = el.value.replace(/[^0-9.]/g, '')
  let titikIndex = raw.indexOf('.')
  let bulat = titikIndex === -1 ? raw : raw.slice(0, titikIndex)
  let desimal = titikIndex === -1 ? '' : raw.slice(titikIndex + 1).replace(/\./g, '').slice(0, 2)
  bulat = bulat.replace(/^0+(?=\d)/, '')
  if (bulat === '') { bulat = '0' }
  let bulatFormatted = ''
  for (let i = 0; i < bulat.length; i++) {
    if (i != 0 && (bulat.length - i) % 3 == 0) { bulatFormatted += ',' }
    bulatFormatted += bulat[i]
  }
  el.value = minus + bulatFormatted + (titikIndex !== -1 ? '.' + desimal : '')
  let posBaru = Math.max(0, el.value.length - posDariKanan)
  el.setSelectionRange(posBaru, posBaru)
}

// ===================== Tambah SPK =====================

let spkNoBuktiAktif = null

// Mode modal Tambah SPK -- dipakai ulang buat Koreksi (buttonEdit()). Di mode edit WIP/BJ,
// Tanggal dan Kode Mesin dikunci: sp_SPKJD cuma insert header sekali (Choice='I'), tidak ada
// Choice untuk mengubah header, jadi perubahan ketiganya tidak akan tersimpan.
function spkSetModeEdit (isEdit) {
  document.getElementById('formAddSPKLabel').textContent = isEdit ? 'Koreksi SPK' : 'Tambah SPK'
  document.getElementById('input_spk_tipe').disabled = isEdit
  document.getElementById('input_spk_tanggal').disabled = isEdit
  document.getElementById('input_spk_kodemesin').disabled = isEdit
}

function buttonAdd () {
  spkSetModeEdit(false)
  document.getElementById('input_spk_tipe').value = 'WIP'
  document.getElementById('input_spk_tanggal').valueAsDate = new Date()
  document.getElementById('input_spk_mesin').value = ''
  document.getElementById('tabel_data_spkbj').innerHTML = '<tr><td colspan="6" class="text-center text-muted">Belum ada Barang Jadi</td></tr>'
  document.getElementById('tabel_data_spkdet').innerHTML = '<tr><td colspan="6" class="text-center text-muted">Belum ada Bahan Baku</td></tr>'
  $('#spkBJForm').hide()
  $('#spkDetForm').hide()

  spkGenerateNoBukti()
  spkIsiMesin()
  $('#formAddSPK').modal('show')
}

// Dropdown Kode Mesin -- ProduksiSPKController::listMesin() (SELECT KodeMsn, Ket FROM
// DBMesin), cuma 3 baris jadi dimuat ulang tiap modal dibuka, tidak di-cache.
// terpilih (opsional) -- KodeMsn yang langsung dipilih setelah daftar termuat (dipakai buttonEdit()).
function spkIsiMesin (terpilih) {
  $.ajax({
    url: "{!! url('produksispklistmesin') !!}",
    type: "get",
    success: function (res) {
      let opts = '<option value="">-</option>'
      res.forEach((item) => {
        opts += `<option value="${item.KodeMsn}" data-ket="${String(item.Ket ?? '').replace(/"/g, '&quot;')}">${item.KodeMsn} - ${item.Ket ?? ''}</option>`
      });
      document.getElementById('input_spk_kodemesin').innerHTML = opts
      document.getElementById('input_spk_mesin').value = ''
      if (terpilih) {
        document.getElementById('input_spk_kodemesin').value = terpilih
        spkPilihMesin()
      }
    },
    error: function () {
      alertify.error('Gagal memuat daftar Mesin')
    }
  });
}

function spkPilihMesin () {
  let sel = document.getElementById('input_spk_kodemesin')
  let opt = sel.options[sel.selectedIndex]
  document.getElementById('input_spk_mesin').value = opt ? (opt.getAttribute('data-ket') || '') : ''
}

// No. Bukti dihitung server tiap WIP/BJ diganti -- ProduksiSPKController::nextNoBukti().
// Belum tersimpan apa-apa sampai Barang Jadi pertama di-Simpan (lihat spkBJSimpan()), jadi
// aman dihitung ulang berkali-kali selama modal masih di tahap ini.
function spkGenerateNoBukti () {
  $.ajax({
    url: "{!! url('produksispknextnobukti') !!}",
    type: "get",
    data: {
      tipe: $('#input_spk_tipe').val(),
      bulan: $('#periode_bulan').val(),
      tahun: $('#periode_tahun').val(),
    },
    success: function (res) {
      spkNoBuktiAktif = res.nobukti
      document.getElementById('input_spk_nobukti').value = res.nobukti
      document.getElementById('input_spk_nourut').value = res.nourut
    },
    error: function () {
      alertify.error('Gagal menghitung No. Bukti')
    }
  });
}

// Koreksi SPK -- membuka modal Tambah SPK untuk No. Bukti yang sudah ada: header diisi dari
// DBSPK (produksispkheader), grid Barang Jadi/Bahan Baku dimuat ulang oleh handler
// shown.bs.modal di bawah (pakai spkNoBuktiAktif). Tiap Simpan/Hapus di dalam modal langsung
// tersimpan ke DB seperti di mode Tambah. Edit Barang Jadi (sp_SPKJD Choice='U') TIDAK
// menghitung ulang Bahan Baku hasil ledak-BOM.
function buttonEdit (nobukti) {
  $.ajax({
    url: "{!! url('produksispkheader') !!}",
    type: "get",
    data: { nobukti },
    success: function (res) {
      spkSetModeEdit(true)
      spkNoBuktiAktif = res.NOBUKTI
      document.getElementById('input_spk_nobukti').value = res.NOBUKTI
      document.getElementById('input_spk_nourut').value = res.NoUrut || ''
      document.getElementById('input_spk_tipe').value = String(res.NOBUKTI).endsWith('/BJ') ? 'BJ' : 'WIP'
      document.getElementById('input_spk_tanggal').value = res.TANGGAL ? String(res.TANGGAL).substring(0, 10) : ''
      document.getElementById('tabel_data_spkbj').innerHTML = '<tr><td colspan="6" class="text-center text-muted">Memuat...</td></tr>'
      document.getElementById('tabel_data_spkdet').innerHTML = '<tr><td colspan="6" class="text-center text-muted">Memuat...</td></tr>'
      $('#spkBJForm').hide()
      $('#spkDetForm').hide()

      spkIsiMesin(res.KodeMSN)
      $('#formAddSPK').modal('show')
    },
    error: function (xhr) {
      alertify.error(xhr.responseJSON?.message || 'Gagal memuat SPK')
    }
  });
}

// Hapus seluruh SPK -- ProduksiSPKController::hapusSPK() (ditolak server kalau SPK sudah
// ditutup/diotorisasi/dipakai jadwal produksi).
function buttonDelete (nobukti) {
  alertify.confirm('Hapus SPK', 'Apakah yakin ingin menghapus SPK "' + nobukti + '" beserta seluruh Barang Jadi dan Bahan Baku-nya?',
    function () {
      $.ajax({
        url: "{!! url('produksispkhapus') !!}",
        type: "post",
        data: { _token: $('#_token').val(), nobukti },
        success: function () {
          alertify.success('SPK berhasil dihapus')
          loadAll()
        },
        error: function (xhr) {
          alertify.error(xhr.responseJSON?.message || 'Gagal menghapus SPK')
        }
      });
    },
    function () {}
  );
}

function submitPrint (nobukti) {
  alertify.message('Cetak SPK belum tersedia')
}

// ---------- Barang Jadi (dbSPKBJDet, Sp_SPKBJ) ----------

function spkRefreshBJ () {
  $.ajax({
    url: "{!! url('produksispklistbj') !!}",
    type: "get",
    data: { nobukti: spkNoBuktiAktif },
    success: function (res) {
      let rowTable = ''
      res.forEach((item) => {
        rowTable += `
          <tr>
            <td>${item.KodeBrg ?? ''}</td>
            <td>${item.NamaBrg ?? ''}</td>
            <td>${formatAngka(Number(item.Qnt ?? 0).toFixed(2))}</td>
            <td>${item.Satuan ?? ''}</td>
            <td>${item.KodeBOM ?? ''}</td>
            <td class="text-center">
              <button class="btn-action-sm btn-action-success" type="button" onclick="spkBJEdit(${item.Urut}, '${item.KodeBrg}', ${item.Qnt ?? 0}, '${item.Satuan ?? ''}', '${item.KodeBOM ?? ''}')"><i class="bi bi-pen"></i></button>
              <button class="btn-action-sm btn-action-danger" type="button" onclick="spkBJHapus(${item.Urut}, '${String(item.NamaBrg ?? item.KodeBrg ?? '').replace(/'/g, "\\'").replace(/"/g, '&quot;')}')"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
        `
      });
      if (!res.length) {
        rowTable = '<tr><td colspan="6" class="text-center text-muted">Belum ada Barang Jadi</td></tr>'
      }
      document.getElementById('tabel_data_spkbj').innerHTML = rowTable
    },
    error: function () {
      alertify.error('Gagal memuat Barang Jadi')
    }
  });
}

function spkBJTambah () {
  document.getElementById('input_spkbj_urut').value = ''
  document.getElementById('input_spkbj_kodebom').value = ''
  document.getElementById('input_spkbj_kodebrg').value = ''
  document.getElementById('input_spkbj_satuan').value = ''
  document.getElementById('input_spkbj_qnt').value = '0.00'
  document.getElementById('btn_spkbj_barang').disabled = false
  document.getElementById('btn_spkbj_bom').disabled = false
  $('#spkBJForm').show()
}

function spkBJEdit (urut, kodebrg, qnt, satuan, kodebom) {
  // Barang/BOM dikunci saat Edit -- sp_SPKJD Choice='U' tidak mengubah KodeBOM dan tidak
  // menghitung ulang Bahan Baku, jadi yang bisa diubah di sini cuma Quantity.
  document.getElementById('btn_spkbj_barang').disabled = true
  document.getElementById('btn_spkbj_bom').disabled = true
  document.getElementById('input_spkbj_urut').value = urut
  document.getElementById('input_spkbj_kodebom').value = kodebom || ''
  document.getElementById('input_spkbj_kodebrg').value = kodebrg
  document.getElementById('input_spkbj_satuan').value = satuan || ''
  document.getElementById('input_spkbj_qnt').value = formatAngka(Number(qnt ?? 0).toFixed(2))
  $('#spkBJForm').show()
}

function spkBJTutup () {
  $('#spkBJForm').hide()
}

function spkBJSimpan () {
  let kodebrg = $('#input_spkbj_kodebrg').val()
  if (!kodebrg) {
    alertify.warning('Pilih Barang Jadi terlebih dahulu')
    return
  }

  let urut = $('#input_spkbj_urut').val()
  let choice = urut ? 'U' : 'I'

  // Tanpa Kode BOM, sp_SPKJD tidak punya komponen yang bisa diledakkan ke Bahan Baku.
  if (choice === 'I' && !$('#input_spkbj_kodebom').val()) {
    alertify.warning('Pilih Kode BOM terlebih dahulu')
    return
  }

  $.ajax({
    url: "{!! url('produksispkspspkjd') !!}",
    type: "post",
    data: {
      _token: $('#_token').val(),
      choice: choice,
      nobukti: spkNoBuktiAktif,
      nourut: $('#input_spk_nourut').val(),
      tanggal: $('#input_spk_tanggal').val(),
      kodebrg: kodebrg,
      qnt: unformatAngka($('#input_spkbj_qnt').val()),
      kodemsn: $('#input_spk_kodemesin').val(),
      kodebom: $('#input_spkbj_kodebom').val(),
      urut: urut,
    },
    success: function () {
      alertify.success(choice === 'U' ? 'Barang Jadi berhasil diubah' : 'Barang Jadi berhasil ditambahkan')
      $('#spkBJForm').hide()
      spkRefreshBJ()
      spkRefreshDet()
    },
    error: function () {
      alertify.error('Gagal menyimpan Barang Jadi')
    }
  });
}

function spkBJHapus (urut, namabrg) {
  alertify.confirm('Hapus Barang Jadi', 'Apakah yakin ingin menghapus "' + namabrg + '" dari SPK ini?',
    function () {
      $.ajax({
        url: "{!! url('produksispkspspkjd') !!}",
        type: "post",
        data: {
          _token: $('#_token').val(),
          choice: 'D',
          nobukti: spkNoBuktiAktif,
          urut: urut,
        },
        success: function () {
          alertify.success('Barang Jadi berhasil dihapus')
          spkRefreshBJ()
          spkRefreshDet()
        },
        error: function () {
          alertify.error('Gagal menghapus Barang Jadi')
        }
      });
    },
    function () {}
  );
}

// Picker Kode Barang (Barang Jadi) -- DataTable serverSide:true lewat produksispklistbarangjadi.
// Dipilih PERTAMA; setelah itu baru Kode BOM milik barang ini (spkPilihBOM()). Mengganti barang
// mengosongkan Kode BOM yang sudah terpilih (BOM-nya milik barang sebelumnya).
let tabelBarangJadiDT = null

function spkPilihBarangJadi () {
  document.getElementById('input_cari_barangjadi').value = ''

  if (!tabelBarangJadiDT) {
    document.getElementById('tabel_data_barangjadi').innerHTML = ''
    tabelBarangJadiDT = $('#tabel_barangjadi').DataTable({
      serverSide: true,
      processing: true,
      searching: true,
      ordering: false,
      dom: 'ltip',
      ajax: {
        url: "{!! url('produksispklistbarangjadi') !!}",
        type: 'GET',
      },
      columns: [
        { data: 'KodeBrg' },
        { data: 'NamaBrg' },
        { data: 'Sat1' },
      ],
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      pageLength: 10,
      language: {
        lengthMenu: 'Tampilkan _MENU_',
        emptyTable: 'Tidak ada barang yang punya BOM',
        zeroRecords: 'Tidak ada data yang cocok dengan pencarian',
        processing: 'Memuat...'
      },
      createdRow: function (row, data) {
        $(row).addClass('pick-row').on('click', function () {
          spkPilihBarangJadiSet(data.KodeBrg, data.Sat1)
        })
      }
    })
  }

  tabelBarangJadiDT.search('').draw()
  $('#formPilihBarangJadi').modal('show')
}

$(document).on('keyup', '#input_cari_barangjadi', function (e) {
  if (e.key === 'Enter' && tabelBarangJadiDT) {
    tabelBarangJadiDT.search($(this).val()).draw()
  }
});

function spkPilihBarangJadiSet (kodebrg, satuan) {
  if (document.getElementById('input_spkbj_kodebrg').value !== kodebrg) {
    document.getElementById('input_spkbj_kodebom').value = ''
  }
  document.getElementById('input_spkbj_kodebrg').value = kodebrg
  document.getElementById('input_spkbj_satuan').value = satuan || ''
  $('#formPilihBarangJadi').modal('hide')
}

// Picker Kode BOM -- cuma BOM berproses milik Kode Barang yang sudah dipilih
// (produksispklistbombybarang).
function spkPilihBOM () {
  let kodebrg = document.getElementById('input_spkbj_kodebrg').value
  if (!kodebrg) {
    alertify.warning('Pilih Kode Barang terlebih dahulu')
    return
  }

  $.ajax({
    url: "{!! url('produksispklistbombybarang') !!}",
    type: "get",
    data: { kodebrg },
    success: function (res) {
      let rowTable = ''
      res.forEach((item) => {
        rowTable += `
          <tr class="pick-row" onclick="spkPilihBOMSet('${item.KodeBOM}')">
            <td>${item.KodeBOM}</td>
            <td class="text-center">${Number(item.IsDefault) ? '<i class="bi bi-check2 text-success"></i>' : ''}</td>
          </tr>
        `
      });
      if (!res.length) {
        rowTable = '<tr><td colspan="2" class="text-center text-muted">Belum ada BOM</td></tr>'
      }
      document.getElementById('tabel_data_pilihbom').innerHTML = rowTable
      pickerKasInit('tabel_pilihbom')
      $('#formPilihBOM').modal('show')
    },
    error: function () {
      alertify.error('Gagal memuat Kode BOM')
    }
  });
}

function spkPilihBOMSet (kodebom) {
  document.getElementById('input_spkbj_kodebom').value = kodebom
  $('#formPilihBOM').modal('hide')
}

// ---------- Bahan Baku (dbSPKDet, sp_SPK) ----------

function spkRefreshDet () {
  $.ajax({
    url: "{!! url('produksispklistdetail') !!}",
    type: "get",
    data: { nobukti: spkNoBuktiAktif },
    success: function (res) {
      let rowTable = ''
      res.forEach((item) => {
        rowTable += `
          <tr>
            <td>${item.KODEBRG ?? ''}</td>
            <td>${item.NamaBrg ?? ''}</td>
            <td>${item.KodePrs ? item.KodePrs + (item.NamaPrs ? ' - ' + item.NamaPrs : '') : '-'}</td>
            <td>${formatAngka(Number(item.QNT ?? 0).toFixed(2))}</td>
            <td>${item.SATUAN ?? ''}</td>
            <td class="text-center">
              <button class="btn-action-sm btn-action-success" type="button" onclick="spkDetEdit(${item.URUT}, '${item.KODEBRG}', ${item.QNT ?? 0}, '${item.SATUAN ?? ''}', '${item.KodePrs ?? ''}')"><i class="bi bi-pen"></i></button>
              <button class="btn-action-sm btn-action-danger" type="button" onclick="spkDetHapus(${item.URUT}, ${item.URUTSPK ?? 0}, '${String(item.NamaBrg ?? item.KODEBRG ?? '').replace(/'/g, "\\'").replace(/"/g, '&quot;')}')"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
        `
      });
      if (!res.length) {
        rowTable = '<tr><td colspan="6" class="text-center text-muted">Belum ada Bahan Baku</td></tr>'
      }
      document.getElementById('tabel_data_spkdet').innerHTML = rowTable
    },
    error: function () {
      alertify.error('Gagal memuat Bahan Baku')
    }
  });
}

// Dropdown Kode Proses form Bahan Baku -- hanya proses yang terikat ke Kode BOM Barang Jadi di
// SPK ini (ProduksiSPKController::listProsesSPK()). terpilih (opsional) -- KodePrs yang langsung
// dipilih setelah daftar termuat (dipakai spkDetEdit(); kalau proses baris itu sudah tidak ada di
// daftar, tetap ditambahkan supaya tidak hilang saat disimpan ulang).
function spkIsiProses (terpilih) {
  $.ajax({
    url: "{!! url('produksispklistproses') !!}",
    type: "get",
    data: { nobukti: spkNoBuktiAktif },
    success: function (res) {
      let opts = '<option value="">-</option>'
      let ada = false
      res.forEach((item) => {
        if (item.KodePrs === terpilih) { ada = true }
        opts += `<option value="${item.KodePrs}">${item.KodePrs} - ${item.Keterangan ?? ''}</option>`
      });
      if (terpilih && !ada) {
        opts += `<option value="${terpilih}">${terpilih}</option>`
      }
      document.getElementById('input_spkdet_kodeprs').innerHTML = opts
      document.getElementById('input_spkdet_kodeprs').value = terpilih || ''
      if (!res.length && !terpilih) {
        alertify.warning('Tambah Barang Jadi (dengan Kode BOM) terlebih dahulu, proses diambil dari BOM-nya')
      }
    },
    error: function () {
      alertify.error('Gagal memuat Kode Proses')
    }
  });
}

function spkDetTambah () {
  document.getElementById('input_spkdet_urut').value = ''
  document.getElementById('input_spkdet_kodebrg').value = ''
  document.getElementById('input_spkdet_satuan').value = ''
  document.getElementById('input_spkdet_qnt').value = '0.00'
  spkIsiProses()
  $('#spkDetForm').show()
}

function spkDetEdit (urut, kodebrg, qnt, satuan, kodeprs) {
  document.getElementById('input_spkdet_urut').value = urut
  document.getElementById('input_spkdet_kodebrg').value = kodebrg
  document.getElementById('input_spkdet_satuan').value = satuan || ''
  document.getElementById('input_spkdet_qnt').value = formatAngka(Number(qnt ?? 0).toFixed(2))
  spkIsiProses(kodeprs || '')
  $('#spkDetForm').show()
}

function spkDetTutup () {
  $('#spkDetForm').hide()
}

function spkDetSimpan () {
  let kodebrg = $('#input_spkdet_kodebrg').val()
  if (!kodebrg) {
    alertify.warning('Pilih Kode Barang terlebih dahulu')
    return
  }

  let kodeprs = $('#input_spkdet_kodeprs').val()
  if (!kodeprs) {
    alertify.warning('Pilih Kode Proses terlebih dahulu')
    return
  }

  let urut = $('#input_spkdet_urut').val()
  let choice = urut ? 'U' : 'I'

  $.ajax({
    url: "{!! url('produksispkspspk') !!}",
    type: "post",
    data: {
      _token: $('#_token').val(),
      choice: choice,
      nobukti: spkNoBuktiAktif,
      kodebrg: kodebrg,
      kodeprs: kodeprs,
      qnt: unformatAngka($('#input_spkdet_qnt').val()),
      isi: 0,
      satuan: $('#input_spkdet_satuan').val(),
      urut: urut,
    },
    success: function () {
      alertify.success(choice === 'U' ? 'Bahan Baku berhasil diubah' : 'Bahan Baku berhasil ditambahkan')
      $('#spkDetForm').hide()
      spkRefreshDet()
    },
    error: function () {
      alertify.error('Gagal menyimpan Bahan Baku')
    }
  });
}

// urutspk -- sp_SPK Choice='D' menghapus berdasarkan NoBukti+URUTSPK+URUT. Baris hasil ledak-BOM
// (dibuat sp_SPKJD) membawa URUTSPK = Urut baris Barang Jadi asalnya; baris manual URUTSPK = 0.
function spkDetHapus (urut, urutspk, namabrg) {
  alertify.confirm('Hapus Bahan Baku', 'Apakah yakin ingin menghapus "' + namabrg + '" dari SPK ini?',
    function () {
      $.ajax({
        url: "{!! url('produksispkspspk') !!}",
        type: "post",
        data: {
          _token: $('#_token').val(),
          choice: 'D',
          nobukti: spkNoBuktiAktif,
          urut: urut,
          urutspk: urutspk,
        },
        success: function () {
          alertify.success('Bahan Baku berhasil dihapus')
          spkRefreshDet()
        },
        error: function () {
          alertify.error('Gagal menghapus Bahan Baku')
        }
      });
    },
    function () {}
  );
}

// Picker Kode Barang Bahan Baku -- DataTable serverSide:true, endpoint sama persis dengan
// picker Kode Barang BOM (ProduksiBomController::listKodeBarang()), lihat
// modalProduksiBOM.blade.php's buttonKodeBarangDetail() -- pola disalin dari sana.
let tabelBahanBakuDT = null

function spkPilihBahanBaku () {
  document.getElementById('input_cari_bahanbaku').value = ''

  if (!tabelBahanBakuDT) {
    document.getElementById('tabel_data_bahanbaku').innerHTML = ''
    tabelBahanBakuDT = $('#tabel_bahanbaku').DataTable({
      serverSide: true,
      processing: true,
      searching: true,
      ordering: false,
      dom: 'ltip',
      ajax: {
        url: "{!! url('produksibomlistkodebarang') !!}",
        type: 'GET',
      },
      columns: [
        { data: 'KODEBRG' },
        { data: 'NAMABRG' },
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
          document.getElementById('input_spkdet_kodebrg').value = data.KODEBRG
          document.getElementById('input_spkdet_satuan').value = data.SAT1 || ''
          $('#formPilihBahanBaku').modal('hide')
        })
      }
    })
  }

  tabelBahanBakuDT.search('').draw()
  $('#formPilihBahanBaku').modal('show')
}

$(document).on('keyup', '#input_cari_bahanbaku', function (e) {
  if (e.key === 'Enter' && tabelBahanBakuDT) {
    tabelBahanBakuDT.search($(this).val()).draw()
  }
});

// Sinkronkan grid Barang Jadi + Bahan Baku begitu modal Tambah SPK selesai dibuka (No. Bukti
// sudah pasti ada walau belum tersimpan di DB -- grid-nya sendiri cuma akan nemu kosong
// sampai baris pertama disimpan, itu wajar).
$('#formAddSPK').on('shown.bs.modal', function () {
  spkRefreshBJ()
  spkRefreshDet()
});

// Daftar SPK dimuat ulang tiap modal Tambah/Koreksi ditutup -- isinya bisa berubah (SPK baru,
// atau SPK terhapus kalau Barang Jadi terakhirnya dihapus dari dalam modal).
$('#formAddSPK').on('hidden.bs.modal', function () {
  loadAll()
});

// Modal pemilih (Kode BOM/Barang Jadi/Bahan Baku) disamakan persis dengan modal pemilih
// menu Kas/SO/PO -- pola sama dengan DOMContentLoaded di produksibom.blade.php.
document.addEventListener('DOMContentLoaded', function () {
  jQuery('#formPilihBarangJadi, #formPilihBOM, #formPilihBahanBaku').addClass('picker-kas')
  jQuery('#formPilihBarangJadi .modal-footer .btn, #formPilihBOM .modal-footer .btn, #formPilihBahanBaku .modal-footer .btn').filter(function () {
    return jQuery.trim(jQuery(this).text()) === 'Batal'
  }).addClass('picker-kas-batal')
})

window.onload = function () {
  MasterList.kolom({ href: 'produksispk', kolom: SPK_KOLOM, onChange: loadAll })
  loadAll();
};

</script>
@endsection
