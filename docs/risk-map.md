# Risk Map

Testing Strategy Principles are defined in `testing-strategy-principles.md`:
future tests for API contracts, admin controls, mobile feature visibility,
permissions, feature flags, remote config, authentication, tenant isolation,
offline sync, conflict behavior, native feature fallbacks, notification flows,
billing rules, and app version rules must map to documented authority, risk,
and user-visible behavior before implementation.

Release And Versioning Principles are defined in `release-versioning-principles.md`:
API versioning, mobile app versioning, admin releases, feature rollout,
rollback, app store release, forced update, documentation update, and Git
change-history decisions must preserve documented authority, compatibility,
rollback, support, audit, and user-visible behavior before release.

Documentation Audit is defined in `documentation-audit.md`:
project documentation for two-system architecture, Admin/API authority, mobile
client execution, API-first communication, feature flags, remote config,
tenancy, permissions, offline sync, NativePHP features, notifications, billing,
support, reports, security, risks, and release principles must use consistent
authority language and resolve contradictions before implementation.

Feature Dependency Map is defined in `feature-dependency-map.md`:
major features must document dependencies on authentication, tenant context,
permissions, feature flags, remote config, API availability, offline cache,
NativePHP permissions, subscription plan, and admin settings before
implementation planning or release decisions.

Updated: 2026-06-26

This document defines the risk map for the Mobile Lara SaaS mobile/admin
system. It covers API dependency, offline sync, tenant isolation, mobile secure
storage, NativePHP plugin availability, app store releases, forced updates,
feature flag mistakes, billing restrictions, admin misconfiguration, support
access, privacy, and data conflicts. It is documentation only and does not
define database structure, database fields, migrations, routes, controllers,
Livewire components, Filament resources, NativePHP plugins, policies, jobs,
services, tests, local storage schemas, API endpoints, UI components, CSS,
JavaScript, queues, provider integrations, app-store configuration, or
application logic.

Use this document with [Acceptance Principles](acceptance-principles.md),
[Testing Strategy Principles](testing-strategy-principles.md),
[Release And Versioning Principles](release-versioning-principles.md),
[Feature Dependency Map](feature-dependency-map.md),
[Documentation-First Architecture](documentation-first-architecture.md),
[Product Principles](product-principles.md), [Two-System Boundary
Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Admin Safety
Principles](admin-safety-principles.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration
Logic](remote-configuration-logic.md), [Mobile Version Control
Logic](mobile-version-control-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), [Role And Permission
Logic](role-permission-logic.md), [Data Privacy
Principles](data-privacy-principles.md), [Audit Logic](audit-logic.md),
[Offline-First Principles](offline-first-principles.md), [Sync Lifecycle
Logic](sync-lifecycle-logic.md), [Conflict Resolution
Logic](conflict-resolution-logic.md), [Mobile App Shell
Logic](mobile-app-shell-logic.md), [Native Feature
Strategy](native-feature-strategy.md), [NativePHP Local
Storage](nativephp-local-storage.md), [NativePHP Runbook](nativephp-run.md),
[Support System Logic](support-system-logic.md), [Reports
Logic](reporting-logic.md), and [AI Feature Logic](ai-feature-logic.md): risks
must be documented before implementation so controls, fallback behavior, admin
impact, mobile impact, privacy boundaries, support visibility, audit meaning,
release/versioning impact, dependency failure states, and rollback options are
clear before code exists.

## Risk Map Statement

Mobile Lara is valuable because the Admin/API system controls mobile behavior
centrally while the NativePHP mobile client keeps users productive at the edge.
That same model creates predictable risks: mobile depends on the API, offline
work can drift from server truth, tenants must stay isolated, secure local data
must be protected, native plugins can be unavailable, app-store releases can
lag behind policy, and admin controls can affect many users quickly.

The risk strategy is not to remove every risk. The strategy is to make each
risk visible, bounded, documented, monitored, reversible where possible, and
owned by the correct system.

Product rule: a feature is not ready for implementation planning until its
risks, prevention principles, documentation requirements, support meaning, and
rollback path are written down.

## Risk Ownership Model

Risk ownership follows the two-system boundary:

| Risk area | Primary owner | Mobile responsibility |
| --- | --- | --- |
| API dependency | Admin/API | Show clear loading, offline, retry, stale, blocked, and support states. |
| Offline sync | Admin/API for acceptance, mobile for queue and state display | Preserve local work, label pending state, and replay only through API rules. |
| Tenant isolation | Admin/API | Separate tenant cache, tenant context, drafts, queues, and screen state. |
| Secure storage | Mobile client with Admin/API security policy | Store secrets only in approved secure storage and explain fallback limits. |
| Native plugin availability | Mobile client with Admin/API feature rules | Detect unavailable capability, avoid prompts for disabled features, and show fallback. |
| App store releases | Product/release admins | Report app version, show update states, and avoid using unsupported behavior. |
| Forced updates | Admin/API | Block or guide mobile navigation through API-provided update state. |
| Feature flags | Admin/API | Render resolved feature state and do not invent local enablement. |
| Billing restrictions | Admin/API | Present plan-blocked state without treating local UI as entitlement. |
| Admin misconfiguration | Admin/API | Show safe mobile impact and recover from invalid or changed config. |
| Support access | Admin/API | Share only user-approved, redacted, tenant-scoped diagnostics. |
| Privacy | Admin/API with mobile safeguards | Minimize, protect, label, and delete local data according to policy. |
| Data conflicts | Admin/API | Preserve user work and explain conflict state without deciding authority locally. |

## Summary Risk Matrix

| Risk | Primary failure mode | Prevention principle | Documentation requirement |
| --- | --- | --- | --- |
| API dependency | Mobile cannot reach or trust required API context. | API responses must be predictable, retryable where safe, and mapped to mobile states. | Document required API context, fallback state, retry policy, and support path. |
| Offline sync | Local intent is mistaken for accepted server truth. | Offline work remains pending until API acceptance. | Document cache, queue, replay, conflict, expiry, and stale-state behavior. |
| Tenant isolation | Data, cache, reports, support, or permissions cross tenants. | Tenant scope is resolved server-side and preserved locally by context. | Document tenant owner, cache partitioning, switch behavior, and audit boundaries. |
| Mobile secure storage | Tokens or sensitive data are stored in unsafe local storage. | Secrets use secure storage; ordinary cache is never a token store. | Document what is secret, where it may live, fallback limits, and wipe behavior. |
| NativePHP plugin availability | A plugin, device capability, permission, or platform behavior is unavailable. | Native features are optional capabilities behind product boundaries and fallbacks. | Document capability dependency, fallback, denied state, unsupported state, and feature flag. |
| App store releases | Store review or user adoption lags behind API policy. | Release policy must assume old clients remain in the field. | Document supported versions, rollout calendar, store links, user copy, and rollback limits. |
| Forced updates | Users are blocked incorrectly or broken clients remain active. | Forced updates require impact preview, support path, and safe minimum-version policy. | Document trigger, audience, recovery actions, support message, and emergency rollback. |
| Feature flag mistakes | Wrong users or tenants gain or lose a feature. | Feature flags resolve from documented priority with preview, audit, and rollback. | Document priority, plan limits, disabled mobile state, blast radius, and rollback. |
| Billing restrictions | Plan state blocks legitimate work or allows unpaid access. | Billing is an Admin/API entitlement gate, separate from local UI state. | Document plan gate, grace state, offline behavior, admin override, and user message. |
| Admin misconfiguration | Config creates unsafe, confusing, or tenant-wide disruption. | Dangerous controls require validation, preview, confirmation, audit, and rollback. | Document safe ranges, defaults, invalid-config fallback, impact preview, and audit. |
| Support access | Support sees too much or cannot help safely. | Support access is least-privilege, tenant-scoped, redacted, and audited. | Document support scope, diagnostic content, redaction, consent, and audit visibility. |
| Privacy | Sensitive data is collected, cached, shared, exported, or retained incorrectly. | Privacy-by-default and data minimization apply to server and mobile. | Document data categories, retention, deletion, export, diagnostics, and never-collect rules. |
| Data conflicts | Server truth and local edits diverge. | Conflicts preserve work and defer authority to API/Admin decisions. | Document detection, auto-resolution, user choice, admin review, audit, and data-loss prevention. |

## API Dependency Risk

### Failure Mode

The mobile client depends on the API for tenant context, permissions, feature
flags, remote config, app-version policy, subscription state, notification
state, support state, sync policy, and server acceptance. If the API is slow,
unavailable, inconsistent, stale, or returns ambiguous errors, mobile users can
lose trust or act on incomplete information.

### Prevention Principles

- Mobile communicates only with the API for trusted business decisions.
- API responses should use predictable shapes and stable meaning.
- Mobile should distinguish loading, offline, stale, retry-later, blocked,
  maintenance, force-update, permission-denied, subscription-blocked, and
  conflict states.
- API errors should be safe for mobile display and should not expose stack
  traces, provider internals, secrets, raw configuration, or tenant data.
- Critical mobile features should have clear bootstrap dependencies and safe
  unavailable states.
- Repeated API failures should give users a calm recovery path and support
  context.
- API contracts should avoid hidden behavior that mobile might accidentally
  depend on.

### Documentation Requirements

Document:

- Which API context is required before the feature can render or act.
- Which API response makes mobile behavior trusted.
- What mobile shows when the API is offline, slow, invalid, stale, or
  unavailable.
- Which actions can be retried safely and which must not be repeated.
- Which errors are user-facing, support-facing, admin-facing, or hidden.
- Which support diagnostics can explain API dependency failure without leaking
  sensitive data.

## Offline Sync Risk

### Failure Mode

Users may create drafts, edits, scans, media, notes, messages, check-ins, or
support requests while offline. If the app treats those local actions as final,
or replays them after permissions, plan, feature, tenant, or app-version state
has changed, server truth can be corrupted or users can lose work.

### Prevention Principles

- Offline actions are intents until API acceptance.
- Mobile should label local draft, queued, pending, syncing, accepted, rejected,
  failed, stale, and conflict states clearly.
- Replay must recheck current tenant, user, role, permission, plan, feature
  flag, app-version, maintenance, and sync policy.
- Queued actions should be idempotent in principle and safe against duplicate
  replay.
- Offline limits should be admin-controlled where risk is meaningful.
- Local work should be preserved until accepted, rejected with explanation, or
  intentionally discarded by the user.
- Sync health should be visible to admins and support without exposing private
  local content.

### Documentation Requirements

Document:

- What works offline and what must wait for the API.
- What can be cached and what must never be cached.
- How long queued work can remain pending.
- What happens when permissions, plan, feature flags, tenant state, app version,
  or remote config change before replay.
- How conflict detection, retry, expiry, support escalation, and user recovery
  work.
- What sync health means in admin reports and support views.

## Tenant Isolation Risk

### Failure Mode

Tenant data, cache, drafts, sync queues, notifications, support cases, reports,
feature flags, permissions, billing state, or audit history may cross tenant
boundaries, especially when users belong to multiple tenants or switch tenants
while offline.

### Prevention Principles

- Tenant authority belongs to Admin/API.
- Every trusted read, write, report, support view, audit event, notification,
  billing decision, and sync acceptance must be tenant-scoped.
- Mobile should remember tenant context only after API confirmation.
- Local cache, drafts, queues, media, diagnostics, and sync state should be
  logically separated by tenant.
- Tenant switching should not replay old-tenant work into the new tenant.
- Suspended, archived, billing-blocked, deleted, or restored tenant states
  should fail closed until API confirms allowed behavior.

### Documentation Requirements

Document:

- Which tenant owns each feature's data, cache, queue, reports, support context,
  notifications, billing state, and audit history.
- How multi-tenant users select, remember, and switch tenants.
- How tenant-specific feature flags, permissions, remote config, and plan state
  change mobile behavior.
- What happens to unsynced work when tenant context changes.
- What support and platform admins may see across tenant boundaries.

## Mobile Secure Storage Risk

### Failure Mode

Access tokens, refresh tokens, app-lock material, private identifiers, secure
session state, or sensitive cached metadata may be stored in ordinary local
storage, exposed through diagnostics, copied across tenants, or left behind
after logout, revocation, tenant removal, or device loss.

### Prevention Principles

- Secure tokens belong in NativePHP secure storage or an approved secure
  platform store, not ordinary SQLite, browser storage, logs, screenshots, or
  diagnostics.
- Local cache is convenience data, not a secret store.
- Secure storage failure should create an explicit degraded or blocked state.
- Logout, logout-all-devices, server revocation, tenant removal, app lock, and
  remote wipe principles should define local cleanup.
- Diagnostics should never expose tokens, secrets, private keys, biometric
  material, raw secure-storage values, or full authentication payloads.
- Development fallback behavior should be documented separately from production
  security expectations.

### Documentation Requirements

Document:

- Which values are secrets and where they may be stored.
- Which values may be cached locally and for how long.
- How secure storage unavailable, denied, reset, corrupted, or unsupported
  states appear to users.
- How logout, revocation, tenant switch, app lock, and device compromise affect
  local data.
- What diagnostics and support exports must redact.

## NativePHP Plugin Availability Risk

### Failure Mode

NativePHP plugins or device capabilities such as camera, scanner, microphone,
location, biometrics, secure storage, files, push notifications, browser,
sharing, or network status may be unavailable, unsupported, denied by the OS,
disabled by admin, blocked by plan, incompatible with a platform version, or
different between development and production builds.

### Prevention Principles

- Native capabilities are optional execution tools, not product authority.
- Screens should depend on documented product capabilities, not raw plugin
  assumptions.
- Every native feature should have browser/development fallback principles.
- Disabled or unavailable features should not request native permissions.
- Permission education should happen before OS prompts.
- Native failures should use stable user states such as unavailable, unsupported,
  permission denied, canceled, failed, blocked by admin, blocked by plan,
  update required, or support needed.
- Native outputs should be treated as untrusted until validated and accepted by
  the API.

### Documentation Requirements

Document:

- Which native capability a feature needs and why.
- Which platforms, app versions, device states, and permissions are required.
- What fallback exists in browser, simulator, emulator, development, and
  production.
- What users see when the plugin is missing, denied, unsupported, canceled, or
  failed.
- Which feature flags, plan gates, permissions, and remote config control the
  capability.
- How native output interacts with offline sync, privacy, support, and audit.

## App Store Release Risk

### Failure Mode

App store review, staged rollout, user update delay, platform-specific store
availability, rejected builds, release bugs, or old installed versions can make
mobile behavior lag behind Admin/API policy.

### Prevention Principles

- Admin/API policy should assume old versions remain active until explicitly
  blocked.
- New API behavior should be additive and safe for older clients where possible.
- Minimum supported versions, optional update messaging, forced update policy,
  store links, and maintenance behavior should be admin-controlled.
- Release notes should explain product impact, not only technical change.
- Store links, update copy, and support routes should be controlled remotely.
- Rollout should consider platform differences, store review timing, tenant
  cohorts, support readiness, and rollback limits.

### Documentation Requirements

Document:

- Supported, recommended, deprecated, blocked, and maintenance version states.
- Which feature requires which minimum app version.
- What happens when a store link is missing, invalid, or platform-specific.
- Which tenants, cohorts, platforms, or versions are affected by a release.
- How support should help users who cannot update.
- What rollback is possible after store release and what is not.

## Forced Update Risk

### Failure Mode

A forced update can block productive users, trap users without a valid store
path, disrupt support, hide critical legal/support access, or fail to block old
clients that are unsafe.

### Prevention Principles

- Forced updates should be used for unsafe, incompatible, unsupported, or
  policy-critical app versions.
- Admins should preview affected tenants, users, platforms, versions, features,
  and support load before saving.
- Force-update screens should show API-provided copy, current version, required
  version, store action, support action, logout path, and retry or refresh
  behavior where appropriate.
- Critical support, legal, or logout paths should remain available when safe.
- Forced update policy should be auditable and rollback-aware.
- Mobile should not bypass a valid forced update state with stale cache.

### Documentation Requirements

Document:

- Why the forced update is required.
- Which versions, platforms, tenants, cohorts, or users are affected.
- What mobile screen and recovery actions appear.
- What support users should say and see.
- What audit event explains the decision.
- What rollback or emergency correction exists.

## Feature Flag Mistake Risk

### Failure Mode

Feature flags may enable a risky feature for the wrong tenant, disable a
business-critical workflow, expose unfinished behavior, violate plan limits,
hide data users need, or create inconsistent mobile states across cache,
bootstrap, and API responses.

### Prevention Principles

- Important mobile features should be feature-controlled.
- Flag priority should be documented across global, tenant, plan, role,
  permission, user, cohort, device, app-version, maintenance, and emergency
  gates.
- Admin changes should show impact before saving.
- Dangerous flag changes should require confirmation and audit.
- Disabled mobile states should be explicit: hidden, unavailable, read-only,
  fallback, contact admin, contact support, update required, or maintenance.
- Mobile should refresh resolved feature state through the API and avoid local
  overrides.
- Rollout should begin with narrow cohorts and clear rollback.

### Documentation Requirements

Document:

- What the flag controls and what it does not control.
- Which rule wins when multiple gates conflict.
- Which tenants, users, roles, plans, devices, versions, and cohorts are
  affected.
- What mobile shows when the feature is disabled.
- What metrics, support signals, audit events, and rollback steps apply.

## Billing Restriction Risk

### Failure Mode

Billing restrictions may incorrectly block active tenants, allow unpaid access,
hide plan-limited features without explanation, accept offline work after
subscription expiry, or create support disputes when plan, feature, and tenant
state are unclear.

### Prevention Principles

- Billing entitlement belongs to Admin/API.
- Plan limits should act as ceilings for feature flags, not as mobile-only UI
  decisions.
- Trial, active, expired, suspended, billing-blocked, grace, and manual override
  states should be user-safe and support-visible.
- Offline replay should recheck current billing and plan state before API
  acceptance.
- Mobile should explain unavailable features without exposing billing-provider
  internals.
- Admin manual controls should be audited and tenant-scoped.

### Documentation Requirements

Document:

- Which plan or subscription state controls the feature.
- Which mobile message appears when a feature is plan-blocked.
- How offline drafts, queued actions, and pending uploads behave after expiry or
  suspension.
- Which admins can override billing restrictions and what audit is required.
- What support can see and say about plan-limited access.

## Admin Misconfiguration Risk

### Failure Mode

An admin may set invalid remote config, unsafe feature combinations, excessive
offline limits, conflicting app-version rules, destructive maintenance states,
overbroad support access, or tenant settings that create broken mobile
experiences.

### Prevention Principles

- Admin controls should be validated before saving.
- Dangerous actions should require confirmation, impact preview, mobile impact
  preview, audit, and rollback thinking.
- Config should have safe defaults, safe ranges, invalid-config fallback, and
  scoped rollout.
- Tenant-specific changes should stay isolated to that tenant.
- Admin UI should explain mobile impact in product terms.
- Support and audit history should show who changed what, where it applied, and
  how to recover.

### Documentation Requirements

Document:

- Safe defaults, allowed values, invalid states, and fallback behavior.
- Which controls are dangerous, destructive, privacy-sensitive,
  billing-sensitive, tenant-sensitive, or support-sensitive.
- Which admin role can change each setting.
- What mobile users see after the change.
- What rollback, restore, or emergency disable principle applies.

## Support Access Risk

### Failure Mode

Support agents may see more data than needed, lack enough context to help, view
cross-tenant data, access private diagnostics, act without audit, or rely on
mobile screenshots/logs that expose sensitive content.

### Prevention Principles

- Support access should be least-privilege and tenant-scoped.
- Support should see safe context: tenant, user, app version, device class,
  feature state, sync state, error category, and redacted diagnostics.
- Support should not see secrets, tokens, full private content, unrelated tenant
  data, raw secure storage, hidden moderation data, or broad exports without
  explicit authority.
- Support actions should be audited.
- User-controlled diagnostics sharing should be documented where diagnostics
  include device or app context.
- Support should have escalation paths for privacy, billing, conflict, and
  security issues.

### Documentation Requirements

Document:

- What support can and cannot access.
- Which support views are tenant-scoped, role-scoped, case-scoped, or
  time-limited.
- What diagnostics are collected, redacted, retained, exported, and deleted.
- Which support actions require user consent, admin approval, audit, or
  escalation.
- What mobile users see when support context is shared.

## Privacy Risk

### Failure Mode

The system may collect too much data, cache sensitive data unnecessarily, expose
diagnostics, retain data too long, leak data through support, cross tenant
boundaries, export restricted data, or send private content to optional future
AI/provider workflows without proper policy.

### Prevention Principles

- Privacy-by-default applies to Admin/API and mobile.
- Collect the minimum data needed for the documented purpose.
- Keep tenant data isolated and permission-scoped.
- Never cache secrets, payment secrets, private keys, raw secure-storage values,
  biometrics, hidden moderation data, or unrelated tenant data.
- Diagnostics should be redacted and user-controlled where possible.
- Data export, deletion, retention, support visibility, audit visibility, and
  optional AI use should be documented before implementation.
- Privacy-sensitive admin actions should require impact preview and audit.

### Documentation Requirements

Document:

- Data categories collected, cached, generated, synced, exported, deleted, and
  retained.
- Purpose and lawful/product reason for each sensitive data category.
- Never-collect and never-cache rules.
- Support visibility and redaction policy.
- Mobile diagnostics privacy limits.
- Optional AI/provider privacy and human-review limits.

## Data Conflict Risk

### Failure Mode

Data conflicts happen when local mobile intent, cached data, and current server
truth no longer align. Conflicts may duplicate work, overwrite newer data,
ignore admin changes, apply stale permissions, corrupt status transitions, or
hide data loss.

### Prevention Principles

- Conflict decisions belong to Admin/API.
- Mobile should preserve user work and explain conflict state.
- Some conflicts can be auto-resolved only when the rule is documented and
  low-risk.
- User choice should be required when meaning or data loss is possible.
- Admin/support review should handle high-impact, ambiguous, billing,
  privacy-sensitive, or policy-sensitive conflicts.
- Conflict decisions should be audited when they affect records, reports,
  support, billing, permissions, or user-visible outcomes.
- Conflict UX should never silently discard local work.

### Documentation Requirements

Document:

- Why conflicts can happen for the feature.
- Which conflicts can be auto-resolved.
- Which conflicts need user choice.
- Which conflicts need admin or support review.
- How mobile shows conflict state and preserves local work.
- How conflict decisions are audited and reported.
- How users avoid data loss.

## Cross-Risk Controls

The following controls reduce multiple risks at once:

- Documentation-first acceptance before implementation.
- Admin/API authority for tenant, permission, billing, feature, config, version,
  sync, conflict, support, report, and audit decisions.
- Mobile UI that distinguishes local state from API-confirmed truth.
- Predictable API responses and user-safe error categories.
- Feature flags with impact preview, audit, staged rollout, and rollback.
- Remote config with safe defaults, validation, invalid-config fallback, and
  scoped overrides.
- Secure storage for tokens and local app-lock material.
- Tenant-separated cache, drafts, queues, diagnostics, and sync state.
- Privacy-by-default and least privilege.
- Support views that are redacted, scoped, and audited.
- App-version policy that protects old clients without trapping users.

## Risk Documentation Checklist

Before implementation, every feature should answer:

- Which risks from this map apply?
- Who owns prevention: platform owner, super admin, tenant admin, support,
  billing, Admin/API, or mobile client?
- Which admin controls can increase or reduce the risk?
- Which API response or policy makes the mobile behavior trusted?
- What happens offline, stale, denied, disabled, blocked, expired, conflicted,
  unsupported, or unavailable?
- Which data can be cached, which data is secret, and which data must never be
  cached?
- Which support view can diagnose the issue safely?
- Which audit event helps explain what happened?
- Which rollback, disable, restore, or support escalation path exists?
- Which canonical Markdown documents record the answer?

If these answers are missing, the correct next step is risk documentation, not
application code.
