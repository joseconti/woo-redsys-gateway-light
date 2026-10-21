---
schema: keel.sprint/2
sprint: 6
goal: Apply the owner's pre-tag answers to the 7.1.0 candidate (D-106)
status: done
scope_reconciliations: []
slices:
  - id: S-097
    title: Bring S-065 (f658ebb) onto develop under the 7.1.0 changelog and replace the 7.1.0 upgrade notice with the 280-character wording, then re-check the shipped-file diff against the candidate
    status: done
    original_hours: 0.75
    actual_hours: 0.1014
    actual_source: measured
    residual_hours: 0
    change_kind: added
    parent_slice: null
    owns: [S-097]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [bin/build_i18n.sh, bin/i18n-map.json, languages/, package.json, readme.txt, tests/Integration/BlocksScriptTranslationTest.php, docs/07-release.md, docs/decisions.md, docs/PROGRESS.md, docs/sprints/, docs/.keel/plan.json]
      contracts:
        reads: []
        writes: []
      exclusive_resources: []
      parallel: serial
      serial_reason: one slice; it changes the release candidate the owner merges
---

# Sprint 6 — The owner's pre-tag answers for 7.1.0

- Acceptance: `develop` carries the S-065 fix and the shorter upgrade notice; the shipped-file diff against `704730a` contains only those two changes; the affected tests are green.
- Notes: opened 2026-10-21 after the owner's answers (D-106). Hours are AI working time plus supervision.
- Close-out:
- S-097 (done 2026-10-21, D-106): `f658ebb` brought onto `develop`, its changelog line under `== 7.1.0 ==`; the 280-character upgrade notice applied; the shipped-file diff against `704730a` is the translation JSON and `readme.txt`; 205 of 205 tests green (entire suite, widened by the build-script change).
- Session close (2026-10-21): estimate total updated to sprint 6 (85.15 h); inbound issue sweep clean; token-ledger and sessions rows written. Suppression count 150 against the 149 recorded at sprint 3: this session's diff adds none (`git diff ebb48d9` carries no suppression comment), so the growth predates it — to be traced at the next sprint close.
