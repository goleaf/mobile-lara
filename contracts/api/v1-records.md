# API v1 Records Contract

Updated: 2026-06-25

Status: documented. Endpoints are planned for Phase 12.

## Purpose

Records endpoints provide tenant-scoped content authority for list, detail,
create, update, archive, restore, delete, categories, tags, notes, attachment
metadata, and activity timeline.

## Planned Routes

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/records` | List tenant-scoped records. | mobile token |
| POST | `/api/v1/mobile/records` | Create a record or accept an online create intent. | mobile token |
| GET | `/api/v1/mobile/records/{record}` | Show record detail. | mobile token |
| PATCH | `/api/v1/mobile/records/{record}` | Update record fields. | mobile token |
| DELETE | `/api/v1/mobile/records/{record}` | Archive or delete based on policy. | mobile token |

## Success Data

Responses use shaped records with `id`, `tenant_id`, `title`, `status`,
`category`, `tags`, `notes_count`, `attachments_count`, `updated_at`,
`sync_version`, and permission-aware `actions`.

## Gates

Records are controlled by tenant membership, record permissions, feature flags,
remote config, app-version state, subscription status, sync policy, attachment
limits, and maintenance mode.

## Offline Behavior

Mobile may cache records locally, create drafts, and queue idempotent intents.
The API decides conflicts, accepted writes, rejected writes, and server truth.

## Audit

Audit create, update, archive, restore, delete, attachment metadata changes,
note changes, conflict decisions, and sync replay outcomes.

## Tests

Phase 12 should verify tenant isolation, explicit selects/eager loads,
permissions, idempotency keys, conflict responses, and activity timeline.
