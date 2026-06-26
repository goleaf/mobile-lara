# Product Positioning

Updated: 2026-06-25

This document defines how Mobile Lara should be positioned as a product. It explains the product as a SaaS control center, mobile workforce/client platform, API-first system, offline-capable mobile system, feature-controlled platform, and tenant-based product. It is documentation only and does not define database fields, migrations, controllers, components, or application logic.

## Positioning Statement

Mobile Lara is a SaaS control center for tenant-based mobile operations. It gives administrators central control over tenants, users, permissions, features, versions, billing, support, notifications, reports, and sync policy while giving mobile users an API-driven, offline-capable NativePHP client for real work.

The positioning starts from [Product Vision](product-vision.md): the product
exists to provide remote control with local resilience for managed mobile
workflows.

The positioning is protected by [Admin Safety Principles](admin-safety-principles.md):
dangerous admin controls are valuable only when confirmation, audit history,
impact preview, mobile preview, rollback, and tenant isolation keep the control
center trustworthy.

## Positioning Contract

| Position | Product meaning | Boundary |
| --- | --- | --- |
| SaaS control center | The Admin/API system is where tenants, users, roles, permissions, features, versions, billing, support, notifications, reports, sync policy, and security are governed. | The control center creates authority; mobile only receives outcomes through API. |
| Mobile workforce/client platform | The NativePHP + Livewire client is the working surface for tenant-side users who need simple, task-focused mobile workflows. | The mobile client owns local UX, native capability use, cache, drafts, queues, sync display, and feedback, not SaaS authority. |
| API-first system | The API is the trusted contract that turns Admin/API decisions into mobile behavior. | Mobile communicates only with API for server-trusted reads, writes, sync replay, support actions, notification registration, billing outcomes, feature state, config, and version policy. |
| Offline-capable mobile system | The product supports real-world mobile conditions with cache, drafts, queued intents, freshness, retry, pending, and conflict states. | Offline work is local intent until API accepts it; local state never becomes final business truth. |
| Feature-controlled platform | Important mobile capabilities are enabled, disabled, rolled out, blocked, deprecated, plan-limited, version-gated, or emergency-disabled from the control plane. | Feature flags and remote config resolve to mobile-safe states; mobile never resolves raw policy layers as authority. |
| Tenant-based product | Each customer workspace is isolated, configurable, billable, supportable, reportable, and governed without app forks. | Tenant scope is resolved and enforced server-side for every protected API response and admin control. |

The positioning answer is deliberately combined: Mobile Lara should be sold and
planned as one SaaS platform with two coordinated surfaces, not as a dashboard
with a mobile skin and not as a mobile app with a thin settings page.

| Alternative | What it gives | What it misses | Why Mobile Lara is stronger |
| --- | --- | --- | --- |
| Web app only | Fast admin workflows, reporting, billing, and centralized governance. | Native capability access, offline-capable task execution, local sync state, mobile-first ergonomics, and device/version context. | Keeps the web/admin strengths while adding a real mobile execution surface. |
| Mobile app only | A focused app experience and native capabilities. | Tenant administration, permission governance, billing enforcement, support visibility, reporting, audit, rollout, rollback, and app-version policy. | Keeps the mobile strengths while adding SaaS authority, operations, and tenant-safe control. |
| Mobile Lara | Central SaaS authority plus resilient NativePHP mobile execution. | Requires discipline around API contracts, documentation, feature flags, version policy, and sync boundaries. | Solves both sides of the business problem: admins govern centrally, mobile users work locally within server-controlled rules. |

The product is stronger than a normal web app because it reaches mobile workers where work happens. It is stronger than a standalone mobile app because mobile behavior is centrally governed, auditable, version-aware, and tenant-safe.

The positioning depends on [Core Product Principles](product-principles.md): admin authority, API-first mobile behavior, feature control, tenant isolation, useful offline behavior, secure defaults, simple mobile UX, documentation-first decisions, and modular expansion.

It depends on [Documentation-First Architecture](documentation-first-architecture.md): positioning stays useful only when features, admin effects, mobile API dependencies, sync behavior, permission ownership, and risks are documented before implementation.

