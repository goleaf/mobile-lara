# API v1 Reports Contract

Updated: 2026-06-26

Status: documented. Endpoint is planned for Phase 25.

Product Vision is defined in `../../docs/product-vision.md`: this contract
supports scalable SaaS reporting while protecting tenant and role boundaries on
mobile.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the SaaS control center by exposing only mobile-safe report
summaries from tenant-scoped authority.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must return predictable
report purpose, scoped summaries, freshness metadata, mobile-friendly errors,
and tenant-safe visibility without exposing report/export authority.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: report behavior must document
stakeholder purpose, mobile screen dependency, API context, cache/freshness
behavior, permission owner, export boundary, privacy risks, and audit needs
before implementation.

Target User Roles are defined in `../../docs/user-roles.md`: report summaries
must respect platform, tenant, manager, support, billing, mobile, and blocked
account-state visibility.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: report contracts
must prove value through scoped adoption, operations, sync, support, billing,
security, and feature-usage insight without overexposing raw tenant data.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: report definitions, scope, aggregation,
and export authority stay in Admin/API while mobile receives only allowed
summaries.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to reporting,
tenant management, users and permissions, billing/support visibility, API
contracts, audit history, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports
mobile-safe report summaries, loading/empty/error states, cache freshness,
navigation visibility, and local feedback without giving mobile report or
export authority.

## Purpose

Reports endpoints expose only permission-safe tenant and user report summaries
to mobile. Admin/API owns report definitions, aggregations, export authority,
and tenant boundaries.

## Planned Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/reports` | Return allowed report summaries. | mobile token |

## Success Data

The response returns `reports`, `filters`, `generated_at`, `freshness`,
`allowed_exports`, and `limited_by` where feature, permission, or plan limits
apply.

## Gates

Reports are controlled by tenant membership, report permissions, feature flags,
remote config, subscription status, app version, maintenance, and export
policy.

## Offline Behavior

Mobile may cache read-only summaries with freshness labels. It cannot create
trusted report exports while offline.

## Audit

Audit report access, export requests, denied report access, and support/admin
report viewing where relevant.

## Tests

Phase 25 should verify tenant isolation, permission filtering, feature/plan
limits, export denial, and cached summary freshness.
