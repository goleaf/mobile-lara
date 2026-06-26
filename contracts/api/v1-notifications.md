# API v1 Notifications Contract

Updated: 2026-06-26

Status: partially implemented. Bootstrap now returns resolved tenant
notification preferences, quiet-hours metadata, push-registration hints,
fail-closed no-tenant behavior, and notification policy version metadata.
Inbox, push token registration/revocation, read state, delete actions, deep
links, and delivery/open tracking remain planned for Phase 21.

Product Vision is defined in `../../docs/product-vision.md`: this contract
keeps notification orchestration centralized while mobile handles device
registration and user-facing delivery state.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the combined platform by keeping notification policy
centralized while mobile presents safe user-facing delivery behavior.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must keep registration,
preferences, inbox state, delivery feedback, mobile errors, and tenant-safe
notification visibility API-shaped and predictable.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: notification behavior must
document admin mobile effect, device permission UX, API dependency, offline
display expectations, permission owner, delivery/support risks, and audit needs
before implementation.

Target User Roles are defined in `../../docs/user-roles.md`: notification
preferences, delivery state, and device registration must respect role and
account-state visibility.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: notification
contracts create value when platform, tenant, mobile, support, and billing
messages reach the right audience without leaking tenant or admin authority.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: notification targeting and delivery truth
stay in Admin/API while mobile owns device registration UX, permission UX, and
safe local display.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to
notification orchestration, users and permissions, tenant management,
feature/version/billing gates, support/reporting visibility, and audit history.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports native
notification permission UX, push-token registration feedback, inbox
presentation, unread status, local display state, and feature-gated
notification visibility.

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
`../../docs/admin-control-center-logic.md`: this contract must keep
notification templates, channels, targeting, quiet hours, priority, escalation,
suppression, and delivery visibility scoped, authorized, auditable, and exposed
to mobile only as resolved API outcomes.

Feature Flag Logic is defined in `../../docs/feature-flag-logic.md`:
notification inbox, push registration, deep links, channel visibility,
targeting affordances, and notification-related mobile features must follow
resolved flag state, rollout, plan, permission, version, and disabled-state
rules.

Remote Configuration Logic is defined in
`../../docs/remote-configuration-logic.md`: notification preference labels,
permission prompts, quiet-hour copy, local display guidance, deep-link copy,
and support explanations may be tuned by resolved config while targeting and
delivery truth remain Admin/API authority.

## Purpose

Notification endpoints manage notification preferences, push token
registration, token revocation, inbox state, unread count, mark-read actions,
deletes, and deep-link payloads.

## Planned Routes

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/notifications` | List notification inbox items. | mobile token |
| POST | `/api/v1/mobile/notifications/push-tokens` | Register a push token. | mobile token |
| DELETE | `/api/v1/mobile/notifications/push-tokens/{token}` | Revoke a push token. | mobile token |
| PATCH | `/api/v1/mobile/notifications/{notification}/read` | Mark a notification read. | mobile token |

## Success Data

Responses return notification `id`, `type`, `title`, `body`, `read_at`,
`deep_link`, `created_at`, `actions`, and `unread_count` where useful.

Bootstrap currently returns `notification_preferences` with `push_enabled`,
`in_app_enabled`, `email_enabled`, `quiet_hours`,
`push_registration_required`, and `status`. `unread_notification_count` remains
`0` until server-side inbox storage is implemented.

## Gates

Notifications are controlled by tenant membership, user preferences, feature
flags, push permission, app version, maintenance, billing state, and support
policy.

## Offline Behavior

Mobile may cache inbox items and read state locally. Mutations queued offline
must sync before server counts are trusted.

## Audit

Audit push token registration/revocation, campaign send placeholders,
notification open tracking, read-state changes, and delete actions where
required.

## Tests

Current preference coverage:

```bash
cd apps/api-admin && php artisan test --compact --filter=MobileNotificationPolicyTest
```

Future Phase 21 coverage should verify token ownership, tenant isolation,
unread counts, read/delete actions, delivery/open tracking, and deep-link
safety.
