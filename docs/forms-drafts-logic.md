# Forms And Drafts Logic

Updated: 2026-06-26

This document defines mobile forms and draft behavior for the Mobile Lara SaaS
system. It explains simple forms, multi-step forms, validation principles,
autosave principles, offline draft behavior, submit behavior when online,
submit behavior when offline, user feedback after submit, admin-controlled form
availability, and avoiding data loss. It is documentation only and does not
define database structure, database fields, migrations, indexes, seeders,
routes, controllers, Livewire components, Filament resources, NativePHP
plugins, policies, gates, middleware, jobs, services, local storage schemas,
API endpoints, UI components, CSS, JavaScript, background workers, queues, or
application logic.

Use this document with [Product Principles](product-principles.md), [Mobile UX
Principles](mobile-ux-principles.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md),
[Mobile App Shell Logic](mobile-app-shell-logic.md), [Mobile Settings
Logic](mobile-settings-logic.md), [Mobile Permission Logic](mobile-permission-logic.md),
[Mobile App Lock Principles](mobile-app-lock-principles.md), [Two-System
Boundary Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Role And Permission Logic](role-permission-logic.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md),
[Data Privacy Principles](data-privacy-principles.md), [Audit Logic](audit-logic.md),
[Offline-First Principles](offline-first-principles.md), [Offline UX
Logic](offline-ux-logic.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md),
[Conflict Resolution Logic](conflict-resolution-logic.md), [Records/Content
Module Logic](records-content-module-logic.md), [Search Logic](search-logic.md),
[Notifications Logic](notifications-logic.md), and [API v1 Records
Contract](../contracts/api/v1-records.md): forms are the primary mobile input
surface, drafts protect user work, and Admin/API remains authoritative for
validation, authorization, submission acceptance, sync, conflict handling,
audit, tenant boundaries, notification-triggered outcomes, and feature
availability.

Notifications Logic is defined in `notifications-logic.md`:
form-submission notifications, draft reminders, validation/security notices,
and deep links back to form-related work must remain Admin/API-authoritative,
tenant-scoped, permission-aware, preference-aware, and mobile-safe.

Support System Logic is defined in `support-system-logic.md`:
support requests, support messages, attachments, tenant context, support-agent
visibility, audit, notifications, and offline support drafts must remain
tenant-scoped, least-privilege, privacy-safe, and Admin/API-authoritative.

Billing And Plan Logic is defined in `billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature states,
and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

## Forms And Drafts Statement

Mobile forms should make it safe and fast for users to enter tenant work
without losing progress.

A form is the user-facing input surface for creating, updating, submitting,
annotating, searching, configuring, or requesting tenant work. A draft is the
local or server-known unfinished state that preserves user intent before the
API accepts the final action.

Product rule: mobile may preserve and explain draft work locally, but submitted
work becomes trusted only when Admin/API accepts it under current tenant, user,
permission, feature flag, subscription, app-version, maintenance, validation,
sync, privacy, and audit rules.

## Goals

Forms and drafts should:

- Keep mobile data entry simple and calm.
- Reduce unnecessary typing.
- Validate early enough to help, but not so aggressively that users feel
  blocked while thinking.
- Preserve work during navigation, lock, app backgrounding, poor connection,
  offline periods, and recoverable errors.
- Clearly distinguish unsaved, saved locally, pending sync, submitted, accepted,
  rejected, failed, and conflicted states.
- Allow online submission through API authority.
- Allow offline draft work where useful and policy permits.
- Prevent stale drafts from bypassing current permissions or feature rules.
- Help admins control form availability without requiring a new mobile build.
- Avoid data loss by design, not by hopeful user behavior.

Forms and drafts should not:

- Treat local draft data as trusted server state.
- Let hidden fields, cached rules, or local UI state bypass API authorization.
- Store sensitive values longer than necessary.
- Keep drafts after logout, revocation, tenant suspension, or policy change when
  they should be cleared or locked.
- Submit duplicate work because the user tapped twice or the network retried.
- Hide the difference between local save and API acceptance.

## Ownership And Authority

| Concern | Admin/API authority | Mobile responsibility |
| --- | --- | --- |
| Form availability | Decide which forms exist, who can use them, which tenants/plans/modules they apply to, and when they are disabled. | Show, hide, disable, or explain form entry points from resolved API context. |
| Form purpose | Define the business action represented by the form. | Present a simple mobile path for that action without inventing new authority. |
| Validation | Own final validation rules and accepted values. | Provide helpful local and real-time feedback, then submit only through API authority. |
| Drafts | Decide which drafts are allowed, retained, synced, discarded, locked, or blocked. | Preserve allowed local work and clearly label its state. |
| Autosave | Define whether autosave is enabled and what autosave means. | Autosave safely as draft/local intent, not as trusted final submission. |
| Submission | Accept, reject, defer, or conflict submitted work. | Prevent duplicate intent, show progress, preserve user input, and render API outcome. |
| Offline behavior | Define which forms can be filled, saved, queued, or blocked offline. | Apply offline policy, label local state, and wait for API confirmation where needed. |
| Privacy | Define sensitive fields, retention, deletion, export, support visibility, and diagnostics limits. | Avoid leaking form input through cache, logs, screenshots, recents, diagnostics, or cross-tenant state. |
| Admin changes | Define feature, config, version, maintenance, and tenant-state changes. | Stop, lock, migrate, or explain affected drafts and forms safely. |

## Simple Forms

Simple forms should complete one clear action.

Examples may include:

- Create a record.
- Edit a small record detail.
- Add a note.
- Update a status.
- Change a setting.
- Submit feedback.
- Send a support request.
- Apply a tag or category.

Simple-form principles:

- The form should have one primary purpose.
- Required input should be obvious before submission.
- Optional input should not crowd the main action.
- The user should see what will happen when they submit.
- The primary action should be easy to reach with one hand.
- Destructive or irreversible actions should not feel like ordinary form
  submission.
- The form should preserve input when validation fails.
- The form should not ask for data that Admin/API can infer safely.
- The form should not expose disabled fields only because cached UI still knows
  about them.

Simple forms should prefer:

- Short labels.
- Clear inline validation.
- Sensible defaults.
- Pickers over free typing where the values are controlled.
- Native permissions only when the enabled feature actually requires them.
- One visible primary action.
- Safe cancel and back behavior.

Simple forms should avoid:

- Long blocks of explanatory text inside the form.
- Hidden business decisions.
- Multi-purpose submission buttons.
- Unclear loading states.
- Fields that change meaning based on stale local state.
- Asking users to re-enter data after recoverable errors.

## Multi-Step Forms

Multi-step forms should break complex work into understandable stages.

Use a multi-step form when:

- The user must provide several groups of information.
- One step depends on another.
- The form includes attachments, review, permissions, or confirmation.
- The action is important enough that users need a review stage.
- The workflow benefits from saving progress between steps.

Multi-step principles:

- Each step should have a clear purpose.
- Users should know where they are in the flow.
- Users should be able to go back without losing completed input.
- Step transitions should save draft progress when policy allows.
- Validation should happen at the right level: field, step, and final submit.
- The final review should show the important submitted meaning, not internal
  field mechanics.
- Online-only steps should be clearly disabled or deferred when offline.
- Sensitive or irreversible final submission should require explicit user
  confirmation.

Multi-step forms should not:

- Force users through unnecessary steps for simple actions.
- Hide validation until the final screen when earlier feedback would help.
- Let later steps depend on data that has become unavailable or unauthorized.
- Lose progress when the app locks, suspends, or reconnects.
- Pretend a step is complete if required API confirmation failed.

Step status vocabulary should be consistent:

- Not started.
- In progress.
- Needs attention.
- Saved locally.
- Pending sync.
- Ready to review.
- Submitted.
- Accepted.
- Blocked.
- Conflict.

## Validation Principles

Validation protects both the user and the system.

Validation principles:

- API validation is final.
- Mobile validation is helpful guidance, not authority.
- Livewire state and public properties must be treated as user input.
- Validation should happen before data is trusted, stored, submitted, synced,
  or used for decisions.
- Authorization and validation should both be checked for protected actions.
- Validation errors should be field-specific where possible.
- Cross-field validation should explain the relationship in plain language.
- Server validation errors should preserve safe user input.
- Offline validation should use only safe local rules and should not claim
  final acceptance.
- Validation should fail closed when config, feature flags, permissions, or
  tenant context are missing or stale.

Validation should communicate:

- What needs attention.
- Why the current value cannot continue.
- Whether the issue can be fixed offline.
- Whether the server must be reached.
- Whether a permission, feature, version, maintenance, billing, or tenant state
  is blocking the form.

Validation should avoid:

- Raw internal rule names.
- Stack traces or implementation details.
- Exposing hidden field names.
- Revealing inaccessible records, tenants, users, or configuration.
- Validating sensitive data more often than needed.
- Blocking draft save when a draft can safely preserve unfinished work.

## Autosave Principles

Autosave should protect progress without confusing users about submission.

Autosave may mean:

- Save the current input locally as a draft.
- Save the current step state locally.
- Update an existing local draft.
- Queue an allowed intent for later sync.
- Send a draft update to API when online and policy allows.

Autosave must not mean:

- The final business action has been accepted.
- API validation has passed.
- Permissions have been confirmed forever.
- The user has submitted intentionally.
- Conflicts cannot happen later.

Autosave principles:

- Autosave should be visible but quiet.
- Autosave should not interrupt typing.
- Autosave should preserve enough state to recover useful work.
- Autosave should not store data that policy forbids caching.
- Autosave should be tenant-scoped and user-scoped.
- Autosave should stop or lock when the form becomes unavailable.
- Autosave should protect against duplicate local drafts.
- Autosave should explain failed local save or failed server draft sync.
- Autosave should be configurable by Admin/API when risk differs by tenant,
  module, form type, or plan.

Autosave states should be clear:

- Unsaved changes.
- Saving locally.
- Saved locally.
- Pending server sync.
- Synced draft.
- Autosave failed.
- Autosave blocked.

## Offline Draft Behavior

Offline drafts are how mobile preserves work when the API cannot be reached.

Offline drafts may support:

- Creating a new record draft.
- Editing an allowed cached record as a pending intent.
- Adding notes that wait for sync.
- Preparing attachments that wait for upload or confirmation.
- Completing multi-step form input locally.
- Capturing scan, camera, file, or typed input when the feature is enabled and
  permission is granted.
- Preserving support or feedback input until online.

Offline drafts must be limited:

- They are not server-trusted.
- They are not visible to other devices until accepted by API.
- They may become invalid if permissions, tenant state, feature flags, version
  policy, or server data changes.
- They cannot guarantee that a referenced record still exists.
- They cannot guarantee that a selected tag, category, status, user, or setting
  is still allowed.
- They cannot submit online-only actions while offline.
- They should not cache sensitive values that policy marks as never-cache.

Offline draft UX should show:

- Draft title or safe summary.
- Current tenant.
- Saved-local state.
- Last local save meaning.
- Required sync state.
- Missing required fields.
- Attachments waiting for upload.
- Conflicts or policy blocks.
- Available actions: continue, review, retry, discard, export if allowed, or
  contact support where relevant.

Offline drafts should survive normal mobile interruptions, but they should not
survive security boundaries that require clearing or locking them.

## Submit Behavior When Online

Online submission is the normal trusted path.

Online submit should:

- Confirm the current tenant and user context.
- Apply current permissions, feature flags, plan limits, app-version rules,
  maintenance state, and remote config.
- Validate the payload at the API boundary.
- Prevent duplicate submission intent.
- Return a predictable accepted, rejected, conflict, blocked, or retryable
  result.
- Preserve safe user input when validation fails.
- Mark local drafts as accepted, replaced, rejected, conflicted, or still
  pending based on API response.
- Produce audit or activity history where policy requires.
- Update sync state so the user knows what happened.

Online submit should not:

- Trust hidden fields, stale local IDs, or local permission flags.
- Submit without the user's clear intent unless the feature is explicitly a
  draft-sync behavior.
- Discard local drafts before the API outcome is known.
- Hide validation or authorization failures.
- Retry blindly in a way that creates duplicate business actions.

Online success should mean API acceptance, not just a network request finishing.

## Submit Behavior When Offline

Offline submission should be honest about what can and cannot happen.

Offline submit may:

- Save the form as a local draft.
- Queue an allowed intent for later sync.
- Label the item as pending sync.
- Let the user continue working if the form policy allows.
- Show what will happen when the app reconnects.

Offline submit must not:

- Claim the form is submitted to the server.
- Mark the work as accepted.
- Trigger server-only actions.
- Complete billing, permission, identity, tenant, or irreversible workflow
  decisions.
- Upload attachments that require online storage.
- Send notifications, reports, or admin-visible final outcomes.
- Hide that API confirmation is still required.

When offline submission is not allowed, mobile should:

- Preserve the user's input as a draft if policy allows.
- Explain that submission requires connection.
- Disable the final submit action or convert it to save draft.
- Avoid panic language.
- Offer retry when online.

## User Feedback After Submit

Submit feedback should answer three questions:

- Was my work preserved?
- Did the server accept it?
- What should I do next?

Feedback states:

- Submitted and accepted.
- Saved locally.
- Pending sync.
- Needs correction.
- Blocked by permission or policy.
- Blocked by tenant, billing, maintenance, or app version.
- Failed but preserved.
- Failed and needs retry.
- Conflict requires review.
- Discarded by user.

Feedback principles:

- Always distinguish local save from API acceptance.
- Show validation errors near the relevant fields.
- Keep success feedback short.
- Keep failure feedback actionable.
- Preserve user input after recoverable errors.
- Prevent duplicate taps while submitting.
- Show pending items in a place the user can return to.
- Confirm destructive discard.
- Provide support context only when useful and privacy-safe.

Mobile should avoid vague feedback such as "done" when the state is actually
pending sync, blocked, failed, or locally saved only.

## Admin-Controlled Form Availability

Admins should control whether forms are available and how risky behavior is
allowed.

Admin/API may control:

- Which forms exist.
- Which tenants can use a form.
- Which roles or users can use a form.
- Which plans include a form.
- Which app versions can use a form.
- Whether a form is read-only, hidden, disabled, or active.
- Whether drafts are enabled.
- Whether offline drafts are enabled.
- Whether autosave is enabled.
- Whether multi-step flow is required.
- Whether attachments, camera, scanner, file, location, or microphone input are
  enabled.
- Whether submission requires online API confirmation.
- Whether existing drafts can continue after a form is disabled.
- Whether stale drafts expire, lock, or require review.
- Which validation/config messages are safe for mobile display.

Admin-control principles:

- Form availability should resolve through API context, not mobile guesses.
- Disabling a form should explain mobile impact before saving the admin change.
- Existing drafts need a clear policy: continue, lock, submit online, discard,
  archive, or support review.
- Dangerous form changes require audit history.
- Tenant-specific changes must not affect other tenants.
- Plan limits should disable or downgrade form behavior predictably.
- Maintenance or forced update may block submit while preserving safe drafts.

Admin controls should not expose hidden forms to mobile by accident. Mobile may
have stale UI, but API must remain final.

## Avoiding Data Loss

Avoiding data loss is a product requirement, not only a technical concern.

Data-loss prevention principles:

- Preserve work before navigation, app backgrounding, lock, tenant switching,
  forced update prompts, and connection changes when policy allows.
- Warn before leaving a form with unsaved or unsynced work.
- Confirm destructive discard.
- Keep drafts tenant-scoped.
- Keep drafts user-scoped unless shared draft behavior is explicitly
  documented.
- Keep attachments and form text linked in user-facing state.
- Never delete a draft only because a submit attempt failed.
- Never clear a draft until accepted, discarded, expired by policy, or blocked
  by a documented security rule.
- Show where pending work can be found later.
- Protect drafts with app lock and secure local storage principles.
- Make tenant switching handle unsaved drafts explicitly.
- Make logout, logout-all-devices, and server revocation handle drafts
  according to privacy and security policy.

Data-loss prevention should also include emotional clarity. Users should not
have to guess whether the app lost their work.

## Privacy And Security

Forms often contain the most sensitive user input in the product.

Privacy and security principles:

- Treat every form input as untrusted until API validation and authorization.
- Do not store secrets, tokens, passwords, payment details, or prohibited data
  in ordinary drafts.
- Avoid diagnostics that include raw form input.
- Avoid support views that expose private draft content by default.
- Keep local drafts separated by tenant and user.
- Clear or lock drafts after logout, server revocation, tenant suspension, app
  lock failure, or policy-triggered storage cleanup.
- Do not let a stale draft resurrect access to a disabled or unauthorized form.
- Do not expose hidden field names, internal validation rules, or server
  internals through error messages.
- Avoid screenshots or previews of sensitive fields where platform behavior
  can be controlled.
- Attachments and native-captured data should follow the same privacy rules as
  typed form input.

Sensitive form types may require a stricter policy: online-only, no autosave,
short retention, app-lock confirmation, or explicit discard after completion.

## Relationship To Records And Search

Forms are often how records are created or updated, and drafts are often how
record work survives offline.

Record-related form behavior:

- Create-record forms may become local drafts before API acceptance.
- Edit-record forms may become pending update intents.
- Note forms may allow offline drafts when enabled.
- Attachment forms may capture local metadata before upload.
- Status forms may require online confirmation if status changes are
  business-critical.
- Archive, restore, and delete flows should use confirmation rather than casual
  autosave.
- Conflicts should protect the user's draft before asking for resolution.

Search-related form behavior:

- Search forms should preserve safe query state without treating it as a
  business draft.
- Saved filters are not the same as drafts.
- Search input should not leak into form drafts unless explicitly intended.
- Form drafts may appear in local search only when safe and clearly labeled.

## Risks

Forms and drafts risks to record before implementation:

- Local drafts being mistaken for submitted work.
- API validation and mobile validation drifting apart.
- Hidden or stale fields bypassing current permission rules.
- Offline submissions creating duplicate work.
- Autosave storing sensitive data longer than allowed.
- Multi-step forms losing data during navigation, lock, or app backgrounding.
- Attachments becoming separated from the draft they belong to.
- Tenant switching mixing draft data between tenants.
- Admin disabling a form without a plan for existing drafts.
- Server revocation leaving private drafts visible on a device.
- Error messages exposing internal rules or inaccessible resources.
- Sync conflicts discarding user input too early.

## Acceptance Questions

Before implementing any mobile form or draft behavior, the team should answer:

- What action does this form represent?
- Is the form simple or multi-step?
- What can be drafted locally?
- What can be autosaved?
- What must never be cached?
- What validation can happen locally, and what requires API authority?
- Can the form be used offline?
- Can it be submitted offline, or only saved as draft?
- What happens to drafts after logout, tenant switch, revocation, suspension,
  forced update, or maintenance?
- What permissions and feature flags control the form?
- What admin settings control availability, autosave, offline drafts, and
  submission?
- What is audited?
- What user feedback appears after success, local save, pending sync, failure,
  block, or conflict?
- How is data loss prevented?

## Success Standard

Forms and drafts are successful when mobile users can enter work quickly,
recover from interruptions, understand whether work is local or accepted,
submit through API authority, continue safely where offline behavior is allowed,
and trust that permission, tenant, privacy, feature, sync, and admin-control
boundaries are never bypassed by local draft state.
