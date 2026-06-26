# Conflict Resolution Logic

Updated: 2026-06-26

This document defines conflict resolution logic for the Mobile Lara SaaS system.
It explains why conflicts happen, which conflicts can be auto-resolved, which
conflicts need user choice, which conflicts need admin or support review, how
mobile should show conflicts, how admins should monitor conflicts, how conflict
decisions should be audited, and how users should avoid data loss. It is
documentation only and does not define database structure, database fields,
migrations, seeders, routes, controllers, Livewire components, Filament
resources, NativePHP plugins, policies, gates, middleware, jobs, services,
local storage schemas, API endpoints, conflict tables, sync workers, retry
jobs, queue tables, or application logic.

Use this document with [Product Principles](product-principles.md), [Two-System
Boundary Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Offline-First Principles](offline-first-principles.md), [Sync Lifecycle
Logic](sync-lifecycle-logic.md), [Offline UX Logic](offline-ux-logic.md),
[Mobile Client Responsibilities](mobile-client-responsibilities.md),
[NativePHP Local Storage](nativephp-local-storage.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Authentication Principles](authentication-principles.md),
[Mobile App Lock Principles](mobile-app-lock-principles.md), [Mobile App Shell
Logic](mobile-app-shell-logic.md), [Mobile Dashboard Logic](mobile-dashboard-logic.md),
[Mobile Settings Logic](mobile-settings-logic.md), [Role And Permission
Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version
Control Logic](mobile-version-control-logic.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Admin Safety Principles](admin-safety-principles.md),
[Audit Logic](audit-logic.md), [Data Privacy Principles](data-privacy-principles.md),
and [API v1 Sync Contract](../contracts/api/v1-sync.md): conflicts are where
mobile local intent meets current server truth, and Admin/API remains
authoritative for conflict detection, conflict classification, resolution
eligibility, audit, tenant boundaries, and canonical state.

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

## Conflict Resolution Statement

Conflict resolution protects user work without weakening server authority.

The mobile client may preserve local work, explain conflict state, ask for user
choice, and submit a resolution intent. It must not silently overwrite server
truth, grant permissions, bypass billing or feature policy, move work between
tenants, or decide restricted conflicts locally. The Admin/API system remains
responsible for deciding whether a conflict can be auto-resolved, needs user
choice, needs admin/support review, or must be rejected.

Product rule: a conflict is resolved only when the API accepts the resolution
under the current user, tenant, device, feature, permission, subscription,
app-version, maintenance, and audit context.

## Why Conflicts Happen

Conflicts happen when locally captured work and current server reality no
longer line up safely.

Common conflict causes include:

- The server record changed after mobile cached it.
- The user edited stale data while offline.
- Another user changed, approved, deleted, archived, restored, or reassigned
  the same resource.
- The user's role, permission, tenant membership, or session state changed.
- The tenant became suspended, archived, billing-blocked, maintenance-blocked,
  or deletion-requested.
- A feature flag, remote config rule, app-version rule, plan gate, device gate,
  cohort gate, or emergency policy changed before replay.
- A queued action is too old, too large, duplicated, out of order, or unsafe.
- A local draft depends on data that the server no longer exposes to that user.
- A media/file upload, scan, location, or native-captured payload can no longer
  be associated with the intended server record.
- The server cannot prove the local intent is idempotent or belongs to the
  current tenant/user context.

Conflicts are not only data-edit problems. They can also be authority, privacy,
billing, lifecycle, feature, sync, or support problems.

## Authority Split

Conflict handling spans both systems, but the trust boundary remains
server-side.

