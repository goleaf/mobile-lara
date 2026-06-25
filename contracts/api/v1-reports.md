# API v1 Reports Contract

Updated: 2026-06-25

Status: documented. Endpoint is planned for Phase 25.

Product Vision is defined in `../../docs/product-vision.md`: this contract
supports scalable SaaS reporting while protecting tenant and role boundaries on
mobile.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the SaaS control center by exposing only mobile-safe report
summaries from tenant-scoped authority.

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
