# Booking Logic

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

Updated: 2026-06-26

This document defines booking module logic for Mobile Lara. It explains
service selection, availability logic, booking request behavior,
confirmation, cancellation, reschedule, reminders, admin schedule control,
tenant rules, and mobile offline limitations. It is documentation only and
does not define database structure, database fields, migrations, seeders,
routes, controllers, Livewire components, Filament resources, NativePHP
plugins, plugin manifests, policies, gates, middleware, jobs, services, local
storage schemas, API endpoints, UI components, CSS, JavaScript, queue workers,
calendar providers, payment-provider implementation, notification-provider
implementation, report builders, dashboards, or application logic.

Use this document with [Module Selection
Principles](module-selection-principles.md), [Product
Principles](product-principles.md), [Documentation-First
Architecture](documentation-first-architecture.md), [Two-System Boundary
Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client
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
Principles](offline-first-principles.md), [Offline UX
Logic](offline-ux-logic.md), [Sync Lifecycle
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
Logic](support-system-logic.md), [Reporting Logic](reporting-logic.md),
[Device, Network, And Diagnostics
Logic](device-network-diagnostics-logic.md), [Field Service
Logic](field-service-logic.md), and [Logistics Delivery
Logic](logistics-delivery-logic.md): booking is an optional industry module
that turns tenant-scoped services, availability, appointment requests,
confirmations, cancellations, reschedules, reminders, schedule control, and
attendance-related context into mobile-visible workflows, while Admin/API
remains authoritative for availability truth, booking acceptance, conflict
prevention, cancellation policy, reschedule policy, reminder orchestration,
tenant rules, reports, audit, support, billing, feature flags, and sync
decisions.

## Booking Statement

The booking module helps tenants coordinate time-based services, appointments,
reservations, sessions, visits, consultations, classes, rentals, or capacity
limited activities. Admins define services and schedule rules. Mobile users
discover available services, request time, receive confirmation, manage
allowed changes, and understand reminders or booking state.

The product goal is not to make the mobile client a standalone calendar or
reservation authority. The goal is to give mobile users a simple, trustworthy
NativePHP booking flow while Admin/API controls official service availability,
capacity, double-booking prevention, cancellation windows, reschedule rules,
tenant policy, permissions, plan access, reporting, support, audit, and
conflict decisions.

Product rule: mobile may display cached service details, draft booking
requests, show previously confirmed bookings, and prepare change requests, but
a booking is not officially requested, confirmed, cancelled, rescheduled,
attended, charged, reported, or released until Admin/API accepts the action.

## Goals

Booking logic should:

- Let admins define and control tenant services, schedule rules, availability,
  capacity, booking windows, cancellation policy, reschedule policy,
  reminders, staff/resource visibility, and support workflows through
  Admin/API authority.
- Let mobile users select services only when the module, tenant, plan, role,
  feature flag, permission, app version, tenant state, and maintenance state
  allow it.
- Let mobile users understand whether a service is available, unavailable,
  plan-blocked, permission-blocked, fully booked, waitlisted, request-only,
  approval-required, cancelled, or offline-unavailable.
- Prevent mobile from creating double bookings, overriding capacity, bypassing
  tenant rules, or treating stale availability as final.
- Make booking requests, confirmations, cancellations, reschedules, and
  reminders clear, auditable, tenant-scoped, and mobile-friendly.
- Support offline visibility for safe cached booking information while clearly
  blocking or drafting actions that require fresh availability.
- Keep booking data private, especially user identity, personal schedule,
  service details, attendance context, notes, support context, reminders, and
  diagnostics.

Booking logic should not:

- Let mobile own availability truth, capacity truth, schedule authority,
  cancellation authority, reschedule authority, payment authority, or tenant
  rules.
- Treat cached availability, cached service details, local forms, or local
  drafts as proof that a time slot can still be booked.
- Let a notification, deep link, old app version, local cache, or stale route
  bypass current API permission and tenant checks.
- Expose one tenant's services, schedules, bookings, attendees, staff,
  resources, reports, reminders, support cases, or diagnostics to another
  tenant.
- Define service tables, schedule tables, availability algorithms, reminder
  jobs, calendar integrations, endpoints, UI screens, or code in this
  document.

## Booking Meaning

A booking represents tenant-scoped intent or commitment for a user, staff
member, resource, location, service, or capacity group at a specific time or
time window. It may describe an appointment, reservation, session, class,
visit, consultation, delivery window, rental window, check-in window, or
tenant-specific scheduled activity.

