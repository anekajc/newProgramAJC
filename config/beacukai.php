<?php

// Pengaturan modul Dokumen Bea Cukai (BC 2.3 / 2.5 / 2.6.1 / 2.6.2 / 4.0 / 4.1) dan CEISA.
// Semua tabel BC dibuat MANUAL di database SML (DBSMLNEW); nama tabelnya dikumpulkan di sini
// supaya cukup diubah di satu tempat. Tabel yang bernilai null belum dibuat / belum diberi
// nama - fitur yang memakainya akan menolak dengan pesan "belum dikonfigurasi".

return [

    // KPPBC asal dokumen. Bawaan Villiger: 071100 (KPPBC TMP C Jember). Dipakai sebagai
    // isian awal kodeKantor header dan kodeKantorAsal bahan baku.
    'kode_kantor' => env('BC_KODE_KANTOR', '071100'),

    // KPPBC tempat bongkar BC 2.3. Bawaan Villiger: 070100 (KPPBC TMP Tanjung Perak). Isian awal
    // kodeKantorBongkar.
    'kode_kantor_bongkar' => env('BC_KODE_KANTOR_BONGKAR', '070100'),

    // Kode valuta DBVALAS yang BUKAN kode ISO 4217 => padanan ISO yang diminta CEISA ("Referensi Valuta").
    // Isi di DBVALAS tidak diubah; terjemahan hanya terjadi saat payload dikirim. Kode 3 huruf lain (USD, SGD,
    // IDR, ...) dikirim apa adanya. Tambahkan baris di sini bila ada valuta lain yang kodenya bukan ISO.
    'valuta_iso' => [
        'RM' => 'MYR',
        'EURO' => 'EUR',
    ],

    'ceisa' => [
        'login_url' => env('BC_CEISA_LOGIN_URL', 'https://apis-gw.beacukai.go.id/nle-oauth/v1/user/login'),
        'document_url' => env('BC_CEISA_DOCUMENT_URL', 'https://apis-gw.beacukai.go.id/openapi/document'),
        'timeout' => (int) env('BC_CEISA_TIMEOUT', 30),
        // Masa berlaku token (detik) bila response login tidak membawa expires_in.
        'default_expires_in' => 600,
    ],

    // Konstanta payload BC 2.5 (docs/documentbc25_plan.md §9, quirk Q-d). Tebakan Villiger yang BELUM
    // dibuktikan oleh uji CEISA: kodeFasilitasTarif 1 = dibayar (3 ditangguhkan, 5 dibebaskan, 6 tidak
    // dipungut), kodeJenisTarif 1 = advalorum. Cocokkan dengan balasan CEISA pertama; ubah di sini bila perlu.
    'bc25' => [
        'kode_fasilitas_tarif' => env('BC25_KODE_FASILITAS_TARIF', '1'),
        'tarif_fasilitas' => (int) env('BC25_TARIF_FASILITAS', 0),
        'kode_jenis_tarif' => env('BC25_KODE_JENIS_TARIF', '1'),
        'kode_lokasi_bayar' => env('BC25_KODE_LOKASI_BAYAR', '1'),
    ],

    // Konstanta payload BC 4.1. Skema resmi tidak punya pungutan / barangTarif, jadi hanya kodeLokasiBayar
    // (1 = Bank) yang masih dipakai; nilai awalnya tebakan Villiger yang BELUM dibuktikan oleh uji CEISA.
    // Dipisah dari bc25 supaya koreksi BC 4.1 tidak mengubah BC 2.5.
    'bc41' => [
        'kode_lokasi_bayar' => env('BC41_KODE_LOKASI_BAYAR', '1'),
    ],

    'tables' => [
        'entitas' => 'ENTITAS',
        'negara' => 'NEGARA',

        // Master referensi (kode + nama) untuk isian combobox. 'table' => null = belum ada.
        'satuan' => ['table' => 'JENISSATUAN', 'kode' => 'KODEJENISSATUAN', 'nama' => 'NAMAJENISSATUAN'],
        'kemasan' => ['table' => 'JENISKEMASAN', 'kode' => 'KODEJENISKEMASAN', 'nama' => 'NAMAJENISKEMASAN'],
        'jenis_dokumen' => ['table' => 'JENISDOKUMEN', 'kode' => 'KODEJENISDOKUMEN', 'nama' => 'NAMAJENISDOKUMEN'],
        'kategori_barang' => ['table' => 'KATEGORIBARANG', 'kode' => 'KODEKATEGORIBARANG', 'nama' => 'KETKATEGORIBARANG'],
        // POSTARIFHS tidak punya kolom uraian; berisi tarif BM / PPN / PPh per pos tarif.
        'hs' => ['table' => 'POSTARIFHS', 'kode' => 'POSTARIF', 'nama' => null],
        'pelabuhan' => ['table' => 'PELABUHAN', 'kode' => 'KODEPELABUHAN', 'nama' => 'NAMAPELABUHAN'],
        // Master Kontainer dipisah: ukuran dan tipe masing-masing satu tabel (kode + nama).
        'ukuran_kontainer' => 'UKURANKONTAINER',
        'tipe_kontainer' => 'TIPEKONTAINER',

        // Token CEISA per user (satu tabel untuk semua dokumen BC).
        'token' => 'CEISATOKEN',

        // BC 2.6.1: header + 6 koleksi anak (docs/update-db-DBSMLNEW/beacukai/09_tabel_documentbc261.sql).
        'bc261' => 'DOCUMENTBC261',
        'bc261_barang' => 'DOCUMENTBC261BARANG',
        'bc261_bahanbaku' => 'DOCUMENTBC261BAHANBAKU',
        'bc261_kemasan' => 'DOCUMENTBC261KEMASAN',
        'bc261_kontainer' => 'DOCUMENTBC261KONTAINER',
        'bc261_jaminan' => 'DOCUMENTBC261JAMINAN',
        'bc261_dokumen' => 'DOCUMENTBC261DOKUMEN',

        // BC 2.6.2: header + 6 koleksi anak (docs/update-db-DBSMLNEW/beacukai/13_tabel_documentbc262.sql).
        // Sumber (Outstanding) = BC 2.6.1 yang sudah terkirim (SENT = 1) dan belum punya BC 2.6.2.
        'bc262' => 'DOCUMENTBC262',
        'bc262_barang' => 'DOCUMENTBC262BARANG',
        'bc262_bahanbaku' => 'DOCUMENTBC262BAHANBAKU',
        'bc262_kemasan' => 'DOCUMENTBC262KEMASAN',
        'bc262_kontainer' => 'DOCUMENTBC262KONTAINER',
        'bc262_jaminan' => 'DOCUMENTBC262JAMINAN',
        'bc262_dokumen' => 'DOCUMENTBC262DOKUMEN',
        'bc262_pungutan' => 'DOCUMENTBC262PUNGUTAN', // docs/update-db-DBSMLNEW/beacukai/19_tabel_documentbc262_pungutan.sql

        // BC 2.5: header + 5 koleksi anak, berdiri sendiri (docs/update-db-DBSMLNEW/beacukai/14_tabel_documentbc25.sql).
        'bc25' => 'DOCUMENTBC25',
        'bc25_barang' => 'DOCUMENTBC25BARANG',
        'bc25_bahanbaku' => 'DOCUMENTBC25BAHANBAKU',
        'bc25_kemasan' => 'DOCUMENTBC25KEMASAN',
        'bc25_kontainer' => 'DOCUMENTBC25KONTAINER',
        'bc25_dokumen' => 'DOCUMENTBC25DOKUMEN',

        // BC 4.1: header + 5 koleksi anak, berdiri sendiri (docs/update-db-DBSMLNEW/beacukai/16_tabel_documentbc41.sql).
        'bc41' => 'DOCUMENTBC41',
        'bc41_barang' => 'DOCUMENTBC41BARANG',
        'bc41_bahanbaku' => 'DOCUMENTBC41BAHANBAKU',
        'bc41_kemasan' => 'DOCUMENTBC41KEMASAN',
        'bc41_kontainer' => 'DOCUMENTBC41KONTAINER',
        'bc41_dokumen' => 'DOCUMENTBC41DOKUMEN',

        // BC 2.3: header (kemasan, kontainer, pengangkut jadi kolom) + barang + dokumen
        // (docs/update-db-DBSMLNEW/beacukai/11_tabel_documentbc23.sql). Sumber barang = DBPO / DBPODET.
        'bc23' => 'DOCUMENTBC23',
        'bc23_barang' => 'DOCUMENTBC23BARANG',
        'bc23_dokumen' => 'DOCUMENTBC23DOKUMEN',

        // BC 4.0: header (kemasan + pengangkut jadi kolom) + barang + dokumen
        // (docs/update-db-DBSMLNEW/beacukai/12_tabel_documentbc40.sql). Sumber barang = DBPO / DBPODET
        // pemasok dalam negeri.
        'bc40' => 'DOCUMENTBC40',
        'bc40_barang' => 'DOCUMENTBC40BARANG',
        'bc40_dokumen' => 'DOCUMENTBC40DOKUMEN',
    ],

];
