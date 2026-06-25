<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="color-scheme" content="light dark">
        <link rel="icon" href="{{ asset('icon.png') }}" type="image/png">

        @php
            $pageTitle = $title ?? trim($__env->yieldContent('title', config('app.name')));
        @endphp

        <title>{{ $pageTitle }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-dvh overflow-hidden bg-app-bg text-app-ink antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <div class="mx-auto grid min-h-dvh w-full max-w-md grid-rows-[auto_auto_minmax(0,1fr)_auto] overflow-hidden bg-app-bg shadow-[0_0_0_1px_var(--color-app-line)] dark:bg-zinc-950 dark:shadow-[0_0_0_1px_#27272a]">
            <header class="safe-x safe-pt z-20 border-b border-app-line bg-app-surface/95 pb-3 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/95">
                <x-mobile.app-header :title="$pageTitle" />
            </header>

            <livewire:mobile.offline-banner />

            <main id="mobile-app-content" class="min-h-0 overflow-y-auto overscroll-contain">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('body')
                @endisset
            </main>

            <nav
                aria-label="Primary tabs"
                class="safe-x safe-pb z-20 border-t border-app-line bg-app-surface/95 pt-3 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/95"
            >
                <x-mobile.bottom-navigation />
            </nav>
        </div>

        <aside
            id="mobile-toast-region"
            aria-live="polite"
            aria-atomic="true"
            class="pointer-events-none fixed inset-x-0 top-0 z-50 mx-auto flex w-full max-w-md flex-col gap-2 safe-x safe-pt"
        >
            <livewire:mobile.toast-center />

            @isset($toast)
                {{ $toast }}
            @else
                @yield('toast')
            @endisset
        </aside>

        @livewireScripts
    </body>
</html>