Admin/API owns the authoritative booking lifecycle. Mobile owns local
presentation, local form input, draft state, reminder display, safe cached
views, and clear feedback.

A booking should be understood through:

- **Tenant context**: which tenant owns the service, schedule, user, booking,
  policy, reports, and support visibility.
- **Service context**: what is being booked, who can book it, what duration or
  capacity rules apply, and which tenant policy controls it.
- **Availability context**: which slots or windows are currently bookable,
  request-only, approval-required, full, blocked, hidden, or expired.
- **Participant context**: who is booking, attending, managing, approving,
  supporting, or receiving reminders.
- **Schedule context**: date, time, timezone, location, resource, staff,
  capacity, lead time, cutoff, buffer, and conflict meaning.
- **Policy context**: cancellation windows, reschedule limits, no-show
  handling, reminders, waitlist rules, plan limits, and approval rules.
- **Mobile context**: cached service details, stale availability warnings,
  local drafts, online-only actions, sync status, and blocked offline states.
- **Admin context**: schedule control, capacity control, booking review,
  exception handling, reporting, audit, billing implications, and support.

## Booking Lifecycle

The lifecycle should describe business meaning, not implementation status
values. A tenant may later customize labels through remote config, but the
core meaning should remain consistent.

| Stage | Business meaning | Admin/API authority | Mobile behavior |
| --- | --- | --- | --- |
| Service visible | A service can be discovered or selected by the current user. | Owns service catalog, tenant enablement, plan access, permissions, feature flags, and availability posture. | Shows service details only when API context allows it. |
| Availability visible | Candidate dates, time slots, windows, or request options are visible. | Owns availability truth, capacity, buffers, blackout periods, staff/resource rules, and tenant policy. | Shows available/unavailable/request-only states with freshness and offline limits. |
| Draft | User is preparing a booking request or change. | Owns whether drafts are allowed and which fields are required. | Preserves local input where safe and labels it as not submitted. |
| Requested | User submitted a booking request that needs API acceptance or admin approval. | Owns validation, eligibility, capacity hold, approval routing, conflict checks, and audit. | Shows submitted, pending, or queued state without treating it as confirmed. |
| Pending confirmation | The booking is awaiting automated or admin confirmation. | Owns confirmation decision, expiration, notification, support context, and audit. | Shows waiting state, next expected outcome, and blocked change actions. |
| Confirmed | The booking is accepted and scheduled. | Owns official booking state, capacity, reminders, reports, cancellation/reschedule rules, and support visibility. | Shows confirmed details, allowed actions, reminders, and offline-safe summary. |
| Waitlisted | User wants a slot that is full or not currently confirmed. | Owns waitlist availability, priority, expiration, notification, and upgrade to confirmed. | Shows waitlist state and avoids implying confirmed attendance. |
| Reschedule requested | User or admin requested a new time. | Owns new availability checks, policy eligibility, conflict decisions, and approval. | Shows pending reschedule and preserves original booking status until API decides. |
| Rescheduled | The booking moved to a new accepted time. | Owns final schedule update, notifications, reminders, audit, and report impact. | Shows new schedule and previous state only where useful and allowed. |
| Cancellation requested | User or admin requested cancellation. | Owns cancellation eligibility, policy, penalties, refunds/credits if relevant, and audit. | Shows pending cancellation when API review is required. |
| Cancelled | The booking is no longer scheduled. | Owns capacity release, notifications, audit, report impact, support visibility, and billing effects if any. | Shows cancelled summary or hides it according to retention and role rules. |
| Expired | A draft, request, hold, waitlist offer, or unconfirmed booking passed its valid window. | Owns expiration rules, slot release, notifications, and audit. | Shows expired state and safe recovery actions. |
| Completed | The scheduled service happened or is accepted as completed. | Owns completion, attendance, report impact, billing handoff if relevant, and audit. | Shows read-only summary where permitted. |
| No-show | A participant did not attend according to tenant policy. | Owns no-show meaning, support/review, reports, restrictions, and billing implications if any. | Shows only user-appropriate summary and next action. |
| Archived | Booking is historical and available only through role/report/support rules. | Owns retention, export, deletion, report visibility, and support/legal access. | Shows role-safe historical summaries or nothing. |