It depends on [Admin Control Center Logic](admin-control-center-logic.md): admin control stays useful only when tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance, force update, sync behavior, notifications, reports, billing, and support are scoped, authorized, auditable, and API-driven.

It depends on [Feature Flag Logic](feature-flag-logic.md): feature-controlled positioning stays useful only when important mobile features have predictable priority, disabled states, admin impact, rollout safety, and plan-limit behavior.

It depends on [Remote Configuration Logic](remote-configuration-logic.md): config-driven positioning stays useful only when runtime behavior is safe to configure, versioned, scoped, cached carefully, tenant-aware, validated, and fallback-safe.

It depends on [Mobile Version Control Logic](mobile-version-control-logic.md): version-aware positioning stays useful only when minimum supported versions, optional updates, forced updates, maintenance mode, store links, update messages, and old-version protection are controlled by Admin/API.

It also depends on [Target User Roles](user-roles.md): each role has a different responsibility, visibility boundary, and control surface.

It is made measurable by the [SaaS Value Map](saas-value-map.md): each positioning angle must create clear value for platform owner, tenant business, tenant admin, mobile worker/client, support team, or billing/operations team.

It is made operational by [Two-System Boundary Logic](two-system-boundary.md): each positioning angle must respect the split between Admin/API authority and mobile-client execution.

It is made contract-driven by [API-First Principles](api-first-principles.md): mobile communicates only with API, API responses are predictable, every mobile feature has an API purpose, API returns operating context, errors are mobile-friendly, sync/conflict behavior is first-class, and tenant boundaries are protected server-side.

It is made accountable by [Admin/API Responsibilities](admin-api-responsibilities.md): the control center must own tenant management, users and permissions, admin operations, API contracts, feature control, remote configuration, mobile version rules, notifications, billing/subscription logic, support operations, reporting, audit history, conflict decisions, and security enforcement.

It is made usable by [Mobile Client Responsibilities](mobile-client-responsibilities.md): the managed client must own mobile UX, secure local session, cache, offline actions, NativePHP features, navigation, permissions UX, sync display, drafts, local feedback, and feature visibility without owning SaaS authority.

It is made clear by [Mobile UX Principles](mobile-ux-principles.md): the NativePHP client must translate API authority into mobile-first navigation, simple screens, loading/offline states, thumb-friendly controls, minimum typing, fast actions, secure sessions, feature visibility, and native permission education.

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

## Six Product Angles

### SaaS Control Center

Mobile Lara is first a control center. The admin/API system is where the business decides what is allowed, enabled, billable, visible, reportable, and supportable.

The control center owns:

- Tenant lifecycle and tenant settings.
- User, role, permission, and device access.
- Feature flags and remote configuration.
- App-version policy and rollout state.
- Notification, support, report, billing, and sync policy.
- Audit context for changes that affect mobile behavior.

This positioning matters because mobile operations cannot be controlled safely from each device. SaaS control belongs in one server-side operating layer.

The detailed control-plane responsibility map lives in [Admin/API Responsibilities](admin-api-responsibilities.md).

The detailed admin control model lives in [Admin Control Center Logic](admin-control-center-logic.md).

The detailed feature-control model lives in [Feature Flag Logic](feature-flag-logic.md).

The detailed remote-config model lives in [Remote Configuration Logic](remote-configuration-logic.md).

The detailed mobile-version model lives in [Mobile Version Control Logic](mobile-version-control-logic.md).

### Mobile Workforce And Client Platform

Mobile Lara is also a mobile workforce/client platform. The mobile client is not a thin afterthought; it is the working surface for people outside the admin panel.

The mobile platform gives users:

- A focused app experience.
- Native capability access when a feature needs camera, files, microphone, device, network, sharing, or local notification behavior.
- Clear state for offline, pending, synced, conflict, blocked, and deprecated workflows.
- Tenant-safe access to the work they are allowed to perform.

The mobile client exists to serve workers and client-side users without exposing admin machinery.

The detailed mobile responsibility map lives in [Mobile Client Responsibilities](mobile-client-responsibilities.md).

### API-First System

Mobile Lara is API-first because the API is the contract between central authority and mobile execution.

