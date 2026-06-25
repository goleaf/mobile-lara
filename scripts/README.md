# Scripts

This directory is reserved for root-level monorepo helper scripts.

Product Vision is defined in `../docs/product-vision.md`: scripts may support
verification, but they must preserve the two-system product idea and must not
create hidden authority outside Admin/API.

Product Positioning is defined in `../docs/product-positioning.md`: scripts
should verify the combined SaaS control center and mobile platform posture,
not create side channels around it.

Core Product Principles are defined in `../docs/product-principles.md`: scripts
may verify admin authority, API-first behavior, tenant isolation, security,
offline state, and documentation coverage, but must not create application
logic.

API-First Principles are defined in `../docs/api-first-principles.md`: scripts
may verify API-purpose, response-shape, context, mobile-error, sync/conflict,
and tenant-boundary documentation, but must not create endpoint, route,
controller, database, or runtime behavior.

Documentation-First Architecture is defined in
`../docs/documentation-first-architecture.md`: scripts may verify feature,
admin-control, mobile-screen, sync, permission, and risk documentation gates,
but must not create implementation logic or hidden product authority.

Target User Roles are defined in `../docs/user-roles.md`: scripts may verify
role documentation coverage and role-scoped outcomes, but must not create role,
permission, guard, or policy logic.

SaaS Value Map is defined in `../docs/saas-value-map.md`: scripts may verify
stakeholder-value coverage for admin controls, mobile access, offline sync,
notifications, reports, security, and feature flags, but must not create hidden
product authority.

Two-System Boundary Logic is defined in `../docs/two-system-boundary.md`:
scripts may verify boundary coverage, but must not create side-channel authority
outside Admin/API or mobile-local verification outside documented contracts.

Admin/API Responsibilities are defined in
`../docs/admin-api-responsibilities.md`: scripts may verify responsibility
coverage, but must not create tenant, user, permission, API, feature, config,
version, notification, billing, support, report, audit, conflict, or security
authority.

Mobile Client Responsibilities are defined in
`../docs/mobile-client-responsibilities.md`: scripts may verify mobile
responsibility coverage, but must not create local UX, session, cache, offline,
NativePHP, navigation, permission, sync, draft, feedback, or feature-visibility
logic outside documented contracts.

Mobile UX Principles are defined in `../docs/mobile-ux-principles.md`: scripts
may verify mobile navigation, loading/offline states, thumb-friendly controls,
minimum typing, fast actions, secure sessions, feature visibility, and native
permission education coverage without creating application logic.

Mobile App Shell Logic is defined in `../docs/mobile-app-shell-logic.md`:
shell states must coordinate welcome, authenticated, locked, offline, maintenance, forced update, tenant
switching, sync-in-progress, permission-blocked, and feature-disabled behavior
before implementation.

Mobile Dashboard Logic is defined in `../docs/mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Mobile Settings Logic is defined in `../docs/mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `../docs/mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

Authentication Principles are defined in `../docs/authentication-principles.md`:
mobile login must happen through the API only; access and refresh tokens must
use secure storage; refresh, logout, logout-all-devices, tenant selection,
session expiry, offline already-authenticated behavior, and server revocation
must preserve Admin/API authority before implementation.

Mobile App Lock Principles are defined in `../docs/mobile-app-lock-principles.md`:
the mobile client must lock on security-sensitive lifecycle, timeout,
account, tenant, offline-cache, and admin-policy conditions; require
confirmation for sensitive areas; use biometric or PIN unlock only as local
protection; handle failed attempts, logout, admin-disabled biometrics, and
offline cached data without bypassing Admin/API authority.

Role And Permission Logic is defined in `../docs/role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact
with feature flags as separate gates; suspended users and suspended tenants
fail closed without bypassing tenant isolation.

Audit Logic is defined in `../docs/audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

Data Privacy Principles are defined in `../docs/data-privacy-principles.md`:
tenant isolation, least privilege, secure local mobile data, secure native
storage, export and deletion boundaries, support access limits, admin
visibility boundaries, privacy-by-default behavior, and mobile diagnostics
privacy limits must protect users and tenants without turning mobile cache,
support views, or audit history into uncontrolled data exposure.

Tenant Lifecycle Logic is defined in `../docs/tenant-lifecycle-logic.md`:
tenant creation, onboarding, trial, active, suspended, archived,
billing-blocked, deletion/requested deletion, and restore states must be
Admin/API-owned lifecycle decisions that mobile presents as safe,
tenant-scoped, billing-aware, supportable states without inventing local
tenant authority.

Tenant Admin Logic is defined in `../docs/tenant-admin-logic.md`:
tenant admins may manage tenant-scoped users, invitations, delegated settings,
delegated mobile-feature controls, tenant reports, and tenant support workflows
only inside their tenant; platform-only controls, cross-tenant visibility,
global policy, billing authority, lifecycle authority, app-version policy, and
security posture remain Admin/API-owned boundaries.

Multi-Tenant Mobile Logic is defined in `../docs/multi-tenant-mobile-logic.md`:
users with more than one tenant choose and remember tenant context through
API-confirmed state; tenant switching, tenant-scoped cache, per-tenant
permissions and feature flags, sync replay, offline behavior, and logout
cleanup must preserve tenant isolation and never turn mobile-local state
into tenant authority.

Offline-First Principles are defined in `../docs/offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

Sync Lifecycle Logic is defined in `../docs/sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Scripts must support the Admin Control Center planning boundary in
`../docs/admin-control-center-logic.md`: verification should prove documented
tenant, user, role, permission, feature, config, version, maintenance, force
update, sync, notification, report, billing, and support controls without
creating undocumented application logic.

Scripts must also respect Feature Flag Logic in
`../docs/feature-flag-logic.md`: any future verification helper should check
resolved mobile-safe feature states and avoid creating hidden feature authority
outside the documented Admin/API path.

Scripts must also respect Remote Configuration Logic in
`../docs/remote-configuration-logic.md`: any future helper should verify
documented config contracts, freshness, fallback, and invalid-config behavior
without creating runtime authority outside Admin/API.

Scripts must also respect Mobile Version Control Logic in
`../docs/mobile-version-control-logic.md`: any future helper should verify
documented minimum-version, optional-update, force-update, maintenance, store
link, update-message, and stale-client behavior without creating runtime
authority outside Admin/API.

Scripts must also respect Admin Safety Principles in
`../docs/admin-safety-principles.md`: any future helper should verify dangerous
admin-control documentation for confirmation, audit history, impact preview,
mobile impact preview, rollback, and tenant isolation without creating
application logic.

Scripts must also respect Mobile UX Principles in
`../docs/mobile-ux-principles.md`: any future helper should verify documented
NativePHP mobile UX contracts without creating screens, components, routes, or
runtime behavior.

Do not add custom verification scripts when a normal project command already
proves the behavior. Prefer the real commands:

```bash
composer install
npm install
npm run build
php artisan test --compact
vendor/bin/pint --dirty --format agent
php artisan route:list
php artisan native:plugin:validate
```

When the monorepo apps are split into `apps/api-admin` and
`apps/mobile-client`, scripts here may coordinate per-app checks without
duplicating the actual Laravel/Pest/Vite commands.
