# Mobile App Audit

Audit date: 2026-06-25

Scope: documentation and planning only. This audit compares the current repository direction with the optimized SaaS mobile + admin concept. It does not request or create application logic, schema, migrations, or API endpoints.

## Executive Summary

Mobile Lara should be treated as a two-system SaaS platform:

1. **Admin/API system** - Laravel API plus Livewire admin panel. This is the control plane.
2. **Mobile client system** - Laravel plus Livewire running inside NativePHP Mobile. This is the managed mobile edge client.

The repository already contains substantial mobile-client concepts: Livewire mobile screens, NativePHP services, mobile-local models, local SQLite migrations, offline action infrastructure, permission center ideas, records, media, check-ins, scan history, notifications, and sync status. The admin/API control plane is documented as the source of authority and should be implemented in future slices, not during documentation work.

The product vision is remote control with local resilience. Current mobile assets should be judged by whether they can become admin-controlled, API-enforced, tenant-safe, supportable, and sync-aware. See [Product Vision](product-vision.md).

The product positioning is a tenant-based SaaS control center plus an API-first, feature-controlled, offline-capable mobile workforce/client platform. See [Product Positioning](product-positioning.md).

## Product Vision Audit

| Vision question | Product answer |
| --- | --- |
| What problem is solved? | Businesses need controlled mobile workflows without changing the app for every policy, tenant, billing, version, support, or sync decision. |
| Who are admin users? | SaaS owners, platform operators, tenant owners/admins, support, billing, release, and security/compliance users. |
| Who are mobile users? | Tenant-side or field users who need simple permitted workflows, native capabilities, and clear offline/sync states. |
| Why two systems? | Admin/API owns authority and operations; mobile owns focused execution, device capability access, cache, drafts, and queue state. |
| Why admin-controlled mobile? | Mobile state can be stale, offline, copied, tampered with, or running an old version, so server policy must remain final. |
| Why NativePHP + Livewire? | It keeps the product Laravel-first while allowing native mobile capabilities and dynamic Blade/Livewire workflows. |
| Why scalable as SaaS? | Tenant isolation, feature flags, remote config, app-version policy, idempotent sync, observability, support, and billing entitlements turn growth into operations instead of one-off app builds. |

## Product Positioning Audit

| Position | Audit lens |
| --- | --- |
| SaaS control center | Does the feature have admin control, support visibility, reports, and audit context? |
| Mobile workforce/client platform | Does the feature serve a real mobile workflow instead of duplicating admin UI? |
| API-first system | Does the API enforce the behavior and return explicit state to the client? |
| Offline-capable mobile system | Is local work treated as cache, draft, queue, or confirmed server state? |
| Feature-controlled platform | Can the feature be enabled, limited, rolled back, or version-gated? |
| Tenant-based product | Is every action tenant-scoped and safe for multi-tenant support/reporting? |

If a future feature only satisfies the web/admin side or only the mobile side, it is incomplete for this product.

## Current Product Assets

### Mobile Client Assets

The repository contains mobile-oriented classes and views for:

- Authentication, registration, password reset, email verification, sessions, app unlock, PIN, and account deletion.
- Dashboard, create flow, search, profile, settings, developer/debug screens, permissions, storage, and legal pages.
- Offline banner, sync status, network status, toast center, and conflict screens.
- Records, record details, categories, tags, notes, attachments, and activity timeline.
- Media capture/gallery, file manager, voice notes, scanner demo, scan history, location check-in, and check-in history.
- Local notifications and schedules.

These assets should be understood as the mobile-client surface. They must receive business authority through the API and admin-controlled config.

### Local-Mobile Assets

The repository contains local mobile infrastructure for:

- A dedicated `mobile_local` SQLite connection.
- Local settings and health checks.
- Offline actions and conflict fields.
- Local records, media, voice notes, check-ins, scan history, notifications, schedules, categories, tags, notes, attachments, and activity logs.
- Repository/service classes for local storage operations and sync work.

Local storage is useful for offline resilience, but it is not the source of tenant, billing, permission, or feature truth.

### NativePHP Assets

NativePHP Mobile is configured with plugins and services around:

- Browser, camera, device, dialog, file, microphone, network, share, system, permissions, fullscreen, loader, splash screen, in-app update, in-app reviews, screenshot blocking, double-back-close, and locales.

Native capabilities should be exposed through product slices only when admin policy, API behavior, permission copy, and mobile UX are all defined.

## Target Product Gaps

The optimized SaaS product still needs these concepts to be implemented in future work:

| Area | Target behavior |
| --- | --- |
| Admin/API control plane | Tenant, user, role, permission, device, config, feature flag, billing, support, report, audit, and sync policy management. |
| API boot payload | Mobile receives tenant memberships, permissions, feature flags, remote config, app-version policy, and sync policy. |
| App-version control | Admin can block, warn, or require update by platform/version/tenant/cohort. |
| Feature flag rollout | Features can be enabled globally, per tenant, per role, per app version, or per cohort. |
| Billing entitlements | API enforces plan access and mobile only displays allowed/denied states. |
| Support operations | Mobile can create cases with safe diagnostics; admin/support can inspect context and sync health. |
| Reports | Admin can report app adoption, device health, sync health, notification health, usage, support, and billing state. |
| Notification policy | Admin controls templates, channels, quiet hours, device targeting, and delivery health. |
| Conflict governance | API decides conflict state; mobile displays and resolves according to policy; admin can monitor conflict rate. |
| Product vision governance | Every future feature must prove the full loop from admin setting to API enforcement to mobile UX to support/audit visibility. |
| Product positioning governance | Future slices must preserve the combined SaaS control center plus mobile platform positioning rather than drifting into web-only or mobile-only work. |

## Business Logic Audit

Future feature work must not start from a screen. Each feature must be documented and implemented across:

- Admin control behavior.
- API request/response behavior.
- Mobile display behavior.
- Offline behavior.
- Sync/conflict behavior.
- Support behavior.
- Billing/entitlement behavior if applicable.
- Audit behavior.

If one of those perspectives is missing, the feature is not yet product-ready.

## API Audit Principles

Boost documentation confirms Laravel's API routes are stateless and Laravel supports API backends for mobile apps. Future API work should use:

- Token authentication for mobile.
- Server-side policies for authorization.
- Shaped resources for responses.
- Versioned payloads for mobile-dependent behavior.
- Rate limits for auth, sync, support, notifications, and telemetry.
- Idempotency keys for queued offline writes.
- Explicit error categories for validation, forbidden, conflict, stale version, maintenance, and retry-later states.

## Offline-First Audit Principles

Offline-first behavior should be constrained:

- Local SQLite can store cache, drafts, local records, sync metadata, and queued intents.
- Secure tokens must not be stored in SQLite.
- Queued writes are not trusted facts until the API confirms them.
- Mobile should show last sync, pending count, offline reason, and conflict state.
- API must be able to reject stale, unauthorized, duplicate, or out-of-policy queued actions.

## Admin Control Audit Principles

Admin controls should be:

- Tenant-scoped.
- Permission-protected.
- Auditable.
- Reversible where possible.
- Safe by default.
- Designed for support and operations, not just configuration.

Every admin action that can change mobile behavior should include the scope, actor, old value, new value, and reason in the audit model when implemented.

## Risk Register

| Risk | Why it matters | Documentation decision |
| --- | --- | --- |
| Mobile screens drift ahead of server authority | Users may see actions they cannot perform. | API boot config and feature flags must drive mobile navigation and capability display. |
| Offline queue becomes business truth | Server-side authorization and billing can be bypassed conceptually. | Offline actions are intents until API confirmation. |
| Admin flags become untraceable | Support cannot explain changed behavior. | Feature/config changes require audit trails. |
| App-version policy is ignored | Old clients keep calling stale API behavior. | App-version policy is part of mobile boot and API enforcement. |
| Tenant data leaks through support/reporting | SaaS trust is broken. | Tenant scoping applies to support and reports as strongly as to core data. |
| Product grows by screens instead of control loops | The SaaS value becomes unclear and expensive to operate. | Every feature starts from the product vision and full admin/API/mobile/support/audit loop. |
| Product drifts into web-only or mobile-only | One side of the product stops justifying the other. | Evaluate every slice against the product positioning audit lens. |

## Next Planning Slices

1. Managed mobile boot: authentication, tenant selection, remote config, feature flags, app-version policy.
2. Offline records: admin-enabled module, local queue, replay, conflict reporting.
3. Notifications: device registration, templates, delivery policy, local history.
4. Support and diagnostics: safe mobile diagnostics, support case timeline, admin triage.
5. Billing and entitlements: plan-driven capability limits enforced by API.

## Verification Commands For Future Implementation

```bash
php artisan route:list --except-vendor
php artisan test --compact
npm run build
php artisan native:debug --no-interaction
php artisan native:plugin:validate
```

## Sources And References

- [SaaS Mobile Admin Platform Concept](saas-mobile-admin-platform.md)
- [Product Vision](product-vision.md)
- [Product Positioning](product-positioning.md)
- [ADR-0001](decisions/0001-admin-api-control-plane-and-native-mobile-client.md)
- Laravel Boost application info and documentation search.
- Laravel API routing, authentication, resources, and JSON testing documentation.
- Livewire 4 project skill guidance.