Lifecycle transitions should be audited when they affect capacity, customer
expectations, staff schedules, reminders, attendance, billing, reports,
support, compliance, or tenant risk.

## Service Selection

Service selection is the point where a user chooses what they want to book.
It should be simple, filtered, and policy-aware.

Service selection should:

1. Resolve tenant context before showing services.
2. Show only services enabled for the current tenant, plan, user role,
   permission, feature flag, app version, lifecycle state, and device context.
3. Explain service name, short description, duration or time expectation,
   location/remote/service mode, basic eligibility, booking method, and
   whether admin approval is required when policy allows.
4. Hide or explain services that are unavailable because of plan, permission,
   tenant state, maintenance, app version, capacity posture, or admin disable.
5. Avoid requesting native permissions unless the chosen service requires a
   native capability and admin policy enables it.
6. Preserve clear navigation back to dashboard, search, support, and settings.

Service selection principles:

- A visible service is not a guarantee that every time slot can be booked.
- Mobile should not infer service availability from cached service cards alone.
- Services may be book-now, request-only, approval-required, waitlist-enabled,
  contact-admin, read-only, or hidden.
- Service labels and grouping may be remotely configurable, but Admin/API
  remains authoritative for the service catalog and availability.
- Service selection should minimize typing and avoid forcing users through
  admin-style scheduling concepts.

## Availability Logic

Availability is the most authority-sensitive part of booking. It controls
whether a time, resource, staff member, capacity group, or service window can
be requested or confirmed.

Availability may be influenced by:

- Tenant business hours.
- Service duration.
- Staff/resource assignment.
- Location or room availability.
- Capacity and participant limits.
- Buffers before or after appointments.
- Minimum lead time.
- Maximum advance booking window.
- Cutoff time for same-day booking.
- Blackout dates, holidays, closures, maintenance, or tenant suspension.
- Existing confirmed bookings.
- Pending holds or requests.
- Waitlist policy.
- Cancellation and reschedule policy.
- User eligibility, role, plan, feature flag, and tenant state.
- Timezone rules.
- App-version or maintenance rules.

Availability principles:

- Admin/API owns availability truth and double-booking prevention.
- Mobile may display availability returned by API, but stale availability must
  be treated as advisory.
- Availability responses should be predictable, mobile-friendly, and explicit
  about available, unavailable, full, blocked, request-only, approval-required,
  waitlist, expired, stale, offline-unavailable, and needs-refresh states.
- Mobile should refresh availability before submitting a booking request when
  online.
- Offline mobile should not promise that a slot is available.
- Cached availability should have visible freshness limits.
- Availability errors should avoid leaking hidden staff, resource, user,
  schedule, or tenant data.
- Race conditions should resolve at API authority, with friendly conflict
  feedback on mobile.

## Booking Request

A booking request is user intent to reserve, request, join, or waitlist a
service at a selected time or window.

Booking request flow should:

1. Confirm the active tenant and selected service.
2. Refresh or validate availability when online.
3. Show required booking details and policy summary before submission.
4. Collect only necessary participant, note, preference, accessibility,
   contact, or tenant-specific fields.
5. Validate input locally enough to help the user, while final validation
   remains API-owned.
6. Explain whether the action creates a request, a confirmed booking, a
   waitlist entry, or an approval-needed state.
7. Submit through API when online or save a local draft only when policy
   allows.
8. Show submitted, pending confirmation, confirmed, waitlisted, rejected,
   conflicted, expired, offline-blocked, or support-needed state.

Booking request principles:

- A local draft is not a booking request.
- A queued request is not a confirmed booking.
- The API should protect against duplicate submissions, double taps, retries,
  stale availability, capacity races, and permission changes.
- Mobile should preserve user input when a request fails for recoverable
  reasons, but it must not preserve sensitive data longer than policy allows.
- If booking requires payment, deposit, identity verification, approval, or
  external confirmation in the future, those rules require separate
  documentation before implementation.

## Confirmation

Confirmation means Admin/API has accepted the booking as official under
current rules.

Confirmation may happen:

- Immediately after a successful booking request.
- After admin approval.
- After staff or resource assignment.
- After a waitlist offer is accepted.
- After payment or external verification in a future documented flow.
- After conflict checks complete.

Confirmation principles:

- Admin/API owns the confirmed state, not mobile.
- Mobile should show confirmation clearly, including service, time, timezone,
  location/remote context, participant summary, allowed actions, reminder
  expectations, and offline-safe state.
