# Multi-Tenant Mobile Logic

Updated: 2026-06-26

This document defines multi-tenant mobile logic for the Mobile Lara SaaS
system. It explains how users with multiple tenants choose a tenant, how mobile
remembers current tenant, how mobile changes tenant safely, how cached data is
separated logically by tenant, how feature flags and permissions change per
tenant, how sync behaves after tenant switch, and how logout affects tenant
context. It is documentation only and does not define database structure,
database fields, migrations, seeders, routes, controllers, Livewire
components, Filament resources, NativePHP plugins, policies, gates,
middleware, jobs, services, local storage schemas, API endpoints, cache tables,
sync queues, tenant-switch actions, or application logic.

Use this document with [Two-System Boundary Logic](two-system-boundary.md),
[API-First Principles](api-first-principles.md), [Authentication Principles](authentication-principles.md),
[Mobile Client Responsibilities](mobile-client-responsibilities.md), [NativePHP
Local Storage](nativephp-local-storage.md), [Mobile App Shell Logic](mobile-app-shell-logic.md),
[Mobile Dashboard Logic](mobile-dashboard-logic.md), [Mobile Settings Logic](mobile-settings-logic.md),
[Role And Permission Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Tenant Lifecycle
Logic](tenant-lifecycle-logic.md), [Tenant Admin Logic](tenant-admin-logic.md),
[Offline-First Principles](offline-first-principles.md), [Offline UX
Logic](offline-ux-logic.md), [Sync Lifecycle
Logic](sync-lifecycle-logic.md), [Conflict Resolution Logic](conflict-resolution-logic.md),
[Audit Logic](audit-logic.md), [Data Privacy Principles](data-privacy-principles.md),
[Admin Safety Principles](admin-safety-principles.md), and [API v1 Tenancy
Contract](../contracts/api/v1-tenancy.md): multi-tenant mobile behavior is the
local user experience for choosing and remembering tenant context while
offline-first behavior defines which tenant data may be cached, queued, shown,
or blocked without online confirmation. Sync lifecycle behavior defines how
tenant-scoped bootstrap, pull, push, retry, conflicts, acknowledgement, and sync
health behave after tenant choice or tenant switch. Conflict resolution defines
how tenant-scoped conflicts are shown, audited, auto-resolved, user-resolved, or
escalated without cross-tenant leakage. Admin/API remains authoritative for
tenant access, tenant state, permissions, features, config, sync acceptance, and
logout/session revocation.

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

## Multi-Tenant Mobile Statement

The mobile client may remember the current tenant, but it must not own tenant
authority.

Users who belong to more than one tenant need a simple way to choose where they
are working. That choice defines every mobile screen, dashboard shortcut,
cached data set, draft, offline queue, notification context, report summary,
support action, feature flag, permission result, remote config value, and sync
decision that follows.

Product rule: the current tenant is valid only when the API confirms the user
can access that tenant in the current account, device, app-version, lifecycle,
subscription, permission, feature, maintenance, and security context.

## Authority Split

Tenant context spans both systems, but the trust boundary stays server-side.

| Area | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Tenant list | Which tenants the user can see, switch to, or is blocked from using. | Simple list presentation, current tenant label, disabled tenant explanations, and refresh UX. |
| Current tenant | Server-confirmed selected tenant, tenant state, membership, role, permissions, features, config, billing, support, reports, and sync policy. | Remembering the last confirmed tenant locally and showing it as current only with freshness/validation awareness. |
| Tenant switch | Validating switch eligibility, denying unsafe switches, returning new tenant context, and auditing switch attempts. | Switcher UX, confirmation where useful, local transition state, cache/draft boundaries, and post-switch refresh prompts. |
| Local cache | Which cached tenant data may be displayed or purged under policy. | Tenant-keyed cached snapshots, freshness labels, local drafts, pending queues, and safe local cleanup UX. |
| Permissions and flags | Per-tenant role, permission, feature flag, plan, config, version, maintenance, and lifecycle decisions. | Rendering tenant-specific capability outcomes without reusing another tenant's state. |
| Sync | Replay acceptance, conflict decisions, idempotency, tenant-scope validation, and final server state. | Tenant-scoped queue display, pending counts, retry UX, conflict display, and post-switch sync status. |
| Logout | Token/session revocation, account/device state, logout-all-devices decisions, and server audit. | Clearing current tenant presentation, secure tokens, private cached session summaries, and protected pending replay state. |

