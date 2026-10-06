# Keel conformance sweep — Payment Gateway for Redsys & WooCommerce Lite

- Date: 2026-10-06
- Sweep: Phase 7 gate (release gate open, slice S-061), regenerated from scratch at `develop` `e89356b`.
- Sources (exactly three): the repository's disk as it is now; the Keel v6.5.0 manifest (Table 1 and Table 3); the decision log (D-001…D-068). The project card, the phase status and the open items were read from the living state file for the conditions only.
- Every state below was derived in this pass from a command run in this pass. The previous version of this file was overwritten; only its header and column layout were copied, no row and no state.
- Commands run for evidence (all read-only): the project linter, the chain check without its smoke mode, the environment doctor in check mode, `git status --porcelain`, `git remote -v`, `git check-ignore`, `git worktree list`, file comparisons of the lock block (both files against each other and against the canonical block of the installed skill) and of the two embedded trees, and greps of the generated scripts, hooks, settings and documents.
- The sweep ran while another session was working in the same checkout (uncommitted edits to product classes, a test, the sprint plan, the Playwright config and the playground setup script; `e89356b` not yet on `origin/develop`). Rows that depend on a moving file say so. No test suite was run and nothing was started or stopped for this sweep.

Project conditions used (read from the card and the phase table): WordPress plugin / WooCommerce extension, released (7.0.2 in production); phases 1–2 adopted (as-built), 3–4 n/a (pre-existing UI, no design handoff), 5 done in slices, 6 in progress, 7 in preparation (gate open, S-032), 8 n/a; assistant config full for Claude Code only (D-008, D-039, D-043), CI and MCP not part of it; client budget no (D-010); user guide declined (D-013); website intent no (D-005); autonomy automatic, chaining start (D-015), sprints on, security audit required (D-040); `Keel baseline:` v6.5.0 (D-049), adopted at v5.9.0 (D-012).

States: `present` (verified on disk now, evidence given) · `missing` (cites the decision and, where one exists, the slice that schedules it; a `missing` row with no decision behind it is repeated under "Unresolved") · `declined` (a recorded refusal) · `n/a` (excluding condition named).

## Table 1 — parity

The manifest's Table 1 carries no row ids; `T1-nn` is the row's position in the manifest's order (78 rows).

