# Implementation Status

Updated: 2026-06-26

This file is the required implementation gate for Mobile Lara. It translates the
product Markdown corpus into an executable checklist and records the current
state before new implementation work continues.

Product Vision is defined in `docs/product-vision.md`. Status is tracked
against the vision before implementation grows.

Product Positioning is defined in `docs/product-positioning.md`. Status is also
tracked against the six positioning angles before implementation grows.

Core Product Principles are defined in `docs/product-principles.md`. Status is
tracked against admin authority, API-only mobile behavior, feature control,
tenant isolation, useful offline behavior, secure defaults, simple mobile UX,
documentation-first planning, and modular expansion.

API-First Principles are defined in `docs/api-first-principles.md`. Status is
tracked against API-only mobile communication, predictable responses, explicit
feature API purpose, permissions/feature/config/version/user context,
mobile-friendly errors, sync/conflict behavior, and tenant-boundary protection
before implementation grows.

Target User Roles are defined in `docs/user-roles.md`. Status is tracked
against role responsibilities, limitations, visibility, and control boundaries
before implementation grows.

SaaS Value Map is defined in `docs/saas-value-map.md`. Status is tracked
against platform owner, tenant business, tenant admin, mobile worker/client,
support team, and billing/operations value before implementation grows.

Two-System Boundary Logic is defined in `docs/two-system-boundary.md`. Status is
tracked against Admin/API ownership, mobile ownership, API-only behavior, local
cache boundaries, remote admin control, and offline reconciliation before
implementation grows.

Admin/API Responsibilities are defined in
`docs/admin-api-responsibilities.md`. Status is tracked against tenant
management, users and permissions, admin panel, API contracts, feature control,
remote configuration, mobile version rules, notifications, billing, support,
reports, audit, conflicts, and security before implementation grows.

Mobile Client Responsibilities are defined in
`docs/mobile-client-responsibilities.md`. Status is tracked against mobile UX,
secure local session, cache, offline actions, NativePHP device features,
navigation, permissions UX, sync display, drafts, feedback, and feature
visibility before implementation grows.

Mobile UX Principles are defined in `docs/mobile-ux-principles.md`. Status is
tracked against mobile-first navigation, simple screens, clear loading/offline
states, thumb-friendly controls, minimum typing, fast actions, admin-rule-based
feature visibility, secure session behavior, and native permission education
before implementation grows.

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

Admin Control Center logic is defined in
`docs/admin-control-center-logic.md`. Future implementation work must map
tenant, user, role, permission, mobile feature, remote config, app version,
maintenance, force update, sync, notification, report, billing, and support
controls to that document before code is written.

Feature Flag Logic is defined in `docs/feature-flag-logic.md`. Future
implementation work must map important mobile features to documented flag
priority, disabled mobile state, admin impact, rollout path, plan limits,
support meaning, audit expectation, and offline behavior before code is
written.

Remote Configuration Logic is defined in `docs/remote-configuration-logic.md`.
Future implementation work must map remote-configurable behavior to documented
scope, safe defaults, override rules, mobile caching, offline behavior,
validation, fallback, support visibility, audit, and rollback before code is
written.

Mobile Version Control Logic is defined in
`docs/mobile-version-control-logic.md`. Future app-version work must map
minimum supported versions, optional updates, forced updates, maintenance mode,
outdated responses, store links, update messages, support context, audit,
rollback, and old-version protection before code is written.

Admin Safety Principles are defined in `docs/admin-safety-principles.md`.
Future dangerous admin-control work must map confirmation, audit history, impact
preview, mobile impact preview, rollback, and tenant-isolated scope before code
is written.

Status values:

- `not started` - No durable implementation exists yet.
- `partial` - Some structure, UI, service, model, or test coverage exists, but
  the full Admin/API to API contract to mobile control loop is incomplete.
- `implemented` - The behavior exists in code for the relevant system.
- `tested` - The behavior has automated coverage and a fresh verification has
  passed for the current implementation slice.
- `documented` - The product behavior is documented, but implementation is not
  complete.

## Current Repository Evidence

| Area | Current state |
| --- | --- |
| Root application | A Laravel 13 + Livewire 4 + NativePHP Mobile app remains at the repository root as a transition mirror. |
| Requested monorepo paths | `apps/api-admin` and `apps/mobile-client` are now separate Laravel applications. |
| API routes | `apps/api-admin/routes/api.php` exposes versioned routes at `GET /api/v1/mobile/status`, `GET /api/v1/mobile/contracts`, public `GET /api/v1/mobile/app-version`, auth endpoints, authenticated `GET /api/v1/mobile/bootstrap`, `GET /api/v1/mobile/config`, `GET /api/v1/mobile/features`, `GET /api/v1/mobile/billing/subscription`, `GET /api/v1/mobile/tenants`, `POST /api/v1/mobile/tenants/current`, authenticated tenant invitation list/accept/decline endpoints, and tenant-scoped records list/create/detail/update/archive/restore endpoints. |
| Mobile routes | The root transition app still exposes 52 `mobile.*` Livewire routes; `apps/mobile-client/routes/web.php` now exposes 53 `mobile.*` routes including `mobile.settings.workspace`. |
| Active database | API/admin migrations now include users, framework tables, mobile device sessions, hashed mobile access/refresh tokens, security audit events, tenants, tenant-user memberships, feature flag tables, remote config tables, app-version policy tables, and tenant-scoped record/category/tag/note/attachment/activity tables. Broader control-plane domain schema remains pending. |
| Mobile local database | Dedicated `mobile_local` connection, local migrations, local models, repositories, and health command exist in `apps/mobile-client`. |
| Admin/API system | `apps/api-admin` contains a Laravel 13 app, protected Livewire dashboard shell, registered policies for current mobile control-plane resources, audited tenant lifecycle/settings/membership controls, audited global feature flag controls, audited global remote config controls, audited app-version policy controls, remote config resolver/API, app-version/maintenance resolver/API, admin session auth, shared API response envelope, mobile status endpoint, public contract catalogue endpoint, mobile auth/token/session endpoints, foundation tenant list/switch endpoints, and tenant-scoped records API foundation. Broader SaaS modules remain pending. |
| Contracts directory | `contracts/api` exists with response-envelope guidance, `v1-foundation.md`, and documented v1 contracts for auth, bootstrap, tenancy, features, remote config, app version/maintenance, records, sync, notifications, support, billing, reports, and diagnostics. |
| Scripts directory | `scripts` exists with root helper guidance; no custom helper scripts are needed yet. |
| Tests | `apps/mobile-client` passes `php artisan test --compact` with 477 tests / 3752 assertions covering routes, Livewire, NativePHP wrappers, local storage, API auth, bootstrap, tenant workspace behavior, mobile invitation check/accept/decline actions, cached app-version/maintenance screens, optional-update dashboard banners, and cached policy guards for mobile record, attachment, profile share, record-detail share, media-gallery share, support-center browser handoff, voice-note, check-in, media-capture, file-manager, scanner, notification inbox, manual sync, conflict resolution, offline queue writes, and developer native debug actions. `apps/api-admin` passes `php artisan test --compact` with 114 tests / 987 assertions covering admin routing, tenant lifecycle/membership management controls, authenticated tenant invitation list/accept/decline flows, feature flag controls, tenant and user feature override controls, remote config controls, tenant remote config controls, app version controls, current resource policies, scoped and version-ranged app version policy, remote config resolution, API envelopes, contract catalogue, mobile auth, bootstrap, tenant context switching, role-derived mobile permission payloads, mobile billing subscription state, mobile notification policy state, mobile sync policy state, tenant-scoped records API behavior, and feature flag resolution with maintenance, plan, cohort, device, emergency, and app-version gates. |
| Native tooling | `apps/mobile-client` exposes NativePHP commands and `native:plugin:validate` passes with two non-fatal third-party manifest warnings. Xcode/Android simulator verification remains external-tooling dependent. |

