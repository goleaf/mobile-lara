# Camera And Media Logic

Updated: 2026-06-26

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

This document defines camera and media logic for the Mobile Lara NativePHP
client. It explains taking photos, choosing media, previewing media, attaching
media to records or support, offline media storage principles, upload queue
principles, admin control through feature flags, permission denial behavior,
and size and privacy principles. It is documentation only and does not define
database structure, database fields, migrations, seeders, routes, controllers,
Livewire components, Filament resources, NativePHP plugins, plugin manifests,
policies, gates, middleware, jobs, services, local storage schemas, API
endpoints, UI components, CSS, JavaScript, upload providers, storage providers,
background workers, queues, or application logic.

Use this document with [Product Principles](product-principles.md),
[Documentation-First Architecture](documentation-first-architecture.md), [Two-System
Boundary Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Role And Permission
Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version
Control Logic](mobile-version-control-logic.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Data Privacy
Principles](data-privacy-principles.md), [Audit Logic](audit-logic.md), [Mobile
Permission Logic](mobile-permission-logic.md), [Native Feature
Strategy](native-feature-strategy.md), [Offline-First
Principles](offline-first-principles.md), [Offline UX
Logic](offline-ux-logic.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md),
[Conflict Resolution Logic](conflict-resolution-logic.md), [Records/Content
Module Logic](records-content-module-logic.md), [Support System
Logic](support-system-logic.md), [Forms And Drafts
Logic](forms-drafts-logic.md), [Mobile Settings Logic](mobile-settings-logic.md),
[Mobile App Lock Principles](mobile-app-lock-principles.md), [Multi-Tenant
Mobile Logic](multi-tenant-mobile-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), and [Reporting Logic](reporting-logic.md):
camera and media workflows are tenant-scoped native-assisted workflows, while
Admin/API remains authoritative for feature eligibility, permission outcomes,
upload acceptance, storage policy, privacy, audit, support visibility, reports,
and sync truth.

## Camera And Media Statement

Camera and media features should help mobile users attach useful visual or file
context without turning the mobile device into the source of SaaS authority.

NativePHP gives the mobile client access to native capture and device media
capabilities. Those capabilities are valuable for records, support requests,
field evidence, screenshots chosen by the user, profile-style media, inspection
photos, and other tenant workflows. They are safe only when the Admin/API
system controls whether the workflow is allowed, which tenant it belongs to,
what size and type limits apply, how uploads are accepted, how privacy is
protected, what support can see, and how sync confirms the result.

Product rule: a photo, selected file, preview, or queued upload is local user
work until the API accepts it. Local presence on the device does not mean the
media is authorized, stored, attached, reportable, visible to admins, visible
to support, or part of server truth.

## Goals

Camera and media logic should:

- Let users take photos when an enabled workflow needs visual evidence or
  faster input.
- Let users choose existing media when policy allows it.
- Show clear previews before upload or attachment submission.
- Attach media to records or support requests only through API-authoritative
  workflows.
- Preserve local work safely when offline or when upload is delayed.
- Make upload queues understandable through pending, uploading, synced,
  failed, blocked, and removed states.
- Let admins control camera capture, media choosing, record attachments, support
  attachments, media size limits, allowed types, offline media behavior, and
  rollout through feature flags and remote config.
- Avoid native permission prompts when the feature is disabled, hidden,
  unlicensed, unsupported, tenant-blocked, user-blocked, or app-version-blocked.
- Protect privacy by minimizing metadata, limiting support visibility, avoiding
  raw path exposure, and separating tenant-local media.
- Keep media workflows modular so future file types, scanner workflows, or
  image processing can be added without weakening authority boundaries.

Camera and media logic should not:

- Upload media silently.
- Treat device permission as SaaS permission.
- Treat a captured photo as accepted server evidence before API confirmation.
- Use original filenames, file extensions, metadata, or local paths as trusted
  authority.
- Let support agents see raw media without case scope, role permission, and
  privacy policy.
- Keep offline media forever by default.
- Sync media across tenants or reuse a media draft after tenant switch without
  explicit policy.
- Ask for camera, photo library, file, or storage access for disabled features.
- Hide permission denial, file too large, unsupported media, upload failure, or
  privacy blocking behind generic errors.
- Define upload endpoints, storage disks, queue tables, file models, plugin
  manifests, or processing jobs in this document.

## Ownership And Authority

| Concern | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Feature eligibility | Whether camera capture, media choosing, record attachments, support attachments, media preview, offline capture, upload retry, and media download are enabled for each tenant, role, plan, user, app version, platform, and maintenance state. | Showing only eligible entry points, explaining blocked states, avoiding permission prompts for disabled workflows, and using last-known state carefully when offline. |
| Permission authority | SaaS permission, record/support permission, tenant state, plan entitlement, app-version eligibility, audit meaning, and support visibility. | Native permission education, requesting permission just in time, displaying native permission status, and helping users recover from denial. |
| Media acceptance | Allowed file categories, size limits, validation, malware or safety review principles, metadata policy, attachment acceptance, rejection, retention, and deletion. | Capturing or selecting media, showing local preview, preserving user intent, removing local drafts, and submitting through API or sync. |
| Offline work | Offline enablement, cache limits, queue limits, replay windows, blocked categories, and emergency stop behavior. | Local draft storage, pending upload labels, retry UX, storage-full handling, tenant-scoped cleanup, and clear saved-local versus synced labels. |
| Upload queue | Idempotency meaning, accepted server state, conflict rules, rejection reasons, audit history, and support/report visibility. | Queue presentation, retry scheduling intent, user cancellation, local failure recovery, and resumable user experience where policy allows. |
| Privacy and support | Which media, thumbnails, metadata, diagnostics, and attachment history support or admins may see. | User-facing notices, safe preview, local redaction behavior where documented, diagnostics summaries, and avoiding raw private path exposure. |

The mobile client can make capture and selection feel fast. The Admin/API system
decides whether the resulting media becomes trusted tenant content.

## Taking Photos

Taking a photo is a native-assisted capture action inside an enabled product
workflow.

Photo capture principles:

- The app should explain why the camera is needed before the native permission
  prompt appears.
- Camera access should be requested only when the user starts an enabled camera
  workflow.
- The capture action should be tied to a purpose such as record evidence,
  support evidence, inspection context, profile-style image, document capture,
  or tenant-specific workflow input.
- The app should distinguish capture success, user cancellation, permission
  denial, device failure, storage failure, feature-disabled state, and upload
  failure.
- Captured photos should become local drafts or pending attachments until API
  acceptance.
- Capture should be blocked or deferred when tenant context is unknown, the app
  is locked, the user is suspended, the tenant is suspended, the feature is
  disabled, the app version is unsupported, storage is unavailable, or policy
  requires online validation first.
- Captured photos should carry local status that users can understand: local
  draft, pending upload, uploading, synced, failed, blocked, removed, or needs
  review.
- The app should not assume that a native event or local file path is enough to
  attach media to a record or support request.
- The user should be able to discard or retake a photo before submitting where
  workflow policy allows.
- Sensitive capture workflows should require app unlock or confirmation when
  app-lock policy marks the destination as sensitive.

Native camera operations may complete asynchronously. Product behavior should
therefore support waiting, cancellation, failure, and later event handling
without creating duplicate attachments or losing the user's context.

## Choosing Media

Choosing media lets the user select existing media or files when policy allows
it.

Media selection principles:

- Media choosing should be available only for workflows that explicitly allow
  user-selected media.
- The app should explain whether selected media will remain local, be queued,
  or be uploaded after API confirmation.
- The app should avoid broad device file access when a narrower photo or media
  picker is enough.
- The app should treat selected media, original filenames, file extensions,
  MIME claims, timestamps, dimensions, metadata, and local paths as untrusted
  until validated by accepted policy.
- Selected media should be copied or referenced locally only under the current
  tenant, user, draft, and workflow context.
- The user should be able to remove or replace selected media before submit
  where workflow policy allows.
- The app should distinguish unsupported type, file too large, unreadable file,
  moved/deleted file, permission denial, user cancellation, storage full, and
  upload failure.
- Manual entry or text-only fallback should be available where the product does
  not require media.
- Media choosing should not be presented as a way to bypass camera permission
  when the workflow explicitly requires live capture.
- Media choosing should not be presented when support, records, or plan rules
  forbid attachments.

Choosing media is user intent. API acceptance decides whether that media can be
attached to tenant content.

## Previewing Media

Media previews help users confirm what they are attaching before it becomes
server-side content.

Preview principles:

- Previews should clearly indicate whether media is local only, pending upload,
  uploading, synced, failed, blocked, or removed.
- Preview should favor safe thumbnails, safe metadata, or image previews rather
  than exposing raw local paths or implementation details.
- Preview should avoid rendering unsupported or risky file types as active
  content.
- Preview should let users remove, replace, retry, or keep as draft where policy
  allows.
- Preview should not imply that the media is attached until the API accepts it.
- Preview should respect app lock, tenant switching, logout, suspension, and
  server revocation. Sensitive previews should disappear or re-lock when local
  protection is required.
- Preview should avoid showing media from another tenant, stale draft, previous
  user session, or previous support case.
- Preview should show size and type problems before upload when possible.
- Preview should explain when a preview is unavailable but the selected file may
  still be submitted after validation.
- Preview should be privacy-safe for support diagnostics. Support-safe
  diagnostics may describe preview status, not raw media content.

The user should understand the difference between "this is saved on my device"
and "this has been accepted by the tenant system."

## Attaching Media To Records

Record attachments add supporting context to tenant-scoped business content.

Record attachment principles:

- Record media actions must map to the record lifecycle: create, view, edit,
  note, status change, archive, restore, delete, and activity history.
- Admin/API decides whether the current user may attach media to the current
  record.
- Mobile may capture, choose, preview, and queue media only when the record,
  tenant, feature flag, permission, subscription, app version, maintenance
  state, and offline policy allow it.
- Record attachments should be associated with the active tenant and record
  context before capture or selection begins.
- Offline record attachments are pending local work until sync replay and API
  acknowledgement.
- Record attachments should not be reused across records unless a future
  documented workflow explicitly allows it.
- If the record changes, is archived, is deleted, becomes inaccessible, or
  conflicts before upload completes, mobile should stop, re-check authority, and
  explain the next step.
- Attachment acceptance, rejection, deletion, retention, visibility, and audit
  history belong to Admin/API.
- Mobile should preserve safe local work after recoverable failure, but should
  not keep media indefinitely when access, tenant, privacy, retention, or logout
  rules require cleanup.
- Record attachments should be visible only to users and admins whose current
  permissions allow them to see that record and attachment category.

Record media should support accountability without turning every device file
into tenant data.

## Attaching Media To Support

Support attachments help users explain a problem without giving support broad
access to private device or tenant data.

Support attachment principles:

- Support attachments should be optional unless the support category explicitly
  requires evidence.
- Mobile should explain which support request or case the media belongs to
  before capture or selection.
- Support attachments should belong to one tenant, one user context, and one
  case.
- Support attachments should not include secrets, tokens, passwords, payment
  data, raw authorization headers, unrelated tenant content, or private local
  drafts unless policy explicitly allows a sanitized version.
- Support agents should see only accepted attachments that their role, case
  assignment, tenant scope, and privacy policy allow.
- Mobile may preserve offline support attachment drafts only within documented
  retention limits.
- Support attachment uploads should re-check support availability, user status,
  tenant state, app-version policy, feature flags, and case state before API
  acceptance.
- Support attachments should be auditable by category, action, actor, tenant,
  case, and outcome, without logging raw private media content.
- If support is unavailable, billing-blocked, maintenance-limited, or disabled
  for the tenant, mobile should preserve a draft only when policy allows and
  explain the block.
- Support diagnostics should describe media workflow state, not silently upload
  raw media.

Support media is for help and recovery. It is not a shortcut for support agents
to inspect tenant data outside authorized case scope.

## Offline Media Storage Principles

Offline media storage exists to protect user work until the app can safely sync
or discard it under policy.

Offline media storage principles:

- Local media should be tenant-scoped, user-scoped, workflow-scoped, and
  draft-scoped.
- Local media should be labeled with user-understandable status: local draft,
  queued, uploading, synced, failed, blocked, expired, removed, or needs
  attention.
- Local media should be stored only when the feature allows offline capture or
  selection.
- Local media should not be stored in ordinary cache when it is sensitive enough
  to require stronger protection or when policy forbids offline storage.
- Local media should be cleaned up after successful sync, user discard, logout,
  tenant switch, revocation, tenant suspension, storage pressure, retention
  expiry, or privacy policy requirement.
- Local media should not survive app reinstall assumptions. The product should
  treat local storage as device-local and fragile.
- Local media should avoid duplicating large files when a safer local reference
  is enough and policy allows it.
- Local media should not be shared between tenants, users, records, support
  cases, or app-lock states.
- Local media storage should have clear behavior when device storage is low or
  unavailable.
- Offline media should never contain access tokens, refresh tokens, API secrets,
  provider credentials, or authorization headers.

Offline storage is a temporary safety net for user intent, not durable business
storage.

## Upload Queue Principles

The upload queue turns local media intent into API-reviewed server work.

Upload queue principles:

- Queue entries should represent user intent and media context, not accepted
  server truth.
- Uploads should re-check current tenant, user, permission, feature flag,
  subscription, app version, maintenance state, support/record availability,
  size limits, type limits, privacy policy, and conflict state before becoming
  accepted.
- Queue behavior should be idempotent at the API boundary so retry does not
  create duplicate attachments.
- Queue states should be visible enough for users: pending, waiting for
  connection, uploading, paused, synced, failed, blocked, canceled, or needs
  action.
- Retry should use calm, bounded behavior. Repeated failure should move the
  item into a needs-attention state rather than hiding it.
- Users should be able to cancel or remove local queued media where policy
  allows.
- Uploads should pause or fail safely when tenant context changes, the app
  locks, the user logs out, access is revoked, the feature is disabled, the app
  enters maintenance, or storage/media disappears.
- Uploads should not continue for media that became disallowed while the device
  was offline until API policy revalidates it.
- Queue health should be reportable to admins through safe counts, categories,
  and failure reasons, not raw media contents.
- Upload success should clearly transition the media from local/pending to
  accepted/synced.

The queue should make delayed work understandable without pretending delayed
work has already succeeded.

## Admin Control Through Feature Flags

Camera and media workflows should be controlled by feature flags because they
affect privacy, storage cost, support visibility, tenant policy, billing, and
mobile permission prompts.

Feature flags should control:

- Camera photo capture.
- Choosing existing media.
- Record attachments.
- Support attachments.
- Offline media capture.
- Offline media selection.
- Upload retry and background upload behavior.
- Media preview categories.
- Allowed media types or categories where config-driven behavior is safe.
- Size-limit tiers where plan or tenant policy requires them.
- Metadata retention or stripping behavior where policy allows options.
- Attachment visibility in admin, tenant admin, support, and mobile surfaces.
- Emergency disablement for risky media workflows.

Feature flag principles:

- Global disabled state should prevent the feature everywhere unless a
  documented platform exception exists.
- Tenant-level state should decide whether the tenant can use the feature within
  global and plan limits.
- User-level or role-level state should narrow access, not bypass tenant or
  plan restrictions.
- Plan limits should cap media features even when feature flags would otherwise
  enable them.
- Disabled media features should avoid native permission prompts.
- Disabled media features should hide entry points or show a clear unavailable
  state based on mobile UX rules.
- Admin impact previews should explain affected screens, permission prompts,
  offline drafts, upload queues, support cases, record attachments, reports,
  storage cost, privacy posture, and rollback behavior.
- Emergency disablement should stop new capture/selection and explain what
  happens to existing local drafts and queued uploads.
- Rollout should support safe cohorts, tenants, roles, app versions, and device
  capability groups.
- Feature flags should be auditable when they change media behavior.

Admin control should make media rollout boring in the best way: visible,
reversible, scoped, and explainable.

## Permission Denial Behavior

Permission denial should be treated as a normal user outcome, not an app error.

Denial behavior principles:

- The app should explain the permission purpose before asking.
- If the user denies camera, media, files, or storage permission, the app should
  show what can still be done.
- If the denial is recoverable in device settings, the app should explain the
  recovery path without pressuring the user.
- If the denial is permanent or the device capability is unavailable, the app
  should offer fallback only where policy allows it.
- The app should distinguish native permission denial from SaaS permission
  denial, tenant suspension, plan block, feature-disabled state, app-version
  block, maintenance mode, and offline-only limitation.
