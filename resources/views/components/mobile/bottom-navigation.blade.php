@php
    $items = [
        ['route' => 'mobile.dashboard', 'label' => 'Home'],
        ['route' => 'mobile.search', 'label' => 'Search'],
        ['route' => 'mobile.notifications', 'label' => 'Alerts'],
        ['route' => 'mobile.profile', 'label' => 'Profile'],
        ['route' => 'mobile.settings', 'label' => 'Settings'],
    ];
@endphp

<div {{ $attributes->class(['grid grid-cols-5 gap-1 text-center text-[11px] font-medium']) }}>
    @foreach ($items as $item)
        <a
            href="{{ route($item['route']) }}"
            wire:navigate
            @class([
                'min-w-0 rounded-lg px-1.5 py-2 transition',
                'bg-app-bg text-app-ink shadow-sm' => request()->routeIs($item['route']),
                'text-app-muted hover:bg-app-bg hover:text-app-ink' => ! request()->routeIs($item['route']),
            ])
        >
            <span class="block truncate">{{ $item['label'] }}</span>
        </a>
    @endforeach
</div>
