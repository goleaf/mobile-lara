# API v1 App Version And Maintenance Contract

Updated: 2026-06-26

Status: partially implemented. `GET /api/v1/mobile/app-version` returns
resolved app-version and maintenance policy for reported platform/version
context, bootstrap uses the same resolver, and platform-admin users can manage
global/platform policies with confirmation, impact preview, audit, and
audit-history restore. Scoped tenant/cohort rules, support reports, and mobile
force-update/maintenance screens remain pending.

Product Vision is defined in `../../docs/product-vision.md`: this contract
protects the product promise by keeping stale or unsafe mobile builds under
Admin/API control.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the feature-controlled, API-first, tenant-based mobile
platform by making version and maintenance policy centrally governable.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must return predictable
version, update, maintenance, stale-client, store-link, support, and
tenant-safe response states through API only.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: app-version and maintenance
behavior must document mobile effect, screen dependency, online/offline limits,
permission or role control, support/audit needs, and rollout risks before
implementation.

Target User Roles are defined in `../../docs/user-roles.md`: version and
maintenance outcomes must respect platform, tenant, support, mobile, invited,
suspended, and guest/pre-login boundaries.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: version and
maintenance outcomes protect platform-owner rollout control, tenant-business
continuity, support diagnosability, billing/operations entitlement clarity, and
mobile-worker trust.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: version authority belongs to Admin/API,
while mobile reports build context and presents update, maintenance, deprecated,
or blocked outcomes.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to mobile
version rules, feature control, maintenance/remote config coordination, support
operations, audit history, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports update
prompts, maintenance UX, limited-mode navigation, local draft protection,
blocked-state feedback, store-link presentation, and support guidance.

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
`../../docs/admin-control-center-logic.md`: this contract must keep app
version, maintenance mode, force update, store-link, update-message, and
stale-client controls scoped, authorized, auditable, and exposed to mobile
only as resolved API outcomes.

Feature Flag Logic is defined in `../../docs/feature-flag-logic.md`: app
version and maintenance policy must be able to constrain, block, deprecate, or
roll out feature-flagged mobile behavior when compatibility, safety, plan, or
support risk requires it.

## Purpose

App-version and maintenance endpoints tell mobile whether the current build can
operate safely. Admin/API owns minimum versions, optional update prompts,
forced updates, blocked versions, maintenance state, support messaging, and
rollback.

## Implemented Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/app-version` | Return update and maintenance state for the reported app context. | public with mobile context |

## Request Context

Mobile must send app version, platform, build number, device identifier, and
tenant/user context when authenticated.

## Success Data

The response returns `state`, `minimum_supported_version`,
`latest_version`, `store_url`, `message`, `support_url`, `retry_after`,
`allowed_actions`, and `logout_allowed`.

Allowed states include `current`, `supported`, `optional_update`,
`recommended_update`, `deprecated`, `force_update`, `blocked`,
`maintenance`, `internal_only`, and `stale_client`.

The current implementation supports foundation defaults, platform-specific or
global active policies, minimum-supported force-update decisions, optional
update decisions from minimum recommended versions, explicit blocked versions,
store links, maintenance state, retry timing, support links, and safe allowed
actions.

## Gates

Version state can differ by platform, tenant, feature risk, API contract,
security incident, billing state, sync risk, rollout cohort, and maintenance.

## Offline Behavior

Mobile may continue safe local reads and drafts if the last-known policy allows
it. Risky writes, sync replay, and protected features require fresh API
approval when the version state is unknown or stale.

## Audit

Audit version rule changes, maintenance start/end, forced update, rollback,
stale-client denial, and support-visible impact.

## Tests

Automated coverage:

- `apps/api-admin/tests/Feature/AdminAppVersionPoliciesTest.php`
- `apps/api-admin/tests/Feature/MobileAppVersionPolicyTest.php`
- `apps/api-admin/tests/Feature/MobileBootstrapApiTest.php`

Fresh checks:

```bash
cd apps/api-admin && php artisan test --compact --filter=AdminAppVersionPoliciesTest
cd apps/api-admin && php artisan test --compact --filter=MobileAppVersionPolicyTest
cd apps/api-admin && php artisan test --compact --filter=MobileBootstrapApiTest
```

Future Phase 11 coverage should add tenant/cohort/version-range scoping,
support-visible impact reporting, and mobile force-update or maintenance UI
behavior.
