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
| API routes | `apps/api-admin/routes/api.php` exposes versioned routes at `GET /api/v1/mobile/status`, `GET /api/v1/mobile/contracts`, auth endpoints, and authenticated `GET /api/v1/mobile/bootstrap`. |
| Mobile routes | 52 `mobile.*` Livewire routes exist in both the root transition app and `apps/mobile-client/routes/web.php`. |
| Active database | API/admin migrations now include users, framework tables, mobile device sessions, hashed mobile access/refresh tokens, and security audit events. Tenant/control-plane domain schema remains pending. |
| Mobile local database | Dedicated `mobile_local` connection, local migrations, local models, repositories, and health command exist in `apps/mobile-client`. |
| Admin/API system | `apps/api-admin` contains a Laravel 13 app, protected Livewire dashboard shell, admin session auth, shared API response envelope, mobile status endpoint, public contract catalogue endpoint, and mobile auth/token/session endpoints. Tenancy and SaaS modules remain pending. |
| Contracts directory | `contracts/api` exists with response-envelope guidance, `v1-foundation.md`, and documented v1 contracts for auth, bootstrap, tenancy, features, remote config, app version/maintenance, records, sync, notifications, support, billing, reports, and diagnostics. |
| Scripts directory | `scripts` exists with root helper guidance; no custom helper scripts are needed yet. |
| Tests | Root mobile suite and `apps/mobile-client` suite each pass with 413 tests / 3342 assertions. `apps/mobile-client` now also has focused API auth and bootstrap service coverage. `apps/api-admin` has focused Pest coverage for admin routing, API envelopes, contract catalogue, mobile auth, and bootstrap. |
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
| Settings | tested | Settings index and sections exist; remote config/tenant policy are not integrated. |
| Profile | tested | Profile and edit profile screens exist; API profile endpoint is missing. |
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
| Tenancy contract | documented | `v1-tenancy.md` defines tenant list/switch behavior; endpoints are not implemented. |
| Features contract | documented | `v1-features.md` defines resolved feature states and gates; endpoint is not implemented. |
| Remote config contract | documented | `v1-remote-config.md` defines receive/cache/offline/fallback rules; endpoint is not implemented. |
| App version/maintenance contract | documented | `v1-app-version-maintenance.md` defines version, force update, and maintenance states; endpoint is not implemented. |
| Notifications contract | documented | `v1-notifications.md` defines inbox, push token, and read-state routes; API is not implemented. |
| Records/content contract | documented | `v1-records.md` defines server record routes and offline/idempotency behavior; endpoints are not implemented. |
| Sync contract | documented | `v1-sync.md` defines sync bootstrap, push, pull, acknowledgement, and conflict behavior; endpoints are not implemented. |
| Support contract | documented | `v1-support.md` defines support ticket/message behavior; API is not implemented. |
| Billing contract | documented | `v1-billing.md` defines subscription/plan presentation behavior; API is not implemented. |
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
| Tenant model and lifecycle | not started | No tenant schema/code exists. |
| Tenant users/memberships | not started | Required for multi-tenant access. |
| Tenant roles and invitations | not started | Role states are documented only. |
| Tenant settings | not started | Required for feature/config/sync policy. |
| Tenant-scoped API middleware | not started | Critical security boundary. |
| Admin tenant management screens | not started | Admin shell exists; tenant management screens are not implemented yet. |
| Mobile tenant store/display/switcher | not started | Required after API bootstrap exists. |
| Tenant-separated local cache | partial | Local models have mobile-local storage, but tenant partitioning is not fully proven. |

## Phase 7 - Roles, Permissions, And Policies

| Feature | Status | Notes |
| --- | --- | --- |
| Role definitions | documented | Product role model exists; implementation missing. |
| Permission definitions | documented | Permission ownership rules exist; implementation missing. |
| Policies for API/admin | not started | Required before protected actions. |
| Protected admin routes | not started | Dashboard route exists; authentication, authorization, and policy protection are not implemented yet. |
| Protected API routes | not started | API routes are missing. |
| Mobile permission payload | not started | Bootstrap endpoint must provide this. |
| Mobile permission-aware UI | partial | Permission settings/center exists for NativePHP device permissions, not SaaS permissions. |

