@php
    $items = [
        ['route' => 'mobile.dashboard', 'label' => 'Home', 'icon' => 'home'],
        ['route' => 'mobile.search', 'label' => 'Search', 'icon' => 'search'],
        ['route' => 'mobile.notifications', 'label' => 'Alerts', 'icon' => 'bell'],
        ['route' => 'mobile.profile', 'label' => 'Profile', 'icon' => 'user'],
        ['route' => 'mobile.settings', 'label' => 'Settings', 'icon' => 'settings'],
    ];
@endphp

<div {{ $attributes->class(['grid grid-cols-5 gap-1 text-center text-[11px] font-medium']) }}>
    @foreach ($items as $item)
        @php
            $active = request()->routeIs($item['route']);
        @endphp

        <a
            href="{{ route($item['route']) }}"
            wire:navigate
            wire:key="mobile-tab-{{ $item['route'] }}"
            @if ($active) aria-current="page" @endif
            @class([
                'flex min-h-12 min-w-0 flex-col items-center justify-center gap-1 rounded-lg px-1.5 py-2 transition',
                'bg-app-bg text-app-ink shadow-sm dark:bg-zinc-800 dark:text-zinc-100 dark:shadow-none' => $active,
                'text-app-muted hover:bg-app-bg hover:text-app-ink dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' => ! $active,
            ])
        >
            @switch($item['icon'])
                @case('home')
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-4v-6H9v6H5a1 1 0 0 1-1-1v-9.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    @break

                @case('search')
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m20 20-4.2-4.2M18 11a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
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

                @case('settings')
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M19 13.5v-3l-2-.5a6.8 6.8 0 0 0-.7-1.6l1.1-1.8-2.1-2.1-1.8 1.1a6.8 6.8 0 0 0-1.6-.7L11.5 3h-3l-.5 2a6.8 6.8 0 0 0-1.6.7L4.6 4.6 2.5 6.7l1.1 1.8a6.8 6.8 0 0 0-.7 1.6l-2 .5v3l2 .5a6.8 6.8 0 0 0 .7 1.6l-1.1 1.8 2.1 2.1 1.8-1.1a6.8 6.8 0 0 0 1.6.7l.5 2h3l.5-2a6.8 6.8 0 0 0 1.6-.7l1.8 1.1 2.1-2.1-1.1-1.8a6.8 6.8 0 0 0 .7-1.6l2-.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    @break
            @endswitch

            <span class="block truncate">{{ $item['label'] }}</span>
        </a>
    @endforeach
</div>
