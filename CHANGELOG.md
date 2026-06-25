# Changelog

All notable changes to Mobile Lara will be documented in this file.

## Unreleased

### Added

- Clarified Product Positioning documentation so the SaaS control center,
  mobile workforce/client platform, API-first system, offline-capable mobile
  system, feature-controlled platform, tenant-based product, and web-only vs
  mobile-only tradeoffs stay explicit across the Markdown corpus.
- Clarified Product Vision documentation so the SaaS problem, admin users,
  mobile users, two-system need, admin-controlled mobile behavior,
  NativePHP + Livewire rationale, and SaaS scalability logic stay explicit.
- Added Mobile Version Control Logic documentation for minimum supported
  versions, optional updates, forced updates, maintenance mode, outdated app
  behavior, store links, update messages, and protection from broken old
  versions.
- Created the implementation status checklist that maps all documented SaaS,
  API/admin, mobile, NativePHP, offline/sync, support, billing, reports, and
  quality-loop requirements to current implementation state.
- Added Phase 1 monorepo boundary documentation for `apps/api-admin`,
  `apps/mobile-client`, API contracts, root scripts, and remaining tasks.
- Added Admin Control Center logic documentation for scoped, authorized,
  auditable control of tenants, users, roles, permissions, mobile features,
  remote config, app versions, maintenance, force update, sync, notifications,
  reports, billing, and support.
- Added Feature Flag Logic documentation for global, tenant, plan, role,
  permission, user, app-version, device, cohort, maintenance, and emergency
  feature decisions.
- Added Remote Configuration Logic documentation for configurable behavior,
  mobile receive/cache rules, offline behavior, tenant overrides, safe admin
  changes, and missing or invalid config handling.
- Scaffolded `apps/api-admin` as a Laravel 13 API/admin app with Livewire,
  Blade, Tailwind, a versioned mobile status endpoint, shared mobile API
  response envelopes, focused Pest coverage, and verified frontend build.
- Copied the verified root NativePHP mobile client into `apps/mobile-client`
  as a standalone Laravel app with Livewire routes, NativePHP config, local
  SQLite infrastructure, mobile UI surfaces, tests, frontend build, and plugin
  validation.
- Added v1 mobile API contract documentation for auth, bootstrap, tenancy,
  features, remote config, app version/maintenance, records, sync,
  notifications, support, billing, reports, and diagnostics, plus an
  implemented contract catalogue endpoint at `GET /api/v1/mobile/contracts`.
