# Notifications Logic

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

This document defines notification logic for the Mobile Lara SaaS system. It
explains admin-created notifications, system notifications, security
notifications, reminder notifications, push notification principles, in-app
inbox principles, read/unread behavior, deep-link behavior, notification
preferences, offline notification behavior, and tenant and permission
boundaries. It is documentation only and does not define database structure,
database fields, migrations, indexes, seeders, routes, controllers, Livewire
components, Filament resources, NativePHP plugins, policies, gates, middleware,
jobs, services, local storage schemas, API endpoints, UI components, CSS,
JavaScript, push-provider configuration, queues, or application logic.

Use this document with [Product Principles](product-principles.md), [Mobile UX
Principles](mobile-ux-principles.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md),
[Mobile Dashboard Logic](mobile-dashboard-logic.md), [Mobile Settings
Logic](mobile-settings-logic.md), [Mobile Permission Logic](mobile-permission-logic.md),
[Mobile App Shell Logic](mobile-app-shell-logic.md), [Mobile App Lock
Principles](mobile-app-lock-principles.md), [Two-System Boundary
Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Admin Control
Center Logic](admin-control-center-logic.md), [Role And Permission
Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version
Control Logic](mobile-version-control-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Data Privacy Principles](data-privacy-principles.md),
[Audit Logic](audit-logic.md), [Tenant Lifecycle Logic](tenant-lifecycle-logic.md),
[Tenant Admin Logic](tenant-admin-logic.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Offline-First Principles](offline-first-principles.md),
[Offline UX Logic](offline-ux-logic.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md),
[Records/Content Module Logic](records-content-module-logic.md), [Forms And
Drafts Logic](forms-drafts-logic.md), [Support System
Logic](support-system-logic.md), and [API v1 Notifications
Contract](../contracts/api/v1-notifications.md): notifications are
tenant-scoped communication events, and Admin/API remains authoritative for
targeting, eligibility, delivery policy, unread truth, preference acceptance,
deep-link safety, audit, and tenant boundaries.

Support System Logic is defined in `support-system-logic.md`:
support replies, diagnostic requests, attachment outcomes, case status changes,
support-agent visibility, audit, and offline support draft reminders must stay
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

## Notifications Statement

Notifications help the right user notice the right event at the right time
without leaking tenant data or turning mobile delivery into business authority.

A notification is a controlled communication event. It may appear as an in-app
inbox item, a push alert, an email or other channel in the future, or a
dashboard count. A notification can point users toward records, tasks, support
updates, security actions, billing notices, sync problems, reminders, or
announcements, but it must never bypass the normal API, tenant, permission,
feature, and privacy rules of the destination.

Product rule: Admin/API owns notification targeting, category eligibility,
delivery policy, unread truth, deep-link validity, audit meaning, and tenant
scope. Mobile owns device permission education, local display, inbox
presentation, safe fallback, offline cache labels, and user feedback.

## Goals

Notification logic should:

- Help mobile users notice important work, reminders, security events, support
  replies, system state, and tenant announcements.
- Give admins a controlled way to communicate with tenant users.
- Keep all targeting tenant-scoped and permission-aware.
- Make push optional where policy allows and always provide an in-app fallback
  when the user can still receive the message.
- Let users understand read/unread state without trusting stale local counts.
- Deep-link users to safe destinations only after current API authority is
  checked.
- Respect user preferences, tenant policy, quiet hours, plan limits, app
  version, maintenance state, and device permission state.
- Avoid notification fatigue by sending fewer, clearer messages.
- Protect sensitive notification content from lock-screen, diagnostics,
  support, and cross-tenant exposure.

Notification logic should not:

- Reveal content the user cannot otherwise access.
- Send cross-tenant or cross-role messages by mistake.
- Treat push delivery as proof that the user saw or accepted anything.
- Treat a push token as user identity or authorization.
- Depend on mobile-local preferences as final server policy.
- Deep-link directly into protected content without API confirmation.
- Store sensitive notification bodies locally when policy forbids it.
- Use notifications to hide broken sync, billing, support, or security flows.

## Ownership And Authority

