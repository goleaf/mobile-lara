# Field Service Logic

Updated: 2026-06-26

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

Logistics Delivery Logic is defined in `logistics-delivery-logic.md`:
delivery job lifecycle, pickup flow, drop-off flow, proof of delivery,
scan validation, location check-in, failed delivery reasons, offline
behavior, and admin monitoring must remain tenant-enabled, plan-controlled,
permission-aware, scan-safe, location-purpose-limited, offline-clear,
privacy-safe, auditable, and Admin/API-authoritative.

This document defines field service module logic for Mobile Lara. It explains
work order lifecycle, technician mobile flow, check-in/check-out, photos,
notes, signatures as a future capability, offline behavior, admin dispatch and
control principles, and report visibility. It is documentation only and does
not define database structure, database fields, migrations, seeders, routes,
controllers, Livewire components, Filament resources, NativePHP plugins, plugin
manifests, policies, gates, middleware, jobs, services, local storage schemas,
API endpoints, UI components, CSS, JavaScript, queue workers, report builders,
dashboards, signature capture implementation, or application logic.

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
Logic](records-content-module-logic.md), [Forms And Drafts
Logic](forms-drafts-logic.md), [Camera And Media
Logic](camera-media-logic.md), [Geolocation Logic](geolocation-logic.md),
[Notifications Logic](notifications-logic.md), [Support System
Logic](support-system-logic.md), [Reporting Logic](reporting-logic.md), and
[Device, Network, And Diagnostics
Logic](device-network-diagnostics-logic.md): field service is an optional
industry module that turns tenant-scoped work orders into mobile execution
flows, while Admin/API remains authoritative for dispatch, assignment,
permissions, work order status, location/photo acceptance, reports, audit,
support, billing, feature flags, and sync decisions.

## Field Service Statement

The field service module helps tenants coordinate work that happens away from a
desk. Admins dispatch work orders, managers monitor progress, technicians
complete mobile work, and support or operations teams review outcomes.

The product goal is not to make mobile a standalone field-service database.
The goal is to give technicians a simple, resilient NativePHP mobile flow while
Admin/API controls the official job state, assignment, dispatch priority,
tenant rules, permissions, plan access, reporting, audit, and conflict
decisions.

Product rule: a technician may capture local progress, photos, notes,
check-in/check-out events, and future signatures while offline, but a work
order is not officially accepted, reassigned, completed, cancelled, invoiced,
reported, or closed until Admin/API accepts the synced result.

## Goals

Field service logic should:

- Let admins and dispatchers create, assign, schedule, prioritize, pause,
  reassign, cancel, reopen, and review work orders through Admin/API authority.
- Let technicians see only assigned or permission-allowed work for the current
  tenant.
- Let technicians understand what must be done, where to go, what context is
  available, what evidence is required, and what is pending sync.
- Let check-in and check-out capture work presence, timing, optional location
  context, and local/offline status without becoming a surveillance feature.
- Let photos and notes provide evidence, context, and completion detail while
  respecting tenant privacy, media rules, retention, and upload limits.
- Treat signatures as a future capability that requires separate
  documentation before implementation.
- Support offline-friendly execution where field conditions make network
  access unreliable.
- Keep dispatch, status acceptance, conflict resolution, reporting, billing,
  support, and audit under Admin/API control.

Field service logic should not:

- Let mobile create trusted assignments, override dispatch, bypass
  permissions, or complete work without API acceptance.
- Treat local check-in, local photo capture, local notes, or local completion
  as official server truth while offline.
- Collect location, photos, signatures, or diagnostics without purpose,
  permission, tenant policy, and visible user understanding.
- Expose one tenant's work orders, technicians, reports, customer context,
  media, notes, or diagnostics to another tenant.
- Turn support access into unrestricted visibility of field notes, photos,
  locations, customer data, or technician activity.
- Define work order tables, status enums, assignment fields, APIs, reports, or
  native capture code in this document.

## Work Order Meaning

