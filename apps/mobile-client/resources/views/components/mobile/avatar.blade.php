@props([
    'src' => null,
    'alt' => '',
    'initials' => null,
    'size' => 'md',
    'status' => null,
])

@php
    $sizeClasses = [
        'sm' => 'size-8 text-xs',
        'md' => 'size-12 text-sm',
        'lg' => 'size-16 text-lg',
    ][$size] ?? 'size-12 text-sm';

    $statusClasses = [
        'online' => 'bg-emerald-500',
        'busy' => 'bg-red-500',
        'away' => 'bg-app-warm',
        'offline' => 'bg-app-muted',
    ][$status] ?? null;
@endphp

<span {{ $attributes->class(['relative inline-flex shrink-0']) }}>
    @if ($src)
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="{{ $sizeClasses }} rounded-full border border-app-line object-cover shadow-sm dark:border-zinc-700"
        >
    @else
        <span class="{{ $sizeClasses }} grid place-items-center rounded-full border border-app-line bg-app-accent font-semibold text-app-accent-ink shadow-sm dark:border-zinc-700 dark:bg-emerald-400 dark:text-zinc-950">
            {{ $initials }}
        </span>
    @endif

    @if ($statusClasses)
        <span class="{{ $statusClasses }} absolute bottom-0 right-0 size-3 rounded-full border-2 border-app-surface dark:border-zinc-900"></span>
    @endif
</span>
