# API v1 Features Contract

Updated: 2026-06-26

Status: documented. Endpoint is planned for Phase 8.

Product Vision is defined in `../../docs/product-vision.md`: this contract
keeps important mobile capabilities feature-controlled by Admin/API.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract is the API-first expression of the feature-controlled platform.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

Target User Roles are defined in `../../docs/user-roles.md`: feature outcomes
must resolve role and account-state access into mobile-safe states.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: feature outcomes
must explain stakeholder value from rollout control, tenant adoption, mobile
clarity, support explanation, billing entitlements, and security boundaries.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: feature authority and rollout decisions
stay in Admin/API while mobile renders resolved enabled, disabled, blocked,
deprecated, or update-required states.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to feature
control, API contracts, billing/subscription logic, mobile version rules,
support/report visibility, audit history, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports
API-derived feature visibility, disabled/blocked/deprecated/update-required
feedback, navigation shaping, cache freshness, and offline-limited messaging.

## Purpose

Feature endpoints expose resolved mobile-safe feature outcomes. Mobile never
receives raw global, tenant, user, plan, version, cohort, maintenance, or
emergency flag internals.

## Planned Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/features` | Return resolved feature availability for the current context. | mobile token |

## Success Data

The response returns `features`, keyed by feature code. Each feature includes
`state`, `visible`, `enabled`, `reason`, `next_action`, `minimum_app_version`,
`offline_behavior`, and optional `message`.

Allowed states include `hidden`, `visible`, `disabled`, `blocked`, `beta`,
`deprecated`, `update_required`, `offline_limited`, and `emergency_disabled`.

## Gates

Resolution must apply safety and maintenance rules, global defaults, tenant
overrides, plan limits, role/permission rules, user overrides, app-version and
device rules, cohort rules, and offline limitations.

## Offline Behavior

Mobile may cache resolved features with freshness metadata. A stale feature
cache cannot broaden access and must hide or disable risky actions when unsure.

## Audit

Audit admin feature changes, tenant overrides, user overrides, emergency
disable, rollout changes, and support-visible denials.

## Tests

Phase 8 should verify resolution order, disabled mobile states, stale-cache
behavior, and no raw flag layers in API responses.
