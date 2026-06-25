<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="QR/barcode scanner"
        description="NativePHP scanner bridge for QR codes and barcodes."
        :back-href="route('mobile.settings.developer')"
    >
        <x-slot:action>
            <a
                href="{{ route('mobile.scan-history') }}"
                wire:navigate
                class="inline-flex min-h-10 items-center justify-center rounded-lg border border-app-line bg-app-surface px-3 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800"
            >
                History
            </a>
        </x-slot:action>
    </x-mobile.page-header>

    <x-mobile.card
        title="Scanner bridge"
        description="Native scan events return directly into this Livewire screen."
    >
        <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
            <div class="min-w-0">
                <p class="text-base font-semibold text-app-ink dark:text-zinc-100">
                    {{ $nativeScannerAvailable ? 'Native scanner available' : 'Browser fallback active' }}
                </p>
                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                    {{ $nativeScannerAvailable ? 'QR and barcode scanner requests can be opened on this device.' : 'Open this route inside NativePHP or Jump Bridge to launch native scanner controls.' }}
                </p>
            </div>

            <x-mobile.badge :variant="$nativeScannerAvailable ? 'success' : 'warning'" dot>
                {{ $nativeScannerAvailable ? 'Ready' : 'Fallback' }}
            </x-mobile.badge>
        </div>

        @if ($pendingScanId)
            <div class="mt-3 rounded-lg border border-sky-200 bg-sky-50 p-4 dark:border-sky-400/30 dark:bg-sky-400/10">
                <p class="text-sm font-semibold text-sky-950 dark:text-sky-100">Pending scan session</p>
                <p class="mt-1 break-words text-sm text-sky-900 dark:text-sky-100/80">{{ $pendingScanMode }} · {{ $pendingScanId }}</p>
            </div>
        @endif

        <x-slot:footer>
            <div aria-live="polite" class="min-h-6">
                @if ($scanError)
                    <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 dark:border-amber-300/20 dark:bg-amber-300/10 dark:text-amber-100">
                        {{ $scanError }}
                    </p>
                @elseif ($scanStatus)
                    <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-200">
                        {{ $scanStatus }}
                    </p>
                @endif
            </div>
        </x-slot:footer>
    </x-mobile.card>

    <x-mobile.card title="Scan setup" description="Choose a format, set the native prompt, then open the scanner.">
        <div class="grid gap-4">
            <x-mobile.select
                wire:model="selectedFormat"
                name="selectedFormat"
                label="Barcode format"
                :options="$formatSelectOptions"
            />

            <x-mobile.input
                wire:model.blur="prompt"
                name="prompt"
                label="Scanner prompt"
                maxlength="120"
            />

            <div class="grid gap-3">
                @forelse ($scannerActions as $scannerAction)
                    <div
                        wire:key="scanner-action-{{ $scannerAction['action'] }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        <div>
                            <p class="text-base font-semibold text-app-ink dark:text-zinc-100">{{ $scannerAction['label'] }}</p>
                            <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $scannerAction['description'] }}</p>
                        </div>

                        <x-mobile.button
                            wire:click="{{ $scannerAction['action'] }}"
                            wire:loading.attr="disabled"
                            wire:target="{{ $scannerAction['action'] }}"
                            :variant="$scannerAction['variant']"
                            full
                        >
                            <span wire:loading.remove wire:target="{{ $scannerAction['action'] }}">{{ $scannerAction['label'] }}</span>
                            <span wire:loading wire:target="{{ $scannerAction['action'] }}">Opening</span>
                        </x-mobile.button>
                    </div>
                @empty
                    <x-mobile.empty-state
                        title="No scanner actions"
                        description="Scanner actions are not configured."
                    />
                @endforelse
            </div>

            @if ($pendingScanMode === 'continuous')
                <x-mobile.button wire:click="stopContinuousScan" variant="secondary" full>
                    Stop continuous tracking
                </x-mobile.button>
            @endif
        </div>
    </x-mobile.card>

    <x-mobile.card title="Latest result" description="Most recent QR or barcode payload returned by the native scanner.">
        @if ($latestData)
            <div class="grid gap-3">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="break-words text-lg font-semibold text-app-ink dark:text-zinc-100">{{ $latestData }}</p>
                        <p class="mt-1 text-sm text-app-muted dark:text-zinc-400">{{ $latestScannedAt }}</p>
                    </div>

                    <x-mobile.badge variant="success">
                        {{ strtoupper($latestFormat ?? 'unknown') }}
                    </x-mobile.badge>
                </div>

                <p class="break-words rounded-lg border border-dashed border-app-line bg-app-bg px-3 py-2 text-sm font-medium text-app-muted dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400">
                    {{ $latestData }}
                </p>
            </div>

            <x-slot:footer>
                <x-mobile.button wire:click="clearLatestResult" variant="secondary" full>
                    Clear latest result
                </x-mobile.button>
            </x-slot:footer>
        @else
            <x-mobile.empty-state
                title="No scan result"
                description="Scan a QR code or barcode to show the latest payload here."
            />
        @endif
    </x-mobile.card>

    <x-mobile.card title="Scan history" description="Current-session scan results, newest first.">
        @if ($scanHistory !== [])
            <div class="grid gap-3">
                @forelse ($scanHistory as $scan)
                    <div
                        wire:key="scan-history-{{ $scan['key'] }}"
                        class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="break-words text-base font-semibold text-app-ink dark:text-zinc-100">{{ $scan['data'] }}</p>
                                <p class="mt-1 text-sm text-app-muted dark:text-zinc-400">{{ $scan['scanned_at'] }}</p>
                            </div>

                            <x-mobile.badge :variant="$scan['format'] === 'qr' ? 'accent' : 'neutral'">
                                {{ strtoupper($scan['format']) }}
                            </x-mobile.badge>
                        </div>

                        @if ($scan['id'])
                            <p class="mt-3 break-words rounded-lg border border-dashed border-app-line bg-app-surface px-3 py-2 text-xs font-medium text-app-muted dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
                                {{ $scan['id'] }}
                            </p>
                        @endif
                    </div>
                @empty
                    <x-mobile.empty-state
                        title="No scan history"
                        description="Scanned codes will appear here."
                    />
                @endforelse
            </div>

            <x-slot:footer>
                <x-mobile.button wire:click="clearHistory" variant="secondary" full>
                    Clear scan history
                </x-mobile.button>
            </x-slot:footer>
        @else
            <x-mobile.empty-state
                title="No scan history"
                description="Scanned codes will appear here until the screen is refreshed."
            />
        @endif
    </x-mobile.card>

    <x-mobile.card title="Capabilities" description="NativePHP scanner bridge methods and accepted code formats.">
        <div class="grid gap-3">
            @forelse ($scannerCapabilities as $capability)
                <div
                    wire:key="scanner-capability-{{ $capability['key'] }}"
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
                    description="Scanner capabilities are not available."
                />
            @endforelse
        </div>

        <x-slot:footer>
            <div class="grid gap-2">
                @forelse ($formatOptions as $format)
                    <p wire:key="scanner-format-{{ $format['value'] }}" class="text-sm leading-5 text-app-muted dark:text-zinc-400">
                        <span class="font-semibold text-app-ink dark:text-zinc-100">{{ $format['label'] }}:</span>
                        {{ $format['description'] }}
                    </p>
                @empty
                    <p class="text-sm text-app-muted dark:text-zinc-400">No barcode formats are configured.</p>
                @endforelse
            </div>
        </x-slot:footer>
    </x-mobile.card>
</section>
