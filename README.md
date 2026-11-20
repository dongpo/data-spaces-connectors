# Data Spaces Connectors — GCP Demo Source Export

This import comes from the user-supplied export of `/opt/tms-edc` on GCP instance `connector-demo`, received 2026-11-20. It is **not** the older 2026-11-17 backup.

Includes the Provider/Consumer role workbench, Python integration drivers, TTC PHP middleware and exchange-report reader, runtime configuration, deployment scripts and the available EDC policy Java extension. Python drivers are orchestration code; the actual connectors run Eclipse EDC Java runtimes.

## Completeness

The supplied export contains only one Java source file (`TtcPolicyExtension.java`) and no `pom.xml`. **It is not yet a complete, rebuildable source backup of the deployed Java connectors.** Obtain the corresponding six-module Maven project from the deployment build workspace before claiming a reproducible build. No older build source was silently substituted. Middleware image dependencies/static assets may also need recovery; this directory is the supplied deployment export, not a full Docker image export.

## Public-copy redactions

API cookies, private keys, runtime tokens and database contents are excluded. The deployed demo management key is replaced with a placeholder. An inherited administrator authentication bypass is disabled in this public copy to avoid publishing that credential mechanism. These repository changes **have not modified the running GCP deployment**. See `SOURCE_IMPORT.json` for affected files and hashes.

The workbench HTML in this export already uses **協商過程** and has no recording controls. Legacy recording handlers remain in Python and the older walkthrough driver; their removal has not been applied as part of this faithful source import.

## Middleware changes

See [MIDDLEWARE_CHANGES.md](MIDDLEWARE_CHANGES.md) for defects, fixes, EDC responsibilities, validation evidence and remaining limits. TTC middleware is third-party handover code; this import grants no additional license.

## Validation

Import checks cover Python syntax, shell syntax, inline JavaScript syntax, expected integration paths and credential-marker scanning. Full Java builds, PHP execution and live GCP integration tests have not been rerun in this import environment. Previously observed 2026-11-17 tests are historical evidence, not a new test run.
