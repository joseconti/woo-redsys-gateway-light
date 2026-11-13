# Playground — local WordPress + WooCommerce environment

> last verified: 2026-10-06 — rebuilt from nothing (`npx wp-env destroy`,
> `npx wp-env start`, `scripts/playground-setup`) on `@wordpress/env` 11.16.0:
> PHP 7.4.33, WordPress 7.0, WooCommerce 7.4.0, and the whole suite green
> (8 unit, 36 integration, 7 e2e). See D-051 and L-008.
>
> Earlier, 2026-08-01: step 6 below (fail-closed notification check) was
> driven for real — two fabricated POSTs to `?wc-api=WC_Gateway_redsys` were
> rejected with HTTP 500 and the order stayed `wc-pending`. Step 7 (a real
> Redsys sandbox round trip) remains `⚠ unverified — CREDENTIAL`.

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
scripts/playground-setup
```

`scripts/playground-setup` is needed once per fresh instance (after the
first start, `npx wp-env clean all` or `npx wp-env destroy`) and is safe to
re-run: it activates this plugin in both environments (wp-env does not do it
reliably, L-002), installs PHPUnit into `vendor/`, and applies everything
under "One-time environment setup" below.

First run downloads the pinned WordPress core (7.0) and WooCommerce (7.4.0,
the plugin's declared `WC requires at least` version) into local Docker
volumes. Subsequent starts are fast — everything is
cached in Docker volumes.

WordPress 7.0 and WooCommerce 7.4.0 are pinned deliberately: they are what
`woocommerce-redsys.php`'s header and `readme.txt` actually declare support
for (`Tested up to: 7.0`, `WC requires at least: 7.4`). Testing against a
newer "latest" would not prove anything about the versions the plugin
promises to work on. PHP is pinned to 7.4 in `.wp-env.json` — one step above
the plugin's stated `Requires PHP: 7.0` floor — because WooCommerce 7.4 itself
requires PHP >= 7.4; PHP 7.0 is declared plugin-side but is not realistically
testable against a current WooCommerce.

`.wp-env.json` also switches WordPress's automatic updater off. Without it
the development site updated itself from 7.0 to 7.1.2 within minutes of
starting, so the "pinned" environment was not the one being tested (L-008).

## The ceiling instance (the other end of the support matrix)

`.wp-env.ceiling.json` is a second, independent instance: WordPress 7.1.3
(the newest 7.1.x, the plugin's `Tested up to`), WooCommerce 11.2.1 (the
newest 11.2.x, its `WC tested up to`) and PHP 8.4, on ports 8890 and 8891
(moved from 7.0.7 / 10.9.4 / 8.3 in S-083, D-092).
It runs beside the pinned one; neither replaces the other. A fresh
WooCommerce 11.2 store uses the High-Performance Order Storage on the
development site, and the tests site (PHPUnit) uses the legacy storage, so
the two suites cover both.

```
npx wp-env start --config .wp-env.ceiling.json
WP_ENV_CONFIG=.wp-env.ceiling.json scripts/playground-setup
npx wp-env run --config .wp-env.ceiling.json cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && vendor/bin/phpunit"
npx wp-env run --config .wp-env.ceiling.json tests-cli bash -c "cd wp-content/plugins/woo-redsys-gateway-light && vendor/bin/phpunit -c phpunit-integration.xml.dist"
WP_ENV_CONFIG=.wp-env.ceiling.json PLAYGROUND_URL=http://localhost:8890 npx playwright test
```

The browser suite needs both variables: `PLAYGROUND_URL` is where the browser goes, and `WP_ENV_CONFIG` is where the specs that prepare an order through `wp-env run` prepare it (three specs since S-059 and S-060). With the first alone they create their orders in the pinned instance and fail here (seen on 2026-10-06, S-071: 18 failed).

Run `scripts/playground-setup` again after the integration suite and before
the browser suite: the integration suite leaves the tests site with no
plugin active. `scripts/keel-affected-tests` and the pre-push hook drive the
pinned instance only; the ceiling is run by hand, at the release gate.

Last run, 2026-10-06 at the tree of S-072 (D-079): unit `OK (34 tests, 71
assertions)`, integration `OK (344 tests, 2012 assertions)`, browser `42
passed, 5 failed` — four are the settings-form accessibility scans, on
WooCommerce's own help-tip markup (`docs/accessibility.md`), and one is the
Inespay case of `checkout-blocks-other-gateways.spec.js`, which fails about
one run in four here: the country it sets through the classic checkout does
not always reach the Blocks page.

The PHP version (8.3) is the assistant's pick and was not asked. WordPress
7.1.3 and WooCommerce 11.1.2 were current on that day and are NOT covered:
the instance tests what the plugin declares, not what is newest.

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

## Automated checkout smoke test (Playwright)

`tests/e2e/checkout-redsys.spec.js` drives a real guest checkout through this
playground with the Redsys gateway — product → checkout → order → the
generated Redsys payment form — using Playwright/Chromium. It never lets a
real request reach `*.redsys.es` (every such request is intercepted and
aborted), so it proves the plugin's own checkout → signed-form chain works
without depending on Redsys's live infrastructure or needing a real
merchant contract. **Real run, 2026-08-01: 1 passed.** Mutation-tested for
real: temporarily corrupted the merchant code sent in the form (`DS_MERCHANT_MERCHANTCODE`),
confirmed the test failed on the expected assertion, reverted, confirmed it
passed again.

### One-time environment setup (done by `scripts/playground-setup`)

The commands below are what the script runs; they are kept here as the
explanation of each step, not as something to type.

This project's `wp-env` starts with plain (`?p=123`) permalinks and no
payment gateway configured, neither of which the classic WooCommerce
checkout page needs to render correctly for a human clicking through the
admin — but the e2e test visits pretty URLs (`/checkout/`) and needs a
gateway to select. Run once after `npx wp-env start` (or after
`npx wp-env clean all`):

```
npx wp-env run cli wp rewrite structure '/%postname%/'
npx wp-env run cli wp rewrite flush --hard
npx wp-env run cli wp option update woocommerce_redsys_settings --format=json \
  '{"enabled":"yes","title":"Redsys","description":"Pay with card via Redsys","customer":"999008881","commercename":"Test Shop","payoptions":"T","terminal":"1","not_use_https":"no","lwvactive":"no","orderdo":"processing","secretsha256":"sq7HjrUOBfKmC576ILgskD5srU870gJ7","customtestsha256":"","redsyslanguage":"002","testmode":"yes","debug":"no"}'
