# API/Admin App

Final Consistency Review is defined in `../../docs/final-consistency-review.md`:
all SaaS idea documentation must preserve API-only mobile authority,
admin-controlled configurable features, separated feature flags and remote
config, tenant isolation, clear offline behavior, permission-aware
NativePHP features, logical billing and plan limits, privacy-safe support,
tenant-bound reports, docs-only planning language, no database-field
definitions, and consistent terminology.

Final Optimized SaaS Blueprint is defined in `../../docs/final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

`apps/api-admin` is the target home for the Laravel API and Livewire admin
control plane.

Product Vision is defined in `../../docs/product-vision.md`: this app exists
because SaaS authority, tenant control, support, billing, version policy,
reports, audit, and sync decisions must be centralized.

Product Positioning is defined in `../../docs/product-positioning.md`: this app
is the SaaS control center side of the product, not a generic admin dashboard.

Core Product Principles are defined in `../../docs/product-principles.md`: this
app must keep authority server-side, tenant-scoped, feature-controlled,
secure-by-default, API-first, documented, and modular.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this app must expose mobile behavior only
through predictable API contracts that return user context, permissions,
feature flags, config, version rules, mobile-friendly errors, sync/conflict
outcomes, and tenant-safe responses.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: this app must not add admin
controls, API behavior, permissions, sync/conflict decisions, or mobile effects
until feature purpose, mobile impact, API dependency, permission ownership, and
risk are documented.

Target User Roles are defined in `../../docs/user-roles.md`: this app must map
each admin, support, billing, tenant, mobile, invited, suspended, and
guest/pre-login responsibility to server-side authority and visibility.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: this app must make
stakeholder value operable through admin controls, API contracts, reports,
security, billing/operations context, support context, notifications, and
feature flags.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: this app owns the trusted side of the
boundary and must expose mobile-safe outcomes only through API contracts.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this app owns tenant management,
users and permissions, admin operations, API contracts, feature/config/version
control, notifications, billing, support, reporting, audit, conflicts, and
security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this app must expose API
outcomes that let mobile own UX, local session presentation, cache, offline
actions, NativePHP capability UX, navigation, permissions UX, sync display,
drafts, local feedback, and feature visibility without granting local
authority.

Mobile UX Principles are defined in `../../docs/mobile-ux-principles.md`: this
app must return API outcomes that the NativePHP client can present as simple
navigation, loading/offline states, thumb-friendly actions, secure session
states, feature visibility, and native permission education.

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

Messaging And Community Logic is defined in `../../docs/messaging-community-logic.md`:
conversation behavior, support chat behavior, message attachments,
moderation, reports/abuse flow, notification behavior, offline message
drafts, admin visibility boundaries, and privacy principles must remain
tenant-enabled, plan-controlled, permission-aware, moderation-ready,
abuse-report-safe, notification-safe, offline-draft-safe, privacy-safe,
auditable, and Admin/API-authoritative.

AI Feature Logic is defined in `../../docs/ai-feature-logic.md`:
AI assistant purpose, summarization, categorization, smart suggestions,
moderation assistance, report generation assistance, admin AI controls,
tenant opt-in, privacy, and human-review principles must remain
tenant-enabled, plan-controlled, permission-aware, opt-in-only,
provider-neutral, human-reviewed, privacy-safe, audit-ready,
rate-limited, cost-aware, and Admin/API-authoritative.

Acceptance Principles are defined in `../../docs/acceptance-principles.md`:
every feature must document purpose, admin control, mobile behavior,
API dependency, offline behavior, permission behavior, feature flag
behavior, tenant behavior, error behavior, security behavior, and
documentation requirements before implementation planning begins.

Risk Map is defined in `../../docs/risk-map.md`:
API dependency, offline sync, tenant isolation, mobile secure storage,
NativePHP plugin availability, app store releases, forced updates,
feature flag mistakes, billing restrictions, admin misconfiguration,
support access, privacy, and data conflicts must document prevention
principles and documentation requirements before implementation.

Testing Strategy Principles are defined in `../../docs/testing-strategy-principles.md`:
future tests for API contracts, admin controls, mobile feature visibility,
permissions, feature flags, remote config, authentication, tenant isolation,
offline sync, conflict behavior, native feature fallbacks, notification flows,
billing rules, and app version rules must map to documented authority, risk,
and user-visible behavior before implementation.

Release And Versioning Principles are defined in `../../docs/release-versioning-principles.md`:
API versioning, mobile app versioning, admin releases, feature rollout,
rollback, app store release, forced update, documentation update, and Git
change-history decisions must preserve documented authority, compatibility,
rollback, support, audit, and user-visible behavior before release.

Documentation Audit is defined in `../../docs/documentation-audit.md`:
project documentation for two-system architecture, Admin/API authority, mobile
client execution, API-first communication, feature flags, remote config,
tenancy, permissions, offline sync, NativePHP features, notifications, billing,
support, reports, security, risks, and release principles must use consistent
authority language and resolve contradictions before implementation.

Feature Dependency Map is defined in `../../docs/feature-dependency-map.md`:
major features must document dependencies on authentication, tenant context,
permissions, feature flags, remote config, API availability, offline cache,
NativePHP permissions, subscription plan, and admin settings before
implementation planning or release decisions.

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

## Product Role

This system owns SaaS authority:

- tenants and tenant lifecycle
- users, roles, permissions, invitations, sessions, and devices
- feature flags and tenant/user overrides
- remote config and app version policy
- maintenance mode and force update rules
- notifications and push registration policy
- records/content API authority
- offline sync acceptance, replay windows, and conflict decisions
- support, billing, reports, audit, and security enforcement

Admin Control Center logic is defined in
`../../docs/admin-control-center-logic.md`. Future API/Admin implementation
must map each tenant, user, role, permission, mobile feature, remote config,
app version, maintenance, force update, sync, notification, report, billing,
and support control to that document before code is written.

Feature Flag Logic is defined in `../../docs/feature-flag-logic.md`. Future
API/Admin implementation must resolve global, tenant, plan, role, permission,
user, app-version, device, cohort, maintenance, and emergency feature decisions
into API outcomes before mobile uses them.

Remote Configuration Logic is defined in
`../../docs/remote-configuration-logic.md`. Future API/Admin implementation
must validate, scope, version, audit, and safely expose resolved mobile config
without letting config become authorization, billing, tenant, or permission
authority.

Mobile Version Control Logic is defined in
`../../docs/mobile-version-control-logic.md`. Future API/Admin implementation
must resolve minimum supported versions, optional updates, forced updates,
maintenance mode, store links, update messages, and outdated-client protection
into mobile-safe API outcomes.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`. Future API/Admin implementation must
confirm, audit, impact-preview, mobile-preview, rollback-plan, and
tenant-isolate dangerous admin actions before those controls affect mobile
users or tenants.

