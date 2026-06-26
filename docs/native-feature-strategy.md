# Native Feature Strategy

Updated: 2026-06-26

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

This document defines the NativePHP native feature strategy for the Mobile Lara
SaaS mobile client. It explains why native features should be wrapped behind
logical services, why every native feature must have browser/development
fallback principles, why permissions must be explained before use, why admin
feature flags must control native features, how native failures should be shown
to users, and how native features should interact with offline sync. It is
documentation only and does not define service classes, interfaces, database
fields, migrations, indexes, seeders, routes, controllers, Livewire components,
Filament resources, NativePHP plugins, plugin manifests, policies, gates,
middleware, jobs, queues, local storage schemas, API endpoints, UI components,
CSS, JavaScript, build configuration, app-store configuration, or application
logic.

Use this document with [Product Principles](product-principles.md), [Target
User Roles](user-roles.md), [Role And Permission Logic](role-permission-logic.md),
[Data Privacy Principles](data-privacy-principles.md), [Audit
Logic](audit-logic.md), [Tenant Lifecycle Logic](tenant-lifecycle-logic.md),
[Tenant Admin Logic](tenant-admin-logic.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Two-System Boundary
Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Mobile UX
Principles](mobile-ux-principles.md), [Mobile App Shell
Logic](mobile-app-shell-logic.md), [Mobile Dashboard Logic](mobile-dashboard-logic.md),
[Mobile Settings Logic](mobile-settings-logic.md), [Mobile Permission
Logic](mobile-permission-logic.md), [Authentication
Principles](authentication-principles.md), [Mobile App Lock
Principles](mobile-app-lock-principles.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration
Logic](remote-configuration-logic.md), [Mobile Version Control
Logic](mobile-version-control-logic.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Offline-First
Principles](offline-first-principles.md), [Offline UX
Logic](offline-ux-logic.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md),
[Notifications Logic](notifications-logic.md), [Support System
Logic](support-system-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), [Reporting Logic](reporting-logic.md),
[NativePHP Local Storage](nativephp-local-storage.md), [NativePHP
Runbook](nativephp-run.md), and [Mobile Stack](mobile-stack.md): NativePHP
capabilities are local execution tools, while Admin/API remains the source of
feature authority, tenant scope, permission eligibility, billing entitlement,
sync acceptance, audit meaning, and support visibility.

## Native Feature Statement

Native features should make mobile work easier without turning the mobile app
into a separate authority path.

NativePHP gives the mobile client access to device capabilities such as camera,
microphone, files, push notifications, device context, sharing, network status,
biometrics, scanner, location, secure storage, screenshot blocking, and other
plugin-backed behavior. Those capabilities are valuable only when they are
mapped to clear product features, controlled by Admin/API decisions, explained
to users, safe when unavailable, and compatible with offline-first sync.

Product rule: NativePHP plugins are implementation details. Mobile product
logic should depend on documented native capability outcomes, not direct plugin
availability. A native feature may collect, protect, display, or queue local
work, but API/Admin authority decides whether the work is allowed, accepted,
synced, audited, visible, reportable, billable, or supportable.

## Goals

Native feature strategy should:

- Keep native capability access behind logical product boundaries.
- Make browser, simulator, emulator, and local-development behavior predictable.
- Explain permissions before native prompts and before sensitive capture.
- Ensure feature flags, permissions, plan limits, app-version rules, tenant
  state, and maintenance mode control whether a native capability is visible or
  usable.
- Show native failures as calm, actionable user states.
- Protect offline drafts, captured media, queued intents, and secure local data.
- Keep native results tenant-scoped, permission-aware, privacy-safe, auditable,
  and API-authoritative before they become trusted server records.
- Give support/admin teams enough safe context to diagnose native capability
  failures without exposing private local content.
- Let the product expand native capabilities modularly without rewriting
  screens around plugin-specific details.

Native feature strategy should not:

- Let a plugin facade become product authority.
- Ask for permissions at first launch just because a plugin exists.
- Let mobile use native permission status as SaaS authorization.
- Hide unsupported, denied, failed, or unavailable native states behind generic
  errors.
- Treat browser fallbacks as production-equivalent native tests.
- Sync captured media, files, biometrics, location, notification tokens, or
  diagnostics without API authorization and privacy boundaries.