## Phase 1 - Repository Foundation

| Feature | Status | Notes |
| --- | --- | --- |
| Root README and product docs | documented | Root docs define the product, boundaries, stack, and audit baseline. |
| Admin Control Center logic | documented | Control principles exist for tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance, force update, sync, notifications, reports, billing, and support. |
| Feature Flag Logic | documented | Feature flag principles exist for important mobile features, global/tenant/user priority, disabled states, admin impact, rollout safety, and plan limits. |
| Remote Configuration Logic | documented | Remote config principles exist for configurable behavior, mobile receive/cache rules, offline behavior, tenant overrides, safe admin changes, fallback, and invalid config handling. |
| Mobile Version Control Logic | documented | Version-control principles exist for minimum supported versions, optional updates, forced updates, maintenance mode, store links, update messages, support context, and old-version protection. |
| Root monorepo structure | partial | `apps/api-admin` and `apps/mobile-client` are implemented as Laravel apps; the root app remains only as a transition mirror until a later cleanup decision. |
| `apps/api-admin` | tested | Laravel app, Livewire dashboard route, versioned API route, shared responder, tests, and frontend build exist. |
| `apps/mobile-client` | tested | Laravel app, Livewire mobile routes, NativePHP config, local SQLite infrastructure, copied mobile UI, tests, and frontend build exist. |
| `docs` | documented | Core docs exist; implementation docs need to track real code as it lands. |
| `contracts/api` | tested | Directory, response-envelope README, `v1-foundation.md`, all required v1 contract files, and contract catalogue coverage exist. |
| Root scripts | partial | Directory and guidance exist; no custom wrappers are needed before app split. |
| Environment examples | partial | Root, `apps/api-admin`, and `apps/mobile-client` `.env.example` files exist; the mobile client now documents API base URL and timeout keys, while later bootstrap/config keys remain pending. |
| Documentation structure | partial | Product docs, implementation status, remaining tasks, changelog, app boundary docs, and contract guidance exist. |
| Git state discipline | partial | Implementation slices are being committed separately; local branch remains ahead of origin. |

## Phase 2 - API/Admin Foundation

| Feature | Status | Notes |
| --- | --- | --- |
| Complete Laravel app under `apps/api-admin` | tested | Laravel 13 app scaffolded with Composer/NPM lockfiles; `php artisan test --compact` and `npm run build` pass for this app. |
| Livewire admin panel | tested | `App\Livewire\Admin\Dashboard` renders at `/admin/dashboard`. Domain controls are represented as foundation cards until later phases implement real modules. |
| Blade and Tailwind admin UI | tested | Admin shell uses Blade, Livewire, and Tailwind v4; production build passes. |
| Admin, auth, and dashboard layouts | implemented | Layout files exist under `resources/views/layouts`; the admin layout is exercised by the dashboard test. |
| Reusable admin UI components | implemented | Admin section heading and status badge components exist and are used by the dashboard. |
| Grouped admin routes | tested | Web routes redirect `/` to the named `admin.dashboard` route inside an `admin` prefix/name group. |
| Versioned API route structure | tested | `apps/api-admin/routes/api.php` exposes `GET /api/v1/mobile/status` under the `api.v1.mobile.*` name prefix. |
| Standard API success response | tested | `App\Support\Api\MobileApiResponse::success()` returns the documented `success/data/meta` envelope. |
| Standard API error response | tested | `App\Support\Api\MobileApiResponse::error()` returns the documented `success/error/meta` envelope. |
| API/admin tests | tested | Focused Pest tests cover the admin dashboard route, root redirect, success envelope, and error envelope. |

## Phase 3 - Mobile Client Foundation

