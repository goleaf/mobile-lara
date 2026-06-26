# Authentication Principles

Updated: 2026-06-26

This document defines authentication principles for the Mobile Lara SaaS system. It explains mobile login through API only, secure token handling, refresh session behavior, logout, logout-all-devices, tenant selection after login, session expiry, offline behavior when already authenticated, and behavior when the server revokes access. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, NativePHP plugins, policies, jobs, services, providers, local storage schemas, token tables, guards, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX Principles](mobile-ux-principles.md), [Mobile App Shell Logic](mobile-app-shell-logic.md), [Mobile Dashboard Logic](mobile-dashboard-logic.md), [Mobile Settings Logic](mobile-settings-logic.md), [Mobile Permission Logic](mobile-permission-logic.md), [Mobile App Lock Principles](mobile-app-lock-principles.md), [API-First Principles](api-first-principles.md), [Documentation-First Architecture](documentation-first-architecture.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md), [Admin Safety Principles](admin-safety-principles.md), [NativePHP Local Storage](nativephp-local-storage.md), and [API v1 Auth Contract](../contracts/api/v1-auth.md): authentication is the trust boundary between the mobile user's local device session and the Admin/API system's authority over identity, tenant access, token validity, device trust, account state, and revocation.

Mobile App Lock Principles are defined in `mobile-app-lock-principles.md`:
local biometric or PIN unlock protects private cached data, sensitive areas,
offline drafts, and app resume behavior, but never replaces API login,
authorization, tenant access, billing authority, feature authority, or server
revocation.

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

## Authentication Statement

Mobile authentication is an Admin/API authority.

The NativePHP mobile client may collect credentials, show login and recovery screens, store tokens securely, present local session state, lock/unlock the app locally, refresh sessions through the API, and clear local state on logout. It must not authenticate users locally, create tenant access locally, grant permissions locally, issue tokens locally, extend expired sessions locally, or ignore server revocation.

Product rule: a local authenticated presentation state is not the same as server authorization. The mobile client is considered authenticated for protected work only when the API accepts the current token/session and returns a valid user, tenant, device, version, permission, feature, and config context.

Authentication should answer:

- Who is the user?
- Is the account allowed to use mobile?
- Which tenant can the user enter?
- Which device/session is active?
- Is the app version allowed?
- Are access and refresh credentials still valid?
- What can be shown while offline?
- What must happen when the server revokes access?

## Authority Split

Authentication spans both systems, but authority is not shared equally.

| Area | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Login decision | Credential validation, invitation state, account state, tenant membership, device/session policy, rate limits, token issuance, audit. | Login form UX, loading/error display, local credential entry, support/recovery entry, secure token storage after API success. |
| Token validity | Access token issuance, refresh token issuance, expiry, rotation, revocation, device binding, suspicious-use handling. | Secure storage, local expiry awareness, refresh timing, token deletion after logout/revocation. |
| Session state | Current session truth, forced logout, logout-all-devices, session expiry, device trust, account suspension. | Local session display, app lock, timeout messaging, offline last-known presentation, local cleanup. |
| Tenant access | Tenant list, default tenant, active tenant authority, tenant status, tenant switching eligibility. | Tenant selection UX after login, cached tenant labels, switcher presentation, offline last-known tenant display. |
| Offline continuity | What can be cached, drafted, queued, or blocked while offline. | Honest offline mode, last-known context, draft/queue UX, no protected server writes until API accepts them. |
| Revocation | Immediate denial, token/session/device invalidation, audit, support/admin recovery meaning. | Clear revoked state, secure token deletion, return to welcome/recovery/support, stop queued protected replay. |

## Mobile Login Through API Only

The mobile client must log in only through the Admin/API system.

Principles:

- Mobile login must send credentials or trusted auth callback results to the API, not to local-only logic.
- The API decides whether credentials are valid.
- The API decides whether the user is invited, active, suspended, restricted, recovery-limited, or blocked.
- The API decides whether the app version, device, tenant, role, subscription, maintenance state, and feature posture allow mobile access.
- The API returns the mobile-safe user context, tenant choices, current tenant, tokens, session metadata, next action, and bootstrap requirement.
- The mobile client must not infer success from locally matching cached user data.
- The mobile client must not unlock private tenant content after credential entry until the API confirms the session or an explicitly allowed offline-unlock state applies.
- Login attempts should be rate-limited, abuse-aware, and generic enough to avoid account enumeration.
- Login errors should be user-friendly and safe: invalid credentials, invited-only, suspended, tenant unavailable, update required, maintenance, rate limited, network unavailable, or support required.

Login is successful only when API authority says it is successful.

## Secure Token Handling

Tokens are secrets and must be handled as secrets.

Principles:

- Access tokens should be short-lived and treated as bearer credentials.
- Refresh tokens should be longer-lived than access tokens but more sensitive because they can obtain new access.
- Tokens should be stored in NativePHP secure storage where available.
- Tokens must not be stored in local SQLite, normal cache, logs, analytics, crash reports, screenshots, exported diagnostics, support text, URL query strings, local notifications, or visible Livewire/browser state.
- Tokens should be opaque to the mobile client. Mobile may know expiry metadata, but should not parse business authority from token contents.
- Token values should be deleted from secure storage on logout, logout-all-devices, revocation, refresh failure that requires re-login, secure-storage failure, or account reset.
- Token handling should assume device compromise is possible and reduce blast radius through short lifetimes, rotation, revocation, and server validation.
- The app should use HTTPS for all API authentication communication.
- Build-time secrets and API credentials must not be bundled into the mobile app as authority secrets.
- Support diagnostics may show token presence, expiry category, or session state only as safe summaries.

Secure storage is the preferred token home. If secure storage is unavailable or unhealthy, authenticated mobile behavior should be limited, support-routed, or forced back to safe login/logout behavior rather than falling back to unsafe storage.

## Refresh Session Principles

Session refresh keeps mobile usable without turning the client into auth authority.

Principles:

- Refresh should happen only through the API.
- Refresh should use the refresh credential, not the user's password or cached profile data.
- Refresh should rotate credentials when the API requires rotation.
- Refresh should update local expiry metadata and current user/session context after success.
- Refresh should not silently grant access to tenants, features, or permissions that the API no longer returns.
- Refresh should re-check account state, tenant state, device trust, app version, maintenance, and support-required outcomes where the API provides them.
- Refresh should avoid endless retry loops.
- Refresh should handle network failure differently from server rejection.
- Refresh should not run while the app is in forced update, blocked version, hard maintenance, or secure-storage failure states unless the API contract explicitly allows it for recovery.

Refresh outcomes:

| Outcome | Mobile behavior |
| --- | --- |
| Refresh succeeds | Store rotated credentials securely, update local session summary, refresh bootstrap/context where required, continue authenticated flow. |
| Network unavailable | Keep last-known authenticated presentation only if offline policy allows it; do not claim server confirmation. |
| Refresh expired | Clear credentials and return to login/recovery. |
| Refresh revoked | Clear credentials, stop protected work, show revoked or logged-out state. |
| User suspended | Clear or quarantine private session state according to policy and show suspended/support guidance. |
| Device blocked | Clear tokens for that device and show device/support guidance. |
| Version blocked | Preserve safe local drafts where allowed and show forced update behavior. |
| Maintenance | Follow maintenance state and avoid protected session refresh loops. |

Refresh is a bridge back to API truth, not a local extension of trust.

## Logout Principles

Logout should remove both server session authority and local token access.

Principles:

- Logout should call the API when network is available so the current mobile session/device token can be revoked server-side.
- Local logout should clear secure tokens, local session presentation, private cached session summaries, and pending auth-only state.
- Logout should not delete all local drafts blindly if they may need recovery; draft handling must follow sync/offline and tenant policy.
- Logout should not leave protected queued actions able to replay under a later user.
- Logout should return the user to welcome, login, support, or offline-logout-pending state depending on API/network outcome.
- Logout should be safe to repeat.
- Logout should be clear when only local cleanup happened because the API was unreachable.
- Logout should not expose raw token/session details to the user.
- Logout should preserve auditability: the API should be able to record successful logout when contacted.