- Store secrets or tokens in ordinary local cache when secure storage is
  required.
- Allow feature flags to be bypassed because the device capability is available.
- Implement plugin integrations, service classes, or local schemas in this
  document.

## Why Native Features Need Logical Services

Native features should be wrapped behind logical service boundaries because the
product needs stable behavior even when plugins, platforms, permissions, and
devices vary.

In this document, "logical service" means a product boundary and decision
contract. It does not mean this documentation creates a service class.

Logical service principles:

- Screens should ask for product capabilities, not plugin names.
- API/Admin decisions should determine whether a native capability is eligible
  before the user reaches the native action.
- Native plugins should be replaceable, disabled, upgraded, or varied per
  platform without changing the product meaning of the feature.
- Plugin-specific events, errors, paths, tokens, and payloads should be
  translated into mobile-safe capability outcomes.
- Capability outcomes should use stable states such as available, unavailable,
  disabled by admin, blocked by plan, blocked by permission, requires update,
  permission needed, permission denied, unsupported, canceled, failed, queued,
  synced, and support needed.
- Native outputs should be validated at the boundary before the app treats them
  as local work or sends them to the API.
- Native feature decisions should be documented before implementation: purpose,
  permission, feature flag, fallback, offline behavior, sync behavior, privacy,
  support, audit, and admin impact.
- Support and diagnostics should see safe capability state, not raw private
  local content.

This boundary keeps NativePHP plugins useful without letting plugin behavior
leak into SaaS authority, API contracts, user permissions, billing, sync, or
tenant isolation.

## Native Capability Categories

Every NativePHP capability should have a product category before implementation.

| Capability category | Product purpose | Required strategy |
| --- | --- | --- |
| Capture | Camera, microphone, scanner, file picker, location, or document capture used to create work evidence or speed input. | Permission education, fallback, draft safety, sync queue, validation, upload/acceptance rules, and privacy boundaries. |
| Identity and security | Biometrics, secure storage, device trust, app lock, screenshot blocking, and session protection. | Local protection only; API still owns authentication, authorization, revocation, and tenant access. |
| Communication | Push notifications, in-app notification support, share sheets, support attachments, and deep links. | Admin/API targeting, user preference, device permission, fallback to in-app state, and audit/reporting boundaries. |
| Device context | Device info, network status, locale, platform, app version, battery/storage capability where used. | Safe diagnostics, feature eligibility, support context, and privacy minimization. |
| Offline support | Local files, SQLite cache, queued actions, background-capable work, and sync status. | Explicit stale/pending/synced/conflict states and API acknowledgement before server truth. |
| Development support | Browser, simulator, emulator, fake adapters, disabled states, and mockable outcomes. | Never pretend a browser fallback proves native behavior; document what can and cannot be tested outside a device. |

Native capability categories should be feature-scoped. A generic camera
capability is not enough; the product should name the specific workflow that
needs camera access.

## Browser And Development Fallback Principles

Every native feature needs a browser/development fallback principle because
Laravel/Livewire development often starts in a browser while true NativePHP
behavior runs on a simulator, emulator, or real device.

Fallback principles:

- Browser fallback is a development aid, not proof that native behavior works.
- Browser fallback should preserve product flow shape: allowed, disabled,
  loading, denied, failed, unsupported, fallback, queued, and synced states.
- Browser fallback should avoid requesting unavailable native permissions.
- Browser fallback should show an honest "not available in browser" state when
  no safe substitute exists.
- Browser fallback may use manual entry, file upload, mocked capability status,
  in-app notification inbox, password/PIN confirmation, cached network status,
  or disabled controls where appropriate.
- Browser fallback must not create trusted native data such as real push tokens,
  biometric proof, secure-storage guarantees, device trust, camera capture
  proof, precise location proof, or scanner proof.
- Browser fallback should not hide feature-flag, permission, tenant, plan,
  app-version, or maintenance blocks.
- Development fakes should be documented per feature and should not be shipped
  as production authority.
- Native behavior must still be validated on the target platform when the
  feature depends on camera, microphone, files, scanner, location, push,
  biometrics, secure storage, background behavior, or plugin events.

Good fallback design lets developers build product flows quickly while keeping
native risk visible.

