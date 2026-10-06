# Keel conformance sweep — Payment Gateway for Redsys & WooCommerce Lite

- Date: 2026-10-06
- Sweep: post-update reconciliation v5.9.0 → v6.5.0 — second pass, after the reconciliation was applied
- Sources (exactly three): the repository's disk as it is now; the Keel v6.5.0 manifest (Table 1 and Table 3); the decision log (D-001…D-047). The project card, phase status and open items were read from the living state file for the conditions only.
- Every state below was re-derived from the disk in this pass. The previous version of this file was overwritten without being consulted.
- Commands run for evidence (all read-only): the project linter, the chain check without its smoke mode, the environment doctor in check mode, a syntax check of every generated script and hook, and file comparisons of the lock and the embedded trees.
- The sweep ran while slice S-026 was still open and the main session was editing the tree; rows that depend on a moving file say so.

Project conditions used (read from the card): WordPress plugin / WooCommerce extension, released (v7.0.2); phases 1–2 adopted (as-built), 3–4 n/a, 5 in progress, 6 in progress, 7 not started, 8 n/a; assistant config full for Claude Code only (D-008, D-039, D-043), CI and MCP not part of it; client budget no (D-010); user guide declined (D-013); website intent no (D-005); autonomy automatic, issues after-sprint, 24h (D-011); chaining start (D-015) with model opus (D-037); sprints on; push test scope affected; security audit required (D-040); no E2E line.

States: `present` (verified on disk now, evidence given) · `missing` (cites the decision and, where one exists, the slice that schedules it; a `missing` row with no decision behind it is repeated under "Unresolved") · `declined` (a recorded refusal) · `n/a` (excluding condition named).

## Table 1 — parity

The manifest's Table 1 carries no row ids; `T1-nn` is the row's position in the manifest's order.

