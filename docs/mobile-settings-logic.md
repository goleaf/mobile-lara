# Mobile Settings Logic

Updated: 2026-06-26

This document defines mobile settings logic for the Mobile Lara NativePHP client. It explains how settings should be grouped into account, tenant, security, notifications, sync, appearance, permissions, storage, support, legal, and diagnostics. It also explains what each section does, what is controlled locally, what is controlled by Admin/API, and what should be disabled when the mobile client is offline. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, NativePHP plugins, policies, jobs, services, providers, local storage schemas, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX Principles](mobile-ux-principles.md), [Mobile App Shell Logic](mobile-app-shell-logic.md), [Mobile Dashboard Logic](mobile-dashboard-logic.md), [Mobile Permission Logic](mobile-permission-logic.md), [API-First Principles](api-first-principles.md), [Documentation-First Architecture](documentation-first-architecture.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md), [Admin Safety Principles](admin-safety-principles.md), [Mobile And Admin Design System](design-system.md), [NativePHP Local Storage](nativephp-local-storage.md), and [NativePHP Run Notes](nativephp-run.md): settings are the user's controlled surface for personal preferences, device state, local cache, permission recovery, support access, legal information, and diagnostics, while Admin/API remains authoritative for tenant rules, account authority, security policy, notifications policy, sync policy, feature availability, subscription limits, and support visibility.

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

## Settings Statement

Mobile settings should help the user understand and adjust the app without exposing admin complexity.

Settings should not become an admin panel. They should not expose raw feature flags, billing internals, rollout cohorts, permission matrices, server config, tenant policy internals, API tokens, storage paths, or diagnostic payloads that could leak sensitive data.

Product rule: settings are split into local device controls, API-backed user preferences, and admin-controlled policy displays. The mobile client may present local toggles, device permission recovery, cache actions, display preferences, and safe support/legal/diagnostic information. It must not decide account authority, tenant authority, billing entitlement, notification targeting, security requirements, sync acceptance, feature enablement, or global configuration.

Settings should answer:

- What can this user safely adjust on this device?
- What is controlled by their tenant or platform admin?
- What is currently unavailable because the app is offline?
- What requires API confirmation before changing?
- What is a local preference and what is a server policy?
- What support, legal, and diagnostic information is safe to expose?

## Settings Group Contract

Every settings group should have a purpose, authority source, local behavior, API/admin behavior, offline behavior, and support path before implementation.

| Group | Purpose | Local control | Admin/API control | Offline behavior |
| --- | --- | --- | --- | --- |
| Account | Show and manage safe personal account context. | Cached display, local session state, device-local sign-out confirmation, app lock prompt state. | User identity, email, profile truth, invitation/suspension state, account changes, session revocation. | Read last-known profile; disable server-changing actions. |
| Tenant | Show current tenant/workspace and tenant switch context. | Last-known active tenant display, local switcher state, tenant-specific cache separation. | Tenant membership, tenant status, tenant switching eligibility, tenant settings, plan limits. | Show last-known tenant; disable tenant switching and tenant-setting changes. |
| Security | Explain and recover secure access state. | Device lock/app lock preference, biometric prompt availability, secure storage presence, local logout confirmation. | Password/MFA/session policy, device trust, token issuance/revocation, security enforcement. | Allow local lock/logout where safe; disable server security changes. |
| Notifications | Manage notification visibility and delivery preferences. | Device permission status, push prompt education, local notification display preferences. | Notification eligibility, categories, unread truth, delivery targeting, push token registration, tenant notification policy. | Show cached preferences; disable preference saves and token registration. |
| Sync | Show and tune safe sync behavior. | Local queue visibility, last sync display, manual retry intent, metered-network preference if allowed. | Sync policy, conflict decisions, replay acceptance, server freshness, feature-specific sync enablement. | Show local queue/cache status; disable server refresh and online-only replay. |
| Appearance | Personalize safe presentation. | Theme, text density, language display preference where allowed, reduced motion, local layout preferences. | Tenant branding, supported languages, feature visibility, remote-configured layout limits. | Allow local-only appearance changes; disable server-saved appearance changes. |
| Permissions | Explain native and SaaS permission state. | Native permission status, pre-prompt education, system settings recovery, local device capability state. | Role permissions, feature eligibility, device trust, subscription limits, tenant policy. | Show native status; disable API permission refresh and protected feature changes. |
| Storage | Manage local cache, drafts, downloads, and storage health. | Cache inspection summaries, clear cache, clear downloads, local draft warnings, storage usage estimates. | Server records, sync acceptance, retention policy, legal hold, remote wipe policy if defined. | Allow safe local cleanup; disable actions that need API confirmation. |
| Support | Reach help and expose safe support context. | Local logs summary, copied diagnostic summary, cached support links, offline support draft if allowed. | Support case creation, support routing, tenant support entitlement, support visibility, incident state. | Show cached help; allow local draft; disable ticket submission. |
| Legal | Show legal, privacy, terms, and compliance information. | Bundled legal copy, cached policy documents, local consent display state. | Current policy versions, required consent, tenant compliance notices, privacy settings authority. | Show bundled/cached legal documents; disable new consent/privacy changes. |
| Diagnostics | Show safe technical state for troubleshooting. | App version, device platform, network status, cache freshness, local queue counts, permission status. | API status, server config version, feature/config/version decisions, support-visible diagnostics. | Show local diagnostics; disable upload/share-to-support actions. |

