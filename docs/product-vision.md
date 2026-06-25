# Product Vision

Updated: 2026-06-25

This document defines the product vision for Mobile Lara. It explains the problem, users, system split, admin control model, mobile technology choice, SaaS scalability logic, boundaries, and risks. It is documentation only and does not define database fields, migrations, controllers, components, or application logic.

## Vision Statement

Mobile Lara exists to let businesses operate managed mobile workflows without shipping a new mobile app every time policy, permission, tenant configuration, billing state, support process, or sync behavior changes.

The product is a SaaS control plane plus a managed NativePHP mobile client:

- The Admin/API system decides what is allowed, configured, billable, supported, auditable, and safe.
- The mobile client gives users a fast, native-feeling, offline-capable app that follows those server decisions.

The strongest product promise is remote control with local resilience: administrators can govern mobile behavior centrally, while mobile users can keep working in real-world conditions where connectivity, device state, and app versions vary.

## Vision Contract

| Question | Vision answer |
| --- | --- |
| What problem does the system solve? | Businesses need governed mobile workflows where tenant access, permissions, features, billing, support, notifications, reports, versions, and sync behavior can change centrally without rebuilding the mobile app for every operational decision. |
| Who are the admin users? | Platform owners, super admins, platform operators, tenant admins/managers, support agents, billing managers, release/product managers, and security/compliance reviewers who operate the SaaS control plane. |
| Who are the mobile users? | Tenant-side workers, field users, service teams, contractors, clients, or customers who need a simple permitted mobile app with native capabilities and clear online/offline state. |
| Why both Admin/API and mobile client? | Admin/API owns authority, API contracts, tenant safety, billing, support, audit, and sync decisions; mobile owns local execution, NativePHP capabilities, cache, drafts, queues, and clear task UX. |
| Why must admin settings control mobile? | Mobile can be stale, offline, copied, tampered with, or outdated; only server-side admin policy can safely enforce tenant scope, permissions, feature rollout, version rules, billing, support, and security. |
| Why NativePHP + Livewire? | The product remains Laravel-first, uses Livewire/Blade for dynamic interfaces without a separate frontend framework, and uses NativePHP for mobile shell and native capability access while still communicating through the API. |
| What makes it scalable SaaS? | Tenant isolation, API-first contracts, feature flags, remote config, mobile-version policy, idempotent sync, support visibility, billing entitlements, modular expansion, and documentation-first planning let many tenants, devices, roles, versions, and feature states operate from one control plane. |
| How does admin safety protect the vision? | [Admin Safety Principles](admin-safety-principles.md) require dangerous admin actions to be confirmed, audited, impact-previewed, mobile-previewed, rollback-aware, and tenant-isolated before they affect users. |
| How should mobile feel? | [Mobile UX Principles](mobile-ux-principles.md) require mobile-first navigation, simple screens, clear loading/offline states, thumb-friendly controls, minimum typing, fast actions, secure session behavior, admin-rule-based feature visibility, and native permission education. |

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

This contract is the product north star. Future documentation may add detail, but it should not reverse these answers without a new decision record.

## Product Positioning

Mobile Lara is positioned as a tenant-based SaaS control center with an API-first, feature-controlled, offline-capable mobile workforce/client platform.

This positioning matters because a web-only product would not be enough for native mobile work, and a mobile-only product would not be enough for SaaS governance. The product needs both the administrative control surface and the mobile working surface.

See [Product Positioning](product-positioning.md) for the full positioning model.

## Core Principles

The vision is governed by [Core Product Principles](product-principles.md). In short: Admin/API controls business authority; mobile never bypasses the API; every feature is controllable; tenant isolation is mandatory; offline-first is used where it helps; security is default; communication is API-first; mobile UX stays simple; documentation precedes implementation; and new capabilities expand as modular feature slices.

The documentation standard is defined in [Documentation-First Architecture](documentation-first-architecture.md). It explains how every feature, admin control, mobile screen, sync behavior, permission, and risk must be documented before implementation.

The Admin Control Center model is defined in [Admin Control Center Logic](admin-control-center-logic.md). It explains how admins control tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance, force update, sync behavior, notifications, reports, billing, and support.

The feature flag model is defined in [Feature Flag Logic](feature-flag-logic.md). It explains why important mobile features need flags, how global/tenant/user decisions are prioritized, how disabled features appear on mobile, how admins understand impact, and how rollout and plan limits stay safe.

