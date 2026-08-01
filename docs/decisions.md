# Decisions — Payment Gateway for Redsys & WooCommerce Lite

> Append-only. A session NEVER re-opens a decision recorded here on its own initiative;
> only the user reverses a decision (append the reversal as a new entry).

## D-001 — Project type and security profile
- Date / phase: 2026-08-01 / Adoption
- Decision: Project type is WordPress plugin / WooCommerce extension. Security profile loaded: `references/security/wordpress.md`.
- Why: the codebase is a WooCommerce payment gateway plugin distributed on WordPress.org.
- Alternatives rejected (and why): n/a — unambiguous from the codebase.
- Supersedes: none

## D-002 — Stack, conventions, license (adopted as-is)
- Date / phase: 2026-08-01 / Adoption
- Decision: PHP plugin targeting `Requires PHP: 7.0` (readme.txt) / WordPress `Tested up to: 7.0` / WooCommerce `7.4`–`10.9`. No Composer dependency manager (hand-rolled signature logic). Front-end build via `@wordpress/scripts` + webpack, source at `resources/js/frontend/index.js` compiling to `assets/js/frontend/blocks.js`. Text domain `woo-redsys-gateway-light`.
- Why: this is what the code already does; conventions are observed, not imposed on adoption.
- Alternatives rejected (and why): n/a — de facto state.
- Supersedes: none

## D-003 — License reconciled to GPL-2.0-or-later
- Date / phase: 2026-08-01 / Adoption
- Decision: the single declared license is now **GPL-2.0-or-later** everywhere: `woocommerce-redsys.php` header, `package.json`, `readme.txt` (already correct), plus a new root `LICENSE` file with the official GNU GPLv2 text.
- Why: the three files previously disagreed (header said GPL-3.0, readme.txt said GPLv2-or-later, package.json said GPL-3.0+), and WordPress.org requires GPLv2-or-later compatibility. User chose GPL-2.0-or-later explicitly when asked.
- Alternatives rejected: keeping GPL-3.0 everywhere — rejected by the user in favor of matching WordPress.org's expectation and the already-correct readme.txt.
- Supersedes: none

## D-004 — Docs language: English
- Date / phase: 2026-08-01 / Adoption
- Decision: every `docs/` artifact Keel creates for this project is written in English (token economy default).
- Why: user confirmed the recommended default; the plugin's own code is already English-based with an `es_ES` translation, so this only affects Keel's internal documentation.
- Alternatives rejected: Spanish docs — user declined the extra token cost.
- Supersedes: none

## D-005 — No project website (Phase 8 not activated)
- Date / phase: 2026-08-01 / Adoption
- Decision: this project does not get its own Keel-managed website; Phase 8 is not run.
- Why: user has a general site (plugins.joseconti.com) and the WordPress.org plugin page already serves as the product page.
- Alternatives rejected: dedicated landing page — declined by user, may be revisited later (a later explicit request supersedes this).
- Supersedes: none

## D-006 — `docs/inespay-payment/` vendored reference material relocated
- Date / phase: 2026-08-01 / Adoption
- Decision: moved the full vendored third-party Inespay plugin copy from `docs/inespay-payment/` to `.reference/inespay-payment/` and added `.reference/` to `.gitignore` (untracked from git).
- Why: it is not part of the shipped plugin, was not `export-ignore`d, and would have shipped inside any git-archive distributable built from this repo — a packaging risk found during the adoption inventory. User confirmed it is reference-only material.
- Alternatives rejected: excluding it in place via `.gitattributes` only — user preferred it out of `docs/` entirely.
- Supersedes: none

## D-007 — Accessibility target: WCAG 2.2 AA
- Date / phase: 2026-08-01 / Adoption
- Decision: WCAG 2.2 AA is the floor for the plugin's admin settings screens and checkout UI contributions, with AAA where feasible — Keel's standard default.
- Why: user confirmed the recommended default.
- Alternatives rejected: none proposed.
- Supersedes: none

## D-008 — Single assistant: Claude Code only
- Date / phase: 2026-08-01 / Adoption
- Decision: no native assistant-config package is generated for other tools (Codex, Cursor, Copilot, Gemini CLI, Windsurf) — only the Claude Code container (`.claude/`) plus the dual portability lock (`CLAUDE.md` + `AGENTS.md`) apply.
- Why: user confirmed only Claude Code works on this repo.
- Alternatives rejected: multi-tool config package — not needed today; can be added later if other tools join.
- Supersedes: none

