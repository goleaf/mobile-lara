# Mobile App Audit

Audit date: 2026-06-25

Scope: documentation and planning only. This audit compares the current repository direction with the optimized SaaS mobile + admin concept. It does not request or create application logic, schema, migrations, or API endpoints.

## Executive Summary

Mobile Lara should be treated as a two-system SaaS platform:

1. **Admin/API system** - Laravel API plus Livewire admin panel. This is the control plane.
2. **Mobile client system** - Laravel plus Livewire running inside NativePHP Mobile. This is the managed mobile edge client.

The repository already contains substantial mobile-client concepts: Livewire mobile screens, NativePHP services, mobile-local models, local SQLite migrations, offline action infrastructure, permission center ideas, records, media, check-ins, scan history, notifications, and sync status. The admin/API control plane is documented as the source of authority and should be implemented in future slices, not during documentation work.

The product vision is remote control with local resilience. Current mobile assets should be judged by whether they can become admin-controlled, API-enforced, tenant-safe, supportable, and sync-aware. See [Product Vision](product-vision.md).

The product positioning is a tenant-based SaaS control center plus an API-first, feature-controlled, offline-capable mobile workforce/client platform. See [Product Positioning](product-positioning.md).

The audit standard is [Core Product Principles](product-principles.md). Current and future features should be judged by whether admin controls them, mobile uses the API, feature state is controllable, tenant isolation is explicit, offline behavior is useful and bounded, security is default, mobile UX is simple, documentation exists, and expansion is modular.

The documentation-first standard is [Documentation-First Architecture](documentation-first-architecture.md). Audits should verify that every feature, admin control, mobile screen, sync behavior, permission, and risk is documented before implementation.

The Admin Control Center standard is [Admin Control Center Logic](admin-control-center-logic.md). Audits should verify that tenant, user, role, permission, mobile feature, remote config, app-version, maintenance, force-update, sync, notification, report, billing, and support controls are scoped, authorized, auditable, supportable, API-driven, and tenant-safe.

The Feature Flag Logic standard is [Feature Flag Logic](feature-flag-logic.md). Audits should verify that important mobile features have documented flag purpose, priority, disabled mobile states, admin impact, safe rollout, plan limits, support visibility, audit expectations, and offline behavior.

The Remote Configuration Logic standard is [Remote Configuration Logic](remote-configuration-logic.md). Audits should verify that runtime-configurable mobile behavior has allowed config type, defaults, scope, tenant overrides, mobile receive/cache rules, offline behavior, invalid-config fallback, admin safety, support visibility, audit expectations, and rollback.

The Mobile Version Control Logic standard is [Mobile Version Control Logic](mobile-version-control-logic.md). Audits should verify that minimum supported versions, optional updates, forced updates, maintenance mode, outdated-client behavior, store links, update messages, support context, audit expectations, rollback, and old-version protection are documented.

The role standard is [Target User Roles](user-roles.md). Audits should verify platform owner, super admin, tenant admin, tenant manager, support agent, billing manager, mobile user, invited user, suspended user, and guest/pre-login user visibility separately.

The value standard is [SaaS Value Map](saas-value-map.md). Audits should verify that platform owner, tenant business, tenant admin, mobile worker/client, support team, and billing/operations team receive clear value from admin control, mobile access, offline sync, notifications, reports, security, and feature flags without receiving inappropriate visibility or authority.

The boundary standard is [Two-System Boundary Logic](two-system-boundary.md). Audits should verify that Admin/API owns authority, mobile owns local execution, API confirms server-trusted behavior, local cache stays non-authoritative, admin controls mobile behavior remotely, and offline behavior reconciles through the API.

The API-first standard is [API-First Principles](api-first-principles.md). Audits should verify that mobile communicates only with API, API responses are predictable, every mobile feature has an API purpose, API returns operating context, errors are mobile-friendly, sync/conflict behavior is first-class, and tenant boundaries are protected.

