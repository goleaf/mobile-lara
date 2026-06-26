# Reporting Logic

Final Optimized SaaS Blueprint is defined in `final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

Updated: 2026-06-26

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

This document defines reporting logic for the Mobile Lara SaaS system. It
explains what platform admins need to measure, what tenant admins need to
measure, what mobile users may see, privacy boundaries, date range principles,
export principles, feature usage reporting, sync health reporting,
notification reporting, support reporting, and billing reporting. It is
documentation only and does not define report tables, database fields,
migrations, indexes, seeders, routes, controllers, Livewire components,
Filament resources, NativePHP plugins, policies, gates, middleware, jobs,
services, local storage schemas, API endpoints, UI components, CSS,
JavaScript, queues, report builders, export workers, billing provider
integration, analytics provider integration, or application logic.

Use this document with [Product Principles](product-principles.md), [Target
User Roles](user-roles.md), [Role And Permission Logic](role-permission-logic.md),
[Data Privacy Principles](data-privacy-principles.md), [Audit
Logic](audit-logic.md), [Tenant Lifecycle Logic](tenant-lifecycle-logic.md),
[Tenant Admin Logic](tenant-admin-logic.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Two-System Boundary
Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Mobile Dashboard
Logic](mobile-dashboard-logic.md), [Mobile Settings Logic](mobile-settings-logic.md),
[Feature Flag Logic](feature-flag-logic.md), [Remote Configuration
Logic](remote-configuration-logic.md), [Mobile Version Control
Logic](mobile-version-control-logic.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Offline-First
Principles](offline-first-principles.md), [Offline UX
Logic](offline-ux-logic.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md),
[Notifications Logic](notifications-logic.md), [Support System
Logic](support-system-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), [SaaS Value Map](saas-value-map.md), and
[Native Feature Strategy](native-feature-strategy.md), and [API v1 Reports
Contract](../contracts/api/v1-reports.md): reporting is an Admin/API-owned
truth layer, and mobile receives only role-appropriate, tenant-scoped,
privacy-safe, freshness-aware summaries.

## Reporting Statement

Reporting turns product activity into decision support.

A report is not raw data access. A report is a scoped, permission-aware,
auditable interpretation of activity, health, usage, support, billing, sync,
notification, security, and tenant outcomes. Reporting helps platform teams
operate the SaaS, tenant admins manage their organization, support teams
resolve problems, billing teams understand commercial health, and mobile users
understand only their own relevant progress or status.

Product rule: Admin/API owns report definitions, aggregation meaning, report
scope, permissions, filters, date interpretation, exports, freshness,
retention, audit, and privacy enforcement. Mobile owns only safe presentation
of resolved report summaries, freshness labels, offline/stale messaging, and
current-user or current-tenant views returned by the API.

## Goals

Reporting logic should:

- Give platform operators trusted operational, product, security, billing,
  support, notification, sync, feature, and tenant-health insight.
- Give tenant admins useful tenant-scoped reports without exposing other
  tenants, platform internals, or private user data they are not allowed to
  inspect.
- Give mobile users simple personal or work-context summaries only when role,
  permission, tenant status, feature flags, subscription state, and remote
  config allow them.
- Keep report authority in Admin/API, even when mobile caches summaries for
  offline display.
- Make date ranges, time zones, filters, freshness, and export scope explicit.
- Treat exports as sensitive data movement that requires authorization,
  tenant scope, audit, redaction, expiry, and retention rules.
- Connect reporting to feature usage, sync health, notifications, support, and
  billing so product decisions are based on outcomes instead of guesses.
- Protect tenant isolation, least privilege, private mobile diagnostics, and
  support access boundaries.
- Fail closed when report scope, permission, tenant context, or freshness is
  unknown.

Reporting logic should not:

- Let mobile invent report totals, tenant-wide metrics, billing status, support
  status, or export authority from local cache.
- Turn reports into unrestricted search across tenant data.
- Expose raw private payloads, secrets, payment details, support-only notes, or
  diagnostics beyond the user's allowed scope.
- Let tenant admins compare themselves against named tenants unless a future
  documented benchmarking feature defines privacy-safe aggregation.
- Let support agents use reports as a broad data-browsing tool outside their
  assigned support context.
- Treat a chart, dashboard card, export, or cached summary as an authorization
  boundary.
- Hide blocked report access behind vague errors.
- Create database structure, application logic, or report endpoints in this
  document.

## Ownership And Authority

| Concern | Admin/API authority | Mobile responsibility |
| --- | --- | --- |
| Report definitions | Define report purpose, metric meaning, source boundaries, allowed roles, filters, date behavior, freshness, and audit needs. | Present only reports returned for the current user, tenant, feature set, version, and subscription state. |
| Report scope | Resolve platform, tenant, user, support-case, billing, sync, and feature scopes server-side. | Preserve current tenant context and never merge report data across tenants locally. |
| Permissions | Enforce report access through platform, tenant, admin-user, mobile-user, support, billing, and account-state rules. | Hide or explain unavailable report surfaces from resolved API outcomes. |
| Aggregation | Compute totals, rates, trends, counts, cohorts, and rollups with documented definitions. | Display aggregation labels, empty states, and stale states without recomputing trusted totals. |
| Date ranges | Own time zone interpretation, range limits, comparison periods, freshness, and generated-at timestamps. | Show date labels exactly as returned and clearly mark cached or offline summaries. |
| Exports | Authorize, generate, redact, expire, retain, and audit exports. | Request or open exports only through API-approved flows; never generate trusted exports offline. |
| Privacy | Enforce tenant isolation, least privilege, data minimization, redaction, support boundaries, and diagnostics limits. | Avoid exposing cached report data after logout, tenant switch, lock, suspension, or permission loss. |
| Audit | Record meaningful report access, export, denied access, cross-role views, support views, and sensitive filters. | Surface user-safe explanations without exposing audit internals. |

## What Platform Admins Need To Measure

Platform admins and super admins need system-wide operational insight because
they are responsible for the health, growth, safety, and commercial operation
of the SaaS control center.

They should be able to measure:

- Tenant growth: new tenants, active tenants, trial tenants, conversions,
  churn risk, suspended tenants, archived tenants, billing-blocked tenants, and
  tenant deletion requests.
- Tenant health: active users, invited users, suspended users, usage trends,
  feature adoption, support volume, sync health, notification reach, app
  version adoption, and data volume signals.
- Feature adoption: which features are enabled, used, disabled, blocked by
  plan, blocked by permission, underused, causing support cases, or failing
  during rollout.
- Mobile health: active app versions, outdated clients, forced-update impact,
  maintenance impact, offline usage, sync failures, conflict volume, queued
  action age, device capability usage, and NativePHP permission friction.
- API health: mobile bootstrap success, authorization denials, validation
  failures, throttling, conflict decisions, response-error categories, and
  contract usage at a product level.
- Notification performance: sent, suppressed, failed, delivered where known,
  read, unread, deep-link engagement, push-permission problems, preference
  effects, and tenant targeting accuracy.
- Support operations: case volume, response time, resolution time, escalation
  rate, attachment volume, diagnostic requests, reopened cases, agent workload,
  and privacy-sensitive access.
- Billing operations: trial conversion, active plans, expired subscriptions,
  suspended billing, plan limits reached, blocked feature attempts, upgrade
  signals, manual overrides, and provider-neutral commercial health.
- Security and audit: admin activity, permission changes, role changes,
  suspicious access patterns, report exports, support access, failed access,
  account-state changes, and sensitive configuration changes.
- Product value: whether admin control, mobile access, offline sync,
  notifications, reports, security, and feature flags are creating measurable
  outcomes for tenants.

Platform reports may be cross-tenant only for authorized platform roles. Even
then, reports should prefer aggregate views first, named tenant views only when
operationally necessary, and detailed user-level views only when justified by
role, support case, security review, or explicit admin task.

## What Tenant Admins Need To Measure

Tenant admins need insight into their own tenant so they can manage users,
workflows, support, plan usage, and mobile adoption without depending on the
platform team for everyday decisions.

Tenant admins should be able to measure:

- Tenant usage: active users, invited users, suspended users, mobile adoption,
  dashboard use, records/content activity, search activity, form submission
  activity, and recent tenant activity.
- Team health: user-level or group-level activity where allowed, inactive
  users, pending invitations, permission coverage, feature adoption, training
  needs, and support needs.
- Feature usage: enabled tenant features, used features, unused features,
  disabled features, plan-blocked features, permission-blocked features, and
  feature changes that affected mobile users.
- Operational work: records/content created, updated, archived, restored,
  deleted where visible, attachment usage, status movement, tags/categories,
  local drafts awaiting sync, and mobile actions awaiting API acceptance.
- Sync health: offline users, stale clients, failed sync attempts, conflicts,
  pending actions, retry patterns, and users needing assistance.
- Notification outcomes: tenant announcements sent, unread notices, read rate,
  suppressed recipients, preference effects, deep-link use, and push-permission
  gaps.
- Support outcomes: open cases, waiting-on-user cases, waiting-on-support
  cases, resolved cases, categories, response time, attachments, and recurring
  issues.
- Billing visibility: plan label, trial/active/expired/suspended state where
  role allows, plan limits used, features unavailable due to plan, and
  contact/billing next actions.
- Security posture: tenant-level admin changes, invitation status, suspended
  accounts, permission changes, sensitive export activity, and support access
  history where allowed.

Tenant admins must not see reports for other tenants, platform-wide billing
details, platform security internals, provider payment details, support-only
private notes, or user-private mobile diagnostics outside documented tenant
authority.

## What Mobile Users Can See

Mobile reporting should stay simple. A mobile user needs status and confidence,
not a full analytics workstation.

Mobile users may see:

- Their own activity summary, such as recent work, submitted forms, records
  they can access, drafts, queued actions, and sync status.
- Current tenant context and tenant-safe status when it affects their ability
  to work.
- Feature-specific progress or counters when allowed by permission and feature
  flag.
- Unread notification counts and notification history allowed for them.
- Support request status for cases they created or are allowed to follow.
- Personal sync health, including pending, synced, failed, conflict, stale, or
  offline states.
- Simple plan or feature-unavailable messages when subscription or plan limits
  block a workflow and the role is allowed to know why.
- Export availability only when API explicitly allows the current user to
  request or open the export.

Mobile users should not see:

- Cross-tenant reports.
- Platform-wide reports.
- Tenant-wide reports unless their role explicitly allows that surface.
- Other users' private activity details unless the tenant role and report
  definition allow it.
- Billing provider internals, card details, payment failures, invoices, or
  commercial investigation notes.
- Support-only private notes or unrestricted diagnostics.
- Raw audit history beyond user-safe activity explanations.

When mobile is offline, reports should clearly state that the content is
cached, stale, or unavailable. Offline mobile may display safe cached summaries
for orientation, but it cannot create trusted report totals, refresh report
filters, or generate trusted exports without API confirmation.

## Report Privacy Boundaries

Reports often combine many events into one view, so privacy boundaries need to
be stricter than ordinary screen visibility.

Privacy principles:

- Tenant isolation is mandatory. A tenant report must contain only that tenant's
  permitted data.
- Least privilege is mandatory. Users see only reports required for their role,
  tenant, support case, billing responsibility, or mobile workflow.
- Aggregation should be used before detail. Detailed row-level report views
  need a stronger reason than counts, trends, or summaries.
- Sensitive data should be minimized. Report outputs should avoid private
  message bodies, attachment contents, credentials, tokens, payment details,
  exact diagnostics, and unnecessary PII.
- Support access should be case-scoped. Support agents should see reporting
  context needed to resolve the case, not unrestricted tenant analytics.
- Billing reports should be provider-neutral unless a future billing provider
  design documents allowed operational details.
- Mobile diagnostics should be summarized and redacted. Device, storage,
  network, permission, and crash context should help support without exposing
  private cached content.
- Exports should apply the same or stricter privacy rules as on-screen reports.
- Suspended users and suspended tenants fail closed unless a documented
  recovery/export/support path allows limited visibility.
- Report access should be auditable when it is sensitive, exported, denied,
  cross-role, support-assisted, billing-related, or security-relevant.

Privacy-safe reporting should answer the business question with the smallest
amount of data needed.

## Date Range Principles

Date ranges must be explicit because reporting decisions often depend on time.

Date range principles:

- Every report should make its date range visible: start, end, comparison
  period when applicable, generated-at time, and freshness.
- Time zone behavior should be defined. Platform views may use platform
  operations time; tenant views should normally use tenant time; mobile views
  should display user-friendly labels returned by API.
- Date ranges should avoid ambiguous labels without context. "Today" should
  mean a specific tenant or platform day, not the mobile device's local guess.
- Range boundaries should be consistent. The report definition should explain
  whether the start is inclusive, the end is exclusive, and how partial days are
  handled.
- Default ranges should be useful and safe, such as last 7 days, last 30 days,
  current billing period, current trial, or current sync window.
- Maximum ranges should protect performance, privacy, and export size.
- Comparison ranges should compare like with like, such as previous period,
  previous billing period, or previous rollout cohort.
- Offline mobile should show the range and generated-at time of cached report
  summaries so users know what they are looking at.
- Reports tied to billing, trials, app versions, maintenance, or forced updates
  should use API-authoritative dates, not mobile-local dates.
- Backfilled or delayed data should be marked when freshness affects decisions.

Date ranges are part of the report contract. They should be documented before a
report is implemented.

## Export Principles

Exports are data movement, not just downloads.

Export principles:

- Exports require explicit authorization.
- Export scope must match the user's tenant, role, permission, feature flag,
  plan, support context, and report definition.
- Platform exports may be cross-tenant only for authorized platform roles and
  should favor aggregates unless detailed data is necessary.
- Tenant exports must never include other tenants.
- Mobile exports should be limited to API-approved flows and should not be
  generated from offline cache as trusted business output.
- Exports should show date range, filters, generated-at time, data freshness,
  tenant scope, and redaction notes.
- Sensitive values should be redacted, omitted, or aggregated according to the
  report's privacy rules.
- Export links should expire. Download access should not become a permanent
  public path.
- Export requests, completions, failures, downloads, denials, and support-agent
  exports should be auditable where relevant.
- Export files should have retention and deletion principles aligned with data
  privacy and tenant deletion rules.
- Exports should not include secrets, tokens, full payment details, raw provider
  payloads, support-only private notes, or unrestricted mobile diagnostics.
- Large exports should be treated as asynchronous product behavior in future
  implementation planning, but this document does not define the job or storage
  implementation.

An export should never reveal more than the equivalent on-screen report unless
a separate documented permission explicitly allows it.

## Feature Usage Reporting

Feature usage reporting explains whether controlled features are valuable,
safe, and commercially aligned.

Feature usage reports should measure:

- Feature enabled, disabled, limited, plan-blocked, permission-blocked,
  version-blocked, maintenance-blocked, and emergency-disabled states.
- Usage frequency by tenant, role, cohort, app version, and feature flag
  rollout where privacy allows.
- Adoption trends after feature enablement, rollout, remote config change, app
  version change, onboarding, or plan change.
- Attempts to use disabled or blocked features, without exposing private
  payloads.
- Support cases, sync failures, conflicts, or notifications linked to a feature
  when relevant.
- Plan-limit pressure, such as seats, records, reports, notifications,
  storage, sync, or support limits.
- Mobile visibility effects, such as shortcuts hidden, disabled, explained, or
  unavailable due to admin/API decisions.

Feature usage reports should help admins decide whether to roll out, pause,
limit, improve, document, support, charge for, or retire a feature. They should
not let admins bypass feature flag priority, tenant isolation, or plan
ceilings.

## Sync Health Reporting

Sync health reporting tells admins whether offline-capable mobile work is safe,
current, and recoverable.

Sync health reports should measure:

- Bootstrap sync success and failure.
- Pull success, failure, lag, and freshness.
- Push success, failure, retry, queue age, and acknowledgement.
- Offline duration, stale cache age, and last successful sync.
- Pending local actions by safe category, not by private payload content.
- Failed changes and failure categories.
- Conflict detection, conflict age, conflict status, and resolution outcome.
- Rejected writes caused by permission, tenant state, plan, feature flag,
  app-version policy, validation, maintenance, or server revocation.
- Manual sync attempts and outcomes.
- Background sync health where the platform supports it.
- App version, tenant status, and mobile permission conditions that correlate
  with sync problems.

Platform admins need cross-tenant sync health to operate the SaaS. Tenant admins
need tenant-scoped sync health to help their users. Mobile users need personal
sync status and clear next steps. Support agents need case-scoped sync context
that explains what happened without exposing private cached content.

## Notification Reporting

Notification reporting explains whether messages reached the right users and
created useful action.

Notification reports should measure:

- Admin-created notifications, system notifications, security notifications,
  reminder notifications, and billing/support notifications by safe category.
- Targeted, skipped, suppressed, sent, failed, delivered where known, read,
  unread, archived, and expired states.
- In-app inbox behavior, including read/unread counts and age.
- Push-notification permission status, preference effects, quiet hours where
  configured, and device capability limits.
- Deep-link outcomes, such as opened, unavailable due to permission, unavailable
  due to feature flag, unavailable due to tenant state, unavailable due to app
  version, or opened after sync.
- Notification fatigue signals, such as repeated unread notices or preference
  opt-outs where privacy allows.
- Tenant targeting accuracy and role/permission boundary enforcement.

Notification reports should not expose private notification content to roles
that cannot view it. Security notifications may require stricter visibility and
audit than ordinary announcements.

## Support Reporting

Support reporting helps support and tenant admins understand service quality
and recurring friction.

Support reports should measure:

- Support request volume by tenant, category, feature, priority, status, and
  channel where applicable.
- Open, waiting-on-user, waiting-on-support, escalated, resolved, reopened, and
  archived support states.
- First response time, resolution time, age, backlog, and escalation patterns.
- Support messages and attachments by safe metadata, not unrestricted content.
- Diagnostic requests, diagnostic submissions, redactions, and access limits.
- Support-agent workload, handoff, internal notes, and case ownership where the
  role allows.
- Support cases connected to sync failures, notification problems, billing
  blocks, permission denials, app-version issues, remote config changes, or
  feature flags.
- Tenant-admin visibility into tenant cases without support-private notes or
  other tenants' information.

Support reports should help answer what users are struggling with, how quickly
the platform responds, which features need improvement, and where privacy-safe
diagnostics can reduce resolution time.

## Billing Reporting

Billing reporting helps platform, tenant, support, and billing roles understand
commercial access without exposing payment secrets.

Billing reports should measure:

- Plan distribution across tenants.
- Trial, active, expired, suspended, canceled, billing-blocked, and unknown
  subscription states.
- Trial conversion signals, renewal risk, expired-tenant recovery, and manual
  billing interventions.
- Plan limits used, plan limits reached, and plan limits exceeded.
- Features unavailable due to plan, quota, subscription state, or billing
  suspension.
- Upgrade/contact-admin signals, such as repeated blocked feature attempts or
  plan-limit pressure.
- Support cases connected to billing status, plan limits, or unavailable
  features.
- Notification outcomes for billing reminders, expiry warnings, and suspension
  notices.
- Manual overrides, reasons, impact previews, rollback needs, and audit
  history.

Billing reports should stay provider-neutral in this planning layer. Mobile
users should see only simple resolved outcomes, such as a feature unavailable
message, read-only state, contact tenant admin guidance, or safe plan label
when role and policy allow.

## Report Access By Role

| Role | Expected report visibility | Limitations |
| --- | --- | --- |
| Platform owner | Strategic SaaS, tenant growth, revenue-adjacent, product value, risk, and operational health reports. | Should use aggregate views first and detailed access only when operationally justified. |
| Super admin | Platform operations, tenant health, support, sync, notification, feature, security, and billing reports. | Must respect audit, privacy, support, and billing boundaries. |
| Tenant admin | Own-tenant usage, users, features, support, reports, notifications, sync health, and allowed billing/plan context. | No other tenants, platform internals, provider secrets, or support-private data. |
| Tenant manager | Delegated own-tenant operational reports. | No billing authority unless explicitly granted; no platform or cross-tenant reports. |
| Support agent | Case-scoped tenant, user, sync, notification, feature, and diagnostic context needed for support. | No broad browsing beyond assigned or authorized support scope. |
| Billing manager | Plan, subscription, limits, trial, expiry, suspension, billing support, and allowed export reports. | No unrelated private operational data or provider secrets beyond documented authority. |
| Mobile user | Personal activity, personal sync, allowed records/content summaries, notifications, and own support request status. | No tenant-wide, cross-user, billing-sensitive, support-private, or platform reports unless role allows. |
| Invited user | Minimal invitation/onboarding status if needed. | No operational reports until accepted and authorized. |
| Suspended user | No reports except safe account-state or recovery messaging. | Cached reports must be hidden or invalidated according to lock/logout/cache rules. |
| Guest/pre-login user | Public or pre-login status only. | No tenant, user, billing, sync, support, notification, or operational reports. |

## Mobile Reporting UX Principles

Mobile report surfaces should be small, clear, and action-oriented.

Mobile reporting should:

- Prefer dashboard summaries, status rows, counters, and simple trend labels
  over dense analytics pages.
- Explain loading, empty, offline, stale, permission-blocked, feature-disabled,
  plan-blocked, and tenant-suspended states.
- Show generated-at and freshness labels for cached summaries.
- Avoid showing precise private data when a role only needs a simple status.
- Use current tenant context and clear tenant labels when a user belongs to
  multiple tenants.
- Hide or disable report shortcuts when feature flags, permissions, remote
  config, app version, maintenance, tenant status, or subscription state blocks
  the report.
- Avoid panic when connectivity changes by explaining whether data is cached,
  pending, stale, or unavailable until online.
- Protect cached report summaries after app lock, logout, tenant switch,
  suspension, server revocation, or permission loss.

Mobile reporting should not become a second admin panel. Tenant-wide and
platform-wide analysis belongs in Admin/API-controlled admin surfaces unless a
specific mobile role flow is documented.

## API-First Reporting Principles

Reporting is API-first because the mobile client cannot safely resolve scope,
permissions, tenant state, subscription state, freshness, or export authority
locally.

Reporting API principles:

- Every report exposed to mobile must have a clear purpose and allowed audience.
- API responses should include report identity, tenant context, date range,
  filters, generated-at time, freshness, limits, unavailable reasons, and
  mobile-safe labels where relevant.
- API responses should distinguish empty reports from blocked reports, stale
  reports, unavailable reports, and failed reports.
- API errors should be mobile-friendly and should avoid leaking internals.
- Permission, feature flag, plan, tenant lifecycle, maintenance, and app-version
  decisions should be resolved before returning report data.
- Export authority should be separate from report-view authority.
- Sync and offline status should be visible where report freshness depends on
  mobile activity.
- Tenant boundaries must be protected server-side.

This document does not design endpoints in detail. Endpoint shape belongs in
versioned API contract documents.

## Offline Reporting Principles

Offline mobile reporting is for orientation, not authority.

Offline reporting principles:

- Mobile may display safe cached report summaries if the user is authenticated,
  the tenant context still matches, the cache is allowed, and the UI clearly
  marks the summary as cached.
- Mobile should not display cached reports after logout, logout-all-devices,
  tenant switch, suspension, lock failure, permission loss, or server revocation
  once known.
- Mobile should not generate trusted exports while offline.
- Mobile should not refresh filters, recompute tenant-wide totals, or merge
  cross-tenant summaries offline.
- Online-only report actions should be disabled with a clear explanation.
- Pending local actions should be marked as not yet reflected in reports until
  API acknowledgement.
- Sync health should explain whether stale report data is caused by offline
  state, failed sync, conflict, app-version block, feature flag, maintenance,
  tenant state, or subscription state where known.

Offline reports should help the user continue calmly, not pretend to be fresh
server truth.

## Admin Safety For Reports

Reporting controls can be dangerous because they change visibility, exports,
business decisions, and privacy exposure.

Dangerous reporting actions include:

- Granting cross-tenant report access.
- Granting report export permission.
- Exporting detailed tenant, user, support, billing, audit, notification, or
  sync data.
- Changing report definitions, metric formulas, date logic, or aggregation
  meaning.
- Enabling tenant benchmarking, cohort comparison, or named-tenant comparison.
- Sharing support diagnostics outside a case scope.
- Changing billing report visibility.
- Deleting, archiving, or changing retention for report output.
- Overriding privacy redaction.

These actions should require confirmation, reason, impact preview, audit,
rollback thinking, and tenant-specific scope. Admins should preview mobile
impact where a report changes mobile dashboard content, mobile settings,
blocked-state explanations, export availability, or cached summary behavior.

## Report Audit Principles

Report audit should help answer:

- Who viewed the report?
- Which tenant, user, support case, billing context, or platform scope did the
  report cover?
- Which date range, filters, and export options were used?
- Was access allowed or denied?
- Was the report exported, downloaded, expired, redacted, or deleted?
- Did the report involve support access, billing access, security review,
  cross-tenant scope, or sensitive diagnostics?
- Which admin action changed report visibility, metric meaning, export policy,
  or retention?
- What mobile-visible effect did a reporting change create?

Audit data itself is sensitive. It should be protected from broad visibility,
tenant leakage, tampering, and unnecessary export.

## Reporting Risks

| Risk | Principle |
| --- | --- |
| Cross-tenant leakage | Keep tenant scope server-side, fail closed, and audit sensitive access. |
| Overexposed exports | Treat exports as sensitive data movement with redaction, expiry, retention, and audit. |
| Misleading stale mobile data | Always show generated-at, freshness, cached/offline labels, and pending-action caveats. |
| Confused date ranges | Define time zone, boundaries, defaults, maximums, and comparison periods. |
| Report totals used as permission | Keep authorization separate from reporting and enforce it before returning data. |
| Support overreach | Keep support reporting case-scoped and audit sensitive views. |
| Billing privacy leaks | Keep reports provider-neutral and hide payment details from mobile and unauthorized roles. |
| Feature rollout confusion | Report enabled, disabled, blocked, and attempted states separately. |
| Sync panic | Show calm sync health, pending state, retry state, and data-loss prevention guidance. |
| Metric drift | Document metric meaning before implementation and audit changes to report definitions. |

## Acceptance Questions Before Implementation

Before implementing any report, the team should answer:

- Who is the report for?
- What decision does the report support?
- Which tenant, user, support, billing, feature, notification, sync, or security
  scope does it cover?
- Which roles can view it?
- Which roles can export it?
- Which filters and date ranges are allowed?
- Which data is excluded or redacted?
- How fresh must it be?
- What happens when mobile is offline?
- What happens when the tenant, user, feature, plan, app version, or
  maintenance state blocks the report?
- What audit history is needed?
- How can support explain the report without overexposing private data?
- What risk would exist if the report were wrong, stale, exported, or visible
  to the wrong user?

## Success Standard

Reporting succeeds when every role can see the right level of insight, no role
sees more than it should, mobile clearly distinguishes cached status from
server truth, exports are controlled and auditable, and platform decisions can
be made from privacy-safe, tenant-scoped, API-authoritative information.
