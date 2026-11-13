# Release — Payment Gateway for Redsys & WooCommerce Lite v7.1.0

> State on 2026-10-21: **candidate ready on `develop`, not released.** Version 7.1.0 approved by the owner (D-099). The gate's checks ran on the candidate (D-100, D-101). What is left is the owner's: two acceptances (below), the merge to `master`, the tag `v7.1.0`, and the upload to WordPress.org SVN. After publication the assistant posts the replies (D-099).

## Version
- Released before: 7.0.2. This release: **7.1.0** (approved, D-099).
- Why a minor and not 7.0.3: the release changes behaviour a merchant can see (Google Pay offered in test mode, a Bizum total equal to the limit allowed, the Logo setting stored as a URL, the secret fields as password fields, the two admin notices shown to shop managers and administrators only, the transaction limit applied on the order-pay page and in the Blocks checkout, an uninstall routine), adds three filters and new public methods, and loads minified assets. The 7.0.3-hotfix question is moot: D-023 ships here.
- Touchpoints (`docs/03-technical-plan.md`, "Version touchpoints"): header `Version: 7.1.0`, `REDSYS_WOOCOMMERCE_VERSION` 7.1.0, `Stable tag: 7.1.0`, changelog `== 7.1.0 ==`, upgrade notice `= 7.1.0 =`, `package.json` 7.1.0 — all agree (`scripts/keel-verify` check 1). Every function and filter new since 7.0.2 carries `@since 7.1.0`.

## .gitignore boundary (what never enters git)
Unchanged this cycle except `docs/security-audit/` (the raw audit runs, D-056). The pre-commit confidential-data gate has run on every commit since D-039; the staged-file check ran on each commit of this gate (only the Redsys public generic test key, D-042, recognised by hash).

## export-ignore boundary (in repo, not in package)
Corrected in S-032 on 2026-10-06 (D-068): `tests/`, `.wp-env-mu-plugins/`, `.agents/`, `.codex/`, `.githooks/`, `composer.json`, `composer.lock`, the two PHPUnit configurations and `playwright.config.js` are `export-ignore`. `scripts/keel-verify` check 29 compares the archive's top level with a closed list; `uninstall.php` was added to it by S-081.

## Package contents (verified, D-101)
`git archive --format=zip --prefix=woo-redsys-gateway-light/ 704730a`: **83 files**, 982 403 bytes, SHA-256 `d2569ef98c4af177a9e363b5f9df1f3295b6633dc38ea999f0e292a92d7d9be5`. Top level exactly: `about-redsys.php`, `assets/`, `classes/`, `includes/`, `index.php`, `languages/`, `LICENSE`, `readme.txt`, `uninstall.php`, `woocommerce-redsys.php`, `wpml-config.xml`. Not in the package: `resources/`, `docs/`, `scripts/`, `tests/`, the tool configuration. No commit after `704730a` changes a shipped file.
- How the owner publishes (D-099): this archive, uploaded to WordPress.org SVN by the owner. Rebuild it from the tag with the same command; the checksum above identifies the tested one.

