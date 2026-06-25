# API v1 Bootstrap Contract

Updated: 2026-06-26

Status: implemented as the Phase 10 foundation endpoint with Phase 7
role-derived permission payloads and Phase 8 feature-flag resolution.
Domain-specific config, billing, notification, sync, and full
permission-management modules still need to replace the remaining explicit
foundation defaults.

Product Vision is defined in `../../docs/product-vision.md`: this contract is
the main API path for turning central SaaS control into mobile operating
context.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the combined product by giving the mobile workforce/client
platform one API-first operating context from the SaaS control center.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must return predictable
operating context for user, tenant, permissions, feature flags, config, version
rules, sync policy, notification policy, support state, and entitlement
outcomes.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: bootstrap context must
document its feature purpose, admin mobile effects, mobile screen dependencies,
online/offline freshness behavior, permission owners, and risks before
implementation.

Target User Roles are defined in `../../docs/user-roles.md`: bootstrap context
must return role-derived capability state and account-state limits as
mobile-safe outcomes.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: bootstrap context
is where stakeholder value first becomes mobile-visible through allowed
features, tenant context, reports, notifications, sync policy, security, and
admin-controlled state.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: bootstrap returns Admin/API decisions as
mobile-safe operating context, and cached bootstrap state is never final
authority.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract resolves tenant
management, users and permissions, feature control, remote configuration,
mobile version rules, notification policy, billing/subscription, support,
reporting, sync/conflict, and security into one mobile-safe context.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supplies the
operating context mobile uses for navigation, cache freshness, sync display,
feature visibility, local feedback, and safe startup recovery.

Admin Control Center Logic is defined in
`../../docs/admin-control-center-logic.md`: this contract must resolve tenant,
user, role, permission, feature, config, version, maintenance, sync,
notification, report, billing, and support controls into scoped, authorized,
auditable, mobile-safe API outcomes.

## Purpose

Bootstrap gives the mobile client one resolved operating context after login,
app start, tenant switch, and manual refresh. It must expose decisions, not raw
admin configuration layers.

## Implemented Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/bootstrap` | Return the current mobile operating context. | mobile token |

## Success Data

The response must include:

- `user`
- `current_tenant`
- `available_tenants`
- `permissions`
- `features`
- `remote_config`
- `app_version`
- `maintenance`
- `subscription`
- `notification_preferences`
- `sync`
- `unread_notification_count`

The current foundation implementation returns real authenticated user,
device-session, current tenant, available tenant membership context, a
server-derived permission payload based on the current active tenant role, and
resolved feature-flag outcomes. It still returns explicit disabled or pending
states for remote config, billing, notifications, sync, and
version/maintenance modules whose authoritative Admin/API data models are not
implemented yet. Mobile must treat those states as fail-closed outcomes.

`permissions` includes:

- `status`: `resolved` when there is a current active tenant, otherwise
  `no_active_tenant`.
- `source`: currently `tenant_role_registry`.
- `tenant_id` and `current_role` for the role that produced the payload.
- `roles`: tenant-scoped role summaries for available memberships.
- `abilities`: nested boolean capability state for mobile presentation.
- `ability_list`: granted ability keys in dot notation for simple lookups.

## Metadata

`meta` should include `api_version`, `server_time`, `bootstrap_version`,
`config_version`, `features_version`, `sync_cursor`, and freshness timestamps.

## Gates

Bootstrap is constrained by tenant lifecycle, user access state,
permissions, feature flags, remote config, app-version policy, maintenance,
subscription status, notification settings, and sync rules.

## Offline Behavior

Mobile may cache the last successful bootstrap with freshness metadata. Stale
bootstrap cannot grant new authority. If bootstrap is unavailable, mobile must
fail closed for sensitive actions and clearly label cached state.

## Audit

Bootstrap is read-mostly. Audit tenant switch, stale-client denial, suspended
access, forced update, maintenance block, and suspicious device context.

## Tests

Automated coverage:

- `apps/api-admin/tests/Feature/MobileBootstrapApiTest.php`
- `apps/mobile-client/tests/Feature/MobileBootstrapServiceTest.php`

Fresh checks:

```bash
cd apps/api-admin && php artisan test --compact --filter=MobileBootstrapApiTest
cd apps/mobile-client && php artisan test --compact --filter=MobileBootstrapServiceTest
```
