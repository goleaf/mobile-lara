# NativePHP Local SQLite Storage

Updated: 2026-06-25

This document defines how local SQLite should fit into the SaaS mobile + admin product. Local storage exists to make the NativePHP mobile client resilient. It does not replace the Admin/API system as the source of business authority.

The storage vision follows [Product Vision](product-vision.md): the mobile app can keep users productive locally, but admin/API policy remains final.

It also supports the [Product Positioning](product-positioning.md): Mobile Lara is an offline-capable mobile system, but offline data is still governed by the SaaS control center and reconciled through the API-first boundary.

The storage rules follow [Core Product Principles](product-principles.md): mobile never bypasses the API, offline-first is used only where useful, tenant isolation remains server-enforced, and secure defaults keep secrets out of local SQLite.

Local storage must also respect [Target User Roles](user-roles.md). Mobile-local cache may reflect the currently authorized mobile user, but invited, suspended, and guest/pre-login states must not retain normal workflow access.

Local storage must also support the [SaaS Value Map](saas-value-map.md). Offline sync creates value for tenant businesses, tenant admins, mobile workers/clients, support teams, and billing/operations only when local work remains cache, draft, or pending intent until the API confirms it.

Local storage must also obey [Two-System Boundary Logic](two-system-boundary.md). Mobile-local data may improve speed, drafts, offline work, and sync visibility, but it must not own tenant, permission, billing, feature, app-version, support, report, audit, or final sync authority.

## Product Role

The mobile client may use local SQLite for:

- Cached server data that is safe to display while stale.
- Local drafts and incomplete user work.
- Offline action intents waiting for replay.
- Local records and metadata that still need server confirmation.
- Sync cursors, last sync timestamps, and conflict state.
- Non-sensitive activity history that helps the user understand what happened.
- Local notification history and schedule metadata when the feature allows it.

The mobile client must not use local SQLite for:

- Access tokens, refresh tokens, API secrets, private keys, or billing credentials.
- Tenant authority, plan authority, or permission authority.
- Server-trusted audit logs.
- Final billing state.
- Unencrypted sensitive documents.
- Anything the API must be able to revoke immediately.

Secrets belong in NativePHP secure storage or another approved secure-token store when available.

## Vision Fit

Local storage is valuable because mobile users often work with changing connectivity, device conditions, and interruption-heavy workflows. It is risky when it starts acting like the business authority.

The product boundary is:

- Admin users configure whether offline work is allowed for a tenant, role, feature, app version, or device state.
- Mobile users can draft, cache, and queue work only inside those allowed boundaries.
- The API accepts, rejects, transforms, or marks queued work as conflicted.
- Support users can inspect safe sync context when local and server state diverge.
- Documentation defines offline behavior before local storage expands into a new module.
- Suspended users cannot replay new local work as trusted actions until the API reauthorizes them.
- Billing/operations users can trust entitlement checks during replay because offline value is confirmed by the server, not by stale local state.

This keeps offline-first behavior scalable: more tenants and devices can work locally without multiplying trusted client-side rules.

This is one reason the product is stronger than web-only and mobile-only alternatives. Web-only systems usually cannot provide reliable local mobile work. Mobile-only systems can store local work, but without a control center they struggle to enforce tenant policy, billing, permissions, rollout, and support context after reconnecting.

## Current Configuration

The mobile app has a dedicated SQLite connection named `mobile_local`.

- Connection: `mobile_local`
- Config: `config/database.php`, `config/mobile_local.php`
- Default database file: `storage/app/mobile/mobile-local.sqlite`
- Migration path: `database/migrations/mobile-local`
- Health command: `php artisan mobile:local-health`

The local database file is intentionally stored under `storage/app/mobile` so it is writable in a packaged NativePHP mobile runtime.

## Offline State Model

Every locally stored item should fit one of these states:

| State | Meaning | Server authority |
| --- | --- | --- |
| Cached | Server-confirmed data stored for fast or offline reads. | Server remains authoritative. |
| Draft | User-created local work not submitted yet. | Server does not know it exists. |
| Pending | Queued intent waiting for API replay. | Server has not accepted it. |
| Synced | Server accepted the intent and returned confirmed state. | Server authoritative. |
| Conflict | Server rejected or could not apply the intent as-is. | Server authoritative; user/admin may resolve. |
| Failed | Retry limit or policy stopped replay. | Server authoritative; support/admin may inspect. |

Mobile UI should expose pending, conflict, and failed states clearly. It should not silently present pending local work as confirmed server truth.

## Offline Action Principles

Queued actions should be designed as intents:

- Include a stable local ID.
- Include an idempotency key.
- Include action type and safe payload.
- Include tenant/user/device context only as claims to be verified by the API.
- Include created-at and retry metadata.
- Avoid storing secrets or oversized binary payloads in the queue.
- Replay only through API endpoints.

The API may accept, transform, reject, or mark an action conflicted. The mobile client must render the server decision.

This is the storage boundary: local SQLite may hold the queue, but the API owns replay acceptance and final state.

## Sync Policy

Sync behavior is controlled by the Admin/API system. The mobile client should receive policy through boot config or remote config:

- Which feature modules can queue offline writes.
- Maximum queue age.
- Maximum retry count.
- Backoff strategy.
- Conflict mode.
- Stale-data warning threshold.
- Whether sync can run on metered networks.
- Whether a tenant or app version is temporarily sync-blocked.

## Conflict Policy

Conflicts should be explicit product objects, not vague errors.

A conflict should explain:

- Which local action failed.
- Which server resource or policy blocked it.
- Whether the user can retry, edit, discard, or request support.
- Whether admin/support should see it in conflict reports.

Examples:

- User edited a record that the server no longer permits.
- Tenant billing state disabled the feature before sync.
- App version is too old for this action.
- User permission changed while offline.
- Server data changed and local action would overwrite newer data.

## Environment

No local override is required for development. The default path is generated with Laravel's `storage_path()` helper.

If a packaged runtime needs a custom location, set an absolute path:

```dotenv
NATIVEPHP_LOCAL_DB_DATABASE=/absolute/path/to/mobile-local.sqlite
NATIVEPHP_LOCAL_DB_FOREIGN_KEYS=true
```

Avoid relative override paths for `NATIVEPHP_LOCAL_DB_DATABASE`; SQLite paths are resolved from the process working directory and can drift between Herd, CLI, simulator, and packaged app contexts.

## Running Migrations

Run only the mobile-local migration path against the mobile-local connection:

```bash
php artisan migrate --database=mobile_local --path=database/migrations/mobile-local
```

Future local-only migrations belong under the mobile-local path and must remain local-cache/offline-work infrastructure. They should not be used to smuggle SaaS authority into the mobile client.

## Health Check

After migrations run, verify read/write access:

```bash
php artisan mobile:local-health
```

The command writes a non-sensitive probe value to `mobile_local_health_checks`, reads it back through Eloquent, and exits with `0` only when the value matches.

Expected output includes:

```text
Connection: mobile_local
Database: /path/to/storage/app/mobile/mobile-local.sqlite
Migrations: /path/to/database/migrations/mobile-local
Mobile local SQLite storage can write and read data.
```

## NativePHP Simulator Checklist

Before launching a simulator or emulator build:

```bash
php artisan config:clear
php artisan migrate --database=mobile_local --path=database/migrations/mobile-local
php artisan mobile:local-health
```

Then run the NativePHP mobile command documented in `docs/nativephp-run.md` for the target platform.

## Documentation Boundary

This document defines storage principles and sync expectations only. It does not create migrations, fields, models, repositories, sync workers, or API endpoints.