This contract is intentionally principle-level. It does not create settings APIs, models, tables, fields, widgets, Livewire components, NativePHP events, routes, controllers, policies, jobs, services, notifications, storage records, or diagnostics payloads.

## Settings Priority Model

Settings should be organized for quick recovery first and configuration second.

Suggested priority order:

1. **Blocking states** - forced update, maintenance, locked session, suspended tenant, revoked account, or disabled app version should limit settings to safe support/legal/local actions.
2. **Account and tenant clarity** - the user should always know which account and tenant the settings apply to.
3. **Security and permissions** - recovery paths for app lock, biometric prompts, native permission denial, and server policy blocks should be easy to find.
4. **Sync and storage health** - local queue, drafts, cache, and storage status should be visible where they can affect data loss.
5. **Notifications and support** - communication preferences, unread state, push recovery, and support routing should be available without exposing admin internals.
6. **Appearance and legal** - low-risk local preferences and required legal information should be available but not distract from operational recovery.
7. **Diagnostics** - troubleshooting information should be safe, privacy-aware, and shaped for support rather than raw internals.

Remote config may reorder settings groups, hide optional groups, choose safe labels, or promote recovery actions. Remote config must not make unauthorized, unlicensed, disabled, tenant-blocked, offline-blocked, maintenance-blocked, or app-version-blocked settings editable.

## Account Settings

Account settings help the user understand who is signed in and manage safe personal account actions.

Account settings should show:

- Signed-in display name or safe profile label.
- Email or account identifier when policy allows.
- Account state: active, invited, suspended, locked, restricted, expired, support-assisted, or offline-limited.
- Profile edit entry point when allowed.
- Sign out, lock app, refresh account, or contact support actions where appropriate.
- Last-known account context while offline.

Controlled locally:

- Local display of cached account context.
- Local app-lock state and prompt timing if policy allows local control.
- Local sign-out confirmation and clearing local-only state after safe logout flow.
- Local UI state such as expanded sections, copied support reference, or profile screen navigation.

Controlled by Admin/API:

- Account identity, email, verified status, invitation status, suspension state, and active session validity.
- Whether profile fields can be edited.
- Whether password, MFA, device trust, account deletion, or session management is available.
- Whether the user should contact tenant admin, platform support, or billing/support team.

Disabled when offline:

- Profile edits that must save to API.
- Email/password/MFA/session changes.
- Account deletion or deactivation requests.
- Server-side sign-out from other devices.
- Refreshing account truth or invitation state.

Offline account settings may show last-known account information, but must clearly separate cached display from server-confirmed state.

## Tenant Settings

