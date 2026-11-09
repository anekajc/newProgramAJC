@extends('newmasterTest')
@section('buttons')
@section('page-title', 'Hasil Produksi')

@endsection

@section('css')
<script>var scwJsPath = "{!! URL::asset('js/canvas') !!}";</script>
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">
{{-- Modal pemilih (No. SPK, dst.) disamakan dengan modal pemilih menu Kas / Debet Note. --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">
<style>
  /* Isi tabel dibuat sebaris; kolom panjang digeser lewat scroll horizontal .po-table-wrap. */
  .po-table-wrap table.dataTable thead th,
  .po-table-wrap table.dataTable tbody td {
    white-space: nowrap;
  }

  /* Halaman dirancang mengisi tinggi layar (lihat hpAturTinggiTabel()). */
  #content { padding-top: 12px; }

  /* Rule .card global layout untuk kartu menu dashboard, bukan kartu berisi tabel. */
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
    border-color: var(--border) !important;
  }

  /* DataTables menulis lebar hasil ukur sebagai inline style; pakai min-width lewat ID. */
  #tabel2 {
    min-width: 100%;
  }

  /* ---------- Kolom Action - tombol bulat kecil warna pastel (sama dengan Debet Note). ---------- */
  #tabel2 td:first-child:not([colspan]) {
    vertical-align: middle;
  }

  #tabel2 td:first-child .po-aksi-wrap {
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
  }

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

  #tabel2 td:first-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #tabel2 td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #tabel2 td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
  #tabel2 td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
  #tabel2 td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
  #tabel2 td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

  /* Tombol di kolom Action baru muncul saat barisnya di-hover. */
  table.data-table.po-aksi-hover tbody td:first-child .btn {
    visibility: hidden;
    opacity: 0;
    transition: opacity .12s ease;
  }
  table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
    visibility: visible;
    opacity: 1;
  }

  /* Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar. */
  .po-len-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--rt-card);
    border: 1.5px solid var(--rt-border);
    border-radius: 8px;
    padding: 5px 12px;
  }

  .po-len-wrap label {
    margin: 0;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--rt-ink-soft);
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
  }

  .po-len-inp {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 700;
    color: var(--rt-ink);
    outline: none;
    cursor: pointer;
    padding: 2px 20px 2px 0;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat: no-repeat;
    background-position: right center;
  }

  /* Tombol Tambah - chip biru (sama dengan Debet Note). */
  .btn-chip-biru {
    background-color: #e8edff;
    border-color: #cfdcff;
    color: #2563eb;
  }

  .btn-chip-biru:hover,
  .btn-chip-biru:focus {
    background-color: #dce6ff;
    border-color: #b9c9ff;
    color: #1d4ed8;
  }

  .btn-chip-biru:active {
    background-color: #cfdcff !important;
    border-color: #a8bdff !important;
    color: #1d4ed8 !important;
  }

  /* ================= Form Tambah/Koreksi (#page2) & Detail (#page3) =================
     Disalin dari purchasing/pembelianpermintaanagen.blade.php supaya seragam. */
  #formBsGrid textarea.form-control,
  #formBsGrid table .form-control {
    height: auto;
  }
  #formBsGrid .btn:has(> .bi-search) {
    height: 38px !important;
  }
  #formBsGrid .input-group .btn:has(> .bi-search) {
    height: auto !important;
    align-self: stretch;
    padding-top: 0;
    padding-bottom: 0;
  }
  .btn-chip-biru:has(> .bi-search) {
    border-radius: 6px !important;
  }
  .form-control:has(+ .btn > .bi-search) {
    border-top-right-radius: 6px !important;
    border-bottom-right-radius: 6px !important;
  }

  /* Batal = aksi sekunder, abu-abu muda. */
  .btn-batal-add {
    background-color: #f1f3f5;
    border-color: #dee2e6;
    color: #495057;
  }
  .btn-batal-add:hover,
  .btn-batal-add:focus {
    background-color: #e9ecef;
    border-color: #ced4da;
    color: #343a40;
  }

  /* Tabel item (#tabel_add di form, #tabel_detail di Detail) - header abu-abu uppercase +
     zebra + hover. */
  #tabel_add thead th,
  #tabel_detail thead th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #e7e9ee;
    border-top: none;
    white-space: nowrap;
  }
  #tabel_add tbody tr:nth-of-type(odd),
  #tabel_detail tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
  #tabel_add tbody tr:hover,
  #tabel_detail tbody tr:hover { background-color: #f5f3ff; }
  #tabel_add td,
  #tabel_detail td { white-space: nowrap; }

  /* Kolom Actions (paling kanan) di #tabel_add - tombol bulat kecil warna pastel. */
  #tabel_add td:last-child:not([colspan]) {
    text-align: center;
    white-space: nowrap;
  }
  #tabel_add td:last-child .btn + .btn { margin-left: 4px; }
  #tabel_add td:last-child .btn {
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
  }
  #tabel_add td:last-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #tabel_add td:last-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

  /* Modal pilih No. SPK - baris diklik langsung (tanpa kolom Actions), sama dengan
     modal Supplier di Debet Note. */
  #tabel_pilih_spk tbody tr.pick-row,
  #tabel_pilih_barang_avalan tbody tr.pick-row,
  #tabel_pilih_barang_spk tbody tr.pick-row {
    cursor: pointer;
    transition: background-color .12s;
  }
  #tabel_pilih_spk tbody tr.pick-row:hover td,
  #tabel_pilih_barang_avalan tbody tr.pick-row:hover td,
  #tabel_pilih_barang_spk tbody tr.pick-row:hover td {
    background-color: #eef2ff;
  }

  /* Label dua kata (Nama Mesin, No. Satuan) tetap sebaris, tidak turun ke bawah. */
  .hp-nowrap { white-space: nowrap; }

  .hp-cek-avalan {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    cursor: pointer;
    margin: 0;
  }
  .hp-cek-avalan input { transform: scale(1.3); }
</style>
@endsection

@section('content')

<div id="page1" class="container-fluid mainpage">

