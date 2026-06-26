# Target User Roles

Updated: 2026-06-25

This document defines the main logical user roles for Mobile Lara. It describes responsibilities, limitations, and what each role should be able to see or control. It is documentation only and does not define database fields, migrations, seeders, policies, permissions, controllers, Livewire components, or application logic.

## Role Model Principles

Roles are product boundaries, not only labels.

- Roles must support [Product Vision](product-vision.md): admin users operate
  the SaaS control plane, while mobile users receive a simple governed app
  experience.
- Roles must support [Product Positioning](product-positioning.md): each role
  should reinforce the split between SaaS control center authority and mobile
  workforce/client execution.
- Roles must support [Core Product Principles](product-principles.md): role
  boundaries enforce admin authority, tenant isolation, secure defaults,
  API-first mobile behavior, and simple mobile UX.
- Role and permission decisions must follow [Documentation-First Architecture](documentation-first-architecture.md): every permission documents who controls it, who can use it, what it exposes, how mobile receives it, and which risk or audit expectation applies.
- Role and permission decisions must follow [Admin Control Center Logic](admin-control-center-logic.md): every role-sensitive control has a named scope, mobile effect, API context, audit expectation, support meaning, and offline behavior.
- Role and permission decisions must follow [Feature Flag Logic](feature-flag-logic.md): role and user-level feature access can refine availability only inside global, tenant, plan, version, permission, and safety boundaries.
- Role and permission decisions must follow [Remote Configuration Logic](remote-configuration-logic.md): role or user presentation config can change safe UX behavior but cannot grant authority, billing access, tenant access, or permissions.
- Role and permission decisions must follow [Mobile Version Control Logic](mobile-version-control-logic.md): support, release, internal, invited, suspended, and guest states may affect version visibility, update prompts, maintenance access, and blocked old-version behavior without bypassing API authority.
- Role and permission decisions must follow [Admin Safety Principles](admin-safety-principles.md): dangerous role, permission, support, billing, report, export, suspension, and recovery actions require confirmation, audit history, impact preview, rollback thinking, and tenant-isolated scope.
- Role and permission decisions must follow [Mobile UX Principles](mobile-ux-principles.md): mobile, invited, suspended, guest, support-assisted, and billing-limited users should see simple navigation, clear blocked/offline/loading states, secure session behavior, and native permission education that matches their account state.
- Every role must be enforced by the Admin/API system.
- The mobile client must receive role-derived capability state through the API.
- Roles should follow least privilege.
- Tenant-scoped roles must never cross tenant boundaries.
- Suspended and invited states restrict access even if the person would otherwise have a role.
- Support and billing roles can see operational context only for their job.
- UI visibility is never authorization; server-side policy remains final.

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

Roles define authority and visibility. The [SaaS Value Map](saas-value-map.md) defines product value. A stakeholder may receive value from a feature without receiving direct control over it; for example, a tenant business benefits from reports and offline sync, while the tenant admin or support team may be the role that actually sees the management surface.

Roles also depend on [Two-System Boundary Logic](two-system-boundary.md). Admin/API enforces role authority, while mobile only renders API-derived capability state and account-state restrictions.

Roles also depend on [API-First Principles](api-first-principles.md). Role and account-state behavior must reach mobile through predictable API context, permissions, feature state, errors, and tenant-scoped responses.

Roles also depend on [Admin/API Responsibilities](admin-api-responsibilities.md). Responsibility ownership explains which control-plane areas each role may operate, observe, or consume as API-derived mobile state.

Roles also depend on [Mobile Client Responsibilities](mobile-client-responsibilities.md). Mobile responsibilities explain how mobile users, invited users, suspended users, and guest/pre-login users experience local UX, session, cache, drafts, sync state, device permissions, and feature visibility without gaining authority.

## Role Summary

