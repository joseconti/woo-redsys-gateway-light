# Keel conformance

Swept 2026-11-06 against `MANIFEST.md` v7.5.0 (`.claude/skills/keel/MANIFEST.md`), baseline v7.3.2 → v7.5.0 (reconciliation D-112, sprint 10, S-103).

Sources used, and only these: the disk, `MANIFEST.md` (Table 1 in full, Table 3 rows v7.4.0 and v7.5.0) and `docs/decisions.md`. The previous sweep was not read.

Position at the sweep: Phase 5 scaffold complete, Phase 6 in progress, Phase 7 release 7.1.0 candidate on `develop` waiting on the owner. Phases 3/4 n/a (no design contract), Phase 8 n/a (D-005). Card: `Sprints: on`, `Chaining: start`, `Parallel development: auto; max_workers: max`, `Security audit: required`, `Client budget: no`, `User guide:` declined (D-013), `Assistant config:` full for Claude only (D-039, D-043).

## Table 1 — what the project must contain

| ID | Requirement | Required from | State | Where / condition |
|---|---|---|---|---|
| T1-01 | `docs/PROGRESS.md` | Phase 1 step 0a | present | `docs/PROGRESS.md` with project card, phase status, position |
| T1-02 | `docs/decisions.md` | Phase 1 step 0a | present | `docs/decisions.md` (D-001 … D-112) |
| T1-03 | `docs/lessons-learned.md` | Phase 1 step 0a | present | `docs/lessons-learned.md` |
| T1-04 | `docs/sessions.md` | Phase 1 step 0a | present | `docs/sessions.md` (`Sprints: on`) |
| T1-05 | `scripts/keel-time` | Phase 1 step 0a / v6.0.0 reconciliation | present | `scripts/keel-time` |
| T1-06 | `scripts/keel-clock` | With keel-time | present | `scripts/keel-clock` |
| T1-07 | `docs/.keel/clock.jsonl` (gitignored) | First keel-time start | present | `docs/.keel/clock.jsonl` on disk, ignored by `.gitignore` line 56 |
| T1-08 | Off-machine durability (Git remote) | Phase 1 step 0a | present | `origin` → github.com/joseconti/woo-redsys-gateway-light; card line `Durability:` |
| T1-09 | Clean working tree at every block close, work on `develop` | Phase 1 step 0a | present | branch `develop` exists and is published; last block close (5417086) committed; this reconciliation's changes are committed at its own close |
| T1-10 | `CLAUDE.md` + `AGENTS.md` lock | Phase 1 step 0a | present | `CLAUDE.md` and `AGENTS.md` line 1, `KEEL:BEGIN — v7.5.0`, cleanup contract included |
| T1-11 | Gemini CLI lock mirror (GEMINI.md or context.fileName) | Phase 1 step 0a | n/a | the user does not work with Gemini CLI — single assistant, Claude Code only (D-008) |
| T1-12 | `.claude/skills/keel/` + `.agents/skills/keel/` | Phase 1 step 0a | present | both trees at v7.5.0, identical; card line `Keel portability:` |
| T1-13 | docs/00-competitive-landscape.md | Phase 1 step 0 | declined | competitive scan declined explicitly by the user (D-035) |
| T1-14 | `docs/01-discovery.md` | Phase 1 | present | `docs/01-discovery.md` (adopted as-built) |
| T1-15 | `docs/estimate.md` | Phase 1 close | present | `docs/estimate.md` |
| T1-16 | `docs/token-ledger.md` | Phase 1 close | present | `docs/token-ledger.md` |
| T1-17 | `docs/02-functional-spec.md` | Phase 2 | present | `docs/02-functional-spec.md` |
| T1-18 | `docs/03-technical-plan.md` | Phase 2 | present | `docs/03-technical-plan.md` |
| T1-19 | `docs/threat-model.md` | Phase 2 | present | `docs/threat-model.md` |
| T1-20 | `docs/flows/` | Phase 2 | present | `docs/flows/` |
| T1-21 | docs/budget.md | Phase 2 close | n/a | `Client budget: no` (D-010) |
| T1-22 | docs/spec-references/ | Phase 2 | n/a | the spec records no `## Reference artifacts` section |
| T1-23 | docs/rubrics/ | Phase 2 | n/a | no rubric domain was accepted (none recorded in the spec or decisions) |
| T1-24 | docs/design/references/ | Phase 2 | n/a | no design references held — pre-existing UI, no design contract (Phases 3/4 n/a, D-009) |
| T1-25 | Assistant rules, one container per accepted tool | Phase 2 close | present | `.claude/rules/` (code-style, docs-discipline, security); only Claude accepted (D-039) |
| T1-26 | Assistant subagents, per capable tool | Phase 2 close | present | `.claude/agents/` (6 agents) (D-039, D-043) |
| T1-27 | docs/design/DESIGN-BRIEF.md | Phase 3 | n/a | Phase 3 n/a — pre-existing UI, no design contract (D-009) |
| T1-28 | docs/design/design-handoff/ | Phase 4 start | n/a | Phase 4 n/a — no design contract (D-009) |
| T1-29 | docs/BUILD-SPEC.md | Phase 4 | n/a | Phase 4 n/a — no design contract (D-009) |
| T1-30 | docs/design/design-requests/ | Phase 4 | n/a | no Design Request has ever appeared (Phases 3/4 n/a) |
| T1-31 | `.gitignore` + `.gitattributes` | Phase 5 scaffold | present | both at the repo root |
| T1-32 | `docs/sprints/` | Phase 5 §0 | present | `docs/sprints/` sprint-1 … sprint-10 (`Sprints: on`) |
| T1-33 | `docs/sprints/deferred.md` | With the first sprint file | present | `docs/sprints/deferred.md` |
| T1-34 | `docs/.keel/plan.json` | With the first sprint file | present | `docs/.keel/plan.json` |
| T1-35 | `scripts/keel-parallel` | Phase 5 scaffold / reconciliation | present | `scripts/keel-parallel` = v7.5.0 `keel_parallel.py` (`Parallel development: auto`, Python 3 available) |
| T1-36 | docs/.keel/parallel/ | First parallel run | n/a | `Parallel development: auto`, but no parallel run has happened yet (none recorded) |
| T1-37 | docs/.keel/slices/run/slice/attempt.json | First writing-worker result | n/a | no parallel writing worker has ever run |
| T1-38 | `docs/05-test-points.md` | Phase 5 | present | `docs/05-test-points.md` |
| T1-39 | `docs/api/INDEX.md` | Phase 5 first slice | present | `docs/api/INDEX.md` |
| T1-40 | `docs/keel-conformance.md` | Phase 1 step 0a / every reconciliation | present | this file, rewritten by this sweep |
| T1-41 | `docs/playground.md` | Phase 5 scaffold | present | `docs/playground.md` (project can be run: wp-env) |
| T1-42 | `scripts/keel-verify` | Phase 5 scaffold | present | `scripts/keel-verify` |
| T1-43 | `scripts/playground-tunnel` | Phase 5 scaffold | present | `scripts/playground-tunnel` — Redsys/Inespay notifications call the plugin from outside |
| T1-44 | scripts/keel-site | Phase 5 scaffold | present | `scripts/keel-site` — sites `wrgl` and `wrgl-ceiling` on keel-development (D-114) |
| T1-45 | `scripts/keel-affected-tests` | Phase 5 scaffold | present | `scripts/keel-affected-tests` (PHPUnit + Playwright suite) |
| T1-46 | `.githooks/pre-push` + core.hooksPath | Phase 5 scaffold | present | `.githooks/pre-push`; `core.hooksPath` set to the repository's `.githooks` |
| T1-47 | `scripts/keel-doctor` | Phase 5 scaffold | present | `scripts/keel-doctor` |
| T1-48 | Build/minify script (name per technical plan) | Phase 5 scaffold | present | `bin/build-assets.js` driven by `package.json` (`build:css`, `check:css`) plus `wp-scripts build` |
| T1-49 | `scripts/keel-handoff-verify` | Phase 5 scaffold | present | `scripts/keel-handoff-verify` |
| T1-50 | Single-lane lock taken by keel-handoff-verify | Phase 5 scaffold | present | lane logic inside `scripts/keel-handoff-verify` (`Chaining: start`) |
| T1-51 | `scripts/keel-tools/` row file per accepted assistant | Phase 5 scaffold | present | `scripts/keel-tools/claude.sh` + `scripts/keel-tools/claude.doctor.sh` |
| T1-52 | `scripts/keel-continue` | Phase 5 scaffold | present | `scripts/keel-continue` |
| T1-53 | `scripts/keel-close` | Phase 5 scaffold | present | `scripts/keel-close` |
| T1-54 | `.githooks/post-commit` + core.hooksPath | Phase 5 scaffold | present | `.githooks/post-commit`; `core.hooksPath` set |
| T1-55 | `scripts/keel-stop-hook` + Stop hook registration | Phase 5 scaffold | present | `scripts/keel-stop-hook`, registered under `Stop` in `.claude/settings.json` |
| T1-56 | `scripts/keel-session-pid.sh` | Phase 5 scaffold | present | `scripts/keel-session-pid.sh` |
| T1-57 | `scripts/keel-chain-check` | Phase 5 scaffold | present | `scripts/keel-chain-check` |
| T1-58 | `Chaining model:` card line | Phase 1 step 0a | present | card: `Chaining model:` opus (D-037) |
| T1-59 | `Chain verified:` card line | Phase 5 scaffold | present | card: `Chain verified:` 2026-10-06, tier start |
| T1-60 | `.githooks/pre-commit` | Phase 5 scaffold | present | `.githooks/pre-commit` (confidential-data gate, D-039) |
| T1-61 | Permission allow-lists per capable tool | Phase 5 scaffold | present | `.claude/settings.json` (confirmed by the user, D-043) |
| T1-62 | CI workflow | Phase 5 scaffold | declined | forge CI not accepted (D-039, D-043) |
| T1-63 | MCP registration per capable tool | Phase 5 scaffold | n/a | the technical plan defines no development MCP servers (card `Assistant config:`) |
| T1-64 | `docs/architecture.md` | Phase 6 | present | `docs/architecture.md` |
| T1-65 | `docs/api/`, `docs/usage/`, `docs/reference/` | Phase 6 | present | all three directories exist (Phase 6 in progress) |
| T1-66 | `docs/security.md` | Phase 6 | present | `docs/security.md` |
| T1-67 | `docs/accessibility.md` | Phase 6 | present | `docs/accessibility.md` |
| T1-68 | `README.md` | Phase 6 | present | `README.md` at the repo root |
| T1-69 | guide/ | Phase 6 | declined | end-user guide declined for now (D-013) |
| T1-70 | guide/_theme/ + guide/brand/ + keel-docs-theme meta | Phase 6 | declined | no guide (D-013); card `Docs theme:` n/a |
| T1-71 | `docs/07-release.md` | Phase 7 | present | `docs/07-release.md` (7.1.0 candidate) |
| T1-72 | `docs/security-audit.md` | Phase 7 | present | `docs/security-audit.md` (`Security audit: required`, D-040) |
| T1-73 | docs/security-audit/ listed in `.gitignore` | First audit run | present | `.gitignore` line 59 |
| T1-74 | site-docs directory | Phase 8 | n/a | no website intent (D-005) |
| T1-75 | site-docs art-direction.md | Phase 8 design handoff | n/a | no website intent (D-005) |
| T1-76 | ~/.keel/art-ledger.md | Phase 8 art direction close | n/a | no website intent (D-005) |
| T1-77 | site-docs launch-report.md | Phase 8 launch | n/a | no website intent (D-005) |
| T1-78 | site-docs operations.md | Phase 8 launch | n/a | no website intent (D-005) |
| T1-79 | docs/.keel/e2e-status.json | Phase 5 onward | n/a | the project card carries no `E2E:` line |
| T1-80 | docs/.keel/e2e-history.jsonl | Alongside the status file | n/a | optional, and there is no `E2E:` card line |
| T1-81 | `docs/issues.md` | First forge contact | present | `docs/issues.md` (GitHub issues accessed) |
| T1-82 | docs/old/ | First sprint close | n/a | archiving has not started |
| T1-83 | `docs/04-adoption-audit.md` | Adoption step 5 | present | `docs/04-adoption-audit.md` (adopted project) |
| T1-84 | env CLAUDE_CODE_EXPERIMENTAL_AGENTS_TEAMS = 1 in .claude/settings.local.json | Session-start setup | present | machine-local settings.local.json of the main checkout, line 21 (gitignored, not in this worktree) |
| T1-85 | `Parallel development:` auto; max_workers: max on the card | Session-start setup / reconciliation | present | card: `Parallel development:` auto; max_workers: max (D-093) |

