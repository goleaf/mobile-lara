# Device, Network, And Diagnostics Logic

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

Updated: 2026-06-26

This document defines device, network, and diagnostics logic for the Mobile
Lara NativePHP client. It explains device information use, network status use,
offline detection principles, diagnostics export principles, what diagnostics
should help support understand, what diagnostics should never expose, admin
visibility of mobile devices, and user control over diagnostics sharing. It is
documentation only and does not define database structure, database fields,
migrations, seeders, routes, controllers, Livewire components, Filament
resources, NativePHP plugins, plugin manifests, policies, gates, middleware,
jobs, services, local storage schemas, API endpoints, UI components, CSS,
JavaScript, queue workers, logging processors, export generators, support
tools, dashboards, or application logic.

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
[Conflict Resolution Logic](conflict-resolution-logic.md), [Support System
Logic](support-system-logic.md), [Mobile Settings
Logic](mobile-settings-logic.md), [Mobile App Lock
Principles](mobile-app-lock-principles.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), [Reporting Logic](reporting-logic.md),
[Camera And Media Logic](camera-media-logic.md), [Scanner
Logic](scanner-logic.md), [Geolocation Logic](geolocation-logic.md), [Voice
Note Logic](voice-note-logic.md), and [API v1 Diagnostics
Contract](../contracts/api/v1-diagnostics.md): device, network, and
diagnostics behavior is mobile-collected context, while Admin/API remains
authoritative for device trust, tenant scope, support visibility, diagnostics
acceptance, redaction, retention, audit, reports, feature gating, and security
decisions.

## Device, Network, And Diagnostics Statement

Device and network context should help the app make better local decisions and
help support diagnose issues without turning the phone into a surveillance
source or authority source.

Mobile users need clear offline state, sync guidance, update guidance, storage
warnings, support help, and diagnostics sharing. Admins and support teams need
enough context to answer why a mobile feature failed, why sync stalled, why a
device needs an update, why native capability behavior differs by platform, or
why a user cannot reach the API. They do not need raw private content, secrets,
tokens, full local logs, or uncontrolled device history.

Product rule: device information, network status, and diagnostics are local
observations until the API accepts them for a specific support, security,
health, or reporting purpose. Mobile may collect safe local context, display it
to the user, export it locally, or submit it through API when allowed.
Admin/API decides whether it is accepted, visible, retained, reportable,
audited, redacted, linked to support, or ignored.

## Goals

Device, network, and diagnostics logic should:

- Use device information to support feature eligibility, app-version rules,
  support troubleshooting, security posture, native capability readiness, and
  safe operational reporting.
- Use network status to explain online, offline, constrained, metered, slow,
  captive, or unreachable states in user-friendly language.
- Distinguish local network connectivity from API reachability, authentication
  freshness, maintenance mode, forced update, tenant state, and sync health.
- Let mobile create privacy-safe diagnostic summaries that users can review
  before sharing where policy requires it.
- Help support understand device, app, network, permission, config, version,
  sync, storage, and recent failure context without seeing private tenant data.
- Prevent diagnostics from exposing secrets, tokens, raw business content,
  private files, exact location, raw media, voice notes, scan payloads, or
  unrestricted logs.
- Give admins safe visibility into mobile devices, versions, capability
  classes, sync health, network health patterns, and support-linked diagnostic
  snapshots.
- Give users control over diagnostics sharing, including preview, consent,
  cancel, download/share where allowed, and revoke or delete request where
  policy allows.
- Keep diagnostics tenant-scoped, permission-aware, support-scoped,
  privacy-safe, auditable, and API-authoritative.

Device, network, and diagnostics logic should not:

- Treat device information as identity, tenant authority, permission authority,
  billing authority, or proof of user action.
