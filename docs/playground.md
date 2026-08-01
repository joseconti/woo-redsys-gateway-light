# Playground — local WordPress + WooCommerce environment

> last verified: not yet run — this environment (the session that wrote this
> file) has Docker and Node available (`scripts/keel-doctor --check` passes),
> but `npx wp-env start` was not executed as part of writing this document.
> The first session that runs it should update this stamp with the real date
> and the real command output.

This project uses [`@wordpress/env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) (`wp-env`), configured at the repo root
in `.wp-env.json`. It runs WordPress + WooCommerce + this plugin inside Docker
containers — nothing is installed on your machine beyond Node.js and Docker
itself.

## Prerequisites

- Docker Desktop (or Docker Engine / Colima) running.
- Node.js >= 18 and npm.
- Run `scripts/keel-doctor --check` first — it verifies both, plus that the
  Docker daemon actually responds (not just that the CLI is installed).

## Start it

```
npx wp-env start
```

First run downloads the pinned WordPress core (7.0) and WooCommerce (7.4.0,
the plugin's declared `WC requires at least` version) into local Docker
volumes, then activates this plugin and WooCommerce automatically (per
`.wp-env.json`'s `plugins` list). Subsequent starts are fast — everything is
cached in Docker volumes.

WordPress 7.0 and WooCommerce 7.4.0 are pinned deliberately: they are what
`woocommerce-redsys.php`'s header and `readme.txt` actually declare support
for (`Tested up to: 7.0`, `WC requires at least: 7.4`). Testing against a
newer "latest" would not prove anything about the versions the plugin
promises to work on. PHP is pinned to 7.4 in `.wp-env.json` — one step above
the plugin's stated `Requires PHP: 7.0` floor — because WooCommerce 7.4 itself
requires PHP >= 7.4; PHP 7.0 is declared plugin-side but is not realistically
testable against a current WooCommerce.

## What you get

- **Site URL:** http://localhost:8888
- **Admin URL:** http://localhost:8888/wp-admin
- **Admin username:** `admin`
- **Admin password:** `password`
- **Test-suite site (used for automated tests, not for browsing):** http://localhost:8889

These are `wp-env`'s own well-known defaults — throwaway, local-only
credentials with no relation to any real account. They only ever protect a
disposable Docker container on your own machine; never reuse them anywhere
real, and never point this environment at a public URL.

## Reset it

```
npx wp-env clean all
```

Wipes the database back to a fresh install (WordPress + WooCommerce +
this plugin active, no other content) without tearing down the containers.
For a full teardown and rebuild:

```
npx wp-env destroy
npx wp-env start
```

## Stop it

```
npx wp-env stop
```

## Reading the WordPress debug log

`.wp-env.json` sets `WP_DEBUG_LOG: true`, so PHP notices, warnings and
fatals land in the container's `wp-content/debug.log`. Read it with:

```
npx wp-env run cli tail -n 100 wp-content/debug.log
```

A flow that "looks fine" while the debug log gained a notice or a fatal has
not actually passed — always check this after driving a flow, not only when
something visibly breaks.

## Try it yourself — step by step

Everything below except the marked `⚠ unverified — CREDENTIAL` step can be
walked with zero external accounts: it is all local, disposable, and
resettable with `npx wp-env clean all`.

1. **Start the environment:** `npx wp-env start`, then open
   http://localhost:8888/wp-admin and log in with `admin` / `password`.
2. **Confirm WooCommerce and the plugin are active:** Plugins → Installed
   Plugins should show "WooCommerce" and "Payment Gateway for Redsys &
   WooCommerce Lite" both active (this project's `.wp-env.json` activates
   both automatically on first start — if either shows inactive, activate it
   manually here).
3. **Add a product:** Products → Add New, give it a name and a price
   (e.g. "Test product", €10.00), Publish it.
4. **Open the gateway settings:** WooCommerce → Settings → Payments. The
   Redsys gateways (card redirection, Bizum, Apple/Google Pay redirection,
   Inespay) should each appear in the payment methods list and each should
   open a configuration screen when clicked, with the expected fields
   (merchant code, terminal, secret key, environment toggle, etc.) — this
   confirms the settings screens render without a PHP fatal.
5. **Confirm the gateway appears at checkout:** add the test product to the
   cart, go to Checkout, and confirm at least one Redsys payment method is
   listed among the payment options (it will show as configured-but-unusable
   until a merchant code/secret key is entered — that is expected without
   real credentials).
6. **Verify the fail-closed notification behaviour (no credentials
   needed):** this is the highest-value thing to verify locally, and it does
   not need a real Redsys account. Per `docs/threat-model.md`, the plugin's
   notification endpoint (`?wc-api=WC_Gateway_redsys`, and the equivalent for
   each of the other three gateways) must **reject** any notification whose
   HMAC-SHA256 signature does not validate — it must never mark an order paid
   from an unsigned or badly-signed POST. To exercise this:
   - Place an order (any payment method) so an order exists in
     `Processing`/`Pending payment` status.
   - Send a fabricated, unsigned POST to the notification endpoint, e.g.:
     ```
     curl -i -X POST "http://localhost:8888/?wc-api=WC_Gateway_redsys" \
       --data "Ds_Order=000000001&Ds_Response=0000&Ds_Signature=not-a-real-signature&Ds_SignatureVersion=HMAC_SHA256_V1&Ds_MerchantParameters=bm90LXJlYWwtcGFyYW1z"
     ```
   - Reload the order in WooCommerce admin (Orders → that order) and confirm
     its status did **not** change to `Processing`/`Completed` as a result of
     that POST, and that the debug log (see above) shows the notification
     was rejected rather than silently accepted.
   - This is drivable and repeatable without any external account — it is
     exactly the fail-closed control `docs/threat-model.md` records as
     `IN PLACE`.
7. **⚠ unverified — CREDENTIAL: a real Redsys sandbox checkout end to end.**
   Completing an actual redirection round-trip to Redsys's own test/sandbox
   environment needs a Redsys **test merchant code, terminal number and
   secret key**, which are issued by Redsys (or by the acquiring bank) to a
   real (test) merchant account — this project has no such credentials on
   file, and Redsys does not publish universal, reusable "anyone can use
   these" test credentials the way some gateways do. Whoever holds a Redsys
   test merchant contract can enter those values into the gateway settings
   screen from step 4 and complete the redirection flow manually; until then
   this leg stays `⚠ unverified — CREDENTIAL` and steps 1–6 above are what
   the assistant can and does verify without it.

## What this playground does NOT verify

- A real Redsys/Bizum/Apple-Google Pay/Inespay round trip against Redsys's
  live test environment (`CREDENTIAL`, step 7 above).
- Anything requiring HTTPS-only behaviour that Redsys's real endpoints may
  enforce — this local environment is plain HTTP.
- Email deliverability of WooCommerce order emails (no SMTP is configured in
  `.wp-env.json`; add a mail-catching plugin if that becomes relevant to a
  future slice).
