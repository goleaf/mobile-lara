# Messaging And Community Logic

AI Feature Logic is defined in `ai-feature-logic.md`:
AI assistant purpose, summarization, categorization, smart suggestions,
moderation assistance, report generation assistance, admin AI controls,
tenant opt-in, privacy, and human-review principles must remain
tenant-enabled, plan-controlled, permission-aware, opt-in-only,
provider-neutral, human-reviewed, privacy-safe, audit-ready,
rate-limited, cost-aware, and Admin/API-authoritative.

Updated: 2026-06-26

This document defines messaging and community logic for Mobile Lara. It
explains conversation behavior, support chat behavior, message attachments,
moderation, reports and abuse flow, notification behavior, offline message
drafts, admin visibility boundaries, and privacy principles. It is
documentation only and does not define database structure, database fields,
migrations, indexes, seeders, routes, controllers, Livewire components,
Filament resources, NativePHP plugins, plugin manifests, policies, gates,
middleware, jobs, services, local storage schemas, API endpoints, UI
components, CSS, JavaScript, broadcast channels, push-provider configuration,
moderation-provider integration, storage-provider integration, queues, or
application logic.

Use this document with [Module Selection
Principles](module-selection-principles.md), [Product
Principles](product-principles.md), [Documentation-First
Architecture](documentation-first-architecture.md), [Two-System Boundary
Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration
Logic](remote-configuration-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), [Role And Permission
Logic](role-permission-logic.md), [Data Privacy
Principles](data-privacy-principles.md), [Audit Logic](audit-logic.md),
[Tenant Lifecycle Logic](tenant-lifecycle-logic.md), [Tenant Admin
Logic](tenant-admin-logic.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Offline-First
Principles](offline-first-principles.md), [Offline UX
Logic](offline-ux-logic.md), [Sync Lifecycle
Logic](sync-lifecycle-logic.md), [Conflict Resolution
Logic](conflict-resolution-logic.md), [Mobile UX
Principles](mobile-ux-principles.md), [Mobile App Shell
Logic](mobile-app-shell-logic.md), [Mobile Dashboard
Logic](mobile-dashboard-logic.md), [Mobile Settings
Logic](mobile-settings-logic.md), [Mobile Permission
Logic](mobile-permission-logic.md), [Mobile App Lock
Principles](mobile-app-lock-principles.md), [Native Feature
Strategy](native-feature-strategy.md), [Records/Content Module
Logic](records-content-module-logic.md), [Search Logic](search-logic.md),
[Forms And Drafts Logic](forms-drafts-logic.md), [Notifications
Logic](notifications-logic.md), [Support System
Logic](support-system-logic.md), [Reporting Logic](reporting-logic.md),
[Camera And Media Logic](camera-media-logic.md), [Voice Note
Logic](voice-note-logic.md), [Device, Network, And Diagnostics
Logic](device-network-diagnostics-logic.md), [Field Service
Logic](field-service-logic.md), [Logistics Delivery
Logic](logistics-delivery-logic.md), [Booking Logic](booking-logic.md), and
[Commerce Logic](commerce-logic.md): messaging/community is an optional
tenant-scoped communication module, while Admin/API remains authoritative for
membership, channel access, conversation eligibility, message acceptance,
attachment acceptance, moderation, abuse handling, notification targeting,
retention, reporting, audit, exports, support visibility, privacy, feature
flags, plan limits, and tenant boundaries.

## Messaging And Community Statement

Messaging and community features help tenant users communicate inside a
controlled product space. They may include direct messages, group
conversations, tenant channels, announcements, support chat surfaces,
community discussion, replies, reactions, read state, abuse reports,
moderation actions, attachments, and notifications.

The product goal is not to make the mobile client a standalone chat authority.
The goal is to give mobile users a simple, safe communication experience while
Admin/API controls who can participate, what can be seen, what can be sent,
what is moderated, what is retained, what is reported, what support can view,
what admins can audit, and what notifications may leave the app.

Product rule: mobile may present conversations, preserve local drafts, attach
approved local media or files, show cached message summaries, and explain
pending or offline state, but a message is not delivered, a read state is not
trusted, an attachment is not accepted, a moderation decision is not final, and
an abuse report is not filed until Admin/API accepts it.

## Goals

Messaging and community logic should:

- Let tenants communicate without losing tenant isolation, role boundaries,
  privacy, auditability, moderation control, or Admin/API authority.
- Let admins control who may create conversations, join channels, post
  messages, send attachments, report abuse, moderate content, export records,
  receive notifications, and view reports.
- Let mobile users see simple conversation states: unread, draft, sending,
  pending, sent, failed, blocked, moderated, deleted, hidden, reported,
  archived, muted, and offline-limited.
- Keep conversation membership, participant eligibility, message acceptance,
  attachment acceptance, notification targeting, moderation decisions,
  retention, exports, reports, and audit in Admin/API.
- Make support chat feel conversational without turning support into a private
  backdoor around case scope, tenant scope, or user permissions.
- Make abuse reporting and moderation visible enough to build trust without
  exposing protected reporter, moderator, tenant, or security information.
- Keep offline behavior useful for drafts and reading safe cached content
  without pretending offline messages are delivered.

Messaging and community logic should not:

- Define chat tables, message schemas, broadcast channels, endpoint names,
  Livewire components, notification providers, moderation providers, or code.
- Let mobile decide conversation membership, channel access, moderation
  outcomes, abuse outcomes, retention, export authority, support visibility,
  or tenant-wide rules.
- Let cached messages, local drafts, notification payloads, or read badges
  become server truth.
- Expose one tenant's conversations, attachments, reports, abuse signals,
  participants, support cases, exports, or diagnostics to another tenant.
- Turn support agents into unrestricted community observers.
- Store secrets, access tokens, secure-storage values, payment information,
  private diagnostics, or unrelated tenant data in messages or attachments.
- Use notifications to leak message content on lock screens or across tenant
  boundaries.

## Messaging Meaning

Messaging represents tenant-scoped communication intent and communication
history. Community represents tenant-scoped spaces where multiple users can
discover, read, participate, or receive announcements under admin rules.

Messaging should be understood through:

- **Tenant context**: which tenant owns the conversation, community space,
  support case, membership, notification policy, retention policy, and reports.
- **Participant context**: which users, roles, groups, support agents, tenant
  admins, guests, invited users, or suspended users may see or act.
- **Conversation context**: direct message, group thread, channel, support
  case, announcement, broadcast, record-linked conversation, order-linked
  conversation, booking-linked conversation, or module-specific discussion.
- **Message context**: body, attachment summary, sender display, timestamp
  meaning, edit/delete state, moderation state, delivery state, read state,
  reply context, and notification state.
- **Moderation context**: reported content, hidden content, blocked users,
  muted users, escalations, moderator decisions, appeals, audit, and retention.
- **Mobile context**: cache freshness, offline draft state, sending state,
  failed state, attachment queue, muted notifications, and safe deep links.
- **Admin context**: tenant settings, plan limits, feature flags, retention,
  moderation rules, support visibility, exports, reports, and audit.

## Conversation Behavior

Conversation behavior should make communication predictable without giving
mobile local authority.

Conversation behavior should:

1. Resolve active tenant context before showing any conversation.
2. Show only conversations allowed by tenant state, plan, role, permission,
   feature flag, app version, remote config, moderation state, membership, and
   support scope.
3. Distinguish direct messages, group conversations, channels, announcements,
   support chats, and module-linked conversations.
4. Show participant labels that are safe for the current user and tenant.
5. Show message states clearly: draft, sending, pending, sent, delivered where
   supported, read where supported, failed, blocked, moderated, hidden,
   deleted, archived, muted, and reported.
6. Let users compose, reply, edit, delete, react, quote, attach, mute, pin, or
   report only when API-resolved rules allow those actions.
7. Re-check API authority before opening a deep-linked conversation or message.
8. Avoid relying on push notifications, cached summaries, or stale unread
   counts as proof of current access.

Conversation principles:

- Admin/API owns conversation membership and message acceptance.
- Mobile owns the conversation experience, local draft experience, safe
  cached display, pending indicators, and clear failure feedback.
- Read state should be treated as a server-resolved user state, not only a
  local scroll position.
- Message ordering should be predictable and explain pending or offline gaps.
- Edited, deleted, hidden, or moderated messages should leave safe user-facing
  context where policy requires it, without leaking hidden content.
- Conversation search should follow search privacy and tenant isolation rules.

## Support Chat Behavior

Support chat is a conversational view over a support case. It should feel fast
and human while staying case-scoped and privacy-safe.

Support chat behavior should:

1. Exist only inside a support request, support case, or support-authorized
   tenant context.
