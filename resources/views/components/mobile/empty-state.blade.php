@props([
    'title',
    'description' => null,
])

<section {{ $attributes->class(['grid place-items-center rounded-lg border border-dashed border-app-line bg-app-surface px-5 py-10 text-center dark:border-zinc-700 dark:bg-zinc-900']) }}>
    <div class="max-w-xs">
        @isset($icon)
            <div class="mx-auto mb-4 grid size-12 place-items-center rounded-full bg-app-bg text-app-muted dark:bg-zinc-800 dark:text-zinc-400">
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
