@props([
    'message' => null,
    'title' => null,
    'variant' => 'success',
])

@php
    $variantClasses = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-950   ',
        'error' => 'border-red-200 bg-red-50 text-red-950   ',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-950   ',
        'info' => 'border-sky-200 bg-sky-50 text-sky-950   ',
    ][$variant] ?? 'border-app-line bg-app-surface text-app-ink   ';

    $dotClasses = [
        'success' => 'bg-emerald-500 ',
        'error' => 'bg-red-500 ',
        'warning' => 'bg-amber-500 ',
        'info' => 'bg-sky-500 ',
    ][$variant] ?? 'bg-app-accent ';
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