The responsibility standard is [Admin/API Responsibilities](admin-api-responsibilities.md). Audits should verify that tenant management, users and permissions, admin panel operations, API contracts, feature control, remote configuration, mobile version rules, notifications, billing/subscription logic, support operations, reporting, audit history, conflict decisions, and security enforcement stay in the control plane.

The mobile responsibility standard is [Mobile Client Responsibilities](mobile-client-responsibilities.md). Audits should verify that mobile UX, secure local session, cache, offline actions, NativePHP device features, navigation, permissions UX, sync display, drafts, local feedback, and feature visibility stay local without owning SaaS authority.

The mobile UX standard is [Mobile UX Principles](mobile-ux-principles.md). Audits should verify mobile-first navigation, simple screens, clear loading/offline states, thumb-friendly controls, minimum typing, fast actions, admin-rule-based feature visibility, secure session behavior, and native permission education.

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

Offline UX Logic is defined in `offline-ux-logic.md`:
offline UX must calmly explain banners, disabled online-only actions,
local drafts, pending indicators, retry, sync success or failure,
saved-local versus synced state, and data-loss prevention whenever
connection changes.

Records/Content Module Logic is defined in `records-content-module-logic.md`:
records are tenant-scoped business content with API-owned lifecycle,
notes, attachments, activity, tags, categories, status, offline draft or
sync behavior, admin controls, permissions, feature flags, audit, and
reporting boundaries.

Search Logic is defined in `search-logic.md`:
search stays tenant-scoped, permission-aware, feature-controlled,
privacy-preserving, and explicit about local-cache limits versus
API-authoritative results, including recent searches, saved filters, filtering,
sorting, scan-to-search, offline limits, and admin-controlled boundaries.

Forms And Drafts Logic is defined in `forms-drafts-logic.md`:
mobile forms must stay simple, validated, autosave-aware, offline-draft
safe, API-submitted, admin-controlled, and explicit about local-save versus
server-accepted state so user work is protected without bypassing authority.

Notifications Logic is defined in `notifications-logic.md`:
notification targeting, delivery policy, push behavior, in-app inbox,
read/unread state, deep links, preferences, offline behavior, and tenant or
permission boundaries must remain Admin/API-authoritative and mobile-safe.

Support System Logic is defined in `support-system-logic.md`:
support requests, support messages, attachments, tenant context, support-agent
visibility, audit, notifications, and offline support drafts must remain
tenant-scoped, least-privilege, privacy-safe, and Admin/API-authoritative.

Billing And Plan Logic is defined in `billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature states,
and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

Reporting Logic is defined in `reporting-logic.md`:
admin measurements, tenant-admin measurements, mobile-visible summaries,
privacy boundaries, date ranges, exports, feature usage, sync health,
notification, support, and billing reports must remain tenant-scoped,
permission-aware, privacy-safe, auditable, and Admin/API-authoritative.

Native Feature Strategy is defined in `native-feature-strategy.md`:
NativePHP capability use, logical service boundaries, browser/development
fallbacks, permission education, admin feature-flag control, native failure
UX, and offline sync behavior must remain feature-scoped, tenant-safe,
privacy-aware, fallback-safe, and Admin/API-authoritative.

Camera And Media Logic is defined in `camera-media-logic.md`:
photo capture, media selection, media preview, record/support attachments,
offline media storage, upload queues, feature-flag control, permission
denial, size limits, and privacy behavior must remain tenant-scoped,
permission-aware, fallback-safe, queue-safe, privacy-safe, and
Admin/API-authoritative.

Sync Lifecycle Logic is defined in `sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

## Product Vision Audit

