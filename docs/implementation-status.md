# Implementation Status

Updated: 2026-06-25

This file is the required implementation gate for Mobile Lara. It translates the
product Markdown corpus into an executable checklist and records the current
state before new implementation work continues.

Admin Control Center logic is defined in
`docs/admin-control-center-logic.md`. Future implementation work must map
tenant, user, role, permission, mobile feature, remote config, app version,
maintenance, force update, sync, notification, report, billing, and support
controls to that document before code is written.

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
| Root application | A Laravel 13 + Livewire 4 + NativePHP Mobile app exists at the repository root. |
| Requested monorepo paths | `apps/api-admin` and `apps/mobile-client` exist as Phase 1 boundary scaffolds. |
| API routes | `routes/api.php` is not present yet. |
| Mobile routes | 52 `mobile.*` Livewire routes exist in `routes/web.php`. |
| Active database | Default SQLite contains only framework tables and `users`; no SaaS control-plane schema exists yet. |
| Mobile local database | Dedicated `mobile_local` connection, local migrations, local models, repositories, and health command exist. |
| Admin/API system | Product responsibilities are documented, but code is not implemented yet. |
| Contracts directory | `contracts/api` exists with the initial response-envelope contract guidance. |
| Scripts directory | `scripts` exists with root helper guidance; no custom helper scripts are needed yet. |
| Tests | Many mobile/local/NativePHP Pest tests exist. Full-suite verification has not yet been run in this implementation pass. |
| Native tooling | Docs record that Xcode, Android Studio, and Gradle were not detected in previous checks. |

## Phase 1 - Repository Foundation

| Feature | Status | Notes |
| --- | --- | --- |
| Root README and product docs | documented | Root docs define the product, boundaries, stack, and audit baseline. |
| Admin Control Center logic | documented | Control principles exist for tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance, force update, sync, notifications, reports, billing, and support. |
| Root monorepo structure | partial | `apps/api-admin` and `apps/mobile-client` exist as documented boundaries; code migration is pending. |
| `apps/api-admin` | partial | Path and boundary README exist; Laravel app implementation is pending. |
| `apps/mobile-client` | partial | Path and boundary README exist; existing root mobile app migration is pending. |
| `docs` | documented | Core docs exist; implementation docs need to track real code as it lands. |
| `contracts/api` | partial | Directory and response-envelope README exist; individual v1 contracts are pending. |
| Root scripts | partial | Directory and guidance exist; no custom wrappers are needed before app split. |
| Environment examples | partial | Root `.env.example` exists; per-app env examples are not present. |
| Documentation structure | partial | Product docs, implementation status, remaining tasks, changelog, app boundary docs, and contract guidance exist. |
| Git state discipline | partial | Repository is clean and ahead of origin by 2 commits before this implementation pass. |

## Phase 2 - API/Admin Foundation

| Feature | Status | Notes |
| --- | --- | --- |
| Complete Laravel app under `apps/api-admin` | not started | No control-plane app exists in the requested path. |
| Livewire admin panel | not started | No admin Livewire namespace, layout, or routes exist yet. |
| Blade and Tailwind admin UI | not started | Admin design principles exist only in docs. |
| Admin, auth, and dashboard layouts | not started | Required layouts are missing for the admin system. |
| Reusable admin UI components | not started | Required components are missing. |
| Grouped admin routes | not started | No admin route group exists. |
| Versioned API route structure | not started | `routes/api.php` is missing in the current root app. |
| Standard API success response | not started | No shared API responder/resource envelope exists yet. |
| Standard API error response | partial | Bootstrap config renders JSON for `api/*`, but no documented envelope exists. |
| API/admin tests | not started | No control-plane feature tests exist. |

## Phase 3 - Mobile Client Foundation

| Feature | Status | Notes |
| --- | --- | --- |
| Complete Laravel app under `apps/mobile-client` | partial | A complete mobile Laravel app exists at root, but not in the requested path. |
| NativePHP Mobile configuration | partial | NativePHP config and generated native artifacts exist. Tooling blockers remain for simulator/emulator builds. |
| Livewire + Blade mobile UI | partial | Many class-based Livewire mobile components and Blade views exist. |
| Tailwind mobile styling | partial | Tailwind v4/SCSS entrypoint and mobile design tokens exist. |
| Mobile-first layout and safe-area shell | partial | Layout and mobile components exist; needs per-app relocation/verification. |
| Welcome screen | partial | `Mobile\Welcome` route and view exist. |
| Auth screens | partial | Login, register, password reset, verification, PIN, unlock, and consent screens exist. API authority is still missing. |
| Dashboard | partial | `Mobile\Dashboard` exists. Admin/API bootstrap integration is missing. |
| Bottom navigation | partial | `<x-mobile.bottom-navigation>` exists. Feature-gated navigation is not API-controlled yet. |
| Settings | partial | Settings index and sections exist. Remote config/tenant policy are not integrated. |
| Profile | partial | Profile and edit profile screens exist. API profile endpoint is missing. |
| Notifications page | partial | Local notification inbox exists. Push/API notification authority is missing. |
| Debug/diagnostics page | partial | Debug screen exists. Full privacy-safe diagnostics export/share is incomplete. |
| Reusable mobile UI components | partial | Components exist and have some test coverage. |