A work order represents tenant-scoped field work that needs assignment,
execution, evidence, review, and reporting. It may describe a visit, repair,
inspection, installation, delivery-related service, maintenance task, support
request, or customer/site job.

Admin/API owns the authoritative work order record and its lifecycle. Mobile
owns local presentation, local technician input, local drafts, offline queue,
native capture, and clear status feedback.

A work order should be understood through:

- **Tenant context**: which tenant owns the job and which tenant rules apply.
- **Assignment context**: who may see, start, update, reassign, or review it.
- **Schedule context**: expected date, time window, priority, and urgency
  meaning.
- **Site or customer context**: only the information the technician is allowed
  to see.
- **Task context**: what should be done and what proof is required.
- **Mobile context**: what is cached, pending, synced, blocked, outdated, or
  unavailable.
- **Admin context**: dispatch status, progress visibility, exceptions,
  reports, audit, and support history.

## Work Order Lifecycle

The lifecycle should be documented as business meaning, not implementation
status values. A tenant may later customize labels through remote config, but
the core lifecycle should remain consistent.

| Stage | Business meaning | Admin/API authority | Mobile behavior |
| --- | --- | --- | --- |
| Draft | Work is being prepared and should not yet appear as technician-ready. | Owns creation, required context, assignment readiness, and tenant visibility. | Hidden from normal technician flow unless explicitly allowed for pre-work. |
| Scheduled | Work has a target date/time or work window. | Owns schedule, priority, route/order meaning, customer/site visibility, and notifications. | Shows upcoming work when assigned and cached through API. |
| Assigned | A technician or team is responsible for the work. | Owns assignment, reassignment, permission, notification, and dispatch audit. | Shows in technician queue, dashboard shortcuts, and offline cache when allowed. |
| Accepted | Technician acknowledges the work if the tenant requires acceptance. | Owns whether acceptance is required and whether the acknowledgement is valid. | Captures acknowledgement or shows assigned work directly when acceptance is not required. |
| In progress | Technician has started field execution. | Owns accepted start meaning, conflict checks, and active-work visibility. | Shows active work, check-in/check-out actions, notes, photos, and completion requirements. |
| Blocked | Work cannot continue due to missing access, parts, customer absence, safety, permission, network, or other exception. | Owns blocked reason acceptance, escalation, reschedule, support, and dispatch response. | Lets technician explain the blocker, save evidence, and show pending escalation state. |
| Completed pending review | Technician believes work is complete and submitted evidence. | Owns review queue, acceptance, rejection, reopening, and audit. | Shows submitted state, pending sync/review, and any correction request. |
| Completed | Work has been accepted as complete. | Owns final completion, reporting, audit, billing handoff if relevant, and support visibility. | Shows read-only completion summary where permitted. |
| Reopened | Work must be corrected or revisited. | Owns reopening reason, reassignment, notification, and audit. | Shows clear reason, required correction, and previous local evidence status. |
| Cancelled | Work should no longer be performed. | Owns cancellation reason, customer/admin visibility, reports, and audit. | Hides or marks read-only; blocks local start or completion attempts. |
| Archived | Work is historical and available only by role/report/support rules. | Owns retention, export, report visibility, and legal/support access. | Shows only role-safe historical summaries or nothing. |

Lifecycle transitions should be audited when they affect dispatch, technician
work, customer/site expectations, reports, support, billing, or tenant risk.

## Technician Mobile Flow

The technician flow should be simple, fast, and clear:

1. **Open the app** and pass app shell gates: authentication, tenant selection,
   app lock, maintenance, forced update, feature availability, and sync state.
2. **Confirm tenant context** so the technician does not act inside the wrong
   tenant.
3. **View assigned work** through the dashboard or field service queue, with
   offline and sync indicators visible.
4. **Open a work order** and see only role-allowed details: task summary,
   schedule, priority, site/customer context, safety or access notes, required
   evidence, recent activity, attachments, and admin announcements.
