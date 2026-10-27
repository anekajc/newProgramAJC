# Document BC 4.1 — Functional Plan for Re-implementation (Laravel 12)

Source of truth: the Villiger ERP `document_bc41` module (JSON `add_v2` / `edit_v2` path).
It was rebuilt on the BC 2.5 pattern, so the two modules are near-identical. This plan
describes **behaviour, rules and the exact CEISA payload**, not code. The database design is
out of scope and will be decided in the Laravel codebase session.

**Read `documentbc25_plan.md` first.** Everything not listed here is **identical to BC 2.5**:
- the standalone page (no Outstanding), the toolbar and list;
- the 10-tab modal, the Transaksi tab and its calculations (Nilai Pabean, DPP, PPN, PPnBM,
  "Kurs terbaru", "Sync Netto");
- the Barang / single Bahan Baku / Kemasan / Kontainer / Dokumen collections, with no Jaminan;
- the normalisation, create/update/delete rules, CEISA login, send flow (`status == "OK"` →
  `sent = 1`, timeout 30 s), the `details` builder and the Laravel fidelity notes.

Decisions applied (carried over): copy current behaviour exactly and flag quirks (§11);
per-user CEISA login; backend + UI behaviour.

---

## 1. Overview

- BC 4.1 = customs document for **releasing goods from a bonded zone to the domestic area
  (local-origin goods)**. Like BC 2.5, it carries a tax-base block (DPP, PPN, PPnBM).
- Lifecycle: **Draft** → **Sent** (`status == "OK"`). Standalone only.
- **Differences from BC 2.5 at a glance:**

| Area | BC 2.5 | BC 4.1 |
|---|---|---|
| kodeDokumen | "25" | **"41"** |
| Header: Cara Pembayaran (`kodeCaraBayar`) | yes | **no** (field, payload key and default all removed) |
| Pungutan tab | 12 manual BM/Cukai/PDRI inputs | **Lokasi Bayar / Wajib Bayar / Bukti Bayar** (§4.1) |
| CEISA `pungutan` lines | from the Pungutan tab (11 types) | from the **Transaksi** `ppnPajak` / `ppnbmPajak` only (§9.3) |
| Entitas slots | compacted ("next non-empty") | **fixed**: E1→3, E2→7, E3→8, never shifted |
| Save validation | bruto > 0 | bruto > 0 **and** a valid Wajib Bayar |
| Extra root keys | `kodeCaraBayar` | `kodePembayar`; optionally `nomorBuktiBayar`, `tanggalBuktiBayar` (§9.1) |

---

## 2. Logical data model: differences from BC 2.5

### 2.1 Header

| Field | Change vs BC 2.5 |
|---|---|
| kodeCaraBayar | **Removed** |
| nilaiBM … nilaiPPh, nilaiTotalPungutan (12 pungutan fields) | **Removed.** BC 4.1 has no BM/Cukai/PDRI boxes. |
| **kodePembayar** (new) | string, nullable. **Wajib Bayar** = the **entitas seri** of the paying party: `"3"` (Entitas 1 Pengusaha TPB), `"7"` (Entitas 2 Pemilik Barang) or `"8"` (Entitas 3 Penerima Barang). **Required to save** (§5.1). Trimmed; empty → null. |
| **nomorBuktiBayar** (new) | string(50), nullable. Bukti Bayar number. Trimmed; empty → null. |
| **tanggalBuktiBayar** (new) | date, nullable. Bukti Bayar date. |
| kodeLokasiBayar | Not stored. Always `"1"` (BANK), from config (`KODE_LOKASI_BAYAR`). |

Everything else is the same as BC 2.5 §2.1: Transaksi fields, entitas snapshot + select ids,
`arr_date` (Tanggal Pengeluaran), `no_reg_bc`, `tgl_bc`, and the Pengesahan block.

### 2.2 Children and masters

Identical to BC 2.5 §2.2–2.5. The only difference is that `kodeDokumen` defaults/fallbacks are
**"41"**.

---

## 3. Outstanding

**None** (standalone, same as BC 2.5).

---

## 4. Screens & UI behaviour: differences from BC 2.5

### 4.1 Pungutan tab: three cards

