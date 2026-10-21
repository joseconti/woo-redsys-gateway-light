---
schema: keel.deferred/1
items:
  - id: S-033
    title: Stop persisting the Redsys signing secret in plain text in order meta (Bizum, Google Pay)
    status: not-started
    original_hours: 2
    residual_hours: 2
    change_kind: added
    parent_slice: null
    owns: [S-033]
    estimate_changes: []
    target: null
    reason: D-025 — the user chose to leave it documented and decide later; it is load-bearing for the per-user test mode
    depends_on: []
    criteria: []
  - id: S-034
    title: Inespay handle_callback() should call set_payment_method() instead of writing _payment_method meta
    status: not-started
    original_hours: 0.5
    residual_hours: 0.5
    change_kind: added
    parent_slice: null
    owns: [S-034]
    estimate_changes: []
    target: null
    reason: L-004 — low severity, works today; revisit when the success path of handle_callback() is next touched
    depends_on: []
    criteria: []
  - id: S-040
    title: Redact the inbound signature and raw notification data from the gateways' debug logs
    status: not-started
    original_hours: 1
    residual_hours: 1
    change_kind: added
    parent_slice: null
    owns: [S-040]
    estimate_changes: []
    target: null
    reason: D-052 — request data, lower risk than the secret; a debug log exists to show it, so what to keep is a product choice
    depends_on: []
    criteria: []
  - id: S-041
    title: Low-severity findings of D-041 item 7 (Inespay absent from redsys-types, on-hold counted as paid, refund URL difference, stray wpml-config key)
    status: not-started
    original_hours: 1.5
    residual_hours: 1.5
    change_kind: added
    parent_slice: null
    owns: [S-041]
    estimate_changes: []
    target: null
    reason: D-052 — triaged only; none blocks the release of the pending fixes
    depends_on: []
    criteria: []
  - id: S-042
    title: Google Pay get_redsys_args() reads the billing name through the generic meta API instead of the order's getters (WooCommerce doing_it_wrong notice)
    status: not-started
    original_hours: 0.25
    residual_hours: 0.25
    change_kind: added
    parent_slice: null
    owns: [S-042]
    estimate_changes: []
    target: null
    reason: D-053 — found while testing S-038; the value is still read correctly, only a notice is raised under WP_DEBUG; same class as L-004
    depends_on: []
    criteria: []
  - id: S-053
    title: Hardening notes of the 2026-10-06 security audit (non-findings listed in the local audit report)
    status: not-started
    original_hours: 2
    residual_hours: 2
    change_kind: added
    parent_slice: null
    owns: [S-053]
    estimate_changes: []
    target: null
    reason: D-056 — none has an affected principal; triage with the user after the confirmed findings are fixed. D-065 — the scoped re-audit added 18 notes (local report of the `476d52e` run); one of them, an uncaught error on PHP 8 for a Bizum notification posted without its signature-version field, was fixed as S-064. D-078 — the second pass of the gate read added four (the signature transient deleted before verification; currency, merchant code and terminal of a signed notification not compared; an undefined variable in a debug line of process_refund(); maybe_unserialize() on an option), in the local run of `5e31697`
    depends_on: []
    criteria: []
  - id: S-057
    title: Admin notices — the four link buttons take WooCommerce's colours for a primary button inside a WooCommerce message, which axe measures below 4.5:1 on WooCommerce 7.4
    status: not-started
    original_hours: 0.5
    residual_hours: 0.5
    change_kind: added
    parent_slice: null
    owns: [S-057]
    estimate_changes: []
    target: null
    reason: D-067 — the colours are WooCommerce's stylesheet, not this plugin's; giving the buttons colours of their own is a visual decision for the user, and the result on a current WooCommerce has not been observed
    depends_on: []
    criteria: []
  - id: S-055
    title: Order-received fallback follow-ups — successful_request() of the three gateways to verify through is_valid_return() (one verification instead of two), and an atomic claim of the per-order attempt
    status: not-started
    original_hours: 1
    residual_hours: 1
    change_kind: added
    parent_slice: null
    owns: [S-055]
    estimate_changes: []
    target: null
    reason: D-060 — both need a valid signature to matter; refactoring the verification head of successful_request() is not a security-fix change
    depends_on: []
    criteria: []
  - id: S-065
    title: The Spanish translation of the Blocks checkout script is never loaded — its JSON file is named after the source path, not after the script WordPress loads
    status: not-started
    original_hours: 0.5
    residual_hours: 0.5
    change_kind: added
    parent_slice: null
    owns: [S-065]
    estimate_changes: []
    target: null
    reason: D-072 — found by the gate's self-audit and confirmed on the ceiling instance; today it only affects the fallback label "Inespay Bank Transfer" when the merchant set no title; older than this release
    depends_on: []
    criteria: []
  - id: S-030
    title: Guided assistive-technology pass with the user
    status: not-started
    original_hours: 1
    residual_hours: 1
    change_kind: added
    parent_slice: null
    owns: [S-030]
    estimate_changes: []
    target: null
    reason: D-099 — the owner shipped 7.1.0 with the guided pass recorded as an accepted shortfall; it runs for the next version
    depends_on: [S-029]
    criteria: []
  - id: S-090
    title: One Redsys test-terminal payment whose notification URL is the playground tunnel, plus the observations SA-06, SA-13 and SA-20 (sandbox payments on the owner's Redsys and Inespay test terminals)
    status: not-started
    original_hours: 0.25
    residual_hours: 0.25
    change_kind: extracted
    parent_slice: S-037
    owns: [S-090]
    estimate_changes: []
    target: null
    reason: D-099 — acknowledged, not validated, for 7.1.0; the owner runs the payments after the release and anything found goes into 7.1.1
    depends_on: []
    criteria: []
  - id: S-092
    title: Low defects found by PHPStan on the 7.1.0 candidate — undefined $result in the no-transaction-ID refund branch (Redsys, Bizum, Google Pay), PSD2 shipAddressUsage fields never sent, two methods that can end without a return
    status: not-started
    original_hours: 1
    residual_hours: 1
    change_kind: added
    parent_slice: null
    owns: [S-092]
    estimate_changes: []
    target: null
    reason: D-102 — found by S-091, none a regression of 7.1.0; each starts from a failing test (D-036)
    depends_on: []
    criteria: []
  - id: S-093
    title: Install PHPCS (phpcs.xml fixed — testVersion 7.0-, property syntax), PHPStan and Plugin Check as project development tools, with doctor rows, and correct the readme header Requires at least
    status: not-started
    original_hours: 1.5
    residual_hours: 1.5
    change_kind: added
    parent_slice: null
    owns: [S-093]
    estimate_changes: []
    target: null
    reason: D-102 — self-audit answer 1; the tools ran once outside the repository; installing them changes composer.json and the playground, so it waits for the tag
    depends_on: []
    criteria: []
---

# Deferred — wanted, not in the current plan

Ids share one namespace with the sprint slices: promoting an item moves it, keeping its id.
