---
schema: keel.deferred/1
items:
  - id: S-033
    title: Stop persisting the Redsys signing secret in plain text in order meta (Bizum, Google Pay)
    status: not-started
    hours: 2
    target: null
    reason: D-025 — the user chose to leave it documented and decide later; it is load-bearing for the per-user test mode
    depends_on: []
    criteria: []
  - id: S-034
    title: Inespay handle_callback() should call set_payment_method() instead of writing _payment_method meta
    status: not-started
    hours: 0.5
    target: null
    reason: L-004 — low severity, works today; revisit when the success path of handle_callback() is next touched
    depends_on: []
    criteria: []
  - id: S-040
    title: Redact the inbound signature and raw notification data from the gateways' debug logs
    status: not-started
    hours: 1
    target: null
    reason: D-052 — request data, lower risk than the secret; a debug log exists to show it, so what to keep is a product choice
    depends_on: []
    criteria: []
  - id: S-041
    title: Low-severity findings of D-041 item 7 (Inespay absent from redsys-types, on-hold counted as paid, refund URL difference, stray wpml-config key)
    status: not-started
    hours: 1.5
    target: null
    reason: D-052 — triaged only; none blocks the release of the pending fixes
    depends_on: []
    criteria: []
  - id: S-042
    title: Google Pay get_redsys_args() reads the billing name through the generic meta API instead of the order's getters (WooCommerce doing_it_wrong notice)
    status: not-started
    hours: 0.25
    target: null
    reason: D-053 — found while testing S-038; the value is still read correctly, only a notice is raised under WP_DEBUG; same class as L-004
    depends_on: []
    criteria: []
  - id: S-053
    title: Hardening notes of the 2026-10-06 security audit (non-findings listed in the local audit report)
    status: not-started
    hours: 2
    target: null
    reason: D-056 — none has an affected principal; triage with the user after the confirmed findings are fixed. D-065 — the scoped re-audit added 18 notes (local report of the `476d52e` run); one of them, an uncaught error on PHP 8 for a Bizum notification posted without its signature-version field, is the first to look at
    depends_on: []
    criteria: []
  - id: S-057
    title: Admin notices — the four link buttons take WooCommerce's colours for a primary button inside a WooCommerce message, which axe measures below 4.5:1 on WooCommerce 7.4
    status: not-started
    hours: 0.5
    target: null
    reason: D-067 — the colours are WooCommerce's stylesheet, not this plugin's; giving the buttons colours of their own is a visual decision for the user, and the result on a current WooCommerce has not been observed
    depends_on: []
    criteria: []
  - id: S-055
    title: Order-received fallback follow-ups — successful_request() of the three gateways to verify through is_valid_return() (one verification instead of two), and an atomic claim of the per-order attempt
    status: not-started
    hours: 1
    target: null
    reason: D-060 — both need a valid signature to matter; refactoring the verification head of successful_request() is not a security-fix change
    depends_on: []
    criteria: []
  - id: S-065
    title: The Spanish translation of the Blocks checkout script is never loaded — its JSON file is named after the source path, not after the script WordPress loads
    status: not-started
    hours: 0.5
    target: null
    reason: D-072 — found by the gate's self-audit and confirmed on the ceiling instance; today it only affects the fallback label "Inespay Bank Transfer" when the merchant set no title; older than this release
    depends_on: []
    criteria: []
  - id: S-066
    title: On WooCommerce 7.4 the Blocks checkout draws the Inespay option for customers outside ES, PT and IT (fallback label, no description, no icon); reproduce, check the ceiling instance, decide
    status: not-started
    hours: 1
    target: null
    reason: D-074 — found while driving AC-46 (S-060); the classic checkout hides it for the same customer; not checked on a current WooCommerce nor whether such an order can be placed; older than this release
    depends_on: []
    criteria: []
  - id: S-068
    title: The line numbers in the "Source files" lists of docs/flows/ are behind the code (docs/flows/refund.md names ask_for_refund() at 1091, it is at 1137); repair them and extend keel-verify's check 28, which reads docs/reference/ only, to docs/flows/
    status: not-started
    hours: 0.5
    target: null
    reason: D-076 — found while correcting docs/usage/configuration.md (S-067); only refund.md was compared with the code, the other flow files were not read; documentation only, nothing ships
    depends_on: []
    criteria: []
---

# Deferred — wanted, not in the current plan

Ids share one namespace with the sprint slices: promoting an item moves it, keeping its id.
