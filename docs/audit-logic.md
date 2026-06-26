# Audit Logic

Updated: 2026-06-26

This document defines audit logic for the Mobile Lara SaaS system. It explains
which actions should be audited, why admin actions require audit trails, why
security events require audit trails, how audit history helps support and
compliance, how mobile activity should be represented to admins, what audit
logs should help answer, and how audit data should be protected. It is
documentation only and does not define database structure, database fields,
migrations, seeders, routes, controllers, policies, middleware, events,
listeners, Livewire components, NativePHP plugins, jobs, services, local
storage schemas, audit tables, indexes, queues, retention jobs, exports, or
application logic.

Use this document with [Product Principles](product-principles.md), [Target
User Roles](user-roles.md), [Role And Permission Logic](role-permission-logic.md),
[Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md),
[Mobile Client Responsibilities](mobile-client-responsibilities.md), [API-First
Principles](api-first-principles.md), [Authentication Principles](authentication-principles.md),
[Mobile App Lock Principles](mobile-app-lock-principles.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md),
[Mobile Version Control Logic](mobile-version-control-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Data Privacy Principles](data-privacy-principles.md),
and [Documentation-First Architecture](documentation-first-architecture.md):
audit history is the accountability layer for Admin/API authority, security
events, mobile activity summaries, support investigations, compliance review,
data privacy, and tenant-safe operational transparency.

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

## Audit Statement

Audit history explains what happened, who caused it, where it applied, and why
the system allowed or denied it.

Audit is not authorization. Admin/API still decides whether an action is
allowed. Audit records the important decision, attempt, outcome, and context so
the product can support users, review incidents, prove compliance, understand
mobile impact, and recover from mistakes.

Product rule: any action that changes authority, tenant state, user state,
security posture, billing entitlement, feature availability, mobile behavior,
support visibility, report/export access, sync outcome, or private-data access
should be auditable.

## Audit Ownership

Audit ownership belongs to the Admin/API system.

The mobile client may show safe local activity, label offline work, collect
mobile-safe context, and submit activity outcomes through the API. It must not
be the final source of audit truth for server-side authority, tenant decisions,
permission decisions, billing decisions, feature flag decisions, or conflict
decisions.

Authority split:

| Area | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Audit policy | Which actions, attempts, denials, support actions, security events, and system outcomes require audit history. | Showing local status and sending mobile-safe activity context through API when allowed. |
| Audit truth | Server-side timeline, actor identity, tenant scope, authorization result, support meaning, compliance meaning, and retention policy. | Last-known local activity summaries, offline queue labels, sync state display, and user-facing explanations. |
| Sensitive data protection | Redaction, access control, export control, tenant isolation, retention, legal hold, and audit access review. | Avoiding secrets, raw tokens, PINs, biometric details, private record payloads, and unnecessary local diagnostics in submitted activity. |
| Support visibility | What support agents, tenant admins, billing managers, super admins, and platform owners can see. | Clear mobile support context that helps users without exposing private cache contents. |

## Actions That Should Be Audited

Audit coverage should prioritize actions with authority, security, financial,
privacy, tenant, support, or mobile-impact meaning.

Admin control actions should be audited:

- Tenant creation, activation, suspension, restoration, deletion, archival, or
  ownership transfer.
- User invitation, activation, suspension, restoration, role change, permission
  change, tenant membership change, device trust change, or forced logout.
- Feature flag changes at global, tenant, user, plan, cohort, app-version,
  device, maintenance, or emergency scope.
- Remote configuration changes, including global defaults and tenant overrides.
- Mobile version rules, minimum supported versions, optional updates, forced
  updates, maintenance mode, store links, and update messaging.
- Billing plan changes, subscription state changes, quota changes, entitlement
  changes, invoice-sensitive actions, and billing recovery actions.
- Notification orchestration, broad announcement sends, support broadcast
  actions, and delivery-policy changes.
- Sync policy changes, conflict decisions, queue replay controls, forced retry,
  forced discard, and support-assisted recovery.
- Report access, report export, sensitive dashboard access, diagnostics export,
  and cross-tenant operational views.
- Support case access, support impersonation-like workflows, recovery actions,
  and any support action that changes a user, tenant, device, queue, or session
  state.
