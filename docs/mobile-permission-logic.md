# Mobile Permission Logic

Updated: 2026-06-26

This document defines mobile permission logic for the Mobile Lara NativePHP client. It explains why the app should explain permissions before asking, which features need camera, microphone, location, notifications, files, scanner, biometrics, and secure storage, how disabled features should avoid requesting permissions, how users recover from denied permissions, how admin feature flags affect permissions, and how permission status should appear in settings. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, NativePHP plugins, policies, jobs, services, providers, local storage schemas, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX Principles](mobile-ux-principles.md), [Mobile App Shell Logic](mobile-app-shell-logic.md), [Mobile Dashboard Logic](mobile-dashboard-logic.md), [Mobile Settings Logic](mobile-settings-logic.md), [Authentication Principles](authentication-principles.md), [API-First Principles](api-first-principles.md), [Documentation-First Architecture](documentation-first-architecture.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md), [Admin Safety Principles](admin-safety-principles.md), [Mobile And Admin Design System](design-system.md), [NativePHP Local Storage](nativephp-local-storage.md), and [NativePHP Run Notes](nativephp-run.md): permissions are the user's trust boundary between native device capability, local app behavior, and Admin/API-controlled feature eligibility.

Authentication Principles are defined in [Authentication Principles](authentication-principles.md): mobile login must happen through the API only; access and refresh tokens must use secure storage; refresh, logout, logout-all-devices, tenant selection, session expiry, offline already-authenticated behavior, and server revocation must preserve Admin/API authority before implementation.

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

## Permission Statement

Mobile permissions should be requested only when the user understands the purpose and the feature is actually available.

The app should never ask for every permission at first launch. It should never ask for a permission because a feature exists in code, because a plugin is installed, because an admin might enable it later, or because a dashboard shortcut is visible. Permission prompts should be tied to a user-visible action, an enabled feature, a clear business purpose, and a safe fallback.

Product rule: native permission status is local device state. SaaS permission, tenant authority, feature availability, subscription entitlement, app-version eligibility, and device trust are Admin/API decisions. The mobile client may explain, request, retry, recover, and display native permission status. It must not use native permission status as business authorization.

Permission logic should answer:

- Why does the app need this permission now?
- Which feature needs it?
- Is the feature enabled by admin/API, feature flag, subscription, tenant status, and app version?
- Is there a useful fallback if the user denies it?
- What happens if the permission is denied, permanently denied, unavailable, unsupported, or blocked by policy?
- How does the user recover later from settings?

## Explain Before Asking

The app should explain permissions before asking because native permission prompts are high-trust moments.

Principles:

- Explain the business purpose before the OS prompt appears.
- Ask just in time, when the user starts a feature that needs the capability.
- Use plain language tied to the user's task, not implementation details.
- Explain what will happen if the user declines.
- Offer a fallback when one exists.
- Do not pressure the user with vague claims such as "required for the app" when only one feature needs the permission.
- Do not request permission for disabled, hidden, unlicensed, tenant-blocked, app-version-blocked, maintenance-blocked, or unsupported features.
- Do not confuse device permission with SaaS permission. A user can grant camera access and still lack permission to use a camera-based tenant workflow.

The pre-permission explanation should be controlled enough for consistency and safe enough for remote updates. Remote config may tune wording, support links, and fallback copy. Admin/API and feature flags decide whether the feature is eligible to request the permission.

## Permission Contract

Every permission-dependent feature should define the permission need before implementation.

| Capability | Permission type | Common feature need | Authority split | User-facing outcome |
| --- | --- | --- | --- | --- |
| Camera | Native device permission and device capability. | Capture photos, attach evidence, scan visual documents, record images for workflow proof. | Device grants camera access; API decides whether the feature is allowed. | Ask only inside enabled camera workflows and show fallback/upload/manual option where allowed. |
| Microphone | Native device permission and device capability. | Record voice notes, audio evidence, dictation, support recordings, inspection notes. | Device grants microphone access; API decides whether audio features are allowed. | Ask before recording and show no-audio/manual-note fallback where allowed. |
| Location | Native device permission, precision capability, and policy sensitivity. | Check-in, route context, nearby work, location-stamped records, field verification. | Device grants location access; API decides whether location is required, optional, or disabled. | Explain location scope, precision, and fallback before asking. |
| Notifications | Native notification permission plus server delivery policy. | Push alerts, work assignment updates, sync failures, announcements, support replies, billing/tenant notices. | Device grants push display/token access; API decides targeting, category eligibility, and delivery. | Ask only when notification feature is enabled and show in-app fallback if push is denied. |
| Files | Native file/storage access and local file handling. | Attach files, downloads, exports, document upload, offline packages, support attachments. | Device grants file access where required; API decides allowed file actions and retention. | Ask at file action time and explain data handling/data-loss risks. |
| Scanner | Native scanner/camera capability and plugin availability. | QR/barcode scan for assets, tickets, tenant records, inventory, check-in, or verification. | Device/plugin/camera enable scanning; API decides whether scan workflow is allowed. | Ask only when scanner feature is enabled and provide manual entry fallback where allowed. |
| Biometrics | Native biometric/system credential capability. | Unlock app, confirm sensitive action, protect local session, re-authenticate. | Device confirms local identity; API decides whether biometric/app-lock is required, optional, or disabled. | Explain local security purpose and provide password/session fallback where policy allows. |
| Secure storage | Native secure storage capability, usually no user prompt. | Store auth tokens, refresh tokens, device keys, small sensitive app secrets. | Device provides secure storage; API controls token/session validity. | Treat absence/failure as security-blocking and route to safe logout/support. |

This contract is intentionally principle-level. It does not create permissions APIs, models, tables, fields, widgets, Livewire components, NativePHP events, routes, controllers, policies, jobs, services, notifications, storage records, or plugin manifests.

## Capability-To-Feature Logic

Permission requests should map to explicit feature behavior.

### Camera

Camera may be needed for:

- Photo capture.
- Evidence attachment.
- Visual inspection.
- Document/photo upload from camera.
- Profile or record image capture.
- Camera-assisted scanner workflows where scanner uses camera hardware.

Camera should not be requested when:

- The camera feature flag is disabled.
- The user lacks role or record permission.
- The tenant is suspended, read-only, maintenance-limited, or billing-blocked.
- The app version is blocked from camera workflows.
- The device has no usable camera or the plugin is unavailable.
- Upload/manual-entry fallback is the only enabled workflow.

Settings should show camera status as available, granted, denied, permanently denied, unavailable, unsupported, disabled by admin, blocked by plan, blocked by tenant, or not needed.

### Microphone

Microphone may be needed for:

- Voice notes.
- Audio evidence.
- Support audio attachments.
- Dictation or spoken workflow notes.
- Field reports with audio context.

Microphone should not be requested when:

- Audio features are disabled by feature flag or plan.
- The user can only submit text/manual notes.
- Tenant policy forbids audio capture.
- The device has no microphone or the plugin is unavailable.
- The user is offline and the workflow cannot safely queue audio.

Settings should show microphone status and explain whether audio capture is optional, required for a specific enabled feature, disabled, or recoverable through system settings.

### Location

Location may be needed for:

- Field check-in or check-out.
- Proximity-based work.
- Location-stamped records.
- Route/worksite context.
- Tenant-specific attendance, service, inspection, or delivery workflows.
- Support diagnostics only when policy explicitly permits it.

Location should not be requested when:

- The location feature is disabled.
- The user is not in a location-aware role or workflow.
- Tenant policy disables location collection.
- Subscription or app version blocks location features.
- The device cannot provide location.
- The feature can proceed without location and the user has not chosen a location-dependent action.

Location explanations should name why location is needed, whether approximate or precise location is expected, whether it is required or optional, and what fallback exists.

