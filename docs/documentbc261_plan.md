# Document BC 2.6.1 — Functional Plan for Re-implementation (Laravel 12)

Source of truth: the Villiger ERP `document_bc261` module (the "v2" JSON path, which is the
only live path). This plan describes **behaviour, rules and the exact CEISA payload**, not
code. The database design is out of scope and will be decided in the Laravel codebase
session; everything below uses *logical* field names.

Decisions already made:
- **Fidelity:** copy current behaviour exactly, since that is what CEISA accepts today.
  Deviations are listed in §11 *Known quirks* with suggested fixes, to be decided later.
- **Outstanding:** a generic *source document* contract (§3), not tied to a specific table.
- **CEISA auth:** a per-user login modal, with the token stored per user (§7).
- **Scope:** backend, the CEISA payload, and UI behaviour (not visual design).

---

## 1. Overview

- BC 2.6.1 = customs document for sending goods **out of a bonded zone temporarily**
  (repair, subcontract, loan, return, other). Here it is used for spareparts.
- Lifecycle: **Draft (saved)** → **Sent** (`sent = 1` after CEISA answers `status: "OK"`).
- A document can be created:
  1. **From an Outstanding source document**: the barang lines are pre-filled and the source
     is linked and marked consumed.
  2. **Standalone**: a blank form, typed from scratch.
- Every document owns 6 child collections: **Barang**, **Bahan Baku** (each row belongs to one
  barang), **Kemasan**, **Kontainer (Peti Kemas)**, **Jaminan**, **Dokumen**.

---

## 2. Logical data model (persisted fields)

### 2.1 Document header

| Field | Type | Required | Notes |
|---|---|---|---|
| id | int | — | |
| source_document_id | int, nullable | no | The Outstanding source this doc was created from. NULL = standalone. |
| sent | int (0/1), nullable | no | Set to 1 only by a successful CEISA send. |
| nomorAju | string(≤26 in UI, 100 in DB) | NOT NULL → "" | Submission number. |
| kodeKantor | string(20) | NOT NULL → "" | Customs office. UI default `071100` (KPPBC Jember). **Make this a config/company setting.** |
| kodeTujuanPengiriman | string(20) | NOT NULL → "" | Select 1–5 (Appendix A). |
| kodeValuta | string(20) | NOT NULL → "" | Currency code, e.g. USD. |
| ndpbm | decimal | 0 | Exchange rate. |
| cif | decimal | 0 | CIF in foreign currency. |
| cifRupiah | — | — | **Not persisted.** Display only: `ndpbm × cif` (see §11 Q-h). |
| bruto | decimal | 0 | Gross weight (kg). Typed manually. |
| netto | decimal | 0 | Net weight (kg). Typed manually. |
| kodeCaraAngkut | string(20) | NOT NULL → "" | Select 1–12 (Appendix A), default 3 (DARAT). |
| no_reg_bc, tgl_bc, tgl_keluar | string / date / date | nullable | **Internal only — never sent to CEISA.** |
| Entitas 1 (Pengusaha TPB): nomorIdentitasEntitas1, namaEntitas1, alamatEntitas1, nomorIjinEntitas1, tanggalIjinEntitas1 | strings + date | nama/alamat NOT NULL → "" | Snapshot typed or filled from the entity master. |
| Entitas 2 (Pemilik Barang): nomorIdentitasEntitas2, namaEntitas2, alamatEntitas2 | strings | nama/alamat NOT NULL → "" | |
| Entitas 3 (Penerima Barang): nomorIdentitasEntitas3, namaEntitas3, alamatEntitas3 | strings | nama/alamat NOT NULL → "" | |
| selectEntitas1 / 2 / 3 | int, nullable | no | **FK to the entity master. This, not the snapshot above, is what gets sent** (§11 Q-f). |
| kotaTtd, tanggalTtd, namaTtd, jabatanTtd | string / date / string / string | kota/nama/jabatan NOT NULL → "" | Signature block ("Pernyataan"). `tanggalTtd` is also sent as `tanggalAju`. |

Legacy single-value header columns for kemasan/kontainer/jaminan/dokumen1-3 exist in Villiger
but are dead (the child tables replaced them). **Do not recreate them.**

### 2.2 Child: Barang (goods line)

`seriBarang` = 1-based position in the list, assigned **by the server** at save time.

| Field | Type | Default on save | Shown in UI modal? |
|---|---|---|---|
| seriBarang | int | position | read-only |
| posTarif | string | "" | yes (HS dropdown) |
| kodeBarang | string(20) | "" | yes |
| uraian | string(100) | "" | yes |
| merk, tipe, ukuran, spesifikasiLain | string, nullable | null | yes |
| kodeSatuanBarang | string | "" | yes (unit dropdown) |
| jumlahSatuan | decimal | 0 | yes |
| jumlahKemasan | decimal | 0 | yes |
| kodeJenisKemasan | string, nullable | null | yes (packaging dropdown) |
| kodeDokumen | string | always `"261"` | no |
| netto | decimal | 0 | yes |
| cif | decimal | 0 | yes, but the input is **disabled** → effectively always 0 (§11 Q-g) |
| cifRupiah | decimal | 0 | yes ("Nilai CIF") |
| ndpbm | decimal | 0 | no → always 0 |
| kodeNegaraAsal | string | "" | yes (country dropdown) |
| kodeKategoriBarang | string, nullable | null | yes ("Daerah Asal Barang") |
| keteranganDokumen | string | "" | no |
| persenBM, persenPPN, persenPPh, nilaiBM, nilaiPPN, nilaiPPh | decimal | 0 | no → always 0 |
| hargaPenyerahan, hargaPerolehan, hargaEkspor, hargaSatuan, diskon, freight, isiPerKemasan, kodePerhitungan | nullable | null (never set) | no |

### 2.3 Child: Bahan Baku (raw material used by a barang)