Mobile UX Principles are defined in `../../docs/mobile-ux-principles.md`.
Future API/Admin implementation must expose mobile-safe states that support
simple NativePHP navigation, clear loading/offline behavior, fast actions,
secure sessions, and native permission education.

## Current Implementation State

This directory is now a Laravel 13 application with a Livewire admin dashboard
shell, the first versioned mobile API route, and a public mobile contract
catalogue. It also has admin session authentication and the first mobile API
authentication foundation.

Implemented foundation:

- `GET /admin/dashboard` renders `App\Livewire\Admin\Dashboard`.
- `/` redirects to `/admin/dashboard`.
- `GET /api/v1/mobile/status` returns the standard mobile success envelope.
- `GET /api/v1/mobile/contracts` returns the v1 mobile contract catalogue.
- `POST /api/v1/mobile/auth/register` creates a mobile user, device session,
  access token, refresh token, and audit event.
- `POST /api/v1/mobile/auth/login` creates a revocable mobile token set.
- `POST /api/v1/mobile/auth/refresh` rotates refresh/access tokens.
- `POST /api/v1/mobile/auth/logout` revokes the current device session.
- `POST /api/v1/mobile/auth/logout-all` revokes all active mobile sessions for
  the current user.
- `GET /api/v1/mobile/auth/user` returns current user/session context.
- `PATCH /api/v1/mobile/auth/profile` updates allowed profile fields.
- `GET /api/v1/mobile/bootstrap` returns the first authenticated mobile
  operating context with real user, device-session, current tenant, and
  available tenant data, role-derived permission payloads, resolved feature
  flags, resolved remote config, resolved subscription state, resolved
  notification preferences, and resolved sync policy.
