<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Storage settings"
        description="Review local storage estimates and manage cached mobile data."
        :back-href="route('mobile.settings')"
    />

    @if ($statusMessage)
        @php
            $statusClasses = [
                'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-900/60 dark:bg-emerald-950 dark:text-emerald-100',
                'error' => 'border-red-200 bg-red-50 text-red-900 dark:border-red-900/60 dark:bg-red-950 dark:text-red-100',
                'warning' => 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900/60 dark:bg-amber-950 dark:text-amber-100',
                'info' => 'border-sky-200 bg-sky-50 text-sky-900 dark:border-sky-900/60 dark:bg-sky-950 dark:text-sky-100',
            ][$statusVariant] ?? 'border-sky-200 bg-sky-50 text-sky-900 dark:border-sky-900/60 dark:bg-sky-950 dark:text-sky-100';
        @endphp

        <div class="{{ $statusClasses }} rounded-lg border px-4 py-3 text-sm font-medium">
            {{ $statusMessage }}
        </div>
    @endif

    <x-mobile.card title="Storage overview" description="Device storage values are local estimates until native telemetry is connected.">
        <div class="grid gap-3">
            @forelse ($storageRows as $row)
                <div
                    wire:key="storage-row-{{ str($row['label'])->slug() }}"
                    class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-app-ink dark:text-zinc-100">{{ $row['label'] }}</p>
                            <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $row['description'] }}</p>
                        </div>

                        <p class="max-w-36 shrink-0 truncate text-right text-sm font-semibold text-app-ink dark:text-zinc-100">
                            {{ $row['value'] }}
                        </p>
                    </div>
                </div>
            @empty
                <x-mobile.empty-state
                    title="No storage details"
                    description="Storage estimates are not available yet."
                />
            @endforelse
        </div>
    </x-mobile.card>

    <x-mobile.card title="Storage actions" description="Destructive actions require confirmation before they run.">
        <div class="grid gap-3">
            <x-mobile.button
                variant="secondary"
                size="lg"
                full
                wire:click="confirmClearCache"
            >
                Clear cache
            </x-mobile.button>

            <x-mobile.button
                variant="danger"
                size="lg"
                full
                wire:click="confirmResetLocalData"
            >
                Reset local data
            </x-mobile.button>

            <x-mobile.button
                variant="ghost"
                size="lg"
                full
                wire:click="exportLocalData"
                wire:loading.attr="disabled"
                wire:target="exportLocalData"
            >
                <span wire:loading.remove wire:target="exportLocalData">Export local data</span>
                <span wire:loading wire:target="exportLocalData">Preparing export</span>
            </x-mobile.button>
        </div>
    </x-mobile.card>

    <x-mobile.modal
        :show="$confirmingClearCache"
        title="Clear file cache?"
        description="Cached files can be rebuilt, but offline previews may need to download again."
    >
        <p>
            This clears the configured Laravel file cache directory for the mobile app. Local account data and queued offline actions stay in place.
        </p>

        <x-slot:footer>
            <x-mobile.button variant="secondary" wire:click="cancelClearCache">
                Cancel
            </x-mobile.button>

            <x-mobile.button
                variant="danger"
                wire:click="clearCache"
                wire:loading.attr="disabled"
                wire:target="clearCache"
            >
                <span wire:loading.remove wire:target="clearCache">Clear cache</span>
                <span wire:loading wire:target="clearCache">Clearing</span>
            </x-mobile.button>
        </x-slot:footer>
    </x-mobile.modal>

    <x-mobile.modal
        :show="$confirmingResetLocalData"
        title="Reset local data?"
        description="This removes local-only data from this device and recreates the mobile database schema."
    >
        <p>
            Offline actions, activity logs, local preferences, and cached local records may be removed from this device. Server-side account data is not deleted.
        </p>

        <x-slot:footer>
            <x-mobile.button variant="secondary" wire:click="cancelResetLocalData">
                Cancel
            </x-mobile.button>

            <x-mobile.button
                variant="danger"
                wire:click="resetLocalData"
                wire:loading.attr="disabled"
                wire:target="resetLocalData"
            >
                <span wire:loading.remove wire:target="resetLocalData">Reset local data</span>
                <span wire:loading wire:target="resetLocalData">Resetting</span>
            </x-mobile.button>
        </x-slot:footer>
    </x-mobile.modal>
</section>
