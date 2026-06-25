<section class="safe-x safe-pb mx-auto flex min-h-full w-full max-w-md flex-col gap-5 py-6">
    <x-mobile.page-header
        title="Consent history"
        description="Local accepted policy versions and sync state."
        back-href="{{ route('mobile.consent.accept') }}"
    >
        <x-slot:action>
            <x-mobile.badge :variant="$hasAcceptedCurrentVersions ? 'success' : 'warning'" dot>
                {{ $hasAcceptedCurrentVersions ? 'Current' : 'Missing' }}
            </x-mobile.badge>
        </x-slot:action>
    </x-mobile.page-header>

    @if ($history === [])
        <x-mobile.empty-state title="No consent history" description="Accept the current policy versions to create a local consent record.">
            <x-slot:action>
                <a href="{{ route('mobile.consent.accept') }}" wire:navigate class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white">
                    Accept consent
                </a>
            </x-slot:action>
        </x-mobile.empty-state>
    @else
        <div class="grid gap-4">
            @foreach ($history as $record)
                <x-mobile.card wire:key="consent-history-{{ $record['id'] }}" title="{{ $record['policy_title'] }}" description="Version {{ $record['version'] }}">
                    <dl class="grid gap-3 text-sm">
                        <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">
                            <dt class="text-app-muted dark:text-zinc-400">Accepted at</dt>
                            <dd class="max-w-[58%] text-right font-semibold text-app-ink dark:text-zinc-100">{{ $record['accepted_at_label'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">
                            <dt class="text-app-muted dark:text-zinc-400">Sync status</dt>
                            <dd class="max-w-[58%] text-right font-semibold text-app-ink dark:text-zinc-100">{{ $record['sync_status'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">
                            <dt class="text-app-muted dark:text-zinc-400">Locale</dt>
                            <dd class="font-semibold text-app-ink dark:text-zinc-100">{{ $record['locale'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">
                            <dt class="text-app-muted dark:text-zinc-400">App version</dt>
                            <dd class="font-semibold text-app-ink dark:text-zinc-100">{{ $record['app_version'] }} ({{ $record['app_version_code'] }})</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">
                            <dt class="text-app-muted dark:text-zinc-400">Device</dt>
                            <dd class="max-w-[58%] text-right font-semibold text-app-ink dark:text-zinc-100">{{ $record['device_label'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">
                            <dt class="text-app-muted dark:text-zinc-400">Device reference</dt>
                            <dd class="font-mono text-xs font-semibold text-app-ink dark:text-zinc-100">{{ $record['device_session_reference'] }}</dd>
                        </div>
                    </dl>
                </x-mobile.card>
            @endforeach
        </div>

        <x-mobile.card title="Sync target" description="History records are ready for future server sync.">
            <dl class="grid gap-3 text-sm">
                <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">
                    <dt class="text-app-muted dark:text-zinc-400">Endpoint</dt>
                    <dd class="text-right font-mono text-xs font-semibold text-app-ink dark:text-zinc-100">{{ $syncPayload['endpoint'] }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 rounded-lg border border-app-line bg-app-bg px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">
                    <dt class="text-app-muted dark:text-zinc-400">Method</dt>
                    <dd class="font-semibold text-app-ink dark:text-zinc-100">{{ $syncPayload['method'] }}</dd>
                </div>
            </dl>
        </x-mobile.card>
    @endif
</section>