5. **Start or accept work** according to tenant policy.
6. **Check in** when arriving or starting a field activity if the tenant
   requires it.
7. **Capture notes, photos, and local updates** while working.
8. **Mark blockers or exceptions** when work cannot continue.
9. **Check out** when leaving or ending work if required.
10. **Submit completion** with required evidence, notes, and future signature
    state where applicable.
11. **Review pending/synced state** so the technician knows what is saved
    locally, what is queued, what failed, and what Admin/API accepted.

Mobile should minimize typing, keep actions thumb-friendly, avoid dense admin
language, and always separate local progress from server-accepted state.

## Check-In And Check-Out

Check-in/check-out should prove workflow timing and job presence, not create
unbounded tracking.

Check-in may represent:

- Technician started travel or arrived, depending on tenant policy.
- Technician began work on site.
- Technician acknowledged site/customer conditions.
- Technician captured optional location accuracy where enabled.
- Technician created a local offline event that awaits API acceptance.

Check-out may represent:

- Technician ended work on site.
- Technician left before completion due to blocker or reschedule.
- Technician completed the work and is submitting final evidence.
- Technician created a local offline event that awaits API acceptance.

Principles:

- Admin/API decides whether check-in/check-out is required, optional, hidden,
  location-attached, photo-attached, note-required, or disabled.
- Mobile explains why check-in/check-out is requested before using native
  location or other sensitive device capability.
- Location should be purpose-limited and never collected continuously unless a
  future explicit location policy documents it.
- Offline check-in/check-out should be marked local until synced.
- Duplicate, late, out-of-order, impossible, or conflicting check-in/check-out
  events should be handled through conflict rules, not silently trusted.
- Support/admin review should show enough context to understand exceptions
  without exposing unnecessary private data.

## Photos

Photos provide evidence and context for field work, such as before/after
condition, completed service proof, damage, parts, installation state, access
issues, or safety context.

Photo principles:

- Admin/API decides whether photos are allowed, required, optional, blocked by
  plan, blocked by tenant policy, blocked by feature flag, or blocked by work
  order type.
- Mobile should request camera/media permissions only when the field service
  module and photo capability are available for the current tenant/user.
- Mobile should explain photo purpose before native permission prompts.
- Photos captured offline should remain local, protected, size-aware,
  tenant-scoped, and clearly pending until uploaded and accepted.
- Photos should be associated with a work order only after API acceptance.
- Photo metadata should be minimized; location metadata should follow
  geolocation privacy rules and tenant policy.
- Users should see upload queue state, retry state, upload failure, and
  successful sync feedback.
- Admin reports should use photo presence or evidence status where useful
  without exposing private image content to unauthorized roles.
- Support access to photos should be support-case scoped, permission-scoped,
  auditable, and privacy-aware.

## Notes

Notes capture technician observations, customer/site context, blocker reasons,
repair details, completion explanations, safety notes, follow-up needs, or
admin-visible updates.

Note principles:

- Notes should be tenant-scoped, permission-aware, and attached to the work
  order context they explain.
- Mobile should support quick notes, required completion notes, blocker notes,
  and local drafts where policy allows.
- Offline notes should be autosaved or clearly saved locally before sync.
- Notes submitted offline should remain pending until API acceptance.
- Admin/API decides which notes are technician-visible, manager-visible,
  support-visible, reportable, editable, append-only, or hidden from mobile.
- Sensitive notes should not be exposed in notifications, diagnostics,
  unrestricted exports, or support views without permission.
- Editing and deletion behavior should be documented before implementation,
  especially when notes are used for compliance, billing, dispute, or customer
  communication.

## Signatures As Future Capability

Signatures are intentionally future capability, not part of the initial field
service logic.

Before signatures are implemented, a separate documentation slice should
define:

- Who signs: customer, technician, manager, inspector, guardian, or another
  role.
- What the signature means: acknowledgement, completion approval, waiver,
  delivery receipt, safety confirmation, billing consent, or dispute evidence.
