@props([
    'title' => config('app.name'),
    'eyebrow' => config('app.name'),
])

@php
    $notificationActive = request()->routeIs('mobile.notifications');
    $profileActive = request()->routeIs('mobile.profile');
@endphp

<div {{ $attributes->class(['flex items-center justify-between gap-3']) }}>
    <div class="min-w-0">
        <p class="text-xs font-medium text-app-muted ">{{ $eyebrow }}</p>
        <h1 class="truncate text-xl font-semibold tracking-normal text-app-ink ">{{ $title }}</h1>
    </div>

    <div class="flex shrink-0 items-center gap-2">
        <a
            href="{{ route('mobile.notifications') }}"
            wire:navigate
            aria-label="Notifications"
            @class([
                'relative grid size-11 place-items-center rounded-lg border transition',
                'border-app-ink bg-app-ink text-white   ' => $notificationActive,
                'border-app-line bg-app-surface text-app-muted shadow-sm hover:bg-app-bg hover:text-app-ink     ' => ! $notificationActive,
            ])
        >
            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M15 17H9m9-6a6 6 0 0 0-12 0c0 3-1.5 4.5-2 5h16c-.5-.5-2-2-2-5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M10 20a2.2 2.2 0 0 0 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            </svg>
            <span class="absolute right-2 top-2 size-2 rounded-full bg-app-accent "></span>
        </a>

        <a
            href="{{ route('mobile.profile') }}"
            wire:navigate
            aria-label="Profile"
            @class([
                'grid size-11 place-items-center rounded-lg border transition',
                'border-app-ink bg-app-ink text-white   ' => $profileActive,
                'border-app-line bg-app-surface text-app-muted shadow-sm hover:bg-app-bg hover:text-app-ink     ' => ! $profileActive,
            ])
        >
            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M4.5 20a7.5 7.5 0 0 1 15 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
    </div>
</div>