| Feature | Status | Notes |
| --- | --- | --- |
| Complete Laravel app under `apps/mobile-client` | tested | Root mobile app was copied into `apps/mobile-client` with Composer/NPM lockfiles; `composer validate`, 52 route verification, full Pest suite, Pint, Vite build, and NativePHP plugin validation pass. |
| NativePHP Mobile configuration | tested | NativePHP config, launcher, lockfile, service provider, wrappers, tests, and plugin validation exist in `apps/mobile-client`; simulator/emulator builds still depend on external tooling. |
| Livewire + Blade mobile UI | tested | Class-based Livewire mobile components and Blade views exist under `apps/mobile-client` and are covered by route/component tests. |
| Tailwind mobile styling | tested | Tailwind v4/SCSS entrypoint and mobile design tokens build through Vite in `apps/mobile-client`. |
| Mobile-first layout and safe-area shell | tested | Shared layout, mobile components, safe-area shell, and bottom navigation are covered by feature tests. |
| Welcome screen | tested | `Mobile\Welcome` route and view exist in `apps/mobile-client`. |
| Auth screens | tested | Login, register, profile update, profile logout, sessions logout, and sessions logout-all now consume the mobile auth API service; password reset and email verification remain local validation placeholders until API endpoints are documented and implemented. |
| Dashboard | tested | `Mobile\Dashboard` exists and renders in `apps/mobile-client`; Admin/API bootstrap integration is missing. |
| Bottom navigation | tested | `<x-mobile.bottom-navigation>` exists and is covered by shell tests; feature-gated navigation is not API-controlled yet. |
| Settings | tested | Settings index and sections exist; workspace settings now reads cached bootstrap tenant context and switches tenants through API. Remote config policy remains pending. |
| Profile | tested | Profile and edit profile screens exist; edit profile syncs the account name through the API profile endpoint when a valid access token exists. |
| Notifications page | tested | Local notification inbox exists; push/API notification authority is missing. |
| Debug/diagnostics page | tested | Debug screen exists; full privacy-safe diagnostics export/share is incomplete. |
| Reusable mobile UI components | tested | Components exist and are covered by mobile UI component tests. |

## Phase 4 - API Contracts

| Contract Area | Status | Notes |
| --- | --- | --- |
| API contract documentation directory | tested | `contracts/api/README.md` defines envelope standards and links every v1 contract file; API/admin tests verify catalogued documents exist. |
| Versioned response envelope | tested | Shared responder and `GET /api/v1/mobile/status` test cover `success`, `data`, `error`, `meta`, and `next_action` shape. |
| Contract catalogue endpoint | tested | `GET /api/v1/mobile/contracts` returns the public v1 contract catalogue through the standard success envelope. |
| Auth contract | tested | `v1-auth.md` defines implemented auth/session/profile routes and the contract catalogue marks auth implemented. |
| Bootstrap contract | tested | `v1-bootstrap.md` defines required payload and cache behavior; API/admin serves the foundation endpoint and mobile client caches it locally. |
| Tenancy contract | tested | `v1-tenancy.md` defines tenant list/switch and authenticated invitation list/accept/decline behavior; foundation tenant context, switch endpoints, and mobile invitation endpoints are implemented and tested while pre-login invitation links, resend/revoke, and richer admin invitation management remain pending. |
| Features contract | tested | `v1-features.md` defines resolved feature states and gates; `GET /features` is implemented for global default, tenant override, user override, plan-gated, device-gated, permission-gated, and app-version-gated mobile-safe outcomes. |
| Remote config contract | tested | `v1-remote-config.md` defines receive/cache/offline/fallback rules; `GET /config` returns resolved foundation/global/tenant config with freshness and version metadata. |
| App version/maintenance contract | tested | `v1-app-version-maintenance.md` defines version, force update, and maintenance states; `GET /app-version` returns resolved policy outcomes, public cohort checks and version-range policy targeting are supported, and bootstrap consumes tenant-aware resolver output. |
| Notifications contract | partial | `v1-notifications.md` defines inbox, push token, and read-state routes; bootstrap notification preferences are implemented from tenant policy settings. Inbox and push endpoints remain pending. |
| Records/content contract | tested | `v1-records.md` defines implemented tenant-scoped list, create, detail, update, archive, restore, category/tag resolution, note append, attachment metadata, activity timeline, and remaining sync/subresource gaps. |
| Sync contract | partial | `v1-sync.md` defines sync bootstrap, push, pull, acknowledgement, and conflict behavior; bootstrap sync policy is implemented, while dedicated sync endpoints remain pending. |
| Support contract | documented | `v1-support.md` defines support ticket/message behavior; API is not implemented. |
| Billing contract | partial | `v1-billing.md` defines subscription/plan presentation behavior; `GET /billing/subscription` returns current tenant subscription state and plan hints. Provider billing, invoices, usage writes, admin billing UI, and audit workflows remain pending. |
| Reports contract | documented | `v1-reports.md` defines permission-safe report summaries; API is not implemented. |
| Diagnostics contract | documented | `v1-diagnostics.md` defines privacy-safe diagnostics upload behavior; API is not implemented. |

## Phase 5 - Authentication And Sessions

| Feature | Admin/API Status | Mobile Status | Notes |
| --- | --- | --- | --- |
| Admin authentication | tested | n/a | Admin login/logout exists for `is_platform_admin` users and dashboard access is protected. |
| API authentication | tested | tested | API/admin mobile auth endpoints exist; mobile client has a tested API client/auth service and Livewire login/register screens now authenticate through it. |
| Access tokens | tested | tested | API/admin stores only hashed access tokens and protects routes through `mobile.auth`; mobile stores received access tokens through `MobileTokenStore` using NativePHP secure storage by default and session fallback for tests/development. |
| Refresh tokens | tested | tested | API/admin refresh endpoint rotates refresh/access tokens; mobile service sends the stored refresh token and replaces the local token set. |
| Logout | tested | tested | API/admin logout revokes the current device session; mobile profile/sessions screens call the endpoint and clear local session/token state. |
| Logout all devices | tested | tested | API/admin logout-all revokes active mobile sessions; mobile sessions screen calls the endpoint and clears local session/token state. |
| Current user endpoint | tested | tested | `GET /api/v1/mobile/auth/user` exists and the mobile service calls it with a bearer token. |
| Profile update endpoint | tested | tested | `PATCH /api/v1/mobile/auth/profile` exists and the edit-profile Livewire screen syncs the profile name through it when a valid access token exists. Avatar storage remains local until a media/upload API slice. |
| Device/session logic | tested | tested | API/admin device sessions are persisted, last-seen tracked, and revocable; mobile auth service sends a stable device context from the client session. Tenant/device trust policy remains pending. |
| Security audit events | tested | partial | API/admin writes auth audit events; broader admin/control-plane audit remains pending. |

## Phase 6 - Tenancy

| Feature | Status | Notes |
| --- | --- | --- |
| Tenant model and lifecycle | tested | `Tenant` schema/model/factory exist with lifecycle status values and switchability checks, and platform admins can create/update tenant lifecycle status through audited admin controls. Full lifecycle deletion/restore policy remains pending. |
| Tenant users/memberships | tested | `TenantUser` schema/model/factory exist, platform admins can assign/update audited tenant memberships, declined memberships fail closed, and mobile API tests verify membership listing, current tenant selection, switch persistence, invitation response, and denial. |
| Tenant roles and invitations | partial | Tenant role enum exists for membership payloads, platform admins can assign tenant roles through the tenant management screen, and authenticated mobile users can list/accept/decline pending invitations. Pre-login signed invitation links, resend/revoke, expiry, email delivery, and richer admin invitation workflows are not implemented. |
| Tenant settings | partial | `tenants.settings` exists and platform admins can edit the JSON settings object through audited admin controls; dedicated settings policy/resolver screens remain pending. |
| Tenant-scoped API middleware | partial | Tenant list/switch endpoints enforce membership server-side; generic tenant-scoped resource middleware remains pending. |
| Admin tenant management screens | tested | `/admin/tenants` lists tenants with membership counts, creates/updates tenant lifecycle status, subscription state, slug, and settings, assigns/updates tenant memberships, shows recent membership changes, and records audit events. |
| Mobile tenant store/display/switcher | tested | `MobileTenantContextStore`, `MobileTenantApiService`, and `mobile.settings.workspace` display cached bootstrap tenant context, refresh bootstrap, switch current tenant through `POST /tenants/current`, and check/accept/decline pending invitations through authenticated API endpoints with bootstrap refresh after response. |
| Tenant-separated local cache | partial | Local models have mobile-local storage, but tenant partitioning is not fully proven. |

## Phase 7 - Roles, Permissions, And Policies

| Feature | Status | Notes |
| --- | --- | --- |
| Role definitions | partial | Product role model exists and `TenantUserRole` now drives the mobile permission registry plus audited admin tenant membership assignment; full admin role-management screens remain pending. |
| Permission definitions | partial | `MobilePermission` defines the foundation mobile ability registry for bootstrap; database-backed permission grants and admin management remain pending. |
| Policies for API/admin | tested | Policies are registered for current mobile control-plane resources and Livewire edit/save/restore actions use policy authorization. Broader resource policies remain pending as new modules are implemented. |
| Protected admin routes | tested | `/admin/dashboard` and current control pages are protected by session auth, platform-admin middleware, and resource policies for current mobile control-plane actions. |
| Protected API routes | partial | Auth, bootstrap, tenant list/switch, and profile routes are mobile-token protected; resource permission middleware/policies remain pending. |
| Mobile permission payload | tested | Bootstrap returns nested role-derived ability state for the current active tenant and fails closed for invited/suspended memberships. |
| Mobile permission-aware UI | tested | Permission settings/center exists for NativePHP device permissions and now uses `MobileAccessPolicy` before offering camera, microphone, location, notification, file, or biometric prompts. `MobileAccessPolicy` also consumes cached Admin/API bootstrap permissions to hide blocked shortcuts and route-block records, notifications, sync conflicts, media/files, scanner, and location screens; record create/update/archive/delete, bulk mutations, attachment management, attachment sharing, profile sharing, record-detail sharing, media-gallery sharing, support-center browser handoff, voice-note recording callbacks, local voice-note save/delete, voice-note upload queue placeholders, NativePHP location callbacks, local check-in creation, media capture callbacks, file manager read/write/copy/move/import/export/delete/share actions, scanner capture callbacks, saved scan-history deletes/clears, notification read/open/read-all actions, manual sync, conflict resolution actions, offline-first queue writes, and developer debug native wrapper calls/callbacks now deny direct Livewire/service calls before local SQLite writes, native share/location/media/scanner/browser/device/dialog/storage handoff, file deletes, imports/exports, scan-history deletes, notification timestamp writes, sync timestamp writes, conflict queue status changes, or offline queue writes. Remaining diagnostics/report native share flows and legal/billing browser flows still need feature-specific policy gates. |

## Phase 8 - Feature Flags

| Feature | Status | Notes |
| --- | --- | --- |
| Global feature flags | tested | `MobileFeatureFlag` schema/model/factory and resolver coverage exist for global defaults, required plan keys, allowed cohort keys, device constraints, and minimum app versions. |
| Tenant feature overrides | tested | `TenantFeatureOverride` schema/model/factory, resolver coverage, and `/admin/mobile/feature-overrides` audited admin controls exist for tenant overrides. |
| User feature overrides | tested | `UserFeatureOverride` schema/model/factory, resolver coverage, and `/admin/mobile/user-feature-overrides` audited admin controls exist for membership-safe user overrides. |
| Resolution order user override -> tenant override -> global default, then emergency/maintenance/plan/cohort/device/permission/version gates | tested | Initial resolver follows the explicit requested override order and applies emergency, maintenance, plan, cohort, device, permission, and minimum-app-version gates before returning mobile-safe state; richer billing authority remains pending. |
| Admin feature flag UI | tested | `/admin/mobile/features` manages audited global mobile feature defaults, required plans, allowed cohorts, and device constraints; `/admin/mobile/feature-overrides` manages audited tenant overrides, and `/admin/mobile/user-feature-overrides` manages audited user overrides with search, validation, create/update actions, impact previews, dashboard/nav entry points, and Livewire coverage. Maintenance gates are driven by app-version policy controls. |
| Mobile feature store/cache | partial | `MobileBootstrapService` caches the API bootstrap envelope in local settings, and `MobileAccessPolicy` reads cached `features`, `permissions`, `subscription`, `maintenance`, `notification_preferences`, and `sync` outcomes for mobile presentation. A dedicated per-tenant feature cache and freshness UI remain pending. |
| Feature-gated mobile navigation/actions | tested | Primary navigation, dashboard quick actions, create actions, search results, and direct module routes now use cached Admin/API bootstrap policy for records, notifications, sync conflicts, NativePHP media/files/scanner/location screens, and disabled-feature recovery messaging. |

