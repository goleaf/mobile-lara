# API v1 Remote Config Contract

Updated: 2026-06-26

Status: partially implemented. `GET /api/v1/mobile/config` returns resolved
foundation, global, and tenant-scoped mobile config with freshness,
compatibility, fallback, and deterministic version metadata. Platform-admin
users can manage global config defaults with JSON validation, impact preview,
audit, and audit-history restore. They can also manage tenant overrides with
the same safety workflow. Publish workflows, plan/version/feature gates, and
mobile local cache integration remain pending.

Product Vision is defined in `../../docs/product-vision.md`: this contract
lets admins adjust safe mobile behavior without turning the mobile app into a
configuration authority.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the feature-controlled and offline-capable mobile platform by
keeping runtime behavior configurable from the control center.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must return resolved config,
config version, freshness, compatibility, fallback state, mobile-friendly
errors, and tenant-safe context instead of raw config authority.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: remote config behavior must
document admin change intent, mobile effect, API dependency, cache/offline
fallback, permission ownership, invalid-config risk, support, audit, and
rollback before implementation.

Target User Roles are defined in `../../docs/user-roles.md`: config outcomes
may vary presentation by role or account state, but must not grant authority.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: remote config
creates value by safely tuning mobile behavior, notifications, sync messaging,
support guidance, and feature presentation without turning config into
authority.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: config authority and validation stay in
Admin/API while mobile caches resolved config with freshness and fallback state.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to remote
configuration, feature control, API contracts, support operations, audit
history, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports local
config cache, freshness display, safe fallback feedback, feature presentation,
permission-purpose copy, and sync/navigation tuning without giving mobile
global configuration authority.

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
`../../docs/admin-control-center-logic.md`: this contract must keep remote
config, defaults, tenant overrides, rollback, invalid-config behavior, support
meaning, and mobile-safe presentation controls scoped, authorized, auditable,
and exposed to mobile only as resolved API outcomes.

## Purpose

Remote config endpoints expose validated, resolved, mobile-safe runtime values.
Remote config can tune enabled behavior but cannot grant permissions, bypass
billing, bypass tenant status, or bypass mobile version policy.

## Implemented Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/config` | Return resolved remote config for the current mobile context. | mobile token |

## Success Data

The response returns `config`, `config_version`, `freshness`, `compatibility`,
`defaults_used`, and optional `support_context`.

Config may cover sync intervals, upload limits, dashboard widgets, legal links,
support links, notification presentation, app lock behavior, and messages.

The current implementation resolves section-level config for `app_lock`,
`dashboard`, `legal`, `support`, `sync`, and `uploads`. Global config records
override foundation defaults, tenant overrides merge on top of the current
tenant context, and sensitive global config records are excluded from mobile
resolution.

## Gates

Config is constrained by global defaults, tenant overrides, plan limits,
permissions, feature flags, app-version policy, maintenance, and emergency
rules.

## Offline Behavior

Mobile may use last-known config with freshness labels. Missing, invalid, or
incompatible config must fall back to bundled safe defaults and fail closed for
sensitive behavior.

## Audit

Audit config publish, tenant override, validation failure, rollback, emergency
override, and retirement.

## Tests

Automated coverage:

- `apps/api-admin/tests/Feature/AdminRemoteConfigsTest.php`
- `apps/api-admin/tests/Feature/AdminTenantRemoteConfigOverridesTest.php`
- `apps/api-admin/tests/Feature/MobileRemoteConfigResolutionTest.php`
- `apps/api-admin/tests/Feature/MobileBootstrapApiTest.php`

Fresh checks:

```bash
cd apps/api-admin && php artisan test --compact --filter=AdminRemoteConfigsTest
cd apps/api-admin && php artisan test --compact --filter=AdminTenantRemoteConfigOverridesTest
cd apps/api-admin && php artisan test --compact --filter=MobileRemoteConfigResolutionTest
cd apps/api-admin && php artisan test --compact --filter=MobileBootstrapApiTest
```

Future Phase 9 coverage should add publish workflows, mobile stale-cache
behavior, plan/version/feature gate interactions, and invalid config failure
modes beyond JSON-object validation.
