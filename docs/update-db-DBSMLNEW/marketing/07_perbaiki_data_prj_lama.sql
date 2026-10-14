-- Disalin dari newSML/docs/update-db-DBSMLBARU/marketing/09 + 10 (14 Oktober 2026),
-- dijalankan di DBSMLNEW supaya newProgramAJC sama dengan newSML.

USE DBSMLNEW
GO

-- 09 - Perbaiki QNT1 PRJ lama SML/PRJ/00001/0416 urut 2 (13 Oktober 2026)
-- QNT1 tersimpan 548.60 (salah ketik 10x). Yang benar 54.86 MTR x 3.281 = 180.00 FEET,
-- sama dengan QNT1 SPR SML/SPR/00001/0416 yang sudah meretur seluruhnya. Akibat salah
-- ketik itu PRJ ini selalu tampil di tab Outstanding PRJ menu Retur Penjualan Gudang
-- padahal tidak bisa diproses ("Data tidak ditemukkan").
-- Aman dijalankan ulang: hanya mengubah baris yang masih bernilai 548.60.

update dbPRRJualDet
set QNT1 = 180.00
where NoBukti = 'SML/PRJ/00001/0416' and Urut = 2 and QNT1 = 548.60

select NoBukti, Urut, QNT, QNT1, QNT2 from dbPRRJualDet where NoBukti = 'SML/PRJ/00001/0416'

GO

-- 10 - Perbaiki QNT1/QNT2 PRJ lama SML/PRJ/00001/0916 urut 1 (13 Oktober 2026)
-- QNT 8 PAK tetapi QNT1 dan QNT2 tersimpan 0. Barang bersatuan 1 (PAK/PAK, ISI 1), jadi
-- yang benar QNT1 = QNT2 = 8. Belum pernah dipakai SPR. Tanpa perbaikan ini, kalau PRJ ini
-- diotorisasi ia tidak akan muncul di Outstanding PRJ (sisa dihitung dari QNT1 = 0).
-- Aman dijalankan ulang: hanya mengubah baris yang masih bernilai 0.

update dbPRRJualDet
set QNT1 = 8.00, QNT2 = 8.00
where NoBukti = 'SML/PRJ/00001/0916' and Urut = 1 and QNT = 8.00 and QNT1 = 0 and QNT2 = 0

select NoBukti, Urut, QNT, QNT1, QNT2 from dbPRRJualDet where NoBukti = 'SML/PRJ/00001/0916'
