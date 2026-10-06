# Release — Payment Gateway for Redsys & WooCommerce Lite, next version (in preparation)

> State on 2026-10-06: **not released, gate open.** The version number is proposed below and not approved; nothing in the tree carries it. Every section says what was run and what was not. The merge to `master`, the tag and the publication on WordPress.org are the owner's acts.

## Version (proposed, waiting for the owner)
- Released today: 7.0.2. Proposed: **7.1.0**.
- Why a minor and not 7.0.3: the release changes behaviour a merchant can see (Google Pay offered in test mode, a Bizum total equal to the limit allowed, the Logo setting stored as a URL, the two admin notices shown to shop managers and administrators only, the transaction limit applied on the order-pay page and in the Blocks checkout), adds three filters and new public methods in the gateway classes (`is_valid_return()`, `set_refund_confirmed()`, `validate_logo_field()`), and changes how assets are loaded (minified). 7.0.1 and 7.0.2 were fixes only.
- Once approved: the touchpoints of `docs/03-technical-plan.md` ("Version touchpoints") are set together, the `== Unreleased ==` section of `readme.txt` becomes the version's entry, and the `@since` tags left out on purpose in S-043 and S-045 are written (D-057, D-059).

## .gitignore boundary (what never enters git)
Unchanged this cycle except `docs/security-audit/` (the raw audit runs, D-056). Whole-tree confidential-data check on the candidate: **not yet run for this gate** (the pre-commit gate has run on every commit since D-039; the Redsys public test key is recognised by hash, D-042).

