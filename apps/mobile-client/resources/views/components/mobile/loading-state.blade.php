@props([
    'target' => null,
    'message' => 'Loading...',
])

<div
    wire:loading.delay
    @if ($target) wire:target="{{ $target }}" @endif
    {{ $attributes->class(['rounded-lg border border-app-line bg-app-surface px-4 py-3 text-sm font-semibold text-app-muted shadow-[0_12px_24px_-22px_rgba(15,23,42,0.45)]']) }}
    role="status"
    aria-live="polite"
>
    <div class="flex items-center gap-2">
        <x-mobile.loading-spinner label="{{ $message }}" />
        <span>{{ $message }}</span>
    </div>
</div>
