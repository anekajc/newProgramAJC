# Document BC 2.6.2 — Functional Plan for Re-implementation (Laravel 12)

Source of truth: the Villiger ERP `document_bc262` module (the "v2" JSON path, which is the
only live path; the legacy form-encoded `add`/`edit` are dead code). This plan describes
**behaviour, rules and the exact CEISA payload**, not code. The database design is out of
scope and will be decided in the Laravel codebase session.

**Read `documentbc261_plan.md` first.** BC 2.6.2 is its mirror document and reuses the same
UI framework, child collections, normalisation, CEISA login and send flow. This plan spells
out every **difference**, and gives the full CEISA payload, because key order matters.

Decisions already made:
- **Fidelity:** copy current behaviour exactly. Every deviation is listed in §11 *Known quirks*.
- **Outstanding source:** a **direct link to the new app's BC 2.6.1 document** (`sent = 1`,
  not yet used by a BC 2.6.2).
- **CEISA auth:** a per-user login modal, with the token stored per user (shared with every
  BC module).
- **Scope:** backend, the CEISA payload, and UI behaviour (not visual design).

---

## 1. Overview

- BC 2.6.2 = customs document for **goods coming back into the bonded zone** after they left
  temporarily on a BC 2.6.1 (repaired, subcontracted, loaned, returned).
- Lifecycle: **Draft (saved)** → **Sent** (`sent = 1` only when CEISA answers `status: "OK"`).
- Created either:
  1. **From an Outstanding BC 2.6.1**: all 6 child collections are copied, and the BC 2.6.1 is
     linked and so consumed.
  2. **Standalone**: a blank form.
- Child collections (identical shape to BC 2.6.1): **Barang**, **Bahan Baku**, **Kemasan**,
  **Kontainer (Peti Kemas)**, **Jaminan**, **Dokumen**.

---

## 2. Logical data model: differences from BC 2.6.1

### 2.1 Header

Same as the BC 2.6.1 plan §2.1, with these changes:

| Field | Change vs BC 2.6.1 |
|---|---|
| source_document_id → **document_bc261_id** | FK to the new app's **BC 2.6.1** (nullable = standalone). There's no packing-list link. |
| kodeTujuanPengiriman | Different meaning and code list: **"Kode Tujuan Pemasukan"**, values 1–6 (Appendix A). Sent as **`kodeTujuanPemasukan`**. |
| tgl_keluar | **Not present.** Replaced by **arr_date** ("Tanggal Pemasukan", date, internal only, not sent). |
| no_reg_bc, tgl_bc | Same (internal only, not sent). |
| **Pungutan totals** (new) | `nilaiBM, nilaiBMAD, nilaiBMI, nilaiBMTP, nilaiBMP, nilaiCukaiEa, nilaiCukaiCmea, nilaiCukaiCtem, nilaiPPN, nilaiPPNBM, nilaiPPh, nilaiTotalPungutan` (decimal, empty → 0). Typed on the Pungutan tab. **Saved but never sent** (§11 Q-c). |
| cifRupiah | Same: not persisted, display = ndpbm × cif. |
| kodeKantor | Default 071100 exists, but **Add blanks the whole header**, so the default isn't applied (§11 Q-k). |

### 2.2 Children

- **Barang**: same fields as BC 2.6.1 §2.2, with `kodeDokumen` always **"262"**. The CIF input
  is still disabled (always 0). There's no `nilaiBM/PPN/PPh` per row and no `volume` column.