<div id="contentContainer" class="container-fluid">
  <input type="hidden" id="periode_tahun" value="{{ $periode->tahun }}" />
  <input type="hidden" id="periode_bulan" value="{{ $periode->bulan }}" />
  <input type="hidden" id="akses_istambah" value="{{ $akses->ISTAMBAH }}" />
  <input type="hidden" id="akses_ishapus" value="{{ $akses->ISHAPUS }}" />
  <input type="hidden" id="akses_iskoreksi" value="{{ $akses->ISKOREKSI }}" />
  <input type="hidden" id="akses_iscetak" value="{{ $akses->ISCETAK }}" />
  <input type="hidden" id="akses_isotorisasi1" value="{{ $akses->IsOtorisasi1 }}" />
  <input type="hidden" id="akses_isbatal" value="{{ $akses->IsBatal }}" />
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

      <div class="po-toolbar">
        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" class="po-filter-inp" id="hpTglAwal" value="{{ $hpTglAwal }}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" class="po-filter-inp" id="hpTglAkhir" value="{{ $hpTglAkhir }}">
        </div>
        <input type="search" id="hpSearch" class="po-search-inp" placeholder="Cari data">
        {{-- Jumlah baris per halaman - lihat hpIkatPanjangHalaman(). --}}
        <div class="po-len-wrap">
          <label for="hpLen">Tampilkan</label>
          <select id="hpLen" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>
        <button class="po-btn-filter" type="button" id="hpBtnFilter" onclick="$('#modalFilterHP').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <div class="po-toolbar-act">
          <button class="btn btn-chip-biru" type="button" onclick="buttonAdd()">Tambah</button>
        </div>
      </div>

      {{-- #rtBar diisi lewat JS oleh ReportTable.init() - lihat hpInitReportTableSekali(). --}}
      <div id="rtBar"></div>

      <table id="tabel2" class="data-table po-aksi-hover">
        <thead id="tabel_header" class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Action</th>
            <th style="padding: 4px 12px;" scope="col">No Bukti</th>
            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
            <th style="padding: 4px 12px;" scope="col">Shift</th>
            <th style="padding: 4px 12px;" scope="col">Otorisasi</th>
            <th style="padding: 4px 12px;" scope="col">User Oto</th>
            <th style="padding: 4px 12px;" scope="col">Tanggal Oto</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
          {{-- Baris digambar renderTabelHP() lewat JS. --}}
        </tbody>
      </table>

      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom.
      </div>
    </div>
  </div>
</div>
</div>

{{-- ================= Form Tambah / Koreksi =================
     Pola pembelianpermintaanagen: header di atas, tombol Tambah Item, tabel item, panel item
     (#hpPanelItem) muncul di bawah tabel. Item langsung disimpan per baris lewat Sp_HasilPrd
     (header dibuat SP saat item pertama). Jam Actual & Nama sengaja belum dipakai. --}}
<div id="page2" class="container-fluid" style="display:none;">
<div id="formBsGrid">
  <div class="row">
    <div class="col-6 text-left"></div>
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
          onclick="buttonCloseForm()">
        Close
      </button>
    </div>
  </div>

  <div class="modal-body">
    <div class="container-fluid">
      {{-- No Urut tetap ada, disembunyikan (seperti menu purchasing/marketing lain). --}}
      <input type="hidden" id="input_add_nourut" value="">

      <div class="row">
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label for="input_add_tipe">WIP/BJ</label></div></div>
            <div class="col-md-4">
              <div class="form-group">
                <select id="input_add_tipe" class="form-control text-left" onchange="hpUbahNoBukti()">
                  <option value="WIP">WIP</option>
                  <option value="BJ">BJ</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label>No Bukti</label></div></div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label for="input_add_tanggal">Tanggal</label></div></div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-left" id="input_add_tanggal" value="{{ date('Y-m-d') }}" onchange="hpUbahNoBukti()">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label for="input_add_nospk">No. SPK</label></div></div>
            <div class="col-md-8">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-left" id="input_add_nospk" placeholder="No. SPK" onchange="hpCariSPKKetik()">
                <button type="button" id="buttonBrowseSPK" class="btn btn-chip-biru btn-sm" onclick="buttonBrowseSPK()"><i class="bi bi-search"></i></button>
              </div>
              {{-- Urut baris DBSPKJD yang dipilih - dikirim ke Sp_HasilPrd (@urutSPK). --}}
              <input type="hidden" id="input_add_urutspk" value="0">
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label for="input_add_kodemsn">Mesin</label></div></div>
            <div class="col-md-8">
              <div class="form-group">
                {{-- Terisi otomatis dari SPK yang dipilih (DBSPK.KodeMSN). --}}
                <input type="text" class="form-control text-left" id="input_add_kodemsn" placeholder="Kode Mesin" disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label for="input_add_namamsn" class="hp-nowrap">Nama Mesin</label></div></div>
            <div class="col-md-8">
              <div class="form-group">
                {{-- Diisi otomatis setelah memilih mesin lewat browse (menyusul). --}}
                <input type="text" class="form-control text-left" id="input_add_namamsn" placeholder="Nama Mesin" disabled>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label for="input_add_shift">Shift</label></div></div>
            <div class="col-md-4">
              <div class="form-group">
                <select id="input_add_shift" class="form-control text-left">
                  <option value="">-</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8" style="margin-top:8px;">
          <small class="text-muted" id="hpInfoHeaderKunci" style="display:none;">
            <i class="bi bi-lock"></i> Header terkunci karena sudah ada item tersimpan.
          </small>
        </div>
      </div>
    </div>

    <div class="container-fluid mt-4">
      <div class="row">
        <table id="tabel_add" class="data-table">
          <thead class="text-center">
            <tr>
              <th scope="col">No. SPK</th>
              <th scope="col">Kode Barang</th>
              <th scope="col">Nama Barang</th>
              <th scope="col" class="text-right">Hasil</th>
              <th scope="col" class="text-right">Reject</th>
              <th scope="col">Satuan</th>
              <th scope="col">Gudang</th>
              <th scope="col" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="tabel_data_add" class="text-left">
            <tr><td class="text-center" colspan="8">Belum ada barang</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    {{-- Tombol Tambah Item di bawah tabel item, kanan - sama dengan marketing/so.blade.php. --}}
    <div class="row mb-3">
      <div class="col-md-12 mt-2 text-right">
        <button type="button" id="buttonTambahItem" class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              transition: background-color 0.3s, box-shadow 0.3s;"
          onclick="buttonAddAddItem()">Tambah Item</button>
      </div>
    </div>

    {{-- Panel Tambah / Koreksi Item --}}
    <div id="hpPanelItem" class="container-fluid showhide" style="display:none;">
      <div class="row">
        <div class="col-4">
          <h4 id="hpJudulPanelItem" style="margin-left:-35px;">Tambah Item</h4>
        </div>
      </div>
      <input type="hidden" id="input_item_urut" value="0">
      {{-- Urut baris DBSPKJD barang yang dipilih (tanpa avalan) - batas sisa & @urutSPK. --}}
      <input type="hidden" id="input_item_urutspk" value="0">
      <div class="row">
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-8">
              <div class="form-group">
                <label class="hp-cek-avalan" for="input_item_isav">
                  <input type="checkbox" id="input_item_isav" onchange="hpUbahAvalan()"> Avalan / Reject?
                </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label for="input_item_kodebrg">Kode Barang</label></div></div>
            <div class="col-md-4">
              <div class="input-group mb-3">
                {{-- Bisa diketik: Enter -> cari barang (pola pembelianpermintaanagen). --}}
                <input type="text" class="form-control text-left" id="input_item_kodebrg" placeholder="Kode Barang" autocomplete="off">
                <button type="button" id="buttonBrowseBarang" class="btn btn-chip-biru btn-sm" onclick="buttonBrowseBarang()"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label for="input_item_namabrg">Nama Barang</label></div></div>
            <div class="col-md-8">
              <div class="form-group">
                {{-- Diisi otomatis setelah memilih barang lewat browse. --}}
                <input type="text" class="form-control text-left" id="input_item_namabrg" disabled>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label for="input_item_qnt">Qty</label></div></div>
            <div class="col-md-3">
              <div class="form-group">
                <input type="text" class="form-control text-right" id="input_item_qnt" value="0.00" inputmode="decimal" autocomplete="off" onfocus="this.select()" oninput="hpFormatRibuanLive(this)" onblur="hpRapikanAngka(this)">
              </div>
            </div>
            <div class="col-md-2" style="margin-top:5px;"><div class="form-group"><label for="input_item_nosat" class="hp-nowrap">No. Satuan</label></div></div>
            <div class="col-md-3">
              <div class="form-group">
                <select id="input_item_nosat" class="form-control text-left" disabled>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label for="input_item_reject">Reject</label></div></div>
            <div class="col-md-3">
              <div class="form-group">
                <input type="text" class="form-control text-right" id="input_item_reject" value="0.00" inputmode="decimal" autocomplete="off" onfocus="this.select()" oninput="hpFormatRibuanLive(this)" onblur="hpRapikanAngka(this)">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label for="input_item_kodegdg">Gudang</label></div></div>
            {{-- col-md-5 = lebar Qty (3) + label No. Satuan (2), jadi ujungnya sejajar label itu. --}}
            <div class="col-md-5">
              <div class="form-group">
                {{-- Isi dari DBGUDANG DBSMLNEW - lihat hpIsiGudang(). --}}
                <select id="input_item_kodegdg" class="form-control text-left">
                  <option value="">Pilih Gudang</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-md-12 text-right">
          <button type="button" class="btn btn-lg btn-batal-add" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="closeShowHideAdd()">Batal</button>

          <button type="button" id="hpSimpanItem" class="btn btn-lg btn-chip-biru" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="submitItem()">Simpan</button>
        </div>
      </div>
    </div>
  </div>
</div>{{-- /#formBsGrid --}}
</div>{{-- /#page2 --}}

{{-- ================= Detail (read-only) ================= --}}
<div id="page3" class="container-fluid" style="display:none;">
<div id="formBsGrid">
  <div class="row">
    <div class="col-6 text-left"></div>
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
          onclick="buttonCloseForm()">
        Close
      </button>
    </div>
  </div>

  <div class="modal-body">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label>WIP/BJ</label></div></div>
            <div class="col-md-4"><div class="form-group"><input type="text" class="form-control" id="input_detail_tipe" disabled></div></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label>No Bukti</label></div></div>
            <div class="col-md-8"><div class="form-group"><input type="text" class="form-control" id="input_detail_nobukti" disabled></div></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label>Tanggal</label></div></div>
            <div class="col-md-8"><div class="form-group"><input type="date" class="form-control" id="input_detail_tanggal" disabled></div></div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label>No. SPK</label></div></div>
            <div class="col-md-8"><div class="form-group"><input type="text" class="form-control" id="input_detail_nospk" disabled></div></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label>Mesin</label></div></div>
            <div class="col-md-8"><div class="form-group"><input type="text" class="form-control" id="input_detail_mesin" disabled></div></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label class="hp-nowrap">Nama Mesin</label></div></div>
            <div class="col-md-8"><div class="form-group"><input type="text" class="form-control" id="input_detail_namamsn" disabled></div></div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;"><div class="form-group"><label>Shift</label></div></div>
            <div class="col-md-4"><div class="form-group"><input type="text" class="form-control" id="input_detail_shift" disabled></div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid mt-4">
      <div class="row">
        <table id="tabel_detail" class="data-table">
          <thead>
            <tr>
              <th scope="col">No. SPK</th>
              <th scope="col">Kode Barang</th>
              <th scope="col">Nama Barang</th>
              <th scope="col" class="text-right">Hasil</th>
              <th scope="col" class="text-right">Reject</th>
              <th scope="col">Satuan</th>
              <th scope="col">Gudang</th>
              <th scope="col" class="text-center">Avalan</th>
            </tr>
          </thead>
          <tbody id="tabel_data_detail" class="text-left">
            <tr><td class="text-center" colspan="8">Belum ada barang</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>{{-- /#formBsGrid --}}
</div>{{-- /#page3 --}}

{{-- ================= Modal pilih Kode Barang - AVALAN dicentang =================
     Pola pembelianpermintaanagen (#formAddListItem): 18rb barang aktif, jadi dicari ke server
     lewat kotak Search modal (Enter) atau kolom Kode Barang (Enter). Gaya picker-kas, tanpa
     kolom Actions - baris diklik langsung. --}}
<div class="modal fade picker-kas" id="formPilihBarangAvalan" tabindex="-1" role="dialog" aria-labelledby="formPilihBarangAvalanLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formPilihBarangAvalanLabel">Barang (Avalan / Reject)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:0px;">
              <table id="tabel_pilih_barang_avalan">
                <thead>
                  <tr>
                    <th scope="col">No Bukti</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col" class="text-right">Qty</th>
                    <th scope="col" class="text-right">Isi2</th>
                    <th scope="col" class="text-right">Isi3</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_pilih_barang_avalan"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" onclick="$('#formPilihBarangAvalan').modal('hide')">Batal</button>
      </div>
    </div>
  </div>
</div>

{{-- ================= Modal pilih Kode Barang - TANPA avalan =================
     Barang SPK di header yang masih bersisa (sedikit, dimuat sekaligus). --}}
<div class="modal fade picker-kas" id="formPilihBarangSPK" tabindex="-1" role="dialog" aria-labelledby="formPilihBarangSPKLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formPilihBarangSPKLabel">Barang SPK</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:0px;">
              <table id="tabel_pilih_barang_spk">
                <thead>
                  <tr>
                    <th scope="col">No SPK</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col" class="text-right">Qty</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_pilih_barang_spk"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" onclick="$('#formPilihBarangSPK').modal('hide')">Batal</button>
      </div>
    </div>
  </div>
</div>

{{-- ================= Modal pilih No. SPK =================
     Gaya picker-kas (Debet Note / PR Agen): tanpa kolom Actions, baris diklik langsung. --}}
<div class="modal fade picker-kas" id="formPilihSPK" tabindex="-1" role="dialog" aria-labelledby="formPilihSPKLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formPilihSPKLabel">No. SPK</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:0px;">
              <table id="tabel_pilih_spk">
                <thead>
                  <tr>
                    <th scope="col">No. SPK</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col" class="text-right">Sisa</th>
                    <th scope="col">Mesin</th>
                    <th scope="col">Nama Mesin</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_pilih_spk" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" onclick="$('#formPilihSPK').modal('hide')">Batal</button>
      </div>
    </div>
  </div>
</div>

<!-- modal filter otorisasi -->
<div class="modal fade rt-filter" id="modalFilterHP">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Hasil Produksi
          <span class="rt-active-badge" id="hpFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterHP').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="hpModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="hpModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="hpResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterHP').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="hpTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">
// Dipatok, harus sama persis dengan href di ProduksiHasilProduksiController@loadAll dan
// cabang 'produksihasilproduksi' di HeaderTableController@getHeaderTable.
const HP_HREF = 'produksihasilproduksi'

let hpCart = []
let dataHP = []

function hpBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
  let cart = []
  ;(headers || []).forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let tipeNama = { 0 : 'varchar', 1 : 'float', 2 : 'date' }
    let label = h
    if (aliasordered && aliasordered[i] && aliasordered[i].alias) {
      label = aliasordered[i].alias
    }
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null)
      ? Number(desimals[i])
      : (tipe === 1 ? 2 : 0)

    cart.push([
      tipe === 0 ? h : values[i],
      label,
      Number(isshowns[i]) === 1 ? 1 : 0,
      tipeNama[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
      h,
      values[i],
      tipe
    ])
  });
  return cart
}

function hpKolomTampil () {
  return (hpCart || []).filter(c => Number(c[2]) === 1)
}

function hpKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

function hpFormatAngkaDes (nilai, des) {
  let d = Number(des)
  if (isNaN(d) || d < 0) { d = 0 }

  let mentah = (nilai === null || nilai === undefined || nilai === '') ? 0 : nilai
  let angka = Number(String(mentah).split(',').join(''))
  if (isNaN(angka)) {
    return (nilai === null || nilai === undefined) ? '' : nilai
  }

  let teks = angka.toFixed(d)
  let minus = teks.charAt(0) === '-'
  if (minus) { teks = teks.substring(1) }

  let bagian = teks.split('.')
  let bulat = bagian[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  return (minus ? '-' : '') + bulat + (bagian[1] ? '.' + bagian[1] : '')
}

function hpEsc (nilai) {
  return String(nilai === null || nilai === undefined ? '' : nilai)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#39;')
}

function hpRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return hpFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai).split('-').reverse().join('/') : ""
  }
  // Tanggal yang datang sebagai teks 'yyyy-mm-dd...' (hasil CONVERT di SQL) tampil dd/mm/yyyy.
  if (typeof nilai === 'string' && /^\d{4}-\d{2}-\d{2}/.test(nilai)) {
    return nilai.substr(0, 10).split('-').reverse().join('/')
  }
  return hpEsc(nilai)
}

function hpHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan. Pastikan public/js/report-table.js ada di server.')
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

let hpRtSudahInit = false

function hpInitReportTableSekali () {
  if (hpRtSudahInit || typeof ReportTable === 'undefined') { return }
  hpRtSudahInit = true

  ReportTable.init({
    table    : '#tabel2',
    bar      : '#rtBar',
    onChange : renderTabelHP
  })

  // Klik roda gigi / pegangan geser jangan sampai memicu sorting DataTables (sama dengan Debet Note).
  let hpGuardUlangKlik = false
  let thead = document.getElementById('tabel_header')
  if (thead) {
    thead.addEventListener('click', function (e) {
      if (hpGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      hpGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      hpGuardUlangKlik = false
    }, true)
  }
}

function hpPindahBar () {
  let bar = document.getElementById('rtBar')
  let tabel = document.getElementById('tabel2')
  if (!bar || !tabel) { return }

  let acuan = tabel
  if ($.fn.DataTable.isDataTable('#tabel2')) {
    acuan = document.getElementById('tabel2_wrapper') || tabel
  }

  if (acuan.previousElementSibling !== bar) {
    acuan.parentNode.insertBefore(bar, acuan)
  }
}

function hpIkatSearch () {
  let input = document.getElementById('hpSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabel2').DataTable().search(input.value).draw()
  })
}

let hpPanjangHalaman = 10
function hpIkatPanjangHalaman () {
  let sel = document.getElementById('hpLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(hpPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    hpPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabel2').DataTable().page.len(hpPanjangHalaman).draw()
  })
}

function hpIkatPeriode () {
  let awal  = document.getElementById('hpTglAwal')
  let akhir = document.getElementById('hpTglAkhir')
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  let onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    loadAll()
  }

  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

function hpAturTinggiTabel () {
  let area = document.getElementById('content')
  let wrap = document.querySelector('#page1 .po-table-wrap')
  if (!area || !wrap) { return }

  wrap.style.maxHeight = 'none'

  let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
  let batasBawah = area.getBoundingClientRect().bottom - padBawah
  let kotak = wrap.getBoundingClientRect()
  let pageEl = document.getElementById('page1')
  let bawah = pageEl.getBoundingClientRect().bottom - kotak.bottom

  let sisa = batasBawah - kotak.top - bawah - 4
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
}

// ---------- Filter otorisasi ----------
// "Sudah" = semua level otorisasi yang diwajibkan (MaxOL) sudah terpenuhi, yaitu
// NeedOtorisasi = 0 dari query desktop.
let hpFilterOtorisasi = 'SEMUA'

function hpSudahOtorisasi (item) {
  return Number(item.NeedOtorisasi) === 0
}

function hpUpdateFilterBadge () {
  let jml = (hpFilterOtorisasi !== 'SEMUA') ? 1 : 0
  let badge = document.getElementById('hpFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function hpTerapkanFilter () {
  hpFilterOtorisasi = $('#hpModalOtorisasi').val() || 'SEMUA'
  hpUpdateFilterBadge()
  $('#modalFilterHP').modal('hide')
  renderTabelHP()
}

function hpResetFilter () {
  hpFilterOtorisasi = 'SEMUA'
  $('#hpModalOtorisasi').val('SEMUA')
  hpUpdateFilterBadge()
  $('#modalFilterHP').modal('hide')
  renderTabelHP()
}

$('#modalFilterHP').on('show.bs.modal', function () {
  $('#hpModalOtorisasi').val(hpFilterOtorisasi)
})

// ---------- Simpan / reset susunan kolom (dipanggil report-table.js) ----------
window.g_href = HP_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  hpCart.forEach((c) => {
    header.push(c[6])
    value.push(c[7])
    isnumber.push(c[8])
    isshown.push(Number(c[2]) === 1 ? 1 : 0)
    desimal.push(Number(c[5]) || 0)
  });

  $.ajax({
    url   : "{!! url('saveheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token   : $("#_token").val(),
      header   : JSON.stringify(header),
      isnumber : JSON.stringify(isnumber),
      tipe     : JSON.stringify(desimal),
      value    : JSON.stringify(value),
      isshown  : JSON.stringify(isshown),
      href     : HP_HREF
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  if (!reset) { return }

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : HP_HREF,
      reset  : 1
    },
    success : function (res) {
      hpCart = hpBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = hpCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

$(document).ready(function() {
  hpInitReportTableSekali()
  loadAll()
  hpIsiGudang()
});

// Dropdown Gudang (panel item) - DBGUDANG DBSMLNEW, dimuat sekali saat halaman dibuka.
function hpIsiGudang () {
  $.ajax({
    url: "{!! url('produksihasilproduksilistgudang') !!}",
    type: "get",
    success: function (res) {
      let html = '<option value="">Pilih Gudang</option>'
      ;(res || []).forEach(function (g) {
        html += `<option value="${hpEsc(g.KODEGDG)}">${hpEsc(g.KODEGDG)} - ${hpEsc(g.NAMA)}</option>`
      })
      $('#input_item_kodegdg').html(html)
    },
    error: function () { alertify.warning('Gagal memuat daftar gudang') }
  })
}

// Pilih gudang di dropdown; kalau kode lama tidak ada di DBGUDANG, tetap ditampilkan sebagai opsi.
function hpPilihGudang (kode) {
  let sel = $('#input_item_kodegdg')
  if (kode && !sel.find('option').filter(function () { return this.value === kode }).length) {
    sel.append(`<option value="${hpEsc(kode)}">${hpEsc(kode)}</option>`)
  }
  sel.val(kode || '')
}

function loadAll () {
  document.getElementById('tabel_data').innerHTML =
    '<tr><td colspan="20" class="text-center">' + loadingHtml('Memuat data...') + '</td></tr>'

  $.ajax({
    url: "{!! url('produksihasilproduksiloadall') !!}",
    type: "get",
    async: true,
    data: {
      tglawal: $('#hpTglAwal').val(),
      tglakhir: $('#hpTglAkhir').val()
    },
    success: function (res) {
      hpCart = hpBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = hpCart
      dataHP = res.listData1 || []
      renderTabelHP()
    },
    error: function (err) {
      console.error("Load failed:", err);
      let pesan = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : 'Terjadi kesalahan silahkan refresh browser'
      alertify.warning(pesan)
      document.getElementById('tabel_data').innerHTML =
        '<tr><td colspan="20" class="text-center text-muted">Gagal memuat data</td></tr>'
    }
  })
}

function renderTabelHP () {
  window.g_modeReport = 1
  window.gcart_header = hpCart

  if ($.fn.DataTable.isDataTable('#tabel2')) {
    $('#tabel2').DataTable().destroy()
  }

  let cols = hpKolomTampil()
  let kolomRender = cols.map(hpKolomRender)

  let thead = document.getElementById('tabel_header')
  thead.innerHTML = hpHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Action</th>')
    baris.insertAdjacentHTML('beforeend', `
      <th style="padding: 4px 12px;" scope="col">Otorisasi</th>
      <th style="padding: 4px 12px;" scope="col">User Oto</th>
      <th style="padding: 4px 12px;" scope="col">Tanggal Oto</th>
    `)
  }

  let dataTampil = dataHP || []
  if (hpFilterOtorisasi !== 'SEMUA') {
    let mauSudah = hpFilterOtorisasi === 'Sudah'
    dataTampil = dataTampil.filter(function (item) { return hpSudahOtorisasi(item) === mauSudah })
  }

  let rowTable = ''
  dataTampil.forEach((item) => {
    let sudahOto = hpSudahOtorisasi(item)
    let nobukti = hpEsc(item.NOBUKTI)

    let tombolAksi = `<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail('${nobukti}')"><i class="bi bi-info"></i></button>`
    if (sudahOto) {
      tombolAksi += `
        <button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${nobukti}')"><i class="bi bi-key"></i></button>
      `
    } else {
      tombolAksi += `
        <button class="btn btn-success btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi('${nobukti}')"><i class="bi bi-pen"></i></button>
        <button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('${nobukti}')"><i class="bi bi-key"></i></button>
      `
    }

    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">${tombolAksi}</div></td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${hpRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${hpRenderNilai(c, item)}</td>`
      }
    });
    rowTable += `
      ${sudahOto ?
          '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
        :
          '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
      }
      <td>${hpEsc(item.OtoUser1)}</td>
      <td>${item.TglOto1 ? formatDate(item.TglOto1).split('-').reverse().join('/') : ''}</td>
    </tr>`
  });

  document.getElementById("tabel_data").innerHTML = rowTable

  $('#tabel2').DataTable({
    lengthChange: false,
    pageLength: hpPanjangHalaman,
    order: [],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  });

  hpPindahBar()
  hpIkatSearch()
  hpIkatPanjangHalaman()
  hpIkatPeriode()
  let inputSearch = document.getElementById('hpSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabel2').DataTable().search(inputSearch.value).draw()
  }
  hpAturTinggiTabel()
}

// ---------- Otorisasi / Batal Otorisasi (pola Debet Note) ----------
// Hak dari Set Pemakai (DBFLMENUWEB menu 08007): IsOtorisasi1 -> otorisasi, IsBatal -> batal.
// Tanpa hak: kirim permintaan ke approver lewat lonceng/push (route otorisasirequest).
function buttonMintaOtorisasi (nobukti) {
  var dlgMinta = alertify.confirm('Minta Otorisasi',
    'Anda tidak berhak otorisasi. Kirim permintaan otorisasi hasil produksi ' + hpEsc(nobukti) + ' ke approver?',
    function () {
      $.ajax({
        url: "{!! url('otorisasirequest') !!}",
        type: "post",
        data: { _token: $("#_token").val(), menu: HP_HREF, nobukti },
        success: function (res) { alertify.success(res.message) },
        error: function (err) {
          console.log(err)
          alertify.warning((err.responseJSON && err.responseJSON.message) || 'Terjadi kesalahan silahkan refresh browser')
        }
      })
    },
    function () {})
  dlgMinta.elements.root.classList.add('ajs-app-buttons')
}

function buttonMintaBatalOtorisasi (nobukti) {
  var dlgBatalOtorisasi = alertify.prompt('Minta Batal Otorisasi',
    'Anda tidak berhak batal otorisasi. Masukkan alasan untuk meminta batal otorisasi hasil produksi ' + hpEsc(nobukti), '',
    function (evt, value) {
      var alasan = (value || '').trim()
      if (alasan === '') {
        alertify.warning('Alasan harus diisi.')
        return false
      }
      $.ajax({
        url: "{!! url('otorisasirequest') !!}",
        type: "post",
        data: { _token: $("#_token").val(), menu: HP_HREF, nobukti, tipe: 'B', alasan },
        success: function (res) { alertify.success(res.message) },
        error: function (err) {
          console.log(err)
          alertify.warning((err.responseJSON && err.responseJSON.message) || 'Terjadi kesalahan silahkan refresh browser')
        }
      })
    },
    function () {});
  dlgBatalOtorisasi.elements.root.classList.add('ajs-app-buttons', 'is-danger');
}

function buttonOtorisasi (nobukti) {
  if (!Number($("#akses_isotorisasi1").val())) {
    buttonMintaOtorisasi(nobukti)
    return
  }

  // Langsung diotorisasi tanpa dialog konfirmasi (permintaan user).
  $.ajax({
    url: "{!! url('produksihasilproduksiotorisasi') !!}",
    type: "post",
    data: { _token: $("#_token").val(), nobukti },
    success: function (res) {
      if (res && res.pesan) {
        alertify.warning(res.pesan)
        loadAll()
        return
      }
      alertify.success('Berhasil update otorisasi')
      loadAll()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan, silakan refresh browser.')
    }
  })
}

function buttonBatalOtorisasi (nobukti) {
  if (!Number($("#akses_isbatal").val())) {
    buttonMintaBatalOtorisasi(nobukti)
    return
  }

  alertify.prompt("Batal Otorisasi", "Masukkan keterangan batal otorisasi nomor   " + hpEsc(nobukti), "",
    function (evt, value) {
      if (!String(value || '').trim()) {
        alertify.warning('Keterangan harus diisi.')
        return false
      }
      $.ajax({
        url: "{!! url('produksihasilproduksibatalotorisasi') !!}",
        type: "post",
        data: { _token: $("#_token").val(), nobukti, pket: value },
        success: function (res) {
          if (res && res.pesan) {
            alertify.warning(res.pesan)
            loadAll()
            return
          }
          if (res > 0) {
            alertify.success('Berhasil batal otorisasi')
            loadAll()
          } else {
            alertify.warning('Gagal batal otorisasi')
          }
        },
        error: function (err) {
          console.log(err)
          if (err.status === 403) {
            alertify.warning('No access')
          } else if (err.status === 422) {
            alertify.warning('Keterangan harus diisi.')
          } else {
            alertify.warning('Terjadi kesalahan. Silakan refresh browser.')
          }
        }
      })
    },
    function () {
      alertify.error("Action cancelled")
    }
  )
}

// ---------- Browse No. SPK (header) ----------
// Isi: baris DBSPKJD yang masih punya sisa. Pilih baris -> No SPK, Kode Mesin, Nama Mesin
// terisi otomatis (mesin dari DBSPK.KodeMSN). Baris dipilih lewat indeks ke hpListSPK,
// bukan teks di onclick, supaya nama barang bertanda petik tidak membuat onclick error.
let hpListSPK = []

function hpIsiSPK (item) {
  $('#input_add_nospk').val(item ? item.nobukti : '')
  $('#input_add_urutspk').val(item ? (Number(item.urut) || 0) : 0)
  $('#input_add_kodemsn').val(item ? (item.KodeMSN || '') : '')
  $('#input_add_namamsn').val(item ? (item.NamaMesin || '') : '')
}

function buttonBrowseSPK () {
  $.ajax({
    url: "{!! url('produksihasilproduksilistspk') !!}",
    type: "get",
    success: function (res) {
      hpListSPK = res || []
      let rowTable = ''
      hpListSPK.forEach(function (item, i) {
        rowTable += `<tr class="pick-row" onclick="hpPilihSPK(${i})">
          <td>${hpEsc(item.nobukti)}</td>
          <td>${hpEsc(item.kodebrg)}</td>
          <td>${hpEsc(item.namaBrg)}</td>
          <td class="text-right">${hpFormatAngkaDes(item.sisa, 2)}</td>
          <td>${hpEsc(item.KodeMSN)}</td>
          <td>${hpEsc(item.NamaMesin)}</td>
        </tr>`
      })
      $('#tabel_data_pilih_spk').html(rowTable)
      pickerKasInit('tabel_pilih_spk', { order: [] })
      $('#formPilihSPK').modal('show')
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat daftar SPK')
    }
  })
}

function hpPilihSPK (i) {
  hpIsiSPK(hpListSPK[i])
  $('#formPilihSPK').modal('hide')
}

// No SPK diketik manual: cari SPK-nya; kalau ada (dan masih bersisa) mesin ikut terisi.
// SPK dengan lebih dari satu baris barang -> diambil baris pertama.
function hpCariSPKKetik () {
  let nospk = $.trim($('#input_add_nospk').val())
  if (!nospk) {
    hpIsiSPK(null)
    return
  }
  $.ajax({
    url: "{!! url('produksihasilproduksilistspk') !!}",
    type: "get",
    data: { nobukti: nospk },
    success: function (res) {
      if (res && res.length) {
        hpIsiSPK(res[0])
      } else {
        hpIsiSPK(null)
        alertify.warning('No SPK ' + hpEsc(nospk) + ' tidak ditemukan atau sudah tidak ada sisa')
      }
    },
    error: function () { alertify.warning('Gagal mencari No SPK') }
  })
}

// =====================================================================================
// Form Tambah / Koreksi (#page2) dan Detail (#page3) - pola pembelianpermintaanagen.
// Item disimpan per baris lewat Sp_HasilPrd (Choice I/U/D); header dbHasilPrd dibuat SP
// saat item pertama disimpan, jadi setelah ada item, header (WIP/BJ, Tanggal, SPK, Mesin,
// Shift) dikunci - Sp_HasilPrd tidak punya jalur update header.
// =====================================================================================
let hpItems = []          // baris hasil produksihasilproduksidetail (satu per item)
let hpHeaderAda = false   // true kalau dbHasilPrd untuk No Bukti di form sudah ada
let hpTipeFormItem = 'add'
// true kalau di form ada item yang disimpan / dihapus - hanya saat itu halaman depan dimuat
// ulang waktu Close (buka Detail / Koreksi tanpa mengubah apa pun tidak perlu memuat ulang).
let hpDataBerubah = false

function hpAngka (nilai) {
  let n = Number(String(nilai === null || nilai === undefined ? '' : nilai).replace(/,/g, ''))
  return isNaN(n) ? 0 : n
}

// Separator ribuan (koma) + 2 desimal saat kolom ditinggalkan.
function hpRapikanAngka (el) {
  el.value = hpFormatAngkaDes(hpAngka(el.value), 2)
}

// Separator ribuan (koma) langsung saat mengetik - pola sama dengan formatRibuanLive() di
// purchasing/pembelianpermintaandebetnote.blade.php. Posisi kursor dijaga supaya tidak
// meloncat ke akhir; maksimal 2 desimal. Nilai dikirim ke server tetap dibersihkan lewat
// hpAngka() (dan angka() di controller).
function hpFormatRibuanLive (el) {
  let cursorPos = el.selectionStart
  let sebelum = el.value.slice(0, cursorPos).replace(/[^0-9.\-]/g, '')

  let raw = el.value.replace(/[^0-9.\-]/g, '')
  let negatif = raw.charAt(0) === '-'
  raw = raw.replace(/-/g, '')
  if (raw === '' && !negatif) { el.value = ''; return }

  let titik = raw.indexOf('.')
  let bulat = titik >= 0 ? raw.slice(0, titik) : raw
  let desimal = titik >= 0 ? raw.slice(titik + 1).replace(/\./g, '').slice(0, 2) : null
  bulat = bulat.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  el.value = (negatif ? '-' : '') + bulat + (desimal !== null ? '.' + desimal : '')

  // Titik / minus ganda yang dibuang tidak ikut dihitung.
  let jumlahTitik = (sebelum.match(/\./g) || []).length
  let jumlahMinus = (sebelum.match(/-/g) || []).length
  let target = sebelum.length - Math.max(0, jumlahTitik - 1) - Math.max(0, jumlahMinus - (negatif ? 1 : 0))

  let pos = 0, hitung = 0
  while (pos < el.value.length && hitung < target) {
    if (el.value[pos] !== ',') hitung++
    pos++
  }
  el.setSelectionRange(pos, pos)
}

function hpTanggalInput (nilai) {
  return nilai ? String(nilai).substr(0, 10) : ''
}

function hpTampilkanPage (no) {
  $('#page1, #page2, #page3').hide()
  $('#page' + no).show()
  if (no === 1) { hpAturTinggiTabel() }
}

// Kembali ke halaman depan. Tabel depan hanya dimuat ulang kalau ada item yang disimpan /
// dihapus selama form terbuka; kalau tidak, tabel yang sudah ada langsung ditampilkan lagi.
function buttonCloseForm () {
  closeShowHideAdd()
  hpTampilkanPage(1)
  if (hpDataBerubah) {
    hpDataBerubah = false
    loadAll()
  }
}

function hpPeriodeTerkunci () {
  let terkunci = false
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    success: function (res) { terkunci = !!(res && res.length) },
    error: function () { alertify.warning('Terjadi kesalahan silahkan refresh browser') }
  })
  return terkunci
}

