-- 16 - Tabel Dokumen BC 4.1 (header + 5 koleksi). Dibuat 29 Oktober 2026.
-- Rancangan acuan - DITINJAU dan DIJALANKAN MANUAL oleh user di DBSMLNEW (bukan php artisan migrate).
-- Kolom mengikuti docs/documentbc41_plan.md §2: kembaran BC 2.5 (14_tabel_documentbc25.sql) TANPA KODECARABAYAR
-- dan TANPA 12 kolom pungutan (NILAIBM ... NILAITOTALPUNGUTAN), DITAMBAH KODEPEMBAYAR (Wajib Bayar = seri
-- entitas pembayar: '3' Pengusaha TPB, '7' Pemilik Barang, '8' Penerima Barang), NOMORBUKTIBAYAR dan
-- TANGGALBUKTIBAYAR. Seperti 2.5: IDHEADER CEISA disimpan, salinan lengkap entitas di dokumen, seriBarangDokAsal
-- tersimpan. Nama tabel dipakai lewat config/beacukai.php (tables.*), jadi kalau nama tabel diubah di sini,
-- ubah juga di sana.
--
-- Anak dihubungkan lewat IDBC41 = DOCUMENTBC41.ID; dihapus eksplisit oleh aplikasi (tanpa cascade).
-- Kolom teks NOT NULL berisi '' bila kosong; angka desimal(19,4) NOT NULL bawaan 0.
-- Tabel CEISATOKEN sudah dibuat oleh script 09 dan dipakai bersama - tidak dibuat ulang di sini.
-- Aman dijalankan ulang (tabel yang sudah ada dilewati).

USE DBSMLNEW
GO