## Phase 9 - Remote Config

| Feature | Status | Notes |
| --- | --- | --- |
| Global remote config | tested | `MobileRemoteConfig` schema/model/factory and resolver coverage exist for mobile-category, non-sensitive global config sections. |
| Tenant remote config | tested | `TenantRemoteConfigOverride` schema/model/factory, resolver coverage, and `/admin/mobile/tenant-config` audited admin controls exist for tenant overrides that merge above global config. |
| Admin remote config UI | tested | `/admin/mobile/config` manages audited global mobile config defaults and `/admin/mobile/tenant-config` manages tenant overrides with JSON-object validation, impact preview, create/update actions, dashboard/nav entry points, and restore from audit snapshots. |
| Config validation and audit | tested | Resolver excludes sensitive global config and the admin UI validates JSON objects, requires mobile-effect confirmation, records before/after audit metadata, and restores prior snapshots. Publish workflows remain pending. |
| Mobile config store/cache | not started | Required after bootstrap exists. |
| Offline defaults | tested | API/admin returns defaults-used, freshness, compatibility, and fallback metadata; mobile-local stale-cache behavior remains pending. |
| Config-driven sync/upload/legal/support behavior | tested | Remote config payload resolves `sync`, `uploads`, `legal`, `support`, `dashboard`, and `app_lock` sections for mobile consumption. |

## Phase 10 - Mobile Bootstrap

| Payload Item | Status | Notes |
| --- | --- | --- |
| Authenticated user | tested | Bootstrap returns the authenticated API user from the mobile token. |
| Current tenant | tested | Bootstrap returns the resolved current tenant from active switchable tenant memberships when one exists. |
| Available tenants | tested | Bootstrap returns available tenant memberships with public tenant IDs, role summaries, switchable state, current state, and disabled reasons. |
| Permissions | tested | Bootstrap returns a role-derived permission payload with nested ability booleans, granted ability list, available role summaries, and `no_active_tenant` fail-closed state when no active tenant is available. |
| Feature flags | tested | Bootstrap returns resolved feature states from global defaults, tenant overrides, user overrides, foundation fallbacks, emergency gates, maintenance gates, plan gates, cohort gates, device gates, permission gates, and minimum-app-version gates. |
| Remote config | tested | Bootstrap returns resolved remote config from foundation defaults, global config, tenant overrides, freshness metadata, compatibility metadata, and deterministic config versions. |
| App version rules | tested | Bootstrap returns resolved version policy from foundation defaults or active tenant, cohort, platform, and global policies. |
| Maintenance mode | tested | Bootstrap returns maintenance state, message, support URL, and retry timing from active version policy. |
| Subscription status | tested | Bootstrap returns current tenant subscription state, plan, trial, limits, usage, billing portal state, feature impacts, and subscription metadata from Admin/API-owned tenant data. |
| Notification preferences | tested | Bootstrap returns tenant notification preferences, quiet hours, push-registration hints, fail-closed no-tenant behavior, and notification policy version metadata. |
| Sync settings | partial | Bootstrap returns sync disabled with local offline queue allowed until server sync endpoints exist. |
| Unread notification count | partial | Bootstrap intentionally returns `0` until server notification inbox storage exists. |
| Mobile bootstrap service/cache | tested | `MobileBootstrapService` calls `GET /bootstrap` with the stored access token and caches the envelope in mobile-local settings; login/register refresh it after authentication, and the workspace settings screen consumes/refetches that cached context after tenant switches. |

## Phase 11 - App Version, Force Update, And Maintenance Mode

| Feature | Status | Notes |
| --- | --- | --- |
| Admin app version control | tested | `/admin/mobile/app-versions` manages global/platform, tenant, cohort, and version-range policies with confirmation, impact preview, audited create/update, and restore from prior audit snapshots. |
| Minimum supported version | tested | `MobileAppVersionPolicyResolver` returns `force_update` when the reported version is below the active minimum. |
| Optional update rules | tested | Resolver returns `optional_update` when the reported version is below the active recommended version but still supported. |
| Force update rules | tested | Resolver returns blocking update actions and store links for unsupported versions. |
| Maintenance mode rules | tested | Resolver returns maintenance state and bootstrap maps it to the top-level maintenance payload. |
| Mobile force update screen | tested | `mobile.update-required` renders the cached bootstrap policy, store/support/logout actions, and refreshes bootstrap before returning users to normal navigation. |
| Optional update banner | tested | Dashboard surfaces cached optional-update policy with current/latest version context and routes to the update screen without blocking normal navigation. |
| Maintenance screen | tested | `mobile.maintenance` renders cached maintenance copy, retry timing, support/logout actions, and refreshes bootstrap before returning to dashboard or update-required state. |

## Phase 12 - Records/Content Module

| Feature | Admin/API Status | Mobile Status | Notes |
| --- | --- | --- | --- |
| Tenant-scoped records | tested | partial | Server `tenant_records` persistence, tenant-scoped queries, and cross-tenant API denial are implemented and tested. Mobile-local records still need full API sync integration. |
| Records list/detail | tested | partial | API list/detail endpoints return shaped, eager-loaded, permission-aware records; mobile screens exist but still rely mostly on local cache. |
| Create/update/archive/restore/delete | partial | partial | API create/update/archive/restore are implemented and tested. Hard-delete policy and sync replay acceptance remain pending. |
| Categories | partial | partial | API create/update can resolve tenant-scoped categories from payloads. Standalone category admin/API screens remain pending. |
| Tags | partial | partial | API create/update resolves tenant-scoped tags and attaches them to records. Standalone tag admin/API screens remain pending. |
| Notes | partial | partial | API create/update can append record notes and includes detail note payloads. Standalone note editing/deleting remains pending. |
| Attachment metadata | partial | partial | API create can persist attachment metadata without trusting local file state. Upload/storage workflows remain pending. |
| Activity timeline | tested | partial | API create/update/archive/restore write activity timeline entries and security audit events. Sync conflict/replay activity remains pending. |
| Admin records management | not started | n/a | Admin shell exists; records management screens are not implemented yet. |
| Records API endpoints | tested | partial | `GET/POST/PATCH/DELETE /api/v1/mobile/records`, detail, and restore endpoints are implemented and covered by feature tests. Mobile sync consumption remains pending. |