- Dangerous destructive or irreversible actions, including local wipe requests,
  tenant data removal, cache-clearing commands that affect recovery, and
  permanent admin decisions.

Security events should be audited:

- Login success, login failure, suspicious login patterns, rate-limit lockout,
  and credential validation outcomes where safe to record.
- Logout, logout-all-devices, current-device logout, session expiry, token
  refresh, token rotation, token revocation, and refresh failure that forces
  re-login.
- Password reset, password reset link sent, account verification, invitation
  acceptance, recovery flow, and support-assisted identity recovery.
- Permission denial, forbidden access attempts, hidden-resource access attempts
  where safe, tenant-boundary denials, and privilege escalation attempts.
- Device registration, device trust changes, blocked devices, app lock policy
  changes, secure-storage failure, biometric-disabled policy, and suspicious
  device posture changes.
- API abuse signals, repeated validation failures, unexpected replay attempts,
  stale offline queue replay, and conflict outcomes with security meaning.
- Admin authentication, admin session changes, admin privilege changes, and
  attempts to access admin-only routes or Livewire actions without authority.

Mobile activity should be audited when it affects server-side truth or support
meaning:

- Mobile login, session refresh, logout, tenant selection, and tenant switch
  outcomes.
- Bootstrap/context refresh, feature/config/version policy refresh, and
  update-required or maintenance outcomes.
- Offline actions queued, submitted, accepted, rejected, expired, conflicted,
  discarded, or retried.
- Sync start, sync finish, sync failure, conflict resolution, upload/download
  failure, and support-relevant queue health.
- Notification preference changes, permission-recovery actions, support contact,
  diagnostics submission, and user-visible safety prompts.
- Native capability use summaries only when product-relevant and safe, such as
  "camera capture submitted" rather than raw media contents.

System and integration events should be audited when they change product state:

- Scheduled policy changes, automated suspension/restoration, billing callbacks,
  notification provider outcomes, support routing changes, and bulk operations.
- Background sync reconciliation, conflict resolution, cleanup, retention, and
  export workflows.
- Failed jobs or integrations that affect user access, billing, notifications,
  mobile behavior, support visibility, or tenant data availability.

## Why Admin Actions Require Audit Trails

Admin actions can affect many users quickly.

Audit trails for admin actions are required because:

- Admins can change tenant access, user access, mobile behavior, billing
  entitlement, feature availability, support visibility, and security posture.
- Admin mistakes can disable mobile workflows, hide important actions, expose
  sensitive information, or block tenant work.
- Admin decisions need accountability: who changed what, for which tenant, when
  it happened, what the previous state meant, and why the change was allowed.
- Admin impact previews and confirmations are stronger when the final decision
  becomes reviewable later.
- Rollback and recovery need a trustworthy timeline.
- Support teams need to distinguish user error, admin change, policy change,
  billing state, feature rollout, maintenance, and platform incident.
- Compliance review often needs proof that high-risk actions were authorized,
  scoped, and traceable.

Admin audit history should be understandable to humans, not only machines.

## Why Security Events Require Audit Trails

Security audit history is the product's memory during an incident.

Security events require audit trails because:

- Authentication and authorization failures can reveal attack attempts,
  credential abuse, tenant-boundary probing, or broken client behavior.
- Session and token changes explain why a user was logged out, blocked, forced
  to re-authenticate, or unable to sync.
- Permission denials show whether a user lacked authority, used stale mobile
  state, crossed tenant boundaries, or tried an unavailable feature.
- Device and app-version outcomes explain whether a mobile issue comes from an
  old app, blocked device, secure-storage problem, offline replay, or
  maintenance state.
- Security investigations need sequence: attempt, denial, lockout, revocation,
  recovery, support action, and restoration.
- Suspicious patterns must be visible without exposing secrets or private
  payloads.

Security audit should protect users and tenants. It should never store
passwords, raw tokens, PINs, biometric templates, secret config values, private
record bodies, or unnecessary sensitive data.

## Support And Compliance Value

Audit history helps support answer "what happened?" without guessing.

Support value:

- Reconstruct user, tenant, device, app-version, feature, config, permission,
  billing, and sync timelines.
- Explain why a mobile user saw a disabled feature, forced update, maintenance
  state, permission denial, tenant unavailable state, or sync conflict.
- Identify whether the issue came from admin action, feature flag, remote
  config, app version, account state, tenant state, billing state, network
  state, or local mobile cache.
- Help support agents resolve tickets without requesting screenshots of
  private data or asking users to expose local cache contents.
- Escalate incidents with enough context for platform admins, security, or
  billing teams.

Compliance value:

- Demonstrate that high-impact actions were performed by authorized actors.
- Show tenant-scoped history without leaking other tenants.
- Prove that sensitive actions had confirmation, impact preview, and policy
  context where required.
- Support retention, legal hold, incident response, internal review, and
  customer-facing explanations.
- Show that audit access itself is controlled and reviewable.

## Mobile Activity Representation For Admins

Admins should see mobile activity as safe operational summaries, not raw device
surveillance.

Mobile activity should be represented to admins as:

- User, tenant, device, app version, and session context when safe and
  authorized.
- Current mobile state summaries: authenticated, locked, offline,
  maintenance-limited, forced-update, tenant-switching, sync-in-progress,
  permission-blocked, feature-disabled, or suspended.
- Sync summaries: queued, submitted, accepted, rejected, conflicted, retried,
  discarded, stale, blocked by permission, blocked by tenant state, or blocked
  by version policy.
- Feature/config/version outcomes: feature disabled, plan limited, config
  invalid, config fallback used, update required, maintenance active, or
  emergency disabled.
- Support-safe diagnostics: network status category, API reachability category,
  stale-cache age category, queue count category, app version, device platform,
  and last successful sync category.
- Mobile user-visible outcomes: "action blocked by permission", "queued while
  offline", "sync accepted", "sync rejected", "support diagnostics sent", or
  "tenant switch denied".

Mobile activity should not expose:

- Raw tokens, refresh credentials, passwords, PINs, biometric details, secure
  storage contents, or secret config values.
- Private cached record bodies unless the admin role and support/compliance
  purpose explicitly allow viewing that data.
- Native media contents, files, exact location, microphone data, screenshots,
  or device identifiers beyond what is necessary and authorized.
- Cross-tenant information to tenant-scoped admins.

Mobile audit should make support possible without turning mobile activity into
unbounded monitoring.

## What Audit Logs Should Help Answer

Audit history should answer practical product, support, security, and
compliance questions.

Audit logs should help answer:

- Who performed the action?
- Was the actor a platform owner, super admin, tenant admin, tenant manager,
  support agent, billing manager, mobile user, system process, or integration?
- Was the actor acting directly, through support, through automation, or through
  an API/mobile session?
- Which tenant, user, device, feature, config, version policy, report, support
  case, billing object, sync item, or resource was affected?
- What action was attempted?
- Was the action allowed, denied, partially applied, queued, accepted, rejected,
  conflicted, retried, reverted, or expired?
- What policy, permission, role, feature flag, tenant state, billing state,
  app-version rule, maintenance state, or security rule influenced the outcome?
- What was the safe before/after meaning?
- What mobile effect did the admin action create?
- What user-facing state did mobile show?
- Was the action online, offline, replayed from queue, or performed by a
  background process?
- Was support involved?
- Was the action high-risk, destructive, sensitive, or security-relevant?
- What needs follow-up, rollback, support escalation, incident response, or
  compliance review?

Audit history should support both narrow timeline review and high-level
operational reporting.

## Protecting Audit Data

Audit data is sensitive and must be protected.

Protection principles:

- Audit data should be append-oriented and resistant to silent tampering.
- Audit records should be tenant-scoped wherever tenant context exists.
- Cross-tenant audit visibility should require platform-level or support-level
  authority and a clear job purpose.
- Access to audit history should itself be auditable.
- Audit exports should require explicit permission, reason, and safe scope.
- Audit data should redact secrets, tokens, passwords, PINs, biometric data,
  private keys, raw credentials, secret config, and unnecessary PII.
- Audit data should prefer safe summaries over raw payloads.
- Audit data should preserve enough context for support and compliance without
  becoming a copy of the whole application database.
- Audit retention should match legal, security, tenant, and product
  requirements.
- Legal hold, incident hold, or support escalation may require preserving
  certain history longer than normal retention.
- Audit deletion or anonymization should be controlled, documented, scoped, and
  reviewed because it can affect compliance and incident response.
- Tenant admins should not see another tenant's audit history.
- Support agents should not see private payloads unless their role, scope, and
  case purpose require it.
- Billing managers should see billing-relevant audit history without gaining
  broad operational or private mobile data.

Audit protection must balance accountability with privacy. More logging is not
automatically better if it stores sensitive data that support and compliance do
not need.

## Audit Boundaries

Audit should not become a hidden business logic system.

Boundaries:

- Audit does not grant permission.
- Audit does not replace policies, gates, API authorization, role checks, or
  tenant isolation.
- Audit does not replace app lock, secure storage, token handling, or native
  permission control.
- Audit does not make stale mobile state authoritative.
- Audit does not require storing full private payloads.
- Audit does not guarantee rollback by itself; it provides the timeline and
  context needed to decide rollback safely.
- Audit should not expose implementation internals that would help attackers.

For high-risk admin and security actions, inability to record a required audit
event should be treated as a product risk. Future implementation must decide
which actions are blocked, delayed, queued, or support-routed when audit
capture is unavailable.

## Offline And Sync Audit Principles

Offline-first behavior needs careful audit meaning.

Principles:

- Creating a local offline action is not the same as the API accepting it.
- Mobile should represent offline work as draft, queued, pending, submitted,
  accepted, rejected, conflicted, expired, or discarded.
- Admins should see server-accepted mobile outcomes, not every private local
  keystroke or local cache read.
- Queued actions should be audited when submitted to the API and again when the
  API accepts, rejects, conflicts, or discards them.
- Offline actions should keep enough context to support later review without
  exposing unnecessary private local data.
- If permissions, feature flags, tenant status, account status, or app-version
  policy change while mobile is offline, audit should make the later denial or
  conflict understandable.
- Support should be able to see whether a mobile issue was caused by offline
  state, stale permissions, stale config, tenant suspension, version policy,
  sync conflict, or network/API unavailability.

Audit should make offline work explainable without turning local cache into
server truth.

## Risks

Audit logic has product, privacy, and security risks:

- Logging too little makes support and compliance guess.
- Logging too much can expose private tenant data, secrets, or sensitive mobile
  context.
- Audit records can be mistaken for source-of-truth business state.
- Audit access can become a privacy leak if support or billing roles are too
  broad.
- Tenant-scoped audit views can accidentally reveal cross-tenant information.
- Mutable or deletable audit history can undermine incident review.
- Missing audit on dangerous admin actions can make rollback and accountability
  difficult.
- Mobile offline actions can be misread as completed server actions.
- Diagnostics can leak tokens, config secrets, private cached data, or device
  identifiers if not redacted.

Mitigation principles:

- Audit high-impact actions and security events by default.
- Keep API authorization final.
- Keep audit summaries safe and tenant-scoped.
- Redact secrets and sensitive payloads.
- Audit access to audit history.
- Show mobile activity as operational summaries.
- Treat offline queued work as pending until API acceptance.
- Document audit expectations before implementing each feature.

## Acceptance Questions

Before audit behavior is implemented for a feature, the product decision should
answer:

- Does this action change authority, tenant state, user state, security
  posture, mobile behavior, billing entitlement, feature availability, support
  state, report access, or sync outcome?
- Is the action admin-originated, mobile-originated, API-originated,
  system-originated, integration-originated, or support-originated?
- Who needs to see the audit history?
- Which tenant or platform scope applies?
- What safe action meaning should be captured?
- What should be redacted?
- What should never be stored?
- What mobile activity summary should admins see?
- What support question should this audit history answer?
- What compliance or incident question should this audit history answer?
- What happens if the action is denied?
- What happens if the action is queued offline and accepted later?
- What happens if the action is queued offline and denied later?
- What permissions are required to view the audit history?
- Should access to this audit history itself be audited?
- What retention, export, privacy, or legal-hold rules apply?

If these questions are not answered, the audit behavior is not ready for
implementation.
