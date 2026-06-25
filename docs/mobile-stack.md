# Mobile Stack

Updated: 2026-06-25

This document describes the intended SaaS stack and the current package baseline. The product has two systems:

1. **Admin/API system** - Laravel API plus Livewire admin panel.
2. **Mobile client system** - Laravel plus Livewire inside NativePHP Mobile.

The stack is intentionally Laravel-first so API rules, admin rules, tests, and mobile client behavior can share conventions without adding a separate JavaScript application framework.

The stack supports the product vision from [Product Vision](product-vision.md): remote admin control with local mobile resilience.

It also supports the [Product Positioning](product-positioning.md): SaaS control center, mobile workforce/client platform, API-first system, offline-capable mobile system, feature-controlled platform, and tenant-based product.

Stack decisions must preserve [Core Product Principles](product-principles.md): admin authority, API-first mobile communication, tenant isolation, secure defaults, simple mobile UX, documentation-first changes, and modular feature expansion.

Stack decisions must preserve [Documentation-First Architecture](documentation-first-architecture.md). New packages, NativePHP plugins, mobile screens, API surfaces, sync mechanisms, permission behavior, and risk-sensitive changes need documentation before implementation.

Stack decisions must preserve [Admin Control Center Logic](admin-control-center-logic.md). Admin-control behavior for tenants, users, roles, permissions, features, config, versions, maintenance, force update, sync, notifications, reports, billing, and support must remain server-scoped, authorized, auditable, and API-driven.

Stack decisions must preserve [Feature Flag Logic](feature-flag-logic.md). Important mobile feature availability must remain resolved by Admin/API from global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, emergency, and offline decisions.

Stack decisions must preserve [Remote Configuration Logic](remote-configuration-logic.md). Runtime-configurable behavior must remain scoped, versioned, validated, API-delivered, safely cached, tenant-aware, and fallback-safe.

Stack decisions must preserve [Mobile Version Control Logic](mobile-version-control-logic.md). Minimum supported versions, optional updates, forced updates, maintenance mode, store links, update messages, and stale-client protection must remain Admin/API-controlled.

Stack decisions must also preserve the role boundaries in [Target User Roles](user-roles.md). Platform-wide, tenant-scoped, support-scoped, billing-scoped, mobile, invited, suspended, and pre-login access should not collapse into one generic user experience.

Stack decisions must also preserve the [SaaS Value Map](saas-value-map.md). New packages, services, NativePHP plugins, reports, notification channels, or feature-flag mechanisms should map to clear value for platform owner, tenant business, tenant admin, mobile worker/client, support team, or billing/operations team.

Stack decisions must also preserve [Two-System Boundary Logic](two-system-boundary.md). A dependency, package, NativePHP plugin, queue, notification channel, cache, or local store should not move Admin/API authority into the mobile client.

Stack decisions must also preserve [API-First Principles](api-first-principles.md). A dependency, package, NativePHP plugin, local store, notification channel, or mobile UI helper should keep mobile communication API-only, response behavior predictable, errors mobile-friendly, sync/conflict behavior explicit, and tenant boundaries server-protected.

Stack decisions must also preserve [Admin/API Responsibilities](admin-api-responsibilities.md). A dependency or package should not blur tenant management, users and permissions, admin panel operations, API contracts, feature control, remote configuration, mobile version rules, notifications, billing, support, reporting, audit, conflict decisions, or security enforcement.

Stack decisions must also preserve [Mobile Client Responsibilities](mobile-client-responsibilities.md). A dependency, NativePHP plugin, local store, UI component, or session mechanism should support mobile UX, secure local session, cache, offline actions, device features, navigation, permissions UX, sync display, drafts, feedback, or feature visibility without taking server authority.

## Current Package Baseline

