# ADR-0001: Admin/API Control Plane And Native Mobile Client

## Status

Accepted

## Date

2026-06-25

## Context

Mobile Lara needs to support a SaaS business where administrators control tenants, users, permissions, remote config, feature flags, app versions, notifications, billing, reports, support, and sync behavior. The mobile application must work through the API and use NativePHP for device capabilities.

The key tension is authority. Mobile apps need local resilience and offline behavior, but SaaS business rules must stay server-controlled and tenant-safe.

The product vision is remote control with local resilience: admin users manage policy and operations centrally, while mobile users get a simple controlled app for day-to-day work. See [Product Vision](../product-vision.md).

The product positioning is deliberately combined: SaaS control center, mobile workforce/client platform, API-first system, offline-capable mobile system, feature-controlled platform, and tenant-based product. See [Product Positioning](../product-positioning.md).

The architecture must also satisfy [Core Product Principles](../product-principles.md): admin authority, API-first mobile communication, feature control, tenant isolation, useful offline behavior, secure defaults, simple mobile UX, documentation-first decisions, and modular expansion.

The documentation-first architecture model is defined in [Documentation-First Architecture](../documentation-first-architecture.md). The architecture must require written feature behavior, admin mobile effects, mobile screen API dependencies, sync behavior, permission ownership, risks, and acceptance criteria before implementation.

The Admin Control Center model is defined in [Admin Control Center Logic](../admin-control-center-logic.md). The architecture must require scoped, authorized, auditable, API-driven controls for tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance mode, force update, sync behavior, notifications, reports, billing, and support.

The feature flag model is defined in [Feature Flag Logic](../feature-flag-logic.md). The architecture must require important mobile features to resolve global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, emergency, and offline decisions into mobile-safe API states.

The remote configuration model is defined in [Remote Configuration Logic](../remote-configuration-logic.md). The architecture must require runtime-configurable behavior to define safe config types, defaults, scope, tenant overrides, mobile receive/cache behavior, offline behavior, invalid-config fallback, admin safety, support meaning, audit expectations, and rollback before implementation.

The mobile version control model is defined in [Mobile Version Control Logic](../mobile-version-control-logic.md). The architecture must require app-version-sensitive behavior to define minimum supported versions, optional updates, forced updates, maintenance mode, outdated-client responses, store links, update messages, support context, audit expectations, rollback, and old-version protection before implementation.

The target role model is defined in [Target User Roles](../user-roles.md). The architecture must keep platform owner, super admin, tenant admin, tenant manager, support agent, billing manager, mobile user, invited user, suspended user, and guest/pre-login user boundaries distinct.

The SaaS value map is defined in [SaaS Value Map](../saas-value-map.md). The architecture must preserve value for platform owner, tenant business, tenant admin, mobile worker/client, support team, and billing/operations team without giving each stakeholder the same visibility or control.

The system boundary is defined in [Two-System Boundary Logic](../two-system-boundary.md). The architecture must keep Admin/API authority separate from mobile execution, cache, drafts, queues, native capabilities, and offline presentation.

The API-first model is defined in [API-First Principles](../api-first-principles.md). The architecture must keep mobile communication API-only, API responses predictable, mobile feature API purpose explicit, operating context complete enough for mobile behavior, errors mobile-friendly, sync/conflict behavior first-class, and tenant boundaries protected server-side.

The Admin/API responsibility model is defined in [Admin/API Responsibilities](../admin-api-responsibilities.md). The architecture must keep tenant management, users and permissions, admin panel operations, API contracts, feature control, remote configuration, mobile version rules, notification orchestration, billing/subscription logic, support operations, reporting, audit history, conflict decisions, and security enforcement in the control plane.

The mobile-client responsibility model is defined in [Mobile Client Responsibilities](../mobile-client-responsibilities.md). The architecture must keep mobile user experience, secure local session, local cache, offline actions, NativePHP device features, mobile navigation, mobile permissions UX, sync status display, local drafts, local user feedback, and feature visibility in the managed client without making them authority.

The mobile UX model is defined in [Mobile UX Principles](../mobile-ux-principles.md). The architecture must keep NativePHP navigation, simple screens, clear loading/offline states, thumb-friendly controls, minimum typing, fast actions, secure session behavior, feature visibility, and native permission education aligned with API-derived authority.

Mobile App Shell Logic is defined in `../mobile-app-shell-logic.md`:
shell states must coordinate welcome, authenticated, locked, offline, maintenance, forced update, tenant
switching, sync-in-progress, permission-blocked, and feature-disabled behavior
before implementation.

Mobile Dashboard Logic is defined in `../mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Mobile Settings Logic is defined in `../mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `../mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

Authentication Principles are defined in `../authentication-principles.md`:
mobile login must happen through the API only; access and refresh tokens must
use secure storage; refresh, logout, logout-all-devices, tenant selection,
session expiry, offline already-authenticated behavior, and server revocation
must preserve Admin/API authority before implementation.

Mobile App Lock Principles are defined in `../mobile-app-lock-principles.md`:
the mobile client must lock on security-sensitive lifecycle, timeout,
account, tenant, offline-cache, and admin-policy conditions; require
confirmation for sensitive areas; use biometric or PIN unlock only as local
protection; handle failed attempts, logout, admin-disabled biometrics, and
offline cached data without bypassing Admin/API authority.

Role And Permission Logic is defined in `../role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact
with feature flags as separate gates; suspended users and suspended tenants
fail closed without bypassing tenant isolation.

Audit Logic is defined in `../audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

Data Privacy Principles are defined in `../data-privacy-principles.md`:
tenant isolation, least privilege, secure local mobile data, secure native
storage, export and deletion boundaries, support access limits, admin
visibility boundaries, privacy-by-default behavior, and mobile diagnostics
privacy limits must protect users and tenants without turning mobile cache,
support views, or audit history into uncontrolled data exposure.

Tenant Lifecycle Logic is defined in `../tenant-lifecycle-logic.md`:
tenant creation, onboarding, trial, active, suspended, archived,
billing-blocked, deletion/requested deletion, and restore states must be
Admin/API-owned lifecycle decisions that mobile presents as safe,
tenant-scoped, billing-aware, supportable states without inventing local
tenant authority.

Tenant Admin Logic is defined in `../tenant-admin-logic.md`:
tenant admins may manage tenant-scoped users, invitations, delegated settings,
delegated mobile-feature controls, tenant reports, and tenant support workflows
only inside their tenant; platform-only controls, cross-tenant visibility,
global policy, billing authority, lifecycle authority, app-version policy, and
security posture remain Admin/API-owned boundaries.

Multi-Tenant Mobile Logic is defined in `../multi-tenant-mobile-logic.md`:
users with more than one tenant choose and remember tenant context through
API-confirmed state; tenant switching, tenant-scoped cache, per-tenant
permissions and feature flags, sync replay, offline behavior, and logout
cleanup must preserve tenant isolation and never turn mobile-local state
into tenant authority.

Offline-First Principles are defined in `../offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

Offline UX Logic is defined in `../offline-ux-logic.md`:
offline UX must calmly explain banners, disabled online-only actions,
local drafts, pending indicators, retry, sync success or failure,
saved-local versus synced state, and data-loss prevention whenever
connection changes.

Records/Content Module Logic is defined in `../records-content-module-logic.md`:
records are tenant-scoped business content with API-owned lifecycle,
notes, attachments, activity, tags, categories, status, offline draft or
sync behavior, admin controls, permissions, feature flags, audit, and
reporting boundaries.

Search Logic is defined in `../search-logic.md`:
search stays tenant-scoped, permission-aware, feature-controlled,
privacy-preserving, and explicit about local-cache limits versus
API-authoritative results, including recent searches, saved filters, filtering,
sorting, scan-to-search, offline limits, and admin-controlled boundaries.

