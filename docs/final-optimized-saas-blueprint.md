# Final Optimized SaaS Blueprint

Updated: 2026-06-26

This is the main planning document for Mobile Lara. It defines the optimized
SaaS product and system blueprint for the Admin/API control plane and the
NativePHP mobile client.

This is documentation only. It does not create database fields, migrations,
routes, controllers, Livewire components, Filament resources, NativePHP
plugins, policies, jobs, services, tests, API endpoints, local storage schemas,
UI components, CSS, JavaScript, queues, provider integrations, billing
provider rules, notification provider rules, release automation, or application
logic.

Use this blueprint as the first planning reference before changing product
logic, admin behavior, API contracts, mobile behavior, offline behavior,
NativePHP feature behavior, billing behavior, support behavior, reporting
behavior, release behavior, or optional module scope.

## Product Vision

Mobile Lara is a tenant-based SaaS platform for organizations that need a
centrally controlled mobile client without losing the speed and simplicity of a
Laravel product. It solves the problem of mobile work being disconnected from
business authority: administrators need to control tenants, users, roles,
permissions, features, app versions, remote config, notifications, billing,
support, reports, security, and sync behavior, while mobile users need a simple
app that keeps working where network access is unreliable.

The optimized product vision is:

- **Admin/API is the SaaS control center.**
- **Mobile is the focused NativePHP execution client.**
- **The API is the only trusted path between admin authority and mobile
  behavior.**
- **Every important mobile capability is controlled remotely before it appears
  locally.**
- **Offline behavior protects continuity, not authority.**
- **Future modules can expand by tenant, plan, role, feature flag, and
  documented dependency boundaries.**

The product is better than only a web app because it can use mobile-native
capabilities, secure local cache, offline drafts, push, device context, and
fast field workflows. It is better than only a mobile app because business
authority, tenant control, billing, support, reporting, security, release
control, and operational safety remain centralized in the SaaS control plane.

## System Architecture

The system has two products that must remain logically separate:

| System | Owns | Must not own |
| --- | --- | --- |
| Admin/API | Tenant authority, user authority, permissions, feature flags, remote config, version rules, billing, notifications, support, reports, audit, security, conflict decisions, API contracts, sync acceptance. | Device-only UX details, native permission prompts, local draft interaction, local cache presentation. |
| Mobile client | Mobile UX, secure local session presentation, local cache, drafts, queued intents, NativePHP device capability, navigation, offline status, sync status, permission education, feature visibility presentation. | Tenant authority, permission authority, billing authority, global configuration authority, final sync truth, conflict authority, report authority, support visibility authority. |

The architecture is optimized around clear authority:

1. Admin/API defines and enforces policy.
2. API returns mobile-safe context.
3. Mobile presents resolved behavior and gathers user intent.
4. Offline mobile work remains local intent until API acceptance.
5. Admin/API audits meaningful control and security decisions.
6. Documentation records behavior before implementation expands it.

## Admin/API Logic

Admin/API is the control plane. It should own:

- Tenant lifecycle, onboarding, suspension, archive, deletion, restore, and
  billing-blocked states.
- Users, invited users, suspended users, roles, and permissions.
- Admin panel behavior and delegated tenant admin controls.
- API contracts and predictable response principles.
- Feature flags, rollout strategy, plan limits, maintenance controls, and
  emergency stops.
- Remote configuration values that tune safe mobile behavior.
- Mobile app version rules, optional updates, forced updates, and store
  messaging.
- Notification orchestration, targeting, inbox meaning, deep links, and
  preferences.
- Billing and subscription entitlement ceilings.
- Support workflows, support visibility, support messages, diagnostics review,
  and privacy limits.
- Reports, exports, analytics visibility, feature usage, sync health, support
  reporting, billing reporting, and notification reporting.
- Audit history for admin actions, security events, support actions, sync
  decisions, conflict decisions, and compliance-relevant activity.
- Security enforcement, tenant isolation, authorization, token/session
  decisions, and fail-closed behavior.

Admin/API controls should be safe by default. Dangerous changes need impact
preview, confirmation, audit history, rollback thinking, tenant isolation, and
mobile-impact explanation before they affect users.

## Mobile-Client Logic

The mobile client is the execution surface for mobile users. It should own:

- Simple mobile-first navigation and screen flow.
- Secure local session presentation and app lock UX.
- Local cache, local drafts, queued intents, offline status, and sync status.
- NativePHP capability interaction such as camera, scanner, location,
  microphone, biometrics, secure storage, notifications, files, device,
  network, diagnostics, haptics, sharing, and browser fallbacks where
  available.
- Permission education before native permission prompts.
- Clear loading, offline, stale, pending, failed, synced, disabled, locked,
  maintenance, forced-update, plan-blocked, and permission-blocked states.
- Tenant context presentation after API confirmation.
- Feature visibility based on resolved Admin/API rules.
- Local user feedback that distinguishes saved locally from accepted by the
  server.

Mobile should never invent business authority locally. When policy, plan,
permissions, tenant state, config, version rules, or API truth are unavailable,
mobile should explain the safest resolved state and preserve user work only
when documented policy allows it.

## API Principles

The API is the product contract between SaaS authority and mobile execution.
It should be designed as a stable, predictable, mobile-safe interface:

- Mobile communicates only with the API.
- API responses provide user context, tenant context, permissions, feature
  flags, remote config, version rules, subscription context, support context,
  sync state, and actionable error meaning where relevant.
- API errors should be structured enough for mobile to show helpful states
  without exposing implementation detail.
- API contracts should prefer additive evolution and documented compatibility.
- API behavior should protect tenant boundaries server-side.
- API sync and conflict decisions should be explicit, auditable, and
  recoverable.
- API contracts should avoid leaking implementation detail that mobile might
  accidentally depend on.

No mobile feature is ready to plan until its API dependency is documented.

## Tenant Principles

Tenant context is the commercial, operational, reporting, billing, support,
configuration, cache, sync, audit, and security boundary.

Tenant principles:

- Every protected user, feature, record, report, support request, notification,
  config, billing state, and sync decision belongs to a tenant or platform
  scope.
- Tenant admins may receive delegated tenant-scoped control, not platform
  authority.
- Mobile users with multiple tenants must choose an API-confirmed active
  tenant.
- Mobile cache, drafts, queues, feature flags, permissions, and sync state must
  remain logically separated by tenant.
- Suspended, archived, billing-blocked, or unavailable tenants fail closed on
  protected behavior and show clear mobile states.

## Permissions Principles

Permissions decide authority. Feature flags decide availability. Billing
decides entitlement. Remote config tunes safe behavior. These concepts must not
be merged.

Permission principles:

- Platform-level permissions apply to platform owners and super admins.
- Tenant-level permissions apply inside one tenant boundary.
- Admin-user permissions decide admin panel visibility and control authority.
- Mobile-user permissions decide API access and mobile feature/action
  visibility.
- Mobile UI hiding is only presentation; API authorization is the source of
  truth.
- Suspended users, revoked sessions, blocked tenants, or expired authority
  should fail closed.

## Feature Flag Principles

Every important mobile feature should be controllable through feature flags.
Flags support safe rollout, tenant-specific enablement, user or cohort testing,
plan limits, emergency shutdown, app-version gating, and admin confidence.

Feature flag principles:

- Global rules define platform defaults.
- Tenant rules adapt defaults to business scope.
- Plan rules define entitlement ceilings.
- Role, permission, user, cohort, device, and app-version rules can narrow
  access.
- Maintenance and emergency rules can override availability for safety.
- Disabled features should be hidden, disabled with explanation, or routed to a
  contact-admin/support state depending on product context.
- Admins should see impact before changing a flag.
- Mobile should receive resolved feature outcomes, not raw internal policy.

## Remote Config Principles

Remote config safely tunes runtime behavior without granting authority.

Remote config may control safe values such as wording, limits, thresholds,
display choices, retry timing, upload limits, dashboard ordering, offline
limits, permission education text, update messages, store links, and support
guidance. It must not grant permissions, override tenant boundaries, bypass
billing, create feature entitlement, or weaken security.

Remote config principles:

- Global defaults should be conservative.
- Tenant overrides should be explicit and isolated.
- Mobile should cache only safe config with freshness awareness.
- Missing, stale, or invalid config should fall back safely.
- Admin changes should support preview, validation, audit, and rollback
  thinking.

## Offline Sync Principles

Offline-first means local continuity with server reconciliation. It does not
mean trusted offline completion.

Offline sync principles:

- Mobile can show safe cached data, create local drafts, queue allowed intents,
  attach pending media, and explain pending state.
