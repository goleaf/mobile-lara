@props([
    'label' => 'Loading',
])

<span {{ $attributes->class(['inline-flex items-center justify-center']) }} role="status" aria-label="{{ $label }}">
    <svg class="size-4 motion-safe:animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3"></circle>
        <path class="opacity-90" d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
    </svg>
    <span class="sr-only">{{ $label }}</span>
</span>
