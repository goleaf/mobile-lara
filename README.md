# Mobile Lara

Mobile Lara is a planned SaaS platform for centrally managed NativePHP mobile applications. Its product vision is remote control with local resilience: administrators govern mobile behavior centrally, while mobile users keep working through a focused NativePHP client.

The product is positioned as a tenant-based SaaS control center with an API-first, feature-controlled, offline-capable mobile workforce/client platform.

The core principle is strict: Admin/API controls business authority; mobile never bypasses the API; every feature is controllable; tenant isolation, security, documentation, and modular expansion are default requirements.

The role model is explicit: platform owner, super admin, tenant admin, tenant manager, support agent, billing manager, mobile user, invited user, suspended user, and guest/pre-login user each have different responsibilities, visibility, and control boundaries.

The role and permission model is explicit: platform, tenant, admin-user, and mobile-user permissions are resolved by Admin/API before API access or mobile UI visibility, feature flags remain separate availability gates, and suspended users or tenants fail closed.

The audit model is explicit: admin actions, security events, mobile activity summaries, API decisions, sync outcomes, support actions, and compliance-relevant changes produce protected audit history that explains who did what, where it applied, why it happened, and what changed.

The value map is explicit too: platform owner, tenant business, tenant admin, mobile worker/client, support team, and billing/operations team each receive different value from admin control, mobile access, offline sync, notifications, reports, security, and feature flags.

The two-system boundary is explicit: Admin/API owns SaaS authority, while mobile owns local execution, native capability use, cache, drafts, queues, and clear state presentation.

The Admin/API responsibility model is explicit: tenant management, users and permissions, admin panel operations, API contracts, feature control, remote configuration, mobile version rules, notifications, billing/subscription logic, support, reporting, audit, conflict decisions, and security enforcement belong to the control plane.

The mobile-client responsibility model is explicit: mobile user experience, secure local session, local cache, offline actions, NativePHP device features, mobile navigation, mobile permissions UX, sync status display, local drafts, local user feedback, and feature visibility based on admin rules belong to the mobile client.

The mobile UX model is explicit: the NativePHP client uses mobile-first navigation, simple screens, clear loading/offline states, thumb-friendly controls, minimum typing, fast actions, admin-rule-based feature visibility, secure session behavior, and native permission education.

The mobile app shell model is explicit: the NativePHP client coordinates welcome, authenticated, locked, offline, maintenance, forced update, tenant switching, sync-in-progress, permission-blocked, and feature-disabled states without taking authority from the API.

The mobile dashboard model is explicit: the NativePHP client shows current user context, current tenant, enabled feature shortcuts, sync/offline status, unread notifications, recent activity, announcements, and quick actions through API-safe rules.

The mobile settings model is explicit: the NativePHP client groups account, tenant, security, notifications, sync, appearance, permissions, storage, support, legal, and diagnostics settings while separating local device control from Admin/API authority and offline-disabled behavior.

The mobile permission model is explicit: camera, microphone, location, notifications, files, scanner, biometrics, and secure storage requests must be explained before prompting, controlled by feature flags and API authority, skipped for disabled features, recoverable after denial, and visible in settings.

The authentication model is explicit: mobile login happens through the API only, tokens live in secure storage, refresh/logout/logout-all-devices/tenant selection/session expiry/offline authenticated behavior/server revocation stay under Admin/API authority, and mobile presents only safe local session state.

The mobile app lock model is explicit: local biometric or PIN unlock protects private cached data, sensitive areas, offline drafts, and app resume behavior, but never replaces API login, authorization, tenant access, billing authority, feature authority, or server revocation.

The API-first model is explicit: mobile communicates only with API, API responses are predictable, every mobile feature has a clear API purpose, operating context is returned through API, errors are mobile-friendly, sync/conflict behavior is first-class, and tenant boundaries are protected server-side.

The documentation-first architecture model is explicit: every feature, admin control, mobile screen, sync behavior, permission, and risk must be documented before implementation.

