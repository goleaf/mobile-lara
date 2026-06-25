# Mobile App Lock Principles

Updated: 2026-06-26

This document defines mobile app lock principles for the Mobile Lara NativePHP
client. It explains when the app should lock, what sensitive areas require
confirmation, when biometric unlock is useful, when PIN unlock is useful, what
happens after repeated failed unlock attempts, what happens after logout, what
happens when biometrics are disabled by admin, and how app lock should protect
offline cached data. It is documentation only and does not define database
structure, database fields, migrations, routes, controllers, Livewire
components, NativePHP plugins, policies, jobs, services, providers, local
storage schemas, token tables, lock tables, guards, or application logic.

Use this document with [Product Vision](product-vision.md), [Core Product
Principles](product-principles.md), [Two-System Boundary Logic](two-system-boundary.md),
[Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX
Principles](mobile-ux-principles.md), [Mobile App Shell Logic](mobile-app-shell-logic.md),
[Mobile Settings Logic](mobile-settings-logic.md), [Mobile Permission Logic](mobile-permission-logic.md),
[Authentication Principles](authentication-principles.md), [Role And Permission
Logic](role-permission-logic.md), [API-First Principles](api-first-principles.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Feature Flag
Logic](feature-flag-logic.md), [Admin Safety Principles](admin-safety-principles.md),
and [NativePHP Local Storage](nativephp-local-storage.md): app lock protects
local access to private mobile state, but it never replaces Admin/API
authentication, authorization, tenant authority, billing authority, feature
authority, or server revocation.