2. Show case state, assigned support visibility, expected response posture,
   and whether the conversation is active, waiting, closed, escalated,
   reopened, archived, or blocked.
3. Let mobile users draft support replies offline where policy allows.
4. Let support agents request clarification, request diagnostics, ask for
   attachments, update case status, or escalate only through allowed case
   authority.
5. Keep diagnostic sharing explicit and user-controlled.
6. Prevent support chat from becoming a workaround for billing, permission,
   tenant, feature flag, or moderation rules.
7. Preserve audit history for status changes, visibility changes, escalations,
   agent replies, diagnostic requests, attachment requests, and restricted
   access.

Support chat principles:

- Support chat is not unrestricted private messaging.
- Support visibility should be case-scoped, tenant-scoped, role-scoped, and
  least-privilege by default.
- Tenant admins may see support chat only when policy allows.
- Platform support may see tenant support chat only for a legitimate support
  purpose, under audit and privacy rules.
- Closed support chat should remain readable or hidden according to retention
  and role policy, not mobile preference alone.

## Message Attachments

Attachments are untrusted input until Admin/API accepts them. They may include
photos, files, screenshots, voice notes, diagnostics exports, record links,
commerce references, booking references, delivery proof references, or other
module-specific evidence.

Attachment behavior should:

1. Explain why an attachment is requested or useful before users share
   sensitive files, screenshots, diagnostics, media, or voice notes.
2. Respect feature flags, plan limits, tenant policy, role permissions,
   native permissions, file type policy, size policy, retention policy, and
   offline policy.
3. Show local attachment states: selected, previewed, queued, uploading,
   uploaded, failed, rejected, removed, expired, redacted, or blocked.
4. Avoid requesting camera, microphone, file, photo, or diagnostics access
   when the relevant messaging feature is disabled.
5. Validate attachment acceptance through API before treating it as part of a
   sent message.
6. Keep attachment previews safe, tenant-scoped, and privacy-aware.
7. Avoid including attachment contents in notifications, diagnostics, logs,
   reports, or exports unless policy explicitly allows it.

Attachment principles:

- Mobile may stage and preview attachments locally.
- Admin/API owns acceptance, storage policy, visibility, retention, redaction,
  moderation, and audit.
- Failed uploads should not discard the message draft unless the user chooses
  to remove it.
- Attachments should be separated by tenant and conversation context.
- Sensitive attachments should be excluded from lock-screen notifications and
  broad support diagnostics by default.

## Moderation

Moderation protects users, tenants, support teams, and the platform from abuse,
spam, harassment, unsafe content, privacy violations, impersonation, fraud,
and off-topic or policy-violating communication.

Moderation behavior should:

1. Define which spaces are pre-moderated, post-moderated, admin-only,
   support-reviewed, automated-review-assisted, or user-report-driven.
2. Let admins configure moderation posture only within platform policy, plan
   limits, legal requirements, and tenant boundaries.
3. Let moderators hide, restore, lock, archive, remove, escalate, or mark
   content according to role and policy.
4. Let users understand when their message is pending moderation, hidden,
   removed, blocked, or under review.
5. Protect reporter identity where policy requires it.
6. Avoid exposing moderator notes, risk scores, internal labels, detection
   details, or abuse patterns to users who should not see them.
7. Audit moderation decisions that affect visibility, access, retention,
   exports, reports, or user status.

Moderation principles:

- Moderation decisions belong to Admin/API, not mobile cache.
- User-facing moderation messages should be clear without revealing internal
  detection logic.
- Automated moderation, if introduced later, should assist review rather than
  silently replace documented policy.
- Moderation should fail closed for unsafe content and fail kind for ordinary
  user confusion.
- Suspended users should not be able to continue posting through cached
  screens, queued drafts, or old app versions.

## Reports And Abuse Flow

Reports and abuse flows give users and admins a structured way to flag unsafe,
unwanted, illegal, spammy, fraudulent, harassing, private, or policy-violating
messages without escalating every issue into broad support access.

Reports and abuse flow should:

1. Let users report messages, conversations, attachments, users, channels, or
   community spaces where policy allows.
2. Ask for only the minimum useful context: reason category, optional note,
   affected content, and whether the user needs help or blocking.
3. Preserve the reported content according to retention and evidence policy,
   even if it becomes hidden to ordinary users.
4. Protect the reporter from retaliation where policy requires it.
5. Give users a simple acknowledgement without promising a specific outcome.
6. Route abuse reports to the correct tenant, moderator, support queue, or
   platform review path.
