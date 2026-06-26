# Testing Strategy Principles

Release And Versioning Principles are defined in `release-versioning-principles.md`:
API versioning, mobile app versioning, admin releases, feature rollout,
rollback, app store release, forced update, documentation update, and Git
change-history decisions must preserve documented authority, compatibility,
rollback, support, audit, and user-visible behavior before release.

Updated: 2026-06-26

This document defines testing strategy principles for future Mobile Lara
implementation work. It explains how future tests should cover API contracts,
admin controls, mobile feature visibility, permissions, feature flags, remote
config, authentication, tenant isolation, offline sync, conflict behavior,
NativePHP fallback behavior, notification flows, billing rules, and app version
rules. It is documentation only and does not create tests, test fixtures,
factories, database fields, migrations, routes, controllers, Livewire
components, Filament resources, NativePHP plugins, policies, jobs, services,
API endpoints, local storage schemas, UI components, CSS, JavaScript, queues,
provider integrations, app-store configuration, or application logic.

Use this document with [Acceptance Principles](acceptance-principles.md),
[Risk Map](risk-map.md), [Documentation-First
Architecture](documentation-first-architecture.md), [API-First
Principles](api-first-principles.md), [Two-System Boundary
Logic](two-system-boundary.md), [Admin/API
Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Role And Permission
Logic](role-permission-logic.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration
Logic](remote-configuration-logic.md), [Authentication
Principles](authentication-principles.md), [Offline-First
Principles](offline-first-principles.md), [Sync Lifecycle
Logic](sync-lifecycle-logic.md), [Conflict Resolution
Logic](conflict-resolution-logic.md), [Native Feature
Strategy](native-feature-strategy.md), [Notifications
Logic](notifications-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), and [Mobile Version Control
Logic](mobile-version-control-logic.md): future tests must prove that the
documented authority boundaries and user-visible states are enforced by the
system that owns them.

## Testing Strategy Statement

Future tests should protect the product contract, not only individual code
paths. Mobile Lara has two systems: Admin/API is the authority for business
rules, and the mobile client is the local executor of those rules. Tests should
therefore prove that trusted decisions come from Admin/API, mobile only presents
or queues allowed behavior, tenant boundaries hold, offline work remains
pending until accepted, and risky controls fail safely.

Testing is part of documentation-first development. Before a feature moves from
documentation into implementation, the feature documentation should name the
future test surfaces that will prove the behavior. A feature is not ready for
implementation planning if nobody can explain how its API contract, admin
control, mobile state, permissions, feature flags, offline behavior, tenant
scope, errors, security, and risks will be verified.

## Scope And Non-Goals

This strategy is not a test suite and does not prescribe concrete test class
names, test files, factories, database schema, endpoint URLs, selectors, or
assertion code. Those belong to future implementation prompts.

This strategy does define:

- What future tests must prove.
- Which system owns the behavior being tested.
- Which failure states future tests should include.
- Which documentation must exist before tests are written.
- Which risks should be protected by regression coverage.

This strategy does not define:

- Database structure.
- Endpoint design.
- Browser automation code.
- Livewire component implementation.
- NativePHP plugin implementation.
- Billing provider integration.
- Push notification provider integration.
- App-store release automation.

## Test Ownership Model

Testing ownership follows the two-system boundary:

| Behavior | Primary test owner | Mobile test responsibility |
| --- | --- | --- |
| API contracts | Admin/API | Consume predictable response states without inventing authority. |
| Admin controls | Admin/API | Reflect control effects after API context changes. |
| Mobile feature visibility | Admin/API for rules, mobile for presentation | Hide, disable, explain, or block features according to resolved context. |
| Permissions | Admin/API | Avoid presenting unavailable actions as usable local authority. |
| Feature flags | Admin/API | Render resolved flag state and refresh stale state safely. |
| Remote config | Admin/API | Cache and apply valid config; fail safely on missing or invalid config. |
| Authentication | Admin/API | Store local session securely and respond to revocation. |
| Tenant isolation | Admin/API | Keep tenant-local cache, drafts, queues, and navigation separated. |
| Offline sync | Admin/API for acceptance, mobile for queue UX | Preserve local intent until API accepts, rejects, or escalates it. |
| Conflict behavior | Admin/API | Preserve user work and show conflict states clearly. |
| Native fallbacks | Mobile client with Admin/API feature rules | Detect unavailable capabilities and show safe fallbacks. |
| Notifications | Admin/API | Present inbox, read state, preferences, and deep links within tenant/permission scope. |
| Billing rules | Admin/API | Explain plan-blocked states without treating local UI as entitlement. |
| App version rules | Admin/API | Show optional update, forced update, maintenance, or outdated states. |

