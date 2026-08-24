@extends('report.masterreport4')

{{-- All .tb-report table + toolbar styling lives in public/css/report-table.css (loaded globally via newmaster2).
     This page is self-contained: it carries its own #formSelect modal (below) and loads Perkiraan from
     its own endpoint (laporanaccountingbukubesar_loadperkiraan), so it does NOT include modalAccountingJurnal. --}}

@section('header2')
    <style>
        .checkmark-red {
            color: red !important;
            font-weight: bold;
            margin-left: 6px;
        }

        #inputDivisiBtn {
            border: 0;
            background: none;
            padding: 0;
            box-shadow: none;
            color: #495057;
            font-weight: 600;
        }

        #inputDivisiBtn:hover,
        #inputDivisiBtn:focus {
            color: #0d6efd;
            box-shadow: none;
        }
    </style>
    <!-- Modal -->
    <div class="modal fade" id="formSelect" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1200px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"
                        onclick="$('#formSelect').modal('hide')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="tabelSelect" class="table table-bordered table-striped">
                        <thead class="text-center" id="tabelHeader">
                            <tr>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="tabel_dataSelect" class="text-left">
                            <tr>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal"
                        onclick="$('#formSelect').modal('hide')">Batal</button>
                </div>
            </div>
        </div>
    </div>
    <div class="tb-report main">
        <div class="content">

            <!-- TOOLBAR -->
            <div class="toolbar">
                <div>
                    <div class="page-title">Buku Tambahan</div>
                    <div class="page-sub">Dicetak oleh: {{ $akses['user'] }} &nbsp;&middot;&nbsp; <span
                            id="printTime"></span></div>
                </div>

                <!-- Periode (date range) -->
                <div class="filter-wrap">
                    <label>Periode</label>
                    <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
                    <span class="filter-sep">s/d</span>
                    <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
                </div>

                <!-- Divisi (DROPDOWN; sumber loadDivisi, default divisi pertama) -->
                <div class="filter-wrap">
                    <label>Divisi</label>
                    <input type="hidden" id="inputDivisi" value="-">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="inputDivisiBtn"
                        data-bs-toggle="dropdown" aria-expanded="false"><span id="divisiLabel">-</span></button>
                    <ul class="dropdown-menu" id="dropdownDivisi" aria-labelledby="inputDivisiBtn"
                        style="max-height:320px; overflow:auto;"></ul>
                </div>

                <!-- Perkiraan range -->
                <div class="filter-wrap">
                    <label>Perkiraan</label>
                    <input type="text" class="filter-inp" id="inputPerkiraan1" value="-" style="width:80px">
                    <button type="button" class="btn-pick" onclick="loadSelectPerkiraan(1)">+</button>
                    <span class="filter-sep">s/d</span>
                    <input type="text" class="filter-inp" id="inputPerkiraan2" value="-" style="width:80px">
                    <button type="button" class="btn-pick" onclick="loadSelectPerkiraan(2)">+</button>
                </div>



                <!-- Actions: second (row-level) search + load + export -->
                <div class="action-group">
                    <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..."
                        oninput="applyFilters()" style="width:180px">
                    <button class="btn-load" onclick="makeTable('REPORT')" title="Tampilkan laporan"><i
                            class="fas fa-check"></i> Tampilkan</button>
                    <div class="export-wrap" id="exportWrap">
                        <button class="export-btn" onclick="toggleExport()"><i class="bi bi-arrow-down"></i> Export <i
                                class="bi bi-caret-down-fill"></i></button>
                        <div class="export-drop" id="exportDrop">
                            <div class="export-opt" onclick="doExport('Excel')"><i
                                    class="bi bi-journals text-success"></i>
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

            <!-- TABLE -->
            <div class="table-outer">
                <div class="table-wrap">
                    <table class="tb" id="mainTable">
                        <thead>
                            <tr>
                                <th style="min-width:90px">Tanggal</th>
                                <th style="min-width:130px">No. Bukti</th>
                                <th>Keterangan</th>
                                <th style="min-width:80px">Lawan</th>
                                <th class="num" style="min-width:130px">Debet</th>
                                <th class="num" style="min-width:130px">Kredit</th>
                                <th class="num" style="min-width:150px">Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr class="empty-row">
                                <td colspan="7">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <span id="footerLabel">Belum ada data dimuat</span>
                </div>
            </div>

        </div><!-- /content -->

        <!-- TOAST -->
        <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
    </div><!-- /tb-report -->
