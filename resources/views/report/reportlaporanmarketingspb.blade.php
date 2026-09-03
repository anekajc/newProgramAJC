@extends('report.masterreport2')

<style>
    .tb-report .table-wrap {
        min-height: 10vh;
    }
</style>

@include('report.modalMarketingSO')

@section('header2')
    <div class="tb-report main">
        <div class="content">

            <!-- TOOLBAR -->
            <div class="toolbar">
                {{-- <div>
                    <div class="page-title">SPB</div>
                </div> --}}

                <!-- Jenis laporan: Non Outstanding (ke Sp_ReportSPBDet, dua tanggal) atau
                     Outstanding (ke Sp_ReportOutSpbDet, hanya tanggal pertama -- #inputDate2
                     disembunyikan & tidak dikirim; LaporanMarketingOutSPPBController TIDAK
                     diubah, jadi date2 sampai ke SP sebagai NULL apa adanya). -->
                <div class="filter-wrap">
                    <label>Jenis</label>
                    <select class="filter-inp" id="inputMode" onchange="setMode(this.value)">
                        <option value="0">Non Outstanding</option>
                        <option value="1">Outstanding</option>
                    </select>
                </div>

                <!-- Periode (date range) -->
                <div class="filter-wrap">
                    <label id="periodeLabel">Periode</label>
                    <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
                    <span class="filter-sep" id="dateSep">s/d</span>
                    <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
                </div>

                {{-- Search --}}
                <div>
                    <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..."
                        oninput="applyFilters()" style="width:180px">
                </div>

                <!-- Actions: search + filter modal + customize + tampilkan + export -->
                <div class="action-group">
                    {{-- Dibuka lewat plugin jQuery (Bootstrap 4), BUKAN data-bs-toggle (Bootstrap 5).
             Halaman ini memuat dua Bootstrap; karena jQuery baru dimuat SESUDAH bundle
             BS5, $.fn.modal dipegang BS4. applyModalFilter() menutup modal ini dengan
             $('#modalFilter').modal('hide'), jadi pembukanya harus memakai API yang sama.
             JANGAN pasang data-toggle dan data-bs-toggle bersamaan. --}}
                    <button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    {{-- <button class="btn-load" onclick="doShowFormCustomizeTable()" title="Customize Table"><i class="fas fa-cog"></i> Customize Table</button> --}}
                    <button class="btn-load" onclick="makeTable('REPORT')" title="Tampilkan laporan"><i
                            class="fas fa-check"></i> Tampilkan</button>
                    <div class="export-wrap" id="exportWrap">
                        <button class="export-btn" onclick="toggleExport()"><i class="bi bi-arrow-down"></i> Export <i
                                class="bi bi-caret-down-fill"></i></button>
                        <div class="export-drop" id="exportDrop">
                            <div class="export-opt" onclick="doExport('Excel')"><i class="bi bi-journals text-success"></i>
                                Ekspor ke <span class="ext">XLSX</span></div>
                            <div class="export-opt" onclick="doExport('CSV')"><i class="bi bi-clipboard"></i> Ekspor ke
                                <span class="ext">CSV</span>
                            </div>
                            <div class="export-opt" onclick="doExport('Print')"><i
                                    class="bi bi-printer-fill text-warning"></i> Cetak Laporan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bar kolom tersembunyi + Tampilan (diisi oleh report-table.js / ReportTable) -->
            <div id="rtBar"></div>

            <!-- TABLE -->
            <div class="table-outer">
                <div class="table-wrap">
                    <table class="tb" id="mainTable">
                        <thead>
                            <tr>
                                <th>No Bukti</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr class="empty-row">
                                <td>Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <span id="footerLabel">Belum ada data dimuat</span>
                </div>
            </div>

            <div class="rt-hint">
                <i class="bi bi-info-circle"></i>
                Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk sembunyikan
                kolom atau atur desimal &amp; total.
            </div>

        </div><!-- /content -->

        <!-- TOAST -->
        <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
    </div><!-- /tb-report -->

    {{-- Modal DILETAKKAN DI LUAR .tb-report supaya reset `.tb-report *{margin:0;padding:0}`
     di report-table.css tidak merusak padding/margin modal Bootstrap. --}}

    <!-- modal filter -->
    <div class="modal fade rt-filter" id="modalFilter">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-filter"></i>
                        Filter Laporan
                        <span class="rt-active-badge" id="filterBadge">0 aktif</span>
                    </h5>
                    {{-- data-dismiss (BS4) = yang benar-benar menutup, karena modal ini dibuka lewat
             $.fn.modal milik BS4. data-bs-dismiss dibiarkan untuk jaga-jaga. --}}
                    <button type="button" class="btn-close" aria-label="Close" data-dismiss="modal" data-bs-dismiss="modal"
                        onclick="$('#modalFilter').modal('hide')"></button>
                </div>

                <div class="modal-body">

                    <div class="rt-section">
                        <div class="rt-group-label">Pengaturan Laporan</div>
                        {{-- Report (Detail/Rekap) TIDAK ada di sini: sudah jadi switcher "Tampilan" di
               bar atas tabel (ReportTable.init views), lihat setReportMode(). Dobel di
               modal ini hanya akan membingungkan (dua kontrol untuk satu setting). --}}
                        <div class="rt-grid-2">
                            <div>
                                <label class="rt-field-label" for="modalOtorisasi">Otorisasi</label>
                                {{-- Nilai = nilai kolom NeedOtorisasi apa adanya (dipakai SP maupun
                                     filter sisi-klien): 0 = semua level otorisasi sudah lengkap
                                     (Sudah), 1 = masih butuh otorisasi (Belum), 2 = semua. --}}
                                <select class="rt-native" id="modalOtorisasi">
                                    <option value="2">Semua</option>
                                    <option value="0">Sudah Otorisasi</option>
                                    <option value="1">Belum Otorisasi</option>
                                </select>
                            </div>
                            {{-- Sp_ReportOutSpbDet tidak punya parameter ini -- hanya berlaku
                                 di mode Non Outstanding. --}}
                            <div id="wrapTerima">
                                <label class="rt-field-label" for="modalTerima">Tgl. Terima</label>
                                <select class="rt-native" id="modalTerima">
                                    <option value="2">Semua</option>
                                    <option value="0">Tgl. Terima</option>
                                    <option value="1">Non Tgl. Terima</option>
                                </select>
                            </div>
                        </div>
                        <div class="rt-grid-2">
                            <div>
                                <label class="rt-field-label" for="modalOrder">Urutkan</label>
                                <select class="rt-native" id="modalOrder">
                                    <option value="N">Nomor Bukti</option>
                                    <option value="B">Nomor Barang</option>
                                    <option value="C">Nomor Customer</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="rt-reset-link" onclick="resetAllFilters()">Reset semua</button>
                    <div class="rt-footer-buttons">
                        <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" data-bs-dismiss="modal"
                            onclick="$('#modalFilter').modal('hide')">Batal</button>
                        <button type="button" class="rt-btn rt-btn-primary"
                            onclick="applyModalFilter()">Terapkan</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- modal filter -->
