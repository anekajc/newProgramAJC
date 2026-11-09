-- =====================================================================================
-- 25 - Pindahkan objek Hasil Produksi dari DBMas (lokal) ke DBSMLNEW
-- Database : DBSMLNEW
-- Dijalankan: 9 November 2026 (atas perintah user).
--
-- Kondisi DBSMLNEW sebelum skrip ini: DBHASILPRD / DBHASILPRDDET / DBHASILPRDMSN KOSONG
-- (0 baris), DBHASILPRDDET cuma 4 kolom, NOBUKTI varchar(20) (No Bukti HPB/00001/1026/MM/WIP
-- = 21+ karakter tidak muat), Sp_HasilPrd versi lain yang merujuk kolom tidak ada (backup:
-- 25_backup_sp_hasilprd_lama_DBSMLNEW.sql), tidak ada trigger stok.
--
-- Isi:
--   1. DBHASILPRD.NOBUKTI (dan DBHASILPRDMSN.NOBUKTI, karena FK) -> varchar(30)
--   2. DBHASILPRDDET dibuat ulang persis DBMas (tabel kosong) + PK + FK
--      (FK ke DBBARANG / DBGUDANG dibuat WITH NOCHECK seperti di DBMas - is_not_trusted=1)
--   3. Tabel dbHasilPrdProses (baru)
--   4. SP dari DBMas: Sp_HasilPrd, Sp_dbHasilPrdProses, SP_CekSisaKPHASILPRD
--      SATU-SATUNYA beda dari DBMas: "--Begin tran" di Sp_HasilPrd diaktifkan (lihat komentar di SP).
--      (sp_InputHasilPrd TIDAK disalin: memakai DBBOMProses.KodeGrpPrs / DBBOMDET.KodeGrpPrs /
--      DBBonPenyerahanBhn yang tidak ada di DBSMLNEW - struktur BOM DBSMLNEW berbeda dari DBMas.
--      SP itu hanya untuk alur "Proses Potong" dari JO, tidak dipakai form Hasil Produksi.)
--      (Sp_HasilPrdDetail / Sp_HasilPrdMesin sudah sama dengan DBMas - tidak disentuh)
--   5. 6 trigger stok DBHASILPRDDET (dbstockbrg.QntHPrd/Qnt2HPrd dan dbSTOCKWIP.QntMasuk/Qnt2Masuk)
--
-- Prasyarat: skrip 24 sudah dijalankan.
-- Catatan: Sp_dbHasilPrdProses memakai dbSubProses yang BELUM ADA di DBSMLNEW. SP tetap
-- terbuat (deferred name resolution), tapi baru bisa dijalankan setelah tabelnya ada.
-- =====================================================================================

-- ---------- 1. NOBUKTI header & mesin -> varchar(30) ----------
IF OBJECT_ID('dbo.FK_DBHASILPRDDET_DBHASILPRD', 'F') IS NOT NULL
  ALTER TABLE dbo.DBHASILPRDDET DROP CONSTRAINT FK_DBHASILPRDDET_DBHASILPRD;
IF OBJECT_ID('dbo.FK_DBHASILPRDMSN_DBHASILPRD', 'F') IS NOT NULL
  ALTER TABLE dbo.DBHASILPRDMSN DROP CONSTRAINT FK_DBHASILPRDMSN_DBHASILPRD;
GO
IF COL_LENGTH('dbo.DBHASILPRD', 'NOBUKTI') < 30
BEGIN
  ALTER TABLE dbo.DBHASILPRD DROP CONSTRAINT PK_DBHASILPRD;
  ALTER TABLE dbo.DBHASILPRD ALTER COLUMN NOBUKTI varchar(30) NOT NULL;
  ALTER TABLE dbo.DBHASILPRD ADD CONSTRAINT PK_DBHASILPRD PRIMARY KEY CLUSTERED (NOBUKTI);
