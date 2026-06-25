<project-product-contract>

# Mobile Lara Product Contract

This repository is the planning and implementation home for a two-system SaaS mobile platform. The product vision is remote control with local resilience: administrators govern mobile behavior centrally, while mobile users keep working through a focused NativePHP client.

The system solves the problem of mobile workflows that need centralized tenant, permission, billing, version, support, notification, report, and sync control without requiring a new mobile release for every product or policy change.

Position the product as a tenant-based SaaS control center with an API-first, feature-controlled, offline-capable mobile workforce/client platform. It is intentionally stronger than web-only because it supports native mobile and offline work, and stronger than mobile-only because SaaS authority, tenant governance, billing, support, reports, and audit live centrally.

Core product principles: Admin/API controls business authority, mobile never bypasses API, every feature can be enabled or disabled, tenant isolation is mandatory, offline-first is used only where useful, security is default, communication is API-first, mobile UX stays simple, documentation comes before implementation, and features expand as modular slices.

Target user roles are defined in `docs/user-roles.md`: platform owner, super admin, tenant admin, tenant manager, support agent, billing manager, mobile user, invited user, suspended user, and guest/pre-login user. Treat invited/suspended/pre-login as access states that override normal role permissions.

Role And Permission Logic is defined in `docs/role-permission-logic.md`: platform, tenant, admin-user, and mobile-user permissions must be resolved by Admin/API before API access or mobile UI visibility; permissions interact with feature flags as separate gates; suspended users and suspended tenants fail closed without bypassing tenant isolation.

