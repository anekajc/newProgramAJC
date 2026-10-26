# Document BC 4.0 — Functional Plan for Re-implementation (Laravel 12)

Source of truth: the Villiger ERP `document_bc40` module, which uses the legacy
form-encoded add/edit path. This plan describes **behaviour, rules and the exact CEISA
payload**, not code. The database design is out of scope and will be decided in the Laravel
codebase session; everything below uses *logical* field names.

Companion plans: `documentbc23_plan.md` (closest sibling: same two-source pattern) and
`documentbc261_plan.md`. The CEISA login (§7), the `details` error builder and the Laravel
payload-fidelity notes (§10) are **identical** to those plans.

Decisions already made (same as BC 2.3):
- **Fidelity:** copy current behaviour exactly, including `sent = 1` on any HTTP 2xx. Every
  deviation is listed in §11 *Known quirks*.
- **Outstanding:** two generic source contracts, A = goods packing list and B = sparepart
  packing list.
- **Kemasan:** a single value on the header. Peti kemas / kontainer is never captured and is
  always sent as `[]`.
- **CEISA auth:** a per-user login modal, with the token stored per user (shared with every
  BC module).
- **`idPengguna`:** the new ERP's logged-in **username**, as today (flagged in §11 Q-c).
- **Scope:** backend, the CEISA payload, and UI behaviour (not visual design).

---

## 1. Overview

- BC 4.0 = customs document for **goods coming into a bonded zone (TPB) from the local
  (domestic) area**. Values are in Rupiah (**Harga Penyerahan**), not CIF.
- Lifecycle: **Draft (saved)** → **Sent** (`sent = 1`).
- **Every BC 4.0 is created from an Outstanding source.** There is no standalone create,
  because Add only shows on the Outstanding tab.
- Each document links to one source: **A** (goods packing list, `source_type = 0`) or **B**
  (sparepart packing list, `source_type = 1`).
- Child collections: **Barang** and **Dokumen**. Kemasan and Pengangkut are header fields.
  BC 4.0 has no Bahan Baku, Jaminan or Kontainer.

---

## 2. Logical data model (persisted fields)

### 2.1 Document header

| Group | Field | Type | Req. | Notes |
|---|---|---|---|---|
| Link | id, source_type (0/1), source_a_id / source_b_id, sent | | | Same as BC 2.3 |
| Header | nomorAju | string(100) | NOT NULL | **Saved UPPER-CASED** |
| | kodeDokumen | string | | Always `"40"` (set on create) |
| | kodeKantor | string(20) | NOT NULL | UI default `071100` (KPPBC TMP C Jember). **Make configurable.** |
| | kodeJenisTpb | string(20) | NOT NULL | Text input, default `"1"` (Kawasan Berikat) |
| | kodeTujuanPengirim | string(20) | | Select 1–5 (Appendix A), default 1. Sent as `kodeTujuanPengiriman`. |
| | nomorDokumenPabean | string(50) | | "F. Kolom khusus BC – Nomor Pendaftaran". **Not sent**; copied to the source. |
| | tanggalAju | date | NOT NULL | Default today. Sent, and also copied to the source's `tgl_bc`. |
| Perdagangan | hargaPenyerahan | decimal | NOT NULL | = Σ barang hargaPenyerahan (auto) |
| | nilaiJasa | decimal | NOT NULL | "19. Nilai Penggantian Jasa", typed manually |
| | uangMuka | decimal | NOT NULL | "20. Nilai Uang Muka", typed manually. Set to 0 on Source B open. |
| | asuransi, biayaTambahan, biayaPengurang | decimal, nullable | | Never set → always null |
| Weight | bruto | decimal | NOT NULL | Manual (kg, 3 dp) |
| | netto | decimal | NOT NULL | = Σ barang netto (auto, see §4.5) |
| | volume | decimal | NOT NULL | = Σ barang volume (Source B only, auto). Otherwise manual. |
| Entitas 1: Pengusaha TPB | nomorIdentitasEntitas1, namaEntitas1, alamatEntitas1, nomorIjinEntitas1, selectEntitas1 | | nama/alamat NOT NULL | |
| Entitas 3: Pengirim Barang | nomorIdentitasEntitas3, namaEntitas3, alamatEntitas3, selectEntitas3 | | nama/alamat NOT NULL | |
| Entitas 2: Pemilik Barang | nomorIdentitasEntitas2, namaEntitas2, alamatEntitas2, selectEntitas2 | | nama/alamat NOT NULL | |
| | kodeEntitas1/2/3 | string | | Stored as `'7'` on create/edit, **never used** |
| Pengangkut | namaPengangkut | string(20) | NOT NULL | "16. Jenis Sarana Pengangkut" (e.g. TRUCK) |
| | nomorPengangkut | string(50) | NOT NULL | "17. No Polisi" (licence plate) |
| | seriPengangkut | | | Never set → sent as 1 |
| Kemasan (single) | kodeJenisKemasan | string(20) | NOT NULL | Select from the packaging master |
| | jumlahKemasan | int | NOT NULL | |
| | merkKemasan | string(50) | NOT NULL | |
| | seriKemasan | | | Never set → sent as 1 |
| Pengesahan | kotaTtd, tanggalTtd, namaTtd, jabatanTtd | | tanggalTtd/namaTtd NOT NULL | tanggalTtd default today |