function hpKunciHeader (kunci) {
  // Kode Mesin & Nama Mesin selalu disabled (terisi otomatis dari SPK), jadi tidak ikut di sini.
  $('#input_add_tipe, #input_add_tanggal, #input_add_nospk, #input_add_shift')
    .prop('disabled', kunci)
  $('#buttonBrowseSPK').prop('disabled', kunci)
  $('#hpInfoHeaderKunci').toggle(kunci)
}

// No Bukti baru dihitung ulang dari WIP/BJ + bulan Tanggal, hanya selama header belum tersimpan.
function hpUbahNoBukti () {
  if (hpHeaderAda) { return }
  $.ajax({
    url: "{!! url('produksihasilproduksinextnobukti') !!}",
    type: "get",
    data: { tipe: $('#input_add_tipe').val(), tanggal: $('#input_add_tanggal').val() },
    success: function (res) {
      $('#input_add_nobukti').val(res.nobukti)
      $('#input_add_nourut').val(res.nourut)
    },
    error: function () { alertify.warning('Gagal membuat No Bukti') }
  })
}

function buttonAdd () {
  if (!Number($('#akses_istambah').val())) {
    alertify.warning('No access')
    return
  }
  if (hpPeriodeTerkunci()) {
    alertify.warning('Periode sudah dikunci')
    return
  }

  hpHeaderAda = false
  hpItems = []
  $('#input_add_tipe').val('WIP')
  $('#input_add_tanggal').val("{{ date('Y-m-d') }}")
  hpIsiSPK(null)
  $('#input_add_shift').val('')
  $('#input_add_nobukti, #input_add_nourut').val('')
  hpKunciHeader(false)
  hpRenderItemForm()
  closeShowHideAdd()
  hpUbahNoBukti()
  hpTampilkanPage(2)
}