If logout API contact fails while offline, the mobile client may perform local cleanup and mark server-side logout as unconfirmed. It must not continue using the old session as if logout failed to happen.

## Logout-All-Devices Principles

Logout-all-devices is a dangerous security action.

Principles:

- Logout-all-devices must be an API-controlled action.
- The API decides which sessions, devices, refresh tokens, and access tokens are revoked.
- The current device should receive a clear result: kept active if policy allows, logged out if all sessions are revoked, or recovery-required if the API demands reauthentication.
- The action should require appropriate confirmation and, when policy requires, fresh authentication or step-up verification.
- The action should be audited because it changes security posture across devices.
- The mobile client should explain that other devices may lose access and may need to log in again.
- The mobile client should not attempt to revoke other devices locally or infer which devices exist from stale cache.
- If offline, logout-all-devices should be disabled or queued only if the API contract explicitly allows a security-safe delayed request.

Logout-all-devices protects the account, but it must not become a local-only promise.

## Tenant Selection After Login

Tenant selection after login is part of authentication context.

Principles:

- The API returns the tenant choices the user is allowed to access.
- The API returns account state and tenant state before mobile shows private tenant data.
- If the user has one allowed active tenant, the API may identify it as the current tenant.
- If the user has multiple allowed tenants, the mobile client may show a simple tenant selection screen.
- If no tenant is available, the mobile client should show invited, suspended, billing-limited, support-required, or no-access guidance based on the API outcome.
- Tenant selection must happen through API validation.
- Mobile must not create tenant membership from cached labels.
- Mobile must not mix cached data, drafts, queue entries, notifications, or feature state across tenants after selection.
- Tenant selection should trigger or require fresh bootstrap/context for the selected tenant.
- Offline tenant selection after first login should be disabled unless explicitly documented as safe.

Tenant selection is not just navigation. It determines the scope of every subsequent mobile action.

## Session Expiry Behavior

Session expiry should be predictable, recoverable, and honest.

Principles:

- Access token expiry should trigger refresh when possible and allowed.
- Refresh token expiry should require login, recovery, or support depending on account state.
- Local app lock timeout is not the same as server session expiry.
- A user may unlock the app locally and still need API revalidation before protected work continues.
- Expiry messages should distinguish offline, expired, revoked, suspended, blocked version, maintenance, and unknown states.
- The mobile app should not repeatedly interrupt the user when a background refresh fails for a temporary network reason.
- Protected writes should check current session validity before submission or replay.
- Pending offline work should remain pending until the API confirms a valid session and accepts replay.
- Expiry should not expose token values, exact security internals, or attacker-useful timing details beyond user-safe guidance.

Expiry behavior should lead the user to the next safe action: continue offline-limited work, refresh, log in again, update the app, contact support, or wait for maintenance to end.

## Offline Behavior When Already Authenticated

Offline authenticated behavior is a limited last-known state.

Principles:

- The mobile app may show last-known user, tenant, permissions, feature flags, config, and cached data while offline when policy allows.
- Offline state should be clearly visible where it affects trust, freshness, or actions.
- The app may allow read-only views, local drafts, and queueable intents only for features documented as offline-capable.
- The app must not refresh tokens, select new tenants, register devices, submit protected writes, change account/security settings, or confirm logout-all-devices while offline unless the API contract explicitly allows a safe delayed behavior.
- Local unlock or biometric success may protect cached content, but it does not prove server session validity.
- Queued actions created while offline must revalidate account, tenant, session, device, version, permissions, feature flags, subscription, and sync policy before replay.
- Offline login for a new user should not be allowed because the API cannot validate identity.
- Offline already-authenticated mode should explain what is cached, what is pending, what is blocked, and what will revalidate when online.

Offline mode is useful for continuity. It is not a second authentication system.

## Server Revocation Behavior

Server revocation wins over local state.

Revocation can happen because:

- User logs out from another device.
- Admin suspends or removes the user.
- Tenant access is revoked.
- Device is blocked or no longer trusted.
- Refresh token rotation detects suspicious reuse.
- Password, MFA, recovery, or security policy changes require reauthentication.
- App version becomes blocked.
- Billing, support, or compliance policy requires access restriction.
- Emergency security action invalidates sessions.

Principles:

- The API should reject revoked access consistently.
- The mobile client should stop protected work immediately after receiving a revoked/unauthenticated/forbidden session outcome.
- Secure tokens should be deleted or quarantined according to policy.
- Private cached data should be hidden, cleared, or made inaccessible according to tenant/security policy.
- Queued actions must not replay under a revoked session.
- The user should see a clear next action: log in again, contact admin, contact support, update app, wait for maintenance, or switch tenant if allowed.
- Revocation should override stale cached user context, local unlock state, remembered tenant, feature shortcuts, and pending sync state.
- Revocation should be auditable server-side.

Mobile must never argue with server revocation.

## Authentication State Model

Authentication state should be expressed as user-safe states, not raw token internals.

| State | Meaning | Mobile outcome |
| --- | --- | --- |
| Guest/pre-login | No usable authenticated API session exists. | Show welcome, login, invitation, recovery, update, maintenance, or support entry points. |
| Logging in | User submitted credentials or auth callback and waits for API result. | Show loading, prevent duplicate submission, keep credentials out of logs and persistent visible state. |
| Authenticated | API accepted the session and returned usable context. | Enter current tenant or tenant selection, then bootstrap mobile context. |
| Tenant selection required | API accepted user but needs active tenant choice. | Show allowed tenants only and validate selection through API. |
| Locked | Local device protection is active. | Hide private content until local unlock; revalidate API when required. |
| Offline authenticated | Last-known session exists but API cannot be reached. | Show offline-limited mode, cached context, drafts, and queueable actions only where allowed. |
| Refreshing | Access token needs renewal through API. | Attempt refresh safely, avoid duplicate loops, preserve user context while pending where appropriate. |
| Expired | Refresh is no longer valid or allowed. | Clear credentials and require login/recovery. |
| Revoked | Server invalidated session, token, device, or access. | Stop protected work, clear credentials, show next action. |
| Suspended/restricted | Account or tenant state blocks normal access. | Show support/admin guidance and avoid normal navigation. |
| Update required | App version cannot safely authenticate or continue. | Show forced update state and preserve safe local work where allowed. |
| Maintenance | Authentication or protected work is temporarily limited. | Show maintenance state and retry/support guidance. |

## Admin Control And Safety

Admin authentication controls are high-impact controls.

Admin/API should control:

- User invitation, activation, suspension, reactivation, removal, and recovery.
- Device trust, device blocking, session revocation, and token invalidation.
- Logout-all-devices behavior.
- Password/MFA/security posture where implemented.
- Tenant membership and tenant selection eligibility.
- Mobile access by app version, maintenance mode, feature flag, subscription, and support state.
- Audit visibility for login, refresh, logout, logout-all, failed login, revoked access, suspicious refresh, and account state changes.

Admin controls should show mobile impact before saving:

- Which users or tenants will be logged out.
- Which devices will be blocked.
- Whether offline users will be forced out on next API contact.
- Whether queued work can still replay.
- Whether support needs recovery instructions.
- Whether tenant switching or mobile bootstrap will change.

Dangerous authentication controls require confirmation, audit history, and rollback or recovery thinking.

## Mobile Settings And Diagnostics

Authentication state should be visible without leaking secrets.

Settings may show:

- Signed-in user summary.
- Active tenant summary.
- Session state: active, locked, offline-limited, expired, revoked, suspended, update-required, or maintenance-limited.
- Secure storage health as a safe summary.
- Last successful API confirmation or last-known label where useful.
- Logout and logout-all-devices actions where allowed.
- Device/session support reference where safe.
- Recovery/contact support path.

Settings must not show:

- Access tokens.
- Refresh tokens.
- Token hashes.
- Raw secure-storage keys or values.
- Internal token identifiers.
- Secret-bearing headers.
- Full device fingerprints.
- Raw audit payloads.
- Internal revocation reasons that could help an attacker.

Diagnostics should help support understand auth state without turning the mobile app into a token inspection tool.

## API Response Principles

Authentication API responses should be predictable and mobile-safe.

Principles:

- Success responses should provide enough context for the next mobile state without exposing internal authority internals.
- Error responses should use stable categories and user-safe next actions.
- Validation errors should help users fix input without revealing account existence.
- Unauthenticated means the user lacks a valid session.
- Forbidden means the user/session exists but cannot perform the requested action.
- Revoked, suspended, tenant-blocked, version-blocked, maintenance, and rate-limited states should be distinguishable enough for mobile UX.
- Responses should not leak secrets, token hashes, policy internals, or cross-tenant information.
- Mobile should treat unknown auth errors conservatively and route to retry, support, or login rather than guessing.

Do not design endpoints in this document. Use the API contract documents for endpoint-level behavior.

## Boundaries

Authentication principles must never grant mobile ownership of:

- User identity authority.
- Tenant membership authority.
- Permission or role authority.
- Billing/subscription authority.
- Token issuance or revocation authority.
- Device trust authority.
- Session expiry authority.
- Logout-all-devices authority.
- Server audit truth.
- Offline replay acceptance.

Authentication principles allow mobile ownership of:

- Login, logout, recovery, tenant-selection, expired-session, and revoked-session UX.
- Secure local token storage behavior.
- Local app lock and local timeout presentation.
- Offline last-known session display.
- Clear mobile-friendly auth error states.
- Safe deletion of local credentials and protected state.
- Support and diagnostics entry points that avoid exposing secrets.

## Risk Register

| Risk | Authentication principle |
| --- | --- |
| Mobile authenticates locally | Login and refresh must go through API only. |
| Token leaks through local storage | Store tokens in secure storage; never SQLite, logs, diagnostics, URLs, or visible state. |
| Refresh extends revoked access | Refresh must validate with API and respect revocation. |
| Logout only clears UI | Logout should revoke server session when online and clear local credentials. |
| Logout-all-devices is treated as local | API owns cross-device revocation and audit. |
| Tenant selection mixes data | Tenant selection must be API-validated and isolate cache/drafts/queues. |
| Offline mode becomes auth bypass | Offline already-authenticated mode is last-known and limited. |
| Local app lock is mistaken for server auth | Local unlock protects device access only; API still decides session validity. |
| Server revocation is ignored | Revocation overrides cached state, local unlock, feature visibility, and queued replay. |
| Error messages leak account existence | Auth errors should be useful but generic where enumeration risk exists. |
| Support diagnostics leak secrets | Diagnostics should show safe summaries only. |
| Old app version mishandles auth | Version policy can block or force update before auth continues. |

## Success Test

Authentication principles are successful when a mobile user can:

- Log in only through the API.
- Understand whether they are logged in, locked, offline-limited, expired, revoked, suspended, or update-required.
- Trust that tokens are protected in secure storage and not exposed in diagnostics.
- Continue safe offline work only when already authenticated and policy allows it.
- Refresh a session without re-entering credentials when the API allows it.
- Log out from the current device safely.
- Revoke access across devices through an API-controlled logout-all-devices flow when allowed.
- Select a tenant only from API-approved choices.
- Be forced out cleanly when the server revokes access.

Before implementation, every authentication-related feature should answer:

- Which system owns the authority?
- Which mobile state appears before, during, and after the auth action?
- What happens when online?
- What happens when offline?
- What happens when the token expires?
- What happens when refresh is revoked?
- What happens when the user is suspended?
- What happens when tenant access changes?
- What local data, drafts, queues, or cache are affected?
- What is cleared, hidden, preserved, or support-routed?
- What audit event should exist server-side?
- What must never be logged, stored, displayed, or cached?

If an authentication feature cannot answer those questions, it is not ready for implementation planning.
