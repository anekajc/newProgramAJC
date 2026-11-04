-- Permintaan BATAL otorisasi (push ke user yang punya hak Batal).
-- Dijalankan MANUAL di database SML (DBSMLNEW) - bukan lewat `php artisan migrate`.
-- Jalankan di SEMUA server SEBELUM kode barunya di-deploy; baris lama otomatis jadi TIPE 'O'.
-- Idempotent: aman dijalankan berulang.

IF COL_LENGTH('dbo.DBREQOTORISASI', 'TIPE') IS NULL
BEGIN
    -- O = permintaan otorisasi (default, semua baris lama), B = permintaan batal otorisasi
    ALTER TABLE dbo.DBREQOTORISASI
        ADD TIPE char(1) NOT NULL CONSTRAINT DF_DBREQOTORISASI_TIPE DEFAULT 'O';
END
GO

IF COL_LENGTH('dbo.DBREQOTORISASI', 'ALASAN') IS NULL
BEGIN
    -- alasan yang ditulis peminta (dipakai permintaan batal; CATATAN tetap alasan tolak dari approver)
    ALTER TABLE dbo.DBREQOTORISASI ADD ALASAN varchar(255) NULL;
END
GO
