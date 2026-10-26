# Document BC 2.3 — Functional Plan for Re-implementation (Laravel 12)

Source of truth: the Villiger ERP `document_bc23` module, which uses the legacy
form-encoded add/edit path. This plan describes **behaviour, rules and the exact CEISA
payload**, not code. The database design is out of scope and will be decided in the Laravel
codebase session; everything below uses *logical* field names.

Companion plan: `documentbc261_plan.md`. Same structure and conventions. The CEISA login
(§7) and the Laravel payload-fidelity notes (§10) are **identical** there.

Decisions already made:
- **Fidelity:** copy current behaviour exactly, including the two bugs (`tanggalTiba`
  hardcoded, and `sent` set on any HTTP 2xx). Every deviation is listed in §11 *Known quirks*.
- **Outstanding:** **two** generic source contracts, A = goods packing list and
  B = sparepart packing list (§3).
- **Kemasan / Kontainer:** **single values on the header** (one of each per document). They
  are sent as arrays of 0 or 1 item.
- **CEISA auth:** a per-user login modal, with the token stored per user (shared with every
  BC module).
- **Scope:** backend, the CEISA payload, and UI behaviour (not visual design).

---

## 1. Overview

- BC 2.3 = customs document for **importing goods into a bonded zone (TPB)**.
- Lifecycle: **Draft (saved)** → **Sent** (`sent = 1`).
- **Every BC 2.3 is created from an Outstanding source.** There is no standalone/blank create,
  because the Add button only shows on the Outstanding tab.
- Each document links to exactly one source: **A** (goods packing list) or **B** (sparepart
  packing list), recorded by `source_type` 0 / 1.
- Child collections: **Barang** and **Dokumen** only. Kemasan, Kontainer and Pengangkut are
  header fields. BC 2.3 has no Bahan Baku and no Jaminan.

---

## 2. Logical data model (persisted fields)

All header columns are nullable in Villiger. Minimal validation (§5.2).

### 2.1 Document header

| Group | Field | Type | Notes |
|---|---|---|---|
| Link | id | int | |
| | source_type | 0/1 | 0 = Source A (goods), 1 = Source B (sparepart) |
| | source_a_id / source_b_id | int, nullable | Exactly one is set, matching source_type |
| | sent | int (0/1), nullable | |
| Header | nomorAju | string(100) | Submission number |
| | kodeDokumen | string | Always `"23"` (set on create) |
| | kodeKantor | string(20) | UI default `071100` (KPPBC TMP C Jember). **Make configurable.** |
| | kodeKantorBongkar | string(20) | UI default `070100` (KPPBC TMP Tanjung Perak). **Make configurable.** |
| | kodeTujuanTpb | string | Select 1–7 (Appendix A) |
| | nomorDokumenPabean, tanggalDokumenPabean | string(50), date | "D. Diisi oleh Bea dan Cukai" (registration no./date). **Not sent to CEISA**; copied to the source (§5.3). |
| BC 1.1 | nomorBc11, tanggalBc11, posBc11, subPosBc11 | string, date, string, string | tanggalBc11 defaults to today |
| Ports | kodePelMuat, kodePelTransit, kodePelBongkar | string(20) | Codes from the port master |
| | selectPelMuat / selectPelTransit / selectPelBongkar | int, nullable | FK to the port master (UI only) |
| TPS | kodeTps | string(20) | |
| | ketTps | string(200) | Notes; not sent |
| Arrival | tanggalTiba | date, nullable | **No UI input and never saved** → see §11 Q-a |
| Prices | kodeValuta | string(20) | |
| | ndpbm | decimal | Exchange rate |
| | kodeIncoterm | string | **Code string** of the incoterm (e.g. `"CIF"`), not the FK |
| | fob, freight, asuransi | decimal | UI sets empty → 0 before submit |
| | kodeAsuransi | string | `LN` / `DN` (default LN) |
| | cif | decimal | Header total (USD) = Σ barang cif |
| | cifRupiah | — | **Not persisted.** Display only = ndpbm × cif |
| | nilaiBarang | decimal | = cif (set on create only) |
| | hargaPenyerahan, biayaTambahan, biayaPengurang | decimal, nullable | Never set → always null |
| | kodeTutupPu, kodeKenaPajak | string, nullable | Never set (send hardcodes them) |
| Weight | bruto | decimal | Typed manually (kg, 3 dp display) |
| | netto | decimal | = Σ barang netto (auto) |
| Entitas 1: Importir / Pengusaha TPB | nomorIdentitasEntitas1, namaEntitas1, alamatEntitas1, nomorIjinEntitas1, nibEntitas1, selectEntitas1 | strings + FK | |
| Entitas 2: Pemasok (supplier) | namaEntitas2, alamatEntitas2, kodeNegaraEntitas2, selectEntitas2 | strings + FK | |
| Entitas 3: Pemilik Barang | nomorIdentitasEntitas3, namaEntitas3, alamatEntitas3, selectEntitas3 | strings + FK | |
| Pengangkut | kodeCaraAngkut | string | Select 1–9 (Appendix A), default 1 (LAUT) |
| | namaPengangkut, nomorPengangkut | string | Vessel/carrier name, voyage/flight no. |
| | kodeBendera, selectBendera | string, FK | Flag country (from the country master) |
| | seriPengangkut | int, nullable | Never set → sent as 0 |
| Kemasan (single) | jumlahKemasan | int | |
| | kodeJenisKemasan, selectJenisKemasan | string, FK | From the packaging master |
| | merkKemasan | string(100) | |
| | seriKemasan | int, nullable | Never set → sent as 1 |
| Kontainer (single) | nomorKontainer | string(50) | |
| | kodeUkuranKontainer | string | 20/40/45/60 |
| | kodeTipeKontainer | string | 1–8, 99 |
| | kodeJenisKontainer | string | 4 Empty / 7 LCL / 8 FCL |
| | selectKontainer | FK, nullable | |
| Pengesahan | kotaTtd, tanggalTtd, namaTtd, jabatanTtd | string, date, string, string | tanggalTtd defaults to today |

Dead legacy columns in Villiger (seri*/kode* per entitas, single dokumen columns, realisasi
penerimaan columns, isRealisasi = 0): **do not recreate.**

### 2.2 Child: Barang

