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
---

# Deferred — wanted, not in the current plan

Ids share one namespace with the sprint slices: promoting an item moves it, keeping its id.
