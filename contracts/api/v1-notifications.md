# API v1 Notifications Contract

Updated: 2026-06-25

Status: documented. Endpoints are planned for Phase 21.

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
