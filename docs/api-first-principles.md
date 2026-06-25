# API-First Principles

Updated: 2026-06-26

This document defines API-first principles for Mobile Lara. It explains how the Admin/API system and NativePHP + Livewire mobile client communicate, what the API must make predictable, how mobile features depend on API purpose, and how API behavior protects tenants, permissions, sync, conflicts, and mobile UX. It is documentation only and does not define endpoints, routes, database fields, migrations, controllers, resources, policies, jobs, services, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Documentation-First Architecture](documentation-first-architecture.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX Principles](mobile-ux-principles.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md), and [Admin Safety Principles](admin-safety-principles.md): Admin/API owns authority, mobile owns local execution, API is the only trusted contract between them, stakeholder value reaches mobile through safe API outcomes, mobile UX presents those outcomes as clear navigation/states/actions, admin controls are scoped, auditable, previewable, and rollback-aware, feature flags resolve to mobile-safe states, remote config resolves to validated mobile-safe values, mobile version policy resolves to safe update/maintenance states, and API behavior is documented before implementation.

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

## API-First Statement

Mobile Lara is API-first because the mobile client must communicate with the SaaS control plane only through the API.

The API is not a convenience layer. It is the contract that turns Admin/API authority into mobile behavior:

- It tells mobile who the user is.
- It tells mobile which tenants, permissions, features, config, version rules, and sync rules apply.
- It tells mobile which Admin Control Center outcomes apply for tenants, users, roles, permissions, features, config, versions, maintenance, force update, sync, notifications, reports, billing, and support.
- It tells mobile resolved feature flag outcomes such as hidden, visible, disabled, blocked, beta, deprecated, update-required, offline-limited, or emergency-disabled.
- It tells mobile resolved remote configuration values, config version, freshness, compatibility, fallback, and invalid-config states where relevant.
- It tells mobile resolved app-version outcomes such as current, supported, optional update, recommended update, deprecated, force update, blocked, maintenance, internal-only, or stale client.
- It receives mobile reads, writes, support actions, notification registration, and offline replay intents.
- It returns predictable success, denial, conflict, stale-client, offline-recovery, and user-friendly error states.
- It protects tenant boundaries even when mobile is stale, offline, copied between devices, or running an old app version.

## Core API-First Principles

1. **Mobile communicates only with API** - Mobile must not read server databases, call admin internals, bypass policies, or invent server authority.
2. **Responses are predictable** - Similar API situations should return similar shapes, states, error categories, metadata, and mobile next actions.
3. **Every mobile feature has an API purpose** - A feature should know why it talks to API: boot, context, list, detail, action, sync replay, conflict, support, notification, reporting, or entitlement.
4. **API returns operating context** - Mobile needs permissions, feature flags, remote config, version rules, user context, tenant context, sync policy, notification policy, support state, and entitlement outcomes through API.
5. **Errors are mobile-friendly** - API errors should be safe, structured, non-leaking, and easy for mobile to translate into next actions.
6. **Sync and conflicts are first-class** - API must support queued intents, idempotency, replay outcomes, stale-state handling, and conflict categories.
7. **Tenant boundaries are protected by API** - Tenant scope must be resolved and enforced server-side for every protected request and response.
8. **The API is version-aware** - Mobile-dependent behavior should be compatible, additive where possible, and governed by app-version policy before removal.
9. **The API shapes data for mobile** - Mobile should receive useful payloads and allowed actions, not raw internal models or admin machinery.
10. **The API is supportable** - API outcomes should leave enough safe context for support, audit, reporting, billing, and conflict explanation.

## API-First Contract

Every mobile feature planning decision should name its API purpose before implementation planning.

| API-first area | Principle | Mobile-safe outcome |
| --- | --- | --- |
| Communication path | The mobile client communicates with Admin/API only through the API for every server-trusted read, write, replay, support action, notification registration, report, entitlement check, and version check. | Mobile uses local cache, drafts, queues, and NativePHP state only as temporary local execution until API confirms truth. |
| Predictable responses | API responses use consistent states, metadata, error categories, pagination/bounds, freshness/version context, and next-action semantics. | Mobile can render allowed, denied, blocked, disabled, pending, synced, conflict, failed, retry-later, update-required, or maintenance states without guessing. |
| Feature API purpose | Every mobile feature names why it talks to API before endpoint or screen design. | Features are planned as boot/context, read, action, draft submission, offline replay, conflict, support, notification, reporting, entitlement, or version behavior. |
| Operating context | API returns the permissions, feature flags, remote config, version rules, user context, tenant context, sync policy, notification policy, support state, and billing/entitlement outcomes mobile needs. | Mobile presents capability and next actions from server-shaped context instead of inferring authority from local role names, plan labels, cached flags, or device state. |
| Mobile-friendly errors | API errors are structured, safe, non-leaking, and mapped to useful mobile recovery states. | Mobile can show field feedback, permission denial, tenant block, billing/quota limit, maintenance, stale-client, conflict, retry, support, or logout guidance. |
| Sync and conflict logic | API supports queued intents, idempotency, replay decisions, stale-state handling, conflict categories, and safe conflict reasons. | Mobile can show pending, synced, conflict, failed, blocked, stale, retry-later, or user-resolution states while API keeps canonical authority. |
| Tenant boundary protection | API resolves tenant scope server-side for every protected request and response, including offline replay and support/report/billing contexts. | Mobile shows only tenant-safe context and never treats a local tenant ID, cached membership, route, or screen as tenant authority. |

This contract is intentionally principle-level. It does not define endpoints, routes, fields, database tables, migrations, controllers, resources, policies, jobs, services, provider integrations, or application logic.

## Mobile Communicates Only With API

The mobile client must use the API for every server-trusted behavior.

Principles:

- Mobile does not connect directly to production databases.
- Mobile does not call admin-only internals.
- Mobile does not bypass policies through NativePHP capabilities, local SQLite, cached flags, local routes, or Livewire UI state.
- Mobile reads server-trusted data only through API responses.
- Mobile writes server-trusted data only through API requests.
- Mobile replays offline work only through API.
- Mobile receives support, notification, billing, feature, config, version, and sync outcomes through API.

Local cache, local drafts, local queues, and local feedback are useful only because they eventually reconcile with API authority.

## Predictable API Responses

API responses should be boring in the best way: consistent, shaped, and unsurprising.

Principles:

- Similar resources should use similar response patterns.
- Similar failures should use similar error categories.
- Mobile should be able to distinguish success, validation failure, unauthenticated, forbidden, not found, conflict, stale client, maintenance, rate limited, retry later, blocked, and server error states.
- Responses should include the state mobile needs to decide presentation: allowed, denied, disabled, blocked, deprecated, pending, synced, conflict, failed, retry-later, or update-required.
- List-style responses should be bounded and safe for mobile consumption.
- Response metadata should be intentional, not accidental leakage.
- API contracts should be additive where possible and deprecated through version policy before removal.

Predictability reduces mobile complexity and support confusion.

## Every Mobile Feature Has A Clear API Purpose

Every mobile feature should explain why it talks to the API before implementation.

[Documentation-First Architecture](documentation-first-architecture.md) requires this API purpose to be written before endpoint design or mobile screen implementation.

Common API purposes include:

| API purpose | Product meaning |
| --- | --- |
| Boot/context | Give mobile user, tenant, permission, feature, config, version, sync, notification, support, and entitlement context. |
| Read/list/detail | Return server-confirmed data in a mobile-safe shape. |
| Action/command | Ask the server to accept a business-sensitive user action. |
| Draft submission | Turn prepared local work into a server-validated request. |
| Offline replay | Submit queued local intents after reconnecting. |
| Conflict result | Explain why replay or update could not be accepted as-is. |
| Support | Create or update support context through safe diagnostics. |
| Notification registration | Register device/channel details without letting mobile decide targeting. |
| Entitlement check | Return allowed, blocked, quota-warning, contact-admin, support, or upgrade outcome. |
| Version check | Return supported, optional update, recommended update, deprecated, force-update, blocked, maintenance, store-link, message, or internal-only app state. |

No feature should exist as only a mobile screen. If a feature changes server-trusted data, capability state, sync behavior, billing access, support context, or tenant visibility, it needs an API purpose.

## API Returns Operating Context

The API must return the operating context mobile needs to behave correctly.

That context should include, where applicable:

- Authenticated user context.
- Account state such as active, invited, suspended, verification-required, or recovery-limited.
- Tenant context and tenant membership allowed by server policy.
- Role-derived permissions and capability state.
- Feature flags and feature availability.
- Remote configuration and config version.
- Mobile app-version rules.
- Mobile version-control state, store link, update message, maintenance state, and stale-client next action.
- Sync policy and replay eligibility.
- Notification policy and device registration requirements.
- Support state and safe diagnostic expectations.
- Billing or entitlement outcomes.
- Server time and freshness/version metadata where useful.

Mobile should not infer these from local role names, local tenant IDs, local plan labels, cached flags, or NativePHP device state.

## Mobile-Friendly API Errors

API errors should be safe for mobile users and useful for mobile UI.

Principles:

- Error responses should be structured enough for mobile to choose the right state.
- Error messages should be user-friendly where the user can act.
- Sensitive internal details, stack traces, secrets, SQL, provider internals, and tenant-private context should not be exposed.
- Validation errors should map cleanly to form fields or local feedback.
- Authorization and tenant errors should explain access state without leaking whether hidden data exists.
- Billing and entitlement errors should return product outcomes, not payment-provider internals.
- Version errors should tell mobile whether to warn, limit, or block.
- Sync errors should distinguish retryable failure, stale data, conflict, unauthorized replay, disabled feature, quota block, and maintenance.
- Support paths should be discoverable when user recovery is not enough.

