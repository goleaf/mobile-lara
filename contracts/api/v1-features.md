# API v1 Features Contract

Updated: 2026-06-26

Status: partially implemented. `GET /api/v1/mobile/features` returns resolved
global, tenant, and user feature outcomes for the current tenant/user context.
The admin panel manages audited global feature defaults and tenant-scoped
overrides with mobile impact previews. User scoped override screens,
plan/version/device/cohort gates, emergency controls, and mobile-local feature
cache integration remain pending.

Product Vision is defined in `../../docs/product-vision.md`: this contract
keeps important mobile capabilities feature-controlled by Admin/API.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract is the API-first expression of the feature-controlled platform.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must expose resolved
feature purpose, availability states, context, mobile-friendly disabled
messages, version constraints, and tenant-safe outcomes through API only.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: feature behavior must
document purpose, admin mobile effect, mobile screen dependency, online/offline
availability, permission owner, rollout risk, and rollback before
implementation.

Target User Roles are defined in `../../docs/user-roles.md`: feature outcomes
must resolve role and account-state access into mobile-safe states.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: feature outcomes
must explain stakeholder value from rollout control, tenant adoption, mobile
clarity, support explanation, billing entitlements, and security boundaries.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: feature authority and rollout decisions
stay in Admin/API while mobile renders resolved enabled, disabled, blocked,
deprecated, or update-required states.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to feature
control, API contracts, billing/subscription logic, mobile version rules,
support/report visibility, audit history, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports
API-derived feature visibility, disabled/blocked/deprecated/update-required
feedback, navigation shaping, cache freshness, and offline-limited messaging.

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

Admin Control Center Logic is defined in
`../../docs/admin-control-center-logic.md`: this contract must keep feature
enablement, disablement, rollout, rollback, plan limits, emergency blocks,
and disabled mobile states scoped, authorized, auditable, and exposed to mobile
only as resolved API outcomes.

Feature Flag Logic is defined in `../../docs/feature-flag-logic.md`: this
contract must resolve important mobile features through controlled purpose,
global/tenant/user priority, disabled-state behavior, admin impact, safe
rollout, and plan-limit rules before mobile receives any feature outcome.

Remote Configuration Logic is defined in
`../../docs/remote-configuration-logic.md`: enabled, disabled, blocked, beta,
deprecated, update-required, offline-limited, and emergency-disabled feature
states may receive resolved copy, limits, thresholds, workflow options, support
guidance, and offline/sync presentation without giving config feature
authority.

## Purpose

Feature endpoints expose resolved mobile-safe feature outcomes. Mobile never
receives raw global, tenant, user, plan, version, cohort, maintenance, or
emergency flag internals.

## Implemented Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/features` | Return resolved feature availability for the current context. | mobile token |

## Success Data

The response returns `features`, keyed by feature code. Each feature includes
`state`, `visible`, `enabled`, `reason`, `next_action`, `minimum_app_version`,
`offline_behavior`, and optional `message`.

Allowed states include `hidden`, `visible`, `disabled`, `blocked`, `beta`,
`deprecated`, `update_required`, `offline_limited`, and `emergency_disabled`.

## Gates

The current implementation resolves user override, tenant override, then global
default, with a permission gate applied before mobile receives the final state.
Future slices must add safety and maintenance rules, plan limits, app-version
and device rules, cohort rules, emergency blocks, and richer offline
limitations.

## Offline Behavior

Mobile may cache resolved features with freshness metadata. A stale feature
cache cannot broaden access and must hide or disable risky actions when unsure.

## Audit

Audit admin feature changes, tenant overrides, user overrides, emergency
disable, rollout changes, and support-visible denials.

The current admin implementation writes `admin_mobile_feature_flag_created` and
`admin_mobile_feature_flag_updated` events with before/after feature metadata
for global default changes. Tenant override controls write
`admin_tenant_feature_override_created`, `admin_tenant_feature_override_updated`,
and `admin_tenant_feature_override_restored` events with before/after
tenant-scoped metadata.

## Tests

Automated coverage:

- `apps/api-admin/tests/Feature/MobileFeatureFlagResolutionTest.php`
- `apps/api-admin/tests/Feature/AdminFeatureFlagsTest.php`
- `apps/api-admin/tests/Feature/AdminTenantFeatureOverridesTest.php`

Fresh checks:

```bash
cd apps/api-admin && php artisan test --compact --filter=MobileFeatureFlagResolutionTest
cd apps/api-admin && php artisan test --compact --filter=AdminFeatureFlagsTest
cd apps/api-admin && php artisan test --compact --filter=AdminTenantFeatureOverridesTest
```

Future Phase 8 coverage should add stale-cache behavior, tenant/user override
admin screens, plan/version/device gates, emergency disablement, and no raw
flag layers in API responses beyond resolved mobile-safe outcomes.
