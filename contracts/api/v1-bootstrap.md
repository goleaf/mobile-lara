# API v1 Bootstrap Contract

Updated: 2026-06-25

Status: documented. Endpoint is planned for Phase 10.

Product Vision is defined in `../../docs/product-vision.md`: this contract is
the main API path for turning central SaaS control into mobile operating
context.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the combined product by giving the mobile workforce/client
platform one API-first operating context from the SaaS control center.

## Purpose

Bootstrap gives the mobile client one resolved operating context after login,
app start, tenant switch, and manual refresh. It must expose decisions, not raw
admin configuration layers.

## Planned Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/bootstrap` | Return the current mobile operating context. | mobile token |

## Success Data

The response must include:

- `user`
- `current_tenant`
- `available_tenants`
- `permissions`
- `features`
- `remote_config`
- `app_version`
- `maintenance`
- `subscription`
- `notification_preferences`
- `sync`
- `unread_notification_count`

## Metadata

`meta` should include `api_version`, `server_time`, `bootstrap_version`,
`config_version`, `features_version`, `sync_cursor`, and freshness timestamps.

## Gates

Bootstrap is constrained by tenant lifecycle, user access state,
permissions, feature flags, remote config, app-version policy, maintenance,
subscription status, notification settings, and sync rules.

## Offline Behavior

Mobile may cache the last successful bootstrap with freshness metadata. Stale
bootstrap cannot grant new authority. If bootstrap is unavailable, mobile must
fail closed for sensitive actions and clearly label cached state.

## Audit

Bootstrap is read-mostly. Audit tenant switch, stale-client denial, suspended
access, forced update, maintenance block, and suspicious device context.

## Tests

Phase 10 should verify response shape, tenant isolation, hidden raw config
layers, stale-client states, and fail-closed behavior.
