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

Target User Roles are defined in `../../docs/user-roles.md`: this app must map
each admin, support, billing, tenant, mobile, invited, suspended, and
guest/pre-login responsibility to server-side authority and visibility.

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

## Current Phase 5 State

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
- `GET /admin/login` renders the admin login form.
- `POST /admin/login` authenticates platform-admin users.
- `POST /admin/logout` invalidates the admin session.
- `/admin/dashboard` is protected by session auth and platform-admin access.
- `App\Support\Api\MobileApiResponse` centralizes success and error envelopes.
- `App\Support\Api\MobileContractRegistry` centralizes documented contract
  groups and planned routes.
- Mobile access and refresh tokens are stored only as SHA-256 hashes.
- `security_audit_events` records mobile auth/security actions.
- Blade layouts exist for admin, auth, and dashboard surfaces.
- Reusable admin Blade components exist for section headings and status badges.
- Pest tests cover the dashboard route, root redirect, success envelope, error
  envelope, contract catalogue, and contract Markdown file coverage.

Still pending:

- Tenant scoping, roles, permissions, policies, and broader control-plane audit.
- Domain modules for feature flags, remote config, app versions, sync,
  notifications, records/content, support, billing, and reports.
- Implemented mobile bootstrap endpoint and protected domain routes.

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
