# Feature Flag Logic

Final Consistency Review is defined in `final-consistency-review.md`:
all SaaS idea documentation must preserve API-only mobile authority,
admin-controlled configurable features, separated feature flags and remote
config, tenant isolation, clear offline behavior, permission-aware
NativePHP features, logical billing and plan limits, privacy-safe support,
tenant-bound reports, docs-only planning language, no database-field
definitions, and consistent terminology.

Final Optimized SaaS Blueprint is defined in `final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

Updated: 2026-06-26

This document defines the feature flag logic for Mobile Lara. It explains why important mobile features should be controlled by feature flags, how global, tenant, and user-level decisions should be prioritized, how disabled features should appear on mobile, how admins should understand impact, how flags support safe rollout, and how flags support plan limits. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, Filament resources, policies, jobs, services, providers, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Target User Roles](user-roles.md), [Admin Control Center Logic](admin-control-center-logic.md), [API-First Principles](api-first-principles.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX Principles](mobile-ux-principles.md), [Core Product Principles](product-principles.md), [SaaS Value Map](saas-value-map.md), [Documentation-First Architecture](documentation-first-architecture.md), [Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md), and [Admin Safety Principles](admin-safety-principles.md): feature flags are admin-controlled product decisions that reach mobile through API outcomes, mobile UX turns resolved feature states into clear navigation/screens/actions, remote config tunes safe runtime behavior for enabled features, mobile version control decides whether the build can safely use those outcomes, and flag purpose, mobile effect, permissions, rollout risk, support meaning, impact preview, audit, and rollback must be documented before implementation.

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

Scanner Logic is defined in `scanner-logic.md`:
QR/barcode scan-to-search, scan-to-create, scan-to-validate, scan history,
offline scanning, invalid scan handling, duplicate scan handling, admin
feature flags, and camera/permission dependency behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear,
duplicate-safe, privacy-safe, and Admin/API-authoritative.

Geolocation Logic is defined in `geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Device, Network, And Diagnostics Logic is defined in `device-network-diagnostics-logic.md`:
device information use, network status use, offline detection, diagnostics
export, support troubleshooting context, diagnostics redaction, admin mobile
device visibility, and user-controlled diagnostics sharing must remain
tenant-scoped, permission-aware, support-scoped, privacy-safe, redacted,
audit-ready, and Admin/API-authoritative.

Module Selection Principles are defined in `module-selection-principles.md`:
optional industry modules such as field service, logistics, ecommerce,
booking, education, events, support, community/messaging, reports, and AI
assistant must be tenant-enabled, plan-controlled, permission-aware,
mobile-hidden when unavailable, feature-flag-safe, documented before
implementation, and Admin/API-authoritative.

Field Service Logic is defined in `field-service-logic.md`:
work order lifecycle, technician mobile flow, check-in/check-out, photos,
notes, future signatures, offline behavior, admin dispatch/control, and
report visibility must remain tenant-enabled, plan-controlled,
permission-aware, offline-clear, evidence-safe, privacy-safe, auditable,
and Admin/API-authoritative.

Booking Logic is defined in `booking-logic.md`:
service selection, availability logic, booking requests, confirmation,
cancellation, reschedule, reminders, admin schedule control, tenant rules,
and mobile offline limitations must remain tenant-enabled, plan-controlled,
permission-aware, availability-safe, schedule-conflict-safe, reminder-safe,
offline-limited, privacy-safe, auditable, and Admin/API-authoritative.

Commerce Logic is defined in `commerce-logic.md`:
catalog browsing, cart behavior, checkout principles, hosted payment
boundaries, order lifecycle, invoice/receipt principles, subscription
upsell, admin product/control, and mobile offline limitations must remain
tenant-enabled, plan-controlled, permission-aware, price-safe,
inventory-safe, hosted-payment-safe, offline-limited, privacy-safe,
auditable, and Admin/API-authoritative.

Messaging And Community Logic is defined in `messaging-community-logic.md`:
conversation behavior, support chat behavior, message attachments,
moderation, reports/abuse flow, notification behavior, offline message
drafts, admin visibility boundaries, and privacy principles must remain
tenant-enabled, plan-controlled, permission-aware, moderation-ready,
abuse-report-safe, notification-safe, offline-draft-safe, privacy-safe,
auditable, and Admin/API-authoritative.

AI Feature Logic is defined in `ai-feature-logic.md`:
AI assistant purpose, summarization, categorization, smart suggestions,
moderation assistance, report generation assistance, admin AI controls,
tenant opt-in, privacy, and human-review principles must remain
tenant-enabled, plan-controlled, permission-aware, opt-in-only,
provider-neutral, human-reviewed, privacy-safe, audit-ready,
rate-limited, cost-aware, and Admin/API-authoritative.

Acceptance Principles are defined in `acceptance-principles.md`:
every feature must document purpose, admin control, mobile behavior,
API dependency, offline behavior, permission behavior, feature flag
behavior, tenant behavior, error behavior, security behavior, and
documentation requirements before implementation planning begins.

Risk Map is defined in `risk-map.md`:
API dependency, offline sync, tenant isolation, mobile secure storage,
NativePHP plugin availability, app store releases, forced updates,
feature flag mistakes, billing restrictions, admin misconfiguration,
support access, privacy, and data conflicts must document prevention
principles and documentation requirements before implementation.

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

Logistics Delivery Logic is defined in `logistics-delivery-logic.md`:
delivery job lifecycle, pickup flow, drop-off flow, proof of delivery,
scan validation, location check-in, failed delivery reasons, offline
behavior, and admin monitoring must remain tenant-enabled, plan-controlled,
permission-aware, scan-safe, location-purpose-limited, offline-clear,
privacy-safe, auditable, and Admin/API-authoritative.

Voice Note Logic is defined in `voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

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

## Feature Flag Decision Contract

Every important mobile feature needs a feature-flag decision before implementation because the Admin/API system must be able to control availability without shipping a new mobile build, protect tenants and plans from accidental access, stop unsafe behavior quickly, and explain feature outcomes to support, billing, admins, and mobile users.

| Decision area | Principle | Required outcome |
| --- | --- | --- |
| Flag purpose | The flag must exist for a named product reason: rollout, tenant variation, plan limit, app-version compatibility, offline control, NativePHP capability safety, supportability, or emergency recovery. | Admins know why the feature is controlled and when the flag can be retired, expanded, or emergency-disabled. |
| Scope priority | Resolution must apply safety and authority gates first, then plan/entitlement ceiling, global default, tenant decision, role/permission decision, user decision, device/app-version/cohort compatibility, and offline policy. | Global safety blocks cannot be bypassed by tenant or user enables; plan limits remain above tenant/user access; user-level flags never bypass tenant, plan, permission, version, or security boundaries. |
| Mobile disabled state | Disabled features must resolve into mobile-safe states such as hidden, disabled, blocked, beta, deprecated, update-required, offline-limited, or emergency-disabled. | Mobile shows a clear product outcome and next action without exposing raw flag names, tenant-private admin reasoning, billing internals, or rollout mechanics. |
| Admin impact | Admins must understand affected tenants, users, roles, plans, app versions, devices, cohorts, mobile screens, API behavior, offline queues, sync replay, notifications, reports, billing, support, audit, and rollback. | Enabling or disabling a feature is an explainable operational decision, not a hidden toggle with unknown blast radius. |
| Safe rollout | Flags must support documented proposal, internal-only use, compatibility gate, pilot tenant, cohort expansion, general availability, monitoring, rollback, and emergency disablement. | New features can expand gradually, be observed safely, and be stopped without confusing mobile users or support teams. |
| Plan limits | Billing and entitlement define the commercial ceiling; feature flags expose or hide behavior only inside that ceiling. | Mobile receives included, disabled, blocked, quota-limited, trial, expired, payment-failed, contact-admin, contact-support, or upgrade/contact-sales outcomes while billing authority remains in Admin/API. |

This contract is intentionally principle-level. It does not create flag storage, schemas, migrations, endpoints, policies, Filament resources, Livewire components, jobs, services, provider integrations, or application logic.

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
