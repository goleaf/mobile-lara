# Feature Flag Logic

Updated: 2026-06-25

This document defines the feature flag logic for Mobile Lara. It explains why important mobile features should be controlled by feature flags, how global, tenant, and user-level decisions should be prioritized, how disabled features should appear on mobile, how admins should understand impact, how flags support safe rollout, and how flags support plan limits. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, Filament resources, policies, jobs, services, providers, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Admin Control Center Logic](admin-control-center-logic.md), [API-First Principles](api-first-principles.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Core Product Principles](product-principles.md), [SaaS Value Map](saas-value-map.md), [Remote Configuration Logic](remote-configuration-logic.md), and [Mobile Version Control Logic](mobile-version-control-logic.md): feature flags are admin-controlled product decisions that reach mobile through API outcomes, remote config tunes safe runtime behavior for enabled features, and mobile version control decides whether the build can safely use those outcomes.

## Feature Flag Statement

Every important mobile feature should be controlled by feature flags because mobile clients can be stale, offline, version-fragmented, tenant-specific, plan-limited, or in phased rollout.

A feature flag is not just a boolean. It is a product-control decision that answers:

- Who can see the feature.
- Who can use the feature.
- Which tenant, plan, app version, user, device, or cohort is eligible.
- Whether the feature is visible, hidden, disabled, blocked, beta, deprecated, or emergency-disabled.
- Whether offline behavior is read-only, draft-only, queueable, or online-only.
- What support, reporting, audit, billing, and rollback context exists.

Feature flags make the mobile app governable without turning the mobile client into the source of authority.

Remote config complements feature flags. A flag decides whether a feature is
available and in what mobile-safe state; remote config decides safe values such
as copy, limits, thresholds, workflow options, offline rules, support prompts,
notification presentation, version messaging, and tenant presentation.

Mobile version control complements feature flags. A flag can require a minimum
app version or block stale clients, but [Mobile Version Control Logic](mobile-version-control-logic.md)
decides optional update, forced update, maintenance, blocked, deprecated, store
link, and outdated-client behavior.

## Why Important Mobile Features Need Flags

Important mobile features need feature flags when they affect product access, tenant trust, billing, permissions, offline behavior, NativePHP capabilities, sync, support, reporting, or operational risk.

Reasons:

- **Remote control** - Admin/API can enable, disable, or limit features without publishing a mobile build.
- **Tenant variation** - Tenants can receive different modules, plan entitlements, support tiers, or rollout timing without app forks.
- **Safe rollout** - New features can start internal, move to pilot tenants, expand to cohorts, and roll back quickly.
- **Version safety** - Features can be limited to app versions that support the required API contract or NativePHP capability.
- **Plan enforcement** - Flags can expose or block capabilities based on plan and entitlement outcomes while billing authority remains server-side.
- **Support clarity** - Support can explain whether a feature is unavailable because of rollout, plan, tenant setting, role, version, maintenance, or emergency disablement.
- **Offline safety** - Flags can decide whether a feature is online-only, read-only offline, draft-only offline, or queueable offline.
- **Operational recovery** - Emergency disablement can stop unsafe mobile behavior while preserving clear user feedback.

Product rule: a mobile feature that matters to business behavior must have a feature-flag story before implementation.

## Flag Scope

Feature flags should support multiple scopes without making mobile responsible for resolving authority.

| Scope | Meaning | Control principle |
| --- | --- | --- |
| Global | Platform-wide default or safety decision. | Defines the ceiling for all tenants and users. |
| Tenant | Tenant-specific availability, rollout, or disablement. | Can narrow or enable within global and plan limits. |
| Plan | Commercial entitlement or quota relationship. | Defines what the tenant may receive commercially. |
| Role/permission | Capability requirement for a user role or action. | Determines who may use the enabled feature. |
| User | Targeted beta, support recovery, preview, or explicit user disablement. | Can refine access inside global, tenant, plan, and permission boundaries. |
| App version | Compatibility with API contracts and NativePHP capabilities. | Prevents stale clients from seeing unsupported behavior. |
| Device/cohort | Device, platform, tester group, percentage, or phased rollout. | Allows controlled expansion and rollback. |
| Maintenance/emergency | Temporary platform, tenant, feature, or API block. | Fails closed when safety or operations require it. |

Mobile receives the resolved outcome through API. It should not resolve these layers locally from raw flag data.

## Priority And Resolution Logic

Feature flag priority should be predictable and conservative.

Recommended decision order:

1. **Safety and authority gates first** - If the tenant is disabled, user is suspended, app version is blocked, device is blocked, maintenance blocks the feature, or a platform emergency kill switch is active, the feature is blocked.
2. **Plan and entitlement ceiling** - If the tenant plan or quota does not allow the feature, the feature cannot be enabled by tenant or user-level flags.
3. **Global default** - The platform default defines whether the feature is generally unavailable, internal-only, beta, available, deprecated, or emergency-disabled.
4. **Tenant decision** - Tenant settings can enable, disable, pilot, or limit the feature only within the global and plan ceiling.
5. **Role and permission decision** - The user's role and permissions decide whether the enabled feature can be used.
6. **User decision** - User-level overrides can grant preview, remove a user from rollout, or disable a user-specific experience only inside the global, tenant, plan, version, and permission boundaries.
7. **Device, app-version, and cohort decision** - The feature is allowed only if the current mobile build, platform, device, and rollout cohort are compatible.
8. **Offline decision** - If the feature is allowed, the API still decides whether offline behavior is read-only, draft-only, queueable, or online-only.

Conflict rule:

- Higher-safety blocks win over lower-scope enables.
- Plan limits win over tenant and user enables.
- Tenant disablement wins over user enablement.
- Role/permission denial wins over feature visibility.
- App-version incompatibility wins over feature availability.
- Emergency disablement wins over normal rollout.
- User-level enablement is for targeted access, not a way to bypass tenant, plan, permission, version, or security boundaries.

This keeps feature flags useful without making them a hidden authorization system.

## Mobile Feature States

The API should resolve flags into mobile-safe states.

| Mobile state | Meaning | Mobile behavior |
| --- | --- | --- |
| Hidden | The user should not see the feature because it is irrelevant or unavailable without useful explanation. | Remove from navigation and action surfaces. |
| Visible | The feature is available for the current user, tenant, plan, version, and context. | Show normal entry points and actions. |
| Disabled | The feature exists but is turned off by admin/tenant policy. | Show a disabled state only where explanation helps. |
| Blocked | The feature cannot be used because of permission, plan, version, tenant, device, maintenance, or safety policy. | Show a clear reason and next action. |
| Beta | The feature is available as a controlled rollout. | Show normal use with beta/support expectations where useful. |
| Deprecated | The feature or current app version still works but should be replaced or updated. | Show warning or reduced access according to API policy. |
| Update required | The app version cannot safely use the feature. | Show update prompt or block feature entry. |
| Offline limited | The feature is allowed online but has restricted offline behavior. | Show read-only, draft-only, queueable, or online-only state. |
| Emergency disabled | The feature has been stopped for safety or incident response. | Fail closed with support-safe messaging. |

Mobile should not expose raw flag names, internal rollout rules, billing provider details, or tenant-private admin reasoning. It should translate the resolved state into clear next action.

## Disabled Feature Behavior On Mobile

Disabled features should appear based on user need and product clarity.

Principles:

- Hide features that the user has no reason to know exist.
- Disable and explain features that the user expected to use or previously used.
- Block with a clear next action when the issue is version, plan, permission, maintenance, tenant status, or support-required recovery.
- Avoid showing billing internals to mobile users; use product outcomes such as contact admin, contact support, update required, or unavailable.
- Keep cached feature state honest. If the app is offline, label state as last known and avoid protected actions until API refresh.
- If a feature becomes disabled while work is queued offline, replay must recheck the current flag and return synced, blocked, conflict, failed, or retry-later state.

Disabled state is not authorization. It is mobile feedback after API authority has resolved the feature outcome.

## Admin Impact Model

Admins should understand the impact before enabling or disabling a feature.

Admin impact should explain:

- Which tenants, users, roles, plans, app versions, devices, and cohorts are affected.
- Whether the change makes the feature visible, hidden, disabled, blocked, beta, deprecated, update-required, offline-limited, or emergency-disabled.
- Whether the change affects mobile navigation, actions, offline queueing, sync replay, notifications, support, reports, billing, or audit.
- Whether the feature requires a minimum app version, NativePHP capability, permission purpose text, API contract, or remote config version.
- Whether queued offline work will be accepted, blocked, delayed, or conflicted after the change.
- Which support message and rollback path applies.
- Whether the change is reversible and whether a reason should be recorded.

Admins should not need to infer feature impact from raw flags. The control center should describe consequences in product language.

## Safe Rollout Logic

Feature flags should make rollout gradual, observable, and reversible.

Recommended rollout path:

1. **Documented proposal** - Product value, control owner, API purpose, mobile UX, offline behavior, support, audit, billing, and risk are written first.
2. **Internal-only** - Feature is visible only to platform/testing roles or internal tenants.
3. **Compatibility gate** - Feature is limited to app versions and API contracts that can support it.
4. **Pilot tenant** - One or more tenants receive controlled access with support visibility.
5. **Cohort rollout** - Access expands by tenant group, user group, platform, device type, geography, percentage, or plan.
6. **General availability** - Feature becomes default for eligible tenants/plans/roles.
7. **Monitoring and support** - Reports track adoption, errors, sync health, conflicts, notification outcomes, support cases, and billing effects.
8. **Rollback or emergency disablement** - Admin/API can quickly stop the feature, preserve safe mobile messaging, and guide support.