## Test Layer Principles

Future tests should use the smallest layer that proves the behavior without
testing implementation details:

- Contract tests should protect API response meaning, error categories, tenant
  scope, permission scope, feature context, config context, version policy,
  billing state, and sync decisions.
- Feature tests should prove full server-side workflows across authorization,
  validation, policies, events, notifications, reports, audit expectations, and
  API resources.
- Livewire or browser-oriented tests should prove admin and mobile-visible
  behavior when screens depend on reactive state, loading state, offline state,
  disabled state, feature visibility, or user action.
- Architecture tests should enforce boundaries such as no mobile bypass of API
  authority, no raw SQL, no queries in views, no leaked secrets, and no
  business rules hidden in presentation files.
- Unit tests should cover deterministic services, value decisions, policy
  helpers, sync decision rules, conflict classification rules, and config
  resolution rules when those concepts later exist as implementation code.
- Regression tests should be added whenever a bug, support issue, security
  issue, tenant-isolation issue, sync issue, feature-flag mistake, billing
  mistake, or version-policy mistake is fixed.

## Coverage Matrix

| Area | Future tests should prove | Required documentation before tests |
| --- | --- | --- |
| API contracts | Response shape, context meaning, error semantics, idempotency, tenant scope, and mobile-safe failures stay stable. | API purpose, context payload, errors, versioning, tenant boundary, and mobile state mapping. |
| Admin controls | Control changes affect only intended tenants, users, roles, plans, versions, or cohorts. | Admin owner, safe range, impact preview, audit expectation, rollback path, and mobile effect. |
| Mobile feature visibility | Mobile shows only features allowed by permissions, flags, config, plan, version, tenant state, and offline state. | Visibility rules, disabled state, offline state, support path, and stale-cache behavior. |
| Permissions | Unauthorized API, admin, support, and mobile actions fail closed. | Permission owner, platform/tenant level, UI behavior, API behavior, audit, and denial message. |
| Feature flags | Global, tenant, plan, role, user, cohort, device, version, and emergency gates resolve predictably. | Flag priority, disabled mobile state, rollout, rollback, blast radius, and plan relationship. |
| Remote config | Defaults, tenant overrides, invalid config, missing config, and cached config fail safely. | Config purpose, allowed values, defaults, overrides, validation, cache, and fallback. |
| Authentication | Login, refresh, logout, logout-all-devices, tenant selection, expiry, offline access, and revocation are safe. | Session authority, secure storage expectations, tenant selection, expiry state, and revocation state. |
| Tenant isolation | Data, cache, reports, support, notifications, billing, and sync cannot cross tenant boundaries. | Tenant owner, switch behavior, local partitioning, support scope, audit, and suspended states. |
| Offline sync | Drafts, queues, retries, expiry, replay, acknowledgement, and stale state preserve user work without claiming authority. | Offline capability, queue rules, replay gates, conflict rules, status UX, and admin limits. |
| Conflict behavior | Auto-resolution, user choice, admin/support review, audit, and data-loss prevention match the documented rule. | Conflict cause, decision owner, recovery UX, audit meaning, and reporting meaning. |
| Native fallbacks | Missing plugins, denied permissions, unsupported devices, disabled features, and development fallbacks are safe. | Native capability, permission explanation, fallback, feature flag, privacy, and support state. |
| Notifications | Targeting, delivery, inbox state, preferences, read/unread, deep links, offline behavior, and tenant scope are correct. | Notification type, audience, permission boundary, preference rule, deep-link rule, and offline rule. |
| Billing rules | Trial, active, expired, suspended, grace, plan limits, and manual overrides affect access predictably. | Plan state, entitlement owner, feature flag interaction, offline replay rule, admin override, and support view. |
| App version rules | Optional updates, forced updates, maintenance, minimum supported version, store links, and old-client protection are enforced. | Version state, minimum version, affected platform, message, store action, support action, and rollback limit. |

## API Contract Test Principles

Future API contract tests should prove that mobile can depend on API responses
without learning implementation details.

They should cover:

- Bootstrap context for user, tenant, permissions, feature flags, remote config,
  app version policy, subscription state, notification state, support state,
  sync state, and conflict state.