## Changelog entry
`readme.txt`, `== 7.1.0 ==` (newest first, as this project's changelog is ordered, D-046), in the assistant's wording approved by the owner (D-099), including: the four visible changes of the Logo setting (D-059); the advice to delete old debug logs that may hold the SHA-256 secret and to consider a new secret (D-058); Google Pay offered in test mode and a Bizum total equal to the limit allowed (D-052); a return to an already cancelled order treated as the cancellation (D-053, confirmed by D-099). Upgrade notice `= 7.1.0 =` added.

## Pre-release verification results
| Check | State | Evidence |
|---|---|---|
| `scripts/keel-doctor --check` | pass | 2026-10-21: `Result: all blocking requirements OK.` (Node 24.21.0, npm 11.19.0, Docker 29.8.1 responding, PHPUnit, `@playwright/test`, `axe-core`, Chromium r1243) |
| Entire suite on the candidate, floor | **pass** | `704730a`, `scripts/keel-affected-tests --full --run`: unit `OK (41 tests, 78 assertions)`, integration `OK (371 tests, 2111 assertions)`, browser `67 passed`; `scope: full — 203 of 203 tests`, `result: GREEN` (D-100). Again at `a210bea` (one test added, no shipped change) by the pre-push hook, widened to the entire suite: `204 of 204`, GREEN |
| Entire suite, ceiling (WordPress 7.1.3, WooCommerce 11.2.1, PHP 8.4) | pass but four known scans | `bcdc389`: unit `OK (41 tests, 78 assertions)`, integration `OK (374 tests, 2124 assertions)`, browser `63 passed`, `4 failed` — the settings-form scans on WooCommerce's help-tip markup (`aria-prohibited-attr`), not the plugin's (D-101) |
| `scripts/keel-verify` | clean but the session row | the only FAIL is S-089's `docs/sessions.md` row, which this session's close writes |
| Minified assets | in sync | `npm run build:assets` on the candidate changed no file; check 11 |
| Debug logging default | off | every gateway's `debug` field has `default => 'no'` |
| Conformance sweep | regenerated at the gate from the disk, the manifest and the decisions | 137 rows: 102 present, **0 missing**, 9 declined, 26 n/a (D-100) |
| Acceptance criteria | 72 of 73 driven | AC-48 is the guided accessibility pass: `ASSISTIVE-TECH`, accepted shortfall (D-099) |
| Real-environment verification on the exact distributable | **pass** | Clean site, 7.0.2 from WordPress.org upgraded to the zip: active, settings byte-identical, four gateways, critical path; uninstall removes the plugin's own data only (D-087); reinstall clean (D-101) |
| `security-auditor` on the final tree | **PASS** | no exploitable defect; two hardening notes; its threat-model corrections applied (D-100) |
| `docs-verifier` | six mismatches, all fixed | D-100 |

## Self-audit results
Run 2026-10-21 on `704730a`, every answer from a command or a file: **27 pass, 5 fail, 3 unverified, 5 n/a** (2026-10-06: 21/11/3/5). The full table is in the gate's run notes; the dispositions are in D-100.

| Failing answer | State |
|---|---|
| 1 — declared static tools never run (PHPCS, PHPStan, Plugin Check) | **run once on the candidate, outside the repository (D-102)**: no security or behaviour finding from PHPCS; three low PHPStan defects, none new in 7.1.0 (S-092); Plugin Check flags the `Requires at least: 4.0` header and the 7.1.0 upgrade notice. Still not installed in the project (S-093) — **accepted for 7.1.0 by the owner (D-106)** |
| 5 — `woocommerce_ajax_loader_url` named by no test | closed: test added (D-100) |
| 6 — the Blocks script's `es_ES` JSON never loaded (wrong file name) | **closed: S-065 brought onto `develop` before the tag (D-106, S-097)** |
| 17n — this session's row | written at the session close |
| 17o — no `scope: full` record | closed: the floor run above |

Unverified: 14 (the doctor — run by the coordinator, pass), 17e (sweeps named in lessons), 18 (unwrapped strings: the PHPCS i18n sniffs are not installed).

## Security audit (card: required)
- Full run at `e50ab39` and scoped run at `476d52e` (2026-10-06): 9 confirmed findings, all fixed and refuted by their verifiers; 0 open. Counts in `docs/security-audit.md`; the runs are local, under `docs/security-audit/`.
- Final-tree read at `704730a`: PASS (D-100).
- `needs_validation` SA-06, SA-13, SA-20: **acknowledged by the owner, not validated** (D-099); they need payments on the owner's test terminals and run after the release (S-090).

## Threat-model verification
Re-read at `704730a` by `security-auditor` row by row. Present-tense rows hold on every path read; stale line references re-resolved; the direct-access and uninstall rows corrected; the D-025 plaintext secret in order meta added to "Not defended" (D-100).

## Accessibility verification results
- Automated: both ends of the support matrix (D-092, D-101); the only failures are the four scans of WooCommerce's help-tip markup — `docs/accessibility.md`. Not run on the installed distributable.
- Real assistive technology: **not run — accepted shortfall for 7.1.0** (D-099), recorded in `docs/accessibility.md`; S-030 deferred.

## Issues closed by this release
`docs/issues.md`: #93 (E-001) and #112 (E-002), shipping in 7.1.0. Two WordPress.org support threads report D-023, shipping in 7.1.0. Replies are posted by the assistant after publication (D-099); no issue is closed by the assistant.

## Token reconciliation
See `docs/token-ledger.md`, "Final reconciliation — 7.1.0". The orchestrating sessions were never metered by the environment, so no total or cost can be stated honestly; the subagent runs that were metered are summed there. Time: 79.4 h estimated (original), 30.68 h measured on the finished slices against 76.9 h estimated for them (−46.2 h, pace factor 0.18), as AI working time plus supervision.

## Release artifacts
- The zip above (built locally, not committed). The tag, the `master` merge and the WordPress.org upload are the owner's.

## What the release still needs, in one list
1. **From the owner:** (answers 1, 6 and the upgrade notice settled by D-106) merge `develop` into `master`; tag `v7.1.0`; build the archive from the tag and upload it to WordPress.org SVN.
2. **From the assistant, after publication:** the replies on #93, #112 and the two forum threads (D-099); `docs/issues.md` entries updated with the shipped version; Phase 7 marked done and the project moved to maintenance.

## Upgrade notice over WordPress.org's limit (D-102)
Plugin Check: the 7.1.0 upgrade notice is about 430 characters; WordPress.org shows 300. What falls past the cut is the instruction to delete old Google Pay debug logs. A 280-character wording, offered and **applied on `develop` by the owner's choice (D-106)**:

> Security and reliability release: fixes unpaid test-mode Bizum and Google Pay payments, refund matching on long order IDs, and notification hardening. If you enabled Google Pay debug logging, delete its old logs (WooCommerce > Status > Logs): they may contain your signing secret.

If the owner takes it, the assistant applies it on `develop`, re-checks the shipped-file diff and rebuilds the package record before the owner tags.