npx wp-env run cli wp option update woocommerce_bizumredsys_settings --format=json \
  '{"enabled":"yes","title":"Bizum","description":"Pay via Bizum you can pay with your Bizum account.","customer":"999008881","commercename":"Test Shop","terminal":"1","orderdo":"processing","not_use_https":"no","secretsha256":"sq7HjrUOBfKmC576ILgskD5srU870gJ7","customtestsha256":"","redsyslanguage":"002","testmode":"yes","debug":"no"}'
npx wp-env run cli wp option update woocommerce_googlepayredirecredsys_settings --format=json \
  '{"enabled":"yes","title":"Google Pay","description":"Pay via GPay you can pay with your Google account.","customer":"999008881","commercename":"Test Shop","terminal":"1","not_use_https":"no","secretsha256":"sq7HjrUOBfKmC576ILgskD5srU870gJ7","customtestsha256":"","redsyslanguage":"002","testmode":"yes","testshowgateway":[""],"debug":"no"}'
```

`999008881` / terminal `1` / `sq7HjrUOBfKmC576ILgskD5srU870gJ7` are Redsys's
own widely-published generic test/demo merchant values — the exact same
secret this plugin's own source already hardcodes as `$this->testsha256`'s
default in `class-wc-gateway-redsys.php`, `class-wc-gateway-bizum-redsys.php`
and `class-wc-gateway-googlepay-redirection-redsys.php`. They are used here
only to exercise this plugin's own form-generation code locally; **this
does not confirm they authenticate against Redsys's real sandbox** — no
network request reaches Redsys during this test (see above), so that
remains unverified and is not claimed. A real round trip against Redsys's
live test environment still needs credentials issued to an actual
(test) merchant account, per step 7 above.

`googlepayredirecredsys`'s `testshowgateway:[""]` is needed ONLY for this
gateway: `check_user_show_payment_method()` hides Google Pay from any guest
whenever `testmode` is `yes` and this allowlist option is unset — a
deliberate feature (don't show a live-test-mode gateway to anonymous real
customers), not a bug. `[""]` is the value that satisfies the class's own
"show to everyone" branch; see D-027 (`docs/decisions.md`) for the full
trace of why this was needed.

All three gateways are enabled simultaneously in this playground, so none of
them is WooCommerce's auto-selected/hidden "only one gateway" case — every
e2e spec explicitly selects its own gateway's radio button.

The checkout specs add post ID 10 ("Test Product") to the cart. On a fresh
install IDs 1 to 9 are taken by the defaults, so the script creates that
product first and stops with an error if it did not get ID 10 — which
happens when something else was created before it; reset with
`npx wp-env clean all` and run the script again.

#### WooCommerce Blocks checkout page (for `checkout-blocks-redsys.spec.js`)

The default Checkout page uses the classic `[woocommerce_checkout]`
shortcode. A second page using the WooCommerce Blocks `Checkout` block is
needed for the Blocks-checkout e2e test:

```
npx wp-env run cli wp post create --post_type=page \
  --post_title="Checkout Blocks" --post_name="checkout-blocks" \
  --post_status=publish \
  --post_content='<!-- wp:woocommerce/checkout --><div class="wp-block-woocommerce-checkout"></div><!-- /wp:woocommerce/checkout -->'
