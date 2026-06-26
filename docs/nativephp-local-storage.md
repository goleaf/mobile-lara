# NativePHP Local SQLite Storage

Updated: 2026-06-25

This document defines how local SQLite should fit into the SaaS mobile + admin product. Local storage exists to make the NativePHP mobile client resilient. It does not replace the Admin/API system as the source of business authority.

The storage vision follows [Product Vision](product-vision.md): the mobile app can keep users productive locally, but admin/API policy remains final.

It also supports the [Product Positioning](product-positioning.md): Mobile Lara is an offline-capable mobile system, but offline data is still governed by the SaaS control center and reconciled through the API-first boundary.

The storage rules follow [Core Product Principles](product-principles.md): mobile never bypasses the API, offline-first is used only where useful, tenant isolation remains server-enforced, and secure defaults keep secrets out of local SQLite.

Local storage planning must follow [Documentation-First Architecture](documentation-first-architecture.md). Every cache, draft, offline action, sync state, permission effect, and storage risk should be documented before implementation.

Local storage planning must follow [Admin Control Center Logic](admin-control-center-logic.md). Admin-controlled sync, offline eligibility, maintenance blocks, force-update effects, tenant status, user suspension, feature availability, billing entitlement, and support visibility define when local cache, drafts, and queues are allowed.

Local storage planning must follow [Feature Flag Logic](feature-flag-logic.md). Feature flags decide whether a mobile workflow is read-only offline, draft-only offline, queueable offline, online-only, disabled, blocked, update-required, or emergency-disabled.

Local storage planning must follow [Remote Configuration Logic](remote-configuration-logic.md). Mobile may cache resolved config with version and freshness state, but stale or invalid config cannot authorize protected work.

Local storage planning must follow [Mobile Version Control Logic](mobile-version-control-logic.md). Mobile may preserve safe drafts and cached state during optional or forced update flows, but blocked, outdated, or unknown version policy cannot authorize protected local work or replay.

Local storage planning must follow [Admin Safety Principles](admin-safety-principles.md). Dangerous admin changes should preview mobile cache, draft, queue, offline, sync replay, rollback, and tenant-isolation effects before they change local behavior.

Local storage planning must follow [Mobile UX Principles](mobile-ux-principles.md). Local cache, drafts, queued actions, sync state, stale data, secure session recovery, and native permission flows should appear as clear mobile-first loading/offline states instead of hidden storage mechanics.

Mobile App Shell Logic is defined in `mobile-app-shell-logic.md`:
shell states must coordinate welcome, authenticated, locked, offline, maintenance, forced update, tenant
switching, sync-in-progress, permission-blocked, and feature-disabled behavior
before implementation.

Mobile Dashboard Logic is defined in `mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Mobile Settings Logic is defined in `mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

Authentication Principles are defined in `authentication-principles.md`:
mobile login must happen through the API only; access and refresh tokens must
use secure storage; refresh, logout, logout-all-devices, tenant selection,
session expiry, offline already-authenticated behavior, and server revocation
must preserve Admin/API authority before implementation.

Mobile App Lock Principles are defined in `mobile-app-lock-principles.md`:
the mobile client must lock on security-sensitive lifecycle, timeout,
account, tenant, offline-cache, and admin-policy conditions; require
confirmation for sensitive areas; use biometric or PIN unlock only as local
protection; handle failed attempts, logout, admin-disabled biometrics, and
offline cached data without bypassing Admin/API authority.

Role And Permission Logic is defined in `role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact
with feature flags as separate gates; suspended users and suspended tenants
fail closed without bypassing tenant isolation.

Audit Logic is defined in `audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

Data Privacy Principles are defined in `data-privacy-principles.md`:
tenant isolation, least privilege, secure local mobile data, secure native
storage, export and deletion boundaries, support access limits, admin
visibility boundaries, privacy-by-default behavior, and mobile diagnostics
privacy limits must protect users and tenants without turning mobile cache,
support views, or audit history into uncontrolled data exposure.

