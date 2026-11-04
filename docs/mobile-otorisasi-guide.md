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
Bell badge in header + /otorisasiinbox ("Perlu diproses" and "Permintaan saya")
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
  §10d). Everything else is still single-level `IsOtorisasi1`. `dbso`/`dbpenawaranso` also compute `maxol` and have
  NOT been converted — each has its own level/plafon rules, so ask the user before copying the PO approach.
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
- **Shared endpoint:** `poupdateotorisasi` is also used by the PO Non Stock page, so `approveWithRules()` is NOT filtered by
  `pJasa`; `summary()/isApproved()` are (`pJasa = 0`), which keeps the push flow stock-only. The desktop controller
  checks the right on the PO's own menu (`purchaseorder` or `pononstock`, level 1 **or** 2) and maps the handler result
  back to the legacy replies the page JS reads (`1`, `2`, `3`, or `tolak`).
- A level-1 user cannot send a request (they have `IsOtorisasi1`, so they are told to authorise directly). A PO left at
  level 1 only that needs a director has no automatic follow-up request unless one already exists.
- **Not done: "Minta Batal Otorisasi" for PO** (batal branches by plafon, and may also void the PO with `isbatal = 1`).

Files: `app/Otorisasi/LeveledOtorisasiHandler.php`, `PurchaseOrderOtorisasi.php`, `config/otorisasi.php`,
`app/Services/OtorisasiService.php` (`requiredLevel`, level-aware `canAct/approvers`, `approveBertingkat`, queue SQL),
`OtorisasiRequestController::show`, `approve.blade.php`, `POController.php`, `purchaseOrder.blade.php`.

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
