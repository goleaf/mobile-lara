<x-slot:header>
    <x-mobile.page-header eyebrow="Explore" title="Search" description="Find routes and app sections." />
</x-slot:header>

<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <label class="block">
        <span class="text-sm font-medium text-app-ink">Search</span>
        <input
            type="search"
            placeholder="Dashboard, profile, settings"
            class="mt-2 min-h-12 w-full rounded-lg border border-app-line bg-white px-3 text-base text-app-ink outline-none focus:border-app-accent"
        >
    </label>

    <div class="grid grid-cols-2 gap-3">
        @foreach (['Dashboard', 'Profile', 'Settings', 'Debug'] as $result)
            <div wire:key="search-result-{{ $result }}" class="rounded-lg border border-app-line bg-app-surface p-4 shadow-sm">
                <p class="text-base font-semibold text-app-ink">{{ $result }}</p>
                <p class="mt-1 text-sm text-app-muted">Mobile route</p>
            </div>
        @endforeach
    </div>
</section>

<x-slot:bottomNavigation>
    <x-mobile.bottom-navigation />
</x-slot:bottomNavigation>
