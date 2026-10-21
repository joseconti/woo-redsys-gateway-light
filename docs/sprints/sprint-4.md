---
schema: keel.sprint/2
sprint: 4
goal: Work independent of the owner's 7.1.0 release block — evidence only, no shipped file changes while develop is the candidate
status: in-progress
scope_reconciliations: []
slices:
  - id: S-091
    title: Run PHPCS (phpcs.xml), PHPStan and Plugin Check read-only against the 7.1.0 candidate, so the owner decides self-audit answer 1 on findings rather than on "never run"
    status: in-progress
    original_hours: 1.5
    residual_hours: 1.5
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
      exclusive_resources: [wp-env-playground]
      parallel: serial
      serial_reason: Plugin Check runs inside the one playground; the tools are installed outside the repository so no manifest or lockfile on the candidate changes
---

# Sprint 4 — Work independent of the owner's 7.1.0 release block

- Acceptance: every slice here leaves the shipped files of the candidate (`704730a`) untouched; `git diff 704730a..HEAD` over the archive's file list stays empty until the owner tags `v7.1.0`.
- Notes: opened 2026-10-21 while the release waits on the owner (merge, tag, WordPress.org upload, and the two acceptances of D-100). Development from `deferred.md` is not started here: any product commit on `develop` would change what the owner merges. Hours are AI working time plus supervision.
- Close-out:
