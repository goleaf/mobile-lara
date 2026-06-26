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

<div {{ $attributes->class([
    'grid items-end gap-1 rounded-lg border border-app-line/80 bg-app-surface/90 p-1 text-center text-[10px] font-semibold shadow-[0_18px_40px_-28px_rgba(15,23,42,0.85)] ring-1 ring-white/70 backdrop-blur',
    $columnClass,
]) }}>
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
                'group relative isolate flex min-w-0 touch-manipulation flex-col items-center justify-center overflow-hidden rounded-lg px-1 py-2 transition duration-150 focus-visible:ring-2 focus-visible:ring-app-accent/25 active:translate-y-px',
                'min-h-[5rem] gap-1.5' => ! $primary,
                'min-h-[6rem] gap-2' => $primary,
                'bg-app-bg text-app-ink shadow-sm ring-1 ring-app-line/80' => $active && ! $primary,
                'text-app-muted hover:bg-app-bg hover:text-app-ink' => ! $active && ! $primary,
                'text-app-ink' => $primary,
            ])
        >
            @if ($active)
                <span class="absolute inset-x-3 top-1 h-1 rounded-full bg-app-accent" aria-hidden="true"></span>
            @endif

            <span @class([
                'grid place-items-center transition',
                'size-11 rounded-lg border' => ! $primary,
                'size-14 rounded-full border-2 shadow-[0_12px_28px_-18px_rgba(15,23,42,0.85)]' => $primary,
                'border-app-ink bg-app-ink text-white' => $active && ! $primary,
                'border-app-line/70 bg-app-surface text-current group-hover:border-app-line group-hover:bg-white' => ! $active && ! $primary,
                'border-app-ink bg-app-ink text-white' => $active && $primary,
                'border-app-accent/40 bg-app-accent/15 text-app-ink group-hover:bg-app-accent/20' => ! $active && $primary,
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
                'text-[9px]' => $item['label'] === 'Notifications',
            ])>{{ $item['label'] }}</span>
        </a>
    @endforeach
</div>
