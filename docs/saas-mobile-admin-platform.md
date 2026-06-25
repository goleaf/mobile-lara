# SaaS Mobile Admin Platform Concept

Updated: 2026-06-25

This document is the canonical product and system concept for Mobile Lara. It describes product intent, business logic, admin control logic, mobile-client behavior, API principles, offline-first principles, and SaaS operating boundaries. It is a planning document only. It does not define database fields or implementation details.

## One-Line Product

Mobile Lara is a SaaS control plane for managed NativePHP mobile apps: administrators configure what each tenant and user can do, and the mobile client executes those rules through API-driven, offline-capable workflows.

## Product Vision

The product vision is remote control with local resilience.

Mobile Lara solves the problem of mobile teams needing simple, native-feeling workflows while the business needs centralized control over tenants, roles, permissions, billing, app versions, support, notifications, reports, sync behavior, and feature rollout. The admin/API system gives the organization a safe operating surface. The mobile client gives users a focused app that works through policy and can keep useful local work moving when connectivity is imperfect.

See [Product Vision](product-vision.md) for the plain-language vision, user definitions, technology rationale, and SaaS scale principles.

## Product Positioning

Mobile Lara should be understood through six product positions:

| Position | Meaning |
| --- | --- |
| SaaS control center | Admin/API centrally controls tenants, users, permissions, billing, features, versions, reports, support, notifications, and sync policy. |
| Mobile workforce/client platform | NativePHP mobile users get focused workflows, native capabilities, clear status, and offline-capable work. |
| API-first system | The API is the contract between central authority and mobile execution. |
| Offline-capable mobile system | Local cache, drafts, queues, and sync state keep mobile work useful without making the device authoritative. |
| Feature-controlled platform | Features are enabled, limited, rolled out, blocked, or reverted through policy. |
| Tenant-based product | Tenant scope is the commercial, security, reporting, billing, and configuration boundary. |

This combined positioning is stronger than web-only because mobile work needs native capability access, local state, and offline resilience. It is stronger than mobile-only because SaaS operations need central tenant governance, billing enforcement, support visibility, reporting, audit, and rollback.

See [Product Positioning](product-positioning.md) for the full positioning rationale.

## Core Product Principles

All product and system design should follow [Core Product Principles](product-principles.md):

1. Admin controls every business-sensitive capability.
2. Mobile never bypasses the API.
3. Every feature can be enabled or disabled.
4. Tenant isolation is non-negotiable.
5. Offline-first is used where it helps, not everywhere.
6. Security is default.
7. Communication is API-first.
8. Mobile UX stays simple.
9. Documentation comes before implementation.
10. Feature expansion is modular.

Documentation-first architecture is detailed in [Documentation-First Architecture](documentation-first-architecture.md). Every feature must document its admin mobile effect, mobile screen API dependency, sync/offline behavior, permission owner, risks, and non-goals before implementation.

Admin Control Center logic is detailed in [Admin Control Center Logic](admin-control-center-logic.md). Every admin control must define its control area, role authority, scope, mobile effect, API context, audit expectation, support meaning, offline behavior, risk, and implementation boundary before coding.

## Target User Roles

The logical role model lives in [Target User Roles](user-roles.md). The main roles are:

- Platform owner.
- Super admin.
- Tenant admin.
- Tenant manager.
- Support agent.
- Billing manager.
- Mobile user.
- Invited user.
- Suspended user.
- Guest/pre-login user.

These roles define who can see and control platform policy, tenant settings, billing, support, mobile workflows, invitations, suspension recovery, and pre-login flows.

## SaaS Value Map

The stakeholder value model lives in [SaaS Value Map](saas-value-map.md). The main value recipients are:

- Platform owner: scalable SaaS governance, rollout safety, commercial control, and risk visibility.
- Tenant business: governed mobile operations without custom app forks.
- Tenant admin: day-to-day tenant control over users, modules, notifications, reports, support, and sync health.
- Mobile worker/client: simple permitted workflows with clear mobile, offline, notification, and sync states.
- Support team: safe diagnostics, feature/config context, and faster issue resolution.
- Billing/operations team: plan, quota, entitlement, usage, notification, and operational visibility.

Every future product slice should name which stakeholder receives value and which system capability proves it: admin control, mobile access, offline sync, notifications, reports, security, feature flags, or a deliberate combination.

## Two-System Boundary

The detailed ownership model lives in [Two-System Boundary Logic](two-system-boundary.md). The summary is:

- Admin/API owns SaaS authority: tenants, users, roles, permissions, billing, feature flags, remote config, app-version policy, notifications, reports, support, audit, API contracts, and sync decisions.
- Mobile owns local execution: NativePHP capability use, mobile UX, local cache, drafts, queued intents, sync status, conflict presentation, and user-facing offline state.
- Mobile must never own tenant authority, permission authority, billing authority, feature authority, app-version policy, report authority, support authority, audit truth, or final sync decisions.
- Server-trusted reads, writes, replay, entitlement checks, support actions, notification registration, reports, and audit events must happen through API.
- Local cache is allowed only as cache, draft, pending intent, safe local metadata, or server-confirmed copy with freshness state.
- Offline mode can keep useful work moving only inside admin/API policy and must reconcile through API when connectivity returns.

## API-First Principles

The detailed API contract model lives in [API-First Principles](api-first-principles.md).

Mobile communicates only with API. API responses should be predictable. Every mobile feature needs a clear API purpose. API returns permissions, feature flags, remote config, version rules, user context, tenant context, sync policy, support state, notification policy, and entitlement outcomes where applicable. API errors should be mobile-friendly, sync and conflict behavior should be first-class, and tenant boundaries must be protected server-side.

This document does not design endpoints in detail. It defines the principle that API is the product contract between Admin/API authority and mobile execution.

## Admin/API Responsibilities

The detailed control-plane responsibility model lives in [Admin/API Responsibilities](admin-api-responsibilities.md).

Admin/API logically owns tenant management, users and permissions, admin panel operations, API contracts, feature control, remote configuration, mobile version rules, notification orchestration, billing/subscription logic, support operations, reporting, audit history, conflict decisions, and security enforcement.

Mobile may consume, cache, display, and act on those decisions, but it must not become the place where those decisions are created or trusted.

## Admin Control Center Logic

The detailed admin-control model lives in [Admin Control Center Logic](admin-control-center-logic.md).

The Admin Control Center is the operational surface where authorized roles control tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance mode, force update, sync behavior, notifications, reports, billing, and support.

Admin controls should be scoped, authorized, explainable, auditable, reversible where possible, tenant-safe, documented first, and delivered to mobile through API outcomes.

## Mobile Client Responsibilities

The detailed managed-client responsibility model lives in [Mobile Client Responsibilities](mobile-client-responsibilities.md).

The mobile client logically owns mobile user experience, secure local session, local cache, offline actions, NativePHP device features, mobile navigation, mobile permissions UX, sync status display, local drafts, local user feedback, and feature visibility based on admin rules.

It must not own tenant authority, billing authority, permission authority, global configuration authority, feature authority, API contract authority, app-version policy, notification targeting, support authority, reporting authority, audit truth, conflict decisions, or security enforcement.

## System Split

### Admin/API System

The Admin/API system is the authoritative backend.

Its users include SaaS owners, platform operators, tenant owners, tenant admins, support users, billing operators, product/release managers, and security or compliance reviewers.

It owns:

- Tenant lifecycle and tenant-level entitlements.
- User lifecycle, roles, permissions, team membership, and device access.
- Remote config, feature flags, rollout policy, app-version policy, and maintenance mode.
- Notification policy, report definitions, support queues, billing state, usage limits, and audits.
- API contracts, idempotency, rate limits, conflict policy, and sync orchestration.

The admin panel is built with Livewire and should stay an operational tool, not a marketing surface. It must prioritize dense, searchable, audit-friendly controls.

### Mobile Client System

The mobile client is a Laravel + Livewire app running inside NativePHP Mobile.

Its users are the people doing tenant-side or field work. They need simple allowed workflows, clear blocked/offline/pending states, native capabilities when required, and no exposure to admin configuration internals.

