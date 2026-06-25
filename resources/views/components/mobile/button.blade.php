@props([
    'variant' => 'primary',
    'size' => 'md',
    'full' => false,
])

@php
    $variantClasses = [
        'primary' => 'bg-app-ink text-white shadow-sm hover:bg-app-ink/90 active:bg-app-ink/80',
        'secondary' => 'border border-app-line bg-app-surface text-app-ink shadow-sm hover:bg-app-bg active:bg-app-line/60',
        'accent' => 'bg-app-accent text-app-accent-ink shadow-sm hover:bg-app-accent/90 active:bg-app-accent/80',
        'ghost' => 'text-app-ink hover:bg-app-bg active:bg-app-line/60',
        'danger' => 'bg-red-600 text-white shadow-sm hover:bg-red-700 active:bg-red-800',
    ][$variant] ?? 'bg-app-ink text-white shadow-sm hover:bg-app-ink/90 active:bg-app-ink/80';

    $sizeClasses = [
        'sm' => 'min-h-10 px-3 text-sm',
        'md' => 'min-h-12 px-4 text-sm',
        'lg' => 'min-h-14 px-5 text-base',
    ][$size] ?? 'min-h-12 px-4 text-sm';
@endphp

<button
    {{ $attributes
        ->class([
            'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition data-loading:pointer-events-none data-loading:opacity-70 disabled:pointer-events-none disabled:opacity-50',
            $variantClasses,
            $sizeClasses,
            'w-full' => $full,
        ])
        ->merge(['type' => 'button']) }}
>
    {{ $slot }}
</button>
