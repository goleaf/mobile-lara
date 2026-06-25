<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <form wire:submit="search" class="grid gap-3">
        <x-mobile.input
            name="query"
            label="Search"
            type="search"
            placeholder="Dashboard, profile, settings"
            wire:model.live.debounce.250ms="query"
        />

        <x-mobile.submit-button target="search" loading-label="Searching..." variant="secondary">
            Search
        </x-mobile.submit-button>
    </form>

    <x-mobile.loading-state target="query, search, retrySearch" message="Searching mobile routes..." />

    <x-mobile.page-skeleton wire:loading.delay wire:target="query, search, retrySearch" :cards="2" />

    <div wire:loading.remove wire:target="query, search, retrySearch" class="contents">
        @if ($hasNetworkError)
            <x-mobile.network-error-state retry-action="retrySearch" />
        @elseif (count($results) === 0)
            <x-mobile.empty-state title="No routes found" description="Try a different search term or clear the search field.">
                <x-slot:action>
                    <x-mobile.retry-button wire:click="retrySearch" target="retrySearch">
                        Retry search
                    </x-mobile.retry-button>
                </x-slot:action>
            </x-mobile.empty-state>
        @else
            <div class="grid grid-cols-2 gap-3">
                @foreach ($results as $result)
                    <a wire:key="search-result-{{ $result['route'] }}" href="{{ route($result['route']) }}" wire:navigate class="rounded-lg border border-app-line bg-app-surface p-4 shadow-sm transition hover:bg-app-bg dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none dark:hover:bg-zinc-800">
                        <p class="text-base font-semibold text-app-ink dark:text-zinc-100">{{ $result['title'] }}</p>
                        <p class="mt-1 text-sm text-app-muted dark:text-zinc-400">{{ $result['description'] }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <x-mobile.floating-action-button label="Create" route="mobile.create">
        <x-slot:icon>
            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </x-slot:icon>
    </x-mobile.floating-action-button>
</section>
