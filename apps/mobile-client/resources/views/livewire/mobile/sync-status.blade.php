<div wire:poll.30s>
    <x-mobile.card title="Sync status" :description="$summaryDescription">
        <x-slot:action>
            <x-mobile.badge :variant="$networkVariant" dot>
                {{ $networkLabel }}
            </x-mobile.badge>
        </x-slot:action>

        <p class="mb-3 text-sm font-medium text-app-muted dark:text-zinc-400">
            {{ $networkDescription }}
        </p>

        <div class="grid grid-cols-3 gap-2 text-center">
            <div class="rounded-lg bg-app-bg px-3 py-3 dark:bg-zinc-950">
                <p class="text-xl font-semibold text-app-ink dark:text-zinc-100">{{ $pendingActionCount }}</p>
                <p class="mt-1 text-[11px] font-medium text-app-muted dark:text-zinc-400">Pending actions</p>
            </div>

            <div class="rounded-lg bg-app-bg px-3 py-3 dark:bg-zinc-950">
                <p class="text-xl font-semibold text-app-ink dark:text-zinc-100">{{ $failedSyncCount }}</p>
                <p class="mt-1 text-[11px] font-medium text-app-muted dark:text-zinc-400">Failed syncs</p>
            </div>

            <div class="rounded-lg bg-app-bg px-3 py-3 dark:bg-zinc-950">
                <p class="text-sm font-semibold leading-6 text-app-ink dark:text-zinc-100">{{ $lastSyncLabel }}</p>
                <p class="mt-1 text-[11px] font-medium text-app-muted dark:text-zinc-400">Last sync</p>
            </div>
        </div>

        <div class="mt-4 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950">
            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-app-ink dark:text-zinc-100">Sync in progress state</p>
                    <p class="mt-1 text-sm text-app-muted dark:text-zinc-400">
                        <span wire:loading.remove wire:target="syncNow">
                            {{ $syncInProgress ? 'Sync in progress' : 'Idle' }}
                        </span>
                        <span wire:loading wire:target="syncNow">
                            Sync in progress
                        </span>
                    </p>
                </div>

                <x-mobile.badge :variant="$summaryVariant" size="sm" dot>
                    Queue
                </x-mobile.badge>
            </div>

            @if ($statusMessage)
                <p @class([
                    'mt-3 rounded-lg px-3 py-2 text-sm font-medium',
                    'bg-emerald-50 text-emerald-800 dark:bg-emerald-400/10 dark:text-emerald-100' => $statusVariant === 'success',
                    'bg-amber-50 text-amber-900 dark:bg-amber-300/10 dark:text-amber-100' => $statusVariant === 'warning',
                    'bg-red-50 text-red-800 dark:bg-red-400/10 dark:text-red-100' => $statusVariant === 'error',
                    'bg-sky-50 text-sky-800 dark:bg-sky-400/10 dark:text-sky-100' => $statusVariant === 'info',
                ])>
                    {{ $statusMessage }}
                </p>
            @endif
        </div>

        @if (! $syncPolicy['sync']['allowed'])
            <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 dark:border-amber-300/20 dark:bg-amber-300/10 dark:text-amber-100">
                {{ $syncPolicy['sync']['message'] }}
            </p>
        @endif

        <x-slot:footer>
            <x-mobile.button
                wire:click="syncNow"
                wire:loading.attr="disabled"
                wire:target="syncNow"
                :disabled="! $canSync"
                full
            >
                <span wire:loading.remove wire:target="syncNow">Sync Now</span>
                <span wire:loading wire:target="syncNow">Syncing...</span>
            </x-mobile.button>
        </x-slot:footer>
    </x-mobile.card>
</div>
