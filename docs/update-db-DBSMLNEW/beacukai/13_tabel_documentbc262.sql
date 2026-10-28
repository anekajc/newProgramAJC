-- 13 - Tabel Dokumen BC 2.6.2 (header + 6 koleksi). Dibuat 28 Oktober 2026.
-- Rancangan acuan - DITINJAU dan DIJALANKAN MANUAL oleh user di DBSMLNEW (bukan php artisan migrate).
-- Kolom mengikuti docs/documentbc262_plan.md §2 (BC 2.6.2 = cermin BC 2.6.1, lihat 09_tabel_documentbc261.sql)
-- ditambah perbaikan yang disepakati: salinan lengkap entitas di dokumen (Q-f), IDHEADER CEISA (Q-e),
-- seriBarangDokAsal / seriBahanBaku tersimpan dan dikirim apa adanya (Q-i). Nama tabel dipakai lewat
-- config/beacukai.php (tables.*), jadi kalau nama tabel diubah di sini, ubah juga di sana.
--
-- Anak dihubungkan lewat IDBC262 = DOCUMENTBC262.ID; dihapus eksplisit oleh aplikasi (tanpa cascade).
-- Kolom teks NOT NULL berisi '' bila kosong; angka desimal(19,4) NOT NULL bawaan 0.
-- Tabel CEISATOKEN sudah dibuat oleh script 09 dan dipakai bersama - tidak dibuat ulang di sini.
-- Aman dijalankan ulang (tabel yang sudah ada dilewati).

USE DBSMLNEW
GO

