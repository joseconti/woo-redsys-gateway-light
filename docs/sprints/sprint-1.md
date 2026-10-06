---
schema: keel.sprint/1
sprint: 1
goal: Backfill of the work done before the sprint plan existed (Keel adoption, test scaffold and coverage, security and correctness fixes)
status: done
slices:
  - id: S-001
    title: Embedded Keel skill updated from v1.11.0 to v5.9.0, lock blocks refreshed, session-setup batch answered (D-011, D-012)
    status: done
    hours: 1
    actual_hours: 1
    actual_source: estimated
    depends_on: []
    criteria: []
  - id: S-002
    title: Full Keel adoption with inventory, as-built docs, gap audit, license reconciliation and reference-material relocation (D-001 to D-010, D-013)
    status: done
    hours: 3
    actual_hours: 3
    actual_source: estimated
    depends_on: [S-001]
    criteria: []
  - id: S-003
    title: Phase 5 testability scaffold built and the playground verified for real (D-014)
    status: done
    hours: 1.5
    actual_hours: 1.5
    actual_source: estimated
    depends_on: [S-002]
    criteria: []
  - id: S-004
    title: Issue 93 fix (wc_enqueue_js deprecation) and triage of all 8 open GitHub issues
    status: done
    hours: 1
    actual_hours: 1
    actual_source: estimated
    depends_on: [S-003]
    criteria: []
  - id: S-005
    title: Chat chaining enabled with the single-lane lock and keel-continue (D-015)
    status: done
    hours: 1
    actual_hours: 1
    actual_source: estimated
    depends_on: [S-002]
    criteria: []
  - id: S-006
    title: RedsysLiteAPI unit tests with no WordPress bootstrap (D-016, D-017)
    status: done
    hours: 1
    actual_hours: 1
    actual_source: estimated
    depends_on: [S-003]
    criteria: []
  - id: S-007
    title: IPN and callback integration tests for the four gateways, including the Google Pay signature-bypass fix (D-018 to D-021)
    status: done
    hours: 3
    actual_hours: 3
    actual_source: estimated
    depends_on: [S-006]
    criteria: []
  - id: S-008
    title: Playwright checkout smoke test for the Redsys gateway (D-022)
    status: done
    hours: 1.5
    actual_hours: 1.5
    actual_source: estimated
    depends_on: [S-003]
    criteria: []
  - id: S-009
    title: Full-plugin review, its 9 bug fixes, the deferred plaintext-secret finding and the Unreleased changelog (D-023 to D-026, D-032)
    status: done
    hours: 3
    actual_hours: 3
    actual_source: estimated
    depends_on: [S-007]
    criteria: []
  - id: S-010
    title: The four remaining testability gaps closed (D-027 to D-031)
    status: done
    hours: 2.5
    actual_hours: 2.5
    actual_source: estimated
    depends_on: [S-008]
    criteria: []
  - id: S-011
    title: The 1 billion bug fixed, with the refund-time transient renewal (D-033)
    status: done
    hours: 1.5
    actual_hours: 1.5
    actual_source: estimated
    depends_on: [S-009]
    criteria: []
---

# Sprint 1 — Backfill of the work done before the sprint plan existed

**Every figure in this file is a backfilled estimate.** This work was done between 2026-07-31 and 2026-08-02, before `scripts/keel-time` and the sprint plan existed. No clock measured any of it, so every slice is `actual_source: estimated` and none of them counts toward the pace factor or the projection.

- Acceptance: the work recorded in `docs/PROGRESS.md` (phase status and current position), `docs/token-ledger.md` and D-001 to D-033 of `docs/decisions.md` is represented in the plan, one slice per coherent piece, so the plan holds all work and not only what came after it.
- Notes: `hours` is a retrospective estimate written on 2026-10-06 from the scope each decision entry and commit describes. `docs/token-ledger.md` records sessions, models and outcomes but no durations, and `docs/estimate.md` gave only "roughly one focused AI working session" for the adoption, so no source supports an actual that differs from the estimate. `actual_hours` is therefore set equal to `hours`, and the zero deviation this sprint shows is a consequence of that, not a finding. Hours are AI working time plus supervision. Ids S-012 to S-019 were reserved for this backfill and are unused. Test-point evidence for these slices is in `docs/05-test-points.md`.
- Close-out: all 11 slices shipped to `develop` (commits e3f43fb to b9c4707). Nothing moved to a later sprint from here; the fixes are unreleased, and releasing them is sprint 3. The two items consciously postponed during this work are in `deferred.md` (S-033, S-034).
