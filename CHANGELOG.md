# Changelog

All notable changes to Mobile Lara will be documented in this file.

## Unreleased

### Added

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
- Scaffolded `apps/api-admin` as a Laravel 13 API/admin app with Livewire,
  Blade, Tailwind, a versioned mobile status endpoint, shared mobile API
  response envelopes, focused Pest coverage, and verified frontend build.