| # | Requirement | State | Evidence / where / decision / excluding condition |
|---|---|---|---|
| T1-01 | `docs/PROGRESS.md` | present | Project card, phase status, current position (slice S-026), open items, deferred items |
| T1-02 | `docs/decisions.md` | present | D-001…D-047 |
| T1-03 | `docs/lessons-learned.md` | present | L-001…L-007 |
| T1-04 | `docs/sessions.md` | present | Fifteen-column header from the current template; zero rows yet (the first is appended when the session clock is ended); linter check 24 OK |
| T1-05 | `scripts/keel-time` | present | Executable, syntax clean; start, slice-start, slice-end, pause, resume, end, report |
| T1-06 | `docs/.keel/clock.jsonl` | present | On disk, two events with schema keel.clock/1 (a session-start and a slice-start for S-026), gitignored and untracked |
| T1-07 | Off-machine durability | present | Remote origin on GitHub; card durability line; linter check 19 OK (every commit on develop is on origin/develop) |
| T1-08 | Clean working tree at every block close | present | Work lands on develop, which tracks origin/develop with nothing ahead. The block (S-026) is still open, so the tree is not clean at this instant: the linter's check 20 reports the living state file modified, and this file is rewritten by this sweep. The requirement binds at the close of S-026 |
| T1-09 | `CLAUDE.md` + `AGENTS.md` lock | present | Both stamped v6.5.0; the two files are byte-identical |
| T1-10 | Gemini lock mirror | n/a | Only if the user works with Gemini CLI — Claude Code only (D-008) |
| T1-11 | `.claude/skills/keel/` + `.agents/skills/keel/` | present | The two trees are identical to each other and to the installed v6.5.0 skill |
| T1-12 | Competitive landscape document | declined | D-035 (the competitive scan and its document declined by the user) |
| T1-13 | `docs/01-discovery.md` with its environment and test drivers section | present | Section "Environment & test drivers (step 5a preflight)" at line 52, recorded from a real doctor run |
| T1-14 | `docs/estimate.md` | present | Estimate v2 re-based on the sprint plan (42.75 h, equal to the plan total; linter check 22 OK) |
| T1-15 | `docs/token-ledger.md` | present | One row per session; the last row is 2026-08-02 — the session of 2026-10-06 has no row yet (it is still open) |
| T1-16 | `docs/02-functional-spec.md` with stable criterion ids | present | AC-01…AC-57 in the acceptance-criteria table (57 rows counted); 26 of them recorded as as-built, unverified (D-045) |
| T1-17 | `docs/03-technical-plan.md` (code map, change map, testing plan with drivers, environment requirements) | present | Code map, testing plan with drivers, environment requirements; the change map lives in docs/02-functional-spec.md and the plan now points to it from its own "Change map" section. |
| T1-18 | `docs/threat-model.md` | present | Assumptions, defended controls with delivery states, "Not defended" table |
| T1-19 | `docs/flows/` | present | Six flow files (four checkouts, notification handling, refund) |
| T1-20 | Client budget document | n/a | Only if the card says client budget yes — it says no (D-010) |
| T1-21 | Spec reference artifacts directory | n/a | Only if the spec records any — the functional spec has no reference-artifacts section |
| T1-22 | Rubrics directory | n/a | Only if a rubric domain was accepted at the Phase 2 review — phases 1–2 were adopted as-built, none on record |
| T1-23 | Design references directory | n/a | Only if the user holds any — phases 3–4 n/a, no design contract (D-009) |
| T1-24 | Assistant rules: `.claude/rules/` | present | Three rule files (code style, docs discipline, security); accepted by D-039 |
| T1-25 | Assistant subagents: `.claude/agents/` | present | Six agent files; model map on the card (D-043) |
| T1-26 | Design brief | n/a | Phase 3 n/a — no design contract (D-009) |
| T1-27 | Design handoff directory | n/a | Phase 4 n/a — no design contract |
| T1-28 | Build spec | n/a | Phase 4 n/a — no design contract |
| T1-29 | Design request register | n/a | When the first Design Request appears — none |
| T1-30 | `.gitignore` + `.gitattributes` with the mandatory ignore entries | present | All five mandatory entries are in the ignore file (local instructions file, local settings file, clock file at line 56, update-check stamp, hand-off file at line 42) |
| T1-31 | `docs/sprints/` — one file per sprint | present | Three sprint files with keel.sprint/1 frontmatter, 26 slices; linter check 22 OK |
| T1-32 | `docs/sprints/deferred.md` | present | Two items (S-033, S-034) |
| T1-33 | `docs/.keel/plan.json` | present | Schema keel.plan/1, generated; matches its sources (linter check 22) |
| T1-34 | `docs/05-test-points.md` with criterion, coverage and red-first columns | present | Criterion, coverage and red-first columns exist; 31 ids are bound to rows, 26 are listed as unverified in D-045, 57 in all (linter check 16). |
| T1-35 | `docs/api/INDEX.md` | present | Linter checks 2 and 7 OK |
| T1-36 | `docs/keel-conformance.md` | present | This file |
| T1-37 | `docs/playground.md` | present | Access, try-it, seed and reset instructions, stamp "last verified: 2026-08-01". The playground does not build today (D-044, L-007); restoring it is S-036 |
| T1-38 | `scripts/keel-verify` | present | Executable, 28 checks, ran to completion in this sweep |
| T1-39 | `scripts/keel-affected-tests` | present | Executable; base, head, run and full options; prints one scope line; linter check 25 OK |
| T1-40 | `.githooks/pre-push` + `core.hooksPath` set | present | Hook executable; hooks path reads .githooks; skips tags and deletions |
| T1-41 | `scripts/keel-doctor` | present | Executable; ran in check mode in this sweep; compiled from the plan's environment-requirements section |
| T1-42 | Build/minify script for the shipped CSS/JS | present | `bin/build-assets.js` (CSS) and `webpack.config.js` (Blocks script, readable and minified); `npm run build:assets`; linter check 11 compares against a rebuild (D-054, S-027) |
| T1-43 | `scripts/keel-handoff-verify` | present | Executable; allow-list entry present in both settings files (chain check row 4) |
| T1-44 | Single-lane lock | present | Outside the repository, under the state directory's keel-locks folder; chain check row 9 OK (reachable, held by the live session) |
| T1-45 | `scripts/keel-tools/claude.sh` (one row per accepted assistant) | present | All nine fields plus detect and launch functions (linter check 26); the only accepted tool is claude |
| T1-46 | `scripts/keel-continue` | present | Executable; sources the tool row; checksum matches the card's chain-verified line (chain check rows 2 and 11) |
| T1-47 | `scripts/keel-close` | present | Executable; steps 0 to 8; allow-list entry in the committed settings file |
| T1-48 | `.githooks/post-commit` + `core.hooksPath` set | present | Hook deletes the hand-off file and nothing else; chain check row 10b OK (installed, active, no hand-off on disk) |
| T1-49 | `scripts/keel-stop-hook` + its `Stop` hook registration | present | Script executable; registered as the Stop hook in the committed Claude Code settings; linter check 26 confirms the registration matches the tool row. Its own allow-list entry and its recorded firing evidence are tracked in Table 3 (T3-5.15.0-1b, T3-5.15.0-1c) |
| T1-50 | `scripts/keel-session-pid.sh` | present | One sourced function; sourced by the launcher and the hand-off verifier |
| T1-51 | `scripts/keel-chain-check` | present | Executable; ran in this sweep: fifteen rows OK, verdict READY; allow-list entry in the committed settings file |
| T1-52 | Chaining-model card line | present | Card: "Chaining model: opus (D-037)" |
| T1-53 | Chain-verified card line | present | Card: dated 2026-10-06, tier start, Keel 6.5.0, launcher and row checksums; chain check row 11 says the proof is not stale |
| T1-54 | `.githooks/pre-commit` | present | Confidential-data gate, executable, active through the hooks path; accepted by D-039; public test key recognised by hash (D-042) |
| T1-55 | Permission allow-list: `.claude/settings.json` | present | Committed allow-list confirmed by the user (D-043) |
| T1-56 | CI workflow | n/a | Only if accepted and the forge has CI — forge CI was not part of the package the user accepted (D-039); the card's CI line reads n/a |
| T1-57 | MCP registration | n/a | Only if the technical plan defines development MCP servers — it defines none |
| T1-58 | `docs/architecture.md` | present | On disk |
| T1-59 | `docs/api/`, `docs/usage/`, `docs/reference/` | present | Index and readme under api; four usage documents; four reference documents (classes, endpoints, functions, hooks and extension points); linter checks 7 and 8 OK |
| T1-60 | `docs/security.md` | present | On disk; profile per D-001 |
| T1-61 | Accessibility record with automated results and the guided assistive-technology pass | missing | The automated pass is built, run and recorded (S-029, D-067: `tests/e2e/accessibility.spec.js`, results in `docs/accessibility.md`). The guided pass is still not run: it needs a person with a screen reader (S-030); its script is written |
| T1-62 | `README.md` | present | Repository root |
| T1-63 | End-user guide | declined | D-013 |
| T1-64 | Guide theme, brand layer and version marker | declined | D-013 |
| T1-65 | Release record | n/a | Required from Phase 7 — not started (the release gate is slice S-032) |
| T1-66 | Security-audit log | n/a | Required from Phase 7 and created by the first audit run — Phase 7 not started and no audit has run. The audit is required (card, D-040) and scheduled as S-028 |
| T1-67 | Security-audit run directory ignored | n/a | Any project on which an audit has run — none has |
| T1-68 | Site documentation set | n/a | Website intent only — no (D-005) |
| T1-69 | Art-direction spec | n/a | Website intent only (D-005) |
| T1-70 | Machine-local art ledger | n/a | Website intent only (D-005) |
| T1-71 | Launch report | n/a | Website intent only (D-005) |
| T1-72 | Site operations record | n/a | Website intent only (D-005) |
| T1-73 | End-to-end status file | n/a | Only if the card carries an E2E line — it does not (absent is the default) |
| T1-74 | End-to-end history file | n/a | Same condition; optional even where the line exists |
| T1-75 | Worker slice reports | n/a | Only if work is fanned out over git worktrees — not on record |
| T1-76 | `docs/issues.md` | present | Header line "Last inbound sweep: 2026-08-01 17:50" (older than the 24h interval; the sweep is due at the sprint close) |
| T1-77 | Archive directory | n/a | When archiving starts — nothing archived yet |
| T1-78 | `docs/04-adoption-audit.md` | present | On disk |

### Project-card lines (manifest paragraph under Table 1)

| # | Requirement | State | Evidence / where / decision / excluding condition |
|---|---|---|---|
| T1-C01 | Base card lines (name, type, stack, license, docs language, security profile, accessibility, i18n, installed base, design system, website intent, durability, autonomy, branches, notify) | present | All read on the card |
| T1-C02 | `Keel portability:` | present | lock + embedded v6.5.0 (D-034) |
| T1-C03 | `Assistant config:` | present | full (tools: claude) — rules, agents, pre-commit gate, committed allow-list and Stop hook; CI not accepted (D-039, D-043) |
| T1-C04 | `Keel baseline:` | present | The line exists and reads v5.9.0; it advances only when the reconciliation is closed |
| T1-C05 | `Client budget:` | present | no (D-010) |
| T1-C06 | `User guide:` | present | declined for now (D-013) |
| T1-C07 | `Docs theme:` | present | n/a — no guide |
| T1-C08 | `Models:` | present | orchestrator = session model, reviewer = sonnet, mechanical = haiku (D-043) |
| T1-C09 | `Chaining:` | present | start (D-015) |
| T1-C10 | `Issue sweep interval:` | present | 24h, on the autonomy line (D-011) |
| T1-C11 | `Test-first policy:` | present | pure-logic (D-036) |
| T1-C12 | `Sprints:` | present | on — the default, never asked |
| T1-C13 | `Push test scope:` | present | affected — the default, never asked |
| T1-C14 | `Security audit:` | present | required — money moves and the notification endpoints are reachable from outside (derived, D-040) |
| T1-C15 | `CI runs on:` | present | n/a — no forge CI (D-039) |
| T1-C16 | E2E card line | n/a | Absent is the default and means the feature does not exist for the project; never invented |
| T1-C17 | E2E environment card line | n/a | Optional, only alongside the E2E line |

## Table 3 — per-version actions (v5.9.0 → v6.5.0)

Ids are `T3-<version>-<manifest action number>`; a letter suffix splits one manifest action into the artifact and its one-time verification where the two ended in different states.

### v5.10.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.10.0-1 | Re-ask the chaining question where the card is automatic with chaining off | n/a | The card is chaining start (D-015) |
| T3-5.10.0-2 | Lock stamp-only refresh (recurs unchanged in v5.10.0–v5.20.0) | present | Both lock files stamped v6.5.0 (D-034) |

### v5.10.1
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.10.1-1 | Closed list of four chain stops; the close-out never asks permission | n/a | No per-project action — behavioural |

### v5.10.2
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.10.2-1 | Chain decided by the script; live re-check of the tool's command | n/a | No per-project action. (The launcher re-checks the row's command at line 185) |

### v5.10.3
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.10.3-1 | Launcher contract points 6a/6b | present | Chain check row 10: script file run by path, temp-file template ends in its X run, the hand-off's content is never an argument |
| T3-5.10.3-2 | Declared PATH includes the per-user installer directory as a literal path | present | Local settings: the declared PATH starts with the user's local bin directory; chain check rows 6 and 8 OK |

### v5.11.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.11.0-1 | Ask the test-first question; add the card line | present | D-036; card line pure-logic |
| T3-5.11.0-2 | Red-first column in the test-point log; existing rows take the predates value | present | Column present; 15 rows carry "n/a — predates" |
| T3-5.11.0-3 | Three red checks in the linter | present | Linter check 17 ran: enum OK, observed rows OK, judgment list reported |
| T3-5.11.0-4 | Bug fixes start from a failing reproduction test | n/a | Behavioural standing rule (restated in D-036) |
| T3-5.11.0-5 | A criterion-derived test is never edited to pass | n/a | Behavioural standing rule |

### v5.12.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.12.0-1 | Art direction, ledger, art-direction spec, blacklist, launch checks | n/a | Website projects only — website intent no (D-005) |

### v5.13.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.13.0-1 | Generate the chain check and its allow-list entry | present | Script on disk; entry in the committed settings file |
| T3-5.13.0-2 | Run the smoke mode once | present | The card's chain-verified line exists (it is written only by a passing smoke run) and its checksums match the launcher and the row on disk (chain check row 11) |
| T3-5.13.0-3 | Chain-verified card line | present | On the card, dated 2026-10-06 |
| T3-5.13.0-3b | Ask the chaining model; the launcher passes it on every fire | present | D-037; chain check row 10a OK |
| T3-5.13.0-4 | Mode field in the hand-off header | present | The close-out script writes it (line 381). No hand-off is on disk at the moment, which is ordinary |
| T3-5.13.0-5 | Two run points for the chain check | n/a | Behavioural run points (session start; step 5 of the close-out script) |

