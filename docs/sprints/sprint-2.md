---
schema: keel.sprint/1
sprint: 2
goal: Keel v5.9.0 to v6.5.0 post-update reconciliation
status: in-progress
slices:
  - id: S-020
    title: Install Keel v6.5.0 (embedded trees, lock block) and run the conformance sweep
    status: done
    hours: 0.5
    actual_hours: 0.5
    actual_source: estimated
    depends_on: []
    criteria: []
  - id: S-021
    title: Session clock and sprint plan tooling (keel-time, keel-plan, sessions.md, plan.json, sprint 1 backfill)
    status: in-progress
    hours: 1.5
    actual_hours: null
    actual_source: estimated
    depends_on: [S-020]
    criteria: []
  - id: S-022
    title: Chaining family (keel-continue regenerated, keel-close, keel-chain-check, keel-stop-hook, keel-session-pid, keel-tools, post-commit hook)
    status: in-progress
    hours: 2.5
    actual_hours: null
    actual_source: estimated
    depends_on: [S-020]
    criteria: []
  - id: S-023
    title: Test selection (keel-affected-tests, pre-push hook), doctor rows, environment and driver sections of the plan
    status: in-progress
    hours: 1.25
    actual_hours: null
    actual_source: estimated
    depends_on: [S-020]
    criteria: []
  - id: S-024
    title: Native Claude Code config package (rules, agents, confidential-data pre-commit gate, shared allow-list)
    status: in-progress
    hours: 1
    actual_hours: null
    actual_source: estimated
    depends_on: [S-020]
    criteria: []
  - id: S-025
    title: Documentation backfill (AC ids, flows, usage, hooks reference)
    status: in-progress
    hours: 1.5
    actual_hours: null
    actual_source: estimated
    depends_on: [S-020]
    criteria: []
  - id: S-026
    title: keel-verify regenerated, one-time verifications, card lines, decisions, conformance closed, baseline advanced
    status: not-started
    hours: 1.5
    actual_hours: null
    actual_source: estimated
    depends_on: [S-021, S-022, S-023, S-024, S-025]
    criteria: []
---

# Sprint 2 — Keel v5.9.0 to v6.5.0 post-update reconciliation

- Acceptance: every row of `docs/keel-conformance.md` is `present`, `declined` with its D-entry, `n/a` with its condition, or scheduled as a named slice by a recorded user decision; `scripts/keel-verify` runs green; `Keel baseline:` reads v6.5.0. No product code changes.
- Notes: ordered by the user on 2026-10-06 ("full reconciliation"). The competitive scan is declined (D-035); the website rows are n/a (D-005). Hours are AI working time plus supervision. The first part of the session ran before `scripts/keel-time` existed, so actuals in this sprint are labelled `estimated` unless the clock covered the whole slice.
- Close-out:
