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
