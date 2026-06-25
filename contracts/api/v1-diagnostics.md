# API v1 Diagnostics Contract

Updated: 2026-06-25

Status: documented. Endpoint is planned for Phase 28.

Product Vision is defined in `../../docs/product-vision.md`: this contract
supports scalable SaaS operations by giving support safe mobile context without
moving authority to the device.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the SaaS control center and mobile platform by making support
diagnostics operational without weakening tenant or device boundaries.

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
