# NativePHP Runbook

Updated: 2026-06-25

This Laravel app is initialized for NativePHP Mobile. In the optimized SaaS product, NativePHP is the mobile shell and native capability bridge. The Admin/API system remains the source of tenant, permission, feature, billing, notification, app-version, and sync policy.

The runbook supports the [Product Vision](product-vision.md): native builds should be stable clients of admin-controlled SaaS behavior, not independent policy engines.

It also supports the [Product Positioning](product-positioning.md): NativePHP turns the product into a mobile workforce/client platform while the Admin/API system remains the SaaS control center.

Native build and release work must follow [Core Product Principles](product-principles.md): admin controls version and feature policy, the mobile client never bypasses the API, app behavior is feature-controlled, security is default, and mobile UX stays simple.

Native build and release work must follow [Documentation-First Architecture](documentation-first-architecture.md): release features, admin mobile effects, screen API dependencies, sync/offline behavior, permission ownership, and risks must be written before implementation or release.

Native build and release work must follow [Admin Control Center Logic](admin-control-center-logic.md): app-version policy, maintenance mode, force update, feature gates, sync behavior, notifications, support, and release-related billing/reporting effects must be scoped, authorized, auditable, and API-driven.

Native build and release work must follow [Feature Flag Logic](feature-flag-logic.md): app-version compatibility, NativePHP capability availability, rollout cohorts, disabled states, force update, and plan-limited features must resolve through API-provided feature states.

Native build and release work must follow [Remote Configuration Logic](remote-configuration-logic.md): bundled defaults, remote config compatibility, config freshness, tenant overrides, and invalid-config fallback must be documented before release.

Native build and release work must follow [Mobile Version Control Logic](mobile-version-control-logic.md): minimum supported versions, optional updates, forced updates, maintenance mode, store links, update messages, support context, and old-version protection must be documented before release.

Native build and release work must follow [Admin Safety Principles](admin-safety-principles.md): dangerous release, force-update, maintenance, config, feature, notification, or sync controls must be confirmed, audited, impact-previewed, mobile-previewed, rollback-aware, and tenant-isolated before release.

Native build and release work must follow [Mobile UX Principles](mobile-ux-principles.md): NativePHP navigation, simple screens, loading/offline states, thumb-friendly controls, minimum typing, fast actions, secure sessions, feature visibility, and native permission education must stay clear across releases.

Native build and release work must follow [Native Feature Strategy](native-feature-strategy.md): plugin-backed capabilities, browser/development fallbacks, permission education, feature flags, failure UX, diagnostics, and offline sync behavior must be documented before a native feature ships.

Mobile App Shell Logic is defined in `mobile-app-shell-logic.md`:
shell states must coordinate welcome, authenticated, locked, offline, maintenance, forced update, tenant
switching, sync-in-progress, permission-blocked, and feature-disabled behavior
before implementation.

Mobile Dashboard Logic is defined in `mobile-dashboard-logic.md`:
dashboard content must resolve current user context, current tenant, enabled
feature shortcuts, sync/offline status, unread notifications, recent
activity, announcements, and quick actions through API-safe rules before
implementation.

Mobile Settings Logic is defined in `mobile-settings-logic.md`:
settings sections for account, tenant, security, notifications, sync,
appearance, permissions, storage, support, legal, and diagnostics must
separate local device control from Admin/API authority and define
offline-disabled behavior before implementation.

Mobile Permission Logic is defined in `mobile-permission-logic.md`:
native permission requests for camera, microphone, location, notifications,
files, scanner, biometrics, and secure storage must explain purpose before
prompting, respect feature flags and API authority, avoid disabled-feature
prompts, support denied-permission recovery, and show status in settings before
implementation.

Authentication Principles are defined in `authentication-principles.md`:
mobile login must happen through the API only; access and refresh tokens must
use secure storage; refresh, logout, logout-all-devices, tenant selection,
session expiry, offline already-authenticated behavior, and server revocation
must preserve Admin/API authority before implementation.

Mobile App Lock Principles are defined in `mobile-app-lock-principles.md`:
the mobile client must lock on security-sensitive lifecycle, timeout,
account, tenant, offline-cache, and admin-policy conditions; require
confirmation for sensitive areas; use biometric or PIN unlock only as local
protection; handle failed attempts, logout, admin-disabled biometrics, and
offline cached data without bypassing Admin/API authority.

Role And Permission Logic is defined in `role-permission-logic.md`:
platform, tenant, admin-user, and mobile-user permissions must be resolved by
Admin/API before API access or mobile UI visibility; permissions interact
with feature flags as separate gates; suspended users and suspended tenants
fail closed without bypassing tenant isolation.

Audit Logic is defined in `audit-logic.md`:
admin actions, security events, support activity, mobile activity summaries,
API decisions, sync outcomes, and compliance-relevant changes must produce
protected audit history that answers who did what, where it applied, why it
happened, what changed, and how tenant-safe support or compliance review can
understand it.

Data Privacy Principles are defined in `data-privacy-principles.md`:
tenant isolation, least privilege, secure local mobile data, secure native
storage, export and deletion boundaries, support access limits, admin
visibility boundaries, privacy-by-default behavior, and mobile diagnostics
privacy limits must protect users and tenants without turning mobile cache,
support views, or audit history into uncontrolled data exposure.

Tenant Lifecycle Logic is defined in `tenant-lifecycle-logic.md`:
tenant creation, onboarding, trial, active, suspended, archived,
billing-blocked, deletion/requested deletion, and restore states must be
Admin/API-owned lifecycle decisions that mobile presents as safe,
tenant-scoped, billing-aware, supportable states without inventing local
tenant authority.

Tenant Admin Logic is defined in `tenant-admin-logic.md`:
tenant admins may manage tenant-scoped users, invitations, delegated settings,
delegated mobile-feature controls, tenant reports, and tenant support workflows
only inside their tenant; platform-only controls, cross-tenant visibility,
global policy, billing authority, lifecycle authority, app-version policy, and
security posture remain Admin/API-owned boundaries.

Multi-Tenant Mobile Logic is defined in `multi-tenant-mobile-logic.md`:
users with more than one tenant choose and remember tenant context through
API-confirmed state; tenant switching, tenant-scoped cache, per-tenant
permissions and feature flags, sync replay, offline behavior, and logout
cleanup must preserve tenant isolation and never turn mobile-local state
into tenant authority.

Offline-First Principles are defined in `offline-first-principles.md`:
mobile may use safe cache, drafts, queued intents, sync status, and clear
offline messaging to keep users productive, but protected reads, writes,
conflicts, billing, permissions, feature access, audit, and tenant authority
must wait for API confirmation before becoming trusted.

Offline UX Logic is defined in `offline-ux-logic.md`:
offline UX must calmly explain banners, disabled online-only actions,
local drafts, pending indicators, retry, sync success or failure,
saved-local versus synced state, and data-loss prevention whenever
connection changes.

Records/Content Module Logic is defined in `records-content-module-logic.md`:
records are tenant-scoped business content with API-owned lifecycle,
notes, attachments, activity, tags, categories, status, offline draft or
sync behavior, admin controls, permissions, feature flags, audit, and
reporting boundaries.

Search Logic is defined in `search-logic.md`:
search stays tenant-scoped, permission-aware, feature-controlled,
privacy-preserving, and explicit about local-cache limits versus
API-authoritative results, including recent searches, saved filters, filtering,
sorting, scan-to-search, offline limits, and admin-controlled boundaries.

Forms And Drafts Logic is defined in `forms-drafts-logic.md`:
mobile forms must stay simple, validated, autosave-aware, offline-draft
safe, API-submitted, admin-controlled, and explicit about local-save versus
server-accepted state so user work is protected without bypassing authority.