| Role | Scope | Primary purpose |
| --- | --- | --- |
| Platform owner | Whole SaaS | Owns business, strategy, highest-level platform control, and ultimate accountability. |
| Super admin | Whole SaaS | Operates the platform with broad administrative authority under owner policy. |
| Tenant admin | One tenant | Controls tenant users, settings, enabled modules, and tenant operations. |
| Tenant manager | One tenant or tenant unit | Manages day-to-day workflows and teams inside configured limits. |
| Support agent | Platform support scope | Helps users and tenants using safe diagnostics and support tools. |
| Billing manager | Platform or tenant billing scope | Manages plans, invoices, billing state, quotas, and entitlement context. |
| Mobile user | Tenant/mobile scope | Performs allowed work in the NativePHP mobile client. |
| Invited user | Pending tenant/user scope | Has an invitation but has not completed activation. |
| Suspended user | Previously valid scope | Is blocked from normal access until reactivated. |
| Guest/pre-login user | Public/pre-auth scope | Can access only public or authentication-related flows. |

## Role Authority Contract

| Role | Responsibilities | Limitations | Should see or control |
| --- | --- | --- | --- |
| Platform owner | Owns product direction, commercial model, operating policy, risk tolerance, and highest-accountability decisions. | Should not perform routine support, bypass audit, or use owner authority as tenant-scoped access. | Platform health, strategic controls, global policy, risk dashboards, feature strategy, billing posture, audit summaries, and super admin assignment. |
| Super admin | Operates the SaaS control center across tenants, features, versions, support, reports, sync, and emergency response. | Must remain auditable, should not override owner-only business decisions, and should not access unnecessary private tenant payloads. | Platform operations, tenant administration, feature rollout, app-version policy, support queues, sync health, and safe tenant-level settings. |
| Tenant admin | Manages one tenant's users, roles, settings, modules, notifications, reports, support, and local governance. | Cannot cross tenant boundaries, override platform policy, bypass billing/plan limits, or treat UI visibility as authorization. | Own-tenant users, invitations, roles, devices, feature state, reports, support cases, notification settings, and sync/conflict status. |
| Tenant manager | Runs delegated day-to-day workflows, assigned teams, work queues, operational reports, and routine issue handling. | Cannot manage billing, global tenant settings, high-risk controls, or users/data outside assigned scope. | Assigned workflows, assigned users/teams, operational status, limited reports, relevant sync/support context, and delegated user management. |
| Support agent | Triages tickets, reviews safe diagnostics, explains state, guides recovery, and escalates incidents. | Cannot silently change tenant policy, billing, feature flags, permissions, secrets, or unrelated tenant data. | Support queue, ticket timelines, safe tenant/user/device/version/config/sync diagnostics, escalation state, and support-safe retry/recovery actions. |
| Billing manager | Manages commercial access, plans, invoices, quotas, payment status, billing contacts, and entitlement outcomes. | Cannot own operational workflows, security policy, tenant isolation, app-version policy, or private mobile content by default. | Billing state, plan/quota/invoice data, entitlement summaries, billing audit trail, and feature availability as billing outcome. |
| Mobile user | Performs allowed work in the NativePHP mobile client and responds to visible offline/sync/conflict states. | Cannot access admin controls, grant permissions, choose tenant authority, change billing/feature/version policy, or make local state server truth. | Allowed mobile workflows, own profile/session/device state, sync/offline/pending/conflict state, disabled-feature explanations, and own support actions. |
| Invited user | Accepts or declines an invitation and completes activation, verification, consent, and onboarding steps. | Cannot use normal admin/mobile workflows, change invitation scope, or access tenant data before activation. | Safe invitation details, registration/login/verification steps, expiration state, and next activation action. |
| Suspended user | Uses allowed recovery or support paths to resolve account, security, billing, tenant, or policy issues. | Cannot access normal admin/API/mobile workflows, sync new trusted writes, or use cached UI as authority. | Minimal suspension notice, logout/session cleanup, recovery/contact actions, and support-safe next steps. |
| Guest/pre-login user | Uses public, login, registration, password reset, invitation acceptance, verification, and legal/support entry flows. | Cannot access tenant data, authenticated API responses, device trust state, reports, billing, support history, or feature-enabled app behavior. | Public status, login/register/reset/verify/invitation/legal flows, and safe pre-login support contact information. |