## Phase 13 - Search, Filters, Saved Views

| Feature | Status | Notes |
| --- | --- | --- |
| API/admin search | not started | No admin/API implementation. |
| Mobile local search | partial | Mobile search screen exists. |
| Filters and sorting | partial | Some local record surfaces exist; full documented behavior not verified. |
| Recent searches | not started | Not found in current implementation evidence. |
| Saved views | not started | Not found in current implementation evidence. |
| Scan-to-search | partial | Scanner and scan history screens exist; full search integration incomplete. |

## Phase 14 - Offline-First And Sync

| Feature | Admin/API Status | Mobile Status | Notes |
| --- | --- | --- | --- |
| Sync bootstrap | tested | partial | Bootstrap returns tenant sync policy from settings, remote config, permissions, subscription state, and maintenance policy; dedicated `/sync/bootstrap` endpoint remains pending. |
| Sync push/pull/ack | not started | partial | Mobile queue and worker exist; server endpoints missing. |
| Sync conflict tracking | not started | partial | Local conflict fields and screens exist, and cached sync policy now hides conflict resolution controls and blocks direct resolution calls before queue status changes. |
| Admin sync monitoring | not started | n/a | Admin shell exists; sync monitoring screens are not implemented yet. |
| Local SQLite cache | n/a | partial | Dedicated `mobile_local` connection and migrations exist. |
| Offline action queue | n/a | tested | Queue/repository/worker exist, and manual sync/conflict-resolution surfaces plus `OfflineFirstActionQueue` now gate sync policy before local sync timestamps, conflict queue statuses, or new replay intents change. Server push/pull/ack endpoints remain missing. |
| Retry logic | not started | partial | Local worker has retry/backoff behavior. |
| Offline banner/manual sync | n/a | tested | Mobile surfaces exist, and manual sync is hidden/blocked when cached sync policy denies `offline_sync` or `sync.run`. |

## Phase 15 - NativePHP Wrappers

| Wrapper | Status | Notes |
| --- | --- | --- |
| Device | partial | Service and tests exist; developer debug flashlight/vibration/haptic calls are gated by cached `native_device` policy. Broader feature-specific API policy remains pending. |
| Network | partial | Service and UI surfaces exist; sync policy integration missing. |
| Camera | partial | Service and media screen exist, and media capture handoff/callback actions are gated by cached `native_camera` policy. API upload policy remains missing. |
| File | partial | Service and file manager exist, and read/write/copy/move/import/export/delete actions are gated by cached `native_files` policy. API import/export policy remains missing. |
| Share | partial | Service exists, and profile, record-detail, media-gallery, file-manager, and developer debug share actions are gated by cached `native_share` policy. Diagnostics/report sharing policy remains incomplete. |
| Browser | partial | Service exists, and support-center plus developer debug browser actions are gated by cached `native_browser` policy. Feature-specific legal/billing policy remains incomplete. |
| Microphone | partial | Audio/voice service exists, and the Livewire voice-note surface is now gated by cached `native_microphone` and `offline_sync` policy before recording callbacks, local writes, deletes, or upload queue placeholders. API upload/replay policy is still missing. |
| Location | partial | Location service/check-in screens exist, and NativePHP permission/current-position calls plus location callbacks are gated by cached `native_location` policy. API acceptance remains missing. |
| Scanner | partial | Scanner service and screens exist, and scanner capture/history mutation actions are gated by cached `native_scanner` policy. API/search/create integration remains incomplete. |
| Push notifications | not started | Local notifications exist; push token registration is missing. |
| Biometrics | partial | Biometric unlock service/tests exist. |
| Secure storage | partial | Native secure storage and session fallback exist, and developer debug storage calls are gated by cached `native_secure_storage` policy. |
| System/settings | partial | System service exists. |
| Dialogs/toasts | partial | Dialog and toast services/surfaces exist, and developer debug dialog wrapper calls are gated by cached `native_dialogs` policy. |

## Phase 16 - Mobile Permissions Center

| Feature | Status | Notes |
| --- | --- | --- |
| Permissions center | partial | Settings permissions screen and service exist. |
| Camera/microphone/location/notification/file status | partial | Native permission handling exists for installed plugins; full API feature gating missing. |
| Biometrics availability | partial | Biometric services exist. |
| Explain before asking | partial | UI exists but must be aligned with remote config purpose copy. |
| Open settings recovery | partial | System/settings wrapper exists. |
| Respect feature flags/config | not started | Requires bootstrap features/config. |

## Phase 17 - Camera, Media, Files, And Sharing

| Feature | Status | Notes |
| --- | --- | --- |
| Camera capture | tested | Mobile service/screen exists; direct capture action and native callback paths are hidden and blocked when cached `native_camera` policy is disabled. |
| Gallery selection | tested | Media capture picker actions and callbacks are hidden and blocked by cached camera policy; durable API upload remains pending. |
| Media preview | partial | Mobile media surfaces exist. |
| Attach media to records/support | partial | Record attachment surfaces exist; support/API missing. |
| Offline media queue | partial | Local media models exist; API upload/replay missing. |
| File import/export | partial | File manager exists and local import/export actions are gated by cached `native_files` policy; API policy remains missing. |
| Native share | partial | Share service exists and profile, record-detail, media-gallery, and file-manager share actions are gated by cached `native_share` policy; diagnostics/report flows remain incomplete. |

## Phase 18 - Scanner

| Feature | Status | Notes |
| --- | --- | --- |
| QR/barcode scanner | tested | Scanner service and demo screen exist; direct scan actions and callback persistence are hidden and blocked when cached `native_scanner` policy is disabled. |
| Scan result screen/history | tested | Scan history exists; saved scan-history delete/clear actions are hidden and blocked when cached scanner policy is disabled. |
| Scan-to-search | partial | Needs complete record/search integration. |
| Scan-to-create | not started | Not found in current implementation evidence. |
| Duplicate/invalid scan handling | partial | Needs verification. |
| Offline scan sync | partial | Local scan history exists; API sync missing. |