| Package / tool | Version | Product role |
| --- | --- | --- |
| PHP | 8.5 | Runtime for admin/API and mobile Laravel app. |
| Laravel Framework | 13.17.0 | API, admin, services, queues, policies, resources, tests. |
| Livewire | 4.3.1 | Admin panel interaction and mobile-client screens. |
| NativePHP Mobile | 3.3.6 | Native shell and mobile device capability bridge. |
| Tailwind CSS | 4.3.1 | Shared utility styling for Livewire/Blade surfaces. |
| `@tailwindcss/postcss` | 4.3.1 | Tailwind v4 processing after Sass preprocessing. |
| `sass-embedded` | 1.100.0 | SCSS entrypoint support. |
| Vite | 8.1.0 | Frontend asset build. |
| Pest | 4.7.3 | Feature, unit, API, and architecture tests. |
| SQLite | current local engine | Development and mobile-local storage baseline. |

## System Responsibilities

The stack is intentionally split because web-only would under-serve mobile workers, while mobile-only would under-serve SaaS governance.

### Admin/API System

The Admin/API system should be implemented as the SaaS control plane:

- Admin users are SaaS owners, platform operators, tenant owners, tenant admins, support users, billing operators, release managers, and security/compliance reviewers.
- Livewire admin panel for operators, tenant admins, support, billing, and reports.
- Versioned API for mobile boot, feature config, domain resources, notifications, sync, conflicts, support, and telemetry.
- Server-side authorization for every tenant, user, device, feature, billing, and support action.
- Eloquent resources or JSON:API style resources for mobile-facing payloads.
- Audit logs for admin changes and sensitive mobile-originated events.
- Role-aware dashboards and APIs that expose only the context each role should see.
- Responsibility-aware modules that make the owning Admin/API concern clear before code is added.
- Admin Control Center logic for tenant, user, role, permission, feature, config, version, maintenance, force update, sync, notification, report, billing, and support controls.

### Mobile Client System

The Mobile client system should be implemented as the managed edge client:

- Mobile users are tenant-side or field users who need simple allowed workflows without admin complexity.
- Livewire mobile screens rendered inside NativePHP.
- NativePHP plugins for device capabilities.
- Local SQLite for cache, drafts, queues, records, activity, notifications, and sync metadata.
- Secure storage for tokens and secrets.
- API-only communication with the Admin/API system.
- Offline-first UX that shows freshness, pending actions, and conflicts.
- Role-derived capability state from the API, not local role assumptions.
- Responsibility-aware mobile modules that make the owning local responsibility clear before code is added.

## Stack Decisions

- Keep mobile UI in Laravel + Livewire + Blade. Do not add React, Vue, Inertia, Ionic, or Capacitor unless a future ADR supersedes this decision.
- Use NativePHP for native capabilities rather than a separate mobile runtime.
- Document feature behavior, admin mobile effects, mobile API dependencies, sync behavior, permission ownership, and risks before adding stack or runtime surface area.
- Keep tenant and feature authority on the Admin/API system.
- Keep mobile local data as cache/draft/queue unless the API confirms it.
- Keep `resources/css/app.scss` as the canonical frontend stylesheet entrypoint.
- Process Tailwind through `@tailwindcss/postcss` after Sass. Do not reintroduce `@tailwindcss/vite` without verifying Tailwind output.
- Keep stack expansion modular: new packages or surfaces should map to a clear feature slice and principle.
- Keep stack expansion value-mapped: new infrastructure should prove stakeholder value instead of adding technical surface area for its own sake.
- Keep stack expansion boundary-safe: new mobile-local infrastructure must remain cache, draft, queue, local metadata, or presentation unless API confirms otherwise.
- Keep stack expansion API-safe: new infrastructure must preserve API-only mobile communication, predictable responses, clear API purpose, mobile-friendly errors, sync/conflict support, and tenant boundary protection.
- Keep stack expansion responsibility-safe: tenant, permission, API, feature, config, version, notification, billing, support, report, audit, conflict, and security concerns must remain Admin/API-owned.
- Keep stack expansion mobile-safe: UX, local session, cache, queues, NativePHP plugins, navigation, permissions UX, sync display, drafts, feedback, and feature visibility must remain local execution concerns.
- Keep stack expansion control-safe: admin controls must have scope, authorized role, mobile effect, API context, audit expectation, support explanation, offline behavior, and risk boundary before implementation.
- Keep stack expansion flag-safe: features must define priority, disabled mobile state, rollout, admin impact, plan limit, support, audit, offline behavior, and retirement before implementation.
- Keep stack expansion config-safe: remote config must define type, default, scope, override, compatibility, mobile cache, offline behavior, invalid-config fallback, support, audit, rollback, and retirement before implementation.
- Keep stack expansion version-safe: app-version policy must define minimum support, optional update, forced update, maintenance, outdated-client response, store links, update messages, support, audit, rollback, and old-version protection before implementation.