| Field | Type | Default on save | Notes |
|---|---|---|---|
| seriBarang | int | client value, else row index | Links the row to the owning barang's `seriBarang`. |
| seriBahanBaku | int | client value, else **global** row index | User-typed in the modal. |
| kodeAsalBahanBaku | string, nullable | null | `"0"` = Impor, `"1"` = Lokal. |
| posTarif, kodeBarang, uraianBarang | string | "" | |
| merkBarang, tipeBarang, ukuranBarang, spesifikasiLainBarang | string, nullable | null | |
| kodeSatuanBarang | string | "" | |
| jumlahSatuan | decimal | 0 | |
| kodeDokumen | string | `"261"` if empty | **Meaning: origin document code** (23 / 27 / 40), Appendix A. |
| kodeKantorAsal | string | "" | Origin customs office. |
| nomorDaftarDokAsal | string | "" | Origin registration number. |
| tanggalDaftarDokAsal | date | null | |
| nomorAjuDokAsal | string, nullable | null | Stored but **not sent**. |
| seriBarangDokAsal | int, nullable | null | Stored but **not sent** (send hardcodes 1, §11 Q-i). |
| cif, cifRupiah, hargaPenyerahan, hargaPerolehan, nilaiJasa | decimal | 0 | |
| netto, ndpbm, seriIjin | nullable | always null | |

### 2.4 Child: Kemasan (packaging)

| Field | Type | Default |
|---|---|---|
| seriKemasan | **string** | position as string ("1", "2", …) |
| jumlahKemasan | int | 0 |
| kodeJenisKemasan | string, nullable | null |
| merkKemasan | string | "" |

### 2.5 Child: Kontainer (Peti Kemas)

| Field | Type | Default |
|---|---|---|
| seriKontainer | int | position |
| nomorKontainer | string, nullable | null |
| kodeUkuranKontainer | string | 20/40/45/60 |
| kodeJenisKontainer | string | 4 Empty / 7 LCL / 8 FCL ("Jenis Muatan") |
| kodeTipeKontainer | string | 1–8, 99 |

> Saved and loaded, but **never sent to CEISA** (§11 Q-a).

### 2.6 Child: Jaminan (guarantee)

| Field | Type | Default |
|---|---|---|
| kodeJenisJaminan | string, nullable | 1–8 (Appendix A) |
| nomorJaminan | string, nullable | |
| tanggalJaminan | date, nullable | |
| nilaiJaminan | **int**, nullable | decimals are truncated |
| tanggalJatuhTempo | date, nullable | |
| penjamin | string, nullable | |
| nomorBpj | string, nullable | |
| tanggalBpj | date, nullable | |

### 2.7 Child: Dokumen (supporting documents)

| Field | Type | Default |
|---|---|---|
| seriDokumen | int | position |
| kodeDokumen | string | `"261"` if empty (picked from the document-type master) |
| nomorDokumen | string, nullable | |
| tanggalDokumen | date, nullable | |

### 2.8 CEISA token (per user)

| Field | Notes |
|---|---|
| user_id | unique — one row per user |
| bearer_token | text |
| expires_at | datetime (UTC) |
| created_at / updated_at | |

In Villiger **one token table is shared by every BC module** (2.3, 2.5, 2.6.1, 2.6.2, 4.0,
4.1), so logging in once covers all of them. Recommended: do the same.

### 2.9 Reference / master data the module needs

- Entity master (Entitas): namaEntitas, alamatEntitas, nomorIdentitas (NPWP), nibEntitas,
  nomorIjinEntitas, tanggalIjinEntitas, kodeJenisApi, kodeJenisIdentitas, kodeStatus,
  country → kodeNegara.
- HS / Pos Tarif list (`posTarif`).
- Country list (`kodeNegara`).
- Kategori Barang list ("Daerah Asal Barang").
- Jenis Satuan (unit codes).
- Jenis Kemasan (packaging codes).
- Jenis Dokumen (document-type codes).

---

## 3. Outstanding (source document contract)

The Outstanding tab lists source documents that may become a BC 2.6.1.

**A source document must provide:**
- Header: `id`, `doc_number` (shown as "No. Bukti"), `doc_date` ("Tanggal Dokumen"),
  `local_or_import` ("Lokal/Import"), `posted` flag, `ready_sent` flag (0/1).
- Lines: `item_code`, `description`, `qty`, `unit`, `unit_price`, `amount`.

**Eligibility query (exact current rule):**
- `posted = 1`, and
- **no** BC 2.6.1 document exists with `source_document_id = this.id`.
- No period filter. A BC-type filter exists in Villiger but is commented out.
- Order: newest first (id desc).

**State changes on the source:**
- Create a BC 2.6.1 with a `source_document_id` → set the source's `ready_sent = 1`.
- Delete that BC 2.6.1 → set `ready_sent = 0`. The source then shows up in Outstanding
  again, because nothing references it any more.
- Edit never changes `source_document_id`.

**Pre-fill mapping (source line → Barang row)**, one barang per line, in line order:

| Barang field | From |
|---|---|
| seriBarang | position (1..n) |
| kodeBarang | line.item_code |
| uraian | line.description |
| kodeSatuanBarang | line.unit |
| jumlahSatuan | line.qty |
| cifRupiah | line.amount |
| cif, netto, jumlahKemasan | 0 |
| posTarif, merk, tipe, ukuran, spesifikasiLain, kodeNegaraAsal, kodeKategoriBarang, kodeJenisKemasan | "" (user completes) |

Nothing else is pre-filled: header, entitas, kemasan and the other tabs stay at the form defaults.

---

## 4. Screens & UI behaviour

### 4.1 Main page

- Permission gate: no "view" access → redirect to home.
- Toolbar: **Detail**, **Add**, **Edit**, **Delete**, plus a **Login BC** button in the tab bar.
- Two tabs:
  - **Outstanding**: a table of eligible source documents with columns No. Bukti,
    Tanggal Dokumen (dd-mm-yyyy), Lokal/Import. DataTable with search, sort and paging
    (10/25/50/100/All), default sort column 0 desc.
  - **Transaction**: every BC 2.6.1 document, newest first, with **no period filter**. Columns:
    - **Rev. Doc.** = the source's doc_number if linked, otherwise `nomorAju`.
    - **Nomor AJU**.
    - **Status**: `sent = 1` → green "SENT ✓"; otherwise a **Send Data** button. The button
      must not trigger row selection, and it must work after a list refresh too (§11 Q-o).
- Row selection: click highlights the row (yellow). Clicking the **selected Outstanding row
  again unselects it**. Selecting in one tab clears the selection in the other.
- Button visibility: on Outstanding, Edit and Delete are hidden. On Transaction they are
  shown. **Add shows on both tabs.**

