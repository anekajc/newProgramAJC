# BC 2.6.1 — update to the OFFICIAL CEISA payload schema

Hand-over spec for re-applying, in the **Villiger Flask app**, the changes already made in the Laravel app
(`D:\newProgramAJC`). It is a **diff against the Villiger behaviour documented in `docs/documentbc261_plan.md`**
(§9 payload, §11 quirks): read that plan first, then apply every section here. A fully filled Villiger document
already passed almost all of the schema (1 error), but empty values, rounding and the keys the schema does not define
made real documents fail, and the app never told the user before sending.

- Official schema: `docs/bc_schema/bc261.json`. Copy it into the Flask repo and read it at runtime.
- The send-time validator, the 422 / dialog / preview contract, the ISO-currency map and the message catalogue are the
  **same as BC 2.3** — implement them once following **`bc23_update.md` §1.4 and §2**, then use the BC 2.6.1 options in §2 below.
- Laravel reference implementation: `app/Services/BeaCukai/Bc261PayloadBuilder.php`,
  `app/Http/Controllers/DocumentBc/DocumentBc261Controller.php` (`WAJIB_ISI`, `masalahKirim`, `spKirim`, `spPreview`),
  `resources/views/documentbc/documentbc261.blade.php`, `tests/Unit/Bc261PayloadBuilderTest.php`.
- Messages are Indonesian, exactly as written.
- Rule chosen with the user: **saving is never blocked by the schema check; every problem is listed when the user tries
  to SEND and in the Preview JSON dialog.**
- **No new database column** is needed for this update (script `15_kolom_kodeasalbarang.sql`, the barang "Asal Barang"
  column, already existed before it).
- The schema has **no `idPengguna` / `userPortal`**: nothing to do with the CEISA username here.

---

## 1. Payload changes (replace plan §9)

Key ORDER stays the Villiger order (minus the removed keys). Helpers: `orDefault(v, d)` = `d` when `v` is null, `""`, `0`
or `0.0` (the string `"0"` is NOT empty); `dec(v, n)` = `round(float(orDefault(v, 0)), n)`, always a float.

### 1.1 Root (39 keys)

Order: asalData, asuransi, biayaTambahan, biayaPengurang, bruto, cif, disclaimer, freight, hargaPenyerahan, jabatanTtd,
jumlahKontainer, kodeDokumen, kodeKantor, kodeTujuanPengiriman, kodeValuta, kotaTtd, namaTtd, ndpbm, netto, nik, nilaiBarang,
nomorAju, seri, tanggalAju, tanggalTtd, tempatStuffing, tglAkhirBerlaku, tglAwalBerlaku, totalDanaSawit, barang, dokumen,
entitas, jaminan, kemasan, **kontainer**, pengangkut, pungutan, uangMuka, vd.

| Key | New value |
|---|---|
| bruto | `dec(header.bruto, 4)` |
| cif | `dec(header.cif, 2)` (schema `multipleOf 0.01`) — **was unrounded** |
| hargaPenyerahan, nilaiBarang | the **same rounded cif** (`dec(header.cif, 2)`) — was the unrounded cif |
| ndpbm | `dec(header.ndpbm, 4)` |
| netto | `dec(header.netto, 4)` — **was `round2`: 100.4567 became 100.46** |
| jumlahKontainer | **`count(kontainer)`** (was always `0`) |
| kodeValuta | **ISO** (`bc23_update.md` §1.4: RM→MYR, EURO→EUR, others as-is) |
| kontainer | **NEW key** (array, §1.7), placed after `kemasan` |
| tempatStuffing, tglAkhirBerlaku, tglAwalBerlaku, totalDanaSawit | still `""` — they are in the schema's `required` list but not in its `properties`, so they must stay |
| asuransi, biayaTambahan, biayaPengurang, freight, uangMuka, vd | `0` (int), unchanged |
| everything else | unchanged (`asalData "S"`, `disclaimer "1"`, `kodeDokumen "261"`, `nik "0"`, `seri 1`, `tanggalAju = tanggalTtd`) |

