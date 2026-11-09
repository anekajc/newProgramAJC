# To do later — "dead" authorisation code

Noted 2026-11-05 while building the Minta Otorisasi checklist (`docs/list_update/otorisasi.md`).

Five controllers contain authorisation methods, but **no route points at those methods**. Their pages are live (the
menu entry and the `index` route exist), so what you see on screen and what the server does may not match. Each one
needs a decision first, before any "Minta Otorisasi" work: should the page authorise at all, and if so, which endpoint?

Menu data checked against `DBMENUWEB` / `dbmenu` (all five are single level, `OL = 1`).

---

## 1. Bon Sementara — `bonsementara` (0201)

- [ ] Controller: `Accounting/BonSementaraController` — has `spOtorisasi` and `spBatalOtorisasi` (around lines 462 and 467).
- [ ] No route for either method, and the page has no authorisation URL or button script. Only a hidden `akses_isotorisasi1`
      field exists in the blade.
- [ ] Decide: is Otorisasi meant to exist on this page? If yes, add routes plus the page script; if no, remove the dead methods.

## 2. Cetak Pengajuan DPH — `cetakpengajuandph` (02073)

- [ ] Controller: `Accounting/CetakPengajuanDphController` — has `spOtorisasi` and `spBatalOtorisasi` (around lines 360 and 384), no routes.
- [ ] **The blade posts to another page's endpoints:** `pelunasanpiutangdppspotorisasi` and `pelunasanpiutangdppspbatalotorisasi`
      (Pelunasan Piutang DPP). The Otorisasi button (`submitOtorisasi()`) and the Batal button (`buttonBatalOtorisasi`) both do this.
- [ ] Risk to check: clicking them from this page runs the Pelunasan Piutang DPP logic with this page's document number.
- [ ] Decide: give this page its own routes and endpoints, or hide the buttons.

## 3. Cetak Tanda Terima — `cetaktandaterima` (04044)

- [ ] Controller: `Marketing/CetakTandaTerimaController` — has `spOtorisasi` and `spBatalOtorisasi` (around lines 296 and 320), no routes.
- [ ] **The blade posts to the same wrong endpoints** as Cetak Pengajuan DPH: `pelunasanpiutangdppspotorisasi` and
      `pelunasanpiutangdppspbatalotorisasi`.
- [ ] Same risk and same decision as above.

## 4. Invoice Jasa — `invoicejasa` (04042)

- [ ] Controller: `Marketing/InvoiceJasaController` — has `spOtorisasi` and `spBatalOtorisasi` (around lines 429 and 449), no routes.
- [ ] **The blade posts to Invoice Penjualan's endpoints:** `invoicepenjualanspotorisasi` and `invoicepenjualanspbatalotorisasi`.
      The Otorisasi, Batal and Edit buttons are all rendered on the list.
- [ ] Its own method `sudahOtorisasi()` and the query `queryInvoiceJasaOtorisasi()` exist, so it was meant to have its own flow.
- [ ] Decide: own routes for these methods, or confirm that sharing Invoice Penjualan's endpoints is intended and then check that
      those endpoints handle Invoice Jasa documents.

## 5. Closing PO — `closingpurchaseorder` (030204)

- [ ] Controller: `Purchasing/ClosingPOController` — has `cekOtorisasi`, `updateOtorisasi` and `updateBatalOtorisasi` (around lines 413-431), no routes.
- [ ] The blade only calls `pocekotorisasi` (the PO page's check endpoint). One Otorisasi button has an empty `onclick=""` (around line 972).
- [ ] Decide: does Closing PO need its own authorisation? Currently the button does nothing.

---

## Before touching any of these

- [ ] Ask whether the page really uses authorisation in day-to-day work (several look like copy-paste leftovers).
- [ ] For pages 2, 3 and 4, check whether anyone has used the buttons: they may have authorised or cancelled documents on a different page.
- [ ] After the decision, follow `docs/mobile-otorisasi-guide.md` (checklist in §3-§5) to add the push request flow.
