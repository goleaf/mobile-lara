# API v1 Foundation Contract

Updated: 2026-06-25

This contract records the first implemented mobile-facing API foundation in
`apps/api-admin`. It proves the route namespace and standard envelope before
the domain contracts are implemented.

## Base Path

```text
/api/v1/mobile
```

## Implemented Endpoint

### GET `/api/v1/mobile/status`

Purpose:

- Confirm the Admin/API app is reachable through the versioned mobile API
  namespace.
- Confirm the API response uses the standard success envelope.
- Give mobile and diagnostics flows a low-risk health target while bootstrap,
  authentication, tenancy, feature flags, remote config, sync, and support
  contracts are still being implemented.

Success response:

```json
{
  "success": true,
  "data": {
    "service": "api-admin",
    "authority": "admin_api",
    "mobile_api": "v1",
    "status": "ok"
  },
  "meta": {
    "api_version": "v1",
    "next_contract": "v1-bootstrap",
    "server_time": "2026-06-25T00:00:00Z"
  }
}
```

## Standard Error Envelope

Implemented error responses use this shape:

```json
{
  "success": false,
  "error": {
    "code": "maintenance",
    "message": "Mobile API is temporarily unavailable.",
    "category": "maintenance",
    "next_action": "retry_later"
  },
  "meta": {
    "api_version": "v1",
    "server_time": "2026-06-25T00:00:00Z"
  }
}
```

## Current Boundaries

- This endpoint does not authenticate a mobile user.
- This endpoint does not expose tenant data, feature flags, remote config,
  permissions, subscription state, notifications, or sync settings.
- The next control-plane contract is `v1-bootstrap.md`.

## Verification

Automated coverage in `apps/api-admin`:

- `tests/Feature/MobileApiEnvelopeTest.php`
- `tests/Unit/MobileApiResponseTest.php`

Fresh checks for this phase:

```bash
php artisan route:list --except-vendor
php artisan test --compact
vendor/bin/pint --dirty --format agent
npm run build
```