Tenant Lifecycle Logic is defined in `tenant-lifecycle-logic.md`:
tenant creation, onboarding, trial, active, suspended, archived,
billing-blocked, deletion/requested deletion, and restore states must be
Admin/API-owned lifecycle decisions that mobile presents as safe,
tenant-scoped, billing-aware, supportable states without inventing local
tenant authority.

Tenant Admin Logic is defined in `tenant-admin-logic.md`:
tenant admins may manage tenant-scoped users, invitations, delegated settings,
delegated mobile-feature controls, tenant reports, and tenant support workflows
only inside their tenant; platform-only controls, cross-tenant visibility,
global policy, billing authority, lifecycle authority, app-version policy, and
security posture remain Admin/API-owned boundaries.

Multi-Tenant Mobile Logic is defined in `multi-tenant-mobile-logic.md`:
users with more than one tenant choose and remember tenant context through
API-confirmed state; tenant switching, tenant-scoped cache, per-tenant
permissions and feature flags, sync replay, offline behavior, and logout
cleanup must preserve tenant isolation and never turn mobile-local state
into tenant authority.

Offline-First Principles are defined in `offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

Offline UX Logic is defined in `offline-ux-logic.md`:
offline UX must calmly explain banners, disabled online-only actions,
local drafts, pending indicators, retry, sync success or failure,
saved-local versus synced state, and data-loss prevention whenever
connection changes.

Records/Content Module Logic is defined in `records-content-module-logic.md`:
records are tenant-scoped business content with API-owned lifecycle,
notes, attachments, activity, tags, categories, status, offline draft or
sync behavior, admin controls, permissions, feature flags, audit, and
reporting boundaries.

Search Logic is defined in `search-logic.md`:
search stays tenant-scoped, permission-aware, feature-controlled,
privacy-preserving, and explicit about local-cache limits versus
API-authoritative results, including recent searches, saved filters, filtering,
sorting, scan-to-search, offline limits, and admin-controlled boundaries.

Forms And Drafts Logic is defined in `forms-drafts-logic.md`:
mobile forms must stay simple, validated, autosave-aware, offline-draft
safe, API-submitted, admin-controlled, and explicit about local-save versus
server-accepted state so user work is protected without bypassing authority.

Notifications Logic is defined in `notifications-logic.md`:
notification targeting, delivery policy, push behavior, in-app inbox,
read/unread state, deep links, preferences, offline behavior, and tenant or
permission boundaries must remain Admin/API-authoritative and mobile-safe.

Support System Logic is defined in `support-system-logic.md`:
support requests, support messages, attachments, tenant context, support-agent
visibility, audit, notifications, and offline support drafts must remain
tenant-scoped, least-privilege, privacy-safe, and Admin/API-authoritative.

Billing And Plan Logic is defined in `billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature states,
and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

Reporting Logic is defined in `reporting-logic.md`:
admin measurements, tenant-admin measurements, mobile-visible summaries,
privacy boundaries, date ranges, exports, feature usage, sync health,
notification, support, and billing reports must remain tenant-scoped,
permission-aware, privacy-safe, auditable, and Admin/API-authoritative.

Native Feature Strategy is defined in `native-feature-strategy.md`:
NativePHP capability use, logical service boundaries, browser/development
fallbacks, permission education, admin feature-flag control, native failure
UX, and offline sync behavior must remain feature-scoped, tenant-safe,
privacy-aware, fallback-safe, and Admin/API-authoritative.

Camera And Media Logic is defined in `camera-media-logic.md`:
photo capture, media selection, media preview, record/support attachments,
offline media storage, upload queues, feature-flag control, permission
denial, size limits, and privacy behavior must remain tenant-scoped,
permission-aware, fallback-safe, queue-safe, privacy-safe, and
Admin/API-authoritative.

Scanner Logic is defined in `scanner-logic.md`:
QR/barcode scan-to-search, scan-to-create, scan-to-validate, scan history,
offline scanning, invalid scan handling, duplicate scan handling, admin
feature flags, and camera/permission dependency behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear,
duplicate-safe, privacy-safe, and Admin/API-authoritative.

