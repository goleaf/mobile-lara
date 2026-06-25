# API v1 Billing Contract

Updated: 2026-06-26

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

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: billing authority, quota checks, and
entitlement decisions stay in Admin/API while mobile shows allowed, blocked,
quota, or contact-admin outcomes.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to
billing/subscription logic, feature control, tenant management, API contracts,
reporting, audit history, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports
mobile-safe entitlement feedback, quota warnings, contact-admin/support prompts,
feature visibility, navigation limits, and local blocked-state messages without
giving mobile billing authority.

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
