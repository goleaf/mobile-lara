@props([
    'title',
    'description' => null,
])

<section {{ $attributes->class(['grid place-items-center rounded-lg border border-dashed border-app-line bg-app-surface px-5 py-10 text-center']) }}>
    <div class="max-w-xs">
        @isset($icon)
            <div class="mx-auto mb-4 grid size-12 place-items-center rounded-full bg-app-bg text-app-muted">
                {{ $icon }}
            </div>
        @endisset

        <h2 class="text-base font-semibold text-app-ink">{{ $title }}</h2>

        @if ($description)
            <p class="mt-2 text-sm leading-6 text-app-muted">{{ $description }}</p>
        @endif

        @isset($action)
            <div class="mt-5">
                {{ $action }}
            </div>
        @endisset
    </div>
</section>
