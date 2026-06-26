# Mobile Client App

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

`apps/mobile-client` is the target home for the Laravel + Livewire + NativePHP
mobile client.

Local Herd serves this app at `https://mobile-lara.test`. The app-local `.env`
belongs in this folder and points `MOBILE_API_BASE_URL` to
`https://mobile-lara-api-admin.test/api/v1/mobile`.

Product Vision is defined in `../../docs/product-vision.md`: this app exists as
the managed NativePHP + Livewire execution surface for mobile users, not as a
source of SaaS authority.

Product Positioning is defined in `../../docs/product-positioning.md`: this app
is the mobile workforce/client platform side of the product, not an independent
mobile authority.

Core Product Principles are defined in `../../docs/product-principles.md`: this
app must never bypass API authority, must keep mobile UX simple, and must treat
offline state as cache, draft, queue, pending, synced, conflict, or failed
state rather than server truth.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this app communicates with Admin/API only
through API, consumes predictable context and response states, shows
mobile-friendly errors, replays offline work through sync/conflict contracts,
and treats tenant boundaries as server-protected.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: this app must not add mobile
screens, NativePHP flows, cache/offline behavior, sync display, permissions UX,
or feature visibility until API dependency, admin effect, online/offline
behavior, permission owner, and risk are documented.

Target User Roles are defined in `../../docs/user-roles.md`: this app must show
mobile, invited, suspended, and guest/pre-login states as API-derived UX, not
local permission authority.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: this app must turn
stakeholder value into simple API-derived mobile access, offline/sync state,
notification UX, secure local behavior, and clear enabled or blocked feature
states.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: this app owns local execution and must not
turn cache, drafts, queued intents, NativePHP state, or UI visibility into SaaS
authority.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this app consumes those
responsibility outcomes through API and must not duplicate tenant, permission,
billing, feature, config, version, notification, support, report, audit,
conflict, or security authority locally.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this app owns the mobile
experience, secure local session presentation, local cache, offline actions,
NativePHP device-feature UX, navigation, permissions UX, sync display, local
drafts, user feedback, and API-derived feature visibility while tenant,
billing, permission, and global configuration authority stay in Admin/API.

Mobile UX Principles are defined in `../../docs/mobile-ux-principles.md`: this
app should make NativePHP navigation, simple screens, loading/offline states,
thumb-friendly controls, minimum typing, fast actions, secure sessions, feature
visibility, and native permission education feel clear and API-governed.

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

This system owns local execution:

- mobile-first Livewire screens and Blade components
- NativePHP service wrappers and permission UX
- secure local session presentation
- local SQLite cache, drafts, queued intents, and sync metadata
- offline banners, pending/conflict state, and user feedback
- API-derived feature visibility and blocked/disabled/update-required states

The mobile client must never own tenant authority, permission authority,
billing authority, feature flag authority, app-version authority, server audit
truth, or final conflict decisions.

Current implementation note: `MobileBootstrapService` caches the Admin/API
bootstrap envelope in local settings, and `MobileAccessPolicy` reads that
cached context to shape the app shell. Primary navigation, dashboard quick
actions, create actions, search results, and guarded module routes now respect
API-derived feature state, permissions, subscription limits, maintenance mode,
notification policy, and sync policy. The permissions center uses the same
policy before offering NativePHP permission prompts, so disabled camera,
microphone, location, notification, file, or biometric features do not ask for
device access. Record create, update, archive, restore, delete, bulk local
mutations, attachment management, and attachment sharing are also checked
against cached API permissions before local SQLite writes or native share
handoff. Voice-note recording, native microphone callbacks, local voice-note
save/delete actions, and upload queue placeholders are checked against cached
microphone and sync policy before local SQLite writes, file deletes, or offline
queue writes. Native location permission/current-position calls, location
callbacks, check-in creation, and check-in history create shortcuts are checked
against cached location and sync policy before native handoff or local
check-in writes. Media capture actions and callbacks are checked against
cached camera policy before native handoff or local media-list changes. File
manager writes, reads, copies, moves, imports, exports, deletes, and share
handoffs are checked against cached file/share policy before local sandbox
changes or native share handoff. Profile sharing, record-detail sharing, and
media-gallery sharing are also checked against cached native share policy
before user-facing share controls render or direct Livewire calls can reach the
native wrapper. Scanner capture callbacks and saved scan-history deletes/clears
are checked against cached scanner policy before local scan-history writes or
deletes. Notification inbox rendering and read/open/read-all actions are
checked against cached notification policy before local inbox rows are shown or
mutated. Manual sync and conflict resolution actions are checked against cached
sync policy before local sync timestamps or conflict queue statuses change, and
the offline-first queue service refuses new replay intents when offline sync is
disabled. Core recovery surfaces such as dashboard, profile, settings,
workspace switching, support, and billing stay reachable so users can recover
when a tenant or policy state blocks a workflow. Support-center browser handoff
is checked against cached native browser policy before support settings opens a
NativePHP browser surface. The Billing screen is checked against cached
`billing` and `billing.view` policy before API refresh, and billing portal
handoff is checked against cached `native_browser` policy. Developer debug
native-action examples use the same cached policy before camera, notification,
share, browser, device, dialog, or secure storage wrapper calls.

