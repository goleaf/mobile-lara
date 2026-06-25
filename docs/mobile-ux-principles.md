# Mobile UX Principles

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

Updated: 2026-06-26

This document defines mobile UX principles for the Mobile Lara NativePHP client. It explains mobile-first navigation, simple screens, clear loading states, clear offline states, thumb-friendly controls, minimum typing, fast actions, feature visibility based on admin rules, secure session behavior, and permission education before native permission prompts. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, NativePHP plugins, policies, jobs, services, providers, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [API-First Principles](api-first-principles.md), [Documentation-First Architecture](documentation-first-architecture.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version Control Logic](mobile-version-control-logic.md), [Admin Safety Principles](admin-safety-principles.md), and [Mobile And Admin Design System](design-system.md): mobile UX is the NativePHP client expression of API authority, local resilience, simple workflows, secure sessions, offline honesty, feature-controlled visibility, and native capability education.

## UX Statement

The NativePHP mobile client should feel fast, calm, safe, and task-focused.

Mobile users should not need to understand tenant policy, rollout cohorts, billing internals, permission matrices, remote config, support operations, sync mechanics, or app-version rules. The mobile UX should translate Admin/API decisions into clear navigation, clear states, clear actions, and clear recovery paths.

Product rule: mobile UX is local execution, not SaaS authority. The client may make work feel immediate through cache, drafts, queues, native capture, and responsive loading states, but protected access, final sync acceptance, tenant scope, feature availability, billing entitlement, and permission decisions still come from the API.

## Mobile UX Decision Contract

Every mobile screen or NativePHP capability should satisfy this contract before implementation planning.

| UX area | Principle | Required outcome |
| --- | --- | --- |
| Mobile-first navigation | Navigation should prioritize the few workflows a mobile user performs most often. | Users can reach primary tasks with one thumb, see account/tenant/status context, and avoid admin-style navigation depth. |
| Simple screens | Each screen should focus on one job and one obvious next action. | Mobile users see product language, not raw policy, raw config, or admin machinery. |
| Clear loading states | Every API, Livewire, sync, native capability, and long-running local action needs honest feedback. | Users know whether the app is loading, saving, syncing, retrying, waiting for native permission, or blocked. |
| Clear offline states | Offline behavior should distinguish cached, draft, pending, queued, synced, stale, conflict, failed, blocked, and read-only states. | Users know what is safe to do offline and what still needs API confirmation. |
| Thumb-friendly controls | Primary actions should be reachable, large enough, spaced clearly, and safe-area aware. | Users can operate the app one-handed without accidental destructive actions. |
| Minimum typing | Mobile data entry should prefer selection, scanning, capture, defaults, recent values, drafts, and progressive input over long text entry. | Users spend effort on the work, not on fighting a keyboard. |
| Fast actions | Common actions should feel immediate while still respecting API authority and idempotent sync. | Users can complete frequent tasks quickly and still see pending, success, conflict, or rollback-safe feedback. |
| Feature visibility | Visible, hidden, disabled, blocked, deprecated, update-required, offline-limited, and maintenance states should come from admin/API rules. | Mobile never exposes features that the current tenant, user, plan, role, app version, device, or feature flag cannot safely use. |
| Secure session | Local session UX should protect the device experience while allowing the server to revoke access. | Users understand locked, authenticated, expired, revoked, offline-limited, and logout states without storing secrets in local cache. |
| Permission education | Native permission prompts should be preceded by plain explanation of value, timing, and fallback. | Users understand why camera, scanner, location, microphone, biometrics, notifications, secure storage, or file access is requested before the OS prompt appears. |

This contract is intentionally principle-level. It does not create screens, components, routes, APIs, NativePHP plugin calls, storage schemas, policies, or implementation logic.

## Mobile-First Navigation

Mobile navigation should start from the user's real work, not from the admin domain model.

Principles:

- Put the highest-frequency workflows within one or two taps.
- Keep primary navigation short and stable.
- Use bottom navigation, top app context, tabs, or task shortcuts only where they match mobile use.
- Keep tenant, account, sync, offline, notification, and support state reachable without turning every screen into a dashboard.
- Preserve task continuity when users move between capture, review, draft, sync, and support states.
- Avoid admin-style nested menus, dense tables, and multi-column operations on the mobile client.
- Keep dangerous, destructive, billing, support, or tenant-switching actions out of accidental thumb zones.
- Let Admin/API feature state decide which navigation items are hidden, visible, disabled, blocked, update-required, or maintenance-limited.

Navigation should answer: where am I, what can I do now, what needs attention, and what is blocked by policy or connectivity?

## Simple Screens

Simple screens reduce mobile cognitive load.

Principles:

- Each screen should have one primary purpose.
- Each screen should expose one obvious primary action when action is possible.
- Secondary actions should be contextual, not spread across the screen.
- Use progressive disclosure for advanced details such as diagnostics, support IDs, stale cache, sync conflicts, or permission explanations.
- Avoid showing raw feature flag names, billing provider states, permission matrices, tenant config, API error internals, or rollout mechanics.
- Use product states such as unavailable, contact admin, contact support, update required, offline only, read-only, retry later, conflict, or pending.
- Keep pre-login, invited, suspended, force-update, and maintenance screens minimal and clear.
- Prefer list/detail, step-by-step capture, review-before-submit, or single-task screens over admin dashboards.

A mobile screen is not simple if the user must understand how Admin/API made the decision before knowing what to do next.

## Clear Loading States

Loading states should be specific enough to build trust without making the interface feel busy.

Principles:

- Show skeletons or structured placeholders for content loading.
- Show button-level or action-level loading for submits, saves, retries, sync, uploads, captures, scans, permission checks, and support actions.
- Avoid full-screen blocking loaders unless the whole app context is truly unavailable.
- Distinguish loading from saving, syncing, retrying, refreshing, uploading, processing, and waiting for native permission.
- Avoid flickering indicators for very fast work.
- Disable duplicate submissions while an action is in progress.
- Keep the previous useful state visible when refreshing where safe.
- Never show success until API or accepted local policy confirms the action outcome.
- If an action moves offline into a queue, show pending or queued instead of pretending it synced.

Livewire loading behavior, NativePHP events, API requests, and sync replay should all have visible UX states where the user can be affected.

## Clear Offline States

Offline state should be honest and useful, not alarming by default.

Principles:

- Show whether the app is online, offline, reconnecting, or using last-known data where the distinction changes what the user may do.
- Label stale cached data when freshness matters.
- Distinguish local draft, queued intent, pending sync, synced, failed, retry-later, blocked, and conflict states.
- Make offline-capable features clear: read-only offline, draft-only offline, queueable offline, or online-only.
- Do not accept protected work offline unless the feature's API/sync contract allows it.
- Recheck tenant, user, role, permission, billing, feature, app-version, maintenance, and conflict policy before replaying queued work.
- Keep support guidance visible for repeated sync failure or unresolved conflicts.
- Never let offline mode bypass force-update, suspended-user, disabled-tenant, revoked-session, or emergency-disabled feature policy after API revalidation.
- Preserve local drafts where safe during outages, maintenance, update-required states, and session recovery.

Offline UX should help users continue safe work and understand what remains local.

## Thumb-Friendly Controls

The NativePHP client should be comfortable in one hand.

Principles:

- Use large touch targets for primary actions, destructive actions, navigation items, native capture, and form controls.
- Keep spacing generous enough to avoid accidental taps.
- Place primary actions where thumbs can reach them without covering important content.
- Keep destructive actions separated from common actions and behind confirmation where risk warrants it.
- Respect safe areas, notches, home indicators, keyboard height, and platform navigation patterns.
- Avoid tiny text links as the only path to important actions.
- Prefer native controls where they improve reliability and familiarity.
- Keep button labels short and action-oriented.
- Ensure loading, disabled, and blocked controls remain visually clear without relying only on color.

