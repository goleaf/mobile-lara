# Billing And Plan Logic

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

This document defines billing and subscription logic for the Mobile Lara SaaS
system. It explains plan-based access, trial behavior, active subscription
behavior, expired subscription behavior, suspended billing behavior, plan
limits, how plan limits affect feature flags, what mobile users see when a
feature is unavailable due to plan, and what admins can control manually. It is
documentation only and does not define billing provider implementation,
provider webhooks, payment methods, checkout flows, invoices, database
structure, database fields, migrations, indexes, seeders, routes, controllers,
Livewire components, Filament resources, NativePHP plugins, policies, gates,
middleware, jobs, services, local storage schemas, API endpoints, UI
components, CSS, JavaScript, queues, or application logic.

Use this document with [Product Principles](product-principles.md), [Target
User Roles](user-roles.md), [Role And Permission Logic](role-permission-logic.md),
[Tenant Lifecycle Logic](tenant-lifecycle-logic.md), [Tenant Admin
Logic](tenant-admin-logic.md), [Two-System Boundary Logic](two-system-boundary.md),
[API-First Principles](api-first-principles.md), [Admin/API Responsibilities](admin-api-responsibilities.md),
[Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX
Principles](mobile-ux-principles.md), [Mobile App Shell
Logic](mobile-app-shell-logic.md), [Mobile Dashboard Logic](mobile-dashboard-logic.md),
[Mobile Settings Logic](mobile-settings-logic.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md),
[Mobile Version Control Logic](mobile-version-control-logic.md), [Admin Control
Center Logic](admin-control-center-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Data Privacy Principles](data-privacy-principles.md),
[Audit Logic](audit-logic.md), [Notifications Logic](notifications-logic.md),
[Support System Logic](support-system-logic.md), [Offline-First
Principles](offline-first-principles.md), [Offline UX Logic](offline-ux-logic.md),
[Sync Lifecycle Logic](sync-lifecycle-logic.md), [Reporting
Logic](reporting-logic.md), [Native Feature
Strategy](native-feature-strategy.md), and [API v1 Billing
Contract](../contracts/api/v1-billing.md): billing is an Admin/API-owned
commercial authority layer, and mobile receives only safe entitlement, plan,
limit, blocked-state, contact-admin, support, reporting, and freshness
outcomes.

## Billing Statement

Billing and subscription logic determines what a tenant is commercially allowed
to use.

A plan is a commercial package. A subscription state is the tenant's current
commercial standing. A plan limit is a controlled ceiling on feature access,
usage, seats, storage, support, sync, reports, notifications, or NativePHP
capability availability. Billing logic does not replace permissions, feature
flags, tenant lifecycle, security, or API authorization. It adds a commercial
gate that those systems must respect.

Product rule: Admin/API owns billing authority, plan rules, subscription state,
plan limits, entitlement resolution, manual overrides, billing audit, and
provider-facing decisions. Mobile owns only clear presentation of the resolved
outcome, local stale-state labels, contact-admin/support guidance, and safe
blocked states.

## Goals

Billing and plan logic should:

- Make commercial access predictable for platform owners, tenant admins,
  billing managers, support agents, and mobile users.
- Keep all billing authority in Admin/API.
- Return mobile-safe entitlement outcomes without exposing provider internals.
- Let trials, active subscriptions, expired subscriptions, billing suspension,
  and plan limits affect feature availability consistently.
- Keep feature flags inside the commercial ceiling defined by plan and
  subscription state.
- Give mobile users clear explanations when a feature is unavailable because of
  plan, expired subscription, quota, or billing suspension.
- Let authorized admins make documented manual decisions without bypassing
  tenant isolation, audit, or safety.
- Help support and billing teams explain billing-related access decisions.
- Fail closed when billing state is missing, stale, unknown, or unsafe for paid
  features.

Billing and plan logic should not:

- Implement or choose a billing provider in this document.
- Expose payment secrets, card data, provider event payloads, provider account
  identifiers, or payment failure internals to mobile.
- Let mobile decide whether a tenant has paid.
- Let tenant/user feature flags override a missing plan entitlement.
- Treat active subscription as permission to access every feature.
- Treat trial access as full production entitlement unless the plan explicitly
  says so.
- Allow cached billing state to unlock paid features while offline after expiry,
  suspension, revocation, or unknown state.
- Hide billing blocks behind vague errors.
- Allow manual admin overrides without reason, scope, audit, and rollback
  thinking.

