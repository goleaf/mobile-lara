<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light">

        <title>{{ config('app.name', 'Mobile Lara') }}</title>

        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body class="min-h-dvh bg-app-bg text-app-ink antialiased">
        <main class="mx-auto grid min-h-dvh max-w-md place-items-center px-6 py-10">
            <section class="grid gap-5 rounded-lg border border-app-line bg-app-surface p-6 shadow-sm">
                <div class="grid gap-2">
                    <p class="text-sm font-medium uppercase text-app-muted">Mobile Lara</p>
                    <h1 class="text-2xl font-semibold text-app-ink">Light mobile interface</h1>
                    <p class="text-sm leading-6 text-app-muted">
                        The mobile client uses one light design system for web preview and NativePHP builds.
                    </p>
                </div>

                <a
                    href="{{ route('mobile.welcome') }}"
                    class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-accent px-4 text-sm font-semibold text-app-accent-ink"
                >
                    Open mobile app
                </a>
            </section>
        </main>
    </body>
</html>