## D-009 — Design system: one-off / n/a
- Date / phase: 2026-08-01 / Adoption
- Decision: no dedicated design system exists or is planned — the plugin ships plain, hand-written CSS for its admin settings and checkout contributions (`assets/css/*.css`), no tokens or component library.
- Why: matches the as-built reality; a WooCommerce settings-panel plugin does not warrant a founding design system.
- Alternatives rejected: none — default accepted, not explicitly asked as a separate question given the project's small UI surface.
- Supersedes: none

## D-010 — Client budget: no
- Date / phase: 2026-08-01 / Adoption
- Decision: no client-facing budget is produced for this project (`Client budget: no`); estimates stay internal AI-time figures only.
- Why: this is the author's own product, not client-billed work. Default accepted.
- Alternatives rejected: none.
- Supersedes: none

## D-011 — Session-start setup batch answers (adoption)
- Date / phase: 2026-08-01 / Adoption
- Decision: Durability = git remote `origin` (GitHub) — satisfied. Autonomy = automatic (Keel does not ask before pushing to `develop`, never merges to `master`/tags without explicit instruction). Forge issues = after-sprint duty ON, sweep interval 24h. Issue capture = OFF (a problem the user reports in chat is not auto-opened as a public issue). Notifications = `PushNotification` tool (desktop + phone), confirmed delivering. Branches = `develop` created as the integration branch (this repo previously had only `master`).
- Why: user's explicit answers in this and the prior session; recorded here (and in the project card) so no future session re-asks. Machine-local mechanics (automatic mode file) also mirrored in `CLAUDE.local.md`.
- Alternatives rejected: n/a.
- Supersedes: none

## D-012 — Keel embedded skill and lock refreshed to v5.9.0
- Date / phase: 2026-07-31 → 2026-08-01 / prior session + Adoption
- Decision: `.claude/skills/keel/` replaced wholesale with v5.9.0 (verified file-for-file); `CLAUDE.md` and `AGENTS.md` lock blocks restamped to v5.9.0.
- Why: project's embedded copy was badly behind (v1.11.0); routine Keel maintenance.
- Alternatives rejected: none.
- Supersedes: none

## D-013 — End-user guide (`guide/`) declined for now
- Date / phase: 2026-08-01 / Adoption
- Decision: no `guide/` HTML guide is built; `readme.txt` remains the user-facing documentation.
- Why: user confirmed the recommended default — the plugin's settings/checkout surface is small enough that readme.txt covers it.
- Alternatives rejected: building `guide/` on keel-docs-theme now — can be added later (User guide: card line updated then).
- Supersedes: none

## D-014 — Phase 5 scaffold built during adoption, not deferred
- Date / phase: 2026-08-01 / Adoption
- Decision: built the full Phase 5 testability scaffold now (`.wp-env.json`, `docs/playground.md`, `scripts/keel-doctor`, `scripts/keel-verify`, `scripts/keel-handoff-verify`, `docs/sprints/`, `docs/05-test-points.md`) instead of deferring to the first real sprint.
- Why: user explicitly chose to build it now when asked, rather than at the first issue-fix sprint.
- Alternatives rejected: deferring to the first real sprint — was the recommended default, user chose otherwise.
- Supersedes: none

