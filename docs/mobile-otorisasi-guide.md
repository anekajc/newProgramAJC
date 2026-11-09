# Mobile Otorisasi Guide — "Minta Otorisasi" + Web Push

How to make a transaction page let a user **without** the otorisasi right send an
approval request to everyone who **has** the right, get a push notification on their
phone, and approve or reject from a mobile page.

**Reference implementation: Kas** (`/kas`). Wired end to end: Kas and Bank.
**Bank** is the second module (done); §10 is the worked example, including a rollout gotcha.

> **For a Claude Code session reading this:** CLAUDE.md requires ≥95% confidence before
> editing. Do the §3 fact-gathering and ask the user about anything you cannot read from
> the code. Never copy the Kas handler blindly — the UPDATE statement, table and line
> shape differ per page (§8). After all edits, list every changed file.

---

## 1. What the user sees

```
Requester (no IsOtorisasi1)             Approvers (IsOtorisasi1 = 1 on that menu)
───────────────────────────             ──────────────────────────────────────────
Clicks the key icon on a row
  → "Anda tidak berhak otorisasi.
     Kirim permintaan ...?"  [OK]
  → POST /otorisasirequest
  → row in DBREQOTORISASI (STATUS 'P')
  → queued job SendWebPush ───────────►  Push notification on every subscribed device
                                         Tap → /otorisasi/{id}  (standalone mobile page)
                                         Shows header + lines + total
                                         [Setujui]  → handler->approve()  (same UPDATE as the page)
                                         [Tolak]    → reason required → STATUS 'R'
Push back: "disetujui" / "ditolak: ..." ◄─────────────────────┘
Bell badge in header + /otorisasiinbox ("Menunggu saya", "Permintaan saya" and "Riwayat" = the last 100 requests this user approved, otorisasi and batal; `OtorisasiService::approvedBy()`)
Home page (mobile only) shows the same counts as a summary panel
```

Users who **can** approve never see the request dialog; the key icon opens the normal
Otorisasi form (unchanged behaviour).

---

## 2. Architecture — what is shared, what you write

### Shared infrastructure (already built — do NOT modify when adding a page)

| File | Role |
|---|---|
| `app/Services/OtorisasiService.php` | All logic: `canApprove`, `approvers`, `createRequest`, `approve`, `reject`, `closePending`, `pendingFor`, `requestsBy`, `badgeCount`, `markResultsRead`, push dispatch |
| `app/Http/Controllers/OtorisasiRequestController.php` | HTTP layer: `spRequest`, `show`, `spApprove`, `spReject`, inbox, count, subscribe/unsubscribe |
| `routes/otorisasi.php` | `/otorisasirequest`, `/otorisasi/{id}[/approve\|/reject]`, `/otorisasiinbox`, `/otorisasiinboxloadall`, `/otorisasicount`, `/pushsubscribe`, `/pushunsubscribe` (all `auth`) |
| `app/Jobs/SendWebPush.php` | Queued Web Push to every device of the given USERIDs; deletes expired subscriptions |
| `app/Otorisasi/OtorisasiHandler.php` | Interface each module implements |
| `resources/views/otorisasi/approve.blade.php` | Standalone mobile approve page (no layout, no jQuery) |
| `resources/views/otorisasi/inbox.blade.php` | Inbox (extends `newmasterTest`) |
| `public/sw.js`, `public/manifest.webmanifest`, `public/js/otorisasi-push.js` | PWA + push; bell/badge logic |
| `public/css/otorisasi-push.css`, `public/css/header-mobile.css` | Bell, home panel, compact mobile header |
| `database/sql/otorisasi_request.sql` | `DBREQOTORISASI`, `DBPUSHSUBSCRIPTION` (run manually, §6) |
| `newmaster.blade.php`, `newmasterTest.blade.php` | The only layouts that load the bell + push script |

### Per page you write (the whole job)

| # | Piece | Kas example |
|---|---|---|
| 1 | Handler class `app/Otorisasi/XxxOtorisasi.php` | `KasOtorisasi.php` |
| 2 | One line in `config/otorisasi.php` | `'kas' => [...]` |
| 3 | Server-side permission check in the page controller's `spOtorisasi` / `spBatalOtorisasi` | `KasController.php` ~L829 |
| 4 | `otorisasirequest` route in the page's `*_ROUTES` JS object | `kas.blade.php` ~L510 |
| 5 | `buttonMintaOtorisasi()` + gate in `buttonDetail()` + 403 message in `submitOtorisasi()` | `kas.js` ~L5968–6080 |

---

## 3. Before writing anything — gather these facts

Read them from the code; ask the user for what you can't determine.

1. **Menu href** — `DBMENUWEB.href` of the page (= `Route::get('/x', ...)` path, = `$req->path()`).
   Verify against the DB; do not guess:
   ```sql
   select KODEMENU, Keterangan, href from DBMENUWEB where href = 'bank'   -- 0203, 'bank'
   ```
   Do **not** trust `$kodemenu` hardcoded in the controller's `index()` — Kas has `'02011'`
   (real: `'0202'`), Bank has `'02012'` (real: `'0203'`). `getAkses()` resolves by href;
   the service does too.
2. **Target table and key column** of the approve UPDATE — copy it from the page's existing
   `spOtorisasi` (see §8 — many pages add `tglbatal`, `IsBatal`, `maxol`, `cast(...)`).
3. **"Already approved" test** — usually `isnull(IsOtorisasi1,0) = 1` on that table.
4. **What to show on the mobile page** — header fields, line items, and a numeric total.
   Take the columns from the page's detail query (`spDetail`).
5. **Does the table hold other modules' documents?** (`dbTrans` is shared by Kas, Bank,
   Giro, Memorial Koreksi.) If yes, you need a `NoBukti` prefix filter (§4.1).
6. **Does the page already have the `akses_isotorisasi1` hidden input?** (Kas/Bank `_list`
   views do: `value="{!! $akses->IsOtorisasi1 !!}"`.) If not, add it.
7. **Layout** — pages on `newmasterTest` / `newmaster` get the bell automatically. Pages on
   other layouts (`accounting/newmaster`, `purchasing/newmasterx`, `gudang/newmasterx`,
   `report/*`) can still *send* requests, but approvers must visit a bell-capable layout
   once to enable notifications on their device.

---

## 4. Implementation steps

### 4.1 Handler — `app/Otorisasi/XxxOtorisasi.php`

Implements `App\Otorisasi\OtorisasiHandler`:

```php
public function summary(string $nobukti): ?array;   // null if not found
public function isApproved(string $nobukti): bool;
public function approve(string $nobukti, string $username): int; // rows updated
```

`summary()` return shape (consumed verbatim by `otorisasi/approve.blade.php`):

```php
[
  'header' => ['No. Bukti' => ..., 'Tanggal' => 'd-m-Y', 'Tipe' => ..., 'Kas' => ..., 'Note' => ...], // label => value
  'lines'  => [ ['Perkiraan' => ..., 'Lawan' => ..., 'Keterangan' => ..., 'Jumlah' => 'Rp 1.000,00'], ... ],
  'total'  => 12345.67,   // float, rendered as "Rp " + number_format(..., 2, ',', '.')
]
```

> **The approve view is generic enough for non-journal pages (since PR Non-Agen, §10b).**
> `approve.blade.php` renders each line from these optional keys: `Jumlah` (bold first line),
> `Uraian` (or `Perkiraan`) muted, `Lawan`, `Keterangan`, `Info` (extra muted line). The `Total`
> row (always `Rp`) is shown only when `summary['total']` is not null — return `'total' => null`
> for documents without a money value (e.g. a PR). A Rupiah-less total in another unit still needs
> a view change — **ask the user** before touching the shared view. `header` is free-form label => value.

`approve()` must run **exactly** the UPDATE the page's own `spOtorisasi` runs today (the Kas
handler's comment says so). Don't "improve" it — pages reset `tglbatal`/`IsBatal`, set `maxol`, etc.

**Shared-table filter.** For modules sharing a table, restrict every query in the handler
(`summary`, `isApproved`, `approve`) to this module's documents, otherwise a request made
with `menu=bank` could target a Kas number (and vice versa) and be approved by the wrong
approver set:

```sql
-- Kas  (matches KasController::fetchList)
and (A.NoBukti like '%BKK%' or A.NoBukti like '%BKM%')
-- Bank (matches BankController::fetchList)
and (A.NoBukti like '%BBK%' or A.NoBukti like '%BBM%')
```

Use the existing SQL style: raw parameterized queries on `DB::connection('SML')`, named
`:params`. No Eloquent.

### 4.2 Register — `config/otorisasi.php`

```php
'kas'  => ['label' => 'Kas',  'handler' => KasOtorisasi::class],
'bank' => ['label' => 'Bank', 'handler' => BankOtorisasi::class],   // add use-statement too
```

Key **must equal** the `DBMENUWEB.href`. `label` appears in push titles, inbox and approve header.
KODEMENU is intentionally not stored — `OtorisasiService::kodeMenu()` resolves it from the href.

### 4.3 Controller — permission check on the page's own endpoints

Kas before this feature had **no** server-side check (and no client check). Add it:

```php
use App\Services\OtorisasiService;

public function spOtorisasi (Request $req, OtorisasiService $oto) {
  $username = \Auth::user()->username;
  if (!$oto->canApprove($username, 'kas')) {
    return response('Anda tidak berhak otorisasi Kas', 403);
  }
  $res = $oto->handler('kas')->approve($req->nobukti, $username);
  $oto->closePending('kas', $req->nobukti, $username);   // closes pending requests + notifies requesters
  return $res;
}

public function spBatalOtorisasi (Request $req, OtorisasiService $oto) {
  if (!$oto->canApprove(\Auth::user()->username, 'kas')) {
    return response('Anda tidak berhak batal otorisasi Kas', 403);
  }
  // ...existing UPDATE + LoggingData stays exactly as it was
}
```

- Approval from the page **must** go through `handler->approve()` (single source of truth)
  and then `closePending()`, otherwise a pending request stays open after someone approved
  directly on desktop. (The service also self-heals: approving a request whose document is
  already approved marks it `C` "Sudah diotorisasi langsung".)
- The `'kas'` string is the href, same as in the config.

### 4.4 Blade — add the route

In the page's `window.XXX_ROUTES = {...}` object (alphabetical, like the rest):

```php
otorisasirequest: "{!! url('otorisasirequest') !!}",
```

The route itself is global (`routes/otorisasi.php`); nothing to add to `routes/accounting.php`.

### 4.5 JS — three edits in the page's script (`public/js/<page>.js`)

