@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'backHref' => null,
    'backLabel' => 'Back',
])

<div {{ $attributes->class(['flex items-center justify-between gap-4']) }}>
    <div class="flex min-w-0 items-center gap-3">
        @if ($backHref)
            <a
                href="{{ $backHref }}"
                wire:navigate
                aria-label="{{ $backLabel }}"
                class="grid size-10 shrink-0 place-items-center rounded-lg border border-app-line bg-app-surface text-app-ink shadow-sm transition hover:bg-app-bg"
            >
                <span aria-hidden="true" class="text-lg leading-none">&lsaquo;</span>
            </a>
        @endif

        <div class="min-w-0">
            @if ($eyebrow)
                <p class="text-sm font-medium text-app-muted">{{ $eyebrow }}</p>
            @endif

            <h1 class="truncate text-xl font-semibold tracking-normal text-app-ink">{{ $title }}</h1>

            @if ($description)
                <p class="mt-1 line-clamp-2 text-sm leading-5 text-app-muted">{{ $description }}</p>
            @endif
        </div>
    </div>

    @isset($action)
        <div class="shrink-0">
            {{ $action }}
        </div>
    @endisset
</div>
