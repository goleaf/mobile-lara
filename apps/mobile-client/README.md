# Mobile Client App

`apps/mobile-client` is the target home for the Laravel + Livewire + NativePHP
mobile client.

Product Vision is defined in `../../docs/product-vision.md`: this app exists as
the managed NativePHP + Livewire execution surface for mobile users, not as a
source of SaaS authority.

Product Positioning is defined in `../../docs/product-positioning.md`: this app
is the mobile workforce/client platform side of the product, not an independent
mobile authority.

Core Product Principles are defined in `../../docs/product-principles.md`: this
app must never bypass API authority, must keep mobile UX simple, and must treat
offline state as cache, draft, queue, pending, synced, conflict, or failed
state rather than server truth.

Target User Roles are defined in `../../docs/user-roles.md`: this app must show
mobile, invited, suspended, and guest/pre-login states as API-derived UX, not
local permission authority.

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

Remote Configuration Logic in `../../docs/remote-configuration-logic.md`
defines how the client receives resolved config, caches it with version and
freshness state, behaves offline, and falls back or fails closed when config is
missing or invalid.

Mobile Version Control Logic in
`../../docs/mobile-version-control-logic.md` defines how the client reports its
version, receives optional-update, force-update, maintenance, blocked, or
deprecated states, shows store links/update messages, and avoids unsafe old
version behavior.

## Current Phase 3 State

This directory now contains a complete Laravel 13 + Livewire 4 + NativePHP
Mobile application copied from the verified root mobile client.

Implemented foundation:

- 52 `mobile.*` Livewire routes.
- Mobile-first Blade layout, safe-area shell, bottom navigation, and reusable
  mobile components.
- Welcome, auth, dashboard, settings, profile, notifications, debug,
  records/content, media, scanner, location, voice-note, sync, and local
  support surfaces from the root mobile app.
- NativePHP config, launcher, lockfile, service provider, and safe service
  wrappers.
- Dedicated `mobile_local` SQLite connection, migrations, models,
  repositories, queue/sync worker, local notifications, and health command.
- Focused Pest coverage copied with the app.

Fresh verification:

```bash
composer validate --strict
php artisan route:list --name=mobile
php artisan test --compact
vendor/bin/pint --dirty --format agent
npm run build
php artisan native:plugin:validate --no-interaction
```

`native:plugin:validate` exits successfully. It reports non-fatal warnings for
two third-party plugins that do not define bridge functions or native code
directories.

The repository root app remains temporarily as a transition mirror. Future
mobile work should target `apps/mobile-client` unless a cleanup task explicitly
removes or rewires the root app.
