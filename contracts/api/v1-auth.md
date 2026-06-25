# API v1 Auth Contract

Updated: 2026-06-25

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
```
