# API Contracts

Final Optimized SaaS Blueprint is defined in `../../docs/final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

This directory records mobile-facing API contracts before endpoint
implementation. The contracts are the bridge between the Admin/API control
plane and the NativePHP mobile client.

Product Vision is defined in `../../docs/product-vision.md`: API contracts exist
to turn central SaaS authority into predictable mobile behavior.

Product Positioning is defined in `../../docs/product-positioning.md`: these
contracts are what make the product API-first instead of web-only or
mobile-only.

Core Product Principles are defined in `../../docs/product-principles.md`:
every contract must preserve admin authority, tenant isolation, API-only mobile
behavior, feature control, useful offline behavior, secure defaults, simple
mobile UX, documentation-first planning, and modular expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: every contract must state the API purpose,
expected operating context, predictable response states, mobile-friendly error
meaning, sync/conflict behavior where relevant, and tenant-boundary protection.

Target User Roles are defined in `../../docs/user-roles.md`: every contract must
describe role and account-state effects as mobile-safe outcomes, not raw role
authority.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: every contract must
name the stakeholder value it supports and connect that value to admin control,
mobile access, offline sync, notifications, reports, security, or feature flags
without exposing raw authority.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: every contract must state which behavior
is API-only, which mobile state may be cached or queued, and which authority
stays in Admin/API.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: every contract must identify which
control-plane responsibility owns the request, response, error, sync, support,
billing, report, audit, or security outcome.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: every contract must also name
which mobile responsibility consumes the outcome as UX, local session, cache,
offline action, NativePHP capability, navigation, permission prompt, sync
status, draft, feedback, or feature visibility.

Mobile UX Principles live in `../../docs/mobile-ux-principles.md`. Every
contract should return mobile-safe states that support simple NativePHP
navigation, loading/offline clarity, thumb-friendly actions, secure sessions,
feature visibility, and native permission education.

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

Booking Logic is defined in `../../docs/booking-logic.md`:
service selection, availability logic, booking requests, confirmation,
cancellation, reschedule, reminders, admin schedule control, tenant rules,
and mobile offline limitations must remain tenant-enabled, plan-controlled,
permission-aware, availability-safe, schedule-conflict-safe, reminder-safe,
offline-limited, privacy-safe, auditable, and Admin/API-authoritative.

Commerce Logic is defined in `../../docs/commerce-logic.md`:
catalog browsing, cart behavior, checkout principles, hosted payment
boundaries, order lifecycle, invoice/receipt principles, subscription
upsell, admin product/control, and mobile offline limitations must remain
tenant-enabled, plan-controlled, permission-aware, price-safe,
inventory-safe, hosted-payment-safe, offline-limited, privacy-safe,
auditable, and Admin/API-authoritative.

Messaging And Community Logic is defined in `../../docs/messaging-community-logic.md`:
conversation behavior, support chat behavior, message attachments,
moderation, reports/abuse flow, notification behavior, offline message
drafts, admin visibility boundaries, and privacy principles must remain
tenant-enabled, plan-controlled, permission-aware, moderation-ready,
abuse-report-safe, notification-safe, offline-draft-safe, privacy-safe,
auditable, and Admin/API-authoritative.

AI Feature Logic is defined in `../../docs/ai-feature-logic.md`:
AI assistant purpose, summarization, categorization, smart suggestions,
moderation assistance, report generation assistance, admin AI controls,
tenant opt-in, privacy, and human-review principles must remain
tenant-enabled, plan-controlled, permission-aware, opt-in-only,
provider-neutral, human-reviewed, privacy-safe, audit-ready,
rate-limited, cost-aware, and Admin/API-authoritative.

Acceptance Principles are defined in `../../docs/acceptance-principles.md`:
every feature must document purpose, admin control, mobile behavior,
API dependency, offline behavior, permission behavior, feature flag
behavior, tenant behavior, error behavior, security behavior, and
documentation requirements before implementation planning begins.

Risk Map is defined in `../../docs/risk-map.md`:
API dependency, offline sync, tenant isolation, mobile secure storage,
NativePHP plugin availability, app store releases, forced updates,
feature flag mistakes, billing restrictions, admin misconfiguration,
support access, privacy, and data conflicts must document prevention
principles and documentation requirements before implementation.

Testing Strategy Principles are defined in `../../docs/testing-strategy-principles.md`:
future tests for API contracts, admin controls, mobile feature visibility,
permissions, feature flags, remote config, authentication, tenant isolation,
offline sync, conflict behavior, native feature fallbacks, notification flows,
billing rules, and app version rules must map to documented authority, risk,
and user-visible behavior before implementation.

Release And Versioning Principles are defined in `../../docs/release-versioning-principles.md`:
API versioning, mobile app versioning, admin releases, feature rollout,
rollback, app store release, forced update, documentation update, and Git
change-history decisions must preserve documented authority, compatibility,
rollback, support, audit, and user-visible behavior before release.

Documentation Audit is defined in `../../docs/documentation-audit.md`:
project documentation for two-system architecture, Admin/API authority, mobile
client execution, API-first communication, feature flags, remote config,
tenancy, permissions, offline sync, NativePHP features, notifications, billing,
support, reports, security, risks, and release principles must use consistent
authority language and resolve contradictions before implementation.

Feature Dependency Map is defined in `../../docs/feature-dependency-map.md`:
major features must document dependencies on authentication, tenant context,
permissions, feature flags, remote config, API availability, offline cache,
NativePHP permissions, subscription plan, and admin settings before
implementation planning or release decisions.

Logistics Delivery Logic is defined in `../../docs/logistics-delivery-logic.md`:
delivery job lifecycle, pickup flow, drop-off flow, proof of delivery,
scan validation, location check-in, failed delivery reasons, offline
behavior, and admin monitoring must remain tenant-enabled, plan-controlled,
permission-aware, scan-safe, location-purpose-limited, offline-clear,
privacy-safe, auditable, and Admin/API-authoritative.

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

Admin Control Center logic lives in `../../docs/admin-control-center-logic.md`.
Every contract that exposes tenant, user, role, permission, feature, config,
version, maintenance, force update, sync, notification, report, billing, or
support behavior should map the admin control to a mobile-safe API outcome.

Feature Flag Logic lives in `../../docs/feature-flag-logic.md`. Every contract
that exposes feature availability should return resolved mobile-safe states
rather than raw global, tenant, user, plan, version, device, cohort,
maintenance, or emergency flag internals.

Remote Configuration Logic lives in
`../../docs/remote-configuration-logic.md`. Every contract that exposes config
should return resolved values, config version, freshness/compatibility metadata,
and safe fallback/error behavior rather than raw admin config layers.

Mobile Version Control Logic lives in
`../../docs/mobile-version-control-logic.md`. Every contract that exposes
version or maintenance policy should return resolved mobile-safe states,
store/update links, user-safe messages, support context, and stale-client error
behavior rather than raw admin version rules.

Admin Safety Principles live in `../../docs/admin-safety-principles.md`. Every
contract that can be affected by dangerous admin actions should keep
confirmation, audit history, impact preview, mobile impact preview, rollback,
and tenant isolation visible before implementation.

## Versioning

Mobile contracts are grouped by API version. The first implementation target is:

```text
/api/v1/mobile
```

Contracts should be additive where possible. Breaking behavior must be governed
through app-version policy before old mobile clients lose support.

## Implemented Foundation

- [v1-foundation.md](v1-foundation.md) defines the implemented
  `GET /api/v1/mobile/status` endpoint, implemented
  `GET /api/v1/mobile/contracts` catalogue endpoint, and the shared
  success/error envelope.
- [v1-auth.md](v1-auth.md) defines the implemented mobile auth, registration,
  refresh, logout, logout-all, current-user, and profile endpoints.
- [v1-records.md](v1-records.md) defines the partial implemented records
  foundation for tenant-scoped list, create, detail, update, archive, restore,
  category/tag resolution, note append, attachment metadata, and activity
  timeline responses.

## Documented v1 Contract Groups

- [v1-auth.md](v1-auth.md)
- [v1-bootstrap.md](v1-bootstrap.md)
- [v1-tenancy.md](v1-tenancy.md)
- [v1-features.md](v1-features.md)
- [v1-remote-config.md](v1-remote-config.md)
- [v1-app-version-maintenance.md](v1-app-version-maintenance.md)
- [v1-records.md](v1-records.md)
- [v1-sync.md](v1-sync.md)
- [v1-notifications.md](v1-notifications.md)
- [v1-support.md](v1-support.md)
- [v1-billing.md](v1-billing.md)
- [v1-reports.md](v1-reports.md)
- [v1-diagnostics.md](v1-diagnostics.md)

## Standard Success Envelope

All mobile API success responses should use the same outer shape:

```json
{
  "success": true,
  "data": {},
  "meta": {
    "api_version": "v1",
    "server_time": "2026-06-25T00:00:00Z"
  }
}
```

`data` contains the shaped resource or command result. `meta` contains only
mobile-useful metadata such as API version, server time, cursor, freshness,
config version, sync cursor, or next action.

## Standard Error Envelope

All mobile API errors should use a predictable shape:

```json
{
  "success": false,
  "error": {
    "code": "forbidden",
    "message": "This action is not available for your account.",
    "category": "permission",
    "next_action": "contact_admin"
  },
  "meta": {
    "api_version": "v1",
    "server_time": "2026-06-25T00:00:00Z"
  }
}
```

Allowed categories include `validation`, `unauthenticated`, `permission`,
`tenant`, `feature`, `billing`, `version`, `maintenance`, `conflict`,
`stale_client`, `rate_limit`, `retry_later`, and `server_error`.

## Contract File Rule

Each v1 contract group has a Markdown file. Future implementation phases should
update the relevant file before adding or changing endpoints:

- `v1-foundation.md`
- `v1-auth.md`
- `v1-bootstrap.md`
- `v1-tenancy.md`
- `v1-features.md`
- `v1-remote-config.md`
- `v1-app-version-maintenance.md`
- `v1-records.md`
- `v1-sync.md`
- `v1-notifications.md`
- `v1-support.md`
- `v1-billing.md`
- `v1-reports.md`
- `v1-diagnostics.md`

Each file should define purpose, request shape, response shape, error states,
tenant boundary, permission/feature/billing/version gates, offline behavior,
audit expectations, tests, and mobile UI effects.
