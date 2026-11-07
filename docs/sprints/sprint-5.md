---
schema: keel.sprint/2
sprint: 5
goal: Next-version defect fixes on the work branch work/after-7.1.0, merged into develop only after the owner tags v7.1.0
status: done
scope_reconciliations: []
slices:
  - id: S-092
    title: Low defects found by PHPStan on the 7.1.0 candidate — undefined $result in the no-transaction-ID refund branch (Redsys, Bizum, Google Pay), PSD2 shipAddressUsage fields never sent, two methods that can end without a return
    status: done
    original_hours: 1
    actual_hours: 0.4106
    actual_source: measured
    residual_hours: 0
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
    status: done
    original_hours: 0.5
    actual_hours: 0.1367
    actual_source: measured
    residual_hours: 0
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
    status: done
    original_hours: 0.5
    actual_hours: 0.0131
    actual_source: measured
    residual_hours: 0
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
    status: done
    original_hours: 0.25
    actual_hours: 0.1086
    actual_source: measured
    residual_hours: 0
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
    status: done
    original_hours: 0.5
    actual_hours: 0.3731
    actual_source: measured
    residual_hours: 0
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
  - id: S-095
    title: get_acctinfo() shipping-address usage, made live by S-092, is computed wrong — days divided by 216000 instead of 86400, and the first-use query matches any customer's order and the current order itself
    status: done
    original_hours: 0.5
    actual_hours: 0.2967
    actual_source: measured
    residual_hours: 0
    change_kind: added
    parent_slice: null
    owns: [S-095]
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
  - id: S-096
    title: get_acctinfo() counts the account age from midnight of the registration day (the date is formatted to Ymd before the minutes are computed), so "just created" (chAccAgeInd 02) is reached only shortly after midnight
    status: done
    original_hours: 0.25
    actual_hours: 0.1114
    actual_source: measured
    residual_hours: 0
    change_kind: added
    parent_slice: null
    owns: [S-096]
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
- Notes: opened 2026-10-21 (D-103); S-094 and S-095 added on the way, S-096 promoted back from `deferred.md` the same day once the rest was done (independent, no product choice). Promoted from `deferred.md` only the four items that are defects with no product choice in them; S-033, S-040, S-041, S-053, S-055, S-057, S-030, S-090 and S-093 stay deferred (owner's choices, the owner's terminals, or a manifest change). S-065 is also self-audit answer 6: if the owner does not accept shipping 7.1.0 without it, its commit can be cherry-picked onto `develop` before the tag. Hours are AI working time plus supervision.
- Close-out (2026-10-21, D-104, D-105): all seven slices done on `work/after-7.1.0` (head `ddb4b1b`), each from a reproduction test seen failing: S-092 (PHPStan defects), S-094 (PHP 8 TypeError in `get_acctinfo()`), S-095 (shipping-address usage computation), S-065 (Blocks script Spanish JSON), S-034 (Inespay `set_payment_method()`), S-042 (Google Pay billing name getters), S-096 (account age from the registration time). Reviews: code-reviewer (four findings, all fixed — S-095 and the expired-token case came from it); security-auditor on a pinned worktree: no finding in the diff (the first run read the wrong branch, L-019). Tests: integration suite 392 green; the pre-push selection widened once to the entire suite (216/216, `f658ebb`), later pushes green on their selection. `develop` still the unchanged 7.1.0 candidate. Merge `work/after-7.1.0` into `develop` after the `v7.1.0` tag. `docs/estimate.md` Phase 5 total updated to 84.4 h (sprints 1 to 5).
