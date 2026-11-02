@extends('newmasterTest')
@section('buttons')
@endsection
@section('page-title', 'Dokumen BC 4.1')

{{-- Dokumen BC 4.1 (barang keluar dari kawasan berikat ke daerah pabean / barang asal lokal, berdiri sendiri: tanpa
     Outstanding dan tanpa Jaminan) - spesifikasi di docs/documentbc41_plan.md. Kembaran documentbc25.blade.php:
     tanpa Cara Pembayaran, tab Pungutan = Lokasi Bayar (BANK) / Wajib Bayar / Bukti Bayar. Bootstrap 4 only
     (data-toggle / data-dismiss).
     Semua koleksi anak (barang, bahan baku, kemasan, peti kemas, dokumen) diedit di
     memori (state) dan baru dikirim ke server saat Simpan dokumen. --}}
@section('css')
    <link rel="stylesheet"
        href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
    <link rel="stylesheet"
        href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
    <link rel="stylesheet"
        href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">
    <link rel="stylesheet"
        href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">
    <style>
        /* Label BC lebih panjang dari form master biasa (mis. "Tujuan Pengiriman", "Nomor Kontainer"):
           kolom label dilebarkan, isian dipersempit, dan label boleh turun baris - tidak terpotong. */
        @media (min-width: 576px) {
            body #formBsGrid .bs-form { grid-template-columns: 170px minmax(0, 1fr) 170px minmax(0, 1fr); }
            body #formBsGrid .bs-form.bs-form-1 { grid-template-columns: 170px minmax(0, 1fr); }
        }
        body #formBsGrid .bs-form label { white-space: normal; line-height: 1.3; }

        .custom-tabs {
            display: inline-flex;
            flex-wrap: wrap;
            justify-content: flex-start;
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

        .custom-tabs .nav-link:hover {
            background: transparent;
            color: #007bff;
        }

        .custom-tabs .nav-link.active {
            background: #007bff;
            border-color: #007bff;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
        }

        #pageForm .tab-content {
            padding: 14px 4px 4px;
        }

        .bc-judul {
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #495057;
            margin: 14px 0 8px;
        }

        .bc-judul:first-child {
            margin-top: 0;
        }

        .bc-toolbar-koleksi {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 6px;
        }

        .bc-info {
            font-size: .75rem;
            color: #6c757d;
        }

        /* Baris barang terpilih (sumber panel bahan baku). */
        #tblBarang tr.bc-sel td {
            background: #fff3cd !important;
        }

        #tblBarang tbody tr {
            cursor: pointer;
        }

        .bc-kosong td {
            text-align: center;
            color: #6c757d;
        }

        #btnLoginBc.is-login {
            background: #e7f7ed;
            border-color: #b7e4c7;
            color: #16a34a;
        }

        #previewIsi {
            max-height: 65vh;
            overflow: auto;
            font-size: 11.5px;
            background: #f8f9fb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            margin: 0;
            white-space: pre;
        }

        /* Modal pemilih HS dibuka di atas modal barang/bahan baku. */
        #mdlHs {
            z-index: 1070;
        }

        .modal-backdrop.bc-backdrop-atas {
            z-index: 1065;
        }

        #tblHs tbody tr {
            cursor: pointer;
        }

        /* Lapisan "Memuat data..." di atas area tabel - tampilannya sama dengan purchasing/purchaseOrder
           (.po-loading-chip + .po-loading-spin). 45% pertama animasi masih transparan, jadi respons yang
           cepat selesai sebelum lapisan sempat terlihat (tidak berkedip). */
        #areaTabel {
            position: relative;
        }

        #areaTabel.bc-pane-memuat {
            min-height: 140px;
        }

        .bc-memuat {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, .62);
            z-index: 40;
            animation: bcMunculMemuat .34s ease-out both;
        }

        @keyframes bcMunculMemuat {
            0%, 45% { opacity: 0; }
            100% { opacity: 1; }
        }

        .po-loading-chip {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: inline-flex;
            align-items: center;
            gap: 9px;
            white-space: nowrap;
            padding: 9px 18px;
            border-radius: 999px;
            background: rgba(31, 36, 48, .92);
            color: #fff;
            font-size: 12.5px;
            font-weight: 600;
            box-shadow: 0 8px 22px rgba(0, 0, 0, .18);
        }

        .po-loading-spin {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, .35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: bcPutarMemuat .6s linear infinite;
        }

        @keyframes bcPutarMemuat {
            to { transform: rotate(360deg); }
        }

        /* Pesan galat di bawah isian (Berat Kotor harus > 0). */
        .bc-err {
            display: none;
            margin-top: 3px;
            font-size: .75rem;
            color: #dc3545;
        }
    </style>
@endsection

