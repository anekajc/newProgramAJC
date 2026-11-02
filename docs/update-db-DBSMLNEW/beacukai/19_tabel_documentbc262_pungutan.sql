-- 19 - Tabel pungutan BC 2.6.2 (koleksi anak). Dibuat 30 Oktober 2026.
-- DITINJAU dan DIJALANKAN MANUAL oleh user di DBSMLNEW (bukan php artisan migrate).
-- Tab Pungutan BC 2.6.2 berubah dari 12 isian tetap (kolom NILAIBM, NILAIPPN, ... di DOCUMENTBC262) menjadi
-- daftar baris: tiap baris = Jenis Pungutan + Fasilitas Tarif + Nilai Pungutan, dikirim ke CEISA sebagai satu objek
-- di array pungutan (idPungutan = nomor baris). Tidak ada baris = pungutan [] (tidak wajib).
-- Kolom lama NILAIBM ... NILAITOTALPUNGUTAN di DOCUMENTBC262 TIDAK dipakai lagi dan nilainya TIDAK dipindahkan
-- (keputusan user); kolomnya dibiarkan (NOT NULL DEFAULT 0), boleh di-drop belakangan.
-- Kode: KODEJENISPUNGUTAN = BK, BM, BMAD, BMI, BMKITE, BMP, BMTP, CEA, CMEA, CTEM, DENDA, DS, PNBP, PPH, PPHEKSPOR,
-- PPN, PPNBM, PPNLOKAL; KODEFASILITASTARIF = 1..9 (1 DIBAYAR ... 9 DITUNDA). Keduanya daftar tetap di aplikasi.
-- Nama tabel dipakai lewat config/beacukai.php (tables.bc262_pungutan). Dihapus eksplisit oleh aplikasi (tanpa cascade).
-- Aman dijalankan ulang (tabel yang sudah ada dilewati).

USE DBSMLNEW
GO

IF OBJECT_ID('dbo.DOCUMENTBC262PUNGUTAN', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DOCUMENTBC262PUNGUTAN (
        ID                  int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        IDBC262             int            NOT NULL,
        SERIPUNGUTAN        int            NOT NULL,             -- nomor baris 1..n (idPungutan)
        KODEJENISPUNGUTAN   varchar(20)    NOT NULL DEFAULT '',
        KODEFASILITASTARIF  varchar(5)     NOT NULL DEFAULT '',
        NILAIPUNGUTAN       decimal(19,4)  NOT NULL DEFAULT 0
    );

    CREATE INDEX IX_DOCUMENTBC262PUNGUTAN_IDBC262 ON dbo.DOCUMENTBC262PUNGUTAN (IDBC262);
END
GO

-- Cek hasil (harus 1 baris)
SELECT name FROM sys.tables WHERE name = 'DOCUMENTBC262PUNGUTAN';
