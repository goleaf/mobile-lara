# Remaining Tasks

Updated: 2026-06-26

This file tracks active work left after the current implementation pass. It is
not a substitute for `docs/implementation-status.md`; the status checklist is
the source of truth for feature state.

Product Vision is defined in `docs/product-vision.md`. Remaining work should
protect the vision before adding implementation scope.

Product Positioning is defined in `docs/product-positioning.md`. Remaining work
should preserve the SaaS control center plus mobile workforce/client platform
position before adding implementation scope.

Core Product Principles are defined in `docs/product-principles.md`. Remaining
work must preserve admin authority, API-only mobile behavior, feature control,
tenant isolation, useful offline behavior, secure defaults, simple mobile UX,
documentation-first planning, and modular expansion.

API-First Principles are defined in `docs/api-first-principles.md`. Remaining
work must name API-only communication, response predictability, feature API
purpose, operating context, mobile-friendly errors, sync/conflict behavior, and
tenant-boundary protection before endpoint or mobile-screen scope is added.

Target User Roles are defined in `docs/user-roles.md`. Remaining work must map
platform owner, super admin, tenant admin, tenant manager, support agent,
billing manager, mobile user, invited user, suspended user, and guest/pre-login
behavior before adding implementation scope.

SaaS Value Map is defined in `docs/saas-value-map.md`. Remaining work must map
platform owner, tenant business, tenant admin, mobile worker/client, support
team, and billing/operations value before adding implementation scope.

Two-System Boundary Logic is defined in `docs/two-system-boundary.md`.
Remaining work must map what Admin/API owns, what mobile owns, what must go
through API, what may be cached locally, what admin controls remotely, and what
happens offline before adding implementation scope.

Admin/API Responsibilities are defined in
`docs/admin-api-responsibilities.md`. Remaining work must name the
control-plane responsibility owner for tenant, user, permission, API, feature,
config, version, notification, billing, support, report, audit, conflict, or
security behavior before implementation scope is added.

Mobile Client Responsibilities are defined in
`docs/mobile-client-responsibilities.md`. Remaining work must name the mobile
responsibility owner for UX, secure local session, cache, offline action,
NativePHP capability, navigation, permissions UX, sync display, draft,
feedback, or feature visibility before implementation scope is added.

Mobile UX Principles are defined in `docs/mobile-ux-principles.md`. Remaining
mobile work must map NativePHP navigation, simple screens, clear
loading/offline states, thumb-friendly controls, minimum typing, fast actions,
feature visibility, secure sessions, and native permission prompts before
implementation scope is added.

Mobile App Shell Logic is defined in `mobile-app-shell-logic.md`:
shell states must coordinate welcome, authenticated, locked, offline, maintenance, forced update, tenant
switching, sync-in-progress, permission-blocked, and feature-disabled behavior
before implementation.

Mobile Dashboard Logic is defined in `mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Mobile Settings Logic is defined in `mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

Authentication Principles are defined in `authentication-principles.md`:
mobile login must happen through the API only; access and refresh tokens must
use secure storage; refresh, logout, logout-all-devices, tenant selection,
session expiry, offline already-authenticated behavior, and server revocation
must preserve Admin/API authority before implementation.

Mobile App Lock Principles are defined in `mobile-app-lock-principles.md`:
the mobile client must lock on security-sensitive lifecycle, timeout,
account, tenant, offline-cache, and admin-policy conditions; require
confirmation for sensitive areas; use biometric or PIN unlock only as local
protection; handle failed attempts, logout, admin-disabled biometrics, and
offline cached data without bypassing Admin/API authority.

Role And Permission Logic is defined in `role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact
with feature flags as separate gates; suspended users and suspended tenants
fail closed without bypassing tenant isolation.

Audit Logic is defined in `audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

Data Privacy Principles are defined in `data-privacy-principles.md`:
tenant isolation, least privilege, secure local mobile data, secure native
storage, export and deletion boundaries, support access limits, admin
visibility boundaries, privacy-by-default behavior, and mobile diagnostics
privacy limits must protect users and tenants without turning mobile cache,
support views, or audit history into uncontrolled data exposure.

Tenant Lifecycle Logic is defined in `tenant-lifecycle-logic.md`:
tenant creation, onboarding, trial, active, suspended, archived,
billing-blocked, deletion/requested deletion, and restore states must be
Admin/API-owned lifecycle decisions that mobile presents as safe,
tenant-scoped, billing-aware, supportable states without inventing local
tenant authority.

Tenant Admin Logic is defined in `tenant-admin-logic.md`:
tenant admins may manage tenant-scoped users, invitations, delegated settings,
delegated mobile-feature controls, tenant reports, and tenant support workflows
only inside their tenant; platform-only controls, cross-tenant visibility,
global policy, billing authority, lifecycle authority, app-version policy, and
security posture remain Admin/API-owned boundaries.

Multi-Tenant Mobile Logic is defined in `multi-tenant-mobile-logic.md`:
users with more than one tenant choose and remember tenant context through
API-confirmed state; tenant switching, tenant-scoped cache, per-tenant
permissions and feature flags, sync replay, offline behavior, and logout
cleanup must preserve tenant isolation and never turn mobile-local state
into tenant authority.