"NOT NULL" columns in Villiger accept empty strings from the form, but an empty numeric field
fails at the DB (§11 Q-p). Dead legacy columns (seri/kode per entitas, single dokumen
columns, realisasi penerimaan, isRealisasi = 0): **do not recreate.**

### 2.2 Child: Barang

| Field | Type | Source on save | Editable in grid? |
|---|---|---|---|
| seriBarang | int (string in Villiger) | position | no |
| posTarif | string(50) | row | yes (HS select) |
| kodeBarang | string(20) | row | yes |
| uraian | string(**50**) | row | yes |
| merk, tipe, ukuran, spesifikasiLain | string | row, else "" | **no** → always "" (§11 Q-l) |
| kodeSatuanBarang | string | row | yes (unit select) |
| jumlahSatuan | decimal | row qty | yes |
| kodeJenisKemasan | string | row | yes (packaging select) |
| jumlahKemasan | int | row | yes |
| netto | decimal | row | yes |
| volume | decimal | row | **Source B only** |
| hargaPenyerahan | decimal | row | yes |
| jumlahRealisasi | decimal | **create = row hargaPenyerahan; edit = row qty** (§11 Q-n) | no |
| cif | decimal | row cif → in practice always 0 | no |
| cifRupiah | decimal | **edit only** = row cif (null on create) | no |
| ndpbm | decimal | row → 0 | no |
| hargaPerolehan, nilaiJasa | decimal | row → 0 (no input) | no |
| persenBM, persenPPN, persenPPh | decimal | from the HS master when HS is picked | no |
| bruto, diskon, hargaSatuan, hargaEkspor, nilaiBarang, isiPerKemasan | nullable | never set | no |

### 2.3 Child: Dokumen

| Field | Type | Notes |
|---|---|---|
| seriDokumen | int | position (server) |
| kodeDokumen | string | From the document-type master (any type, see §3.1) |
| nomorDokumen | string(100) | |
| tanggalDokumen | date | UI default today |

### 2.4 CEISA token and masters

- The token is the same as in the other plans (per user, shared by every BC module).
- Masters: entity (nama, alamat, NPWP, NIB, nomorIjin, tanggalIjin, kodeJenisApi,
  kodeJenisIdentitas, kodeStatus), HS / Pos Tarif (persenBM/PPN/PPh), Jenis Satuan,
  Jenis Kemasan, Jenis Dokumen.

---

## 3. Outstanding (two source contracts)

Shared rules (same as BC 2.3, only the BC type differs):
- **Eligible** when: source `doc_date` is in the user's **working period** (month + year),
  `posted = 1`, **`bc_type = "BC 4.0"`**, and no BC 4.0 document links to it with the matching
  `source_type`.
- Server order: id desc. The UI table sorts by column 0 ascending.
- Source A row display: doc_number, container, seal, registration, shipment no.,
  arrival date, plus a red-row warning flag (`cek_pv = 0`). Source B row display:
  doc_number, invoice date, Lokal/Import.
- Read-only previews are the same as BC 2.3 §3.1 / §3.2.

### 3.1 Source A: goods packing list (`source_type = 0`)

Lines = the **aggregated goods view** (one row per aggregated article): item_code,
item_name, qty, unit, carton, weight_net, kodeJenisKemasan.

| Barang field | From |
|---|---|
| seriBarang | position |
| kodeBarang | item_code |
| uraian | item_name |
| kodeSatuanBarang | unit |
| jumlahSatuan | qty |
| jumlahKemasan | carton |
| kodeJenisKemasan | kodeJenisKemasan |
| netto | weight_net |
| hargaPenyerahan | **0** (user types the Rupiah value per row) |
| cif, ndpbm, persen* | 0 |
| posTarif, merk, tipe, ukuran, spesifikasiLain | "" |

**Default Dokumen rows (Source A):** 2 rows created on open, tanggal = today, and **rows 1–2
cannot be deleted**. Unlike BC 2.3, **both rows accept any document type** (no
Invoice/BL filter). More rows can be added and deleted.

On open: entitas 1/2/3 are cleared. "Rev. Doc." shows the source's doc_number.

### 3.2 Source B: sparepart packing list (`source_type = 1`)

| Barang field | From |
|---|---|
| seriBarang | position |
| kodeBarang | item_code |
| uraian | description |
| kodeSatuanBarang | unit |
| jumlahSatuan | qty |
| **hargaPenyerahan** | **amount** (line subtotal) |
| netto, volume, jumlahKemasan, nilaiJasa, persen* | 0 |
| kodeJenisKemasan, posTarif | "" |

