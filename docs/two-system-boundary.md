# Two-System Boundary Logic

Updated: 2026-06-25

This document defines the logical boundary between the two Mobile Lara systems:

1. **Admin/API system** - Laravel API plus Livewire admin panel.
2. **Mobile client system** - Laravel plus Livewire running inside NativePHP Mobile.

It explains ownership, API-only behavior, local caching, remote admin control, offline behavior, risks, and product boundaries. It is documentation only and does not define database fields, migrations, controllers, components, policies, jobs, services, or application logic.

## Boundary Statement

The Admin/API system is the source of SaaS authority. The mobile client is a managed execution surface.

Use this document with [Product Vision](product-vision.md): the boundary exists
because the product needs both central SaaS authority and resilient mobile
execution.

Use this document with [Product Positioning](product-positioning.md): the
boundary is what keeps the SaaS control center and mobile workforce/client
platform coordinated without collapsing into web-only or mobile-only design.

Use this document with [Core Product Principles](product-principles.md): the
boundary enforces admin control, API-only mobile behavior, tenant isolation,
secure defaults, useful offline behavior, simple mobile UX, documentation-first
planning, and modular expansion.

Use this document with [Target User Roles](user-roles.md): role authority belongs
to Admin/API, while mobile renders only API-derived capability and account-state
outcomes.

Use this document with [SaaS Value Map](saas-value-map.md): stakeholder value
belongs in the correct system boundary, with Admin/API owning control and mobile
presenting local execution value.

The simplest rule is:

- **Admin/API decides** what is allowed, configured, billable, reportable, auditable, supported, secure, and synced.
- **Mobile executes and explains** those decisions through a simple NativePHP app, local cache, local drafts, queued intents, native device capabilities, and clear user-facing state.

The mobile client may remember server decisions for usability, but it must never become the place where business authority is created.

## Boundary Contract

Every product decision should pass this boundary contract before implementation planning:

| Boundary question | Product answer |
| --- | --- |
| What does Admin/API own? | All SaaS authority: tenants, users, roles, permissions, billing, feature flags, remote config, app-version policy, notifications, reports, support, audit, security, sync acceptance, and conflict decisions. |
| What does mobile own? | Local execution: NativePHP + Livewire UX, secure session presentation, local cache, drafts, queued intents, device capability UX, navigation, sync status, offline/freshness labels, and user feedback. |
| What must mobile never own? | Tenant authority, permission authority, billing authority, feature/config/version authority, report authority, support authority, audit truth, final sync truth, conflict decisions, or secrets in unsafe storage. |
| What must only happen through API? | Server-trusted reads, writes, boot context, tenant resolution, permission checks, billing checks, feature decisions, notification registration, support actions, report access, sync replay, and audit acceptance. |
| What can be cached locally? | Safe server-confirmed snapshots, tenant labels, capability snapshots, resolved config, recent resources, drafts, queued intents, sync metadata, safe activity hints, and local notification history with freshness state. |
| What must admin control remotely? | Tenant/user access, roles, permissions, feature availability, remote config, version policy, maintenance, force update, sync rules, notifications, report visibility, billing entitlements, support diagnostics, and security posture. |
| What happens offline? | Mobile may show safe cached data, create drafts, and queue allowed intents, but it must label freshness/pending state and reconcile through API before anything becomes server truth. |

The contract is intentionally strict. Mobile can improve speed, clarity, and resilience, but it cannot move the trust boundary away from Admin/API.

The documentation-first architecture model is defined in [Documentation-First Architecture](documentation-first-architecture.md). Use it with this boundary document to record every feature, admin mobile effect, mobile screen API dependency, sync behavior, permission owner, and risk before implementation.

The Admin Control Center model is defined in [Admin Control Center Logic](admin-control-center-logic.md). Use it with this boundary document whenever admin control touches tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance mode, force update, sync behavior, notifications, reports, billing, or support.

The feature flag model is defined in [Feature Flag Logic](feature-flag-logic.md). Use it with this boundary document whenever feature availability depends on global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, emergency, or offline decisions.

The remote configuration model is defined in [Remote Configuration Logic](remote-configuration-logic.md). Use it with this boundary document whenever mobile behavior can vary by safe runtime config, tenant override, app version, cache freshness, offline state, or invalid-config fallback.

The mobile version control model is defined in [Mobile Version Control Logic](mobile-version-control-logic.md). Use it with this boundary document whenever minimum supported versions, optional updates, forced updates, maintenance mode, outdated-client behavior, store links, update messages, or old-version protection affect mobile behavior.

