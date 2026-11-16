---
schema: keel.sprint/2
sprint: 15
goal: Keel v7.5.0 → v7.6.0 reconciliation, and the follow-ups that 7.1.0's publication on WordPress.org (2026-11-13) unblocked
status: in-progress
scope_reconciliations:
  - id: R15-01
    date: 2026-11-16
    reason: Keel v7.6.0 running against a v7.5.0 baseline (mandatory reconciliation); 7.1.0 found published (origin/master 622d01f, tag v7.1.0, WordPress.org serves 7.1.0) — S-108 and S-109 added
    slices: [S-108, S-109]
    remaining_before: 1
    completed_residual: 0
    added_scope: 2
    explicit_residual_reestimates: 0
    remaining_after: 3
slices:
  - id: S-108
    title: Keel v7.5.0 → v7.6.0 reconciliation — embedded copies and lock stamp, keel-continue rests on an idle queue (queue.workable, idle-links breaker, exec claude), keel-chain-check row 7c, plan.json carries queue and waiting_on, machine-recipe standing check, conformance sweep
    status: in-progress
    original_hours: 1.5
    actual_hours: 0
    actual_source: measured
    residual_hours: 1.5
    change_kind: added
    parent_slice: null
    owns: [S-108]
    estimate_changes: []
    depends_on: []
    waiting_on: null
    criteria: []
    execution:
      write_paths: [.claude/skills/keel/, .agents/skills/keel/, CLAUDE.md, AGENTS.md, scripts/keel-continue, scripts/keel-chain-check, scripts/keel-plan, scripts/keel-verify, docs/keel-conformance.md, docs/PROGRESS.md, docs/decisions.md, docs/sprints/]
      contracts:
        reads: []
        writes: []
      exclusive_resources: []
      parallel: serial
      serial_reason: bookkeeping and Keel scaffolding, coordinator-owned
  - id: S-109
    title: 7.1.0 published — inbound issue sweep, reporter replies where the fix is testable in 7.1.0, docs/issues.md and PROGRESS brought in line (Phase 7 of 7.1.0 done, project in maintenance)
    status: in-progress
    original_hours: 0.5
    actual_hours: 0
    actual_source: measured
    residual_hours: 0.5
    change_kind: added
    parent_slice: null
    owns: [S-109]
    estimate_changes: []
    depends_on: []
    waiting_on: null
    criteria: []
    execution:
      write_paths: [docs/issues.md, docs/PROGRESS.md, docs/07-release.md, docs/decisions.md]
      contracts:
        reads: []
        writes: []
      exclusive_resources: []
      parallel: serial
      serial_reason: bookkeeping, coordinator-owned
---

# Sprint 15 — Keel v7.6.0 and the 7.1.0 follow-ups

- Acceptance: `Keel baseline: v7.6.0` with the conformance sweep complete and every standing check passing; `docs/issues.md` and `docs/PROGRESS.md` describe 7.1.0 as published. Hours are AI working time plus supervision.
- Progress (2026-11-16 19:30): S-108 — copies, lock stamp, launcher REST path, chain-check 7c and plan queue done and smoke-proven (843bd73, 84da8fa); left: the docs/machines/ refresh to keel-base-rev 2026-11-12-1 (checked against the VM), the keel-verify drift check, the conformance sweep and the baseline. S-109 — 7.1.1 published by the owner, GitHub release v7.1.1 created on request (D-117, 792c39f); left: the issue sweep and the replies on #112 and #93, docs/issues.md.
