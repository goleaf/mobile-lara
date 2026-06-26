# Records/Content Module Logic

Updated: 2026-06-26

This document defines records/content module logic for the Mobile Lara SaaS
system. It explains what a record represents, how users create, view, edit,
archive, restore, and delete records, how notes, attachments, activity, tags,
categories, and status behave logically, what works offline, what requires
sync, how admins can view and control tenant records, and how permissions and
feature flags affect the module. It is documentation only and does not define
database structure, database fields, migrations, seeders, routes, controllers,
Livewire components, Filament resources, NativePHP plugins, policies, gates,
middleware, jobs, services, local storage schemas, API endpoints, UI
components, CSS, JavaScript, sync workers, queues, or application logic.

Use this document with [Product Principles](product-principles.md), [Two-System
Boundary Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Role And Permission
Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Audit Logic](audit-logic.md), [Data Privacy Principles](data-privacy-principles.md),
[Tenant Lifecycle Logic](tenant-lifecycle-logic.md), [Tenant Admin Logic](tenant-admin-logic.md),
[Multi-Tenant Mobile Logic](multi-tenant-mobile-logic.md), [Offline-First
Principles](offline-first-principles.md), [Offline UX Logic](offline-ux-logic.md),
[Sync Lifecycle Logic](sync-lifecycle-logic.md), [Conflict Resolution Logic](conflict-resolution-logic.md),
[Mobile Dashboard Logic](mobile-dashboard-logic.md), [Mobile Settings Logic](mobile-settings-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version
Control Logic](mobile-version-control-logic.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Admin Safety Principles](admin-safety-principles.md),
[Search Logic](search-logic.md), [Forms And Drafts Logic](forms-drafts-logic.md),
and [API v1 Records Contract](../contracts/api/v1-records.md): records are
tenant-scoped business content, and Admin/API remains authoritative for access,
validation, lifecycle state, sync acceptance, conflict decisions, audit,
retention, reporting, search, forms, drafts, and tenant boundaries.

Search Logic is defined in `search-logic.md`:
record discovery must stay tenant-scoped, permission-aware, feature-controlled,
privacy-preserving, and explicit about local-cache limits versus
API-authoritative results, including recent searches, saved filters, filtering,
sorting, scan-to-search, offline limits, and admin-controlled boundaries.

Forms And Drafts Logic is defined in `forms-drafts-logic.md`:
record create, edit, note, attachment, status, archive, restore, and delete
forms must stay simple, validated, autosave-aware, offline-draft safe,
API-submitted, admin-controlled, and explicit about local-save versus
server-accepted state so user work is protected without bypassing authority.

## Module Statement

The records/content module is the shared tenant workspace for structured mobile
work.

A record represents a tenant-scoped business item that mobile users can create,
review, update, annotate, attach evidence to, categorize, tag, and progress
through a controlled lifecycle. A record is not only a row of content. It is a
container for work state, user intent, supporting context, offline drafts,
activity history, and admin-visible accountability.

Product rule: mobile can help users work with records locally, but a record is
trusted only when the API confirms it under current tenant, user, permission,
feature flag, subscription, app-version, maintenance, sync, privacy, and audit
rules.

## What A Record Represents

A record should represent one meaningful unit of tenant work or content.

Examples of record meaning may include:

- A job, case, task, visit, request, issue, inspection, form, note, content
  item, asset entry, supportable event, or tenant-specific workflow item.
- A user-created item that needs review, sync, history, reporting, or
  follow-up.
- A business object that can be viewed on mobile and controlled from Admin/API.
- A container for notes, attachments, activity, categories, tags, status, and
  permission-aware actions.

Record principles:

- A record always belongs to one tenant context.
- A record must have a clear business purpose before implementation.
- A record should expose only mobile-safe context to the current user.
- A record should not become a generic dumping ground for unrelated data.
- A record should have a lifecycle that users and admins can understand.
- A record should be reportable, auditable, and supportable where policy
  requires.
