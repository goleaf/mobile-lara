@props([
    'variant' => 'primary',
    'size' => 'md',
    'full' => false,
])

@php
    $variantClasses = [
        'primary' => 'bg-app-ink text-white shadow-sm hover:bg-app-ink/90 active:bg-app-ink/80 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white dark:active:bg-zinc-200',
        'secondary' => 'border border-app-line bg-app-surface text-app-ink shadow-sm hover:bg-app-bg active:bg-app-line/60 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800 dark:active:bg-zinc-700',
        'accent' => 'bg-app-accent text-app-accent-ink shadow-sm hover:bg-app-accent/90 active:bg-app-accent/80 dark:bg-emerald-400 dark:text-zinc-950 dark:hover:bg-emerald-300 dark:active:bg-emerald-500',
        'ghost' => 'text-app-ink hover:bg-app-bg active:bg-app-line/60 dark:text-zinc-100 dark:hover:bg-zinc-900 dark:active:bg-zinc-800',
        'danger' => 'bg-red-600 text-white shadow-sm hover:bg-red-700 active:bg-red-800 dark:bg-red-500 dark:text-white dark:hover:bg-red-400 dark:active:bg-red-600',
    ][$variant] ?? 'bg-app-ink text-white shadow-sm hover:bg-app-ink/90 active:bg-app-ink/80 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white dark:active:bg-zinc-200';

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
