# Feature Dependency Map

Final Optimized SaaS Blueprint is defined in `final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

Updated: 2026-06-26

This document defines the feature dependency map for the Mobile Lara SaaS
mobile/admin system. It explains how major product features depend on
authentication, tenant context, permissions, feature flags, remote config, API
availability, offline cache, NativePHP permissions, subscription plan, and
admin settings.

This is documentation only. It does not create database fields, migrations,
routes, controllers, Livewire components, Filament resources, NativePHP
plugins, policies, jobs, services, tests, API endpoints, local storage schemas,
UI components, CSS, JavaScript, queues, provider integrations, billing
provider rules, notification provider rules, release automation, or application
logic.

Use this document with [Documentation Audit](documentation-audit.md),
[Acceptance Principles](acceptance-principles.md), [Risk Map](risk-map.md),
[Testing Strategy Principles](testing-strategy-principles.md), [Release And
Versioning Principles](release-versioning-principles.md), [Two-System Boundary
Logic](two-system-boundary.md), [Admin/API
Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [API-First
Principles](api-first-principles.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration
Logic](remote-configuration-logic.md), [Role And Permission
Logic](role-permission-logic.md), [Tenant Lifecycle
Logic](tenant-lifecycle-logic.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Offline-First
Principles](offline-first-principles.md), [Sync Lifecycle
Logic](sync-lifecycle-logic.md), [Native Feature
Strategy](native-feature-strategy.md), [Billing And Plan
Logic](billing-and-plan-logic.md), [Admin Control Center
Logic](admin-control-center-logic.md), and [Mobile Version Control
Logic](mobile-version-control-logic.md): feature dependency decisions must be
documented before implementation so Admin/API authority, mobile execution,
offline behavior, native capability use, subscription limits, and admin control
remain predictable.

## Dependency Principle Statement

Every major feature in Mobile Lara is allowed only after its dependency chain is
resolved. A screen, API contract, native capability, or admin setting is not a
standalone product feature. It is a behavior that depends on identity, tenant
scope, permission authority, feature availability, safe configuration, API
reachability, local cache state, native permission status, plan entitlement,
and admin policy.

Dependency resolution belongs to Admin/API. The mobile client may display the
resolved state, cache safe context, request native permissions, preserve drafts,
queue allowed intents, and explain unavailable behavior. The mobile client must
not invent a dependency result locally.

## Dependency Axes

| Dependency | Product meaning | Failure behavior |
| --- | --- | --- |
| Authentication | The user, session, token, and device trust context are known to Admin/API. | Guest-safe or locked state; protected features do not start. |
| Tenant context | The active tenant is selected, valid, and allowed for the user. | Tenant selection, tenant-blocked, billing-blocked, suspended, archived, or support state. |
| Permissions | Admin/API confirms what the current user can see or do. | Hide, disable, read-only, explain denied access, or route to support. |
| Feature flags | Admin/API resolves whether the capability is available for the scope. | Hide, disabled explanation, update-required, plan-blocked, maintenance, or rollout-wait state. |
| Remote config | Admin/API returns safe runtime values for the allowed capability. | Safe defaults, conservative limits, stale warning, or blocked protected workflow. |
| API availability | Mobile can reach the trusted API and receive authoritative decisions. | Loading, retry, offline, stale, pending, read-only, or support state. |
| Offline cache | Mobile has safe local data, drafts, queues, and freshness metadata. | Empty offline state, stale state, draft-only state, or wait-for-online state. |
| NativePHP permissions | Device permission and plugin capability are available for native behavior. | Educate, request, recover in settings, fallback, or disable the native action. |
| Subscription plan | The tenant plan allows the capability and its limits. | Plan unavailable, limit reached, upsell/contact-admin, or admin billing action. |
| Admin settings | Platform or tenant admin settings allow the behavior under current policy. | Disabled, maintenance, force update, restricted, support-only, or audit-required state. |

## Resolution Order

Features should resolve dependencies in this order unless a canonical feature
document defines a stricter order:

1. Security, maintenance, suspension, force-update, and tenant isolation rules
   fail closed.
2. Authentication establishes user, session, token, and device context.
3. Tenant context establishes the active commercial and security boundary.
4. Subscription plan establishes the maximum allowed capability.
5. Feature flags establish whether the capability is available for the scope.
6. Permissions establish what the current user may see or do.
7. Admin settings and remote config tune safe behavior inside the allowed
   capability.
8. API availability decides whether trusted reads, writes, sync, and conflict
   decisions can happen now.
9. Offline cache decides whether mobile can show safe local state, preserve
   drafts, or queue allowed intents.
10. NativePHP permissions decide whether native device actions can run.

The resolved output should be a mobile-safe state such as available,
hidden, disabled, read-only, pending sync, offline draft, permission blocked,
plan blocked, tenant blocked, update required, maintenance, or contact support.

## Major Feature Dependency Matrix

| Feature | Primary dependencies | Mobile behavior when dependencies are missing |
| --- | --- | --- |
| App shell and bootstrap | Authentication, tenant context, feature flags, remote config, API availability, offline cache, admin settings. | Show welcome, locked, offline, maintenance, forced update, tenant selection, sync-in-progress, permission-blocked, or feature-disabled state. |
| Authentication and session | API availability, admin settings, NativePHP secure storage availability, tenant context after login. | Allow guest/pre-login screens, explain connection failure, preserve safe existing session offline, and lock or logout when server revokes access. |
| Tenant switching | Authentication, tenant context, permissions, API availability, offline cache, admin settings. | Require online confirmation for trusted tenant changes, separate tenant cache, pause unsafe sync, and keep stale tenant context read-only when needed. |
| Mobile dashboard | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, subscription plan, admin settings. | Show only enabled shortcuts, stale/offline status, pending sync, plan or permission explanations, and safe cached summaries. |
| Mobile settings | Authentication, tenant context, permissions, remote config, API availability, offline cache, NativePHP permissions, admin settings. | Keep local-only settings available, disable server-owned settings offline, show permission recovery, and explain admin-controlled settings. |
| Records/content | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, NativePHP permissions for attachments/media/location, subscription plan, admin settings. | Allow safe local drafts and queued intents when policy allows; final create/update/archive/restore/delete and conflict decisions wait for API acceptance. |
| Search and filters | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, subscription plan, admin settings. | Use local search only over cached tenant-safe data, explain stale or limited results, and require API for authoritative search, saved filters, or cross-device results. |
| Forms and drafts | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, NativePHP permissions when form fields use native capability, subscription plan, admin settings. | Autosave locally when safe, queue allowed submissions, block online-only forms, and preserve user work without claiming server acceptance. |
| Notifications and inbox | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, NativePHP notification permission, subscription plan, admin settings. | Show cached inbox state, request notification permission only for enabled features, defer registration or preference changes until API is available, and respect admin targeting. |
| Support requests | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, NativePHP permissions for attachments/diagnostics, subscription plan, admin settings. | Allow offline support drafts, limit diagnostics to privacy-safe data, and require API for case creation, messages, escalation, assignment, and audit. |
| Billing and plan visibility | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, subscription plan, admin settings. | Show cached plan labels cautiously, require API for entitlement truth, hide payment authority from mobile users, and explain unavailable features without local plan changes. |
| Reports and analytics | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, subscription plan, admin settings. | Show only allowed cached summaries if documented; require API for authoritative metrics, exports, date ranges, billing reports, support reports, and cross-tenant views. |
| Offline sync | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, subscription plan, admin settings. | Queue only allowed intents, label pending state, retry through API, stop on tenant/permission/plan changes, and surface conflicts without local final authority. |
| Camera and media | Authentication, tenant context, permissions, feature flags, remote config, API availability for upload/acceptance, offline cache, NativePHP camera/file permissions, subscription plan, admin settings. | Permit preview or local attachment draft only when policy allows; upload and server attachment acceptance wait for API. |
| Scanner | Authentication, tenant context, permissions, feature flags, remote config, API availability for validation, offline cache for local scan history, NativePHP camera/scanner permission, subscription plan, admin settings. | Allow scan-to-local-search only within cached data if permitted; require API for scan validation, duplicate decisions, create decisions, and authoritative results. |
| Geolocation | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, NativePHP location permission, subscription plan, admin settings. | Explain location purpose, allow local location-attached drafts or pending check-ins only when policy allows, and require API for trusted check-in acceptance. |
| Voice notes | Authentication, tenant context, permissions, feature flags, remote config, API availability for upload/acceptance, offline cache, NativePHP microphone/file permissions, subscription plan, admin settings. | Record and store locally only when enabled; upload, retention, attachment acceptance, and future transcription stay API/Admin controlled. |
| Device, network, and diagnostics | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, NativePHP device/network/secure-storage capability, subscription plan, admin settings. | Show local status, redact diagnostics, let users control sharing, and require API/admin permission for support-visible diagnostics. |
| App version, maintenance, and forced update | Authentication where available, tenant context where relevant, remote config, API availability, offline cache, subscription plan, admin settings. | Use cached policy conservatively, block protected workflows when version state is unsafe, show update/maintenance messages, and preserve safe local data. |
| Admin control center | Authentication, tenant context for tenant-scoped admins, permissions, feature flags, remote config, API availability, subscription plan, admin settings. | Admin behavior is not mobile-local; unavailable dependencies require safe disabled controls, impact preview, confirmation, audit expectation, and rollback path. |
| Feature flag management | Authentication, tenant context for scoped changes, permissions, API availability, subscription plan, admin settings. | Mobile only receives resolved outcomes; admin changes require impact clarity, tenant isolation, audit, rollback, and support meaning. |
| Remote config management | Authentication, tenant context for overrides, permissions, feature flags, API availability, subscription plan, admin settings. | Mobile uses validated resolved config; missing, stale, or invalid config falls back safely and never grants authority. |
| Field service module | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, NativePHP camera/location/signature capability when enabled, subscription plan, admin settings. | Technicians can use safe assigned-work cache, notes, media drafts, and pending check-in/out where allowed; dispatch, acceptance, conflict, and reports stay API/Admin controlled. |
| Logistics/delivery module | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, NativePHP scanner/location/camera permissions, subscription plan, admin settings. | Allow route/job cache and proof drafts where allowed; pickup/drop-off validation, failed delivery decisions, duplicates, and monitoring stay API/Admin controlled. |
| Booking module | Authentication as required by tenant rules, tenant context, permissions, feature flags, remote config, API availability, offline cache, subscription plan, admin settings. | Mobile may show cached services or drafts, but availability, confirmation, cancellation, reschedule, reminders, and tenant rules require API authority. |
| Commerce module | Authentication where required, tenant context, permissions, feature flags, remote config, API availability, offline cache for catalog/cart drafts, subscription plan, admin settings. | Mobile may browse safe cached catalog and cart drafts where allowed; price, inventory, checkout, payment, order, invoice, and receipt truth require API/hosted payment authority. |
| Messaging/community | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, NativePHP notification/file permissions when used, subscription plan, admin settings. | Allow offline message drafts, local muted state presentation, and cached conversations when safe; sending, moderation, reports/abuse, delivery, and visibility require API. |
| AI assistant module | Authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache only for safe prompts/context, subscription plan, admin settings. | Hide unless tenant opted in and plan allows it; require API/admin policy for AI use, privacy boundaries, human review, moderation assistance, and generated report assistance. |

## Dependency Failure States

Dependency failures should produce clear product states rather than generic
errors:

| Failed dependency | Product state |
| --- | --- |
| Not authenticated | Welcome, login, locked, session expired, or revoked access. |
| No valid tenant | Tenant selection, tenant unavailable, tenant suspended, archived, billing blocked, or support path. |
| Missing permission | Hidden, disabled, read-only, permission denied, request access, or contact admin. |
| Feature disabled | Hidden shortcut, disabled explanation, rollout unavailable, plan unavailable, or support explanation. |
| Remote config missing or invalid | Safe default, stale warning, conservative limit, or blocked protected workflow. |
| API unavailable | Offline, retry, pending sync, read-only cache, local draft, or wait-for-online state. |
| Offline cache missing or stale | Empty offline state, stale data warning, or require connection. |
| Native permission denied | Explain purpose, recover in settings, fallback, or disable native action. |
| Plan unavailable or limit reached | Plan blocked, limit reached, contact tenant admin, billing manager action, or upsell message. |
| Admin setting disabled | Disabled by admin, maintenance, support-only, force update, or policy blocked. |

## Admin Visibility Principles

Admins should be able to understand dependency impact before enabling,
disabling, or changing a feature:

- Which tenants, users, roles, app versions, devices, and plans are affected.
- Which API contracts, mobile screens, native permissions, offline queues, and
  reports depend on the change.
- Whether the feature has safe fallback, disabled, read-only, update-required,
  maintenance, or rollback states.
- Whether the feature depends on an external provider such as push, hosted
  payment, AI, or app-store distribution.
- Whether offline users may hold stale dependency context and how mobile should
  refresh or fail closed.
- Which support, audit, reporting, billing, and privacy messages explain the
  dependency decision.

## Mobile UX Principles For Dependencies

Mobile should make dependency results understandable without exposing internal
policy complexity:

- Show the resolved state, not raw policy internals.
- Prefer one clear reason for unavailability when multiple dependencies fail.
- Preserve user work as drafts when safe.
- Label pending local work separately from synced server truth.
- Request NativePHP permissions only after feature flags, plan, tenant,
  permission, and admin settings allow the feature.
- Avoid permission prompts for disabled, unavailable, or plan-blocked features.
- Use offline cache only with freshness, tenant, and pending-state language.
- Offer recovery actions such as retry, sign in, select tenant, update app,
  open settings, contact support, or contact admin.

## Documentation Requirements

Every future feature document should include a dependency section that answers:

1. Does the feature require authentication, or can a guest/pre-login user use
   part of it?
2. Which tenant context does the feature require?
3. Which permissions decide visibility and action authority?
4. Which feature flags or rollout gates control availability?
5. Which remote config values may safely tune behavior?
6. Which behavior requires live API availability?
7. Which data may be cached, drafted, queued, searched, or shown offline?
8. Which NativePHP permissions or plugins are required, optional, or forbidden?
9. Which subscription plans, limits, or billing states affect the feature?
10. Which admin settings can disable, limit, preview, or roll back the feature?

If a feature cannot answer these questions, it is not ready for implementation
planning.
