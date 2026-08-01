# Security — Payment Gateway for Redsys & WooCommerce Lite

> Consolidated from `references/security/wordpress.md` (the loaded profile) and `docs/threat-model.md`. Only `IN PLACE` controls are stated in the present tense.

## Profile
WordPress plugin / WooCommerce extension — `references/security/wordpress.md` (D-001).

## Posture summary
This is a payment-gateway plugin whose core security surface is **notification-signature verification**: every payment confirmation arrives over an unauthenticated HTTP callback (`?wc-api=WC_Gateway_<id>`) and is only trusted after its HMAC_SHA256_V1 signature is verified against the merchant's configured secret. That verification is `IN PLACE` and fails closed when unconfigured (see `docs/threat-model.md`). The remaining posture gaps are process gaps (no automated security tests, no dependency-audit habit, no CI) rather than known live vulnerabilities.

## Full detail
See `docs/threat-model.md` for the complete assumptions / defended-controls (with delivery state) / not-defended tables. This file is the pointer Phase 6 consolidates into; nothing here restates it in different words to avoid drift.

## Process gaps (see `docs/04-adoption-audit.md`, Security dimension, for the full list)
- No automated security tests (signature verification, fail-closed paths) — `TO BUILD`.
- No dependency-audit habit (`npm audit` never run against `package.json` devDependencies) — `TO BUILD`.
- Output-escaping was spot-checked, not exhaustively verified across the whole tree — `VERIFY`.
- Capability checks beyond WooCommerce's default Settings API gating were not independently re-verified — `VERIFY`.