The API-first position means:

- Mobile clients receive permissions, feature state, remote config, version policy, and sync policy through the API.
- Mobile writes pass through server-side validation, authorization, entitlement checks, and idempotency.
- API responses should be shaped, version-aware, and explicit about blocked, stale, conflict, maintenance, and retry states.
- Admin changes become enforceable mobile behavior through API contracts, not local assumptions.

API-first does not mean mobile-only. It means every mobile capability has a server-enforced contract.

The detailed API boundary is defined in [Two-System Boundary Logic](two-system-boundary.md): server-trusted reads, writes, sync replay, billing checks, feature decisions, support actions, and audit events must happen through the API.

The detailed API behavior model is defined in [API-First Principles](api-first-principles.md).

### Offline-Capable Mobile System

Mobile Lara is offline-capable because real mobile work does not always happen on stable networks.

Offline-capable positioning means:

- Local storage can hold cache, drafts, pending actions, local metadata, and sync cursors.
- Queued work is treated as intent until the API accepts it.
- Users can see freshness, pending work, conflicts, and retry states.
- Admin/API policy decides which features can work offline and how replay behaves.
- Support can reason about sync failures and local/server divergence.

Offline capability is a product trust feature, not a license for client-side authority.

### Feature-Controlled Platform

Mobile Lara is feature-controlled because SaaS growth depends on safe rollout, not hardcoded screens.

Feature control includes:

- Global feature defaults.
- Tenant-level enablement.
- User-level preview or exclusion inside global, tenant, plan, permission, version, and safety boundaries.
- Role and permission gates.
- Plan and billing entitlement gates.
- App-version compatibility gates.
- Device or cohort rollout gates.
- Emergency disablement and rollback.

A feature is not product-ready until it has admin control, API enforcement, mobile presentation, offline behavior where relevant, support visibility, and audit context.

Remote configuration complements feature control by tuning safe runtime behavior for allowed features without granting authority, changing billing, or redefining permissions.

### Tenant-Based Product

Mobile Lara is tenant-based because each customer workspace must be isolated, configurable, billable, reportable, and supportable.

Tenant-based positioning means:

- Every admin action has a scope.
- Every API request resolves tenant authority server-side.
- Every mobile boot payload is tenant-aware.
- Reports and support views respect tenant boundaries.
- Billing and feature availability are tenant-aware.
- Tenant configuration can differ without forking the mobile app.

The product scales when new tenants create configuration and policy differences, not new code paths.

## Why Not Only A Web App

Only building a web app would make the control center easier to start, but it would weaken the mobile product.

A web-only product would struggle with:

- Native mobile capability access.
- Offline-capable field workflows.
- Device-aware app-version policy.
- Local queues, drafts, and sync status.
- Mobile-first ergonomics for real-world use.
- App-like trust patterns such as local notifications, camera capture, device state, and secure local access.

The admin web experience is necessary, but it is not enough for workers who need a focused mobile client.

## Why Not Only A Mobile App

Only building a mobile app would make the user surface visible quickly, but it would weaken the SaaS business.

A mobile-only product would struggle with:

- Tenant administration.
- Permission and role governance.
- Billing entitlement enforcement.
- Feature rollout and rollback.
- App-version governance.
- Support visibility.
- Reports and audit trails.
- Central sync and conflict policy.

Mobile without a control center becomes hard to operate as customers, teams, versions, devices, and pricing plans multiply.

## Why The Combined Position Wins

The combined product wins because it treats administration, API authority, mobile UX, offline resilience, feature control, and tenant isolation as one system.

It also wins because the same platform capability creates different stakeholder value without collapsing permissions. For example, feature flags give platform owners rollout safety, tenant businesses controlled adoption, support teams explanation context, billing teams entitlement mapping, and mobile users clear enabled or blocked states.

