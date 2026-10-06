---
schema: keel.sprint/1
sprint: 3
goal: Release preparation for the unreleased security and correctness fixes
status: not-started
slices:
  - id: S-027
    title: CSS/JS source plus minified pairs, build script, SCRIPT_DEBUG-aware enqueue
    status: not-started
    hours: 1.5
    actual_hours: null
    actual_source: estimated
    depends_on: []
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
    depends_on: []
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
    status: not-started
    hours: 1.5
    actual_hours: null
    actual_source: estimated
    depends_on: []
    criteria: []
  - id: S-035
    title: Reproduce and fix the candidate defects surfaced by the documentation backfill (Google Pay fallback and paid guard, zero-amount refunds, Bizum limit casts, secret in debug logs)
    status: not-started
    hours: 2.5
    actual_hours: null
    actual_source: estimated
    depends_on: []
    criteria: []
  - id: S-032
    title: Phase 7 release gate on the candidate (full suite, version proposed to the user, package hygiene)
    status: not-started
    hours: 2
    actual_hours: null
    actual_source: estimated
    depends_on: [S-027, S-028, S-029, S-031, S-035]
    criteria: []
---

# Sprint 3 — Release preparation for the unreleased security and correctness fixes

- Acceptance: the fixes recorded in D-020, D-023, D-026 and D-033 are ready to ship: the full suite is green on the candidate, the security audit covers it or is declined on the record, and the version number has been approved by the user. Merging to `master` and tagging remain the user's acts.
- Notes: built from `docs/PROGRESS.md` open items on 2026-10-06 during the reconciliation. S-027 was scheduled here by the user rather than applied inside the reconciliation, because it changes code that reaches production stores. Hours are AI working time plus supervision.
- Candidate defects (S-035), read from code during S-025 and NOT yet reproduced — each starts from a failing test per D-036: (1) the order-received fallback passes no `Ds_SignatureVersion`, and the Google Pay gateway calls `wp_die()` without it; (2) Google Pay `successful_request()` has no already-paid guard; (3) a refund amount of `0` refunds the full total in the Redsys, Bizum and Google Pay gateways (fixed for Inespay only in D-026); (4) the Bizum transaction limit still truncates with integer casts (fixed for Inespay in D-026/D-030); (5) debug logging in Bizum and Google Pay writes the signing secret in clear — not covered by D-025, to be added to `docs/threat-model.md`; (6) Google Pay is hidden for everyone in test mode because `testshowgateway` has no settings field. The full list with file:line is in the S-025 hand-back recorded in `docs/decisions.md` D-041.
- Close-out:
