# BC 2.5 — update to the OFFICIAL CEISA payload schema

Hand-over spec for re-applying, in the **Villiger Flask app**, the changes already made in the Laravel app
(`D:\newProgramAJC`). It is a **diff against the Villiger behaviour documented in `docs/documentbc25_plan.md`**
(§9 payload, §11 quirks): read that plan first, then apply every section here. Unlike BC 2.3, the official BC 2.5
schema differs **a lot** from what Villiger sends: the plan's payload cannot pass the official schema at all (a fully
filled document failed with 9 errors), so this is a rewrite of the payload plus a few new inputs.

- Official schema: `docs/bc_schema/bc25.json`. Copy it into the Flask repo and read it at runtime.
- The send-time validator, the 422 / dialog / preview contract, the ISO-currency map and the message catalogue are the
  **same as BC 2.3** — implement them once, following **`bc23_update.md` §1.4 and §2**, then use the BC 2.5 options in §4 below.
  (If this file is applied alone, copy those two sections of `bc23_update.md` first.)
- Laravel reference implementation: `app/Services/BeaCukai/Bc25PayloadBuilder.php`,
  `app/Http/Controllers/DocumentBc/DocumentBc25Controller.php` (`masalahKirim`, `spKirim`, `spPreview`, `salinanEntitas`,
  `susunPayload`), `resources/views/documentbc/documentbc25.blade.php`, `docs/update-db-DBSMLNEW/beacukai/17_kolom_bc25_skema_resmi.sql`.
- Messages are Indonesian, exactly as written.
- Rule chosen with the user: **saving is never blocked by the schema check; every problem is listed when the user tries
  to SEND and in the Preview JSON dialog.**

---

## 1. New payload (replaces plan §9)

Keys are emitted in exactly this order. Helpers: `orDefault(v, d)` = `d` when `v` is null, `""`, `0` or `0.0` (the string
`"0"` is NOT empty); `num(v, d)` = `d` (int) when empty else `float(v)`; `round2(v, d)` = `d` when empty else `round(v, 2)`;
`flt2(v)` = `round(float(orDefault(v, 0)), 2)` always a float.

### 1.1 Root (35 keys)

| # | Key | Value |
|---|---|---|
| 1 | asalData | `"S"` |
| 2 | bruto | float(orDefault(header.bruto, 0)) |
| 3 | cif | `flt2(header.cif)` |
| 4 | dasarPengenaanPajak | `flt2(header.dasarPengenaanPajak)` |
| 5 | disclaimer | `"1"` |
| 6 | hargaPenyerahan | float(orDefault(header.hargaPenyerahan, 0)) |
| 6a | **idPengguna** | **CEISA login username** of the sending user, `""` when none (see below) — **added after the first version of this document** |
| 7 | jabatanTtd | string, `""` when null |
| 8 | jumlahKontainer | **count of kontainer rows** |
| 9 | kodeDokumen | `"25"` |
| 10 | kodeKantor | string |
| 11 | kodeLokasiBayar | config `kode_lokasi_bayar` (default `"1"`) |
| 12 | kodeTujuanPengiriman | string |
| 13 | kodeJenisTpb | orDefault(header.kodeJenisTpb, `"1"`) |
| 14 | kodeCaraBayar | orDefault(header.kodeCaraBayar, `"1"`) |
| 15 | kodeValuta | **ISO** (`bc23_update.md` §1.4: RM→MYR, EURO→EUR, others as-is) |
| 16 | kotaTtd | string |
| 17 | namaTtd | string |
| 18 | ndpbm | float(orDefault(header.ndpbm, 0)) |
| 19 | netto | `flt4(header.netto)` = `round(float(orDefault(v, 0)), 4)` always a float — **was `round2` (2 decimals); changed because the schema is `multipleOf 0.0001` and 2 decimals turned 100.4567 into 100.46** |
| 20 | nomorAju | string |
| 21 | ppnPajak | `flt2(header.ppnPajak)` |
| 22 | ppnbmPajak | `flt2(header.ppnbmPajak)` |
| 23 | seri | `1` |
| 24 | tanggalAju | header.tanggalTtd as `YYYY-MM-DD`, `""` when empty |
| 25 | tanggalTtd | `YYYY-MM-DD`, `""` when empty |
| 26 | tarifPpnPajak | `flt2(header.tarifPpnPajak)` |
| 27 | tarifPpnbmPajak | `flt2(header.tarifPpnbmPajak)` |
| 28 | **volume** | float(orDefault(header.volume, 0))  — **new, required by the schema** |
| 29-34 | barang, dokumen, entitas, kemasan, kontainer, pengangkut | arrays, see below |