Admin Control Center logic in `../../docs/admin-control-center-logic.md`
defines the server-side controls that mobile receives as API outcomes:
tenant, user, role, permission, feature, remote config, app version,
maintenance, force update, sync, notification, report, billing, and support
state.

Feature Flag Logic in `../../docs/feature-flag-logic.md` defines the mobile
states the client should receive from API: hidden, visible, disabled, blocked,
beta, deprecated, update-required, offline-limited, or emergency-disabled.

Remote Configuration Logic in `../../docs/remote-configuration-logic.md`
defines how the client receives resolved config, caches it with version and
freshness state, behaves offline, and falls back or fails closed when config is
missing or invalid.

Mobile Version Control Logic in
`../../docs/mobile-version-control-logic.md` defines how the client reports its
version, receives optional-update, force-update, maintenance, blocked, or
deprecated states, shows store links/update messages, and avoids unsafe old
version behavior.

Admin Safety Principles in `../../docs/admin-safety-principles.md` define how
dangerous admin actions should be confirmed, audited, impact-previewed,
mobile-previewed, rollback-aware, and tenant-isolated before the mobile client
receives changed API outcomes.

Mobile UX Principles in `../../docs/mobile-ux-principles.md` define how the
client presents mobile-first navigation, simple screens, loading/offline
states, thumb-friendly controls, minimum typing, fast actions, secure session
behavior, feature visibility, and native permission education.

## Current Implementation State

This directory contains the complete Laravel 13 + Livewire 4 + NativePHP
Mobile application. It is the only mobile runtime in the monorepo and talks to
`apps/api-admin` through the tested API/auth service boundary.

Implemented foundation:

- 52 `mobile.*` Livewire routes.
- Mobile-first Blade layout, safe-area shell, bottom navigation, and reusable
  mobile components.
- Welcome, auth, dashboard, settings, profile, notifications, debug,
  records/content, media, scanner, location, voice-note, sync, and local
  support surfaces.
- NativePHP config, launcher, lockfile, service provider, and safe service
  wrappers.
- Dedicated `mobile_local` SQLite connection, migrations, models,
  repositories, queue/sync worker, local notifications, and health command.
- Focused Pest coverage copied with the app.
- `config/mobile_auth.php` exposes the mobile API base URL and timeout
  settings through `MOBILE_API_BASE_URL`, `MOBILE_API_TIMEOUT_SECONDS`, and
  `MOBILE_API_CONNECT_TIMEOUT_SECONDS`.
- `App\Services\MobileApi\MobileApiClient` sends standard JSON requests to the
  versioned mobile API and converts standard error envelopes into
  `MobileApiException`.
- `App\Services\MobileAuth\MobileAuthApiService` calls the API/admin auth
  endpoints for login, register, refresh, logout, logout-all, current user, and
  profile update.
- `App\Services\MobileRecords\MobileRecordApiService` and
  `App\Services\MobileRecords\MobileRecordSyncService` call the API/admin
  records endpoints for local-first create/update/archive/restore sync
  attempts, then store server record IDs and sync versions in local metadata.
- `App\Services\MobileSync\MobileSyncApiService` calls
  `GET /sync/bootstrap`, `GET /sync/pull`, `POST /sync/push`, and
  `POST /sync/acknowledge` with the stored access token so the mobile client
  can consume the dedicated records-only sync contract.
- `App\Services\MobileNotifications\MobileNotificationsApiService` calls the
  API/admin notification inbox, read/read-all/delete, and push-token
  register/revoke endpoints with the stored access token.
- `App\Services\MobileSupport\MobileSupportApiService` calls the API/admin
  support ticket list/create/detail and message-create endpoints with the
  stored access token.
- `App\Livewire\Mobile\SupportTickets`,
  `App\Livewire\Mobile\SupportTicketCreate`, and
  `App\Livewire\Mobile\SupportTicketDetail` expose the support list, create,
  detail, and reply flow through the API-backed service while respecting cached
  support feature/permission gates.
- Returned access and refresh tokens are stored through `MobileTokenStore`,
  which defaults to NativePHP secure storage and uses the session adapter for
  tests or safe development fallback.
