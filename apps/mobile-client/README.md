# Mobile Client App

`apps/mobile-client` is the target home for the Laravel + Livewire + NativePHP
mobile client.

## Product Role

This system owns local execution:

- mobile-first Livewire screens and Blade components
- NativePHP service wrappers and permission UX
- secure local session presentation
- local SQLite cache, drafts, queued intents, and sync metadata
- offline banners, pending/conflict state, and user feedback
- API-derived feature visibility and blocked/disabled/update-required states

The mobile client must never own tenant authority, permission authority,
billing authority, feature flag authority, app-version authority, server audit
truth, or final conflict decisions.

Admin Control Center logic in `../../docs/admin-control-center-logic.md`
defines the server-side controls that mobile receives as API outcomes:
tenant, user, role, permission, feature, remote config, app version,
maintenance, force update, sync, notification, report, billing, and support
state.

Feature Flag Logic in `../../docs/feature-flag-logic.md` defines the mobile
states the client should receive from API: hidden, visible, disabled, blocked,
beta, deprecated, update-required, offline-limited, or emergency-disabled.

## Current Phase 1 State

The existing mobile implementation still lives at the repository root. It
already contains many mobile screens, NativePHP wrappers, local models,
repositories, migrations, and tests. Moving it into this directory is a
dedicated implementation task because path changes affect Composer autoloading,
Vite, NativePHP generated assets, database paths, tests, and Laravel Boost.

Until that move is complete, treat this directory as the documented target
boundary and the root app as the working mobile-client codebase.