**Removed from the root** (not in the official schema): `asuransi`, `biayaTambahan`, `biayaPengurang`, `freight`, `nik`,
`nilaiBarang`, `uangMuka`, `vd`, `jaminan`, **`pungutan`** (the whole array; see §3.1).
**`idPengguna` is now sent** (it was postponed at first). Value = the **CEISA username used at the last "Login BC"** of the
user who sends, NOT the ERP username. The login stores that username next to the token and the payload reads it — the
mechanism (table column, login change, controller lookup, extra send-time rule with the message
`ID Pengguna (idPengguna): belum ada. Klik Login BC sekali supaya username CEISA tersimpan.`) is specified once in
**`bc41_update.md` §1.5** (BC 4.1 sends the same username as `userPortal`). Apply that section first. BC 4.0 is unchanged
(it keeps sending the ERP username as `idPengguna`).

### 1.2 `barang[]` (32 keys)

Order and values:

```
cif                 float(row.cif or 0)                       (= CIF Rupiah / header NDPBM, computed in the form, plan Q-c)
cifRupiah           round(float(row.cifRupiah or 0), 2)
diskon              num(row.diskon, 0)
fob                 0                                         (integer 0, new, required)
freight             num(row.freight, 0)
hargaEkspor         num(row.hargaEkspor, 0)
hargaPenyerahan     num(row.hargaPenyerahan, 0)
hargaPerolehan      num(row.hargaPerolehan, 0)
isiPerKemasan       num(row.isiPerKemasan, 0)
jumlahKemasan       round2(row.jumlahKemasan, 0.0)            (schema: multipleOf 0.01)
jumlahSatuan        float(row.jumlahSatuan or 0)
kodeBarang          string
kodeDokAsal         string  = row.kodeDokAsal                 (new, required)
kodeGunaBarang      string  = row.kodeGunaBarang              (new, required, "0".."4")
kodeJenisKemasan    string
kodeKategoriBarang  string  = row.kodeKategoriBarang          (was always null; the schema needs a string)
kodeKondisiBarang   string  = row.kodeKondisiBarang           (new, required, "1" or "2")
kodePerhitungan     orDefault(row.kodePerhitungan, "0")
kodeSatuanBarang    string
merk                orDefault(row.merk, "-")
ndpbm               num(row.ndpbm, 0)                         (schema: INTEGER; the form never sets it, so 0)
netto               flt4(row.netto)                           (4 decimals, was round2)
nilaiBarang         round2(row.nilaiBarang, 0)
posTarif            string
seriBarang          int
spesifikasiLain     orDefault(row.spesifikasiLain, "0")
tipe                orDefault(row.tipe, "0")
ukuran              orDefault(row.ukuran, "0")
uraian              string
bahanBaku           [] rows whose seriBarang = this seriBarang   (§1.3)
barangTarif         3 computed rows BM, PPN, PPH               (unchanged except `tarif` is now round(x, 2))
barangDokumen       []
```

**Removed from barang:** `idBarang`, `hargaSatuan`, `kodeAsalBahanBaku`, `kodeAsalBarang`, `kodeDokumen`,
`kodeNegaraAsal`, `nilaiDiskon`, `nilaiJasa`, `uangMuka` (none exists in the official barang schema).

