# Offline UX Logic

Updated: 2026-06-26

This document defines offline user experience logic for the Mobile Lara SaaS
system. It explains offline banner behavior, pending action indicators,
disabled online-only actions, local draft behavior, retry behavior, sync
success feedback, sync failure feedback, how users know what is saved locally
versus synced, and how the app avoids panic when connection is lost. It is
documentation only and does not define database structure, database fields,
migrations, seeders, routes, controllers, Livewire components, Filament
resources, NativePHP plugins, policies, gates, middleware, jobs, services,
local storage schemas, API endpoints, UI components, CSS, JavaScript,
background workers, queues, or application logic.

Use this document with [Product Principles](product-principles.md), [Mobile UX
Principles](mobile-ux-principles.md), [Offline-First Principles](offline-first-principles.md),
[Sync Lifecycle Logic](sync-lifecycle-logic.md), [Conflict Resolution Logic](conflict-resolution-logic.md),
[Mobile App Shell Logic](mobile-app-shell-logic.md), [Mobile Dashboard Logic](mobile-dashboard-logic.md),
[Mobile Settings Logic](mobile-settings-logic.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md),
[NativePHP Local Storage](nativephp-local-storage.md), [Two-System Boundary
Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Role And Permission Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version
Control Logic](mobile-version-control-logic.md), [Authentication Principles](authentication-principles.md),
[Mobile App Lock Principles](mobile-app-lock-principles.md), [Data Privacy
Principles](data-privacy-principles.md), [Audit Logic](audit-logic.md),
[Records/Content Module Logic](records-content-module-logic.md), and [API v1
Sync Contract](../contracts/api/v1-sync.md): offline UX is the human
layer over offline-first, records/content, and sync behavior, and Admin/API remains
authoritative for what can be cached, queued, retried, synced, blocked,
resolved, audited, or trusted.

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

## Offline UX Statement

Offline UX should keep users calm, informed, and productive without pretending
that local work is already server-trusted.

The NativePHP mobile client may show offline banners, preserve drafts, label
local saves, queue allowed actions, explain retry status, and confirm API
accepted sync outcomes. It must not hide offline state, imply server success
before API acceptance, silently discard work, grant online-only actions, or
turn stale cached data into trusted authority.

Product rule: offline UX must always answer three user questions clearly:
"Can I keep working?", "Where is my work saved?", and "What still needs the
server?"

## UX State Vocabulary

Offline behavior should use a small, consistent vocabulary across dashboard,
forms, details screens, settings, and support.

| UX state | Meaning | User expectation |
| --- | --- | --- |
| Online | The app can reach the API and can request current authority. | Normal actions may continue, subject to permissions and feature rules. |
| Offline | The app cannot reach the API or NativePHP/network status says connection is unavailable. | Safe cached views and allowed drafts may continue; online-only actions are disabled or replaced. |
| Saved locally | The user work is preserved on the device but has not been accepted by the API. | The user can leave the screen if the feature policy allows, but the work is not synced. |
| Pending sync | The app intends to send an allowed local action when policy and connection permit. | The user sees the pending item and understands it still needs the server. |
| Syncing | The app is actively submitting or refreshing data. | The user sees progress without duplicate taps or unclear blocking. |
| Synced | The API accepted the work and mobile has applied the accepted outcome. | The user can trust the result as server-confirmed. |
| Failed | The app could not complete sync and the item needs retry, edit, discard, support, or admin action. | The user sees the next action and knows local work is preserved when possible. |
| Conflict | Local intent and server truth no longer align safely. | The user sees whether choice, edit, retry, support, or admin review is required. |
| Blocked | Policy prevents the action: permission, feature flag, app version, maintenance, billing, tenant state, or security. | The user sees why the action cannot continue and what path is available. |

The vocabulary should be stable enough that users learn it once and recognize
it everywhere.

## Offline Banner Behavior

The offline banner is a calm orientation signal, not an alarm.

Banner principles:

- Show an offline banner when the current screen is affected by connection
  loss, stale data, pending work, or online-only actions.