function buttonKoreksi (nobukti) {
  if (!Number($('#akses_iskoreksi').val())) {
    alertify.warning('No access')
    return
  }
  if (hpPeriodeTerkunci()) {
    alertify.warning('Periode sudah dikunci')
    return
  }
  closeShowHideAdd()
  hpMuatForm(nobukti, function (ok) {
    if (ok) { hpTampilkanPage(2) }
  })
}

// Isi form #page2 dari server (dipakai Koreksi dan sesudah simpan/hapus item).
function hpMuatForm (nobukti, selesai) {
  $.ajax({
    url: "{!! url('produksihasilproduksidetail') !!}",
    type: "get",
    data: { nobukti: nobukti },
    success: function (res) {
      if (!res.length) {
        // Header ikut terhapus (item terakhir dihapus) - kembali ke form kosong No Bukti sama.
        hpHeaderAda = false
        hpItems = []
        hpKunciHeader(false)
        hpRenderItemForm()
        if (selesai) { selesai(false) }
        return
      }
      let h = res[0]
      hpHeaderAda = true
      hpItems = res.filter(function (r) { return r.URUT !== null && r.URUT !== undefined })

      $('#input_add_nobukti').val(h.NOBUKTI)
      $('#input_add_nourut').val(h.NOURUT || '')
      $('#input_add_tipe').val(/\/BJ$/.test(h.NOBUKTI) ? 'BJ' : 'WIP')
      $('#input_add_tanggal').val(hpTanggalInput(h.TANGGAL))
      $('#input_add_nospk').val(h.NoSPKHeader || '')
      $('#input_add_urutspk').val(Number(h.UrutSpk) || 0)
      $('#input_add_kodemsn').val(h.KodeMSn || '')
      $('#input_add_namamsn').val(h.NmMesin || '')
      $('#input_add_shift').val(Number(h.Shift) ? String(Number(h.Shift)) : '')
      hpKunciHeader(true)
      hpRenderItemForm()
      if (selesai) { selesai(true) }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat data, silahkan refresh browser')
      if (selesai) { selesai(false) }
    }
  })
}

