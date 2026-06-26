# Apps

This directory is the target monorepo boundary for the two Mobile Lara systems.

Product Vision is defined in `../docs/product-vision.md`: both apps exist to
support remote control with local resilience, where Admin/API owns authority and
mobile owns local execution.

Product Positioning is defined in `../docs/product-positioning.md`: the two apps
must remain one SaaS control center plus one managed mobile workforce/client
platform.

Core Product Principles are defined in `../docs/product-principles.md`: both
apps must preserve admin authority, API-only mobile behavior, feature control,
tenant isolation, useful offline behavior, secure defaults, simple mobile UX,
documentation-first planning, and modular expansion.

API-First Principles are defined in `../docs/api-first-principles.md`: both
apps must keep mobile communication API-only, responses predictable, feature
API purpose explicit, operating context complete enough for mobile behavior,
errors mobile-friendly, sync/conflict behavior first-class, and tenant
boundaries protected server-side.

Target User Roles are defined in `../docs/user-roles.md`: both apps must keep
platform owner, super admin, tenant admin, tenant manager, support agent,
billing manager, mobile user, invited user, suspended user, and guest/pre-login
boundaries explicit.

SaaS Value Map is defined in `../docs/saas-value-map.md`: both apps must connect
admin control, mobile access, offline sync, notifications, reports, security,
and feature flags to explicit stakeholder value.

Two-System Boundary Logic is defined in `../docs/two-system-boundary.md`: both
apps must keep Admin/API authority separate from mobile local execution, cache,
drafts, queues, NativePHP capability use, and offline state.

Admin/API Responsibilities are defined in
`../docs/admin-api-responsibilities.md`: both apps must map tenant, user,
permission, API, feature, config, version, notification, billing, support,
report, audit, conflict, and security decisions to the control plane.

Mobile Client Responsibilities are defined in
`../docs/mobile-client-responsibilities.md`: both apps must keep mobile UX,
secure local session, cache, offline actions, NativePHP device features,
navigation, permissions UX, sync display, drafts, feedback, and API-derived
feature visibility in the managed mobile client without moving authority out
of Admin/API.

Mobile UX Principles are defined in `../docs/mobile-ux-principles.md`: both
apps must keep NativePHP navigation, simple screens, loading/offline states,
thumb-friendly controls, minimum typing, fast actions, secure sessions, and
native permission prompts aligned with API-derived authority.

Mobile App Shell Logic is defined in `../docs/mobile-app-shell-logic.md`:
shell states must coordinate welcome, authenticated, locked, offline, maintenance, forced update, tenant
switching, sync-in-progress, permission-blocked, and feature-disabled behavior
before implementation.

Mobile Dashboard Logic is defined in `../docs/mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Mobile Settings Logic is defined in `../docs/mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `../docs/mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

Authentication Principles are defined in `../docs/authentication-principles.md`:
mobile login must happen through the API only; access and refresh tokens must
use secure storage; refresh, logout, logout-all-devices, tenant selection,
session expiry, offline already-authenticated behavior, and server revocation
must preserve Admin/API authority before implementation.

Mobile App Lock Principles are defined in `../docs/mobile-app-lock-principles.md`:
the mobile client must lock on security-sensitive lifecycle, timeout,
account, tenant, offline-cache, and admin-policy conditions; require
confirmation for sensitive areas; use biometric or PIN unlock only as local
protection; handle failed attempts, logout, admin-disabled biometrics, and
offline cached data without bypassing Admin/API authority.

Role And Permission Logic is defined in `../docs/role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact
with feature flags as separate gates; suspended users and suspended tenants
fail closed without bypassing tenant isolation.

Audit Logic is defined in `../docs/audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

Data Privacy Principles are defined in `../docs/data-privacy-principles.md`:
tenant isolation, least privilege, secure local mobile data, secure native
storage, export and deletion boundaries, support access limits, admin
visibility boundaries, privacy-by-default behavior, and mobile diagnostics
privacy limits must protect users and tenants without turning mobile cache,
support views, or audit history into uncontrolled data exposure.

Tenant Lifecycle Logic is defined in `../docs/tenant-lifecycle-logic.md`:
tenant creation, onboarding, trial, active, suspended, archived,
billing-blocked, deletion/requested deletion, and restore states must be
Admin/API-owned lifecycle decisions that mobile presents as safe,
tenant-scoped, billing-aware, supportable states without inventing local
tenant authority.

Tenant Admin Logic is defined in `../docs/tenant-admin-logic.md`:
tenant admins may manage tenant-scoped users, invitations, delegated settings,
delegated mobile-feature controls, tenant reports, and tenant support workflows
only inside their tenant; platform-only controls, cross-tenant visibility,
global policy, billing authority, lifecycle authority, app-version policy, and
security posture remain Admin/API-owned boundaries.

Multi-Tenant Mobile Logic is defined in `../docs/multi-tenant-mobile-logic.md`:
users with more than one tenant choose and remember tenant context through
API-confirmed state; tenant switching, tenant-scoped cache, per-tenant
permissions and feature flags, sync replay, offline behavior, and logout
cleanup must preserve tenant isolation and never turn mobile-local state
into tenant authority.

Offline-First Principles are defined in `../docs/offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

