@props([
    'title' => 'Connection problem',
    'message' => 'We could not reach the server. Check your connection and try again.',
    'retryAction' => null,
    'retryLabel' => 'Try again',
])

<x-mobile.error-state :title="$title" :message="$message" {{ $attributes }}>
    <x-slot:icon>
        <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M3 5.5c5.8-3.3 12.2-3.3 18 0M6.5 9.5a11 11 0 0 1 11 0M10 13.5a4.4 4.4 0 0 1 4 0M4 20 20 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </x-slot:icon>

    @if ($retryAction || isset($action))
        <x-slot:action>
            @if ($retryAction)
                <x-mobile.retry-button wire:click="{{ $retryAction }}" :target="$retryAction">
                    {{ $retryLabel }}
                </x-mobile.retry-button>
            @else
                {{ $action }}
            @endif
        </x-slot:action>
    @endif
</x-mobile.error-state>
