# Keel conformance — Payment Gateway for Redsys & WooCommerce Lite

> Sweep of 2026-10-21 · Keel v7.3.2 · commit `704730a` (branch `develop`) · position: Phase 7 release gate for 7.1.0 in progress (S-032) · **totals: 137 rows — 102 present · 0 missing · 9 declined · 26 n/a** (Table 1: 84 `T1-nn` rows, 4 declined sub-rows, 15 card-line rows; Table 3: 34 rows)
>
> Sources, and only these: the disk of this repository, `.claude/skills/keel/MANIFEST.md` (Tables 1 and 3) and `docs/decisions.md`. Conditions from the project card: Website intent no (D-005), Client budget no (D-010), User guide declined (D-013), Assistant config full for claude only, CI not accepted (D-039), MCP n/a, Security audit required (D-040), Parallel development auto; max_workers: max (D-093), Chaining start (D-015), Sprints on, adopted project, no `E2E:` line. Phases 3 and 4 n/a (pre-existing UI, no design contract).

## Table 1 — project inventory

| Id | Requirement | State | Evidence / decision / excluding condition |
|---|---|---|---|
| T1-01 | `docs/PROGRESS.md` | present | `docs/PROGRESS.md` |
| T1-02 | `docs/decisions.md` | present | D-001 … D-099 |
| T1-03 | `docs/lessons-learned.md` | present | `docs/lessons-learned.md` |
| T1-04 | `docs/sessions.md` | present | rows through 2026-10-21, v7.3.1 columns present (D-094) |
| T1-05 | `scripts/keel-time` | present | executable (D-094) |
| T1-06 | `scripts/keel-clock` (byte copy of `keel_clock.py`) | present | `cmp` identical to `.claude/skills/keel/scripts/keel_clock.py` (D-094) |
| T1-07 | `docs/.keel/clock.jsonl` (gitignored) | present | on disk, untracked, listed in `.gitignore` |
| T1-08 | Off-machine durability | present | `origin` → github.com/joseconti/woo-redsys-gateway-light; card `Durability:` |
| T1-09 | A clean working tree at every block close | present | `git status` clean at `704730a`, work on `develop` |
| T1-10 | `CLAUDE.md` + `AGENTS.md` portability lock | present | both stamped `KEEL:BEGIN — v7.3.2`, blocks identical (D-093) |
| T1-11 | Gemini CLI lock mirror | n/a | Gemini CLI not in use (card: tools claude) |
| T1-12 | Embedded skill trees (claude + agents) | present | `.claude/skills/keel/` and `.agents/skills/keel/` v7.3.2, `diff -rq` empty (D-093) |
| T1-13 | Competitive landscape document | declined | D-035 |
| T1-14 | `docs/01-discovery.md` incl. Environment & test drivers | present | §"Environment & test drivers (step 5a preflight)" |
| T1-15 | `docs/estimate.md` | present | `docs/estimate.md` |
| T1-16 | `docs/token-ledger.md` | present | rows through 2026-10-21 |
| T1-17 | `docs/02-functional-spec.md` with AC-nn IDs | present | `docs/02-functional-spec.md` |
| T1-18 | `docs/03-technical-plan.md` incl. Environment requirements | present | code map with markers; §"Environment requirements"; §"Inbound calls" |
| T1-19 | `docs/threat-model.md` | present | delivery states + "Not defended" table |
| T1-20 | `docs/flows/` | present | 6 flow files |
| T1-21 | Client budget document | n/a | `Client budget: no` (D-010) |
| T1-22 | Spec reference artifacts directory | n/a | the spec records no reference artifacts |
| T1-23 | Rubrics directory | n/a | no rubric domain accepted |
| T1-24 | Design references directory | n/a | the user holds no visual references (no design system, D-009) |
| T1-25 | Assistant rules `.claude/rules/` | present | code-style, docs-discipline, security (D-039) |
| T1-26 | Assistant subagents `.claude/agents/` | present | 6 agents (D-039, D-043) |
| T1-27 | Design brief | n/a | Phase 3 n/a — pre-existing UI, no design contract |
| T1-28 | Design handoff directory | n/a | Phase 4 n/a — pre-existing UI, no design contract |
| T1-29 | Build spec | n/a | Phase 4 n/a — pre-existing UI, no design contract |
| T1-30 | Design Request register | n/a | no Design Request has appeared |
| T1-31 | `.gitignore` + `.gitattributes` | present | mandatory ignores incl. `CLAUDE.local.md`, `docs/.keel/clock.jsonl`, `.keel-update-check`, `docs/continuation-prompt.md`, `docs/.keel/parallel/`; `export-ignore` boundary (S-032) |
| T1-32 | `docs/sprints/` | present | sprint-1, sprint-2 (`keel.sprint/1`), sprint-3 (`keel.sprint/2`, D-095) |
| T1-33 | `docs/sprints/deferred.md` | present | `keel.deferred/1`; holds S-030, S-090 (D-099) |
| T1-34 | `docs/.keel/plan.json` | present | `keel.plan/2` from `scripts/keel-plan` (D-094) |
| T1-35 | `scripts/keel-parallel` (byte copy) | present | `cmp` identical to `keel_parallel.py` (D-095) |
| T1-36 | Parallel coordinator journal directory | n/a | no parallel run yet — serial fallback, effective cap 1 (D-095); ignore line in place |
| T1-37 | Committed worker-result reports | n/a | no parallel writing worker has run (D-095) |
| T1-38 | `docs/05-test-points.md` with Criterion / Coverage / Red first | present | header row of `docs/05-test-points.md` |
| T1-39 | `docs/api/INDEX.md` | present | `docs/api/INDEX.md` |
| T1-40 | `docs/keel-conformance.md` | present | this file |
| T1-41 | `docs/playground.md` | present | `last verified: 2026-10-06`; tunnel section (D-096) |
| T1-42 | `scripts/keel-verify` | present | `scripts/keel-verify` |
| T1-43 | `scripts/playground-tunnel` | present | D-096 — payment notifications are external callers |
| T1-44 | `scripts/keel-affected-tests` | present | `scripts/keel-affected-tests` |
| T1-45 | `.githooks/pre-push` + `core.hooksPath` | present | `core.hooksPath` = `<repo>/.githooks` |
| T1-46 | `scripts/keel-doctor` | present | plus `scripts/keel-tools/claude.doctor.sh` |
| T1-47 | Build/minify script (name per the technical plan) | present | `bin/build-assets.js` + `npm run build:assets`, as named in `docs/03-technical-plan.md` (D-054) |
| T1-48 | `scripts/keel-handoff-verify` | present | plus its allow-list entry in `.claude/settings.json` |
| T1-49 | Single-lane lock (`Chaining: start`) | present | implemented in `scripts/keel-handoff-verify` ("Single-lane lock", `--release`) |
| T1-50 | Tool registry row `scripts/keel-tools/claude.sh` | present | `scripts/keel-tools/claude.sh` |
| T1-51 | `scripts/keel-continue` | present | plus allow-list entry |
| T1-52 | `scripts/keel-close` | present | plus allow-list entry |
| T1-53 | `.githooks/post-commit` + `core.hooksPath` | present | deletes only the hand-off; observed on a real commit (D-048) |
| T1-54 | `scripts/keel-stop-hook` + Stop registration | present | `.claude/settings.json` `hooks.Stop`; real blocks observed (D-048), and on 2026-10-21 ~14:43 a fresh session's turn was blocked on an unpushed commit (S-037) |
| T1-55 | `scripts/keel-session-pid.sh` | present | sourced |
| T1-55a | Tunnel proven by a real provider payment through it | declined | D-099 — acknowledged for 7.1.0; S-090 in `deferred.md` (D-096) |
| T1-56 | `scripts/keel-chain-check` | present | plus allow-list entry; `--smoke` observed (D-048) |
| T1-57 | Chaining model card line | present | `Chaining model: opus` (D-037) |
| T1-58 | Chain verified card line | present | 2026-10-06, tier start, Keel 6.5.0, checksum `b8796f66873c0ff2` matches `scripts/keel-continue` (D-093, D-097) |
| T1-59 | `.githooks/pre-commit` | present | confidential-data gate, active via `core.hooksPath` |
| T1-60 | Permission allow-list `.claude/settings.json` | present | D-043 |
| T1-61 | CI workflow | declined | D-039 (card `CI runs on: n/a`) |
| T1-62 | MCP registration | n/a | the technical plan defines no development MCP servers (D-039) |
| T1-63 | `docs/architecture.md` | present | `docs/architecture.md` |
| T1-64 | `docs/api/`, `docs/usage/`, `docs/reference/` | present | all three directories |
| T1-65 | `docs/security.md` | present | `docs/security.md` |
| T1-66 | `docs/accessibility.md` | present | automated pass recorded (S-029, S-056, D-067) |
| T1-66a | Allow-list entry for running the Stop hook by hand | declined | D-048 (with D-043) — the harness invokes the hook |
| T1-67 | `README.md` | present | `README.md` |
| T1-68 | End-user guide directory | declined | D-013 (`User guide: declined for now`) |
| T1-69 | Guide theme, brand layer and marker | n/a | no guide (D-013); card `Docs theme: n/a` |
| T1-70 | `docs/07-release.md` | present | being completed in this gate (S-032, in progress; version approved D-099) |
| T1-71 | `docs/security-audit.md` | present | runs of 2026-10-06; card `Security audit: required` (D-040) |
| T1-72 | `docs/security-audit/` listed in `.gitignore` | present | `.gitignore` line `docs/security-audit/` |
| T1-73 | Phase 8 site docs set | n/a | Website intent no (D-005) |
| T1-74 | Site art direction | n/a | Website intent no (D-005) |
| T1-75 | Cross-project art ledger | n/a | Website intent no (D-005) |
| T1-76 | Site launch report | n/a | Website intent no (D-005) |
| T1-77 | Site operations document | n/a | Website intent no (D-005) |
| T1-78 | E2E status file | n/a | the card carries no `E2E:` line |
| T1-78a | Guided assistive-technology pass | declined | D-099 — accepted shortfall for 7.1.0; S-030 in `deferred.md`; AC-48 `ASSISTIVE-TECH` |
| T1-79 | E2E history file | n/a | the card carries no `E2E:` line (optional even where it exists) |
| T1-80 | `docs/issues.md` with Last inbound sweep | present | sweep 2026-10-21 13:45 |
| T1-81 | Archive directory | n/a | archiving has not started |
| T1-82 | `docs/04-adoption-audit.md` | present | adopted project |
| T1-83 | Agent teams flag in `.claude/settings.local.json` (standing check 1) | present | `env.CLAUDE_CODE_EXPERIMENTAL_AGENTS_TEAMS` = "1" (D-093) |
| T1-83a | SA-06, SA-13, SA-20 observations resolved | declined | D-099 — acknowledged, deferred after the release (S-090) |
| T1-84 | `Parallel development:` auto; max_workers: max (standing check 2) | present | card line (D-093) |
| T1-C1 | Project-card line `Keel portability:` | present | `docs/PROGRESS.md` project card |
| T1-C2 | Project-card line `Assistant config:` | present | `docs/PROGRESS.md` project card |
| T1-C3 | Project-card line `Models:` | present | `docs/PROGRESS.md` project card |
| T1-C4 | Project-card line `Keel baseline:` | present | `docs/PROGRESS.md` project card |
| T1-C5 | Project-card line `Client budget:` | present | `docs/PROGRESS.md` project card |
| T1-C6 | Project-card line `User guide:` | present | `docs/PROGRESS.md` project card |
| T1-C7 | Project-card line `Docs theme:` | present | `docs/PROGRESS.md` project card |
| T1-C8 | Project-card line `Chaining:` | present | `docs/PROGRESS.md` project card |
| T1-C9 | Project-card line `Issue sweep interval:` | present | `docs/PROGRESS.md` project card |
| T1-C10 | Project-card line `Test-first policy:` | present | `docs/PROGRESS.md` project card |
| T1-C11 | Project-card line `Sprints:` | present | `docs/PROGRESS.md` project card |
| T1-C12 | Project-card line `Push test scope:` | present | `docs/PROGRESS.md` project card |
| T1-C13 | Project-card line `Security audit:` | present | `docs/PROGRESS.md` project card |
| T1-C14 | Project-card line `Parallel development:` | present | `docs/PROGRESS.md` project card |
| T1-C15 | Project-card line `Durability:` | present | `docs/PROGRESS.md` project card |