## Phase 19 - Geolocation

| Feature | Status | Notes |
| --- | --- | --- |
| Location permission UX | tested | Permission/service surfaces exist and direct permission status/request actions are hidden and blocked when cached `native_location` policy is disabled. |
| Geolocation check-ins | tested | Check-in screens and local model exist; native current-location calls, location callbacks, local create forms, and history create shortcuts are gated by cached `native_location` and `offline_sync` policy. |
| Accuracy display | tested | Current-location event tests verify accuracy display formatting. |
| Attach location to records/support | not started | API/support integration missing. |
| Offline location queue | partial | Local check-ins exist and direct local writes are blocked when sync policy is disabled; server sync/replay is still missing. |
| Privacy principles | documented | Needs enforcement in API/support diagnostics. |

## Phase 20 - Voice Notes

| Feature | Status | Notes |
| --- | --- | --- |
| Voice note recording | tested | Audio service and voice-note screen exist with browser fallback coverage, native-start service coverage, and cached `native_microphone` policy denial before direct Livewire recording calls or native callbacks. |
| Pause/resume | partial | Pause/resume controls are guarded by cached microphone policy; full device capability verification still depends on NativePHP runtime testing. |
| Save locally | tested | Local voice-note model/repository and screen coverage exist; cached microphone policy now blocks direct save/delete calls before local SQLite writes or file deletes. |
| Attach to records/support | partial | Record/support API integration missing. |
| Upload queue | partial | Local upload placeholder queue exists and is blocked by cached `native_microphone` plus `offline_sync` policy; API upload/replay is still missing. |
| Playback/list/detail | tested | Mobile voice notes screen lists saved notes, shows detail, and prepares playback without requiring mutation permission. |

## Phase 21 - Push Notifications And Notification Inbox

| Feature | Admin/API Status | Mobile Status | Notes |
| --- | --- | --- | --- |
| Notifications | not started | partial | Local notification inbox exists; server notifications missing. |
| Notification preferences | not started | partial | Mobile settings exist; API authority missing. |
| Push token registration/revocation | not started | not started | Required for push. |
| Admin notification center | not started | n/a | Admin shell exists; notification center screens are not implemented yet. |
| Campaign placeholders | not started | n/a | Not implemented. |
| Delivery/open tracking | not started | partial | Local history exists; server truth missing. |
| Mark read/all read/delete | partial | tested | Local read/open/read-all behavior exists, and cached notification policy now hides inbox rows and blocks direct read/open/read-all calls before local timestamp writes. API read/delete endpoints remain pending. |
| Deep links | not started | partial | Native config has scheme; notification deep link handling incomplete. |

## Phase 22 - Security And Privacy

| Feature | Status | Notes |
| --- | --- | --- |
| Server audit logic | not started | Required for admin/API authority. |
| Sensitive action confirmation | partial | Mobile account deletion/PIN/biometric flows exist; admin confirmations missing. |
| Mobile app lock PIN/biometric | partial | Implemented locally with tests in existing suite; fresh run pending. |
| Privacy settings | partial | Legal/privacy screens exist; API privacy settings missing. |
| Secure token handling | partial | Native secure storage fallback exists; server token authority missing. |
| Data export principles/features | documented | Implementation missing. |
| Local data reset | partial | Storage settings exist; needs verification. |
| Diagnostics privacy protection | partial | Debug surfaces exist; export/share redaction incomplete. |
| Support/admin access limits | not started | No support/admin implementation yet. |

## Phase 23 - Billing And Subscriptions

| Feature | Status | Notes |
| --- | --- | --- |
| Plans/subscriptions | partial | `MobileSubscriptionResolver` reads tenant `subscription_state` and mobile-safe billing settings for bootstrap, `/billing/subscription`, and feature plan gates. Provider plans/subscriptions remain pending. |
| Invoice placeholders | not started | Documented only. |
| Usage events | not started | Documented only. |
| Admin billing screens | not started | Admin shell exists; billing screens are not implemented yet. |
| Plan-based feature availability | tested | Feature flags can require plan keys and the resolver uses the current tenant subscription plan key before returning mobile-safe feature outcomes. Richer billing authority remains pending. |
| Mobile plan/status display | partial | API/bootstrap now expose mobile-safe plan/status payloads; mobile UI integration remains pending. |
| Trial/expired/suspended behavior | documented | Implementation missing. |

## Phase 24 - Support System

| Feature | Status | Notes |
| --- | --- | --- |
| Support tickets/messages | not started | Documented only. |
| Support attachments | not started | Documented only. |
| Admin support panel | not started | Admin shell exists; support screens are not implemented yet. |
| Assignment/status/priority | not started | Documented only. |
| Support audit | not started | Required. |
| Mobile support ticket list/create/detail | not started | Settings support page exists, but full support system is missing. |
| Offline support drafts | not started | Not implemented. |

## Phase 25 - Reports

| Feature | Status | Notes |
| --- | --- | --- |
| Reports dashboard | not started | Documented only. |
| Tenant/user/records reports | not started | Documented only. |
| Sync/notification/support/billing reports | not started | Documented only. |
| Exports | not started | Documented only. |
| Mobile reports screen | not started | Feature-gated screen not implemented. |

## Phase 26 - Optional Modules From Documentation

| Module | Status | Notes |
| --- | --- | --- |
| Field service | not started | Mentioned by prompt as possible only; not documented as a required module in project docs. |
| Logistics | not started | Mentioned by prompt as possible only; not documented as a required module in project docs. |
| Ecommerce | not started | Mentioned by prompt as possible only; not documented as a required module in project docs. |
| Booking | not started | Mentioned by prompt as possible only; not documented as a required module in project docs. |
| Education | not started | Mentioned by prompt as possible only; not documented as a required module in project docs. |
| Events | not started | Mentioned by prompt as possible only; not documented as a required module in project docs. |
| Messaging/community | not started | Mentioned by prompt as possible only; not documented as a required module in project docs. |
| AI assistant | not started | Mentioned by prompt as possible only; not documented as a required module in project docs. |

