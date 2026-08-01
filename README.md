# Payment Gateway for Redsys & WooCommerce Lite

WooCommerce payment gateway plugin for [Redsys](https://www.redsys.es/) (the payment processor used by most Spanish banks): card payments via redirection, plus Bizum, Apple Pay/Google Pay redirection, and Inespay bank-transfer redirection. Free "Lite" version of a [premium plugin](https://woocommerce.com/products/redsys-gateway/); distributed on [WordPress.org](https://wordpress.org/plugins/woo-redsys-gateway-light/).

- **Author:** José Conti — https://plugins.joseconti.com/
- **License:** GPL-2.0-or-later (see `LICENSE`)
- **Requires:** PHP ≥ 7.0, WordPress (tested up to 7.0), WooCommerce ≥ 7.4 (tested up to 10.9)

## Development

This project is developed under the [Keel](https://github.com/joseconti/keel-skill) workflow — see `docs/PROGRESS.md` for the current state and `docs/03-technical-plan.md` for the code map and conventions.

```bash
npm install
npm run build     # compiles resources/js/frontend/index.js → assets/js/frontend/blocks.js
npm run start      # watch mode
npm run i18n:pot   # regenerates languages/*.pot (requires WP-CLI)
```

No automated test suite exists yet (tracked in `docs/04-adoption-audit.md`, Testability).

## Documentation

| File | Purpose |
|---|---|
| `docs/PROGRESS.md` | Current project state — read this first |
| `docs/02-functional-spec.md` | Features and flows |
| `docs/03-technical-plan.md` | Stack, code map, conventions |
| `docs/architecture.md` | Consolidated architecture |
| `docs/security.md` + `docs/threat-model.md` | Security posture |
| `docs/api/INDEX.md` | Public hooks/filters this plugin exposes |
| `docs/issues.md` | GitHub issue tracking log |
| `docs/04-adoption-audit.md` | Gap audit vs Keel standards |

## Support

Issues: https://github.com/joseconti/woo-redsys-gateway-light/issues