## Ownership And Authority

| Concern | Admin/API authority | Mobile responsibility |
| --- | --- | --- |
| Plans | Define plan catalog, commercial tiers, included features, limits, support level, trial eligibility, and upgrade/downgrade rules. | Display only plan labels, included/blocked states, and next actions returned by API. |
| Subscription state | Resolve active, trial, expired, past-due, suspended, canceled, blocked, or unknown states. | Present current state and stale/offline warnings without inventing authority. |
| Entitlements | Decide what the tenant can use commercially after plan, subscription, limits, role, permission, tenant, feature flag, app-version, and safety gates. | Hide, disable, or explain feature entry points from resolved API outcomes. |
| Plan limits | Define and enforce ceilings such as seats, records, storage, attachments, notifications, reports, sync, support, or API usage. | Show remaining/blocked/warning states only when API allows and context is fresh enough. |
| Feature flags | Apply plan ceiling before tenant/user enables and return mobile-safe feature states. | Never use cached or local flags to bypass plan outcomes. |
| Manual controls | Allow authorized platform/billing/admin actions with confirmation, impact preview, reason, audit, and rollback guidance. | Show resulting state only after API confirmation. |
| Provider integration | Own provider selection, payment collection, invoice/payment events, reconciliation, and secrets outside this document. | Never interact directly with provider authority unless a future documented flow allows it. |
| Support and audit | Explain billing decisions through case-scoped support context and protected audit history. | Offer contact-admin/support paths without exposing payment internals. |

## Plan-Based Access

Plan-based access defines the commercial ceiling for a tenant.

Plan access principles:

- A plan should describe what product modules, limits, support level, reports,
  sync behavior, notification capacity, storage, seats, NativePHP capabilities,
  and admin controls are commercially included.
- Plan access should be resolved by Admin/API before mobile receives feature
  visibility or allowed actions.
- Plan access should combine with tenant state, user role, permission, feature
  flag, app version, maintenance state, remote config, and security policy.
- A plan should not grant access to a feature that is globally disabled,
  tenant-disabled, role-denied, permission-denied, app-version-blocked,
  maintenance-blocked, or security-blocked.
- A tenant/user feature flag can enable a feature only inside the plan ceiling.
- Plans should have clear labels and support-safe explanations, but mobile
  should not see raw provider metadata or internal pricing logic.
- Plan changes should show admin impact before saving: affected tenants, users,
  mobile screens, API behavior, feature flags, offline drafts, sync queues,
  reports, support, audit, and notifications.

Plan-based access should answer:

- Is the tenant commercially allowed to use this feature?
- Is the feature included, limited, trial-only, add-on-only, disabled, over
  limit, expired, suspended, or contact-admin?
- Which roles can see the plan state?
- Which mobile surfaces must change when the plan changes?
- Which queued/offline actions remain valid if the plan changes?

## Trial Behavior

Trial behavior lets a tenant evaluate the product under controlled limits.

Trial principles:

- Trial state is Admin/API authority.
- Trial access should be explicit: allowed modules, limits, duration, support
  level, reporting access, export behavior, sync behavior, notification
  capacity, storage, seats, and NativePHP capability access.
- Trial access should not silently equal paid access unless the plan says so.
- Trial start, extension, conversion, expiry warning, and end behavior should be
  controlled and auditable.
- Trial warnings should appear before hard blocks where role and policy allow.
- Mobile users should see role-appropriate trial labels and limited-feature
  messages without provider detail.
- Tenant admins or billing managers should see conversion or contact guidance
  where allowed.
- Support should see safe trial context so they can explain why a feature is
  unavailable.
- Offline trial behavior should be conservative: protected paid features should
  revalidate before sync if the trial may have expired.

Trial states may include:

- Trial active.
- Trial warning.
- Trial ending soon.
- Trial expired.
- Trial converted.
- Trial extension pending.
- Trial blocked by admin or abuse policy.

Mobile should not extend a trial, convert a tenant, or bypass trial limits.

## Active Subscription Behavior

An active subscription means the tenant is commercially in good standing for its
current plan.

Active subscription principles:

- Active subscription enables access only to features and limits included in the
  plan and allowed by role, permission, feature flag, app version, tenant state,
  maintenance, and security policy.
- Active does not mean unlimited.
- Active does not mean every feature is visible.
- Active does not mean every user in the tenant can use every included feature.
- Active subscription state should be returned to mobile as simple, mobile-safe
  operating context.
