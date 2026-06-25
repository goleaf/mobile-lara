@props([
    'variant' => 'neutral',
    'size' => 'md',
    'dot' => false,
])

@php
    $variantClasses = [
        'neutral' => 'border-app-line bg-app-bg text-app-muted dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
        'primary' => 'border-app-ink/10 bg-app-ink text-white dark:border-zinc-100/10 dark:bg-zinc-100 dark:text-zinc-950',
        'accent' => 'border-app-accent/20 bg-app-accent/15 text-app-ink dark:border-emerald-400/20 dark:bg-emerald-400/15 dark:text-emerald-200',
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/15 dark:text-emerald-200',
        'warning' => 'border-app-warm/30 bg-app-warm/15 text-app-ink dark:border-amber-300/20 dark:bg-amber-300/15 dark:text-amber-200',
        'danger' => 'border-red-200 bg-red-50 text-red-700 dark:border-red-400/20 dark:bg-red-400/15 dark:text-red-200',
    ][$variant] ?? 'border-app-line bg-app-bg text-app-muted dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300';

    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-[11px]',
        'md' => 'px-2.5 py-1 text-xs',
    ][$size] ?? 'px-2.5 py-1 text-xs';
@endphp

<span
    {{ $attributes->class([
        'inline-flex max-w-full items-center gap-1.5 rounded-full border font-semibold',
        $variantClasses,
        $sizeClasses,
    ]) }}
>
    @if ($dot)
        <span class="size-1.5 shrink-0 rounded-full bg-current"></span>
    @endif

    <span class="truncate">{{ $slot }}</span>
</span>