- Protected reads, writes, billing, permission changes, feature availability,
  tenant authority, conflict resolution, audit meaning, and final sync truth
  require API acceptance.
- Sync should move through bootstrap, pull, local intent push, retry, conflict
  detection, conflict resolution, acknowledgement, and status communication.
- Failed sync should preserve user work where possible and explain the next
  recovery action.
- Admin/API should be able to monitor sync health and conflict patterns.
- Offline limits should be controlled by admin policy and documented per
  feature.

## NativePHP Feature Principles

NativePHP is the chosen mobile approach because it keeps Laravel and Livewire
as the product development language while exposing mobile-native capability
where it creates real value. It fits this product because the same Laravel
team can build the mobile client, reuse server-side product thinking, and keep
mobile behavior tightly controlled by the API.

NativePHP principles:

- Native capability is not product authority.
- Native features should sit behind logical product services and documented
  fallbacks.
- Disabled or plan-blocked features should not request native permissions.
- Permissions should be explained before native prompts.
- Browser/development fallbacks should exist where useful.
- Native failures should become clear user states, not silent broken flows.
- Native output such as photos, scans, locations, voice notes, diagnostics, or
  push registrations becomes business data only after API acceptance.
- Native feature use must remain tenant-scoped, permission-aware,
  feature-flag-controlled, privacy-safe, and audit-ready where relevant.

## Notification Principles

Notifications are controlled communication, not generic alerts.

Notification principles:

- Admin-created notifications, system notifications, security notifications,
  reminders, push notifications, and in-app inbox behavior should be
  tenant-scoped and permission-aware.
- Push permission should be requested only when notification behavior is
  enabled and useful.
- Notification targeting, delivery rules, read/unread state, deep links, and
  preferences should resolve through API.
- Offline mobile may show cached inbox state but cannot invent delivery truth.
- Security notifications should prioritize clarity and account protection.
- Notification reporting should help admins understand delivery, engagement,
  and failures without violating privacy.

## Billing Principles

Billing and plan state define entitlement ceilings for tenants.

Billing principles:

- Trial, active, expired, suspended, billing-blocked, and manually overridden
  states should produce clear admin and mobile behavior.
- Plans can limit features, modules, usage, seats, storage, notifications,
  reports, support levels, native features, offline limits, or API access
  patterns.
- Feature flags may narrow availability below the plan ceiling but should not
  grant access above plan authority.
- Mobile users should see simple unavailable-feature explanations, not billing
  system complexity.
- Tenant admins or billing managers may receive delegated billing visibility
  depending on permission.
- Payment provider implementation should remain separate from product
  entitlement principles.

## Support Principles

Support exists to help users without breaking tenant isolation or privacy.

Support principles:

- Mobile users can create support requests with tenant context, category,
  message, attachments, and optional diagnostics where allowed.
- Support agents see only what their role, tenant scope, and assignment allow.
- Support access should be least-privilege, audited, privacy-limited, and
  explainable.
- Offline support drafts may be preserved locally but case creation and
  messaging require API acceptance.
- Diagnostics should redact secrets, tokens, private content, and unrelated
  tenant data.
- Support workflows should connect to notifications, reports, audit, and
  release operations when incidents affect users.

## Reporting Principles

Reports convert system behavior into operational understanding.

Reporting principles:

- Platform admins need cross-tenant operational, billing, support, feature,
  release, sync, and risk visibility where authorized.
- Tenant admins need tenant-scoped usage, mobile activity, support, billing,
  notification, feature, and sync health visibility.
- Mobile users should see only personal or role-appropriate summaries.
- Reports must respect tenant isolation, role permissions, privacy, date-range
  scope, export authority, and audit expectations.
- Feature usage, sync health, notification performance, support load, billing
  state, and module activity should be measurable before release decisions.

## Security Principles

Security is a product behavior, not only an implementation detail.

Security principles:

- Tenant isolation and least privilege are default.
- API authorization is required for protected access.
- Mobile secure storage protects local tokens and sensitive local state.
- App lock can protect cached data but cannot replace server
  authentication or authorization.
- Offline cache should be minimized, scoped, freshness-aware, and protected.
- Admin actions, support actions, security events, conflict decisions, and
  policy changes should be audited.
- Privacy-by-default limits diagnostics, support access, exports, and report
  visibility.