| Vision question | Product answer |
| --- | --- |
| What problem is solved? | Businesses need controlled mobile workflows without changing the app for every policy, tenant, billing, version, support, or sync decision. |
| Who are admin users? | SaaS owners, platform operators, tenant owners/admins, support, billing, release, and security/compliance users. |
| Who are mobile users? | Tenant-side or field users who need simple permitted workflows, native capabilities, and clear offline/sync states. |
| Why two systems? | Admin/API owns authority and operations; mobile owns focused execution, device capability access, cache, drafts, and queue state. |
| Why admin-controlled mobile? | Mobile state can be stale, offline, copied, tampered with, or running an old version, so server policy must remain final. |
| Why NativePHP + Livewire? | It keeps the product Laravel-first while allowing native mobile capabilities and dynamic Blade/Livewire workflows. |
| Why scalable as SaaS? | Tenant isolation, feature flags, remote config, app-version policy, idempotent sync, observability, support, and billing entitlements turn growth into operations instead of one-off app builds. |

## Documentation-First Architecture Audit

| Documentation principle | Audit lens |
| --- | --- |
| Feature documented before implementation | Does the feature document stakeholder value, roles, boundaries, API purpose, mobile UX, sync, support, billing, audit, risks, and non-goals? |
| Admin control documents mobile effect | Does every admin control explain mobile visibility, copy, disabled/blocked state, offline behavior, sync behavior, support prompt, and rollback/audit expectation? |
| Mobile screen documents API dependency | Does every screen name its boot context, tenant context, permission state, feature/config/version state, payload purpose, errors, and sync states? |
| Sync behavior documents offline and online behavior | Does the plan explain cache, drafts, queued intents, blocked offline actions, replay, idempotency, conflicts, retry, failure, and support context? |
| Permission documents controller | Does each permission name who controls it, where it applies, what it exposes, what mobile receives, and how denials/audit/support work? |
| Risk recorded before coding | Are product, tenant, security, permission, billing, sync, NativePHP, API, support/reporting/audit, user-confusion, and rollout risks recorded with status? |

## Product Positioning Audit

| Position | Audit lens |
| --- | --- |
| SaaS control center | Does the feature have admin control, support visibility, reports, and audit context? |
| Mobile workforce/client platform | Does the feature serve a real mobile workflow instead of duplicating admin UI? |
| API-first system | Does the API enforce the behavior and return explicit state to the client? |
| Offline-capable mobile system | Is local work treated as cache, draft, queue, or confirmed server state? |
| Feature-controlled platform | Can the feature be enabled, limited, rolled back, or version-gated? |
| Tenant-based product | Is every action tenant-scoped and safe for multi-tenant support/reporting? |

If a future feature only satisfies the web/admin side or only the mobile side, it is incomplete for this product.

## Admin Control Center Logic Audit

| Control area | Audit lens |
| --- | --- |
| Tenants | Does tenant status, plan, settings, support tier, feature availability, maintenance, reports, and sync scope stay server-controlled and tenant-safe? |
| Users | Do invitations, activation, suspension, recovery, profile state, device association, session effects, cache effects, and sync effects have API outcomes? |
| Roles | Are responsibility bundles least-privilege, tenant-scoped where needed, support/billing-limited, and auditable when sensitive? |
| Permissions | Are grants, denials, approval requirements, offline rechecks, and mobile capability states enforced server-side? |
| Mobile features | Can features be enabled, disabled, blocked, deprecated, rolled out, emergency-disabled, supported, audited, and rolled back? |
| Remote config | Is config scoped, versioned, compatible, defaulted, reversible, support-visible, and prevented from becoming hidden business logic? |
| App versions | Are supported, recommended update, deprecated, blocked, and internal-only states clear to mobile and support? |
| Maintenance mode | Are platform, tenant, feature, API, sync, and notification maintenance states documented with user-facing and support messages? |
| Force update | Are hard, soft, phased, tenant, platform, feature, and version-specific update rules coordinated with API contracts and NativePHP capabilities? |
| Sync behavior | Are offline eligibility, replay windows, retry limits, stale thresholds, conflict modes, and maintenance blocks controlled by Admin/API? |
| Notifications | Are templates, channels, targeting, quiet hours, priority, escalation, suppression, and delivery visibility tenant-safe and role-safe? |
| Reports | Are report definitions, aggregation, exports, dashboards, and visibility scoped by tenant, role, support, billing, and purpose? |
| Billing | Do plans, quotas, entitlements, trials, renewals, restrictions, failed-payment outcomes, and replay checks map to mobile product states? |
| Support | Are cases, diagnostics, escalation, recovery actions, support visibility, and config-refresh guidance case-scoped and diagnostic-safe? |

## Feature Flag Logic Audit

| Feature flag question | Audit lens |
| --- | --- |
| Why flag it? | Does the feature affect mobile behavior, rollout, tenant variation, plan limits, app versions, NativePHP capability, offline behavior, support, reporting, or risk? |
| What priority applies? | Do safety, plan, global, tenant, role/permission, user, version/device/cohort, and offline decisions resolve predictably? |
| What mobile state appears? | Does API resolve hidden, visible, disabled, blocked, beta, deprecated, update-required, offline-limited, or emergency-disabled state? |
| What is admin impact? | Can admins see affected tenants, users, roles, plans, versions, devices, cohorts, sync, support, reports, billing, audit, and rollback? |
| What rollout path applies? | Is rollout internal, pilot, cohort, plan-limited, tenant-limited, general availability, rollback, or emergency-disabled? |
| What plan limit applies? | Does plan entitlement define the ceiling while flags decide exposure inside that ceiling? |
| What happens offline? | Does replay recheck current flag, plan, permission, version, tenant, maintenance, and emergency state before acceptance? |

## Remote Configuration Logic Audit

| Remote config question | Audit lens |
| --- | --- |
| What is configurable? | Is the config limited to safe runtime behavior such as copy, limits, workflow options, offline/sync behavior, native permission wording, support guidance, notification presentation, version messaging, or tenant presentation? |
| What must not be config? | Does config avoid authorization, billing authority, tenant authority, permission grants, secrets, provider internals, and undocumented business logic? |
| How does mobile receive it? | Does API return resolved values, config version, freshness, compatibility, fallback, and safe next-action state? |
| What can mobile cache? | Is only resolved mobile-safe config cached with version and freshness metadata? |
| What happens offline? | Does mobile use last-known config only for safe behavior and refresh before protected replay? |
| How do tenant overrides work? | Do tenant-specific values override global defaults only inside platform, plan, permission, feature, version, and safety limits? |
| How do admins change it? | Are owner, validation, preview/staging, reason, audit, support visibility, rollback, and affected scope documented? |
| What if config is missing or invalid? | Does mobile use safe defaults or fail closed without exposing internals? |

## Mobile Version Control Logic Audit

| Version-control question | Audit lens |
| --- | --- |
| What minimum version applies? | Is the minimum supported version scoped by platform, channel, tenant, feature risk, API contract, or emergency state? |
| What update state appears? | Does API resolve current, supported, optional update, recommended update, deprecated, force update, blocked, maintenance, internal-only, or unknown state? |
| What does mobile do when outdated? | Does mobile stop protected actions, preserve safe drafts, show update guidance, and revalidate after update? |
| How does maintenance work? | Are scope, expected end, affected operations, retry guidance, support message, and offline behavior documented? |
| How are store links controlled? | Are platform/channel links, update messages, support messages, and message revisions Admin/API-controlled and safe? |
| How are old versions protected? | Are stale API calls, unsafe offline replay, broken NativePHP capability paths, and known security issues blocked or limited? |
| What can happen offline? | Does cached version policy allow only safe offline behavior and require online revalidation before replay? |
| How do admins change policy? | Are affected scope, reason, grace period, audit, support visibility, rollback, and confirmation expectations documented? |

## SaaS Value Map Audit