The remote configuration model is defined in [Remote Configuration Logic](remote-configuration-logic.md). It explains which mobile behavior is safe to configure remotely, how mobile receives and caches config, how offline behavior works, how tenant config overrides global defaults, and how missing or invalid config fails safely.

The mobile version control model is defined in [Mobile Version Control Logic](mobile-version-control-logic.md). It explains how admins control minimum supported versions, optional updates, forced updates, maintenance mode, outdated-client behavior, store links, update messages, and protection from broken old versions.

The target role model is defined in [Target User Roles](user-roles.md). Role boundaries explain who can own, operate, manage, support, bill, use, join, recover, or preview the product.

The SaaS value map is defined in [SaaS Value Map](saas-value-map.md). Value boundaries explain why platform owner, tenant business, tenant admin, mobile worker/client, support team, and billing/operations team need different outcomes from admin control, mobile access, offline sync, notifications, reports, security, and feature flags.

The logical system boundary is defined in [Two-System Boundary Logic](two-system-boundary.md). Boundary rules explain what Admin/API owns, what mobile owns, what mobile must never own, what must happen through the API, what can be cached locally, what admin must control remotely, and what happens offline.

The API contract model is defined in [API-First Principles](api-first-principles.md). API-first rules explain why mobile communicates only with API, why responses should be predictable, why every mobile feature needs an API purpose, and how API returns context, errors, sync/conflict outcomes, and tenant-safe responses.

The Admin/API responsibility model is defined in [Admin/API Responsibilities](admin-api-responsibilities.md). Responsibility rules explain how tenant management, users and permissions, admin operations, API contracts, feature control, remote configuration, mobile version rules, notifications, billing, support, reporting, audit history, conflict decisions, and security enforcement stay in the control plane.

The mobile-client responsibility model is defined in [Mobile Client Responsibilities](mobile-client-responsibilities.md). Responsibility rules explain how mobile user experience, secure local session, local cache, offline actions, NativePHP device features, mobile navigation, permissions UX, sync status display, drafts, local feedback, and feature visibility stay in the mobile client without becoming SaaS authority.

The mobile UX model is defined in [Mobile UX Principles](mobile-ux-principles.md). UX rules explain how NativePHP navigation, simple screens, loading/offline states, thumb-friendly controls, minimum typing, fast actions, secure sessions, feature visibility, and permission education keep mobile users focused.

## Problem The System Solves

Organizations that rely on mobile work usually face the same problem: the people doing the work need a simple app, but the business needs centralized control.

Without a control plane:

- Mobile releases become the only way to change behavior.
- Features are enabled too broadly or hidden only in the UI.
- Permissions, tenant limits, and billing entitlements drift across clients.
- Offline actions become hard to trust after reconnecting.
- Support teams cannot explain what the user saw, which version they ran, or why sync failed.
- Old app versions keep calling APIs after the business logic has changed.
- Operators cannot safely roll out, pause, or reverse mobile features by tenant or cohort.

Mobile Lara solves this by making the backend the business authority and the mobile app the controlled execution surface.

## Admin Users

Admin users are people who manage the SaaS product, tenant operations, mobile behavior, or support process. They do not all have the same permissions.

| Admin user | Primary job |
| --- | --- |
| Platform owner | Sets platform defaults, global feature strategy, version policy, billing model, and operating rules. |
| Super admin | Operates the platform with broad administrative authority under owner policy. |
| Platform operator | Monitors health, incidents, app versions, sync behavior, device state, and rollout progress. |
| Tenant owner | Manages one organization, its users, plan, enabled modules, and tenant-level settings. |
| Tenant admin | Handles day-to-day team membership, roles, permissions, notifications, and local operating settings. |
| Tenant manager | Manages assigned teams and day-to-day workflows inside tenant limits. |
| Support user | Investigates tickets, sync conflicts, device state, recent config changes, and safe diagnostics. |
| Billing operator | Manages plan state, entitlements, quotas, invoices, and account restrictions. |
| Product or release manager | Rolls out features, controls app-version gates, reviews adoption, and reverses risky changes. |
| Security or compliance reviewer | Reviews audit trails, device trust, tenant isolation, and sensitive setting changes. |

The admin experience should be dense, searchable, scoped, and audit-friendly. It is an operations surface, not a marketing page.

## Mobile Users

Mobile users are the people who perform work through the managed app. They may be frontline staff, field workers, service teams, tenant employees, contractors, customers, or any role that needs mobile access to tenant-controlled workflows.

Mobile users need:

- A simple app that shows only what they can use.
- Clear feedback when a feature is disabled, blocked, offline, pending, synced, or conflicted.
- Native capabilities such as camera, files, microphone, network status, local notifications, or device context when a feature requires them.
- Offline-friendly workflows where local work is allowed.
- No exposure to tenant billing, rollout mechanics, feature-flag complexity, or admin configuration internals.

Mobile users should experience policy as clear product behavior, not as admin machinery.

Guest/pre-login, invited, and suspended users are not normal mobile users. They are restricted states with limited visibility and control, described in [Target User Roles](user-roles.md).

## Why The System Needs Both Admin/API And Mobile Client

The product needs two systems because the business problem has two different centers of gravity.

The Admin/API system is the source of truth. It owns tenant isolation, users, roles, permissions, billing entitlements, feature flags, remote config, app-version policy, notifications, reports, support context, audit trails, and sync decisions. Laravel's API surface is the contract that mobile clients consume, and its stateless API routes and token-auth patterns fit mobile-client access.

The mobile client is the user-facing execution layer. It owns the mobile layout, device capabilities, local cache, offline queue, local drafts, pending state, conflict presentation, and native-feeling workflows.

Putting both jobs in one place would create the wrong product:

- If the mobile app owns authority, billing, permissions, tenant safety, and emergency rollback become unreliable.
- If the admin web app is merely wrapped as mobile, the product loses offline behavior, native capability access, and a focused mobile UX.
- If both systems use unrelated stacks, implementation cost, testing burden, and product drift increase.

The split gives the product a clean rule: Admin/API decides; mobile executes and explains.

See [Two-System Boundary Logic](two-system-boundary.md) for the detailed ownership model behind that rule.

## Why Mobile Must Be Controlled By Admin Settings

The mobile client must be controlled by admin settings because mobile state can be stale, offline, copied across devices, or running an old version.

Admin-controlled mobile behavior protects:

- **Tenant isolation** - a mobile client cannot choose its own tenant scope.
- **Permissions** - a hidden or disabled mobile button is not authorization.
- **Billing** - plan limits and entitlements must be enforced by the API, not local UI.
- **Rollouts** - features need staged release, emergency disablement, and tenant-specific enablement.
- **App versions** - old clients may need warnings, reduced capability, or hard blocks.
- **Support** - support teams need to know which config, feature flags, version policy, and sync policy applied.
- **Security** - device trust, forced logout, and sensitive capability access must remain centrally revocable.
- **Compliance** - important admin changes need audit context and should not be hidden in client code.

Admin settings should not make the mobile app feel unstable. They should make it predictable: the app receives policy, renders the correct capability state, stores safe local work when allowed, and asks the API to confirm every business-sensitive action.

## Why NativePHP + Livewire Is The Mobile Approach

NativePHP + Livewire is the chosen mobile approach because the product is Laravel-first and needs a native-capable client without creating a second frontend architecture.

NativePHP provides the mobile shell and native bridge:

- It lets a Laravel app run as a mobile application.
- It gives access to native capability plugins when product slices require them.
- It keeps native work tied to a Laravel product instead of creating a separate mobile codebase by default.

Livewire provides the interaction model:

- It keeps UI behavior close to Laravel validation, authorization, resources, tests, and server-side rules.
- It supports dynamic Blade interfaces without introducing React, Vue, Inertia, Ionic, or Capacitor for this product.
- It allows admin and mobile surfaces to share Laravel conventions while still having different UX.

This approach is not chosen so the mobile app can bypass the API. The mobile client still works through the API for SaaS authority. NativePHP + Livewire is chosen to reduce stack sprawl, keep the product Laravel-native, and ship mobile workflows with native capability access and server-aligned behavior.

## What Makes The Product Scalable As SaaS

The product scales as SaaS when growth is handled through configuration, contracts, isolation, and operations instead of one-off app changes.

Scalable SaaS principles:

- **Tenant isolation** - every admin action, API request, report, support view, notification, and sync decision is scoped.
- **Config-driven behavior** - feature flags, remote config, app-version policy, notification policy, and sync policy can change without a mobile release.
- **Control-center governance** - tenants, users, roles, permissions, features, config, versions, maintenance, force update, sync, notifications, reports, billing, and support are controlled through scoped, authorized, auditable admin operations.
- **Feature-flag governance** - important mobile features resolve global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, emergency, and offline decisions into mobile-safe API states.
- **Remote-config governance** - runtime behavior is scoped, versioned, validated, cached carefully, tenant-aware, and safe when offline, missing, or invalid.
- **Mobile-version governance** - minimum supported versions, optional updates, forced updates, maintenance state, store links, update messages, and old-version protection are controlled by Admin/API policy.
- **API contracts** - mobile clients consume versioned, shaped API responses instead of raw internal models.
- **API-first purpose** - every mobile feature has a clear API purpose, predictable response shape, mobile-friendly errors, and tenant-safe context.
- **Entitlement enforcement** - billing and plan rules are enforced server-side and surfaced to mobile as allowed or denied capability.
- **Idempotent sync** - offline writes replay safely with idempotency keys and explicit conflict handling.
- **Operational visibility** - admins can observe app adoption, device trust, sync health, notification health, support load, and feature rollout state.
- **Role separation** - SaaS owners, tenant admins, support, billing, and mobile users each see only the controls or workflows they need.
- **Value separation** - platform, tenant, mobile, support, and billing stakeholders receive different value from the same control plane without receiving the same visibility or authority.
- **Responsibility separation** - Admin/API responsibilities remain explicit so tenant, permission, API, feature, version, billing, support, reporting, audit, conflict, and security decisions do not drift into mobile-local logic.
- **Local execution separation** - Mobile-client responsibilities remain explicit so UX, cache, drafts, offline queues, NativePHP features, sync display, and feedback can improve resilience without claiming authority.
- **Progressive rollout** - features can move from internal tenant to limited tenant to general availability with rollback.
- **Documentation discipline** - product decisions, risks, boundaries, and architecture decisions stay written before implementation.
- **Documentation-first architecture** - admin effects on mobile, mobile API dependencies, sync behavior, permission ownership, and risks are written before coding.

Scalability here is not only traffic volume. It is the ability to serve many tenants, devices, roles, app versions, feature states, and support cases without losing control or trust.

## Product Principles

The full principle set lives in [Core Product Principles](product-principles.md). The vision-level summary is:

1. Admin/API is the source of business authority.
2. Mobile is a managed local executor, not a policy engine.
3. Every feature can be enabled, disabled, rolled out, or blocked.
4. Tenant isolation is the SaaS trust boundary.
5. Offline behavior is useful only when it stays under API authority.
6. Security is a default product behavior.
7. API-first communication is the product contract.
8. Mobile UX stays simple and honest.
9. Documentation comes before product-critical implementation.
10. Feature expansion is modular and complete across admin, API, mobile, support, audit, and sync.

Boundary-level summary:

1. Admin/API owns business authority.
2. Mobile owns local execution and clear state presentation.
3. API is the only path for server-trusted behavior.
4. Local cache, drafts, and queues are useful but not authoritative.
5. Offline mode is constrained by the latest server policy and must reconcile through API.

## SaaS Value Map

The full value map lives in [SaaS Value Map](saas-value-map.md). The vision-level summary is:

1. Platform owner value is strategic control, SaaS governance, rollout safety, and risk visibility.
2. Tenant business value is governed mobile operations without custom app forks.
3. Tenant admin value is tenant-scoped control over users, modules, notifications, reports, support, and sync health.
4. Mobile worker/client value is a simple permitted app with useful offline behavior and clear status.
5. Support team value is safe diagnostic context for faster issue resolution.
6. Billing/operations value is connecting plan, quota, entitlement, and usage state to product access.

Every future feature should identify which stakeholder receives value and which capability proves it: admin control, mobile access, offline sync, notifications, reports, security, feature flags, or a deliberate combination.

## Boundaries

This product vision does not create or imply immediate implementation of:

- Database fields.
- Migrations.
- API controllers.
- Livewire components.
- Policies.
- Jobs or services.
- Billing provider integrations.
- Push provider integrations.
- Native plugin integrations.
- Application logic.

Those belong in future implementation slices with explicit acceptance criteria and tests.

## Risks

| Risk | Product response |
| --- | --- |
| Admin settings become too complex | Keep controls layered by global, tenant, role, user, device, feature, version, and sync scope. |
| Mobile users see confusing blocked states | Mobile copy should explain the state without exposing admin internals. |
| Offline queues are trusted too much | Treat queued writes as intents until the API accepts them. |
| Feature flags become invisible risk | Require audit and support visibility for behavior-changing settings. |
| Native permissions feel invasive | Request permissions only when needed and explain the feature purpose. |
| SaaS scale becomes only a technical concern | Include billing, support, rollout, documentation, and operations in every feature slice. |

## Vision Success Test

The product vision is working when an admin can safely change a mobile capability for a tenant or cohort without publishing a new app build, the mobile app reflects that policy clearly, the API enforces it even for stale clients, offline work reconciles through explicit sync rules, and support can explain the outcome from version, config, audit, and device context.
