# API v1 Remote Config Contract

Updated: 2026-06-26

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

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must return resolved config,
config version, freshness, compatibility, fallback state, mobile-friendly
errors, and tenant-safe context instead of raw config authority.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: remote config behavior must
document admin change intent, mobile effect, API dependency, cache/offline
fallback, permission ownership, invalid-config risk, support, audit, and
rollback before implementation.

Target User Roles are defined in `../../docs/user-roles.md`: config outcomes
may vary presentation by role or account state, but must not grant authority.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: remote config
creates value by safely tuning mobile behavior, notifications, sync messaging,
support guidance, and feature presentation without turning config into
authority.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: config authority and validation stay in
Admin/API while mobile caches resolved config with freshness and fallback state.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to remote
configuration, feature control, API contracts, support operations, audit
history, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports local
config cache, freshness display, safe fallback feedback, feature presentation,
permission-purpose copy, and sync/navigation tuning without giving mobile
global configuration authority.

Mobile UX Principles are defined in `../../docs/mobile-ux-principles.md`: this
contract should support mobile-first navigation, simple screens, clear
loading/offline states, thumb-friendly controls, minimum typing, fast actions,
secure sessions, feature visibility, and native permission education.

Admin Control Center Logic is defined in
`../../docs/admin-control-center-logic.md`: this contract must keep remote
config, defaults, tenant overrides, rollback, invalid-config behavior, support
meaning, and mobile-safe presentation controls scoped, authorized, auditable,
and exposed to mobile only as resolved API outcomes.

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
