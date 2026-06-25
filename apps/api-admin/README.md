# API/Admin App

`apps/api-admin` is the target home for the Laravel API and Livewire admin
control plane.

Product Vision is defined in `../../docs/product-vision.md`: this app exists
because SaaS authority, tenant control, support, billing, version policy,
reports, audit, and sync decisions must be centralized.

Product Positioning is defined in `../../docs/product-positioning.md`: this app
is the SaaS control center side of the product, not a generic admin dashboard.

Core Product Principles are defined in `../../docs/product-principles.md`: this
app must keep authority server-side, tenant-scoped, feature-controlled,
secure-by-default, API-first, documented, and modular.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this app must expose mobile behavior only
through predictable API contracts that return user context, permissions,
feature flags, config, version rules, mobile-friendly errors, sync/conflict
outcomes, and tenant-safe responses.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: this app must not add admin
controls, API behavior, permissions, sync/conflict decisions, or mobile effects
until feature purpose, mobile impact, API dependency, permission ownership, and
risk are documented.

Target User Roles are defined in `../../docs/user-roles.md`: this app must map
each admin, support, billing, tenant, mobile, invited, suspended, and
guest/pre-login responsibility to server-side authority and visibility.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: this app must make
stakeholder value operable through admin controls, API contracts, reports,
security, billing/operations context, support context, notifications, and
feature flags.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: this app owns the trusted side of the
boundary and must expose mobile-safe outcomes only through API contracts.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this app owns tenant management,
users and permissions, admin operations, API contracts, feature/config/version
control, notifications, billing, support, reporting, audit, conflicts, and
security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this app must expose API
outcomes that let mobile own UX, local session presentation, cache, offline
actions, NativePHP capability UX, navigation, permissions UX, sync display,
drafts, local feedback, and feature visibility without granting local
authority.

Mobile UX Principles are defined in `../../docs/mobile-ux-principles.md`: this
app must return API outcomes that the NativePHP client can present as simple
navigation, loading/offline states, thumb-friendly actions, secure session
states, feature visibility, and native permission education.

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

## Product Role

This system owns SaaS authority:

- tenants and tenant lifecycle
- users, roles, permissions, invitations, sessions, and devices
- feature flags and tenant/user overrides
- remote config and app version policy
- maintenance mode and force update rules
- notifications and push registration policy
- records/content API authority
- offline sync acceptance, replay windows, and conflict decisions
- support, billing, reports, audit, and security enforcement

Admin Control Center logic is defined in
`../../docs/admin-control-center-logic.md`. Future API/Admin implementation
must map each tenant, user, role, permission, mobile feature, remote config,
app version, maintenance, force update, sync, notification, report, billing,
and support control to that document before code is written.

Feature Flag Logic is defined in `../../docs/feature-flag-logic.md`. Future
API/Admin implementation must resolve global, tenant, plan, role, permission,
user, app-version, device, cohort, maintenance, and emergency feature decisions
into API outcomes before mobile uses them.

Remote Configuration Logic is defined in
`../../docs/remote-configuration-logic.md`. Future API/Admin implementation
must validate, scope, version, audit, and safely expose resolved mobile config
without letting config become authorization, billing, tenant, or permission
authority.