| # | Requirement | State | Evidence / where / decision / excluding condition |
|---|---|---|---|
| T1-01 | `docs/PROGRESS.md` — living state | present | File on disk with project card, phase status, current position (S-032), open items and deferred items. |
| T1-02 | `docs/decisions.md` — append-only decision log | present | 68 entries, D-001 to D-068, no duplicate heading id (grep, sort, uniq -d returned nothing). |
| T1-03 | `docs/lessons-learned.md` — problem to solution log | present | 10 entries, L-001 to L-010, no duplicate id; each carries a "Check added" field. |
| T1-04 | `docs/sessions.md` — one row per working session | present | 6 session rows with the Active h, Pace factor and Projected left h columns; the linter's session-time check finds the arithmetic consistent. |
| T1-05 | `scripts/keel-time` — the only reader of the clock | present | Executable; carries start, slice-start, slice-end, pause, resume, end and the pace and projection figures (grep). |
| T1-06 | docs/.keel/clock.jsonl — machine-local clock events | present | On disk (schema keel.clock/1), ignored by `.gitignore` line 56 and not tracked (`git ls-files` lists only the plan file under that directory). |
| T1-07 | Off-machine durability — a Git remote | present | `git remote -v`: origin on GitHub; card `Durability:` line says satisfied. At the moment of the sweep `e89356b` is one commit ahead of `origin/develop` (the live session has not pushed it yet). |
| T1-08 | A clean working tree at every block close | present | Mechanism in place: `develop` exists and is published, the linter fails a dirty or unpushed tree, the Stop hook blocks on both. Not a closed block right now: `git status --porcelain` lists ten modified files and one untracked file that belong to the live session, so cleanliness at the close cannot be read at this instant. |
| T1-09 | `CLAUDE.md` + `AGENTS.md` — the portability lock | present | Both carry the block stamped v6.5.0; the two blocks are identical to each other and to the canonical block of the installed skill (diff, 126 lines, no difference). |
| T1-10 | GEMINI.md or the Gemini settings mirror | n/a | Only if the user works with Gemini CLI — Claude Code is the only accepted tool (D-008); neither file exists. |
| T1-11 | `.claude/skills/keel/` + `.agents/skills/keel/` — embedded skill | present | Both trees on disk, manifest header v6.5.0 in each, identical to each other and to the installed skill (diff -rq, no difference). Card: `Keel portability:` lock + embedded v6.5.0 (D-034). |
| T1-12 | docs/00-competitive-landscape.md — competitive scan | declined | D-035: the user declined every row that concerns the competition. File absent. |
| T1-13 | `docs/01-discovery.md` — discovery, with the environment preflight | present | Carries `## Environment & test drivers (step 5a preflight)`: present, missing, impossible, screen-stealing verdict, command execution, environment restrictions, `claude` on PATH. That section was recorded while the playground was down and still says no suite could run; the doctor run in this pass reports every blocking requirement OK. |
| T1-14 | `docs/estimate.md` — estimate | present | Adoption estimate plus "Estimate v2" re-based on the sprint plan. Its Phase 5 figure (50.25 h) no longer equals the plan's slice hours (55.25 h) since four slices were added in `e89356b`; the linter fails on it. |
| T1-15 | `docs/token-ledger.md` — token usage per session | present | 12 session rows, the newest for sprint 3 (S-052, S-054, S-029, S-056, S-032 first part). |
| T1-16 | `docs/02-functional-spec.md` — functional contract | present | 71 acceptance criteria with `AC-nn` ids, flows index, change map, data model, permissions. |
| T1-17 | `docs/03-technical-plan.md` — technical plan | present | Stack, code map with 38 paths marked [E] that exist on disk (linter), change map pointer, testing block (driver per surface, run mode, element addressability, division of labour, static analysis, accessibility automation, read-back duty, test selection) and `## Environment requirements`. |
| T1-18 | `docs/threat-model.md` — threat model | present | Assumptions, defended controls with delivery states (12 IN PLACE, 3 MANUAL, 2 TO BUILD by grep) and the "Not defended" table. One control row carries two states at once ("TO BUILD or MANUAL, undetermined"). |
| T1-19 | `docs/flows/` — one file per journey | present | Six files: four checkouts, notification handling, refund. |
| T1-20 | docs/budget.md — client budget | n/a | Only if `Client budget: yes` — the card says no (D-010). |
| T1-21 | docs/spec-references/ — reference artifacts | n/a | Only if the spec records any — the functional spec has no "Reference artifacts" section and the directory does not exist. |
| T1-22 | docs/rubrics/ — judgment criteria | n/a | Only if a rubric domain was accepted — none is recorded anywhere under docs/ (grep); the directory does not exist. |
| T1-23 | docs/design/references/ — rich visual references | n/a | Only if the user holds any — none recorded; no docs/design/ directory. |
| T1-24 | Assistant rules — `.claude/rules/` | present | Three rule files: code-style, docs-discipline, security. Card: `Assistant config:` full (tools: claude). |
| T1-25 | Assistant subagents — `.claude/agents/` | present | Six agents, each with its model field: code-reviewer and security-auditor on sonnet; docs-verifier, playground-qa, test-driver and a11y-auditor on haiku (D-043). |
| T1-26 | docs/design/DESIGN-BRIEF.md | n/a | Required from Phase 3, which is n/a on the phase table: adopted project with a pre-existing UI and no design contract (D-009). |
| T1-27 | docs/design/design-handoff/ | n/a | Required from Phase 4, which is n/a on the phase table: no design handoff (D-009). |
| T1-28 | docs/BUILD-SPEC.md | n/a | Required from Phase 4, which is n/a on the phase table: no design handoff (D-009). |
| T1-29 | docs/design/design-requests/ | n/a | When the first Design Request appears — none has; the card's open items say "Open Design Requests: none". |
| T1-30 | .gitignore + .gitattributes — hygiene boundaries | present | Both at the repo root. `git check-ignore` confirms the five mandatory entries: CLAUDE.local.md, the local Claude settings file, the clock file, the update-check stamp and the continuation prompt. The personal files of other tools are not listed because no other tool is accepted (D-008). |
| T1-31 | `docs/sprints/` — one file per sprint | present | Three sprint files with `schema: keel.sprint/1` frontmatter; slices carry id, title, status, hours, actual_hours, actual_source, depends_on, criteria. The newest file is being edited by the live session. |
| T1-32 | `docs/sprints/deferred.md` — the one backlog file | present | `schema: keel.deferred/1`, items with target and reason; ids share the slice namespace (no duplicate id across the four files). |
| T1-33 | `docs/.keel/plan.json` — the derived plan | present | `schema: keel.plan/1`, generated by the plan script, tracked. At the moment of the sweep it drifts from its sources (S-061 moved in the uncommitted sprint file); the linter reports the drift. |
| T1-34 | `docs/05-test-points.md` — test-point log | present | 37 rows, 15 columns including Criterion, Coverage, Red first and Evidence; the linter finds every Coverage and Red first cell in its enum. |
| T1-35 | `docs/api/INDEX.md` — one line per public surface | present | 48 rows; the linter finds each one in the source and each document under the api and reference directories indexed. |
| T1-36 | `docs/keel-conformance.md` — this sweep | present | This file, regenerated in this pass from the manifest, the disk and the decision log. |
| T1-37 | `docs/playground.md` — playground | present | Start, reset, stop, try-it steps, the setup script as the seed, `last verified: 2026-10-06`. |
| T1-38 | `scripts/keel-verify` — release linter | present | Executable; 29 checks; run in this pass. |
| T1-39 | `scripts/keel-affected-tests` — push-time selection | present | Executable; the linter's synthetic uncovered file makes it widen (scope: affected, 3 of 120 tests). |
| T1-40 | `.githooks/pre-push` + core.hooksPath | present | Executable, calls the selector, skips tag refs; `git config core.hooksPath` is .githooks. |
| T1-41 | `scripts/keel-doctor` — environment doctor | present | Executable; check, plan, fix and json modes; run in check mode in this pass: all blocking requirements OK. |
| T1-42 | Build and minify script — `bin/build-assets.js` | present | Named by the technical plan's "Front-end asset build contract" and run by `npm run build:assets` (D-054). It lives under bin/, not under scripts/. The linter finds the three stylesheets and the built script in sync with a fresh build. |
| T1-43 | `scripts/keel-handoff-verify` — courier checks | present | Executable; containment, release and baton handling present (grep); its allow-list entry is in `.claude/settings.json`. |
| T1-44 | Single-lane lock (card: Chaining start) | present | Taken by the hand-off verifier; the chain check's row 9 finds the lane directory outside the repository and writable, and names its current holder. |
| T1-45 | `scripts/keel-tools/claude.sh` — tool registry row | present | Declares the nine fields and both functions (linter check 26); evidence VERIFIED, tier start. One accepted tool, one row file. |
| T1-46 | `scripts/keel-continue` — launcher | present | Executable; sources the registry row, claims the session entry before firing, releases the lane first; the chain check finds none of the four measured launch bugs. |
| T1-47 | `scripts/keel-close` — the close-out | present | Executable; runs the session clock end, the linter, the chain check and the launcher in order; its allow-list entry is in `.claude/settings.json`. |
| T1-48 | `.githooks/post-commit` + core.hooksPath | present | Executable; deletes the continuation prompt and nothing else; D-048 records it deleting a hand-off on a real commit. |
| T1-49 | `scripts/keel-stop-hook` + its Stop registration in `.claude/settings.json` | present | Executable and registered as the Stop hook for Claude Code only. Its allow-list entry is not added: declined on the record (D-048), because the harness invokes the hook, not the shell tool. D-048 records it blocking a live turn four times. |
| T1-50 | `scripts/keel-session-pid.sh` — session identity | present | One function, `keel_session_pid`; not executable by design (sourced). |
| T1-51 | `scripts/keel-chain-check` — the chaining contract | present | Executable; run in this pass without its smoke mode: every row OK, VERDICT READY. |
| T1-52 | `Chaining model:` line on the project card | present | opus (D-037). |
| T1-53 | `Chain verified:` line on the project card | present | 2026-10-06, tier start, Keel 6.5.0, launcher checksum b8796f66873c0ff2; the chain check's row 11 finds the checksum still matching the launcher on disk. |
| T1-54 | `.githooks/pre-commit` — confidential-data gate | present | Executable; exempts the hooks directory and both embedded skill trees; active through core.hooksPath (D-039). |
| T1-55 | Permission allow-list — `.claude/settings.json` | present | Committed allow-list confirmed by the user by name (D-043): playground start and stop, the three suites, the build, the read-and-verify Keel scripts, the gate's tree scan, edits under tests/. |
| T1-56 | CI workflow | declined | D-039: forge CI was not part of the package the user accepted. No .github directory; card `CI runs on:` n/a. |
| T1-57 | MCP registration | n/a | Only if the technical plan defines development MCP servers — it defines none ("Browser MCP: none is registered for this project"); no .mcp.json. |
| T1-58 | `docs/architecture.md` — consolidated architecture | present | Overview, components, request and data flow, external dependencies, extension points, known gaps. |
| T1-59 | `docs/api/`, `docs/usage/`, `docs/reference/` — documentation layout | present | api: index and readme; usage: configuration, examples, getting started, installation; reference: classes, endpoints, functions, hooks and extension points. |
| T1-60 | `docs/security.md` — consolidated security posture | present | Profile, posture summary, pointer to the threat model, process gaps. |
| T1-61 | docs/accessibility.md — automated results plus the guided assistive-technology pass, per item | missing | The file exists and holds the automated pass (S-029, D-067), but its guided-pass section says "Not run (S-030)" and holds the eleven-step script, not results. D-067 records the pass as not run; S-030 is `not-started` and waits for a person with a screen reader. |
| T1-62 | `README.md` — the repository's front door | present | At the repo root. |
| T1-63 | guide/ — end-user HTML guide | declined | D-013: the end-user guide is declined for now. Card `User guide:` says so; no guide/ directory. |
| T1-64 | guide/_theme/ + guide/brand/ + the theme marker | declined | D-013: no guide, so no vendored theme. Card `Docs theme:` n/a. |
| T1-65 | docs/07-release.md — release record including the full-suite re-run on the candidate and the linter output | missing | The file exists and is open (D-068, S-032 `in-progress`): it records the gate item by item, but the entire-suite run with `scope: full` on the final candidate, the linter output on that tree, the real-environment pass and the self-audit results are not in it yet, and no version is approved (7.1.0 proposed). |
| T1-66 | `docs/security-audit.md` — counts-only audit log | present | Two rows: the full run at `e50ab39` and the scoped run at `476d52e`. Card: `Security audit:` required (D-040). |
| T1-67 | docs/security-audit/ listed in .gitignore | present | `.gitignore` line 59; the two run directories are on disk and untracked. |
| T1-68 | Site documentation set | n/a | Website intent only — the card says no (D-005). |
| T1-69 | Site art direction | n/a | Website intent only — the card says no (D-005). |
| T1-70 | Machine-local art ledger | n/a | Website intent only — the card says no (D-005). |
| T1-71 | Site launch report | n/a | Website intent only — the card says no (D-005). |
| T1-72 | Site operations record | n/a | Website intent only — the card says no (D-005). |
| T1-73 | docs/.keel/e2e-status.json | n/a | Only if the card carries an `E2E:` line — it carries none (absent is the default). |
| T1-74 | docs/.keel/e2e-history.jsonl | n/a | Optional, and only where `E2E:` exists — the card carries no such line. |
| T1-75 | docs/.keel/slices/ worker reports | n/a | Only if the project fans work out over git worktrees — `git worktree list` shows the main tree only. |
| T1-76 | `docs/issues.md` — forge issue log | present | Inventory, entries, and the `Last inbound sweep:` header line stamped 2026-10-06 19:20. |
| T1-77 | docs/old/ — archive | n/a | When archiving starts — nothing has been archived; the directory does not exist. |
| T1-78 | `docs/04-adoption-audit.md` — gap audit | present | On disk; adopted project. |