### 1.2 `barang[]` — 28 keys (13 removed)

**Removed** (not in the official barang schema): `idBarang`, `diskon`, `freight`, `hargaSatuan`, `kodeKategoriBarang`,
`kodePerhitungan`, **`barangTarif`** (the whole BM / PPh / PPN block with the hard-coded 40 / 2.5 / 11), `nilaiDiskon`,
`volume`, `persenBM`, `persenPPN`, `persenPPh`, `barangDokumen`. (The persen values stay in the database and the HS picker;
they are just no longer sent.)

Order and values:

```
cif                 float(row.cif or 0)                 (the page now fills it: CIF Rupiah / header NDPBM, see §3.5)
cifRupiah           dec(row.cifRupiah, 2)
hargaEkspor         dec(row.hargaEkspor, 2)
hargaPenyerahan     dec(row.hargaPenyerahan, 4)
isiPerKemasan       dec(row.isiPerKemasan, 2)
jumlahKemasan       dec(row.jumlahKemasan, 2)
jumlahSatuan        dec(row.jumlahSatuan, 4)
kodeAsalBahanBaku   "0"
kodeAsalBarang      string(row.kodeAsalBarang)          NO default any more (was "0", not one of the page's options 1-4)
kodeBarang          string
kodeDokumen         "261"
kodeJenisKemasan    string, "" when null                (was null)
kodeNegaraAsal      string
kodeSatuanBarang    string
merk                orDefault(row.merk, "-")
ndpbm               dec(row.ndpbm, 4)                   (the page now stores the header NDPBM here, see §3.5)
netto               dec(row.netto, 4)                   (was 2 decimals)
nilaiBarang         dec(row.cifRupiah, 2)               (was unrounded)
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

### 1.3 `barang[].bahanBaku[]` — 30 keys, order unchanged

`seriBarangDokAsal, cif, cifRupiah, flagTis, hargaPenyerahan, hargaPerolehan, isiPerKemasan, jumlahSatuan, kodeAsalBahanBaku,
kodeBarang, kodeDokAsal, kodeDokumen, kodeKantor, kodeSatuanBarang, merkBarang, ndpbm, netto, nilaiJasa, nomorDaftarDokAsal,
nomorDokumen, posTarif, seriBahanBaku, seriBarang, seriIjin, spesifikasiLainBarang, tanggalDaftarDokAsal, tipeBarang, ukuranBarang,
uraianBarang, bahanBakuTarif`. `flagTis ""`, `isiPerKemasan 0` and `nomorDokumen = nomorDaftarDokAsal` are not in the schema's
`properties` but are in its `required`, so they stay. `nomorAjuDokAsal` is **not** sent (not in the schema) and its page input was removed (§3.4).

Changes:

- `seriBarangDokAsal` = **the typed value** (`orDefault(stored, 1)`, int) — was always `1` (quirk Q-i).
- **`bahanBakuTarif = []`** (empty list; the schema only requires the key). The old hard-coded PPN row (11 %, `nilaiBayar` =
  CIF Rupiah, `seriBahanBaku` always 1) is gone, same decision as BC 2.5.
- `jumlahSatuan`, `nilaiJasa` 4 decimals; `cifRupiah` 2; `cif`, `hargaPenyerahan`, `hargaPerolehan`, `ndpbm`, `netto` `dec(…, 4)`.
- `kodeAsalBahanBaku` = `orDefault(stored, "0")`, `kodeDokumen "261"`, `kodeDokAsal` = the origin document code (23/27/40), `kodeKantor` = `kodeKantorAsal`.

### 1.4 `entitas[]` — three FIXED slots, official keys

Quirk Q-d is fixed: `selectEntitas1` → seri 3 Pengusaha TPB, `selectEntitas2` → seri 7 Pemilik Barang, `selectEntitas3` → seri 8
Penerima Barang. An empty slot is still emitted with blank values and **does not shift** the others. All three are mandatory
(enforced at send). Data still comes from the entity master at send time (quirk Q-f unchanged: no snapshot, no DB change).

- seri 3 (11 keys): alamatEntitas, kodeEntitas `"3"`, kodeJenisApi (default `"02"`), kodeJenisIdentitas (default `"6"`), namaEntitas,
  kodeStatus (default `"0"`), nibEntitas, nomorIdentitas, nomorIjinEntitas, tanggalIjinEntitas (`YYYY-MM-DD` or `""`), seriEntitas `3`.
- seri 7 (7 keys): alamatEntitas, kodeEntitas `"7"`, nomorIdentitas, kodeStatus, namaEntitas, kodeJenisIdentitas, seriEntitas `7`
  (**`kodeNegara` removed**).
- seri 8 (7 keys): alamatEntitas, kodeEntitas `"8"`, kodeJenisIdentitas, kodeStatus, namaEntitas, nomorIdentitas, seriEntitas `8`
  (**`kodeJenisApi`, `nomorIjinEntitas`, `tanggalIjinEntitas` removed**).

### 1.5 `dokumen[]`, `jaminan[]`, `kemasan[]` — no nulls

- `dokumen`: `idDokumen "0", kodeDokumen, nomorDokumen, seriDokumen, tanggalDokumen`; empty nomor / tanggal are sent `""` (not null).
- `jaminan`: key order unchanged; `idJaminan` = `kodeJenisJaminan`; every string is `""` when empty (was null), dates `""`; `nilaiJaminan`
  stays an int (null only when never typed → "wajib diisi").
- `kemasan`: **`seriKemasan` is an int** (was the string `"1"`, the one error a complete Villiger document had), `jumlahKemasan` int,
  `kodeJenisKemasan`, `merkKemasan` strings. Exactly these 4 keys.

### 1.6 `pengangkut[]` — exactly 3 keys

`idPengangkut "1"`, `kodeCaraAngkut` (string, `""` when none), `seriPengangkut 1`. **`kodeBendera`, `namaPengangkut`,
`nomorPengangkut` removed** (not in the schema).

### 1.7 `kontainer[]` — NEW (quirk Q-a fixed)

The rows saved on the Peti Kemas tab are now sent: `kodeJenisKontainer, kodeTipeKontainer, kodeUkuranKontainer, nomorKontainer,
seriKontainer` (strings, `""` when empty; `seriKontainer` int = row position). `jumlahKontainer` = the number of rows.

### 1.8 `pungutan[]`

Still one row, but now senilai the **sum of the CIF Rupiah of ALL barang** (was the first barang only, quirk Q-c):

```
{ "idPungutan": "1", "kodeFasilitasTarif": "3", "kodeJenisPungutan": "PPN", "nilaiPungutan": round(sum(barang.cifRupiah), 2) }
```
`nilaiPungutan` is `0.0` without barang. (It is a CIF value, not a computed tax — chosen with the user; a real BM / PPN / PPh
calculation would need persen inputs and is a possible later step.)

---

## 2. Send-time validation options for BC 2.6.1

Validator, 422 contract and preview contract = `bc23_update.md` §2 (same code, `bc261.json`). Extra labels (fallback = JSON key):
kodeAsalBarang Asal Barang · idPungutan ID Pungutan · idJaminan ID Jaminan · kodeJenisJaminan Jenis Jaminan · nomorJaminan Nomor Jaminan ·
tanggalJaminan Tanggal Jaminan · tanggalJatuhTempo Jatuh Tempo · nilaiJaminan Nilai Jaminan · penjamin Penjamin · nomorBpj Nomor BPJ ·
tanggalBpj Tanggal BPJ · nilaiPungutan Nilai Pungutan · idPengangkut ID Pengangkut · idDokumen ID Dokumen (plus the common labels of
`bc23_update.md` §2.2).

`bagian`: `barang`→`Barang`, `bahanBaku`→`Bahan Baku`, `dokumen`→`Dokumen`, `entitas`→[`Pengusaha TPB`,`Pemilik Barang`,`Penerima Barang`],
`jaminan`→`Jaminan`, `kemasan`→`Kemasan`, `kontainer`→`Peti Kemas`, `pengangkut`→`Pengangkut`, `pungutan`→`Pungutan`.
`sisa`: `barang`, `dokumen`, `kemasan`, `kontainer`, `jaminan`, `bahanBaku`.

`wajibIsi` (flat paths that may not be `""`, chosen with the user):

```
kodeKantor, kodeTujuanPengiriman, kodeValuta, kotaTtd, namaTtd, jabatanTtd,
barang.kodeBarang, barang.uraian, barang.posTarif, barang.kodeSatuanBarang, barang.kodeJenisKemasan,
barang.kodeAsalBarang, barang.kodeNegaraAsal,
barang.bahanBaku.kodeBarang, barang.bahanBaku.posTarif, barang.bahanBaku.uraianBarang, barang.bahanBaku.kodeSatuanBarang,
barang.bahanBaku.kodeDokAsal, barang.bahanBaku.kodeKantor, barang.bahanBaku.nomorDaftarDokAsal,
entitas.namaEntitas, entitas.alamatEntitas, entitas.nomorIdentitas, entitas.nibEntitas, entitas.nomorIjinEntitas,
kemasan.kodeJenisKemasan, kontainer.nomorKontainer,
dokumen.kodeDokumen, dokumen.nomorDokumen,
jaminan.kodeJenisJaminan, jaminan.nomorJaminan, jaminan.nomorBpj, jaminan.penjamin
```

`dokumen` and `jaminan` may be absent, but a row that exists must be complete (dates are caught by the schema: `""` is not a date).
May stay empty: `merkKemasan`, bahan baku merk / tipe / ukuran / spesifikasi.

Extra BC 2.6.1 rules appended to the validator's list:
1. `barang` empty → `Barang: minimal 1 barang.`
2. `kemasan` empty → `Kemasan: wajib diisi (Jenis Kemasan dan Jumlah Kemasan).`; any row with `jumlahKemasan <= 0` →
   `Kemasan #<n> > Jumlah Kemasan (jumlahKemasan): harus lebih dari 0`
