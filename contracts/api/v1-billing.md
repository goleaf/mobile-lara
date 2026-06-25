# API v1 Billing Contract

Updated: 2026-06-25

Status: documented. Endpoint is planned for Phase 23.

Product Vision is defined in `../../docs/product-vision.md`: this contract
keeps billing and entitlement authority centralized while mobile receives only
safe plan outcomes.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the SaaS control center by keeping billing and plan state
server-controlled while mobile receives clear entitlement outcomes.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

Target User Roles are defined in `../../docs/user-roles.md`: billing outcomes
must distinguish billing manager authority, tenant admin visibility, support
context, and mobile entitlement messages.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: billing outcomes
connect commercial value to plan limits, entitlement-driven feature flags,
mobile-safe access messages, reports, security, and billing/operations insight.

## Purpose

Billing endpoints expose mobile-safe plan and subscription state. Admin/API
owns plans, subscriptions, usage, invoice placeholders, plan-based feature
availability, and suspended/trial/expired behavior.

## Planned Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/billing/subscription` | Return current tenant/user billing state for mobile presentation. | mobile token |

## Success Data

The response returns `plan`, `subscription_status`, `trial`, `limits`,
`usage`, `available_actions`, `billing_portal`, and `feature_impacts`.

## Gates

Billing behavior is constrained by tenant subscription, role/permission,
feature flags, plan limits, app version, maintenance, and support policy.

## Offline Behavior

Mobile may display last-known billing state with freshness metadata. Expired,
suspended, or unknown billing states must fail closed for paid features.

## Audit

Audit plan changes, subscription state changes, usage events where documented,
billing portal access, and feature denials caused by billing.

## Tests

Phase 23 should verify plan-feature resolution, trial/expired/suspended
states, no raw provider secrets, and bootstrap subscription status.
