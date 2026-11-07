---
schema: keel.sprint/2
sprint: 11
goal: Move the playground from wp-env/Docker to the shared Parallels VM keel-development (D-114)
status: in-progress
scope_reconciliations:
  - id: R11-01
    date: 2026-11-07
    reason: D-114 — the owner approved the migration; S-104 added
    slices: [S-104]
    remaining_before: 1
    completed_residual: 0
    added_scope: 3
    explicit_residual_reestimates: 0
    remaining_after: 4
slices:
  - id: S-104
    title: Playground on parallels-shared — scripts/keel-site for the wrgl (floor) and wrgl-ceiling sites, the test runner, doctor, setup, tunnel and Playwright moved off wp-env, full suite on both sites compared with Docker, then this project's Docker resources removed; the Lite plugin never shares a site with the premium plugin
    status: in-progress
    original_hours: 3
    residual_hours: 3
    change_kind: added
    parent_slice: null
    owns: [S-104]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [scripts/, playwright.config.js, tests/, docs/, .gitattributes]
      contracts:
        reads: []
        writes: []
      exclusive_resources: [keel-development]
      parallel: serial
      serial_reason: one shared VM, one test lane
---

# Sprint 11 — Playground on the shared Parallels VM

- Acceptance: `scripts/keel-site` drives both sites; `scripts/keel-affected-tests --run` runs unit, integration and e2e against the VM with no Docker; the full suite on the floor site is as green as it was on wp-env (226 of 226 on 2026-11-06); `scripts/keel-doctor --check` has no wp-env row; no site that carries this plugin has `woocommerce-gateway-redsys` installed; this project's Docker resources removed by name. Hours are AI working time plus supervision.
- Close-out:
