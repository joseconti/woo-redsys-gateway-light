---
schema: keel.sprint/2
sprint: 12
goal: Rebuild recipe for this project's machines and the shared VM keel-development (Keel machine recipe)
status: done
scope_reconciliations:
  - id: R12-01
    date: 2026-11-10
    reason: the owner asked that every machine be rebuildable from documents in each repository; S-105 added
    slices: [S-105]
    remaining_before: 1
    completed_residual: 0.5
    added_scope: 0.5
    explicit_residual_reestimates: 0
    remaining_after: 1
slices:
  - id: S-105
    title: docs/machines/ — this project's recipe for wrgl and wrgl-ceiling, plus the shared VM's recipe and provisioning scripts (base revision 2026-11-10-1) moved in from ~/Parallels/keel-development-kit
    status: done
    original_hours: 0.5
    actual_hours: 0.5
    actual_source: estimated
    residual_hours: 0
    change_kind: added
    parent_slice: null
    owns: [S-105]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [docs/machines/, docs/PROGRESS.md]
      contracts:
        reads: []
        writes: []
      exclusive_resources: [keel-development]
      parallel: serial
      serial_reason: documents one shared VM
---

# Sprint 12 — machine recipe

- Acceptance: a fresh clone carries everything needed to rebuild the two sites and the shared VM; nothing it needs lives in a temporary directory or only beside the VM. Hours are AI working time plus supervision.
- Close-out: S-105 done 2026-11-10 by a session working on Keel itself (the clock of this project was not read, so the hours are estimated). Written from the running VM; the recipe is not yet proven by a rebuild.