- `GET /api/v1/mobile/app-version` returns resolved app-version, update, and
  maintenance policy for the reported mobile platform/version context.
- `GET /api/v1/mobile/config` returns resolved mobile-safe remote config with
  tenant context, freshness metadata, compatibility state, and deterministic
  config version metadata.
- `GET /api/v1/mobile/features` returns resolved mobile-safe feature outcomes
  for the current user and tenant through the standard response envelope.
- `GET /api/v1/mobile/billing/subscription` returns mobile-safe subscription,
  plan, limits, usage, and feature-impact state for the current tenant.
- `POST /api/v1/mobile/diagnostics` accepts a privacy-filtered diagnostics
  snapshot for the current active tenant when support diagnostics are enabled
  by remote config, re-applies server-side redaction, stores only support-safe
  report metadata, and writes a security audit event.
- `/admin/mobile/diagnostics` lets platform admins search and review redacted
  diagnostics reports without exposing mobile-user emails or raw support
  secrets. Ticket-linked support queues and support-agent scoping remain
  pending.
- `GET /api/v1/mobile/tenants` returns the authenticated user's tenant context.
- `POST /api/v1/mobile/tenants/current` switches the current tenant after
  membership/lifecycle checks and records a security audit event.
- `App\Services\MobilePermissions\MobilePermissionResolver` derives nested
  mobile permission state from the current active tenant role and fails closed
  when the user has only invited, suspended, or unavailable memberships.
- `mobile_feature_flags`, `tenant_feature_overrides`, and
  `user_feature_overrides` provide the first feature flag data model.
- `App\Services\MobileFeatures\MobileFeatureResolver` resolves user override,
  tenant override, global default, emergency-gate, maintenance-gate, plan-gate,
  cohort-gate, device-gate, permission-gate, and minimum-app-version outcomes
  into mobile-safe feature states.
- `mobile_remote_configs` and `tenant_remote_config_overrides` provide the
  first remote config data model.
- `App\Services\MobileConfig\MobileRemoteConfigResolver` merges foundation,
  global, and tenant config into resolved mobile-safe config payloads.
- `mobile_app_version_policies` provides the first scoped version and
  maintenance policy data model for global/platform, tenant, and cohort
  decisions.
- `App\Services\MobileVersion\MobileAppVersionPolicyResolver` resolves
  supported, optional-update, force-update, blocked, and maintenance outcomes
  from tenant, cohort, platform, and global fallback policy order.
- `App\Services\Billing\MobileSubscriptionResolver` resolves tenant
  subscription state and mobile-safe plan/limit hints from Admin/API-owned
  tenant data.
- `App\Services\Notifications\MobileNotificationPolicyResolver` resolves
  tenant notification preferences, quiet hours, push-registration hints,
  unread counts, and fail-closed no-tenant behavior for bootstrap.
- `GET /api/v1/mobile/notifications`, read/read-all/delete endpoints, and
  push-token register/revoke endpoints provide tenant/user-safe notification
  inbox behavior with audit history. Admin notification creation and provider
  delivery remain pending.
- `App\Services\Sync\MobileSyncPolicyResolver` resolves tenant sync policy from
  tenant settings, remote config, permissions, subscription state, and
  maintenance policy and marks server replay endpoints ready when sync gates
  pass.
- `GET /api/v1/mobile/sync/bootstrap`, `GET /api/v1/mobile/sync/pull`,
  `POST /api/v1/mobile/sync/push`, and
  `POST /api/v1/mobile/sync/acknowledge` provide records-only sync bootstrap,
  cursor pull, idempotent replay, conflict responses, sync event storage, and
  acknowledgement.
- Policies are registered for current mobile control-plane resources:
  feature flags, tenant/user feature overrides, remote config, tenant remote
  config overrides, app-version policies, and mobile diagnostic reports.