Mobile-friendly errors are not softer security. They are structured, safe product outcomes.

## Sync And Conflict Logic

The API must support sync and conflict logic because offline work is central to the mobile product.

Principles:

- Queued offline actions are intents until API acceptance.
- Replayable writes need idempotency expectations.
- The API should decide whether an intent is accepted, transformed, rejected, duplicated, stale, unauthorized, out-of-policy, conflicted, retryable, or failed.
- Conflict outcomes should include enough safe reason and next-action context for mobile and support.
- Replay should re-check tenant state, permission state, billing/entitlement state, feature state, app-version policy, and current server state.
- Mobile should be able to show pending, synced, conflict, failed, blocked, stale, and retry-later states without guessing.
- Support and reports should receive only server-accepted diagnostic or conflict context defined by policy.

Sync is not a background detail. It is part of the API contract.

## Tenant Boundary Protection

The API protects tenant boundaries for every protected request and response.

Principles:

- Tenant scope is resolved server-side.
- Mobile-provided tenant IDs are claims to validate, not authority.
- Every response should expose only the tenant context the user and device are allowed to see.
- Cross-tenant support, billing, reporting, notification, and audit context must be scoped by role and purpose.
- Offline replay must re-check tenant membership and tenant status.
- Errors should avoid leaking other tenants, hidden resources, or private membership details.
- API contracts should make tenant scope explicit enough for mobile to present context without giving mobile authority.

Tenant protection is an API responsibility, not a mobile navigation rule.

## What API-First Does Not Mean

API-first does not mean:

- Designing endpoints before product purpose is clear.
- Exposing raw models because mobile asks for data.
- Letting mobile choose tenant, permission, billing, feature, config, or version state.
- Turning the API into a pass-through to admin tables.
- Returning every possible field to avoid future work.
- Hiding business decisions in mobile code.
- Treating offline replay as trusted server truth.
- Designing separate unaligned API behavior for each screen.

API-first means the product contract is written, predictable, secure, tenant-scoped, mobile-usable, and supportable.

## API-First Feature Checklist

Use this checklist before planning a future mobile feature.

| Question | Required answer |
| --- | --- |
| What is the API purpose? | Boot/context, read, action, draft submission, offline replay, conflict, support, notification, entitlement, version, or reporting purpose is named. |
| What context does mobile need? | User, tenant, permissions, feature flags, config, version rules, sync policy, notification policy, support state, or entitlement outcome is explicit. |
| What shape should mobile receive? | Mobile-safe state, payload, metadata, and next action are clear at principle level. |
| What errors can happen? | Validation, unauthenticated, forbidden, not found, conflict, stale client, maintenance, rate limited, retry later, blocked, or server error states are considered. |
| How is the error mobile-friendly? | User-facing state and safe next action are defined without leaking internals. |
| How does sync work? | Online-only, draft-only, queueable, idempotent replay, or conflict behavior is explicit. |
| How are tenant boundaries protected? | Server-side tenant resolution, role scope, response scope, and offline replay checks are explicit. |
| What must mobile not infer? | Tenant, permission, billing, feature, config, version, notification, report, support, audit, conflict, or security authority is excluded. |
| What support can explain? | Safe API outcome, app version, config version, tenant context, sync state, and conflict reason are named. |
| What must stay undocumented until implementation? | Concrete endpoints, fields, database changes, controllers, policies, jobs, and code are deferred to implementation slices. |

## Risks

| Risk | API-first response |
| --- | --- |
| Mobile bypasses API | Treat API as the only trusted path for server reads, writes, replay, support, notifications, and audit. |
| Responses become inconsistent | Keep states, errors, metadata, and payload purpose predictable. |
| Features start from screens | Require a clear API purpose before a mobile feature becomes durable. |
| Boot/context payload grows into a dump | Return only operating context mobile needs; keep internals hidden. |
| Errors are too technical | Return safe categories and mobile next actions instead of stack traces or provider internals. |
| Sync becomes vague | Model replay outcomes and conflicts as first-class API behavior. |
| Tenant data leaks | Resolve tenant scope server-side and shape every response by role and tenant context. |
| Version drift breaks mobile | Use app-version policy and additive contracts before removing behavior. |
| Support cannot explain outcomes | Include safe, scoped context for feature, config, version, sync, billing, and conflict decisions. |
| API becomes a generic admin backend | Keep API contracts focused on mobile-safe product behavior, not raw admin data. |

## Success Test

The API-first model is successful when every mobile feature can name its API purpose, receive predictable context and responses, translate errors into useful mobile states, replay offline work through explicit sync/conflict rules, and preserve tenant boundaries without the mobile client inventing authority.
