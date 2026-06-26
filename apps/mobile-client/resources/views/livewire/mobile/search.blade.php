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
                    <a wire:key="search-result-{{ $result['route'] }}" href="{{ route($result['route']) }}" wire:navigate class="rounded-lg border border-app-line bg-app-surface p-4 shadow-sm transition hover:bg-app-bg    ">
                        <p class="text-base font-semibold text-app-ink ">{{ $result['title'] }}</p>
                        <p class="mt-1 text-sm text-app-muted ">{{ $result['description'] }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
