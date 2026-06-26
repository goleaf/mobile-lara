# Mobile Client Responsibilities

Updated: 2026-06-26

This document defines the logical responsibilities of the Mobile Lara mobile client. It explains what the NativePHP + Livewire client owns, what it may cache or perform locally, how it should present server-controlled behavior, and which authority it must never claim. It is documentation only and does not define database fields, migrations, controllers, components, policies, jobs, services, NativePHP plugins, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Documentation-First Architecture](documentation-first-architecture.md), [API-First Principles](api-first-principles.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Two-System Boundary Logic](two-system-boundary.md), [Mobile UX Principles](mobile-ux-principles.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md), and [Admin Safety Principles](admin-safety-principles.md): Admin/API owns authority, API is the trusted contract, mobile owns local execution and presentation, stakeholder value is shown as simple mobile UX, admin controls define API-derived outcomes, dangerous changes are previewed before mobile receives them, feature flags resolve to mobile-safe states, remote config resolves to mobile-safe values, mobile version policy resolves to update/maintenance states, and every mobile screen documents its API dependency before implementation.

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

## Responsibility Statement

The mobile client is the managed local execution surface.

It owns mobile user experience, secure local session presentation, local cache, offline actions, NativePHP device-feature interaction, mobile navigation, mobile permissions UX, sync status display, local drafts, local user feedback, and feature visibility based on Admin/API rules.

It does not own SaaS authority. Tenant authority, billing authority, permission authority, global configuration authority, feature authority, API contract authority, app-version policy, notification targeting, support authority, reporting authority, audit truth, conflict decisions, and security enforcement belong to Admin/API.

## Core Responsibility Principles

1. **Mobile executes server decisions** - The mobile client turns API policy into usable screens, local state, and user feedback.
2. **Local state improves resilience** - Cache, drafts, queues, and local metadata exist to keep the app useful, not authoritative.
3. **Every state is honest** - Offline, cached, draft, pending, synced, conflict, failed, blocked, disabled, and deprecated states must be visible where they affect the workflow.
4. **Native capability is feature-scoped** - NativePHP features are used only for documented product needs and within Admin/API eligibility.
5. **Secure local session is not server authority** - Local unlock, secure storage, and session display can protect the device experience, but the server can revoke access.
6. **Mobile UX stays simple** - Mobile users should see clear next actions, not raw feature flags, billing rules, tenant policy, or support internals.
7. **API remains the path to truth** - Any server-trusted read, write, sync replay, support action, notification registration, or audit event must pass through API.

## Mobile Responsibility Ownership Contract

Every mobile planning decision should name the local responsibility area that owns the experience before implementation planning.

| Mobile responsibility | Mobile client owns | Must not become |
| --- | --- | --- |
| Mobile user experience | Task flow, mobile layout, loading, empty, error, offline, disabled, blocked, pending, synced, conflict, and failed states. | Product authority, server validation, workflow permission, or canonical data state. |
| Secure local session | Secure local session presentation, token-storage UX where available, app lock/unlock, timeout, logout, and session-expired messaging. | Authentication authority, token issuance, token revocation, forced logout policy, device trust, or account status. |
| Local cache | Safe server-confirmed copies, bootstrap snapshots, capability snapshots, freshness labels, and refresh UX. | Tenant authority, permission authority, billing authority, global configuration authority, feature authority, or audit truth. |
| Offline actions | Queued intents, retry metadata, pending state, failed state, replay prompts, and offline-only explanation. | Accepted server records, entitlement decisions, authorization results, conflict decisions, or canonical persistence. |
| NativePHP device features | Device permission prompts, capture/selection UX, capability availability, denial guidance, unsupported-platform state, and local device feedback. | Product eligibility, server storage, validation, audit acceptance, tenant scope, or bypass of API rules. |
| Mobile navigation | Shell structure, tabs, menus, back flows, route grouping, screen state, and account-state presentation. | Permission system, feature flag source, tenant-switch authority, or app-version authority. |
| Mobile permissions UX | User education for camera, files, biometrics, network, notifications, location, scanner, microphone, and settings recovery. | SaaS role permission, policy decision, billing entitlement, feature gate, or device-trust authority. |
| Sync status display | Last sync, freshness, pending count, retry count, conflict count, network state, stale warnings, and user-safe recovery paths. | Sync policy, replay acceptance, conflict reason authority, support visibility, report authority, or server truth. |
| Local drafts | Unsynced work, draft recovery, edit/resume, discard, submit, and interruption recovery UX. | Validated submission, permission grant, billing grant, canonical record, accepted audit event, or conflict outcome. |
| Local user feedback | Toasts, banners, inline messages, Livewire loading state, validation response display, retry guidance, support prompts, and local/server confirmation wording. | Error semantics, validation rule authority, authorization decision, support policy, billing outcome, or security decision. |
| Feature visibility from admin rules | API-derived show, hide, disable, block, beta, deprecate, update-required, offline-limited, and emergency-disabled presentation. | Feature flag authority, remote config authority, plan gate, tenant gate, role gate, version gate, cohort rule, or emergency override. |

This contract is intentionally principle-level. It does not create models, tables, policies, jobs, controllers, Livewire components, API routes, NativePHP plugins, local storage tables, provider integrations, or feature records.

## Responsibility Map

| Responsibility | Mobile client owns | Admin/API still owns |
| --- | --- | --- |
| Mobile user experience | Mobile layout, task flow, loading states, empty states, error states, offline states, disabled states, conflict presentation. | Product policy, allowed workflows, server validation, authorization, and final data state. |
| Secure local session | Local session presentation, secure token storage where available, app lock/unlock UX, logout UX, local timeout messaging. | Authentication authority, token issuance, token revocation, forced logout, session/device trust, suspension. |
| Local cache | Safe server-confirmed data copies, boot snapshots, capability snapshots, freshness labels, cache refresh UX. | Canonical records, tenant authority, permission authority, billing authority, feature state, app-version policy. |
| Offline actions | Local queued intents, retry metadata, pending state, retry UI, failed-state display. | Replay acceptance, idempotency, authorization, entitlement check, conflict decision, canonical result. |
| NativePHP device features | Device permission prompts, local capture/selection, device capability UX, plugin interaction, local permission state display. | Eligibility, feature flag, tenant/role/device/version policy, permission purpose, server acceptance of resulting data. |
| Mobile navigation | Mobile shell, tab/menu presentation, back flows, route grouping, current screen state. | Navigation eligibility through capability state, account state, tenant state, feature flags, app-version policy. |
| Mobile permissions UX | Native permission education, denied/retry instructions, settings deep-link UX where appropriate. | Role permissions, policy decisions, feature eligibility, device trust, server authorization. |
| Sync status display | Last sync, freshness, pending count, retry count, conflict count, online/offline status, stale-state warnings. | Sync policy, replay windows, accepted server state, conflict reasons, reporting/support visibility. |
| Local drafts | Unsynced user work, draft recovery, edit/resume UX, discard/submit choices. | Validation, permission, entitlement, canonical persistence, conflict outcome, audit acceptance. |
| Local user feedback | Toasts, banners, inline messages, Livewire loading feedback, validation response display, support/contact prompts. | Error semantics, validation rules, authorization result, support policy, billing outcome, security outcome. |
| Feature visibility from admin rules | Show, hide, disable, block, deprecate, update-required, or offline-limited feature states based on API policy. | Feature flags, remote config, plan gates, tenant gates, role gates, app-version gates, rollout cohorts, emergency disablement. |

## Mobile User Experience

The mobile client owns the working surface for mobile users.

Principles:

- Every mobile screen should document its API dependency before implementation.
- Screens should be task-focused and mobile-first.
- The app should make allowed workflows easy to start, pause, resume, and complete.
- Loading, empty, error, offline, disabled, blocked, pending, synced, conflict, and failed states should be clear.
- Admin machinery should be translated into simple outcomes.
- Mobile UX should not expose raw tenant configuration, billing internals, permission matrices, rollout cohorts, or support internals unless the user needs a safe explanation.

The mobile client owns presentation quality. Admin/API owns whether an action is allowed and whether submitted work becomes server truth.

## Secure Local Session

The mobile client owns secure local session experience on the device.

Principles:

- Tokens and secrets should use secure storage where available, not local SQLite or visible logs.
- Local lock/unlock, biometric/PIN prompts, timeout messaging, and logout UX are mobile responsibilities.
- Local session state should be treated as a convenience until the API confirms that the account, tenant, device, and app version are still allowed.
- The app should gracefully handle token expiration, forced logout, suspension, blocked devices, and app-version blocks.
- Local unlock must not bypass server authentication or authorization.

Secure local session protects the device experience. It does not grant tenant, permission, billing, or feature authority.

## Local Cache

The mobile client owns local cache for speed and resilience.

Principles:

- Cache may store safe server-confirmed data, boot snapshots, capability snapshots, config copies, and recent resources.
- Cached data must carry freshness expectations when stale data could mislead the user.
- Cache can support read-only offline use where policy allows it.
- Cache refresh should happen when online before protected actions rely on stale state.
- Cache must not store secrets, payment credentials, private keys, final audit truth, or authority that must be revocable.

The cache is useful memory, not business truth.

Remote config cache follows [Remote Configuration Logic](remote-configuration-logic.md): mobile may cache resolved values with version and freshness metadata, but stale or invalid config cannot grant authority.

Version policy follows [Mobile Version Control Logic](mobile-version-control-logic.md): mobile reports its version, follows optional-update, force-update, maintenance, blocked, or deprecated API outcomes, preserves safe local drafts where possible, and avoids protected actions when old-version policy is unsafe.

## Offline Actions

The mobile client owns offline action preparation and local queue presentation.

Principles:

- Offline actions are queued intents, not accepted server records.
- Queue entries should represent what the user attempted, when it was attempted, and what retry/conflict state is visible locally.
- Offline actions should exist only for features whose Admin/API policy allows offline replay.
- High-risk, volatile, billing-sensitive, permission-sensitive, or online-only actions should be blocked or draft-only while offline.
- When online returns, queued intents must replay through API and accept the API result.

The mobile client can keep work moving. The API decides whether that work becomes canonical.

## NativePHP Device Features

The mobile client owns NativePHP device-feature interaction.

Principles:

- Native features should be requested just in time.
- Permission prompts should explain the feature purpose in user-friendly language.
- Device features should be tied to a product feature, not exposed as generic device access.
- Captured or selected local data remains local until submitted through API.
- Device availability, permission denial, and unsupported-platform states should be presented clearly.

NativePHP bridges make the mobile app useful on real devices. They do not bypass Admin/API eligibility, validation, storage, audit, or security rules.

## Mobile Navigation

The mobile client owns navigation experience.

Principles:

- Navigation should reflect API-provided account state, tenant state, feature state, and version state.
- Guest/pre-login, invited, suspended, blocked-version, offline-limited, and authenticated mobile states should have distinct navigation behavior.
- Navigation should avoid showing dead ends when a feature is disabled, but disabled states may be shown when explanation helps the user.
- Cached navigation can be used for startup speed, but it must refresh when online.
- Mobile navigation must not infer authority from local route availability.

Navigation is a presentation layer for server policy, not a permission system.

## Mobile Permissions UX

The mobile client owns mobile permissions UX for device capabilities.

Principles:

- Device permission UX should explain why a permission is needed before or during the request.
- Permission denial should offer a safe next step: continue without the capability, retry, open settings, or contact support.
- Native permission state should be distinguished from SaaS permission state.
- A granted camera, file, microphone, network, notification, or device permission does not grant feature access.
- A denied native permission should not be confused with a role or billing denial.

