# BC 2.6.2 — update to the OFFICIAL CEISA payload schema

Hand-over spec for re-applying, in the **Villiger Flask app**, the changes already made in the Laravel app
(`D:\newProgramAJC`). It is a **diff against the Villiger behaviour documented in `docs/documentbc262_plan.md`**
(§9 payload, §11 quirks): read that plan first, then apply every section here. BC 2.6.2 is the mirror of BC 2.6.1 —
read **`bc261_update.md`** too; this document only lists what is different or new.

- Official schema: `docs/bc_schema/bc262.json`. Copy it into the Flask repo and read it at runtime.
- The send-time validator, the 422 / dialog / preview contract, the ISO-currency map and the message catalogue are the
  **same as BC 2.3** — implement them once following **`bc23_update.md` §1.4 and §2**, then use the BC 2.6.2 options in §2 below.
- Laravel reference implementation: `app/Services/BeaCukai/Bc262PayloadBuilder.php`,
  `app/Http/Controllers/DocumentBc/DocumentBc262Controller.php` (`WAJIB_ISI`, `masalahKirim`, `tolakPungutanGanda`, `spKirim`,
  `spPreview`, `susunPayload`), `resources/views/documentbc/documentbc262.blade.php`, `tests/Unit/Bc262PayloadBuilderTest.php`.
- Messages are Indonesian, exactly as written.
- Rule chosen with the user: **saving is never blocked by the schema check; every problem is listed when the user tries to SEND
  and in the Preview JSON dialog.** (The one save-time block is the duplicate Pungutan rule, §3.1.)
- **Database:** one new table, script `docs/update-db-DBSMLNEW/beacukai/19_tabel_documentbc262_pungutan.sql` (`DOCUMENTBC262PUNGUTAN`:
  `IDBC262`, `SERIPUNGUTAN`, `KODEJENISPUNGUTAN`, `KODEFASILITASTARIF`, `NILAIPUNGUTAN`). The old header columns `NILAIBM` …
  `NILAITOTALPUNGUTAN` stay but are no longer read or written (their values were **not** migrated, user decision). Script
  `15_kolom_kodeasalbarang.sql` (barang `KODEASALBARANG`) must also have been run.
- The schema has **no `idPengguna` / `userPortal`**: nothing to do with the CEISA username here.

---

## 1. Payload changes (replace plan §9)

Key ORDER stays the Villiger order (minus the removed keys). Helpers: `orDefault(v, d)` = `d` when `v` is null, `""`, `0` or `0.0`
(the string `"0"` is NOT empty); `dec(v, n)` = `round(float(orDefault(v, 0)), n)`, always a float.

### 1.1 Root (34 keys — exactly the schema's `required` list)

Order: asalData, asuransi, biayaTambahan, biayaPengurang, bruto, cif, disclaimer, freight, hargaPenyerahan, jabatanTtd, kodeDokumen,
kodeKantor, **kodeTujuanPemasukan**, kodeValuta, kotaTtd, namaTtd, ndpbm, netto, nik, nilaiBarang, nomorAju, seri, tanggalAju, tanggalTtd,
barang, dokumen, entitas, jaminan, kemasan, kontainer, pengangkut, pungutan, uangMuka, vd.

| Key | New value |
|---|---|
| **removed** | `jumlahKontainer`, `tempatStuffing`, `tglAkhirBerlaku`, `tglAwalBerlaku`, `totalDanaSawit` — none is in this schema's `properties` **or** `required` (unlike BC 2.6.1, where the last four are required) |
| bruto | `dec(header.bruto, 4)` |
| cif | `dec(header.cif, 2)` (schema `multipleOf 0.01`) — was unrounded |
| hargaPenyerahan, nilaiBarang | the **same rounded cif** — was the unrounded cif |
| ndpbm | `dec(header.ndpbm, 4)` |
| netto | `dec(header.netto, 4)` — was `round2` |
| kodeValuta | **ISO** (`bc23_update.md` §1.4: RM→MYR, EURO→EUR, others as-is) |
| kodeTujuanPemasukan | string, raw (1–6) |
| asuransi, biayaTambahan, biayaPengurang, freight, uangMuka, vd | `0` (int), unchanged |
| everything else | unchanged (`asalData "S"`, `disclaimer "1"`, `kodeDokumen "262"`, `nik "0"`, `seri 1`, `tanggalAju = tanggalTtd`) |

### 1.2 `barang[]` — 28 keys (13 removed)

