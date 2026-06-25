<x-slot:header>
    <x-mobile.page-header eyebrow="Today" title="Dashboard" description="Your mobile overview." />
</x-slot:header>

<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <div class="rounded-lg border border-app-line bg-app-surface p-5 shadow-sm">
        <p class="text-sm font-medium text-app-muted">Status</p>
        <p class="mt-2 text-3xl font-semibold text-app-ink">Ready</p>
        <p class="mt-2 text-sm leading-6 text-app-muted">Core mobile routes are available.</p>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('mobile.search') }}" wire:navigate class="rounded-lg border border-app-line bg-app-surface p-4 shadow-sm">
            <p class="text-lg font-semibold text-app-ink">Search</p>
            <p class="mt-1 text-sm text-app-muted">Find content</p>
        </a>

        <a href="{{ route('mobile.notifications') }}" wire:navigate class="rounded-lg border border-app-line bg-app-surface p-4 shadow-sm">
            <p class="text-lg font-semibold text-app-ink">Alerts</p>
            <p class="mt-1 text-sm text-app-muted">Recent updates</p>
        </a>
    </div>
</section>

<x-slot:bottomNavigation>
    <x-mobile.bottom-navigation />
</x-slot:bottomNavigation>
