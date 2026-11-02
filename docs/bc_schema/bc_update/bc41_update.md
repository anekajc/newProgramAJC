# BC 4.1 — update to the OFFICIAL CEISA payload schema

Hand-over spec for re-applying, in the **Villiger Flask app**, the changes already made in the Laravel app
(`D:\newProgramAJC`). It is a **diff against the Villiger behaviour documented in `docs/documentbc41_plan.md`**
(§9 payload, §11 quirks): read that plan first, then apply every section here. Like BC 2.5, the official BC 4.1 schema
differs **a lot** from what Villiger sends: a fully filled Villiger document failed with **8 errors** (missing `userPortal`,
`volume`, barang `volume`, three Pemilik entity fields, wrong pengangkut types) and the payload carried many keys the
schema does not define. So this is a rewrite of the payload plus a few new inputs.

- Official schema: `docs/bc_schema/bc41.json`. Copy it into the Flask repo and read it at runtime.
- The send-time validator, the 422 / dialog / preview contract and the message catalogue are the **same as BC 2.3** —
  implement them once, following **`bc23_update.md` §2** (BC 2.5 and BC 4.0 use it too), then use the BC 4.1 options in §4.
  One small addition to the validator (option `angka`) is in §4.
- Laravel reference implementation: `app/Services/BeaCukai/Bc41PayloadBuilder.php`,
  `app/Http/Controllers/DocumentBc/DocumentBc41Controller.php` (`WAJIB_ISI`, `masalahKirim`, `jumlahVolume`, `salinanEntitas`,
  `susunPayload`), `app/Services/BeaCukai/CeisaService.php` (`login`, `username`), `app/Services/BeaCukai/CeisaSchemaValidator.php`,
  `resources/views/documentbc/documentbc41.blade.php`, `docs/update-db-DBSMLNEW/beacukai/18_kolom_bc41_skema_resmi_dan_username_ceisa.sql`,
  tests `tests/Unit/Bc41PayloadBuilderTest.php`.
- Messages are Indonesian, exactly as written.
- Rule chosen with the user: **saving is never blocked by the schema check; every problem is listed when the user tries
  to SEND and in the Preview JSON dialog.**

---

## 1. New payload (replaces plan §9)

Keys are emitted in exactly this order. Helpers: `orDefault(v, d)` = `d` when `v` is null, `""`, `0` or `0.0` (the string
`"0"` is NOT empty); `dec(v, n)` = `round(float(orDefault(v, 0)), n)` always a float; `num(v, d)` = `d` (int) when empty else `float(v)`.

### 1.1 Root (41 keys)