## Tenant Selection

Tenant selection should happen after authentication or context refresh when the
API says more than one tenant is available.

Selection principles:

- The API returns allowed tenant choices and disabled tenant explanations.
- Mobile should show only API-returned tenants.
- A single allowed active tenant can become current without forcing the user
  through a chooser when the API makes that clear.
- Multiple allowed tenants should be shown in a simple tenant chooser with
  tenant name, status, current marker, and safe disabled reason where relevant.
- Tenants that are suspended, archived, billing-blocked, in maintenance,
  deletion-requested, or unavailable should appear only when the API says a
  role needs to see them, and only with safe next action guidance.
- Tenant selection must not rely on stale local labels, old invitations, old
  tenant IDs, local URLs, or cached membership lists.
- Pre-login, invited, suspended, and guest states should not enter private
  tenant selection unless API context explicitly allows a safe activation or
  support flow.

Mobile should avoid making tenant selection feel like a technical workspace
picker. It should feel like choosing the business or location where the user is
about to work.

## Remembering Current Tenant

Mobile may remember the last server-confirmed current tenant to improve startup
speed and reduce repeated selection.

Remembering principles:

- The remembered tenant is a local preference and context hint, not authority.
- The remembered tenant should include enough safe metadata for presentation:
  tenant label, public tenant identifier, last confirmed time, freshness state,
  and whether a fresh bootstrap/context refresh is required.
- The remembered tenant must be tied to the authenticated user and device
  context.
- The remembered tenant must not be reused after a different user logs in.
- The remembered tenant should be cleared, quarantined, or marked stale when
  the user logs out, the server revokes access, secure token storage fails, the
  tenant disappears from API choices, or tenant state becomes unsafe.
- Mobile may show a last-known tenant while offline only as last-known context,
  not as proof of current access.
- Sensitive tenant data should remain protected by app lock and privacy rules
  even when the remembered tenant is available locally.

When the app starts online, mobile should refresh context before protected work
uses the remembered tenant.

## Safe Tenant Switching

Tenant switching is a security-sensitive context change.

Switching principles:

- Mobile sends the desired tenant choice to the API as a request, not a command.
- The API confirms or denies the switch based on membership, tenant lifecycle,
  role, permission, subscription, app version, maintenance, support, and
  security state.
- A successful switch should require fresh tenant-scoped context before mobile
  exposes protected screens.
- A failed switch should leave the previous tenant context intact when it is
  still valid, or move the user to a safe no-current-tenant/support state when
  it is not.
- Switching should not carry open forms, dashboard shortcuts, record details,
  reports, support cases, notifications, native permission explanations,
  feature states, or sync actions from the previous tenant as if they belonged
  to the new tenant.
- Switching while offline should be disabled unless a future documented policy
  allows an offline read-only preview. Protected tenant switching requires API
  confirmation.
- Mobile should show a clear transition state when changing tenant: switching,
  refreshing context, sync paused, unavailable, retry, or support-required.

Tenant switch UX should prioritize correctness over speed. A fast wrong tenant
switch is worse than a short loading state.

## Tenant-Separated Cache

Cached data must be logically separated by tenant.

Cache separation principles:

- Tenant-scoped cached resources, dashboard data, settings snapshots, feature
  snapshots, permission snapshots, config snapshots, report summaries,
  notification history, support context, drafts, queues, and sync metadata
  should be associated with the tenant they came from.
