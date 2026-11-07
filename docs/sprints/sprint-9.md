---
schema: keel.sprint/2
sprint: 9
goal: Static-analysis hygiene independent of the owner's 7.1.0 release block, on work/after-7.1.0 (D-111)
status: in-progress
scope_reconciliations:
  - id: R9-01
    date: 2026-10-21
    reason: D-111 — S-101 extracted from S-093 (residual 1.5 to 1, the parent staying in deferred.md); S-102 added
    slices: [S-093, S-101, S-102]
    remaining_before: 0
    completed_residual: 0
    added_scope: 1.5
    explicit_residual_reestimates: 0
    remaining_after: 1.5
slices:
  - id: S-101
    title: phpcs.xml corrected (testVersion 7.0-, minimum WordPress 5.9, array properties in element syntax, manage_woocommerce as a known capability) and the readme header Requires at least raised from 4.0 to 5.9, the floor of the WooCommerce 7.4 the plugin requires
    status: done
    original_hours: 0.5
    actual_hours: 0.0119
    actual_source: measured
    residual_hours: 0
    change_kind: extracted
    parent_slice: S-093
    owns: [S-101]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [phpcs.xml, readme.txt]
      contracts:
        reads: []
        writes: []
      exclusive_resources: []
      parallel: serial
      serial_reason: two small slices on one work branch
  - id: S-102
    title: PHPCS findings of D-102 that have one fix and change no behaviour — the 19 hooks without a hook comment, 7 alignments, 3 doc lines in lower case, the mt_rand() fallback annotated
    status: in-progress
    original_hours: 1
    actual_hours: 0
    residual_hours: 1
    actual_source: measured
    change_kind: added
    parent_slice: null
    owns: [S-102]
    estimate_changes: []
    depends_on: [S-101]
    criteria: []
    execution:
      write_paths: [classes/, includes/, woocommerce-redsys.php]
      contracts:
        reads: []
        writes: []
      exclusive_resources: []
      parallel: serial
      serial_reason: two small slices on one work branch
---

# Sprint 9 — Static-analysis hygiene

- Acceptance: on `work/after-7.1.0`, PHPCS with the committed `phpcs.xml` reports no deprecation notice and none of the findings the slices name; comment-only and configuration changes, so the affected tests stay green; independent review of the diff; `develop` (the 7.1.0 candidate) carries no product change.
- Notes: opened 2026-10-21 at the owner's fourth request for work that does not depend on the merge and tag. S-093 waits for the tag because installing the analysers changes `composer.json` and the playground (D-102, D-103); its `phpcs.xml` fix and its readme-header correction change neither, so they are extracted (D-111). The PHPCS run of the work branch (tools under `/tmp/redsys-sa`, nothing installed) gives 40 errors and 10 warnings, none about security. Left for the owner: the 15 prohibited `@copyright`/`@license`/`@author`/`@access` tags (the files' own header convention), and `Requires PHP: 7.0` (D-001) while WooCommerce 7.4 declares PHP 7.2. Hours are AI working time plus supervision.
- Close-out:
- S-101 (done 2026-10-21, D-111): `work/after-7.1.0` at `dca7915`; PHPCS on the tracked PHP files no longer prints the four deprecation notices, and its `t_coalesce` (3) and unknown-capability (2) findings are gone — 45 left, all S-102's or the owner's. Configuration and readme only, no PHP changed.
