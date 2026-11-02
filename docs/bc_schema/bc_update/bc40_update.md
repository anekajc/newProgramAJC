# BC 4.0 — update to the OFFICIAL CEISA payload schema

Hand-over spec for re-applying, in the **Villiger Flask app**, the changes already made in the Laravel app
(`D:\newProgramAJC`). It is a **diff against the Villiger behaviour documented in `docs/documentbc40_plan.md`**
(§9 payload, §11 quirks): read that plan first, then apply every section here.

The structure of the Villiger BC 4.0 payload already matches the official schema (all key names, types, enums, consts and
the entitas order 3 / 7 / 9 pass). What fails is **empty / unchecked input**, a few values whose meaning disagrees with the
official description, and one misspelt key. So this is a small payload change plus the send-time check.

- Official schema: `docs/bc_schema/bc40.json`. Copy it into the Flask repo and read it at runtime.
- The send-time validator, the 422 / dialog / preview contract and the message catalogue are the **same as BC 2.3** —
  implement them once, following **`bc23_update.md` §2** (BC 2.5 does the same), then use the BC 4.0 options in §3 below.
  (If this file is applied alone, copy that section of `bc23_update.md` first. BC 4.0 has no currency, so the ISO-currency
  part of BC 2.3 does not apply.)
- Laravel reference implementation: `app/Services/BeaCukai/Bc40PayloadBuilder.php`,
  `app/Http/Controllers/DocumentBc/DocumentBc40Controller.php` (`WAJIB_ISI`, `masalahKirim`, `spKirim`, `spPreview`),
  `app/Services/BeaCukai/CeisaSchemaValidator.php` (extra labels), `resources/views/documentbc/documentbc40.blade.php`,
  tests `tests/Unit/Bc40PayloadBuilderTest.php` and `tests/Unit/Bc40SchemaTest.php`.
- Messages are Indonesian, exactly as written.
- Rule chosen with the user: **saving is never blocked by the schema check; every problem is listed when the user tries
  to SEND and in the Preview JSON dialog.**

---

## 1. Payload changes (replaces parts of plan §9)

Helpers are the same as in the plan: `orDefault(v, d)` = `d` when `v` is null, `""`, `0`, `0.0`; `flt(v)` = float with
fallback 0. New helper `bagi(total, weights)` — see §1.2.

### 1.1 Root (32 keys, unchanged order and values)

Nothing changes in the root order or values. Reminders that the validator now enforces:
`nomorAju` must match `^[A-Za-z0-9]{26}$` (exactly 26 letters/digits); `tanggalAju` and `tanggalTtd` stay `null` when
empty and are reported as "wajib diisi"; `kodeKantor`, `kodeJenisTpb`, `kotaTtd`, `namaTtd`, `jabatanTtd` stay `null` when
empty (reported the same way). `idPengguna` is still the logged-in ERP username (optional in the schema).

### 1.2 `barang[]` — 32 keys become 31, three values change

Remove the key **`isiPerkemasan`** (Villiger's misspelling; the official key is `isiPerKemasan` with a capital K, it is
optional, and CEISA already ignored the misspelt one). Do not send either spelling. Resulting key order:

```
asuransi, bruto, cif, diskon, hargaEkspor, hargaPenyerahan, hargaSatuan, jumlahKemasan, jumlahRealisasi, jumlahSatuan,
kodeBarang, kodeDokumen, kodeJenisKemasan, kodeSatuanBarang, merk, netto, nilaiBarang, posTarif, seriBarang,
spesifikasiLain, tipe, ukuran, uraian, volume, cifRupiah, hargaPerolehan, kodeAsalBahanBaku, ndpbm, uangMuka, nilaiJasa,
barangTarif
```

Value changes (everything else as in plan §9.4):

| Key | Old (plan §9.4) | New |
|---|---|---|
| `jumlahRealisasi` | row `hargaPenyerahan` (money in a quantity field, plan Q-j) | **row `jumlahSatuan`** (float, `null` if empty) |
| `nilaiBarang` | `0` | **`round(row.hargaPenyerahan, 2)`** as float (schema allows 2 decimals) |
| `bruto`, `uangMuka`, `nilaiJasa` | the header value copied onto EVERY row (plan Q-g) | **header total split across the rows** (below) |

**Split (`bagi`)** — so the rows add up to the header instead of repeating it:

```
bagi(total, weights):                    # weights = list, one per barang row (rows sorted by seriBarang)
    if no rows: return []
    if sum(weights) <= 0: weights = [1]*n     # equal split
    out = []; remaining = total
    for i, w in enumerate(weights):
        part = round(remaining, 4) if i == n-1 else round(total * w / sum(weights), 4)
        out.append(part); remaining -= part
    return out
```

- `bruto` rows = `bagi(flt(header.bruto), [row.netto ...])` (proportional to netto).
- `uangMuka` rows = `bagi(flt(header.uangMuka), [row.hargaPenyerahan ...])`.
- `nilaiJasa` rows = `bagi(flt(header.nilaiJasa), [row.hargaPenyerahan ...])`.
- Four decimals everywhere (the schema's `multipleOf 0.0001`); the LAST row takes the rounding remainder, so the sum
  equals the header. Example: bruto 100 over three equal rows → `33.3333, 33.3333, 33.3334`. One row keeps the whole value.
- Root `bruto`, `uangMuka`, `nilaiJasa` are unchanged (they are the totals).

`barangTarif[]` (the PPN block) is **unchanged on purpose**: `nilaiBayar` 0, `nilaiFasilitas` 0, `nilaiSudahDilunasi` 0,
`tarif` 11, `tarifFasilitas` 100, `kodeFasilitasTarif` "3", `jumlahSatuan` = row netto. The root `pungutan` (PPN, fasilitas 3,
`nilaiPungutan` 0) is unchanged too. These are tax values the user decided to leave as they are.

### 1.3 `entitas[]` — three FIXED slots, all mandatory

Replace the "next non-empty slot" fill (plan §9.2 / Q-d) with fixed slots:

| Slot | Page block | `seriEntitas` / `kodeEntitas` |
|---|---|---|
| `entitas[0]` | Entitas 1 — Pengusaha TPB (`selectEntitas1`) | 3 |
| `entitas[1]` | Entitas 2 — Pemilik Barang (`selectEntitas2`) | 7 |
| `entitas[2]` | Entitas 3 — Pengirim Barang (`selectEntitas3`) | 9 |

- An empty slot is NOT filled from another one. It is still sent, with empty values (`""`) and the existing defaults
  (`kodeJenisApi` "02", `kodeJenisIdentitas` "6", `kodeStatus` "0"; `tanggalIjinEntitas` `""`), and the validator reports it.
- Key sets and key order (slot 3: `seriEntitas` before `tanggalIjinEntitas`; slots 7 and 9 identical) are unchanged.
- Values still come from the entity MASTER at send time (plan Q-e).

### 1.4 `dokumen[]` and `kemasan[]`

- `dokumen[]`: when a row's `nomorDokumen` is empty send `""` (was `null`); when `tanggalDokumen` is empty send `""` (was
  `null`). Both then show up as "wajib diisi" (the date format check rejects `""`). Key order unchanged.
- `kemasan[]`: still emitted only when header `kodeJenisKemasan` or `jumlahKemasan` is non-empty. Now `jumlahKemasan` is
  `int(...)` (so `0` stays `0`, it was `null`), `kodeJenisKemasan` is a string (`""` when empty) and `merkKemasan` is a
  string (`""` when empty). Key order unchanged (jumlah, jenis, merk, seri).

---

## 2. UI changes (BC 4.0 page)

1. **Nomor Pengajuan** input: `maxlength` 26 (was 100); info text says it must be exactly 26 letters/digits when sent. The
   DB column stays wide; the 26 check happens at send (§3), saving is not blocked.
