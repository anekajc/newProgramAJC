-- 12 - Tabel Dokumen BC 4.0 (header + barang + dokumen). Dibuat 27 Oktober 2026.
-- Rancangan acuan - DITINJAU dan DIJALANKAN MANUAL oleh user di DBSMLNEW (bukan php artisan migrate).
-- Kolom mengikuti docs/documentbc40_plan.md §2. Nama tabel dipakai lewat config/beacukai.php
-- (tables.bc40*), jadi kalau nama tabel diubah di sini, ubah juga di sana. Nama kolom dipakai langsung
-- oleh app/Http/Controllers/DocumentBc/DocumentBc40Controller.php. Token CEISA memakai tabel CEISATOKEN
-- dari skrip 09 (satu tabel untuk semua dokumen BC) - tidak dibuat lagi di sini.
--
-- SUMBER DOKUMEN = PO (DBPO / DBPODET): NOBUKTI = DBPO.NOBUKTI. DBPO dan DBPODET TIDAK diubah.
-- PO yang pemasoknya DALAM negeri (DBCUSTSUPP.NEGARA = 'INDONESIA'), tidak batal, sudah IsOtorisasi1,
-- punya baris barang (bukan jasa) dan tanggalnya dalam rentang periode yang dipilih muncul di
-- Outstanding selama BELUM punya baris di DOCUMENTBC40. Satu PO = satu BC 4.0 (NOBUKTI unik); hapus
-- dokumennya = PO kembali ke Outstanding. (BC 2.3 memakai pemasok luar negeri, jadi satu PO tidak
-- pernah masuk ke BC 2.3 dan BC 4.0 sekaligus.)
--
-- Status dokumen ada di tabel ini: BCTYPE ('40'), SENT (NULL/0 = siap dikirim, 1 = sudah diterima
-- CEISA), NOMORDOKUMENPABEAN + TANGGALAJU (nomor pendaftaran BC + tanggal AJU; nomor tidak dikirim,
-- tanggal AJU dikirim). Kemasan dan pengangkut hanya satu per dokumen, jadi menjadi kolom di header.
-- BC 4.0 tidak punya peti kemas (selalu dikirim []), CIF, incoterm atau pelabuhan.
-- Anak dihubungkan lewat IDBC40 = DOCUMENTBC40.ID; dihapus eksplisit oleh aplikasi (tanpa cascade).
-- Kolom teks NOT NULL berisi '' bila kosong; angka desimal(19,4) NOT NULL bawaan 0.
-- Kolom teks header yang NULL-able (NOMORAJU, KODEKANTOR, KODEJENISTPB, KOTATTD, NAMATTD, JABATANTTD)
-- memang dikirim sebagai null ke CEISA bila kosong (sama seperti Villiger). NOMORAJU disimpan HURUF BESAR.
-- Aman dijalankan ulang (tabel yang sudah ada dilewati).

USE DBSMLNEW
GO

