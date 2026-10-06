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
    status: not-started
    hours: 2
    actual_hours: null
    actual_source: estimated
    depends_on: []
    criteria: []
  - id: S-029
    title: Automated accessibility pass on the settings screens and the checkout surfaces
    status: not-started
    hours: 1
    actual_hours: null
    actual_source: estimated
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
    status: not-started
    hours: 1
    actual_hours: null
    actual_source: estimated
    depends_on: [S-036]
    criteria: []
  - id: S-032
    title: Phase 7 release gate on the candidate (full suite, version proposed to the user, package hygiene)
    status: not-started
    hours: 2
    actual_hours: null
    actual_source: estimated
    depends_on: [S-036, S-027, S-028, S-029, S-031, S-035, S-039]
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