2. **Info texts** (Indonesian, as in the Laravel page):
   - Transaksi / Header: "Nomor Pengajuan disimpan huruf besar dan harus tepat 26 huruf/angka saat dikirim ke CEISA. …"
   - Entitas: add "Ketiga entitas wajib dipilih saat dikirim; Pengusaha TPB juga butuh Nomor Izin dan Tanggal Izin di master."
   - Dokumen: add "Dokumen tidak wajib ada, tetapi baris yang ada harus lengkap (jenis, nomor, tanggal) saat dikirim."
   - Kemasan: "Kemasan wajib diisi saat dikirim ke CEISA (Jenis Kemasan terisi dan Jumlah Kemasan lebih dari 0). BC 4.0 tidak punya peti kemas."
3. **Send**: a 422 response `{message, details[]}` opens a blocking dialog (alertify alert, scrollable, one `<li>` per
   problem) instead of the error toast. Other errors keep the toast. (Same code as BC 2.3 / 2.5.)
4. **Preview JSON**: the endpoint now returns `{json, masalah}` (was the raw JSON text). The modal shows a green box
   "Lolos pemeriksaan skema CEISA, siap dikirim." or a red box "Belum bisa dikirim: N masalah ditemukan." with the list,
   above the JSON. Add the `#previewMasalah` container and read `res.json` / `res.masalah`. There is no problem
   with showing the JSON for an incomplete document; the check is a warning only.

No data-model change and no new DB column is needed for BC 4.0.

---

## 3. Send-time validation options for BC 4.0

Use the generic validator from `bc23_update.md` §2.2 with `schema = bc40.json` and these options.

**`bagian`** (labels for the message path):

```
barang      → "Barang"
barangTarif → ["Tarif PPN"]
dokumen     → "Dokumen"
kemasan     → "Kemasan"
entitas     → ["Pengusaha TPB", "Pemilik Barang", "Pengirim Barang"]
pengangkut  → "Pengangkut"
pungutan    → "Pungutan"
```

**`sisa`** (arrays whose last schema applies to the extra rows): `["barang", "dokumen"]`.

**`wajibIsi`** (flat paths that may not be `""` although the schema only asks for a string):

```
kodeKantor, kodeJenisTpb, kodeTujuanPengiriman, kotaTtd, namaTtd, jabatanTtd,
pengangkut.namaPengangkut, pengangkut.nomorPengangkut, kemasan.kodeJenisKemasan,
barang.kodeBarang, barang.uraian, barang.posTarif, barang.kodeSatuanBarang, barang.kodeJenisKemasan,
entitas.namaEntitas, entitas.alamatEntitas, entitas.nomorIdentitas, entitas.nibEntitas, entitas.nomorIjinEntitas,
dokumen.kodeDokumen, dokumen.nomorDokumen
```

May stay empty: barang `merk`, `tipe`, `ukuran`, `spesifikasiLain` (the schema requires the keys to be present, and they are),
and `merkKemasan`. NIB is mandatory for ALL three entities (user decision). `tanggalIjinEntitas` and `tanggalDokumen` are
covered by the date-format rule (`""` is reported as "wajib diisi").

**Extra labels** to add to the label map: `asalData` "Asal Data", `seri` "Seri Dokumen", `uangMuka` "Uang Muka",
`nilaiJasa` "Nilai Jasa", `vd` "VD", `jumlahRealisasi` "Jumlah Realisasi", `nilaiPungutan` "Nilai Pungutan".

**Extra rules** (outside the schema), appended to the validator's list:

1. No barang → `Barang: minimal 1 barang.`
2. No kemasan in the payload → `Kemasan: wajib diisi (Jenis Kemasan dan Jumlah Kemasan).`
   Else if `kemasan[0].jumlahKemasan <= 0` → `Kemasan > Jumlah Kemasan (jumlahKemasan): harus lebih dari 0`
