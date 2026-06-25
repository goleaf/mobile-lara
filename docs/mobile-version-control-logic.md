# Mobile Version Control Logic

Updated: 2026-06-25

This document defines the mobile app version control logic for Mobile Lara. It explains how admins control minimum supported versions, how optional updates work, how forced updates work, how maintenance mode works, how mobile behaves when API says the app is outdated, how store links and update messages are controlled, and how users are protected from broken old versions. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, Filament resources, policies, jobs, services, providers, or application logic.

Use this document with [Product Vision](product-vision.md): version control
protects the vision by keeping old mobile builds from breaking central
authority or local resilience.

Use this document with [Product Positioning](product-positioning.md): version
policy is part of the feature-controlled, API-first mobile platform posture, not
a device-local preference.

Use this document with [Core Product Principles](product-principles.md): mobile
version rules are admin-controlled, API-first, secure-by-default, tenant-aware,
documented, and modular.

Use this document with [Target User Roles](user-roles.md): version prompts,
maintenance access, internal-only access, support visibility, and suspended or
guest behavior must follow role and account-state boundaries.

Use this document with [SaaS Value Map](saas-value-map.md): version policy must
protect stakeholder value by preserving platform rollout control, tenant
continuity, mobile-worker trust, support diagnosability, and billing/operations
clarity.

Use this document with [Two-System Boundary Logic](two-system-boundary.md):
Admin/API decides version safety, while mobile reports build context and
presents update, limited-mode, maintenance, deprecated, or blocked states.

## Version Control Statement

Mobile app version control is a control-plane responsibility.

The Admin/API system decides whether a mobile build is current, supported, recommended for update, deprecated, blocked, forced to update, maintenance-limited, internal-only, or incompatible with a required API contract. The mobile client reports its app version, build identity, platform, release channel, and relevant capability context to the API, then follows the server's resolved version policy.

Mobile may present update prompts, store links, release notes, maintenance banners, limited-mode screens, blocked states, and support guidance. It must not decide that an old build is safe after the API says it is unsafe.

Product rule: app-version policy protects users and tenants from stale mobile assumptions, broken API contracts, missing NativePHP capability support, known security issues, unsafe sync behavior, and unsupported release channels.

## What Admins Control

Admins should control mobile version policy through scoped, authorized, auditable settings.

Version controls should include:

- **Minimum supported version** - the oldest mobile version that may still operate normally.
- **Minimum recommended version** - the oldest version that may continue but should receive an optional update prompt.
- **Blocked version** - a version or range that must not operate because it is unsafe, broken, vulnerable, or contract-incompatible.
- **Forced update rule** - a policy that blocks normal operation until the user updates.
- **Optional update rule** - a policy that recommends updating without blocking normal operation.
- **Maintenance mode** - a platform, tenant, feature, API, sync, or notification maintenance state that changes mobile behavior temporarily.
- **Store or distribution link** - the safe destination for update action by platform, channel, tenant, region, or deployment model.
- **Update message** - user-facing copy explaining why an update is recommended or required.
- **Support message** - role-safe context support can use to explain version state without exposing private internals.
- **Grace period** - a planned window where old versions continue with warning before stronger enforcement.
- **Effective time** - when a version rule starts, changes, or ends.
- **Rollback expectation** - how admins recover if a version rule blocks too much or creates support load.

Admin controls should be scoped by platform, release channel, tenant, feature risk, API contract, NativePHP capability, security exposure, sync risk, rollout cohort, or emergency status.

Tenant-specific version policy may narrow or clarify global policy, but it must not make globally unsafe versions safe. Global security, API compatibility, and emergency blocks are the ceiling.

## Version States

The API should resolve version policy into clear mobile-safe states.

