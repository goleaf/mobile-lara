@props([
    'show' => false,
    'title' => null,
    'description' => null,
])

@if ($show)
    <div {{ $attributes->class(['fixed inset-0 z-50']) }} role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-app-ink/45 dark:bg-black/70"></div>

        <div class="safe-x safe-pb fixed inset-x-0 bottom-0 mx-auto w-full max-w-md">
            <section class="rounded-t-lg border border-b-0 border-app-line bg-app-surface p-5 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-app-line dark:bg-zinc-700"></div>

                @if ($title || $description || isset($action))
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            @if ($title)
                                <h2 class="text-lg font-semibold text-app-ink dark:text-zinc-100">{{ $title }}</h2>
                            @endif

                            @if ($description)
                                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $description }}</p>
                            @endif
                        </div>

                        @isset($action)
                            <div class="shrink-0">
                                {{ $action }}
                            </div>
                        @endisset
                    </div>
                @endif

                <div class="text-sm leading-6 text-app-ink dark:text-zinc-100">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <div class="mt-5 grid gap-2">
                        {{ $footer }}
                    </div>
                @endisset
            </section>
        </div>
    </div>
@endif