IF OBJECT_ID('dbo.DOCUMENTBC40', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC40 (
        ID                      int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        NOBUKTI                 varchar(20)    NOT NULL,         -- DBPO.NOBUKTI (sumber); unik
        BCTYPE                  varchar(10)    NOT NULL DEFAULT '40',
        SENT                    int            NULL,             -- 1 = sudah diterima CEISA (status OK)
        IDHEADER                varchar(100)   NULL,             -- idHeader balasan CEISA
        TGLKIRIM                datetime       NULL,
        USERKIRIM               varchar(50)    NULL,

        NOMORAJU                varchar(100)   NULL,             -- huruf besar
        KODEKANTOR              varchar(20)    NULL,
        KODEJENISTPB            varchar(20)    NULL,             -- bawaan '1' (Kawasan Berikat)
        KODETUJUANPENGIRIM      varchar(20)    NOT NULL DEFAULT '1',   -- dikirim sebagai kodeTujuanPengiriman
        NOMORDOKUMENPABEAN      varchar(50)    NULL,             -- F. Nomor Pendaftaran (internal, tidak dikirim)
        TANGGALAJU              date           NULL,             -- dikirim; bawaan hari ini

        -- Perdagangan
        HARGAPENYERAHAN         decimal(19,4)  NOT NULL DEFAULT 0,   -- = jumlah harga penyerahan barang
        NILAIJASA               decimal(19,4)  NOT NULL DEFAULT 0,   -- 19. Nilai Penggantian Jasa (diketik)
        UANGMUKA                decimal(19,4)  NOT NULL DEFAULT 0,   -- 20. Nilai Uang Muka (diketik)
        BRUTO                   decimal(19,4)  NOT NULL DEFAULT 0,
        NETTO                   decimal(19,4)  NOT NULL DEFAULT 0,   -- = jumlah netto barang
        VOLUME                  decimal(19,4)  NOT NULL DEFAULT 0,   -- = jumlah volume barang

        -- Entitas 1 Pengusaha TPB, 2 Pemilik Barang, 3 Pengirim Barang. Yang DIKIRIM tetap data
        -- master ENTITAS lewat SELECTENTITASn; ini salinan isian di dokumen.
        NOMORIDENTITASENTITAS1  varchar(50)    NULL,
        NAMAENTITAS1            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS1          varchar(1000)  NOT NULL DEFAULT '',
        NOMORIJINENTITAS1       varchar(50)    NULL,
        NOMORIDENTITASENTITAS2  varchar(50)    NULL,
        NAMAENTITAS2            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS2          varchar(1000)  NOT NULL DEFAULT '',
        NOMORIDENTITASENTITAS3  varchar(50)    NULL,
        NAMAENTITAS3            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS3          varchar(1000)  NOT NULL DEFAULT '',
        SELECTENTITAS1          int            NULL,             -- ENTITAS.id
        SELECTENTITAS2          int            NULL,
        SELECTENTITAS3          int            NULL,

        -- Pengangkut (satu): 16. Jenis Sarana Pengangkut (mis. TRUCK), 17. No Polisi
        NAMAPENGANGKUT          varchar(20)    NOT NULL DEFAULT '',
        NOMORPENGANGKUT         varchar(50)    NOT NULL DEFAULT '',

        -- Kemasan (satu)
        KODEJENISKEMASAN        varchar(20)    NOT NULL DEFAULT '',
        JUMLAHKEMASAN           int            NOT NULL DEFAULT 0,
        MERKKEMASAN             varchar(50)    NOT NULL DEFAULT '',

        -- Pengesahan
        KOTATTD                 varchar(100)   NULL,
        TANGGALTTD              date           NULL,
        NAMATTD                 varchar(100)   NULL,
        JABATANTTD              varchar(50)    NULL,

        USERBUAT                varchar(50)    NULL,
        TGLBUAT                 datetime       NOT NULL DEFAULT GETDATE(),
        USERUBAH                varchar(50)    NULL,
        TGLUBAH                 datetime       NULL
    );

    CREATE UNIQUE INDEX UX_DOCUMENTBC40_NOBUKTI ON dbo.DOCUMENTBC40 (NOBUKTI);
    CREATE INDEX IX_DOCUMENTBC40_TANGGALAJU ON dbo.DOCUMENTBC40 (TANGGALAJU);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC40BARANG', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC40BARANG (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC40              int            NOT NULL,
        SERIBARANG          int            NOT NULL,
        POSTARIF            varchar(50)    NOT NULL DEFAULT '',
        KODEBARANG          varchar(25)    NOT NULL DEFAULT '',  -- DBPODET.KODEBRG (varchar 25)
        URAIAN              varchar(100)   NOT NULL DEFAULT '',
        MERK                varchar(100)   NOT NULL DEFAULT '',  -- tanpa isian di layar: selalu ''
        TIPE                varchar(100)   NOT NULL DEFAULT '',
        UKURAN              varchar(100)   NOT NULL DEFAULT '',
        SPESIFIKASILAIN     varchar(100)   NOT NULL DEFAULT '',
        KODESATUANBARANG    varchar(20)    NOT NULL DEFAULT '',
        JUMLAHSATUAN        decimal(19,4)  NOT NULL DEFAULT 0,
        KODEJENISKEMASAN    varchar(20)    NOT NULL DEFAULT '',
        JUMLAHKEMASAN       int            NOT NULL DEFAULT 0,
        NETTO               decimal(19,4)  NOT NULL DEFAULT 0,
        VOLUME              decimal(19,4)  NOT NULL DEFAULT 0,
        HARGAPENYERAHAN     decimal(19,4)  NOT NULL DEFAULT 0    -- Rupiah; dikirim juga sebagai jumlahRealisasi
    );

    CREATE INDEX IX_DOCUMENTBC40BARANG_IDBC40 ON dbo.DOCUMENTBC40BARANG (IDBC40);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC40DOKUMEN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC40DOKUMEN (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC40              int            NOT NULL,
        SERIDOKUMEN         int            NOT NULL,
        KODEDOKUMEN         varchar(20)    NOT NULL DEFAULT '',  -- semua jenis dokumen (tanpa batas 380 / 705)
        NOMORDOKUMEN        varchar(100)   NULL,
        TANGGALDOKUMEN      date           NULL
    );

    CREATE INDEX IX_DOCUMENTBC40DOKUMEN_IDBC40 ON dbo.DOCUMENTBC40DOKUMEN (IDBC40);
END
GO