END
GO
IF OBJECT_ID('dbo.DBHASILPRDMSN', 'U') IS NOT NULL AND COL_LENGTH('dbo.DBHASILPRDMSN', 'NOBUKTI') < 30
BEGIN
  ALTER TABLE dbo.DBHASILPRDMSN DROP CONSTRAINT PK_DBHASILPRDMSN;
  ALTER TABLE dbo.DBHASILPRDMSN ALTER COLUMN NOBUKTI varchar(30) NOT NULL;
  ALTER TABLE dbo.DBHASILPRDMSN ADD CONSTRAINT PK_DBHASILPRDMSN PRIMARY KEY CLUSTERED (NOBUKTI, URUT);
END
GO
IF OBJECT_ID('dbo.DBHASILPRDMSN', 'U') IS NOT NULL AND OBJECT_ID('dbo.FK_DBHASILPRDMSN_DBHASILPRD', 'F') IS NULL
  ALTER TABLE dbo.DBHASILPRDMSN ADD CONSTRAINT FK_DBHASILPRDMSN_DBHASILPRD
    FOREIGN KEY (NOBUKTI) REFERENCES dbo.DBHASILPRD (NOBUKTI) ON DELETE CASCADE ON UPDATE CASCADE;
GO

-- ---------- 2. DBHASILPRDDET persis DBMas ----------
-- Hanya dibuat ulang kalau strukturnya masih versi lama (belum ada KODEBRG) DAN kosong.
IF COL_LENGTH('dbo.DBHASILPRDDET', 'KODEBRG') IS NULL
BEGIN
  IF EXISTS (SELECT 1 FROM dbo.DBHASILPRDDET)
    RAISERROR('DBHASILPRDDET berisi data - batalkan, periksa manual', 16, 1);
  ELSE
    DROP TABLE dbo.DBHASILPRDDET;
END
GO
IF OBJECT_ID('dbo.DBHASILPRDDET', 'U') IS NULL
BEGIN
  CREATE TABLE dbo.DBHASILPRDDET (
    NOBUKTI       varchar(30)   NOT NULL CONSTRAINT DF__DBHASILPRDDET_1 DEFAULT (''),
    URUT          int           NOT NULL,
    KODEBRG       varchar(25)   NULL     CONSTRAINT DF__DBHASILPRDDET1 DEFAULT (''),
    KodeGdg       varchar(15)   NULL,
    QNT           numeric(18,2) NULL,
    NOSAT         tinyint       NULL,
    SATUAN        varchar(5)    NULL,
    ISI           numeric(18,2) NULL,
    NoSPK         varchar(50)   NULL,
    HPP           float         NULL,
    UrutSPK       int           NULL,
    KodeBrgProses varchar(25)   NULL,
    KodePrs       varchar(20)   NULL,
    KodeGrpPrs    varchar(25)   NULL,
    waktu         numeric(18,2) NULL,
    noJO          varchar(30)   NULL,
    QntReject     numeric(18,2) NULL,
    Avalan        numeric(18,2) NULL,
    Biaya         numeric(18,9) NULL,
    IsAv          bit           NULL,
    BiayaStd      numeric(18,2) NULL,
    CONSTRAINT PK_DBHASILPRDDET PRIMARY KEY CLUSTERED (NOBUKTI, URUT)
  );
END
GO
IF OBJECT_ID('dbo.FK_DBHASILPRDDET_DBHASILPRD', 'F') IS NULL
  ALTER TABLE dbo.DBHASILPRDDET ADD CONSTRAINT FK_DBHASILPRDDET_DBHASILPRD
    FOREIGN KEY (NOBUKTI) REFERENCES dbo.DBHASILPRD (NOBUKTI) ON DELETE CASCADE ON UPDATE CASCADE;
