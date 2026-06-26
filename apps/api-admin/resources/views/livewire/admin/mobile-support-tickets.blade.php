<section class="mx-auto grid max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-admin.section-heading
        title="Support Queue"
        description="Requester-scoped mobile support tickets, triage state, assignment, and support replies."
    />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Total</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['total'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Open</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700 dark:text-amber-300">{{ $summary['open'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">In progress</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['in_progress'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Waiting</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['waiting_on_user'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Resolved</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $summary['resolved'] }}</p>
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            <p class="text-xs font-medium uppercase tracking-normal text-zinc-500 dark:text-zinc-400">Closed</p>
            <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-zinc-100">{{ $summary['closed'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(30rem,0.85fr)]">
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
                    Status
                    <select
                        wire:model.change="status"
                        class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                    >
                        <option value="">All statuses</option>

                        @foreach ($statusOptions as $value => $label)
                            <option wire:key="support-status-filter-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-normal text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                            <tr>
                                <th class="px-4 py-3">Ticket</th>
                                <th class="px-4 py-3">Tenant</th>
                                <th class="px-4 py-3">Requester</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Assigned</th>
                                <th class="px-4 py-3 text-right">Review</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($tickets as $ticket)
                                <tr wire:key="support-ticket-row-{{ $ticket->id }}" class="align-top">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">{{ $ticket->subject }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $ticket->category ?: 'general' }} / {{ $ticket->messages_count }} messages
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">{{ $ticket->tenant?->name ?: 'Unknown tenant' }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $ticket->tenant?->slug ?: 'none' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-zinc-950 dark:text-zinc-100">{{ $ticket->requester?->name ?: 'Unknown requester' }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">#{{ $ticket->requester_user_id ?: 'none' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-admin.status-badge :tone="$this->statusTone($ticket->status)">
                                            {{ str($ticket->status)->replace('_', ' ')->title() }}
                                        </x-admin.status-badge>
                                        <div class="mt-2">
                                            <x-admin.status-badge :tone="$this->priorityTone($ticket->priority)">
                                                {{ str($ticket->priority)->title() }}
                                            </x-admin.status-badge>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                        {{ $ticket->assignedAgent?->name ?: 'Unassigned' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            type="button"
                                            wire:click="selectTicket({{ $ticket->id }})"
                                            class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 data-loading:pointer-events-none data-loading:opacity-70 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                        >
                                            Review
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        No support tickets found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $tickets->links() }}
            </div>
        </div>

        <aside class="grid content-start gap-4 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none">
            @if ($selectedTicket)
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">Ticket detail</h2>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $selectedTicket->public_id }}</p>
                    </div>

                    <button
                        type="button"
                        wire:click="clearSelectedTicket"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        Close
                    </button>
                </div>

                <div class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Status
                        <select
                            wire:model.change="statusDraft"
                            class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        >
                            @foreach ($statusOptions as $value => $label)
                                <option wire:key="support-status-draft-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('statusDraft')
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Priority
                        <select
                            wire:model.change="priorityDraft"
                            class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        >
                            @foreach ($priorityOptions as $value => $label)
                                <option wire:key="support-priority-draft-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priorityDraft')
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Assigned support user
                        <select
                            wire:model.change="assignedUserIdDraft"
                            class="h-11 rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        >
                            <option value="">Unassigned</option>

                            @foreach ($agentOptions as $agentId => $agentName)
                                <option wire:key="support-agent-{{ $agentId }}" value="{{ $agentId }}">{{ $agentName }}</option>
                            @endforeach
                        </select>
                        @error('assignedUserIdDraft')
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </label>

                    <button
                        type="button"
                        wire:click="saveTicketState"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
                    >
                        Save ticket state
                    </button>
                </div>

                <div class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <h3 class="text-sm font-semibold text-zinc-950 dark:text-zinc-100">Messages</h3>

                    <div class="grid max-h-80 gap-3 overflow-auto pr-1">
                        @forelse ($selectedTicket->messages as $message)
                            <article
                                wire:key="support-message-{{ $message->id }}"
                                class="grid gap-2 rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-800"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <p class="font-semibold text-zinc-950 dark:text-zinc-100">
                                        {{ $message->author?->name ?: 'Unknown author' }}
                                    </p>
                                    <x-admin.status-badge :tone="$message->direction === 'support' ? 'success' : 'neutral'">
                                        {{ $message->direction }}
                                    </x-admin.status-badge>
                                </div>
                                <p class="break-words leading-6 text-zinc-600 dark:text-zinc-300">{{ $message->body }}</p>
                            </article>
                        @empty
                            <p class="rounded-lg border border-zinc-200 p-3 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                                No support messages found.
                            </p>
                        @endforelse
                    </div>
                </div>

                <form wire:submit="sendReply" class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <label class="grid gap-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Support reply
                        <textarea
                            wire:model.blur="replyBody"
                            rows="5"
                            class="rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 shadow-sm outline-none focus:border-zinc-500 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-400 dark:focus:ring-zinc-800"
                        ></textarea>
                        @error('replyBody')
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </label>

                    <button
                        type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-zinc-950 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800 data-loading:pointer-events-none data-loading:opacity-70 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
                    >
                        Send requester reply
                    </button>
                </form>
            @else
                <div class="rounded-lg border border-zinc-200 p-4 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                    Select a support ticket to triage status, assignment, and requester-visible replies.
                </div>
            @endif
        </aside>
    </div>
</section>
