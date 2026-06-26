<div wire:poll.15s="refreshStatus">
    @if ($isOffline)
        <section
            role="status"
            aria-live="polite"
            class="safe-x border-b border-amber-200 bg-amber-50 px-4 py-3 text-amber-950 shadow-[0_12px_24px_-20px_rgba(120,53,15,0.35)]"
        >
            <div class="mx-auto flex w-full max-w-md items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-amber-200 text-sm font-black text-amber-950">
                    !
                </span>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm font-semibold">Offline mode</p>
                        <x-mobile.badge variant="warning" size="sm">
                            {{ $pendingActionLabel }}
                        </x-mobile.badge>
                    </div>

                    <p class="mt-1 text-sm leading-5 text-amber-900">
                        NativePHP network status or the fallback check is offline. {{ $networkDescription }}.
                    </p>

                    @if ($statusMessage)
                        <p class="mt-2 text-xs font-semibold leading-5 text-amber-950">
                            {{ $statusMessage }}
                        </p>
                    @endif
                </div>

                <button
                    type="button"
                    wire:click="retrySync"
                    wire:loading.attr="disabled"
                    wire:target="retrySync"
                    class="inline-flex min-h-11 shrink-0 touch-manipulation items-center justify-center rounded-lg bg-app-ink px-3 text-xs font-semibold text-white shadow-[0_12px_24px_-20px_rgba(15,23,42,0.65)] transition duration-150 focus-visible:ring-2 focus-visible:ring-app-accent/25 data-loading:pointer-events-none data-loading:opacity-70 hover:bg-app-ink/90 active:translate-y-px disabled:pointer-events-none disabled:opacity-60"
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
