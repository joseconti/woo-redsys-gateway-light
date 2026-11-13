#!/usr/bin/env bash
# Runs as root inside keel-development. The local CA and the *.keel.test wildcard used by
# connected sites (recipe step 4). Idempotent: an existing CA is kept, so certificates stay trusted.
set -euo pipefail
a2enmod -q ssl
C=/srv/keel/ca; mkdir -p "$C"; cd "$C"
[ -f ca.key ] || { openssl genrsa -out ca.key 2048 2>/dev/null; openssl req -x509 -new -key ca.key -days 3650 -subj "/CN=Keel Development CA" -out ca.crt; }
if [ ! -f w.crt ]; then
  openssl genrsa -out w.key 2048 2>/dev/null
  openssl req -new -key w.key -subj "/CN=*.keel.test" -out w.csr
  printf 'subjectAltName=DNS:*.keel.test,DNS:keel.test\n' > ext
  openssl x509 -req -in w.csr -CA ca.crt -CAkey ca.key -CAcreateserial -days 825 -extfile ext -out w.crt 2>/dev/null
fi
chmod 644 w.crt ca.crt; chmod 640 w.key; chgrp www-data w.key
cp ca.crt /usr/local/share/ca-certificates/keel-development.crt; update-ca-certificates >/dev/null
systemctl reload apache2
echo CA-READY