## Phase 8 - Feature Flags

| Feature | Status | Notes |
| --- | --- | --- |
| Global feature flags | not started | Required server authority. |
| Tenant feature overrides | not started | Required for SaaS control. |
| User feature overrides | not started | Required by requested resolution order. |
| Resolution order safety -> plan -> global -> tenant -> role/permission -> user -> version/device/cohort -> offline | documented | Defined in Feature Flag Logic; needs implementation and tests. |
| Admin feature flag UI | not started | Admin shell exists; feature flag management screens are not implemented yet. |
| Mobile feature store/cache | not started | Required after bootstrap exists. |
| Feature-gated mobile navigation/actions | partial | Mobile routes exist; not API/feature controlled yet. |

## Phase 9 - Remote Config

| Feature | Status | Notes |
| --- | --- | --- |
| Global remote config | not started | Required server authority; Remote Configuration Logic defines safe defaults, versioning, validation, and rollback expectations. |
| Tenant remote config | not started | Required for tenant variation inside global, plan, permission, feature, version, and safety boundaries. |
| Admin remote config UI | not started | Admin shell exists; remote config screens are not implemented yet. |
| Config validation and audit | not started | Required for sensitive controls. |
| Mobile config store/cache | not started | Required after bootstrap exists. |
| Offline defaults | documented | Remote Configuration Logic defines last-known context, safe bundled defaults, freshness labeling, and fail-closed behavior; implementation against API payloads is pending. |
| Config-driven sync/upload/legal/support behavior | partial | Some local config files exist; remote config is missing. |

## Phase 10 - Mobile Bootstrap

| Payload Item | Status | Notes |
| --- | --- | --- |
| Authenticated user | tested | Bootstrap returns the authenticated API user from the mobile token. |
| Current tenant | partial | Bootstrap returns `null` until tenant schema and tenant switch logic exist. |
| Available tenants | partial | Bootstrap returns an empty list until tenant memberships exist. |
| Permissions | partial | Bootstrap returns explicit `not_configured` roles/abilities until the permission system exists. |
| Feature flags | partial | Bootstrap returns foundation feature states with disabled/offline-limited reasons for pending server modules and visible native capability hints. |
| Remote config | partial | Bootstrap returns foundation config defaults for dashboard, sync, uploads, support, legal, and app lock behavior. |
| App version rules | partial | Bootstrap echoes reported app version context and returns supported/no-update defaults until version policy exists. |
| Maintenance mode | partial | Bootstrap returns maintenance disabled until maintenance policy exists. |
| Subscription status | partial | Bootstrap returns active foundation subscription status until billing exists. |
| Notification preferences | partial | Bootstrap returns in-app enabled/push pending defaults until notification API exists. |
| Sync settings | partial | Bootstrap returns sync disabled with local offline queue allowed until server sync endpoints exist. |
| Unread notification count | partial | Bootstrap returns `0` until server notifications exist. |
| Mobile bootstrap service/cache | tested | `MobileBootstrapService` calls `GET /bootstrap` with the stored access token and caches the envelope in mobile-local settings; login/register refresh it after authentication. |

## Phase 11 - App Version, Force Update, And Maintenance Mode

| Feature | Status | Notes |
| --- | --- | --- |
| Admin app version control | not started | Required by Mobile Version Control Logic. |
| Minimum supported version | not started | Required for stale clients. |
| Optional update rules | not started | Required for release operations. |
| Force update rules | not started | Required for blocked clients. |
| Maintenance mode rules | not started | Required for incidents. |
| Mobile force update screen | not started | Required after version endpoint/bootstrap exists. |
| Optional update banner | not started | Required after version endpoint/bootstrap exists. |
| Maintenance screen | not started | Required after maintenance endpoint/bootstrap exists. |

## Phase 12 - Records/Content Module