**Default Dokumen rows (Source B):** none, and every row can be deleted while adding. (When a
B document is later edited, rows 1–2 become undeletable, because the edit uses the A renderer.)

On open: entitas 1/2/3 are cleared, the **kodeKantor and kodeJenisTpb inputs are hidden**
(their default values are still submitted), and header **uangMuka is set to 0**.

### 3.3 State changes on the source

| Event | Both sources |
|---|---|
| Create BC 4.0 | `ready_sent = 1`, `no_reg_bc = nomorDokumenPabean`, `tgl_bc = tanggalAju` |
| Edit BC 4.0 | `no_reg_bc = nomorDokumenPabean`, `tgl_bc = tanggalAju` |
| Delete BC 4.0 | `ready_sent = 0` (no_reg_bc / tgl_bc **not cleared**) |

(Both source types behave the same here, unlike BC 2.3.)

---

## 4. Screens & UI behaviour

### 4.1 Main page

Same as BC 2.3 §4.1:
- Permission gate.
- Toolbar: Refresh, Detail, Add (Outstanding only), Edit/Delete (Transaction only), and a
  Login BC button.
- **Outstanding** tab: A list + B list. **Transaction** tab: docs whose source's `doc_date` is
  in the working period and the source is posted, both types merged, id desc. Columns
  Rev. Doc. (source doc_number), Nomor AJU, Status (SENT ✓ / **Send Data** button).

### 4.2 Toolbar actions

| Action | Source A selected | Source B selected | Transaction doc selected |
|---|---|---|---|
| Detail | A preview | B preview | Form **read-only** (A or B grid layout by source_type) |
| Add | Form pre-filled from A + 2 Dokumen rows | Form pre-filled from B | (hidden) |
| Edit | — | — | Form editable (grid layout by source_type) |
| Delete | — | — | Confirm → delete |

Nothing selected → "No data selected." After save: close, refresh the Transaction + Source A
lists (not B, §11 Q-s), select the doc. After delete: refresh all lists.

### 4.3 Document form (one modal, paper-form layout)

In order:
1. **Rev. Doc.** (read-only) + **Dokumen** table (Jenis / Nomor / Tanggal, add-row button).
2. **Header:** Nomor Pengajuan (nomorAju).
3. **A. Kantor Pabean** (kodeKantor), **B. Jenis TPB** (kodeJenisTpb). Both hidden on
   Source B add.
4. **C. Jenis Transaksi:** label "PENYERAHAN BKP" + **Tujuan Pengiriman** select.
5. **F. Kolom khusus Bea dan Cukai:** Nomor Pendaftaran (nomorDokumenPabean) + tanggal
   (tanggalAju).
6. **D. Data Pemberitahuan:**
   - **Pengusaha TPB** (Entitas 1): 1. NPWP, 2. Nama (entity select), 3. Alamat,
     4. No izin TPB.
   - **Pengirim Barang** (Entitas 3): 5. NPWP, 6. Nama (select), 7. Alamat.
   - **Pemilik Barang** (Entitas 2): 8. NPWP, 9. Nama (select), 10. Alamat.
   - Picking an entity fills NPWP, nama (hidden), alamat and nomor izin from the master.
7. **Dokumen Pelengkap Pabean** (label for the Dokumen table).
8. **Data Pengangkutan:** 16. Jenis Sarana Pengangkut (namaPengangkut), 17. No Polisi
   (nomorPengangkut).
9. **Data Perdagangan:** 18. Harga Penyerahan (Rp, auto total), 19. Nilai Penggantian Jasa,
   20. Nilai Uang Muka, 21. Jenis Jasa Kena (label only).
10. **Data Pengemas:** 22. Jenis Kemasan (packaging select), 23. Merek, 24. Jumlah Kemasan.
11. **Data Barang:** 24. Volume, 25. Berat Kotor, 26. Berat Bersih, then the barang grid (§4.5).
12. **PPN** summary: 31. Dilunasi, 32. Dibebaskan (blank, read-only), 33. **Tidak Dipungut**
    (= §4.5 formula, read-only).
13. **Tanda tangan Pengusaha TPB:** kota, tanggal, nama, jabatan.

### 4.4 Dokumen rows

- Add row: kodeDokumen "", nomor "", tanggal today.
- Source A and every edit: rows 1–2 have no delete button.
- Source B add: every row has a delete button.
- All rows use the full document-type list.

### 4.5 Barang grid & calculations

- **Source A layout** (per barang): HS select, qty, Harga Penyerahan ("CIF (IDR)" label),
  kode barang, satuan select, uraian, netto, jumlahKemasan + jenis kemasan select. Delete
  button per row; the rest renumber.
