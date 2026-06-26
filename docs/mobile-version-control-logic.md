# Mobile Version Control Logic

Updated: 2026-06-26

This document defines the mobile app version control logic for Mobile Lara. It explains how admins control minimum supported versions, how optional updates work, how forced updates work, how maintenance mode works, how mobile behaves when API says the app is outdated, how store links and update messages are controlled, and how users are protected from broken old versions. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, Filament resources, policies, jobs, services, providers, or application logic.

Use this document with [Product Vision](product-vision.md): version control
protects the vision by keeping old mobile builds from breaking central
authority or local resilience.

Use this document with [Product Positioning](product-positioning.md): version
policy is part of the feature-controlled, API-first mobile platform posture, not
a device-local preference.

Use this document with [Core Product Principles](product-principles.md): mobile
version rules are admin-controlled, API-first, secure-by-default, tenant-aware,
documented, and modular.

Use this document with [API-First Principles](api-first-principles.md): version
policy reaches mobile only through predictable API responses that include
version rules, update state, maintenance state, user-safe errors, and
tenant-safe context.

Use this document with [Documentation-First Architecture](documentation-first-architecture.md):
version rules, optional updates, forced updates, maintenance mode, outdated
responses, store links, update messages, support context, audit expectations,
rollback, and old-version risks must be documented before implementation.

Use this document with [Target User Roles](user-roles.md): version prompts,
maintenance access, internal-only access, support visibility, and suspended or
guest behavior must follow role and account-state boundaries.

Use this document with [SaaS Value Map](saas-value-map.md): version policy must
protect stakeholder value by preserving platform rollout control, tenant
continuity, mobile-worker trust, support diagnosability, and billing/operations
clarity.

Use this document with [Two-System Boundary Logic](two-system-boundary.md):
Admin/API decides version safety, while mobile reports build context and
presents update, limited-mode, maintenance, deprecated, or blocked states.

Use this document with [Admin Control Center Logic](admin-control-center-logic.md):
app version, maintenance mode, and force-update policy must be controlled as
scoped, authorized, auditable admin decisions with clear mobile/API outcomes.

Use this document with [Mobile Client Responsibilities](mobile-client-responsibilities.md):
mobile owns the update, maintenance, limited-mode, blocked, deprecated,
store-link, local draft protection, and support-guidance experience, but it
does not own version authority.

Use this document with [Mobile UX Principles](mobile-ux-principles.md):
outdated, optional-update, forced-update, maintenance, deprecated, blocked,
store-link, stale-client, and local-draft states should stay clear, simple,
thumb-friendly, and supportive of secure session behavior.

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

## Version Control Statement

Mobile app version control is a control-plane responsibility.

The Admin/API system decides whether a mobile build is current, supported, recommended for update, deprecated, blocked, forced to update, maintenance-limited, internal-only, or incompatible with a required API contract. The mobile client reports its app version, build identity, platform, release channel, and relevant capability context to the API, then follows the server's resolved version policy.

Mobile may present update prompts, store links, release notes, maintenance banners, limited-mode screens, blocked states, and support guidance. It must not decide that an old build is safe after the API says it is unsafe.

Product rule: app-version policy protects users and tenants from stale mobile assumptions, broken API contracts, missing NativePHP capability support, known security issues, unsafe sync behavior, and unsupported release channels.

## Mobile Version Control Decision Contract

Every app-version policy should be documented before implementation because stale mobile clients can keep running long after Admin/API behavior changes. Admin/API owns version safety; mobile reports build context and presents only the resolved update, maintenance, blocked, or supported outcome.

