-- =====================================================================================
-- 26 - Hak akses penuh user SA untuk menu Hasil Produksi
-- Database : DBSMLNEW
-- Dijalankan: 9 November 2026 (atas perintah user).
--
-- Menu 08007 'Hasil Produksi' (href produksihasilproduksi) dan header '08' Produksi.
-- Sebelumnya SA sudah punya akses/tambah/koreksi/hapus, tetapi IsOtorisasi1 dan IsBatal = 0
-- (tidak bisa otorisasi / batal otorisasi). Semua hak diatur di Berkas > Set Pemakai.
-- Baris DBFLMENUWEB dibuat kalau belum ada. Aman dijalankan ulang.
-- =====================================================================================

INSERT INTO DBFLMENUWEB (USERID, L1, HASACCESS, ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK, ISEXPORT,
                         IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5, TIPE, IsBatal, pembatalan)
SELECT 'SA', m.KODEMENU, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, '', 1, 0
FROM DBMENUWEB m
WHERE m.KODEMENU IN ('08', '08007')
  AND NOT EXISTS (SELECT 1 FROM DBFLMENUWEB f WHERE f.USERID = 'SA' AND f.L1 = m.KODEMENU);
GO

UPDATE DBFLMENUWEB
   SET HASACCESS = 1, ISTAMBAH = 1, ISKOREKSI = 1, ISHAPUS = 1, ISCETAK = 1, ISEXPORT = 1,
       IsOtorisasi1 = 1, IsBatal = 1
 WHERE USERID = 'SA' AND L1 IN ('08', '08007');
GO
