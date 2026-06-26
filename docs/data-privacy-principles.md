# Data Privacy Principles

Updated: 2026-06-26

This document defines data privacy principles for the Mobile Lara SaaS system.
It explains tenant isolation, least privilege, secure local mobile data, secure
native storage, data export principles, data deletion principles, support access
limitations, admin visibility boundaries, privacy-by-default behavior, and
mobile diagnostics privacy limits. It is documentation only and does not define
database structure, database fields, migrations, seeders, routes, controllers,
Livewire components, NativePHP plugins, policies, middleware, jobs, services,
local storage schemas, export jobs, retention jobs, support tools, audit tables,
indexes, queues, or application logic.

Use this document with [Product Principles](product-principles.md), [Target
User Roles](user-roles.md), [Role And Permission Logic](role-permission-logic.md),
[Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md),
[Mobile Client Responsibilities](mobile-client-responsibilities.md), [API-First
Principles](api-first-principles.md), [Authentication Principles](authentication-principles.md),
[Mobile App Lock Principles](mobile-app-lock-principles.md), [Mobile Settings
Logic](mobile-settings-logic.md), [Mobile Permission Logic](mobile-permission-logic.md),
[Audit Logic](audit-logic.md), [Admin Safety Principles](admin-safety-principles.md),
[Tenant Lifecycle Logic](tenant-lifecycle-logic.md), [Documentation-First Architecture](documentation-first-architecture.md),
[NativePHP Local Storage](nativephp-local-storage.md), and [API v1 Diagnostics
Contract](../contracts/api/v1-diagnostics.md): privacy is the boundary that
keeps SaaS control, mobile convenience, support visibility, diagnostics,
exports, deletion, audit history, and offline behavior from becoming uncontrolled
data exposure.

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

## Privacy Statement

Privacy is a product boundary, not only a compliance notice.

The Admin/API system is the source of data authority. It decides which tenant,
user, role, permission, support context, billing context, report, export,
deletion, diagnostic, and audit view is allowed. The mobile client is a local
executor. It may cache data, protect local state, prepare drafts, queue offline
actions, and collect safe diagnostics, but it must not become a hidden copy of
the SaaS control plane.

Product rule: collect, display, cache, export, diagnose, and delete only what is
needed for the current user, tenant, role, feature, permission, support purpose,
and documented retention rule.

The privacy model should answer:

- Which tenant owns this data?
- Which user or role needs to see it?
- Which API purpose requires it?
- Which mobile screen needs it?
- Which support case or admin task justifies access?
- Which local cache, draft, or queue may temporarily hold it?
- Which export, deletion, audit, or retention rule applies?
- Which fields, logs, diagnostics, or summaries must be redacted?

## Authority Split

Privacy spans both systems, but authority must stay server-side.

| Area | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Tenant privacy | Tenant scope, tenant membership, cross-tenant denial, support exceptions, reporting scope, and export scope. | Showing only the selected tenant context returned by API and separating tenant-local cache, drafts, queues, and diagnostics. |
| Access privacy | Role and permission resolution, least-privilege rules, account state, tenant state, support access, billing visibility, and audit access. | Hiding or disabling UI from API context without treating UI hiding as authorization. |
| Local privacy | What may be cached, when cache expires, which data must be encrypted or locked, and when local wipe or logout cleanup is required. | Local cache minimization, app lock, secure storage use, local draft protection, offline labels, and safe purge UX. |
| Export privacy | Who can export, which data is exportable, tenant scope, redaction, file lifetime, and audit expectations. | Showing export status or downloaded file handling only when API allows it. |
| Deletion privacy | Account, tenant, record, cache, draft, retention, legal, billing, audit, and support deletion decisions. | Local cache clearing, draft discard, offline deletion queue presentation, and safe messaging about server authority. |
| Support privacy | Case-scoped visibility, diagnostics policy, redaction rules, support role limits, escalation, and audit history. | Collecting and previewing mobile-safe diagnostic summaries without secrets or unrelated tenant data. |
| Admin visibility | Platform, super admin, tenant admin, manager, billing, support, and report visibility boundaries. | Rendering API-shaped visibility only for the current user and tenant context. |
| Diagnostics privacy | Accepted diagnostic categories, redaction, support visibility, retention, user notice, and audit. | Collecting minimal app/device/sync/config/version summaries and sending them only through the API. |

## Tenant Isolation

Tenant isolation is the first privacy rule.