Audit Logic is defined in `docs/audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

The SaaS value map is defined in `docs/saas-value-map.md`: platform owner, tenant business, tenant admin, mobile worker/client, support team, and billing/operations team receive different value from admin control, mobile access, offline sync, notifications, reports, security, and feature flags.

The two-system boundary is defined in `docs/two-system-boundary.md`: Admin/API owns SaaS authority and mobile owns local execution, native capability use, cache, drafts, queues, and state presentation.

Admin/API responsibilities are defined in `docs/admin-api-responsibilities.md`: tenant management, users and permissions, admin panel operations, API contracts, feature control, remote configuration, mobile version rules, notification orchestration, billing/subscription logic, support operations, reporting, audit history, conflict decisions, and security enforcement belong to the control plane.

Mobile client responsibilities are defined in `docs/mobile-client-responsibilities.md`: mobile user experience, secure local session, local cache, offline actions, NativePHP device features, mobile navigation, mobile permissions UX, sync status display, local drafts, local user feedback, and feature visibility based on admin rules belong to the mobile client.

Mobile app shell logic is defined in `docs/mobile-app-shell-logic.md`: welcome, authenticated, locked, offline, maintenance, forced update, tenant switching, sync-in-progress, permission-blocked, and feature-disabled states must be coordinated by the NativePHP client without bypassing API authority.

Mobile dashboard logic is defined in `docs/mobile-dashboard-logic.md`: current user context, current tenant, enabled feature shortcuts, sync status, offline status, unread notifications, recent activity, important announcements, and quick actions must resolve through permissions, feature flags, remote config, tenant status, offline state, and subscription status before implementation.

Mobile settings logic is defined in `docs/mobile-settings-logic.md`: account, tenant, security, notifications, sync, appearance, permissions, storage, support, legal, and diagnostics settings must separate local device control, Admin/API authority, and offline-disabled behavior before implementation.

Mobile permission logic is defined in `docs/mobile-permission-logic.md`: camera, microphone, location, notifications, files, scanner, biometrics, and secure-storage behavior must explain purpose before prompting, respect feature flags and API authority, avoid disabled-feature prompts, support denied-permission recovery, and show status in settings before implementation.

Authentication principles are defined in `docs/authentication-principles.md`: mobile login must happen through the API only; access and refresh tokens must use secure storage; refresh, logout, logout-all-devices, tenant selection, session expiry, offline already-authenticated behavior, and server revocation must preserve Admin/API authority before implementation.

Mobile app lock principles are defined in `docs/mobile-app-lock-principles.md`: local biometric or PIN unlock protects private cached data, sensitive areas, offline drafts, and app resume behavior, but never replaces API login, authorization, tenant access, billing authority, feature authority, or server revocation.

API-first principles are defined in `docs/api-first-principles.md`: mobile communicates only with API, API responses are predictable, every mobile feature has a clear API purpose, API returns operating context, errors are mobile-friendly, sync and conflict behavior are first-class, and tenant boundaries are protected server-side.

Documentation-first architecture is defined in `docs/documentation-first-architecture.md`: every feature must be documented before implementation, every admin control must document its mobile effect, every mobile screen must document its API dependency, every sync behavior must document offline and online behavior, every permission must document who controls it, and every risk must be recorded before coding.

Admin Control Center logic is defined in `docs/admin-control-center-logic.md`: admins control tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance mode, force update, sync behavior, notifications, reports, billing, and support through scoped, authorized, auditable server-side controls.

Feature flag logic is defined in `docs/feature-flag-logic.md`: important mobile features are controlled by global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, and emergency decisions that resolve into mobile-safe API outcomes.

Remote configuration logic is defined in `docs/remote-configuration-logic.md`: safe runtime mobile behavior is controlled by versioned, scoped, validated config that mobile receives through API, caches carefully, and treats as non-authoritative when stale or invalid.

Mobile version control logic is defined in `docs/mobile-version-control-logic.md`: Admin/API controls minimum supported versions, optional updates, forced updates, maintenance mode, store links, update messages, and stale-client protection through mobile-safe API outcomes.

1. **Admin/API system** - Laravel API plus Livewire admin panel. This is the SaaS control plane.
2. **Mobile client system** - Laravel plus Livewire inside NativePHP Mobile. This is the managed mobile edge client.

The Admin/API system owns tenant authority, user authority, permissions, feature flags, remote config, app-version policy, notifications, billing, reports, support, audit, and sync decisions. The mobile client consumes those decisions through the API and may cache or queue local work, but it must never become the source of business authority.

NativePHP + Livewire is the chosen mobile approach because the product remains Laravel-first, keeps dynamic UI close to server-side validation and authorization, avoids a separate JavaScript/mobile framework stack, and still allows native capabilities through NativePHP plugins.

## Documentation-Only Planning Rule

When the user asks for planning, product concept, documentation, system design, or architecture docs:

- Write Markdown only.
- Do not create database fields.
- Do not create migrations.
- Do not create API controllers.
- Do not create Livewire components.
- Do not create policies, jobs, services, or other application logic.
- Do not add billing, push, storage, or NativePHP plugin integrations.
- Record decisions, boundaries, risks, flows, and acceptance criteria instead.

## Product Documentation Source

Use these docs before changing the product direction:

- `docs/saas-mobile-admin-platform.md`
- `docs/product-vision.md`
- `docs/product-positioning.md`
- `docs/product-principles.md`
- `docs/documentation-first-architecture.md`
- `docs/user-roles.md`
- `docs/role-permission-logic.md`
- `docs/audit-logic.md`
- `docs/saas-value-map.md`
- `docs/two-system-boundary.md`
- `docs/api-first-principles.md`
- `docs/admin-api-responsibilities.md`
- `docs/mobile-client-responsibilities.md`
- `docs/mobile-app-shell-logic.md`
- `docs/mobile-dashboard-logic.md`
- `docs/mobile-settings-logic.md`
- `docs/mobile-permission-logic.md`
- `docs/authentication-principles.md`
- `docs/mobile-app-lock-principles.md`
- `docs/admin-control-center-logic.md`
- `docs/feature-flag-logic.md`
- `docs/remote-configuration-logic.md`
- `docs/mobile-version-control-logic.md`
- `docs/decisions/0001-admin-api-control-plane-and-native-mobile-client.md`
- `docs/mobile-stack.md`
- `docs/nativephp-local-storage.md`
- `docs/nativephp-run.md`
- `docs/design-system.md`

## Authority Boundary

- Admin/API is authoritative for SaaS rules.
- Mobile is authoritative only for local presentation, local drafts, local queues, and native device interaction.
- Documentation-first architecture decisions must follow `docs/documentation-first-architecture.md`.
- Role and permission decisions must follow `docs/role-permission-logic.md`.
- Audit decisions must follow `docs/audit-logic.md`.
- Two-system boundary decisions must follow `docs/two-system-boundary.md`.
- API-first decisions must follow `docs/api-first-principles.md`.
- Admin/API responsibility decisions must follow `docs/admin-api-responsibilities.md`.
- Mobile-client responsibility decisions must follow `docs/mobile-client-responsibilities.md`.
- Mobile UX decisions must follow `docs/mobile-ux-principles.md`.
- Mobile app shell decisions must follow `docs/mobile-app-shell-logic.md`.
- Mobile dashboard decisions must follow `docs/mobile-dashboard-logic.md`.
- Mobile settings decisions must follow `docs/mobile-settings-logic.md`.
- Mobile permission decisions must follow `docs/mobile-permission-logic.md`.
- Authentication decisions must follow `docs/authentication-principles.md`.
- Mobile app lock decisions must follow `docs/mobile-app-lock-principles.md`.
- Admin Control Center decisions must follow `docs/admin-control-center-logic.md`.
- Feature flag decisions must follow `docs/feature-flag-logic.md`.
- Remote configuration decisions must follow `docs/remote-configuration-logic.md`.
- Mobile version control decisions must follow `docs/mobile-version-control-logic.md`.
- Admin safety decisions must follow `docs/admin-safety-principles.md`.
- Admin settings control mobile feature availability because mobile state can be stale, offline, copied between devices, or running an old app version.
- API-first means admin decisions become enforceable mobile behavior through versioned server contracts.
- Local SQLite stores cache, drafts, and queued intents, not trusted server facts.
- Secure tokens belong in secure storage, not SQLite.
- Every replayable mobile write must be idempotent at the API boundary.
- Any feature must define admin behavior, API behavior, mobile behavior, offline behavior, support behavior, and audit behavior before implementation.
- Any feature, admin control, mobile screen, sync behavior, permission, or risk-sensitive change must be documented before implementation according to `docs/documentation-first-architecture.md`.
- Any feature must pass the core principles checklist in `docs/product-principles.md`.
- Any feature that changes visibility or control must map behavior to the role model in `docs/user-roles.md`.
- Any platform-level permission, tenant-level permission, admin-user permission, mobile-user permission, API access rule, mobile UI visibility rule, feature-flag access interaction, suspended-user behavior, or suspended-tenant behavior must map to `docs/role-permission-logic.md`.
- Any admin action, security event, support activity, mobile activity summary, API decision, sync outcome, compliance-relevant change, audit history view, or audit export must map to `docs/audit-logic.md`.
- Any feature, report, notification, sync behavior, security control, billing rule, or feature flag must map to stakeholder value in `docs/saas-value-map.md`.
- Any mobile cache, draft, queue, native capability, offline behavior, or local state must map to the ownership rules in `docs/two-system-boundary.md`.
- Any mobile/API behavior, boot context, response shape, mobile error, sync replay, conflict, or tenant-scoped response must map to `docs/api-first-principles.md`.
- Any tenant, user, permission, API, feature, config, version, notification, billing, support, report, audit, conflict, or security concern must map to the Admin/API responsibility rules in `docs/admin-api-responsibilities.md`.
- Any mobile UX, secure local session, cache, offline action, NativePHP capability, navigation, permissions UX, sync display, draft, local feedback, or feature-visibility concern must map to `docs/mobile-client-responsibilities.md`.
- Any NativePHP mobile UX, navigation, simple screen, loading/offline state, thumb-friendly control, minimum-typing flow, fast action, secure session, or native permission prompt must map to `docs/mobile-ux-principles.md`.
- Any welcome, authenticated, locked, offline, maintenance, forced update, tenant switching, sync-in-progress, permission-blocked, or feature-disabled shell behavior must map to `docs/mobile-app-shell-logic.md`.
- Any mobile dashboard content, feature shortcut, sync/offline summary, notification count, recent activity, announcement, or quick action must map to `docs/mobile-dashboard-logic.md`.
- Any mobile settings group, local preference, device permission recovery, account setting, tenant setting, security setting, notification setting, sync setting, appearance setting, storage setting, support setting, legal setting, diagnostic setting, or offline-disabled setting behavior must map to `docs/mobile-settings-logic.md`.
- Any native permission prompt, camera, microphone, location, notifications, files, scanner, biometrics, secure-storage, denied-permission recovery, disabled-feature permission, or permission-status setting behavior must map to `docs/mobile-permission-logic.md`.
- Any mobile login, token storage, refresh session, logout, logout-all-devices, tenant selection after login, session expiry, offline already-authenticated state, or server-revoked access behavior must map to `docs/authentication-principles.md`.
- Any app lock, biometric unlock, PIN unlock, sensitive-area confirmation, failed unlock attempt, logout cleanup, admin-disabled biometric, or offline cached-data protection behavior must map to `docs/mobile-app-lock-principles.md`.
- Any admin control for tenants, users, roles, permissions, features, config, versions, maintenance, force update, sync, notifications, reports, billing, or support must map to `docs/admin-control-center-logic.md`.
- Any important mobile feature flag must map to `docs/feature-flag-logic.md`, including priority, disabled mobile state, rollout, impact, plan limit, support, audit, and offline behavior.
- Any remote-configurable behavior must map to `docs/remote-configuration-logic.md`, including allowed config type, scope, default, override behavior, mobile caching, offline behavior, validation, fallback, support, audit, and rollback.
- Any app-version behavior must map to `docs/mobile-version-control-logic.md`, including minimum supported version, optional update, forced update, maintenance mode, outdated response, store link, update message, support context, audit, rollback, and old-version protection.
- Any dangerous admin action must map to `docs/admin-safety-principles.md`, including confirmation, audit history, impact preview, mobile impact preview, rollback, and tenant-isolated scope.

</project-product-contract>

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
