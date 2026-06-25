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

## Active Implementation Work

- Decide when to remove or rewire the root Laravel app now that
  `apps/api-admin` and `apps/mobile-client` both exist as Laravel apps.
- Implement mobile bootstrap as the first real control-plane endpoint after the
  foundation status, contract catalogue, and mobile auth routes.
- Implement the first mobile bootstrap endpoint and call it after
  login/register/logout-sensitive state changes so mobile receives tenant,
  permission, feature, config, version, subscription, notification, and sync
  policy from Admin/API.
- Add tenancy, roles, permissions, feature flags, remote config, app version
  policy, maintenance mode, subscription state, notification policy, sync
  policy, and audit foundations.
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