The detailed control-plane responsibility model is defined in [Admin/API Responsibilities](admin-api-responsibilities.md). Use it with this boundary document whenever a feature touches tenants, users, permissions, admin operations, API contracts, feature control, remote configuration, mobile version rules, notifications, billing, support, reporting, audit history, conflict decisions, or security enforcement.

The detailed API contract model is defined in [API-First Principles](api-first-principles.md). Use it with this boundary document whenever a feature touches mobile/API communication, operating context, predictable responses, mobile-friendly errors, sync replay, conflict logic, version rules, or tenant-scoped responses.

The detailed mobile-client responsibility model is defined in [Mobile Client Responsibilities](mobile-client-responsibilities.md). Use it with this boundary document whenever a feature touches mobile UX, secure local session, local cache, offline actions, NativePHP device features, mobile navigation, mobile permissions UX, sync display, drafts, local feedback, or feature visibility.

The detailed mobile UX model is defined in [Mobile UX Principles](mobile-ux-principles.md). Use it with this boundary document whenever NativePHP navigation, simple screens, loading/offline states, thumb-friendly controls, minimum typing, fast actions, secure sessions, admin-rule-based feature visibility, or native permission prompts need local execution without local authority.

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

## System Ownership Summary

| Area | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Identity | Account authority, token issuance, token revocation, session policy, suspension, invitation state. | Login/register UI, local session presentation, secure token storage where available, logout UX. |
| Tenant scope | Tenant membership, tenant selection authority, tenant isolation, tenant plan and limits. | Display of current tenant, cached tenant label, tenant switch UX when API allows it. |
| Roles and permissions | Role definitions, permission checks, policy enforcement, least privilege. | Rendering allowed actions from API-provided capability state. |
| Feature availability | Global, tenant, plan, role, user, device, version, and cohort gates. | Showing enabled, disabled, blocked, deprecated, or update-required states. |
| Remote config | Config schemas, config versions, tenant and rollout scope. | Cached config, config refresh, UI behavior that follows config. |
| App versions | Minimum, supported, deprecated, blocked, and internal-only version policy. | Version reporting, update prompts, blocked/deprecated UX. |
| Billing | Plans, quotas, entitlements, invoices, payment state, restrictions. | Clear allowed/blocked capability state; no billing internals. |
| Notifications | Templates, channels, targeting, quiet hours, delivery policy, delivery reporting. | Device registration, local display, local history when safe, user notification UX. |
| Reports | Report definitions, tenant and role scoping, aggregation, exports, audit-safe visibility. | Personal or workflow status only when API grants it. |
| Support | Ticket authority, safe diagnostics, case timeline, escalation, support reports. | Support request UI, safe diagnostic payload, retry/config-refresh UX. |
| Audit | Server-trusted audit events and sensitive change history. | Local activity timeline only; server may accept mobile-originated events through API. |
| Sync | Replay windows, idempotency rules, conflict decisions, accepted server state. | Local queue, retry UX, freshness state, pending count, conflict presentation. |
| Native capabilities | Policy for when a capability is allowed and why. | Device permission prompts, NativePHP bridge usage, local capture/selection where allowed. |

## What Admin/API Owns

The Admin/API system owns all business-sensitive authority.

See [Admin/API Responsibilities](admin-api-responsibilities.md) for the detailed principles behind each responsibility area.

It owns:

- Tenant lifecycle, tenant membership, tenant isolation, tenant settings, and tenant plan state.
- User lifecycle, invitations, suspension, session policy, token issuance, token revocation, and device trust.
- Roles, permissions, policy checks, least privilege, and all server-side authorization.
- Feature flags, remote config, rollout cohorts, emergency disablement, and rollback.
- App-version policy for supported, recommended, deprecated, blocked, and internal-only builds.
- Billing entitlements, quotas, restrictions, usage limits, renewals, invoices, and payment state.
- Notification templates, channels, targeting, quiet hours, delivery rules, and delivery reports.
- Reports, exports, operational dashboards, aggregation rules, tenant scoping, and support-safe visibility.
- Support cases, safe diagnostics, escalation paths, support actions, and case timeline authority.
- Server-trusted audit events for admin changes and sensitive mobile-originated actions.
- API contracts, request validation, response shaping, error semantics, rate limits, idempotency, and versioning.
- Sync acceptance, sync rejection, conflict decisions, replay windows, retry policy, and canonical server state.

