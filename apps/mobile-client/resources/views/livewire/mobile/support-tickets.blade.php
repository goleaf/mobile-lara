<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="loadTickets,setStatus,clearSearch,search" message="Updating support tickets..." />

    <x-mobile.page-header
        title="Support tickets"
        description="Requester-scoped help conversations from the Admin/API support system."
        :back-href="route('mobile.settings.support')"
    >
        <x-slot:action>
            <x-mobile.badge :variant="$ticketCount > 0 ? 'accent' : 'neutral'" dot>
                {{ $ticketCount }} shown
            </x-mobile.badge>
        </x-slot:action>
    </x-mobile.page-header>

    @if (! $supportPolicy['allowed'])
        <x-mobile.error-state
            title="Support disabled"
            :message="$supportPolicy['message']"
        />
    @else
        <x-mobile.card title="Find tickets" description="Search and filter only your current workspace support requests.">
            <div class="grid gap-4">
                <x-mobile.input
                    wire:model.live.debounce.400ms="search"
                    name="search"
                    label="Search"
                    placeholder="Search subject, status, priority, or category"
                />

                <div class="flex gap-2 overflow-x-auto pb-1">
                    @forelse ($statusOptions as $option)
                        <button
                            type="button"
                            wire:key="support-status-{{ $option['key'] }}"
                            wire:click="setStatus('{{ $option['key'] }}')"
                            @class([
                                'inline-flex min-h-10 shrink-0 items-center rounded-lg border px-3 text-sm font-semibold transition',
                                'border-app-ink bg-app-ink text-white dark:border-zinc-100 dark:bg-zinc-100 dark:text-zinc-950' => $option['active'],
                                'border-app-line bg-app-bg text-app-ink hover:bg-app-surface dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:bg-zinc-900' => ! $option['active'],
                            ])
                        >
                            {{ $option['label'] }}
                        </button>
                    @empty
                        <span class="text-sm font-medium text-app-muted dark:text-zinc-400">No filters available</span>
                    @endforelse
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <x-mobile.button wire:click="clearSearch" variant="secondary" full>
                        Clear search
                    </x-mobile.button>

                    <x-mobile.button wire:click="loadTickets" variant="accent" full>
                        Refresh
                    </x-mobile.button>
                </div>
            </div>
        </x-mobile.card>

        @if ($loadError)
            <x-mobile.error-state
                title="Support unavailable"
                :message="$loadError"
            >
                <x-slot:action>
                    <x-mobile.button wire:click="loadTickets" variant="secondary">
                        Retry
                    </x-mobile.button>
                </x-slot:action>
            </x-mobile.error-state>
        @endif

        <x-mobile.card title="Tickets" description="Newest support activity first.">
            <div class="grid gap-3">
                @forelse ($tickets as $ticket)
                    <a
                        wire:key="support-ticket-{{ $ticket['id'] ?? $loop->index }}"
                        href="{{ route('mobile.support.show', ['ticket' => $ticket['id'] ?? 'unknown']) }}"
                        wire:navigate
                        class="grid gap-3 rounded-lg border border-app-line bg-app-bg p-4 text-left transition hover:bg-app-surface dark:border-zinc-800 dark:bg-zinc-950 dark:hover:bg-zinc-900"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words text-base font-semibold text-app-ink dark:text-zinc-100">
                                    {{ $ticket['subject'] ?? 'Untitled ticket' }}
                                </p>
                                <p class="mt-1 text-sm leading-5 text-app-muted dark:text-zinc-400">
                                    {{ $ticket['category'] ?? 'general' }} - {{ $ticket['messages_count'] ?? 0 }} messages
                                </p>
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <x-mobile.badge variant="accent">
                                    {{ str($ticket['status'] ?? 'open')->replace('_', ' ')->title() }}
                                </x-mobile.badge>
                                <x-mobile.badge variant="neutral" size="sm">
                                    {{ str($ticket['priority'] ?? 'normal')->title() }}
                                </x-mobile.badge>
                            </div>
                        </div>

                        <p class="text-xs font-medium text-app-muted dark:text-zinc-500">
                            Last activity: {{ $ticket['last_message_at'] ?? $ticket['updated_at'] ?? 'unknown' }}
                        </p>
                    </a>
                @empty
                    <x-mobile.empty-state
                        title="No support tickets"
                        description="Create a support ticket when you need tenant-safe help from the Admin/API support team."
                    >
                        <x-slot:action>
                            <a
                                href="{{ route('mobile.support.create') }}"
                                wire:navigate
                                class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
                            >
                                Create ticket
                            </a>
                        </x-slot:action>
                    </x-mobile.empty-state>
                @endforelse
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-medium text-app-muted dark:text-zinc-400">
                        {{ $ticketCount }} shown
                    </p>

                    <a
                        href="{{ route('mobile.support.create') }}"
                        wire:navigate
                        class="inline-flex min-h-10 items-center justify-center rounded-lg bg-app-ink px-3 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
                    >
                        Create ticket
                    </a>
                </div>
            </x-slot:footer>
        </x-mobile.card>
    @endif
</section>
