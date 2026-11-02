# BC 2.3 — update to the OFFICIAL CEISA payload schema

Hand-over spec for re-applying, in the **Villiger Flask app**, the changes already made in the Laravel app
(`D:\newProgramAJC`). It is a **diff against the Villiger behaviour documented in `docs/documentbc23_plan.md`**
(§9 payload, §11 quirks): read that plan first, then apply every section here.

- Official schema: `docs/bc_schema/bc23.json` (JSON Schema draft-07, CEISA dialect). Copy it into the Flask repo
  and read it at runtime; it is the single source of truth for the checks in §2.
- The Laravel reference implementation, if you need to look at it: `app/Services/BeaCukai/Bc23PayloadBuilder.php`,
  `app/Services/BeaCukai/CeisaSchemaValidator.php`, `app/Http/Controllers/DocumentBc/DocumentBc23Controller.php`
  (`masalahKirim`, `spKirim`, `spPreview`), `resources/views/documentbc/documentbc23.blade.php`.
- Language of user-facing messages: **Indonesian**, exactly as written below.
- **No database change** is needed for BC 2.3.

Why: a document that was filled in correctly still failed the official schema in many ways (null where a string is
required, `""` where a date is required, wrong document order, `RM` instead of `MYR`, 4-decimal amounts, ...), and
the app never told the user before sending. The rule chosen with the user: **do not block saving; list every problem
when the user tries to SEND, and also in the Preview JSON dialog.**

---

## 1. Payload changes (`build` of the BC 2.3 payload)

Key ORDER is unchanged except for the removals below (Villiger/CEISA fidelity). Every other value is as in plan §9.

### 1.1 Root

| Key | Old (plan §9) | New |
|---|---|---|
| `jumlahKontainer` | always `0` (quirk Q-h) | **number of containers actually sent** (`count(kontainer)`; BC 2.3 has at most 1, so 0 or 1) |
| `kodeValuta` | raw | **translated to ISO** (§1.4) |
| `asuransi`, `cif`, `fob`, `freight` | float, unrounded | **`round(x, 2)`**, still always a float (`0.0` when empty) |
| `nilaiBarang` | float(header cif) | **`round(header cif, 2)`** float |
| `tanggalTiba` | already "as typed" (Q-a) | unchanged |
| everything else | | unchanged (`bruto`, `netto`, `hargaPenyerahan`, `ndpbm` keep 4 decimals, no rounding) |

Reason: the schema requires `multipleOf 0.01` for `asuransi`, `cif`, `fob`, `freight`, `nilaiBarang`.

Root key order (44 keys, unchanged): asalData, asuransi, bruto, cif, fob, freight, hargaPenyerahan, jabatanTtd,
jumlahKontainer, kodeAsuransi, kodeDokumen, kodeIncoterm, kodeKantor, kodeKantorBongkar, kodePelBongkar, kodePelMuat,
kodePelTransit, kodeTps, kodeTujuanTpb, kodeTutupPu, kodeValuta, kotaTtd, namaTtd, ndpbm, netto, nik, nilaiBarang,
nomorAju, nomorBc11, posBc11, seri, subposBc11, tanggalBc11, tanggalTiba, tanggalTtd, biayaTambahan, biayaPengurang,
kodeKenaPajak, barang, entitas, kemasan, kontainer, dokumen, pengangkut.

### 1.2 `barang[]` — drop 6 keys, round 3 values

Keys **removed** (not in the official barang schema): `nilaiDiskon`, `volume`, `nilaiJasa`, `persenBM`, `persenPPN`,
`persenPPh`. (The per-barang persen values stay in the DB / UI: they are still used to pre-fill the HS rates and for
display, they are just no longer sent.)

Values changed:

| Key | New |
|---|---|
| `nilaiBarang` | `round(row.cif, 2)` (fallback int `0` when empty) |
| `nilaiTambah` | `round(row.cif, 2)` (fallback int `0` when empty) |
| `jumlahKemasan` | `round(row.jumlahKemasan, 2)` (fallback `0.0`) |

Resulting barang key order (35 keys): idBarang, asuransi, cif, diskon, fob, freight, hargaEkspor, hargaPenyerahan,
hargaSatuan, isiPerKemasan, jumlahKemasan, jumlahSatuan, kodeBarang, kodeDokumen, kodeKategoriBarang, kodeJenisKemasan,
kodeNegaraAsal, kodePerhitungan, kodeSatuanBarang, merk, netto, nilaiBarang, nilaiTambah, posTarif, seriBarang,
spesifikasiLain, tipe, ukuran, uraian, ndpbm, cifRupiah, hargaPerolehan, kodeAsalBahanBaku, barangTarif, barangDokumen.