The Admin Control Center model is explicit: admins control tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance mode, force update, sync behavior, notifications, reports, billing, and support through scoped, authorized, auditable server-side controls.

The feature flag model is explicit: important mobile features are controlled by global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, and emergency decisions that resolve into mobile-safe states through the API.

The remote configuration model is explicit: Admin/API controls safe runtime mobile behavior through versioned, scoped, validated config that mobile receives through API, caches carefully, and treats as non-authoritative when stale or invalid.

The mobile version control model is explicit: Admin/API controls minimum supported versions, optional updates, forced updates, maintenance mode, store links, update messages, and old-version protection through mobile-safe API outcomes.

The admin safety model is explicit: dangerous admin actions require confirmation, audit history, impact preview, mobile impact preview, rollback thinking, and tenant-isolated scope before they affect users.

The product solves a common business problem: mobile teams need a simple app, but the organization needs tenant-safe control over permissions, billing, feature availability, app versions, support, notifications, reports, and sync behavior without publishing a new mobile build for every policy change.

The product is split into two cooperating systems:

1. **Admin/API system** - Laravel API plus a Livewire admin panel. This is the SaaS control plane.
2. **Mobile client system** - Laravel plus Livewire running inside NativePHP Mobile. This is the managed edge client.

The core idea is simple: admin users operate the control plane, while mobile users work in a controlled mobile client that receives all business rules, permissions, feature availability, app-version policy, notifications, sync behavior, support state, and billing entitlement through the API.

## Product Position

Mobile Lara is not just a mobile app starter. It is a control-plane product for businesses that need mobile workflows they can govern remotely.

Admin users include SaaS owners, platform operators, tenant owners, tenant admins, support users, billing operators, product/release managers, and security or compliance reviewers. Mobile users are the frontline or tenant-side people who perform work in the app and should not need to understand feature flags, billing rules, rollout cohorts, or sync policy internals.

Mobile Lara is better than building only a web app because mobile workers need native capability access, offline-capable workflows, local sync state, and mobile-first ergonomics. It is better than building only a mobile app because a SaaS business needs tenant administration, billing enforcement, feature rollout, app-version policy, support visibility, reports, and audit trails.

The admin/API system owns:

- Tenants, teams, users, roles, permissions, and device trust.
- Remote config, feature flags, app-version requirements, and rollout rules.
- Notification policy, support workflow, reports, billing plans, and usage limits.
- API contracts, audit trails, sync policy, conflict handling, and operational controls.

The mobile client owns:

- Local mobile UX, Livewire screens, NativePHP capability bridges, and device permissions.
- Offline-first local state, queued actions, local records, local media metadata, and sync status.
- Safe presentation of admin-controlled capabilities without inventing its own product rules.

## Product Principle

The admin system is the source of authority. The mobile client is a resilient local executor.

If a capability is disabled, unlicensed, blocked by version policy, denied by permission, or outside tenant scope, the mobile client must treat that as final even if local UI state still contains stale cached data.

## Documentation Map

