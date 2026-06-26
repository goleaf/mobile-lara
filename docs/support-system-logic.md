# Support System Logic

Updated: 2026-06-26

This document defines support system logic for the Mobile Lara SaaS system. It
explains how mobile users create support requests, how admins and support agents
review support requests, how support messages behave, how attachments should be
handled, how tenant context helps support, what support can and cannot access,
how support activity should be audited, and how offline support drafts should
behave. It is documentation only and does not define database structure,
database fields, migrations, indexes, seeders, routes, controllers, Livewire
components, Filament resources, NativePHP plugins, policies, gates, middleware,
jobs, services, local storage schemas, API endpoints, UI components, CSS,
JavaScript, background workers, queues, notification providers, storage
providers, or application logic.

Use this document with [Product Principles](product-principles.md), [Mobile UX
Principles](mobile-ux-principles.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md),
[Mobile Dashboard Logic](mobile-dashboard-logic.md), [Mobile Settings
Logic](mobile-settings-logic.md), [Mobile Permission Logic](mobile-permission-logic.md),
[Mobile App Shell Logic](mobile-app-shell-logic.md), [Mobile App Lock
Principles](mobile-app-lock-principles.md), [Two-System Boundary
Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Admin Control
Center Logic](admin-control-center-logic.md), [Role And Permission
Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version
Control Logic](mobile-version-control-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Data Privacy Principles](data-privacy-principles.md),
[Audit Logic](audit-logic.md), [Tenant Lifecycle Logic](tenant-lifecycle-logic.md),
[Tenant Admin Logic](tenant-admin-logic.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Offline-First Principles](offline-first-principles.md),
[Offline UX Logic](offline-ux-logic.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md),
[Conflict Resolution Logic](conflict-resolution-logic.md), [Records/Content
Module Logic](records-content-module-logic.md), [Search Logic](search-logic.md),
[Forms And Drafts Logic](forms-drafts-logic.md), [Notifications
Logic](notifications-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), and [API v1 Support
Contract](../contracts/api/v1-support.md): support is a tenant-scoped recovery
and assistance workflow, and Admin/API remains authoritative for request
acceptance, case state, assignment, visibility, diagnostics policy, attachment
acceptance, audit, notifications, and tenant boundaries.

