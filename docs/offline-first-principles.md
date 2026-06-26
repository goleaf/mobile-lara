# Offline-First Principles

Updated: 2026-06-26

This document defines offline-first principles for the Mobile Lara SaaS system.
It explains what the mobile client can do offline, what must wait for online
API access, what should be cached, what should never be cached, how the app
communicates offline state, how offline actions are queued logically, how users
understand pending changes, and how admins control offline limits. It is
documentation only and does not define database structure, database fields,
migrations, seeders, routes, controllers, Livewire components, Filament
resources, NativePHP plugins, policies, gates, middleware, jobs, services,
local storage schemas, queue tables, API endpoints, sync workers, retry jobs,
or application logic.

Use this document with [Product Principles](product-principles.md), [Two-System
Boundary Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Mobile Client Responsibilities](mobile-client-responsibilities.md), [NativePHP
Local Storage](nativephp-local-storage.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md),
[Offline UX Logic](offline-ux-logic.md), [Multi-Tenant Mobile Logic](multi-tenant-mobile-logic.md),
[Authentication Principles](authentication-principles.md), [Mobile App Lock Principles](mobile-app-lock-principles.md),
[Mobile App Shell Logic](mobile-app-shell-logic.md), [Mobile Dashboard
Logic](mobile-dashboard-logic.md), [Mobile Settings Logic](mobile-settings-logic.md),
[Role And Permission Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version
Control Logic](mobile-version-control-logic.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Admin Safety Principles](admin-safety-principles.md),
[Audit Logic](audit-logic.md), [Data Privacy Principles](data-privacy-principles.md),
[Conflict Resolution Logic](conflict-resolution-logic.md), and [API v1 Sync
Contract](../contracts/api/v1-sync.md): offline-first behavior keeps mobile
users productive with cache, drafts, and queued intents while sync lifecycle
behavior defines bootstrap, pull, push, retry, conflict, acknowledgement,
status, manual sync, background sync, and health monitoring. Conflict
resolution defines auto-resolution, user choice, admin/support review, audit,
and data-loss prevention. Offline UX Logic defines calm user-facing banners,
disabled online-only actions, local draft messages, pending indicators, retry,
sync success/failure feedback, saved-local versus synced status, and
connection-loss recovery. Admin/API remains authoritative for access,
validation, permission, billing, feature, tenant, sync, conflict, audit, and
security decisions.

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

## Offline-First Statement

Offline-first means useful offline, not trusted offline.

The NativePHP mobile client should keep users oriented and productive when the
network is unavailable, slow, captive, or unreliable. It may show safe cached
data, preserve drafts, collect local input, queue allowed intents, show sync
state, and prepare support context. It must not turn local state into server
truth, bypass the API, grant tenant access, grant permissions, override feature
flags, bypass billing, ignore app-version policy, create audit truth, or accept
conflict decisions locally.

Product rule: offline work becomes trusted only after the API accepts it in the
current user, tenant, device, feature, permission, subscription, app-version,
maintenance, and sync-policy context.

## Authority Split

Offline behavior spans both systems, but authority remains in Admin/API.

| Area | Mobile client may own offline | Admin/API still owns |
| --- | --- | --- |
| Cached reads | Safe last-known data, freshness labels, offline navigation, and read-only local display. | Canonical data, tenant access, visibility rules, retention, and whether offline read is allowed. |
| Drafts | Local unfinished work, edits, recovery, discard, and submit intent. | Validation, authorization, final persistence, conflict rules, and accepted server state. |
| Queued actions | Local pending intents, retry metadata, pending/syncing/failed/conflict display. | Replay windows, idempotency rules, acceptance, rejection, conflict decisions, and audit. |
| Native capture | Local photos, files, scans, audio, or metadata when the feature allows it. | Feature eligibility, upload acceptance, storage policy, privacy, malware checks, and audit acceptance. |
| Offline state | Banners, disabled actions, last-synced labels, pending counts, retry guidance, and support prompts. | Error semantics, maintenance policy, force update policy, support escalation, and sync limits. |
| Admin limits | No local authority; mobile receives limits and explains them. | Offline enablement, cache TTL, queue limits, retry policy, tenant/role/feature gates, and emergency shutdown. |

## What Mobile Can Do Offline

Offline mobile behavior should be useful, bounded, and honest.

The mobile client can do offline:

- Show the last confirmed current tenant and user context with a clear
  last-known label.
- Display cached records, dashboard summaries, settings summaries,
  notifications, announcements, support references, and recent activity when
  policy allows offline read.
- Preserve local drafts for unfinished user work.
- Edit local drafts before submission.
- Queue allowed offline actions as pending intents.
- Capture local media or scanned data for a feature that explicitly allows
  offline capture.
- Show sync status, pending counts, stale-data warnings, retry state, conflict
  state, and failed state.
