# Admin Control Center Logic

Updated: 2026-06-26

This document defines the logic of the Mobile Lara Admin Control Center. It explains how admins should control tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance mode, force update, sync behavior, notifications, reports, billing, and support. It is documentation only and does not define database fields, migrations, routes, controllers, Livewire components, Filament resources, policies, jobs, services, providers, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Documentation-First Architecture](documentation-first-architecture.md), [API-First Principles](api-first-principles.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Target User Roles](user-roles.md), and [SaaS Value Map](saas-value-map.md): the Admin Control Center is the operational surface for server authority, and mobile receives outcomes through API.

Use [Feature Flag Logic](feature-flag-logic.md) whenever a control changes important mobile feature availability, rollout, disabled states, plan limits, or user-level access.

Use [Remote Configuration Logic](remote-configuration-logic.md) whenever a control changes safe runtime mobile behavior, config defaults, tenant overrides, mobile caching, offline behavior, or invalid-config fallback.

Use [Mobile Version Control Logic](mobile-version-control-logic.md) whenever a control changes minimum supported versions, optional updates, forced updates, maintenance mode, outdated app behavior, store links, update messages, support context, or stale-client protection.

Use [Admin Safety Principles](admin-safety-principles.md) whenever a control is dangerous, broad, destructive, security-sensitive, billing-impacting, mobile-blocking, support-relevant, or tenant-sensitive enough to require confirmation, audit history, impact preview, mobile impact preview, rollback, or strict tenant isolation.

Use [Mobile UX Principles](mobile-ux-principles.md) whenever an admin control changes NativePHP navigation, simple screens, loading/offline states, thumb-friendly actions, secure session states, feature visibility, or native permission education.

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

## Control Center Statement

The Admin Control Center is the operational control surface for the SaaS.

It is where authorized admin roles manage tenant access, user lifecycle, role and permission policy, mobile features, remote configuration, app-version rules, maintenance mode, force-update policy, sync behavior, notifications, reports, billing, and support.

The control center does not make mobile independent. It makes mobile governed.

## Core Control Principles

1. **Control is scoped** - Every control has platform, tenant, role, user, device, app-version, feature, billing, support, report, or sync scope.
2. **Control is authorized** - Only the right admin role can view or change a control.
3. **Control is explainable** - Every control should have a reason, owner, expected mobile effect, support meaning, and rollback expectation where relevant.
4. **Control reaches mobile through API** - Admin changes become mobile behavior through API context, feature state, config, version rules, entitlement outcomes, errors, or sync policy.
5. **Control is auditable** - Sensitive controls should record actor, scope, old value, new value, reason, time, and affected area.
6. **Control is reversible where possible** - Features, config, version rules, notifications, sync policy, and maintenance states should have a rollback or recovery path.
7. **Control fails closed** - If a control cannot be evaluated safely, mobile should receive blocked, disabled, retry-later, maintenance, or contact-support behavior.
8. **Control is tenant-safe** - No control may leak another tenant's users, data, reports, billing, support, notifications, or sync state.
9. **Control is documented first** - Every control must document mobile effect, API dependency, permission owner, sync/offline impact, risks, and support meaning before implementation.

## Admin Control Ownership Contract

Every Admin Control Center planning decision should name the control area, control scope, mobile effect, API outcome, support meaning, audit expectation, and rollback path before implementation planning.

