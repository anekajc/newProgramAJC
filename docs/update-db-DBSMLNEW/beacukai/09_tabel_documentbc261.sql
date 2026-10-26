-- 09 - Tabel Dokumen BC 2.6.1 (header + 6 koleksi) dan token CEISA per user. Dibuat 26 Oktober 2026.
-- Rancangan acuan - DITINJAU dan DIJALANKAN MANUAL oleh user di DBSMLNEW (bukan php artisan migrate).
-- Kolom mengikuti docs/documentbc261_plan.md §2. Nama tabel dipakai lewat config/beacukai.php
-- (tables.*), jadi kalau nama tabel diubah di sini, ubah juga di sana. Nama kolom dipakai langsung
-- oleh app/Http/Controllers/DocumentBc/DocumentBc261Controller.php.
--
-- Anak dihubungkan lewat IDBC261 = DOCUMENTBC261.ID; dihapus eksplisit oleh aplikasi (tanpa cascade).
-- Kolom teks NOT NULL berisi '' bila kosong; angka desimal(19,4) NOT NULL bawaan 0.
-- Aman dijalankan ulang (tabel yang sudah ada dilewati).

USE DBSMLNEW
GO

IF OBJECT_ID('dbo.DOCUMENTBC261', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC261 (
        ID                      int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        SOURCEDOCUMENTID        int            NULL,             -- dokumen sumber (Outstanding); NULL = standalone
        SENT                    int            NULL,             -- 1 = sudah diterima CEISA (status OK)
        IDHEADER                varchar(100)   NULL,             -- idHeader balasan CEISA
        TGLKIRIM                datetime       NULL,
        USERKIRIM               varchar(50)    NULL,

        NOMORAJU                varchar(100)   NOT NULL DEFAULT '',
        KODEKANTOR              varchar(20)    NOT NULL DEFAULT '',
        KODETUJUANPENGIRIMAN    varchar(20)    NOT NULL DEFAULT '',
        KODEVALUTA              varchar(20)    NOT NULL DEFAULT '',
        NDPBM                   decimal(19,4)  NOT NULL DEFAULT 0,
        CIF                     decimal(19,4)  NOT NULL DEFAULT 0,
        BRUTO                   decimal(19,4)  NOT NULL DEFAULT 0,
        NETTO                   decimal(19,4)  NOT NULL DEFAULT 0,
        KODECARAANGKUT          varchar(20)    NOT NULL DEFAULT '',

        -- Internal, tidak dikirim ke CEISA
        NOREGBC                 varchar(50)    NULL,
        TGLBC                   date           NULL,
        TGLKELUAR               date           NULL,

        -- Salinan isian entitas di dokumen (yang DIKIRIM tetap data master ENTITAS lewat SELECTENTITASn)
        NOMORIDENTITASENTITAS1  varchar(50)    NULL,             -- Pengusaha TPB
        NAMAENTITAS1            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS1          varchar(1000)  NOT NULL DEFAULT '',
        NOMORIJINENTITAS1       varchar(50)    NULL,
        TANGGALIJINENTITAS1     date           NULL,
        NOMORIDENTITASENTITAS2  varchar(50)    NULL,             -- Pemilik Barang
        NAMAENTITAS2            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS2          varchar(1000)  NOT NULL DEFAULT '',
        NOMORIDENTITASENTITAS3  varchar(50)    NULL,             -- Penerima Barang
        NAMAENTITAS3            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS3          varchar(1000)  NOT NULL DEFAULT '',
        SELECTENTITAS1          int            NULL,             -- ENTITAS.id
        SELECTENTITAS2          int            NULL,
        SELECTENTITAS3          int            NULL,

        -- Pernyataan (tanda tangan); TANGGALTTD juga dikirim sebagai tanggalAju
        KOTATTD                 varchar(100)   NOT NULL DEFAULT '',
        TANGGALTTD              date           NULL,
        NAMATTD                 varchar(100)   NOT NULL DEFAULT '',
        JABATANTTD              varchar(100)   NOT NULL DEFAULT '',

        USERBUAT                varchar(50)    NULL,
        TGLBUAT                 datetime       NOT NULL DEFAULT GETDATE(),
        USERUBAH                varchar(50)    NULL,
        TGLUBAH                 datetime       NULL
    );

    CREATE INDEX IX_DOCUMENTBC261_TANGGALTTD ON dbo.DOCUMENTBC261 (TANGGALTTD);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC261BARANG', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC261BARANG (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC261             int            NOT NULL,
        SERIBARANG          int            NOT NULL,
        POSTARIF            varchar(100)   NOT NULL DEFAULT '',
        KODEBARANG          varchar(20)    NOT NULL DEFAULT '',
        URAIAN              varchar(100)   NOT NULL DEFAULT '',
        MERK                varchar(100)   NULL,
        TIPE                varchar(100)   NULL,
        UKURAN              varchar(100)   NULL,
        SPESIFIKASILAIN     varchar(100)   NULL,
        KODESATUANBARANG    varchar(20)    NOT NULL DEFAULT '',
        JUMLAHSATUAN        decimal(19,4)  NOT NULL DEFAULT 0,
        JUMLAHKEMASAN       decimal(19,4)  NOT NULL DEFAULT 0,
        KODEJENISKEMASAN    varchar(20)    NULL,
        KODEDOKUMEN         varchar(20)    NOT NULL DEFAULT '261',
        NETTO               decimal(19,4)  NOT NULL DEFAULT 0,
        CIF                 decimal(19,4)  NOT NULL DEFAULT 0,
        CIFRUPIAH           decimal(19,4)  NOT NULL DEFAULT 0,
        NDPBM               decimal(19,4)  NOT NULL DEFAULT 0,
        KODENEGARAASAL      varchar(5)     NOT NULL DEFAULT '',
        KODEKATEGORIBARANG  varchar(20)    NULL,                 -- "Daerah Asal Barang"
        KETERANGANDOKUMEN   varchar(100)   NOT NULL DEFAULT '',
        PERSENBM            decimal(19,4)  NOT NULL DEFAULT 0,
        PERSENPPN           decimal(19,4)  NOT NULL DEFAULT 0,
        PERSENPPH           decimal(19,4)  NOT NULL DEFAULT 0,
        NILAIBM             decimal(19,4)  NOT NULL DEFAULT 0,
        NILAIPPN            decimal(19,4)  NOT NULL DEFAULT 0,
        NILAIPPH            decimal(19,4)  NOT NULL DEFAULT 0,
        HARGAPENYERAHAN     decimal(19,4)  NULL,
        HARGAPEROLEHAN      decimal(19,4)  NULL,
        HARGAEKSPOR         decimal(19,4)  NULL,
        HARGASATUAN         decimal(19,4)  NULL,
        DISKON              decimal(19,4)  NULL,
        FREIGHT             decimal(19,4)  NULL,
        ISIPERKEMASAN       decimal(19,4)  NULL,
        KODEPERHITUNGAN     varchar(20)    NULL
    );

    CREATE INDEX IX_DOCUMENTBC261BARANG_IDBC261 ON dbo.DOCUMENTBC261BARANG (IDBC261);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC261BAHANBAKU', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC261BAHANBAKU (
        ID                      int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC261                 int            NOT NULL,
        SERIBARANG              int            NOT NULL,         -- seri barang pemilik
        SERIBAHANBAKU           int            NOT NULL,
        KODEASALBAHANBAKU       varchar(1)     NULL,             -- '0' Impor, '1' Lokal
        POSTARIF                varchar(100)   NOT NULL DEFAULT '',
        KODEBARANG              varchar(20)    NOT NULL DEFAULT '',
        URAIANBARANG            varchar(100)   NOT NULL DEFAULT '',
        MERKBARANG              varchar(100)   NULL,
        TIPEBARANG              varchar(100)   NULL,
        UKURANBARANG            varchar(100)   NULL,
        SPESIFIKASILAINBARANG   varchar(100)   NULL,
        KODESATUANBARANG        varchar(20)    NOT NULL DEFAULT '',
        JUMLAHSATUAN            decimal(19,4)  NOT NULL DEFAULT 0,
        KODEDOKUMEN             varchar(20)    NOT NULL DEFAULT '', -- dokumen ASAL: 23 / 27 / 40
        KODEKANTORASAL          varchar(20)    NOT NULL DEFAULT '',
        NOMORDAFTARDOKASAL      varchar(50)    NOT NULL DEFAULT '',
        TANGGALDAFTARDOKASAL    date           NULL,
        NOMORAJUDOKASAL         varchar(100)   NULL,             -- disimpan, tidak dikirim
        SERIBARANGDOKASAL       int            NULL,             -- disimpan, tidak dikirim (dikirim 1)
        CIF                     decimal(19,4)  NOT NULL DEFAULT 0,
        CIFRUPIAH               decimal(19,4)  NOT NULL DEFAULT 0,
        HARGAPENYERAHAN         decimal(19,4)  NOT NULL DEFAULT 0,
        HARGAPEROLEHAN          decimal(19,4)  NOT NULL DEFAULT 0,
        NILAIJASA               decimal(19,4)  NOT NULL DEFAULT 0,
        NETTO                   decimal(19,4)  NULL,
        NDPBM                   decimal(19,4)  NULL,
        SERIIJIN                int            NULL
    );

    CREATE INDEX IX_DOCUMENTBC261BAHANBAKU_IDBC261 ON dbo.DOCUMENTBC261BAHANBAKU (IDBC261);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC261KEMASAN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC261KEMASAN (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC261             int            NOT NULL,
        SERIKEMASAN         varchar(10)    NOT NULL,             -- dikirim sebagai string ("1", "2", ...)
        JUMLAHKEMASAN       int            NOT NULL DEFAULT 0,
        KODEJENISKEMASAN    varchar(20)    NULL,
        MERKKEMASAN         varchar(100)   NOT NULL DEFAULT ''
    );

    CREATE INDEX IX_DOCUMENTBC261KEMASAN_IDBC261 ON dbo.DOCUMENTBC261KEMASAN (IDBC261);