- `MobileDeviceContext` sends stable device id, device name, platform, and app
  version metadata with auth requests.
- Login and register Livewire screens call the API service, store secure token
  state, then create a Laravel session from the API user id without creating or
  updating a local mobile `users` row.
- Profile logout and sessions logout/logout-all call the API service before
  clearing local session/token state.
- Edit profile syncs account details (`name`, `username`, `phone`, `bio`,
  `location`, `website`), optional avatar upload, and avatar removal through
  `PATCH /auth/profile`. Profile display and edit hydration use
  `GET /auth/user`; mobile no longer persists account/profile fields in its
  local user table. Temporary avatar staging is cleaned after the API accepts
  the upload, and the UI displays the API-returned `avatar_url`.
- Record create, edit, list-row, detail, and bulk actions now call the
  records API service for server-backed create/update/archive/restore/delete
  mutations before changing local cache rows. A failed API delete keeps the
  server-backed local row in place and shows a blocked delete state.
- Notification inbox read, open-as-read, and read-all actions now call the
  notifications API for server-backed rows before updating local read/open
  cache state.
- Device-local actions remain intentionally local: PIN/app-lock state, secure
  storage, local cache reset, local file import/export/delete, native
  permission probes, native share/browser/dialog wrappers, diagnostics export,
  and offline drafts/queues until their replay endpoint accepts them.
- `App\Services\MobileBootstrap\MobileBootstrapService` calls
  `GET /bootstrap` with the stored access token and caches the response in the
  mobile-local settings row.
- `App\Services\MobileConfig\MobileRemoteConfigStore` reads cached bootstrap
  remote config without creating local settings rows, applies bundled safe
  defaults when offline or uninitialized, and feeds dashboard widgets, sync
  settings, upload-limit hints, app-lock policy copy, support URLs, and legal
  URLs.
- `App\Services\MobileDiagnostics\MobileDiagnosticsReportBuilder` builds a
  privacy-safe diagnostics snapshot from cached bootstrap context, feature
  state, redacted remote config, network state, sync queue counts, failed sync
  action metadata, and safe device-service fields. The developer debug screen
  previews that snapshot, exports `mobile-lara-diagnostics.json` through a
  Livewire download, and shares the same redacted JSON through the NativePHP
  share wrapper when the cached `native_share` policy allows it.
- Login and register refresh bootstrap immediately after authentication, so
  the next phase can hydrate tenant, permission, feature, config, version,
  subscription, notification, and sync policy from one cached context.
- `App\Services\MobileTenancy\MobileTenantContextStore` reads the cached
  bootstrap envelope for presentation-only tenant context and safely renders an
  empty workspace state before the local settings table is initialized.
- `App\Services\MobileTenancy\MobileTenantApiService` calls the Admin/API
  tenant list and switch endpoints with the stored access token.
- `App\Livewire\Mobile\Settings\Workspace` displays the current tenant,
  available switchable tenants, supports manual bootstrap refresh, and switches
  the current tenant through `POST /tenants/current` before refreshing
  bootstrap.
- `App\Services\MobileBilling\MobileBillingApiService` calls
  `GET /billing/subscription` with the stored access token.
- `App\Livewire\Mobile\Billing` displays the current plan, subscription state,
  trial ending, limits/usage snapshots, available actions, and portal status.
  It caches live subscription payloads into the local bootstrap context and
  displays cached state as last-known when the API is unavailable.

Fresh verification:

```bash
composer validate --strict
php artisan route:list --name=mobile
php artisan test --compact
php artisan test --compact --filter=MobileAuthApiServiceTest
php artisan test --compact --filter=MobileBootstrapServiceTest
php artisan test --compact --filter=MobileTenantApiServiceTest
php artisan test --compact --filter=MobileWorkspaceSettingsTest
php artisan test --compact tests/Feature/MobileBillingScreenTest.php
php artisan test --compact tests/Feature/MobileDiagnosticsReportTest.php tests/Feature/MobileDebugDialogExamplesTest.php tests/Feature/NativeShareServiceTest.php
vendor/bin/pint --dirty --format agent
npm run build
php artisan native:plugin:validate --no-interaction
```

`native:plugin:validate` exits successfully. It reports non-fatal warnings for
two third-party plugins that do not define bridge functions or native code
directories.

The repository root is now a monorepo shell. Future mobile work should target
`apps/mobile-client`; Admin/API authority belongs in `apps/api-admin`.

Next platform work is to replace bootstrap foundation defaults with real
permission, feature flag, remote config, app-version, billing, notification,
and sync policy modules, then partition local caches by API-selected tenant
where each local module needs tenant-specific data.