| Feature | Admin/API Status | Mobile Status | Notes |
| --- | --- | --- | --- |
| Tenant-scoped records | not started | partial | Mobile-local records exist; server authority missing. |
| Records list/detail | not started | partial | Mobile screens exist. |
| Create/update/archive/restore/delete | not started | partial | Mobile-local flows exist; API acceptance missing. |
| Categories | not started | partial | Mobile-local categories exist. |
| Tags | not started | partial | Mobile-local tags exist. |
| Notes | not started | partial | Mobile-local notes exist. |
| Attachment metadata | not started | partial | Mobile-local attachment metadata exists. |
| Activity timeline | not started | partial | Local activity timeline exists; server audit missing. |
| Admin records management | not started | n/a | Admin shell exists; records management screens are not implemented yet. |
| Records API endpoints | not started | partial | Mobile queues reference API-style endpoints, but no server endpoints exist. |

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
| Sync bootstrap | not started | partial | Mobile sync status exists; server settings missing. |
| Sync push/pull/ack | not started | partial | Mobile queue and worker exist; server endpoints missing. |
| Sync conflict tracking | not started | partial | Local conflict fields and screens exist. |
| Admin sync monitoring | not started | n/a | Admin shell exists; sync monitoring screens are not implemented yet. |
| Local SQLite cache | n/a | partial | Dedicated `mobile_local` connection and migrations exist. |
| Offline action queue | n/a | partial | Queue/repository/worker exist. |
| Retry logic | not started | partial | Local worker has retry/backoff behavior. |
| Offline banner/manual sync | n/a | partial | Mobile surfaces exist. |

## Phase 15 - NativePHP Wrappers

| Wrapper | Status | Notes |
| --- | --- | --- |
| Device | partial | Service and tests exist; feature/permission gating needs API policy. |
| Network | partial | Service and UI surfaces exist; sync policy integration missing. |
| Camera | partial | Service and media screen exist; API upload policy missing. |
| File | partial | Service and file manager exist; API import/export policy missing. |
| Share | partial | Service exists; diagnostics/report sharing policy incomplete. |
| Browser | partial | Service exists. |
| Microphone | partial | Audio/voice service exists; API upload policy missing. |
| Location | partial | Location service/check-in screens exist; API acceptance missing. |
| Scanner | partial | Scanner service and screens exist; API/search/create integration incomplete. |
| Push notifications | not started | Local notifications exist; push token registration is missing. |
| Biometrics | partial | Biometric unlock service/tests exist. |
| Secure storage | partial | Native secure storage and session fallback exist. |
| System/settings | partial | System service exists. |
| Dialogs/toasts | partial | Dialog and toast services/surfaces exist. |

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
| Camera capture | partial | Mobile service/screen exists. |
| Gallery selection | partial | Media gallery exists. |
| Media preview | partial | Mobile media surfaces exist. |
| Attach media to records/support | partial | Record attachment surfaces exist; support/API missing. |
| Offline media queue | partial | Local media models exist; API upload/replay missing. |
| File import/export | partial | File manager exists; API policy missing. |
| Native share | partial | Share service exists; diagnostics/report flows incomplete. |

## Phase 18 - Scanner

| Feature | Status | Notes |
| --- | --- | --- |
| QR/barcode scanner | partial | Scanner service and demo screen exist. |
| Scan result screen/history | partial | Scan history exists. |
| Scan-to-search | partial | Needs complete record/search integration. |
| Scan-to-create | not started | Not found in current implementation evidence. |
| Duplicate/invalid scan handling | partial | Needs verification. |
| Offline scan sync | partial | Local scan history exists; API sync missing. |

## Phase 19 - Geolocation

| Feature | Status | Notes |
| --- | --- | --- |
| Location permission UX | partial | Permission/service surfaces exist. |
| Geolocation check-ins | partial | Check-in screens and local model exist. |
| Accuracy display | partial | Needs verification against UI/tests. |
| Attach location to records/support | not started | API/support integration missing. |
| Offline location queue | partial | Local check-ins exist; server sync missing. |
| Privacy principles | documented | Needs enforcement in API/support diagnostics. |

## Phase 20 - Voice Notes

