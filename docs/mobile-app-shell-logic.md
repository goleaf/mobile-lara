# Mobile App Shell Logic

Updated: 2026-06-26

This document defines mobile app shell logic for the Mobile Lara NativePHP client. It explains welcome state, authenticated state, locked state, offline state, maintenance state, forced update state, tenant switching state, sync-in-progress state, permission-blocked state, feature-disabled state, and how users move between those states. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, NativePHP plugins, policies, jobs, services, providers, local storage schemas, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX Principles](mobile-ux-principles.md), [API-First Principles](api-first-principles.md), [Documentation-First Architecture](documentation-first-architecture.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md), [Admin Safety Principles](admin-safety-principles.md), [Mobile And Admin Design System](design-system.md), [NativePHP Local Storage](nativephp-local-storage.md), and [NativePHP Run Notes](nativephp-run.md): the app shell is the mobile state coordinator that translates API authority, NativePHP capability state, local cache, secure session posture, offline status, sync work, feature availability, tenant context, maintenance policy, and update policy into one clear mobile experience.

Mobile Dashboard Logic is defined in `mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Mobile Settings Logic is defined in `mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

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

Offline-First Principles are defined in `offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

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

Voice Note Logic is defined in `voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

Sync Lifecycle Logic is defined in `sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

## Shell Statement

The NativePHP mobile shell is the first product layer a mobile user experiences.

It should answer four questions at all times:

- Can this user use the app?
- Which tenant, session, version, feature, permission, network, or sync condition affects the user right now?
- What can the user safely do next?
- Which state is local convenience and which state is API authority?

The shell does not own SaaS authority. It coordinates state presentation, navigation eligibility, safe local recovery, and NativePHP capability prompts. Admin/API still owns authentication, tenant access, role and permission decisions, feature availability, billing entitlement, app-version policy, maintenance state, conflict decisions, and final sync acceptance.

Product rule: the shell may keep the app usable, but it must never make stale local state look like server approval.

## Shell State Precedence

Some shell states are full-app gates. Others are overlays on an otherwise usable app. The user should never see contradictory states such as normal authenticated navigation behind a forced update block.

State precedence should be documented before implementation:

1. **Forced update state** - Blocks normal use when Admin/API says the build is unsafe, unsupported, blocked, or contract-incompatible.
2. **Maintenance state** - Blocks, limits, or narrows use according to platform, tenant, feature, API, sync, notification, billing, support, or version maintenance policy.
3. **Suspended, revoked, or invalid session state** - Returns the user to welcome, recovery, support, or blocked-session behavior according to API authority.
4. **Locked state** - Protects local device access while keeping server authority unchanged.
5. **Welcome state** - Applies when there is no usable local authenticated session or the user is pre-login.
6. **Authenticated state** - Normal shell state after API confirms the user, tenant, session, app version, and context may operate.
7. **Tenant switching state** - Temporary transition while changing the active tenant context through API.
8. **Offline state** - Overlay or limited state when network is unavailable, unknown, constrained, or too expensive for configured sync behavior.
9. **Sync-in-progress state** - Overlay while local drafts, queued intents, uploads, downloads, bootstrap refresh, conflict checks, or replay work is running.
10. **Permission-blocked state** - Feature-level state when a NativePHP capability is missing, denied, restricted, unavailable, or unsupported.
11. **Feature-disabled state** - Feature-level state when Admin/API says a feature is hidden, disabled, blocked, deprecated, update-required, plan-limited, offline-limited, or maintenance-limited.

This precedence is a product principle, not an implementation algorithm. Future code may represent states differently, but user-facing behavior should remain clear, predictable, and safe.

## Shell State Contract

Every shell state should document its owner, entry reason, visible user meaning, allowed actions, and exit path.

| State | Authority source | User meaning | Allowed user movement |
| --- | --- | --- | --- |
| Welcome | Local session absence plus API/login availability. | The user has not entered an authenticated mobile workspace. | Register, login, accept invitation, recover account, check update/maintenance message, or leave app. |
| Authenticated | API confirms session, tenant, app version, permissions, feature context, and user status. | The user can work in the active tenant within current policy. | Navigate allowed features, start tasks, sync, switch tenant, lock, logout, or respond to feature/version/offline states. |
| Locked | Local security policy, timeout, app resume, biometric/PIN requirement, or user action. | The app is protecting local access on this device. | Unlock locally, logout, recover session if allowed, or remain blocked until API/local policy permits. |
| Offline | NativePHP/network status, failed reachability, constrained connection, or API unavailable. | The app is using last-known data and safe local actions only. | Continue read-only/draft/queueable work where policy allows, retry, wait, view sync status, or logout. |
| Maintenance | API/admin maintenance policy. | The platform, tenant, feature, API, sync, billing, support, or notification area is temporarily limited. | Read allowed status, preserve local drafts, retry later, use limited mode, contact support, or logout. |
| Forced update | API/admin app-version policy. | This app build cannot safely continue normal work. | Update through approved link, view support/release guidance, preserve safe local data, logout, or retry version check after update. |
| Tenant switching | User selects tenant and API validates tenant context. | The app is changing the active workspace. | Confirm or cancel where safe, wait for API context, recover previous tenant on failure, or enter target tenant after validation. |
| Sync-in-progress | Local queue, API sync, upload/download, conflict check, bootstrap refresh, or replay operation. | Work is being saved, sent, refreshed, reconciled, or verified. | Keep working where safe, pause/cancel only if policy allows, view pending state, retry failures, or resolve conflicts. |
| Permission-blocked | NativePHP capability status, OS permission status, device support, or admin feature eligibility. | A native capability needed by this feature is unavailable or denied. | Learn why permission is needed, open settings if appropriate, use fallback, retry permission, or leave feature. |
| Feature-disabled | API-resolved feature, plan, role, tenant, app-version, maintenance, offline, or emergency state. | This feature is not available in the current context. | Use alternative allowed workflow, contact admin/support, update app, reconnect, switch tenant, or wait for rollout. |

## Welcome State

Welcome state is the pre-authenticated shell.

Entry conditions:

- No local session exists.
- Secure local session data is missing, cleared, expired, or unusable.
- The user intentionally logged out.
- API rejects session recovery and requires login.
- The user is invited, pre-login, or recovering access.
- Forced update or maintenance is known before login and must be shown before normal registration/login behavior.

Principles:

- Welcome should stay minimal and mobile-first.
- Welcome may show login, register, accept invitation, password recovery, update, maintenance, or support entry points when allowed.
- Welcome should not request native permissions that are not needed before authentication.
- Welcome should not expose tenant data, feature flags, billing state, sync queues, support internals, or cached private content.
- Welcome may show offline or retry-later state, but should not pretend that login can succeed without API reachability unless a documented recovery flow exists.

User movement:

- **Welcome to authenticated**: user authenticates through API and receives valid session, tenant context, feature context, config, version policy, and user status.
- **Welcome to forced update**: API or app-version check says the current build cannot proceed.
- **Welcome to maintenance**: API or public maintenance check says normal entry is blocked or limited.
- **Welcome to offline**: no network or API reachability exists; user sees offline login/retry guidance.
- **Welcome to locked**: only if local policy protects a partially recoverable session before showing private data.

## Authenticated State

Authenticated state is the normal working shell.

Entry conditions:

- API confirms the session is valid.
- API confirms user status allows mobile use.
- API confirms active tenant context.
- API confirms app version is current, supported, deprecated-but-allowed, or optional-update only.
- API returns resolved permissions, feature states, remote config, sync policy, support context, and user context.
- Local lock policy is satisfied.

Principles:

- Authenticated state should expose only allowed mobile navigation.
- The active tenant, account, sync, offline, notification, support, and app-version status should be reachable without overwhelming the user.
- API-derived feature visibility controls navigation and feature entry.
- Local cache may speed up the shell, but protected work should refresh or revalidate before submission when freshness matters.
- Authenticated state should gracefully degrade into offline, sync, permission-blocked, feature-disabled, tenant switching, maintenance, locked, or forced update states.

User movement:

- **Authenticated to locked**: app resumes, times out, user locks it, or local security policy requires reauthentication.
- **Authenticated to offline**: NativePHP network status or API reachability becomes unavailable or constrained.
- **Authenticated to sync-in-progress**: a queue replay, upload, download, bootstrap refresh, or conflict check starts.
- **Authenticated to tenant switching**: user selects another allowed tenant.
- **Authenticated to permission-blocked**: user enters a feature requiring denied, unavailable, or unsupported native capability.
- **Authenticated to feature-disabled**: API changes feature availability, plan, permission, tenant, version, maintenance, or emergency state.
- **Authenticated to maintenance**: API reports maintenance affecting the platform, tenant, feature, API, sync, billing, support, or notification area.
- **Authenticated to forced update**: API reports the current build is unsafe or unsupported.
- **Authenticated to welcome**: user logs out, session expires without recovery, or API revokes access.

## Locked State

Locked state protects local access on the device.

Entry conditions:

- User manually locks the app.
- App resumes from background and local policy requires unlock.
- Idle timeout or security policy requires local reauthentication.
- Biometric/PIN policy is enabled and available.
- Sensitive cached state should not be shown until local unlock succeeds.

Principles:

- Locked state is local protection, not server authority.
- Unlocking locally should not prove that the API session is still valid.
- Locked state should hide private tenant data where appropriate.
- Local biometric/PIN prompts should be clear and recoverable.
- If biometric or device unlock fails, the shell should offer safe fallback such as retry, logout, or support/recovery according to policy.
- If the app is offline, unlocking may allow only last-known safe offline behavior until API revalidation.

User movement:

- **Locked to authenticated**: local unlock succeeds and API/session context is already valid or can be revalidated.
- **Locked to offline**: local unlock succeeds but API reachability is unavailable; only offline-safe shell behavior appears.
- **Locked to welcome**: user logs out, clears local session, session recovery fails, or local secrets are missing.
- **Locked to forced update or maintenance**: API version or maintenance policy is discovered before normal work resumes.

## Offline State

Offline state communicates that the app is operating with limited or last-known context.

Entry conditions:

- NativePHP network status says the device is disconnected.
- API reachability fails even if the device reports connectivity.
- Connection is constrained or expensive and policy limits sync.
- API maintenance, rate limiting, or service outage produces retry-later behavior.
- The app is using cached bootstrap, cached feature state, cached config, local drafts, or queued intents.

Principles:

- Offline state should be honest, calm, and specific.
- Offline should not bypass forced update, revoked session, suspended user, disabled tenant, emergency feature disablement, or unsafe version policy after revalidation.
- The shell should distinguish read-only offline, draft-only offline, queueable offline, and online-only behavior.
- Cached data should be labeled when stale data could mislead the user.
- Local drafts and queues should be visible enough that users know what has not reached the API.
- Reconnect should trigger safe revalidation before protected queued actions become accepted.

User movement:

- **Offline to authenticated**: network and API recover, session and tenant context revalidate, and no higher-priority state blocks use.
- **Offline to sync-in-progress**: queued work begins replay or bootstrap/config/feature state refresh starts after reconnect.
- **Offline to locked**: local security policy requires lock while offline.
- **Offline to welcome**: user logs out or local session cannot be recovered.
- **Offline to forced update or maintenance**: API revalidation reports version or maintenance block after reconnect.
- **Offline to feature-disabled**: API revalidation says a cached feature is no longer available.

## Maintenance State

Maintenance state is API/admin-controlled operational limitation.

Entry conditions:

- API reports platform maintenance.
- API reports tenant maintenance.
- API reports feature, API, sync, billing, support, notification, or version-range maintenance.
- Admin remote config or app-version policy resolves into limited-mode, retry-later, draft-only, queueable, read-only, or blocked behavior.

Principles:

- Maintenance should name the safe scope without exposing sensitive internal incident details.
- Maintenance may block the whole app, a tenant, a feature, sync, uploads, reports, billing, support, notifications, or specific actions.
- Local drafts should be preserved where safe.
- Offline work during maintenance should follow API policy: blocked, read-only, draft-only, queueable, or limited.
- Users should see retry guidance, expected next action, support path, and logout where appropriate.
- Maintenance does not make an unsupported app version safe.

User movement:

- **Maintenance to authenticated**: API says maintenance ended or no longer applies to the active user/tenant/app context.
- **Maintenance to offline**: network disappears while maintenance state is cached; shell shows last-known maintenance with retry guidance.
- **Maintenance to forced update**: version policy becomes higher priority than maintenance.
- **Maintenance to welcome**: user logs out or session becomes invalid.
- **Maintenance to feature-disabled**: scoped maintenance ends but the feature remains disabled by feature, plan, tenant, role, or version policy.

## Forced Update State

Forced update state protects users from unsafe old app builds.

Entry conditions:

- API says current build is below minimum supported version.
- API says current build, platform, channel, tenant version range, or capability set is blocked.
- API says a known security, API compatibility, NativePHP capability, sync, data-loss, or incident risk requires update.
- A stale client cannot safely interpret required API, config, feature, permission, or sync policy.

Principles:

- Forced update blocks normal authenticated navigation.
- The shell should preserve safe local drafts where possible without allowing protected sync or edits that require the unsafe build.
- Store links, distribution links, update copy, support copy, and retry guidance come from Admin/API.
- The shell should avoid exposing admin internals or incident details.
- Logout, support, diagnostics, and retry-version-check actions may remain available where safe.
- After update, the app should rebootstrap through API before returning to normal work.

User movement:

- **Forced update to authenticated**: user updates, relaunches, API validates the new build, and session/tenant context remains valid.
- **Forced update to welcome**: user logs out, session expires, or local session recovery fails.
- **Forced update to maintenance**: updated or blocked app receives maintenance state after version check.
- **Forced update to offline**: update cannot be verified because API is unreachable; the shell should not grant normal work from stale local state.

## Tenant Switching State

Tenant switching state protects tenant isolation during workspace changes.

Entry conditions:

- Authenticated user selects another tenant.
- API returns a tenant switch requirement, invitation acceptance, tenant selection prompt, or current-tenant invalidation.
- Active tenant becomes unavailable, suspended, disabled, maintenance-limited, or removed from the user's allowed tenant list.

Principles:

- Tenant switching must happen through API.
- The shell should not mix cached data, drafts, queues, permissions, feature flags, config, reports, support context, or notifications across tenants.
- The current tenant should remain stable until the new tenant is validated, unless API requires the user to leave it immediately.
- If switching fails, the shell should return to the previous valid tenant or show a safe tenant-selection/recovery state.
- Pending sync should be handled before, during, or after switching according to API policy; local queues must remain tenant-scoped.
- Tenant switching should clearly show which workspace is active and which state is pending.

User movement:

- **Authenticated to tenant switching**: user selects a tenant or API requires selection.
- **Tenant switching to authenticated**: API validates the new active tenant and returns resolved context.
- **Tenant switching to offline**: connectivity fails before validation; the previous tenant remains active where safe, or shell shows tenant-switch retry.
- **Tenant switching to maintenance or feature-disabled**: target tenant is maintenance-limited or lacks required features.
- **Tenant switching to welcome**: no tenant remains available, invitation is invalid, or session authority fails.

## Sync-In-Progress State

Sync-in-progress state explains work moving between local and server authority.

Entry conditions:

- Bootstrap context is refreshing.
- Feature, config, version, permission, or user context is refreshing.
- Local drafts are saving locally.
- Queued intents are replaying through API.
- Uploads, downloads, record refresh, conflict checks, or retry operations are running.
- Tenant switch or app resume triggers queue/freshness validation.

Principles:

- Sync-in-progress should not hide whether work is local, queued, pending, accepted, failed, conflicted, or blocked.
- Users should know what is being synced and whether they can continue working.
- Duplicate submissions should be prevented.
- High-risk or tenant-switch-sensitive sync should block navigation where policy requires it.
- Background sync should remain calm and visible only where user action or risk is affected.
- Failure should become retry, failed, conflict, blocked, permission-denied, maintenance, or support-guided state.
- Final acceptance belongs to API, not the local queue.

User movement:

- **Authenticated or offline to sync-in-progress**: queued or refresh work starts.
- **Sync-in-progress to authenticated**: work completes, API accepts results, and no higher-priority state applies.
- **Sync-in-progress to offline**: network disappears or API becomes unreachable.
- **Sync-in-progress to feature-disabled**: API rejects a replay because feature, plan, tenant, role, version, or maintenance policy changed.
- **Sync-in-progress to maintenance or forced update**: API reports a higher-priority block during sync.
- **Sync-in-progress to conflict/recovery flow**: API detects conflict, stale state, duplicate, validation failure, or manual resolution requirement.

## Permission-Blocked State

Permission-blocked state explains native capability limits.

Entry conditions:

- Camera, scanner, microphone, geolocation, push notification, biometrics, secure storage, file, share, browser, or system-settings capability is denied, restricted, unavailable, unsupported, or not installed.
- NativePHP reports permission denied or missing capability.
- Admin/API says the feature is enabled, but device permission or device support prevents local execution.
- User declines an OS permission prompt.

Principles:

- Permission education should happen before native permission prompts.
- Permission-blocked state should name the product reason, not just the OS permission name.
- The shell should offer fallback when the feature supports it.
- The shell may offer settings recovery where NativePHP and platform behavior allow it.
- The shell should not repeatedly pressure users after denial.
- Native permission grant does not grant SaaS permission, role permission, billing entitlement, tenant access, or feature availability.
- Permission-blocked state should remain feature-level unless the missing permission blocks the whole app.

User movement:

- **Authenticated to permission-blocked**: user opens a feature that needs denied, unavailable, or unsupported native capability.
- **Permission-blocked to authenticated feature flow**: user grants permission, device supports the capability, and API feature authority remains valid.
- **Permission-blocked to feature-disabled**: API disables or blocks the feature while permission is unresolved.
- **Permission-blocked to offline**: capability needs API confirmation and the device is offline.
- **Permission-blocked to settings/retry/fallback**: user opens device settings, retries prompt, or chooses an alternate allowed workflow.

## Feature-Disabled State

Feature-disabled state explains server-controlled feature unavailability.

Entry conditions:

- API resolves a feature as hidden, disabled, blocked, deprecated, update-required, offline-limited, read-only, plan-limited, role-limited, tenant-limited, maintenance-limited, device-limited, or emergency-disabled.
- Feature flag, remote config, billing entitlement, app-version policy, role permission, tenant status, or maintenance state changes.
- Cached feature state is stale and revalidation says the feature cannot continue.

Principles:

- Feature-disabled state comes from API-resolved outcomes, not local guesses.
- Hidden features disappear where users have no useful reason to know they exist.
- Disabled or blocked features should explain a safe reason category when the user expects access.
- Disabled features should not leave broken navigation, blank screens, dead buttons, or ambiguous spinners.
- Feature-disabled state should avoid raw flag names, billing internals, rollout cohorts, permission matrices, or tenant config internals.
- Local drafts tied to disabled features should be preserved, made read-only, discarded, or support-routed only according to documented policy.

User movement:

- **Authenticated to feature-disabled**: user opens a feature that API says is unavailable.
- **Feature-disabled to authenticated feature flow**: API later enables the feature for the current tenant/user/version/context.
- **Feature-disabled to forced update**: disabled state is caused by outdated app version that requires update.
- **Feature-disabled to maintenance**: feature is temporarily unavailable because of scoped maintenance.
- **Feature-disabled to tenant switching**: user can access the feature only in another allowed tenant.
- **Feature-disabled to permission-blocked**: feature is allowed by API but blocked by native capability status.

## App Launch And Resume Flow

The shell should treat launch, resume, tenant switch, login, reconnect, and sync replay as state-evaluation moments.

Principles:

- On launch, check whether any local session exists without exposing private data too early.
- Check app-version and maintenance policy before normal authenticated navigation when possible.
- Recover secure local session only through safe local storage and API revalidation.
- Present locked state before private data if local lock policy requires it.
- Present offline or last-known state when network/API is unavailable.
- Refresh bootstrap context before trusting cached tenant, feature, permission, config, sync, support, or billing state for protected actions.
- On app resume, re-evaluate lock, network, version, maintenance, session, tenant, and sync conditions.
- On reconnect, revalidate queued work before replay.

## NativePHP And Livewire Shell Boundaries

NativePHP and Livewire should support shell states without becoming separate authorities.

Principles:

- NativePHP may provide network status, secure storage, biometric prompts, app settings, native permissions, device information, and native events.
- Livewire may present loading, saving, syncing, retrying, validation, disabled, and error states.
- Secure storage may hold small secrets such as tokens; local SQLite/cache should not hold secrets.
- Native biometric/local unlock protects the device experience; API still owns server session validity.
- Network status is a signal, not proof that API is reachable or that cached data is current.
- Native permission status is a device fact, not SaaS permission authority.
- Native events should become shell/user states such as granted, denied, unavailable, pending, completed, failed, or cancelled.
- Shell state copy and navigation should stay simple, mobile-first, and thumb-friendly.

## Transition Risk Register

| Risk | Shell principle |
| --- | --- |
| Stale authenticated state grants access | Revalidate through API before protected work and when app resumes or reconnects. |
| Offline mode hides unsynced work | Show draft, queued, pending, failed, conflict, and synced states separately. |
| Locked state is confused with authentication | Explain locked as local protection and revalidate API authority when needed. |
| Forced update loses user work | Preserve safe local drafts and block only unsafe protected work. |
| Maintenance appears as a crash | Show scoped maintenance, retry guidance, limited mode, and support path. |
| Tenant switch leaks data | Keep tenant-scoped cache, queues, drafts, permissions, and config isolated. |
| Sync feels stuck | Show what is syncing, what can continue, what failed, and what needs action. |
| Permission denial dead-ends a workflow | Educate before prompts, offer fallback/settings/retry where safe. |
| Feature-disabled state exposes admin internals | Use safe reason categories and resolved API outcomes. |
| Native capability bypasses API | Treat native results as local inputs that still require API validation and acceptance. |

## Success Test

Mobile app shell logic is successful when a mobile user can understand the current state and next safe action without knowing internal admin policy.

Before implementation, every shell state should answer:

- What caused this state?
- Is this state controlled by API, NativePHP, local cache, local security policy, or the user?
- What private data is safe to show?
- What actions are allowed now?
- What actions are blocked and why?
- What happens if the app goes offline?
- What happens if the app comes back online?
- What happens if the user changes tenant?
- What happens if sync is pending?
- What happens if the app version becomes unsupported?
- What is the safest exit path?

If a shell state cannot answer those questions, it is not ready for implementation planning.