| Stakeholder | Audit lens |
| --- | --- |
| Platform owner | Does the feature improve governance, rollout safety, commercial control, risk visibility, or platform health? |
| Tenant business | Does the feature improve governed mobile operations without requiring a custom app fork? |
| Tenant admin | Does the feature improve tenant-scoped control, visibility, supportability, or workflow management? |
| Mobile worker/client | Does the feature make permitted mobile work simpler, clearer, more resilient, or better notified? |
| Support team | Does the feature provide safe diagnostic context, feature/config explanation, or faster case resolution? |
| Billing/operations team | Does the feature connect plan, quota, entitlement, usage, notification, or operations state to product access? |

If a feature cannot name stakeholder value, it should not move from idea to implementation planning.

## Two-System Boundary Audit

| Boundary question | Audit lens |
| --- | --- |
| What does Admin/API own? | Tenant, permission, billing, feature, version, notification, report, support, audit, and sync authority remain server-side. |
| What does mobile own? | Mobile UX, NativePHP capability use, local cache, drafts, queues, local metadata, and state presentation stay client-side. |
| What must mobile never own? | Mobile never owns tenant authority, permission authority, billing authority, feature authority, report authority, audit truth, or final sync. |
| What must happen through API? | Server-trusted reads, writes, support actions, notification registration, sync replay, entitlement checks, and audit events. |
| What can be cached locally? | Safe boot snapshots, config copies, server-confirmed resources, drafts, queued intents, sync metadata, and safe local activity with freshness state. |
| What is remotely controlled? | Feature flags, remote config, app-version policy, offline eligibility, sync policy, notification policy, support diagnostics, and entitlements. |
| What happens offline? | Mobile can read cache, create drafts, or queue allowed intents, but API must confirm or reject when online. |

## API-First Principles Audit

| Principle | Audit lens |
| --- | --- |
| Mobile communicates only with API | Does every server-trusted mobile read, write, support action, notification registration, and replay go through API? |
| Predictable responses | Are response states, metadata, errors, and mobile next actions consistent enough for the app to render safely? |
| Clear API purpose | Can each mobile feature name its API purpose before implementation? |
| Operating context | Does API return user, tenant, permissions, feature flags, config, version rules, sync policy, support state, and entitlement outcomes where needed? |
| Mobile-friendly errors | Can validation, auth, permission, tenant, billing, version, maintenance, retry, conflict, and server errors become safe mobile states? |
| Sync and conflict logic | Are offline intents, idempotency, replay acceptance, conflicts, stale data, and retry outcomes treated as API behavior? |
| Tenant boundary protection | Is tenant scope resolved server-side and preserved across responses, support, reports, billing, notifications, and offline replay? |

## Admin/API Responsibilities Audit

| Responsibility | Audit lens |
| --- | --- |
| Tenant management | Are tenant lifecycle, tenant settings, tenant isolation, plan state, and tenant-blocked outcomes server-owned? |
| Users and permissions | Are invitations, activation, suspension, roles, permissions, and account-state restrictions enforced by Admin/API? |
| Admin panel | Are operational controls scoped, auditable, role-aware, and backed by API/server authority? |
| API contracts | Are request/response shapes, validation, authorization, errors, rate limits, idempotency, and versions explicit? |
| Feature control | Can each feature be enabled, disabled, blocked, deprecated, reported, supported, audited, and rolled back centrally? |
| Remote configuration | Are config scope, version, defaults, compatibility, support visibility, and rollback expectations documented? |
| Mobile version rules | Can Admin/API classify builds as supported, recommended update, deprecated, blocked, or internal-only? |
| Notification orchestration | Are templates, channels, targeting, quiet hours, delivery policy, and delivery visibility centrally controlled? |
| Billing/subscription logic | Are plans, quotas, entitlements, trials, invoices, restrictions, and replay checks server-side? |
| Support operations | Are cases, safe diagnostics, escalation, and support visibility scoped by tenant, case, and role? |
| Reporting | Are report definitions, aggregation, exports, and visibility scoped by tenant, role, support, and billing purpose? |
| Audit history | Are sensitive changes and accepted sensitive mobile-originated events recorded as server-trusted history? |
| Conflict decisions | Does API decide accepted, transformed, rejected, duplicated, stale, unauthorized, out-of-policy, or conflicted outcomes? |
| Security enforcement | Are authentication, authorization, tenant scope, token revocation, device trust, rate limits, and secrets policy server-owned? |

