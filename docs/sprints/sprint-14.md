---
schema: keel.sprint/2
sprint: 14
goal: Hotfix 7.1.1 — the redirect gateways submit the payment form to Redsys on their own again (D-116)
status: done
scope_reconciliations:
  - id: R14-01
    date: 2026-11-16
    reason: D-116 — the owner reported that the order-pay page no longer sends the customer to Redsys automatically; S-107 added
    slices: [S-107]
    remaining_before: 1
    completed_residual: 0
    added_scope: 2
    explicit_residual_reestimates: 0
    remaining_after: 3
slices:
  - id: S-107
    title: Auto-submit of the redirect form restored for card (both branches), Bizum and Google Pay — the inline script no longer depends on WooCommerce's handle being registered when the receipt page renders (block themes); reproduction tests first; version 7.1.1
    status: done
    original_hours: 2
    actual_hours: 0.7269
    actual_source: measured
    residual_hours: 0
    change_kind: added
    parent_slice: null
    owns: [S-107]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [woocommerce-redsys.php, classes/class-wc-gateway-redsys.php, classes/class-wc-gateway-bizum-redsys.php, classes/class-wc-gateway-googlepay-redirection-redsys.php, tests/Integration/RedirectFormEscapingTest.php, tests/Integration/RedirectAutoSubmitTest.php, tests/e2e/checkout-redsys.spec.js, tests/e2e/checkout-bizum.spec.js, tests/e2e/checkout-googlepay.spec.js, readme.txt, docs/api/INDEX.md, docs/reference/classes.md, docs/reference/hooks-and-extension-points.md, docs/reference/endpoints.md, docs/reference/functions.md, docs/flows/, scripts/keel-verify, docs/estimate.md, docs/sessions.md]
      contracts:
        reads: []
        writes: []
      exclusive_resources: [keel-development]
      parallel: serial
      serial_reason: one hotfix branch and the one test lane
---

# Sprint 14 — Hotfix 7.1.1

- Acceptance: on a block theme and on a classic theme, opening the order-pay page of a card, Bizum or Google Pay order sends the browser to Redsys with no click; reproduction tests seen failing first, then green. Hours are AI working time plus supervision.
- Close-out: S-107 done 2026-11-16 (measured 0.73 h). Full suite GREEN on the floor site on the candidate (41 unit, 418 integration, 67 browser); affected checkout, accessibility and cancel-return specs also run on the ceiling — green except the four known admin help-tip axe scans (S-083, D-114). Live check without mocks: card, Bizum and Google Pay order-pay pages land on sis-t.redsys.es on both sites.
- Close of S-107 (commit 95ec175, inside its measured time): line references in docs/flows and docs/reference re-pointed (the gateway code moved; several were stale before 7.1.0), keel-verify check 1 reads `= x =` changelog headings, estimate 97.4 h, two session rows recomputed and S-106 listed in its session's Done column.
