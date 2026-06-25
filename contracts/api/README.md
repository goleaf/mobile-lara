# API Contracts

This directory records mobile-facing API contracts before endpoint
implementation. The contracts are the bridge between the Admin/API control
plane and the NativePHP mobile client.

Admin Control Center logic lives in `../../docs/admin-control-center-logic.md`.
Every contract that exposes tenant, user, role, permission, feature, config,
version, maintenance, force update, sync, notification, report, billing, or
support behavior should map the admin control to a mobile-safe API outcome.

Feature Flag Logic lives in `../../docs/feature-flag-logic.md`. Every contract
that exposes feature availability should return resolved mobile-safe states
rather than raw global, tenant, user, plan, version, device, cohort,
maintenance, or emergency flag internals.

## Versioning

Mobile contracts are grouped by API version. The first implementation target is:

```text
/api/v1/mobile
```

Contracts should be additive where possible. Breaking behavior must be governed
through app-version policy before old mobile clients lose support.

## Implemented Foundation

- [v1-foundation.md](v1-foundation.md) defines the implemented
  `GET /api/v1/mobile/status` endpoint and the shared success/error envelope.

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

## Required Contract Files

Future implementation phases should add one Markdown file per contract group:

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