- Confirmation should trigger appropriate in-app and push reminders according
  to notification policy and user preferences.
- Confirmation should be auditable when it affects capacity, staff/resource
  schedules, billing, support, or compliance.
- Mobile should handle delayed confirmation, rejected confirmation, and
  confirmation conflicts without losing user context.
- Confirmed bookings should remain visible offline only as cached summaries
  where privacy and tenant policy allow.

## Cancellation

Cancellation releases or marks a booking as no longer scheduled. It may be
user-initiated, admin-initiated, system-initiated, tenant-initiated, or caused
by policy, maintenance, suspension, or service unavailability.

Cancellation logic should consider:

- Who can cancel.
- How close to the booking time cancellation is allowed.
- Whether cancellation needs admin approval.
- Whether cancellation has penalties, refund/credit implications, or no-show
  implications.
- Whether cancellation releases capacity immediately or after review.
- Whether cancellation requires a reason.
- Whether cancellation notifies staff, users, support, or admins.
- Whether cancellation affects reports, attendance, billing, or support.

Cancellation principles:

- Admin/API owns cancellation eligibility and final cancellation state.
- Mobile should show cancellation policy before the user acts.
- Mobile should ask for confirmation before destructive or hard-to-reverse
  cancellation actions.
- Offline mobile should not finalize cancellation. It may draft a cancellation
  request only when policy allows and should label it as pending.
- Cancellation should distinguish requested, accepted, rejected, too late,
  blocked, already cancelled, already completed, no-show, and support-needed
  outcomes.
- Cancellation should be audited when it affects capacity, billing, reports,
  support, or tenant obligations.

## Reschedule

Rescheduling moves a booking from one accepted time or window to another.
It combines cancellation risk with new availability risk, so mobile must keep
the state especially clear.

Reschedule flow should:

1. Confirm that the booking can be rescheduled by the current user.
2. Show policy limits before the user searches for a new time.
3. Refresh availability for the same service or allowed replacement service.
4. Explain whether the original booking remains active during the request.
5. Submit the reschedule request through API.
6. Show pending, accepted, rejected, conflict, expired, original-retained, or
   support-needed state.

Reschedule principles:

- Admin/API owns reschedule eligibility, availability, conflict detection, and
  final state.
- Mobile should never silently cancel the original booking before API accepts
  the replacement unless policy explicitly defines that behavior.
- Reschedule may require approval, staff/resource reassignment, capacity
  checks, reminder updates, and audit.
- Offline mobile should not finalize reschedule because current availability
  is required.
- A reschedule conflict should preserve both the user's attempted new choice
  and the current server-accepted booking state.

## Reminders

Reminders help users attend or act on bookings without relying on memory or
stale mobile state.

Reminder types may include:

- Booking request received.
- Booking confirmed.
- Booking waiting for approval.
- Waitlist offer available.
- Upcoming booking.
- Same-day reminder.
- Check-in or preparation reminder.
- Reschedule accepted or rejected.
- Cancellation accepted or rejected.
- Admin change to service, time, location, or instructions.
- Post-booking follow-up.
- No-show or missed booking notice.

Reminder principles:

- Admin/API owns reminder rules, targeting, timing, quiet hours, eligibility,
  tenant policy, and notification preference acceptance.
- Mobile owns permission education, local display, in-app inbox presentation,
  push fallback, and user-friendly reminder state.
- A reminder should never reveal booking details the user cannot otherwise
  access.
- Reminder deep links must re-check current API authority before showing a
  protected booking.
- Offline mobile may show cached reminders, but read/unread truth and booking
  action eligibility remain API-owned.
- Reminder timing should respect tenant timezones and user-visible timezone
  context.
- Reminder changes caused by cancellation, reschedule, tenant suspension,
  forced update, maintenance, or permission revocation should fail closed.

## Admin Schedule Control

Admin schedule control lets platform admins, tenant admins, managers, or
delegated schedule operators configure booking behavior without mobile owning
policy.

Admins may control:

- Which services are enabled, hidden, request-only, approval-required,
  waitlist-enabled, suspended, or retired.
- Business hours, service hours, holidays, blackout periods, closures, and
  maintenance windows.
- Service duration, buffers, lead time, booking window, capacity, and cutoff
  rules.
- Staff, resource, room, location, or remote-service availability where the
  product supports those concepts.
