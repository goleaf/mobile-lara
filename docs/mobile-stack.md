# Mobile Stack

Final Consistency Review is defined in `final-consistency-review.md`:
all SaaS idea documentation must preserve API-only mobile authority,
admin-controlled configurable features, separated feature flags and remote
config, tenant isolation, clear offline behavior, permission-aware
NativePHP features, logical billing and plan limits, privacy-safe support,
tenant-bound reports, docs-only planning language, no database-field
definitions, and consistent terminology.

Final Optimized SaaS Blueprint is defined in `final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

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

Stack decisions must preserve [Admin Safety Principles](admin-safety-principles.md). Dangerous admin controls, operational packages, providers, scripts, or NativePHP release choices must support confirmation, audit history, impact preview, mobile impact preview, rollback, and tenant-isolated scope.

Stack decisions must preserve [Mobile UX Principles](mobile-ux-principles.md). Packages, NativePHP plugins, local stores, UI helpers, and Livewire patterns should support mobile-first navigation, simple screens, clear loading/offline states, thumb-friendly controls, minimum typing, fast actions, secure sessions, feature visibility, and native permission education.

Mobile App Shell Logic is defined in `mobile-app-shell-logic.md`:
shell states must coordinate welcome, authenticated, locked, offline, maintenance, forced update, tenant
switching, sync-in-progress, permission-blocked, and feature-disabled behavior
before implementation.

Mobile Dashboard Logic is defined in `mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Mobile Settings Logic is defined in `mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

Authentication Principles are defined in `authentication-principles.md`:
mobile login must happen through the API only; access and refresh tokens must
use secure storage; refresh, logout, logout-all-devices, tenant selection,
session expiry, offline already-authenticated behavior, and server revocation
must preserve Admin/API authority before implementation.

Mobile App Lock Principles are defined in `mobile-app-lock-principles.md`:
the mobile client must lock on security-sensitive lifecycle, timeout,
account, tenant, offline-cache, and admin-policy conditions; require
confirmation for sensitive areas; use biometric or PIN unlock only as local
protection; handle failed attempts, logout, admin-disabled biometrics, and
offline cached data without bypassing Admin/API authority.

Role And Permission Logic is defined in `role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact
with feature flags as separate gates; suspended users and suspended tenants
fail closed without bypassing tenant isolation.

Audit Logic is defined in `audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

Data Privacy Principles are defined in `data-privacy-principles.md`:
tenant isolation, least privilege, secure local mobile data, secure native
storage, export and deletion boundaries, support access limits, admin
visibility boundaries, privacy-by-default behavior, and mobile diagnostics
privacy limits must protect users and tenants without turning mobile cache,
support views, or audit history into uncontrolled data exposure.

Tenant Lifecycle Logic is defined in `tenant-lifecycle-logic.md`:
tenant creation, onboarding, trial, active, suspended, archived,
billing-blocked, deletion/requested deletion, and restore states must be
Admin/API-owned lifecycle decisions that mobile presents as safe,
tenant-scoped, billing-aware, supportable states without inventing local
tenant authority.

Tenant Admin Logic is defined in `tenant-admin-logic.md`:
tenant admins may manage tenant-scoped users, invitations, delegated settings,
delegated mobile-feature controls, tenant reports, and tenant support workflows
only inside their tenant; platform-only controls, cross-tenant visibility,
global policy, billing authority, lifecycle authority, app-version policy, and
security posture remain Admin/API-owned boundaries.

Multi-Tenant Mobile Logic is defined in `multi-tenant-mobile-logic.md`:
users with more than one tenant choose and remember tenant context through
API-confirmed state; tenant switching, tenant-scoped cache, per-tenant
permissions and feature flags, sync replay, offline behavior, and logout
cleanup must preserve tenant isolation and never turn mobile-local state
into tenant authority.

Offline-First Principles are defined in `offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

Offline UX Logic is defined in `offline-ux-logic.md`:
offline UX must calmly explain banners, disabled online-only actions,
local drafts, pending indicators, retry, sync success or failure,
saved-local versus synced state, and data-loss prevention whenever
connection changes.

