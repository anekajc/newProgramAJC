-- 18 - Kolom baru BC 4.1 supaya payload sesuai skema RESMI CEISA (docs/bc_schema/bc41.json) dan username CEISA
-- pada token login. Dibuat 29 Oktober 2026.
-- DITINJAU dan DIJALANKAN MANUAL oleh user di DBSMLNEW (bukan php artisan migrate).
--
--   CEISATOKEN.USERNAMECEISA            username CEISA yang dipakai saat Login BC (password tidak disimpan).
--                                       Dikirim sebagai "userPortal" (BC 4.1, wajib) dan "idPengguna" (BC 2.5, wajib).
--                                       Diisi ulang setiap Login BC; user yang belum login ulang setelah script ini
--                                       akan melihat catatan "login BC dulu" saat kirim / preview.
--   DOCUMENTBC41.VOLUME                 Total volume (root "volume", wajib) = jumlah volume semua barang (dihitung server)
--   DOCUMENTBC41.NAMAPENGANGKUT         Nama sarana pengangkut (pengangkut[].namaPengangkut, wajib)
--   DOCUMENTBC41.NOMORPENGANGKUT        Nomor sarana pengangkut (pengangkut[].nomorPengangkut, wajib)
--   DOCUMENTBC41.NOMORIJINENTITAS2      salinan nomor izin master ENTITAS untuk Pemilik Barang (seri 7, wajib)
--   DOCUMENTBC41.TANGGALIJINENTITAS2    salinan tanggal izin master ENTITAS untuk Pemilik Barang (seri 7, wajib)
--   DOCUMENTBC41BARANG.VOLUME           Volume barang (barang[].volume, wajib), diketik user
--
-- Dokumen BC 4.1 yang sudah ada: VOLUME = 0, nama / nomor pengangkut = '', izin Pemilik NULL (user melengkapinya lewat
-- Edit sebelum dikirim; pemeriksaan skema saat kirim akan mencatat yang kurang).
--
-- Dihapus dari aplikasi (skema resmi tidak punya): array pungutan, Bukti Bayar, Cara Angkut, Negara Asal barang,
-- tarif BM / PPN / PPh barang. Kolomnya (NOMORBUKTIBAYAR, TANGGALBUKTIBAYAR, KODECARAANGKUT, KODENEGARAASAL,
-- PERSENBM / PERSENPPN / PERSENPPH, KETERANGANDOKUMEN, DISKON, FREIGHT, HARGASATUAN, KODEPERHITUNGAN,
-- KODENEGARAENTITAS1..3) tidak lagi dipakai; dibiarkan, tidak dihapus di sini. Hapus sendiri bila perlu.
-- Aman dijalankan ulang (kolom yang sudah ada dilewati).

USE DBSMLNEW
GO

IF COL_LENGTH('dbo.CEISATOKEN', 'USERNAMECEISA') IS NULL
    ALTER TABLE dbo.CEISATOKEN ADD USERNAMECEISA varchar(100) NULL;
GO

IF COL_LENGTH('dbo.DOCUMENTBC41', 'VOLUME') IS NULL
    ALTER TABLE dbo.DOCUMENTBC41 ADD VOLUME decimal(19,4) NOT NULL CONSTRAINT DF_DOCUMENTBC41_VOLUME DEFAULT 0;
GO

IF COL_LENGTH('dbo.DOCUMENTBC41', 'NAMAPENGANGKUT') IS NULL
    ALTER TABLE dbo.DOCUMENTBC41 ADD NAMAPENGANGKUT varchar(100) NOT NULL CONSTRAINT DF_DOCUMENTBC41_NAMAPENGANGKUT DEFAULT '';
GO

IF COL_LENGTH('dbo.DOCUMENTBC41', 'NOMORPENGANGKUT') IS NULL
    ALTER TABLE dbo.DOCUMENTBC41 ADD NOMORPENGANGKUT varchar(50) NOT NULL CONSTRAINT DF_DOCUMENTBC41_NOMORPENGANGKUT DEFAULT '';
GO

IF COL_LENGTH('dbo.DOCUMENTBC41', 'NOMORIJINENTITAS2') IS NULL
    ALTER TABLE dbo.DOCUMENTBC41 ADD NOMORIJINENTITAS2 varchar(50) NULL;
GO

IF COL_LENGTH('dbo.DOCUMENTBC41', 'TANGGALIJINENTITAS2') IS NULL
    ALTER TABLE dbo.DOCUMENTBC41 ADD TANGGALIJINENTITAS2 date NULL;
GO

IF COL_LENGTH('dbo.DOCUMENTBC41BARANG', 'VOLUME') IS NULL
    ALTER TABLE dbo.DOCUMENTBC41BARANG ADD VOLUME decimal(19,4) NOT NULL CONSTRAINT DF_DOCUMENTBC41BARANG_VOLUME DEFAULT 0;
GO

-- Cek hasil (harus 1 + 5 + 1 baris)
SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
WHERE (TABLE_NAME = 'CEISATOKEN' AND COLUMN_NAME = 'USERNAMECEISA')
   OR (TABLE_NAME = 'DOCUMENTBC41' AND COLUMN_NAME IN ('VOLUME', 'NAMAPENGANGKUT', 'NOMORPENGANGKUT', 'NOMORIJINENTITAS2', 'TANGGALIJINENTITAS2'))
   OR (TABLE_NAME = 'DOCUMENTBC41BARANG' AND COLUMN_NAME = 'VOLUME')
ORDER BY TABLE_NAME, COLUMN_NAME;
