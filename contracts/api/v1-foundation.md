# API v1 Foundation Contract

Updated: 2026-06-25

This contract records the first implemented mobile-facing API foundation in
`apps/api-admin`. It proves the route namespace and standard envelope before
the domain contracts are implemented.

Product Vision is defined in `../../docs/product-vision.md`: this foundation
keeps mobile clients dependent on Admin/API authority instead of local
assumptions.

Product Positioning is defined in `../../docs/product-positioning.md`: this
foundation proves the API-first bridge between the SaaS control center and the
mobile workforce/client platform.

Core Product Principles are defined in `../../docs/product-principles.md`: this
foundation must preserve admin control, API-first communication, tenant
isolation, secure defaults, simple mobile UX, and modular feature expansion.

Remote Configuration Logic is defined in
`../../docs/remote-configuration-logic.md`. Future bootstrap and config
contracts should return resolved mobile-safe config values, config version,
freshness, compatibility, tenant override context, and fallback/error states
without exposing admin-only configuration internals.

Mobile Version Control Logic is defined in
`../../docs/mobile-version-control-logic.md`. Future app-version and
maintenance contracts should return resolved update, force-update, blocked,
deprecated, maintenance, store-link, message, and stale-client states without
exposing raw admin policy internals.

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

### GET `/api/v1/mobile/contracts`

Purpose:

- Return the authoritative v1 mobile contract catalogue.
- Let mobile diagnostics, tests, and implementation planning discover which
  contract groups exist and which endpoints are implemented or planned.
- Keep planned endpoints explicit without pretending auth, tenancy, or domain
  modules exist before their phases.

Success response:

```json
{
  "success": true,
  "data": {
    "base_path": "/api/v1/mobile",
    "contract_version": "v1",
    "authority": "admin_api",
    "contracts": [
      {
        "key": "foundation",
        "document": "v1-foundation.md",
        "status": "implemented",
        "routes": [
          {
            "method": "GET",
            "path": "/status",
            "status": "implemented",
            "auth": "public"
          }
        ]
      }
    ]
  },
  "meta": {
    "api_version": "v1",
    "contract_count": 14,
    "next_contract": "v1-auth",
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
- The contract catalogue is public because it exposes only documentation
  metadata and planned route names.
- The next control-plane implementation contract is `v1-auth.md`.

## Verification

Automated coverage in `apps/api-admin`:

- `tests/Feature/MobileApiEnvelopeTest.php`
- `tests/Feature/MobileApiContractCatalogueTest.php`
- `tests/Unit/MobileApiResponseTest.php`

Fresh checks for this phase:

```bash
php artisan route:list --except-vendor
php artisan test --compact
vendor/bin/pint --dirty --format agent
npm run build
```
