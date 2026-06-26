# Core Product Principles

Updated: 2026-06-25

This document defines the core product principles for Mobile Lara. These principles guide product decisions, documentation, feature design, and future implementation planning. It is documentation only and does not define database fields, migrations, controllers, components, policies, or application logic.

## Principle Summary

Mobile Lara is a tenant-based SaaS control center with an API-first, feature-controlled, offline-capable mobile client. The Admin/API system is the source of business authority. The mobile client is a simple, resilient executor of that authority.

Every future feature should satisfy these principles before implementation begins.

Use this document with [Product Vision](product-vision.md): the principles
explain how the vision remains safe as features expand.

Use this document with [Product Positioning](product-positioning.md): the
principles protect the six positioning angles from drifting into web-only,
mobile-only, or tenant-unsafe behavior.

Use this document with [Acceptance Principles](acceptance-principles.md): every
future feature must turn these principles into documented purpose, admin
control, mobile behavior, API dependency, offline behavior, permission
behavior, feature flag behavior, tenant behavior, error behavior, security
behavior, and documentation requirements before implementation planning begins.

Use this document with [Risk Map](risk-map.md): product principles stay
operationally safe when API dependency, offline sync, tenant isolation, secure
storage, NativePHP availability, release, update, feature flag, billing, admin,
support, privacy, and conflict risks are documented before implementation.

Use this document with [Testing Strategy
Principles](testing-strategy-principles.md): future tests should prove the
documented product authority, tenant boundary, API dependency, offline behavior,
feature control, billing rule, native fallback, and app-version rule rather than
testing accidental implementation details.

Use this document with [Release And Versioning
Principles](release-versioning-principles.md): release decisions should preserve
API compatibility, mobile app version safety, admin rollout control, rollback
options, app-store constraints, forced update discipline, documentation updates,
and traceable Git history before behavior reaches users.

Use this document with [Admin Safety Principles](admin-safety-principles.md):
admin authority stays trustworthy only when dangerous controls are confirmed,
audited, impact-previewed, mobile-previewed, rollback-aware, and tenant-isolated.

Use this document with [Mobile UX Principles](mobile-ux-principles.md): simple
mobile UX means mobile-first navigation, simple screens, clear loading/offline
states, thumb-friendly controls, minimum typing, fast actions, secure sessions,
feature visibility based on admin rules, and native permission education.

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

## Principles Contract

| Principle | Product rule | Feature behavior |
| --- | --- | --- |
| Admin controls everything | Admin/API is the source of business authority for tenants, users, permissions, features, config, versions, billing, support, notifications, reports, sync, audit, and security. | Every feature names its admin owner, allowed scope, mobile effect, API outcome, audit expectation, support meaning, and rollback path. |
| Mobile client never bypasses API | Mobile is a managed executor, not a policy engine or data authority. | Server-trusted reads, writes, sync replay, support actions, notification registration, billing checks, feature state, config, and version policy move through API only. |
| Every feature can be enabled or disabled | A feature is not product-ready until it can be safely enabled, disabled, limited, rolled out, blocked, or retired. | Important features define global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, emergency, offline, and disabled states. |
| Tenant isolation | Tenant scope is the commercial, security, reporting, support, billing, notification, and sync boundary. | Every admin action, API response, mobile context, report, support case, notification, audit entry, and sync decision is tenant-scoped and server-enforced. |
| Offline-first where useful | Offline behavior is valuable only when it improves mobile work without claiming authority. | Mobile may cache, draft, queue, label freshness, retry, and show conflicts, but queued work remains local intent until API accepts it. |
| Secure by default | Security is part of every product slice, not a later cleanup phase. | Authorization, tenant scope, least privilege, token handling, secure local storage, audit, rate limits, safe errors, and secret boundaries are defined before implementation. |
| API-first communication | The API is the trusted contract between the SaaS control center and mobile execution. | Mobile receives shaped permissions, feature flags, remote config, version policy, user/tenant context, sync rules, support state, entitlement outcomes, and clear errors through versioned API responses. |
| Simple mobile UX | Mobile users should see clear next actions, not admin machinery. | Mobile screens show only permitted workflows and honest states for loading, disabled, blocked, offline, pending, synced, conflict, failed, deprecated, and update-required behavior. |
| Documentation-first development | Product-critical behavior is written before implementation. | Features document stakeholder value, admin mobile effect, API dependency, offline behavior, permission owner, risks, support meaning, audit needs, and acceptance criteria before code. |
| Modular feature expansion | The product grows through complete feature slices, not scattered screens. | Each module carries admin controls, API contracts, mobile UX, feature flags, remote config, tenant scope, offline rules, support/reporting visibility, audit behavior, rollout, and rollback. |

