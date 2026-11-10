#!/usr/bin/env bash
# Runs as root inside keel-development. Idempotent: safe to re-run.
set -euo pipefail
export DEBIAN_FRONTEND=noninteractive
PHPS="7.4 8.1 8.3 8.4"
EXT="cli fpm mysql mbstring xml curl gd zip intl bcmath soap redis xdebug opcache igbinary"

step() { echo "== $*"; }

step base packages
apt-get update -qq
apt-get install -y -qq ca-certificates curl unzip zip rsync git subversion gnupg software-properties-common jq acl

step php repository
if ! ls /etc/apt/sources.list.d | grep -q ondrej; then
  add-apt-repository -y ppa:ondrej/php
  apt-get update -qq
fi

step web, database, cache
apt-get install -y -qq apache2 mariadb-server redis-server
a2enmod -q proxy_fcgi setenvif rewrite headers expires
a2dissite -q 000-default || true

step php versions: $PHPS
pkgs=""
for v in $PHPS; do for e in $EXT; do pkgs="$pkgs php$v-$e"; done; done
apt-get install -y -qq $pkgs
for v in $PHPS; do
  phpdismod -v "$v" xdebug
  systemctl enable --now "php$v-fpm" >/dev/null
done

step tools
apt-get install -y -qq composer nodejs npm
if [ ! -x /usr/local/bin/wp ]; then
  curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
  chmod +x /usr/local/bin/wp
fi

step mailpit
if ! command -v mailpit >/dev/null; then
  tmp=$(mktemp -d)
  curl -fsSL -o "$tmp/m.tgz" https://github.com/axllent/mailpit/releases/latest/download/mailpit-linux-arm64.tar.gz
  tar -xzf "$tmp/m.tgz" -C "$tmp" mailpit
  install -m 755 "$tmp/mailpit" /usr/local/bin/mailpit
  rm -rf "$tmp"
fi
cat > /etc/systemd/system/mailpit.service <<'U'
[Unit]
Description=Mailpit
After=network.target
[Service]
ExecStart=/usr/local/bin/mailpit --listen 0.0.0.0:8025 --smtp 127.0.0.1:1025
Restart=on-failure
DynamicUser=yes
[Install]
WantedBy=multi-user.target
U
systemctl daemon-reload
systemctl enable --now mailpit >/dev/null

step cloudflared
if ! command -v cloudflared >/dev/null; then
  curl -fsSL -o /tmp/cloudflared.deb https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-arm64.deb
  dpkg -i /tmp/cloudflared.deb >/dev/null
  rm -f /tmp/cloudflared.deb
fi

step php-fpm mail and limits
for v in $PHPS; do
  for sapi in fpm cli; do
    cat > "/etc/php/$v/$sapi/conf.d/90-keel.ini" <<'I'
memory_limit = 512M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
sendmail_path = "/usr/local/bin/mailpit sendmail -S 127.0.0.1:1025"
I
  done
done

mkdir -p /srv/keel/sites /srv/keel/wp-tests
systemctl restart apache2 mariadb redis-server

step versions
for v in $PHPS; do "php$v" -r 'echo PHP_VERSION, " ", PHP_OS, " ", php_uname("m"), PHP_EOL;'; done
apache2 -v | head -1; mariadb --version; redis-server --version | cut -c1-40; wp --allow-root --version; node --version; composer --version 2>/dev/null | head -1; mailpit version | head -1; cloudflared --version
echo PROVISIONED
