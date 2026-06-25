# API v1 Features Contract

Updated: 2026-06-25

Status: documented. Endpoint is planned for Phase 8.

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
