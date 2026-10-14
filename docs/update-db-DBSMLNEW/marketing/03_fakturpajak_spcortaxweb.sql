-- 03 - Faktur Pajak: SP export Coretax versi web (14 Oktober 2026)
--
-- FakturPajakController@spExport (export Coretax beberapa invoice sekaligus) menulis invoice
-- terpilih ke DBCUSTOMIZE (tipe '7777777') lalu memanggil SPHeadCortaxweb / SPDetailCortaxweb.
-- Kedua SP ini ada di DBSMLBARU tetapi belum ada di DBSMLNEW, sehingga export di newProgramAJC
-- gagal. Definisi disalin apa adanya dari DBSMLBARU. SPHeadCortax/SPDetailCortax lama TIDAK diubah.
--
-- Hanya MENAMBAH dua SP baru. Aman dijalankan ulang (dibuat hanya kalau belum ada).

USE DBSMLNEW
GO

IF OBJECT_ID('dbo.SPHeadCortaxweb', 'P') IS NULL
  EXEC('CREATE PROCEDURE dbo.SPHeadCortaxweb AS RETURN 0')
GO

IF OBJECT_ID('dbo.SPDetailCortaxweb', 'P') IS NULL
  EXEC('CREATE PROCEDURE dbo.SPDetailCortaxweb AS RETURN 0')
GO


ALTER Procedure [dbo].[SPHeadCortaxweb]
--declare
@nobukti Varchar(30),
@iduser varchar(20)=''
as

--select @nobukti='SML/INVC/00001/0626'




select A.* from (

select cast(ROW_NUMBER() Over(PARTITION By stuffingdate  Order by stuffingdate) as varchar(5))Baris
--,convert(date,a.Tanggal,13) Tanggal,
,CONVERT(VARCHAR(10), a.tanggal, 103) + ' ' + CONVERT(VARCHAR(8), a.tanggal, 108)  Tanggal
,'Normal' JenisFaktur,'04' KodeTransaksi,
'' KeteranganTambahan,'' Dokumenpendukung,'' Periodedokpendukung,A.NoBukti + ' '+c.NoPesanan referensi,'' capfasilitas ,
(select +'0'+Replace(replace(NPWP,'.',''),'-','')  from DBPERUSAHAAN) IDTKUPenjual,
Replace(replace(b.NPWP,'.',''),'-','') NPWPNIKPembeli,'TIN'JenisIDPembeli,'IDN' NegaraPembeli,'' NomorDokumenPembeli,B.NAMACUSTSUPP NamaPembeli,b.ALAMATPKP1 AlamatPembeli,b.EMAIL EmailPembeli
,Replace(replace(b.NPWP,'.',''),'-','')+'000000' IDTKUPembeli
,(select +'0'+Replace(replace(NPWP,'.',''),'-','')+'000000'  from DBPERUSAHAAN) IDTKUPenjual2
from dbinvoicepl A
left outer join DBCUSTSUPP b on a.KodeCustSupp=b.KODECUSTSUPP
left outer join DBSO C on a.NoSPP=c.NOBUKTI
where a.NoBukti in (select ID from DBCUSTOMIZE where IDUser=@iduser and   Tipe in ('tax','7777777'))

) A
order by case when  A.baris = 'END' then 99999 else cast(A.baris as int) end




GO


ALTER Procedure [dbo].[SPDetailCortaxweb]
--declare
@nobukti Varchar(20),
@iduser varchar(20)

--select @nobukti='SML/INVC/00193/0726'
as