@endsection

@section('jsreport')
    <script type="text/javascript">
        let globalDate1 = "{!! date('Y-m-d') !!}";
        let globalDate2 = "{!! date('Y-m-d') !!}";
        let globalOtorisasi = "2"; // default: Semua
        let globalOrderBy = "N"; // default: Nomor Bukti
        let globalReportMode = "0"; // default: Detail
        let globalTerima = "2"; // default: Semua
        let globalMode = "0"; // "0" = Non Outstanding (Sp_ReportSPBDet), "1" = Outstanding (Sp_ReportOutSpbDet)

        var jenisreport = 0; // 0 = Detail, 1 = Rekap

        let lastRows = []; // hasil fetch terakhir (dipakai render / export / search)
        let currentGroupby = 'NOBUKTI'; // groupby aktif untuk render ulang saat search

        // Offset mode report Outstanding supaya kolom tersimpan (DBSIMPANHEADER, dikunci per
        // href+reportmode) tidak bentrok dengan mode Non Outstanding di href yang sama.
        const OUT_MODE_OFFSET = 20;

        const reportUrlSpb = "{{ url('laporanmarketingspb_doReport') }}";
        const reportUrlOut = "{{ url('laporanmarketingoutsppb_doReport') }}";

        // Urutkan: Non Outstanding punya 3 opsi (masing-masing mengubah susunan kolom lewat
        // setModeReport()); Outstanding punya 6 (Sp_ReportOutSpbDet mengembalikan field yang
        // sama apa pun Ordr -- lihat komentar di reportmarketingoutsppb.blade.php -- jadi Ordr
        // di sana hanya mengubah currentGroupby/subtotal, bukan kolom).
        const ORDER_OPTIONS_SPB = [{
                value: 'N',
                label: 'Nomor Bukti'
            },
            {
                value: 'B',
                label: 'Nomor Barang'
            },
            {
                value: 'C',
                label: 'Nomor Customer'
            },
        ];
        const ORDER_OPTIONS_OUT = ORDER_OPTIONS_SPB.concat([{
                value: 'S',
                label: 'Sales'
            },
            {
                value: 'HG',
                label: 'Head Group'
            },
            {
                value: 'P',
                label: 'PIC'
            },
        ]);

        // Menulis ulang <option> #modalOrder sesuai mode. Kalau nilai globalOrderBy saat ini
        // tidak ada di daftar mode baru (mis. pindah dari Outstanding 'S'/'HG'/'P' ke Non
        // Outstanding), jatuhkan ke 'N' -- SP_REPORTSPBDet tidak punya kolom untuk itu.
        function renderOrderOptions() {
            const opts = (globalMode === '1') ? ORDER_OPTIONS_OUT : ORDER_OPTIONS_SPB;
            const valid = opts.some(o => o.value === globalOrderBy);
            if (!valid) {
                globalOrderBy = 'N';
            }
            $('#modalOrder').html(opts.map(o => '<option value="' + o.value + '">' + o.label + '</option>')
                .join(''));
            $('#modalOrder').val(globalOrderBy);
        }

        $(document).ready(function() {
            setReportMode(globalReportMode);
            setOtorisasi(globalOtorisasi);
            setTerima(globalTerima);
            renderOrderOptions();
            setOrderBy(globalOrderBy);
            showPeriode();

            // Menu lama boleh mengarahkan ke /laporanmarketingspb?mode=out supaya langsung
            // terbuka di mode Outstanding (lihat rencana retire halaman lama).
            if ("{{ request('mode') }}" === "out") {
                $('#inputMode').val('1');
                setMode('1');
            }

            setDefaultHeader();

            // Header tabel interaktif. "Tampilan" = Report Mode (Detail/Rekap) -- SATU-SATUNYA
            // tempat kontrol ini muncul (tidak diulang di modal Filter). Report Mode hanya
            // mengubah susunan kolom (bukan query), jadi cukup render() ulang -- tidak perlu makeTable().
            ReportTable.init({
                table: '#mainTable',
                bar: '#rtBar',
                onChange: render,
                views: {
                    label: 'Tampilan',
                    options: [{
                            value: '0',
                            label: 'Detail',
                            desc: 'Rincian per baris'
                        },
                        {
                            value: '1',
                            label: 'Rekap',
                            desc: 'Ringkasan per grup'
                        }
                    ],
                    get: function() {
                        return globalReportMode;
                    },
                    set: function(v) {
                        setReportMode(String(v));
                        if (lastRows.length) {
                            render();
                        }
                    }
                }
            });
        });

        // periode
        function showPeriode() {
            globalDate1 = $('#inputDate1').val();
            globalDate2 = $('#inputDate2').val();
        }

        // otorisasi / tgl. terima: filter query, dibaca langsung oleh makeTable()
        function setOtorisasi(val) {
            globalOtorisasi = val;
        }

        function setTerima(val) {
            globalTerima = val;
        }

        // Jenis laporan: "0" Non Outstanding (Sp_ReportSPBDet, dua tanggal) atau "1" Outstanding
        // (Sp_ReportOutSpbDet, HANYA tanggal pertama -- lihat komentar di toolbar).
        function setMode(val) {
            globalMode = val;
            const isOut = (val === '1');

            // date2 tidak dikirim di mode Outstanding -- LaporanMarketingOutSPPBController TIDAK
            // diubah (permintaan eksplisit), jadi tetap dibaca $req->get('date2') apa adanya
            // (jadi NULL di SP kalau tidak dikirim).
            $('#inputDate2').toggle(!isOut);
            $('#dateSep').toggle(!isOut);
            $('#periodeLabel').text(isOut ? 'Per Tanggal' : 'Periode');

            // Tgl. Terima (@tglterima) tidak ada di Sp_ReportOutSpbDet -- lewati di Outstanding.
            $('#wrapTerima').toggle(!isOut);
            if (isOut) {
                $('#modalTerima').val('2');
                setTerima('2');
            }

            renderOrderOptions();

            // Ganti mode tidak langsung fetch ulang -- tabel dikosongkan, user tekan Tampilkan.
            lastRows = [];
            currentGroupby = 'NOBUKTI';
            $('#tableBody').html('<tr class="empty-row"><td>Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>');
            $('#footerLabel').text('Belum ada data dimuat');

            setModeReport();
            updateFilterBadge();
        }

        // order by: ikut menentukan groupby & susunan kolom (lewat setModeReport)
        function setOrderBy(val) {
            globalOrderBy = val;
            setModeReport();
        }

        function setReportMode(val) {
            globalReportMode = val;
            jenisreport = Number(val); // 0 = Detail, 1 = Rekap
            setModeReport();
        }

        /* -- FILTER MODAL -- */

        function updateFilterBadge() {
            let count = 0;
            if ($('#modalOtorisasi').val() !== '2') {
                count++;
            }
            if ($('#modalTerima').val() !== '2') {
                count++;
            }
            // Urutkan: pilihan wajib tanpa nilai netral -> sengaja tidak dihitung
            $('#filterBadge').text(count + ' aktif');
        }

        function resetAllFilters() {
            $('#modalOtorisasi').val('2');
            $('#modalTerima').val('2');
            $('#modalOrder').val('N');
            updateFilterBadge();
        }

        $('#modalFilter').on('show.bs.modal', function() {
            $('#modalOtorisasi').val(globalOtorisasi);
            $('#modalTerima').val(globalTerima);
            $('#modalOrder').val(globalOrderBy);
            updateFilterBadge();
        });

        $('#modalFilter').on('change', 'select.rt-native', updateFilterBadge);

        function applyModalFilter() {
            setOtorisasi($('#modalOtorisasi').val());
            setTerima($('#modalTerima').val());
            if ($('#modalOrder').length) {
                setOrderBy($('#modalOrder').val());
            }

            $('#modalFilter').modal('hide');
        }

        /* -- EXPORT -- */
        function toggleExport() {
            document.getElementById('exportDrop').classList.toggle('open');
        }
        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('exportWrap');
            if (wrap && !wrap.contains(e.target)) {
                document.getElementById('exportDrop').classList.remove('open');
            }
        });

        function doExport(fmt) {
            document.getElementById('exportDrop').classList.remove('open');
            if (fmt === 'Print') {
                window.print();
                return;
            }
            exportDelimited(fmt);
        }

        function exportDelimited(fmt) {
            const cols = gcart_header.filter(c => c[2] === 1);
            const header = cols.map(c => c[1]);
            const body = (lastRows || []).map(r => cols.map(function(c) {
                const v = pickCI(r, c[0]);
                if (c[3] === 'bool' || c[0] === 'NeedOtorisasi') return otorisasiText(v);
                if (c[3] === 'date') return format_date(v);
                if (c[3] === 'float' || c[3] === 'int') return currencyNormalizer(v);
                return (v == null ? '' : v);
            }));
            const rows = [header].concat(body);
            const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
            const ext = (fmt === 'Excel') ? 'xls' : 'csv';
            const blob = new Blob(['﻿' + csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = (globalMode === '1')
                ? 'OutstandingSPPB_' + (globalDate1 || '') + '.' + ext
                : 'LaporanSPB_' + (globalDate1 || '') + '_' + (globalDate2 || '') + '.' + ext;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            showToast('📄', 'Data diekspor sebagai ' + fmt);
        }

        /* -- TOAST -- */
        function showToast(icon, msg) {
            const t = document.getElementById('toast');
            document.getElementById('ti').textContent = icon;
            document.getElementById('tm').textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        // NeedOtorisasi (bit di VwReportSpB): 0 = semua level otorisasi lengkap -> SUDAH,
        // 1 = masih butuh otorisasi -> BELUM. Nilai bisa datang sebagai 0/1, "0"/"1" atau
        // true/false, jadi dinormalkan ke string dulu. null/undefined -> '' (tanpa badge).
        function otorisasiText(v) {
            if (v == null || v === '') return '';
            return (String(v) === '1' || v === true) ? 'Belum' : 'Sudah';
        }

        function pickCI(r, key) {
            if (r[key] !== undefined) return r[key];
            const lk = String(key).toLowerCase();
            for (const k in r) {
                if (k.toLowerCase() === lk) return r[k];
            }
            return undefined;
        }

        var modereport_detailnobukti = 0,
            modereport_detailbarang = 1,
            modereport_detailcustomer = 2;
        var modereport_rekapnobukti = 3,
            modereport_rekapbarang = 4,
            modereport_rekapcustomer = 5;
        g_modeReport = modereport_detailnobukti;

        // Dispatcher: kedua SP punya set kolom & penomoran mode yang berbeda total (SPB 0-5,
        // Outstanding hanya 0=Detail/1=Rekap dalam numbering-nya sendiri) -- tetap dipisah jadi
        // dua fungsi, BUKAN digabung, supaya g_modeReport (dengan offset) tidak salah dibaca.
        function setDefaultHeader() {
            const isOut = (globalMode === '1');
            const base = isOut ? (g_modeReport - OUT_MODE_OFFSET) : g_modeReport;
            if (isOut) {
                setHeaderOut(base);
            } else {
                setHeaderSpb(base);
            }
        }

        function setHeaderSpb(base) {
            if (base == modereport_detailnobukti) {
                gcart_header = [
                    ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
                    ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Customer', 1, 'varchar', 0, 0],
                    ['NoPOCustomer', 'No. PO Customer', 1, 'varchar', 0, 0],
                    ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
                    ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['QNT', 'Qnt', 1, 'float', 1, 0],
                    ['namaGdg', 'Nama Gudang', 1, 'varchar', 0, 0],
                    ['TGLKIRIM', 'Tanggal Kirim', 1, 'date', 0, 0],
                    ['TGLTERIMA', 'Tanggal Terima', 1, 'date', 0, 0],
                    // ['NBerat', 'Berat/Volume', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'bool', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else if (base == modereport_detailbarang) {
                gcart_header = [
                    ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
                    ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
                    ['KodeCustSupp', 'Kode Customer', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
                    ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['QNT', 'Qnt', 1, 'float', 1, 0],
                    ['NetW', 'Net W', 1, 'float', 1, 2],
                    ['GrossW', 'Gross W', 1, 'float', 1, 2],
                    ['HARGA', 'Harga', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'bool', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else if (base == modereport_detailcustomer) {
                gcart_header = [
                    ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
                    ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
                    ['KodeCustSupp', 'Kode Customer', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
                    ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['QNT', 'Qnt', 1, 'float', 1, 0],
                    ['NetW', 'Net W', 1, 'float', 1, 2],
                    ['GrossW', 'Gross W', 1, 'float', 1, 2],
                    ['HARGA', 'Harga', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'bool', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else if (base == modereport_rekapnobukti) {
                gcart_header = [
                    ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
                    ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
                    ['NoPOCustomer', 'No. PO Customer', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Customer', 1, 'varchar', 0, 0],
                    ['QNT', 'Qnt', 1, 'float', 1, 0],
                    ['QNT2', 'Qnt', 1, 'float', 1, 0],
                    ['TGLKIRIM', 'Tanggal Kirim', 1, 'date', 0, 0],
                    ['TGLTERIMA', 'Tanggal Terima', 1, 'date', 0, 0],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'bool', 0, 0]
                ];
                gsum_issubtotal = 0;
                gsum_isgrandtotal = 1;

            } else if (base == modereport_rekapbarang) {
                gcart_header = [
                    ['KodeBrg', 'No Bukti', 1, 'varchar', 0, 0],
                    ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['Qnt', 'QNT', 1, 'float', 1, 2],
                    ['NDPP', 'DPP IDR', 1, 'float', 1, 2],
                    ['NPPN', 'PPN IDR', 1, 'float', 1, 2],
                    ['TotalIDR', 'Total IDR', 1, 'float', 1, 2],
                    ['KODEVLS', 'Vls', 1, 'varchar', 0, 0],
                    ['kurs', 'Kurs', 1, 'varchar', 0, 0],
                    ['Ndppusd', 'DPP $', 1, 'float', 1, 2],
                    ['NPPNusd', 'PPN $', 1, 'float', 1, 2],
                    ['totalusd', 'Total $', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'bool', 0, 0]
                ];
                gsum_issubtotal = 0;
                gsum_isgrandtotal = 1;

            } else {
                gcart_header = [
                    ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
                    ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
                    ['KodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
                    ['NDPP', 'DPP IDR', 1, 'float', 1, 2],
                    ['NPPN', 'PPN IDR', 1, 'float', 1, 2],
                    ['TotalIDR', 'Total IDR', 1, 'float', 1, 2],
                    ['KODEVLS', 'Vls', 1, 'varchar', 0, 0],
                    ['kurs', 'Kurs', 1, 'varchar', 0, 0],
                    ['Ndppusd', 'DPP $', 1, 'float', 1, 2],
                    ['NPPNusd', 'PPN $', 1, 'float', 1, 2],
                    ['totalusd', 'Total $', 1, 'float', 1, 2],
                    ['NeedOtorisasi', 'Otorisasi', 1, 'bool', 0, 0]
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;
            }
        }

        // Kolom Outstanding (Sp_ReportOutSpbDet) -- diambil apa adanya dari
        // reportmarketingoutsppb.blade.php. Hanya dua mode (Detail/Rekap): proc ini
        // mengembalikan field yang sama apa pun Ordr, jadi base di sini dipakai dalam
        // numbering-nya SENDIRI (0=Detail, 1=Rekap) -- BUKAN modereport_* di atas, yang
        // sudah dipakai untuk 6 mode SPB dan akan salah kalau disamakan.
        function setHeaderOut(base) {
            if (base === 0) {
                gcart_header = [
                    ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
                    ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
                    ['kodeCustSupp', 'Kode', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Customer', 1, 'varchar', 0, 0],
                    ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
                    ['Namabrg', 'Nama Barang', 1, 'varchar', 0, 0],
                    ['NOPOCUstomer', 'No. PO. Cust', 1, 'varchar', 0, 0],
                    ['NoSo', 'No. SO', 1, 'varchar', 0, 0],
                    ['TanggalSO', 'Tgl. SO', 1, 'date', 0, 0],
                    ['QntOut1', 'Qty 1', 1, 'float', 1, 0],
                    ['QntOut2', 'Qty 2', 1, 'float', 1, 0],
                    ['HARGA', 'Harga', 1, 'float', 1, 0],
                    ['NDPPRPZX', 'Total', 1, 'float', 1, 0],
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;

            } else {
                gcart_header = [
                    ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
                    ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
                    ['NamaSls', 'Sales', 1, 'varchar', 0, 0],
                    ['NAMACUSTSUPP', 'Nama Customer', 1, 'varchar', 0, 0],
                    ['NOPOCUstomer', 'No. PO. Customer', 1, 'varchar', 0, 0],
                    ['NoSo', 'No. SO', 1, 'varchar', 0, 0],
                    ['TanggalSO', 'Tgl. SO', 1, 'date', 0, 0],
                    ['NDPPRPZX', 'Total', 1, 'float', 1, 0],
                ];
                gsum_issubtotal = 1;
                gsum_isgrandtotal = 1;
            }
        }

        function makeTable(_mode) {
            // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
            // mode report menentukan kolom yang dipakai
            let groupby = '';
            let _date1 = $("#inputDate1").val();
            let _date2 = $("#inputDate2").val();
            let inputOto = globalOtorisasi;
            let input_order = globalOrderBy;
            let inputTerima = globalTerima;
            const isOut = (globalMode === '1');

            // Kolom yang dikembalikan dua proc ini beda casing (mis. NOBUKTI vs NoBukti,
            // KodeCustSupp vs kodeCustSupp) -- groupby (dibaca render() sebagai r[currentGroupby]
            // apa adanya) harus mengikuti casing masing-masing proc, bukan satu tabel bersama.
            if (isOut) {
                if (input_order == "N") {
                    groupby = 'NoBukti';
                } else if (input_order == "B") {
                    groupby = 'KodeBrg';
                } else if (input_order == "S") {
                    groupby = 'KodeSls';
                } else {
                    groupby = 'kodeCustSupp';
                }
            } else {
                if (input_order == "N") {
                    groupby = 'NOBUKTI';
                } else if (input_order == "B") {
                    groupby = 'KODEBRG';
                } else {
                    groupby = 'KodeCustSupp';
                }
            }

            setDefaultHeader();
            if (typeof doSetHeader === 'function') {
                doSetHeader(g_modeReport);
            }

            // Sp_ReportOutSpbDet tidak punya parameter @tglterima. date2 SENGAJA tidak dikirim
            // di mode Outstanding (hanya tanggal pertama yang dipakai) -- LaporanMarketingOutSPPBController
            // TIDAK diubah, jadi $req->get('date2') otomatis NULL di sisi server, termasuk posisi
            // tukar date1/date2-nya yang juga dipertahankan apa adanya (lihat komentar di toolbar).
            let url, data;
            if (isOut) {
                url = reportUrlOut;
                data = {
                    date1: _date1,
                    inputOto: inputOto,
                    inputOrd: input_order,
                };
            } else {
                url = reportUrlSpb;
                data = {
                    date1: _date1,
                    date2: _date2,
                    inputOto: inputOto,
                    inputOrd: input_order,
                    inputTerima: inputTerima
                };
            }

            document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

            $.ajax({
                url: url,
                type: 'get',
                data: data,
                success: function(res) {
                    lastRows = res || [];
                    currentGroupby = groupby;
                    $('#searchBox2').val('');
                    render();
                },
                error: function() {
                    lastRows = [];
                    currentGroupby = groupby;
                    render();
                }
            });
        }

        // Filter langsung di client (ga ke sp) berdasarkan pilihan #modalOtorisasi.
        // Nilainya sama dengan kolom NeedOtorisasi: 0 = Sudah Otorisasi, 1 = Belum Otorisasi,
        // 2/lainnya = Semua. Sp_ReportSPBDet juga sudah memfilter (NeedOtorisasi = @NeedOto,
        // 2 = semua), jadi filter ini hanya jaring kedua supaya tabel tetap konsisten dengan
        // pilihan filter walau data di lastRows dimuat sebelum filter diganti.
        function filterByOtorisasi(rows, filterVal) {
            switch (String(filterVal)) {
                case '0':
                    return rows.filter(r => otorisasiText(pickCI(r, 'NeedOtorisasi')) === 'Sudah');
                case '1':
                    return rows.filter(r => otorisasiText(pickCI(r, 'NeedOtorisasi')) === 'Belum');
                default:
                    return rows;
            }
        }

        // === RENDER KE TABEL STYLED (.tb-report #mainTable) ===
        // Kolom dibangun DINAMIS dari gcart_header (hanya kolom yang terlihat / item[2]===1,
        // sesuai urutan simpanan) -> mode-agnostic, jadi tiap mode report (item[4]===1 menandai
        // kolom yang di-subtotal) langsung terpakai tanpa daftar kolom hardcode.
        function render() {
            const cols = gcart_header.filter(c => c[2] === 1); // kolom terlihat, terurut
            const keys = cols.filter(c => c[4] === 1).map(c => c[0]); // kolom yang di-subtotal
            const thead = document.querySelector('#mainTable thead');
            const tbody = document.getElementById('tableBody');
            const showSub = (gsum_issubtotal === 1);
            const showGrand = (gsum_isgrandtotal === 1);

            const search = ($('#searchBox2').val() || '').trim().toLowerCase();
            const searched = !search ? (lastRows || []) : (lastRows || []).filter(function(r) {
                return rowSearchText(r, cols).indexOf(search) !== -1;
            });
            // pakai globalOtorisasi (nilai yang sudah di-Terapkan), BUKAN nilai select modal:
            // kalau user mengubah dropdown lalu menekan Batal, select tetap memegang nilai
            // yang dibatalkan itu dan akan ikut terpakai di render berikutnya (mis. saat cari).
            // Outstanding: lewati filter ini -- baris Sp_ReportOutSpbDet tidak punya kolom
            // NeedOtorisasi, jadi otorisasiText(undefined) selalu '' dan tidak cocok 'Sudah'
            // atau 'Belum' (proc sudah memfilter sendiri lewat parameter inputOto).
            const rows = (globalMode === '1') ? searched : filterByOtorisasi(searched, globalOtorisasi);

            // HEADER dinamis — dibangun report-table.js (ReportTable) supaya kolom bisa diseret
            // untuk diurutkan & punya menu roda gigi (sembunyikan / desimal / total).
            thead.innerHTML = ReportTable.headHtml(cols);

            if (!rows.length) {
                tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length +
                    '">Tidak ada data ditemukan.</td></tr>';
                document.getElementById('footerLabel').textContent = 'Tidak ada data';
                return;
            }

            let html = '',
                prev = null;
            let sub = {},
                grand = {};
            keys.forEach(k => {
                sub[k] = 0;
                grand[k] = 0;
            });

            rows.forEach(function(r, i) {
                const now = r[currentGroupby];

                // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
                if (showSub && i !== 0 && prev !== now) {
                    html += totalRowTotal('Subtotal', sub, cols, keys, 'subtotal-row');
                    keys.forEach(k => {
                        sub[k] = 0;
                    });
                }

                keys.forEach(function(k) {
                    const v = currencyNormalizer(pickCI(r, k));
                    sub[k] += v;
                    grand[k] += v;
                });

                // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5])
                html += '<tr class="data-row">' + cols.map(function(c) {
                    const key = c[0],
                        type = c[3];
                    // NeedOtorisasi = 0 berarti sudah otorisasi (hijau), 1 = belum (merah)
                    if (type === 'bool' || key === 'NeedOtorisasi') {
                        const txt = otorisasiText(pickCI(r, key));
                        if (!txt) return '<td></td>';
                        const cls = (txt === 'Sudah') ? 'is-active' : 'is-inactive';
                        return '<td><span class="sp-badge ' + cls + '">' + txt + '</span></td>';
                    }
                    if (type === 'date') return '<td>' + format_date(pickCI(r, key)) + '</td>';
                    if (type === 'float' || type === 'int') return '<td class="num">' + format_number(
                        currencyNormalizer(pickCI(r, key)), c[5]) + '</td>';
                    return '<td>' + nullToEmpty(pickCI(r, key)) + '</td>';
                }).join('') + '</tr>';

                prev = now;
            });

            // subtotal grup terakhir + grand total   mengikuti toggle di modal Customize Table
            if (showSub) html += totalRowTotal('Subtotal', sub, cols, keys, 'subtotal-row');
            if (showGrand) html += totalRowTotal('GRAND TOTAL', grand, cols, keys, 'grand-total');

            tbody.innerHTML = html;
            document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
        }

        // Baris total: nilai di kolom yang di-subtotal (item[4]===1), label di kolom pertama
        // non-total yang masih terlihat, sel lain dikosongkan.
        function totalRowTotal(label, total, cols, keys, cls) {
            const labelIdx = cols.findIndex(c => keys.indexOf(c[0]) === -1);

            const tds = cols.map(function(c, idx) {
                if (keys.indexOf(c[0]) !== -1) {
                    return '<td class="num">' + format_number(total[c[0]], c[5]) + '</td>';
                }
                if (idx === labelIdx) return '<td>' + label + '</td>';
                return '<td></td>';
            });

            return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
        }

        // === PENCARIAN SISI-KLIEN ===
        function applyFilters() {
            if (!lastRows.length) return; // belum ada data dimuat
            render();
        }

        // Gabungan teks satu baris dari kolom terlihat (tanggal pakai format tampil
        // dd/mm/yyyy) supaya pencarian cocok dengan apa yang user lihat di tabel.
        function rowSearchText(r, cols) {
            return cols.map(function(c) {
                const v = pickCI(r, c[0]);
                // kolom badge dicari lewat teksnya ("sudah"/"belum"), bukan nilai mentah 0/1
                if (c[3] === 'bool' || c[0] === 'NeedOtorisasi') return otorisasiText(v);
                if (c[3] === 'date') return format_date(v);
                return (v == null ? '' : String(v));
            }).join(' ').toLowerCase();
        }

        function setModeReport() {
            if (globalMode === '1') {
                // Sp_ReportOutSpbDet mengembalikan field yang sama apa pun Ordr -> hanya
                // Detail/Rekap (numbering sendiri, lihat setHeaderOut()), lalu digeser
                // OUT_MODE_OFFSET supaya tidak bentrok dengan kolom tersimpan mode SPB
                // (DBSIMPANHEADER dikunci per href+reportmode, href-nya sama).
                g_modeReport = (jenisreport === 0 ? 0 : 1) + OUT_MODE_OFFSET;
            } else if (globalOrderBy == "N") {
                if (jenisreport === 0) {
                    g_modeReport = modereport_detailnobukti;
                } else {
                    g_modeReport = modereport_rekapnobukti;
                }
            } else if (globalOrderBy == "B") {
                if (jenisreport === 0) {
                    g_modeReport = modereport_detailbarang;
                } else {
                    g_modeReport = modereport_rekapbarang;
                }
            } else {
                if (jenisreport === 0) {
                    g_modeReport = modereport_detailcustomer;
                } else {
                    g_modeReport = modereport_rekapcustomer;
                }
            }

            doSetHeader(g_modeReport);
            doShowCustomize();
        }
    </script>
@endsection
