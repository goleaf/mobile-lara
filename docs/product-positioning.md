# Product Positioning

Updated: 2026-06-25

This document defines how Mobile Lara should be positioned as a product. It explains the product as a SaaS control center, mobile workforce/client platform, API-first system, offline-capable mobile system, feature-controlled platform, and tenant-based product. It is documentation only and does not define database fields, migrations, controllers, components, or application logic.

## Positioning Statement

Mobile Lara is a SaaS control center for tenant-based mobile operations. It gives administrators central control over tenants, users, permissions, features, versions, billing, support, notifications, reports, and sync policy while giving mobile users an API-driven, offline-capable NativePHP client for real work.

The product is stronger than a normal web app because it reaches mobile workers where work happens. It is stronger than a standalone mobile app because mobile behavior is centrally governed, auditable, version-aware, and tenant-safe.

The positioning depends on [Core Product Principles](product-principles.md): admin authority, API-first mobile behavior, feature control, tenant isolation, useful offline behavior, secure defaults, simple mobile UX, documentation-first decisions, and modular expansion.

It depends on [Documentation-First Architecture](documentation-first-architecture.md): positioning stays useful only when features, admin effects, mobile API dependencies, sync behavior, permission ownership, and risks are documented before implementation.

It depends on [Admin Control Center Logic](admin-control-center-logic.md): admin control stays useful only when tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance, force update, sync behavior, notifications, reports, billing, and support are scoped, authorized, auditable, and API-driven.

It depends on [Feature Flag Logic](feature-flag-logic.md): feature-controlled positioning stays useful only when important mobile features have predictable priority, disabled states, admin impact, rollout safety, and plan-limit behavior.

It depends on [Remote Configuration Logic](remote-configuration-logic.md): config-driven positioning stays useful only when runtime behavior is safe to configure, versioned, scoped, cached carefully, tenant-aware, validated, and fallback-safe.

It depends on [Mobile Version Control Logic](mobile-version-control-logic.md): version-aware positioning stays useful only when minimum supported versions, optional updates, forced updates, maintenance mode, store links, update messages, and old-version protection are controlled by Admin/API.

It also depends on [Target User Roles](user-roles.md): each role has a different responsibility, visibility boundary, and control surface.

It is made measurable by the [SaaS Value Map](saas-value-map.md): each positioning angle must create clear value for platform owner, tenant business, tenant admin, mobile worker/client, support team, or billing/operations team.

It is made operational by [Two-System Boundary Logic](two-system-boundary.md): each positioning angle must respect the split between Admin/API authority and mobile-client execution.

It is made contract-driven by [API-First Principles](api-first-principles.md): mobile communicates only with API, API responses are predictable, every mobile feature has an API purpose, API returns operating context, errors are mobile-friendly, sync/conflict behavior is first-class, and tenant boundaries are protected server-side.

It is made accountable by [Admin/API Responsibilities](admin-api-responsibilities.md): the control center must own tenant management, users and permissions, admin operations, API contracts, feature control, remote configuration, mobile version rules, notifications, billing/subscription logic, support operations, reporting, audit history, conflict decisions, and security enforcement.

It is made usable by [Mobile Client Responsibilities](mobile-client-responsibilities.md): the managed client must own mobile UX, secure local session, cache, offline actions, NativePHP features, navigation, permissions UX, sync display, drafts, local feedback, and feature visibility without owning SaaS authority.

## Six Product Angles

### SaaS Control Center

Mobile Lara is first a control center. The admin/API system is where the business decides what is allowed, enabled, billable, visible, reportable, and supportable.

The control center owns:

- Tenant lifecycle and tenant settings.
- User, role, permission, and device access.
- Feature flags and remote configuration.
- App-version policy and rollout state.
- Notification, support, report, billing, and sync policy.
- Audit context for changes that affect mobile behavior.

This positioning matters because mobile operations cannot be controlled safely from each device. SaaS control belongs in one server-side operating layer.

The detailed control-plane responsibility map lives in [Admin/API Responsibilities](admin-api-responsibilities.md).

The detailed admin control model lives in [Admin Control Center Logic](admin-control-center-logic.md).

The detailed feature-control model lives in [Feature Flag Logic](feature-flag-logic.md).

The detailed remote-config model lives in [Remote Configuration Logic](remote-configuration-logic.md).

The detailed mobile-version model lives in [Mobile Version Control Logic](mobile-version-control-logic.md).

### Mobile Workforce And Client Platform