select *
--A.Baris,BarangJasa,KodeBarangJasa,NamaBarangJasa,NamaSatuanUkur,HargaSatuan,SUM(JumlahBarangJasa) JumlahBarangJasa,
--SUM(TotalDiskon) TotalDiskon,SUM(DPP) DPP,SUM(DPPNilaiLain) DPPNilaiLain,TarifPPN,SUM(ppn) PPN,tarifppnbm,ppnbm
from (


select cast(c.RowNum as varchar(5)) Baris,

'A' BarangJasa,--a.KodeBrg 
'000000' KodeBarangJasa,x5.nmbrg NamaBarangJasa,x5.satxbrg NamaSatuanUkur,
/*
cast(a.HARGA as numeric(18,0)) HargaSatuan,
cast(round(SUM(a.QNT2),0) as numeric(18,0)) JumlahBarangJasa,
cast(round(SUM(a.DISCTOT),0) as numeric(18,0)) TotalDiskon,
cast(round(SUM(a.NDPP),0) as numeric(18,0)) DPP 
--SUM(a.NDPP) DPP
,---cast(round(SUM(a.NDPP * 11.00/12.00),0) as numeric(18,0)) DPPNilaiLain,
SUM(a.NDPP * 11.00/12.00) DPPNilaiLain,
12 TarifPPN,
--,cast(round(SUM(a.NPPN),0) as numeric(18,0)) PPN,
SUM(a.NPPN)   PPN,
*/

a.HARGA HargaSatuan,SUM(a.QNT2) JumlahBarangJasa,SUM(a.DISCTOT) TotalDiskon,SUM(Round(a.NDPP,2)) DPP 
,SUM(a.NDPP * 11.00/12.00) DPPNilaiLain,12 TarifPPN
,SUM(a.NPPN) PPN


,0 tarifppnbm, 0 ppnbm



from dbinvoicepldet A
left outer join DBBARANG b on a.KodeBrg=b.KODEBRG
left outer join (select ROW_NUMBER() Over(PARTITION By stuffingdate  Order by stuffingdate) RowNum,nobukti
				from dbinvoicepl A
				left outer join DBCUSTSUPP b on a.KodeCustSupp=b.KODECUSTSUPP
				--where NoBukti = @nobukti
				where NoBukti in  (select ID from DBCUSTOMIZE where IDUser=@Iduser and  Tipe in ('tax','7777777'))
				) c on a.NoBukti=c.NoBukti
LEFT outer join (select a.NoBukti,a.Urut,case when c.NAMABRG<>'' then c.NAMABRG else d.NAMABRG end nmbrg	,
     -- case when c.SATX<>'' then c.SATX else case when a.NOSAT=1 then d.SAT1 when a.NOSAT=2 then d.SAT2 when a.NOSAT=3 then d.SAT3 end end satxbrg
     ISNULL(E.KODETAX,'')satxbrg
					  from dbInvoicePLDet a
					  left outer join dbSPBDet b on a.NoSPB=b.NoBukti and a.UrutSPB=b.Urut
					  left outer join DBSODET c on b.NoSo=c.NOBUKTI and b.UrutSo=c.URUT
					  left outer join DBBARANG d on a.KodeBrg=D.KodeBrg
					  LEFT OUTER JOIN DBSATTAX E ON C.SATTAX=E.NAMATAX
					  ) x5 on A.NoBukti=x5.NoBukti and A.Urut=x5.Urut
					  
					  
					  	
--where c.NoBukti = @nobukti 				
where c.NoBukti in  (select ID from DBCUSTOMIZE where IDUser=@Iduser and  Tipe in ('tax','7777777')) 
 --and 
 --a.NoBukti='SML/INVC/00193/0726'
 
 GROUP BY X5.nmbrg,X5.satxbrg,A.HARGA,A.NoSPB,C.RowNum,A.KodeBrg
--union all

--select 'END' Baris,'' Barangjasa,'' KOdebarangjasa,'' namabarangjasa,'' namasatuanukur,null hargasatuan,null jumlahbarang,null totaldiskon,null dpp,null dppnilailain,null tarifppn
--,null ppn,null tarifppnbm,null ppnbm
)A
--group by Baris,BarangJasa,KodeBarangJasa,NamaBarangJasa,NamaSatuanUkur,HargaSatuan,TarifPPN,tarifppnbm,ppnbm
order by case when  A.baris = 'END' then 99999 else cast(A.baris as int) end

GO
