# Saved Filter Per User — Guide

**What this gives you:** the choices a user makes in a page's filter modal (Status, Otorisasi,
Perkiraan, …) are remembered **per user, on every device**. They are stored in SQL Server (SML),
not in the browser, so they follow the user to another computer or phone.

**How to use this file:** a session that is asked to "add saved filter to page X" should read this
file, then follow **§6 Recipe** for page X. The first (and so far only) migrated page is
`reportaccountingkasharian`; it is the reference implementation (§9).

> Read [CLAUDE.md](../CLAUDE.md) first. In particular: ask the user follow-up questions until you
> are 95% confident, and finish by listing every file you changed. §7 lists the questions that are
> worth asking for each page.

---

## 1. Decisions (already made — do not re-litigate)

| Decision | Choice | Why |
|---|---|---|
| Where to store | SQL Server table `DBUSERFILTER` on the `SML` connection | Filters are small, permanent, per-user data. Redis is a cache (data can be evicted/lost on restart) and the app has no Redis in use today (`CACHE_STORE=array`, `SESSION_DRIVER=file`). localStorage does not follow the user across devices. `DBHEADERTABLE` already stores per-user table layout the same way. |
| Who is "the user" | `DBFLPASS.username`, read server-side from `\Auth::user()->username` | Same identity that `DBHEADERTABLE` and the otorisasi code use. The `User` model maps to `DBFLPASS`. Never take the username from the request. |
| When to save | When the user clicks **Terapkan** (and on an applying **Reset**, see §5) | Not on every change, so half-picked values are never stored. |
| What to save | Only `select.rt-native` that has an `id`, inside the filter modal | Status / Otorisasi / Perkiraan style dropdowns. |
| What NOT to save | Date ranges, periode, bulan/tahun | A saved "Oct 2026" is stale next month. Put `data-nosave` on any select that holds these. |
| Field discovery | Automatic from the modal markup | Most pages already use `rt-native`; no per-page field list to maintain. |
| Who creates the table | The user (DBA), by running the DDL in §2 | Same rule as the BC module: Claude never runs DDL. |
| Language of guide | English; code comments in examples stay Indonesian like the codebase | Matches the other `docs/*.md` guides. |

---

## 2. Database schema

Run once on `DBSMLNEW`:

```sql
CREATE TABLE DBUSERFILTER (
  USERNAME   varchar(50)   NOT NULL,   -- DBFLPASS.username (= Auth::user()->username)
  HREF       varchar(150)  NOT NULL,   -- page path without leading/trailing '/', e.g. 'reportaccountingkasharian'
  FILTERKEY  varchar(50)   NOT NULL,   -- id of the filter modal, e.g. 'modalFilter'
  FILTERJSON nvarchar(max) NULL,       -- {"modalPerkiraan":"1101.001"}  (select id -> chosen value)
  UPDATED_AT datetime      NOT NULL DEFAULT GETDATE(),
  CONSTRAINT PK_DBUSERFILTER PRIMARY KEY (USERNAME, HREF, FILTERKEY)
);
```

- **One row = one user × one page × one filter modal.** A page with two filter modals (different
  `id`s) gets two rows. The primary key is also the lookup index; no extra index is needed.
- `FILTERJSON` is a flat object `{ "<select id>": "<value>" }`. New pages and new fields never need
  a schema change.
- The column lengths match the server validation (`href` ≤ 150, `key` ≤ 50).
- `USERNAME`, not `USERID`, is the key — it is what `\Auth::user()->username` returns.

Useful SQL:

```sql
-- what is saved for one user
SELECT * FROM DBUSERFILTER WHERE USERNAME = 'ADRI' ORDER BY HREF, FILTERKEY;

-- make a page forget one user's filter (page falls back to its own default)
DELETE FROM DBUSERFILTER WHERE USERNAME = 'ADRI' AND HREF = 'reportaccountingkasharian';

-- reset everyone on a page (e.g. after you changed the dropdown's meaning)
DELETE FROM DBUSERFILTER WHERE HREF = 'reportaccountingkasharian';
```

---

## 3. What exists today (do not rebuild)

