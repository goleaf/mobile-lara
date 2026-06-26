<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Media capture"
        description="Native camera, video recorder, and gallery picker demo."
        :back-href="route('mobile.settings.developer')"
    />

    <x-mobile.card
        title="Camera bridge"
        description="NativePHP camera actions emit events back into this Livewire screen."
    >
        <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
            <div class="min-w-0">
                <p class="text-base font-semibold text-app-ink dark:text-zinc-100">
                    {{ $nativeCameraAvailable ? 'Native camera available' : 'Browser fallback active' }}
                </p>
                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                    {{ $nativeCameraAvailable ? 'Camera and gallery requests can be opened on this device.' : 'Open this route inside NativePHP or Jump Bridge to launch native media controls.' }}
                </p>
            </div>

            <x-mobile.badge :variant="$nativeCameraAvailable ? 'success' : 'warning'" dot>
                {{ $nativeCameraAvailable ? 'Ready' : 'Fallback' }}
            </x-mobile.badge>
        </div>

        @if ($pendingOperationId)
            <div class="mt-3 rounded-lg border border-sky-200 bg-sky-50 p-4 dark:border-sky-400/30 dark:bg-sky-400/10">
                <p class="text-sm font-semibold text-sky-950 dark:text-sky-100">Pending native operation</p>
                <p class="mt-1 break-words text-sm text-sky-900 dark:text-sky-100/80">{{ $pendingOperation }} · {{ $pendingOperationId }}</p>
            </div>
        @endif

        <x-slot:footer>
            <div aria-live="polite" class="min-h-6">
                @if ($mediaError)
                    <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 dark:border-amber-300/20 dark:bg-amber-300/10 dark:text-amber-100">
                        {{ $mediaError }}
                    </p>
                @elseif ($mediaStatus)
                    <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
                        {{ $mediaStatus }}
                    </p>
                @endif
            </div>
        </x-slot:footer>
    </x-mobile.card>

    <x-mobile.card title="Capture actions" description="Launch native media flows and wait for NativePHP events.">
        @if (! $mediaPolicy['camera']['allowed'])
            <x-mobile.error-state
                title="Media capture disabled"
                :message="$mediaPolicy['camera']['message']"
            />
        @else
            <div class="grid gap-3">
                @forelse ($mediaActions as $mediaAction)
                    <div
                        wire:key="media-action-{{ $mediaAction['action'] }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        <div>
                            <p class="text-base font-semibold text-app-ink dark:text-zinc-100">{{ $mediaAction['label'] }}</p>
                            <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $mediaAction['description'] }}</p>
                        </div>

                        <x-mobile.button
                            wire:click="{{ $mediaAction['action'] }}"
                            wire:loading.attr="disabled"
                            wire:target="{{ $mediaAction['action'] }}"
                            :variant="$mediaAction['variant']"
                            full
                        >
                            <span wire:loading.remove wire:target="{{ $mediaAction['action'] }}">{{ $mediaAction['label'] }}</span>
                            <span wire:loading wire:target="{{ $mediaAction['action'] }}">Opening</span>
                        </x-mobile.button>
                    </div>
                @empty
                    <x-mobile.empty-state
                        title="No media actions"
                        description="Camera actions are not configured."
                    />
                @endforelse
            </div>
        @endif
    </x-mobile.card>

    <x-mobile.card title="Capabilities" description="NativePHP camera plugin methods exposed by this app service.">
        <div class="grid gap-3">
            @forelse ($cameraCapabilities as $capability)
                <div
                    wire:key="camera-capability-{{ $capability['key'] }}"
                    class="flex min-h-16 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950"
                >
                    <span class="min-w-0">
                        <span class="block text-base font-semibold text-app-ink dark:text-zinc-100">{{ $capability['label'] }}</span>
                        <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $capability['description'] }}</span>
                    </span>

                    <x-mobile.badge :variant="$capability['supported'] ? 'success' : 'neutral'">
                        {{ $capability['supported'] ? 'Supported' : 'Unavailable' }}
                    </x-mobile.badge>
                </div>
            @empty
                <x-mobile.empty-state
                    title="No capabilities"
                    description="Camera capabilities are not available."
                />
            @endforelse
        </div>
    </x-mobile.card>

    <x-mobile.card title="Recent media" description="Returned file metadata from captured or selected native media.">
        @if ($mediaItems !== [])
            <div class="grid gap-3">
                @forelse ($mediaItems as $mediaItem)
                    <div
                        wire:key="media-item-{{ $mediaItem['key'] }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="break-words text-base font-semibold text-app-ink dark:text-zinc-100">{{ $mediaItem['name'] }}</p>
                                <p class="mt-1 text-sm text-app-muted dark:text-zinc-400">{{ $mediaItem['source'] }} · {{ $mediaItem['mime_type'] }}</p>
                            </div>

                            <x-mobile.badge :variant="$mediaItem['type'] === 'video' ? 'accent' : 'success'">
                                {{ ucfirst($mediaItem['type']) }}
                            </x-mobile.badge>
                        </div>

                        <p class="break-words rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 text-xs font-medium text-app-muted dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
                            {{ $mediaItem['path'] }}
                        </p>
                    </div>
                @empty
                    <x-mobile.empty-state
                        title="No media yet"
                        description="Captured media will appear here."
                    />
                @endforelse
            </div>

            <x-slot:footer>
                <x-mobile.button wire:click="clearMedia" variant="secondary" full>
                    Clear media list
                </x-mobile.button>
            </x-slot:footer>
        @else
            <x-mobile.empty-state
                title="No media yet"
                description="Take a photo, record a video, or select gallery media to see returned file metadata."
            />
        @endif
    </x-mobile.card>
</section>