| Card | Content |
|---|---|
| **Lokasi Bayar** | Read-only label "Cara Bayar: **BANK**". Nothing to enter; always sends `kodeLokasiBayar = "1"`. |
| **Wajib Bayar** | Select `kodePembayar`. **Options are rebuilt every time the Pungutan tab opens**, from the NPWPs currently on the Entitas tab, in Entitas-tab order:<br>1. Entitas 1 NPWP → value `"3"` (Pengusaha TPB)<br>2. Entitas 3 NPWP → value `"8"` (Penerima Barang)<br>3. Entitas 2 NPWP → value `"7"` (Pemilik Barang)<br>The option **text is the NPWP only**. An entitas with no NPWP gets no option. There's a leading blank option. The current selection is kept if its seri is still offered, otherwise it's cleared. |
| **Bukti Bayar** | `nomorBuktiBayar` (text, ≤50) and `tanggalBuktiBayar` (date). Both optional. |

There's no "Detail Barang" button on this tab (BC 2.5 has an unwired one).

### 4.2 Header tab

Nomor Pengajuan, Kantor, **Jenis TPB**, **Tujuan Pengiriman**, no_reg_bc, tgl_bc,
arr_date. There's **no Cara Pembayaran**.

### 4.3 Add / Edit / Detail

- **Add:** same as BC 2.5 (header blanked, then transaksi defaults; Jenis TPB "1", Tujuan "3"
  restored). Then Wajib Bayar options are rebuilt with **nothing selected**. Validation
  messages are cleared.
- **Edit / Detail:** load the header. The saved `kodePembayar` can't be selected until the
  options exist, so **rebuild the Wajib Bayar options from the just-loaded NPWPs, then select
  the saved seri**. After that, the BC 2.5 edit-load steps run.
- **Read-only mode** (Detail) also locks `kodePembayar`, `nomorBuktiBayar`, `tanggalBuktiBayar`.

### 4.4 Client checks before save (in this order)

1. Bruto > 0, otherwise jump to Transaksi with toast "Berat Kotor harus lebih besar dari 0".
2. **Wajib Bayar selected**, otherwise jump to Pungutan with toast "Wajib Bayar harus dipilih"
   and a red field message.

If the server rejects with `errors.kodePembayar`, the UI shows the same message and opens the
Pungutan tab.

---

## 5. Server-side rules: differences from BC 2.5

### 5.1 Validation (create and update)

Errors are collected together and returned as `{ errors: { … } }` (HTTP 200 in Villiger,
§11 Q-n):
- `bruto ≤ 0` → `errors.bruto = "Berat Kotor harus lebih besar dari 0"`.
- `kodePembayar` (trimmed) **not in {"3", "7", "8"}** →
  `errors.kodePembayar = "Wajib Bayar harus dipilih"`.

The server does **not** check that the chosen seri's entitas slot actually has an entity
(§11 Q-f).

### 5.2 Save

Same as BC 2.5 §5.3, minus kodeCaraBayar and the 12 pungutan fields, plus `kodePembayar`,
`nomorBuktiBayar`, `tanggalBuktiBayar`.

---

## 6–8. Endpoints, CEISA authentication, send

**Identical to BC 2.5 §6–§8** (list / show / kurs / create / update / delete / token
status / login / send; `status == "OK"` → `sent = 1`; timeout 30 s).

---

## 9. CEISA JSON payload — EXACT field order

