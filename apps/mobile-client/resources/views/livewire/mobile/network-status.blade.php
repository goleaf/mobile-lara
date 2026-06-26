<div wire:poll.15s="refreshStatus">
    <x-mobile.card title="Network status" description="NativePHP network telemetry with fallback connectivity state.">
        <x-slot:action>
            <x-mobile.badge :variant="$stateVariant" dot>
                {{ $stateLabel }}
            </x-mobile.badge>
        </x-slot:action>

        <div class="grid grid-cols-2 gap-3">
            @forelse ($statusRows as $row)
                <div
                    wire:key="network-status-{{ $row['key'] }}"
                    class="rounded-lg border border-app-line bg-app-bg p-3  "
                >
                    <p class="text-xs font-semibold uppercase text-app-muted ">{{ $row['label'] }}</p>
                    <p class="mt-1 break-words text-sm font-semibold text-app-ink ">{{ $row['value'] }}</p>
                </div>
            @empty
                <x-mobile.empty-state
                    title="No network status"
                    description="Network telemetry is not available yet."
                />
            @endforelse
        </div>

        <x-slot:footer>
            <p class="text-sm leading-5 text-app-muted ">
                Native status {{ $nativeStatusAvailable ? 'available' : 'unavailable' }}.
                Fallback check {{ $fallbackCheckUsed ? 'enabled' : 'not used' }}@if ($fallbackUrl): {{ $fallbackUrl }}@endif.
            </p>
        </x-slot:footer>
    </x-mobile.card>
</div>
