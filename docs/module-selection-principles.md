# Module Selection Principles

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

Updated: 2026-06-26

This document defines module selection principles for optional industry modules
in Mobile Lara. It explains how field service, logistics, ecommerce, booking,
education, events, support, community/messaging, reports, AI assistant, and
future modules should be selected, enabled by tenant, controlled by plan,
hidden or explained on mobile when unavailable, and documented before
implementation. It is documentation only and does not define database
structure, database fields, migrations, seeders, routes, controllers, Livewire
components, Filament resources, NativePHP plugins, plugin manifests, policies,
gates, middleware, jobs, services, local storage schemas, API endpoints, UI
components, CSS, JavaScript, billing-provider implementation, AI-provider
implementation, queue workers, reports, dashboards, or application logic.

Use this document with [Product Principles](product-principles.md),
[Documentation-First Architecture](documentation-first-architecture.md),
[Product Positioning](product-positioning.md), [SaaS Value
Map](saas-value-map.md), [Two-System Boundary Logic](two-system-boundary.md),
[API-First Principles](api-first-principles.md), [Admin/API
Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration
Logic](remote-configuration-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), [Role And Permission
Logic](role-permission-logic.md), [Data Privacy
Principles](data-privacy-principles.md), [Audit Logic](audit-logic.md),
[Tenant Lifecycle Logic](tenant-lifecycle-logic.md), [Tenant Admin
Logic](tenant-admin-logic.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Offline-First
Principles](offline-first-principles.md), [Sync Lifecycle
Logic](sync-lifecycle-logic.md), [Conflict Resolution
Logic](conflict-resolution-logic.md), [Mobile UX
Principles](mobile-ux-principles.md), [Mobile App Shell
Logic](mobile-app-shell-logic.md), [Mobile Dashboard
Logic](mobile-dashboard-logic.md), [Mobile Settings
Logic](mobile-settings-logic.md), [Mobile Permission
Logic](mobile-permission-logic.md), [Native Feature
Strategy](native-feature-strategy.md), [Records/Content Module
Logic](records-content-module-logic.md), [Search Logic](search-logic.md),
[Forms And Drafts Logic](forms-drafts-logic.md), [Notifications
Logic](notifications-logic.md), [Support System
Logic](support-system-logic.md), [Reporting Logic](reporting-logic.md), and
[Device, Network, And Diagnostics
Logic](device-network-diagnostics-logic.md): optional modules are product
capability bundles, while Admin/API remains authoritative for catalog
availability, tenant enablement, plan entitlement, permissions, feature flags,
remote config, app-version eligibility, sync acceptance, audit, support,
privacy, and billing decisions.

## Module Selection Statement

Optional industry modules let Mobile Lara serve different tenant businesses
without becoming a separate product for every industry. Each module should be a
documented capability bundle that can be enabled, configured, measured,
supported, suspended, upgraded, downgraded, or retired per tenant through
Admin/API authority.

Mobile should never decide that a module is available because a screen exists,
a route is reachable, cached data remains on the device, a NativePHP plugin is
installed, or an old app version still knows about it. Mobile may present,
cache, draft, queue, and sync work for an enabled module, but Admin/API decides
whether the module is available, licensed, visible, writable, reportable,
supportable, and accepted as server truth.

Product rule: modules are selected for tenant value first, then constrained by
plan, permissions, feature flags, remote config, app version, native
capabilities, offline policy, support readiness, reporting needs, privacy
requirements, and operational risk.

## Goals

Module selection should:

- Let the platform owner package industry-specific value without forking the
  product or mobile app.
- Let tenant businesses subscribe to only the modules that match their work.
- Let admins enable modules per tenant only when the tenant's plan and status
  allow them.
- Let tenant admins manage delegated module settings only when platform policy
  allows it.
- Let mobile show only modules, shortcuts, actions, forms, notifications,
  reports, and settings that are available for the current tenant and user.
- Let unavailable modules be hidden by default, or shown as read-only or
  upgrade/contact-admin states only when that helps the user.
- Let every module define its API purpose, admin controls, mobile screens,
  permissions, feature flags, plan limits, remote config, offline behavior,
  sync behavior, conflict behavior, audit behavior, reporting value, support
  behavior, privacy boundaries, and risks before implementation.