## Table 3 — per-version actions v6.6.0 … v7.3.2 (baseline was v6.5.0; reconciled 2026-10-21, D-093 … D-097)

| Id | Action | State | Evidence / decision / excluding condition |
|---|---|---|---|
| T3-v6.6.0-1 | Migrate legacy hours to `original_hours` | present | D-094 |
| T3-v6.6.0-2 | Map ownership and estimate `residual_hours` | present | D-094 (plan `reconciled`) |
| T3-v6.6.0-3 | Regenerate keel-time, plan generator, keel-verify | present | D-094 |
| T3-v6.6.0-4 | Reconcile parent/child decompositions | present | D-094 (none existed) |
| T3-v6.6.0-5 | Restamp the lock | present | D-093 (v7.3.2) |
| T3-v7.0.0-1 | Add the Parallel development card line | present | D-093 |
| T3-v7.0.0-2 | Active sprint to `keel.sprint/2` | present | D-095 |
| T3-v7.0.0-3 | Copy keel-parallel, gitignore the journal | present | D-095 |
| T3-v7.0.0-4 | Probe the writing backend, record serial fallback | present | D-095 (effective cap 1) |
| T3-v7.0.0-5 | Worker-aware locks and hooks | present | D-095 |
| T3-v7.0.0-6 | Integrate only after validation and review | n/a | no worker has been integrated — serial fallback (D-095) |
| T3-v7.0.0-7 | Keep elapsed, worker effort and coordinator time separate | present | D-094, D-095 |
| T3-v7.0.0-8 | Restamp the lock | present | D-093 |
| T3-v7.1.0-1 | Static-analysis evidence classified PASS / FINDINGS / BLOCKED | n/a | forward evidence rule; no static-analysis run recorded since the reconciliation (D-097); applies at this gate's runs (S-032) |
| T3-v7.1.0-2 | Restamp the lock | present | D-093 |
| T3-v7.2.0-1 | Inventory inbound calls | present | `docs/03-technical-plan.md` §"Inbound calls" (D-096) |
| T3-v7.2.0-2 | Generate playground-tunnel, doctor row, playground section | present | D-096 |
| T3-v7.2.0-3 | Provider acceptance of the tunnel URL tested by hand once | declined | D-099 — deferred after the release (S-090; D-093, D-096) |
| T3-v7.2.0-4 | Re-examine reachability-only delegations through the tunnel | declined | D-099 — tunnel leg deferred (S-090; D-096) |
| T3-v7.2.0-5 | Every slice's new behaviour driven live in the playground | n/a | forward rule; no shipped-behaviour slice since the update (S-089 dev-dependencies only, D-098) |
| T3-v7.3.0-1 | Run the reconciliation | present | D-093, D-097 |
| T3-v7.3.0-2 | Standing check 1 — agent teams flag | present | D-093 |
| T3-v7.3.0-3 | Standing check 2 — max_workers: max | present | D-093 |
| T3-v7.3.0-4 | Effective cap resolved at kickoff | present | D-095 (sprint 3: cap 1, no verified backend) |
| T3-v7.3.0-5 | Restamp the lock | present | D-093 |
| T3-v7.3.1-1 | Copy keel-clock | present | D-094 |
| T3-v7.3.1-2 | Regenerate keel-time | present | D-094 |
| T3-v7.3.1-3 | New sessions.md columns | present | D-094 |
| T3-v7.3.1-4 | keel-verify accepts overlapping intervals | present | D-094 |
| T3-v7.3.2-1 | Copy keel-parallel with verify-batch | present | D-095 |
| T3-v7.3.2-2 | Worker test_commands only cheap static checks | n/a | no worker dispatched — serial fallback (D-095) |
| T3-v7.3.2-3 | Integration checks cheap; full suite once per batch | n/a | no worker dispatched — serial fallback (D-095) |
| T3-v7.3.2-4 | Worker-role branch of stop hook / controls | present | D-095 |
| T3-v7.3.2-5 | Refresh the lock stamp | present | D-093 |
