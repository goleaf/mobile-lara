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
                        <div class="flex items-center justify-between gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3  ">
                            <p class="text-sm font-medium text-amber-900 ">
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

                @if ($appState['force_update'] || $appState['optional_update'] || $appState['maintenance_enabled'])
                    <x-mobile.card
                        :title="$appState['banner_title']"
                        :description="$appState['message']"
                    >
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-app-ink ">{{ $appState['label'] }}</p>
                                <p class="mt-1 text-sm leading-5 text-app-muted ">
                                    Current {{ $appState['current_version'] }}
                                    @if ($appState['latest_version'])
                                        · Latest {{ $appState['latest_version'] }}
                                    @endif
                                </p>
                            </div>

                            <a
                                href="{{ route($appState['maintenance_enabled'] ? 'mobile.maintenance' : 'mobile.update-required') }}"
                                wire:navigate
                                class="inline-flex min-h-11 shrink-0 touch-manipulation items-center justify-center rounded-lg border border-app-line bg-app-surface px-3.5 text-sm font-semibold text-app-ink shadow-[0_12px_24px_-20px_rgba(15,23,42,0.45)] transition duration-150 hover:bg-app-bg focus-visible:ring-2 focus-visible:ring-app-accent/25 active:translate-y-px"
                            >
                                Review
                            </a>
                        </div>
                    </x-mobile.card>
                @endif

                <div class="rounded-lg border border-app-line bg-app-surface p-5 shadow-[0_18px_38px_-30px_rgba(15,23,42,0.72)] ring-1 ring-white/75">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-app-muted">Welcome back</p>
                            <h2 class="mt-1 text-2xl font-semibold tracking-normal text-app-ink">
                                Good afternoon, {{ $greetingName }}
                            </h2>
                            <p class="mt-2 text-sm leading-6 text-app-muted">
                                Your mobile workspace is ready with cached content, sync status, and recent alerts.
                            </p>
                        </div>

                        <x-mobile.badge :variant="$offlineStatus['variant']" dot>
                            {{ $offlineStatus['label'] }}
                        </x-mobile.badge>
                    </div>

                    <div class="mt-5 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-lg border border-app-line/70 bg-app-bg px-3 py-3">
                            <p class="text-lg font-semibold text-app-ink">{{ $syncStatus['queued_changes'] }}</p>
                            <p class="mt-1 text-[11px] font-medium text-app-muted">Queued</p>
                        </div>
                        <div class="rounded-lg border border-app-line/70 bg-app-bg px-3 py-3">
                            <p class="text-lg font-semibold text-app-ink">{{ $offlineStatus['cached_screens'] }}</p>
                            <p class="mt-1 text-[11px] font-medium text-app-muted">Cached</p>
                        </div>
                        <div class="rounded-lg border border-app-line/70 bg-app-bg px-3 py-3">
                            <p class="text-lg font-semibold text-app-ink">{{ count($notificationPreview) }}</p>
                            <p class="mt-1 text-[11px] font-medium text-app-muted">Alerts</p>
                        </div>
                    </div>
                </div>

                <section aria-labelledby="quick-stats-title" class="grid gap-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 id="quick-stats-title" class="text-base font-semibold text-app-ink ">Quick stats</h2>
                        <x-mobile.badge variant="neutral">Fake data</x-mobile.badge>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        @forelse ($quickStats as $stat)
                            <article wire:key="dashboard-stat-{{ $stat['key'] }}" class="min-h-32 rounded-lg border border-app-line bg-app-surface p-4 shadow-[0_12px_24px_-22px_rgba(15,23,42,0.45)]">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-medium leading-5 text-app-muted ">{{ $stat['label'] }}</p>
                                    <x-mobile.badge :variant="$stat['variant']" size="sm" dot>
                                        Live
                                    </x-mobile.badge>
                                </div>
                                <p class="mt-4 text-3xl font-semibold tracking-normal text-app-ink ">{{ $stat['value'] }}</p>
                                <p class="mt-1 text-sm leading-5 text-app-muted ">{{ $stat['description'] }}</p>
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
                                <p class="text-2xl font-semibold text-app-ink ">{{ $offlineStatus['label'] }}</p>
                                <p class="mt-1 text-sm text-app-muted ">{{ $offlineStatus['cached_screens'] }} cached screens</p>
                            </div>
                            <x-mobile.badge :variant="$offlineStatus['variant']" dot>
                                Ready
                            </x-mobile.badge>
                        </div>

                        <x-slot:footer>
                            <p class="text-sm leading-5 text-app-muted ">{{ $offlineStatus['updated_label'] }}</p>
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
                                class="flex min-h-14 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 transition duration-150 hover:bg-app-surface focus-visible:ring-2 focus-visible:ring-app-accent/25 active:translate-y-px"
                            >
                                <span class="min-w-0">
                                    <span class="block text-base font-semibold text-app-ink ">{{ $action['label'] }}</span>
                                    <span class="mt-1 block text-sm leading-5 text-app-muted ">{{ $action['description'] }}</span>
                                </span>
                                <span aria-hidden="true" class="shrink-0 text-lg text-app-muted ">&rsaquo;</span>
                            </a>
                        @empty
                            <p class="text-sm text-app-muted ">No quick actions available.</p>
                        @endforelse
                    </div>
                </x-mobile.card>

                <x-mobile.card title="Recent activity" description="Latest local app events and placeholder sync activity.">
                    <div class="grid gap-4">
                        @forelse ($recentActivities as $activity)
                            <article wire:key="dashboard-activity-{{ $activity['id'] }}" class="grid grid-cols-[auto_1fr] gap-3">
                                <span class="mt-1 size-2.5 rounded-full bg-app-accent "></span>
                                <div class="min-w-0 border-b border-app-line pb-4 last:border-b-0 last:pb-0 ">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="text-sm font-semibold text-app-ink ">{{ $activity['title'] }}</h3>
                                        <x-mobile.badge :variant="$activity['variant']" size="sm">
                                            {{ $activity['time_label'] }}
                                        </x-mobile.badge>
                                    </div>
                                    <p class="mt-1 text-sm leading-5 text-app-muted ">{{ $activity['description'] }}</p>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-app-muted ">No recent activity yet.</p>
                        @endforelse
                    </div>
                </x-mobile.card>

                <x-mobile.card title="Notification preview" description="Most recent alerts waiting on this device.">
                    <x-slot:action>
                        <a href="{{ route('mobile.notifications') }}" wire:navigate class="text-sm font-semibold text-app-ink underline-offset-4 hover:underline ">
                            View all
                        </a>
                    </x-slot:action>

                    <div class="grid gap-4">
                        @forelse ($notificationPreview as $notification)
                            <article wire:key="dashboard-notification-{{ $notification['id'] }}" class="flex gap-3">
                                <span @class([
                                    'mt-2 size-2 shrink-0 rounded-full',
                                    'bg-app-accent ' => $notification['unread'],
                                    'bg-app-line ' => ! $notification['unread'],
                                ])></span>
                                <div class="min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="text-sm font-semibold text-app-ink ">{{ $notification['title'] }}</h3>
                                        <span class="shrink-0 text-xs font-medium text-app-muted ">{{ $notification['time_label'] }}</span>
                                    </div>
                                    <p class="mt-1 text-sm leading-5 text-app-muted ">{{ $notification['body'] }}</p>
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-app-muted ">No notifications yet.</p>
                        @endforelse
                    </div>
                </x-mobile.card>
            </div>
        @endif
    </div>

    <x-mobile.retry-button wire:click="refreshDashboard" target="refreshDashboard" full>
        Refresh dashboard
    </x-mobile.retry-button>
</section>