### Project-card lines

One row per line of the card template. `T1-52` and `T1-53` above already cover the two chaining-proof lines.

| # | Requirement | State | Evidence / where / decision / excluding condition |
|---|---|---|---|
| T1-C01 | `Name / one-line purpose:` | present | Card line present. |
| T1-C02 | `Project type:` | present | WordPress plugin / WooCommerce extension (D-001). |
| T1-C03 | `Stack & target platform(s):` | present | PHP, WordPress, WooCommerce, the Blocks build. |
| T1-C04 | `License:` | present | GPL-2.0-or-later (D-003). |
| T1-C05 | `Docs language:` | present | English (D-004). |
| T1-C06 | `Security profile:` | present | The WordPress profile. |
| T1-C07 | `Security audit:` | present | required, derived (D-040). |
| T1-C08 | `Accessibility:` | present | WCAG 2.2 AA floor (D-007). |
| T1-C09 | `i18n:` | present | multi, base English, shipped locale es_ES. |
| T1-C10 | `Installed base:` | present | In production, 7.0.2. |
| T1-C11 | `Design system:` | present | one-off / n/a (D-009). |
| T1-C12 | `Keel portability:` | present | lock + embedded v6.5.0 (D-034). |
| T1-C13 | `Assistant config:` | present | full (tools: claude) (D-039, D-043). |
| T1-C14 | E2E card line | n/a | Absent is the default and means the feature does not exist for this project; the card carries no such line. |
| T1-C15 | E2E env card line | n/a | Optional, only where the E2E line exists; the card carries neither. |
| T1-C16 | `CI runs on:` | present | n/a — no forge CI. |
| T1-C17 | `Models:` | present | orchestrator, reviewer and mechanical map (D-043). |
| T1-C18 | `Keel baseline:` | present | v6.5.0 (D-049) — equal to the running Keel. |
| T1-C19 | `Website intent:` | present | no (D-005). |
| T1-C20 | `Client budget:` | present | no (D-010). |
| T1-C21 | `User guide:` | present | declined for now (D-013). |
| T1-C22 | `Docs theme:` | present | n/a — no guide. |
| T1-C23 | `Test-first policy:` | present | pure-logic (D-036). |
| T1-C24 | `Push test scope:` | present | affected. |
| T1-C25 | `Sprints:` | present | on. |
| T1-C26 | `Durability:` | present | git remote origin — satisfied. |
| T1-C27 | `Autonomy:` | present | automatic, issues after-sprint, Issue capture off (D-011). |
| T1-C28 | `Issue sweep interval:` | present | 24h, on the Autonomy line. |
| T1-C29 | `Branches:` | present | Integration branch develop. Its text still says "current work: adoption on develop", which the phase table contradicts (adoption is complete). |
| T1-C30 | `Notify:` | present | PushNotification tool (D-011). |
| T1-C31 | `Chaining:` | present | start (D-015). |

