<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="refreshHistory,setFilter" message="Refreshing check-ins..." />

    <x-mobile.page-header
        title="Check-in history"
        description="Local location check-ins stored on this device."
        :back-href="route('mobile.dashboard')"
    >
        @if ($canCreateCheckIn)
            <x-slot:action>
                <a
                    href="{{ route('mobile.check-ins.create') }}"
                    wire:navigate
                    class="inline-flex min-h-10 items-center justify-center rounded-lg border border-app-line bg-app-surface px-3 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800"
                >
                    Create
                </a>
            </x-slot:action>
        @endif
    </x-mobile.page-header>

    @if (! $storageAvailable)
        <x-mobile.error-state
            title="Check-in storage unavailable"
            message="Run the mobile local storage migrations before viewing local check-ins."
        >
            <x-slot:action>
                <x-mobile.button wire:click="refreshHistory" variant="secondary">
                    Retry
                </x-mobile.button>
            </x-slot:action>
        </x-mobile.error-state>
    @else
        <x-mobile.card title="History summary" description="Sync state totals from the local check_ins table.">
            <div class="grid grid-cols-2 gap-3">
                @forelse ($metrics as $metric)
                    <div
                        wire:key="check-in-history-metric-{{ $metric['label'] }}"
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
                        description="Check-in totals will appear after local storage is available."
                    />
                @endforelse
            </div>
        </x-mobile.card>

        <x-mobile.card title="Check-ins" description="Filter saved check-ins by sync state.">
            <x-slot:action>
                <x-mobile.badge variant="neutral">
                    {{ $historyCount }} shown
                </x-mobile.badge>
            </x-slot:action>

            <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
                @forelse ($filters as $filterOption)
                    <button
                        type="button"
                        wire:key="check-in-history-filter-{{ $filterOption['key'] }}"
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

            <div class="grid gap-3">
                @forelse ($checkIns as $checkIn)
                    <article
                        wire:key="check-in-history-item-{{ $checkIn->id }}"
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4 dark:border-zinc-800 dark:bg-zinc-950"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="break-words text-base font-semibold text-app-ink dark:text-zinc-100">
                                    {{ $checkIn->coordinates() }}
                                </p>
                                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                                    {{ $checkIn->created_at?->diffForHumans() ?? 'Time unknown' }}
                                    @if ($checkIn->formattedAccuracy())
                                        / {{ $checkIn->formattedAccuracy() }}
                                    @endif
                                </p>
                            </div>

                            <x-mobile.badge :variant="$checkIn->sync_status === 'failed' ? 'danger' : ($checkIn->sync_status === 'synced' ? 'success' : 'warning')" dot>
                                {{ $checkIn->sync_status }}
                            </x-mobile.badge>
                        </div>

                        <p class="text-sm leading-6 text-app-ink dark:text-zinc-200">
                            {{ $checkIn->notePreview() }}
                        </p>

                        <div class="flex flex-wrap gap-2">
                            @if ($checkIn->photoLabel())
                                <x-mobile.badge variant="accent" size="sm">
                                    {{ $checkIn->photoLabel() }}
                                </x-mobile.badge>
                            @endif

                            <x-mobile.badge variant="neutral" size="sm">
                                User #{{ $checkIn->user_id }}
                            </x-mobile.badge>
                        </div>
                    </article>
                @empty
                    <x-mobile.empty-state
                        title="No check-ins"
                        description="Saved location check-ins will appear here after they are stored locally."
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