| State | Meaning | Mobile behavior |
| --- | --- | --- |
| Current | The app is on the expected version or newer. | Operate normally. |
| Supported | The app is allowed to operate even if it is not latest. | Operate normally, possibly with no prompt. |
| Optional update | A better version exists, but current use remains safe. | Show a dismissible or scheduled update prompt. |
| Recommended update | The app should update soon because support, UX, or feature compatibility is improving. | Show a stronger non-blocking prompt and release note. |
| Deprecated | The app still works, but support is ending. | Warn clearly, reduce risky feature access if policy requires it, and guide update. |
| Force update | The app cannot safely continue normal operation. | Block normal workflows and show update action. |
| Blocked | This version, platform, build, or channel must not operate. | Fail closed with support-safe message and update/support action. |
| Maintenance | Service, tenant, feature, API, sync, or notification behavior is temporarily limited. | Show maintenance state, retry timing, limited mode, or blocked action. |
| Internal-only | Version is allowed only for internal, test, support, or release roles. | Block normal users; allow authorized internal use only through API policy. |
| Unknown/unverified | The API cannot safely verify version state. | Fail conservatively for protected workflows and request refresh/support. |

Mobile should not expose raw policy names, admin notes, vulnerability details, store credentials, tenant-private reasoning, or internal release mechanics. It should show a clear product outcome and next action.

## Minimum Supported Versions

Minimum supported versions protect the platform from old clients that no longer match business, API, security, or NativePHP assumptions.

Principles:

- Minimum version rules should be set by Admin/API, not hardcoded only in the app.
- Minimum rules should be platform-aware because iOS, Android, desktop dev shells, internal builds, and NativePHP channels can differ.
- Minimum rules should be API-contract-aware because old clients may call stale endpoints or expect stale response shapes.
- Minimum rules should be security-aware because vulnerable or compromised builds may need immediate blocks.
- Minimum rules should be sync-aware because old queue formats, conflict behavior, or offline assumptions can become unsafe.
- Minimum rules should be tenant-aware only inside global safety limits.
- Minimum rules should be documented with owner, reason, affected users, affected tenants, expected mobile behavior, support message, audit expectation, and rollback path.

Admins should prefer a planned path:

1. Announce or document the upcoming minimum version change.
2. Move old versions into optional or recommended update state.
3. Move unsupported versions into deprecated state with a grace period where safe.
4. Force update or block only when the risk justifies it.
5. Keep support and reports able to explain who is affected.

Emergency security, data integrity, or API-compatibility incidents can skip the grace period, but the reason and support path should still be recorded.

## Optional Updates

Optional updates are for useful improvements that do not require blocking the current version.

Use optional updates when:

- A new version improves UX, performance, reliability, translations, NativePHP behavior, or support diagnostics.
- A feature works better on the new version but the old version remains safe.
- A tenant or cohort should be nudged to upgrade before a planned deprecation.
- Release notes or support guidance should be visible without interrupting work.

Mobile behavior:

- Continue normal operation.
- Show a non-blocking update prompt at appropriate moments.
- Let users dismiss, postpone, or continue when policy allows.
- Avoid interrupting active offline work, critical tasks, forms, captures, uploads, or sync recovery unless policy says the version is becoming unsafe.
- Keep update prompts honest: optional means the user may continue.
- Refresh version policy after app resume, login, tenant switch, support flow, or a meaningful time interval.

Optional updates should include platform-specific store links, release notes, support message, and a planned escalation path if the version will later become deprecated or forced.

## Forced Updates

Forced updates protect users, tenants, and the platform when old builds are unsafe.

Use forced updates when:

- The app version calls an API contract that is no longer safe to serve.
- The build has a known security issue.
- Offline queue or sync behavior can corrupt, duplicate, leak, or lose data.
- A NativePHP capability changed in a way old builds cannot handle safely.
- Billing, permission, tenant, or feature enforcement changed and old clients cannot represent the correct outcome.
- The release channel or build is invalid, revoked, or internal-only.
- Emergency incident response requires blocking old behavior.

Mobile behavior:

- Stop normal workflows.
- Show a clear required-update state.
- Provide the correct store or distribution link.
- Preserve safe local state where possible without granting new authority.
- Allow only safe utility actions such as update, logout, support contact, local diagnostics, or viewing limited cached guidance if policy allows.
- Prevent new protected actions and avoid replaying queued writes until updated and revalidated through API.
- Recheck API policy after update before resuming normal work.

Forced update should be support-visible and auditable. Admins should know why the block exists, who is affected, whether the block is global or scoped, and how to roll back if the rule was too broad.

## Maintenance Mode

Maintenance mode protects the product during planned work, incidents, migrations, provider outages, or operational recovery.

Maintenance can apply to:

- The whole platform.
- One tenant or tenant group.
- One mobile feature.
- One API contract group.
- Sync replay or offline acceptance.
- Notification delivery.
- Billing or entitlement checks.
- Support or diagnostics.
- A release channel, platform, or app version range.

Admin principles:

- Maintenance should define scope, reason, start time, expected end time, affected workflows, user-facing message, support message, retry guidance, and rollback path.
- Planned maintenance should avoid surprise where possible.
- Emergency maintenance should fail closed for protected operations.
- Maintenance state should be delivered through API, not inferred locally.
- Maintenance should not leak tenant-private operational details to unrelated users.

Mobile behavior:

- Show a clear maintenance state when API reports it.
- Block affected actions with retry-later or limited-mode guidance.
- Continue unaffected local presentation only when policy allows.
- Treat offline actions during maintenance as draft-only, queueable, blocked, or read-only according to API policy.
- Recheck policy before replaying queued work after maintenance ends.

Maintenance is different from forced update. Maintenance means the service or workflow is temporarily limited; forced update means the client version itself is unsafe or unsupported.

## Outdated API Response Behavior

When the API says the app is outdated, mobile must treat that response as authoritative.

Mobile should:

- Stop relying on cached feature, config, permission, billing, and sync state for protected actions.
- Show the resolved version state: optional update, recommended update, deprecated, force update, blocked, maintenance, or retry later.
- Show the API-provided safe message and next action.
- Use the API-provided store or distribution link for the current platform/channel.
- Preserve local drafts and queued intents where possible, but avoid replay until the app is updated, policy is refreshed, and server rules accept the action.
- Avoid creating new protected work if the version state is force update, blocked, unknown, or maintenance-blocked.
- Recheck boot/context after update before restoring normal navigation.

The API should return predictable user-facing error categories such as `version`, `stale_client`, `maintenance`, `blocked`, or `retry_later` so mobile can present the correct state without guessing.

## Store Links And Update Messages

Store links and update messages are controlled by Admin/API because they affect user trust and recovery.

Principles:

- Store links should be platform-specific and safe for the release channel.
- Update messages should be scoped by state: optional, recommended, deprecated, required, blocked, maintenance, or internal-only.
- Messages should explain what the user needs to do, not expose internal implementation details.
- Support messages can contain more context than mobile user messages, but still must avoid secrets and sensitive incident details.
- Links and messages should be versioned or revisioned so support can know what the user saw.
- Tenant-specific copy may clarify local policy, but it cannot bypass global safety blocks.
- Remote configuration may tune safe wording, but version policy decides whether the update is optional, forced, blocked, or maintenance-limited.

Update messages should be short, actionable, and honest:

- Optional update: "A newer version is available."
- Recommended update: "Update soon for the best experience."
- Deprecated: "This version will stop being supported."
- Force update: "Update required to continue."
- Blocked: "This app version is no longer supported."
- Maintenance: "Service is temporarily unavailable. Try again later."

Exact copy can vary by product tone, tenant, locale, or platform, but the underlying state must come from Admin/API.

## Protecting Users From Broken Old Versions

Version control protects users by preventing old app assumptions from creating confusing, unsafe, or data-damaging behavior.

Protection principles:

- Do not let old clients call removed or incompatible API behavior as if it still works.
- Do not accept offline replay from old queue formats when the server cannot safely interpret them.
- Do not let stale feature, permission, config, billing, or tenant assumptions authorize protected work.
- Do not expose broken NativePHP capability flows when the app version lacks safe handling.
- Do not silently degrade security behavior.
- Do not hide critical update requirements behind dismissible prompts.
- Do not expose vulnerability details in user-facing messages.
- Do not strand users without a next action, store link, support path, or retry guidance.

The safest old-version experience is explicit: the user understands whether they can continue, should update soon, must update now, should wait for maintenance, or must contact support.

## Offline Version Behavior

Offline behavior must be conservative because the latest version policy may be unknown.

Principles:

- If mobile has a fresh cached policy that says the version is supported, it may continue policy-allowed offline work until freshness expires.
- If cached policy says force update or blocked, mobile should keep blocking even offline.
- If cached policy is missing, expired, invalid, or unknown, mobile should avoid starting protected workflows and ask for connection or support.
- Offline drafts may be preserved locally, but replay must wait for online policy revalidation.
- Offline queue replay must recheck app version, tenant status, permissions, feature flags, remote config, billing/entitlement, maintenance, and current server state.
- Mobile should label stale version policy honestly when it affects user choices.

