@props([
    'title' => null,
    'description' => null,
    'padding' => true,
])

<section
    {{ $attributes->class([
        'overflow-hidden rounded-lg border border-app-line/80 bg-app-surface/95 shadow-[0_18px_38px_-30px_rgba(15,23,42,0.72)] ring-1 ring-white/75 backdrop-blur',
        'p-5 sm:p-6' => $padding,
    ]) }}
>
    @if ($title || $description || isset($action))
        <div class="mb-5 flex items-start justify-between gap-4 border-b border-app-line/70 pb-4">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="text-base font-semibold tracking-normal text-app-ink">{{ $title }}</h2>
                @endif

                @if ($description)
                    <p class="mt-1 text-sm leading-5 text-app-muted">{{ $description }}</p>
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
        <div class="mt-5 border-t border-app-line/70 pt-4">
            {{ $footer }}
        </div>
    @endisset
</section>
