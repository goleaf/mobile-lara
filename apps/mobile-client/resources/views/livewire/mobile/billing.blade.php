<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="refreshSubscription,openBillingPortal" message="Updating billing..." />

    <x-mobile.page-header
        title="Billing"
        description="Current workspace plan, subscription state, limits, and Admin/API-controlled billing actions."
        :back-href="route('mobile.settings')"
    >
        <x-slot:action>
            <x-mobile.badge :variant="$featureImpact['limited'] ? 'warning' : 'success'" dot>
                {{ $statusLabel }}
            </x-mobile.badge>
        </x-slot:action>
    </x-mobile.page-header>

    @if (! $billingPolicy['allowed'])
        <x-mobile.error-state
            title="Billing disabled"
            :message="$billingPolicy['message']"
        />
    @elseif ($subscription === [])
        <x-mobile.error-state
            title="Billing unavailable"
            :message="$loadError ?: 'No billing state is available for this workspace yet.'"
        >
            <x-slot:action>
                <x-mobile.button wire:click="refreshSubscription" variant="secondary">
                    Retry
                </x-mobile.button>
            </x-slot:action>
        </x-mobile.error-state>
    @else
        @if ($loadError)
            <x-mobile.error-state
                title="{{ $usingCachedSubscription ? 'Last known billing state' : 'Billing unavailable' }}"
                :message="$loadError"
            >
                <x-slot:action>
                    <x-mobile.button wire:click="refreshSubscription" variant="secondary">
                        Retry
                    </x-mobile.button>
                </x-slot:action>
            </x-mobile.error-state>
        @endif

        <x-mobile.card title="Plan" description="Billing authority stays in Admin/API; this device only displays the resolved outcome.">
            <div class="grid gap-4">
                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="break-words text-xl font-semibold text-app-ink ">{{ $plan['name'] }}</p>
                            <p class="mt-1 text-sm leading-5 text-app-muted ">
                                {{ $plan['key'] }} / {{ $plan['tier'] }}
                            </p>
                        </div>

                        <x-mobile.badge :variant="$featureImpact['limited'] ? 'warning' : 'success'">
                            {{ $statusLabel }}
                        </x-mobile.badge>
                    </div>

                    @if ($trial['active'])
                        <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900   ">
                            Trial ends {{ $trial['ends_at'] }}{{ $trial['days_remaining'] !== null ? ' - '.$trial['days_remaining'].' days remaining' : '' }}
                        </p>
                    @endif

                    @if ($featureImpact['limited'])
                        <p class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800   ">
                            Paid features are limited{{ $featureImpact['reason'] ? ': '.$featureImpact['reason'] : '.' }}
                        </p>
                    @endif
                </div>

                <div class="grid gap-3">
                    <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg p-4  ">
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-app-ink ">Billing portal</span>
                            <span class="mt-1 block text-sm leading-5 text-app-muted ">
                                {{ $portal['available'] ? 'Portal available' : str($portal['reason'] ?: 'portal_not_configured')->replace('_', ' ')->title() }}
                            </span>
                        </span>

                        <x-mobile.badge :variant="$portal['available'] ? 'accent' : 'neutral'">
                            {{ $portal['available'] ? 'Portal available' : 'Unavailable' }}
                        </x-mobile.badge>
                    </div>

                    @if ($portal['available'])
                        <x-mobile.button wire:click="openBillingPortal" variant="primary" full>
                            Open billing portal
                        </x-mobile.button>
                    @endif
                </div>
            </div>

            <x-slot:footer>
                <x-mobile.button wire:click="refreshSubscription" variant="secondary" full>
                    Refresh billing
                </x-mobile.button>
            </x-slot:footer>
        </x-mobile.card>

        <x-mobile.card title="Limits and usage" description="Mobile-safe usage snapshots from tenant billing settings.">
            <div class="grid gap-3">
                @forelse ($metricRows as $row)
                    <div wire:key="billing-metric-{{ $row['key'] }}" class="rounded-lg border border-app-line bg-app-bg p-4  ">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-semibold text-app-ink ">{{ $row['label'] }}</p>
                            <p class="text-sm font-medium text-app-muted ">{{ $row['usage'] }} / {{ $row['limit'] }}</p>
                        </div>
                    </div>
                @empty
                    <x-mobile.empty-state
                        title="No billing limits"
                        description="This plan does not expose mobile-safe limits or usage yet."
                    />
                @endforelse
            </div>
        </x-mobile.card>

        <x-mobile.card title="Available actions" description="Actions are advisory and still enforced by Admin/API permissions, feature flags, and subscription state.">
            <div class="flex flex-wrap gap-2">
                @forelse ($actions as $action)
                    <x-mobile.badge wire:key="billing-action-{{ $action['key'] }}" variant="accent">
                        {{ $action['label'] }}
                    </x-mobile.badge>
                @empty
                    <x-mobile.badge variant="neutral">
                        No actions
                    </x-mobile.badge>
                @endforelse
            </div>
        </x-mobile.card>
    @endif
</section>