> **Order matters.** Keys are listed in emit order. "Fallback" applies when the value is
> null, "", 0 or 0.0 (**not** the string "0"). "raw" = no fallback. `float(...)` = always a
> float. ⚑ marks a difference from BC 2.5.
>
> Configurable constants (same as BC 2.5, an unverified guess): `KODE_FASILITAS_TARIF = "1"`,
> `TARIF_FASILITAS = 0`, `KODE_JENIS_TARIF = "1"`, plus ⚑ `KODE_LOKASI_BAYAR = "1"`.

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
| 12 | jumlahKontainer | number | count of kontainer rows |
| 13 | kodeDokumen | string | ⚑ `"41"` |
| 14 | kodeKantor | string | raw |
| 15 | kodeLokasiBayar | string | `KODE_LOKASI_BAYAR` (`"1"`) |
| 16 | ⚑ **kodePembayar** | string | header.kodePembayar, fallback `""` (in practice "3"/"7"/"8") |
| 17 | kodeTujuanPengiriman | string | raw |
| 18 | kodeJenisTpb | string | header.kodeJenisTpb, fallback `"1"` |
| — | ~~kodeCaraBayar~~ | | ⚑ **not sent** |
| 19 | kodeValuta | string | fallback "" |
| 20 | kotaTtd | string | raw |
| 21 | namaTtd | string | raw |
| 22 | ndpbm | number (float) | fallback 0 |
| 23 | netto | number | round(header.netto, 2), fallback 0 |
| 24 | nik | string | `"0"` |
| 25 | nilaiBarang | number (float) | float(**header.hargaPenyerahan**, fallback 0) |
| 26 | nomorAju | string | raw |
| 27 | ppnPajak | number (float) | fallback 0 |
| 28 | ppnbmPajak | number (float) | fallback 0 |
| 29 | seri | number | `1` |
| 30 | tanggalAju | string | **header.tanggalTtd** `YYYY-MM-DD`, fallback "" |
| 31 | tanggalTtd | string | `YYYY-MM-DD`, fallback "" |
| 32 | tarifPpnPajak | number (float) | fallback 0 |
| 33 | tarifPpnbmPajak | number (float) | fallback 0 |
| 34 | uangMuka | number (float) | fallback 0 |
| 35 | vd | number | `0` |
| 36 | barang | array | same as BC 2.5 §9.3 (with `kodeDokumen "41"`) |
| 37 | dokumen | array | same as BC 2.5 §9.5 |
| 38 | entitas | array | ⚑ §9.2 (fixed slots) |
| 39 | jaminan | array | always `[]` |
| 40 | kemasan | array | same as BC 2.5 §9.6 |
| 41 | kontainer | array | same as BC 2.5 §9.6b |
| 42 | pengangkut | array | same as BC 2.5 §9.6c |
| 43 | pungutan | array | ⚑ §9.3 (0–2 items) |
| 44 | ⚑ **nomorBuktiBayar** | string | **only present if** header.nomorBuktiBayar is non-empty, otherwise the key is omitted |
| 45 | ⚑ **tanggalBuktiBayar** | string | **only present if** header.tanggalBuktiBayar is set (`YYYY-MM-DD`), otherwise omitted |

Keys 44–45 are **appended after `pungutan`**, at the very end, and each is independent: either,
both or neither may appear. Don't emit them as `null` or `""`.

### 9.2 ⚑ `entitas[]`: always 3 objects, **fixed slots** seri 3 → 7 → 8

Each slot reads **its own** select id, and slots are **never compacted**: selectEntitas1 →
seri 3, selectEntitas2 → seri 7, selectEntitas3 → seri 8. An empty select id (or an id
not found in the master) produces that slot with the empty-slot defaults, and the other slots
stay where they are. This is required because `kodePembayar` refers to a seri.

The key layout is **identical to BC 2.5 §9.2** (the same 7 base keys, then slot-specific keys
appended after `seriEntitas`):

