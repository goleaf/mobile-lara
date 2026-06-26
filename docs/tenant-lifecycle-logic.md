# Tenant Lifecycle Logic

Updated: 2026-06-26

This document defines tenant lifecycle logic for the Mobile Lara SaaS system. It
explains tenant creation, onboarding, trial state, active state, suspended
state, archived state, billing-blocked state, tenant deletion or requested
deletion, tenant restore principles, and what mobile users see in each state.
It is documentation only and does not define database structure, database
fields, migrations, seeders, routes, controllers, Livewire components,
Filament resources, NativePHP plugins, policies, middleware, jobs, services,
local storage schemas, billing tables, deletion jobs, restore jobs, audit
tables, queues, exports, or application logic.

Use this document with [Product Principles](product-principles.md), [Target
User Roles](user-roles.md), [Role And Permission Logic](role-permission-logic.md),
[Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md),
[Mobile Client Responsibilities](mobile-client-responsibilities.md), [API-First
Principles](api-first-principles.md), [Authentication Principles](authentication-principles.md),
[Mobile App Shell Logic](mobile-app-shell-logic.md), [Mobile Dashboard Logic](mobile-dashboard-logic.md),
[Mobile Settings Logic](mobile-settings-logic.md), [Mobile App Lock Principles](mobile-app-lock-principles.md),
[Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md),
[Mobile Version Control Logic](mobile-version-control-logic.md), [Audit
Logic](audit-logic.md), [Data Privacy Principles](data-privacy-principles.md),
[Tenant Admin Logic](tenant-admin-logic.md), [Admin Safety
Principles](admin-safety-principles.md), and [API v1 Tenancy
Contract](../contracts/api/v1-tenancy.md): tenant lifecycle is the product
state machine that decides whether a tenant can onboard, trial, operate,
pause, recover, archive, delete, or return to service, while mobile receives
only safe API outcomes for the current user and tenant.

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

## Lifecycle Statement

Tenant lifecycle is Admin/API authority.

The mobile client may show tenant state, guide the user to the next safe
action, hide unavailable features, keep local cache locked or stale-labeled,
queue only allowed offline work, and request fresh context when network returns.
It must not create a tenant, activate a tenant, extend a trial, override billing
blocks, restore an archived tenant, cancel deletion, decide final deletion, or
use cached tenant state as authority.

Product rule: a tenant is usable on mobile only when the API returns a tenant
state, account state, permission set, feature set, subscription outcome, sync
policy, version policy, and support path that allow mobile access.

The lifecycle model should answer:

- Who can create the tenant?
- What onboarding steps are incomplete?
- Is the tenant in trial, active, suspended, archived, billing-blocked, deletion
  requested, deleted, or restore pending state?
- Which roles can see or change the state?
- Which mobile features remain visible?
- Which API actions are blocked, read-only, or support-only?
- Which local cache, drafts, queues, notifications, and diagnostics remain safe?
- Which audit and support records explain the state change?

## Authority Split

Tenant lifecycle spans both systems, but the state decision belongs to Admin/API.

| Area | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Creation | Tenant creation authority, creator role, initial owner/admin assignment, initial plan/trial decision, audit, and support visibility. | No creation authority; may show invitation or tenant-selection outcomes returned by API. |
| Onboarding | Required setup steps, completion criteria, allowed admins, blocking reasons, feature readiness, billing readiness, and support path. | Simple onboarding progress display, missing-step explanations, disabled mobile features, and retry/refresh UX. |
| Trial | Trial eligibility, trial dates, limits, feature access, conversion path, warnings, extension rules, and billing handoff. | Trial label, allowed feature shortcuts, warning messages, contact-admin/billing prompts, and stale/offline handling. |
| Active | Tenant access, feature availability, billing entitlements, permissions, sync policy, reports, notifications, support, and audit. | Normal mobile workflows allowed by API, local cache, drafts, queues, sync display, and mobile settings. |
| Suspended | Suspension reason, who can suspend, who can restore, allowed recovery actions, support visibility, audit, and tenant data protection. | Blocked/suspended state, safe support path, logout or locked-cache behavior, and no trusted offline replay. |
| Archived | Archive reason, read-only or hidden behavior, restore eligibility, export/retention posture, and audit. | Archive notice, disabled feature shortcuts, read-only safe summaries if allowed, and cache cleanup guidance. |
| Billing-blocked | Billing state, entitlement block, grace rules, plan limits, payment recovery, billing contact, and support escalation. | Billing-limited message, allowed read-only context if any, contact-admin/billing support prompts, and blocked writes. |
| Deletion | Deletion request authority, confirmation, waiting period, retention, legal/billing/audit constraints, final deletion, and support process. | Deletion notice, loss-of-access state, local cache hiding/purge guidance, and no server-trusted writes. |
| Restore | Restore authority, eligibility, what returns, what remains restricted, billing/permission revalidation, and audit. | Restore-pending or restored state display after fresh API context and safe local cache reconciliation. |

