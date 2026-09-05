# ProgrammerTime

A project-management tool for small software teams — clients, projects,
tasks, logged work hours ("etapas"), billing, internal messaging, and
client-facing PDF reports. Portuguese UI throughout. Originally built as a
TCC (undergraduate thesis) project by Bruno Vieira ([@brunovidasi](https://github.com/brunovidasi))
and Filipe Moreira.

Built on **CodeIgniter 2.2.0** (PHP) with a **MySQL** database, Bootstrap 3 /
AdminLTE-style UI, and PDF export via bundled **mPDF** and **dompdf** copies.

## Status

This app was written against PHP 5.4 (2014–2015) and did not run at all on
PHP 8. It has been fixed and modernized:

- **~500 instances** of removed curly-brace string/array-offset syntax
  (`$str{0}`) across the application code and the vendored mPDF/dompdf
  libraries, converted to `$str[0]`.
- **~20 vendored classes** (the entire mPDF/CPDF PDF-rendering stack —
  `mPDF`, `Cpdf`, `cssmgr`, `SVG`, `otl`, the GIF/BMP/WMF decoders, etc.)
  used PHP 4-style constructors (`function ClassName()` instead of
  `function __construct()`). PHP 8 removed support for these entirely, so
  `new mPDF()` silently ran an *empty* constructor — none of the class's
  setup code executed, and PDF generation failed with a cascade of
  "call to a member function on null" errors. This was the single biggest
  blocker to the app actually running, and easy to miss since `php -l`
  doesn't catch it (it's not a parse error).
- The database layer used the `mysql` driver against PHP's long-removed
  `ext/mysql`; switched to `mysqli`. Also restored CodeIgniter's expected
  `mysqli_query() === false`-on-error behavior via `mysqli_report(MYSQLI_REPORT_OFF)`,
  since PHP 8.1 made mysqli throw exceptions by default, which bypassed
  CodeIgniter's own error handling.
- Removed the `__autoload()` magic function (removed in PHP 8) from
  dompdf's config, `create_function()` (removed in PHP 8) in dompdf's
  reflower classes, and `each()` (removed in PHP 8) throughout mPDF and
  CodeIgniter's core `Security`/`Xmlrpc` classes.
- Fixed several PHP 8 type-strictness fatals surfaced by actually running
  the app end-to-end (not just linting): `count()`/`in_array()`-style calls
  on values that used to silently coerce from `null`, a `filter_var()` call
  passed a string instead of an int/array flag, an `UPDATE` helper called
  with an empty string where an array was expected, arithmetic on
  non-numeric CSS-length strings (mPDF's `ConvertSize()`), and a
  `break` used outside any loop/switch in an SVG parser callback (should
  have been `return`).
- Fixed CodeIgniter 2's own `&get_config()` reference-return quirk and a
  couple of "optional parameter before required parameter" signatures
  (deprecated, and noisy, under PHP 8).
- **Fixed a live, unauthenticated SQL injection** in `assets/ajax/*.php` —
  two standalone endpoints (used for inline status/priority edits) built
  raw `UPDATE` statements by concatenating the table name, column name, and
  value straight from POST data, with no auth check. Rewrote them to
  validate table/column against a fixed whitelist and bind the value via a
  prepared statement.
- Reconstructed `schema.sql` from the model queries, since no database dump
  existed anywhere in the project.

See [Security](#security) below for what else was found and fixed in the
credential/secret scan.

## Requirements

- PHP 8.1+ with `mysqli`, `mbstring`, `gd`
- MySQL or MariaDB
- A web server with URL rewriting (Apache + `mod_rewrite`, using the bundled
  `.htaccess`)

## Setup

1. Create the database and load the schema:

   ```bash
   mysql -u root < schema.sql
   ```

   This also seeds a first-login admin account (`login: admin`,
   `password: admin123`) hashed against the placeholder `encryption_key`
   that ships in `application/config/config.php`. This is a **local-dev-only**
   password — change it immediately after first login, and if you change
   `encryption_key` (step 3) first, regenerate the hash as described in
   `schema.sql`'s comment before the INSERT, since the two must match.

2. Set your database credentials in `application/config/database.php`.

3. Set a fresh `encryption_key` in `application/config/config.php` — the
   committed one was exposed on a public repo (see Security below) and is
   used for both session-cookie integrity and password hashing, so
   generate a new one before using this anywhere but a throwaway local copy.

4. Point your web server's document root at the project root (so
   `.htaccess` and `index.php` are at the top level).

5. Before any real deployment, switch `ENVIRONMENT` in `index.php` from
   `'development'` to `'production'` (the app's own `leia-me.txt` already
   said this) — with it left on `development`, every PHP notice/warning is
   printed inline, which corrupts binary responses like generated PDFs.

## Project layout

Standard CodeIgniter 2 MVC layout:

- `application/controllers/` — one file per feature area: `acesso`
  (login/registration), `projeto`, `cliente`, `tarefa`, `etapa` (logged
  work hours), `financeiro` (billing), `relatorio` (PDF/HTML reports),
  `mensagem` (internal messages), `usuario`, `nivel_acesso` (permission
  profiles), `empresa` (company settings), `dashboard`.
- `application/models/` — one model per feature area, mirroring the
  controllers above.
- `application/views/` — Bootstrap 3 / AdminLTE-style templates, one
  subfolder per feature area.
- `application/helpers/fdata_helper.php`, `cripto_helper.php`,
  `sql_helper.php`, `gera_senha_helper.php` — app-specific helpers (date
  formatting, password hashing, escaping, password generation).
- `assets/ajax/` — two standalone (non-CodeIgniter) PHP endpoints used for
  inline click-to-edit fields in the UI; see Security below.
- `assets/mpdf/`, `assets/pdf/` — vendored PDF libraries (mPDF and
  dompdf/CPDF respectively). The app's `relatorio` feature uses mPDF; the
  dompdf code path (`Relatorio_model::gera_relatorio_pdf()`) exists but
  isn't wired up to any controller.
- `system/` — CodeIgniter 2.2.0 core, patched only where it broke under
  PHP 8 (see Status above); not upgraded to a newer framework version.
- `schema.sql` — reconstructed from the model layer; not part of stock
  CodeIgniter.

A couple of stray old files worth knowing about (left in place, not part of
the working app): `application/controllers/acesso_old_2014_05_20.php` and
`java_old_2015_03_03.php` are dated snapshots with class names that don't
match CodeIgniter's routing convention for their filenames, so they aren't
reachable — safe to delete if you want to tidy the tree further.

## Security

A secret/PII scan was run over the tracked files and full git history
(single squashed commit) before this modernization. The repository is
**public** on GitHub, so anything found here was already exposed.

**Rotated/replaced in this pass** (values swapped for placeholders in the
working tree — see comments at each site):
- `application/config/config.php`'s `encryption_key` — used for both
  CodeIgniter's session-cookie HMAC and password hashing
  (`cripto_helper.php`). With the old key public, anyone could forge a
  valid session cookie (e.g. set `logado => true`) for any deployment
  still using it, or brute-force stored password hashes offline knowing
  the salt.
- `config.php` (project root)'s `PT_DB_*`/`DB_*_P` database credentials and
  `PT_LINCENSE_KEY`/`PT_ACCESS_PASS`/`PT_VERIFICATION` licensing secrets.
  Unused by the current app (the license check in `acesso.php` is
  hardcoded to always pass) but real-looking hosting credentials.
- A hardcoded shared token in `acesso.php`'s `logar()`, used as an
  API-key-style check for external/JSON callers.

**Flagged, not silently changed** — rotate these on any real server where
they were actually used, since replacing them in the repo doesn't undo the
exposure:
- The admin account's MD5 password hash was sitting in `acesso.php` as a
  plain comment; removed from source. If this corresponds to a real
  deployment's admin account, change that password now.
- All of the above secrets remain visible in this repo's git history even
  after this commit — purging them requires rewriting history
  (`git filter-repo` or BFG) and force-pushing, which is a separate,
  more disruptive step not taken here.

**Removed entirely** (unauthenticated info-disclosure / SSRF surfaces, not
linked from the app's own UI, no functional loss):
- `phpVersion.php` — a bare `phpinfo()` call at the web root.
- `dataServer.php` — dumped the full `$_SERVER` array.
- `getJson.php` — a manual API tester that took an arbitrary `url` POST
  field and made a server-side `curl` request to it with
  `CURLOPT_SSL_VERIFYPEER` disabled (SSRF).

**Cleaned up** (stray files that shouldn't be in version control):
- A Dropbox "conflicted copy" of `application/config/config.php`.
- Two committed CodeIgniter debug log files and one PHP `error_log` file.
- Added `.gitignore` for OS files, logs, and local config overrides.

**Not a secret, left as-is**: several `@programmertime.com` addresses used
as the app's own from/reply-to addresses (e.g. `naoresponda@programmertime.com`
in `enviar_email.php`) — these are meant to be public-facing. One personal
Gmail address embedded in a sample report fixture
(`application/views/relatorio/relatorio.html`) was genericized.