IF OBJECT_ID('dbo.DOCUMENTBC41', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC41 (
        ID                      int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        SENT                    int            NULL,             -- 1 = sudah diterima CEISA (status OK); dokumen terkunci
        IDHEADER                varchar(100)   NULL,             -- idHeader balasan CEISA
        TGLKIRIM                datetime       NULL,
        USERKIRIM               varchar(50)    NULL,

        NOMORAJU                varchar(100)   NOT NULL DEFAULT '',
        KODEKANTOR              varchar(20)    NOT NULL DEFAULT '',
        KODEJENISTPB            varchar(10)    NOT NULL DEFAULT '1',  -- 1 Kawasan Berikat
        KODETUJUANPENGIRIMAN    varchar(20)    NOT NULL DEFAULT '',   -- 1..5, bawaan form 3 (Retur)
        KODECARAANGKUT          varchar(20)    NOT NULL DEFAULT '',   -- 1..9, bawaan form 3 (Darat)

        -- Internal, tidak dikirim ke CEISA
        NOREGBC                 varchar(200)   NULL,
        TGLBC                   date           NULL,
        ARRDATE                 date           NULL,             -- Tanggal Pengeluaran (tampil di daftar)

        -- Transaksi: Harga
        KODEVALUTA              varchar(20)    NOT NULL DEFAULT 'IDR',
        NDPBM                   decimal(19,4)  NOT NULL DEFAULT 1,
        CIF                     decimal(19,4)  NOT NULL DEFAULT 0,
        CIFRUPIAH               decimal(19,4)  NOT NULL DEFAULT 0,   -- Nilai Pabean = CIF x NDPBM (dihitung form)
        HARGAPENYERAHAN         decimal(19,4)  NOT NULL DEFAULT 0,
        -- Transaksi: Pajak (DPP / PPN / PPnBM dihitung form, server tidak menghitung ulang - plan §5.2)
        UANGMUKA                decimal(19,4)  NOT NULL DEFAULT 0,
        DISKON                  decimal(19,4)  NOT NULL DEFAULT 0,
        DASARPENGENAANPAJAK     decimal(19,4)  NOT NULL DEFAULT 0,
        TARIFPPNPAJAK           decimal(19,4)  NOT NULL DEFAULT 11,
        PPNPAJAK                decimal(19,4)  NOT NULL DEFAULT 0,
        TARIFPPNBMPAJAK         decimal(19,4)  NOT NULL DEFAULT 0,
        PPNBMPAJAK              decimal(19,4)  NOT NULL DEFAULT 0,
        -- Transaksi: Berat (bruto wajib > 0)
        BRUTO                   decimal(19,4)  NOT NULL DEFAULT 0,
        NETTO                   decimal(19,4)  NOT NULL DEFAULT 0,

        -- Pungutan: Lokasi Bayar selalu BANK (tidak disimpan). Wajib Bayar = seri entitas pembayar.
        KODEPEMBAYAR            varchar(10)    NULL,             -- '3' / '7' / '8' (wajib saat simpan)
        NOMORBUKTIBAYAR         varchar(50)    NULL,
        TANGGALBUKTIBAYAR       date           NULL,

        -- Entitas 1 = Pengusaha TPB (seri 3), 2 = Pemilik Barang (seri 7), 3 = Penerima Barang (seri 8).
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

    CREATE INDEX IX_DOCUMENTBC41_ARRDATE ON dbo.DOCUMENTBC41 (ARRDATE);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC41BARANG', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC41BARANG (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC41              int            NOT NULL,
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
        KODEDOKUMEN         varchar(20)    NOT NULL DEFAULT '41',
        NETTO               decimal(19,4)  NOT NULL DEFAULT 0,
        CIF                 decimal(19,4)  NOT NULL DEFAULT 0,   -- = CIFRUPIAH / NDPBM header (dihitung form)
        CIFRUPIAH           decimal(19,4)  NOT NULL DEFAULT 0,
        NDPBM               decimal(19,4)  NOT NULL DEFAULT 0,
        KODENEGARAASAL      varchar(5)     NOT NULL DEFAULT '',
        KETERANGANDOKUMEN   varchar(100)   NOT NULL DEFAULT '',
        PERSENBM            decimal(19,4)  NOT NULL DEFAULT 0,   -- tarif dari master HS saat HS dipilih
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

    CREATE INDEX IX_DOCUMENTBC41BARANG_IDBC41 ON dbo.DOCUMENTBC41BARANG (IDBC41);
END
GO

-- Bahan baku milik satu barang (SERIBARANG). Tanpa asal Impor/Lokal (dikirim "0"), tanpa tarif.
IF OBJECT_ID('dbo.DOCUMENTBC41BAHANBAKU', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC41BAHANBAKU (
        ID                      int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC41                  int            NOT NULL,
        SERIBARANG              int            NOT NULL,         -- seri barang pemilik
        SERIBAHANBAKU           int            NOT NULL,
        POSTARIF                varchar(100)   NOT NULL DEFAULT '',
        KODEBARANG              varchar(20)    NOT NULL DEFAULT '',
        URAIANBARANG            varchar(100)   NOT NULL DEFAULT '',
        MERKBARANG              varchar(100)   NULL,
        TIPEBARANG              varchar(100)   NULL,
        UKURANBARANG            varchar(100)   NULL,
        SPESIFIKASILAINBARANG   varchar(100)   NULL,
        KODESATUANBARANG        varchar(20)    NOT NULL DEFAULT '',
        JUMLAHSATUAN            decimal(19,4)  NOT NULL DEFAULT 0,
        NETTO                   decimal(19,4)  NOT NULL DEFAULT 0,
        KODEDOKUMEN             varchar(20)    NOT NULL DEFAULT '', -- dokumen ASAL: 23 / 27 / 40
        KODEKANTORASAL          varchar(20)    NOT NULL DEFAULT '',
        NOMORDAFTARDOKASAL      varchar(50)    NOT NULL DEFAULT '',
        TANGGALDAFTARDOKASAL    date           NULL,
        NOMORAJUDOKASAL         varchar(100)   NULL,
        SERIBARANGDOKASAL       int            NULL,             -- dikirim apa adanya, kosong = 1
        CIF                     decimal(19,4)  NOT NULL DEFAULT 0,   -- = CIFRUPIAH / NDPBM header (dihitung form)
        CIFRUPIAH               decimal(19,4)  NOT NULL DEFAULT 0,
        HARGAPENYERAHAN         decimal(19,4)  NOT NULL DEFAULT 0,
        HARGAPEROLEHAN          decimal(19,4)  NOT NULL DEFAULT 0,
        NILAIJASA               decimal(19,4)  NOT NULL DEFAULT 0
    );

    CREATE INDEX IX_DOCUMENTBC41BAHANBAKU_IDBC41 ON dbo.DOCUMENTBC41BAHANBAKU (IDBC41);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC41KEMASAN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC41KEMASAN (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC41              int            NOT NULL,
        SERIKEMASAN         int            NOT NULL,             -- dikirim sebagai angka
        JUMLAHKEMASAN       int            NOT NULL DEFAULT 0,
        KODEJENISKEMASAN    varchar(20)    NULL,
        MERKKEMASAN         varchar(100)   NOT NULL DEFAULT ''
    );

    CREATE INDEX IX_DOCUMENTBC41KEMASAN_IDBC41 ON dbo.DOCUMENTBC41KEMASAN (IDBC41);
END
GO

-- Peti kemas: DIKIRIM ke CEISA (array kontainer; jumlahKontainer = banyaknya baris).
IF OBJECT_ID('dbo.DOCUMENTBC41KONTAINER', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC41KONTAINER (
        ID                      int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC41                  int            NOT NULL,
        SERIKONTAINER           int            NOT NULL,
        NOMORKONTAINER          varchar(20)    NULL,
        KODEUKURANKONTAINER     varchar(20)    NOT NULL DEFAULT '',
        KODEJENISKONTAINER      varchar(20)    NOT NULL DEFAULT '', -- Jenis Muatan: 4 Empty / 7 LCL / 8 FCL
        KODETIPEKONTAINER       varchar(20)    NOT NULL DEFAULT ''
    );

    CREATE INDEX IX_DOCUMENTBC41KONTAINER_IDBC41 ON dbo.DOCUMENTBC41KONTAINER (IDBC41);
END
GO

IF OBJECT_ID('dbo.DOCUMENTBC41DOKUMEN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC41DOKUMEN (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC41              int            NOT NULL,
        SERIDOKUMEN         int            NOT NULL,
        KODEDOKUMEN         varchar(20)    NOT NULL DEFAULT '',   -- JENISDOKUMEN.KODEJENISDOKUMEN
        NOMORDOKUMEN        varchar(50)    NULL,
        TANGGALDOKUMEN      date           NULL
    );

    CREATE INDEX IX_DOCUMENTBC41DOKUMEN_IDBC41 ON dbo.DOCUMENTBC41DOKUMEN (IDBC41);
END
GO

-- Cek hasil (harus 6 baris)
SELECT name FROM sys.tables
WHERE name IN ('DOCUMENTBC41', 'DOCUMENTBC41BARANG', 'DOCUMENTBC41BAHANBAKU', 'DOCUMENTBC41KEMASAN',
               'DOCUMENTBC41KONTAINER', 'DOCUMENTBC41DOKUMEN')
ORDER BY name;
