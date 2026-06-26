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
            class="{{ $sizeClasses }} rounded-full border border-app-line object-cover shadow-[0_12px_24px_-20px_rgba(15,23,42,0.45)]"
        >
    @else
        <span class="{{ $sizeClasses }} grid place-items-center rounded-full border border-app-accent/40 bg-app-accent font-semibold text-app-accent-ink shadow-[0_12px_24px_-20px_rgba(20,83,45,0.55)]">
            {{ $initials }}
        </span>
    @endif

    @if ($statusClasses)
        <span class="{{ $statusClasses }} absolute bottom-0 right-0 size-3 rounded-full border-2 border-app-surface"></span>
    @endif
</span>
