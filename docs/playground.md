# Playground — local WordPress + WooCommerce environment

> last verified: 2026-11-07 — on the shared Parallels VM `keel-development`
> (D-114, S-104), at the tree of that day:
>
> - **Floor, `wrgl`** (PHP 7.4, WordPress 7.0, WooCommerce 7.4.0): the full
>   suite green — unit `OK (41 tests, 78 assertions)`, integration
>   `OK (410 tests, 2190 assertions)`, browser `67 passed` (1.2 min; the same
>   suite took 2.8 min on wp-env).
> - **Ceiling, `wrgl-ceiling`** (PHP 8.4, WordPress 7.1.3, WooCommerce
>   11.2.1): unit `OK` (41 tests), integration `OK` (410 tests), browser
>   `63 passed, 4 failed` — the four admin settings-form axe scans
>   (`aria-prohibited-attr` on WooCommerce's own `.woocommerce-help-tip`
>   markup), the same four recorded on wp-env (S-083, `docs/accessibility.md`).
>   Nothing newly red.
> - The inbound tunnel (`scripts/playground-tunnel`) has **not** been driven
>   since the move: `⚠ unverified since D-114`.
>
> Earlier, 2026-08-01, on wp-env: step 6 below (fail-closed notification
> check) was driven for real — two fabricated POSTs to
> `?wc-api=WC_Gateway_redsys` were rejected with HTTP 500 and the order stayed
> `wc-pending`. Step 7 (a real Redsys sandbox round trip) remains
> `⚠ unverified — CREDENTIAL`.

The playground is two WordPress sites on the shared Parallels virtual machine
`keel-development`, driven by `scripts/keel-site` (the authority for every
command below; `scripts/keel-site --help` lists them). It replaced
`@wordpress/env` (wp-env, Docker) on 2026-11-07 (D-114); `.wp-env.json` and
`.wp-env.ceiling.json` no longer exist and there is no Docker in the test path.

The sites are ordinary hosting tenants — files, a database, a PHP-FPM pool
running as the site's own Unix user, `open_basedir`, no root — which is the
environment a WordPress plugin actually runs on.

## The rule that comes first: never beside the premium plugin

**The Lite plugin is never active, nor even installed, on the same site as
the premium plugin `woocommerce-gateway-redsys` (WooCommerce Redsys Gateway)**
(owner's rule, D-114). On `keel-development` the premium plugin has its own
tenant, `redsys`. `scripts/keel-site` refuses `reset` and `test` on a site of
this project where the premium plugin is installed, `create` stops there too,
and `scripts/keel-site doctor` reports `premium plugin PRESENT` for it. Never
install it on `wrgl` or `wrgl-ceiling` to "compare" anything.

## The two sites

| Site | Role | PHP | WordPress | WooCommerce | URL |
|---|---|---|---|---|---|
| `wrgl` | floor — the default | 7.4 | 7.0 | 7.4.0 | `http://10.211.55.9:8101` |
| `wrgl-ceiling` | ceiling | 8.4 | 7.1.3 | 11.2.1 | `http://10.211.55.9:8102` |

The address is the VM's, as `scripts/keel-site url` prints it (it reads it
from the VM on every call); the host reaches it directly. Choose the site with
`--site <name>` or the environment variable `KEEL_SITE`; with neither, every
command acts on `wrgl`.