- **Source B layout**: same, plus a **volume** input per row.
- "Add row" adds a blank row (all 0 / "").
- **HS selected** → load persenBM/PPN/PPh from the HS master into the row (no text shown).
- **Row Harga Penyerahan changed** (and on new-record render) → header
  `hargaPenyerahan = Σ row hargaPenyerahan`.
- **Row netto changed** → header `netto = Σ row netto` (3 dp). On open, the pre-filled weights
  are **not** summed into the header until a netto is touched (§11 Q-o).
- **Row volume changed** (B) → header `volume = Σ row volume`. Volumes reset to 0 on Source B open.
- **"33. PPN Tidak Dipungut"** display = `Σ row hargaPenyerahan × 0.89` (hardcoded factor,
  the real PPN formula is commented out, §11 Q-m). Display only, not saved or sent.

### 4.6 Client checks before save

- At least 1 barang row ("No data found.").
- No other client validation (the row/dokumen checks are commented out).
- Server field errors are highlighted, with toast "There are fields that have not been filled
  in or are still incorrect."

---

## 5. Server-side rules

### 5.1 Save payload (UI → server)

Header fields (§2.1) + `source_type` + `source_id` (create) or `id` (update) + `barang[]` +
`dokumen[]`. Villiger posts form fields with the arrays as JSON strings. The new app may use
one JSON body.

### 5.2 Validation (length limits only)

nomorAju ≤100, nomorDokumenPabean ≤50, kodeKantor ≤20, kodeJenisTpb ≤20, NPWP ≤50,
alamat ≤200, nomorIjin ≤50, namaPengangkut ≤**20**, nomorPengangkut ≤50,
kodeJenisKemasan ≤20, merkKemasan ≤50, kotaTtd/namaTtd ≤100, jabatanTtd ≤50. Dates are
`YYYY-MM-DD`; everything else is optional.

### 5.3 Create

1. Header from the inputs, plus `nomorAju` upper-cased, `kodeDokumen = "40"`,
   `asuransi/biayaTambahan/biayaPengurang = null`, `kodeEntitas1/2/3 = "7"`, and the source link.
2. Source update (§3.3).
3. Barang rows: position → seriBarang. Mapping as in §2.2, with string fields defaulting to ""
   and numbers to 0. `jumlahRealisasi = hargaPenyerahan`, `cifRupiah` not set.
4. Dokumen rows: position → seriDokumen.
5. Response `{ id, message: "Data telah ditambahkan." }`.

Villiger commits in steps (not atomic, §11 Q-q). **Recommended: one transaction.**

### 5.4 Update

1. Overwrite all form header fields (nomorAju upper-cased). Source link and `sent` are left
   untouched.
2. Source update (§3.3).
3. **Delete and re-insert** Barang (here `jumlahRealisasi = qty`, `cifRupiah = cif`) and
   Dokumen.
4. Response `{ id, message: "Data telah diubah." }`.

### 5.5 Delete

1. Source `ready_sent = 0`.
2. Delete Dokumen, Barang, then the header.
3. Response `{ message: "Data telah dihapus." }`.

### 5.6 Read endpoints

Same set as BC 2.3 §5.6 (A list, B list, Transaction list, show header incl. `source_type` +
rev_doc, show barang, show dokumen, source previews, Source A aggregated lines). Show-dokumen
must handle a null `tanggalDokumen` (Villiger crashes, §11 Q-r).

---

## 6. Endpoint list (logical)

| Method | Purpose |
|---|---|
| GET | Page |
| GET | Outstanding A list / Outstanding B list / Transaction list (JSON) |
| GET | Show document (header + barang + dokumen) |
| GET | Source A preview / aggregated lines; Source B preview |
| POST / PUT / DELETE | Create / Update / Delete |
| GET / POST | CEISA token status / CEISA login |
| POST | Send document `{ id }` |

---

## 7. CEISA authentication

**Identical to the BC 2.6.1 plan §7** (NLE OAuth login, recursive token/expiry lookup, default
600 s, per-user storage, countdown button, 401 flips the button to logged-out).

---

## 8. Send to CEISA

Same flow as BC 2.3 §8:
1. No id → 400.
2. No valid token → 401 "Token tidak valid atau sudah expired. Silakan login ulang."
3. Doc not found → 404.
4. Build the payload (§9).
5. `POST https://apis-gw.beacukai.go.id/openapi/document` with the Bearer token,
   JSON headers and `User-Agent: Mozilla/5.0`. **Timeout 60 s.**
6. Responses:
   - HTTP error → same status + `{message: "Pengiriman gagal.", error, details}`.
   - Network error → 500.
   - **Any 2xx → `sent = 1`** (no status check), with
     `{message: "Data berhasil dikirim.", response: <JSON or {"raw": body}>}`.

The `details` builder is identical to the BC 2.6.1 plan §8.8. UI toasts are the same as BC 2.3.

---

## 9. CEISA JSON payload — EXACT field order