Mobile Lara is also a mobile workforce/client platform. The mobile client is not a thin afterthought; it is the working surface for people outside the admin panel.

The mobile platform gives users:

- A focused app experience.
- Native capability access when a feature needs camera, files, microphone, device, network, sharing, or local notification behavior.
- Clear state for offline, pending, synced, conflict, blocked, and deprecated workflows.
- Tenant-safe access to the work they are allowed to perform.

The mobile client exists to serve workers and client-side users without exposing admin machinery.

The detailed mobile responsibility map lives in [Mobile Client Responsibilities](mobile-client-responsibilities.md).

### API-First System

Mobile Lara is API-first because the API is the contract between central authority and mobile execution.

The API-first position means:

- Mobile clients receive permissions, feature state, remote config, version policy, and sync policy through the API.
- Mobile writes pass through server-side validation, authorization, entitlement checks, and idempotency.
- API responses should be shaped, version-aware, and explicit about blocked, stale, conflict, maintenance, and retry states.
- Admin changes become enforceable mobile behavior through API contracts, not local assumptions.

API-first does not mean mobile-only. It means every mobile capability has a server-enforced contract.

The detailed API boundary is defined in [Two-System Boundary Logic](two-system-boundary.md): server-trusted reads, writes, sync replay, billing checks, feature decisions, support actions, and audit events must happen through the API.

The detailed API behavior model is defined in [API-First Principles](api-first-principles.md).

### Offline-Capable Mobile System

Mobile Lara is offline-capable because real mobile work does not always happen on stable networks.

Offline-capable positioning means:

- Local storage can hold cache, drafts, pending actions, local metadata, and sync cursors.
- Queued work is treated as intent until the API accepts it.
- Users can see freshness, pending work, conflicts, and retry states.
- Admin/API policy decides which features can work offline and how replay behaves.
- Support can reason about sync failures and local/server divergence.

Offline capability is a product trust feature, not a license for client-side authority.

### Feature-Controlled Platform

Mobile Lara is feature-controlled because SaaS growth depends on safe rollout, not hardcoded screens.

Feature control includes:

- Global feature defaults.
- Tenant-level enablement.
- User-level preview or exclusion inside global, tenant, plan, permission, version, and safety boundaries.
- Role and permission gates.
- Plan and billing entitlement gates.
- App-version compatibility gates.
- Device or cohort rollout gates.
- Emergency disablement and rollback.

A feature is not product-ready until it has admin control, API enforcement, mobile presentation, offline behavior where relevant, support visibility, and audit context.

Remote configuration complements feature control by tuning safe runtime behavior for allowed features without granting authority, changing billing, or redefining permissions.

### Tenant-Based Product

Mobile Lara is tenant-based because each customer workspace must be isolated, configurable, billable, reportable, and supportable.

Tenant-based positioning means:

- Every admin action has a scope.
- Every API request resolves tenant authority server-side.
- Every mobile boot payload is tenant-aware.
- Reports and support views respect tenant boundaries.
- Billing and feature availability are tenant-aware.
- Tenant configuration can differ without forking the mobile app.

The product scales when new tenants create configuration and policy differences, not new code paths.

## Why Not Only A Web App

Only building a web app would make the control center easier to start, but it would weaken the mobile product.

A web-only product would struggle with:

- Native mobile capability access.
- Offline-capable field workflows.
- Device-aware app-version policy.
- Local queues, drafts, and sync status.
- Mobile-first ergonomics for real-world use.
- App-like trust patterns such as local notifications, camera capture, device state, and secure local access.

The admin web experience is necessary, but it is not enough for workers who need a focused mobile client.

## Why Not Only A Mobile App

Only building a mobile app would make the user surface visible quickly, but it would weaken the SaaS business.

A mobile-only product would struggle with:

- Tenant administration.
- Permission and role governance.
- Billing entitlement enforcement.
- Feature rollout and rollback.
- App-version governance.
- Support visibility.
- Reports and audit trails.
- Central sync and conflict policy.

Mobile without a control center becomes hard to operate as customers, teams, versions, devices, and pricing plans multiply.

## Why The Combined Position Wins

The combined product wins because it treats administration, API authority, mobile UX, offline resilience, feature control, and tenant isolation as one system.

It also wins because the same platform capability creates different stakeholder value without collapsing permissions. For example, feature flags give platform owners rollout safety, tenant businesses controlled adoption, support teams explanation context, billing teams entitlement mapping, and mobile users clear enabled or blocked states.

| Product need | Web-only | Mobile-only | Mobile Lara |
| --- | --- | --- | --- |
| Central tenant control | Strong | Weak | Strong |
| Mobile field usability | Weak | Strong | Strong |
| Native capability access | Weak | Strong | Strong |
| Offline work | Weak | Possible but risky | Controlled and API-reconciled |
| Feature rollout | Medium | Weak | Strong |
| Billing enforcement | Strong | Weak | Strong |
| App-version policy | Weak | Medium | Strong |
| Support and audit context | Strong | Weak | Strong |
| Tenant-based SaaS scale | Medium | Weak | Strong |

Mobile Lara is positioned as the middle path that keeps the web/admin strengths and mobile strengths while avoiding their isolated weaknesses.

## Operating Principles

1. The admin/API system is the SaaS control center.
2. The mobile client is the workforce/client platform.
3. The API is the contract, not a convenience layer.
4. Offline work is local intent until the server accepts it.
5. Features are controlled by policy before they appear as screens.
6. Tenants are the primary commercial and security boundary.
7. Secure-by-default is part of the product position, not a separate hardening phase.
8. Documentation-first architecture records feature behavior, admin mobile effects, screen API dependencies, sync behavior, permission ownership, and risks before coding.
9. Admin Control Center logic records who can control tenants, users, roles, permissions, features, config, versions, maintenance, force update, sync, notifications, reports, billing, and support before coding.
10. Feature Flag Logic records how important mobile features resolve scope, priority, disabled states, rollout, impact, plan limits, support, and offline behavior before coding.
11. Remote Configuration Logic records what can be configured, how mobile receives and caches config, offline behavior, tenant overrides, safe admin changes, and fallback behavior before coding.
12. Mobile Version Control Logic records minimum supported versions, optional updates, forced updates, maintenance mode, outdated-client behavior, store links, update messages, and old-version protection before coding.
12. Role boundaries determine who can see or control each surface.
13. Stakeholder value determines why a feature exists and which outcome it should prove.
14. Web-only is insufficient for mobile work.
15. Mobile-only is insufficient for SaaS governance.
16. Product positioning should guide every future modular feature slice.
17. Two-system boundary rules decide whether a behavior belongs in Admin/API, mobile, local cache, or API-only execution.
18. API-first principles decide the purpose, predictability, context, error, sync/conflict, and tenant-scope expectations for mobile/API behavior.
19. Admin/API responsibility rules decide which control-plane owner must govern tenant, user, API, feature, config, version, notification, billing, support, report, audit, conflict, or security behavior.
20. Mobile-client responsibility rules decide which local experience owner should present UX, session, cache, offline, NativePHP, navigation, permissions, sync, draft, feedback, or feature-visibility behavior.

## Risks

| Risk | Product response |
| --- | --- |
| Positioning becomes too broad | Keep the six angles tied to one promise: governed mobile operations. |
| Admin grows into generic CRM/admin software | Keep admin focused on controlling mobile behavior, tenants, support, billing, reports, and sync. |
| Admin Control Center scope becomes vague | Use the control-center checklist before planning tenant, user, role, permission, feature, config, version, maintenance, force-update, sync, notification, report, billing, or support controls. |
| Feature flags become hidden product logic | Use Feature Flag Logic to define scope, priority, mobile state, admin impact, rollout, plan limits, support, audit, and retirement. |
| Remote config becomes hidden product logic | Use Remote Configuration Logic to define safe config types, scope, defaults, overrides, caching, offline behavior, validation, fallback, support, audit, and rollback. |
| Old mobile versions damage trust | Use Mobile Version Control Logic to define minimum supported versions, update paths, maintenance behavior, store links, support messages, and stale-client blocks. |
| Mobile grows into an independent app | Keep mobile API-driven and policy-controlled. |
| Offline features undermine authority | Treat local writes as intents and reconcile through the API. |
| Tenant flexibility becomes custom code | Prefer tenant config, feature flags, entitlements, and versioned API contracts. |

## Positioning Success Test

The product is positioned correctly when a buyer understands that Mobile Lara is neither just an admin dashboard nor just a mobile app. It is a tenant-based SaaS control center with an API-first, feature-controlled, offline-capable mobile client.

The positioning should also make the value map obvious: platform leaders get control, tenant businesses get governed mobile operations, tenant admins get practical management, mobile workers get simple allowed workflows, support gets diagnosable context, and billing/operations gets entitlement control.