- Let modules share common primitives such as records, forms, attachments,
  notifications, support, reports, sync, search, and native capabilities
  instead of inventing separate rules for each industry.
- Let modules be expanded gradually through feature flags, cohorts, tenant
  pilots, plan upgrades, and app-version gates.

Module selection should not:

- Turn mobile into the source of module entitlement, permission authority,
  tenant authority, billing authority, or global configuration authority.
- Treat module presence in code, cached data, or local navigation as proof that
  the module is available.
- Allow module-specific shortcuts to bypass API, feature flags, permissions,
  plan limits, tenant state, or app-version policy.
- Add a module because it is technically possible when the tenant value,
  support model, privacy model, and operating model are unclear.
- Ship an AI, payments, messaging, location, media, scanner, or native-device
  module without explicit security, privacy, retention, permission, support,
  and audit principles.
- Create unbounded modules that combine unrelated industries into one control
  surface.
- Document module behavior as implementation detail instead of product logic,
  boundaries, risks, and acceptance principles.

## Ownership And Authority

| Concern | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Module catalog | The official list of modules, platform availability, lifecycle status, default posture, support status, and rollout policy. | Reading the resolved catalog context from API and presenting only safe mobile states. |
| Tenant enablement | Whether a tenant may use a module, who enabled it, when it applies, whether it is suspended, and whether tenant admins may configure it. | Current-tenant module visibility, local navigation, module onboarding hints, and unavailable-state presentation. |
| Plan control | Which plans include a module, which limits apply, what happens in trial, active, expired, suspended, downgraded, or manually overridden states. | Showing plan-blocked states, upgrade/contact-admin guidance where allowed, and preserving safe local drafts until API policy decides their fate. |
| Permissions | Role/user abilities, admin abilities, mobile abilities, support abilities, and suspended-user behavior for each module. | Hiding, disabling, or explaining module screens/actions according to API-resolved permissions. |
| Feature flags | Safe rollout, cohort rules, tenant/user overrides, emergency disable, experiment state, and app-version or platform gates. | Applying resolved feature availability without requesting disabled native permissions or showing dead shortcuts. |
| Remote config | Module-specific labels, limits, timing, behavior choices, offline limits, upload rules, and safe defaults. | Receiving, caching, and applying valid config while falling back safely when config is stale, missing, or invalid. |
| API behavior | Module contracts, response shape, errors, sync acceptance, conflict decisions, audit meaning, and tenant boundary protection. | Calling API only for authoritative module reads/writes and explaining mobile-friendly errors. |
| Offline behavior | Which module actions may be drafted, cached, queued, synced, rejected, retried, or blocked. | Local cache, drafts, queues, pending indicators, local feedback, and offline recovery UX. |
| Native capability use | Whether a module may use camera, scanner, geolocation, microphone, files, notifications, biometrics, secure storage, or diagnostics. | Permission education, native prompts only when enabled, fallback states, local capture, and sync status. |
| Reporting and support | Module reporting visibility, support access, diagnostics categories, escalation, retention, and audit trails. | User-visible support context, safe diagnostics sharing, local troubleshooting hints, and module-specific support drafts. |

## Enablement Resolution

Module availability should resolve through a clear hierarchy. Each layer can
narrow access, and the final mobile state should be returned through the API as
an understandable outcome.

1. **Platform catalog** decides whether the module exists and is allowed for
   the product.
2. **Platform status** decides whether the module is planned, beta, active,
   deprecated, suspended, or retired.
3. **Plan entitlement** decides whether the tenant's subscription can include
   the module and which plan limits apply.
4. **Tenant lifecycle state** decides whether trial, active, suspended,
   billing-blocked, archived, or deletion-requested tenants can use it.
5. **Tenant enablement** decides whether the module is turned on for a specific
   tenant and whether tenant admins can configure delegated behavior.
6. **Role and permission rules** decide what admins, support agents, tenant
   admins, managers, mobile users, invited users, suspended users, and guests
   can see or control.
7. **Feature flags** decide rollout, user cohorts, emergency disable,
   app-version gates, device gates, platform gates, and safe release waves.
8. **Remote config** decides module-specific behavior, labels, thresholds,
   limits, copy, local cache policy, retry policy, and fallback behavior.