3. Dokumen has **no minimum** (the schema does not require any); only the rows that exist must be complete.

Examples of messages: `Nomor AJU (nomorAju): harus tepat 26 karakter huruf/angka (sekarang 12 karakter: '000040-AJU-1')`,
`Pengusaha TPB > Tanggal Izin (tanggalIjinEntitas): wajib diisi`, `Dokumen #1 > Nomor (nomorDokumen): wajib diisi`.

---

## 4. Test checklist

Builder (`Bc40PayloadBuilderTest`):
- Root keys unchanged (32, interleaved order); barang keys = the 31 in §1.2, `isiPerkemasan` / `isiPerKemasan` absent.
- One barang: `bruto` = header bruto, `uangMuka` / `nilaiJasa` = header values, `jumlahRealisasi` = `jumlahSatuan`
  (100.0), `nilaiBarang` = `hargaPenyerahan` (15000000.0).
- Three equal rows, bruto 100 → `[33.3333, 33.3333, 33.3334]`; uangMuka 1000 → `[333.3333, 333.3333, 333.3334]`;
  nilaiJasa 10 → `[3.3333, 3.3333, 3.3334]`; the bruto sum is exactly 100.
- Weights 10:20 with bruto 90 → `[30.0, 60.0]`, uangMuka 300 by harga 100:200 → `[100.0, 200.0]`; all-zero netto → equal split.
- Entitas: slot 3 / 7 / 9 key sets unchanged; with entities `[null, null, pengirim]` the Pengusaha slot stays empty and the
  Pengirim stays in seri 9.
- Dokumen with null nomor / tanggal → both `""`. Kemasan: jenis only → `jumlahKemasan` 0; jumlah only → jenis `""`.
- Encoded JSON contains no `isiPer…`.

Schema (`Bc40SchemaTest`, validator + `bc40.json`):
- A complete document → `[]`.
- `nomorAju` `000040-AJU-1` → message contains `Nomor AJU (nomorAju): harus tepat 26`; `""` and `null` → `wajib diisi`.
- Null dates and empty `kodeKantor` / `kotaTtd` / `namaTtd` / `jabatanTtd` → reported with their labels.
- Entity slot 1 missing → Pengusaha TPB problems (nama, tanggal izin, nomor izin) and nothing for Pemilik Barang; slot 3
  missing → `Pengirim Barang > NIB (nibEntitas): wajib diisi`.
- No dokumen → `[]`; an empty dokumen row → three problems (jenis, nomor, tanggal).
- Empty pengangkut / kode barang / pos tarif / jenis kemasan are reported; empty `merkKemasan` passes.
- Split amounts (three rows, 4 decimals) pass the schema.

Verified in Laravel with an independent Python `jsonschema` run: a complete payload has 0 errors; the remaining errors in
the 27 broken scenarios are only unfilled values.

---

## 5. Not changed / still open (do not "fix" these now)

- The whole PPN block (`barangTarif`, root `pungutan`): all amounts 0, tarif 11, fasilitas 3 / 100, `barangTarif.jumlahSatuan`
  = netto while `kodeSatuanBarang` is the barang's unit. The user said to leave it.
- `kodeJenisTpb` stays a free-text input (default "1"), `kodeTujuanPengiriman` the 1–5 select; both are only checked for "not empty".
- `idPengguna` = ERP username (plan Q-c). `userPortal`, `nik` ("" as before) are not set.
- `jumlahKontainer` 0 and `kontainer` `[]` (BC 4.0 has no peti kemas input).
- Plan quirks still as in Villiger apart from those fixed above: Q-d (fixed now), Q-g and Q-j (fixed now); Q-a (status check),
  Q-f (lock after send) were already implemented in the Laravel app before this review.
- Per-BC filter on the KATEGORIBARANG master: pending the user's confirmation (BC 4.0 has no category field).
