# API v1 Support Contract

Updated: 2026-06-25

Status: documented. Endpoints are planned for Phase 24.

## Purpose

Support endpoints let mobile users create tickets, view tickets, add messages,
attach allowed files, and share privacy-safe diagnostics while Admin/API owns
assignment, status, priority, support visibility, and audit.

## Planned Routes

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/support/tickets` | List current user's tenant-scoped tickets. | mobile token |
| POST | `/api/v1/mobile/support/tickets` | Create a support ticket. | mobile token |
| GET | `/api/v1/mobile/support/tickets/{ticket}` | Show ticket detail and messages. | mobile token |
| POST | `/api/v1/mobile/support/tickets/{ticket}/messages` | Add a message or attachment metadata. | mobile token |

## Success Data

Responses return ticket `id`, `subject`, `status`, `priority`, `assignment`,
`messages`, `attachments`, `allowed_actions`, and `support_context`.

## Gates

Support is controlled by tenant status, user permissions, feature flags,
remote config support links, app version, upload limits, subscription state,
and privacy rules.

## Offline Behavior

Mobile may save support drafts and queue safe create/message intents. It must
not expose private diagnostics unless the user confirms export/share.

## Audit

Audit ticket create, message create, attachment metadata, assignment, status,
priority, support access, and diagnostic export.

## Tests

Phase 24 should verify tenant isolation, assignment/status transitions,
attachment limits, privacy filtering, offline drafts, and audit events.
