<div wire:poll.15s="refreshStatus">
    @if ($isOffline)
        <section
            role="status"
            aria-live="polite"
            class="safe-x border-b border-amber-200 bg-amber-50 px-4 py-3 text-amber-950 shadow-sm dark:border-amber-300/20 dark:bg-amber-300/10 dark:text-amber-100"
        >
            <div class="mx-auto flex w-full max-w-md items-center gap-3">
                <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-amber-200 text-sm font-black text-amber-950 dark:bg-amber-300 dark:text-zinc-950">
                    !
                </span>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm font-semibold">Offline mode</p>
                        <x-mobile.badge variant="warning" size="sm">
                            {{ $pendingActionLabel }}
                        </x-mobile.badge>
                    </div>

                    <p class="mt-1 text-sm leading-5 text-amber-900 dark:text-amber-100/80">
                        NativePHP network status or the fallback check is offline. {{ $networkDescription }}.
                    </p>

                    @if ($statusMessage)
                        <p class="mt-2 text-xs font-semibold leading-5 text-amber-950 dark:text-amber-100">
                            {{ $statusMessage }}
                        </p>
                    @endif
                </div>

                <button
                    type="button"
                    wire:click="retrySync"
                    wire:loading.attr="disabled"
                    wire:target="retrySync"
                    class="inline-flex min-h-10 shrink-0 items-center justify-center rounded-lg bg-app-ink px-3 text-xs font-semibold text-white shadow-sm transition data-loading:pointer-events-none data-loading:opacity-70 hover:bg-app-ink/90 disabled:pointer-events-none disabled:opacity-60 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white"
                >
                    <span wire:loading.remove wire:target="retrySync">Retry Sync</span>
                    <span wire:loading wire:target="retrySync">Checking</span>
                </button>
            </div>
        </section>
    @else
        <span class="sr-only">Network online</span>
    @endif
</div>
