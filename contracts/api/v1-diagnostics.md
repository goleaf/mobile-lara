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

Offline-First Principles are defined in `../../docs/offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

Offline UX Logic is defined in `../../docs/offline-ux-logic.md`:
offline UX must calmly explain banners, disabled online-only actions,
local drafts, pending indicators, retry, sync success or failure,
saved-local versus synced state, and data-loss prevention whenever
connection changes.

Records/Content Module Logic is defined in `../../docs/records-content-module-logic.md`:
records are tenant-scoped business content with API-owned lifecycle,
notes, attachments, activity, tags, categories, status, offline draft or
sync behavior, admin controls, permissions, feature flags, audit, and
reporting boundaries.

Search Logic is defined in `../../docs/search-logic.md`:
search stays tenant-scoped, permission-aware, feature-controlled,
privacy-preserving, and explicit about local-cache limits versus
API-authoritative results, including recent searches, saved filters, filtering,
sorting, scan-to-search, offline limits, and admin-controlled boundaries.

Forms And Drafts Logic is defined in `../../docs/forms-drafts-logic.md`:
mobile forms must stay simple, validated, autosave-aware, offline-draft
safe, API-submitted, admin-controlled, and explicit about local-save versus
server-accepted state so user work is protected without bypassing authority.

Notifications Logic is defined in `../../docs/notifications-logic.md`:
notification targeting, delivery policy, push behavior, in-app inbox,
read/unread state, deep links, preferences, offline behavior, and tenant or
permission boundaries must remain Admin/API-authoritative and mobile-safe.

Support System Logic is defined in `../../docs/support-system-logic.md`:
support requests, support messages, attachments, tenant context, support-agent
visibility, audit, notifications, and offline support drafts must remain
tenant-scoped, least-privilege, privacy-safe, and Admin/API-authoritative.

Billing And Plan Logic is defined in `../../docs/billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature states,
and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

Reporting Logic is defined in `../../docs/reporting-logic.md`:
admin measurements, tenant-admin measurements, mobile-visible summaries,
privacy boundaries, date ranges, exports, feature usage, sync health,
notification, support, and billing reports must remain tenant-scoped,
permission-aware, privacy-safe, auditable, and Admin/API-authoritative.

Native Feature Strategy is defined in `../../docs/native-feature-strategy.md`:
NativePHP capability use, logical service boundaries, browser/development
fallbacks, permission education, admin feature-flag control, native failure
UX, and offline sync behavior must remain feature-scoped, tenant-safe,
privacy-aware, fallback-safe, and Admin/API-authoritative.

Camera And Media Logic is defined in `../../docs/camera-media-logic.md`:
photo capture, media selection, media preview, record/support attachments,
offline media storage, upload queues, feature-flag control, permission
denial, size limits, and privacy behavior must remain tenant-scoped,
permission-aware, fallback-safe, queue-safe, privacy-safe, and
Admin/API-authoritative.

Scanner Logic is defined in `../../docs/scanner-logic.md`:
QR/barcode scan-to-search, scan-to-create, scan-to-validate, scan history,
offline scanning, invalid scan handling, duplicate scan handling, admin
feature flags, and camera/permission dependency behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear,
duplicate-safe, privacy-safe, and Admin/API-authoritative.

Geolocation Logic is defined in `../../docs/geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Device, Network, And Diagnostics Logic is defined in `../../docs/device-network-diagnostics-logic.md`:
device information use, network status use, offline detection, diagnostics
export, support troubleshooting context, diagnostics redaction, admin mobile
device visibility, and user-controlled diagnostics sharing must remain
tenant-scoped, permission-aware, support-scoped, privacy-safe, redacted,
audit-ready, and Admin/API-authoritative.

Module Selection Principles are defined in `../../docs/module-selection-principles.md`:
optional industry modules such as field service, logistics, ecommerce,
booking, education, events, support, community/messaging, reports, and AI
assistant must be tenant-enabled, plan-controlled, permission-aware,
mobile-hidden when unavailable, feature-flag-safe, documented before
implementation, and Admin/API-authoritative.

Field Service Logic is defined in `../../docs/field-service-logic.md`:
work order lifecycle, technician mobile flow, check-in/check-out, photos,
notes, future signatures, offline behavior, admin dispatch/control, and
report visibility must remain tenant-enabled, plan-controlled,
permission-aware, offline-clear, evidence-safe, privacy-safe, auditable,
and Admin/API-authoritative.

Voice Note Logic is defined in `../../docs/voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

Sync Lifecycle Logic is defined in `../../docs/sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `../../docs/conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

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