| Feature | Status | Notes |
| --- | --- | --- |
| Voice note recording | partial | Audio service and voice-note screen exist. |
| Pause/resume | partial | Needs capability verification. |
| Save locally | partial | Local voice-note model/repository exist. |
| Attach to records/support | partial | Record/support API integration missing. |
| Upload queue | partial | Local storage exists; API upload/replay missing. |
| Playback/list/detail | partial | Mobile voice notes screen exists. |

## Phase 21 - Push Notifications And Notification Inbox

| Feature | Admin/API Status | Mobile Status | Notes |
| --- | --- | --- | --- |
| Notifications | not started | partial | Local notification inbox exists; server notifications missing. |
| Notification preferences | not started | partial | Mobile settings exist; API authority missing. |
| Push token registration/revocation | not started | not started | Required for push. |
| Admin notification center | not started | n/a | Admin shell exists; notification center screens are not implemented yet. |
| Campaign placeholders | not started | n/a | Not implemented. |
| Delivery/open tracking | not started | partial | Local history exists; server truth missing. |
| Mark read/all read/delete | not started | partial | Local inbox behavior exists. |
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
| Plans/subscriptions | not started | Documented only. |
| Invoice placeholders | not started | Documented only. |
| Usage events | not started | Documented only. |
| Admin billing screens | not started | Admin shell exists; billing screens are not implemented yet. |
| Plan-based feature availability | not started | Required in feature resolution/bootstrap. |
| Mobile plan/status display | not started | Required after bootstrap/billing API. |
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
| Central mobile control dashboard | not started | Required in `apps/api-admin`. |
| Module controls | not started | Needs feature flag/remote config foundation. |
| Feature flags and tenant overrides | not started | Needs feature implementation. |
| Remote config | not started | Needs config implementation. |
| App versions, force update, maintenance | not started | Needs version policy implementation. |
| Sync/offline/upload limits | not started | Needs sync/config implementation. |
| Push/support/legal links | not started | Needs notification/support/config implementation. |
| Mobile effect preview | not started | Required by design docs. |

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
| API/admin tests | tested | `php artisan test --compact` passes in `apps/api-admin`. |
| API/admin frontend build | tested | `npm run build` passes in `apps/api-admin`. |
| API routes verification | tested | `php artisan route:list --except-vendor` shows status and contracts routes. |
| Admin navigation verification | tested | Admin dashboard smoke coverage exists; browser-level verification remains future. |
| Mobile formatting | partial | Existing PHP code needs fresh `vendor/bin/pint --dirty --format agent` after edits. |
| Mobile tests | partial | Many tests exist; full fresh run pending. |
| Mobile frontend build | partial | Build command exists; fresh run pending. |
| Mobile navigation verification | partial | Routes exist; browser/native verification pending. |
| NativePHP fallback verification | partial | Some service tests exist; native tooling blockers remain. |
| Offline/sync verification | partial | Local worker tests exist; server sync missing. |
| Root monorepo scripts | not started | Scripts missing. |
| Final git status | not started | Must be clean after commits. |

## Highest-Priority Implementation Order

1. Implement tenancy, roles, feature flags, remote config, version/maintenance,
   and audit before broad records/support/billing/reporting expansion.
2. Replace bootstrap foundation defaults with real tenant, permission, feature,
   config, version, subscription, notification, and sync policy modules.
3. Migrate existing mobile-local features behind API-derived policy instead of
   letting local screens remain standalone authority.

## Current Blocking Risks

| Risk | Status | Mitigation |
| --- | --- | --- |
| Root transition app still exists | partial | Decide when to remove or rewire it after `apps/mobile-client` is fully authoritative. |
| Admin/API domain modules do not exist yet | partial | Build auth, tenancy, policy, feature, config, version, and audit foundations before business modules. |
| API contracts are documented but most endpoints are planned | partial | Implement endpoints in phase order and keep each contract updated before code changes. |
| Mobile features are mostly local-only | partial | Route all server-trusted behavior through API contracts as they land. |
| Native build tooling incomplete | partial | Keep NativePHP service fallbacks tested; treat simulator/emulator release verification as external blocker until tooling is installed. |
| Full completion scope is very large | partial | Continue in small commits by phase; keep this checklist authoritative. |