- Mobile should show normal workflows for features resolved as available.
- Mobile should show quota, upgrade/contact-admin, or permission messages when
  active tenants hit plan limits or role restrictions.
- Active subscription changes should be auditable when they affect access,
  limits, support level, billing visibility, or mobile behavior.

Mobile should treat active subscription context as stale when offline and should
avoid using old active state to override newer API blocks after reconnect.

## Expired Subscription Behavior

Expired subscription behavior protects the platform when commercial access has
ended.

Expired principles:

- Expiry should be resolved by Admin/API and reflected through tenant lifecycle,
  billing, feature flags, API responses, mobile shell state, support context,
  and audit.
- Expired subscriptions should block paid features unless policy grants grace,
  read-only access, data export, recovery, or contact-admin flows.
- Expired state should not delete user work by surprise.
- Expired state should clearly distinguish read-only access, limited access,
  blocked access, and recovery access.
- Mobile should show a clear explanation and next action rather than a generic
  permission error.
- Mobile should not queue paid-feature writes during expired state unless API
  explicitly allows recovery or grace behavior.
- Offline mobile should not assume an expired tenant can continue protected sync
  or paid writes.
- Expiry should notify tenant admins, billing managers, support, and mobile
  users only according to role and notification policy.

Mobile user messages should be concise and safe:

- The feature is not available on the current plan.
- The subscription needs attention.
- Contact your tenant admin or billing manager.
- Some actions are read-only until billing is resolved.
- Refresh when online to check current access.

Mobile should not mention raw provider failure codes, card details, invoices, or
internal billing investigation notes.

## Suspended Billing Behavior

Billing suspension is a stronger commercial block than ordinary expiry or
quota.

Suspended billing principles:

- Billing suspension should be Admin/API authority and may be caused by payment,
  fraud, abuse, chargeback, contract, compliance, manual admin decision, or
  provider reconciliation.
- Suspension should fail closed for paid features.
- Suspension may allow limited recovery actions, support contact, billing
  contact, legal notice, data export, or read-only access only if policy allows.
- Support and billing roles should see safe reason categories, not raw secrets
  or payment details.
- Mobile users should see simple blocked-state messaging and next action.
- Mobile should stop or hold offline queues that would create paid-feature
  writes.
- Existing local drafts should be labeled blocked or pending review until API
  confirms recovery.
- Suspension and restore should require audit history and, for manual actions,
  confirmation and impact preview.

Billing suspension must not become a cross-tenant or cross-role data exposure.
Users should see only the current tenant's mobile-safe blocked state.

## Plan Limits

Plan limits define the allowed usage envelope for a tenant.

Plan limits may apply to:

- Seats or active mobile users.
- Tenant managers or admins.
- Records/content count.
- Attachments, files, or storage.
- Notifications or push usage.
- Reports or exports.
- Offline draft count or retention.
- Sync frequency, background sync, or queue depth.
- API requests or rate categories.
- Support tier, case count, response level, or diagnostic depth.
- NativePHP capabilities such as scanner, location, microphone, camera, secure
  storage, or push-related modules when sold as plan features.
- Environments, tenants, integrations, custom branding, or remote config
  controls.

Limit principles:

- Limits should be defined and enforced server-side.
- Mobile may display remaining usage only when API returns safe values.
- Limits should have warning, approaching-limit, at-limit, over-limit, and
  blocked states where useful.
- Limits should explain which action is blocked and what the user can do next.
- Limits should avoid revealing tenant-wide sensitive usage data to users who
  should not see it.
- Limits should be scoped by tenant and plan, not mobile device.
- Offline actions that may exceed a limit should be queued only when policy
  allows and should be accepted or rejected by API when online.
- Limit breaches should be support-explainable and auditable when they affect
  feature access or user work.

Limit messaging should be helpful, not punitive. The user should know whether
they can retry, delete/archive, contact admin, wait for reset, upgrade, request
approval, or continue read-only.

## Plan Limits And Feature Flags

Plan limits and feature flags work together, but they are not the same thing.

Decision order:

1. Safety and authority gates: suspended user, suspended tenant, blocked app
   version, maintenance, security, emergency disablement.
2. Subscription state: trial, active, expired, suspended, canceled, unknown.
3. Plan entitlement: included, excluded, add-on, trial-only, limited, or
   unavailable.
