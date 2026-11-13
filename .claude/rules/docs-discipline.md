---
paths:
  - "woocommerce-redsys.php"
  - "about-redsys.php"
  - "classes/**/*.php"
  - "includes/**/*.php"
  - "resources/js/**/*.js"
  - "assets/css/**/*.css"
  - "tests/**"
---

# Build discipline — Payment Gateway for Redsys & WooCommerce Lite

- Before changing anything, read the change map row for that type of change (docs/02-functional-spec.md §Change map): it lists every artifact that must be touched.
- Before writing ANY new function, method, class or hook: grep docs/api/INDEX.md first; reuse or generalize an existing fit — a near-duplicate is a defect.
- Every public surface (action, filter, public method) is documented at the moment it changes, in the same slice: created — doc in docs/api/ AND its INDEX.md row, with a runnable example; modified — doc, example and row updated to the as-built signature; removed — row and doc deleted, or marked deprecated with its replacement if it was ever released. Rows still marked "progressive" get their full doc the first time a slice touches that surface.
- Extension points: user-facing strings filterable, before/after actions on decisions, filterable requests/responses — all prefixed per the hook pattern in the code-style rule.
- Test-first (D-036, `pure-logic`): pure functions of their inputs — signature computation and verification, order-number preparation and recovery, amount handling, validators — get their test written and seen failing before their code. At every value: a bug fix STARTS from a test that reproduces the bug and fails, and a test derived from an acceptance criterion or a reproduced bug is never edited to make it pass.
- Test the boundary, not many small values: anything that formats, truncates or pads an ID needs a case at the exact size where it would first break (L-006, D-033).
- Run tests where they are verified to run: inside `wp-env` (docs/03-technical-plan.md §Testing has the exact commands). After reverting a PHP mutation in an e2e check, restart the `wordpress` container before distrusting the revert (L-005).
- Push test scope is `affected` (project card): selection is made by `scripts/keel-affected-tests`, never by hand; the full suite runs at release boundaries.
- Work belongs to a slice in the sprint plan (`docs/sprints/`, read through `scripts/keel-plan`); time is recorded with `scripts/keel-time`. Unplanned work is added to the plan first, not done on the side.
- User-visible fixes are added to the `== Unreleased ==` section of `readme.txt` (D-032). A version bump touches every row of the plan's version-touchpoints table.
- Update docs/PROGRESS.md and docs/decisions.md at the moment of change, never later. Recorded decisions are not re-opened: declined and not to be re-offered — competitive scan (D-035), end-user guide (D-013), project website (D-005).