## Mobile Client Responsibilities Audit

| Responsibility | Audit lens |
| --- | --- |
| Mobile user experience | Are mobile screens task-focused and clear about allowed, offline, blocked, pending, synced, conflict, and failed states? |
| Secure local session | Are secure storage, local unlock, timeout, logout, and forced-logout UX local without becoming auth authority? |
| Local cache | Are cached boot, capability, config, and resource copies safe, fresh-labeled, and non-authoritative? |
| Offline actions | Are queued actions treated as intents and replayed through API before becoming server truth? |
| NativePHP device features | Are native capabilities feature-scoped, permission-aware, and still subject to Admin/API eligibility? |
| Mobile navigation | Does navigation reflect API-provided account, tenant, feature, version, and offline state? |
| Mobile permissions UX | Are native permission prompts and denial states distinct from SaaS role/permission outcomes? |
| Sync status display | Are last sync, pending, retry, conflict, failed, stale, and offline states visible near affected workflows? |
| Local drafts | Are drafts separate from pending, synced, failed, and conflicted work until API submission succeeds? |
| Local user feedback | Does feedback distinguish local save, queued intent, API acceptance, validation failure, denial, and support need? |
| Feature visibility from admin rules | Does mobile show, hide, disable, block, deprecate, or require update based on API/admin policy only? |

## Current Product Assets

### Mobile Client Assets

The repository contains mobile-oriented classes and views for:

- Authentication, registration, password reset, email verification, sessions, app unlock, PIN, and account deletion.
- Dashboard, create flow, search, profile, settings, developer/debug screens, permissions, storage, and legal pages.
- Offline banner, sync status, network status, toast center, and conflict screens.
- Records, record details, categories, tags, notes, attachments, and activity timeline.
- Media capture/gallery, file manager, voice notes, scanner demo, scan history, location check-in, and check-in history.
- Local notifications and schedules.

These assets should be understood as the mobile-client surface. They must receive business authority through the API and admin-controlled config.

### Local-Mobile Assets

The repository contains local mobile infrastructure for:

- A dedicated `mobile_local` SQLite connection.
- Local settings and health checks.
- Offline actions and conflict fields.
- Local records, media, voice notes, check-ins, scan history, notifications, schedules, categories, tags, notes, attachments, and activity logs.
- Repository/service classes for local storage operations and sync work.

Local storage is useful for offline resilience, but it is not the source of tenant, billing, permission, or feature truth.

### NativePHP Assets

NativePHP Mobile is configured with plugins and services around:

- Browser, camera, device, dialog, file, microphone, network, share, system, permissions, fullscreen, loader, splash screen, in-app update, in-app reviews, screenshot blocking, double-back-close, and locales.

Native capabilities should be exposed through product slices only when admin policy, API behavior, permission copy, and mobile UX are all defined.

## Target Product Gaps

The optimized SaaS product still needs these concepts to be implemented in future work:

| Area | Target behavior |
| --- | --- |
| Admin/API control plane | Tenant, user, role, permission, device, config, feature flag, billing, support, report, audit, and sync policy management. |
| API boot payload | Mobile receives tenant memberships, permissions, feature flags, remote config, app-version policy, and sync policy. |
| App-version control | Admin can block, warn, or require update by platform/version/tenant/cohort. |
| Feature flag rollout | Features can be enabled globally, per tenant, per role, per app version, or per cohort. |
| Billing entitlements | API enforces plan access and mobile only displays allowed/denied states. |
| Support operations | Mobile can create cases with safe diagnostics; admin/support can inspect context and sync health. |
| Reports | Admin can report app adoption, device health, sync health, notification health, usage, support, and billing state. |
| Notification policy | Admin controls templates, channels, quiet hours, device targeting, and delivery health. |
| Conflict governance | API decides conflict state; mobile displays and resolves according to policy; admin can monitor conflict rate. |
| Product vision governance | Every future feature must prove the full loop from admin setting to API enforcement to mobile UX to support/audit visibility. |
| Product positioning governance | Future slices must preserve the combined SaaS control center plus mobile platform positioning rather than drifting into web-only or mobile-only work. |
| Documentation-first governance | Every future slice must document feature behavior, admin mobile effect, mobile API dependency, sync behavior, permission owner, risks, and acceptance criteria before implementation. |
| Admin Control Center governance | Every future admin slice must define control area, authorized role, scope, mobile effect, API context, audit expectation, support meaning, offline behavior, risk, and non-goals before implementation. |
| Feature flag governance | Every future important mobile feature must define flag priority, disabled mobile states, admin impact, rollout path, plan limits, support meaning, audit expectation, offline behavior, and non-goals before implementation. |
| Remote config governance | Every future runtime-configurable behavior must define config type, default, scope, tenant override, mobile cache, offline behavior, invalid-config fallback, safe admin change, support, audit, rollback, and non-goals before implementation. |
| SaaS value governance | Every future slice must prove stakeholder value and connect that value to admin control, mobile access, offline sync, notifications, reports, security, or feature flags. |
| API-first governance | Every future slice must define API purpose, operating context, predictable responses, mobile-friendly errors, sync/conflict behavior, and tenant-boundary protection before endpoint design. |
| Admin/API responsibility governance | Every future slice must identify which control-plane responsibility owns tenant, user, API, feature, config, version, notification, billing, support, report, audit, conflict, or security behavior. |
| Mobile responsibility governance | Every future slice must identify which mobile-client responsibility owns UX, local session, cache, offline queue, NativePHP capability, navigation, permission prompt, sync display, draft, feedback, or feature visibility. |

## Business Logic Audit

Future feature work must not start from a screen. Each feature must be documented and implemented across:

- Admin control behavior.
- API request/response behavior.
- Mobile display behavior.
- Offline behavior.
- Sync/conflict behavior.
- Support behavior.
- Billing/entitlement behavior if applicable.
- Stakeholder value and value proof.
- Two-system boundary ownership.
- Audit behavior.

If one of those perspectives is missing, the feature is not yet product-ready.

## Core Principles Audit

| Principle | Audit question |
| --- | --- |
| Admin controls everything | Is there an admin/API authority story for this behavior? |
| Mobile never bypasses API | Does every server-trusted read/write/sync action go through the API? |
| Every feature can be enabled or disabled | Is disabled/blocked behavior defined? |
| Tenant isolation | Is tenant scope enforced server-side and visible in support/reporting? |
| Offline-first where useful | Is offline behavior helpful, explicit, and non-authoritative? |
| Secure by default | Are authorization, secrets, least privilege, and audit considered before code? |
| API-first communication | Is the API contract known before the mobile workflow becomes durable? |
| Simple mobile UX | Can a mobile user understand the next action without admin knowledge? |
| Documentation-first development | Are docs/ADRs updated before implementation? |
| Modular feature expansion | Does the feature expand as a complete admin/API/mobile/support/audit slice? |

## Role Boundary Audit

| Role boundary | Audit question |
| --- | --- |
| Platform owner vs super admin | Are business ownership decisions separated from operational administration? |
| Tenant admin vs tenant manager | Can managers perform day-to-day work without tenant-wide authority? |
| Support agent | Is support access case-scoped and limited to safe diagnostics? |
| Billing manager | Is billing authority separated from tenant workflow control? |
| Mobile user | Does mobile show only API-granted capabilities? |
| Invited user | Is access blocked until activation is complete? |
| Suspended user | Does suspension override previous role permissions? |
| Guest/pre-login user | Are only public/authentication flows visible? |