| Concern | Admin/API authority | Mobile responsibility |
| --- | --- | --- |
| Targeting | Decide recipients by tenant, role, permission, user state, plan, feature flag, category, and business event. | Display only notifications returned or confirmed by API for the active user and tenant. |
| Category eligibility | Decide which notification categories exist and who can receive or manage them. | Show safe category labels and preference controls only when API permits. |
| Push delivery | Decide whether push is enabled, required, suppressed, quieted, retried, or blocked. | Explain push permission, register/recover device capability when allowed, and show in-app fallback. |
| In-app inbox | Own unread truth, list eligibility, visibility, retention, and server state. | Present cached or fresh inbox items with clear online/offline state. |
| Read/unread | Own server-confirmed read/unread state and unread count. | Allow local read feedback, then sync and reconcile with API truth. |
| Deep links | Validate destination, tenant, permission, app version, feature state, and fallback behavior. | Route safely only after API/context confirmation or show a blocked/unavailable state. |
| Preferences | Define preference availability, quiet hours, category controls, policy limits, and role restrictions. | Let users view or request changes while distinguishing local device settings from server policy. |
| Privacy | Define sensitive content rules, lock-screen behavior, support visibility, audit, retention, export, and deletion. | Avoid exposing raw tokens, private bodies, sensitive previews, or cross-tenant cached content. |

## Notification Types

Notification type should describe why the message exists and how urgent it is.

Core types:

- Admin-created notifications.
- System notifications.
- Security notifications.
- Reminder notifications.
- Support notifications.
- Sync and conflict notifications.
- Billing or subscription notifications.
- Tenant lifecycle notifications.
- Product or maintenance announcements.

Every type should define:

- Purpose.
- Allowed sender or trigger.
- Eligible recipients.
- Tenant scope.
- Permission requirements.
- Feature flag and plan requirements.
- Priority.
- Preferred channels.
- Whether push is allowed.
- Whether in-app inbox is required.
- Whether read/unread matters.
- Whether deep-linking is allowed.
- Offline behavior.
- Audit or support visibility.
- Retention and privacy sensitivity.

Types should be stable enough that users can control preferences and admins can
understand impact before sending.

## Admin-Created Notifications

Admin-created notifications are intentional messages created by authorized
admins.

Examples may include:

- Tenant announcements.
- Maintenance notices.
- Policy updates.
- Work instructions.
- Support notices.
- Release or app-version guidance.
- Billing or plan notices where the admin role is allowed.
- Emergency messages to a tenant or segment.

Admin-created notification principles:

- Only authorized admins can create or send them.
- Tenant admins can target only their tenant unless platform policy explicitly
  allows broader platform roles.
- Targeting should be previewed before sending.
- Sending should show impact: recipients, tenant scope, channels, push
  eligibility, quiet-hour behavior, deep-link destination, and sensitive
  content warnings.
- Broad, urgent, cross-role, security-sensitive, billing-sensitive, or
  destructive-impact messages should require confirmation.
- Messages should be auditable: who created it, who approved it if required,
  what was targeted, when it was sent, and what policy applied.
- Admins should not use notifications to disclose data to users who could not
  reach that data through normal API access.

Admin-created notifications should support drafts or scheduling only as product
behavior when documented separately. A scheduled admin notification should still
be re-evaluated against current tenant/user/permission/policy state at send
time.

## System Notifications

System notifications are generated by product events rather than manually
written by an admin at send time.

Examples may include:

- Record assigned.
- Record status changed.
- Sync failed.
- Conflict requires attention.
- Support ticket updated.
- Subscription state changed.
- Tenant maintenance scheduled.
- App update recommended.
- Feature enabled or disabled where user action is needed.

System notification principles:

- System events should map to clear notification categories.
- A system notification should explain what changed and what the user can do.
- The system should avoid duplicate messages for the same event.
- Priority should match user impact.
- Channels should match urgency and preference rules.
- In-app notification should remain available when push is disabled but the
  category is still eligible.
- Delivery should happen only after the triggering server state is trustworthy.
- If the related record, ticket, tenant, or feature becomes unavailable, the
  notification should degrade safely instead of deep-linking into broken state.

System notifications should not become a substitute for durable status screens.
Users should still be able to find the underlying state through the dashboard,
records, settings, sync views, or support flows.

## Security Notifications

Security notifications protect accounts, sessions, tenants, and devices.

Examples may include:

- New login or device registration.
- Password changed.
- MFA changed.
- Session revoked.
- App lock or biometric policy changed.
- Suspicious access attempt.
- Permission or role changed where user awareness is appropriate.
- Push token registered or revoked where policy requires user awareness.

Security notification principles:

- Security notifications should be high-trust and low-noise.
- They should avoid exposing secrets, raw tokens, device identifiers, precise
  location, or sensitive internals.
- They should provide a safe next action, such as review sessions, contact
  support, re-authenticate, or secure account.
- They should not depend only on push. In-app or other durable channels should
  be available where policy requires.
- Some security notifications may ignore normal marketing-like preferences but
  must still respect legal, tenant, and platform policy.
- Deep links from security notifications should require current authentication,
  app lock where required, and API authority.
- Security notification events should be auditable and protected from support
  overexposure.

Security notifications should fail closed. If the app is offline, locked,
revoked, or blocked by version policy, mobile should show only safe summaries
until API confirmation is possible.

## Reminder Notifications

Reminder notifications help users return to time-sensitive or incomplete work.

Examples may include:

- Upcoming task.
- Due record.
- Pending draft.
- Pending sync retry.
- Unresolved conflict.
- Support follow-up.
- Scheduled tenant event.
- Expiring invitation.

Reminder principles:

- A reminder should correspond to useful user action.
- Reminder timing should be controlled by Admin/API policy, remote config, user
  preference, or tenant workflow rules.
- Reminders should respect quiet hours unless policy defines an urgent
  exception.
- Reminders should be deduplicated and capped to avoid fatigue.
- Reminders should stop when the underlying condition no longer applies.
- Reminders should not reveal private work on a lock screen when sensitive
  content policy forbids it.
- Offline mobile reminders may use safe cached state, but must be labeled or
  refreshed before claiming current server truth.

Reminder notifications should help, not nag. The user should understand why the
reminder arrived and how to stop or complete the underlying work.

## Push Notification Principles

Push notifications are a delivery channel, not the notification authority.

Push principles:

- Push requires native device permission and Admin/API delivery eligibility.
- Push should be requested only after feature eligibility and user value are
  clear.
- Push token registration should happen only through API-approved context.
- Raw push tokens should never be shown in UI, diagnostics, support views, or
  logs unless a tightly controlled support policy explicitly allows a safe
  summary.
- Push payloads should be minimal and privacy-safe.
- Sensitive content should be hidden or summarized when lock-screen exposure is
  risky.
- Push delivery should not be treated as read, opened, accepted, or completed.
- Push open should route through safe deep-link validation.
- Push should respect quiet hours, category preferences, tenant policy, plan
  limits, app version, maintenance mode, and revoked sessions.
- Push failure should not lose the notification if in-app inbox delivery is
  required.

Push should answer: "Should the device display attention now?" The API still
answers: "Is this user allowed to know and act on this?"

## In-App Inbox Principles

The in-app inbox is the durable notification surface inside the mobile client.

Inbox principles:

- The inbox should show notifications for the active user and tenant only.
- The inbox should be filtered by current permissions and feature flags.
- The inbox should be available as the fallback when push is denied or disabled.
- The inbox should support unread counts, read state, safe filtering, and
  clear empty states.
- The inbox should label stale cached state when offline.
- The inbox should not expose notifications from another tenant after tenant
  switching.
- The inbox should not reveal deleted, inaccessible, or hidden destination
  content through notification snippets.
- The inbox should protect sensitive messages behind app lock where required.
- The inbox should use predictable ordering and stable states.

Inbox states:

- Loading.
- Empty.
- Unread available.
- All read.
- Offline cached.
- Syncing read state.
- Read-state failed.
- Feature disabled.
- Permission denied.
- Tenant unavailable.
- Maintenance or forced update.

The inbox should be simple. It is not a full admin broadcast console, audit
viewer, or report builder.

## Read/Unread Behavior

Read/unread state helps users track attention, but API truth is final.

Read/unread principles:

- Unread count should be server-confirmed when online.
- Mobile may optimistically mark an item read locally, but it must reconcile
  with API state.
- Offline read changes may be queued where policy allows.
- Local read state must not change server truth until synced.
- Marking one tenant's notification read must not affect another tenant.
- Mark-all-read should be permission-aware and tenant-scoped.
- A push open does not necessarily mark read unless policy says that opening
  the destination counts as read and API accepts it.
- Admin-created urgent/security notifications may require stricter read or
  acknowledgement logic if separately documented.
- Unread counts should not include notifications the user can no longer view.

Read/unread feedback should say whether the action is local, pending, synced,
failed, or blocked. Users should not have to guess why a count changed and then
changed back after reconnecting.