- Mobile should never display one tenant's cached data under another tenant's
  current context.
- Tenant-specific cache should carry freshness and source information so users
  can understand whether data is current, stale, offline, partial, or awaiting
  sync.
- Tenant-specific drafts should not move automatically to another tenant.
- Tenant-specific pending actions should not replay after a tenant switch until
  the API confirms they still belong to the tenant they were created for.
- Cross-tenant local search should be avoided unless a future role-specific
  product rule documents safe, tenant-labeled, permission-aware behavior.
- Tenant cache cleanup should respect data privacy, logout, tenant removal,
  support diagnostics, app lock, and offline recovery requirements.

Local storage may be one physical store, but product behavior must treat each
tenant as a separate logical boundary.

## Per-Tenant Feature Flags And Permissions

Feature flags and permissions can change when the current tenant changes.

Per-tenant capability principles:

- Permissions should be resolved for the selected tenant.
- Feature flags should be resolved for the selected tenant.
- Remote config should be resolved for the selected tenant.
- Subscription and plan state should be resolved for the selected tenant.
- Tenant lifecycle state should be resolved for the selected tenant.
- App-version and maintenance policy may vary by tenant or feature and should
  be refreshed after switch.
- Mobile navigation, dashboard shortcuts, settings sections, native permission
  prompts, reports, support actions, notifications, and offline eligibility
  should update after fresh tenant context.
- Mobile should not reuse permission or feature snapshots from the previous
  tenant.
- Disabled features should avoid native permission prompts and prevent new
  tenant-scoped queued work.
- If a capability disappears after switch, mobile should close or replace the
  affected screen with a clear disabled, denied, update-required, maintenance,
  billing-limited, or support-required state.

Capability changes should be normal, not exceptional. The same user can be a
tenant admin in one tenant, a mobile user in another, suspended in another, and
invited-only in another.

## Sync After Tenant Switch

Tenant switching should make sync behavior more careful, not more eager.

Sync principles:

- Sync state is tenant-scoped.
- Pending actions should keep the tenant context they were created under.
- Switching tenants should pause or finish visible sync work safely before
  showing the new tenant as ready, according to documented sync policy.
- Mobile should not replay pending actions for the previous tenant while
  presenting the new tenant unless the sync UI clearly labels background
  tenant work and the API policy allows it.
- If background sync across tenants is allowed later, each action still needs
  tenant, user, device, feature, permission, version, and idempotency checks
  during replay.
- After tenant switch, mobile should refresh bootstrap/context before syncing
  tenant-specific writes for the new tenant.
- Conflicts should identify the tenant context safely enough for the user,
  tenant admin, or support to resolve without cross-tenant leakage.
- Tenant switch should not erase pending work silently. It should preserve,
  quarantine, sync, discard, or escalate pending work according to documented
  feature and sync policy.

Examples of switch-sensitive sync outcomes:

- A user switches tenants with no pending work: mobile refreshes context and
  shows the new tenant dashboard.
- A user switches tenants with pending work for the old tenant: mobile keeps the
  pending work under the old tenant and shows it only in old-tenant sync status
  or a safe global pending indicator if policy allows.
- A feature is disabled in the new tenant: mobile hides or disables that
  feature and does not queue new work for it.
- A permission changed while offline: sync replay rechecks permission and may
  return denied, conflict, support-required, or discarded states.

## Logout And Tenant Context

Logout changes tenant context because tenant context belongs to an authenticated
session.

Logout principles:

- Logout should clear current tenant presentation for the active session.
- Logout should clear or quarantine tenant-scoped private cache according to
  privacy, offline recovery, app lock, and support policy.
- Logout should prevent protected tenant-scoped queued actions from replaying
  under a later user.
- Logout should preserve only safe, explicitly allowed local diagnostics,
  support references, or draft recovery metadata.
- Logout-all-devices should assume tenant context on other devices is revoked
  once the API confirms the action.