Records/Content Module Logic is defined in `records-content-module-logic.md`:
records are tenant-scoped business content with API-owned lifecycle,
notes, attachments, activity, tags, categories, status, offline draft or
sync behavior, admin controls, permissions, feature flags, audit, and
reporting boundaries.

Search Logic is defined in `search-logic.md`:
search stays tenant-scoped, permission-aware, feature-controlled,
privacy-preserving, and explicit about local-cache limits versus
API-authoritative results, including recent searches, saved filters, filtering,
sorting, scan-to-search, offline limits, and admin-controlled boundaries.

Forms And Drafts Logic is defined in `forms-drafts-logic.md`:
mobile forms must stay simple, validated, autosave-aware, offline-draft
safe, API-submitted, admin-controlled, and explicit about local-save versus
server-accepted state so user work is protected without bypassing authority.

Notifications Logic is defined in `notifications-logic.md`:
notification targeting, delivery policy, push behavior, in-app inbox,
read/unread state, deep links, preferences, offline behavior, and tenant or
permission boundaries must remain Admin/API-authoritative and mobile-safe.

Support System Logic is defined in `support-system-logic.md`:
support requests, support messages, attachments, tenant context, support-agent
visibility, audit, notifications, and offline support drafts must remain
tenant-scoped, least-privilege, privacy-safe, and Admin/API-authoritative.

Billing And Plan Logic is defined in `billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature states,
and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

Reporting Logic is defined in `reporting-logic.md`:
admin measurements, tenant-admin measurements, mobile-visible summaries,
privacy boundaries, date ranges, exports, feature usage, sync health,
notification, support, and billing reports must remain tenant-scoped,
permission-aware, privacy-safe, auditable, and Admin/API-authoritative.

Native Feature Strategy is defined in `native-feature-strategy.md`:
NativePHP capability use, logical service boundaries, browser/development
fallbacks, permission education, admin feature-flag control, native failure
UX, and offline sync behavior must remain feature-scoped, tenant-safe,
privacy-aware, fallback-safe, and Admin/API-authoritative.

Camera And Media Logic is defined in `camera-media-logic.md`:
photo capture, media selection, media preview, record/support attachments,
offline media storage, upload queues, feature-flag control, permission
denial, size limits, and privacy behavior must remain tenant-scoped,
permission-aware, fallback-safe, queue-safe, privacy-safe, and
Admin/API-authoritative.

Scanner Logic is defined in `scanner-logic.md`:
QR/barcode scan-to-search, scan-to-create, scan-to-validate, scan history,
offline scanning, invalid scan handling, duplicate scan handling, admin
feature flags, and camera/permission dependency behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear,
duplicate-safe, privacy-safe, and Admin/API-authoritative.

Geolocation Logic is defined in `geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Device, Network, And Diagnostics Logic is defined in `device-network-diagnostics-logic.md`:
device information use, network status use, offline detection, diagnostics
export, support troubleshooting context, diagnostics redaction, admin mobile
device visibility, and user-controlled diagnostics sharing must remain
tenant-scoped, permission-aware, support-scoped, privacy-safe, redacted,
audit-ready, and Admin/API-authoritative.

Module Selection Principles are defined in `module-selection-principles.md`:
optional industry modules such as field service, logistics, ecommerce,
booking, education, events, support, community/messaging, reports, and AI
assistant must be tenant-enabled, plan-controlled, permission-aware,
mobile-hidden when unavailable, feature-flag-safe, documented before
implementation, and Admin/API-authoritative.

Field Service Logic is defined in `field-service-logic.md`:
work order lifecycle, technician mobile flow, check-in/check-out, photos,
notes, future signatures, offline behavior, admin dispatch/control, and
report visibility must remain tenant-enabled, plan-controlled,
permission-aware, offline-clear, evidence-safe, privacy-safe, auditable,
and Admin/API-authoritative.

Booking Logic is defined in `booking-logic.md`:
service selection, availability logic, booking requests, confirmation,
cancellation, reschedule, reminders, admin schedule control, tenant rules,
and mobile offline limitations must remain tenant-enabled, plan-controlled,
permission-aware, availability-safe, schedule-conflict-safe, reminder-safe,
offline-limited, privacy-safe, auditable, and Admin/API-authoritative.

