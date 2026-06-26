# Documentation-First Architecture

Updated: 2026-06-26

This document defines documentation-first architecture principles for Mobile Lara. It explains how product ideas, admin controls, mobile screens, API dependencies, sync behavior, permissions, and risks must be documented before implementation. It is documentation only and does not define endpoints, routes, database fields, migrations, controllers, Livewire components, resources, policies, jobs, services, NativePHP plugins, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [API-First Principles](api-first-principles.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX Principles](mobile-ux-principles.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), and [Mobile Version Control Logic](mobile-version-control-logic.md): documentation is the agreement that prevents stakeholder value, authority, API behavior, admin controls, feature flags, remote config, mobile-version policy, mobile UX, offline behavior, permissions, and risk handling from drifting during implementation.

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

## Architecture Statement

Mobile Lara is documentation-first because the product has two systems, tenant boundaries, offline behavior, feature control, mobile-local state, NativePHP capabilities, billing effects, support expectations, reports, audit needs, and security concerns.

Every feature must be documented before implementation so that future code has a clear product reason, authority boundary, API purpose, mobile effect, offline rule, permission owner, and risk record.

Documentation-first does not mean writing long documents for their own sake. It means recording the decisions that make implementation safe.

## Core Documentation-First Principles

1. **Document before coding** - Every feature, control, screen, sync behavior, permission, and risk needs a written product decision before implementation.
2. **Document authority** - Every feature must state what Admin/API owns, what mobile owns, what must go through API, and what must never be trusted locally.
3. **Document mobile effect** - Every admin control must explain how mobile behavior, visibility, copy, offline behavior, sync, support, and errors change.
4. **Document API dependency** - Every mobile screen must explain the API context, response state, error state, permission state, feature state, and sync state it depends on.
5. **Document online and offline behavior** - Every sync behavior must explain local cache/draft/queue behavior, online confirmation, replay, retry, conflict, and failure.
6. **Document permission ownership** - Every permission must explain who controls it, who can use it, what it exposes, and how mobile receives the result.
7. **Document risk before implementation** - Every meaningful risk must be recorded with its owner, affected surface, mitigation, and unresolved decision before coding starts.
8. **Document enough to test later** - Documentation should produce clear acceptance criteria for future implementation and tests.
9. **Document changes as the product changes** - If implementation discovers a different product truth, update docs before treating the change as accepted.
10. **Document boundaries, not just features** - The most important docs describe what the system must not do.

## Documentation-First Contract

Every feature planning decision should prove that documentation exists before implementation starts.

| Documentation area | Required principle | Prevents |
| --- | --- | --- |
| Feature decision | Every feature must document stakeholder value, roles, ownership, API purpose, tenant boundary, feature/config/version relationship, mobile states, offline/sync behavior, support/reporting/audit expectations, risks, and non-goals before implementation. | Screen-first features, hidden authority, accidental billing effects, unclear support paths, and code that invents product rules. |
| Admin control effect | Every admin control must document what it changes, who controls it, which scope applies, what mobile receives, how mobile changes, how rollback/support/audit work, and what can go wrong. | Admin toggles that surprise mobile users, support teams, billing users, or tenant admins. |
| Mobile screen dependency | Every mobile screen must document its API dependency, required context, permission/capability state, feature/config/version state, user actions needing server confirmation, error states, and offline states. | Mobile screens that look complete while missing API authority, permission state, or failure behavior. |
| Sync behavior | Every sync behavior must document offline cache/draft/queue limits and online replay, idempotency, re-checks, retry, accepted, rejected, stale, out-of-policy, conflict, failed, support, and reporting outcomes. | Offline queues that become trusted truth, vague retry behavior, and unsupported conflict handling. |
| Permission ownership | Every permission must document who controls it, where it applies, what mobile capability state it creates, which API actions it affects, denied/account states, audit needs, and support explanation. | Permissions treated as UI labels instead of server-owned authority. |
| Risk record | Every meaningful risk must be recorded with owner, affected surface, mitigation status, unresolved decision, and coding gate before implementation. | Security, tenant, billing, sync, NativePHP, support, reporting, audit, rollout, and user-confusion risks found too late. |

This contract is intentionally principle-level. It does not create features, controls, screens, endpoints, routes, fields, database tables, migrations, controllers, Livewire components, resources, policies, jobs, services, NativePHP plugins, provider integrations, tests, or application logic.

## Every Feature Must Be Documented Before Implementation

Every feature should start as a documented product slice.

A feature document or updated product doc should explain:

- The stakeholder value.
- The user roles and account states involved.
- The Admin/API responsibility owner.
- The mobile-client responsibility owner.
- The API purpose.
- The tenant boundary.
- The feature flag or remote-config relationship.
- The mobile UX states.
- The offline and sync behavior.
- The support and reporting expectations.
- The billing or entitlement effect, if any.
- The audit expectation, if any.
- The risks and non-goals.

No feature should move to code because it is "obvious from the screen." Screens are consequences of product decisions, not substitutes for them.

## Every Admin Control Must Document Its Mobile Effect

Admin controls are not isolated settings. They change what mobile users see, can do, can queue, can sync, or can understand.

Every admin control should document:

| Question | Documentation expectation |
| --- | --- |
| What does the control change? | Feature, permission, config, version, notification, billing, support, sync, report, or security behavior is named. |
| Who can change it? | Platform owner, super admin, tenant admin, tenant manager, support, billing, or another role is named. |
| Which scope applies? | Global, tenant, plan, role, user, device, app version, cohort, support case, or billing scope is explicit. |
| What does mobile receive? | API state, remote config value, feature state, error state, version state, sync state, or entitlement outcome is explicit. |
| How does mobile change? | Screen visibility, disabled state, blocked state, copy, offline eligibility, retry behavior, sync behavior, or support prompt is named. |
| What can go wrong? | Risk, rollback, support explanation, audit expectation, and user-facing failure mode are recorded. |

Admin controls are product levers. Their mobile effects must be written before those levers exist.

Use [Admin Control Center Logic](admin-control-center-logic.md) as the checklist for tenant, user, role, permission, feature, config, version, maintenance, force-update, sync, notification, report, billing, and support controls.

Use [Feature Flag Logic](feature-flag-logic.md) as the checklist for important mobile feature priority, disabled states, rollout, admin impact, plan limits, support, audit, offline behavior, and retirement.

Use [Remote Configuration Logic](remote-configuration-logic.md) as the checklist for remote-configurable behavior, scope, default, tenant override, mobile receive/cache rules, offline behavior, missing/invalid fallback, admin safety, support, audit, rollback, and retirement.

Use [Mobile Version Control Logic](mobile-version-control-logic.md) as the checklist for minimum supported versions, optional updates, forced updates, maintenance mode, outdated responses, store links, update messages, support, audit, rollback, and old-version protection.

## Every Mobile Screen Must Document Its API Dependency

Every mobile screen should document what it needs from the API before it is built.

That dependency should include:

- Boot or account context required.
- Tenant context required.
- Permissions or capability state required.
- Feature flags or remote config required.
- App-version behavior required.
- Data payload purpose, without endpoint design detail.
- User actions that require server confirmation.
- Error states that must be shown.
- Offline state, cached state, draft state, pending state, synced state, conflict state, and failed state where relevant.
- Support path when the screen cannot recover locally.

A mobile screen without an API dependency story is only a mockup. It is not ready for implementation in this SaaS product.

## Every Sync Behavior Must Document Offline And Online Behavior

Sync behavior must be documented from both sides of the connection.

Offline behavior should explain:

- Whether the feature is read-only, draft-only, queueable, or online-only.
- What local cache is safe to display.
- What local drafts can be created.
- What queued intents can be stored.
- What user state is shown while offline.
- What actions are blocked while offline.
- What local data must not be stored.

Online behavior should explain:

- When boot context or config refresh happens.
- How queued intents replay.
- Which idempotency expectation applies.
- Which server checks happen again.
- What accepted, transformed, rejected, duplicated, stale, unauthorized, out-of-policy, conflicted, retry-later, and failed outcomes mean.
- How support and reports can see safe sync context.

Sync is not complete until both offline and online behavior are documented.

## Every Permission Must Document Who Controls It

Permissions are product authority, not UI labels.

Every permission should document:

- The role or admin surface that controls it.
- The tenant, team, user, device, app-version, plan, or support scope where it applies.
- The mobile capability state it creates.
- The API reads, writes, sync replay, support actions, reports, or admin actions it affects.
- The denied, suspended, invited, guest/pre-login, blocked, or expired states.
- The audit expectation for grants, revocations, or high-risk permission use.
- The support explanation for why a user can or cannot act.

Mobile may display permission-derived capability state, but Admin/API owns the permission decision.

## Every Risk Must Be Recorded Before Coding

Risk recording is part of architecture, not an afterthought.

Every feature slice should record:

- Product risk.
- Tenant-boundary risk.
- Security risk.
- Permission or role risk.
- Billing or entitlement risk.
- Offline/sync risk.
- Native permission/device risk.
- API contract risk.
- Support/reporting/audit risk.
- User-confusion risk.
- Rollout or rollback risk.

