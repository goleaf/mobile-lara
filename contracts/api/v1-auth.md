# API v1 Auth Contract

Updated: 2026-06-26

Status: implemented for the API/admin mobile token foundation.

Product Vision is defined in `../../docs/product-vision.md`: this contract
keeps identity and session authority in Admin/API while mobile presents a
simple NativePHP + Livewire authentication experience.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the API-first tenant-based platform by keeping identity,
sessions, and device trust out of mobile-local authority.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must keep identity and
session behavior API-only, predictable, mobile-friendly, sync-safe where
relevant, and tenant-scoped.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: identity and session behavior
must document feature purpose, mobile effect, API dependency, permission owner,
offline/session recovery behavior, and risks before implementation.

Target User Roles are defined in `../../docs/user-roles.md`: auth outcomes must
distinguish admin, tenant, support, billing, mobile, invited, suspended, and
guest/pre-login states without exposing raw authority.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: auth outcomes
create stakeholder value by making secure mobile access, tenant isolation,
support recovery, billing restrictions, and feature eligibility trustworthy.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: identity, tenant access, session validity,
device trust, and revocation remain Admin/API authority while mobile presents
local session state.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to identity,
users and permissions, security enforcement, audit history, and API-contract
responsibility.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports secure
local session UX, login/logout feedback, guest/pre-login navigation, and
API-derived account-state visibility without giving mobile auth authority.

Mobile UX Principles are defined in `../../docs/mobile-ux-principles.md`: this
contract should support mobile-first navigation, simple screens, clear
loading/offline states, thumb-friendly controls, minimum typing, fast actions,
secure sessions, feature visibility, and native permission education.

Mobile App Shell Logic is defined in `../../docs/mobile-app-shell-logic.md`:
shell states must coordinate welcome, authenticated, locked, offline, maintenance, forced update, tenant
switching, sync-in-progress, permission-blocked, and feature-disabled behavior
before implementation.

Mobile Dashboard Logic is defined in `../../docs/mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Mobile Settings Logic is defined in `../../docs/mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `../../docs/mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

Authentication Principles are defined in `../../docs/authentication-principles.md`:
mobile login must happen through the API only; access and refresh tokens must
use secure storage; refresh, logout, logout-all-devices, tenant selection,
session expiry, offline already-authenticated behavior, and server revocation
must preserve Admin/API authority before implementation.

Mobile App Lock Principles are defined in `../../docs/mobile-app-lock-principles.md`:
the mobile client must lock on security-sensitive lifecycle, timeout,
account, tenant, offline-cache, and admin-policy conditions; require
confirmation for sensitive areas; use biometric or PIN unlock only as local
protection; handle failed attempts, logout, admin-disabled biometrics, and
offline cached data without bypassing Admin/API authority.

Role And Permission Logic is defined in `../../docs/role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact
with feature flags as separate gates; suspended users and suspended tenants
fail closed without bypassing tenant isolation.

Audit Logic is defined in `../../docs/audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

Data Privacy Principles are defined in `../../docs/data-privacy-principles.md`:
tenant isolation, least privilege, secure local mobile data, secure native
storage, export and deletion boundaries, support access limits, admin
visibility boundaries, privacy-by-default behavior, and mobile diagnostics
privacy limits must protect users and tenants without turning mobile cache,
support views, or audit history into uncontrolled data exposure.

Tenant Lifecycle Logic is defined in `../../docs/tenant-lifecycle-logic.md`:
tenant creation, onboarding, trial, active, suspended, archived,
billing-blocked, deletion/requested deletion, and restore states must be
Admin/API-owned lifecycle decisions that mobile presents as safe,
tenant-scoped, billing-aware, supportable states without inventing local
tenant authority.

Tenant Admin Logic is defined in `../../docs/tenant-admin-logic.md`:
tenant admins may manage tenant-scoped users, invitations, delegated settings,
delegated mobile-feature controls, tenant reports, and tenant support workflows
only inside their tenant; platform-only controls, cross-tenant visibility,
global policy, billing authority, lifecycle authority, app-version policy, and
security posture remain Admin/API-owned boundaries.

Multi-Tenant Mobile Logic is defined in `../../docs/multi-tenant-mobile-logic.md`:
users with more than one tenant choose and remember tenant context through
API-confirmed state; tenant switching, tenant-scoped cache, per-tenant
permissions and feature flags, sync replay, offline behavior, and logout
cleanup must preserve tenant isolation and never turn mobile-local state
into tenant authority.

Offline-First Principles are defined in `../../docs/offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

