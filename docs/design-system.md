# Mobile Design System

Updated: 2026-06-25

This app uses Blade components and Tailwind utility classes as the mobile design system. Keep UI in Livewire + Blade. Do not add React, Vue, Inertia, Ionic, Capacitor, or component CSS frameworks for app screens.

## Principles

- Prefer `<x-mobile.*>` Blade components over repeated markup.
- Style with Tailwind utility classes in Blade components.
- Keep mobile tap targets at `min-h-12` or larger.
- Use `gap-*` for sibling spacing instead of stacked margins.
- Use `rounded-lg` as the default radius for controls, cards, and panels.
- Use `dark:` classes in every reusable component that owns color.
- Forward attributes so Livewire directives like `wire:model`, `wire:click`, `wire:submit`, and `wire:navigate` continue to work.

## Design Tokens

The base Tailwind v4 theme lives in `resources/css/app.css`.

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

`safe-x`, `safe-pt`, and `safe-pb` are defined in `resources/css/app.css` for mobile safe-area support.

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

## Components

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

Example:

```blade
<x-mobile.button type="submit" variant="accent" size="lg" full wire:click="save">
    Save changes
</x-mobile.button>
```

Livewire loading is handled with `data-loading:pointer-events-none data-loading:opacity-70`.

## Loading And Error States

Use Livewire-aware loading components for every page action that can wait on the server:

```blade
<x-mobile.loading-state target="saveSettings" message="Saving settings..." />
<x-mobile.page-skeleton wire:loading.delay wire:target="saveSettings" />
<x-mobile.submit-button target="saveSettings" loading-label="Saving settings...">
    Save settings
</x-mobile.submit-button>
```

Use network and empty states as explicit branches in page components:

```blade
@if ($hasNetworkError)
    <x-mobile.network-error-state retry-action="retrySettings" />
@elseif (count($settings) === 0)
    <x-mobile.empty-state title="No settings available" description="Try reloading settings.">
        <x-slot:action>
            <x-mobile.retry-button wire:click="retrySettings" target="retrySettings">
                Reload settings
            </x-mobile.retry-button>
        </x-slot:action>
    </x-mobile.empty-state>
@endif
```

## Forms

Inputs, textareas, and selects share the same form pattern:

- wrapper: `grid gap-2`
- label: `text-sm font-medium text-app-ink dark:text-zinc-100`
- control: `min-h-12`, `rounded-lg`, `border`, `text-base`, focus ring
- hint: `text-sm text-app-muted dark:text-zinc-400`
- error: `text-sm font-medium text-red-600 dark:text-red-400`

Example:

```blade
<x-mobile.input
    name="email"
    label="Email"
    type="email"
    autocomplete="email"
    wire:model.live="email"
/>
```

## Cards And Panels

Use cards for individual repeated items, forms, and framed tools. Do not nest cards inside cards.

```blade
<x-mobile.card title="Profile" description="Local account details">
    <p class="text-sm leading-6 text-app-muted dark:text-zinc-400">
        Account settings live here.
    </p>
</x-mobile.card>
```

Default card styling:

```text
rounded-lg border border-app-line bg-app-surface p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none
```

## Dark Mode

Tailwind's `dark:` variant is used directly in Blade components. The layout declares:

```text
bg-app-bg text-app-ink dark:bg-zinc-950 dark:text-zinc-100
```

Reusable components should include dark-mode classes anywhere they own color. Prefer:

- `dark:bg-zinc-950` for the app canvas.
- `dark:bg-zinc-900` for surfaces.
- `dark:border-zinc-800` or `dark:border-zinc-700` for borders.
- `dark:text-zinc-100` for primary text.
- `dark:text-zinc-400` for muted text.
- `dark:bg-emerald-400 dark:text-zinc-950` for accent actions.

The app does not yet include a theme toggle. Tailwind's default dark mode behavior follows the user's system preference unless a future toggle changes the strategy.

## Page Layout

Full-page Livewire screens render inside the shared mobile app shell:

```blade
<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.card title="Status">
        Ready
    </x-mobile.card>
</section>
```

The shared layout renders `<x-mobile.app-header>` and `<x-mobile.bottom-navigation>` automatically for Livewire pages. Page components should own the scrollable content only unless a route needs a special-purpose layout.

Keep visible UI strings inside Blade/Livewire for now. Move them to translation files only when localization is introduced for this app.