- Whether signature capture is legally meaningful in each tenant context.
- Whether signatures are required, optional, disabled, or plan-controlled.
- How identity, consent, timestamp, location, device context, and offline state
  are explained.
- How signatures are stored, displayed, exported, retained, redacted, deleted,
  audited, and protected.
- How offline signature capture behaves if API later rejects the work order,
  assignment, tenant state, app version, or permission.
- What support and admin users may see.

Until that future document exists, field service should reference signatures
only as a planned capability and should not imply that signed work is accepted
or legally binding.

## Offline Behavior

Field service should be offline-capable where useful because technicians often
work in basements, remote sites, vehicles, facilities, warehouses, or customer
locations with unreliable network access.

Offline-capable behavior may include:

- Viewing recently synced assigned work orders.
- Opening cached work order details that are safe to store locally.
- Creating local notes and drafts.
- Capturing photos into a local upload queue.
- Recording check-in/check-out events locally.
- Marking blockers or completion intent locally.
- Viewing pending/synced/failed status.
- Retrying failed uploads or sync when online.

Online/API-required behavior should include:

- Receiving new assignments.
- Confirming permission and tenant access.
- Accepting official lifecycle transitions.
- Reassigning work.
- Cancelling work.
- Closing work as accepted.
- Resolving conflicts.
- Updating plan/feature/module availability.
- Confirming billing-impacting or report-impacting outcomes.
- Viewing uncached private customer/site data.
- Submitting final photos, notes, check-in/check-out events, and future
  signatures as server truth.

Offline principles:

- Local work should be marked as local, queued, synced, failed, rejected, or
  conflict-needing-review.
- Mobile should avoid panic messaging when connection drops.
- Mobile should prevent online-only actions from appearing executable while
  offline.
- Admin/API may limit offline duration, queue size, photo count, photo size,
  cached work order count, local retention, and manual sync behavior.
- Mobile should protect offline cached work with app lock where useful and
  secure storage for secrets only.
- Conflict-prone actions should be idempotent at the API boundary and
  user-understandable when rejected or reopened.

## Admin Dispatch And Control Principles

Dispatch is a control-plane responsibility. The admin side should help the
business assign the right work to the right technician, with the right context,
at the right time, while keeping changes auditable and tenant-scoped.

Admins or dispatchers should control:

- Module enablement per tenant and plan.
- Work order visibility by tenant, role, team, technician, and lifecycle stage.
- Required evidence such as check-in, check-out, photos, notes, and future
  signatures.
- Assignment, reassignment, schedule, priority, cancellation, reopening, and
  escalation.
- Work order types, labels, instructions, safety notes, and customer/site
  visibility through documented configuration rules.
- Offline policy, queue policy, photo upload policy, sync policy, and conflict
  handling.
- Notifications to technicians, managers, support, customers, or tenant admins
  where allowed.
- Report visibility, export availability, support visibility, and audit
  history.

Dangerous admin dispatch changes should show impact before saving:

- Reassigning active work.
- Cancelling in-progress work.
- Disabling field service for a tenant.
- Disabling photos, notes, check-in/check-out, or sync while technicians have
  pending local work.
- Changing evidence requirements after assignment.
- Changing report visibility or support access.
- Changing retention or export rules.

Admin/API should audit dispatch decisions, assignment changes, evidence
requirements, lifecycle transitions, conflict decisions, support access, and
report/export actions.

## Report Visibility

Reports should help the platform owner, tenant business, tenant admins,
managers, support, and billing/operations understand field service outcomes
without exposing more detail than the viewer is allowed to see.

Field service reports may summarize:

- Work order volume by tenant, status, type, priority, technician, team, date
  range, or customer/site grouping where allowed.
- Scheduled, assigned, in-progress, blocked, completed, reopened, cancelled,
  overdue, and archived work.
- Technician workload, completion time, response time, travel/start/check-in
  timing where policy allows.
- Evidence completion such as required photos present, notes present,
  check-in/check-out present, future signature present, or missing proof.