- Stable response meaning for success, validation, authentication,
  authorization, tenant denial, feature-disabled, plan-blocked, version-blocked,
  maintenance, conflict, rate-limit, unavailable, and server-failure states.
- Tenant-scoped response data and the absence of cross-tenant leakage.
- Idempotent or duplicate-safe behavior for replayable mobile actions.
- Additive contract evolution so future fields do not break older mobile
  clients.
- Mobile-safe errors that do not expose stack traces, secrets, raw provider
  payloads, raw config, or unrelated tenant data.

Documentation before tests:

- The API purpose of the feature.
- The context payload mobile needs.
- The trusted server decision mobile is waiting for.
- The mobile state for every relevant error category.
- The backwards-compatible change expectation.

## Admin Control Test Principles

Future admin-control tests should prove that admin decisions are scoped,
audited, previewable where needed, and reflected in mobile behavior only through
Admin/API authority.

They should cover:

- Platform-wide, tenant-scoped, role-scoped, user-scoped, plan-scoped,
  version-scoped, cohort-scoped, device-scoped, and emergency-only controls.
- Dangerous controls requiring confirmation, impact preview, mobile impact
  preview, audit history, and rollback principles.
- Tenant-admin controls staying inside the current tenant.
- Platform-only controls remaining unavailable to tenant admins.
- Mobile bootstrap or context refresh reflecting the changed admin decision.
- Invalid, conflicting, or out-of-range admin settings failing safely.

Documentation before tests:

- Which admin role owns the control.
- Which audience is affected.
- What mobile users see after the change.
- Whether confirmation, audit, preview, support visibility, or rollback applies.
- What invalid configuration means.

## Mobile Feature Visibility Test Principles

Future mobile visibility tests should prove that mobile does not show, enable,
or imply access to features that Admin/API has not allowed.

They should cover:

- Feature shortcut visibility on dashboard and navigation.
- Feature-disabled, permission-denied, plan-blocked, version-blocked,
  maintenance, offline-only, online-only, tenant-suspended, user-suspended, and
  support-routed states.
- Stale cached context refreshing into the correct visible state.
- Disabled native features not requesting OS permissions.
- Mobile presenting local drafts and queued work without claiming server
  acceptance.

Documentation before tests:

- Where the feature appears.
- What allowed, hidden, disabled, read-only, fallback, and blocked states mean.
- Which API context controls visibility.
- Which stale or offline states can appear.

## Permission Test Principles

Future permission tests should prove that permissions are enforced at the API
boundary and only reflected in mobile or admin UI as presentation.

They should cover:

- Platform-level permission decisions.
- Tenant-level permission decisions.
- Admin-user permission decisions.
- Mobile-user permission decisions.
- Support-agent and billing-manager visibility limits.
- Guest, invited, suspended, and revoked-user behavior.
- URL-bypass and direct-API attempts failing closed.
- Feature flags and permissions acting as separate gates.

Documentation before tests:

- Permission owner and level.
- Allowed role, denied role, and suspended state.
- API access behavior.
- Admin and mobile visibility behavior.
- Audit and support expectations for denied or changed access.

## Feature Flag Test Principles

Future feature-flag tests should prove that rollout and access decisions are
predictable, reversible, and safe.

They should cover:

- Global, tenant, plan, role, permission, user, cohort, device, app-version,
  maintenance, and emergency priorities.
- Disabled mobile behavior.
- Safe rollout to narrow audiences.
- Rollback to previous state.
- Plan limits acting as entitlement ceilings.
- Admin impact preview and audit expectation for risky flag changes.
- Offline and stale-cache behavior when a flag changes.

Documentation before tests:

- Flag purpose and non-purpose.
- Priority order.
- Affected audience.
- Mobile disabled state.
- Rollout, rollback, support, audit, and metric expectations.

## Remote Config Test Principles

Future remote-config tests should prove that configurable behavior remains
bounded by safe defaults and does not become hidden application logic.

They should cover:

- Global defaults.
- Tenant-specific overrides.
- Missing config.
- Invalid config.
- Cached config while offline.
- Config refresh after reconnect.
- Safe ranges and unsafe combinations.
- Admin preview and rollback for risky config changes.

Documentation before tests:

- Config purpose.
- Allowed values and safe ranges.
- Default and override behavior.
- Mobile cache behavior.
- Invalid-config fallback.
- Audit and rollback expectations.

