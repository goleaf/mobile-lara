<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="refreshHistory,setFilter,deleteScan,clearHistory,search,clearSearch" message="Updating scan history..." />

    <x-mobile.page-header
        title="Scan history"
        description="Saved QR and barcode scans stored on this device."
        :back-href="route('mobile.scanner')"
    >
        <x-slot:action>
            <x-mobile.badge variant="neutral">
                {{ $historyCount }} shown
            </x-mobile.badge>
        </x-slot:action>
    </x-mobile.page-header>

    @if (! $storageAvailable)
        <x-mobile.error-state
            title="Scan history unavailable"
            message="Run the mobile local storage migrations before viewing saved scans."
        >
            <x-slot:action>
                <x-mobile.button wire:click="refreshHistory" variant="secondary">
                    Retry
                </x-mobile.button>
            </x-slot:action>
        </x-mobile.error-state>
    @else
        <x-mobile.card title="History summary" description="Local scan totals and action status counts.">
            <div class="grid grid-cols-2 gap-3">
                @forelse ($metrics as $metric)
                    <div
                        wire:key="scan-history-metric-{{ $metric['label'] }}"
                        class="rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        <p class="text-xs font-semibold uppercase tracking-normal text-app-muted dark:text-zinc-500">
                            {{ $metric['label'] }}
                        </p>
                        <p class="mt-2 text-2xl font-semibold tracking-normal text-app-ink dark:text-zinc-100">
                            {{ $metric['value'] }}
                        </p>
                        <p class="mt-1 text-xs font-medium text-app-muted dark:text-zinc-400">
                            {{ $metric['description'] }}
                        </p>
                    </div>
                @empty
                    <x-mobile.empty-state
                        title="No metrics"
                        description="Scan totals will appear after local storage is available."
                    />
                @endforelse
            </div>
        </x-mobile.card>

        <x-mobile.card title="Filters" description="Narrow history by code type, action status, or text search.">
            <div class="grid gap-4">
                <x-mobile.input
                    wire:model.live.debounce.300ms="search"
                    name="search"
                    label="Search scan history"
                    placeholder="Search raw value, status, or action result"
                />

                <div class="flex gap-2 overflow-x-auto pb-1">
                    @forelse ($filters as $filterOption)
                        <button
                            type="button"
                            wire:key="scan-history-filter-{{ $filterOption['key'] }}"
                            wire:click="setFilter('{{ $filterOption['key'] }}')"
                            @class([
                                'inline-flex min-h-10 shrink-0 items-center gap-2 rounded-lg border px-3 text-sm font-semibold transition',
                                'border-app-ink bg-app-ink text-white dark:border-zinc-100 dark:bg-zinc-100 dark:text-zinc-950' => $filterOption['active'],
                                'border-app-line bg-app-bg text-app-ink hover:bg-app-surface dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:bg-zinc-900' => ! $filterOption['active'],
                            ])
                        >
                            <span>{{ $filterOption['label'] }}</span>
                            <span class="rounded-full bg-current/10 px-1.5 py-0.5 text-[11px]">
                                {{ $filterOption['count'] }}
                            </span>
                        </button>
                    @empty
                        <span class="text-sm font-medium text-app-muted dark:text-zinc-400">No filters available</span>
                    @endforelse
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <x-mobile.button wire:click="clearSearch" variant="secondary" full>
                        Clear search
                    </x-mobile.button>

                    <x-mobile.button
                        wire:click="clearHistory"
                        wire:confirm="Clear the currently shown scan history items from this device?"
                        variant="danger"
                        full
                    >
                        Clear shown
                    </x-mobile.button>
                </div>
            </div>
        </x-mobile.card>

        <x-mobile.card title="Scans" description="Newest saved scan payloads first.">
            <div class="grid gap-3">
                @forelse ($scanHistory as $scan)
                    <article
                        wire:key="scan-history-item-{{ $scan->id }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="break-words text-base font-semibold text-app-ink dark:text-zinc-100">
                                    {{ $scan->raw_value }}
                                </p>
                                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                                    {{ $scan->created_at?->diffForHumans() ?? 'Time unknown' }}
                                </p>
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <x-mobile.badge :variant="$scan->statusVariant()" dot>
                                    {{ $scan->status }}
                                </x-mobile.badge>

                                <x-mobile.badge variant="accent" size="sm">
                                    {{ $scan->scanTypeLabel() }}
                                </x-mobile.badge>
                            </div>
                        </div>

                        <div class="grid gap-2 rounded-lg border border-dashed border-app-line bg-app-surface p-3 dark:border-zinc-800 dark:bg-zinc-900">
                            <p class="text-xs font-semibold uppercase tracking-normal text-app-muted dark:text-zinc-500">
                                Parsed {{ $scan->parsedType() }}
                            </p>
                            <p class="break-words text-sm font-medium text-app-ink dark:text-zinc-100">
                                {{ $scan->parsedSummary() }}
                            </p>
                        </div>

                        <p class="text-sm leading-6 text-app-muted dark:text-zinc-400">
                            {{ $scan->actionResultPreview() }}
                        </p>

                        <div class="flex justify-end">
                            <x-mobile.button
                                wire:click="deleteScan({{ $scan->id }})"
                                wire:confirm="Delete this scan history item from this device?"
                                wire:loading.attr="disabled"
                                wire:target="deleteScan({{ $scan->id }})"
                                variant="danger"
                                size="sm"
                            >
                                <span wire:loading.remove wire:target="deleteScan({{ $scan->id }})">Delete</span>
                                <span wire:loading wire:target="deleteScan({{ $scan->id }})">Deleting</span>
                            </x-mobile.button>
                        </div>
                    </article>
                @empty
                    <x-mobile.empty-state
                        title="No saved scans"
                        description="Scans captured by the NativePHP scanner will appear here after they are stored locally."
                    />
                @endforelse
            </div>

            <x-slot:footer>
                <x-mobile.button wire:click="refreshHistory" variant="secondary" full>
                    Refresh history
                </x-mobile.button>
            </x-slot:footer>
        </x-mobile.card>
    @endif
</section>
