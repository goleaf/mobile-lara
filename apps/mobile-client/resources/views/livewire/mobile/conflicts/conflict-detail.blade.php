<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Conflict detail"
        description="Compare local and remote versions before choosing how this offline action should continue."
        :back-href="route('mobile.conflicts.index')"
    >
        <x-slot:action>
            <x-mobile.badge variant="warning" dot>
                {{ $offlineAction->conflict_status }}
            </x-mobile.badge>
        </x-slot:action>
    </x-mobile.page-header>

    @if ($statusMessage)
        <div @class([
            'rounded-lg border px-4 py-3 text-sm font-medium',
            'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-100' => $statusVariant === 'success',
            'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-300/20 dark:bg-amber-300/10 dark:text-amber-100' => $statusVariant === 'warning',
            'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-400/20 dark:bg-sky-400/10 dark:text-sky-100' => $statusVariant === 'info',
        ])>
            {{ $statusMessage }}
        </div>
    @endif

    <x-mobile.card title="Sync action" description="The local mutation that could not be applied automatically.">
        <dl class="grid gap-3">
            @foreach ($summaryRows as $row)
                <div wire:key="conflict-summary-{{ str($row['label'])->slug() }}" class="grid grid-cols-[8rem_1fr] gap-3 border-b border-app-line pb-3 last:border-b-0 last:pb-0 dark:border-zinc-800">
                    <dt class="text-sm font-medium text-app-muted dark:text-zinc-400">{{ $row['label'] }}</dt>
                    <dd class="min-w-0 break-words text-sm font-semibold text-app-ink dark:text-zinc-100">{{ $row['value'] ?: '—' }}</dd>
                </div>
            @endforeach
        </dl>
    </x-mobile.card>

    <div class="grid gap-3 sm:grid-cols-2">
        <x-mobile.card title="Local payload" description="The offline change stored on this device.">
            <pre class="max-h-80 overflow-auto rounded-lg bg-app-bg p-3 text-xs leading-5 text-app-ink dark:bg-zinc-950 dark:text-zinc-100">{{ $localPayloadJson }}</pre>
        </x-mobile.card>

        <x-mobile.card title="Remote payload" description="The server version returned with the conflict.">
            <pre class="max-h-80 overflow-auto rounded-lg bg-app-bg p-3 text-xs leading-5 text-app-ink dark:bg-zinc-950 dark:text-zinc-100">{{ $remotePayloadJson }}</pre>
        </x-mobile.card>
    </div>

    <x-mobile.card title="Conflict payload" description="Raw conflict metadata from the sync response.">
        <pre class="max-h-80 overflow-auto rounded-lg bg-app-bg p-3 text-xs leading-5 text-app-ink dark:bg-zinc-950 dark:text-zinc-100">{{ $serverPayloadJson }}</pre>
    </x-mobile.card>

    <x-mobile.card title="Resolution" description="Choose what should happen to this offline action next.">
        @if (! $conflictPolicy['resolution']['allowed'])
            <x-mobile.error-state
                title="Conflict resolution disabled"
                :message="$conflictPolicy['resolution']['message']"
            />
        @else
            <div class="grid gap-3">
                <x-mobile.button wire:click="keepLocal" wire:target="keepLocal" wire:loading.attr="disabled" full>
                    <span wire:loading.remove wire:target="keepLocal">Keep local and retry</span>
                    <span wire:loading wire:target="keepLocal">Resolving</span>
                </x-mobile.button>

                <x-mobile.button variant="secondary" wire:click="acceptRemote" wire:target="acceptRemote" wire:loading.attr="disabled" full>
                    <span wire:loading.remove wire:target="acceptRemote">Accept remote version</span>
                    <span wire:loading wire:target="acceptRemote">Resolving</span>
                </x-mobile.button>

                <x-mobile.button variant="ghost" wire:click="dismissConflict" wire:target="dismissConflict" wire:loading.attr="disabled" full>
                    <span wire:loading.remove wire:target="dismissConflict">Dismiss conflict</span>
                    <span wire:loading wire:target="dismissConflict">Dismissing</span>
                </x-mobile.button>
            </div>
        @endif
    </x-mobile.card>
</section>
