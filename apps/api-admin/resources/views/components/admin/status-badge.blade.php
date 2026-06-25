@props([
    'tone' => 'neutral',
])

@php
    $classes = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300',
        'danger' => 'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-300',
        'neutral' => 'border-zinc-200 bg-zinc-50 text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
    ][$tone] ?? 'border-zinc-200 bg-zinc-50 text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex min-h-7 items-center rounded-full border px-2.5 text-xs font-semibold {$classes}"]) }}>
    {{ $slot }}
</span>
