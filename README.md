# Native EDC × TTC × TMS integration demo

> **Recovered tested baseline: 2026-11-17.** This is not a fresh snapshot of the current GCP runtime. See [SOURCE_STATUS.md](SOURCE_STATUS.md) for missing later workbench files and validation limits. The public checkout contains placeholder management keys; configure consistent local values before running.

This project runs real Eclipse EDC 0.14.1 control planes for Provider and Consumer, an EDC data plane extension, and the sanitized TTC PHP/MySQL middleware. Python files under `scripts/` are integration test drivers; they do not implement the connectors.

## Data and control flow

- TTC authenticates the two machine accounts and supplies the approved contract, parties, product version, validity window and allowed actions.
- EDC exchanges its catalog, negotiates an EDC contract agreement and starts `HttpData-PULL` through the Dataspace Protocol.
- The EDC policy extension checks the actual participant mapping and live TTC contract during negotiation and transfer authorization.
- The EDC data plane authorizes the EDR, checks the TTC contract again for each HTTP pull, and fetches the TMS API using its private Vault secret.
- Each HTTP exchange receives a new UUID. Provider and Consumer separately report their SHA-256, byte count, EDC agreement and provider transfer process identifiers to TTC. TTC pairs matching reports and rejects conflicting duplicate submissions.

`contract_id` identifies the TTC agreement; EDC agreement/process IDs identify the native exchange lifecycle; `exchange_id` distinguishes individual HTTP pulls. These identifiers are not interchangeable access tokens.

## Isolated test setup

Use Java 17+ and Maven; Docker is required for the middleware. The middleware copy contains no production SQL rows or original private keys. `middleware/schema-only.sql` contains only DDL; `middleware/seed-test.php` creates synthetic organizations, machines, product/version and an approved test contract.

```sh
mvn -B -ntp clean package
docker build -t ttc-middleware:demo middleware
bash scripts/cloud-middleware-start.sh
docker cp middleware/seed-test.php ttc-demo-php:/tmp/seed-test.php
docker cp secrets/platform/provider_public.pem ttc-demo-php:/tmp/provider.pub
docker cp secrets/platform/consumer_public.pem ttc-demo-php:/tmp/consumer.pub
docker exec ttc-demo-php php /tmp/seed-test.php
python3 scripts/configure-ttc-demo.py
bash scripts/start-demo.sh
# Wait for both control planes' /api/check/health endpoints to return HTTP 200.
bash scripts/start-dataplane.sh
python3 scripts/ttc_test_client.py
python3 scripts/test_ttc_exchange.py
```

The live test additionally requires a deployment-supplied full cookie value in `secrets/tms-cookie.txt` and `tms.secret.tms-upstream-cookie.path=secrets/tms-cookie.txt` in data-plane configuration. Restart the data plane after changing its secret configuration. Credentials are deliberately absent from this source package.

```sh
python3 scripts/test_live_tms_exchange.py
```

The test verifies native negotiation and pull, nonempty JSON from the real TMS endpoint, invalid EDR rejection, paired audit, idempotent retry, conflicting retry rejection, reporter role enforcement, and rejection of an existing EDR while the TTC test contract is suspended. It restores the synthetic contract afterward.

## Google Cloud demonstration

Project: `sanguine-era-92109`; instance: `connector-demo`; zone: `asia-east1-b`.

Private service directory: `/opt/tms-edc`. Native services are `tms-edc-provider`, `tms-edc-consumer`, and `tms-edc-dataplane`; PHP/MySQL run as `ttc-demo-php` and `ttc-demo-mysql`. Access uses the existing IAP path, without exposing EDC management endpoints publicly.

```sh
gcloud compute ssh connector-demo --zone=asia-east1-b --tunnel-through-iap
sudo systemctl status tms-edc-provider tms-edc-consumer tms-edc-dataplane
cd /opt/tms-edc
sudo python3 scripts/test_live_tms_exchange.py
```

Deployment script: `deploy/install-vm.sh`. The temporary demo stop timer is scheduled for 2026-11-20 05:00 UTC (13:00 Taiwan/Singapore). Changing or canceling the demo requires reviewing this timer. Stopping the VM stops compute charges; persistent storage and retained IP resources may still incur charges.

## Scope and remaining production work

This is an isolated interoperability demonstration. EDC uses mock IAM and in-memory EDC stores/Vault; TTC uses a persistent MySQL volume. After restarting EDC, rerun the test to register new catalog offerings and negotiate fresh agreements. The original TTC approval UI workflow is not covered by the seeded fixture test. Unsupported runtime constraints are rejected; this demo supports the fixture's `query` action. Provider audit failures are saved under `logs/audit-outbox`; automatic replay is not yet implemented. Production requires federated identity, durable EDC stores/Vault, HTTPS beyond the isolated loopback network, supported constraint enforcement, and a reliable audit replay worker.