The floor is pinned deliberately: WordPress 7.0 and WooCommerce 7.4.0 are what
`woocommerce-redsys.php`'s header and `readme.txt` declare
(`WC requires at least: 7.4`). PHP is 7.4, one step above the plugin's stated
`Requires PHP: 7.0`, because WooCommerce 7.4 itself requires PHP >= 7.4; PHP
7.0 is declared plugin-side but is not realistically testable against a
current WooCommerce. The ceiling is the other end of the support matrix:
WordPress 7.1.3 (the newest 7.1.x, the plugin's `Tested up to`) and
WooCommerce 11.2.1 (the newest 11.2.x, its `WC tested up to`), on PHP 8.4
(S-083, D-092). Both sites set `AUTOMATIC_UPDATER_DISABLED` and
`WP_AUTO_UPDATE_CORE=false`: without that a pinned site updated itself within
minutes (L-008).

**How the code gets there.** The repository is the read-only Parallels share
`wrgl`, bind-mounted at `wp-content/plugins/woo-redsys-gateway-light` of each
site by the VM's `keel-mounts` service (it survives reboots). An edit on the
host is live on both sites at once; nothing is copied. `vendor/` is installed
on the host (`composer install`) and the VM reads it through the share.

**Dev-only must-use plugins.** `.wp-env-mu-plugins/` keeps its historical
name because the records cite it. Each site's `mu-plugins` holds only a
loader, `keel-repo-mu-plugins.php`, that requires the repository's
`.wp-env-mu-plugins/*.php`: the Inespay HTTP stub always, and the tunnel's
HTTPS shim while a tunnel is up. The directory is `export-ignore`: it never
ships.

## Prerequisites

- Parallels Desktop Pro or Business (`prlctl` must answer) and the VM
  `keel-development`. Keel never installs Parallels.
- Node.js and npm (for Playwright), and Composer on the host (for `vendor/`).
- Run `scripts/keel-doctor --check` first. Its rows: Parallels (`prlctl`),
  the `keel-development` VM, the playground sites (`wrgl`, `wrgl-ceiling`:
  HTTP 200, plugin mounted, premium plugin absent), npx, PHPUnit in `vendor/`,
  `@playwright/test`, `axe-core`, Playwright Chromium, and the optional ones.

## Start it

```
composer install          # on the host, once and after composer.lock changes
scripts/keel-site start   # starts the VM if it is not running, re-runs keel-mounts, prints the URL
scripts/keel-site doctor  # both sites: HTTP code, mount, premium plugin, WordPress/WooCommerce versions
```

`scripts/keel-site stop` changes nothing on purpose: the VM is shared with
other projects and is never suspended from here.

## What you get

- **Site URL:** `scripts/keel-site url` (floor: `http://10.211.55.9:8101`,
  ceiling: `http://10.211.55.9:8102`)
- **Admin URL:** the site URL + `/wp-admin`
- **Admin username / password:** `admin` / `password`

These are throwaway, local-only credentials on a synthetic site with no real
data; never reuse them anywhere real. While a tunnel is open (below) they also
protect a public URL, which is why only test keys ever go in front of it.

Other commands, all non-interactive:

```
scripts/keel-site wp <args>       # WP-CLI on the site, as the site's user, with the site's PHP
scripts/keel-site sql <query>     # MariaDB on the site's database
scripts/keel-site list            # every site on the VM: versions, port, disk, last use
```

## Seed and reset

```
scripts/keel-site reset                       # floor
scripts/keel-site --site wrgl-ceiling reset   # ceiling
```

`reset` checks the premium-plugin rule, rewrites the mu-plugin loader and runs
the seed, `scripts/playground-setup`, on that site only. The seed is
idempotent and does everything under "One-time environment setup" below:
activates WooCommerce and this plugin, sets pretty permalinks, creates the
Test Product (it must be post 10), the Transaction Limit product and the
Blocks checkout page, sets the classic checkout page, opens the store to
guests, configures the four gateways in test mode and turns the Inespay HTTP
stub on. It stops with an error if `vendor/bin/phpunit` is missing.

## Rebuild a site from nothing

```
scripts/keel-site --site <name> retire   # vhost, PHP-FPM pool, mount, databases and their user, Unix user
scripts/keel-site --site <name> create   # rebuilds it, seeds it and warms the tests database
```

`create` builds the vhost, the PHP-FPM pool running as the site's own user,
the site database and a separate tests database, downloads the pinned
WordPress and WooCommerce, writes the mu-plugin loader, checks the bind mount,
installs the WordPress test library of the same version as the site's
`wp-tests` directory, runs `reset`, and runs the integration suite once
because the first run on an empty tests database is not representative. If the
share is not visible in the guest it stops and says how to add it; it never
restarts `prltoolsd` in place, which would unmount every other tenant.

## Running the tests

Unit and integration suites run on the VM as the site's Unix user, with the
site's PHP, `--do-not-cache-result` (the source is read-only there), and
`WP_TESTS_DIR` set to the site's own `wp-tests` directory, on the tests
database. The browser suite runs on the host, headless, against
`scripts/keel-site url` (`playwright.config.js` computes `baseURL`;
`PLAYGROUND_URL` overrides it).

Floor:

```
scripts/keel-site test unit
scripts/keel-site test integration
scripts/keel-site test integration tests/Integration/RefundAsBuiltTest.php   # one or more files
npx playwright test
```

Ceiling — the same commands with the site selected; `KEEL_SITE` reaches
`playwright.config.js` and the specs that prepare data through WP-CLI
(`scripts/keel-site --site $KEEL_SITE wp ...`, which replaced `WP_ENV_CONFIG`):

```
KEEL_SITE=wrgl-ceiling scripts/keel-site test unit
KEEL_SITE=wrgl-ceiling scripts/keel-site test integration
KEEL_SITE=wrgl-ceiling npx playwright test
```

`scripts/keel-affected-tests --run` (the pre-push hook) and
`scripts/keel-affected-tests --full --run` (the release gate) run unit and
integration through `scripts/keel-site test`, and the browser suite with
Playwright on the host, against the floor unless `KEEL_SITE` says otherwise.
Before running, they check `prlctl` and `scripts/keel-site doctor` and exit
non-zero naming the fix. The ceiling is run by hand at the release gate.

## Reading the logs

Both sites set `WP_DEBUG` and `WP_DEBUG_LOG` (with `WP_DEBUG_DISPLAY` off).
Each site has two logs on the VM:

- WordPress: `/srv/keel/sites/<site>/public/wp-content/debug.log`
- PHP (the site's PHP-FPM pool): `/srv/keel/sites/<site>/php-error.log`

```
prlctl exec keel-development tail -n 100 /srv/keel/sites/wrgl/public/wp-content/debug.log
prlctl exec keel-development tail -n 100 /srv/keel/sites/wrgl/php-error.log
```

(Replace `wrgl` with `wrgl-ceiling` for the ceiling.) A flow that "looks fine"
while the debug log gained a notice or a fatal has not actually passed —
always check this after driving a flow, not only when something visibly
breaks.

PHP lint at the floor's PHP: `prlctl exec keel-development php7.4 -l
/srv/keel/sites/wrgl/public/wp-content/plugins/woo-redsys-gateway-light/<file>`,
or `php -l <file>` on the host for the host's PHP.

## Try it yourself — step by step

Everything below except the marked `⚠ unverified — CREDENTIAL` step can be
walked with zero external accounts: it is all local, synthetic, and
resettable with `scripts/keel-site reset`.

1. **Start the environment:** `scripts/keel-site start`, then open the URL it
   prints + `/wp-admin` and log in with `admin` / `password`.
2. **Confirm WooCommerce and the plugin are active:** Plugins → Installed
   Plugins should show "WooCommerce" and "Payment Gateway for Redsys &
   WooCommerce Lite" both active (the seed activates both — if either shows
   inactive, run `scripts/keel-site reset`). "WooCommerce Redsys Gateway"
   (the premium plugin) must **not** be listed at all.
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
   listed among the payment options.
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
     curl -i -X POST "$(scripts/keel-site url)/?wc-api=WC_Gateway_redsys" \
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
merchant contract. **Real run, 2026-08-01 (wp-env): 1 passed.** Mutation-tested for
real: temporarily corrupted the merchant code sent in the form (`DS_MERCHANT_MERCHANTCODE`),
confirmed the test failed on the expected assertion, reverted, confirmed it
passed again.

### One-time environment setup (done by `scripts/playground-setup`)

The commands below are what the seed runs (through `scripts/keel-site wp`, on
the site `KEEL_SITE` names); they are kept here as the explanation of each
step, not as something to type. `scripts/keel-site reset` runs the seed.

A fresh site starts with plain (`?p=123`) permalinks and no payment gateway
configured, neither of which the classic WooCommerce checkout page needs to
render correctly for a human clicking through the admin — but the e2e test
visits pretty URLs (`/checkout/`) and needs a gateway to select:

```
scripts/keel-site wp rewrite structure '/%postname%/'
scripts/keel-site wp rewrite flush --hard
scripts/keel-site wp option update woocommerce_redsys_settings --format=json \
  '{"enabled":"yes","title":"Redsys","description":"Pay with card via Redsys","customer":"999008881","commercename":"Test Shop","payoptions":"T","terminal":"1","not_use_https":"no","lwvactive":"no","orderdo":"processing","secretsha256":"sq7HjrUOBfKmC576ILgskD5srU870gJ7","customtestsha256":"","redsyslanguage":"002","testmode":"yes","debug":"no"}'
scripts/keel-site wp option update woocommerce_bizumredsys_settings --format=json \
  '{"enabled":"yes","title":"Bizum","description":"Pay via Bizum you can pay with your Bizum account.","customer":"999008881","commercename":"Test Shop","terminal":"1","orderdo":"processing","not_use_https":"no","secretsha256":"sq7HjrUOBfKmC576ILgskD5srU870gJ7","customtestsha256":"","redsyslanguage":"002","testmode":"yes","debug":"no"}'
scripts/keel-site wp option update woocommerce_googlepayredirecredsys_settings --format=json \
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

All gateways are enabled simultaneously in this playground, so none of
them is WooCommerce's auto-selected/hidden "only one gateway" case — every
e2e spec explicitly selects its own gateway's radio button.

The checkout specs add post ID 10 ("Test Product") to the cart. `create`
removes the navigation post WordPress 7 adds on install, so on a fresh site
IDs 1 to 9 are the defaults and the seed creates that product first; it stops
with an error if the product did not get ID 10 — which happens when something
else was created before it. Rebuild the site then (`retire`, then `create`).

The seed also replaces the checkout page's content with the classic
`[woocommerce_checkout]` shortcode and sets `woocommerce_coming_soon` to `no`:
a current WooCommerce (the ceiling) creates the checkout page with the
Checkout block and starts the store in "coming soon" mode. Neither changes
anything on WooCommerce 7.4.

#### WooCommerce Blocks checkout page (for `checkout-blocks-redsys.spec.js`)

A second page using the WooCommerce Blocks `Checkout` block is needed for the
Blocks-checkout e2e test; the seed creates it (or rewrites its content):

```
scripts/keel-site wp post create --post_type=page \
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
in the browser. `.wp-env-mu-plugins/inespay-http-stub.php` — loaded by the
site's mu-plugin loader (`keel-repo-mu-plugins.php`), dev/test infra only,
never shipped in the plugin — fakes that API response, but only when
explicitly turned on:

```
scripts/keel-site wp option update redsyslite_e2e_stub_inespay yes
scripts/keel-site wp option update woocommerce_inespayredsys_settings --format=json \
  '{"enabled":"yes","title":"Inespay Bank Transfer","description":"Pay via instant bank transfer.","api_key":"e2e-test-api-key","api_token":"e2e-test-api-token","testmode":"yes","transactionlimit":"200","debug":"no"}'
scripts/keel-site wp wc product create --user=1 --name="E2E Transaction Limit Product" --type=simple --regular_price=200.50 --porcelain
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

#### Mutation-testing gotcha: opcache can serve the old bytecode

On wp-env, a reverted PHP mutation kept failing until the `wordpress`
container was restarted: opcache had cached the corrupted bytecode across a
same-second file edit (L-005). If a "should now be green" run still shows the
old behaviour while the file on disk is confirmed reverted, don't re-suspect
the revert — suspect the site's PHP-FPM opcache. On `keel-development` the
equivalent is reloading the site's PHP-FPM service
(`prlctl exec keel-development systemctl reload php7.4-fpm` for the floor,
`php8.4-fpm` for the ceiling) — `⚠ unverified since D-114`: not reproduced
on the VM yet. PHPUnit is not affected: it runs as a fresh CLI process every
time.

### Running it

```
npm install                                   # once, installs @playwright/test
npx playwright install --with-deps chromium   # once, downloads the browser
scripts/keel-site start                       # if the VM is not running
npx playwright test                           # or: npm run test:e2e
```

## Inbound tunnel (Redsys/Inespay notifications reaching the playground)

> `⚠ unverified since D-114`: the script was adapted to the VM, but no
> tunnel has been opened since the move — the session that migrated the
> playground was not permitted to open a public tunnel. Treat everything in
> this section as the script's design, not as a driven result.

The playground lives on the VM's private address, so Redsys and Inespay
cannot send their server-to-server notification to it. Without a tunnel, a
sandbox payment stops at "pending". `scripts/playground-tunnel` opens a public
HTTPS URL that forwards to the playground for as long as the exercise lasts.
The inventory of the four notification endpoints is in
`docs/03-technical-plan.md` → "Inbound calls". It needs `cloudflared`
(`brew install cloudflared`), which runs on the **host** and forwards to the
site's VM address. `scripts/keel-doctor` reports it as an optional row.
`KEEL_SITE` picks the site (default `wrgl`).

```
scripts/playground-tunnel                 # open a Cloudflare quick tunnel, point WordPress at it, wait
scripts/playground-tunnel --url <https>   # point WordPress at a tunnel you already opened
scripts/playground-tunnel --restore       # back to the site's own URL (scripts/keel-site url); safe to repeat
```

**What it changes while it is open:**

- `WP_HOME` and `WP_SITEURL` in the site's `wp-config.php` are set to the
  tunnel URL (through `scripts/keel-site wp config set`). Every gateway builds
  its notification URL from that address, so `DS_MERCHANT_MERCHANTURL`
  (Redsys, Bizum, Google Pay) and Inespay's `notifUrl` now carry the tunnel
  hostname. The script prints the four endpoints.
- A playground-only must-use plugin,
  `.wp-env-mu-plugins/keel-playground-tunnel-https.php`, is written into the
  repository (the site's mu-plugin loader picks it up) and tells WordPress
  the request is HTTPS when the tunnel says so (`X-Forwarded-Proto`). It
  never ships: `.wp-env-mu-plugins/` is `export-ignore`.
- While the tunnel is open, the site (including `/wp-admin`) is reachable
  through it by anyone who has the URL, with the `admin` / `password`
  credentials above. That is why the script refuses to start while any
  enabled gateway is out of test mode. Only test keys go in front of it,
  such as the public Redsys test key `scripts/playground-setup` writes. Don't
  paste the URL into commits, issues or public notes.
- Leave the Redsys gateways' "not use https" option off. It rewrites the
  notification URL to `http:`.

**How it is closed:** press Ctrl-C in the terminal running it, or run
`kill -TERM <pid>` (the PID is printed). From a shell that started with
SIGINT ignored (a background job, an assistant's tool shell), only TERM
reaches the trap. Every exit path closes the tunnel, deletes the HTTPS
plugin and puts `WP_HOME`/`WP_SITEURL` back on the site's own URL. That
includes the tunnel dying on its own, the URL never appearing (it gives up
after `PLAYGROUND_TUNNEL_TIMEOUT` seconds, default 30, and prints
cloudflared's last lines), and a failed switch. After a `kill -9`, or after
`--url`, run `scripts/playground-tunnel --restore`. Never leave a tunnel
open between sessions. A playground still pointing at a dead hostname
looks like a product bug.

Exit codes: 0 done, 2 could not run. It exits 2 without changing anything
in these cases: `scripts/keel-site` gives no address for the site, nothing
answers on it, WP-CLI does not answer on it, `cloudflared` is missing, a
gateway is out of test mode, or `~/.cloudflared/config.yml` exists. That last
file is yours and the script never edits it. Open your own tunnel and use
`--url`.

**Last real run: none yet.** The script's exit paths were exercised against
stand-ins for wp-env and `cloudflared` before the move (S-087). No public
tunnel to the playground has been opened, on wp-env or on the VM. The one
provider acceptance check, a Redsys test-terminal payment whose notification
URL is on the tunnel, is deferred to S-037 by the owner (D-093). Until it
runs, it is unknown whether Redsys accepts a `trycloudflare.com` notification
URL. If it does not, the next step is a named tunnel, not a deployment.

## History

- Until 2026-11-07 the playground was `@wordpress/env` (wp-env) on Docker:
  `.wp-env.json` (floor, ports 8888/8889) and `.wp-env.ceiling.json`
  (ceiling, ports 8890/8891). Last verified there on 2026-10-06, rebuilt from
  nothing on `@wordpress/env` 11.16.0 (D-051, L-008). D-114 replaced it with
  the two sites above.

## What this playground does NOT verify

- A real Redsys/Bizum/Apple-Google Pay/Inespay round trip against Redsys's
  live test environment (`CREDENTIAL`, step 7 above).
- Anything requiring HTTPS-only behaviour that Redsys's real endpoints may
  enforce — this local environment is plain HTTP (the inbound tunnel above
  would serve it over HTTPS, but no tunnel has been opened yet: S-037, D-093,
  and `⚠ unverified since D-114`).
- Email deliverability of WooCommerce order emails (no SMTP is configured on
  the sites; add a mail-catching plugin if that becomes relevant to a future
  slice).
- Anything about the premium plugin `woocommerce-gateway-redsys`: by rule it
  is never on these sites (D-114).