```

The block needs its wrapper element. The self-closing form
(`<!-- wp:woocommerce/checkout /-->`) renders an empty page on the pinned
WooCommerce 7.4 (Blocks 9.4.3) — measured on 2026-10-06 (L-008); an earlier
version of this file claimed the opposite.

#### Inespay checkout page (for `checkout-inespay.spec.js`)

Inespay's `process_payment()` makes a real server-side API call
(`apiflow.inespay.com`) during checkout, which Playwright cannot intercept
in the browser. `.wp-env-mu-plugins/inespay-http-stub.php` — mapped into
`wp-content/mu-plugins` via `.wp-env.json`'s `mappings` key, dev/test infra
only, never shipped in the plugin — fakes that API response, but only when
explicitly turned on:

```
npx wp-env run cli wp option update redsyslite_e2e_stub_inespay yes
npx wp-env run cli wp option update woocommerce_inespayredsys_settings --format=json \
  '{"enabled":"yes","title":"Inespay Bank Transfer","description":"Pay via instant bank transfer.","api_key":"e2e-test-api-key","api_token":"e2e-test-api-token","testmode":"yes","transactionlimit":"200","debug":"no"}'
npx wp-env run cli wp wc product create --user=1 --name="E2E Transaction Limit Product" --type=simple --regular_price=200.50 --porcelain
```

`api_key`/`api_token` are dummy values — the stub intercepts the request
before either is ever sent anywhere real. Inespay is also only offered in
`ES`/`PT`/`IT` (`is_allowed_country()`), and this playground's store base
location is `US`, so the e2e specs themselves set the billing country to
Spain before expecting to see the gateway — no extra playground setup
needed for that part. See D-029 (`docs/decisions.md`) for the full design
rationale.

`transactionlimit: "200"` and the `E2E Transaction Limit Product` (€200.50,
created by the command above, added to the cart by its slug
`/product/e2e-transaction-limit-product/` rather than a numeric post ID)
exist for `inespay-transaction-limit.spec.js`'s fractional-total coverage of
`disable_inespay()` — see D-030 (`docs/decisions.md`).

#### Mutation-testing gotcha: the `wordpress` container caches PHP via opcache

If you mutation-test a PHP change (temporarily break something, confirm a
test fails, revert, confirm it passes again) and the "should now be green"
run still shows the OLD (corrupted) behavior even though the file on disk is
confirmed reverted, **don't re-suspect the revert** — opcache in the
persistent `wordpress` container can cache the corrupted bytecode across a
same-second file edit. Fix: `docker restart <project>-wordpress-1` (find the
exact name with `docker ps`), then re-run. See L-005 in
`docs/lessons-learned.md` for the full diagnosis (this does NOT affect
PHPUnit, which runs as a fresh short-lived CLI process every time).

### Running it

```
npm install          # once, installs @playwright/test
npx playwright install --with-deps chromium   # once, downloads the browser
npx wp-env start      # if not already running
npx playwright test   # or: npm run test:e2e
```

## Inbound tunnel (Redsys/Inespay notifications reaching the playground)

The playground lives on `localhost`, so Redsys and Inespay cannot send their
server-to-server notification to it. Without a tunnel, a sandbox payment stops
at "pending". `scripts/playground-tunnel` opens a public HTTPS URL that forwards
to the playground for as long as the exercise lasts. The inventory of the four
notification endpoints is in `docs/03-technical-plan.md` → "Inbound calls".
It needs `cloudflared` (`brew install cloudflared`). `scripts/keel-doctor`
reports it as an optional row.

```
scripts/playground-tunnel                 # open a Cloudflare quick tunnel, point WordPress at it, wait
scripts/playground-tunnel --url <https>   # point WordPress at a tunnel you already opened
scripts/playground-tunnel --restore       # back to http://localhost:8888 (safe to repeat)
```

**What it changes while it is open:**

- `WP_HOME` and `WP_SITEURL` in the playground's `wp-config.php` are set to
  the tunnel URL. Every gateway builds its notification URL from that address,
  so `DS_MERCHANT_MERCHANTURL` (Redsys, Bizum, Google Pay) and Inespay's
  `notifUrl` now carry the tunnel hostname. The script prints the four
  endpoints.
- A playground-only must-use plugin,
  `.wp-env-mu-plugins/keel-playground-tunnel-https.php`, tells WordPress the
  request is HTTPS when the tunnel says so (`X-Forwarded-Proto`). It never
  ships: `.wp-env-mu-plugins/` is `export-ignore`.
- While the tunnel is open, the playground (including `/wp-admin`) is
  reachable through it by anyone who has the URL, with the `admin` /
  `password` credentials above. That is why the script refuses to start while
  any enabled gateway is out of test mode. Only test keys go in front of it,
  such as the public Redsys test key `scripts/playground-setup` writes. Don't
  paste the URL into commits, issues or public notes.
- Leave the Redsys gateways' "not use https" option off. It rewrites the
  notification URL to `http:`.

**How it is closed:** press Ctrl-C in the terminal running it, or run
`kill -TERM <pid>` (the PID is printed). From a shell that started with
SIGINT ignored (a background job, an assistant's tool shell), only TERM
reaches the trap. Every exit path closes the tunnel, deletes the HTTPS
plugin and puts `WP_HOME`/`WP_SITEURL` back on `http://localhost:8888`.
That includes the tunnel dying on its own, the URL never appearing (it
gives up after `PLAYGROUND_TUNNEL_TIMEOUT` seconds, default 30, and prints
cloudflared's last lines), and a failed switch. After a `kill -9`, or after
`--url`, run `scripts/playground-tunnel --restore`. Never leave a tunnel
open between sessions. A playground still pointing at a dead hostname
looks like a product bug.

Exit codes: 0 done, 2 could not run. It exits 2 without changing anything
in these cases: the playground is not answering, port 8888 belongs to
another checkout's wp-env, `cloudflared` is missing, a gateway is out of
test mode, or `~/.cloudflared/config.yml` exists. That last file is yours
and the script never edits it. Open your own tunnel and use `--url`.

**Last real run: none yet.** The script's exit paths were exercised against
stand-ins for `npx wp-env` and `cloudflared` (S-087). No public tunnel to the
playground has been opened. The one provider acceptance check, a Redsys
test-terminal payment whose notification URL is on the tunnel, is deferred to
S-037 by the owner (D-093). Until it runs, it is unknown whether Redsys
accepts a `trycloudflare.com` notification URL. If it does not, the next step
is a named tunnel, not a deployment.

## What this playground does NOT verify

- A real Redsys/Bizum/Apple-Google Pay/Inespay round trip against Redsys's
  live test environment (`CREDENTIAL`, step 7 above).
- Anything requiring HTTPS-only behaviour that Redsys's real endpoints may
  enforce — this local environment is plain HTTP (the inbound tunnel above
  serves it over HTTPS, but no Redsys run through it has happened yet:
  S-037, D-093).
- Email deliverability of WooCommerce order emails (no SMTP is configured in
  `.wp-env.json`; add a mail-catching plugin if that becomes relevant to a
  future slice).
