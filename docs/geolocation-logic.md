# Geolocation Logic

Final Optimized SaaS Blueprint is defined in `final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

Updated: 2026-06-26

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

This document defines geolocation logic for the Mobile Lara NativePHP client.
It explains check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin control
through feature flags, how users should understand location use, and when
location should never be collected. It is documentation only and does not
define database structure, database fields, migrations, seeders, routes,
controllers, Livewire components, Filament resources, NativePHP plugins,
plugin manifests, policies, gates, middleware, jobs, services, local storage
schemas, API endpoints, UI components, CSS, JavaScript, queue workers, map
providers, or application logic.

Use this document with [Product Principles](product-principles.md),
[Documentation-First Architecture](documentation-first-architecture.md),
[Two-System Boundary Logic](two-system-boundary.md), [API-First
Principles](api-first-principles.md), [Admin/API
Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Role And Permission
Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version
Control Logic](mobile-version-control-logic.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Data Privacy
Principles](data-privacy-principles.md), [Audit Logic](audit-logic.md),
[Mobile Permission Logic](mobile-permission-logic.md), [Native Feature
Strategy](native-feature-strategy.md), [Offline-First
Principles](offline-first-principles.md), [Offline UX
Logic](offline-ux-logic.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md),
[Conflict Resolution Logic](conflict-resolution-logic.md), [Records/Content
Module Logic](records-content-module-logic.md), [Forms And Drafts
Logic](forms-drafts-logic.md), [Support System Logic](support-system-logic.md),
[Mobile Settings Logic](mobile-settings-logic.md), [Mobile App Lock
Principles](mobile-app-lock-principles.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), [Reporting Logic](reporting-logic.md), and
[Scanner Logic](scanner-logic.md): geolocation workflows are tenant-scoped
native-assisted context workflows, while Admin/API remains authoritative for
feature eligibility, location purpose, check-in acceptance, record attachment
acceptance, accuracy requirements, privacy policy, retention, audit,
reporting, support visibility, and sync truth.

## Geolocation Statement

Geolocation helps mobile users prove, explain, or contextualize work only when
the product has a clear purpose for location.

Location can support check-ins, check-outs, field verification, worksite
context, route context, nearby work, delivery or service proof, incident
context, support diagnostics, and location-attached records. It is also highly
sensitive because coordinates can reveal homes, workplaces, customer sites,
travel patterns, safety risks, habits, and tenant operations.

Product rule: a device location result is sensitive local input until the API
accepts it for the current tenant, user, feature flag, permission, plan, app
version, and sync state. Mobile may request, display, cache, queue, retry, or
discard location intent. Admin/API decides whether location is required,
optional, accepted, rejected, audited, reportable, retained, visible to
support, or deleted.

## Goals

Geolocation logic should:

- Let users check in or check out when a tenant workflow requires location
  proof.
- Let users attach location to records only when the record workflow and admin
  policy allow it.
- Display location accuracy clearly enough for the user to understand whether
  the result is precise, approximate, stale, pending, or unacceptable.
- Explain why location is needed before requesting native permission.
- Support offline location capture only as local pending work when policy
  allows it.
- Protect privacy through minimization, tenant isolation, retention limits,
  support visibility limits, and safe diagnostics.
- Let admins control location workflows through feature flags, remote config,
  app-version rules, plan limits, tenant rules, role permissions, and
  emergency disablement.
- Help users understand when location is required, optional, unavailable,
  denied, approximate, too inaccurate, offline pending, synced, or rejected.
- Define strong rules for when location should never be collected.

Geolocation logic should not:

- Collect location silently.
- Collect location at first launch without a user-visible location workflow.
- Treat native location permission as SaaS permission.
- Treat coordinates as server truth before API acceptance.
- Track continuous background location unless a separate documented product
  decision explicitly authorizes it.
- Use location for billing, attendance, compliance, performance review,
  reports, support, or enforcement without clear admin policy and user
  explanation.
- Store raw coordinates longer than policy allows.
- Expose exact location in support diagnostics, reports, exports, logs, or
  mobile history unless explicitly allowed.
- Collect location for disabled, hidden, unlicensed, unsupported,
  tenant-blocked, user-blocked, app-version-blocked, maintenance-blocked, or
  offline-ineligible workflows.
- Define geolocation plugins, provider payloads, database tables, queue
  schemas, endpoints, or code in this document.

## Ownership And Authority

| Concern | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Location availability | Whether location, check-ins, check-outs, location-attached records, offline capture, location history, maps, nearby work, and manual fallback are enabled by global rollout, tenant, plan, role, user, app version, platform, maintenance state, and emergency state. | Showing only eligible location entry points, explaining disabled states, and avoiding native permission prompts when workflows are unavailable. |
| Location purpose | Which product workflows may request location and whether location is required, optional, prohibited, or support-only. | Explaining the purpose in user language before native permission or capture. |
| Permission authority | SaaS role permission, tenant policy, feature flag, subscription entitlement, app-version eligibility, device trust, and revocation. | Native permission education, requesting permission just in time, displaying permission status, and helping users recover from denial. |
| Check-in authority | Whether a check-in/check-out is accepted, rejected, duplicate, late, outside allowed area, too inaccurate, stale, needs review, or reportable. | Capturing local location intent, showing pending or failure state, and replaying only through API/sync. |
| Record authority | Whether a record may include location, whether location is required, optional, hidden, redacted, retained, removed, or visible to admins/support. | Showing local draft state, accuracy, and clear attach/remove controls where allowed. |
| Accuracy authority | Required accuracy, age limits, provider preferences, stale thresholds, fallback rules, and review thresholds. | Displaying accuracy, provider class, freshness, and whether the reading meets policy. |
| Offline behavior | Which location workflows may run offline, queue limits, replay windows, accuracy rules, conflict rules, and emergency disablement. | Local capture, local pending labels, retry UX, tenant-scoped cleanup, and sync status presentation. |
| Privacy and audit | Retention, masking, support visibility, reports, exports, deletion, legal hold, audit meaning, and safe diagnostics. | Minimizing local display/storage, avoiding raw leakage, explaining user-facing privacy state, and clearing local state when required. |

The mobile client can collect a location reading. Admin/API decides what that
reading is allowed to mean.

## Native Capability Model

NativePHP geolocation is a permissioned native capability. Current NativePHP
Mobile documentation describes network-based location as faster and less
accurate, GPS/fine accuracy as slower and more accurate, permission checking
and permission requesting, and asynchronous location result events that include
success or failure, latitude, longitude, accuracy in meters, timestamp,
provider, and error context.

Product implications:

- Location results should be treated as asynchronous native events, not direct
  business decisions.
- Network and GPS/fine accuracy are product choices, not just technical
  options. Higher accuracy can be more sensitive, slower, more battery-heavy,
  and more intrusive.
- Permission status should be handled separately from tenant/role permission.
- A location event can succeed, fail, time out, be too inaccurate, be stale,
  use an unexpected provider, arrive after navigation, arrive after tenant
  switch, or arrive after app lock.
- Browser/development fallback may show disabled, manual, mocked, or
  unsupported states, but must not prove real native geolocation behavior.
- Geolocation should be wrapped behind a product workflow such as check-in,
  field verification, nearby work, or location-attached record. The plugin is
  an implementation detail.
- The mobile app should not retain raw native errors or provider payloads when
  safe outcome categories are enough.

The product should define location outcomes before implementation: available,
disabled by admin, blocked by plan, blocked by permission, permission needed,
permission denied, permanently denied, unavailable, unsupported, locating,
found, too inaccurate, stale, offline pending, synced, rejected, duplicate,
needs review, and support needed.

## Check-Ins

Check-ins and check-outs are location-aware user actions that prove presence,
arrival, departure, work start, work completion, route stop, attendance, or
tenant-specific field activity.

Check-in principles:

- Check-in should be available only when location, check-in workflow, tenant,
  user role, permission, subscription, app version, device capability,
  maintenance state, and offline policy allow it.
- The user should understand what the check-in means before location is
  collected.
- The app should explain whether location is required or optional for the
  check-in.
- The app should show whether it is finding location, found location, using
  approximate location, waiting for better accuracy, saving locally, syncing,
  synced, failed, rejected, duplicate, or needs review.
- The API owns check-in acceptance, duplicate handling, allowed area policy,
  time-window policy, accuracy policy, audit history, reports, and support
  visibility.
- Mobile should not silently check in because a location fix was received.
- Mobile should not silently check out because the user moved.
- Repeated check-in taps, repeated native events, or offline replay should not
  double-count attendance, work, arrival, departure, or service completion.
- If a user checks in offline, the app should label the action as pending until
  the API accepts it.
- If the API rejects a pending offline check-in, the app should preserve safe
  context for correction or support without implying success.
- If the user is in the wrong tenant context, the app should block or re-check
  authority before location is collected or submitted.

Check-ins should be intentional. Presence proof is sensitive, so accidental
location capture is a product failure.

## Location-Attached Records

Location-attached records let a user add location context to tenant business
content such as records, inspections, incidents, deliveries, service visits,
support requests, assets, notes, or evidence.

Location-attached record principles:

- Location attachment should be controlled by the record module, current
  tenant, feature flags, role permissions, plan, app version, and offline
  policy.
- A record may require location, allow optional location, forbid location, or
  allow location only for certain statuses or workflows.
- Mobile should show whether the location is local draft data, pending sync,
  accepted by API, rejected, removed, redacted, or hidden by policy.
- Mobile should let users remove optional location from drafts where policy
  allows.
- Required location should be explained before the user starts the record
  action, not after the form is complete.
- API acceptance decides whether location becomes part of the record, activity
  timeline, audit history, report, support context, or export.
- Location should not be reused across records unless a future documented
  workflow explicitly allows it.
- If a record becomes inaccessible, archived, deleted, conflicted, or
  permission-blocked before sync, location attachment should be stopped,
  rechecked, or rejected according to API policy.
- Media EXIF, file metadata, and scanner payloads should not silently attach
  location to records unless the location policy explicitly allows it.

Location can support record context, but it should not become hidden
surveillance metadata.

## Accuracy Display

Accuracy display helps users understand whether the location reading is fit for
the workflow.

Accuracy principles:

- The app should display accuracy in user-understandable terms, such as exact
  enough, approximate, low accuracy, stale, unavailable, or waiting for better
  signal.
- When meters are shown, they should be explained as an accuracy radius rather
  than perfect certainty.
- The app should distinguish provider class where useful: GPS/fine accuracy,
  network/approximate, cached/stale, simulator/development, unknown, or failed.
- The app should show when accuracy does not meet policy and what the user can
  do: retry, move outdoors, enable precise location, use manual fallback,
  continue without location if allowed, or contact support.
- The app should show freshness or timestamp meaning when stale location is a
  risk.
- A precise-looking map pin should not imply perfect truth.
- Accuracy should not be hidden when it affects acceptance, reports, support,
  or audit.
- Admin/API should decide the minimum accuracy and maximum age for each
  workflow.
- Mobile should avoid showing raw coordinates when a safe label, map preview,
  approximate area, or accepted/rejected outcome is enough.

Users should know whether the app has a useful location reading, not just that
the app has a coordinate.

## Permission Explanation

Location permission is a trust moment. The app should explain it before the OS
prompt appears.

Permission explanation should answer:

- Why does this workflow need location?
- Is location required or optional?
- Will the app use precise or approximate location?
- Will location be used once, attached to a record, attached to a check-in, or
  stored for later sync?
- Will location be visible to admins, tenant managers, support, reports, or
  only the current user?
- What happens if permission is denied?
- What fallback exists?
- How can the user change permission later?

Permission principles:

- Ask just in time, when the user starts an enabled location workflow.
- Do not request location at first launch only because a plugin exists.
- Do not request location when the feature is disabled by admin, plan, tenant,
  role, app version, maintenance mode, device capability, or offline policy.
- Do not pressure the user with vague wording.
- Keep native permission separate from SaaS authorization. A user can grant
  location permission and still be blocked by tenant policy.
- If permission is denied or permanently denied, show recovery through settings
  or a safe fallback where policy allows.
- Permission status should be visible in settings: granted, denied,
  permanently denied, not determined, approximate/limited, unavailable,
  unsupported, disabled by admin, blocked by plan, blocked by tenant, or not
  needed.
- Remote config may adjust wording and support links, but it must not override
  feature eligibility or privacy policy.

Permission explanation should make location use feel specific, bounded, and
understandable.

## Offline Location Behavior

Offline location behavior should keep users productive without pretending that
offline location is trusted server truth.

Offline location may allow:

- Capturing one current location reading for an enabled workflow.
- Attaching location to a local record draft when policy allows it.
- Creating a pending check-in or check-out intent when offline check-ins are
  allowed.
- Showing cached worksite, tenant, or route context that was already allowed
  for offline use.
- Displaying local accuracy, timestamp, provider class, and pending state.
- Retrying location-attached work when connectivity returns.

Offline location must wait for online API access when:

- The workflow requires current tenant, permission, billing, app-version,
  maintenance, or feature confirmation that is not safely cached.
- The location would finalize a check-in, check-out, record, attendance event,
  delivery proof, compliance event, audit event, billing event, or reportable
  server fact.
- Current duplicate detection, allowed-area checks, time-window checks,
  conflict decisions, or support review are required.
- The cached policy is stale, revoked, unknown, or emergency-disabled.
- The user, tenant, session, or app version is suspended, revoked, unknown, or
  blocked.

Offline UX principles:

- Label offline location as local, pending, stale, queued, synced, rejected, or
  needs review.
- Show whether the location was captured while offline.
- Show whether a pending action can be canceled before sync.
- Preserve enough safe context for user correction after rejection.
- Avoid keeping raw location forever when sync fails.
- Apply tenant-scoped cleanup on logout, tenant switch, revocation, app lock,
  forced update, or privacy policy trigger.

Offline location is a queued intent, not proof until accepted.

## Privacy Boundaries

Location is sensitive data and should be minimized by default.

Privacy principles:

- Collect the minimum location needed for the workflow.
- Prefer one-time location over continuous tracking unless a separate
  documented decision justifies ongoing collection.
- Prefer approximate or reduced precision when exact coordinates are not
  necessary.
- Avoid storing raw coordinates when a safe accepted outcome, approximate area,
  worksite match, or redacted label is enough.
- Avoid exposing exact location in notifications, lock-screen content,
  support summaries, diagnostics, reports, exports, screenshots, logs, or
  analytics by default.
- Do not attach EXIF or media-derived location silently.
- Do not mix location across tenants.
- Clear or hide local location data on logout, tenant switch, session
  revocation, user suspension, tenant suspension, retention expiry, remote
  wipe policy, or app lock where policy requires it.
- Support access should be least-privilege and case-scoped.
- Admin reports should prefer aggregate or purpose-bound location outcomes
  over raw movement trails.
- Audit should describe accepted location decisions while protecting exact
  coordinates unless exact coordinates are required for the audit purpose.

Location should answer a business workflow question. It should not create a
shadow movement history.

## Admin Control Through Feature Flags

Location workflows must be controlled by feature flags and remote config
because location changes privacy, permissions, reporting, support, sync, and
tenant risk.

Admin controls may govern:

- Geolocation capability visibility.
- Check-in and check-out availability.
- Location-attached record availability.
- Optional versus required location per workflow.
- Approximate versus precise location requirements.
- Accuracy thresholds.
- Maximum allowed age of a location reading.
- Offline location capture.
- Offline queue limits and replay windows.
- Manual fallback.
- Map previews or nearby work features.
- Allowed tenants, roles, users, cohorts, plans, app versions, and platforms.
- Support-visible location context.
- Report and export visibility.
- Retention, redaction, and deletion policy.
- Emergency disablement.

Admin UX principles:

- Admins should see the mobile impact before enabling or disabling location
  features.
- Impact preview should include permission prompts, check-in surfaces,
  record forms, offline queues, sync behavior, reports, support views,
  privacy language, and plan messages.
- Tenant-specific location changes should not affect other tenants.
- Dangerous changes should require confirmation and audit history.
- Emergency disablement should stop new location collection, explain pending
  queued location behavior, and preserve or discard local work according to
  documented privacy policy.

Feature flags make location rollout visible, reversible, and tenant-safe.

## How Users Should Understand Location Use

Users should never have to guess why the app wants their location.

User-facing understanding principles:

- Explain location in relation to the task, not the technology.
- Distinguish "used once for this action" from "stored with this record" from
  "submitted for admin review" from "shown in reports."
- Distinguish device permission from admin/tenant permission.
- Distinguish local pending location from synced/accepted location.
- Distinguish approximate from precise location.
- Distinguish location unavailable from permission denied.
- Distinguish too inaccurate from rejected by admin policy.
- Show when location is optional and how to continue without it.
- Show when location is required and why the workflow cannot continue without
  it.
- Provide settings recovery for denied or permanently denied permission.
- Provide support paths when location is required but unavailable.

The app should be plain with users: what is collected, why, when it is sent,
who may see it, and what happens if it fails.

## When Location Should Never Be Collected

Location should never be collected when the product cannot explain a current,
specific, tenant-authorized purpose.

Never collect location when:

- The feature is disabled by admin, plan, tenant, role, app version,
  maintenance mode, emergency state, or remote config.
- The user is not actively starting a location-aware workflow.
- The app is in a pre-login, guest, welcome, or onboarding state without a
  documented location purpose.
- The user is suspended or the tenant is suspended, archived, billing-blocked,
  deleted, or unknown.
- The app is locked and location collection would bypass app-lock policy.
- The session has been revoked or cannot be trusted.
- The user denied permission and the workflow has a non-location fallback.
- The workflow can complete safely without location and location is not
  optional user intent.
- The app is only trying to improve analytics, personalization, marketing, or
  convenience without explicit policy.
- Support diagnostics do not explicitly require location and policy does not
  permit it.
- The location would be collected continuously or in the background without a
  separate documented decision, admin control, user explanation, and privacy
  review.
- The app is running in browser/development fallback and cannot provide real
  native permission and location behavior.
- The location would cross tenant boundaries, reveal another tenant's context,
  or be reused outside its original workflow.

When in doubt, do not collect location. Ask for API-confirmed policy first.

## API And Sync Principles

Location workflows should use API-first communication for anything
authoritative.

API and sync principles:

- Mobile sends location intent through API or sync. It does not invent server
  meaning.
- API responses should be predictable and mobile-safe for accepted, rejected,
  duplicate, too inaccurate, stale, permission denied, feature disabled, plan
  blocked, version blocked, maintenance, offline pending, conflict, and needs
  review states.
- Location actions that can be replayed should be idempotent at the API
  boundary.
- Offline queued location actions should include enough context for safe
  reconciliation without trusting stale mobile authority.
- Conflict decisions belong to Admin/API, with mobile presenting the outcome
  and preserving user work where possible.
- Tenant boundaries must be enforced before accepting, showing, reporting, or
  exporting location.
- API errors should protect privacy and avoid exposing exact location or
  internal policy details unnecessarily.

Geolocation API behavior can be designed later. The principle is fixed now:
location reaches authority only through API-controlled contracts.

## Reporting, Support, And Audit

Location reporting should answer operational questions without becoming broad
movement surveillance.

Reporting and support principles:

- Admin reports may show check-in completion, location-required workflow use,
  accuracy failure categories, offline pending counts, rejection categories,
  and sync health where tenant and privacy rules allow.
- Tenant admins should see location information only for their tenant and only
  where their role permits it.
- Mobile users should see their own check-in/location state, pending actions,
  failures, and accepted outcomes where useful.
- Support agents should see safe context first: feature enabled state, app
  version, permission state, device class, provider class, accuracy category,
  offline status, outcome category, and tenant context.
- Support should not see exact coordinates by default.
- Audit history should help answer who requested location, for what workflow,
  in which tenant, whether permission was granted, what accuracy category was
  used, whether the action was offline, whether it replayed, and what authority
  accepted or rejected it.
- Audit records should protect exact coordinates through minimization,
  masking, or controlled access unless exact location is required for the audit
  purpose.

Location data should help the business operate responsibly, not create
unbounded surveillance.

## Risks

Geolocation implementation should not begin until these risks are documented
for the target workflow:

- Silent collection: the user does not understand that location is being
  requested or stored.
- Over-precision: the app collects exact coordinates when approximate context
  would be enough.
- Stale proof: offline or cached location is treated as current proof.
- Duplicate check-ins: retries or repeated native events create multiple
  accepted actions.
- Cross-tenant leakage: a location captured in one tenant context is submitted
  under another.
- Support overexposure: diagnostics include exact coordinates unnecessarily.
- Report overreach: reporting turns workflow location into movement tracking.
- App-version drift: old clients submit location without current policy.
- Permission confusion: device permission is mistaken for SaaS authorization.
- Background creep: one-time location slowly becomes continuous tracking
  without a documented product decision.

Each location workflow should define risk controls before code is written.

## Implementation Readiness Checklist

Before implementing a geolocation workflow, documentation should answer:

- What product problem requires location?
- Is location required, optional, prohibited, or support-only?
- Is the workflow check-in, check-out, location-attached record, nearby work,
  support diagnostic, or another documented purpose?
- Which admin feature flags, remote config values, plan limits, permissions,
  roles, app versions, platforms, tenant states, and maintenance states
  control it?
- What does the mobile app show before requesting location permission?
- Is approximate location enough, or is precise location required?
- What accuracy and freshness are required?
- What happens when permission is denied, permanently denied, unavailable,
  unsupported, too inaccurate, stale, or offline?
- What manual or no-location fallback exists?
- What is safe to cache locally?
- What must never be cached?
- What can happen offline?
- What must wait for online API authority?
- How are duplicates, conflicts, replays, and rejected pending actions shown?
- Who can see accepted location data?
- What support diagnostics are safe?
- What audit and reporting questions should the workflow answer?
- When must location be deleted, redacted, hidden, or excluded?
- How is user work protected if the app locks, tenant switches, user logs out,
  session is revoked, or app version becomes unsupported?

If a geolocation workflow cannot answer those questions in documentation, it
is not ready for implementation.