Mobile permissions UX helps the user control device access. Admin/API still controls product access.

## Sync Status Display

The mobile client owns sync status display.

Principles:

- Users should be able to see whether work is cached, draft, pending, synced, conflicted, failed, blocked, or stale.
- Last sync time, pending count, retry state, and conflict state should appear near affected workflows.
- Sync status should distinguish network outage from policy block, version block, authentication failure, permission denial, billing denial, and server error.
- Retry actions should not promise success before the API confirms it.
- Support paths should be visible when local recovery is not enough.

Sync status display turns API and network complexity into honest mobile feedback.

## Local Drafts

The mobile client owns local drafts for unfinished work.

Principles:

- Drafts let users prepare work before submission.
- Drafts should be clearly separate from submitted, pending, synced, and conflicted work.
- Drafts may persist while offline or after interruption when safe.
- Draft submission must pass through API validation, authorization, entitlement, feature, version, and conflict rules.
- Draft discard and recovery behavior should be clear.

Drafts belong to the mobile workflow. Accepted records belong to the server.

## Local User Feedback

The mobile client owns local user feedback.

Principles:

- Feedback should be immediate where possible and honest where server confirmation is pending.
- Livewire loading, disabled, retry, validation, and error states should be visible and specific.
- API errors should be translated into useful mobile states without leaking internals.
- Local success messages should distinguish saved locally from accepted by server.
- Billing, permission, feature, app-version, support, and security outcomes should be phrased as next actions.

Feedback is the mobile client's way of making Admin/API outcomes understandable.

## Feature Visibility Based On Admin Rules

The mobile client owns how feature visibility is presented after Admin/API decides eligibility.

Principles:

- The app may show, hide, disable, block, deprecate, or require update for features based on API policy.
- Feature visibility should map back to Admin Control Center controls for tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance, force update, sync behavior, notifications, reports, billing, and support.
- Feature visibility should map back to Feature Flag Logic states: hidden, visible, disabled, blocked, beta, deprecated, update-required, offline-limited, or emergency-disabled.
- Feature visibility should consider tenant, role, plan, user, device, app version, cohort, remote config, and offline eligibility as returned by API.
- Feature-disabled messaging should be clear without exposing raw flag names or billing internals.
- Emergency-disabled features should fail closed and explain what the user can do next.
- Cached feature state should refresh online before sensitive actions.

The mobile client presents feature state. It does not define feature availability.

## What Mobile Must Not Own

The mobile client must not own:

- **Tenant authority** - tenant membership, tenant switching authority, tenant isolation, tenant status, and cross-tenant visibility.
- **Billing authority** - plan state, quota authority, invoices, payment status, price rules, entitlement decisions, and renewal restrictions.
- **Permission authority** - role definitions, permission grants, authorization decisions, account-state overrides, and server-side policy.
- **Global configuration authority** - remote config schema, global defaults, tenant config, rollout config, emergency disablement, and safe fallback rules.
- **Feature authority** - global, tenant, plan, role, user, device, version, or cohort feature gates.
- **API contract authority** - request/response contracts, error semantics, validation rules, idempotency, pagination, and deprecation policy.
- **App-version authority** - supported, recommended, deprecated, blocked, or internal-only version rules.
- **Notification authority** - targeting, templates, channels, quiet hours, delivery policy, escalation, and delivery truth.
- **Support authority** - case state, support visibility, escalation policy, support actions, and diagnostic schema.
- **Reporting authority** - tenant reports, support reports, billing reports, exports, aggregation, and cross-user visibility.
- **Audit authority** - trusted audit history for admin changes or accepted sensitive mobile-originated events.
- **Conflict authority** - final accepted, rejected, transformed, duplicated, stale, unauthorized, out-of-policy, or conflicted decisions.
- **Security enforcement** - authentication authority, token revocation, forced logout, device trust, rate limits, tenant scope, and secrets policy.
- **Canonical resource authority** - server-trusted records after sync, deletion, approval, rejection, or billing/permission-sensitive mutation.

