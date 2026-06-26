@props([
    'title',
    'description' => null,
])

<section {{ $attributes->class(['grid place-items-center rounded-lg border border-dashed border-app-line/90 bg-app-surface/85 px-5 py-10 text-center shadow-[0_14px_32px_-28px_rgba(15,23,42,0.65)] ring-1 ring-white/60 backdrop-blur dark:border-zinc-700/90 dark:bg-zinc-900/80 dark:ring-white/5']) }}>
    <div class="max-w-xs">
        @isset($icon)
            <div class="mx-auto mb-4 grid size-12 place-items-center rounded-lg border border-app-line/70 bg-app-bg text-app-muted shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-400 dark:shadow-none">
                {{ $icon }}
            </div>
        @endisset

        <h2 class="text-base font-semibold text-app-ink dark:text-zinc-100">{{ $title }}</h2>

        @if ($description)
            <p class="mt-2 text-sm leading-6 text-app-muted dark:text-zinc-400">{{ $description }}</p>
        @endif

        @isset($action)
            <div class="mt-5">
                {{ $action }}
            </div>
        @endisset
    </div>
</section>
