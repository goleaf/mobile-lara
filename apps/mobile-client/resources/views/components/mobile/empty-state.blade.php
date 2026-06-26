@props([
    'title',
    'description' => null,
])

<section {{ $attributes->class(['grid place-items-center rounded-lg border border-dashed border-app-line/90 bg-app-surface/90 px-5 py-10 text-center shadow-[0_18px_38px_-30px_rgba(15,23,42,0.65)] ring-1 ring-white/70 backdrop-blur']) }}>
    <div class="max-w-xs">
        @isset($icon)
            <div class="mx-auto mb-4 grid size-14 place-items-center rounded-full border border-app-line/70 bg-app-bg text-app-muted shadow-[0_12px_24px_-20px_rgba(15,23,42,0.45)]">
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
