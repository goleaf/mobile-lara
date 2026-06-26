# Voice Note Logic

Final Consistency Review is defined in `final-consistency-review.md`:
all SaaS idea documentation must preserve API-only mobile authority,
admin-controlled configurable features, separated feature flags and remote
config, tenant isolation, clear offline behavior, permission-aware
NativePHP features, logical billing and plan limits, privacy-safe support,
tenant-bound reports, docs-only planning language, no database-field
definitions, and consistent terminology.

Final Optimized SaaS Blueprint is defined in `final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

Updated: 2026-06-26

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

Release And Versioning Principles are defined in `release-versioning-principles.md`:
API versioning, mobile app versioning, admin releases, feature rollout,
rollback, app store release, forced update, documentation update, and Git
change-history decisions must preserve documented authority, compatibility,
rollback, support, audit, and user-visible behavior before release.

Documentation Audit is defined in `documentation-audit.md`:
project documentation for two-system architecture, Admin/API authority, mobile
client execution, API-first communication, feature flags, remote config,
tenancy, permissions, offline sync, NativePHP features, notifications, billing,
support, reports, security, risks, and release principles must use consistent
authority language and resolve contradictions before implementation.

Feature Dependency Map is defined in `feature-dependency-map.md`:
major features must document dependencies on authentication, tenant context,
permissions, feature flags, remote config, API availability, offline cache,
NativePHP permissions, subscription plan, and admin settings before
implementation planning or release decisions.

Logistics Delivery Logic is defined in `logistics-delivery-logic.md`:
delivery job lifecycle, pickup flow, drop-off flow, proof of delivery,
scan validation, location check-in, failed delivery reasons, offline
behavior, and admin monitoring must remain tenant-enabled, plan-controlled,
permission-aware, scan-safe, location-purpose-limited, offline-clear,
privacy-safe, auditable, and Admin/API-authoritative.

This document defines voice note logic for the Mobile Lara NativePHP client. It
explains recording, pausing, resuming, saving locally, attaching to records and
support, transcription as an optional future feature, offline upload queues,
permission denial behavior, admin control through feature flags, privacy, and
retention principles. It is documentation only and does not define database
structure, database fields, migrations, seeders, routes, controllers, Livewire
components, Filament resources, NativePHP plugins, plugin manifests, policies,
gates, middleware, jobs, services, local storage schemas, API endpoints, UI
components, CSS, JavaScript, queue workers, transcription providers, storage
providers, or application logic.

Use this document with [Product Principles](product-principles.md),
[Documentation-First Architecture](documentation-first-architecture.md),
[Two-System Boundary Logic](two-system-boundary.md), [API-First
Principles](api-first-principles.md), [Admin/API
Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Role And Permission
Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version
Control Logic](mobile-version-control-logic.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Data Privacy
Principles](data-privacy-principles.md), [Audit Logic](audit-logic.md),
[Mobile Permission Logic](mobile-permission-logic.md), [Native Feature
Strategy](native-feature-strategy.md), [Offline-First
Principles](offline-first-principles.md), [Offline UX
Logic](offline-ux-logic.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md),
[Conflict Resolution Logic](conflict-resolution-logic.md), [Records/Content
Module Logic](records-content-module-logic.md), [Forms And Drafts
Logic](forms-drafts-logic.md), [Support System Logic](support-system-logic.md),
[Mobile Settings Logic](mobile-settings-logic.md), [Mobile App Lock
Principles](mobile-app-lock-principles.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), [Reporting Logic](reporting-logic.md),
[Camera And Media Logic](camera-media-logic.md), [Scanner
Logic](scanner-logic.md), and [Geolocation Logic](geolocation-logic.md): voice
note workflows are tenant-scoped native-assisted audio workflows, while
Admin/API remains authoritative for feature eligibility, microphone purpose,
recording acceptance, attachment acceptance, offline replay, transcription
eligibility, privacy policy, retention, audit, reporting, support visibility,
and sync truth.

## Voice Note Statement

Voice notes help mobile users capture spoken context when typing is slow,
unsafe, inconvenient, or less expressive.

Voice notes can support field notes, record evidence, inspection context,
support requests, incident descriptions, handover notes, and other tenant
workflows where short audio improves clarity. They are also sensitive because
audio can capture personal data, bystanders, customer information, workplace
context, private conversations, accents, health details, legal statements,
background sound, or operational secrets.

Product rule: a local audio recording is user-created draft content until the
API accepts it for the current tenant, user, feature flag, permission, plan,
app version, and sync state. Mobile may record, pause, resume, save locally,
preview, attach intent, queue, retry, or discard audio. Admin/API decides
whether the audio is allowed, accepted, attached, transcribed, retained,
audited, reportable, visible to support, exportable, or deleted.

## Goals

Voice note logic should:

- Let users intentionally record short audio notes inside enabled workflows.
- Let users pause and resume recordings without losing the recording context.
- Save local recordings as drafts only when policy allows local audio storage.
- Attach audio to records or support requests only through API-authoritative
  workflows.
- Support offline capture and upload queue behavior only where admin policy and
  sync rules allow it.
- Explain microphone use before the native permission prompt appears.
- Avoid microphone prompts when the audio feature is disabled, hidden,
  unsupported, unlicensed, blocked, or unavailable.
- Let admins control audio capture, audio attachments, offline queue behavior,
  duration limits, size limits, transcription eligibility, retention, and
  rollout through feature flags and remote config.
- Treat transcription as optional future behavior that requires separate
  documentation, consent, privacy review, and feature control before use.
- Protect privacy through minimization, tenant isolation, secure local handling,
  retention limits, support visibility limits, and safe diagnostics.
- Make recording states understandable: idle, recording, paused, saved locally,
  pending upload, uploading, synced, failed, blocked, rejected, deleted, and
  expired.

Voice note logic should not:

- Record silently or in the background without a clear user action.
- Ask for microphone access at first launch just because the plugin exists.
- Treat microphone permission as SaaS authorization.
- Treat a local file path as trusted tenant content.
- Attach audio to records or support without API acceptance.
- Sync audio across tenants or reuse a voice draft after tenant switching
  unless a future documented workflow explicitly allows it.
- Keep local audio forever by default.
- Expose raw local paths, raw native errors, private audio, or transcripts in
  diagnostics, logs, reports, or support views without policy.
- Use audio for monitoring, surveillance, worker evaluation, compliance,
  billing, or enforcement without explicit admin policy and user explanation.
- Implement microphone plugins, upload endpoints, audio models, queues,
  transcription providers, storage disks, or processing jobs in this document.

## Ownership And Authority

| Concern | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Feature eligibility | Whether voice notes, record audio attachments, support audio attachments, offline audio capture, queue replay, playback, deletion, transcription, and retention are enabled by global rollout, tenant, plan, role, user, app version, platform, maintenance state, and emergency state. | Showing only eligible audio entry points, explaining unavailable states, and avoiding microphone prompts when workflows are not eligible. |
| Microphone purpose | Which workflows may request audio and whether audio is required, optional, prohibited, support-only, record-only, or draft-only. | Explaining the purpose in user language before microphone permission or recording starts. |
| Permission authority | SaaS role permission, tenant policy, feature flags, subscription entitlement, app-version eligibility, device trust, revocation, and audit meaning. | Native permission education, just-in-time permission request, permission status display, and denied-permission recovery guidance. |
| Recording authority | Duration limits, size limits, supported audio categories, local-save eligibility, cancellation policy, retry policy, and audio acceptance criteria. | Recording session UX, pause/resume state, local draft state, playback where allowed, discard controls, and clear saved-local versus synced labels. |
| Attachment authority | Whether a record or support request can accept audio, whether audio is required, optional, rejected, hidden, deleted, retained, reportable, or visible to support. | Preserving user intent, letting users choose where audio belongs, and submitting attachment intent only through API/sync. |
| Offline behavior | Which audio workflows may run offline, queue limits, replay windows, blocked categories, emergency disablement, conflict rules, and API acknowledgement rules. | Local recording draft, queue presentation, retry/cancel UX, storage-full handling, tenant-scoped cleanup, and sync status communication. |
| Transcription | Whether transcription is allowed, which tenants/plans/users may use it, consent expectations, review workflow, language policy, provider policy, retention, and report/support visibility. | Showing transcription as unavailable, optional, pending, failed, or review-needed only after API policy allows the future capability. |
| Privacy and retention | Retention windows, deletion, exports, support visibility, audit history, legal hold, diagnostics limits, redaction rules, and transcript privacy. | Local minimization, secure handling, local deletion UX, safe diagnostics, and avoiding private audio leakage. |

The mobile client can capture sound. Admin/API decides what that sound is
allowed to mean.

## Native Capability Model

NativePHP microphone behavior is a native capability, not product authority.
Current NativePHP Mobile documentation describes the microphone plugin as a
separate package that can record audio, stop, pause, resume, report status
values such as idle, recording, and paused, return the last recording path, and
dispatch a recorded event that includes local audio path, MIME type, and an
identifier. NativePHP native operations may complete asynchronously and report
back through native events.

Product implications:

- A microphone event should be translated into a stable product outcome before
  the app treats it as a draft, attachment intent, queue item, or error.
- Local audio path, MIME type, native identifier, duration, size, and plugin
  status are untrusted local facts until validated by product policy and API
  acceptance.
- Browser and development fallback may show unavailable, simulated, manual,
  text-only, or disabled states, but cannot prove native microphone behavior.
- Microphone availability should be separate from SaaS permission, tenant
  permission, plan entitlement, app-version eligibility, and sync acceptance.
- Audio capture should remain bound to a product workflow such as a record
  note, support request, inspection note, incident report, or field update.
- Native failures should be shown as user-understandable outcomes, not raw
  plugin messages.

The product should define audio outcomes before implementation: available,
disabled by admin, blocked by plan, blocked by permission, permission needed,
permission denied, permanently denied, unsupported, unavailable, recording,
paused, canceled, saved locally, too long, too large, pending upload, uploading,
synced, rejected, failed, expired, deleted, and support needed.

## Recording

Recording is an intentional user action that starts a voice note inside an
enabled workflow.

Recording principles:

- The app should explain why microphone access is needed before the native
  permission prompt appears.
- Recording should start only after the user chooses a voice-note action in an
  eligible tenant, feature, workflow, app version, permission, plan, and device
  context.
- Recording should be tied to a purpose such as a record note, support
  request, inspection note, incident description, or tenant workflow update.
- The recording surface should show whether it is ready, requesting
  permission, recording, paused, stopping, saving locally, failed, blocked, or
  unavailable.
- The app should show a timer or equivalent progress state so the user knows
  audio is being captured.
- Recording should be stopped, canceled, or rejected safely if the tenant
  switches, the app locks, access is revoked, the user logs out, the tenant is
  suspended, the feature is disabled, the app enters maintenance, the device
  reports failure, or local storage becomes unsafe.
- Repeated taps, repeated native events, resumed sessions, or offline replay
  should not create duplicate attachments.
- The app should distinguish user cancellation from device failure, permission
  denial, feature blocking, storage failure, duration limits, and upload
  failure.
- A recording should not imply that a record or support request was updated
  until API acceptance confirms it.

Recording should make the user feel in control. Silent or ambiguous audio
capture is a product failure.

## Pausing

Pausing should preserve the same voice-note intent without ending the note.

Pause principles:

- Pause should be available only while recording and only when the native
  capability and admin policy allow it.
- The app should show a clear paused state that cannot be mistaken for
  recording or saved.
- Paused audio should remain local draft content under the current tenant,
  user, workflow, and recording session.
- The app should explain whether a paused note can be resumed, saved, discarded,
  or must be restarted after interruption.
- If the app is backgrounded, locked, interrupted by a phone call, or loses
  microphone access, the user should see a safe state on return.
- If pause is unsupported on a platform or plugin version, the app should hide
  pause controls or show a clear unsupported state before recording begins.
- Pausing should not upload, attach, transcribe, or accept audio.
- Admin/API may disable pause/resume if the workflow requires short atomic
  recordings.

Paused is not finished. It is a temporary local capture state.

## Resuming

Resuming should continue the same recording session only when it is still safe
and meaningful to do so.

Resume principles:

- Resume should check that the same tenant, user, workflow, feature flag,
  permission, plan, app version, device capability, and local draft context
  still apply.
- Resume should not continue audio after tenant switch, logout, session
  revocation, app-lock failure, feature disablement, or workflow closure.
- Resume should clearly return the user to recording state and continue the
  same voice-note intent.
- If a long interruption makes the recording stale or confusing, the app should
  require the user to save, discard, or start again according to policy.
- Resume should not duplicate the audio segment, duplicate the draft, or create
  multiple queue items for one user intent.
- If resume fails, the user should be able to keep the existing saved portion,
  discard it, or create a new recording where policy allows.

Resuming is continuity, not a new authority decision. API acceptance still
comes later.

## Saving Locally

Saving locally protects user work before API acceptance.

Local save principles:

- Local audio should be labeled as local draft, not synced content.
- Local audio should be scoped to the current tenant, user, workflow, and
  destination intent.
- Local metadata should be minimal: enough for draft recovery, status, duration,
  destination intent, and sync, but not enough to leak private content through
  diagnostics or logs.
- Local audio should be playable only where policy allows and where app-lock
  and tenant context remain valid.
- The app should let users discard local drafts where policy allows.
- Local audio should be removed or locked when the user logs out, the server
  revokes access, the tenant switches, retention expires, the device is shared,
  app lock requires protection, or admin policy requires cleanup.
- Local file paths should not be displayed to users, stored as server truth, or
  sent as meaningful business data.
- Local storage failure should be explicit and should not pretend a voice note
  was saved.
- Local audio should not be backed up, exported, or shared outside policy.
- If local storage is full, unavailable, corrupted, or unsupported, the app
  should provide safe recovery such as retry, discard, text note fallback, or
  support guidance.

Saving locally is a continuity feature. It is not record acceptance.

## Attaching To Records

Voice notes attached to records become part of tenant business content only
after API acceptance.

Record attachment principles:

- Audio attachment should be available only when the records/content module,
  current record, tenant, user role, permission, feature flag, plan, app
  version, and offline policy allow it.
- The user should know which record the audio will attach to before recording
  or before saving the attachment intent.
- A record may require audio, allow optional audio, forbid audio, or allow
  audio only for certain statuses or workflow steps.
- Mobile should show local draft, pending upload, uploaded, attached, rejected,
  removed, or expired states separately.
- API acceptance decides whether audio becomes part of the record, activity
  timeline, attachment list, audit history, report, export, or support context.
- If the record is archived, deleted, inaccessible, conflicted, tenant-blocked,
  or permission-blocked before sync, audio attachment should stop, re-check, or
  be rejected according to API policy.
- Audio should not be copied between records unless a future documented
  workflow explicitly allows it.
- Record exports and reports should include voice notes only when privacy,
  permission, retention, and admin policy allow it.

Voice notes can enrich records, but they must not bypass record authority.

## Attaching To Support

Voice notes attached to support requests should help support teams understand a
problem without expanding support access beyond policy.

Support attachment principles:

- Support audio should be available only when the support feature, tenant,
  support category, user permission, feature flag, plan, app version, and
  privacy policy allow it.
- The app should explain who may see the audio and why it is useful before the
  user records or attaches it.
- Support audio should be scoped to the current tenant and support request.
- Support agents should see only audio that belongs to cases they are allowed
  to handle.
- Support should not receive unrelated local audio drafts, raw local paths,
  unrelated record context, hidden tenant data, or diagnostics that include
  private audio content.
- Offline support audio may be saved as a local draft only when queue policy
  allows it; otherwise the app should offer text draft fallback.
- Support activity involving audio should be audited at the action level
  without exposing raw audio content in audit logs.
- If a support request closes before upload, the app should re-check policy
  before attaching, reject safely, or ask the user to create a new request.

Support audio should reduce friction, not widen private data visibility.

## Transcription As Optional Future Feature

Transcription is not part of the required voice-note authority model unless a
future product decision explicitly enables it.

Future transcription principles:

- Transcription should be controlled by a separate feature flag and should not
  be implied by audio recording.
- Transcription should require documented consent, privacy, provider,
  retention, language, review, correction, and deletion decisions before
  implementation.
- Transcription output should be treated as derived draft text until the API
  accepts it.
- Users should be able to review and correct transcripts before they become
  record or support content where workflow policy allows.
- Transcription should not make automated eligibility, compliance, billing,
  discipline, or support decisions without separate documented policy.
- Transcription should explain language limitations, accuracy limitations,
  failures, and sensitive-content risks.
- Transcription should not expose audio or text to support, reports, exports,
  analytics, or diagnostics beyond the original audio policy.
- If external services are used in the future, provider selection, data
  transfer, retention, error handling, and regional/privacy constraints must be
  documented before coding.

Transcription can be valuable later, but voice notes must work as protected
audio drafts without it.

## Offline Upload Queue

The offline upload queue should preserve user work while making it clear that
API acceptance has not happened yet.

Queue principles:

- Offline audio queueing should be enabled only by admin policy, feature flags,
  plan limits, tenant state, app version, device capability, storage health,
  and sync policy.
- Queued audio should show status such as local draft, pending upload,
  uploading, synced, failed, blocked, rejected, expired, or canceled.
- Queued audio should preserve a stable user intent without creating duplicate
  record or support attachments on retry.
- Queue replay should happen only through API/sync, never through a direct
  mobile-to-storage bypass.
- The user should be able to retry, cancel, discard, or keep local draft where
  policy allows.
- Admin/API should decide replay windows, maximum queue size, duration limits,
  file size limits, network requirements, metered-network rules, and emergency
  stop behavior.
- If the feature is disabled after a note is queued, the app should stop new
  recordings and follow API policy for existing pending audio: continue, hold,
  reject, expire, or ask the user.
- Queue failure should preserve enough safe context for correction or support
  without claiming the note was synced.
- API rejection should be explained in mobile-friendly language such as too
  large, unsupported, permission changed, record unavailable, tenant blocked,
  plan limit reached, retention expired, or support request closed.
- Background sync should not hide expensive, sensitive, or policy-blocked audio
  uploads from the user.

Pending audio should feel safe, but never final.

## Permission Denial Behavior

Microphone denial is expected user behavior and must be handled respectfully.

Permission principles:

- The app should explain microphone purpose before asking.
- The app should ask just in time, when the user starts an enabled audio
  workflow.
- Disabled voice-note features should not request microphone permission.
- If permission is denied, the app should explain that audio recording is
  unavailable and show permitted alternatives such as text note, attachment
  without audio, support text, or retry later.
- If permission is permanently denied, the app should guide the user to system
  settings only when the feature remains eligible and audio is genuinely useful.
- If the device lacks a microphone, the plugin is unavailable, or the platform
  is unsupported, the app should show an unavailable state instead of repeated
  prompts.
- If admin policy disables audio after permission was granted, the app should
  show disabled by admin, not ask for permission again.
- Device microphone permission should not override tenant policy, role
  permission, feature flags, billing limits, app-version rules, or server
  revocation.
- Permission status should appear in settings with understandable states:
  granted, denied, permanently denied, unavailable, unsupported, disabled by
  admin, blocked by plan, blocked by tenant, blocked by app version, or not
  needed.

A denied microphone permission is not an error to punish. It is a state to
support.

## Admin Control Through Feature Flags

Voice-note behavior should be controlled by feature flags because audio is
sensitive, native-dependent, potentially expensive, and workflow-specific.

Admin feature flag principles:

- Voice notes should have a named product feature flag or documented parent
  feature flag.
- Record audio attachments and support audio attachments may need separate
  controls because they affect different privacy and retention surfaces.
- Offline audio recording, offline upload queues, pause/resume, local playback,
  local deletion, upload retry, sharing, transcription, support visibility,
  reports, exports, and retention may need separate controls or clear child
  rules under the voice-note feature.
- Global decisions should set the broad product default.
- Tenant decisions should adapt to tenant policy, plan, rollout, and risk.
- Role or user decisions should narrow access where business workflows require
  it.
- Plan limits should cap audio availability, queue size, duration, storage, and
  transcription even when a tenant flag is enabled.
- App-version rules should block stale clients from audio features that depend
  on newer native plugin behavior or API policy.
- Emergency disablement should stop new recordings, protect existing local
  drafts, and explain whether pending uploads can continue.
- Admin impact preview should explain affected mobile screens, permission
  prompts, record/support attachment flows, offline queues, storage, sync,
  reporting, support visibility, billing/plan messages, and retention.
- Admin changes should be auditable and reversible where safe.

Audio feature flags should make rollout safer, not create hidden complexity for
mobile users.

## Privacy Principles

Voice notes are sensitive content and should be private by default.

Privacy principles:

- Record only with clear user action and visible recording state.
- Minimize what is captured, displayed, cached, uploaded, logged, reported, and
  exposed to support.
- Do not capture background audio, ambient monitoring, or bystander audio as a
  hidden product behavior.
- Avoid raw audio in logs, diagnostics, crash reports, browser console output,
  support metadata, audit payloads, analytics, or screenshots.
- Treat local audio, transcripts, filenames, durations, notes, attachment
  destinations, and retry history as tenant-scoped sensitive data.
- Keep local audio protected by app lock and local session policy where the
  workflow is sensitive.
- Separate cached audio by tenant and user so tenant switching cannot leak
  drafts.
- Support agents should see only audio that is case-scoped and permissioned.
- Reports should show aggregate or policy-approved audio usage without exposing
  raw content by default.
- Exports and deletion should follow tenant privacy, retention, and legal hold
  policy.
- Diagnostics should use safe categories such as permission denied, upload
  failed, too large, or unsupported, not private recording content.

Privacy should be designed before audio capture is built.

## Retention Principles

Retention defines how long audio can exist locally, server-side, in support,
in reports, in exports, and as derived transcript text.

Retention principles:

- Admin/API should define retention windows for local drafts, pending uploads,
  accepted record attachments, support attachments, rejected uploads, expired
  queue items, transcripts, reports, exports, and audit summaries.
- Local drafts should expire or be cleaned up according to policy, especially
  after logout, tenant switch, server revocation, storage pressure, app lock
  failure, or missed replay windows.
- Accepted audio should follow record/support retention rules, not an
  independent mobile rule.
- Deleted or expired audio should no longer be playable or attachable on mobile
  after policy cleanup.
- Audit history should preserve action meaning without storing raw audio
  content.
- If legal hold or compliance retention exists in the future, it must be
  documented separately before implementation.
- Transcripts should not outlive their source audio unless a documented policy
  explicitly allows it.
- Retention behavior should be explainable to admins and support, and
  user-facing where privacy policy requires it.

Retention is part of privacy, not a cleanup afterthought.

## When Voice Should Never Be Recorded

The app should never record voice when:

- The voice-note feature is disabled by global, tenant, role, user, plan,
  app-version, maintenance, emergency, or platform policy.
- The user has not taken an intentional recording action.
- The app is in guest/pre-login state.
- The app is locked and sensitive local data is protected.
- The tenant is unknown, suspended, archived, billing-blocked, deleted, or
  restore-limited.
- The user is suspended, revoked, logged out, or missing required permission.
- The workflow is text-only, online-only, closed, read-only, archived, or no
  longer accessible.
- Microphone permission is denied, permanently denied, unavailable, unsupported,
  or not installed.
- The user is in another tenant context than the intended destination.
- Local storage is unsafe, full, corrupted, unprotected, or beyond retention
  policy.
- The environment is legally or operationally inappropriate for audio capture.
- Admin policy requires online pre-check and the app is offline.
- The only purpose is diagnostics, analytics, monitoring, surveillance, or
  evaluation without explicit documented policy.

Never-record rules should fail closed and explain the safest available
alternative.

## API And Sync Principles

Voice notes should follow the API-first and offline-first rules used by the
rest of the mobile system.

API and sync principles:

- The mobile client should communicate voice-note acceptance only through the
  API and sync lifecycle.
- API responses should return predictable mobile-friendly states for audio
  eligibility, upload acceptance, rejection, retry, retention, and disabled
  behavior.
- API errors should be translated into user-facing categories without exposing
  storage internals, provider errors, native paths, or private content.
- API should protect tenant boundaries for every audio upload, attachment,
  support request, report, export, transcript, and audit summary.
- Sync should support idempotent replay so retries do not duplicate audio
  attachments.
- Conflict behavior should protect the user's local work while recognizing that
  records, support requests, permissions, plans, and tenant state may change
  before upload.
- Admin/support monitoring should see safe sync health and failure categories,
  not raw private audio.
- Mobile should never claim that audio is synced, attached, transcribed, or
  accepted until API acknowledgement says so.

Audio is local intent until API acknowledgement.

## Admin Safety Principles

Admin changes to voice-note behavior can affect privacy, storage, support
visibility, sync, and user trust.

Admin safety principles:

- Enabling audio capture should show impact before saving.
- Disabling audio capture should explain what happens to existing local drafts,
  pending uploads, accepted attachments, support audio, reports, exports, and
  retention.
- Changes to retention, transcription, support visibility, exports, queue
  limits, duration limits, or plan limits should require confirmation and audit
  history.
- Tenant-specific audio changes should not affect other tenants.
- Rollout should support global default, tenant override, role/user narrowing,
  staged cohorts, app-version gating, platform gating, emergency disablement,
  and rollback.
- Admin previews should show mobile effects such as hidden entry points,
  disabled controls, permission prompts avoided, queued uploads held, and
  unavailable-feature messaging.
- Rollback should preserve privacy and avoid re-enabling stale uploads without
  policy re-check.

Audio policy changes need more care than ordinary UI toggles.

## User Feedback

Voice-note UX should keep users oriented.

Feedback principles:

- Show clear states for ready, permission needed, recording, paused, stopping,
  saved locally, pending upload, uploading, synced, failed, blocked, rejected,
  deleted, and expired.
- Use plain language for why audio is unavailable.
- Distinguish saved locally from synced.
- Distinguish synced from attached.
- Distinguish attached from transcribed.
- Avoid panic when connection drops by preserving local draft state and showing
  next retry behavior.
- Give safe actions: resume, stop, save, discard, retry, cancel upload, switch
  to text note, contact support, or open settings where allowed.
- Keep controls thumb-friendly and obvious because recording often happens in
  mobile field conditions.

The user should always know whether audio is still only on the device or has
become accepted tenant content.

## Risks

Voice-note risks include:

- Accidental recording or unclear recording state.
- Audio capture after tenant switch, logout, app lock, or permission revocation.
- Local audio leaking across tenants or users.
- Duplicate uploads during retry.
- Large files consuming storage or data.
- Support seeing private audio outside case scope.
- Transcription exposing sensitive content to a provider or report.
- Retention policy leaving local audio longer than expected.
- Admin feature flags disabling a workflow while local drafts still exist.
- Users believing local audio is synced before API acceptance.
- Diagnostics or logs leaking local paths, raw audio, or transcript content.

These risks should be recorded before coding any new voice-note behavior.

## Readiness Checklist

Before implementing or expanding voice-note behavior, the product team should
document:

- Which workflow needs audio and why.
- Whether audio is required, optional, support-only, record-only, draft-only, or
  disabled.
- Which admin feature flags control the behavior.
- Which tenants, roles, plans, app versions, and platforms are eligible.
- What permission education appears before the microphone prompt.
- What happens when microphone permission is denied or unavailable.
- Whether pause and resume are supported.
- Whether local save is allowed.
- Whether offline queueing is allowed.
- What duration, size, storage, retry, and retention limits apply.
- What record/support attachment rules apply.
- What API acceptance, rejection, conflict, and sync states mean.
- Whether transcription exists, and if so, what separate policy governs it.
- What support, audit, report, export, and deletion visibility rules apply.
- How rollback and emergency disablement protect existing local drafts.

Voice notes should ship only when the product can explain what is recorded, why
it is recorded, who can access it, how long it survives, and when it becomes
server truth.
