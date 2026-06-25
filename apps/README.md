# Apps

This directory is the target monorepo boundary for the two Mobile Lara systems.

Product Vision is defined in `../docs/product-vision.md`: both apps exist to
support remote control with local resilience, where Admin/API owns authority and
mobile owns local execution.

Product Positioning is defined in `../docs/product-positioning.md`: the two apps
must remain one SaaS control center plus one managed mobile workforce/client
platform.

Core Product Principles are defined in `../docs/product-principles.md`: both
apps must preserve admin authority, API-only mobile behavior, feature control,
tenant isolation, useful offline behavior, secure defaults, simple mobile UX,
documentation-first planning, and modular expansion.

Target User Roles are defined in `../docs/user-roles.md`: both apps must keep
platform owner, super admin, tenant admin, tenant manager, support agent,
billing manager, mobile user, invited user, suspended user, and guest/pre-login
boundaries explicit.

SaaS Value Map is defined in `../docs/saas-value-map.md`: both apps must connect
admin control, mobile access, offline sync, notifications, reports, security,
and feature flags to explicit stakeholder value.

## Systems

| Path | Responsibility | Current state |
| --- | --- | --- |
| `apps/api-admin` | Laravel API plus Livewire admin panel. This is the SaaS control plane and source of authority. | Laravel app scaffold exists with Livewire dashboard shell, versioned status API, shared response envelope, tests, and build verification. |
| `apps/mobile-client` | Laravel plus Livewire inside NativePHP Mobile. This is the managed edge client. | Complete mobile app exists with Livewire routes, NativePHP config, local SQLite infrastructure, copied mobile UI, tests, and build verification. |

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
- Remote Configuration Logic in `../docs/remote-configuration-logic.md`
  defines how safe runtime mobile behavior is remotely configured, delivered
  through API, cached locally, handled offline, overridden per tenant, and
  failed closed when missing or invalid.
- Mobile Version Control Logic in
  `../docs/mobile-version-control-logic.md` defines how minimum supported
  versions, optional updates, forced updates, maintenance mode, store links,
  update messages, and stale-client protection move through API outcomes.
- Mobile owns local execution, NativePHP capability UX, cache, drafts, queues,
  sync display, and API-derived feature visibility.
- Mobile never reads the admin database directly. Server-trusted behavior must
  move through versioned API contracts.

## Transition Rule

The root Laravel app is retained as a transition mirror. New control-plane code
belongs in `apps/api-admin`. New mobile-client code should target
`apps/mobile-client` unless a later cleanup task explicitly removes or rewires
the root app.