function hpBarisItem (item, denganAksi, i) {
  let html = `<tr>
    <td>${hpEsc(item.NoSPK)}</td>
    <td>${hpEsc(item.KODEBRG)}</td>
    <td>${hpEsc(item.NAMABRG)}</td>
    <td class="text-right">${hpFormatAngkaDes(item.Qnt, 2)}</td>
    <td class="text-right">${hpFormatAngkaDes(item.QntReject, 2)}</td>
    <td>${hpEsc(item.Satx)}</td>
    <td>${hpEsc(item.KodeGdg)}${item.NamaGdg ? ' - ' + hpEsc(item.NamaGdg) : ''}</td>`
  if (denganAksi) {
    html += `<td>
      <button class="btn btn-success btn-sm" type="button" title="Koreksi Item" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" title="Hapus Item" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>
    </td>`
  } else {
    html += `<td class="text-center">${Number(item.IsAv) ? '<i class="bi bi-check2 text-success" style="-webkit-text-stroke-width: 2px;"></i>' : ''}</td>`
  }
  return html + '</tr>'
}

function hpRenderItemForm () {
  if (!hpItems.length) {
    $('#tabel_data_add').html('<tr><td class="text-center" colspan="8">Belum ada barang</td></tr>')
    return
  }
  $('#tabel_data_add').html(hpItems.map(function (item, i) { return hpBarisItem(item, true, i) }).join(''))
}