### v5.14.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.14.0-1 | Generate the close-out script and its allow-list entry | present | Script on disk, steps 0 to 8; entry in the committed settings file |
| T3-5.14.0-2a | Install the post-commit hook and set the hooks path | present | Hook executable; hooks path set; chain check row 10b OK |
| T3-5.14.0-2b | Verify the post-commit hook fires on a real commit | present | Observed 2026-10-06: a probe hand-off existed before commit 066fdfd and was gone after it. Recorded in D-048 and in the sprint 2 row of docs/05-test-points.md. |
| T3-5.14.0-3 | Row 10b in the chain check | present | Printed by the chain check in this sweep |
| T3-5.14.0-4 | Launcher degrades on a bad Keel artifact instead of printing | present | Launcher lines 275–321 and 379: missing, unreadable, stale or blocked hand-off degrade onto the living state; identity and concurrency stay terminal |
| T3-5.14.0-5 | Notify when the launcher prints on a chaining card | n/a | Behavioural session duty through the recorded channel (D-011); the launcher prints a notify line |

### v5.15.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.15.0-1a | Generate the stop hook and register it as the Stop hook | present | Script on disk; registered in the committed Claude Code settings |
| T3-5.15.0-1b | The stop hook's own allow-list entry | declined | D-048 — the hook is invoked by the harness, not through the shell tool; the user confirmed the committed allow-list by name without it (D-043). |
| T3-5.15.0-1c | Verify the stop hook fires by ending a turn with a dirty tree | present | Observed 2026-10-06: the hook blocked a live turn four times. Recorded in D-048 and in the sprint 2 row of docs/05-test-points.md. |
| T3-5.15.0-2 | Re-read anti-patterns 12e–12l | n/a | Re-read duty, no project artifact |
| T3-5.15.0-3 | Two new operating principles | n/a | Behavioural |
| T3-5.15.0-4 | Context-discipline exceptions | n/a | Behavioural |

### v5.15.1
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.15.1-1 | Stop hook: session-scoped uncommitted rule, block log keyed by repository and session | present | Generated from the current contract: cede branches at lines 477–482, session-keyed ledger entry at line 430 |
| T3-5.15.1-3 | Generate the session-identity file | present | On disk, one function, PID plus start time |
| T3-5.15.1-5 | Verify the hook in both directions, then observe it after a session restart | missing | Both directions proven in fixtures (D-048); the observation after a full session restart is scheduled S-037 (D-048). |
| T3-5.15.1-6 | Re-read anti-pattern 12m | n/a | Re-read duty |

### v5.15.2
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.15.2-1 | Stop hook: queue rule cedes, close-out discharge, queue counted in the open-items section, per-rule fingerprints | present | Lines 254–263 (open items only), 567–574 (cede and block), close-out record written by step 8 of the close-out script |
| T3-5.15.2-5 | Verify both directions for every blocking rule | present | Every blocking rule in both directions, the cede, the close-out discharge and the plan-behind state: 35 assertions in throwaway fixtures, recorded as fixture-grade evidence in D-048. |
| T3-5.15.2-6 | Operating principle "fix the class, not the instance" | n/a | Behavioural |

### v5.16.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.16.0-1 | Ask when CI runs; record the card line | present | Card line reads n/a — no forge CI; forge CI was not part of the accepted package (D-039) |
| T3-5.16.0-5 | Regenerate the workflow's trigger block | n/a | No CI workflow exists |

### v5.17.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.17.0-1 | Sprint files with frontmatter, deferred backlog, generated plan file and human index | present | Three sprint files, the deferred file, the plan file and the generated index in the sprints readme (D-040) |
| T3-5.17.0-3 | Plan checks in the linter | present | Linter check 22 OK |
| T3-5.17.0-4 | E2E card lines and published result | n/a | Absent is the default; never invented |
| T3-5.17.0-5 | One convention for machine-readable artifacts | present | The plan file carries schema keel.plan/1 and the clock file keel.clock/1, both under the keel data directory |

### v5.18.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.18.0-1 | Stop hook registered only for the tool whose schema is confirmed | present | Registered for Claude Code only; the Codex directory holds a config file and no hook file |
| T3-5.18.0-2 | Launcher point 4a — only the detected tool's own action | present | Launcher lines 157–169: no recognising row means print |
| T3-5.18.0-3 | Codex start row | n/a | Codex is not an accepted tool (D-008) |
| T3-5.18.0-4 | Chaining-model description generalised; anti-patterns 12p/12q | n/a | Wording and re-read only |
| T3-5.18.0-6 | Default reasoning for CI on private GitHub repositories | n/a | No CI (D-039) |

