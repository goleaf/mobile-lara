# API v1 Remote Config Contract

Updated: 2026-06-25

Status: documented. Endpoint is planned for Phase 9.

Product Vision is defined in `../../docs/product-vision.md`: this contract
lets admins adjust safe mobile behavior without turning the mobile app into a
configuration authority.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the feature-controlled and offline-capable mobile platform by
keeping runtime behavior configurable from the control center.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

Target User Roles are defined in `../../docs/user-roles.md`: config outcomes
may vary presentation by role or account state, but must not grant authority.

## Purpose

Remote config endpoints expose validated, resolved, mobile-safe runtime values.
Remote config can tune enabled behavior but cannot grant permissions, bypass
billing, bypass tenant status, or bypass mobile version policy.

## Planned Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/config` | Return resolved remote config for the current mobile context. | mobile token |

## Success Data

The response returns `config`, `config_version`, `freshness`, `compatibility`,
`defaults_used`, and optional `support_context`.

Config may cover sync intervals, upload limits, dashboard widgets, legal links,
support links, notification presentation, app lock behavior, and messages.

## Gates

Config is constrained by global defaults, tenant overrides, plan limits,
permissions, feature flags, app-version policy, maintenance, and emergency
rules.

## Offline Behavior

Mobile may use last-known config with freshness labels. Missing, invalid, or
incompatible config must fall back to bundled safe defaults and fail closed for
sensitive behavior.

## Audit

Audit config publish, tenant override, validation failure, rollback, emergency
override, and retirement.

## Tests

Phase 9 should verify validation, resolved-only payloads, config versioning,
fallback behavior, and stale mobile cache metadata.
