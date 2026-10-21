---
schema: keel.sprint/2
sprint: 8
goal: Mechanical defects extracted from the S-053 and S-041 notes, independent of the owner's 7.1.0 release block, on work/after-7.1.0 (D-109)
status: done
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
    title: get_acctinfo() with a $user_id of a user that does not exist read properties of get_userdata()'s false (PHP notices/warnings, a meaningless account age) — such a customer is now a guest
    status: done
    original_hours: 0.5
    actual_hours: 0.2397
    actual_source: measured
    residual_hours: 0
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
    status: done
    original_hours: 0.5
    actual_hours: 0
    actual_source: measured
    residual_hours: 0
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
    title: wpml-config.xml registers the settings of a gateway that does not exist (iupay) — removed, and the title and description of all four gateways the plugin has are registered
    status: done
    original_hours: 0.25
    actual_hours: 0
    actual_source: measured
    residual_hours: 0
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
- S-098, S-099, S-100 (done 2026-10-21, D-110): `work/after-7.1.0` at `f6f523a`, pushed; reproduction tests `AcctInfoMissingUserTest`, `RedsysOptionNotArrayTest`, `WpmlConfigTest` seen failing first, then green; affected selection 212 of 226 green before the push, and again in `pre-push`; code-reviewer: no blocking finding (its two low notes applied). The three slices were built inside one clock interval, all measured on S-098 (0.24 h); S-099 and S-100 read 0 h for that reason. Estimated 1.25 h in all.
- Sprint closed: the rest of `deferred.md` needs the owner (D-103, D-109) or waits for the tag (S-093, D-102).