## Table 3 — per-version actions

Two parts, by the manifest's own rule: Table 3 lists what a reconciliation applies "for every version newer than the project's Keel baseline". The project was adopted at v5.9.0 (D-012) and reconciled v5.9.0 → v6.5.0 (D-049), so the applicable delta is v5.10.0 → v6.5.0. The versions at or before v5.9.0 were walked as well, one row each, and are `n/a` by that rule; what the disk shows for them is written in the row, and the three things found absent there are listed under "Observed outside the applicable set".

A version whose actions end in different states has one row per state (suffix a, b, c).

### Applicable delta — v5.10.0 → v6.5.0

| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-v5.10.0 | Re-ask the chaining question where the card is automatic and chaining off | n/a | Projects already on prefill or start need nothing — the card says start (D-015). |
| T3-v5.10.1 | Closed list of what may stop a chain | n/a | No per-project action: the rules live in the skill files every session reads. |
| T3-v5.10.2 | The script decides whether to chain | n/a | No per-project action. The launcher on disk does re-check the tool's command before firing (grep). |
| T3-v5.10.3 | Launcher contract 6a and 6b; per-user installer directory in env.PATH | present | The machine-local settings carry the literal per-user bin directory first in env.PATH; the chain check's rows 6, 8 and 10 are OK. |
| T3-v5.11.0 | Test-first policy card line, Red first column, the three red checks | present | Card line pure-logic (D-036); the column exists with 37 valid cells; linter check 17 runs the enum and failure-output checks. |
| T3-v5.12.0 | Art direction for websites | n/a | Applies to website projects only — website intent is no (D-005). |
| T3-v5.13.0 | Chain check script with its allow-list entry, smoke run, the two card lines, Mode field in the hand-off | present | Script and entry on disk; both card lines present; D-048 records the smoke run observed for real; the close-out script writes the Mode field (grep). |
| T3-v5.14.0 | Close-out script with its entry; post-commit hook; chain check row 10b | present | All three on disk; row 10b OK in this pass. |
| T3-v5.15.0-a | Stop hook generated, registered and seen firing | present | Registered in the committed Claude settings; D-048 records it blocking a live turn four times. |
| T3-v5.15.0-b | Allow-list entry for running the Stop hook by hand | declined | D-048: not added; the harness invokes the hook, and the user confirmed the allow-list without it (D-043). |
| T3-v5.15.1-a | Stop hook scoped to the session; block log keyed by repository and session; session identity file | present | Cede logic and the session-keyed block log are in the hook (grep); the identity file is on disk. |
| T3-v5.15.1-b | Observe the fixed hook firing after a session restart | missing | D-048: proven in fixtures only; scheduled as S-037, which is `not-started`. |
| T3-v5.15.2 | Queue block cedes and is discharged by a completed close-out; queue counted in Open items only | present | The hook reads the Open items section and the fire ledger (grep). Both directions were proven in fixtures (D-048); the observation after a restart is the same open item as T3-v5.15.1-b. |
| T3-v5.16.0 | `CI runs on:` card line | present | n/a — no forge CI (CI declined, D-039). No workflow exists to regenerate. |
| T3-v5.17.0-a | Sprint frontmatter, deferred file, derived plan, the plan checks | present | Present as in T1-31 to T1-33; linter check 22 runs the plan checks (and currently reports the in-flight drift). |
| T3-v5.17.0-b | E2E card lines and status file | n/a | Absent is the default — the card carries no E2E line. |
| T3-v5.18.0 | Stop hook registered only where the tool's contract is confirmed; launcher fires only the detected tool's row | present | Registered in the Claude settings only; one row file, for Claude, with its stop-hook field yes; linter check 26 finds no tool name in a shared script. The committed Codex config file holds an environment policy only, no hook. |
| T3-v5.19.0-a | Not checked field on entries asserting an impossibility, with its linter check | present | Linter check 21: 8 such entries, each with the field. |
| T3-v5.19.0-b | Card value supervised | n/a | Only where something outside the project continues it — the card says start. |
| T3-v5.19.1 | Wording of the supervised option | n/a | Wording only, no per-project action. |
| T3-v5.19.2 | Stop hook parses the porcelain line (rename, quoted path) | present | The hook splits on the rename arrow (grep); D-048 records the rename and path-with-space fixtures. |
| T3-v5.20.0 | Session fire ledger in the launcher, the hook and the chain check; smoke re-run | present | Ledger claim in the launcher and read in the hook (grep); chain check rows 7a and 7b OK; row 11 finds the proof not stale. |
| T3-v5.21.0 | `Sprints:` card line, plan created, actual hours on done slices, plan checks, the hook's plan state | present | Card line on; linter check 23: plan not behind the work, 38 done slices each with numeric actual hours. |
| T3-v6.0.0-a | Session clock, sessions file, clock file ignored, actual_source, close-out step 0, Push test scope, test selection, selector and pre-push hook, lock refreshed | present | Each on disk as in T1-04 to T1-06, T1-39, T1-40 and T1-C24; the plan's "Test selection" section exists; the close-out script calls the clock's end step. |
| T3-v6.0.0-b | Selector and pre-push hook verified on a real push | missing | D-048: a real push through the hook with a real selection is scheduled as S-037, `not-started`. The release record does show a real selection run widened to 120 of 120 tests, which is not the push. |
| T3-v6.1.0 | Tool registry as data; four linter rows; smoke re-run | present | As T1-45; linter check 26 OK; the card's proof line carries the row checksum. |
| T3-v6.2.0 | Codex launch flags | n/a | Structural only on a project that accepted Codex — Claude Code is the only accepted tool (D-008). |
| T3-v6.3.0 | `Security audit:` card line; run directory ignored; counts-only log; audit covering the candidate | present | Card line required (D-040); ignore line and log on disk; two runs recorded. The newest audited commit is `476d52e`, HEAD is `e89356b`, and product classes are being edited in the working tree right now: whether the audit covers the final candidate is a gate item, not settled by this row. |
| T3-v6.4.0-a | Clock script with pace and projection; plan generator fields; three new sessions columns; linter checks | present | Greps of the clock and plan scripts; the columns exist; linter check 24 runs the arithmetic. |
| T3-v6.4.0-b | Timing report exercised over a real multi-session slice | missing | D-048: proven in fixtures; the reading in this repository is scheduled as S-037, `not-started`. |
| T3-v6.5.0-a | Playwright worker cap recorded; doctor's three browser rows; lock restamped | present | The config caps workers through PW_WORKERS with a default of 1 (stricter than the reference default of 2, with the reason recorded in the plan's run-mode block); the doctor prints the three advisory rows; lock stamped v6.5.0. The config file is being edited by the live session. |
| T3-v6.5.0-b | Browser MCP registration shape | n/a | Only where the assistant drives the browser through an MCP server — none is registered (no .mcp.json; doctor row OK). |