| Area | Mobile client may own | Admin/API owns |
| --- | --- | --- |
| Local preservation | Keep drafts, pending items, local notes, attachments, and user-visible conflict context. | Whether preserved work can be submitted, merged, rejected, or escalated. |
| Conflict display | Explain safe conflict summaries, affected workflow, next actions, and data-loss risks. | Conflict classification, canonical state, restricted details, privacy-safe messages, and resolution eligibility. |
| User choice | Collect a user's chosen resolution for allowed conflicts. | Validate the choice, apply permissions, accept or reject the resolution, and audit the result. |
| Auto-resolution | Apply only server-approved outcomes to local presentation. | Decide when auto-resolution is safe and produce accepted canonical state. |
| Admin/support review | Surface support paths, diagnostics, and tenant-safe summaries. | Review restricted conflicts, resolve policy conflicts, preserve audit history, and protect private payloads. |
| Monitoring | Show local conflict counts and workflow impact. | Tenant-wide health, conflict patterns, dangerous spikes, policy impact, and operational reporting. |

Mobile can make conflict resolution humane. Admin/API makes it trustworthy.

## Auto-Resolvable Conflicts

Auto-resolution is safe only when it cannot cause data loss, privilege changes,
cross-tenant leakage, billing changes, or silent destructive behavior.

Conflicts may be auto-resolved when:

- The same idempotent local intent was already accepted by the API.
- The queued action is a harmless duplicate and the server can prove the
  accepted outcome.
- A pull response includes a newer server value for read-only cached data and
  no local draft depends on the old value.
- A local pending item became unnecessary because the server already reached
  the same result.
- A non-destructive metadata refresh can safely update labels, timestamps,
  counters, read states, or presentation state.
- The server can transform the local intent without changing user meaning and
  without hiding the transformation.
- The conflict is caused by a temporary retry-later state that later succeeds
  without changing user intent.

Auto-resolution principles:

- The server decides auto-resolution eligibility.
- Mobile should tell users when a pending conflict was resolved automatically
  if it affected visible work.
- Auto-resolution should never discard meaningful local input without a
  recoverable copy or clear accepted server outcome.
- Auto-resolution should update conflict counts, pending counts, activity, and
  support context.
- Auto-resolution should still be auditable when it affects business records,
  permissions, tenant state, billing, support, reporting, or compliance.

If the system cannot explain why auto-resolution is safe, it should not
auto-resolve.

## Conflicts That Need User Choice

User choice is appropriate when the user owns the meaning of the work and has
permission to choose between safe outcomes.

Conflicts may need user choice when:

- The user edited a field that changed on the server.
- The user can choose to keep their draft, edit it, submit a new version, or
  discard it.
- The local note, comment, checklist item, response, or attachment can be
  preserved without overriding another user's work.
- The server can show enough safe comparison context for the user to decide.
- A non-destructive merge is possible but requires the user to confirm intent.
- The user can reselect a valid target because the original target changed,
  was deleted, or is no longer available.
- The user can retry after updating the app, refreshing permissions, switching
  tenant, or reconnecting.

User-choice principles:

- Preserve the local version until the user chooses a safe outcome.
- Show what happened in plain language.
- Make the available choices explicit: keep draft, edit, retry, submit as new,
  use server version, attach to another allowed record, discard, or contact
  support.
- Avoid technical comparison screens unless the domain needs them.
- Never ask users to choose from data they are no longer allowed to see.
- Never make discard the only visible option when preserving work is possible.
- Do not let a user choice bypass server validation, permissions, feature
  flags, tenant state, billing, maintenance, app-version policy, or audit.

User choice should feel like recovering work, not debugging sync internals.

## Conflicts That Need Admin Or Support Review

Some conflicts are not safe for normal mobile users to resolve.

Conflicts need admin/support review when:

- Resolving the conflict could expose another user's private data.
- Resolving the conflict could affect billing, subscription, tenant lifecycle,
  permissions, roles, feature access, support state, reports, or audit history.
- The user's access changed and the app cannot safely show enough context.
- A tenant is suspended, archived, billing-blocked, in maintenance, or under
  deletion review.
- The conflict involves possible replay abuse, duplicate submission, device
  trust, account takeover, suspicious session behavior, or security policy.
- Multiple users or roles made competing updates that require business
  judgement.
- A queued action affects a regulated or compliance-sensitive workflow.
- A file, media item, scan, location, or diagnostic payload needs privacy,
  malware, retention, or support review before acceptance.
- The conflict appears to be caused by an admin policy rollout, feature flag,
  remote config, app-version rule, or sync-policy change.

Admin/support review principles:

- Show mobile users a safe explanation and expected next action.
- Show admins/support enough context to understand impact without exposing
  unnecessary payloads.
- Keep tenant isolation strict.
- Separate support diagnosis from authority to change business outcomes.
- Require elevated review for dangerous, destructive, cross-tenant,
  billing-impacting, or security-sensitive resolution.
- Preserve local user work until a reviewed outcome is accepted, rejected,
  exported, retained, or deleted under policy.

Support should help users recover work. Admins should control business
decisions. Neither role should receive private data by default unless the
policy allows it.

## Mobile Conflict UX

Mobile conflict UX should be clear, calm, and recoverable.

The mobile client should show:

- Which workflow or item needs attention.
- Whether the conflict is waiting for user choice, admin review, support
  review, app update, retry, or server policy.
- Whether local work is preserved.
- Whether the user can keep editing, retry, discard, submit as new, use server
  version, contact support, or wait.
- Whether the app is offline, syncing, stale, blocked, or locked.
- Whether the conflict belongs to the current tenant and current user context.
- Whether leaving the screen, switching tenant, logging out, or updating the
  app affects the pending work.

Mobile conflict UX should avoid:

- Raw internal IDs, queue names, stack traces, or exception messages.
- Blaming language.
- Silent discard.
- Hidden retries with no status.
- Showing another tenant's data.
- Showing private admin/support context to a normal mobile user.
- Presenting server-accepted work as still conflicted.
- Presenting unresolved local work as synced.

The user should always know: what is safe, what is pending, what needs a choice,
and how to avoid losing work.

## Admin Conflict Monitoring

Admin monitoring turns conflicts into an operational signal.

Admins should be able to understand:

- Which tenants have healthy, elevated, severe, or blocked conflict rates.
- Which features, roles, app versions, device types, or cohorts create the
  most conflicts.
- Whether conflicts are data conflicts, permission conflicts, billing
  conflicts, tenant lifecycle conflicts, version conflicts, feature conflicts,
  security conflicts, or support-review conflicts.
- Whether a rollout, feature flag, remote config, app-version rule, maintenance
  window, or sync-policy change caused new conflicts.
- Which conflicts are auto-resolved, waiting for user choice, waiting for
  support, waiting for admin review, rejected, discarded, or resolved.
- Which conflicts are aging too long.
- Whether support needs safer context, more escalation tools, or privacy
  limits.

Admin monitoring principles:

- Show aggregate conflict health before item-level detail.
- Scope all conflict views by tenant and role.
- Redact private payloads by default.
- Make dangerous policy changes show conflict impact before saving.
- Highlight spikes, old unresolved conflicts, repeated retry failures, and
  support-heavy workflows.
- Connect conflict health to feature flags, remote config, app versions,
  offline limits, and sync lifecycle health.

Conflict monitoring should help admins improve product policy, not turn mobile
users into manual sync operators.

## Audit Principles

Conflict decisions need audit history because they can change business truth,
support outcomes, user trust, and compliance posture.

Audit should answer:

- What local intent caused the conflict?
- Which tenant, user, role, device context, app version, feature, and policy
  context applied?
- What server state or policy caused the conflict?
- Was the conflict auto-resolved, user-resolved, support-reviewed,
  admin-reviewed, rejected, discarded, or left pending?
- Who made the decision, or which server policy made it?
- What changed after resolution?
- What local work was preserved, transformed, accepted, rejected, discarded, or
  escalated?
- Did the decision affect permissions, billing, tenant lifecycle, reports,
  support, privacy, or audit-sensitive data?

Audit principles:

- Audit conflict decisions, not every private payload detail.
- Keep audit records tenant-safe and role-safe.
- Do not store secrets, raw private diagnostics, or unnecessary local payloads
  in audit history.
- Preserve enough before/after meaning for support and compliance.
- Make automated conflict decisions distinguishable from user/admin/support
  decisions.
- Make discarded work auditable when discarding changes business, compliance,
  support, or user trust outcomes.

Audit is part of user protection. It should explain what happened without
creating a new privacy problem.

## Avoiding Data Loss

Conflict resolution should be designed around preserving user work first.

Data-loss prevention principles:

- Preserve local drafts until a safe accepted, rejected, discarded, or exported
  outcome exists.
- Do not silently overwrite local work with pulled server data.
- Do not silently overwrite server work with local data.
- Offer edit, retry, submit as new, keep draft, or contact support when those
  options are safe.
- Warn before discard.
- Explain whether logout, tenant switch, app update, app lock, storage cleanup,
  or uninstall risk affects local pending work.
- Protect local conflict data with app lock and tenant/user scoping.
- Keep attachments, media, scans, and files recoverable where policy allows.
- Stop replay when server revocation, user suspension, tenant suspension,
  billing block, forced update, or security policy makes replay unsafe.
- Provide support-safe diagnostics when users cannot resolve data loss risk
  themselves.

Users should never need to guess whether their work still exists.

## Resolution Outcomes

Conflict resolution should result in one clear outcome.

Allowed outcomes include:

- Accepted: the API accepted the local intent or accepted a user/admin/support
  resolution.
- Auto-resolved: the API resolved the conflict safely without user choice.
- User action required: the mobile user must choose a safe next step.
- Admin review required: an admin must decide or change policy.
- Support review required: support must help diagnose or recover.
- Retry later: the conflict is not final and may be retried under policy.
- Rejected: the API refuses the action under current rules.
- Discarded: the local work was intentionally discarded after confirmation or
  policy.
- Preserved as draft: the local work remains available but is not submitted.
- Blocked: version, maintenance, tenant, billing, permission, feature, or
  security state prevents resolution.

Each outcome should have a mobile message, admin/support meaning, audit meaning,
and data-retention expectation.

## Failure Modes

Conflict planning should name failure modes before implementation.

Failure modes include:

- Mobile shows unresolved local work as synced.
- Mobile silently discards a draft.
- Pull overwrites a local edit without warning.
- Push overwrites newer server data without conflict detection.
- The user cannot understand the next action.
- The user is asked to choose using data they are no longer allowed to see.
- Support sees more private payload than needed.
- Admins cannot see that a rollout created conflicts.
- Auto-resolution hides destructive behavior.
- Audit cannot explain who or what resolved the conflict.
- Tenant switching mixes conflict context.
- Logout or account switch leaves replayable work in the wrong context.
- A conflict stays unresolved until cached data becomes misleading.

Each failure mode should have a mobile state, API outcome, admin/support
meaning, privacy posture, and recovery path before implementation.

## Acceptance Questions

Before implementing conflict resolution behavior, documentation should answer:

- Why can this conflict happen?
- Can this conflict be auto-resolved safely?
- What data-loss risk exists?
- What can the user choose?
- What must go to admin or support review?
- What should mobile show without leaking private data?
- What should admins monitor?
- What should support be allowed to see?
- What is audited?
- What happens if the user logs out, switches tenant, loses access, updates the
  app, goes offline again, or discards local work?
- What does the API accept, reject, block, preserve, or escalate?
- How does the product prevent silent data loss?

## Success Standard

Conflict resolution is successful when user work is preserved, server authority
is protected, safe conflicts are auto-resolved, meaningful conflicts offer clear
user choice, restricted conflicts reach admin/support review, mobile explains
the state without leaking data, admins can monitor conflict health, conflict
decisions are auditable, and users do not lose work silently.