| Decision area | Principle | Required outcome |
| --- | --- | --- |
| Minimum supported version | Admins define the oldest app version, build, platform, channel, or tenant-scoped version range that may operate safely. | Old clients that cannot satisfy API, security, NativePHP, sync, feature, permission, billing, or tenant assumptions receive deprecated, force-update, blocked, or maintenance-limited outcomes instead of normal access. |
| Optional update | Optional updates are non-blocking prompts for useful improvements while the current app remains safe. | Mobile continues normal operation, shows honest update guidance, allows dismissal or postponement when policy permits, and avoids interrupting active work. |
| Forced update | Forced updates are blocking controls for unsafe versions, stale API contracts, known security issues, revoked builds, invalid channels, unsafe sync behavior, or missing NativePHP capability support. | Mobile blocks normal workflows, preserves safe local state where possible, exposes update/support/logout/diagnostic actions only where allowed, and rechecks API policy after update. |
| Maintenance mode | Maintenance is a temporary service, tenant, feature, API, sync, notification, billing, support, platform, or version-range limitation. | Mobile shows maintenance, retry-later, limited-mode, draft-only, queueable, read-only, or blocked behavior based on API policy without treating maintenance as version safety. |
| Outdated API response | When API says the app is outdated, mobile treats that response as authoritative. | Mobile stops relying on cached feature, config, permission, billing, tenant, and sync state for protected actions, shows the resolved state, and revalidates after update before resuming normal work. |
| Store links and messages | Store links, distribution links, update copy, release notes, support copy, and retry guidance are controlled by Admin/API and scoped by platform, channel, tenant, locale, or release model. | Users receive safe, actionable messages and correct update destinations without exposing internal incident details, store credentials, or tenant-private reasoning. |
| Broken old-version protection | Version policy protects users from clients that can corrupt data, lose offline work, bypass changed enforcement, misunderstand API responses, or trigger broken NativePHP flows. | Old versions fail explicitly with next action, support path, local draft preservation where safe, and no broader access than a supported version would receive. |

This contract is intentionally principle-level. It does not create version storage, schemas, migrations, endpoints, validation classes, policies, Filament resources, Livewire components, jobs, services, provider integrations, store integrations, or application logic.

## What Admins Control

Admins should control mobile version policy through scoped, authorized, auditable settings.

Version controls should include:

- **Minimum supported version** - the oldest mobile version that may still operate normally.
- **Minimum recommended version** - the oldest version that may continue but should receive an optional update prompt.
- **Blocked version** - a version or range that must not operate because it is unsafe, broken, vulnerable, or contract-incompatible.
- **Forced update rule** - a policy that blocks normal operation until the user updates.
- **Optional update rule** - a policy that recommends updating without blocking normal operation.
- **Maintenance mode** - a platform, tenant, feature, API, sync, or notification maintenance state that changes mobile behavior temporarily.
- **Store or distribution link** - the safe destination for update action by platform, channel, tenant, region, or deployment model.
- **Update message** - user-facing copy explaining why an update is recommended or required.
- **Support message** - role-safe context support can use to explain version state without exposing private internals.
- **Grace period** - a planned window where old versions continue with warning before stronger enforcement.
- **Effective time** - when a version rule starts, changes, or ends.
- **Rollback expectation** - how admins recover if a version rule blocks too much or creates support load.

Admin controls should be scoped by platform, release channel, tenant, feature risk, API contract, NativePHP capability, security exposure, sync risk, rollout cohort, or emergency status.

Tenant-specific version policy may narrow or clarify global policy, but it must not make globally unsafe versions safe. Global security, API compatibility, and emergency blocks are the ceiling.

## Version States

The API should resolve version policy into clear mobile-safe states.

| State | Meaning | Mobile behavior |
| --- | --- | --- |
| Current | The app is on the expected version or newer. | Operate normally. |
| Supported | The app is allowed to operate even if it is not latest. | Operate normally, possibly with no prompt. |
| Optional update | A better version exists, but current use remains safe. | Show a dismissible or scheduled update prompt. |
| Recommended update | The app should update soon because support, UX, or feature compatibility is improving. | Show a stronger non-blocking prompt and release note. |
| Deprecated | The app still works, but support is ending. | Warn clearly, reduce risky feature access if policy requires it, and guide update. |
| Force update | The app cannot safely continue normal operation. | Block normal workflows and show update action. |
| Blocked | This version, platform, build, or channel must not operate. | Fail closed with support-safe message and update/support action. |
| Maintenance | Service, tenant, feature, API, sync, or notification behavior is temporarily limited. | Show maintenance state, retry timing, limited mode, or blocked action. |
| Internal-only | Version is allowed only for internal, test, support, or release roles. | Block normal users; allow authorized internal use only through API policy. |
| Unknown/unverified | The API cannot safely verify version state. | Fail conservatively for protected workflows and request refresh/support. |

Mobile should not expose raw policy names, admin notes, vulnerability details, store credentials, tenant-private reasoning, or internal release mechanics. It should show a clear product outcome and next action.

## Minimum Supported Versions

Minimum supported versions protect the platform from old clients that no longer match business, API, security, or NativePHP assumptions.

Principles:

- Minimum version rules should be set by Admin/API, not hardcoded only in the app.
- Minimum rules should be platform-aware because iOS, Android, desktop dev shells, internal builds, and NativePHP channels can differ.
- Minimum rules should be API-contract-aware because old clients may call stale endpoints or expect stale response shapes.
- Minimum rules should be security-aware because vulnerable or compromised builds may need immediate blocks.
- Minimum rules should be sync-aware because old queue formats, conflict behavior, or offline assumptions can become unsafe.
- Minimum rules should be tenant-aware only inside global safety limits.
- Minimum rules should be documented with owner, reason, affected users, affected tenants, expected mobile behavior, support message, audit expectation, and rollback path.

Admins should prefer a planned path:

1. Announce or document the upcoming minimum version change.
2. Move old versions into optional or recommended update state.
3. Move unsupported versions into deprecated state with a grace period where safe.
4. Force update or block only when the risk justifies it.
5. Keep support and reports able to explain who is affected.

Emergency security, data integrity, or API-compatibility incidents can skip the grace period, but the reason and support path should still be recorded.

## Optional Updates

Optional updates are for useful improvements that do not require blocking the current version.

Use optional updates when:

- A new version improves UX, performance, reliability, translations, NativePHP behavior, or support diagnostics.
- A feature works better on the new version but the old version remains safe.
- A tenant or cohort should be nudged to upgrade before a planned deprecation.
- Release notes or support guidance should be visible without interrupting work.

Mobile behavior:

- Continue normal operation.
- Show a non-blocking update prompt at appropriate moments.
- Let users dismiss, postpone, or continue when policy allows.
- Avoid interrupting active offline work, critical tasks, forms, captures, uploads, or sync recovery unless policy says the version is becoming unsafe.
- Keep update prompts honest: optional means the user may continue.
- Refresh version policy after app resume, login, tenant switch, support flow, or a meaningful time interval.

Optional updates should include platform-specific store links, release notes, support message, and a planned escalation path if the version will later become deprecated or forced.

## Forced Updates

Forced updates protect users, tenants, and the platform when old builds are unsafe.

Use forced updates when:

- The app version calls an API contract that is no longer safe to serve.
- The build has a known security issue.
- Offline queue or sync behavior can corrupt, duplicate, leak, or lose data.
- A NativePHP capability changed in a way old builds cannot handle safely.
- Billing, permission, tenant, or feature enforcement changed and old clients cannot represent the correct outcome.
- The release channel or build is invalid, revoked, or internal-only.
- Emergency incident response requires blocking old behavior.

Mobile behavior:

- Stop normal workflows.
- Show a clear required-update state.
- Provide the correct store or distribution link.
- Preserve safe local state where possible without granting new authority.
- Allow only safe utility actions such as update, logout, support contact, local diagnostics, or viewing limited cached guidance if policy allows.
- Prevent new protected actions and avoid replaying queued writes until updated and revalidated through API.
- Recheck API policy after update before resuming normal work.

Forced update should be support-visible and auditable. Admins should know why the block exists, who is affected, whether the block is global or scoped, and how to roll back if the rule was too broad.

## Maintenance Mode

Maintenance mode protects the product during planned work, incidents, migrations, provider outages, or operational recovery.

Maintenance can apply to:

- The whole platform.
- One tenant or tenant group.
- One mobile feature.
- One API contract group.
- Sync replay or offline acceptance.
- Notification delivery.
- Billing or entitlement checks.
- Support or diagnostics.
- A release channel, platform, or app version range.

Admin principles:

- Maintenance should define scope, reason, start time, expected end time, affected workflows, user-facing message, support message, retry guidance, and rollback path.
- Planned maintenance should avoid surprise where possible.
- Emergency maintenance should fail closed for protected operations.
- Maintenance state should be delivered through API, not inferred locally.
- Maintenance should not leak tenant-private operational details to unrelated users.

Mobile behavior:

- Show a clear maintenance state when API reports it.
- Block affected actions with retry-later or limited-mode guidance.
- Continue unaffected local presentation only when policy allows.
- Treat offline actions during maintenance as draft-only, queueable, blocked, or read-only according to API policy.
- Recheck policy before replaying queued work after maintenance ends.

Maintenance is different from forced update. Maintenance means the service or workflow is temporarily limited; forced update means the client version itself is unsafe or unsupported.

## Outdated API Response Behavior

When the API says the app is outdated, mobile must treat that response as authoritative.

Mobile should:

- Stop relying on cached feature, config, permission, billing, and sync state for protected actions.
- Show the resolved version state: optional update, recommended update, deprecated, force update, blocked, maintenance, or retry later.
- Show the API-provided safe message and next action.
- Use the API-provided store or distribution link for the current platform/channel.
- Preserve local drafts and queued intents where possible, but avoid replay until the app is updated, policy is refreshed, and server rules accept the action.
- Avoid creating new protected work if the version state is force update, blocked, unknown, or maintenance-blocked.
- Recheck boot/context after update before restoring normal navigation.

The API should return predictable user-facing error categories such as `version`, `stale_client`, `maintenance`, `blocked`, or `retry_later` so mobile can present the correct state without guessing.

## Store Links And Update Messages

Store links and update messages are controlled by Admin/API because they affect user trust and recovery.

Principles:

- Store links should be platform-specific and safe for the release channel.
- Update messages should be scoped by state: optional, recommended, deprecated, required, blocked, maintenance, or internal-only.
- Messages should explain what the user needs to do, not expose internal implementation details.
- Support messages can contain more context than mobile user messages, but still must avoid secrets and sensitive incident details.
- Links and messages should be versioned or revisioned so support can know what the user saw.
- Tenant-specific copy may clarify local policy, but it cannot bypass global safety blocks.
- Remote configuration may tune safe wording, but version policy decides whether the update is optional, forced, blocked, or maintenance-limited.

Update messages should be short, actionable, and honest:

- Optional update: "A newer version is available."
- Recommended update: "Update soon for the best experience."
- Deprecated: "This version will stop being supported."
- Force update: "Update required to continue."
- Blocked: "This app version is no longer supported."
- Maintenance: "Service is temporarily unavailable. Try again later."

Exact copy can vary by product tone, tenant, locale, or platform, but the underlying state must come from Admin/API.

## Protecting Users From Broken Old Versions

Version control protects users by preventing old app assumptions from creating confusing, unsafe, or data-damaging behavior.

Protection principles:

- Do not let old clients call removed or incompatible API behavior as if it still works.
- Do not accept offline replay from old queue formats when the server cannot safely interpret them.
- Do not let stale feature, permission, config, billing, or tenant assumptions authorize protected work.
- Do not expose broken NativePHP capability flows when the app version lacks safe handling.
- Do not silently degrade security behavior.
- Do not hide critical update requirements behind dismissible prompts.
- Do not expose vulnerability details in user-facing messages.
- Do not strand users without a next action, store link, support path, or retry guidance.

The safest old-version experience is explicit: the user understands whether they can continue, should update soon, must update now, should wait for maintenance, or must contact support.

## Offline Version Behavior

Offline behavior must be conservative because the latest version policy may be unknown.

Principles:

- If mobile has a fresh cached policy that says the version is supported, it may continue policy-allowed offline work until freshness expires.
- If cached policy says force update or blocked, mobile should keep blocking even offline.
- If cached policy is missing, expired, invalid, or unknown, mobile should avoid starting protected workflows and ask for connection or support.
- Offline drafts may be preserved locally, but replay must wait for online policy revalidation.
- Offline queue replay must recheck app version, tenant status, permissions, feature flags, remote config, billing/entitlement, maintenance, and current server state.
- Mobile should label stale version policy honestly when it affects user choices.

