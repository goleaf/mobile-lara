# Mobile Client App

`apps/mobile-client` is the target home for the Laravel + Livewire + NativePHP
mobile client.

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
device access. Core recovery surfaces such as dashboard, profile, settings,
workspace switching, support, and billing stay reachable so users can recover
when a tenant or policy state blocks a workflow.

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

This directory now contains a complete Laravel 13 + Livewire 4 + NativePHP
Mobile application copied from the verified root mobile client, plus the first
tested API/auth service boundary for talking to `apps/api-admin`.

Implemented foundation:

- 52 `mobile.*` Livewire routes.
- Mobile-first Blade layout, safe-area shell, bottom navigation, and reusable
  mobile components.
- Welcome, auth, dashboard, settings, profile, notifications, debug,
  records/content, media, scanner, location, voice-note, sync, and local
  support surfaces from the root mobile app.
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
- Returned access and refresh tokens are stored through `MobileTokenStore`,
  which defaults to NativePHP secure storage and uses the session adapter for
  tests or safe development fallback.
- `MobileDeviceContext` sends stable device id, device name, platform, and app
  version metadata with auth requests.
- Login and register Livewire screens call the API service, then create a
  local presentation-only Laravel session from the API user payload.
- Profile logout and sessions logout/logout-all call the API service before
  clearing local session/token state.
- Edit profile syncs the account name through `PATCH /auth/profile` when a
  valid access token exists; avatar storage remains local until a media/upload
  API slice.
- `App\Services\MobileBootstrap\MobileBootstrapService` calls
  `GET /bootstrap` with the stored access token and caches the response in the
  mobile-local settings row.
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

Fresh verification:

```bash
composer validate --strict
php artisan route:list --name=mobile
php artisan test --compact
php artisan test --compact --filter=MobileAuthApiServiceTest
php artisan test --compact --filter=MobileBootstrapServiceTest
php artisan test --compact --filter=MobileTenantApiServiceTest
php artisan test --compact --filter=MobileWorkspaceSettingsTest
vendor/bin/pint --dirty --format agent
npm run build
php artisan native:plugin:validate --no-interaction
```

`native:plugin:validate` exits successfully. It reports non-fatal warnings for
two third-party plugins that do not define bridge functions or native code
directories.

The repository root app remains temporarily as a transition mirror. Future
mobile work should target `apps/mobile-client` unless a cleanup task explicitly
removes or rewires the root app.

Next platform work is to replace bootstrap foundation defaults with real
permission, feature flag, remote config, app-version, billing, notification,
and sync policy modules, then partition local caches by API-selected tenant
where each local module needs tenant-specific data.
