-- 11 - Tabel Dokumen BC 2.3 (header + barang + dokumen). Dibuat 27 Oktober 2026.
-- Rancangan acuan - DITINJAU dan DIJALANKAN MANUAL oleh user di DBSMLNEW (bukan php artisan migrate).
-- Kolom mengikuti docs/documentbc23_plan.md §2. Nama tabel dipakai lewat config/beacukai.php
-- (tables.bc23*), jadi kalau nama tabel diubah di sini, ubah juga di sana. Nama kolom dipakai langsung
-- oleh app/Http/Controllers/DocumentBc/DocumentBc23Controller.php. Token CEISA memakai tabel CEISATOKEN
-- dari skrip 09 (satu tabel untuk semua dokumen BC) - tidak dibuat lagi di sini.
--
-- SUMBER DOKUMEN = PO (DBPO / DBPODET): NOBUKTI = DBPO.NOBUKTI. DBPO dan DBPODET TIDAK diubah.
-- PO yang pemasoknya luar negeri (DBCUSTSUPP.NEGARA <> 'INDONESIA'), tidak batal dan sudah
-- IsOtorisasi1 muncul di Outstanding selama BELUM punya baris di DOCUMENTBC23. Satu PO = satu BC 2.3
-- (NOBUKTI unik); hapus dokumennya = PO kembali ke Outstanding.
--
-- Status dokumen ada di tabel ini: BCTYPE ('23'), SENT (NULL/0 = siap dikirim, 1 = sudah diterima
-- CEISA), NOMORDOKUMENPABEAN + TANGGALDOKUMENPABEAN (nomor + tanggal daftar BC, tidak dikirim).
-- Kemasan, kontainer dan pengangkut hanya satu per dokumen, jadi menjadi kolom di header.
-- Anak dihubungkan lewat IDBC23 = DOCUMENTBC23.ID; dihapus eksplisit oleh aplikasi (tanpa cascade).
-- Kolom teks NOT NULL berisi '' bila kosong; angka desimal(19,4) NOT NULL bawaan 0.
-- Kolom teks header yang NULL-able (NOMORAJU, KODEKANTOR, NOMORBC11, POSBC11, SUBPOSBC11, KOTATTD,
-- NAMATTD, JABATANTTD) memang dikirim sebagai null ke CEISA bila kosong (sama seperti Villiger).
-- Aman dijalankan ulang (tabel yang sudah ada dilewati).

USE DBSMLNEW
GO