The Admin/API system may expose controls through Livewire admin screens, API resources, jobs, notifications, reports, and support workflows in future implementation slices, but the authority remains server-side.

## What The Mobile Client Owns

The mobile client owns the local user experience and local execution mechanics.

See [Mobile Client Responsibilities](mobile-client-responsibilities.md) for the detailed principles behind each mobile responsibility area.

It owns:

- Mobile navigation, layout, form presentation, loading states, empty states, offline states, and error states.
- NativePHP shell behavior and native capability interaction such as camera, file, microphone, network, share, device, dialog, and local notification UX when allowed.
- Local cache for safe server-confirmed data.
- Local drafts for unfinished user work.
- Local queued intents for offline-capable actions waiting for API replay.
- Local metadata such as last sync time, pending count, retry count, conflict display state, and safe activity history.
- Presentation of feature availability, disabled states, blocked states, deprecated states, update prompts, and conflict explanations.
- Local support request entry and safe diagnostic collection.
- Local notification display/history when policy allows it.
- User-facing explanation when online confirmation is required.

The mobile client can make the product feel fast and resilient. It cannot make the product authoritative.

## What Mobile Must Never Own

The mobile client must never own business authority.

It must never own:

- Tenant authority, tenant membership, tenant switching rules, or cross-tenant visibility.
- Role definitions, permission definitions, permission grants, or final authorization decisions.
- Billing authority, plan authority, quota authority, payment state, price rules, or entitlement decisions.
- Feature flag authority, remote-config authority, rollout authority, or emergency disablement.
- App-version policy, supported-version rules, blocked-version rules, or update enforcement authority.
- Server-trusted audit history.
- Final report data, tenant-wide report visibility, support-wide report visibility, or billing reports.
- Support authority beyond creating requests and sending safe diagnostics.
- Notification targeting, notification templates, quiet-hour policy, or delivery truth.
- Canonical resource state after sync.
- Conflict decisions that affect server data.
- Idempotency acceptance, duplicate detection, replay windows, or retry limits.
- Security decisions such as forced logout, suspension, device trust, or token revocation.
- Secrets in local SQLite, local logs, docs, or visible UI state.

Mobile UI hiding, disabled buttons, cached flags, local role names, local tenant IDs, local plan labels, `wire:confirm`, or NativePHP device state are not security boundaries.

## What Must Only Happen Through API

Any server-trusted behavior must happen through the API.

See [API-First Principles](api-first-principles.md) for the detailed principles behind API purpose, response predictability, operating context, mobile-friendly errors, sync/conflict behavior, and tenant boundary protection.

API-only actions include:

- Authentication exchange, token refresh, token revocation, forced logout, and session/device trust checks.
- Tenant resolution, tenant selection, tenant membership changes, and tenant-scoped data access.
- Role and permission evaluation for every protected read and write.
- Boot config retrieval: tenant memberships, permissions, feature flags, remote config, app-version policy, sync policy, notification policy, and support state.
- Fetching tenant data, workflow data, reports, support cases, billing outcomes, notification history, and canonical resource state.
- Creating, updating, deleting, approving, rejecting, exporting, or sharing server-trusted records.
- Registering device tokens or notification channels.
- Creating support tickets, attaching diagnostics, updating support status, or requesting support actions.
- Replaying offline queued intents.
- Accepting or rejecting sync actions.
- Returning conflict decisions and conflict resolution options.
- Enforcing billing entitlements and quotas before accepting a read, write, sync replay, or feature use.
- Recording server-trusted audit events.
- Returning structured errors for validation, unauthorized, forbidden, conflict, stale client, maintenance, rate limited, and retry later states.

The API contract should be explicit, version-aware, additive where possible, and shaped through resources rather than exposing internal models directly.

## What Can Be Cached Locally

The mobile client may cache data when it improves usability and does not create authority.

Allowed local cache categories:

| Local category | Purpose | Boundary |
| --- | --- | --- |
| Boot snapshot | Fast startup and offline explanation. | Must include freshness/version state; API remains final. |
| Tenant display data | Show current tenant name, logo, and safe labels. | Cannot grant tenant access. |
| Capability snapshot | Show last known allowed/disabled/blocked states. | Cannot authorize new server-trusted actions. |
| Remote config copy | Preserve UI behavior while offline. | Must refresh when online and respect server version. |
| Server-confirmed resources | Read safe recent data offline. | Must show freshness and update from API when online. |
| Drafts | Let users prepare work before submission. | Not server truth until submitted and accepted. |
| Queued intents | Store offline-capable actions for replay. | Must replay through API with idempotency. |
| Sync metadata | Track pending count, last sync, retry, conflict, failed state. | Cannot decide final conflict outcome. |
| Safe local activity | Help the user understand local events. | Not a server-trusted audit log. |
| Local notification history | Help users review received reminders or status. | Not delivery truth unless confirmed by API. |