## D-015 — Chaining: start (full automatic chat chaining)
- Date / phase: 2026-08-01 / maintenance
- Decision: the project card's `Chaining:` value changes from `off` to `start` — a clean session close-out now opens a brand-new Claude Code CLI session automatically (via `osascript` driving Terminal.app on macOS) and submits the continuation prompt, with no human keystroke in between.
- Why: user explicitly asked ("Abre tú mismo el nuevo chat en modo automático"), after being told plainly this only works from the standalone CLI (not the VS Code extension or desktop app) and requires the `claude` binary on PATH. The warning Keel requires before this choice was given: it changes the tool (development moves to CLI sessions), and it means development advances with nobody watching between links.
- What this required: installing the standalone `claude` CLI to `~/.local/bin/claude` (it was not previously on this machine's PATH — neither the desktop app nor a VS Code extension expose it), confirmed added to `~/.zshrc`. Built and end-to-end verified in a scratch `/tmp` repo (never in this project's own working tree): the single-lane lock (claim / busy-detection against a genuinely live PID / orphan recovery from a dead PID / release restricted to the owning session), the launch receipt (atomic `mkdir`, keyed by the hand-off's own `Generated`+`Commit` identity so a regenerated hand-off gets a fresh launch and a re-run of the same one does not fire twice), and the real `osascript` fire — which genuinely opened a new Terminal.app window, ran `claude` in the correct directory, and submitted the prompt; the test window was closed immediately after confirming.
- Alternatives rejected: `prefill` (the safer middle ground, human presses Enter) — user asked for full `start` directly, was given the choice between installing the CLI, recording the decision without it working yet, or staying `off`, and chose to install the CLI and get the real thing.
- Supersedes: none — the earlier `off` (recorded as the un-asked default, never a formal D-entry) is superseded by this one.

## D-016 — First automated test coverage: `RedsysLiteAPI` unit tests, no WordPress bootstrap
- Date / phase: 2026-08-01 / Phase 5 (sprint)
- Decision: added PHPUnit 9.6 unit tests for `RedsysLiteAPI` (`tests/Unit/RedsysLiteAPITest.php`, `composer.json`, `phpunit.xml.dist`, `tests/bootstrap.php`) as true unit tests against the class in isolation — `wp_json_encode()` is stubbed in `tests/bootstrap.php` rather than booting a full WordPress core test suite (`WP_UnitTestCase`). Tests run inside the existing `wp-env` `cli` Docker container (the host has no local PHP/Composer), via `composer install` + `vendor/bin/phpunit`.
- Why: `RedsysLiteAPI` has no WordPress runtime dependency beyond that one function, so a full WP bootstrap (WP_TESTS_DIR, `wp-env run tests-cli`, a test database) would add real engineering and runtime cost for zero additional coverage on this class. This was the highest-risk untested code path per `docs/threat-model.md`, flagged as a deferred item in `docs/PROGRESS.md`.
- Alternatives rejected: full `WP_UnitTestCase` integration bootstrap against the `tests-wordpress`/`tests-cli` containers — the right choice once tests are written for code that DOES touch WordPress (hooks, `$wpdb`, the gateway classes), but unnecessary overhead for this specific class; revisit when the gateway classes themselves get test coverage.
- Supersedes: none

## D-017 — `vendor/` gitignored, `composer.lock` committed
- Date / phase: 2026-08-01 / Phase 5 (sprint)
- Decision: `vendor/` (Composer's downloaded dependencies) is gitignored; `composer.lock` is committed so every environment installs the exact same dependency versions.
- Why: standard PHP project hygiene — `vendor/` is a regenerable build artifact (`composer install`), not source; the lock file is what makes that regeneration reproducible.
- Alternatives rejected: committing `vendor/` — unnecessary repo bloat for a dev-only dependency (PHPUnit never ships in the plugin's production package).
- Supersedes: none

## D-018 — Integration tests for the gateway's IPN validation, via wp-env's own WP core test scaffold
- Date / phase: 2026-08-01 / Phase 5 (sprint)
- Decision: added a second PHPUnit suite, `tests/Integration/` (`phpunit-integration.xml.dist`, `tests/bootstrap-integration.php`), covering `WC_Gateway_redsys::check_ipn_request_is_valid()` as a real `WP_UnitTestCase` against a booted WordPress + WooCommerce. It runs inside `wp-env`'s `tests-cli` container, reusing the WordPress core PHPUnit scaffold already provisioned there (`WP_TESTS_DIR=/wordpress-phpunit`) — no separate `install-wp-tests.sh` step was needed. Added `yoast/phpunit-polyfills` as a dev dependency (required by the WP core test bootstrap).
- Why: `WC_Gateway_redsys` extends `WC_Payment_Gateway` and genuinely depends on WordPress/WooCommerce (hooks, options, `WC_Logger`), unlike `RedsysLiteAPI` (D-016) — a true unit test would have to fake too much of WooCommerce to be trustworthy. This automates the exact fail-closed scenario already driven manually in the playground (`docs/05-test-points.md`, "Adoption — playground verification" row) plus forged-signature, tampered-payload, and wrong-order-signature cases.
- Alternatives rejected: mocking `WC_Payment_Gateway`/WooCommerce instead of booting it for real — rejected because the class under test IS the integration with WooCommerce; mocking it away would test the mock, not the gateway.
- Supersedes: none
