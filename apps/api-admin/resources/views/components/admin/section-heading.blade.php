@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'grid gap-1']) }}>
    <h1 class="text-xl font-semibold tracking-normal text-zinc-950 dark:text-zinc-100">
        {{ $title }}
    </h1>

    @if ($description)
        <p class="max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
            {{ $description }}
        </p>
    @endif
</div>
