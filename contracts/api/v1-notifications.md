# API v1 Notifications Contract

Updated: 2026-06-26

Status: documented. Endpoints are planned for Phase 21.

Product Vision is defined in `../../docs/product-vision.md`: this contract
keeps notification orchestration centralized while mobile handles device
registration and user-facing delivery state.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the combined platform by keeping notification policy
centralized while mobile presents safe user-facing delivery behavior.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must keep registration,
preferences, inbox state, delivery feedback, mobile errors, and tenant-safe
notification visibility API-shaped and predictable.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: notification behavior must
document admin mobile effect, device permission UX, API dependency, offline
display expectations, permission owner, delivery/support risks, and audit needs
before implementation.

Target User Roles are defined in `../../docs/user-roles.md`: notification
preferences, delivery state, and device registration must respect role and
account-state visibility.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: notification
contracts create value when platform, tenant, mobile, support, and billing
messages reach the right audience without leaking tenant or admin authority.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: notification targeting and delivery truth
stay in Admin/API while mobile owns device registration UX, permission UX, and
safe local display.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to
notification orchestration, users and permissions, tenant management,
feature/version/billing gates, support/reporting visibility, and audit history.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports native
notification permission UX, push-token registration feedback, inbox
presentation, unread status, local display state, and feature-gated
notification visibility.

Mobile UX Principles are defined in `../../docs/mobile-ux-principles.md`: this
contract should support mobile-first navigation, simple screens, clear
loading/offline states, thumb-friendly controls, minimum typing, fast actions,
secure sessions, feature visibility, and native permission education.

Mobile App Shell Logic is defined in `../../docs/mobile-app-shell-logic.md`:
shell states must coordinate welcome, authenticated, locked, offline, maintenance, forced update, tenant
switching, sync-in-progress, permission-blocked, and feature-disabled behavior
before implementation.

Mobile Dashboard Logic is defined in `../../docs/mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Admin Control Center Logic is defined in
`../../docs/admin-control-center-logic.md`: this contract must keep
notification templates, channels, targeting, quiet hours, priority, escalation,
suppression, and delivery visibility scoped, authorized, auditable, and exposed
to mobile only as resolved API outcomes.

Feature Flag Logic is defined in `../../docs/feature-flag-logic.md`:
notification inbox, push registration, deep links, channel visibility,
targeting affordances, and notification-related mobile features must follow
resolved flag state, rollout, plan, permission, version, and disabled-state
rules.

Remote Configuration Logic is defined in
`../../docs/remote-configuration-logic.md`: notification preference labels,
permission prompts, quiet-hour copy, local display guidance, deep-link copy,
and support explanations may be tuned by resolved config while targeting and
delivery truth remain Admin/API authority.

## Purpose

Notification endpoints manage notification preferences, push token
registration, token revocation, inbox state, unread count, mark-read actions,
deletes, and deep-link payloads.

## Planned Routes

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/notifications` | List notification inbox items. | mobile token |
| POST | `/api/v1/mobile/notifications/push-tokens` | Register a push token. | mobile token |
| DELETE | `/api/v1/mobile/notifications/push-tokens/{token}` | Revoke a push token. | mobile token |
| PATCH | `/api/v1/mobile/notifications/{notification}/read` | Mark a notification read. | mobile token |

## Success Data

Responses return notification `id`, `type`, `title`, `body`, `read_at`,
`deep_link`, `created_at`, `actions`, and `unread_count` where useful.

## Gates

Notifications are controlled by tenant membership, user preferences, feature
flags, push permission, app version, maintenance, billing state, and support
policy.

## Offline Behavior

Mobile may cache inbox items and read state locally. Mutations queued offline
must sync before server counts are trusted.

## Audit

Audit push token registration/revocation, campaign send placeholders,
notification open tracking, read-state changes, and delete actions where
required.

## Tests

Phase 21 should verify token ownership, tenant isolation, unread counts,
preference effects, read/delete actions, and deep-link safety.