Local cache must avoid secrets, payment credentials, private keys, raw access tokens when secure storage is available, server-trusted audit records, final billing state, and sensitive data that cannot be protected or revoked appropriately.

## What Must Be Controlled Remotely By Admin

Any behavior that affects business access, tenant trust, or SaaS operations must be remotely controlled by Admin/API.

See [Admin Control Center Logic](admin-control-center-logic.md) for the control checklist behind tenant, user, role, permission, feature, config, version, maintenance, force update, sync, notification, report, billing, and support controls.

Remote admin control includes:

- Tenant enablement, tenant limits, plan state, data-retention posture, and support tier.
- User invitations, suspensions, role assignment, manager scope, and access recovery.
- Device trust, blocked devices, forced logout, app-lock requirements, and minimum app version.
- Feature flags, module availability, rollout cohorts, emergency kill switches, and rollback.
- Remote config that changes mobile copy, limits, workflow options, offline eligibility, retry behavior, or support instructions.
- App-version policy for warnings, deprecated mode, blocked mode, internal testing, and release cohorts.
- Notification templates, channels, quiet hours, targeting, escalation, and delivery reporting.
- Billing entitlement gates, quota outcomes, renewal restrictions, and plan-driven feature availability.
- Report visibility, aggregation, export permissions, and support/billing-safe report boundaries.
- Sync policy: queueable action types, retry limits, replay windows, stale-data thresholds, conflict modes, metered-network rules, and maintenance blocks.
- Support diagnostics policy: what mobile can collect, when it can send it, and who can see it.

Admin control should not make the mobile app unpredictable. It should make mobile behavior explainable: the app receives policy, renders the current state, stores local work only where allowed, and asks the API to confirm business-sensitive actions.

## What Happens When Mobile Is Offline

Offline mode is a constrained state, not a separate authority mode.

When mobile is offline:

1. The app may display a safe cached boot snapshot and last known server-confirmed data.
2. The app must show offline/freshness state near affected workflows.
3. The app may allow read-only offline behavior when cached data is safe to display.
4. The app may allow drafts when work can be prepared without server confirmation.
5. The app may queue intents only for features whose API/admin policy explicitly allows offline replay.
6. The app must label queued work as pending, not synced or final.
7. The app must prevent online-only, high-risk, volatile, billing-sensitive, permission-sensitive, or server-confirmation-required actions.
8. The app must avoid pretending that local tenant, role, plan, feature, or version state is current.
9. The app should keep enough local metadata to explain pending, failed, stale, blocked, and conflict states.

When connectivity returns:

1. The app refreshes boot config before trusting stale capability state.
2. The app checks app-version policy and remote config version.
3. The app replays allowed queued intents through the API with idempotency keys.
4. The API accepts, transforms, rejects, or marks each intent as conflicted.
5. The mobile client updates local state from the API response.
6. The user sees synced, conflict, failed, blocked, or retry-later state.
7. Support/admin reporting receives only the server-accepted diagnostic or conflict context defined by policy.

If the user is suspended, the device is blocked, the app version is blocked, the tenant is disabled, the plan no longer permits the feature, or the permission changed while offline, queued work remains local/pending/failed/conflicted until the API says otherwise.

## Boundary Decision Matrix

Use this matrix when designing a future feature.

| Question | If yes | Boundary decision |
| --- | --- | --- |
| Does it affect tenant access? | Yes | Admin/API only. Mobile may display API-provided state. |
| Does it affect permissions or roles? | Yes | Admin/API only. Mobile cannot grant or infer authority. |
| Does it affect billing, quota, or entitlement? | Yes | Admin/API only. Mobile shows outcome, not billing logic. |
| Does it affect feature availability or rollout? | Yes | Admin/API controls. Mobile renders feature state. |
| Does it affect app-version policy? | Yes | Admin/API controls. Mobile reports version and follows policy. |
| Does it need server-trusted audit? | Yes | API records. Mobile may submit event context. |
| Can the user prepare it offline safely? | Yes | Mobile may create draft. API must accept before it becomes server truth. |
| Can the user submit it offline safely? | Yes, by policy | Mobile may queue intent. API decides replay outcome. |
| Does stale data create harm? | Yes | Online-only or read-only offline with strong freshness warnings. |
| Does support need to diagnose it? | Yes | API defines safe diagnostics and support visibility. |
| Does a report expose tenant/business data? | Yes | Admin/API owns report scope and aggregation. |
| Does native hardware improve UX? | Yes | Mobile may use NativePHP capability after API/admin eligibility and permission purpose are defined. |

