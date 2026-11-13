---
schema: keel.sprint/2
sprint: 4
goal: Work independent of the owner's 7.1.0 release block — evidence only, no shipped file changes while develop is the candidate
status: done
scope_reconciliations: []
slices:
  - id: S-091
    title: Run PHPCS (phpcs.xml), PHPStan and Plugin Check read-only against the 7.1.0 candidate, so the owner decides self-audit answer 1 on findings rather than on "never run"
    status: done
    original_hours: 1.5
    actual_hours: 0.0989
    actual_source: measured
    residual_hours: 0
    change_kind: added
    parent_slice: null
    owns: [S-091]
    estimate_changes: []
    depends_on: []
    criteria: []
    execution:
      write_paths: [docs/07-release.md, docs/decisions.md, docs/PROGRESS.md, docs/sprints/, docs/.keel/plan.json]
      contracts:
        reads: []
        writes: []
      exclusive_resources: []
      parallel: serial
      serial_reason: Plugin Check needs a WordPress site (a throw-away one was used, not the playground); the tools are installed outside the repository so no manifest or lockfile on the candidate changes
---

# Sprint 4 — Work independent of the owner's 7.1.0 release block

- Acceptance: every slice here leaves the shipped files of the candidate (`704730a`) untouched; `git diff 704730a..HEAD` over the archive's file list stays empty until the owner tags `v7.1.0`.
- Notes: opened 2026-10-21 while the release waits on the owner (merge, tag, WordPress.org upload, and the two acceptances of D-100). Development from `deferred.md` is not started here: any product commit on `develop` would change what the owner merges. Hours are AI working time plus supervision.
- Close-out:
- S-091 (done 2026-10-21, D-102): PHPCS, PHPStan and Plugin Check run once on `git archive 704730a`, tools under `/tmp`, a throw-away site destroyed afterwards; no shipped file touched. No security finding; three low PHPStan defects (S-092); installing the tools is S-093; the 7.1.0 upgrade notice exceeds WordPress.org's 300 characters — a shorter wording is offered to the owner in `docs/07-release.md`.
- Session close (2026-10-21): `scripts/keel-verify` check 24 failed on a rounding difference between `scripts/keel-time end` and the check on a seven-minute session; the row corrected to its displayed figures and recorded as L-018, to report to Keel upstream with L-017.
