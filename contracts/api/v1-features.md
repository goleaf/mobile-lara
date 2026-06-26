# API v1 Features Contract

Updated: 2026-06-26

Status: partially implemented. `GET /api/v1/mobile/features` returns resolved
global, tenant, and user feature outcomes for the current tenant/user context.
The admin panel manages audited global feature defaults and tenant-scoped
overrides with mobile impact previews. It also manages membership-safe
user-scoped overrides with audit-history restore. Minimum app-version gates are
enforced for otherwise-enabled features, and global flags can require plan keys,
cohort keys, or device constraints before a feature remains enabled.
Emergency-disabled states fail closed before lower-scope overrides can
re-enable a feature. App-version maintenance policy blocks ordinary enabled
features while leaving support behavior available. Richer billing plan
authority and mobile-local feature cache integration remain pending.

Product Vision is defined in `../../docs/product-vision.md`: this contract
keeps important mobile capabilities feature-controlled by Admin/API.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract is the API-first expression of the feature-controlled platform.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must expose resolved
feature purpose, availability states, context, mobile-friendly disabled
messages, version constraints, and tenant-safe outcomes through API only.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: feature behavior must
document purpose, admin mobile effect, mobile screen dependency, online/offline
availability, permission owner, rollout risk, and rollback before
implementation.

Target User Roles are defined in `../../docs/user-roles.md`: feature outcomes
must resolve role and account-state access into mobile-safe states.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: feature outcomes
must explain stakeholder value from rollout control, tenant adoption, mobile
clarity, support explanation, billing entitlements, and security boundaries.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: feature authority and rollout decisions
stay in Admin/API while mobile renders resolved enabled, disabled, blocked,
deprecated, or update-required states.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to feature
control, API contracts, billing/subscription logic, mobile version rules,
support/report visibility, audit history, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports
API-derived feature visibility, disabled/blocked/deprecated/update-required
feedback, navigation shaping, cache freshness, and offline-limited messaging.

Mobile UX Principles are defined in `../../docs/mobile-ux-principles.md`: this
contract should support mobile-first navigation, simple screens, clear
loading/offline states, thumb-friendly controls, minimum typing, fast actions,
secure sessions, feature visibility, and native permission education.

Mobile App Shell Logic is defined in `../../docs/mobile-app-shell-logic.md`:
shell states must coordinate welcome, authenticated, locked, offline, maintenance, forced update, tenant
switching, sync-in-progress, permission-blocked, and feature-disabled behavior
before implementation.

Mobile Dashboard Logic is defined in `../../docs/mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Mobile Settings Logic is defined in `../../docs/mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `../../docs/mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

Authentication Principles are defined in `../../docs/authentication-principles.md`:
mobile login must happen through the API only; access and refresh tokens must
use secure storage; refresh, logout, logout-all-devices, tenant selection,
session expiry, offline already-authenticated behavior, and server revocation
must preserve Admin/API authority before implementation.

Mobile App Lock Principles are defined in `../../docs/mobile-app-lock-principles.md`:
the mobile client must lock on security-sensitive lifecycle, timeout,
account, tenant, offline-cache, and admin-policy conditions; require
confirmation for sensitive areas; use biometric or PIN unlock only as local
protection; handle failed attempts, logout, admin-disabled biometrics, and
offline cached data without bypassing Admin/API authority.

Role And Permission Logic is defined in `../../docs/role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact
with feature flags as separate gates; suspended users and suspended tenants
fail closed without bypassing tenant isolation.

Audit Logic is defined in `../../docs/audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

Data Privacy Principles are defined in `../../docs/data-privacy-principles.md`:
tenant isolation, least privilege, secure local mobile data, secure native
storage, export and deletion boundaries, support access limits, admin
visibility boundaries, privacy-by-default behavior, and mobile diagnostics
privacy limits must protect users and tenants without turning mobile cache,
support views, or audit history into uncontrolled data exposure.

Tenant Lifecycle Logic is defined in `../../docs/tenant-lifecycle-logic.md`:
tenant creation, onboarding, trial, active, suspended, archived,
billing-blocked, deletion/requested deletion, and restore states must be
Admin/API-owned lifecycle decisions that mobile presents as safe,
tenant-scoped, billing-aware, supportable states without inventing local
tenant authority.

Tenant Admin Logic is defined in `../../docs/tenant-admin-logic.md`:
tenant admins may manage tenant-scoped users, invitations, delegated settings,
delegated mobile-feature controls, tenant reports, and tenant support workflows
only inside their tenant; platform-only controls, cross-tenant visibility,
global policy, billing authority, lifecycle authority, app-version policy, and
security posture remain Admin/API-owned boundaries.

Multi-Tenant Mobile Logic is defined in `../../docs/multi-tenant-mobile-logic.md`:
users with more than one tenant choose and remember tenant context through
API-confirmed state; tenant switching, tenant-scoped cache, per-tenant
permissions and feature flags, sync replay, offline behavior, and logout
cleanup must preserve tenant isolation and never turn mobile-local state
into tenant authority.

Offline-First Principles are defined in `../../docs/offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

Offline UX Logic is defined in `../../docs/offline-ux-logic.md`:
offline UX must calmly explain banners, disabled online-only actions,
local drafts, pending indicators, retry, sync success or failure,
saved-local versus synced state, and data-loss prevention whenever
connection changes.

Records/Content Module Logic is defined in `../../docs/records-content-module-logic.md`:
records are tenant-scoped business content with API-owned lifecycle,
notes, attachments, activity, tags, categories, status, offline draft or
sync behavior, admin controls, permissions, feature flags, audit, and
reporting boundaries.

Search Logic is defined in `../../docs/search-logic.md`:
search stays tenant-scoped, permission-aware, feature-controlled,
privacy-preserving, and explicit about local-cache limits versus
API-authoritative results, including recent searches, saved filters, filtering,
sorting, scan-to-search, offline limits, and admin-controlled boundaries.

Forms And Drafts Logic is defined in `../../docs/forms-drafts-logic.md`:
mobile forms must stay simple, validated, autosave-aware, offline-draft
safe, API-submitted, admin-controlled, and explicit about local-save versus
server-accepted state so user work is protected without bypassing authority.

Notifications Logic is defined in `../../docs/notifications-logic.md`:
notification targeting, delivery policy, push behavior, in-app inbox,
read/unread state, deep links, preferences, offline behavior, and tenant or
permission boundaries must remain Admin/API-authoritative and mobile-safe.

Support System Logic is defined in `../../docs/support-system-logic.md`:
support requests, support messages, attachments, tenant context, support-agent
visibility, audit, notifications, and offline support drafts must remain
tenant-scoped, least-privilege, privacy-safe, and Admin/API-authoritative.

Billing And Plan Logic is defined in `../../docs/billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature states,
and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

Reporting Logic is defined in `../../docs/reporting-logic.md`:
admin measurements, tenant-admin measurements, mobile-visible summaries,
privacy boundaries, date ranges, exports, feature usage, sync health,
notification, support, and billing reports must remain tenant-scoped,
permission-aware, privacy-safe, auditable, and Admin/API-authoritative.

Native Feature Strategy is defined in `../../docs/native-feature-strategy.md`:
NativePHP capability use, logical service boundaries, browser/development
fallbacks, permission education, admin feature-flag control, native failure
UX, and offline sync behavior must remain feature-scoped, tenant-safe,
privacy-aware, fallback-safe, and Admin/API-authoritative.

Camera And Media Logic is defined in `../../docs/camera-media-logic.md`:
photo capture, media selection, media preview, record/support attachments,
offline media storage, upload queues, feature-flag control, permission
denial, size limits, and privacy behavior must remain tenant-scoped,
permission-aware, fallback-safe, queue-safe, privacy-safe, and
Admin/API-authoritative.

Scanner Logic is defined in `../../docs/scanner-logic.md`:
QR/barcode scan-to-search, scan-to-create, scan-to-validate, scan history,
offline scanning, invalid scan handling, duplicate scan handling, admin
feature flags, and camera/permission dependency behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear,
duplicate-safe, privacy-safe, and Admin/API-authoritative.

Geolocation Logic is defined in `../../docs/geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Device, Network, And Diagnostics Logic is defined in `../../docs/device-network-diagnostics-logic.md`:
device information use, network status use, offline detection, diagnostics
export, support troubleshooting context, diagnostics redaction, admin mobile
device visibility, and user-controlled diagnostics sharing must remain
tenant-scoped, permission-aware, support-scoped, privacy-safe, redacted,
audit-ready, and Admin/API-authoritative.

Module Selection Principles are defined in `../../docs/module-selection-principles.md`:
optional industry modules such as field service, logistics, ecommerce,
booking, education, events, support, community/messaging, reports, and AI
assistant must be tenant-enabled, plan-controlled, permission-aware,
mobile-hidden when unavailable, feature-flag-safe, documented before
implementation, and Admin/API-authoritative.

Field Service Logic is defined in `../../docs/field-service-logic.md`:
work order lifecycle, technician mobile flow, check-in/check-out, photos,
notes, future signatures, offline behavior, admin dispatch/control, and
report visibility must remain tenant-enabled, plan-controlled,
permission-aware, offline-clear, evidence-safe, privacy-safe, auditable,
and Admin/API-authoritative.

Booking Logic is defined in `../../docs/booking-logic.md`:
service selection, availability logic, booking requests, confirmation,
cancellation, reschedule, reminders, admin schedule control, tenant rules,
and mobile offline limitations must remain tenant-enabled, plan-controlled,
permission-aware, availability-safe, schedule-conflict-safe, reminder-safe,
offline-limited, privacy-safe, auditable, and Admin/API-authoritative.

Commerce Logic is defined in `../../docs/commerce-logic.md`:
catalog browsing, cart behavior, checkout principles, hosted payment
boundaries, order lifecycle, invoice/receipt principles, subscription
upsell, admin product/control, and mobile offline limitations must remain
tenant-enabled, plan-controlled, permission-aware, price-safe,
inventory-safe, hosted-payment-safe, offline-limited, privacy-safe,
auditable, and Admin/API-authoritative.

Logistics Delivery Logic is defined in `../../docs/logistics-delivery-logic.md`:
delivery job lifecycle, pickup flow, drop-off flow, proof of delivery,
scan validation, location check-in, failed delivery reasons, offline
behavior, and admin monitoring must remain tenant-enabled, plan-controlled,
permission-aware, scan-safe, location-purpose-limited, offline-clear,
privacy-safe, auditable, and Admin/API-authoritative.

Voice Note Logic is defined in `../../docs/voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

Sync Lifecycle Logic is defined in `../../docs/sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `../../docs/conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

Admin Control Center Logic is defined in
`../../docs/admin-control-center-logic.md`: this contract must keep feature
enablement, disablement, rollout, rollback, plan limits, emergency blocks,
and disabled mobile states scoped, authorized, auditable, and exposed to mobile
only as resolved API outcomes.

Feature Flag Logic is defined in `../../docs/feature-flag-logic.md`: this
contract must resolve important mobile features through controlled purpose,
global/tenant/user priority, disabled-state behavior, admin impact, safe
rollout, and plan-limit rules before mobile receives any feature outcome.

Remote Configuration Logic is defined in
`../../docs/remote-configuration-logic.md`: enabled, disabled, blocked, beta,
deprecated, update-required, offline-limited, and emergency-disabled feature
states may receive resolved copy, limits, thresholds, workflow options, support
guidance, and offline/sync presentation without giving config feature
authority.

## Purpose

Feature endpoints expose resolved mobile-safe feature outcomes. Mobile never
receives raw global, tenant, user, plan, version, cohort, maintenance, or
emergency flag internals.

## Implemented Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/features` | Return resolved feature availability for the current context. | mobile token |

## Success Data

The response returns `features`, keyed by feature code. Each feature includes
`state`, `visible`, `enabled`, `reason`, `next_action`, `minimum_app_version`,
`required_plans`, `allowed_cohorts`, `device_constraints`, `offline_behavior`,
and optional `message`.

The top-level payload also includes `plan_key`, `cohort_key`, `device_context`,
`maintenance`, and `reported_app_version` when the client reports it through
`X-Mobile-App-Version` or the authenticated device session has a stored version
from login/register.

Allowed states include `hidden`, `visible`, `disabled`, `blocked`, `beta`,
`deprecated`, `update_required`, `offline_limited`, and `emergency_disabled`.

## Gates

The current implementation resolves user override, tenant override, then global
default, with emergency, maintenance, plan, cohort, device, permission, and
minimum-app-version gates applied before mobile receives the final state.
Emergency-disabled states at the global, tenant, or user level fail closed with
`next_action` set to `contact_support`. If app-version maintenance is active,
ordinary enabled features become `blocked` with `next_action` set to `retry`;
the `support` feature is allowed to continue through plan and permission gates.
If an otherwise-enabled feature is not included in the resolved `plan_key`, the
state becomes `blocked` with `next_action` set to `upgrade_plan`. If an
otherwise-enabled feature is not included in the reported `cohort_key`, the
state becomes `blocked` with `next_action` set to `contact_admin`. If the
current device platform or device ID does not match `device_constraints`, the
state becomes `blocked` with `next_action` set to `use_supported_device`. If an
otherwise-enabled feature has a `minimum_app_version` above the reported app
version, the resolved state becomes `update_required` with `next_action` set to
`update_app`. Future slices must add richer billing plan authority and richer
offline limitations.

## Offline Behavior

Mobile may cache resolved features with freshness metadata. A stale feature
cache cannot broaden access and must hide or disable risky actions when unsure.

## Audit

Audit admin feature changes, tenant overrides, user overrides, emergency
disable, rollout changes, and support-visible denials.

The current admin implementation writes `admin_mobile_feature_flag_created` and
`admin_mobile_feature_flag_updated` events with before/after feature metadata
for global default changes. Tenant override controls write
`admin_tenant_feature_override_created`, `admin_tenant_feature_override_updated`,
and `admin_tenant_feature_override_restored` events with before/after
tenant-scoped metadata. User override controls write
`admin_user_feature_override_created`, `admin_user_feature_override_updated`,
and `admin_user_feature_override_restored` events with before/after
user-scoped metadata.

## Tests

Automated coverage:

- `apps/api-admin/tests/Feature/MobileFeatureFlagResolutionTest.php`
- `apps/api-admin/tests/Feature/AdminFeatureFlagsTest.php`
- `apps/api-admin/tests/Feature/AdminTenantFeatureOverridesTest.php`
- `apps/api-admin/tests/Feature/AdminUserFeatureOverridesTest.php`

Fresh checks:

```bash
cd apps/api-admin && php artisan test --compact --filter=MobileFeatureFlagResolutionTest
cd apps/api-admin && php artisan test --compact --filter=AdminFeatureFlagsTest
cd apps/api-admin && php artisan test --compact --filter=AdminTenantFeatureOverridesTest
cd apps/api-admin && php artisan test --compact --filter=AdminUserFeatureOverridesTest
```

Future Phase 8 coverage should add stale-cache behavior, richer billing plan
authority, and no raw flag layers in API responses beyond resolved mobile-safe
outcomes.
