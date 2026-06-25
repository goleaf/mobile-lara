# Remaining Tasks

Updated: 2026-06-25

This file tracks active work left after the current implementation pass. It is
not a substitute for `docs/implementation-status.md`; the status checklist is
the source of truth for feature state.

Admin Control Center logic is defined in
`docs/admin-control-center-logic.md`. Remaining implementation work must map
tenant, user, role, permission, mobile feature, remote config, app version,
maintenance, force update, sync, notification, report, billing, and support
controls to that document before code is written.

Feature Flag Logic is defined in `docs/feature-flag-logic.md`. Remaining
implementation work must map important mobile features to documented global,
tenant, plan, role, permission, user, app-version, device, cohort,
maintenance, emergency, disabled-state, rollout, and plan-limit decisions.

## Active Implementation Work

- Move from the current root Laravel mobile app to the requested monorepo shape
  with `apps/api-admin` and `apps/mobile-client`.
- Scaffold the API/Admin Laravel app and implement the first versioned mobile
  API route group.
- Add the shared mobile API response and error envelope.
- Write the first concrete API contracts in `contracts/api`.
- Implement mobile bootstrap as the first control-plane endpoint.
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
