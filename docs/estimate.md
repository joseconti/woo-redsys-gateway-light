# Estimate — Payment Gateway for Redsys & WooCommerce Lite

> AI working hours + vibe-coder supervision hours — never traditional human development time (SKILL.md, "EVERY time figure is AI development time"). No client budget (D-010): this is internal-only.

## Adoption (this pass)
- AI hours: inventory + 4 asked-question batches + reconstructing `docs/01-discovery.md` through `docs/04-adoption-audit.md`, `docs/keel-conformance.md`, `docs/threat-model.md`, `docs/architecture.md`, `docs/security.md`, `docs/accessibility.md`, `README.md`, plus the license reconciliation and reference-material relocation — roughly one focused AI working session.
- Supervision hours: answering the 6 batched questions, reviewing this output — under an hour.

## Future work (not yet scoped)
No firm estimate exists yet for: triaging/fixing the 8 open GitHub issues, building the Phase 5 test/scaffold infrastructure, or any new feature. Each gets its own estimate per `references/estimation-budget.md` when scoped — proposed as the natural next step once the deferred-scaffold decision (see `docs/PROGRESS.md` open items) is made.

## Estimate v2 — 2026-10-06, re-based on the sprint plan
The plan in `docs/sprints/` now holds all work, each slice with its hours, so the Phase 5 figure is no longer a separate estimate: it is the sum of the plan's slice hours, as written by `scripts/keel-plan build` into `docs/.keel/plan.json` (`totals.estimated_hours`). This supersedes "Future work (not yet scoped)" above for everything the plan now covers.

- Phase 5 hours: 50.25 h (sum of the slice hours of sprints 1 to 3; contingency and `docs/sprints/deferred.md` not included)

Of these, 20 h are sprint 1, a backfilled estimate of the work done before the plan existed (no clock measured it); 9.75 h are sprint 2 (the Keel v6.5.0 reconciliation) and 20.5 h are sprint 3 (release preparation; 4.25 h of them added on 2026-10-06 by the security audit: nine fix slices and the scoped re-audit, D-056). When a slice is added, dropped or re-estimated, this figure changes with it in the same commit.
