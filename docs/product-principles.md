# Core Product Principles

Updated: 2026-06-25

This document defines the core product principles for Mobile Lara. These principles guide product decisions, documentation, feature design, and future implementation planning. It is documentation only and does not define database fields, migrations, controllers, components, policies, or application logic.

## Principle Summary

Mobile Lara is a tenant-based SaaS control center with an API-first, feature-controlled, offline-capable mobile client. The Admin/API system is the source of business authority. The mobile client is a simple, resilient executor of that authority.

Every future feature should satisfy these principles before implementation begins.

The documentation-first architecture standard in [Documentation-First Architecture](documentation-first-architecture.md) defines how those principles become planning requirements: every feature, admin control, mobile screen, sync behavior, permission, and risk is documented before coding.

The [Admin Control Center Logic](admin-control-center-logic.md) defines how admin authority becomes operational control over tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance mode, force update, sync behavior, notifications, reports, billing, and support.

The [Feature Flag Logic](feature-flag-logic.md) defines how important mobile features are controlled, prioritized, disabled, rolled out, plan-limited, audited, supported, and resolved into mobile-safe states.

The [Remote Configuration Logic](remote-configuration-logic.md) defines how safe runtime behavior is configured, delivered, cached, overridden, validated, audited, and handled when offline, missing, or invalid.

The [Mobile Version Control Logic](mobile-version-control-logic.md) defines how supported versions, optional updates, forced updates, maintenance mode, outdated responses, store links, update messages, and old-version protection are controlled.

The role model in [Target User Roles](user-roles.md) defines who can see and control product surfaces. Role boundaries are part of every principle below.

The [SaaS Value Map](saas-value-map.md) defines why each product surface matters. Stakeholder value is part of every principle below: admin control, mobile access, offline sync, notifications, reports, security, and feature flags must create clear value for the right role without leaking authority to the wrong one.

The [Two-System Boundary Logic](two-system-boundary.md) defines where each responsibility belongs. Boundary ownership is part of every principle below: Admin/API owns authority, mobile owns local execution, API confirms server-trusted behavior, and offline work remains constrained.

The [API-First Principles](api-first-principles.md) define how the two systems communicate. API purpose is part of every principle below: mobile communicates only with API, responses are predictable, operating context is explicit, errors are mobile-friendly, sync/conflict behavior is first-class, and tenant boundaries are protected server-side.

The [Admin/API Responsibilities](admin-api-responsibilities.md) define what the control plane must own. Responsibility ownership is part of every principle below: tenant management, users and permissions, admin operations, API contracts, feature control, remote configuration, mobile version rules, notifications, billing, support, reporting, audit, conflict decisions, and security enforcement stay server-side.

The [Mobile Client Responsibilities](mobile-client-responsibilities.md) define what the managed client must own. Responsibility ownership is part of every principle below: mobile UX, secure local session, cache, offline actions, NativePHP features, navigation, permissions UX, sync status, drafts, feedback, and feature visibility stay local while authority stays server-side.

## 1. Admin Controls Everything

Admin controls every business-sensitive mobile capability.

This includes tenants, users, roles, permissions, devices, feature flags, remote config, app-version policy, notifications, billing entitlements, reports, support visibility, sync policy, and emergency rollback.

"Admin controls everything" does not mean the admin UI stores all logic in screens. It means the Admin/API system is the authority and the admin panel exposes safe controls for that authority. The API still enforces decisions server-side.

Product rule: if a mobile behavior can affect business data, tenant access, billing, support, security, or sync, it must have an admin/API control story.

Use [Admin/API Responsibilities](admin-api-responsibilities.md) to name the exact control-plane responsibility before planning the feature.

Use [Admin Control Center Logic](admin-control-center-logic.md) to name the exact admin control area, scope, mobile effect, API context, audit expectation, support meaning, and offline behavior.

## 2. Mobile Client Never Bypasses API

The mobile client must never bypass the API for server-trusted behavior.

The mobile client may use local storage for cache, drafts, local metadata, and queued intents. It must not directly decide tenant authority, permission authority, billing authority, feature authority, audit authority, or final sync outcomes.

All remote reads, writes, sync replay, support actions, notification registration, version checks, and feature decisions must go through the API contract.

Product rule: mobile can prepare work locally, but the API confirms whether that work becomes server truth.

Use [Mobile Client Responsibilities](mobile-client-responsibilities.md) to name the exact local responsibility before planning mobile behavior.

## 3. Every Feature Can Be Enabled Or Disabled

Every feature must be controllable.

Feature control can happen globally, by tenant, by plan, by role, by user, by device, by app version, or by rollout cohort. Some features need emergency kill switches. Some features need gradual rollout. Some features should be visible but blocked with a clear reason.

A feature is not product-ready if it exists only as a screen. It must define:

- Who can enable it.
- Who can use it.
- Which plans or tenants get it.
- Which app versions support it.
- Whether it works offline.
- What happens when it is disabled.
- How support can explain its state.

Product rule: every feature ships with enable, disable, rollback, support, and audit thinking.

Use [Feature Flag Logic](feature-flag-logic.md) to define global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, emergency, and offline decisions before implementation.

Use [Remote Configuration Logic](remote-configuration-logic.md) to define safe runtime config, defaults, tenant overrides, mobile caching, offline behavior, invalid-config fallback, support visibility, audit, and rollback.

Use [Mobile Version Control Logic](mobile-version-control-logic.md) to define minimum supported versions, optional updates, forced updates, maintenance behavior, store links, update messages, support visibility, audit, and rollback.

## 4. Tenant Isolation Is Non-Negotiable

Tenant isolation is the primary SaaS trust boundary.

Every admin action, API request, mobile boot payload, report, support case, notification, sync decision, and audit event must be scoped to the correct tenant. Mobile-provided tenant IDs are claims to verify, not authority to trust.

Tenant isolation also applies to product operations. Support users, billing operators, tenant admins, and platform operators should only see the tenant context their role allows.

Invited, suspended, and guest/pre-login states override normal role access.

Product rule: no feature is complete until tenant scope is explicit and enforced by the Admin/API system.

## 5. Offline-First Where Useful

Offline-first is a product choice, not a blanket rule.

Some features should work offline. Some should be read-only offline. Some should allow drafts but not queue writes. Some must remain online-only because the server decision is too important or too volatile.

Local offline work should be modeled as cache, draft, pending intent, synced copy, conflict, or failed state. Queued writes must replay through the API with idempotency and clear conflict behavior.

Product rule: use offline-first where it helps mobile users, but never let offline behavior become client-side authority.

## 6. Secure By Default

Security is a default product property, not a later hardening pass.

Secure by default means:

- Server-side authorization for every business action.
- Tenant scope on every relevant action and response.
- Least-privilege admin roles.
- Token-based mobile access through the API.
- Secrets stored in secure storage, not local SQLite, docs, code, or logs.
- Sensitive admin changes audited with actor, scope, old value, new value, and reason.
- Dangerous controls require confirmation and are reversible where possible.

Product rule: disabled UI, hidden buttons, cached flags, and local state are never security boundaries.

## 7. API-First Communication

The API is the contract between the SaaS control center and the mobile client.

API-first means mobile behavior comes from versioned, explicit server responses. The API returns permissions, feature state, remote config, app-version policy, sync policy, support state, and resource data in shaped payloads. It also returns clear error categories such as validation, unauthorized, forbidden, conflict, stale client, maintenance, rate limited, and retry later.

Product rule: every mobile capability needs a server contract before it becomes a durable mobile workflow.

Use [API-First Principles](api-first-principles.md) to define the API purpose, predictable response, context, mobile-friendly error, sync/conflict, and tenant-boundary expectations.

## 8. Simple Mobile UX

The mobile client should be simple because mobile users are there to do work, not administer the SaaS.

Simple mobile UX means:

- Show only permitted workflows.
- Explain disabled, blocked, deprecated, offline, pending, synced, conflict, and failed states clearly.
- Keep forms short and task-focused.
- Request native permissions only when needed.
- Avoid exposing billing, tenant configuration, rollout, or support internals unless they directly help the user.
- Keep local feedback fast while making server confirmation honest.

Product rule: mobile users should understand what they can do next without understanding the admin machinery behind it.

## 9. Documentation-First Development

Product-critical decisions must be written before implementation.

Documentation-first means feature work starts with principles, boundaries, flows, risks, API behavior, admin control behavior, mobile behavior, offline behavior, support behavior, and audit behavior. ADRs record decisions that would be expensive to reverse.

The detailed checklist lives in [Documentation-First Architecture](documentation-first-architecture.md).

Documentation is not a substitute for tests or implementation. It is the agreement that makes future implementation safer.

Product rule: if a feature changes product authority, tenant behavior, API contracts, sync behavior, native permissions, billing, support, or security, document the decision before writing code.

## 10. Modular Feature Expansion

The product should grow by modules that prove the full control loop.

Each module should define:

- Admin controls.
- API contracts.
- Mobile screens or workflows.
- Feature flags and entitlements.
- Offline behavior where useful.
- Tenant scope.
- Support and reporting visibility.
- Audit behavior.
- Rollout and rollback plan.

Modules should be independently understandable and should avoid duplicating logic across admin, API, and mobile surfaces.

Product rule: expand by complete feature modules, not scattered screens.

## Principle Checklist

Before a future feature is implemented, answer:

| Question | Required answer |
| --- | --- |
| Who controls it? | Admin/API owns the business decision. |
| Which admin control owns it? | Tenant, user, role, permission, feature, config, version, maintenance, force update, sync, notification, report, billing, or support control maps to `docs/admin-control-center-logic.md`. |
| What is the feature flag logic? | Important mobile features map to `docs/feature-flag-logic.md` for priority, disabled state, rollout, admin impact, plan limit, support, audit, and offline behavior. |
| What is the remote config logic? | Runtime-configurable behavior maps to `docs/remote-configuration-logic.md` for config type, scope, default, override, cache, offline, validation, fallback, support, audit, and rollback. |
| Which roles see it? | Visibility and control map to `docs/user-roles.md`. |
| Who receives value? | Stakeholder value maps to `docs/saas-value-map.md`. |
| Which system owns it? | Boundary ownership maps to `docs/two-system-boundary.md`. |
| What is the API purpose? | API-first purpose maps to `docs/api-first-principles.md`. |
| Which Admin/API responsibility owns it? | Responsibility ownership maps to `docs/admin-api-responsibilities.md`. |
| Which mobile-client responsibility owns it? | Local execution ownership maps to `docs/mobile-client-responsibilities.md`. |
| Can mobile bypass it? | No; mobile uses the API contract. |
| Can it be disabled? | Yes; behavior is defined for disabled state. |
| Is tenant scope clear? | Yes; scope is server-enforced. |
| Does offline help? | Yes/no, with explicit state and replay behavior. |
| Is it secure by default? | Yes; authorization, secrets, audit, and least privilege are defined. |
| Is the API contract clear? | Yes; request, response, errors, versioning, and idempotency are known. |
| Is mobile UX simple? | Yes; users see clear next actions and status. |
| Is the decision documented? | Yes; documentation-first architecture checks feature docs, admin mobile effect, screen API dependency, sync behavior, permission owner, and risks before code. |
| Is it modular? | Yes; admin/API/mobile/support/audit behavior belongs to one feature slice. |

## Boundaries

This principles document does not create:

- Database fields.
- Migrations.
- API routes or controllers.
- Livewire components.
- Policies.
- Jobs or services.
- Feature flag records.
- Tenant records.
- Billing provider integrations.
- Push provider integrations.
- Native plugin integrations.
- Application logic.

Those belong in future implementation prompts with tests and acceptance criteria.

## Risks

| Risk | Principle response |
| --- | --- |
| Admin controls become UI-only | API remains the enforcement layer. |
| Mobile duplicates business logic | Mobile renders server policy and replays work through the API. |
| Feature flags become unmanaged sprawl | Every feature needs owner, scope, disable behavior, support visibility, and audit trail. |
| Admin controls become unmanaged sprawl | Use Admin Control Center logic to define scope, role authority, mobile effect, API context, audit, support, offline behavior, and risk. |
| Feature flags become unmanaged sprawl | Use Feature Flag Logic to define owner, scope, purpose, priority, rollout, mobile state, support meaning, audit, and retirement. |
| Remote config becomes unmanaged sprawl | Use Remote Configuration Logic to define owner, purpose, scope, default, compatibility, validation, fallback, support, audit, rollback, and retirement. |
| Feature value becomes unclear | Use the SaaS value map to name the stakeholder, outcome, and proof metric before implementation. |
| Ownership boundary becomes blurry | Use the two-system boundary before deciding where state, authority, cache, queue, or offline behavior belongs. |
| API behavior becomes accidental | Use the API-first principles before deciding response shape, operating context, mobile errors, sync/conflict, or tenant-scoped responses. |
| Responsibility ownership becomes blurry | Use the Admin/API responsibility map before deciding who owns tenant, permission, API, feature, config, version, notification, billing, support, report, audit, conflict, or security behavior. |
| Mobile local ownership becomes blurry | Use the mobile-client responsibility map before deciding who owns UX, session, cache, queue, NativePHP, navigation, permissions, sync display, drafts, feedback, or feature visibility. |
| Tenant boundaries blur in support/reporting | Tenant scope applies to operations as strongly as core data. |
| Offline behavior creates false confidence | Local state is labeled cache, draft, pending, synced, conflict, or failed. |
| Security is delayed | Secure-by-default is part of every feature checklist. |
| Documentation drifts | Use Documentation-First Architecture to update docs as part of feature definition and before accepting implementation changes. |
| Modules become tangled | Features expand as complete slices with clear admin/API/mobile/support/audit ownership. |

## Success Test

The principles are working when every new product slice can explain who benefits, who controls it, how the API enforces it, how mobile presents it, how tenants are isolated, how it behaves offline, how it stays secure, how it is documented, and how it can expand without becoming tangled.