END
GO

-- Peti kemas: disimpan, TIDAK dikirim ke CEISA (sama dengan Villiger, quirk Q-a).
IF OBJECT_ID('dbo.DOCUMENTBC261KONTAINER', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC261KONTAINER (
        ID                      int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC261                 int            NOT NULL,
        SERIKONTAINER           int            NOT NULL,
        NOMORKONTAINER          varchar(20)    NULL,
        KODEUKURANKONTAINER     varchar(20)    NOT NULL DEFAULT '',
        KODEJENISKONTAINER      varchar(20)    NOT NULL DEFAULT '', -- Jenis Muatan: 4 Empty / 7 LCL / 8 FCL
        KODETIPEKONTAINER       varchar(20)    NOT NULL DEFAULT ''
    );

    CREATE INDEX IX_DOCUMENTBC261KONTAINER_IDBC261 ON dbo.DOCUMENTBC261KONTAINER (IDBC261);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC261JAMINAN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC261JAMINAN (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC261             int            NOT NULL,
        KODEJENISJAMINAN    varchar(20)    NULL,
        NOMORJAMINAN        varchar(50)    NULL,
        TANGGALJAMINAN      date           NULL,
        NILAIJAMINAN        bigint         NULL,                 -- bulat (desimal dibuang)
        TANGGALJATUHTEMPO   date           NULL,
        PENJAMIN            varchar(100)   NULL,
        NOMORBPJ            varchar(50)    NULL,
        TANGGALBPJ          date           NULL
    );

    CREATE INDEX IX_DOCUMENTBC261JAMINAN_IDBC261 ON dbo.DOCUMENTBC261JAMINAN (IDBC261);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC261DOKUMEN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC261DOKUMEN (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC261             int            NOT NULL,
        SERIDOKUMEN         int            NOT NULL,
        KODEDOKUMEN         varchar(20)    NOT NULL DEFAULT '',   -- JENISDOKUMEN.KODEJENISDOKUMEN
        NOMORDOKUMEN        varchar(50)    NULL,
        TANGGALDOKUMEN      date           NULL
    );

    CREATE INDEX IX_DOCUMENTBC261DOKUMEN_IDBC261 ON dbo.DOCUMENTBC261DOKUMEN (IDBC261);
END
GO

-- Token login CEISA per user - satu tabel untuk semua dokumen BC. Password tidak pernah disimpan.
IF OBJECT_ID('dbo.CEISATOKEN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.CEISATOKEN (
        USERID      varchar(50)   NOT NULL PRIMARY KEY,         -- users.username
        BEARERTOKEN varchar(max)  NOT NULL,
        EXPIRESAT   datetime      NOT NULL,                     -- UTC
        TGLBUAT     datetime      NOT NULL DEFAULT GETDATE(),
        TGLUBAH     datetime      NULL
    );
END
GO

-- Cek hasil (harus 8 baris)
SELECT name FROM sys.tables
WHERE name IN ('DOCUMENTBC261', 'DOCUMENTBC261BARANG', 'DOCUMENTBC261BAHANBAKU', 'DOCUMENTBC261KEMASAN',
               'DOCUMENTBC261KONTAINER', 'DOCUMENTBC261JAMINAN', 'DOCUMENTBC261DOKUMEN', 'CEISATOKEN')
ORDER BY name;