- `GET /admin/login` renders the admin login form.
- `POST /admin/login` authenticates platform-admin users.
- `POST /admin/logout` invalidates the admin session.
- `/admin/dashboard` is protected by session auth and platform-admin access.
- `/admin/mobile/features` is protected by session auth and platform-admin
  access, and manages audited global mobile feature defaults, plan gates,
  cohort gates, and device constraints.
- `/admin/mobile/feature-overrides` is protected by session auth and
  platform-admin access, and manages audited tenant-scoped mobile feature
  overrides with confirmation, impact preview, and audit-history restore.
- `/admin/mobile/user-feature-overrides` is protected by session auth and
  platform-admin access, and manages audited user-scoped mobile feature
  overrides with tenant membership validation and audit-history restore.
- `/admin/mobile/config` is protected by session auth and platform-admin
  access, and manages audited global mobile remote config defaults with JSON
  validation, impact preview, and audit-history restore.
- `/admin/mobile/tenant-config` is protected by session auth and platform-admin
  access, and manages audited tenant remote config overrides with JSON
  validation, impact preview, and audit-history restore.
- `/admin/mobile/app-versions` is protected by session auth and platform-admin
  access, and manages audited global/platform, tenant, and cohort mobile
  version policies with version-range targeting, confirmation, impact preview,
  and audit-history restore.
- `App\Actions\Admin\SaveMobileFeatureFlagAction` persists feature defaults and
  writes before/after audit metadata to `security_audit_events`.
- `App\Actions\Admin\SaveTenantFeatureOverrideAction` persists tenant feature
  overrides and writes before/after audit metadata to `security_audit_events`.
- `App\Actions\Admin\SaveUserFeatureOverrideAction` persists user feature
  overrides and writes before/after audit metadata to `security_audit_events`.
- `App\Actions\Admin\SaveMobileRemoteConfigAction` persists global config
  defaults and writes before/after audit metadata to `security_audit_events`.
- `App\Actions\Admin\SaveTenantRemoteConfigOverrideAction` persists tenant
  config overrides and writes before/after audit metadata to
  `security_audit_events`.
- `App\Actions\Admin\SaveMobileAppVersionPolicyAction` persists scoped version
  policies and writes before/after audit metadata to `security_audit_events`.
- `App\Actions\Records\SaveTenantRecordAction` persists tenant-scoped record
  create/update payloads with category/tag resolution, note append, attachment
  metadata, activity timeline entries, and security audit events.
- `App\Actions\Records\ArchiveTenantRecordAction` and
  `App\Actions\Records\RestoreTenantRecordAction` control reversible record
  lifecycle transitions with audit metadata.
- `App\Support\Api\MobileApiResponse` centralizes success and error envelopes.
- `App\Support\Api\MobileContractRegistry` centralizes documented contract
  groups, implemented routes, and planned routes.
- Mobile access and refresh tokens are stored only as SHA-256 hashes.
- `security_audit_events` records mobile auth/security actions.
- Blade layouts exist for admin, auth, and dashboard surfaces.
- Reusable admin Blade components exist for section headings and status badges.
- Pest tests cover the dashboard route, root redirect, feature flag admin
  controls, tenant and user feature override controls, remote config admin
  controls, tenant remote config controls, app version admin controls, remote
  config resolution, feature maintenance/plan/cohort/device/emergency/app-version
  gates, tenant/cohort app version policy, app-version range resolution, mobile
  billing subscription resolution, mobile notification policy and endpoint
  behavior, mobile sync policy resolution, tenant-scoped records API behavior,
  resource policies, success envelope, error envelope, contract catalogue, and
  contract Markdown file coverage.

Still pending:

- Admin tenant management, invitations, full permission management UI,
  broader resource policy expansion, and broader control-plane audit.
- Admin records/content management screens, standalone records subresource
  endpoints, non-record sync replay, admin sync monitoring, notifications,
  support, billing admin, and reports.
- Protected domain routes for notifications, support, billing, reports, and
  remaining feature/config/version policy surfaces.

Verification commands for this app:

```bash
composer validate --strict
php artisan route:list --except-vendor
php artisan test --compact
vendor/bin/pint --dirty --format agent
npm run build
```

Before implementing endpoints or screens, update the relevant contract in
`contracts/api` and keep `docs/implementation-status.md` accurate.