**(a) Add `buttonMintaOtorisasi`** (next to `submitOtorisasi`). Change the `KAS_ROUTES`
prefix, the `menu:` value (= href) and the label text:

```js
function buttonMintaOtorisasi (nobukti) {
  var dlgMinta = alertify.confirm('Minta Otorisasi',
    'Anda tidak berhak otorisasi. Kirim permintaan otorisasi Kas ' + nobukti + ' ke approver?',
    function() {
      $.ajax({
        url: KAS_ROUTES.otorisasirequest,
        type: "post",
        data: { _token: $("#_token").val(), menu: 'kas', nobukti },
        success: function(res) { alertify.success(res.message) },
        error: function (err) {
          console.log(err)
          alertify.warning((err.responseJSON && err.responseJSON.message) || 'Terjadi kesalahan silahkan refresh browser')
        }
      })
    },
    function(){});
  dlgMinta.elements.root.classList.add('ajs-app-buttons');
}
```

**(b) Gate at the top of `buttonDetail`** (before `refreshDataTableDetail`):

```js
function buttonDetail (nobukti , tipe = 'detail') {
  if (tipe == 'otorisasi' && !Number($("#akses_isotorisasi1").val())) {
    buttonMintaOtorisasi(nobukti)
    return
  }
  // ...existing body
```

**(c) Show the server's 403 text in `submitOtorisasi` (and `buttonBatalOtorisasi`) error handler:**

```js
error: function (err) {
  console.log(err)
  alertify.warning(err.status == 403 ? err.responseText : 'Terjadi kesalahan silahkan refresh browser')
}
```

The request endpoint's JSON contract is `{ok: bool, message: string}`; statuses: `200` created or
already pending, `422` unsupported menu / requester can approve / already approved / **no approver exists
for the menu** (nothing is saved), `404` not found. The `200` message names the approver count, or warns
when no approver has a push-enabled device.
Don't change the row-button markup: the key icon on unapproved rows stays visible to everyone —
the gate in (b) decides what it does.

---

## 5. Verification checklist

1. **Lint & compile** (note §7 about PHP versions):
   ```bash
   php -l app/Otorisasi/XxxOtorisasi.php && php -l app/Http/Controllers/.../XxxController.php
   php artisan view:cache      # compiles every Blade view
   ```
   `php artisan route:list` currently fails on an unrelated broken route
   (`CetakTandaTerimaController`) — check routes through a script/tinker instead.
2. **Handler against real data** (script in §7): `summary()` returns header/lines/total for a
   real unapproved `NoBukti`; `isApproved()` false; do **not** call `approve()` on production data.
3. **Browser, 3 accounts** (user with no right, user with the right, second approver):
   - No right: key icon → confirm → "Permintaan otorisasi terkirim"; click again → "sudah dikirim oleh … masih menunggu".
   - Approver gets a push (queue worker running!); tap opens `/otorisasi/{id}`; **Setujui** works; second approver then sees "sudah diproses".
   - **Tolak** requires a reason; requester gets "ditolak: …" push and sees it under *Permintaan saya*.
   - Approver clicks the key icon directly on the page → pending request becomes `A` and requester is notified.
   - Direct POST to `…spotorisasi` as a no-right user → `403`.
   - Request for an already-approved document → "Transaksi sudah diotorisasi".
4. **Mobile**: header, bell badge, `/otorisasiinbox`, home-page panel (< 768px only).

---

## 6. One-time server setup (already done for the current server)

