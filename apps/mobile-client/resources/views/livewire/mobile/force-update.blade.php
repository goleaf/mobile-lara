<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <x-mobile.loading-state target="checkAgain,logout" message="Checking app policy..." />

    <x-mobile.page-header
        eyebrow="Admin/API policy"
        :title="$appState['force_update'] ? 'Update required' : 'App update available'"
        :description="$appState['message']"
    />

    @if ($statusMessage)
        <div @class([
            'rounded-lg border px-4 py-3 text-sm font-medium',
            'border-emerald-200 bg-emerald-50 text-emerald-900   ' => $statusVariant === 'success',
            'border-amber-200 bg-amber-50 text-amber-900   ' => $statusVariant !== 'success',
        ])>
            {{ $statusMessage }}
        </div>
    @endif

    <x-mobile.card
        :title="$appState['banner_title'] ?? 'App update'"
        :description="$appState['message']"
    >
        <div class="grid gap-3">
            @foreach ($versionRows as $row)
                <div class="flex items-start justify-between gap-4 border-b border-app-line pb-3 last:border-b-0 last:pb-0 ">
                    <span class="text-sm font-medium text-app-muted ">{{ $row['label'] }}</span>
                    <span class="max-w-44 text-right text-sm font-semibold text-app-ink ">{{ $row['value'] }}</span>
                </div>
            @endforeach
        </div>

        <x-slot:footer>
            <div class="grid gap-3">
                @if ($appState['can_update'])
                    <a
                        href="{{ $appState['store_url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex min-h-12 items-center justify-center rounded-lg bg-app-ink px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-app-ink/90   "
                    >
                        Open app store
                    </a>
                @endif

                <x-mobile.button wire:click="checkAgain" wire:target="checkAgain" variant="secondary" full>
                    Refresh status
                </x-mobile.button>

                @if ($appState['can_support'])
                    <a
                        href="{{ $appState['support_url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex min-h-12 items-center justify-center rounded-lg border border-app-line bg-app-surface px-4 text-sm font-semibold text-app-ink shadow-sm transition hover:bg-app-bg    "
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
