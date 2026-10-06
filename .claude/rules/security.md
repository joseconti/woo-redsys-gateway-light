---
paths:
  - "woocommerce-redsys.php"
  - "about-redsys.php"
  - "classes/**/*.php"
  - "includes/**/*.php"
  - "resources/js/**/*.js"
  - ".wp-env-mu-plugins/**"
  - "tests/**"
---

# Security — Payment Gateway for Redsys & WooCommerce Lite

Sources: the Keel WordPress/WooCommerce security profile (D-001), docs/threat-model.md, docs/security.md. The notification endpoint (`?wc-api=WC_Gateway_<id>`) is unauthenticated: the signature check is the only thing standing between the internet and "order paid".

- Verify the notification signature BEFORE anything else: no order lookup, no meta write, no status change, no blocking call ahead of it (D-026).
- Fail closed. No configured secret means reject — never fall back to the merchant code or any public value (D-020).
- Verify against the secret that actually signed the request: live, test-mode and per-user test secrets resolve differently per gateway (D-023). Read the whole method of a "twin" gateway before assuming it behaves like its sibling (L-003).
- Compare signatures, tokens and keys with `hash_equals()`, never `==` / `===` (D-026). Do not roll custom crypto; signature logic lives in `RedsysLiteAPI`.
- Never log, echo, export or newly persist a signing secret, API key or token. Recorded omission: Bizum and Google Pay still write the secret to order meta (D-025, deferred by the user) — do not extend that pattern, do not copy it to another gateway, and do not "fix" it on your own initiative; it is the user's open decision. It is not mentioned in public text such as `readme.txt` until fixed (D-032).
- Cross-check the notified amount against the order total before marking it paid; a mismatch goes `on-hold`.
- Sanitize every external input (`wp_unslash()` + the right `sanitize_*`); escape every output at print time with the right `esc_*`, translated strings included.
- Any state-changing admin action carries a nonce AND a capability check. Gateway configuration goes through the WooCommerce Settings API only; add no new unauthenticated endpoint.
- Order data goes through WooCommerce CRUD (`$order->get_meta()`, `update_meta_data()`, `set_payment_method()` — L-004); any custom SQL uses `$wpdb->prepare()`.
- Every PHP file starts with the `ABSPATH` direct-access guard. Dev-only code (HTTP stubs) lives in `.wp-env-mu-plugins/` and never ships (D-029).
- No secret, credential or real customer data in code, tests, fixtures or docs. Tests use self-describing fake values; docs describe a secret's shape instead of pasting one. `.githooks/pre-commit` is the net, not the control.

Full profile: the Keel security reference for this project type governs; this file is the reminder, not the standard.
