# Document BC 2.5 — Functional Plan for Re-implementation (Laravel 12)

Source of truth: the Villiger ERP `document_bc25` module (JSON `add_v2` / `edit_v2` path,
the newest and cleanest of the BC modules). This plan describes **behaviour, rules and the
exact CEISA payload**, not code. The database design is out of scope and will be decided in
the Laravel codebase session.

Companion plans: `documentbc261_plan.md` (same modal/collection framework, CEISA login,
`details` error builder and Laravel payload-fidelity notes) and `documentbc262_plan.md`. A
BC 4.1 plan would be a near-twin of this one (same pattern, see §12).

Decisions applied (carried over from the earlier plans):
- **Fidelity:** copy current behaviour exactly. Every deviation is listed in §11 *Known quirks*.
- **CEISA auth:** a per-user login modal, with the token stored per user (shared with every
  BC module).
- **Scope:** backend, the CEISA payload, and UI behaviour (not visual design).
- **No Outstanding source:** BC 2.5 is standalone in Villiger, so the plan keeps it standalone.

---

## 1. Overview

- BC 2.5 = customs document for **releasing goods from a bonded zone into the domestic market
  (TLDDP)**. Duties and taxes are payable, so the document carries a tax-base block (DPP, PPN,
  PPnBM) and document-level pungutan.
- Lifecycle: **Draft (saved)** → **Sent** (`sent = 1` only when CEISA answers `status: "OK"`).
- **Standalone only:** the user types everything. There's no Outstanding tab, no source FK,
  and nothing is written back to other documents.
- Child collections: **Barang**, **Bahan Baku** (each belongs to a barang; **no Impor/Lokal
  split**), **Kemasan**, **Kontainer (Peti Kemas)**, **Dokumen**. There's **no Jaminan**
  (always sent as `[]`).

---

## 2. Logical data model (persisted fields)

### 2.1 Document header

