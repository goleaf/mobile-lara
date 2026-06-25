# API v1 Reports Contract

Updated: 2026-06-26

Status: documented. Endpoint is planned for Phase 25.

Product Vision is defined in `../../docs/product-vision.md`: this contract
supports scalable SaaS reporting while protecting tenant and role boundaries on
mobile.

Product Positioning is defined in `../../docs/product-positioning.md`: this
contract supports the SaaS control center by exposing only mobile-safe report
summaries from tenant-scoped authority.

Core Product Principles are defined in `../../docs/product-principles.md`: this
contract must preserve admin control, API-first communication, tenant isolation,
secure defaults, simple mobile UX, and modular feature expansion.

API-First Principles are defined in
`../../docs/api-first-principles.md`: this contract must return predictable
report purpose, scoped summaries, freshness metadata, mobile-friendly errors,
and tenant-safe visibility without exposing report/export authority.

Documentation-First Architecture is defined in
`../../docs/documentation-first-architecture.md`: report behavior must document
stakeholder purpose, mobile screen dependency, API context, cache/freshness
behavior, permission owner, export boundary, privacy risks, and audit needs
before implementation.

Target User Roles are defined in `../../docs/user-roles.md`: report summaries
must respect platform, tenant, manager, support, billing, mobile, and blocked
account-state visibility.

SaaS Value Map is defined in `../../docs/saas-value-map.md`: report contracts
must prove value through scoped adoption, operations, sync, support, billing,
security, and feature-usage insight without overexposing raw tenant data.

Two-System Boundary Logic is defined in
`../../docs/two-system-boundary.md`: report definitions, scope, aggregation,
and export authority stay in Admin/API while mobile receives only allowed
summaries.

Admin Safety Principles are defined in
`../../docs/admin-safety-principles.md`: dangerous admin actions behind this
contract must be confirmed, audited, impact-previewed, mobile-previewed,
rollback-aware, and tenant-isolated before implementation.

Admin/API Responsibilities are defined in
`../../docs/admin-api-responsibilities.md`: this contract belongs to reporting,
tenant management, users and permissions, billing/support visibility, API
contracts, audit history, and security enforcement.

Mobile Client Responsibilities are defined in
`../../docs/mobile-client-responsibilities.md`: this contract supports
mobile-safe report summaries, loading/empty/error states, cache freshness,
navigation visibility, and local feedback without giving mobile report or
export authority.

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

Admin Control Center Logic is defined in
`../../docs/admin-control-center-logic.md`: this contract must keep report
definitions, scopes, aggregates, exports, dashboard visibility, operational
metrics, and cross-tenant safeguards scoped, authorized, auditable, and exposed
to mobile only as resolved API outcomes.

## Purpose

Reports endpoints expose only permission-safe tenant and user report summaries
to mobile. Admin/API owns report definitions, aggregations, export authority,
and tenant boundaries.

## Planned Route

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/api/v1/mobile/reports` | Return allowed report summaries. | mobile token |

## Success Data

The response returns `reports`, `filters`, `generated_at`, `freshness`,
`allowed_exports`, and `limited_by` where feature, permission, or plan limits
apply.

## Gates

Reports are controlled by tenant membership, report permissions, feature flags,
remote config, subscription status, app version, maintenance, and export
policy.

## Offline Behavior

Mobile may cache read-only summaries with freshness labels. It cannot create
trusted report exports while offline.

## Audit

Audit report access, export requests, denied report access, and support/admin
report viewing where relevant.

## Tests

Phase 25 should verify tenant isolation, permission filtering, feature/plan
limits, export denial, and cached summary freshness.
