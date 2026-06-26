# Role And Permission Logic

Final Optimized SaaS Blueprint is defined in `final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

Updated: 2026-06-26

This document defines role and permission logic for the Mobile Lara SaaS
system. It explains platform-level permissions, tenant-level permissions,
mobile-user permissions, admin-user permissions, how permissions affect API
access, how permissions affect mobile UI visibility, how permissions interact
with feature flags, and how suspended users and tenants should behave. It is
documentation only and does not define database structure, database fields,
migrations, seeders, routes, controllers, policies, gates, middleware,
Livewire components, NativePHP plugins, services, jobs, local storage schemas,
or application logic.

Use this document with [Target User Roles](user-roles.md), [Core Product
Principles](product-principles.md), [Two-System Boundary Logic](two-system-boundary.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [API-First Principles](api-first-principles.md),
[Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md),
[Authentication Principles](authentication-principles.md), [Mobile App Lock
Principles](mobile-app-lock-principles.md), [Audit Logic](audit-logic.md),
[Admin Control Center Logic](admin-control-center-logic.md), [Admin Safety
Principles](admin-safety-principles.md), and [SaaS Value Map](saas-value-map.md):
roles describe responsibility, permissions describe allowed actions, feature
flags describe product availability, and account/tenant state can deny access
even when a role or permission would otherwise allow it.

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

AI Feature Logic is defined in `ai-feature-logic.md`:
AI assistant purpose, summarization, categorization, smart suggestions,
moderation assistance, report generation assistance, admin AI controls,
tenant opt-in, privacy, and human-review principles must remain
tenant-enabled, plan-controlled, permission-aware, opt-in-only,
provider-neutral, human-reviewed, privacy-safe, audit-ready,
rate-limited, cost-aware, and Admin/API-authoritative.

Acceptance Principles are defined in `acceptance-principles.md`:
every feature must document purpose, admin control, mobile behavior,
API dependency, offline behavior, permission behavior, feature flag
behavior, tenant behavior, error behavior, security behavior, and
documentation requirements before implementation planning begins.

Risk Map is defined in `risk-map.md`:
API dependency, offline sync, tenant isolation, mobile secure storage,
NativePHP plugin availability, app store releases, forced updates,
feature flag mistakes, billing restrictions, admin misconfiguration,
support access, privacy, and data conflicts must document prevention
principles and documentation requirements before implementation.

Testing Strategy Principles are defined in `testing-strategy-principles.md`:
future tests for API contracts, admin controls, mobile feature visibility,
permissions, feature flags, remote config, authentication, tenant isolation,
offline sync, conflict behavior, native feature fallbacks, notification flows,
billing rules, and app version rules must map to documented authority, risk,
and user-visible behavior before implementation.

Release And Versioning Principles are defined in `release-versioning-principles.md`:
API versioning, mobile app versioning, admin releases, feature rollout,
rollback, app store release, forced update, documentation update, and Git
change-history decisions must preserve documented authority, compatibility,
rollback, support, audit, and user-visible behavior before release.

Documentation Audit is defined in `documentation-audit.md`:
project documentation for two-system architecture, Admin/API authority, mobile
client execution, API-first communication, feature flags, remote config,
tenancy, permissions, offline sync, NativePHP features, notifications, billing,
support, reports, security, risks, and release principles must use consistent
authority language and resolve contradictions before implementation.

Feature Dependency Map is defined in `feature-dependency-map.md`:
major features must document dependencies on authentication, tenant context,
permissions, feature flags, remote config, API availability, offline cache,
NativePHP permissions, subscription plan, and admin settings before
implementation planning or release decisions.

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

## Access Statement

Admin/API is the source of permission truth.

The mobile client may display role-derived capability state, hide unavailable
actions, show disabled explanations, request native permissions, cache
last-known capability summaries, and protect private state with app lock. It
must not invent permissions locally, grant tenant authority locally, override
server denials, replay offline actions without API acceptance, or treat UI
visibility as authorization.

Product rule: a user can perform an action only when identity, account state,
tenant state, role, permission, feature flag, resource scope, subscription
state, app-version policy, and API authorization all allow it.

## Roles Versus Permissions

Roles and permissions are related but not the same thing.

- **Roles** describe a person's product responsibility, such as platform owner,
  super admin, tenant admin, tenant manager, support agent, billing manager,
  mobile user, invited user, suspended user, or guest/pre-login user.
- **Permissions** describe concrete actions or visibility rights, such as
  viewing a report, managing tenant users, switching tenant context, opening a
  support case, changing billing settings, or submitting a mobile action.
- **Feature flags** describe whether a product capability is available in the
  current global, tenant, plan, role, user, app-version, device, cohort,
  maintenance, or emergency context.
- **Remote config** describes safe runtime behavior, presentation defaults,
  limits, timing, and mobile UX rules, but it must not create authority.
- **Account and tenant state** can remove access even when role, permission,
  and feature flag state would otherwise allow it.

Roles should be treated as permission presets or responsibility groups.
Permissions should be the smallest product-level decisions that can be safely
checked, explained, audited, and returned to mobile.

## Permission Resolution Principles

Permission resolution should be fail-closed and explainable.

The product decision order should be:

1. **Authentication state** - If the user is not authenticated, only guest,
   login, registration, invitation, recovery, legal, and safe public status
   flows may be available.
2. **Account state** - Suspended, invited, locked, revoked, deleted, or
   recovery-limited users receive restricted flows before normal role
   permissions are considered.
3. **Platform state** - Global maintenance, incident response, forced update,
   platform suspension, or emergency disablement may deny or limit access.
4. **Tenant state** - Tenant membership, tenant status, tenant suspension,
   tenant billing posture, tenant maintenance, and tenant isolation must be
   resolved before tenant data is exposed.
5. **Role responsibility** - Role determines the broad category of work the
   user may perform.
6. **Explicit permission** - Permission decides whether the role may perform
   the specific action in the current scope.
7. **Resource scope** - The action must still match the user's tenant,
   assignment, ownership, support case, billing scope, report scope, or mobile
   workflow scope.
8. **Feature flag and plan state** - The feature must be enabled and allowed by
   rollout, plan, version, maintenance, and safety rules.
9. **API authorization result** - The API returns the final allow, deny,
   hidden, disabled, read-only, update-required, maintenance, suspended, or
   support-required outcome.

If any required layer denies access, the action is denied. A later layer should
not re-grant access that an earlier safety or state layer removed.

## Platform-Level Permissions

Platform-level permissions apply across the SaaS control plane.

They should be rare, strongly scoped, and auditable because they can affect
many tenants or the whole product.

Platform-level permissions may cover:

- Managing platform-wide settings and operating policy.
- Creating, suspending, restoring, or retiring tenants.
- Assigning super admin or platform operator responsibility.
- Managing global feature defaults and emergency feature shutdowns.
- Managing global remote configuration defaults.
- Managing mobile app version rules, forced updates, and maintenance mode.
- Viewing platform health, aggregate reports, audit summaries, and incident
  state.
- Orchestrating notification policy, support operations, billing operations,
  and sync behavior across tenants.
- Reviewing cross-tenant diagnostics only where support, security, or
  operational policy allows it.

Platform permissions should not mean unlimited private tenant data access by
default. Even broad platform operators should see the least data required for
their job, with elevated tenant-data access requiring clear reason, impact
preview, and audit history.

## Tenant-Level Permissions

Tenant-level permissions apply inside one tenant boundary.

They should never cross tenant boundaries unless the Admin/API system returns a
specific audited platform or support reason.

Tenant-level permissions may cover:

- Managing tenant users, invitations, roles, and delegated access.
- Viewing tenant settings, reports, notifications, support cases, sync status,
  devices, and feature availability.
- Managing tenant-specific remote config and feature overrides where platform
  policy allows it.
- Controlling tenant mobile workflows, enabled modules, operational reports,
  and workflow assignments.
- Managing tenant support visibility and tenant-safe diagnostics.
- Viewing tenant billing or entitlement summaries only when the role and plan
  scope allow it.
- Taking tenant-local recovery actions that do not bypass platform safety,
  billing, app-version, or security policy.

Tenant permissions should be constrained by tenant status, subscription state,
feature flags, maintenance policy, and resource scope. A tenant admin should be
powerful inside the tenant, but not more powerful than platform policy.

## Admin-User Permissions

Admin-user permissions govern control-plane access.

Admin users include platform owner, super admin, tenant admin, tenant manager,
support agent, and billing manager. Each admin user should see only the admin
surfaces that match their role, tenant scope, job scope, and permission set.

Admin-user permissions should cover:

- Which admin panels, dashboards, reports, tables, forms, support views, and
  billing views are visible.
- Which records or tenants can be listed, searched, opened, exported, changed,
  suspended, restored, or deleted.
- Which dangerous actions require confirmation, impact preview, and audit
  history.
- Which mobile-facing effects the admin can create, such as enabling a feature,
  disabling a feature, changing remote config, forcing update, putting a tenant
  in maintenance, or suspending a user.
- Which support diagnostics can be viewed without exposing secrets, private
  payloads, or unrelated tenant data.

Admin UI visibility is helpful, but server-side authorization remains final.
Hidden admin controls reduce mistakes; they do not protect the system by
themselves.

## Mobile-User Permissions

Mobile-user permissions govern what the NativePHP client may show and what the
API may accept from a mobile session.

Mobile permissions should cover:

- Which mobile navigation items, dashboard shortcuts, settings sections,
  records, notifications, announcements, and quick actions are visible.
- Which mobile actions can be performed online.
- Which mobile actions can be drafted offline.
- Which mobile actions can be queued for later sync.
- Which cached data can be viewed while offline.
- Which native capabilities may be requested, such as camera, microphone,
  location, files, scanner, notifications, biometrics, and secure storage.
- Which sensitive areas require app lock or step-up confirmation.
- Which support, diagnostics, storage, security, and sync controls are visible
  in mobile settings.

Mobile permissions must be received through the API as mobile-safe capability
state. The mobile client may cache the last-known state for offline UX, but
cached permission state is not authority to sync protected writes after the API
denies or revokes access.

## Permissions And API Access

API access must be permission-aware on every protected operation.

Principles:

- Authentication proves who the caller claims to be; authorization decides what
  that caller may do.
- Every protected API behavior should evaluate tenant, account, role,
  permission, feature, version, maintenance, billing, support, and resource
  scope before returning protected data or accepting protected changes.
- API responses should distinguish unauthenticated, unauthorized, hidden,
  suspended, tenant-unavailable, feature-disabled, plan-limited, update-required,
  maintenance, conflict, validation, and rate-limited outcomes where the mobile
  UX needs different handling.
- The API may hide the existence of resources when exposing them would leak
  cross-tenant or private information.
- The API should return mobile-safe permission context during bootstrap and
  refresh flows so the mobile client can render predictable UI.
- The API should return enough denial reason for safe UX, support, and admin
  troubleshooting without exposing private data or implementation details.
- Offline replay must re-check permissions at the API boundary. A queued action
  created while the user had permission may still be denied later.

API authorization is the enforcement point. UI visibility, local cache, app
lock, and NativePHP device state are supporting signals only.

## Permissions And Mobile UI Visibility

Mobile UI visibility should make the allowed path obvious without becoming the
security boundary.

Principles:

- Mobile should show only navigation, shortcuts, actions, settings, and records
  that the API says are visible or usable in the current context.
- Mobile should use clear states: hidden, disabled, read-only, offline-limited,
  update-required, maintenance-limited, plan-limited, permission-blocked, or
  support-required.
- Disabled states should explain the next safe action when useful: contact
  admin, update app, reconnect, switch tenant, wait for rollout, resolve
  billing, or contact support.
- Mobile should not show admin-only concepts to normal mobile users unless the
  explanation is necessary for support or recovery.
- Mobile should not request native permissions for features hidden or disabled
  by Admin/API.
- Mobile should refresh capability state after login, tenant switch, bootstrap,
  app resume where required, session refresh, remote config change, feature
  flag change, and sync-relevant permission changes.
- Mobile should not treat a visible button as permission to act; the API must
  still decide when the action is submitted.

The best mobile permission UX is simple: users see what they can do, understand
why something is unavailable, and never need to reason about internal role
logic.

## Permissions And Feature Flags

Permissions and feature flags answer different questions.

| Decision | Question | Owner | Product meaning |
| --- | --- | --- | --- |
| Permission | May this user perform this action in this scope? | Admin/API authorization. | Access control. |
| Feature flag | Is this product capability available in this context? | Admin/API feature control. | Product availability and rollout. |
| Plan limit | Is the tenant entitled to this capability? | Billing/subscription authority. | Commercial access. |
| Remote config | How should allowed behavior be tuned or presented? | Admin/API configuration authority. | Runtime behavior. |

Principles:

- A feature flag cannot grant access without permission.
- A permission cannot make a disabled feature available.
- A plan entitlement cannot bypass role or permission limits.
- Remote config cannot create permission authority.
- Emergency disablement, maintenance, forced update, tenant suspension, or user
  suspension can override both permission and feature availability.
- Admins should see the impact of enabling or disabling a feature, including
  which roles, tenants, mobile screens, API behaviors, support queues, reports,
  billing outcomes, and offline queues may change.
- Mobile should receive the resolved result, not a pile of conflicting raw
  decisions that it must interpret as authority.

The safest rule is "permission AND feature availability are both required."

## Suspended Users

Suspended users should fail closed.

Suspension may be caused by security, billing, tenant policy, support review,
abuse, account recovery, invitation problems, or platform action. Regardless of
cause, suspended state should override normal role and permission access.

Suspended user principles:

- Normal admin, API, and mobile access should be blocked.
- Existing tokens, sessions, local authenticated presentation, cached permission
  state, and queued mobile actions should not continue as authority after
  suspension is known.
- Mobile may show a minimal suspended state, support/recovery guidance, logout,
  and safe legal or account messages.
- Admin/API may expose support-safe diagnostics to authorized support or tenant
  admins according to policy.
- Offline cached data should be hidden or limited after suspension is known.
- Queued offline actions should stop replaying until the API explicitly accepts
  the user again.
- Suspension should be auditable, reversible where policy allows, and clear
  about mobile impact before saving.

Suspended users are not guests. They may need targeted recovery or support
flows, but they do not receive normal tenant or mobile permissions.

## Suspended Tenants

Suspended tenants should also fail closed.

Tenant suspension may be caused by billing failure, compliance review, abuse,
platform risk, support escalation, security incident, contract cancellation, or
maintenance policy.

Suspended tenant principles:

- Normal tenant admin and mobile workflows should be blocked or narrowed to
  recovery/support/billing flows.
- Tenant users should not access normal tenant data, reports, notifications,
  sync actions, or feature workflows through stale mobile state.
- Platform owner, super admin, billing manager, or support agent may see
  tenant-safe operational context only according to their job scope.
- Tenant suspension should not automatically delete tenant data.
- Local mobile caches for that tenant should be hidden, locked, quarantined, or
  cleared according to documented offline and security policy.
- Queued tenant actions should not replay while the tenant is suspended.
- Admins should see the impact on mobile users, notifications, offline queues,
  billing, support, and reports before suspension takes effect.
- Restoration should re-check permissions, features, config, app version,
  billing, and sync queues before normal access resumes.

Suspended tenant state outranks tenant-level role permissions.

## Denial And Visibility States

Access denial should be safe, consistent, and mobile-friendly.

Recommended product meanings:

| State | Meaning | Mobile/Admin behavior |
| --- | --- | --- |
| Unauthenticated | No valid session is present. | Show login, invitation, recovery, or public status. |
| Unauthorized | Session exists but permission is missing. | Hide or disable action; API rejects protected operation. |
| Hidden | Exposing existence would leak data. | Do not reveal the resource; use generic not-found style messaging. |
| Suspended user | User account cannot use normal flows. | Show minimal recovery/support/logout state. |
| Suspended tenant | Tenant cannot use normal flows. | Show tenant unavailable, support, billing, or recovery state. |
| Feature disabled | Product capability is unavailable. | Hide or explain feature according to feature flag logic. |
| Plan limited | Tenant is not entitled to the feature. | Explain billing/admin path without granting access. |
| Read-only | Viewing is allowed but changes are blocked. | Disable writes and explain why. |
| Offline limited | Last-known state allows limited local work only. | Permit safe cache/draft behavior; re-check at sync. |
| Update required | App version cannot safely perform action. | Block action and guide update. |
| Maintenance | Platform, tenant, feature, API, or sync area is limited. | Show maintenance state and safe next action. |

The exact implementation may map these states to HTTP responses, API envelope
codes, Livewire states, or mobile shell states later. This document defines the
product meaning, not endpoint details.

## Admin Control Principles

Admins who manage roles and permissions need safety rails.

Principles:

- Role and permission changes should show mobile impact before saving.
- Dangerous permission changes should require confirmation.
- Broad platform permissions should be rare and clearly labelled.
- Tenant-specific permission changes should stay tenant-isolated.
- Granting support or billing access should not silently grant unrelated
  private data access.
- Permission changes should be auditable.
- Rollback should be considered before changing high-impact access.
- Suspensions and restorations should show effect on API access, mobile UI,
  offline queues, notifications, reports, billing, and support.
- Admins should understand whether a user is blocked by role, permission,
  feature flag, plan, tenant state, app version, maintenance, or suspension.

Admin tools should make access decisions easier to understand, not easier to
bypass.

## Offline And Sync Principles

Offline behavior must respect permissions without pretending stale state is
fresh authority.

Principles:

- Mobile may use last-known permissions to shape offline UX.
- Offline permission state should be labelled as last-known when relevant.
- Offline drafts and queues should record the intended action context, but the
  API must re-check permission and feature availability before accepting them.
- If permission is removed while mobile is offline, queued actions may be
  denied during sync.
- If a user or tenant is suspended while mobile is offline, the next successful
  API contact should move the mobile client into the appropriate suspended,
  logged-out, locked, support, or recovery state.
- Offline cache should be tenant-separated and protected by app lock.
- Offline visibility should prefer hiding sensitive data when permission,
  tenant state, or session authority is uncertain.

Offline-first means useful local continuity, not local authorization authority.

## Risks

Role and permission logic has product and security risks:

- Role names can become too broad and hide excessive access.
- UI-only hiding can create false confidence.
- Feature flags can be mistaken for permissions.
- Tenant admins can accidentally be given platform-like power.
- Support and billing users can receive more private data than their job needs.
- Suspended users can continue through stale mobile cache if fail-closed rules
  are unclear.
- Suspended tenants can leave queued offline actions in confusing states.
- Denial messaging can leak resource existence or tenant membership.
- Cross-tenant reports can accidentally bypass tenant isolation.

Mitigation principles:

- Keep API authorization final.
- Keep mobile permission state derived from API.
- Treat roles as presets and permissions as explicit actions.
- Require both permission and enabled feature state.
- Deny suspended users and tenants before normal role evaluation.
- Use least privilege for support and billing.
- Audit dangerous permission changes.
- Document mobile impact before implementing any role or permission behavior.

## Acceptance Questions

Before role and permission behavior is implemented, the product decision should
answer:

- Which role owns this responsibility?
- Which permission allows the action?
- Is the permission platform-scoped, tenant-scoped, job-scoped, or mobile-only?
- Which account states override the permission?
- Which tenant states override the permission?
- Which feature flags or plan limits also need to allow the action?
- What does the API return when access is denied?
- What does mobile hide, disable, or explain?
- What happens offline before sync?
- What happens if permission is removed while a mobile action is queued?
- What happens if the user is suspended?
- What happens if the tenant is suspended?
- What should support see?
- What should billing see?
- What should be audited?
- What requires admin confirmation or impact preview?

If these questions are not answered, the role or permission behavior is not
ready for implementation.