## API Audit Principles

Boost documentation confirms Laravel's API routes are stateless and Laravel supports API backends for mobile apps. Future API work should use:

- Token authentication for mobile.
- Server-side policies for authorization.
- Shaped resources for responses.
- Versioned payloads for mobile-dependent behavior.
- Rate limits for auth, sync, support, notifications, and telemetry.
- Idempotency keys for queued offline writes.
- Explicit error categories for validation, forbidden, conflict, stale version, maintenance, and retry-later states.

## Offline-First Audit Principles

Offline-first behavior should be constrained:

- Local SQLite can store cache, drafts, local records, sync metadata, and queued intents.
- Secure tokens must not be stored in SQLite.
- Queued writes are not trusted facts until the API confirms them.
- Mobile should show last sync, pending count, offline reason, and conflict state.
- API must be able to reject stale, unauthorized, duplicate, or out-of-policy queued actions.

## Admin Control Audit Principles

Admin controls should be:

- Tenant-scoped.
- Permission-protected.
- Auditable.
- Reversible where possible.
- Safe by default.
- Designed for support and operations, not just configuration.

Every admin action that can change mobile behavior should include the scope, actor, old value, new value, and reason in the audit model when implemented.

## Risk Register

| Risk | Why it matters | Documentation decision |
| --- | --- | --- |
| Mobile screens drift ahead of server authority | Users may see actions they cannot perform. | API boot config and feature flags must drive mobile navigation and capability display. |
| Offline queue becomes business truth | Server-side authorization and billing can be bypassed conceptually. | Offline actions are intents until API confirmation. |
| Admin flags become untraceable | Support cannot explain changed behavior. | Feature/config changes require audit trails. |
| App-version policy is ignored | Old clients keep calling stale API behavior. | App-version policy is part of mobile boot and API enforcement. |
| Tenant data leaks through support/reporting | SaaS trust is broken. | Tenant scoping applies to support and reports as strongly as to core data. |
| Product grows by screens instead of control loops | The SaaS value becomes unclear and expensive to operate. | Every feature starts from the product vision and full admin/API/mobile/support/audit loop. |
| Product drifts into web-only or mobile-only | One side of the product stops justifying the other. | Evaluate every slice against the product positioning audit lens. |

## Next Planning Slices

1. Managed mobile boot: authentication, tenant selection, remote config, feature flags, app-version policy.
2. Offline records: admin-enabled module, local queue, replay, conflict reporting.
3. Notifications: device registration, templates, delivery policy, local history.
4. Support and diagnostics: safe mobile diagnostics, support case timeline, admin triage.
5. Billing and entitlements: plan-driven capability limits enforced by API.

## Verification Commands For Future Implementation

```bash
php artisan route:list --except-vendor
php artisan test --compact
npm run build
php artisan native:debug --no-interaction
php artisan native:plugin:validate
```

## Sources And References

- [SaaS Mobile Admin Platform Concept](saas-mobile-admin-platform.md)
- [Product Vision](product-vision.md)
- [Product Positioning](product-positioning.md)
- [Core Product Principles](product-principles.md)
- [Documentation-First Architecture](documentation-first-architecture.md)
- [Target User Roles](user-roles.md)
- [SaaS Value Map](saas-value-map.md)
- [Two-System Boundary Logic](two-system-boundary.md)
- [API-First Principles](api-first-principles.md)
- [Admin/API Responsibilities](admin-api-responsibilities.md)
- [Mobile Client Responsibilities](mobile-client-responsibilities.md)
- [ADR-0001](decisions/0001-admin-api-control-plane-and-native-mobile-client.md)
- Laravel Boost application info and documentation search.
- Laravel API routing, authentication, resources, and JSON testing documentation.
- Livewire 4 project skill guidance.
