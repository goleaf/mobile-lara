# Remote Configuration Logic

Updated: 2026-06-26

This document defines the remote configuration logic for Mobile Lara. It explains what behavior should be remotely configurable, how mobile should receive and cache config, what happens offline, how tenant-specific config overrides global defaults, how admins should safely change config, and how mobile should handle missing or invalid config. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, Filament resources, policies, jobs, services, providers, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md), [API-First Principles](api-first-principles.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX Principles](mobile-ux-principles.md), [Core Product Principles](product-principles.md), [Documentation-First Architecture](documentation-first-architecture.md), and [Admin Safety Principles](admin-safety-principles.md): remote config is a server-controlled product decision that mobile consumes through API to create stakeholder value without granting authority, supports safe UX copy and limits, and dangerous config changes require confirmation, audit history, impact preview, rollback, and tenant-isolated scope.

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

## Remote Configuration Statement

Remote configuration lets Admin/API adjust mobile behavior without publishing a new mobile build.

Remote config should control safe runtime variation: copy, thresholds, limits, workflow options, offline eligibility, sync behavior, native permission wording, maintenance/support messages, and feature behavior that has already been documented.

Remote config must not become hidden business logic, authorization, billing authority, permission authority, tenant authority, or a substitute for versioned API contracts.

## Remote Configuration Decision Contract

Every remote configuration value should be documented before implementation because it changes mobile behavior after the app has shipped. Admin/API owns the resolved decision; mobile receives only safe, compatible, cached configuration outcomes through API.

| Decision area | Principle | Required outcome |
| --- | --- | --- |
| Configurable behavior | Remote config is for safe runtime variation such as copy, thresholds, limits, workflow options, offline eligibility, sync timing, native permission wording, notification presentation, support guidance, maintenance text, update text, and tenant presentation. | Config changes adapt mobile UX without redefining authorization, billing, tenant authority, security, validation, or API contracts. |
| Mobile receive path | Mobile receives resolved config through API boot/context, targeted refresh, login, resume, tenant switch, app-version change, and sensitive workflow refresh where needed. | Mobile gets config values, config version, freshness, compatibility, fallback behavior, and user-safe next actions rather than raw admin layers. |
| Mobile cache | Mobile may cache only resolved mobile-safe config with version, freshness, tenant scope, and fallback metadata. | Cache improves startup and offline stability, but cached config never becomes current authority for protected actions. |
| Offline behavior | Offline mobile uses last-known config for safe presentation and previously allowed offline behavior only. | Mobile labels stale context where needed, avoids newly protected actions, refreshes before replay, and lets API recheck current tenant, user, feature, permission, billing, version, maintenance, and sync policy. |
| Tenant override | Tenant-specific config may override global defaults only inside platform safety, plan, feature flag, permission, app-version, support, and emergency limits. | Tenant variation improves fit without allowing tenants or users to bypass global safety, plan ceilings, disabled features, blocked versions, or permissions. |
| Safe admin change | Admin changes need owner, purpose, scope, default, validation, preview or staged rollout where useful, support meaning, audit expectation, compatibility check, and rollback path. | Admins understand affected tenants, users, roles, versions, features, offline/sync behavior, reports, billing, support, and risk before activation. |
| Missing or invalid config | Missing, incompatible, expired, or invalid config must fall back safely or fail closed depending on risk. | Mobile avoids crashes, shows user-friendly fallback states, records safe diagnostic context, and never receives broader access because config is absent or malformed. |

This contract is intentionally principle-level. It does not create config storage, schemas, migrations, endpoints, validation classes, policies, Filament resources, Livewire components, jobs, services, provider integrations, or application logic.

## What Should Be Remotely Configurable

Remote config should be used for behavior that changes often, differs by tenant, supports rollout, improves supportability, or helps mobile adapt safely.

Good remote-config candidates:

| Config type | Examples | Principle |
| --- | --- | --- |
| Mobile copy | Empty states, disabled-feature messages, support prompts, update text, maintenance text. | Copy can change without app release when it stays product-safe. |
| Limits and thresholds | Page size, upload size guidance, retry copy, stale-data threshold, warning thresholds, soft limits. | Limits must remain inside API/server authority. |
| Workflow options | Optional steps, default filters, visible tabs, checklist order, lightweight preferences. | Options should not bypass permissions or validation. |
| Offline behavior | Read-only, draft-only, queueable, online-only, stale warning, retry timing guidance. | Offline policy is server-owned and mobile-presented. |
| Sync behavior | Retry windows, freshness messaging, conflict explanation, metered-network guidance, manual sync prompts. | API still decides replay and conflict outcomes. |
| Native permission copy | Camera, microphone, file, notification, network, device, location, scanner purpose text. | Native permission text must match enabled features. |
| Support behavior | Support instructions, diagnostic copy, safe troubleshooting steps, contact routing labels. | Support config must not expose secrets or private tenant data. |
| Notification behavior | Quiet-hour copy, opt-in copy, notification preference labels, local display guidance. | Admin/API owns targeting and delivery truth. |
| Version behavior | Recommended update copy, deprecated-mode copy, feature compatibility notes. | App-version policy remains server-owned. |
| Tenant presentation | Tenant labels, safe branding copy, onboarding copy, tenant-specific workflow wording. | Tenant config cannot grant tenant authority. |

Remote config should not control:

- Final authorization decisions.
- Role grants or permission grants.
- Tenant membership or tenant switching authority.
- Billing entitlements, plan authority, prices, invoices, or payment state.
- Canonical data validation rules that require API enforcement.
- Security decisions such as token validity, device trust, suspension, or forced logout.
- Raw secrets, API keys, credentials, private URLs, or provider internals.
- Large content payloads or files that should have a separate content contract.
- Undocumented experimental behavior with no support, audit, rollback, or version story.

## Config Scope And Override Logic

Remote config should resolve predictably.

Recommended scope order:

1. **Platform-safe defaults** - The fallback values that keep the mobile app usable and safe.
2. **Global config** - Platform-wide behavior for all eligible tenants and users.
3. **Plan and entitlement limits** - Commercial ceilings that config cannot exceed.
4. **Tenant-specific config** - Tenant-owned or platform-owned variation inside global and plan limits.
5. **Feature-specific config** - Runtime options for a feature already allowed by feature flags and permissions.
6. **Role/user presentation config** - Safe copy, layout, or workflow variation for a role or user state.
7. **App-version compatibility** - Config values only apply if the mobile build can understand them safely.
8. **Emergency/maintenance overrides** - Temporary operational config for incidents, maintenance, or support recovery.

Override principles:

- Tenant-specific config can override global defaults only inside platform, plan, permission, version, and safety limits.
- A tenant override cannot enable a feature that feature flags, plan limits, app-version policy, or permissions block.
- User-specific presentation config cannot bypass tenant, plan, feature, permission, or version rules.
- Remote config cannot bypass [Mobile Version Control Logic](mobile-version-control-logic.md); it may tune update copy or support text, but it cannot make a blocked or forced-update version safe.
- Emergency and maintenance overrides can narrow behavior or change user-facing messages, but should not silently broaden authority.
- Mobile receives the resolved config and config metadata, not raw unresolved layers.

## How Mobile Receives Config

Mobile should receive remote config through the API, usually as part of boot/context and targeted refresh responses.

The API should give mobile:

- Resolved config values.
- Config version or revision.
- Scope summary that is safe to expose.
- Compatibility state for the current app version.
- Freshness or expiry expectations.
- Fallback behavior for missing values.
- User-facing states or next actions when config cannot be applied.

Mobile should not call admin internals, read server config files, infer config from local settings, or decide tenant-specific overrides locally.

Config should be shaped for mobile use. Mobile should receive what it needs to render behavior, not raw admin configuration records.

## Mobile Cache Rules

Mobile may cache remote config to keep the app stable during startup, poor connectivity, or offline work.

Cache principles:

- Cache only resolved mobile-safe config.
- Store the config version/revision with the cached copy.
- Label stale config where stale behavior could mislead the user.
- Refresh config on boot, resume, login, tenant switch, app-version change, and before sensitive workflows when online.
- Keep safe platform defaults bundled with the app for missing or invalid config.
- Do not cache secrets, credentials, provider internals, or sensitive tenant data in remote config.
- Do not treat cached config as current authority for protected actions.

Cached config is a usability layer. API policy remains final.

## Offline Behavior

When mobile is offline, remote config becomes last-known context, not live authority.

Offline principles:

- Mobile may use cached resolved config for UI copy, safe defaults, local workflow presentation, and offline-eligible behavior.
- Mobile must show offline or freshness state when config age matters.
- Mobile must not use cached config to start newly protected, billing-sensitive, permission-sensitive, or online-only actions.
- Offline queueing should follow the last known sync/offline policy only where the API previously allowed it.
- When connectivity returns, mobile should refresh boot/context and remote config before replaying queued work.
- Replay must recheck current tenant, user, permission, feature flag, remote config, app-version, billing, maintenance, and sync policy.
- If current config now blocks or changes behavior, API returns blocked, conflict, retry-later, failed, transformed, or support-needed outcomes.

Offline mode should feel stable without pretending stale config is current server truth.

## Safe Admin Changes

Admins should be able to change remote config safely and understand the product impact.

Safe-change principles:

- Every config value should have an owner, purpose, scope, default, compatibility expectation, and rollback path.
- Admin UI should explain affected tenants, users, roles, app versions, features, offline behavior, support messages, reports, and billing/plan effects where relevant.
- Sensitive config changes should capture reason, actor, scope, old value, new value, time, and affected area.
- Config changes should be validated before activation.
- Config should support preview or staged rollout where mistakes could affect many users.
- Config changes should be reversible where possible.
- Config that affects mobile behavior should be support-visible.
- Config should avoid unbounded values, unsafe copy, invalid enum-like values, impossible thresholds, and app-version-incompatible shapes.
- Config changes should not silently redefine permissions, billing, tenant membership, or API contracts.

Admins should change remote config in product language, not raw implementation language.

## Missing Or Invalid Config

Mobile must handle missing or invalid config defensively.

Principles:

- Prefer safe app-bundled defaults for non-sensitive presentation behavior.
- Fail closed for security, permission, billing, tenant, app-version, maintenance, sync, and feature-control behavior.
- Treat invalid config as a recoverable API/config error, not as permission to continue blindly.
- Show user-friendly fallback messages when copy/config is missing.
- Avoid crashes caused by missing optional config.
- Do not expose raw validation details, stack traces, secrets, or internal config keys to mobile users.
- Record safe diagnostic context for support: config version, app version, tenant scope, feature state, and missing/invalid category.
- Refresh config when online before retrying a blocked or invalid-config workflow.

Invalid config should never create more access than valid config.

## Relationship To Feature Flags

Feature flags decide whether a feature is available. Remote config decides safe runtime behavior for a feature that is already eligible.

Relationship principles:

- Feature flags gate visibility and access state.
- Remote config tunes behavior, copy, limits, workflow options, offline policy details, and support instructions.
- Remote config cannot enable a feature blocked by feature flags.
- A disabled feature may still use remote config for disabled-state copy or support guidance.
- Rollout should coordinate feature flag state, remote config version, app-version compatibility, support messaging, and reports.

Feature flags answer "can this feature exist here?" Remote config answers "how should the allowed behavior work here?"

## Admin Checklist

Use this checklist before planning a remote config value.

| Question | Required answer |
| --- | --- |
| What behavior changes? | Copy, limit, workflow option, offline behavior, sync behavior, native permission wording, support guidance, notification behavior, version behavior, or tenant presentation is named. |
| Why should it be remote? | Tenant variation, rollout, supportability, frequent change, operational recovery, or app-release avoidance is named. |
| What scope applies? | Global, tenant, plan, feature, role, user, app version, maintenance, emergency, or support scope is explicit. |
| What is the default? | Safe platform default and fallback behavior are defined. |
| What overrides what? | Tenant overrides, plan ceilings, feature flags, permissions, app-version compatibility, and emergency overrides are clear. |
| How does mobile receive it? | API boot/context, targeted refresh, config version, freshness, compatibility, and fallback behavior are named. |
| What can mobile cache? | Resolved mobile-safe config, version, freshness, and safe defaults are explicit. |
| What happens offline? | Last-known config, offline/freshness state, queue eligibility, refresh-before-replay, and blocked/conflict behavior are defined. |
| What if config is missing or invalid? | Safe default, fail-closed behavior, support diagnostics, and retry/refresh path are named. |
| How do admins change it safely? | Owner, validation, preview/staging, reason, audit, support visibility, rollback, and affected scope are defined. |
| What is out of scope? | Database fields, schemas, endpoints, controllers, policies, resources, services, jobs, and code remain deferred until implementation. |

## Risks

| Risk | Remote config response |
| --- | --- |
| Remote config becomes hidden business logic | Keep config to safe runtime variation and document product behavior before implementation. |
| Config bypasses authorization | Permissions and policies remain API/Admin authority. |
| Tenant override bypasses plan limits | Plan and entitlement ceilings win over tenant config. |
| Mobile trusts stale config | Mobile labels freshness, refreshes when online, and rechecks through API before protected actions. |
| Invalid config crashes mobile | Mobile uses safe defaults or fails closed with user-friendly state. |
| Config leaks secrets | Remote config never carries secrets, credentials, tokens, private keys, or provider internals. |
| Config breaks old app versions | Config is versioned, compatible, defaulted, and gated by app-version policy. |
| Admin changes surprise users | Admin impact, support message, rollout, and rollback are documented before activation. |
| Support cannot explain behavior | Config version, scope, feature state, app version, and safe error category are support-visible. |
| Config grows without ownership | Every value needs owner, purpose, scope, default, compatibility, rollback, and retirement expectation. |

## Success Test

Remote configuration logic is successful when Admin/API can safely vary mobile copy, limits, workflow options, offline/sync behavior, native permission wording, support guidance, and tenant presentation; tenant-specific config overrides global defaults only inside plan, permission, feature, version, and safety boundaries; mobile receives resolved config through API, caches it safely, behaves honestly offline, and fails closed or falls back safely when config is missing or invalid.