## Permission Education Before Use

Native permissions are trust moments. They should be explained before use
because the operating system prompt is usually short, final-looking, and easy
to misunderstand.

Permission education principles:

- Explain the feature purpose before the native prompt appears.
- Ask just in time, when the user starts an enabled feature that needs the
  capability.
- Explain what data may be captured, stored locally, synced, shared with API,
  attached to records, used by support, or included in reports.
- Explain what happens if the user denies permission.
- Offer a fallback where the product allows one.
- Avoid pressure language.
- Avoid requesting permissions for disabled, hidden, unlicensed,
  tenant-blocked, app-version-blocked, maintenance-blocked, unsupported, or
  offline-ineligible features.
- Keep device permission separate from SaaS permission. A user may grant camera
  access and still lack permission to use a camera workflow.
- Use remote config for safe wording and support links where useful, but do not
  let wording changes override feature eligibility.
- Show permission status in settings: granted, denied, permanently denied,
  unavailable, unsupported, disabled by admin, blocked by plan, blocked by
  tenant, or not needed.

Permission education should make the user feel oriented, not trapped.

## Admin Feature Flag Control

Admin feature flags must control native features because native availability is
not enough to make a feature safe, licensed, tenant-appropriate, or supported.

Feature flag principles:

- Every important native capability should have a named product feature flag or
  be explicitly governed by a documented parent feature flag.
- Feature flags should resolve through Admin/API before mobile shows the native
  entry point.
- Flag decisions should account for global rollout, tenant enablement, plan
  entitlement, role, permission, user override, app version, device/platform,
  cohort, maintenance mode, emergency disablement, and support status.
- Disabled native features should avoid permission prompts.
- Disabled native features should hide or show a clear unavailable state based
  on mobile UX rules, not plugin state.
- Admin impact preview should explain which mobile surfaces, permission prompts,
  offline drafts, queued actions, sync behavior, reports, notifications,
  support flows, and billing/plan messages are affected by enabling or
  disabling the feature.
- Emergency disablement should stop new native actions, preserve already
  captured local work safely, and explain whether pending sync can continue.
- Plan limits should cap native feature availability even when tenant/user flags
  try to enable it.
- App-version rules should prevent stale clients from seeing features that need
  newer plugin support or API behavior.

Native features are feature-controlled product capabilities, not app binaries
that decide for themselves.

## Native Failure UX

Native failures should be shown as clear user states because device capability
failures are normal on mobile.

Native failure categories:

- Permission denied.
- Permission permanently denied.
- Permission revoked after previous approval.
- Capability unavailable on device.
- Plugin unavailable or not installed in the build.
- Unsupported platform or OS version.
- App version too old.
- Admin disabled the feature.
- Tenant, permission, plan, maintenance, or security state blocks the feature.
- User canceled the native action.
- Capture failed.
- File too large, unsupported, unreadable, missing, or moved.
- Location unavailable, inaccurate, timed out, or blocked by device settings.
- Push token registration failed or changed.
- Biometric enrollment missing, changed, locked out, or unavailable.
- Secure storage unavailable or failed.
- Network changed during native action.
- Local storage is full or unavailable.
- Sync cannot accept the native output yet.

User-facing failure principles:

- Use calm language that names the recovery path.
- Distinguish user cancellation from error.
- Distinguish native permission problems from SaaS permission problems.
- Distinguish offline/pending state from server rejection.
- Preserve user work when safe.
- Offer retry only when retry is meaningful.
- Offer settings recovery when the OS requires settings changes.
- Offer manual entry, file upload, in-app inbox, contact-admin, contact-support,
  or later-sync fallback where allowed.
- Do not expose stack traces, plugin internals, provider secrets, raw native
  error payloads, or private local paths.
- Show support-safe diagnostics when the user needs help.

Failure UX should help users continue, recover, or understand why they must
stop.

## Native Features And Offline Sync

Native features should interact with offline sync as queued local work until
the API accepts it.

Offline sync principles:

- Native capture can create a local draft or queued intent only when the feature
  is allowed and offline behavior is documented.
- Local native output should be marked as local, pending, failed, conflict, or
  synced.
- Captured media/files should stay tenant-scoped and separated by tenant
  context.