**Removed** (not in the official barang schema): `idBarang`, `diskon`, `freight`, `hargaSatuan`, `kodeKategoriBarang`, `kodePerhitungan`,
**`barangTarif`** (the whole BM 40 % / PPh 2.5 % / PPN 11 % block), `nilaiDiskon`, `volume`, `persenBM`, `persenPPN`, `persenPPh`,
`barangDokumen`.

```
cif                 float(row.cif or 0)                 (the page fills it: CIF Rupiah / header NDPBM, §3.4)
cifRupiah           dec(row.cifRupiah, 2)
hargaEkspor         dec(row.hargaEkspor, 2)
hargaPenyerahan     dec(row.hargaPenyerahan, 4)
isiPerKemasan       dec(row.isiPerKemasan, 2)
jumlahKemasan       dec(row.jumlahKemasan, 2)
jumlahSatuan        dec(row.jumlahSatuan, 4)
kodeAsalBahanBaku   "0"
kodeAsalBarang      string(row.kodeAsalBarang)          NO default any more (was "0"; the page offers 1-4)
kodeBarang          string
kodeDokumen         "262"
kodeJenisKemasan    string, "" when null                (was null)
kodeNegaraAsal      string
kodeSatuanBarang    string
merk                orDefault(row.merk, "-")
ndpbm               dec(row.ndpbm, 4)                   (the page stores the header NDPBM here)
netto               dec(row.netto, 4)                   (was 2 decimals)
nilaiBarang         dec(row.cifRupiah, 2)               (Villiger always sent 0)
nilaiJasa           dec(row.cifRupiah, 2)
posTarif            string
seriBarang          int
spesifikasiLain     orDefault(row.spesifikasiLain, "0")
hargaPerolehan      dec(row.hargaPerolehan, 4)
tipe                orDefault(row.tipe, "0")
uangMuka            0
ukuran              orDefault(row.ukuran, "0")
uraian              string
bahanBaku           [] rows whose seriBarang = this seriBarang
```

### 1.3 `barang[].bahanBaku[]` — 28 keys, already exactly the schema's set

Order (unchanged): cif, cifRupiah, hargaPenyerahan, hargaPerolehan, jumlahSatuan, kodeSatuanBarang, kodeAsalBahanBaku, kodeBarang, kodeDokAsal,
kodeDokumen `"262"`, kodeKantor, merkBarang, ndpbm, netto, **nomorAjuDokAsal**, nomorDaftarDokAsal, posTarif, seriBahanBaku, seriBarang,
seriBarangDokAsal, seriIjin, spesifikasiLainBarang, tanggalDaftarDokAsal, tipeBarang, ukuranBarang, uraianBarang, nilaiJasa, bahanBakuTarif.

Changes:

- **`nomorAjuDokAsal` = the Nomor AJU of the source BC 2.6.1** (`DOCUMENTBC261.NOMORAJU` of `IDBC261`), same value for every bahan baku.
  It is no longer typed (the modal input was removed; the table column `NOMORAJUDOKASAL` stays, nullable, no longer read or written).
  A **standalone** BC 2.6.2 (no source) sends `""`, which is reported at send (§2). The BC 2.6.1 page no longer collects this field either.
- **`bahanBakuTarif = []`** (the key is required, the schema accepts an empty list). The old hard-coded PPN row is gone, same as BC 2.5 / 2.6.1.
- `seriBarangDokAsal` = typed value (`orDefault(stored, 1)`, int); `seriIjin` `orDefault(stored, 1)`.
- `cif`, `hargaPenyerahan`, `hargaPerolehan`, `ndpbm`, `netto`, `nilaiJasa` `dec(…, 4)` (`nilaiJasa` and `netto` were not rounded to the schema before);
  `jumlahSatuan` `dec(…, 4)`; `cifRupiah` `dec(…, 2)`.

### 1.4 `entitas[]` — three FIXED slots, official keys

Fixed since the original build: `selectEntitas1` → seri 3 Pengusaha TPB, `selectEntitas2` → seri 7 Pemilik Barang, `selectEntitas3` → seri **9** Pengirim
Barang; an empty slot is emitted blank and does not shift the others. Data = what the form holds (nama / alamat / NPWP / izin 1) plus the master
copy saved with the document.

- seri 3 (11 keys, unchanged): alamatEntitas, kodeEntitas `"3"`, kodeJenisApi (default `"02"`), kodeJenisIdentitas (`"6"`), namaEntitas, kodeStatus (`"0"`),
  nibEntitas, nomorIdentitas, nomorIjinEntitas, tanggalIjinEntitas (`YYYY-MM-DD` or `""`), seriEntitas `3`.