- Show cached feature visibility only as last-known presentation.
- Open local settings that do not require server authority.
- Prepare support drafts and safe diagnostic summaries for later submission.
- Allow app lock, local unlock, and secure local session presentation where
  policy allows already-authenticated offline behavior.

Offline capability should be feature-specific. A mobile app can be offline-capable
without every feature being offline-capable.

## What Must Wait For Online API Access

Some actions must wait for online API access because they change authority,
security, billing, tenant state, or canonical records.

The mobile client must wait online for:

- First login, registration, invitation acceptance, tenant activation, and
  session refresh that requires server confirmation.
- Tenant selection or tenant switching that changes protected tenant context.
- Any server-trusted read that is not already safely cached.
- Any final write that creates, updates, deletes, submits, approves, exports,
  pays, bills, grants access, revokes access, changes roles, changes
  permissions, changes features, changes config, changes support state, changes
  audit state, or changes tenant lifecycle.
- Report exports, billing actions, support case submission, support escalation,
  notification registration, push-token updates, app-version checks, and
  maintenance-state decisions.
- Conflict resolution when the server must decide canonical state.
- Replay of queued intents.
- Upload of media or files that must be validated, scanned, stored, transformed,
  or associated with server records.
- Any action denied by stale permissions, missing feature flag, billing block,
  force update, maintenance, tenant suspension, user suspension, or offline
  policy.

Offline mode should offer drafts, pending intents, or explanation where useful,
but it should not pretend these actions succeeded.

## What Should Be Cached

Cache exists for speed, resilience, and context.

The mobile client may cache:

- Bootstrap snapshots returned by API.
- Current user display context and safe profile summary.
- Current tenant label, tenant choices, tenant state summary, and last-confirmed
  tenant context.
- Resolved permissions and feature flags for presentation with freshness
  metadata.
- Resolved remote config with version, scope, and freshness metadata.
- App-version and maintenance policy summary needed for safe presentation.
- Cached records and resource summaries that the current user and tenant are
  allowed to view offline.
- Dashboard summaries, recent activity, announcement summaries, notification
  inbox summaries, and support references when policy allows.
- Local drafts and draft metadata.
- Queued action intents and retry metadata.
- Sync cursors, last sync time, pending count, retry count, conflict count, and
  failure reasons.
- Safe diagnostics such as app version, platform, network category, last sync
  time, feature state, and error category.

Cached items should carry enough metadata to answer: tenant, user, source,
freshness, scope, allowed offline behavior, last refresh, and whether protected
work requires online revalidation.

## What Should Never Be Cached

Offline-first must not become secret sprawl.

The mobile client should never cache in ordinary local storage:

- Access tokens, refresh tokens, API secrets, private keys, signing keys,
  provider credentials, payment credentials, or passwords.
- Raw billing credentials, card details, bank details, or payment-provider
  payloads.
- Server-trusted audit truth.
- Tenant authority, permission authority, billing authority, feature authority,
  remote-config authority, app-version authority, support authority, report
  authority, or sync acceptance authority.
- Cross-tenant data in a shared, unlabeled, or ambiguous cache.
- Unredacted diagnostics, raw logs, stack traces, private payloads, secrets, or
  support notes that would leak another user or tenant.
- Sensitive documents, media, or personally sensitive data unless a specific
  feature documents encryption, retention, purge, access, and support rules.
- Deleted, revoked, suspended, archived, billing-blocked, or no-longer-allowed
  tenant data as usable current data.
- Anything the API must be able to revoke immediately as a condition of safety.

Secrets belong in approved secure storage where available. Cached business data
belongs behind app lock, tenant scope, freshness labels, privacy rules, and
server revalidation.

## Communicating Offline State

Offline state should be visible without overwhelming the user.

Communication principles:

- Show a clear offline indicator when network state affects the current screen.
- Distinguish offline from permission denied, feature disabled, billing limited,
  maintenance, forced update, server error, and support-required states.
- Show last synced or last refreshed time where stale data could mislead the
  user.
- Label cached data, drafts, pending changes, conflicts, failed actions, and
  blocked actions distinctly.
- Disable or replace actions that cannot be performed offline.
- Keep copy simple: users should understand whether they can continue, save a
  draft, queue for later, retry, discard, update the app, contact support, or
  wait for admin action.
- Avoid raw queue IDs, internal status codes, feature flag names, or debug
  messages in normal mobile UX.
- Show tenant context with offline state when more than one tenant can be used.
- Make support paths visible when offline recovery is not enough.

The app should never silently pretend online success happened while offline.

## Queuing Offline Actions

Offline actions are queued intents, not accepted server changes.

Queue principles:

- Queue only actions whose feature, tenant, role, permission, app version,
  subscription, and admin offline policy allow offline replay.