- Cancellation windows, reschedule windows, no-show rules, reason
  requirements, and support escalation.
- Reminder timing, reminder categories, quiet hours, push/in-app behavior, and
  deep-link policy.
- Tenant-specific labels, instructions, terms, preparation notes, and mobile
  unavailable-state messaging.
- Which roles can create, approve, confirm, cancel, reschedule, export, report,
  support, or view bookings.
- Offline draft limits and stale availability limits.
- Emergency disable, maintenance mode, force update, and app-version
  eligibility.

Admin schedule control principles:

- Dangerous schedule changes should show impact before saving.
- Changes should be tenant-scoped unless explicitly platform-wide.
- Changes that affect existing bookings should explain affected users,
  reminders, cancellations, reschedules, capacity, reports, support, and audit.
- Admins should preview mobile impact before changing service availability,
  cancellation policy, reschedule policy, reminders, or offline limits.
- Rollback should preserve audit history and avoid silently deleting user
  requests or confirmed bookings.
- Tenant admins may control delegated booking settings only inside their
  tenant and only inside platform policy.

## Tenant Rules

Tenant rules make booking adaptable without turning each tenant into a custom
application.

Tenant rules may define:

- Which services are bookable.
- Who can book each service.
- Whether bookings are instant, approval-required, request-only, or waitlist.
- Minimum and maximum advance booking windows.
- Same-day booking behavior.
- Cutoff windows.
- Capacity limits.
- Cancellation and reschedule windows.
- Required reasons or notes.
- Reminder timing and communication categories.
- Location/remote context.
- Timezone display.
- Guest/pre-login booking posture if ever supported.
- Support escalation.
- Reporting visibility.
- Retention and privacy boundaries.

Tenant rule principles:

- Platform defaults should exist before tenant overrides.
- Tenant-specific rules may narrow or customize behavior only inside plan and
  platform policy.
- Tenant rules must not weaken tenant isolation, security, privacy, audit, or
  API authority.
- Mobile should receive resolved rules, not raw rule-building authority.
- Tenant switching should re-resolve services, availability, bookings,
  reminders, permissions, feature flags, and cache boundaries.
- Suspended, archived, billing-blocked, deletion-requested, or maintenance
  tenants should fail closed according to tenant lifecycle policy.

## Mobile Offline Limitations

Booking is less offline-capable than field service or logistics because fresh
availability is required to prevent double booking and stale commitments.

Mobile may cache:

- Service catalog summaries for the active tenant.
- User-visible service instructions.
- Previously confirmed booking summaries.
- Previously received reminder summaries.
- Cancellation and reschedule policy summaries.
- Local draft text for a booking request or support request where policy
  allows.
- Feature flags, remote config, permissions, plan outcomes, and app-version
  outcomes within documented freshness rules.
- Sync status and conflict explanations.

Mobile should not cache:

- Cross-tenant bookings, schedules, services, attendees, staff, resources,
  reminders, reports, support context, or diagnostics.
- Availability as final truth.
- Capacity counts as final truth.
- Sensitive participant data, staff schedules, private notes, or personal
  booking context longer than policy allows.
- Billing authority, tenant authority, permission authority, global config
  authority, or final booking truth.

Offline mobile may:

- Show cached confirmed bookings as read-only summaries.
- Show cached services with stale/offline labels.
- Draft a booking request only when tenant policy allows and with clear
  "not submitted" state.
- Draft a cancellation or reschedule request only when policy allows and with
  clear "not submitted" or "pending online" state.
- Show cached reminders and offline-safe instructions.
- Let the user contact support through an offline support draft where support
  policy allows.

Offline mobile must not:

- Confirm a new booking.
- Guarantee a slot remains available.
- Release capacity through cancellation.
- Move a booking through reschedule.
- Approve a waitlist offer.
- Mark attendance, no-show, completed, refunded, charged, or cancelled as
  official.
- Override tenant rules, feature flags, permissions, plan limits, force update,
  maintenance, or server revocation.

Offline principles:

- Booking actions that require availability should clearly say they need
  online confirmation.
- Local drafts should be protected from data loss but not confused with
  submitted requests.
- If a cached slot becomes unavailable before sync, mobile should show a
  conflict and preserve the user's attempted choice where safe.
- If the user, tenant, plan, feature flag, booking, service, or app version is
  revoked while offline, sync should fail closed on reconnect.

## Conflict Scenarios