Commerce Logic is defined in `commerce-logic.md`:
catalog browsing, cart behavior, checkout principles, hosted payment
boundaries, order lifecycle, invoice/receipt principles, subscription
upsell, admin product/control, and mobile offline limitations must remain
tenant-enabled, plan-controlled, permission-aware, price-safe,
inventory-safe, hosted-payment-safe, offline-limited, privacy-safe,
auditable, and Admin/API-authoritative.

Messaging And Community Logic is defined in `messaging-community-logic.md`:
conversation behavior, support chat behavior, message attachments,
moderation, reports/abuse flow, notification behavior, offline message
drafts, admin visibility boundaries, and privacy principles must remain
tenant-enabled, plan-controlled, permission-aware, moderation-ready,
abuse-report-safe, notification-safe, offline-draft-safe, privacy-safe,
auditable, and Admin/API-authoritative.

AI Feature Logic is defined in `ai-feature-logic.md`:
AI assistant purpose, summarization, categorization, smart suggestions,
moderation assistance, report generation assistance, admin AI controls,
tenant opt-in, privacy, and human-review principles must remain
tenant-enabled, plan-controlled, permission-aware, opt-in-only,
provider-neutral, human-reviewed, privacy-safe, audit-ready,
rate-limited, cost-aware, and Admin/API-authoritative.

Acceptance Principles are defined in `acceptance-principles.md`:
every feature must document purpose, admin control, mobile behavior,
API dependency, offline behavior, permission behavior, feature flag
behavior, tenant behavior, error behavior, security behavior, and
documentation requirements before implementation planning begins.

Risk Map is defined in `risk-map.md`:
API dependency, offline sync, tenant isolation, mobile secure storage,
NativePHP plugin availability, app store releases, forced updates,
feature flag mistakes, billing restrictions, admin misconfiguration,
support access, privacy, and data conflicts must document prevention
principles and documentation requirements before implementation.

Testing Strategy Principles are defined in `testing-strategy-principles.md`:
future tests for API contracts, admin controls, mobile feature visibility,
permissions, feature flags, remote config, authentication, tenant isolation,
offline sync, conflict behavior, native feature fallbacks, notification flows,
billing rules, and app version rules must map to documented authority, risk,
and user-visible behavior before implementation.

Release And Versioning Principles are defined in `release-versioning-principles.md`:
API versioning, mobile app versioning, admin releases, feature rollout,
rollback, app store release, forced update, documentation update, and Git
change-history decisions must preserve documented authority, compatibility,
rollback, support, audit, and user-visible behavior before release.

Documentation Audit is defined in `documentation-audit.md`:
project documentation for two-system architecture, Admin/API authority, mobile
client execution, API-first communication, feature flags, remote config,
tenancy, permissions, offline sync, NativePHP features, notifications, billing,
support, reports, security, risks, and release principles must use consistent
authority language and resolve contradictions before implementation.

Feature Dependency Map is defined in `feature-dependency-map.md`:
major features must document dependencies on authentication, tenant context,
permissions, feature flags, remote config, API availability, offline cache,
NativePHP permissions, subscription plan, and admin settings before
implementation planning or release decisions.

Logistics Delivery Logic is defined in `logistics-delivery-logic.md`:
delivery job lifecycle, pickup flow, drop-off flow, proof of delivery,
scan validation, location check-in, failed delivery reasons, offline
behavior, and admin monitoring must remain tenant-enabled, plan-controlled,
permission-aware, scan-safe, location-purpose-limited, offline-clear,
privacy-safe, auditable, and Admin/API-authoritative.

Voice Note Logic is defined in `voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

Sync Lifecycle Logic is defined in `sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

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

The Admin/API system is planned as the SaaS control plane:

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

The Mobile client system is planned as the managed edge client:

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
- Use [Native Feature Strategy](native-feature-strategy.md) before adding,
  enabling, disabling, or replacing NativePHP capability groups.

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