IF OBJECT_ID('dbo.DOCUMENTBC262', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC262 (
        ID                      int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC261                 int            NULL,             -- DOCUMENTBC261.ID (Outstanding); NULL = standalone
        SENT                    int            NULL,             -- 1 = sudah diterima CEISA (status OK); dokumen terkunci
        IDHEADER                varchar(100)   NULL,             -- idHeader balasan CEISA
        TGLKIRIM                datetime       NULL,
        USERKIRIM               varchar(50)    NULL,

        NOMORAJU                varchar(100)   NOT NULL DEFAULT '',
        KODEKANTOR              varchar(20)    NOT NULL DEFAULT '',
        KODETUJUANPEMASUKAN     varchar(20)    NOT NULL DEFAULT '', -- 1..6 (dikirim sebagai kodeTujuanPemasukan)
        KODEVALUTA              varchar(20)    NOT NULL DEFAULT '',
        NDPBM                   decimal(19,4)  NOT NULL DEFAULT 0,
        CIF                     decimal(19,4)  NOT NULL DEFAULT 0,
        BRUTO                   decimal(19,4)  NOT NULL DEFAULT 0,
        NETTO                   decimal(19,4)  NOT NULL DEFAULT 0,
        KODECARAANGKUT          varchar(20)    NOT NULL DEFAULT '', -- 1..9, bawaan 3 (Darat)

        -- Internal, tidak dikirim ke CEISA
        NOREGBC                 varchar(50)    NULL,
        TGLBC                   date           NULL,
        ARRDATE                 date           NULL,             -- Tanggal Pemasukan

        -- Pungutan (diketik manual di tab Pungutan). Dikirim ke CEISA sebagai array pungutan,
        -- satu objek per jenis yang nilainya > 0 (NILAITOTALPUNGUTAN hanya penjumlahan, tidak dikirim).
        NILAIBM                 decimal(19,4)  NOT NULL DEFAULT 0,   -- BM
        NILAIBMAD               decimal(19,4)  NOT NULL DEFAULT 0,   -- BMAD
        NILAIBMI                decimal(19,4)  NOT NULL DEFAULT 0,   -- BMI
        NILAIBMTP               decimal(19,4)  NOT NULL DEFAULT 0,   -- BMTP
        NILAIBMP                decimal(19,4)  NOT NULL DEFAULT 0,   -- BMP
        NILAICUKAIEA            decimal(19,4)  NOT NULL DEFAULT 0,   -- CEA
        NILAICUKAICMEA          decimal(19,4)  NOT NULL DEFAULT 0,   -- CMEA
        NILAICUKAICTEM          decimal(19,4)  NOT NULL DEFAULT 0,   -- CTEM
        NILAIPPN                decimal(19,4)  NOT NULL DEFAULT 0,   -- PPN
        NILAIPPNBM              decimal(19,4)  NOT NULL DEFAULT 0,   -- PPNBM
        NILAIPPH                decimal(19,4)  NOT NULL DEFAULT 0,   -- PPH
        NILAITOTALPUNGUTAN      decimal(19,4)  NOT NULL DEFAULT 0,

        -- Entitas 1 = Pengusaha TPB (seri 3), 2 = Pemilik Barang (seri 7), 3 = Pengirim Barang (seri 9).
        -- Nama / alamat / nomor identitas = isian user. SELECTENTITASn = ENTITAS.id yang dipilih.
        -- Kolom KODE* / NIB* di bawahnya = SALINAN data master ENTITAS yang diambil server saat
        -- simpan, supaya dokumen yang sudah dikirim tidak berubah bila master diubah (Q-f).
        NOMORIDENTITASENTITAS1  varchar(50)    NULL,
        NAMAENTITAS1            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS1          varchar(1000)  NOT NULL DEFAULT '',
        NOMORIJINENTITAS1       varchar(50)    NULL,
        TANGGALIJINENTITAS1     date           NULL,
        NOMORIDENTITASENTITAS2  varchar(50)    NULL,
        NAMAENTITAS2            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS2          varchar(1000)  NOT NULL DEFAULT '',
        NOMORIDENTITASENTITAS3  varchar(50)    NULL,
        NAMAENTITAS3            varchar(100)   NOT NULL DEFAULT '',
        ALAMATENTITAS3          varchar(1000)  NOT NULL DEFAULT '',
        NOMORIJINENTITAS3       varchar(50)    NULL,             -- salinan master (tidak diketik)
        TANGGALIJINENTITAS3     date           NULL,             -- salinan master (tidak diketik)
        SELECTENTITAS1          int            NULL,
        SELECTENTITAS2          int            NULL,
        SELECTENTITAS3          int            NULL,

        KODEJENISAPIENTITAS1        varchar(10)  NULL,
        KODEJENISIDENTITASENTITAS1  varchar(10)  NULL,
        KODESTATUSENTITAS1          varchar(10)  NULL,
        NIBENTITAS1                 varchar(50)  NULL,
        KODENEGARAENTITAS1          varchar(10)  NULL,
        KODEJENISAPIENTITAS2        varchar(10)  NULL,
        KODEJENISIDENTITASENTITAS2  varchar(10)  NULL,
        KODESTATUSENTITAS2          varchar(10)  NULL,
        NIBENTITAS2                 varchar(50)  NULL,
        KODENEGARAENTITAS2          varchar(10)  NULL,
        KODEJENISAPIENTITAS3        varchar(10)  NULL,
        KODEJENISIDENTITASENTITAS3  varchar(10)  NULL,
        KODESTATUSENTITAS3          varchar(10)  NULL,
        NIBENTITAS3                 varchar(50)  NULL,
        KODENEGARAENTITAS3          varchar(10)  NULL,

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

    CREATE INDEX IX_DOCUMENTBC262_TANGGALTTD ON dbo.DOCUMENTBC262 (TANGGALTTD);

    -- Satu BC 2.6.1 hanya boleh diselesaikan satu BC 2.6.2 (standalone = NULL, boleh banyak).
    CREATE UNIQUE INDEX UX_DOCUMENTBC262_IDBC261 ON dbo.DOCUMENTBC262 (IDBC261) WHERE IDBC261 IS NOT NULL;
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC262BARANG', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC262BARANG (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC262             int            NOT NULL,
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
        KODEDOKUMEN         varchar(20)    NOT NULL DEFAULT '262',
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
        HARGAPENYERAHAN     decimal(19,4)  NULL,
        HARGAPEROLEHAN      decimal(19,4)  NULL,
        HARGAEKSPOR         decimal(19,4)  NULL,
        HARGASATUAN         decimal(19,4)  NULL,
        DISKON              decimal(19,4)  NULL,
        FREIGHT             decimal(19,4)  NULL,
        ISIPERKEMASAN       decimal(19,4)  NULL,
        KODEPERHITUNGAN     varchar(20)    NULL
    );

    CREATE INDEX IX_DOCUMENTBC262BARANG_IDBC262 ON dbo.DOCUMENTBC262BARANG (IDBC262);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC262BAHANBAKU', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC262BAHANBAKU (
        ID                      int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC262                 int            NOT NULL,
        SERIBARANG              int            NOT NULL,         -- seri barang pemilik
        SERIBAHANBAKU           int            NOT NULL,         -- dikirim apa adanya (Q-i)
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
        NETTO                   decimal(19,4)  NOT NULL DEFAULT 0,   -- isian di BC 2.6.2, dikirim
        KODEDOKUMEN             varchar(20)    NOT NULL DEFAULT '', -- dokumen ASAL: 23 / 27 / 40 (kosong = 262 saat kirim)
        KODEKANTORASAL          varchar(20)    NOT NULL DEFAULT '',
        NOMORDAFTARDOKASAL      varchar(50)    NOT NULL DEFAULT '',
        TANGGALDAFTARDOKASAL    date           NULL,
        NOMORAJUDOKASAL         varchar(100)   NULL,             -- dikirim di BC 2.6.2
        SERIBARANGDOKASAL       int            NULL,             -- dikirim apa adanya, kosong = 1 (Q-i)
        CIF                     decimal(19,4)  NOT NULL DEFAULT 0,
        CIFRUPIAH               decimal(19,4)  NOT NULL DEFAULT 0,
        HARGAPENYERAHAN         decimal(19,4)  NOT NULL DEFAULT 0,
        HARGAPEROLEHAN          decimal(19,4)  NOT NULL DEFAULT 0,
        NILAIJASA               decimal(19,4)  NOT NULL DEFAULT 0
    );

    CREATE INDEX IX_DOCUMENTBC262BAHANBAKU_IDBC262 ON dbo.DOCUMENTBC262BAHANBAKU (IDBC262);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC262KEMASAN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC262KEMASAN (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC262             int            NOT NULL,
        SERIKEMASAN         int            NOT NULL,             -- dikirim sebagai angka (BC 2.6.1: teks)
        JUMLAHKEMASAN       int            NOT NULL DEFAULT 0,
        KODEJENISKEMASAN    varchar(20)    NULL,
        MERKKEMASAN         varchar(100)   NOT NULL DEFAULT ''
    );

    CREATE INDEX IX_DOCUMENTBC262KEMASAN_IDBC262 ON dbo.DOCUMENTBC262KEMASAN (IDBC262);