Offline-first does not override app-version safety.

## Admin Safety Model

Admins should be able to change version policy without accidentally blocking the wrong users.

Safe admin changes should include:

- Affected platform, app version, build, channel, tenant, feature, cohort, or user count.
- Current state and target state.
- Reason for the change.
- Expected mobile behavior.
- Store links and update messages.
- Support message and escalation path.
- Planned effective time and grace period.
- Compatibility with API contracts, feature flags, remote config, NativePHP capabilities, sync behavior, billing, and security requirements.
- Audit trail and rollback path.

High-impact controls such as forced update, blocked version, platform maintenance, or emergency disablement should require stronger confirmation and should be visible to support.

## Relationship To Feature Flags And Remote Config

Version control is related to feature flags and remote config, but it is not the same thing.

- Feature flags decide whether a feature is available for a user, tenant, plan, version, device, cohort, or emergency state.
- Remote config tunes safe runtime values such as copy, limits, thresholds, workflow options, notification presentation, support prompts, and tenant presentation.
- Version control decides whether the mobile build itself is safe to operate, update, or block.

Version policy is a higher-safety gate. A feature flag or remote config value cannot make a globally blocked app version safe.

## Admin Checklist

Use this checklist before planning app-version policy.

| Question | Required answer |
| --- | --- |
| What version state applies? | Current, supported, optional update, recommended update, deprecated, force update, blocked, maintenance, internal-only, or unknown/unverified. |
| Who controls it? | Platform owner, super admin, release manager, support escalation, tenant admin inside limits, or another documented role. |
| What scope applies? | Platform, tenant, user, role, device, app version, build, platform, release channel, feature, API contract, cohort, or maintenance scope. |
| Why is the policy needed? | Security, API compatibility, NativePHP capability, sync safety, supportability, rollout, billing/permission correctness, or incident response. |
| What does mobile show? | Prompt, warning, limited mode, blocked screen, maintenance screen, support path, store link, or retry state. |
| What can continue? | Normal operation, limited read-only use, drafts, support, logout, diagnostics, or nothing beyond update flow. |
| What happens offline? | Continue with fresh supported policy, block with cached forced policy, preserve drafts, or require online revalidation. |
| What support sees? | Version state, policy reason, store link, message revision, affected scope, sync impact, and safe escalation path. |
| What is the rollback path? | Revert to previous policy, extend grace period, change store link/message, limit by cohort, or switch to maintenance. |
| What is out of scope? | Database fields, migrations, endpoints, controllers, policies, resources, services, jobs, and code remain deferred until implementation. |

## Risks

| Risk | Version-control response |
| --- | --- |
| Old clients call stale APIs | Use minimum supported versions, additive contracts, deprecation windows, and stale-client errors. |
| Forced update blocks too many users | Preview affected scope, use phased rollout where safe, keep rollback ready, and expose support context. |
| Optional update feels mandatory | Keep optional copy honest and allow continuation when policy says continuation is safe. |
| Maintenance is confused with version block | Separate service/workflow maintenance from unsafe app-version states. |
| Store link is wrong | Scope links by platform/channel and validate before publishing policy. |
| User loses work during update | Preserve drafts and queues where safe; revalidate before replay after update. |
| Stale cached policy grants access | Treat cached policy as presentation guidance only for protected work; recheck API before server-trusted actions. |
| Tenant override bypasses global safety | Global security, API compatibility, blocked version, and emergency rules win over tenant preferences. |
| Support cannot explain state | Version policy must include safe reason, affected scope, message revision, and next action. |
| Vulnerability details leak | User-facing copy stays generic; sensitive incident context remains role-scoped. |

## Success Test

Mobile version control is successful when admins can safely define minimum supported versions, optional updates, forced updates, maintenance windows, store links, and update messages; the API resolves those decisions into predictable mobile-safe states; mobile follows outdated, blocked, or maintenance responses without guessing; users keep safe local work where possible; support can explain what happened; and broken old versions cannot continue unsafe behavior.
