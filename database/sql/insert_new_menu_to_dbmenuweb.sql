-- WMS: insert DBSMLBARU.dbo.new_menu ke DBSMLNEW.dbo.DBMENUWEB sebagai grup baru di bawah Utilitas (KODEMENU 21).
-- Jalankan di database DBSMLNEW. Aman dijalankan ulang (tiap baris dicek NOT EXISTS).
--
-- Struktur: 21 WMS (L0=0) -> 2101..2105 grup (L0=1) -> item 6 digit (L0=2), seperti Pembelian (03 -> 0301 -> 030101).
--  * Master Menu hanya berisi: Gudang, Barang, Barcode Barang. Grup lain (In Bound, Out Bound, Gudang, Dashboard) lengkap.
--  * ACCESS = '8' + ACCESS lama (mis. 01 -> 801); sudah dicek tidak bentrok. HeaderMenu = 21, OL = 0, TipeTrans = '', icon/color NULL.
--  * Dilewati (href sudah ada di DBMENUWEB): 009 Ganti Password (gantipassword); 011 Penerimaan Barang (newpo); 025 Retur Beli (returbeli)
--  * Header '0291 Ubah Kemasan' diratakan: 3 anaknya jadi item langsung di grup Out Bound (2103).
--  * Langkah 0 membersihkan hasil rencana lama (0608-0612 di bawah Gudang) beserta izin di DBFLMENUWEB.

-- 1) Backup dulu (dilewati kalau tabel backup sudah ada)
IF OBJECT_ID('DBMENUWEB_BAK_20261113') IS NULL
    SELECT * INTO DBMENUWEB_BAK_20261113 FROM DBMENUWEB;
GO

BEGIN TRAN;

