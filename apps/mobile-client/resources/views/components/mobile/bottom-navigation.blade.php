@php
    $items = $items ?? [
        ['route' => 'mobile.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'primary' => false],
        ['route' => 'mobile.search', 'label' => 'Search', 'icon' => 'search', 'primary' => false],
        ['route' => 'mobile.create', 'label' => 'Create', 'icon' => 'plus', 'primary' => true],
        ['route' => 'mobile.notifications', 'label' => 'Notifications', 'icon' => 'bell', 'primary' => false],
        ['route' => 'mobile.profile', 'label' => 'Profile', 'icon' => 'user', 'primary' => false],
    ];
    $columnClass = match (count($items)) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-2',
        3 => 'grid-cols-3',
        4 => 'grid-cols-4',
        default => 'grid-cols-5',
    };
@endphp

<div {{ $attributes->class(['grid items-end gap-1.5 text-center text-[11px] font-semibold', $columnClass]) }}>
    @foreach ($items as $item)
        @php
            $active = request()->routeIs($item['route']);
            $primary = $item['primary'];
            $iconClass = $primary ? 'size-7' : 'size-6';
        @endphp

        <a
            href="{{ route($item['route']) }}"
            wire:navigate
            wire:key="mobile-tab-{{ $item['route'] }}"
            aria-label="{{ $item['label'] }}"
            @if ($active) aria-current="page" @endif
            @class([
                'group flex min-w-0 flex-col items-center justify-end rounded-lg px-1.5 py-2 transition focus-visible:outline-2 focus-visible:outline-offset-2',
                'min-h-[4.75rem] gap-1.5' => ! $primary,
                'min-h-[5.5rem] gap-2' => $primary,
                'bg-app-bg text-app-ink shadow-sm ring-1 ring-app-line/80 dark:bg-zinc-800 dark:text-zinc-100 dark:ring-zinc-700 dark:shadow-none' => $active && ! $primary,
                'text-app-muted hover:bg-app-bg hover:text-app-ink dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! $active && ! $primary,
                'text-app-ink dark:text-zinc-100' => $primary,
            ])
        >
            <span @class([
                'grid place-items-center transition',
                'size-11 rounded-lg border' => ! $primary,
                'size-14 rounded-full border-2 shadow-[0_12px_28px_-18px_rgba(15,23,42,0.85)]' => $primary,
                'border-app-ink bg-app-ink text-white dark:border-zinc-100 dark:bg-zinc-100 dark:text-zinc-950' => $active && ! $primary,
                'border-transparent bg-app-bg text-current group-hover:border-app-line dark:bg-zinc-950 dark:group-hover:border-zinc-700' => ! $active && ! $primary,
                'bg-app-ink text-white dark:bg-zinc-100 dark:text-zinc-950' => $active && $primary,
                'border-app-line bg-app-surface text-app-ink group-hover:bg-app-bg dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:group-hover:bg-zinc-800' => ! $active && $primary,
            ])>
                @switch($item['icon'])
                    @case('dashboard')
                        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 5a1 1 0 0 1 1-1h5v7H4V5Zm10-1h5a1 1 0 0 1 1 1v3h-6V4ZM4 15h6v5H5a1 1 0 0 1-1-1v-4Zm10-3h6v7a1 1 0 0 1-1 1h-5v-8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        @break

                    @case('search')
                        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m20 20-4.2-4.2M18 11a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        @break

                    @case('plus')
                        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        @break

                    @case('bell')
                        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M15 17H9m9-6a6 6 0 0 0-12 0c0 3-1.5 4.5-2 5h16c-.5-.5-2-2-2-5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M10 20a2.2 2.2 0 0 0 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                        @break

                    @case('user')
                        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4.5 20a7.5 7.5 0 0 1 15 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        @break
                @endswitch
            </span>

            <span @class([
                'mobile-bottom-nav-label',
                'text-[10px]' => $item['label'] === 'Notifications',
            ])>{{ $item['label'] }}</span>
        </a>
    @endforeach
</div>