### v5.19.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.19.0-1a | A not-checked field on every decision asserting an impossibility | present | D-029, D-044 and D-047 carry the field; linter check 21 passes. |
| T3-5.19.0-1b | Matching linter check | present | Linter check 21 exists and ran |
| T3-5.19.0-2 | Re-measure when the user contradicts a recorded negative | n/a | Behavioural |
| T3-5.19.0-3 | Supervised chaining value | n/a | The card is chaining start (D-015) |
| T3-5.19.0-4 | Anti-patterns 12s/12t | n/a | Re-read duty |

### v5.19.1
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.19.1-1 | Wording of the supervised option | n/a | Wording only |

### v5.19.2
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.19.2-1 | Stop hook parses the porcelain output instead of slicing it | present | Lines 207–244: NUL-separated porcelain parsed, rename source consumed and skipped, an entry that cannot be stat-ed is not established. Its fixture verification (a rename, a path with a space) is part of T3-5.15.1-5 |
| T3-5.19.2-2 | Same parsing rule binds the write rule | n/a | Behavioural |
| T3-5.19.2-3 | Re-read anti-pattern 12u | n/a | Re-read duty |

### v5.20.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.20.0-1 | Launcher: per-session fire ledger and a receipt for the degraded path | present | Launcher lines 346 and 379 |
| T3-5.20.0-2 | Stop hook reads the fire ledger and stands down | present | Stop hook line 430; chain check row 7b OK |
| T3-5.20.0-3 | Chain check rows 7a and 7b; the smoke fires twice | present | Rows 7a and 7b printed in this sweep; second smoke launch at line 252 |
| T3-5.20.0-4 | Re-run the smoke after regenerating the launcher | present | The chain-verified line carries the checksum of the launcher on disk (chain check row 11) |
| T3-5.20.0-6 | Re-read anti-pattern 12v | n/a | Re-read duty |

### v5.21.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-5.21.0-0 | Lock block text refresh | present | Block rewritten from the canonical copy, stamped v6.5.0 (D-034) |
| T3-5.21.0-1 | Card line for sprints | present | Card: on |
| T3-5.21.0-2 | Create the plan where none exists | present | Sprints 1 to 3 and the deferred backlog (D-040) |
| T3-5.21.0-3 | Actual hours on every done slice | present | 17 done slices carry numeric actual hours (linter check 23) |
| T3-5.21.0-4 | Five plan checks in the linter | present | Linter check 23: five OK lines |
| T3-5.21.0-5 | Stop hook: plan-behind-the-work state | present | Stop hook lines 533–551. Its both-directions verification is part of T3-5.15.2-5 |
| T3-5.21.0-6 | Every unit of work is a slice from now on | n/a | Behavioural |
| T3-5.21.0-7 | Re-read the sprint-ledger section and anti-pattern 12w | n/a | Re-read duty |