`barangTarif` (computed, same as plan §9.3.1): `cifRp = row.cifRupiah`; `BM = cifRp × persenBM / 100`;
`base = cifRp + BM`; `PPN = base × persenPPN / 100`; `PPH = base × persenPPh / 100`; `jumlahSatuan = flt4(row.netto)`;
objects in order BM (seriBarang 1), PPN (2), PPH (3), each with keys in this order: kodeJenisTarif (config, default `"1"`),
kodeFasilitasTarif (config, default `"1"`), kodeJenisPungutan, kodeSatuanBarang, jumlahSatuan, `tarif = round(persen, 2)`,
tarifFasilitas (config, default `0`), nilaiBayar = `round(value, 2)`, nilaiFasilitas `0`, nilaiSudahDilunasi `0`, seriBarang.

### 1.3 `barang[].bahanBaku[]` (25 keys)

```
cif, cifRupiah(round2), hargaPenyerahan, hargaPerolehan, jumlahSatuan (num each, fallback 0),
kodeSatuanBarang, kodeAsalBahanBaku = "0", kodeBarang,
kodeDokAsal = bb.kodeDokumen (the ORIGIN document code, 23/27/40), kodeKantor = bb.kodeKantorAsal,
merkBarang (orDefault ""), ndpbm = 0,
nomorAjuDokAsal  string ("" when null — it is a required string, null is invalid),
nomorDaftarDokAsal, posTarif, seriBahanBaku, seriBarang,
seriBarangDokAsal = orDefault(stored value, 1), seriIjin = 1,
spesifikasiLainBarang (orDefault ""), tanggalDaftarDokAsal ("YYYY-MM-DD" or ""), tipeBarang, ukuranBarang, uraianBarang,
bahanBakuTarif = []        (NEW, required by the schema; empty list for now — decision with the user)
```

**Removed from bahan baku:** `kodeDokumen`, `netto`, `nilaiJasa`.

### 1.4 `entitas[]` — three FIXED slots, all mandatory, official keys

Slots never shift (plan quirk Q-e is fixed): `selectEntitas1` → seri 3 Pengusaha TPB, `selectEntitas2` → seri 7 Pemilik Barang,
`selectEntitas3` → seri 8 Penerima Barang. An empty slot is still emitted with blank values. Base keys of every slot, in order:
`alamatEntitas, kodeEntitas, kodeJenisIdentitas (default "6"), kodeStatus (default "0"), namaEntitas, nomorIdentitas, seriEntitas`; then:

- seri **3**: `+ kodeJenisApi (default "02"), nomorIjinEntitas, tanggalIjinEntitas (YYYY-MM-DD or ""), nibEntitas`
- seri **7**: nothing more (**`kodeNegara` removed**, not in the schema)
- seri **8**: `+ kodeJenisApi (default "02"), niperEntitas` (**`niperEntitas` new and required; `nomorIjinEntitas` / `tanggalIjinEntitas` removed**)

`niperEntitas` comes from the entity master's NIPER (Laravel master column `NIPERENTITAS`; it is copied into a snapshot column
`NIPERENTITAS3` at save because the Laravel app sends entity data from a snapshot). In Villiger, where entity data is read from the
master at send time (quirk Q-f), read the NIPER of the master entity selected for slot 3 at send time; add the field to the master
if it does not exist. Empty NIPER is allowed (`""`).

### 1.5 Other arrays

- `kemasan[]`: `jumlahKemasan` (int), `kodeJenisKemasan`, `seriKemasan` (int, fallback 1), `merkKemasan` — **exactly these 4 keys**
  (the schema has `additionalProperties: false` here). Unchanged.
- `kontainer[]`: `kodeJenisKontainer, kodeTipeKontainer, kodeUkuranKontainer, nomorKontainer, seriKontainer` (strings or null). Unchanged.
- `dokumen[]`: `idDokumen "0", kodeDokumen, nomorDokumen, seriDokumen, tanggalDokumen`. Unchanged.
- `pengangkut[]` (exactly 1): `namaPengangkut, nomorPengangkut, kodeCaraAngkut, seriPengangkut = 1`
  (**`idPengangkut` and `kodeBendera` removed**).