| Document | Purpose |
| --- | --- |
| [docs/product-vision.md](docs/product-vision.md) | Plain-language product vision, users, problem, technology choice, and SaaS scale logic. |
| [docs/product-positioning.md](docs/product-positioning.md) | Product positioning as SaaS control center, mobile client platform, API-first system, offline-capable system, feature-controlled platform, and tenant-based product. |
| [docs/product-principles.md](docs/product-principles.md) | Core product principles for admin control, API-first mobile behavior, feature control, tenant isolation, offline use, security, documentation, and modular expansion. |
| [docs/documentation-first-architecture.md](docs/documentation-first-architecture.md) | Documentation-first architecture principles for feature docs, admin mobile effects, screen API dependencies, sync behavior, permission ownership, risks, and acceptance criteria. |
| [docs/user-roles.md](docs/user-roles.md) | Main logical user roles, responsibilities, limitations, visibility, and control boundaries. |
| [docs/role-permission-logic.md](docs/role-permission-logic.md) | Role and permission logic for platform-level, tenant-level, admin-user, and mobile-user permissions, API access, mobile UI visibility, feature flag interaction, and suspended users or tenants. |
| [docs/audit-logic.md](docs/audit-logic.md) | Audit logic for admin actions, security events, support/compliance history, mobile activity representation, audit questions, and audit data protection. |
| [docs/saas-value-map.md](docs/saas-value-map.md) | SaaS value map connecting stakeholders to admin control, mobile access, offline sync, notifications, reports, security, and feature flags. |
| [docs/two-system-boundary.md](docs/two-system-boundary.md) | Logical boundary between Admin/API authority and mobile-client execution, caching, API-only behavior, remote control, and offline behavior. |
| [docs/api-first-principles.md](docs/api-first-principles.md) | API-first principles for mobile/API communication, predictable responses, context payloads, mobile-friendly errors, sync/conflict behavior, and tenant protection. |
| [docs/admin-api-responsibilities.md](docs/admin-api-responsibilities.md) | Admin/API responsibility map for tenant management, users and permissions, API contracts, feature/config/version control, notifications, billing, support, reports, audit, conflicts, and security. |
| [docs/mobile-client-responsibilities.md](docs/mobile-client-responsibilities.md) | Mobile-client responsibility map for UX, secure local session, cache, offline actions, NativePHP capabilities, navigation, permissions UX, sync display, drafts, feedback, and feature visibility. |
| [docs/mobile-ux-principles.md](docs/mobile-ux-principles.md) | Mobile UX principles for NativePHP navigation, simple screens, loading/offline states, thumb-friendly controls, minimum typing, fast actions, feature visibility, secure sessions, and native permission education. |
| [docs/mobile-app-shell-logic.md](docs/mobile-app-shell-logic.md) | Mobile app shell logic for welcome, authenticated, locked, offline, maintenance, forced update, tenant switching, sync-in-progress, permission-blocked, and feature-disabled states. |
| [docs/mobile-dashboard-logic.md](docs/mobile-dashboard-logic.md) | Mobile dashboard logic for user/tenant context, feature shortcuts, sync/offline state, notifications, activity, announcements, and quick actions. |
| [docs/mobile-settings-logic.md](docs/mobile-settings-logic.md) | Mobile settings logic for account, tenant, security, notifications, sync, appearance, permissions, storage, support, legal, diagnostics, local control, admin/API control, and offline-disabled behavior. |
| [docs/mobile-permission-logic.md](docs/mobile-permission-logic.md) | Mobile permission logic for pre-prompt education, camera, microphone, location, notifications, files, scanner, biometrics, secure storage, denied-permission recovery, feature flag effects, and settings status. |
| [docs/authentication-principles.md](docs/authentication-principles.md) | Authentication principles for API-only mobile login, secure token handling, refresh sessions, logout, logout-all-devices, tenant selection, session expiry, offline authenticated behavior, and server revocation. |
| [docs/mobile-app-lock-principles.md](docs/mobile-app-lock-principles.md) | Mobile app lock principles for lock timing, sensitive-area confirmation, biometric unlock, PIN unlock, repeated failed attempts, logout behavior, admin-disabled biometrics, and offline cached-data protection. |
| [docs/admin-control-center-logic.md](docs/admin-control-center-logic.md) | Admin Control Center logic for tenant, user, role, permission, feature, config, version, maintenance, force update, sync, notification, report, billing, and support controls. |
| [docs/feature-flag-logic.md](docs/feature-flag-logic.md) | Feature flag logic for important mobile features, global/tenant/user priority, disabled mobile states, admin impact, safe rollout, and plan limits. |
| [docs/remote-configuration-logic.md](docs/remote-configuration-logic.md) | Remote configuration logic for configurable behavior, mobile receive/cache rules, offline behavior, tenant overrides, safe admin changes, and missing/invalid config. |
| [docs/mobile-version-control-logic.md](docs/mobile-version-control-logic.md) | Mobile version control logic for minimum supported versions, optional updates, forced updates, maintenance mode, outdated app behavior, store links, update messages, and old-version protection. |
| [docs/admin-safety-principles.md](docs/admin-safety-principles.md) | Admin safety principles for dangerous actions, confirmations, audit history, impact previews, mobile impact previews, rollback, and tenant-isolated changes. |
| [docs/saas-mobile-admin-platform.md](docs/saas-mobile-admin-platform.md) | Canonical product and system concept. |
| [docs/decisions/0001-admin-api-control-plane-and-native-mobile-client.md](docs/decisions/0001-admin-api-control-plane-and-native-mobile-client.md) | ADR for the two-system architecture. |
| [docs/mobile-stack.md](docs/mobile-stack.md) | Stack, package, and boundary notes. |
| [docs/mobile-app-audit.md](docs/mobile-app-audit.md) | Current-state audit against the target concept. |
| [docs/nativephp-local-storage.md](docs/nativephp-local-storage.md) | Offline-first local SQLite and sync principles. |
| [docs/nativephp-run.md](docs/nativephp-run.md) | NativePHP run, release, and app-version operating notes. |
| [docs/design-system.md](docs/design-system.md) | Mobile and admin UX principles. |
| [docs/implementation-status.md](docs/implementation-status.md) | Current implementation checklist grouped by phase and feature area. |
| [docs/remaining-tasks.md](docs/remaining-tasks.md) | Active remaining work, external blockers, and future enhancements. |
| [contracts/api](contracts/api) | Versioned mobile API contract documents and implemented contract catalogue. |
| [apps](apps) | Target monorepo boundary for `api-admin` and `mobile-client`. |
| [AGENTS.md](AGENTS.md) / [CLAUDE.md](CLAUDE.md) | Agent-facing project rules. |