It owns:

- Mobile UX and local state presentation.
- Native capability access through NativePHP plugins.
- Secure token storage, app lock, local SQLite storage, offline queues, local drafts, local records, media metadata, and sync status.
- API consumption, conflict surfacing, retry behavior, and local notifications.

The mobile client must not invent tenant rules, billing rules, permissions, or feature availability. It renders the current server policy and gracefully degrades when offline.

The client is controlled by admin settings because mobile state can be stale, offline, copied between devices, or running an old app version. Server policy must stay final for business-sensitive behavior.

## Product Architecture

```text
SaaS owner
  |
  v
Admin/API control plane
  - tenants
  - users and roles
  - remote config
  - feature flags
  - versions
  - billing
  - notifications
  - reports
  - support
  - sync policy
  |
  v
Versioned API
  - auth
  - config
  - domain resources
  - offline queue replay
  - conflicts
  - telemetry
  |
  v
NativePHP mobile client
  - Livewire screens
  - native plugins
  - local SQLite
  - secure storage
  - offline action queue
```

## Business Logic Model

The business logic should be organized around a few stable concepts.

| Concept | Meaning | Authority |
| --- | --- | --- |
| Tenant | Paying customer workspace or organization. | Admin/API |
| User | Human account that may belong to one or more tenants. | Admin/API |
| Role | Named permission bundle inside a tenant. | Admin/API |
| Permission | Atomic ability checked by API and admin actions. | Admin/API |
| Device | Mobile installation that can be trusted, blocked, expired, or required to update. | Admin/API |
| Feature | Business capability that can be enabled, disabled, limited, or rolled out. | Admin/API |
| Remote config | Runtime settings delivered to mobile clients. | Admin/API |
| App version policy | Minimum supported, recommended, blocked, or phased rollout version. | Admin/API |
| Offline action | Mobile-side queued intent to replay through the API. | Mobile creates, API accepts/rejects |
| Conflict | Server decision that a queued local action cannot be applied as-is. | API decides, mobile resolves or displays |

## Feature Logic

Every mobile feature should be described by six decisions before implementation:

1. **Stakeholder value** - Which stakeholder benefits, and what product outcome improves?
2. **Eligibility** - Which tenants, plans, app versions, devices, roles, or users can access it?
3. **Configuration** - Which remote settings change behavior without a mobile release?
4. **Offline behavior** - Is the feature read-only offline, draftable offline, queueable offline, or online-only?
5. **Sync behavior** - What is idempotent, conflict-prone, retryable, or discardable?
6. **Audit behavior** - Which admin-visible events prove who did what, when, from which device?

No feature should exist only as a mobile screen. Each feature needs an admin story, API story, mobile story, support story, and failure story.

Each feature also needs a boundary story: what Admin/API owns, what mobile owns, what must happen through API, what can be cached, what remote admin controls, and what happens offline.

Each feature also needs an API story: why mobile talks to API, what operating context the API returns, what predictable states/errors mobile can show, how sync/conflict works, and how tenant boundaries are protected.

Each feature also needs a responsibility story: which Admin/API responsibility owns tenant, permission, API, feature, config, version, notification, billing, support, report, audit, conflict, or security behavior.

Each feature also needs a mobile responsibility story: which mobile-client responsibility owns UX, local session, cache, offline queue, NativePHP capability, navigation, permission prompt, sync display, draft, feedback, or feature visibility.

Each feature also needs a documentation story: which docs record the feature, admin control effect on mobile, mobile API dependency, offline/online sync behavior, permission owner, risks, and acceptance criteria before coding.

Each feature also needs an Admin Control Center story: which admin control area owns the behavior, who can change it, what scope applies, what mobile effect appears, what API context changes, what is audited, what support can explain, and what happens offline.

## Admin Control Logic

Admin controls should follow a layered model:

1. **Global SaaS controls** - Platform-wide defaults, supported app versions, incident banners, global maintenance, global feature kill switches.
2. **Tenant controls** - Plan, quotas, enabled modules, data retention, notification defaults, support SLA, billing state.
3. **Role controls** - Permissions and approval requirements for specific work.
4. **User controls** - Invitations, suspension, MFA/app-lock requirements, notification preferences, support context.
5. **Device controls** - Device trust, last seen, app version, push token state, blocked status, forced logout.
6. **Feature controls** - Flags, staged rollout cohorts, limits, remote copy, risk gates, rollback policy.
7. **Sync controls** - Allowed offline queue types, retry limits, conflict mode, stale-data limits, and server-side replay windows.

Admin changes must be auditable and reversible where possible. Dangerous controls should require explicit confirmation and should produce an audit event that names the actor, scope, old value, new value, and reason.

The detailed checklist for these controls lives in [Admin Control Center Logic](admin-control-center-logic.md).

## Mobile User Flows

### First Launch

1. Mobile app opens inside NativePHP.
2. App checks local secure storage for tokens and local SQLite for cached boot state.
3. If no valid session exists, the app shows login/register/forgot-password flows.
4. After authentication, the app requests boot config: tenant memberships, user permissions, app-version policy, feature flags, remote config, sync policy, and notification requirements.
5. App initializes local state and renders only permitted screens.

### Daily Use

1. User opens app and sees the dashboard.
2. Mobile loads cached state immediately when safe.
3. App refreshes boot config and pending sync state when online.
4. Actions either execute online immediately or enter the offline queue if the feature allows queuing.
5. User sees sync status, offline banner, pending actions, and conflicts without needing admin knowledge.

### Offline Work

1. Network state changes to offline or fallback connectivity fails.
2. App keeps permitted offline-capable screens usable.
3. Local actions are stored as intent records, not as trusted server facts.
4. App shows pending count and last sync state.
5. When connectivity returns, queued actions replay through the API with idempotency keys.
6. Server accepts, rejects, or returns conflicts. Mobile updates local state from server response.

### Forced Update

1. App requests boot config.
2. API returns version policy.
3. If app version is blocked, the mobile client prevents normal operation and shows update instructions.
4. If app version is deprecated, the mobile client warns while allowing limited operation.
5. Admin can target version policy by platform, tenant, risk level, or rollout cohort.

### Support Flow

1. User opens support inside mobile.
2. App includes safe diagnostic context: tenant, user, app version, device class, sync status, and recent non-sensitive errors.
3. API creates a support case.
4. Admin/support user sees case timeline, device state, plan, recent config changes, and sync conflicts.
5. Support actions can request logs, ask user to retry, force config refresh, or escalate.

## API Principles

The API must be boring, explicit, and versioned.

- Use `routes/api.php` and stateless API middleware for mobile API routes.
- Use token authentication appropriate for a first-party mobile client.
- Treat the API as the stable contract between admin authority and mobile execution.
- Return shaped resources, not raw models.
- Version responses that mobile clients depend on.
- Include server time, config version, and sync cursor metadata where relevant.
- Use idempotency keys for replayable write actions.
- Rate-limit auth, sync replay, support, notification registration, and high-volume telemetry endpoints.
- Make authorization server-side on every read and write.
- Never trust mobile-provided tenant IDs, role names, permission flags, price/plan data, or feature flags.
- Prefer additive API changes; deprecate with app-version policy before removing behavior.
- Return explicit error categories: validation, unauthorized, forbidden, conflict, stale client, maintenance, rate limited, and retry later.

## Offline-First Principles

Offline-first does not mean serverless. It means useful local work with server authority.

- Local SQLite stores cache, drafts, pending actions, local-only history, and safe metadata.
- Secure tokens and secrets belong in secure storage, not SQLite.
- Every queued write is an intent that the API may accept, transform, reject, or mark conflicted.
- Queued actions need stable client IDs and idempotency keys.
- Sync should be incremental, resumable, and visible to the user.
- Conflicts should be explicit admin/support objects when they affect business data.
- Offline screens should show freshness and limitations.
- The app should never hide that a server decision is pending.

## SaaS Operating Principles

### Multi-Tenancy