- Keep the banner short and persistent enough to notice, but not so dominant
  that users think the app has failed.
- Prefer screen-level or shell-level placement that does not cover active form
  fields, confirmation controls, native permission prompts, or critical
  conflict messages.
- Include the most useful status: offline, last synced time, pending count,
  retry status, or online-only limitation.
- Do not show raw NativePHP network status text, queue IDs, transport errors,
  stack traces, or API internals in normal UX.
- Distinguish offline from server error, permission denial, billing block,
  maintenance, forced update, feature disabled, conflict, and app lock.
- Remove or downgrade the banner when connection returns and bootstrap/sync
  confirms the app is usable again.
- Keep panic words out of normal offline copy. The app should feel temporarily
  limited, not broken.

The banner should answer what changed and what the user can still do.

## Pending Action Indicators

Pending indicators show that work is preserved but not yet server-confirmed.

Pending-action principles:

- Show pending state near the item or workflow the user just changed.
- Also show aggregate pending counts in the dashboard, sync settings, or app
  shell when pending work affects user confidence.
- Use separate indicators for draft, pending, syncing, synced, failed,
  conflict, and blocked.
- Do not hide pending items after navigation, app resume, tenant switch, or
  connection changes unless policy says they were accepted, discarded,
  quarantined, or no longer available.
- Show tenant context for pending work when multi-tenant users could be
  confused.
- Show whether the user can edit, retry, discard, continue working, or contact
  support.
- Avoid progress theatrics. If no reliable percentage exists, use status
  language instead of fake progress.
- Never mark work as synced until the API accepts it.

Pending indicators should reduce uncertainty, not add another system for the
user to manage.

## Disabled Online-Only Actions

Online-only actions should be visibly unavailable while offline, with a useful
reason and a safe alternative where possible.

Online-only action principles:

- Disable or replace actions that require current API authority: login,
  registration, invitation acceptance, tenant switching, final writes, billing,
  exports, support submission, permission changes, feature/config changes,
  app-version checks, notification registration, and sync replay.
- Do not offer online-only actions as tappable controls that fail after the
  user waits.
- Explain the reason in user language: needs connection, needs refresh, needs
  update, needs admin, needs support, or blocked by policy.
- Offer safe alternatives when available: save draft, keep editing, queue for
  later, view cached copy, retry when online, or open support guidance.
- Keep disabled state separate from permission denial. Offline means "not
  available right now"; denied means "not allowed."
- If a feature is disabled by admin, do not request native permissions or
  collect offline input for it.
- When connection returns, re-check API context before re-enabling sensitive
  controls.

Disabled actions should prevent dead ends. The user should know whether the
action is temporarily offline-only, policy-blocked, or permanently unavailable.

## Local Draft Behavior

Drafts are the safest way to let users keep working without claiming success.

Draft principles:

- Preserve user input locally when the feature allows offline drafting.
- Label drafts as local, unsynced, or not submitted where that distinction
  matters.
- Let users continue editing drafts offline when the data is safe to store and
  app lock/privacy rules protect it.
- Keep drafts tenant-scoped and user-scoped.
- Do not move a draft to another tenant automatically.
- Do not submit a draft after logout, tenant switch, account switch, app
  update, or permission change without API revalidation.
- Avoid silent draft deletion. If a draft must expire or be purged, the policy
  and UX should make that risk clear before it happens.
- Support user-controlled discard when safe, with confirmation for meaningful
  work.
- Preserve attached local media or files only when the feature documents
  privacy, retention, storage, and upload behavior.

Draft UX should say: "your work is here" and "this still needs sync."

## Retry Behavior

Retry should feel predictable and bounded.

Retry principles:

- Retry automatically only for recoverable connection, timeout, server
  availability, or retry-later conditions within admin-controlled limits.
- Do not retry policy denials, invalid payloads, permission loss, tenant
  suspension, billing blocks, forced updates, maintenance blocks, or conflicts
  as if they were network glitches.
- Show when the app will retry automatically, when manual retry is available,
  and when the user must edit, update, discard, wait, or contact support.
- Prevent duplicate taps by making active retries visibly in progress.
- Keep the local item visible through retry attempts.
- Stop retrying when the item is too old, no longer allowed, too risky, or
  requires a reviewed resolution.
- Make retry failure understandable without exposing raw status codes.
- Keep battery, bandwidth, and API load in mind; repeated invisible retry loops
  are poor mobile UX.

Retry is a recovery path. It is not a way to force the server to accept stale
or unauthorized work.

## Sync Success Feedback

Sync success should reassure users without interrupting them.

Success feedback principles:

- Show success only after the API accepts the work and mobile applies the
  accepted outcome.
- Prefer lightweight feedback for routine sync success: status label, check
  marker, updated pending count, recent activity entry, or quiet toast.
- Use stronger feedback only when the user explicitly waited for sync or the
  workflow depends on confirmation.
- Update local state from the server-accepted result, not from optimistic local
  assumptions.
- Clear local draft or pending labels only when the accepted outcome preserves
  the user's work or explains what changed.
- Show last synced time where stale data could matter.
- Avoid repeated success messages for background sync that would distract from
  the current task.

The goal is confidence, not celebration. Users should know the work is safe.

## Sync Failure Feedback

Sync failure should preserve trust by explaining the next step.

Failure feedback principles:

- Keep failed work visible unless policy requires quarantine, deletion,
  support review, or security handling.
- Explain whether the issue is connection, timeout, server unavailable,
  validation, permission, feature disabled, tenant state, billing, app version,
  maintenance, conflict, or support/admin review.
- Provide the next action: retry, edit, keep draft, discard, update app,
  switch tenant, contact support, wait for admin, or sign in again.
- Distinguish temporary failure from permanent rejection.
- Avoid alarming full-screen errors when a small inline state is enough.
- Use stronger interruption only when data loss, security, tenant access,
  forced update, maintenance, or logout is involved.
- Preserve enough safe diagnostic context for support without exposing private
  payloads, secrets, or cross-tenant data.
- Never make failure look like success.

Failure UX should be honest and recoverable. It should not leave users guessing
whether their work disappeared.

## Saved Locally Versus Synced

Users need a simple mental model for where their work lives.

Saved/synced principles:

- "Saved locally" means preserved on this device and not yet trusted by the
  server.
- "Pending sync" means queued for API review when connection and policy allow.
- "Syncing" means the app is actively asking the API to accept or refresh data.
- "Synced" means the API accepted the result.
- "Failed" means the API or connection did not complete the work and the item
  needs recovery.
- "Conflict" means the server needs a safe resolution path before accepting the
  local intent.
- Show saved/synced state near important inputs, draft lists, queued actions,
  dashboard summaries, and settings sync status.
- Avoid ambiguous words such as "done" when work is only local.
- Avoid hiding local-vs-synced state behind icons that have no label in
  critical workflows.

The user should never need to know the internal queue model to understand
whether work is safe.

## Avoiding Panic When Connection Is Lost

Connection loss is normal mobile behavior. The app should treat it that way.

Calm-offline principles:

- Do not blank the screen when connection drops if safe cached data is
  available.
- Do not clear forms just because the API is unreachable.
- Do not navigate users away from their work unless security, app lock,
  forced update, maintenance, or tenant state requires it.
- Show what still works: viewing cached data, editing drafts, capturing local
  input, reviewing pending work, or changing local settings.
- Explain online-only limits before the user taps a dead action.
- Keep app shell, navigation, and sync status stable during short network
  changes.
- Avoid rapid banner flicker; connection changes should feel debounced and
  intentional.
- On reconnect, refresh quietly first, then explain only meaningful changes:
  synced, failed, conflict, blocked, update required, or admin action needed.
- Keep support visible for non-recoverable offline/sync problems.

The app should communicate resilience: connection was lost, work is protected,
and the next server check will happen when possible.

## NativePHP And Livewire UX Notes

The product should use platform-aware behavior without making native/network
details visible to normal users.