7. Distinguish abuse reports from support tickets, security events, legal
   requests, billing requests, and ordinary bug reports.
8. Audit report submission, review, escalation, closure, and high-risk
   moderation actions.

Reports and abuse principles:

- Abuse reporting should be available where communication can cause harm.
- Reported content should be handled as sensitive evidence.
- Mobile may draft an abuse report offline only when policy allows, but the
  report is not filed until API accepts it.
- Users should understand when blocking, muting, leaving, or contacting support
  is more appropriate than an abuse report.
- Admins should see impact before changing report categories, moderation
  routing, retention, or visibility.

## Notification Behavior

Messaging notifications help users notice relevant communication without
leaking content or creating notification fatigue.

Notification behavior should:

1. Respect tenant policy, user preferences, role permissions, channel
   membership, mute settings, quiet hours, plan limits, feature flags, app
   version, device permission state, and moderation state.
2. Support safe categories such as new message, mention, reply, support reply,
   announcement, moderation outcome, abuse report update, invite, channel
   change, or message failure.
3. Hide message body content from lock-screen or push payloads when policy
   requires privacy.
4. Deep-link only into conversations that current API authority still allows.
5. Avoid notifying users about content they cannot access, content removed by
   moderation, muted conversations, blocked senders, suspended tenants, or
   expired support cases.
6. Treat push delivery as a hint, not read confirmation.
7. Keep in-app inbox and unread state aligned with Admin/API truth.

Notification principles:

- Admin/API owns targeting, eligibility, delivery policy, unread truth, and
  deep-link safety.
- Mobile owns permission education, local display, muted-state presentation,
  offline labels, and fallback behavior.
- Notifications should never expose cross-tenant content.
- High-volume channels should prefer summaries, mentions, digests, or muted
  defaults where policy allows.

## Offline Message Drafts

Offline messaging should protect user work without pretending communication
has happened.

Offline draft behavior should:

1. Let users compose local drafts where tenant, plan, role, feature flag,
   remote config, app version, and conversation policy allow it.
2. Label drafts as local and unsent while offline.
3. Preserve draft text and staged attachments according to local storage,
   app lock, tenant switch, logout, and privacy policy.
4. Queue send intent only when policy allows and the target conversation is
   likely to remain valid.
5. Revalidate conversation membership, user permission, moderation policy,
   attachment policy, tenant state, app version, and feature flags before
   sending after reconnect.
6. Show clear outcomes after reconnect: sent, failed, rejected, needs edit,
   conversation unavailable, attachment rejected, user suspended, tenant
   suspended, feature disabled, or moderation review required.
7. Avoid replaying drafts across tenant switches or user sessions.

Offline principles:

- Offline draft is local user work, not delivered communication.
- Offline read state and unread counts may be displayed as cached hints, not
  trusted truth.
- Draft sync should be idempotent where API accepts replayable sends.
- Users should be able to delete local drafts without needing online access.
- Sensitive drafts should be protected by app lock and secure local data rules.

## Admin Visibility Boundaries

Admin visibility must be powerful enough to operate the tenant safely and
limited enough to preserve user trust.

Admin visibility should:

1. Separate platform owner, super admin, tenant admin, tenant manager,
   support agent, billing manager, moderator, mobile user, invited user,
   suspended user, and guest visibility.
2. Let platform admins manage global messaging policy without casually reading
   tenant conversations unless a documented support, moderation, security, or
   legal purpose allows it.
3. Let tenant admins manage tenant spaces, membership, moderation settings,
   reports, exports, and retention only inside their tenant and only when
   delegated by platform policy.
4. Let moderators view reported or policy-relevant content without receiving
   unrestricted access to every private conversation.
5. Let support agents see support chat content only inside assigned or
   permitted support cases.
6. Hide private direct messages from admin dashboards by default unless
   policy, consent, abuse report, support escalation, or legal process allows
   controlled access.
7. Audit privileged views, exports, moderation actions, retention changes,
   report reviews, and support escalations.

Visibility principles:

- Admin visibility is not the same as admin control.
- Least privilege should be the default for every messaging surface.
- Cross-tenant visibility should be impossible through ordinary admin use.
- Exports should be permission-gated, scoped, audited, privacy-reviewed, and
  retention-aware.
- Billing users should not see message content unless explicitly allowed for a
  billing support purpose.

## Privacy Principles

