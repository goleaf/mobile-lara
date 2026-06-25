# Mobile And Admin Design System

Updated: 2026-06-25

This document defines UI principles for the two-system product:

1. **Admin/API system** - Livewire admin panel for SaaS operations.
2. **Mobile client system** - Livewire mobile app inside NativePHP.

The two interfaces should feel related but serve different jobs. Admin is an operational control plane. Mobile is a resilient task-focused client.

The design system supports the [Product Vision](product-vision.md): admin users need control, visibility, and audit context; mobile users need clear permitted workflows, native-feeling interaction, and honest offline/sync states.

It also supports the [Product Positioning](product-positioning.md): the admin UI should feel like a SaaS control center, and the mobile UI should feel like a workforce/client platform. The interfaces are related, but they should not collapse into a web-only dashboard or a mobile-only app.

UI decisions must follow [Core Product Principles](product-principles.md): admin controls policy, mobile never presents local state as server authority, disabled features are clear, tenant scope is visible where relevant, secure defaults are not hidden behind UI-only affordances, and mobile UX stays simple.

UI decisions must follow [Documentation-First Architecture](documentation-first-architecture.md): every admin control documents its mobile effect, every mobile screen documents its API dependency, every sync state documents online/offline behavior, and every UX risk is recorded before coding.

UI decisions must follow [Admin Control Center Logic](admin-control-center-logic.md): admin controls for tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance, force update, sync, notifications, reports, billing, and support must show scope, consequence, support meaning, and API-derived mobile effect.

UI decisions must follow [Feature Flag Logic](feature-flag-logic.md): feature-controlled surfaces must distinguish hidden, visible, disabled, blocked, beta, deprecated, update-required, offline-limited, and emergency-disabled states without exposing raw flag internals.

UI decisions must follow [Remote Configuration Logic](remote-configuration-logic.md): remotely configured copy, limits, workflow options, offline/sync messaging, support prompts, and tenant presentation must have safe defaults and invalid-config fallbacks.

UI decisions must follow [Mobile Version Control Logic](mobile-version-control-logic.md): optional update, forced update, maintenance, deprecated, blocked, store-link, update-message, support, and stale-client states must be clear without exposing raw admin policy.

UI decisions must follow [Admin Safety Principles](admin-safety-principles.md): dangerous admin actions should show confirmation, audit context, impact before save, mobile impact preview, rollback meaning, and tenant-isolated scope in product language.

UI decisions must also follow [Target User Roles](user-roles.md): each role should see the controls, diagnostics, billing context, support context, tenant scope, or pre-login state that matches its job.

UI decisions must also express the [SaaS Value Map](saas-value-map.md): platform owners need high-level control and risk visibility, tenant admins need scoped management, mobile workers need simple next actions, support needs safe diagnostics, and billing/operations needs entitlement clarity without tenant workflow overreach.

UI decisions must also express [Two-System Boundary Logic](two-system-boundary.md): admin surfaces show authority and scope, while mobile surfaces show API-derived capability state, local freshness, pending work, conflicts, and offline limits without pretending to own server truth.

UI decisions must also express [API-First Principles](api-first-principles.md): mobile UI should present predictable API states, operating context, mobile-friendly errors, sync/conflict outcomes, and tenant-safe feature visibility without exposing internals.

UI decisions must also express [Admin/API Responsibilities](admin-api-responsibilities.md): admin UI exposes scoped control-plane responsibilities, while mobile UI presents only the resulting capability, version, notification, billing, support, report, audit, conflict, and security states.

UI decisions must also express [Mobile Client Responsibilities](mobile-client-responsibilities.md): mobile UI owns experience, local session presentation, cache/freshness signals, offline queues, NativePHP permission UX, navigation, sync display, drafts, local feedback, and feature visibility.

## Shared Principles

- Keep UI in Livewire + Blade.
- Do not add React, Vue, Inertia, Ionic, Capacitor, or component CSS frameworks for app screens.
- Use Tailwind utility classes and project Blade components.
- Let product positioning decide UI purpose: admin controls policy and operations; mobile performs allowed work.
- Let product principles decide UI state: every disabled, offline, pending, conflict, blocked, or tenant-scoped state should be honest.
- Keep visible state honest: pending, offline, conflict, disabled, blocked, and deprecated states must be clear.
- Never rely on UI hiding as authorization.
- Show server-controlled capability state when a feature is disabled by plan, role, version, tenant, or app policy.
- Design each admin, support, billing, report, notification, offline, or feature-flag surface around the stakeholder value it is meant to deliver.
- Keep boundary language visible in state design: server-confirmed, cached, draft, pending, synced, conflict, failed, blocked, and offline states should not collapse into one generic status.

## Mobile UX Principles

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

The mobile client should be calm, direct, and explicit about sync state.

Mobile users should not need to understand tenant billing, feature rollout, support policy, or API versioning. The UI should translate those decisions into clear states such as enabled, disabled, blocked, deprecated, pending, synced, conflict, and offline.

Mobile API states should have UI homes: boot/context changes affect navigation, validation errors affect forms, version errors affect update prompts, sync/conflict errors affect status surfaces, and tenant/billing/permission denials affect clear next-action messaging.