- Dangerous states fail closed: revoked access, suspended users, blocked
  tenants, forced update, maintenance, missing config, invalid permission, and
  uncertain sync authority.

## Release Principles

Release is controlled exposure of behavior, not just deployment.

Release principles:

- API contracts should be versioned and compatible.
- Mobile app versions should be controlled through minimum-supported,
  optional-update, forced-update, maintenance, store-link, and update-message
  policy.
- Admin releases should document mobile impact before controls change.
- Feature rollout should use flags, cohorts, tenants, plans, app versions,
  rollback states, support readiness, and reporting.
- App store releases should account for review delay, old-version users,
  forced-update risk, and rollback limits.
- Every release should update documentation, changelog, and Git history with
  clear planning intent.

## Future Module Expansion Principles

Future modules should expand the platform without weakening the control model.
Possible modules include field service, logistics, ecommerce, booking,
education, events, support, community/messaging, reports, and AI assistant.

Expansion principles:

- Every module must be documented before implementation.
- Every module must define purpose, tenant scope, user roles, admin controls,
  API dependency, mobile behavior, offline behavior, native permissions,
  feature flags, remote config, billing impact, support impact, reporting
  impact, security impact, release impact, and risks.
- Modules should be enabled by tenant, controlled by plan, hidden or disabled
  on mobile when unavailable, and measured after rollout.
- Shared platform primitives should be reused: authentication, tenant context,
  permissions, feature flags, remote config, sync, notifications, support,
  billing, reporting, audit, and release controls.
- AI-assisted behavior must be optional, tenant opt-in, privacy-safe,
  provider-neutral, human-reviewed where consequential, and controlled by
  admin policy.

## Planning Checklist

Before any feature, module, or release moves from idea to implementation, the
project should answer:

1. What product problem does this solve?
2. Which admin role controls it?
3. Which mobile role uses it?
4. Which tenant state allows it?
5. Which permissions grant visibility and action authority?
6. Which feature flags and plan limits control availability?
7. Which remote config values safely tune behavior?
8. Which API context and errors does mobile require?
9. What works offline, what queues, and what must wait for API?
10. Which NativePHP permissions or fallbacks apply?
11. What notifications, support, reports, audit, privacy, and security effects
    exist?
12. What release, rollback, app-version, and documentation updates are needed?

If these questions are not answered in Markdown, implementation should wait.

## Canonical Documentation Map

This blueprint summarizes the product. Detailed authority remains in the
canonical documents:

- [Product Vision](product-vision.md)
- [Product Positioning](product-positioning.md)
- [Core Product Principles](product-principles.md)
- [Two-System Boundary Logic](two-system-boundary.md)
- [Admin/API Responsibilities](admin-api-responsibilities.md)
- [Mobile Client Responsibilities](mobile-client-responsibilities.md)
- [API-First Principles](api-first-principles.md)
- [Documentation-First Architecture](documentation-first-architecture.md)
- [Admin Control Center Logic](admin-control-center-logic.md)
- [Feature Dependency Map](feature-dependency-map.md)
- [Feature Flag Logic](feature-flag-logic.md)
- [Remote Configuration Logic](remote-configuration-logic.md)
- [Mobile Version Control Logic](mobile-version-control-logic.md)
- [Role And Permission Logic](role-permission-logic.md)
- [Tenant Lifecycle Logic](tenant-lifecycle-logic.md)
- [Tenant Admin Logic](tenant-admin-logic.md)
- [Multi-Tenant Mobile Logic](multi-tenant-mobile-logic.md)
- [Offline-First Principles](offline-first-principles.md)
- [Sync Lifecycle Logic](sync-lifecycle-logic.md)
- [Conflict Resolution Logic](conflict-resolution-logic.md)
- [Native Feature Strategy](native-feature-strategy.md)
- [Notifications Logic](notifications-logic.md)
- [Billing And Plan Logic](billing-and-plan-logic.md)
- [Support System Logic](support-system-logic.md)
- [Reporting Logic](reporting-logic.md)
- [Data Privacy Principles](data-privacy-principles.md)
- [Audit Logic](audit-logic.md)
- [Risk Map](risk-map.md)
- [Testing Strategy Principles](testing-strategy-principles.md)
- [Release And Versioning Principles](release-versioning-principles.md)
- [Module Selection Principles](module-selection-principles.md)

When documents appear to disagree, resolve the contradiction in Markdown before
writing application logic.
