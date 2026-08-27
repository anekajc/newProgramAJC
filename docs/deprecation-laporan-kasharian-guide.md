# Deprecation Fix — `LaporanAccountingKasHarianController`

Change notes for `app/Http/Controllers/Report/LaporanAccountingKasHarianController.php`
(page: **Laporan Accounting → Kas Harian**, view `resources/views/report/reportaccountingkasharian.blade.php`).

This controller still used APIs from the old pre-Laravel-6 codebase. The app now runs on
**Laravel 13 / PHP 8.3** (`composer.json: "laravel/framework": "^13.8"`), so several of those calls
were not merely "deprecated" — they were **hard fatal errors**.

---

## 1. Summary

| # | Problem | Status on Laravel 13 | Action taken |
|---|---------|----------------------|--------------|
| 1 | `array_add()` | **Removed** in Laravel 6.0 (deprecated in 5.8) → `Error: Call to undefined function` | Enclosing method deleted |
| 2 | `DB::connection('MGL')` | The `MGL` connection **does not exist** in `config/database.php`, `.env`, or `.env.example` → `InvalidArgumentException` | Enclosing method deleted |
| 3 | `VwREPORTHISPO` | The view **does not exist in any database** on the server | Enclosing method deleted |
| 4 | `auth()->user()->username` with no null check | `Attempt to read property on null` for a guest | Guard added → `401` |
| 5 | `$req->get(...)` / `$req->date1` | Works, but not the idiomatic Laravel accessor for a GET route | Replaced with `$req->query(...)` |
| 6 | Commented-out code blocks | Noise | Removed |

---

## 2. Changes per method

### `index()` — **unchanged**

The `if ($akses['userLoggedOut'])` guard is in fact **dead code**: that flag is only ever set to `true`
inside a commented-out block in `app/Traits/AksesTrait.php`. It was left alone because the same pattern
appears in 100+ other controllers — changing it here alone would create an inconsistency for no gain.

### `doReport()`

```php
- $TglAw = $req->get('date1');
+ $TglAw = $req->query('date1');
```

Same for `date2`, `inputPerkiraan`, and `detOrRekap`. The route is `Route::get`, so `query()` is exactly
equivalent in behaviour. The commented-out `Sp_LapSaldoAwal` block that used to sit here was removed —
the live version already exists in `doReportSaldoAwal()`.

The stored-procedure call was **not** changed. Verified directly against the database; the argument
order is correct:

```
sp_LapKasHarian  @Perkiraan, @TglAw, @TglAk, @Divisi, @IdUser, @Tipe, @Valas
```

`$Divisi = '01'`, `$IDuser = ''`, and `$TipeTrans = ''` remain hardcoded exactly as before.

### `doReportSaldoAwal()`

Same treatment: `->get()` → `->query()`, plus removal of the trailing `// return $res2;`. The
`sp_LapSaldoAwal` procedure was verified to exist.

### `doFilter()` & `doReportFilter()` — **DELETED**

Along with their routes in `routes/report.php`:

```php
- Route::get('/reportaccountingkasharian_doFilter', [...'doFilter']);
- Route::get('/reportaccountingkasharian_doReportFilter', [...'doReportFilter']);
```

Three independent reasons, all true at once:

1. **Never called.** `reportaccountingkasharian.blade.php` only ever hits `_doReport`, `_saldoawal`,
   and `_loadperkiraan`.
2. **Unrelated to Kas Harian.** Both queried `VwREPORTHISPO` — a *Purchase Order* history view. This was
   copy-paste residue from a marketing report, not part of the daily-cash report.
3. **The query target does not exist.** Checked against server `36.88.190.218`: `VwREPORTHISPO` is
   **absent** from `DBSMLNEW`, `DBMGL2`, `DBSML`, `DBSML2`, and `DBETM` — neither as a table nor as a view.

Repairing them (`MGL` → `SML`) was considered and rejected: it only swaps the error
`Database connection [MGL] not configured` for `Invalid object name 'VwREPORTHISPO'`. There is no target
that makes these endpoints work.

> **If a PO filter is ever needed on another page:** what actually exists in `DBSMLNEW` is
> **`VWREPORTHISSO`** (Sales Order), carrying the columns `NOBUKTI`, `TANGGAL`, `KODEBRG`, `NAMABRG` —
> precisely the four columns `doFilter` selected. So the original code was most likely copied from an SO
> report, with `HISSO` renamed to `HISPO` pointing at a view that was never created.

### `loadPerkiraan()`