@section('content')
    <div id="contentContainer" class="container-fluid">
        <input type="hidden" id="akses_istambah" value="{!! (int) ($akses->ISTAMBAH ?? 0) !!}" />
        <input type="hidden" id="akses_iskoreksi" value="{!! (int) ($akses->ISKOREKSI ?? 0) !!}" />
        <input type="hidden" id="akses_ishapus" value="{!! (int) ($akses->ISHAPUS ?? 0) !!}" />
        <input type="hidden" id="akses_isexport" value="{!! (int) ($akses->ISEXPORT ?? 0) !!}" />
        <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

        <div id="pageHome" class="container-fluid po-list-page">
            <div class="card">
                <div class="card-body" style="padding:0;">

                    <div class="po-toolbar">
                        <input class="po-search-inp" type="search" id="searchBox2" placeholder="Cari data">

                        <div class="po-len-wrap">
                            <label for="tampilLen">Tampilkan</label>
                            <select class="po-len-inp" id="tampilLen">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="-1">Semua</option>
                            </select>
                        </div>

                        {{-- Token CEISA per user; teks tombol berubah jadi hitung mundur masa berlaku token. --}}
                        <button class="po-btn-filter" type="button" id="btnLoginBc" onclick="bukaLoginBc()">
                            <i class="bi bi-box-arrow-in-right"></i> <span id="btnLoginBcTeks">Login BC</span>
                        </button>

                        @if ((int) ($akses->ISTAMBAH ?? 0) === 1)
                            <div class="po-toolbar-act">
                                <button class="btn btn-chip-biru" type="button" onclick="buttonAdd()">Tambah</button>
                            </div>
                        @endif
                    </div>

                    {{-- #areaTabel = acuan lapisan "Memuat data..." (tampilMemuat). --}}
                    <div id="areaTabel">
                        <div id="rtBar"></div>

                        <table id="tabel" class="data-table po-aksi-hover">
                            <thead>
                                <tr>
                                    <th style="padding: 4px 12px;" scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tabel_data" class="text-left"></tbody>
                        </table>

                        <div class="po-rt-hint">
                            <i class="bi bi-info-circle"></i>
                            Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
                            untuk menyembunyikan kolom.
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- start pageForm (Tambah / Edit / Detail, full page) -->
    <div id="pageForm" class="container-fluid po-form-page" style="display:none">
        <div id="formBsGrid">
            <div class="row" style="padding: 1rem 0">
                <div class="col-6 text-left">
                    <h5 id="formJudul" style="margin:0"></h5>
                </div>
                <div class="col-6 text-right" id="contentContainer">
                    <button type="button" class="btn btn-danger btn-pill-primary btn-action-danger"
                        onclick="buttonCloseForm()">Close</button>
                    <button type="button" class="btn btn-primary btn-pill-primary btn-action-primary bc-edit"
                        onclick="submitDokumen()">Simpan</button>
                </div>
            </div>

            <div class="nav nav-tabs border-0 custom-tabs" id="bcTabs" role="tablist">
                <a class="nav-item nav-link active" data-toggle="tab" href="#tabHeader" role="tab">Header</a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tabEntitas" role="tab">Entitas</a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tabDokumen" role="tab">Dokumen</a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tabPengangkut" role="tab">Pengangkut</a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tabKemasan" role="tab">Kemasan</a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tabTransaksi" role="tab">Transaksi</a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tabBarang" role="tab">Barang</a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tabPungutan" role="tab">Pungutan</a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tabPernyataan" role="tab">Pernyataan</a>
            </div>

            <div class="tab-content">

                {{-- HEADER --}}
                <div class="tab-pane fade show active" id="tabHeader" role="tabpanel">
                    <div class="bs-form">
                        <label for="h_nomorAju">Nomor AJU</label>
                        <input type="text" class="form-control" id="h_nomorAju" data-h="nomorAju" maxlength="26">

                        <label for="h_kodeKantor">Kantor Pabean</label>
                        <input type="text" class="form-control" id="h_kodeKantor" data-h="kodeKantor" maxlength="20">

                        <label for="h_kodeJenisTpb">Jenis TPB</label>
                        <select class="form-control" id="h_kodeJenisTpb" data-h="kodeJenisTpb">
                            @foreach ($kode['jenisTpb'] as $k => $v)
                                <option value="{{ $k }}">{{ $k }} - {{ $v }}</option>
                            @endforeach
                        </select>

                        <label for="h_kodeTujuanPengiriman" class="bs-wrap">Tujuan Pengiriman</label>
                        <select class="form-control" id="h_kodeTujuanPengiriman" data-h="kodeTujuanPengiriman">
                            @foreach ($kode['tujuanPengiriman'] as $k => $v)
                                <option value="{{ $k }}">{{ $k }} - {{ $v }}</option>
                            @endforeach
                        </select>

                        <label for="h_noRegBc">No. Reg BC</label>
                        <input type="text" class="form-control" id="h_noRegBc" data-h="noRegBc" maxlength="50">

                        <label for="h_tglBc">Tanggal BC</label>
                        <input type="date" class="form-control" id="h_tglBc" data-h="tglBc">

                        <label for="h_arrDate">Tanggal Pengeluaran</label>
                        <input type="date" class="form-control" id="h_arrDate" data-h="arrDate">
                    </div>
                    <div class="bc-info mt-2">No. Reg BC, Tanggal BC dan Tanggal Pengeluaran hanya untuk catatan internal (tidak dikirim ke CEISA).</div>
                </div>

                {{-- ENTITAS - yang dikirim ke CEISA = isian di bawah + salinan data master yang diambil server saat Simpan
                     (seri 3 Pengusaha TPB, 7 Pemilik Barang, 8 Penerima Barang). --}}
                <div class="tab-pane fade" id="tabEntitas" role="tabpanel">
                    @foreach ([1 => 'Pengusaha TPB', 3 => 'Penerima Barang', 2 => 'Pemilik Barang'] as $n => $judul)
                        <div class="bc-judul">{{ $judul }}</div>
                        <div class="bs-form">
                            <label for="h_selectEntitas{{ $n }}">Entitas</label>
                            <select class="form-control bc-entitas" id="h_selectEntitas{{ $n }}" data-h="selectEntitas{{ $n }}" data-n="{{ $n }}">
                                <option value="">- Pilih Entitas -</option>
                                @foreach ($entitas as $e)
                                    <option value="{{ $e->id }}">{{ $e->namaEntitas }}</option>
                                @endforeach
                            </select>

                            <label for="h_nomorIdentitasEntitas{{ $n }}">NPWP</label>
                            <input type="text" class="form-control" id="h_nomorIdentitasEntitas{{ $n }}" data-h="nomorIdentitasEntitas{{ $n }}" maxlength="50">

                            <label for="h_alamatEntitas{{ $n }}">Alamat</label>
                            <div class="bs-full">
                                <input type="hidden" id="h_namaEntitas{{ $n }}" data-h="namaEntitas{{ $n }}">
                                <textarea class="form-control" id="h_alamatEntitas{{ $n }}" data-h="alamatEntitas{{ $n }}" rows="2" maxlength="1000"></textarea>
                            </div>

                            @if ($n === 1)
                                <label for="h_nomorIjinEntitas1" class="bs-wrap">Nomor Izin TPB</label>
                                <input type="text" class="form-control" id="h_nomorIjinEntitas1" data-h="nomorIjinEntitas1" maxlength="50">

                                <label for="h_tanggalIjinEntitas1" class="bs-wrap">Tanggal Izin TPB</label>
                                <input type="date" class="form-control" id="h_tanggalIjinEntitas1" data-h="tanggalIjinEntitas1">
                            @endif
                        </div>
                    @endforeach
                    <div class="bc-info mt-2">Ketiga entitas wajib dipilih saat dikirim ke CEISA. Pengusaha TPB juga butuh NIB, Nomor Izin dan Tanggal Izin; izin Pemilik Barang disalin dari master Entitas saat Simpan, jadi lengkapi izinnya di master.</div>
                </div>

                {{-- DOKUMEN --}}
                <div class="tab-pane fade" id="tabDokumen" role="tabpanel">
                    <div class="bc-toolbar-koleksi">
                        <span class="bc-info" id="infoDokumen"></span>
                        <button type="button" class="btn btn-chip-biru btn-sm bc-edit" onclick="bukaModal('dokumen', null)">Tambah Dokumen</button>
                    </div>
                    <div class="tb-report">
                        <div class="table-outer">
                            <div class="table-wrap" style="max-height:50vh;">
                                <table class="tb">
                                    <thead>
                                        <tr><th>No</th><th>Jenis Dokumen</th><th>Nomor Dokumen</th><th>Tanggal</th><th>Action</th></tr>
                                    </thead>
                                    <tbody id="tblDokumen"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="bc-info mt-2">Dokumen tidak wajib ada, tetapi baris yang ada harus lengkap (jenis, nomor, tanggal) saat dikirim.</div>
                </div>

                {{-- PENGANGKUT --}}
                <div class="tab-pane fade" id="tabPengangkut" role="tabpanel">
                    <div class="bs-form">
                        <label for="h_namaPengangkut" class="bs-wrap">Nama Sarana Pengangkut</label>
                        <input type="text" class="form-control" id="h_namaPengangkut" data-h="namaPengangkut" maxlength="100" placeholder="mis. TRUCK">

                        <label for="h_nomorPengangkut" class="bs-wrap">Nomor Sarana Pengangkut</label>
                        <input type="text" class="form-control" id="h_nomorPengangkut" data-h="nomorPengangkut" maxlength="50" placeholder="mis. No. Polisi">
                    </div>
                    <div class="bc-info mt-2">Nama dan nomor sarana pengangkut wajib diisi saat dikirim ke CEISA.</div>
                </div>

                {{-- KEMASAN + PETI KEMAS --}}
                <div class="tab-pane fade" id="tabKemasan" role="tabpanel">
                    <div class="bc-judul">Kemasan</div>
                    <div class="bc-toolbar-koleksi">
                        <span class="bc-info" id="infoKemasan"></span>
                        <button type="button" class="btn btn-chip-biru btn-sm bc-edit" onclick="bukaModal('kemasan', null)">Tambah Kemasan</button>
                    </div>
                    <div class="tb-report">
                        <div class="table-outer">
                            <div class="table-wrap" style="max-height:30vh;">
                                <table class="tb">
                                    <thead>
                                        <tr><th>No</th><th>Jumlah</th><th>Jenis Kemasan</th><th>Merk</th><th>Action</th></tr>
                                    </thead>
                                    <tbody id="tblKemasan"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="bc-judul">Peti Kemas</div>
                    <div class="bc-toolbar-koleksi">
                        <span class="bc-info" id="infoKontainer"></span>
                        <button type="button" class="btn btn-chip-biru btn-sm bc-edit" onclick="bukaModal('kontainer', null)">Tambah Peti Kemas</button>
                    </div>
                    <div class="tb-report">
                        <div class="table-outer">
                            <div class="table-wrap" style="max-height:30vh;">
                                <table class="tb">
                                    <thead>
                                        <tr><th>No</th><th>Nomor Kontainer</th><th>Ukuran</th><th>Tipe</th><th>Jenis Muatan</th><th>Action</th></tr>
                                    </thead>
                                    <tbody id="tblKontainer"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="bc-info mt-2">Kemasan wajib ada saat dikirim ke CEISA (Jenis Kemasan terisi dan Jumlah lebih dari 0). Peti kemas ikut dikirim bila diisi.</div>
                </div>

                {{-- TRANSAKSI - harga, pajak, berat. Nilai turunan (abu-abu) dihitung otomatis setiap isian berubah. --}}
                <div class="tab-pane fade" id="tabTransaksi" role="tabpanel">
                    <div class="bc-judul">Harga</div>
                    <div class="bs-form">
                        <label for="h_kodeValuta">Valuta</label>
                        <select class="form-control" id="h_kodeValuta" data-h="kodeValuta"></select>

                        <label for="h_ndpbm">NDPBM</label>
                        <div class="input-group">
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_ndpbm" data-h="ndpbm" oninput="hitungTransaksi()">
                            <button type="button" class="btn btn-chip-biru bc-edit" onclick="kursTerbaru()" title="Isi dari kurs master valas">Kurs terbaru</button>
                        </div>

                        <label for="h_cif">CIF</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_cif" data-h="cif" oninput="hitungTransaksi()">

                        <label for="h_cifRupiah" class="bs-wrap">Nilai Pabean (CIF Rupiah)</label>
                        <input type="text" class="form-control text-right bc-tetap" id="h_cifRupiah" data-h="cifRupiah" disabled>

                        <label for="h_hargaPenyerahan" class="bs-wrap">Harga Penyerahan</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_hargaPenyerahan" data-h="hargaPenyerahan" oninput="hitungTransaksi()">
                    </div>

                    <div class="bc-judul">Data untuk Keperluan Pajak</div>
                    <div class="bs-form">
                        <label for="h_uangMuka">Uang Muka</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_uangMuka" data-h="uangMuka" oninput="hitungTransaksi()">

                        <label for="h_diskon">Diskon</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_diskon" data-h="diskon" oninput="hitungTransaksi()">

                        <label for="h_dasarPengenaanPajak" class="bs-wrap">Dasar Pengenaan Pajak (DPP)</label>
                        <input type="text" class="form-control text-right bc-tetap" id="h_dasarPengenaanPajak" data-h="dasarPengenaanPajak" disabled>

                        <span></span><span></span>

                        <label for="h_tarifPpnPajak">Tarif PPN (%)</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_tarifPpnPajak" data-h="tarifPpnPajak" oninput="hitungTransaksi()">

                        <label for="h_ppnPajak">PPN</label>
                        <input type="text" class="form-control text-right bc-tetap" id="h_ppnPajak" data-h="ppnPajak" disabled>

                        <label for="h_tarifPpnbmPajak">Tarif PPnBM (%)</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_tarifPpnbmPajak" data-h="tarifPpnbmPajak" oninput="hitungTransaksi()">

                        <label for="h_ppnbmPajak">PPnBM</label>
                        <input type="text" class="form-control text-right bc-tetap" id="h_ppnbmPajak" data-h="ppnbmPajak" disabled>
                    </div>

                    <div class="bc-judul">Berat</div>
                    <div class="bs-form">
                        <label for="h_bruto">Bruto (Kg)</label>
                        <div>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_bruto" data-h="bruto">
                            <div class="bc-err" id="errBruto">Berat Kotor harus lebih besar dari 0</div>
                        </div>

                        <label for="h_netto">Netto (Kg)</label>
                        <div class="input-group">
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_netto" data-h="netto">
                            <button type="button" class="btn btn-chip-biru bc-edit" onclick="syncNettoBarang()" title="Netto = jumlah netto semua barang">Sync Netto Barang</button>
                        </div>

                        <label for="h_volume">Total Volume (m3)</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan bc-tetap" id="h_volume" data-h="volume" disabled>
                    </div>
                    <div class="bc-info mt-2">Nilai Pabean = CIF x NDPBM. DPP = Harga Penyerahan - Uang Muka - Diskon. PPN dan PPnBM = DPP x tarif. Total Volume = jumlah volume semua barang.</div>
                </div>

                {{-- BARANG + BAHAN BAKU (satu panel untuk barang terpilih, tanpa asal Impor/Lokal) --}}
                <div class="tab-pane fade" id="tabBarang" role="tabpanel">
                    <div class="bc-toolbar-koleksi">
                        <span class="bc-info" id="infoBarang"></span>
                        <button type="button" class="btn btn-chip-biru btn-sm bc-edit" onclick="bukaModal('barang', null)">Tambah Barang</button>
                    </div>
                    <div class="tb-report">
                        <div class="table-outer">
                            <div class="table-wrap" style="max-height:35vh;">
                                <table class="tb" id="tblBarang">
                                    <thead>
                                        <tr><th>No</th><th>Kode Barang</th><th>Uraian</th><th>Pos Tarif</th><th>Jumlah</th><th>Satuan</th><th>Netto</th><th>Volume</th><th>Nilai CIF</th><th>Action</th></tr>
                                    </thead>
                                    <tbody id="tblBarangData"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="bc-judul">Bahan Baku <span class="bc-info" id="infoBahanBaku"></span></div>
                    <div class="bc-toolbar-koleksi">
                        <span></span>
                        <button type="button" class="btn btn-chip-biru btn-sm bc-edit" onclick="tambahBahanBaku()">Tambah Bahan Baku</button>
                    </div>
                    <div class="tb-report">
                        <div class="table-outer">
                            <div class="table-wrap" style="max-height:30vh;">
                                <table class="tb">
                                    <thead>
                                        <tr><th>Seri</th><th>Kode Barang</th><th>Uraian</th><th>Pos Tarif</th><th>Jumlah</th><th>Satuan</th><th>Dok Asal</th><th>No Daftar</th><th>Tgl Daftar</th><th>CIF Rupiah</th><th>Action</th></tr>
                                    </thead>
                                    <tbody id="tblBahanBaku"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PUNGUTAN BC 4.1 - Lokasi Bayar selalu BANK; Wajib Bayar = seri entitas pembayar (3 / 7 / 8), pilihannya
                     dibangun dari entitas di tab Entitas. PPN / PPnBM dikirim dari tab Transaksi. --}}
                <div class="tab-pane fade" id="tabPungutan" role="tabpanel">
                    <div class="bc-judul">Lokasi Bayar</div>
                    <div class="bs-form">
                        <label>Cara Bayar</label>
                        <input type="text" class="form-control bc-tetap" value="BANK" disabled>
                    </div>

                    <div class="bc-judul">Wajib Bayar</div>
                    <div class="bs-form">
                        <label for="h_kodePembayar">Wajib Bayar</label>
                        <div>
                            <select class="form-control" id="h_kodePembayar" data-h="kodePembayar">
                                <option value="">- Pilih -</option>
                            </select>
                            <div class="bc-err" id="errPembayar">Wajib Bayar harus dipilih</div>
                        </div>
                    </div>

                    <div class="bc-info mt-2">
                        Pilihan Wajib Bayar diambil dari entitas di tab Entitas yang punya NPWP (NPWP - nama), dan diperbarui tiap entitas diganti.
                        PPN dan PPnBM dikirim ke CEISA sebagai nilai di tab Transaksi (skema resmi tidak punya daftar pungutan dan bukti bayar).
                    </div>
                </div>

                {{-- PERNYATAAN --}}
                <div class="tab-pane fade" id="tabPernyataan" role="tabpanel">
                    <div class="bs-form">
                        <label for="h_kotaTtd">Kota</label>
                        <input type="text" class="form-control" id="h_kotaTtd" data-h="kotaTtd" maxlength="100">

                        <label for="h_tanggalTtd">Tanggal</label>
                        <input type="date" class="form-control" id="h_tanggalTtd" data-h="tanggalTtd">

                        <label for="h_namaTtd">Nama</label>
                        <input type="text" class="form-control" id="h_namaTtd" data-h="namaTtd" maxlength="100">

                        <label for="h_jabatanTtd">Jabatan</label>
                        <input type="text" class="form-control" id="h_jabatanTtd" data-h="jabatanTtd" maxlength="100">
                    </div>
                    <div class="bc-info mt-2">Tanggal pernyataan juga dikirim sebagai tanggal AJU.</div>
                </div>

            </div>
        </div>
    </div>
    <!-- End pageForm -->

    {{-- ===================== MODAL KOLEKSI (di luar .tb-report) ===================== --}}

    <!-- modal barang -->
    <div class="modal fade" id="mdlBarang" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title bc-modal-judul">Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div id="formBsGrid">
                    <div class="modal-body">
                        <div class="bs-form">
                            <label>Seri Barang</label>
                            <input type="text" class="form-control bc-tetap" data-f="seriBarang" disabled>

                            <label>Pos Tarif / HS</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="f_barang_posTarif" data-f="posTarif" maxlength="100">
                                <button type="button" class="btn btn-chip-biru bc-edit" onclick="bukaHs('#f_barang_posTarif')"><i class="bi bi-search"></i></button>
                            </div>

                            <label>Kode Barang</label>
                            <input type="text" class="form-control" data-f="kodeBarang" maxlength="20">

                            <label>Uraian</label>
                            <input type="text" class="form-control" data-f="uraian" maxlength="100">

                            <label>Merk</label>
                            <input type="text" class="form-control" data-f="merk" maxlength="100">

                            <label>Tipe</label>
                            <input type="text" class="form-control" data-f="tipe" maxlength="100">

                            <label>Ukuran</label>
                            <input type="text" class="form-control" data-f="ukuran" maxlength="100">

                            <label class="bs-wrap">Spesifikasi Lain</label>
                            <input type="text" class="form-control" data-f="spesifikasiLain" maxlength="100">

                            <label>Jumlah Satuan</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="jumlahSatuan">

                            <label>Satuan</label>
                            <select class="form-control" data-f="kodeSatuanBarang"></select>

                            <label class="bs-wrap">Jumlah Kemasan</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="jumlahKemasan">

                            <label class="bs-wrap">Jenis Kemasan</label>
                            <select class="form-control" data-f="kodeJenisKemasan"></select>

                            <label>Netto (Kg)</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="netto">

                            <label>Volume (m3)</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="volume">

                            <label>CIF</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan bc-tetap" data-f="cif" disabled>

                            <label>Nilai CIF (Rupiah)</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="cifRupiah" oninput="hitungCifBaris(this)">
                        </div>
                        <div class="bc-info mt-2">CIF = Nilai CIF / NDPBM header. Volume semua barang dijumlahkan menjadi Total Volume di tab Transaksi.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-chip-biru bc-edit" onclick="simpanModal('barang', false)">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal bahan baku -->
    <div class="modal fade" id="mdlBahanbaku" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title bc-modal-judul">Bahan Baku</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div id="formBsGrid">
                    <div class="modal-body">
                        <input type="hidden" data-f="seriBarang">
                        <div class="bs-form">
                            <label class="bs-wrap">Seri Bahan Baku</label>
                            <input type="number" step="1" class="form-control text-right" data-f="seriBahanBaku">

                            <label>Pos Tarif / HS</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="f_bahanbaku_posTarif" data-f="posTarif" maxlength="100">
                                <button type="button" class="btn btn-chip-biru bc-edit" onclick="bukaHs('#f_bahanbaku_posTarif')"><i class="bi bi-search"></i></button>
                            </div>

                            <label>Kode Barang</label>
                            <input type="text" class="form-control" data-f="kodeBarang" maxlength="20">

                            <label>Uraian</label>
                            <input type="text" class="form-control" data-f="uraianBarang" maxlength="100">

                            <label>Merk</label>
                            <input type="text" class="form-control" data-f="merkBarang" maxlength="100">

                            <label>Tipe</label>
                            <input type="text" class="form-control" data-f="tipeBarang" maxlength="100">

                            <label>Ukuran</label>
                            <input type="text" class="form-control" data-f="ukuranBarang" maxlength="100">

                            <label class="bs-wrap">Spesifikasi Lain</label>
                            <input type="text" class="form-control" data-f="spesifikasiLainBarang" maxlength="100">

                            <label>Jumlah Satuan</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="jumlahSatuan">

                            <label>Satuan</label>
                            <select class="form-control" data-f="kodeSatuanBarang"></select>

                            <label>Netto (Kg)</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="netto">

                            <label class="bs-wrap">Dokumen Asal</label>
                            <select class="form-control" data-f="kodeDokumen">
                                <option value="">- Pilih -</option>
                                @foreach ($kode['dokumenAsal'] as $k => $v)
                                    <option value="{{ $k }}">{{ $k }} - {{ $v }}</option>
                                @endforeach
                            </select>

                            <label class="bs-wrap">Kantor Asal</label>
                            <input type="text" class="form-control" data-f="kodeKantorAsal" maxlength="20">

                            <label class="bs-wrap">Nomor Daftar Dok Asal</label>
                            <input type="text" class="form-control" data-f="nomorDaftarDokAsal" maxlength="50">

                            <label class="bs-wrap">Tanggal Daftar Dok Asal</label>
                            <input type="date" class="form-control" data-f="tanggalDaftarDokAsal">

                            <label class="bs-wrap">Nomor AJU Dok Asal</label>
                            <input type="text" class="form-control" data-f="nomorAjuDokAsal" maxlength="100">

                            <label class="bs-wrap">Seri Barang Dok Asal</label>
                            <input type="number" step="1" class="form-control text-right" data-f="seriBarangDokAsal">

                            <label>CIF</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan bc-tetap" data-f="cif" disabled>

                            <label>CIF Rupiah</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="cifRupiah" oninput="hitungCifBaris(this)">

                            <label class="bs-wrap">Harga Penyerahan</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="hargaPenyerahan">

                            <label class="bs-wrap">Harga Perolehan</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="hargaPerolehan">

                            <label>Nilai Jasa</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="nilaiJasa">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-dpp-utama bc-edit bc-baru" onclick="simpanModal('bahanbaku', true)">Tambah</button>
                    <button type="button" class="btn btn-sm btn-chip-biru bc-edit" onclick="simpanModal('bahanbaku', false)">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal kemasan -->
    <div class="modal fade" id="mdlKemasan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title bc-modal-judul">Kemasan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div id="formBsGrid">
                    <div class="modal-body">
                        <div class="bs-form bs-form-1">
                            <label>Jumlah</label>
                            <input type="text" inputmode="numeric" class="form-control text-right bc-ribuan bc-int" data-f="jumlahKemasan">

                            <label class="bs-wrap">Jenis Kemasan</label>
                            <select class="form-control" data-f="kodeJenisKemasan"></select>

                            <label>Merk</label>
                            <input type="text" class="form-control" data-f="merkKemasan" maxlength="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-chip-biru bc-edit" onclick="simpanModal('kemasan', false)">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal peti kemas -->
    <div class="modal fade" id="mdlKontainer" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title bc-modal-judul">Peti Kemas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div id="formBsGrid">
                    <div class="modal-body">
                        <div class="bs-form bs-form-1">
                            <label class="bs-wrap">Nomor Kontainer</label>
                            <input type="text" class="form-control" data-f="nomorKontainer" maxlength="20">

                            <label class="bs-wrap">Ukuran</label>
                            <select class="form-control" data-f="kodeUkuranKontainer"></select>

                            <label class="bs-wrap">Tipe</label>
                            <select class="form-control" data-f="kodeTipeKontainer"></select>

                            <label class="bs-wrap">Jenis Muatan</label>
                            <select class="form-control" data-f="kodeJenisKontainer">
                                <option value="">- Pilih -</option>
                                @foreach ($kode['jenisMuatan'] as $k => $v)
                                    <option value="{{ $k }}">{{ $k }} - {{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-chip-biru bc-edit" onclick="simpanModal('kontainer', false)">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal dokumen -->
    <div class="modal fade" id="mdlDokumen" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title bc-modal-judul">Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div id="formBsGrid">
                    <div class="modal-body">
                        <div class="bs-form bs-form-1">
                            <label class="bs-wrap">Jenis Dokumen</label>
                            <select class="form-control" data-f="kodeDokumen" id="f_dokumen_kode"></select>

                            <label>Nomor</label>
                            <input type="text" class="form-control" data-f="nomorDokumen" maxlength="50">

                            <label>Tanggal</label>
                            <input type="date" class="form-control" data-f="tanggalDokumen">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-chip-biru bc-edit" onclick="simpanModal('dokumen', false)">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal pemilih HS -->
    {{-- Tampilan sama dengan modal pemilih menu Kas (public/css/picker-kas.css + picker-kas.js):
         baris = .pick-row (klik baris = pilih), kotak Search DataTables dipakai untuk mencari ke server. --}}
    <div class="modal fade rt-picker-v2 picker-kas" id="mdlHs" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Pos Tarif / HS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid mt-4">
                        <div class="row">
                            <div class="col-12" style="overflow:auto; margin-top:0px;">
                                <table id="tblHs" class="dph-tb">
                                    <thead>
                                        <tr><th scope="col">Pos Tarif</th><th scope="col">BM (%)</th><th scope="col">PPN (%)</th><th scope="col">PPh (%)</th></tr>
                                    </thead>
                                    <tbody id="tblHsData" class="text-left"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-pill-secondary picker-kas-batal" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal login CEISA -->
    <div class="modal fade" id="mdlLoginBc" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 420px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login CEISA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div id="formBsGrid">
                    <div class="modal-body">
                        <div class="bs-form bs-form-1">
                            <label for="loginBcUser">Username</label>
                            <input type="text" class="form-control" id="loginBcUser" autocomplete="username">

                            <label for="loginBcPass">Password</label>
                            <input type="password" class="form-control" id="loginBcPass" autocomplete="current-password">
                        </div>
                        <div class="bc-info mt-2" id="loginBcStatus"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-chip-biru" id="btnLoginBcKirim" onclick="submitLoginBc()">Login</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal preview JSON -->
    <div class="modal fade" id="mdlPreview" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview JSON CEISA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="previewMasalah" class="mb-2"></div>
                    <pre id="previewIsi"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-sm btn-chip-biru" onclick="salinPreview()">Salin</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
    <script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
    <script type="text/javascript">
        const KODE_KANTOR = @json($kodeKantor);
        const ENTITAS = @json($entitas);
        // Master Ukuran / Tipe Kontainer: [{kode, nama}]; yang dikirim ke CEISA hanya kodenya.
        const UKURAN_KONTAINER = @json($ukuranKontainer);
        const TIPE_KONTAINER = @json($tipeKontainer);
        const NAMA_UKURAN = Object.fromEntries(UKURAN_KONTAINER.map(k => [k.kode, k.nama]));
        const NAMA_TIPE = Object.fromEntries(TIPE_KONTAINER.map(k => [k.kode, k.nama]));
        // Master Jenis Kemasan (JENISKEMASAN): [{kode, nama}]
        const JENIS_KEMASAN = @json($lookup['kemasan']);
        // Master Jenis Satuan (JENISSATUAN): [{kode, nama}] untuk Satuan barang dan bahan baku
        const SATUAN = @json($lookup['satuan']);
        // Master Jenis Dokumen (JENISDOKUMEN): [{kode, nama}]
        const JENIS_DOKUMEN = @json($lookup['jenisDokumen']);
        const KODE = @json($kode);
        // Master valas (DBVALAS): [{kode, nama, kurs}]; kurs dipakai tombol "Kurs terbaru" (IDR selalu 1).
        const VALUTA = @json($valuta);

        const AKSES = {
            tambah: Number($('#akses_istambah').val()) === 1,
            koreksi: Number($('#akses_iskoreksi').val()) === 1,
            hapus: Number($('#akses_ishapus').val()) === 1,
            kirim: Number($('#akses_isexport').val()) === 1,
        };

        /* ============================================================================
         * Daftar transaksi - mesin kolom gcart_header disalin dari
         * gudang/permintaanpemakaian.blade.php (docs/new-slider-table-guide.md).
         * ========================================================================= */
        let lastRows = [];
        let pageSize = 10;
        let fokusId = '';

        var g_href = 'documentbc41';
        var g_modeReport = '1';
        var gcart_header = [];
        var gsum_issubtotal = 0;
        var gsum_isgrandtotal = 0;
        var gct_desimal_max = 4;

        function setDefaultHeader() {
            // [ field, label, visible, type, total, decimals ]
            gcart_header = [
                ['nomorAju', 'Nomor AJU', 1, 'varchar', 0, 0],
                ['arrDate', 'Tanggal Pengeluaran', 1, 'date', 0, 0],
                ['namaEntitas3', 'Penerima Barang', 1, 'varchar', 0, 0],
                ['sent', 'Status', 1, 'varchar', 0, 0],
                ['tglKirim', 'Tgl Kirim', 1, 'date', 0, 0]
            ];
        }

        function doSetHeader(_modereport, _isReset = false) {
            let _strHeader = (!_isReset) ? doLoadHeader(g_href, _modereport) : "";

            if (_strHeader != "") {
                gcart_header = doGetHeader(_strHeader);
            } else if ($.isFunction(window.setDefaultHeader)) {
                setDefaultHeader();
                doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
            }
        }

        function doLoadHeader(_href, _mode) {
            let _header = "";

            $.ajax({
                url: "{!! url('globalfunctions_doLoadHeader') !!}",
                type: "get",
                async: false,
                data: {
                    href: _href,
                    mode: _mode
                },
                success: function(res) {
                    _header = (res.length > 0) ? res[0].header : "";
                    if (res.length > 0) {
                        gsum_issubtotal = Number(res[0].issubtotal);
                        gsum_isgrandtotal = Number(res[0].isgrandtotal);
                    }
                }
            })

            return _header;
        }

        function doGetHeader(_strHeader) {
            let _cart = [];

            _strHeader.split("||").forEach((item, i) => {
                let temp = [];
                temp.push(item.split(";;")[0]);
                temp.push(item.split(";;")[1]);
                temp.push(Number(item.split(";;")[2]));
                temp.push(item.split(";;")[3]);
                temp.push(Number(item.split(";;")[4]));
                temp.push(Number(item.split(";;")[5]));
                _cart.push(temp);
            });

            return _cart;
        }

        function doSimpanHeader(_href, _mode, _cart, _issubtotal, _isgrandtotal) {
            let _strHeader = "";

            _cart.forEach((item, i) => {
                if (i != 0) {
                    _strHeader += '||';
                }
                _strHeader += item[0] + ';;' + item[1] + ';;' + item[2] + ';;' + item[3] + ';;' + item[4] + ';;' +
                    item[5];
            });

            $.ajax({
                url: "{!! url('globalfunctions_doSimpanHeader') !!}",
                type: "get",
                async: false,
                data: {
                    href: _href,
                    mode: _mode,
                    header: _strHeader,
                    issubtotal: _issubtotal,
                    isgrandtotal: _isgrandtotal
                },
                success: function(res) {
                    // nothing to do
                }
            })
        }

        function doMoveHeader(_from, _to) {
            if (_from < 0 || _to < 0 || _from === _to) {
                return;
            }
            if (_from >= gcart_header.length || _to >= gcart_header.length) {
                return;
            }

            let _moved = gcart_header.splice(_from, 1)[0];
            gcart_header.splice(_to, 0, _moved);

            doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
        }

        function doButtonVisibility(_id) {
            gcart_header[_id][2] = (Number(gcart_header[_id][2]) === 1) ? 0 : 1;

            doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
        }

        function doSetDesimal(_index, _step) {
            let _next = Number(gcart_header[_index][5]) + _step;
            if (_next < 0 || _next > gct_desimal_max) {
                return;
            }

            gcart_header[_index][5] = _next;
            doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
        }

        function doButtonTotal(_index) {
            gcart_header[_index][4] = (Number(gcart_header[_index][4]) === 1) ? 0 : 1;

            doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
        }

        /* ---------------------------------------------------------------- util */
        function esc(v) {
            return $('<div>').text(v === null || v === undefined ? '' : String(v)).html();
        }

        // Pemisah ribuan untuk semua input angka (class bc-ribuan; bc-int = bilangan bulat). Nilai di
        // field berformat "1,234.5"; disimpan/dikirim tanpa koma (lihat header di simpan() dan bacaModal()).
        function formatRibuan(v, bulatSaja) {
            if (v === null || v === undefined) {
                return '';
            }
            const raw = String(v).replace(/[^0-9.]/g, '');
            if (raw === '') {
                return '';
            }
            const titik = raw.indexOf('.');
            let bulat = titik === -1 ? raw : raw.slice(0, titik);
            bulat = bulat.replace(/^0+(?=\d)/, '');
            if (bulat === '') {
                bulat = '0';
            }
            bulat = bulat.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            if (bulatSaja || titik === -1) {
                return bulat;
            }
            return bulat + '.' + raw.slice(titik + 1).replace(/\./g, '');
        }

        // Format ulang isi satu input bc-ribuan, posisi kursor dijaga dari kanan.
        function formatRibuanKetik(el) {
            const aktif = document.activeElement === el;
            const posDariKanan = aktif && el.selectionStart !== null ? el.value.length - el.selectionStart : 0;
            el.value = formatRibuan(el.value, el.classList.contains('bc-int'));
            if (aktif) {
                const pos = Math.max(0, el.value.length - posDariKanan);
                el.setSelectionRange(pos, pos);
            }
        }

        // Isi input bc-ribuan lewat kode (nilai mentah -> tampil berformat).
        function setRibuan(selector, v) {
            const el = $(selector).get(0);
            el.value = formatRibuan(v, el.classList.contains('bc-int'));
        }

        $(document).on('input', '.bc-ribuan', function() {
            formatRibuanKetik(this);
        });

        function angka(v) {
            let n = Number(String(v === null || v === undefined || v === '' ? 0 : v).replace(/,/g, ''));
            return isNaN(n) ? 0 : n;
        }

        function fmtNum(v) {
            return angka(v).toLocaleString('en-US', { maximumFractionDigits: 4 });
        }

        function fmtYMD(v) {
            if (!v) {
                return '';
            }
            let t = String(v).substring(0, 10).split('-');
            return t.length === 3 ? t[0] + '/' + t[1] + '/' + t[2] : '';
        }

        function pesanError(err, bawaan) {
            return (err && err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : bawaan;
        }

        /* ---------------------------------------------------------------- list */
        function aksiButtonsHtml(r) {
            const id = Number(r.id);
            const sent = Number(r.sent) === 1;
            let html = '<button type="button" class="btn btn-warning btn-sm" title="Detail" onclick="buttonDetail(' + id + ')"><i class="bi bi-info"></i></button>';

            // Dokumen yang sudah terkirim tidak bisa diedit/dihapus (masih bisa dikirim ulang).
            if (!sent && AKSES.koreksi) {
                html += '<button type="button" class="btn btn-success btn-sm" title="Edit" onclick="buttonEdit(' + id + ')"><i class="bi bi-pen"></i></button>';
            }
            if (!sent && AKSES.hapus) {
                html += '<button type="button" class="btn btn-danger btn-sm" title="Hapus" onclick="buttonDelete(' + id + ')"><i class="bi bi-trash"></i></button>';
            }
            if (AKSES.kirim) {
                html += '<button type="button" class="btn btn-primary btn-sm" title="' + (sent ? 'Kirim Ulang ke CEISA' : 'Kirim ke CEISA') +
                    '" onclick="buttonKirim(' + id + ')"><i class="bi bi-send"></i></button>';
                html += '<button type="button" class="btn btn-info btn-sm" title="Preview JSON" onclick="buttonPreview(' + id + ')"><i class="bi bi-code-slash"></i></button>';
            }

            return '<div class="po-aksi-wrap">' + html + '</div>';
        }

        function rtPindahBar(idBar, idTabel) {
            let bar = document.getElementById(idBar);
            let tabel = document.getElementById(idTabel);
            if (!bar || !tabel) {
                return;
            }

            let acuan = tabel;
            if ($.fn.DataTable.isDataTable('#' + idTabel)) {
                acuan = document.getElementById(idTabel + '_wrapper') || tabel;
            }

            if (acuan.previousElementSibling !== bar) {
                acuan.parentNode.insertBefore(bar, acuan);
            }
        }

        function ikatSearch() {
            let input = document.getElementById('searchBox2');
            if (!input || input.dataset.rtBound) {
                return;
            }
            input.dataset.rtBound = '1';

            input.addEventListener('input', function() {
                $('#tabel').DataTable().search(input.value).draw();
            });
        }

        function ikatPanjangHalaman() {
            let sel = document.getElementById('tampilLen');
            if (!sel || sel.dataset.rtBound) {
                return;
            }
            sel.dataset.rtBound = '1';
            sel.value = String(pageSize);

            sel.addEventListener('change', function() {
                let n = Number(sel.value);
                pageSize = (n === -1 || n > 0) ? n : 10;
                $('#tabel').DataTable().page.len(pageSize).draw();
            });
        }

        function pindahKeHalamanBaris(id) {
            let dt = $('#tabel').DataTable();
            let panjang = dt.page.len();
            if (!id || panjang === -1) {
                return;
            }

            let idx = dt.rows({
                    order: 'current',
                    search: 'applied'
                }).nodes().toArray()
                .findIndex(function(tr) {
                    return tr.getAttribute('data-id') === String(id);
                });
            if (idx < 0) {
                return;
            }

            dt.page(Math.floor(idx / panjang)).draw('page');
        }

        function aturTinggiTabel() {
            let page = document.getElementById('pageHome');
            if (!page || page.offsetParent === null) {
                return;
            }

            let area = document.getElementById('content');
            let wrap = document.querySelector('#pageHome .po-table-wrap');
            if (!area || !wrap) {
                return;
            }

            wrap.style.maxHeight = 'none';

            let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0;
            let batasBawah = area.getBoundingClientRect().bottom - padBawah;
            let kotak = wrap.getBoundingClientRect();
            let bawah = page.getBoundingClientRect().bottom - kotak.bottom;

            let sisa = batasBawah - kotak.top - bawah - 4;
            wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px';
        }

        function renderTabel() {
            const cols = gcart_header.filter(c => c[2] === 1);

            if ($.fn.DataTable.isDataTable('#tabel')) {
                $('#tabel').DataTable().destroy();
            }

            const thead = document.querySelector('#tabel thead');
            thead.innerHTML = ReportTable.headHtml(cols).replace('<tr>',
                '<tr><th style="padding: 4px 12px;" scope="col">Actions</th>');

            let html = '';
            lastRows.forEach(function(r) {
                html += '<tr data-id="' + Number(r.id) + '">';
                html += '<td class="text-center">' + aksiButtonsHtml(r) + '</td>';
                html += cols.map(function(c) {
                    const v = r[c[0]];
                    if (c[0] === 'sent') {
                        return Number(v) === 1 ?
                            '<td><span class="sp-badge is-active">SENT &#10003;</span></td>' :
                            '<td><span class="sp-badge is-inactive">Belum</span></td>';
                    }
                    if (c[3] === 'date') {
                        return '<td>' + fmtYMD(v) + '</td>';
                    }
                    return '<td>' + esc(v) + '</td>';
                }).join('');
                html += '</tr>';
            });

            document.getElementById('tabel_data').innerHTML = html;

            $('#tabel').DataTable({
                lengthChange: false,
                pageLength: pageSize,
                order: [],
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }],
                dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    emptyTable: 'Tidak ada data',
                    zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
                }
            });

            rtPindahBar('rtBar', 'tabel');
            ikatSearch();
            ikatPanjangHalaman();

            let inputSearch = document.getElementById('searchBox2');
            if (inputSearch && inputSearch.value) {
                $('#tabel').DataTable().search(inputSearch.value).draw();
            }

            if (fokusId) {
                pindahKeHalamanBaris(fokusId);
                fokusId = '';
            }

            aturTinggiTabel();
        }

        // Lapisan "Memuat data..." di atas area tabel selama datanya diambil dan tabelnya digambar.
        // Dihitung supaya dua pemuatan yang tumpang tindih tidak saling mematikan lapisan.
        let memuatJml = 0;

        function tampilMemuat(tampil) {
            memuatJml = Math.max(0, memuatJml + (tampil ? 1 : -1));
            $('#areaTabel').children('.bc-memuat').remove();
            $('#areaTabel').toggleClass('bc-pane-memuat', memuatJml > 0);
            if (memuatJml > 0) {
                $('#areaTabel').append('<div class="bc-memuat"><span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span></div>');
            }
        }

        function reloadData(_fokusId) {
            tampilMemuat(true);
            $.ajax({
                url: "{!! url('documentbc41loadall') !!}",
                type: "get",
                success: function(res) {
                    lastRows = res || [];
                    fokusId = _fokusId || '';
                    renderTabel();
                },
                error: function(err) {
                    alertify.error(pesanError(err, 'Gagal memuat daftar dokumen'));
                },
                complete: function() {
                    tampilMemuat(false);
                }
            });
        }

        function showPage(id) {
            $('#pageHome, #pageForm').hide();
            $('#' + id).show();
        }

        function buttonCloseForm() {
            showPage('pageHome');
            aturTinggiTabel();
        }

        /* ============================================================================
         * Form dokumen: header (input [data-h]) + koleksi anak di `state`.
         * ========================================================================= */
        let mode = 'add'; // add | edit | detail
        let docId = null;
        let state = {};
        let selBarang = null; // seriBarang (posisi 1..n) yang dipilih untuk panel bahan baku
        let editIdx = {}; // index baris yang sedang diedit di modal per koleksi (null = baru)

        function stateKosong() {
            return { barang: [], bahanbaku: [], kemasan: [], kontainer: [], dokumen: [] };
        }

        function isiEntitas(n, id) {
            const e = ENTITAS.find(x => String(x.id) === String(id)) || null;
            $('#h_nomorIdentitasEntitas' + n).val(e ? (e.nomorIdentitas || '') : '');
            $('#h_namaEntitas' + n).val(e ? (e.namaEntitas || '') : '');
            $('#h_alamatEntitas' + n).val(e ? (e.alamatEntitas || '') : '');
            if (Number(n) === 1) {
                $('#h_nomorIjinEntitas1').val(e ? (e.nomorIjinEntitas || '') : '');
                $('#h_tanggalIjinEntitas1').val(e ? (e.tanggalIjinEntitas || '') : '');
            }
            bangunWajibBayar();
        }

        $(document).on('change', '.bc-entitas', function() {
            isiEntitas($(this).data('n'), $(this).val());
        });

        // Pilihan Wajib Bayar (kodePembayar = seri entitas) dari entitas master di tab Entitas yang punya NPWP, urutan
        // tab Entitas: Pengusaha TPB (3), Penerima Barang (8), Pemilik Barang (7). Teks = "NPWP - nama". Dibangun ulang
        // tiap entitas berubah (Q-h'), saat tab Pungutan dibuka dan saat dokumen dimuat; pilihan yang entitasnya sudah
        // tidak ada dikosongkan.
        const WAJIB_BAYAR = [[1, '3'], [3, '8'], [2, '7']];

        function bangunWajibBayar(pilih) {
            const sekarang = pilih === undefined ? String($('#h_kodePembayar').val() || '') : String(pilih || '');
            let html = '<option value="">- Pilih -</option>';
            let ada = false;
            WAJIB_BAYAR.forEach(function(p) {
                const e = ENTITAS.find(x => String(x.id) === String($('#h_selectEntitas' + p[0]).val())) || null;
                const npwp = e ? String(e.nomorIdentitas || '').trim() : '';
                if (!npwp) {
                    return;
                }
                const cocok = sekarang === p[1];
                ada = ada || cocok;
                html += '<option value="' + p[1] + '"' + (cocok ? ' selected' : '') + '>' + esc(npwp + ' - ' + (e.namaEntitas || '')) + '</option>';
            });
            $('#h_kodePembayar').html(html);
            if (ada) {
                $('#errPembayar').hide();
            }
        }

        $(document).on('shown.bs.tab', '#bcTabs a[href="#tabPungutan"]', function() {
            bangunWajibBayar();
        });
        $(document).on('change', '#h_kodePembayar', function() {
            $('#errPembayar').hide();
        });

        const rp = n => n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const bulat2 = n => Math.round(n * 100) / 100;

        // Nilai turunan Transaksi (plan §4.4): Nilai Pabean = CIF x NDPBM; DPP = Harga Penyerahan - Uang Muka - Diskon;
        // PPN / PPnBM = DPP x tarif / 100. Dihitung ulang setiap isian berubah; server mempercayai hasilnya.
        function hitungTransaksi() {
            $('#h_cifRupiah').val(rp(bulat2(angka($('#h_ndpbm').val()) * angka($('#h_cif').val()))));
            const dpp = angka($('#h_hargaPenyerahan').val()) - angka($('#h_uangMuka').val()) - angka($('#h_diskon').val());
            $('#h_dasarPengenaanPajak').val(rp(dpp));
            $('#h_ppnPajak').val(rp(dpp * angka($('#h_tarifPpnPajak').val()) / 100));
            $('#h_ppnbmPajak').val(rp(dpp * angka($('#h_tarifPpnbmPajak').val()) / 100));
        }

        // Kurs dari master valas (DBVALAS.KURS) untuk valuta terpilih; IDR = 1. null bila belum diisi.
        function kursValuta(kode) {
            if (kode === 'IDR') {
                return 1;
            }
            const v = VALUTA.find(x => String(x.kode) === String(kode));
            return v && Number(v.kurs) > 0 ? Number(v.kurs) : null;
        }

        function kursTerbaru() {
            const kode = $('#h_kodeValuta').val();
            const kurs = kursValuta(kode);
            if (kurs === null) {
                alertify.warning('Kurs ' + esc(kode || 'valuta') + ' belum diisi di master valas.');
                return;
            }
            setRibuan('#h_ndpbm', kurs);
            hitungTransaksi();
        }

        // Ganti valuta mengisi NDPBM dari kursnya (bila ada); kurs yang tidak ada dibiarkan agar bisa diketik.
        $(document).on('change', '#h_kodeValuta', function() {
            const kurs = kursValuta($(this).val());
            if (kurs !== null) {
                setRibuan('#h_ndpbm', kurs);
                hitungTransaksi();
            }
        });

        function syncNettoBarang() {
            const total = state.barang.reduce((s, r) => s + angka(r.netto), 0);
            setRibuan('#h_netto', Math.round(total * 10000) / 10000);
            alertify.success(state.barang.length + ' barang dijumlahkan.');
        }

        $(document).on('input', '#h_bruto', function() {
            $('#errBruto').toggle(angka($(this).val()) <= 0);
        });

        // CIF barang / bahan baku = Nilai CIF (Rupiah) / NDPBM header (Q-c); kosong bila NDPBM 0.
        function cifDariRupiah(nilaiRupiah) {
            const nd = angka($('#h_ndpbm').val());
            return nd ? bulat2(angka(nilaiRupiah) / nd) : '';
        }

        function hitungCifBaris(el) {
            setRibuan($(el).closest('.modal').find('[data-f="cif"]'), cifDariRupiah($(el).val()));
        }

        // Tambah: header kembali ke nilai awal (kantor, cara angkut, entitas pertama - Q-k), koleksi dikosongkan.
        function resetHeader() {
            $('[data-h]').each(function() {
                if (this.tagName === 'SELECT') {
                    this.selectedIndex = 0;
                } else {
                    $(this).val('');
                }
            });
            $('#h_kodeKantor').val(KODE_KANTOR);
            [1, 2, 3].forEach(function(n) {
                const pertama = ENTITAS.length ? String(ENTITAS[0].id) : '';
                $('#h_selectEntitas' + n).val(pertama);
                isiEntitas(n, pertama);
            });
            // Nilai awal transaksi (plan §2.1): IDR, kurs 1, PPN 11%, semua jumlah 0; pilihan TPB / tujuan Retur.
            opsiMaster('#h_kodeValuta', VALUTA, 'IDR');
            $('#h_kodeJenisTpb').val('1');
            $('#h_kodeTujuanPengiriman').val('3');
            $('#h_ndpbm').val('1');
            $('#h_tarifPpnPajak').val('11');
            $('#h_cif, #h_hargaPenyerahan, #h_uangMuka, #h_diskon, #h_tarifPpnbmPajak, #h_bruto, #h_netto').val('0');
            hitungTransaksi();
            $('#errBruto').hide();
            $('#errPembayar').hide();
        }

        function isiHeader(d) {
            opsiMaster('#h_kodeValuta', VALUTA, d.kodeValuta || 'IDR');
            $('[data-h]').each(function() {
                const v = d[$(this).data('h')];
                $(this).val(v === null || v === undefined ? '' : String(v));
                if ($(this).hasClass('bc-ribuan')) {
                    formatRibuanKetik(this);
                }
            });
            // Dokumen lama tanpa pilihan: kembali ke nilai bawaan.
            [['#h_kodeValuta', 'IDR'], ['#h_kodeJenisTpb', '1']].forEach(function(p) {
                if (!$(p[0]).val()) {
                    $(p[0]).val(p[1]);
                }
            });
            // Entitas sudah terisi di atas: bangun pilihan Wajib Bayar dari situ, lalu pilih seri yang tersimpan.
            bangunWajibBayar(d.kodePembayar);
            hitungTransaksi();
            $('#errBruto').hide();
            $('#errPembayar').hide();
        }

        // Detail = read-only (semua isian dimatikan, tombol ubah disembunyikan).
        function aturMode() {
            const baca = mode === 'detail';
            $('#pageForm').find('input, select, textarea').not('.bc-tetap').prop('disabled', baca);
            $('#pageForm .bc-edit').toggle(!baca);
            $('#formJudul').text(mode === 'add' ? 'Tambah Dokumen BC 4.1' : (mode === 'edit' ? 'Edit Dokumen BC 4.1' : 'Detail Dokumen BC 4.1'));
        }

        function bukaForm() {
            selBarang = null;
            renderSemua();
            aturMode();
            $('#bcTabs a:first').tab('show');
            showPage('pageForm');
        }

        function buttonAdd() {
            if (!AKSES.tambah) {
                alertify.warning('No access');
                return;
            }
            mode = 'add';
            docId = null;
            state = stateKosong();
            resetHeader();
            bukaForm();
        }

        function muatDokumen(id, _mode) {
            $.ajax({
                url: "{!! url('documentbc41spdetail') !!}",
                type: "get",
                data: { id },
                success: function(res) {
                    if (Number(res.sent) === 1 && _mode === 'edit') {
                        alertify.warning('Dokumen sudah dikirim ke CEISA, tidak bisa diubah');
                        _mode = 'detail';
                    }
                    mode = _mode;
                    docId = Number(res.id);
                    state = stateKosong();
                    Object.keys(state).forEach(function(k) {
                        state[k] = res[k] || [];
                    });
                    isiHeader(res);
                    bukaForm();
                },
                error: function(err) {
                    alertify.error(pesanError(err, 'Gagal memuat dokumen'));
                }
            });
        }

        function buttonDetail(id) {
            muatDokumen(id, 'detail');
        }

        function buttonEdit(id) {
            if (!AKSES.koreksi) {
                alertify.warning('No access');
                return;
            }
            muatDokumen(id, 'edit');
        }

        function buttonDelete(id) {
            if (!AKSES.hapus) {
                alertify.warning('No access');
                return;
            }
            const r = lastRows.find(x => Number(x.id) === Number(id)) || {};
            alertify.confirm('Hapus Dokumen', 'Apakah yakin ingin menghapus dokumen BC 4.1 ' + esc(r.nomorAju || ('#' + id)) + ' ?',
                function() {
                    $.ajax({
                        url: "{!! url('documentbc41spdelete') !!}",
                        type: "post",
                        data: {
                            _token: $('#_token').val(),
                            id
                        },
                        success: function(res) {
                            alertify.success(res.message);
                            reloadData();
                        },
                        error: function(err) {
                            alertify.error(pesanError(err, 'Gagal menghapus dokumen'));
                        }
                    });
                },
                function() {});
        }

        function submitDokumen() {
            if (mode === 'detail') {
                return;
            }
            // Bruto wajib > 0 dan Wajib Bayar dipilih (plan §4.4), Nomor AJU wajib (Q-l); server memeriksa ulang.
            if (angka($('#h_bruto').val()) <= 0) {
                $('#errBruto').show();
                $('#bcTabs a[href="#tabTransaksi"]').tab('show');
                alertify.error('Berat Kotor harus lebih besar dari 0');
                return;
            }
            if (!$('#h_kodePembayar').val()) {
                $('#errPembayar').text('Wajib Bayar harus dipilih').show();
                $('#bcTabs a[href="#tabPungutan"]').tab('show');
                alertify.error('Wajib Bayar harus dipilih');
                return;
            }
            if (!$('#h_nomorAju').val().trim()) {
                $('#bcTabs a[href="#tabHeader"]').tab('show');
                $('#h_nomorAju').trigger('focus');
                alertify.error('Nomor AJU harus diisi.');
                return;
            }
            // CIF barang / bahan baku mengikuti NDPBM header saat ini (Q-c).
            state.barang.forEach(r => r.cif = cifDariRupiah(r.cifRupiah));
            state.bahanbaku.forEach(r => r.cif = cifDariRupiah(r.cifRupiah));

            const header = {};
            $('[data-h]').each(function() {
                header[$(this).data('h')] = $(this).hasClass('bc-ribuan') ? $(this).val().replace(/,/g, '') : $(this).val();
            });

            const data = { header };
            if (mode === 'edit') {
                data.id = docId;
            }
            Object.keys(state).forEach(function(k) {
                data[k] = state[k];
            });

            $.ajax({
                url: mode === 'edit' ? "{!! url('documentbc41spedit') !!}" : "{!! url('documentbc41spadd') !!}",
                type: "post",
                data: {
                    _token: $('#_token').val(),
                    data: JSON.stringify(data)
                },
                success: function(res) {
                    alertify.success(res.message);
                    buttonCloseForm();
                    reloadData(res.id);
                },
                error: function(err) {
                    alertify.error(pesanError(err, 'Gagal menyimpan dokumen'));
                    const errors = (err.responseJSON && err.responseJSON.errors) || {};
                    if (errors.bruto) {
                        $('#errBruto').show();
                        $('#bcTabs a[href="#tabTransaksi"]').tab('show');
                    } else if (errors.kodePembayar) {
                        $('#errPembayar').text(errors.kodePembayar).show();
                        $('#bcTabs a[href="#tabPungutan"]').tab('show');
                    } else if (errors.nomorAju) {
                        $('#bcTabs a[href="#tabHeader"]').tab('show');
                    }
                }
            });
        }

        /* ============================================================================
         * Koleksi anak - semua di memori; nomor baris = posisi (seri diisi ulang server).
         * ========================================================================= */
        const MODAL = {
            barang: '#mdlBarang',
            bahanbaku: '#mdlBahanbaku',
            kemasan: '#mdlKemasan',
            kontainer: '#mdlKontainer',
            dokumen: '#mdlDokumen',
        };

        // Tombol baris tabel item: markup & kelas sama dengan baris item di gudang/permintaanpemakaian
        // (.btn-action-sm + .btn-action-success / -danger, gayanya dari po-table-header.css di .po-form-page).
        function aksiBaris(nama, idx) {
            if (mode === 'detail') {
                return '<div class="text-center text-nowrap"><button type="button" class="btn-action-sm btn-action-warning" title="Lihat" onclick="event.stopPropagation(); bukaModal(\'' +
                    nama + '\', ' + idx + ')"><i class="bi bi-info"></i></button></div>';
            }
            return '<div class="text-center text-nowrap">' +
                '<button type="button" class="btn-action-sm btn-action-success" title="Edit" onclick="event.stopPropagation(); bukaModal(\'' + nama + '\', ' + idx + ')"><i class="bi bi-pen"></i></button>' +
                '<button type="button" class="btn-action-sm btn-action-danger" title="Hapus" onclick="event.stopPropagation(); hapusBaris(\'' + nama + '\', ' + idx + ')"><i class="bi bi-trash"></i></button>' +
                '</div>';
        }

        function barisKosong(kolom, teks) {
            return '<tr class="bc-kosong"><td colspan="' + kolom + '">' + esc(teks || 'Belum ada data') + '</td></tr>';
        }

        function namaKode(daftar, kode) {
            return kode ? (kode + (daftar[kode] ? ' - ' + daftar[kode] : '')) : '';
        }

        function renderDokumen() {
            let html = state.dokumen.map(function(r, i) {
                return '<tr><td>' + (i + 1) + '</td><td>' + esc(r.kodeDokumen) + '</td><td>' + esc(r.nomorDokumen) +
                    '</td><td>' + fmtYMD(r.tanggalDokumen) + '</td><td>' + aksiBaris('dokumen', i) + '</td></tr>';
            }).join('');
            $('#tblDokumen').html(html || barisKosong(5));
            $('#infoDokumen').text(state.dokumen.length + ' dokumen');
        }

        function renderKemasan() {
            let html = state.kemasan.map(function(r, i) {
                return '<tr><td>' + (i + 1) + '</td><td class="text-right">' + fmtNum(r.jumlahKemasan) + '</td><td>' + esc(r.kodeJenisKemasan) +
                    '</td><td>' + esc(r.merkKemasan) + '</td><td>' + aksiBaris('kemasan', i) + '</td></tr>';
            }).join('');
            $('#tblKemasan').html(html || barisKosong(5));
            const total = state.kemasan.reduce((s, r) => s + angka(r.jumlahKemasan), 0);
            $('#infoKemasan').text(state.kemasan.length + ' baris, total ' + fmtNum(total) + ' kemasan');
        }

        function renderKontainer() {
            let html = state.kontainer.map(function(r, i) {
                return '<tr><td>' + (i + 1) + '</td><td>' + esc(r.nomorKontainer) + '</td><td>' + esc(namaKode(NAMA_UKURAN, r.kodeUkuranKontainer)) +
                    '</td><td>' + esc(namaKode(NAMA_TIPE, r.kodeTipeKontainer)) + '</td><td>' + esc(namaKode(KODE.jenisMuatan, r.kodeJenisKontainer)) +
                    '</td><td>' + aksiBaris('kontainer', i) + '</td></tr>';
            }).join('');
            $('#tblKontainer').html(html || barisKosong(6));
            $('#infoKontainer').text(state.kontainer.length + ' peti kemas');
        }

        function renderBarang() {
            let html = state.barang.map(function(r, i) {
                const seri = i + 1;
                return '<tr data-seri="' + seri + '" class="' + (selBarang === seri ? 'bc-sel' : '') + '" onclick="pilihBarang(' + seri + ')">' +
                    '<td>' + seri + '</td><td>' + esc(r.kodeBarang) + '</td><td>' + esc(r.uraian) + '</td><td>' + esc(r.posTarif) +
                    '</td><td class="text-right">' + fmtNum(r.jumlahSatuan) + '</td><td>' + esc(r.kodeSatuanBarang) +
                    '</td><td class="text-right">' + fmtNum(r.netto) + '</td><td class="text-right">' + fmtNum(r.volume) +
                    '</td><td class="text-right">' + fmtNum(r.cifRupiah) + '</td><td>' + aksiBaris('barang', i) + '</td></tr>';
            }).join('');
            $('#tblBarangData').html(html || barisKosong(10));
            // Total Volume header = jumlah volume barang (server menghitung ulang saat simpan).
            setRibuan('#h_volume', Math.round(state.barang.reduce((t, r) => t + angka(r.volume), 0) * 10000) / 10000);
            $('#infoBarang').text(state.barang.length + ' barang - klik baris barang untuk melihat bahan bakunya');
            renderBahanBaku();
        }

        function renderBahanBaku() {
            if (selBarang === null) {
                $('#tblBahanBaku').html(barisKosong(11, 'Pilih barang untuk melihat bahan bakunya.'));
                $('#infoBahanBaku').text('');
                return;
            }
            let jml = 0;
            let html = '';
            state.bahanbaku.forEach(function(r, i) {
                if (Number(r.seriBarang) !== selBarang) {
                    return;
                }
                jml++;
                html += '<tr><td>' + esc(r.seriBahanBaku) + '</td><td>' + esc(r.kodeBarang) + '</td><td>' + esc(r.uraianBarang) +
                    '</td><td>' + esc(r.posTarif) + '</td><td class="text-right">' + fmtNum(r.jumlahSatuan) + '</td><td>' + esc(r.kodeSatuanBarang) +
                    '</td><td>' + esc(r.kodeDokumen) + '</td><td>' + esc(r.nomorDaftarDokAsal) + '</td><td>' + fmtYMD(r.tanggalDaftarDokAsal) +
                    '</td><td class="text-right">' + fmtNum(r.cifRupiah) + '</td><td>' + aksiBaris('bahanbaku', i) + '</td></tr>';
            });
            $('#tblBahanBaku').html(html || barisKosong(11));
            $('#infoBahanBaku').text('(barang ' + selBarang + ': ' + jml + ' baris)');
        }

        function renderSemua() {
            renderDokumen();
            renderKemasan();
            renderKontainer();
            renderBarang();
        }

        // Klik baris barang = pilih; klik lagi = batal pilih.
        function pilihBarang(seri) {
            selBarang = (selBarang === seri) ? null : seri;
            renderBarang();
        }

        // Isi select Ukuran / Tipe / Jenis Kemasan dari master ("kode - nama"). Kode lama yang sudah tidak ada
        // di master tetap ditampilkan supaya tidak hilang saat dokumen lama disimpan ulang.
        function opsiMaster(selector, daftar, nilai) {
            let html = '<option value="">- Pilih -</option>';
            let ada = false;
            daftar.forEach(function(k) {
                const cocok = String(k.kode) === String(nilai);
                ada = ada || cocok;
                html += '<option value="' + esc(k.kode) + '"' + (cocok ? ' selected' : '') + '>' + esc(k.kode + ' - ' + k.nama) + '</option>';
            });
            if (!ada && nilai) {
                html += '<option value="' + esc(nilai) + '" selected>' + esc(nilai) + '</option>';
            }
            $(selector).html(html);
        }

        function judulModal(nama, idx) {
            const label = { barang: 'Barang', kemasan: 'Kemasan', kontainer: 'Peti Kemas', dokumen: 'Dokumen' };
            if (nama === 'bahanbaku') {
                const b = state.barang[selBarang - 1] || {};
                return 'Bahan Baku - Barang ' + (b.kodeBarang || '') + ' - ' + (b.uraian || '');
            }
            return (mode === 'detail' ? '' : (idx === null ? 'Tambah ' : 'Edit ')) + label[nama];
        }

        function nilaiAwal(nama) {
            if (nama === 'bahanbaku') {
                // Seri bahan baku berikutnya dihitung per barang (bukan urutan global).
                const seri = state.bahanbaku.filter(r => Number(r.seriBarang) === selBarang)
                    .reduce((m, r) => Math.max(m, angka(r.seriBahanBaku)), 0) + 1;
                return { seriBarang: selBarang, seriBahanBaku: seri, kodeKantorAsal: KODE_KANTOR };
            }
            if (nama === 'barang') {
                return { seriBarang: state.barang.length + 1, cif: 0, volume: 0 };
            }
            return {};
        }

        function bukaModal(nama, idx) {
            if (mode === 'detail' && idx === null) {
                return;
            }
            const modal = $(MODAL[nama]);
            editIdx[nama] = idx;

            if (nama === 'barang' && idx !== null) {
                selBarang = idx + 1; // pensil barang juga memilih barangnya
                renderBarang();
            }

            const row = idx === null ? nilaiAwal(nama) : state[nama][idx];
            opsiMaster(modal.find('[data-f="kodeJenisKemasan"]'), JENIS_KEMASAN, row.kodeJenisKemasan || '');
            if (nama === 'barang' || nama === 'bahanbaku') {
                opsiMaster(modal.find('[data-f="kodeSatuanBarang"]'), SATUAN, row.kodeSatuanBarang || '');
            }
            if (nama === 'dokumen') {
                opsiMaster('#f_dokumen_kode', JENIS_DOKUMEN, row.kodeDokumen || '');
            }
            modal.find('[data-f]').each(function() {
                const v = row[$(this).data('f')];
                $(this).val(v === null || v === undefined ? '' : String(v));
                if ($(this).hasClass('bc-ribuan')) {
                    formatRibuanKetik(this);
                }
            });
            if (nama === 'barang' && idx !== null) {
                modal.find('[data-f="seriBarang"]').val(idx + 1);
            }
            if (nama === 'kontainer') {
                opsiMaster('#mdlKontainer [data-f="kodeUkuranKontainer"]', UKURAN_KONTAINER, row.kodeUkuranKontainer || '');
                opsiMaster('#mdlKontainer [data-f="kodeTipeKontainer"]', TIPE_KONTAINER, row.kodeTipeKontainer || '');
            }

            const baca = mode === 'detail';
            modal.find('input, select, textarea').not('.bc-tetap').not('[type=hidden]').prop('disabled', baca);
            modal.find('.bc-edit').toggle(!baca);
            modal.find('.bc-baru').toggle(!baca && idx === null);
            modal.find('.bc-modal-judul').text(judulModal(nama, idx));
            modal.modal('show');
        }

        function tambahBahanBaku() {
            if (selBarang === null) {
                alertify.error('Pilih barang terlebih dahulu.');
                return;
            }
            bukaModal('bahanbaku', null);
        }

        function bacaModal(nama) {
            const row = {};
            $(MODAL[nama]).find('[data-f]').each(function() {
                const v = $(this).val();
                row[$(this).data('f')] = $(this).hasClass('bc-ribuan') ? v.replace(/,/g, '') : v;
            });
            return row;
        }

        // Simpan baris ke memori. lanjut = tombol "Tambah" bahan baku: simpan lalu buka baris baru.
        function simpanModal(nama, lanjut) {
            const row = bacaModal(nama);
            if (nama === 'kontainer' && (!row.kodeUkuranKontainer || !row.kodeTipeKontainer)) {
                alertify.error('Ukuran dan Tipe kontainer harus dipilih.');
                return;
            }
            if (nama === 'dokumen' && !row.kodeDokumen) {
                alertify.error('Jenis dokumen harus dipilih.');
                return;
            }
            const idx = editIdx[nama];
            if (idx === null || idx === undefined) {
                state[nama].push(row);
            } else {
                state[nama][idx] = Object.assign({}, state[nama][idx], row);
            }
            seriUlang(nama);
            renderSemua();

            if (lanjut) {
                bukaModal(nama, null);
                return;
            }
            $(MODAL[nama]).modal('hide');
        }

        function seriUlang(nama) {
            const kunci = { barang: 'seriBarang', kemasan: 'seriKemasan', kontainer: 'seriKontainer', dokumen: 'seriDokumen' }[nama];
            if (kunci) {
                state[nama].forEach((r, i) => r[kunci] = i + 1);
            }
        }

        function hapusBaris(nama, idx) {
            if (nama === 'barang') {
                // Hapus barang ikut menghapus bahan bakunya, dan seri barang sesudahnya digeser turun 1.
                const seri = idx + 1;
                state.bahanbaku = state.bahanbaku.filter(r => Number(r.seriBarang) !== seri);
                state.bahanbaku.forEach(function(r) {
                    if (Number(r.seriBarang) > seri) {
                        r.seriBarang = Number(r.seriBarang) - 1;
                    }
                });
                if (selBarang === seri) {
                    selBarang = null;
                } else if (selBarang !== null && selBarang > seri) {
                    selBarang--;
                }
            }
            state[nama].splice(idx, 1);
            seriUlang(nama);
            renderSemua();
        }

        /* ---------------------------------------------------------------- pemilih HS */
        let hsTarget = null;
        let hsTunda = null;
        let hsDt = null;

        // Tabel pemilih HS = DataTables bergaya Kas (pickerKasInit). Pencarian dilakukan di server
        // (POSTARIFHS besar), jadi pencarian bawaan DataTables diganti: kotak Search memanggil cariHs().
        function initHs() {
            const sel = (kunci, render) => ({ data: kunci, render: (d, t) => t === 'display' ? render(d) : d });
            hsDt = pickerKasInit('tblHs', {
                order: [],
                columns: [
                    sel('posTarif', esc),
                    sel('persenBM', fmtNum),
                    sel('persenPPN', fmtNum),
                    sel('persenPPh', fmtNum)
                ],
                createdRow: function(tr, r) {
                    $(tr).addClass('pick-row').attr({
                        'data-hs': r.posTarif,
                        'data-bm': angka(r.persenBM),
                        'data-ppn': angka(r.persenPPN),
                        'data-pph': angka(r.persenPPh)
                    });
                },
                language: {
                    lengthMenu: 'Tampilkan _MENU_',
                    emptyTable: 'Tidak ada data - pos tarif tetap bisa diketik langsung.',
                    zeroRecords: 'Tidak ada data'
                }
            });
            $('#tblHs_filter input').off('.DT').on('input', function() {
                clearTimeout(hsTunda);
                hsTunda = setTimeout(cariHs, 300);
            });
        }

        function bukaHs(target) {
            hsTarget = target;
            if (!hsDt) {
                initHs();
            }
            $('#tblHs_filter input').val($(target).val());
            cariHs();
            $('#mdlHs').modal('show');
        }

        function cariHs() {
            $.ajax({
                url: "{!! url('documentbc41lisths') !!}",
                type: "get",
                data: { search: $('#tblHs_filter input').val() },
                success: function(res) {
                    hsDt.clear().rows.add(res || []).draw();
                }
            });
        }

        // Memilih HS mengisi pos tarif (tarif BM / PPN / PPh di daftar hanya rujukan; skema resmi tidak mengirim tarif barang).
        $(document).on('click', '#tblHsData tr[data-hs]', function() {
            $(hsTarget).val($(this).data('hs'));
            $('#mdlHs').modal('hide');
        });

        // Backdrop modal HS di atas modal barang/bahan baku yang masih terbuka.
        $(document).on('shown.bs.modal', '#mdlHs', function() {
            $('.modal-backdrop').last().addClass('bc-backdrop-atas');
            $('#tblHs_filter input').trigger('focus');
        });

        $(document).on('hidden.bs.modal', '#mdlHs', function() {
            if ($('.modal.show').length) {
                $('body').addClass('modal-open');
            }
        });

        /* ---------------------------------------------------------------- CEISA */
        let bcExpires = null;
        let bcTimer = null;

        function setLoginBc(expiresAt) {
            bcExpires = expiresAt ? new Date(expiresAt).getTime() : null;
            clearInterval(bcTimer);
            tickLoginBc();
            if (bcExpires) {
                bcTimer = setInterval(tickLoginBc, 1000);
            }
        }

        function tickLoginBc() {
            const sisa = bcExpires ? bcExpires - Date.now() : 0;
            if (sisa <= 0) {
                clearInterval(bcTimer);
                bcExpires = null;
                $('#btnLoginBc').removeClass('is-login');
                $('#btnLoginBcTeks').text('Login BC');
                return;
            }
            const detik = Math.floor(sisa / 1000);
            const j = Math.floor(detik / 3600);
            const m = Math.floor((detik % 3600) / 60);
            const s = detik % 60;
            const dua = n => String(n).padStart(2, '0');
            $('#btnLoginBc').addClass('is-login');
            $('#btnLoginBcTeks').text('BC ' + (j ? j + ':' : '') + dua(m) + ':' + dua(s));
        }

        function cekStatusBc() {
            $.ajax({
                url: "{!! url('bcceisastatus') !!}",
                type: "get",
                success: function(res) {
                    setLoginBc(res && res.loggedIn ? res.expiresAt : null);
                }
            });
        }

        function bukaLoginBc() {
            $('#loginBcPass').val('');
            $('#loginBcStatus').text(bcExpires ? 'Sudah login - login ulang untuk memperpanjang token.' : '');
            $('#mdlLoginBc').modal('show');
        }

        $(document).on('shown.bs.modal', '#mdlLoginBc', function() {
            $('#loginBcUser').val() ? $('#loginBcPass').trigger('focus') : $('#loginBcUser').trigger('focus');
        });

        $(document).on('keydown', '#loginBcPass', function(e) {
            if (e.key === 'Enter') {
                submitLoginBc();
            }
        });

        function submitLoginBc() {
            const tombol = $('#btnLoginBcKirim').prop('disabled', true);
            $.ajax({
                url: "{!! url('bcceisalogin') !!}",
                type: "post",
                data: {
                    _token: $('#_token').val(),
                    username: $('#loginBcUser').val(),
                    password: $('#loginBcPass').val()
                },
                success: function(res) {
                    $('#loginBcPass').val('');
                    $('#mdlLoginBc').modal('hide');
                    setLoginBc(res.expiresAt);
                    alertify.success(res.message);
                },
                error: function(err) {
                    alertify.error(pesanError(err, 'Login gagal.'));
                },
                complete: function() {
                    tombol.prop('disabled', false);
                }
            });
        }

        function tampilGagalKirim(err) {
            const res = err.responseJSON || {};
            if (err.status === 401) {
                setLoginBc(null);
            }
            const baris = [res.message || 'Pengiriman gagal.'].concat(Array.isArray(res.details) ? res.details : []);
            if (err.status === 422 && Array.isArray(res.details) && res.details.length) {
                // Daftar masalah skema CEISA bisa panjang: tampil sebagai dialog (tetap sampai ditutup), satu masalah per baris.
                alertify.alert('Dokumen belum bisa dikirim', '<div style="max-height:60vh;overflow:auto">' +
                    esc(res.message) + '<ul class="mt-2 mb-0 pl-3">' + res.details.map(d => '<li>' + esc(d) + '</li>').join('') + '</ul></div>');
                return;
            }
            alertify.error(baris.map(esc).join('<br>'), 15);
        }

        function buttonKirim(id) {
            if (!AKSES.kirim) {
                alertify.warning('No access');
                return;
            }
            if (!bcExpires) {
                alertify.warning('Silakan Login BC terlebih dahulu.');
                bukaLoginBc();
                return;
            }
            const r = lastRows.find(x => Number(x.id) === Number(id)) || {};
            const ulang = Number(r.sent) === 1;
            alertify.confirm(ulang ? 'Kirim Ulang ke CEISA' : 'Kirim ke CEISA',
                (ulang ? 'Dokumen ini sudah pernah terkirim. Kirim ulang ' : 'Kirim dokumen ') + esc(r.nomorAju || ('#' + id)) + ' ke CEISA?',
                function() {
                    $.ajax({
                        url: "{!! url('documentbc41spkirim') !!}",
                        type: "post",
                        data: {
                            _token: $('#_token').val(),
                            id
                        },
                        success: function(res) {
                            alertify.success(esc(res.message));
                            reloadData(id);
                        },
                        error: tampilGagalKirim
                    });
                },
                function() {});
        }

        function buttonPreview(id) {
            $.ajax({
                url: "{!! url('documentbc41sppreview') !!}",
                type: "get",
                dataType: "json",
                data: { id },
                success: function(res) {
                    // Hasil pemeriksaan skema CEISA di atas JSON: hijau bila lolos, merah + daftar masalah bila belum.
                    const masalah = res.masalah || [];
                    $('#previewMasalah').html(masalah.length ?
                        '<div class="alert alert-danger mb-0"><strong>Belum bisa dikirim: ' + masalah.length + ' masalah ditemukan.</strong>' +
                        '<ul class="mb-0 pl-3 mt-1">' + masalah.map(m => '<li>' + esc(m) + '</li>').join('') + '</ul></div>' :
                        '<div class="alert alert-success mb-0">Lolos pemeriksaan skema CEISA, siap dikirim.</div>');
                    $('#previewIsi').text(res.json);
                    $('#mdlPreview').modal('show');
                },
                error: function(err) {
                    let pesan = 'Gagal menyusun JSON';
                    try {
                        pesan = JSON.parse(err.responseText).message || pesan;
                    } catch (e) {}
                    alertify.error(esc(pesan));
                }
            });
        }

        function salinPreview() {
            const teks = $('#previewIsi').text();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(teks).then(() => alertify.success('JSON disalin'));
            }
        }

        $(document).ready(function() {
            doSetHeader(g_modeReport);
            ReportTable.init({
                table: '#tabel',
                bar: '#rtBar',
                onChange: renderTabel
            });
            renderTabel();
            reloadData();
            cekStatusBc();
        });
    </script>
@endsection
