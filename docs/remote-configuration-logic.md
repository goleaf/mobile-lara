# Remote Configuration Logic

Updated: 2026-06-25

This document defines the remote configuration logic for Mobile Lara. It explains what behavior should be remotely configurable, how mobile should receive and cache config, what happens offline, how tenant-specific config overrides global defaults, how admins should safely change config, and how mobile should handle missing or invalid config. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, Filament resources, policies, jobs, services, providers, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md), [API-First Principles](api-first-principles.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Core Product Principles](product-principles.md), and [Documentation-First Architecture](documentation-first-architecture.md): remote config is a server-controlled product decision that mobile consumes through API to create stakeholder value without granting authority.

## Remote Configuration Statement

Remote configuration lets Admin/API adjust mobile behavior without publishing a new mobile build.

Remote config should control safe runtime variation: copy, thresholds, limits, workflow options, offline eligibility, sync behavior, native permission wording, maintenance/support messages, and feature behavior that has already been documented.

Remote config must not become hidden business logic, authorization, billing authority, permission authority, tenant authority, or a substitute for versioned API contracts.

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