| Piece | File | Role |
|---|---|---|
| Controller | [app/Http/Controllers/UserFilterController.php](../app/Http/Controllers/UserFilterController.php) | `get()` and `save()` against `DBUSERFILTER` (raw parameterized SQL on `SML`) |
| Routes | [routes/web.php](../routes/web.php) | `GET /userfilter_get`, `POST /userfilter_save`, both `->middleware('auth')` |
| JS helper | [public/js/user-filter.js](../public/js/user-filter.js) | `UserFilter.restore()` and `UserFilter.save()` |
| Config partial | [resources/views/partials/user-filter.blade.php](../resources/views/partials/user-filter.blade.php) | Emits `window.USER_FILTER_CFG` (csrf token + two URLs) and loads the helper |

### 3.1 Endpoints

| | `GET /userfilter_get` | `POST /userfilter_save` |
|---|---|---|
| Params | `href` (≤150), `key` (≤50) | `href`, `key`, `filters` (JSON **string**, ≤4096 bytes), `_token` |
| Success | JSON object `{ "id": "value", … }`, or `{}` when nothing is saved | `{ "success": true }` |
| Failure | 500 if the table is missing (see §8) | 422 `{ "message": "Format filter tidak valid" }` when the payload breaks the rules below, or Laravel's 422 on missing/oversized params |

Save validation (server side): `filters` must be a **flat** JSON object (not an array), at most
**30 keys**, each key matching `^[A-Za-z0-9_-]{1,60}$`, each value a string, number or `null`.
Saving is update-then-insert (same pattern as `HeaderTableController`).
The `USERNAME` is always the logged-in user's — a user can never write another user's row.

### 3.2 JS helper API

```js
UserFilter.restore(modalSelector, onRestored)
UserFilter.save(modalSelector)
```

**`restore('#modalFilter', cb)`**
- Synchronous (`async:false`) GET, same style as the page's existing `loadXxxDropdown()` calls, so
  the values are in place before the next line of page code runs.
- For each `select.rt-native[id]` in the modal (excluding `[data-nosave]`) that has a saved value
  **and that option still exists in the dropdown**, it sets the select. Missing options are skipped
  silently, so the page keeps its own default for that field.
- Calls `cb(applied)` **always** (even with `{}`), where `applied` contains only the values that
  were really set: `{ modalPerkiraan: "1101.001" }`. Also returns `applied`.
- It does **not** trigger `change`, does **not** run the report/table, and does **not** update
  badges. All of that is the page's job, inside `cb` (see Gotcha G1).
- On a network/server error it only does `console.warn` and the page continues normally.

**`save('#modalFilter')`**
- Reads the current value of every `select.rt-native[id]` in the modal (excluding `[data-nosave]`)
  and POSTs it in the background. Failure is only a `console.warn`; it never blocks the UI.
- Reads the **DOM**, not your JS globals, so set the selects first, then call `save`.

**Keys the helper derives for you**
- `href` = `location.pathname` without leading/trailing `/` (e.g. `/reportaccountingkasharian` →
  `reportaccountingkasharian`).
- `key` = the modal element's `id`.

---

## 4. Layout checklist (the helper only exists on layouts that include the partial)

A page can only use `UserFilter` if its layout renders `@include('partials.user-filter')`.
Include it **right after** `@include('partials.ajax-error-logger')` at the bottom of the layout (the
same place as in `newmaster` and `report/newmaster2x`).

> ⚠️ **Always call `UserFilter` inside `$(document).ready(...)` / `$(function(){...})`, never at the top
> level of the page script.** In the report layouts the page's own `<script>` (`@section('jsreport')`)
> is printed *before* the layout's bottom scripts, so `user-filter.js` is not defined yet while the page
> script is being parsed. Document-ready fires after every script tag has loaded, which is why Kas Harian
> works. A top-level call gives `UserFilter is not defined` (G9).

The partial does nothing on its own until a page calls the helper, so adding it to a layout is safe.

Status as of 2026-11-17 (page counts = how many views `@extends` it):

