# Lessons Learned — Payment Gateway for Redsys & WooCommerce Lite

## L-001 — Vendored third-party reference code inside `docs/` was not export-ignored
- Symptom: `docs/inespay-payment/` contained a full copy of a third-party Inespay gateway plugin (own text domain, own PHP API client), sitting inside the `docs/` tree.
- Cause: reference material was dropped into `docs/` at some point and never excluded from `.gitattributes`' `export-ignore` rules, unlike the plugin's other dev-only paths.
- Fix: moved to `.reference/inespay-payment/` (repo root) and gitignored, per D-006.
- Where: adoption inventory, step 1 (git/package hygiene).
- What failed first: n/a — caught before any distributable package was built from this state.
- Check added: none possible yet — `scripts/keel-verify` does not exist for this project. Proposed check for when the Phase 5 scaffold adds it: a rule that flags any tracked file under `docs/` whose path or contents don't match the project's own text domain / namespace.
- Rule for next time: reference material that is not part of the shipped plugin belongs outside `docs/` (or gitignored) from the moment it's added — never inside a tree that is packaged for distribution without an explicit exclusion.
