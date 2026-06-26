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

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must define record API
purpose, predictable mobile-safe shapes, friendly validation/permission errors,
sync/conflict behavior, and tenant-boundary protection.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: record behavior must document
feature purpose, admin/mobile ownership, mobile screen dependency, offline and
online sync behavior, permission owner, support/audit expectations, and risks
before implementation.

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

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to API
contracts, users and permissions, tenant management, reporting, audit history,
conflict decisions, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports mobile
record UX, local cache, local drafts, offline action preparation, sync status
display, validation feedback, and API-derived feature visibility.

Mobile UX Principles are defined in `../../docs/mobile-ux-principles.md`: this
contract should support mobile-first navigation, simple screens, clear
loading/offline states, thumb-friendly controls, minimum typing, fast actions,
secure sessions, feature visibility, and native permission education.

Mobile App Shell Logic is defined in `../../docs/mobile-app-shell-logic.md`:
shell states must coordinate welcome, authenticated, locked, offline, maintenance, forced update, tenant
switching, sync-in-progress, permission-blocked, and feature-disabled behavior
before implementation.

Mobile Dashboard Logic is defined in `../../docs/mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Mobile Settings Logic is defined in `../../docs/mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `../../docs/mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

Authentication Principles are defined in `../../docs/authentication-principles.md`:
mobile login must happen through the API only; access and refresh tokens must
use secure storage; refresh, logout, logout-all-devices, tenant selection,
session expiry, offline already-authenticated behavior, and server revocation
must preserve Admin/API authority before implementation.

Mobile App Lock Principles are defined in `../../docs/mobile-app-lock-principles.md`:
the mobile client must lock on security-sensitive lifecycle, timeout,
account, tenant, offline-cache, and admin-policy conditions; require
confirmation for sensitive areas; use biometric or PIN unlock only as local
protection; handle failed attempts, logout, admin-disabled biometrics, and
offline cached data without bypassing Admin/API authority.

Role And Permission Logic is defined in `../../docs/role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact
with feature flags as separate gates; suspended users and suspended tenants
fail closed without bypassing tenant isolation.

Audit Logic is defined in `../../docs/audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

Data Privacy Principles are defined in `../../docs/data-privacy-principles.md`:
tenant isolation, least privilege, secure local mobile data, secure native
storage, export and deletion boundaries, support access limits, admin
visibility boundaries, privacy-by-default behavior, and mobile diagnostics
privacy limits must protect users and tenants without turning mobile cache,
support views, or audit history into uncontrolled data exposure.

Tenant Lifecycle Logic is defined in `../../docs/tenant-lifecycle-logic.md`:
tenant creation, onboarding, trial, active, suspended, archived,
billing-blocked, deletion/requested deletion, and restore states must be
Admin/API-owned lifecycle decisions that mobile presents as safe,
tenant-scoped, billing-aware, supportable states without inventing local
tenant authority.

Tenant Admin Logic is defined in `../../docs/tenant-admin-logic.md`:
tenant admins may manage tenant-scoped users, invitations, delegated settings,
delegated mobile-feature controls, tenant reports, and tenant support workflows
only inside their tenant; platform-only controls, cross-tenant visibility,
global policy, billing authority, lifecycle authority, app-version policy, and
security posture remain Admin/API-owned boundaries.

Multi-Tenant Mobile Logic is defined in `../../docs/multi-tenant-mobile-logic.md`:
users with more than one tenant choose and remember tenant context through
API-confirmed state; tenant switching, tenant-scoped cache, per-tenant
permissions and feature flags, sync replay, offline behavior, and logout
cleanup must preserve tenant isolation and never turn mobile-local state
into tenant authority.

Offline-First Principles are defined in `../../docs/offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

Offline UX Logic is defined in `../../docs/offline-ux-logic.md`:
offline UX must calmly explain banners, disabled online-only actions,
local drafts, pending indicators, retry, sync success or failure,
saved-local versus synced state, and data-loss prevention whenever
connection changes.

Records/Content Module Logic is defined in `../../docs/records-content-module-logic.md`:
records are tenant-scoped business content with API-owned lifecycle,
notes, attachments, activity, tags, categories, status, offline draft or
sync behavior, admin controls, permissions, feature flags, audit, and
reporting boundaries.

Sync Lifecycle Logic is defined in `../../docs/sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `../../docs/conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

Admin Control Center Logic is defined in
`../../docs/admin-control-center-logic.md`: this contract must keep record
feature availability, permissions, sync policy, conflict outcomes, report
visibility, support context, and tenant boundaries scoped, authorized,
auditable, and exposed to mobile only as resolved API outcomes.

Mobile Version Control Logic is defined in
`../../docs/mobile-version-control-logic.md`: record viewing, editing,
attachments, drafts, offline intents, and sync replay must respect app-version
compatibility, forced updates, deprecated clients, blocked builds, maintenance,
and stale-client protection before API accepts protected record work.

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