| Control area | Admin control principle | Required mobile/API outcome |
| --- | --- | --- |
| Tenants | Admins control tenant lifecycle, status, isolation posture, settings, plan relationship, support tier, and tenant-scoped feature availability. | Tenant context, active/limited/suspended/maintenance state, allowed tenant choices, tenant-safe messages, and tenant-scoped feature/config/sync/report limits. |
| Users | Admins control invitation, activation, suspension, reactivation, recovery, profile/account state, and device/user association. | Account state, profile context, invited/suspended/recovery/pre-login behavior, session/device outcomes, and safe support context. |
| Roles | Admins control least-privilege role bundles, assignment rules, tenant manager scope, support boundaries, and billing boundaries. | Role-derived capability state, navigation visibility, denied/allowed outcomes, and no mobile-side role authority. |
| Permissions | Admins control granular abilities, grant/revoke rules, approval requirements, denied states, high-risk reason capture, and audit expectations. | Allowed, denied, blocked, approval-required, or support-contact states through API; offline replay rechecks permission state. |
| Mobile features | Admins control enablement, disablement, rollout, rollback, plan/role/version/device/cohort gates, and emergency disablement. | Visible, hidden, disabled, blocked, deprecated, update-required, beta, emergency-disabled, or offline-limited feature states. |
| Remote config | Admins control safe runtime values, scope, version, defaults, compatibility, tenant overrides, rollback, and invalid-config behavior. | Resolved config copy, config version, freshness, fallback state, UI copy/limits, sync/offline rules, and support-safe explanations. |
| App versions | Admins control supported, recommended, deprecated, blocked, internal-only, minimum version, compatibility, and API contract assumptions. | Update prompt, warning, limited mode, blocked mode, release/store guidance, stale-client errors, or normal operation. |
| Maintenance mode | Admins control platform, tenant, feature, API, sync, or notification maintenance state, schedule, affected operations, messages, and rollback. | Maintenance banner, limited mode, blocked action, retry-later state, safe offline policy, and support/contact path. |
| Force update | Admins control hard, soft, phased, tenant-scoped, platform-specific, feature-specific, and version-specific update requirements. | Required update, recommended update, deprecated warning, app-store/deployment instructions, and old-version protection. |
| Sync behavior | Admins control offline eligibility, queueable actions, replay windows, retry limits, stale thresholds, conflict modes, and policy blocks. | Offline, draft, pending, synced, conflict, failed, blocked, stale, retry-later, or replay behavior with API-owned decisions. |
| Notifications | Admins control templates, channels, targeting, quiet hours, priority, escalation, suppression, and delivery visibility. | Device registration requirements, notification inbox/display state, safe local history, and tenant/role/feature/billing/version-safe targeting outcomes. |
| Reports | Admins control report definitions, scopes, aggregates, exports, dashboard visibility, and operational metrics. | Personal/workflow summaries only when API allows them, export limits, freshness, and no cross-tenant leaks. |
| Billing | Admins control plans, quotas, entitlements, trials, renewals, restrictions, failed-payment outcomes, billing contacts, and support tier. | Allowed, blocked, quota-warning, entitlement-limited, contact-admin, contact-support, or upgrade/contact-sales states. |
| Support | Admins control case state, safe diagnostics, escalation, support role visibility, recovery actions, retry guidance, and config-refresh guidance. | Support request flow, diagnostic submission, case status, recovery guidance, safe context, and audit-visible support actions. |

This contract is intentionally principle-level. It does not create admin panels, routes, controllers, Livewire components, Filament resources, policies, jobs, services, database fields, migrations, provider integrations, or application logic.

## Control Map