Each risk should have one of these statuses:

| Status | Meaning |
| --- | --- |
| Avoided | The feature design removes the risk. |
| Mitigated | The design includes a control, boundary, or fallback. |
| Accepted | The risk is known and intentionally tolerated. |
| Deferred | The risk needs a future decision before implementation. |
| Blocked | The feature cannot be implemented until the risk is resolved. |

Unrecorded risk becomes accidental architecture.

## Documentation Artifacts

Future implementation slices should update the smallest useful set of docs.

| Artifact | Purpose |
| --- | --- |
| Product principle update | Records a new or changed product rule. |
| Boundary update | Records what Admin/API owns, what mobile owns, what is API-only, what can be cached, and what must never be local authority. |
| API-first note | Records API purpose, response expectations, mobile-friendly errors, sync/conflict behavior, and tenant scope. |
| Responsibility update | Records Admin/API and mobile-client responsibility owners. |
| Role/value update | Records who controls the feature and who receives value. |
| Sync/offline note | Records online, offline, replay, conflict, retry, failed, and support behavior. |
| Risk register entry | Records risks and mitigation status before coding. |
| ADR | Records a decision that would be expensive to reverse. |
| Acceptance criteria | Records the future behavior that tests should prove. |

Documentation should be concise, but it should be specific enough that another engineer or agent can implement without inventing product authority.

## Documentation-First Checklist

Use this checklist before a feature moves from planning to implementation.

| Question | Required answer |
| --- | --- |
| Is the feature documented? | The product slice, stakeholder value, user roles, and non-goals are written. |
| What does Admin/API own? | Control-plane authority and responsibility owner are named. |
| What Admin Control Center area owns it? | Tenant, user, role, permission, feature, config, version, maintenance, force update, sync, notification, report, billing, or support control is named. |
| What feature flag logic applies? | Global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, emergency, disabled state, rollout, and offline behavior are named. |
| What remote configuration logic applies? | Config type, default, scope, tenant override, mobile cache, offline behavior, missing/invalid fallback, admin safety, support, audit, and rollback are named. |
| What mobile version control logic applies? | Minimum version, optional update, forced update, maintenance mode, store link, update message, stale-client response, support, audit, and rollback are named. |
| What does mobile own? | Local UX, cache, draft, queue, NativePHP, sync display, feedback, or visibility owner is named. |
| What is the API purpose? | API context, response states, errors, sync/conflict, and tenant boundary are documented. |
| What is the admin control's mobile effect? | Mobile visibility, copy, disabled/blocked state, offline behavior, sync behavior, or support prompt is explicit. |
| What does the mobile screen depend on? | API context, permission state, feature state, config, version state, errors, and sync state are explicit. |
| What happens offline and online? | Cache, draft, queue, replay, idempotency, retry, conflict, failed, and support behavior are written. |
| Who controls each permission? | Role, scope, grant/revoke authority, denied state, support explanation, and audit expectation are written. |
| What risks exist? | Risks are recorded as avoided, mitigated, accepted, deferred, or blocked. |
| What remains out of scope? | Schema, migrations, endpoints, controllers, policies, jobs, services, plugins, and code are deferred until an implementation prompt. |

## Risks

| Risk | Documentation-first response |
| --- | --- |
| Docs become busywork | Keep docs decision-focused: authority, API purpose, mobile effect, sync behavior, permissions, risks, and acceptance criteria. |
| Features start from UI screenshots | Require API dependency, admin control, mobile effect, and sync/offline behavior before screen implementation. |
| Admin settings surprise mobile users | Document every admin control's mobile effect and support explanation. |
| API behavior is invented during coding | Document API purpose, context, predictable states, errors, sync/conflict, and tenant boundary first. |
| Offline behavior becomes unclear | Document offline and online behavior together. |
| Permissions become hidden assumptions | Document who controls every permission and how mobile receives the result. |
| Risks are discovered too late | Record risks before coding and mark unresolved risks as deferred or blocked. |
| Documentation drifts from implementation | Update docs when implementation changes product truth, before accepting the change. |
| Documentation overreaches into code | Keep this layer to principles, behavior, boundaries, risks, and acceptance criteria. |
| Agents implement from stale context | Keep docs linked from every project Markdown file and treat them as planning preflight. |

## Success Test

Documentation-first architecture is successful when every feature can be implemented from written product decisions: what value it creates, who controls it, what Admin/API owns, what mobile owns, why the API exists, what the admin control does to mobile, what each mobile screen depends on, how sync works online and offline, who controls permissions, what risks exist, and what code remains intentionally out of scope until implementation.
