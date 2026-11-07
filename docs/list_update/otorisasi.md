# Checklist — "Minta Otorisasi" (push to approvers) per page

Last updated: 2026-11-06.

A page counts as **converted** when a user without the Otorisasi right clicks the key icon and the page sends a
request (push notification to the approvers) instead of the old `No access` toast, and the approver can approve or
reject it on the phone. Many pages also got "Minta Batal Otorisasi" (request type `B`, right `IsBatal`).

How to convert one more page: `docs/mobile-otorisasi-guide.md` (checklist in §3-§5, session prompt in §12).

- **Menu** = `DBMENUWEB.Keterangan`, **Href** = `DBMENUWEB.href` (also the key in `config/otorisasi.php`).
- **OL** = number of approval levels in `dbmenu.OL`. `OL 2` pages need the multi-level approach (guide §10d) — ask before copying it.
- The list was built from the live routes (controllers with an authorisation endpoint), so dead copies such as
  `*-copy.php`, `*(1105).php` or `*Old.php` are not counted.

Progress: **22 converted, 30 to do** (8 Accounting, 17 Gudang, 3 Marketing, 2 Purchasing).

---

## Converted (22)

- [x] **Kas** — `kas` (0202)
- [x] **Bank** — `bank` (0203)
- [x] **PR Stock (Non Keagenan)** — `pembelianpermintaannonagen` (030101) — Otorisasi + Batal
- [x] **PR (Keagenan)** — `pembelianpermintaanagen` (030102) — Otorisasi + Batal
- [x] **PR Non Stock** — `pembelianpermintaannonstock` (030103) — Otorisasi + Batal
- [x] **PO Stock** — `purchaseorder` (030201) — Otorisasi + Batal, OL 2 (routing by amount, plafon rules)
- [x] **PO Non Stock** — `pononstock` (030202) — Otorisasi + Batal, OL 2 (same handler as PO Stock, `pJasa = 1`)
- [x] **Pembelian (Penerimaan ACC)** — `newpobeliacc` (0304) — Otorisasi + Batal, posts hutang + jurnal
- [x] **Perintah Retur Beli** — `perintahreturbeli` (030601) — Otorisasi + Batal
- [x] **Invoice Retur Pembelian** — `invoicereturbeli` (030603) — Otorisasi + Batal, posts hutang + jurnal, period-lock check
- [x] **Debet Note** — `pembelianpermintaandebetnote` (0307) — Otorisasi + Batal, posts hutang + jurnal
- [x] **Penawaran SO** — `penawaranso` (04011) — Otorisasi + Batal, no posting
- [x] **SO** — `so` (04021) — Otorisasi + Batal, no posting; keeps the blacklist + plafon rules (§10j)
- [x] **Performa** — `performance` (04024) — Otorisasi + Batal, flips `DBSO.OtoPerf`; batal blocked by Uang Muka Jual (§10k)
- [x] **Uang Muka Jual** — `uangmukajual` (04025) — Otorisasi + Batal, posts jurnal; batal blocked by a faktur (§10l)
- [x] **Surat Jalan** — `suratjalan` (04031) — Otorisasi + Batal; batal VOIDS the SPB, no blocker (§10m)
- [x] **Retur Surat Jalan** — `retursuratjalan` (04032) — Otorisasi + Batal; approver's period must match the retur's month (§10n)
- [x] **Invoice Penjualan** — `invoicepenjualan` (04041) — Otorisasi + Batal, posts piutang + jurnal; batal VOIDS the invoice, no blocker (§10o)
- [x] **Perintah Retur Jual** — `perintahreturjual` (04051) — Otorisasi + Batal; batal blocked by Retur Penjualan Gudang; `$pMin = 0`, so the (-) page is a one-line subclass (§10p)
- [x] **Perintah Retur Jual (-)** — `perintahreturjualminus` (04052) — Otorisasi + Batal; one-line subclass with `$pMin = 1`, same gudang-retur blocker (§10q)
- [x] **Retur Penjualan Gudang** — `returpenjualangudang` (04053) — Otorisasi + Batal; flag only (SPR in `dbSPBRJual`); batal blocked by Nota Retur Penjualan (§10r)
- [x] **Nota Retur Penjualan** — `notareturpenjualan` (04055) — Otorisasi + Batal, posts piutang + jurnal; batal VOIDS the nota (IsBatal = 1), no blocker (§10s)

---

## To do — Purchasing (2)

