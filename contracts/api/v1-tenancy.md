# API v1 Tenancy Contract

Updated: 2026-06-25

Status: documented. Endpoints are planned for Phase 6.

Product Vision is defined in `../../docs/product-vision.md`: this contract
keeps tenant isolation and tenant access controlled by Admin/API while mobile
shows only allowed tenant context.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the tenant-based product by keeping workspace access and
tenant switching server-controlled.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

Target User Roles are defined in `../../docs/user-roles.md`: tenant access and
tenant switching must resolve platform, tenant, invited, suspended, and
guest/pre-login boundaries server-side.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: tenancy contracts
create value by preserving tenant isolation while enabling governed mobile
access, tenant reports, support context, billing visibility, notifications, and
feature flags.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: tenant authority stays in Admin/API while
mobile displays current tenant context and switch choices returned by API.

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