Billing And Plan Logic is defined in `billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature
states, and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

## Support System Statement

Support helps mobile users recover from confusion, blocked work, sync problems,
permission questions, billing restrictions, app-version issues, feature
availability changes, and product defects without exposing more tenant or
personal data than the case requires.

A support request is a user-created or admin-created case timeline. It may
include messages, safe diagnostics, approved attachments, status changes,
assignment, escalation, notifications, and references to tenant-scoped product
state. The support workflow is not a private chat system and not a shortcut
around normal permissions.

Product rule: Admin/API owns support authority. Mobile owns the help entry
point, local draft experience, safe diagnostic preview, attachment selection,
offline draft preservation, and clear status presentation. Support agents own
triage and communication only within the role, tenant, case, and privacy scope
allowed by Admin/API.

## Goals

Support logic should:

- Give mobile users a fast way to ask for help from the active tenant context.
- Preserve user work when a support request cannot be submitted immediately.
- Help support agents understand the relevant user, tenant, app, feature,
  config, version, notification, sync, and offline context.
- Keep support visibility tenant-scoped, case-scoped, role-scoped, and
  least-privilege by default.
- Allow tenant admins and platform support teams to collaborate only where
  policy allows.
- Make messages predictable, auditable, and safe for mobile display.
- Treat attachments as untrusted input until Admin/API accepts, validates, and
  stores them under policy.
- Protect secrets, tokens, passwords, PINs, raw private payloads, payment
  secrets, unrelated tenant content, and unsynced private drafts.
- Make support actions explainable through audit history.
- Connect support with notifications, reports, diagnostics, sync health, and
  admin controls without giving support broad admin authority.

Support logic should not:

- Let mobile users bypass permissions, tenant state, billing state, feature
  flags, app-version policy, or maintenance state by opening a ticket.
- Let support agents browse tenant data without a case purpose and role scope.
- Treat local diagnostic data as trusted server truth.
- Upload attachments silently or without user awareness.
- Expose secure-storage values, access tokens, refresh tokens, private keys,
  passwords, PINs, biometric data, or raw authorization headers.
- Mix conversations from different tenants, users, devices, or cases.
- Use support messaging as the only durable status source for sync, billing,
  records, notifications, or app-version decisions.
- Store offline support drafts indefinitely when access, tenant, or privacy
  policy has changed.

## Ownership And Authority

| Concern | Admin/API authority | Mobile responsibility |
| --- | --- | --- |
| Support availability | Decide whether support is available by tenant, plan, role, feature flag, app version, maintenance state, and user status. | Show, hide, disable, or explain support entry points from resolved API context. |
| Request acceptance | Accept, reject, defer, or route support requests. | Gather user intent, preserve drafts, prevent duplicate submission, and render API outcomes. |
| Case state | Own status, priority, assignment, escalation, closure, reopen rules, and retention. | Present case state and allowed user actions without inventing local status. |
| Messages | Own accepted message timeline, sender identity, visibility, moderation, notifications, and read state where used. | Compose, preview, draft, send through API, and clearly show pending or failed state. |
| Attachments | Define allowed types, size limits, validation, scanning, retention, visibility, download rules, and deletion. | Let users select files only where allowed, preview safe metadata, and label pending uploads. |
| Diagnostics | Define what diagnostic context mobile may collect, send, store, redact, and expose to support. | Collect only allowed summaries and ask for user confirmation when policy requires. |
| Tenant context | Attach tenant, membership, app version, feature/config state, and sync context to support safely. | Use only the active API-confirmed tenant and clearly warn when context is stale or offline. |
| Support access | Define support-agent, tenant-admin, platform-admin, billing, and escalation visibility. | Never expose support-only controls inside mobile user flows. |
| Audit | Own durable history for support access and actions. | Surface user-visible history where useful without leaking internal notes. |

## Mobile Support Request Creation

Mobile users should create support requests from the current product context
whenever possible. A request from settings may include general app context. A
request from a record, notification, conflict, sync state, app-version prompt,
permission screen, billing block, or disabled feature should preserve that
context as support-safe metadata.

Mobile request creation principles:

- The user should see which tenant the request belongs to before submitting.
- The support form should be simple: issue category, subject or summary,
  description, optional safe diagnostics, and optional allowed attachments.
- Mobile should explain whether diagnostics are included and what kind of
  information they summarize.
- Mobile should not collect diagnostics that policy has not allowed.
- Mobile should not submit when the user is unauthenticated, suspended, outside
  tenant scope, blocked by app-version policy, or missing required support
  permission, except where Admin/API explicitly allows a recovery path.
- Mobile should use current API context for available categories and allowed
  actions when online.
- Mobile may let the user save a local support draft when offline or when the
  API is temporarily unavailable.
- Mobile must distinguish "saved locally", "pending send", "sent", "accepted",
  "needs attention", "failed", and "blocked".
- Mobile should avoid duplicate support requests by reusing pending drafts and
  showing existing related open cases when API context allows.
- Mobile should preserve user-written text after validation or network failure.

Support requests created from sensitive areas should require local app unlock
or re-authentication according to app-lock and authentication policy before
showing or sending sensitive context.

## Admin And Support Agent Review

Admins and support agents review support requests through the Admin/API control
plane. Review is a controlled operational workflow, not a cross-tenant browsing
permission.

Review principles:

- Support queues should be scoped by platform role, tenant role, assignment,
  escalation state, and case visibility rules.
- Support agents should see safe summaries before raw or sensitive details.
- Tenant admins should see only their tenant support cases unless platform
  policy delegates a narrower subset.
- Platform support may see cross-tenant queues only when their platform role and
  case assignment allow it.
- Billing managers should see support cases only when the case category or
  assignment requires billing context.
- Support views should explain relevant app version, config version, feature
  flags, tenant status, subscription state, permission state, notification
  state, sync status, offline state, and recent safe errors when available.
- Support agents should be able to ask for more information, send a reply,
  change case status, assign or escalate, request a retry, ask the user to
  refresh config, or request new diagnostics only when policy allows.
- Dangerous support actions, such as account recovery, session/device recovery,
  diagnostic export, tenant-impacting changes, manual state correction, or
  escalation to privileged teams, should require reason, confirmation, and audit
  history.
- Support internal notes, if planned, should be separate from user-visible
  messages and should never be sent to mobile by accident.

Support review should answer:

- Who needs help?
- Which tenant and membership does the case belong to?
- What did the user see?
- Which app version, feature flags, remote config, permissions, subscription,
  tenant status, and sync state applied?
- What has support already said or done?
- What data is support allowed to see for this case?
- What next action is safe and authorized?

## Support Messages

Support messages form the case timeline between the mobile user and authorized
support-side users.

Message principles:

- Messages should belong to one tenant, one case, and one sender identity.
- Message authorship should be clear: mobile user, support agent, tenant admin,
  system, or automated policy event.
- User-visible messages should be separate from internal support notes.
- Messages should be accepted and ordered by Admin/API, not by mobile-local
  timestamps alone.
- Mobile may show local pending messages while offline or sending, but must
  reconcile with API-accepted timeline when online.
- Messages should support status labels such as draft, pending, sent, accepted,
  failed, redacted, deleted by policy, or unavailable.
- Message edits and deletions, if allowed, should be governed by role, time,
  audit, notification, and retention policy.
- Support messages should be written for mobile readability: short, actionable,
  and clear about the next step.
- Messages that include instructions to change security, billing, sync, or
  account state should link users toward safe in-app flows instead of asking
  them to share secrets.
- System messages should clarify state changes such as assignment, escalation,
  status changes, closure, reopen, attachment rejection, or diagnostic request.

Messages should never contain passwords, raw tokens, recovery secrets, payment
secrets, private keys, PINs, biometric data, or hidden operational instructions.

## Attachments

Attachments help explain an issue, but every attachment is untrusted until the
server accepts it under policy.

Allowed attachment examples may include:

- Screenshots.
- Images from camera or gallery.
- Short audio notes where enabled.
- Exported support-safe diagnostic summaries.
- Documents or files where the tenant policy allows them.
- Scanner output or barcode evidence where the feature is enabled.

Attachment principles:

- Admin/API defines allowed types, size limits, count limits, retention,
  visibility, scanning, download, deletion, and export rules.
- Mobile should request native permissions only when the support feature is
  enabled and the user chooses an action that needs the permission.
- Mobile should explain why camera, microphone, file, scanner, notification, or
  storage access is needed before triggering native prompts.
- Mobile should preview safe metadata before upload, such as filename, type,
  approximate size, count, and privacy warning.
- Mobile should not silently include screenshots, logs, files, location,
  contact data, or private record content.
- Attachments should be tied to a support case and tenant context before support
  agents can see them.
- Rejected attachments should remain understandable to the user without exposing
  security internals.
- Failed uploads should not delete the user's draft message unless policy
  requires cleanup.
- Attachment links should expire or be permission-checked where shared outside
  the immediate support view.
- Attachment retention should respect tenant policy, case closure, deletion
  requests, legal hold, and privacy requirements.

If mobile is offline, attachment paths may be remembered only as local draft
references allowed by policy. The final file must still be validated and
accepted through API before it becomes part of the support case.

## Tenant Context

Tenant context helps support diagnose problems without asking the user to
explain invisible system state.

Support-safe tenant context may include:

- Tenant identifier or display name allowed for the user and agent.
- Current membership state.
- User role or permission category summary.
- Tenant lifecycle state such as active, trial, suspended, archived, or
  billing-blocked.
- Subscription or entitlement summary where the support role is allowed.
- App version and platform.
- Remote config version.
- Feature flag resolution summary.
- Maintenance or force-update state.
- Recent support-relevant notification, sync, conflict, or API error category.
- Device capability or permission status summary where policy allows.

Tenant context principles:

- Tenant context should be attached by API-confirmed state, not mobile guesswork.
- Mobile should label context as stale when offline or when it cannot refresh.
- Support should see only the tenant context needed for the case.
- Cross-tenant users must not leak one tenant's context into another tenant's
  ticket.
- Tenant switching should not move an unsent support draft to a different tenant
  without explicit user action and API confirmation.
- Suspended, archived, billing-blocked, or deleted tenants should show recovery
  or contact paths only as allowed by lifecycle policy.

Tenant context should make support faster without turning support into broad
tenant administration.

## What Support Can Access

Support can access:

- Cases assigned or visible to the support role.
- User-visible message timeline for those cases.
- Internal notes only where the support role permits.
- Safe diagnostic summaries accepted by Admin/API.
- Attachment metadata and files accepted under policy.
- Tenant, user, device, app-version, config, feature, permission, sync,
  notification, and billing summaries required to solve the case.
- Audit history related to the support case, according to role and privacy
  policy.
- Escalation state and next allowed support actions.

Support cannot access by default:

- Raw access tokens, refresh tokens, session secrets, passwords, PINs,
  biometric data, private keys, API credentials, or secure-storage values.
- Full local mobile cache.
- Unsynced mobile drafts unrelated to the case.
- Unrelated tenant records, attachments, messages, reports, or users.
- Payment secrets or full payment instrument data.
- Cross-tenant support cases without platform role and case scope.
- Admin controls outside the support role.
- Hidden feature flags, private incident notes, or security-sensitive
  internals unless explicitly allowed by escalation policy.
- Raw logs or diagnostics that include private payloads when a redacted summary
  is enough.

Support access should prefer summaries first. Deeper access should require a
case reason, role permission, scope check, audit trail, and, where appropriate,
additional approval.

## Support Activity Audit

Support activity needs audit history because support teams can influence user
trust, privacy, account recovery, diagnostics, device state, tenant state, and
operational outcomes.

Audit support activity for:

- Case creation.
- Message creation.
- Message edit, redaction, or deletion where allowed.
- Attachment upload, rejection, acceptance, download, export, deletion, or
  retention change.
- Diagnostic preview, user consent where required, submission, access, export,
  or deletion.
- Case assignment, reassignment, priority change, escalation, status change,
  closure, reopen, or merge.
- Support-agent access to a case or sensitive detail.
- Internal note creation or update where planned.
- Recovery actions, retry requests, config refresh requests, session/device
  actions, account recovery, or manual correction.
- Tenant-admin or platform-admin intervention.
- Notification sent because of a support update.
- Support setting, category, routing, or visibility policy changes.

Audit records should help answer:

- Who acted?
- Which tenant, user, case, message, attachment, or diagnostic summary was
  affected?
- What changed?
- Why was the action taken?
- Which role, permission, feature flag, policy, and tenant scope allowed it?
- Was the action visible to the mobile user?
- Was any sensitive data viewed, exported, redacted, or deleted?
- What was the before and after state when relevant?

Audit data should be protected from broad browsing, tenant leakage, mobile cache
exposure, and casual export. Support audit exists for accountability,
compliance, troubleshooting, and abuse prevention.

## Offline Support Drafts

Offline support drafts let users describe a problem while the problem is still
fresh, even when the network is unavailable.

Offline draft principles:

- Mobile may save a local support draft when the support feature is enabled or
  when cached policy allows offline drafting.
- Drafts should be scoped to the active API-confirmed tenant, user, device, and
  case intent.
- Drafts should show clear state: local draft, waiting for connection, needs
  review, blocked by policy, sending, failed, or sent.
- Drafts should preserve user-written text during app backgrounding, app lock,
  navigation, and temporary connection loss.
- Drafts should not include unapproved diagnostics or attachments unless policy
  allows local staging.
- Drafts should not be submitted automatically after a major context change
  without rechecking API authority.
- When the app reconnects, mobile should refresh support availability, tenant
  state, permissions, feature flags, app-version policy, and attachment rules
  before sending.
- If API policy changed, mobile should explain what can still be sent, what must
  be edited, and what is blocked.
- Logout, server revocation, tenant switch, tenant suspension, tenant archive,
  requested deletion, or app reset should lock, discard, or require review of
  drafts according to privacy and lifecycle policy.
- Draft deletion should warn users when meaningful text or selected attachments
  would be lost.

Offline drafts are not cases. They become support cases only after Admin/API
accepts them.

## Feature Flags And Remote Controls

Support behavior should be remotely controllable.

Admin/API may control:

- Whether support is available globally, by tenant, by plan, by role, by user,
  by app version, or by platform.
- Which categories are available.
- Whether support requests can include diagnostics.
- Whether attachments are allowed.
- Which attachment types and limits apply.
- Whether users can reply to closed cases.
- Whether support notifications are sent.
- Whether offline drafts are allowed.
- Whether support can request config refresh, sync retry, recovery, or
  escalation.
- Whether support is replaced by maintenance, billing, upgrade, or external
  contact guidance.

Mobile should treat these controls as API outcomes. If cached controls are
stale, mobile should fail closed for risky actions and allow only safe local
drafting where policy permits.

## Notifications And Support

Support updates often need notifications, but notifications do not replace the
case timeline.

Support notification principles:

- Notify mobile users when support replies, requests information, changes
  status, resolves a case, rejects an attachment, requests diagnostics, or
  escalates a case where policy allows.
- Notify support agents or admins when a new case, new message, failed upload,
  urgent category, security-sensitive case, billing-sensitive case, or stalled
  case needs attention.
- Deep links from support notifications must recheck authentication, app lock,
  tenant context, case visibility, feature flag state, and app-version policy.
- Push content should be privacy-safe and may need a generic lock-screen
  message.
- Offline mobile should show cached notification state separately from
  API-confirmed support timeline.

Read/unread and delivery state should remain Admin/API-authoritative where used.

## Status And Lifecycle

Support cases need predictable lifecycle language.

Recommended logical states:

- Draft.
- Submitted.
- Open.
- Waiting for support.
- Waiting for user.
- In review.
- Escalated.
- Blocked.
- Resolved.
- Closed.
- Reopened.
- Archived.

Lifecycle principles:

- Mobile users should see user-centered state labels and next actions.
- Support agents should see operational status, priority, assignment, and
  escalation state.
- Status changes should be audited and should trigger notifications only where
  policy allows.
- Closed cases should explain whether users can reply, reopen, or create a new
  request.
- Archived cases should remain visible only according to retention, privacy, and
  role rules.

## Privacy And Safety

Support is a privacy-sensitive workflow because users often ask for help during
broken, confusing, or stressful moments.

Privacy principles:

- Collect the smallest useful support context.
- Prefer summaries over raw payloads.
- Redact sensitive values before support sees diagnostics.
- Ask for user confirmation before sending diagnostics when policy requires it.
- Keep lock-screen, push, and cached support previews generic when content is
  sensitive.
- Avoid attaching unrelated records or tenant content automatically.
- Keep internal notes away from mobile users.
- Keep user-visible messages away from support-only private operational notes.
- Make support exports narrow, expiring, role-scoped, and auditable.
- Respect tenant deletion, user deletion, retention, and legal hold policy.

Support safety should be designed before implementation because retrospective
privacy cleanup is usually more expensive and less trustworthy.

## Risks

Key risks:

- Support becomes broad admin access.
- Attachments leak private tenant content.
- Diagnostics include secrets or unrelated local cache.
- Offline drafts are sent under the wrong tenant after tenant switching.
- Support replies reveal information the user cannot otherwise access.
- Push notifications expose sensitive support content on a locked device.
- Support agents act without audit history.
- Billing, security, and account recovery support actions become informal and
  unreviewable.
- Closed or archived support cases retain data longer than policy allows.
- Users confuse local drafts with submitted support cases.

Risk controls:

- Case-scoped visibility.
- Tenant isolation.
- Permission and feature-flag checks.
- Safe diagnostic schemas.
- Attachment validation and retention policy.
- Audit history.
- Mobile-safe state labels.
- Explicit user consent where diagnostics or attachments are sensitive.
- Admin impact previews for support policy changes.

## Acceptance Questions Before Implementation

Before implementing support behavior, documentation should answer:

- Which support categories exist and who controls them?
- Which users may create support requests?
- Which support requests are allowed while offline?
- Which support requests are allowed for suspended, invited, billing-blocked, or
  pre-login users?
- What diagnostic summaries can mobile collect?
- Which diagnostics require user confirmation?
- Which attachments are allowed, blocked, retained, or redacted?
- What can a support agent see before assignment?
- What can a tenant admin see?
- What can platform support see across tenants?
- Which support actions require confirmation or escalation?
- Which support actions notify the user?
- Which support actions are audited?
- What happens to drafts after logout, tenant switch, revocation, or tenant
  suspension?
- How does mobile explain local draft versus submitted case?

## Success Standard

Support system logic is ready for implementation only when support requests,
messages, attachments, tenant context, support visibility, audit, notifications,
offline drafts, feature flags, remote config, privacy, and tenant lifecycle
effects are documented before code. The final product should let users get help
quickly while keeping Admin/API authority, tenant isolation, privacy, security,
and auditability intact.
