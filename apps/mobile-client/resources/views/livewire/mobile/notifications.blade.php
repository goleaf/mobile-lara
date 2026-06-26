<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="refreshInbox,setFilter,markAsRead,markAsOpened,markAllAsRead,search,clearSearch" message="Updating notifications..." />

    <x-mobile.page-header
        title="Notifications"
        description="Local inbox items saved on this device."
        :back-href="route('mobile.dashboard')"
    >
        <x-slot:action>
            <x-mobile.badge :variant="$unreadCount > 0 ? 'warning' : 'neutral'" dot>
                {{ $unreadCount }} unread
            </x-mobile.badge>
        </x-slot:action>
    </x-mobile.page-header>

    @if (! $storageAvailable)
        <x-mobile.error-state
            title="Notification inbox unavailable"
            message="Run the mobile local storage migrations before viewing saved notifications."
        >
            <x-slot:action>
                <x-mobile.button wire:click="refreshInbox" variant="secondary">
                    Retry
                </x-mobile.button>
            </x-slot:action>
        </x-mobile.error-state>
    @elseif (! $notificationPolicy['inbox']['allowed'])
        <x-mobile.error-state
            title="Notifications disabled"
            :message="$notificationPolicy['inbox']['message']"
        />
    @else
        <x-mobile.card title="Inbox summary" description="Unread, opened, and warning counts from local storage.">
            <div class="grid grid-cols-2 gap-3">
                @forelse ($metrics as $metric)
                    <div
                        wire:key="notification-metric-{{ $metric['label'] }}"
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
                        description="Notification totals will appear after local storage is available."
                    />
                @endforelse
            </div>
        </x-mobile.card>

        <x-mobile.card title="Filters" description="Search by title, body, type, or deep link.">
            <div class="grid gap-4">
                <x-mobile.input
                    wire:model.live.debounce.300ms="search"
                    name="search"
                    label="Search notifications"
                    placeholder="Search notification text"
                />

                <div class="flex gap-2 overflow-x-auto pb-1">
                    @forelse ($filters as $filterOption)
                        <button
                            type="button"
                            wire:key="notification-filter-{{ $filterOption['key'] }}"
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
                        wire:click="markAllAsRead"
                        wire:loading.attr="disabled"
                        wire:target="markAllAsRead"
                        variant="accent"
                        full
                    >
                        <span wire:loading.remove wire:target="markAllAsRead">Mark all read</span>
                        <span wire:loading wire:target="markAllAsRead">Marking</span>
                    </x-mobile.button>
                </div>
            </div>
        </x-mobile.card>

        <x-mobile.card title="Inbox" description="Newest local notifications first.">
            <div class="grid gap-3">
                @forelse ($notifications as $notification)
                    <article
                        wire:key="notification-item-{{ $notification->id }}"
                        @class([
                            'grid gap-3 rounded-lg border bg-app-bg p-4 dark:bg-zinc-950',
                            'border-app-warm/40 dark:border-amber-300/30' => $notification->isUnread(),
                            'border-app-line dark:border-zinc-800' => ! $notification->isUnread(),
                        ])
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="break-words text-base font-semibold text-app-ink dark:text-zinc-100">
                                        {{ $notification->title }}
                                    </p>

                                    @if ($notification->isUnread())
                                        <x-mobile.badge variant="warning" size="sm" dot>
                                            Unread
                                        </x-mobile.badge>
                                    @endif
                                </div>

                                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                                    {{ $notification->created_at?->diffForHumans() ?? 'Time unknown' }}
                                </p>
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <x-mobile.badge :variant="$notification->typeVariant()" dot>
                                    {{ $notification->typeLabel() }}
                                </x-mobile.badge>

                                @if ($notification->isOpened())
                                    <x-mobile.badge variant="neutral" size="sm">
                                        Opened
                                    </x-mobile.badge>
                                @endif
                            </div>
                        </div>

                        <p class="break-words text-sm leading-6 text-app-muted dark:text-zinc-400">
                            {{ $notification->bodyPreview() }}
                        </p>

                        @if ($notification->deepLinkLabel())
                            <div class="rounded-lg border border-dashed border-app-line bg-app-surface p-3 dark:border-zinc-800 dark:bg-zinc-900">
                                <p class="text-xs font-semibold uppercase tracking-normal text-app-muted dark:text-zinc-500">
                                    Deep link
                                </p>
                                <p class="mt-1 break-words text-sm font-medium text-app-ink dark:text-zinc-100">
                                    {{ $notification->deepLinkLabel() }}
                                </p>
                            </div>
                        @endif

                        @if ($notification->dataEntries() !== [])
                            <div class="grid gap-2">
                                @forelse ($notification->dataEntries() as $entry)
                                    <div
                                        wire:key="notification-data-{{ $notification->id }}-{{ $entry['key'] }}"
                                        class="flex items-start justify-between gap-3 rounded-lg bg-app-surface px-3 py-2 text-sm dark:bg-zinc-900"
                                    >
                                        <span class="shrink-0 font-semibold text-app-ink dark:text-zinc-100">{{ $entry['key'] }}</span>
                                        <span class="break-words text-right text-app-muted dark:text-zinc-400">{{ $entry['value'] }}</span>
                                    </div>
                                @empty
                                    <span class="text-sm font-medium text-app-muted dark:text-zinc-400">No notification data</span>
                                @endforelse
                            </div>
                        @endif

                        <div class="flex flex-wrap justify-end gap-2">
                            @if ($notification->isUnread())
                                <x-mobile.button
                                    wire:click="markAsRead({{ $notification->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="markAsRead({{ $notification->id }})"
                                    variant="secondary"
                                    size="sm"
                                >
                                    <span wire:loading.remove wire:target="markAsRead({{ $notification->id }})">Mark read</span>
                                    <span wire:loading wire:target="markAsRead({{ $notification->id }})">Marking</span>
                                </x-mobile.button>
                            @endif

                            @if (! $notification->isOpened())
                                <x-mobile.button
                                    wire:click="markAsOpened({{ $notification->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="markAsOpened({{ $notification->id }})"
                                    variant="ghost"
                                    size="sm"
                                >
                                    <span wire:loading.remove wire:target="markAsOpened({{ $notification->id }})">Mark opened</span>
                                    <span wire:loading wire:target="markAsOpened({{ $notification->id }})">Opening</span>
                                </x-mobile.button>
                            @endif
                        </div>
                    </article>
                @empty
                    <x-mobile.empty-state
                        title="No notifications"
                        description="Local notifications will appear here once they are created on this device."
                    />
                @endforelse
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-medium text-app-muted dark:text-zinc-400">
                        {{ $inboxCount }} shown
                    </p>

                    <x-mobile.button wire:click="refreshInbox" variant="secondary" size="sm">
                        Refresh
                    </x-mobile.button>
                </div>
            </x-slot:footer>
        </x-mobile.card>
    @endif
</section>
