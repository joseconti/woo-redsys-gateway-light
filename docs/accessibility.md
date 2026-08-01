# Accessibility — Payment Gateway for Redsys & WooCommerce Lite

> Target: WCAG 2.2 AA floor, AAA where feasible (D-007). This record is created during adoption; no automated or assistive-technology pass has been run yet against this plugin's specific screens — every item below is honestly `TO BUILD`/`VERIFY`, never claimed as passed without evidence.

## Scope
- Admin settings screens (WooCommerce → Settings → Payments → [gateway]) — rendered by WooCommerce's own Settings API, so baseline accessibility inherits from WooCommerce/WordPress admin markup; this plugin's own contribution is limited to field labels, descriptions and a few custom notices (`assets/css/redsys-notice.css`, `welcome.css`).
- Checkout UI — classic checkout (WooCommerce-rendered) and Blocks checkout (`includes/blocks/*-support.php` + `assets/js/frontend/blocks.js`), where this plugin contributes the gateway's title, description, icon and radio option per payment method.

## Automated pass
`TO BUILD` — not run during adoption (adoption is read-only for code; no `scripts/keel-doctor`/CI exists yet to host an axe-core/pa11y/Lighthouse run). Proposed for the first real Phase 5 sprint that touches the checkout UI, or as a standalone accessibility sprint if the user wants results before then.

## Guided assistive-technology pass
`TO BUILD` — not run. Per `references/accessibility.md`, this is a real screen-reader/keyboard-navigation pass driven by the user in a guided loop, one instruction at a time. Proposed at the same time as the automated pass.

## Known risk areas (informed guess from reading the code, not verified)
- Payment-method icons (`bizum.png`, `GPay.svg`, `inespay.svg`, etc.) — need to confirm they carry appropriate `alt` text at the point they're rendered in checkout (classic + Blocks) rather than being purely decorative without a text alternative for the gateway name.
- Admin settings fields — WooCommerce's Settings API generally provides labeled inputs, but any custom field type this plugin defines should be checked for a properly associated `<label>`.

## Next steps
Recorded as a deferred item, not blocking adoption: run the automated pass + the guided AT loop the first time the checkout UI or admin settings screens are next touched by a slice, or sooner on request.
