# Keel conformance sweep — Payment Gateway for Redsys & WooCommerce Lite

- Date: 2026-10-06
- Sweep: post-update reconciliation v5.9.0 → v6.5.0
- Sources (exactly three): the repository's disk; `MANIFEST.md` of Keel v6.5.0 (Table 1 and Table 3); `docs/decisions.md` (D-001…D-034). `docs/PROGRESS.md` lines 1–60 were read only for the project card, phase status and open items.
- The previous `docs/keel-conformance.md` was overwritten without being consulted.
- Nothing in this file has been applied. Every `missing` row awaits the user's decision; `Keel baseline:` stays v5.9.0 until the reconciliation completes.

Project conditions used (read from the card): WordPress plugin / WooCommerce extension, released (v7.0.2); phases 1–2 adopted (as-built), 3–4 n/a, 5 in progress, 6 in progress, 7 not started, 8 n/a; `Assistant config: none … (tools: claude)` (D-008); `Client budget: no` (D-010); `User guide: declined` (D-013); `Website intent: no` (D-005); `Autonomy: automatic`, issues after-sprint, 24h (D-011); `Chaining: start` (D-015); no `Sprints:` line (so not `Sprints: off`); no `E2E:` line.

States: `present` (path verified on disk, content grepped where the requirement is about content) · `missing` · `declined` (real D-entry) · `n/a` (excluding condition named).

## Table 1 — parity

The manifest's Table 1 carries no row ids; `T1-nn` is the row's position in the manifest's order.