Mobile Version Control Logic is defined in
`../../docs/mobile-version-control-logic.md`. Future API/Admin implementation
must resolve minimum supported versions, optional updates, forced updates,
maintenance mode, store links, update messages, and outdated-client protection
into mobile-safe API outcomes.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`. Future API/Admin implementation must
confirm, audit, impact-preview, mobile-preview, rollback-plan, and
tenant-isolate dangerous admin actions before those controls affect mobile
users or tenants.

Mobile UX Principles are defined in `../../docs/mobile-ux-principles.md`.
Future API/Admin implementation must expose mobile-safe states that support
simple NativePHP navigation, clear loading/offline behavior, fast actions,
secure sessions, and native permission education.

## Current Implementation State

This directory is now a Laravel 13 application with a Livewire admin dashboard
shell, the first versioned mobile API route, and a public mobile contract
catalogue. It also has admin session authentication and the first mobile API
authentication foundation.

Implemented foundation:

- `GET /admin/dashboard` renders `App\Livewire\Admin\Dashboard`.
- `/` redirects to `/admin/dashboard`.
- `GET /api/v1/mobile/status` returns the standard mobile success envelope.
- `GET /api/v1/mobile/contracts` returns the v1 mobile contract catalogue.
- `POST /api/v1/mobile/auth/register` creates a mobile user, device session,
  access token, refresh token, and audit event.
- `POST /api/v1/mobile/auth/login` creates a revocable mobile token set.
- `POST /api/v1/mobile/auth/refresh` rotates refresh/access tokens.
- `POST /api/v1/mobile/auth/logout` revokes the current device session.
- `POST /api/v1/mobile/auth/logout-all` revokes all active mobile sessions for
  the current user.
- `GET /api/v1/mobile/auth/user` returns current user/session context.
- `PATCH /api/v1/mobile/auth/profile` updates allowed profile fields.
- `GET /api/v1/mobile/bootstrap` returns the first authenticated mobile
  operating context with real user, device-session, current tenant, and
  available tenant data, role-derived permission payloads, resolved feature
  flags, resolved remote config, and explicit foundation defaults for pending
  subscription, notification, and sync modules.
- `GET /api/v1/mobile/app-version` returns resolved app-version, update, and
  maintenance policy for the reported mobile platform/version context.
- `GET /api/v1/mobile/config` returns resolved mobile-safe remote config with
  tenant context, freshness metadata, compatibility state, and deterministic
  config version metadata.
- `GET /api/v1/mobile/features` returns resolved mobile-safe feature outcomes
  for the current user and tenant through the standard response envelope.
- `GET /api/v1/mobile/tenants` returns the authenticated user's tenant context.
- `POST /api/v1/mobile/tenants/current` switches the current tenant after
  membership/lifecycle checks and records a security audit event.
- `App\Services\MobilePermissions\MobilePermissionResolver` derives nested
  mobile permission state from the current active tenant role and fails closed
  when the user has only invited, suspended, or unavailable memberships.
- `mobile_feature_flags`, `tenant_feature_overrides`, and
  `user_feature_overrides` provide the first feature flag data model.
- `App\Services\MobileFeatures\MobileFeatureResolver` resolves user override,
  tenant override, global default, plan-gate, device-gate, permission-gate,
  and minimum-app-version outcomes into mobile-safe feature states.
- `mobile_remote_configs` and `tenant_remote_config_overrides` provide the
  first remote config data model.
- `App\Services\MobileConfig\MobileRemoteConfigResolver` merges foundation,
  global, and tenant config into resolved mobile-safe config payloads.
- `mobile_app_version_policies` provides the first scoped version and
  maintenance policy data model for global/platform, tenant, and cohort
  decisions.
- `App\Services\MobileVersion\MobileAppVersionPolicyResolver` resolves
  supported, optional-update, force-update, blocked, and maintenance outcomes
  from tenant, cohort, platform, and global fallback policy order.
- Policies are registered for current mobile control-plane resources:
  feature flags, tenant/user feature overrides, remote config, tenant remote
  config overrides, and app-version policies.
- `GET /admin/login` renders the admin login form.
- `POST /admin/login` authenticates platform-admin users.
- `POST /admin/logout` invalidates the admin session.
- `/admin/dashboard` is protected by session auth and platform-admin access.
- `/admin/mobile/features` is protected by session auth and platform-admin
  access, and manages audited global mobile feature defaults, plan gates, and
  device constraints.
- `/admin/mobile/feature-overrides` is protected by session auth and
  platform-admin access, and manages audited tenant-scoped mobile feature
  overrides with confirmation, impact preview, and audit-history restore.
- `/admin/mobile/user-feature-overrides` is protected by session auth and
  platform-admin access, and manages audited user-scoped mobile feature
  overrides with tenant membership validation and audit-history restore.
- `/admin/mobile/config` is protected by session auth and platform-admin
  access, and manages audited global mobile remote config defaults with JSON
  validation, impact preview, and audit-history restore.
- `/admin/mobile/tenant-config` is protected by session auth and platform-admin
  access, and manages audited tenant remote config overrides with JSON
  validation, impact preview, and audit-history restore.
- `/admin/mobile/app-versions` is protected by session auth and platform-admin
  access, and manages audited global/platform, tenant, and cohort mobile
  version policies with version-range targeting, confirmation, impact preview,
  and audit-history restore.
- `App\Actions\Admin\SaveMobileFeatureFlagAction` persists feature defaults and
  writes before/after audit metadata to `security_audit_events`.
- `App\Actions\Admin\SaveTenantFeatureOverrideAction` persists tenant feature
  overrides and writes before/after audit metadata to `security_audit_events`.
- `App\Actions\Admin\SaveUserFeatureOverrideAction` persists user feature
  overrides and writes before/after audit metadata to `security_audit_events`.
- `App\Actions\Admin\SaveMobileRemoteConfigAction` persists global config
  defaults and writes before/after audit metadata to `security_audit_events`.
- `App\Actions\Admin\SaveTenantRemoteConfigOverrideAction` persists tenant
  config overrides and writes before/after audit metadata to
  `security_audit_events`.
- `App\Actions\Admin\SaveMobileAppVersionPolicyAction` persists scoped version
  policies and writes before/after audit metadata to `security_audit_events`.
- `App\Support\Api\MobileApiResponse` centralizes success and error envelopes.
- `App\Support\Api\MobileContractRegistry` centralizes documented contract
  groups and planned routes.
- Mobile access and refresh tokens are stored only as SHA-256 hashes.
- `security_audit_events` records mobile auth/security actions.
- Blade layouts exist for admin, auth, and dashboard surfaces.
- Reusable admin Blade components exist for section headings and status badges.
- Pest tests cover the dashboard route, root redirect, feature flag admin
  controls, tenant and user feature override controls, remote config admin
  controls, tenant remote config controls, app version admin controls, remote
  config resolution, feature plan/device/app-version gates, tenant/cohort app
  version policy, app-version range resolution, resource policies, success
  envelope, error envelope, contract catalogue, and contract Markdown file
  coverage.

Still pending:

- Admin tenant management, invitations, full permission management UI,
  broader resource policy expansion, and broader control-plane audit.
- Sync, notifications, records/content, support, billing, and reports.
- Protected domain routes for records/content, sync, notifications, support,
  billing, reports, diagnostics, and feature/config/version policies.

Verification commands for this app:

```bash
composer validate --strict
php artisan route:list --except-vendor
php artisan test --compact
vendor/bin/pint --dirty --format agent
npm run build
```

Before implementing endpoints or screens, update the relevant contract in
`contracts/api` and keep `docs/implementation-status.md` accurate.