Messaging can contain sensitive, personal, commercial, operational, support,
health, location, payment-adjacent, legal, or private tenant information.
Privacy rules must be stricter than ordinary display rules.

Privacy principles:

- Keep messages, participants, attachments, read state, reports, abuse
  signals, moderation notes, exports, and diagnostics tenant-scoped.
- Apply least privilege to every conversation and admin view.
- Never expose secure tokens, passwords, PINs, raw diagnostics, private keys,
  payment secrets, biometric data, unrelated tenant records, or hidden
  support data through messages.
- Avoid sensitive message bodies in push notifications, lock-screen content,
  logs, diagnostics, and broad reports.
- Let users understand when messages are visible to participants, admins,
  support, moderators, or compliance reviewers.
- Treat reporter identities, moderator notes, abuse evidence, hidden content,
  and legal review context as sensitive data.
- Define retention, deletion, archive, export, and restore behavior before
  implementing communication features.
- Protect local cached messages and drafts with app lock, tenant separation,
  secure session behavior, logout cleanup, and offline privacy rules.
- Do not use messaging metadata to infer behavior for reports beyond the
  documented measurement purpose.

## Community Spaces

Community spaces are shared tenant communication areas such as channels,
groups, announcement boards, project rooms, team rooms, topic spaces, or
module-specific discussions.

Community spaces should:

1. Be tenant-enabled and plan-controlled.
2. Have documented purpose, audience, allowed content, posting rules,
   moderation posture, retention, notification policy, and admin owner.
3. Support read-only, announcement-only, members-only, invite-only,
   moderated, archived, and closed states where policy requires.
4. Explain unavailable states without showing hidden content.
5. Avoid becoming a generic unbounded feed when the tenant needs a specific
   workflow conversation.
6. Provide clear exits, mute controls, report controls, and support routes.

Community principles:

- A community space should exist because it solves a tenant communication
  problem, not because chat is technically possible.
- Announcements should be clearly separate from ordinary conversations.
- Membership and posting rules should be visible enough for users to avoid
  mistakes.
- Community reports should measure adoption and health without exposing
  private content to broad admin audiences.

## Message Lifecycle

Messages should have understandable product states before implementation.

| State | Meaning |
| --- | --- |
| Draft | Local user text or attachment intent that has not been accepted by API. |
| Queued | A send intent is waiting for online API access where policy allows. |
| Sending | Mobile is attempting to submit the message to API. |
| Pending review | API accepted the message for moderation or policy review, but it is not broadly visible. |
| Sent | API accepted the message for the allowed conversation. |
| Failed | API or network could not accept the message; user action may be needed. |
| Rejected | API refused the message because of permission, policy, moderation, tenant, plan, version, or attachment rules. |
| Edited | API accepted a change and exposes edit context according to policy. |
| Deleted | API accepted removal or hiding according to policy. |
| Hidden | Moderation or admin policy hides the content from some or all users. |
| Reported | A user or system report exists; visibility may or may not change. |
| Archived | Conversation or message is no longer active but may remain retained. |

Lifecycle principles:

- State labels should be mobile-friendly and consistent.
- Mobile should not invent states that Admin/API cannot explain.
- Deleted or hidden content should follow retention and audit policy.
- Failed or rejected drafts should preserve recoverable user work where safe.

## Reporting Principles

Messaging reports should help admins understand health, usage, support load,
moderation load, abuse risk, notification effectiveness, and tenant value
without becoming broad surveillance.

Reports may describe:

- Conversation volume by tenant, module, space, or time period.
- Active community spaces and participation trends.
- Unread and notification health in aggregate.
- Support chat response posture and resolution trends.
- Abuse report volume, moderation backlog, and outcome categories.
- Attachment volume and failure/rejection rates.
- Offline draft and failed send rates.
- Muted, archived, or disabled spaces.

Reports should not:

- Expose private message content to users who do not need it.
- Expose reporter identities broadly.
- Expose hidden content, moderator notes, legal details, or private
  diagnostics outside allowed review.
- Treat message volume alone as productivity or quality.
- Cross tenant boundaries.

## Support Principles

Messaging and support overlap, but they are not identical.

- Ordinary conversations are for tenant communication.
- Support chat is for help and recovery inside support scope.
- Abuse reports are for safety and policy review.
- Security reports are for possible security events.
- Billing conversations are for authorized billing context.

When a user needs help from a conversation, mobile should route them to the
right support or report flow without exposing unrelated conversation data.
Support agents should receive only the message context needed for the case,
not unrestricted access to the user's communication history.