Offline UX Logic is defined in `../docs/offline-ux-logic.md`:
offline UX must calmly explain banners, disabled online-only actions,
local drafts, pending indicators, retry, sync success or failure,
saved-local versus synced state, and data-loss prevention whenever
connection changes.

Records/Content Module Logic is defined in `../docs/records-content-module-logic.md`:
records are tenant-scoped business content with API-owned lifecycle,
notes, attachments, activity, tags, categories, status, offline draft or
sync behavior, admin controls, permissions, feature flags, audit, and
reporting boundaries.

Search Logic is defined in `../docs/search-logic.md`:
search stays tenant-scoped, permission-aware, feature-controlled,
privacy-preserving, and explicit about local-cache limits versus
API-authoritative results, including recent searches, saved filters, filtering,
sorting, scan-to-search, offline limits, and admin-controlled boundaries.

Forms And Drafts Logic is defined in `../docs/forms-drafts-logic.md`:
mobile forms must stay simple, validated, autosave-aware, offline-draft
safe, API-submitted, admin-controlled, and explicit about local-save versus
server-accepted state so user work is protected without bypassing authority.

Notifications Logic is defined in `../docs/notifications-logic.md`:
notification targeting, delivery policy, push behavior, in-app inbox,
read/unread state, deep links, preferences, offline behavior, and tenant or
permission boundaries must remain Admin/API-authoritative and mobile-safe.

Support System Logic is defined in `../docs/support-system-logic.md`:
support requests, support messages, attachments, tenant context, support-agent
visibility, audit, notifications, and offline support drafts must remain
tenant-scoped, least-privilege, privacy-safe, and Admin/API-authoritative.

Billing And Plan Logic is defined in `../docs/billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature states,
and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

Reporting Logic is defined in `../docs/reporting-logic.md`:
admin measurements, tenant-admin measurements, mobile-visible summaries,
privacy boundaries, date ranges, exports, feature usage, sync health,
notification, support, and billing reports must remain tenant-scoped,
permission-aware, privacy-safe, auditable, and Admin/API-authoritative.

Native Feature Strategy is defined in `../docs/native-feature-strategy.md`:
NativePHP capability use, logical service boundaries, browser/development
fallbacks, permission education, admin feature-flag control, native failure
UX, and offline sync behavior must remain feature-scoped, tenant-safe,
privacy-aware, fallback-safe, and Admin/API-authoritative.

Camera And Media Logic is defined in `../docs/camera-media-logic.md`:
photo capture, media selection, media preview, record/support attachments,
offline media storage, upload queues, feature-flag control, permission
denial, size limits, and privacy behavior must remain tenant-scoped,
permission-aware, fallback-safe, queue-safe, privacy-safe, and
Admin/API-authoritative.

Scanner Logic is defined in `../docs/scanner-logic.md`:
QR/barcode scan-to-search, scan-to-create, scan-to-validate, scan history,
offline scanning, invalid scan handling, duplicate scan handling, admin
feature flags, and camera/permission dependency behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear,
duplicate-safe, privacy-safe, and Admin/API-authoritative.

Geolocation Logic is defined in `../docs/geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Device, Network, And Diagnostics Logic is defined in `../docs/device-network-diagnostics-logic.md`:
device information use, network status use, offline detection, diagnostics
export, support troubleshooting context, diagnostics redaction, admin mobile
device visibility, and user-controlled diagnostics sharing must remain
tenant-scoped, permission-aware, support-scoped, privacy-safe, redacted,
audit-ready, and Admin/API-authoritative.

Module Selection Principles are defined in `../docs/module-selection-principles.md`:
optional industry modules such as field service, logistics, ecommerce,
booking, education, events, support, community/messaging, reports, and AI
assistant must be tenant-enabled, plan-controlled, permission-aware,
mobile-hidden when unavailable, feature-flag-safe, documented before
implementation, and Admin/API-authoritative.