Booking conflicts are normal because time slots and capacity can change
quickly.

Potential conflicts include:

- Slot was available on mobile but filled before submission.
- Booking request was submitted after service was disabled.
- Tenant policy changed while the user was drafting.
- User permission changed while offline.
- Booking was cancelled or rescheduled by admin before mobile submitted a
  change.
- Waitlist offer expired before the user accepted.
- Reschedule target became unavailable.
- Cancellation was attempted after the cancellation window closed.
- Reminder deep link points to a booking that is no longer visible to the user.
- Tenant moved to suspended, archived, billing-blocked, deletion-requested, or
  maintenance state.

Conflict principles:

- Mobile should preserve user work and explain the conflict without implying
  server failure.
- API/Admin authority decides whether a conflict can be auto-resolved, needs a
  new user choice, or requires admin/support review.
- Availability conflicts should suggest safe recovery paths such as choose a
  new time, join waitlist, contact support, or keep existing booking where
  allowed.
- Conflict decisions should be audited when they affect confirmed bookings,
  capacity, reminders, billing, support, or reports.
- Conflicts must not reveal hidden capacity, staff, users, or cross-tenant
  schedules.

## Mobile UX Principles

Booking UX should feel calm, current, and honest.

Mobile should:

- Show service, time, timezone, location/remote context, status, and next
  action clearly.
- Keep service selection and time selection simple.
- Use mobile-friendly states such as available, unavailable, full,
  request-only, approval needed, waitlist, confirmed, pending, cancelled,
  rescheduled, expired, offline, and conflict.
- Warn when availability is stale or offline.
- Explain cancellation and reschedule rules before destructive or important
  actions.
- Avoid unnecessary typing by using structured choices, short notes, and
  remembered safe preferences where policy allows.
- Preserve drafts without pretending they are submitted.
- Show reminders and deep links only when current permissions allow.
- Hide or explain booking entry points blocked by tenant, plan, permission,
  feature flag, app version, maintenance, or offline state.

Mobile should not:

- Use admin scheduling language in user-facing booking flows.
- Let cached availability appear as guaranteed availability.
- Let stale notification deep links bypass current booking checks.
- Request native permissions for disabled or unavailable booking features.
- Hide why a booking cannot be requested, cancelled, or rescheduled when a
  safe explanation is possible.

## Privacy And Security Principles

Booking data may reveal personal schedules, health or service needs,
locations, staff schedules, business demand, attendance, absence, and tenant
operations. It should be treated as sensitive tenant data.

Privacy and security principles:

- Tenant isolation applies to services, schedules, bookings, reminders,
  participants, staff/resource context, reports, support, diagnostics, and
  audit history.
- Least privilege applies to platform admins, tenant admins, managers, support
  agents, billing users, and mobile users.
- Booking notes should be minimized and protected.
- Reminder previews should avoid sensitive booking details where lock-screen,
  shared-device, or privacy policy risk exists.
- Support agents should see only the booking context needed to resolve the
  issue.
- Reports should prefer aggregate trends over personal schedules or private
  notes.
- Diagnostics should explain device, network, sync, app version, notification,
  and offline issues without exposing personal booking details.
- Suspended users, suspended tenants, billing-blocked tenants, revoked devices,
  maintenance mode, and forced updates should fail closed.
- Audit history should protect schedule integrity without becoming a broad
  data-browsing surface.

## Reporting Principles

Booking reporting should help tenants understand schedule demand, capacity,
attendance, reminders, and operational health without exposing unnecessary
personal details.

Reports may summarize:

- Service usage.
- Booking requests, confirmations, cancellations, reschedules, waitlist,
  no-shows, completions, and expirations.
- Capacity usage and unavailable slots.
- Cancellation and reschedule reason trends.
- Reminder delivery, unread, and engagement patterns.
- Approval queue health.
- Offline drafts, stale availability conflicts, and sync failures.
- Support volume tied to booking flows.
- Plan or module usage where billing/operations users have permission.

Reporting should:

- Keep tenant reports inside tenant boundaries.
- Show mobile users only personal or role-safe booking summaries.
- Show tenant admins operational outcomes for their tenant.
- Show platform admins cross-tenant health only when their role allows it.
- Keep exports scoped, auditable, privacy-aware, and retention-aligned.

## Support Principles

Support workflows should help resolve booking issues without granting
unrestricted access to schedules or personal booking data.

