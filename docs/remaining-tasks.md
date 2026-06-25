# Remaining Tasks

Updated: 2026-06-25

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
  foundation status and contract catalogue routes.
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
