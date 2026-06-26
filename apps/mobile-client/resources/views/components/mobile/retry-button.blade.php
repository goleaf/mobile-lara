@props([
    'target' => null,
    'variant' => 'secondary',
    'size' => 'md',
    'full' => false,
    'loadingLabel' => 'Retrying...',
])

@php
    $variantClasses = [
        'primary' => 'bg-app-ink text-white shadow-[0_14px_28px_-20px_rgba(15,23,42,0.8)] hover:bg-app-ink/90 active:bg-app-ink/80',
        'secondary' => 'border border-app-line bg-app-surface text-app-ink shadow-[0_12px_24px_-20px_rgba(15,23,42,0.45)] hover:bg-app-bg active:bg-app-line/60',
        'accent' => 'bg-app-accent text-app-accent-ink shadow-[0_14px_28px_-20px_rgba(20,83,45,0.65)] hover:bg-app-accent/90 active:bg-app-accent/80',
        'ghost' => 'text-app-ink hover:bg-app-surface active:bg-app-line/60',
        'danger' => 'bg-red-600 text-white shadow-[0_14px_28px_-20px_rgba(127,29,29,0.7)] hover:bg-red-700 active:bg-red-800',
    ][$variant] ?? 'border border-app-line bg-app-surface text-app-ink shadow-[0_12px_24px_-20px_rgba(15,23,42,0.45)] hover:bg-app-bg active:bg-app-line/60';

    $sizeClasses = [
        'sm' => 'min-h-11 px-3.5 text-sm',
        'md' => 'min-h-12 px-4 text-sm',
        'lg' => 'min-h-14 px-5 text-base',
    ][$size] ?? 'min-h-12 px-4 text-sm';
@endphp

<button
    wire:loading.attr="disabled"
    @if ($target) wire:target="{{ $target }}" @endif
    {{ $attributes
        ->class([
            'inline-flex touch-manipulation items-center justify-center gap-2 rounded-lg font-semibold transition duration-150 focus-visible:ring-2 focus-visible:ring-app-accent/25 data-loading:pointer-events-none data-loading:opacity-70 disabled:pointer-events-none disabled:opacity-50 active:translate-y-px',
            $variantClasses,
            $sizeClasses,
            'w-full' => $full,
        ])
        ->merge(['type' => 'button']) }}
>
    <span
        wire:loading.remove
        @if ($target) wire:target="{{ $target }}" @endif
    >
        {{ $slot }}
    </span>

    <span
        wire:loading.flex
        @if ($target) wire:target="{{ $target }}" @endif
        class="items-center gap-2"
    >
        <x-mobile.loading-spinner label="{{ $loadingLabel }}" />
        <span>{{ $loadingLabel }}</span>
    </span>
</button>
