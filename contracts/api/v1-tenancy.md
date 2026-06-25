# API v1 Tenancy Contract

Updated: 2026-06-26

Status: partially implemented. Tenant schema, mobile tenant context listing,
tenant switching, bootstrap tenant context, and switch audit events exist;
admin tenant management, invitations, tenant settings UI, and mobile switcher
integration remain pending.

Product Vision is defined in `../../docs/product-vision.md`: this contract
keeps tenant isolation and tenant access controlled by Admin/API while mobile
shows only allowed tenant context.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the tenant-based product by keeping workspace access and
tenant switching server-controlled.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must keep tenant access,
switching context, response shape, errors, and offline replay checks
server-resolved and tenant-safe.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: tenancy behavior must
document tenant authority, mobile effect, API dependency, offline/online tenant
context, permission ownership, and tenant-boundary risks before implementation.

Target User Roles are defined in `../../docs/user-roles.md`: tenant access and
tenant switching must resolve platform, tenant, invited, suspended, and
guest/pre-login boundaries server-side.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: tenancy contracts
create value by preserving tenant isolation while enabling governed mobile
access, tenant reports, support context, billing visibility, notifications, and
feature flags.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: tenant authority stays in Admin/API while
mobile displays current tenant context and switch choices returned by API.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to tenant
management, users and permissions, API contracts, reporting/support/billing
boundaries, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports tenant
context display, tenant-switch UX where allowed, cache labels, navigation
state, and local feedback without giving mobile tenant authority.

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
`../../docs/admin-control-center-logic.md`: this contract must keep tenant
lifecycle, status, isolation, membership, feature availability, billing,
support, report, and sync controls scoped, authorized, auditable, and exposed
to mobile only as resolved API outcomes.

Remote Configuration Logic is defined in
`../../docs/remote-configuration-logic.md`: tenant labels, onboarding copy,
workflow wording, support guidance, cache freshness messages, and safe tenant
presentation may vary by resolved tenant config, but tenant membership,
switching authority, and isolation remain Admin/API authority.

Mobile Version Control Logic is defined in
`../../docs/mobile-version-control-logic.md`: tenant listing, tenant switching,
tenant-scoped bootstrap context, and tenant-specific maintenance or rollout
states must respect supported, deprecated, force-update, blocked, internal-only,
and maintenance-limited app-version policy.

## Purpose

Tenancy endpoints keep tenant authority on the Admin/API system. Mobile may
display and switch allowed tenants, but every server response remains
tenant-scoped and validates membership server-side.

## Implemented Foundation Routes

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/tenants` | List tenants available to the current user. | mobile token |
| POST | `/api/v1/mobile/tenants/current` | Switch current tenant context. | mobile token |

## Success Data

Tenant list responses return `current_tenant` and `available_tenants`.
Tenant API identifiers are `tenants.public_id`; numeric database IDs are not
sent to mobile.

Each tenant item includes `id`, `name`, `slug`, `status`,
`subscription_state`, `role_summary`, `switchable`, `current`, and
`disabled_reason`.

Tenant switch responses return refreshed tenant context and
`next_bootstrap_required: true`. Permissions, feature flags, remote config,
sync, billing, notification, and version policy still come from the bootstrap
foundation defaults until those modules are implemented.

## Gates

Tenant access is controlled by membership, invitation state, suspended state,
tenant lifecycle, role, permission, subscription, maintenance, and app-version
policy.

## Offline Behavior

Mobile can show the last known tenant and separate local cache by tenant.
Switching tenants requires API confirmation before protected actions use the
new tenant context.

## Audit

Tenant switch, failed tenant switch, invitation acceptance, suspended access,
and cross-tenant denial are audit events.

## Tests

Automated coverage:

- `apps/api-admin/tests/Feature/MobileTenancyApiTest.php`
- `apps/api-admin/tests/Feature/MobileBootstrapApiTest.php`

Fresh checks:

```bash
cd apps/api-admin && php artisan test --compact --filter=MobileTenancyApiTest
cd apps/api-admin && php artisan test --compact --filter=MobileBootstrapApiTest
```
