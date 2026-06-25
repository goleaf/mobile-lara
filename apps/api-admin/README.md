# API/Admin App

`apps/api-admin` is the target home for the Laravel API and Livewire admin
control plane.

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

## Current Phase 1 State

The directory exists so the monorepo boundary is explicit. The Laravel app,
admin routes, versioned API routes, schemas, policies, Livewire admin screens,
and tests are pending implementation.

Before implementing endpoints or screens, update the relevant contract in
`contracts/api` and keep `docs/implementation-status.md` accurate.