9. **App version and device capability** decide whether the mobile client can
   safely present or execute the module.
10. **Offline and sync policy** decides whether cached reads, local drafts,
    queued actions, manual sync, background sync, and conflict recovery are
    allowed.
11. **Current user context** decides the final mobile state for shortcuts,
    screens, actions, settings, notifications, reports, drafts, and support.

No lower layer should expand access beyond a higher layer. For example, a user
permission should not enable a module blocked by plan, a feature flag should
not bypass a suspended tenant, and mobile cache should not bypass an API
revocation.

## Tenant Enablement Principles

Tenant enablement should be explicit, scoped, auditable, and reversible.

- A module may be enabled for one tenant without affecting other tenants.
- Tenant-specific settings may override global defaults only inside the
  tenant's allowed plan and platform policy.
- Tenant admins may configure a module only when platform admins delegate that
  control.
- Module enablement should record impact: expected mobile screens, API
  dependencies, native permissions, reports, billing effects, support effects,
  sync effects, offline limits, and user groups affected.
- Module disablement should show impact before saving: hidden mobile shortcuts,
  disabled actions, pending drafts, queued changes, notifications, reports,
  support workflows, and possible data retention consequences.
- Tenant-specific changes should never leak settings, records, users, reports,
  notifications, diagnostics, or support context to another tenant.
- Tenant switching on mobile should re-resolve module availability for the new
  tenant before showing shortcuts or replaying queued work.

## Plan Control Principles

Plans define commercial entitlement. Feature flags and permissions define
operational access. These concerns should remain separate.

- A plan may include, exclude, meter, limit, trial, or downgrade a module.
- Plan limits should set ceilings; tenant enablement and permissions still
  decide who can use the included capability.
- A manual admin override should be auditable, scoped, time-aware where useful,
  and visible to support/billing users with appropriate permission.
- Expired or billing-blocked tenants should lose write access or module access
  according to billing policy, while mobile explains the state without exposing
  billing details to users who should not see them.
- Downgrades should define what happens to historical module data, cached data,
  local drafts, queued work, reports, notifications, and support tickets.
- Mobile should never infer plan access locally from product copy, cached
  feature names, or previous tenant state.

## Mobile Unavailable-State Principles

Unavailable modules should not create confusion, permission prompts, or failed
actions.

- Hide unavailable modules by default when the user has no useful action.
- Show a disabled state only when it helps the user understand access,
  onboarding, upgrade, tenant-admin contact, maintenance, forced update,
  permission denial, offline limits, or app-version requirements.
- Disabled states should use API-provided reason categories such as not in
  plan, not enabled for tenant, not allowed by role, blocked by feature flag,
  blocked by app version, blocked by maintenance, blocked while offline, or
  blocked by tenant lifecycle state.
- Disabled modules should not request native permissions.
- Disabled modules should not show deep links, quick actions, notifications, or
  settings that lead to dead ends.
- Existing local drafts should be preserved locally when safe, clearly marked
  as not yet accepted by API, and submitted only when API policy allows it.
- Cached records from a disabled module should be hidden, read-only, or cleared
  according to privacy, retention, tenant, support, and offline policy.
- Mobile should refresh resolved module context after login, tenant switch,
  app resume, forced update checks, maintenance checks, plan changes, and sync
  recovery.

## Documentation Before Implementation

Every optional industry module must have a module brief before implementation.
The brief should be reviewed with the existing product docs before code starts.

The brief should answer:

- Which tenant problem does the module solve?
- Which roles use it in admin and mobile?
- Which plan or trial states include it?
- Which tenant settings enable, disable, configure, suspend, or delegate it?
- Which mobile screens, dashboard shortcuts, settings, notifications,
  permission prompts, and support flows does it affect?
- Which API dependency exists for every mobile screen and action?
- Which permissions are platform-level, tenant-level, admin-user, and
  mobile-user decisions?
- Which feature flags control rollout, emergency disable, cohorts, app
  versions, device classes, and plan limits?
- Which remote config values are safe to change without a mobile release?
- Which data can be cached, drafted, queued, synced, retried, or searched
  offline?
- Which data must never be cached or must be protected by secure storage,
  app lock, redaction, retention, or explicit user consent?