## Current Technical Baseline

- PHP 8.5.
- Laravel 13.
- Livewire 4.
- NativePHP Mobile 3.
- SQLite for current local development and mobile-local storage.
- Tailwind CSS 4 through the SCSS/PostCSS bridge.
- Pest 4 for tests.

The repository now contains separate Laravel applications under
`apps/api-admin` and `apps/mobile-client`. The root Laravel app is retained as a
temporary mobile-client transition mirror until a later cleanup task removes or
rewires it. `contracts/api` remains the home for versioned mobile API
contracts.

## Operating Rules

- Use Eloquent and Laravel resources for API-facing data. Do not use raw SQL strings.
- Apply [core product principles](docs/product-principles.md) before feature implementation.
- Apply [documentation-first architecture](docs/documentation-first-architecture.md) before coding any feature, admin control, mobile screen, sync behavior, permission, or risk-sensitive change.
- Apply [target user roles](docs/user-roles.md) before designing permissions, visibility, support, billing, or mobile access.
- Apply [role and permission logic](docs/role-permission-logic.md) before planning platform-level permissions, tenant-level permissions, admin-user permissions, mobile-user permissions, API access, mobile UI visibility, feature flag interaction, suspended users, or suspended tenants.
- Apply [audit logic](docs/audit-logic.md) before planning admin actions, security events, support actions, mobile activity summaries, API decisions, sync outcomes, compliance-relevant changes, audit history views, or audit exports.
- Apply the [SaaS value map](docs/saas-value-map.md) before prioritizing features, reports, notifications, offline sync, security controls, billing logic, or feature flags.
- Apply the [two-system boundary](docs/two-system-boundary.md) before deciding what belongs in Admin/API, what belongs in mobile, what must go through API, and what can be cached locally.
- Apply [API-first principles](docs/api-first-principles.md) before planning mobile/API contracts, boot context, mobile feature purpose, API errors, sync replay, conflict behavior, or tenant-scoped responses.
- Apply [Admin/API responsibilities](docs/admin-api-responsibilities.md) before planning control-plane behavior, API contracts, admin panels, reports, support, billing, notifications, audit, conflict, or security work.
- Apply [mobile-client responsibilities](docs/mobile-client-responsibilities.md) before planning mobile UX, secure local session, local cache, offline actions, NativePHP capability use, navigation, mobile permissions UX, sync status, drafts, local feedback, or feature visibility.
- Apply [mobile UX principles](docs/mobile-ux-principles.md) before planning NativePHP navigation, loading/offline states, thumb-friendly controls, data entry, fast actions, secure session behavior, or native permission prompts.
- Apply [mobile app shell logic](docs/mobile-app-shell-logic.md) before planning welcome, authenticated, locked, offline, maintenance, forced update, tenant switching, sync-in-progress, permission-blocked, or feature-disabled app-shell behavior.
- Apply [mobile dashboard logic](docs/mobile-dashboard-logic.md) before planning current user context, current tenant, feature shortcuts, sync/offline state, unread notifications, recent activity, announcements, or quick actions.
- Apply [mobile settings logic](docs/mobile-settings-logic.md) before planning account, tenant, security, notifications, sync, appearance, permissions, storage, support, legal, diagnostics, local controls, admin/API controls, or offline-disabled settings behavior.
- Apply [mobile permission logic](docs/mobile-permission-logic.md) before planning native permission prompts, camera, microphone, location, notifications, files, scanner, biometrics, secure storage, denied-permission recovery, feature flag effects, or settings permission status.
- Apply [authentication principles](docs/authentication-principles.md) before planning mobile login, token handling, refresh sessions, logout, logout-all-devices, tenant selection after login, session expiry, offline authenticated behavior, or server revocation.
- Apply [mobile app lock principles](docs/mobile-app-lock-principles.md) before planning lock timing, biometric unlock, PIN unlock, sensitive-area confirmation, failed-attempt behavior, logout cleanup, admin-disabled biometrics, or offline cached-data protection.
- Apply [Admin Control Center logic](docs/admin-control-center-logic.md) before planning admin controls, remote config, app-version policy, maintenance, force update, sync policy, notifications, reports, billing, or support workflows.
- Apply [feature flag logic](docs/feature-flag-logic.md) before planning important mobile features, flag priority, disabled mobile states, rollout, rollback, or plan-limited access.
- Apply [remote configuration logic](docs/remote-configuration-logic.md) before planning runtime-configurable mobile behavior, config caching, offline config use, tenant-specific overrides, safe admin config changes, or missing/invalid config handling.
- Apply [mobile version control logic](docs/mobile-version-control-logic.md) before planning minimum supported versions, optional update prompts, forced updates, maintenance mode, outdated-client responses, store links, update messages, or old-version protection.
- Apply [admin safety principles](docs/admin-safety-principles.md) before planning dangerous admin actions, confirmations, audit history, impact previews, mobile impact previews, rollback, or tenant-specific changes.
- Keep admin business rules on the server. Mobile UI state is never an authorization boundary.
- Let admin settings control mobile behavior because mobile state may be stale, offline, copied between devices, or running an old app version.
- Position the product as both admin control center and mobile workforce/client platform; avoid web-only or mobile-only thinking.
- Treat NativePHP secure storage as the home for secrets and tokens. Do not store secrets in local SQLite.
- Treat local SQLite as a cache, queue, draft, and offline-working database.
- Make every mobile action idempotent at the API boundary.
- Version every API behavior that the mobile app depends on.
- Prefer feature flags and remote config for rollout control, not hardcoded app decisions.
- Keep NativePHP + Livewire as the mobile approach unless a future ADR changes the product direction.

## Common Commands

```bash
composer install
npm install
npm run build
php artisan test --compact
php artisan native:debug --no-interaction
php artisan native:plugin:validate
```

Laravel Herd serves the local app at the project test domain. Use Laravel Boost's `get-absolute-url` MCP tool before sharing URLs.

## Non-Goals For This Documentation Commit

- No application logic was implemented.
- No schema, migrations, or database fields were created.
- No admin resources, API controllers, policies, or Livewire components were added.
- No billing provider, push provider, or external service was integrated.

This repository should move from concept to implementation through explicit product slices, each with tests, migrations, authorization, API contracts, and admin/mobile acceptance criteria.
