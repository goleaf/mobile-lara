# API v1 Diagnostics Contract

Updated: 2026-06-26

Status: documented. Endpoint is planned for Phase 28.

Product Vision is defined in `../../docs/product-vision.md`: this contract
supports scalable SaaS operations by giving support safe mobile context without
moving authority to the device.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the SaaS control center and mobile platform by making support
diagnostics operational without weakening tenant or device boundaries.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must keep diagnostics
submission, redaction results, support next actions, mobile-friendly errors,
and tenant-safe visibility API-defined.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: diagnostics behavior must
document mobile collection purpose, API dependency, online/offline submission,
permission owner, privacy/security risks, support visibility, and audit needs
before implementation.

Target User Roles are defined in `../../docs/user-roles.md`: diagnostics must
separate support-agent visibility from tenant, billing, mobile, invited,
suspended, and guest/pre-login visibility.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: diagnostics create
support-team value through safe mobile context, sync visibility, version/config
evidence, security boundaries, and reportable incident patterns.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: mobile may collect safe local diagnostics,
but Admin/API controls acceptance, visibility, redaction, support scope, and
audit.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to support
operations, diagnostics policy, security enforcement, audit history, API
contracts, and reporting.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports local
diagnostic presentation, safe device/context collection, submission feedback,
support guidance, sync/config/version evidence display, and privacy-safe local
review.

## Purpose

Diagnostics endpoints let mobile share privacy-safe troubleshooting context
with support. Mobile owns local diagnostics presentation and export/share, but
Admin/API owns acceptance, support visibility, audit, and privacy boundaries.

## Planned Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| POST | `/api/v1/mobile/diagnostics` | Upload a privacy-filtered diagnostics snapshot. | mobile token |

## Success Data

The response returns `diagnostic_id`, `received_at`, `support_ticket_id`,
`redactions_applied`, and `next_action`.

## Payload Rules

Allowed fields include app version, API base URL, tenant ID, user ID,
feature/config snapshots, network status, sync status, failed sync action
summaries, and device info where safe.

Secrets, tokens, raw private files, exact sensitive payloads, and unredacted
personal data must not be sent.

## Gates

Diagnostics are controlled by support feature flags, permissions, tenant
status, app version, remote config, privacy settings, and support policy.

## Offline Behavior

Mobile may export/share diagnostics locally. Upload requires API availability
and user confirmation when private context is included.

## Audit

Audit diagnostics upload, support access, redaction failure, and linked ticket
visibility.

## Tests

Phase 28 should verify redaction, payload validation, ticket linking, tenant
isolation, and no secrets in accepted snapshots.