Notifications Logic is defined in `notifications-logic.md`:
notification targeting, delivery policy, push behavior, in-app inbox,
read/unread state, deep links, preferences, offline behavior, and tenant or
permission boundaries must remain Admin/API-authoritative and mobile-safe.

Support System Logic is defined in `support-system-logic.md`:
support requests, support messages, attachments, tenant context, support-agent
visibility, audit, notifications, and offline support drafts must remain
tenant-scoped, least-privilege, privacy-safe, and Admin/API-authoritative.

Billing And Plan Logic is defined in `billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature states,
and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

Reporting Logic is defined in `reporting-logic.md`:
admin measurements, tenant-admin measurements, mobile-visible summaries,
privacy boundaries, date ranges, exports, feature usage, sync health,
notification, support, and billing reports must remain tenant-scoped,
permission-aware, privacy-safe, auditable, and Admin/API-authoritative.

Native Feature Strategy is defined in `native-feature-strategy.md`:
NativePHP capability use, logical service boundaries, browser/development
fallbacks, permission education, admin feature-flag control, native failure
UX, and offline sync behavior must remain feature-scoped, tenant-safe,
privacy-aware, fallback-safe, and Admin/API-authoritative.

Camera And Media Logic is defined in `camera-media-logic.md`:
photo capture, media selection, media preview, record/support attachments,
offline media storage, upload queues, feature-flag control, permission
denial, size limits, and privacy behavior must remain tenant-scoped,
permission-aware, fallback-safe, queue-safe, privacy-safe, and
Admin/API-authoritative.

Scanner Logic is defined in `scanner-logic.md`:
QR/barcode scan-to-search, scan-to-create, scan-to-validate, scan history,
offline scanning, invalid scan handling, duplicate scan handling, admin
feature flags, and camera/permission dependency behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear,
duplicate-safe, privacy-safe, and Admin/API-authoritative.

Geolocation Logic is defined in `geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Device, Network, And Diagnostics Logic is defined in `device-network-diagnostics-logic.md`:
device information use, network status use, offline detection, diagnostics
export, support troubleshooting context, diagnostics redaction, admin mobile
device visibility, and user-controlled diagnostics sharing must remain
tenant-scoped, permission-aware, support-scoped, privacy-safe, redacted,
audit-ready, and Admin/API-authoritative.

Module Selection Principles are defined in `module-selection-principles.md`:
optional industry modules such as field service, logistics, ecommerce,
booking, education, events, support, community/messaging, reports, and AI
assistant must be tenant-enabled, plan-controlled, permission-aware,
mobile-hidden when unavailable, feature-flag-safe, documented before
implementation, and Admin/API-authoritative.

Field Service Logic is defined in `field-service-logic.md`:
work order lifecycle, technician mobile flow, check-in/check-out, photos,
notes, future signatures, offline behavior, admin dispatch/control, and
report visibility must remain tenant-enabled, plan-controlled,
permission-aware, offline-clear, evidence-safe, privacy-safe, auditable,
and Admin/API-authoritative.

Booking Logic is defined in `booking-logic.md`:
service selection, availability logic, booking requests, confirmation,
cancellation, reschedule, reminders, admin schedule control, tenant rules,
and mobile offline limitations must remain tenant-enabled, plan-controlled,
permission-aware, availability-safe, schedule-conflict-safe, reminder-safe,
offline-limited, privacy-safe, auditable, and Admin/API-authoritative.

Commerce Logic is defined in `commerce-logic.md`:
catalog browsing, cart behavior, checkout principles, hosted payment
boundaries, order lifecycle, invoice/receipt principles, subscription
upsell, admin product/control, and mobile offline limitations must remain
tenant-enabled, plan-controlled, permission-aware, price-safe,
inventory-safe, hosted-payment-safe, offline-limited, privacy-safe,
auditable, and Admin/API-authoritative.

