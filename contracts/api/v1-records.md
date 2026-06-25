# API v1 Records Contract

Updated: 2026-06-26

Status: documented. Endpoints are planned for Phase 12.

Product Vision is defined in `../../docs/product-vision.md`: this contract
lets mobile users do tenant-scoped work while Admin/API remains the source of
record authority.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports mobile workforce/client workflows while preserving the
tenant-based SaaS control boundary.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

Target User Roles are defined in `../../docs/user-roles.md`: record access must
resolve tenant admin, tenant manager, mobile user, support, invited, suspended,
and guest/pre-login boundaries server-side.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: records create
tenant-business and mobile-worker value through governed mobile access,
offline-capable work where allowed, reports, security, supportability, and
feature-controlled availability.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: record authority and validation stay in
Admin/API while mobile may cache records, create drafts, and queue allowed
intents for API replay.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to API
contracts, users and permissions, tenant management, reporting, audit history,
conflict decisions, and security enforcement.

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
