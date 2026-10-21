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
    reason: D-052 — request data, lower risk than the secret; a debug log exists to show it, so what to keep is a product choice. Sprint 5's security review (2026-10-21) noted that Inespay's handle_callback() logs the raw callback body (debtor name and account) BEFORE its signature is verified, when debug is on — same item
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
    reason: D-056 — none has an affected principal; triage with the user after the confirmed findings are fixed. D-065 — the scoped re-audit added 18 notes (local report of the `476d52e` run); one of them, an uncaught error on PHP 8 for a Bizum notification posted without its signature-version field, was fixed as S-064. D-078 — the second pass of the gate read added four (the signature transient deleted before verification; currency, merchant code and terminal of a signed notification not compared; an undefined variable in a debug line of process_refund(); maybe_unserialize() on an option), in the local run of `5e31697`. Sprint 5's security review added: get_acctinfo() with a `$user_id` of a user that does not exist would fatal on get_userdata() false (no caller passes one today), and a shop manager paying someone else's order while logged in gets the 3DS account figures of their own account
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
