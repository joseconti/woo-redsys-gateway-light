---
schema: keel.sprint/2
sprint: 13
goal: 7.1.0 carries the next-version work — work/after-7.1.0 folded into the release, readme changelog unified, develop is the new candidate (D-115)
status: in-progress
scope_reconciliations:
  - id: R13-01
    date: 2026-11-13
    reason: D-115 — the owner asked for develop to carry the correct version and a fixed readme.txt so they can release now; S-106 added
    slices: [S-106]
    remaining_before: 1
    completed_residual: 0
    added_scope: 1.5
    explicit_residual_reestimates: 0
    remaining_after: 2.5
slices:
  - id: S-106
    title: Fold the Unreleased changelog into 7.1.0, merge work/after-7.1.0 into develop, full suite on the new candidate (floor and ceiling)
    status: in-progress
    original_hours: 1.5
    actual_hours: 0
    actual_source: measured
    residual_hours: 1.5
    change_kind: added
    parent_slice: null
    owns: [S-106]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [readme.txt, docs/PROGRESS.md, docs/decisions.md, docs/07-release.md]
      contracts:
        reads: []
        writes: []
      exclusive_resources: [keel-development]
      parallel: serial
      serial_reason: the release candidate and the one test lane
---

# Sprint 13 — 7.1.0 carries the next-version work

- Acceptance: readme.txt has no Unreleased section and every line of it under == 7.1.0 ==, once; develop contains work/after-7.1.0; the full suite is green on the candidate. Hours are AI working time plus supervision.
