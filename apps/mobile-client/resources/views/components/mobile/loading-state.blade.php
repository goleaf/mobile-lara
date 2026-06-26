@props([
    'target' => null,
    'message' => 'Loading...',
])

<div
    wire:loading.delay
    @if ($target) wire:target="{{ $target }}" @endif
    {{ $attributes->class(['rounded-lg border border-app-line bg-app-surface px-4 py-3 text-sm font-medium text-app-muted shadow-sm   ']) }}
    role="status"
    aria-live="polite"
>
    <div class="flex items-center gap-2">
        <x-mobile.loading-spinner label="{{ $message }}" />
        <span>{{ $message }}</span>
    </div>
</div>
