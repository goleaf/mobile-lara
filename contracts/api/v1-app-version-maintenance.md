# API v1 App Version And Maintenance Contract

Updated: 2026-06-25

Status: documented. Endpoint is planned for Phase 11.

## Purpose

App-version and maintenance endpoints tell mobile whether the current build can
operate safely. Admin/API owns minimum versions, optional update prompts,
forced updates, blocked versions, maintenance state, support messaging, and
rollback.

## Planned Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/app-version` | Return update and maintenance state for the reported app context. | public with mobile context |

## Request Context

Mobile must send app version, platform, build number, device identifier, and
tenant/user context when authenticated.

## Success Data

The response returns `state`, `minimum_supported_version`,
`latest_version`, `store_url`, `message`, `support_url`, `retry_after`,
`allowed_actions`, and `logout_allowed`.

Allowed states include `current`, `supported`, `optional_update`,
`recommended_update`, `deprecated`, `force_update`, `blocked`,
`maintenance`, `internal_only`, and `stale_client`.

## Gates

Version state can differ by platform, tenant, feature risk, API contract,
security incident, billing state, sync risk, rollout cohort, and maintenance.

## Offline Behavior

Mobile may continue safe local reads and drafts if the last-known policy allows
it. Risky writes, sync replay, and protected features require fresh API
approval when the version state is unknown or stale.

## Audit

Audit version rule changes, maintenance start/end, forced update, rollback,
stale-client denial, and support-visible impact.

## Tests

Phase 11 should verify minimum version checks, optional update banners, forced
update blocks, maintenance responses, and rollback behavior.
