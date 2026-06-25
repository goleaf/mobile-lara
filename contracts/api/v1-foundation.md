# API v1 Foundation Contract

Updated: 2026-06-26

This contract records the first implemented mobile-facing API foundation in
`apps/api-admin`. It proves the route namespace and standard envelope before
the domain contracts are implemented.

Product Vision is defined in `../../docs/product-vision.md`: this foundation
keeps mobile clients dependent on Admin/API authority instead of local
assumptions.

Product Positioning is defined in `../../docs/product-positioning.md`: this
foundation proves the API-first bridge between the SaaS control center and the
mobile workforce/client platform.

Core Product Principles are defined in `../../docs/product-principles.md`: this
foundation must preserve admin control, API-first communication, tenant
isolation, secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this foundation must prove predictable
response envelopes, error semantics, operating-context conventions, and
tenant-safe API behavior before domain contracts expand.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: foundation behavior must
document response purpose, future mobile dependency, API consistency risk,
permission assumptions, and implementation boundaries before domain contracts
expand.

Target User Roles are defined in `../../docs/user-roles.md`: foundation
contracts must keep role and account-state context explicit before domain
endpoints depend on it.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: foundation
contracts should prove that API shape, errors, metadata, and contract catalogue
support stakeholder value without implementing hidden product authority.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: foundation contracts should prove the API
is the boundary between Admin/API authority and mobile-local execution.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this foundation belongs to API
contracts, security enforcement, audit history, and response-shape
responsibility before domain contracts expand.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this foundation should prove
the response shape mobile can consume for UX, local session, cache, offline,
sync, feedback, and feature-visibility responsibilities before domain
contracts expand.

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

Admin Control Center Logic is defined in
`../../docs/admin-control-center-logic.md`: this foundation should keep future
tenant, user, role, permission, feature, config, version, maintenance, sync,
notification, report, billing, and support controls scoped, authorized,
auditable, and mobile-safe before domain contracts expand.

Remote Configuration Logic is defined in
`../../docs/remote-configuration-logic.md`. Future bootstrap and config
contracts should return resolved mobile-safe config values, config version,
freshness, compatibility, tenant override context, and fallback/error states
without exposing admin-only configuration internals.

Mobile Version Control Logic is defined in
`../../docs/mobile-version-control-logic.md`. Future app-version and
maintenance contracts should return resolved update, force-update, blocked,
deprecated, maintenance, store-link, message, and stale-client states without
exposing raw admin policy internals.

## Base Path

```text
/api/v1/mobile
```

## Implemented Endpoint

### GET `/api/v1/mobile/status`

Purpose:

- Confirm the Admin/API app is reachable through the versioned mobile API
  namespace.
- Confirm the API response uses the standard success envelope.
- Give mobile and diagnostics flows a low-risk health target while bootstrap,
  authentication, tenancy, feature flags, remote config, sync, and support
  contracts are still being implemented.

Success response:

```json
{
  "success": true,
  "data": {
    "service": "api-admin",
    "authority": "admin_api",
    "mobile_api": "v1",
    "status": "ok"
  },
  "meta": {
    "api_version": "v1",
    "next_contract": "v1-bootstrap",
    "server_time": "2026-06-25T00:00:00Z"
  }
}
```

### GET `/api/v1/mobile/contracts`

Purpose:

- Return the authoritative v1 mobile contract catalogue.
- Let mobile diagnostics, tests, and implementation planning discover which
  contract groups exist and which endpoints are implemented or planned.
- Keep planned endpoints explicit without pretending auth, tenancy, or domain
  modules exist before their phases.

Success response:

```json
{
  "success": true,
  "data": {
    "base_path": "/api/v1/mobile",
    "contract_version": "v1",
    "authority": "admin_api",
    "contracts": [
      {
        "key": "foundation",
        "document": "v1-foundation.md",
        "status": "implemented",
        "routes": [
          {
            "method": "GET",
            "path": "/status",
            "status": "implemented",
            "auth": "public"
          }
        ]
      }
    ]
  },
  "meta": {
    "api_version": "v1",
    "contract_count": 14,
    "next_contract": "v1-bootstrap",
    "server_time": "2026-06-25T00:00:00Z"
  }
}
```

## Standard Error Envelope

Implemented error responses use this shape:

```json
{
  "success": false,
  "error": {
    "code": "maintenance",
    "message": "Mobile API is temporarily unavailable.",
    "category": "maintenance",
    "next_action": "retry_later"
  },
  "meta": {
    "api_version": "v1",
    "server_time": "2026-06-25T00:00:00Z"
  }
}
```

## Current Boundaries

- This endpoint does not authenticate a mobile user.
- This endpoint does not expose tenant data, feature flags, remote config,
  permissions, subscription state, notifications, or sync settings.
- The contract catalogue is public because it exposes only documentation
  metadata and planned route names.
- The next control-plane implementation contract is `v1-bootstrap.md`.

## Verification

Automated coverage in `apps/api-admin`:

- `tests/Feature/MobileApiEnvelopeTest.php`
- `tests/Feature/MobileApiContractCatalogueTest.php`
- `tests/Unit/MobileApiResponseTest.php`

Fresh checks for this phase:

```bash
php artisan route:list --except-vendor
php artisan test --compact
vendor/bin/pint --dirty --format agent
npm run build
```