| Layout file | Pages | Has partial |
|---|---|---|
| `resources/views/newmaster.blade.php` | 36 | ✅ |
| `resources/views/report/newmaster2x.blade.php` | ≈104 — it is the parent of `report/masterreport2` (86), `report/masterreportGudang` (9), `report/masterreport4` (5) and 4 direct pages | ✅ |
| `resources/views/newmasterTest.blade.php` | **158** (includes `purchasing/purchaseOrder`) | ❌ add when migrating the first page on it |
| `resources/views/accounting/newmaster.blade.php` | 5 | ❌ |
| `resources/views/master/newmaster.blade.php` | 4 | ❌ |
| `resources/views/gudang/newmaster.blade.php`, `gudang/newmasterx.blade.php` | 3 + ? | ❌ |
| `resources/views/report/newmaster2.blade.php` | 5 | ❌ |
| `resources/views/report/mastercore.blade.php` | ? | ❌ |
| `resources/views/purchasing/newmaster.blade.php` | 3 | ❌ — it also has no `ajax-error-logger`; check it is still in use before touching it |
| `@extends('master')` (34 pages), `@extends('masterreport2')` (15 pages) | 49 | no matching view file found at `resources/views/master.blade.php` / `masterreport2.blade.php` — probably dead/legacy pages; verify before migrating |
| `partials.tesnavbar`, `layouts.app` | 3 + 3 | views not found — same warning |

When you add the partial to a layout, update the ✅ in this table in the same change.

Do **not** add a layout-level `@auth` guard around the partial: several report routes have no `auth`
middleware, and a guest would then get `UserFilter is not defined`. The endpoints themselves are
still protected by `auth`.

---

## 5. Behaviour rules