4. Plan limit or quota: within limit, near limit, at limit, over limit, grace,
   or blocked.
5. Feature flag state: global, tenant, role, permission, user, app-version,
   device, cohort, rollout, or emergency outcome inside the plan ceiling.
6. Permission and role gate: user-specific access to the included feature.
7. Offline policy: read-only, draft-only, queueable, online-only, or blocked.

Rules:

- Plan exclusion wins over tenant/user feature enablement.
- Plan limit at/over blocked state wins over normal feature visibility.
- Feature flags may hide or disable a plan-included feature for rollout,
  version, tenant, support, safety, or maintenance reasons.
- Feature flags may reveal upgrade/contact-admin affordances for features not
  included in the plan, but cannot grant use of excluded features.
- User-level or tenant-level overrides may narrow access inside the plan
  ceiling, but should not widen commercial entitlement unless an authorized
  manual billing override explicitly creates that outcome.
- Support and reports should be able to explain whether a feature is missing
  because of plan, limit, flag, role, permission, app version, tenant state,
  maintenance, or emergency disablement.

Mobile should receive a resolved state, not raw plan and flag internals.

## Mobile Unavailable-Feature Behavior

When a mobile feature is unavailable due to plan, mobile should show a clear,
safe outcome.

Mobile states:

- Hidden because the feature is irrelevant to this plan and explanation would
  create noise.
- Disabled with plan label because the user can understand the feature exists
  but is not included.
- Blocked because the tenant is expired, suspended, or over limit.
- Read-only because the tenant may view existing data but cannot create or
  modify paid-feature data.
- Trial-limited because the tenant is evaluating the feature under limits.
- Quota warning because usage is near the plan ceiling.
- Contact admin because the current user cannot resolve billing.
- Contact billing manager because the user has billing responsibility.
- Contact support because the state may be a mistake or requires assistance.
- Upgrade/contact sales where the product supports that role-safe action.
- Refresh required because cached/offline billing state is stale.

Mobile copy should communicate:

- What is unavailable.
- Why at a product level.
- Whether the user can do anything.
- Who can help.
- Whether the app is offline or showing last-known state.
- Whether local drafts or pending actions are preserved.

Mobile copy should not expose:

- Payment provider names unless product policy intentionally shows them.
- Payment method details.
- Failed charge codes.
- Invoice internals.
- Admin-only notes.
- Cross-tenant billing state.
- Raw feature flag keys or internal plan IDs.

## Admin Manual Controls

Authorized admins may need manual controls for real business operations.

Manual control examples:

- Assign or change a plan.
- Start, extend, end, or convert a trial.
- Mark a subscription state for internal handling.
- Apply temporary grace access.
- Suspend or restore billing access.
- Add or remove an add-on entitlement.
- Adjust a plan limit.
- Override a limit temporarily.
- Reset a usage period where policy allows.
- Block or unblock a tenant for billing/commercial reasons.
- Change support tier.
- Add internal notes or support context.
- Trigger billing-related notifications.
- Preview mobile impact before a billing change.

Manual-control principles:

- Only authorized platform, super admin, billing manager, or delegated tenant
  roles may make billing changes according to policy.
- Manual controls should show impact before saving: affected tenant, users,
  features, limits, mobile screens, API behavior, offline queues, reports,
  support, notifications, and audit.
- Dangerous changes should require confirmation and reason.
- Manual changes should be tenant-scoped unless platform scope is explicitly
  required.
- Manual changes should never expose payment secrets.
- Manual overrides should have clear duration, owner, reason, and rollback path
  when temporary.
- Manual changes should be auditable and support-explainable.
- Tenant admins should only control billing settings delegated to their tenant
  role.
- Billing managers should see commercial context needed for their work without
  becoming broad platform administrators.
- Support agents should see safe billing summaries, not authority to change
  billing unless explicitly delegated.

Manual controls are not provider implementation. They are product authority
decisions that may later connect to a provider through a separate documented
integration.

## Offline Billing Behavior

Billing authority requires fresh API confirmation.

Offline principles:

- Mobile may display last-known billing and plan state with freshness metadata.
- Mobile should label billing state as cached when offline.
- Mobile should not unlock paid features from stale cached billing state when
  the state is expired, suspended, unknown, or near a time-sensitive boundary.
- Mobile may allow safe read-only access where policy allows.
- Mobile may preserve local drafts, but API acceptance after reconnect depends
  on current subscription, plan, limit, feature flag, permission, tenant, app
  version, and sync policy.