## Tenant State Model

Tenant states should be explicit, mutually understandable, and safe to explain.

| State | Product meaning | Mobile posture |
| --- | --- | --- |
| Creating | Tenant is being created or provisioned by an authorized admin/platform flow. | Mobile users do not enter normal tenant workflows yet. |
| Onboarding | Tenant exists but required setup, ownership, billing, configuration, users, permissions, or feature readiness is incomplete. | Mobile shows setup-limited state and only API-allowed onboarding or support actions. |
| Trial | Tenant can operate under time, plan, feature, usage, or support limits before conversion. | Mobile shows trial status and only features allowed by trial, role, permission, and API policy. |
| Active | Tenant is in good standing and can use enabled features according to plan and permissions. | Mobile enters normal governed workflows. |
| Suspended | Tenant is blocked by admin/security/compliance/support policy. | Mobile blocks normal work and shows support/admin guidance. |
| Archived | Tenant is no longer operational but may be retained for history, support, reporting, or possible restore. | Mobile hides normal work and may show read-only/archive messaging if allowed. |
| Billing-blocked | Tenant access is limited by payment, plan, quota, invoice, subscription, or entitlement state. | Mobile shows billing-limited state and blocks paid/controlled features. |
| Deletion requested | Tenant deletion has been requested or scheduled but final deletion has not completed. | Mobile warns, blocks risky work, and guides users to admin/support policy. |
| Deleted | Tenant is no longer available as an operational tenant. | Mobile must not show tenant data as usable authority. |
| Restore pending | Tenant restore is being evaluated or processed. | Mobile shows restore-pending state and waits for fresh API context. |

The product may refine these labels later, but the principle should remain:
tenant state controls access before feature flags, remote config, local cache, or
mobile navigation can expose private tenant workflows.

## Creation Principles

Tenant creation is a control-plane action.

Creation principles:

- Tenant creation should be allowed only to platform roles or explicit tenant
  acquisition/onboarding flows.
- Creation should identify the tenant owner or initial tenant admin.
- Creation should establish the intended plan, trial, onboarding path, support
  channel, and initial mobile availability as product decisions.
- Creation should not automatically grant broad support, billing, or mobile
  access beyond the documented role and permission model.
- Creation should be auditable because it introduces a new tenant boundary.
- Creation should not expose the tenant to mobile users until API context says
  the tenant can be selected, joined, or viewed.
- Creation failures should be safe and not leak other tenant names, billing
  records, private invitations, or hidden duplicate information.

Mobile users should see:

- No normal tenant access while creation is incomplete.
- A pending invitation, tenant unavailable, setup in progress, or contact-admin
  state only when the API explicitly returns it.
- No locally invented tenant switcher entry.

## Onboarding Principles

Onboarding is the bridge between creation and usable operation.

Onboarding can include ownership setup, tenant profile completion, admin
assignment, mobile user invitation, role/permission selection, billing or trial
selection, feature flag defaults, remote config defaults, app-version policy,
notification policy, support contact setup, and sync behavior decisions.

Onboarding principles:

- Onboarding requirements should be explicit before implementation.
- Required steps should identify who controls them: platform owner, super admin,
  tenant admin, billing manager, support agent, or mobile user.
- Incomplete onboarding should block or limit mobile features that depend on the
  missing setup.
- Mobile users should not be asked to solve admin-only setup problems.
- Tenant admins should see actionable setup gaps through admin/API surfaces.
- Support should see safe onboarding context without gaining unnecessary tenant
  private data.
- Onboarding completion should be auditable when it changes access, billing,
  feature availability, sync behavior, or mobile visibility.

Mobile users should see:

- A setup-limited state when the tenant is not ready.
- Clear next action such as wait, contact tenant admin, contact support, accept
  invitation, complete profile, update app, or retry context.
- Only mobile screens that are safe before onboarding completes.
- Offline mode should not bypass onboarding requirements.

## Trial State Principles

Trial state lets a tenant experience the product before full commercial
activation.

Trial principles:

- Trial state is Admin/API authority and must not be extended or bypassed by
  mobile.
- Trial access should define which features, limits, support level, reports,
  notifications, sync behavior, exports, and NativePHP capabilities are allowed.
- Trial expiry should be explained before hard block where possible.
- Trial conversion should route to tenant admin, billing manager, platform
  owner, or support according to role.
- Trial restrictions should be API-returned outcomes, not mobile guesses.
- Trial data should remain tenant-isolated and privacy-protected like active
  tenant data.
- Trial changes should be audited when they affect access, billing, features,
  or retention.

Mobile users should see:

- Trial label or warning only when useful and role-appropriate.
- Enabled features that work normally inside trial limits.
- Disabled or limited features with safe explanations.
- Expiry, grace, or conversion messages that point to tenant admin or billing
  roles, not raw billing internals.
- Offline access only within documented trial/offline policy; expired trials
  should revalidate before protected sync.

## Active State Principles

Active state is the normal operating state for a tenant in good standing.

Active principles:

- Active tenants can use enabled features according to tenant plan, role,
  permission, feature flag, remote config, app version policy, and sync policy.
- Active does not mean every feature is enabled.
- Active does not bypass least privilege or tenant isolation.
- Active mobile access still requires valid authentication, current tenant
  context, non-revoked session/device state, supported app version, and allowed
  account state.
- Active tenant decisions should remain auditable for sensitive changes.

Mobile users should see:

- Normal mobile dashboard, tenant context, feature shortcuts, settings, sync
  status, notifications, support entry, and quick actions allowed by API.
- Clear disabled states for features unavailable by plan, flag, permission,
  app version, maintenance, or remote config.
- Offline cache and queued work only where useful and allowed.

## Suspended State Principles

Suspension blocks normal tenant operation for admin, security, compliance,
support, abuse, billing-risk, or operational reasons.

Suspension principles:

- Suspension is an Admin/API decision.
- Suspension should define who can suspend, why, who can restore, what is
  blocked, what remains visible, and what support path exists.
- Suspension should fail closed for mobile writes, sync replay, feature access,
  report access, exports, and normal tenant switching.
- Suspension should protect tenant data rather than deleting it.
- Suspension should be auditable and should show impact before saving when
  initiated by an admin.
- Suspension errors should not leak hidden resources or sensitive policy
  internals.

Mobile users should see:

- A clear suspended or access-limited state.
- No normal workflow shortcuts unless API explicitly allows a safe read-only or
  recovery path.
- Contact tenant admin, contact support, retry later, log out, switch tenant if
  allowed, or update app if version-related.
- Pending offline work should be stopped from replay until API revalidates it.
- Cached private data should be hidden, locked, stale-labeled, or cleared by
  policy.

## Archived State Principles

Archived state means the tenant is no longer operational but may be retained for
history, reporting, support, compliance, export, or possible restore.

Archive principles:

- Archive is separate from deletion.
- Archived tenants should not accept normal mobile writes.
- Archived tenants may allow limited read-only admin/report/support access if
  policy allows.
- Archived tenants should keep audit, privacy, billing, support, and retention
  rules explicit.
- Archive should be auditable and should show impact before saving.
- Archive should define whether restore is possible.

Mobile users should see:

- Tenant archived or unavailable messaging.
- No normal dashboard actions unless API grants safe read-only access.
- No local tenant switching into archived tenant as if it were active.
- Offline queues should not replay into archived tenants.
- Local cache should be hidden, purged, or retained only according to policy.

## Billing-Blocked State Principles

Billing-blocked state limits tenant access because commercial requirements are
not satisfied.

Billing-blocked can come from failed payment, expired trial, unpaid invoice,
quota exhaustion, plan cancellation, subscription pause, missing billing setup,
or entitlement limits.

Billing-blocked principles:

- Billing-blocked state is Admin/API authority.
- Billing blocks should be role-aware: billing managers and tenant admins may
  see recovery actions that mobile users should not see.
- Billing blocks should define which features are fully blocked, read-only,
  grace-limited, support-only, or still available.
- Billing blocks should not expose payment secrets, card details, provider
  internals, or invoice-private data to normal mobile users.
- Billing blocks should be explainable without making mobile own billing logic.
- Billing recovery should be auditable when it changes access or entitlement.

Mobile users should see:

- Billing-limited, plan-limited, trial-ended, quota-reached, or contact-admin
  guidance based on API outcome.
- No raw billing provider errors or sensitive invoice/payment details.
- Feature shortcuts disabled when entitlement blocks them.
- Read-only access only if policy allows.
- Offline writes should not replay until billing state is revalidated.

## Deletion And Requested Deletion Principles

Tenant deletion is dangerous and should be treated separately from archive,
suspension, cache clearing, and user removal.

Deletion principles:

- Requested deletion should be a visible lifecycle state before final deletion
  when policy requires review, waiting period, billing closure, export,
  retention, legal hold, support confirmation, or owner approval.
- Final deletion authority belongs to Admin/API.
- Deletion should explain what happens to users, invitations, mobile sessions,
  device tokens, feature flags, remote config, reports, notifications, support
  cases, billing history, audit history, exports, sync queues, local cache, and
  backups at a principle level before implementation.
- Deletion should require confirmation, impact preview, audit history, and
  tenant-isolated scope.
- Deletion should not remove audit, billing, abuse, security, or legal records
  that must be retained unless a documented privacy/legal process allows or
  requires redaction.
- Deletion errors should not reveal hidden records or cross-tenant information.

Mobile users should see:

- Deletion requested, tenant unavailable, access ending, or deleted state only
  when API returns it.
- No normal tenant work once deletion state blocks operation.
- Clear guidance to contact tenant admin or support.
- Local cache and drafts should be hidden, purged, exported, or left recoverable
  only according to documented policy.
- Offline queued writes must not become server truth for a deleted or
  deletion-requested tenant unless API explicitly accepts a safe recovery path.

## Restore Principles

Restore returns a tenant from suspended, archived, billing-blocked, or
deletion-requested state only when policy allows.

Restore principles:

- Restore authority belongs to Admin/API.
- Restore should revalidate billing, account state, tenant admin ownership,
  permissions, feature flags, remote config, app version policy, support state,
  reports, notifications, sync policy, exports, and privacy obligations.
- Restore does not automatically make every user, device, feature, queue, or
  cached mobile record safe again.
- Restore should define what is restored, what stays disabled, what requires
  re-invitation, what requires re-authentication, and what requires support or
  billing review.
- Restore should be auditable and should explain impact before saving.
- Restore should avoid resurrecting stale or unsafe mobile local state without
  fresh API context.

Mobile users should see:

- Restore pending, access restored, re-login required, refresh tenant context,
  or contact admin/support states based on API outcome.
- Normal tenant navigation only after API returns active or otherwise usable
  context.
- Local cache should be reconciled cautiously; stale drafts and queues should
  revalidate before display or replay.

## Mobile State Matrix

Mobile should present tenant lifecycle as simple, safe states.

| Tenant state | Mobile user sees | Mobile can do | Mobile must not do |
| --- | --- | --- | --- |
| Creating | Tenant setup is not ready or no tenant access yet. | Show invitation/support/pre-login state if API allows. | Invent tenant membership or show tenant cache. |
| Onboarding | Setup in progress or limited access. | Show safe missing-step, wait, retry, contact-admin, or support guidance. | Unlock features that require complete setup. |
| Trial | Trial label, limits, expiry warning, or conversion guidance. | Use API-enabled trial features and show safe warnings. | Extend trial, hide expiry, or replay expired writes without API. |
| Active | Normal tenant context and enabled workflows. | Use allowed features, cache safe data, queue allowed offline work. | Treat local state as authority. |
| Suspended | Suspended/access-limited message. | Contact support/admin, logout, switch tenant if allowed, refresh context. | Submit normal writes, replay queues, or expose stale private cache. |
| Archived | Tenant archived/unavailable message. | Show read-only or support-safe state if API allows. | Enter normal workflows or replay offline work. |
| Billing-blocked | Billing-limited/contact-admin message. | Show allowed read-only or recovery guidance. | Expose payment internals or bypass entitlement blocks. |
| Deletion requested | Tenant access ending or deletion pending message. | Show support/admin guidance and protect local data. | Create new trusted work or assume deletion can be cancelled locally. |
| Deleted | Tenant unavailable. | Clear, hide, or quarantine local tenant state by policy. | Show deleted tenant as selectable or usable. |
| Restore pending | Restore being reviewed or processed. | Wait, refresh, contact support/admin. | Assume restored state before API confirms it. |

## Offline Principles

Offline tenant lifecycle behavior must be conservative.

- Mobile may show last-known tenant state only as last-known context.
- Mobile should label stale tenant state when the API cannot be reached.
- Mobile should block risky writes when tenant state is suspended, archived,
  billing-blocked, deletion-requested, deleted, restore-pending, unknown, or
  stale beyond policy.
- Mobile may keep drafts locally only where policy allows recovery.
- Offline queue replay must re-check tenant lifecycle state before the API
  accepts anything as server truth.
- Offline support diagnostics should avoid private payloads and should describe
  lifecycle state as a safe category.
- Switching tenants while offline should be disabled unless explicitly
  documented as safe.
- When lifecycle state changes while the device is offline, the API decision on
  reconnect overrides cached mobile state.

