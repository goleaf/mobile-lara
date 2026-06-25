# Mobile And Admin Design System

Updated: 2026-06-25

This document defines UI principles for the two-system product:

1. **Admin/API system** - Livewire admin panel for SaaS operations.
2. **Mobile client system** - Livewire mobile app inside NativePHP.

The two interfaces should feel related but serve different jobs. Admin is an operational control plane. Mobile is a resilient task-focused client.

The design system supports the [Product Vision](product-vision.md): admin users need control, visibility, and audit context; mobile users need clear permitted workflows, native-feeling interaction, and honest offline/sync states.

It also supports the [Product Positioning](product-positioning.md): the admin UI should feel like a SaaS control center, and the mobile UI should feel like a workforce/client platform. The interfaces are related, but they should not collapse into a web-only dashboard or a mobile-only app.

UI decisions must follow [Core Product Principles](product-principles.md): admin controls policy, mobile never presents local state as server authority, disabled features are clear, tenant scope is visible where relevant, secure defaults are not hidden behind UI-only affordances, and mobile UX stays simple.

UI decisions must also follow [Target User Roles](user-roles.md): each role should see the controls, diagnostics, billing context, support context, tenant scope, or pre-login state that matches its job.

UI decisions must also express the [SaaS Value Map](saas-value-map.md): platform owners need high-level control and risk visibility, tenant admins need scoped management, mobile workers need simple next actions, support needs safe diagnostics, and billing/operations needs entitlement clarity without tenant workflow overreach.

UI decisions must also express [Two-System Boundary Logic](two-system-boundary.md): admin surfaces show authority and scope, while mobile surfaces show API-derived capability state, local freshness, pending work, conflicts, and offline limits without pretending to own server truth.

UI decisions must also express [Admin/API Responsibilities](admin-api-responsibilities.md): admin UI exposes scoped control-plane responsibilities, while mobile UI presents only the resulting capability, version, notification, billing, support, report, audit, conflict, and security states.

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

The mobile client should be calm, direct, and explicit about sync state.

Mobile users should not need to understand tenant billing, feature rollout, support policy, or API versioning. The UI should translate those decisions into clear states such as enabled, disabled, blocked, deprecated, pending, synced, conflict, and offline.

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