| Slot | Keys in order (fallback / empty-slot value) |
|---|---|
| **seri 3** (Pengusaha TPB, from selectEntitas1) | alamatEntitas (""), kodeEntitas `"3"`, kodeJenisIdentitas ("6"), kodeStatus ("0"), namaEntitas (""), nomorIdentitas (""), seriEntitas `3`, kodeJenisApi ("02"), nomorIjinEntitas (""), tanggalIjinEntitas (`YYYY-MM-DD` / ""), nibEntitas ("") |
| **seri 7** (Pemilik Barang, from selectEntitas2) | alamatEntitas, kodeEntitas `"7"`, kodeJenisIdentitas, kodeStatus, namaEntitas, nomorIdentitas, seriEntitas `7`, kodeNegara (the entity's master country code / "") |
| **seri 8** (Penerima Barang, from selectEntitas3) | alamatEntitas, kodeEntitas `"8"`, kodeJenisIdentitas, kodeStatus, namaEntitas, nomorIdentitas, seriEntitas `8`, kodeJenisApi, nomorIjinEntitas, tanggalIjinEntitas |

Values come from the **entity master** (not the typed snapshot, §11 Q-g).

### 9.3 ⚑ `pungutan[]`: from the Transaksi tax block, non-zero only

Walk these two header fields in order. For each non-zero value, append an object:

| Order | Header field | kodeJenisPungutan |
|---|---|---|
| 1 | ppnPajak | `PPN` |
| 2 | ppnbmPajak | `PPNBM` |

Each object (same shape as BC 2.5):

| # | Key | Type | Value |
|---|---|---|---|
| 1 | idPungutan | string | running counter of emitted items (`"1"`, `"2"`) |
| 2 | kodeFasilitasTarif | string | `KODE_FASILITAS_TARIF` (`"1"`) |
| 3 | kodeJenisPungutan | string | `"PPN"` / `"PPNBM"` |
| 4 | nilaiPungutan | number | round(value, 2) |

So with PPnBM tarif 0 (the default), there is exactly one PPN line.

### 9.4 Barang, bahan baku, dokumen, kemasan, kontainer, pengangkut

**Identical to BC 2.5 §9.3–§9.6c** (key orders, computed `barangTarif` BM/PPN/PPH,
`bahanBaku` without `bahanBakuTarif`, kontainer without `merkKontainer`, a single pengangkut),
with every `kodeDokumen` constant = **"41"** (barang `kodeDokumen`, bahan baku `kodeDokumen`).
Bahan baku `kodeDokAsal` is still the stored origin doc code.

### 9.5 Skeleton example (root-level differences only; values illustrative)

```json
{
  "asalData": "S", "asuransi": 0, "biayaTambahan": 0, "biayaPengurang": 0.0,
  "bruto": 120.0, "cif": 0.0, "dasarPengenaanPajak": 10000000.0, "disclaimer": "1",
  "freight": 0, "hargaPenyerahan": 10000000.0, "jabatanTtd": "Manager",
  "jumlahKontainer": 0, "kodeDokumen": "41", "kodeKantor": "071100",
  "kodeLokasiBayar": "1", "kodePembayar": "3", "kodeTujuanPengiriman": "3",
  "kodeJenisTpb": "1", "kodeValuta": "IDR", "kotaTtd": "Jember", "namaTtd": "Budi",
  "ndpbm": 1.0, "netto": 100.0, "nik": "0", "nilaiBarang": 10000000.0,
  "nomorAju": "000041...", "ppnPajak": 1100000.0, "ppnbmPajak": 0.0, "seri": 1,
  "tanggalAju": "2026-10-26", "tanggalTtd": "2026-10-26", "tarifPpnPajak": 11.0,
  "tarifPpnbmPajak": 0.0, "uangMuka": 0.0, "vd": 0,
  "barang": [ { "...": "same as BC 2.5, kodeDokumen \"41\"" } ],
  "dokumen": [],
  "entitas": [
    { "alamatEntitas": "...", "kodeEntitas": "3", "kodeJenisIdentitas": "6", "kodeStatus": "0", "namaEntitas": "...", "nomorIdentitas": "...", "seriEntitas": 3, "kodeJenisApi": "02", "nomorIjinEntitas": "...", "tanggalIjinEntitas": "2020-01-01", "nibEntitas": "..." },
    { "alamatEntitas": "", "kodeEntitas": "7", "kodeJenisIdentitas": "6", "kodeStatus": "0", "namaEntitas": "", "nomorIdentitas": "", "seriEntitas": 7, "kodeNegara": "" },
    { "alamatEntitas": "...", "kodeEntitas": "8", "kodeJenisIdentitas": "6", "kodeStatus": "0", "namaEntitas": "...", "nomorIdentitas": "...", "seriEntitas": 8, "kodeJenisApi": "02", "nomorIjinEntitas": "", "tanggalIjinEntitas": "" }
  ],
  "jaminan": [],
  "kemasan": [],
  "kontainer": [],
  "pengangkut": [ { "idPengangkut": "1", "kodeBendera": "", "namaPengangkut": "", "nomorPengangkut": "", "kodeCaraAngkut": "3", "seriPengangkut": 1 } ],
  "pungutan": [ { "idPungutan": "1", "kodeFasilitasTarif": "1", "kodeJenisPungutan": "PPN", "nilaiPungutan": 1100000.0 } ],
  "nomorBuktiBayar": "BB-0001",
  "tanggalBuktiBayar": "2026-10-25"
}
```

(This example has an empty Entitas 2: slot 7 keeps its position with blank values. That's the
fixed-slot behaviour.)

---

## 10. Laravel implementation notes

**Identical to the BC 2.5 plan §10.** BC 4.1 specifics:
- **Conditional trailing keys:** append `nomorBuktiBayar` / `tanggalBuktiBayar` only when they
  have a value, *after* `pungutan`. Build the base array first, then add them, so they always
  land at the end.
- **Fixed entitas slots:** iterate `[[selectEntitas1, 3], [selectEntitas2, 7],
  [selectEntitas3, 8]]` and emit every slot. Never filter out empty ones.
- **Wajib Bayar options** can be built server-side or client-side, but the stored value must
  stay the **seri** ("3"/"7"/"8"), not the entity id or the NPWP.
- Validate with a Laravel FormRequest (`bruto` gt:0, `kodePembayar` in:3,7,8) and return 422.
  Remember the UI must read 422 instead of the 200 + `errors` shape (§11 Q-n).

---

## 11. Known quirks (replicated as-is; suggested fixes for later)

BC 2.5 quirks that **also apply** (same behaviour, see the BC 2.5 plan §11):
- Q-a: kodeKantor / kodeCaraAngkut blank on Add.
- Q-b: barang tariffs are always 0 (no persen inputs).
- Q-c: barang CIF is always 0.
- Q-d: `KODE_FASILITAS_TARIF` is a guess.
- Q-h: Kurs terbaru ignores the currency.
- Q-i: editable/deletable after sent, `idHeader` not stored.
- Q-j: bahan baku has no origin; global seri fallback; orphan bahan baku when a barang is deleted.
- Q-k: Detail is editable.
- Q-l: nomorAju isn't enforced.
- Q-m: tanggalAju = tanggalTtd.
- Q-n: validation errors return HTTP 200.
- Q-o: client-derived tax values are trusted.
- Q-p: no period filter.

**Fixed in BC 4.1** (don't copy from BC 2.5):
- BC 2.5 Q-e (compacted entitas slots). BC 4.1 uses fixed slots.

BC 4.1-specific:

| ID | Quirk (current behaviour) | Suggested fix |
|---|---|---|
| Q-e′ | Only **PPN and PPnBM** are sent as pungutan, and only when non-zero. There's no BM, Cukai or PPh path at all. If both are 0, `pungutan` is `[]`. | Confirm with CEISA that BC 4.1 needs nothing else. |
| Q-f | The server checks only that `kodePembayar` is 3/7/8, **not** that the chosen slot has an entity with an NPWP. A stale seri (the entity was removed after it was chosen) is accepted and sent. | Validate that the referenced selectEntitas* is set. |
| Q-g | The Wajib Bayar option text is the **NPWP only**. Two entities sharing an NPWP look identical, and the entity name isn't shown. | Show "NPWP — Nama". |
| Q-h′ | Wajib Bayar options are rebuilt only when the Pungutan tab **opens**. If the user changes an entitas and saves without reopening Pungutan, the stored seri may now point at a different party than the user intended (fixed slot, new entity). | Rebuild on every entitas change, or re-confirm on save. |
| Q-i′ | Lokasi Bayar is fixed to BANK and has no input. | Keep it, unless other payment locations are needed. |

---

## 12. Open items for the database session (Laravel codebase)

- Header table: the BC 2.5 header **minus** `kodeCaraBayar` and the 12 pungutan columns,
  **plus** `kodePembayar`, `nomorBuktiBayar`, `tanggalBuktiBayar`. Five child tables, the
  same shape as BC 2.5 (no jaminan).
- **Share or separate:** BC 2.5 and BC 4.1 differ only in header columns and two builder
  details. Decide between separate tables, or a shared table set with a `kode_dokumen`
  discriminator and nullable type-specific columns.
- Kurs master with a currency code (shared with BC 2.5). Reuse the CEISA token table and the
  masters. Per-route permissions.

---

## Appendix A: code lists

- **kodePembayar (Wajib Bayar):** 3 Pengusaha TPB (Entitas 1), 7 Pemilik Barang (Entitas 2),
  8 Penerima Barang (Entitas 3). In the dropdown the options appear in the order 3, 8, 7
  (Entitas tab order).
- **kodeLokasiBayar:** 1 Bank (fixed).
- **kodeJenisTpb, kodeTujuanPengiriman, kodeCaraAngkut, kodeValuta:** same as the BC 2.5
  Appendix A. There's no kodeCaraBayar.
- **Entity seri slots (fixed):** 3 Pengusaha TPB, 7 Pemilik Barang, 8 Penerima Barang.
