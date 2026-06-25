# API Contracts

This directory records mobile-facing API contracts before endpoint
implementation. The contracts are the bridge between the Admin/API control
plane and the NativePHP mobile client.

Product Vision is defined in `../../docs/product-vision.md`: API contracts exist
to turn central SaaS authority into predictable mobile behavior.

Product Positioning is defined in `../../docs/product-positioning.md`: these
contracts are what make the product API-first instead of web-only or
mobile-only.

Core Product Principles are defined in `../../docs/product-principles.md`:
every contract must preserve admin authority, tenant isolation, API-only mobile
behavior, feature control, useful offline behavior, secure defaults, simple
mobile UX, documentation-first planning, and modular expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: every contract must state the API purpose,
expected operating context, predictable response states, mobile-friendly error
meaning, sync/conflict behavior where relevant, and tenant-boundary protection.

Target User Roles are defined in `../../docs/user-roles.md`: every contract must
describe role and account-state effects as mobile-safe outcomes, not raw role
authority.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: every contract must
name the stakeholder value it supports and connect that value to admin control,
mobile access, offline sync, notifications, reports, security, or feature flags
without exposing raw authority.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: every contract must state which behavior
is API-only, which mobile state may be cached or queued, and which authority
stays in Admin/API.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: every contract must identify which
control-plane responsibility owns the request, response, error, sync, support,
billing, report, audit, or security outcome.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: every contract must also name
which mobile responsibility consumes the outcome as UX, local session, cache,
offline action, NativePHP capability, navigation, permission prompt, sync
status, draft, feedback, or feature visibility.

Admin Control Center logic lives in `../../docs/admin-control-center-logic.md`.
Every contract that exposes tenant, user, role, permission, feature, config,
version, maintenance, force update, sync, notification, report, billing, or
support behavior should map the admin control to a mobile-safe API outcome.

Feature Flag Logic lives in `../../docs/feature-flag-logic.md`. Every contract
that exposes feature availability should return resolved mobile-safe states
rather than raw global, tenant, user, plan, version, device, cohort,
maintenance, or emergency flag internals.

Remote Configuration Logic lives in
`../../docs/remote-configuration-logic.md`. Every contract that exposes config
should return resolved values, config version, freshness/compatibility metadata,
and safe fallback/error behavior rather than raw admin config layers.

Mobile Version Control Logic lives in
`../../docs/mobile-version-control-logic.md`. Every contract that exposes
version or maintenance policy should return resolved mobile-safe states,
store/update links, user-safe messages, support context, and stale-client error
behavior rather than raw admin version rules.

## Versioning

Mobile contracts are grouped by API version. The first implementation target is:

```text
/api/v1/mobile
```

Contracts should be additive where possible. Breaking behavior must be governed
through app-version policy before old mobile clients lose support.

## Implemented Foundation

- [v1-foundation.md](v1-foundation.md) defines the implemented
  `GET /api/v1/mobile/status` endpoint, implemented
  `GET /api/v1/mobile/contracts` catalogue endpoint, and the shared
  success/error envelope.
- [v1-auth.md](v1-auth.md) defines the implemented mobile auth, registration,
  refresh, logout, logout-all, current-user, and profile endpoints.

## Documented v1 Contract Groups

- [v1-auth.md](v1-auth.md)
- [v1-bootstrap.md](v1-bootstrap.md)
- [v1-tenancy.md](v1-tenancy.md)
- [v1-features.md](v1-features.md)
- [v1-remote-config.md](v1-remote-config.md)
- [v1-app-version-maintenance.md](v1-app-version-maintenance.md)
- [v1-records.md](v1-records.md)
- [v1-sync.md](v1-sync.md)
- [v1-notifications.md](v1-notifications.md)
- [v1-support.md](v1-support.md)
- [v1-billing.md](v1-billing.md)
- [v1-reports.md](v1-reports.md)
- [v1-diagnostics.md](v1-diagnostics.md)

## Standard Success Envelope

All mobile API success responses should use the same outer shape:

```json
{
  "success": true,
  "data": {},
  "meta": {
    "api_version": "v1",
    "server_time": "2026-06-25T00:00:00Z"
  }
}
```

`data` contains the shaped resource or command result. `meta` contains only
mobile-useful metadata such as API version, server time, cursor, freshness,
config version, sync cursor, or next action.

## Standard Error Envelope

All mobile API errors should use a predictable shape:

```json
{
  "success": false,
  "error": {
    "code": "forbidden",
    "message": "This action is not available for your account.",
    "category": "permission",
    "next_action": "contact_admin"
  },
  "meta": {
    "api_version": "v1",
    "server_time": "2026-06-25T00:00:00Z"
  }
}
```

Allowed categories include `validation`, `unauthenticated`, `permission`,
`tenant`, `feature`, `billing`, `version`, `maintenance`, `conflict`,
`stale_client`, `rate_limit`, `retry_later`, and `server_error`.

## Contract File Rule

Each v1 contract group has a Markdown file. Future implementation phases should
update the relevant file before adding or changing endpoints:

- `v1-foundation.md`
- `v1-auth.md`
- `v1-bootstrap.md`
- `v1-tenancy.md`
- `v1-features.md`
- `v1-remote-config.md`
- `v1-app-version-maintenance.md`
- `v1-records.md`
- `v1-sync.md`
- `v1-notifications.md`
- `v1-support.md`
- `v1-billing.md`
- `v1-reports.md`
- `v1-diagnostics.md`

Each file should define purpose, request shape, response shape, error states,
tenant boundary, permission/feature/billing/version gates, offline behavior,
audit expectations, tests, and mobile UI effects.