`barangTarif[]` (BM, PPH, PPN with the hard-coded 40 / 2.5 / 11, `nilaiBayar` = full CIF Rupiah) is **unchanged** —
the schema accepts it. (Whether those business values are right is a separate, still open question.)

### 1.3 `entitas[]` — fixed slots, all three mandatory

- Old (quirk Q-d): the non-empty entities were compacted ("next non-empty") into seri 3 → 5 → 7.
- New: **fixed slots**: `selectEntitas1` → seri 3 (Importir / Pengusaha TPB), `selectEntitas2` → seri 5 (Pemasok),
  `selectEntitas3` → seri 7 (Pemilik Barang). An empty slot is still emitted (blank values) and does **not** shift the
  others. All three entities are **mandatory** (enforced at send, §2).
- Slot data still comes from the entity master at send time (quirk Q-e unchanged).
- **Pemilik Barang (seri 7) only:** `nomorIjinEntitas` and `tanggalIjinEntitas` are **omitted when empty** (they are
  optional in the schema, and `""` is not a valid date). When filled they are emitted in the old position (after
  `nomorIdentitas`, before `seriEntitas`). Pengusaha (seri 3) always emits all its keys (the schema requires them;
  an empty izin date is then reported as a problem, §2).
- Slot key lists are otherwise as in plan §9.2.

### 1.4 `kodeValuta` → ISO

The CEISA "Referensi Valuta" is ISO 4217. The ERP currency master has non-ISO codes. Translate **only when building the
payload** (the stored value is untouched, and NDPBM lookups keep using the master code):

```
valuta_iso = { "RM": "MYR", "EURO": "EUR" }     # configurable; case-insensitive match on the key
kodeValuta = valuta_iso.get(code) or code        # any other code is sent as-is (USD, SGD, IDR, ...)
```

Put the map in app config. At send, the translated value must match `^[A-Z]{3}$`, otherwise add the problem
`Valuta (<code>): belum punya kode ISO 3 huruf; tambahkan padanannya di config/beacukai.php (valuta_iso) atau ganti valuta.`
(adjust the config file name in the message to the Flask config).

### 1.5 Unchanged but worth stating

`dokumen[]`, `kemasan[]`, `kontainer[]`, `pengangkut[]` key lists and values are unchanged. `kontainer` is emitted only
when `nomorKontainer` is filled (quirk Q-h `kodeUkuranKontainer` fallback to number `0` is kept but is now caught by the
validator because it is not one of `"20","40","45","60"`).

---

## 2. Send-time validation against the official schema

### 2.1 Behaviour

1. `POST send`: build the payload, then **run the validator before calling CEISA** (the old "Tanggal Tiba harus diisi"
   check is superseded: a blank `tanggalTiba` now shows up as a normal problem).
2. If there is any problem: **HTTP 422** with
   `{"message": "Dokumen belum bisa dikirim: <N> masalah ditemukan. Perbaiki lalu kirim ulang.", "details": ["<problem 1>", ...]}`
   and **do not call CEISA / do not set `sent`**.
3. UI: on 422 with `details`, show a blocking dialog (not a toast): title `Dokumen belum bisa dikirim`, the message, then a
   `<ul>` with one `<li>` per problem (scrollable, max-height ~60vh, HTML-escape every line). Other errors keep the
   existing toast.
4. **Saving is never blocked** by these checks (the existing save rules are unchanged).
5. **Preview JSON** endpoint now returns `{"json": "<pretty printed payload>", "masalah": ["<problem>", ...]}`
   (it still builds the payload for incomplete documents; it never blocks). The dialog shows, above the JSON:
   - problems → red box `Belum bisa dikirim: <N> masalah ditemukan.` + `<ul>` of problems;
   - no problems → green box `Lolos pemeriksaan skema CEISA, siap dikirim.`
   The Copy button still copies only the JSON text. Any other caller of the preview endpoint must read the new shape.

### 2.2 The validator (generic, reusable for the other BCs)

Implement a small draft-07 subset validator that reads `bcNN.json` and returns a **list of Indonesian problem strings**.
Supported keywords (all the schema uses): `type` (`string`, `number`, `integer`, `array`, `object`), `required`,
`properties`, `items`, `enum`, `const`, `pattern`, `format: date`, `multipleOf`, `maxlength` (CEISA dialect, see below),
`dependencies`.

Rules that are easy to get wrong:

- **`items` as a LIST is positional (tuple):** the n-th array element is validated against the n-th schema. This is how
  the schema says "dokumen #1 must be Invoice 380, #2 must be BL/AWB", "entitas #1/#2/#3 are seri 3/5/7" and
  "barangTarif #1/#2/#3 are BM/PPH/PPN". Elements beyond the list have **no schema** unless the array name is in the
  `sisa` option, in which case the **last** schema is reused for the remaining rows (used for `barang`, `dokumen`,
  `kemasan`, `kontainer`: the schema only describes the first row but the rule is meant for every row).
- **`null` is never valid** for any property that is present → problem `wajib diisi`. A required key that is absent →
  `wajib diisi` too.
- **Numbers:** `integer` accepts an int or a float with an integral value. `multipleOf 0.01/0.0001` → compare with
  `abs(v * 10^d - round(v * 10^d)) <= 1e-4` where `d` = number of decimals of the multiple.
  **`maxlength` on a number = number of DIGITS** (format with 4 decimals, strip trailing zeros and the dot, count digits,
  ignore the sign); on a string it is the character count.
- **Empty string `""`:** a problem when the property has `enum` (`wajib dipilih`), `pattern` or `format` (`wajib diisi`), or
  when its flat path (array indexes removed, e.g. `barang.kodeNegaraAsal`) is in the `wajibIsi` option (`wajib diisi`).
  Otherwise the schema accepts it (plain string).
- `format: date` → `^\d{4}-\d{2}-\d{2}$` and a real calendar date (`checkdate`).
- `dependencies`: if the key exists, every listed key must exist and not be null.
- Return unique problems, in schema walk order.

Problem message format: `<where>: <what>`; where = `Section #n > Field label (jsonKey)` (section only for array
rows; sections with fixed roles use their role name instead of `#n`).

| Situation | `<what>` |
|---|---|
| missing / null / blank-required | `wajib diisi` (enum: `wajib dipilih`) |
| wrong type | `harus berupa teks` / `harus berupa angka` / `harus berupa bilangan bulat` / `bentuk data tidak sesuai (harus objek)` / `(harus daftar)` |
| const | `harus berisi '<const>' (sekarang '<value>')` (omit the parenthesis when blank) |
| enum | `harus salah satu dari: a, b, c (sekarang '<value>')` |
| pattern `^[A-Za-z0-9]{26}$` | `harus tepat 26 karakter huruf/angka (sekarang <len> karakter: '<value>')` |
| pattern `^[A-Z]{2}$` | `harus 2 huruf kapital (kode negara, mis. MY) (sekarang <len> karakter: '<value>')` |
| pattern `^[A-Za-z]{2}$` | `harus 2 huruf (kode negara, mis. MY) (sekarang <len> karakter: '<value>')` |
| other pattern | `format tidak sesuai (sekarang ...)` |
| date | `harus tanggal yang valid (YYYY-MM-DD), sekarang '<value>'` |
| multipleOf | `maksimal <d> angka di belakang koma (sekarang <value>)` |
| maxlength digits | `maksimal <n> digit (sekarang <digits>)` ; string: `maksimal <n> karakter` |
| dependency | `wajib diisi (data ini ikut dikirim)` |

Example lines: `Nomor AJU (nomorAju): harus tepat 26 karakter huruf/angka (sekarang 6 karakter: 'TEST41')`,
`Barang #2 > Negara Asal (kodeNegaraAsal): wajib diisi`, `Dokumen #1 > Jenis Dokumen (kodeDokumen): harus berisi '380' (sekarang '705')`,
`Pemasok > Negara (kodeNegara): wajib diisi`, `Nilai CIF (cif): maksimal 2 angka di belakang koma (sekarang 1234.5678)`.