---

## 2. Data model changes (add equivalents in the Villiger DB)

Laravel script `17_kolom_bc25_skema_resmi.sql` (names for reference; map them to the Villiger tables):

| Where | New column | Type / default | Meaning |
|---|---|---|---|
| BC 2.5 header | `volume` | decimal(19,4) NOT NULL default 0 | Total Volume (typed by the user) |
| BC 2.5 header | `niper_entitas3` (only if you snapshot entities) | varchar(50) NULL | NIPER of the Penerima Barang |
| BC 2.5 barang | `kode_kategori_barang` | varchar(20) NULL | Kategori Barang (from the category master) |
| BC 2.5 barang | `kode_guna_barang` | varchar(1) NULL | `0..4` |
| BC 2.5 barang | `kode_kondisi_barang` | varchar(1) NOT NULL default `'1'` | `1` Tidak Rusak, `2` Rusak |
| BC 2.5 barang | `kode_dok_asal` | varchar(20) NOT NULL default `'23'` | origin document of the goods (23 / 27 / 40) |

Existing documents: volume 0, kondisi `1`, dokumen asal `23`, the rest empty (the user completes them via Edit; the send-time
check lists what is missing). The 12 pungutan columns (BM … total) and the barang "negara asal" column become unused; leave them.

---

## 3. UI changes (BC 2.5 page)

### 3.1 Pungutan tab removed
The tab with the 12 manual pungutan inputs (BM, BMAD, BMI, BMTP, BMP, Cukai EA/MEA/TEM, PPN, PPnBM, PPh, Total) and its JS
(auto-total, reset) is **removed**, and the payload no longer has a `pungutan` array (the official root has none). PPN / PPnBM still
live on the Transaksi tab (`ppnPajak`, `ppnbmPajak`, `tarifPpn*`, `dasarPengenaanPajak`).

### 3.2 Transaksi tab
- Berat card: add **Total Volume** (`volume`, right-aligned thousands-formatted number input, 4 decimals) below Netto; reset to `0` on
  Add. (Bruto must still be > 0 to save, as before.)
- Info text: append *"Saat dikirim, kode valuta DBVALAS diterjemahkan ke kode ISO (mis. RM menjadi MYR, EURO menjadi EUR; daftarnya di
  config valuta_iso)."*