Role And Permission Logic is defined in `role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact with
feature flags as separate gates; suspended users and suspended tenants fail
closed without bypassing tenant isolation.

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

## App Lock Statement

Mobile app lock is a local privacy and security control.

The NativePHP client may lock the visible app, require a local unlock gesture,
hide sensitive cached data, protect offline drafts, and step up confirmation
before sensitive local actions. It must not use local unlock success as proof
that the server session is valid, the tenant is active, the user still has
permission, the subscription is current, or a queued action may sync.

Product rule: app lock protects the device edge. Admin/API still owns identity,
tenant access, permissions, feature availability, app-version policy, remote
config, revocation, support state, billing entitlement, and final sync
acceptance.

App lock should answer:

- Can private cached data be shown on this device right now?
- Does this user need a local unlock before resuming work?
- Does this sensitive area require an extra confirmation?
- Is biometric unlock allowed, available, and appropriate?
- Is PIN unlock allowed, available, and appropriate?
- What happens after failed unlock attempts?
- What is protected while the app is offline?
- What happens when admin policy changes?

## Authority Split

App lock spans both systems, but authority must remain clear.

| Area | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Lock policy | Whether app lock is required, optional, disabled, tenant-specific, role-specific, feature-specific, or version-specific. | Presenting locked state, applying last-known policy locally, and requesting unlock before showing private data. |
| Unlock methods | Whether biometrics, PIN, full API re-authentication, or support recovery are allowed for the tenant, role, feature, or device posture. | Native biometric prompt UX, PIN prompt UX, fallback sequencing, and local failed-attempt handling. |
| Sensitive areas | Which account, tenant, sync, support, storage, billing, report, file, or security areas require step-up confirmation. | Showing confirmation before entering or changing those areas and keeping wording mobile-safe. |
| Session authority | Token validity, session revocation, account suspension, device blocking, tenant removal, and logout-all-devices meaning. | Hiding local content when authority is uncertain and clearing local secrets when API says access is revoked. |
| Offline protection | Which cached data, drafts, queued actions, attachments, and diagnostics are allowed offline. | Locking offline cache before display, separating tenant data, showing last-known context, and preventing unsafe replay. |
| Audit and support | Server-side records for policy changes, forced logout, failed server auth, and admin actions. | Safe local summaries for diagnostics without exposing secrets, PINs, biometric details, or protected content. |

## When The App Should Lock

The app should lock whenever private local state might be visible without a
fresh enough local security check.

Lock triggers should include:

- **App launch with existing session** - Before showing private cached data,
  tenant context, recent activity, notifications, drafts, or files.
- **App resume after backgrounding** - After a documented idle timeout or when
  the OS returns from a locked or interrupted state.
- **Manual lock** - When the user chooses to lock the app from settings, shell,
  or account controls.
- **Sensitive area entry** - Before opening account security, local storage,
  diagnostics, private records, reports, billing-visible areas, tenant
  switching, or destructive local actions.
- **Tenant or account switch** - Before exposing data from another tenant or
  account, especially when local drafts, queues, or cached records exist.
- **Offline private access** - Before showing cached content while the API
  cannot revalidate the session.
- **Policy change** - When Admin/API changes lock requirements, disables an
  unlock method, increases sensitivity, or requires full re-authentication.
- **Repeated failed attempts** - After too many failed biometric, PIN, or local
  unlock attempts.
- **Session uncertainty** - When refresh fails, secure storage is unhealthy,
  server revocation is suspected, device trust is unknown, or the app cannot
  confidently separate local convenience from server authority.
- **High-risk lifecycle events** - After app reinstall, restore from backup,
  biometric enrollment change, secure-storage reset, device time anomaly, or
  other device posture change that makes local trust uncertain.

App lock should not block public welcome, login, support, update-required, or
maintenance messaging unless those screens would reveal private cached content.

## Sensitive Areas Requiring Confirmation

Some areas need more than normal navigation because they can expose private
data, change trust posture, or affect queued work.

Confirmation should be required for:

- **Account and security settings** - Password changes, session lists, device
  trust, logout-all-devices, app lock preferences, and recovery actions.
- **Tenant switching** - Switching active tenant when private cached data,
  drafts, queued actions, reports, files, or notifications could be mixed.
- **Local storage actions** - Clearing cache, deleting drafts, removing queued
  work, exporting diagnostics, downloading files, or changing storage behavior.
- **Sync replay and conflict actions** - Submitting sensitive queued actions,
  resolving conflicts, retrying protected uploads, or discarding local changes.
- **Private records and reports** - Viewing personally sensitive records,
  confidential tenant data, reports, billing-visible summaries, or support
  case details.
- **Notification and permission changes** - Enabling sensitive notifications,
  granting native permissions for capture or location, or changing permission
  recovery status.
- **Support diagnostics** - Sending logs, app state summaries, sync metadata,
  storage summaries, or device diagnostics to support.
- **Native capture features** - Camera, microphone, location, files, scanner,
  or biometric flows where a user could expose private tenant data.
- **Logout and destructive local choices** - Logout, logout-all-devices, local
  wipe, tenant data removal, or any action that may make offline drafts harder
  to recover.

Confirmation is not authorization. It is a mobile safety step layered on top of
API permissions, feature flags, tenant status, subscription status, and server
policy.

## Biometric Unlock Principles

Biometric unlock is useful when the app needs a fast local proof that the person
holding the device can unlock private cached content.

Biometrics are useful for:

- Returning users who open the app many times per day.
- Unlocking offline cached data without asking the user to type a password.
- Step-up confirmation before sensitive but frequent mobile actions.
- Protecting local drafts, queues, files, and notifications while preserving a
  fast mobile workflow.
- Reducing PIN entry exposure in public or field environments.
- Supporting simple NativePHP mobile UX when the device has a secure biometric
  capability and admin policy allows it.

Biometrics should not be treated as:

- API login.
- Server-side authorization.
- Permission to sync queued protected actions.
- Permission to enter a tenant after the API removed access.
- Proof that billing, feature flags, version policy, or account status still
  allow the action.
- A required method when admin policy disables biometrics, the device does not
  support them, the user has not enrolled them, or the OS reports biometric
  state changed.

Biometric success means only that the local device accepted a supported local
unlock prompt. The app should still refresh or revalidate API authority when
online and required by policy.

## PIN Unlock Principles

PIN unlock is useful as a local fallback when biometrics are unavailable,
disabled, denied, unreliable, or not allowed by admin policy.

PIN unlock is useful for:

- Devices without biometric support.
- Users who cannot or do not want to use biometrics.
- Admin policies that allow app lock but disable biometrics.
- Offline access to cached data when full API re-authentication is unavailable.
- Recovery from temporary biometric failure.
- Environments where gloves, masks, lighting, hardware, or accessibility needs
  make biometrics unreliable.

PIN unlock must stay local and limited:

- A PIN is not the account password.
- A PIN is not an API credential.
- A PIN must not grant new tenant access.
- A PIN must not extend a revoked server session.
- A PIN must not be stored or displayed in plain text.
- A PIN must follow retry limits, timeout, and reset behavior.
- A PIN reset should require API authority when online, or a documented safe
  recovery path when offline.

PIN unlock should be easy enough for mobile use but strong enough to protect
offline cached data from casual device access.

## Repeated Failed Unlock Attempts

Failed unlock attempts should protect local data without pretending to make
server-side account decisions.

After repeated failed attempts, the mobile client should:

- Keep private cached content hidden.
- Increase delay, require waiting, or temporarily block local unlock attempts.
- Require a stronger unlock method when available, such as PIN after biometric
  failure or full API login when online.
- Stop sensitive queued-action replay until the user is revalidated.
- Preserve local drafts where policy allows, but prevent viewing, exporting, or
  syncing them until unlock or re-authentication succeeds.
- Show calm, generic messaging that avoids exposing whether a specific account,
  tenant, or cache exists.
- Offer logout, support, recovery, or retry paths according to policy.
- Record safe local diagnostics and send server-side audit context only when
  online and allowed.

Repeated local unlock failure should not delete server data, change server
permissions, suspend the account, or revoke every device by itself. Those are
Admin/API decisions. Local wipe may be appropriate only when the policy is
explicit, documented, communicated, tenant-scoped, and designed to avoid
destroying recoverable server-backed data.

## After Logout

Logout should end the old local session as an unlock target.

After logout, the mobile client should:

- Clear local app-lock state tied to the old authenticated session.
- Delete secure tokens and session secrets according to authentication
  principles.
- Return to welcome, login, update, maintenance, or support state.
- Prevent biometric or PIN unlock from reopening the previous account.
- Hide private cached data, notifications, tenant context, files, drafts, and
  queues until a valid session is restored or a documented recovery flow
  applies.
- Keep only safe, non-secret device preferences where appropriate.
- Keep recoverable drafts only under documented sync/offline rules and never
  replay them under a different user or tenant without API acceptance.
- Treat logout-all-devices or server revocation as stronger than local logout:
  local unlock must not restore access after either event is known.

If logout starts offline, the app may show a safe logout-pending state, but it
must not expose protected content through biometric or PIN unlock while server
logout is unresolved.

## Biometrics Disabled By Admin

Admin/API may disable biometric unlock globally, by tenant, by plan, by role,
by user state, by device posture, by app version, or during an incident.

When biometrics are disabled by admin:

- The mobile client should not show a biometric prompt.
- Existing local biometric preferences should become inactive.
- Security settings should explain that biometrics are unavailable because of
  admin policy.
- The app should fall back to PIN, full API login, or locked/support behavior
  according to the policy returned by the API.
- Disabled biometric settings should be visible only when useful, and should be
  read-only or hidden according to mobile UX principles.
- Cached remote config or feature flags must not override a newer known admin
  disable decision.
- Offline behavior should use the last-known safe policy. If the app cannot
  decide safely, it should prefer locking and require API revalidation when
  online.

Biometric disablement is a control-plane decision. Mobile should present it
clearly, not negotiate it locally.

## Offline Cached Data Protection

Offline app lock protects cached data while the API is unavailable.

Offline cached data should be protected by these principles:

- Private cached data must stay hidden until local unlock succeeds.
- Local unlock while offline should be labelled as last-known access, not fresh
  server authorization.
- Cached data, drafts, queues, and attachments should remain tenant-separated.
- The shell may show safe summaries, but should not expose private record
  contents, token values, secret config, full diagnostics, or sensitive files
  before unlock.
- Offline unlock may allow viewing, drafting, or queueing only where offline
  policy allows it.
- Offline unlock must not force-sync, submit protected writes, resolve
  conflicts, or refresh entitlements without API acceptance.
- If server revocation, suspension, device blocking, forced update, or tenant
  removal is discovered later, the app should hide or clear local state
  according to the stronger server outcome.
- Secure storage should hold secrets; local SQLite should hold only cache,
  drafts, queues, and metadata that are protected by app lock and offline
  policy.
- Support diagnostics should redact private data and never include PINs,
  biometric details, token values, or protected record contents.

The safest offline principle is simple: local unlock may reveal allowed
last-known local data, but it must never create new server authority.

## Settings And User Feedback

Mobile settings should make app lock understandable without exposing secrets.

Settings may show:

- Whether app lock is required, optional, disabled, or controlled by admin.
- Whether biometric unlock is available, unavailable, denied, disabled by
  admin, or unsupported by device.
- Whether PIN unlock is enabled, required, or needs reset.
- Whether offline cached data is protected.
- Whether sensitive-area confirmation is required.
- Whether the current tenant or role has stricter lock rules.
- Whether recent lock or unlock failures require waiting, support, logout, or
  API revalidation.

Settings must not show:

- PIN values.
- Token values.
- Raw secure-storage contents.
- Biometric templates or biometric identifiers.
- Sensitive cached record contents.
- Diagnostics that would reveal private tenant data before unlock.

## Risks

App lock reduces local exposure, but it has risks that must be documented before
implementation:

- Users may misunderstand biometric or PIN unlock as server login.
- Offline unlock can expose stale data after server revocation if stale policy
  is not handled carefully.
- Aggressive lockouts can strand mobile users in the field.
- Weak local PIN rules can undercut offline cache protection.
- Unclear tenant switching can reveal data from the wrong tenant.
- Diagnostic exports can leak protected cached data if not redacted.
- Backup or device-restore behavior can make local trust uncertain.
- Biometric enrollment changes can invalidate the meaning of prior local trust.

Mitigation principles:

- Keep app lock language local: "Unlock this app", not "Sign in".
- Revalidate with API when online and required.
- Hide private cached data by default when authority is uncertain.
- Keep tenant caches separated.
- Provide support and recovery paths.
- Avoid destructive local wipe unless policy is explicit and documented.
- Document admin impact before changing lock policy.

## Acceptance Questions

Before app lock behavior is implemented, the product decision should answer:

- What conditions lock the app?
- Which areas require step-up confirmation?
- Which unlock methods are allowed by default?
- Which unlock methods can admin disable?
- What is the fallback when biometrics are unavailable?
- What is the fallback when PIN unlock fails?
- What happens after repeated failed attempts?
- What is cleared or hidden after logout?
- What can a logged-out user recover?
- What can be shown while offline and locked?
- What can be shown while offline and unlocked?
- What happens when server revocation is discovered after offline use?
- What does support see without exposing secrets?
- What mobile settings explain the policy?
- What audit or diagnostic signals are safe to collect?

If these questions are not answered, the app lock behavior is not ready for
implementation.