- Offline and sync health, including pending actions, failed uploads,
  conflicts, stale clients, and app-version blocks.
- Dispatch performance, reassignment count, cancellation reasons, blocker
  reasons, reopen reasons, and support escalations.
- Feature usage, plan limits, module adoption, and billing-relevant usage where
  the viewer has permission.

Report visibility principles:

- Platform owners may see cross-tenant aggregates only where privacy and
  product policy allow.
- Tenant admins should see tenant-scoped operational reports.
- Tenant managers should see the teams, technicians, work orders, and reports
  they are allowed to manage.
- Mobile technicians should see only their own work summaries or tenant-approved
  team summaries.
- Support agents should see report context only when tied to a support purpose
  and permission.
- Billing/operations users should see billing-relevant module usage without
  unnecessary private work order content.
- Reports should not expose raw photos, raw notes, exact location, diagnostics,
  customer data, or technician activity beyond role and tenant boundaries.

## Notifications And Support

Field service notifications should be purposeful and scoped:

- Assignment notifications should tell technicians that work is available
  without exposing unnecessary customer/site details in the notification body.
- Schedule or priority changes should be visible and auditable.
- Blocker/escalation notifications should route to dispatchers, managers, or
  support roles according to tenant policy.
- Completion/reopen notifications should explain required action without
  turning notification deep links into authorization bypasses.
- Offline-created notifications should not claim server acceptance until API
  confirms the event.

Support should help resolve mobile, sync, permission, dispatch, media, and
workflow issues without broad data access. Support context should be
tenant-scoped, case-scoped, role-scoped, redacted where needed, and audited.

## Privacy And Safety

Field service may involve private homes, facilities, customer contact details,
site photos, technician location, safety notes, and operational timelines.
Privacy and safety must be part of the module design.

- Collect only the work order data needed for the job.
- Explain location, camera, file, notification, and diagnostics use before
  requesting permissions.
- Do not collect continuous location by default.
- Do not expose exact location or detailed timelines to roles that do not need
  them.
- Do not put private site/customer details in push notifications unless tenant
  policy explicitly allows it.
- Do not include raw private content in diagnostics.
- Keep field service cache tenant-scoped and protected by app lock where
  useful.
- Record dangerous admin changes and support access in audit history.

## Risks

Field service risks include:

- Technicians treating local offline status as official completion.
- Dispatchers changing assignments while technicians have pending offline work.
- Photos exposing private customer/site information.
- Notes becoming hidden compliance or billing evidence without clear rules.
- Location use feeling like tracking rather than job context.
- Reports exposing technician, customer, or site detail too broadly.
- Support users gaining excessive visibility through troubleshooting needs.
- Future signatures being interpreted as legally meaningful before policy is
  documented.
- Old mobile versions showing stale work order capability after module or plan
  changes.

Mitigation should come from Admin/API authority, feature flags, plan control,
clear mobile states, conservative privacy defaults, permission-aware reports,
auditable dispatch changes, offline sync clarity, and documentation before
implementation.

## Readiness Checklist

Before implementing field service, documentation should confirm:

- The field service module is enabled by tenant and allowed by plan.
- Work order lifecycle meaning is documented and tenant-safe.
- Technician mobile flow is documented from app open to synced completion.
- Check-in/check-out purpose, optional location use, offline behavior, and
  conflict behavior are documented.
- Photo capture, local storage, upload queue, privacy, retention, and support
  visibility are documented.
- Note behavior, sensitive-note handling, editing/deletion policy, offline
  drafts, and audit meaning are documented.
- Signature behavior remains future-only until a dedicated signature document
  exists.
- Admin dispatch, reassignment, cancellation, evidence requirements, sync
  policy, and dangerous-action impact preview are documented.
- Report visibility is role-scoped, tenant-scoped, privacy-safe, and
  support-aware.
- Offline behavior distinguishes local work from API-accepted work.
- Feature flags, remote config, app-version rules, native permissions,
  diagnostics, support, audit, and rollback behavior are documented.
