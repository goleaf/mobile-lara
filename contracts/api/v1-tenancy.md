# API v1 Tenancy Contract

Updated: 2026-06-25

Status: documented. Endpoints are planned for Phase 6.

## Purpose

Tenancy endpoints keep tenant authority on the Admin/API system. Mobile may
display and switch allowed tenants, but every server response remains
tenant-scoped and validates membership server-side.

## Planned Routes

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/tenants` | List tenants available to the current user. | mobile token |
| POST | `/api/v1/mobile/tenants/current` | Switch current tenant context. | mobile token |

## Success Data

Tenant list responses return `tenants`, each with `id`, `name`, `status`,
`role_summary`, `subscription_state`, and `switchable`.

Tenant switch responses return `current_tenant`, refreshed `permissions`,
`features`, `remote_config`, `sync`, and `next_bootstrap_required`.

## Gates

Tenant access is controlled by membership, invitation state, suspended state,
tenant lifecycle, role, permission, subscription, maintenance, and app-version
policy.

## Offline Behavior

Mobile can show the last known tenant and separate local cache by tenant.
Switching tenants requires API confirmation before protected actions use the
new tenant context.

## Audit

Tenant switch, failed tenant switch, invitation acceptance, suspended access,
and cross-tenant denial are audit events.

## Tests

Phase 6 should verify tenant isolation, route model binding or opaque IDs,
membership denial, tenant state denial, and cache refresh triggers.