@endsection

@section('jsreport')
    {{-- Reusable bottom voucher panel (Bukti Kas/Bank + Invoice). Reads window.ReportTableConfig
     below; report-table.css is already loaded globally via report/newmaster2.blade.php. --}}
    <script>
        window.ReportTableConfig = {
            kasUrl: "{{ url('laporanaccountingbukubesar_doKasharian') }}",
            invoiceUrl: "{{ url('laporanaccountingbukubesar_doInvoice') }}",
            // Voucher titles per Jenis. BBK/BBM/BKK/BKM/BMM/BJK/INVC are already covered by
            // report-table.js. Unmapped jenis (TDP/BPL/UMJ/R/L/OPN/RPB/BP/KRS/…) fall back to
            // "Bukti Kas". Add overrides here later, e.g.  TDP: 'BUKTI TITIPAN',
            jenisTitle: {
                // KODE: 'JUDUL VOUCHER',
            }
        };
    </script>
    <script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>

    <script type="text/javascript">
        // Endpoint for the styled table (Sp_ReportBukuTambahan over the perkiraan range)
        const reportUrl = "{{ url('laporanaccountingbukubesar_doReport') }}";

        // Raw rows from the last successful load — kept so search / export don't refetch.
        let lastRows = [];

        // The shared report engine (masterreport4) is declared globally; provide a minimal
        // header so its customize/save machinery stays valid even though we render our own table.
        g_modeReport = 0;

        function setDefaultHeader() {
            gcart_header = [
                ['Tanggal', 'Tanggal', 1, 'date', 0, 0, [1, 1, 1], false],
                ['Nobukti', 'No. Bukti', 1, 'varchar', 0, 0, [1, 1, 1], false],
                ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0, [1, 1, 1], false],
                ['Lawan', 'Lawan', 1, 'varchar', 0, 0, [1, 1, 1], false],
                ['Debet', 'Debet', 1, 'float', 1, 2, [1, 1, 1], false],
                ['kredit', 'Kredit', 1, 'float', 1, 2, [1, 1, 1], false],
                ['SaldoAkhir', 'Saldo', 1, 'float', 0, 0, [1, 1, 1], false]
            ];
            gsum_issubtotal = 1;
            gsum_isgrandtotal = 1;
        }

        $(document).ready(function() {
            // This report renders its own styled table; strip the shared engine's table markup.
            $("#showTableReport").empty().hide();

            var pt = document.getElementById('printTime');
            if (pt) pt.textContent = new Date().toLocaleString('id-ID');

            loadDivisiDropdown(); // isi dropdown Divisi (default: divisi pertama)

            // NO auto-load: the table loads only when the "Tampilkan" (check) button is clicked.
        });

        /* ── DROPDOWN DIVISI (sumber loadDivisi; default: divisi pertama) ── */
        function loadDivisiDropdown() {
            let list = [];
            $.ajax({
                url: "{!! url('laporanaccountingbukubesar_loaddivisi') !!}",
                type: "get",
                async: false,
                success: function(res) {
                    list = res || [];
                }
            });

            let html = '';
            list.forEach((item) => {
                const nama = (item.NamaDevisi != null ? String(item.NamaDevisi) : '').replace(/"/g, '&quot;');
                html += '<li><a class="dropdown-item divisi-item" style="cursor:pointer" ' +
                    'data-value="' + item.Devisi + '" data-nama="' + nama + '">' +
                    item.Devisi + ' - ' + (item.NamaDevisi != null ? item.NamaDevisi : '') +
                    ' <span class="checkmark-red" style="display:none">&#10003;</span></a></li>';
            });
            $("#dropdownDivisi").html(html);

            // default: divisi pertama
            if (list.length) {
                applyDivisi(list[0].Devisi, list[0].NamaDevisi != null ? list[0].NamaDevisi : '');
            }
        }

        function applyDivisi(kode, nama) {
            $("#inputDivisi").val(kode);
            $("#divisiLabel").text(nama || kode);
            $("#inputDivisiBtn").attr('title', nama || kode);
            $('#dropdownDivisi .checkmark-red').hide();
            $(`#dropdownDivisi .divisi-item[data-value='${kode}'] .checkmark-red`).show();
        }

        // Memilih divisi hanya menyetel filter; tabel dimuat saat klik "Tampilkan".
        $(document).on('click', '#dropdownDivisi .divisi-item', function() {
            applyDivisi($(this).data('value'), $(this).data('nama'));
        });

        /* ── HELPERS ── */
        // Sp_ReportBukuTambahan mixes column casing (Debet, kredit, Nobukti…); read lowercased.
        function lc(r) {
            const o = {};
            Object.keys(r).forEach(k => {
                o[k.toLowerCase()] = r[k];
            });
            return o;
        }

        function num(v) {
            if (v === null || v === undefined || v === '') return 0;
            const n = parseFloat(v);
            return isNaN(n) ? 0 : n;
        }

        // Debet/Kredit cell value ('-' when zero)
        function money(v) {
            if (!v) return '-';
            return (v < 0 ? '-' : '') + 'Rp ' + Math.abs(v).toLocaleString('id-ID', {
                maximumFractionDigits: 2
            });
        }
        // Running saldo always shows a value (incl. 0)
        function saldo(v) {
            return (v < 0 ? '-' : '') + 'Rp ' + Math.abs(v).toLocaleString('id-ID', {
                maximumFractionDigits: 2
            });
        }

        function setFooter(msg) {
            const el = document.getElementById('footerLabel');
            if (el) el.textContent = msg;
        }

        function isOpening(r) {
            return String(r.nobukti || '').trim().toUpperCase() === 'SALDO AWAL';
        }

        // Group rows by account (NoACC), preserving the order the proc returned them in.
        function buildGroups(rows) {
            const groups = [],
                idx = {};
            rows.forEach(r0 => {
                const r = lc(r0);
                const acc = (r.noacc != null ? r.noacc : '').toString().trim();
                if (!(acc in idx)) {
                    idx[acc] = groups.length;
                    groups.push({
                        acc: acc,
                        nama: (r.nama != null ? r.nama : '').toString().trim(),
                        rows: []
                    });
                }
                groups[idx[acc]].rows.push(r);
            });
            return groups;
        }

        /* ── LOAD (only on click) ── */
        function makeTable(_mode) {
            const data = {
                date1: $("#inputDate1").val(),
                date2: $("#inputDate2").val(),
                divisi: $("#inputDivisi").val(),
                inputPerkiraan1: $("#inputPerkiraan1").val(),
                inputPerkiraan2: $("#inputPerkiraan2").val()
            };

            document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');
            $.ajax({
                url: reportUrl,
                type: 'get',
                data: data,
                success: function(res) {
                    lastRows = res || [];
                    renderLedger();
                },
                error: function() {
                    lastRows = [];
                    renderLedger();
                    setFooter('Gagal memuat data laporan.');
                }
            });
        }

        /* ── RENDER (grouped general ledger) ── */
        // Both search boxes match the same fields (account code/name, no. bukti, keterangan)
        // and combine with OR: a transaction row is shown if it matches ANY non-empty term.
        // Filtering is row-level — non-matching rows are dropped, and subtotals / running
        // balance / grand total are recomputed from the rows that remain visible.
        function activeTerms() {
            return [$("#searchBox").val(), $("#searchBox2").val()]
                .map(s => (s || '').trim().toLowerCase())
                .filter(s => s !== '');
        }

        function rowMatches(g, r, terms) {
            if (!terms.length) return true;
            const hay = (g.acc + ' ' + g.nama + ' ' + (r.nobukti || '') + ' ' + (r.keterangan || '')).toLowerCase();
            return terms.some(t => hay.includes(t));
        }

        function renderLedger() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            const terms = activeTerms();
            const groups = buildGroups(lastRows);

            let visible = 0,
                shownGroups = 0,
                grandD = 0,
                grandK = 0;

            groups.forEach(g => {
                const opening = g.rows.find(isOpening);
                const txns = g.rows.filter(r => !isOpening(r));
                const shownTxns = txns.filter(r => rowMatches(g, r, terms));
                if (!shownTxns.length) return; // no matching rows → hide the whole account

                shownGroups++;

                // group header
                const gtr = document.createElement('tr');
                gtr.className = 'group-row';
                gtr.innerHTML = `<td colspan="7">${g.acc}${g.nama ? ' &mdash; ' + g.nama : ''}</td>`;
                tbody.appendChild(gtr);

                let running = 0,
                    subD = 0,
                    subK = 0;

                // opening "Saldo Awal" seeds the running balance (when the account has one)
                if (opening) {
                    running += num(opening.saldoakhir);
                    const tr = document.createElement('tr');
                    tr.className = 'data-row';
                    tr.innerHTML =
                        `<td colspan="4" style="font-style:italic;color:var(--muted)">Saldo Awal</td>
           <td class="num">-</td><td class="num">-</td>
           <td class="num ${running < 0 ? 'neg' : ''}" style="font-weight:600">${saldo(running)}</td>`;
                    tbody.appendChild(tr);
                }

                shownTxns.forEach(r => {
                    running += num(r.saldoakhir); // cumulative over the rows shown
                    const d = num(r.debet),
                        k = num(r.kredit);
                    subD += d;
                    subK += k;
                    grandD += d;
                    grandK += k;
                    const tgl = (typeof format_date === 'function') ? format_date(r.tanggal) : (r.tanggal ||
                        '');

                    // Each transaction row opens its source voucher in the shared bottom panel
                    // (report-table.js): INVC -> Invoice, B* -> Bukti Kas/Bank, by row Jenis.
                    const nb = (r.nobukti != null ? String(r.nobukti) : '');
                    const jn = (r.jenis != null ? String(r.jenis).trim() : '');

                    const tr = document.createElement('tr');
                    tr.className = 'data-row kas-clickable';
                    tr.innerHTML =
                        `<td style="white-space:nowrap">${tgl}</td>
           <td class="code">${nb}</td>
           <td class="name">${r.keterangan != null ? r.keterangan : ''}</td>
           <td class="code">${r.lawan != null ? r.lawan : ''}</td>
           <td class="num">${money(d)}</td>
           <td class="num">${money(k)}</td>
           <td class="num ${running < 0 ? 'neg' : ''}" style="font-weight:600">${saldo(running)}</td>`;
                    tr.style.cursor = 'pointer';
                    tr.title = 'Klik untuk lihat ' + (typeof jenisTitle === 'function' ? jenisTitle(jn) :
                        'voucher') + ' ' + nb;
                    (function(noBukti, jenis) {
                        tr.onclick = function() {
                            openVoucher(noBukti, jenis);
                        };
                    })(nb, jn);
                    tbody.appendChild(tr);
                    visible++;
                });

                // per-account subtotal (totals of shown rows + closing balance)
                const str = document.createElement('tr');
                str.className = 'subtotal-row';
                str.innerHTML =
                    `<td colspan="4">Subtotal ${g.acc}</td>
         <td class="num">${money(subD)}</td>
         <td class="num">${money(subK)}</td>
         <td class="num ${running < 0 ? 'neg' : ''}"></td>`;
                tbody.appendChild(str);
            });

            if (!shownGroups) {
                const msg = lastRows.length ? 'Tidak ada baris yang cocok dengan pencarian.' :
                    'Tidak ada data untuk filter ini.';
                tbody.innerHTML = `<tr class="empty-row"><td colspan="7">${msg}</td></tr>`;
                setFooter(lastRows.length ? 'Tidak ada hasil pencarian' : 'Tidak ada data');
                return;
            }

            // grand total (movement totals across all shown rows)
            const gt = document.createElement('tr');
            gt.className = 'grand-total';
            gt.innerHTML =
                `<td colspan="4" style="font-weight:800">TOTAL KESELURUHAN</td>
       <td class="num">${money(grandD)}</td>
       <td class="num">${money(grandK)}</td>
       <td class="num"></td>`;
            tbody.appendChild(gt);

            setFooter(`Menampilkan ${visible} transaksi - ${shownGroups} akun`);
        }

        function applyFilters() {
            if (lastRows.length) renderLedger();
        }

        // The voucher drill (openVoucher / openKasharian / openInvoice / closeKasharian)
        // lives in public/js/report-table.js, configured via window.ReportTableConfig above.

        /* ── EXPORT ── */
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
            const out = [
                ['Perkiraan', 'Nama', 'Tanggal', 'No. Bukti', 'Keterangan', 'Lawan', 'Debet', 'Kredit', 'Saldo']
            ];
            buildGroups(lastRows).forEach(g => {
                let running = 0;
                g.rows.forEach(r => {
                    running += num(r.saldoakhir);
                    if (isOpening(r)) {
                        out.push([g.acc, g.nama, '', 'SALDO AWAL', 'Saldo Awal', '', '', '', running]);
                    } else {
                        out.push([g.acc, g.nama, r.tanggal || '', r.nobukti || '', r.keterangan || '', r
                            .lawan || '', num(r.debet), num(r.kredit), running
                        ]);
                    }
                });
            });

            const csv = out.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
            const ext = (fmt === 'Excel') ? 'xls' : 'csv';
            const blob = new Blob(['﻿' + csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'BukuBesar_' + ($("#inputDate1").val() || '') + '_sd_' + ($("#inputDate2").val() || '') + '.' +
                ext;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            showToast('📄', 'Data diekspor sebagai ' + fmt);
        }

        /* ── TOAST ── */
        function showToast(icon, msg) {
            const t = document.getElementById('toast');
            document.getElementById('ti').textContent = icon;
            document.getElementById('tm').textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        // Buka modal pilih Perkiraan (rentang: target 1 = awal → #inputPerkiraan1,
        // target 2 = akhir → #inputPerkiraan2). Sumber khusus Buku Besar:
        // laporanaccountingbukubesar_loadperkiraan (parameter beda dari modal bersama).
        function loadSelectPerkiraan(target) {
            let _token = $("#_token").val();
            let dataRefresh = [];

            if ($.fn.DataTable.isDataTable('#tabelSelect')) {
                $('#tabelSelect').DataTable().destroy();
            }

            $.ajax({
                url: "{!! url('laporanaccountingbukubesar_loadperkiraan') !!}",
                type: "get",
                async: false,
                data: {
                    _token: _token,
                },
                success: function(res) {
                    dataRefresh = res || [];
                },
            });

            document.getElementById('exampleModalLabel').innerHTML = "Select Perkiraan";
            document.getElementById("tabelHeader").innerHTML =
                `<tr>
        <th>Actions</th>
        <th>Perkiraan</th>
        <th>Keterangan</th>
        </tr>`;

            let rowTable = "";
            dataRefresh.forEach((item) => {
                const kodeJs = String(item.Perkiraan).replace(/'/g, "\\'");
                rowTable += `<tr>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonPilihPerkiraan(${target},'${kodeJs}')">+</button>
      </td>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
            });

            document.getElementById("tabel_dataSelect").innerHTML = rowTable;
            $("#tabelSelect").DataTable({
                "lengthChange": false,
                "paging": true,
            });

            $("#formSelect").modal('toggle'); // <-- tampilkan modalnya
        }

        // Pilih satu Perkiraan → isi input awal/akhir lalu tutup modal.
        function buttonPilihPerkiraan(target, kode) {
            $(target == 2 ? "#inputPerkiraan2" : "#inputPerkiraan1").val(kode);
            $("#formSelect").modal('hide');
        }
    </script>
@endsection