## Phase 4 - API Contracts

| Contract Area | Status | Notes |
| --- | --- | --- |
| API contract documentation directory | partial | `contracts/api/README.md` defines envelope standards and required contract files. |
| Versioned response envelope | not started | Must define `success`, `data`, `meta`, `errors`, and mobile next actions. |
| Auth contract | documented | Product behavior is documented; endpoints are not implemented. |
| Bootstrap contract | documented | Required payload is documented; endpoint is not implemented. |
| Features contract | documented | Resolution rules are documented; endpoint is not implemented. |
| Remote config contract | documented | Product rules exist; endpoint/schema are not implemented. |
| Notifications contract | documented | Product rules exist; API is not implemented. |
| Records/content contract | partial | Mobile-local records exist; server API contract is missing. |
| Sync contract | partial | Mobile queue/replay worker exists; server sync contract is missing. |
| Support contract | documented | Product rules exist; API is not implemented. |
| Billing contract | documented | Product rules exist; API is not implemented. |
| Reports contract | documented | Product rules exist; API is not implemented. |

## Phase 5 - Authentication And Sessions

| Feature | Admin/API Status | Mobile Status | Notes |
| --- | --- | --- | --- |
| Admin authentication | not started | n/a | No admin auth surface exists. |
| API authentication | not started | partial | Mobile token services exist locally, but no API token authority exists. |
| Access tokens | not started | partial | Mobile storage and placeholder services exist. |
| Refresh tokens | not started | partial | Mobile refresh service exists without server endpoint. |
| Logout | not started | partial | Mobile logout UX/services exist. |
| Logout all devices | not started | partial | Mobile session screens exist; server authority is missing. |
| Current user endpoint | not started | not started | Required for bootstrap/profile. |
| Profile update endpoint | not started | partial | Mobile edit profile exists; API endpoint missing. |
| Device/session logic | not started | partial | Mobile session display exists; server device trust missing. |
| Security audit events | not started | partial | Local activity logs exist; server audit truth missing. |

## Phase 6 - Tenancy

| Feature | Status | Notes |
| --- | --- | --- |
| Tenant model and lifecycle | not started | No tenant schema/code exists. |
| Tenant users/memberships | not started | Required for multi-tenant access. |
| Tenant roles and invitations | not started | Role states are documented only. |
| Tenant settings | not started | Required for feature/config/sync policy. |
| Tenant-scoped API middleware | not started | Critical security boundary. |
| Admin tenant management screens | not started | No admin panel exists. |
| Mobile tenant store/display/switcher | not started | Required after API bootstrap exists. |
| Tenant-separated local cache | partial | Local models have mobile-local storage, but tenant partitioning is not fully proven. |

## Phase 7 - Roles, Permissions, And Policies

| Feature | Status | Notes |
| --- | --- | --- |
| Role definitions | documented | Product role model exists; implementation missing. |
| Permission definitions | documented | Permission ownership rules exist; implementation missing. |
| Policies for API/admin | not started | Required before protected actions. |
| Protected admin routes | not started | No admin routes exist. |
| Protected API routes | not started | API routes are missing. |
| Mobile permission payload | not started | Bootstrap endpoint must provide this. |
| Mobile permission-aware UI | partial | Permission settings/center exists for NativePHP device permissions, not SaaS permissions. |

## Phase 8 - Feature Flags

| Feature | Status | Notes |
| --- | --- | --- |
| Global feature flags | not started | Required server authority. |
| Tenant feature overrides | not started | Required for SaaS control. |
| User feature overrides | not started | Required by requested resolution order. |
| Resolution order user -> tenant -> global | documented | Needs implementation and tests. |
| Admin feature flag UI | not started | No admin panel exists. |
| Mobile feature store/cache | not started | Required after bootstrap exists. |
| Feature-gated mobile navigation/actions | partial | Mobile routes exist; not API/feature controlled yet. |

## Phase 9 - Remote Config