Every protected record, report, support case, notification, export, diagnostic
summary, audit view, local cache entry, draft, and queued action should be
understood in a tenant context unless it is explicitly platform-level and safe
to view outside one tenant.

Tenant isolation principles:

- A tenant user should see only the data that belongs to their tenant and their
  permitted scope inside that tenant.
- A tenant admin should manage their own tenant without seeing another tenant's
  users, billing details, reports, diagnostics, support cases, exports, queued
  actions, or audit history.
- A platform or super admin view should still be purpose-limited and auditable.
  Broad platform visibility is not a reason to display unnecessary private data.
- A support agent should access tenant data only through a support purpose,
  support role, and case or escalation context.
- A mobile client must not treat a cached tenant ID, route parameter, local
  setting, screen state, stored label, or offline queue entry as tenant
  authority.
- A mobile device must keep tenant-local cache, drafts, queues, notifications,
  attachments, and diagnostics separated so one tenant cannot bleed into another
  tenant's mobile session.
- Offline actions must be replayed only through the API, and replay must
  re-check tenant membership, tenant status, permissions, feature flags, app
  version policy, subscription state, and sync policy.
- Reports and exports must be scoped to the tenant and role that requested them.
- Diagnostics must never include unrelated tenant records or cross-tenant
  identifiers that are not required for support.
- Errors should not reveal whether another tenant, hidden resource, private
  membership, or blocked report exists.

Tenant isolation fails closed. If tenant scope is missing, stale, ambiguous, or
contradictory, the product should hide private data, request fresh API context,
or route the user to support instead of guessing.

## Least Privilege

Least privilege means every role gets the smallest useful access for the task.

Privacy does not rely on trust in the user interface. It relies on server-side
permission decisions, scoped API responses, explicit support purposes, and
auditability for sensitive access.

Least-privilege principles:

- Deny by default when identity, tenant, role, permission, feature, plan,
  version, support purpose, or account state is unknown.
- Platform owner, super admin, tenant admin, tenant manager, support agent,
  billing manager, mobile user, invited user, suspended user, and guest/pre-login
  user visibility should be different by design.
- Admin roles should not inherit private data visibility they do not need for
  their operational duty.
- Billing managers should see billing and entitlement context, not private
  mobile records unless separately authorized.
- Support agents should see support-relevant summaries first, not full tenant
  data by default.
- Tenant managers should see assigned operational data, not platform-level or
  unrelated tenant data.
- Mobile users should see only the features, records, notifications, settings,
  drafts, files, and actions their API context allows.
- Feature flags decide availability, but they do not grant privacy access by
  themselves.
- Remote config can tune behavior, but it must not widen privacy visibility.
- Temporary elevation should be time-bound, purpose-bound, scoped, confirmed
  where dangerous, and auditable.
- Read access, write access, export access, deletion access, support access, and
  audit access are separate privacy decisions.

The safest product posture is to expose less data by default, then add explicit
visibility only when the role, tenant, support purpose, and API contract justify
it.

## Secure Local Mobile Data

Mobile data is convenient, but it is also a privacy risk because devices can be
lost, shared, restored from backup, inspected through debugging tools, or used
offline after server access changes.

The mobile client may store local data only for a documented mobile purpose:
offline usefulness, fast navigation, drafts, queued actions, notification
history, sync status, settings, or support diagnostics.

Local data classes:

| Data class | Privacy principle |
| --- | --- |
| Access and refresh tokens | Store only in secure native storage where available. Never store in SQLite, normal cache, logs, diagnostics, URLs, screenshots, or visible state. |
| Private tenant records | Cache only when useful, tenant-scoped, app-lock protected, expiry-aware, and allowed by API policy. |
| Drafts and queued actions | Store only the minimum payload needed for recovery and replay, tied to tenant, user, feature, and sync policy. |
| Attachments and files | Keep in app-controlled storage, label tenant and feature context, avoid export without permission, and purge when policy requires. |
| Notifications and activity | Store summaries that help the user work without exposing private payloads unnecessarily. |
| Settings | Separate local preferences from admin/API authority and avoid storing server-trusted decisions as local truth. |
| Diagnostics | Store safe summaries only and let users/support understand what may be sent. |

Secure local mobile data principles:

- Cache only what the mobile experience actually needs.
- Keep local cache tenant-scoped and user-scoped.
- Protect private local data with app lock before display.
- Treat stale cache as last-known information, not current authority.
- Avoid storing secrets, token values, private keys, PINs, biometric details,
  passwords, payment credentials, raw audit payloads, or raw support transcripts
  in local application storage.
