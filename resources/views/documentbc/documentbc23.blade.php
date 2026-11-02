@extends('newmasterTest')
@section('buttons')
@endsection
@section('page-title', 'Dokumen BC 2.3')

{{-- Dokumen BC 2.3 (pemasukan barang impor ke kawasan berikat) - spesifikasi di docs/documentbc23_plan.md.
     Halaman = salinan pola documentbc261.blade.php (list #pageHome + form full page #pageForm, mesin
     kolom gcart_header, tab form). Bedanya: SEMUA dokumen dibuat dari PO (DBPO) di tab Outstanding
     (PO pemasok luar negeri yang belum punya BC 2.3), kemasan / peti kemas / pengangkut hanya satu per
     dokumen (isian header), dan hanya ada dua koleksi anak: barang dan dokumen. Barang / dokumen
     diedit di memori (state) dan baru dikirim ke server saat Simpan dokumen. Bootstrap 4 only. --}}
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
        /* Label BC lebih panjang dari form master biasa: kolom label dilebarkan, label boleh turun baris. */
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

        #homeTabs {
            margin: 8px 12px;
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

        .bc-kosong td {
            text-align: center;
            color: #6c757d;
        }

        .bc-ringkas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 8px;
            margin-top: 10px;
        }

        .bc-ringkas div {
            background: #f8f9fb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: .8rem;
        }

        .bc-ringkas b {
            display: block;
            font-size: .95rem;
            text-align: right;
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

        /* Modal pemilih HS dibuka di atas modal barang. */
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
        #tabOutstanding,
        #tabTransaksiList {
            position: relative;
        }

        .tab-pane.bc-pane-memuat {
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
                        {{-- Periode hanya berlaku untuk tab Transaksi (Outstanding tidak difilter periode). --}}
                        <div class="po-filter-wrap" id="wrapPeriode" style="display:none">
                            <label>Periode</label>
                            <input type="date" class="po-filter-inp" id="inputDate1" value="{!! $date1 !!}" autocomplete="off">
                            <span class="po-filter-sep">s/d</span>
                            <input type="date" class="po-filter-inp" id="inputDate2" value="{!! $date2 !!}" autocomplete="off">
                        </div>

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
                    </div>

                    <div class="nav nav-tabs border-0 custom-tabs" id="homeTabs" role="tablist">
                        <a class="nav-item nav-link active" data-toggle="tab" href="#tabOutstanding" role="tab">Outstanding (PO)</a>
                        <a class="nav-item nav-link" data-toggle="tab" href="#tabTransaksiList" role="tab">Transaksi</a>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabOutstanding" role="tabpanel">
                            <table id="tabel2" class="data-table po-aksi-hover">
                                <thead>
                                    <tr>
                                        <th style="padding: 4px 12px;" scope="col">Actions</th>
                                        <th style="padding: 4px 12px;" scope="col">Rev. Doc. (No. PO)</th>
                                        <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                                        <th style="padding: 4px 12px;" scope="col">Pemasok</th>
                                        <th style="padding: 4px 12px;" scope="col">Negara</th>
                                        <th style="padding: 4px 12px;" scope="col">Valuta</th>
                                        <th style="padding: 4px 12px;" scope="col" class="text-right">Jumlah Baris</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel2_data" class="text-left"></tbody>
                            </table>
                            <div class="po-rt-hint">
                                <i class="bi bi-info-circle"></i>
                                PO pemasok luar negeri yang belum dibuatkan BC 2.3 (tidak batal dan sudah otorisasi).
                                Klik <i class="bi bi-plus-lg"></i> untuk membuat BC 2.3 dari PO.
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tabTransaksiList" role="tabpanel">
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
    </div>

    {{-- Daftar isian combobox (diambil dari master; tetap bisa diketik bebas). --}}
    <datalist id="dlNegara">
        @foreach ($lookup['negara'] as $r)
            <option value="{{ $r->kode }}">{{ $r->nama }}</option>
        @endforeach
    </datalist>
    <datalist id="dlPelabuhan">
        @foreach ($lookup['pelabuhan'] as $r)
            <option value="{{ $r->kode }}">{{ $r->nama }}</option>
        @endforeach
    </datalist>
    <datalist id="dlSatuan">
        @foreach ($lookup['satuan'] as $r)
            <option value="{{ $r->kode }}">{{ $r->nama }}</option>
        @endforeach
    </datalist>
    <datalist id="dlKategori">
        @foreach ($lookup['kategoriBarang'] as $r)
            <option value="{{ $r->kode }}">{{ $r->nama }}</option>
        @endforeach
    </datalist>
    <datalist id="dlValuta">
        @foreach ($valuta as $r)
            <option value="{{ $r->kode }}">{{ $r->nama }}</option>
        @endforeach
    </datalist>
    <datalist id="dlIncoterm">
        @foreach ($kode['incoterm'] as $v)
            <option value="{{ $v }}"></option>
        @endforeach
    </datalist>

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
                <a class="nav-item nav-link" data-toggle="tab" href="#tabKemasan" role="tab">Kemasan &amp; Peti Kemas</a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tabTransaksi" role="tab">Transaksi</a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tabBarang" role="tab">Barang</a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tabPernyataan" role="tab">Pernyataan</a>
            </div>

            <div class="tab-content">

                {{-- HEADER --}}
                <div class="tab-pane fade show active" id="tabHeader" role="tabpanel">
                    <div class="bs-form">
                        <label for="h_revDoc">Rev. Doc. (No. PO)</label>
                        <input type="text" class="form-control bc-tetap" id="h_revDoc" disabled>

                        <label for="h_nomorAju">Nomor Pengajuan</label>
                        <input type="text" class="form-control" id="h_nomorAju" data-h="nomorAju" maxlength="100">

                        <label for="h_kodeTujuanTpb">A. Tujuan</label>
                        <select class="form-control" id="h_kodeTujuanTpb" data-h="kodeTujuanTpb">
                            @foreach ($kode['tujuanTpb'] as $k => $v)
                                <option value="{{ $k }}">{{ $k }} - {{ $v }}</option>
                            @endforeach
                        </select>

                        <label for="h_kodeKantor">Kantor Pabean</label>
                        <input type="text" class="form-control" id="h_kodeKantor" data-h="kodeKantor" maxlength="20">

                        <label for="h_kodeKantorBongkar" class="bs-wrap">Kantor Pabean Bongkar</label>
                        <input type="text" class="form-control" id="h_kodeKantorBongkar" data-h="kodeKantorBongkar" maxlength="20">

                        <label for="h_nomorBc11">Nomor BC 1.1</label>
                        <input type="text" class="form-control" id="h_nomorBc11" data-h="nomorBc11" maxlength="50">

                        <label for="h_tanggalBc11">Tanggal BC 1.1</label>
                        <input type="date" class="form-control" id="h_tanggalBc11" data-h="tanggalBc11">

                        <label for="h_posBc11">Pos BC 1.1</label>
                        <input type="text" class="form-control" id="h_posBc11" data-h="posBc11" maxlength="50">

                        <label for="h_subPosBc11" class="bs-wrap">Sub Pos BC 1.1</label>
                        <input type="text" class="form-control" id="h_subPosBc11" data-h="subPosBc11" maxlength="50">

                        <label for="h_kodePelMuat" class="bs-wrap">Pelabuhan Muat</label>
                        <input type="text" class="form-control" id="h_kodePelMuat" data-h="kodePelMuat" list="dlPelabuhan" maxlength="20">

                        <label for="h_kodePelTransit" class="bs-wrap">Pelabuhan Transit</label>
                        <input type="text" class="form-control" id="h_kodePelTransit" data-h="kodePelTransit" list="dlPelabuhan" maxlength="20">

                        <label for="h_kodePelBongkar" class="bs-wrap">Pelabuhan Bongkar</label>
                        <input type="text" class="form-control" id="h_kodePelBongkar" data-h="kodePelBongkar" list="dlPelabuhan" maxlength="20">

                        <label for="h_kodeTps" class="bs-wrap">Tempat Penimbunan (TPS)</label>
                        <input type="text" class="form-control" id="h_kodeTps" data-h="kodeTps" maxlength="20">

                        <label for="h_ketTps" class="bs-wrap">Keterangan TPS</label>
                        <input type="text" class="form-control" id="h_ketTps" data-h="ketTps" maxlength="200">

                        <label for="h_tanggalTiba">Tanggal Tiba</label>
                        <input type="date" class="form-control" id="h_tanggalTiba" data-h="tanggalTiba">

                        <label for="h_nomorDokumenPabean" class="bs-wrap">No. Dokumen Pabean</label>
                        <input type="text" class="form-control" id="h_nomorDokumenPabean" data-h="nomorDokumenPabean" maxlength="50">

                        <label for="h_tanggalDokumenPabean" class="bs-wrap">Tgl. Dokumen Pabean</label>
                        <input type="date" class="form-control" id="h_tanggalDokumenPabean" data-h="tanggalDokumenPabean">
                    </div>
                    <div class="bc-info mt-2">Tanggal Tiba wajib diisi sebelum dikirim ke CEISA. No. dan Tgl. Dokumen Pabean ("Diisi oleh Bea dan Cukai") hanya catatan internal, tidak dikirim. Keterangan TPS juga tidak dikirim.</div>
                </div>

                {{-- ENTITAS - data yang DIKIRIM ke CEISA diambil dari master entitas yang dipilih. --}}
                <div class="tab-pane fade" id="tabEntitas" role="tabpanel">
                    @foreach ([1 => 'Importir / Pengusaha TPB', 2 => 'Pemasok', 3 => 'Pemilik Barang'] as $n => $judul)
                        <div class="bc-judul">{{ $judul }}</div>
                        <div class="bs-form">
                            <label for="h_selectEntitas{{ $n }}">Entitas</label>
                            <select class="form-control bc-entitas" id="h_selectEntitas{{ $n }}" data-h="selectEntitas{{ $n }}" data-n="{{ $n }}">
                                <option value="">- Pilih Entitas -</option>
                                @foreach ($entitas as $e)
                                    <option value="{{ $e->id }}">{{ $e->namaEntitas }}</option>
                                @endforeach
                            </select>

                            @if ($n !== 2)
                                <label for="h_nomorIdentitasEntitas{{ $n }}">NPWP</label>
                                <input type="text" class="form-control" id="h_nomorIdentitasEntitas{{ $n }}" data-h="nomorIdentitasEntitas{{ $n }}" maxlength="50">
                            @else
                                <label for="h_kodeNegaraEntitas2">Kode Negara</label>
                                <input type="text" class="form-control bc-tetap" id="h_kodeNegaraEntitas2" data-h="kodeNegaraEntitas2" disabled>
                            @endif

                            <label for="h_alamatEntitas{{ $n }}">Alamat</label>
                            <div class="bs-full">
                                <input type="hidden" id="h_namaEntitas{{ $n }}" data-h="namaEntitas{{ $n }}">
                                <textarea class="form-control" id="h_alamatEntitas{{ $n }}" data-h="alamatEntitas{{ $n }}" rows="2" maxlength="1000"></textarea>
                            </div>

                            @if ($n === 1)
                                <label for="h_nomorIjinEntitas1" class="bs-wrap">Nomor Izin TPB</label>
                                <input type="text" class="form-control" id="h_nomorIjinEntitas1" data-h="nomorIjinEntitas1" maxlength="50">

                                <label for="h_nibEntitas1">NIB</label>
                                <input type="text" class="form-control" id="h_nibEntitas1" data-h="nibEntitas1" maxlength="50">
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- DOKUMEN --}}
                <div class="tab-pane fade" id="tabDokumen" role="tabpanel">
                    <div class="bc-toolbar-koleksi">
                        <span class="bc-info" id="infoDokumen"></span>
                        <button type="button" class="btn btn-chip-biru btn-sm bc-edit" onclick="bukaModal('dokumen', null)">Tambah Dokumen</button>
                    </div>
                    <div class="tb-report">
                        <div class="table-outer">
                            <div class="table-wrap" style="max-height:40vh;">
                                <table class="tb">
                                    <thead>
                                        <tr><th>No</th><th>Jenis Dokumen</th><th>Nomor Dokumen</th><th>Tanggal</th><th>Action</th></tr>
                                    </thead>
                                    <tbody id="tblDokumen"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PENGANGKUT --}}
                <div class="tab-pane fade" id="tabPengangkut" role="tabpanel">
                    <div class="bs-form">
                        <label for="h_kodeCaraAngkut" class="bs-wrap">11. Cara Pengangkutan</label>
                        <select class="form-control" id="h_kodeCaraAngkut" data-h="kodeCaraAngkut">
                            @foreach ($kode['caraAngkut'] as $k => $v)
                                <option value="{{ $k }}">{{ $k }} - {{ $v }}</option>
                            @endforeach
                        </select>

                        <label for="h_namaPengangkut" class="bs-wrap">Nama Sarana Pengangkut</label>
                        <input type="text" class="form-control" id="h_namaPengangkut" data-h="namaPengangkut" maxlength="200">

                        <label for="h_nomorPengangkut" class="bs-wrap">Nomor Voy / Flight</label>
                        <input type="text" class="form-control" id="h_nomorPengangkut" data-h="nomorPengangkut" maxlength="200">

                        <label for="h_kodeBendera">Bendera</label>
                        <input type="text" class="form-control" id="h_kodeBendera" data-h="kodeBendera" list="dlNegara" maxlength="5">
                    </div>
                </div>

                {{-- KEMASAN + PETI KEMAS (masing-masing satu per dokumen) --}}
                <div class="tab-pane fade" id="tabKemasan" role="tabpanel">
                    <div class="bc-judul">30. Kemasan</div>
                    <div class="bs-form">
                        <label for="h_jumlahKemasan">Jumlah</label>
                        <input type="text" inputmode="numeric" class="form-control text-right bc-ribuan bc-int" id="h_jumlahKemasan" data-h="jumlahKemasan">

                        <label for="h_kodeJenisKemasan" class="bs-wrap">Jenis Kemasan</label>
                        <select class="form-control" id="h_kodeJenisKemasan" data-h="kodeJenisKemasan"></select>

                        <label for="h_merkKemasan">Merk</label>
                        <input type="text" class="form-control" id="h_merkKemasan" data-h="merkKemasan" maxlength="100">
                    </div>

                    <div class="bc-judul">29. Peti Kemas</div>
                    <div class="bs-form">
                        <label for="h_nomorKontainer" class="bs-wrap">Nomor Kontainer</label>
                        <input type="text" class="form-control" id="h_nomorKontainer" data-h="nomorKontainer" maxlength="50">

                        <label for="h_kodeUkuranKontainer">Ukuran</label>
                        <select class="form-control" id="h_kodeUkuranKontainer" data-h="kodeUkuranKontainer"></select>

                        <label for="h_kodeTipeKontainer">Tipe</label>
                        <select class="form-control" id="h_kodeTipeKontainer" data-h="kodeTipeKontainer"></select>

                        <label for="h_kodeJenisKontainer" class="bs-wrap">Jenis Muatan</label>
                        <select class="form-control" id="h_kodeJenisKontainer" data-h="kodeJenisKontainer">
                            <option value="">- Pilih -</option>
                            @foreach ($kode['jenisMuatan'] as $k => $v)
                                <option value="{{ $k }}">{{ $k }} - {{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="bc-info mt-2">Peti kemas hanya dikirim ke CEISA bila Nomor Kontainer terisi; Ukuran dan Tipe wajib bila Nomor Kontainer diisi. Kemasan dikirim bila Jumlah atau Jenis Kemasan terisi.</div>
                </div>

                {{-- TRANSAKSI --}}
                <div class="tab-pane fade" id="tabTransaksi" role="tabpanel">
                    <div class="bs-form">
                        <label for="h_kodeValuta">23. Valuta</label>
                        <input type="text" class="form-control" id="h_kodeValuta" data-h="kodeValuta" list="dlValuta" maxlength="20">

                        <label for="h_ndpbm">24. NDPBM</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_ndpbm" data-h="ndpbm" oninput="hitungSemua()">

                        <label for="h_kodeIncoterm">Incoterm</label>
                        <input type="text" class="form-control" id="h_kodeIncoterm" data-h="kodeIncoterm" list="dlIncoterm" maxlength="20">

                        <label for="h_fob">25. FOB</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_fob" data-h="fob">

                        <label for="h_freight">26. Freight</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_freight" data-h="freight">

                        <label for="h_asuransi">27. Asuransi</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_asuransi" data-h="asuransi">

                        <label for="h_kodeAsuransi" class="bs-wrap">Jenis Asuransi</label>
                        <select class="form-control" id="h_kodeAsuransi" data-h="kodeAsuransi">
                            @foreach ($kode['asuransi'] as $k => $v)
                                <option value="{{ $k }}">{{ $k }} - {{ $v }}</option>
                            @endforeach
                        </select>

                        <label for="h_cif">28. Nilai CIF</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan bc-tetap" id="h_cif" data-h="cif" disabled>

                        <label for="h_cifRupiah">CIF Rupiah</label>
                        <input type="text" class="form-control text-right bc-tetap" id="h_cifRupiah" disabled>

                        <label for="h_bruto">31. Bruto (Kg)</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" id="h_bruto" data-h="bruto">

                        <label for="h_netto">32. Netto (Kg)</label>
                        <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan bc-tetap" id="h_netto" data-h="netto" disabled>
                    </div>
                    <div class="bc-info mt-2">NDPBM terisi dari master valuta (DBVALAS) bila kode valuta ada di sana; bisa diubah. CIF dan Netto = jumlah dari semua barang. Saat dikirim, kode valuta DBVALAS diterjemahkan ke kode ISO (mis. RM menjadi MYR, EURO menjadi EUR; daftarnya di config valuta_iso).</div>
                </div>

                {{-- BARANG --}}
                <div class="tab-pane fade" id="tabBarang" role="tabpanel">
                    <div class="bc-toolbar-koleksi">
                        <span class="bc-info" id="infoBarang"></span>
                        <button type="button" class="btn btn-chip-biru btn-sm bc-edit" onclick="bukaModal('barang', null)">Tambah Barang</button>
                    </div>
                    <div class="tb-report">
                        <div class="table-outer">
                            <div class="table-wrap" style="max-height:45vh;">
                                <table class="tb" id="tblBarang">
                                    <thead>
                                        <tr><th>No</th><th>Kode Barang</th><th>Uraian</th><th>Pos Tarif</th><th>Jumlah</th><th>Satuan</th><th>Netto</th><th>CIF</th><th>CIF Rupiah</th><th>Action</th></tr>
                                    </thead>
                                    <tbody id="tblBarangData"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="bc-judul">Pungutan</div>
                    <div class="bc-ringkas">
                        <div>Bea Masuk (BM)<b id="pgBM">0</b></div>
                        <div>PPN<b id="pgPPN">0</b></div>
                        <div>PPh<b id="pgPPH">0</b></div>
                        <div>Ditangguhkan (BM)<b id="pgTangguh">0</b></div>
                        <div>Tidak Dipungut (PPN + PPh)<b id="pgTidak">0</b></div>
                    </div>
                    <div class="bc-info mt-2">Hitungan layar saja (dibulatkan ke atas per 1.000); CEISA menerima tarif tetap sesuai program lama.</div>
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
                        <input type="text" class="form-control" id="h_jabatanTtd" data-h="jabatanTtd" maxlength="50">
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End pageForm -->

    {{-- ===================== MODAL (di luar .tb-report) ===================== --}}

    <!-- modal isi PO (Detail Outstanding) -->
    <div class="modal fade" id="mdlPo" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="poJudul">PO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="bc-info mb-2" id="poInfo"></div>
                    <div class="tb-report">
                        <div class="table-outer">
                            <div class="table-wrap" style="max-height:50vh;">
                                <table class="tb">
                                    <thead>
                                        <tr><th>No</th><th>Kode Barang</th><th>Uraian</th><th>Jumlah</th><th>Satuan</th><th>CIF (valuta PO)</th><th>Netto</th></tr>
                                    </thead>
                                    <tbody id="tblPo"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

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
                            <input type="text" class="form-control" data-f="kodeBarang" maxlength="25">

                            <label>Uraian</label>
                            <input type="text" class="form-control" data-f="uraian" maxlength="100">

                            <label>Jumlah Satuan</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="jumlahSatuan">

                            <label>Satuan</label>
                            <input type="text" class="form-control" data-f="kodeSatuanBarang" list="dlSatuan" maxlength="20">

                            <label>Netto (Kg)</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="netto">

                            <label>CIF (valuta)</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan" data-f="cif">

                            <label>CIF Rupiah</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan bc-tetap" data-f="cifRupiah" disabled>

                            <label class="bs-wrap">Kategori Barang</label>
                            <input type="text" class="form-control" data-f="kodeKategoriBarang" list="dlKategori" maxlength="20">

                            <label class="bs-wrap">Negara Asal</label>
                            <input type="text" class="form-control" data-f="kodeNegaraAsal" list="dlNegara" maxlength="5">

                            <label>BM (%)</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan bc-tetap" data-f="persenBM" disabled>

                            <label>PPN (%)</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan bc-tetap" data-f="persenPPN" disabled>

                            <label>PPh (%)</label>
                            <input type="text" inputmode="decimal" class="form-control text-right bc-ribuan bc-tetap" data-f="persenPPh" disabled>
                        </div>
                        <div class="bc-info mt-2">BM / PPN / PPh terisi otomatis saat Pos Tarif dipilih dari daftar HS. CIF Rupiah = CIF x NDPBM header.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-chip-biru bc-edit" onclick="simpanModal('barang')">Simpan</button>
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
                            <input type="text" class="form-control" data-f="nomorDokumen" maxlength="100">

                            <label>Tanggal</label>
                            <input type="date" class="form-control" data-f="tanggalDokumen">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-chip-biru bc-edit" onclick="simpanModal('dokumen')">Simpan</button>
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
        const KODE_KANTOR_BONGKAR = @json($kodeKantorBongkar);
        const ENTITAS = @json($entitas);
        // Master valas (DBVALAS): kurs dipakai sebagai isian awal NDPBM.
        const VALUTA = @json($valuta);
        // Master Ukuran / Tipe Kontainer: [{kode, nama}]; yang dikirim ke CEISA hanya kodenya.
        const UKURAN_KONTAINER = @json($ukuranKontainer);
        const TIPE_KONTAINER = @json($tipeKontainer);
        // Master Jenis Kemasan (JENISKEMASAN): [{kode, nama}]
        const JENIS_KEMASAN = @json($lookup['kemasan']);
        // Master Jenis Dokumen (JENISDOKUMEN): [{kode, nama}]
        const JENIS_DOKUMEN = @json($lookup['jenisDokumen']);
        // Dua dokumen pertama bersifat tetap (skema resmi CEISA per posisi): baris 1 = Invoice (380), baris 2 = BL / AWB
        // (705 / 740). Tidak bisa dihapus; baris ke-3 dan seterusnya bebas jenisnya.
        const DOK_POSISI = [
            [{ kode: '380', nama: 'INVOICE' }],
            [{ kode: '705', nama: 'B/L' }, { kode: '740', nama: 'AWB' }],
        ];
        const KODE = @json($kode);

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
        let outRows = [];
        let pageSize = 10;
        let fokusId = '';

        var g_href = 'documentbc23';
        var g_modeReport = '1';
        var gcart_header = [];
        var gsum_issubtotal = 0;
        var gsum_isgrandtotal = 0;
        var gct_desimal_max = 4;

        function setDefaultHeader() {
            // [ field, label, visible, type, total, decimals ]
            gcart_header = [
                ['revDoc', 'Rev. Doc.', 1, 'varchar', 0, 0],
                ['nomorAju', 'Nomor AJU', 1, 'varchar', 0, 0],
                ['tanggalTtd', 'Tanggal', 1, 'date', 0, 0],
                ['namaEntitas2', 'Pemasok', 1, 'varchar', 0, 0],
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

        function hariIni() {
            const d = new Date();
            const dua = n => String(n).padStart(2, '0');
            return d.getFullYear() + '-' + dua(d.getMonth() + 1) + '-' + dua(d.getDate());
        }

        function pesanError(err, bawaan) {
            return (err && err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : bawaan;
        }

        /* ---------------------------------------------------------------- list */
        function tabelAktif() {
            return $('#tabTransaksiList').hasClass('active') ? '#tabel' : '#tabel2';
        }

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

        // Kotak cari dan "Tampilkan" milik toolbar bersama; berlaku untuk tabel pada tab yang aktif.
        function ikatSearch() {
            let input = document.getElementById('searchBox2');
            if (!input || input.dataset.rtBound) {
                return;
            }
            input.dataset.rtBound = '1';

            input.addEventListener('input', function() {
                terapkanCari();
            });
        }

        function terapkanCari() {
            const input = document.getElementById('searchBox2');
            const t = tabelAktif();
            if (input && $.fn.DataTable.isDataTable(t)) {
                $(t).DataTable().search(input.value).draw();
            }
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
                ['#tabel2', '#tabel'].forEach(function(t) {
                    if ($.fn.DataTable.isDataTable(t)) {
                        $(t).DataTable().page.len(pageSize).draw();
                    }
                });
            });
        }

        function ikatPeriode() {
            let awal = document.getElementById('inputDate1');
            let akhir = document.getElementById('inputDate2');
            if (!awal || !akhir || awal.dataset.rtBound) {
                return;
            }
            awal.dataset.rtBound = '1';

            let onUbah = function() {
                if (!awal.value || !akhir.value) {
                    return;
                }
                if (awal.value > akhir.value) {
                    alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir');
                    return;
                }
                reloadData();
            };
            awal.addEventListener('change', onUbah);
            akhir.addEventListener('change', onUbah);
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
            let wrap = document.querySelector('#pageHome .tab-pane.active .po-table-wrap');
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

        function opsiTabel() {
            return {
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
            };
        }

        /* ---- Outstanding (PO) ---- */
        function renderOutstanding() {
            if ($.fn.DataTable.isDataTable('#tabel2')) {
                $('#tabel2').DataTable().destroy();
            }

            let html = '';
            outRows.forEach(function(r) {
                let aksi = '<button type="button" class="btn btn-warning btn-sm" title="Detail PO" onclick="buttonDetailPo(\'' + esc(r.nobukti) + '\')"><i class="bi bi-info"></i></button>';
                if (AKSES.tambah) {
                    aksi += '<button type="button" class="btn btn-success btn-sm" title="Buat BC 2.3" onclick="buttonAddDariPo(\'' + esc(r.nobukti) + '\')"><i class="bi bi-plus-lg"></i></button>';
                }
                html += '<tr><td class="text-center"><div class="po-aksi-wrap">' + aksi + '</div></td><td>' + esc(r.nobukti) + '</td><td>' +
                    fmtYMD(r.tanggal) + '</td><td>' + esc(r.kodeSupp + ' - ' + r.namaSupp) + '</td><td>' + esc(r.negara) + '</td><td>' +
                    esc(r.kodeValuta) + '</td><td class="text-right">' + fmtNum(r.jumlahBaris) + '</td></tr>';
            });
            document.getElementById('tabel2_data').innerHTML = html;
            $('#tabel2').DataTable(opsiTabel());
            terapkanCari();
            aturTinggiTabel();
        }

        // Lapisan "Memuat data..." di atas tab selama datanya diambil dan tabelnya digambar.
        // Dihitung per tab supaya dua pemuatan yang tumpang tindih tidak saling mematikan lapisan.
        const memuatJml = {};

        function tampilMemuat(idPane, tampil) {
            memuatJml[idPane] = Math.max(0, (memuatJml[idPane] || 0) + (tampil ? 1 : -1));
            $(idPane).children('.bc-memuat').remove();
            $(idPane).toggleClass('bc-pane-memuat', memuatJml[idPane] > 0);
            if (memuatJml[idPane] > 0) {
                $(idPane).append('<div class="bc-memuat"><span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span></div>');
            }
        }

        function reloadOutstanding() {
            tampilMemuat('#tabOutstanding', true);
            $.ajax({
                url: "{!! url('documentbc23loadoutstanding') !!}",
                type: "get",
                success: function(res) {
                    outRows = res || [];
                    renderOutstanding();
                },
                error: function(err) {
                    alertify.error(pesanError(err, 'Gagal memuat daftar PO'));
                },
                complete: function() {
                    tampilMemuat('#tabOutstanding', false);
                }
            });
        }

        /* ---- Transaksi ---- */
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

            $('#tabel').DataTable(opsiTabel());

            rtPindahBar('rtBar', 'tabel');
            terapkanCari();

            if (fokusId) {
                pindahKeHalamanBaris(fokusId);
                fokusId = '';
            }

            aturTinggiTabel();
        }

        function reloadData(_fokusId) {
            tampilMemuat('#tabTransaksiList', true);
            $.ajax({
                url: "{!! url('documentbc23loadall') !!}",
                type: "get",
                data: {
                    date1: $('#inputDate1').val(),
                    date2: $('#inputDate2').val()
                },
                success: function(res) {
                    lastRows = res || [];
                    fokusId = _fokusId || '';
                    renderTabel();
                },
                error: function(err) {
                    alertify.error(pesanError(err, 'Gagal memuat daftar dokumen'));
                },
                complete: function() {
                    tampilMemuat('#tabTransaksiList', false);
                }
            });
        }

        // Tab beranda: periode hanya untuk Transaksi; cari/tampilkan berlaku ke tabel tab aktif.
        $(document).on('shown.bs.tab', '#homeTabs a[data-toggle="tab"]', function() {
            $('#wrapPeriode').toggle($('#tabTransaksiList').hasClass('active'));
            const t = tabelAktif();
            if ($.fn.DataTable.isDataTable(t)) {
                $(t).DataTable().columns.adjust();
            }
            terapkanCari();
            aturTinggiTabel();
        });

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
        let nobuktiSumber = '';
        let state = {};
        let editIdx = {}; // index baris yang sedang diedit di modal per koleksi (null = baru)

        function stateKosong() {
            return { barang: [], dokumen: [] };
        }

        function isiEntitas(n, id) {
            const e = ENTITAS.find(x => String(x.id) === String(id)) || null;
            const isi = (kolom, nilai) => $('#h_' + kolom + n).val(e ? (nilai || '') : '');
            if (Number(n) !== 2) {
                isi('nomorIdentitasEntitas', e && e.nomorIdentitas);
            } else {
                $('#h_kodeNegaraEntitas2').val(e ? (e.kodeNegara || '') : '');
            }
            isi('namaEntitas', e && e.namaEntitas);
            isi('alamatEntitas', e && e.alamatEntitas);
            if (Number(n) === 1) {
                $('#h_nomorIjinEntitas1').val(e ? (e.nomorIjinEntitas || '') : '');
                $('#h_nibEntitas1').val(e ? (e.nibEntitas || '') : '');
            }
        }

        $(document).on('change', '.bc-entitas', function() {
            isiEntitas($(this).data('n'), $(this).val());
        });

        // Valuta yang ada di master valas (DBVALAS) mengisi NDPBM dengan kursnya (masih bisa diubah).
        $(document).on('change', '#h_kodeValuta', function() {
            const v = VALUTA.find(x => String(x.kode).toLowerCase() === String($(this).val()).trim().toLowerCase());
            if (v && angka(v.kurs) > 0) {
                setRibuan('#h_ndpbm', angka(v.kurs));
                hitungSemua();
            }
        });

        // Pembulatan ke atas kelipatan 1.000 (utuh bila sudah kelipatan 1.000), via bilangan bulat sen.
        function bulatSeribu(x) {
            const n = Math.round(x * 100);
            const sisa = n % 100000;
            return sisa === 0 ? n / 100 : (n - sisa + 100000) / 100;
        }

        // BM = %BM x CIF Rp; PPN / PPh = % x (CIF Rp + BM) - hitungan layar, tidak dikirim ke CEISA.
        function hitungPajak(r) {
            const cif = angka(r.cifRupiah);
            const bm = bulatSeribu(angka(r.persenBM) / 100 * cif);
            r.nilaiBM = bm;
            r.nilaiPPN = bulatSeribu(angka(r.persenPPN) / 100 * (cif + bm));
            r.nilaiPPh = bulatSeribu(angka(r.persenPPh) / 100 * (cif + bm));
        }

        // NDPBM / CIF / netto berubah: CIF Rp tiap barang = CIF x NDPBM, header CIF dan Netto = jumlah barang.
        function hitungSemua() {
            const ndpbm = angka($('#h_ndpbm').val());
            let cif = 0;
            let netto = 0;
            state.barang.forEach(function(r) {
                r.ndpbm = ndpbm;
                r.cifRupiah = angka(r.cif) * ndpbm;
                hitungPajak(r);
                cif += angka(r.cif);
                netto += angka(r.netto);
            });
            setRibuan('#h_cif', Math.round(cif * 10000) / 10000);
            setRibuan('#h_netto', Math.round(netto * 10000) / 10000);
            tampilCifRupiah();
            renderBarang();
        }

        function tampilCifRupiah() {
            const nilai = Math.round(angka($('#h_ndpbm').val()) * angka($('#h_cif').val()) * 100) / 100;
            $('#h_cifRupiah').val(nilai.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }

        // Isi select Ukuran / Tipe dari master ("kode - nama"). Kode lama yang sudah tidak ada di master
        // tetap ditampilkan supaya tidak hilang saat dokumen lama disimpan ulang.
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

        // Tambah: header kembali ke nilai awal (bukan dikosongkan semua), koleksi dikosongkan.
        function resetHeader() {
            $('[data-h]').each(function() {
                if (this.tagName === 'SELECT') {
                    this.selectedIndex = 0;
                } else {
                    $(this).val('');
                }
            });
            opsiMaster('#h_kodeUkuranKontainer', UKURAN_KONTAINER, '');
            opsiMaster('#h_kodeTipeKontainer', TIPE_KONTAINER, '');
            opsiMaster('#h_kodeJenisKemasan', JENIS_KEMASAN, '');
            $('#h_kodeKantor').val(KODE_KANTOR);
            $('#h_kodeKantorBongkar').val(KODE_KANTOR_BONGKAR);
            $('#h_kodeCaraAngkut').val('1');
            $('#h_kodeAsuransi').val('LN');
            $('#h_tanggalBc11').val(hariIni());
            $('#h_tanggalTtd').val(hariIni());
            [1, 2, 3].forEach(n => isiEntitas(n, ''));
        }

        function isiHeader(d) {
            opsiMaster('#h_kodeUkuranKontainer', UKURAN_KONTAINER, d.kodeUkuranKontainer);
            opsiMaster('#h_kodeTipeKontainer', TIPE_KONTAINER, d.kodeTipeKontainer);
            opsiMaster('#h_kodeJenisKemasan', JENIS_KEMASAN, d.kodeJenisKemasan);
            $('[data-h]').each(function() {
                const v = d[$(this).data('h')];
                $(this).val(v === null || v === undefined ? '' : String(v));
                if ($(this).hasClass('bc-ribuan')) {
                    formatRibuanKetik(this);
                }
            });
            tampilCifRupiah(); // Q-t: CIF Rupiah langsung terhitung saat dokumen dibuka
        }

        // Detail = read-only (semua isian dimatikan, tombol ubah disembunyikan).
        function aturMode() {
            const baca = mode === 'detail';
            $('#pageForm').find('input, select, textarea').not('.bc-tetap').prop('disabled', baca);
            $('#pageForm .bc-edit').toggle(!baca);
            const judul = {
                add: 'Tambah Dokumen BC 2.3',
                edit: 'Edit Dokumen BC 2.3',
                detail: 'Detail Dokumen BC 2.3'
            }[mode];
            $('#formJudul').text(judul + (nobuktiSumber ? ' - PO ' + nobuktiSumber : ''));
            $('#h_revDoc').val(nobuktiSumber);
        }

        function bukaForm() {
            renderSemua();
            aturMode();
            $('#bcTabs a:first').tab('show');
            showPage('pageForm');
        }

        // Tambah dokumen dari PO: header / barang diisi dari DBPO + DBPODET, sisanya diisi user.
        function buttonAddDariPo(nobukti) {
            if (!AKSES.tambah) {
                alertify.warning('No access');
                return;
            }
            $.ajax({
                url: "{!! url('documentbc23spsumber') !!}",
                type: "get",
                data: { nobukti },
                success: function(res) {
                    if (res.po.dipakai) {
                        alertify.warning('PO ' + esc(nobukti) + ' sudah dibuatkan BC 2.3.');
                        reloadOutstanding();
                        return;
                    }
                    if (!res.barang.length) {
                        alertify.warning('PO ' + esc(nobukti) + ' tidak punya baris barang.');
                        return;
                    }
                    mode = 'add';
                    docId = null;
                    nobuktiSumber = res.po.nobukti;
                    state = stateKosong();
                    resetHeader();
                    $('#h_kodeValuta').val(res.header.kodeValuta);
                    setRibuan('#h_ndpbm', res.header.ndpbm || '');
                    if (res.header.selectEntitas2) {
                        $('#h_selectEntitas2').val(String(res.header.selectEntitas2));
                        isiEntitas(2, res.header.selectEntitas2);
                    }
                    state.barang = res.barang.map(function(b, i) {
                        return Object.assign({ seriBarang: i + 1, posTarif: '', kodeKategoriBarang: '', kodeNegaraAsal: '', persenBM: 0, persenPPN: 0, persenPPh: 0 }, b);
                    });
                    state.dokumen = DOK_POSISI.map((opsi, i) => ({
                        seriDokumen: i + 1, kodeDokumen: opsi[0].kode, nomorDokumen: '', tanggalDokumen: hariIni()
                    }));
                    bukaForm();
                    hitungSemua();
                },
                error: function(err) {
                    alertify.error(pesanError(err, 'Gagal memuat PO'));
                }
            });
        }

        // Detail PO (baca saja) dari Outstanding.
        function buttonDetailPo(nobukti) {
            $.ajax({
                url: "{!! url('documentbc23spsumber') !!}",
                type: "get",
                data: { nobukti },
                success: function(res) {
                    const po = res.po;
                    $('#poJudul').text('PO ' + po.nobukti);
                    $('#poInfo').text(fmtYMD(po.tanggal) + ' - ' + po.kodeSupp + ' ' + po.namaSupp + ' (' + po.negara + ') - ' +
                        po.kodeValuta + (po.keterangan ? ' - ' + po.keterangan : ''));
                    const html = res.barang.map(function(b, i) {
                        return '<tr><td>' + (i + 1) + '</td><td>' + esc(b.kodeBarang) + '</td><td>' + esc(b.uraian) + '</td><td class="text-right">' +
                            fmtNum(b.jumlahSatuan) + '</td><td>' + esc(b.kodeSatuanBarang) + '</td><td class="text-right">' + fmtNum(b.cif) +
                            '</td><td class="text-right">' + fmtNum(b.netto) + '</td></tr>';
                    }).join('');
                    $('#tblPo').html(html || barisKosong(7));
                    $('#mdlPo').modal('show');
                },
                error: function(err) {
                    alertify.error(pesanError(err, 'Gagal memuat PO'));
                }
            });
        }

        function muatDokumen(id, _mode) {
            $.ajax({
                url: "{!! url('documentbc23spdetail') !!}",
                type: "get",
                data: { id },
                success: function(res) {
                    if (Number(res.sent) === 1 && _mode === 'edit') {
                        alertify.warning('Dokumen sudah dikirim ke CEISA, tidak bisa diubah');
                        _mode = 'detail';
                    }
                    mode = _mode;
                    docId = Number(res.id);
                    nobuktiSumber = res.nobukti;
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
            alertify.confirm('Hapus Dokumen', 'Apakah yakin ingin menghapus dokumen BC 2.3 ' + esc(r.nomorAju || r.revDoc || ('#' + id)) + ' ? PO-nya kembali ke Outstanding.',
                function() {
                    $.ajax({
                        url: "{!! url('documentbc23spdelete') !!}",
                        type: "post",
                        data: {
                            _token: $('#_token').val(),
                            id
                        },
                        success: function(res) {
                            alertify.success(res.message);
                            reloadOutstanding();
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
            if (!state.barang.length) {
                alertify.error('Barang belum ada (minimal 1 baris).');
                return;
            }
            if ($('#h_nomorKontainer').val().trim() && (!$('#h_kodeUkuranKontainer').val() || !$('#h_kodeTipeKontainer').val())) {
                alertify.error('Ukuran dan Tipe kontainer harus dipilih bila Nomor Kontainer diisi.');
                return;
            }

            const header = {};
            $('[data-h]').each(function() {
                header[$(this).data('h')] = $(this).hasClass('bc-ribuan') ? $(this).val().replace(/,/g, '') : $(this).val();
            });
            // Netto dan CIF header = jumlah barang (kolom itu dimatikan, tetap dikirim).
            const data = { header, nobukti: nobuktiSumber };
            if (mode === 'edit') {
                data.id = docId;
            }
            Object.keys(state).forEach(function(k) {
                data[k] = state[k];
            });

            $.ajax({
                url: mode === 'edit' ? "{!! url('documentbc23spedit') !!}" : "{!! url('documentbc23spadd') !!}",
                type: "post",
                data: {
                    _token: $('#_token').val(),
                    data: JSON.stringify(data)
                },
                success: function(res) {
                    alertify.success(res.message);
                    buttonCloseForm();
                    reloadOutstanding();
                    $('#homeTabs a[href="#tabTransaksiList"]').tab('show');
                    reloadData(res.id);
                },
                error: function(err) {
                    alertify.error(pesanError(err, 'Gagal menyimpan dokumen'));
                }
            });
        }

        /* ============================================================================
         * Koleksi anak - semua di memori; nomor baris = posisi (seri diisi ulang server).
         * ========================================================================= */
        const MODAL = {
            barang: '#mdlBarang',
            dokumen: '#mdlDokumen',
        };

        // Tombol baris tabel item: markup & kelas sama dengan baris item di gudang/permintaanpemakaian
        // (.btn-action-sm + .btn-action-success / -danger, gayanya dari po-table-header.css di .po-form-page).
        function aksiBaris(nama, idx) {
            if (mode === 'detail') {
                return '<div class="text-center text-nowrap"><button type="button" class="btn-action-sm btn-action-warning" title="Lihat" onclick="event.stopPropagation(); bukaModal(\'' +
                    nama + '\', ' + idx + ')"><i class="bi bi-info"></i></button></div>';
            }
            const tetap = nama === 'dokumen' && idx < DOK_POSISI.length;
            return '<div class="text-center text-nowrap">' +
                '<button type="button" class="btn-action-sm btn-action-success" title="Edit" onclick="event.stopPropagation(); bukaModal(\'' + nama + '\', ' + idx + ')"><i class="bi bi-pen"></i></button>' +
                (tetap ? '' : '<button type="button" class="btn-action-sm btn-action-danger" title="Hapus" onclick="event.stopPropagation(); hapusBaris(\'' + nama + '\', ' + idx + ')"><i class="bi bi-trash"></i></button>') +
                '</div>';
        }

        function barisKosong(kolom, teks) {
            return '<tr class="bc-kosong"><td colspan="' + kolom + '">' + esc(teks || 'Belum ada data') + '</td></tr>';
        }

        function renderDokumen() {
            let html = state.dokumen.map(function(r, i) {
                return '<tr><td>' + (i + 1) + '</td><td>' + esc(r.kodeDokumen) + '</td><td>' + esc(r.nomorDokumen) +
                    '</td><td>' + fmtYMD(r.tanggalDokumen) + '</td><td>' + aksiBaris('dokumen', i) + '</td></tr>';
            }).join('');
            $('#tblDokumen').html(html || barisKosong(5));
            $('#infoDokumen').text(state.dokumen.length + ' dokumen');
        }

        function renderBarang() {
            let html = state.barang.map(function(r, i) {
                return '<tr><td>' + (i + 1) + '</td><td>' + esc(r.kodeBarang) + '</td><td>' + esc(r.uraian) + '</td><td>' + esc(r.posTarif) +
                    '</td><td class="text-right">' + fmtNum(r.jumlahSatuan) + '</td><td>' + esc(r.kodeSatuanBarang) +
                    '</td><td class="text-right">' + fmtNum(r.netto) + '</td><td class="text-right">' + fmtNum(r.cif) +
                    '</td><td class="text-right">' + fmtNum(r.cifRupiah) + '</td><td>' + aksiBaris('barang', i) + '</td></tr>';
            }).join('');
            $('#tblBarangData').html(html || barisKosong(10));
            $('#infoBarang').text(state.barang.length + ' barang');

            const jml = k => state.barang.reduce((s, r) => s + angka(r[k]), 0);
            $('#pgBM').text(fmtNum(jml('nilaiBM')));
            $('#pgPPN').text(fmtNum(jml('nilaiPPN')));
            $('#pgPPH').text(fmtNum(jml('nilaiPPh')));
            $('#pgTangguh').text(fmtNum(jml('nilaiBM')));
            $('#pgTidak').text(fmtNum(jml('nilaiPPN') + jml('nilaiPPh')));
        }

        function renderSemua() {
            renderDokumen();
            renderBarang();
        }

        function judulModal(nama, idx) {
            const label = { barang: 'Barang', dokumen: 'Dokumen' };
            return (mode === 'detail' ? '' : (idx === null ? 'Tambah ' : 'Edit ')) + label[nama];
        }

        function nilaiAwal(nama) {
            if (nama === 'barang') {
                return { seriBarang: state.barang.length + 1, cif: 0, persenBM: 0, persenPPN: 0, persenPPh: 0, cifRupiah: 0 };
            }
            return { tanggalDokumen: hariIni() };
        }

        function bukaModal(nama, idx) {
            if (mode === 'detail' && idx === null) {
                return;
            }
            const modal = $(MODAL[nama]);
            editIdx[nama] = idx;

            const row = idx === null ? nilaiAwal(nama) : state[nama][idx];
            if (nama === 'dokumen') {
                // Posisi baris menentukan jenis yang boleh dipilih (dua pertama: Invoice, lalu BL / AWB).
                const pos = idx === null ? state.dokumen.length : idx;
                const batas = DOK_POSISI[pos] || null;
                if (batas && !row.kodeDokumen) {
                    row.kodeDokumen = batas[0].kode;
                }
                opsiMaster('#f_dokumen_kode', batas || JENIS_DOKUMEN, row.kodeDokumen || '');
            }
            modal.find('[data-f]').each(function() {
                const v = row[$(this).data('f')];
                $(this).val(v === null || v === undefined ? '' : String(v));
                if ($(this).hasClass('bc-ribuan')) {
                    formatRibuanKetik(this);
                }
            });
            if (nama === 'barang') {
                modal.find('[data-f="seriBarang"]').val(idx === null ? state.barang.length + 1 : idx + 1);
            }

            const baca = mode === 'detail';
            modal.find('input, select, textarea').not('.bc-tetap').not('[type=hidden]').prop('disabled', baca);
            modal.find('.bc-edit').toggle(!baca);
            modal.find('.bc-modal-judul').text(judulModal(nama, idx));
            modal.modal('show');
        }

        function bacaModal(nama) {
            const row = {};
            $(MODAL[nama]).find('[data-f]').each(function() {
                row[$(this).data('f')] = $(this).hasClass('bc-ribuan') ? $(this).val().replace(/,/g, '') : $(this).val();
            });
            return row;
        }

        // Simpan baris ke memori.
        function simpanModal(nama) {
            const row = bacaModal(nama);
            const idx = editIdx[nama];

            if (nama === 'dokumen' && !row.kodeDokumen) {
                alertify.error('Jenis dokumen harus dipilih.');
                return;
            }

            if (idx === null || idx === undefined) {
                state[nama].push(row);
            } else {
                state[nama][idx] = Object.assign({}, state[nama][idx], row);
            }
            seriUlang(nama);
            $(MODAL[nama]).modal('hide');

            if (nama === 'barang') {
                hitungSemua();
            } else {
                renderSemua();
            }
        }

        function seriUlang(nama) {
            const kunci = { barang: 'seriBarang', dokumen: 'seriDokumen' }[nama];
            if (kunci) {
                state[nama].forEach((r, i) => r[kunci] = i + 1);
            }
        }

        function hapusBaris(nama, idx) {
            if (nama === 'dokumen' && idx < DOK_POSISI.length) {
                alertify.warning('Dokumen Invoice dan BL/AWB wajib ada, tidak bisa dihapus.');
                return;
            }
            state[nama].splice(idx, 1);
            seriUlang(nama);
            if (nama === 'barang') {
                hitungSemua();
            } else {
                renderSemua();
            }
        }

        // CIF barang diubah di modal: CIF Rupiah ikut berubah (pratinjau).
        $(document).on('input', '#mdlBarang [data-f="cif"]', function() {
            setRibuan('#mdlBarang [data-f="cifRupiah"]', angka($(this).val()) * angka($('#h_ndpbm').val()));
        });

        // Pos tarif diketik manual (bukan dipilih dari daftar): tarif lama dikosongkan supaya tidak basi.
        $(document).on('input', '#f_barang_posTarif', function() {
            ['persenBM', 'persenPPN', 'persenPPh'].forEach(k => $('#mdlBarang [data-f="' + k + '"]').val(0));
        });

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
                    emptyTable: 'Tidak ada data - pos tarif tetap bisa diketik langsung (tarif 0).',
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
                url: "{!! url('documentbc23lisths') !!}",
                type: "get",
                data: { search: $('#tblHs_filter input').val() },
                success: function(res) {
                    hsDt.clear().rows.add(res || []).draw();
                }
            });
        }

        // Memilih HS mengisi pos tarif + tarif BM / PPN / PPh barang (nilai pajak dihitung saat disimpan).
        $(document).on('click', '#tblHsData tr[data-hs]', function() {
            $(hsTarget).val($(this).data('hs'));
            setRibuan('#mdlBarang [data-f="persenBM"]', $(this).data('bm'));
            setRibuan('#mdlBarang [data-f="persenPPN"]', $(this).data('ppn'));
            setRibuan('#mdlBarang [data-f="persenPPh"]', $(this).data('pph'));
            $('#mdlHs').modal('hide');
        });

        // Backdrop modal HS di atas modal barang yang masih terbuka.
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
                (ulang ? 'Dokumen ini sudah pernah terkirim. Kirim ulang ' : 'Kirim dokumen ') + esc(r.nomorAju || r.revDoc || ('#' + id)) + ' ke CEISA?',
                function() {
                    $.ajax({
                        url: "{!! url('documentbc23spkirim') !!}",
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
                url: "{!! url('documentbc23sppreview') !!}",
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
            ikatSearch();
            ikatPanjangHalaman();
            ikatPeriode();
            renderTabel();
            reloadOutstanding();
            reloadData();
            cekStatusBc();
        });
    </script>
@endsection