## export-ignore boundary (in repo, not in package)
Corrected in S-032. Before, the archive carried development files: `tests/`, `.wp-env-mu-plugins/` (the playground's HTTP stub, which must never ship, D-029), `.agents/`, `.codex/`, `.githooks/`, `composer.json`, `composer.lock`, the two PHPUnit configurations and `playwright.config.js`. All are now `export-ignore`. `scripts/keel-verify` check 29 compares the archive's top level with a closed list and fails on anything else (seen failing against the previous `.gitattributes`: ten entries reported).

## Package contents (verified on `git archive` of the working tree's attributes, 2026-10-06)
71 files, top level exactly: `about-redsys.php`, `assets/`, `classes/`, `includes/`, `index.php`, `languages/`, `LICENSE`, `readme.txt`, `woocommerce-redsys.php`, `wpml-config.xml`.
- `assets/css/`: three stylesheets, each with its minified pair. `assets/js/frontend/`: the Blocks script, its minified pair and their two manifests. `assets/images/`: the gateway and About-page images.
- `classes/` 7 files, `includes/` 3 files plus `blocks/` (5) and `data/` (16), `languages/` 5 files.
- Not in the package and present in the repository: `resources/` (the script's source), `docs/`, `scripts/`, `tests/`, the tool configuration.
- Not verified: how the owner actually builds what goes to WordPress.org (an SVN checkout or this archive). If it is not `git archive`, this boundary protects nothing there.

## Changelog entry
The `== Unreleased ==` section of `readme.txt`, newest first as this project's changelog is ordered (D-046). To decide with the owner when the version is approved:
- the wording about existing Google Pay debug logs, which may hold the signing secret (D-058);
- the four visible changes of the Logo setting (D-059);
- the two visible changes of D-052 and the handling of a return to an already cancelled order (D-053, to be confirmed).

## Pre-release verification results
| Check | State | Evidence |
|---|---|---|
| `scripts/keel-doctor --check` | pass | 2026-10-06: `Result: all blocking requirements OK.` (Node 24.21.0, npm 11.19.0, Docker responding, PHPUnit, `@playwright/test`, `axe-core`, Chromium r1243) |
| Entire suite | run at `388bda8`, **to be run again on the final candidate** | `scripts/keel-affected-tests --run`, widened to the entire suite: unit `OK (34 tests, 71 assertions)`, integration `OK (193 tests, 1372 assertions)`, e2e `22 passed`. `scope: affected — 120 of 120 tests`. The gate needs the `scope: full` run on the tree that carries the approved version |
| `scripts/keel-verify` | clean except the session row, which the session close writes | 29 checks |
| Minified assets | in sync | `npm run build:assets` on the candidate changed no file |
| Debug logging default | off | every gateway's `debug` field has `default => 'no'` |
| Conformance sweep | re-run 2026-10-06 from the disk, the manifest and the decisions (S-061) | 173 rows: 101 present, 5 missing, 5 declined, 62 n/a. Missing, each with its decision: T1-61 (guided accessibility pass, S-030), T1-65 (this file, until the gate closes), and the three one-time observations of S-037. To be re-run on the final candidate |
| Acceptance criteria | 23 still on D-045's unverified list | to be walked at the gate |
| Support matrix, both ends, on the development tree | run 2026-10-06 (S-058, D-069); **to be run again on the final candidate** | Floor (WordPress 7.0, WooCommerce 7.4.0, PHP 7.4.33): unit 34, integration 193, browser 22, all green. Ceiling (`.wp-env.ceiling.json`: WordPress 7.0.7, WooCommerce 10.9.4, PHP 8.3.35): unit 34 and integration 193 green; browser 18 of 22 — the four failures are the settings accessibility scans, on WooCommerce's help-tip markup. The first ceiling run failed 13 integration tests; two defects in shipped code were fixed (S-062, S-063, D-070). WordPress 7.1.3 and WooCommerce 11.1.2 are current and not declared, not tested |
| Real-environment verification on the exact distributable (install, configure, uninstall, reinstall; upgrade from 7.0.2) | **not run** | needs the versioned package; a rehearsal on the unversioned archive is slice S-059 |
| `security-auditor` on the tree | read at `e89356b`, 2026-10-06 (S-061); **not closed** | 7 candidates raised (1 medium, 1 low-medium, 5 low), none verified by a second reader yet; they are in the local run directory, not here. The shipped code changed after the read (S-062, S-063), so the final tree needs its own read |

## Self-audit results
Not run yet. `references/anti-patterns.md`, answer by answer, on the final candidate.

## Security audit (card: required)
- Full run at `e50ab39` and scoped run at `476d52e` (2026-10-06): 9 confirmed findings, all fixed and each refuted by its verifier at the candidate; 0 open. Counts in `docs/security-audit.md`; the runs are local, under `docs/security-audit/`.
- Since `476d52e` the shipped code changed in one file only, `includes/class-redsys-lite-apps-plugins.php` (four colour values, S-056).
- **Open for the gate:** three `needs_validation` items (SA-06, SA-13, SA-20) wait for the owner: resolve or acknowledge each.

## Threat-model verification
Not re-read for this gate. Controls were moved to their real state at each fix slice (D-057 to D-064).

## Accessibility verification results
- Automated: run on the development tree, 2026-10-06 — `docs/accessibility.md`. Not yet run on the installed distributable.
- Real assistive technology: **not run** (S-030, `ASSISTIVE-TECH`). The gate needs it, or the shortfall recorded and accepted by the owner.

## Issues closed by this release
`docs/issues.md`: #93 (E-001) and #112 (E-002), both fixed on `develop`. #112 has no reply yet. Neither is closed by the assistant.

## Token reconciliation
At the release.

## Release artifacts
None yet.

## What the gate still needs, in one list
1. From the owner: the version number; the three `needs_validation` items; D-053's choice for issue #112 and the reply on it; the release-note wording for D-058 and D-059; the guided accessibility pass (or its acceptance as a recorded shortfall); how the WordPress.org package is built.
2. From the assistant, once the version is approved: touchpoints and changelog; the entire suite, `scope: full`, on that tree; the package rebuilt and installed in a clean site (lifecycle and upgrade from 7.0.2); the self-audit; the conformance sweep; the threat model and the unverified criteria walked; `security-auditor` on the final tree.