Forms And Drafts Logic is defined in `../forms-drafts-logic.md`:
mobile forms must stay simple, validated, autosave-aware, offline-draft
safe, API-submitted, admin-controlled, and explicit about local-save versus
server-accepted state so user work is protected without bypassing authority.

Notifications Logic is defined in `../notifications-logic.md`:
notification targeting, delivery policy, push behavior, in-app inbox,
read/unread state, deep links, preferences, offline behavior, and tenant or
permission boundaries must remain Admin/API-authoritative and mobile-safe.

Support System Logic is defined in `../support-system-logic.md`:
support requests, support messages, attachments, tenant context, support-agent
visibility, audit, notifications, and offline support drafts must remain
tenant-scoped, least-privilege, privacy-safe, and Admin/API-authoritative.

Billing And Plan Logic is defined in `../billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature states,
and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

Reporting Logic is defined in `../reporting-logic.md`:
admin measurements, tenant-admin measurements, mobile-visible summaries,
privacy boundaries, date ranges, exports, feature usage, sync health,
notification, support, and billing reports must remain tenant-scoped,
permission-aware, privacy-safe, auditable, and Admin/API-authoritative.

Native Feature Strategy is defined in `../native-feature-strategy.md`:
NativePHP capability use, logical service boundaries, browser/development
fallbacks, permission education, admin feature-flag control, native failure
UX, and offline sync behavior must remain feature-scoped, tenant-safe,
privacy-aware, fallback-safe, and Admin/API-authoritative.

Camera And Media Logic is defined in `../camera-media-logic.md`:
photo capture, media selection, media preview, record/support attachments,
offline media storage, upload queues, feature-flag control, permission
denial, size limits, and privacy behavior must remain tenant-scoped,
permission-aware, fallback-safe, queue-safe, privacy-safe, and
Admin/API-authoritative.

Scanner Logic is defined in `../scanner-logic.md`:
QR/barcode scan-to-search, scan-to-create, scan-to-validate, scan history,
offline scanning, invalid scan handling, duplicate scan handling, admin
feature flags, and camera/permission dependency behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear,
duplicate-safe, privacy-safe, and Admin/API-authoritative.

Geolocation Logic is defined in `../geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Device, Network, And Diagnostics Logic is defined in `../device-network-diagnostics-logic.md`:
device information use, network status use, offline detection, diagnostics
export, support troubleshooting context, diagnostics redaction, admin mobile
device visibility, and user-controlled diagnostics sharing must remain
tenant-scoped, permission-aware, support-scoped, privacy-safe, redacted,
audit-ready, and Admin/API-authoritative.

Module Selection Principles are defined in `../module-selection-principles.md`:
optional industry modules such as field service, logistics, ecommerce,
booking, education, events, support, community/messaging, reports, and AI
assistant must be tenant-enabled, plan-controlled, permission-aware,
mobile-hidden when unavailable, feature-flag-safe, documented before
implementation, and Admin/API-authoritative.

Field Service Logic is defined in `../field-service-logic.md`:
work order lifecycle, technician mobile flow, check-in/check-out, photos,
notes, future signatures, offline behavior, admin dispatch/control, and
report visibility must remain tenant-enabled, plan-controlled,
permission-aware, offline-clear, evidence-safe, privacy-safe, auditable,
and Admin/API-authoritative.

Booking Logic is defined in `../booking-logic.md`:
service selection, availability logic, booking requests, confirmation,
cancellation, reschedule, reminders, admin schedule control, tenant rules,
and mobile offline limitations must remain tenant-enabled, plan-controlled,
permission-aware, availability-safe, schedule-conflict-safe, reminder-safe,
offline-limited, privacy-safe, auditable, and Admin/API-authoritative.

