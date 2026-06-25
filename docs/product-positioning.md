# Product Positioning

Updated: 2026-06-25

This document defines how Mobile Lara should be positioned as a product. It explains the product as a SaaS control center, mobile workforce/client platform, API-first system, offline-capable mobile system, feature-controlled platform, and tenant-based product. It is documentation only and does not define database fields, migrations, controllers, components, or application logic.

## Positioning Statement

Mobile Lara is a SaaS control center for tenant-based mobile operations. It gives administrators central control over tenants, users, permissions, features, versions, billing, support, notifications, reports, and sync policy while giving mobile users an API-driven, offline-capable NativePHP client for real work.

The product is stronger than a normal web app because it reaches mobile workers where work happens. It is stronger than a standalone mobile app because mobile behavior is centrally governed, auditable, version-aware, and tenant-safe.

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

### Mobile Workforce And Client Platform

Mobile Lara is also a mobile workforce/client platform. The mobile client is not a thin afterthought; it is the working surface for people outside the admin panel.

The mobile platform gives users:

- A focused app experience.
- Native capability access when a feature needs camera, files, microphone, device, network, sharing, or local notification behavior.
- Clear state for offline, pending, synced, conflict, blocked, and deprecated workflows.
- Tenant-safe access to the work they are allowed to perform.

The mobile client exists to serve workers and client-side users without exposing admin machinery.

### API-First System

Mobile Lara is API-first because the API is the contract between central authority and mobile execution.

The API-first position means:

- Mobile clients receive permissions, feature state, remote config, version policy, and sync policy through the API.
- Mobile writes pass through server-side validation, authorization, entitlement checks, and idempotency.
- API responses should be shaped, version-aware, and explicit about blocked, stale, conflict, maintenance, and retry states.
- Admin changes become enforceable mobile behavior through API contracts, not local assumptions.

API-first does not mean mobile-only. It means every mobile capability has a server-enforced contract.

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
- Role and permission gates.
- Plan and billing entitlement gates.
- App-version compatibility gates.
- Device or cohort rollout gates.
- Emergency disablement and rollback.

A feature is not product-ready until it has admin control, API enforcement, mobile presentation, offline behavior where relevant, support visibility, and audit context.

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
7. Web-only is insufficient for mobile work.
8. Mobile-only is insufficient for SaaS governance.
9. Product positioning should guide every future feature slice.

## Risks

| Risk | Product response |
| --- | --- |
| Positioning becomes too broad | Keep the six angles tied to one promise: governed mobile operations. |
| Admin grows into generic CRM/admin software | Keep admin focused on controlling mobile behavior, tenants, support, billing, reports, and sync. |
| Mobile grows into an independent app | Keep mobile API-driven and policy-controlled. |
| Offline features undermine authority | Treat local writes as intents and reconcile through the API. |
| Tenant flexibility becomes custom code | Prefer tenant config, feature flags, entitlements, and versioned API contracts. |

## Positioning Success Test

The product is positioned correctly when a buyer understands that Mobile Lara is neither just an admin dashboard nor just a mobile app. It is a tenant-based SaaS control center with an API-first, feature-controlled, offline-capable mobile client.