Tenant settings help the user understand the active tenant/workspace and tenant-scoped constraints.

Tenant settings should show:

- Active tenant name or safe workspace label.
- Tenant status: active, onboarding, limited, read-only, suspended, disabled, billing-limited, support-limited, or maintenance.
- Tenant switch entry point when allowed.
- Tenant-specific plan, feature, notification, sync, or support limitations only as safe user-facing summaries.
- Tenant admin contact route when policy allows.

Controlled locally:

- Last-known tenant display.
- Tenant switcher UI state.
- Tenant-specific cache separation, draft grouping, and queue labels.
- Local reminder that unsynced work belongs to the current tenant.

Controlled by Admin/API:

- Tenant membership, active tenant choice, switch eligibility, tenant status, tenant settings, plan posture, feature availability, support tier, and billing-limited outcomes.
- Whether the user can view tenant settings, invite users, manage devices, see reports, or contact tenant admin.
- Whether tenant switching is allowed while there are pending drafts or conflicts.

Disabled when offline:

- Tenant switching.
- Tenant membership refresh.
- Tenant settings changes.
- Invitation, member, device, or role actions.
- Tenant-scoped support entitlement refresh.

Offline tenant settings should show the last-known tenant and warn when pending work, cached data, or drafts are tenant-specific.

## Security Settings

Security settings explain and recover secure access behavior on the device.

Security settings should show:

- Local app lock status if available.
- Biometric availability and permission/enablement state where NativePHP capability exists.
- Secure session state such as authenticated, locked, expired, revoked, or offline-limited.
- Device trust or session policy summary when API provides it.
- Password/MFA/security contact routes when allowed.

Controlled locally:

- Local app lock toggle if admin policy allows user control.
- Biometric prompt preference if capability exists and policy allows it.
- Local lock now action.
- Local secure storage health signal, without exposing token values.
- Device-level permission/settings recovery guidance.

Controlled by Admin/API:

- Minimum security requirements.
- Token issuance, refresh, revocation, and expiration.
- MFA/password policy and recovery rules.
- Device trust, session validity, forced logout, and support-assisted access.
- Whether biometric/app lock is optional, required, or disabled.

Disabled when offline:

- Password changes.
- MFA enrollment or reset.
- Device trust changes.
- Session revocation on other devices.
- Security policy refresh.
- Support-assisted account recovery submission.

Offline security settings may allow local lock/logout and biometric retry where safe, but must not treat local success as server authorization.

## Notification Settings

Notification settings let the user understand and manage communication preferences without overriding tenant or platform policy.

Notification settings should show:

- In-app notification preference status where allowed.
- Push notification device permission status.
- Push token/registration attention state only as a safe summary.
- Notification categories the user can control.
- Tenant or plan limits that affect notifications.
- Link to notification center when allowed.

Controlled locally:

- Device permission education and recovery path.
- Local preference for display style, quiet presentation, or local grouping where allowed.
- Cached notification preference display.
- Native app-settings recovery action for denied permissions where platform behavior allows it.

Controlled by Admin/API:

- Notification eligibility, category availability, delivery targeting, unread truth, tenant notification policy, support/billing announcements, and push token registration.
- Whether notification preferences can be changed by this role.
- Whether push, in-app, email, or other channels are available under the plan.

Disabled when offline:

- Saving notification preferences.
- Registering or refreshing push tokens.
- Marking notification settings as server-confirmed.
- Fetching unread truth or categories.
- Enabling new server-delivered categories.

Offline notification settings may show cached choices and native permission status, but should make clear that delivery policy is server-controlled.

## Sync Settings

Sync settings help the user understand local work, freshness, and safe replay behavior.

Sync settings should show:

- Last successful sync time.
- Pending queue, draft, failed, retry, and conflict summaries.
- Manual sync/retry entry point when online and allowed.
- Metered or constrained network guidance where relevant.
- Feature-specific sync limits if API provides safe summaries.
- Storage or cache state when it affects sync.

Controlled locally:

- Local queue visibility.
- Draft and cache counts.
- Manual retry intent for pending local work.
- Local preference for Wi-Fi-only or low-data behavior if admin policy allows.
- Last-known sync display and device/network status.

Controlled by Admin/API:

- Sync policy, conflict rules, replay acceptance, record freshness, server-side canonical state, feature-specific sync enablement, tenant sync suspension, and subscription-limited sync behavior.
- Whether local work can be queued, replayed, discarded, retried, or resolved by the current user.

Disabled when offline:

- Manual online sync.
- Server refresh.
- Conflict resolution that needs API truth.
- Changing sync policy that must be saved server-side.
- Uploading diagnostics or queue snapshots to support.

Offline sync settings should focus on pending work, drafts, local queue safety, and reconnect guidance.

## Appearance Settings

Appearance settings control safe presentation preferences.

Appearance settings should show:

- Theme preference where allowed.
- Text size/density preference if supported.
- Reduced motion or animation preference.
- Language/locale display preference where allowed.
- Dashboard/settings section display preferences when remote config allows.

Controlled locally:

- Device-local theme.
- Local density or display preference.
- Reduced motion.
- Cached language preference for display while offline.
- Section expansion/collapse state.

Controlled by Admin/API:

- Tenant branding.
- Supported languages.
- Required legal language behavior.
- Remote-configured layout limits.
- Feature visibility, navigation shape, and settings sections that are not optional.
- Whether appearance preferences sync across devices.

Disabled when offline:

- Saving server-backed appearance preferences.
- Fetching tenant branding changes.
- Changing server-supported locale.
- Refreshing remote-configured layout.

Offline appearance settings may allow safe local-only presentation changes, but must not claim tenant branding or server preference changes have been saved.

## Permission Settings

Permission settings explain the difference between native device permissions and SaaS permissions.

Permission settings should show:

- Native permission status for camera, scanner, microphone, geolocation, push notifications, biometrics, file/storage access, network, share, browser, or system settings where relevant.
- Why a permission is needed before prompting.
- Retry or open app settings recovery where NativePHP and the operating system allow it.
- SaaS permission summary as API-safe role/feature explanations.
- Disabled feature explanations when permission, feature flag, tenant status, subscription, app version, or device capability blocks access.

Controlled locally:

- Native device permission status.
- Device capability detection.
- Pre-prompt education.
- Opening system app settings where supported.
- Local explanation of denied, permanently denied, unavailable, unsupported, or not installed capability state.

Controlled by Admin/API:

- Role permissions, feature eligibility, tenant policy, subscription entitlement, device trust, support visibility, app-version rules, and whether a native capability should be requested for the user.
- Whether a feature remains available without a native permission.

Disabled when offline:

- Refreshing SaaS permission truth.
- Requesting feature eligibility changes.
- Updating device trust or server-side device registration.
- Enabling protected features that require API confirmation.

Offline permission settings may show native device state and cached SaaS summaries, but cached SaaS permissions must not grant new authority.

## Storage Settings

Storage settings help users understand and manage local data without risking server truth.

Storage settings should show:

- Local cache size summary where safe.
- Draft count, pending queue count, failed item count, and conflict count.
- Downloaded/offline data summary where applicable.
- Clear cache, clear downloads, or clear local drafts actions only with data-loss explanation.
- Storage health warnings.
- Last server sync/freshness context.

Controlled locally:

- Cache cleanup.
- Download cleanup.
- Local temporary file cleanup.
- Draft cleanup only with explicit warning and policy-safe behavior.
- Storage usage estimates and local data health.

Controlled by Admin/API:

- Server records, accepted sync state, retention rules, legal hold, tenant policy, remote wipe policy if defined, and whether local drafts or queues may be discarded.
- Whether certain data must be retained until sync, support review, or legal/compliance review.

Disabled when offline:

- Server-side data deletion.
- Confirming queue acceptance or discard with API.
- Remote wipe acknowledgment.
- Fetching current retention/legal-hold policy.
- Uploading storage diagnostics.

Offline storage settings may allow safe cleanup of local cache/downloads, but any destructive action touching drafts, queue, or unsynced records should be blocked or require stronger confirmation.

