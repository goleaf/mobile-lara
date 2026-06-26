<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="Mobile Sync Monitor"
        description="Server-recorded mobile replay outcomes, conflicts, rejections, and acknowledgements."
    />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Total</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['total'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Last 24h</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $summary['recent'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Conflicts</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700 dark:text-amber-300">{{ $summary['conflicts'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Rejected</p>
            <p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-300">{{ $summary['rejected'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Unacknowledged</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['unacknowledged'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(28rem,0.8fr)]">
        <div class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_14rem]">
                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Search
                    <input
                        type="search"
                        wire:model.blur="search"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        autocomplete="off"
                    >
                </label>

                <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Outcome
                    <select
                        wire:model.change="outcome"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        <option value="">All outcomes</option>

                        @foreach ($outcomeOptions as $value => $label)
                            <option wire:key="sync-outcome-filter-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-normal text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                            <tr>
                                <th class="px-4 py-3">Processed</th>
                                <th class="px-4 py-3">Tenant</th>
                                <th class="px-4 py-3">Action</th>
                                <th class="px-4 py-3">Outcome</th>
                                <th class="px-4 py-3">Device</th>
                                <th class="px-4 py-3 text-right">Review</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($events as $event)
                                <tr wire:key="mobile-sync-event-{{ $event->id }}" class="align-top">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">
                                            {{ $event->processed_at?->toDayDateTimeString() ?: 'Unknown' }}
                                        </p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ str($event->public_id)->limit(12, '') }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">
                                            {{ $event->tenant?->name ?: 'Unknown tenant' }}
                                        </p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $event->tenant?->slug ?: 'none' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">
                                            {{ $event->collection }} / {{ $event->action }}
                                        </p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $event->target_public_id ?: 'no target' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.status-badge :tone="$this->outcomeTone($event->outcome)">
                                            {{ $event->outcome }}
                                        </x-admin.status-badge>
                                        @if ($event->error_code)
                                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $event->error_code }}
                                            </p>
                                        @endif
                                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $event->isAcknowledged() ? 'acknowledged' : 'pending ack' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                        <p>{{ $event->deviceSession?->device_name ?: 'Unknown device' }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $event->deviceSession?->platform ?: 'unknown' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            type="button"
                                            wire:click="selectEvent({{ $event->id }})"
                                            class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                        >
                                            Review
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        No sync events found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $events->links() }}
            </div>
        </div>

        <aside class="grid content-start gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            @if ($selectedEvent)
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">Sync event detail</h2>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $selectedEvent->public_id }}</p>
                    </div>

                    <button
                        type="button"
                        wire:click="clearSelectedEvent"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        Close
                    </button>
                </div>

                <dl class="grid gap-3 text-sm">
                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">User</dt>
                        <dd class="mt-1 font-medium text-zinc-950 dark:text-zinc-100">
                            {{ $selectedEvent->user?->name ?: 'Unknown user' }} / #{{ $selectedEvent->user_id ?: 'none' }}
                        </dd>
                    </div>

                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Batch</dt>
                        <dd class="mt-1 text-zinc-700 dark:text-zinc-300">
                            {{ $selectedEvent->client_batch_id ?: 'none' }}
                        </dd>
                    </div>

                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Intent</dt>
                        <dd class="mt-1 break-all text-zinc-700 dark:text-zinc-300">
                            {{ $selectedEvent->client_intent_id }}
                        </dd>
                    </div>

                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Idempotency</dt>
                        <dd class="mt-1 break-all text-zinc-700 dark:text-zinc-300">
                            {{ $selectedEvent->idempotency_key }}
                        </dd>
                    </div>

                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Version</dt>
                        <dd class="mt-1 text-zinc-700 dark:text-zinc-300">
                            Base {{ $selectedEvent->base_sync_version ?: 'none' }}
                        </dd>
                    </div>

                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Error</dt>
                        <dd class="mt-1 text-zinc-700 dark:text-zinc-300">
                            {{ $selectedEvent->error_code ?: 'none' }}
                            @if ($selectedEvent->error_message)
                                <span class="mt-1 block text-zinc-500 dark:text-zinc-400">{{ $selectedEvent->error_message }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="grid gap-2">
                    <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-100">Response payload</h3>
                    <pre class="max-h-96 overflow-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-800">{{ $this->responsePayloadJson($selectedEvent) }}</pre>
                </div>
            @else
                <div class="rounded-lg border border-zinc-200 p-4 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                    Select a sync event to review replay context and response payload.
                </div>
            @endif
        </aside>
    </div>
</section>