- seri 7 (7 keys): alamatEntitas, kodeEntitas `"7"`, nomorIdentitas, kodeStatus, namaEntitas, kodeJenisIdentitas, seriEntitas `7` (**`kodeNegara` removed**).
- seri 9 (7 keys): alamatEntitas, kodeEntitas `"9"`, kodeJenisIdentitas, kodeStatus, namaEntitas, nomorIdentitas, seriEntitas `9`
  (**`kodeJenisApi`, `nomorIjinEntitas`, `tanggalIjinEntitas` removed**).

### 1.5 `dokumen[]`, `jaminan[]`, `kemasan[]`, `kontainer[]`, `pengangkut[]`

- `dokumen`: `idDokumen "0", kodeDokumen, nomorDokumen, seriDokumen, tanggalDokumen`; empty nomor / tanggal are `""` (not null). (`kodeFasilitas` is optional in the schema and not sent.)
- `jaminan`: order unchanged, `idJaminan` = `kodeJenisJaminan`, every string `""` when empty (was null), `nilaiJaminan` int (null only when never typed → "wajib diisi").
- `kemasan`: `jumlahKemasan` int, `kodeJenisKemasan`, `seriKemasan` int, `merkKemasan` — 4 keys, unchanged.
- `kontainer`: exactly `kodeJenisKontainer, kodeTipeKontainer, kodeUkuranKontainer, nomorKontainer, seriKontainer` (strings `""` when empty, int seri).
  **`merkKontainer` removed**; `jumlahKontainer` is no longer sent at all.
- `pengangkut`: exactly 3 keys `idPengangkut "1"`, `kodeCaraAngkut`, `seriPengangkut 1` (**`kodeBendera`, `namaPengangkut`, `nomorPengangkut` removed**).

### 1.6 `pungutan[]` — one object per row of the new Pungutan list

```
{ "idPungutan": "<row number 1..n as string>", "kodeFasilitasTarif": "<row>", "kodeJenisPungutan": "<row>", "nilaiPungutan": dec(row.nilai, 2) }
```