Support may need to understand:

- Which tenant, service, booking, user, device class, app version, reminder,
  or sync state was involved.
- Whether a request was drafted, submitted, pending, confirmed, cancelled,
  rescheduled, rejected, conflicted, or expired.
- Whether availability changed before submission.
- Whether notifications, offline state, app version, tenant state, feature
  flags, plan limits, or permission changes blocked the workflow.

Support should not receive unrestricted access to participant details, staff
schedules, private notes, exact attendance patterns, reminder contents,
billing-sensitive details, or diagnostics unless the support role, tenant
policy, privacy policy, and audit rules allow it.

## Rollout And Rollback Principles

Booking should be introduced gradually because it touches availability,
capacity, reminders, cancellations, reschedules, reports, support, privacy,
and tenant operations.

Rollout principles:

- Start with documentation, pilot tenants, feature flags, limited services,
  and simple availability rules.
- Resolve booking availability through platform catalog, plan entitlement,
  tenant enablement, permissions, feature flags, remote config, app version,
  device support, and offline policy.
- Prefer request-only or approval-required pilots before instant confirmation
  if availability rules are not mature.
- Add reminders, waitlists, cancellation, reschedule, reports, and support
  visibility in controlled stages.
- Review privacy, support, reporting, audit, and tenant-rule impact before
  expanding.

Rollback principles:

- Emergency disable should hide booking entry points, stop new requests where
  safe, preserve already captured drafts according to policy, and explain what
  will happen when online.
- Admins should understand pending requests, confirmed bookings, reminders,
  cancellations, reschedules, and support cases before disabling a tenant or
  service.
- Rollback should not silently delete user drafts or confirmed booking
  summaries.
- Rollback should not expose cross-tenant data or bypass API authority.

## Risks

Key risks:

- Mobile stale availability is mistaken for confirmed availability.
- Race conditions create double bookings or over-capacity bookings.
- Cancellation or reschedule policy is unclear to users.
- Reminders reveal sensitive booking details.
- Admin schedule changes affect existing users without impact preview.
- Offline drafts are confused with submitted booking requests.
- Tenant-specific rules become inconsistent or weaken platform controls.
- Reports expose personal schedules, staff schedules, or private notes too
  broadly.
- Support receives more sensitive booking data than needed.
- Plan, feature flag, or tenant lifecycle changes strand users with unclear
  unavailable states.

Risk controls:

- Keep Admin/API authoritative for service catalog, availability,
  confirmation, cancellation, reschedule, reminders, reports, audit, billing,
  support, and conflict decisions.
- Keep mobile clear about local draft, submitted, pending, confirmed,
  cancelled, rescheduled, rejected, expired, and conflict states.
- Use tenant isolation, least privilege, feature flags, remote config,
  app-version gates, privacy rules, and audit history.
- Require impact preview and confirmation for dangerous admin schedule
  changes.
- Document every booking workflow before implementation.

## Readiness Checklist

Before implementing booking behavior, the product documentation should answer:

- Which tenants and plans can use the booking module?
- Which services are bookable, request-only, approval-required, waitlistable,
  hidden, suspended, or retired?
- Which roles can view, request, confirm, approve, cancel, reschedule, manage,
  export, report, support, or audit bookings?
- Which availability rules apply to each service?
- Which cancellation rules apply?
- Which reschedule rules apply?
- Which reminder rules apply?
- Which tenant rules can override platform defaults?
- Which actions can be drafted offline and which require fresh online API
  access?
- How should mobile show stale availability, pending requests, confirmations,
  cancellations, reschedules, conflicts, and offline limitations?
- How should admins preview mobile impact before schedule changes?
- What data appears in reports, exports, diagnostics, support views, reminders,
  and audit history?
- What happens when a tenant, plan, feature flag, permission, app version,
  service, availability rule, booking, or reminder changes while the device is
  offline?

## Acceptance Principle

The booking module is ready for implementation only when the team can trace
every mobile booking action to:

- A documented tenant and plan rule.
- A documented permission rule.
- A documented feature flag and remote config rule.
- A documented API authority.
- A documented availability and conflict outcome.
- A documented cancellation and reschedule policy.
- A documented reminder rule.
- A documented offline limitation.
- A documented privacy and retention boundary.
- A documented audit and reporting meaning.
- A documented support visibility rule.

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

If any of those are unclear, the correct next step is more documentation, not
application code.