Field labels (fallback = the JSON key itself): asuransi Asuransi · bruto Berat Kotor (Bruto) · cif Nilai CIF · fob FOB ·
freight Freight · hargaPenyerahan Harga Penyerahan · jabatanTtd Jabatan (Pernyataan) · jumlahKontainer Jumlah Kontainer ·
kodeAsuransi Kode Asuransi · kodeIncoterm Incoterm · kodeKantor Kantor Pabean · kodeKantorBongkar Kantor Pabean Bongkar ·
kodePelBongkar Pelabuhan Bongkar · kodePelMuat Pelabuhan Muat · kodePelTransit Pelabuhan Transit · kodeTps Tempat
Penimbunan (TPS) · kodeTujuanTpb Tujuan TPB · kodeTutupPu Tutup PU · kodeValuta Valuta · kotaTtd Kota (Pernyataan) ·
namaTtd Nama (Pernyataan) · ndpbm NDPBM · netto Berat Bersih (Netto) · nik NIK · nilaiBarang Nilai Barang · nomorAju
Nomor AJU · nomorBc11 Nomor BC 1.1 · posBc11 Pos BC 1.1 · subposBc11 Sub Pos BC 1.1 · tanggalBc11 Tanggal BC 1.1 ·
tanggalTiba Tanggal Tiba · tanggalTtd Tanggal (Pernyataan) · biayaTambahan Biaya Tambahan · biayaPengurang Biaya
Pengurang · kodeKenaPajak Kode Kena Pajak · diskon Diskon · hargaEkspor Harga Ekspor · hargaSatuan Harga Satuan ·
isiPerKemasan Isi per Kemasan · jumlahKemasan Jumlah Kemasan · jumlahSatuan Jumlah Satuan · kodeBarang Kode Barang ·
kodeKategoriBarang Kategori Barang · kodeJenisKemasan Jenis Kemasan · kodeNegaraAsal Negara Asal · kodePerhitungan Kode
Perhitungan · kodeSatuanBarang Satuan · merk Merk · nilaiTambah Nilai Tambah · posTarif Pos Tarif / HS · seriBarang Seri
Barang · spesifikasiLain Spesifikasi Lain · tipe Tipe · ukuran Ukuran · uraian Uraian · cifRupiah CIF Rupiah ·
hargaPerolehan Harga Perolehan · kodeAsalBahanBaku Asal Bahan Baku · kodeJenisTarif Jenis Tarif · kodeFasilitasTarif
Fasilitas Tarif · kodeJenisPungutan Jenis Pungutan · nilaiBayar Nilai Bayar · nilaiFasilitas Nilai Fasilitas ·
nilaiSudahDilunasi Nilai Sudah Dilunasi · tarif Tarif · tarifFasilitas Tarif Fasilitas · alamatEntitas Alamat ·
kodeEntitas Kode Entitas · kodeJenisIdentitas Jenis Identitas · namaEntitas Nama · nibEntitas NIB · nomorIdentitas NPWP /
Identitas · nomorIjinEntitas Nomor Izin · tanggalIjinEntitas Tanggal Izin · kodeNegara Negara · kodeJenisApi Jenis API ·
kodeStatus Status · seriEntitas Seri Entitas · merkKemasan Merk Kemasan · seriKemasan Seri Kemasan · kodeTipeKontainer
Tipe · kodeUkuranKontainer Ukuran · nomorKontainer Nomor Peti Kemas · seriKontainer Seri Peti Kemas · kodeJenisKontainer
Jenis Muatan · kodeDokumen Jenis Dokumen · nomorDokumen Nomor · seriDokumen Seri Dokumen · tanggalDokumen Tanggal ·
kodeBendera Bendera · namaPengangkut Nama Sarana Pengangkut · nomorPengangkut Nomor Sarana Pengangkut · kodeCaraAngkut Cara
Pengangkutan · seriPengangkut Seri Pengangkut.

### 2.3 Options passed for BC 2.3

`bagian` (section labels): `barang`→`Barang`, `barangTarif`→[`Tarif BM`,`Tarif PPH`,`Tarif PPN`] (by position),
`dokumen`→`Dokumen`, `entitas`→[`Importir / Pengusaha TPB`,`Pemasok`,`Pemilik Barang`] (by position),
`kemasan`→`Kemasan`, `kontainer`→`Peti Kemas`, `pengangkut`→`Pengangkut`.
`sisa`: `barang`, `dokumen`, `kemasan`, `kontainer`.

`wajibIsi` (flat paths that may not be `""` — decision made with the user):

```
kodeKantor, kodeKantorBongkar, kodePelMuat, kodePelBongkar, kodeTps, kodeTujuanTpb, kodeValuta, kodeIncoterm,
nomorBc11, posBc11, subposBc11, kotaTtd, namaTtd, jabatanTtd,
barang.kodeBarang, barang.uraian, barang.posTarif, barang.kodeSatuanBarang, barang.kodeJenisKemasan,
barang.kodeKategoriBarang, barang.kodeNegaraAsal,
entitas.namaEntitas, entitas.alamatEntitas, entitas.nomorIdentitas,
dokumen.kodeDokumen, dokumen.nomorDokumen
```

These **may stay empty** (schema accepts `""`): `kodePelTransit`, `nibEntitas`, `merkKemasan`, pengangkut name / number / flag.

### 2.4 Extra BC 2.3 rules (outside the schema), appended to the validator's list

1. `barang` empty → `Barang: minimal 1 barang.`
2. fewer than 2 dokumen → `Dokumen: minimal 2 dokumen, Invoice (380) lalu BL/AWB (705 atau 740).`
3. translated `kodeValuta` not matching `^[A-Z]{3}$` (and not empty) → the message in §1.4.