## Role Decision Rules

Role decisions should be resolved as product authority before any screen, endpoint, report, notification, sync action, or support workflow is designed.

- Account state overrides normal role access: suspended users, invited users, and guest/pre-login users receive restricted flows even when a future or previous role exists.
- Tenant scope overrides convenience: tenant admins, tenant managers, mobile users, support agents, and billing managers may only see cross-tenant data when the Admin/API system returns an explicit audited reason.
- Platform scope is exceptional: platform owner and super admin abilities should be visible, logged, reversible where possible, and separated from ordinary tenant work.
- Mobile visibility is an outcome, not a permission source: the NativePHP client displays role-derived capabilities from the API and must not create local authority.
- Support and billing access are job-scoped: those roles see enough context to solve the support or billing problem without becoming broad tenant administrators.

## Platform Owner

The platform owner is the highest business authority for the SaaS.

Responsibilities:

- Define product direction, commercial model, platform-level policies, and operating principles.
- Appoint or remove super admins.
- Approve dangerous global controls such as platform-wide maintenance, global feature kill switches, production billing provider changes, and data-retention policy changes.
- Own legal, compliance, privacy, and business-risk accountability.

Limitations:

- Should not perform routine tenant support or day-to-day tenant management.
- Should not bypass audit, approval, or reason-capture for sensitive changes.
- Should not use owner authority as a substitute for tenant-scoped roles.

Should see/control:

- Global tenant list and platform health.
- Global feature flags, app-version policy, billing posture, risk dashboards, and audit summaries.
- Super admin assignments and highest-risk platform settings.
- Cross-tenant reports in aggregated or authorized operational form.

Should not see/control by default:

- Tenant private content unless a policy, legal, security, or support reason requires it.
- Mobile user local-only data that has not been synced or submitted.

## Super Admin

The super admin is the highest operational role inside the Admin/API system.

Responsibilities:

- Operate the SaaS control center.
- Manage tenants, tenant admins, support visibility, app-version policy, remote config, feature flags, notifications, reports, and sync policy.
- Investigate platform incidents and coordinate rollbacks.
- Apply emergency controls when product safety or availability requires it.

Limitations:

- Must remain auditable for sensitive changes.
- Should not own business/legal decisions reserved for the platform owner.
- Should not directly perform tenant business workflows unless explicitly acting inside a tenant-scoped role.
- Should not access billing secrets, payment credentials, or private tenant content unless policy allows it.

Should see/control:

- Platform-wide configuration and operational dashboards.
- Tenants, users, devices, app versions, feature rollout state, support queues, and sync health.
- Tenant-level settings when needed for administration or support.

Should not see/control by default:

- Payment provider secrets.
- Unnecessary private tenant payloads.
- Mobile-local unsynced drafts.

## Tenant Admin

The tenant admin is the highest authority inside a tenant.

Responsibilities:

- Manage tenant users, invitations, roles, teams, and access.
- Configure tenant-level settings, enabled modules, notifications, and operational preferences within the plan.
- Review tenant reports, support cases, sync/conflict state, and device/user access for the tenant.
- Coordinate tenant onboarding and internal governance.

Limitations:

- Cannot access other tenants.
- Cannot override platform-owner or super-admin global policy.
- Cannot enable features not allowed by tenant plan, app-version policy, or platform rollout.
- Cannot bypass API authorization by changing mobile UI state.

Should see/control:

- Tenant dashboard, tenant users, tenant devices, tenant feature state, tenant reports, tenant support cases, tenant notification settings, and tenant sync/conflict status.
- Role assignment inside tenant-defined limits.
- Invitations and suspensions inside the tenant.

Should not see/control:

- Platform-wide settings.
- Other tenants.
- Billing provider internals unless also granted billing scope.
- Mobile-local unsynced drafts except where submitted through support diagnostics.

## Tenant Manager

The tenant manager handles day-to-day operations inside the tenant.

Responsibilities:

- Manage assigned teams, mobile users, work queues, records, reports, and operational status.
- Monitor workflow completion, conflicts, pending sync, and user/device readiness for their area.
- Help mobile users resolve routine operational issues.

Limitations:

- Cannot manage tenant billing, global tenant settings, platform policy, or high-risk feature controls.
- Cannot grant permissions beyond their own scope.
- Cannot access teams, records, users, or reports outside their assigned tenant/unit scope.
- Cannot reactivate suspended users unless tenant policy grants that ability.

Should see/control:

- Assigned tenant workflows, users, teams, operational reports, and relevant support/sync context.
- Feature state as applied to their team or workflows.
- Limited user management where delegated by tenant admin.

Should not see/control:

- Tenant-wide security settings unless delegated.
- Billing state beyond visible entitlement impact.
- Platform settings or other tenant data.

## Support Agent

The support agent helps platform and tenant users solve problems.

Responsibilities:

- Triage support requests.
- View safe diagnostic context: tenant, user, device, app version, feature flags, remote config version, sync status, and recent non-sensitive errors.
- Guide users through retries, config refreshes, version updates, and escalation paths.
- Escalate bugs, billing issues, security issues, and platform incidents.

Limitations:

- Cannot silently change tenant policy, billing, feature flags, or permissions unless explicitly granted.
- Cannot view secrets, payment credentials, private files, or mobile-local unsynced drafts by default.
- Cannot impersonate users without explicit audited policy.
- Must not use support access for tenant administration.

Should see/control:

- Support queue, ticket timeline, safe diagnostics, tenant context, app version, device state, sync/conflict summaries, and recent relevant admin/config changes.
- Support actions such as request logs, ask user to retry, mark ticket status, escalate, or trigger safe config refresh if policy allows.

Should not see/control:

- Broad tenant data unrelated to the support case.
- Payment secrets or private sensitive payloads.
- Global platform settings.

## Billing Manager

The billing manager handles commercial access and entitlement state.

Responsibilities:

- Manage plans, invoices, payment status, quotas, billing contacts, and entitlement outcomes.
- Coordinate failed payment restrictions, upgrades, downgrades, renewals, and account limitations.
- Explain billing-driven feature availability to tenant admins and support agents.

Limitations:

- Should not manage operational tenant workflows unless separately granted.
- Should not view mobile content or private tenant data unless needed for billing support and allowed by policy.
- Cannot override security, tenant isolation, or app-version policy.
- Cannot change payment provider credentials unless platform owner/super admin policy allows it.

Should see/control:

- Tenant billing state, plan, quotas, invoices, payment status, entitlement summary, and billing-related audit trail.
- Feature availability only as billing/entitlement outcome.

Should not see/control:

- Tenant operational records unrelated to billing.
- Mobile-local data.
- Platform security controls outside billing scope.

## Mobile User

The mobile user performs work in the NativePHP mobile client.

Responsibilities:

- Use mobile workflows allowed by tenant role, feature flags, app version, device policy, and offline policy.
- Complete assigned tasks, records, media capture, check-ins, notifications, or other enabled workflows.
- Resolve visible pending, failed, or conflict states when prompted.
- Request support when local or sync state blocks progress.

Limitations:

- Cannot access admin controls.
- Cannot choose their own tenant authority, permission state, billing state, feature flags, or app-version policy.
- Cannot make server-trusted changes without API confirmation.
- Cannot treat offline local state as final business truth.

Should see/control:

- Only mobile screens and actions allowed by server-provided policy.
- Their own profile/session/device state as allowed.
- Sync status, offline status, pending actions, conflicts, and feature-disabled explanations.

Should not see/control:

- Admin settings.
- Billing internals.
- Other users' private data unless a workflow grants it.
- Raw feature flag/config internals.

## Invited User

The invited user has been invited but has not completed activation.

Responsibilities:

- Accept or decline invitation.
- Complete required registration, verification, consent, and onboarding steps.
- Join only the tenant and role scope granted by the invitation.

Limitations:

- Cannot use normal admin or mobile workflows until activation is complete.
- Cannot change invitation scope.
- Cannot access tenant data before required verification/acceptance.
- Invitation may expire, be revoked, or be replaced.

Should see/control:

- Invitation details safe for pre-activation display: tenant name, inviter, role summary, expiration, and next step.
- Registration/login/verification flows.

Should not see/control:

- Tenant data, admin dashboards, mobile workflows, billing state, or support data before activation.

## Suspended User

The suspended user was previously valid but is blocked from normal access.

Responsibilities:

- Resolve account, security, billing, policy, or tenant-admin issue through the allowed recovery/support path.
- Stop using mobile/admin workflows until reactivated.

Limitations:

- Cannot access normal admin, API, or mobile workflows.
- Cannot sync new writes as trusted actions.
- Cannot accept new invitations into the same blocked context unless policy allows.
- Cannot use cached mobile UI as authority after suspension.

Should see/control:

- Minimal suspension notice.
- Allowed support/contact/recovery actions.
- Logout/session cleanup actions.

Should not see/control:

- Tenant data, mobile work queues, admin controls, billing management, reports, or support internals.

## Guest / Pre-Login User

The guest/pre-login user has no authenticated product identity yet.

Responsibilities:

- Register, log in, verify email or phone where required, reset password, accept invitation, or read public/legal information.
- Decide whether to continue into authenticated product flows.

Limitations:

- Cannot access tenant data, admin controls, mobile workflows, device trust state, reports, support case history, billing state, or feature-enabled app behavior.
- Cannot call authenticated API endpoints.
- Cannot rely on local mobile state as an authenticated session.

Should see/control:

- Login, registration, password reset, invitation acceptance, email verification, legal/privacy/support contact pages, and safe public status messages.

Should not see/control:

- Tenant-specific data or capabilities.
- Authenticated API responses.
- Feature flags beyond safe public/pre-login copy.

## Visibility And Control Matrix

| Area | Platform owner | Super admin | Tenant admin | Tenant manager | Support agent | Billing manager | Mobile user | Invited user | Suspended user | Guest/pre-login |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| Global platform policy | Control | Operate | No | No | View only if needed | No | No | No | No | No |
| Tenant settings | View/control by policy | View/control by policy | Control own tenant | Limited | View for cases | Billing-related only | No | No | No | No |
| Users and roles | Control high-level | Control | Control own tenant | Limited | View for cases | Billing contacts only | Own profile | Invitation only | Recovery only | No |
| Billing and entitlements | Control policy | Operate by grant | View tenant outcome | View impact only | View support-safe outcome | Control billing scope | View allowed/blocked state | No | Recovery notice only | No |
| Feature flags/config | Control policy | Control | Tenant-scoped control | View/use allowed | View for cases | Entitlement impact | Render allowed state | No | No | No |
| App version/device policy | Control policy | Control | View/control own tenant by policy | View assigned scope | View for cases | No | View own device/update state | No | No | No |
| Support cases | Oversight | Oversight/control | Own tenant | Assigned scope | Control support workflow | Billing cases | Own cases | Invitation help only | Recovery case only | Public contact only |
| Reports | Global/aggregate | Global/tenant | Own tenant | Assigned scope | Case-related | Billing reports | Personal/task state only | No | No | No |
| Mobile workflows | No direct use by default | No direct use by default | Configure | Monitor/manage | Support context | Entitlement context | Use allowed workflows | No | No | No |
| Offline queue/sync | Oversight | Oversight/control policy | Own tenant view | Assigned scope | Case diagnostics | Entitlement impact | Own visible state | No | No trusted replay | No |
| API context | Policy context | Operate context | Tenant context | Assigned context | Case context | Billing context | User/mobile context | Activation context | Recovery context | Public/pre-login context |
| Admin/API responsibilities | Own/control by policy | Operate by grant | Tenant-scoped control | Assigned scope | Case/support view | Billing scope | API outcome only | Activation outcome only | Recovery outcome only | Public/pre-login outcome only |
| Mobile client responsibilities | No direct use by default | No direct use by default | Configure/observe outcomes | Observe assigned outcomes | Support-safe diagnostics | Entitlement outcome | Use local UX | Activation UX only | Recovery UX only | Public/pre-login UX only |

