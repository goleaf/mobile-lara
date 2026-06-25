@props([
    'message' => null,
    'title' => null,
    'variant' => 'success',
])

@php
    $variantClasses = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-950 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-100',
        'error' => 'border-red-200 bg-red-50 text-red-950 dark:border-red-400/30 dark:bg-red-400/10 dark:text-red-100',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-950 dark:border-amber-300/30 dark:bg-amber-300/10 dark:text-amber-100',
        'info' => 'border-sky-200 bg-sky-50 text-sky-950 dark:border-sky-400/30 dark:bg-sky-400/10 dark:text-sky-100',
    ][$variant] ?? 'border-app-line bg-app-surface text-app-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100';

    $dotClasses = [
        'success' => 'bg-emerald-500 dark:bg-emerald-300',
        'error' => 'bg-red-500 dark:bg-red-300',
        'warning' => 'bg-amber-500 dark:bg-amber-300',
        'info' => 'bg-sky-500 dark:bg-sky-300',
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
            <span class="grid gap-1">
                @if ($title)
                    <span class="font-bold">{{ $title }}</span>
                @endif

                <span>{{ $message }}</span>
            </span>
        </div>
    </div>
@endif
