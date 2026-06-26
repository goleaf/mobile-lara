# Tenant Admin Logic

Updated: 2026-06-26

This document defines tenant admin logic for the Mobile Lara SaaS system. It
explains what tenant admins can control, what only platform admins can
control, how tenant admins invite users, how tenant admins manage mobile
features if allowed, how tenant admins view reports, how tenant admins manage
support, and how tenant admins must remain isolated from other tenants. It is
documentation only and does not define database structure, database fields,
migrations, seeders, routes, controllers, Livewire components, Filament
resources, NativePHP plugins, policies, gates, middleware, jobs, services,
local storage schemas, billing integrations, support integrations, reports, or
application logic.

Use this document with [Target User Roles](user-roles.md), [Role And
Permission Logic](role-permission-logic.md), [Tenant Lifecycle Logic](tenant-lifecycle-logic.md),
[Admin Control Center Logic](admin-control-center-logic.md), [Admin/API
Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [API-First Principles](api-first-principles.md),
[Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md),
[Mobile Version Control Logic](mobile-version-control-logic.md), [Audit
Logic](audit-logic.md), [Data Privacy Principles](data-privacy-principles.md),
[Multi-Tenant Mobile Logic](multi-tenant-mobile-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Mobile Dashboard
Logic](mobile-dashboard-logic.md), [Mobile Settings Logic](mobile-settings-logic.md),
and [API v1 Tenancy Contract](../contracts/api/v1-tenancy.md): tenant admins
operate one tenant inside platform-defined limits, while Admin/API remains the
source of authority for scope, lifecycle, billing, security, and mobile-safe
outcomes.

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

## Tenant Admin Statement

A tenant admin is the highest normal operator inside one tenant.

Tenant admins can manage the people, settings, delegated feature controls,
reports, support workflows, and operational visibility that belong to their own
tenant. They should help the tenant business operate the mobile platform
without needing platform-wide access.

A tenant admin is not a platform owner, super admin, global billing operator,
global support operator, release manager, security authority, or cross-tenant
operator. The Admin/API system must enforce that distinction even when a
tenant admin uses an admin panel, Livewire action, mobile-facing setting, report
export, support view, tenant switcher, or cached mobile state.

Product rule: a tenant admin may control only tenant-scoped behavior that
platform policy, tenant lifecycle state, role and permission state, plan state,
feature flags, app-version rules, maintenance posture, security policy, and API
authorization all allow.

## Authority Split

Tenant admins work inside the tenant boundary. Platform admins own the boundary
itself.

| Area | Tenant admin may control | Platform-only control |
| --- | --- | --- |
| Tenant identity | Tenant display name, tenant-facing contact details, support contact preferences, and allowed tenant profile settings. | Tenant creation, permanent deletion, restore, global suspension, archival policy, legal identity policy, and cross-tenant identity decisions. |
| Users | Own-tenant invitations, activation assistance, allowed role assignment, user suspension requests or delegated suspension, and tenant membership cleanup. | Platform users, super admins, support agents, billing operators, cross-tenant memberships, global account state, and final user-security decisions. |
| Roles and permissions | Tenant-scoped role assignment and delegated permission presets where allowed. | Platform roles, super admin permissions, global permission catalog, dangerous permission definitions, and cross-tenant access. |
| Mobile features | Delegated tenant-level feature enablement or disablement inside plan, platform, version, and safety limits. | Global feature catalog, emergency kill switches, plan gates, app-version gates, security gates, and feature semantics. |
| Remote config | Delegated tenant-safe presentation or workflow defaults where allowed. | Global defaults, schema, validation, compatibility rules, security-sensitive config, and emergency rollback. |
| Reports | Tenant-scoped dashboards, summaries, report filters, and exports when permitted. | Cross-tenant reports, platform health, global billing reports, global support analytics, and unrestricted data exports. |
| Notifications | Tenant announcements, tenant-level notification preferences, and allowed targeting inside the tenant. | Platform-wide templates, provider configuration, emergency notifications, global quiet-hour policy, and cross-tenant campaigns. |
| Support | Own-tenant support cases, safe diagnostics, case comments, status tracking, and escalation requests. | Support tooling configuration, cross-tenant support queues, platform incident response, global diagnostic policy, and privileged recovery actions. |
| Billing | Tenant-visible billing summaries or billing-contact workflows when permitted. | Plan definitions, pricing, payment provider configuration, entitlement engine, invoices across tenants, and final billing-block decisions. |
| Tenant lifecycle | Onboarding completion tasks and tenant-local recovery coordination. | Tenant creation, trial extension policy, suspension, restore, archive, deletion, retention, and lifecycle override authority. |
| Audit and privacy | Tenant-visible audit summaries for allowed tenant actions. | Audit retention, immutable audit storage, global audit exports, security investigations, and privacy policy. |

## What Tenant Admins Can Control

Tenant admin controls should be practical, scoped, and explainable.

Tenant admins can control:

- Tenant-facing profile details that do not change legal, billing, or platform
  ownership policy.
- Tenant users, invitations, memberships, allowed roles, and delegated access
  inside their tenant.
- Tenant manager and mobile user visibility where the platform allows tenant
  delegation.
- Tenant notification preferences, announcement targeting, and communication
  defaults within platform-defined channel rules.
- Tenant support cases, tenant-safe diagnostics, and support escalation
  requests.
- Tenant reports, operational summaries, export requests, and dashboard filters
  that match their role and data scope.
- Tenant mobile feature availability only when the platform has explicitly
  delegated that feature to tenant control.
- Tenant-specific workflow defaults or safe remote-config overrides only when
  the platform allows tenant overrides.
- Tenant onboarding tasks that are explicitly assigned to tenant admins.
- Tenant-local cleanup such as removing inactive members, helping invited users,
  explaining disabled features, and coordinating with support or billing roles.

Tenant admin controls should always name their scope before saving: tenant,
user, role, permission, feature, report, support case, notification, config, or
mobile-visible behavior.

## What Only Platform Admins Can Control

Some controls must remain platform-only because they affect the SaaS boundary,
security posture, commercial model, release safety, or more than one tenant.

Platform-only controls include:

- Creating tenants outside approved onboarding flows.
- Permanently deleting, restoring, archiving, suspending, or globally blocking a
  tenant.
- Changing global feature flag definitions, default feature behavior, emergency
  feature kill switches, or feature semantics.
- Changing global remote configuration schema, compatibility rules, fallback
  behavior, or security-sensitive config.
- Setting minimum supported mobile versions, forced updates, maintenance mode,
  store links, release messages, and stale-client protection.
- Defining billing plans, plan limits, pricing, payment provider settings,
  entitlement rules, trial extension policy, and final billing-block behavior.
- Granting platform owner, super admin, support agent, billing manager, or
  cross-tenant roles.
- Viewing or exporting cross-tenant reports, platform health, global audit
  history, global support analytics, or unrestricted diagnostics.
- Changing security policy, token/session policy, device-trust policy, data
  retention, data deletion, privacy defaults, or diagnostic redaction rules.
- Managing secrets, external providers, infrastructure, queues, storage,
  notification providers, and integration credentials.

Tenant admins may request some platform-only actions through support or billing
workflows, but request visibility is not authority to perform the action.

## Invitation Logic

Tenant admin invitation logic should let tenant admins grow and manage their
own tenant without creating cross-tenant or platform risk.

Invitation principles:

- A tenant admin may invite users only into the tenant they administer.
- Invitations must use allowed tenant roles or permission presets, not arbitrary
  platform roles.
- Invitations should clearly distinguish tenant admin, tenant manager, mobile
  user, and any future tenant-scoped role.
- Invitation acceptance should go through API-controlled authentication,
  verification, consent, tenant membership, and bootstrap context.
- Invited users should see only safe invitation details before activation.
- Expired, revoked, already-used, suspicious, or out-of-scope invitations should
  fail closed with user-friendly guidance.
- Tenant admins should be able to resend, revoke, or correct invitations only
  inside their tenant and only while the invitation state allows it.
- Bulk invitations are acceptable only when limits, preview, validation,
  duplicate handling, audit, privacy, and support behavior are documented first.
- Invitation errors must not reveal whether an email belongs to another tenant,
  another account, a platform admin, a support agent, or a private user.
- Every invitation decision that changes access should be auditable.

Mobile behavior:

- The mobile client should not create tenant authority from an invitation link.
- Mobile should show invitation, activation, expired, revoked, already accepted,
  or contact-admin state only after API confirmation.
- Offline mode should not accept, modify, or upgrade invitations.

## Mobile Feature Delegation

Tenant admins may manage mobile features only when the platform has delegated
that control.

Delegated feature principles:

- Tenant admin feature control is an override inside platform rules, not a
  replacement for platform rules.
- Global emergency disables, plan limits, security gates, version rules,
  maintenance mode, tenant lifecycle state, and explicit user permissions
  always outrank tenant admin preference.
- Tenant admins should see the mobile impact before enabling or disabling a
  feature: affected roles, mobile screens, native permissions, offline behavior,
  reports, support impact, and sync behavior.
- Disabled features should be hidden or disabled on mobile according to feature
  flag logic, with safe explanations for roles that need them.
- A disabled feature should not request native permissions, queue new offline
  writes, or expose stale local workflows as usable.
- Tenant admins should understand whether a feature is unavailable because of
  plan limit, platform block, app-version requirement, tenant state, user
  permission, maintenance, or security policy.
- Feature changes should have audit history, support context, and rollback
  expectations where possible.

Mobile behavior:

- Mobile receives feature visibility, disabled states, update-required states,
  and support guidance through API context.
- Cached feature state can help offline presentation, but protected writes must
  be rechecked with the API before acceptance.
- If a tenant admin disables a feature while a user is offline, mobile should
  label stale state and reconcile safely when online.

## Reports Logic

Tenant admins need reports to run their tenant, not to inspect the platform.

Report principles:

- Tenant admin reports should be scoped to the current tenant.
- Reports should show the least detail needed for operational, support,
  billing-visible, or compliance-visible decisions.
- Tenant admins may see tenant-level usage, feature adoption, sync health,
  support trends, notification outcomes, workflow summaries, and mobile access
  posture when permitted.
- Sensitive reports should require the right permission, role, tenant state,
  export rule, confirmation, audit expectation, and privacy review.
- Report exports should be treated as higher risk than on-screen summaries.
- Reports should not include another tenant's users, records, diagnostics,
  support cases, billing details, private mobile cache, unsynced drafts, or raw
  audit internals.
- Report freshness, filters, limitations, and hidden data should be clear
  enough for tenant admins and support to explain.

Mobile behavior:

- Mobile users may see personal or workflow summaries only when API grants
  them.
- Tenant admin report decisions should not grant mobile report authority
  locally.
- Offline mobile reports should be labeled as cached, partial, or unavailable
  unless the API has explicitly allowed offline-safe summaries.

## Support Logic

Tenant admins are a first line of tenant support, but they are not platform
support operators by default.

Support principles:

- Tenant admins should see and manage support cases for their tenant when
  permitted.
- Tenant admins should be able to help mobile users understand account state,
  invitation state, disabled features, sync state, offline state, version
  blocks, and tenant lifecycle blocks.
- Tenant admins may see safe diagnostics such as app version, device category,
  sync status, last context refresh, feature state, and support-safe error
  categories when allowed.
- Tenant admins should not see secrets, tokens, raw private payloads,
  unrelated user content, another tenant's cases, provider credentials, or
  unrestricted logs.
- Tenant admins should be able to escalate cases to support when the issue is
  platform-only, billing-only, release-only, security-sensitive, or outside
  delegated controls.
- Support actions should be auditable when they change user access, feature
  access, sync behavior, tenant settings, report visibility, or case outcome.

Mobile behavior:

- Mobile should route users to tenant admin, support, billing, or platform
  guidance according to API context.
- Mobile diagnostics should be redacted and user-confirmed where privacy rules
  require it.
- Offline support drafts may be local, but submitted support state becomes
  trusted only after API acceptance.

## Cross-Tenant Isolation

Tenant admin scope must never affect other tenants.

Isolation principles:

- Tenant admins may list, search, invite, edit, report, support, notify, and
  configure only users and resources in their tenant.
- Tenant admins should not infer other tenant names, counts, emails, feature
  states, support cases, billing posture, or report data through validation
  errors, search results, exports, diagnostics, logs, or URLs.
- Tenant switching must revalidate role, membership, tenant state, permissions,
  feature flags, remote config, version policy, and support context.
- Direct URL access, Livewire action calls, API request parameters, local cache,
  stale bootstrap payloads, and mobile offline queues are not authority.
- Tenant admin actions should fail closed when tenant scope is missing,
  ambiguous, stale, suspended, archived, billing-blocked, deletion-requested,
  or unsupported by the user's permission.
- Support and platform roles may cross tenant boundaries only through
  documented, audited, job-scoped policy.

## Admin Safety For Tenant Admin Actions

Tenant admin actions are narrower than platform actions, but they can still be
dangerous inside one tenant.

Actions that should show impact before saving include:

- Inviting a user as tenant admin or tenant manager.
- Removing or suspending a tenant member.
- Changing a user role or permission preset.
- Enabling or disabling a delegated mobile feature.
- Changing a tenant setting that affects mobile behavior.
- Sending tenant-wide notifications or announcements.
- Exporting tenant reports.
- Escalating or closing support cases.
- Changing sync, storage, diagnostics, or permission-related settings where
  tenant delegation exists.

Risk controls:

- Confirm destructive, broad, or privacy-sensitive tenant admin actions.
- Record audit history for access, permission, feature, report, support,
  notification, export, and tenant-setting changes.
- Show mobile impact preview where a control affects mobile navigation,
  dashboard shortcuts, settings, permissions, offline queueing, sync status, or
  native permission prompts.
- Keep rollback or recovery paths visible where the action can be reversed.
- Require platform escalation for actions that exceed tenant authority.

## Offline And Stale-State Behavior

Tenant admin decisions can change while mobile users are offline.

Offline principles:

- Offline mobile state may show last-known tenant-admin-controlled visibility,
  but it must not create new authority.
- Offline mobile actions that depend on tenant admin decisions should be queued
  as pending intent only when the API previously allowed queueing.
- Sync replay must recheck tenant state, user state, feature state, permission
  state, version policy, subscription state, and conflict policy before the API
  accepts work.
- If a tenant admin removes access, disables a feature, changes a role, or
  suspends a user while the mobile user is offline, mobile should reconcile to
  denied, disabled, conflict, or support-required state when online.
- Mobile should label stale cached data instead of presenting it as current
  tenant authority.

## Acceptance Questions

Before implementing any tenant-admin feature, the documentation should answer:

- Which tenant-admin role or permission can see the control?
- Which tenant does the control affect?
- Which platform rule can override it?
- Which mobile screens, settings, dashboard shortcuts, native permissions,
  offline queues, reports, notifications, or support states change?
- Which API context, error, or sync outcome will mobile receive?
- What happens when the tenant is suspended, archived, billing-blocked,
  deletion-requested, in trial, in maintenance, or offline?
- What audit history is required?
- What support explanation is available?
- What privacy risk exists?
- What rollback or escalation path exists?

## Success Standard

Tenant admin logic is successful when a tenant admin can safely operate one
tenant without platform help for routine work, cannot affect another tenant,
cannot bypass platform-only controls, can invite and manage users inside
documented limits, can manage delegated mobile features with clear impact, can
view tenant reports without data leakage, can manage tenant support without
overreach, and mobile receives all tenant-admin effects through the API as
safe, tenant-scoped, auditable outcomes.
