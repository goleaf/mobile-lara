# Apps

This directory is the target monorepo boundary for the two Mobile Lara systems.

## Systems

| Path | Responsibility | Current state |
| --- | --- | --- |
| `apps/api-admin` | Laravel API plus Livewire admin panel. This is the SaaS control plane and source of authority. | Scaffold documentation exists; Laravel app implementation is pending. |
| `apps/mobile-client` | Laravel plus Livewire inside NativePHP Mobile. This is the managed edge client. | Scaffold documentation exists; current mobile implementation still lives at the repository root during Phase 1 transition. |

The product contract remains unchanged:

- Admin/API owns tenant, user, permission, feature, config, version, billing,
  notification, support, reporting, audit, conflict, and security authority.
- Admin Control Center logic in `../docs/admin-control-center-logic.md`
  defines how tenant, user, role, permission, feature, config, version,
  maintenance, force update, sync, notification, report, billing, and support
  controls are scoped, authorized, audited, and sent to mobile through API
  outcomes.
- Feature Flag Logic in `../docs/feature-flag-logic.md` defines how important
  mobile features resolve global, tenant, plan, role, permission, user,
  app-version, device, cohort, maintenance, and emergency decisions into
  mobile-safe states.
- Mobile owns local execution, NativePHP capability UX, cache, drafts, queues,
  sync display, and API-derived feature visibility.
- Mobile never reads the admin database directly. Server-trusted behavior must
  move through versioned API contracts.

## Transition Rule

Until the code migration is completed, the root Laravel app is treated as the
existing mobile-client implementation. New control-plane code belongs in
`apps/api-admin` once that Laravel app is scaffolded. New mobile-client code
should be added to the current root app only when it is required to preserve or
stabilize existing mobile functionality before the move.