3. translated `kodeValuta` not `^[A-Z]{3}$` (and not empty) → the message of `bc23_update.md` §1.4.

Things the schema already catches (appear automatically): `nomorAju` exactly 26 letters/digits; `tanggalAju` / `tanggalTtd` / jaminan and
dokumen dates real dates (blank signature date breaks both `tanggalAju` and `tanggalTtd`); `kodeCaraAngkut` ∈ 1..9; `kodeNegaraAsal`
`^[A-Z]{2}$`; entity `kodeJenisIdentitas` ∈ 2..6, Pengusaha `kodeJenisApi` ∈ 01/02 (**an entity whose master value is `-` is therefore
reported**); kontainer enums (ukuran 20/40/45/60, tipe 1..8/99, jenis muatan 4/7/8); decimals (`cif`, `nilaiBarang` 2 dp; `bruto`, `netto`,
`ndpbm` 4 dp).

---

## 3. UI changes (BC 2.6.1 page)

1. **Cara Angkut** (Pengangkut tab): the list is limited to the schema values `1 Laut … 9 Lainnya`; `10 Instalasi`, `11 Pipa`,
   `12 Transmisi` are removed. An old document that stored 10-12 is reported at send (`wajib dipilih` / "harus salah satu dari").
2. **Send dialog and Preview** exactly as `bc23_update.md` §2.1 (422 + blocking dialog; the preview endpoint returns `{json, masalah}`;
   green / red box `#previewMasalah` above the JSON).
