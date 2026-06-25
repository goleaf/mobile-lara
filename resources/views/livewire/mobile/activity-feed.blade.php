<section class="safe-x safe-pb flex min-h-full flex-col gap-4 py-6">
    <x-mobile.loading-state target="refreshFeed" message="Refreshing activity..." />

    <x-mobile.card title="Activity feed" description="Recent local app events stored on this device.">
        <x-slot:action>
            <button
                type="button"
                wire:click="refreshFeed"
                class="inline-flex min-h-10 items-center justify-center rounded-lg border border-app-line bg-app-bg px-3 text-sm font-semibold text-app-ink transition hover:bg-app-surface disabled:opacity-60 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:bg-zinc-900"
                wire:loading.attr="disabled"
                wire:target="refreshFeed"
            >
                <span wire:loading.remove wire:target="refreshFeed">Refresh</span>
                <span wire:loading wire:target="refreshFeed">Refreshing</span>
            </button>
        </x-slot:action>

        <div class="grid gap-4">
            @forelse ($activities as $activity)
                <article wire:key="activity-log-{{ $activity->id }}" class="grid grid-cols-[auto_1fr] gap-3">
                    <span @class([
                        'mt-1.5 size-2.5 rounded-full',
                        'bg-app-accent dark:bg-emerald-400' => $activity->sync_status === 'synced',
                        'bg-amber-500 dark:bg-amber-400' => $activity->sync_status === 'pending',
                        'bg-red-500 dark:bg-red-400' => $activity->sync_status === 'failed',
                        'bg-app-line dark:bg-zinc-700' => ! in_array($activity->sync_status, ['synced', 'pending', 'failed'], true),
                    ])></span>

                    <div class="min-w-0 border-b border-app-line pb-4 last:border-b-0 last:pb-0 dark:border-zinc-800">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-normal text-app-muted dark:text-zinc-500">{{ $activity->action }}</p>
                                <h3 class="mt-1 text-sm font-semibold text-app-ink dark:text-zinc-100">{{ $activity->message }}</h3>
                            </div>

                            <x-mobile.badge variant="neutral" size="sm">
                                {{ $activity->sync_status }}
                            </x-mobile.badge>
                        </div>

                        @if ($activity->entity_type || $activity->entity_id)
                            <p class="mt-2 text-xs font-medium text-app-muted dark:text-zinc-400">
                                {{ $activity->entity_type ?: 'entity' }} @if ($activity->entity_id) #{{ $activity->entity_id }} @endif
                            </p>
                        @endif

                        @if (filled($activity->metadata))
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($activity->metadata as $key => $value)
                                    <span wire:key="activity-log-{{ $activity->id }}-metadata-{{ $key }}" class="rounded-lg bg-app-bg px-2 py-1 text-xs font-medium text-app-muted dark:bg-zinc-950 dark:text-zinc-400">
                                        {{ $key }}: {{ is_scalar($value) ? $value : json_encode($value) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <p class="mt-3 text-xs font-medium text-app-muted dark:text-zinc-500">
                            {{ $activity->created_at?->diffForHumans() ?? 'Time unknown' }}
                        </p>
                    </div>
                </article>
            @empty
                <x-mobile.empty-state title="No activity yet" description="Local activity will appear here after the app records device events." />
            @endforelse
        </div>
    </x-mobile.card>
</section>
