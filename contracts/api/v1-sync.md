# API v1 Sync Contract

Updated: 2026-06-25

Status: documented. Endpoints are planned for Phase 14.

## Purpose

Sync endpoints let the mobile client replay queued local intents and pull
server changes while the Admin/API remains authoritative for acceptance,
conflict decisions, tenant boundaries, and audit.

## Planned Routes

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/sync/bootstrap` | Return sync policy and cursors. | mobile token |
| POST | `/api/v1/mobile/sync/push` | Submit queued idempotent mobile intents. | mobile token |
| GET | `/api/v1/mobile/sync/pull` | Pull server changes after a cursor. | mobile token |
| POST | `/api/v1/mobile/sync/acknowledge` | Acknowledge delivered changes. | mobile token |

## Success Data

Responses return `accepted`, `rejected`, `conflicts`, `server_changes`,
`next_cursor`, `retry_after`, and `sync_policy`.

Push items require stable client intent IDs and idempotency keys.

## Gates

Sync is controlled by tenant status, user permissions, feature flags, app
version, maintenance, subscription status, remote config, replay window,
payload size, and conflict policy.

## Offline Behavior

Mobile owns the local queue and status presentation. It must not mark an intent
as server-accepted until the API confirms acceptance.

## Audit

Audit pushed intents, accepted writes, rejected writes, conflicts, conflict
resolution, replay abuse, and sync disabling.

## Tests

Phase 14 should verify idempotency, tenant isolation, cursor behavior,
conflict states, retry behavior, and fail-closed stale policy.