Mobile UI should also distinguish local responsibility from authority: local draft, local cache, local permission prompt, and local feedback are useful only when the user can tell what still needs API confirmation.

Guest/pre-login, invited, and suspended states should have minimal, clear screens that never expose tenant data or normal mobile workflows.

- Prefer `<x-mobile.*>` Blade components over repeated markup.
- Keep mobile tap targets at `min-h-12` or larger.
- Use `gap-*` for sibling spacing instead of stacked margins.
- Use `rounded-lg` as the default radius for controls, cards, and panels.
- Use `dark:` classes in every reusable component that owns color.
- Forward attributes so Livewire directives like `wire:model`, `wire:click`, `wire:submit`, and `wire:navigate` continue to work.
- Show last sync, pending count, network status, and conflict state near affected workflows.
- Request NativePHP permissions just in time and explain the business purpose.

## Admin UX Principles

The admin panel should be dense, searchable, and audit-friendly.

Admin users include SaaS owners, platform operators, tenant admins, support, billing, release, and security/compliance roles. The UI should make scope and consequence obvious before a setting changes mobile behavior.

The admin UI should distinguish platform owner, super admin, tenant admin, tenant manager, support agent, and billing manager actions instead of presenting one generic admin surface.

The admin UI should also distinguish responsibility areas. Tenant management, users and permissions, API contracts, feature control, remote configuration, mobile version rules, notifications, billing, support, reports, audit, conflicts, and security should not collapse into one unscoped settings page.

The admin UI should also distinguish Admin Control Center areas: tenants, users, roles, permissions, mobile features, remote config, app versions, maintenance mode, force update, sync behavior, notifications, reports, billing, and support each need visible scope and consequence.

- Optimize for repeated operations, not landing-page presentation.
- Use tables, filters, tabs, segmented controls, and clear state badges.
- Keep destructive or broad controls behind confirmation.
- Show scope before action: global, tenant, role, user, device, feature, version, or sync policy.
- Show audit context for sensitive settings: actor, time, old value, new value, reason.
- Prefer compact forms with grouped sections for remote config, feature flags, app versions, billing, support, and notification policy.
- Do not hide operational errors behind generic success messages.

## Design Tokens

The base Tailwind v4 theme lives in `resources/css/app.scss`.

| Purpose | Utility |
| --- | --- |
| App background | `bg-app-bg dark:bg-zinc-950` |
| Surface | `bg-app-surface dark:bg-zinc-900` |
| Primary text | `text-app-ink dark:text-zinc-100` |
| Muted text | `text-app-muted dark:text-zinc-400` |
| Border | `border-app-line dark:border-zinc-800` |
| Accent | `bg-app-accent text-app-accent-ink dark:bg-emerald-400 dark:text-zinc-950` |
| Warning accent | `bg-app-warm` or `bg-amber-*` in dark mode |

## Spacing

Use compact mobile spacing that fits a single-column app shell.

| Context | Utility |
| --- | --- |
| Screen horizontal padding | `safe-x` |
| Screen vertical section padding | `py-6` |
| Card and panel padding | `p-5` |
| Dense list item padding | `p-4` |
| Form field groups | `grid gap-2` |
| Form stacks | `space-y-4` or `grid gap-4` |
| Screen stacks | `flex flex-col gap-5` |
| Button/icon gap | `gap-2` |

`safe-x`, `safe-pt`, and `safe-pb` are defined in `resources/css/app.scss` for mobile safe-area support.

## Rounded Corners

| Element | Utility |
| --- | --- |
| Buttons | `rounded-lg` |
| Inputs/selects/textareas | `rounded-lg` |
| Cards/panels | `rounded-lg` |
| Modals | `rounded-lg` |
| Bottom sheets | `rounded-t-lg` |
| Badges | `rounded-full` |
| Avatars | `rounded-full` |
| Skeleton bars | `rounded-full` |

Avoid larger radii unless the element is circular or a bottom sheet edge.

## Typography

| Role | Utility |
| --- | --- |
| Page title | `text-xl font-semibold tracking-normal` |
| Modal/sheet title | `text-lg font-semibold` |
| Card title | `text-base font-semibold` |
| Body text | `text-sm leading-6` |
| Form label | `text-sm font-medium` |
| Input text | `text-base` |
| Badge text | `text-xs font-semibold` |
| Bottom navigation | `text-[11px] font-medium` |

Do not scale font sizes with viewport width. Keep `tracking-normal` for display text.

## Mobile Components

All reusable mobile components live in `resources/views/components/mobile`.

