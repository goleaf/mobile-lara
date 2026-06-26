# Documentation Audit

Final Optimized SaaS Blueprint is defined in `final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

Updated: 2026-06-26

Feature Dependency Map is defined in `feature-dependency-map.md`:
major features must document dependencies on authentication, tenant context,
permissions, feature flags, remote config, API availability, offline cache,
NativePHP permissions, subscription plan, and admin settings before
implementation planning or release decisions.

This document audits the Mobile Lara documentation set for consistency across
the two-system SaaS mobile/admin concept. It explains the shared language that
all project Markdown files should use when describing architecture, Admin/API
responsibilities, mobile-client responsibilities, API-first behavior, feature
flags, remote config, tenancy, permissions, offline sync, NativePHP features,
notifications, billing, support, reports, security, risks, and releases.

This is documentation only. It does not create database fields, migrations,
routes, controllers, Livewire components, Filament resources, NativePHP
plugins, policies, jobs, services, tests, API endpoints, local storage schemas,
UI components, CSS, JavaScript, queues, provider integrations, release
automation, or application logic.

Use this audit with [Final Optimized SaaS
Blueprint](final-optimized-saas-blueprint.md), [Product
Vision](product-vision.md), [Product
Positioning](product-positioning.md), [Core Product
Principles](product-principles.md), [Documentation-First
Architecture](documentation-first-architecture.md), [Acceptance
Principles](acceptance-principles.md), [Risk Map](risk-map.md), [Testing
Strategy Principles](testing-strategy-principles.md), [Release And Versioning
Principles](release-versioning-principles.md), [Feature Dependency
Map](feature-dependency-map.md), [Two-System Boundary
Logic](two-system-boundary.md), [Admin/API
Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [API-First
Principles](api-first-principles.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration
Logic](remote-configuration-logic.md), [Role And Permission
Logic](role-permission-logic.md), [Offline-First
Principles](offline-first-principles.md), [Sync Lifecycle
Logic](sync-lifecycle-logic.md), [Native Feature
Strategy](native-feature-strategy.md), [Notifications
Logic](notifications-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), [Support System
Logic](support-system-logic.md), and [Reporting
Logic](reporting-logic.md): this audit keeps the documentation set aligned
before implementation expands the product surface.

## Audit Statement

The documentation set must describe one coherent product:

- **Admin/API is the SaaS authority.**
- **Mobile is the managed NativePHP execution client.**
- **The API is the only trusted communication path between them.**
- **Tenant, permission, billing, feature, config, version, notification,
  support, report, audit, sync, security, and release decisions resolve through
  Admin/API.**
- **Mobile may cache, draft, queue, explain, and use native device capability,
  but it must not create business authority locally.**

Any document that describes a feature, role, module, contract, native
capability, release, risk, report, or support flow should preserve those
statements. When a document needs local detail, it may specialize the rule, but
it must not contradict the rule.

## Audit Outcome

The project documentation consistently supports the two-system concept when the
following clarifications are applied:

- "Admin controls everything" means **the Admin/API system is authoritative**,
  not that every administrator can change every setting.
- "Tenant admin controls" means **delegated tenant-scoped control**, not
  platform authority or cross-tenant access.
- "Mobile owns" means **local UX, cache, drafts, queues, native capability
  prompts, and status presentation**, not tenant, permission, billing, feature,
  config, version, report, support, audit, or sync authority.
- "Offline-first" means **useful local continuity**, not trusted offline
  completion.
- "Feature controlled" means **availability is resolved from policy**, not from
  the existence of a screen or native capability on the device.
- "Remote config" means **safe runtime behavior adjustment**, not permission,
  tenant, billing, or feature entitlement grants.
- "Release" means **controlled exposure of behavior**, not merely deployment or
  app-store submission.

These clarifications remove the main contradiction risk in the documentation:
confusing local ownership with authority.

## Consistency Matrix

| Topic | Consistent project meaning | Documents that own detail |
| --- | --- | --- |
| Final blueprint | The main planning document summarizes product vision, system architecture, Admin/API logic, mobile-client logic, API principles, tenant principles, permission principles, feature flags, remote config, offline sync, NativePHP features, notifications, billing, support, reporting, security, release, and future module expansion. | [Final Optimized SaaS Blueprint](final-optimized-saas-blueprint.md), [Documentation Audit](documentation-audit.md) |
| Two-system architecture | Admin/API is the control plane; mobile is the managed NativePHP client. | [Two-System Boundary Logic](two-system-boundary.md), [SaaS Mobile Admin Platform](saas-mobile-admin-platform.md), [ADR-0001](decisions/0001-admin-api-control-plane-and-native-mobile-client.md) |
| Admin/API responsibilities | Owns tenants, users, roles, permissions, API contracts, feature control, remote config, version rules, notifications, billing, support, reports, audit, conflicts, sync acceptance, and security. | [Admin/API Responsibilities](admin-api-responsibilities.md), [Admin Control Center Logic](admin-control-center-logic.md), [Admin Safety Principles](admin-safety-principles.md) |
| Mobile-client responsibilities | Owns local mobile UX, secure local session presentation, local cache, drafts, queued intents, NativePHP device UX, navigation, permission education, sync status, and feature visibility presentation. | [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX Principles](mobile-ux-principles.md), [Mobile App Shell Logic](mobile-app-shell-logic.md) |
| API-first principles | Mobile communicates only through documented API contracts; API returns user context, tenant context, permissions, flags, config, version rules, errors, sync outcomes, and conflict state. | [API-First Principles](api-first-principles.md), [API Contracts](../contracts/api/README.md) |
| Feature flags | Important mobile features are resolved through global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, emergency, and offline gates. | [Feature Flag Logic](feature-flag-logic.md), [Admin Control Center Logic](admin-control-center-logic.md) |
| Remote config | Safe runtime behavior can vary by global default, tenant override, app version, role, feature state, and offline cache freshness, but config cannot grant authority. | [Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md) |
| Tenancy | Tenant scope is the commercial, security, reporting, support, billing, configuration, cache, sync, and audit boundary. | [Tenant Lifecycle Logic](tenant-lifecycle-logic.md), [Tenant Admin Logic](tenant-admin-logic.md), [Multi-Tenant Mobile Logic](multi-tenant-mobile-logic.md) |
| Permissions | Permissions are API-enforced authority decisions; mobile visibility is only a presentation outcome. | [Role And Permission Logic](role-permission-logic.md), [Target User Roles](user-roles.md) |
| Offline sync | Offline work remains local intent until API acceptance; conflict handling and final truth stay server-side. | [Offline-First Principles](offline-first-principles.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md), [Conflict Resolution Logic](conflict-resolution-logic.md) |
| NativePHP features | Native capability use is feature-flagged, permission-aware, fallback-safe, privacy-bound, and synced through API rules. | [Native Feature Strategy](native-feature-strategy.md), [Camera And Media Logic](camera-media-logic.md), [Scanner Logic](scanner-logic.md), [Geolocation Logic](geolocation-logic.md), [Voice Note Logic](voice-note-logic.md), [Device Network Diagnostics Logic](device-network-diagnostics-logic.md) |
| Notifications | Notification targeting, delivery policy, push registration meaning, inbox state, preferences, deep links, and tenant/permission boundaries stay Admin/API-owned. | [Notifications Logic](notifications-logic.md), [API Notifications Contract](../contracts/api/v1-notifications.md) |
| Billing | Billing and plan state define entitlement ceilings; mobile shows unavailable states without payment or plan authority. | [Billing And Plan Logic](billing-and-plan-logic.md), [API Billing Contract](../contracts/api/v1-billing.md) |
| Support | Support workflows are tenant-scoped, least-privilege, audited, privacy-limited, and explainable from mobile context. | [Support System Logic](support-system-logic.md), [API Support Contract](../contracts/api/v1-support.md) |
| Reports | Reports are scoped by role, tenant, date, privacy, export authority, feature usage, sync health, notification, support, and billing boundaries. | [Reporting Logic](reporting-logic.md), [API Reports Contract](../contracts/api/v1-reports.md) |
| Security | Security is enforced by least privilege, tenant isolation, secure token storage, secure local cache boundaries, audit, privacy defaults, and conservative offline behavior. | [Data Privacy Principles](data-privacy-principles.md), [Authentication Principles](authentication-principles.md), [Mobile App Lock Principles](mobile-app-lock-principles.md), [Audit Logic](audit-logic.md) |
| Risks | Risks must name prevention principles, support meaning, audit meaning, rollback options, and documentation requirements before coding. | [Risk Map](risk-map.md), [Acceptance Principles](acceptance-principles.md) |
| Release principles | Releases separate deployment from exposure, preserve compatibility, roll out by policy, document rollback, and keep Git history traceable. | [Release And Versioning Principles](release-versioning-principles.md), [Mobile Version Control Logic](mobile-version-control-logic.md) |
| Feature dependencies | Major features must resolve authentication, tenant context, permissions, feature flags, remote config, API availability, offline cache, NativePHP permissions, subscription plan, and admin settings before implementation planning. | [Feature Dependency Map](feature-dependency-map.md), [Acceptance Principles](acceptance-principles.md) |

## Cross-Document Rules

All project Markdown files should follow these rules when describing product or
system behavior:

1. Do not describe mobile as the source of tenant, permission, billing, feature,
   config, version, report, support, audit, security, sync, or conflict
   authority.
2. Do not describe admin controls as UI-only. Admin controls are meaningful only
   when Admin/API enforces them.
3. Do not describe feature flags as authorization. Feature flags decide
   availability; permissions decide authority; billing decides entitlement;
   tenant state decides scope.
4. Do not describe remote config as a way to bypass code, permissions, feature
   flags, billing, security, or tenant boundaries.
5. Do not describe offline sync as trusted completion. Offline actions are
   queued intents until API acceptance.
6. Do not describe NativePHP plugin availability as product availability. Native
   capability still needs feature flag, permission, tenant, plan, version,
   privacy, and API rules.
7. Do not describe notifications, reports, support, billing, or diagnostics as
   cross-tenant surfaces unless the platform role and audit boundary are
   explicitly defined.
8. Do not describe release as done when code is merged. Release requires
   versioning, rollout, rollback, documentation, support, audit, and
   user-visible behavior decisions.

## Decision Priority

When a feature or screen depends on multiple controls, documents should use this
priority language unless a more specific canonical document overrides it:

1. Security and tenant isolation fail closed.
2. Platform emergency, maintenance, suspension, or forced-update rules can block
   behavior.
3. Tenant lifecycle and billing state can limit or block behavior.
4. Plan entitlements define the maximum available capability.
5. Feature flags decide whether capability is available for the resolved scope.
6. Permissions decide whether the current user can act or see protected data.
7. Remote config adjusts safe behavior within the allowed capability.
8. Offline state may reduce behavior to cache, drafts, queued intents, or
   read-only presentation.
9. Mobile UX explains the resolved state without creating a local override.

## Audit Checklist

Use this checklist when adding or changing project documentation:

- The document names whether behavior belongs to Admin/API, mobile, or both.
- The document keeps API communication as the only trusted mobile path.
- The document distinguishes feature flags, permissions, billing, remote config,
  and tenant lifecycle.
- The document explains offline behavior as local continuity plus API
  reconciliation.
- The document explains how NativePHP capability is controlled, permissioned,
  and fallback-safe.
- The document preserves tenant isolation, least privilege, audit, support, and
  privacy boundaries.
- The document explains release, rollback, version, and change-history impact
  when behavior can reach users.
- The document stays implementation-neutral unless the user explicitly asks for
  implementation.

## Documentation Maintenance Principle

When two documents appear to disagree, do not implement from either document
until the contradiction is resolved in Markdown. The expected resolution path is:

1. Identify the affected authority boundary.
2. Compare the specific document to the canonical owner in the consistency
   matrix.
3. Update the less-specific document to defer to the canonical owner.
4. Update acceptance, risk, testing, and release notes if user-visible behavior
   or operational safety changes.
5. Commit the documentation change before implementation work begins.

This keeps Mobile Lara documentation useful as an operating contract instead of
a loose pile of feature notes.
