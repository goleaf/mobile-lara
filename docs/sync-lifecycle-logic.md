# Sync Lifecycle Logic

Updated: 2026-06-26

This document defines sync lifecycle logic for the Mobile Lara SaaS system. It
explains bootstrap sync, pull changes, push local changes, retry failed
changes, conflict detection, conflict resolution, acknowledgement, sync status
communication, manual sync, background sync principles, and admin monitoring of
sync health. It is documentation only and does not define database structure,
database fields, migrations, seeders, routes, controllers, Livewire
components, Filament resources, NativePHP plugins, policies, gates,
middleware, jobs, services, local storage schemas, API endpoints, sync workers,
retry jobs, queue tables, or application logic.

Use this document with [Product Principles](product-principles.md), [Two-System
Boundary Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Offline-First Principles](offline-first-principles.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [NativePHP Local
Storage](nativephp-local-storage.md), [Multi-Tenant Mobile Logic](multi-tenant-mobile-logic.md),
[Authentication Principles](authentication-principles.md), [Mobile App Lock
Principles](mobile-app-lock-principles.md), [Mobile App Shell Logic](mobile-app-shell-logic.md),
[Mobile Dashboard Logic](mobile-dashboard-logic.md), [Mobile Settings Logic](mobile-settings-logic.md),
[Role And Permission Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version
Control Logic](mobile-version-control-logic.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Admin Safety Principles](admin-safety-principles.md),
[Audit Logic](audit-logic.md), [Data Privacy Principles](data-privacy-principles.md),
and [API v1 Sync Contract](../contracts/api/v1-sync.md): sync exists so mobile
can reconcile useful local work with server authority while Admin/API remains
authoritative for access, validation, permissions, feature availability,
tenant boundaries, billing, conflict decisions, audit, and canonical state.

## Sync Lifecycle Statement

Sync is the lifecycle that turns local mobile work into server-reviewed
outcomes.

The NativePHP mobile client may cache, draft, queue, retry, and communicate
sync state. It must not declare server acceptance, resolve authoritative
conflicts, bypass tenant or permission checks, override admin limits, or hide
failed pending work. The Admin/API system remains responsible for deciding what
changed, what can be accepted, what must be rejected, what conflicts exist, and
what the mobile client should do next.

Product rule: a local change is only synced when the API confirms the outcome
in the current user, tenant, device, feature, permission, subscription,
app-version, maintenance, and sync-policy context.

## Lifecycle Overview

The sync lifecycle should be understandable to users, support teams, and
admins without exposing internal mechanics.

| Lifecycle moment | Mobile client owns | Admin/API owns |
| --- | --- | --- |
| Bootstrap sync | First sync state presentation, local readiness, last-known context, and safe startup messages. | Current sync policy, allowed features, cursors or equivalent progress context, tenant/user eligibility, and force-block decisions. |
| Pull changes | Applying server-confirmed changes to local presentation and marking stale data. | Canonical changed data, visibility rules, deletion/revocation meaning, and what the client may receive. |
| Push local changes | Sending queued local intents for server review and showing pending/syncing state. | Validation, authorization, tenant checks, idempotent acceptance, rejection, and audit. |
| Retry failed changes | User-visible retry options and safe retry timing. | Retry eligibility, backoff policy, replay limits, permanent failure reasons, and abuse protection. |
| Conflicts | Clear conflict display and user-safe next actions. | Conflict detection, conflict reason, resolution policy, accepted canonical state, and admin/support visibility. |
| Acknowledgement | Marking delivered server changes as seen locally only after safe receipt. | Whether acknowledgement is needed, what it means, and whether undelivered changes must be resent. |
| Status communication | Offline, pending, syncing, synced, stale, failed, blocked, and conflict labels. | Mobile-safe status semantics, support meanings, admin monitoring, and audit visibility. |

## Bootstrap Sync

Bootstrap sync prepares the mobile client to work safely.

Bootstrap sync should:

- Confirm the current account, tenant, app version, maintenance state, feature
  availability, permissions, remote config, and offline/sync policy before
  protected work begins when online.
- Let mobile show safe last-known context when already authenticated and
  temporarily offline.
- Tell mobile whether local cache can be shown, whether queued work can replay,
  whether new offline work is allowed, and whether sync must be blocked.
- Refresh sync status, stale-data warnings, pending counts, conflict counts,
  and retry guidance.
- Protect the user from old policy assumptions by treating stale bootstrap
  context as presentation only.
- Fail closed for protected work when user access, tenant access, app-version
  support, subscription access, feature access, maintenance state, or sync
  policy cannot be confirmed.

Bootstrap sync should feel like the app becoming ready, not like a technical
download step. The user should know whether the app is ready, offline-ready,
limited, syncing, blocked, or waiting for admin/server action.

## Pull Changes

Pulling changes updates the mobile client with server-confirmed truth.

Pull principles:

- The mobile client pulls only what the current user and tenant may see.
- Pulled changes should preserve tenant isolation and should never mix data
  between tenants.
- Pulled changes may update cached records, dashboard summaries, notifications,
  announcements, support state, feature visibility, config visibility, and
  sync status.
- Deletions, revocations, suspensions, feature disablements, and billing blocks
  should be presented as server decisions, not as missing local data.
- Pull should not overwrite unsynced local drafts without clear user handling.
- Pull should not make failed or conflicted local work disappear without a
  visible outcome.
- Pull should refresh stale markers so users know whether they are looking at
  current, cached, or outdated information.

The mobile client should treat pull as receiving server-confirmed context. It
should not infer new permissions, new features, or new tenant authority from
old cached data.

## Push Local Changes

Pushing local changes submits queued mobile work for API review.

Push principles:

- Mobile pushes local changes as requests for acceptance, not as final writes.
- The API re-checks user identity, tenant access, permissions, feature flags,
  subscription state, app-version policy, maintenance policy, payload safety,
  and conflict policy before accepting anything.
- Mobile should keep local changes visible as pending until the API returns an
  accepted, rejected, conflicted, failed, or retry-later outcome.
- Mobile should push only work that was allowed to be queued by the current
  offline and sync policy.
- Local changes should not replay under a different user, tenant, account,
  device trust state, or app policy context.
- Sensitive or oversized local material should not be pushed silently; users
  need clear upload/sync status where the workflow depends on it.
- Server acceptance should update local presentation calmly and consistently.

Push should protect both sides: mobile users do not lose work, and the server
does not accept stale, unauthorized, unsafe, or cross-tenant changes.

## Retry Failed Changes

Retry is for recoverable sync problems, not for overriding server decisions.

Retry principles:

- Temporary network, timeout, server availability, and retry-later outcomes may
  be retried within admin-controlled limits.
- Permanent rejection, permission loss, tenant suspension, feature disablement,
  billing block, forced update, maintenance block, invalid payload, or conflict
  may require user action, admin action, support action, app update, edit,
  discard, or resolution instead of automatic retry.
- Retry timing should avoid draining battery, flooding the API, or confusing
  users with repeated failures.
- Retry should keep the original pending item visible until it is accepted,
  rejected, conflicted, discarded, or escalated.
- Retry should preserve business ordering only when order matters.
- Retry should stop when policy says the action is too old, too large, too
  risky, too frequent, or no longer allowed.

Users should understand whether the app will retry automatically, whether they
can retry manually, or whether the item needs edit/support/admin action.

## Conflict Detection

Conflicts happen when local intent and server reality cannot be safely merged.

Conflict detection should consider:

- The server record changed after the mobile cache was created.
- The user lost permission while offline.
- The tenant changed lifecycle state while offline.
- The feature was disabled, plan-limited, device-limited, or app-version-limited
  before replay.
- Billing or subscription state now blocks the action.
- The queued change would overwrite newer server data.
- The queued change refers to deleted, archived, restored, merged, or
  otherwise changed server context.
- The payload is no longer valid under current rules.
- The same local intent appears to have been replayed already.

Conflict detection belongs to Admin/API because only the server can compare
current authority, current data, current policy, current tenant state, and
accepted history. Mobile presents the conflict and preserves enough local
context for recovery without becoming the decision-maker.

## Conflict Resolution

Conflict resolution should be explicit, calm, and role-aware.

Resolution principles:

- The API should explain conflicts in mobile-safe language.
- The mobile client should show whether the user can edit, retry, discard,
  keep a draft, choose a server version, submit a new version, or contact
  support.
- Some conflicts should be user-resolvable; others require tenant admin,
  support, billing, platform admin, or security review.
- Mobile should never silently choose a destructive resolution.
- Mobile should not expose another tenant's data, private admin notes, or raw
  server internals while explaining a conflict.
- Resolution should update pending counts, dashboard status, settings status,
  support context, and audit meaning.
- Conflict resolution should leave a clear history of what happened and who or
  what resolved it.

The best resolution flow is usually the simplest one that preserves user work,
protects server truth, and explains the next action.

## Acknowledgement

Acknowledgement means the mobile client confirms it received or applied a
server-delivered sync outcome.

Acknowledgement principles:

- Acknowledgement should not mean the mobile client owns the truth.
- Acknowledgement can help the server know which changes, conflicts, notices,
  or policy updates are safe to consider delivered.
- Mobile should acknowledge only after the user/tenant context matches and the
  delivered outcome has been safely handled locally.
- Failed local application of a server change should not be acknowledged as
  successfully handled.
- Acknowledgement should be tenant-aware and user-aware.
- Acknowledgement should support recovery when a device crashes, loses network,
  switches tenants, logs out, or resumes later.

The user usually should not need to think about acknowledgement. It should
support reliability and support visibility behind a clear sync status.

## Sync Status Communication

Sync status is product UX, not background trivia.

The mobile client should communicate:

- Offline.
- Online but not yet refreshed.
- Last synced time.
- Sync in progress.
- All changes synced.
- Pending changes.
- Waiting to retry.
- Manual retry available.
- Conflict needs attention.
- Failed sync needs action.
- Sync blocked by permissions, tenant state, billing, feature flag, maintenance,
  app version, admin policy, or security policy.
- Data is cached or stale.
- Support can help when recovery is not user-resolvable.

Status copy should be short and understandable. It should not show raw internal
codes, queue mechanics, stack traces, or implementation terms unless the user
is in a diagnostics/support context designed for that purpose.

## Manual Sync

Manual sync gives users confidence and control.

Manual sync principles:

- Users should be able to request sync from appropriate places such as the
  dashboard, sync settings, or the workflow with pending work.
- Manual sync should explain when it cannot run: offline, locked, blocked by
  app version, blocked by maintenance, blocked by tenant status, blocked by
  permissions, or blocked by admin policy.
- Manual sync should not bypass retry limits, conflict rules, tenant checks,
  feature flags, billing rules, or security policy.
- Manual sync should show immediate feedback: checking, pulling, pushing,
  waiting, synced, conflict, failed, or blocked.
- Manual sync should be safe to run repeatedly.
- Manual sync should not hide automatic sync; it complements automatic recovery
  for users who want reassurance.

Manual sync is especially useful after the user returns online, switches
tenant, updates the app, resolves a conflict, changes permissions, or receives
support guidance.

## Background Sync Principles

Background sync should be useful, conservative, and admin-controlled.

Background sync principles:

- It should run only when policy allows it for the tenant, user, feature, app
  version, device state, and network context.
- It should respect battery, metered network, operating-system limits, app lock,
  secure session state, and native permission state.
- It should prefer small, safe, resumable work over long hidden operations.
- It should not surprise users with large uploads, sensitive data movement, or
  irreversible outcomes.
- It should stop or downgrade when maintenance, forced update, logout,
  revocation, suspension, billing block, or security policy requires it.
- It should preserve clear user-visible state for pending, synced, failed, and
  conflicted work.
- It should not assume NativePHP/native platform availability guarantees every
  background task will run on schedule.

Background sync is a resilience aid, not a promise that all work completes
without user awareness.

## Admin Monitoring Of Sync Health

Admins and support teams need visibility into sync health without seeing more
private data than necessary.

Admin monitoring should help answer:

- Which tenants have healthy, degraded, blocked, or failing sync.
- Which features produce the most pending, failed, retried, or conflicted work.
- Which app versions or device categories are producing sync issues.
- Whether failures are caused by policy, permissions, billing, maintenance,
  stale versions, network conditions, validation, payload size, or conflicts.
- Whether support should help a user, tenant admin should adjust configuration,
  billing should resolve entitlement, or platform admins should change rollout
  policy.
- Whether admin policy changes created new sync failures.
- Whether offline limits are too broad, too strict, or unsafe.

Admin monitoring principles:

- Show aggregate health before exposing item-level detail.
- Keep tenant isolation strict.
- Redact sensitive payloads by default.
- Distinguish user-caused, policy-caused, network-caused, app-version-caused,
  and system-caused sync problems.
- Preserve audit meaning for accepted, rejected, conflicted, retried,
  discarded, and admin-resolved sync outcomes.
- Support rollback thinking when a feature flag, remote config, app-version
  rule, or offline policy change harms sync health.

Monitoring should turn sync from a hidden mobile problem into an operationally
visible SaaS control-plane concern.

## Failure Modes

Sync lifecycle planning should name failure modes before implementation.

Failure modes include:

- Bootstrap cannot confirm current user, tenant, version, or policy.
- Pull receives changes the mobile client cannot safely apply.
- Push sends work that is no longer authorized.
- Retry loops too aggressively.
- Conflict is detected but not understandable to the user.
- Conflict resolution would expose private or cross-tenant data.
- Acknowledgement says work was handled when it was not.
- Background sync fails silently.
- Manual sync appears to succeed while items remain pending.
- Tenant switch happens while old-tenant work is pending.
- Logout, revocation, or account switch leaves replayable work in an unsafe
  state.
- Admin disables sync without understanding pending work impact.
- Support sees too much private payload while diagnosing sync health.

Each failure mode should have a mobile state, API outcome, admin/support
meaning, privacy posture, and recovery path before implementation.

## Acceptance Questions

Before implementing sync behavior, documentation should answer:

- What starts bootstrap sync and what does ready mean?
- What can be pulled for the current tenant and user?
- What can be pushed and what must wait online?
- Which local changes are retried automatically?
- Which failures require user, support, admin, billing, security, or app-update
  action?
- How are conflicts detected and explained?
- Who can resolve each conflict type?
- What does acknowledgement mean and what does it never mean?
- How does the app show offline, pending, syncing, synced, stale, retrying,
  failed, blocked, and conflict states?
- When can users run manual sync?
- When can background sync run, pause, or stop?
- What does admin monitor, and what is redacted?
- What is audited when sync accepts, rejects, conflicts, retries,
  acknowledges, discards, or resolves work?

## Success Standard

Sync lifecycle behavior is successful when mobile users can understand the
state of their work, local changes become trusted only after API confirmation,
pull and push preserve tenant isolation, retry behavior is bounded, conflicts
are visible and recoverable, acknowledgement improves reliability without
moving authority to mobile, manual sync gives users confidence, background sync
is conservative, and admins can monitor sync health without violating privacy.
