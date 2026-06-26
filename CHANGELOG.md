# Changelog

All notable changes to Mobile Lara will be documented in this file.

## Unreleased

### Added

- Added Sync Lifecycle Logic documentation for bootstrap sync, pull changes,
  push local changes, retries, conflict detection, conflict resolution,
  acknowledgement, sync status communication, manual sync, background sync,
  and admin monitoring of sync health.
- Added Offline-First Principles documentation for offline mobile capability,
  online-only API authority, cache rules, never-cache rules, offline state
  messaging, queued action logic, pending-change UX, and admin-controlled
  offline limits.
- Added Multi-Tenant Mobile Logic documentation for multiple-tenant choice,
  remembered current tenant context, safe tenant switching, tenant-scoped cache,
  per-tenant permissions and feature flags, sync after switch, offline behavior,
  and logout tenant cleanup.
- Added Tenant Admin Logic documentation for tenant-scoped controls,
  platform-only boundaries, invitations, delegated mobile-feature management,
  reports, support, admin safety, and cross-tenant isolation.
- Added Tenant Lifecycle Logic documentation for tenant creation, onboarding,
  trial, active, suspended, archived, billing-blocked, deletion/requested
  deletion, restore, and mobile-visible tenant state behavior.
- Added Data Privacy Principles documentation for tenant isolation, least
  privilege, secure local mobile data, secure native storage, exports,
  deletion, support access, admin visibility, privacy-by-default behavior, and
  mobile diagnostics privacy limits.
- Added Audit Logic documentation for audited admin actions, security events,
  support and compliance history, mobile activity representation, audit
  questions, and protected audit data principles.
- Added Role And Permission Logic documentation for platform-level,
  tenant-level, admin-user, and mobile-user permissions, API access, mobile UI
  visibility, feature flag interaction, and suspended user or tenant behavior.
- Added Mobile App Lock Principles documentation for lock timing,
  sensitive-area confirmation, biometric unlock, PIN unlock, repeated failed
  unlock handling, logout behavior, admin-disabled biometrics, and offline
  cached-data protection.
- Added Authentication Principles documentation for API-only mobile login,
  secure token handling, refresh sessions, logout, logout-all-devices, tenant
  selection after login, session expiry, offline authenticated behavior, and
  server revocation.
- Added Mobile Permission Logic documentation for pre-permission education,
  camera, microphone, location, notifications, files, scanner, biometrics,
  secure storage, disabled-feature prompts, denied-permission recovery,
  feature flag effects, and settings status.
- Added Mobile Settings Logic documentation for account, tenant, security,
  notifications, sync, appearance, permissions, storage, support, legal, and
  diagnostics sections, including local control, Admin/API authority, and
  offline-disabled behavior.
- Added Mobile Dashboard Logic documentation for current user context, current
  tenant, enabled feature shortcuts, sync/offline status, unread notifications,
  recent activity, announcements, quick actions, and state-based dashboard
  changes.
- Added Mobile App Shell Logic documentation for welcome, authenticated,
  locked, offline, maintenance, forced update, tenant switching,
  sync-in-progress, permission-blocked, and feature-disabled NativePHP shell
  states.
- Added Mobile UX Principles documentation for mobile-first navigation, simple
  screens, loading/offline states, thumb-friendly controls, minimum typing,
  fast actions, admin-rule-based feature visibility, secure session behavior,
  and native permission education.
- Added Admin Safety Principles documentation for dangerous admin actions,
  confirmations, audit history, impact previews, mobile impact previews,
  rollback, and tenant-isolated changes.
- Clarified Mobile Version Control Logic with a decision contract for minimum
  supported versions, optional updates, forced updates, maintenance mode,
  outdated API responses, store links, update messages, and broken old-version
  protection.
- Clarified Remote Configuration Logic with a decision contract for
  configurable behavior, mobile receive/cache rules, offline handling,
  tenant overrides, safe admin changes, and missing or invalid config.
- Clarified Feature Flag Logic with a decision contract for controlled mobile
  feature availability, global/tenant/user priority, disabled mobile states,
  admin impact, safe rollout, and plan-limit behavior.
- Clarified Admin Control Center Logic with a control ownership contract for
  tenants, users, roles, permissions, mobile features, remote config, app
  versions, maintenance mode, force update, sync behavior, notifications,
  reports, billing, and support.
- Clarified Documentation-First Architecture with a documentation-first contract
  for pre-implementation feature decisions, admin mobile effects, mobile screen
  API dependencies, sync online/offline behavior, permission ownership, and
  risk records.
- Clarified API-First Principles with an API-first contract for API-only mobile
  communication, predictable responses, explicit mobile-feature API purpose,
  permissions/feature/config/version/user context, mobile-friendly errors,
  sync/conflict behavior, and tenant-boundary protection.
- Clarified Mobile Client Responsibilities with a mobile ownership contract for
  mobile UX, secure local session, cache, offline actions, NativePHP device
  features, navigation, permissions UX, sync status display, local drafts,
  local user feedback, and admin-rule-based feature visibility.
- Clarified Admin/API Responsibilities with a responsibility ownership contract
  for tenant management, users and permissions, admin panel, API contracts,
  feature control, remote configuration, mobile version rules, notifications,
  billing/subscription logic, support operations, reporting, audit history,
  conflict decisions, and security enforcement.