- Which native capabilities are required and how permission education,
  denial, fallback, and diagnostics behave?
- Which conflicts can be auto-resolved, need user choice, or require
  admin/support review?
- Which reports, support views, audit entries, exports, billing effects, and
  privacy risks exist?
- What happens when the module is disabled, downgraded, suspended, retired, or
  hidden from mobile?
- What acceptance criteria prove the module respects Admin/API authority,
  tenant isolation, plan control, feature flags, offline rules, and mobile UX?

## Module Catalog Principles

The modules below are examples of industry capability bundles. They should use
shared platform rules and only become implementation work after module-specific
documentation is accepted.

### Field Service

Field service is defined in [Field Service Logic](field-service-logic.md). It
supports mobile work performed away from a desk: jobs, visits, check-ins,
notes, media, signatures or confirmations, issue reports, and completion
status. It should be selected when tenants need mobile users to receive
assigned work, capture evidence, work offline at job sites, and sync results
later.

Admin/API should own job assignment authority, tenant scheduling rules, user
permission, location/media policy, reports, support visibility, and conflict
decisions. Mobile should own field-friendly task presentation, offline drafts,
check-in status, media capture, pending sync indicators, and simple completion
feedback. Native camera, geolocation, scanner, files, notifications, and
diagnostics dependencies must be documented before use.

### Logistics

Logistics is defined in [Logistics Delivery
Logic](logistics-delivery-logic.md). It supports route, stop, pickup,
delivery, inventory movement, scan, proof-of-delivery, status update, and
exception workflows. It should be selected when tenants need reliable mobile
execution under changing network conditions.

Admin/API should own route authority, stop order meaning, barcode/QR meaning,
exception rules, proof acceptance, reporting, and conflict handling. Mobile
should own scan-first UX, local route context, offline stop updates, pending
proof uploads, duplicate-scan warnings, and clear sync state. Offline behavior
must separate local route progress from server-accepted delivery truth.

### Commerce

Commerce is defined in [Commerce Logic](commerce-logic.md). It supports
product, order, return, customer-service, inventory, fulfillment, cart,
checkout, hosted payment handoff, invoice/receipt, and subscription upsell
workflows. It should be selected when tenants need mobile access to commerce
operations, not when a tenant only needs static product content.

Admin/API should own catalog authority, order state, pricing visibility,
payment provider boundaries, inventory truth, plan limits, permissions,
returns, reports, and audit. Mobile should present role-appropriate commerce
workflows, offline-safe draft actions where allowed, unavailable-payment
states, and clear server acceptance. Payment or financial decisions must never
be owned by mobile cache.

### Booking

Booking is defined in [Booking Logic](booking-logic.md). It supports
availability, reservations, appointments, attendance, rescheduling,
cancellation, reminders, and capacity rules. It should be selected when
tenants need time-slot coordination with mobile visibility or mobile-side
updates.

Admin/API should own availability truth, double-booking prevention, capacity,
cancellation policy, reminders, conflict decisions, and reports. Mobile should
show current bookings, offline-safe drafts where policy allows, sync-required
state for changes, and friendly conflict feedback when availability changed
while offline.

### Education

Education supports learners, courses, lessons, attendance, assignments,
progress, certificates, instructor workflows, and student or parent-visible
mobile summaries. It should be selected when tenants need structured learning
or training workflows.

Admin/API should own enrollment, role visibility, progress truth, assessment
rules, privacy boundaries, reports, notifications, and support access. Mobile
should offer simple learner or instructor tasks, offline reading or draft
submission where allowed, permission-aware progress visibility, and
privacy-safe notifications. Sensitive learner data requires explicit privacy
and retention principles before implementation.

### Events

Events supports schedules, attendees, tickets, check-in, announcements,
capacity, venue context, session feedback, and event support. It should be
selected when tenants need time-bound mobile experiences with admin control.

Admin/API should own attendee eligibility, ticket validity, capacity,
announcement targeting, reports, and audit. Mobile should own schedule
presentation, scan-to-check-in where enabled, offline check-in queues only when
policy allows, duplicate and invalid scan feedback, and clear sync status.
Event modules should document what happens after the event ends.

### Support

Support as an industry module extends the base support system into a tenant
workflow product: cases, service requests, diagnostics, attachments,
assignment, messaging, escalation, and resolution.

