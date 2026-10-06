---
schema: keel.sprint/1
sprint: 3
goal: Release preparation for the unreleased security and correctness fixes
status: in-progress
slices:
  - id: S-036
    title: Restore the playground on this machine (PHP 7.4 base image no longer builds; plugin activation order under the current wp-env)
    status: done
    hours: 1.5
    actual_hours: 0.1892
    actual_source: measured
    depends_on: []
    criteria: []
  - id: S-027
    title: CSS/JS source plus minified pairs, build script, SCRIPT_DEBUG-aware enqueue
    status: done
    hours: 1.5
    actual_hours: 0.1189
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-028
    title: Active security audit of the release candidate (Security audit is required)
    status: done
    hours: 2
    actual_hours: 0.2872
    actual_source: measured
    depends_on: []
    criteria: []
  - id: S-029
    title: Automated accessibility pass on the settings screens and the checkout surfaces
    status: done
    hours: 1
    actual_hours: 0.0672
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-030
    title: Guided assistive-technology pass with the user
    status: not-started
    hours: 1
    actual_hours: null
    actual_source: estimated
    depends_on: [S-029]
    criteria: []
  - id: S-031
    title: Dependabot alerts triage, stale wp-scripts dependency, package.json version sync
    status: done
    hours: 1.5
    actual_hours: 0.0936
    actual_source: measured
    depends_on: []
    criteria: []
  - id: S-035
    title: Reproduce and fix the candidate defects surfaced by the documentation backfill (Google Pay fallback and paid guard, zero-amount refunds, Bizum limit casts, secret in debug logs)
    status: done
    hours: 2.5
    actual_hours: 0.1303
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-037
    title: Remaining one-time verifications of the Keel scaffolding (Stop hook after a session restart, timing report over a real multi-session slice, a real push through pre-push)
    status: not-started
    hours: 0.5
    actual_hours: null
    actual_source: estimated
    depends_on: [S-036]
    criteria: []
  - id: S-038
    title: Issue 112 — cancel URL sent to Redsys is HTML-escaped and re-cancels an already cancelled order
    status: done
    hours: 1
    actual_hours: 0.1347
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-039
    title: Build toolchain on current Node — major upgrade of @wordpress/scripts and the WooCommerce dependency-extraction plugin (clears the remaining build-tool audit findings)
    status: done
    hours: 1
    actual_hours: 0.0681
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-043
    title: Security fix SA-01 from the 2026-10-06 audit
    status: done
    hours: 0.5
    actual_hours: 0.1086
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-044
    title: Security fix SA-03 from the 2026-10-06 audit
    status: done
    hours: 0.25
    actual_hours: 0.0578
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-045
    title: Security fix SA-17 from the 2026-10-06 audit
    status: done
    hours: 0.5
    actual_hours: 0.0781
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-046
    title: Security fix SA-07 from the 2026-10-06 audit
    status: done
    hours: 0.5
    actual_hours: 0.1564
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-047
    title: Security fix SA-08 from the 2026-10-06 audit
    status: done
    hours: 0.25
    actual_hours: 0.0164
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-048
    title: Security fix SA-09 from the 2026-10-06 audit
    status: done
    hours: 0.25
    actual_hours: 0.0544
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-049
    title: Security fix SA-05 from the 2026-10-06 audit
    status: done
    hours: 0.75
    actual_hours: 0.1097
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-050
    title: Security fix SA-16 from the 2026-10-06 audit
    status: done
    hours: 0.5
    actual_hours: 0.2094
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-051
    title: Security fix SA-19 from the 2026-10-06 audit
    status: done
    hours: 0.25
    actual_hours: 0
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-054
    title: File and line references in docs/reference re-resolved against the code, with a keel-verify check that fails when one no longer points at its symbol
    status: done
    hours: 0.5
    actual_hours: 0
    actual_source: measured
    depends_on: [S-043, S-044, S-045, S-046, S-047, S-048, S-049, S-050, S-051]
    criteria: []
  - id: S-052
    title: Scoped security re-audit of the fixes (each verifier re-run against the candidate; diff since e50ab39)
    status: done
    hours: 0.5
    actual_hours: 0.1256
    actual_source: measured
    depends_on: [S-043, S-044, S-045, S-046, S-047, S-048, S-049, S-050, S-051]
    criteria: []
  - id: S-056
    title: About page — text colours below the 4.5:1 contrast of WCAG 2.2 AA (found by the automated pass, S-029)
    status: done
    hours: 0.25
    actual_hours: 0.1061
    actual_source: measured
    depends_on: [S-029]
    criteria: []
  - id: S-032
    title: Phase 7 release gate on the candidate (full suite, version proposed to the user, package hygiene)
    status: in-progress
    hours: 2
    actual_hours: 0.0386
    actual_source: measured
    depends_on: [S-036, S-027, S-028, S-029, S-031, S-035, S-039, S-052, S-054, S-056]
    criteria: []
  - id: S-058
    title: Support-matrix ceiling — a second wp-env instance on the declared WooCommerce ceiling and a current PHP, and the entire suite run on it
    status: done
    hours: 1
    actual_hours: 0.1092
    actual_source: measured
    depends_on: [S-036]
    criteria: []
  - id: S-059
    title: Lifecycle rehearsal on the built archive — install, configure, uninstall, reinstall, and the upgrade from 7.0.2 (unversioned candidate; repeated on the versioned package at the gate)
    status: done
    hours: 1
    actual_hours: 0.0783
    actual_source: measured
    depends_on: [S-032]
    criteria: []
  - id: S-060
    title: Driven tests for the as-built acceptance criteria still on D-045's unverified list
    status: done
    hours: 2
    actual_hours: 0.8333
    actual_source: measured
    depends_on: [S-036]
    criteria: [AC-09, AC-10, AC-11, AC-12, AC-15, AC-16, AC-23, AC-31, AC-32, AC-35, AC-41, AC-42, AC-43, AC-46, AC-47, AC-49, AC-50, AC-52, AC-54, AC-55, AC-56, AC-57]
  - id: S-061
    title: Gate checks that do not need the version number — whole-tree confidential-data check, conformance sweep, self-audit, threat model, code map and change map, security-auditor and docs-verifier on the tree
    status: done
    hours: 1
    actual_hours: 0.0989
    actual_source: measured
    depends_on: [S-052]
    criteria: []
  - id: S-062
    title: The reverse lookup of an order by its Redsys order number uses a query WooCommerce 9.2+ reports as unsupported on the legacy order storage (found by the ceiling run, S-058)
    status: done
    hours: 0.5
    actual_hours: 0.3425
    actual_source: measured
    depends_on: [S-058]
    criteria: []
  - id: S-063
    title: PHP 8.1+ deprecation notices from the notification handlers (null passed to htmlspecialchars_decode) and a dynamic property set by one test (found by the ceiling run, S-058)
    status: done
    hours: 0.25
    actual_hours: 0.0003
    actual_source: measured
    depends_on: [S-058]
    criteria: []
  - id: S-064
    title: A Bizum notification posted without its signature-version field ends in an uncaught error on PHP 8 (first hardening note of S-053; raised again by the gate's security read)
    status: done
    hours: 0.25
    actual_hours: 0.1022
    actual_source: measured
    depends_on: [S-058]
    criteria: []
  - id: S-067
    title: Two drifts found while driving the last criteria (S-060) — the configuration reference says the refund confirmation flag never expires, and the test map has no row for PluginWithoutWooCommerceTest
    status: not-started
    hours: 0.15
    actual_hours: null
    actual_source: estimated
    depends_on: [S-060]
    criteria: []
---

# Sprint 3 — Release preparation for the unreleased security and correctness fixes

- Acceptance: the fixes recorded in D-020, D-023, D-026 and D-033 are ready to ship: the full suite is green on the candidate, the security audit covers it or is declined on the record, and the version number has been approved by the user. Merging to `master` and tagging remain the user's acts.
- Notes: built from `docs/PROGRESS.md` open items on 2026-10-06 during the reconciliation. S-027 was scheduled here by the user rather than applied inside the reconciliation, because it changes code that reaches production stores. Hours are AI working time plus supervision.
- Candidate defects (S-035), read from code during S-025 and NOT yet reproduced — each starts from a failing test per D-036: (1) the order-received fallback passes no `Ds_SignatureVersion`, and the Google Pay gateway calls `wp_die()` without it; (2) Google Pay `successful_request()` has no already-paid guard; (3) a refund amount of `0` refunds the full total in the Redsys, Bizum and Google Pay gateways (fixed for Inespay only in D-026); (4) the Bizum transaction limit still truncates with integer casts (fixed for Inespay in D-026/D-030); (5) debug logging in Bizum and Google Pay writes the signing secret in clear — not covered by D-025, to be added to `docs/threat-model.md`; (6) Google Pay is hidden for everyone in test mode because `testshowgateway` has no settings field. The full list with file:line is in the S-025 hand-back recorded in `docs/decisions.md` D-041.
- Close-out:
- S-031 (done 2026-10-06, D-050): the two unused `dependencies` removed, `package.json` version synced to 7.0.2 and now checked by `scripts/keel-verify`, non-breaking audit fixes applied. `npm audit` 147 → 115; everything left hangs on two major upgrades — `@wordpress/scripts` 36 (new slice S-039) and `@wordpress/env` 11 (belongs to S-036, it is the playground tool). Not pushed: the pre-push selection for a lockfile change is the entire suite, which needs S-036.
- S-036 (done 2026-10-06, D-051, L-008): playground rebuilt from nothing on `@wordpress/env` 11 with PHP 7.4 unchanged; setup scripted in `scripts/playground-setup`; automatic updater off; whole suite green, 49 of 49. The S-031 commit was pushed with it, through the pre-push hook.
- S-035 (done 2026-10-06, D-052): the six candidate defects reproduced from failing tests and fixed, plus two the tests uncovered; 20 new tests; `AC-24`, `AC-33` revised and bound, `AC-58` to `AC-61` added. Two visible behaviour changes for the release notes: Google Pay is now offered in test mode, and a Bizum total equal to the limit is allowed.
- S-038 (done 2026-10-06, D-053): issue 112 reproduced in both halves and fixed — the cancel URL sent to Redsys is a plain URL, and a return to an order Redsys already cancelled shows WooCommerce's cancelled notice instead of an error. 11 integration tests and one e2e spec; `AC-62`, `AC-63` added. The way the second half was solved is the assistant's choice and is to be confirmed by the user before the release. S-042 deferred.
- S-027 (done 2026-10-06, D-054): every stylesheet and the Blocks script ship as a source plus a minified pair; production loads the minified file, `SCRIPT_DEBUG` the readable one. The minified script is byte-identical to the one shipped today. Linter check 11 now compares against a rebuild. Conformance row T1-42 is `present`.
- S-039 (done 2026-10-06, D-055): `@wordpress/scripts` 36 and the WooCommerce dependency-extraction plugin 5.1; the build runs on Node 24 without the OpenSSL workaround; `npm audit` 115 → 45. The upgrade's default would have made the Blocks script depend on a handle WordPress older than 6.6 does not have; the source now compiles JSX against `wp-element`.
- S-028 (done 2026-10-06, D-056): full active audit at `e50ab39` — 25 units, 19 candidates, each decided by a verifier that did not hunt it. 9 confirmed (3 medium, 6 low), 2 need a fact only the owner can observe, 8 rejected; no critical or high. The run is local and gitignored (`docs/security-audit/2026-10-06-e50ab39/`); the committed record is the counts-only row in `docs/security-audit.md`. The confirmed findings are slices S-043 to S-051, and S-052 is the scoped re-audit the release gate needs. Titles stay neutral while the findings are open.
- S-043 to S-051 (done 2026-10-06, D-057 to D-064): the nine confirmed findings of the audit, each reproduced by a failing test, fixed, reviewed by an independent read and pushed. S-043: a notification that names no order never verifies. S-044: the Google Pay form-path debug line no longer writes the signing secret. S-045: the Logo setting is a URL, validated on save and escaped where used. S-046: the order-received fallback checks gateway, order and signature before its wait, and a return must name the order in the URL. S-047 and S-048: the two admin notices and the welcome redirect check the user. S-049: a refund waits for its own confirmation. S-050: the Bizum and Inespay transaction limit holds on every route to a payment. S-051: no fatal error on the front end without WooCommerce. Four reviews found something the first fix had missed (S-046 twice, S-048, S-050); each was reproduced by a test before being closed. Added on the way: S-054 (line references in `docs/reference/`), and S-055 in `deferred.md`. Whole suite at the last slice: unit 34, integration 193, e2e 8, all green. S-051 shows 0 h measured because its reproduction and fix were done while S-050 waited for its review; the time is inside S-050's figure.
- S-052 (done 2026-10-06, D-065): scoped re-audit at `476d52e`. The nine confirmed findings each went back to a verifier that had not hunted them and saw neither the fixes' records nor the earlier verdicts: nine `rejected`, each with the control located on every path, siblings included. Four hunters over the shipped-code diff (three, then one more for the gap the coverage check found): no candidate. One variant noticed by a verifier was raised as SA-20 and decided by another verifier: `needs_validation`, a fact about Redsys the source cannot show. Nothing was executed in the run. 18 hardening notes join S-053.
- S-054 (done 2026-10-06, D-066): 64 of the 96 `file:line` references in `docs/reference/` no longer pointed at their symbol; all re-resolved. `scripts/keel-verify` check 28 fails a reference whose line does not name the symbol written beside it (seen failing on a reference moved by one line). Done inside the waits of S-052, so its 0 h measured is inside S-052's figure.
- S-029 (done 2026-10-06, D-067): `tests/e2e/accessibility.spec.js`, 14 axe scans limited to what the plugin renders. Checkout rows, payment forms, Blocks options and settings forms: no violation. About page: 32 contrast nodes (S-056). Admin notices: 4 contrast nodes from WooCommerce's stylesheet, recorded and deferred (S-057). `axe-core` declared as a development dependency; doctor row and selector mapping added. The guided script for S-030 is written.
- S-056 (done 2026-10-06, D-067): four colour values of the About page darkened; the scan that failed passes. Whole suite green: unit 34, integration 193, e2e 22.
- S-032 (in progress, D-068): the archive carried development files (tests, the playground's HTTP stub, tool configuration); ten paths are now `export-ignore` and `scripts/keel-verify` check 29 holds the boundary. `docs/07-release.md` records every gate item with its real state. Version 7.1.0 proposed, not set. Stopped at the owner.
- S-037 (open): two of its three observations were made this session without starting the slice's clock. A real push went through `.githooks/pre-push` with a real selection (commit `388bda8`: `scope: affected — 120 of 120 tests`, GREEN; it printed `scripts/keel-affected-tests: line 221: printf: write error: Broken pipe` twice, harmless to the result, cause not looked at). The timing report was read here over several sessions (`scripts/keel-time report`: cumulative figures over 20 measured slices). Not observed: the Stop hook after a full session restart — a session cannot watch its own turn end; and no slice has yet spanned two sessions (S-032 will).
- Session close of 2026-10-06, evening (commit `48e49a3`): lesson L-010 (the archive shipped development files), the issue sweep (six open, none new, #112 unanswered), the token-ledger row and the session row. No slice changed state in it.
- Records fix after the last slice (commit `a6b8623`): four bookkeeping lines `scripts/keel-verify` rejected at the session close — two index rows, D-045's list, the estimate's Phase 5 sum, one test-point row without its scope line. No code.
- Session of 2026-10-06, night — the work that does not wait for the owner. Six slices added: S-058 to S-061 at the start, S-062 and S-063 when the ceiling run found them.
- S-058 (done, D-069, L-011): a second wp-env instance on the declared ceiling (WordPress 7.0.7, WooCommerce 10.9.4, PHP 8.3) and the entire suite on it. First run: 13 integration failures and 19 of 22 browser specs. After the two fixes and three setup changes: unit 34, integration 193, browser 18 of 22 (four settings accessibility scans, WooCommerce's help-tip markup). The clock gives S-058 0.11 h and S-062 0.34 h; most of S-062's figure is S-058's work, because the clock holds one slice at a time and the ceiling runs continued after S-062 was opened.
- S-062 and S-063 (done, D-070): the order lookup by Redsys order number no longer uses a query WooCommerce 9.2+ reports as unsupported on the legacy order storage; no PHP 8.1+ deprecation from the notification handlers.
- S-061 (in progress): conformance sweep regenerated (173 rows, 5 missing, each with its decision); `docs-verifier` found one missing index row (`validate_logo_field()`, added); `security-auditor` read the tree at `e89356b` and raised 7 candidates, none verified yet (local run directory); the self-audit ran — 21 pass, 11 fail, 3 unverified, 5 not applicable.
- S-059 (done, D-071): on a clean third site, 7.0.2 from WordPress.org upgraded to the archive of `5d31223` — active, settings identical, no error; browser suite against the installed package 18 of 22 (the same four WooCommerce scans). Uninstalling removes the files only: settings with their secrets, two options and the transients stay, and a reinstall inherits them. Not changed; open for the owner. To be repeated on the versioned package.
- S-061 (done, D-072) and S-064 (done, D-072): the gate checks that need no version were run and recorded with what they found; the one finding reproduced by a test was fixed (a Bizum notification without its signature-version field no longer ends in a PHP error). S-065 deferred (the Blocks script's Spanish translation file is never loaded). Docker Desktop stopped once during the session, at about the time the throw-away rehearsal site was removed; it was started again and both instances came back with their data.
- S-060 (in progress, nothing bound): a `test-driver` agent wrote two files and reported ten criteria bound. Checked by running and reading them: one file ended the PHPUnit process after its second test (and with it every test after it in a whole-suite run), the other asserted nothing about the criteria (an option written and read back, `method_exists`, a string in a file). Neither was committed. What the slice needs, learned here: `successful_request()` of the card, Bizum and Google Pay gateways ends in a bare `exit` on the amount-mismatch, refused and already-paid paths, so AC-10, AC-11, AC-12, AC-23 and AC-31 cannot be driven in-process — they need a signed notification posted over HTTP to `?wc-api=WC_Gateway_<id>` on the development site and the order read back. The 2 h estimate stands. Ceiling at the session's last tree: unit `OK (34 tests, 71 assertions)`, integration `OK (199 tests, 1378 assertions)`.
- S-060 (in progress, six of 22 bound — D-073): `tests/e2e/notification-outcomes.spec.js` posts a signed notification over HTTP to the three Redsys-protocol gateways and reads the order back; AC-09, AC-10, AC-11, AC-12, AC-23 and AC-31 are bound (11 tests, green on the pinned instance; whole selection `131 of 131`, GREEN). No shipped code changed. No mutation run: the permission classifier refused the temporary edit of the handlers, and after it also refused plain reads of the gateway classes, so the sixteen criteria left (AC-15, AC-16, AC-32, AC-35, AC-41, AC-42, AC-43, AC-46, AC-47, AC-49, AC-50, AC-52, AC-54, AC-55, AC-56, AC-57) were not started — they need the gateway source read first.
- S-060 (in progress, thirteen of 22 bound — D-074): AC-32, AC-35, AC-41, AC-42, AC-43, AC-46 and AC-54 bound to three new integration classes (44 tests) and one browser spec (3 tests); whole integration suite `OK (243 tests, 1506 assertions)`. Docker Desktop stopped during the first push of this session and was started again; the pinned instance came back as it was, the ceiling instance was left stopped. One finding deferred as S-066 (Inespay drawn in the Blocks checkout outside its countries on WooCommerce 7.4). Left: AC-15, AC-16, AC-47, AC-49, AC-50, AC-52, AC-55, AC-56, AC-57.
- S-060, timing: the slice's clock was closed at the first commit of this session and not re-opened for the second part, so the measured 0.49 h covers the first part only; the second part's time (about 40 minutes by the session's wall clock) is in the session row, not in the slice's `actual_hours`.
- S-037 (open), one more observation made without its clock: `.githooks/pre-push` refused a real push (`pre-push: REFUSED — the selection for refs/heads/develop is RED`) when Docker Desktop had stopped under the suite, and let the same commit through once the playground was back (`131 of 131`, GREEN). Lesson L-013 recorded (the refused mutation run).
- S-060 (done 2026-10-06, D-075): the last nine criteria bound — AC-15 and AC-16 (test-mode warning, currency rule), AC-47 (settings fields of the four gateways against `docs/usage/configuration.md`), AC-49 (About page, who may open it, its six filters), AC-50 and AC-52 (refund request, wait and failures), AC-55, AC-56 and AC-57 (admin order details, order-received text, High-Performance Order Storage declaration). Five integration classes (66 tests) and one browser spec (11 tests); whole integration suite `OK (309 tests, 1977 assertions)`. The session read the gateway classes without being refused. No shipped code changed. Only AC-48 is left on D-045's list, and it is S-030's. Two drifts found on the way are S-067. Not run on the ceiling instance, which is still stopped.
