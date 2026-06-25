# API v1 Bootstrap Contract

Updated: 2026-06-26

Status: documented. Endpoint is planned for Phase 10.

Product Vision is defined in `../../docs/product-vision.md`: this contract is
the main API path for turning central SaaS control into mobile operating
context.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the combined product by giving the mobile workforce/client
platform one API-first operating context from the SaaS control center.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

Target User Roles are defined in `../../docs/user-roles.md`: bootstrap context
must return role-derived capability state and account-state limits as
mobile-safe outcomes.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: bootstrap context
is where stakeholder value first becomes mobile-visible through allowed
features, tenant context, reports, notifications, sync policy, security, and
admin-controlled state.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: bootstrap returns Admin/API decisions as
mobile-safe operating context, and cached bootstrap state is never final
authority.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract resolves tenant
management, users and permissions, feature control, remote configuration,
mobile version rules, notification policy, billing/subscription, support,
reporting, sync/conflict, and security into one mobile-safe context.

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