### 4.2 Toolbar actions

| Action | Outstanding tab | Transaction tab |
|---|---|---|
| Detail | Read-only preview modal of the selected source (header + lines). Error toast if nothing is selected. | Opens the document form loaded with the doc (currently **editable**, same as Edit). |
| Add | Opens a blank form. **If an Outstanding row is selected, the Barang tab is pre-filled from it** (§3) and the source id is remembered for save. | Opens a blank form. |
| Edit | hidden | Loads the selected doc into the form in edit mode. Error toast if nothing is selected. |
| Delete | hidden | Confirm dialog → delete (§5.4). |
| Send Data (row button) | — | §8. |

After any successful save or delete: close the modal, refresh both lists, and re-select the
saved doc.

### 4.3 Document form (one modal, 10 tabs)

| Tab | Content |
|---|---|
| Header | nomorAju, kodeKantor (default 071100), kodeTujuanPengiriman (select), no_reg_bc, tgl_bc, tgl_keluar |
| Entitas | 3 blocks: **Pengusaha TPB** (NPWP, entity select, alamat, nomor + tanggal izin TPB), **Penerima Barang** (NPWP, select, alamat), **Pemilik Barang** (NPWP, select, alamat). Choosing an entity fills NPWP, nama (hidden), alamat, nomor/tanggal izin from the master. Each select defaults to the first entity. |
| Dokumen | Dokumen collection |
| Pengangkut | kodeCaraAngkut (select, default 3) |
| Kemasan | Kemasan collection + Peti Kemas collection (each with a total count) |
| Transaksi | kodeValuta, ndpbm, cif, **cifRupiah (read-only = ndpbm × cif, recalculated when ndpbm or cif changes)**, bruto, netto |
| Barang | Barang collection + two Bahan Baku panels (Impor / Lokal) |
| Jaminan | Jaminan collection |
| Pungutan | Display only. Its "Generate" button is **not wired**, and nothing from this tab is saved or sent (§11 Q-c). |
| Pernyataan | kotaTtd, tanggalTtd, namaTtd, jabatanTtd (plain inputs, no defaults) |

Form open behaviour:
- **Add:** reset the header to its initial defaults (kodeKantor, first entity per select,
  default selects). It does not blank everything. Empty all collections and clear the barang
  selection.
- **Edit:** load the header + all 6 collections, and clear the barang selection.

### 4.4 Collection editors (all client-side)

- Each collection is an **in-memory list**. "Tambah" opens a sub-modal with empty fields. The
  pencil opens it pre-filled. The sub-modal's Simpan adds or replaces the row locally and
  re-renders the table. The trash icon removes it locally. **Nothing hits the server until the
  document-level Save.**
- The row number shown is the position (1..n). That position becomes `seri*` on save.
- Dropdown contents (HS, country, kategori, unit, packaging, document type) are loaded from
  the masters when the sub-modal opens.

### 4.5 Barang ↔ Bahan Baku

