@props([
    'title' => null,
    'description' => null,
    'padding' => true,
])

<section
    {{ $attributes->class([
        'rounded-lg border border-app-line bg-app-surface shadow-sm',
        'p-5' => $padding,
    ]) }}
>
    @if ($title || $description || isset($action))
        <div class="mb-4 flex items-start justify-between gap-4">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="text-base font-semibold text-app-ink">{{ $title }}</h2>
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
        <div class="mt-5 border-t border-app-line pt-4">
            {{ $footer }}
        </div>
    @endisset
</section>