- Revalidate queued actions before sync, even if they were valid when created.
- Clear or hide private cache after logout, revocation, tenant removal, device
  block, repeated unlock failure, app reinstall uncertainty, or admin policy.
- Let the user clear local cache without implying that server data was deleted.
- Avoid keeping hidden private payloads only because a feature is currently
  disabled.
- Never use local data as proof of tenant membership, permission, subscription,
  app-version support, support access, or sync acceptance.

## Secure Native Storage

Secure native storage is for secrets and small sensitive values. It is not a
general database, export folder, support log, or cache for private records.

NativePHP secure storage should be used for mobile secrets where available,
including access tokens, refresh tokens, device-bound security values, and small
session-protection values. The product should treat secure storage as the
preferred place for bearer credentials because the NativePHP mobile stack uses
platform-backed secure storage patterns for iOS and Android.

Secure native storage principles:

- Store bearer tokens and secret session values in secure native storage, not
  SQLite, normal cache, local settings, logs, diagnostics, URLs, screenshots, or
  visible Livewire state.
- Do not store raw passwords, PINs, biometric templates, payment credentials,
  tenant exports, full private record payloads, support transcripts, or audit
  payloads as secure-storage shortcuts.
- Keep secure-storage values small and purpose-specific.
- Delete secure values on logout, logout-all-devices where applicable, server
  revocation, account reset, secure-storage failure, device block, or app policy
  that requires re-authentication.
- If secure storage is unavailable, unhealthy, or using a weak fallback, the
  mobile client should restrict authenticated behavior, hide private content,
  route to login or support, or require fresh API context rather than silently
  falling back to unsafe storage.
- Diagnostics may report secure-storage health or token presence as safe
  summaries, but must never include token values, hashes, secret keys, PINs, or
  encryption material.
- Secure storage does not grant authority. API validation still decides whether
  the token, user, tenant, app version, feature, and device can continue.

## Data Export Principles

Exports are high-risk because they move data outside normal application screens.

Every export should have a purpose, scope, permission, tenant boundary, and
audit expectation before implementation.

Export principles:

- Export is a separate permission from view access.
- Tenant export scope must be explicit and server-enforced.
- Platform-level exports must be purpose-bound and auditable.
- Tenant admins may export only tenant data their role allows.
- Support agents should export only case-relevant diagnostic or support context
  when policy allows.
- Billing managers should export billing/operations context, not private mobile
  records unless separately authorized.
- Exports should include the least data needed for the purpose.
- Exports should redact secrets, tokens, private keys, PINs, biometric details,
  sensitive internal notes, and unrelated tenant context.
- Export files should have documented lifetime, storage location, download
  rules, and revocation or expiry expectations.
- Exports should not include hidden fields merely because they exist in server
  storage.
- Mobile users should not export private tenant data from local cache unless the
  API explicitly grants and records the export.
- Export failures should be safe and non-leaking.
- Export actions should be audited with actor, tenant, purpose, scope, outcome,
  and support/compliance meaning.

## Data Deletion Principles

Deletion must distinguish local cleanup from server-side data removal.

A user clearing mobile cache is not the same as deleting server data. A draft
discard is not the same as deleting an accepted record. A tenant deletion is not
the same as deleting platform audit history. A support redaction is not the same
as removing legal or billing records.

Deletion principles:

- Deletion authority belongs to Admin/API for server-side data.
- Local deletion may clear cache, drafts, queued actions, files, settings, and
  diagnostics on the device according to policy.
- Deletion requests should explain what is deleted, what is retained, what is
  only hidden, and what may remain for legal, billing, audit, recovery, or
  backup reasons.
- Dangerous deletion should require confirmation and impact preview.
- Tenant-specific deletion should remain tenant-isolated and must not affect
  other tenants.
- Account deletion should address sessions, tokens, devices, tenant access,
  local cache, drafts, support cases, billing context, audit constraints, and
  recovery limits at a principle level before implementation.
- Tenant deletion or archival should address users, reports, exports,
  diagnostics, notifications, support cases, billing history, audit history,
  sync queues, mobile cache, and legal retention boundaries.
- Offline deletion requests should be queued only when the API contract defines
  safe delayed behavior; replay must revalidate authority.
- Deletion should not remove audit history that is required for security,
  compliance, billing, support, or abuse investigation unless a documented
  privacy/legal process requires redaction.