| Component | Use |
| --- | --- |
| `<x-mobile.app-header>` | Default app shell header with page title, notification icon, and profile icon. |
| `<x-mobile.page-header>` | Mobile page headers with optional eyebrow, description, back link, and action slot. |
| `<x-mobile.button>` | Buttons with variants, sizes, disabled state, and Livewire loading styling. |
| `<x-mobile.input>` | Text inputs with label, hint, validation error, and forwarded Livewire attributes. |
| `<x-mobile.textarea>` | Multiline input with the same form contract as input. |
| `<x-mobile.select>` | Native select with label, hint, errors, placeholder, and options. |
| `<x-mobile.card>` | Standard content surface with optional title, description, action, and footer slots. |
| `<x-mobile.modal>` | Centered mobile dialog controlled by a passed `show` state. |
| `<x-mobile.bottom-sheet>` | Bottom anchored action panel controlled by a passed `show` state. |
| `<x-mobile.badge>` | Status labels with neutral, primary, accent, success, warning, and danger variants. |
| `<x-mobile.avatar>` | Image or initials avatar with optional status dot. |
| `<x-mobile.empty-state>` | Empty screens or empty sections. |
| `<x-mobile.error-state>` | Recoverable error screens or error panels. |
| `<x-mobile.loading-skeleton>` | Loading placeholders for cards and list rows. |
| `<x-mobile.loading-spinner>` | Inline status spinner for buttons and compact loading text. |
| `<x-mobile.loading-state>` | Livewire `wire:loading` status banner scoped to a target action or property. |
| `<x-mobile.submit-button>` | Form submit button with Livewire disabled state and loading spinner. |
| `<x-mobile.retry-button>` | Action button with retry loading copy and spinner. |
| `<x-mobile.page-skeleton>` | Full page loading placeholder built from mobile skeleton cards. |
| `<x-mobile.network-error-state>` | Connection error state with optional Livewire retry action. |
| `<x-mobile.bottom-navigation>` | Primary mobile navigation. |

## Product State Badges

The SaaS product needs consistent state language.

| State | Meaning |
| --- | --- |
| Enabled | Feature or permission is available now. |
| Disabled | Admin/API policy denies the feature. |
| Pending | Local action is queued or waiting for server confirmation. |
| Synced | Server accepted local action. |
| Conflict | Server could not apply local action as-is. |
| Blocked | App version, device, tenant, billing, or permission policy prevents use. |
| Deprecated | Current app version or feature path still works but should be upgraded. |
| Offline | Network is unavailable or fallback connectivity failed. |
| Beta | Feature is available through controlled rollout. |
| Update required | Feature cannot run safely on the current app version. |
| Offline limited | Feature is allowed but restricted while offline. |
| Emergency disabled | Feature is stopped by Admin/API for safety or incident response. |

Mobile and admin should use the same state names in copy, badges, filters, support tickets, and reports.

These state names also carry value-map meaning: mobile users get clarity, tenant admins get manageability, support gets diagnostic language, billing/operations gets entitlement explanation, and platform owners get consistent reporting.

They also carry boundary meaning: only server-confirmed and synced states can imply accepted API state; cached, draft, pending, conflict, failed, blocked, and offline states need honest local or policy context.

## Buttons

Button variants:

| Variant | Use |
| --- | --- |
| `primary` | Main action on a screen. |
| `secondary` | Secondary action with bordered surface. |
| `accent` | Positive or brand-highlighted action. |
| `ghost` | Low-emphasis inline action. |
| `danger` | Destructive action. |

Button sizes:

| Size | Utility |
| --- | --- |
| `sm` | `min-h-10 px-3 text-sm` |
| `md` | `min-h-12 px-4 text-sm` |
| `lg` | `min-h-14 px-5 text-base` |

Livewire loading is handled with `data-loading:pointer-events-none data-loading:opacity-70`.

## Loading, Error, And Offline States

Use Livewire-aware loading components for every page action that can wait on the server. Mobile pages should distinguish:

- Loading from local cache.
- Loading from API.
- Offline but usable.
- Offline and blocked.
- Pending sync.
- Conflict requiring user action.
- Policy blocked by admin/API.

## Forms

Inputs, textareas, and selects share the same form pattern:

- wrapper: `grid gap-2`
- label: `text-sm font-medium text-app-ink dark:text-zinc-100`
- control: `min-h-12`, `rounded-lg`, `border`, `text-base`, focus ring
- hint: `text-sm text-app-muted dark:text-zinc-400`
- error: `text-sm font-medium text-red-600 dark:text-red-400`

Admin forms should also show scope and audit reason for sensitive changes.

## Cards And Panels

Use cards for individual repeated items, forms, and framed tools. Do not nest cards inside cards.

Default card styling:

```text
rounded-lg border border-app-line bg-app-surface p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none
```

## Dark Mode

Tailwind's `dark:` variant is used directly in Blade components. Reusable components should include dark-mode classes anywhere they own color. The app does not yet include a theme toggle. Tailwind's default dark mode behavior follows the user's system preference unless a future toggle changes the strategy.

## Page Layout

Full-page Livewire mobile screens render inside the shared mobile app shell:

```blade
<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.card title="Status">
        Ready
    </x-mobile.card>
</section>
```

The shared layout renders `<x-mobile.app-header>` and `<x-mobile.bottom-navigation>` automatically for Livewire pages. Page components should own the scrollable content only unless a route needs a special-purpose layout.

## Documentation Boundary

This document defines UI/product principles only. It does not create components, screens, styles, translations, or application logic.
