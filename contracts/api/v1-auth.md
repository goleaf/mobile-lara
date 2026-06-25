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

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to identity,
users and permissions, security enforcement, audit history, and API-contract
responsibility.

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

The Livewire auth/profile/session screens still need to consume this service in
a later Phase 5 slice.

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