-- 2) Insert
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '21')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('21', 'WMS', 0, 0, 0, '', 21, '#', NULL, NULL); -- new_menu -
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '2101')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('2101', 'Master Menu', 1, 80, 0, '', 21, '#', NULL, NULL); -- new_menu 00
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210101')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210101', 'Gudang', 2, 801, 0, '', 21, 'gudang', NULL, NULL); -- new_menu 001
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210102')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210102', 'Barang', 2, 802, 0, '', 21, 'barangjadi', NULL, NULL); -- new_menu 002
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210103')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210103', 'Barcode Barang', 2, 80091, 0, '', 21, 'barcodebarang', NULL, NULL); -- new_menu 0091
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '2102')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('2102', 'In Bound', 1, 81, 0, '', 21, '#', NULL, NULL); -- new_menu 01
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210201')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210201', 'QC LPB', 2, 812, 0, '', 21, 'qclpb', NULL, NULL); -- new_menu 012
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210202')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210202', 'Put Away', 2, 813, 0, '', 21, 'putaway', NULL, NULL); -- new_menu 013
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210203')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210203', 'Retur Jual', 2, 814, 0, '', 21, 'newreturjual', NULL, NULL); -- new_menu 014
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210204')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210204', 'Retur Srt Jln', 2, 816, 0, '', 21, 'newretursuratjalan', NULL, NULL); -- new_menu 016
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210205')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210205', 'Terima Ubah Kemasan', 2, 817, 0, '', 21, 'terimaubahkemasan', NULL, NULL); -- new_menu 017
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210206')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210206', 'Transfer Lokasi In', 2, 818, 0, '', 21, 'transferlokasiin', NULL, NULL); -- new_menu 018
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210207')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210207', 'Retur Serah Sample', 2, 8019, 0, '', 21, 'returserahsample', NULL, NULL); -- new_menu 019
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '2103')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('2103', 'Out Bound', 1, 82, 0, '', 21, '#', NULL, NULL); -- new_menu 02
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210301')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210301', 'Picking List SO', 2, 821, 0, '', 21, 'newpermintaanpersiapan', NULL, NULL); -- new_menu 021
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210302')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210302', 'Picking List Non SO', 2, 8211, 0, '', 21, 'newpermintaanpersiapannonso', NULL, NULL); -- new_menu 0211
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210303')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210303', 'Picking List Booking', 2, 8212, 0, '', 21, 'newpermintaanpersiapanboo', NULL, NULL); -- new_menu 0212
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210304')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210304', 'Picking', 2, 8022, 0, '', 21, 'newpicking', NULL, NULL); -- new_menu 022
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210305')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210305', 'QC Picking', 2, 8023, 0, '', 21, 'qcpicking', NULL, NULL); -- new_menu 023
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210306')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210306', 'Packing', 2, 80231, 0, '', 21, 'packing', NULL, NULL); -- new_menu 0231
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210307')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210307', 'SPB', 2, 824, 0, '', 21, 'persiapanspb', NULL, NULL); -- new_menu 024
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210308')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210308', 'Transfer Barang', 2, 826, 0, '', 21, 'newtransferbarang', NULL, NULL); -- new_menu 026
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210309')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210309', 'Serah Sample', 2, 827, 0, '', 21, 'penyerahansample', NULL, NULL); -- new_menu 027
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210310')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210310', 'Serah Konsi', 2, 828, 0, '', 21, 'penyerahankonsi', NULL, NULL); -- new_menu 028
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210311')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210311', 'Transfer Gudang Out', 2, 80290, 0, '', 21, 'transferlokasiout', NULL, NULL); -- new_menu 0290
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210312')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210312', 'Permintaan UK', 2, 8029101, 0, '', 21, 'permintaanubahkemasan', NULL, NULL); -- new_menu 029101
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210313')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210313', 'Picking UK', 2, 8029102, 0, '', 21, 'pickinguk', NULL, NULL); -- new_menu 029102
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210314')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210314', 'Finalisasi UK', 2, 8029103, 0, '', 21, 'ubahkemasanfinalisasi', NULL, NULL); -- new_menu 029103
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '2104')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('2104', 'Gudang', 1, 83, 0, '', 21, '#', NULL, NULL); -- new_menu 03
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210401')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210401', 'Permintaan Pemakaian Internal', 2, 8031, 0, '', 21, 'permintaanpenyerahangudang', NULL, NULL); -- new_menu 031
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210402')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210402', 'Pemakaian Internal', 2, 832, 0, '', 21, 'penyerahangudang', NULL, NULL); -- new_menu 032
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210403')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210403', 'Opname Gudang', 2, 833, 0, '', 21, 'opnamegudang', NULL, NULL); -- new_menu 033
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210404')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210404', 'Permintaan Transfer Gudang', 2, 8034, 0, '', 21, 'transferlokasi', NULL, NULL); -- new_menu 034
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210405')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210405', 'Transfer Lokasi (Scan)', 2, 8036, 0, '', 21, 'transferlokasisite', NULL, NULL); -- new_menu 036
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210406')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210406', 'Opname Harian', 2, 8037, 0, '', 21, 'opnamegdg', NULL, NULL); -- new_menu 037
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210407')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210407', 'Inspeksi Opname Harian', 2, 8038, 0, '', 21, 'opnamegdginspeksi', NULL, NULL); -- new_menu 038
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '2105')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('2105', 'Dashboard', 1, 84, 0, '', 21, '#', NULL, NULL); -- new_menu 04
IF NOT EXISTS (SELECT 1 FROM DBMENUWEB WHERE KODEMENU = '210501')
    INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
    VALUES ('210501', 'Opname Harian', 2, 8041, 0, '', 21, 'dashboardopnameharian', NULL, NULL); -- new_menu 041

-- Cek: harus 0 baris (ACCESS ganda di antara baris WMS)
SELECT ACCESS, COUNT(*) AS jumlah FROM DBMENUWEB WHERE ACCESS IN (SELECT ACCESS FROM DBMENUWEB WHERE KODEMENU >= '21' AND ACCESS <> 0) GROUP BY ACCESS HAVING COUNT(*) > 1;
SELECT KODEMENU, Keterangan, L0, ACCESS, href FROM DBMENUWEB WHERE KODEMENU >= '20' ORDER BY KODEMENU;

-- Kalau hasil sudah benar jalankan COMMIT; kalau tidak, ROLLBACK.
COMMIT TRAN;
-- ROLLBACK TRAN;