These principles are not optional preferences. They are the acceptance gate for
future product planning, API contracts, admin controls, mobile screens, support
operations, billing behavior, sync behavior, and NativePHP capability use.

The documentation-first architecture standard in [Documentation-First Architecture](documentation-first-architecture.md) defines how those principles become planning requirements: every feature, admin control, mobile screen, sync behavior, permission, and risk is documented before coding.

The [Admin Control Center Logic](admin-control-center-logic.md) defines how admin authority becomes operational control over tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance mode, force update, sync behavior, notifications, reports, billing, and support.

The [Feature Flag Logic](feature-flag-logic.md) defines how important mobile features are controlled, prioritized, disabled, rolled out, plan-limited, audited, supported, and resolved into mobile-safe states.

The [Remote Configuration Logic](remote-configuration-logic.md) defines how safe runtime behavior is configured, delivered, cached, overridden, validated, audited, and handled when offline, missing, or invalid.

The [Mobile Version Control Logic](mobile-version-control-logic.md) defines how supported versions, optional updates, forced updates, maintenance mode, outdated responses, store links, update messages, and old-version protection are controlled.

The role model in [Target User Roles](user-roles.md) defines who can see and control product surfaces. Role boundaries are part of every principle below.

The [SaaS Value Map](saas-value-map.md) defines why each product surface matters. Stakeholder value is part of every principle below: admin control, mobile access, offline sync, notifications, reports, security, and feature flags must create clear value for the right role without leaking authority to the wrong one.

The [Two-System Boundary Logic](two-system-boundary.md) defines where each responsibility belongs. Boundary ownership is part of every principle below: Admin/API owns authority, mobile owns local execution, API confirms server-trusted behavior, and offline work remains constrained.

The [API-First Principles](api-first-principles.md) define how the two systems communicate. API purpose is part of every principle below: mobile communicates only with API, responses are predictable, operating context is explicit, errors are mobile-friendly, sync/conflict behavior is first-class, and tenant boundaries are protected server-side.

The [Admin/API Responsibilities](admin-api-responsibilities.md) define what the control plane must own. Responsibility ownership is part of every principle below: tenant management, users and permissions, admin operations, API contracts, feature control, remote configuration, mobile version rules, notifications, billing, support, reporting, audit, conflict decisions, and security enforcement stay server-side.

The [Mobile Client Responsibilities](mobile-client-responsibilities.md) define what the managed client must own. Responsibility ownership is part of every principle below: mobile UX, secure local session, cache, offline actions, NativePHP features, navigation, permissions UX, sync status, drafts, feedback, and feature visibility stay local while authority stays server-side.

## 1. Admin Controls Everything

Admin controls every business-sensitive mobile capability.

This includes tenants, users, roles, permissions, devices, feature flags, remote config, app-version policy, notifications, billing entitlements, reports, support visibility, sync policy, and emergency rollback.

"Admin controls everything" does not mean the admin UI stores all logic in screens. It means the Admin/API system is the authority and the admin panel exposes safe controls for that authority. The API still enforces decisions server-side.

Product rule: if a mobile behavior can affect business data, tenant access, billing, support, security, or sync, it must have an admin/API control story.

Use [Admin/API Responsibilities](admin-api-responsibilities.md) to name the exact control-plane responsibility before planning the feature.

Use [Admin Control Center Logic](admin-control-center-logic.md) to name the exact admin control area, scope, mobile effect, API context, audit expectation, support meaning, and offline behavior.

## 2. Mobile Client Never Bypasses API

The mobile client must never bypass the API for server-trusted behavior.

The mobile client may use local storage for cache, drafts, local metadata, and queued intents. It must not directly decide tenant authority, permission authority, billing authority, feature authority, audit authority, or final sync outcomes.

All remote reads, writes, sync replay, support actions, notification registration, version checks, and feature decisions must go through the API contract.

Product rule: mobile can prepare work locally, but the API confirms whether that work becomes server truth.

Use [Mobile Client Responsibilities](mobile-client-responsibilities.md) to name the exact local responsibility before planning mobile behavior.

## 3. Every Feature Can Be Enabled Or Disabled

Every feature must be controllable.

Feature control can happen globally, by tenant, by plan, by role, by user, by device, by app version, or by rollout cohort. Some features need emergency kill switches. Some features need gradual rollout. Some features should be visible but blocked with a clear reason.

A feature is not product-ready if it exists only as a screen. It must define:

- Who can enable it.
- Who can use it.
- Which plans or tenants get it.
- Which app versions support it.
- Whether it works offline.
- What happens when it is disabled.
- How support can explain its state.