- Mobile should avoid capturing sensitive native data offline when policy
  requires online validation first.
- Native output should not become a trusted server record until API
  acknowledgement.
- Sync replay should re-check tenant status, user permission, feature flags,
  plan limits, app-version policy, maintenance state, validation, and conflict
  rules.
- Queued native actions should be idempotent at the API boundary.
- If the server rejects a native output, mobile should preserve safe local work
  where allowed and explain recovery.
- If feature flags change while offline, mobile should reconcile on reconnect
  before continuing queued native workflows.
- If tenant switches, logout, suspension, server revocation, or app lock failure
  occurs, native drafts and local files should follow cache, privacy, and sync
  cleanup rules.
- Sync health reports should expose safe metadata about native-related failures,
  not private captured content.

Offline native work should feel useful, but never more authoritative than the
API.

## Capability-Specific Strategy

### Camera

Camera should support documented workflows such as evidence capture, record
photos, visual inspection, profile images, document capture, or scanner input.

Camera strategy:

- Ask only when the user starts an enabled camera workflow.
- Provide upload/manual fallback where the product allows it.
- Treat captured photos as local drafts until API acceptance.
- Avoid capturing when storage is full, tenant context is unknown, permission is
  denied, or feature flags block the workflow.
- Redact or minimize metadata where privacy rules require it.
- Show clear retry, manual fallback, or support guidance on failure.

### Microphone

Microphone should support voice notes, audio evidence, dictation, support
recordings, or inspection notes only when clearly valuable.

Microphone strategy:

- Explain recording purpose and storage/sync behavior before asking.
- Provide text/manual note fallback where allowed.
- Avoid recording if tenant policy, plan, permission, feature flag, or offline
  rules block audio capture.
- Treat audio as sensitive local content until API acceptance.
- Show failed/canceled/denied states distinctly.

### Location

Location should support check-ins, nearby work, field verification,
route/context, or location-stamped records only when the product need justifies
it.

Location strategy:

- Explain why location is needed and whether it is required or optional.
- Avoid silent background assumptions unless separately documented.
- Respect precision, denied, unavailable, timeout, and device-setting states.
- Provide manual location or no-location fallback where policy allows.
- Re-check API authority before syncing location-stamped work.

### Files And Attachments

Files should support user-selected attachments, downloads, exports, offline
packages, support evidence, or record documents.

File strategy:

- Validate file type, size, source, and sensitivity before sync.
- Treat file names and metadata as untrusted.
- Keep files tenant-scoped and associated with local draft/sync state.
- Use upload/download/export authority through API, not local file presence.
- Offer retry, replace, remove, or support options on file failure.

### Scanner

Scanner should support QR/barcode or similar structured lookup workflows only
when manual fallback and API purpose are clear.

Scanner strategy:

- Treat scan output as untrusted input until validated by API or documented
  local rules.
- Provide manual entry fallback where allowed.
- Avoid scanning when camera permission, plugin, feature flag, or app version is
  unavailable.
- Show no-match, invalid, duplicate, offline-limited, or permission-blocked
  states separately.

### Push Notifications

Push notifications should complement the in-app inbox and never replace API
notification authority.

Push strategy:

- Request push permission only after explaining the value of alerts.
- Treat push token creation, failure, rotation, and revocation as API-managed
  device context.
- Provide in-app inbox fallback if push is denied or unavailable.
- Respect notification preferences, tenant targeting, permission boundaries,
  quiet/disabled categories, and billing/support/security rules.
- Avoid exposing sensitive content in push messages unless policy allows it.

### Biometrics And App Lock

Biometrics should protect local access and sensitive actions. They should not
replace API login, permissions, token refresh, tenant access, or server
revocation.

Biometric strategy:

- Explain local protection purpose before enabling or prompting.
- Provide PIN, password, session refresh, or logout fallback where policy
  allows.
- Handle enrollment changes, lockout, unavailable sensors, and admin-disabled
  states.
- Require API authority for sensitive server actions even after local unlock.

### Secure Storage

Secure storage should protect tokens, refresh tokens, device keys, and small
sensitive local secrets where available.

Secure storage strategy:

- Treat secure storage failure as security-sensitive.
- Never fall back to ordinary local cache for secrets without a documented
  security decision.