- The app should not keep asking repeatedly after denial.
- The app should not request permission for disabled features.
- The app should not imply that granting native permission will grant business
  access.
- The app should show permission status in settings as granted, denied,
  permanently denied, unavailable, unsupported, disabled by admin, blocked by
  plan, blocked by tenant, not needed, or unknown.
- Support guidance should use safe diagnostic categories rather than raw OS
  permission payloads.

The user should feel oriented and in control even when they decline access.

## Size And Type Principles

Media size and type rules protect storage, performance, privacy, security, and
mobile reliability.

Size and type principles:

- Admin/API should define accepted media categories and maximum sizes before
  implementation.
- Mobile should communicate size/type limits before or during selection where
  practical.
- Mobile should validate early for user feedback, but API validation remains
  authoritative.
- Unsupported media should be rejected with a clear explanation and safe
  replacement path.
- Large media should not block the entire app; it should enter a clear pending
  or failed state.
- Compression, resizing, thumbnailing, or metadata stripping should be governed
  by documented policy before implementation.
- Media type should not be trusted from filename alone.
- Original filenames should be treated as user-provided, potentially unsafe
  display text.
- SVG or active document types should require explicit risk review before being
  allowed as previewable media.
- Plan or tenant limits may reduce allowed size, count, type, retention, or
  attachment availability.

Size limits should be user-understandable, admin-visible, and API-enforced.

## Privacy Principles

Media can contain sensitive personal, tenant, location, customer, workplace, or
device context. Privacy must be designed before capture.

Privacy principles:

- Capture and selection should happen only inside a clear tenant and workflow
  context.
- The app should explain whether media will be stored locally, queued, uploaded,
  attached to a record, attached to support, visible to admins, visible to
  tenant admins, visible to support, included in reports, or retained after
  deletion.
- Media metadata such as EXIF, location, device details, timestamps, filenames,
  and dimensions should be minimized or controlled by documented policy.
- Media previews should not expose private local paths.
- Media diagnostics should include safe categories and failure states, not raw
  files.
- Support access should be case-scoped and least-privilege.
- Reports should use counts, categories, size bands, status, and failure reasons
  unless a role and privacy policy explicitly allow more.
- Deletion and retention behavior should be clear for local drafts, queued
  media, accepted attachments, removed attachments, archived records, closed
  support cases, and tenant deletion.
- Media should never be used as a hidden tracking channel.
- Media should not cross tenant boundaries through cache, drafts, queues,
  previews, support cases, reports, or diagnostics.

Privacy-safe media logic protects the user, the tenant, and the platform.

## Sync And Conflict Behavior

Media sync is more failure-prone than small text changes, so the product should
make every state visible and recoverable.

Sync and conflict principles:

- Media capture and selection may create local drafts or queued intents.
- Upload should not mark the attachment as accepted until API acknowledgement.
- If the parent record, support case, tenant, user permission, feature flag,
  plan, app version, or maintenance state changes before upload, the queued
  media should re-check policy.
- If the parent object no longer exists or the user can no longer access it,
  mobile should preserve safe local work only long enough for documented
  recovery.
- Auto-resolution may retry unchanged media when the failure was temporary.
- User choice may be required when media is too large, unsupported, attached to
  the wrong context, duplicated, or linked to a changed parent.
- Admin/support review may be required when media is blocked by policy, privacy,
  safety review, compliance, or repeated sync failure.
- Conflict decisions should be audited by outcome category without exposing raw
  private media in logs.
- Users should be warned before discarding unsynced media.
- Sync health should expose safe media failure categories for admin monitoring.

Media sync should protect user work while respecting the current server truth.

## Admin And Support Visibility

Admin and support visibility should answer operational questions without
becoming a privacy leak.

Admin/support visibility principles:

- Platform admins may need media feature usage, failure categories, queue health,
  storage impact, support burden, and tenant-level rollout status.
- Tenant admins may need tenant-scoped attachment availability, record/support
  attachment status, and user-facing workflow outcomes.
- Support agents may need case-scoped media status and accepted attachments
  only when the support case and role allow it.
- Billing managers may need plan-limit and usage summaries, not raw media.
- Mobile users should see their own local draft and upload status for the active
  tenant and workflow.
