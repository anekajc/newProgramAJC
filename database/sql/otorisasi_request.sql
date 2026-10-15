-- Permintaan otorisasi + langganan Web Push (PWA).
-- Dijalankan MANUAL di database SML (DBSMLNEW) - bukan lewat `php artisan migrate`,
-- karena tabel bisnis hidup di SQL Server, bukan di sqlite bawaan Laravel.
-- Dipakai oleh app/Services/OtorisasiService.php dan app/Jobs/SendWebPush.php.

IF OBJECT_ID('dbo.DBREQOTORISASI', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DBREQOTORISASI (
        ID          int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        KODEMENU    varchar(20)  NOT NULL,          -- DBMENUWEB.KODEMENU, mis. '02011' (Kas)
        MENUPATH    varchar(100) NOT NULL,          -- DBMENUWEB.href, mis. 'kas' (kunci config/otorisasi.php)
        NOBUKTI     varchar(30)  NOT NULL,
        LEVELOTO    int          NOT NULL DEFAULT 1, -- IsOtorisasi1..5 (saat ini hanya 1)
        REQUSER     varchar(50)  NOT NULL,
        TGLREQ      datetime     NOT NULL DEFAULT GETDATE(),
        STATUS      char(1)      NOT NULL DEFAULT 'P', -- P=Pending, A=Approved, R=Rejected, C=Cancelled
        ACTUSER     varchar(50)  NULL,
        TGLACT      datetime     NULL,
        CATATAN     varchar(255) NULL,              -- alasan tolak
        REQREAD     bit          NOT NULL DEFAULT 0  -- peminta sudah melihat hasilnya
    );

    CREATE INDEX IX_DBREQOTORISASI_STATUS ON dbo.DBREQOTORISASI (STATUS, MENUPATH);
    CREATE INDEX IX_DBREQOTORISASI_NOBUKTI ON dbo.DBREQOTORISASI (NOBUKTI);
    CREATE INDEX IX_DBREQOTORISASI_REQUSER ON dbo.DBREQOTORISASI (REQUSER);
END
GO

IF OBJECT_ID('dbo.DBPUSHSUBSCRIPTION', 'U') IS NULL
BEGIN
    CREATE TABLE dbo.DBPUSHSUBSCRIPTION (
        ID          int IDENTITY(1,1) NOT NULL PRIMARY KEY,
        USERID      varchar(50)  NOT NULL,
        ENDPOINT    varchar(500) NOT NULL,
        P256DH      varchar(255) NOT NULL,
        AUTHKEY     varchar(255) NOT NULL,
        USERAGENT   varchar(255) NULL,
        TGLBUAT     datetime     NOT NULL DEFAULT GETDATE(),
        CONSTRAINT UQ_DBPUSHSUBSCRIPTION_ENDPOINT UNIQUE (ENDPOINT)
    );

    CREATE INDEX IX_DBPUSHSUBSCRIPTION_USERID ON dbo.DBPUSHSUBSCRIPTION (USERID);
END
GO