Settings should show location status as granted, denied, permanently denied, approximate/limited where relevant, unavailable, disabled by admin, or not needed.

### Notifications

Notifications may be needed for:

- Work assignment alerts.
- Sync failure or conflict alerts.
- Important announcements.
- Support replies.
- Tenant maintenance notices.
- Billing or plan notices where appropriate for the role.
- Security/session warnings.

Notifications should not be requested when:

- Push notifications are disabled by admin/API.
- The tenant plan does not include push delivery.
- The user role cannot receive push categories.
- Notification targeting is not configured.
- The app is in a blocked/outdated state where push registration should wait.
- The user only has in-app notifications enabled.

Push permission does not decide notification authority. The API controls targeting and categories. The device permission only controls whether push can be displayed and whether a push token can be registered.

Settings should show push permission, in-app notification availability, registration health, category eligibility, and recovery guidance without exposing raw push tokens.

### Files

Files may be needed for:

- Attachments.
- Downloads.
- Exports.
- Support files.
- Offline packages.
- Documents or media captured outside the app.
- Sharing allowed files through native share behavior.

Files should not be requested when:

- Attachment/export/download features are disabled.
- The user lacks record, report, export, or support permission.
- Tenant, subscription, or app-version policy blocks file operations.
- The action is read-only and no file access is needed.
- The device cannot provide the expected file access.

File permission explanations should include data handling, allowed file types, size or sensitivity expectations when relevant, and whether files remain local, upload to API, or attach to support.

Settings should show file capability status, local storage health, download/cache state, and whether file actions are disabled by policy.

### Scanner

Scanner may be needed for:

- QR code scanning.
- Barcode scanning.
- Asset lookup.
- Ticket validation.
- Tenant record lookup.
- Inventory or field workflow identification.
- Check-in or verification flows.

Scanner should not be requested when:

- Scanner feature flag is disabled.
- The scanner plugin/capability is unavailable.
- The camera permission is denied and no scanner fallback exists.
- The user lacks permission for scan results.
- The tenant or subscription does not allow scanner workflows.
- Manual entry is the only enabled path.

Scanner status should explain scanner availability separately from camera availability. If scanner depends on camera, settings should show both the scanner feature state and camera permission state.

### Biometrics

Biometrics may be needed for:

- App unlock.
- Re-authentication after idle lock.
- Local session protection.
- Sensitive local action confirmation.
- API-required step-up security where the API allows biometrics as a local factor.

Biometrics should not be requested when:

- Biometric/app-lock policy is disabled.
- The device has no biometric/system credential support.
- The user has not enabled local app lock and policy does not require it.
- The current action does not require local re-authentication.
- The server requires password/MFA instead of local biometric confirmation.

Biometric success is not API authorization. It is a local proof that the current device user passed an OS-backed prompt. Server-side authority must still come from API/session policy.

Settings should show biometric availability, enabled/disabled state, required/optional policy, fallback path, and recovery guidance.

### Secure Storage

Secure storage may be needed for:

- Access tokens.
- Refresh tokens.
- Device-local secrets.
- Per-device encryption material.
- Small sensitive session values.

Secure storage usually should not be presented as a normal permission prompt because it is a device capability, not a user-facing OS prompt in the same way as camera or location.

Secure storage should be treated as required for authenticated mobile operation. If secure storage is unavailable, unhealthy, or inaccessible, the app should limit authenticated behavior, avoid storing secrets in local SQLite, and route to safe logout, support, or re-authentication.

Settings should show secure storage health only as a safe summary. It must never expose token values, key names that reveal secrets, raw secure-storage payloads, or internal encryption details.

## Disabled Feature Rules

Disabled features should avoid requesting permissions.

Principles:

- If a feature is disabled by global flag, tenant flag, user flag, role permission, subscription, tenant status, maintenance mode, app version, device capability, or remote emergency rule, the app should not ask for its native permission.
- Hidden features should not show permission prompts.
- Disabled features may show an explanation only when the user expects access or needs recovery context.
- Permission requests should happen after feature eligibility is resolved through API/bootstrap/config state.
- Local cached feature state may explain last-known availability while offline, but should not request new permissions for protected actions that need API confirmation.
- Pre-permission copy should not promise a feature will work until API authority, native permission, and device capability are all satisfied.

Disabled features should appear as:

- Hidden when irrelevant.
- Disabled with reason when the user expects access.
- Contact admin when access depends on tenant/admin choice.
- Update app when app version blocks safe behavior.
- Open settings/retry when native permission is the only blocker.
- Use fallback when a safe manual/offline path exists.

## Denied Permission Recovery

Denied permissions should not dead-end the user.

Recovery principles:

- Explain what is blocked and why.
- Show whether the denial is temporary, permanent, restricted, unsupported, or policy-blocked.
- Offer retry only when the operating system allows another prompt.
- Offer open-app-settings recovery when supported and appropriate.
- Offer fallback flows such as manual entry, attach later, text note, in-app notification, or contact support when available.
- Avoid repeated prompts after denial.
- Do not nag users for permissions unrelated to their current work.
- Do not treat a denied native permission as a SaaS permission failure.

Recovery paths by status:

| Status | User experience |
| --- | --- |
| Not determined | Explain before asking; prompt only when eligible feature is started. |
| Granted | Allow the native part of the eligible feature, then still rely on API authorization for business action. |
| Denied | Explain impact, offer retry if OS allows, show fallback if available. |
| Permanently denied | Explain impact, offer open app settings if supported, show fallback/contact path. |
| Restricted | Explain that the device or OS policy prevents use; show fallback/support path. |
| Unavailable | Explain missing device capability or plugin; hide or disable dependent feature. |
| Disabled by admin/API | Do not request native permission; show admin/API-controlled reason where useful. |
| Blocked by subscription/tenant/app version | Do not request native permission; show plan, tenant, support, or update path. |

Permission recovery should be available from the relevant feature and from settings. Settings should provide a stable place to understand permission state without forcing a new prompt.

## Feature Flag And Admin Control Logic

Admin feature flags affect permission requests because they decide whether a permission-dependent feature should exist for the user at all.

Feature flag principles:

- Feature flags decide whether a permission-dependent feature is visible, enabled, beta, disabled, deprecated, emergency-blocked, or update-required.
- Permission prompts should only be possible after feature flag, permission, tenant, subscription, app-version, device, and remote-config checks allow the feature.
- Global disabled and emergency-disabled states win over tenant/user enablement.
- Tenant flags can enable a feature only inside global, plan, version, permission, and safety limits.
- User-level flags can personalize rollout but cannot bypass tenant or global policy.
- Remote config can change explanation copy, ordering, fallback labels, and settings display, but not grant authorization.
- Admins should understand permission impact before enabling a feature that triggers native prompts.

Admin impact preview should describe:

- Which native permissions may be requested.
- Which mobile users or tenants may see the permission education screen.
- Which disabled or fallback states users will see.
- Whether app-store privacy disclosures or build-time permission declarations may be affected.
- Whether support documentation needs updates.
- Whether old app versions can safely handle the feature.

## Permission Status In Settings

Permission status should appear in settings as a clear, non-technical summary.

Settings should show:

- Permission/capability name.
- Current device status.
- Feature dependency.
- Admin/API state.
- Whether the permission is needed now.
- Recovery action when available.
- Fallback action when available.
- Last checked or last-known label where useful.

Recommended status vocabulary:

| Status | Meaning |
| --- | --- |
| Not needed | No enabled feature currently requires this permission. |
| Available | Device capability exists, but permission has not been requested or is not needed yet. |
| Ready | Permission/capability is available for enabled features. |
| Needs permission | Enabled feature requires the user to grant the permission before use. |
| Denied | User denied permission; retry or fallback may be available. |
| Open settings | OS requires recovery through app settings. |
| Restricted | OS/device policy prevents access. |
| Unavailable | Device, platform, plugin, or build does not support the capability. |
| Disabled by admin | Admin/API or feature flag disables the dependent feature. |
| Blocked by plan | Subscription/plan does not allow the dependent feature. |
| Blocked by tenant | Tenant status or tenant policy disables the dependent feature. |
| Update required | App version cannot safely use the dependent feature. |
| Offline limited | Cached status is shown, but API-controlled feature eligibility cannot be refreshed. |

Settings should not show:

- Raw permission API payloads.
- Push tokens.
- Secure storage keys or values.
- Internal feature flag keys.
- Rollout cohort IDs.
- Permission matrices.
- Tenant/private diagnostic payloads.
- Native plugin internals unless support-safe.

## Offline Behavior

Offline permission behavior should be conservative.

Principles:

- Native permission status can be shown offline because it is local device state.
- API-controlled feature eligibility should be shown as last-known while offline.
- The app should not request new permission for an API-protected feature when it cannot confirm that the feature is still enabled and allowed.
- Permission recovery through OS settings may remain available while offline.
- Local-only permission explanations may remain available while offline.
- Push token registration, device trust updates, permission-dependent server actions, and support uploads should wait for network/API access.
- Local queued actions that depend on permissions must recheck current feature, tenant, subscription, version, and server authorization before replay.

Offline should not relax permission policy. It should make the boundary visible: "your device status is known locally; your SaaS feature eligibility will be confirmed when online."

## Permission Boundaries

Mobile permission logic must never own:

- SaaS permission authority.
- Tenant authority.
- Billing or subscription authority.
- Feature flag authority.
- Remote config authority.
- Notification targeting authority.
- Device trust authority.
- API authorization.
- Sync replay acceptance.
- Audit truth.
- Legal/privacy disclosure truth.

Mobile permission logic may own:

- Pre-permission education.
- Native permission prompt timing.
- Native status presentation.
- OS settings recovery guidance.
- Local fallback presentation.
- Device capability explanation.
- Local loading, retry, denied, unavailable, and blocked states.
- User-friendly explanation of Admin/API outcomes.

## Risk Register

| Risk | Permission principle |
| --- | --- |
| User is asked too early | Ask just in time after feature eligibility is resolved. |
| Disabled feature still prompts | Feature flag/API state must be checked before native prompt. |
| OS permission is mistaken for authorization | Treat native status as local device state only. |
| Denied permission creates dead end | Provide retry, settings recovery, fallback, or support path. |
| Repeated prompts annoy users | Avoid nagging and remember denial state locally. |
| Scanner and camera state are confused | Show scanner capability and camera permission separately. |
| Push permission is treated as notification policy | API owns targeting/categories; device permission owns display/token availability. |
| Secure storage failure leaks secrets | Never fall back to local SQLite for tokens; limit auth and route to safe recovery. |
| Feature rollout creates privacy surprise | Admin impact preview should mention permission prompts and privacy disclosures. |
| Offline state grants too much | Show last-known eligibility but revalidate before protected use or replay. |

## Success Test

Mobile permission logic is successful when a mobile user can:

- Understand why the app needs a permission before the OS prompt.
- See which feature needs the permission.
- Decline without losing unrelated app access.
- Recover from denial through retry, settings, fallback, or support.
- See permission status in settings without raw technical internals.
- Trust that disabled features do not ask for unrelated permissions.
- Understand the difference between device permission and admin/API authorization.

Before implementation, every permission-dependent feature should answer:

- Which native capability does this feature need?
- Is the feature enabled by global, tenant, user, role, plan, app-version, and device checks?
- What pre-permission explanation appears?
- What happens if permission is denied?
- What fallback exists?
- What appears in settings?
- What is disabled offline?
- What admin impact should be previewed before rollout?
- What must never be requested, shown, stored, or logged?

If a permission-dependent feature cannot answer those questions, it is not ready for implementation planning.