function closeShowHideAdd () {
  $('.showhide').hide()
}

// ---------- Barang di panel item ----------
// Batas Qty + Reject untuk barang dari SPK (tanpa avalan); null = tanpa batas (avalan).
let hpMaxQty = null

function hpIsAvalan () {
  return $('#input_item_isav').is(':checked')
}

function hpKosongkanBarang () {
  $('#input_item_kodebrg, #input_item_namabrg').val('')
  $('#input_item_urutspk').val(0)
  $('#input_item_qnt').val('0.00')
  hpMaxQty = null
}

// Reset seluruh isian item (dipakai saat centang Avalan diganti).
function hpResetSemuaItem () {
  hpKosongkanBarang()
  $('#input_item_reject').val('0.00')
  $('#input_item_nosat').val('1')
  $('#input_item_kodegdg').val('')
}

// No Satuan hanya bisa diubah kalau avalan dicentang; tanpa avalan selalu 1.
function hpAturNoSatuan () {
  if (hpIsAvalan()) {
    $('#input_item_nosat').prop('disabled', false)
  } else {
    $('#input_item_nosat').val('1').prop('disabled', true)
  }
}

// Ganti centang avalan = sumber barang berubah, jadi SEMUA isian item dikosongkan
// (barang, qty, reject, no satuan, gudang).
function hpUbahAvalan () {
  hpResetSemuaItem()
  hpAturNoSatuan()
}

// Mode Koreksi Item: hanya Qty & Reject yang boleh diubah - Avalan, Kode Barang (+ tombol cari),
// No. Satuan dan Gudang dikunci (server juga memakai nilai item yang tersimpan, lihat spItem()).
// Mode Tambah: semua dibuka lagi (No. Satuan tetap mengikuti centang Avalan).
function hpKunciItemKoreksi (kunci) {
  $('#input_item_isav, #input_item_kodebrg, #buttonBrowseBarang, #input_item_kodegdg').prop('disabled', kunci)
  if (kunci) {
    $('#input_item_nosat').prop('disabled', true)
  } else {
    hpAturNoSatuan()
  }
}