- Track users across tenants or apps through raw device identifiers.
- Use network status as proof that API writes succeeded.
- Claim offline state from a single signal when API reachability is unknown.
- Upload diagnostics silently when private context may be included.
- Share raw local databases, raw cache, raw logs, raw request/response bodies,
  raw headers, raw tokens, private media, raw location, or raw support content.
- Let support browse mobile devices without a support purpose, tenant scope,
  role permission, and audit trail.
- Make diagnostics a backdoor around role permissions, tenant isolation,
  feature flags, billing restrictions, or app-version policy.
- Define device registry tables, diagnostics endpoints, log schemas, support
  dashboards, export file formats, or code in this document.

## Ownership And Authority

| Concern | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Device visibility | Device trust, device registration meaning, app-version eligibility, supported platform rules, support-visible device context, and reportable device aggregates. | Local device context collection, safe local display, platform/capability warnings, and last-known device status. |
| Network meaning | API reachability policy, maintenance state, forced update state, sync retry policy, upload eligibility, metered-network rules, and server-side error truth. | Local network status, offline banner, constrained/metered hints, retry guidance, and user-facing network recovery. |
| Diagnostics acceptance | Which diagnostics are allowed, required redaction, support linking, retention, audit, reports, visibility, and deletion. | Local diagnostic summary, user review, local export/share flow, submission status, and safe failure messages. |
| Support visibility | Case scope, role scope, escalation rules, tenant boundaries, redaction policy, and support audit. | Showing the user what support can see and collecting only allowed local context. |
| Privacy and retention | Diagnostic categories, redaction standards, deletion, legal hold, retention windows, report aggregation, and export rules. | Minimizing local diagnostic data, excluding private content, and clearing local exports according to policy. |
| User control | Consent requirements, required/admin-initiated diagnostics rules, support-request linkage, and deletion-request handling. | Preview, confirm, cancel, retry, download/share where allowed, and explain what is local versus submitted. |

The mobile client can observe the device. Admin/API decides whether that
observation has product, support, security, billing, report, or audit meaning.

## Native Capability Model

NativePHP device and network features are local capability sources, not SaaS
authority. Current NativePHP Mobile documentation describes device information
such as device name, model, platform, OS version, manufacturer, simulator
state, memory, WebView version, battery level, charging state, and a platform
device identifier. It also describes network status with connected state,
connection type such as Wi-Fi, cellular, ethernet, or unknown, and constrained
or expensive network signals.

Product implications:

- Native device values are local observations and must be treated as untrusted
  input when sent to the API.
- Device identifiers should be minimized, rotated or abstracted where policy
  allows, and never treated as user identity by themselves.
- Device model, OS version, WebView version, simulator state, memory, battery,
  and manufacturer can help support explain behavior, but they should not grant
  permissions or bypass tenant rules.
- Network status can guide local UX and sync scheduling, but only API
  acknowledgement proves server reachability or accepted writes.
- Browser/development fallback may show simulated, unavailable, or manual
  diagnostic states. It should not be treated as proof of real native device or
  network behavior.
- Native app-settings recovery can help users fix denied permissions, but it
  should not imply that a device permission grants SaaS access.

The product should define stable outcomes before implementation: device
supported, unsupported, app update required, unknown device, simulator,
trusted, untrusted, revoked, low storage, low memory, low battery, charging,
network connected, offline, constrained, expensive, captive, API unreachable,
maintenance, sync blocked, diagnostics disabled, diagnostics local-only,
diagnostics ready, diagnostics redacted, diagnostics submitted, diagnostics
rejected, and support-linked.

## Device Information Use

Device information should be collected only for clear product, support,
security, or compatibility purposes.

Appropriate uses include:

- Determining whether the app version, OS version, platform, WebView version,
  or native capability class can safely use a feature.
- Showing the user whether their app or device needs an update to use a
  feature.
- Helping support understand platform-specific behavior, native plugin
  failures, permission friction, sync failures, storage problems, and
  performance issues.
- Helping Admin/API enforce device trust, session safety, token revocation,
  app-version policy, forced update, and emergency blocks.