GO
IF OBJECT_ID('dbo.FK_DBHASILPRDDET_DBBARANG', 'F') IS NULL
  ALTER TABLE dbo.DBHASILPRDDET WITH NOCHECK ADD CONSTRAINT FK_DBHASILPRDDET_DBBARANG
    FOREIGN KEY (KODEBRG) REFERENCES dbo.DBBARANG (KODEBRG) ON UPDATE CASCADE;
GO
IF OBJECT_ID('dbo.FK_DBHASILPRDDET_DBGUDANG', 'F') IS NULL
  ALTER TABLE dbo.DBHASILPRDDET WITH NOCHECK ADD CONSTRAINT FK_DBHASILPRDDET_DBGUDANG
    FOREIGN KEY (KodeGdg) REFERENCES dbo.DBGUDANG (KODEGDG) ON UPDATE CASCADE;
GO

-- ---------- 3. dbHasilPrdProses ----------
IF OBJECT_ID('dbo.dbHasilPrdProses', 'U') IS NULL
  CREATE TABLE dbo.dbHasilPrdProses (
    NoBukti       varchar(25) NULL,
    NoSPK         varchar(25) NULL,
    KodePrs       varchar(15) NULL,
    KodeBrgProses varchar(25) NULL,
    KodeGrpPrs    varchar(25) NULL
  );
GO

-- ---------- 4. Stored procedure (salinan apa adanya dari DBMas) ----------
IF OBJECT_ID('dbo.Sp_HasilPrd', 'P') IS NOT NULL DROP PROCEDURE dbo.Sp_HasilPrd;
GO
CREATE Procedure [dbo].[Sp_HasilPrd]
@Choice varchar(1),
@NoBukti varchar(30)='',
@NoUrut varchar(10)='',
@Tanggal datetime='',
@KodeGdg varchar(15)='',
@Keterangan varchar(200)='',
@KodeBrg varchar(25)='',
@Qnt numeric(18,2)=0,

@NoSat tinyint=0,
@Satuan varchar(5)='',

@Isi numeric(18,2)=0,

@NoSPK Varchar(50)='',
@urutSPK int=0,
@Urut Int=0,
@KodeBrgProses varchar(25)='',
@KodeProses varchar(15)='',
@KodeGrpPrs Varchar(25)='',
@Waktu Numeric(18,2)=0,
@NoJO Varchar(30)='',
@Shift tinyint=0,
@pPotong Tinyint=0,

@KodeMSn varchar(20),
@Reject Numeric(18,2),
@pProses Tinyint=0,
@php02 tinyint=0,
@avalan numeric(18,2)=0,
@IsAV Bit=0,
@JamActual Numeric(18,2)=0,
@PBJ integer=0,
@Nama Varchar(150)=''

As
Begin tran -- [DBSMLNEW] di DBMas baris ini dikomentari; diaktifkan supaya Commit tran di bawah seimbang (tanpa ini error 266/3902 saat dipanggil dari web)


--SET @KodeGdg=CASE WHEN @NoBukti LIKE '%HPDP%' THEN 'G02' WHEN @NoBukti LIKE '%HPDI%' THEN 'G03' ELSE @KodeGdg END
select @Isi= case when @NoSat=1 then ISI1 when @NoSat=2 then ISI2 when @NoSat=3 then ISI3 end ,
@Satuan= case when @NoSat=1 then SAT1 when @NoSat=2 then SAT2 when @NoSat=3 then SAT3 end 
from DBBARANG where KODEBRG=@KodeBrg

