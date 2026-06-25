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
  flags, and explicit foundation defaults for pending config, subscription,
  notification, and sync modules.
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
  tenant override, global default, and permission-gate outcomes into
  mobile-safe feature states.
- `GET /admin/login` renders the admin login form.
- `POST /admin/login` authenticates platform-admin users.
- `POST /admin/logout` invalidates the admin session.
- `/admin/dashboard` is protected by session auth and platform-admin access.
- `/admin/mobile/features` is protected by session auth and platform-admin
  access, and manages audited global mobile feature defaults.
- `App\Actions\Admin\SaveMobileFeatureFlagAction` persists feature defaults and
  writes before/after audit metadata to `security_audit_events`.
- `App\Support\Api\MobileApiResponse` centralizes success and error envelopes.
- `App\Support\Api\MobileContractRegistry` centralizes documented contract
  groups and planned routes.
- Mobile access and refresh tokens are stored only as SHA-256 hashes.
- `security_audit_events` records mobile auth/security actions.
- Blade layouts exist for admin, auth, and dashboard surfaces.
- Reusable admin Blade components exist for section headings and status badges.
- Pest tests cover the dashboard route, root redirect, feature flag admin
  controls, success envelope, error envelope, contract catalogue, and contract
  Markdown file coverage.

Still pending:

- Admin tenant management, invitations, full permission management UI,
  resource policies, and broader control-plane audit.
- Tenant/user feature override UI, feature impact previews, remote config, app
  versions, sync, notifications, records/content, support, billing, and reports.
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
