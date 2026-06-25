<div class="grid gap-4">
    <x-mobile.loading-state target="refreshTimeline" message="Refreshing timeline..." />

    @if (! $storageAvailable)
        <x-mobile.error-state
            title="Timeline unavailable"
            :message="$storageError ?: 'Run the mobile local storage migrations before viewing record activity.'"
        >
            <x-slot:action>
                <x-mobile.button wire:click="refreshTimeline" variant="secondary">
                    Retry
                </x-mobile.button>
            </x-slot:action>
        </x-mobile.error-state>
    @else
        <x-mobile.card title="Activity timeline" description="Record lifecycle, notes, attachments, and sync events.">
            <x-slot:action>
                <x-mobile.badge variant="neutral">
                    {{ $rowCount }} events
                </x-mobile.badge>
            </x-slot:action>

            <div class="grid gap-3">
                @forelse ($rows as $row)
                    <article wire:key="activity-timeline-{{ $row['key'] }}" class="grid grid-cols-[0.75rem_1fr] gap-3">
                        <div class="pt-1.5">
                            <span
                                @class([
                                    'block size-2.5 rounded-full',
                                    'bg-emerald-500 dark:bg-emerald-300' => $row['variant'] === 'success',
                                    'bg-red-500 dark:bg-red-300' => $row['variant'] === 'danger',
                                    'bg-app-accent dark:bg-emerald-400' => $row['variant'] === 'accent',
                                    'bg-app-muted dark:bg-zinc-500' => ! in_array($row['variant'], ['success', 'danger', 'accent'], true),
                                ])
                            ></span>
                        </div>

                        <div class="rounded-lg border border-app-line bg-app-bg p-3 dark:border-zinc-800 dark:bg-zinc-950">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="break-words text-sm font-semibold text-app-ink dark:text-zinc-100">{{ $row['title'] }}</p>
                                    <p class="mt-1 text-xs font-medium text-app-muted dark:text-zinc-400">{{ $row['time'] }}</p>
                                </div>

                                @if ($row['sync_status'])
                                    <x-mobile.badge
                                        :variant="$row['sync_status'] === 'failed' ? 'danger' : ($row['sync_status'] === 'synced' ? 'success' : 'neutral')"
                                        size="sm"
                                    >
                                        {{ $row['sync_status'] }}
                                    </x-mobile.badge>
                                @endif
                            </div>

                            <p class="mt-2 whitespace-pre-line break-words text-sm leading-5 text-app-ink dark:text-zinc-100">{{ $row['message'] }}</p>

                            @if ($row['meta'])
                                <p class="mt-2 break-words text-xs leading-5 text-app-muted dark:text-zinc-400">{{ $row['meta'] }}</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <x-mobile.empty-state
                        title="No activity yet"
                        description="Record lifecycle events will appear here after local actions are saved."
                    />
                @endforelse
            </div>

            <x-slot:footer>
                <x-mobile.button wire:click="refreshTimeline" variant="secondary" full>
                    Refresh timeline
                </x-mobile.button>
            </x-slot:footer>
        </x-mobile.card>
    @endif
</div>