Geolocation Logic is defined in `geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Device, Network, And Diagnostics Logic is defined in `device-network-diagnostics-logic.md`:
device information use, network status use, offline detection, diagnostics
export, support troubleshooting context, diagnostics redaction, admin mobile
device visibility, and user-controlled diagnostics sharing must remain
tenant-scoped, permission-aware, support-scoped, privacy-safe, redacted,
audit-ready, and Admin/API-authoritative.

Module Selection Principles are defined in `module-selection-principles.md`:
optional industry modules such as field service, logistics, ecommerce,
booking, education, events, support, community/messaging, reports, and AI
assistant must be tenant-enabled, plan-controlled, permission-aware,
mobile-hidden when unavailable, feature-flag-safe, documented before
implementation, and Admin/API-authoritative.

Field Service Logic is defined in `field-service-logic.md`:
work order lifecycle, technician mobile flow, check-in/check-out, photos,
notes, future signatures, offline behavior, admin dispatch/control, and
report visibility must remain tenant-enabled, plan-controlled,
permission-aware, offline-clear, evidence-safe, privacy-safe, auditable,
and Admin/API-authoritative.

Booking Logic is defined in `booking-logic.md`:
service selection, availability logic, booking requests, confirmation,
cancellation, reschedule, reminders, admin schedule control, tenant rules,
and mobile offline limitations must remain tenant-enabled, plan-controlled,
permission-aware, availability-safe, schedule-conflict-safe, reminder-safe,
offline-limited, privacy-safe, auditable, and Admin/API-authoritative.

Commerce Logic is defined in `commerce-logic.md`:
catalog browsing, cart behavior, checkout principles, hosted payment
boundaries, order lifecycle, invoice/receipt principles, subscription
upsell, admin product/control, and mobile offline limitations must remain
tenant-enabled, plan-controlled, permission-aware, price-safe,
inventory-safe, hosted-payment-safe, offline-limited, privacy-safe,
auditable, and Admin/API-authoritative.

Messaging And Community Logic is defined in `messaging-community-logic.md`:
conversation behavior, support chat behavior, message attachments,
moderation, reports/abuse flow, notification behavior, offline message
drafts, admin visibility boundaries, and privacy principles must remain
tenant-enabled, plan-controlled, permission-aware, moderation-ready,
abuse-report-safe, notification-safe, offline-draft-safe, privacy-safe,
auditable, and Admin/API-authoritative.

AI Feature Logic is defined in `ai-feature-logic.md`:
AI assistant purpose, summarization, categorization, smart suggestions,
moderation assistance, report generation assistance, admin AI controls,
tenant opt-in, privacy, and human-review principles must remain
tenant-enabled, plan-controlled, permission-aware, opt-in-only,
provider-neutral, human-reviewed, privacy-safe, audit-ready,
rate-limited, cost-aware, and Admin/API-authoritative.

Acceptance Principles are defined in `acceptance-principles.md`:
every feature must document purpose, admin control, mobile behavior,
API dependency, offline behavior, permission behavior, feature flag
behavior, tenant behavior, error behavior, security behavior, and
documentation requirements before implementation planning begins.

Risk Map is defined in `risk-map.md`:
API dependency, offline sync, tenant isolation, mobile secure storage,
NativePHP plugin availability, app store releases, forced updates,
feature flag mistakes, billing restrictions, admin misconfiguration,
support access, privacy, and data conflicts must document prevention
principles and documentation requirements before implementation.

Testing Strategy Principles are defined in `testing-strategy-principles.md`:
future tests for API contracts, admin controls, mobile feature visibility,
permissions, feature flags, remote config, authentication, tenant isolation,
offline sync, conflict behavior, native feature fallbacks, notification flows,
billing rules, and app version rules must map to documented authority, risk,
and user-visible behavior before implementation.

Logistics Delivery Logic is defined in `logistics-delivery-logic.md`:
delivery job lifecycle, pickup flow, drop-off flow, proof of delivery,
scan validation, location check-in, failed delivery reasons, offline
behavior, and admin monitoring must remain tenant-enabled, plan-controlled,
permission-aware, scan-safe, location-purpose-limited, offline-clear,
privacy-safe, auditable, and Admin/API-authoritative.

Voice Note Logic is defined in `voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

