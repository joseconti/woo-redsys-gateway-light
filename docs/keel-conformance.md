# Keel conformance sweep

- Date: 2026-10-21
- Reconciliation: Keel v6.5.0 → v7.3.2 (slice S-084)
- Sources, and only these: the repository's disk (checked with commands), `~/.claude/skills/keel/MANIFEST.md` v7.3.2 (Table 1 and Table 3 rows v6.6.0–v7.3.2, read together with `CHANGELOG.md` 6.6.0–7.3.2 and `references/project-state.md` / `references/parallel-development.md`), and `docs/decisions.md`. The previous sweep was not read.
- Position: Phase 5 development / Phase 7 release in preparation (sprint 3), Phase 6 in progress; adopted project; no website (D-005), no client budget (D-010), no user guide (D-013), no design system (D-009); Claude Code only; `Autonomy: automatic`; `Chaining: start`; `Sprints: on`; `Security audit: required`.
- States: `present` / `missing` / `declined` (+ D-entry) / `n/a` (+ the condition that excludes it).

| Row id | Requirement | State | Where / condition |
|---|---|---|---|
| T1-01 | `docs/PROGRESS.md` | present | `docs/PROGRESS.md` (card lines 5–37) |
| T1-02 | `docs/decisions.md` | present | `docs/decisions.md` |
| T1-03 | `docs/lessons-learned.md` | present | `docs/lessons-learned.md` |
| T1-04 | `docs/sessions.md` | present | `docs/sessions.md` (columns: see T3 v7.3.1 row 3) |
| T1-05 | `scripts/keel-time` | present | `scripts/keel-time` (pre-v7.3.1 rules: see T3 v7.3.1 row 2) |
| T1-06 | `scripts/keel-clock` | missing | not in `scripts/`; Python 3.9.6 available (see T3 v7.3.1 row 1); owner approved applying it (D-093), scheduled in S-086 |
| T1-07 | `docs/.keel/clock.jsonl`, gitignored | present | exists; `.gitignore:56` |
| T1-08 | Off-machine durability | present | `origin` → GitHub; card `Durability:` line |
| T1-09 | Clean working tree at block close | present | block in progress (S-084 open on `develop`); the close commits it |
| T1-10 | `CLAUDE.md` + `AGENTS.md` lock block | present | restamp v6.5.0 → v7.3.2; applied in S-084 (D-093) |
| T1-11 | `GEMINI.md` / `.gemini/settings.json` | n/a | Gemini CLI not in use (card `Assistant config: … tools: claude`) |
| T1-12 | `.claude/skills/keel/` + `.agents/skills/keel/` | present | both trees replaced with v7.3.2; card `Keel portability:`; applied in S-084 (D-093) |
| T1-13 | `docs/00-competitive-landscape.md` | declined | D-035 |
| T1-14 | `docs/01-discovery.md` incl. `## Environment & test drivers` | present | `docs/01-discovery.md:52` |
| T1-15 | `docs/estimate.md` | present | `docs/estimate.md` |
| T1-16 | `docs/token-ledger.md` | present | `docs/token-ledger.md` |
| T1-17 | `docs/02-functional-spec.md` with `AC-nn` IDs | present | 79 `AC-` references |
| T1-18 | `docs/03-technical-plan.md` (code map `[E]`, change map, testing, `## Environment requirements`) | present | sections at lines 13, 60, 72, 182 |
| T1-19 | `docs/threat-model.md` with delivery states and "Not defended" | present | `docs/threat-model.md` |
| T1-20 | `docs/flows/` | present | 6 flow files |
| T1-21 | `docs/budget.md` | n/a | `Client budget: no` (D-010) |
| T1-22 | `docs/spec-references/` | n/a | the spec records no reference artifacts |
| T1-23 | `docs/rubrics/` | n/a | no rubric domain accepted |
| T1-24 | `docs/design/references/` | n/a | the user holds no visual references; pre-existing UI, Phase 3 n/a |
| T1-25 | Assistant rules `.claude/rules/` | present | 3 rule files; card `Assistant config: full` (D-039) |
| T1-26 | Assistant subagents `.claude/agents/` | present | 6 agents |
| T1-27 | `docs/design/DESIGN-BRIEF.md` | n/a | Phase 3 n/a — pre-existing UI, no design contract (PROGRESS phase status) |
| T1-28 | `docs/design/design-handoff/` | n/a | Phase 4 n/a (same) |
| T1-29 | `docs/BUILD-SPEC.md` | n/a | Phase 4 n/a (same) |
| T1-30 | `docs/design/design-requests/` | n/a | no Design Request ever raised |
| T1-31 | `.gitignore` + `.gitattributes` mandatory entries | present | `.gitignore` 39–42, 56, 59; `.gitattributes` present (parallel exclusions: n/a, no `auto`) |
| T1-32 | `docs/sprints/` | present | sprint-1..3 (`keel.sprint/1`) |
| T1-33 | `docs/sprints/deferred.md` | present | `keel.deferred/1` |
| T1-34 | `docs/.keel/plan.json` | present | `keel.plan/1` (no `original_hours`/reconciliation fields: see T3 v6.6.0) |
| T1-35 | `scripts/keel-parallel` | missing | `Parallel development: auto` chosen by the owner (D-093); scheduled in S-088 |
| T1-36 | `docs/.keel/parallel/` | missing | `Parallel development: auto` chosen by the owner (D-093); scheduled in S-088 |
| T1-37 | `docs/.keel/slices/…` | n/a | no parallel writing worker has run |
| T1-38 | `docs/05-test-points.md` (Criterion, Coverage, Red first) | present | header line 9 |
| T1-39 | `docs/api/INDEX.md` | present | `docs/api/INDEX.md` |
| T1-40 | `docs/keel-conformance.md` | present | this file |
| T1-41 | `docs/playground.md` | present | `last verified:` line 3 |
| T1-42 | `scripts/keel-verify` | present | `scripts/keel-verify` |
| T1-43 | `scripts/playground-tunnel` | missing | Redsys/Bizum/Google Pay/Inespay call `?wc-api=WC_Gateway_<id>` from outside (card `Security audit:` line); script absent (see T3 v7.2.0); owner approved applying it (D-093), scheduled in S-087 |
| T1-44 | `scripts/keel-affected-tests` | present | `scripts/keel-affected-tests` |
| T1-45 | `.githooks/pre-push` + `core.hooksPath` | present | `core.hooksPath=.githooks`; pre-push calls `keel-affected-tests` |
| T1-46 | `scripts/keel-doctor` | present | `scripts/keel-doctor` |
| T1-47 | Build/minify script | present | `bin/build-assets.js` via `npm run build:css` (technical plan "Front-end asset build contract") |
| T1-48 | `scripts/keel-handoff-verify` | present | `scripts/keel-handoff-verify`; allow-list entry in `.claude/settings.json` |
| T1-49 | Single-lane lock (`Chaining: start`) | present | implemented in `scripts/keel-handoff-verify` (lines 32–53, `--release`) |
| T1-50 | `scripts/keel-tools/<tool>.sh` | present | `scripts/keel-tools/claude.sh` (+ `claude.doctor.sh`) |
| T1-51 | `scripts/keel-continue` | present | `scripts/keel-continue` |
| T1-52 | `scripts/keel-close` | present | allow-list entry `.claude/settings.json:25` |
| T1-53 | `.githooks/post-commit` | present | deletes `docs/continuation-prompt.md` only |
| T1-54 | `scripts/keel-stop-hook` + `Stop` registration | present | `.claude/settings.json:33–38`; its manual-run allow-list entry declined (D-048) |
| T1-55 | `scripts/keel-session-pid.sh` | present | `scripts/keel-session-pid.sh` |
| T1-56 | `scripts/keel-chain-check` | present | allow-list entry `.claude/settings.json:26` |
| T1-57 | `Chaining model:` card line | present | `Chaining model: opus` (D-037) |
| T1-58 | `Chain verified:` card line | present | checksum `b8796f66873c0ff2` still matches `scripts/keel-continue`; stamp says Keel 6.5.0 |
| T1-59 | `.githooks/pre-commit` | present | `.githooks/pre-commit`, active |
| T1-60 | Permission allow-list | present | `.claude/settings.json` |
| T1-61 | CI workflow | declined | D-039 (also D-043); card `CI runs on: n/a` |
| T1-62 | MCP registration | n/a | the technical plan defines no dev MCP servers |
| T1-63 | `docs/architecture.md` | present | `docs/architecture.md` |
| T1-64 | `docs/api/`, `docs/usage/`, `docs/reference/` | present | all three populated |
| T1-65 | `docs/security.md` | present | `docs/security.md` |
| T1-66 | `docs/accessibility.md` | present | `docs/accessibility.md` |
| T1-67 | `README.md` | present | repo root |
| T1-68 | `guide/` | declined | D-013 (`User guide: declined for now`) |
| T1-69 | `guide/_theme/` + `guide/brand/` | declined | D-013; `Docs theme: n/a` |
| T1-70 | `docs/07-release.md` | present | `docs/07-release.md` (release in preparation) |
| T1-71 | `docs/security-audit.md` | present | `Security audit: required` (D-040) |
| T1-72 | `docs/security-audit/` gitignored | present | `.gitignore:59` |
| T1-73 | `<site-docs>/` (`docs/site/` or the site's own repo) | n/a | no website intent (D-005) |
| T1-74 | `<site-docs>/design/design-handoff/SPEC/art-direction.md` | n/a | no website intent (D-005) |
| T1-75 | `~/.keel/art-ledger.md` | n/a | no website intent (D-005) |
| T1-76 | `<site-docs>/launch-report.md` | n/a | no website intent (D-005) |
| T1-77 | `<site-docs>/operations.md` | n/a | no website intent (D-005) |
| T1-78 | `docs/.keel/e2e-status.json` | n/a | the card carries no `E2E:` line |
| T1-79 | `docs/.keel/e2e-history.jsonl` | n/a | the card carries no `E2E:` line |
| T1-80 | `docs/issues.md` with `Last inbound sweep:` | present | `docs/issues.md:5` |
| T1-81 | `docs/old/` | n/a | archiving has not started |
| T1-82 | `docs/04-adoption-audit.md` | present | adopted project |
| T1-83 | `CLAUDE_CODE_EXPERIMENTAL_AGENTS_TEAMS: "1"` in `.claude/settings.local.json` | present | key present in `env`; takes effect at the next Claude Code start; applied in S-084 (D-093) |
| T1-84 | `Parallel development:` card line (`auto; max_workers: max` or `off (D-0XX)`) | present | `docs/PROGRESS.md` card: `auto; max_workers: max` (owner, D-093) |
| C-01 | Card lines `Keel baseline:` / `Keel portability:` | present | being updated to v7.3.2; applied in S-084 (D-093) |
| C-02 | Card lines `Assistant config:`, `Client budget:`, `User guide:`, `Docs theme:`, `Models:`, `Chaining:`, `Issue sweep interval:`, `Test-first policy:`, `Sprints:`, `Push test scope:`, `Security audit:` | present | `docs/PROGRESS.md` lines 16–37 |
| T3-v6.6.0-1 | Legacy `hours` → immutable `original_hours`, actuals preserved | missing | sprint-1..3 and `deferred.md` carry `hours:` only (0 `original_hours`); owner approved applying it (D-093), scheduled in S-085 |
| T3-v6.6.0-2 | Open slices: exclusive ownership + explicit `residual_hours`; `reconciliation_status` | missing | open slices S-032, S-084 carry neither field; owner approved applying it (D-093), scheduled in S-085 |
| T3-v6.6.0-3 | Regenerate `scripts/keel-time`, plan generator (`scripts/keel-plan`), `scripts/keel-verify` for original/residual, `added / extracted / re-estimated`, balance equation | missing | 0 occurrences of `original_hours` / `reconciliation_status` in the three scripts; owner approved applying it (D-093), scheduled in S-085 |
| T3-v6.6.0-4 | Reconcile existing parent/child decompositions | n/a | no slice declares a parent or extraction |
| T3-v6.6.0-5 | Restamp lock | present | applied in S-084 (D-093) |
| T3-v7.0.0-1 | `Parallel development:` card line | present | `docs/PROGRESS.md` card: `auto; max_workers: max` (owner, D-093) |
| T3-v7.0.0-2 | Active sprint → `keel.sprint/2` | missing | `Parallel development: auto` chosen by the owner (D-093); scheduled in S-088 |
| T3-v7.0.0-3 | `scripts/keel-parallel` + SHA-256 + gitignore `docs/.keel/parallel/` | missing | `Parallel development: auto` chosen by the owner (D-093); scheduled in S-088 |
| T3-v7.0.0-4 | Probe isolated writing backend / record serial fallback | missing | `Parallel development: auto` chosen by the owner (D-093); scheduled in S-088 |
| T3-v7.0.0-5 | Worker-aware locks/hooks | missing | `Parallel development: auto` chosen by the owner (D-093); scheduled in S-088 |
| T3-v7.0.0-6-7 | Integration after validated result + review; separate time/effort counters | n/a | procedural, only when parallel workers run |
| T3-v7.0.0-8 | Restamp lock | present | applied in S-084 (D-093) |
| T3-v7.1.0 | Static-analysis evidence `PASS`/`FINDINGS`/`BLOCKED — ENVIRONMENT` | n/a | no file or card line; procedural rule from the next test point (phpcs `TO BUILD`, PHPStan absent per technical plan) |
| T3-v7.1.0-2 | Restamp lock | present | applied in S-084 (D-093) |
| T3-v7.2.0-1 | Inbound-call inventory in the technical plan's `## Testing` | missing | `docs/03-technical-plan.md` has no inbound/tunnel inventory; owner approved applying it (D-093), scheduled in S-087 |
| T3-v7.2.0-2 | `scripts/playground-tunnel`, tunnel binary as `optional` env row, regenerated `keel-doctor`, `docs/playground.md` tunnel section, outside the distributable | missing | none present (`scripts/` is already `export-ignore`, `.gitattributes:31`); owner approved applying it (D-093), scheduled in S-087 |
| T3-v7.2.0-3 | Provider accepts the tunnel URL — tested once by hand, D-entry | missing | no D-entry; owner approved applying it (D-093), scheduled in S-037 |
| T3-v7.2.0-4 | Re-examine delegations whose only reason was reachability | n/a | the only `CREDENTIAL` row (test points line 40) cites a merchant terminal, not reachability |
| T3-v7.2.0-5 | `Real run in playground` cell; Playwright where a browser surface exists | present | test-points header line 9; `@playwright/test` in `package.json` |
| T3-v7.3.0-1 | Reconciliation runs now | present | S-084 |
| T3-v7.3.0-2 | Standing check 1 (agent teams flag) | present | `.claude/settings.local.json`; applied in S-084 (D-093) |
| T3-v7.3.0-3 | Standing check 2 (`max_workers: max`) | present | `docs/PROGRESS.md` card: `auto; max_workers: max` (owner, D-093) |
| T3-v7.3.0-4 | Effective cap resolved at kickoff with its limit | n/a | only when `auto`; next kickoff |
| T3-v7.3.0-5 | Restamp lock | present | applied in S-084 (D-093) |
| T3-v7.3.1-1 | `scripts/keel-clock` = byte copy of `keel_clock.py` | missing | absent; owner approved applying it (D-093), scheduled in S-086 |
| T3-v7.3.1-2 | Regenerate `scripts/keel-time` (no cross-slice close, `slice-record`, wall-clock vs summed, concurrency, throughput, basis) | missing | 0 `slice-record` / `keel-clock` references; owner approved applying it (D-093), scheduled in S-086 |
| T3-v7.3.1-3 | `docs/sessions.md` gains `Slice h`, `Max concurrency`, `Throughput`, `Projection basis` before `Notes` | missing | header line 6 lacks them; owner approved applying it (D-093), scheduled in S-086 |
| T3-v7.3.1-4 | `scripts/keel-verify` accepts overlapping intervals, checks projection vs basis | missing | no such check; owner approved applying it (D-093), scheduled in S-086 |
| T3-v7.3.1 | Refresh lock stamp | present | applied in S-084 (D-093) |
| T3-v7.3.2-1 | Copy `keel_parallel.py` over `scripts/keel-parallel` | missing | `Parallel development: auto` chosen by the owner (D-093); scheduled in S-088 |
| T3-v7.3.2-2-4 | Worker `test_commands` cheap only; `verify-batch` once per batch | n/a | procedural, only when parallel workers run |
| T3-v7.3.2-5 | Worker-role branch of `keel-stop-hook` | n/a | `scripts/keel-stop-hook` has no worker-role branch |
| T3-v7.3.2 | Refresh lock stamp | present | applied in S-084 (D-093) |
