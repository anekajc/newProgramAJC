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

> **Gotcha — the approve view is Kas-shaped.** `approve.blade.php` renders each line with the
> keys `Jumlah` (bold), `Perkiraan`, `Lawan`, `Keterangan`, and the total with an `Rp` prefix.
> A page whose lines look different (items/qty/price, non-Rupiah total) needs the view made
> generic first — **ask the user** before changing a shared view. `header` is already generic.

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
already pending, `422` unsupported menu / requester can approve / already approved, `404` not found.
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
- **Multi-level otorisasi (`IsOtorisasi2..5`) is NOT supported.** `LEVELOTO` is always 1,
  `canApprove`/`approvers` only look at `IsOtorisasi1`. The user is discussing this with their
  boss — **ask before implementing**. `dbso`/`dbpenawaranso` already compute `maxol`.
- **Kas handler has no `NoBukti` prefix filter** (nothing prevents `menu=kas` with a Bank number).
  Bank's handler has one (§10); Kas still needs the BKK/BKM equivalent — a small change plus a retest.
- **Requester's own device:** approvers who are the requester are excluded from the push
  (`array_diff`), but a user with the right never sees the request dialog anyway.
- **Batal otorisasi has no push flow** — only approval requests are supported.
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

## 11. Prompt template for a new session

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