Field Service Logic is defined in `../docs/field-service-logic.md`:
work order lifecycle, technician mobile flow, check-in/check-out, photos,
notes, future signatures, offline behavior, admin dispatch/control, and
report visibility must remain tenant-enabled, plan-controlled,
permission-aware, offline-clear, evidence-safe, privacy-safe, auditable,
and Admin/API-authoritative.

Booking Logic is defined in `../docs/booking-logic.md`:
service selection, availability logic, booking requests, confirmation,
cancellation, reschedule, reminders, admin schedule control, tenant rules,
and mobile offline limitations must remain tenant-enabled, plan-controlled,
permission-aware, availability-safe, schedule-conflict-safe, reminder-safe,
offline-limited, privacy-safe, auditable, and Admin/API-authoritative.

Commerce Logic is defined in `../docs/commerce-logic.md`:
catalog browsing, cart behavior, checkout principles, hosted payment
boundaries, order lifecycle, invoice/receipt principles, subscription
upsell, admin product/control, and mobile offline limitations must remain
tenant-enabled, plan-controlled, permission-aware, price-safe,
inventory-safe, hosted-payment-safe, offline-limited, privacy-safe,
auditable, and Admin/API-authoritative.

Logistics Delivery Logic is defined in `../docs/logistics-delivery-logic.md`:
delivery job lifecycle, pickup flow, drop-off flow, proof of delivery,
scan validation, location check-in, failed delivery reasons, offline
behavior, and admin monitoring must remain tenant-enabled, plan-controlled,
permission-aware, scan-safe, location-purpose-limited, offline-clear,
privacy-safe, auditable, and Admin/API-authoritative.

Voice Note Logic is defined in `../docs/voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

Sync Lifecycle Logic is defined in `../docs/sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `../docs/conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

## Systems

| Path | Responsibility | Current state |
| --- | --- | --- |
| `apps/api-admin` | Laravel API plus Livewire admin panel. This is the SaaS control plane and source of authority. | Laravel app scaffold exists with Livewire dashboard shell, versioned status API, shared response envelope, tests, and build verification. |
| `apps/mobile-client` | Laravel plus Livewire inside NativePHP Mobile. This is the managed edge client. | Complete mobile app exists with Livewire routes, NativePHP config, local SQLite infrastructure, copied mobile UI, tests, and build verification. |

The product contract remains unchanged:

- Admin/API owns tenant, user, permission, feature, config, version, billing,
  notification, support, reporting, audit, conflict, and security authority.
- Admin Control Center logic in `../docs/admin-control-center-logic.md`
  defines how tenant, user, role, permission, feature, config, version,
  maintenance, force update, sync, notification, report, billing, and support
  controls are scoped, authorized, audited, and sent to mobile through API
  outcomes.
- Feature Flag Logic in `../docs/feature-flag-logic.md` defines how important
  mobile features resolve global, tenant, plan, role, permission, user,
  app-version, device, cohort, maintenance, and emergency decisions into
  mobile-safe states.
- Remote Configuration Logic in `../docs/remote-configuration-logic.md`
  defines how safe runtime mobile behavior is remotely configured, delivered
  through API, cached locally, handled offline, overridden per tenant, and
  failed closed when missing or invalid.
- Mobile Version Control Logic in
  `../docs/mobile-version-control-logic.md` defines how minimum supported
  versions, optional updates, forced updates, maintenance mode, store links,
  update messages, and stale-client protection move through API outcomes.
- Admin Safety Principles in `../docs/admin-safety-principles.md` define how
  dangerous admin actions are confirmed, audited, impact-previewed,
  mobile-previewed, rollback-aware, and tenant-isolated before they affect
  either app.
- Mobile UX Principles in `../docs/mobile-ux-principles.md` define how the
  NativePHP client presents navigation, simple screens, loading/offline states,
  thumb-friendly controls, fast actions, secure sessions, feature visibility,
  and native permission education.
- Mobile owns local execution, NativePHP capability UX, cache, drafts, queues,
  sync display, and API-derived feature visibility.
- Mobile never reads the admin database directly. Server-trusted behavior must
  move through versioned API contracts.

## Transition Rule

The root Laravel app is retained as a transition mirror. New control-plane code
belongs in `apps/api-admin`. New mobile-client code should target
`apps/mobile-client` unless a later cleanup task explicitly removes or rewires
the root app.
