# Machine recipe — woo-redsys-gateway-light

> Machines: two tenants, `wrgl` and `wrgl-ceiling`, on the shared Parallels VM `keel-development`.
> Host: parallels-shared (D-114).
> Base: `docs/machines/keel-development/`, revision **keel-base-rev=2026-11-10-1**.
> Last updated: 2026-11-10 — written from the running VM and `scripts/keel-site`.
> Last rebuilt from this recipe: **never — unproven** (the sites were last rebuilt with
> `scripts/keel-site create` on 2026-11-07, S-104; the VM itself has not been rebuilt from `keel-development/`).

## 1. Host prerequisites

Everything in `docs/machines/keel-development/recipe.md` §1, plus: Python 3 (for `scripts/keel-site`), Composer
(`vendor/` is installed on the host and read by the VM through the share), Node/npm for the browser suite.

## 2. Base

The shared VM. If it does not exist, rebuild it first: `docs/machines/keel-development/recipe.md` §9.

## 3–5. Packages, configuration, isolation

Nothing beyond the base. Each site is an ordinary hosting tenant created by `scripts/keel-site create`:
Unix user `ks-<site>`, PHP-FPM pool, `open_basedir`, database and user, WordPress test library and
tests database, `AUTOMATIC_UPDATER_DISABLED` and `WP_AUTO_UPDATE_CORE=false` (L-008).

| Site | Role | PHP | WordPress | WooCommerce | Port |
|---|---|---|---|---|---|
| `wrgl` | floor (default) | 7.4 | 7.0 | 7.4.0 | 8101 |
| `wrgl-ceiling` | ceiling | 8.4 | 7.1.3 | 11.2.1 | 8102 |

- Source: the repository as the read-only Parallels share `wrgl` (`prlctl set keel-development
  --shf-host-add wrgl --path <repo> --mode ro`, done by `create`), bind-mounted at
  `wp-content/plugins/woo-redsys-gateway-light` by the VM's `keel-mounts` service.
- Dev-only must-use plugins: a one-file loader `keel-repo-mu-plugins.php` in each site's `mu-plugins`
  requires the repository's `.wp-env-mu-plugins/*.php`.
- Rule: the premium plugin `woocommerce-gateway-redsys` is never installed on these sites (D-114);
  `scripts/keel-site` refuses to work on a site where it is.

## 7. Network

`http://10.211.55.9:8101` and `:8102` (`scripts/keel-site url`). Inbound calls through
`scripts/playground-tunnel` (cloudflared in the guest).

## 8. Data

Synthetic seed only, by `scripts/playground-setup` (run by `create` and `reset`). Database credentials
are generated at creation and never written to the repository.

## 9. Rebuild procedure

```sh
composer install                                   # on the host
scripts/keel-site --site wrgl create               # share, tenant, WordPress, WooCommerce, seed, tests DB
scripts/keel-site --site wrgl-ceiling create
```

## 10. Verification

```sh
scripts/keel-doctor --check
scripts/keel-site doctor                           # HTTP, mount, premium plugin absent, versions
scripts/keel-site test unit && scripts/keel-site test integration
KEEL_SITE=wrgl-ceiling scripts/keel-site test unit
```

Expected: the results recorded in `docs/playground.md` ("last verified").