set @pBJ = case when @Nobukti like '%BJ%' then 1 else 0 End
if @Choice='I'
begin

 select @Urut=isnull(max(urut),0)+1 from dbHasilPrddet Where NoBukti=@NoBukti

 if not exists(select * from dbHasilPrd Where NoBukti=@NoBukti) 
  begin
   insert into dbHasilPrd (NOBUKTI, NOURUT, TANGGAL, KETERANGAN, NoSPK,Shift,pPotong,KodeMsn,pProses,pHP02,JamActual,pBJ,nama)
   values (@NOBUKTI, @NOURUT, @TANGGAL, @KETERANGAN, @NoSPK,@Shift,@pPotong,@KodeMSn,@pProses,@php02,@JamActual,@PBJ,@Nama)
  end
  insert into dbHasilPrdDET (NOBUKTI, URUT,  KODEBRG, Qnt, NOSAT, ISI, SATUAN,KodeGdg,NoSPK,UrutSPK,KodebrgProses,KodePrs,KodeGrpPrs,Waktu,NOJO,QntReject,avalan,Isav)
  values(@NOBUKTI, @URUT, @KODEBRG, @Qnt, @NoSat, @Isi, @Satuan, @KodeGdg,@NoSPK,@urutSPK,@KodeBrgProses,@KodeProses,@KodeGrpPrs,@Waktu,@NoJO,@Reject,@avalan,@IsAV)
end
if @Choice='U'
begin
   update dbHasilPrdDET set KodeBrg=@KODEBRG, Qnt=@QNT, NOSAT=@NoSat, ISI=@ISI, SATUAN=@Satuan,KodeGdg=@KodeGdg,KodeGrpPrs=@KodeGrpPrs,
   Nospk=@NoSPK,KodeBrgProses=@KodeBrgProses,KodePrs=@KodeProses,Waktu=@Waktu,NoJO=@NoJO,QntReject=@Reject,avalan=@avalan
   where NoBukti=@NoBukti and Urut=@Urut
end
if @Choice='D'
begin
  if @KodeGrpPrs='01'
  Begin
  delete dbHasilPrd where NoBukti=@NoBukti
  end
  else
  Begin
  delete dbHasilPrdDET where NoBukti=@NoBukti and Urut=@Urut 
  --Delete dbhasilPrdProses where  NoBukti=@NoBukti and KodeGrpPrs=@KodeGrpPrs 
  if not exists( select NoBukti from dbHasilPrdDET where NoBukti=@NoBukti)
  begin
    
    delete dbHasilPrd where NoBukti=@NoBukti
  end
  end
end
if @@error<>0  goto jikasalah
Commit tran
Return
JikaSalah: Rollback tran
           Return

GO

IF OBJECT_ID('dbo.Sp_dbHasilPrdProses', 'P') IS NOT NULL DROP PROCEDURE dbo.Sp_dbHasilPrdProses;
GO
CREATE Procedure [dbo].[Sp_dbHasilPrdProses]
@Choice char,
@NoBukti Varchar(25),
@NoSPK Varchar(25),
@KodePrs Varchar(15),
@KodeBrgProses Varchar(25),
@KodeGrpPrs Varchar(25)=''

As

Begin tran
  If @Choice='I' 
  begin
   
    Insert into dbHasilPrdProses (NoBukti,NoSPK ,KodePrs ,KodeBrgProses,KodeGrpPrs )
    Values(@NoBukti,@NoSPK,@KodePrs,@KodeBrgProses,@KodeGrpPrs) 
  end
  else if @Choice='U' 
  begin
    Update dbHasilPrdProses Set KodePrs=@KodePrs ,KodeBrgProses=@KodeBrgProses,KodeGrpPrs=@KodeGrpPrs
    where NoBukti=@NoBukti and NoSPK=@NoSPK 
  end
  else if @Choice='D'
  begin
    Delete From dbHasilPrdProses where NoBukti=@NoBukti and NoSPK=@NoSPK and KodePrs in(select SubKodePrs from dbSubProses Where KodePrs=@KodePrs) 
    and KodeGrpPrs=@KodeGrpPrs
  end
  if @@Error<>0 Goto JikaSalah
Commit Tran
Return
JikaSalah: Rollback Tran
           raiserror('Proses Input Data Gagal',16,1)
           Return

GO

