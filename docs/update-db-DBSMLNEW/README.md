# Update Database DBSMLNEW — samakan purchasing & marketing dengan newSML

Pada 14 Oktober 2026 semua controller purchasing dan marketing newSML disalin ke newProgramAJC
(kecuali menu **Surat Jalan**, yang tetap versi newProgramAJC). Kode newSML itu bergantung pada
beberapa perubahan database yang sudah dijalankan di DBSMLBARU (lihat
`newSML/docs/update-db-DBSMLBARU/`). Folder ini berisi perubahan yang sama untuk **DBSMLNEW**,
database newProgramAJC.

Semua skrip hanya **menambah** atau membetulkan rumus/data tertentu, dan aman dijalankan ulang.
Perubahan purchasing lain dari newSML (VwPPL, DBHEADERTABLE, menu web, vwMasterPO, vwBrowsPrRBeli)
dulu justru disalin dari DBSMLNEW, jadi sudah ada di sini.

## Urutan menjalankan

**Backup DBSMLNEW dulu.**

| No | File | Isi | Akibat kalau tidak dijalankan |
|---|---|---|---|
| 01 | `purchasing/01_vwoutporp_uang_muka_beli.sql` | Perbaiki rumus VWOUTPORP (PPN include: pakai QntOS dan NilaiPPN, bukan Qnt dan 1.11) | Validasi "Uang Muka melebihi PO" salah hitung untuk PO PPN include |
| 02 | `purchasing/02_debetnote_akun_bd.sql` | DBPOSTHUTPIUT Kode `BD` = 51300 | Akun lawan jurnal Debet Note kosong |
| 03 | `marketing/03_fakturpajak_spcortaxweb.sql` | SP baru `SPHeadCortaxweb`, `SPDetailCortaxweb` (salinan DBSMLBARU) | Export Coretax di menu Faktur Pajak error (SP tidak ada) |
| 04 | `marketing/04_hak_akses_sa_verifikasibarang.sql` | User SA akses penuh menu Verifikasi Barang (04013) | SA tidak punya tombol Batal Verifikasi |
| 05 | `marketing/05_index_dbspbdet_noso.sql` | Index `dbSPBDet (NoSo, UrutSo)` | Menu SO tetap jalan tetapi lebih lambat |
| 06 | `marketing/06_kreditnote_perkhp_dan_akun_bk.sql` | Default `''` untuk `DBKreditNoteDET.perkHP` + DBPOSTHUTPIUT Kode `BK` = 41200 | Item Kredit Note tidak tersimpan, akun debet jurnal KN kosong |
| 07 | `marketing/07_perbaiki_data_prj_lama.sql` | Perbaiki QNT1/QNT2 PRJ lama `SML/PRJ/00001/0416` dan `SML/PRJ/00001/0916` | PRJ 0416 terus tampil di Outstanding PRJ padahal tidak bisa diproses |
| 23 | `marketing/23_vwposthutpiut_kreditnote_valas.sql` | `vwPostHutPiut` bagian Kredit Note: nilai valas masuk `KreditD` (dulu `DebetD`) | KN valas yang diotorisasi malah menambah saldo valas piutang di DBHUTPIUT |

## Yang tidak perlu diubah

- Beberapa SP/view berbeda isi antara DBSMLBARU dan DBSMLNEW (mis. `sp_PO`, `sp_SO`, `vwOutPPL`,
  SP cetak). Kolom dan parameter yang dipakai kode newSML sudah ada di DBSMLNEW, jadi tidak disalin.
- Menu web (`DBMENUWEB`) untuk Verifikasi Barang, PO Customer, Kredit Note, dll. sudah ada di DBSMLNEW.