## Authentication Test Principles

Future authentication tests should prove that mobile login and session behavior
depend on the API and secure local handling.

They should cover:

- Login through API only.
- Refresh session behavior.
- Logout and logout-all-devices.
- Tenant selection after login.
- Session expiry.
- Offline behavior for already authenticated users.
- Server revocation.
- Suspended users.
- Secure-storage unavailable or reset behavior.
- App lock interaction where sensitive cached data exists.

Documentation before tests:

- Session authority.
- Token handling principles.
- Tenant selection rules.
- Offline authenticated behavior.
- Revocation behavior.
- Secure-storage and app-lock expectations.

## Tenant Isolation Test Principles

Future tenant-isolation tests should prove that tenant boundaries are enforced
server-side and preserved by mobile-local state.

They should cover:

- Cross-tenant API access denial.
- Tenant-scoped admin panels, reports, support cases, notifications, billing,
  audit history, and sync decisions.
- Multi-tenant user tenant selection.
- Tenant switching with separate cache, drafts, queues, and feature state.
- Suspended, archived, billing-blocked, deleted, and restored tenant states.
- Tenant-specific feature flags, remote config, permissions, and plan rules.

Documentation before tests:

- Tenant owner for data and configuration.
- Tenant switch behavior.
- Local cache partitioning.
- Cross-tenant denial behavior.
- Support and audit visibility boundaries.

## Offline Sync Test Principles

Future offline-sync tests should prove that mobile can preserve work locally
without pretending local work is server truth.

They should cover:

- Bootstrap sync.
- Pull changes.
- Push local changes.
- Retry failed changes.
- Manual sync.
- Background sync.
- Queue expiry.
- Acknowledgement.
- Offline banners and pending indicators.
- Rechecking current permission, flag, config, billing, tenant, app-version, and
  maintenance state before replay.
- Admin monitoring of sync health.

Documentation before tests:

- Offline-capable actions.
- Online-only actions.
- Queue and retry rules.
- Pending and accepted states.
- Admin offline limits.
- Support and reporting meaning of sync state.

## Conflict Behavior Test Principles

Future conflict tests should prove that conflict decisions are intentional and
protect users from silent data loss.

They should cover:

- Conflicts caused by stale cache, concurrent edits, changed permissions,
  changed flags, changed config, changed billing state, changed tenant state,
  changed version policy, and duplicate replay.
- Auto-resolution only for documented low-risk cases.
- User choice where meaning or data loss is possible.
- Admin or support review for high-impact, ambiguous, billing-sensitive,
  privacy-sensitive, or policy-sensitive conflicts.
- Mobile conflict messaging.
- Audit history for conflict decisions.
- Data-loss prevention for local drafts and queued work.

Documentation before tests:

- Conflict causes.
- Decision owner.
- Auto-resolution rule.
- User-choice rule.
- Admin/support review rule.
- Audit and reporting expectations.

## Native Feature Fallback Test Principles

Future native fallback tests should prove that NativePHP capabilities are
optional execution tools controlled by product rules.

They should cover:

- Camera, scanner, microphone, location, notifications, files, biometrics,
  secure storage, network status, diagnostics, and browser/development fallback
  states where relevant.
- Feature disabled by admin, plan, permission, tenant state, app version, or
  maintenance state.
- OS permission denied, revoked, unsupported, canceled, unavailable, or failed.
- Disabled features avoiding native permission prompts.
- Native output being validated by API before becoming trusted.
- Offline upload queues for media, voice notes, scans, or location-attached
  actions.
- Diagnostics redaction and user-controlled sharing.

Documentation before tests:

- Native capability dependency.
- Permission explanation.
- Fallback behavior.
- Disabled-feature behavior.
- Privacy and diagnostics boundaries.
- Offline sync interaction.

## Notification Flow Test Principles

Future notification tests should prove that notifications respect tenant,
permission, preference, security, and offline boundaries.

They should cover:

- Admin-created notifications.
- System notifications.
- Security notifications.
- Reminder notifications.
- Push delivery state where provider integrations exist.
- In-app inbox state.
- Read and unread behavior.
- Deep-link routing only to accessible tenant-scoped content.
- Notification preferences.
- Offline notification display and queued read-state updates.
- Suspended users, suspended tenants, billing-blocked tenants, and disabled
  notification features.

Documentation before tests:

- Notification type.
- Audience and tenant scope.
- Permission boundary.
- Preference behavior.
- Deep-link target and fallback.
- Offline behavior.
- Audit and reporting expectations.

## Billing Rule Test Principles

Future billing tests should prove that billing is an Admin/API entitlement
gate and that mobile only presents the resolved access state.

They should cover:

- Trial, active, expired, suspended, billing-blocked, grace, and manually
  overridden states.
- Plan limits.
- Feature flags constrained by plan entitlement.
- Mobile plan-blocked messages.
- Offline drafts or queued actions created before subscription changes.
- Admin manual billing controls.
- Support visibility into billing-blocked access without exposing provider
  secrets or payment details.

Documentation before tests:

- Plan state.
- Entitlement owner.
- Feature flag relationship.
- Mobile blocked state.
- Offline replay behavior.
- Admin override and audit expectations.
- Support visibility boundary.

## App Version Rule Test Principles

Future app-version tests should prove that old, deprecated, outdated, optional
update, forced update, and maintenance states protect users without trapping
them.

They should cover:

- Minimum supported version.
- Recommended version.
- Deprecated but allowed version.
- Optional update message.
- Forced update message and allowed actions.
- Maintenance mode.
- Store links and missing-store-link fallback.
- Platform-specific version behavior.
- Old clients receiving backwards-compatible API responses where allowed.
- Emergency rollback or correction of bad version policy.

Documentation before tests:

- Version state.
- Affected platform, tenant, cohort, or user group.
- Store action.
- User-facing message.
- Support action.
- Logout, legal, or support access during forced-update and maintenance states.
- Rollback limits.

## Regression And Release Principles

Future regression tests should be added when a defect reveals that documented
product logic was not enforced. High-priority regression areas include:

- Tenant boundary failures.
- Permission bypasses.
- Admin misconfiguration.
- Feature flag blast-radius mistakes.
- Billing access disputes.
- App-version lockouts.
- Offline data loss.
- Conflict resolution mistakes.
- Native permission or fallback failures.
- Notification deep-link leakage.
- Support access overreach.
- Privacy exposure.

Future release verification should connect tests to the release risk:

- API changes should run affected API contract tests.
- Admin-control changes should run affected admin, audit, permission, and
  mobile-context tests.
- Mobile UI changes should run affected Livewire, browser, visibility, offline,
  native fallback, and app-version tests.
- Sync changes should run offline, replay, conflict, idempotency, tenant, and
  billing replay tests.
- Security-sensitive changes should run authentication, authorization, tenant,
  privacy, support, and audit tests.

## Documentation Requirements Before Writing Tests

Before future tests are implemented, the feature documentation should answer:

- What behavior is being proven?
- Which system owns the decision?
- Which roles, tenants, plans, versions, flags, permissions, and config states
  affect it?
- What is the happy path?
- What are the denied, disabled, offline, stale, conflict, expired,
  maintenance, forced-update, and unsupported states?
- What data must remain tenant-scoped?
- What data must never be cached, logged, exported, or exposed to support?
- Which API response makes the mobile behavior trusted?
- Which admin control can change the behavior?
- Which support and audit views explain the behavior?
- Which risk-map entries does the future test protect?

If these answers are missing, the next step is documentation, not test code.

## Future Test Acceptance Checklist

When implementation prompts later ask for test code, each feature should be
accepted only when the relevant future tests prove:

- API contracts are predictable and mobile-safe.
- Admin controls are scoped, audited, and reflected through API context.
- Mobile visibility matches permissions, flags, config, plan, tenant, version,
  and offline state.
- Permissions fail closed at the API boundary.
- Feature flags resolve with documented priority and rollback behavior.
- Remote config has defaults, overrides, validation, cache behavior, and
  fallback.
- Authentication handles login, refresh, logout, expiry, revocation, tenant
  selection, and offline access safely.
- Tenant isolation protects data, cache, queues, support, reports,
  notifications, billing, and audit history.
- Offline sync preserves user work while waiting for API acceptance.
- Conflict behavior prevents silent data loss.
- Native feature fallbacks are safe when plugins, permissions, or devices are
  unavailable.
- Notifications respect tenant, permission, preference, deep-link, offline, and
  read/unread rules.
- Billing rules control access without exposing provider internals.
- App version rules protect users on old or unsupported clients.

Testing strategy is successful when future code can be changed with confidence
because every critical product decision has an explicit test surface and every
test surface traces back to documented authority, risk, and user-visible
behavior.