- Store a stable local intent identifier.
- Store an idempotency key for replay.
- Store action type and minimal safe payload.
- Store tenant, user, device, feature, app-version, and timestamp context as
  claims for the API to verify.
- Store retry count, last attempt, next retry guidance, and failure/conflict
  reason where useful.
- Preserve action ordering only when business meaning depends on order.
- Keep payloads small and avoid storing secrets or oversized binary data in the
  queue.
- Support discard, edit, retry, or support escalation according to feature
  policy.
- Re-check permission, tenant state, feature flag, billing, app-version,
  maintenance, and conflict policy during replay.

Queued actions should be safe to retry and safe to reject. A queue that cannot
explain its state is not a user-friendly queue.

## Pending Changes

Users should always understand which changes are still local.

Pending-change principles:

- Pending changes should be visible near the workflow they affect.
- Dashboard, sync status, and settings should expose pending counts where useful.
- Draft, pending, syncing, synced, conflict, failed, blocked, and stale states
  should have different labels and next actions.
- Users should know whether they can leave the screen, switch tenant, logout,
  retry, edit, discard, or contact support.
- Synced state should be shown only after API acceptance.
- Conflicts should explain whether the user can edit, retry, discard, or ask
  support/admin for help.
- Failed changes should not disappear silently.
- Pending changes should not replay under a different user or tenant after
  logout, tenant switch, account switch, or server revocation.

Pending state is part of the product experience. It should be calm, visible,
and recoverable.

## Admin-Controlled Offline Limits

Admins control offline limits through Admin/API policy.

Admin-controlled limits may include:

- Which tenants can use offline mode.
- Which roles and permissions can use offline mode.
- Which features are read-only offline, draft-only offline, queueable offline,
  online-only, disabled, blocked, or emergency-disabled.
- Cache freshness thresholds.
- Cache retention and purge behavior.
- Maximum queue age.
- Maximum retry count.
- Maximum pending actions.
- Maximum payload size.
- Whether sync can run on metered networks, low battery, background mode, or
  only on explicit user action.
- Conflict mode and resolution ownership.
- Whether support can inspect safe offline diagnostics.
- Whether forced update, maintenance, suspension, billing block, tenant archive,
  or security incident stops offline replay.
- Whether app lock, biometrics, PIN, or step-up confirmation is required before
  offline cached data can be shown.

Admin controls should provide mobile impact preview before changing offline
behavior. A control that disables offline replay can affect real pending work,
so it needs support, audit, and recovery thinking before implementation.

## Offline Failure Modes

Offline-first design should name failure modes before implementation.

Failure modes include:

- User loses permission while offline.
- Tenant is suspended while offline.
- Feature is disabled while offline.
- App version becomes blocked before replay.
- Billing state blocks a queued action.
- Server record changed before queued action replays.
- Duplicate action replays after retry.
- Queued payload is too old, too large, invalid, or unsafe.
- User logs out with pending work.
- A different user logs in on the same device.
- Tenant switch happens with pending old-tenant work.
- Secure storage fails while cached data remains locally present.
- Local cache becomes stale enough to mislead the user.

Each failure mode should have a user-facing state, support meaning, privacy
posture, and API replay outcome before implementation.

## Privacy And Security

Offline storage increases privacy and security responsibility.

Privacy and security principles:

- Offline data should be tenant-scoped and user-scoped.
- Cached data should be minimized to what the mobile workflow needs.
- App lock should protect private cached data and drafts.
- Sensitive cached data should have retention, purge, and support rules.
- Diagnostics should be redacted and user-safe.
- Logout, server revocation, account switch, tenant removal, and device block
  should clear, quarantine, or hide private offline state according to policy.
- Offline replay should never trust client claims without API validation.
- Support should see safe sync context, not raw private local payloads by
  default.

Offline convenience must not weaken tenant isolation or least privilege.

## Acceptance Questions

Before implementing an offline-capable feature, documentation should answer:

- What can the user see offline?
- What can the user draft offline?
- What can the user queue offline?
- What must wait for online API access?
- What is cached, under which tenant and user, and for how long?
- What is never cached?
- What happens if the user loses permission, tenant access, feature access,
  subscription access, or app-version support while offline?
- How does the app show offline, cached, draft, pending, synced, conflict,
  failed, blocked, and stale states?
- What does admin control about offline behavior?
- What does support see when offline work fails?
- What is audited when queued work is accepted, rejected, conflicted, or
  discarded?
- What happens on tenant switch, logout, logout-all-devices, revocation, and
  account switch?

## Success Standard

Offline-first behavior is successful when mobile users can keep useful work
moving with safe cache, drafts, and queued intents; the app clearly explains
offline and pending state; protected actions wait for online API authority;
secrets and revocable authority are not cached unsafely; tenant data remains
separated; admin controls offline limits centrally; and every queued action is
accepted, rejected, conflicted, or retried only through Admin/API.
