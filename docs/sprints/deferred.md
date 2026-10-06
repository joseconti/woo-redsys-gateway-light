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
---

# Deferred — wanted, not in the current plan

Ids share one namespace with the sprint slices: promoting an item moves it, keeping its id.