```php
- $userid = auth()->user()->username;
+ $user = auth()->user();
+ if (! $user) { return response()->json([], 401); }
+ $userid = $user->username;
```

This route has **no** `->middleware('auth')`, so a guest can reach it and trigger
`Attempt to read property on null`. It returns `401` + `[]` because this is an AJAX endpoint (`$.ajax`
from the Blade view), not a page load — an HTML redirect would be swallowed as garbage JSON on the
client side.

The tables `DBPOSTHUTPIUT`, `DBPERKIRAAN`, and `DBAKSESPERKIRAANR` were all verified to exist.

---

## 3. Note on the `MGL` connection

`MGL` is a legacy connection name that is **never defined** anywhere in this project, yet is still
referenced by **119 files**. The server does host a database called `DBMGL2` — most likely its original
target — but `VwREPORTHISPO` is not there either, so registering an `MGL` connection would still not
have solved anything for this controller.

If `MGL` genuinely needs to be registered one day: **do not** follow the `SML` block's pattern in
`config/database.php`, which puts the host and password in as `env()` defaults (credentials hardcoded
into the repo). Use `env()` without credential defaults.

---

## 4. Why `Arr::add()` was NOT used

For anyone fixing `array_add()` in the other controllers: **do not** do a 1:1 swap to `Arr::add()`.

```php
// the old pattern, present in ~102 files
$res = array_add($res, $i+$j, $row[$j]);
```

`Arr::add()` only writes when the key is **absent**, while the `$i+$j` index collides across iterations
(i=0,j=1 and i=1,j=0 both produce key 1) — rows would be silently dropped. The correct form is:

```php
$res[] = $row[$j];
```

`Arr::add()` itself remains appropriate for genuine keyed-map cases, such as in `app/Traits/AksesTrait.php`.

---

## 5. Verification

```bash
# no removed APIs remain
grep -n "array_add\|connection('MGL')" app/Http/Controllers/Report/LaporanAccountingKasHarianController.php

# lint with PHP 8.3 (Laragon's default on PATH is PHP 7.3 — wrong engine)
/c/laragon/bin/php/php-8.3.26-Win32-vs16-x64/php.exe -l app/Http/Controllers/Report/LaporanAccountingKasHarianController.php
/c/laragon/bin/php/php-8.3.26-Win32-vs16-x64/php.exe -l routes/report.php
```

Browser smoke test — page behaviour must be **identical** to before:

- Open `/reportaccountingkasharian` → the Perkiraan dropdown populates (`_loadperkiraan`).
- Pick a date range + Perkiraan and run the report → the table renders and the saldo footer fills in
  (`_doReport` → `res1`, `_saldoawal` → `res2`).
- Toggle Tampilan **Rp ↔ Valas** → the column set switches (detail ↔ rekap).
- Log out, then hit `/reportaccountingkasharian_loadperkiraan` directly → `401` + `[]`, not a 500.

> **Note on Pint.** `vendor/bin/pint --test` reports `fail` on this file — but it fails with the same
> fixer list on untouched files such as `LaporanAccountingBankHarianController.php`. This codebase does
> not follow the Pint preset anywhere (2-space indent, same-line braces). Pre-existing, not a regression;
> the surrounding style was matched deliberately rather than reformatted.

---

## 6. Outstanding work (not done here)

- **Repo-wide sweep.** `array_add()` appears in **102 files** and `connection('MGL')` in **119 files**,
  almost all under `app/Http/Controllers/Report/`. Around 30 are already commented out (the
  Hutang/Piutang controllers); the rest are live and will fatal the moment their route is hit. The same
  `doFilter`/`doReportFilter` + `VwREPORTHISPO` pattern exists in ~101 other controllers — candidates for
  deletion on the same grounds as §2.
- **`auth` middleware.** Adding `->middleware('auth')` to the Kas Harian routes is **not yet safe**:
  there is no route named `login` in `routes/`, and `bootstrap/app.php` does not set `redirectGuestsTo`.
  A guest would get `RouteNotFoundException: Route [login] not defined` rather than a redirect. This needs
  `->redirectGuestsTo('/')` in `bootstrap/app.php` first — a global change, outside this controller's scope.
- **NOT a bug:** `LaporanAccountingBankHarianController::doReport()` calls `sp_LapKasHarian` rather than a
  Bank-specific procedure. This is **intentional** — the procedure is generic, its first parameter being
  `@Perkiraan`, so Bank Harian simply passes a `BANK` perkiraan. Verified against the database; do not
  "fix" it.
