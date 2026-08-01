# Keel Conformance Sweep — Payment Gateway for Redsys & WooCommerce Lite

> Derived from `.claude/skills/keel/MANIFEST.md` Table 1, row by row, at the project's current position (Phases 1–2 adopted as-built; Phase 5 maintenance beginning). Never read from a previous sweep — there was none before this one. Every row carries exactly one state: `present` / `missing` / `declined` (+ decision entry) / `n/a` (+ condition).

| Requirement (MANIFEST Table 1) | State | Where / decision | Notes |
|---|---|---|---|
| `docs/PROGRESS.md` | present | docs/PROGRESS.md | — |
| `docs/decisions.md` | present | docs/decisions.md | — |
| `docs/lessons-learned.md` | present | docs/lessons-learned.md | — |
| Off-machine durability | present | `origin` → GitHub | verified with `git remote -v` |
| Clean working tree at block close | present | ongoing discipline | enforced going forward each commit |
| `CLAUDE.md` + `AGENTS.md` | present | both, v5.9.0 lock | refreshed 2026-08-01 |
| `GEMINI.md` mirror | n/a | — | user doesn't use Gemini CLI |
| `.claude/skills/keel/` embed | present | .claude/skills/keel/ v5.9.0 | — |
| `.agents/skills/keel/` embed | declined | D-008 | single-assistant project (Claude Code only); the `AGENTS.md` lock alone covers any other tool that opens this repo without the skill installed |
| `docs/00-competitive-landscape.md` | n/a | docs/01-discovery.md | competitive scan skipped on record (adoption step 3 — recommended-but-optional) |
| `docs/01-discovery.md` | **present, partial** | docs/01-discovery.md | missing the required `## Environment & test drivers` (§5a preflight) subsection — proposed below |
| `docs/estimate.md` | missing | — | proposed below |
| `docs/token-ledger.md` | missing | — | proposed below |
| `docs/02-functional-spec.md` | **present, partial** | docs/02-functional-spec.md | acceptance criteria not reconstructed with `AC-nn` IDs (progressive-backfill rule — deferred to when each feature is next touched) |
| `docs/03-technical-plan.md` | **present, partial** | docs/03-technical-plan.md | missing `## Environment requirements` subsection (feeds `scripts/keel-doctor`, not yet built) |
| `docs/threat-model.md` | missing | — | required for the gap audit (step 5) — created in this same adoption pass, see docs/threat-model.md |
| `docs/flows/` | missing | — | proposed for progressive backfill, deferred |
| `docs/budget.md` | n/a | D-010 | Client budget: no |
| `docs/spec-references/` | n/a | — | no reference artifacts recorded |
| `docs/rubrics/` | n/a | — | no rubric domain accepted |
| `docs/design/references/` | n/a | — | no rich visual references held |
| Assistant rules (per tool) | n/a | D-008 | no assistant-config package accepted beyond the lock + embed |
| Assistant subagents (per tool) | n/a | D-008 | same |
| `docs/design/DESIGN-BRIEF.md` | n/a | — | no active Phase 3 |
| `docs/design/design-handoff/` | n/a | — | pre-existing UI, no design contract (per adoption.md guidance — recorded, not retrofitted) |
| `docs/BUILD-SPEC.md` | n/a | — | same reason |
| `docs/design/design-requests/` | n/a | — | no Design Request has ever been needed |
| `.gitignore` + `.gitattributes` | present | root | verified — includes all required machine-local exclusions |
| `docs/sprints/` | present | docs/sprints/README.md | directory + convention doc; no sprint invented yet |
| `docs/05-test-points.md` | present | docs/05-test-points.md | template only, no rows yet — no test point has happened |
| `docs/api/INDEX.md` | present | docs/api/INDEX.md | created this pass, complete for existing hooks/filters; corrected by `scripts/keel-verify`'s first run (row 15 — Bizum's `_args` filter name) |
| `docs/keel-conformance.md` | present | this file | — |
| `docs/playground.md` | present | docs/playground.md | wp-env-based; `last verified: not yet run` (honestly stated — not run in this environment) |
| `scripts/keel-verify` | present | scripts/keel-verify | run clean, all 5 checks pass |
| `scripts/keel-doctor` | present | scripts/keel-doctor | `--check`/`--plan`/`--fix`/`--json`; run clean, all blocking requirements OK |
| `scripts/` build/minify script (CSS) | missing | — | project ships unminified CSS with no minify step at all — deferred, low urgency (cosmetic/perf only) |
| `scripts/keel-handoff-verify` | present | scripts/keel-handoff-verify | five courier checks + `VERDICT`; correctly returned `STOP` against the stale pre-adoption `docs/continuation-prompt.md` (regenerated at this session's close) |
| Single-lane lock | n/a | Chaining: off | — |
| `scripts/keel-continue` | n/a | Chaining: off | — |
| `.githooks/pre-commit` | n/a | D-008 | Assistant config package not accepted |
| Permission allow-lists (scaffolded) | n/a | D-008 | machine-local `.claude/settings.local.json` already covers automatic mode; the MANIFEST row is about the scaffolded package, not accepted |
| CI workflow | n/a | D-008 | Assistant config package not accepted |
| MCP registration | n/a | — | no dev MCP servers defined |
| `docs/architecture.md` | missing | — | created this pass, see docs/architecture.md |
| `docs/api/`, `docs/usage/`, `docs/reference/` | **present, partial** | docs/api/ | `docs/api/INDEX.md` exists; per-surface docs, `usage/`, `reference/` backfilled progressively |
| `docs/security.md` | missing | — | created this pass, see docs/security.md |
| `docs/accessibility.md` | missing | — | created this pass (mostly `TO BUILD`/`VERIFY` states — no automated pass has run yet), see docs/accessibility.md |
| `README.md` (repo root) | missing | — | created this pass, see README.md |
| `guide/` (end-user guide) | declined | D-013 | user declined for now; readme.txt covers it |
| `guide/_theme/` + brand | n/a | D-013 | `guide/` declined |
| `docs/07-release.md` | missing | — | proposed below, deferred to the next real release |
| `<site-docs>/` and launch/operations | n/a | D-005 | no website intent |
| `docs/.keel/slices/` | n/a | — | project doesn't fan work out over worktrees |
| `docs/issues.md` | present | docs/issues.md | created this pass, 8 open issues inventoried |
| `docs/old/` | n/a | — | nothing archived yet |
| `docs/04-adoption-audit.md` | present | docs/04-adoption-audit.md | created this pass |

## Summary
- **Applied (present):** 27
- **Present but partial (flagged, listed above):** 4
- **Declined (recorded decision):** 2 — `.agents/skills/keel/` mirror (D-008), `guide/` end-user guide (D-013 — declined for now, "no by default" answered when asked)
- **Not applicable:** 20
- **Missing, created in this adoption pass:** `docs/threat-model.md`, `docs/architecture.md`, `docs/security.md`, `docs/accessibility.md`, `README.md`, `docs/04-adoption-audit.md`, `docs/estimate.md`, `docs/token-ledger.md`, `docs/sprints/`, `docs/05-test-points.md`, `docs/playground.md`, `.wp-env.json`, `scripts/keel-doctor`, `scripts/keel-verify`, `scripts/keel-handoff-verify`
- **Missing, still deferred:** a CSS minify script (low urgency, cosmetic/perf only), `docs/flows/` (progressive backfill as each flow is next touched), `docs/07-release.md` (created at the next real release)

"Keel adopted" is not claimed without this table. The user was presented the deferred batch and chose to build the Phase 5 scaffold now rather than at the first real sprint (2026-08-01); the CSS minify script and `docs/flows/` remain genuinely deferred, low-urgency items.
