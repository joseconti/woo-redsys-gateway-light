# Machine recipe — `keel-development` (the shared Parallels VM)

> Base revision: **keel-base-rev=2026-11-10-1** (the VM's own description carries the same value).
> Last updated: 2026-11-10 — written from the running VM (`prlctl list -i`, package list, apt history,
> `/srv/keel/sites/*/site.env`, Apache vhosts, systemd units), not from memory.
> Last rebuilt from this recipe: **never — unproven**. The scripts are the ones that built the VM on
> 2026-10-31, updated with what was installed on it since; no rebuild has followed them yet.

This file is the VM's DOCUMENTS, never the VM. There is one `keel-development` on the machine, with one
disk, in `~/Parallels/keel-development.pvm`. Every repository that has a site on it carries an identical
copy of this directory, so any of them can recreate the VM from a fresh clone. No disk image, snapshot
or `.pvm` is ever committed. The project's own site is described one level up, in `../recipe.md`.

A session that changes this VM raises the revision (here and in the VM description), updates this copy
in the repository it is working in, and the other copies are refreshed from the running VM at their
projects' next session start.

## 1. Host prerequisites

| What | Version used | How |
|---|---|---|
| macOS on Apple silicon (arm64) | — | the VM is arm64 |
| Parallels Desktop Pro or Business (`prlctl` must answer `prlctl list -a`) | 27.1.0 | bought and installed by the user; Keel never installs it |
| SSH key `~/.ssh/keel_development` | ed25519 | created by `docs/machines/keel-development/scripts/create-vm` if missing; never committed |
| Ubuntu Server 24.04.5 live ISO, arm64 | SHA-256 checksum `f12484464c7d4d73e2167e0a7dbda1e669f8fae9bcf778dc0944937034b320cb` | downloaded by `docs/machines/keel-development/scripts/create-vm` into `~/Parallels/iso/` (persistent) unless `KEEL_ISO` names a copy |

## 2. Base

- Ubuntu 24.04.5 LTS, aarch64, hostname `keel-development`, user `keel` (key login only, passwordless sudo).
- 4 vCPU, 8192 MB RAM, expanding disk with a 120 GB maximum (122880 MB), shared network (`10.211.55.9`).
- Lease: `keel-lease: owner=shared-plugin-host until=never`.

## 3. Packages (installed by `docs/machines/keel-development/scripts/provision-guest.sh` unless noted)

- Base: ca-certificates curl unzip zip rsync git subversion gnupg software-properties-common jq acl.
- Apache 2.4.58 with `proxy_fcgi setenvif rewrite headers expires ssl`, default site disabled.
- PHP from `ppa:ondrej/php`: **7.4, 8.1, 8.3, 8.4**, each with cli fpm mysql mbstring xml curl gd zip intl
  bcmath soap redis xdebug opcache igbinary; Xdebug disabled by default.
- MariaDB 10.11 (distribution), Redis 7.0, Composer 2.7, Node 18 + npm (distribution), WP-CLI (phar,
  `/usr/local/bin/wp`), Mailpit (`/usr/local/bin/mailpit`, systemd `mailpit`, UI :8025, SMTP 127.0.0.1:1025),
  cloudflared (`.deb`, latest).
- Parallels Tools prerequisites: build-essential, `linux-headers-$(uname -r)`, dkms (step 2 of the procedure).

## 4. Configuration

- `/etc/php/<v>/{fpm,cli}/conf.d/90-keel.ini` for every PHP version: `memory_limit 512M`, uploads 64M,
  `max_execution_time 300`, `sendmail_path` to Mailpit (written by `provision-guest.sh`).
- Local CA and `*.keel.test` wildcard in `/srv/keel/ca`, trusted system-wide (`docs/machines/keel-development/scripts/local-ca.sh`).
- `/etc/hosts`: `127.0.0.1 api.keel.test licenses.keel.test client.keel.test` (connected-sites test names).
- `keel-mounts` systemd oneshot after `prltoolsd`, restoring every site's read-only bind mount at boot
  (`docs/machines/keel-development/scripts/keel-mounts.sh`). It reads `share=` and `slug=` from each `site.env`; PrestaShop sites use
  `psrg_share=` and `module_target=` and are mounted by that project's own `scripts/keel-site`.
- Directories: `/srv/keel/sites/<site>/` (one per tenant), `/srv/keel/wp-tests`, `/srv/keel/cache`.

## 5. Isolation and sharing

- Each tenant: its own Unix user `ks-<site>`, PHP-FPM pool on `/run/php/ks-<site>.sock`, `open_basedir`,
  database and database user, Apache vhost on its own port. Created by each project's `scripts/keel-site create`.
- Parallels default sharing OFF: `prlctl set keel-development --shared-cloud off --smart-mount off --shf-host-defined off`,
  then `sudo systemctl restart prltoolsd` in the guest; `ls /media/psf` must list only the project shares.
- Project shares (read-only, one repository each): `aswc`, `mcm`, `psrg`, `redsys`, `wrgl`.

## 6. Tenants (2026-11-10) and what each added to the VM

| Site(s) | Port(s) | PHP | Owner repository | Share |
|---|---|---|---|---|
| `wrgl`, `wrgl-ceiling` | 8101, 8102 | 7.4, 8.4 | woo-redsys-gateway-light | `wrgl` |
| `redsys` | 8103 | 8.3 | woocommerce-gateway-redsys | `redsys` |
| `aswc` | 8104 | 7.4 | advanced-subscriptions-for-woocommerce | `aswc` |
| `psrg17`, `psrg82`, `psrg90`, `psrg91`, `psrg92` | 8111–8115 | 7.4, 8.1, 8.4, 8.4, 8.4 | ps_redsys_gateway | `psrg` |
| `mcm` | 8892 | 8.3 | mcp-content-manager-for-wordpress | `mcm` |
| DeCA MariaDB 10.6.28 tenant `ks_deca` | 127.0.0.1:3316 | — | deca-pretiumgestion-com | — |
| PostgreSQL 16 database `keel_fs_synthetic_pg` | 5432 | 8.4 (`php8.4-pgsql`) | facturascripts_mcp (inferred from the name) | — |

Additions to the base made for one tenant, which that tenant's own recipe must describe in full:

- `/opt/mariadb-10.6.28` and systemd unit `deca-mariadb106` (MariaDB 10.6 for DeCA, beside the system 10.11).
- `postgresql` (16.15) and `php8.4-pgsql`, installed 2026-11-08 and 2026-11-10.
- Chromium runtime libraries (`libasound2t64 libatk-bridge2.0-0t64 … libnss3`), installed 2026-11-10 16:43; owner not confirmed.
- `/srv/keel/cache/prestashop` (PrestaShop downloads, ps_redsys_gateway).

## 7. Network

- Shared network; the VM is `10.211.55.9`. macOS Local Network privacy blocks non-Apple binaries from that
  address, so projects reach their site through an SSH forward on `127.0.0.1:<port>` (`/usr/bin/ssh` is allowed).
- Inbound tunnels: `cloudflared` in the guest, opened per project by its `scripts/playground-tunnel`.

## 8. Data

Nothing on this VM is irreplaceable: every site is rebuilt from its repository and its seed. Database
credentials are generated per site at creation and live only in the site's `wp-config.php` / `site.env`;
they are never written here.

## 9. Rebuild procedure (from an empty host)

Run from the root of any repository that carries this directory:

```sh
docs/machines/keel-development/scripts/create-vm                                   # 1. unattended install; prints READY <ip>
docs/machines/keel-development/scripts/vm 'sudo apt-get install -y -qq build-essential linux-headers-$(uname -r) dkms'
prlctl installtools keel-development                # 2. Parallels Tools (then run the installer from the mounted tools ISO in the guest, unattended)
prlctl set keel-development --shared-cloud off --smart-mount off --shf-host-defined off
docs/machines/keel-development/scripts/vm 'sudo systemctl restart prltoolsd'       # 3. close default sharing
docs/machines/keel-development/scripts/vm 'sudo bash -s' < docs/machines/keel-development/scripts/provision-guest.sh   # 4. packages and configuration; ends in PROVISIONED
docs/machines/keel-development/scripts/vm 'sudo bash -s' < docs/machines/keel-development/scripts/local-ca.sh          # 5. CA and wildcard; ends in CA-READY
docs/machines/keel-development/scripts/vm 'sudo bash -s' < docs/machines/keel-development/scripts/keel-mounts.sh       # 6. the boot-time mount service
docs/machines/keel-development/scripts/vm "echo '127.0.0.1 api.keel.test licenses.keel.test client.keel.test' | sudo tee -a /etc/hosts"
prlctl set keel-development --description "keel-lease: owner=shared-plugin-host until=never keel-base-rev=2026-11-10-1"
prlctl snapshot keel-development --name baseline         # 7. snapshot of the empty, provisioned VM
```

Then each project recreates its own tenant from its repository (`../recipe.md` there): its share, its
`scripts/keel-site create`, its seed. The tenant-specific additions of section 6 are part of those recipes.

## 10. Verification

- `provision-guest.sh` ends by printing every version above; compare with sections 3 and 6.
- `docs/machines/keel-development/scripts/vm 'ls /media/psf'` lists only project shares; `systemctl is-enabled keel-mounts mailpit` → enabled.
- For each project: its `scripts/keel-doctor --check` and `scripts/keel-site doctor` pass, and its gate zero runs.
