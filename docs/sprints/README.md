# Sprints — one file per sprint

This directory holds one file per Phase 5 development sprint, per Keel's
`references/project-state.md` ("Sprint files (Phase 5) — template").
**No sprint has been planned yet** — this directory exists as scaffolding
only; do not invent a sprint file until one is actually planned with the
user.

## Convention

- One file per sprint: `docs/sprints/sprint-N.md` (or a short slug, e.g.
  `docs/sprints/sprint-1-test-automation.md`), never one shared running log.
- Created when a sprint is planned (in plan mode, with the user), not before.
- Kept current for the sprint's whole life — `Status:` moves from `planned`
  to `in progress` to `closed`, and the `Slices` table and `Close-out`
  section are filled in as the work happens, not reconstructed afterward.

## Template

```
# Sprint [N] — [short goal]
- Scope: [slices/tasks in this sprint]
- Acceptance: [what "done" means for this sprint]
- Status: [planned / in progress / closed]
- Slices:
  | Slice | Status | Test point result | Notes |
- Close-out: [filled at close: what shipped, what moved to next sprint]
```

## Related files

- `docs/PROGRESS.md` — the project card and current position; always the
  authority on what the "next sprint" is.
- `docs/05-test-points.md` — the running log of test-point evidence across
  all sprints (command run, result, coverage tag, commit hash).
- `docs/04-adoption-audit.md` — "Testability" and "Prioritization" sections
  record the first candidate sprint (test automation + playground), pending
  the user scheduling it.
