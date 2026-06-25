# NativePHP Runbook

Updated: 2026-06-25

This Laravel app is initialized for NativePHP Mobile. In the optimized SaaS product, NativePHP is the mobile shell and native capability bridge. The Admin/API system remains the source of tenant, permission, feature, billing, notification, app-version, and sync policy.

The runbook supports the [Product Vision](product-vision.md): native builds should be stable clients of admin-controlled SaaS behavior, not independent policy engines.

It also supports the [Product Positioning](product-positioning.md): NativePHP turns the product into a mobile workforce/client platform while the Admin/API system remains the SaaS control center.

Native build and release work must follow [Core Product Principles](product-principles.md): admin controls version and feature policy, the mobile client never bypasses the API, app behavior is feature-controlled, security is default, and mobile UX stays simple.

Native build and release work must follow [Documentation-First Architecture](documentation-first-architecture.md): release features, admin mobile effects, screen API dependencies, sync/offline behavior, permission ownership, and risks must be written before implementation or release.

Native builds must also honor [Target User Roles](user-roles.md): mobile screens, pre-login flows, invitation flows, suspension handling, and support diagnostics should reflect the account state returned by the API.

Native releases must also honor the [SaaS Value Map](saas-value-map.md): each build should preserve platform-owner rollout control, tenant-business mobile continuity, tenant-admin governance, mobile-worker simplicity, support diagnosability, and billing/operations entitlement clarity.

Native releases must also honor [Two-System Boundary Logic](two-system-boundary.md): a build may improve mobile execution and native capability access, but it must not move SaaS authority out of Admin/API.

Native releases must also honor [API-First Principles](api-first-principles.md): every build must remain an API-only mobile client with predictable response handling, clear API feature purpose, mobile-friendly errors, sync/conflict expectations, and tenant-safe context.

Native releases must also honor [Admin/API Responsibilities](admin-api-responsibilities.md): version rules, feature gates, notification behavior, billing outcomes, support diagnostics, reports, audit expectations, conflict handling, and security enforcement are controlled by Admin/API.

Native releases must also honor [Mobile Client Responsibilities](mobile-client-responsibilities.md): builds own mobile UX, secure local session behavior, local cache, offline actions, NativePHP capability UX, navigation, permissions UX, sync display, drafts, feedback, and feature visibility without owning SaaS authority.

## Current Placeholders

| Setting | Value |
| --- | --- |
| App name | `Mobile Lara` |
| NativePHP app ID | `com.example.mobilelara` |
| iOS bundle identifier placeholder | `com.example.mobilelara` |
| Android package placeholder | `com.example.mobilelara` |
| App icon source | `public/icon.png` |
| Deep link scheme | `mobilelara` |
| Deep link host | `mobile-lara.test` |
| Start URL | `/` |

Replace `com.example.mobilelara` with a real reverse-domain identifier before signing or publishing.

## Product Release Model

Native builds should be treated as managed clients of the Admin/API system.

The reason is product scalability. A SaaS operator must be able to support many tenants, app versions, devices, feature states, and rollout cohorts without publishing a new mobile build for every operational decision.

This is why the product should not be mobile-only. Native builds are important, but they are managed clients of the tenant-based SaaS platform.

Each release should have:

- Platform: iOS, Android, or both.
- App version and build number.
- Minimum supported API contract.
- Remote config schema version.
- Feature flag compatibility.
- Required NativePHP plugin list.
- Permission purpose copy.
- Rollout cohort.
- Support and rollback notes.

The admin control plane should eventually be able to mark versions as:

| State | Mobile behavior |
| --- | --- |
| Supported | Normal operation. |
| Recommended update | App works but shows update prompt. |
| Deprecated | App works with warnings and possibly reduced feature access. |
| Blocked | App blocks normal operation and directs user to update. |
| Internal only | App is usable only for internal tenants, testers, or cohorts. |

## Native Capability Policy

Native permissions should be requested just in time, not all at first launch.

NativePHP + Livewire is chosen so the Laravel product can reach native device capabilities while keeping mobile workflows close to server-side rules, tests, and API contracts. Native capability access should strengthen the mobile UX; it should never become a separate authority path.

Every NativePHP capability needs:

- Admin/API feature flag.
- Tenant and role eligibility.
- Permission purpose copy.
- Offline behavior.
- Support diagnostics.
- Audit or activity behavior if business-sensitive.

Examples:

- Camera capture should be enabled per tenant/feature and explain why camera access is needed.
- File access should be scoped to the feature that needs files.
- Microphone access should be tied to voice-note behavior.
- Network status should drive sync/offline UX but not hide server-side errors.

