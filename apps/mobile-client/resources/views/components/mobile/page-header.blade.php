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
                class="inline-flex min-h-11 touch-manipulation items-center justify-center gap-2 rounded-lg border border-app-line/80 bg-app-surface/90 px-3.5 text-sm font-semibold text-app-ink shadow-[0_12px_24px_-20px_rgba(15,23,42,0.45)] transition duration-150 hover:bg-white focus-visible:ring-2 focus-visible:ring-app-accent/25 active:translate-y-px"
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