Offline-First Principles are defined in `offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

Offline UX Logic is defined in `offline-ux-logic.md`:
offline UX must calmly explain banners, disabled online-only actions,
local drafts, pending indicators, retry, sync success or failure,
saved-local versus synced state, and data-loss prevention whenever
connection changes.

Records/Content Module Logic is defined in `records-content-module-logic.md`:
records are tenant-scoped business content with API-owned lifecycle,
notes, attachments, activity, tags, categories, status, offline draft or
sync behavior, admin controls, permissions, feature flags, audit, and
reporting boundaries.

Search Logic is defined in `search-logic.md`:
search stays tenant-scoped, permission-aware, feature-controlled,
privacy-preserving, and explicit about local-cache limits versus
API-authoritative results, including recent searches, saved filters, filtering,
sorting, scan-to-search, offline limits, and admin-controlled boundaries.

Forms And Drafts Logic is defined in `forms-drafts-logic.md`:
mobile forms must stay simple, validated, autosave-aware, offline-draft
safe, API-submitted, admin-controlled, and explicit about local-save versus
server-accepted state so user work is protected without bypassing authority.

Notifications Logic is defined in `notifications-logic.md`:
notification targeting, delivery policy, push behavior, in-app inbox,
read/unread state, deep links, preferences, offline behavior, and tenant or
permission boundaries must remain Admin/API-authoritative and mobile-safe.

Support System Logic is defined in `support-system-logic.md`:
support requests, support messages, attachments, tenant context, support-agent
visibility, audit, notifications, and offline support drafts must remain
tenant-scoped, least-privilege, privacy-safe, and Admin/API-authoritative.

Billing And Plan Logic is defined in `billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature states,
and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

Reporting Logic is defined in `reporting-logic.md`:
admin measurements, tenant-admin measurements, mobile-visible summaries,
privacy boundaries, date ranges, exports, feature usage, sync health,
notification, support, and billing reports must remain tenant-scoped,
permission-aware, privacy-safe, auditable, and Admin/API-authoritative.

Native Feature Strategy is defined in `native-feature-strategy.md`:
NativePHP capability use, logical service boundaries, browser/development
fallbacks, permission education, admin feature-flag control, native failure
UX, and offline sync behavior must remain feature-scoped, tenant-safe,
privacy-aware, fallback-safe, and Admin/API-authoritative.

Camera And Media Logic is defined in `camera-media-logic.md`:
photo capture, media selection, media preview, record/support attachments,
offline media storage, upload queues, feature-flag control, permission
denial, size limits, and privacy behavior must remain tenant-scoped,
permission-aware, fallback-safe, queue-safe, privacy-safe, and
Admin/API-authoritative.

Scanner Logic is defined in `scanner-logic.md`:
QR/barcode scan-to-search, scan-to-create, scan-to-validate, scan history,
offline scanning, invalid scan handling, duplicate scan handling, admin
feature flags, and camera/permission dependency behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear,
duplicate-safe, privacy-safe, and Admin/API-authoritative.

Geolocation Logic is defined in `geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Sync Lifecycle Logic is defined in `sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

Admin Control Center logic is defined in
`docs/admin-control-center-logic.md`. Remaining implementation work must map
tenant, user, role, permission, mobile feature, remote config, app version,
maintenance, force update, sync, notification, report, billing, and support
controls to that document before code is written.

Feature Flag Logic is defined in `docs/feature-flag-logic.md`. Remaining
implementation work must map important mobile features to documented global,
tenant, plan, role, permission, user, app-version, device, cohort,
maintenance, emergency, disabled-state, rollout, and plan-limit decisions.

Remote Configuration Logic is defined in `docs/remote-configuration-logic.md`.
Remaining runtime-config work must map configurable behavior to documented
scope, safe defaults, mobile caching, offline behavior, validation, fallback,
support visibility, audit, and rollback.

Mobile Version Control Logic is defined in
`docs/mobile-version-control-logic.md`. Remaining version/maintenance work must
map minimum supported versions, optional updates, forced updates, maintenance
mode, outdated responses, store links, update messages, support context, audit,
rollback, and old-version protection.

Admin Safety Principles are defined in `docs/admin-safety-principles.md`.
Remaining admin-control work must map dangerous actions to confirmation, audit
history, impact preview, mobile impact preview, rollback, and tenant isolation
before implementation scope is added.

## Active Implementation Work

- Decide when to remove or rewire the root Laravel app now that
  `apps/api-admin` and `apps/mobile-client` both exist as Laravel apps.
- Complete tenancy beyond the foundation tenant list/switch API and mobile
  workspace switcher: admin tenant management screens, invitations, tenant
  settings policy, tenant-scoped resource middleware, and tenant-local cache
  partition verification.
- Complete admin role/permission management, admin billing management, push
  notification inbox/token workflows, sync policy, and audit foundations.
- Continue rewiring lower-level mobile-local actions so server-trusted behavior
  comes from API/bootstrap state instead of local placeholders. Primary
  navigation, dashboard/create/search shortcuts, and direct module routes now
  use cached Admin/API policy, and the permissions center blocks native prompts
  for disabled features. Record create/update/archive/delete and bulk local
  mutations now deny direct Livewire calls before SQLite writes. Attachment
  management and attachment sharing now gate local writes and native share
  handoff. Voice-note recording, native callbacks, local save/delete actions,
  and upload queue placeholders now gate cached microphone and sync policy
  before local writes, file deletes, or queue writes. Native location
  permission/current-position calls, callbacks, check-in creation, and
  check-in history shortcuts now gate cached location and sync policy before
  native handoff or local check-in writes. Media capture/file-manager actions,
  scanner result mutations, lower-level NativePHP service calls, and remaining
  offline queue writes still need per-action gates.
- Run formatting, tests, route verification, builds, and NativePHP validation
  after each implementation slice.

## Known External Blockers

- iOS simulator verification requires Xcode and available simulators.
- Android emulator verification requires Android Studio/SDK, Gradle, and an
  emulator image available to NativePHP.

## Future Enhancements

- Optional modules such as field service, logistics, ecommerce, booking,
  education, events, messaging/community, and AI assistant should remain
  unimplemented until a project Markdown file explicitly makes them part of the
  product scope.