| Feature | Status | Notes |
| --- | --- | --- |
| Global remote config | not started | Required server authority. |
| Tenant remote config | not started | Required for tenant variation. |
| Admin remote config UI | not started | No admin panel exists. |
| Config validation and audit | not started | Required for sensitive controls. |
| Mobile config store/cache | not started | Required after bootstrap exists. |
| Offline defaults | documented | Needs implementation against API payloads. |
| Config-driven sync/upload/legal/support behavior | partial | Some local config files exist; remote config is missing. |

## Phase 10 - Mobile Bootstrap

| Payload Item | Status | Notes |
| --- | --- | --- |
| Authenticated user | not started | No endpoint. |
| Current tenant | not started | No tenant implementation. |
| Available tenants | not started | No tenant implementation. |
| Permissions | not started | No role/permission implementation. |
| Feature flags | not started | No feature implementation. |
| Remote config | not started | No config implementation. |
| App version rules | not started | No version policy implementation. |
| Maintenance mode | not started | No maintenance policy implementation. |
| Subscription status | not started | No billing implementation. |
| Notification preferences | not started | No API notification preferences. |
| Sync settings | not started | No server sync settings. |
| Unread notification count | partial | Local notification count exists; API count missing. |
| Mobile bootstrap service/cache | not started | Required after endpoint exists. |

## Phase 11 - App Version, Force Update, And Maintenance Mode

| Feature | Status | Notes |
| --- | --- | --- |
| Admin app version control | not started | Required by docs. |
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
| Admin records management | not started | n/a | No admin panel exists. |
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
| Admin sync monitoring | not started | n/a | No admin panel exists. |
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
| Admin notification center | not started | n/a | No admin panel exists. |
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
| Admin billing screens | not started | No admin panel exists. |
| Plan-based feature availability | not started | Required in feature resolution/bootstrap. |
| Mobile plan/status display | not started | Required after bootstrap/billing API. |
| Trial/expired/suspended behavior | documented | Implementation missing. |

## Phase 24 - Support System

| Feature | Status | Notes |
| --- | --- | --- |
| Support tickets/messages | not started | Documented only. |
| Support attachments | not started | Documented only. |
| Admin support panel | not started | No admin panel exists. |
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
| API contracts | not started | Need `contracts/api`. |
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
| API/admin formatting | not started | No app exists yet. |
| API/admin tests | not started | No app exists yet. |
| API/admin frontend build | not started | No app exists yet. |
| API routes verification | not started | `routes/api.php` missing. |
| Admin navigation verification | not started | Admin panel missing. |
| Mobile formatting | partial | Existing PHP code needs fresh `vendor/bin/pint --dirty --format agent` after edits. |
| Mobile tests | partial | Many tests exist; full fresh run pending. |
| Mobile frontend build | partial | Build command exists; fresh run pending. |
| Mobile navigation verification | partial | Routes exist; browser/native verification pending. |
| NativePHP fallback verification | partial | Some service tests exist; native tooling blockers remain. |
| Offline/sync verification | partial | Local worker tests exist; server sync missing. |
| Root monorepo scripts | not started | Scripts missing. |
| Final git status | not started | Must be clean after commits. |

## Highest-Priority Implementation Order

1. Finish Phase 1 by creating the monorepo structure, API contract directory,
   root helper scripts/docs, remaining-task tracking, and migration plan for the
   existing mobile app into `apps/mobile-client`.
2. Build Phase 2 as a minimal but real API/admin foundation with versioned
   `api/v1/mobile` routes, shared response envelope, admin routes/layout, and
   smoke tests.
3. Connect Phase 4 and Phase 10 with the first mobile bootstrap endpoint because
   it becomes the control point for tenancy, permissions, features, config,
   version rules, subscription status, notifications, and sync policy.
4. Implement tenancy, roles, feature flags, remote config, version/maintenance,
   and audit before broad records/support/billing/reporting expansion.
5. Migrate existing mobile-local features behind API-derived policy instead of
   letting local screens remain standalone authority.

## Current Blocking Risks

| Risk | Status | Mitigation |
| --- | --- | --- |
| Requested monorepo does not match current root app | partial | Introduce `apps/` structure with docs and safe wrappers first, then move code in controlled commits. |
| Admin/API does not exist yet | not started | Build minimal control-plane foundation before adding business modules. |
| API contracts do not exist yet | not started | Add `contracts/api` before endpoint implementation. |
| Mobile features are mostly local-only | partial | Route all server-trusted behavior through API contracts as they land. |
| Native build tooling incomplete | partial | Keep NativePHP service fallbacks tested; treat simulator/emulator release verification as external blocker until tooling is installed. |
| Full completion scope is very large | partial | Continue in small commits by phase; keep this checklist authoritative. |