Things the schema itself already catches (so they appear automatically): Nomor AJU must be exactly 26 letters/digits;
`tanggalBc11`, `tanggalTiba`, `tanggalTtd`, `tanggalIjinEntitas` (Pengusaha) must be real dates; `kodeAsuransi` ∈ LN/DN;
`kodeJenisIdentitas` ∈ 2..6; `kodeNegaraAsal` `^[A-Z]{2}$`; Pemasok `kodeNegara` `^[A-Za-z]{2}$`; kontainer enums
(ukuran 20/40/45/60, tipe 1..8/99, jenis muatan 4/7/8); `pengangkut.kodeCaraAngkut` ∈ 1..9; dokumen #1 const `380`,
dokumen #2 ∈ `705`/`740`; decimals/digits listed in §1.

---

## 3. UI changes (BC 2.3 page)

1. **Dokumen tab — two fixed rows.** "Add from PO" now pre-creates two dokumen rows:
   row 1 `kodeDokumen = 380` (Invoice), row 2 `kodeDokumen = 705` (B/L), both with `nomorDokumen = ""` and
   `tanggalDokumen = today`. Row types are limited **by position**:
   - row 1: the only option is `380 - INVOICE`;
   - row 2: options `705 - B/L` and `740 - AWB`;
   - row 3 onward: any type from the document-type master.
   These lists are **hard-coded** (the master may not contain 740). The same rule applies when the user adds rows to an
   old document that has fewer than two rows (the position of the new row decides the allowed types; pre-select the first
   allowed type).
2. **Rows 1 and 2 cannot be deleted** (hide the delete button; also guard the delete function with the warning
   `Dokumen Invoice dan BL/AWB wajib ada, tidak bisa dihapus.`).
3. The send-error dialog and the preview dialog described in §2.1.
4. Info text under the Transaksi card: *"Saat dikirim, kode valuta DBVALAS diterjemahkan ke kode ISO (mis. RM menjadi MYR,
   EURO menjadi EUR; daftarnya di config valuta_iso)."* (adapt "DBVALAS" to the Villiger currency master name).
5. No new input fields, no removed fields.

---

## 4. Test checklist (what the Laravel tests assert; reproduce equivalents)

- A fully and correctly filled document yields **zero** problems.
- `nomorAju` of 6 chars, 25 chars, 27 chars, or with a `-` → the 26-character problem; `null` → `wajib diisi`.
- Blank `kodeKantor` (null), `kotaTtd` (`""`), `tanggalBc11` (`""`), `kodeTps` (`"  "`) → one `wajib diisi` problem each;
  blank `kodePelTransit` → **no** problem.
- Dokumen `[705, 380]` → `Dokumen #1 ... harus berisi '380'` and `Dokumen #2 ... harus salah satu dari: 705, 740`.
  Dokumen #3 with null nomor / tanggal → both reported.
- Two barang where #2 has blank `kodeNegaraAsal` and `kodeKategoriBarang` → problems only for `Barang #2`; `kodeNegaraAsal = "my"`
  → `harus 2 huruf kapital`.
- Pemasok `kodeNegara = ""`, Pengusaha `tanggalIjinEntitas = ""`, kontainer `kodeJenisKontainer = ""` (→ `wajib dipilih`).
- `cif = 1234.5678` → `maksimal 2 angka di belakang koma`; `ndpbm = 15000.12345` → `maksimal 4 angka di belakang koma`.
- Builder: `cif 1234.5678 → 1234.57`, `fob 1000.125 → 1000.13`, `freight 50.004 → 50.0`, `asuransi 10.006 → 10.01`,
  barang `jumlahKemasan 10.123 → 10.12`; `kodeValuta RM/rm → MYR`, `EURO → EUR`, `USD → USD`, `"" → ""`;
  `jumlahKontainer = 1` with a container and `0` without; entities `[null, supplier, owner]` keep seri 3/5/7 positions;
  Pemilik without izin has no `nomorIjinEntitas` / `tanggalIjinEntitas` keys; the six dropped barang keys are absent.

---

## 5. Not changed / still open (do not "fix" these now)

- `barangTarif` constants (BM 40, PPh 2.5, PPN 11, facility codes 3/6/6, `tarifFasilitas` 100, `nilaiBayar` = CIF Rupiah)
  are still the Villiger values.
- Barang `fob` is still the integer-truncated header fob (quirk Q-g).
- Kategori Barang uses the existing master as-is. **Pending (user will confirm later):** a filter on that master saying
  which BC a category belongs to, so a category chosen for BC 2.3 is not offered to any other BC.
- Entities still come from the entity master at send time (not a saved snapshot).