- Mobile should hold, reject locally, or require review for queued actions that
  may consume paid limits.
- When online returns, mobile should refresh billing state before replaying
  paid-feature writes.
- If a queued action is rejected because of plan or subscription state, mobile
  should preserve user intent where possible and explain the reason.

Offline billing should be clear enough that users understand whether they are
working locally, waiting for billing verification, blocked, or read-only.

## Notifications, Support, And Reports

Billing decisions affect operations beyond access control.

Notification principles:

- Notify billing managers or tenant admins when trial expiry, subscription
  expiry, payment attention, plan-limit warnings, suspension, restoration, or
  manual changes require action.
- Notify mobile users only when the billing outcome affects their work and the
  message is role-safe.
- Keep push content generic when billing state is sensitive.
- Deep links must recheck role, tenant, plan, feature, app version, and support
  context.

Support principles:

- Support should see safe billing status, plan label, reason category, feature
  impact, support tier, and recommended next action.
- Support should not see payment secrets, full provider event payloads, card
  data, private invoices, or cross-tenant billing details.
- Support should know whether the issue is plan, quota, feature flag,
  permission, tenant state, app version, or sync related.

Report principles:

- Platform reports may summarize plan adoption, trial conversion, churn risk,
  usage limits, feature demand, support load, and blocked-feature patterns.
- Tenant reports should show only tenant-authorized billing and usage context.
- Mobile diagnostics should expose only safe summaries needed for user support.

## Privacy And Security

Billing data is sensitive.

Privacy and security principles:

- Store and expose the minimum billing context needed for product decisions.
- Keep provider secrets and payment method details out of mobile, support views,
  local cache, logs, diagnostics, and exports unless a separate documented,
  authorized process allows a redacted view.
- Separate commercial entitlement from payment-provider mechanics.
- Protect billing audit and manual changes from casual browsing.
- Use least privilege for billing managers, support agents, tenant admins, and
  platform operators.
- Redact billing context in diagnostics by default.
- Avoid using billing data as a covert way to identify other tenants, users, or
  payment relationships.
- Treat billing state from external systems as untrusted until Admin/API
  validates and reconciles it.

## Risks

Key risks:

- Mobile uses stale billing state to unlock paid features.
- Feature flags bypass plan limits.
- Manual admin overrides silently grant commercial access.
- Support sees too much billing detail.
- Mobile users see confusing generic errors instead of plan-related guidance.
- Trial users lose work unexpectedly at expiry.
- Offline queues consume limits after the tenant has expired or been suspended.
- Plan changes break mobile navigation without clear blocked states.
- Provider-specific details leak into product docs or mobile UI before provider
  implementation is designed.
- Billing managers gain broader tenant or platform access than their job
  requires.

Risk controls:

- Admin/API entitlement resolution.
- Plan ceiling before feature flags.
- Mobile-safe blocked states.
- Freshness labels for cached billing state.
- Audit history for billing changes and denials.
- Impact preview for manual controls.
- Support-safe reason categories.
- Least-privilege billing roles.
- Documentation before provider integration.

## Acceptance Questions Before Implementation

Before implementing billing behavior, documentation should answer:

- Which plans exist at the product level?
- Which features and limits belong to each plan?
- Which trial limits differ from active subscription limits?
- Which state wins when plan, feature flag, permission, tenant lifecycle, and
  app-version policy conflict?
- Which features become hidden, disabled, read-only, blocked, or upgrade/contact
  states?
- Which mobile actions can continue offline under each subscription state?
- Which queued actions should be held or rejected after billing changes?
- Which admins can manually change plan, trial, limits, grace, suspension, and
  support tier?
- Which manual controls need confirmation, reason, impact preview, and audit?
- Which billing events notify tenant admins, billing managers, mobile users,
  support, or platform operators?
- Which billing details are support-visible and which are never visible?
- What is the rollback path for a mistaken manual billing change?

## Success Standard

Billing and plan logic is ready for implementation only when plan-based access,
trial behavior, active subscription behavior, expired subscription behavior,
suspended billing behavior, plan limits, feature flag interaction, mobile
blocked-state messaging, manual admin controls, privacy, support, notifications,
audit, offline behavior, and provider boundaries are documented before code.
The final product should make commercial access clear without letting billing
state leak secrets, bypass authority, or surprise mobile users.
