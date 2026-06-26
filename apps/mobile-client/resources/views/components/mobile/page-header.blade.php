@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'backHref' => null,
    'backLabel' => 'Back',
])

<span hidden>
    @if ($eyebrow)
        {{ $eyebrow }}
    @endif

    {{ $title }}

    @if ($description)
        {{ $description }}
    @endif
</span>

@if ($backHref || isset($action))
    <div {{ $attributes->class(['flex items-center justify-between gap-3']) }}>
        @if ($backHref)
            <a
                href="{{ $backHref }}"
                wire:navigate
                aria-label="{{ $backLabel }}"
                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-app-line/80 bg-app-surface/90 px-3 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-white dark:border-zinc-700 dark:bg-zinc-900/90 dark:text-zinc-100 dark:hover:bg-zinc-800"
            >
                <span aria-hidden="true" class="text-lg leading-none">&lsaquo;</span>
                <span>{{ $backLabel }}</span>
            </a>
        @endif

        @isset($action)
            <div class="ml-auto shrink-0">
                {{ $action }}
            </div>
        @endisset
    </div>
@endif
