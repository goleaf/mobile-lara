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
- Replace the remaining mobile bootstrap foundation defaults with real
  subscription, notification, and sync policy modules.
- Complete tenancy beyond the foundation tenant list/switch API and mobile
  workspace switcher: admin tenant management screens, invitations, tenant
  settings policy, tenant-scoped resource middleware, and tenant-local cache
  partition verification.
- Complete admin role/permission management, resource policies, feature-flag
  plan/device gates, app-version range scoping, subscription state,
  notification policy, sync policy, and audit foundations.
- Rewire existing mobile-local screens so server-trusted behavior comes from
  API/bootstrap state instead of local placeholders.
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