### At or before the adoption baseline — v1.10.0 → v5.9.0 (walked, not applicable)

Excluding condition for every row of this table: Table 3 applies to versions newer than the project's baseline; this project was adopted at v5.9.0 (D-012), where the Table 1 parity above is the check. The last column says what the disk shows anyway.

| # | Action | State | Evidence / condition |
|---|---|---|---|
| T3-v1.10.0 | Config and baseline card lines; config package offered | n/a | At or before the adoption baseline. Disk: both card lines exist; package accepted (D-039). |
| T3-v1.11.0 | Lock block version stamp | n/a | At or before the adoption baseline. Disk: stamped v6.5.0. |
| T3-v1.12.0 | None structural | n/a | None structural. |
| T3-v1.12.1 | Update-check stamp ignored | n/a | At or before the adoption baseline. Disk: .gitignore line 39. |
| T3-v1.13.0 | None structural | n/a | None structural. |
| T3-v2.0.0 | Client budget question, linter and playground reset, CI offer, debug log with a switch, user-guide question | n/a | At or before the adoption baseline. Disk: budget no (D-010); linter and playground reset exist; CI declined (D-039); each of the four gateways has a debug setting defaulting to no; guide declined (D-013). |
| T3-v2.1.0 | None structural | n/a | None structural; no design handoff exists. |
| T3-v3.0.0 | Assistant list, second lock file, second embedded tree, gate exemption, export-ignore of config trees | n/a | At or before the adoption baseline. Disk: Claude only (D-008); both lock files and both trees exist; the gate exempts both skill trees; .gitattributes export-ignores the Claude, agents, Codex and hooks trees and both lock files. |
| T3-v3.1.0 | Comments check in the reviewer agent and the code-style rule | n/a | At or before the adoption baseline. Disk: both carry the comments line; no guide, so no guide agent. |
| T3-v3.2.0 | Docs theme card line | n/a | At or before the adoption baseline. Disk: line present, n/a — no guide. |
| T3-v3.2.1 | None structural | n/a | None structural. |
| T3-v3.3.0 | Models card line and model fields | n/a | At or before the adoption baseline. Disk: line present (D-043); six agents carry a model field. |
| T3-v3.4.0 | Source-first assets and a local build script | n/a | At or before the adoption baseline. Disk: applied by S-027 (D-054); linter check 11 OK. |
| T3-v3.5.0 | Docs follow the code; the rubric question asked once and its answer recorded | n/a | At or before the adoption baseline. Disk: no record of the rubric question or of an answer anywhere under docs/ — see "Observed outside the applicable set". |
| T3-v4.0.0 | Anti-patterns self-audit, code-map markers, change map, threat model, linter checks, competitive confrontation | n/a | At or before the adoption baseline. Disk: markers, change map, threat model and the linter checks exist; the confrontation is declined (D-035); the self-audit is being run in this same gate pass (S-061) and the release record still says "Not run yet". |
| T3-v5.0.0 | Conformance sweep, environment requirements and doctor, driver per surface, AC ids and coverage columns, test-driver agent, allow-list coverage | n/a | At or before the adoption baseline. Disk: all present; 23 of the 71 criteria have no test-point row by D-045 and are scheduled as S-060. |
| T3-v5.1.0 | Video and trace recording on; a headed script; run mode recorded | n/a | At or before the adoption baseline. Disk: run mode recorded; trace is retain-on-failure only, video is off and no headed script exists (the plan marks both TO BUILD) — see "Observed outside the applicable set". |
| T3-v5.2.0 | Parallel verifier dispatch; test-driver with Edit | n/a | At or before the adoption baseline. Disk: the test-driver agent lists Edit in its tools. |
| T3-v5.3.0 | Hand-off file ignored, verifier script and entry, chaining question, single lane | n/a | At or before the adoption baseline. Disk: all present (T1-43, T1-44, T1-C31). |
| T3-v5.3.1 | Verifier at the scaffold; lane taken by it | n/a | At or before the adoption baseline. Disk: present. |
| T3-v5.3.2 | The assistant's command on PATH as a gate for start | n/a | At or before the adoption baseline. Disk: chain check row 8 OK. |
| T3-v5.3.3 | The sweep never reads its predecessor; orphan lane recovered | n/a | At or before the adoption baseline. This sweep consulted no previous row; the verifier handles an orphan lane (grep). |
| T3-v5.4.0 | Two discovery lines; worktree fan-out reports | n/a | At or before the adoption baseline. Disk: both lines are in the discovery document; no fan-out. |
| T3-v5.4.1 | Fan-out dispatch; probe corroboration | n/a | At or before the adoption baseline. Disk: the doctor distinguishes its states; no fan-out. |
| T3-v5.5.0 | Setup batch card lines, env.PATH and permission mode, develop flow, launcher receipt, three-beat issue replies | n/a | At or before the adoption baseline. Disk: Autonomy, Branches and Notify lines (D-011); literal env.PATH and mode auto; develop published; receipt and breaker in the launcher; the issue log carries the reply fields. |
| T3-v5.5.1 | Fan-out permission mode | n/a | At or before the adoption baseline, and no fan-out. |
| T3-v5.5.2 | Launcher changes directory first | n/a | At or before the adoption baseline. Disk: the Claude row writes the directory change into the launch script. |
| T3-v5.6.0 | Durability card line; the two linter checks | n/a | At or before the adoption baseline. Disk: line present; linter checks 19 and 20 exist (and fail right now on the live session's unpushed commit and uncommitted files). |
| T3-v5.7.0 | Hand-off at every sprint close; lock content refresh | n/a | At or before the adoption baseline. Disk: lock identical to the canonical block. |
| T3-v5.8.0 | Issue sweep interval; last-sweep header line | n/a | At or before the adoption baseline. Disk: both present. |
| T3-v5.8.1 | Launcher and verifier regenerated with release and baton | n/a | At or before the adoption baseline. Disk: both present (grep). |
| T3-v5.9.0 | Forge allow block merged into the machine-local settings; Issue capture card value | n/a | At or before the adoption baseline. Disk: Issue capture off is on the card (D-011); the machine-local allow list holds one forge entry (issue view) of the block the manifest lists — see "Observed outside the applicable set". |

## Totals

Counted from the tables above by the linter (see "Linter result").

| Table | present | missing | declined | n/a | rows |
|---|---|---|---|---|---|
| Table 1 — paths | 53 | 2 | 4 | 19 | 78 |
| Table 1 — card lines | 29 | 0 | 0 | 2 | 31 |
| Table 3 — applicable delta | 19 | 3 | 1 | 9 | 32 |
| Table 3 — at or before the baseline | 0 | 0 | 0 | 32 | 32 |
| All | 101 | 5 | 5 | 62 | 173 |

## Still missing, with the decision that schedules it

- T1-61 — the guided assistive-technology pass has not run; the record holds its script only. D-067; slice S-030, waiting for a person with a screen reader.
- T1-65 — the release record is open: no entire-suite run on the final candidate, no linter output on that tree, no real-environment pass, no self-audit results, no approved version. D-068; slice S-032.
- T3-v5.15.1-b — the Stop hook has not been observed firing after a session restart. D-048; slice S-037.
- T3-v6.0.0-b — no real push through the pre-push hook with a real selection is recorded. D-048; slice S-037.
- T3-v6.4.0-b — the timing report has not been read in this repository over a real multi-session slice. D-048; slice S-037.

## Observed outside the applicable set

Three things the manifest asks of versions at or before the adoption baseline are absent on disk and have no decision behind them. They are not rows of the applicable set, so they carry no state; they are listed for the user to decide, one by one.

- v3.5.0 — the rubric question was never recorded as asked, and no answer ("a rubric" or "none for this project") exists.
- v5.1.0 — browser recording is partial: trace on failure only, no video, and no headed script in the package file. The technical plan says so itself (TO BUILD).
- v5.9.0 — the machine-local allow list carries one forge entry (issue view); the rest of the block (issue list, comment, create, edit, close, reopen; the pull-request and label commands) is absent, so a reply on an issue still meets a permission dialog.

## Unresolved

None inside the applicable set: every `missing` row cites a recorded decision and a slice.

Two rows whose state rests on something this pass could not read at rest:

- T1-08 — the working tree is dirty because another session is mid-block in this checkout; cleanliness at the block close is that session's to show.
- T3-v6.3.0 — the security audit covers `476d52e`; whether it covers the final candidate depends on what the live session commits.
