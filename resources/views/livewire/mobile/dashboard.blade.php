<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="refreshDashboard" message="Refreshing dashboard..." />

    <x-mobile.page-skeleton wire:loading.delay wire:target="refreshDashboard" />

    <div wire:loading.remove wire:target="refreshDashboard" class="contents">
        @if ($hasNetworkError)
            <x-mobile.network-error-state retry-action="refreshDashboard" />
        @elseif (! $hasDashboardContent)
            <x-mobile.empty-state title="No dashboard data" description="Refresh the page to load your mobile overview.">
                <x-slot:action>
                    <x-mobile.retry-button wire:click="refreshDashboard" target="refreshDashboard">
                        Refresh dashboard
                    </x-mobile.retry-button>
                </x-slot:action>
            </x-mobile.empty-state>
        @else
            <x-mobile.card title="Status" description="Core mobile routes are available.">
                <div class="flex items-end justify-between gap-4">
                    <p class="text-3xl font-semibold text-app-ink dark:text-zinc-100">Ready</p>
                    <x-mobile.badge variant="success" dot>Online</x-mobile.badge>
                </div>
            </x-mobile.card>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('mobile.search') }}" wire:navigate class="rounded-lg border border-app-line bg-app-surface p-4 shadow-sm transition hover:bg-app-bg dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none dark:hover:bg-zinc-800">
                    <p class="text-lg font-semibold text-app-ink dark:text-zinc-100">Search</p>
                    <p class="mt-1 text-sm text-app-muted dark:text-zinc-400">Find content</p>
                </a>

                <a href="{{ route('mobile.notifications') }}" wire:navigate class="rounded-lg border border-app-line bg-app-surface p-4 shadow-sm transition hover:bg-app-bg dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none dark:hover:bg-zinc-800">
                    <p class="text-lg font-semibold text-app-ink dark:text-zinc-100">Alerts</p>
                    <p class="mt-1 text-sm text-app-muted dark:text-zinc-400">Recent updates</p>
                </a>
            </div>
        @endif
    </div>

    <x-mobile.retry-button wire:click="refreshDashboard" target="refreshDashboard" full>
        Refresh dashboard
    </x-mobile.retry-button>
</section>
