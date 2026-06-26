# API v1 Billing Contract

Updated: 2026-06-26

Status: partially implemented. `GET /api/v1/mobile/billing/subscription`
returns mobile-safe subscription state from the current tenant's
Admin/API-owned subscription state and settings. Provider billing integration,
invoices, usage event writes, admin billing screens, and billing audit workflows
remain pending.

Product Vision is defined in `../../docs/product-vision.md`: this contract
keeps billing and entitlement authority centralized while mobile receives only
safe plan outcomes.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the SaaS control center by keeping billing and plan state
server-controlled while mobile receives clear entitlement outcomes.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must return predictable
entitlement, quota, plan-impact, feature-visibility, mobile-error, and
tenant-safe billing outcomes without exposing billing authority.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: billing behavior must
document mobile entitlement effect, API dependency, online/offline limits,
permission owner, support explanation, audit expectations, and commercial risks
before implementation.

Target User Roles are defined in `../../docs/user-roles.md`: billing outcomes
must distinguish billing manager authority, tenant admin visibility, support
context, and mobile entitlement messages.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: billing outcomes
connect commercial value to plan limits, entitlement-driven feature flags,
mobile-safe access messages, reports, security, and billing/operations insight.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: billing authority, quota checks, and
entitlement decisions stay in Admin/API while mobile shows allowed, blocked,
quota, or contact-admin outcomes.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to
billing/subscription logic, feature control, tenant management, API contracts,
reporting, audit history, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports
mobile-safe entitlement feedback, quota warnings, contact-admin/support prompts,
feature visibility, navigation limits, and local blocked-state messages without
giving mobile billing authority.

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
`../../docs/admin-control-center-logic.md`: this contract must keep billing,
plan, quota, entitlement, support-tier, and failed-payment controls scoped,
authorized, auditable, and exposed to mobile only as resolved API outcomes.

Remote Configuration Logic is defined in
`../../docs/remote-configuration-logic.md`: billing presentation, quota copy,
contact-admin guidance, entitlement messages, and support text may be tuned by
resolved config, but billing authority, plan state, payment outcomes, and quota
acceptance stay in Admin/API.

## Purpose

Billing endpoints expose mobile-safe plan and subscription state. Admin/API
owns plans, subscriptions, usage, invoice placeholders, plan-based feature
availability, and suspended/trial/expired behavior.

## Implemented Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/billing/subscription` | Return current tenant/user billing state for mobile presentation. | mobile token |

## Success Data

The response returns `status`, `plan`, `trial`, `features_limited`, `limits`,
`usage`, `available_actions`, `billing_portal`, `feature_impacts`, `source`,
`resolved_at`, and `subscription_version`.

The current implementation resolves:

- `status` from the current tenant's `subscription_state`.
- `plan`, `limits`, `usage`, trial dates, and billing portal URL from
  mobile-safe tenant settings.
- `features_limited` and `feature_impacts` from active/trialing versus
  past-due, expired, canceled, suspended, unknown, or missing tenant states.
- `subscription_version` as a deterministic support/debug key.

## Gates

Billing behavior is constrained by tenant subscription, role/permission,
feature flags, plan limits, app version, maintenance, and support policy.

## Offline Behavior

Mobile may display last-known billing state with freshness metadata. Expired,
suspended, or unknown billing states must fail closed for paid features.

## Audit

Audit plan changes, subscription state changes, usage events where documented,
billing portal access, and feature denials caused by billing.

## Tests

Current coverage:

```bash
cd apps/api-admin && php artisan test --compact --filter=MobileBillingSubscriptionTest
```

Future Phase 23 coverage should add provider integration, invoice placeholders,
usage event writes, admin billing screens, richer role authorization, and audit
history for plan or subscription changes.