Offline UX Logic is defined in `../../docs/offline-ux-logic.md`:
offline UX must calmly explain banners, disabled online-only actions,
local drafts, pending indicators, retry, sync success or failure,
saved-local versus synced state, and data-loss prevention whenever
connection changes.

Records/Content Module Logic is defined in `../../docs/records-content-module-logic.md`:
records are tenant-scoped business content with API-owned lifecycle,
notes, attachments, activity, tags, categories, status, offline draft or
sync behavior, admin controls, permissions, feature flags, audit, and
reporting boundaries.

Sync Lifecycle Logic is defined in `../../docs/sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `../../docs/conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

Admin Control Center Logic is defined in
`../../docs/admin-control-center-logic.md`: this contract must keep identity,
session, user-state, device, support, and security controls scoped,
authorized, auditable, and exposed to mobile only as resolved API outcomes.

Remote Configuration Logic is defined in
`../../docs/remote-configuration-logic.md`: auth screens may use resolved
config for safe copy, support links, recovery guidance, native permission text,
and pre-login presentation, but session validity, account state, tenant access,
and device trust remain Admin/API authority.

## Purpose

Auth endpoints make the Admin/API system authoritative for mobile identity,
sessions, devices, profile state, token refresh, logout, and security audit.
The mobile client may store tokens securely and present auth state, but it does
not decide account validity, tenant access, or session revocation.

## Implemented Routes

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| POST | `/api/v1/mobile/auth/login` | Create a mobile session and token set. | public |
| POST | `/api/v1/mobile/auth/register` | Request or create an invited/mobile account where enabled. | public |
| POST | `/api/v1/mobile/auth/refresh` | Rotate access token using refresh token. | refresh token |
| POST | `/api/v1/mobile/auth/logout` | Revoke the current device session. | mobile token |
| POST | `/api/v1/mobile/auth/logout-all` | Revoke all user mobile sessions. | mobile token |
| GET | `/api/v1/mobile/auth/user` | Return current user and session state. | mobile token |
| PATCH | `/api/v1/mobile/auth/profile` | Update allowed profile fields. | mobile token |

## Success Data

Auth success responses return `user`, `session`, `device`, `tokens` when
applicable, and `next_bootstrap_required`.

Tokens must include access token expiry and refresh token expiry metadata.
Secure token values belong in secure storage on mobile, not local SQLite.

## Error States

Use the standard error envelope with categories `validation`,
`unauthenticated`, `permission`, `tenant`, `billing`, `version`,
`maintenance`, `stale_client`, `rate_limit`, or `server_error`.

Revoked, suspended, invited-only, blocked-version, maintenance, and expired
subscription states must produce mobile-friendly `next_action` values.

## Gates

Auth is controlled by tenant status, invited/suspended access state,
subscription state, feature flags, app-version policy, maintenance mode,
device/session rules, and rate limits.

## Offline Behavior

Mobile may keep a last-known authenticated presentation state while offline,
but protected writes must wait for a valid API session. Refresh failures caused
by revocation require local logout and token deletion.

## Mobile Client Integration

`apps/mobile-client` contains the first tested client-side service boundary for
this contract:

- `App\Services\MobileApi\MobileApiClient` sends standard JSON requests to the
  configured v1 mobile API base URL and raises `MobileApiException` for standard
  error envelopes.
- `App\Services\MobileApi\MobileDeviceContext` attaches device id, device name,
  platform, and app version metadata to login/register requests.
- `App\Services\MobileAuth\MobileAuthApiService` calls login, register,
  refresh, logout, logout-all, current user, and profile update routes.
- Returned access and refresh tokens are stored through `MobileTokenStore`, so
  NativePHP secure storage remains the default token home and the session
  adapter remains available for tests and safe development fallback.
- Login and register Livewire screens consume the service, store API tokens,
  and create a local presentation-only Laravel session from the API user
  payload so existing mobile route protection remains usable.
- Profile and sessions logout actions call the API service before clearing
  local session/token state; sessions also exposes logout-all-devices.
- Edit profile syncs the account name through `PATCH /auth/profile` when a
  valid access token exists.

Password reset and email verification screens remain local validation
placeholders until those API routes are documented and implemented.

## Audit

Login, refresh rotation, logout, logout-all, profile update, failed login,
revoked session use, and suspicious device changes are audit events.

## Verification

Automated coverage in `apps/api-admin`:

- `tests/Feature/MobileAuthApiTest.php`
- `tests/Feature/MobileTokenAuthenticatorTest.php`

Fresh checks for this phase:

```bash
php artisan test --compact --filter=MobileAuthApiTest
php artisan test --compact --filter=MobileTokenAuthenticatorTest
cd ../mobile-client && php artisan test --compact --filter=MobileAuthApiServiceTest
```