| Control area | Admin controls | Mobile receives |
| --- | --- | --- |
| Tenants | Lifecycle, status, settings, plan relationship, isolation posture, support tier, feature availability. | Tenant context, blocked/limited/active state, allowed tenant choices, tenant labels. |
| Users | Invitations, activation, suspension, recovery, profile/account state, device/user association. | Account state, profile context, invited/suspended/recovery/pre-login behavior. |
| Roles | Role bundles, role assignment rules, tenant manager scope, support/billing boundaries. | Role-derived capability state, not role authority. |
| Permissions | Granular abilities, grant/revoke controls, approval requirements, denied states. | Allowed/denied/blocked capability state and clear next action. |
| Mobile features | Enablement, disablement, rollout, rollback, plan/role/version/device/cohort gating. | Visible, hidden, disabled, blocked, deprecated, update-required, or offline-limited feature state. |
| Remote config | Config values, config version, scope, defaults, compatibility, rollback. | Cached config copy, config version, UI behavior, limits, copy, sync/offline rules. |
| App versions | Supported, recommended, deprecated, blocked, internal-only, minimum API contract. | Update prompt, warning, limited mode, blocked mode, or normal operation. |
| Maintenance mode | Platform/tenant/feature/API maintenance state, schedule, message, affected operations. | Maintenance banner, blocked actions, retry-later state, support/contact path. |
| Force update | Hard update, soft update, phased update, version-specific block, platform-specific rule. | Required update, recommended update, deprecated warning, app-store/deployment instructions. |
| Sync behavior | Offline eligibility, queueable actions, replay windows, retry limits, conflict modes, stale thresholds. | Offline/draft/pending/synced/conflict/failed/retry-later state and replay behavior. |
| Notifications | Templates, channels, targeting, quiet hours, priority, escalation, delivery visibility. | Device registration requirements, received notification display, safe local history. |
| Reports | Report definitions, scopes, aggregates, exports, dashboard visibility, operational metrics. | Personal/workflow summaries only when API allows them. |
| Billing | Plans, quotas, entitlements, trials, renewals, restrictions, failed-payment outcomes. | Allowed, blocked, quota-warning, contact-admin, contact-support, upgrade/contact-sales state. |
| Support | Cases, safe diagnostics, escalation, case state, support visibility, recovery actions. | Support request flow, diagnostic submission, retry/config-refresh guidance, case status. |

## Tenants

Admin control of tenants defines the commercial, security, support, billing, reporting, and configuration boundary.

Principles:

- Tenant lifecycle states should be explicit: active, onboarding, limited, suspended, disabled, archived, or maintenance.
- Tenant controls should never affect other tenants unless the control is explicitly platform-wide.
- Tenant settings can alter mobile behavior without forking the mobile app.
- Tenant status can block mobile boot, block specific features, limit sync, change support prompts, or expose maintenance state.
- Tenant controls should document billing, support, feature, notification, report, and sync effects.

## Users

Admin control of users defines who can enter the product and what account state mobile should show.

Principles:

- User controls include invitation, activation, suspension, reactivation, recovery, profile state, and device association.
- User status overrides normal role access when invited, suspended, recovery-limited, or pre-login.
- Admin user actions should produce mobile account-state outcomes through API.
- Suspending or recovering a user should document mobile session, cache, queue, support, and sync effects.
- Support-visible user context should be safe and role-scoped.

## Roles

Admin control of roles defines reusable responsibility bundles.

Principles:

- Roles should be least-privilege bundles, not just UI labels.
- Role changes should document which mobile capabilities become visible, hidden, disabled, blocked, or support-only.
- Tenant-scoped roles must not gain platform authority.
- Support and billing roles should be job-specific and not become broad admin roles.
- Role changes should be auditable when they affect sensitive access.

## Permissions

Admin control of permissions defines granular abilities.

Principles:

- Every permission should document who controls it, who can receive it, what it exposes, and how mobile receives the outcome.
- Permission controls should apply server-side through API, not only by hiding buttons.
- Permission denials should produce clear mobile states without leaking hidden data.
- Permission changes while mobile is offline must be rechecked during sync replay.
- High-risk permissions should have reason capture, audit expectation, and support explanation.

## Mobile Features

Admin control of mobile features defines what capabilities exist for each tenant, plan, role, user, device, version, or cohort.

Principles:

- Features can be enabled, disabled, blocked, deprecated, phased, or emergency-disabled.
- Important mobile features should follow the priority, disabled-state, rollout, impact, and plan-limit rules in [Feature Flag Logic](feature-flag-logic.md).
- A mobile feature is not ready unless admin control, API purpose, mobile UX, offline behavior, support, audit, and rollback are documented.
- Feature controls should explain disabled and blocked states for mobile users.
- Feature rollout should support internal, tenant, cohort, plan, role, version, and device constraints.
- Emergency disablement should fail closed and be support-visible.

## Remote Config

Admin control of remote config lets the platform adjust mobile behavior safely.

Principles:

- Remote config should be scoped, versioned, compatible, defaulted, and reversible.
- Remote config should follow [Remote Configuration Logic](remote-configuration-logic.md): only safe runtime behavior is configurable, tenant overrides stay inside global/plan/permission/version/safety limits, and missing or invalid config fails safely.
- Config can control copy, limits, workflow options, offline eligibility, sync rules, native permission purpose text, maintenance messages, or support instructions.
- Config should not become hidden business logic.
- Config changes that alter product behavior should be auditable and support-visible.
- Mobile may cache config, but API policy remains final.

## App Versions

Admin control of app versions keeps stale mobile clients safe.

Principles:

- Version states should include supported, recommended update, deprecated, blocked, and internal-only.
- Version controls should follow [Mobile Version Control Logic](mobile-version-control-logic.md): admins define minimum supported versions, optional update prompts, forced updates, maintenance state, store links, update messages, support context, audit, rollback, and old-version protection.
- Version rules should consider platform, tenant, feature risk, API contract, security, billing, sync, and rollout cohort.
- Deprecation should happen before removal when feasible.
- Support should be able to see which version rule affected a user.
- Version control should protect API assumptions and NativePHP capability compatibility.

## Maintenance Mode

Admin control of maintenance mode protects the product during planned or emergency operational windows.

Principles:

- Maintenance can be platform-wide, tenant-scoped, feature-scoped, API-scoped, sync-scoped, or notification-scoped.
- Maintenance controls should document start, expected end, affected operations, user-facing message, support message, and rollback plan.
- Mobile should receive maintenance state through API and show clear retry-later or limited-mode behavior.
- Offline work during maintenance should be blocked, draft-only, or queueable only by policy.
- Maintenance should not leak tenant-private operational details.

## Force Update

Admin control of force update protects users and the platform when old builds are unsafe.

Principles:

- Force update can be hard, soft, phased, tenant-scoped, platform-specific, or feature-specific.
- Hard update blocks normal operation and gives a safe next step.
- Soft update allows continued use with warning or limited feature access.
- Force-update policy should document why the update is required, what breaks if ignored, and what support should say.
- Force update should be coordinated with app-version policy, API contract changes, feature flags, and NativePHP capability changes.

## Sync Behavior

Admin control of sync behavior defines how offline work reconciles.

Principles:

- Admin/API controls which features can be read-only offline, draft-only offline, queueable offline, or online-only.
- Sync rules should define replay windows, retry limits, backoff, stale-data thresholds, metered-network behavior, maintenance blocks, and conflict modes.
- Mobile receives sync policy and renders pending, synced, conflict, failed, blocked, stale, retry-later, or offline state.
- Sync replay must recheck tenant, user, role, permission, billing, feature, version, and server state.
- Conflict decisions belong to API, while mobile presents options.

## Notifications

Admin control of notifications defines who gets which message, when, and why.

Principles:

- Notification controls include templates, channels, targeting, quiet hours, priority, escalation, suppression, and delivery visibility.
- Targeting must respect tenant, role, permission, feature, billing, version, device, and user preference boundaries.
- Mobile handles permission prompts and local display, not targeting or delivery truth.
- Notifications should be support-visible when they affect user action or support cases.
- Sensitive notification changes should be auditable.

## Reports

Admin control of reports defines operational visibility.

Principles:

- Reports should be scoped by platform, tenant, role, support case, billing purpose, aggregation level, and export permission.
- Reports should measure adoption, devices, app versions, sync health, notification health, feature rollout, support load, billing posture, security events, and tenant health.
- Mobile should receive personal or workflow-level summaries only when API grants them.
- Reports must not leak cross-tenant or private user data.
- Export and dashboard visibility should be controlled and auditable.

## Billing

Admin control of billing defines commercial access.

Principles:

- Billing controls include plans, quotas, entitlements, trials, renewals, restrictions, failed-payment outcomes, billing contacts, and support tier.
- Billing outcomes should map to feature availability and API-enforced entitlement states.
- Mobile sees product outcomes, not payment-provider internals.
- Offline replay must recheck entitlement and quota before API acceptance.
- Billing controls should be explainable to tenant admins, support, and billing roles.

## Support

Admin control of support defines safe recovery and diagnosis.

Principles:

- Support controls include case state, safe diagnostics, escalation, support role visibility, recovery actions, retry guidance, and config-refresh guidance.
- Support visibility must be case-scoped, tenant-scoped, role-scoped, and diagnostic-safe.
- Mobile can submit support requests and safe diagnostics, but Admin/API owns case state.
- Support actions that affect users, tenants, devices, config, sync, security, or billing should be auditable.
- Support should connect app version, config version, feature state, sync state, notification state, billing outcome, and recent safe errors.

## Cross-Control Rules

Controls should not be designed in isolation.

| If this changes | Also document |
| --- | --- |
| Tenant status | Mobile boot, feature availability, billing, support, reports, sync, notifications. |
| User status | Session, device trust, local cache, offline queue, support, audit. |
| Role or permission | Mobile navigation, API authorization, sync replay, support explanation, audit. |
| Feature flag | API purpose, remote config, mobile visibility, support, reports, rollback. |
| Remote config | Compatibility, version, mobile copy, support meaning, rollback. |
| App version or force update | API compatibility, NativePHP capability, rollout, support, blocked/deprecated mobile state. |
| Maintenance mode | Offline behavior, sync replay, notifications, support, retry-later state. |
| Sync policy | Offline UX, replay, conflicts, reports, support diagnostics. |
| Billing | Entitlements, quotas, mobile blocked states, support, reports, notifications. |

## Admin Control Checklist

Use this checklist before planning a future Admin Control Center slice.

| Question | Required answer |
| --- | --- |
| Which control area is this? | Tenant, user, role, permission, feature, config, version, maintenance, force update, sync, notification, report, billing, or support. |
| Who can control it? | Platform owner, super admin, tenant admin, tenant manager, support, billing, or another role is named. |
| What scope applies? | Platform, tenant, plan, role, user, device, version, cohort, support case, billing, report, or sync scope is explicit. |
| What mobile effect occurs? | Visible, hidden, disabled, blocked, deprecated, update-required, maintenance, retry-later, offline-limited, pending, synced, conflict, or failed state is named. |
| What API context changes? | Permissions, feature flags, config, version rules, user context, tenant context, sync policy, notification policy, support state, or entitlement outcome is explicit. |
| What is audited? | Actor, scope, old value, new value, reason, time, and affected area are named for sensitive changes. |
| What support can explain? | Support-safe context and next actions are named. |
| What happens offline? | Cache, draft, queue, block, replay, conflict, retry, or failed behavior is documented. |
| What risk exists? | Risk and mitigation/rollback/support expectation are recorded before coding. |
| What is out of scope? | Schema, migrations, endpoints, policies, components, resources, services, jobs, providers, and code stay deferred until implementation. |

## Risks

| Risk | Control response |
| --- | --- |
| Control center becomes generic admin software | Keep controls tied to mobile behavior, SaaS authority, tenant safety, support, billing, reports, and sync. |
| Controls become UI-only | API and Admin/API policy remain final. |
| Admin changes surprise mobile users | Document mobile effect, user-facing state, support explanation, and rollback. |
| Controls overlap and conflict | Use cross-control rules and precedence expectations before coding. |
| Tenant data leaks through controls | Scope every control by tenant, role, support purpose, billing purpose, report aggregation, and export permission. |
| Remote config becomes hidden logic | Keep config scoped, versioned, compatible, auditable, and documented. |
| Force update is overused | Reserve hard blocks for security, API compatibility, unsafe data, or unsupported capability risk. |
| Sync policy causes data loss | Treat offline work as intent, document replay and conflict behavior, and keep support-visible recovery paths. |
| Reports expose too much | Keep report scope, aggregation, exports, and role visibility explicit. |
| Support becomes broad admin access | Keep support case-scoped and diagnostic-safe. |

## Success Test

The Admin Control Center logic is successful when an authorized admin can change tenant, user, role, permission, feature, config, version, maintenance, force-update, sync, notification, report, billing, or support behavior; the mobile client receives a clear API outcome; support can explain the result; audit can reconstruct sensitive changes; offline behavior remains safe; and tenant boundaries remain protected.