### v6.0.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.0.0-1a | Generate the session clock script | present | On disk, executable |
| T3-6.0.0-1b | Create the sessions ledger from its template | present | On disk |
| T3-6.0.0-1c | Ignore the clock file | present | Ignore file line 56; the clock file is untracked (linter check 24) |
| T3-6.0.0-1d | Sessions ledger in the bookkeeping list of the linter and the stop hook | present | Linter line 1292; stop hook line 533 |
| T3-6.0.0-1e | Every session opens and closes with the clock | n/a | Behavioural. (The clock file shows today's session opened; it has not been closed yet) |
| T3-6.0.0-2 | Actual-source field; existing actuals marked estimated | present | All 26 slices carry the field, all estimated (linter check 24) |
| T3-6.0.0-3 | Close-out script runs the clock's end as step 0 | present | Close-out script line 152 |
| T3-6.0.0-4a | Card line for push test scope | present | Card: affected |
| T3-6.0.0-4b | Test-selection line in the technical plan | present | Plan section "Test selection" at line 152 |
| T3-6.0.0-4c | Generate the test selector | present | On disk; widening list matches the plan (linter check 25) |
| T3-6.0.0-4d | Generate the pre-push hook | present | On disk, executable, hooks path set |
| T3-6.0.0-4e | Verify both on a real diff (a dependent's tests selected, a push blocked by a red selection, an uncovered file widened) | missing | A docs-only push went through the real hook at 066fdfd; a real selection through it is scheduled S-037 after S-036 (D-044, D-048). |
| T3-6.0.0-4f | Switch non-main CI triggers to the affected selection | n/a | No CI |
| T3-6.0.0-5 | Test-selection and session-time rows in the linter | present | Linter checks 24 and 25 |
| T3-6.0.0-6 | Refresh the lock block | present | v6.5.0 block in both files (D-034) |

### v6.1.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.1.0-1 | One row file per accepted tool | present | The claude row file, complete (linter check 26) |
| T3-6.1.0-2 | Move every per-tool fact out of the shared scripts | present | Linter check 26 at the final run of this sweep: no shared keel script names a registry tool outside a comment (11 scripts). The doctor's and the selector's Claude Code facts sit in a companion file beside the row, not yet committed |
| T3-6.1.0-3 | Four registry rows in the linter | present | Linter check 26 carries all four |
| T3-6.1.0-4 | Run the hook-registration check on the existing tree first | present | Linter check 26: the row says the stop hook is the tool's own, and the tool's hook file mentions it |
| T3-6.1.0-5 | Re-run the smoke | present | The chain-verified line carries the row's checksum beside the launcher's |
| T3-6.1.0-6 | Restamp the lock | present | v6.5.0 |

### v6.2.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.2.0-1 | Regenerate the Codex row with the new flags | n/a | Only on a project that accepted Codex — not accepted (D-008) |
| T3-6.2.0-2 | Restamp the lock | present | v6.5.0 |

### v6.3.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.3.0-1 | Derive and write the security-audit card line | present | Card: required (D-040) |
| T3-6.3.0-2 | Nothing is created until an audit runs | n/a | No audit has run |
| T3-6.3.0-3 | Say now that the next release gate needs an audit covering its candidate, or a decision declining it | present | Stated in D-040 and scheduled as S-028 in sprint 3, ahead of the release gate S-032 |
| T3-6.3.0-4 | Restamp the lock | present | v6.5.0 |

### v6.4.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.4.0-1 | Session clock: active and planned hours, deviation, baseline remaining, pace projection | present | Clock script lines 378–401 and 562–600 |
| T3-6.4.0-2 | Plan generator: pace factor and projected remaining hours | present | Generator and plan file carry both fields (null: no measured completed slice yet) |
| T3-6.4.0-3 | Three new columns in the sessions ledger | present | Header carries active hours, pace factor and projected left hours |
| T3-6.4.0-4a | Arithmetic, data-eligibility and ledger checks in the linter | present | Linter check 24: five OK lines |
| T3-6.4.0-4b | Exercise the timing report in the three named cases | missing | Exercised in a scratch copy only; reading it over a real multi-session slice in this repository is scheduled S-037 (D-048). |
| T3-6.4.0-5 | Restamp the lock | present | v6.5.0 |

### v6.5.0
| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-6.5.0-1 | Cap local Playwright workers through an environment variable and record the cap in the plan | present | The Playwright config reads the worker count from PW_WORKERS with a default of 1; the plan's run-mode block records the cap and why this project keeps 1 everywhere and no separate CI branch (one shared site, no forge CI). Unexercised until the playground builds (D-044) |
| T3-6.5.0-2 | Browser MCP registered at repository level only | n/a | Only where the assistant drives the browser through an MCP server — none is registered for this project; the doctor reports no user-level registration |
| T3-6.5.0-3 | Three advisory browser rows in the doctor | present | Doctor output in this sweep: MCP scope, MCP flags, orphaned Playwright browsers |
| T3-6.5.0-4 | Restamp the lock | present | v6.5.0 |

## Totals

Counted from the tables above.

| Table | present | missing | declined | n/a | Rows |
|---|---|---|---|---|---|
| Table 1 — paths (T1-01…T1-78) | 51 | 1 | 3 | 23 | 78 |
| Table 1 — card lines (T1-C01…T1-C17) | 15 | 0 | 0 | 2 | 17 |
| Table 1 — total | 66 | 1 | 3 | 25 | 95 |
| Table 3 — v5.10.0…v6.5.0 | 71 | 3 | 1 | 32 | 107 |

## Still missing, with the decision that schedules it


- T1-61 — accessibility passes: the automated one is done (S-029, D-067); the guided one is S-030 (sprint 3) and needs the user.
- T3-5.15.1-5 — the Stop hook observed after a full session restart: S-037 (D-048).
- T3-6.0.0-4e — a real selection through the pre-push hook: S-037 after S-036 (D-044, D-048).
- T3-6.4.0-4b — the timing report over a real multi-session slice: S-037 (D-048).

## Unresolved

None. Every `missing` row above is scheduled as a named slice by a recorded decision.
