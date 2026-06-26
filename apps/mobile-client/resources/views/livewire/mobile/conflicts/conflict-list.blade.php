<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="refreshConflicts" message="Refreshing conflicts..." />

    <x-mobile.page-header
        title="Sync conflicts"
        description="Review offline actions that need a local or remote resolution before they can sync."
        :back-href="route('mobile.settings.sync')"
    >
        <x-slot:action>
            <x-mobile.badge variant="warning" dot>
                {{ $conflicts->count() }} pending
            </x-mobile.badge>
        </x-slot:action>
    </x-mobile.page-header>

    @if (! $storageAvailable)
        <x-mobile.error-state
            title="Conflict storage unavailable"
            description="Run the mobile local storage migrations before reviewing sync conflicts."
        />
    @else
        <x-mobile.card title="Conflict inbox" description="Pending conflicts are held out of automatic retry until resolved.">
            <x-slot:action>
                <x-mobile.button size="sm" variant="secondary" wire:click="refreshConflicts" wire:target="refreshConflicts" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="refreshConflicts">Refresh</span>
                    <span wire:loading wire:target="refreshConflicts">Refreshing</span>
                </x-mobile.button>
            </x-slot:action>

            <div class="grid gap-3">
                @forelse ($conflicts as $conflict)
                    <a
                        wire:key="sync-conflict-{{ $conflict->id }}"
                        href="{{ route('mobile.conflicts.show', $conflict) }}"
                        wire:navigate
                        class="flex min-h-20 items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 transition hover:bg-app-surface   "
                    >
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold uppercase tracking-normal text-app-muted ">
                                {{ $conflict->method }} {{ $conflict->action_type }}
                            </span>
                            <span class="mt-1 block truncate text-base font-semibold text-app-ink ">
                                {{ $conflict->endpoint }}
                            </span>
                            <span class="mt-1 block text-sm leading-5 text-app-muted ">
                                Local {{ $conflict->local_version ?: 'unknown' }} · Remote {{ $conflict->remote_version ?: 'unknown' }}
                            </span>
                        </span>

                        <span class="flex shrink-0 items-center gap-2">
                            <x-mobile.badge variant="warning">
                                {{ $conflict->conflict_status }}
                            </x-mobile.badge>
                            <span aria-hidden="true" class="text-lg font-semibold text-app-muted ">›</span>
                        </span>
                    </a>
                @empty
                    <x-mobile.empty-state
                        title="No sync conflicts"
                        description="Conflicting offline actions will appear here after a server sync response requires review."
                    />
                @endforelse
            </div>
        </x-mobile.card>
    @endif
</section>