Admin/API should own support visibility, case routing, role access, support
agent limitations, redaction, audit, reports, and tenant boundaries. Mobile
should own request creation, message drafting, attachment capture, diagnostics
preview, offline support drafts, and clear submission state. Support must not
become a backdoor into private tenant data.

### Community/Messaging

Community/messaging is defined in [Messaging And Community
Logic](messaging-community-logic.md). It supports tenant-scoped
conversations, announcements, channels, groups, moderation, direct messages,
support chat surfaces, message attachments, reports/abuse flow, read state,
offline message drafts, admin visibility boundaries, privacy principles, and
mobile notifications. It should be selected only when tenants have a clear
need for controlled communication.

Admin/API should own membership, moderation, retention, notification policy,
blocking/reporting, exports, audit, and privacy boundaries. Mobile should own
simple conversation UX, offline draft messages, read/unread presentation, safe
notification deep links, and unavailable states. Messaging must include abuse,
moderation, privacy, support, retention, and tenant-boundary principles before
implementation.

### Reports

Reports as a module packages tenant-facing and platform-facing measurement
around module adoption, work completion, sync health, notifications, support,
billing, usage, and exceptions. It should be selected when tenants need
self-service visibility beyond operational screens.

Admin/API should own report definitions, aggregation meaning, permissions,
export rules, privacy boundaries, billing visibility, audit, and retention.
Mobile should show only user-appropriate summaries, status widgets, and
role-safe drill-downs. Reports must never expose another tenant, hidden plan
data, private diagnostics, or unsupported raw operational data.

### AI Assistant

AI assistant is defined in [AI Feature Logic](ai-feature-logic.md). It is an
optional helper module, not a decision authority. It may help users find
information, summarize permitted content, categorize content, draft messages,
suggest next actions, assist moderation review, explain offline or sync state,
or assist admins with support and report-generation context after explicit
module documentation is approved.

Admin/API should own AI availability, plan entitlement, tenant opt-in,
allowed data sources, prompt boundaries, provider-neutral policy, tool limits,
audit, retention, rate/cost limits, privacy policy, support review, and human
override. Mobile should own a simple assistant UX, context explanation,
permission-aware prompts, offline unavailable behavior, and clear uncertainty
feedback. AI must not bypass permissions, invent tenant authority, expose
secrets, make billing decisions, execute destructive actions without
confirmation, or treat model output as trusted truth.

## Shared Module Behaviors

Every module should resolve shared behaviors consistently:

- **Dashboard**: show only enabled shortcuts for the current tenant, user,
  plan, feature flags, app version, offline state, and subscription status.
- **Navigation**: remove unavailable routes from primary mobile navigation and
  protect direct links with API-resolved access.
- **Settings**: show module settings only when the user can understand or
  control them; separate local preferences from Admin/API authority.
- **Search**: scope local and API search by tenant, permission, module
  availability, privacy, and offline cache limits.
- **Forms**: autosave drafts only where allowed; validate through API before
  server acceptance; explain local-save versus synced state.
- **Notifications**: target module notifications through Admin/API; hide or
  neutralize deep links when the module becomes unavailable.
- **Reports**: show role-appropriate summaries, not raw tenant data or
  cross-tenant aggregates.
- **Support**: provide module context without leaking private content beyond
  support scope.
- **Audit**: preserve the story of enablement, disablement, access decisions,
  admin actions, support actions, sync decisions, and conflict decisions.
- **Offline**: document what works offline, what is read-only, what queues,
  what must wait, what conflicts, and what users see.

## Admin Control Principles

Admins should understand the impact of module decisions before saving changes.

Admin module controls should explain:

- Affected tenants, roles, users, mobile shortcuts, admin surfaces, API
  behavior, native permissions, sync behavior, reports, support workflows, and
  billing effects.
- Whether the change is global, plan-level, tenant-level, role-level,
  user-level, feature-flag-based, cohort-based, app-version-based, or
  device/platform-based.
- Whether the module change is immediate, scheduled, gradual, reversible,
  emergency-only, or dependent on app update.
- What mobile users see while online, offline, out of date, in maintenance,
  suspended, billing-blocked, invited, or revoked.