END
GO

-- Peti kemas: di BC 2.6.2 DIKIRIM ke CEISA (array kontainer).
IF OBJECT_ID('dbo.DOCUMENTBC262KONTAINER', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC262KONTAINER (
        ID                      int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC262                 int            NOT NULL,
        SERIKONTAINER           int            NOT NULL,
        NOMORKONTAINER          varchar(20)    NULL,
        KODEUKURANKONTAINER     varchar(20)    NOT NULL DEFAULT '',
        KODEJENISKONTAINER      varchar(20)    NOT NULL DEFAULT '', -- Jenis Muatan: 4 Empty / 7 LCL / 8 FCL
        KODETIPEKONTAINER       varchar(20)    NOT NULL DEFAULT ''
    );

    CREATE INDEX IX_DOCUMENTBC262KONTAINER_IDBC262 ON dbo.DOCUMENTBC262KONTAINER (IDBC262);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC262JAMINAN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC262JAMINAN (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC262             int            NOT NULL,
        KODEJENISJAMINAN    varchar(20)    NULL,
        NOMORJAMINAN        varchar(50)    NULL,
        TANGGALJAMINAN      date           NULL,
        NILAIJAMINAN        bigint         NULL,                 -- bulat (desimal dibuang)
        TANGGALJATUHTEMPO   date           NULL,
        PENJAMIN            varchar(100)   NULL,
        NOMORBPJ            varchar(50)    NULL,
        TANGGALBPJ          date           NULL
    );

    CREATE INDEX IX_DOCUMENTBC262JAMINAN_IDBC262 ON dbo.DOCUMENTBC262JAMINAN (IDBC262);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC262DOKUMEN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC262DOKUMEN (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC262             int            NOT NULL,
        SERIDOKUMEN         int            NOT NULL,
        KODEDOKUMEN         varchar(20)    NOT NULL DEFAULT '',   -- JENISDOKUMEN.KODEJENISDOKUMEN
        NOMORDOKUMEN        varchar(50)    NULL,
        TANGGALDOKUMEN      date           NULL
    );

    CREATE INDEX IX_DOCUMENTBC262DOKUMEN_IDBC262 ON dbo.DOCUMENTBC262DOKUMEN (IDBC262);
END
GO

-- Cek hasil (harus 7 baris)
SELECT name FROM sys.tables
WHERE name IN ('DOCUMENTBC262', 'DOCUMENTBC262BARANG', 'DOCUMENTBC262BAHANBAKU', 'DOCUMENTBC262KEMASAN',
               'DOCUMENTBC262KONTAINER', 'DOCUMENTBC262JAMINAN', 'DOCUMENTBC262DOKUMEN')
ORDER BY name;
