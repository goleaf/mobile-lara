# Mobile Lara

Mobile Lara is a planned SaaS platform for centrally managed NativePHP mobile applications. Its product vision is remote control with local resilience: administrators govern mobile behavior centrally, while mobile users keep working through a focused NativePHP client.

The product is positioned as a tenant-based SaaS control center with an API-first, feature-controlled, offline-capable mobile workforce/client platform.

The core principle is strict: Admin/API controls business authority; mobile never bypasses the API; every feature is controllable; tenant isolation, security, documentation, and modular expansion are default requirements.

The role model is explicit: platform owner, super admin, tenant admin, tenant manager, support agent, billing manager, mobile user, invited user, suspended user, and guest/pre-login user each have different responsibilities, visibility, and control boundaries.

The product solves a common business problem: mobile teams need a simple app, but the organization needs tenant-safe control over permissions, billing, feature availability, app versions, support, notifications, reports, and sync behavior without publishing a new mobile build for every policy change.

The product is split into two cooperating systems:

1. **Admin/API system** - Laravel API plus a Livewire admin panel. This is the SaaS control plane.
2. **Mobile client system** - Laravel plus Livewire running inside NativePHP Mobile. This is the managed edge client.

The core idea is simple: admin users operate the control plane, while mobile users work in a controlled mobile client that receives all business rules, permissions, feature availability, app-version policy, notifications, sync behavior, support state, and billing entitlement through the API.

## Product Position

Mobile Lara is not just a mobile app starter. It is a control-plane product for businesses that need mobile workflows they can govern remotely.

Admin users include SaaS owners, platform operators, tenant owners, tenant admins, support users, billing operators, product/release managers, and security or compliance reviewers. Mobile users are the frontline or tenant-side people who perform work in the app and should not need to understand feature flags, billing rules, rollout cohorts, or sync policy internals.

Mobile Lara is better than building only a web app because mobile workers need native capability access, offline-capable workflows, local sync state, and mobile-first ergonomics. It is better than building only a mobile app because a SaaS business needs tenant administration, billing enforcement, feature rollout, app-version policy, support visibility, reports, and audit trails.

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
| [docs/product-vision.md](docs/product-vision.md) | Plain-language product vision, users, problem, technology choice, and SaaS scale logic. |
| [docs/product-positioning.md](docs/product-positioning.md) | Product positioning as SaaS control center, mobile client platform, API-first system, offline-capable system, feature-controlled platform, and tenant-based product. |
| [docs/product-principles.md](docs/product-principles.md) | Core product principles for admin control, API-first mobile behavior, feature control, tenant isolation, offline use, security, documentation, and modular expansion. |
| [docs/user-roles.md](docs/user-roles.md) | Main logical user roles, responsibilities, limitations, visibility, and control boundaries. |
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
- Apply [core product principles](docs/product-principles.md) before feature implementation.
- Apply [target user roles](docs/user-roles.md) before designing permissions, visibility, support, billing, or mobile access.
- Keep admin business rules on the server. Mobile UI state is never an authorization boundary.
- Let admin settings control mobile behavior because mobile state may be stale, offline, copied between devices, or running an old app version.
- Position the product as both admin control center and mobile workforce/client platform; avoid web-only or mobile-only thinking.
- Treat NativePHP secure storage as the home for secrets and tokens. Do not store secrets in local SQLite.
- Treat local SQLite as a cache, queue, draft, and offline-working database.
- Make every mobile action idempotent at the API boundary.
- Version every API behavior that the mobile app depends on.
- Prefer feature flags and remote config for rollout control, not hardcoded app decisions.
- Keep NativePHP + Livewire as the mobile approach unless a future ADR changes the product direction.

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
