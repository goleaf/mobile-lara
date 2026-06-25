<div
    @if ($hasAutoDismissToasts) wire:poll.1000ms.visible="pruneExpiredToasts" @endif
    class="grid gap-2"
>
    @forelse ($toastRows as $toast)
        <section
            wire:key="{{ $toast['id'] }}"
            role="{{ $toast['role'] }}"
            class="{{ $toast['wrapper_classes'] }} pointer-events-auto rounded-lg border p-4 text-sm shadow-lg backdrop-blur"
        >
            <div class="flex items-start gap-3">
                <span class="{{ $toast['marker_classes'] }} flex size-7 shrink-0 items-center justify-center rounded-full text-xs font-black">
                    {{ $toast['marker'] }}
                </span>

                <div class="min-w-0 flex-1">
                    @if ($toast['title'])
                        <h2 class="text-sm font-semibold leading-5">{{ $toast['title'] }}</h2>
                    @endif

                    <p class="{{ $toast['title'] ? 'mt-1' : '' }} break-words leading-5">
                        {{ $toast['message'] }}
                    </p>

                    @if ($toast['action_label'] && $toast['action_event'])
                        <div class="mt-3">
                            <button
                                type="button"
                                wire:click="runAction('{{ $toast['id'] }}')"
                                wire:loading.attr="disabled"
                                wire:target="runAction('{{ $toast['id'] }}')"
                                class="{{ $toast['action_classes'] }} inline-flex min-h-9 items-center justify-center rounded-lg border px-3 text-xs font-semibold transition disabled:pointer-events-none disabled:opacity-60"
                            >
                                {{ $toast['action_label'] }}
                            </button>
                        </div>
                    @endif
                </div>

                <button
                    type="button"
                    wire:click="dismiss('{{ $toast['id'] }}')"
                    class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg opacity-70 transition hover:bg-black/5 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-current/20 dark:hover:bg-white/10"
                    aria-label="Dismiss notification"
                >
                    <svg aria-hidden="true" class="size-4" viewBox="0 0 20 20" fill="none">
                        <path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </section>
    @empty
        <span class="sr-only">No notifications</span>
    @endforelse
</div>