Rows come from the Pungutan tab (§3.1). **No rows → `pungutan: []`** (not required). The old behaviour (11 fixed inputs sent per type, otherwise one PPN row =
first barang's CIF Rupiah) is gone.

---

## 2. Send-time validation options for BC 2.6.2

Validator, 422 contract and preview contract = `bc23_update.md` §2 (same code, `bc262.json`). Labels as `bc261_update.md` §2.

`bagian`: `barang`→`Barang`, `bahanBaku`→`Bahan Baku`, `dokumen`→`Dokumen`, `entitas`→[`Pengusaha TPB`,`Pemilik Barang`,`Pengirim Barang`],
`jaminan`→`Jaminan`, `kemasan`→`Kemasan`, `kontainer`→`Peti Kemas`, `pengangkut`→`Pengangkut`, `pungutan`→`Pungutan`.
`sisa`: `barang`, `dokumen`, `kemasan`, `kontainer`, `jaminan`, `bahanBaku`, **`pungutan`** (its schema is a single object, so every row after the
first needs it).

`wajibIsi` (flat paths that may not be `""`, chosen with the user):

```
kodeKantor, kodeTujuanPemasukan, kodeValuta, kotaTtd, namaTtd, jabatanTtd,
barang.kodeBarang, barang.uraian, barang.posTarif, barang.kodeSatuanBarang, barang.kodeJenisKemasan,
barang.kodeAsalBarang, barang.kodeNegaraAsal,
barang.bahanBaku.kodeBarang, barang.bahanBaku.posTarif, barang.bahanBaku.uraianBarang, barang.bahanBaku.kodeSatuanBarang,
barang.bahanBaku.kodeDokAsal, barang.bahanBaku.kodeKantor, barang.bahanBaku.nomorDaftarDokAsal,
entitas.namaEntitas, entitas.alamatEntitas, entitas.nomorIdentitas, entitas.nibEntitas, entitas.nomorIjinEntitas,
kemasan.kodeJenisKemasan, kontainer.nomorKontainer,
dokumen.kodeDokumen, dokumen.nomorDokumen,
jaminan.kodeJenisJaminan, jaminan.nomorJaminan, jaminan.nomorBpj, jaminan.penjamin,
pungutan.kodeJenisPungutan, pungutan.kodeFasilitasTarif
```

Extra BC 2.6.2 rules appended to the validator's list:
1. `barang` empty → `Barang: minimal 1 barang.`
2. `kemasan` empty → `Kemasan: wajib diisi (Jenis Kemasan dan Jumlah Kemasan).`; a row with `jumlahKemasan <= 0` →
   `Kemasan #<n> > Jumlah Kemasan (jumlahKemasan): harus lebih dari 0`
3. each pungutan row with `nilaiPungutan <= 0` → `Pungutan #<n> > Nilai Pungutan (nilaiPungutan): harus lebih dari 0`
4. translated `kodeValuta` not `^[A-Z]{3}$` (and not empty) → the message of `bc23_update.md` §1.4.
5. any bahan baku with `nomorAjuDokAsal == ""` → **one** message (not one per row):
   `Bahan Baku: Nomor AJU Dokumen Asal (nomorAjuDokAsal) kosong - diambil dari BC 2.6.1 sumber, sedangkan dokumen ini dibuat tanpa BC 2.6.1 sumber.`

Things the schema already catches: `nomorAju` 26 letters/digits; real dates; `kodeCaraAngkut` ∈ 1..9; `kodeNegaraAsal` `^[A-Z]{2}$`; entity
`kodeJenisIdentitas` ∈ 2..6; kontainer enums; decimals (`cif`, `nilaiBarang`, `nilaiPungutan` 2 dp; `bruto`, `netto`, `ndpbm` 4 dp).

---

## 3. UI changes (BC 2.6.2 page)

1. **Pungutan tab → a list.** Table (No, Jenis Pungutan, Fasilitas Tarif, Nilai Pungutan, Action) + "Tambah Pungutan" + modal with:
   - **Jenis Pungutan** select, hard-coded (user's list from the CEISA reference): BK Bea Keluar, BM Bea Masuk, BMAD Bea Masuk Anti Dumping, BMI Bea Masuk Imbalan,
     BMKITE Bea Masuk KITE, BMP Bea Masuk Pembalasan, BMTP Bea Masuk Tindakan Pengamanan, CEA Cukai Etil Alkohol, CMEA Cukai Minuman Mengandung Etil Alkohol,
     CTEM Cukai Tembakau, DENDA Denda Administrasi Pabean, DS Dana Sawit, PNBP Penerimaan Negara Bukan Pajak, PPH PPh Impor, PPHEKSPOR PPh Ekspor, PPN PPN Impor,
     PPNBM PPnBM Impor, PPNLOKAL PPN Dalam Negeri / Hasil Tembakau (shown as `BM - Bea Masuk`);
   - **Fasilitas Tarif** select, hard-coded: 1 DIBAYAR, 2 DITANGGUNG PEMERINTAH, 3 DITANGGUHKAN, 4 BERKALA, 5 DIBEBASKAN, 6 TIDAK DIPUNGUT, 7 SUDAH DILUNASI,
     8 DIJAMINKAN, 9 DITUNDA (shown as `1 - DIBAYAR`);
   - **Nilai Pungutan** decimal. **Pre-filled with the header CIF Rupiah** (`NDPBM × CIF`, 2 decimals), editable.
   - Modal save rules (client side): both selects chosen; **Nilai ≤ 0 is refused** with *"Nilai Pungutan tidak boleh 0. Isi CIF dan NDPBM dulu (tab Transaksi)."*;
     a row with the **same Jenis + Fasilitas** as another is refused (*"Pungutan BM dengan Fasilitas Tarif 1 sudah ada. Satu kombinasi hanya boleh satu baris."*).
     The server repeats the duplicate check on Add / Edit (HTTP 422, same message); it does **not** check the value (the send check does, §2 rule 3).
   - **Recalculation:** whenever the header **CIF or NDPBM** (Transaksi tab) is typed, **every** pungutan row's Nilai is set to the new CIF Rupiah (overwriting
     earlier edits; the user may edit again afterwards). Opening a saved document keeps the stored values. A header that becomes 0 therefore leaves rows at 0, reported at send.
   - The 12 fixed inputs and the Total input are gone; a read-only "N baris, total X" shows above the table (information only, never sent).
   - Pre-fill from an Outstanding BC 2.6.1 leaves Pungutan empty (BC 2.6.1 has no such rows).
2. **Send dialog and Preview** exactly as `bc23_update.md` §2.1 (422 + blocking dialog; preview endpoint returns `{json, masalah}`; green / red box `#previewMasalah`).
3. **Entitas tab:** NPWP, Alamat, Nomor Izin and Tanggal Izin are **read-only** (filled from the entity master when the entity is picked). Info text on the tab.
4. **Barang CIF** (same as BC 2.6.1 §3.5): the disabled "CIF" shows `Nilai CIF / header NDPBM`, live in the modal; on Save Dokumen every barang gets
   `cif = Nilai CIF / header NDPBM` and `ndpbm = header NDPBM`. Info text under the barang modal.
5. **Bahan baku modal:** the "Nomor AJU Dok Asal" input is removed (§1.3), with an info line saying it comes from the BC 2.6.1 source. The pre-fill from BC 2.6.1 no
   longer copies it (nor barang `persenBM/PPN/PPh`, `keteranganDokumen`).
6. Info texts: Dokumen (optional but complete rows), Kemasan (*"Kemasan wajib ada saat dikirim … Peti kemas ikut dikirim bila diisi."*), Transaksi (ISO currency),
   Pungutan tab.
7. **Cara Angkut** was already limited to 1–9; the Pengangkut tab has nothing else (no input needed removing).

---

## 4. Test checklist

- Builder: root key list/order = §1.1 (34 keys; no `jumlahKontainer` / `tempatStuffing`); barang 28 keys; bahan baku 28 keys; entitas 11 / 7 / 7 keys with codes `3 / 7 / 9`;
  `cif 1500.456 → 1500.46` (and `hargaPenyerahan`, `nilaiBarang`), `netto 100.4567` stays, `bruto 120.12345 → 120.1235`, `ndpbm 16000.12345 → 16000.1235`; barang `cifRupiah 24000000.125 → 24000000.13`,
  `netto 100.45678 → 100.4568`, `jumlahKemasan 1.005 → 1.01`; `kodeAsalBarang null → ""`, `kodeJenisKemasan null → ""`; entitas `[null, B, C]` keeps B on seri 7 and C on seri 9;
  pungutan rows → objects with `idPungutan "1","2"` and `2500.505 → 2500.51`, no rows → `[]`; bahan baku `nomorAjuDokAsal` = the source nomor AJU (`""` without source), `bahanBakuTarif = []`
  and the JSON contains `"bahanBakuTarif":[]`, `netto 1.23456 → 1.2346`, `seriBarangDokAsal 3 → 3`, null → 1; pengangkut exactly 3 keys; kontainer exactly 5 keys.
- A fully filled document (kontainer, jaminan, dokumen, bahan baku, 2 pungutan rows) → **zero** problems; also with long decimals; also with no pungutan rows.
- Incomplete document: `nomorAju "TEST262"`; blank kantor / tujuan / asal barang / negara asal / jenis kemasan / nomor peti kemas / cara angkut; dokumen with null nomor and tanggal;
  jaminan with only a kode; no kemasan; a pungutan row with value 0 (and a second row with blank fasilitas — checks the `sisa` handling); a standalone document with bahan baku → the single
  `nomorAjuDokAsal` message.
- Independent cross-check used in Laravel: Python `jsonschema` (draft-07, tuple items, Decimal `multipleOf`, digit-based `maxlength`) on a fully filled payload → 0 errors and no key sent that
  the schema does not list (the only schema key never sent is the optional `dokumen.kodeFasilitas`).
- End-to-end in Laravel through the controller on session temp tables (scripts 09 + 13 + 19 + the barang `KODEASALBARANG` column): a complete document gives 0 problems.

---

## 5. Not changed / still open (do not "fix" these now)

- Barang `cif` / `ndpbm` are computed in the page, not in the builder: the builder reads the stored row values.
- No. Reg BC / Tanggal BC / Tanggal Pemasukan stay as internal notes (never sent).
- `nilaiJasa` = `nilaiBarang` = CIF Rupiah of the barang, and the root `hargaPenyerahan` / `nilaiBarang` = the foreign header CIF: Villiger values, kept.
- `kodeAsalBahanBaku` on barang stays `"0"`; the pending per-BC filter on the KATEGORIBARANG master is unaffected (Kategori Barang is no longer sent, not in this schema).
- The entity master must carry valid data (`KODEJENISAPI` etc.); the Pengusaha needs NIB, izin number and date.
- Old BC 2.6.2 documents: their header pungutan values are ignored (not migrated) and the Pungutan list starts empty; old typed `NOMORAJUDOKASAL` values are ignored (the source's nomor AJU is sent).