## Phase 27 - Admin Mobile Control Dashboard

| Feature | Status | Notes |
| --- | --- | --- |
| Central mobile control dashboard | partial | The dashboard shell exists and links to the live feature flag, remote config, and app version controls; broader module controls remain pending. |
| Module controls | partial | Foundation controls exist for global/tenant/user feature states, global/tenant remote config, and scoped version-ranged app versions; broader modules remain pending. |
| Feature flags and tenant overrides | tested | Global feature defaults, tenant-specific overrides, user-specific overrides, emergency gates, maintenance gates, plan gates, cohort gates, device gates, permission gates, and minimum-app-version gates are implemented with mobile-safe API outcomes. Richer billing gates remain pending. |
| Remote config | tested | Admin/API has global and tenant config schema, resolver, API endpoint, bootstrap integration, and audited admin controls. Publish workflows remain pending. |
| App versions, force update, maintenance | tested | Admin/API has scoped version-range policy schema, resolver, API endpoint, bootstrap integration, tenant/cohort precedence, and audited admin controls. Mobile now renders cached force-update, optional-update, and maintenance states from bootstrap policy. |
| Sync/offline/upload limits | not started | Needs sync/config implementation. |
| Push/support/legal links | not started | Needs notification/support/config implementation. |
| Mobile effect preview | partial | Implemented for tenant/user feature overrides, global/tenant remote config, and app-version policy controls; still required for other dangerous control-plane settings. |

## Phase 28 - Mobile Diagnostics

| Feature | Status | Notes |
| --- | --- | --- |
| Diagnostics screen | partial | Debug page exists. |
| App version/API URL/current tenant/user | partial | Some debug context exists; bootstrap tenant/API integration missing. |
| Feature/config snapshots | not started | Requires bootstrap stores. |
| Network/sync status | partial | Local network/sync surfaces exist. |
| Failed sync actions | partial | Local offline action/conflict surfaces exist. |
| Device info | partial | Device service exists. |
| Export diagnostics as JSON | not started | Required. |
| Native share diagnostics | not started | Share service exists; diagnostics export integration missing. |
| Private data protection | partial | Principles documented; redaction implementation incomplete. |

## Phase 29 - Documentation Completion

| Document Area | Status | Notes |
| --- | --- | --- |
| Root README | documented | Needs update as implementation changes. |
| Architecture docs | documented | Need updates after monorepo/control-plane implementation. |
| Local development docs | partial | Commands exist in README/docs; per-app docs missing. |
| API contracts | tested | `contracts/api` exists with all required v1 contract files and catalogue coverage. |
| Admin panel docs | documented | Principles only; implementation docs missing. |
| Mobile client docs | documented | Principles plus audit exist. |
| NativePHP docs | documented | Runbook exists. |
| Auth/tenancy/features/config/sync docs | documented | Product-level docs exist; implementation-specific docs missing. |
| Notifications/billing/support/reports/security docs | documented | Product-level docs exist; implementation-specific docs missing. |
| Testing/deployment/build docs | partial | Command references exist; full per-app docs missing. |
| Changelog | partial | `CHANGELOG.md` exists and needs updates after each meaningful slice. |
| Remaining tasks | partial | `docs/remaining-tasks.md` exists and must shrink to blockers/future enhancements as implementation lands. |

## Phase 30 - Testing And Quality Loop

| Check | Status | Notes |
| --- | --- | --- |
| API/admin formatting | tested | `vendor/bin/pint --dirty --format agent` passes in `apps/api-admin`. |
| API/admin tests | tested | `php artisan test --compact` passes in `apps/api-admin` with 114 tests / 987 assertions. |
| API/admin frontend build | tested | `npm run build` passes in `apps/api-admin`. |
| API routes verification | tested | `php artisan route:list --except-vendor` shows 30 app routes including app-version, auth, bootstrap, config, contracts, features, status, tenant context, tenant invitation, and records routes. |
| Admin navigation verification | tested | Admin dashboard smoke coverage exists; browser-level verification remains future. |
| Mobile formatting | tested | `vendor/bin/pint --dirty --format agent` passes in `apps/mobile-client`. |
| Mobile tests | tested | `php artisan test --compact` passes in `apps/mobile-client` with 477 tests / 3752 assertions. |
| Mobile frontend build | tested | `npm run build` passes in `apps/mobile-client`. |
| Mobile navigation verification | tested | `php artisan route:list --name=mobile` shows 55 named mobile routes and route tests cover authenticated/guest rendering. Browser/native manual verification remains future. |
| NativePHP fallback verification | tested | `php artisan native:plugin:validate --no-interaction` exits successfully with two non-fatal third-party manifest warnings; simulator/emulator release verification remains external-tooling dependent. |
| Offline/sync verification | partial | Local worker tests exist; server sync missing. |
| Root monorepo scripts | not started | Scripts missing. |
| Final git status | not started | Must be clean after commits. |

## Highest-Priority Implementation Order

1. Continue migrating lower-level mobile actions and NativePHP service calls
   behind `MobileAccessPolicy`, especially remaining offline queue writes and
   lower-level service calls.

## Current Blocking Risks

| Risk | Status | Mitigation |
| --- | --- | --- |
| Root transition app still exists | partial | Decide when to remove or rewire it after `apps/mobile-client` is fully authoritative. |
| Admin/API domain modules do not exist yet | partial | Build auth, tenancy, policy, feature, config, version, and audit foundations before business modules. |
| API contracts are documented but most endpoints are planned | partial | Implement endpoints in phase order and keep each contract updated before code changes. |
| Mobile features are mostly local-only | partial | Route all server-trusted behavior through API contracts as they land. |
| Native build tooling incomplete | partial | Keep NativePHP service fallbacks tested; treat simulator/emulator release verification as external blocker until tooling is installed. |
| Full completion scope is very large | partial | Continue in small commits by phase; keep this checklist authoritative. |