- **Bahan Baku**: same as BC 2.6.1 §2.3, plus:
  - **netto** is a real input here (decimal, empty → 0, sent).
  - **nomorAjuDokAsal is sent** (BC 2.6.1 stores it but doesn't send it).
  - kodeDokumen (origin doc code) falls back to `"262"` if empty.
- **Kemasan, Kontainer, Jaminan, Dokumen**: identical to BC 2.6.1 §2.4–2.7. Dokumen
  kodeDokumen falls back to `"262"`. **Kontainer IS sent here** (§9.6).

### 2.3 Token and masters

Same as BC 2.6.1 §2.8–2.9.

---

## 3. Outstanding (BC 2.6.1 as the source)

**Eligibility (exact current rule):**
- BC 2.6.1 with `sent = 1`, and
- **no** BC 2.6.2 exists with `document_bc261_id = this.id`.
- No period filter. Order: id desc.

**Outstanding list columns:**
- **Nomor AJU** (of the BC 2.6.1).
- **Tanggal** (its tanggalTtd, dd-mm-yyyy).
- **Rev. Doc.**: the BC 2.6.1's own Rev. Doc., i.e. its source packing-list number if it has
  one, otherwise its nomorAju.

DataTable default sort column 0 desc. Clicking the selected row again **unselects** it, so
Add then opens a blank form.

**Read-only preview (Detail on Outstanding):** BC 2.6.1 nomorAju, tanggalTtd + a barang table
(No, Kode Barang, Uraian, Satuan, Jumlah).

**Pre-fill on Add (BC 2.6.1 selected):** the **header is NOT copied**, it stays blank (§11 Q-j).
All 6 child collections are copied from the BC 2.6.1:

| Collection | Copy rule |
|---|---|
| Barang | Each row copies posTarif, kodeBarang, uraian, merk, tipe, ukuran, spesifikasiLain, kodeSatuanBarang, jumlahSatuan, netto, cif, cifRupiah, kodeNegaraAsal, kodeKategoriBarang, jumlahKemasan, kodeJenisKemasan. **Renumbered 1..N** in source order. ndpbm, persen*, keteranganDokumen are **not** copied (→ 0/""). |
| Bahan Baku | Every column is copied (origin, posTarif, kode, uraian, merk/tipe/ukuran/spek, satuan, jumlah, origin doc code, kantor asal, nomor/tanggal daftar, nomorAju asal, seri barang asal, cif, cifRupiah, harga penyerahan/perolehan, nilaiJasa). **`seriBarang` is remapped** old → new barang number, so each row still points at the right barang. netto: BC 2.6.1 has none → 0. |
| Kemasan | Copied as-is (jumlah, jenis, merk). |
| Kontainer | Copied as-is (nomor, ukuran, jenis muatan, tipe). |
| Dokumen | Copied as-is (kode, nomor, tanggal). |
| Jaminan | Copied as-is (all 8 fields). |

The BC 2.6.1 id is remembered and saved as `document_bc261_id`.

**State changes:**
- **No flags are written on the BC 2.6.1.** "Consumed" means only that a BC 2.6.2 links to it.
- Deleting the BC 2.6.2 makes the BC 2.6.1 outstanding again.
- Edit never changes the link.

---

## 4. Screens & UI behaviour

Same as the BC 2.6.1 plan §4, with these differences:

- **Outstanding tab** shows sent BC 2.6.1 documents (§3) instead of packing lists.
- **Transaction tab**: all BC 2.6.2 documents, id desc, **no period filter**. **Rev. Doc.** =
  the linked BC 2.6.1's nomorAju, otherwise this document's own nomorAju. Status = SENT ✓ /
  **Send Data** button. The button must work after a list refresh too (§11 Q-a).
- **Add** (both tabs) → `showAdd`. The **whole header is blanked** (BC 2.6.1 restores
  defaults instead). Then, if a BC 2.6.1 is selected, the child collections are pre-filled
  (§3). CIF Rupiah is recalculated (ndpbm × cif).
- **Detail**: Outstanding → BC 2.6.1 preview. Transaction → the document form, **editable**
  (same as BC 2.6.1 Q-l).
- **Edit / Delete** as in BC 2.6.1.
- **Form tabs** are the same 10: Header, Entitas, Dokumen, Pengangkut, Kemasan, Transaksi,
  Barang, Jaminan, Pungutan, Pernyataan.
  - **Header tab:** nomorAju, kodeKantor, **Kode Tujuan Pemasukan** (1–6), no_reg_bc, tgl_bc,
    **arr_date (Tanggal Pemasukan)**.
  - **Entitas tab:** **Pengusaha TPB** (Entitas 1: NPWP, select, alamat, no + tgl izin TPB),
    **Pengirim Barang** (Entitas 3: NPWP, select, alamat), **Pemilik Barang** (Entitas 2:
    NPWP, select, alamat).
  - **Pengangkut tab:** kodeCaraAngkut select 1–9, default 3 (DARAT). There's no 10–12 here.
  - **Pungutan tab: wired to save.** It has 12 numeric inputs (default `0.00`): BM & BMT (BM,
    BMAD, BMI, BMTP, BMP), Cukai (EA, CMEA, CTEM), Total, PDRI (PPN, PPNBM, PPh). They are
    **typed manually**; nothing auto-calculates them, the Total included. The "Detail Barang"
    button there isn't wired.
  - **Barang / Bahan Baku editors:** identical to BC 2.6.1 §4.4–4.5, with a **Netto** input
    added to the Bahan Baku modal.
- After save: close, refresh both lists, select the saved doc.

---

## 5. Server-side rules

### 5.1 Save payload (UI → server)

Same shape as BC 2.6.1 §5.1, with **`document_bc261_id`** in place of `source_document_id`.
The header adds `arr_date` and the 12 pungutan fields, and has no `tgl_keluar`.

### 5.2 Normalisation

Identical to BC 2.6.1 §5.2 (decimals → 0, ints → null, dates → null, NOT NULL strings → "",
`seri*` = position).

### 5.3 Create

1. Create the header with `document_bc261_id`, `isRealisasi = 0`, and the vestigial
   `merkKemasan = ""`.
2. Insert all 6 child collections.
3. **One transaction.** No update to the BC 2.6.1.
4. Response `{ id, message: "Data telah ditambahkan." }`.

### 5.4 Update

1. Doc not found → 404 `{ errors: { id: "Document not found." } }`.
2. Overwrite the header (not the link, not `sent`).
3. Delete and re-insert all 6 child collections.
4. One transaction. Response `{ id, message: "Data telah diubah." }`.

### 5.5 Delete

1. Delete all 6 child collections, then the header.
2. One transaction. Response `{ message: "Data telah dihapus." }`.
3. Nothing else to reset: the BC 2.6.1 reappears in Outstanding automatically.

### 5.6 Read endpoints

- Outstanding list (id, nomorAju, tanggal, rev_doc).
- Transaction list.
- Show header (dates `YYYY-MM-DD` or "", including `arr_date` and pungutan fields).
- Show each child collection.
- BC 2.6.1 preview (header + barang).
- The BC 2.6.1 child collections for pre-fill (reuse the BC 2.6.1 module's show endpoints).

---

## 6. Endpoint list (logical)

Same as BC 2.6.1 §6, plus pre-fill reads from the BC 2.6.1 module. The Outstanding list
returns BC 2.6.1 documents.

---

## 7. CEISA authentication

**Identical to the BC 2.6.1 plan §7.**

---

## 8. Send to CEISA

**Identical to the BC 2.6.1 plan §8:**
- Token check → 401.
- `POST https://apis-gw.beacukai.go.id/openapi/document`, **timeout 30 s**.
- HTTP error → same status; network → 500; non-JSON → 502.
- **`status == "OK"` → `sent = 1`**, with `{message, idHeader}` (idHeader not stored).
- Any other status → 400 with details.

The `details` builder is the same. Only the payload (§9) differs.

---

## 9. CEISA JSON payload — EXACT field order

> **Order matters.** Keys are listed in emit order. "Fallback" applies when the value is
> null, "", 0 or 0.0 (**not** the string "0"). "raw" = no fallback (may be `null`).
> ⚑ marks a difference from BC 2.6.1.

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
| 10 | jabatanTtd | string | raw |
| 11 | jumlahKontainer | number | `0` (always, even though kontainer rows are sent) |
| 12 | kodeDokumen | string | ⚑ `"262"` |
| 13 | kodeKantor | string | raw |
| 14 | ⚑ **kodeTujuanPemasukan** | string | header.kodeTujuanPengiriman (raw, no fallback) |
| 15 | kodeValuta | string | fallback "" |
| 16 | kotaTtd | string | raw |
| 17 | namaTtd | string | raw |
| 18 | ndpbm | number (float) | fallback 0 |
| 19 | netto | number | round(header.netto, 2), fallback 0 |
| 20 | nik | string | `"0"` |
| 21 | nilaiBarang | number (float) | **header.cif**, fallback 0 |
| 22 | nomorAju | string | raw |
| 23 | seri | number | `1` |
| 24 | tanggalAju | string | **header.tanggalTtd** `YYYY-MM-DD`, fallback "" |
| 25 | tanggalTtd | string | `YYYY-MM-DD`, fallback "" |
| 26 | tempatStuffing | string | `""` |
| 27 | tglAkhirBerlaku | string | `""` |
| 28 | tglAwalBerlaku | string | `""` |
| 29 | totalDanaSawit | string | `""` |
| 30 | barang | array | §9.8 |
| 31 | dokumen | array | §9.3 |
| 32 | entitas | array | §9.2 (always 3) |
| 33 | jaminan | array | §9.4 |
| 34 | kemasan | array | §9.5 |
| 35 | ⚑ **kontainer** | array | §9.6 (new: between kemasan and pengangkut) |
| 36 | pengangkut | array | §9.7 (always 1) |
| 37 | pungutan | array | §9.7b (always 1) |
| 38 | uangMuka | number | `0` |
| 39 | vd | number | `0` |

### 9.2 `entitas[]`: always 3 objects, order seri 3 → 7 → ⚑ 9

Slot filling as in BC 2.6.1 (selectEntitas1, 2, 3 in order, skipping empty ones; values from
the **entity master**). Normal mapping: Entitas 1 **Pengusaha TPB** → 3, Entitas 2
**Pemilik Barang** → 7, Entitas 3 **Pengirim Barang** → 9.

**Slot seri 3**: same as BC 2.6.1 seri 3

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | ("") | "" |
| 2 | kodeEntitas | string | `"3"` | "3" |
| 3 | kodeJenisApi | string | ("02") | "02" |
| 4 | kodeJenisIdentitas | string | ("6") | "6" |
| 5 | namaEntitas | string | ("") | "" |
| 6 | kodeStatus | string | ("0") | "0" |
| 7 | nibEntitas | string | ("") | "" |
| 8 | nomorIdentitas | string | ("") | "" |
| 9 | nomorIjinEntitas | string | ("") | "" |
| 10 | tanggalIjinEntitas | string | `YYYY-MM-DD` ("") | "" |
| 11 | seriEntitas | number | `3` | 3 |

**Slot seri 7**: same as BC 2.6.1 seri 7

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | ("") | "" |
| 2 | kodeEntitas | string | `"7"` | "7" |
| 3 | nomorIdentitas | string | ("") | "" |
| 4 | kodeNegara | string | entity's master country code ("") | "" |
| 5 | kodeStatus | string | ("0") | "0" |
| 6 | namaEntitas | string | ("") | "" |
| 7 | kodeJenisIdentitas | string | ("6") | "6" |
| 8 | seriEntitas | number | `7` | 7 |

**Slot seri 9**: same keys as BC 2.6.1's seri 8 slot, but code `"9"` and seri `9`

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | ("") | "" |
| 2 | kodeEntitas | string | ⚑ `"9"` | "9" |
| 3 | kodeJenisApi | string | ("02") | "02" |
| 4 | kodeJenisIdentitas | string | ("6") | "6" |
| 5 | kodeStatus | string | ("0") | "0" |
| 6 | namaEntitas | string | ("") | "" |
| 7 | nomorIdentitas | string | ("") | "" |
| 8 | nomorIjinEntitas | string | ("") | "" |
| 9 | tanggalIjinEntitas | string | `YYYY-MM-DD` ("") | "" |
| 10 | seriEntitas | number | ⚑ `9` | 9 |

### 9.3 `dokumen[]`: same as BC 2.6.1

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idDokumen | string | `"0"` |
| 2 | kodeDokumen | string | row.kodeDokumen |
| 3 | nomorDokumen | string/null | row.nomorDokumen |
| 4 | seriDokumen | number | row.seriDokumen |
| 5 | tanggalDokumen | string/null | `YYYY-MM-DD` or null |

### 9.4 `jaminan[]`: same as BC 2.6.1

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idJaminan | string/null | **row.kodeJenisJaminan** |
| 2 | kodeJenisJaminan | string/null | row.kodeJenisJaminan |
| 3 | nilaiJaminan | number(int)/null | row.nilaiJaminan |
| 4 | nomorBpj | string/null | raw |
| 5 | nomorJaminan | string/null | raw |
| 6 | penjamin | string/null | raw |
| 7 | tanggalBpj | string/null | `YYYY-MM-DD` or null |
| 8 | tanggalJaminan | string/null | `YYYY-MM-DD` or null |
| 9 | tanggalJatuhTempo | string/null | `YYYY-MM-DD` or null |

### 9.5 `kemasan[]`: one per Kemasan row

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | jumlahKemasan | number(int) | (0) |
| 2 | kodeJenisKemasan | string | ("") |
| 3 | seriKemasan | ⚑ **number** | int(stored seri), fallback 1 (BC 2.6.1 sends it as a string) |
| 4 | merkKemasan | string | ("") |

### 9.6 ⚑ `kontainer[]`: one per Kontainer row (BC 2.6.1 builds these but never sends them)

| # | Key | Type | Value |
|---|---|---|---|
| 1 | kodeJenisKontainer | string/null | raw (4 / 7 / 8) |
| 2 | kodeTipeKontainer | string/null | raw |
| 3 | kodeUkuranKontainer | string/null | raw (20/40/45/60) |
| 4 | nomorKontainer | string/null | raw |
| 5 | seriKontainer | number | row.seriKontainer |
| 6 | merkKontainer | string | `""` |

### 9.7 `pengangkut[]`: exactly 1 (same as BC 2.6.1)

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idPengangkut | string | `"1"` |
| 2 | kodeBendera | string | `""` |
| 3 | namaPengangkut | string | `""` |
| 4 | nomorPengangkut | string | `""` |
| 5 | kodeCaraAngkut | string | header.kodeCaraAngkut ("") |
| 6 | seriPengangkut | number | `1` |

### 9.7b `pungutan[]`: exactly 1 (same as BC 2.6.1, ignores the Pungutan tab)

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idPungutan | string | `"1"` |
| 2 | kodeFasilitasTarif | string | `"3"` |
| 3 | kodeJenisPungutan | string | `"PPN"` |
| 4 | nilaiPungutan | number | round(**first barang's** cifRupiah, 2), or `0` if there are no barang |

### 9.8 `barang[]`: one per Barang row

Same as BC 2.6.1 §9.8 (including the duplicated `ukuran`/`uraian`, which keep their first
position with the last value), with the differences marked.

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | idBarang | string | `"0"` |
| 2 | cif | number | row.cif (raw) |
| 3 | cifRupiah | number | round(row.cifRupiah, 2) |
| 4 | hargaEkspor | number | (0) |
| 5 | hargaPenyerahan | number | (0) |
| 6 | isiPerKemasan | number | (0) |
| 7 | jumlahKemasan | number | row.jumlahKemasan |
| 8 | jumlahSatuan | number | row.jumlahSatuan |
| 9 | kodeAsalBahanBaku | string | `"0"` |
| 10 | kodeAsalBarang | string | `"0"` |
| 11 | kodeBarang | string | raw |
| 12 | kodeDokumen | string | ⚑ `"262"` |
| 13 | kodeJenisKemasan | string/null | raw |
| 14 | kodeNegaraAsal | string | raw |
| 15 | kodeSatuanBarang | string | raw |
| 16 | merk | string | (`"-"`) |
| 17 | ndpbm | number | (0) |
| 18 | netto | number | round(row.netto, 2) |
| 19 | nilaiBarang | number | ⚑ **row.nilaiBarang** (0) → never set → `0` (BC 2.6.1 sends cifRupiah) |
| 20 | nilaiJasa | number | round(row.cifRupiah, 2) |
| 21 | posTarif | string | raw |
| 22 | seriBarang | number | row.seriBarang |
| 23 | spesifikasiLain | string | (`"0"`) |
| 24 | hargaPerolehan | number | (0) |
| 25 | tipe | string | (`"0"`) |
| 26 | uangMuka | number | `0` |
| 27 | ukuran | string | row.ukuran (`"0"`) |
| 28 | uraian | string | row.uraian |
| 29 | diskon | number | (0) |
| 30 | freight | number | (0) |
| 31 | hargaSatuan | number | (0) |
| 32 | kodeKategoriBarang | string/null | raw |
| 33 | kodePerhitungan | string | (`"0"`) |
| 34 | bahanBaku | array | §9.9: rows whose seriBarang = this seriBarang |
| 35 | barangTarif | array | §9.10: exactly 3 objects |
| 36 | nilaiDiskon | number | `0` |
| 37 | volume | null | always `null` |
| 38 | persenBM | number | row.persenBM (in practice 0) |
| 39 | persenPPN | number | row.persenPPN (in practice 0) |
| 40 | persenPPh | number | row.persenPPh (in practice 0) |
| 41 | barangDokumen | array | `[]` |

### 9.9 ⚑ `barang[].bahanBaku[]`: a different key set and order from BC 2.6.1

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | cif | number | bb.cif (0) |
| 2 | cifRupiah | number | round(bb.cifRupiah, 2) (0) |
| 3 | hargaPenyerahan | number | (0) |
| 4 | hargaPerolehan | number | (0) |
| 5 | jumlahSatuan | number | (0) |
| 6 | kodeSatuanBarang | string | raw |
| 7 | kodeAsalBahanBaku | string | (`"0"`) |
| 8 | kodeBarang | string | raw |
| 9 | kodeDokAsal | string | **bb.kodeDokumen** (origin doc code) |
| 10 | kodeDokumen | string | `"262"` |
| 11 | kodeKantor | string | **bb.kodeKantorAsal** |
| 12 | merkBarang | string | ("") |
| 13 | ndpbm | number | (0) → always 0 |
| 14 | netto | number | round(bb.netto, 2), fallback 0 |
| 15 | nomorAjuDokAsal | string/null | raw (sent here) |
| 16 | nomorDaftarDokAsal | string | raw |
| 17 | posTarif | string | raw |
| 18 | seriBahanBaku | number | raw |
| 19 | seriBarang | number | raw |
| 20 | seriBarangDokAsal | number | **`1` (hardcoded)** |
| 21 | seriIjin | number | (`1`) → always 1 |
| 22 | spesifikasiLainBarang | string | ("") |
| 23 | tanggalDaftarDokAsal | string | `YYYY-MM-DD` ("") |
| 24 | tipeBarang | string | ("") |
| 25 | ukuranBarang | string | ("") |
| 26 | uraianBarang | string | raw |
| 27 | nilaiJasa | number | (0) |
| 28 | bahanBakuTarif | array | exactly 1 object, below |

There are no `flagTis`, `isiPerKemasan` or `nomorDokumen` keys and no duplicate keys
(unlike BC 2.6.1).

`bahanBakuTarif[0]`: ⚑ a different order from BC 2.6.1

| # | Key | Type | Value |
|---|---|---|---|
| 1 | jumlahSatuan | number | bb.jumlahSatuan (0) |
| 2 | kodeFasilitasTarif | string | `"3"` |
| 3 | kodeAsalBahanBaku | string | bb.kodeAsalBahanBaku (`"0"`) |
| 4 | kodeJenisPungutan | string | `"PPN"` |
| 5 | kodeSatuanBarang | string | raw |
| 6 | kodeJenisTarif | string | `"1"` |
| 7 | nilaiBayar | number | round(bb.cifRupiah, 2) (0) |
| 8 | nilaiSudahDilunasi | number | `0` |
| 9 | nilaiFasilitas | number | `0` |
| 10 | seriBahanBaku | number | **`1` (hardcoded)** |
| 11 | tarif | number | `11` |
| 12 | tarifFasilitas | number | `100` |
| 13 | jumlahKemasan | number | `0` (last) |

### 9.10 `barang[].barangTarif[]`: 3 objects (BM, PPH, PPN)

Same as BC 2.6.1 §9.10 (same key orders: in BM `kodeJenisPungutan` comes *before*
`kodeSatuanBarang`, in PPH and PPN *after*; tarif 40 / 2.5 / 11; fasilitas 3 / 6 / 6;
seriBarang 1 / 2 / 3; nilaiBayar = round(row.cifRupiah, 2)).

⚑ The one difference: in BC 2.6.2 **all three** `jumlahSatuan` values are
`round(row.netto, 2)` (fallback 0). BC 2.6.1 leaves the PPH one unrounded.

---

## 10. Laravel implementation notes (payload fidelity)

**Identical to the BC 2.6.1 plan §10** (hand-built ordered arrays, `array_values()` on every
list, `JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES` via `withBody()`, the
falsy-fallback helper that doesn't treat `"0"` as empty, the rounding note, `Y-m-d` dates,
`User-Agent: Mozilla/5.0`).

**Recommendation:** BC 2.6.1 and 2.6.2 payload builders look alike, but the bahan baku, the
seri-9 slot, `kodeTujuanPemasukan`, the kontainer array and the kemasan seri type all differ.
Give each document type **its own builder** rather than a shared one with flags, so a change
to one can't silently reorder the other.

---

## 11. Known quirks (replicated as-is unless noted; suggested fixes for later)

| ID | Quirk (current behaviour) | Suggested fix |
|---|---|---|
| **Q-a** | The **Send Data** button only works on the server-rendered list. After any JS list refresh it has **no click handler** (same bug in BC 2.6.1, Q-o there). | Always wire it (delegated handler). Don't copy this one. |
| Q-b | `barangTarif` is hardcoded to BM 40% / PPh 2.5% / PPN 11%, and `bahanBakuTarif` to PPN 11%. nilaiBayar = full CIF Rupiah. | Use the HS rates and computed amounts. |
| Q-c | **The Pungutan tab's 12 values are saved but never sent.** `pungutan` = one PPN row = the first barang's CIF Rupiah. Nothing auto-calculates the tab, Total included. | Send the per-type pungutan from the tab, or compute it from the barang. |
| Q-d | Entitas slots are filled by "next non-empty" (a missing Entitas 1 shifts the others). | Fixed mapping E1→3, E2→7, E3→9. |
| Q-e | A sent document can still be edited, deleted and re-sent. `idHeader` is not stored. | Lock it when sent, and store idHeader. |
| Q-f | Entities are sent from the **master**, not the typed or saved snapshot. | Send the snapshot. |
| Q-g | The barang CIF input is disabled (always 0). ndpbm/persen* are never filled. `nilaiBarang` is sent as 0. | Compute them. |
| Q-h | Root `hargaPenyerahan` / `nilaiBarang` = the **foreign** CIF. Header cifRupiah is not persisted. `jumlahKontainer` is 0 even when kontainer rows are sent. | Confirm with CEISA; send the count. |
| Q-i | Bahan baku `seriBarangDokAsal = 1` and `bahanBakuTarif.seriBahanBaku = 1` are hardcoded. | Send the stored values. |
| Q-j | Pre-fill from BC 2.6.1 copies the child lists but **not the header** (entities, valuta, ndpbm, cif, weights, cara angkut all start blank). | Copy the header too (entities, valuta, ndpbm, cif, bruto/netto, cara angkut). |
| Q-k | Add blanks the **entire header**, so the defaults (kodeKantor 071100, cara angkut 3, first entity) are lost. BC 2.6.1 restores them. | Restore the defaults on Add, like BC 2.6.1. |
| Q-l | Pre-fill doesn't copy barang ndpbm/persen*/keteranganDokumen. Copied bahan baku get netto 0 (BC 2.6.1 has none). | Copy what exists. |
| Q-m | Deleting a barang row doesn't remove or renumber its bahan baku (same as BC 2.6.1 Q-j). The fallback `seriBahanBaku` is global (BC 2.6.1 Q-k). | Same fixes as BC 2.6.1. |
| Q-n | Detail on the Transaction tab opens the form **editable**. | Read-only. |
| Q-o | No period filter on Transaction or Outstanding. | Decide. |
| Q-p | Nothing stops a BC 2.6.2 being created for a BC 2.6.1 whose goods have already returned through another (standalone) BC 2.6.2. Only the direct link is checked. | Optionally reconcile quantities. |

---

## 12. Open items for the database session (Laravel codebase)

- The BC 2.6.2 header table with an FK to the BC 2.6.1 header (nullable), and a unique
  constraint if one BC 2.6.1 may only be settled once (today it's enforced only by the
  Outstanding query).
- Child tables can share the BC 2.6.1 shape (they're identical, plus bahan baku `netto`).
  Decide whether to use separate tables per document or shared tables with a document-type
  discriminator.
- Storage for the 12 pungutan header fields, `arr_date`, `no_reg_bc`, `tgl_bc`.
- Reuse the CEISA token table and the masters. Per-route permissions.

---

## Appendix A: code lists (differences from BC 2.6.1)

- **kodeTujuanPengiriman → sent as `kodeTujuanPemasukan`:** 1 Eks-Diperbaiki,
  2 Eks-Disubkontrakkan, 3 Eks-Dipinjamkan, 4 Return Bahan Baku, 5 Pemasukan Melewati Jangka
  Waktu, 6 Lainnya.
- **kodeCaraAngkut:** 1 Laut, 2 Kereta Api, 3 Darat *(default)*, 4 Udara, 5 Pos, 6 Multimoda,
  7 Instalasi/Pipa, 8 Perairan, 9 Lainnya.
- **Entity seri slots:** 3 Pengusaha TPB, 7 Pemilik Barang, 9 Pengirim Barang.
- Jaminan, kontainer, kemasan, asal bahan baku, kantor asal and origin doc codes are the same
  as the BC 2.6.1 Appendix A.