## Support Settings

Support settings help the user get help without exposing private or admin-only data.

Support settings should show:

- Support contact route.
- Tenant support entitlement or contact-admin state.
- Support ticket entry point when allowed.
- Incident or maintenance guidance.
- Safe diagnostic summary for the user to share.
- Offline support draft behavior if allowed.

Controlled locally:

- Cached support links.
- Local support draft.
- Copyable safe diagnostic summary.
- Local log summary without secrets or sensitive payloads.
- Device/app status visible to the user.

Controlled by Admin/API:

- Support eligibility, support routing, ticket creation, support case visibility, incident state, tenant support tier, billing/support ownership, and whether diagnostics can be uploaded.
- Whether support agents can view tenant/device diagnostics.

Disabled when offline:

- Ticket submission.
- Support conversation refresh.
- Diagnostic upload.
- Incident status refresh.
- Support entitlement refresh.

Offline support settings may let users draft a support request, view cached guidance, copy safe diagnostics, or contact a local/tenant admin through already-known information.

## Legal Settings

Legal settings show required policy and compliance information.

Legal settings should show:

- Terms of service.
- Privacy policy.
- License/open-source notices where applicable.
- Data usage and offline storage explanation.
- Consent status when policy requires it.
- Tenant-specific legal/compliance notices where allowed.

Controlled locally:

- Bundled legal copy.
- Cached legal policy display.
- Local acknowledgement display state until server confirmation is available.
- App version and policy version labels if safely available.

Controlled by Admin/API:

- Current legal policy versions, required consent, privacy preference authority, tenant compliance notices, data processing policy, and whether the user must accept a new policy before continuing.
- Whether privacy settings are user-editable, tenant-controlled, or platform-controlled.

Disabled when offline:

- Accepting new server-required legal terms.
- Updating privacy preferences.
- Fetching current policy versions.
- Submitting data requests.
- Revoking or changing server-held consent.

Offline legal settings may show bundled/cached documents, but required consent changes should wait for API confirmation unless policy explicitly supports offline acknowledgement with later replay.

## Diagnostic Settings

Diagnostic settings help the user and support understand app health without leaking secrets.

Diagnostic settings should show:

- App version and build label.
- API environment label only if safe.
- Device platform, OS version, and NativePHP capability status where useful.
- Network status, API reachability, metered/constrained state, and last successful API contact.
- Last bootstrap/config/version/sync freshness.
- Local queue, draft, failed, and conflict summaries.
- Feature/config/version decision summaries without raw secrets or sensitive payloads.

Controlled locally:

- Device info summary.
- Native capability status.
- Network status.
- Local cache/queue/sync freshness summaries.
- Copied diagnostic text with sensitive values removed.

Controlled by Admin/API:

- Server health, API status, support-visible diagnostics, feature/config/version decisions, tenant support scope, and whether diagnostic upload is allowed.
- Which diagnostics are visible to the user, tenant admin, support agent, or platform operator.

Disabled when offline:

- Diagnostic upload.
- API health refresh.
- Server config/version validation.
- Support case attachment.
- Remote log submission.

Offline diagnostic settings should remain useful for local troubleshooting, but must clearly label API data as last-known and must never expose tokens, secrets, raw request bodies, private tenant payloads, or unredacted logs.

## Offline Settings Rules

Offline settings should be honest, calm, and conservative.

General offline rules:

- Local-only appearance changes may remain enabled.
- Local lock/logout actions may remain enabled where safe.
- Native permission status may be shown because it is local device state.
- Opening system app settings may remain enabled where supported.
- Cache/download cleanup may remain enabled when it cannot destroy unsynced work.
- Server-changing saves should be disabled.
- Server-backed preferences should show last-known state.
- Tenant switching should be disabled.
- Account, security, notification, sync, support, legal, and diagnostics actions that need API confirmation should be disabled.
- Any local draft or queued setting change must be labeled pending and revalidated before it is treated as accepted.

