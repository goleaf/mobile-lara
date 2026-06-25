# Mobile Dashboard Logic

Updated: 2026-06-26

This document defines mobile dashboard logic for the Mobile Lara NativePHP client. It explains how the dashboard should show current user context, current tenant, enabled feature shortcuts, sync status, offline status, unread notifications, recent activity, important announcements, and quick actions. It also explains how dashboard content changes based on permissions, feature flags, remote config, tenant status, offline state, and subscription status. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, NativePHP plugins, policies, jobs, services, providers, local storage schemas, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX Principles](mobile-ux-principles.md), [Mobile App Shell Logic](mobile-app-shell-logic.md), [Mobile Settings Logic](mobile-settings-logic.md), [Mobile Permission Logic](mobile-permission-logic.md), [API-First Principles](api-first-principles.md), [Documentation-First Architecture](documentation-first-architecture.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md), [Admin Safety Principles](admin-safety-principles.md), [Mobile And Admin Design System](design-system.md), [NativePHP Local Storage](nativephp-local-storage.md), and [NativePHP Run Notes](nativephp-run.md): the dashboard is the authenticated mobile command surface that turns API authority, tenant context, feature state, notification state, subscription status, remote config, sync health, offline state, announcements, recent activity, NativePHP capability state, and permission status into a focused daily working view.

Mobile Permission Logic is defined in [Mobile Permission Logic](mobile-permission-logic.md): native permission requests for camera, microphone, location, notifications, files, scanner, biometrics, and secure storage must explain purpose before prompting, respect feature flags and API authority, avoid disabled-feature prompts, support denied-permission recovery, and show status in settings before implementation.

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

## Dashboard Statement

The mobile dashboard is the user's first authenticated working surface.

It should not be a miniature admin panel. It should not expose every module, raw feature flag, billing rule, permission matrix, support detail, sync mechanism, or rollout cohort. It should summarize what matters now and guide the user to the next safe action.

Product rule: the dashboard is a presentation layer. It may personalize, prioritize, cache, and simplify. It must not decide tenant authority, billing entitlement, permission grants, feature enablement, subscription status, notification targeting, announcement eligibility, recent activity truth, or final sync acceptance. Those decisions come from Admin/API.

The dashboard should answer:

- Who am I signed in as?
- Which tenant/workspace am I working in?
- What can I do right now?
- What needs attention?
- What is offline, syncing, stale, blocked, disabled, limited, or unread?
- Which actions are safe from this device and app version?

## Dashboard Content Contract

Every dashboard section should have an authority source, visibility rule, empty state, offline behavior, and action rule before implementation.

| Section | Purpose | Authority source | Dashboard outcome |
| --- | --- | --- | --- |
| Current user context | Show the signed-in user's identity, role/account state, and safe profile summary. | API-authenticated user context plus local shell/session state. | User understands the active account without seeing sensitive internals. |
| Current tenant | Show the active tenant/workspace and switching eligibility. | API tenant context, role membership, tenant status, and shell tenant-switching state. | User knows which tenant their actions affect. |
| Enabled feature shortcuts | Show the most useful allowed feature entry points. | API-resolved feature flags, permissions, plan, app version, device capability, tenant status, and remote config ordering. | User sees only safe, relevant shortcuts with disabled/blocked states where useful. |
| Sync status | Show local/server freshness, pending work, conflicts, failures, and last sync. | Mobile local queue/cache plus API sync policy and replay outcomes. | User knows whether work is local, queued, pending, synced, failed, or conflicted. |
| Offline status | Show online/offline/reconnecting/constrained state. | NativePHP network status, API reachability, remote config, and sync policy. | User knows what remains safe while offline. |
| Unread notifications | Show actionable unread count and latest important items. | API notification context and push/native token state where relevant. | User sees communication needing attention without exposing notification internals. |
| Recent activity | Show safe recent user/tenant activity. | API activity feed, local accepted history, and cache freshness rules. | User can resume work and understand what changed recently. |
| Important announcements | Show platform, tenant, feature, maintenance, update, support, or billing notices. | Admin/API announcements, remote config, app-version policy, maintenance, and tenant/subscription state. | User sees relevant product messages with clear next action. |
| Quick actions | Provide fast actions for common permitted work. | API permissions, feature flags, tenant status, subscription state, remote config, NativePHP capability state, and offline policy. | User can start safe work quickly without bypassing API authority. |

This contract is intentionally principle-level. It does not create dashboard APIs, models, tables, fields, widgets, cards, Livewire components, NativePHP events, routes, controllers, policies, jobs, services, notifications, or storage records.