## Deep-Link Behavior

Deep links help users move from a notification to the relevant screen.

Deep-link principles:

- A deep link is navigation intent, not authorization.
- Deep links must be tenant-scoped.
- Deep links must validate current user, tenant, permission, feature flag, app
  version, maintenance state, subscription state, and destination availability.
- Deep links should handle missing, archived, deleted, disabled, blocked, or
  conflict states safely.
- Cross-tenant deep links should require explicit API-confirmed tenant switch
  behavior before navigation.
- Deep links should not include secrets, raw tokens, private payloads, or
  sensitive identifiers that are not safe to expose.
- Deep links should route through the app shell so locked, offline, forced
  update, maintenance, permission-blocked, and feature-disabled states are
  honored.
- Deep links should provide a fallback screen when the destination is no longer
  available.

Deep-link outcomes:

- Open destination.
- Ask to switch tenant.
- Require login or unlock.
- Show permission denied.
- Show feature disabled.
- Show item unavailable.
- Show offline limited state.
- Show forced update or maintenance.
- Show support guidance where useful.

## Notification Preferences

Notification preferences let users control allowed communication without
overriding mandatory platform or tenant policy.

Preference principles:

- Preferences should be scoped by user and tenant where tenant-specific
  communication differs.
- Preferences should separate channels from categories.
- Device notification permission is local state; API preference is server
  policy.
- Users may choose category/channel settings only when Admin/API allows it.
- Some categories may be mandatory, especially security, account, critical
  tenant, or legal/operational notices.
- Quiet hours should be clear and should explain exceptions.
- Preference saves require online API confirmation.
- Offline preference screens may show cached preferences and native permission
  state, but must not claim changes are saved server-side.
- Preference changes should not retroactively expose old notifications that are
  no longer visible under current permission rules.

Preference states:

- Enabled.
- Disabled by user.
- Disabled by admin.
- Required by policy.
- Unavailable on this plan.
- Blocked by device permission.
- Cached offline.
- Save pending.
- Save failed.

Preferences should be easy to understand. Users should not need to learn
transport details to know whether they will receive important messages.

## Offline Notification Behavior

Offline notification behavior should preserve useful context without pretending
to have current delivery truth.

Offline mobile may:

- Show cached inbox items for the active tenant.
- Show cached unread count with a stale label.
- Let the user open cached notification details when safe.
- Queue read/unread changes where policy allows.
- Preserve push-open navigation intent until API confirmation is possible.
- Show local device permission status.
- Show cached preferences without allowing server-confirmed changes.

Offline mobile must not:

- Claim unread count is current.
- Register, refresh, or revoke push tokens.
- Save notification preferences as server-confirmed.
- Deep-link into protected current data without API confirmation.
- Reveal notifications from a previous tenant after tenant switch.
- Send push delivery/open/read acknowledgements to server.
- Mark admin or security acknowledgements as accepted.
- Trigger notification-based workflow actions that require API authority.

Offline notification UX should answer:

- Is this notification cached or current?
- Can I act on it offline?
- What will sync later?
- What needs the server?
- Is my push/device permission healthy?

## Tenant And Permission Boundaries

Notifications must obey tenant and permission boundaries before every display,
count, delivery, read, deep-link, preference, and audit decision.

Boundary principles:

- Every notification belongs to a tenant, platform context, or explicitly
  documented account/security scope.
- Tenant-scoped notifications never appear in another tenant context.
- Platform-level notifications must still respect user role, account state,
  privacy, and support visibility rules.
- Suspended users fail closed.
- Suspended, archived, billing-blocked, or deleted tenants should show only
  safe tenant-state notifications allowed by policy.
- Invited users should receive only invitation-safe notifications until access
  is accepted and API-confirmed.
- Guest/pre-login users should not see tenant inbox content.
- Support agents should see only notification context needed for support and
  only inside allowed tenant scope.
- Billing managers should see billing notifications only where their role
  permits.
- Mobile users should see work notifications only for work they can view or act
  on.

Counts, snippets, push titles, notification categories, deep-link labels, and
read state can all leak data. They must be filtered as carefully as full
notification bodies.

## Admin Control And Safety

Admins should understand notification impact before changing policy or sending
messages.

Admin controls may include:

- Notification categories.
- Channel availability.
- Push enablement.
- In-app inbox enablement.
- Tenant targeting.
- Role or user targeting.
- Quiet hours.
- Priority and urgency.
- Reminder timing.
- Message templates.
- Deep-link destinations.
- Suppression rules.
- Retry or fallback policy.
- Read or acknowledgement requirements.
- Preference editability.
- Retention and deletion policy.

Admin safety principles:

- Show target audience before sending.
- Show channel impact before saving policy changes.
- Warn about sensitive lock-screen content.
- Warn about cross-tenant or broad targeting.
- Require confirmation for urgent, broad, security, billing, tenant-lifecycle,
  or emergency notices.
- Audit dangerous changes and sends.
- Provide rollback or stop-sending thinking for scheduled or repeated
  notifications.
- Keep tenant-specific changes isolated.

Admin-created messages should be written for the target user's context. Mobile
users should not receive admin jargon, raw policy names, or internal rollout
metadata.

## Privacy And Security

Notifications are privacy-sensitive because they can appear outside the app.

Privacy principles:

- Push payloads should use the minimum useful content.
- Sensitive content should be hidden on lock screen when policy requires.
- In-app details may reveal more only after login/unlock/API confirmation.
- Raw push tokens, provider payloads, and device identifiers should not be
  exposed in normal UI or support views.
- Diagnostics should avoid raw notification bodies and tokens.
- Notification content should follow export, deletion, retention, and support
  access policies.
- Security notifications should avoid creating a new attack path through links
  or overly specific details.
- Deep links should not carry secrets.
- Notification preferences should not reveal other users, teams, tenants, or
  hidden categories.
- Notification bodies should not include data that would violate tenant or role
  boundaries if displayed on a locked device.

Security-sensitive notifications may need stricter rules than normal messages:
no push preview, require app unlock, require re-authentication, or show a safe
summary until API confirms current access.

## Feature Flags And Remote Config

Notifications should be remotely controllable.

Feature flags may control:

- Notification module availability.
- In-app inbox availability.
- Push registration availability.
- Push delivery eligibility.
- Admin-created notification availability.
- Reminder notification availability.
- Deep-link actions.
- Read/unread actions.
- Preference editing.
- Category visibility.
- Tenant-specific rollout.
- User or cohort rollout.

Remote config may control safe behavior such as:

- User-facing category labels.
- Pre-permission prompt copy.
- Quiet-hour wording.
- Empty-state text.
- Offline stale-state text.
- Lock-screen sensitivity labels.
- Deep-link fallback copy.
- Reminder timing explanations.
- Support guidance.

Remote config must not grant targeting, tenant, permission, security, billing,
or delivery authority by itself. Missing or invalid config should fall back to
safe defaults or disable risky notification behavior until API confirmation is
available.

## Risks

Notification risks to record before implementation:

- Cross-tenant leaks through push titles, inbox counts, snippets, deep links,
  diagnostics, or support views.
- Push notification shown on a lock screen with sensitive content.
- Notification delivered after permission, tenant, or record state changed.
- Read/unread counts drifting between local cache and API truth.
- Deep links opening unauthorized, deleted, archived, or wrong-tenant content.
- Push token treated as identity or authorization.
- Users overwhelmed by duplicate reminders.
- Admin sends broad or urgent messages without understanding impact.
- Offline read or preference changes presented as server-confirmed.
- Security messages becoming too noisy to trust.
- Mandatory notifications ignoring legal, tenant, or privacy constraints.

## Acceptance Questions

Before implementing notification behavior, the team should answer:

- What type of notification is this?
- Who is allowed to trigger it?
- Which tenant, role, user, plan, permission, feature flag, and app-version
  rules control it?
- Is push allowed, required, optional, or disabled?
- Is in-app inbox required?
- Can the user control this category or channel?
- Does quiet-hours policy apply?
- What content is safe for push preview?
- What is the deep-link destination and fallback?
- What happens when the device is offline?
- What happens after tenant switch, logout, revocation, suspension,
  maintenance, or forced update?
- What read/unread behavior is allowed?
- What support and audit visibility is required?
- What must be retained, exported, deleted, hidden, or redacted?

## Success Standard

Notifications are successful when the right users receive useful, timely,
tenant-safe messages; push and in-app inbox work together without confusing
delivery with authority; read/unread and deep links reconcile with API truth;
preferences respect both user choice and admin policy; offline states are
honest; and no notification leaks data across tenant, permission, privacy, or
security boundaries.