- Bahan baku is **one list for the whole document**. Each row carries `seriBarang` (the owning
  barang's position) and `kodeAsalBahanBaku` ("0" = Impor, "1" = Lokal).
- Click a barang row to select it (highlight). Click it again to deselect. The pencil on a
  barang also selects it.
- The Impor panel shows bahan baku rows where `kodeAsalBahanBaku = "0"` and
  `seriBarang = selected position`. The Lokal panel does the same with "1". If no barang is
  selected, both panels show "Pilih barang untuk melihat bahan bakunya."
- Adding bahan baku with no barang selected → error toast "Pilih barang terlebih dahulu."
  Otherwise the sub-modal opens with origin and seriBarang preset and the title
  "Bahan Baku Impor|Lokal - Barang <kode> - <uraian>".
- The sub-modal has "Simpan" (save and close) and "Tambah" (save and reopen for the next row
  with the same origin).

---

## 5. Server-side rules

### 5.1 Save payload (UI → server), one JSON body

```
{
  "id": <edit only>,
  "source_document_id": <int|null>,     // create only
  "header":    { ...header fields (§2.1) },
  "barang":    [ {...}, ... ],
  "bahanbaku": [ {...}, ... ],
  "kemasan":   [ {...}, ... ],
  "kontainer": [ {...}, ... ],
  "jaminan":   [ {...}, ... ],
  "dokumen":   [ {...}, ... ]
}
```

(Internal contract: key names may follow the Laravel app's conventions.)

### 5.2 Normalisation (applies to header and children)

- **Decimals:** empty, null or garbage → `0`. Strip thousands separators (`"2,500,000.00"` → 2500000).
- **Integers:** empty or garbage → `null`. Parse via float then truncate (`"3.0"` → 3).
- **Dates:** `YYYY-MM-DD` → date; empty or invalid → `null`.
- **NOT NULL strings:** empty → `""`, never null.
- `seri*` for barang, kemasan, kontainer and dokumen = **position in the submitted array,
  assigned by the server**. Client values are ignored.
- Validation is intentionally minimal: every field is optional, with no required-field
  checks on the document.

### 5.3 Create

1. Create the header with `sent = null`/0 and `source_document_id`.
2. Insert all children.
3. If `source_document_id` is set → `source.ready_sent = 1`.
4. **One DB transaction.** Response: `{ id, message: "Data telah ditambahkan." }`.

### 5.4 Update

1. Find the doc; if missing → 404 `{ errors: { id: "Document not found." } }`.
2. Overwrite the header fields (not `source_document_id`, not `sent`).
3. **Delete all 6 child collections and re-insert them** from the payload (full replace).
4. One transaction. Response: `{ id, message: "Data telah diubah." }`.

### 5.5 Delete

1. Delete all 6 child collections, then the header.
2. If the doc had a source → `source.ready_sent = 0`.
3. One transaction. Response: `{ message: "Data telah dihapus." }`.

### 5.6 Read endpoints

- Outstanding list (JSON) and Transaction list (JSON: id, rev_doc, nomorAju, sent).
- Show doc: the header plus each child collection. One combined response is fine.
  Dates go out as `YYYY-MM-DD` (empty string when null) so date inputs accept them.
- Source preview: header + lines.

---

## 6. Endpoint list (logical)

| Method | Purpose |
|---|---|
| GET | Page (renders both lists and the form defaults) |
| GET | Outstanding list (JSON) |
| GET | Transaction list (JSON) |
| GET | Show document + children (JSON) |
| GET | Source document preview (JSON) |
| POST | Create |
| PUT/PATCH | Update |
| DELETE | Delete |
| GET | CEISA token status → `{ loggedIn, expiresAt? }` |
| POST | CEISA login `{ username, password }` |
| POST | Send document `{ id }` |

---

## 7. CEISA authentication (per user)

**Login**
- `POST https://apis-gw.beacukai.go.id/nle-oauth/v1/user/login`
- Headers: `Content-Type: application/json`, `Accept: application/json`,
  `User-Agent: Mozilla/5.0` (keep it; the gateway may reject requests without it).
- Body: `{"username": "...", "password": "..."}`. Timeout 30 s.
- Missing username or password → 400 "Username dan password harus diisi."
- HTTP error → return the same status with `{ message: "Login gagal.", error: <raw body> }`.
  Network error → 500.
- Response parsing: search the JSON **recursively** for the first non-empty value of
  `access_token` | `token` | `bearerToken`. If none → 400 "Token tidak ditemukan di response."
- Expiry: search recursively for `expires_in` | `expiresIn` (seconds). **Default 600 s** if
  missing or not numeric. `expires_at = now(UTC) + expires_in`.
- Upsert the token row for the current user. Response:
  `{ message: "Login berhasil.", expiresAt: ISO8601 }`.
- **Passwords are never stored.**

**Status**
- No row, or `expires_at <= now(UTC)` → `{ loggedIn: false }`. Otherwise
  `{ loggedIn: true, expiresAt }`.

**UI**
- On page load, call status. If logged in, the Login BC button shows a live countdown to
  expiry, and at expiry it flips back to "Login BC".
- Login BC opens a modal (username, password). On success, start the countdown.
- Any send that returns 401 flips the button back to logged-out.

---

## 8. Send to CEISA

1. No `id` → 400 "ID dokumen tidak ditemukan."
2. No valid token for the current user (missing or expired) → **401**
   "Token tidak valid atau sudah expired. Silakan login ulang."
3. Doc not found → 404 "Dokumen tidak ditemukan."
4. Load all children and the 3 entities (by `selectEntitas1/2/3`) from the master.
5. Build the payload exactly as in §9.
6. `POST https://apis-gw.beacukai.go.id/openapi/document` with headers
   `Authorization: Bearer <token>`, `Content-Type: application/json`,
   `Accept: application/json`, `User-Agent: Mozilla/5.0`. Timeout 30 s.
7. Response handling:

| Case | Server reply to UI |
|---|---|
| HTTP error from CEISA | same status code, `{ message: "Pengiriman gagal.", error: <raw>, details: [...] }` |
| Network / timeout | 500, same shape |
| Body is not JSON | 502 `{ message: "Response tidak valid.", raw, details }` |
| JSON with `status == "OK"` | **set `sent = 1`**, 200 `{ message: <CEISA message or "Data berhasil dikirim.">, idHeader }` |
| JSON with other status | 400 `{ message: "Pengiriman gagal.", details, response }`. `sent` is unchanged. |

8. **`details` builder** (turns a CEISA error into short display lines):
   - Parse the body as JSON. If it isn't JSON → return `[raw trimmed to 200 chars + "..."]`.
   - Take `message`. If it's a string, wrap it in a list. If it isn't a list → return
     `[json string trimmed to 200 chars]`.
   - For each non-empty string: skip lines starting with "lihat referensi kode error"
     (case-insensitive). If the line contains two `$` signs, keep only the text between the
     first and last `$`. Otherwise strip a leading `$` and a trailing `[CODE:...]`. Then trim
     to 200 chars.
   - Keep at most **5** lines. If there are more, append `"+N lagi"`.
9. UI: on success → toast the message and refresh the Transaction list. On error → toast the
   details lines joined with line breaks (HTML-escaped), timeout 15 s.

---

## 9. CEISA JSON payload — EXACT field order

> **Order matters:** CEISA rejects the payload when keys are out of order. Every object
> below lists keys **in the order they must be emitted**. Types are JSON types. The
> "fallback" column means: use the fallback when the source value is null, empty string,
> 0 or 0.0. **The string "0" is NOT empty** (see §10 for the PHP pitfall).

### 9.1 Root object

| # | Key | JSON type | Value |
|---|---|---|---|
| 1 | asalData | string | `"S"` |
| 2 | asuransi | number | `0` |
| 3 | biayaTambahan | number | `0` |
| 4 | biayaPengurang | number | `0` |
| 5 | bruto | number (float) | header.bruto, fallback 0 |
| 6 | cif | number (float) | header.cif, fallback 0 |
| 7 | disclaimer | string | `"1"` |
| 8 | freight | number | `0` |
| 9 | hargaPenyerahan | number (float) | **header.cif**, fallback 0 |
| 10 | jabatanTtd | string | header.jabatanTtd |
| 11 | jumlahKontainer | number | `0` (always — §11 Q-a) |
| 12 | kodeDokumen | string | `"261"` |
| 13 | kodeKantor | string | header.kodeKantor |
| 14 | kodeTujuanPengiriman | string | header.kodeTujuanPengiriman |
| 15 | kodeValuta | string | header.kodeValuta, fallback "" |
| 16 | kotaTtd | string | header.kotaTtd |
| 17 | namaTtd | string | header.namaTtd |
| 18 | ndpbm | number (float) | header.ndpbm, fallback 0 |
| 19 | netto | number | round(header.netto, 2), fallback 0 |
| 20 | nik | string | `"0"` |
| 21 | nilaiBarang | number (float) | **header.cif**, fallback 0 |
| 22 | nomorAju | string | header.nomorAju |
| 23 | seri | number | `1` |
| 24 | tanggalAju | string | **header.tanggalTtd** `YYYY-MM-DD`, fallback "" |
| 25 | tanggalTtd | string | header.tanggalTtd `YYYY-MM-DD`, fallback "" |
| 26 | tempatStuffing | string | `""` |
| 27 | tglAkhirBerlaku | string | `""` |
| 28 | tglAwalBerlaku | string | `""` |
| 29 | totalDanaSawit | string | `""` |
| 30 | barang | array | §9.8 (one per barang, in seriBarang order) |
| 31 | dokumen | array | §9.3 |
| 32 | entitas | array | §9.2 (always exactly 3 objects) |
| 33 | jaminan | array | §9.4 |
| 34 | kemasan | array | §9.5 |
| 35 | pengangkut | array | §9.6 (always exactly 1 object) |
| 36 | pungutan | array | §9.7 (always exactly 1 object) |
| 37 | uangMuka | number | `0` |
| 38 | vd | number | `0` |

**There is no `kontainer` key.** It is not sent (§11 Q-a).

### 9.2 `entitas[]`: always 3 objects, in the order seri 3 → 7 → 8

Slot filling: collect the entities for `selectEntitas1`, `selectEntitas2` and `selectEntitas3`
**in that order, skipping empty ones**. The 1st found goes to slot seri 3, the 2nd to seri 7,
the 3rd to seri 8 (§11 Q-d). If a slot has no entity, use the "empty slot" column.
Values come from the **entity master**, not from the document's typed snapshot (§11 Q-f).

Normal mapping: selectEntitas1 (Pengusaha TPB) → seri 3, selectEntitas2 (Pemilik Barang) →
seri 7, selectEntitas3 (Penerima Barang) → seri 8.

**Slot seri 3**

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | alamatEntitas ("") | "" |
| 2 | kodeEntitas | string | `"3"` | "3" |
| 3 | kodeJenisApi | string | kodeJenisApi ("02") | "02" |
| 4 | kodeJenisIdentitas | string | kodeJenisIdentitas ("6") | "6" |
| 5 | namaEntitas | string | namaEntitas ("") | "" |
| 6 | kodeStatus | string | kodeStatus ("0") | "0" |
| 7 | nibEntitas | string | nibEntitas ("") | "" |
| 8 | nomorIdentitas | string | nomorIdentitas ("") | "" |
| 9 | nomorIjinEntitas | string | nomorIjinEntitas ("") | "" |
| 10 | tanggalIjinEntitas | string | `YYYY-MM-DD` ("") | "" |
| 11 | seriEntitas | number | `3` | 3 |

**Slot seri 7**: a different key set and order

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | alamatEntitas ("") | "" |
| 2 | kodeEntitas | string | `"7"` | "7" |
| 3 | nomorIdentitas | string | nomorIdentitas ("") | "" |
| 4 | kodeNegara | string | entity's country kodeNegara ("") | "" |
| 5 | kodeStatus | string | kodeStatus ("0") | "0" |
| 6 | namaEntitas | string | namaEntitas ("") | "" |
| 7 | kodeJenisIdentitas | string | kodeJenisIdentitas ("6") | "6" |
| 8 | seriEntitas | number | `7` | 7 |

**Slot seri 8**

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | alamatEntitas ("") | "" |
| 2 | kodeEntitas | string | `"8"` | "8" |
| 3 | kodeJenisApi | string | kodeJenisApi ("02") | "02" |
| 4 | kodeJenisIdentitas | string | kodeJenisIdentitas ("6") | "6" |
| 5 | kodeStatus | string | kodeStatus ("0") | "0" |
| 6 | namaEntitas | string | namaEntitas ("") | "" |
| 7 | nomorIdentitas | string | nomorIdentitas ("") | "" |
| 8 | nomorIjinEntitas | string | nomorIjinEntitas ("") | "" |
| 9 | tanggalIjinEntitas | string | `YYYY-MM-DD` ("") | "" |
| 10 | seriEntitas | number | `8` | 8 |

### 9.3 `dokumen[]`: one per Dokumen row

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idDokumen | string | `"0"` |
| 2 | kodeDokumen | string | row.kodeDokumen |
| 3 | nomorDokumen | string/null | row.nomorDokumen |
| 4 | seriDokumen | number | row.seriDokumen |
| 5 | tanggalDokumen | string/null | `YYYY-MM-DD` or **null** |

### 9.4 `jaminan[]`: one per Jaminan row

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idJaminan | string/null | **row.kodeJenisJaminan** (yes, the type code) |
| 2 | kodeJenisJaminan | string/null | row.kodeJenisJaminan |
| 3 | nilaiJaminan | number(int)/null | row.nilaiJaminan |
| 4 | nomorBpj | string/null | row.nomorBpj |
| 5 | nomorJaminan | string/null | row.nomorJaminan |
| 6 | penjamin | string/null | row.penjamin |
| 7 | tanggalBpj | string/null | `YYYY-MM-DD` or null |
| 8 | tanggalJaminan | string/null | `YYYY-MM-DD` or null |
| 9 | tanggalJatuhTempo | string/null | `YYYY-MM-DD` or null |

### 9.5 `kemasan[]`: one per Kemasan row

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | jumlahKemasan | number(int) | row.jumlahKemasan (0) |
| 2 | kodeJenisKemasan | string | row.kodeJenisKemasan ("") |
| 3 | seriKemasan | **string** | row.seriKemasan as stored, e.g. `"1"` (fallback number 1) |
| 4 | merkKemasan | string | row.merkKemasan ("") |

### 9.6 `pengangkut[]`: exactly one object

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idPengangkut | string | `"1"` |
| 2 | kodeBendera | string | `""` |
| 3 | namaPengangkut | string | `""` |
| 4 | nomorPengangkut | string | `""` |
| 5 | kodeCaraAngkut | string | header.kodeCaraAngkut ("") |
| 6 | seriPengangkut | number | `1` |

### 9.7 `pungutan[]`: exactly one object

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idPungutan | string | `"1"` |
| 2 | kodeFasilitasTarif | string | `"3"` |
| 3 | kodeJenisPungutan | string | `"PPN"` |
| 4 | nilaiPungutan | number | round(**first barang's** cifRupiah, 2). `0` if there are no barang (§11 Q-c). |

### 9.8 `barang[]`: one per Barang row (ordered by seriBarang)

> Villiger's code writes `ukuran` and `uraian` **twice** in this object. The key stays at its
> **first** position (27/28) and takes the **last** value. The table below already shows the
> final result: emit each key once.

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | idBarang | string | `"0"` |
| 2 | cif | number | row.cif (raw, no rounding) |
| 3 | cifRupiah | number | round(row.cifRupiah, 2) |
| 4 | hargaEkspor | number | row.hargaEkspor (0) — never set → 0 |
| 5 | hargaPenyerahan | number | row.hargaPenyerahan (0) — never set → 0 |
| 6 | isiPerKemasan | number | row.isiPerKemasan (0) — never set → 0 |
| 7 | jumlahKemasan | number | row.jumlahKemasan |
| 8 | jumlahSatuan | number | row.jumlahSatuan |
| 9 | kodeAsalBahanBaku | string | `"0"` |
| 10 | kodeAsalBarang | string | `"0"` |
| 11 | kodeBarang | string | row.kodeBarang |
| 12 | kodeDokumen | string | `"261"` |
| 13 | kodeJenisKemasan | string/null | row.kodeJenisKemasan (may be null) |
| 14 | kodeNegaraAsal | string | row.kodeNegaraAsal |
| 15 | kodeSatuanBarang | string | row.kodeSatuanBarang |
| 16 | merk | string | row.merk (`"-"`) |
| 17 | ndpbm | number | row.ndpbm (0) |
| 18 | netto | number | round(row.netto, 2) |
| 19 | nilaiBarang | number | row.cifRupiah (0) — not rounded |
| 20 | nilaiJasa | number | round(row.cifRupiah, 2) |
| 21 | posTarif | string | row.posTarif |
| 22 | seriBarang | number | row.seriBarang |
| 23 | spesifikasiLain | string | row.spesifikasiLain (`"0"`) |
| 24 | hargaPerolehan | number | row.hargaPerolehan (0) — never set → 0 |
| 25 | tipe | string | row.tipe (`"0"`) |
| 26 | uangMuka | number | `0` |
| 27 | ukuran | string | row.ukuran (`"0"`) |
| 28 | uraian | string | row.uraian |
| 29 | diskon | number | row.diskon (0) |
| 30 | freight | number | row.freight (0) |
| 31 | hargaSatuan | number | row.hargaSatuan (0) |
| 32 | kodeKategoriBarang | string/null | row.kodeKategoriBarang (may be null) |
| 33 | kodePerhitungan | string | row.kodePerhitungan (`"0"`) |
| 34 | bahanBaku | array | §9.9: bahan baku rows whose seriBarang = this row's seriBarang, in stored order |
| 35 | barangTarif | array | §9.10: always exactly 3 objects (BM, PPH, PPN) |
| 36 | nilaiDiskon | number | `0` |
| 37 | volume | null | always `null` |
| 38 | persenBM | number | row.persenBM (no fallback; in practice 0) |
| 39 | persenPPN | number | row.persenPPN (in practice 0) |
| 40 | persenPPh | number | row.persenPPh (in practice 0) |
| 41 | barangDokumen | array | `[]` |

### 9.9 `barang[].bahanBaku[]`

> Villiger writes `kodeAsalBahanBaku` twice here too, with the same value. It stays at
> position 9.

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | seriBarangDokAsal | number | **`1` (hardcoded)** — §11 Q-i |
| 2 | cif | number | bb.cif (0) |
| 3 | cifRupiah | number | round(bb.cifRupiah, 2) (0) |
| 4 | flagTis | string | `""` |
| 5 | hargaPenyerahan | number | bb.hargaPenyerahan (0) |
| 6 | hargaPerolehan | number | bb.hargaPerolehan (0) |
| 7 | isiPerKemasan | number | `0` |
| 8 | jumlahSatuan | number | bb.jumlahSatuan (0) |
| 9 | kodeAsalBahanBaku | string | bb.kodeAsalBahanBaku (`"0"`) |
| 10 | kodeBarang | string | bb.kodeBarang |
| 11 | kodeDokAsal | string | **bb.kodeDokumen** (origin doc code: 23/27/40) |
| 12 | kodeDokumen | string | `"261"` |
| 13 | kodeKantor | string | **bb.kodeKantorAsal** |
| 14 | kodeSatuanBarang | string | bb.kodeSatuanBarang |
| 15 | merkBarang | string | bb.merkBarang ("") |
| 16 | ndpbm | number | bb.ndpbm (0) — always 0 |
| 17 | netto | number | bb.netto (0) — always 0 |
| 18 | nilaiJasa | number | bb.nilaiJasa (0) |
| 19 | nomorDaftarDokAsal | string | bb.nomorDaftarDokAsal |
| 20 | nomorDokumen | string | **bb.nomorDaftarDokAsal** (same value as #19) |
| 21 | posTarif | string | bb.posTarif |
| 22 | seriBahanBaku | number | bb.seriBahanBaku |
| 23 | seriBarang | number | bb.seriBarang |
| 24 | seriIjin | number | bb.seriIjin (`1`) — always 1 |
| 25 | spesifikasiLainBarang | string | bb.spesifikasiLainBarang ("") |
| 26 | tanggalDaftarDokAsal | string | `YYYY-MM-DD` ("") |
| 27 | tipeBarang | string | bb.tipeBarang ("") |
| 28 | ukuranBarang | string | bb.ukuranBarang ("") |
| 29 | uraianBarang | string | bb.uraianBarang |
| 30 | bahanBakuTarif | array | exactly 1 object, below |

`bahanBakuTarif[0]`:

| # | Key | Type | Value |
|---|---|---|---|
| 1 | jumlahKemasan | number | `0` |
| 2 | jumlahSatuan | number | bb.jumlahSatuan (0) |
| 3 | kodeAsalBahanBaku | string | bb.kodeAsalBahanBaku (`"0"`) |
| 4 | kodeFasilitasTarif | string | `"3"` |
| 5 | kodeJenisPungutan | string | `"PPN"` |
| 6 | kodeJenisTarif | string | `"1"` |
| 7 | kodeSatuanBarang | string | bb.kodeSatuanBarang |
| 8 | nilaiBayar | number | round(bb.cifRupiah, 2) (0) |
| 9 | nilaiFasilitas | number | `0` |
| 10 | nilaiSudahDilunasi | number | `0` |
| 11 | seriBahanBaku | number | **`1` (hardcoded)** |
| 12 | tarif | number | `11` |
| 13 | tarifFasilitas | number | `100` |

### 9.10 `barang[].barangTarif[]`: exactly 3 objects, in this order

> ⚠ **The key order differs between the objects.** In BM, `kodeJenisPungutan` comes *before*
> `kodeSatuanBarang`. In PPH and PPN it comes *after*.

**[0] BM**

| # | Key | Type | Value |
|---|---|---|---|
| 1 | kodeJenisTarif | string | `"1"` |
| 2 | jumlahSatuan | number | round(row.netto, 2) (0) |
| 3 | kodeFasilitasTarif | string | `"3"` |
| 4 | kodeJenisPungutan | string | `"BM"` |
| 5 | kodeSatuanBarang | string | row.kodeSatuanBarang |
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
| 2 | jumlahSatuan | number | row.netto (0) — **not rounded** |
| 3 | kodeFasilitasTarif | string | `"6"` |
| 4 | kodeSatuanBarang | string | row.kodeSatuanBarang |
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
| 2 | jumlahSatuan | number | round(row.netto, 2) (0) |
| 3 | kodeFasilitasTarif | string | `"6"` |
| 4 | kodeSatuanBarang | string | row.kodeSatuanBarang |
| 5 | kodeJenisPungutan | string | `"PPN"` |
| 6 | nilaiBayar | number | round(row.cifRupiah, 2) (0) |
| 7 | nilaiFasilitas | number | `0` |
| 8 | nilaiSudahDilunasi | number | `0` |
| 9 | seriBarang | number | `3` |
| 10 | tarif | number | `11` |
| 11 | tarifFasilitas | number | `100` |

### 9.11 Skeleton example (1 barang, 1 bahan baku, values illustrative)

```json
{
  "asalData": "S", "asuransi": 0, "biayaTambahan": 0, "biayaPengurang": 0,
  "bruto": 120.0, "cif": 1500.0, "disclaimer": "1", "freight": 0,
  "hargaPenyerahan": 1500.0, "jabatanTtd": "Manager", "jumlahKontainer": 0,
  "kodeDokumen": "261", "kodeKantor": "071100", "kodeTujuanPengiriman": "1",
  "kodeValuta": "USD", "kotaTtd": "Jember", "namaTtd": "Budi", "ndpbm": 16000.0,
  "netto": 100.5, "nik": "0", "nilaiBarang": 1500.0, "nomorAju": "000000...",
  "seri": 1, "tanggalAju": "2026-10-26", "tanggalTtd": "2026-10-26",
  "tempatStuffing": "", "tglAkhirBerlaku": "", "tglAwalBerlaku": "", "totalDanaSawit": "",
  "barang": [
    {
      "idBarang": "0", "cif": 0.0, "cifRupiah": 24000000.0, "hargaEkspor": 0,
      "hargaPenyerahan": 0, "isiPerKemasan": 0, "jumlahKemasan": 1.0, "jumlahSatuan": 10.0,
      "kodeAsalBahanBaku": "0", "kodeAsalBarang": "0", "kodeBarang": "SP-001",
      "kodeDokumen": "261", "kodeJenisKemasan": "BX", "kodeNegaraAsal": "DE",
      "kodeSatuanBarang": "PCE", "merk": "-", "ndpbm": 0, "netto": 100.5,
      "nilaiBarang": 24000000.0, "nilaiJasa": 24000000.0, "posTarif": "84879000",
      "seriBarang": 1, "spesifikasiLain": "0", "hargaPerolehan": 0, "tipe": "0",
      "uangMuka": 0, "ukuran": "0", "uraian": "BEARING", "diskon": 0, "freight": 0,
      "hargaSatuan": 0, "kodeKategoriBarang": null, "kodePerhitungan": "0",
      "bahanBaku": [ { "seriBarangDokAsal": 1, "cif": 0.0, "...": "...", "bahanBakuTarif": [ { "...": "..." } ] } ],
      "barangTarif": [ { "kodeJenisTarif": "1", "...": "BM" }, { "...": "PPH" }, { "...": "PPN" } ],
      "nilaiDiskon": 0, "volume": null, "persenBM": 0.0, "persenPPN": 0.0, "persenPPh": 0.0,
      "barangDokumen": []
    }
  ],
  "dokumen": [ { "idDokumen": "0", "kodeDokumen": "380", "nomorDokumen": "INV-1", "seriDokumen": 1, "tanggalDokumen": "2026-10-20" } ],
  "entitas": [ { "alamatEntitas": "...", "kodeEntitas": "3", "...": "..." }, { "kodeEntitas": "7", "...": "..." }, { "kodeEntitas": "8", "...": "..." } ],
  "jaminan": [],
  "kemasan": [ { "jumlahKemasan": 1, "kodeJenisKemasan": "BX", "seriKemasan": "1", "merkKemasan": "" } ],
  "pengangkut": [ { "idPengangkut": "1", "kodeBendera": "", "namaPengangkut": "", "nomorPengangkut": "", "kodeCaraAngkut": "3", "seriPengangkut": 1 } ],
  "pungutan": [ { "idPungutan": "1", "kodeFasilitasTarif": "3", "kodeJenisPungutan": "PPN", "nilaiPungutan": 24000000.0 } ],
  "uangMuka": 0,
  "vd": 0
}
```

---

## 10. Laravel implementation notes (payload fidelity)

- **Build the payload by hand** as PHP arrays, literally in the §9 order. PHP arrays keep
  insertion order and `json_encode` keeps it too. Do **not** use Eloquent `toArray()`, API
  Resources or `array_merge` to assemble it, because they follow column order or reorder keys.
- **Lists must be sequential arrays.** `array_filter()` and `Collection::where()/filter()`
  keep the original keys, so `json_encode` then produces an *object* (`{"0":…}`) instead of a
  list. Always call `array_values()` / `->values()`, especially when grouping bahan baku by
  seriBarang.
- **Encode yourself, then send the raw body:**
  `json_encode($payload, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES)` and send it
  with `Http::withToken(...)->withHeaders([...])->withBody($json, 'application/json')->timeout(30)->post(...)`.
  `Http::post($url, $array)` lets Guzzle re-encode with default flags.
  - `JSON_PRESERVE_ZERO_FRACTION`: the current sender writes floats like `120.0`. PHP would
    otherwise write `120`.
  - `JSON_UNESCAPED_SLASHES`: the current sender does not escape `/`. PHP escapes it as `\/`
    by default.
- **Cast types explicitly** to match the §9 "Type" column. Code fields are strings (`"261"`,
  `"0"`, `"1"`). `seri*` are ints. Amounts are floats. Use `(string)`, `(int)` and `(float)`.
  Nullable fields must stay `null` (not "").
- **Falsy-fallback pitfall:** the rule is "use the fallback if the value is null, "", 0 or 0.0".
  PHP's `?:` also treats the **string `"0"`** as falsy, but the current sender keeps `"0"`.
  Write a small helper, e.g. `orDefault($v, $d)`, that returns `$d` only for null, `""`, `0`
  and `0.0`.
- **Rounding:** the current sender rounds the binary float value (`round(2.675, 2)` → 2.67),
  while PHP `round()` rounds half away from zero (→ 2.68). This only matters at exact .xx5
  boundaries. Accept it, or round with `bcmath` / `number_format` consistently.
- **Dates:** always `Y-m-d`.
- Keep `User-Agent: Mozilla/5.0` on both CEISA calls.
- Wrap create, update and delete in `DB::transaction()`. Villiger's legacy path committed
  per row, but the v2 path is already atomic.

---

## 11. Known quirks (replicated as-is; suggested fixes for later)

| ID | Quirk (current behaviour) | Suggested fix (optional, later) |
|---|---|---|
| Q-a | Kontainer rows are saved, but **no `kontainer` key is sent**, and `jumlahKontainer` is always 0. | Add a `kontainer` array + count, if CEISA requires it for BC 2.6.1. |
| Q-b | `barangTarif` is hardcoded to BM 40% / PPh 2.5% / PPN 11%, and `bahanBakuTarif` to PPN 11%. `nilaiBayar` = the full CIF Rupiah (not CIF × rate). | Take rates from the HS master and compute nilaiBayar. |
| Q-c | `pungutan` is one PPN row equal to the **first barang's** CIF Rupiah. The Pungutan tab is not wired. | Sum over all barang, per pungutan type. |
| Q-d | Entitas slots are filled by "next non-empty", so if Entitas 1 is empty, Entitas 2's data lands in the seri 3 slot. | Map fixed: E1→3, E2→7, E3→8, with an empty slot when missing. |
| Q-e | A sent document can still be edited, deleted and re-sent. The `idHeader` returned by CEISA is not stored. | Lock edit/delete when `sent = 1`, and store `idHeader` + the sent timestamp. |
| Q-f | Send reads entity data from the **master** (by select id), and ignores the NPWP/nama/alamat typed or saved on the document. | Send the document's snapshot instead, or don't let users edit the snapshot. |
| Q-g | The per-barang `cif` input is disabled → always 0. The per-barang `ndpbm` and the persen/nilai BM/PPN/PPh fields are never filled → 0. | Compute `cif = cifRupiah / header.ndpbm`, and fill ndpbm and rates from the header / HS master. |
| Q-h | Header `cifRupiah` is shown (ndpbm × cif) but **never persisted**. Root `hargaPenyerahan` and `nilaiBarang` are sent as the **foreign** CIF. | Persist cifRupiah. Confirm with CEISA which currency these fields expect. |
| Q-i | Bahan baku `seriBarangDokAsal` is sent as `1` and `bahanBakuTarif.seriBahanBaku` as `1`, regardless of stored values. `nomorAjuDokAsal` is stored but not sent. | Send the stored values. |
| Q-j | Deleting a barang row in the UI does **not** renumber or remove its bahan baku, so its `seriBarang` can point at the wrong barang after save. | On barang delete: drop its bahan baku and shift higher seriBarang values down by 1. |
| Q-k | The fallback `seriBahanBaku` is the **global** index across all bahan baku, not per barang. | Number per barang (1..n within each seriBarang). |
| Q-l | Detail on the Transaction tab opens the form in **editable** mode. | Make it read-only. |
| Q-m | The header `tanggalAju` column exists (NOT NULL), but the save path never sets it. Send uses `tanggalTtd`. | Drop the column, or set it explicitly. |
| Q-n | Transaction list has no period filter. Outstanding has no BC-type filter (it's commented out). | Decide whether the new ERP needs either filter. |
| Q-o | The **Send Data** button only works on the server-rendered list. The JS list refresh (after save/delete/send) re-creates the button **without a click handler**, so it does nothing until the page is reloaded. | Always wire the button (e.g. a delegated click handler on the table). Don't copy this one. |

---

## 12. Open items for the database session (Laravel codebase)

- The concrete source-document table(s) for Outstanding, and where the `posted` /
  `ready_sent` flags live.
- Table names and keys for the header + 6 children, and FK cascade vs explicit delete.
- Where the master/reference lists live (entities, HS, country, kategori, satuan,
  kemasan, jenis dokumen), and whether a CEISA token table already exists for other BC modules.
- Whether to store the internal-only fields (`no_reg_bc`, `tgl_bc`, `tgl_keluar`).
- Per-route permission model for view / add / edit / delete / send.

---

## Appendix A: code lists used by the UI

- **kodeTujuanPengiriman:** 1 Diperbaiki, 2 Disubkontrakkan, 3 Dipinjamkan, 4 Barang Return, 5 Lainnya.
- **kodeCaraAngkut:** 1 Laut, 2 Kereta Api, 3 Darat *(default)*, 4 Udara, 5 Pos, 6 Multimoda,
  7 Instalasi/Pipa, 8 Perairan, 9 Lainnya, 10 Instalasi, 11 Pipa, 12 Transmisi.
- **kodeJenisJaminan:** 1 Tunai, 2 Bank Garansi, 3 Customs Bond, 4 Tertulis/CG, 5 STTJ,
  6 Pusat, 7 Indonesia EximBank, 8 Perusahaan Penjaminan.
- **kodeUkuranKontainer:** 20, 40, 45, 60 (feet).
- **kodeJenisKontainer (Jenis Muatan):** 4 Empty, 7 LCL, 8 FCL.
- **kodeTipeKontainer:** 1 General/Dry Cargo, 2 Tunnel Type, 3 Open Top Steel, 4 Flat Rack,
  5 Reefer, 6 Barge Container, 7 Bulk Container, 8 Isotank, 99 Lain-lain.
- **kodeAsalBahanBaku:** 0 Impor, 1 Lokal.
- **Bahan baku kodeKantorAsal:** 071100 KPPBC TMP C Jember (company-specific; make it configurable).
- **Bahan baku origin kodeDokumen:** 23 BC 2.3, 27 BC 2.7, 40 BC 4.0.