- Deletion should not expose hidden records by naming them in errors.
- Mobile should show local cleanup state separately from server deletion state.

## Support Access Limitations

Support access exists to solve a user or tenant problem. It must not become a
general data-browsing role.

Support principles:

- Support visibility should be tied to a support case, incident, tenant request,
  escalation, or documented operational purpose.
- Support should see summaries before raw data.
- Support should see diagnostics, version, config, sync, permission, tenant,
  and error context only when useful for resolution.
- Support should not see secrets, token values, private keys, passwords, PINs,
  biometric details, payment credentials, full private record payloads, or
  unrelated tenant data.
- Support access to private tenant content should require explicit role,
  purpose, and audit expectations.
- Support actions that change users, tenants, sessions, devices, queues, sync,
  billing, features, permissions, reports, or mobile behavior should be
  confirmed, scoped, and audited.
- Support impersonation-like workflows, if ever planned, must be documented as
  high-risk before implementation and must preserve tenant scope, actor
  identity, reason, duration, and audit history.
- Support exports should be narrow, redacted, expiring, and auditable.
- Support diagnostics should be sent only through the API and accepted only
  under server-defined policy.

## Admin Visibility Boundaries

Admin visibility is not unlimited by default.

The admin panel should make authority clear by showing what each admin role can
control, what it can only view, what it cannot see, and what requires escalation
or confirmation.

Admin visibility principles:

- Platform owner and super admin views should still minimize private data and
  separate operational summaries from sensitive detail.
- Tenant admins should see only their tenant, their allowed users, their allowed
  reports, their feature/config outcomes, their support cases, their exports,
  and their tenant audit views.
- Tenant managers should see assigned operational scope, not tenant-wide
  sensitive data unless permitted.
- Billing managers should see billing, plan, quota, entitlement, invoice, and
  payment-state context without default access to private mobile records.
- Support agents should see support-safe context and require purpose for deeper
  access.
- Mobile users should not gain admin visibility through mobile cache, local
  settings, debug screens, notifications, or diagnostics.
- Invited users, suspended users, and guest/pre-login users should receive only
  safe entry, recovery, legal, support, update, or no-access information.
- Hidden or disabled admin UI controls are not authorization. Server-side
  permission checks and API response shaping remain required.
- Cross-tenant views, exports, diagnostics, and reports require explicit
  platform authority, purpose, and audit history.
- Admin screens should avoid exposing raw tokens, credentials, internal secrets,
  unredacted stack traces, private local drafts, raw diagnostic payloads, and
  unrelated tenant records.

## Privacy-By-Default Behavior

Privacy-by-default means the safest state is the normal state.

Privacy-by-default principles:

- New features start private, tenant-scoped, disabled for unauthorized roles,
  and documented before implementation.
- API responses return only the fields mobile needs for the current purpose.
- Bootstrap/context payloads should be minimal and role-shaped.
- Disabled features should not request native permissions, collect diagnostics,
  keep hidden payloads, or expose stale shortcuts.
- Unknown feature, config, permission, tenant, version, sync, support, billing,
  or diagnostics state should fail closed.
- Error messages should be useful without exposing secrets, internal
  implementation details, hidden resources, private tenant membership, or stack
  traces.
- Logs, audit summaries, diagnostics, reports, notifications, and support
  timelines should be redacted by default.
- Local mobile cache should expire, hide, or purge according to policy rather
  than accumulating indefinitely.
- Debug and developer screens should never become user-facing privacy leaks.
- Production behavior should avoid verbose logs, raw payloads, and token/session
  inspection surfaces.
- Documentation should describe privacy risks before code creates new data
  collection, export, deletion, support, diagnostic, or admin visibility
  behavior.

## Mobile Diagnostics Privacy Limits

Diagnostics should help support and engineering understand mobile state without
turning the app into a private-data export tool.

Safe diagnostic categories may include:

- App version, build channel, platform, and supported/outdated state.
- Network status category, such as online, offline, metered, constrained, or
  unknown.
- Sync status summary, pending count, conflict count, last successful sync time,
  and retry category.
- Feature/config/version policy identifiers or safe labels.
- Tenant context label or safe tenant identifier when allowed by API policy.
- Permission status summaries, such as granted, denied, blocked, unavailable, or
  not requested.
- Secure-storage health status, not secure-storage contents.
- Local storage health status, size category, cache age, and cleanup state.
- Error category, correlation ID, request category, or support reference.
- Device capability summary, not precise private device content.

