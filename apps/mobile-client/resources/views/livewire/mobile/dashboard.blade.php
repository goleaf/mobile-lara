<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="refreshDashboard" message="Refreshing dashboard..." />

    <x-mobile.page-skeleton wire:loading.delay wire:target="refreshDashboard" :cards="4" />

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
            <div class="grid gap-5">
                @if (session('mobile_policy_denial'))
                    <x-mobile.card title="Feature unavailable" description="{{ session('mobile_policy_denial') }}">
                        <div class="flex items-center justify-between gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-400/30 dark:bg-amber-400/10">
                            <p class="text-sm font-medium text-amber-900 dark:text-amber-100">
                                Admin/API policy blocked that screen for the current workspace.
                            </p>

                            @if (session('mobile_policy_denial_reason'))
                                <x-mobile.badge variant="warning" size="sm">
                                    {{ session('mobile_policy_denial_reason') }}
                                </x-mobile.badge>
                            @endif
                        </div>
                    </x-mobile.card>
                @endif

                <div class="rounded-lg border border-app-line bg-app-surface p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-app-muted dark:text-zinc-400">Welcome back</p>
                            <h2 class="mt-1 text-2xl font-semibold tracking-normal text-app-ink dark:text-zinc-100">
                                Good afternoon, {{ $greetingName }}
                            </h2>
                            <p class="mt-2 text-sm leading-6 text-app-muted dark:text-zinc-400">
                                Your mobile workspace is ready with cached content, sync status, and recent alerts.
                            </p>
                        </div>

                        <x-mobile.badge :variant="$offlineStatus['variant']" dot>
                            {{ $offlineStatus['label'] }}
                        </x-mobile.badge>
                    </div>

                    <div class="mt-5 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-lg bg-app-bg px-3 py-3 dark:bg-zinc-950">
                            <p class="text-lg font-semibold text-app-ink dark:text-zinc-100">{{ $syncStatus['queued_changes'] }}</p>
                            <p class="mt-1 text-[11px] font-medium text-app-muted dark:text-zinc-400">Queued</p>
                        </div>
                        <div class="rounded-lg bg-app-bg px-3 py-3 dark:bg-zinc-950">
                            <p class="text-lg font-semibold text-app-ink dark:text-zinc-100">{{ $offlineStatus['cached_screens'] }}</p>
                            <p class="mt-1 text-[11px] font-medium text-app-muted dark:text-zinc-400">Cached</p>
                        </div>
                        <div class="rounded-lg bg-app-bg px-3 py-3 dark:bg-zinc-950">
                            <p class="text-lg font-semibold text-app-ink dark:text-zinc-100">{{ count($notificationPreview) }}</p>
                            <p class="mt-1 text-[11px] font-medium text-app-muted dark:text-zinc-400">Alerts</p>
                        </div>
                    </div>
                </div>

                <section aria-labelledby="quick-stats-title" class="grid gap-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 id="quick-stats-title" class="text-base font-semibold text-app-ink dark:text-zinc-100">Quick stats</h2>
                        <x-mobile.badge variant="neutral">Fake data</x-mobile.badge>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        @forelse ($quickStats as $stat)
                            <article wire:key="dashboard-stat-{{ $stat['key'] }}" class="min-h-32 rounded-lg border border-app-line bg-app-surface p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-medium leading-5 text-app-muted dark:text-zinc-400">{{ $stat['label'] }}</p>
                                    <x-mobile.badge :variant="$stat['variant']" size="sm" dot>
                                        Live
                                    </x-mobile.badge>
                                </div>
                                <p class="mt-4 text-3xl font-semibold tracking-normal text-app-ink dark:text-zinc-100">{{ $stat['value'] }}</p>
                                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $stat['description'] }}</p>
                            </article>
                        @empty
                            <x-mobile.empty-state title="No stats yet" description="Stats will appear once dashboard data is connected." />
                        @endforelse
                    </div>
                </section>

                <div class="grid gap-3 sm:grid-cols-2">
                    <livewire:mobile.sync-status />

                    <x-mobile.card title="Offline status" description="{{ $offlineStatus['description'] }}">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-2xl font-semibold text-app-ink dark:text-zinc-100">{{ $offlineStatus['label'] }}</p>
                                <p class="mt-1 text-sm text-app-muted dark:text-zinc-400">{{ $offlineStatus['cached_screens'] }} cached screens</p>
                            </div>
                            <x-mobile.badge :variant="$offlineStatus['variant']" dot>
                                Ready
                            </x-mobile.badge>
                        </div>

                        <x-slot:footer>
                            <p class="text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $offlineStatus['updated_label'] }}</p>
                        </x-slot:footer>
                    </x-mobile.card>
                </div>

                <x-mobile.card title="Quick actions" description="Jump into the most common mobile workflows.">
                    <div class="grid gap-2">
                        @forelse ($quickActions as $action)
                            <a
                                wire:key="dashboard-action-{{ $action['key'] }}"
                                href="{{ route($action['route']) }}"
                                wire:navigate
                                class="flex min-h-14 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 transition hover:bg-app-surface dark:border-zinc-800 dark:bg-zinc-950 dark:hover:bg-zinc-900"
                            >
                                <span class="min-w-0">
                                    <span class="block text-base font-semibold text-app-ink dark:text-zinc-100">{{ $action['label'] }}</span>
                                    <span class="mt-1 block text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $action['description'] }}</span>
                                </span>
                                <span aria-hidden="true" class="shrink-0 text-lg text-app-muted dark:text-zinc-400">&rsaquo;</span>
                            </a>
                        @empty
                            <p class="text-sm text-app-muted dark:text-zinc-400">No quick actions available.</p>
                        @endforelse
                    </div>
                </x-mobile.card>

                <x-mobile.card title="Recent activity" description="Latest local app events and placeholder sync activity.">
                    <div class="grid gap-4">
                        @forelse ($recentActivities as $activity)
                            <article wire:key="dashboard-activity-{{ $activity['id'] }}" class="grid grid-cols-[auto_1fr] gap-3">
                                <span class="mt-1 size-2.5 rounded-full bg-app-accent dark:bg-emerald-400"></span>
                                <div class="min-w-0 border-b border-app-line pb-4 last:border-b-0 last:pb-0 dark:border-zinc-800">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="text-sm font-semibold text-app-ink dark:text-zinc-100">{{ $activity['title'] }}</h3>
                                        <x-mobile.badge :variant="$activity['variant']" size="sm">
                                            {{ $activity['time_label'] }}
                                        </x-mobile.badge>
                                    </div>
                                    <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $activity['description'] }}</p>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-app-muted dark:text-zinc-400">No recent activity yet.</p>
                        @endforelse
                    </div>
                </x-mobile.card>

                <x-mobile.card title="Notification preview" description="Most recent alerts waiting on this device.">
                    <x-slot:action>
                        <a href="{{ route('mobile.notifications') }}" wire:navigate class="text-sm font-semibold text-app-ink underline-offset-4 hover:underline dark:text-zinc-100">
                            View all
                        </a>
                    </x-slot:action>

                    <div class="grid gap-4">
                        @forelse ($notificationPreview as $notification)
                            <article wire:key="dashboard-notification-{{ $notification['id'] }}" class="flex gap-3">
                                <span @class([
                                    'mt-2 size-2 shrink-0 rounded-full',
                                    'bg-app-accent dark:bg-emerald-400' => $notification['unread'],
                                    'bg-app-line dark:bg-zinc-700' => ! $notification['unread'],
                                ])></span>
                                <div class="min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="text-sm font-semibold text-app-ink dark:text-zinc-100">{{ $notification['title'] }}</h3>
                                        <span class="shrink-0 text-xs font-medium text-app-muted dark:text-zinc-400">{{ $notification['time_label'] }}</span>
                                    </div>
                                    <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">{{ $notification['body'] }}</p>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-app-muted dark:text-zinc-400">No notifications yet.</p>
                        @endforelse
                    </div>
                </x-mobile.card>
            </div>
        @endif
    </div>

    <x-mobile.retry-button wire:click="refreshDashboard" target="refreshDashboard" full>
        Refresh dashboard
    </x-mobile.retry-button>

    <x-mobile.floating-action-button label="Create" route="mobile.create">
        <x-slot:icon>
            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </x-slot:icon>
    </x-mobile.floating-action-button>
</section>
