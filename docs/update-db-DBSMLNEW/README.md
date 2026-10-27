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
| 08 | `beacukai/08_hak_akses_sa_bc.sql` | User SA akses penuh menu Dokumen BC 2.6.1 (`documentbc261`), Dokumen BC 2.3 (`documentbc23`), Dokumen BC 4.0 (`documentbc40`), Master Entitas BC (`masterbcentitas`) dan 8 master referensi BC (`masterbcnegara`, `masterbcpelabuhan`, `masterbckontainer`, `masterbcpostarif`, `masterbckategoribarang`, `masterbcjenisdokumen`, `masterbcjeniskemasan`, `masterbcjenissatuan`). Jalankan **setelah** baris DBMENUWEB menu-menu itu dibuat | SA diarahkan ke /home saat membuka menu-menu itu |
| 09 | `beacukai/09_tabel_documentbc261.sql` | Tabel baru `DOCUMENTBC261` + 6 tabel anak (barang, bahan baku, kemasan, kontainer, jaminan, dokumen) dan `CEISATOKEN` (token login CEISA per user). Master BC (`NEGARA`, `ENTITAS`, `JENISSATUAN`, dll.) dibuat terpisah | Menu Dokumen BC 2.6.1 error (tabel tidak ada); Login BC gagal |
| 10 | `beacukai/10_pisah_ukuran_tipe_kontainer.sql` | Master Kontainer dipisah: `KONTAINER` di-rename jadi `UKURANKONTAINER` (kolom `KODEUKURANKONTAINER`, `NAMAUKURANKONTAINER`; ukuran dobel digabung, kolom tipe lama dibuang) dan tabel baru `TIPEKONTAINER` (`KODETIPEKONTAINER`, `NAMATIPEKONTAINER`, berisi 9 tipe CEISA). Jalankan **setelah** 09 | Halaman Master Kontainer dan modal Peti Kemas di BC 2.6.1 error (tabel tidak ada) |
| 11 | `beacukai/11_tabel_documentbc23.sql` | Tabel baru `DOCUMENTBC23` (header; kemasan, kontainer dan pengangkut jadi kolom; `NOBUKTI` = `DBPO.NOBUKTI` unik, `BCTYPE`, `SENT`, `NOMORDOKUMENPABEAN`, `TANGGALDOKUMENPABEAN`), `DOCUMENTBC23BARANG` dan `DOCUMENTBC23DOKUMEN`. `DBPO` / `DBPODET` tidak diubah. Memakai `CEISATOKEN` dari 09 | Menu Dokumen BC 2.3 error (tabel tidak ada) |
| 12 | `beacukai/12_tabel_documentbc40.sql` | Tabel baru `DOCUMENTBC40` (header; kemasan dan pengangkut jadi kolom; `NOBUKTI` = `DBPO.NOBUKTI` unik, `BCTYPE`, `SENT`, `NOMORDOKUMENPABEAN`, `TANGGALAJU`), `DOCUMENTBC40BARANG` dan `DOCUMENTBC40DOKUMEN`. Sumber = PO pemasok dalam negeri; `DBPO` / `DBPODET` tidak diubah. Memakai `CEISATOKEN` dari 09 | Menu Dokumen BC 4.0 error (tabel tidak ada) |
| 23 | `marketing/23_vwposthutpiut_kreditnote_valas.sql` | `vwPostHutPiut` bagian Kredit Note: nilai valas masuk `KreditD` (dulu `DebetD`) | KN valas yang diotorisasi malah menambah saldo valas piutang di DBHUTPIUT |

## Yang tidak perlu diubah

- Beberapa SP/view berbeda isi antara DBSMLBARU dan DBSMLNEW (mis. `sp_PO`, `sp_SO`, `vwOutPPL`,
  SP cetak). Kolom dan parameter yang dipakai kode newSML sudah ada di DBSMLNEW, jadi tidak disalin.
- Menu web (`DBMENUWEB`) untuk Verifikasi Barang, PO Customer, Kredit Note, dll. sudah ada di DBSMLNEW.
