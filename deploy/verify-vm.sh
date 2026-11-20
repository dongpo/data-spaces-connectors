#!/usr/bin/env bash
set -euo pipefail
cd /opt/tms-edc
chmod 644 secrets/platform/server_private.key secrets/platform/server_public.pem
find config -type d -exec chmod 750 {} +
find config -type f -exec chmod 640 {} +
for port in 19191 29191 19181; do
 ready=false
 for i in $(seq 1 45); do
  if curl -fsS "http://localhost:$port/api/check/health" >/dev/null; then ready=true;break;fi
  sleep 2
 done
 "$ready" || { echo "Runtime $port did not become healthy"; exit 1; }
done
python3 scripts/ttc_test_client.py
python3 scripts/test_ttc_exchange.py
python3 scripts/test_live_tms_exchange.py
systemctl is-active tms-edc-provider tms-edc-consumer tms-edc-dataplane
systemctl list-timers tms-demo-stop.timer --no-pager