- [ ] **Kelengkapan Dokumen** — `invoicepembelian` (0305) — OL 1 — `InvoicePembelianController` (`spOtorisasi1` / `spUnOtorisasi1`)
- [ ] **Uang Muka Beli** — `uangmukabeli` (030205) — OL 1 — `UangMukaBeliController`

## To do — Marketing (3)

- [ ] **Verifikasi Penawaran** — `verifikasipenawaran` (04012) — OL 1
- [ ] **Verifikasi Barang** — `verifikasibarang` (04013) — **OL 2** (multi-level)
- [ ] **Kredit Note** — `kreditnote` (0406) — OL 1

## To do — Accounting (8)

- [ ] **Memorial / Koreksi** — `memorialkoreksi` (0204) — OL 1
- [ ] **Giro Diterima** — `giroditerima` (0205) — OL 1
- [ ] **Giro Dibuka** — `girodibuka` (0206) — OL 1
- [ ] **Pengajuan DPH** — `pengajuandphtunai` (02071) — OL 1
- [ ] **Pengajuan DPH UM** — `pengajuandph` (02072) — OL 1
- [ ] **Pengajuan DPP** — `pengajuandpp` (02081) — OL 1
- [ ] **Penerimaan DPP** — `penerimaandpp` (02082) — OL 1
- [ ] **Pelunasan Piutang DPP** — `pelunasanpiutangdpp` (02083) — **OL 2** (multi-level)

## To do — Gudang (17)

- [ ] **Permintaan Pemakaian** — `permintaanpemakaian` (06011) — OL 1
- [ ] **Pemakaian Barang ACC** — `pembebananpemakaian` (06013) — OL 1
- [ ] **Perintah Opname** — `perintahopname` (06021) — OL 1
- [ ] **Berita Acara Opname** — `beritaacaraopname` (06023) — OL 1
- [ ] **Opname Barang** — `opnamebarang` (06024) — OL 1
- [ ] **Koreksi Stock** — `koreksistock` (06030) — OL 1
- [ ] **Ubah Kemasan Barang** — `ubahkemasanbarang` (06031) — OL 1
- [ ] **Permintaan Transfer Barang** — `gudangpermintaantrfbrg` (06041) — OL 1
- [ ] **Transfer Barang** — `gudangtrfbrg` (06043) — OL 1
- [ ] **Penerimaan Transfer Barang** — `terimatransferbarang` (06045) — OL 1
- [ ] **Permintaan Sample** — `gudangpermintaansample` (06051) — OL 1
- [ ] **Penyerahan Sample** — `gudangpenyerahansample` (06052) — OL 1
- [ ] **Retur Penyerahan Sample** — `gudangreturpenyerahansample` (06054) — OL 1
- [ ] **Pembebanan Sample** — `pembebanansample` (06055) — OL 1
- [ ] **Permintaan Konsinyasi** — `gudangpermintaankonsinyasi` (06061) — OL 1
- [ ] **Retur Konsinyasi** — `gudangreturpenyerahankonsinyasi` (06062) — OL 1
- [ ] **Penyerahan Konsinyasi** — `gudangpenyerahankonsinyasi` (06063) — **no `dbmenu` row found**, check the level before starting

---

## Not on the list on purpose

These controllers contain authorisation methods, but no live route reaches them, so nobody can authorise through
them today. Add them to the list only if their route is switched back on. Details and the decisions needed are in
`docs/list_update/todo.md`.

- `Accounting/BonSementaraController` (`spOtorisasi`, `spBatalOtorisasi`)
- `Accounting/CetakPengajuanDphController` (`spOtorisasi`, `spBatalOtorisasi`)
- `Marketing/CetakTandaTerimaController` (`spOtorisasi`, `spBatalOtorisasi`)
- `Marketing/InvoiceJasaController` (`spOtorisasi`, `spBatalOtorisasi`)
- `Purchasing/ClosingPOController` (`updateOtorisasi`, `updateBatalOtorisasi`)
- The old Pembelian ACC variants (`newpobeliacctunai`, `newpobeliaccbiayatunai`, `newpobeliaccbiayakredit`) now only
  redirect to `newpobeliacc`, which is converted.
- Dead copies: `*-copy.php`, `*(1105).php`, `*(1305).php`, `*Old.php`, `POController1.php`, `NewTesController.php`, and so on.

## Notes

- **Every unconverted page has an Otorisasi endpoint**; whether each one also checks the user's right on the server
  has not been checked page by page. On the pages converted so far, the server check was missing on most of them and
  was added as part of the conversion.
- Rights prerequisite for multi-level pages: a level-1-only approver must exist for the routing by amount to find
  recipients (see guide §10d.1 for the PO Non Stock example).
