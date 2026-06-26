@props([
    'title' => null,
    'description' => null,
    'padding' => true,
])

<section
    {{ $attributes->class([
        'overflow-hidden rounded-lg border border-app-line/80 bg-app-surface/95 shadow-[0_14px_32px_-26px_rgba(15,23,42,0.7)] ring-1 ring-white/70 backdrop-blur dark:border-zinc-800/90 dark:bg-zinc-900/90 dark:ring-white/5 dark:shadow-none',
        'p-5 sm:p-6' => $padding,
    ]) }}
>
    @if ($title || $description || isset($action))
        <div class="mb-5 flex items-start justify-between gap-4 border-b border-app-line/70 pb-4 dark:border-zinc-800/80">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="text-base font-semibold tracking-normal text-app-ink dark:text-zinc-100">{{ $title }}</h2>
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

    {{ $slot }}

    @isset($footer)
        <div class="mt-5 border-t border-app-line/70 pt-4 dark:border-zinc-800/80">
            {{ $footer }}
        </div>
    @endisset
</section>
