#!/usr/bin/env bash
# Run as root on the existing isolated demo VM, after copying release.tar.gz.
set -euo pipefail
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
apt-get install -y -qq docker.io openjdk-17-jre-headless python3 openssl curl > /var/log/tms-edc-install.log 2>&1
systemctl enable --now docker
cd /opt/tms-edc
# Source credentials remain in this private deployment directory.
chmod 700 secrets
docker pull mirror.gcr.io/library/mysql:8.0
docker tag mirror.gcr.io/library/mysql:8.0 mysql:8.0
docker build -t ttc-middleware:demo middleware
bash scripts/cloud-middleware-start.sh
# Import only synthetic demo organizations and the approved contract fixture.
docker cp middleware/seed-test.php ttc-demo-php:/tmp/seed-test.php
docker cp secrets/platform/provider_public.pem ttc-demo-php:/tmp/provider.pub
docker cp secrets/platform/consumer_public.pem ttc-demo-php:/tmp/consumer.pub
docker exec ttc-demo-php php /tmp/seed-test.php
id tmsedc >/dev/null 2>&1 || useradd --system --home /opt/tms-edc --shell /usr/sbin/nologin tmsedc
chown -R root:tmsedc config secrets
find config -type d -exec chmod 750 {} +
find config -type f -exec chmod 640 {} +
find secrets -type d -exec chmod 750 {} +
find secrets -type f -exec chmod 640 {} +
# Only these explicitly mounted files must be readable by Apache inside its container.
chmod 644 secrets/platform/server_private.key secrets/platform/server_public.pem
mkdir -p logs
chown -R tmsedc:tmsedc logs
for role in provider consumer dataplane; do
 module=control-plane
 [[ $role == dataplane ]] && module=data-plane
 cat >"/etc/systemd/system/tms-edc-$role.service" <<EOF
[Unit]
Description=Native Eclipse EDC TMS $role demo
After=network-online.target docker.service
Wants=network-online.target
[Service]
User=tmsedc
Group=tmsedc
WorkingDirectory=/opt/tms-edc
ExecStart=/usr/bin/java -Xms64m -Xmx384m -Dedc.fs.config=config/$role.properties -jar $module/target/$module-0.1.0.jar
Restart=on-failure
RestartSec=5
NoNewPrivileges=true
PrivateTmp=true
ProtectHome=true
ProtectSystem=strict
ReadWritePaths=/opt/tms-edc/logs
[Install]
WantedBy=multi-user.target
EOF
done
systemctl daemon-reload
systemctl enable --now tms-edc-provider tms-edc-consumer
sleep 15
systemctl enable --now tms-edc-dataplane
# Bound the demo's unattended runtime after Friday morning's presentation.
cat >/etc/systemd/system/tms-demo-stop.service <<'EOF'
[Unit]
Description=Stop temporary Data Spaces demo VM
[Service]
Type=oneshot
ExecStart=/usr/sbin/shutdown -h now
EOF
cat >/etc/systemd/system/tms-demo-stop.timer <<'EOF'
[Unit]
Description=Stop demo VM after Friday presentation
[Timer]
OnCalendar=2026-11-20 05:00:00 UTC
Persistent=true
[Install]
WantedBy=timers.target
EOF
systemctl daemon-reload
systemctl enable --now tms-demo-stop.timer
chmod 644 scripts/demo_status.py
cp deploy/tms-demo-status.service /etc/systemd/system/
systemctl daemon-reload
systemctl enable --now tms-demo-status
printf 'Native EDC and TTC demo services installed\n'
