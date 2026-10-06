# Installation

> Developer- and operator-facing. Written from `readme.txt`, the plugin header in `woocommerce-redsys.php` and the code on 2026-10-06. An end-user guide was declined for this project (D-013).

## Requirements

| Requirement | Value | Declared in |
|-------------|-------|-------------|
| WordPress | 4.0 or later; tested up to 7.0 | `readme.txt` (`Requires at least`, `Tested up to`) |
| PHP | 7.0 or later | `readme.txt` (`Requires PHP`) |
| WooCommerce | 7.4 or later; tested up to 10.9 | `woocommerce-redsys.php` header (`WC requires at least`, `WC tested up to`) |
| WooCommerce active | required | `woocommerce-redsys.php` header (`Requires Plugins: woocommerce`) |
| PHP `openssl` extension | required for signatures (`openssl_encrypt()` in `includes/class-redsysliteapi.php`) | code |
| PHP `mbstring` extension | required by Bizum and Google Pay (`mb_convert_encoding()` in `get_redsys_sha256()`) | code |
| Store currency | one of the currencies in `includes/data/allowed-currencies.php` for the three Redsys-protocol gateways; EUR for Inespay | code |
| A merchant contract | Redsys credentials from your bank (card, Bizum, Google Pay) or an Inespay project (bank transfer) | external |

The declared floors are what the plugin states, not what has been tested: the project's own playground runs WordPress 7.0, WooCommerce 7.4.0 and PHP 7.4 (`.wp-env.json`), and PHP 7.0 cannot be tested against a current WooCommerce (`docs/playground.md`).

The notification URLs must be reachable from the internet by Redsys and Inespay. A site behind HTTP authentication, a maintenance page or a firewall that blocks them will take payments that never confirm.

## Install the released plugin

The steps in `readme.txt` are these. The plugin is also distributed on WordPress.org, so it can be installed from Plugins → Add New by searching for its name instead of step 1.

1. Unzip the package and upload the folder to `wp-content/plugins/`, overwriting any older version.
2. Activate the plugin in the WordPress admin.
3. Open WooCommerce → Settings → Payments.
4. Open the gateway you want to use.
5. Configure it — see `docs/usage/configuration.md`.

## What activation does

- There is no activation, deactivation or uninstall routine: the plugin registers none (no `register_activation_hook`, `register_deactivation_hook` or `uninstall.php`). Nothing is created in the database on activation.
- On the first admin page load after activation, and again after every update, `redsys_welcome_splash()` stores the running version in the option `woocommerce-redsys-version` and redirects once to WooCommerce → About Redsys.
- Four gateways are added to WooCommerce, all disabled by default: Redsys (card), Bizum, Google Pay redirection and Inespay.
- Two admin notices appear until dismissed: the "updated to version…" notice and the Telegram-channel notice.
- Compatibility with WooCommerce High-Performance Order Storage is declared.

## Removing the plugin

Deleting the plugin removes its files only. The stored settings and options listed in `docs/usage/configuration.md` ("Stored data") remain in the database, including the merchant secrets. Delete those options by hand if the site must not keep them.

## Install from source (development)

The repository is the plugin directory itself.

```bash
git clone https://github.com/joseconti/woo-redsys-gateway-light.git
cd woo-redsys-gateway-light
npm install        # build and test tooling
npm run build:assets   # rebuilds the Blocks script (readable and minified) and the minified stylesheets
npm run build      # the same, then the translations
```

- `npm run build` runs `npm run build:assets` and then `npm run i18n:build`, which needs WP-CLI on the path (`package.json`).
- The plugin loads the minified stylesheets and script. Set `SCRIPT_DEBUG` to `true` in `wp-config.php` to load the readable files instead.
- PHP test dependencies are installed with Composer inside the playground container; the exact commands are in `docs/03-technical-plan.md` (Testing) and `docs/playground.md`.
- To run the plugin locally, use the playground: `npx wp-env start` (`docs/playground.md`).

The built file `assets/js/frontend/blocks.js` is what the Blocks checkout loads. Edit the source and rebuild; never edit the built file.

## Upgrading

Replace the plugin files with the new version. There are no database migrations. Settings are kept, because they live in WooCommerce's own options. After an update the admin is redirected once to the About page.

## Related
- `docs/usage/configuration.md` — every setting.
- `docs/usage/getting-started.md` — the shortest path to a first test payment.
- `docs/playground.md` — the local environment.
