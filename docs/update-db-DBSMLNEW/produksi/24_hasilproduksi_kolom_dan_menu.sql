-- =====================================================================================
-- 24 - Hasil Produksi (menu 08007, href produksihasilproduksi)
-- Database : DBSMLNEW
-- Sumber   : DBMas (lokal) - struktur dbHasilPrd / DBPRoses disamakan.
--
-- Query tampilan depan Hasil Produksi (ProduksiHasilProduksiController@loadAll) memakai
-- kolom dbHasilPrd.Shift dan dbHasilPrd.Php02 yang belum ada di DBSMLNEW. Sekalian
-- ditambahkan semua kolom DBMas yang belum ada, supaya menu input Hasil Produksi nanti
-- tidak perlu skrip baru:
--   dbHasilPrd : Shift, pPotong, KodeMSN, QntReject, pProses, Php02, JamActual, pBJ, nama
--   DBPRoses   : IsHeader, Harga, Biaya
-- DBMENUWEB 08007 'Hasil Produksi' sudah ada tetapi href masih NULL -> diisi.
--
-- Hanya MENAMBAH kolom / mengisi href. Aman dijalankan ulang.
-- =====================================================================================

-- ---------- dbHasilPrd ----------
IF COL_LENGTH('dbo.DBHASILPRD', 'Shift') IS NULL
  ALTER TABLE dbo.DBHASILPRD ADD Shift tinyint NULL;
GO
IF COL_LENGTH('dbo.DBHASILPRD', 'pPotong') IS NULL
  ALTER TABLE dbo.DBHASILPRD ADD pPotong tinyint NULL;
GO
IF COL_LENGTH('dbo.DBHASILPRD', 'KodeMSN') IS NULL
  ALTER TABLE dbo.DBHASILPRD ADD KodeMSN varchar(20) NULL;
GO
IF COL_LENGTH('dbo.DBHASILPRD', 'QntReject') IS NULL
  ALTER TABLE dbo.DBHASILPRD ADD QntReject numeric(18,2) NULL;
GO
IF COL_LENGTH('dbo.DBHASILPRD', 'pProses') IS NULL
  ALTER TABLE dbo.DBHASILPRD ADD pProses tinyint NOT NULL
    CONSTRAINT DF_DBHASILPRD_pProses DEFAULT ((0));
GO
IF COL_LENGTH('dbo.DBHASILPRD', 'Php02') IS NULL
  ALTER TABLE dbo.DBHASILPRD ADD Php02 tinyint NULL;
GO
IF COL_LENGTH('dbo.DBHASILPRD', 'JamActual') IS NULL
  ALTER TABLE dbo.DBHASILPRD ADD JamActual numeric(18,2) NULL;
GO
IF COL_LENGTH('dbo.DBHASILPRD', 'pBJ') IS NULL
  ALTER TABLE dbo.DBHASILPRD ADD pBJ tinyint NULL;
GO
IF COL_LENGTH('dbo.DBHASILPRD', 'nama') IS NULL
  ALTER TABLE dbo.DBHASILPRD ADD nama varchar(150) NULL;
GO

-- ---------- DBPRoses ----------
IF COL_LENGTH('dbo.DBPRoses', 'IsHeader') IS NULL
  ALTER TABLE dbo.DBPRoses ADD IsHeader bit NULL;
GO
IF COL_LENGTH('dbo.DBPRoses', 'Harga') IS NULL
  ALTER TABLE dbo.DBPRoses ADD Harga numeric(18,2) NULL;
GO
IF COL_LENGTH('dbo.DBPRoses', 'Biaya') IS NULL
  ALTER TABLE dbo.DBPRoses ADD Biaya numeric(18,2) NULL;
GO

-- ---------- Menu web ----------
UPDATE DBMENUWEB
   SET href = 'produksihasilproduksi'
 WHERE KODEMENU = '08007'
   AND (href IS NULL OR href <> 'produksihasilproduksi');
GO