IF OBJECT_ID('dbo.SP_CekSisaKPHASILPRD', 'P') IS NOT NULL DROP PROCEDURE dbo.SP_CekSisaKPHASILPRD;
GO
Create Procedure [dbo].[SP_CekSisaKPHASILPRD]
@NoBukti Varchar(30),
@KodeBrg Varchar(25)
AS
select ISNULL(a.QNT*isi,0)QntSPK,ISNULL(B.QNT,0)QNTHP,(a.QNT*a.isi)-ISNULL(B.QNT,0) Sisa from DBSPK a
Left Outer Join  (select NoSPK,Kodebrg ,SUM(QNT*ISI)Qnt from DBHASILPRDDET Group by NoSPK,Kodebrg) b on a.NOBUKTI=b.NoSPK and b.KODEBRG=a.KODEBRG
where a.KODEBRG=@KodeBrg and a.NOBUKTI=@NoBukti

GO

-- ---------- 5. Trigger stok DBHASILPRDDET (salinan apa adanya dari DBMas) ----------
IF OBJECT_ID('dbo.TRG_Add_DBHasilPrdDet', 'TR') IS NOT NULL DROP TRIGGER dbo.TRG_Add_DBHasilPrdDet;
GO
CREATE TRIGGER [dbo].[TRG_Add_DBHasilPrdDet] ON dbo.DBHASILPRDDET 
FOR INSERT
AS

Update dbstockbrg set QntHPrd=QntHPrd+(d.Qnt),Qnt2HPrd=Qnt2HPrd+(d.QNt/Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end)
from inserted d, DBHASILPRD a,DBBARANG b
where d.nobukti=a.nobukti and d.kodebrg=dbstockbrg.Kodebrg and d.KODEBRG=b.KODEBRG and
      d.Kodegdg=dbstockbrg.kodegdg and 
      Month(a.tanggal)=dbstockbrg.Bulan and year(a.tanggal)=dbstockbrg.Tahun

Insert into dbStockbrg(Bulan,Tahun,Kodebrg,Kodegdg,QntHPrd,Qnt2HPrd)
Select Month(a.tanggal),year(a.tanggal),d.Kodebrg,d.Kodegdg,(d.Qnt),(d.QNt/Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end)
From DBHASILPRD a, Inserted d,DBBARANG b
where a.Nobukti=d.nobukti and d.KODEBRG=b.KODEBRG and 
      not exists (select x.kodebrg 
                  from dbSTOCKBRG x
                  where x.kodebrg=d.kodebrg and x.kodegdg=d.kodegdg and 
                        x.Bulan=Month(a.Tanggal) and
                        x.tahun=year(a.tanggal) )

GO

IF OBJECT_ID('dbo.TRG_Add_DBHasilPrdWIPDet', 'TR') IS NOT NULL DROP TRIGGER dbo.TRG_Add_DBHasilPrdWIPDet;
GO
CREATE TRIGGER [dbo].[TRG_Add_DBHasilPrdWIPDet] ON dbo.DBHASILPRDDET 
FOR INSERT
AS

Update dbSTOCKWIP set QntMasuk=QntMasuk+(d.Qnt),Qnt2Masuk=Qnt2Masuk+(d.QNt/Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end)
from inserted d, DBHASILPRD a,DBBARANG b
where d.nobukti=a.nobukti and d.kodebrg=dbSTOCKWIP.Kodebrg and d.KODEBRG=b.KODEBRG and
      d.NoSPK=dbSTOCKWIP.NOSPK and 
      d.KodePrs=dbSTOCKWIP.KODEPRS and d.KodePrs<>'-' and
      Month(a.tanggal)=dbSTOCKWIP.Bulan and year(a.tanggal)=dbSTOCKWIP.Tahun