Diagnostics must not include:

- Access tokens, refresh tokens, token hashes, private keys, API secrets, app
  keys, encryption material, passwords, PINs, biometric details, recovery codes,
  or payment credentials.
- Raw private record payloads, private drafts, queued action payloads, files,
  media, screenshots, attachments, or user-entered sensitive notes unless a
  separate documented support flow explicitly allows a redacted attachment.
- Cross-tenant data or unrelated tenant identifiers.
- Exact location, microphone audio, camera photos, scanner contents, contact
  data, file contents, notification payloads, or push tokens unless the feature
  purpose and consent/permission flow are explicitly documented.
- Raw stack traces, raw SQL, server internals, provider secrets, internal
  exception payloads, or unredacted logs.

Diagnostics principles:

- Diagnostics should be previewable or explainable to the user where practical.
- Diagnostics should be submitted only through the API.
- Diagnostics should be accepted, redacted, retained, and shown according to
  Admin/API policy.
- Diagnostics should be tenant-scoped and support-case-scoped where applicable.
- Diagnostics should support audit and support review without exposing private
  payloads.
- Offline diagnostics may be stored locally only as safe summaries and should be
  sent later only if policy still allows it.
- Users should be able to clear local diagnostic cache without deleting server
  support records unless the API provides a server deletion or redaction flow.

## Audit Interaction

Audit history supports privacy, but audit history is also sensitive data.

Audit principles for privacy:

- Audit records should contain enough context to answer who, what, where, when,
  why, and outcome without copying full private payloads.
- Audit data should be tenant-scoped, role-scoped, redacted, protected from
  tampering, and accessed only by authorized roles.
- Audit exports are exports and must follow export privacy rules.
- Security events, support access, data exports, deletion requests, permission
  changes, tenant changes, mobile diagnostics submission, and privacy-sensitive
  admin views should be audit-relevant.
- Audit history should not expose secrets, raw tokens, PINs, biometric details,
  private keys, payment credentials, or unrelated tenant context.
- Audit access should itself be auditable when sensitive.

## Product Risks

Privacy risk should be recorded before coding.

| Risk | Principle response |
| --- | --- |
| Mobile cache becomes a second source of truth | Treat cache as temporary presentation state and revalidate through API before protected work or sync. |
| Support sees too much data | Default support to summaries, tie deeper access to case purpose, and audit support actions. |
| Exports leak cross-tenant data | Require explicit export permission, tenant scope, redaction, expiry, and audit. |
| Deleted data remains visible locally | Separate local purge from server deletion and hide stale cache after revocation, logout, or tenant removal. |
| Diagnostics include secrets | Define safe diagnostic categories and forbid tokens, secrets, raw payloads, media, and cross-tenant data. |
| Admin UI hides controls but API still allows access | Treat UI visibility as UX only and require server-side authorization. |
| Feature flags reveal hidden data | Feature flags control availability, not data authority. Permissions and tenant scope still decide visibility. |
| Offline mode preserves removed access | Offline data must be locked, labeled stale, and revalidated before replay or fresh private display. |
| Audit stores too much private data | Store decisions and summaries, not full sensitive payloads. Redact by default. |
| Secure storage fallback is unsafe | Limit authenticated behavior and route to login/support instead of storing secrets unsafely. |

## Acceptance Questions

Before any privacy-sensitive feature is implemented, the documentation should
answer:

- Which tenant owns the data?
- Which roles can view, change, export, delete, or diagnose it?
- Which API response or admin control grants visibility?
- What data is sent to mobile?
- What data is cached locally?
- What data is stored in secure native storage?
- What is hidden, cleared, or locked when offline?
- What happens after logout, revocation, tenant suspension, user suspension, or
  app-version block?
- What can support see without escalation?
- What can support see only with case purpose and audit?
- What can tenant admins see?
- What can billing managers see?
- What can platform admins see?
- What can be exported, by whom, for what purpose, and for how long?
- What can be deleted locally versus server-side?
- What audit event should exist?
- What fields, logs, diagnostics, errors, reports, and exports are redacted?

## Success Standard

The privacy model is successful when a mobile user can work simply, an admin can
operate safely, support can diagnose without overexposure, billing can manage
commercial state without private record access, platform operators can protect
tenants, and the mobile client can remain useful offline without becoming a
privacy leak or unauthorized source of SaaS authority.