NativePHP and Livewire principles:

- NativePHP network status can inform offline state, but raw network values
  should be translated into product states.
- NativePHP secure storage remains for secrets and tokens; offline UX should
  never imply ordinary cache is safe for credentials.
- Livewire offline/loading affordances can support visible offline and in-flight
  states, but UI state remains presentation only.
- Livewire public component state must not be treated as authority for
  permissions, tenant access, feature access, billing, or sync acceptance.
- Online revalidation through the API remains required before sensitive actions
  resume after reconnect.
- Native permission prompts should not appear merely because the app is trying
  to recover from offline state.

The mobile UX can be local and responsive while the product remains API-first.

## Admin And Support Visibility

Admins and support teams need enough context to help without overwhelming
mobile users.

Visibility principles:

- Admins should understand whether offline UX problems are caused by network
  loss, queue age, retry policy, app version, tenant state, feature flag,
  permission, billing, maintenance, conflicts, or support escalation.
- Support should see safe summaries of pending, failed, conflicted, and
  blocked states when policy allows.
- Mobile diagnostics should include safe categories, not raw private payloads.
- Admin settings should define which features can draft, queue, retry, or
  show cached data offline.
- Support scripts should use the same vocabulary as mobile UX so users are not
  told different stories by the app and support team.
- Dangerous or privacy-sensitive recovery should move to admin/support review,
  not mobile-only self-resolution.

Offline UX is part of operations. Good labels on mobile become good support
signals in the admin system.

## Data Loss Prevention

Offline UX must be designed around not losing user work.

Data-loss prevention principles:

- Preserve local input before attempting online submission when the feature
  allows drafts.
- Make destructive discard explicit.
- Warn users before logout, tenant switch, account switch, app reset, storage
  cleanup, or feature disablement could affect unsynced work.
- Keep local work tenant-scoped, user-scoped, and protected by app lock where
  appropriate.
- Avoid optimistic clearing of forms before API acceptance.
- Keep failed and conflicted items recoverable where policy allows.
- Use support/export/recovery paths for meaningful work that cannot be synced
  automatically.
- Treat stale cache and local drafts differently. A cached server record may be
  replaceable; a user's unsynced input may not be.

The safest offline UX is one where the user never has to ask whether the app
kept their work.

## Risk Boundaries

Offline UX should explicitly avoid risky product behavior.

Risks to avoid:

- Panic copy that makes normal mobile network loss feel catastrophic.
- Silent data loss after failed sync, logout, tenant switch, or app restart.
- Fake success before API acceptance.
- Offline queues that hide pending work.
- Disabled actions that look broken instead of intentionally unavailable.
- Retrying permanent policy failures.
- Showing stale permissions, stale features, or stale tenant access as current
  authority.
- Cross-tenant pending indicators or drafts.
- Exposing internal errors, private payloads, or support-only details in normal
  mobile UI.
- Asking for native permissions for disabled, blocked, or offline-unavailable
  features.

These risks should be reviewed before any offline-capable mobile screen is
implemented.

## Acceptance Questions

Every offline-capable workflow should answer these questions before coding:

- What does the user see immediately when connection is lost?
- What remains usable offline?
- Which actions become disabled or replaced?
- What is saved locally?
- What is queued for sync?
- What requires online API authority?
- How does the user see pending, syncing, synced, failed, conflict, and blocked
  states?
- What happens if the user leaves the screen?
- What happens if the app closes and reopens?
- What happens if the user switches tenant or logs out?
- What happens when connection returns?
- What support/admin context is available if recovery fails?
- What prevents local work from being mistaken for synced work?

If a workflow cannot answer these questions, it is not ready to become an
offline-capable mobile feature.

## Success Standard

Offline UX succeeds when a normal mobile user loses connection and still
understands what is available, what is saved locally, what is pending, what is
synced, what failed, and what to do next.

The product standard is calm resilience: no fake success, no silent loss, no
unexplained disabled actions, no hidden queues, no cross-tenant leakage, and no
panic when the network changes.
