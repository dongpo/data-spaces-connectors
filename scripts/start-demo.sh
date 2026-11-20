#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
umask 077
mkdir -p logs secrets
if [[ ! -f secrets/signing-key.pem ]]; then
 openssl req -x509 -newkey rsa:2048 -keyout secrets/signing-key.pem -out secrets/signing-cert.pem -sha256 -days 14 -nodes -subj /CN=tms-edc-isolated-demo >/dev/null 2>&1
fi
for role in provider consumer; do
 nohup java -Dedc.fs.config="config/$role.properties" -jar control-plane/target/control-plane-0.1.0.jar >"logs/$role.log" 2>&1 &
 echo "$!" >"logs/$role.pid"
done
# Data plane startup is intentionally separate so control-plane readiness can be checked first.