- What happens to cached data, drafts, queued actions, attachments,
  diagnostics, reports, notifications, and support tickets.
- Which audit entries, support notes, and rollback options are available.

Dangerous module actions should require confirmation and impact preview. This
includes enabling modules with sensitive data, disabling active modules,
changing plan entitlement, changing native capability use, changing offline
write policy, changing retention, enabling AI assistant, enabling messaging,
removing tenant access, or retiring a module.

## API-First Module Principles

Every mobile module should have a clear API purpose. The API should provide the
resolved operating context that mobile needs, without forcing mobile to
rebuild business decisions locally.

API principles for modules:

- Return predictable module availability outcomes for the current tenant and
  user.
- Return permissions, feature flags, plan state, tenant state, app-version
  state, remote config, offline policy, and support context needed by mobile.
- Use mobile-friendly errors that explain the reason category and safe next
  action.
- Treat sync and conflict behavior as first-class module behavior.
- Protect tenant boundaries server-side regardless of mobile navigation state.
- Remain additive and version-aware so old mobile clients fail safely.
- Avoid endpoint-level design in module briefs unless the user explicitly asks
  for API contract detail.

## Offline And Sync Principles

Modules may be offline-capable only where offline work improves the user's job
without weakening business authority.

- Cached module data is local working context, not server truth.
- Drafts and queued actions should show pending status until API accepts or
  rejects them.
- Online-only actions should be disabled while offline with a clear reason.
- Conflict-prone module work should define auto-resolution, user-choice
  recovery, admin/support review, and audit behavior before implementation.
- Native captures such as media, scans, location, voice notes, and diagnostics
  should define local storage, upload queue, retry, deletion, retention, and
  privacy behavior.
- Admins should be able to limit offline module duration, queue size, media
  upload behavior, local cache retention, and manual sync availability through
  documented policy.

## Privacy, Security, And Support Principles

Industry modules can introduce sensitive data quickly. Privacy and support
rules should be part of module selection, not added after implementation.

- Tenant isolation applies to every module by default.
- Least privilege applies to admin, support, tenant admin, manager, mobile
  user, invited user, suspended user, and guest states.
- Module data should use secure native storage only for secrets and highly
  sensitive local values; ordinary local cache must still be minimized and
  protected by app lock where useful.
- Support views should be case-scoped, permission-scoped, tenant-scoped,
  redacted where needed, and audited.
- Diagnostics should explain module failures without exposing secrets, raw
  tokens, private content, raw location, raw media, raw voice notes, raw scan
  payloads, or unrestricted logs.
- AI assistant, messaging, ecommerce, education, geolocation, media, voice,
  scanner, and support modules require extra privacy and abuse-case review
  before implementation.

## Rollout, Rollback, And Retirement

Modules should be introduced and removed safely.

- Start with documentation, then controlled pilots, then tenant-level rollout,
  then broader plan packaging.
- Prefer feature flags and remote config for gradual exposure, not hardcoded
  mobile assumptions.
- Rollback should define mobile visibility, queued action handling, cached data
  treatment, notification deep links, reports, support visibility, audit, and
  billing impact.
- Deprecated modules should show admin warnings before mobile users are
  affected.
- Retired modules should define historical data visibility, exports, support
  access, reports, retention, and tenant communication before removal.

## Readiness Checklist

Before any optional industry module moves from idea to implementation, confirm:

- The tenant problem and business value are documented.
- The module owner, status, non-goals, and risks are documented.
- Plan entitlement, tenant enablement, tenant lifecycle, and manual override
  behavior are documented.
- Admin controls and mobile effects are documented.
- Mobile screens, dashboard shortcuts, settings, notifications, support, and
  unavailable states are documented.
- API purpose, response expectations, mobile-friendly error principles, sync
  behavior, and conflict behavior are documented.
- Permissions, feature flags, remote config, app-version gates, native
  capability dependencies, and offline limits are documented.
- Privacy, security, audit, support, diagnostics, reporting, billing, rollout,
  rollback, downgrade, and retirement principles are documented.
- The module can be hidden or disabled on mobile without dead shortcuts,
  unauthorized native prompts, or local authority bypass.
- The module can be explained to admins in terms of tenant value, operational
  impact, risk, and support readiness.
