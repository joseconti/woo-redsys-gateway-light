# 04 — Adoption Gap Audit

> As-built reality vs Keel's standards, per `references/adoption.md` step 5. No code was changed to produce this audit beyond the user-approved license reconciliation (D-003) and relocating vendored reference material (D-006) — everything else below is a recorded finding, prioritized with the user, not silently fixed.

## Security
| Gap | Where | Severity | Standard it fails |
|---|---|---|---|
| Output escaping not exhaustively verified across the whole tree (only the main gateway class was spot-checked) | `classes/`, `includes/` | medium | `references/security/wordpress.md` — escape-on-output |
| Capability checks beyond WooCommerce's default Settings API gating not independently re-verified | gateway settings screens | low | same profile |
| No replay protection beyond signature verification on the notification endpoint; unclear whether Redsys's own protocol already prevents replay | `check_ipn_response`/`handle_callback` handlers | medium (undetermined without checking Redsys's protocol docs) | same profile |
| No automated security tests (signature verification, fail-closed paths) | whole plugin | high | Keel's own testing contract |
| No dependency-audit habit (`npm audit` never run) | `package.json` | low | same profile |

## Accessibility (UI: admin settings + checkout)
| Gap | Where | Severity | Standard it fails |
|---|---|---|---|
| No automated accessibility pass ever run | checkout + admin settings | medium | WCAG 2.2 AA (D-007) |
| No guided assistive-technology pass ever run | same | medium | same |
| Payment-icon `alt` text not verified | checkout icons (`bizum.png`, `GPay.svg`, `inespay.svg`, ...) | low–medium | WCAG 2.2 AA |

Full detail: `docs/accessibility.md`.

## i18n
No gap found in the live plugin code — text domain used consistently, `.pot`/`.po`/`.mo` pipeline exists and is wired. (The unrelated `docs/inespay-payment` reference copy used a different text domain, but it has been relocated out of the shipped tree, D-006.)

## Duplication
Not exhaustively scanned function-by-function during this adoption pass (would need a dedicated read of all four gateway classes side by side). The four gateways clearly share significant logic (redirection request building, notification handling shape) already partially centralized in `class-wc-gateway-redsys-global-lite.php` — worth a closer look the next time any of the four gateway classes is touched, to confirm nothing has silently drifted into copy-paste duplication since that shared base was introduced. Severity: low (informational), review trigger: next gateway-class touch.

## Extensibility
The plugin already exposes a reasonable set of hooks (see `docs/api/INDEX.md`): a notification-received action and an `_args`/`_icon` filter per gateway. Not found: a filter over the final order-status decision (e.g. filtering whether a mismatch goes to `on-hold` vs another status), which third-party code might want. Severity: low, informational — not a defect, a possible future enhancement.

## Docs
| Gap | Severity |
|---|---|
| No `README.md` existed before this adoption (only `readme.txt`) | fixed this pass |
| No per-surface docs for any of the 14 indexed hooks/filters (only the one-line INDEX exists) | low — progressive backfill, per adoption rule |
| Function-level docblocks inconsistent (class/property docblocks are present, function-level less so) | low |

## Hygiene
| Gap | Severity | Fixed this pass? |
|---|---|---|
| License declared 3 different ways across 3 files | high (legal ambiguity) | **yes — D-003** |
| No `LICENSE` file | medium | **yes — D-003** |
| Vendored third-party plugin copy inside `docs/`, not export-ignored | medium (packaging risk) | **yes — D-006** |
| `package.json` version (4.0.0) drifted from the real plugin version (7.0.2) | low | no — deferred, `docs/PROGRESS.md` |
| `package.json` has a stale/suspicious `wp-scripts ^0.0.1-security` dependency (distinct from `@wordpress/scripts`, looks like a placeholder/squat package) | medium | no — deferred, `docs/PROGRESS.md` |
| No `Requires PHP:` in the main plugin file header (only in readme.txt) | low | no — deferred |

