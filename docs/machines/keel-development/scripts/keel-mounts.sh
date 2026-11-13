cat > /usr/local/sbin/keel-mounts <<'S'
#!/usr/bin/env bash
# Bind every site's source read-only into its plugin directory. Waits for the Parallels share.
for e in /srv/keel/sites/*/site.env; do
  [ -e "$e" ] || continue; unset slug share; . "$e"; [ -n "${share:-}" ] || continue
  t="$(dirname "$e")/public/wp-content/plugins/$slug"; s="/media/psf/$share"
  for _ in $(seq 60); do [ -n "$(ls -A "$s" 2>/dev/null)" ] && break; sleep 2; done
  mountpoint -q "$t" || mount --bind -o ro "$s" "$t"
done
S
chmod 755 /usr/local/sbin/keel-mounts
cat > /etc/systemd/system/keel-mounts.service <<'U'
[Unit]
Description=Keel site source mounts
After=prltoolsd.service
[Service]
Type=oneshot
RemainAfterExit=yes
ExecStart=/usr/local/sbin/keel-mounts
[Install]
WantedBy=multi-user.target
U
systemctl daemon-reload; systemctl enable keel-mounts -q