Insert into dbSTOCKWIP(Bulan,Tahun,Kodebrg,NOSPK,KODEPRS,QntMasuk,Qnt2Masuk)
Select Month(a.tanggal),year(a.tanggal),d.Kodebrg,d.NoSPK,d.KodePrs,(d.Qnt),(d.QNt/Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end)
From DBHASILPRD a, Inserted d,DBBARANG b
where a.Nobukti=d.nobukti and d.KODEBRG=b.KODEBRG and 
      not exists (select x.kodebrg 
                  from dbSTOCKWIP x
                  where x.kodebrg=d.kodebrg and x.NOSPK=d.NoSPK and x.KODEPRS=d.KodePrs and
                        x.Bulan=Month(a.Tanggal) and
                        x.tahun=year(a.tanggal) )

GO

IF OBJECT_ID('dbo.Trg_DEL_DBHasilPrdDet', 'TR') IS NOT NULL DROP TRIGGER dbo.Trg_DEL_DBHasilPrdDet;
GO
CREATE TRIGGER [dbo].[Trg_DEL_DBHasilPrdDet] ON dbo.DBHASILPRDDET 
FOR DELETE
AS

Update dbstockbrg set QntHPrd=QntHPrd-(d.Qnt),Qnt2HPrd=Qnt2HPrd-d.Qnt/Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end
from deleted d, DBHASILPRD a,DBBARANG b
where d.nobukti=a.nobukti and d.kodebrg=dbstockbrg.Kodebrg and b.KODEBRG=d.KODEBRG and
      d.Kodegdg=dbstockbrg.kodegdg and 
      Month(a.tanggal)=dbstockbrg.Bulan and year(a.tanggal)=dbstockbrg.Tahun

GO

IF OBJECT_ID('dbo.Trg_DEL_DBHasilPrdWIPDet', 'TR') IS NOT NULL DROP TRIGGER dbo.Trg_DEL_DBHasilPrdWIPDet;
GO
CREATE TRIGGER [dbo].[Trg_DEL_DBHasilPrdWIPDet] ON dbo.DBHASILPRDDET 
FOR DELETE
AS

Update dbSTOCKWIP set QntMasuk=QntMasuk-(d.Qnt),Qnt2Masuk=Qnt2Masuk-d.Qnt/Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end
from deleted d, DBHASILPRD a,DBBARANG b
where d.nobukti=a.nobukti and d.kodebrg=dbSTOCKWIP.Kodebrg and b.KODEBRG=d.KODEBRG and dbSTOCKWIP.NOSPK=d.NoSPK
      and dbSTOCKWIP.KODEPRS=d.KodePrs and 
      Month(a.tanggal)=dbSTOCKWIP.Bulan and year(a.tanggal)=dbSTOCKWIP.Tahun

GO

IF OBJECT_ID('dbo.Trg_UPD_DBHasilPrddet', 'TR') IS NOT NULL DROP TRIGGER dbo.Trg_UPD_DBHasilPrddet;
GO
CREATE TRIGGER [dbo].[Trg_UPD_DBHasilPrddet] ON dbo.DBHASILPRDDET 
FOR UPDATE
AS

declare @QntN numeric(18,2), @QntO numeric(18,2)

select @QntO=Qnt*Isi from Deleted
select @QntN=Qnt*Isi from Inserted

--if @QntO<>@QntN

begin

Update dbstockbrg set QntHPrd=QntHPrd-(d.Qnt),Qnt2HPrd=Qnt2HPrd-(d.Qnt/(Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end))
from Deleted d, DBHasilPrd a,DBBARANG B
where d.nobukti=a.nobukti and d.kodebrg=dbstockbrg.Kodebrg and b.KODEBRG=d.KODEBRG and
      d.Kodegdg=dbstockbrg.kodegdg and 
      Month(a.tanggal)=dbstockbrg.Bulan and year(a.tanggal)=dbstockbrg.Tahun