## Why NativePHP + Livewire

NativePHP + Livewire is chosen because this product is a Laravel SaaS first and a native-capable mobile shell second.

- Laravel remains the center for validation, authorization, API resources, policies, queues, notifications, billing logic, support workflows, and tests.
- Livewire keeps admin and mobile interactions in the Laravel/Blade model without adding a separate JavaScript frontend framework.
- NativePHP supplies the mobile shell and native plugin bridge for capabilities such as camera, files, microphone, network status, sharing, and device context when product slices require them.
- A shared Laravel mental model reduces duplicated logic and keeps mobile behavior aligned with API/admin authority.
- The mobile client still works through the API. NativePHP + Livewire is a client implementation choice, not a shortcut around server authority.

## API Stack Principles

Use Laravel's API routing and resource conventions for mobile endpoints:

- Follow [API-First Principles](api-first-principles.md) before designing any mobile/API behavior.
- API routes belong in the stateless API surface.
- Authentication should use token-based first-party mobile auth.
- The API is the boundary where admin settings become enforceable mobile behavior.
- API-first positioning means every mobile feature should have a server contract before it becomes a local screen.
- Responses should be shaped resources, not raw models.
- Request validation and authorization must happen server-side.
- High-volume endpoints need rate limits.
- Replayable writes need idempotency keys.
- Mobile-dependent behavior needs versioned contracts.

## Admin Stack Principles

The admin panel should be operational, dense, and auditable:

- Tables should support search, filters, pagination, and scoped tenant visibility.
- Forms should validate on the server and record audit context for sensitive changes.
- Destructive or broad changes should require confirmation and produce audit events.
- Remote config, feature flags, app-version policy, and sync policy should be reversible where possible.
- Admin screens should delegate business decisions to actions/services rather than embedding logic in Blade or panel classes.

## Mobile Stack Principles

The mobile client should remain small, predictable, and resilient:

- Keep Livewire public state compact and non-sensitive.
- Do not treat disabled buttons, `wire:confirm`, or local flags as authorization.
- Do not store tenant, permission, billing, feature, app-version, support, report, audit, or sync authority in mobile-local state.
- Use local SQLite for offline working state only.
- Use NativePHP secure storage for tokens.
- Use conservative polling and sync intervals.
- Use API boot config to decide navigation, feature visibility, app-version gates, and notification behavior.

## Installed NativePHP Capability Groups

The installed plugin set currently covers browser, camera, device, dialog, file, microphone, network, share, system, permissions, fullscreen, loaders, splash screen, in-app update, in-app reviews, screenshot blocking, double-back-close, and locales.

Premium or marketplace capabilities such as secure storage, geolocation, scanner, biometrics, background tasks, local notifications, Firebase, contacts, NFC, and calendar should be added only when a product slice requires them and only with credentials outside git.

## Build And Verification Commands

```bash
composer install
npm install
npm run build
php artisan test --compact
php artisan native:debug --no-interaction
php artisan native:plugin:validate
```

Supporting inspection:

```bash
composer show livewire/livewire --locked
composer show nativephp/mobile --locked
npm ls tailwindcss @tailwindcss/postcss sass-embedded vite --depth=0
php artisan route:list --except-vendor
```

## Current Native Tooling Notes

NativePHP debug previously reported:

- Java: present.
- CocoaPods: present.
- Xcode: not detected.
- Android Studio: not detected.
- Gradle: not detected.

iOS and Android simulator work should be considered blocked until local native build tooling is installed and rechecked.

## Documentation Boundary

This document defines stack direction and system responsibility. It does not create schema, migrations, routes, controllers, admin resources, or mobile logic.