IF OBJECT_ID('dbo.DOCUMENTBC23', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC23 (
        ID                      int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        NOBUKTI                 varchar(20)    NOT NULL,         -- DBPO.NOBUKTI (sumber); unik
        BCTYPE                  varchar(10)    NOT NULL DEFAULT '23',
        SENT                    int            NULL,             -- 1 = sudah diterima CEISA (status OK)
        IDHEADER                varchar(100)   NULL,             -- idHeader balasan CEISA
        TGLKIRIM                datetime       NULL,
        USERKIRIM               varchar(50)    NULL,

        NOMORAJU                varchar(100)   NULL,
        KODEKANTOR              varchar(20)    NULL,
        KODEKANTORBONGKAR       varchar(20)    NOT NULL DEFAULT '',
        KODETUJUANTPB           varchar(20)    NOT NULL DEFAULT '',

        -- D. Diisi oleh Bea dan Cukai (internal, tidak dikirim ke CEISA)
        NOMORDOKUMENPABEAN      varchar(50)    NULL,
        TANGGALDOKUMENPABEAN    date           NULL,

        -- BC 1.1
        NOMORBC11               varchar(50)    NULL,
        TANGGALBC11             date           NULL,
        POSBC11                 varchar(50)    NULL,
        SUBPOSBC11              varchar(50)    NULL,

        -- Pelabuhan (kode dari master PELABUHAN) dan TPS
        KODEPELMUAT             varchar(20)    NOT NULL DEFAULT '',
        KODEPELTRANSIT          varchar(20)    NOT NULL DEFAULT '',
        KODEPELBONGKAR          varchar(20)    NOT NULL DEFAULT '',
        KODETPS                 varchar(20)    NOT NULL DEFAULT '',
        KETTPS                  varchar(200)   NULL,             -- catatan, tidak dikirim
        TANGGALTIBA             date           NULL,             -- wajib terisi saat Kirim (Q-a)

        -- Harga
        KODEVALUTA              varchar(20)    NOT NULL DEFAULT '',
        NDPBM                   decimal(19,4)  NOT NULL DEFAULT 0,
        KODEINCOTERM            varchar(20)    NOT NULL DEFAULT '',
        FOB                     decimal(19,4)  NOT NULL DEFAULT 0,
        FREIGHT                 decimal(19,4)  NOT NULL DEFAULT 0,
        ASURANSI                decimal(19,4)  NOT NULL DEFAULT 0,
        KODEASURANSI            varchar(5)     NOT NULL DEFAULT 'LN',
        CIF                     decimal(19,4)  NOT NULL DEFAULT 0,   -- = jumlah CIF barang
        BRUTO                   decimal(19,4)  NOT NULL DEFAULT 0,
        NETTO                   decimal(19,4)  NOT NULL DEFAULT 0,   -- = jumlah netto barang

        -- Entitas 1 Importir / Pengusaha TPB, 2 Pemasok, 3 Pemilik Barang. Yang DIKIRIM tetap data
        -- master ENTITAS lewat SELECTENTITASn; ini salinan isian di dokumen.
        NOMORIDENTITASENTITAS1  varchar(50)    NULL,
        NAMAENTITAS1            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS1          varchar(1000)  NOT NULL DEFAULT '',
        NOMORIJINENTITAS1       varchar(50)    NULL,
        NIBENTITAS1             varchar(50)    NULL,
        NAMAENTITAS2            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS2          varchar(1000)  NOT NULL DEFAULT '',
        KODENEGARAENTITAS2      varchar(5)     NOT NULL DEFAULT '',
        NOMORIDENTITASENTITAS3  varchar(50)    NULL,
        NAMAENTITAS3            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS3          varchar(1000)  NOT NULL DEFAULT '',
        SELECTENTITAS1          int            NULL,             -- ENTITAS.id
        SELECTENTITAS2          int            NULL,
        SELECTENTITAS3          int            NULL,

        -- Pengangkut (satu)
        KODECARAANGKUT          varchar(20)    NOT NULL DEFAULT '1',
        NAMAPENGANGKUT          varchar(200)   NOT NULL DEFAULT '',
        NOMORPENGANGKUT         varchar(200)   NOT NULL DEFAULT '',
        KODEBENDERA             varchar(5)     NOT NULL DEFAULT '',

        -- Kemasan (satu)
        JUMLAHKEMASAN           int            NOT NULL DEFAULT 0,
        KODEJENISKEMASAN        varchar(20)    NOT NULL DEFAULT '',
        MERKKEMASAN             varchar(100)   NOT NULL DEFAULT '',

        -- Peti kemas (satu; hanya dikirim bila NOMORKONTAINER terisi)
        NOMORKONTAINER          varchar(50)    NULL,
        KODEUKURANKONTAINER     varchar(20)    NOT NULL DEFAULT '',
        KODETIPEKONTAINER       varchar(20)    NOT NULL DEFAULT '',
        KODEJENISKONTAINER      varchar(20)    NOT NULL DEFAULT '',

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

    CREATE UNIQUE INDEX UX_DOCUMENTBC23_NOBUKTI ON dbo.DOCUMENTBC23 (NOBUKTI);
    CREATE INDEX IX_DOCUMENTBC23_TANGGALTTD ON dbo.DOCUMENTBC23 (TANGGALTTD);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC23BARANG', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC23BARANG (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC23              int            NOT NULL,
        SERIBARANG          int            NOT NULL,
        POSTARIF            varchar(100)   NOT NULL DEFAULT '',
        KODEBARANG          varchar(25)    NOT NULL DEFAULT '',  -- DBPODET.KODEBRG (varchar 25)
        URAIAN              varchar(100)   NOT NULL DEFAULT '',
        MERK                varchar(100)   NULL,
        TIPE                varchar(100)   NULL,
        UKURAN              varchar(100)   NULL,
        SPESIFIKASILAIN     varchar(100)   NULL,
        KODESATUANBARANG    varchar(20)    NOT NULL DEFAULT '',
        JUMLAHSATUAN        decimal(19,4)  NOT NULL DEFAULT 0,
        JUMLAHKEMASAN       decimal(19,4)  NOT NULL DEFAULT 0,
        KODEJENISKEMASAN    varchar(20)    NOT NULL DEFAULT '',
        KODEDOKUMEN         varchar(20)    NOT NULL DEFAULT '23',
        NETTO               decimal(19,4)  NOT NULL DEFAULT 0,
        CIF                 decimal(19,4)  NOT NULL DEFAULT 0,   -- nilai valuta dokumen
        CIFRUPIAH           decimal(19,4)  NOT NULL DEFAULT 0,   -- = CIF x NDPBM header
        NDPBM               decimal(19,4)  NOT NULL DEFAULT 0,
        FOB                 decimal(19,4)  NOT NULL DEFAULT 0,   -- total header disalin ke tiap barang (Q-g)
        ASURANSI            decimal(19,4)  NOT NULL DEFAULT 0,
        FREIGHT             decimal(19,4)  NOT NULL DEFAULT 0,
        KODEKATEGORIBARANG  varchar(20)    NOT NULL DEFAULT '',
        KODENEGARAASAL      varchar(5)     NOT NULL DEFAULT '',
        KETERANGANDOKUMEN   varchar(100)   NOT NULL DEFAULT '',
        PERSENBM            decimal(19,4)  NOT NULL DEFAULT 0,   -- dari master POSTARIFHS saat HS dipilih
        PERSENPPN           decimal(19,4)  NOT NULL DEFAULT 0,
        PERSENPPH           decimal(19,4)  NOT NULL DEFAULT 0,
        NILAIBM             decimal(19,4)  NOT NULL DEFAULT 0,   -- hitungan layar, tidak dikirim
        NILAIPPN            decimal(19,4)  NOT NULL DEFAULT 0,
        NILAIPPH            decimal(19,4)  NOT NULL DEFAULT 0
    );

    CREATE INDEX IX_DOCUMENTBC23BARANG_IDBC23 ON dbo.DOCUMENTBC23BARANG (IDBC23);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC23DOKUMEN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC23DOKUMEN (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC23              int            NOT NULL,
        SERIDOKUMEN         int            NOT NULL,
        KODEDOKUMEN         varchar(20)    NOT NULL DEFAULT '',  -- 380 Invoice, 705/740 BL/AWB, 999 SK, dst
        NOMORDOKUMEN        varchar(100)   NULL,
        TANGGALDOKUMEN      date           NULL
    );

    CREATE INDEX IX_DOCUMENTBC23DOKUMEN_IDBC23 ON dbo.DOCUMENTBC23DOKUMEN (IDBC23);
END
GO
