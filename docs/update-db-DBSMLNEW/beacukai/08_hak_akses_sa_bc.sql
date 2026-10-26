-- 08 - Hak akses penuh user SA ke menu Dokumen BC 2.6.1 (href documentbc261), Master Entitas
-- Bea Cukai (href masterbcentitas) dan 8 master referensi BC (href masterbcnegara, masterbcpelabuhan,
-- masterbckontainer, masterbcpostarif, masterbckategoribarang, masterbcjenisdokumen,
-- masterbcjeniskemasan, masterbcjenissatuan). Dibuat 26 Oktober 2026.
--
-- JALANKAN SETELAH baris DBMENUWEB menu-menu itu dibuat (KODEMENU bebas, href harus persis
-- seperti di atas - getAkses mencari menu lewat href, bukan KODEMENU).
-- ISEXPORT dipakai sebagai hak "Kirim ke CEISA" + "Preview JSON" di BC 2.6.1.
-- HANYA user SA; user lain diatur lewat Berkas > Set Pemakai.
-- Aman dijalankan ulang.

USE DBSMLNEW
GO

-- Baris DBFLMENUWEB SA untuk menu baru belum tentu ada (biasanya baru dibuat saat Set Pemakai
-- dibuka) - sisipkan dulu dengan pola yang sama seperti NewSetPemakaiController::listAkses.
INSERT INTO DBFLMENUWEB (USERID, L1, HASACCESS, ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK, ISEXPORT,
    IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5, TIPE, IsBatal, pembatalan)
SELECT 'SA', m.KODEMENU, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'STK', 0, 0
FROM DBMENUWEB m
WHERE m.href IN ('documentbc261', 'masterbcentitas', 'masterbcnegara', 'masterbcpelabuhan',
                 'masterbckontainer', 'masterbcpostarif', 'masterbckategoribarang', 'masterbcjenisdokumen',
                 'masterbcjeniskemasan', 'masterbcjenissatuan')
  AND NOT EXISTS (SELECT 1 FROM DBFLMENUWEB f WHERE f.USERID = 'SA' AND f.L1 = m.KODEMENU);

UPDATE f
SET HASACCESS = 1, ISTAMBAH = 1, ISKOREKSI = 1, ISHAPUS = 1, ISCETAK = 1, ISEXPORT = 1,
    IsOtorisasi1 = 1, IsOtorisasi2 = 1, IsOtorisasi3 = 1, IsOtorisasi4 = 1, IsOtorisasi5 = 1,
    IsBatal = 1
FROM DBFLMENUWEB f
JOIN DBMENUWEB m ON m.KODEMENU = f.L1
WHERE f.USERID = 'SA' AND m.href IN ('documentbc261', 'masterbcentitas', 'masterbcnegara', 'masterbcpelabuhan',
                 'masterbckontainer', 'masterbcpostarif', 'masterbckategoribarang', 'masterbcjenisdokumen',
                 'masterbcjeniskemasan', 'masterbcjenissatuan');

-- Cek hasil (harus 10 baris; kalau kurang, baris DBMENUWEB-nya belum dibuat)
SELECT m.href, f.USERID, f.L1, f.HASACCESS, f.ISTAMBAH, f.ISKOREKSI, f.ISHAPUS, f.ISEXPORT
FROM DBFLMENUWEB f
JOIN DBMENUWEB m ON m.KODEMENU = f.L1
WHERE f.USERID = 'SA' AND m.href IN ('documentbc261', 'masterbcentitas', 'masterbcnegara', 'masterbcpelabuhan',
                 'masterbckontainer', 'masterbcpostarif', 'masterbckategoribarang', 'masterbcjenisdokumen',
                 'masterbcjeniskemasan', 'masterbcjenissatuan');
