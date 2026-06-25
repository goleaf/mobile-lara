# Mobile Client Responsibilities

Updated: 2026-06-25

This document defines the logical responsibilities of the Mobile Lara mobile client. It explains what the NativePHP + Livewire client owns, what it may cache or perform locally, how it should present server-controlled behavior, and which authority it must never claim. It is documentation only and does not define database fields, migrations, controllers, components, policies, jobs, services, NativePHP plugins, or application logic.

Use this document with [Documentation-First Architecture](documentation-first-architecture.md), [API-First Principles](api-first-principles.md), [Admin/API Responsibilities](admin-api-responsibilities.md), and [Two-System Boundary Logic](two-system-boundary.md): Admin/API owns authority, API is the trusted contract, mobile owns local execution and presentation, and every mobile screen documents its API dependency before implementation.

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