## Rollout And Rollback Principles

Messaging/community should roll out gradually because it affects privacy,
support, moderation, notifications, storage, reporting, and user trust.

Rollout principles:

- Start with low-risk tenant pilots, clear feature flags, limited channels,
  documented retention, explicit support routes, and moderation readiness.
- Use plan gates, tenant gates, role gates, app-version gates, and remote
  config to limit exposure.
- Preview mobile impact before enabling messaging: new shortcuts, settings,
  notification preferences, support routes, native permissions, storage use,
  offline drafts, reports, and moderation workload.
- Track abuse reports, failed sends, attachment failures, notification volume,
  support escalations, and privacy complaints.

Rollback principles:

- Emergency disable should stop new message sends, hide unsafe entry points,
  preserve local drafts according to policy, and explain what will happen when
  online.
- Disabling messaging should not silently delete sent conversation history
  unless documented retention/deletion policy says so.
- Admins should understand pending drafts, queued attachments, moderation
  queues, abuse reports, notification campaigns, support chats, and exports
  before disabling a tenant's communication features.
- Rollback should not expose cross-tenant data or bypass API authority.

## Risks

Key risks:

- Cross-tenant message leakage.
- Admin overexposure to private conversations.
- Support chat becomes a backdoor around permissions.
- Push notifications leak private message content.
- Offline drafts are mistaken for delivered messages.
- Attachments upload private screenshots, diagnostics, or secrets.
- Abuse reporting exposes the reporter or mishandles evidence.
- Moderation decisions are inconsistent, unaudited, or opaque.
- Users continue posting after suspension through stale cache or old app
  versions.
- Reports become surveillance instead of operational measurement.
- Deletion, retention, export, and legal hold rules are unclear before launch.

Risk controls:

- Keep Admin/API authoritative for membership, posting, attachment acceptance,
  notifications, moderation, reports, abuse handling, retention, exports,
  support visibility, and audit.
- Keep mobile clear about draft, queued, sending, sent, failed, rejected,
  moderated, hidden, reported, archived, and offline states.
- Use tenant isolation, least privilege, feature flags, remote config,
  app-version gates, notification privacy, attachment validation, moderation
  policy, abuse reporting, audit history, and retention rules.
- Require impact preview and confirmation for dangerous admin messaging
  changes.
- Document every messaging workflow before implementation.

## Readiness Checklist

Before implementing messaging/community behavior, the product documentation
should answer:

- Which tenants and plans can use messaging/community?
- Which roles can create, read, post, reply, edit, delete, react, attach,
  report, moderate, export, view reports, or manage settings?
- Which conversation types are allowed: direct, group, channel, announcement,
  support chat, record-linked, commerce-linked, booking-linked, delivery-linked,
  or module-specific?
- Which spaces are public inside tenant, private, invite-only, read-only,
  moderated, archived, or disabled?
- Which message states are user-facing and which are admin-facing?
- Which attachments are allowed, blocked, retained, redacted, or review-only?
- Which native permissions may be needed for attachments, media, voice notes,
  files, diagnostics, or notifications?
- Which messages can be drafted offline and which actions require online API
  access?
- How should mobile show draft, queued, sending, sent, failed, rejected,
  moderated, hidden, reported, archived, offline, and disabled states?
- How do mute, block, leave, report, support, moderation, and notification
  choices interact?
- What can tenant admins, platform admins, support agents, moderators, billing
  users, and mobile users see?
- Which data appears in reports, exports, audit history, support views,
  diagnostics, notifications, and legal or compliance review?
- What happens when a tenant, plan, feature flag, permission, app version,
  conversation, channel, user status, support case, moderation rule,
  notification policy, or retention policy changes while the device is
  offline?

## Acceptance Principle

The messaging/community module is ready for implementation only when the team
can trace every messaging action to:

- A documented tenant and plan rule.
- A documented conversation or community-space purpose.
- A documented participant and permission rule.
- A documented feature flag and remote config rule.
- A documented API authority.
- A documented message lifecycle outcome.
- A documented attachment rule.
- A documented notification rule.
- A documented offline limitation.
- A documented moderation and abuse-reporting rule.
- A documented admin visibility boundary.
- A documented privacy and retention boundary.
- A documented audit and reporting meaning.
- A documented support visibility rule.

If any of those are unclear, the correct next step is more documentation, not
application code.
