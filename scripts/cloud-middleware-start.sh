#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
umask 077
mkdir -p secrets/platform
if [[ ! -f secrets/db.env ]]; then
 python3 - <<'PY'
from pathlib import Path
import secrets
Path('secrets/db.env').write_text('TTC_DB_PASSWORD='+secrets.token_hex(24)+'\nMYSQL_ROOT_PASSWORD='+secrets.token_hex(24)+'\n')
PY
fi
source secrets/db.env
for name in server provider consumer; do
 if [[ ! -f secrets/platform/${name}_private.key ]]; then
  openssl genpkey -algorithm RSA -pkeyopt rsa_keygen_bits:2048 -out "secrets/platform/${name}_private.key" 2>/dev/null
  openssl pkey -in "secrets/platform/${name}_private.key" -pubout -out "secrets/platform/${name}_public.pem" 2>/dev/null
 fi
done
chmod 644 secrets/platform/server_private.key secrets/platform/server_public.pem
if ! docker network inspect tms-ttc-test >/dev/null 2>&1; then docker network create tms-ttc-test >/dev/null; fi
if ! docker container inspect ttc-demo-mysql >/dev/null 2>&1; then
 docker run -d --restart unless-stopped --memory=768m --name ttc-demo-mysql --network tms-ttc-test --network-alias mysql --env MYSQL_DATABASE=ttcdb --env MYSQL_USER=ttc --env MYSQL_PASSWORD="$TTC_DB_PASSWORD" --env MYSQL_ROOT_PASSWORD="$MYSQL_ROOT_PASSWORD" -v ttc-demo-db:/var/lib/mysql mysql:8.0 --innodb-buffer-pool-size=128M --performance-schema=OFF >/dev/null
fi
for i in $(seq 1 60); do
 if docker exec -e MYSQL_PWD="$MYSQL_ROOT_PASSWORD" ttc-demo-mysql mysql -uroot -N -e "SELECT 1" >/dev/null 2>&1; then break; fi
 sleep 2
done
docker exec -e MYSQL_PWD="$MYSQL_ROOT_PASSWORD" ttc-demo-mysql mysql -uroot -N -e "SELECT 1" >/dev/null
if [[ ! -f secrets/schema-loaded ]]; then
 docker exec -i -e MYSQL_PWD="$MYSQL_ROOT_PASSWORD" ttc-demo-mysql mysql -uroot ttcdb <middleware/schema-only.sql
 docker exec -i -e MYSQL_PWD="$MYSQL_ROOT_PASSWORD" ttc-demo-mysql mysql -uroot ttcdb <middleware/edc-schema.sql
 touch secrets/schema-loaded
fi
if ! docker container inspect ttc-demo-php >/dev/null 2>&1; then
 docker run -d --restart unless-stopped --memory=256m --name ttc-demo-php --network tms-ttc-test -p 127.0.0.1:8088:80 -e TTC_DB_HOST=mysql -e TTC_DB_NAME=ttcdb -e TTC_DB_USER=ttc -e TTC_DB_PASSWORD="$TTC_DB_PASSWORD" -v "$PWD/secrets/platform/server_private.key:/var/www/html/server_private.key:ro" -v "$PWD/secrets/platform/server_public.pem:/var/www/html/server_public.pem:ro" ttc-middleware:demo >/dev/null
fi
printf 'TTC isolated middleware ready on loopback port 8088\n'
