#!/usr/bin/env bash
set -euo pipefail
cd /opt/tms-edc
python3 -m py_compile demo/workbench.py
# This read API retains existing TTC machine authentication and contract-party checks.
docker cp middleware/source/integration/index.php ttc-demo-php:/var/www/html/integration/index.php
docker exec ttc-demo-php chmod 644 /var/www/html/integration/index.php
docker exec ttc-demo-php php -l /var/www/html/integration/index.php
install -m 644 deploy/tms-role-workbench.service /etc/systemd/system/tms-role-workbench.service
systemctl daemon-reload
systemctl enable --now tms-role-workbench
printf 'Role workbench service launched on loopback port 8082\n'
