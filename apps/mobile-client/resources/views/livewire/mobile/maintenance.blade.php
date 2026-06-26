<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="retryPolicy,logout" message="Checking maintenance policy..." />

    <x-mobile.page-header
        eyebrow="Admin/API policy"
        title="Maintenance mode"
        :description="$appState['message']"
    />

    @if ($statusMessage)
        <div @class([
            'rounded-lg border px-4 py-3 text-sm font-medium',
            'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-100' => $statusVariant === 'success',
            'border-amber-200 bg-amber-50 text-amber-900 dark:text-amber-100 dark:border-amber-400/30 dark:bg-amber-400/10' => $statusVariant !== 'success',
        ])>
            {{ $statusMessage }}
        </div>
    @endif

    <x-mobile.card title="Mobile access is limited" :description="$appState['message']">
        <div class="grid gap-3">
            @foreach ($policyRows as $row)
                <div class="flex items-start justify-between gap-4 border-b border-app-line pb-3 last:border-b-0 last:pb-0 dark:border-zinc-800">
                    <span class="text-sm font-medium text-app-muted dark:text-zinc-400">{{ $row['label'] }}</span>
                    <span class="max-w-44 text-right text-sm font-semibold text-app-ink dark:text-zinc-100">{{ $row['value'] }}</span>
                </div>
            @endforeach
        </div>

        <x-slot:footer>
            <div class="grid gap-3">
                @if ($appState['can_retry'])
                    <x-mobile.button wire:click="retryPolicy" wire:target="retryPolicy" full>
                        Retry now
                    </x-mobile.button>
                @endif

                @if ($appState['can_support'])
                    <a
                        href="{{ $appState['support_url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex min-h-12 items-center justify-center rounded-lg border border-app-line bg-app-surface px-4 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800"
                    >
                        Contact support
                    </a>
                @endif

                @if ($appState['can_logout'])
                    <x-mobile.button wire:click="logout" wire:target="logout" variant="ghost" full>
                        Logout
                    </x-mobile.button>
                @endif
            </div>
        </x-slot:footer>
    </x-mobile.card>
</section>