Product rule: every feature ships with enable, disable, rollback, support, and audit thinking.

Use [Feature Flag Logic](feature-flag-logic.md) to define global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, emergency, and offline decisions before implementation.

Use [Remote Configuration Logic](remote-configuration-logic.md) to define safe runtime config, defaults, tenant overrides, mobile caching, offline behavior, invalid-config fallback, support visibility, audit, and rollback.

Use [Mobile Version Control Logic](mobile-version-control-logic.md) to define minimum supported versions, optional updates, forced updates, maintenance behavior, store links, update messages, support visibility, audit, and rollback.

## 4. Tenant Isolation Is Non-Negotiable

Tenant isolation is the primary SaaS trust boundary.

Every admin action, API request, mobile boot payload, report, support case, notification, sync decision, and audit event must be scoped to the correct tenant. Mobile-provided tenant IDs are claims to verify, not authority to trust.

Tenant isolation also applies to product operations. Support users, billing operators, tenant admins, and platform operators should only see the tenant context their role allows.

Invited, suspended, and guest/pre-login states override normal role access.

Product rule: no feature is complete until tenant scope is explicit and enforced by the Admin/API system.

## 5. Offline-First Where Useful

Offline-first is a product choice, not a blanket rule.

Some features should work offline. Some should be read-only offline. Some should allow drafts but not queue writes. Some must remain online-only because the server decision is too important or too volatile.

Local offline work should be modeled as cache, draft, pending intent, synced copy, conflict, or failed state. Queued writes must replay through the API with idempotency and clear conflict behavior.

Product rule: use offline-first where it helps mobile users, but never let offline behavior become client-side authority.

## 6. Secure By Default

Security is a default product property, not a later hardening pass.

Secure by default means:

- Server-side authorization for every business action.
- Tenant scope on every relevant action and response.
- Least-privilege admin roles.
- Token-based mobile access through the API.
- Secrets stored in secure storage, not local SQLite, docs, code, or logs.
- Sensitive admin changes audited with actor, scope, old value, new value, and reason.
- Dangerous controls require confirmation and are reversible where possible.

Product rule: disabled UI, hidden buttons, cached flags, and local state are never security boundaries.

## 7. API-First Communication

The API is the contract between the SaaS control center and the mobile client.

API-first means mobile behavior comes from versioned, explicit server responses. The API returns permissions, feature state, remote config, app-version policy, sync policy, support state, and resource data in shaped payloads. It also returns clear error categories such as validation, unauthorized, forbidden, conflict, stale client, maintenance, rate limited, and retry later.

Product rule: every mobile capability needs a server contract before it becomes a durable mobile workflow.

Use [API-First Principles](api-first-principles.md) to define the API purpose, predictable response, context, mobile-friendly error, sync/conflict, and tenant-boundary expectations.

## 8. Simple Mobile UX

The mobile client should be simple because mobile users are there to do work, not administer the SaaS.

Simple mobile UX means:

- Show only permitted workflows.
- Explain disabled, blocked, deprecated, offline, pending, synced, conflict, and failed states clearly.
- Keep forms short and task-focused.
- Request native permissions only when needed.
- Avoid exposing billing, tenant configuration, rollout, or support internals unless they directly help the user.
- Keep local feedback fast while making server confirmation honest.

Product rule: mobile users should understand what they can do next without understanding the admin machinery behind it.

## 9. Documentation-First Development

Product-critical decisions must be written before implementation.

Documentation-first means feature work starts with principles, boundaries, flows, risks, API behavior, admin control behavior, mobile behavior, offline behavior, support behavior, and audit behavior. ADRs record decisions that would be expensive to reverse.

The detailed checklist lives in [Documentation-First Architecture](documentation-first-architecture.md).

Documentation is not a substitute for tests or implementation. It is the agreement that makes future implementation safer.

Product rule: if a feature changes product authority, tenant behavior, API contracts, sync behavior, native permissions, billing, support, or security, document the decision before writing code.

## 10. Modular Feature Expansion

The product should grow by modules that prove the full control loop.

Each module should define:

- Admin controls.
- API contracts.
- Mobile screens or workflows.
- Feature flags and entitlements.
- Offline behavior where useful.
- Tenant scope.
- Support and reporting visibility.
- Audit behavior.
- Rollout and rollback plan.

Modules should be independently understandable and should avoid duplicating logic across admin, API, and mobile surfaces.

Product rule: expand by complete feature modules, not scattered screens.

## Principle Checklist

Before a future feature is implemented, answer:

| Question | Required answer |
| --- | --- |
| Who controls it? | Admin/API owns the business decision. |
| Which admin control owns it? | Tenant, user, role, permission, feature, config, version, maintenance, force update, sync, notification, report, billing, or support control maps to `docs/admin-control-center-logic.md`. |
| What is the feature flag logic? | Important mobile features map to `docs/feature-flag-logic.md` for priority, disabled state, rollout, admin impact, plan limit, support, audit, and offline behavior. |
| What is the remote config logic? | Runtime-configurable behavior maps to `docs/remote-configuration-logic.md` for config type, scope, default, override, cache, offline, validation, fallback, support, audit, and rollback. |
| Which roles see it? | Visibility and control map to `docs/user-roles.md`. |
| Who receives value? | Stakeholder value maps to `docs/saas-value-map.md`. |
| Which system owns it? | Boundary ownership maps to `docs/two-system-boundary.md`. |
| What is the API purpose? | API-first purpose maps to `docs/api-first-principles.md`. |
| Which Admin/API responsibility owns it? | Responsibility ownership maps to `docs/admin-api-responsibilities.md`. |
| Which mobile-client responsibility owns it? | Local execution ownership maps to `docs/mobile-client-responsibilities.md`. |
| Can mobile bypass it? | No; mobile uses the API contract. |
| Can it be disabled? | Yes; behavior is defined for disabled state. |
| Is tenant scope clear? | Yes; scope is server-enforced. |
| Does offline help? | Yes/no, with explicit state and replay behavior. |
| Is it secure by default? | Yes; authorization, secrets, audit, and least privilege are defined. |
| Is the API contract clear? | Yes; request, response, errors, versioning, and idempotency are known. |
| Is mobile UX simple? | Yes; users see clear next actions and status. |
| Is the decision documented? | Yes; documentation-first architecture checks feature docs, admin mobile effect, screen API dependency, sync behavior, permission owner, and risks before code. |
| Is it modular? | Yes; admin/API/mobile/support/audit behavior belongs to one feature slice. |

## Boundaries

This principles document does not create:

- Database fields.
- Migrations.
- API routes or controllers.
- Livewire components.
- Policies.
- Jobs or services.
- Feature flag records.
- Tenant records.
- Billing provider integrations.
- Push provider integrations.
- Native plugin integrations.
- Application logic.

Those belong in future implementation prompts with tests and acceptance criteria.

## Risks

| Risk | Principle response |
| --- | --- |
| Admin controls become UI-only | API remains the enforcement layer. |
| Mobile duplicates business logic | Mobile renders server policy and replays work through the API. |
| Feature flags become unmanaged sprawl | Every feature needs owner, scope, disable behavior, support visibility, and audit trail. |
| Admin controls become unmanaged sprawl | Use Admin Control Center logic to define scope, role authority, mobile effect, API context, audit, support, offline behavior, and risk. |
| Feature flags become unmanaged sprawl | Use Feature Flag Logic to define owner, scope, purpose, priority, rollout, mobile state, support meaning, audit, and retirement. |
| Remote config becomes unmanaged sprawl | Use Remote Configuration Logic to define owner, purpose, scope, default, compatibility, validation, fallback, support, audit, rollback, and retirement. |
| Feature value becomes unclear | Use the SaaS value map to name the stakeholder, outcome, and proof metric before implementation. |
| Ownership boundary becomes blurry | Use the two-system boundary before deciding where state, authority, cache, queue, or offline behavior belongs. |
| API behavior becomes accidental | Use the API-first principles before deciding response shape, operating context, mobile errors, sync/conflict, or tenant-scoped responses. |
| Responsibility ownership becomes blurry | Use the Admin/API responsibility map before deciding who owns tenant, permission, API, feature, config, version, notification, billing, support, report, audit, conflict, or security behavior. |
| Mobile local ownership becomes blurry | Use the mobile-client responsibility map before deciding who owns UX, session, cache, queue, NativePHP, navigation, permissions, sync display, drafts, feedback, or feature visibility. |
| Tenant boundaries blur in support/reporting | Tenant scope applies to operations as strongly as core data. |
| Offline behavior creates false confidence | Local state is labeled cache, draft, pending, synced, conflict, or failed. |
| Security is delayed | Secure-by-default is part of every feature checklist. |
| Documentation drifts | Use Documentation-First Architecture to update docs as part of feature definition and before accepting implementation changes. |
| Modules become tangled | Features expand as complete slices with clear admin/API/mobile/support/audit ownership. |

## Success Test

The principles are working when every new product slice can explain who benefits, who controls it, how the API enforces it, how mobile presents it, how tenants are isolated, how it behaves offline, how it stays secure, how it is documented, and how it can expand without becoming tangled.
