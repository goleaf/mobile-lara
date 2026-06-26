<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light dark">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-dvh bg-zinc-50 text-zinc-950 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <div class="min-h-dvh">
            <header class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold tracking-normal text-zinc-950 dark:text-zinc-100">
                        Mobile Lara Admin
                    </a>

                    <nav aria-label="Admin navigation" class="flex items-center gap-2 text-sm">
                        <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            Control
                        </a>

                        <a href="{{ route('admin.tenants') }}" class="rounded-lg px-3 py-2 font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            Tenants
                        </a>

                        <a href="{{ route('admin.mobile.features') }}" class="rounded-lg px-3 py-2 font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            Features
                        </a>

                        <a href="{{ route('admin.mobile.diagnostics') }}" class="rounded-lg px-3 py-2 font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            Diagnostics
                        </a>

                        <a href="{{ route('admin.mobile.sync') }}" class="rounded-lg px-3 py-2 font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            Sync
                        </a>

                        <a href="{{ route('admin.records') }}" class="rounded-lg px-3 py-2 font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            Records
                        </a>

                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf

                            <button type="submit" class="rounded-lg px-3 py-2 font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                                Logout
                            </button>
                        </form>
                    </nav>
                </div>
            </header>

            <main>
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