Commerce Logic is defined in `../commerce-logic.md`:
catalog browsing, cart behavior, checkout principles, hosted payment
boundaries, order lifecycle, invoice/receipt principles, subscription
upsell, admin product/control, and mobile offline limitations must remain
tenant-enabled, plan-controlled, permission-aware, price-safe,
inventory-safe, hosted-payment-safe, offline-limited, privacy-safe,
auditable, and Admin/API-authoritative.

Logistics Delivery Logic is defined in `../logistics-delivery-logic.md`:
delivery job lifecycle, pickup flow, drop-off flow, proof of delivery,
scan validation, location check-in, failed delivery reasons, offline
behavior, and admin monitoring must remain tenant-enabled, plan-controlled,
permission-aware, scan-safe, location-purpose-limited, offline-clear,
privacy-safe, auditable, and Admin/API-authoritative.

Voice Note Logic is defined in `../voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

Sync Lifecycle Logic is defined in `../sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `../conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

## Decision

Use a two-system architecture:

1. **Admin/API system** - Laravel API plus Livewire admin panel. This system is the SaaS control plane and source of authority.
2. **Mobile client system** - Laravel plus Livewire running through NativePHP Mobile. This system is the managed edge client and local executor.

The mobile client must consume server-provided boot config, remote config, feature flags, permissions, app-version policy, and sync policy. Local mobile state can improve resilience and UX, but it cannot grant business authority.

The API must remain the mobile client's only trusted communication path to Admin/API. Future endpoint design should follow the principles in [API-First Principles](../api-first-principles.md), but this ADR does not define endpoint details.

Future implementation must follow [Documentation-First Architecture](../documentation-first-architecture.md) before coding any feature, admin control, mobile screen, sync behavior, permission, or risk-sensitive change.

Future Admin Control Center implementation must follow [Admin Control Center Logic](../admin-control-center-logic.md) before coding tenant, user, role, permission, feature, config, version, maintenance, force-update, sync, notification, report, billing, or support controls.

Future feature flag implementation must follow [Feature Flag Logic](../feature-flag-logic.md) before coding mobile feature availability, global/tenant/user priority, disabled states, rollout, plan limits, support, audit, or offline behavior.

Future remote configuration implementation must follow [Remote Configuration Logic](../remote-configuration-logic.md) before coding configurable mobile copy, limits, thresholds, workflow options, offline behavior, sync behavior, NativePHP permission text, support prompts, notification behavior, version messaging, tenant presentation, cache behavior, validation, fallback, audit, or rollback.

Future mobile version control implementation must follow [Mobile Version Control Logic](../mobile-version-control-logic.md) before coding minimum supported versions, optional updates, forced updates, maintenance mode, outdated app behavior, store links, update messages, support context, audit, rollback, or old-version protection.

The Admin/API system must remain the owner of the responsibility areas documented in [Admin/API Responsibilities](../admin-api-responsibilities.md). The mobile client receives outcomes from those responsibilities; it does not duplicate or override them.

The mobile client must remain the owner of the local execution areas documented in [Mobile Client Responsibilities](../mobile-client-responsibilities.md). The Admin/API system controls the policy and canonical outcomes; mobile owns how those outcomes are presented, cached, queued, retried, and explained.

Future NativePHP mobile UX implementation must follow [Mobile UX Principles](../mobile-ux-principles.md) before coding navigation, simple screens, loading/offline states, thumb-friendly controls, minimum-typing flows, fast actions, secure session behavior, feature visibility, or native permission prompts.

This split exists because admin users and mobile users have different jobs. Admin users need tenant-safe operational control, rollout visibility, support context, and auditability. Mobile users need fast workflows, clear state, and native device capabilities without seeing the underlying SaaS machinery.

## Alternatives Considered

### Mobile-first authority

The mobile app would own more rules locally and sync when possible.

- Pros: Fast local UX and fewer API dependencies.
- Cons: Hard to enforce billing, permissions, tenant isolation, feature rollout, and app-version policy.
- Rejected because SaaS control and auditability are core product requirements.

### Admin-only web product with thin mobile wrapper

The admin web app would be the main product and NativePHP would wrap a mostly online web UI.

- Pros: Simpler backend and fewer offline concerns.
- Cons: Weak mobile UX, weak native capability story, poor offline behavior, and poor fit for mobile workers.
- Rejected because the product is positioned as both a SaaS control center and a mobile workforce/client platform.

### Mobile-only product

The product would focus almost entirely on the mobile app, with minimal admin or API control.

- Pros: Faster visible mobile surface.
- Cons: Weak tenant governance, billing enforcement, support visibility, reporting, app-version policy, auditability, and feature rollout.
- Rejected because a tenant-based SaaS product needs central authority and operations.

### Separate technology stacks for admin and mobile

The admin/API and mobile client would use completely different frameworks.

- Pros: Each app could optimize independently.
- Cons: More operational cost, duplicated conventions, more agent/context drift.
- Rejected for now because Laravel + Livewire can serve both admin and mobile surfaces while keeping server-side rules consistent.

### Native-only mobile application

The mobile client would be implemented as a fully native iOS/Android application.

- Pros: Maximum platform control and native convention support.
- Cons: More codebases, duplicated validation/state patterns, slower iteration for a Laravel-centered product, and more work to keep mobile behavior aligned with API/admin rules.
- Rejected for now because NativePHP + Livewire provides enough native capability access while preserving Laravel-first product development.

## Consequences

- Admin/API is responsible for authorization, tenant scope, feature eligibility, billing entitlements, audit trails, API contracts, and sync decisions.
- Mobile is responsible for NativePHP bridges, local SQLite, secure local auth state, offline queues, and mobile UX.
- API design must be versioned, idempotent for replayable writes, and explicit about conflicts.
- Feature work must include admin logic, API behavior, mobile behavior, offline behavior, support behavior, and audit behavior.
- Feature work must identify stakeholder value and connect it to admin control, mobile access, offline sync, notifications, reports, security, feature flags, or an explicit combination.
- Feature work must document product behavior, admin mobile effects, mobile API dependencies, sync behavior, permission ownership, risks, non-goals, and acceptance criteria before implementation.
- Feature work must document Admin Control Center scope, authorized role, mobile effect, API context, audit expectation, support meaning, offline behavior, and risk before implementation.
- Feature work must document feature flag priority, disabled mobile state, admin impact, rollout path, plan-limit behavior, support meaning, audit expectation, offline behavior, and risk before implementation.
- Feature work must document remote configuration type, default, scope, tenant override, mobile cache freshness, offline behavior, invalid-config fallback, support meaning, audit expectation, and rollback before implementation.
- Feature work must document mobile version policy, optional update behavior, forced update behavior, maintenance behavior, store links, update messages, support meaning, audit expectation, and old-version protection before implementation.
- Feature work must identify system ownership: what Admin/API owns, what mobile owns, what is API-only, what can be cached locally, what admin controls remotely, and how offline reconciliation works.
- Feature work must identify API-first behavior: why mobile talks to API, which context is returned, which response states/errors exist, how sync/conflict works, and how tenant scope is protected.
- Feature work must identify responsibility ownership: which Admin/API responsibility owns tenant, user, permission, API, feature, config, version, notification, billing, support, report, audit, conflict, or security behavior.
- Feature work must identify mobile responsibility ownership: which mobile-client responsibility owns UX, local session, cache, offline action, NativePHP capability, navigation, permission prompt, sync display, draft, feedback, or feature visibility behavior.
- Documentation and future implementation should treat local mobile data as cache, draft, queue, or confirmed server copy depending on sync state.
- NativePHP + Livewire remains the chosen mobile approach until a future ADR demonstrates that native-only or another mobile stack is worth the extra operational cost.
- Future architecture changes should preserve the core principles unless a newer ADR explicitly supersedes them.
- Role and account-state boundaries should be treated as authorization requirements, not UI preferences.

## Implementation Boundary

This ADR is documentation only. It does not create schema, migrations, controllers, Livewire components, policies, or application logic.
