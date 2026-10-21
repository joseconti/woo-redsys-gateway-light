---
schema: keel.sprint/2
sprint: 5
goal: Next-version defect fixes on the work branch work/after-7.1.0, merged into develop only after the owner tags v7.1.0
status: in-progress
scope_reconciliations: []
slices:
  - id: S-092
    title: Low defects found by PHPStan on the 7.1.0 candidate — undefined $result in the no-transaction-ID refund branch (Redsys, Bizum, Google Pay), PSD2 shipAddressUsage fields never sent, two methods that can end without a return
    status: not-started
    original_hours: 1
    residual_hours: 1
    change_kind: added
    parent_slice: null
    owns: [S-092]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [classes/, woocommerce-redsys.php, tests/, docs/api/]
      contracts:
        reads: []
        writes: []
      exclusive_resources: [playground]
      parallel: serial
      serial_reason: every slice's reproduction test runs on the one wp-env playground mounted on this checkout
  - id: S-065
    title: The Spanish translation of the Blocks checkout script is never loaded — its JSON file is named after the source path, not after the script WordPress loads
    status: not-started
    original_hours: 0.5
    residual_hours: 0.5
    change_kind: added
    parent_slice: null
    owns: [S-065]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [languages/, classes/, tests/]
      contracts:
        reads: []
        writes: []
      exclusive_resources: [playground]
      parallel: serial
      serial_reason: same playground as S-092
  - id: S-034
    title: Inespay handle_callback() should call set_payment_method() instead of writing _payment_method meta
    status: not-started
    original_hours: 0.5
    residual_hours: 0.5
    change_kind: added
    parent_slice: null
    owns: [S-034]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [classes/, tests/]
      contracts:
        reads: []
        writes: []
      exclusive_resources: [playground]
      parallel: serial
      serial_reason: same playground as S-092
  - id: S-042
    title: Google Pay get_redsys_args() reads the billing name through the generic meta API instead of the order's getters (WooCommerce doing_it_wrong notice)
    status: not-started
    original_hours: 0.25
    residual_hours: 0.25
    change_kind: added
    parent_slice: null
    owns: [S-042]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [classes/, tests/]
      contracts:
        reads: []
        writes: []
      exclusive_resources: [playground]
      parallel: serial
      serial_reason: same playground as S-092
  - id: S-094
    title: get_acctinfo() subtracts the customer's last_update user meta from a timestamp; with no such meta PHP 8 throws a TypeError at the card checkout (dead computation, overwritten a few lines later)
    status: not-started
    original_hours: 0.5
    residual_hours: 0.5
    change_kind: added
    parent_slice: null
    owns: [S-094]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [classes/, tests/]
      contracts:
        reads: []
        writes: []
      exclusive_resources: [playground]
      parallel: serial
      serial_reason: same playground as S-092
---

# Sprint 5 — Next-version defect fixes, held on a work branch until v7.1.0 is tagged

- Acceptance: product commits land only on `work/after-7.1.0` (branched from `develop` at this sprint's opening); `develop` keeps the 7.1.0 candidate's shipped files unchanged (`git diff 704730a..develop` over the archive's file list stays empty) until the owner tags `v7.1.0`. Each fix starts from a reproduction test seen failing (D-036). Bookkeeping (this file, `docs/PROGRESS.md`, `docs/decisions.md`, `plan.json`) is committed on `develop` — `docs/` never ships.
- Notes: opened 2026-10-21 (D-103). Promoted from `deferred.md` only the four items that are defects with no product choice in them; S-033, S-040, S-041, S-053, S-055, S-057, S-030, S-090 and S-093 stay deferred (owner's choices, the owner's terminals, or a manifest change). S-065 is also self-audit answer 6: if the owner does not accept shipping 7.1.0 without it, its commit can be cherry-picked onto `develop` before the tag. Hours are AI working time plus supervision.
- Close-out:
