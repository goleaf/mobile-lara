# ADR-0001: Admin/API Control Plane And Native Mobile Client

## Status

Accepted

## Date

2026-06-25

## Context

Mobile Lara needs to support a SaaS business where administrators control tenants, users, permissions, remote config, feature flags, app versions, notifications, billing, reports, support, and sync behavior. The mobile application must work through the API and use NativePHP for device capabilities.

The key tension is authority. Mobile apps need local resilience and offline behavior, but SaaS business rules must stay server-controlled and tenant-safe.

The product vision is remote control with local resilience: admin users manage policy and operations centrally, while mobile users get a simple controlled app for day-to-day work. See [Product Vision](../product-vision.md).

The product positioning is deliberately combined: SaaS control center, mobile workforce/client platform, API-first system, offline-capable mobile system, feature-controlled platform, and tenant-based product. See [Product Positioning](../product-positioning.md).

The architecture must also satisfy [Core Product Principles](../product-principles.md): admin authority, API-first mobile communication, feature control, tenant isolation, useful offline behavior, secure defaults, simple mobile UX, documentation-first decisions, and modular expansion.

The target role model is defined in [Target User Roles](../user-roles.md). The architecture must keep platform owner, super admin, tenant admin, tenant manager, support agent, billing manager, mobile user, invited user, suspended user, and guest/pre-login user boundaries distinct.

The SaaS value map is defined in [SaaS Value Map](../saas-value-map.md). The architecture must preserve value for platform owner, tenant business, tenant admin, mobile worker/client, support team, and billing/operations team without giving each stakeholder the same visibility or control.

The system boundary is defined in [Two-System Boundary Logic](../two-system-boundary.md). The architecture must keep Admin/API authority separate from mobile execution, cache, drafts, queues, native capabilities, and offline presentation.

The Admin/API responsibility model is defined in [Admin/API Responsibilities](../admin-api-responsibilities.md). The architecture must keep tenant management, users and permissions, admin panel operations, API contracts, feature control, remote configuration, mobile version rules, notification orchestration, billing/subscription logic, support operations, reporting, audit history, conflict decisions, and security enforcement in the control plane.

The mobile-client responsibility model is defined in [Mobile Client Responsibilities](../mobile-client-responsibilities.md). The architecture must keep mobile user experience, secure local session, local cache, offline actions, NativePHP device features, mobile navigation, mobile permissions UX, sync status display, local drafts, local user feedback, and feature visibility in the managed client without making them authority.

## Decision

Use a two-system architecture:

1. **Admin/API system** - Laravel API plus Livewire admin panel. This system is the SaaS control plane and source of authority.
2. **Mobile client system** - Laravel plus Livewire running through NativePHP Mobile. This system is the managed edge client and local executor.

The mobile client must consume server-provided boot config, remote config, feature flags, permissions, app-version policy, and sync policy. Local mobile state can improve resilience and UX, but it cannot grant business authority.

The Admin/API system must remain the owner of the responsibility areas documented in [Admin/API Responsibilities](../admin-api-responsibilities.md). The mobile client receives outcomes from those responsibilities; it does not duplicate or override them.

The mobile client must remain the owner of the local execution areas documented in [Mobile Client Responsibilities](../mobile-client-responsibilities.md). The Admin/API system controls the policy and canonical outcomes; mobile owns how those outcomes are presented, cached, queued, retried, and explained.

This split exists because admin users and mobile users have different jobs. Admin users need tenant-safe operational control, rollout visibility, support context, and auditability. Mobile users need fast workflows, clear state, and native device capabilities without seeing the underlying SaaS machinery.

## Alternatives Considered

### Mobile-first authority

The mobile app would own more rules locally and sync when possible.

- Pros: Fast local UX and fewer API dependencies.
- Cons: Hard to enforce billing, permissions, tenant isolation, feature rollout, and app-version policy.
- Rejected because SaaS control and auditability are core product requirements.

### Admin-only web product with thin mobile wrapper

The admin web app would be the main product and NativePHP would wrap a mostly online web UI.

- Pros: Simpler backend and fewer offline concerns.
- Cons: Weak mobile UX, weak native capability story, poor offline behavior, and poor fit for mobile workers.
- Rejected because the product is positioned as both a SaaS control center and a mobile workforce/client platform.

### Mobile-only product

The product would focus almost entirely on the mobile app, with minimal admin or API control.

- Pros: Faster visible mobile surface.
- Cons: Weak tenant governance, billing enforcement, support visibility, reporting, app-version policy, auditability, and feature rollout.
- Rejected because a tenant-based SaaS product needs central authority and operations.

### Separate technology stacks for admin and mobile

The admin/API and mobile client would use completely different frameworks.

- Pros: Each app could optimize independently.
- Cons: More operational cost, duplicated conventions, more agent/context drift.
- Rejected for now because Laravel + Livewire can serve both admin and mobile surfaces while keeping server-side rules consistent.

### Native-only mobile application

The mobile client would be implemented as a fully native iOS/Android application.

- Pros: Maximum platform control and native convention support.
- Cons: More codebases, duplicated validation/state patterns, slower iteration for a Laravel-centered product, and more work to keep mobile behavior aligned with API/admin rules.
- Rejected for now because NativePHP + Livewire provides enough native capability access while preserving Laravel-first product development.

## Consequences

- Admin/API is responsible for authorization, tenant scope, feature eligibility, billing entitlements, audit trails, API contracts, and sync decisions.
- Mobile is responsible for NativePHP bridges, local SQLite, secure local auth state, offline queues, and mobile UX.
- API design must be versioned, idempotent for replayable writes, and explicit about conflicts.
- Feature work must include admin logic, API behavior, mobile behavior, offline behavior, support behavior, and audit behavior.
- Feature work must identify stakeholder value and connect it to admin control, mobile access, offline sync, notifications, reports, security, feature flags, or an explicit combination.
- Feature work must identify system ownership: what Admin/API owns, what mobile owns, what is API-only, what can be cached locally, what admin controls remotely, and how offline reconciliation works.
- Feature work must identify responsibility ownership: which Admin/API responsibility owns tenant, user, permission, API, feature, config, version, notification, billing, support, report, audit, conflict, or security behavior.
- Feature work must identify mobile responsibility ownership: which mobile-client responsibility owns UX, local session, cache, offline action, NativePHP capability, navigation, permission prompt, sync display, draft, feedback, or feature visibility behavior.
- Documentation and future implementation should treat local mobile data as cache, draft, queue, or confirmed server copy depending on sync state.
- NativePHP + Livewire remains the chosen mobile approach until a future ADR demonstrates that native-only or another mobile stack is worth the extra operational cost.
- Future architecture changes should preserve the core principles unless a newer ADR explicitly supersedes them.
- Role and account-state boundaries should be treated as authorization requirements, not UI preferences.

## Implementation Boundary

This ADR is documentation only. It does not create schema, migrations, controllers, Livewire components, policies, or application logic.