| # | Key | Value |
|---|---|---|
| 1 | asalData | `"S"` |
| 2 | asuransi | `0.0` |
| 3 | bruto | `dec(header.bruto, 4)` |
| 4 | cif | `dec(header.cif, 2)` |
| 5 | dasarPengenaanPajak | `dec(header.dasarPengenaanPajak, 2)` |
| 6 | kodeJenisTpb | orDefault(header.kodeJenisTpb, `"1"`) |
| 7 | freight | `0.0` |
| 8 | hargaPenyerahan | `dec(header.hargaPenyerahan, 4)` |
| 9 | jabatanTtd | string, `""` when null |
| 10 | jumlahKontainer | **count of kontainer rows** |
| 11 | kodeDokumen | `"41"` |
| 12 | kodeKantor | string |
| 13 | kodeLokasiBayar | config `kode_lokasi_bayar` (default `"1"`, an unverified guess) |
| 14 | kodePembayar | orDefault(header.kodePembayar, `""`) — the entity seri `"3"` / `"7"` / `"8"` chosen as Wajib Bayar |
| 15 | kodeTujuanPengiriman | string |
| 16 | kotaTtd | string |
| 17 | namaTtd | string |
| 18 | ndpbm | `dec(header.ndpbm, 4)` |
| 19 | netto | `dec(header.netto, 4)` (was rounded to 2 decimals) |
| 20 | nik | `"0"` |
| 21 | nilaiBarang | `round(dec(header.hargaPenyerahan, 4), 2)` |
| 22 | nomorAju | string |
| 23 | seri | `1` |
| 24 | tanggalAju | header.tanggalTtd as `YYYY-MM-DD`, `""` when empty (unchanged: tanggalAju = tanggalTtd) |
| 25 | tanggalTtd | `YYYY-MM-DD`, `""` when empty |
| 26 | **userPortal** | **CEISA login username** of the sending user, `""` when none (§1.5) — **new, required** |
| 27 | **volume** | `dec(header.volume, 4)` = sum of the barang volumes — **new, required** |
| 28 | biayaTambahan | `0.0` |
| 29 | biayaPengurang | `dec(header.diskon, 4)` |
| 30 | vd | `0.0` |
| 31 | uangMuka | `dec(header.uangMuka, 4)` |
| 32 | ppnPajak | `dec(header.ppnPajak, 2)` |
| 33 | ppnbmPajak | `dec(header.ppnbmPajak, 2)` |
| 34 | tarifPpnPajak | `dec(header.tarifPpnPajak, 2)` |
| 35 | tarifPpnbmPajak | `dec(header.tarifPpnbmPajak, 2)` |
| 36-41 | entitas, dokumen, pengangkut, kontainer, kemasan, barang | arrays, see below |

**Removed from the root** (not in the official schema): `disclaimer`, `kodeValuta`, `jaminan`, **`pungutan`** (the whole
array, built from PPN/PPnBM before), `nomorBuktiBayar`, `tanggalBuktiBayar`. The Valuta / NDPBM / CIF inputs stay on the page
(they compute CIF Rupiah, DPP, PPN); only `ndpbm` and `cif` are sent. Nothing else about the Transaksi calculation changes.

### 1.2 `barang[]` (27 keys; was ~39)

In this order. Values (everything not listed = as in plan §9.3):

```
cif, hargaEkspor, hargaPenyerahan, isiPerKemasan, jumlahKemasan, jumlahSatuan, kodeBarang, kodeDokumen, kodeJenisKemasan,
kodeSatuanBarang, merk, netto, nilaiBarang, posTarif, seriBarang, spesifikasiLain, tipe, ukuran, uraian, volume, cifRupiah,
hargaPerolehan, kodeAsalBahanBaku, ndpbm, uangMuka, nilaiJasa, bahanBaku
```

| Key | Value |
|---|---|
| cif | `float(row.cif)` |
| hargaEkspor, hargaPenyerahan, hargaPerolehan, ndpbm | `num(row.x, 0)` |
| isiPerKemasan | `dec(row.isiPerKemasan, 2)` (official spelling, capital K) |
| jumlahKemasan | `dec(row.jumlahKemasan, 2)` |
| jumlahSatuan | `dec(row.jumlahSatuan, 4)` |
| kodeDokumen | `"41"` |
| kodeJenisKemasan, kodeSatuanBarang, kodeBarang, posTarif, uraian | string, `""` when null |
| merk | orDefault(row.merk, `"-"`); `tipe`, `ukuran`, `spesifikasiLain` → orDefault(…, `"0"`) (unchanged Villiger defaults) |
| netto | `dec(row.netto, 4)` (was 2) |
| nilaiBarang | `dec(row.nilaiBarang, 2)` (the row has no such input, so `0.0`; unchanged in meaning) |
| volume | **`dec(row.volume, 4)` — new, required** |
| cifRupiah | `round(float(row.cifRupiah), 2)` |
| kodeAsalBahanBaku | `"0"` |
| uangMuka | `0` |
| nilaiJasa | `round(float(row.cifRupiah), 2)` (unchanged oddity of the old payload) |
| bahanBaku | the bahan baku rows of this barang (seriBarang match) |

