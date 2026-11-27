-- =============================================================
-- Amortisasi: metode penyusutan Garis Lurus / Saldo Menurun
-- Untuk halaman Amortisasi (AmortisasiController).
--
-- - Metode: 0 atau NULL = Garis Lurus, 1 = Saldo Menurun.
--   Baris lama (sebelum kolom ini ada) tetap NULL dan dianggap Garis Lurus.
-- - Persen: tarif penyusutan per tahun (%), hanya dipakai untuk Saldo Menurun.
--   NULL untuk Garis Lurus.
-- - Aman dijalankan ulang (idempotent).
-- - Wajib dijalankan sebelum memakai halaman Amortisasi yang baru.
-- =============================================================

IF COL_LENGTH('DBAMORTISASI','Metode') IS NULL
    ALTER TABLE DBAMORTISASI ADD Metode int NULL;          -- 0 / NULL = Garis Lurus, 1 = Saldo Menurun

IF COL_LENGTH('DBAMORTISASI','Persen') IS NULL
    ALTER TABLE DBAMORTISASI ADD Persen numeric(9,4) NULL; -- tarif penyusutan per tahun (%), hanya untuk Saldo Menurun
