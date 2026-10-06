# Security audit log — Payment Gateway for Redsys & WooCommerce Lite

> One row per run. The raw run (`recon.md`, `coverage.md`, `findings.json`, `SECURITY-AUDIT.md`) lives in `docs/security-audit/<date>-<sha>/`, which is gitignored: a report of confirmed, unfixed vulnerabilities is a disclosure the moment it reaches a public repository. While a finding is open, nothing here describes it. When its fix ships, its row gains the title, severity and fix commit.

| Date | Audited commit | Scope | Profiles | Verification | Confirmed (by severity) | Needs validation | Rejected | Confirmed still open |
|---|---|---|---|---|---|---|---|---|
| 2026-10-06 | `e50ab39` | full — 25 units (16 with candidates, 8 covered with none, 1 n/a) | `references/security/wordpress.md` | subagent — every candidate decided by a verifier that did not hunt it; one reproduced by its verifier in the playground | 9 — critical 0, high 0, medium 3, low 6 | 2 | 8 | 0 open; 9 fixed on `develop`, unreleased (S-043 to S-051); each refuted by its verifier at `476d52e` (scoped re-audit below) |
| 2026-10-06 | `476d52e` | scoped, a partial pass — the 9 findings confirmed at `e50ab39` re-verified, plus the shipped-code diff `e50ab39..476d52e`: 15 units (13 covered, 1 with a candidate, 1 n/a) | `references/security/wordpress.md` | subagent — each of the 9 cards decided by a verifier that had not hunted it and was given neither the fix's records nor the earlier verdict; the 1 new candidate decided by a verifier other than the one that noticed it; nothing executed (source readings) | 0 | 1 new (3 with the 2 still open from the full run) | 9 — the re-verified findings, refuted because fixed | 0 |
| 2026-10-06 | `e89356b`, then `5e31697` | gate read, a partial pass in two parts — a read of the tree at `e89356b` (7 candidates), then at `5e31697` the shipped diff since that read, the PSD2 and global classes whole, the refund request path of the three Redsys gateways, and the first part's undecided candidates | `references/security/wordpress.md` | subagent — the second part's two verifiers had not hunted what they decided and were given the claims without the earlier reading; nothing executed by them; one candidate reproduced afterwards by a failing test on PHP 7.4 and PHP 8.3 | 2 — low 2 | 2 new (5 with SA-06, SA-13 and SA-20) | 2 — one refuted, one the disclosed behaviour of test mode | 0 open; both fixed on `develop`, unreleased (S-064, S-072). 9 hardening notes with no affected principal are in the local runs and in S-053, S-073 and S-074 |

## Findings disclosed after their fix shipped

None yet.

## Not covered by the 2026-10-06 run

Multisite, HPOS order storage, PHP 8.x and WordPress older than 6.5 were not exercised (the playground is PHP 7.4, WordPress 7.0, WooCommerce 7.4). Nothing was run against production, a live processor account, or a deployment.