## Role Value Alignment

The value map should be used with this role model before feature planning:

| Value stakeholder | Closest role surface | Value boundary |
| --- | --- | --- |
| Platform owner | Platform owner and super admin | Strategic value and operational value are related, but owner accountability should not bypass super-admin audit controls. |
| Tenant business | Tenant admin and tenant manager | The business benefits from governed mobile operations, while tenant roles receive only the controls their job requires. |
| Tenant admin | Tenant admin | Tenant value is tenant-scoped and cannot override platform, plan, app-version, or security policy. |
| Mobile worker/client | Mobile user plus invited/suspended/pre-login states | Mobile value is simple allowed work, not access to admin machinery. |
| Support team | Support agent | Support value is safe diagnostics and case resolution, not broad tenant administration. |
| Billing/operations team | Billing manager | Billing value is entitlement and operational control, not tenant workflow ownership. |

## State Rules

Some entries are account states rather than full roles:

- Invited user is a pending state before activation.
- Suspended user is a blocked state after activation.
- Guest/pre-login user is unauthenticated state.

State restrictions override role permissions. For example, a tenant admin who is suspended should not keep tenant-admin access, and an invited support agent should not see support cases before activation.

## Boundaries

This role model does not implement:

- Role database records.
- Permission enums.
- Policies.
- Guards.
- Middleware.
- Invitations.
- Suspension logic.
- Tenant membership tables.
- Admin screens.
- Mobile screens.
- API endpoints.

Those belong in future implementation prompts with tests, migrations, policies, and acceptance criteria.

## Risks

| Risk | Product response |
| --- | --- |
| Platform owner and super admin blur together | Keep owner as business/accountability role and super admin as operational role. |
| Tenant manager becomes hidden tenant admin | Limit manager scope to assigned teams/workflows and avoid tenant-wide settings by default. |
| Permission ownership is undocumented | Use Documentation-First Architecture so every permission states who controls it, how mobile receives it, and what risk/audit expectations apply. |
| Support sees too much private data | Support gets safe diagnostics and case-scoped context, not broad tenant access. |
| Billing manager changes operational access | Billing controls entitlements and invoices, not day-to-day workflows. |
| Stakeholder value is confused with role authority | Use the SaaS value map to identify who benefits and this role model to decide who can see or control. |
| API context leaks role or tenant data | Use API-first principles so user, permission, feature, config, version, and tenant context are predictable and scoped. |
| Mobile infers role authority locally | Use the two-system boundary: mobile may cache role-derived capability state, but Admin/API must enforce final access. |
| Role grants do not map to responsibility ownership | Use Admin/API responsibilities to decide which control-plane area a role may operate, observe, or consume. |
| Mobile states become role authority | Use mobile-client responsibilities to keep UX, cache, drafts, sync, permissions prompts, and feature visibility as API-derived presentation. |
| Mobile user gains authority offline | Offline actions remain intents until API acceptance. |
| Invited or suspended states leak access | State restrictions override role permissions. |
| UI hiding becomes authorization | API and policies remain final authority. |

## Success Test

The role model is successful when each person can see and control only what their job requires, every stakeholder value is delivered through the right role surface, every sensitive action is server-authorized and auditable, tenant boundaries remain clear, and mobile capability state is derived from API-controlled role and account state.
