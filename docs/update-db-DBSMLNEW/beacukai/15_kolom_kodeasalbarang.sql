-- 15 - Kolom KODEASALBARANG pada barang BC 2.6.1 dan BC 2.6.2. Dibuat 29 Oktober 2026.
-- DITINJAU dan DIJALANKAN MANUAL oleh user di DBSMLNEW (bukan php artisan migrate).
-- Dikirim ke CEISA sebagai kodeAsalBarang per barang. Isi: '1' = Sepenuhnya diperoleh dan/atau diproduksi di luar
-- daerah pabean, '2' = ... di tempat lain dalam daerah pabean, '3' = Kawasan Pabean, '4' = Lainnya.
-- NULL = belum dipilih; payload tetap mengirim "0" seperti sebelumnya, jadi dokumen lama tidak berubah.
-- Nama tabel dipakai lewat config/beacukai.php (tables.bc261_barang, tables.bc262_barang).
-- Aman dijalankan ulang (kolom yang sudah ada dilewati).

USE DBSMLNEW
GO

IF COL_LENGTH('dbo.DOCUMENTBC261BARANG', 'KODEASALBARANG') IS NULL
    ALTER TABLE dbo.DOCUMENTBC261BARANG ADD KODEASALBARANG varchar(1) NULL;
GO

IF COL_LENGTH('dbo.DOCUMENTBC262BARANG', 'KODEASALBARANG') IS NULL
    ALTER TABLE dbo.DOCUMENTBC262BARANG ADD KODEASALBARANG varchar(1) NULL;
GO
