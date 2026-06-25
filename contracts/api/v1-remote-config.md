# API v1 Remote Config Contract

Updated: 2026-06-26

Status: partially implemented. `GET /api/v1/mobile/config` returns resolved
foundation, global, and tenant-scoped mobile config with freshness,
compatibility, fallback, and deterministic version metadata. Platform-admin
users can manage global config defaults with JSON validation, impact preview,
audit, and audit-history restore. Tenant override UI, publish workflows,
plan/version/feature gates, and mobile local cache integration remain pending.

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
- `apps/api-admin/tests/Feature/MobileRemoteConfigResolutionTest.php`
- `apps/api-admin/tests/Feature/MobileBootstrapApiTest.php`

Fresh checks:

```bash
cd apps/api-admin && php artisan test --compact --filter=AdminRemoteConfigsTest
cd apps/api-admin && php artisan test --compact --filter=MobileRemoteConfigResolutionTest
cd apps/api-admin && php artisan test --compact --filter=MobileBootstrapApiTest
```

Future Phase 9 coverage should add tenant override UI, publish workflows,
mobile stale-cache behavior, plan/version/feature gate interactions, and
invalid config failure modes beyond JSON-object validation.