Offline-first does not override app-version safety.

## Admin Safety Model

Admins should be able to change version policy without accidentally blocking the wrong users.

Safe admin changes should include:

- Affected platform, app version, build, channel, tenant, feature, cohort, or user count.
- Current state and target state.
- Reason for the change.
- Expected mobile behavior.
- Store links and update messages.
- Support message and escalation path.
- Planned effective time and grace period.
- Compatibility with API contracts, feature flags, remote config, NativePHP capabilities, sync behavior, billing, and security requirements.
- Audit trail and rollback path.

High-impact controls such as forced update, blocked version, platform maintenance, or emergency disablement should require stronger confirmation and should be visible to support.

## Relationship To Feature Flags And Remote Config

Version control is related to feature flags and remote config, but it is not the same thing.

- Feature flags decide whether a feature is available for a user, tenant, plan, version, device, cohort, or emergency state.
- Remote config tunes safe runtime values such as copy, limits, thresholds, workflow options, notification presentation, support prompts, and tenant presentation.
- Version control decides whether the mobile build itself is safe to operate, update, or block.

Version policy is a higher-safety gate. A feature flag or remote config value cannot make a globally blocked app version safe.

## Admin Checklist

Use this checklist before planning app-version policy.

| Question | Required answer |
| --- | --- |
| What version state applies? | Current, supported, optional update, recommended update, deprecated, force update, blocked, maintenance, internal-only, or unknown/unverified. |
| Who controls it? | Platform owner, super admin, release manager, support escalation, tenant admin inside limits, or another documented role. |
| What scope applies? | Platform, tenant, user, role, device, app version, build, platform, release channel, feature, API contract, cohort, or maintenance scope. |
| Why is the policy needed? | Security, API compatibility, NativePHP capability, sync safety, supportability, rollout, billing/permission correctness, or incident response. |
| What does mobile show? | Prompt, warning, limited mode, blocked screen, maintenance screen, support path, store link, or retry state. |
| What can continue? | Normal operation, limited read-only use, drafts, support, logout, diagnostics, or nothing beyond update flow. |
| What happens offline? | Continue with fresh supported policy, block with cached forced policy, preserve drafts, or require online revalidation. |
| What support sees? | Version state, policy reason, store link, message revision, affected scope, sync impact, and safe escalation path. |
| What is the rollback path? | Revert to previous policy, extend grace period, change store link/message, limit by cohort, or switch to maintenance. |
| What is out of scope? | Database fields, migrations, endpoints, controllers, policies, resources, services, jobs, and code remain deferred until implementation. |

## Risks

| Risk | Version-control response |
| --- | --- |
| Old clients call stale APIs | Use minimum supported versions, additive contracts, deprecation windows, and stale-client errors. |
| Forced update blocks too many users | Preview affected scope, use phased rollout where safe, keep rollback ready, and expose support context. |
| Optional update feels mandatory | Keep optional copy honest and allow continuation when policy says continuation is safe. |
| Maintenance is confused with version block | Separate service/workflow maintenance from unsafe app-version states. |
| Store link is wrong | Scope links by platform/channel and validate before publishing policy. |
| User loses work during update | Preserve drafts and queues where safe; revalidate before replay after update. |
| Stale cached policy grants access | Treat cached policy as presentation guidance only for protected work; recheck API before server-trusted actions. |
| Tenant override bypasses global safety | Global security, API compatibility, blocked version, and emergency rules win over tenant preferences. |
| Support cannot explain state | Version policy must include safe reason, affected scope, message revision, and next action. |
| Vulnerability details leak | User-facing copy stays generic; sensitive incident context remains role-scoped. |

## Success Test

Mobile version control is successful when admins can safely define minimum supported versions, optional updates, forced updates, maintenance windows, store links, and update messages; the API resolves those decisions into predictable mobile-safe states; mobile follows outdated, blocked, or maintenance responses without guessing; users keep safe local work where possible; support can explain what happened; and broken old versions cannot continue unsafe behavior.