## Boundary Examples

### Managed Mobile Boot

Admin/API owns authentication, tenant membership, permissions, feature flags, remote config, app-version policy, sync policy, and notification policy. Mobile owns startup UX, cached boot display, config refresh, update prompts, and permitted navigation.

Mobile must never decide its own tenant list or feature list. It can show a stale boot snapshot only as last known state until API refresh succeeds.

### Offline Records

Admin/API owns whether the records module is enabled, which roles can use it, which fields are accepted, whether offline queueing is allowed, and how conflicts are resolved. Mobile owns local draft entry, local queue storage, pending status, retry UX, and conflict presentation.

Mobile must never mark an offline record as server-confirmed before the API accepts it.

### Notifications

Admin/API owns templates, targeting, channels, quiet hours, delivery policy, and delivery reporting. Mobile owns permission prompts, device token registration flow, local display, and safe local history.

Mobile must never decide who should receive a business notification or whether delivery happened globally.

### Billing-Gated Feature

Admin/API owns plan entitlement, quota checks, renewal restrictions, and blocked/upgrade/contact-support outcomes. Mobile owns clear user-facing messaging.

Mobile must never infer that a cached plan label grants access.

### Support Diagnostics

Admin/API owns ticket creation, case state, escalation, support visibility, and safe diagnostic schema. Mobile owns the support form and safe local diagnostic collection.

Mobile must never send secrets, private local drafts, payment credentials, or unrelated tenant data as diagnostics.

## Risks

| Risk | Product response |
| --- | --- |
| Mobile drifts into policy engine | Keep all tenant, permission, billing, feature, version, and sync decisions in Admin/API. |
| Cached state is mistaken for current authority | Label freshness and refresh boot config before protected actions. |
| Offline queue becomes server truth | Treat queued actions as intents until the API accepts them. |
| Admin settings become surprising to mobile users | Translate policy into stable states: enabled, disabled, blocked, deprecated, pending, synced, conflict, failed, offline. |
| Support receives too much local data | Define a safe diagnostic schema through API policy. |
| Billing logic leaks to mobile | Mobile shows entitlement outcomes only; billing logic remains server-side. |
| Reports leak tenant data | Reports remain Admin/API-owned with tenant, role, aggregation, and export boundaries. |
| Native capabilities bypass policy | NativePHP capabilities require admin/API eligibility and clear purpose before use. |
| App versions fragment behavior | Version boot payloads and deprecate behavior through app-version policy before removal. |

## Boundary Success Test

The boundary is healthy when every feature can answer:

1. What does Admin/API decide?
2. What does mobile present or perform locally?
3. What must happen through the API?
4. What can be cached, drafted, or queued?
5. What must never be trusted from mobile?
6. What does admin remotely control?
7. What happens when the app is offline?
8. How does support explain the outcome?
9. How does billing or entitlement affect access?
10. How does the API enforce the final result?

If these answers are unclear, the feature is not ready for implementation planning.

If the Admin/API responsibility owner is also unclear, the feature is not ready for product planning.

If the mobile-client responsibility owner is also unclear, the feature is not ready for product planning.

If the API purpose, response, context, error, sync/conflict, or tenant-boundary behavior is unclear, the feature is not ready for product planning.

If the feature's documentation-first checklist is incomplete, the feature is not ready for implementation planning.

If the Admin Control Center scope, role authority, mobile effect, API context, audit expectation, support meaning, or offline behavior is unclear, the feature is not ready for admin planning.

If feature flag priority, disabled mobile state, rollout path, admin impact, plan-limit behavior, support meaning, audit expectation, or offline behavior is unclear, the feature is not ready for implementation planning.

If remote config type, default, scope, tenant override, mobile cache rule, offline behavior, invalid-config fallback, admin safety, support meaning, audit expectation, or rollback is unclear, the feature is not ready for implementation planning.