| Product need | Web-only | Mobile-only | Mobile Lara |
| --- | --- | --- | --- |
| Central tenant control | Strong | Weak | Strong |
| Mobile field usability | Weak | Strong | Strong |
| Native capability access | Weak | Strong | Strong |
| Offline work | Weak | Possible but risky | Controlled and API-reconciled |
| Feature rollout | Medium | Weak | Strong |
| Billing enforcement | Strong | Weak | Strong |
| App-version policy | Weak | Medium | Strong |
| Support and audit context | Strong | Weak | Strong |
| Tenant-based SaaS scale | Medium | Weak | Strong |

Mobile Lara is positioned as the middle path that keeps the web/admin strengths and mobile strengths while avoiding their isolated weaknesses.

## Operating Principles

1. The admin/API system is the SaaS control center.
2. The mobile client is the workforce/client platform.
3. The API is the contract, not a convenience layer.
4. Offline work is local intent until the server accepts it.
5. Features are controlled by policy before they appear as screens.
6. Tenants are the primary commercial and security boundary.
7. Secure-by-default is part of the product position, not a separate hardening phase.
8. Documentation-first architecture records feature behavior, admin mobile effects, screen API dependencies, sync behavior, permission ownership, and risks before coding.
9. Admin Control Center logic records who can control tenants, users, roles, permissions, features, config, versions, maintenance, force update, sync, notifications, reports, billing, and support before coding.
10. Feature Flag Logic records how important mobile features resolve scope, priority, disabled states, rollout, impact, plan limits, support, and offline behavior before coding.
11. Remote Configuration Logic records what can be configured, how mobile receives and caches config, offline behavior, tenant overrides, safe admin changes, and fallback behavior before coding.
12. Mobile Version Control Logic records minimum supported versions, optional updates, forced updates, maintenance mode, outdated-client behavior, store links, update messages, and old-version protection before coding.
13. Role boundaries determine who can see or control each surface.
14. Stakeholder value determines why a feature exists and which outcome it should prove.
15. Web-only is insufficient for mobile work.
16. Mobile-only is insufficient for SaaS governance.
17. Product positioning should guide every future modular feature slice.
18. Two-system boundary rules decide whether a behavior belongs in Admin/API, mobile, local cache, or API-only execution.
19. API-first principles decide the purpose, predictability, context, error, sync/conflict, and tenant-scope expectations for mobile/API behavior.
20. Admin/API responsibility rules decide which control-plane owner must govern tenant, user, API, feature, config, version, notification, billing, support, report, audit, conflict, or security behavior.
21. Mobile-client responsibility rules decide which local experience owner should present UX, session, cache, offline, NativePHP, navigation, permissions, sync, draft, feedback, or feature-visibility behavior.

## Risks

| Risk | Product response |
| --- | --- |
| Positioning becomes too broad | Keep the six angles tied to one promise: governed mobile operations. |
| Admin grows into generic CRM/admin software | Keep admin focused on controlling mobile behavior, tenants, support, billing, reports, and sync. |
| Admin Control Center scope becomes vague | Use the control-center checklist before planning tenant, user, role, permission, feature, config, version, maintenance, force-update, sync, notification, report, billing, or support controls. |
| Feature flags become hidden product logic | Use Feature Flag Logic to define scope, priority, mobile state, admin impact, rollout, plan limits, support, audit, and retirement. |
| Remote config becomes hidden product logic | Use Remote Configuration Logic to define safe config types, scope, defaults, overrides, caching, offline behavior, validation, fallback, support, audit, and rollback. |
| Old mobile versions damage trust | Use Mobile Version Control Logic to define minimum supported versions, update paths, maintenance behavior, store links, support messages, and stale-client blocks. |
| Mobile grows into an independent app | Keep mobile API-driven and policy-controlled. |
| Offline features undermine authority | Treat local writes as intents and reconcile through the API. |
| Tenant flexibility becomes custom code | Prefer tenant config, feature flags, entitlements, and versioned API contracts. |

## Positioning Success Test

The product is positioned correctly when a buyer understands that Mobile Lara is neither just an admin dashboard nor just a mobile app. It is a tenant-based SaaS control center with an API-first, feature-controlled, offline-capable mobile client.

The positioning should also make the value map obvious: platform leaders get control, tenant businesses get governed mobile operations, tenant admins get practical management, mobile workers get simple allowed workflows, support gets diagnosable context, and billing/operations gets entitlement control.