`seriBarang` = 1-based position, assigned by the server.

| Field | Type | Source on save | Editable in UI grid? |
|---|---|---|---|
| seriBarang | int | position | no |
| posTarif | string | row | yes (HS select) |
| kodeBarang | string(20) | row | yes |
| uraian | string(100) | row | yes |
| merk, tipe, ukuran, spesifikasiLain | string | row | **no** → always "" (§11 Q-l) |
| kodeSatuanBarang | string | row | yes (unit select) |
| jumlahSatuan | decimal | row qty | yes |
| jumlahKemasan | decimal | row carton | no (from source) |
| kodeJenisKemasan | string | row | no (from source) |
| kodeDokumen | string | **null on create, "23" on edit** (§11 Q-o) | no |
| netto | decimal | row | yes |
| cif | decimal | row (USD) | yes |
| cifRupiah | decimal | row = cif × ndpbm (auto) | no |
| ndpbm | decimal | header ndpbm (auto) | no |
| fob / asuransi / freight | decimal (fob int) | **the header totals copied onto every row** (§11 Q-g) | no |
| nilaiBarang | decimal | = row cif | no |
| kodeKategoriBarang | string | row | yes (select) |
| kodeNegaraAsal | string | row | yes (country select) |
| keteranganDokumen | string(50) | row | **no** → "" |
| persenBM, persenPPN, persenPPh | decimal | from the HS master when HS is picked | no |
| nilaiBM, nilaiPPN, nilaiPPh | decimal | computed in the UI (§4.5) | no |
| nilaiTambah, diskon, hargaPenyerahan, hargaPerolehan, hargaSatuan, hargaEkspor, kodePerhitungan, isiPerKemasan | nullable | never set | no |

### 2.3 Child: Dokumen

| Field | Type | Notes |
|---|---|---|
| seriDokumen | int | position (server) |
| kodeDokumen | string | From the document-type master. Special codes: `380` Invoice, `705`/`740` BL/AWB, `999` Surat Keputusan |
| nomorDokumen | string(100) | |
| tanggalDokumen | date | UI default today |

### 2.4 CEISA token (per user)

Same as the BC 2.6.1 plan §2.8: one row per user (user_id unique, bearer_token,
expires_at UTC), **shared by every BC module.**

### 2.5 Reference / master data needed

- Entity master: nama, alamat, NPWP (nomorIdentitas), NIB, nomorIjinEntitas,
  tanggalIjinEntitas, kodeJenisApi, kodeJenisIdentitas, kodeStatus, country → kodeNegara/namaNegara.
- Port master (Pelabuhan): id, kodePelabuhan, name.
- Country master: kodeNegara, name (used for flag and country of origin).
- Incoterms master: id, code (the code string is what gets stored).
- Container master (kodeUkuran, kodeTipe): fills size/type when picked.
- Packaging master (kodeJenisKemasan, namaJenisKemasan).
- HS / Pos Tarif master: posTarif, persenBM, persenPPN, persenPPh.
- Kategori Barang, Jenis Satuan, Jenis Dokumen masters.

---

## 3. Outstanding (two source contracts)

Both sources share these rules:
- Source header has `id`, `doc_number`, `doc_date`, `posted` (Villiger `lock`), `bc_type`,
  `ready_sent` (0/1), `no_reg_bc`, `tgl_bc`.
- **Eligible** when: `doc_date` falls in the user's **working period** (month + year),
  `posted = 1`, `bc_type = "BC 2.3"`, and **no** BC 2.3 document links to it with the matching
  `source_type`.
- Server order: id desc. The UI table then sorts by column 0 ascending.

### 3.1 Source A: goods packing list (`source_type = 0`)

Header fields shown: doc_number ("Rev. Doc."), container no., seal no., registration,
shipment no., arrival date. Plus a warning flag (Villiger `cek_pv`): when it is 0, the row is
shown in **red** (display only).

**Lines for pre-fill:** an *aggregated goods view* of the packing list (Villiger uses DB view
`vw_detail_packing_list_barang`, one row per aggregated article). Each row has: item_code,
item_name, qty, unit, carton (count), weight_net, kodeJenisKemasan.

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
| kodeNegaraAsal | the view's kodeNegaraAsal if present, else "". **The Villiger view doesn't return it, so it's always ""** (§11 Q-k) |
| cif, cifRupiah, ndpbm, persen*, nilai* | 0 |
| posTarif, kodeKategoriBarang, merk, tipe, ukuran, spesifikasiLain, keteranganDokumen | "" |

**Default Dokumen rows (Source A only):** 2 fixed rows are created on open:
- Row 1: type limited to **380 (Invoice)**.
- Row 2: type limited to **705 / 740 (BL / AWB)**.
- Both default tanggal = today. **These 2 rows cannot be deleted.** Further rows can use any
  type and can be deleted.

Read-only preview (Detail): header fields + lines split into two tables by line type (goods
vs help material), with columns Article-No, Type of Tobacco, Format, Charge, Qty, Unit,
Bale/Carton, Weight gross, Weight net.

### 3.2 Source B: sparepart packing list (`source_type = 1`)

Header fields shown: doc_number ("No. Bukti"), invoice date ("Tanggal Dokumen"), Lokal/Import.

**Lines:** item_code, description, qty, unit, unit_price, amount.

| Barang field | From |
|---|---|
| seriBarang | position |
| kodeBarang | item_code |
| uraian | description |
| kodeSatuanBarang | unit |
| jumlahSatuan | qty |
| **cifRupiah** | **unit_price** (not amount, §11 Q-j) |
| jumlahKemasan, netto, cif, ndpbm, persen*, nilai* | 0 |
| kodeJenisKemasan, kodeNegaraAsal, posTarif, kodeKategoriBarang, merk, tipe, ukuran, spesifikasiLain, keteranganDokumen | "" |

**Default Dokumen rows (Source B):** none. The list starts empty. The first 2 rows the user
adds still get the 380 and 705/740 type limits and can't be deleted (same renderer as A).

Read-only preview: header + lines table (Article-No, Deskripsi, Qty, Unit, Harga/Unit, Subtotal).

### 3.3 State changes on the source