## Table 3 — per-version actions, v7.3.2 → v7.5.0

| ID | Action | Version | State | Where / condition |
|---|---|---|---|---|
| T3-740-1 | Copy `keel_parallel.py` over `scripts/keel-parallel` (new reap-worktrees) | v7.4.0 | present | `scripts/keel-parallel` replaced with the v7.5.0 copy (superset), D-112 |
| T3-740-2 | Add card line `Playground host:` filled from detection | v7.4.0 | present | card: `Playground host:` parallels-shared (D-112, D-114) |
| T3-740-3 | Parallels migration row (prlctl works, playground on Docker) | v7.4.0 | present | accepted by the owner and done 2026-11-07 (D-114, S-104); this project's Docker resources removed by name |
| T3-740-4 | Standing check 3: card line, migration row, reap-worktrees once per repository | v7.4.0 | present | card line present; migration row raised (T3-740-3); reap-worktrees ran 2026-11-06, nothing to retire (D-112) |
| T3-740-5 | scripts/keel-site for parallels-shared; partner sites in the technical plan | v7.4.0 | present | `scripts/keel-site` (D-114); no partner sites — the plugin calls nothing else on the VM, and the premium plugin's tenant `redsys` is deliberately separate |
| T3-750-1 | Refresh the lock block (cleanup contract) | v7.5.0 | present | `CLAUDE.md` + `AGENTS.md` stamped v7.5.0 with the cleanup contract (D-112) |
| T3-750-2 | Copy `keel_parallel.py` over `scripts/keel-parallel` (sweep, sweep-hook, sweep-install) | v7.5.0 | present | `scripts/keel-parallel` = v7.5.0 (D-112) |
| T3-750-3 | Lease on each of this project's own Parallels/Docker machines, or propose deleting unneeded ones | v7.5.0 | n/a | the project currently owns no Docker containers or Parallels VMs |
| T3-750-4 | Run sweep-install once per machine and register the session-start command | v7.5.0 | present | already registered on this machine: a SessionStart hook runs the Keel sweep |