- Reporting aggregate mobile health such as active app versions, unsupported
  platforms, sync health by app version, crash categories, permission
  failures, and broad network health patterns.
- Explaining why a feature is hidden, disabled, update-required,
  platform-limited, unsupported, or degraded.
- Distinguishing simulator/development behavior from real device behavior for
  testing and support.

Device information should not be used to:

- Identify a person without authentication.
- Grant tenant membership or role permission.
- Override a suspended user, suspended tenant, billing block, feature flag, or
  server revocation.
- Track a user across unrelated tenants, support cases, or apps.
- Infer private behavior such as location, habits, working hours, or
  productivity without documented policy.
- Expose exact device identifiers in ordinary UI, reports, support notes, logs,
  exports, or diagnostics.
- Replace app lock, authentication, authorization, secure storage, or API
  session validation.

Device information should be useful enough to support the user and sparse
enough that losing it would not expose private tenant content.

## Stable Device Identity

A mobile device may need a stable local identifier for session safety,
notification routing, device trust, diagnostics, and support correlation.

Stable device identity principles:

- A device identifier should be scoped to the product purpose that needs it.
- Device identity should never equal user identity.
- Device identity should never equal tenant authority.
- Admin/API should decide whether a device is trusted, revoked, stale,
  duplicate, unsupported, update-required, or needs re-authentication.
- Mobile should display device identity as friendly context, such as current
  device label and last-seen status, not raw identifiers.
- Device identifiers should be redacted or hashed in diagnostics and reports
  unless a privileged support/security workflow explicitly allows otherwise.
- Logout, logout-all-devices, token revocation, tenant switch, app reinstall,
  OS reset, or hardware change may alter device context; mobile should not
  assume continuity without API confirmation.
- Device identity should not be shared across tenants except through an
  Admin/API-approved platform security view with audit.

Stable device context helps security. It must not become silent tracking.

## Network Status Use

Network status should help the app choose honest local behavior.

Network status may be used to:

- Show offline, connecting, constrained, metered, slow, or unknown network
  states.
- Decide whether to attempt API calls, config refresh, app-version checks,
  notification registration, diagnostic upload, sync replay, media upload, or
  large downloads.
- Delay non-urgent uploads on expensive or constrained networks when admin
  policy allows.
- Warn users before large sync, media, diagnostics, or export uploads.
- Explain why sync, login, support submission, diagnostics upload, or app
  update checks are unavailable.
- Help support understand whether a failure happened while offline, on
  cellular, under low-data mode, behind a captive network, or while API
  reachability was failing.

Network status should not be used to:

- Claim the API accepted a write.
- Claim the server is healthy.
- Claim authentication is valid.
- Claim tenant permissions are current.
- Override maintenance mode or forced update rules.
- Send sensitive diagnostics automatically just because the device is online.
- Retry indefinitely without respecting battery, data, rate, support, and sync
  policy.

Connected means the device has some network path. It does not guarantee that
the API is reachable, authenticated, authorized, or ready to accept changes.

## Offline Detection Principles

Offline detection should combine multiple signals and explain uncertainty.

Offline detection principles:

- Treat local network status, recent API success, recent API failure, DNS/TLS
  failure, timeout, captive network suspicion, maintenance response, forced
  update response, token expiry, and sync state as separate signals.
- Distinguish "device offline" from "API unreachable", "server maintenance",
  "authentication expired", "tenant blocked", "feature disabled", "version
  blocked", and "sync delayed".
- Show the least alarming accurate state: offline, reconnecting, API
  unreachable, update required, maintenance, retrying, paused on metered
  network, or support needed.
- Avoid flipping banners rapidly when connectivity is unstable.
- Use last-known online time and last-successful sync time where useful.
- Let safe cached reads, drafts, and local settings continue where policy
  allows.
- Disable online-only actions while explaining why they are disabled.
- Queue only actions whose feature-specific offline policy allows queueing.
- Do not treat offline mode as permission to bypass validation,
  authorization, billing, feature flags, version rules, or audit.
- Re-check API authority before replaying any queued work after reconnect.

Offline detection should orient users. It should not turn network uncertainty
into false success or false failure.

## Diagnostics Export Principles

Diagnostics export is sensitive data movement, even when the payload is small.

Diagnostics export principles:

- Diagnostics should have a clear purpose such as support troubleshooting,
  sync recovery, app-version investigation, native capability failure,
  security review, or tenant incident analysis.
- The user should be able to review a safe summary before sharing when the
  diagnostics include device, tenant, sync, support, or local state context.
- Diagnostics should be redacted before display, local export, or API upload.
- Diagnostics should be scoped to the current tenant, user, device, app
  version, support request, and timeframe.
- Diagnostics should default to API upload when support visibility and audit are
  required; local share/download may exist only when policy allows it.
- Offline diagnostics may be prepared locally, but support upload requires API
  availability and policy confirmation.
- Diagnostics should have clear states: unavailable, disabled by admin,
  local-only, preparing, ready for review, redacted, sharing, uploaded, linked
  to support, failed, rejected, expired, or deleted.
- Diagnostic exports should expire locally and server-side according to
  retention policy.
- Diagnostic export and support access should be audited.
- Diagnostics should not include more than support needs for the current case.

Diagnostics should reduce support friction without making the mobile device an
uncontrolled evidence dump.

## What Diagnostics Should Help Support Understand

Diagnostics should help support answer practical questions safely.

Diagnostics may help support understand:

- Which app version, build channel, platform, OS version, and WebView/runtime
  context the user was using.
- Whether the device appears to be a simulator, unsupported platform,
  outdated OS, stale app version, or update-required client.
- Which tenant and user context the app believed was current, using safe
  identifiers and labels allowed by policy.
- Which feature flags, remote config version, maintenance state, app-version
  rules, and subscription/plan outcomes were last known.
- Whether native capabilities appeared available, unavailable, denied,
  permanently denied, disabled by admin, blocked by plan, blocked by app
  version, or unsupported.
- Whether network was offline, cellular, Wi-Fi, ethernet, unknown, expensive,
  constrained, captive-suspected, API-unreachable, or recently successful.
- Last successful bootstrap, config refresh, app-version check, sync pull, sync
  push, and notification registration times where safe.
- Sync queue counts, failed action categories, retry state, conflict counts,
  and rejection categories without raw payload content.
- Local storage health, cache freshness, draft counts, upload queue counts,
  and storage pressure categories without raw files.
- Recent error categories such as validation failed, unauthorized, forbidden,
  version blocked, maintenance, timeout, network failure, native unavailable,
  permission denied, or sync conflict.
- Whether a diagnostic snapshot is linked to a support request, incident,
  tenant, app version, or admin action.

Diagnostics should answer "what state was the app in?" more than "what private
data was inside the app?"

## What Diagnostics Should Never Expose

Diagnostics must fail closed around sensitive content.

Diagnostics should never expose:

- Access tokens, refresh tokens, session cookies, API keys, secrets, private
  keys, passwords, recovery codes, one-time codes, CSRF tokens, or raw
  authorization headers.
- Environment variables, bundled secrets, raw app keys, signing material,
  provider credentials, push provider secrets, or payment provider data.
- Raw local SQLite databases, raw cache files, raw queue payloads, raw request
  bodies, raw response bodies, raw headers, or raw exception traces with
  private data.
- Raw record content, notes, support messages, private notifications, billing
  details, invoice data, payment data, private reports, or tenant-private
  exports.
- Raw camera media, raw files, raw voice notes, raw transcripts, raw scanner
  payloads, exact location coordinates, biometric results, secure-storage
  values, or notification tokens.