1. **Restore after the dropdown is filled, before the first data load.** If the page's first query
   depends on the filter (e.g. PO's table), restore must run first, otherwise the page loads once
   with defaults and then again with the saved filter.
2. **The callback must update the page's own filter state.** Setting the `<select>` is not enough;
   the page keeps the "applied" filter in JS variables (e.g. `globalPerkiraan`, `poFilterStatus`) and
   usually re-syncs the select from them every time the modal opens (`show.bs.modal`). If the
   callback does not update those variables the modal reverts to the default on the next open.
3. **Restore must not run the report/query by itself** on pages where data loads from a button
   (Tampilkan, Proses). The user still clicks the button as before. Pages whose table loads
   automatically simply load once, using the restored value (rule 1).
4. **Save at "commit" time.** Call `UserFilter.save('#modal')` in the Terapkan handler, after the
   page has read the selects into its globals.
5. **Reset also saves — when the reset is committed.** Two styles exist in this codebase:
   - *Reset applies and closes the modal* (PO: `poResetFilter()` sets the selects to `SEMUA`,
     applies, hides, reloads): set the selects to their defaults **then call `UserFilter.save`**, so
     the next visit starts clean. Without this the saved filter comes back after a reload and it
     looks like a bug.
   - *Reset only edits the fields* and the user must still click Terapkan (Kas Harian:
     `resetAllFilters()`): do **not** save in Reset — the Terapkan that follows saves.
6. **Exclude anything time-based** with `data-nosave` (date ranges, periode, bulan/tahun,
   even if it is a `rt-native` select).
7. **Never save secrets or large lists.** Only the chosen value of a dropdown is stored.

---

## 6. Recipe — add saved filter to a page

### Step 0 — Read the page, then ask the user (see §7)
Find: the filter modal id, every `select.rt-native` in it, how/where those options are filled
(sync or async), what JS variables hold the applied filter, whether the modal re-syncs the selects on
`show.bs.modal`, the Terapkan and Reset handlers, and where the first data load happens.

### Step 1 — Make sure the layout loads the helper
Check §4. If the page's layout is ❌, add `@include('partials.user-filter')` as described and tick the
table.

### Step 2 — Make sure every field to be saved is identifiable
- Each `select.rt-native` to save needs an `id` (without one it is silently ignored).
- Add `data-nosave` to any select that must not be saved (§5.6).
- The modal element needs an `id` (the helper uses it as `FILTERKEY`).

### Step 3 — Restore on load
Place the call **after the dropdown options exist** and **before the first load**.

```js
UserFilter.restore('#modalFilter', function (v) {
  // v = only the values that were really applied, keyed by select id
  if (v.modalPerkiraan) { setPerkiraan(v.modalPerkiraan); }   // update the page's own state
  // call the page's badge/summary updater here if it has one, e.g. updateFilterBadge();
});
```

If the options are filled **asynchronously** (non-`async:false` AJAX), call `restore` inside that
AJAX's success callback, after `.html(options)`.

### Step 4 — Save on Terapkan
```js
function applyModalFilter() {
  setPerkiraan($('#modalPerkiraan').val());        // page reads the selects into its globals first
  UserFilter.save('#modalFilter');                 // then persist
  $('#modalFilter').modal('hide');
}
```

### Step 5 — Reset (only for "reset applies immediately" pages, §5.5)
Set the selects to defaults, update the globals, then `UserFilter.save('#modalFilter')`, then close/reload.

### Step 6 — Verify (§10) and update the status table (§9)

### Pattern A — report page that loads from a button (reference: Kas Harian)
Restore in `$(document).ready` after the dropdown fill; save in the Terapkan handler; leave the
Tampilkan button as the only thing that loads data.

### Pattern B — list/transaction page whose table loads automatically (example: Purchase Order, **not yet migrated**)
Illustration only — verify the real load order before copying. PO keeps the applied filter in
`poFilterStatus` / `poFilterOtorisasi`, re-syncs the selects from them in
`$('#modalFilterPO').on('show.bs.modal', …)`, and calls `loadTabelPO()` from its init path and from
the tab-shown handler. So the restore has to run before the first `loadTabelPO()`:

```js
UserFilter.restore('#modalFilterPO', function (v) {
  if (v.poModalStatus)    { poFilterStatus    = v.poModalStatus; }
  if (v.poModalOtorisasi) { poFilterOtorisasi = v.poModalOtorisasi; }
  poUpdateFilterBadge();
});
// ...later
function poTerapkanFilter () {
  poFilterStatus = $('#poModalStatus').val() || 'SEMUA'
  poFilterOtorisasi = $('#poModalOtorisasi').val() || 'SEMUA'
  UserFilter.save('#modalFilterPO')
  poUpdateFilterBadge()
  $('#modalFilterPO').modal('hide')
  loadTabelPO(true, true)
}
function poResetFilter () {          // applies immediately -> must save (§5.5)
  poFilterStatus = 'SEMUA'; poFilterOtorisasi = 'SEMUA'
  $('#poModalStatus').val('SEMUA'); $('#poModalOtorisasi').val('SEMUA')
  UserFilter.save('#modalFilterPO')
  poUpdateFilterBadge()
  $('#modalFilterPO').modal('hide')
  loadTabelPO(true, true)
}
```
`purchaseOrder` extends `newmasterTest`, which does not load the helper yet (§4).

### Pattern C — choosing a value triggers side effects (Hutang/Piutang pages; reference: `reportaccountingpiutanglpp`, `reportaccountinghutangumur`, `reportaccountingpiutangoutstandingJT`)
Setting the `<select>` is **not** enough here. The restore callback must do **exactly what the page does when the user
picks that value**:
- Find the page's own path for the control: an inline `onchange="setPerkiraan(...)"` in the markup, or a
  `$('#modalFilter').on('change', '#modalPerkiraan', …)` handler, plus what `loadPerkiraanDropdown()` does for the default account.
- Call the same setter (`setPerkiraan(kode, ket)`, `setOrderBy(v)`, `setGroupBy(v)`), **and** the dependent load that follows
  it, so the customer/supplier range matches the restored account: `autoSelectSuppRange()` on the *Umur* pages,
  `loadSuppList(kode)` on `piutanglpp`. Some pages do this inside `setPerkiraan` already (`hutanglph`, `hutangoutstandingJT`,
  `hutangpelunasan`) — read it before adding a second call.
- *Live-commit* pages (inline `onchange`, `applyModalFilter()` only hides the modal) work the same: the select **is** the state.
  Still add `UserFilter.save` in `applyModalFilter()`, right before the modal is hidden.
- A select that is not valid on its own gets `data-nosave`: the Valas mode (`modalReportMode`) is not saved, because `$` forces
  the user to pick a currency again. Say so in a comment next to the restore call.

```js
UserFilter.restore('#modalFilter', function (v) {
  if (v.modalPerkiraan !== undefined) {
    setPerkiraan(v.modalPerkiraan, $('#modalPerkiraan option:selected').data('ket') || '');
    autoSelectSuppRange();            // only where the page's load path does it (Umur pages)
  }
  if (v.modalOrder !== undefined) { setOrderBy(v.modalOrder); }
});
```

---

## 7. Ask the user first (per page)

The project rule is to ask until 95% confident. For each page, confirm only what the code cannot tell
you:

1. Which selects in the modal should be remembered, and is any of them time-based (→ `data-nosave`)?
2. Does **Reset** apply immediately (→ save) or only edit the fields (→ do not save)?
3. Does the page already auto-load data on open? If yes, is it acceptable that it loads **once with
   the restored filter** instead of the default?
4. If the page has several tabs/modals with filters, should all of them be remembered?

---

## 8. Gotchas

- **G1 — `restore` does not fire `change`.** Anything wired to `change` (badge counters, dependent
  dropdowns, cascading reloads) will not run; call those yourself in the callback.
- **G2 — Modal re-syncs from globals.** Many pages run `$('#modal').val(globalX)` on `show.bs.modal`.
  If the callback did not update `globalX`, the restored value disappears the next time the modal opens (§5.2).
- **G3 — A select without an `id` is never saved.** Add the `id` (and keep it stable: renaming an id
  orphans the old saved value, which is then ignored silently).
- **G4 — Saved value no longer in the dropdown** (e.g. a deleted account): skipped, the page keeps
  its default. This is intended; do not "fix" it by adding the option.
- **G5 — `href` comes from the URL path.** A page whose path contains an id or changes by route
  parameter (`/page/123`) would create one row per value. Such pages need a helper extension
  (an optional `href` override) before they are migrated. Also, if the app's base path changes
  (virtual directory, different host path), old rows no longer match and the filter resets once.
- **G6 — Missing `DBUSERFILTER` table.** `userfilter_get` returns a 500 (the app's `ajaxError`
  logger prints the real message in the browser console); `restore` only `console.warn`s and the page
  works with its defaults. `save` fails silently too. First check that the DDL in §2 was run.
- **G7 — Two simultaneous first-time saves** by the same user for the same page can both try to insert
  and one hits the primary key. Very unlikely (one user, one button); it only means that one save is lost.
- **G8 — Renaming a user** (`DBFLPASS.username`) orphans that user's saved filters.
- **G13 — No `applyModalFilter()` function.** Some modals close with a plain `data-bs-dismiss="modal"` Terapkan button and the
  page reads the selects straight from the DOM when Tampilkan runs (`reportstockkartustock`, `reportstockmutasistock`). Save by
  adding `onclick="UserFilter.save('#modalFilter')"` to the **Terapkan** button only — keep `data-bs-dismiss`, and leave Batal and
  the X button alone so cancelling never saves.
- **G14 — `ReportTable.init` is not always a direct statement of `ready()`.** In `reportstockmutasistock` it sits inside
  `if (g_modeReport == modereport_qty) { … }`; a restore placed in there would be skipped in the other modes. Place `restore`
  as a direct statement of `ready()` (before that `if`), and re-check indentation after inserting. Selects filled by a
  **non-synchronous** AJAX (`reportstockkartustock` → `loadGudangDropdown()`) restore inside that AJAX success callback, right after
  the options are written, not in `ready()`.
- **G11 — Commented-out selects.** Old markup is often kept "for reference" inside `{{-- … --}}` (e.g. `modalReport` in
  `reportmarketinglaporanoutso`). It is not a live control: do not restore it, and do not count it when listing a page's fields.
- **G12 — Where to put `restore` when the page has no dropdown loader** (all selects are static in the markup): inside
  `$(document).ready`, **after** the defaults are applied (`setOtorisasi(globalOtorisasi)`, `doSetHeader(...)`) and **before**
  `ReportTable.init(...)` — above the comment block that leads into it — so restored values override the defaults and the
  "Tampilan" switcher starts from them. Pages whose Apply only hides the modal (state read from the `<select>` when
  Tampilkan runs, e.g. `reportmarketinghistoryoutso`) need `UserFilter.restore('#modalFilter')` with no callback.
- **G10 — A dependent value stays at the default.** If picking a value normally also fills something else (customer/supplier
  range, a hidden `#inputXxx`, a header label), restoring only the `<select>` leaves that part on the *default* account and the
  report comes out wrong or empty. Use Pattern C (§6).
- **G9 — `UserFilter is not defined`** in the console, and the page's own script stops at that line.
  Two causes: the layout does not include the partial (do §4 first), or the call is at the top level
  of the page script instead of inside `$(document).ready` (§4 warning).

---

## 9. Migration status

| Page | Route / file | Modal id | Fields saved | Status |
|---|---|---|---|---|
| Laporan Kas Harian | `reportaccountingkasharian` — [resources/views/report/reportaccountingkasharian.blade.php](../resources/views/report/reportaccountingkasharian.blade.php) | `modalFilter` | `modalPerkiraan` | ✅ implemented (reference) |
| Laporan Bank Harian | `reportaccountingbankharian` — [resources/views/report/reportaccountingbankharian.blade.php](../resources/views/report/reportaccountingbankharian.blade.php) | `modalFilter` | `modalPerkiraan` | ✅ implemented (same as Kas Harian; Reset only edits the field, so it does not save) |
| Accounting reports (25 more pages: Aktiva, Biaya, Buku Besar, HPP, Laba Rugi, Neraca, all Hutang/Piutang…) | listed one by one in [docs/file_list/report-files-list.md](file_list/report-files-list.md) | `modalFilter` | `modalDivisi` / `modalPerkiraan` / `modalOrder` / `modalGroup`; **Valas (`modalReportMode`) not saved** (`data-nosave`) | ✅ implemented — Pattern A for Divisi/Perkiraan pages, Pattern C for the 12 Hutang/Piutang pages |
| Marketing reports (14 pages: SPB, SPB LT, SPB Hrg SO, Analisa Kotor, History SO, History Out SO, Laporan Out SO, Out GTC, Out SPB Hrg SO, Out SPPB, Reg Sale Inv, Retur Penjualan, SO, Uang Muka Out) | listed one by one in [docs/file_list/report-files-list.md](file_list/report-files-list.md) | `modalFilter` | `modalOtorisasi` / `modalTerima` / `modalStatus` / `modalOutstanding` / `modalAgen` / `modalOrder` (History Out SO: `inputOutstanding`) | ✅ implemented — restore placed just before `ReportTable.init`, through each page's own setters |
| Pengadaan (Purchasing) reports (9 pages: Debet Note, Invoice Pembelian, Rekap Invoice Pembelian, Retur Pembelian ACC/GDG, Register OS Invoice, PO, PR, Register Pembelian) | listed one by one in [docs/file_list/report-files-list.md](file_list/report-files-list.md) | `modalFilter` | `modalOtorisasi`, `modalPPN`, `modalPjasa`, `modalTipe`, `modalDiterima`, `modalStatusPO` | ✅ implemented — most pages keep the filter in plain globals (`globalOtorisasi = …`), so restore assigns the same globals |
| Stock reports (2 pages: `reportstockkartustock`, `reportstockmutasistock`) | listed in [docs/file_list/report-files-list.md](file_list/report-files-list.md) | `modalFilter` | `inputGudang` (Kartu Stock, filled by AJAX), `inputAgenSelect` (Mutasi Stock) | ✅ implemented — no `applyModalFilter()`: save goes on the Terapkan button's `onclick` (G13); Kartu Stock restores inside the AJAX callback (G14) |
| Gudang & others (3 pages: `reportopname`, `reportpemakaian`, `reportclosingopname`) | same checklist | `modalFilter` | `modalOtorisasi` | ✅ implemented — standard pattern (`closingopname` also loads the report on Terapkan; the save runs before that) |
| Pengadaan: `reportpengadaanpgoutstandingpo` | same checklist | `modalFilter` | — | ⏭️ skipped on purpose: its only select is a "Mode Report" toggle read once at load and documented as non-functional |
| Purchase Order | `purchasing/purchaseOrder.blade.php` | `modalFilterPO` | `poModalStatus`, `poModalOtorisasi` | ⬜ not started — needs partial on `newmasterTest` first |

**Reference implementation — Kas Harian** (`reportaccountingkasharian.blade.php`):
- Layout `report.masterreport2` → `report.newmaster2x`, which includes the partial.
- In `$(document).ready`, right after `loadPerkiraanDropdown();` (which is `async:false`):
  `UserFilter.restore('#modalFilter', function (v) { if (v.modalPerkiraan) { setPerkiraan(v.modalPerkiraan); } });`
  — `setPerkiraan()` updates the page's `globalPerkiraan` (rule §5.2). The report itself is still loaded
  only by the Tampilkan button (rule §5.3).
- In `applyModalFilter()`, after `setPerkiraan($('#modalPerkiraan').val());`:
  `UserFilter.save('#modalFilter');`
- `resetAllFilters()` only resets the select and the user must still click Terapkan, so it does **not** save
  (§5.5, second style).

Add a row to this table every time a page is migrated.

**Report pages checklist:** every file in `resources/views/report/` is classified, and the 56 ready pages (+ 7 blocked on
picker support) have checkboxes in [docs/file_list/report-files-list.md](file_list/report-files-list.md). Pick an unticked
page there, and tick it (and update its progress line) when the page is done.

---

## 10. Verification (manual — there is no JS test runner)

The PHPUnit suite uses sqlite and never touches SML, so it cannot cover this feature. Test in the browser:

1. DDL from §2 has been run on `DBSMLNEW`.
2. Log in as user A, open the page, open the filter modal, choose **non-default** values, click Terapkan.
   - Network tab: `userfilter_save` → 200 `{"success":true}`.
   - SQL: `SELECT * FROM DBUSERFILTER WHERE USERNAME = '<A>'` shows one row for the page with the chosen values.
3. Reload the page → the values are selected again (reopen the modal and check) and, for auto-loading pages, the
   table is already filtered. For button pages, nothing loads until the button is clicked.
4. Open the page in another browser / private window, log in as user A → same values (proves it is not browser storage).
5. Log in as user B → defaults (B has no row; A's choice never leaks).
6. Reset: for an applying-reset page, click Reset then reload → defaults (the reset was saved).
7. Edit the row in SQL so one value is not an option any more, reload → that select keeps the page default, no
   JavaScript error, other selects still restore.
8. Console must be free of `UserFilter is not defined` and of `UserFilter:` warnings.
9. Bad payload (optional): `userfilter_save` with a `filters` string over 4096 bytes, or nested JSON → 422.

PHP syntax/lint: use Laragon's PHP 8.3 (the `php` on PATH is 7.3), e.g.
`C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe -l app/Http/Controllers/UserFilterController.php`
(`vendor/bin/pint --test <file>` needs `-d extension=mbstring` there). `php artisan route:list` currently crashes on a
duplicate `ReportTesController` class that predates this feature; check the routes in `routes/web.php` by reading them.

---

## 11. Not covered yet (future phases — need a helper change, so ask first)

The helper today handles **`select.rt-native` inside a modal** only. Not supported yet:

| Control | Why it needs more | Idea |
|---|---|---|
| Picker boxes `rt-combo` (customer, barang, gudang) | They hold a code **and** a label, and may point to master data that was deleted | save `{code,label}`, validate on restore |
| Checkbox / radio / text inputs in a modal | Not `select`; need `.checked` / `.val()` handling | extend `fields()` and the apply logic |
| Filters that are not in a modal (toolbar dropdowns, form pages) | Helper takes a modal selector | accept any container selector; `key` from an option |
| Pages whose URL carries an id | `href` would differ per value (G5) | optional `href` override |

When extending the helper keep backward compatibility with the current two-line API, keep the server
validation in `UserFilterController` in sync (new value types), and update §3 of this file.

---

## 12. File map (everything this feature touches)

| File | Purpose |
|---|---|
| [app/Http/Controllers/UserFilterController.php](../app/Http/Controllers/UserFilterController.php) | server get/save + validation |
| [routes/web.php](../routes/web.php) | `userfilter_get` / `userfilter_save` |
| [public/js/user-filter.js](../public/js/user-filter.js) | client helper |
| [resources/views/partials/user-filter.blade.php](../resources/views/partials/user-filter.blade.php) | config + script tag |
| `resources/views/newmaster.blade.php`, `resources/views/report/newmaster2x.blade.php` | layouts already including the partial |
| `resources/views/report/reportaccountingkasharian.blade.php` | reference page |
| `DBUSERFILTER` (SML) | data |