- Use safe logout, session recovery, or support guidance when secure storage is
  unavailable.
- Do not expose tokens in reports, support diagnostics, logs, exports, or
  screenshots.

### Network, Device, And System Capabilities

Network status, device context, sharing, dialogs, screenshots, system settings,
locale, and app-update helpers should improve UX and support, not create
authority.

Device/system strategy:

- Use device context as a hint for UX, support, and eligibility, not as trusted
  business truth.
- Treat network status as advisory; API results remain authoritative.
- Keep screenshot blocking and secure display controls tied to documented
  security-sensitive screens.
- Use sharing only for allowed data and safe URLs.
- Avoid exposing private diagnostics without role and support boundaries.

## Native Diagnostics And Support

Native feature diagnostics should help support without leaking private user
data.

Support-safe native diagnostics may include:

- Capability available/unavailable.
- Permission status category.
- App version and platform.
- Plugin capability group.
- Last native action category.
- Failure category.
- Sync status category.
- Generated-at/freshness time.
- Tenant context identifier already visible to support.

Diagnostics should not include:

- Raw tokens.
- Private file contents.
- Local file paths where they reveal private information.
- Exact captured media.
- Raw location history unless explicitly authorized.
- Secret storage values.
- Full native error payloads when they contain sensitive platform data.

Support access to native diagnostics should follow support, audit, privacy,
tenant, and role rules.

## Admin Control And Preview

Admin controls for native features should show impact before saving.

Admin preview should answer:

- Which native capability is affected?
- Which mobile screens and shortcuts change?
- Which permission prompts disappear or appear?
- Which users, tenants, roles, cohorts, plans, app versions, and devices are
  affected?
- What happens to existing local drafts and queued native actions?
- What offline behavior changes?
- What support, reporting, notification, billing, and audit effects occur?
- What fallback is shown to users?
- What rollback path exists?

Dangerous native feature changes include emergency disabling capture workflows,
blocking secure storage behavior, disabling app lock/biometrics, changing push
token behavior, changing file attachment rules, changing location policy,
changing scanner behavior, and changing native sync eligibility.

## Risks

| Risk | Principle |
| --- | --- |
| Plugin behavior becomes product authority | Wrap native features behind logical service boundaries and API/Admin decisions. |
| Browser fallback hides native failure | Mark browser fallback as development-only and require target-platform validation. |
| Permission surprise damages trust | Explain permission purpose, data use, fallback, and denial outcome before native prompts. |
| Feature flag bypass | Resolve native feature visibility through Admin/API before prompting or capture. |
| Offline native work becomes trusted too early | Treat native outputs as drafts or queued intents until API acknowledgement. |
| Cross-tenant local leakage | Keep captured files, drafts, cache, and sync queues tenant-scoped. |
| Sensitive diagnostics leak | Redact diagnostics and expose only support-safe metadata. |
| App version lacks required plugin | Gate capability by app-version policy and show update-required state. |
| Native failure causes data loss | Preserve safe local work, show pending/failed state, and offer recovery. |
| Secure storage unavailable | Fail closed for secrets and route to safe logout, recovery, or support. |

## Acceptance Questions Before Implementation

Before implementing a native feature, the team should answer:

- What product problem does the native capability solve?
- Which NativePHP plugin or platform capability is needed?
- Which logical service boundary owns the capability outcome?
- Which API dependency controls the feature?
- Which admin feature flag controls it?
- Which roles, tenants, plans, app versions, devices, and cohorts can use it?
- Which permission prompt is needed, and what explanation appears before it?
- What happens in browser development?
- What happens on simulator/emulator?
- What happens on unsupported devices?
- What happens when permission is denied or revoked?
- What fallback is available?
- What local data is created?
- What is cached, queued, or drafted offline?
- What must wait for API acceptance?
- How are sync failures, conflicts, and retries shown?
- What support-safe diagnostics are exposed?
- What audit/reporting/billing/notification effects exist?
- What risk exists if the plugin fails, changes, or is unavailable?

## Success Standard

Native feature strategy succeeds when NativePHP capabilities feel natural to
mobile users, stay controlled by Admin/API, remain safe in browser/development
fallbacks, explain permissions before use, show failures clearly, protect
offline work, and sync only through API-authoritative acceptance.
