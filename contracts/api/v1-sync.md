# API v1 Sync Contract

Updated: 2026-06-26

Status: documented. Endpoints are planned for Phase 14.

Product Vision is defined in `../../docs/product-vision.md`: this contract
supports offline-capable mobile work while Admin/API remains authoritative for
replay, conflicts, and canonical state.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract is the API-first boundary that makes offline-capable mobile work safer
than a standalone mobile app.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must make queued intents,
idempotency, replay outcomes, conflicts, retry states, mobile errors, and
tenant re-checks first-class API behavior.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: sync behavior must document
offline cache/draft/queue rules, online replay, idempotency, accepted/rejected
outcomes, conflicts, permission owners, support/reporting visibility, and
risks before implementation.

Target User Roles are defined in `../../docs/user-roles.md`: sync replay,
conflict visibility, and offline state must follow role and account-state
boundaries.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: sync contracts
create value when offline work, tenant continuity, support diagnostics,
reports, security, and billing/entitlement checks reconcile through API.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: mobile may queue intents and show pending
state, but API replay decides acceptance, rejection, conflict, retry, and
canonical server state.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to conflict
decisions, API contracts, tenant management, users and permissions,
billing/subscription checks, audit history, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports offline
actions, pending queues, sync status display, conflict presentation, retry
feedback, stale-state warnings, and local draft recovery.

Admin Control Center Logic is defined in
`../../docs/admin-control-center-logic.md`: this contract must keep offline
eligibility, queueable actions, replay windows, retry limits, conflict modes,
stale thresholds, maintenance blocks, and policy decisions scoped, authorized,
auditable, and exposed to mobile only as resolved API outcomes.

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