## Admin Control Principles

Lifecycle controls are dangerous because they affect every user in a tenant.

Admin principles:

- Tenant creation, suspension, archive, billing block, deletion request, final
  deletion, and restore should have clear role ownership.
- Dangerous lifecycle changes should require confirmation.
- Admins should see impact before saving: affected users, mobile access, sync
  queues, reports, billing, notifications, support, exports, and local cache
  implications.
- Admins should preview mobile impact before lifecycle changes reach users.
- Lifecycle changes should be tenant-isolated.
- Lifecycle changes should be auditable.
- Rollback or restore thinking should be documented before irreversible changes.
- Support should receive safe context for lifecycle-related tickets.

## API Principles

The API should make lifecycle outcomes predictable without exposing internal
implementation details.

API principles:

- Bootstrap/context responses should identify the current tenant lifecycle state
  as a mobile-safe outcome where needed.
- API errors should distinguish setup-limited, trial-limited, suspended,
  archived, billing-blocked, deletion-requested, deleted, restore-pending,
  maintenance, update-required, forbidden, and support-required outcomes when
  useful for mobile UX.
- API responses should avoid raw internal status names if those names are not
  safe or stable product language.
- API should protect tenant boundaries before returning lifecycle state.
- API should re-check tenant lifecycle on protected reads, writes, sync replay,
  notification registration, support actions, report access, export access, and
  billing-sensitive actions.
- API should return user-friendly next action, not stack traces, provider
  internals, or private billing/security details.

## Audit And Privacy Principles

Lifecycle changes need audit and privacy because they can expose, block, retain,
or remove tenant data.

- Tenant creation should audit the actor, scope, owner/admin assignment, and
  initial lifecycle intent.
- Onboarding completion should audit state changes that unlock mobile access,
  billing, feature availability, support, or sync.
- Trial start, extension, expiry, conversion, or block should be auditable.
- Active-state sensitive changes should remain auditable through the relevant
  feature docs.
- Suspension, archive, billing block, deletion request, final deletion, and
  restore should be audited.
- Audit records should be redacted and tenant-scoped.
- Lifecycle state should not expose private billing, support, legal, security,
  or deletion details to roles that do not need them.
- Mobile diagnostics should send lifecycle categories, not raw private records
  or internal policy payloads.

## Product Risks

| Risk | Principle response |
| --- | --- |
| Mobile treats cached active state as current authority | Require fresh API context for protected work and replay. |
| Tenant suspension becomes UI-only | Enforce suspension in Admin/API and return mobile-safe blocked states. |
| Billing block exposes payment details to mobile users | Return role-shaped billing-limited outcomes without provider internals. |
| Archive is confused with deletion | Document archive as retained/read-only/unavailable state and deletion as separate dangerous lifecycle. |
| Restore resurrects unsafe local queues | Revalidate tenant, user, permission, billing, feature, version, and sync state before replay. |
| Trial expiry breaks offline users unexpectedly | Define trial/offline grace and revalidation behavior before implementation. |
| Support sees too much lifecycle context | Show safe lifecycle categories first and audit deeper case-scoped access. |
| Tenant deletion removes required audit or billing history | Define retention, legal, billing, audit, and redaction boundaries before code. |
| Admin changes affect all mobile users without warning | Require impact preview, mobile preview, confirmation, and audit. |
| State labels become inconsistent across API/admin/mobile | Document product language and API outcome principles before implementation. |

## Acceptance Questions

Before implementing tenant lifecycle behavior, documentation should answer:

- Who can create a tenant?
- What onboarding steps are required before mobile access?
- What does trial allow, limit, warn, and block?
- What makes a tenant active?
- Who can suspend a tenant, why, and what mobile users see?
- Who can archive a tenant, what remains visible, and whether restore is
  possible?
- What causes billing-blocked state and which roles can recover it?
- Who can request deletion and who can approve final deletion?
- What is retained, hidden, exported, deleted, or redacted during deletion?
- Who can restore a tenant and what must be revalidated?
- What does mobile show in each lifecycle state?
- What happens to local cache, drafts, queues, notifications, diagnostics, and
  app lock in each state?
- What API outcome and mobile next action exists for each state?
- What audit record should exist?
- What support context is safe?
- What privacy boundaries apply?

## Success Standard

Tenant lifecycle logic is successful when admins can safely create, onboard,
trial, activate, suspend, archive, billing-block, delete, and restore tenants;
mobile users always receive clear state and next action; support can explain
the state without overexposure; billing can enforce commercial limits without
leaking payment internals; and stale mobile cache never becomes tenant
authority.