## Dashboard Priority Model

The dashboard should prioritize useful work while surfacing risks that change what the user can do.

Suggested priority order:

1. **Blocking shell state** - forced update, maintenance, invalid session, locked, or tenant unavailable state should replace or limit the dashboard.
2. **Safety and work continuity** - offline, sync failure, pending uploads, conflicts, stale cache, and local drafts should appear before optional content.
3. **Primary quick actions** - the most frequent permitted work should be near the top.
4. **Feature shortcuts** - enabled features should appear based on role, tenant, feature flag, remote config, subscription, device, and app-version eligibility.
5. **Unread notifications and announcements** - urgent or required messages should outrank passive informational messages.
6. **Recent activity** - should help the user resume work, not crowd the screen.
7. **Secondary actions** - settings, support, profile, diagnostics, or tenant switching should remain reachable without dominating the dashboard.

Priority should be remotely configurable only within safe bounds. Remote config may reorder dashboard sections, set limits, choose copy, hide optional panels, or promote safe quick actions. Remote config must not make unauthorized, unlicensed, disabled, suspended, maintenance-blocked, or app-version-blocked work appear available.

## Current User Context

Current user context tells the user which account is active.

Dashboard should show:

- User name or safe display label.
- Account state when it affects work: active, invited, suspended, restricted, support-assisted, locked, expired, or offline-limited.
- Role or mobile capability summary where useful.
- Profile/settings entry point when allowed.
- Session or app-lock prompt only when it affects access.

Principles:

- User context comes from API-authenticated context, not from editable local profile data.
- Cached user context may appear while offline only as last-known context.
- Suspended, revoked, invited-only, or expired session state should not show normal dashboard actions.
- Dashboard should not expose internal role IDs, permission matrices, token metadata, device trust internals, or admin-only notes.
- Profile shortcuts should be hidden, disabled, or support-routed when session, tenant, or subscription policy blocks account changes.

How content changes:

- **Permissions** control which profile/account actions appear.
- **Feature flags** can expose or hide optional profile-related mobile features.
- **Remote config** can tune profile copy, support links, and display preferences.
- **Tenant status** can limit account actions tied to the tenant.
- **Offline state** turns profile edits into read-only or draft-only behavior unless policy allows queueing.
- **Subscription status** may show billing-limited account context without granting billing authority.

## Current Tenant

Current tenant context tells the user which workspace their work belongs to.

Dashboard should show:

- Active tenant name or safe workspace label.
- Tenant status when it affects access: active, onboarding, limited, suspended, disabled, maintenance, read-only, billing-limited, or support-limited.
- Tenant switcher entry point only when the user has more than one allowed tenant and switching is safe.
- Tenant-specific sync, feature, notification, announcement, or billing-limited warnings when relevant.

Principles:

- Tenant context comes from API bootstrap/tenant context and must remain server-controlled.
- Tenant switching must not mix cache, drafts, queues, notifications, activity, feature state, config, or support context across tenants.
- Tenant status should explain user impact, not admin internals.
- If tenant status blocks work, dashboard should favor status explanation and support/contact next action over normal shortcuts.
- Offline dashboard may show last-known tenant only with freshness context.

How content changes:

- **Permissions** decide whether the user can see tenant switch, tenant settings, tenant members, or tenant-scoped reports.
- **Feature flags** decide which tenant features appear.
- **Remote config** may choose tenant label, section order, and safe copy.
- **Tenant status** can turn the dashboard into limited, read-only, maintenance, suspended, or support-only mode.
- **Offline state** can keep last-known tenant visible but must not authorize a new tenant switch.
- **Subscription status** can show plan-limited, trial, overdue, grace-period, or blocked states as API-safe outcomes.

## Enabled Feature Shortcuts

Feature shortcuts are the dashboard's main navigation into useful work.

Dashboard should show:

- Shortcuts for enabled, permitted, subscription-allowed, version-safe, device-safe, tenant-safe, and offline-compatible features.
- Disabled or blocked shortcuts only when the user expects access and needs explanation.
- Optional badge or state labels for beta, new, pending, offline-limited, update-required, maintenance, or read-only states.
- No raw flag names, rollout cohorts, billing internals, permission matrices, or tenant config internals.

Principles:

- Shortcuts are resolved from API outcomes, not local guesses.
- Hidden features disappear when there is no useful user action.
- Disabled features should use safe reason categories: permission, plan, tenant, update, maintenance, offline, device, support, or unavailable.
- Shortcut ordering can be remote-configured but must respect authority.
- NativePHP capability shortcuts should appear only when feature eligibility and device capability are both safe.
- Offline shortcuts should clearly show read-only, draft-only, queueable, or online-only behavior.

How content changes:

- **Permissions** remove or disable actions the user cannot perform.
- **Feature flags** determine visibility, rollout, beta state, deprecation, or emergency disablement.
- **Remote config** determines dashboard layout, ordering, max shortcut count, labels, and announcement placement.
- **Tenant status** can hide or disable tenant-scoped features.
- **Offline state** changes feature shortcuts to offline-safe states.
- **Subscription status** can hide, disable, upsell, read-only, or contact-admin features according to API policy.

## Sync Status

Sync status tells the user whether local work is safe, pending, or needs attention.

Dashboard should show:

- Last successful sync time or freshness label.
- Pending queue count where useful.
- Upload/download progress where user action depends on it.
- Conflict count or conflict summary.
- Failed or retry-later state.
- Draft count if drafts are not yet synced.
- Sync disabled, maintenance-limited, metered-network-limited, or subscription-limited state where relevant.

Principles:

- Sync status should distinguish local draft, queued intent, pending replay, uploading, downloading, accepted, failed, conflict, stale, and blocked.
- Dashboard should never show local queued work as accepted server work.
- Background sync should not dominate the dashboard unless action is needed.
- Conflict and failure states should provide clear next action: retry, resolve, discard, contact support, reconnect, update app, or wait.
- Sync replay must recheck permissions, feature flags, tenant status, subscription status, app version, maintenance, and conflict policy.

How content changes:

- **Permissions** can block replay or resolution for actions the user no longer owns.
- **Feature flags** can pause or disable sync for feature-specific queues.
- **Remote config** can tune sync intervals, pending thresholds, dashboard labels, and retry guidance.
- **Tenant status** can pause, block, or read-only-limit sync.
- **Offline state** changes sync status to pending/retry/reconnect guidance.
- **Subscription status** can pause uploads, limit records, block premium sync, or show billing-limited retry guidance.

## Offline Status

Offline status tells the user what can continue without API reachability.

Dashboard should show:

- Online, offline, reconnecting, constrained, metered, or API-unreachable state where it affects work.
- Last-known data indicator when cached data is used.
- Which dashboard actions remain safe.
- Sync and draft implications.
- Retry/reconnect guidance.

Principles:

- NativePHP network status is useful but not enough; API reachability matters too.
- Offline should be calm, honest, and specific.
- Offline should not bypass forced update, revoked session, suspended tenant, disabled feature, subscription block, or emergency policy after revalidation.
- Offline dashboard can show cached user, tenant, feature, notification, and activity summaries only with safe freshness labels.
- Online-only content should not pretend it is available offline.

How content changes:

- **Permissions** remain last-known while offline and require API revalidation for protected actions.
- **Feature flags** remain last-known while offline but cannot grant new authority.
- **Remote config** defines offline copy, thresholds, limits, and safe fallback behavior.
- **Tenant status** remains last-known while offline and must be revalidated before protected work.
- **Offline state** converts live sections to cached, draft-only, queueable, read-only, or unavailable states.
- **Subscription status** should not be relaxed by offline mode.

## Unread Notifications

Unread notifications help users notice important communication without turning the dashboard into an inbox.

Dashboard should show:

- Unread count.
- Latest important notification summary where useful.
- Push permission or token attention only if it affects receiving notifications.
- Link to notification center when allowed.
- Offline/cached notification freshness when relevant.

Principles:

- Notification visibility is API-controlled and tenant-safe.
- Dashboard should show the count and high-value summaries, not full notification administration.
- Notification state should respect tenant, role, subscription, support, feature, and privacy boundaries.
- Push permission status is a device state, not notification authority.
- A denied push permission may show education or settings recovery, but it should not block in-app notification visibility if API permits it.

How content changes:

- **Permissions** determine which notifications and notification actions are visible.
- **Feature flags** can enable notification categories or notification center access.
- **Remote config** can choose dashboard notification limits, copy, and priority rules.
- **Tenant status** can suppress, redirect, or maintenance-limit notification behavior.
- **Offline state** shows cached unread state with freshness and sync implications.
- **Subscription status** can limit notification categories or show billing-related notices according to API policy.

## Recent Activity

Recent activity helps users resume work and understand what changed.

Dashboard should show:

- Recent user actions accepted by API.
- Recent tenant-safe updates the user is allowed to see.
- Local drafts or queued actions only with explicit local/pending labels.
- Conflict, failed, or retry items when they need attention.
- No cross-tenant, support-only, billing-private, admin-only, or unauthorized activity.

Principles:

- Recent activity should be safe, compact, and resumable.
- API-accepted activity and local pending activity must be visually and semantically different.
- Activity should be tenant-scoped and permission-filtered.
- Dashboard activity should not become an audit log, report, or admin feed.
- Offline dashboard may show cached activity with last-updated state.

How content changes:

- **Permissions** filter activity by role, user, tenant, support scope, and record access.
- **Feature flags** determine whether feature-specific activity appears.
- **Remote config** can set activity limit, grouping, retention window, and empty-state copy.
- **Tenant status** can make activity read-only, hidden, maintenance-limited, or support-routed.
- **Offline state** shows cached or local-only activity with freshness labels.
- **Subscription status** can hide premium activity categories or show plan-limited summaries.

## Important Announcements

Announcements are admin-controlled messages that affect the mobile user.

Dashboard should show:

- Platform announcements.
- Tenant announcements.
- Feature announcements.
- Maintenance notices.
- Forced or optional update notices.
- Subscription, trial, overdue, grace-period, or plan-limit notices where relevant.
- Support or incident guidance.

Principles:

- Announcements should be scoped, relevant, and dismissible only when policy allows.
- Critical announcements should outrank regular dashboard content.
- Announcement copy should be safe for the user's role and tenant.
- Remote config may tune announcement copy or placement, but Admin/API decides eligibility and severity.
- Dashboard should avoid showing multiple competing banners when one clear state is enough.

How content changes:

- **Permissions** determine whether the user can see administrative, support, billing, or operational announcement details.
- **Feature flags** can target feature-specific announcements.
- **Remote config** can control copy, placement, priority, and dismiss behavior.
- **Tenant status** can create tenant-specific maintenance, limited, suspended, or support announcements.
- **Offline state** shows last-known announcements only with freshness and retry context.
- **Subscription status** can show billing-limited or contact-admin notices without exposing billing internals.

## Quick Actions

Quick actions let users start common work fast.

Dashboard should show:

- Primary allowed actions for the current role and tenant.
- Offline-safe actions only where policy permits read-only, draft-only, queueable, or online-only behavior.
- Native capability actions only after feature, permission, device, subscription, and app-version checks.
- Support or retry actions when normal work is blocked.
- Logout, lock, refresh, sync now, contact support, update app, or switch tenant when relevant.

Principles:

- Quick actions must be permission-filtered, feature-filtered, tenant-scoped, subscription-aware, and API-safe.
- Dangerous, destructive, billing-sensitive, tenant-sensitive, or broad actions should not be one-tap from dashboard without confirmation or review.
- Quick actions should not create duplicate submissions.
- Quick actions should show loading, pending, queued, success, failed, conflict, or blocked state clearly.
- Quick action order may be remote-configured within safe limits.

How content changes:

- **Permissions** decide whether the action is shown, hidden, disabled, or contact-admin.
- **Feature flags** decide whether the action is enabled, beta, disabled, emergency-blocked, deprecated, or update-required.
- **Remote config** can order actions, choose labels, set action count, and tune safe empty states.
- **Tenant status** can make actions read-only, maintenance-limited, support-only, or hidden.
- **Offline state** converts actions to draft-only, queueable, read-only, retry, or unavailable states.
- **Subscription status** can disable premium actions, show plan-limit explanations, or route to contact admin.

## Dashboard Change Matrix

Dashboard content should change predictably as product state changes.

| Condition | Dashboard behavior |
| --- | --- |
| Permission denied | Hide action if irrelevant; disable with safe reason if the user expects access; never rely on UI hiding as authorization. |
| Feature flag disabled | Remove shortcut or show disabled state with reason category, support path, or contact-admin action. |
| Feature flag emergency-disabled | Fail closed, remove or block actions, preserve safe drafts, and show support-safe message. |
| Remote config changes | Reorder, rename, limit, or tune dashboard content only within validated safe bounds. |
| Remote config missing/invalid | Fall back to safe defaults and do not grant new visibility or action access. |
| Tenant active | Show normal permitted tenant-scoped content. |
| Tenant onboarding/limited | Show setup or limited-work guidance and only safe permitted shortcuts. |
| Tenant suspended/disabled | Replace normal dashboard work with blocked/support/contact-admin behavior. |
| Tenant maintenance | Show scoped maintenance and safe limited/read-only/draft-only behavior. |
| Offline | Show last-known context, offline-safe actions, pending sync, stale labels, and reconnect guidance. |
| Subscription active | Show entitled features and normal plan-limited context. |
| Subscription trial/grace | Show allowed features plus safe notice if user action is needed. |
| Subscription overdue/blocked | Hide or disable plan-limited features, show contact-admin/support guidance, and protect local drafts. |
| App version outdated | Show optional update, deprecated, force-update, or blocked state according to API policy. |
| Push permission denied | Keep in-app notifications where allowed, show permission education/settings recovery only when useful. |
| Sync conflict | Surface conflict summary and route to resolution before promoting new work. |