Update dbstockbrg set QntHPrd=QntHPrd+(d.Qnt),Qnt2HPrd=Qnt2HPrd+(d.Qnt/(Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end))
from Inserted d, DBHasilPrd a,DBBARANG B
where d.nobukti=a.nobukti and d.kodebrg=dbstockbrg.Kodebrg and b.KODEBRG=d.KODEBRG and
      d.Kodegdg=dbstockbrg.kodegdg and 
      Month(a.tanggal)=dbstockbrg.Bulan and year(a.tanggal)=dbstockbrg.Tahun

Insert into dbStockbrg(Bulan,Tahun,Kodebrg,Kodegdg,QntHPrd,Qnt2HPrd)
Select Month(a.tanggal),year(a.tanggal),d.Kodebrg,d.Kodegdg,(d.Qnt),d.Qnt/(Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end)
From DBHasilPrd a, Inserted d,DBBARANG B
where a.Nobukti=d.nobukti and b.KODEBRG=d.KODEBRG and
      not exists (select x.kodebrg 
                  from dbSTOCKBRG x
                  where x.kodebrg=d.kodebrg and x.kodegdg=d.kodegdg and 
                        x.Bulan=Month(a.Tanggal) and
                        x.tahun=year(a.tanggal) )

end

GO

IF OBJECT_ID('dbo.Trg_UPD_DBHasilPrdWIPdet', 'TR') IS NOT NULL DROP TRIGGER dbo.Trg_UPD_DBHasilPrdWIPdet;
GO
CREATE TRIGGER [dbo].[Trg_UPD_DBHasilPrdWIPdet] ON dbo.DBHASILPRDDET 
FOR UPDATE
AS

declare @QntN numeric(18,2), @QntO numeric(18,2)

select @QntO=Qnt*Isi from Deleted
select @QntN=Qnt*Isi from Inserted

--if @QntO<>@QntN

begin

Update dbSTOCKWIP set QntMasuk=QntMasuk-(d.Qnt),Qnt2Masuk=Qnt2Masuk-(d.Qnt/(Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end))
from Deleted d, DBHasilPrd a,DBBARANG B
where d.nobukti=a.nobukti and d.kodebrg=dbSTOCKWIP.Kodebrg and b.KODEBRG=d.KODEBRG and
      d.NoSPK=dbSTOCKWIP.NOSPK and d.KodePrs=dbSTOCKWIP.KODEPRS and 
      Month(a.tanggal)=dbSTOCKWIP.Bulan and year(a.tanggal)=dbSTOCKWIP.Tahun

Update dbSTOCKWIP set QntMasuk=QntMasuk+(d.Qnt),Qnt2Masuk=Qnt2Masuk+(d.Qnt/(Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end))
from Inserted d, DBHasilPrd a,DBBARANG B
where d.nobukti=a.nobukti and d.kodebrg=dbSTOCKWIP.Kodebrg and b.KODEBRG=d.KODEBRG and
      d.NoSPK=dbSTOCKWIP.NOSPK and d.KodePrs=dbSTOCKWIP.KODEPRS and 
      Month(a.tanggal)=dbSTOCKWIP.Bulan and year(a.tanggal)=dbSTOCKWIP.Tahun

Insert into dbSTOCKWIP(Bulan,Tahun,Kodebrg,NOSPK,KODEPRS,QntMasuk,Qnt2Masuk)
Select Month(a.tanggal),year(a.tanggal),d.Kodebrg,d.NoSPK,d.KodePrs,(d.Qnt),d.Qnt/(Case When Isnull(B.ISI2,0)=0 Then 1 else b.ISI2 end)
From DBHasilPrd a, Inserted d,DBBARANG B
where a.Nobukti=d.nobukti and b.KODEBRG=d.KODEBRG and
      not exists (select x.kodebrg 
                  from dbSTOCKWIP x
                  where x.kodebrg=d.kodebrg and x.NOSPK=d.NoSPK and x.KODEPRS=d.KodePrs and 
                        x.Bulan=Month(a.Tanggal) and
                        x.tahun=year(a.tanggal) )

end

GO