Messaging And Community Logic is defined in `messaging-community-logic.md`:
conversation behavior, support chat behavior, message attachments,
moderation, reports/abuse flow, notification behavior, offline message
drafts, admin visibility boundaries, and privacy principles must remain
tenant-enabled, plan-controlled, permission-aware, moderation-ready,
abuse-report-safe, notification-safe, offline-draft-safe, privacy-safe,
auditable, and Admin/API-authoritative.

AI Feature Logic is defined in `ai-feature-logic.md`:
AI assistant purpose, summarization, categorization, smart suggestions,
moderation assistance, report generation assistance, admin AI controls,
tenant opt-in, privacy, and human-review principles must remain
tenant-enabled, plan-controlled, permission-aware, opt-in-only,
provider-neutral, human-reviewed, privacy-safe, audit-ready,
rate-limited, cost-aware, and Admin/API-authoritative.

Acceptance Principles are defined in `acceptance-principles.md`:
every feature must document purpose, admin control, mobile behavior,
API dependency, offline behavior, permission behavior, feature flag
behavior, tenant behavior, error behavior, security behavior, and
documentation requirements before implementation planning begins.

Risk Map is defined in `risk-map.md`:
API dependency, offline sync, tenant isolation, mobile secure storage,
NativePHP plugin availability, app store releases, forced updates,
feature flag mistakes, billing restrictions, admin misconfiguration,
support access, privacy, and data conflicts must document prevention
principles and documentation requirements before implementation.

Logistics Delivery Logic is defined in `logistics-delivery-logic.md`:
delivery job lifecycle, pickup flow, drop-off flow, proof of delivery,
scan validation, location check-in, failed delivery reasons, offline
behavior, and admin monitoring must remain tenant-enabled, plan-controlled,
permission-aware, scan-safe, location-purpose-limited, offline-clear,
privacy-safe, auditable, and Admin/API-authoritative.

Voice Note Logic is defined in `voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

Sync Lifecycle Logic is defined in `sync-lifecycle-logic.md`:
sync moves from bootstrap readiness to pull, push, retry, conflict
resolution, acknowledgement, status communication, manual sync,
background sync, and admin health monitoring while API authority remains
responsible for acceptance, rejection, conflict decisions, and audit.

Conflict Resolution Logic is defined in `conflict-resolution-logic.md`:
conflicts happen when local mobile intent and current server truth no
longer align, and resolution must protect user work while API/Admin
authority decides auto-resolution, user choice, admin/support review,
audit meaning, and data-loss prevention.

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
| Optional update | App works and shows a non-blocking update prompt. |
| Recommended update | App works but shows update prompt. |
| Deprecated | App works with warnings and possibly reduced feature access. |
| Force update | App blocks normal operation and directs user to update. |
| Blocked | App blocks normal operation and directs user to update or contact support. |
| Maintenance | App shows scoped maintenance, retry-later, or limited-mode behavior. |
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
- Admin Control Center checks are complete for app-version, maintenance, force-update, feature, sync, notification, support, report, and billing behavior affected by the release.
- Feature Flag Logic checks are complete for every enabled, beta, disabled, blocked, update-required, offline-limited, and emergency-disabled mobile capability in the release.
- Remote Configuration Logic checks are complete for every runtime-configurable copy, limit, workflow option, offline/sync behavior, native permission wording, support prompt, notification presentation, version message, and tenant presentation value in the release.
- Mobile Version Control Logic checks are complete for minimum supported version, optional update prompt, forced update behavior, maintenance state, store links, update messages, support context, and old-version protection.
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
- Admin Control Center logic: [Admin Control Center Logic](admin-control-center-logic.md)
- Feature flag logic: [Feature Flag Logic](feature-flag-logic.md)
- Remote configuration logic: [Remote Configuration Logic](remote-configuration-logic.md)
- Mobile version control logic: [Mobile Version Control Logic](mobile-version-control-logic.md)
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
