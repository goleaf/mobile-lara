# API v1 Support Contract

Updated: 2026-06-26

Status: documented. Endpoints are planned for Phase 24.

Product Vision is defined in `../../docs/product-vision.md`: this contract
keeps support operations centralized while mobile users receive clear help and
case status.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the combined product by connecting mobile help flows to the
SaaS support control surface.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must keep support actions,
diagnostic submission, ticket state, error handling, sync/config/version
context, and tenant-safe support visibility API-defined.

Target User Roles are defined in `../../docs/user-roles.md`: support flows must
separate support-agent controls from tenant, billing, mobile, invited,
suspended, and guest/pre-login experiences.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: support contracts
create value by connecting mobile help, diagnostics, notifications, sync state,
reports, security boundaries, and feature-flag explanation.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: support authority, assignment, visibility,
and escalation stay in Admin/API while mobile owns help entry and safe
diagnostic submission.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to support
operations, diagnostics policy, users and permissions, tenant management, API
contracts, audit history, reporting, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports mobile
help UX, local feedback, safe diagnostic submission, support status display,
sync/config/version context presentation, and recovery guidance.

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