3. Info texts: Entitas tab (all three required; Pengusaha needs NIB, izin number and date from the master), Dokumen tab (optional but
   complete rows), Kemasan tab (*"Kemasan wajib ada saat dikirim … Peti kemas ikut dikirim bila diisi."* — replaces "Peti kemas disimpan
   di dokumen tetapi tidak dikirim"), Transaksi tab (*"Saat dikirim, kode valuta DBVALAS diterjemahkan ke kode ISO …"*).
4. **Bahan baku modal: the "Nomor AJU Dok Asal" input is removed** (stored but never sent; the table column `NOMORAJUDOKASAL` stays, nullable, and is no longer written, so an old row loses that value on its next Edit).
   **Entitas tab: NPWP, Alamat, Nomor Izin and Tanggal Izin are read-only** (they are filled from the entity master when the entity is picked, because the master is what is sent). The info text says so. No. Reg BC / Tanggal BC / Tanggal Keluar stay as internal notes.
5. **Barang CIF.** In the barang modal the disabled "CIF" input shows `Nilai CIF (Rupiah) / header NDPBM` (2 decimals, empty when NDPBM is 0), updated while typing the Nilai CIF and when the modal opens. On Save Dokumen every barang gets `cif = Nilai CIF / header NDPBM` and `ndpbm = header NDPBM`, so the payload carries the current header rate. (Same rule as BC 2.5 / 4.1; bahan baku keeps its own typed CIF.) Info text: *"CIF = Nilai CIF / NDPBM header (dihitung ulang saat Simpan Dokumen). NDPBM barang mengikuti NDPBM header."*

---

## 4. Test checklist

- Builder: root key list/order = §1.1 (39 keys, `kontainer` after `kemasan`); barang 28 keys, bahan baku 30 keys, entitas 11 / 7 / 7 keys;
  `cif 1500.456 → 1500.46` (and `hargaPenyerahan`, `nilaiBarang` the same), `netto 100.4567` stays, `bruto 120.12345 → 120.1235`,
  `ndpbm 16000.12345 → 16000.1235`; barang `cifRupiah 24000000.125 → 24000000.13`, `netto 100.45678 → 100.4568`;
  `kodeAsalBarang null → ""` (no `"0"`); pungutan = sum (`1000 + 2500.505 → 3500.51`, no barang → `0.0`); entitas `[null, B, C]` keeps B on
  seri 7 and C on seri 8; 2 containers → `jumlahKontainer 2`; kemasan `seriKemasan "1" → 1` (int); dokumen / jaminan empty values are `""`;
  pengangkut has exactly 3 keys; a barang row with `cif 1500.46`, `ndpbm 16000` is sent as is (the division happens in the page); `bahanBakuTarif = []` and the JSON text contains `"bahanBakuTarif":[]`; bahan baku `seriBarangDokAsal 3 → 3`, null → 1.
- A fully filled document (with kontainer, jaminan, dokumen, bahan baku) → **zero** problems; also with long decimals in cif / netto / bruto / ndpbm.
- Incomplete document: `nomorAju "TEST261"` → the 26-character problem; blank kantor / asal barang / negara asal / nomor peti kemas / cara angkut
  (`wajib dipilih`) / NIB; dokumen with null nomor and tanggal → both reported; jaminan with only a kode → the other fields reported.
- `kodeCaraAngkut "10"` → `harus salah satu dari: 1, 2, 3, 4, 5, 6, 7, 8, 9`.
- Independent cross-check used in Laravel: Python `jsonschema` (draft-07, tuple items, Decimal `multipleOf`, digit-based `maxlength`) on a fully filled
  payload → 0 errors, and no key sent that the schema does not list (apart from the required-but-undeclared ones in §1.1 / §1.3).

---

## 5. Not changed / still open (do not "fix" these now)

- Barang `cif` / `ndpbm` (quirk Q-g) are fixed in the page, not in the builder: the builder still just reads the stored row values (§3.5).
- No. Reg BC / Tanggal BC / Tanggal Keluar stay on the page and in the database as internal notes (never sent) — the user wants them.
- `nilaiJasa` = `nilaiBarang` = CIF Rupiah of the barang, and the root `hargaPenyerahan` / `nilaiBarang` = the foreign CIF: Villiger values, kept.
- Pungutan is a CIF value, not a tax (§1.8). `kodeFasilitasTarif "3"` is a Villiger constant.
- Kategori Barang is no longer sent (not in this schema); the pending per-BC filter on that master (see `bc23_update.md` §5) is unaffected.
- The master entity must carry a valid `KODEJENISAPI` (01 / 02) for the Pengusaha, otherwise the send check lists it.