| Group | Field | Type | Default on Add | Notes |
|---|---|---|---|---|
| Link | id, sent | | | No source link. `isRealisasi = 0` (legacy, don't recreate). |
| Header | nomorAju | string(100) | "" | Labelled required in the form, but **not enforced** by the save (§11 Q-l) |
| | kodeKantor | string(20) | **blank** (§11 Q-a) | Form default 071100 exists but isn't applied on Add |
| | kodeJenisTpb | string | `"1"` | Select, only option: 1 Kawasan Berikat. Server fallback "1". |
| | kodeTujuanPengiriman | string | `"3"` | Select 1–5 (Appendix A), default **3 Retur** |
| | kodeCaraBayar | string | `"1"` | Select, only option: 1 Biasa/Tunai. Server fallback "1". |
| | kodeCaraAngkut | string | (blank) | Select 1–9 (Appendix A). Form default 3. |
| Internal | no_reg_bc, tgl_bc | string(200), date | blank | Not sent |
| | arr_date | date | blank | "**Tanggal Pengeluaran**". Not sent. Shown in the list. |
| Transaksi: Harga | kodeValuta | string | `"IDR"` | Select IDR/USD/EUR/CHF/SGD/JPY. Server fallback "IDR". |
| | ndpbm | decimal (4 dp) | `1.0000` | Exchange rate. "Kurs terbaru" button (§4.4). |
| | cif | decimal (2 dp) | 0.00 | Nilai CIF (valuta) |
| | cifRupiah | decimal | derived | "Nilai Pabean" = cif × ndpbm (read-only). **Persisted here**, unlike BC 2.6.1/2.6.2. |
| | hargaPenyerahan | decimal (2 dp) | 0.00 | Harga Penyerahan / Harga Jual |
| Transaksi: Pajak | uangMuka, diskon | decimal (2 dp) | 0.00 | |
| | dasarPengenaanPajak | decimal | derived | DPP = hargaPenyerahan − uangMuka − diskon (read-only) |
| | tarifPpnPajak | decimal (2 dp) | **11.00** | |
| | ppnPajak | decimal | derived | DPP × tarifPpnPajak % (read-only) |
| | tarifPpnbmPajak | decimal (2 dp) | 0.00 | |
| | ppnbmPajak | decimal | derived | DPP × tarifPpnbmPajak % (read-only) |
| Transaksi: Berat | bruto | decimal (4 dp) | 0.0000 | **Must be > 0** to save (§5.2) |
| | netto | decimal (4 dp) | 0.0000 | Manual, or the "Sync Netto Barang" button |
| Entitas 1: Pengusaha TPB | nomorIdentitasEntitas1, namaEntitas1, alamatEntitas1, nomorIjinEntitas1, tanggalIjinEntitas1, selectEntitas1 | | blank | Filled from the entity master on select. NPWP, alamat, izin are read-only. |
| Entitas 3: Penerima Barang / Pembeli BKP / Penerima JKP | nomorIdentitasEntitas3, namaEntitas3, alamatEntitas3, selectEntitas3 | | blank | Same |
| Entitas 2: Pemilik Barang | nomorIdentitasEntitas2, namaEntitas2, alamatEntitas2, selectEntitas2 | | blank | Same |
| Pungutan (tab) | nilaiBM, nilaiBMAD, nilaiBMI, nilaiBMTP, nilaiBMP, nilaiCukaiEa, nilaiCukaiCmea, nilaiCukaiCtem, nilaiPPN, nilaiPPNBM, nilaiPPh, nilaiTotalPungutan | decimal | 0.00 | Typed manually. **Non-zero values ARE sent** as pungutan lines (§9.7); Total is not. |
| Pengesahan | kotaTtd, tanggalTtd, namaTtd, jabatanTtd | | blank | `tanggalTtd` is also sent as `tanggalAju` |

NOT NULL strings (nomorAju, kodeKantor, kodeTujuanPengiriman, kodeCaraAngkut, nama/alamat
entitas, kota/nama/jabatan Ttd) are saved as `""` when empty.

### 2.2 Child: Barang

Same fields as BC 2.6.1 §2.2, with `kodeDokumen` always **"25"**. In the modal:
- The CIF (valuta) input is **disabled with no calculation**, so it's always 0 (§11 Q-c).
- **CIF Rupiah** is typed.
- There are **no persen BM/PPN/PPh inputs**, so they're saved as 0 and the computed tariffs
  are 0 (§11 Q-b).

### 2.3 Child: Bahan Baku

Same fields as BC 2.6.1 §2.3, except:
- There's **no origin (Impor/Lokal) input**. `kodeAsalBahanBaku` is saved null and sent as "0".
- **netto** is an input (sent).
- **cif is derived:** when CIF Rupiah changes, `cif = round(cifRupiah ÷ header ndpbm, 2)`
  (blank if ndpbm is 0). The cif input itself is disabled.
- `kodeDokumen` (origin doc code) and Dokumen `kodeDokumen` fall back to `"25"`.
- `seriBarangDokAsal` **is sent** (stored value, fallback 1). BC 2.6.x hardcodes 1.

### 2.4 Child: Kemasan, Kontainer, Dokumen

Identical to BC 2.6.1 §2.4, §2.5, §2.7. There's no Jaminan.

### 2.5 Masters

Same as BC 2.6.1 §2.9, plus a **Kurs master**: the latest non-null rate (Villiger
`GlobalInfo.kurs`, a single rate with no currency code).

---

## 3. Outstanding

**None.** BC 2.5 has only a Transaction tab.

---

## 4. Screens & UI behaviour

### 4.1 Main page

- Permission gate. Toolbar: **Refresh**, **Detail**, **Add**, **Edit**, **Delete**, plus a
  **Login BC** button.
- One **Transaction** tab: every BC 2.5, id desc, **no period filter**. Columns: **Nomor AJU**,
  **Tanggal** (= `arr_date`, dd-mm-yyyy), **Status** (SENT ✓ / **Send Data** button).
- The Send Data button is **correctly wired in both the server-rendered and JS-refreshed
  list** (unlike BC 2.6.1/2.6.2). The button doesn't select the row.

### 4.2 Toolbar actions

| Action | Behaviour |
|---|---|
| Add | Blank form, then apply the defaults (§2.1 "Default on Add"). Barang selection cleared. |
| Edit | Load the header + 5 collections. Empty transaksi numbers show 0. Missing valuta → IDR. Missing selects (Jenis TPB/Tujuan/Cara Bayar) get their defaults. Derived fields recalculated. |
| Detail | Same as Edit (form **editable**, §11 Q-k) |
| Delete | Confirm → delete |
| Send Data | §8 |

After save: close, refresh the list, select the saved doc.

### 4.3 Form tabs (10)

| Tab | Content |
|---|---|
| Header | nomorAju, kodeKantor, Jenis TPB, Tujuan Pengiriman, Cara Pembayaran, no_reg_bc, tgl_bc, arr_date (Tanggal Pengeluaran) |
| Entitas | Pengusaha TPB (E1), Penerima Barang (E3), Pemilik Barang (E2). Picking an entity fills its fields from the master. |
| Dokumen | Dokumen collection |
| Pengangkut | kodeCaraAngkut |
| Kemasan | Kemasan + Peti Kemas collections |
| Transaksi | Three cards (Harga, Data Untuk Keperluan Pajak, Berat), §4.4 |
| Barang | Barang collection + **one** Bahan Baku panel for the selected barang |
| Pungutan | 12 manual inputs (BM & BMT, Cukai, PDRI, Total). The "Detail Barang" button isn't wired. |
| Pernyataan | kotaTtd, tanggalTtd, namaTtd, jabatanTtd |

(There's a "Jaminan" tab in neither the UI nor the payload.)

### 4.4 Transaksi calculations (recalculated on every input change)

- `cifRupiah (Nilai Pabean) = cif × ndpbm`, rounded to 2 dp.
- `dasarPengenaanPajak (DPP) = hargaPenyerahan − uangMuka − diskon`.
- `ppnPajak = DPP × tarifPpnPajak / 100`.
- `ppnbmPajak = DPP × tarifPpnbmPajak / 100`.
- Derived fields are read-only.
- Number formatting: ndpbm, bruto, netto at 4 dp; money at 2 dp; values re-formatted on blur.
- **"Kurs terbaru" button:**
  - Valuta IDR → ndpbm = 1.0000.
  - Otherwise → ndpbm = the latest Kurs master rate (4 dp). If no rate exists, toast
    "Kurs belum diisi di master Kurs." Then recalculate.
- **"Sync Netto Barang" button:** `netto = Σ barang netto` (4 dp), with a toast showing how
  many barang were summed.
- **Bruto check:** a red field message "Berat Kotor harus lebih besar dari 0", shown once the
  user has typed or tries to save.

### 4.5 Barang ↔ Bahan Baku

Same client-side collection editors as BC 2.6.1 §4.4–4.5, except there's **one** Bahan Baku
panel (no Impor/Lokal split). The bahan baku modal title is "Bahan Baku - Barang <kode> -
<uraian>". A barang must be selected first ("Pilih barang terlebih dahulu.").

---

## 5. Server-side rules

### 5.1 Save payload (UI → server)

`{ id (edit only), header:{…§2.1}, barang:[], bahanbaku:[], kemasan:[], kontainer:[],
dokumen:[] }`. There's no source id and no jaminan.

### 5.2 Validation and normalisation

- **bruto ≤ 0 → reject** with `{ errors: { bruto: "Berat Kotor harus lebih besar dari 0" } }`
  (HTTP 200 in Villiger). The UI jumps to the Transaksi tab. This is checked on create and
  update.
- Everything else uses BC 2.6.1 §5.2 normalisation (decimals → 0, ints → null, dates → null,
  NOT NULL strings → "", `seri*` = position), plus these server fallbacks: kodeJenisTpb "1",
  kodeCaraBayar "1", kodeValuta "IDR".
- The server **trusts the client's derived values** (cifRupiah, DPP, ppn, ppnbm); it doesn't
  recompute them.

### 5.3 Create / Update / Delete

- **Create:** header + 5 collections, **one transaction**. Response
  `{ id, message: "Data telah ditambahkan." }`.
- **Update:**
  1. Not found → 404 `{ errors: { id: "Document not found." } }`.
  2. Bruto check.
  3. Overwrite the header (not `sent`).
  4. Delete and re-insert the 5 collections, all in one transaction.
  5. Response `{ id, message: "Data telah diubah." }`.
- **Delete:** delete the 5 collections, then the header, in one transaction. Response
  `{ message: "Data telah dihapus." }`.

### 5.4 Read endpoints

- List (id, nomorAju, tanggal = arr_date, sent).
- Show header (dates `YYYY-MM-DD` or "").
- Show barang / bahan baku / kemasan / kontainer / dokumen.
- **Kurs terbaru** → `{ kurs: number|null }`.

---

## 6. Endpoint list (logical)

| Method | Purpose |
|---|---|
| GET | Page |
| GET | List (JSON) |
| GET | Show document (header + 5 collections) |
| GET | Latest kurs |
| POST / PUT / DELETE | Create / Update / Delete |
| GET / POST | CEISA token status / CEISA login |
| POST | Send `{ id }` |

---

## 7. CEISA authentication

**Identical to the BC 2.6.1 plan §7.**

---

## 8. Send to CEISA

**Identical to the BC 2.6.1 plan §8:**
- Token check → 401.
- `POST https://apis-gw.beacukai.go.id/openapi/document`, timeout **30 s**.
- HTTP error → same status; network → 500; non-JSON → 502.
- **`status == "OK"` → `sent = 1`**, with `{message, idHeader}` (idHeader not stored).
- Any other status → 400 with details.

The same `details` builder applies. Only the payload (§9) differs.

---

## 9. CEISA JSON payload — EXACT field order

> **Order matters.** Keys are listed in emit order. "Fallback" applies when the value is
> null, "", 0 or 0.0 (**not** the string "0"). "raw" = no fallback (may be `null`).
> `float(...)` = always emitted as a float (`0.0`).
>
> **Configurable constants** (Villiger module constants, an unverified guess, §11 Q-d):
> `KODE_FASILITAS_TARIF = "1"` (dibayar), `TARIF_FASILITAS = 0`, `KODE_JENIS_TARIF = "1"`
> (advalorum). Keep them in config, not hardcoded.

### 9.1 Root object

| # | Key | JSON type | Value |
|---|---|---|---|
| 1 | asalData | string | `"S"` |
| 2 | asuransi | number | `0` |
| 3 | biayaTambahan | number | `0` |
| 4 | biayaPengurang | number (float) | float(**header.diskon**, fallback 0) |
| 5 | bruto | number (float) | float(header.bruto, fallback 0) |
| 6 | cif | number (float) | float(header.cif, fallback 0) |
| 7 | dasarPengenaanPajak | number (float) | float(header.dasarPengenaanPajak, fallback 0) |
| 8 | disclaimer | string | `"1"` |
| 9 | freight | number | `0` |
| 10 | hargaPenyerahan | number (float) | float(header.hargaPenyerahan, fallback 0) |
| 11 | jabatanTtd | string | raw |
| 12 | jumlahKontainer | number | **count of kontainer rows** |
| 13 | kodeDokumen | string | `"25"` |
| 14 | kodeKantor | string | raw |
| 15 | kodeLokasiBayar | string | `"1"` (hardcoded: Bank) |
| 16 | kodeTujuanPengiriman | string | raw |
| 17 | kodeJenisTpb | string | header.kodeJenisTpb, fallback `"1"` |
| 18 | kodeCaraBayar | string | header.kodeCaraBayar, fallback `"1"` |
| 19 | kodeValuta | string | fallback "" |
| 20 | kotaTtd | string | raw |
| 21 | namaTtd | string | raw |
| 22 | ndpbm | number (float) | float(header.ndpbm, fallback 0) |
| 23 | netto | number | round(header.netto, 2), fallback 0 |
| 24 | nik | string | `"0"` |
| 25 | nilaiBarang | number (float) | float(**header.hargaPenyerahan**, fallback 0) |
| 26 | nomorAju | string | raw |
| 27 | ppnPajak | number (float) | float(header.ppnPajak, fallback 0) |
| 28 | ppnbmPajak | number (float) | float(header.ppnbmPajak, fallback 0) |
| 29 | seri | number | `1` |
| 30 | tanggalAju | string | **header.tanggalTtd** `YYYY-MM-DD`, fallback "" |
| 31 | tanggalTtd | string | `YYYY-MM-DD`, fallback "" |
| 32 | tarifPpnPajak | number (float) | float(header.tarifPpnPajak, fallback 0) |
| 33 | tarifPpnbmPajak | number (float) | float(header.tarifPpnbmPajak, fallback 0) |
| 34 | uangMuka | number (float) | float(header.uangMuka, fallback 0) |
| 35 | vd | number | `0` |
| 36 | barang | array | §9.3 |
| 37 | dokumen | array | §9.5 |
| 38 | entitas | array | §9.2 (always 3) |
| 39 | jaminan | array | always `[]` |
| 40 | kemasan | array | §9.6 |
| 41 | kontainer | array | §9.6b |
| 42 | pengangkut | array | §9.6c (always 1) |
| 43 | pungutan | array | §9.7 (0..11 items) |

### 9.2 `entitas[]`: always 3 objects, order seri 3 → 7 → 8

Slot filling: selectEntitas1, selectEntitas2, selectEntitas3 **in that order, skipping empty
ones** (compacted, §11 Q-e). Values come from the **entity master**. Normal mapping: Entitas 1
**Pengusaha TPB** → 3, Entitas 2 **Pemilik Barang** → 7, Entitas 3 **Penerima Barang** → 8.

Every slot starts with the same 7 keys, then slot-specific keys are **appended** after
`seriEntitas`:

**Slot seri 3**

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | ("") | "" |
| 2 | kodeEntitas | string | `"3"` | "3" |
| 3 | kodeJenisIdentitas | string | ("6") | "6" |
| 4 | kodeStatus | string | ("0") | "0" |
| 5 | namaEntitas | string | ("") | "" |
| 6 | nomorIdentitas | string | ("") | "" |
| 7 | seriEntitas | number | `3` | 3 |
| 8 | kodeJenisApi | string | ("02") | "02" |
| 9 | nomorIjinEntitas | string | ("") | "" |
| 10 | tanggalIjinEntitas | string | `YYYY-MM-DD` ("") | "" |
| 11 | nibEntitas | string | ("") | "" |

**Slot seri 7**

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | ("") | "" |
| 2 | kodeEntitas | string | `"7"` | "7" |
| 3 | kodeJenisIdentitas | string | ("6") | "6" |
| 4 | kodeStatus | string | ("0") | "0" |
| 5 | namaEntitas | string | ("") | "" |
| 6 | nomorIdentitas | string | ("") | "" |
| 7 | seriEntitas | number | `7` | 7 |
| 8 | kodeNegara | string | entity's master country code ("") | "" |

**Slot seri 8**

| # | Key | Type | Value (fallback) | Empty slot |
|---|---|---|---|---|
| 1 | alamatEntitas | string | ("") | "" |
| 2 | kodeEntitas | string | `"8"` | "8" |
| 3 | kodeJenisIdentitas | string | ("6") | "6" |
| 4 | kodeStatus | string | ("0") | "0" |
| 5 | namaEntitas | string | ("") | "" |
| 6 | nomorIdentitas | string | ("") | "" |
| 7 | seriEntitas | number | `8` | 8 |
| 8 | kodeJenisApi | string | ("02") | "02" |
| 9 | nomorIjinEntitas | string | ("") | "" |
| 10 | tanggalIjinEntitas | string | `YYYY-MM-DD` ("") | "" |

### 9.3 `barang[]`: one per Barang row (no duplicate keys)

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | idBarang | string | `"0"` |
| 2 | cif | number | row.cif (raw) → in practice 0 |
| 3 | cifRupiah | number | round(row.cifRupiah, 2) (0) |
| 4 | diskon | number | (0) |
| 5 | freight | number | (0) |
| 6 | hargaEkspor | number | (0) |
| 7 | hargaPenyerahan | number | (0) |
| 8 | hargaPerolehan | number | (0) |
| 9 | hargaSatuan | number | (0) |
| 10 | isiPerKemasan | number | (0) |
| 11 | jumlahKemasan | number | raw |
| 12 | jumlahSatuan | number | raw |
| 13 | kodeAsalBahanBaku | string | `"0"` |
| 14 | kodeAsalBarang | string | `"0"` |
| 15 | kodeBarang | string | raw |
| 16 | kodeDokumen | string | `"25"` |
| 17 | kodeJenisKemasan | string/null | raw |
| 18 | kodeKategoriBarang | string/null | raw |
| 19 | kodeNegaraAsal | string | raw |
| 20 | kodePerhitungan | string | (`"0"`) |
| 21 | kodeSatuanBarang | string | raw |
| 22 | merk | string | (`"-"`) |
| 23 | ndpbm | number | (0) |
| 24 | netto | number | round(row.netto, 2) (0) |
| 25 | nilaiBarang | number | (0) → never set → 0 |
| 26 | nilaiDiskon | number | `0` |
| 27 | nilaiJasa | number | round(row.cifRupiah, 2) (0) |
| 28 | posTarif | string | raw |
| 29 | seriBarang | number | raw |
| 30 | spesifikasiLain | string | (`"0"`) |
| 31 | tipe | string | (`"0"`) |
| 32 | uangMuka | number | `0` |
| 33 | ukuran | string | (`"0"`) |
| 34 | uraian | string | raw |
| 35 | bahanBaku | array | §9.4: rows whose seriBarang = this seriBarang |
| 36 | barangTarif | array | §9.3.1: always 3 objects |
| 37 | barangDokumen | array | `[]` |

There are no `volume` or `persen*` keys (unlike BC 2.6.x).

#### 9.3.1 `barang[].barangTarif[]`: **computed**, always 3 objects in order **BM, PPN, PPH**

Calculation per barang:
- `cifRp = row.cifRupiah` (fallback 0).
- `BM = cifRp × persenBM / 100`.
- `base = cifRp + BM`.
- `PPN = base × persenPPN / 100`.
- `PPH = base × persenPPh / 100`.
- `jumlah = round(row.netto, 2)`.

Since persen* has no input, all three come out as tarif 0 / nilaiBayar 0 (§11 Q-b).

Every object has the **same key order**:

| # | Key | Type | Value |
|---|---|---|---|
| 1 | kodeJenisTarif | string | `KODE_JENIS_TARIF` (`"1"`) |
| 2 | kodeFasilitasTarif | string | `KODE_FASILITAS_TARIF` (`"1"`) |
| 3 | kodeJenisPungutan | string | `"BM"` / `"PPN"` / `"PPH"` |
| 4 | kodeSatuanBarang | string | row.kodeSatuanBarang |
| 5 | jumlahSatuan | number | round(row.netto, 2) (0) |
| 6 | tarif | number | row.persenBM / persenPPN / persenPPh (fallback 0) |
| 7 | tarifFasilitas | number | `TARIF_FASILITAS` (`0`) |
| 8 | nilaiBayar | number | round(BM / PPN / PPH, 2) |
| 9 | nilaiFasilitas | number | `0` |
| 10 | nilaiSudahDilunasi | number | `0` |
| 11 | seriBarang | number | `1` / `2` / `3` (the tariff line's position, not the barang seri) |

### 9.4 `barang[].bahanBaku[]`: **no `bahanBakuTarif`**

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | cif | number | (0) |
| 2 | cifRupiah | number | round(bb.cifRupiah, 2) (0) |
| 3 | hargaPenyerahan | number | (0) |
| 4 | hargaPerolehan | number | (0) |
| 5 | jumlahSatuan | number | (0) |
| 6 | kodeSatuanBarang | string | raw |
| 7 | kodeAsalBahanBaku | string | (`"0"`), always "0" (no input) |
| 8 | kodeBarang | string | raw |
| 9 | kodeDokAsal | string | **bb.kodeDokumen** (origin doc code) |
| 10 | kodeDokumen | string | `"25"` |
| 11 | kodeKantor | string | **bb.kodeKantorAsal** |
| 12 | merkBarang | string | ("") |
| 13 | ndpbm | number | (0) → always 0 |
| 14 | netto | number | round(bb.netto, 2) (0) |
| 15 | nomorAjuDokAsal | string/null | raw |
| 16 | nomorDaftarDokAsal | string | raw |
| 17 | posTarif | string | raw |
| 18 | seriBahanBaku | number | raw |
| 19 | seriBarang | number | raw |
| 20 | seriBarangDokAsal | number | **stored value**, fallback 1 |
| 21 | seriIjin | number | (`1`) → always 1 |
| 22 | spesifikasiLainBarang | string | ("") |
| 23 | tanggalDaftarDokAsal | string | `YYYY-MM-DD` ("") |
| 24 | tipeBarang | string | ("") |
| 25 | ukuranBarang | string | ("") |
| 26 | uraianBarang | string | raw |
| 27 | nilaiJasa | number | (0) |

### 9.5 `dokumen[]`: same as BC 2.6.1

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idDokumen | string | `"0"` |
| 2 | kodeDokumen | string | raw |
| 3 | nomorDokumen | string/null | raw |
| 4 | seriDokumen | number | raw |
| 5 | tanggalDokumen | string/null | `YYYY-MM-DD` or null |

### 9.6 `kemasan[]`

| # | Key | Type | Value (fallback) |
|---|---|---|---|
| 1 | jumlahKemasan | number(int) | (0) |
| 2 | kodeJenisKemasan | string | ("") |
| 3 | seriKemasan | number | int(stored seri), fallback 1 |
| 4 | merkKemasan | string | ("") |

### 9.6b `kontainer[]` (no `merkKontainer`, unlike BC 2.6.2)

| # | Key | Type | Value |
|---|---|---|---|
| 1 | kodeJenisKontainer | string/null | raw |
| 2 | kodeTipeKontainer | string/null | raw |
| 3 | kodeUkuranKontainer | string/null | raw |
| 4 | nomorKontainer | string/null | raw |
| 5 | seriKontainer | number | raw |

### 9.6c `pengangkut[]`: exactly 1 (same as BC 2.6.1)

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idPengangkut | string | `"1"` |
| 2 | kodeBendera | string | `""` |
| 3 | namaPengangkut | string | `""` |
| 4 | nomorPengangkut | string | `""` |
| 5 | kodeCaraAngkut | string | header.kodeCaraAngkut ("") |
| 6 | seriPengangkut | number | `1` |

### 9.7 `pungutan[]`: from the **Pungutan tab**, non-zero values only

Walk these header fields **in this fixed order**. For each one with a non-zero value, append
an object. Zero fields are skipped, so the array can be empty. `nilaiTotalPungutan` is never
sent.

| Order | Header field | kodeJenisPungutan |
|---|---|---|
| 1 | nilaiBM | `BM` |
| 2 | nilaiBMAD | `BMAD` |
| 3 | nilaiBMI | `BMI` |
| 4 | nilaiBMTP | `BMTP` |
| 5 | nilaiBMP | `BMP` |
| 6 | nilaiCukaiEa | `CEA` |
| 7 | nilaiCukaiCmea | `CMEA` |
| 8 | nilaiCukaiCtem | `CTEM` |
| 9 | nilaiPPN | `PPN` |
| 10 | nilaiPPNBM | `PPNBM` |
| 11 | nilaiPPh | `PPH` |

Each object:

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idPungutan | string | **running counter of emitted items** as a string (`"1"`, `"2"`, …), not the table order |
| 2 | kodeFasilitasTarif | string | `KODE_FASILITAS_TARIF` (`"1"`) |
| 3 | kodeJenisPungutan | string | code from the table above |
| 4 | nilaiPungutan | number | round(value, 2) |

### 9.8 Skeleton example (values illustrative)

```json
{
  "asalData": "S", "asuransi": 0, "biayaTambahan": 0, "biayaPengurang": 0.0,
  "bruto": 120.0, "cif": 0.0, "dasarPengenaanPajak": 10000000.0, "disclaimer": "1",
  "freight": 0, "hargaPenyerahan": 10000000.0, "jabatanTtd": "Manager",
  "jumlahKontainer": 0, "kodeDokumen": "25", "kodeKantor": "071100",
  "kodeLokasiBayar": "1", "kodeTujuanPengiriman": "3", "kodeJenisTpb": "1",
  "kodeCaraBayar": "1", "kodeValuta": "IDR", "kotaTtd": "Jember", "namaTtd": "Budi",
  "ndpbm": 1.0, "netto": 100.0, "nik": "0", "nilaiBarang": 10000000.0,
  "nomorAju": "000025...", "ppnPajak": 1100000.0, "ppnbmPajak": 0.0, "seri": 1,
  "tanggalAju": "2026-10-26", "tanggalTtd": "2026-10-26", "tarifPpnPajak": 11.0,
  "tarifPpnbmPajak": 0.0, "uangMuka": 0.0, "vd": 0,
  "barang": [
    {
      "idBarang": "0", "cif": 0.0, "cifRupiah": 10000000.0, "diskon": 0, "freight": 0,
      "hargaEkspor": 0, "hargaPenyerahan": 0, "hargaPerolehan": 0, "hargaSatuan": 0,
      "isiPerKemasan": 0, "jumlahKemasan": 1.0, "jumlahSatuan": 10.0,
      "kodeAsalBahanBaku": "0", "kodeAsalBarang": "0", "kodeBarang": "SP-01",
      "kodeDokumen": "25", "kodeJenisKemasan": "BX", "kodeKategoriBarang": null,
      "kodeNegaraAsal": "ID", "kodePerhitungan": "0", "kodeSatuanBarang": "PCE",
      "merk": "-", "ndpbm": 0.0, "netto": 100.0, "nilaiBarang": 0, "nilaiDiskon": 0,
      "nilaiJasa": 10000000.0, "posTarif": "84879000", "seriBarang": 1,
      "spesifikasiLain": "0", "tipe": "0", "uangMuka": 0, "ukuran": "0",
      "uraian": "BEARING",
      "bahanBaku": [],
      "barangTarif": [
        { "kodeJenisTarif": "1", "kodeFasilitasTarif": "1", "kodeJenisPungutan": "BM", "kodeSatuanBarang": "PCE", "jumlahSatuan": 100.0, "tarif": 0.0, "tarifFasilitas": 0, "nilaiBayar": 0.0, "nilaiFasilitas": 0, "nilaiSudahDilunasi": 0, "seriBarang": 1 },
        { "...": "PPN, seriBarang 2" },
        { "...": "PPH, seriBarang 3" }
      ],
      "barangDokumen": []
    }
  ],
  "dokumen": [ { "idDokumen": "0", "kodeDokumen": "380", "nomorDokumen": "INV-1", "seriDokumen": 1, "tanggalDokumen": "2026-10-20" } ],
  "entitas": [
    { "alamatEntitas": "...", "kodeEntitas": "3", "kodeJenisIdentitas": "6", "kodeStatus": "0", "namaEntitas": "...", "nomorIdentitas": "...", "seriEntitas": 3, "kodeJenisApi": "02", "nomorIjinEntitas": "...", "tanggalIjinEntitas": "2020-01-01", "nibEntitas": "..." },
    { "alamatEntitas": "...", "kodeEntitas": "7", "kodeJenisIdentitas": "6", "kodeStatus": "0", "namaEntitas": "...", "nomorIdentitas": "...", "seriEntitas": 7, "kodeNegara": "ID" },
    { "alamatEntitas": "...", "kodeEntitas": "8", "kodeJenisIdentitas": "6", "kodeStatus": "0", "namaEntitas": "...", "nomorIdentitas": "...", "seriEntitas": 8, "kodeJenisApi": "02", "nomorIjinEntitas": "", "tanggalIjinEntitas": "" }
  ],
  "jaminan": [],
  "kemasan": [ { "jumlahKemasan": 1, "kodeJenisKemasan": "BX", "seriKemasan": 1, "merkKemasan": "" } ],
  "kontainer": [],
  "pengangkut": [ { "idPengangkut": "1", "kodeBendera": "", "namaPengangkut": "", "nomorPengangkut": "", "kodeCaraAngkut": "3", "seriPengangkut": 1 } ],
  "pungutan": [ { "idPungutan": "1", "kodeFasilitasTarif": "1", "kodeJenisPungutan": "PPN", "nilaiPungutan": 1100000.0 } ]
}
```

---

## 10. Laravel implementation notes (payload fidelity)

**Identical to the BC 2.6.1 plan §10** (hand-built ordered arrays, `array_values()` on every
list, `JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES` via `withBody()`, the
falsy-fallback helper that doesn't treat `"0"` as empty, the rounding note, `Y-m-d` dates,
`User-Agent: Mozilla/5.0`).

BC 2.5 specifics:
- The **entitas** objects are built as *base keys + appended slot keys* (§9.2). Build each slot
  literally in that order; don't reuse the BC 2.6.x slot shapes.
- **pungutan** is variable-length. `idPungutan` counts only the items emitted, so build the
  list first and number as you append.
- Put `KODE_FASILITAS_TARIF`, `TARIF_FASILITAS`, `KODE_JENIS_TARIF` and `kodeLokasiBayar` in
  config.

---

## 11. Known quirks (replicated as-is; suggested fixes for later)

| ID | Quirk (current behaviour) | Suggested fix |
|---|---|---|
| Q-a | Add blanks the header, then restores only Jenis TPB / Tujuan / Cara Bayar / transaksi defaults. **kodeKantor (071100) and kodeCaraAngkut (3) start blank.** | Restore them on Add too. |
| Q-b | Barang has no persen BM/PPN/PPh inputs, so the computed `barangTarif` always sends tarif 0 / nilaiBayar 0. | Load the rates from the HS master when HS is picked (like BC 2.3), or add inputs. |
| Q-c | The barang CIF (valuta) input is disabled with no formula, so it's always 0 (bahan baku derives its cif; barang doesn't). | `cif = cifRupiah ÷ ndpbm`, like bahan baku. |
| Q-d | `KODE_FASILITAS_TARIF = "1"` / `TARIF_FASILITAS = 0` are an **unverified guess**, not yet confirmed by a CEISA test. | Verify against a CEISA test response. |
| Q-e | Entitas slots are compacted ("next non-empty"), so a missing Entitas 1 shifts the others. BC 4.1 already uses fixed slots. | Fixed mapping E1→3, E2→7, E3→8. |
| Q-f | Entities are sent from the **master**, not the typed or saved snapshot. | Send the snapshot. |
| Q-g | The Pungutan tab is typed manually: not computed from barang or Transaksi, and Total is neither calculated nor sent. Nothing checks it against `ppnPajak`. | Pre-fill PPN from ppnPajak, compute Total, and validate. |
| Q-h | "Kurs terbaru" uses the single latest master rate **regardless of currency** (the master has no currency code). | Per-currency kurs table. |
| Q-i | A sent document can still be edited, deleted and re-sent. `idHeader` is not stored. | Lock it when sent, and store idHeader. |
| Q-j | Bahan baku has no Impor/Lokal origin (always "0"). The fallback `seriBahanBaku` is global. Deleting a barang doesn't remove or renumber its bahan baku. | Add an origin if CEISA needs it. Same fixes as BC 2.6.1 Q-j/Q-k. |
| Q-k | Detail opens the form **editable** (same as Edit). | Read-only. |
| Q-l | `nomorAju` is labelled required, but the save doesn't enforce it, so a blank nomorAju can be saved and sent. | Require it server-side. |
| Q-m | `tanggalAju` = tanggalTtd (no separate field). | Confirm with CEISA, or add the field. |
| Q-n | The bruto error is returned as HTTP 200 with `errors` (not 422). | Use 422 in Laravel validation, and have the UI read it. |
| Q-o | The server trusts the client-derived cifRupiah / DPP / PPN / PPnBM. | Recompute server-side before saving and sending. |
| Q-p | No period filter on the list. | Decide. |

---

## 12. Open items for the database session (Laravel codebase)

- The header table (including the transaksi tax fields and 12 pungutan fields) + 5 child
  tables (no jaminan). Shapes match BC 2.6.x apart from bahan baku `netto`, the missing origin
  column and the missing jaminan.
- **BC 4.1 is a twin of this module** (same standalone pattern, transaksi tab and no jaminan).
  Its differences:
  - No Cara Bayar.
  - Pungutan = Lokasi Bayar / Wajib Bayar (`kodePembayar` = entitas seri) / Bukti Bayar.
  - Pungutan lines come from ppnPajak/ppnbmPajak.
  - **Fixed** entitas slots.

  Decide whether to share tables or a base class between BC 2.5 and BC 4.1.
- A Kurs master with a currency code.
- Reuse the CEISA token table and the masters. Per-route permissions.

---

## Appendix A: code lists

- **kodeJenisTpb:** 1 Kawasan Berikat (only option).
- **kodeTujuanPengiriman:** 1 Penyerahan BKP, 2 Penyerahan JKP, 3 Retur *(default)*,
  4 Non Penyerahan, 5 Lainnya.
- **kodeCaraBayar:** 1 Biasa/Tunai (only option).
- **kodeCaraAngkut:** 1 Laut, 2 Kereta Api, 3 Darat (form default), 4 Udara, 5 Pos,
  6 Multimoda, 7 Instalasi/Pipa, 8 Perairan, 9 Lainnya.
- **kodeValuta:** IDR *(default)*, USD, EUR, CHF, SGD, JPY.
- **Fasilitas tarif reference** (from the Villiger constant comment): 1 Dibayar,
  3 Ditangguhkan, 5 Dibebaskan, 6 Tidak Dipungut.
- **Entity seri slots:** 3 Pengusaha TPB, 7 Pemilik Barang, 8 Penerima Barang.
- Kontainer, kemasan and origin doc codes are the same as the BC 2.6.1 Appendix A.