**Removed from barang** (not in the schema): `idBarang`, `diskon`, `freight`, `hargaSatuan`, `kodeAsalBarang`,
`kodeKategoriBarang`, `kodeNegaraAsal`, `kodePerhitungan`, `nilaiDiskon`, **`barangTarif`** (the computed BM / PPN / PPH rows),
`barangDokumen`.

### 1.3 `barang[].bahanBaku[]` (27 keys, same order as before)

Key order unchanged. Value changes (all rounding follows the schema's `multipleOf`):

| Key | Value |
|---|---|
| cif, cifRupiah, hargaPenyerahan, hargaPerolehan | `dec(x, 2)` |
| jumlahSatuan | `dec(x, 4)` |
| netto | `dec(x, 4)` (was 2) |
| nilaiJasa | **`dec(x, 2)` — a number with up to 2 decimals, NOT an integer** (see the note in §4, option `angka`) |

Everything else (`kodeAsalBahanBaku "0"`, `kodeDokumen "41"`, `kodeDokAsal` = row.kodeDokumen, `kodeKantor` = row.kodeKantorAsal,
`seriBarangDokAsal` fallback 1, `seriIjin 1`, dates `""` when empty, …) is unchanged.

### 1.4 Other arrays

- **`entitas[]`** — three FIXED slots, all mandatory: `[0]` Pengusaha TPB seri 3, `[1]` Pemilik Barang seri 7, `[2]` Penerima Barang
  seri 8 (same mapping as before). An empty slot is still sent with empty values and the defaults (`kodeJenisApi "02"`,
  `kodeJenisIdentitas "6"`, `kodeStatus "0"`). Exact keys per slot (the old extras are gone):

  | Slot | Keys in order |
  |---|---|
  | 3 | alamatEntitas, kodeEntitas `"3"`, kodeJenisIdentitas, namaEntitas, nibEntitas, nomorIdentitas, nomorIjinEntitas, seriEntitas `3`, tanggalIjinEntitas |
  | 7 | alamatEntitas, kodeEntitas `"7"`, **kodeJenisApi**, kodeJenisIdentitas, kodeStatus, namaEntitas, nomorIdentitas, **nomorIjinEntitas**, seriEntitas `7`, **tanggalIjinEntitas** |
  | 8 | alamatEntitas, kodeEntitas `"8"`, kodeJenisApi, kodeJenisIdentitas, kodeStatus, namaEntitas, nomorIdentitas, seriEntitas `8` |

  Removed: slot 3 `kodeJenisApi` / `kodeStatus`, slot 7 `kodeNegara`, slot 8 `nomorIjinEntitas` / `tanggalIjinEntitas`. The Pemilik (7)
  izin and date are a **snapshot copied from the entity master when the document is saved** (§2), like the Penerima's izin was before.
- **`dokumen[]`** — keys unchanged (`idDokumen "0"`, kodeDokumen, nomorDokumen, seriDokumen, tanggalDokumen). Empty `nomorDokumen` /
  `tanggalDokumen` are sent as `""` (were `null`) so each is reported once by the must-fill list.
- **`pengangkut[]`** — exactly one object, **new keys and types**: `idPengangkut` = the **number** `1` (was the string `"1"`),
  `namaPengangkut` = header.namaPengangkut, `nomorPengangkut` = header.nomorPengangkut, `seriPengangkut` = the **string** `"1"`
  (was the number 1). `kodeBendera` and `kodeCaraAngkut` are removed.
- **`kemasan[]`** — one object per row, key order now **jumlahKemasan, kodeJenisKemasan, merkKemasan, seriKemasan** (merk before seri);
  `jumlahKemasan` = int (0 stays 0, not null), the other two strings (`""` when empty).
- **`kontainer[]`** — unchanged (kodeJenisKontainer, kodeTipeKontainer, kodeUkuranKontainer, nomorKontainer, seriKontainer).

### 1.5 CEISA username → `userPortal` (BC 4.1) and `idPengguna` (BC 2.5)

The user chose: both fields carry the **username typed at the "Login BC" dialog** (the CEISA account), not the ERP username. The
password is never stored. This is a change to the **shared** login, so it touches every BC module in a harmless way:

1. **Data**: add a nullable column to the CEISA token table: `USERNAMECEISA varchar(100) NULL` (script 18 in Laravel).
2. **Login**: when a login succeeds and the token is stored, store the typed username in that column too (upsert by ERP user id as before).
   Make the INSERT conditional on the column existing (Laravel: `COL_LENGTH(table, 'USERNAMECEISA') IS NOT NULL`, cached) so login keeps
   working for every module until the column is created.
3. **Read**: new `username(erp_user_id)` → the stored CEISA username of the last login of that user — **regardless of token expiry** (so
   Preview works even after the token expired); `None` when no row / blank / column missing.
4. **Payload**: BC 4.1 builds the header with `userPortal = username(...) or ""`; BC 2.5 with `idPengguna = username(...) or ""`.
   BC 4.0 is **unchanged** (it keeps `idPengguna` = ERP username; plan Q-c).
5. **Send-time rule** (outside the schema, because `""` passes a string type): empty → one problem, exactly
   - BC 4.1: `User Portal CEISA (userPortal): belum ada. Klik Login BC sekali supaya username CEISA tersimpan.`
   - BC 2.5: `ID Pengguna (idPengguna): belum ada. Klik Login BC sekali supaya username CEISA tersimpan.`
   Do NOT put `userPortal` / `idPengguna` in the must-fill list (it would add a second, generic message).
6. A user who has not logged in again after the column exists sees that problem until they do.

---

## 2. Data model changes (add equivalents in the Villiger DB)

| Table | Column | Notes |
|---|---|---|
| token table | `USERNAMECEISA varchar(100) NULL` | §1.5 |
| BC 4.1 header | `VOLUME decimal(19,4) NOT NULL DEFAULT 0` | **server-computed** = sum of barang volumes (4 decimals) on every save; the page shows it read-only |
| BC 4.1 header | `NAMAPENGANGKUT varchar(100) NOT NULL DEFAULT ''`, `NOMORPENGANGKUT varchar(50) NOT NULL DEFAULT ''` | new Pengangkut inputs |
| BC 4.1 header | `NOMORIJINENTITAS2 varchar(50) NULL`, `TANGGALIJINENTITAS2 date NULL` | snapshot of the Pemilik Barang's izin from the entity master, filled on save when entity 2 is chosen (null when not) |
| BC 4.1 barang | `VOLUME decimal(19,4) NOT NULL DEFAULT 0` | typed per barang |

Columns no longer used by the app (left in the table): header `NOMORBUKTIBAYAR`, `TANGGALBUKTIBAYAR`, `KODECARAANGKUT`, snapshot
`KODENEGARAENTITAS1..3`; barang `KODENEGARAASAL`, `PERSENBM`, `PERSENPPN`, `PERSENPPH`, `KETERANGANDOKUMEN`, `DISKON`, `FREIGHT`,
`HARGASATUAN`, `KODEPERHITUNGAN`. The save maps no longer read or write them. Existing BC 4.1 documents keep their data;
the missing new data (volume, pengangkut, Pemilik izin) must be completed by Edit before sending — the check lists what is missing.

---

## 3. UI changes (BC 4.1 page)

1. **Pengangkut tab**: replace the "Cara Angkut" select with two text inputs: **Nama Sarana Pengangkut** (max 100) and **Nomor Sarana
   Pengangkut** (max 50). Note under them: "Nama dan nomor sarana pengangkut wajib diisi saat dikirim ke CEISA."
2. **Transaksi tab → Berat**: add **Total Volume (m3)** — read-only, filled automatically with the sum of the barang volumes
   (recomputed every time the barang list is rendered). Extend the info text: "… Total Volume = jumlah volume semua barang."
3. **Barang modal**: add **Volume (m3)** (decimal, after Netto). **Remove** Negara Asal and the BM / PPN / PPh inputs; the info text
   becomes "CIF = Nilai CIF / NDPBM header. Volume semua barang dijumlahkan menjadi Total Volume di tab Transaksi." New barang rows start
   with `cif 0, volume 0`. **Barang table**: new **Volume** column before "Nilai CIF" (10 columns).
4. **Pungutan tab**: remove the **Bukti Bayar** block (nomor + tanggal). Keep Cara Bayar (BANK, display only) and **Wajib Bayar**. Info text:
   "Pilihan Wajib Bayar diambil dari entitas di tab Entitas yang punya NPWP (NPWP - nama), dan diperbarui tiap entitas diganti. PPN dan PPnBM
   dikirim ke CEISA sebagai nilai di tab Transaksi (skema resmi tidak punya daftar pungutan dan bukti bayar)."
5. **HS picker**: choosing an HS now only fills Pos Tarif (it no longer fills BM / PPN / PPh in the barang modal, and typing a pos tarif no
   longer resets them). The picker table may still show the master rates as a reference.
6. **Info texts** (Indonesian): Entitas — "Ketiga entitas wajib dipilih saat dikirim ke CEISA. Pengusaha TPB juga butuh NIB, Nomor Izin dan
   Tanggal Izin; izin Pemilik Barang disalin dari master Entitas saat Simpan, jadi lengkapi izinnya di master." · Dokumen — "Dokumen tidak wajib
   ada, tetapi baris yang ada harus lengkap (jenis, nomor, tanggal) saat dikirim." · Kemasan — "Kemasan wajib ada saat dikirim ke CEISA (Jenis
   Kemasan terisi dan Jumlah lebih dari 0). Peti kemas ikut dikirim bila diisi."
7. **Reset (Tambah)** no longer sets Cara Angkut. Nomor AJU input already has `maxlength` 26.
8. **Send**: a 422 `{message, details[]}` opens the blocking dialog (one `<li>` per problem); other errors keep the toast. **Preview**: the
   endpoint returns `{json, masalah}`; green box "Lolos pemeriksaan skema CEISA, siap dikirim." or red box "Belum bisa dikirim: N masalah
   ditemukan." with the list, above the JSON (`#previewMasalah`). Exactly as `bc23_update.md` §2.1 / §3.

Server side on save (add and edit): normalise as before, then **`header.volume = round(sum(barang.volume), 4)`** (do not trust the form value);
also copy the entity master snapshot including the izin of entity 2 and 3 (`NOMORIJINENTITAS{n}`, `TANGGALIJINENTITAS{n}`).
The save-time checks are unchanged (Nomor AJU required, Berat Kotor > 0, Wajib Bayar chosen from an entity that has an NPWP).

---

## 4. Send-time validation options for BC 4.1

Validator = `bc23_update.md` §2.2 (same code, `bc41.json`) **plus one new option**:

**`angka`** (list of flat paths, like `wajibIsi`): paths whose schema type is `integer` but that the user decided may be decimal.
In the scalar check, when `schema.type == "integer"` and the flat path is in `angka`, treat the type as `number` (so 10.55 is OK, `multipleOf`
still applies). For BC 4.1: `["barang.bahanBaku.nilaiJasa"]` — the schema says integer (while also saying `multipleOf 0.0001`, which is
contradictory); the user chose to keep a numeric with up to 2 decimals. If CEISA rejects it, drop the option and round to an integer.
Default `[]`. An independent Python `jsonschema` run will report that one field as "not an integer" when it has decimals — an intended difference.

**`bagian`**: `barang` "Barang", `bahanBaku` "Bahan Baku", `dokumen` "Dokumen", `kemasan` "Kemasan", `kontainer` "Peti Kemas",
`entitas` ["Pengusaha TPB", "Pemilik Barang", "Penerima Barang"], `pengangkut` "Pengangkut".
**`sisa`**: `["barang", "dokumen", "kemasan", "kontainer", "bahanBaku"]`.
**`angka`**: `["barang.bahanBaku.nilaiJasa"]`.

**`wajibIsi`**:

```
kodeKantor, kodeJenisTpb, kodeTujuanPengiriman, kodeLokasiBayar, kodePembayar, kotaTtd, namaTtd, jabatanTtd,
pengangkut.namaPengangkut, pengangkut.nomorPengangkut, kemasan.kodeJenisKemasan,
barang.kodeBarang, barang.uraian, barang.posTarif, barang.kodeSatuanBarang, barang.kodeJenisKemasan,
barang.bahanBaku.kodeBarang, barang.bahanBaku.posTarif, barang.bahanBaku.uraianBarang, barang.bahanBaku.kodeSatuanBarang,
barang.bahanBaku.kodeDokAsal, barang.bahanBaku.kodeKantor, barang.bahanBaku.nomorDaftarDokAsal, barang.bahanBaku.nomorAjuDokAsal,
entitas.namaEntitas, entitas.alamatEntitas, entitas.nomorIdentitas, entitas.nibEntitas, entitas.nomorIjinEntitas,
dokumen.kodeDokumen, dokumen.nomorDokumen, dokumen.tanggalDokumen
```

May stay empty: barang `merk` / `tipe` / `ukuran` / `spesifikasiLain` (defaults are sent), `merkKemasan`. `nibEntitas` and `nomorIjinEntitas` only exist in the
slots where the schema defines them (NIB: Pengusaha; izin: Pengusaha and Pemilik), so the same flat path only checks those. `tanggalIjinEntitas` is
covered by the date-format rule. `dokumen.tanggalDokumen` has no date format in this schema, hence it is in the list. Bahan baku are **not required**
per barang; if a row exists it must be complete.

**Extra rules** (outside the schema), appended to the validator's list:

1. No barang → `Barang: minimal 1 barang.`
2. No kemasan → `Kemasan: wajib diisi (Jenis Kemasan dan Jumlah Kemasan).`; each kemasan row with `jumlahKemasan <= 0` →
   `Kemasan #N > Jumlah Kemasan (jumlahKemasan): harus lebih dari 0`
3. Empty `userPortal` → the message in §1.5.
4. Dokumen has **no minimum**.

Extra labels for the label map: `userPortal` User Portal CEISA · `kodePembayar` Wajib Bayar · `idPengangkut` ID Pengangkut · `idDokumen` ID Dokumen ·
`asalData` Asal Data · `seri` Seri Dokumen · `uangMuka` Uang Muka · `nilaiJasa` Nilai Jasa · `vd` VD · `jumlahRealisasi` Jumlah Realisasi ·
`nilaiPungutan` Nilai Pungutan. **Also change `nomorPengangkut` from "No. Voy/Flight" to "Nomor Sarana Pengangkut"** (it was wrong for a truck plate;
BC 2.3's doc `bc23_update.md` was updated the same way).

---

## 5. Test checklist

Builder (`Bc41PayloadBuilderTest`):
- Root keys = the 41 in §1.1; barang keys = the 27 in §1.2; bahan baku keys = the 27 in §1.3; none of the removed root / barang keys present
  (`disclaimer`, `kodeValuta`, `jaminan`, `pungutan`, `nomorBuktiBayar`, `tanggalBuktiBayar`, `idPengguna`; `idBarang`, `diskon`, `freight`, `hargaSatuan`,
  `kodeAsalBarang`, `kodeKategoriBarang`, `kodeNegaraAsal`, `kodePerhitungan`, `nilaiDiskon`, `barangTarif`, `barangDokumen`).
- Root values: `userPortal` from the header, `volume 2.5`, `nilaiBarang` = harga penyerahan, `tanggalAju` = `tanggalTtd`, `kodePembayar "3"`, `jumlahKontainer 0`.
- Pengangkut = `[{idPengangkut: 1, namaPengangkut: "TRUK", nomorPengangkut: "N 1", seriPengangkut: "1"}]`.
- Entitas: key lists per slot as §1.4; kode `["3","7","8"]`; with entities `[None, None, x]` slots 1 and 2 stay blank (not shifted, `kodeJenisApi "02"`).
- Rounding: `cif 1234.5678 → 1234.57`, `hargaPenyerahan 100.1234 → 100.1234` and `nilaiBarang 100.12`, `netto 100.12345 → 100.1235`, `bruto 120.12345 → 120.1235`,
  `ppnPajak 11.005 → 11.01`; barang `netto 50.12345 → 50.1235`, `volume 1.23456 → 1.2346`, `jumlahKemasan 1.236 → 1.24`; bahan baku `cif 1.239 → 1.24`,
  `hargaPenyerahan 2.345 → 2.35`, `netto 0.12345 → 0.1235`, `nilaiJasa 10.555 → 10.56`.
- Kemasan key order jumlah, jenis, merk, seri; dokumen keys with `idDokumen`.

Schema (validator + `bc41.json` + the options of §4):
- A complete document → `[]`. A barang without bahan baku → `[]`.
- Bahan baku `nilaiJasa 10.5` → `[]` with option `angka`; without the option → `Bahan Baku #1 > Nilai Jasa (nilaiJasa): harus berupa bilangan bulat`.
- Pemilik without izin → `Pemilik Barang > Nomor Izin (nomorIjinEntitas): wajib diisi` and `… > Tanggal Izin (tanggalIjinEntitas): wajib diisi`; empty pengangkut →
  `Pengangkut #1 > Nama Sarana Pengangkut (namaPengangkut): wajib diisi` and `… > Nomor Sarana Pengangkut (nomorPengangkut): wajib diisi`.
- `nomorAju` `000041-AJU-1` → `Nomor AJU (nomorAju): harus tepat 26 …`; null `tanggalTtd` → `Tanggal Aju (tanggalAju): wajib diisi`; empty `kodePembayar` →
  `Wajib Bayar (kodePembayar): wajib diisi`; dokumen row without nomor/tanggal → one problem each; kemasan row with empty jenis → `Kemasan #1 > Jenis Kemasan (kodeJenisKemasan): wajib diisi`.

CEISA username (end-to-end in Laravel, with temporary tables): the token table without the new column still saves a token and `username()` returns null; with the
column the username is stored, replaced by the next login, and null for another user; a document with a stored username has no problems, without it exactly the
one "Klik Login BC" problem. Server volume: barang volumes 1.5 + 2.25 → header volume 3.75.

Independent cross-check used in Laravel: Python `jsonschema` (draft-07, tuple items, Decimal-based `multipleOf`, digit-based `maxlength`) on a fully filled payload →
**0 errors** (the old payload: 8).

---

## 6. Not changed / still open (do not "fix" these now)

- `kodeLokasiBayar "1"` is a config guess (never proven against CEISA); `nik "0"`, `seri 1`, `kodeAsalBahanBaku "0"`, `kodeJenisTpb` select, `kodeTujuanPengiriman` select.
- Money meaning of some fields is as in the old payload: barang `nilaiBarang` has no input (0.0), barang `nilaiJasa` = barang `cifRupiah`, barang `uangMuka` 0, root `cif` = the typed
  foreign-currency CIF while the schema describes it as "Nilai Pabean". The user has not asked to change these.
- No `barangTarif` / `pungutan` any more, hence the HS rates are informational only.
- Bahan baku per barang are optional; no document minimum for dokumen.
- Per-BC filter on the KATEGORIBARANG master: pending the user's confirmation (BC 4.1 has no category field).
- BC 4.1 has never been sent to the real CEISA gateway.
