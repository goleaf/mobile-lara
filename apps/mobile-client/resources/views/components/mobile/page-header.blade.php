@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'backHref' => null,
    'backLabel' => 'Back',
])

<div {{ $attributes->class(['relative flex items-center justify-between gap-4 rounded-lg border border-app-line/80 bg-app-surface/80 px-4 py-3 shadow-[0_14px_28px_-28px_rgba(15,23,42,0.75)] ring-1 ring-white/70 backdrop-blur dark:border-zinc-800/90 dark:bg-zinc-900/80 dark:ring-white/5']) }}>
    <span aria-hidden="true" class="absolute inset-y-3 left-0 w-1 rounded-r bg-app-accent dark:bg-emerald-400"></span>

    <div class="flex min-w-0 items-center gap-3">
        @if ($backHref)
            <a
                href="{{ $backHref }}"
                wire:navigate
                aria-label="{{ $backLabel }}"
                class="grid size-10 shrink-0 place-items-center rounded-lg border border-app-line/80 bg-white/80 text-app-ink shadow-sm transition hover:bg-app-bg dark:border-zinc-700 dark:bg-zinc-950/80 dark:text-zinc-100 dark:hover:bg-zinc-800"
            >
                <span aria-hidden="true" class="text-lg leading-none">&lsaquo;</span>
            </a>
        @endif

        <div class="min-w-0 pl-1">
            @if ($eyebrow)
                <p class="text-sm font-medium text-app-muted dark:text-zinc-400">{{ $eyebrow }}</p>
            @endif

            <h1 class="truncate text-xl font-semibold tracking-normal text-app-ink dark:text-zinc-100">{{ $title }}</h1>

            @if ($description)
                <p class="mt-1 line-clamp-2 text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $description }}</p>
            @endif
        </div>
    </div>

    @isset($action)
        <div class="shrink-0">
            {{ $action }}
        </div>
    @endisset
</div>
