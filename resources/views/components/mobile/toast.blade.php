@props([
    'message' => null,
    'variant' => 'success',
])

@php
    $variantClasses = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200',
        'error' => 'border-red-200 bg-red-50 text-red-800 dark:border-red-400/30 dark:bg-red-400/10 dark:text-red-200',
    ][$variant] ?? 'border-app-line bg-app-surface text-app-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100';

    $dotClasses = [
        'success' => 'bg-emerald-500 dark:bg-emerald-300',
        'error' => 'bg-red-500 dark:bg-red-300',
    ][$variant] ?? 'bg-app-accent dark:bg-emerald-300';
@endphp

@if ($message)
    <div
        {{ $attributes->class([
            'pointer-events-auto rounded-lg border px-4 py-3 text-sm font-semibold shadow-lg backdrop-blur',
            $variantClasses,
        ]) }}
        role="{{ $variant === 'error' ? 'alert' : 'status' }}"
    >
        <div class="flex items-start gap-3">
            <span class="{{ $dotClasses }} mt-1.5 size-2 shrink-0 rounded-full"></span>
            <span>{{ $message }}</span>
        </div>
    </div>
@endif
