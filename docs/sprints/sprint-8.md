---
schema: keel.sprint/2
sprint: 8
goal: Mechanical defects extracted from the S-053 and S-041 notes, independent of the owner's 7.1.0 release block, on work/after-7.1.0 (D-109)
status: in-progress
scope_reconciliations:
  - id: R8-01
    date: 2026-10-21
    reason: D-109 — S-098 and S-099 extracted from S-053 (residual 2 to 1), S-100 from S-041 (residual 1.5 to 1.25), both parents staying in deferred.md
    slices: [S-053, S-098, S-099, S-041, S-100]
    remaining_before: 0
    completed_residual: 0
    added_scope: 1.25
    explicit_residual_reestimates: 0
    remaining_after: 1.25
slices:
  - id: S-098
    title: get_acctinfo() with a $user_id of a user that does not exist reads get_userdata() false as an object (a fatal on PHP 8) — such a customer is now a guest
    status: not-started
    original_hours: 0.5
    residual_hours: 0.5
    change_kind: extracted
    parent_slice: S-053
    owns: [S-098]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [classes/class-wc-gateway-redsys-psd2-light.php, tests/Integration/, readme.txt]
      contracts:
        reads: []
        writes: []
      exclusive_resources: [playground]
      parallel: serial
      serial_reason: the reproduction tests run on the one wp-env playground mounted on this checkout
  - id: S-099
    title: get_redsys_option() of the global and PSD2 helpers calls array_key_exists() on whatever the gateway settings option holds — a non-array value is a TypeError on PHP 8 (H-6)
    status: not-started
    original_hours: 0.5
    residual_hours: 0.5
    change_kind: extracted
    parent_slice: S-053
    owns: [S-099]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [classes/class-wc-gateway-redsys-global-lite.php, classes/class-wc-gateway-redsys-psd2-light.php, tests/Integration/, readme.txt]
      contracts:
        reads: []
        writes: []
      exclusive_resources: [playground]
      parallel: serial
      serial_reason: the reproduction tests run on the one wp-env playground mounted on this checkout
  - id: S-100
    title: wpml-config.xml registers the settings of a gateway that does not exist (iupay) — removed, and the title and description of the three gateways the plugin has are registered
    status: not-started
    original_hours: 0.25
    residual_hours: 0.25
    change_kind: extracted
    parent_slice: S-041
    owns: [S-100]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [wpml-config.xml, readme.txt]
      contracts:
        reads: []
        writes: []
      exclusive_resources: []
      parallel: serial
      serial_reason: three small slices on one work branch
---

# Sprint 8 — Mechanical defects from the S-053 and S-041 notes

- Acceptance: each fix on `work/after-7.1.0` from a reproduction check seen failing; the affected tests green; independent review of the diff; `develop` (the 7.1.0 candidate) carries no product change.
- Notes: opened 2026-10-21 at the owner's third request for work that does not depend on the merge and tag. D-103 left S-053 and S-041 whole for triage with the owner; each holds items that need the owner's judgment AND items that are plain defects with one possible fix. The defects are extracted here (D-109); everything that changes what a shop or a customer sees stays in the parents. H-5 of S-053 (an undefined `$result` in `process_refund()`) is already gone: S-092 (`7bbac84`) removed it. Hours are AI working time plus supervision.
- Close-out:
