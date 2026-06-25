# Documentation-First Architecture

Updated: 2026-06-25

This document defines documentation-first architecture principles for Mobile Lara. It explains how product ideas, admin controls, mobile screens, API dependencies, sync behavior, permissions, and risks must be documented before implementation. It is documentation only and does not define endpoints, routes, database fields, migrations, controllers, Livewire components, resources, policies, jobs, services, NativePHP plugins, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [API-First Principles](api-first-principles.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), and [Mobile Version Control Logic](mobile-version-control-logic.md): documentation is the agreement that prevents authority, API behavior, admin controls, feature flags, remote config, mobile-version policy, mobile UX, offline behavior, permissions, and risk handling from drifting during implementation.

## Architecture Statement

Mobile Lara is documentation-first because the product has two systems, tenant boundaries, offline behavior, feature control, mobile-local state, NativePHP capabilities, billing effects, support expectations, reports, audit needs, and security concerns.

Every feature must be documented before implementation so that future code has a clear product reason, authority boundary, API purpose, mobile effect, offline rule, permission owner, and risk record.

Documentation-first does not mean writing long documents for their own sake. It means recording the decisions that make implementation safe.

## Core Documentation-First Principles

1. **Document before coding** - Every feature, control, screen, sync behavior, permission, and risk needs a written product decision before implementation.
2. **Document authority** - Every feature must state what Admin/API owns, what mobile owns, what must go through API, and what must never be trusted locally.
3. **Document mobile effect** - Every admin control must explain how mobile behavior, visibility, copy, offline behavior, sync, support, and errors change.
4. **Document API dependency** - Every mobile screen must explain the API context, response state, error state, permission state, feature state, and sync state it depends on.
5. **Document online and offline behavior** - Every sync behavior must explain local cache/draft/queue behavior, online confirmation, replay, retry, conflict, and failure.
6. **Document permission ownership** - Every permission must explain who controls it, who can use it, what it exposes, and how mobile receives the result.
7. **Document risk before implementation** - Every meaningful risk must be recorded with its owner, affected surface, mitigation, and unresolved decision before coding starts.
8. **Document enough to test later** - Documentation should produce clear acceptance criteria for future implementation and tests.
9. **Document changes as the product changes** - If implementation discovers a different product truth, update docs before treating the change as accepted.
10. **Document boundaries, not just features** - The most important docs describe what the system must not do.

## Every Feature Must Be Documented Before Implementation

Every feature should start as a documented product slice.

A feature document or updated product doc should explain:

- The stakeholder value.
- The user roles and account states involved.
- The Admin/API responsibility owner.
- The mobile-client responsibility owner.
- The API purpose.
- The tenant boundary.
- The feature flag or remote-config relationship.
- The mobile UX states.
- The offline and sync behavior.
- The support and reporting expectations.
- The billing or entitlement effect, if any.
- The audit expectation, if any.
- The risks and non-goals.

No feature should move to code because it is "obvious from the screen." Screens are consequences of product decisions, not substitutes for them.

## Every Admin Control Must Document Its Mobile Effect

Admin controls are not isolated settings. They change what mobile users see, can do, can queue, can sync, or can understand.

Every admin control should document:

| Question | Documentation expectation |
| --- | --- |
| What does the control change? | Feature, permission, config, version, notification, billing, support, sync, report, or security behavior is named. |
| Who can change it? | Platform owner, super admin, tenant admin, tenant manager, support, billing, or another role is named. |
| Which scope applies? | Global, tenant, plan, role, user, device, app version, cohort, support case, or billing scope is explicit. |
| What does mobile receive? | API state, remote config value, feature state, error state, version state, sync state, or entitlement outcome is explicit. |
| How does mobile change? | Screen visibility, disabled state, blocked state, copy, offline eligibility, retry behavior, sync behavior, or support prompt is named. |
| What can go wrong? | Risk, rollback, support explanation, audit expectation, and user-facing failure mode are recorded. |

Admin controls are product levers. Their mobile effects must be written before those levers exist.

Use [Admin Control Center Logic](admin-control-center-logic.md) as the checklist for tenant, user, role, permission, feature, config, version, maintenance, force-update, sync, notification, report, billing, and support controls.

Use [Feature Flag Logic](feature-flag-logic.md) as the checklist for important mobile feature priority, disabled states, rollout, admin impact, plan limits, support, audit, offline behavior, and retirement.

Use [Remote Configuration Logic](remote-configuration-logic.md) as the checklist for remote-configurable behavior, scope, default, tenant override, mobile receive/cache rules, offline behavior, missing/invalid fallback, admin safety, support, audit, rollback, and retirement.

Use [Mobile Version Control Logic](mobile-version-control-logic.md) as the checklist for minimum supported versions, optional updates, forced updates, maintenance mode, outdated responses, store links, update messages, support, audit, rollback, and old-version protection.

## Every Mobile Screen Must Document Its API Dependency

Every mobile screen should document what it needs from the API before it is built.

That dependency should include:

- Boot or account context required.
- Tenant context required.
- Permissions or capability state required.
- Feature flags or remote config required.
- App-version behavior required.
- Data payload purpose, without endpoint design detail.
- User actions that require server confirmation.
- Error states that must be shown.
- Offline state, cached state, draft state, pending state, synced state, conflict state, and failed state where relevant.
- Support path when the screen cannot recover locally.

A mobile screen without an API dependency story is only a mockup. It is not ready for implementation in this SaaS product.

## Every Sync Behavior Must Document Offline And Online Behavior

Sync behavior must be documented from both sides of the connection.

Offline behavior should explain:

- Whether the feature is read-only, draft-only, queueable, or online-only.
- What local cache is safe to display.
- What local drafts can be created.
- What queued intents can be stored.
- What user state is shown while offline.
- What actions are blocked while offline.
- What local data must not be stored.

Online behavior should explain:

- When boot context or config refresh happens.
- How queued intents replay.
- Which idempotency expectation applies.
- Which server checks happen again.
- What accepted, transformed, rejected, duplicated, stale, unauthorized, out-of-policy, conflicted, retry-later, and failed outcomes mean.
- How support and reports can see safe sync context.

Sync is not complete until both offline and online behavior are documented.

## Every Permission Must Document Who Controls It

Permissions are product authority, not UI labels.

Every permission should document:

- The role or admin surface that controls it.
- The tenant, team, user, device, app-version, plan, or support scope where it applies.
- The mobile capability state it creates.
- The API reads, writes, sync replay, support actions, reports, or admin actions it affects.
- The denied, suspended, invited, guest/pre-login, blocked, or expired states.
- The audit expectation for grants, revocations, or high-risk permission use.
- The support explanation for why a user can or cannot act.

Mobile may display permission-derived capability state, but Admin/API owns the permission decision.

## Every Risk Must Be Recorded Before Coding

Risk recording is part of architecture, not an afterthought.

Every feature slice should record:

- Product risk.
- Tenant-boundary risk.
- Security risk.
- Permission or role risk.
- Billing or entitlement risk.
- Offline/sync risk.
- Native permission/device risk.
- API contract risk.
- Support/reporting/audit risk.
- User-confusion risk.
- Rollout or rollback risk.

Each risk should have one of these statuses:

| Status | Meaning |
| --- | --- |
| Avoided | The feature design removes the risk. |
| Mitigated | The design includes a control, boundary, or fallback. |
| Accepted | The risk is known and intentionally tolerated. |
| Deferred | The risk needs a future decision before implementation. |
| Blocked | The feature cannot be implemented until the risk is resolved. |

Unrecorded risk becomes accidental architecture.

## Documentation Artifacts

Future implementation slices should update the smallest useful set of docs.

| Artifact | Purpose |
| --- | --- |
| Product principle update | Records a new or changed product rule. |
| Boundary update | Records what Admin/API owns, what mobile owns, what is API-only, what can be cached, and what must never be local authority. |
| API-first note | Records API purpose, response expectations, mobile-friendly errors, sync/conflict behavior, and tenant scope. |
| Responsibility update | Records Admin/API and mobile-client responsibility owners. |
| Role/value update | Records who controls the feature and who receives value. |
| Sync/offline note | Records online, offline, replay, conflict, retry, failed, and support behavior. |
| Risk register entry | Records risks and mitigation status before coding. |
| ADR | Records a decision that would be expensive to reverse. |
| Acceptance criteria | Records the future behavior that tests should prove. |

Documentation should be concise, but it should be specific enough that another engineer or agent can implement without inventing product authority.

## Documentation-First Checklist

Use this checklist before a feature moves from planning to implementation.

| Question | Required answer |
| --- | --- |
| Is the feature documented? | The product slice, stakeholder value, user roles, and non-goals are written. |
| What does Admin/API own? | Control-plane authority and responsibility owner are named. |
| What Admin Control Center area owns it? | Tenant, user, role, permission, feature, config, version, maintenance, force update, sync, notification, report, billing, or support control is named. |
| What feature flag logic applies? | Global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, emergency, disabled state, rollout, and offline behavior are named. |
| What remote configuration logic applies? | Config type, default, scope, tenant override, mobile cache, offline behavior, missing/invalid fallback, admin safety, support, audit, and rollback are named. |
| What mobile version control logic applies? | Minimum version, optional update, forced update, maintenance mode, store link, update message, stale-client response, support, audit, and rollback are named. |
| What does mobile own? | Local UX, cache, draft, queue, NativePHP, sync display, feedback, or visibility owner is named. |
| What is the API purpose? | API context, response states, errors, sync/conflict, and tenant boundary are documented. |
| What is the admin control's mobile effect? | Mobile visibility, copy, disabled/blocked state, offline behavior, sync behavior, or support prompt is explicit. |
| What does the mobile screen depend on? | API context, permission state, feature state, config, version state, errors, and sync state are explicit. |
| What happens offline and online? | Cache, draft, queue, replay, idempotency, retry, conflict, failed, and support behavior are written. |
| Who controls each permission? | Role, scope, grant/revoke authority, denied state, support explanation, and audit expectation are written. |
| What risks exist? | Risks are recorded as avoided, mitigated, accepted, deferred, or blocked. |
| What remains out of scope? | Schema, migrations, endpoints, controllers, policies, jobs, services, plugins, and code are deferred until an implementation prompt. |

## Risks

| Risk | Documentation-first response |
| --- | --- |
| Docs become busywork | Keep docs decision-focused: authority, API purpose, mobile effect, sync behavior, permissions, risks, and acceptance criteria. |
| Features start from UI screenshots | Require API dependency, admin control, mobile effect, and sync/offline behavior before screen implementation. |
| Admin settings surprise mobile users | Document every admin control's mobile effect and support explanation. |
| API behavior is invented during coding | Document API purpose, context, predictable states, errors, sync/conflict, and tenant boundary first. |
| Offline behavior becomes unclear | Document offline and online behavior together. |
| Permissions become hidden assumptions | Document who controls every permission and how mobile receives the result. |
| Risks are discovered too late | Record risks before coding and mark unresolved risks as deferred or blocked. |
| Documentation drifts from implementation | Update docs when implementation changes product truth, before accepting the change. |
| Documentation overreaches into code | Keep this layer to principles, behavior, boundaries, risks, and acceptance criteria. |
| Agents implement from stale context | Keep docs linked from every project Markdown file and treat them as planning preflight. |

## Success Test

Documentation-first architecture is successful when every feature can be implemented from written product decisions: what value it creates, who controls it, what Admin/API owns, what mobile owns, why the API exists, what the admin control does to mobile, what each mobile screen depends on, how sync works online and offline, who controls permissions, what risks exist, and what code remains intentionally out of scope until implementation.
