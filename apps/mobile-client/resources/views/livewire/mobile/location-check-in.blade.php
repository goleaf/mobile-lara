<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Location check-in"
        description="Native permission status and current position check for mobile devices."
        :back-href="route('mobile.settings.developer')"
    />

    <x-mobile.card
        title="Location bridge"
        description="NativePHP geolocation calls return through Livewire native events."
    >
        <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg p-4  ">
            <div class="min-w-0">
                <p class="text-base font-semibold text-app-ink ">
                    {{ $nativeLocationAvailable ? 'Native location available' : 'Browser fallback active' }}
                </p>
                <p class="mt-1 text-sm leading-5 text-app-muted ">
                    {{ $nativeLocationAvailable ? 'Permission and location requests can run on this device.' : 'Open this route inside NativePHP or Jump Bridge to request native location access.' }}
                </p>
            </div>

            <x-mobile.badge :variant="$nativeLocationAvailable ? 'success' : 'warning'" dot>
                {{ $nativeLocationAvailable ? 'Ready' : 'Fallback' }}
            </x-mobile.badge>
        </div>

        @if ($pendingOperationId)
            <div class="mt-3 rounded-lg border border-sky-200 bg-sky-50 p-4  ">
                <p class="text-sm font-semibold text-sky-950 ">Pending native operation</p>
                <p class="mt-1 break-words text-sm text-sky-900 ">{{ $pendingOperation }} · {{ $pendingOperationId }}</p>
            </div>
        @endif

        <x-slot:footer>
            <div aria-live="polite" class="min-h-6">
                @if ($operationError)
                    <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900   ">
                        {{ $operationError }}
                    </p>
                @elseif ($operationStatus)
                    <p class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-sm font-medium text-sky-900   ">
                        {{ $operationStatus }}
                    </p>
                @endif
            </div>
        </x-slot:footer>
    </x-mobile.card>

    <x-mobile.card title="Permission status" description="Read or request location permission from the native runtime.">
        @if (! $locationPolicy['location']['allowed'])
            <x-mobile.error-state
                title="Location access disabled"
                :message="$locationPolicy['location']['message']"
            />
        @else
            <div class="grid gap-3">
            <div class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3  ">
                <span class="min-w-0">
                    <span class="block text-base font-semibold text-app-ink ">Overall permission</span>
                    <span class="mt-1 block text-sm leading-5 text-app-muted ">
                        {{ $permissionMessage ?? 'Permission status has not been checked yet.' }}
                    </span>
                </span>

                <x-mobile.badge :variant="$permissionBadgeVariant">
                    {{ $permissionStatusLabel }}
                </x-mobile.badge>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <p class="text-xs font-semibold uppercase text-app-muted ">Coarse</p>
                    <p class="mt-2 break-words text-sm font-semibold text-app-ink ">
                        {{ $coarsePermissionStatusLabel }}
                    </p>
                </div>

                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <p class="text-xs font-semibold uppercase text-app-muted ">Fine</p>
                    <p class="mt-2 break-words text-sm font-semibold text-app-ink ">
                        {{ $finePermissionStatusLabel }}
                    </p>
                </div>
            </div>

            @if ($permissionError)
                <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700   ">
                    {{ $permissionError }}
                </p>
            @endif

            <div class="grid gap-3 sm:grid-cols-2">
                <x-mobile.button
                    wire:click="checkPermissionStatus"
                    wire:loading.attr="disabled"
                    wire:target="checkPermissionStatus"
                    variant="secondary"
                    full
                >
                    <span wire:loading.remove wire:target="checkPermissionStatus">Check status</span>
                    <span wire:loading wire:target="checkPermissionStatus">Checking</span>
                </x-mobile.button>

                <x-mobile.button
                    wire:click="requestLocationPermission"
                    wire:loading.attr="disabled"
                    wire:target="requestLocationPermission"
                    variant="primary"
                    full
                >
                    <span wire:loading.remove wire:target="requestLocationPermission">Request permission</span>
                    <span wire:loading wire:target="requestLocationPermission">Requesting</span>
                </x-mobile.button>
            </div>
            </div>
        @endif
    </x-mobile.card>

    <x-mobile.card title="Current location" description="Capture one check-in result with accuracy and timestamp metadata.">
        @if (! $locationPolicy['location']['allowed'])
            <x-mobile.error-state
                title="Location check-in disabled"
                :message="$locationPolicy['location']['message']"
            />
        @else
            <div class="grid gap-4">
                <label class="flex min-h-14 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3  ">
                    <span class="min-w-0">
                        <span class="block text-base font-semibold text-app-ink ">High accuracy</span>
                        <span class="mt-1 block text-sm leading-5 text-app-muted ">Use GPS precision when the device allows it.</span>
                    </span>

                    <input
                        wire:model.live="fineAccuracy"
                        type="checkbox"
                        class="size-5 rounded border-app-line text-app-ink focus:ring-app-ink   "
                    >
                </label>

                <x-mobile.button
                    wire:click="checkIn"
                    wire:loading.attr="disabled"
                    wire:target="checkIn"
                    variant="accent"
                    size="lg"
                    full
                >
                    <span wire:loading.remove wire:target="checkIn">Check in now</span>
                    <span wire:loading wire:target="checkIn">Locating</span>
                </x-mobile.button>

                <div aria-live="polite" class="min-h-6">
                    @if ($locationError)
                        <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700   ">
                            {{ $locationError }}
                        </p>
                    @elseif ($locationStatus)
                        <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800   ">
                            {{ $locationStatus }}
                        </p>
                    @endif
                </div>
            </div>
        @endif
    </x-mobile.card>

    <x-mobile.card title="Last check-in" description="The most recent location payload returned by NativePHP.">
        @if ($locationHasCoordinates)
            <div class="grid gap-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                        <p class="text-xs font-semibold uppercase text-app-muted ">Latitude</p>
                        <p class="mt-2 break-words text-sm font-semibold text-app-ink ">{{ $locationLatitude }}</p>
                    </div>

                    <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                        <p class="text-xs font-semibold uppercase text-app-muted ">Longitude</p>
                        <p class="mt-2 break-words text-sm font-semibold text-app-ink ">{{ $locationLongitude }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                        <p class="text-xs font-semibold uppercase text-app-muted ">Accuracy</p>
                        <p class="mt-2 text-sm font-semibold text-app-ink ">
                            {{ $locationAccuracy !== null ? $locationAccuracy.' m' : 'Unknown' }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                        <p class="text-xs font-semibold uppercase text-app-muted ">Provider</p>
                        <p class="mt-2 break-words text-sm font-semibold text-app-ink ">{{ $locationProvider ?? 'Unknown' }}</p>
                    </div>
                </div>

                <div class="rounded-lg border border-app-line bg-app-bg p-4  ">
                    <p class="text-xs font-semibold uppercase text-app-muted ">Timestamp</p>
                    <p class="mt-2 break-words text-sm font-semibold text-app-ink ">{{ $locationTimestamp ?? 'Unknown' }}</p>
                </div>
            </div>

            <x-slot:footer>
                <x-mobile.button wire:click="clearCheckIn" variant="secondary" full>
                    Clear check-in
                </x-mobile.button>
            </x-slot:footer>
        @else
            <x-mobile.empty-state
                title="No check-in yet"
                description="Request the current location to see coordinates, accuracy, provider, and timestamp."
            />
        @endif
    </x-mobile.card>

    <x-mobile.card title="Capabilities" description="NativePHP geolocation methods exposed by this app service.">
        <div class="grid gap-3">
            @forelse ($locationCapabilities as $capability)
                <div
                    wire:key="location-capability-{{ $capability['key'] }}"
                    class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3  "
                >
                    <span class="min-w-0">
                        <span class="block text-base font-semibold text-app-ink ">{{ $capability['label'] }}</span>
                        <span class="mt-1 block text-sm leading-5 text-app-muted ">{{ $capability['description'] }}</span>
                        <span class="mt-1 block text-xs font-medium text-app-muted ">{{ $capability['driver'] }}</span>
                    </span>

                    <x-mobile.badge :variant="$capability['supported'] ? 'success' : 'neutral'">
                        {{ $capability['supported'] ? 'Supported' : 'Unavailable' }}
                    </x-mobile.badge>
                </div>
            @empty
                <x-mobile.empty-state
                    title="No capabilities"
                    description="Location capabilities are not available."
                />
            @endforelse
        </div>
    </x-mobile.card>
</section>