| Item | Detail |
|---|---|
| Tables | Run `database/sql/otorisasi_request.sql` manually on `DBSMLNEW` (SSMS). Not a Laravel migration — business data is on SQL Server. Idempotent (`IF OBJECT_ID ... IS NULL`). |
| VAPID keys | `php artisan webpush:vapid` once → put `VAPID_PUBLIC_KEY`, `VAPID_PRIVATE_KEY`, `VAPID_SUBJECT` in `.env`. **Never regenerate** — it invalidates every device subscription. |
| Windows `OPENSSL_CONF` | web-push creates an EC key on every send. On Windows set the env var **before PHP starts** (putenv doesn't work), e.g. `C:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\extras\ssl\openssl.cnf`. Needed for the web server **and** the queue worker, else push silently fails. |
| Queue worker | `QUEUE_CONNECTION=database`. A worker must run in production (`php artisan queue:work`); `composer run dev` already starts `queue:listen`. Without it, requests save but nobody is notified. |
| HTTPS | Push + service workers need a public HTTPS origin (localhost is OK for dev). |
| iPhone | Push only works after *Share → Add to Home Screen*, opening from the icon, then tapping the bell (permission must come from a tap). The inbox and home panel show this hint. |
| Icons | `public/img/pwa-192.png`, `pwa-512.png`, `pwa-badge.png` are generated placeholders — swap in the real logo. |

---

## 7. Local testing tips (this Windows machine)

- **`php` on PATH is 7.3** (Laragon default). The app needs 8.3 — use the full path:
  `C:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\php.exe`. With 7.3, artisan dies on the Composer platform check.
- `php artisan tinker --execute="..."` breaks on multi-line/quoted code under PowerShell. Instead write
  a small script that bootstraps the app and run it with the 8.3 binary:
  ```php
  <?php
  require 'D:/newProgramAJC/vendor/autoload.php';
  $app = require 'D:/newProgramAJC/bootstrap/app.php';
  $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
  use Illuminate\Support\Facades\DB;
  foreach (DB::connection('SML')->select("select * from DBMENUWEB where href = 'bank'") as $r) echo json_encode($r), PHP_EOL;
  ```
- `DBMENUWEB` has columns `KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color` (no `NAMAMENU`).
- Test `canApprove`/`approvers` through `app(OtorisasiService::class)`; they only read.
- The `SML` connection is the real business DB. Read-only checks are fine; don't run `approve`/`createRequest` against real documents.

---

## 8. Variants catalog — which UPDATE each page uses

Built from a grep of every `spOtorisasi` in `app/Http/Controllers` (backup/`Old`/`-copy`/dated copies
excluded). **Heuristic — re-read the actual controller before writing a handler.** "Href" is the
`index` route; `?` means the route has a different name — look it up in `DBMENUWEB`.

| Table | Pages (controller → href) | Approve UPDATE shape |
|---|---|---|
| `dbtrans` | Kas `kas`, Bank `bank`, GiroDibuka `girodibuka`, GiroDiterima `giroditerima`, MemorialKoreksi `memorialkoreksi` | `isOtorisasi1=1, maxol=1, OtoUser1, TglOto1` where `nobukti`. **Shared table → prefix filter required (§4.1).** |
| `dbdph` | PengajuanDPH `pengajuandph`, PengajuanDPHTunai `pengajuandphtunai` | same + `tglbatal=NULL, userbatal=''` |
| `dbdph` | PengajuanDPP (`?`) | same as dbtrans (no batal reset) |
| `DBTerimaDPP` | BonSementara (`?`), CetakPengajuanDph (`?`), CetakTandaTerima `cetaktandaterima`, PelunasanPiutangDPP `pelunasanpiutangdpp`, PenerimaanDPP (`?`) | same + `tglbatal=NULL, userbatal=''`. Shared table. |
| `dbinvoicepl` | InvoicePenjualan `invoicepenjualan`, InvoiceJasa `invoicejasa` | `IsOtorisasi1=1, OtoUser1, TglOto1=getDate(), IsBatal/UserBatal/TglBatal=NULL` (no `maxol`) |
| `dbSPB` / `dbrSPB` / `DBINVOICERPJ` | SuratJalan, ReturSuratJalan, NotaReturPenjualan | `IsOtorisasi1`… with batal reset; ReturSuratJalan sets `MaxOL=1` |
| `dbkreditnote`, `dbprrjual`, `dbSPBRJUAL` | KreditNote, PerintahReturJual(+Minus), ReturPenjualanGudang | `where nobukti = cast(:nobukti as varchar(10))` — keep the cast |
| `dbso`, `dbpenawaranso` | SO, PenawaranSO | `maxol = :maxol` **computed** (multi-level aware) — not a plain level-1; discuss with user first |
| `dbpo`, `dbbeli`, `dbinvoice`, `dbrbeli`, `DBUMJUAL` | ClosingPO, NewPOBeliAcc*, InvoicePembelian, InvoiceReturBeli, UangMuka* | per controller; verify (UangMuka mapping from the grep is unreliable) |
| `dbkoreksi` | KoreksiStock, OpnameBarang | simple level-1 |
| Gudang tables | `dbbaop`, `dbperintahop`, `DBSERAHSAMPLE`, `DBPRSAMPLE`, `DBRSERAHSAMPLE`, `DBBEBANSAMPLE`, `dbPenyerahanBhn`, `dbPRPenyerahanBhn`, `dbprtransfer`, `dbtransfer`, `DBUBAHKEMASAN` | simple level-1 |
| *Verifikasi* pages | VerifikasiBarang, VerifikasiPenawaran | different mechanism — inspect |

Pages that differ from "list of journal lines" (invoices, SO, gudang) also need the §4.1 gotcha
on the approve view and a decision on what the mobile summary should show.

---

## 9. Known gaps and deferred items

- **~35 other `spOtorisasi`/`spBatalOtorisasi` endpoints have no server-side permission check**
  (only an optional client-side `akses_isotorisasi1` check). Kas is fixed; Bank and the rest are not.
  Converting a page per this guide closes that hole for it. Fixing all of them is a separate task.
- **Multi-level otorisasi is supported only for handlers that implement `LeveledOtorisasiHandler`** (so far PO Stock,
  §10d). Everything else is single-level `IsOtorisasi1`. `dbso`/`dbpenawaranso` compute `maxol` from `dbmenu.OL` (1) and are
  converted as single-level (§10i, §10j); SO keeps its own blacklist/plafon rules inside the handler.
- **Kas handler has no `NoBukti` prefix filter** (nothing prevents `menu=kas` with a Bank number).
  Bank's handler has one (§10); Kas still needs the BKK/BKM equivalent — a small change plus a retest.
- **Requester's own device:** approvers who are the requester are excluded from the push
  (`array_diff`), but a user with the right never sees the request dialog anyway.
- **Batal otorisasi push flow exists only for pages whose handler implements `BatalOtorisasiHandler`**
  (PR Non-Agen and PR Keagenan, §10c). Kas, Bank and every other page still only support approval requests.
- **Revoked right:** an existing pending request stays `P` if an approver's right is removed;
  it is only stuck if no approver is left.
- Composer reported 25 security advisories in existing packages (unrelated to this feature).
- Placeholder PWA icons; no offline mode by design (`sw.js` does no caching).

---

## 10. Worked example — Bank (DONE, handler shape A)

**Status: Bank is wired** using shape A below. What it replaced: `BankController::spOtorisasi` /
`spBatalOtorisasi` had **no permission check**, `bank.js` `buttonDetail` had no request gate,
`submitOtorisasi` showed a generic error, `BANK_ROUTES` had no `otorisasirequest`.
href `bank`, `KODEMENU` `0203`. Bank rows live in `dbTrans`/`dbTransaksi`, filtered
`NoBukti like '%BBM%' or '%BBK%'` (Kas: BKM/BKK; real numbers look like `SML/BBM/00001RI/1026`).
Same approve UPDATE as Kas.

> **Rollout step that is easy to forget:** the permission check is now enforced, so a menu where
> *nobody* has `DBFLMENUWEB.IsOtorisasi1 = 1` has **no approver at all**. Bank had none when it was
> wired (`HARRY/Noel/putra/SA` all `0`). Grant the right (the user-access screen, menu "Bank" /
> `KODEMENU 0203`) **before** releasing, or Bank otorisasi stops working and requests notify nobody.
> Check any page before converting it:
> `select USERID, IsOtorisasi1 from DBFLMENUWEB where L1 = '<KODEMENU>' and HASACCESS = 1`.

> **Lesson — escape quotes in the handler SQL.** Handlers use single-quoted PHP strings, so the
> prefix filter must be written `like \'%BBK%\'`. Unescaped, `php -l` still passes (it parses as a
> constant) and the failure only shows at runtime as `Undefined constant "App\Otorisasi\BBK"`.
> Always run the handler against real data (§5 step 2, §7), not just a lint.

### Handler shapes (A was chosen for Bank)

- **A — copy `KasOtorisasi` → `BankOtorisasi`** (smallest change; duplicates ~70 lines; Kas untouched).
  Changes: header key `'Kas'` → `'Bank'`, BBK/BBM prefix filter on all three queries. **Used for Bank.**
- **B — shared base (worth doing when more `dbtrans` pages are converted — Giro, Memorial Koreksi)**:
  extract the Kas queries into an abstract `DbTransOtorisasi` with two abstract methods and have
  both extend it. Touches Kas, so retest Kas.

```php
abstract class DbTransOtorisasi implements OtorisasiHandler
{
    /** SQL fragment on alias A.NoBukti, e.g. "(A.NoBukti like '%BBK%' or A.NoBukti like '%BBM%')" */
    abstract protected function prefixFilter(): string;
    /** Header label for the account row: 'Kas' / 'Bank' */
    abstract protected function accountLabel(): string;
    // summary(): KasOtorisasi::summary() with  "and ".$this->prefixFilter()  added and 'Kas' => accountLabel()
    // isApproved()/approve(): same SQL as KasOtorisasi, restricted by the prefix filter (alias dbtrans A)
}
```

### Files changed for Bank

| File | Change |
|---|---|
| `app/Otorisasi/BankOtorisasi.php` | **New** handler (header `'Bank'`, BBK/BBM filter) |
| `config/otorisasi.php` | `+ use App\Otorisasi\BankOtorisasi;` and `'bank' => ['label' => 'Bank', 'handler' => BankOtorisasi::class],` |
| `app/Http/Controllers/Accounting/BankController.php` | `use App\Services\OtorisasiService;` — `spOtorisasi` and `spBatalOtorisasi` per §4.3 with href `'bank'`; keep the `LoggingData('btloto','BANK', ...)` call |
| `resources/views/accounting/bank.blade.php` | `BANK_ROUTES`: `otorisasirequest: "{!! url('otorisasirequest') !!}",` |
| `public/js/bank.js` | `buttonMintaOtorisasi` (`BANK_ROUTES`, `menu: 'bank'`, text "Bank"); gate in `buttonDetail`; 403 message in `submitOtorisasi` and `buttonBatalOtorisasi` |

No SQL, route, layout or service changes are needed — Bank's layout is `newmasterTest` (bell included).
Both `kas.js` and `bank.js` define `buttonDetail`, `submitOtorisasi`, etc. as globals, but each page loads only its own script.

Verified read-only against the real DB: `summary()` of a real Bank number returns header/lines/total;
`isApproved()` false; the Bank handler returns `null` for a Kas number (`SML/BKK/...`). Still to do in a
browser: the §5 checklist (needs an account with `IsOtorisasi1` on Bank — see the rollout note above).

---

## 10b. Worked example 2 — PR Stock Non Keagenan (DONE; a non-Kas-shaped page)

href `pembelianpermintaannonagen`, `KODEMENU` `030101`, table `DBPPL` (+ `DBPPLDET`, view `VwPPL`).
Differences from Kas/Bank that you will meet on other purchasing/gudang pages:

- **Endpoints are `updateOtorisasi` / `updateBatalOtorisasi`**, not `spOtorisasi`. They already refuse
  with `PenjagaTransaksi::tolak()` = **HTTP 200 + `{ok:0, pesan}`**, which the page JS already shows —
  so the permission refusal uses `tolak('Anda tidak berhak otorisasi PR')`, not a 403, and no JS change
  is needed for it. The success response keeps its shape (`ok`, `otouser`, `tgloto`).
- **JS is inline in the blade** (no `public/js` file, no `*_ROUTES` object): the route is
  `{!! url('otorisasirequest') !!}` and the menu value is the page's existing `PR_HREF` const.
  `buttonOtorisasi` already authorised on click (no form), so the gate is its existing
  "no `akses_isotorisasi1`" branch: `alertify.warning('No access')` became `buttonMintaOtorisasi(nobukti)`.
- **Shared table `DBPPL`**: PR Agen (`pAgen=1`), PR Jasa (`pJasa=1`) and Non-Agen live together. Every handler
  query is filtered `isnull(pAgen,0)=0 and isnull(pJasa,0)=0` (the page list is `vwppl … IsJasa=0 and pAgen=0`).
  Verified: the handler returns `null` for an Agen, a Jasa and a Kas number.
- **The controller routes through the handler**: `updateOtorisasi` keeps its friendly `headerDokumen`
  pre-checks, then calls `handler->approve()` and `closePending()`. The audit entry
  `LoggingData('oto','PR',…)` moved **into the handler's `approve()`** (only when rows were updated), so
  desktop and phone approvals leave the same `dbLogFile` trail. (Kas/Bank handlers do not log from the phone — a gap.)
- **No money total**: summary returns `'total' => null`, lines use `Uraian`/`Jumlah` (`5,00 PCS`)/`Info`.
- Approvers `putra`, `SA` already had `IsOtorisasi1` on 030101, so no rollout step; `HARRY`/`Noel` get the dialog.
- `updateBatalOtorisasi` is gated with `canCancel` (`IsBatal`) — the same right the page's button checks
  (`akses_isbatal`) — and users without it get the "Minta Batal Otorisasi" request flow (§10c).

**Sibling page done the same way: PR Keagenan** (`pembelianpermintaanagen`, `KODEMENU` `030102`) — handler
`PembelianPermintaanAgenOtorisasi` (filter `pAgen=1 and pJasa=0`), same controller/JS edits. Its
`updateOtorisasi` returns the raw row count and its JS tests `res > 0`, so the controller keeps returning
`$res` (int) instead of the Non-Agen `{ok, otouser, tgloto}` array. **PR Non Stock** (`pembelianpermintaannonstock`,
`030103`) is also done, with both Minta Otorisasi and Minta Batal Otorisasi (§10c): handler
`PembelianPermintaanNonStockOtorisasi` filters `pJasa=1 and pAgen=0` (the page list is `vwppl … IsJasa=1 and pAgen=0`),
and its controller/JS follow the Agen pattern (int responses). All three `DBPPL` pages are now converted.
Rights are per user and change over time — check `DBFLMENUWEB.IsOtorisasi1` **and** `IsBatal` for the menu before testing
(a menu where nobody has `IsBatal` answers a batal request with "Belum ada user yang berhak batal otorisasi").

Files (Non-Agen): `app/Otorisasi/PembelianPermintaanNonAgenOtorisasi.php` (new), `config/otorisasi.php`,
`app/Http/Controllers/Purchasing/PembelianPermintaanNonAgenController.php`,
`resources/views/purchasing/pembelianpermintaannonagen.blade.php`,
`resources/views/otorisasi/approve.blade.php` (generic lines + optional total).

---

## 10c. "Minta Batal Otorisasi" (request type `B`) — done for the two PR pages

A user **without** `IsBatal` clicks the red Batal key, types a reason, and everyone with `IsBatal = 1` on that
menu gets a push. They open `/otorisasi/{id}` (titled "Batal Otorisasi …", reason shown, items listed) and
**Setujui** cancels the document's otorisasi; **Tolak** declines. The requester is pushed the result.

**One-time DDL before deploying (run on every server, idempotent):** `database/sql/otorisasi_request_batal.sql`
adds `DBREQOTORISASI.TIPE char(1) NOT NULL DEFAULT 'O'` (`O` otorisasi, `B` batal) and `ALASAN varchar(255) NULL`
(requester's reason; `CATATAN` stays the approver's reject reason). Old rows become `O`. The new code reads/writes
`TIPE`, so deploying it before the DDL breaks **all** otorisasi requests, not only batal.

How it is built (generic — another page adopts it with two steps):

| Piece | Where |
|---|---|
| Optional interface | `app/Otorisasi/BatalOtorisasiHandler.php`: `cancelBlocker($nobukti): ?string` (why it must NOT be cancelled, e.g. a PO exists) and `cancelApproval($nobukti, $username, $alasan): int` (the page's own batal UPDATE + its `LoggingData('btloto')`). Kas/Bank do not implement it, so they are unaffected. |
| Rights | `OtorisasiService::canCancel()` / `canAct($user,$menu,$tipe)` / `approvers($menu,$tipe)` use `DBFLMENUWEB.IsBatal` for `B` and `IsOtorisasi1` for `O`. The inbox queue and bell badge use the matching right per row's `TIPE`. |
| Request | `POST /otorisasirequest` takes optional `tipe` (`O` default, `B`) and `alasan` (required for `B`). 422 when: menu has no cancel support, requester already has the Batal right, document not yet authorised, document blocked (PO), no `IsBatal` approver exists. |
| Approve | `OtorisasiService::approve()` → `approveBatal()`: claims the request, re-checks "still authorised" and the blocker (the state may have changed since the request), then `cancelApproval()`. A request that can no longer be honoured is closed `C` with the reason in `CATATAN`. |
| Direct batal on the page | Controller: `canCancel` check, `handler->cancelBlocker()`, `handler->cancelApproval()`, then `closePending($href, $nobukti, $user, OtorisasiService::BATAL)` so a pending `B` request closes and the requester is told. `closePending` only touches the matching `TIPE`. |
| Views | `approve.blade.php` switches wording on `$request->TIPE` and shows the reason card; `inbox.blade.php` shows a red "Batal Otorisasi" tag and the reason. |

To adopt it on another page: (1) `implements BatalOtorisasiHandler` on its handler with the page's own batal
UPDATE (keep its filters, add an `IsOtorisasi1 = 1` guard); (2) in the page JS, send the "no `akses_isbatal`"
branch to a `buttonMintaBatalOtorisasi` that prompts for a reason and posts `{menu, nobukti, tipe: 'B', alasan}`
(copy it from `pembelianpermintaanagen.blade.php`), and change the controller's batal check to `canCancel`.

Tested read-write inside a rolled-back transaction (so nothing persisted) against the real DB: request, queue
visibility per right, approve, reject, PO-appears-later, direct batal closing the request, and `O` regression.

---

## 10d. Multi-level: PO Stock (`purchaseorder`, `KODEMENU` 030201) — "send to the highest level"

PO has two levels (`DBMENUWEB.OL = 2`) and approval depends on the approver's jabatan/plafon, so it implements the
optional `App\Otorisasi\LeveledOtorisasiHandler` (`PurchaseOrderOtorisasi`). Decisions made with the user:

- **Recipients depend on the PO amount.** `approverLevel($nobukti)` returns 1 for a PO up to 200 jt and 2 above it
  (`SUM(NNetRp)`, the same figure the plafon rules use). The request is stored with that level in
  `DBREQOTORISASI.LEVELOTO` (the column existed, it was always 1 before — no DDL). Push, inbox queue, bell badge
  and the right check use `DBFLMENUWEB.IsOtorisasi{LEVELOTO}`: up to 200 jt the level-1 approvers are notified and
  can authorise (within the plafon, level 1 + 2 together); above 200 jt only level-2 approvers are.
- **"Highest level held" (multi-level handlers only).** A level-N request goes only to users whose highest level is N
  (`OtorisasiService::hanyaLevel()`): up to 200 jt it goes to SA but NOT to Ezra, who also holds level 2 (he is
  notified only for POs above 200 jt). The same rule applies to the queue, bell badge, `canAct` and the approve page,
  so a level-2 holder cannot approve a level-1 request on the phone (the desktop page is unchanged and still lets
  him). Single-level handlers (Kas, Bank, PR) and batal requests are not affected by stray higher-level flags.
- **Self-correcting routing.** Clicking "Minta Otorisasi" again on a PO whose pending request sits at a level that
  no longer matches (old rule, or the PO amount changed) moves it to the right level and pushes the right people
  (reply: "dialihkan ke N approver yang sesuai").
- **The plafon rules are NOT bypassed on the phone.** `approveWithRules()` is the old `POController::updateOtorisasi`
  logic moved verbatim: within the approver's plafon → level 1 and 2 together; jabatan `03` and PO > 200 jt → level 1
  only (**partial**); `DIR` → level 2, only after level 1 (else code 3); over plafon → code 2 "Melebihi plafon".
  Result `done` closes the request (`A`); `partial` keeps it `P` with a progress note, tells the requester and
  re-notifies the other top-level approvers; `refused` changes nothing and keeps it `P`.
- **Price check on the phone:** `summary()['warnings']` (PO vs SO price, the old `cekHargaOto` SQL, now the static
  `PurchaseOrderOtorisasi::cekHargaBarang()` shared with the desktop) renders as a read-only "Perhatian" card in
  `approve.blade.php`. It never blocks Setujui.
- **Shared endpoint:** `poupdateotorisasi` / `poupdatebatalotorisasi` are used by both PO pages. The controller reads the PO's own
  `pJasa`, picks the menu (`purchaseorder` or `pononstock`) and its handler from that, checks the right on that menu
  (otorisasi: level 1 **or** 2; batal: plain `IsBatal`), and maps the handler result back to the legacy replies the page
  JS reads (`1`, `2`, `3`, or `tolak`). `approveWithRules()` itself is not `pJasa`-filtered; `summary()/isApproved()/
  hasApproval()` are, by the handler's `$pJasa`.
- A level-1 user cannot send a request (they have `IsOtorisasi1`, so they are told to authorise directly). A PO left at
  level 1 only that needs a director has no automatic follow-up request unless one already exists.
- A PO that was voided by batal (`isbatal = 1`, both approvals 0) **can be authorised again**, on the desktop and on the phone, exactly as the old `POController`
  allowed. `approveWithRules()` once refused it ("sudah dibatalkan"); that check was removed on 2026-11-09 because users re-authorise batal'd POs as a normal
  workflow (e.g. `SML/PO/00001/1026`: batal 1 Oct, re-authorised 27 Oct). Like the old code, re-authorising does **not** clear `IsBatal`/`UserBatal`/`TglBatal`.

### 10d.1 PO Non Stock (`pononstock`, `KODEMENU` 030202) — same handler, `pJasa = 1`

`PurchaseOrderNonStockOtorisasi extends PurchaseOrderOtorisasi` and only overrides `protected int $pJasa = 1`;
registered in `config/otorisasi.php` as `pononstock` ("PO Non Stock"). Same amount routing, plafon rules, summary and
batal as PO Stock. **Rights prerequisite:** the "highest level" rule needs a level-1-only approver. On this server SA
was ticked for levels 1–5 on `pononstock` (on `purchaseorder` SA is level 1 only), so a PO up to 200 jt answers
"Belum ada user yang berhak otorisasi" until an admin leaves only `IsOtorisasi1` for SA there.

### 10d.2 PO batal ("Minta Batal Otorisasi") — PO Stock and PO Non Stock

- **Recipients:** `IsBatal` holders whose highest approval level equals the amount level (`approverLevel`): up to
  200 jt level 1 (SA), above it level 2 (Ezra). `OtorisasiService::hanyaLevel()` adds `IsOtorisasiN = 1` for batal on
  leveled handlers. `canCancel()` (the plain right check used by the desktop page and `spRequest`) is **not** level
  filtered (`canAct(..., $sesuaiLevel: false)`).
- **What can be cancelled:** `LeveledOtorisasiHandler::hasApproval()` — any level set (a >200 jt PO with only level 1
  is cancellable, as on the desktop). `OtorisasiService::adaOtorisasi()` uses it instead of `isApproved()`.
- **What approving does = the desktop button, decided by the approver's plafon** (`PurchaseOrderOtorisasi::cancelApproval`,
  the old `POController::updateBatalOtorisasi` logic): PO value (`SUM(NNetRp)`) above the approver's plafon → only the
  approvals are cleared (level 1 only if the approver's first level is 1, else both); within plafon → approvals cleared
  **and the PO is voided** (`isbatal = 1`, `Userbatal`, `tglbatal`). Logs `btloto` / `btloto2`.
- **Approve page tells the approver first:** optional `BatalPreviewHandler::cancelPreview()` renders a red "Yang terjadi
  bila disetujui" card (voided vs approvals only).
- **Blockers** (`cancelBlocker`): received at warehouse (`DBBELIDET.NoPO`) and Uang Muka Beli (`DBUMJUAL`), checked when the
  request is created and again when it is approved.
- **Desktop button now needs `IsBatal`** (was `IsOtorisasi1`) on both PO pages; users without it send a request with a reason.
  Pending batal requests are closed when someone cancels directly (`closePending(..., BATAL)`).
- Deviation from the old code: the plafon comparison uses `NNetRp` (rupiah) like the approve rule; the old batal code used `SUM(nnet)`
  (document currency), which only differs for foreign-currency POs.

Files: `app/Otorisasi/LeveledOtorisasiHandler.php` (+`hasApproval`), `BatalPreviewHandler.php` (new),
`PurchaseOrderOtorisasi.php`, `PurchaseOrderNonStockOtorisasi.php` (new), `config/otorisasi.php`,
`app/Services/OtorisasiService.php` (`hanyaLevel` for batal, `canAct($sesuaiLevel)`, `adaOtorisasi`, batal re-route),
`OtorisasiRequestController::show`, `approve.blade.php`, `POController.php`, `purchaseOrder.blade.php`,
`purchaseOrderNonStock.blade.php`.

---

## 10e. Pembelian ACC (`newpobeliacc`, `KODEMENU` 0304) — single level, posts hutang + jurnal

One page serves all four old menus (Tunai/Kredit × Stock/Non Stock) over `DBBELI`, so `PembelianAccOtorisasi` has no
`pJasa`/`TipeBayar` filter; the mobile summary shows the kind as "Kredit - Stock" etc. Decisions with the user: Minta
Otorisasi **and** Minta Batal, summary = header + item lines (qty × harga) + total (`NNetRp`), and **rights are now
enforced on the desktop endpoints** (they had no check at all).

- **Approve** (`approve()`): the page's guarded `UPDATE dbbeli … and isnull(IsOtorisasi1,0)=0` followed by
  `sp_ProsesPostingHutPiut` + `sp_ProsesPostingJurnalOto` (flag 1). The posting uses the **logged-in user's working
  period** (`GlobalController::getPeriode()`), exactly like pressing Otorisasi on the page — on the phone that is the approver's period.
- **Batal** (`cancelApproval()`): `IsOtorisasi1 = 0, MaxOL = -1` guarded by `IsOtorisasi1 = 1`, then the same procedures with flag 0.
  `cancelBlocker()` reuses `NewPOController::dipakaiBeli()` (invoice / return / payment / DPH). The page never stored the batal
  reason (and still doesn't); on the phone it lives in `DBREQOTORISASI.ALASAN`.
- **Controller** (`NewPOBeliAccController::spOtorisasi1 / spUnOtorisasi1`): `canApprove` / `canCancel` check first (reply
  `tolak('Anda tidak berhak …')`), then the handler, then `closePending`. Replies are unchanged (int > 0, or `tolak`).
- **Blade:** hidden `akses_isotorisasi1` / `akses_isbatal` (also refreshed in `loadAll()`), `buttonMintaOtorisasi` /
  `buttonMintaBatalOtorisasi`. The gate sits inside `submitOtorisasi1` (both definitions in the file — the later one wins) and
  `submitUnOtorisasi1`, i.e. a user without the right can still open the detail first and is asked to send a request when
  pressing the button inside it.
- Files: `app/Otorisasi/PembelianAccOtorisasi.php`, `config/otorisasi.php`, `NewPOBeliAccController.php`, `newpobeliacc.blade.php`.
  Verified end-to-end in a rolled-back transaction including the posting procedures (DBHUTPIUT rows 0→1 on approve, 1→0 on batal).

---

## 10f. Perintah Retur Beli (`perintahreturbeli`, `KODEMENU` 030601) — single level, flag only

`PerintahReturBeliOtorisasi` (`DBPRRBELI`, `OL = 1`): the same shape as the PR pages. Built with the answers already settled on
the other pages (Minta Otorisasi **and** Minta Batal, header + item list + total, generic approve view, audit log from the
handler, batal right = `IsBatal`, desktop rights enforced) — no new questions were needed.

- **Approve:** the page's guarded `UPDATE DBPRRBELI … and isnull(IsOtorisasi1,0)=0` + `LoggingData('oto','PRB',…)`. No posting.
- **Batal:** `IsOtorisasi1 = 0, maxol = -1` guarded by `IsOtorisasi1 = 1` + `LoggingData('btloto','PRB', …, alasan)`.
  `cancelBlocker()`: a Retur Pembelian Gudang already points at it (`DBRBELIDET.NOPRB`).
- **Summary:** supplier(s) and purchase number(s) come from the item rows (the header has no supplier); lines show qty × harga and `NNetRp`.
- **Controller:** `canApprove` / `canCancel` first (these endpoints had no check; the JS used IsOtorisasi1 for both buttons), then the
  handler, then `closePending`. Replies unchanged (int > 0, or `tolak`).
- Files: `app/Otorisasi/PerintahReturBeliOtorisasi.php`, `config/otorisasi.php`, `PerintahReturBeliController.php`,
  `perintahreturbeli.blade.php` (`buttonMintaOtorisasi`, `buttonMintaBatalOtorisasi`, `PRB_HREF`).

---

## 10g. Invoice Retur Beli (`invoicereturbeli`, `KODEMENU` 030603) — single level, posts hutang + jurnal

`InvoiceReturBeliOtorisasi` (`DBRBELI`, `OL = 1`): the Pembelian ACC shape (§10e) — same posting procedures in the approver's
working period, same answers (both flows, header + items + total, `IsBatal` for batal, desktop rights enforced). Differences:

- **Server-side guards moved into the handler** (they only existed in the browser / controller): a locked period
  (`dblockperiode` for the approver's working period — the page JS asks `/ceklockperiode`) and a document without items
  (`DBRBELIDET`). Both are refused on the phone **and** now on the desktop endpoint too.
- **New generic mechanism:** a handler may throw `\DomainException('reason')` from `approve()` / `cancelApproval()`.
  `OtorisasiService::approve()` catches it: the transaction is rolled back, the request stays `P`, and the message is shown to
  the approver ("Periode sudah dikunci", "Retur … belum punya barang …"). The desktop controller maps it to `tolak(...)`.
- **Batal blocker:** already used in a pelunasan (`DBDPHDET.NoInvoice` / `NOFAKTUR`).
- **Controller** (`spOtorisasi`, `spBatalOtorisasi`): `canApprove` / `canCancel`, handler inside a DB transaction (as before),
  `closePending`. Replies unchanged (int > 0, `0` = already authorised, or `tolak`).
- **Blade** has a non-UTF-8 byte: edit with `sed`, not the Edit tool. `buttonMintaOtorisasi` / `buttonMintaBatalOtorisasi` / `IRB_HREF`.
- Files: `app/Otorisasi/InvoiceReturBeliOtorisasi.php`, `OtorisasiHandler.php` (doc), `OtorisasiService.php`
  (`approve` → try/catch around `prosesApprove`), `config/otorisasi.php`, `InvoiceReturBeliController.php`, `invoicereturbeli.blade.php`.

---

## 10h. Debet Note (`pembelianpermintaandebetnote`, `KODEMENU` 0307) — single level, posts hutang + jurnal

`DebetNoteOtorisasi` (`DBDEBETNOTE`, `OL = 1`): the Pembelian ACC shape (§10e). Same answers as there (both flows, header + items + total,
`IsBatal` for batal, desktop rights enforced). Specifics:

- **Mirrors the desktop exactly:** approve does *not* touch `MaxOL` and writes no `oto` log (the page never did); batal sets `maxol = -1`
  and logs `btloto`. Posting = `sp_ProsesPostingHutPiut` + `sp_ProsesPostingJurnalOto` with `TipeProses` 1 / 0 for `DBDEBETNOTE`, run after the
  flag is set (`vwPostHutPiut` only reads authorised DNs). The old private `postingDN()` moved into the handler.
- **Summary lines** come from `DBDEBETNOTEDET`: "Invoice <NoInv>", `NilaiRp`, and for foreign currency "USD 247,50 x kurs 18.000,00" (+ keterangan).
- **Batal blocker:** used in a pelunasan (`DBDPHDET.NoInvoice` / `NOFAKTUR`).
- **Controller:** `canApprove` first (reply `tolak`), flag + posting now inside one DB transaction (before they were separate), reply stays
  `{"success": bool}`. Batal keeps its existing 403 `No access` / 422 reasons, now through `canCancel`.
- Files: `app/Otorisasi/DebetNoteOtorisasi.php`, `config/otorisasi.php`, `PembelianPermintaanDebetNoteController.php`,
  `pembelianpermintaandebetnote.blade.php` (`buttonMintaOtorisasi`, `buttonMintaBatalOtorisasi`, `DN_HREF`).
- The phone batal path was verified on a DN authorised inside the test transaction (the data had no authorised DN); the "already used"
  blocker was not exercised for lack of data — it is the same code as Invoice Retur Beli (§10g).

---

## 10i. Penawaran SO (`penawaranso`, `KODEMENU` 04011) — single level, flag only

`PenawaranSOOtorisasi` (`DBPENAWARANSO`, `dbmenu.OL = 1`; SA and Ezra also carry stray `IsOtorisasi2` flags, harmless because the handler is not
a `LeveledOtorisasiHandler`). An older-style page: its controller had no header checks, no "already authorised" guard in the UPDATE, no
`PenjagaTransaksi` trait, and its JS ignored `{ok:0, pesan}` replies. Done with the settled defaults (both flows, header + items + total, `IsBatal`
for batal, desktop rights enforced). Specifics:

- **Mirrors the desktop:** `maxol` from `dbmenu.OL` on approve, `maxol = -1` on batal, logs `oto` / `btloto` with source `PNW` (wrapped in
  try/catch like the controller's old `catatLog`). **New:** the UPDATEs are guarded (`IsOtorisasi1 = 0` / `= 1`), so a double click now
  answers "sudah diotorisasi oleh X" instead of silently re-stamping the approver and date.
- **Summary:** customer, perihal (`KETERANGAN`), catatan (`VALIDITAS`), PIC, "Berlaku sampai" (`MasaBerlaku`); lines from `DBPENAWARANSODET`
  (`nmsat`, `HARGA`, `NNETRP`). `PEMBAYARAN` is a numeric code with no known mapping, so it is not shown.
- **No batal blocker (open decision):** the desktop never checked usage. A Penawaran is referenced by SO lines (`DBSODET.NOtawar` / `urutTawar`)
  and PO lines (`DBPODET.nopnw`). `PenawaranSOOtorisasi::cancelBlocker()` returns null on purpose; put a check there if cancelling an
  already-used penawaran should be refused.
- **Blade:** `buttonMintaOtorisasi` / `buttonMintaBatalOtorisasi` / `PSO_HREF`; the JS now handles `res.pesan` replies (it did not before) and
  batal is gated by `akses_isbatal`.
- Files: `app/Otorisasi/PenawaranSOOtorisasi.php`, `config/otorisasi.php`, `PenawaranSOController.php`, `penawaranso.blade.php`.

---

## 10j. SO (`so`, `KODEMENU` 04021) — single level, flag only, with blacklist + plafon rules

`SOOtorisasi` (`DBSO`, `dbmenu.OL = 1`, every SO has `MaxOL` 1 or -1, so it is **not** a `LeveledOtorisasiHandler`). Not the same shape as
Penawaran SO (§10i): the old endpoints applied two rules, which now live in the handler so the desktop and the phone share them.

- **Blacklist:** `DBCUSTSUPP.pblacklist = 1` refuses the approval ("Customer masuk dalam daftar blacklist"; old reply code 9).
- **Plafon:** `SUM(DBSODET.nnet)` above the approver's `DBJABATAN.PlafonOtoSO` refuses **both approve and batal** when the approver is level 1
  in the *old* `dbflmenu` table for `dbmenu.href = 'so'` (old reply code 2 / 0). Approvers at a higher level are not limited. The jabatan/level
  is read from the old tables exactly like the controller did (SA and Ezra are DEPT.HEAD, 5 miliar, and both count as level 1 because
  `IsOtorisasi1` is checked first, so the plafon applies to both). A user with no `DBFLPASS`/`DBJABATAN` row is refused with a message
  (the old code crashed with a 500).
- **How refusals work:** the handler throws `\DomainException`; on the phone the request stays `P` and the message is shown (§10g), on the
  desktop the controller maps it to `tolak(...)` = `{ok:0, pesan}`. The page JS now reads `res.pesan` (the old codes 0/9/2 are still handled).
- **Price-margin warnings:** the old `cekHargaOto` SQL moved to the static `SOOtorisasi::cekHarga()` (the controller endpoint `socekhargaoto`
  calls it, so the desktop confirm dialog is unchanged). On the phone, items outside the margin ("Margin Kurang dari Harga Minimal" /
  "Margin Lebih dari 100%") show as the read-only "Perhatian" card, as for PO (§10d). They never block Setujui.
- **Mirrors the desktop:** `maxol` from `dbmenu.OL` on approve, `maxol = -1` on batal, logs `oto` / `btloto` with source `SO`. UPDATEs are now
  guarded (`IsOtorisasi1 = 0` / `= 1`), so a double click answers "sudah diotorisasi oleh X".
- **No batal blocker (decision with the user):** like the desktop, cancelling an SO that already has a Surat Jalan (`dbSPBDet.NoSo`), Invoice
  (`dbInvoicePLDet.NoSO`), Uang Muka Jual (`DBUMJUAL.NOSO`), PO (`DBPO.NOSO`) or Perintah Retur Jual (`dbPRRJual.NOSO`) is allowed.
  `SOOtorisasi::cancelBlocker()` returns null on purpose; put the check there if that changes.
- **Summary:** customer, sales, PIC, No. Pesanan, keterangan, catatan, Pembayaran (`TIPEBAYAR` 0 CBD / 1 Kredit + `HARI` / 2 Termin, the
  codes of the page's Tipe Bayar filter), Tgl Kirim, currency; lines from `DBSODET` (qty `SATUAN` x `HARGA`, `NNETRp`; cancelled lines are marked).
- **Blade:** the page already has `SO_HREF = 'marketingso'` (used only for the header-save helper), so the otorisasi key is a separate
  `SO_OTO_HREF = 'so'`. `buttonMintaOtorisasi` / `buttonMintaBatalOtorisasi`; batal is gated by `akses_isbatal` (was `IsOtorisasi1`).
  "Open CBD" (`lockCBD`, `IsOtorisasi5`) and `updateCBD` are a different feature and were not touched.
- Rights on `04021`: SA and Ezra hold `IsOtorisasi1` + `IsBatal` (Ezra also a stray level 2, SA a level 5 for Open CBD); Noel has none.
- Files: `app/Otorisasi/SOOtorisasi.php`, `config/otorisasi.php`, `app/Http/Controllers/Marketing/SOController.php`,
  `resources/views/marketing/so.blade.php`.

---

## 10k. Performa (`performance`, `KODEMENU` 04024) — single level, a second flag on `DBSO`

`PerformanceOtorisasi extends SOOtorisasi` (`dbmenu.OL = 1`). "Oto Perf" is an extra authorisation on a **fully SO-authorised** SO that is CBD
(`TIPEBAYAR = 0`) or has an advance (`DP > 0`); the flag `DBSO.OtoPerf` (bit) is what lets a Uang Muka Jual be made. Same answers as the other
single-level pages (both flows, header + items + total, `IsBatal` for batal, desktop rights enforced). Specifics:

- **Mirrors the desktop:** approve = `OtoPerf = 1, TglOtoPerf = GetDate(), UserOtoPerf`; batal clears the three columns. The page never wrote to
  `dbLogFile` and never asked for a reason, so neither does the handler (the phone batal reason lives in `DBREQOTORISASI.ALASAN`).
- **New guards:** the UPDATEs only touch the matching state (`OtoPerf = 0` / `= 1`), so a double click answers "sudah diotorisasi oleh X". Approve is
  refused (`\DomainException`) when the SO is not fully authorised (`NeedOtorisasi = 1`, the same condition that hides it from the page's list).
- **Batal blocker:** a Uang Muka Jual already exists for the SO (`dbumjual.noso`) — the desktop's own 422 text, now in `cancelBlocker()`, checked when the
  request is created and again when it is approved; the desktop endpoint still answers `422` + that text (the page JS reads it).
- **Summary:** inherited from SO (customer, sales, PIC, pembayaran, items, total) plus "Uang Muka (DP)" and "Otorisasi SO oleh"; no margin warnings, and none
  of the SO blacklist/plafon rules (this page never used them). `SOOtorisasi::peringatanHarga` became `protected` so the subclass can switch it off.
- **Blade:** the gate sits inside `submitAdd` (row key and modal button) and `submitBatalOto` (modal button), so a user without the right can still open the
  detail first; `PERF_HREF = 'performance'` already was the menu key. The JS reads `res.pesan`.
- Rights on `04024`: SA and Ezra hold `IsOtorisasi1` + `IsBatal`; Noel has none.
- Files: `app/Otorisasi/PerformanceOtorisasi.php` (new), `SOOtorisasi.php` (one visibility change), `config/otorisasi.php`, `PerformanceController.php`,
  `performance.blade.php`.

---

## 10l. Uang Muka Jual (`uangmukajual`, `KODEMENU` 04025) — single level, posts a journal

`UangMukaJualOtorisasi` (`DBUMJUAL`, `dbmenu.OL = 1`): the Pembelian ACC shape (§10e). Same answers as the other single-level pages (both flows,
`IsBatal` for batal, desktop rights enforced); no new questions were needed. Specifics:

- **Shared table:** `DBUMJUAL` also holds Uang Muka Beli (`pBeli = 1`, ~5,700 rows vs 2 jual rows). Every handler query and UPDATE filters
  `isnull(pBeli,0) = 0`; the controller treats a Beli number as "not found" (reply 5).
- **Approve** = the page's UPDATE (`TGLOTO1, ISOTORISASI1, OTOUSER1`; **no** `MaxOL` change, as before) now guarded by `isnull(ISOTORISASI1,0) = 0`, then
  `LoggingData('oto','UMJ')`, then `sp_ProsesPostingHutPiut` + `sp_ProsesPostingJurnalOto` (flag 1) **in the approver's working period**. Batal is the mirror
  (`isnull(ISOTORISASI1,0) = 1`, `btloto`, flag 0). Both run inside one DB transaction (controller and phone service), so a posting failure undoes the flag.
- **What the posting writes:** journal rows in `DBJurnalOto` (`Jenis = 'UMJ'`, `NoBuktiTrans` = the UMJ number; `NoBukti` there is the journal's own number).
  The real posted UMJ has 2 rows; cancelling removes them, approving writes them again. The `vwPostJurnalOto` view yields nothing for a UMJ whose `NOJURNAL`
  is NULL (the one unauthorised jual document in the data), so that document cannot show posting in a test.
- **Batal blocker (already on the page, now shared):** a non-cancelled faktur has used the uang muka (`DBINVOICEPL.NoSPP/NUangMuka`, `DBJUAL.NobuktiUM`).
  `UangMukaJualOtorisasi::invoicePemakai()` is static and also used by the controller's koreksi/hapus guard, so the SQL exists once. The desktop keeps its reply
  `{status: 7, invoice}`; the phone request is refused when created and again when approved.
- **Desktop replies are unchanged** (they are codes the page JS reads): `1` ok, `3` already authorised / not authorised, `4` empty reason, `5` not found,
  `{status:7, invoice}`; a missing right answers `tolak(...)` = `{ok:0, pesan}`, which the JS now shows (it did not exist before).
- **Summary:** a UMJ has no item lines, so the approve page shows two lines (DPP uang muka with the percentage and the SO's DPP; PPN) in the document currency
  and the total in rupiah (`SUBTOTAL * KURS`), plus customer and SO number.
- **Not checked, as on the desktop:** a locked period (`dblockperiode`) does not stop the Otorisasi button (only add / edit / delete ask `/ceklockperiode`).
- **Operational note:** posting uses the *approver's* period (`dbperiode`), on the phone and on the desktop alike. Approving a UMJ from a month other than the
  document's month writes no journal rows. Check the approver's working period when journals look missing.
- Rights on `04025`: SA and Ezra hold `IsOtorisasi1` + `IsBatal`; Noel has none.
- Files: `app/Otorisasi/UangMukaJualOtorisasi.php` (new), `config/otorisasi.php`, `UangMukaJualController.php`, `uangmukajual.blade.php`.

---

## 10m. Surat Jalan (`suratjalan`, `KODEMENU` 04031) — single level, flag only, batal VOIDS the SPB

`SuratJalanOtorisasi` (`dbSPB`, `dbmenu.OL = 1`). `dbSPB` has no header trigger and the page's endpoints never called a posting procedure, so
authorising is a plain flag (stock is handled by the `dbSPBDet` triggers when the document is saved). Same answers as the other single-level
pages (both flows, header + items, `IsBatal` for batal, desktop rights enforced). Specifics:

- **"Batal Otorisasi" voids the SPB.** The old UPDATE sets `IsBatal = 1, UserBatal, TglBatal` *and* clears the authorisation (`IsOtorisasi1 = 0, MaxOL = -1`);
  authorising again clears the void (`IsBatal/UserBatal/TglBatal = NULL`). The handler does exactly that, so approving a batal request on the phone voids the
  document. The approve page shows "Status: Dibatalkan oleh X" for a voided SPB.
- **No batal blocker (decision with the user):** like the desktop, an SPB already used by an Invoice Penjualan (`dbInvoicePLDet.NoSPB`), a Retur Surat Jalan
  (`DBRSPB` / `DBRSPBDet.NoSPB`) or a Retur Invoice (`DBRInvoicePL.NOSPB`) can still be voided. `SuratJalanOtorisasi::cancelBlocker()` returns null on purpose.
- **Shared table:** `dbSPB` also holds POS/kasir documents; the page's list excludes `NoBukti like '%POS%'`, so every handler query and UPDATE does too
  and the controller answers "tidak ditemukan" for such a number.
- **Mirrors the desktop:** no `dbLogFile` entry and no reason column (the page never had either); the phone batal reason lives in `DBREQOTORISASI.ALASAN`. New: the UPDATEs are
  guarded (`IsOtorisasi1 = 0` / `= 1`), so a double click answers "sudah diotorisasi oleh X" instead of re-stamping the approver/date.
- **Summary:** customer, tipe (`IsFlag`: 0 SPB Barang Jadi, 1 Bahan Baku dan Lain-lain), SO number(s), gudang, ekspedisi, polisi, sopir, resi, container/seal, catatan;
  lines from `dbSPBDet` (qty in the line's `NOSAT` unit, gross weight, gudang). No money value, so `total => null`.
- **Blade:** `SJ_OTO_HREF = 'suratjalan'`, `buttonMintaOtorisasi` / `buttonMintaBatalOtorisasi`; the row buttons (`buttonOtorisasiSPB`, `buttonBatalOtorisasiSPB`) gate on
  `akses_isotorisasi1` / `akses_isbatal` (batal used IsOtorisasi1 before). The JS used to show "Berhasil" whatever the server answered; it now shows `res.pesan` refusals.
- Rights on `04031`: SA and Ezra hold `IsOtorisasi1` + `IsBatal`; Noel has none.
- Files: `app/Otorisasi/SuratJalanOtorisasi.php` (new), `config/otorisasi.php`, `SuratJalanController.php`, `suratjalan.blade.php`.

---

## 10n. Retur Surat Jalan (`retursuratjalan`, `KODEMENU` 04032) — single level, flag only, active-period rule

`ReturSuratJalanOtorisasi` (`dbRSPB`, `dbmenu.OL = 1`, no triggers, no posting). An already well-guarded page (readable string replies, `oto` / `btloto` log with source `RSJ`),
so only the rights check, the UPDATE guard and the phone flow were missing. Done with the settled answers (both flows, header + items without a total, `IsBatal` for batal,
desktop rights enforced); no new questions were needed. Specifics:

- **Active-period rule (from the page):** approve and batal only work for a retur dated in the logged-in user's working month (`dbperiode`). The handler enforces it by throwing
  `\DomainException('Retur SJ … di luar periode aktif Anda (MM/YYYY), tidak bisa …')`, so on the phone the request stays `P` and the approver is told which period to switch to.
  **Practical effect:** SA and Ezra are in 11/2026, so they cannot approve a September retur from their phone until their working period matches it — exactly as on the desktop.
- **Mirrors the desktop:** `MaxOL = 1` on approve, `maxol = -1` on batal, `LoggingData('oto' | 'btloto', 'RSJ')` (the batal reason goes to the log; on the phone it comes from the request).
  New: the UPDATEs are guarded (`IsOtorisasi1 = 0` / `= 1`), so a double click answers "sudah diotorisasi oleh X".
- **No batal blocker (open, as on SO / Surat Jalan):** the page never checked whether a retur is used further down (`DBUMJUAL.NoRetur`, `DBHUTPIUT.NoRetur`, `DBPERSEDIAANJALAN.NoRetur`).
  `cancelBlocker()` returns null on purpose.
- **Replies stay plain strings** (`1` = ok; anything else is shown by the JS with `alertify.warning(res)`), so this controller does not use the `PenjagaTransaksi` JSON `tolak()`.
  The flag + log run inside one DB transaction.
- **Summary:** customer, tipe (`IsFlag`), the original Surat Jalan with its date, gudang, polisi, container/seal, catatan; lines from `dbRSPBDet` (qty in the line's `NOSAT` unit,
  gross weight). No money value, so `total => null`.
- **Blade:** uses the page's existing `RSJ_HREF`; `buttonOto` (opens the detail first) and `buttonBatalOto` gate on `akses_isotorisasi1` / `akses_isbatal` (batal used IsOtorisasi1 before).
- Rights on `04032`: SA and Ezra hold `IsOtorisasi1` + `IsBatal`; Noel has none.
- Files: `app/Otorisasi/ReturSuratJalanOtorisasi.php` (new), `config/otorisasi.php`, `ReturSuratJalanController.php`, `retursuratjalan.blade.php`.

---

## 10o. Invoice Penjualan (`invoicepenjualan`, `KODEMENU` 04041) — single level, posts piutang + jurnal, batal VOIDS the invoice

`InvoicePenjualanOtorisasi` (`dbInvoicePL`, `dbmenu.OL = 1`): the Pembelian ACC shape (§10e). Same answers as the other single-level pages (both flows, header + items + total, `IsBatal` for batal,
desktop rights enforced). Specifics:

- **Shared table:** `dbInvoicePL` also holds Invoice Jasa (`pJasa = 1`, menu `invoicejasa`) and kasir/POS invoices (`FlagKasir = 1`). The page's list excludes both, so every handler query and UPDATE does
  too (`isnull(FlagKasir,0)=0 and isnull(pJasa,0)=0`); other numbers answer "sudah diotorisasi atau tidak ditemukan". When Invoice Jasa is converted, give it its own handler.
- **Approve** = the page's UPDATE (`IsOtorisasi1, OtoUser1, TglOto1, MaxOL = 1`, clears `IsBatal/UserBatal/TglBatal`) guarded by `isnull(IsOtorisasi1,0) = 0`, then `LoggingData('oto','INVC')`, then
  `sp_ProsesPostingHutPiut` + `sp_ProsesPostingJurnalOto` (flag 1) **in the approver's working period**. The page JS already warned that the flag could be saved while the posting failed; both endpoints
  now run in one DB transaction, so a posting failure undoes the flag.
- **"Batal Otorisasi" VOIDS the invoice** (as on Surat Jalan, §10m): `IsBatal = 1, UserBatal, TglBatal, IsOtorisasi1 = 0, MaxOL = -1`, `btloto` log with the reason, and the posting reversed (flag 0).
  Approving a batal request on the phone therefore voids the invoice and removes its piutang and journal; authorising again un-voids it.
- **No batal blocker (decision with the user):** like the desktop, an invoice already used by a pelunasan (`DBDPHDET.NoInvoice/NOFAKTUR`), a Nota Retur Penjualan (`DBINVOICERPJ.NoInvoice`), a Retur Invoice
  (`DBRInvoicePL.NoInvoice`) or a Kredit/Debet Note (`DBKreditNoteDET` / `DBDebetNoteDET.NoInv`) can still be voided. `InvoicePenjualanOtorisasi::cancelBlocker()` returns null on purpose.
- **What the posting writes** (verified on a real authorised invoice, rolled back): journal rows in `DBJurnalOto` (`Jenis = 'INVC'`, `NoBuktiTrans`) and a piutang row in `DBHUTPIUT` (`NoFaktur`); cancelling removes both,
  approving writes them again. As with Uang Muka Jual (§10l), posting follows the *approver's* period (`dbperiode`), on the phone and on the desktop alike.
- **Desktop replies are unchanged:** `1` ok, `{status: 0, pesan}` otherwise (the JS already read this shape); the new rights refusal uses the same shape.
- **Summary:** customer, SO and Surat Jalan numbers (from the lines), pembayaran (`TipeBayar` 0 CBD / 1 Kredit + `HARI` / 2 Termin), no. pajak, currency, DPP, PPN, uang muka; lines from
  `dbInvoicePLDet` (qty x harga, `NNetRp` incl. PPN); total = `vwRpDetInvoicePL.TotNetRp` (equals the sum of the lines).
- **Blade:** uses the page's `IP_HREF`; the gates sit in `buttonOtorisasi` (opens the detail), `submitOtorisasi` (also the detail's button) and `buttonBatalOtorisasi` (`akses_isbatal`, was IsOtorisasi1).
- Rights on `04041`: SA and Ezra hold `IsOtorisasi1` + `IsBatal`; Noel has none.
- Files: `app/Otorisasi/InvoicePenjualanOtorisasi.php` (new), `config/otorisasi.php`, `InvoicePenjualanController.php`, `invoicepenjualan.blade.php`.

---

## 10p. Perintah Retur Jual (`perintahreturjual`, `KODEMENU` 04051) — single level, flag only, shared table with the (-) page

`PerintahReturJualOtorisasi` (`dbPRRJual`, `dbmenu.OL = 1`, no triggers, no posting). The page was already hardened (refusals as `{status: 0, pesan}`, which its JS shows via `prjDitolak`, `oto` /
`btloto` log with source `PRJ`, and a batal blocker), so only the rights check and the phone flow were missing. Settled answers (both flows, header + items without a total, `IsBatal` for batal,
desktop rights enforced); no new questions were needed. Specifics:

- **Shared table, template for the next page:** Perintah Retur Jual (`pMIN = 0`) and Perintah Retur Jual (-) (`pMIN = 1`, href `perintahreturjualminus`) live in `dbPRRJual`. Every query and UPDATE filters
  `isnull(pMIN,0) = $pMin`, and the controller answers "tidak ditemukan" for a number of the other page. **The (-) page is then a one-line subclass:** `class PerintahReturJualMinusOtorisasi extends
  PerintahReturJualOtorisasi { protected int $pMin = 1; }` plus its own config entry and controller/blade edits (its controller already has the same guarded UPDATEs).
- **Batal blocker (already on the page, now shared):** the PRJ is used by a Retur Penjualan Gudang (`dbSPBRJualDet.NoPRRJUAL`). `PerintahReturJualOtorisasi::dipakaiGudang()` is static and also feeds
  `getDetail` (`IsProses`) and `cancelBlocker()`, so the SQL exists once. The desktop keeps its message; a phone request is refused when created and again when approved.
- **Mirrors the desktop:** `maxol = 1` on approve (guard `isnull(isOtorisasi1,0) <> 1`), `maxol = -1` on batal (guard `isOtorisasi1 = 1`), logs `oto` / `btloto`. Replies are unchanged: the number of rows on success,
  `{status: 0, pesan}` otherwise (the new rights refusal uses the same shape).
- **Summary:** customer, the invoice(s) being returned (from the lines, else `NoRPJ`), SO, gudang, catatan; lines from `dbPRRJualDet` (qty in the line's `NOSAT` unit, invoice number, `KETDET`). No money value, so `total => null`.
- **Test data limits:** only 4 unauthorised PRJ exist (3 are old ones already used by a gudang retur) and no authorised PRJ is unused, so the happy path was tested on one document inside the rolled-back transaction.
- **Blade:** uses the page's `PRJ_HREF`; `buttonOtorisasi` and `buttonBatalOtorisasi` gate on `akses_isotorisasi1` / `akses_isbatal` (batal used IsOtorisasi1 before).
- Rights on `04051`: SA and Ezra hold `IsOtorisasi1` + `IsBatal`; Noel has none.
- Files: `app/Otorisasi/PerintahReturJualOtorisasi.php` (new), `config/otorisasi.php`, `PerintahReturJualController.php`, `perintahreturjual.blade.php`.

---

## 10q. Perintah Retur Jual (-) (`perintahreturjualminus`, `KODEMENU` 041051) — one-line subclass of §10p

Done exactly as announced in §10p: `PerintahReturJualMinusOtorisasi extends PerintahReturJualOtorisasi { protected int $pMin = 1; }`, config entry `perintahreturjualminus` ("Perintah Retur Jual (-)"). Nothing else is new in the
handler: summary, approve, batal blocker (gudang retur) and logs (`oto` / `btloto`, source `PRJ`) are inherited. No new questions were needed.

- **Summary:** (-) documents have no invoice or SO (stored as `-`), so those header rows are skipped by the existing `'-'` checks; the card shows customer, gudang, catatan and the item lines.
- **Controller:** `PerintahReturJualMinusController::spOtorisasi` / `spBatalOtorisasi` now check `canApprove` / `canCancel` (refusal `{status: 0, pesan}`, same shape the page already used), call the handler and
  `closePending`. `headerPRJ` already filtered `pmin = 1`, so a plain-PRJ number answers "tidak ditemukan" here, and the plain page answers the same for a (-) number (verified both ways).
  `sudahDiprosesGudang` delegates to `PerintahReturJualOtorisasi::dipakaiGudang()`. The `pket` argument the old `oto` log passed (always empty) is gone.
- **Blade:** same two dialogs as §10p with `PRJ_HREF = 'perintahreturjualminus'`; `buttonOtorisasi` / `buttonBatalOtorisasi` gate on `akses_isotorisasi1` / `akses_isbatal` (batal used IsOtorisasi1 before).
- **Test data:** one usable unauthorised (-) PRJ (`SML/PRJ/00001/0916`) and one authorised-but-used (`SML/PRJ/00001/0416`, for the blocker); no authorised unused (-) PRJ, so the plain batal path was covered by the phone flow.
- Files: `app/Otorisasi/PerintahReturJualMinusOtorisasi.php` (new), `config/otorisasi.php`, `PerintahReturJualMinusController.php`, `perintahreturjualminus.blade.php`.

---

## 10r. Retur Penjualan Gudang (`returpenjualangudang`, `KODEMENU` 04053) — single level, flag only (SPR)

`ReturPenjualanGudangOtorisasi` on `dbSPBRJual` (documents `SML/SPR/...`; no triggers, no posting; `dbmenu` has no row for 04053, so OL 1 is assumed from the code, which only uses `IsOtorisasi1`). The page was already
hardened (refusals as `{status: 0, pesan}` read by `rpgDitolak`, `oto` / `btloto` log with source `RPJ`, a batal blocker), so only the rights check and the phone flow were missing. Settled answers apply; no new questions.

- **What approval does:** sets `IsOtorisasi1 = 1`, `MaxOL = 1` (batal: `0` / `-1`), `OtoUser1`, `TglOto1`. Nota Retur Penjualan only pulls SPRs that are authorised (`vwBrowsOutSPBRJual`), which is why the blocker below exists.
- **Batal blocker (already on the page, now shared):** the SPR was pulled into a Nota Retur Penjualan (`DBINVOICERPJDet.NOSPR`). `ReturPenjualanGudangOtorisasi::dipakaiNotaRetur()` is static and feeds both the controller's
  `sudahDipakaiNotaRetur()` and `cancelBlocker()`; a phone request is refused when created and again when approved.
- **Table filter:** every handler query/UPDATE adds `NoBukti like '%SPR%'` (the same filter the page's list uses). Today every row of `dbSPBRJual` is an SPR; the filter only protects against other document types appearing later.
- **Summary:** customer, PRJ number(s) and invoice(s) from the lines (fallback `NoRPJ`), SO, gudang, vehicle, driver, catatan; lines from `dbSPBRJualDet` (qty in the line's `NOSAT` unit, invoice, reject qty). `HPP` is cost data and is not shown; `total => null`.
- **Mirrors the desktop:** guard `isnull(isOtorisasi1,0) <> 1` on approve, `isOtorisasi1 = 1` on batal, same logs. Replies unchanged (rows on success, `{status: 0, pesan}` otherwise, including the new rights refusal).
- **Blade:** uses the page's `RPG_HREF`; `buttonOtorisasiPenerimaan` / `buttonBatalOtorisasiPenerimaan` gate on `akses_isotorisasi1` / `akses_isbatal` (batal used IsOtorisasi1 before).
- **Test data:** the two oldest unauthorised SPRs (2017, 2025) are already referenced by a Nota Retur row, so the test deleted those references inside the rolled-back transaction to exercise the unblocked batal path; the three authorised SPRs
  are all used by a Nota Retur (blocker verified on `SML/SPR/00002/1026`). Verified afterwards that the references were back.
- Rights on `04053`: SA holds `IsOtorisasi1` + `IsBatal`; Ezra the same (plus a stray level 2); Noel has none.
- Files: `app/Otorisasi/ReturPenjualanGudangOtorisasi.php` (new), `config/otorisasi.php`, `ReturPenjualanGudangController.php`, `returpenjualangudang.blade.php`.

---

## 10s. Nota Retur Penjualan (`notareturpenjualan`, `KODEMENU` 04055) — single level, posts piutang + jurnal, batal VOIDS the nota

`NotaReturPenjualanOtorisasi` on `DBINVOICERPJ` / `DBINVOICERPJDet` (documents `…/IVRJ/…`; only this page uses the table, so no extra filter; no triggers; `dbmenu` has no row for 04055, one level assumed from the code).
Same shape as Invoice Penjualan (§10o): the page already had `{status: 0, pesan}` refusals (`nrpDitolak`), `oto` / `btloto` logs with source `IVRJ` and posting; only the rights check and the phone flow were missing. Settled answers
(same-as-desktop, no new blocker) applied; no new questions.

- **Posting:** `sp_ProsesPostingHutPiut` + `sp_ProsesPostingJurnalOto` with `('', 'DBINVOICERPJ', bulan, tahun, nobukti, 1|0)` in the **approver's working period**, as on the desktop. Rows land in `DBJurnalOto` (`Jenis = 'RPJ'`,
  `NoBuktiTrans` = the nota) and `DBHUTPIUT` (`NoFaktur`; one baseline row per nota exists before approval, so approval takes it 1 → 2 and batal back to 1). A nota outside the approver's month posts nothing, so
  approve on the phone with the period set to the nota's month. Handler and controller run inside `DB::connection('SML')->transaction`, so a failed posting undoes the flag.
- **Batal VOIDS:** `IsBatal = 1`, `UserBatal`, `TglBatal`, `IsOtorisasi1 = 0`, `MaxOL = -1`, posting reversed; approving again clears `IsBatal` (the phone summary shows "Status: Dibatalkan oleh …" for a voided nota). `cancelBlocker()` is empty because the
  page checks no usage (e.g. a pelunasan on the nota) - that is where a blocker would go.
- **"Belum punya item":** the old pre-check moved into `approve()` as a `\DomainException` (service rolls back, keeps the request `P`, shows the message on the phone); the controller keeps the same refusal text.
- **Summary:** customer, invoice asal, retur gudang (SPR) from the lines (fallback `NORPJ`), pembayaran (CBD / Kredit n hari / Termin), no. pajak, mata uang, DPP, PPN, catatan; lines show `Rp NNETRp` with `qty x harga` (qty in the line's `Nosat` unit: 1 = `Qnt` / `SAT_1`, otherwise `Qnt2` / `SAT_2`, as the print query does); total = sum of `NNETRp`.
- **Mirrors the desktop:** guard `isnull(IsOtorisasi1,0) <> 1` on approve, `IsOtorisasi1 = 1` on batal. Replies unchanged: `1` on success, `{status: 0, pesan}` otherwise (the rights refusals use the same shape).
- **Blade:** uses the page's `NRP_HREF`; `buttonOtorisasi` / `buttonBatalOtorisasi` gate on `akses_isotorisasi1` / `akses_isbatal` (batal used IsOtorisasi1 before).
- **Test data:** the oldest nota (2015) and one 2025 nota are unauthorised (the 2025 one is already voided); the test set SA/Ezra/Noel's working period to the nota's month inside the transaction and proved posting and reversal on a real posted nota (`SML/IVRJ/00001/1025`: journal rows 2 → 0 → 2). A nota without items was made in-transaction by deleting its detail rows.
- Rights on `04055`: SA holds `IsOtorisasi1` + `IsBatal`; Ezra the same (plus a stray level 2); Noel has none.
- Files: `app/Otorisasi/NotaReturPenjualanOtorisasi.php` (new), `config/otorisasi.php`, `NotaReturPenjualanController.php`, `notareturpenjualan.blade.php`.

---

## 11. Troubleshooting — "no notification" / "queue is empty"

Work through these in order; the first four are data checks you can run read-only (§7).

| Symptom | Cause | Check / fix |
|---|---|---|
| Approver's **"Permintaan saya"** is empty | Wrong tab. It lists requests *that user made*. The approval queue is **"Menunggu saya"**. | Open `/otorisasiinbox` → "Menunggu saya". |
| Dialog said sent, but **"Menunggu saya" is empty and no push** | The menu has **no approver**: nobody has `DBFLMENUWEB.IsOtorisasi1 = 1` (+ `HASACCESS = 1`) for it. Both the push list and the queue are built from that right. | `select USERID, IsOtorisasi1 from DBFLMENUWEB where L1 = '<KODEMENU>' and HASACCESS = 1`. Grant the right. Since this was found the dialog now refuses with "Belum ada user yang berhak otorisasi" and saves nothing. Rows saved *before* that fix stay `P` (they show in "Menunggu saya" once someone is granted the right, but no push was ever sent for them). |
| Approver is in the queue but **gets no push** | That user has **no subscribed device**: `select * from DBPUSHSUBSCRIPTION where USERID = '<user>'` is empty. Subscriptions are per user *and* per browser/phone. | On that phone, logged in as that user, open the app and tap the crossed-out bell (iPhone: Add to Home Screen first). The dialog now says when no approver has a device. |
| Subscribed, server says sent, **phone shows nothing** | Phone side: site notifications blocked/denied, Focus/Do Not Disturb, Android battery saver/“unrestricted” off for Chrome, iOS app not opened from the Home Screen icon. | Send a test push (script below) and watch the phone. |
| Server errors or no send at all | VPS side. `.env` (gitignored) must have `VAPID_PUBLIC_KEY/PRIVATE_KEY/SUBJECT` **identical to the keys the devices subscribed with** — different keys ⇒ every push is rejected (403). Also: `composer install` run (the `minishlink/web-push` package), PHP extensions `openssl`, `mbstring`, `gmp` or `bcmath`, config cache stale (`php artisan config:clear`), outbound HTTPS from the VPS allowed. | `grep VAPID .env`; `tail storage/logs/laravel.log` for `SendWebPush gagal`; `php -m \| grep -Ei 'gmp\|bcmath\|openssl\|mbstring'`. |
| `QUEUE_CONNECTION=database` but nothing is sent | Needs a `jobs` table **on the default DB connection** and a running `queue:work`; after every deploy run `php artisan queue:restart` (workers keep old code). With `QUEUE_CONNECTION=sync` neither is needed. | `grep QUEUE_CONNECTION .env`. |

**Test push to one user** (read-only on data; sends a real labelled notification to that user's devices and
prints each push service's answer — `201` = accepted, `404/410` = subscription dead, `403` = VAPID key mismatch).
On Windows set `OPENSSL_CONF` in the shell first (§6). Build it from `app/Jobs/SendWebPush.php`: queue a
`Minishlink\WebPush\Subscription` per row of `DBPUSHSUBSCRIPTION` and loop over `$webPush->flush()`,
printing `$report->isSuccess()`, `getResponse()->getStatusCode()` and `getReason()`.

If the test prints `201` for every device locally but the real flow fails on the VPS, the difference is
the VPS environment (the table above), not the code or the subscriptions.

---

## 12. Prompt template for a new session

```
Add "Minta Otorisasi" (push to approvers) to the <Page name> page, following
docs/mobile-otorisasi-guide.md. Reference implementation: Kas.

Page: resources/views/<...>.blade.php, controller <...>Controller, JS public/js/<...>.js
Menu href: <href>

Before changing code: do §3 of the guide, check §8 for this page's UPDATE shape, read the
page's own spOtorisasi, and ask me anything you can't determine (especially what the mobile
summary should show and whether the approve view needs to be generic). Don't touch the shared
infrastructure files. When done, list every changed file and how to test it.
```