function buttonAddAddItem () {
  if (!$('#input_add_nobukti').val()) {
    alertify.warning('No Bukti belum terbentuk, coba lagi')
    return
  }
  hpTipeFormItem = 'add'
  $('#hpJudulPanelItem').text('Tambah Item')
  $('#input_item_urut').val(0)
  $('#input_item_isav').prop('checked', false)
  hpKosongkanBarang()
  $('#input_item_reject').val('0.00')
  $('#input_item_kodegdg').val('')
  hpKunciItemKoreksi(false)
  $('#hpPanelItem').show()
}

function buttonAddEditItem (i) {
  let item = hpItems[i]
  if (!item) { return }
  hpTipeFormItem = 'edit'
  $('#hpJudulPanelItem').text('Koreksi Item')
  $('#input_item_urut').val(item.URUT)
  $('#input_item_isav').prop('checked', Number(item.IsAv) === 1)
  $('#input_item_kodebrg').val(item.KODEBRG || '')
  $('#input_item_namabrg').val(item.NAMABRG || '')
  $('#input_item_urutspk').val(Number(item.UrutSpk) || 0)
  $('#input_item_qnt').val(hpFormatAngkaDes(item.Qnt, 2))
  $('#input_item_reject').val(hpFormatAngkaDes(item.QntReject, 2))
  $('#input_item_nosat').val(String(Number(item.NOSAT) || 1))
  hpPilihGudang(item.KodeGdg || '')
  hpKunciItemKoreksi(true)
  $('#hpPanelItem').show()

  // Batas koreksi tanpa avalan = sisa SPK sekarang + Qty/Reject item ini sendiri.
  hpMaxQty = null
  if (!hpIsAvalan()) {
    let milikSendiri = (hpAngka(item.Qnt) + hpAngka(item.QntReject)) * (hpAngka(item.Isi) || 1)
    hpMaxQty = milikSendiri
    $.ajax({
      url: "{!! url('produksihasilproduksilistbarangspk') !!}",
      type: "get",
      data: { nospk: $.trim($('#input_add_nospk').val()) },
      success: function (res) {
        let baris = (res || []).find(function (r) { return Number(r.urut) === Number(item.UrutSpk) })
        hpMaxQty = milikSendiri + (baris ? hpAngka(baris.sisa) : 0)
      }
    })
  }
}

// ---------- Browse Kode Barang (pola pembelianpermintaanagen) ----------
// Kolom Kode Barang bisa diketik: Enter -> cari. Satu hasil (atau kode persis sama) langsung
// dipilih, lebih dari satu -> modal. Tombol 🔍 membuka modal.
//  - avalan dicentang : modal #formPilihBarangAvalan, dicari ke server (18rb barang aktif)
//    lewat kotak Search modal (Enter). No Bukti = No SPK header, Qty = 0 (sesuai query).
//  - tanpa avalan     : modal #formPilihBarangSPK, barang SPK di header yang masih bersisa.
// Baris dipilih lewat indeks ke array, bukan teks di onclick (nama barang bisa berisi petik).
let hpListBarangAvalan = []
let hpListBarangSPK = []

// Animasi loading DI DALAM tabel: selama data dimuat, pesan "tabel kosong" DataTables
// diisi spinner (baris tepat di bawah header kolom).
function hpPesanLoading () {
  return loadingHtml('Memuat data...')
}

function hpBarisAvalan () {
  return hpListBarangAvalan.map(function (item, i) {
    return `<tr class="pick-row" onclick="hpPilihBarangAvalan(${i})">
      <td>${hpEsc(item.NObukti)}</td>
      <td>${hpEsc(item.KODEBRG)}</td>
      <td>${hpEsc(item.NAMABRG)}</td>
      <td class="text-right">${hpFormatAngkaDes(item.Sisa, 2)}</td>
      <td class="text-right">${hpFormatAngkaDes(item.ISi2, 2)}</td>
      <td class="text-right">${hpFormatAngkaDes(item.ISi3, 2)}</td>
    </tr>`
  }).join('')
}