- Full device identifiers in ordinary support views, user-visible exports,
  logs, or reports unless a privileged security workflow explicitly allows it.
- Data from another tenant, another user, another support case, another
  logged-out account, or a previous tenant context.
- User contacts, photos, files, calendar entries, clipboard contents,
  keystrokes, microphone data, background activity, or installed app lists.
- Hidden admin flags, plan internals, rollout cohorts, pricing internals,
  support-only notes, or policy details beyond what support needs.

When in doubt, diagnostics should report categories and counts, not content.

## Admin Visibility Of Mobile Devices

Admin visibility should be useful, scoped, and auditable.

Admin visibility principles:

- Platform admins may need aggregate mobile health: app versions, OS versions,
  platform split, active device counts, unsupported clients, sync health,
  feature rollout issues, and support diagnostics trends.
- Tenant admins may need tenant-scoped device visibility: current app-version
  compliance, last-seen state, sync health, update-required users, and support
  request context.
- Support agents may need case-scoped device context only for assigned or
  authorized cases.
- Billing managers should not see private diagnostics unless a billing support
  workflow explicitly requires a safe summary.
- Mobile users should see their own device status, app version, permission
  status, network state, local cache status, and shared diagnostic history
  where policy allows.
- Admin views should avoid raw device identifiers, raw diagnostics, exact
  private content, hidden local data, and cross-tenant browsing.
- Device visibility should respect tenant lifecycle, user suspension,
  logout-all-devices, device revocation, deletion requests, retention, legal
  hold, and support scope.
- Sensitive support or admin access to diagnostics should be audited.

Admins should see enough to operate the SaaS, not enough to inspect a user's
private device.

## User Control Over Diagnostics Sharing

Users should understand and control diagnostics sharing unless a documented
security or compliance workflow says otherwise.

User-control principles:

- The app should explain why diagnostics are useful before sharing.
- The app should show a safe preview or summary of categories included.
- The user should be able to cancel before submission where policy allows.
- The user should see whether diagnostics will be uploaded to support,
  attached to a support request, downloaded locally, copied, or shared through
  another app.
- The user should see whether diagnostics include tenant context, device
  context, network status, sync status, app version, permissions, config
  version, and recent error categories.
- The user should not be asked to manually copy sensitive logs or raw payloads.
- The user should receive clear feedback after upload, failure, rejection,
  expiry, or deletion.
- The user should be able to request deletion or revoke support access where
  privacy and retention policy allow.
- If admin or security policy requires diagnostics for a support or compliance
  workflow, the app should explain the policy and minimize collected data.

Diagnostics sharing should feel like informed help, not hidden extraction.

## Diagnostics In Support Workflows

Diagnostics should be connected to support outcomes, not collected for their
own sake.

Support workflow principles:

- A support request may ask the user to attach diagnostics when safe context
  would shorten resolution.
- Support agents should see diagnostics only within assigned, escalated, or
  authorized support scope.
- Diagnostics should show support-safe explanations, not raw implementation
  noise.
- A support reply should be able to reference diagnostic outcomes such as
  update required, permission denied, sync queued, API unreachable, storage
  pressure, or feature disabled by admin.
- Support should not use diagnostics to browse unrelated tenant data, inspect
  private records, or monitor users.
- Diagnostic access, redaction failure, export, download, support linkage, and
  deletion should be auditable.
- Diagnostics should expire or be removed from support context according to
  retention policy.

Diagnostics should help support fix the problem the user asked about.

## Diagnostics In Reports

Diagnostics may inform reports only through safe aggregation.

Reporting principles:

- Reports may show aggregate mobile health by app version, platform, OS class,
  network class, sync health, feature failure category, permission friction,
  update compliance, or support volume.
- Reports should avoid raw diagnostic snapshots, exact device IDs, private
  content, raw errors, tokens, local paths, raw queue payloads, or support-only
  notes.
