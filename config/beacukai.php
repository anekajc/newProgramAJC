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

    'ceisa' => [
        'login_url' => env('BC_CEISA_LOGIN_URL', 'https://apis-gw.beacukai.go.id/nle-oauth/v1/user/login'),
        'document_url' => env('BC_CEISA_DOCUMENT_URL', 'https://apis-gw.beacukai.go.id/openapi/document'),
        'timeout' => (int) env('BC_CEISA_TIMEOUT', 30),
        // Masa berlaku token (detik) bila response login tidak membawa expires_in.
        'default_expires_in' => 600,
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
