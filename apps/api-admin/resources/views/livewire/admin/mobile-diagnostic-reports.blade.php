<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="Mobile Diagnostics"
        description="Privacy-filtered mobile troubleshooting snapshots uploaded through the API."
    />

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Total</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['total'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Last 24h</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $summary['recent'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Failed sync</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700 dark:text-amber-300">{{ $summary['failed_sync'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(28rem,0.8fr)]">
        <div class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Search
                <input
                    type="search"
                    wire:model.blur="search"
                    class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    autocomplete="off"
                >
            </label>

            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-normal text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                            <tr>
                                <th class="px-4 py-3">Received</th>
                                <th class="px-4 py-3">Tenant</th>
                                <th class="px-4 py-3">App</th>
                                <th class="px-4 py-3">Device</th>
                                <th class="px-4 py-3">Sync</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($reports as $report)
                                <tr wire:key="mobile-diagnostic-report-{{ $report->id }}" class="align-top">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">
                                            {{ $report->received_at?->toDayDateTimeString() ?: 'Unknown' }}
                                        </p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ str($report->public_id)->limit(12, '') }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">
                                            {{ $report->tenant?->name ?: 'Unknown tenant' }}
                                        </p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $report->tenant?->slug ?: 'none' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                        {{ $report->app_version ?: 'Unknown' }}
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                        <p>{{ $report->deviceSession?->device_name ?: 'Unknown device' }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $report->deviceSession?->platform ?: 'unknown' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.status-badge :tone="$report->failed_sync_actions_count > 0 ? 'danger' : 'success'">
                                            {{ $report->failed_sync_actions_count }} failed
                                        </x-admin.status-badge>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            type="button"
                                            wire:click="selectReport({{ $report->id }})"
                                            class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                        >
                                            Review
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        No diagnostics reports found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $reports->links() }}
            </div>
        </div>

        <aside class="grid content-start gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            @if ($selectedReport)
                @php($snapshotSummary = $this->snapshotSummary($selectedReport))

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">Report detail</h2>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $selectedReport->public_id }}</p>
                    </div>

                    <button
                        type="button"
                        wire:click="clearSelectedReport"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        Close
                    </button>
                </div>

                <dl class="grid gap-3 text-sm">
                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">User</dt>
                        <dd class="mt-1 font-medium text-zinc-950 dark:text-zinc-100">
                            {{ $selectedReport->user?->name ?: 'Unknown user' }} · #{{ $selectedReport->user_id }}
                        </dd>
                    </div>

                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Network</dt>
                        <dd class="mt-1 text-zinc-700 dark:text-zinc-300">{{ $snapshotSummary['network'] }}</dd>
                    </div>

                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Sync</dt>
                        <dd class="mt-1 text-zinc-700 dark:text-zinc-300">{{ $snapshotSummary['sync'] }}</dd>
                    </div>

                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Device</dt>
                        <dd class="mt-1 text-zinc-700 dark:text-zinc-300">{{ $snapshotSummary['device'] }}</dd>
                    </div>

                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <dt class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Versions</dt>
                        <dd class="mt-1 text-zinc-700 dark:text-zinc-300">
                            Features {{ $snapshotSummary['feature_version'] }} · Config {{ $snapshotSummary['config_version'] }}
                        </dd>
                    </div>
                </dl>

                <div class="grid gap-2">
                    <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-100">Redactions</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse (($selectedReport->redactions_applied ?? []) as $redaction)
                            <x-admin.status-badge tone="neutral">
                                {{ $redaction }}
                            </x-admin.status-badge>
                        @empty
                            <x-admin.status-badge tone="danger">
                                none
                            </x-admin.status-badge>
                        @endforelse
                    </div>
                </div>

                <div class="grid gap-2">
                    <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-100">Snapshot</h3>
                    <pre class="max-h-96 overflow-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-800">{{ $this->snapshotJson($selectedReport) }}</pre>
                </div>
            @else
                <div class="rounded-lg border border-zinc-200 p-4 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                    Select a diagnostics report to review its redacted snapshot.
                </div>
            @endif
        </aside>
    </div>
</section>