Offline mode must not relax admin policy. Cached permissions, cached feature flags, cached tenant status, cached subscription state, cached notification policy, or cached security rules cannot grant new access after API revalidation says otherwise.

## Settings Change Matrix

Settings should change predictably as product state changes.

| Condition | Settings behavior |
| --- | --- |
| User has no permission | Hide if irrelevant; disable with safe reason if expected; never rely on UI hiding as authorization. |
| Feature flag disabled | Hide group/action or show disabled state with reason category and support path. |
| Tenant active | Show normal tenant-scoped settings available to this role. |
| Tenant onboarding/limited | Show setup or limited-work guidance and only safe editable settings. |
| Tenant suspended/disabled | Replace tenant-scoped settings with support/contact-admin behavior. |
| Tenant maintenance | Allow local/settings recovery and legal/support views; disable server-changing actions. |
| Subscription active | Show entitled settings and normal plan-limited context. |
| Subscription trial/grace | Show allowed settings plus safe notice when action is needed. |
| Subscription overdue/blocked | Disable plan-limited settings and route to contact-admin/support. |
| App version outdated | Disable settings that depend on unsupported API behavior; show update path. |
| Remote config missing/invalid | Use safe default section order and do not enable new settings. |
| Offline | Show cached/local state; disable server-changing actions; label last-known data. |
| Native permission denied | Explain, provide retry/settings recovery where safe, and keep SaaS authority separate. |
| Secure storage unhealthy | Limit account/security actions, protect tokens, and route to support or safe logout. |
| Sync conflict exists | Surface sync settings and conflict path before allowing destructive storage cleanup. |

## Settings Boundaries

Mobile settings must never own:

- Account authority.
- Tenant authority.
- Billing or subscription authority.
- Role or permission authority.
- Feature flag authority.
- Remote config authority.
- Security policy authority.
- Notification targeting authority.
- Sync acceptance or conflict decision authority.
- Legal policy truth.
- Support entitlement authority.
- Audit truth.

Mobile settings may own:

- Local preference presentation.
- Device permission education and recovery.
- Local app lock affordance within API policy.
- Local cache/download cleanup where safe.
- Local diagnostics display and redacted diagnostic copy.
- Local loading, pending, stale, offline, disabled, and blocked states.
- User-friendly explanations for API/admin decisions.

## Risk Register

| Risk | Settings principle |
| --- | --- |
| Settings become an admin panel | Show user-facing controls and summaries only; keep admin authority in Admin/API. |
| Local toggle bypasses server policy | Revalidate server-backed settings before treating them as accepted. |
| Offline settings appear saved | Label pending/last-known states and disable server-changing actions. |
| User changes wrong tenant settings | Keep tenant context visible and disable tenant switching offline. |
| Native permission is confused with SaaS permission | Separate device permission state from role/feature authorization. |
| Storage cleanup deletes unsynced work | Distinguish cache/downloads from drafts/queue and require stronger warnings. |
| Diagnostics leak secrets | Redact tokens, request bodies, private payloads, and raw logs. |
| Support access leaks tenant data | Scope support visibility through API policy and tenant support entitlement. |
| Legal documents become stale | Label cached/bundled policy state and require API confirmation for current versions. |
| Remote config enables unsafe settings | Treat config as layout/copy guidance, not authorization. |

## Success Test

Mobile settings logic is successful when a mobile user can open settings and immediately understand:

- Which account and tenant the settings apply to.
- Which controls are local to this device.
- Which controls are managed by Admin/API.
- Which controls are disabled offline and why.
- How to recover denied native permissions.
- How local cache, drafts, queue, and storage affect their work.
- How to get support without exposing sensitive data.
- Which legal and diagnostic information is current, cached, or last-known.

Before implementation, every settings group should answer:

- What is this group for?
- What can be changed locally?
- What must be confirmed by API?
- What is controlled by admin, tenant policy, subscription, feature flag, app version, or remote config?
- What is visible offline?
- What is disabled offline?
- What local data could be lost?
- What support or recovery path exists?
- What must never be shown or edited here?

If a settings group cannot answer those questions, it is not ready for implementation planning.
