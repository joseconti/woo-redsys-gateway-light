# Security audit log — Payment Gateway for Redsys & WooCommerce Lite

> One row per run. The raw run (`recon.md`, `coverage.md`, `findings.json`, `SECURITY-AUDIT.md`) lives in `docs/security-audit/<date>-<sha>/`, which is gitignored: a report of confirmed, unfixed vulnerabilities is a disclosure the moment it reaches a public repository. While a finding is open, nothing here describes it. When its fix ships, its row gains the title, severity and fix commit.

| Date | Audited commit | Scope | Profiles | Verification | Confirmed (by severity) | Needs validation | Rejected | Confirmed still open |
|---|---|---|---|---|---|---|---|---|
| 2026-10-06 | `e50ab39` | full — 25 units (16 with candidates, 8 covered with none, 1 n/a) | `references/security/wordpress.md` | subagent — every candidate decided by a verifier that did not hunt it; one reproduced by its verifier in the playground | 9 — critical 0, high 0, medium 3, low 6 | 2 | 8 | 0 open; 9 fixed on `develop`, unreleased (S-043 to S-051); scoped re-audit pending (S-052) |

## Findings disclosed after their fix shipped

None yet.

## Not covered by the 2026-10-06 run

Multisite, HPOS order storage, PHP 8.x and WordPress older than 6.5 were not exercised (the playground is PHP 7.4, WordPress 7.0, WooCommerce 7.4). Nothing was run against production, a live processor account, or a deployment.