- Clarified Two-System Boundary Logic with an explicit boundary contract for
  Admin/API ownership, mobile ownership, API-only behavior, local cache,
  remote admin control, and offline behavior.
- Clarified the SaaS Value Map with a value delivery contract that connects
  stakeholders to admin control, mobile access, offline sync, notifications,
  reports, security, feature flags, API outcomes, boundaries, and proof
  signals.
- Clarified Target User Roles documentation so platform owner, super admin,
  tenant admin, tenant manager, support agent, billing manager, mobile user,
  invited user, suspended user, and guest/pre-login user responsibilities,
  limits, visibility, and control boundaries stay explicit.
- Added Target User Roles decision rules for account-state precedence,
  tenant-scoped visibility, platform exceptions, mobile API-derived capability,
  and job-scoped support/billing access.
- Clarified Core Product Principles documentation so admin authority, API-only
  mobile behavior, feature control, tenant isolation, useful offline behavior,
  secure defaults, API-first communication, simple mobile UX, documentation-first
  development, and modular expansion stay explicit across the Markdown corpus.
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
- Implemented the API/admin mobile authentication foundation with registration,
  login, refresh-token rotation, logout, logout-all-devices, current-user, and
  profile endpoints, hashed token persistence, device sessions, validation
  envelopes, and security audit events.
- Added API/admin session authentication for platform-admin users, protected the
  admin dashboard, added admin login/logout routes and Blade view, and audited
  admin login/logout attempts.
- Added the mobile client auth API service boundary with configurable API base
  URL/timeouts, standard JSON/error handling, device context payloads,
  secure-token-store persistence, and focused coverage for login, register,
  refresh, current user, profile update, logout, and logout-all calls.
- Rewired mobile login, register, profile update, profile logout, sessions
  logout, and sessions logout-all Livewire flows to the mobile auth API service
  while keeping local Laravel sessions presentation-only.
- Implemented the first mobile bootstrap endpoint and mobile bootstrap service,
  returning authenticated user/device context plus explicit foundation defaults
  for pending SaaS modules and caching the envelope in mobile-local settings.
- Added the API/admin tenancy foundation with tenant and tenant-membership
  schema, public tenant IDs, mobile tenant list/switch endpoints, bootstrap
  tenant context, switch denial handling, and tenant switch audit events.
- Added the mobile tenant workspace foundation with cached bootstrap tenant
  context, an authenticated tenant API service, workspace settings UI, manual
  context refresh, tenant switching through API/admin, and focused coverage for
  routes, settings, API calls, and cache refresh behavior.
- Added the API/admin mobile permission foundation with a role-derived ability
  registry, nested bootstrap permission payloads, fail-closed invited/suspended
  membership behavior, and focused bootstrap permission coverage.
- Added the API/admin feature flag foundation with global defaults, tenant and
  user overrides, resolved `/features` API output, bootstrap integration,
  permission-gated feature states, and focused resolution-order coverage.
- Added the API/admin feature flag control page for audited global mobile
  defaults, including admin navigation, search, create/update validation, and
  focused Livewire coverage.
- Enforced feature minimum app-version gates so otherwise-enabled mobile
  features return `update_required` with an `update_app` action when the
  reported app build is too old.
- Added API/admin resource policies for current mobile control-plane resources,
  registered them explicitly, and wired Livewire edit/save/restore actions
  through policy authorization.
- Added the API/admin tenant feature override control page with tenant-scoped
  feature states, mobile impact preview, audited create/update, dashboard
  navigation, and restore-from-audit coverage.
- Added the API/admin user feature override control page with membership-safe
  user-specific feature states, mobile impact preview, audited create/update,
  dashboard navigation, and restore-from-audit coverage.
- Added the API/admin remote config foundation with global defaults,
  tenant-scoped overrides, resolved `/config` API output, bootstrap
  integration, freshness metadata, deterministic config versions, and focused
  contract coverage.
- Added the API/admin remote config control page with JSON-object validation,
  mobile impact preview, audited create/update, dashboard navigation, and
  restore-from-audit coverage.
- Added the API/admin tenant remote config override page with tenant-scoped
  JSON-object validation, mobile impact preview, audited create/update,
  dashboard navigation, and restore-from-audit coverage.
- Added the API/admin app version and maintenance foundation with
  minimum-version force updates, optional update states, public `/app-version`
  API output, bootstrap maintenance integration, and focused policy coverage.
- Added the API/admin app version policy control page with confirmation,
  mobile impact preview, audited create/update, dashboard navigation, and
  restore-from-audit coverage.
- Extended app-version policy control with tenant and rollout-cohort scopes,
  trusted bootstrap precedence for tenant policies, public cohort checks through
  `X-Mobile-Cohort`, scoped audit snapshots, and focused resolver coverage.
- Added app-version range controls so admins can target scoped policies to
  specific reported build ranges, with deterministic resolver precedence,
  audited range snapshots, Livewire validation, and focused coverage.
- Added feature flag plan and device gates with global required-plan/device
  constraints, resolver-enforced blocked states, admin controls, audit
  snapshots, and focused API/bootstrap coverage.
- Added a fail-closed emergency feature gate so global, tenant, or user
  emergency-disabled states cannot be bypassed by lower-scope overrides.
- Added feature flag cohort gates with admin-managed allowed cohorts,
  resolver-enforced rollout blocking, API cohort context, and focused coverage.