## Testability
**The honest answer is "none of it exists" — the normal, high-value finding for a released project predating Keel:**
- No playground/dev environment defined (`docs/playground.md` missing).
- No `scripts/keel-doctor`, no `scripts/keel-verify`.
- No test suite at all — 0 acceptance criteria have a driven test; every past bug fix has been verified manually (inferred from the readme.txt changelog's prose descriptions, not from any recorded test evidence).
- No stable test identifiers on interactive elements (not verified in detail, but no test-automation attributes were observed during the code read).
- **This is the single highest-value remediation area** — without an environment and drivers, every future bug report becomes "install this and tell me if it works." Recommended as the first real Phase 5 sprint (wp-env + a minimal Playwright smoke test for the checkout flow + a PHPUnit test for `RedsysLiteAPI` signature verification, which is the highest-risk single function in the codebase).

> **Update, 2026-08-01 (Phase 5 sprint, post-adoption):** the wp-env playground and full test coverage recommended above are now built — `RedsysLiteAPI` unit tests (D-016/D-017), IPN/callback integration tests for all four gateway classes (`WC_Gateway_redsys` D-018, `WC_Gateway_Bizum_Redsys` D-019, `WC_Gateway_GooglePay_Redirection_Redsys` D-020 — this one also uncovered and fixed a real signature-bypass vulnerability, see `docs/threat-model.md` "Known vulnerabilities" — and `WC_Gateway_Inespay_Redsys` D-021), and a Playwright checkout smoke test for the Redsys gateway (D-022). 29 automated tests total. See `docs/playground.md` and `docs/03-technical-plan.md` §Testing. This snapshot is left unedited above as the as-adopted record; still open: the WooCommerce Blocks checkout, the other three gateways' checkout flows, and any JS unit-test suite.
>
> **Update, 2026-08-01 (same-day follow-up sprint):** every item still open above is now closed. Bizum and Google Pay checkout e2e tests (D-027); the WooCommerce Blocks checkout, previously untested since before this adoption, now has e2e coverage (D-028); Inespay's checkout flow — structurally different, since it makes a real server-side API call rather than redirecting to a form — is covered via a dev-only `pre_http_request` stub (D-029); `disable_inespay()`'s fractional-total behavior is covered (D-030). A JS unit-test suite was deliberately NOT added (D-031) — `resources/js/frontend/index.js` has no independent logic to unit-test, and D-028's e2e test already exercises the real compiled bundle. 46 automated tests total (8 unit + 31 integration + 7 e2e). See `docs/decisions.md` D-027–D-031 and `docs/05-test-points.md`'s ninth through thirteenth slices.

## Known traps (anti-patterns self-audit, applied against this tree)
Run against `references/anti-patterns.md`'s universal + WordPress/WooCommerce sections, evidence-based (not from impression):
- **Declared tool that never runs:** `phpcs.xml` exists but was not confirmed to run cleanly (not run during this read-only adoption pass) — flagged as `VERIFY`, not claimed as passing.
- **A fact pinned in multiple places, one stale:** version string is pinned in 4 places; one (`package.json`) has drifted — see Hygiene table above.
- **Documentation contradicting the build:** none found — this is the first real `docs/` set the project has had beyond `readme.txt`.
- **The generated artifact nobody consumes:** none found — `assets/js/frontend/blocks.js` is consumed (enqueued by the blocks-support classes).
- **Verification claimed but not run:** none found in the pre-adoption state (there was no Keel documentation making claims); the discipline going forward is what this whole file exists to protect.
- **The silent omission:** the vendored `docs/inespay-payment/` packaging risk (D-006) is exactly this pattern — fixed.

## Threat model
See `docs/threat-model.md` — produced this pass, with every control at its honest delivery state. Headline: the core payment-integrity control (signature verification, fail-closed) is `IN PLACE`; most of the remaining gaps are test/process gaps (`TO BUILD`), not known live vulnerabilities.

## Prioritization (fix now / fix when touched / accepted)

**Fixed now (during adoption, user-approved):**
- License reconciliation + `LICENSE` file (D-003)
- Vendored reference material relocated out of `docs/` (D-006)

**Fix when touched (bound to the area's next slice):**
- Output-escaping full verification, capability-check verification, replay-protection question — next time any gateway's notification handler is touched
- `package.json` version sync + stale `wp-scripts` dependency cleanup — next time `package.json` is touched
- `Requires PHP:` header addition — next version bump
- Per-surface docs backfill for the 14 indexed hooks — each the first time it's next touched
- Duplication review across the four gateway classes — next gateway-class touch
- Accessibility automated + guided passes — next time checkout UI or admin settings are touched

**Accepted as-is for now, deferred to a dedicated remediation sprint (not blocking ongoing maintenance):**
- No test suite (Testability — the highest-value item, recommended as the first real Phase 5 sprint whenever the user wants to schedule it)
- No `scripts/keel-doctor`/`scripts/keel-verify`/`docs/playground.md`/CI (Phase 5 scaffold — deferred to that same sprint)
- No CSS minify pipeline (source-first-assets contract — deferred, low urgency, cosmetic/perf only)

This prioritization mirrors `docs/PROGRESS.md`'s "Deferred items" list and `docs/keel-conformance.md`'s "missing, proposed and deferred" batch.