- Tenant reports should stay inside tenant scope.
- Platform reports may aggregate across tenants only for authorized platform
  roles and privacy-safe product operations.
- Diagnostic reports should make trends visible without identifying a mobile
  user's private behavior unless a privileged, audited security workflow
  explicitly allows it.

Diagnostic reporting is for product health, not private inspection.

## Admin Controls

Admins need controls because diagnostics can expose more than intended if left
unbounded.

Admin controls should define:

- Whether diagnostics are enabled globally, by tenant, by role, by user, by app
  version, by platform, by support plan, or by support case.
- Which diagnostic categories are allowed: app version, device class, network,
  permissions, sync state, config version, queue counts, storage health, error
  categories, and support linkage.
- Which categories are forbidden: secrets, raw payloads, raw content, raw
  files, exact location, raw media, biometric outputs, tokens, and unredacted
  personal data.
- Whether local export/share is allowed or only API upload is allowed.
- Whether user confirmation is required before sharing.
- How long diagnostics are retained.
- Which roles may view diagnostics.
- Whether support can request diagnostics from a user.
- Whether diagnostics may be attached to reports.
- What happens during maintenance, forced update, suspension, tenant archive,
  billing block, support closure, or account revocation.

Admin controls should be impact-previewed, audited, and tenant-isolated.

## Privacy And Security Principles

Device and diagnostic privacy should be strict by default.

Privacy and security principles:

- Collect the minimum diagnostic context needed for the support or product
  purpose.
- Redact before storing, displaying, uploading, exporting, sharing, reporting,
  or logging diagnostics.
- Treat all mobile-provided diagnostics as untrusted input.
- Validate diagnostic payloads at the API boundary.
- Authorize every diagnostic upload, view, export, deletion, support link, and
  report.
- Rate-limit diagnostics submission and export to prevent abuse.
- Use HTTPS for diagnostic upload and avoid diagnostic sharing over insecure
  channels.
- Store tokens and secrets only through secure storage principles, never in
  diagnostic payloads.
- Do not log diagnostics before redaction.
- Protect local diagnostics with app lock where they include tenant or support
  context.
- Clear local diagnostic exports after retention expiry, logout, tenant switch,
  device revocation, or user request where policy allows.
- Audit sensitive diagnostic access.

Diagnostics are operationally useful only when users can trust the privacy
boundary.

## Risk Register

Key risks include:

- Diagnostics accidentally include secrets, tokens, raw payloads, or raw tenant
  data.
- Support gains broad device visibility outside a case scope.
- Device identifiers become silent cross-tenant tracking identifiers.
- Network status is mistaken for API success or failure.
- Offline detection hides maintenance, forced update, token expiry, or tenant
  suspension.
- Local exports remain on the device longer than policy allows.
- Users are asked to share diagnostics without understanding the contents.
- Admins enable broad diagnostics without impact preview or audit.
- Aggregated reports become identifying because groups are too small.
- Browser fallback gives false confidence about native device or network
  behavior.

These risks should be recorded before implementing or expanding diagnostics.

## Readiness Checklist

Before implementing or expanding device, network, or diagnostics behavior, the
product team should document:

- Which device information is needed and why.
- Which network states affect the workflow.
- Which offline signals determine the user-facing state.
- Which diagnostic categories are allowed.
- Which diagnostic categories are forbidden.
- Which users can preview, submit, export, delete, or revoke diagnostics.
- Which admins or support agents can view diagnostics.
- Whether diagnostics are linked to support requests.
- What redaction rules apply.
- What retention windows apply locally and server-side.
- What audit events are required.
- What happens when the app is offline, locked, logged out, revoked,
  suspended, in maintenance, force-updated, or tenant-switched.
- What reports may aggregate diagnostic outcomes.
- How support should explain diagnostic findings to the user.

Device, network, and diagnostics behavior is ready only when support can learn
enough to help and users can trust that private content stays out of the
diagnostic path.
