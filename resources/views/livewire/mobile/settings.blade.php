<x-slot:header>
    <x-mobile.page-header eyebrow="App" title="Settings" description="Mobile configuration surface." />
</x-slot:header>

<section class="safe-x safe-pb flex min-h-full flex-col gap-3 py-6">
    @foreach (['Notifications', 'Privacy', 'Appearance', 'Storage'] as $setting)
        <div wire:key="setting-{{ $setting }}" class="flex items-center justify-between rounded-lg border border-app-line bg-app-surface p-4 shadow-sm">
            <span class="text-base font-semibold text-app-ink">{{ $setting }}</span>
            <span class="text-sm font-medium text-app-muted">Default</span>
        </div>
    @endforeach
</section>

<x-slot:bottomNavigation>
    <x-mobile.bottom-navigation />
</x-slot:bottomNavigation>