Rollout is not complete until mobile states, API outcomes, support visibility, reports, billing effects, and rollback behavior are documented.

## Plan Limit Logic

Feature flags should support plan limits without becoming billing authority.

Principles:

- Billing/plan entitlement defines the commercial ceiling.
- Feature flags decide exposure inside that commercial ceiling.
- A tenant cannot enable a feature that its plan does not permit unless an authorized platform role grants a documented trial, promotion, or exception.
- Mobile users should receive product outcomes, not raw billing records.
- Tenant admins and billing roles should see plan-limit explanations appropriate to their role.
- Offline replay must recheck current plan, quota, entitlement, feature flag, and permission state before API acceptance.
- Reports should distinguish rollout-disabled, plan-disabled, permission-denied, version-blocked, and emergency-disabled outcomes.

Plan-gated feature states should support clear mobile outcomes:

| Plan outcome | Mobile state |
| --- | --- |
| Included and enabled | Visible. |
| Included but tenant disabled | Disabled or hidden depending on context. |
| Not included | Blocked with contact-admin/contact-support/upgrade guidance appropriate to role. |
| Quota reached | Blocked or limited with quota-safe wording. |
| Trial active | Visible or beta with support/billing context. |
| Trial expired or payment failed | Blocked or limited, with tenant-admin/billing next action. |

Feature flags should help monetize and operate modules, but billing logic remains Admin/API authority.

## Admin Checklist

Use this checklist before planning any feature flag.

| Question | Required answer |
| --- | --- |
| What feature is controlled? | A named mobile feature, workflow, capability, NativePHP capability, sync behavior, report, notification, support action, or billing-related module. |
| Why does it need a flag? | Rollout, tenant variation, plan limit, version compatibility, offline control, supportability, emergency disablement, or risk reduction is named. |
| What scopes exist? | Global, tenant, plan, role, permission, user, app version, device, cohort, maintenance, or emergency scope is explicit. |
| What is the resolution order? | Safety, plan, global, tenant, role/permission, user, version/device/cohort, and offline decisions are considered. |
| What can override what? | Higher-safety blocks, plan limits, tenant disables, permission denials, version blocks, and emergency disablement rules are explicit. |
| What does mobile receive? | Hidden, visible, disabled, blocked, beta, deprecated, update-required, offline-limited, or emergency-disabled state. |
| What is the disabled behavior? | Hide, disable with explanation, block with next action, retry later, update required, contact admin, or contact support. |
| What is the admin impact? | Affected tenants/users/plans/versions/devices/cohorts, mobile effect, sync effect, support effect, report effect, billing effect, audit, and rollback are named. |
| What rollout path applies? | Internal, pilot, cohort, plan/tenant/user rollout, general availability, rollback, or emergency disablement. |
| What plan limit applies? | Included, not included, quota-limited, trial, expired, payment-failed, exception, or promotion outcome is explicit. |
| What remote config applies? | Configurable values, defaults, tenant overrides, cache freshness, offline behavior, validation, fallback, support, audit, and rollback are documented in Remote Configuration Logic. |
| What is out of scope? | Database fields, schemas, endpoints, controllers, policies, resources, services, jobs, and code remain deferred until implementation. |

## Risks

| Risk | Feature flag response |
| --- | --- |
| Feature flags become authorization | Flags shape availability; API authorization and policies still enforce access. |
| Flags become unmanageable sprawl | Every flag needs owner, scope, purpose, rollout state, disabled behavior, support meaning, audit expectation, and retirement plan. |
| Mobile sees raw flag internals | API returns resolved mobile-safe states instead of raw flag machinery. |
| User override bypasses tenant or plan | User-level decisions can refine access only inside global, tenant, plan, permission, version, and safety boundaries. |
| Admins cannot predict impact | Admin UI must explain affected scope, mobile effect, sync effect, support, reports, billing, audit, and rollback. |
| Disabled features confuse users | Use hidden, disabled, blocked, update-required, offline-limited, or support states intentionally. |
| Rollout causes support spikes | Start internal/pilot, expose support context, monitor reports, and keep rollback ready. |
| Plan gating leaks billing internals | Mobile receives product outcomes; billing details stay role-scoped in Admin/API. |
| Offline queued work violates new flags | Replay rechecks current flag, plan, permission, version, tenant, and maintenance state. |
| Remote config is mistaken for feature availability | Flags decide availability; remote config tunes safe behavior only after API authority resolves the feature state. |
| Old flags stay forever | Flags should have a lifecycle: proposed, active rollout, default-on, deprecated, retired, or emergency-disabled. |

## Success Test

Feature flag logic is successful when every important mobile feature can be enabled, disabled, limited, rolled out, blocked, or rolled back by Admin/API; the API resolves global, tenant, user, plan, version, permission, device, cohort, and offline factors into a clear mobile state; admins understand the impact of changes; support can explain outcomes; billing limits remain server-owned; and mobile never treats flags as authority.
