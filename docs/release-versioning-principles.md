# Release And Versioning Principles

Final Optimized SaaS Blueprint is defined in `final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

Updated: 2026-06-26

Documentation Audit is defined in `documentation-audit.md`:
project documentation for two-system architecture, Admin/API authority, mobile
client execution, API-first communication, feature flags, remote config,
tenancy, permissions, offline sync, NativePHP features, notifications, billing,
support, reports, security, risks, and release principles must use consistent
authority language and resolve contradictions before implementation.

Feature Dependency Map is defined in `feature-dependency-map.md`:
major features must document dependencies on authentication, tenant context,
permissions, feature flags, remote config, API availability, offline cache,
NativePHP permissions, subscription plan, and admin settings before
implementation planning or release decisions.

This document defines release and versioning principles for the Mobile Lara
SaaS mobile/admin system. It explains API versioning, mobile app versioning,
admin release process, feature rollout process, rollback principles, app store
release principles, forced update principles, documentation update
requirements, and Git commit/change history principles. It is documentation
only and does not create release scripts, CI workflows, deployment files,
database fields, migrations, routes, controllers, Livewire components,
Filament resources, NativePHP plugins, app-store configuration, provider
integrations, policies, jobs, services, tests, API endpoints, local storage
schemas, UI components, CSS, JavaScript, queues, or application logic.

Use this document with [Final Optimized SaaS
Blueprint](final-optimized-saas-blueprint.md), [Acceptance
Principles](acceptance-principles.md),
[Risk Map](risk-map.md), [Testing Strategy
Principles](testing-strategy-principles.md), [Documentation-First
Architecture](documentation-first-architecture.md), [Feature Dependency
Map](feature-dependency-map.md), [API-First
Principles](api-first-principles.md), [Two-System Boundary
Logic](two-system-boundary.md), [Admin/API
Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration
Logic](remote-configuration-logic.md), [Mobile Version Control
Logic](mobile-version-control-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Native Feature
Strategy](native-feature-strategy.md), [NativePHP Runbook](nativephp-run.md),
[Billing And Plan Logic](billing-and-plan-logic.md), [Notifications
Logic](notifications-logic.md), [Offline-First
Principles](offline-first-principles.md), [Sync Lifecycle
Logic](sync-lifecycle-logic.md), [Conflict Resolution
Logic](conflict-resolution-logic.md), [Audit Logic](audit-logic.md), and [Data
Privacy Principles](data-privacy-principles.md): releases must preserve the
documented authority boundaries, tenant boundaries, API contracts, mobile
states, dependency gates, rollback options, and change history before new
behavior reaches users.

## Release Principle Statement

Mobile Lara releases must separate deployment from release. Admin/API code may
be deployed before a feature is visible. Mobile builds may be available in app
stores before every tenant can use their new capabilities. Feature flags, remote
config, app-version policy, tenant scope, plan limits, and admin controls decide
when behavior becomes active.

The release strategy is conservative: ship small, document first, preserve API
compatibility, roll out gradually, monitor impact, and keep a rollback or
disable path for risky behavior. A release is not ready when code exists; a
release is ready when its versioning, rollout, rollback, documentation,
testing, support, audit, and user-visible states are understood.

## Release Ownership Model

| Release surface | Authority owner | Mobile responsibility |
| --- | --- | --- |
| API version | Admin/API | Send supported client context and handle versioned responses. |
| Mobile app version | Admin/API for policy, release owner for store build | Report app version and show update, maintenance, deprecated, or blocked states. |
| Admin release | Platform owner and super admin | Reflect admin-controlled effects only through API context. |
| Feature rollout | Admin/API | Render resolved feature state without local enablement. |
| Rollback | Admin/API and release owner | Return to safe mobile states, preserve local work, and refresh context. |
| App store release | Release owner | Package stable mobile behavior and keep old clients safe. |
| Forced update | Admin/API | Block or guide users only when API policy requires it. |
| Documentation updates | Product and engineering owners | Use docs as release contract before behavior ships. |
| Git change history | Engineering owner | Keep commits atomic, reviewable, and traceable to product decisions. |

## Release Readiness Matrix

| Area | Release principle | Required documentation |
| --- | --- | --- |
| API versioning | API contracts change additively by default and break only through documented version policy. | API version, compatibility rule, deprecation rule, error behavior, and supported mobile versions. |
| Mobile app versioning | App versions are product policy inputs, not device-local authority. | Version name/code, supported platforms, minimum supported version, optional/forced update policy, and store links. |
| Admin release process | Admin changes must be scoped, permissioned, previewed, audited, and rollback-aware. | Admin owner, affected tenant/user/plan/version scope, impact preview, audit expectation, and rollback path. |
| Feature rollout | Features activate through flags/config/plan/version gates, not big-bang visibility. | Rollout stages, audience, success signal, hold signal, rollback trigger, and disabled state. |
| Rollback | Every release needs a safe disable, revert, or forward-fix strategy before launch. | Trigger conditions, rollback owner, user impact, data impact, support message, and audit record. |
| App store release | Store builds assume review delay and slow user adoption. | Build purpose, platform state, review risk, release notes, phased rollout, support path, and old-client behavior. |
| Forced update | Forced updates are exceptional and require impact preview and support recovery. | Reason, affected versions/platforms/tenants, user message, store action, support action, and emergency correction. |
| Documentation updates | Documentation must change with the product decision, not after implementation. | Updated canonical docs, API contracts, changelog, release notes, risk map, and testing strategy. |
| Git history | Commit history is part of release evidence. | Atomic commits, conventional message, scoped diff, changelog entry, and verification notes. |

## API Versioning Principles

API versioning protects the mobile client from accidental contract changes and
protects Admin/API from supporting unknown client assumptions forever.

Principles:

- API contracts are documented before implementation.
- API versions should be stable public contracts, not internal route folders
  only.
- Additive changes are preferred over breaking changes.
- New response fields should not change the meaning of existing fields.
- Existing error categories should remain predictable for supported clients.
- Breaking changes require a documented version boundary, migration path,
  deprecation window, support plan, and old-client behavior.
- Mobile clients should receive enough context to decide whether they are
  supported, deprecated, outdated, blocked, or in limited mode.
- API version policy should protect tenant isolation, permissions, billing,
  feature flags, remote config, sync, conflicts, support, and audit behavior.
- Undocumented behavior should not be treated as a reliable API promise.

Documentation requirements:

- Which API version is affected.
- Whether the change is additive, behavioral, deprecated, or breaking.
- Which mobile app versions can safely consume it.
- Which tenants, plans, roles, features, and sync flows are affected.
- Which error states mobile should display.
- Which API contract documents and testing strategy entries need updates.

## Mobile App Versioning Principles

Mobile app versioning exists because NativePHP clients can remain installed long
after Admin/API evolves. A mobile version is therefore both a release artifact
and an input to Admin/API policy.

Principles:

- Mobile app version, platform, build number, and capability context should be
  reported to Admin/API.
- Admin/API decides whether a version is supported, recommended, deprecated,
  blocked, in maintenance, or requires an update.
- Mobile should not assume that a locally installed feature is available for the
  current tenant, plan, user, or version.
- Old supported clients should receive compatible API responses where possible.
- Version-specific feature availability should be controlled through API
  context, feature flags, remote config, and app-version policy.
- A mobile build should include safe bundled defaults, but Admin/API remains the
  authority for current rules.
- Local drafts, queues, and cache should survive ordinary updates when safe.
- Updates that risk local data loss require explicit documentation and user
  guidance.

Documentation requirements:

- Version naming and version-code principles.
- Supported platforms and platform-specific limitations.
- Minimum supported version and recommended version.
- Optional update, forced update, deprecated, blocked, and maintenance states.
- Store link and update-message ownership.
- Local cache, draft, queue, and secure-storage expectations across update.

## Admin Release Process Principles

Admin releases change the control plane. They can affect tenants, users,
permissions, features, billing, notifications, support, reporting, remote config,
app-version policy, maintenance, and sync behavior.

Principles:

- Admin release notes should describe product impact, not only technical change.
- Admin controls should be released with permission boundaries, impact preview,
  confirmation rules, audit expectations, and support meaning.
- Tenant-scoped controls must remain tenant-isolated.
- Platform-only controls must not become tenant-admin controls by accident.
- Risky admin changes should be staged internally before tenant exposure.
- Admin UI should show when a setting affects mobile behavior.
- Admin releases should keep old mobile clients safe while new controls appear.
- Support teams should understand what changed before tenants encounter it.

Documentation requirements:

- Which admin roles can see or change the new behavior.
- Which tenants, plans, roles, users, devices, versions, or cohorts are affected.
- Which mobile states change.
- Which confirmations, audit records, support views, and rollback options apply.
- Which reports or diagnostics should show release impact.

## Feature Rollout Process Principles

Feature rollout is the controlled activation of capability after deployment.
For this SaaS system, rollout should usually happen through feature flags,
remote config, tenant scope, plan entitlement, role/permission gates, app version
rules, and support readiness.

Principles:

- Deploying code is not the same as enabling a feature.
- Important features should start hidden or disabled until the rollout decision
  is documented.
- Rollout should begin with the smallest useful audience.
- Each rollout stage should have success, hold, and rollback criteria.
- Admins should understand blast radius before enabling a feature.
- Mobile should show disabled, unavailable, read-only, fallback, update-required,
  maintenance, or contact-support states according to the resolved rule.
- Plan limits and tenant state should act as hard ceilings on rollout.
- Rollout should account for offline behavior and stale mobile context.

Documentation requirements:

- Rollout owner.
- Audience and scope.
- Feature flag/config/version/plan dependencies.
- Mobile disabled state.
- Success metrics and support signals.
- Rollback trigger and rollback behavior.
- Cleanup principle after full rollout.

## Rollback Principles

Rollback is a release feature, not an emergency improvisation. Every release
should have a documented path to reduce harm if something goes wrong.

Principles:

- Prefer reversible rollout controls before irreversible release steps.
- Feature flags, remote config, maintenance mode, disabled states, and
  app-version policy should act as fast rollback tools where appropriate.
- API rollback must consider mobile clients that already observed new response
  fields or queued new actions.
- Mobile rollback must consider app-store latency and old clients in the field.
- Data changes may require forward-fix principles when reversal is unsafe.
- Rollback must preserve tenant isolation, local drafts, queued work, audit
  history, and user trust.
- Support and admins should know how to explain rollback states.
- Rollback decisions should be auditable when they affect users, tenants,
  billing, support, sync, notifications, or security.

Documentation requirements:

- What can be disabled immediately.
- What requires redeploy, app-store review, or forward fix.
- What local mobile data is affected.
- What tenant/user/plan/version audience is affected.
- What users and support see.
- What audit event explains the rollback.
- What follow-up documentation must be updated after rollback.

## App Store Release Principles

NativePHP mobile releases must treat app stores as asynchronous distribution
systems. Store review, staged rollout, platform policy, and slow user updates
mean Admin/API must continue to protect older clients.

Principles:

- App-store release timing should not be the only control for feature release.
- Store builds should be compatible with current Admin/API policy before
  submission.
- Store release notes should describe meaningful user impact.
- Review delay and platform-specific approval risk should be expected.
- Staged store rollout should be used for risky mobile changes where available.
- App-store release should be coordinated with API compatibility, remote config,
  feature flags, support readiness, and app-version policy.
- Mobile builds should include safe fallbacks for unavailable NativePHP plugins,
  denied permissions, network changes, and missing config.
- Store rollback is limited; release plans should prefer remote disablement and
  forward fixes for mobile-specific issues.

Documentation requirements:

- Platform and store channel.
- Build purpose and release scope.
- Required API compatibility.
- NativePHP plugin/capability changes.
- Store review risk.
- Phased rollout plan.
- Support message and diagnostics expectations.
- Old-client and rollback behavior.

## Forced Update Principles

Forced updates are exceptional because they block users from productive work.
They are justified only when old clients are unsafe, incompatible, unsupported,
policy-breaking, or likely to corrupt data.

Principles:

- Forced update policy belongs to Admin/API.
- Admins should preview affected versions, platforms, tenants, users, plans,
  features, and support load before enabling forced update.
- Forced update screens should show clear API-provided copy, current version,
  required version, store action, support action, logout path, and retry or
  refresh behavior where safe.
- Critical support, legal, and logout paths should remain available when safe.
- Forced update should not destroy local drafts or queued work without a
  documented data-protection rule.
- Bad forced update policy needs an emergency correction path.
- Forced updates should be audited and support-visible.

Documentation requirements:

- Reason for forced update.
- Affected app versions, platforms, tenants, cohorts, plans, or users.
- Store links and fallback when store links are missing.
- User-facing copy.
- Support handling.
- Local data behavior.
- Emergency rollback or correction principle.

## Documentation Update Requirements

Documentation is part of release readiness. A release that changes product
behavior without updating the relevant Markdown creates hidden architecture.

Principles:

- Product documentation should change before implementation or release.
- API contract docs should change with API behavior.
- Mobile version docs should change with app-version policy.
- Feature flag docs should change with rollout behavior.
- Remote config docs should change with configurable behavior.
- Risk map entries should change when a release creates or reduces risk.
- Testing strategy docs should identify the future or required test surfaces.
- Changelog entries should summarize release-relevant product decisions.
- ADRs should be added or updated when a release decision is expensive to
  reverse.
- Agent-facing rules should be updated when future work must follow a new
  constraint.

Documentation requirements:

- Updated canonical feature or principle doc.
- Updated API contract docs when API behavior changes.
- Updated mobile/admin docs when user-visible behavior changes.
- Updated risk, testing, and acceptance docs when release readiness changes.
- Updated changelog entry before commit.
- Clear statement of what remains intentionally out of scope.

## Git Commit And Change History Principles

Git history is part of the release record. It should explain what changed,
why it changed, and whether the change was documentation, implementation,
tests, configuration, or release process.

Principles:

- Commits should be atomic and scoped to one logical change.
- Documentation-only releases should not include unrelated code, tests,
  migrations, generated files, or local WIP.
- Commit messages should follow Conventional Commit style.
- Changelog and docs should be committed with the release planning change they
  describe.
- Implementation commits should be separate from unrelated formatting,
  refactoring, or documentation-only cleanup.
- Reverts should identify the reverted behavior and preserve follow-up context.
- Breaking API or release-policy changes should be obvious in commit history.
- Release branches are acceptable for stabilization; long-lived feature branches
  should be avoided when feature flags can protect incomplete work.
- Change history should allow support, admins, and future agents to answer what
  changed, when, why, and how to recover.

Documentation requirements:

- Commit message type and scope.
- Changelog entry.
- Release notes when users, admins, or support need to understand impact.
- ADR link when a release decision is expensive to reverse.
- Verification evidence appropriate to the change type.
- Explicit note when tests are not run because the change is documentation-only.

## Release Safety Checklist

Before release planning is accepted, the team should be able to answer:

- What API version or contract is affected?
- Which mobile app versions are supported, deprecated, blocked, or required?
- Which admin controls are new or changed?
- Which feature flags, remote config, plan rules, tenant rules, or version rules
  gate the release?
- What is the smallest safe rollout audience?
- What is the rollback trigger?
- What can be disabled remotely?
- What requires redeploy, store review, or forward fix?
- What happens to offline drafts, queues, cache, sync, and conflicts?
- What support, audit, reporting, billing, privacy, and notification effects
  exist?
- Which documentation files must change before release?
- Which future tests or existing tests should prove release safety?
- Which Git commits and changelog entries tell the change history?

If these answers are missing, the release is not ready. The next step is
documentation and release planning, not code or app-store submission.
