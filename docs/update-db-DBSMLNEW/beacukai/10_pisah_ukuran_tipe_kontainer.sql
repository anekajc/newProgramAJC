-- 10 - Pisah master Kontainer menjadi Ukuran (UKURANKONTAINER) dan Tipe (TIPEKONTAINER). Dibuat 26 Oktober 2026.
-- DITINJAU dan DIJALANKAN MANUAL oleh user di DBSMLNEW (bukan php artisan migrate). Backup dulu.
--
-- Sebelumnya tabel KONTAINER menyimpan pasangan (ukuran, tipe) dalam satu baris. Sekarang:
--   UKURANKONTAINER (id, KODEUKURANKONTAINER, NAMAUKURANKONTAINER)  - hasil rename dari KONTAINER
--   TIPEKONTAINER   (id, KODETIPEKONTAINER,   NAMATIPEKONTAINER)    - tabel baru
-- Dokumen BC 2.6.1 (DOCUMENTBC261KONTAINER) tidak berubah: tetap menyimpan KODE ukuran dan KODE tipe
-- sebagai teks, dikirim ke CEISA sebagai kodeUkuranKontainer / kodeTipeKontainer.
--
-- Data lama KONTAINER: ukuran yang sama digabung jadi satu baris, namanya diisi sama dengan kodenya
-- (ganti lewat halaman Master Kontainer). KODETIPEKONTAINER lama DIBUANG - isi ulang tipe lewat
-- halaman Master Kontainer atau dari seed di bawah.
-- Nama tabel dipakai lewat config/beacukai.php (tables.ukuran_kontainer, tables.tipe_kontainer).
-- Aman dijalankan ulang.

USE DBSMLNEW
GO

-- 1) TIPEKONTAINER (baru) + seed 9 tipe CEISA bila masih kosong
IF OBJECT_ID('dbo.TIPEKONTAINER', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.TIPEKONTAINER (
        id                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        KODETIPEKONTAINER   varchar(20)   NOT NULL,
        NAMATIPEKONTAINER   varchar(100)  NOT NULL DEFAULT ''
    );
    CREATE UNIQUE INDEX UX_TIPEKONTAINER_KODE ON dbo.TIPEKONTAINER (KODETIPEKONTAINER);
END
GO

IF NOT EXISTS (SELECT 1 FROM dbo.TIPEKONTAINER)
BEGIN
    INSERT INTO dbo.TIPEKONTAINER (KODETIPEKONTAINER, NAMATIPEKONTAINER) VALUES
        ('1', 'General/Dry Cargo'), ('2', 'Tunnel Type'), ('3', 'Open Top Steel'), ('4', 'Flat Rack'),
        ('5', 'Reefer'), ('6', 'Barge Container'), ('7', 'Bulk Container'), ('8', 'Isotank'), ('99', 'Lain-lain');
END
GO

-- 2) KONTAINER -> UKURANKONTAINER (rename)
IF OBJECT_ID('dbo.UKURANKONTAINER', 'U') IS NULL AND OBJECT_ID('dbo.KONTAINER', 'U') IS NOT NULL
BEGIN
    EXEC sp_rename 'dbo.KONTAINER', 'UKURANKONTAINER';
END
GO

-- 3) kolom nama ukuran
IF OBJECT_ID('dbo.UKURANKONTAINER', 'U') IS NOT NULL
   AND COL_LENGTH('dbo.UKURANKONTAINER', 'NAMAUKURANKONTAINER') IS NULL
BEGIN
    ALTER TABLE dbo.UKURANKONTAINER ADD NAMAUKURANKONTAINER varchar(100) NOT NULL DEFAULT '';
END
GO

-- 4) gabung ukuran yang dobel (simpan id terkecil), isi nama = kode bila masih kosong
IF OBJECT_ID('dbo.UKURANKONTAINER', 'U') IS NOT NULL
BEGIN
    DELETE u FROM dbo.UKURANKONTAINER u
    WHERE u.id > (SELECT MIN(x.id) FROM dbo.UKURANKONTAINER x WHERE x.KODEUKURANKONTAINER = u.KODEUKURANKONTAINER);

    UPDATE dbo.UKURANKONTAINER SET NAMAUKURANKONTAINER = KODEUKURANKONTAINER WHERE NAMAUKURANKONTAINER = '';
END
GO

-- 5) buang kolom tipe lama, kode ukuran unik
IF OBJECT_ID('dbo.UKURANKONTAINER', 'U') IS NOT NULL
BEGIN
    IF COL_LENGTH('dbo.UKURANKONTAINER', 'KODETIPEKONTAINER') IS NOT NULL
        ALTER TABLE dbo.UKURANKONTAINER DROP COLUMN KODETIPEKONTAINER;

    IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name = 'UX_UKURANKONTAINER_KODE' AND object_id = OBJECT_ID('dbo.UKURANKONTAINER'))
        CREATE UNIQUE INDEX UX_UKURANKONTAINER_KODE ON dbo.UKURANKONTAINER (KODEUKURANKONTAINER);
END
GO

-- Cek hasil: UKURANKONTAINER (id, KODEUKURANKONTAINER, NAMAUKURANKONTAINER) dan TIPEKONTAINER (9 baris seed)
SELECT * FROM dbo.UKURANKONTAINER;
SELECT * FROM dbo.TIPEKONTAINER;