| Event | Source A | Source B |
|---|---|---|
| Create BC 2.3 | `ready_sent = 1`, `no_reg_bc = nomorDokumenPabean`, `tgl_bc = tanggalDokumenPabean` | `ready_sent = 1`, `no_reg_bc = nomorDokumenPabean` (**tgl_bc not set**) |
| Edit BC 2.3 | `no_reg_bc`, `tgl_bc` updated | `no_reg_bc`, `tgl_bc` updated |
| Delete BC 2.3 | `ready_sent = 0` (no_reg_bc / tgl_bc **not cleared**) | same |

(The B-create / B-edit difference and the leftover values after delete are §11 Q-n.)

---

## 4. Screens & UI behaviour

### 4.1 Main page

- Permission gate: no "view" access → redirect to home.
- Toolbar: **Refresh** (reloads all 3 lists), **Detail**, **Add** (Outstanding tab only),
  **Edit** and **Delete** (Transaction tab only), plus a **Login BC** button.
- Tabs:
  - **Outstanding**: two tables, Source A list and Source B list (§3). Selecting a row in one
    clears any selection in the other tables.
  - **Transaction**: BC 2.3 documents whose **source's doc_date is in the working period and
    source is posted**, from both source types merged, sorted by id desc. Columns:
    **Rev. Doc.** (the source's doc_number), **Nomor AJU**, **Status** (`sent = 1` → green
    "SENT ✓", otherwise a **Send Data** button that doesn't select the row).
- Row click highlights the row (yellow). There is no click-again-to-unselect here.

### 4.2 Toolbar actions

| Action | Source A row selected | Source B row selected | Transaction row selected |
|---|---|---|---|
| Detail | Source A read-only preview | Source B read-only preview | Document form, **read-only** (no save button) |
| Add | Form pre-filled from A (§3.1) + 2 default Dokumen rows | Form pre-filled from B (§3.2), no Dokumen rows | (button hidden) |
| Edit | — | — | Form loaded, editable, "Simpan" = update |
| Delete | — | — | Confirm dialog → delete (§5.5) |

If nothing is selected → toast "No data selected." On Add, if the selected source is missing
a selection the form doesn't open.

On Add open, the form is reset:
- **Source A:** entitas 1/2/3, ports (muat/transit/bongkar), flag, container and packaging
  selects are **cleared**.
- **Source B:** entitas 1/2/3, container and packaging are cleared. Ports and flag keep their
  defaults (first master record).
- "Rev. Doc." (read-only) shows the source's doc_number.

After a successful save: close the modal, refresh the Transaction list and the Source A list
(**not** the B list, §11 Q-s), and select the saved document. After delete: refresh all lists.

### 4.3 Document form (one modal, laid out like the paper BC 2.3 form, no tabs)

In order:
1. **Rev. Doc.** (read-only) + **Dokumen** table (Jenis / Nomor / Tanggal; add-row button,
   delete per row except rows 1–2).
2. **Header:** Nomor Pengajuan (nomorAju), A. Tujuan (kodeTujuanTpb), Pelabuhan Bongkar
   (select → code).
3. **B. Data Pemberitahuan / Pemasok** (Entitas 2): entity select → nama, alamat, country code
   + country name.
4. **D. Diisi oleh Bea dan Cukai:** No. + Tgl. Dokumen Pabean, Kantor Pabean Bongkar
   (default 070100), Kantor Pabean (default 071100).
5. **Importir / Pengusaha TPB** (Entitas 1): NPWP, entity select, nama, alamat, No. izin TPB, NIB.
6. **Pemilik Barang** (Entitas 3): NPWP, entity select, nama, alamat.
7. **PPJK**: labels only (no inputs, nothing saved).
8. **16. Invoice / 18. Surat Keputusan / 20. BL-AWB** shortcut fields (§4.4). 17. Fasilitas
   Impor and 19. LC are labels only.
9. **21. BC 1.1**: nomor, tanggal, pos, sub pos.
10. **11. Cara Pengangkutan** (select). **22. Tempat Penimbunan**: kodeTps + ketTps.
11. **12. Sarana Pengangkut**: nama, nomor voy/flight, bendera (country select → kodeBendera).
12. **23. Valuta**, **24. NDPBM** (change → recalculation, §4.5).
13. **13/14/15. Pelabuhan Muat / Transit / Bongkar** (selects → codes). Pelabuhan Bongkar
    appears twice (here and in the header), and the two stay in sync.
14. **25. FOB**, **26. Freight**, **27. Asuransi** (LN/DN + value).
15. **28. Nilai CIF**: incoterm select, CIF (USD, header total), CIF Rupiah (read-only).
16. **29. Kontainer**: nomor, ukuran (select), tipe (select), jenis (select). Picking a
    container from the master fills ukuran and tipe.
17. **30. Kemasan**: jumlah, jenis (packaging select → code), merek.
18. **31. Bruto** (manual), **32. Netto** (auto).
19. **Data Barang** grid (§4.5) + **Pungutan** summary.
20. **C. Pengesahan**: kota, tanggal, nama, jabatan.

Entity, port, flag, container and packaging selects: choosing a record fills its code and
detail fields from the master.

### 4.4 Invoice / BL-AWB / SK shortcuts ↔ Dokumen rows

- Each shortcut is bound to the **first** Dokumen row with the matching code: Invoice ↔ `380`,
  BL/AWB ↔ `705` or `740`, SK ↔ `999`.
- The shortcut **nomor is read-only**, mirroring the row's nomor. The shortcut **tanggal is
  editable** and writes back to that row.
- Changing a row's type, nomor or date refreshes all three shortcuts. If no matching row
  exists, the shortcut is blank.

### 4.5 Barang grid & calculations (client-side, recalculated live)

- Each barang is shown as a 3-line block: line 1 HS select, kategori, country of origin, HS
  rate text, qty, **CIF (USD)**; line 2 kode barang, satuan; line 3 uraian, **netto**. There's
  a delete button per row (the rest renumber) and an "add row" button (blank row).
- **HS selected** → load persenBM/PPN/PPh from the HS master, show
  "BM x% 100% DTC PPH y% 100% TID PPN z% 100% TID" (the PPH/PPN labels are swapped, §11 Q-m),
  then recalculate taxes.
- **Row CIF (USD) changed** → header `cif = Σ row cif` → recalc CIF Rupiah.
- **NDPBM or incoterm changed** → recalc CIF Rupiah.
- **Recalc CIF Rupiah:** header `cifRupiah = round2(ndpbm × header cif)`; for every row,
  `row.ndpbm = ndpbm` and `row.cifRupiah = row.cif × ndpbm`; then recalc taxes. This
  **overwrites** Source B's pre-filled cifRupiah.
- **Row netto changed** (and on every grid render) → header `netto = Σ row netto`, shown to 3 dp.
- **Taxes per row**, stored as nilaiBM/nilaiPPN/nilaiPPh but **not sent**:
  - `BM = persenBM% × cifRupiah`
  - `PPN = persenPPN% × (cifRupiah + BM)`
  - `PPh = persenPPh% × (cifRupiah + BM)`
  - Each one is rounded **up to the next 1000**: if `x mod 1000 = 0` keep x, otherwise
    `x − (x mod 1000) + 1000`.
- **Pungutan summary:** BM, PPN, PPh totals. "Ditangguhkan" total = Σ BM.
  "Tidak Dipungut" total = Σ PPN + Σ PPh.
- On Edit load, the header CIF Rupiah is left blank until NDPBM or CIF changes (it's not stored).

### 4.6 Client-side checks before save

- At least 1 barang row, otherwise toast "No data found."
- Empty FOB, Freight or Asuransi → set to 0.
- Formatted numbers are un-formatted (thousands separators removed) before submit.
- Server field errors come back per field. They are highlighted, with toast "There are fields
  that have not been filled in or are still incorrect."

---

## 5. Server-side rules

### 5.1 Save payload (UI → server)

Header fields (§2.1) + `source_type` + `source_id` (create) or `id` (update) + `barang[]` +
`dokumen[]`. Villiger posts form-encoded fields with the two arrays as JSON strings. The new
app may post a single JSON body; key names follow the Laravel app's conventions.

### 5.2 Validation

- Length limits only: nomorAju ≤100, nomorDokumenPabean ≤50, kodeKantor/kodeKantorBongkar ≤20,
  NPWP ≤50, nomorIjin ≤50, NIB ≤50, alamat ≤200, kodeNegaraEntitas2 ≤5, BC 1.1 fields ≤50,
  kodeTps ≤20, ketTps ≤200, pengangkut nama/nomor ≤200, kodeValuta ≤20, nomorKontainer ≤50,
  merkKemasan ≤100, kotaTtd/namaTtd ≤100, jabatanTtd ≤50.
- Everything else is optional. Dates are `YYYY-MM-DD`.

### 5.3 Create

1. Header: all §2.1 inputs, plus `kodeDokumen = "23"`, `nilaiBarang = cif`,
   `kodeIncoterm = the selected incoterm's code string`, `tanggalTiba = null`,
   `hargaPenyerahan/biayaTambahan/biayaPengurang = null`, and `source_type` + source id.
2. Update the source as in §3.3.
3. Barang rows (position → seriBarang, mapping in §2.2; fob/asuransi/freight = header values).
4. Dokumen rows (position → seriDokumen).
5. Response `{ id, message: "Data telah ditambahkan." }`.

Villiger commits header, source, and each row separately (not atomic, §11 Q-p).
**Recommended: one DB transaction.**

### 5.4 Update

1. Overwrite header fields: everything in the form. The source link, `sent`, `nilaiBarang`
   and `tanggalTiba` are left untouched.
2. Update the source's `no_reg_bc` / `tgl_bc` (§3.3).
3. **Delete and re-insert** all Barang and Dokumen rows.
4. Response `{ id, message: "Data telah diubah." }`.

### 5.5 Delete

1. Source `ready_sent = 0`.
2. Delete Dokumen rows, Barang rows, then the header.
3. Response `{ message: "Data telah dihapus." }`.

### 5.6 Read endpoints

- Outstanding A list, Outstanding B list, Transaction list (id, rev_doc, nomorAju, sent).
- Show header (dates as `YYYY-MM-DD`, "" when null; includes rev_doc), Show barang rows,
  Show dokumen rows.
- Source A preview (header + lines), Source B preview (header + lines), Source A
  aggregated-goods lines (for pre-fill).

---

## 6. Endpoint list (logical)

| Method | Purpose |
|---|---|
| GET | Page |
| GET | Outstanding A list (JSON) |
| GET | Outstanding B list (JSON) |
| GET | Transaction list (JSON) |
| GET | Show document (header + barang + dokumen) |
| GET | Source A preview / Source A aggregated goods lines |
| GET | Source B preview |
| POST | Create |
| PUT/PATCH | Update |
| DELETE | Delete |
| GET | CEISA token status |
| POST | CEISA login |
| POST | Send document `{ id }` |

---

## 7. CEISA authentication

**Identical to the BC 2.6.1 plan §7.** Summary:
- `POST https://apis-gw.beacukai.go.id/nle-oauth/v1/user/login` with
  `{"username","password"}`, headers `Content-Type`/`Accept: application/json` and
  `User-Agent: Mozilla/5.0`, timeout 30 s.
- Token = first non-empty `access_token` | `token` | `bearerToken`, found recursively. Expiry =
  `expires_in` | `expiresIn` seconds, **default 600**. Stored per user (UTC); the password is
  never stored.
- Status endpoint → `{loggedIn, expiresAt}`. The UI button shows a countdown and flips back at
  expiry. A send that returns 401 flips the button to logged-out.
- Errors: missing credentials → 400; HTTP error → same status + raw body; network → 500;
  no token found → 400.

---

## 8. Send to CEISA

1. No `id` → 400 "ID dokumen tidak ditemukan."
2. No valid token → **401** "Token tidak valid atau sudah expired. Silakan login ulang."
3. Doc not found → 404 "Dokumen tidak ditemukan."
4. Load barang, dokumen, and the 3 entities (by selectEntitas1/2/3) from the master.
5. Build the payload exactly as in §9.
6. `POST https://apis-gw.beacukai.go.id/openapi/document` with headers
   `Authorization: Bearer <token>`, `Content-Type: application/json`,
   `Accept: application/json`, `User-Agent: Mozilla/5.0`. **Timeout 60 s** (BC 2.6.1 uses 30).
7. Response handling:

| Case | Server reply to UI |
|---|---|
| HTTP error from CEISA (4xx/5xx) | same status, `{ message: "Pengiriman gagal.", error: <raw>, details: [...] }` |
| Network / timeout | 500, same shape |
| **Any 2xx** (JSON or not) | **set `sent = 1`** (no `status` check, §11 Q-b), 200 `{ message: "Data berhasil dikirim.", response: <parsed JSON, or {"raw": body} if not JSON> }` |

8. The `details` builder is identical to the BC 2.6.1 plan §8.8 (max 5 lines, `$…$`
   extraction, `[CODE:…]` stripped, 200-char cut, "+N lagi").
9. UI: success → toast the message and refresh the Transaction list. Error → toast the
   details lines (HTML-escaped, 15 s).

---

## 9. CEISA JSON payload — EXACT field order

> **Order matters:** CEISA rejects the payload when keys are out of order. Keys are listed
> **in emit order**. "Fallback" means: used when the value is null, "", 0 or 0.0. The
> **string "0" is not empty.** "no fallback" means the raw value is sent, possibly `null`.
> `float(...)` means the value is always emitted as a float (e.g. `0.0`, `120.0`).

### 9.1 Root object

| # | Key | JSON type | Value |
|---|---|---|---|
| 1 | asalData | string | `"S"` |
| 2 | asuransi | number (float) | float(header.asuransi, fallback 0) |
| 3 | bruto | number (float) | float(header.bruto, fallback 0) |
| 4 | cif | number (float) | float(header.cif, fallback 0) |
| 5 | fob | number (float) | float(header.fob, fallback 0) |
| 6 | freight | number (float) | float(header.freight, fallback 0) |
| 7 | hargaPenyerahan | number (float) | float(header.hargaPenyerahan, fallback 0) → always `0.0` |
| 8 | jabatanTtd | string/null | header.jabatanTtd (no fallback) |
| 9 | jumlahKontainer | number | `0` (always, even when a kontainer is sent) |
| 10 | kodeAsuransi | string | header.kodeAsuransi, fallback "" |
| 11 | kodeDokumen | string/null | header.kodeDokumen (`"23"`) (no fallback) |
| 12 | kodeIncoterm | string | header.kodeIncoterm, fallback "" |
| 13 | kodeKantor | string/null | header.kodeKantor (no fallback) |
| 14 | kodeKantorBongkar | string | fallback "" |
| 15 | kodePelBongkar | string | fallback "" |
| 16 | kodePelMuat | string | fallback "" |
| 17 | kodePelTransit | string | fallback "" |
| 18 | kodeTps | string | fallback "" |
| 19 | kodeTujuanTpb | string | fallback "" |
| 20 | kodeTutupPu | string | `"11"` (hardcoded) |
| 21 | kodeValuta | string | fallback "" |
| 22 | kotaTtd | string/null | no fallback |
| 23 | namaTtd | string/null | no fallback |
| 24 | ndpbm | number (float) | float(header.ndpbm, fallback 0) |
| 25 | netto | number (float) | float(header.netto, fallback 0), **not rounded** |
| 26 | nik | string | `"0"` |
| 27 | nilaiBarang | number (float) | float(**header.cif**, fallback 0) |
| 28 | nomorAju | string/null | no fallback |
| 29 | nomorBc11 | string/null | no fallback |
| 30 | posBc11 | string/null | no fallback |
| 31 | seri | number | `1` |
| 32 | **subposBc11** | string/null | header.subPosBc11 (note: **lower-case "p"** in the key) |
| 33 | tanggalBc11 | string | `YYYY-MM-DD`, fallback "" |
| 34 | tanggalTiba | string | header.tanggalTiba `YYYY-MM-DD`, fallback **`"2026-06-02"`** → in practice always that date (§11 Q-a) |
| 35 | tanggalTtd | string | `YYYY-MM-DD`, fallback "" |
| 36 | biayaTambahan | number (float) | fallback 0 → always `0.0` |
| 37 | biayaPengurang | number (float) | fallback 0 → always `0.0` |
| 38 | kodeKenaPajak | string | `"1"` (hardcoded) |
| 39 | barang | array | §9.4 |
| 40 | entitas | array | §9.2 (always 3) |
| 41 | kemasan | array | §9.5 (0 or 1) |
| 42 | kontainer | array | §9.6 (0 or 1) |
| 43 | dokumen | array | §9.3 |
| 44 | pengangkut | array | §9.7 (always 1) |

BC 2.6.1 differences to watch: there are **no** `disclaimer`, `jaminan`, `pungutan`,
`uangMuka`, `vd` or `tanggalAju` keys, and the root order is completely different.

### 9.2 `entitas[]`: always 3 objects, order seri 3 → 5 → 7

Slot filling: collect selectEntitas1, selectEntitas2, selectEntitas3 **in that order,
skipping empty ones**. The 1st found → seri 3, 2nd → seri 5, 3rd → seri 7 (§11 Q-d). Values
come from the **entity master** (§11 Q-e). Normal mapping: Entitas 1 Importir/Pengusaha TPB
→ 3, Entitas 2 Pemasok → 5, Entitas 3 Pemilik Barang → 7.

**Slot seri 3**

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | alamat ("") | "" |
| 2 | kodeEntitas | string | `"3"` | "3" |
| 3 | kodeJenisIdentitas | string | kodeJenisIdentitas ("6") | "6" |
| 4 | namaEntitas | string | nama ("") | "" |
| 5 | nibEntitas | string | NIB ("") | "" |
| 6 | nomorIdentitas | string | NPWP ("") | "" |
| 7 | nomorIjinEntitas | string | ("") | "" |
| 8 | tanggalIjinEntitas | string | `YYYY-MM-DD` ("") | "" |
| 9 | seriEntitas | number | `3` | 3 |

**Slot seri 5 (Pemasok)**

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | alamat ("") | "" |
| 2 | kodeEntitas | string | `"5"` | "5" |
| 3 | kodeNegara | string | the entity's **master** country code ("") | "" |
| 4 | namaEntitas | string | nama ("") | "" |
| 5 | seriEntitas | number | `5` | 5 |

**Slot seri 7**

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | alamat ("") | "" |
| 2 | kodeEntitas | string | `"7"` | "7" |
| 3 | kodeJenisApi | string | ("02") | "02" |
| 4 | kodeJenisIdentitas | string | ("6") | "6" |
| 5 | kodeStatus | string | ("0") | "0" |
| 6 | namaEntitas | string | ("") | "" |
| 7 | nomorIdentitas | string | ("") | "" |
| 8 | nomorIjinEntitas | string | ("") | "" |
| 9 | tanggalIjinEntitas | string | `YYYY-MM-DD` ("") | "" |
| 10 | seriEntitas | number | `7` | 7 |

### 9.3 `dokumen[]`: one per Dokumen row

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idDokumen | string | `"0"` |
| 2 | kodeDokumen | string/null | row.kodeDokumen |
| 3 | nomorDokumen | string/null | row.nomorDokumen |
| 4 | seriDokumen | number | row.seriDokumen |
| 5 | tanggalDokumen | string/null | `YYYY-MM-DD` or **null** |

### 9.4 `barang[]`: one per Barang row (seriBarang order)

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | idBarang | string | `"0"` |
| 2 | asuransi | number/null | row.asuransi (**no fallback**; = header asuransi at save) |
| 3 | cif | number/null | row.cif (no fallback, raw) |
| 4 | diskon | number | row.diskon (0) |
| 5 | fob | number | row.fob (0) (int, = header fob truncated) |
| 6 | freight | number | row.freight (0) (= header freight) |
| 7 | hargaEkspor | number | (0) |
| 8 | hargaPenyerahan | number | (0) |
| 9 | hargaSatuan | number | (0) |
| 10 | isiPerKemasan | number | (0) |
| 11 | jumlahKemasan | number/null | row.jumlahKemasan (no fallback) |
| 12 | jumlahSatuan | number/null | row.jumlahSatuan (no fallback) |
| 13 | kodeBarang | string/null | row.kodeBarang |
| 14 | kodeDokumen | string | `"23"` |
| 15 | kodeKategoriBarang | string/null | row (no fallback) |
| 16 | kodeJenisKemasan | string/null | row (no fallback) |
| 17 | kodeNegaraAsal | string/null | row (no fallback) |
| 18 | kodePerhitungan | string | row ("0") |
| 19 | kodeSatuanBarang | string/null | row |
| 20 | merk | string | row.merk (`"-"`) |
| 21 | netto | number/null | row.netto (no fallback, **not rounded**) |
| 22 | nilaiBarang | number | row.nilaiBarang (0) (= row cif) |
| 23 | nilaiTambah | number | **row.cif** (0) |
| 24 | posTarif | string/null | row.posTarif |
| 25 | seriBarang | number | row.seriBarang |
| 26 | spesifikasiLain | string | row ("0") |
| 27 | tipe | string | row ("0") |
| 28 | ukuran | string | row ("0") |
| 29 | uraian | string/null | row.uraian |
| 30 | ndpbm | number | row.ndpbm (0) |
| 31 | cifRupiah | number | round(row.cifRupiah, 2) |
| 32 | hargaPerolehan | number | (0) |
| 33 | kodeAsalBahanBaku | string | `"0"` |
| 34 | barangTarif | array | §9.4.1: exactly 3 objects |
| 35 | nilaiDiskon | number | `0` |
| 36 | volume | null | always `null` |
| 37 | nilaiJasa | null | always `null` (no such field on BC 2.3 barang) |
| 38 | persenBM | number/null | row.persenBM (raw, from the HS master) |
| 39 | persenPPN | number/null | row.persenPPN |
| 40 | persenPPh | number/null | row.persenPPh |
| 41 | barangDokumen | array | `[]` |

There are **no** duplicate keys in this object (unlike BC 2.6.1).

#### 9.4.1 `barang[].barangTarif[]`: 3 objects, in this order

> ⚠ The key order differs between objects. In BM, `kodeJenisPungutan` comes *before*
> `kodeSatuanBarang`. In PPH and PPN it comes *after*. In BC 2.3, `jumlahSatuan` is
> **row.netto unrounded in all three** (BC 2.6.1 rounds BM and PPN).

**[0] BM**

| # | Key | Type | Value |
|---|---|---|---|
| 1 | kodeJenisTarif | string | `"1"` |
| 2 | jumlahSatuan | number | row.netto (0) |
| 3 | kodeFasilitasTarif | string | `"3"` |
| 4 | kodeJenisPungutan | string | `"BM"` |
| 5 | kodeSatuanBarang | string/null | row.kodeSatuanBarang |
| 6 | nilaiBayar | number | round(row.cifRupiah, 2) (0) |
| 7 | nilaiFasilitas | number | `0` |
| 8 | nilaiSudahDilunasi | number | `0` |
| 9 | seriBarang | number | `1` |
| 10 | tarif | number | `40` |
| 11 | tarifFasilitas | number | `100` |

**[1] PPH**

| # | Key | Type | Value |
|---|---|---|---|
| 1 | kodeJenisTarif | string | `"1"` |
| 2 | jumlahSatuan | number | row.netto (0) |
| 3 | kodeFasilitasTarif | string | `"6"` |
| 4 | kodeSatuanBarang | string/null | row.kodeSatuanBarang |
| 5 | kodeJenisPungutan | string | `"PPH"` |
| 6 | nilaiBayar | number | round(row.cifRupiah, 2) (0) |
| 7 | nilaiFasilitas | number | `0` |
| 8 | nilaiSudahDilunasi | number | `0` |
| 9 | seriBarang | number | `2` |
| 10 | tarif | number | `2.5` |
| 11 | tarifFasilitas | number | `100` |

**[2] PPN**

| # | Key | Type | Value |
|---|---|---|---|
| 1 | kodeJenisTarif | string | `"1"` |
| 2 | jumlahSatuan | number | row.netto (0) |
| 3 | kodeFasilitasTarif | string | `"6"` |
| 4 | kodeSatuanBarang | string/null | row.kodeSatuanBarang |
| 5 | kodeJenisPungutan | string | `"PPN"` |
| 6 | nilaiBayar | number | round(row.cifRupiah, 2) (0) |
| 7 | nilaiFasilitas | number | `0` |
| 8 | nilaiSudahDilunasi | number | `0` |
| 9 | seriBarang | number | `3` |
| 10 | tarif | number | `11` |
| 11 | tarifFasilitas | number | `100` |

### 9.5 `kemasan[]`: 0 or 1 object

Included **only if** header kodeJenisKemasan **or** jumlahKemasan is non-empty/non-zero.
Otherwise `[]`.

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | jumlahKemasan | number(int) | header.jumlahKemasan (0) |
| 2 | kodeJenisKemasan | string | header.kodeJenisKemasan ("") |
| 3 | seriKemasan | number | header.seriKemasan (`1`) → always 1 |
| 4 | merkKemasan | string | header.merkKemasan ("") |

### 9.6 `kontainer[]`: 0 or 1 object

Included **only if** header nomorKontainer is non-empty. Otherwise `[]`.

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | seriKontainer | number | `1` |
| 2 | kodeTipeKontainer | string | header.kodeTipeKontainer ("") |
| 3 | kodeUkuranKontainer | string **or number** | header.kodeUkuranKontainer, fallback **number `0`** (§11 Q-h) |
| 4 | nomorKontainer | string | header.nomorKontainer ("") |
| 5 | kodeJenisKontainer | string | header.kodeJenisKontainer ("") |

### 9.7 `pengangkut[]`: exactly 1 object

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | kodeBendera | string | header.kodeBendera ("") |
| 2 | namaPengangkut | string | header.namaPengangkut ("") |
| 3 | nomorPengangkut | string | header.nomorPengangkut ("") |
| 4 | kodeCaraAngkut | string | header.kodeCaraAngkut ("") |
| 5 | seriPengangkut | number | header.seriPengangkut (`0`) → always 0 |

(There is no `idPengangkut` key, unlike BC 2.6.1.)

### 9.8 Skeleton example (values illustrative)

```json
{
  "asalData": "S", "asuransi": 0.0, "bruto": 1250.5, "cif": 18000.0, "fob": 17000.0,
  "freight": 1000.0, "hargaPenyerahan": 0.0, "jabatanTtd": "Manager", "jumlahKontainer": 0,
  "kodeAsuransi": "LN", "kodeDokumen": "23", "kodeIncoterm": "CIF", "kodeKantor": "071100",
  "kodeKantorBongkar": "070100", "kodePelBongkar": "IDSUB", "kodePelMuat": "DEHAM",
  "kodePelTransit": "", "kodeTps": "TPS01", "kodeTujuanTpb": "1", "kodeTutupPu": "11",
  "kodeValuta": "USD", "kotaTtd": "Jember", "namaTtd": "Budi", "ndpbm": 16000.0,
  "netto": 1200.0, "nik": "0", "nilaiBarang": 18000.0, "nomorAju": "000023...",
  "nomorBc11": "001234", "posBc11": "0001", "seri": 1, "subposBc11": "0001",
  "tanggalBc11": "2026-10-20", "tanggalTiba": "2026-06-02", "tanggalTtd": "2026-10-26",
  "biayaTambahan": 0.0, "biayaPengurang": 0.0, "kodeKenaPajak": "1",
  "barang": [
    {
      "idBarang": "0", "asuransi": 0.0, "cif": 18000.0, "diskon": 0, "fob": 17000,
      "freight": 1000.0, "hargaEkspor": 0, "hargaPenyerahan": 0, "hargaSatuan": 0,
      "isiPerKemasan": 0, "jumlahKemasan": 20.0, "jumlahSatuan": 1200.0,
      "kodeBarang": "TOB-01", "kodeDokumen": "23", "kodeKategoriBarang": "11",
      "kodeJenisKemasan": "BL", "kodeNegaraAsal": "", "kodePerhitungan": "0",
      "kodeSatuanBarang": "KGM", "merk": "-", "netto": 1200.0, "nilaiBarang": 18000.0,
      "nilaiTambah": 18000.0, "posTarif": "24011010", "seriBarang": 1,
      "spesifikasiLain": "0", "tipe": "0", "ukuran": "0", "uraian": "TOBACCO LEAF",
      "ndpbm": 16000.0, "cifRupiah": 288000000.0, "hargaPerolehan": 0,
      "kodeAsalBahanBaku": "0",
      "barangTarif": [ { "kodeJenisTarif": "1", "...": "BM" }, { "...": "PPH" }, { "...": "PPN" } ],
      "nilaiDiskon": 0, "volume": null, "nilaiJasa": null,
      "persenBM": 5.0, "persenPPN": 11.0, "persenPPh": 2.5, "barangDokumen": []
    }
  ],
  "entitas": [ { "alamatEntitas": "...", "kodeEntitas": "3", "...": "..." }, { "kodeEntitas": "5", "...": "..." }, { "kodeEntitas": "7", "...": "..." } ],
  "kemasan": [ { "jumlahKemasan": 20, "kodeJenisKemasan": "BL", "seriKemasan": 1, "merkKemasan": "" } ],
  "kontainer": [ { "seriKontainer": 1, "kodeTipeKontainer": "1", "kodeUkuranKontainer": "40", "nomorKontainer": "MSKU1234567", "kodeJenisKontainer": "8" } ],
  "dokumen": [ { "idDokumen": "0", "kodeDokumen": "380", "nomorDokumen": "INV-1", "seriDokumen": 1, "tanggalDokumen": "2026-10-01" } ],
  "pengangkut": [ { "kodeBendera": "DE", "namaPengangkut": "MAERSK X", "nomorPengangkut": "V123", "kodeCaraAngkut": "1", "seriPengangkut": 0 } ]
}
```

---

## 10. Laravel implementation notes (payload fidelity)

**Identical to the BC 2.6.1 plan §10.** The ones that matter most here:
- Build the payload with hand-written PHP arrays in §9 order. Never use `toArray()`,
  Resources or `array_merge`.
- Call `array_values()` / `->values()` on every list.
- Encode with `json_encode($p, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES)` and send
  the raw body via `withBody(...)`. **Many BC 2.3 root fields are always floats** (`0.0`), so
  `JSON_PRESERVE_ZERO_FRACTION` matters even more here.
- **Keep raw nulls** where §9 says "no fallback" (e.g. barang `asuransi`, `cif`, `netto`, and
  root `nomorBc11`). Don't silently turn them into 0 or "".
- Falsy-fallback helper: only null, `""`, `0`, `0.0` trigger the fallback, **not** the string
  `"0"`.
- `kodeUkuranKontainer`: emit the stored string, or the number `0` if empty (copy the type
  mismatch, or fix it per §11 Q-h).
- Rounding difference at .xx5 (Python vs PHP `round`). Dates are `Y-m-d`.
- Keep `User-Agent: Mozilla/5.0`. Send timeout 60 s.

---

## 11. Known quirks (replicated as-is; suggested fixes for later)

| ID | Quirk (current behaviour) | Suggested fix |
|---|---|---|
| **Q-a** | **`tanggalTiba` has no input and is never saved, so every send uses the hardcoded date `"2026-06-02"`.** | Add a Tanggal Tiba field (required) and send it. |
| **Q-b** | **`sent = 1` on any HTTP 2xx, even if CEISA's body reports a rejection.** The message is always "Data berhasil dikirim." and `idHeader` is not stored. | Check `status == "OK"` like BC 2.6.1, surface the CEISA message, and store idHeader. |
| Q-c | `barangTarif` is hardcoded to BM 40% / PPh 2.5% / PPN 11%, with nilaiBayar = full CIF Rupiah and jumlahSatuan = netto. The UI-computed nilaiBM/PPN/PPh are ignored. | Use the HS rates and the computed amounts. |
| Q-d | Entitas slots are filled by "next non-empty", so a missing Entitas 1 shifts the Pemasok into the seri 3 slot. | Fixed mapping E1→3, E2→5, E3→7. |
| Q-e | Send reads entities from the **master**, ignoring the typed or saved NPWP/nama/alamat. The seri 5 country comes from the master, not from `kodeNegaraEntitas2`. | Send the document snapshot. |
| Q-f | A sent document can still be edited, deleted and re-sent. | Lock edit/delete when `sent = 1`. |
| Q-g | Every barang row gets the **header** fob/asuransi/freight totals (barang fob is also truncated to an integer). | Allocate per row, or send 0. |
| Q-h | `jumlahKontainer` is 0 even when a kontainer is sent. The `kodeUkuranKontainer` fallback is the number `0` (a type mismatch). | Send 1 or 0 to match. Use the "" fallback. |
| Q-i | Hardcoded or never-set values: `kodeTutupPu "11"`, `kodeKenaPajak "1"`, `seriPengangkut 0`, `seriKemasan 1`, `hargaPenyerahan/biaya* 0.0`. | Confirm with CEISA what is required. |
| Q-j | Source B pre-fills cifRupiah with **unit_price** (not amount). It is overwritten as soon as any CIF or NDPBM is entered. | Use amount, or drop the pre-fill. |
| Q-k | Source A pre-fill expects kodeNegaraAsal, but the view doesn't provide it, so it's always "". | Add the country of origin to the source lines. |
| Q-l | Barang merk/tipe/ukuran/spesifikasiLain/keteranganDokumen have no input in the grid, so CEISA always gets `"-"`/`"0"`. | Add the inputs. |
| Q-m | The HS rate text shows "PPH" with persenPPN and "PPN" with persenPPh (display only). | Swap the labels. |
| Q-n | Source B create sets `no_reg_bc` but not `tgl_bc` (edit sets both). Delete doesn't clear the source's `no_reg_bc`/`tgl_bc`. | Set both on create, and clear both on delete. |
| Q-o | Barang `kodeDokumen` is saved null on create and "23" on edit (send hardcodes "23" anyway). | Always "23". |
| Q-p | Create, update and delete are not atomic (commit per step). | One transaction. |
| Q-q | No standalone BC 2.3 (Add needs a source; the Transaction-tab Add path is missing). | Keep it, or add a blank create. |
| Q-r | `kodeKantor`, `kodeDokumen`, `nomorAju`, `nomorBc11`, `posBc11`, `subposBc11`, `*Ttd` are sent without a fallback, so they can be `null`. | Require them, or fall back to "". |
| Q-s | After an edit, only the Source A Outstanding list is refreshed (not B). | Refresh all lists. |
| Q-t | On Edit load, header CIF Rupiah is blank until NDPBM or CIF is touched (it isn't stored). | Compute it on load. |

---

## 12. Open items for the database session (Laravel codebase)

- The concrete tables for Source A (goods packing list + its aggregated-goods query) and
  Source B (sparepart packing list), and where `posted`, `bc_type`, `ready_sent`,
  `no_reg_bc`, `tgl_bc` and the warning flag live.
- How to store the source link (two nullable FKs + `source_type`, as in Villiger, or a
  polymorphic relation).
- Header table (kemasan/kontainer/pengangkut as columns), Barang and Dokumen child tables, and
  the delete strategy.
- Masters: entity, port, country, incoterms, container, packaging, HS, kategori, satuan,
  jenis dokumen. Reuse the CEISA token table from the other BC modules.
- The "working period" concept (used by the Outstanding and Transaction filters) and the
  per-route permissions.

---

## Appendix A: code lists used by the UI

- **kodeTujuanTpb:** 1 Kawasan Berikat, 2 Gudang Berikat, 3 TPPB, 4 TBB, 5 TLB, 6 KDUB, 7 Lainnya.
- **kodeCaraAngkut:** 1 Laut *(default)*, 2 Kereta Api, 3 Darat, 4 Udara, 5 Pos, 6 Multimoda,
  7 Instalasi/Pipa, 8 Perairan, 9 Lainnya.
- **kodeAsuransi:** LN (luar negeri, default), DN (dalam negeri).
- **kodeUkuranKontainer:** 20, 40, 45, 60 (feet).
- **kodeTipeKontainer:** 1 General/Dry Cargo, 2 Tunnel Type, 3 Open Top Steel, 4 Flat Rack,
  5 Reefer, 6 Barge Container, 7 Bulk Container, 8 Isotank, 99 Lain-lain.
- **kodeJenisKontainer:** 4 Empty, 7 LCL, 8 FCL.
- **Dokumen rows 1–2 allowed types:** row 1 = 380 (Invoice); row 2 = 705 / 740 (BL / AWB).
  Shortcut codes: 380 Invoice, 705/740 BL-AWB, 999 Surat Keputusan.
- **Kantor defaults (company-specific, make configurable):** kodeKantor 071100 (KPPBC TMP C
  Jember), kodeKantorBongkar 070100 (KPPBC TMP Tanjung Perak).