## Dashboard Data Freshness

Dashboard data can come from API, cache, local drafts, local queue, NativePHP status, or remote config. The user should not need to know the implementation source, but the dashboard must represent freshness honestly.

Principles:

- Server-confirmed data may be shown as current after successful API refresh.
- Cached data should be labeled last-known where stale state changes user decisions.
- Local drafts and queued actions should be labeled local, pending, or queued.
- NativePHP network/push/permission/device status should be treated as local device state.
- Dashboard should refresh context after login, app resume, reconnect, tenant switch, sync completion, feature/config update, and app-version check.
- Dashboard should never use cached permissions, cached feature flags, cached subscription state, or cached tenant status to grant protected actions after API revalidation says otherwise.

## Dashboard Empty And Blocked States

Empty and blocked states are product states, not errors by default.

Principles:

- If no feature shortcuts are available, explain whether this is due to role, tenant, plan, offline, app version, maintenance, or setup state.
- If there are no notifications, recent activity, or announcements, keep the section quiet or omit it.
- If sync has no pending work, show healthy status only if it helps trust.
- If subscription blocks work, explain contact-admin/support path without exposing billing internals.
- If tenant is suspended or maintenance-limited, prioritize safe next action over empty dashboard content.
- If remote config hides optional sections, the dashboard should not look broken.

## Dashboard Boundaries

The dashboard must never own:

- Permission authority.
- Tenant authority.
- Billing or subscription authority.
- Feature flag authority.
- Remote config authority.
- Notification targeting authority.
- Announcement eligibility authority.
- Sync acceptance or conflict decision authority.
- Audit truth.
- App-version safety.
- Support visibility.

The dashboard may own:

- Presentation priority.
- Local section arrangement within API/config bounds.
- Local loading, offline, pending, stale, and blocked states.
- User-friendly labels and next-action framing.
- Local quick-action affordances for API-authorized operations.
- Native capability education and local device-state explanation.

## Risk Register

| Risk | Dashboard principle |
| --- | --- |
| Dashboard becomes an admin panel | Show only mobile-user context, next actions, and safe summaries. |
| User acts in wrong tenant | Keep tenant context visible and isolate tenant-specific cache, activity, notifications, and queues. |
| Hidden feature still runs through quick action | Quick actions must use API-resolved feature and permission outcomes. |
| Offline dashboard looks current | Label cached and last-known data where freshness matters. |
| Pending work looks synced | Separate draft, queued, pending, accepted, failed, and conflict states. |
| Subscription state leaks billing internals | Use safe reason categories and contact-admin/support guidance. |
| Announcements overwhelm work | Prioritize critical messages and limit competing banners. |
| Recent activity leaks tenant/private data | Filter by tenant, role, support scope, and API permission. |
| Push permission is confused with notification authority | Treat push permission as device delivery state only. |
| Remote config grants unsafe access | Validate config and keep authority in API permissions, flags, subscription, tenant status, and version policy. |

## Success Test

Mobile dashboard logic is successful when a mobile user can open the app and immediately understand:

- Which account and tenant are active.
- Which work is available now.
- Which work is blocked and why.
- Whether the app is online, offline, syncing, stale, pending, failed, or conflicted.
- Whether notifications, announcements, or recent activity need attention.
- Which quick actions are safe.
- Which next action is most important.

Before implementation, every dashboard section should answer:

- What authority source controls this content?
- What permissions and features are required?
- What remote config can safely change?
- What tenant status changes it?
- What offline state changes it?
- What subscription state changes it?
- What local cache or sync freshness label is required?
- What empty, disabled, blocked, or error state appears?
- What user action is allowed?
- What must never be shown on this dashboard?

If a section cannot answer those questions, it is not ready for implementation planning.