- A record should be feature-controlled so the module can be rolled out,
  disabled, limited, or plan-gated safely.

The module should stay generic enough to support many tenant workflows, but
specific enough that users understand what they are working on.

## Ownership And Authority

Records span both systems, but authority stays in Admin/API.

| Area | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Record authority | Tenant scope, validation, permissions, status meaning, lifecycle rules, visibility, retention, reporting, and canonical state. | Mobile presentation, local drafts, cached views, pending indicators, offline entry, and user-friendly recovery. |
| Record actions | Whether create, view, edit, archive, restore, delete, note, attach, tag, categorize, or status change is allowed. | Showing only allowed actions and collecting user intent for API review. |
| Sync and conflicts | Accepted writes, rejected writes, conflict decisions, idempotency meaning, audit, and final state. | Queued intents, retry UX, saved-local labels, conflict display, and local preservation. |
| Attachments | Acceptance, scanning/review policy, storage policy, privacy, retention, download visibility, and audit. | Native capture, local file selection, upload status, local preview where allowed, and failed-upload recovery. |
| Admin views | Tenant-wide visibility, filters, controls, reports, support context, and dangerous action review. | None beyond mobile-safe user context returned by API. |

Mobile should make record work efficient. Admin/API should make record work
safe, tenant-scoped, auditable, and governed.

## User Record Lifecycle

Record lifecycle should be understandable and reversible where policy allows.

### Create

Creation principles:

- Users create records only when the API or last-confirmed offline policy says
  the feature, tenant, role, permission, app version, and subscription allow
  creation.
- Online creation becomes trusted only after API acceptance.
- Offline creation may be a local draft or queued create intent, never a
  trusted server record.
- Required business meaning should be clear before a user starts a record.
- Creation should support minimum typing where possible through templates,
  defaults, categories, tags, scanning, native capture, or previously confirmed
  context.
- Duplicate or repeated create attempts should be handled through sync and
  idempotency principles.

### View

Viewing principles:

- Users view only records they are allowed to see for the current tenant.
- Cached records may be shown offline only with freshness and last-known
  context.
- Sensitive fields, restricted notes, private attachments, support-only
  context, and admin-only activity should not leak into mobile views.
- List views should support fast scanning: status, category, tags, freshness,
  pending state, and allowed next action.
- Detail views should separate record content, notes, attachments, activity,
  and system state clearly.

### Edit

Editing principles:

- Edits are requests for API acceptance, not final authority.
- Online edits require current validation, permission, feature, tenant,
  subscription, app-version, maintenance, and conflict checks.
- Offline edits may become local drafts or queued update intents only if the
  feature policy allows.
- Users should see whether edits are saved locally, pending sync, synced,
  failed, blocked, or conflicted.
- Edits should avoid overwriting newer server changes without conflict
  resolution.
- Mobile should preserve meaningful local edits until they are accepted,
  discarded, expired under documented policy, or escalated.

### Archive

Archive means the record is removed from active work without necessarily being
destroyed.

Archive principles:

- Archive is a lifecycle action controlled by permission and tenant policy.
- Archived records should be hidden from normal active lists unless filters or
  admin views include them.
- Archive should preserve audit and activity history.
- Archive may be reversible when restore is allowed.
- Offline archive should be queued only when policy allows; it is not trusted
  until API acceptance.
- Archive should not delete local drafts or pending notes silently.

### Restore

Restore returns an archived record to active use when policy allows.

Restore principles:

- Restore is an authority-changing action and requires API confirmation.
- Restore should re-check feature flags, tenant state, permissions, category,
  status, subscription, and app-version compatibility.
- Restore should make the restored state clear in activity history and admin
  views.
- Restore should not revive restricted attachments, notes, or actions unless
  current policy allows them.
- Offline restore should generally wait for online API access unless a future
  documented workflow allows a queued restore intent.

### Delete

Delete is destructive and should be treated as the riskiest lifecycle action.

Delete principles:

- Delete must be controlled by admin/API policy, permission, retention,
  privacy, audit, and tenant lifecycle rules.
- Normal mobile users should usually archive or submit a delete request rather
  than permanently delete records.
- Permanent deletion should require confirmation, audit meaning, and impact
  awareness.
- Delete should respect legal, billing, support, audit, reporting, and tenant
  deletion policies.
- Offline delete should not be treated as final. If allowed at all, it should
  be a queued intent or local hidden state until API acceptance.
- Users should understand whether a record was archived, delete-requested,
  permanently deleted, blocked, or waiting for admin review.

Deletion should never be a surprise side effect of failed sync, logout, tenant
switching, app update, or cache cleanup.

## Notes

Notes capture human context around a record.

Note principles:

- Notes should belong to a record and tenant context.
- Notes may be user-visible, internal, admin-only, support-only, or restricted
  depending on policy.
- Mobile should show only notes the current user may see.
- Offline note creation may be allowed as local draft or queued note intent.
- Notes should preserve authorship meaning, edit history meaning, and audit
  meaning where required.
- Notes should not be used to store secrets, raw credentials, private support
  payloads, or cross-tenant information.
- Deleted or hidden notes should not disappear from audit meaning when policy
  requires a history.

Users should understand whether a note is local, pending, synced, failed,
restricted, or removed.

## Attachments

Attachments provide supporting evidence or content for a record.

Attachment principles:

- Attachments may include photos, files, scans, audio, or other native-captured
  material only when the feature and tenant policy allow.
- NativePHP device features should be requested only after explaining why the
  attachment feature needs the permission.
- Mobile may stage attachments locally when offline policy allows, but upload,
  validation, scanning/review, storage, retention, and final association belong
  to Admin/API.
- Attachment status should be clear: local, queued, uploading, uploaded,
  failed, blocked, rejected, or removed.
- Sensitive attachments need privacy, app lock, tenant scoping, and support
  visibility limits.
- Attachments should not be silently uploaded under the wrong tenant, wrong
  record, wrong user, stale permission, or disabled feature.
- Users should be able to recover from failed attachment upload without losing
  the whole record draft where policy allows.

Attachments often carry the highest privacy and storage risk in the module, so
they need clear limits before implementation.

## Activity

Activity explains what happened to a record over time.

Activity principles:

- Activity should summarize meaningful record events: create, view where
  policy requires, edit, note, attachment, status change, category change, tag
  change, archive, restore, delete request, delete, sync acceptance, rejection,
  conflict, admin action, and support action.
- Activity should distinguish user actions, admin actions, API/system
  decisions, sync outcomes, and support actions.
- Mobile activity should be simplified and mobile-safe.
- Admin activity should help support, audit, reporting, and compliance without
  exposing unnecessary private payloads.
- Offline local activity is provisional until sync acceptance.
- Activity should help answer who acted, what changed, when it happened, where
  it applied, and why the outcome occurred.

Activity should create confidence and accountability, not clutter.

## Tags

Tags provide lightweight grouping and filtering.

Tag principles:

- Tags should help users find, group, and prioritize records.
- Tag creation and assignment may be controlled by tenant policy, admin
  settings, role permission, feature flags, or remote config.
- Mobile users may select from allowed tags and may create new tags only if
  policy allows.
- Offline tag changes may be local drafts or queued intents when allowed.
- Tags should not become an authorization system. Permissions still come from
  Admin/API.
- Tags should be tenant-scoped and should not leak between tenants.
- Tag conflicts should be resolved through sync/conflict rules if tags are
  renamed, removed, disabled, merged, or restricted while mobile is offline.

Tags are convenience metadata. They should not carry hidden business authority.

## Categories

Categories define stronger organization than tags.

Category principles:

- A category may shape record templates, required steps, allowed statuses,
  reports, retention, attachment rules, or mobile shortcuts.
- Category options should be admin/API-controlled.
- Mobile may choose only allowed categories for the current tenant, feature,
  role, and record state.
- Offline category selection may be local only until API validation.
- Category changes can affect permissions, status, required fields, reports,
  and sync conflicts, so they require careful revalidation.
- Category removal or disablement should not make existing records unusable
  without a documented fallback state.

Categories should help tenants model work without turning mobile into a local
configuration authority.

## Status

Status represents where a record is in its business lifecycle.

Status principles:

- Status options and transitions belong to Admin/API policy.
- Mobile should show only allowed status choices and next actions.
- Status changes require current permission, feature, tenant, subscription,
  app-version, maintenance, and conflict checks.
- Offline status changes should be queued only when policy allows and should
  remain pending until API acceptance.
- Status should be clear enough for dashboard shortcuts, reports, support, and
  notifications.
- Status should not be confused with sync status. A business status like
  active or complete is different from pending sync, failed, or conflicted.
- Status changes should be auditable when they affect work outcomes, reports,
  billing, support, or compliance.

Status is business meaning. Sync state is delivery meaning. The module should
keep them visibly separate.

## What Works Offline

Offline record behavior should be useful, bounded, and honest.

The mobile client may support offline:

- Viewing cached record lists and details when tenant policy allows.
- Creating local record drafts.
- Editing local drafts.
- Preparing notes as drafts or queued note intents.
- Staging allowed attachments locally.
- Assigning allowed last-known tags or categories as local draft choices.
- Preparing queued create, update, note, attachment metadata, archive, or status
  change intents when feature policy allows.
- Showing local activity for draft and pending work as provisional.
- Showing saved-local, pending, failed, stale, blocked, or conflict states.

Offline record work must remain tenant-scoped, user-scoped, freshness-aware,
and protected by app lock/privacy rules where needed.

## What Requires Sync Or Online API Access

Server-trusted record behavior requires API authority.

The following require sync or online API access:

- Trusted record creation.
- Trusted edits.
- Final archive, restore, or delete.
- Permission-sensitive view refresh.
- Current category, tag, status, and action availability.
- Attachment upload, validation, scanning/review, storage, transformation, and
  final association.
- Conflict detection and resolution.
- Audit acceptance.
- Report inclusion.
- Support visibility.
- Notifications or workflow side effects.
- Actions affected by billing, tenant lifecycle, maintenance, app version, or
  feature flag changes.

Mobile may prepare intent. Admin/API decides acceptance.

## Admin View And Control

Admins need tenant-safe visibility and control without bypassing privacy and
least privilege.

Admin control principles:

- Platform admins may govern module availability, global controls, support
  visibility, reports, retention policy, and dangerous actions according to
  platform authority.
- Tenant admins may view and manage tenant records only inside their tenant and
  only within delegated permissions.
- Tenant managers may view, filter, assign, update, archive, restore, or review
  records only when tenant policy grants those actions.
- Support agents may see enough record context to help users when policy
  allows, but should not gain default business authority or unrestricted
  payload visibility.
- Admin views should expose record state, status, sync health, conflict state,
  attachment state, notes visibility, activity, and audit context in a
  tenant-safe way.
- Dangerous admin actions such as forced status change, restore, permanent
  delete, bulk archive, retention override, or attachment removal should show
  impact before saving and require audit history.
- Admin changes should produce mobile-safe outcomes through the API, not direct
  mobile state assumptions.

Admin control should make record operations governable without turning support
or tenant admins into cross-tenant operators.

## Permissions

Permissions decide what a user can see or do with records.

Permission principles:

- Record permissions are resolved by Admin/API before API access and mobile UI
  visibility.
- Permissions may differ for create, view list, view detail, edit, archive,
  restore, delete, note, attach, tag, categorize, change status, export,
  report, and admin review.
- Mobile should hide or disable actions that the API says are unavailable, but
  backend authorization remains mandatory.
- Suspended users fail closed.
- Suspended, archived, billing-blocked, maintenance-blocked, or
  deletion-requested tenants may change record access.
- Permissions should interact with feature flags as separate gates: permission
  says "may this user act"; feature flag says "is this capability available."
- Offline permissions are last-known presentation only and must be revalidated
  before trusted sync acceptance.

Permission UX should be clear: unavailable because offline, denied by role,
disabled by feature, blocked by billing, or waiting for admin/support.

## Feature Flags And Remote Config

The records module should be feature-controlled from the start.

Feature-control principles:

- The whole records module can be enabled or disabled.
- Sub-features can be controlled independently: create, edit, archive, restore,
  delete, notes, attachments, activity, tags, categories, status changes,
  offline drafts, offline queueing, search, filters, reports, exports, and
  admin review.
- Feature flags may apply globally, by tenant, plan, role, permission, user,
  app version, device, cohort, maintenance, or emergency policy.
- Disabled record features should not request native permissions or collect
  local input.
- Remote config may shape safe presentation: default filters, labels, limits,
  retry messaging, offline guidance, or lightweight workflow options.
- Remote config should not grant authority that permissions, tenant policy,
  billing, or feature flags deny.
- Mobile should treat cached feature/config state as presentation only when
  offline.

Feature control allows safe rollout, safe rollback, plan limits, and tenant
customization without shipping a new app build.

## Reporting And Support

Records become more valuable when admins can understand work health.

Reporting/support principles:

- Reports should use server-trusted state, not unsynced local drafts.
- Pending, failed, conflicted, archived, restored, deleted, and blocked states
  should be reportable where useful.
- Support should understand whether a user issue is caused by permission,
  feature flag, sync failure, conflict, attachment upload, tenant state,
  billing, app version, maintenance, or local offline state.
- Mobile diagnostics should avoid private payloads while providing enough
  context for support.
- Record activity should help support reconstruct user-visible outcomes without
  exposing data outside tenant/privacy boundaries.

The module should be built for supportability before it becomes large.

## Risk Boundaries

Records/content is a high-blast-radius module because it combines user input,
attachments, offline work, audit, and tenant data.

Risks to avoid:

- Treating mobile drafts as server-trusted records.
- Cross-tenant record visibility, drafts, attachments, tags, categories, or
  activity.
- Silent deletion of records, drafts, notes, or attachments.
- Confusing business status with sync state.
- Uploading attachments after feature disablement, permission loss, tenant
  switch, logout, or app-version block.
- Letting tags or categories act as hidden permissions.
- Exposing support-only notes or private attachments to mobile users.
- Retrying permanent policy failures as if they were network failures.
- Allowing admin bulk actions without impact preview and audit meaning.
- Using offline cache as authority after tenant/user access changes.

These risks should be recorded before any implementation work begins.

## Acceptance Questions

Every records/content implementation slice should answer:

- What does a record represent for this tenant workflow?
- Who can create, view, edit, archive, restore, and delete it?
- Which notes, attachments, activity, tags, categories, and status values are
  visible to each role?
- Which actions work offline as drafts or queued intents?
- Which actions require online API authority?
- How are saved-local, pending, syncing, synced, failed, blocked, and conflicted
  states shown?
- What does admin see that mobile does not?
- What does support see, and what remains private?
- What happens when the feature is disabled?
- What happens when the tenant or user is suspended?
- What happens when a record changes on the server while mobile is offline?
- What is audited?
- What is reportable?
- What prevents data loss?

If these questions are unanswered, the module is not ready for application
logic.

## Success Standard

The records/content module succeeds when mobile users can work with
tenant-scoped records simply, admins can govern records safely, support can
understand record issues, and the API remains the only source of trusted
record authority.

The product standard is clear: useful mobile record work, strong tenant
boundaries, explicit permissions, feature-controlled rollout, honest offline
state, recoverable sync, auditable lifecycle decisions, and no schema or
implementation assumptions before documentation is accepted.