## Initialize NativePHP

Run this after changing NativePHP config, plugins, app IDs, or icon assets:

```bash
php artisan native:install both --no-interaction
```

For platform-specific regeneration:

```bash
php artisan native:install ios --no-interaction
php artisan native:install android --no-interaction
```

The generated `nativephp/` directory is an ephemeral build artifact. NativePHP may delete and rebuild it during install or upgrade commands.

## Verify Local Tooling

```bash
php artisan native:debug --no-interaction
php artisan native:plugin:validate
```

Known local tooling status from previous checks:

- Java: present.
- CocoaPods: present.
- Xcode: not detected.
- Android Studio: not detected.
- Gradle: not detected.

iOS simulator runs require Xcode and installed simulators. Android emulator runs require Android Studio/SDK, an emulator image, and Gradle available to NativePHP.

## Run On iOS Simulator

```bash
php artisan native:run ios
```

To target a specific simulator after NativePHP shows available devices:

```bash
php artisan native:run ios <simulator-udid>
```

Useful development flags:

```bash
php artisan native:run ios --watch
php artisan native:run ios --start-url=/dev/tailwind
```

## Run On Android Emulator

```bash
php artisan native:run android
```

To target a specific emulator/device:

```bash
php artisan native:run android <device-udid>
```

Useful development flags:

```bash
php artisan native:run android --watch
php artisan native:run android --start-url=/dev/tailwind
```

## Open Native Projects

```bash
php artisan native:open ios
php artisan native:open android
```

## Test Without Building

NativePHP Jump can serve the app for device testing without compiling a native build:

```bash
php artisan native:jump
```

Use this for quick device smoke tests before full simulator/emulator build tooling is available.

## Pre-Run Checklist

```bash
npm run build
php artisan test --compact
php artisan native:plugin:validate
```

Also verify product policy before a real mobile release:

- API boot config exists for the target app version.
- API-first purpose, response, context, error, sync/conflict, and tenant-boundary expectations are documented for every enabled mobile capability.
- Documentation-first architecture checks are complete for every enabled mobile capability.
- Remote config and feature flags are compatible with the build.
- Two-system boundary ownership is documented for every mobile capability in the release.
- Admin/API responsibility ownership is documented for every mobile capability in the release.
- Mobile-client responsibility ownership is documented for every mobile capability in the release.
- Blocked/deprecated version policy is tested.
- Guest, invited, suspended, and mobile user states are covered in release expectations.
- Native permission copy matches enabled features.
- Core product principles are still satisfied for every enabled mobile feature.
- SaaS value map outcomes are known for every enabled mobile feature, report, notification, offline behavior, and feature flag.
- Support runbook knows the release version.
- Sync policy is compatible with the app's offline queue format.

Run `php artisan native:install both --no-interaction` again after replacing `public/icon.png`, changing app identifiers, changing NativePHP permissions, or adding/removing NativePHP plugins.

## Store And Distribution Boundary

Before production distribution, the project needs:

- Real iOS bundle identifier and Android package name.
- Production icon and splash assets.
- Apple team, signing, provisioning, and bundle capabilities.
- Android signing key and release build configuration.
- Store metadata, privacy disclosures, and permission disclosures.
- Version policy entered in the admin control plane.
- Support and rollback plan for the release.

## References

- Product vision: [Product Vision](product-vision.md)
- Product positioning: [Product Positioning](product-positioning.md)
- Core product principles: [Core Product Principles](product-principles.md)
- Documentation-first architecture: [Documentation-First Architecture](documentation-first-architecture.md)
- Target user roles: [Target User Roles](user-roles.md)
- SaaS value map: [SaaS Value Map](saas-value-map.md)
- Two-system boundary: [Two-System Boundary Logic](two-system-boundary.md)
- API-first principles: [API-First Principles](api-first-principles.md)
- Admin/API responsibilities: [Admin/API Responsibilities](admin-api-responsibilities.md)
- Mobile client responsibilities: [Mobile Client Responsibilities](mobile-client-responsibilities.md)
- NativePHP installation: https://nativephp.com/docs/mobile/3/getting-started/installation
- NativePHP command reference: https://nativephp.com/docs/mobile/3/getting-started/commands
- NativePHP app icons: https://nativephp.com/docs/mobile/3/the-basics/app-icon
- Product concept: [SaaS Mobile Admin Platform Concept](saas-mobile-admin-platform.md)
