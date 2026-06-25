# API v1 Notifications Contract

Updated: 2026-06-25

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

Target User Roles are defined in `../../docs/user-roles.md`: notification
preferences, delivery state, and device registration must respect role and
account-state visibility.

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