Tenant isolation is a product guarantee, not just a query filter. Every admin action, API request, mobile boot payload, report, support view, and notification must be scoped to the correct tenant.

### Billing And Entitlements

Billing controls feature access, usage limits, support level, data retention, and rollout eligibility. Mobile clients should receive entitlement results, not billing internals.

### Observability

The admin system should expose:

- App version adoption.
- Active devices and blocked devices.
- Sync health and conflict rate.
- Notification delivery health.
- Feature flag rollout status.
- API error rate by tenant and app version.
- Support case load and response status.

### Security

Security is layered:

- API authentication and token rotation.
- Device trust and forced logout.
- Server-side policies and tenant scope.
- Role and permission checks.
- Audit logs for admin and sensitive mobile actions.
- App-lock or PIN/biometric controls for local access.
- No secrets in code, docs, local SQLite, or public logs.

### Rollout Discipline

Every major feature should be introduced as:

1. Backend/API capability hidden behind admin-only control.
2. Admin control and audit trail.
3. Mobile UI behind a feature flag.
4. Internal tenant rollout.
5. Limited tenant rollout.
6. General availability.
7. Report/support readiness.

### SaaS Scalability

The product scales as SaaS by moving variation into tenant-scoped configuration and versioned contracts instead of custom mobile builds:

- Tenant isolation keeps customer data and controls separate.
- The value map keeps platform owner, tenant business, tenant admin, mobile user, support, and billing outcomes explicit as modules expand.
- Remote config and feature flags let operators change behavior without a store release.
- App-version policy controls stale clients before they damage API contracts.
- Idempotent sync makes offline work replayable.
- Observability lets operators see adoption, device health, sync failures, notification delivery, support load, and rollout status.
- Billing entitlements are enforced by the API and presented by mobile as clear capability state.

## Optimized Product Slices

The product should be built in slices that each prove the full control loop.

### Slice 1: Managed Mobile Boot

Admin defines tenant, user, feature flags, remote config, and minimum app version. Mobile authenticates, fetches boot config, renders permitted navigation, and blocks if version policy requires it.

### Slice 2: Offline Records

Admin enables a records module for selected tenants. Mobile creates local records offline, queues sync intents, replays through the API, and exposes conflicts. Admin sees sync status and conflict reports.

### Slice 3: Notifications

Admin defines notification templates, channels, quiet hours, and tenant defaults. Mobile registers device tokens, receives remote/local notification behavior, and stores safe local notification history.

### Slice 4: Support And Diagnostics

Mobile sends support tickets with safe diagnostics. Admin sees user, tenant, device, app version, recent sync status, and recent config changes.

### Slice 5: Billing And Plan Controls

Admin maps plan state to entitlements and quotas. Mobile only sees allowed/denied capability state and user-friendly upgrade/contact-support messaging.

## Boundaries

Do not implement these during documentation/planning work:

- Database fields.
- Migrations.
- API controllers.
- Livewire admin screens.
- Policies.
- Billing provider integrations.
- Push provider integrations.
- Native plugin integrations.
- Application logic.

Those belong in future implementation prompts with tests and acceptance criteria.

## Risks

| Risk | Mitigation |
| --- | --- |
| Mobile duplicates server business rules | Keep mobile as renderer/executor of API config and permissions. |
| Offline data becomes trusted fact | Treat queued actions as intents until API confirms. |
| Feature flags become untraceable | Require admin audit events for flag changes. |
| Tenants leak data through reports/support | Scope every report and support surface to tenant and role. |
| App versions fragment API behavior | Version boot payloads and deprecate through app-version policy. |
| Billing controls become UI-only | Enforce entitlements in API policies and admin actions. |
| Native permissions feel invasive | Request permissions just-in-time with admin-configured purpose copy. |

## Success Criteria

The concept is successful when a tenant admin can change a mobile capability without shipping a mobile build, the mobile app respects that change after config refresh, the API enforces it even if the mobile app is stale, support can explain what happened from audit and sync records, billing can explain entitlement outcomes, the value map makes clear who benefited from the feature, and the boundary map makes clear which system owned every decision.