- Suspended users and suspended tenants should fail closed except for explicit
  recovery or support states.
- Admin views should distinguish captured local work, queued upload, accepted
  attachment, rejected attachment, removed attachment, expired draft, and failed
  upload where these states are exposed.
- Admin/support actions that change media availability, retry policy, retention,
  visibility, or deletion should require audit history.
- Support should never require users to send screenshots or photos when safe
  diagnostics can answer the support question.
- Admin reporting should not imply access to raw media unless the role and
  privacy policy allow it.

Media visibility should be useful, scoped, and explainable.

## Remote Config Principles

Remote config may tune safe behavior, but it should not replace feature flags,
permissions, or API validation.

Remote config may control:

- User-facing explanation text for camera, media, permission, upload, and
  denial states.
- Safe size labels or help text that mirrors API policy.
- Retry messaging and support links.
- Whether to recommend Wi-Fi for larger media uploads.
- Preview guidance and privacy notices.
- Tenant-specific fallback copy.
- Diagnostic category labels.

Remote config should not:

- Grant camera or media access by itself.
- Override feature flags, permissions, plan limits, tenant state, app-version
  rules, or API validation.
- Increase accepted media size/type limits beyond server policy.
- Enable sensitive metadata retention without documented privacy approval.
- Hide a security, privacy, permission, or upload failure.

Remote config should improve wording and safe presentation, not redefine media
authority.

## User Flow Principles

Camera and media flows should be short, clear, and recoverable.

Expected flow shape:

1. User enters an enabled record or support workflow.
2. Mobile confirms feature, permission, tenant, plan, version, offline, and
   workflow eligibility from current API or last-known allowed policy.
3. Mobile explains why camera or media access is needed.
4. User takes a photo or chooses media.
5. Mobile shows preview, size/type status, and local/sync state.
6. User attaches, removes, replaces, retries, or saves as draft.
7. Mobile uploads immediately when online and allowed, or queues when offline
   policy allows.
8. API accepts, rejects, defers, or flags the media according to current
   authority.
9. Mobile reconciles local state and explains the outcome.

Every step should have a blocked, denied, canceled, failed, offline, and
supportable state before implementation.

## Risks

| Risk | Principle |
| --- | --- |
| Camera permission becomes confused with business permission | Keep native permission separate from SaaS authorization and API acceptance. |
| Local media becomes server truth too early | Treat captured or selected media as draft or queued intent until API acknowledgement. |
| Cross-tenant leakage | Scope local media, previews, queues, and cleanup by tenant, user, workflow, record, and support case. |
| Privacy leakage through metadata | Minimize or control EXIF, location, filenames, local paths, and diagnostics through documented policy. |
| Upload retries create duplicates | Make replay idempotent at the API boundary and show clear queue state. |
| Support over-collects media | Prefer safe diagnostics and case-scoped attachments with least-privilege visibility. |
| Feature flags strand local work | Emergency disablement should stop new media work and document what happens to existing drafts and queues. |
| Browser fallback hides native risk | Treat browser media fallback as development aid, not proof of NativePHP capture behavior. |
| Large files degrade mobile UX | Use clear size/type limits, pending states, retries, and user recovery. |
| Original filenames or paths leak sensitive details | Treat them as untrusted and avoid exposing raw paths. |

## Documentation Checklist Before Implementation

Before implementing any camera or media feature, document:

- The product workflow and user role that needs media.
- The tenant, record, support, or settings context where the feature appears.
- The feature flag, plan limit, permission, remote config, version policy, and
  maintenance behavior.
- The permission education text and denial recovery behavior.
- The allowed media categories, size limits, preview behavior, metadata policy,
  and privacy boundaries.
- The offline behavior, local retention, cleanup triggers, and storage-full
  behavior.
- The upload queue states, retry behavior, cancellation behavior, idempotency
  expectation, and sync conflict outcomes.
- The admin impact preview, audit needs, support visibility, reporting
  summaries, and rollback behavior.
- The browser/development fallback and target-device verification expectation.
- The acceptance criteria for blocked, denied, canceled, failed, offline,
  queued, uploaded, rejected, synced, deleted, and support-escalated states.

Camera and media features should ship only when the product can explain what is
captured, where it goes, who controls it, how it is protected, and what happens
when the happy path fails.