Sync Lifecycle Logic is defined in `sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

Local storage must also respect [Target User Roles](user-roles.md). Mobile-local cache may reflect the currently authorized mobile user, but invited, suspended, and guest/pre-login states must not retain normal workflow access.

Local storage must also support the [SaaS Value Map](saas-value-map.md). Offline sync creates value for tenant businesses, tenant admins, mobile workers/clients, support teams, and billing/operations only when local work remains cache, draft, or pending intent until the API confirms it.

Local storage must also obey [Two-System Boundary Logic](two-system-boundary.md). Mobile-local data may improve speed, drafts, offline work, and sync visibility, but it must not own tenant, permission, billing, feature, app-version, support, report, audit, or final sync authority.

Local storage must also follow [API-First Principles](api-first-principles.md). Cached data, local drafts, queued intents, sync metadata, conflicts, and retry states are useful only when API purpose, response states, mobile-friendly errors, sync/conflict behavior, and tenant-boundary checks are clear.

Local storage must also obey [Admin/API Responsibilities](admin-api-responsibilities.md). Mobile-local data may cache or queue outcomes, but tenant management, users and permissions, API contracts, feature control, remote configuration, mobile version rules, notifications, billing, support, reporting, audit history, conflict decisions, and security enforcement stay in Admin/API.

Local storage must also follow [Mobile Client Responsibilities](mobile-client-responsibilities.md). Cache, drafts, offline actions, sync status display, local feedback, and feature visibility are mobile responsibilities only while they remain non-authoritative and API-reconciled.

## Product Role

The mobile client may use local SQLite for:

- Cached server data that is safe to display while stale.
- Local drafts and incomplete user work.
- Offline action intents waiting for replay.
- Local records and metadata that still need server confirmation.
- Sync cursors, last sync timestamps, and conflict state.
- Non-sensitive activity history that helps the user understand what happened.
- Local notification history and schedule metadata when the feature allows it.

The mobile client must not use local SQLite for:

- Access tokens, refresh tokens, API secrets, private keys, or billing credentials.
- Tenant authority, plan authority, or permission authority.
- Server-trusted audit logs.
- Final billing state.
- Unencrypted sensitive documents.
- Anything the API must be able to revoke immediately.

Secrets belong in NativePHP secure storage or another approved secure-token store when available.

## Vision Fit

Local storage is valuable because mobile users often work with changing connectivity, device conditions, and interruption-heavy workflows. It is risky when it starts acting like the business authority.

The product boundary is:

- Admin users configure whether offline work is allowed for a tenant, role, feature, app version, or device state.
- Mobile users can draft, cache, and queue work only inside those allowed boundaries.
- The API accepts, rejects, transforms, or marks queued work as conflicted.
- Support users can inspect safe sync context when local and server state diverge.
- Documentation defines offline behavior before local storage expands into a new module.
- Suspended users cannot replay new local work as trusted actions until the API reauthorizes them.
- Billing/operations users can trust entitlement checks during replay because offline value is confirmed by the server, not by stale local state.

This keeps offline-first behavior scalable: more tenants and devices can work locally without multiplying trusted client-side rules.

This is one reason the product is stronger than web-only and mobile-only alternatives. Web-only systems usually cannot provide reliable local mobile work. Mobile-only systems can store local work, but without a control center they struggle to enforce tenant policy, billing, permissions, rollout, and support context after reconnecting.

## Current Configuration

The mobile app has a dedicated SQLite connection named `mobile_local`.

- Connection: `mobile_local`
- Config: `config/database.php`, `config/mobile_local.php`
- Default database file: `storage/app/mobile/mobile-local.sqlite`
- Migration path: `database/migrations/mobile-local`
- Health command: `php artisan mobile:local-health`

The local database file is intentionally stored under `storage/app/mobile` so it is writable in a packaged NativePHP mobile runtime.

## Offline State Model

Every locally stored item should fit one of these states:

| State | Meaning | Server authority |
| --- | --- | --- |
| Cached | Server-confirmed data stored for fast or offline reads. | Server remains authoritative. |
| Draft | User-created local work not submitted yet. | Server does not know it exists. |
| Pending | Queued intent waiting for API replay. | Server has not accepted it. |
| Synced | Server accepted the intent and returned confirmed state. | Server authoritative. |
| Conflict | Server rejected or could not apply the intent as-is. | Server authoritative; user/admin may resolve. |
| Failed | Retry limit or policy stopped replay. | Server authoritative; support/admin may inspect. |

Mobile UI should expose pending, conflict, and failed states clearly. It should not silently present pending local work as confirmed server truth.

## Offline Action Principles

Queued actions should be designed as intents:

- Include a stable local ID.
- Include an idempotency key.
- Include action type and safe payload.
- Include tenant/user/device context only as claims to be verified by the API.
- Include created-at and retry metadata.
- Avoid storing secrets or oversized binary payloads in the queue.
- Replay only through API endpoints.

The API may accept, transform, reject, or mark an action conflicted. The mobile client must render the server decision.

This is the storage boundary: local SQLite may hold the queue, but the API owns replay acceptance and final state.

## Sync Policy

Sync behavior is controlled by the Admin/API system. The mobile client should receive policy through boot config or remote config:

- Which feature modules can queue offline writes.
- Maximum queue age.
- Maximum retry count.
- Backoff strategy.
- Conflict mode.
- Stale-data warning threshold.
- Whether sync can run on metered networks.
- Whether a tenant or app version is temporarily sync-blocked.

## Conflict Policy

Conflicts should be explicit product objects, not vague errors.

A conflict should explain:

- Which local action failed.
- Which server resource or policy blocked it.
- Whether the user can retry, edit, discard, or request support.
- Whether admin/support should see it in conflict reports.

Examples:

- User edited a record that the server no longer permits.
- Tenant billing state disabled the feature before sync.
- App version is too old for this action.
- User permission changed while offline.
- Server data changed and local action would overwrite newer data.

## Environment

No local override is required for development. The default path is generated with Laravel's `storage_path()` helper.

If a packaged runtime needs a custom location, set an absolute path:

```dotenv
NATIVEPHP_LOCAL_DB_DATABASE=/absolute/path/to/mobile-local.sqlite
NATIVEPHP_LOCAL_DB_FOREIGN_KEYS=true
```

Avoid relative override paths for `NATIVEPHP_LOCAL_DB_DATABASE`; SQLite paths are resolved from the process working directory and can drift between Herd, CLI, simulator, and packaged app contexts.

## Running Migrations

Run only the mobile-local migration path against the mobile-local connection:

```bash
php artisan migrate --database=mobile_local --path=database/migrations/mobile-local
```

Future local-only migrations belong under the mobile-local path and must remain local-cache/offline-work infrastructure. They should not be used to smuggle SaaS authority into the mobile client.

## Health Check

After migrations run, verify read/write access:

```bash
php artisan mobile:local-health
```

The command writes a non-sensitive probe value to `mobile_local_health_checks`, reads it back through Eloquent, and exits with `0` only when the value matches.

Expected output includes:

```text
Connection: mobile_local
Database: /path/to/storage/app/mobile/mobile-local.sqlite
Migrations: /path/to/database/migrations/mobile-local
Mobile local SQLite storage can write and read data.
```

## NativePHP Simulator Checklist

Before launching a simulator or emulator build:

```bash
php artisan config:clear
php artisan migrate --database=mobile_local --path=database/migrations/mobile-local
php artisan mobile:local-health
```

Then run the NativePHP mobile command documented in `docs/nativephp-run.md` for the target platform.

## Documentation Boundary

This document defines storage principles and sync expectations only. It does not create migrations, fields, models, repositories, sync workers, or API endpoints.
