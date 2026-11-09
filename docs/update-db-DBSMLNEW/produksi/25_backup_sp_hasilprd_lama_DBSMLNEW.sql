-- BACKUP definisi Sp_HasilPrd LAMA di DBSMLNEW (sebelum diganti versi DBMas oleh skrip 25, 9 Nov 2026).
-- Versi ini merujuk kolom dbHasilPrd.KodeBrg/Qnt/KodeMesin/... yang tidak ada, jadi memang tidak bisa dipakai.
-- Hanya untuk arsip - JANGAN dijalankan.

CREATE   Procedure [dbo].[Sp_HasilPrd]
@Choice varchar(1),
@NoBukti varchar(20)='',
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
@KodePrs1 varchar(20)='',
@KodeMesin varchar(20)='',
@TanggalMsn datetime='',
@JamAwal datetime='',
@JamAkhir datetime=''

As
Begin tran
if @Choice='I'
begin
/*
  select @Urut=isnull(max(urut),0)+1 from dbHasilPrddet Where NoBukti=@NoBukti
  if not exists(select * from dbHasilPrd Where NoBukti=@NoBukti) 
  begin
    insert into dbHasilPrd (NOBUKTI, NOURUT, TANGGAL, KETERANGAN, NoSPK, KodePrs, KodeBrg, Qnt, NoSat, Satuan, Isi, KodeMesin, TanggalMesin, JamAwal, JamAkhir)
    values (@NOBUKTI, @NOURUT, @TANGGAL, @KETERANGAN, @NoSPK, @KodePrs1, @KodeBrg, @Qnt, @NoSat, @Satuan, @Isi, @KodeMesin, @TanggalMsn, @JamAwal, @JamAkhir)
  end
  insert into dbHasilPrdDET (NOBUKTI, URUT,  KODEBRG, Qnt, NOSAT, ISI, SATUAN,KodeGdg,NoSPK, KodePrs)
  values(@NOBUKTI, @URUT, @KodeBrg2, @Qnt2, @NoSat2, @Isi2, @Satuan2, '','',@KodePrs2)
*/  
  insert into dbHasilPrd (NOBUKTI, NOURUT, TANGGAL, KETERANGAN, NoSPK, KodePrs, KodeBrg, Qnt, NoSat, Satuan, Isi, KodeMesin, TanggalMesin, JamAwal, JamAkhir)
  values (@NOBUKTI, @NOURUT, @TANGGAL, @KETERANGAN, @NoSPK, @KodePrs1, @KodeBrg, @Qnt, @NoSat, @Satuan, @Isi, @KodeMesin, @TanggalMsn, @JamAwal, @JamAkhir)

end
if @Choice='U'
begin
  update DBHASILPRD set KETERANGAN=@Keterangan, NoSPK=@NoSPK, KodePrs=@KodePrs1, KodeBrg=@KodeBrg, 
						Qnt=@Qnt, NoSat=@NoSat, Satuan=@Satuan, Isi=@Isi, 
						KodeMesin=@KodeMesin, TanggalMesin=@TanggalMsn, JamAwal=@JamAwal, JamAkhir=@JamAkhir
  where NOBUKTI = @NoBukti
  
  --update dbHasilPrdDET set KodeBrg=@KODEBRG, Qnt=@Qnt2, NOSAT=@NoSat2, ISI=@Isi2, SATUAN=@Satuan2,KodeGdg=@KodeGdg
  --where NoBukti=@NoBukti and Urut=@Urut
end
if @Choice='D'
begin
  --delete dbHasilPrdDET where NoBukti=@NoBukti and Urut=@Urut 
  --if not exists( select NoBukti from dbHasilPrdDET where NoBukti=@NoBukti)
  --begin
  --  delete dbHasilPrd where NoBukti=@NoBukti
  --end
  delete dbHasilPrd where NoBukti=@NoBukti
end
if @@error<>0  goto jikasalah
Commit tran
Return
JikaSalah: Rollback tran
           Return













