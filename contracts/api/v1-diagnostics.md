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

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to support
operations, diagnostics policy, security enforcement, audit history, API
contracts, and reporting.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports local
diagnostic presentation, safe device/context collection, submission feedback,
support guidance, sync/config/version evidence display, and privacy-safe local
review.

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

Mobile Settings Logic is defined in `../../docs/mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `../../docs/mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

Authentication Principles are defined in `../../docs/authentication-principles.md`:
mobile login must happen through the API only; access and refresh tokens must
use secure storage; refresh, logout, logout-all-devices, tenant selection,
session expiry, offline already-authenticated behavior, and server revocation
must preserve Admin/API authority before implementation.

Mobile App Lock Principles are defined in `../../docs/mobile-app-lock-principles.md`:
the mobile client must lock on security-sensitive lifecycle, timeout,
account, tenant, offline-cache, and admin-policy conditions; require
confirmation for sensitive areas; use biometric or PIN unlock only as local
protection; handle failed attempts, logout, admin-disabled biometrics, and
offline cached data without bypassing Admin/API authority.

Role And Permission Logic is defined in `../../docs/role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact
with feature flags as separate gates; suspended users and suspended tenants
fail closed without bypassing tenant isolation.

Audit Logic is defined in `../../docs/audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

Data Privacy Principles are defined in `../../docs/data-privacy-principles.md`:
tenant isolation, least privilege, secure local mobile data, secure native
storage, export and deletion boundaries, support access limits, admin
visibility boundaries, privacy-by-default behavior, and mobile diagnostics
privacy limits must protect users and tenants without turning mobile cache,
support views, or audit history into uncontrolled data exposure.

Tenant Lifecycle Logic is defined in `../../docs/tenant-lifecycle-logic.md`:
tenant creation, onboarding, trial, active, suspended, archived,
billing-blocked, deletion/requested deletion, and restore states must be
Admin/API-owned lifecycle decisions that mobile presents as safe,
tenant-scoped, billing-aware, supportable states without inventing local
tenant authority.

Tenant Admin Logic is defined in `../../docs/tenant-admin-logic.md`:
tenant admins may manage tenant-scoped users, invitations, delegated settings,
delegated mobile-feature controls, tenant reports, and tenant support workflows
only inside their tenant; platform-only controls, cross-tenant visibility,
global policy, billing authority, lifecycle authority, app-version policy, and
security posture remain Admin/API-owned boundaries.

Multi-Tenant Mobile Logic is defined in `../../docs/multi-tenant-mobile-logic.md`:
users with more than one tenant choose and remember tenant context through
API-confirmed state; tenant switching, tenant-scoped cache, per-tenant
permissions and feature flags, sync replay, offline behavior, and logout
cleanup must preserve tenant isolation and never turn mobile-local state
into tenant authority.

Admin Control Center Logic is defined in
`../../docs/admin-control-center-logic.md`: this contract must keep diagnostic
collection, support visibility, privacy boundaries, recovery guidance, and
case-context controls scoped, authorized, auditable, and exposed to mobile only
as resolved API outcomes.

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