Mobile UI state, local route access, cached feature flags, cached role names, cached tenant IDs, local plan labels, NativePHP device state, local unlock state, or disabled buttons are not authority.

## Relationship To Admin/API

The mobile client is the consumer and presenter of Admin/API decisions.

Mobile receives:

- Boot state.
- Account state.
- Tenant context.
- Capability state.
- Feature state.
- Remote config copy.
- App-version policy.
- Notification policy.
- Sync policy.
- Support state.
- Billing or entitlement outcomes.
- API error and conflict outcomes.

Mobile returns:

- Auth requests.
- Device registration details.
- Safe diagnostic payloads.
- User-initiated actions.
- Offline replay intents.
- Local draft submissions.
- Native capability outputs where allowed.
- User feedback events only when Admin/API accepts them as meaningful.

The API is the contract that keeps this relationship safe.

See [API-First Principles](api-first-principles.md) for how mobile/API communication, predictable responses, operating context, mobile-friendly errors, sync/conflict behavior, and tenant boundaries should work.

## Responsibility Checklist

Use this checklist before planning a future mobile-client slice.

| Question | Required answer |
| --- | --- |
| What mobile experience owns this? | UX, navigation, local session, cache, draft, queue, sync display, native capability, permissions UX, feedback, or feature visibility is named. |
| What Admin/API rule controls it? | Tenant, permission, billing, feature, config, version, notification, support, report, audit, conflict, or security authority is named. |
| What can be cached? | Safe cache category, freshness, and refresh behavior are explicit. |
| Can it work offline? | Read-only, draft-only, queueable, or online-only behavior is explicit. |
| What state does the user see? | Offline, cached, draft, pending, synced, conflict, failed, blocked, disabled, deprecated, or update-required state is explicit. |
| What NativePHP capability is involved? | Device permission, purpose, denial behavior, unsupported-platform behavior, and API submission boundary are explicit. |
| What must go through API? | Server-trusted read, write, replay, support, notification, audit, or validation behavior is explicit. |
| What must mobile not decide? | Tenant, billing, permission, config, feature, version, notification, support, report, audit, conflict, or security authority is explicitly excluded. |
| What support can explain? | Safe local state, app version, config version, sync status, device state, and recent errors are named without secrets. |
| What is the user feedback rule? | Local-only success and server-confirmed success are distinguishable. |

## Risks

| Risk | Responsibility response |
| --- | --- |
| Mobile becomes a policy engine | Mobile presents API outcomes; Admin/API owns rules. |
| Local cache is mistaken for current authority | Show freshness and refresh before protected actions. |
| Local success messages overpromise | Distinguish saved locally, queued, pending, synced, failed, and conflicted states. |
| Offline queue becomes canonical | Treat queued work as intent until API accepts it. |
| Native permissions imply product access | Separate device permission from tenant, role, billing, and feature eligibility. |
| Navigation leaks unavailable workflows | Navigation follows API capability state and account state. |
| Drafts are treated as submitted work | Draft state remains local until API accepts submission. |
| Sync errors become vague | Display network, policy, version, auth, permission, billing, conflict, and server failures differently where useful. |
| Feature visibility hides important context | Hide complexity, but show clear disabled, blocked, deprecated, or update-required explanations. |
| Secure local session is overtrusted | Local unlock is not API authentication, authorization, or token validity. |

## Success Test

The mobile-client responsibility model is successful when a mobile user can understand what they can do next, what is local versus server-confirmed, what is offline versus blocked, what needs retry or support, and why a feature is visible or unavailable, while every tenant, billing, permission, configuration, feature, version, notification, report, audit, conflict, and security decision remains owned by Admin/API.
