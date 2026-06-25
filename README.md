# Mobile Lara

Mobile Lara is a planned SaaS platform for centrally managed NativePHP mobile applications. The product is split into two cooperating systems:

1. **Admin/API system** - Laravel API plus a Livewire admin panel. This is the SaaS control plane.
2. **Mobile client system** - Laravel plus Livewire running inside NativePHP Mobile. This is the managed edge client.

The core idea is simple: tenants operate from the admin panel, while mobile users work in a controlled mobile client that receives all business rules, permissions, feature availability, app-version policy, notifications, sync behavior, support state, and billing entitlement through the API.

## Product Position

Mobile Lara is not just a mobile app starter. It is a control-plane product for businesses that need mobile workflows they can govern remotely.

The admin/API system owns:

- Tenants, teams, users, roles, permissions, and device trust.
- Remote config, feature flags, app-version requirements, and rollout rules.
- Notification policy, support workflow, reports, billing plans, and usage limits.
- API contracts, audit trails, sync policy, conflict handling, and operational controls.

The mobile client owns:

- Local mobile UX, Livewire screens, NativePHP capability bridges, and device permissions.
- Offline-first local state, queued actions, local records, local media metadata, and sync status.
- Safe presentation of admin-controlled capabilities without inventing its own product rules.

## Product Principle

The admin system is the source of authority. The mobile client is a resilient local executor.

If a capability is disabled, unlicensed, blocked by version policy, denied by permission, or outside tenant scope, the mobile client must treat that as final even if local UI state still contains stale cached data.

## Documentation Map

| Document | Purpose |
| --- | --- |
| [docs/saas-mobile-admin-platform.md](docs/saas-mobile-admin-platform.md) | Canonical product and system concept. |
| [docs/decisions/0001-admin-api-control-plane-and-native-mobile-client.md](docs/decisions/0001-admin-api-control-plane-and-native-mobile-client.md) | ADR for the two-system architecture. |
| [docs/mobile-stack.md](docs/mobile-stack.md) | Stack, package, and boundary notes. |
| [docs/mobile-app-audit.md](docs/mobile-app-audit.md) | Current-state audit against the target concept. |
| [docs/nativephp-local-storage.md](docs/nativephp-local-storage.md) | Offline-first local SQLite and sync principles. |
| [docs/nativephp-run.md](docs/nativephp-run.md) | NativePHP run, release, and app-version operating notes. |
| [docs/design-system.md](docs/design-system.md) | Mobile and admin UX principles. |
| [AGENTS.md](AGENTS.md) / [CLAUDE.md](CLAUDE.md) | Agent-facing project rules. |

## Current Technical Baseline

- PHP 8.5.
- Laravel 13.
- Livewire 4.
- NativePHP Mobile 3.
- SQLite for current local development and mobile-local storage.
- Tailwind CSS 4 through the SCSS/PostCSS bridge.
- Pest 4 for tests.

The repository currently contains mobile-client surfaces and local-mobile infrastructure. The admin/API system is documented here as the product control plane and must be implemented only after a dedicated implementation prompt. This documentation pass does not create database fields, migrations, controllers, or application logic.

## Operating Rules

- Use Eloquent and Laravel resources for API-facing data. Do not use raw SQL strings.
- Keep admin business rules on the server. Mobile UI state is never an authorization boundary.
- Treat NativePHP secure storage as the home for secrets and tokens. Do not store secrets in local SQLite.
- Treat local SQLite as a cache, queue, draft, and offline-working database.
- Make every mobile action idempotent at the API boundary.
- Version every API behavior that the mobile app depends on.
- Prefer feature flags and remote config for rollout control, not hardcoded app decisions.

## Common Commands

```bash
composer install
npm install
npm run build
php artisan test --compact
php artisan native:debug --no-interaction
php artisan native:plugin:validate
```

Laravel Herd serves the local app at the project test domain. Use Laravel Boost's `get-absolute-url` MCP tool before sharing URLs.

## Non-Goals For This Documentation Commit

- No application logic was implemented.
- No schema, migrations, or database fields were created.
- No admin resources, API controllers, policies, or Livewire components were added.
- No billing provider, push provider, or external service was integrated.

This repository should move from concept to implementation through explicit product slices, each with tests, migrations, authorization, API contracts, and admin/mobile acceptance criteria.
