@php
    $items = [
        ['route' => 'mobile.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'primary' => false],
        ['route' => 'mobile.search', 'label' => 'Search', 'icon' => 'search', 'primary' => false],
        ['route' => 'mobile.create', 'label' => 'Create', 'icon' => 'plus', 'primary' => true],
        ['route' => 'mobile.notifications', 'label' => 'Notifications', 'icon' => 'bell', 'primary' => false],
        ['route' => 'mobile.profile', 'label' => 'Profile', 'icon' => 'user', 'primary' => false],
    ];
@endphp

<div {{ $attributes->class(['grid grid-cols-5 items-end gap-1 text-center text-[11px] font-semibold']) }}>
    @foreach ($items as $item)
        @php
            $active = request()->routeIs($item['route']);
            $primary = $item['primary'];
        @endphp

        <a
            href="{{ route($item['route']) }}"
            wire:navigate
            wire:key="mobile-tab-{{ $item['route'] }}"
            @if ($active) aria-current="page" @endif
            @class([
                'group flex min-w-0 flex-col items-center justify-end rounded-lg px-1 py-1.5 transition focus-visible:outline-2 focus-visible:outline-offset-2',
                'min-h-14 gap-1' => ! $primary,
                'min-h-16 gap-1.5' => $primary,
                'bg-app-bg text-app-ink shadow-sm dark:bg-zinc-800 dark:text-zinc-100 dark:shadow-none' => $active && ! $primary,
                'text-app-muted hover:bg-app-bg hover:text-app-ink dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! $active && ! $primary,
                'text-app-ink dark:text-zinc-100' => $primary,
            ])
        >
            <span @class([
                'grid place-items-center transition',
                'size-7 rounded-lg' => ! $primary,
                'size-11 rounded-full border shadow-sm' => $primary,
                'bg-app-ink text-white dark:bg-zinc-100 dark:text-zinc-950' => $active && $primary,
                'border-app-line bg-app-surface text-app-ink group-hover:bg-app-bg dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:group-hover:bg-zinc-800' => ! $active && $primary,
                'text-app-ink dark:text-zinc-100' => $active && ! $primary,
                'text-current' => ! $active && ! $primary,
            ])>
                @switch($item['icon'])
                    @case('dashboard')
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 5a1 1 0 0 1 1-1h5v7H4V5Zm10-1h5a1 1 0 0 1 1 1v3h-6V4ZM4 15h6v5H5a1 1 0 0 1-1-1v-4Zm10-3h6v7a1 1 0 0 1-1 1h-5v-8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        @break

                    @case('search')
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m20 20-4.2-4.2M18 11a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        @break

                    @case('plus')
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        @break

                    @case('bell')
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M15 17H9m9-6a6 6 0 0 0-12 0c0 3-1.5 4.5-2 5h16c-.5-.5-2-2-2-5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M10 20a2.2 2.2 0 0 0 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                        @break

                    @case('user')
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4.5 20a7.5 7.5 0 0 1 15 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        @break
                @endswitch
            </span>

            <span @class([
                'block max-w-full truncate leading-4',
                'text-[10px]' => $item['label'] === 'Notifications',
            ])>{{ $item['label'] }}</span>
        </a>
    @endforeach
</div>