> **Order matters.** Keys are listed in emit order. "Fallback" applies when the value is
> null, "", 0 or 0.0 (**not** the string "0"). "raw" = no fallback (may be `null`).
> `float(...)` = always emitted as a float (`0.0`, `125.5`).

### 9.1 Root object

| # | Key | JSON type | Value |
|---|---|---|---|
| 1 | asalData | string | `"S"` |
| 2 | asuransi | number (float) | float(header.asuransi, fallback 0) → always `0.0` |
| 3 | bruto | number (float) | float(header.bruto, fallback 0) |
| 4 | kodeJenisTpb | string/null | header.kodeJenisTpb (raw) |
| 5 | hargaPenyerahan | number (float) | float(header.hargaPenyerahan, fallback 0) |
| 6 | jabatanTtd | string/null | raw |
| 7 | kodeDokumen | string/null | header.kodeDokumen (`"40"`), raw |
| 8 | kodeKantor | string/null | raw |
| 9 | **kodeTujuanPengiriman** | string | header.**kodeTujuanPengirim**, fallback "" |
| 10 | kotaTtd | string/null | raw |
| 11 | namaTtd | string/null | raw |
| 12 | netto | number (float) | float(header.netto, fallback 0) |
| 13 | nomorAju | string/null | raw (upper-case) |
| 14 | seri | number | `1` |
| 15 | tanggalAju | string/**null** | `YYYY-MM-DD`, or null (no "" fallback) |
| 16 | tanggalTtd | string/**null** | `YYYY-MM-DD`, or null |
| 17 | entitas | array | §9.2 (always 3) |
| 18 | pengangkut | array | §9.6 (always 1) |
| 19 | kemasan | array | §9.5 (0 or 1) |
| 20 | pungutan | array | §9.7 (always 1) |
| 21 | barang | array | §9.4 |
| 22 | idPengguna | string | **logged-in ERP username** (§11 Q-c) |
| 23 | jumlahKontainer | number | `0` |
| 24 | nik | string | `""` (empty; BC 2.3/2.6.1 send `"0"`) |
| 25 | volume | number (float) | float(header.volume, fallback 0) |
| 26 | biayaTambahan | number (float) | → always `0.0` |
| 27 | biayaPengurang | number (float) | → always `0.0` |
| 28 | vd | number (float) | `0.0` |
| 29 | uangMuka | number (float) | float(header.uangMuka, fallback 0) |
| 30 | nilaiJasa | number (float) | float(header.nilaiJasa, fallback 0) |
| 31 | dokumen | array | §9.3 |
| 32 | kontainer | array | always `[]` |

Note how the arrays are interleaved with scalars (entitas … barang, then idPengguna …
nilaiJasa, then dokumen, kontainer). Don't group the arrays at the end.

### 9.2 `entitas[]`: always 3 objects, order seri 3 → 7 → 9

Slot filling: selectEntitas1, selectEntitas2, selectEntitas3 **in that order, skipping empty
ones**. The 1st → seri 3, 2nd → seri 7, 3rd → seri 9 (§11 Q-d). Values come from the
**entity master** (§11 Q-e). Normal mapping: Entitas 1 Pengusaha TPB → 3, Entitas 2 Pemilik
Barang → 7, Entitas 3 Pengirim Barang → 9.

**Slot seri 3**: note `seriEntitas` comes **before** `tanggalIjinEntitas`

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | ("") | "" |
| 2 | kodeEntitas | string | `"3"` | "3" |
| 3 | kodeJenisIdentitas | string | ("6") | "6" |
| 4 | namaEntitas | string | ("") | "" |
| 5 | nibEntitas | string | ("") | "" |
| 6 | nomorIdentitas | string | ("") | "" |
| 7 | nomorIjinEntitas | string | ("") | "" |
| 8 | seriEntitas | number | `3` | 3 |
| 9 | tanggalIjinEntitas | string | `YYYY-MM-DD` ("") | "" |

**Slot seri 7** and **slot seri 9**: identical key sets, only the code/seri differ

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | ("") | "" |
| 2 | kodeEntitas | string | `"7"` / `"9"` | same |
| 3 | kodeJenisApi | string | ("02") | "02" |
| 4 | kodeJenisIdentitas | string | ("6") | "6" |
| 5 | kodeStatus | string | ("0") | "0" |
| 6 | namaEntitas | string | ("") | "" |
| 7 | nibEntitas | string | ("") | "" |
| 8 | nomorIdentitas | string | ("") | "" |
| 9 | seriEntitas | number | `7` / `9` | same |

### 9.3 `dokumen[]`: one per Dokumen row (no `idDokumen` key)

| # | Key | Type | Value |
|---|---|---|---|
| 1 | kodeDokumen | string | row.kodeDokumen |
| 2 | nomorDokumen | string/null | row.nomorDokumen |
| 3 | seriDokumen | number | row.seriDokumen |
| 4 | tanggalDokumen | string/null | `YYYY-MM-DD` or null |

### 9.4 `barang[]`: one per Barang row

| # | Key | Type | Value |
|---|---|---|---|
| 1 | asuransi | number (float) | float(**header**.asuransi, fallback 0) → `0.0` |
| 2 | bruto | number (float) | float(**header**.bruto, fallback 0): the document's gross weight on every row (§11 Q-g) |
| 3 | cif | number (float) | `0.0` (hardcoded) |
| 4 | diskon | number (float) | float(row.diskon, fallback 0) → `0.0` |
| 5 | hargaEkspor | number (float) | → `0.0` |
| 6 | hargaPenyerahan | number (float) | float(row.hargaPenyerahan, fallback 0) |
| 7 | hargaSatuan | number (float) | → `0.0` |
| 8 | **isiPerkemasan** | null | row.isiPerKemasan, raw → always `null` (note **lower-case "k"** in the key) |
| 9 | jumlahKemasan | number/null | row.jumlahKemasan (raw int) |
| 10 | jumlahRealisasi | number | **row.hargaPenyerahan** (raw, not float-cast) |
| 11 | jumlahSatuan | number | row.jumlahSatuan (raw) |
| 12 | kodeBarang | string | raw |
| 13 | kodeDokumen | string | **header**.kodeDokumen (`"40"`) |
| 14 | kodeJenisKemasan | string | raw |
| 15 | kodeSatuanBarang | string | raw |
| 16 | merk | string | raw (stored "") |
| 17 | netto | number | raw |
| 18 | nilaiBarang | number | row.nilaiBarang (0) → `0` |
| 19 | posTarif | string | raw |
| 20 | seriBarang | number | int(row.seriBarang) |
| 21 | spesifikasiLain | string | raw ("") |
| 22 | tipe | string | raw ("") |
| 23 | ukuran | string | raw ("") |
| 24 | uraian | string | raw |
| 25 | volume | number | row.volume (raw) |
| 26 | cifRupiah | number | **row.cif** (raw) → in practice `0` |
| 27 | hargaPerolehan | number | row.hargaPerolehan (0) |
| 28 | kodeAsalBahanBaku | string | `"1"` (BC 2.3/2.6.1 send "0") |
| 29 | ndpbm | number | row.ndpbm (raw) → 0 |
| 30 | uangMuka | number | **header**.uangMuka (raw) |
| 31 | nilaiJasa | number | **header**.nilaiJasa (raw) |
| 32 | barangTarif | array | §9.4.1: exactly 1 object |

No `idBarang`, `barangDokumen`, `persen*`, `nilaiDiskon` or `kodeNegaraAsal` keys.

#### 9.4.1 `barang[].barangTarif[]`: 1 object (PPN)

> ⚠ Here `kodeJenisPungutan` is the **last** key, and `seriBarang` = the barang's own seri
> (BC 2.3/2.6.1 use 1/2/3).

| # | Key | Type | Value |
|---|---|---|---|
| 1 | kodeJenisTarif | string | `"1"` |
| 2 | jumlahSatuan | number | row.netto (fallback 0) |
| 3 | kodeFasilitasTarif | string | `"3"` |
| 4 | kodeSatuanBarang | string | row.kodeSatuanBarang |
| 5 | nilaiBayar | number | **row.cif** (fallback 0) → in practice `0` |
| 6 | nilaiFasilitas | number | `0` |
| 7 | nilaiSudahDilunasi | number | `0` |
| 8 | seriBarang | number | int(row.seriBarang) |
| 9 | tarif | number | `11` |
| 10 | tarifFasilitas | number | `100` |
| 11 | kodeJenisPungutan | string | `"PPN"` |

### 9.5 `kemasan[]`: 0 or 1 object

Included only if header kodeJenisKemasan **or** jumlahKemasan is non-empty/non-zero.

| # | Key | Type | Value |
|---|---|---|---|
| 1 | jumlahKemasan | number/null | header.jumlahKemasan (raw) |
| 2 | kodeJenisKemasan | string/null | header.kodeJenisKemasan (raw) |
| 3 | merkKemasan | string/null | header.merkKemasan (raw) |
| 4 | seriKemasan | number | header.seriKemasan (fallback `1`) → always 1 |

(BC 2.3 order is jumlah, jenis, **seri, merk**. BC 4.0 is jumlah, jenis, **merk, seri**.)

### 9.6 `pengangkut[]`: exactly 1 object

| # | Key | Type | Value |
|---|---|---|---|
| 1 | namaPengangkut | string | header.namaPengangkut ("") |
| 2 | nomorPengangkut | string | header.nomorPengangkut ("") |
| 3 | seriPengangkut | number | header.seriPengangkut (fallback `1`) → always 1 |

There are no `kodeCaraAngkut` or `kodeBendera` keys.

### 9.7 `pungutan[]`: exactly 1 object

| # | Key | Type | Value |
|---|---|---|---|
| 1 | kodeFasilitasTarif | string | `"3"` |
| 2 | kodeJenisPungutan | string | `"PPN"` |
| 3 | nilaiPungutan | number | `0` |

### 9.8 Skeleton example (values illustrative)

```json
{
  "asalData": "S", "asuransi": 0.0, "bruto": 520.0, "kodeJenisTpb": "1",
  "hargaPenyerahan": 15000000.0, "jabatanTtd": "Manager", "kodeDokumen": "40",
  "kodeKantor": "071100", "kodeTujuanPengiriman": "1", "kotaTtd": "Jember",
  "namaTtd": "Budi", "netto": 500.0, "nomorAju": "000040...", "seri": 1,
  "tanggalAju": "2026-10-26", "tanggalTtd": "2026-10-26",
  "entitas": [
    { "alamatEntitas": "...", "kodeEntitas": "3", "kodeJenisIdentitas": "6", "namaEntitas": "...", "nibEntitas": "...", "nomorIdentitas": "...", "nomorIjinEntitas": "...", "seriEntitas": 3, "tanggalIjinEntitas": "2020-01-01" },
    { "alamatEntitas": "...", "kodeEntitas": "7", "kodeJenisApi": "02", "kodeJenisIdentitas": "6", "kodeStatus": "0", "namaEntitas": "...", "nibEntitas": "", "nomorIdentitas": "...", "seriEntitas": 7 },
    { "alamatEntitas": "...", "kodeEntitas": "9", "kodeJenisApi": "02", "kodeJenisIdentitas": "6", "kodeStatus": "0", "namaEntitas": "...", "nibEntitas": "", "nomorIdentitas": "...", "seriEntitas": 9 }
  ],
  "pengangkut": [ { "namaPengangkut": "TRUCK", "nomorPengangkut": "P 1234 AB", "seriPengangkut": 1 } ],
  "kemasan": [ { "jumlahKemasan": 10, "kodeJenisKemasan": "BX", "merkKemasan": "-", "seriKemasan": 1 } ],
  "pungutan": [ { "kodeFasilitasTarif": "3", "kodeJenisPungutan": "PPN", "nilaiPungutan": 0 } ],
  "barang": [
    {
      "asuransi": 0.0, "bruto": 520.0, "cif": 0.0, "diskon": 0.0, "hargaEkspor": 0.0,
      "hargaPenyerahan": 15000000.0, "hargaSatuan": 0.0, "isiPerkemasan": null,
      "jumlahKemasan": 10, "jumlahRealisasi": 15000000.0, "jumlahSatuan": 100.0,
      "kodeBarang": "SP-01", "kodeDokumen": "40", "kodeJenisKemasan": "BX",
      "kodeSatuanBarang": "PCE", "merk": "", "netto": 500.0, "nilaiBarang": 0,
      "posTarif": "84879000", "seriBarang": 1, "spesifikasiLain": "", "tipe": "",
      "ukuran": "", "uraian": "BEARING", "volume": 0.0, "cifRupiah": 0.0,
      "hargaPerolehan": 0.0, "kodeAsalBahanBaku": "1", "ndpbm": 0.0,
      "uangMuka": 0.0, "nilaiJasa": 0.0,
      "barangTarif": [ { "kodeJenisTarif": "1", "jumlahSatuan": 500.0, "kodeFasilitasTarif": "3", "kodeSatuanBarang": "PCE", "nilaiBayar": 0, "nilaiFasilitas": 0, "nilaiSudahDilunasi": 0, "seriBarang": 1, "tarif": 11, "tarifFasilitas": 100, "kodeJenisPungutan": "PPN" } ]
    }
  ],
  "idPengguna": "erp_username", "jumlahKontainer": 0, "nik": "", "volume": 0.0,
  "biayaTambahan": 0.0, "biayaPengurang": 0.0, "vd": 0.0, "uangMuka": 0.0, "nilaiJasa": 0.0,
  "dokumen": [ { "kodeDokumen": "380", "nomorDokumen": "INV-1", "seriDokumen": 1, "tanggalDokumen": "2026-10-20" } ],
  "kontainer": []
}
```

---

## 10. Laravel implementation notes (payload fidelity)

**Identical to the BC 2.6.1 plan §10.** BC 4.0 specifics:
- Use hand-built ordered arrays only. Watch the **interleaved root order** (§9.1) and the
  BC 4.0-only key spellings `isiPerkemasan` and `kodeTujuanPengiriman` (built from the stored
  `kodeTujuanPengirim`).
- `json_encode(..., JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES)` sent via
  `withBody()`. Many fields are deliberately float-cast (`0.0`), while others are raw (an int
  `jumlahKemasan`, or `null`).
- Keep raw nulls where §9 says "raw" (e.g. `isiPerkemasan`, `tanggalAju` when empty).
- The fallback helper treats only null, `""`, `0`, `0.0` as empty.
- Send timeout 60 s, and keep `User-Agent: Mozilla/5.0`.

---

## 11. Known quirks (replicated as-is; suggested fixes for later)

| ID | Quirk (current behaviour) | Suggested fix |
|---|---|---|
| **Q-a** | **`sent = 1` on any HTTP 2xx, even if CEISA's body reports a rejection.** The message is fixed and `idHeader` is not stored. | Check `status == "OK"` and store idHeader. |
| Q-b | `cif` is hardcoded to `0.0`. `cifRupiah` and the tax `nilaiBayar` read the barang `cif`, which is always 0. Pungutan `nilaiPungutan` is 0. | Base nilaiBayar/pungutan on hargaPenyerahan × 11%, if CEISA requires it. |
| Q-c | `idPengguna` = the **ERP** username, not the CEISA account. | Store the CEISA username at login and send that. |
| Q-d | Entitas slots are filled by "next non-empty" (a missing Entitas 1 shifts the others). | Fixed mapping E1→3, E2→7, E3→9. |
| Q-e | Entities are sent from the **master**, not the typed or saved snapshot. | Send the document snapshot. |
| Q-f | A sent document can still be edited, deleted and re-sent. | Lock edit/delete when `sent = 1`. |
| Q-g | Every barang row gets the **header** `bruto`, `uangMuka` and `nilaiJasa`. | Per-row values, or allocate them. |
| Q-h | The tarif is hardcoded to PPN 11%, fasilitas 3. | Take it from the HS master. |
| Q-i | Hardcoded or never-set values: `seriPengangkut 1`, `seriKemasan 1`, `asuransi/biaya* 0.0`, `vd 0.0`, `nik ""`, `kontainer []`, `jumlahKontainer 0`, `kodeAsalBahanBaku "1"`. | Confirm with CEISA. |
| Q-j | `jumlahRealisasi` is sent as row hargaPenyerahan (a money value in a quantity field). | Send qty. |
| Q-k | `kodeEntitas1/2/3 = "7"` is stored but never used. | Drop it. |
| Q-l | Barang merk/tipe/ukuran/spesifikasiLain have no inputs, so they're always "". | Add the inputs. |
| Q-m | "PPN Tidak Dipungut" display = Σ hargaPenyerahan × **0.89** (hardcoded; the real formula is commented out). | Σ hargaPenyerahan × persenPPN%. |
| Q-n | Stored barang `jumlahRealisasi` = hargaPenyerahan on create but qty on edit. `cifRupiah` is set only on edit. | Make them consistent. |
| Q-o | Source A pre-filled netto is not summed into header netto until a row netto is edited. | Sum on open. |
| Q-p | Header numeric NOT NULL columns (hargaPenyerahan, nilaiJasa, uangMuka, bruto, netto, volume, jumlahKemasan) have no client default, so an empty field fails at the DB. | Default to 0 / validate. |
| Q-q | Create, update and delete are not atomic. | One transaction. |
| Q-r | The show-dokumen serializer crashes on a null `tanggalDokumen`. | Null-safe date. |
| Q-s | After save, only the Source A Outstanding list is refreshed. | Refresh all lists. |
| Q-t | Source A Dokumen rows 1–2 can't be deleted but accept any type. The row-specific type filter exists in the code but is ignored. | Decide: restrict them (like BC 2.3) or allow deletion. |
| Q-u | The Source A grid's Harga Penyerahan input is `type="number"` but receives a thousands-formatted value, so it can display blank for values ≥ 1,000. | Use a text input with a numeric mask. |
| Q-v | Delete doesn't clear the source's `no_reg_bc`/`tgl_bc`. | Clear them. |
| Q-w | No standalone BC 4.0 (the Transaction-tab Add path is missing). | Keep it, or add a blank create. |

---

## 12. Open items for the database session (Laravel codebase)

- Source A / Source B tables and the aggregated-goods query. Where `posted`, `bc_type`,
  `ready_sent`, `no_reg_bc`, `tgl_bc` and the warning flag live (shared with BC 2.3).
- Source link storage (two nullable FKs + `source_type`, or polymorphic). This should be
  consistent across BC 2.3 / 4.0.
- Header table (kemasan/pengangkut as columns), Barang and Dokumen child tables.
- Masters: entity, HS, satuan, kemasan, jenis dokumen. Reuse the CEISA token table.
- The working-period concept and per-route permissions.

---

## Appendix A: code lists used by the UI

- **kodeTujuanPengirim** (sent as `kodeTujuanPengiriman`): 1 Penyerahan BKP *(default)*,
  2 Penyerahan JKP, 3 Retur, 4 Non Penyerahan, 5 Lainnya.
- **kodeJenisTpb:** free text, default `1` (Kawasan Berikat).
- **kodeKantor default (company-specific, make configurable):** 071100 (KPPBC TMP C Jember).
- **Entity seri slots:** 3 Pengusaha TPB, 7 Pemilik Barang, 9 Pengirim Barang.
