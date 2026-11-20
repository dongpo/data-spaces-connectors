#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
nohup java -Dedc.fs.config=config/dataplane.properties -jar data-plane/target/data-plane-0.1.0.jar >logs/dataplane.log 2>&1 &
echo "$!" >logs/dataplane.pid
