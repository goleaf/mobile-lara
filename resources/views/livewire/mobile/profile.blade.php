<x-slot:header>
    <x-mobile.page-header eyebrow="Account" title="Profile" description="Identity and app preferences." />
</x-slot:header>

<section class="safe-x safe-pb flex min-h-full flex-col gap-5 py-6">
    <div class="rounded-lg border border-app-line bg-app-surface p-5 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="grid size-14 place-items-center rounded-full bg-app-accent text-lg font-semibold text-app-accent-ink">
                ML
            </div>
            <div>
                <p class="text-lg font-semibold text-app-ink">Mobile Lara</p>
                <p class="text-sm text-app-muted">Local mobile account</p>
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-app-line bg-app-surface p-5 shadow-sm">
        <p class="text-sm font-medium text-app-muted">Route</p>
        <p class="mt-1 text-base font-semibold text-app-ink">mobile.profile</p>
    </div>
</section>

<x-slot:bottomNavigation>
    <x-mobile.bottom-navigation />
</x-slot:bottomNavigation>
