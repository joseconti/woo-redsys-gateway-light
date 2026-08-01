# Token Ledger — Payment Gateway for Redsys & WooCommerce Lite

> One row per working session. Final reconciliation (cost + deviation vs estimate) at release — no client budget, so this tracks the author's own usage for reference.

| Date | Session | Model | Notes |
|---|---|---|---|
| 2026-07-31 | Keel skill maintenance (v1.11.0 → v5.9.0 embed update) | Claude Sonnet 5 | Paused mid-task, continued 2026-08-01 |
| 2026-08-01 | Keel skill maintenance completion + session-setup batch (automatic mode, forge issues, notifications) | Claude Sonnet 5 | Pushed `develop`, wrote `.claude/settings.local.json` + `CLAUDE.local.md` |
| 2026-08-01 | Full Keel adoption (this session) | Claude Sonnet 5 | Inventory, 6 batched questions, license reconciliation (D-003), reference-material relocation (D-006), full `docs/` state + as-built artifacts + gap audit |
| 2026-08-01 | Phase 5 sprint: PHPUnit test suites (all 4 gateways) + Playwright checkout smoke test + full-plugin review (2 real bugs fixed, 8 documented) | Claude Sonnet 5 | Unit tests for `RedsysLiteAPI` (D-016/D-017); integration tests for `WC_Gateway_redsys` (D-018), `WC_Gateway_Bizum_Redsys` (D-019, real WC_Order fixture), `WC_Gateway_GooglePay_Redirection_Redsys` (D-020), `WC_Gateway_Inespay_Redsys` (D-021, own signature algorithm + WPDieException); Playwright checkout smoke test (D-022); found + escalated + fixed a real signature-bypass vulnerability in GooglePay (D-020); full-plugin review via 4 parallel agents (findings independently re-verified) found + fixed a wrong-secret bug in `successful_request()` for Bizum + GooglePay (D-023), documented 7 lower-severity findings (D-024) and a plaintext-secret-in-order-meta finding the user chose to defer (D-025); 31 automated tests total, all mutation/regression-verified. L-003 and L-004 recorded. 11 commits pushed to `develop`. Two security/correctness fixes unreleased — flagged as a release priority. |