### 3.3 Barang modal
- **Remove** the "Negara Asal" select.
- **Add** four selects (placed where Negara Asal was):
  1. `Kategori Barang` — from the category master, shown as `kode - nama`, no default.
  2. `Guna Barang` — fixed list: `0 Barang Berhubungan Langsung`, `1 Tidak Berhubungan Langsung`, `2 Barang Konsumsi`,
     `3 Barang Hasil Olahan`, `4 Barang Lainnya`; no default (the user must choose before sending).
  3. `Kondisi Barang` — `1 Tidak Rusak`, `2 Rusak`; default `1`.
  4. `Dokumen Asal` — `23 BC 2.3`, `27 BC 2.7`, `40 BC 4.0`; default `23` (the user's choice).
- New barang rows start with `kodeKondisiBarang = "1"` and `kodeDokAsal = "23"`. Barang-row "dropdown shows `kode - nama`, keeps an unknown
  stored code visible" behaviour is unchanged.

### 3.4 Dokumen tab — first row is a fixed Invoice
"Add" pre-creates one dokumen row: `kodeDokumen = 380`, `nomorDokumen = ""`, `tanggalDokumen = today`. Position 1 is limited to
`380 - INVOICE` (hard-coded option), **cannot be deleted** (hide the delete button, and guard the delete function with
`Dokumen Invoice wajib ada, tidak bisa dihapus.`); positions 2+ are free (document-type master). The same position rule applies
when the user adds the first row to an old document that has no rows.

### 3.5 Send / Preview
Exactly as `bc23_update.md` §2.1 (422 + blocking dialog; preview endpoint returns `{json, masalah}`; green / red box above the JSON).

---

## 4. Send-time validation options for BC 2.5

Validator = `bc23_update.md` §2.2 (same code, `bc25.json`). Extra labels for BC 2.5 keys (fallback = JSON key): idPengguna ID Pengguna ·
volume Total Volume · disclaimer Disclaimer · kodeCaraBayar Cara Bayar · kodeLokasiBayar Lokasi Bayar · kodeJenisTpb Jenis TPB ·
kodeTujuanPengiriman Tujuan Pengiriman · tanggalAju Tanggal Aju · dasarPengenaanPajak DPP · ppnPajak PPN · ppnbmPajak PPnBM ·
tarifPpnPajak Tarif PPN · tarifPpnbmPajak Tarif PPnBM · kodeGunaBarang Guna Barang · kodeKondisiBarang Kondisi Barang ·
kodeDokAsal Dokumen Asal · niperEntitas NIPER · bahanBakuTarif Tarif Bahan Baku · merkBarang Merk · tipeBarang Tipe ·
ukuranBarang Ukuran · uraianBarang Uraian · spesifikasiLainBarang Spesifikasi Lain · nomorAjuDokAsal Nomor AJU Dokumen Asal ·
nomorDaftarDokAsal Nomor Daftar Dokumen Asal · tanggalDaftarDokAsal Tanggal Daftar Dokumen Asal · seriBahanBaku Seri Bahan Baku ·
seriBarangDokAsal Seri Barang Dokumen Asal · seriIjin Seri Izin (plus the common labels listed in `bc23_update.md` §2.2).

`bagian`: `barang`→`Barang`, `barangTarif`→[`Tarif BM`,`Tarif PPN`,`Tarif PPH`], `bahanBaku`→`Bahan Baku`, `dokumen`→`Dokumen`,
`entitas`→[`Pengusaha TPB`,`Pemilik Barang`,`Penerima Barang`], `kemasan`→`Kemasan`, `kontainer`→`Peti Kemas`, `pengangkut`→`Pengangkut`.
`sisa`: `barang`, `dokumen`, `kemasan`, `kontainer`, `bahanBaku`.

`wajibIsi` (may not be `""`; chosen with the user, plus the bahan baku origin fields because the schema rejects a bahan baku row without them):

```
kodeKantor, kodeTujuanPengiriman, kodeJenisTpb, kodeCaraBayar, kodeValuta, kotaTtd, namaTtd, jabatanTtd,
barang.kodeBarang, barang.uraian, barang.posTarif, barang.kodeSatuanBarang, barang.kodeJenisKemasan,
barang.kodeKategoriBarang, barang.kodeGunaBarang, barang.kodeKondisiBarang, barang.kodeDokAsal,
barang.bahanBaku.kodeBarang, barang.bahanBaku.posTarif, barang.bahanBaku.uraianBarang, barang.bahanBaku.kodeSatuanBarang,
barang.bahanBaku.kodeDokAsal, barang.bahanBaku.kodeKantor, barang.bahanBaku.nomorDaftarDokAsal, barang.bahanBaku.nomorAjuDokAsal,
entitas.namaEntitas, entitas.alamatEntitas, entitas.nomorIdentitas,
dokumen.kodeDokumen, dokumen.nomorDokumen
```

May stay empty: NIB, NIPER, `merkKemasan`, pengangkut name / number, bahan baku merk / tipe / ukuran / spesifikasi.

Extra BC 2.5 rules appended to the validator's list: `barang` empty → `Barang: minimal 1 barang.`; `dokumen` empty →
`Dokumen: wajib ada, dokumen pertama adalah Invoice (380).`; translated currency not `^[A-Z]{3}$` → the message of `bc23_update.md` §1.4.

What the schema already catches for BC 2.5 (appears automatically): `nomorAju` exactly 26 letters/digits; `tanggalAju`/`tanggalTtd` real dates
(blank signature date breaks both); `kodeLokasiBayar` ∈ 1..4; `disclaimer` ∈ 0/1; `kodeGunaBarang` ∈ 0..4; `kodeKondisiBarang` ∈ 1/2;
`kodePerhitungan` ∈ 0/1; kontainer enums; `pengangkut.kodeCaraAngkut` ∈ 1..9; dokumen #1 const `380`; entity `kodeJenisIdentitas` ∈ 2..6;
`tanggalIjinEntitas` (Pengusaha) real date; decimals (`cif`, `dasarPengenaanPajak`, `ppn*`, `tarifPpn*` 2 dp; `ndpbm` 4 dp, max 10 digits;
`bruto`/`netto`/`volume` 4 dp; barang `jumlahKemasan` 2 dp, `jumlahSatuan` 4 dp).

Save-time rules stay as before (Laravel: `bruto > 0` and, as an extra, a non-empty Nomor AJU; Villiger already has the bruto rule, so do not add
more save-time checks).

---

## 5. Test checklist

- Builder: root key list/order = §1.1 (35 keys, `idPengguna` after `hargaPenyerahan`) and none of the removed keys; barang 32 keys; bahan baku 25 keys; entitas key lists per slot with
  `niperEntitas` on seri 8 only; pengangkut 4 keys; kemasan 4 keys; `jumlahKontainer` = row count (2 containers → 2); `volume` null → `0.0`;
  `cif 1234.5678 → 1234.57`, `ppnPajak 1100000.456 → 1100000.46`, `tarifPpnPajak 11.005 → 11.01`; `kodeValuta RM → MYR`, `EURO → EUR`, `IDR → IDR`;
  empty entity slot 2 keeps seri 7 blank and does not move slot 3; bahan baku `nomorAjuDokAsal` null → `""`, `seriBarangDokAsal` null → 1,
  `bahanBakuTarif = []`; barang `fob = 0`, `ndpbm = 0` (int); barangTarif: persen 5/11/2.5 on CIF Rupiah 10,000,000 → nilaiBayar
  500,000 / 1,155,000 / 262,500 (BM, PPN, PPH).
- Netto keeps 4 decimals: header `netto 100.45678 → 100.4568`, barang `netto 50.12345 → 50.1235` (also the `jumlahSatuan` of the 3 barangTarif rows); `null → 0.0`.
- Validator on a fully filled document (header has `idPengguna`) → no problem; `payload['idPengguna']` equals the header value.
- Extra rule: an empty `idPengguna` → `ID Pengguna (idPengguna): belum ada. Klik Login BC sekali supaya username CEISA tersimpan.`
- Independent cross-check used in Laravel: Python `jsonschema` (draft-07, tuple items, Decimal-based `multipleOf`, digit-based `maxlength`) on a fully filled payload →
  no error (when `idPengguna` is filled), and no key sent that the schema does not list (apart from the optional ones `bruto`, `flag4tahun`, `bahanBakuDokumen`).
- Mutations that must produce problems: `nomorAju` empty/short, `tanggalTtd` null (two problems: `tanggalAju` and `tanggalTtd`), `cif` with 4 decimals, only a
  non-380 first dokumen, blank `kodeCaraAngkut` (`wajib dipilih`).

---

## 6. Not changed / still open

- Constants `kode_fasilitas_tarif "1"`, `tarif_fasilitas 0`, `kode_jenis_tarif "1"`, `kode_lokasi_bayar "1"` remain config guesses (plan Q-d).
- `bahanBakuTarif` is intentionally empty; real tariff entries for bahan baku are a possible later step.
- `idPengguna` = CEISA username (done, §1.1); a user who has not logged in again after the new column exists gets the "Klik Login BC" problem.
- Kategori Barang uses the existing master as-is; a per-BC filter on that master is pending (see `bc23_update.md` §5).