- If logout happens offline, mobile may perform local cleanup and mark
  server-side logout as unconfirmed, but it must not continue using the old
  current tenant as active authority.
- When the same user logs in again, tenant context should be restored only
  after API confirmation.
- When a different user logs in, all previous current-tenant context must be
  unavailable to the new user.

Logout is not only token cleanup. It is also tenant-context cleanup.

## Offline Behavior

Offline mode should be honest about tenant context.

Offline principles:

- Mobile may show the last confirmed current tenant when offline if offline
  policy allows it.
- Mobile should label offline tenant context as last-known.
- Mobile should avoid tenant switching while offline.
- Mobile may allow offline read, draft, or queue behavior only for the current
  last-confirmed tenant and only when the feature's offline policy allows it.
- Mobile should not accept new invitations, create tenant membership, restore
  a removed tenant, or change current tenant authority while offline.
- When network returns, mobile should refresh authentication, tenant list,
  current tenant, permissions, feature flags, config, version policy,
  subscription state, and sync policy before protected replay.

Offline tenant behavior exists for resilience, not hidden access.

## Mobile UX Rules

Multi-tenant mobile UX should stay simple.

UX principles:

- Always show the current tenant clearly in the dashboard or shell when tenant
  context matters.
- Make tenant switching reachable but not noisy.
- Show disabled tenant reasons in plain user-safe language.
- Avoid exposing raw tenant IDs, internal status codes, billing internals,
  support internals, or permission matrices.
- Use tenant labels consistently on sync status, support cases, report
  summaries, notifications, and cached/offline warnings when ambiguity is
  possible.
- Warn before actions that could be lost, hidden, paused, or quarantined by
  switching tenants.
- Keep native permission education tenant-aware when a feature is enabled in
  one tenant but disabled in another.
- Make no-current-tenant, tenant-unavailable, tenant-suspended,
  billing-limited, maintenance, and update-required states actionable.

## Privacy And Audit

Tenant context is a privacy boundary and an audit topic.

Privacy principles:

- Tenant labels, membership, reports, notifications, support cases, diagnostics,
  cached records, drafts, and sync queues must not leak across tenants.
- Diagnostics should describe tenant context safely without exposing private
  tenant data.
- Support views should see tenant switch context only where job scope allows.
- Tenant admins should not see another tenant's mobile cache, drafts, pending
  actions, notifications, or switch history.

Audit principles:

- Successful tenant switches, failed tenant switches, cross-tenant denials,
  unsafe switch attempts, tenant-unavailable outcomes, and support-relevant
  tenant context changes should be audit candidates.
- Audit should answer which user, device, tenant, previous tenant, policy, and
  outcome were involved without exposing secrets or private cached payloads.
- Mobile may show local activity hints, but server audit remains Admin/API
  authority.

## Acceptance Questions

Before implementing any multi-tenant mobile behavior, documentation should
answer:

- How does the user get the allowed tenant list?
- What does mobile show when there is one tenant, multiple tenants, no tenants,
  disabled tenants, or unavailable tenants?
- How is current tenant remembered and refreshed?
- What happens if the remembered tenant is no longer allowed?
- What cache, draft, queue, notification, support, report, and settings data is
  tenant-scoped?
- How do feature flags, permissions, remote config, version policy, lifecycle
  state, subscription state, and maintenance state change after switch?
- What happens to pending sync before, during, and after switch?
- What is allowed while offline?
- What gets cleared, quarantined, preserved, or refreshed on logout?
- Which switch outcomes are audited or support-visible?

## Success Standard

Multi-tenant mobile logic is successful when a user can choose the correct
tenant easily, mobile remembers the last confirmed tenant without treating it
as authority, tenant switching refreshes context safely, cached data and queued
work never leak across tenants, feature flags and permissions update per
tenant, sync remains tenant-scoped after switch, logout removes active tenant
authority, and every tenant-sensitive mobile behavior resolves through the API
before it becomes trusted.