| # | Requirement | State | Evidence / where / decision / excluding condition |
|---|---|---|---|
| T1-01 | `docs/PROGRESS.md` | present | `docs/PROGRESS.md` (project card, phase status, position, deferred items read) |
| T1-02 | `docs/decisions.md` | present | `docs/decisions.md`, D-001…D-034 |
| T1-03 | `docs/lessons-learned.md` | present | `docs/lessons-learned.md` |
| T1-04 | `docs/sessions.md` | missing | No such file. Condition holds: the card has no `Sprints: off` |
| T1-05 | `scripts/keel-time` | missing | `scripts/` holds only `keel-continue`, `keel-doctor`, `keel-handoff-verify`, `keel-verify` |
| T1-06 | `docs/.keel/clock.jsonl` (machine-local, gitignored) | n/a | Not yet applicable: created by the first `scripts/keel-time start`, and `scripts/keel-time` does not exist (T1-05). `docs/.keel/` absent |
| T1-07 | Off-machine durability | present | `git remote -v` → `origin https://github.com/joseconti/woo-redsys-gateway-light.git`; card `Durability:` line present (D-011) |
| T1-08 | Clean working tree at every block close | present | `git status --porcelain` empty at sweep start, on `develop` (this sweep's rewrite of this file is the only change since) |
| T1-09 | `CLAUDE.md` + `AGENTS.md` lock | present | Both stamped `KEEL:BEGIN — v6.5.0`; `cmp` reports the files identical |
| T1-10 | `GEMINI.md` / `.gemini/settings.json` | n/a | Only if the user works with Gemini CLI — Claude Code only (D-008); neither path exists |
| T1-11 | `.claude/skills/keel/` + `.agents/skills/keel/` | present | `diff -rq` between the two trees and against the installed v6.5.0 skill: no differences; embedded `MANIFEST.md` header v6.5.0 |
| T1-12 | `docs/00-competitive-landscape.md` | n/a | "Unless the scan was skipped on record": skip recorded in `docs/01-discovery.md` `## Competitive scan` ("Not run … skipped for this pass"). Recorded in the discovery document, not as a D-entry |
| T1-13 | `docs/01-discovery.md` incl. `## Environment & test drivers` | missing | File present; the `## Environment & test drivers` section (§5a preflight) is absent (headings grepped) |
| T1-14 | `docs/estimate.md` | present | `docs/estimate.md` (adoption estimate; states no firm estimate exists for future work) |
| T1-15 | `docs/token-ledger.md` | present | `docs/token-ledger.md`, one row per session, last row 2026-08-02 |
| T1-16 | `docs/02-functional-spec.md` with `AC-nn` IDs | missing | File present; zero `AC-nn` identifiers. `## Acceptance criteria` says "Not reconstructed line-by-line … progressive backfill". Its `## Testing` paragraph also still says no automated suite exists (stale) |
| T1-17 | `docs/03-technical-plan.md` (code map, change map, testing plan with drivers, `## Environment requirements`) | missing | File present with `[E]`/`[A]`/`[G]` code map, conventions, verified test commands. Missing parts: `## Environment requirements` section; driver-per-surface with headless verdict; element-addressability convention; division of labour with tags; static-analysis commands (phpcs "not verified to run cleanly"); accessibility automation; read-back duty. The change map lives in `docs/02-functional-spec.md`, not in the plan |
| T1-18 | `docs/threat-model.md` | present | Assumptions, defended controls with delivery states (7 `IN PLACE`, 4 `TO BUILD`, 2 `MANUAL`, 3 `VERIFY` occurrences), `## Not defended` table |
| T1-19 | `docs/flows/` | missing | Directory absent; no D-entry declines it. Flows are a list inside `docs/02-functional-spec.md` `## Features / flows` |
| T1-20 | `docs/budget.md` | n/a | Only if `Client budget: yes` — card says `no` (D-010) |
| T1-21 | `docs/spec-references/` | n/a | Only if the spec records any — `docs/02-functional-spec.md` has no `## Reference artifacts` section |
| T1-22 | `docs/rubrics/` | n/a | Only if a rubric domain was accepted at Phase 2 §6a — phases 1–2 were adopted as-built, no rubric on record |
| T1-23 | `docs/design/references/` | n/a | Only if the user holds any — phases 3–4 n/a, no design contract (D-009) |
| T1-24 | Assistant rules containers | n/a | Only if accepted — card `Assistant config: none beyond the lock + embedded skill` (D-008); `.claude/rules/` absent |
| T1-25 | Assistant subagents | n/a | Same condition (D-008); `.claude/agents/` absent; card `Models: n/a` |
| T1-26 | `docs/design/DESIGN-BRIEF.md` | n/a | UI projects with a design contract only — Phase 3 n/a (card, D-009) |
| T1-27 | `docs/design/design-handoff/` | n/a | Phase 4 n/a — no design contract |
| T1-28 | `docs/BUILD-SPEC.md` | n/a | Phase 4 n/a — no design contract |
| T1-29 | `docs/design/design-requests/` | n/a | "When the first Design Request appears" — none (card open items) |
| T1-30 | `.gitignore` + `.gitattributes` with the mandatory ignore entries | missing | Both files present. `.gitignore` has `CLAUDE.local.md`, `.claude/settings.local.json`, `.keel-update-check`, `docs/continuation-prompt.md`. Missing entry: `docs/.keel/clock.jsonl` (since v6.0.0) |
| T1-31 | `docs/sprints/` — one file per sprint, `keel.sprint/1` frontmatter | missing | Directory holds only `README.md` (old pre-frontmatter template, "No sprint has been planned yet"). No sprint file although Phase 5 slices were done (D-016…D-033) |
| T1-32 | `docs/sprints/deferred.md` | missing | Absent. Deferred work lives only in `docs/PROGRESS.md` "Deferred items" |
| T1-33 | `docs/.keel/plan.json` | missing | `docs/.keel/` absent |
| T1-34 | `docs/05-test-points.md` with `Criterion`, `Coverage`, `Red first` | missing | File present with `Criterion (AC-nn)` and `Coverage` columns; the `Red first` column is absent (grepped) |
| T1-35 | `docs/api/INDEX.md` | present | `docs/api/INDEX.md` |
| T1-36 | `docs/keel-conformance.md` | present | This file, rewritten by this sweep (tracked in git) |
| T1-37 | `docs/playground.md` | present | `last verified: 2026-08-01` stamp present |
| T1-38 | `scripts/keel-verify` | present | Executable, five checks. Its missing newer checks are Table 3 rows |
| T1-39 | `scripts/keel-affected-tests` | missing | Absent. Condition holds: automated suite exists (PHPUnit unit + integration, Playwright) |
| T1-40 | `.githooks/pre-push` + `core.hooksPath` | missing | `.githooks/` absent; `git config --get core.hooksPath` returns nothing |
| T1-41 | `scripts/keel-doctor` | present | Executable, `--check`/`--plan`/`--fix`/`--json`. Note: it is hand-built, not compiled from a `## Environment requirements` section (that section is missing — T1-17) |
| T1-42 | `scripts/` build/minify script | missing | Condition holds: the plugin ships front-end CSS/JS (`assets/css/*.css`, `assets/js/frontend/`). No minify script and no `*.min.*` file in the tree. Recorded as a gap, not as a D-entry: `docs/03-technical-plan.md` "Front-end asset build contract" ("not yet applied") and `docs/04-adoption-audit.md` line 90 ("accepted as-is for now, deferred to a dedicated remediation sprint") |
| T1-43 | `scripts/keel-handoff-verify` | present | Executable; five courier checks, lane claim on `Chaining: start`, `--release`; allow-list entry `Bash(./scripts/keel-handoff-verify:*)` in `.claude/settings.local.json` |
| T1-44 | Single-lane lock | present | Implemented in `scripts/keel-handoff-verify`: file outside the repo at `${TMPDIR:-/tmp}/keel-locks/<sha256 of repo toplevel path>.lock`, PID + start time, baton case. Note: the location is the temp dir, not a user state dir |
| T1-45 | `scripts/keel-tools/<tool>.sh` (one row per accepted assistant) | missing | Directory absent. Condition holds: `Chaining: start`; accepted tool list is `claude` |
| T1-46 | `scripts/keel-continue` | present | Executable; allow-list entry present. Predates several contract points — see Table 3 (T3-5.10.3-1, T3-5.13.0-3b, T3-5.14.0-4, T3-5.20.0-1, T3-6.1.0-2) |
| T1-47 | `scripts/keel-close` | missing | Absent; no allow-list entry |
| T1-48 | `.githooks/post-commit` + `core.hooksPath` | missing | `.githooks/` absent; `core.hooksPath` unset |
| T1-49 | `scripts/keel-stop-hook` + `Stop` hook registration | missing | Script absent; no `hooks` key in `.claude/settings.local.json`; `.claude/settings.json` absent; no allow-list entry |
| T1-50 | `scripts/keel-session-pid.sh` | missing | Absent. Both existing scripts carry their own `find_owning_session_pid` copy |
| T1-51 | `scripts/keel-chain-check` | missing | Absent; no allow-list entry. Condition holds: `Chaining: start` |
| T1-52 | `Chaining model:` card line | missing | Not on the card (grepped). Condition holds: `Chaining: start` |
| T1-53 | `Chain verified:` card line | missing | Not on the card. Written only by `scripts/keel-chain-check --smoke`, which does not exist |
| T1-54 | `.githooks/pre-commit` | n/a | Only if the assistant-config package is accepted — card `Assistant config: none` (D-008) |
| T1-55 | Permission allow-lists (`.claude/settings.json`) | n/a | Same condition (D-008). Only the machine-local `.claude/settings.local.json` exists |
| T1-56 | CI workflow | n/a | Same condition (D-008); `.github/` absent |
| T1-57 | MCP registration | n/a | Only if the technical plan defines dev MCP servers — none in `docs/03-technical-plan.md`; `.mcp.json` absent |
| T1-58 | `docs/architecture.md` | present | `docs/architecture.md` |
| T1-59 | `docs/api/`, `docs/usage/`, `docs/reference/` | missing | `docs/api/` present (INDEX only). `docs/usage/` and `docs/reference/` absent. Phase 6 is "in progress"; per-surface docs are on record as progressive backfill (`docs/api/INDEX.md`, `docs/04-adoption-audit.md`) — no D-entry |
| T1-60 | `docs/security.md` | present | `docs/security.md` (profile `references/security/wordpress.md`, D-001) |
| T1-61 | `docs/accessibility.md` with automated results + guided AT pass | missing | File present; both the automated pass and the assistive-technology pass are recorded as `TO BUILD — not run` |
| T1-62 | `README.md` (repo root) | present | `README.md` |
| T1-63 | `guide/` | declined | D-013 (end-user guide declined for now; `readme.txt` covers it) |
| T1-64 | `guide/_theme/` + `guide/brand/` + theme meta | declined | D-013; card `Docs theme: n/a — no guide` |
| T1-65 | `docs/07-release.md` | n/a | Required from Phase 7 — "not started" |
| T1-66 | `docs/security-audit.md` | n/a | Required from Phase 7, and only once the card says `Security audit: required` or an audit has run. Phase 7 not started, no audit has run. The card line itself is missing — see T1-C14 |
| T1-67 | `docs/security-audit/` in `.gitignore` | n/a | Any project on which an audit has run — none has |
| T1-68 | `<site-docs>/` | n/a | Website intent only — `no` (D-005) |
| T1-69 | `SPEC/art-direction.md` | n/a | Website intent only (D-005) |
| T1-70 | `~/.keel/art-ledger.md` | n/a | Website intent only (D-005) |
| T1-71 | `<site-docs>/launch-report.md` | n/a | Website intent only (D-005) |
| T1-72 | `<site-docs>/operations.md` | n/a | Website intent only (D-005) |
| T1-73 | `docs/.keel/e2e-status.json` | n/a | Only if the card carries an `E2E:` line — it does not (absent is the default) |
| T1-74 | `docs/.keel/e2e-history.jsonl` | n/a | Same condition; optional even where `E2E:` exists |
| T1-75 | `docs/.keel/slices/<n>.json` | n/a | Only if work is fanned out over git worktrees — not on record |
| T1-76 | `docs/issues.md` with `Last inbound sweep:` | present | `docs/issues.md` line 5: `Last inbound sweep: 2026-08-01 17:50` |
| T1-77 | `docs/old/` | n/a | "When archiving starts" — nothing archived yet |
| T1-78 | `docs/04-adoption-audit.md` | present | `docs/04-adoption-audit.md` |

### Project-card lines (manifest paragraph under Table 1; template in `references/project-state.md`)

| # | Requirement | State | Evidence / where / decision / excluding condition |
|---|---|---|---|
| T1-C01 | Base card lines (name, type, stack, license, docs language, security profile, accessibility, i18n, installed base, design system, website intent, durability, autonomy, branches, notify) | present | All read on the card, `docs/PROGRESS.md` lines 6–27 |
| T1-C02 | `Keel portability:` | present | "lock + embedded v6.5.0" |
| T1-C03 | `Assistant config:` | present | "none beyond the lock + embedded skill (tools: claude) (D-008)" |
| T1-C04 | `Keel baseline:` | present | "v5.9.0" — the line exists; its value advances only when this reconciliation completes |
| T1-C05 | `Client budget:` | present | "no (D-010)" |
| T1-C06 | `User guide:` | present | "declined for now (D-013)" |
| T1-C07 | `Docs theme:` | present | "n/a — no guide" |
| T1-C08 | `Models:` | present | "n/a — no subagent role→model map configured" |
| T1-C09 | `Chaining:` | present | "start (D-015)" |
| T1-C10 | `Issue sweep interval:` (on the `Autonomy:` line) | present | "Issue sweep interval: 24h" (D-011) |
| T1-C11 | `Test-first policy:` | missing | Not on the card; the question was never asked (no D-entry) |
| T1-C12 | `Sprints:` | missing | Not on the card (to be written `on`, never asked) |
| T1-C13 | `Push test scope:` | missing | Not on the card (to be written `affected`, never asked) |
| T1-C14 | `Security audit:` | missing | Not on the card (derived from the threat model, never asked) |
| T1-C15 | `CI runs on:` | missing | Not on the card. Template value for this project is `n/a` (no forge CI, config package not accepted). The manifest's own card-line paragraph does not list this line; the full-card template does — judgment flagged |
| T1-C16 | `E2E:` | n/a | Absent is the default and means the feature does not exist for the project; never invented or guessed from `package.json` |
| T1-C17 | `E2E env:` | n/a | Optional, only alongside `E2E:` |

## Table 3 — per-version actions (v5.9.0 → v6.5.0)

Ids are `T3-<version>-<manifest action number>`. Where a row asks to REGENERATE a script that does not exist on this disk, the row is `n/a` and names the `missing` row that creates that script from the current (v6.5.0) contract — so nothing is counted twice and nothing is dropped.

### v5.10.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.10.0-1 | Re-ask the chaining question where the card is `Autonomy: automatic` + `Chaining: off` | n/a | Card is `Chaining: start` (D-015) |
| T3-5.10.0-2 | Lock stamp-only refresh (this action recurs unchanged in v5.10.0–v5.20.0) | present | `CLAUDE.md` + `AGENTS.md` stamped v6.5.0 (D-034) |

### v5.10.1
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.10.1-1 | Closed list of four chain stops; close-out never asks permission | n/a | "No per-project action" — behavioural, lives in SKILL.md / references |

### v5.10.2
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.10.2-1 | Chain decided by the script; live `command -v claude` re-check | n/a | "No per-project action". (The live re-check is in `scripts/keel-continue` line 133) |

### v5.10.3
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.10.3-1 | `scripts/keel-continue` contract points 6a/6b, picked up at its next regeneration | missing | 6b met (passes "Lee <handoff> y continúa.", not the file content). 6a not met: the Terminal command is an interpolated string inside a generated AppleScript (`do script "cd … && claude '…'"`), not a `chmod +x` script file run by path; and `mktemp -t keel-continue-XXXXXX.applescript` puts a literal suffix after the `X` run |
| T3-5.10.3-2 | `env.PATH` includes the per-user installer dir as a literal absolute path | present | `.claude/settings.local.json` `env.PATH` starts with `/Users/joseconti/.local/bin` |

### v5.11.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.11.0-1 | Ask the test-first question once; add `Test-first policy:` | missing | Never asked; no card line (T1-C11) |
| T3-5.11.0-2 | Add `Red first` column to `docs/05-test-points.md`; existing rows `n/a — predates` | missing | Column absent (T1-34) |
| T3-5.11.0-3 | Extend `scripts/keel-verify` with the three red checks | missing | No `Red first` handling in `scripts/keel-verify` (grepped) |
| T3-5.11.0-4 | Bug fixes start from a failing reproduction test | n/a | Behavioural standing rule, no artifact |
| T3-5.11.0-5 | A test derived from an `AC-nn` or a bug is never edited to pass | n/a | Behavioural standing rule, no artifact |

### v5.12.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.12.0-1 | Art direction, ledger, `SPEC/art-direction.md`, blacklist, launch checks (all seven points) | n/a | Website projects only — `Website intent: no` (D-005) |

### v5.13.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.13.0-1 | Generate `scripts/keel-chain-check` + allow-list entry | missing | Script and entry absent (T1-51) |
| T3-5.13.0-2 | Run `--smoke` once | missing | Cannot have run — script absent |
| T3-5.13.0-3 | `Chain verified:` card line | missing | Absent (T1-53) |
| T3-5.13.0-3b | Ask `Chaining model:`; launcher passes `--model` on every fire | missing | No card line (T1-52); `scripts/keel-continue` launches `claude '<prompt>'` with no `--model` |
| T3-5.13.0-4 | `Mode:` field in the hand-off freshness header | missing | The (gitignored, stale) `docs/continuation-prompt.md` header has no `Mode:` line; nothing on disk writes one |
| T3-5.13.0-5 | Two run points for `keel-chain-check` (session start, before the fire) | n/a | Behavioural run points; become applicable once T3-5.13.0-1 exists |

### v5.14.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.14.0-1 | Generate `scripts/keel-close` + allow-list entry | missing | Absent (T1-47) |
| T3-5.14.0-2 | Install `.githooks/post-commit`, set `core.hooksPath`, verify it fires on a real commit | missing | Absent / unset (T1-48) |
| T3-5.14.0-3 | Row 10b in `keel-chain-check` | n/a | Script does not exist; generated with it under T3-5.13.0-1 |
| T3-5.14.0-4 | `scripts/keel-continue` degrades on a bad Keel artifact (DEGRADE/TERMINAL table) instead of printing | missing | Current script prints and exits on a missing or failing hand-off; no degrade path (grepped) |
| T3-5.14.0-5 | Notify when `keel-continue` prints on a chaining card | n/a | Behavioural session duty through the recorded channel (D-011), no artifact |

### v5.15.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.15.0-1 | Generate `scripts/keel-stop-hook`, allow-list entry, register as `Stop` hook, verify it fires | missing | Absent / unregistered (T1-49) |
| T3-5.15.0-2 | Re-read anti-patterns 12e–12l | n/a | Re-read duty, no project artifact |
| T3-5.15.0-3 | Two new operating principles | n/a | Behavioural |
| T3-5.15.0-4 | Context-discipline exceptions | n/a | Behavioural |

### v5.15.1
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.15.1-1 | Regenerate `scripts/keel-stop-hook` (session-scoped rule, block log keyed by repo and session) | n/a | No hook on disk to regenerate; created under T3-5.15.0-1 from the current contract |
| T3-5.15.1-3 | Generate `scripts/keel-session-pid.sh` | missing | Absent (T1-50) |
| T3-5.15.1-5 | Verify the hook in both directions, then observe it firing after a restart | missing | One-time verification, never run (no hook) |
| T3-5.15.1-6 | Re-read anti-pattern 12m | n/a | Re-read duty |

### v5.15.2
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.15.2-1 | Regenerate `scripts/keel-stop-hook` (rule 2 cedes, close-out discharge, queue counted in `## Open items`, per-rule fingerprints) | n/a | No hook on disk to regenerate; created under T3-5.15.0-1 |
| T3-5.15.2-5 | Verify both directions for every blocking rule | missing | One-time verification, never run (no hook) |
| T3-5.15.2-6 | Operating principle "fix the class, not the instance" | n/a | Behavioural |

### v5.16.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.16.0-1 | Ask the `CI runs on:` question | n/a | Only on a project with forge CI and an accepted config package — neither (D-008; `.github/` absent). "Projects with no CI need nothing". (The card line itself: T1-C15) |
| T3-5.16.0-5 | Regenerate the workflow's `on:` block | n/a | No CI workflow exists |

### v5.17.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.17.0-1 | Sprint files with `keel.sprint/1` frontmatter, `docs/sprints/deferred.md`, generated `docs/.keel/plan.json` + human index | missing | None exist (T1-31, T1-32, T1-33) |
| T3-5.17.0-3 | `scripts/keel-verify` plan checks | missing | No plan handling in `scripts/keel-verify` (grepped) |
| T3-5.17.0-4 | `E2E:` / `E2E env:` card lines and published result | n/a | Absent is the default; never invented. (The project does have `npm run test:e2e` — declaring it is the user's choice) |
| T3-5.17.0-5 | Convention for machine-readable artifacts | n/a | Standing convention; no machine-readable artifact exists yet |

### v5.18.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.18.0-1 | Stop hook registered only for the tool whose schema is confirmed | n/a | No hook registered anywhere; `.codex/` holds only `config.toml` (no `hooks.json`) |
| T3-5.18.0-2 | `keel-continue` point 4a — only the detected tool's own action, never a fallback | present | `scripts/keel-continue` lines 84–101: no `CLAUDECODE` marker → print; CLI row with no action at the card's tier → print |
| T3-5.18.0-3 | Codex `start` row | n/a | Codex not an accepted tool (D-008) |
| T3-5.18.0-4 | `Chaining model:` description generalised; anti-patterns 12p/12q | n/a | Wording / re-read only |
| T3-5.18.0-6 | `CI runs on:` default reasoning for private GitHub repos | n/a | No CI (D-008) |

### v5.19.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.19.0-1a | `Not checked:` field on any decision entry asserting an impossibility | missing | No `Not checked:` line anywhere in `docs/decisions.md`. The manifest's grep words hit D-029 (line 233, "impossible", in Alternatives rejected). Entries are append-only, so this needs the user's say on how to satisfy it |
| T3-5.19.0-1b | Matching `scripts/keel-verify` check | missing | Not in `scripts/keel-verify` (grepped) |
| T3-5.19.0-2 | Re-measure when the user contradicts a recorded negative | n/a | Behavioural |
| T3-5.19.0-3 | `Chaining: supervised` card value | n/a | Card is `start` (D-015); nothing external supervises it on record |
| T3-5.19.0-4 | Anti-patterns 12s/12t | n/a | Re-read duty |

### v5.19.1
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.19.1-1 | Wording of the `supervised` option | n/a | Wording only |

### v5.19.2
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.19.2-1 | Regenerate `scripts/keel-stop-hook` (porcelain parsing for renames) | n/a | "On any project that carries one" — none; created under T3-5.15.0-1 |
| T3-5.19.2-2 | Same parsing rule binds the write rule | n/a | Behavioural (SKILL.md) |
| T3-5.19.2-3 | Re-read anti-pattern 12u | n/a | Re-read duty |

### v5.20.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.20.0-1 | Regenerate `scripts/keel-continue` (per-session fire ledger, `degraded:` receipt) | missing | Script exists and has only the per-hand-off receipt; no `fired-` / `degraded` entries (grepped) |
| T3-5.20.0-2 | Regenerate `scripts/keel-stop-hook` (rule 4 reads the fire ledger) | n/a | No hook on disk; created under T3-5.15.0-1 |
| T3-5.20.0-3 | Regenerate `scripts/keel-chain-check` (rows 7a/7b, double-fire smoke) | n/a | No script on disk; created under T3-5.13.0-1 |
| T3-5.20.0-4 | Re-run `--smoke` after regenerating the launcher | missing | One-time verification, never run |
| T3-5.20.0-6 | Re-read anti-pattern 12v | n/a | Re-read duty |

### v5.21.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.21.0-0 | Lock block TEXT refresh | present | Block rewritten from the canonical copy and stamped v6.5.0 (D-034); it names sprints as the ledger |
| T3-5.21.0-1 | Card line `Sprints: on` | missing | Absent (T1-C12) |
| T3-5.21.0-2 | Create the plan where none exists (sprint file, `deferred.md`, `plan.json`) from open items and work in flight, with hours, and show it | missing | None exist |
| T3-5.21.0-3 | `actual_hours` on every `done` slice (backfilled estimate), regenerate `plan.json` | missing | No slices recorded at all |
| T3-5.21.0-4 | Regenerate `scripts/keel-verify` with the five plan checks | missing | Not in the script |
| T3-5.21.0-5 | Regenerate `scripts/keel-stop-hook` (plan-behind-the-work state) | n/a | No hook on disk; created under T3-5.15.0-1 |
| T3-5.21.0-6 | Every unit of work is a slice from now on | n/a | Behavioural |
| T3-5.21.0-7 | Re-read SKILL.md "Sprints are the ledger of all work", anti-pattern 12w | n/a | Re-read duty |

### v6.0.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.0.0-1a | Generate `scripts/keel-time` | missing | Absent (T1-05) |
| T3-6.0.0-1b | Create `docs/sessions.md` from its template | missing | Absent (T1-04) |
| T3-6.0.0-1c | Add `docs/.keel/clock.jsonl` to `.gitignore` | missing | Entry absent (T1-30) |
| T3-6.0.0-1d | Add `docs/sessions.md` to the bookkeeping-file list in `keel-verify` and `keel-stop-hook` | missing | `scripts/keel-verify` has no such list; hook absent |
| T3-6.0.0-1e | Every session opens with `keel-time start` and closes with `keel-time end` | n/a | Behavioural; not yet applicable until T3-6.0.0-1a exists |
| T3-6.0.0-2 | Slice field `actual_source`; mark existing `actual_hours` as `estimated` | missing | No slice data exists; applies together with T3-5.21.0-3 |
| T3-6.0.0-3 | Regenerate `scripts/keel-close` with step 0 | n/a | No script on disk; created under T3-5.14.0-1 |
| T3-6.0.0-4a | Card line `Push test scope: affected` | missing | Absent (T1-C13) |
| T3-6.0.0-4b | `Test selection` line in `docs/03-technical-plan.md` §Testing | missing | Absent (grepped) |
| T3-6.0.0-4c | Generate `scripts/keel-affected-tests` | missing | Absent (T1-39) |
| T3-6.0.0-4d | Generate `.githooks/pre-push` | missing | Absent (T1-40) |
| T3-6.0.0-4e | Verify both on a real diff (dependent's tests selected, red selection blocks a push, uncovered file widened) | missing | One-time verification, never run |
| T3-6.0.0-4f | Switch non-`main` CI triggers to the affected selection | n/a | No CI |
| T3-6.0.0-5 | Regenerate `scripts/keel-verify` with the test-selection and session-time rows | missing | Not in the script |
| T3-6.0.0-6 | Refresh the lock block | present | v6.5.0 block in both files (D-034) |

### v6.1.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.1.0-1 | Generate `scripts/keel-tools/<tool>.sh` for each accepted tool (here: `claude`) | missing | Directory absent (T1-45) |
| T3-6.1.0-2 | Move every per-tool fact out of the shared scripts into the row | missing | `scripts/keel-continue` and `scripts/keel-handoff-verify` carry tool names on executable lines (`claude-vscode`, `claude-cli`, `claude '<prompt>'`, the `*[Cc]laude*` process match) |
| T3-6.1.0-3 | Regenerate `scripts/keel-verify` with the four registry rows | missing | Not in the script |
| T3-6.1.0-4 | Run the fourth check (hook file mentions the stop hook iff the row says `yes`) on the existing tree first | missing | Not run as a check (the check does not exist). By direct inspection there is no stop-hook registration in any tool container, so nothing would need removing |
| T3-6.1.0-5 | Re-run `scripts/keel-chain-check --smoke` | missing | One-time verification, never run |
| T3-6.1.0-6 | Restamp the lock | present | v6.5.0 |

### v6.2.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.2.0-1 | Regenerate `scripts/keel-tools/codex.sh` with the new flags; re-run smoke | n/a | Only on a project that accepted Codex — not accepted (D-008). Note: a tracked `.codex/config.toml` exists (sets `PATH` only) |
| T3-6.2.0-2 | Restamp the lock | present | v6.5.0 |

### v6.3.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.3.0-1 | Derive and write the `Security audit:` card line | missing | Absent (T1-C14). By the manifest's criteria it derives to `required` (money moves; externally reachable notification endpoints `?wc-api=WC_Gateway_<id>` in `docs/threat-model.md`) |
| T3-6.3.0-2 | Nothing is created until an audit runs | n/a | No audit has run |
| T3-6.3.0-3 | On a `required` project, tell the user now that the next release gate needs an audit covering its candidate, or a D-entry declining it | missing | Not yet stated or recorded; the card's next action is a release |
| T3-6.3.0-4 | Restamp the lock | present | v6.5.0 |

### v6.4.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.4.0-1 | Regenerate `scripts/keel-time` | n/a | No script on disk; created under T3-6.0.0-1a from the current contract |
| T3-6.4.0-2 | Extend the `plan.json` generator with `pace_factor`, `projected_remaining_hours` | n/a | No generator on disk; created under T3-5.17.0-1 |
| T3-6.4.0-3 | Add `Active h`, `Pace factor`, `Projected left h` to `docs/sessions.md` | n/a | No file on disk; created under T3-6.0.0-1b from the current template |
| T3-6.4.0-4a | Regenerate `scripts/keel-verify` with the arithmetic, data-eligibility and ledger checks | missing | Not in the script |
| T3-6.4.0-4b | Exercise the timing report in the three named cases | missing | One-time verification, never run |
| T3-6.4.0-5 | Restamp the lock | present | v6.5.0 |

### v6.5.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.5.0-1 | Cap local Playwright workers with the `PW_WORKERS` expression and record the cap in the plan's testing block | missing | `playwright.config.js` has a fixed `workers: 1` (already capped, stricter than the default of 2, but not the mandated expression); the cap is not recorded in `docs/03-technical-plan.md` (grepped) |
| T3-6.5.0-2 | Browser MCP registered repo-level only with `--headless --isolated` | n/a | Only where the assistant drives the browser through an MCP server — none on record for this project; `.mcp.json` absent. User-level registrations were not inspected (outside the three sources) |
| T3-6.5.0-3 | Regenerate `scripts/keel-doctor` with the three advisory browser-MCP rows | missing | No MCP / orphaned-browser rows in `scripts/keel-doctor` (grepped) |
| T3-6.5.0-4 | Restamp the lock | present | v6.5.0 |

## Totals

| Table | present | missing | declined | n/a | Rows |
|---|---|---|---|---|---|
| Table 1 — paths (T1-01…T1-78) | 23 | 24 | 2 | 29 | 78 |
| Table 1 — card lines (T1-C01…T1-C17) | 10 | 5 | 0 | 2 | 17 |
| Table 1 — total | 33 | 29 | 2 | 31 | 95 |
| Table 3 — v5.10.0…v6.5.0 | 10 | 48 | 0 | 46 | 104 |

## Pending decisions

None of the rows below has been applied. Each awaits the user's decision (apply / trim / defer / decline — a refusal becomes a `declined` row with its D-entry).

- Table 1, paths: T1-04, T1-05, T1-13, T1-16, T1-17, T1-19, T1-30, T1-31, T1-32, T1-33, T1-34, T1-39, T1-40, T1-42, T1-45, T1-47, T1-48, T1-49, T1-50, T1-51, T1-52, T1-53, T1-59, T1-61.
- Table 1, card lines: T1-C11, T1-C12, T1-C13, T1-C14, T1-C15.
- Table 3: T3-5.10.3-1; T3-5.11.0-1, -2, -3; T3-5.13.0-1, -2, -3, -3b, -4; T3-5.14.0-1, -2, -4; T3-5.15.0-1; T3-5.15.1-3, -5; T3-5.15.2-5; T3-5.17.0-1, -3; T3-5.19.0-1a, -1b; T3-5.20.0-1, -4; T3-5.21.0-1, -2, -3, -4; T3-6.0.0-1a, -1b, -1c, -1d, -2, -4a, -4b, -4c, -4d, -4e, -5; T3-6.1.0-1, -2, -3, -4, -5; T3-6.3.0-1, -3; T3-6.4.0-4a, -4b; T3-6.5.0-1, -3.

Many Table 3 rows are the per-version view of a Table 1 row (for example T3-6.0.0-1a and T1-05 are both `scripts/keel-time`); they are listed in both tables because the manifest lists them in both.

Judgments recorded for the user's review: T1-12 (scan skip recorded in the discovery document, not as a D-entry); T1-24/25/54/55/56 (`n/a` rests on the card's `Assistant config: none`; D-008 records "Claude Code only" rather than an explicit refusal of the Claude package); T1-42 (deferral recorded in the adoption audit, not as a D-entry, so it is `missing`, not `declined`); T1-16, T1-19, T1-59, T1-61 (adoption's progressive-backfill rule is on record in the docs but no D-entry declines them); T1-44 (lane lives under the temp dir); T1-C15; T3-5.19.0-1a (append-only log); T3-6.5.0-1 and -2.