function hpTabelAvalanInit (cari, pesanKosong) {
  $('#tabel_data_pilih_barang_avalan').html(hpBarisAvalan())
  pickerKasInit('tabel_pilih_barang_avalan', {
    order: [],
    search: { search: cari || '' },
    language: {
      lengthMenu: 'Tampilkan _MENU_',
      emptyTable: pesanKosong || 'Ketik kode / nama barang di kotak Search, lalu tekan Enter',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  })
  // Kotak Search modal = kotak cari server: Enter -> hpCariBarangAvalan().
  $('#tabel_pilih_barang_avalan_filter input')
    .off('keypress.hpBarang')
    .on('keypress.hpBarang', function (e) {
      if (e.which === 13) { hpCariBarangAvalan($(this).val().trim(), false) }
    })
}

// darifield = true: dipanggil dari Enter di kolom Kode Barang (boleh langsung pilih).
function hpCariBarangAvalan (cari, darifield) {
  if (!cari) {
    hpListBarangAvalan = []
    hpTabelAvalanInit('', '')
    return
  }
  if (!darifield) {
    hpListBarangAvalan = []
    hpTabelAvalanInit(cari, hpPesanLoading())
  }
  $.ajax({
    url: "{!! url('produksihasilproduksilistbarangavalan') !!}",
    type: "get",
    data: { search: cari, nospk: $.trim($('#input_add_nospk').val()) },
    success: function (res) {
      hpListBarangAvalan = res || []
      if (darifield) {
        let persis = hpListBarangAvalan.findIndex(function (b) { return String(b.KODEBRG).toUpperCase() === cari.toUpperCase() })
        if (hpListBarangAvalan.length === 1 || persis >= 0) {
          hpPilihBarangAvalan(persis >= 0 ? persis : 0)
          return
        }
        $('#formPilihBarangAvalan').modal('show')
      }
      hpTabelAvalanInit(cari, 'Tidak ada data')
    },
    error: function () {
      if (!darifield) { hpTabelAvalanInit(cari, 'Gagal memuat data') }
      alertify.warning('Gagal mencari barang')
    }
  })
}

function hpPilihBarangAvalan (i) {
  let item = hpListBarangAvalan[i]
  if (!item) { return }
  $('#input_item_kodebrg').val(item.KODEBRG || '')
  $('#input_item_namabrg').val(item.NAMABRG || '')
  $('#input_item_qnt').val(hpFormatAngkaDes(item.Sisa, 2))
  $('#input_item_urutspk').val(0)
  hpMaxQty = null
  $('#formPilihBarangAvalan').modal('hide')
}

function hpBarisSPK (daftar) {
  return daftar.map(function (item) {
    let i = hpListBarangSPK.indexOf(item)
    return `<tr class="pick-row" onclick="hpPilihBarangSPK(${i})">
      <td>${hpEsc(item.nobukti)}</td>
      <td>${hpEsc(item.kodebrg)}</td>
      <td>${hpEsc(item.namaBrg)}</td>
      <td class="text-right">${hpFormatAngkaDes(item.sisa, 2)}</td>
    </tr>`
  }).join('')
}

// cari = teks dari kolom Kode Barang (Enter) atau '' dari tombol 🔍.
function hpCariBarangSPK (cari) {
  let nospk = $.trim($('#input_add_nospk').val())
  if (!nospk) {
    alertify.warning('Pilih No SPK di header dulu, atau centang Avalan / Reject')
    return
  }
  $('#formPilihBarangSPKLabel').text('Barang SPK ' + nospk)
  if (!cari) {
    hpListBarangSPK = []
    $('#tabel_data_pilih_barang_spk').html('')
    pickerKasInit('tabel_pilih_barang_spk', {
      order: [],
      language: { lengthMenu: 'Tampilkan _MENU_', emptyTable: hpPesanLoading(), zeroRecords: hpPesanLoading() }
    })
    $('#formPilihBarangSPK').modal('show')
  }
  $.ajax({
    url: "{!! url('produksihasilproduksilistbarangspk') !!}",
    type: "get",
    data: { nospk: nospk },
    success: function (res) {
      hpListBarangSPK = res || []
      let daftar = hpListBarangSPK
      if (cari) {
        let c = cari.toUpperCase()
        daftar = hpListBarangSPK.filter(function (b) {
          return String(b.kodebrg).toUpperCase().indexOf(c) >= 0 || String(b.namaBrg || '').toUpperCase().indexOf(c) >= 0
        })
        if (daftar.length === 1) {
          hpPilihBarangSPK(hpListBarangSPK.indexOf(daftar[0]))
          return
        }
        $('#formPilihBarangSPK').modal('show')
      }
      $('#tabel_data_pilih_barang_spk').html(hpBarisSPK(daftar))
      pickerKasInit('tabel_pilih_barang_spk', {
        order: [],
        language: {
          lengthMenu: 'Tampilkan _MENU_',
          emptyTable: cari ? 'Tidak ada barang SPK yang cocok' : 'Tidak ada barang SPK yang masih bersisa',
          zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
        }
      })
    },
    error: function () {
      $('#tabel_data_pilih_barang_spk').html('')
      pickerKasInit('tabel_pilih_barang_spk', {
        order: [],
        language: { lengthMenu: 'Tampilkan _MENU_', emptyTable: 'Gagal memuat data', zeroRecords: 'Gagal memuat data' }
      })
      alertify.warning('Gagal memuat barang SPK')
    }
  })
}

function hpPilihBarangSPK (i) {
  let item = hpListBarangSPK[i]
  if (!item) { return }
  $('#input_item_kodebrg').val(item.kodebrg || '')
  $('#input_item_namabrg').val(item.namaBrg || '')
  $('#input_item_qnt').val(hpFormatAngkaDes(item.sisa, 2))
  $('#input_item_urutspk').val(Number(item.urut) || 0)
  hpMaxQty = hpAngka(item.sisa)
  $('#formPilihBarangSPK').modal('hide')
}

function buttonBrowseBarang () {
  if (hpIsAvalan()) {
    hpListBarangAvalan = []
    hpTabelAvalanInit('', '')
    $('#formPilihBarangAvalan').modal('show')
    setTimeout(function () { $('#tabel_pilih_barang_avalan_filter input').trigger('focus') }, 400)
    return
  }
  hpCariBarangSPK('')
}

// Kolom Kode Barang: Enter -> cari. Kalau isinya diubah setelah memilih, barang lama
// dilepas (Nama Barang dikosongkan) supaya yang tersimpan selalu hasil pilihan.
$(document).on('keypress', '#input_item_kodebrg', function (e) {
  if (e.which !== 13) { return }
  e.preventDefault()
  let cari = $(this).val().trim()
  if (!cari) { buttonBrowseBarang(); return }
  if (hpIsAvalan()) {
    hpCariBarangAvalan(cari, true)
  } else {
    hpCariBarangSPK(cari)
  }
})
$(document).on('input', '#input_item_kodebrg', function () {
  $('#input_item_namabrg').val('')
  $('#input_item_urutspk').val(0)
  hpMaxQty = null
})

function submitItem () {
  let kodebrg = $.trim($('#input_item_kodebrg').val())
  let kodegdg = $.trim($('#input_item_kodegdg').val())
  let qnt = hpAngka($('#input_item_qnt').val())
  let reject = hpAngka($('#input_item_reject').val())

  if (!kodebrg) { alertify.warning('Kode Barang harus dipilih'); return }
  if (!$.trim($('#input_item_namabrg').val())) {
    alertify.warning('Tekan Enter di Kode Barang atau pilih lewat tombol cari, supaya barangnya terpilih')
    return
  }
  if (!kodegdg) { alertify.warning('Gudang harus diisi'); return }
  if (qnt <= 0 && reject <= 0) { alertify.warning('Qty atau Reject harus diisi'); return }
  // Tanpa avalan: Qty + Reject tidak boleh melebihi sisa SPK (dicek ulang di server).
  if (!hpIsAvalan() && hpMaxQty !== null && qnt + reject > hpMaxQty + 0.0001) {
    alertify.warning('Qty + Reject (' + hpFormatAngkaDes(qnt + reject, 2) + ') melebihi sisa SPK (' + hpFormatAngkaDes(hpMaxQty, 2) + ')')
    return
  }

  let nobukti = $('#input_add_nobukti').val()
  $('#hpSimpanItem').prop('disabled', true)

  $.ajax({
    url: "{!! url('produksihasilproduksispitem') !!}",
    type: "post",
    data: {
      _token  : $('#_token').val(),
      choice  : hpTipeFormItem === 'edit' ? 'U' : 'I',
      nobukti : nobukti,
      nourut  : $('#input_add_nourut').val(),
      tanggal : $('#input_add_tanggal').val(),
      nospk   : $.trim($('#input_add_nospk').val()),
      // Urut baris SPK dari barang yang dipilih (bukan dari header - satu SPK bisa
      // punya beberapa barang). Avalan = 0.
      urutspk : $('#input_item_urutspk').val() || 0,
      kodemsn : $.trim($('#input_add_kodemsn').val()),
      shift   : $('#input_add_shift').val() || 0,
      urut    : $('#input_item_urut').val(),
      kodebrg : kodebrg,
      kodegdg : kodegdg,
      qnt     : qnt,
      reject  : reject,
      nosat   : $('#input_item_nosat').val(),
      isav    : $('#input_item_isav').is(':checked') ? 1 : 0
    },
    success: function (res) {
      alertify.success(res.message || 'Item tersimpan')
      hpDataBerubah = true
      closeShowHideAdd()
      hpMuatForm(nobukti)
    },
    error: function (err) {
      let pesan = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : 'Gagal menyimpan item'
      alertify.warning(pesan)
    },
    complete: function () {
      $('#hpSimpanItem').prop('disabled', false)
    }
  })
}

function buttonAddDeleteItem (i) {
  let item = hpItems[i]
  if (!item) { return }
  if (!Number($('#akses_ishapus').val())) {
    alertify.warning('No access')
    return
  }
  let nobukti = $('#input_add_nobukti').val()
  alertify.confirm('Hapus Item', 'Yakin hapus item ' + hpEsc(item.KODEBRG) + '?', function () {
    $.ajax({
      url: "{!! url('produksihasilproduksihapusitem') !!}",
      type: "post",
      data: { _token: $('#_token').val(), nobukti: nobukti, urut: item.URUT },
      success: function (res) {
        alertify.success(res.message || 'Item dihapus')
        hpDataBerubah = true
        closeShowHideAdd()
        hpMuatForm(nobukti)
      },
      error: function (err) {
        let pesan = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : 'Gagal menghapus item'
        alertify.warning(pesan)
      }
    })
  }, function () {})
}

function buttonDetail (nobukti) {
  $.ajax({
    url: "{!! url('produksihasilproduksidetail') !!}",
    type: "get",
    data: { nobukti: nobukti },
    success: function (res) {
      if (!res.length) {
        alertify.warning('Data tidak ditemukan, silakan refresh')
        loadAll()
        return
      }
      let h = res[0]
      let items = res.filter(function (r) { return r.URUT !== null && r.URUT !== undefined })

      $('#input_detail_nobukti').val(h.NOBUKTI)
      $('#input_detail_tipe').val(/\/BJ$/.test(h.NOBUKTI) ? 'BJ' : 'WIP')
      $('#input_detail_tanggal').val(hpTanggalInput(h.TANGGAL))
      $('#input_detail_nospk').val(h.NoSPKHeader || '')
      $('#input_detail_mesin').val(h.KodeMSn || '')
      $('#input_detail_namamsn').val(h.NmMesin || '')
      $('#input_detail_shift').val(Number(h.Shift) ? Number(h.Shift) : '-')

      $('#tabel_data_detail').html(items.length
        ? items.map(function (item) { return hpBarisItem(item, false) }).join('')
        : '<tr><td class="text-center" colspan="8">Belum ada barang</td></tr>')

      hpTampilkanPage(3)
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat detail, silahkan refresh browser')
    }
  })
}

function formatDate(date , pemisah = '-') {
  var d = new Date(date),
      month = '' + (d.getMonth() + 1),
      day = '' + d.getDate(),
      year = d.getFullYear();

  if (month.length < 2)
      month = '0' + month;
  if (day.length < 2)
      day = '0' + day;

  return [year, month, day].join(pemisah);
}
</script>
@endsection