Thumb-friendly design is a safety principle as much as a comfort principle.

## Minimum Typing

Mobile typing should be minimized because field work often happens under time, lighting, motion, or connectivity constraints.

Principles:

- Prefer selection, scanning, camera capture, voice capture, location capture, recent values, defaults, templates, and drafts where they fit the workflow.
- Use short forms and split long forms into task-sized steps.
- Ask only for information needed now.
- Use sensible defaults from API context, tenant settings, feature config, or prior safe local state.
- Save drafts automatically where safe and explain whether a draft is local or synced.
- Use validation at the right time: immediate for simple format issues, on blur or submit for heavier checks, and server confirmation for authority.
- Avoid forcing repeated entry of tenant, user, device, or context data the API already knows.
- Allow correction and review before final submit.
- Do not request native capture, location, microphone, camera, or scanner input unless the feature is enabled and the user understands why it helps.

Minimum typing should not become minimum validation. The API still owns final acceptance.

## Fast Actions

Fast actions should make frequent work feel lightweight while preserving trust.

Principles:

- Keep common actions close to the content they affect.
- Use one-tap actions for safe, reversible, or low-risk operations.
- Use review, confirmation, or reason capture for destructive, broad, billing, support, or tenant-sensitive actions.
- Use optimistic feedback only when rollback, retry, and API rejection states are clear.
- Queue offline actions only when the sync contract allows replay.
- Make retry, undo, edit draft, contact support, refresh, update, and logout actions easy to find when relevant.
- Avoid repeating loading and confirmation steps for every small action when the risk is low.
- Keep action labels concrete: save draft, sync now, retry upload, update app, contact support.
- Preserve task context after actions so users do not lose their place.

Fast does not mean hidden. Users should always know what happened and what still needs confirmation.

## Feature Visibility Based On Admin Rules

Mobile feature visibility should be resolved through Admin/API rules.

Principles:

- Mobile receives resolved feature states through API, not raw admin rule layers.
- Hidden features should disappear when users have no useful reason to know they exist.
- Disabled features should explain next action when the user expects access.
- Blocked features should name the safe reason category: permission, plan, tenant, maintenance, update, offline, support, or unavailable.
- Update-required features should connect to app-version policy and safe store/distribution guidance.
- Offline-limited features should show read-only, draft-only, queueable, or online-only behavior.
- Emergency-disabled features should fail closed with support-safe copy.
- Feature visibility must account for tenant status, user status, role, permission, plan, feature flag, remote config, app version, device capability, maintenance, and safety policy.
- Cached feature state should be labeled as last-known when offline and must be refreshed before protected actions.

Mobile should help the user understand the product outcome without exposing admin internals.

## Secure Session Behavior

Secure session UX should protect the user's device without claiming server authority.

Principles:

- Keep authentication, session validity, token revocation, device trust, tenant access, and account state under Admin/API authority.
- Use secure local storage for secrets and tokens; local SQLite or cache should not store secrets.
- Local lock, biometric unlock, or app resume checks can protect the device experience but cannot override server revocation.
- Show clear states for signed out, session expired, session revoked, offline-limited, locked, suspended, invited, force-update, and maintenance.
- Let users log out, clear local session state, and recover when API policy allows.
- Protect sensitive cached screens when the app resumes, switches user, changes tenant, or loses session authority.
- Avoid showing tenant data after logout, session revocation, account suspension, tenant disablement, or app-version block.
- Revalidate server authority before syncing protected queued actions after reconnect or app resume.
- Make support paths clear when a user cannot recover locally.

Secure session behavior should be quiet during normal use and explicit during risk or recovery.

## Permission Education Before Native Prompts

Native permissions are trust moments.

Principles:

- Explain why a permission is needed before triggering the OS prompt.
- Ask just in time, near the workflow that needs the capability.
- Request native permissions only when the feature is enabled for the current user, tenant, plan, role, device, and app version.
- Use plain product language: scan code, attach photo, record note, share file, verify identity, receive alerts, use current location.
- Explain what happens if the user declines.
- Provide a fallback path when possible.
- If permission is denied, show recovery guidance and settings direction where appropriate.
- Do not repeat prompts aggressively after denial.
- Do not ask for broad capability access during onboarding unless it is essential to the first task.
- Keep permission copy configurable only within safe remote-config boundaries and never use it to hide security or privacy meaning.

Permission education should increase user trust, not pressure the user into accepting.

## NativePHP And Livewire UX Boundaries

NativePHP and Livewire are chosen to make mobile workflows Laravel-first while giving access to native capabilities.

Principles:

- NativePHP capabilities should appear as product actions, not technology demonstrations.
- Livewire screens should keep server interaction clear through loading, validation, disabled, error, and retry states.
- Native capability results should flow back into the task state as draft, pending, uploaded, failed, blocked, or synced where relevant.
- Device features should not bypass Admin/API permission, feature, billing, version, or tenant rules.
- Native permission prompts should be part of the screen journey and not surprise users outside context.
- Mobile UI should stay responsive when an API request, native event, file upload, scan, capture, or sync replay is in progress.

The mobile client may feel native, but the product remains API-first.

## Key Mobile Flows

| Flow | UX principle |
| --- | --- |
| Guest/pre-login | Minimal navigation, clear login/register/recovery path, no tenant data, no unnecessary native permission prompts. |
| Invited user | Clear invitation state, next action, tenant context where safe, and no normal workflow access until accepted/activated. |
| Suspended user | Clear blocked state, support or tenant-admin next action, local data protection, and no protected work. |
| Active mobile user | Simple navigation, clear feature visibility, local drafts, sync status, and fast task actions. |
| Offline user | Last-known state, safe offline options, draft/queue clarity, stale labels, and replay expectations. |
| Old app version | Optional update, deprecated, force-update, blocked, or maintenance state according to API policy. |
| Permission request | Pre-prompt education, native prompt, fallback/denied guidance, and settings recovery where allowed. |
| Session recovery | Locked, expired, revoked, offline-limited, logout, retry, support, or reauthenticate state. |
| Sync conflict | Clear affected item, server decision or user choice, retry/support path, and no hidden data loss. |

## Risk Register

| Risk | UX principle |
| --- | --- |
| Mobile mirrors admin complexity | Keep navigation short, screens single-purpose, and policy translated into next actions. |
| Users think local draft is synced | Distinguish draft, pending, queued, synced, failed, conflict, and stale states. |
| Loading hides real state | Use specific loading/saving/syncing/retrying labels and avoid false success. |
| Offline mode bypasses authority | Revalidate through API before protected replay and label last-known/cached state. |
| Feature visibility leaks admin internals | Return resolved feature states and use safe reason categories. |
| Permission prompts feel invasive | Explain value first, request just in time, and provide denial recovery. |
| Secure session feels confusing | Separate local lock, authenticated, expired, revoked, suspended, and offline-limited states. |
| Fast actions cause accidental damage | Use confirmation and separation for destructive or high-risk actions. |
| Native capability becomes a side channel | Treat native capture, location, scan, microphone, share, and file access as API-governed feature behavior. |

## Success Test

Mobile UX is working when a mobile user can answer these questions without knowing the admin system:

- What can I do now?
- What is blocked, disabled, offline, pending, synced, failed, conflicted, or update-required?
- Is this information current or last-known?
- Is this action local, queued, or accepted by the API?
- Why is the app asking for this native permission?
- What should I do if the session expires, the app is offline, or the feature is unavailable?

If the user needs admin knowledge to answer those questions, the mobile UX is not simple enough.
