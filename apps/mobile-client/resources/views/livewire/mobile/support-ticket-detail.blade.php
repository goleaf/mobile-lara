<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="loadTicket,sendMessage" message="Updating support ticket..." />

    <x-mobile.page-header
        :title="$ticketTitle"
        description="Tenant-scoped support conversation from the Admin/API system."
        :back-href="route('mobile.support.index')"
    >
        <x-slot:action>
            @if ($ticketData !== [])
                <x-mobile.badge variant="accent">
                    {{ str($ticketData['status'] ?? 'open')->replace('_', ' ')->title() }}
                </x-mobile.badge>
            @endif
        </x-slot:action>
    </x-mobile.page-header>

    @if (! $supportPolicy['allowed'])
        <x-mobile.error-state
            title="Support disabled"
            :message="$supportPolicy['message']"
        />
    @elseif ($loadError)
        <x-mobile.error-state
            title="Support ticket unavailable"
            :message="$loadError"
        >
            <x-slot:action>
                <x-mobile.button wire:click="loadTicket" variant="secondary">
                    Retry
                </x-mobile.button>
            </x-slot:action>
        </x-mobile.error-state>
    @else
        <x-mobile.card title="Ticket summary" description="Admin/API owns status, priority, assignment, and visibility.">
            <div class="grid gap-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-app-line bg-app-bg p-3  ">
                        <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Priority</p>
                        <p class="mt-1 text-sm font-semibold text-app-ink ">
                            {{ str($ticketData['priority'] ?? 'normal')->title() }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-app-line bg-app-bg p-3  ">
                        <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Messages</p>
                        <p class="mt-1 text-sm font-semibold text-app-ink ">
                            {{ $ticketData['messages_count'] ?? count($ticketMessages) }}
                        </p>
                    </div>
                </div>

                <div class="rounded-lg border border-app-line bg-app-bg p-3  ">
                    <p class="text-xs font-semibold uppercase tracking-normal text-app-muted ">Assignment</p>
                    <p class="mt-1 text-sm font-semibold text-app-ink ">
                        @if (($ticketData['assignment']['assigned'] ?? false) && is_array($ticketData['assignment']['agent'] ?? null))
                            {{ $ticketData['assignment']['agent']['name'] ?? 'Assigned agent' }}
                        @else
                            Waiting for support triage
                        @endif
                    </p>
                </div>
            </div>
        </x-mobile.card>

        <x-mobile.card title="Conversation" description="Messages returned by the support API.">
            <div class="grid gap-3">
                @forelse ($ticketMessages as $message)
                    <article
                        wire:key="support-message-{{ $message['id'] ?? $loop->index }}"
                        class="grid gap-2 rounded-lg border border-app-line bg-app-bg p-4  "
                    >
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-semibold text-app-ink ">
                                {{ $message['author']['name'] ?? 'Support message' }}
                            </p>
                            <x-mobile.badge variant="neutral" size="sm">
                                {{ str($message['direction'] ?? 'message')->replace('_', ' ')->title() }}
                            </x-mobile.badge>
                        </div>

                        <p class="break-words text-sm leading-6 text-app-muted ">
                            {{ $message['body'] ?? '' }}
                        </p>

                        @if (! empty($message['attachments']) || ! empty($message['diagnostic_report_id']))
                            <p class="text-xs font-medium text-app-muted ">
                                Includes attachment metadata or diagnostics reference.
                            </p>
                        @endif
                    </article>
                @empty
                    <x-mobile.empty-state
                        title="No messages"
                        description="The initial support message will appear after the API returns ticket detail."
                    />
                @endforelse
            </div>
        </x-mobile.card>

        <x-mobile.card title="Reply" description="Replies are sent to the API and audited by the support system.">
            @if ($canAddMessage)
                <form wire:submit="sendMessage" class="grid gap-4">
                    <x-mobile.textarea
                        wire:model.blur="messageBody"
                        name="messageBody"
                        label="Message"
                        hint="Do not include passwords, access tokens, or other secrets."
                        rows="5"
                    />

                    @if ($messageError)
                        <p class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900   ">
                            {{ $messageError }}
                        </p>
                    @endif

                    <x-mobile.button type="submit" variant="primary" full wire:loading.attr="disabled" wire:target="sendMessage">
                        <span wire:loading.remove wire:target="sendMessage">Send message</span>
                        <span wire:loading wire:target="sendMessage">Sending</span>
                    </x-mobile.button>
                </form>
            @else
                <x-mobile.empty-state
                    title="Replies disabled"
                    description="Admin/API has closed or restricted this support ticket."
                />
            @endif
        </x-mobile.card>
    @endif
</section>
